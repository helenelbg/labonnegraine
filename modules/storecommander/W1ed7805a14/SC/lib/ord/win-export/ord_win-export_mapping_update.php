<?php
if (!defined('STORE_COMMANDER')) { exit; }

if (!defined('SC_ExportOrders_ACTIVE') || (int) SC_ExportOrders_ACTIVE !== 1)
{
    exit;
}

$response = array(
    'state' => 'error',
    'message' => '',
);

$action = Tools::getValue('action');
$allowedActions = array(
    'add_mapping',
    'delete_mapping',
    'submit_mapping_form',
    'update_mapping',
    'duplicate_mapping',
);

if(empty($action)
    || !in_array($action,$allowedActions))
{
    $response['message'] = 'invalid params';
    exit(json_encode($response));
}

$mappingId = Tools::getValue(ExportOrderMapping::$definition['primary'],null);

$mappingObject = new ExportOrderMapping($mappingId);

switch ($action)
{
    case 'add_mapping':
        $mappingObject->name = 'NEW';
        $mappingObject->separator = 1;
        $mappingObject->export_format = 'CSV';
        $mappingObject->format_properties = 'delimitor::;@@display_header::1@@display_breakdown_shipping::1@@display_breakdown_discounts::1';
        $mappingObject->date_add = date('Y-m-d H:i:s');
        $mappingObject->date_upd = $mappingObject->date_add;
        if ($mappingObject->add()) {
            $response['state'] = 'success';
            $response['message'] = _l('New template added');
        } else {
            $response['message'] = _l('Unable to add template');
        }
        break;
    case 'delete_mapping':
        if(!$mappingObject->id){
            $response['message'] = _l('invalid template');
            exit(json_encode($response));
        }
        $exportListFound = ExportOrder::getExportByMapping($mappingObject->id);
        if($exportListFound)
        {
            $response['message'] = _l('Template is used for export IDs')._l(':').' '.implode(',',$exportListFound);
            break;
        }

        if ($mappingObject->delete()) {
            $response['state'] = 'success';
            $response['message'] = _l('Template %s deleted', null, array('<b>' . $mappingObject->name . '</b>'));
            break;
        }

        $response['message'] = _l('Unable to delete template');
        break;
    case 'submit_mapping_form':
        if(!$mappingId){
            $response['message'] = _l('invalid params');
            exit(json_encode($response));
        }
        $formData = Tools::getValue('formData', '');
        if($formData) {
            $delimitor_choice = $formData['CSV_delimitor_choice'];
            if(!in_array($delimitor_choice,array(';',',','|','tab')))
            {
                $response['message'] = _l('Unknow delimitor');
                exit(json_encode($response));
            }

            $fields = Tools::safeOutput($formData['selected_fields']);
            if(empty($fields)) {
                $response['message'] = _l('Please, select at least one field to export');
                exit(json_encode($response));
            }

            $mappingObject->separator = (int)$formData['separator'];
            $mappingObject->fields = $fields;

            $mappingObject->format_properties = Tools::safeOutput(implode('@@',array(
                'delimitor::'.$delimitor_choice,
                'display_header::'.(int)$formData['CSV_display_header'],
                'display_breakdown_shipping::'.(int)$formData['CSV_display_breakdown_shipping'],
                'display_breakdown_discounts::'.(int)$formData['CSV_display_breakdown_discounts'],
            )));

            $mappingObject->date_upd=date("Y-m-d H:i:s");

            if ($mappingObject->update()) {
                $response['state'] = 'success';
                $response['message'] = _l('Template %s updated', null, array('<b>' . $mappingObject->name . '</b>'));
            } else {
                $response['message'] = _l('Unable to update template %s', null, array('<b>' . $mappingObject->name . '</b>'));
            }
        } else {
            $response['message'] = _l('Something wrong with the submitted value');
        }
        break;
    case 'update_mapping':
        if(!$mappingId){
            $response['message'] = _l('invalid params');
            exit(json_encode($response));
        }
        $field = Tools::safeOutput(Tools::getValue('field', ''));
        $value = Tools::safeOutput(Tools::getValue('value', ''));
        if(property_exists($mappingObject, $field)
            && in_array($field,array('name')))
        {
            $mappingObject->{$field} = $value;
            if($mappingObject->update())
            {
                $response['state'] = 'success';
                $response['message'] = _l('Template %s updated', null, array('<b>'.$mappingObject->name.'</b>'));
            }
            else
            {
                $response['message'] = _l('Unable to update template %s', null, array('<b>'.$mappingObject->name.'</b>'));
            }
        }
        else
        {
            $response['message'] = _l('Something wrong with the submitted value');
        }
        break;
    case 'duplicate_mapping':
        if(!$mappingObject->id){
            $response['message'] = _l('invalid template');
            exit(json_encode($response));
        }
        if ($mappingObject->duplicateObject()) {
            $response['state'] = 'success';
            $response['message'] = _l('Template %s duplicated', null, array('ID'._l(':').'<b>' . $mappingObject->id . '</b>'));
        } else {
            $response['message'] = _l('Unable to duplicate template');
        }
        break;
    default:
}
exit(json_encode($response));