<?php
if (!defined('STORE_COMMANDER')) { exit; }

if (!defined('SC_ExportCustomers_ACTIVE') || (int) SC_ExportCustomers_ACTIVE !== 1)
{
    exit;
}

$response = array(
    'state' => 'error',
    'message' => '',
);

$action = Tools::getValue('action');
$allowedActions = array(
    'add_export',
    'delete_export',
    'update_export',
    'reset_token',
    'duplicate_export'
);

if(empty($action)
    || !in_array($action,$allowedActions))
{
    $response['message'] = 'invalid params';
    exit(json_encode($response));
}

$exportId = Tools::getValue(ExportCustomer::$definition['primary'],null);
switch ($action)
{
    case 'add_export':
        $exportObject = new ExportCustomer();
        $defaultLangId = (int) Configuration::get('PS_LANG_DEFAULT', null, 0, 0);
        $defaultLang = new Language($defaultLangId);
        if(!in_array($defaultLang->iso_code,array('fr','en')))
        {
            $defaultLangId = (int) Language::getIdByIso('en');
            $defaultLang = new Language($defaultLangId);
        }
        $exportObject->id_lang = (int)$defaultLang->id;
        $exportObject->filename = 'export_'.time();
        if ($exportObject->add()) {
            $response['state'] = 'success';
            $response['message'] = _l('New export added');
        } else {
            $response['message'] = _l('Unable to add export');
        }
        break;
    case 'delete_export':
        $exportObject = new ExportCustomer($exportId);
        if(!$exportObject->id){
            $response['message'] = _l('invalid export');
            exit(json_encode($response));
        }
        if ($exportObject->delete()) {
            $response['state'] = 'success';
            $response['message'] = _l('Export %s deleted', null, array('ID'._l(':').'<b>' . $exportObject->id . '</b>'));
        } else {
            $response['message'] = _l('Unable to delete export');
        }
        break;
    case 'update_export':
        $exportObject = new ExportCustomer($exportId);
        $field = Tools::safeOutput(Tools::getValue('field', ''));
        $value = Tools::getValue('value', '');
        if(property_exists($exportObject, $field))
        {
            if($field == 'filename')
            {
                $value = str_replace(' ', '-', trim(link_rewrite($value),'-_'));
            } else {
                $value = Tools::safeOutput($value);
            }
            $exportObject->{$field} = $value;
            if($exportObject->update())
            {
                $response['state'] = 'success';
                 if($field == 'filename')
                 {
                     $response['filename'] = $exportObject->filename;
                 }
                $response['message'] = _l('Export %s updated', null, array('ID'._l(':').'<b>'.$exportObject->id.'</b>'));
            }
            else
            {
                $response['message'] = _l('Enable to update export %s', null, array('ID'._l(':').'<b>'.$exportObject->id.'</b>'));
            }
        }
        else
        {
            $response['message'] = _l('Something wrong with the submitted value');
        }
        break;
    case 'duplicate_export':
        $exportObject = new ExportCustomer($exportId);
        if(!$exportObject->id){
            $response['message'] = _l('invalid export');
            exit(json_encode($response));
        }
        if ($exportObject->duplicateObject()) {
            $response['state'] = 'success';
            $response['message'] = _l('Export %s duplicated', null, array('ID'._l(':').'<b>' . $exportObject->id . '</b>'));
        } else {
            $response['message'] = _l('Unable to duplicate export');
        }
        break;
    case 'reset_token':
        $exportList = Tools::getValue('export_list',null);
        $exportList = explode(',',$exportList);
        foreach($exportList as $id_export)
        {
            $exportObject = new ExportCustomer($id_export);
            if(!$exportObject->id)
            {
                continue;
            }
            $exportObject->token = generateToken();
            $exportObject->update();
        }
        $response['state'] = 'success';
        $response['message'] = _l('Tokens reseted');
        break;
    default:
}
exit(json_encode($response));