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
    'reset_indexes',
    'submit_configuration_form',
);

if(empty($action)
    || !in_array($action,$allowedActions))
{
    $response['message'] = 'invalid params';
    exit(json_encode($response));
}

switch ($action)
{
    case 'reset_indexes':
        $sql = 'UPDATE '._DB_PREFIX_.'order_detail 
                SET `tax_name`="", 
                    `tax_rate`="", 
                    `sc_qc_product_price`=""';
        if(Db::getInstance()->execute($sql))
        {
            $response['state'] = 'success';
            $response['message'] = _l('Indexes successfully reseted');
        } else {
            $response['message'] = _l('Error during reseting indexes');
        }
        break;
    case 'submit_configuration_form':
        $formData = Tools::getValue('formData', '');
        if($formData) {
            if(!empty($formData['error_margin']))
            {
                $error_margin = str_replace(',','.',$formData['error_margin']);
                $error_margin = (float)number_format($error_margin, 2, ".", "");
            } else {
                $error_margin = '';
            }

            $firstDone = SCI::updateConfigurationValue('SC_QUICKACCOUNTING_MARGIN',$error_margin);
            $secondDone = SCI::updateConfigurationValue('SC_QUICKACCOUNTING_EXCLUDE_RATE',implode("\n",$formData['excluded_tax_rate']));
            if($firstDone && $secondDone)
            {
                $response['state'] = 'success';
                $response['message'] = _l('Configuration updated');
            } else {
                $response['message'] = _l('Unable to update configuration');
            }
        }
        break;
    default:
}
exit(json_encode($response));