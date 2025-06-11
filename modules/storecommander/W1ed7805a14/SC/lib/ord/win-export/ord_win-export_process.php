<?php
if (!defined('STORE_COMMANDER')) { exit; }

if (!defined('SC_ExportOrders_ACTIVE') || (int)SC_ExportOrders_ACTIVE !== 1)
{
    exit;
}

if (!empty(Tools::getValue("DEBUG"))) {
    ini_set("display_errors", "ON");
}
$expectedNumberOfOrders = (int)Tools::getValue('expectedNumberOfOrders');
$response = array(
    'state' => 'error',
);

$id_export = false;
switch(true)
{
    case (!isset($CRON) && Tools::isSubmit(ExportOrderTools::getDefaultHash())):
        $id_export = Tools::getValue(ExportOrderTools::getDefaultHash());
        break;
    case (isset($CRON) && $CRON):
        $exportsList = ExportOrder::getExportList();
        foreach($exportsList as $export)
        {
            if(Tools::getValue('process') == $export['token'])
            {
                $id_export = (int)$export[ExportOrder::$definition['primary']];
                break;
            }
        }
        break;
    default:
        echo ExportOrderTools::returnError('Wrong parameters');
        exit;
}

if(!$id_export){
    echo ExportOrderTools::returnError('Error');
    exit;
}

$orderExportOject = new ExportOrder($id_export);
$orderList = new ExportOrderFilter($orderExportOject->{ExportOrderFilter::$definition['primary']});

// init logger
ExportOrderTools::manageLogSize();

## CRON
if (isset($CRON)) {
    $exportTemplate = new ExportOrderMapping($orderExportOject->{ExportOrderMapping::$definition['primary']});
    $listing = $orderList->getList($exportTemplate->getAliases(), $orderExportOject->id_lang);
    $expectedNumberOfOrders = count($listing);
}

ExportOrderTools::addLog( '');
ExportOrderTools::addLog( '>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> EXPORT <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
ExportOrderTools::addLog( '');
ExportOrderTools::addLog( 'Export with Parameters : '.ExportOrderFilter::$definition['primary'].'=' . (int)$orderExportOject->{ExportOrderFilter::$definition['primary']} . ' & id_export_template=' . (int)$orderExportOject->{ExportOrderMapping::$definition['primary']} . ' & id_lang=' . (int)$orderExportOject->id_lang . ' & expectedNumberOfOrders=' . $expectedNumberOfOrders);
if (!$orderExportOject->{ExportOrderFilter::$definition['primary']} || !$orderExportOject->{ExportOrderMapping::$definition['primary']} || !$orderExportOject->id_lang || !$expectedNumberOfOrders) {
    $errorMessage = 'Missing expected parameters';
    if(isset($CRON)) {
        die(ExportOrderTools::returnError($errorMessage));
    } else {
        $response['message'] = $errorMessage;
        die(json_encode($response));
    }
}

// load objects
ExportOrderTools::addLog( 'Have loaded order list ' . (int)$orderExportOject->{ExportOrderFilter::$definition['primary']});
if (!Validate::isLoadedObject($orderList)) {
    $errorMessage = 'Unable to load the order list of id ' . (int)$orderExportOject->{ExportOrderFilter::$definition['primary']};
    if(isset($CRON)) {
        die(ExportOrderTools::returnError($errorMessage));
    } else {
        $response['message'] = $errorMessage;
        die(json_encode($response));
    }
}

// export
ExportOrderTools::addLog( 'Ready to call for export');
$utf8export = $orderList->export($orderExportOject->{ExportOrderMapping::$definition['primary']}, $orderExportOject->id_lang, $expectedNumberOfOrders);
$orderExportOject->date_last_export = date('Y-m-d H:i:s');
$orderExportOject->update();

// CSV export
$file = $orderExportOject->getFullPathFile();
$exportToFile = iconv( "UTF-8", "ISO-8859-1//TRANSLIT", $utf8export);
$savedFile = file_put_contents($file, $exportToFile);
if (isset($CRON)) {
    if($savedFile !== false)
    {
        header("HTTP/1.1 200 Cron done");
    } else {
        header("HTTP/1.1 503 Error");
    }
    exit;
}

/**
 * Ajax Response
 */
if($savedFile !== false)
{
    $response['state'] = 'success';
    $response['message'] = _l('Export successfully ended');
    $response['date_last_export'] = $orderExportOject->date_last_export;
} else {
    $response['message'] = _l('Error during saving file');
}
die(json_encode($response));