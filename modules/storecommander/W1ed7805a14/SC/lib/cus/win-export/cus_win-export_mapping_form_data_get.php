<?php
if (!defined('STORE_COMMANDER')) { exit; }

if (!defined('SC_ExportCustomers_ACTIVE') || (int)SC_ExportCustomers_ACTIVE !== 1)
{
    exit;
}

$response = array(
    'state' => 'error',
    'message' => '',
);

$mappingId = (int) Tools::getValue(ExportCustomerMapping::$definition['primary']);
if(!$mappingId)
{
    $response['message'] = _l('wrong parameters');
    exit(json_encode($response));
}

$exportCustomerMappinObject = new ExportCustomerMapping($mappingId);

if(!$exportCustomerMappinObject->id)
{
    $response['message'] = _l('%s not found',null,array('order mapping object'));
    exit(json_encode($response));
}
$response['state'] = 'success';

$fields = array();
if(!empty($exportCustomerMappinObject->fields))
{
    $fields = explode('__', $exportCustomerMappinObject->fields);
}

$response['message'] = array(
    'separator' => $exportCustomerMappinObject->separator,
    'fields' => $fields,
    'properties' => ExportCustomerTools::explodeKeyValue($exportCustomerMappinObject->format_properties, '@@', '::')
);

exit(json_encode($response));