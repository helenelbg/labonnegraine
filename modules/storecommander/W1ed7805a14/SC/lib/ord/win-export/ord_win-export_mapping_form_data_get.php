<?php
if (!defined('STORE_COMMANDER')) { exit; }

if (!defined('SC_ExportOrders_ACTIVE') || (int)SC_ExportOrders_ACTIVE !== 1)
{
    exit;
}

$response = array(
    'state' => 'error',
    'message' => '',
);

$mappingId = (int) Tools::getValue(ExportOrderMapping::$definition['primary']);
if(!$mappingId)
{
    $response['message'] = _l('wrong parameters');
    exit(json_encode($response));
}

$exportOrderMappinObject = new ExportOrderMapping($mappingId);

if(!$exportOrderMappinObject->id)
{
    $response['message'] = _l('%s not found',null,array('order mapping object'));
    exit(json_encode($response));
}
$response['state'] = 'success';

$fields = array();
if(!empty($exportOrderMappinObject->fields))
{
    $fields = explode('__', $exportOrderMappinObject->fields);
}

$response['message'] = array(
    'separator' => $exportOrderMappinObject->separator,
    'fields' => $fields,
    'properties' => ExportOrderTools::explodeKeyValue($exportOrderMappinObject->format_properties, '@@', '::')
);

exit(json_encode($response));