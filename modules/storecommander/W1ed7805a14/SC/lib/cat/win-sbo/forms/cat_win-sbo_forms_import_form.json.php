<?php
if (!defined('STORE_COMMANDER'))
{
    exit;
}
use Sc\Service\Shippingbo\Process\ShippingboImport;
use Sc\Service\Shippingbo\Repository\Prestashop\SegmentRepository;
use Sc\Service\Shippingbo\ShippingboService;

$shippingBoService = ShippingboService::getInstance();
$defaultDataImport = (array) json_decode($shippingBoService->getConfigValue('defaultDataImport'));
$process = Tools::getIsset('process');

$shippingBoConfig = $shippingBoService->getConfig();

$form = [
    [
        'type' => 'settings',
        'position' => 'label-right',
    ],
    [
        'type' => 'label',
        'label' => _l('Positionner les nouveaux produits dans'),
        'className' => 'section',
    ],
    [
        'type' => 'radio',
        'name' => 'fields_import[segment_type]',
        'value' => SegmentRepository::TYPE_PENDING,
        'label' => _l("Segment 'Shippingbo/Pending products'"),
        'checked' => $defaultDataImport['segment_type'] === SegmentRepository::TYPE_PENDING,
    ],
    [
        'type' => 'radio',
        'name' => 'fields_import[segment_type]',
        'value' => SegmentRepository::TYPE_DATE,
        'label' => _l("New segment 'Shippingbo/products from YYMMDD HHMMSS'"),
        'checked' => $defaultDataImport['segment_type'] === SegmentRepository::TYPE_DATE,
    ],
    [
        'type' => 'label',
        'label' => _l('Nom des nouveaux produits'),
        'className' => 'section',
    ],
    [
        'type' => 'radio',
        'name' => 'fields_import[product_name]',
        'value' => ShippingboImport::PRODUCT_NAME_TYPE_SKU,
        'label' => "'"._l('product').'/'._l('batch').'/'._l('pack')."' + "._l('Logistic SKU'),
        'checked' => $defaultDataImport['product_name'] === ShippingboImport::PRODUCT_NAME_TYPE_SKU,
    ],
    [
        'type' => 'radio',
        'name' => 'fields_import[product_name]',
        'value' => ShippingboImport::PRODUCT_NAME_TYPE_TITLE,
        'label' => _l('Logistic Title'),
        'checked' => $defaultDataImport['product_name'] === ShippingboImport::PRODUCT_NAME_TYPE_TITLE,
    ],
    [
        'type' => 'label',
        'label' => _l('Fields to import'),
        'className' => 'section',
    ],
    [
        'type' => 'checkbox',
        'name' => 'fields_import[width]',
        'label' => _l('Width'),
        'checked' => (bool) $defaultDataImport['width'],
    ],
    [
        'type' => 'checkbox',
        'name' => 'fields_import[height]',
        'label' => _l('Height'),
        'checked' => (bool) $defaultDataImport['height'],
    ],
    [
        'type' => 'checkbox',
        'name' => 'fields_import[length]',
        'label' => _l('Length'),
        'checked' => (bool) $defaultDataImport['length'],
    ],
    [
        'type' => 'checkbox',
        'name' => 'fields_import[weight]',
        'label' => _l('Weight'),
        'checked' => (bool) $defaultDataImport['weight'],
    ],
    [
        'type' => 'label',
        'label' => '<a href="">'._l('How does import work ?').'</a>',
        'className' => 'message notice',
    ],
    [
        'type' => 'button',
        'name' => $process ? 'startImport' : 'save_import',
        'className' => 'save_btn',
        'value' => $process ? _l('Import Shippingbo data to Prestashop') : _l('Save'),
    ],
];

echo json_encode($form);

?>



