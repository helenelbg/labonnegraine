<?php
if (!defined('STORE_COMMANDER')) { exit; }

if (!defined('SC_ExportCustomers_ACTIVE') || (int)SC_ExportCustomers_ACTIVE !== 1)
{
    exit;
}

if (!empty(Tools::getValue("DEBUG"))) {
    ini_set("display_errors", "ON");
}
$expectedNumberOfCustomers = (int)Tools::getValue('expectedNumberOfCustomers');
$response = array(
    'state' => 'error',
);

$id_export = false;
switch(true)
{
    case (!isset($CRON) && Tools::isSubmit(ExportCustomerTools::getDefaultHash())):
        $id_export = Tools::getValue(ExportCustomerTools::getDefaultHash());
        break;
    case (isset($CRON) && $CRON):
        $exportsList = ExportCustomer::getExportList();
        foreach($exportsList as $export)
        {
            if(Tools::getValue('process') == $export['token'])
            {
                $id_export = (int)$export[ExportCustomer::$definition['primary']];
                break;
            }
        }
        break;
    default:
        echo ExportCustomerTools::returnError('Wrong parameters');
        exit;
}

if(!$id_export){
    echo ExportCustomerTools::returnError('Error');
    exit;
}

$customerExportOject = new ExportCustomer($id_export);
$customerList = new ExportCustomerFilter($customerExportOject->{ExportCustomerFilter::$definition['primary']});

// init logger
ExportCustomerTools::manageLogSize();

## CRON
if (isset($CRON)) {
    $exportTemplate = new ExportCustomerMapping($customerExportOject->{ExportCustomerMapping::$definition['primary']});
    $listing = $customerList->getList($exportTemplate->getAliases(), $customerExportOject->id_lang);
    $expectedNumberOfCustomers = count($listing);
}

ExportCustomerTools::addLog( '');
ExportCustomerTools::addLog( '>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> EXPORT <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
ExportCustomerTools::addLog( '');
ExportCustomerTools::addLog( 'Export with Parameters : '.ExportCustomerFilter::$definition['primary'].'=' . (int)$customerExportOject->{ExportCustomerFilter::$definition['primary']} . ' & id_export_template=' . (int)$customerExportOject->{ExportCustomerMapping::$definition['primary']} . ' & id_lang=' . (int)$customerExportOject->id_lang . ' & expectedNumberOfCustomers=' . $expectedNumberOfCustomers);
if (!$customerExportOject->{ExportCustomerFilter::$definition['primary']} || !$customerExportOject->{ExportCustomerMapping::$definition['primary']} || !$customerExportOject->id_lang || !$expectedNumberOfCustomers) {
    $errorMessage = 'Missing expected parameters';
    if(isset($CRON)) {
        die(ExportCustomerTools::returnError($errorMessage));
    } else {
        $response['message'] = $errorMessage;
        die(json_encode($response));
    }
}

// load objects
ExportCustomerTools::addLog( 'Have loaded customer list ' . (int)$customerExportOject->{ExportCustomerFilter::$definition['primary']});
if (!Validate::isLoadedObject($customerList)) {
    $errorMessage = 'Unable to load the customer list of id ' . (int)$customerExportOject->{ExportCustomerFilter::$definition['primary']};
    if(isset($CRON)) {
        die(ExportCustomerTools::returnError($errorMessage));
    } else {
        $response['message'] = $errorMessage;
        die(json_encode($response));
    }
}

// export
ExportCustomerTools::addLog( 'Ready to call for export');
try {
    $utf8export = $customerList->export($customerExportOject->{ExportCustomerMapping::$definition['primary']}, $customerExportOject->id_lang, $expectedNumberOfCustomers);
} catch(\Exception $e)
{
    if(!isset($CRON))
    {
        $response['message'] = $e->getMessage();
        die(json_encode($response));
    }
    $utf8export = $e->getMessage();
}
$customerExportOject->date_last_export = date('Y-m-d H:i:s');
$customerExportOject->update();

// CSV export
$file = $customerExportOject->getFullPathFile();
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
    $response['date_last_export'] = $customerExportOject->date_last_export;
} else {
    $response['message'] = _l('Error during saving file');
}
die(json_encode($response));