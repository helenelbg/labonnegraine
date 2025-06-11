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
    'add_filter',
    'delete_filter',
    'submit_filter_form',
    'update_filter',
    'duplicate_filter',
);

if(empty($action)
    || !in_array($action,$allowedActions))
{
    $response['message'] = 'invalid params';
    exit(json_encode($response));
}

$filderId = Tools::getValue(ExportOrderFilter::$definition['primary'],null);

$filterObject = new ExportOrderFilter($filderId);

switch ($action)
{
    case 'add_filter':
        $filterObject->id_shop = SCI::getSelectedShop();
        $filterObject->name = 'NEW';
        $filterObject->date_add = date('Y-m-d H:i:s');
        $filterObject->date_upd = $filterObject->date_add;
        if ($filterObject->add()) {
            $response['state'] = 'success';
            $response['message'] = _l('New filter added');
        } else {
            $response['message'] = _l('Unable to add filter');
        }
        break;
    case 'delete_filter':
        if(!$filterObject->id){
            $response['message'] = _l('invalid filter');
            exit(json_encode($response));
        }

        $exportListFound = ExportOrder::getExportByFilter($filterObject->id);
        if($exportListFound)
        {
            $response['message'] = _l('Filter is used for export IDs')._l(':').' '.implode(',',$exportListFound);
            break;
        }

        if ($filterObject->delete()) {
            $response['state'] = 'success';
            $response['message'] = _l('Filter %s deleted', null, array('<b>' . $filterObject->name . '</b>'));
            break;
        }

        $response['message'] = _l('Unable to delete Filter');
        break;
    case 'submit_filter_form':
        $dynamicDefinition = Tools::getValue('dynamic_definition', '');
        $staticDefinition = Tools::getValue('static_definition', '');
        $filterObject->dynamic_definition = $dynamicDefinition;
        $filterObject->static_definition = $staticDefinition;
        $filterObject->date_upd=date("Y-m-d H:i:s");
        if($filterObject->update())
        {
            $response['state'] = 'success';
            $response['message'] = _l('Filter %s updated', null, array('<b>'.$filterObject->name.'</b>'));
        }
        else
        {
            $response['message'] = _l('Unable to update filter %s', null, array('<b>'.$filterObject->name.'</b>'));
        }
        break;
    case 'update_filter':
        $field = Tools::safeOutput(Tools::getValue('field', ''));
        $value = Tools::safeOutput(Tools::getValue('value', ''));
        if(property_exists($filterObject, $field)
            && in_array($field,array('id_shop','name','description')))
        {
            $filterObject->{$field} = $value;
            if($filterObject->update())
            {
                $response['state'] = 'success';
                $response['message'] = _l('Filter %s updated', null, array('<b>'.$filterObject->name.'</b>'));
            }
            else
            {
                $response['message'] = _l('Unable to update filter %s', null, array('<b>'.$filterObject->name.'</b>'));
            }
        }
        else
        {
            $response['message'] = _l('Something wrong with the submitted value');
        }
        break;
    case 'duplicate_filter':
        if(!$filterObject->id){
            $response['message'] = _l('invalid filter');
            exit(json_encode($response));
        }
        if ($filterObject->duplicateObject()) {
            $response['state'] = 'success';
            $response['message'] = _l('Filter %s duplicated', null, array('ID'._l(':').'<b>' . $filterObject->id . '</b>'));
        } else {
            $response['message'] = _l('Unable to duplicate filter');
        }
        break;
    default:
}
exit(json_encode($response));