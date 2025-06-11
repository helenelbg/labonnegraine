<?php

if (!defined('STORE_COMMANDER'))
{
    exit;
}

use Sc\Service\Shippingbo\ShippingboService;

$shippingBoService = ShippingboService::getInstance();

extract(Tools::getValue('fields_import'));
try
{
    $shippingBoService
        ->getImportProcess()
        ->setSegmentType($segment_type)
        ->setProductNameType($product_name)
        ->setFieldsToImport([
            'width' => (bool) $width,
            'height' => (bool) $height,
            'length' => (bool) $length,
            'weight' => (bool) $weight,
        ])
        ->startImport();
}
catch (Exception $e)
{
    $shippingBoService->addError($e);
}
finally
{
    $shippingBoService->sendResponse();
}
