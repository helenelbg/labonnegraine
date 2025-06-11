<?php
if (!defined('STORE_COMMANDER')) {
    exit;
}

use Sc\Service\Shippingbo\Process\ShippingboImport;
use Sc\Service\Shippingbo\Repository\Prestashop\SegmentRepository;
use Sc\Service\Shippingbo\ShippingboService;

$shippingBoService = ShippingboService::getInstance();
$defaultDataImport = (array)json_decode($shippingBoService->getConfigValue('defaultDataImport'));
$process = Tools::getIsset('process_started', false);

$shippingBoConfig = $shippingBoService->getConfig();

function differentUnitOnAllShops()
{
    $dimensionUnitDefaultShop = Configuration::get('PS_DIMENSION_UNIT',null,null,Configuration::get('PS_DEFAULT_SHOP'));
    $weightUnitDefaultShop = Configuration::get('PS_WEIGHT_UNIT',null,null,Configuration::get('PS_DEFAULT_SHOP'));
    foreach(SCI::getAllShops() as $shop){
        return $dimensionUnitDefaultShop !== Configuration::get('PS_DIMENSION_UNIT',null,null,$shop['id_shop']) || $weightUnitDefaultShop !== Configuration::get('PS_WEIGHT_UNIT',null,null,$shop['id_shop']);
    }

    return false;

}


$formShops = [
    [
        'type' => 'settings',
        'position' => 'label-left'
    ],
    [
        'type' => 'label',
        'label' => _l('Shops', 1),
        'className' => 'section'
    ],
    [
        "type" => "block",
        "className" => "sbo_settings_forms",
        "width" => "300px",
        "list" => getShopCheckbox($shippingBoConfig)
    ],
    [
        'type' => 'label',
        'label' => _l('Unit conversions', 1),
        'className' => 'section'
    ]
];

if(differentUnitOnAllShops()){
    $formUnits =[
        [
            'type' => 'container',
            'name' => 'error_message',
            'position' => 'label-left',
            'label' => "<p class=\"message error\">"._l('All shops need to use the same weight and dimension units', 1)."</p>",
        ],
    ];

} else {
    $defaultUnits = json_decode($shippingBoService->getConfigValue("unitConversion"),true);
    $formUnits =[
        [
            'type' => 'input',
            'label' => _l('Weight coefficient') . ' (' . '1'.Configuration::get('PS_WEIGHT_UNIT', null, null, Configuration::get('PS_SHOP_DEFAULT')).'=<b>X</b>g' . ')',
            'name' => 'coeff[weight]',
            "value" => $defaultUnits['weight']?:1000,
        ],
        [
            'type' => 'input',
            'label' => _l('Dimension coefficient') . ' (' . '1'.Configuration::get('PS_DIMENSION_UNIT', null, null, Configuration::get('PS_SHOP_DEFAULT')).'=<b>X</b>cm' . ')',
            'name' => 'coeff[dimension]',
            "value" => $defaultUnits['dimension']?:10,
        ],
        [
            'type' => 'button',
            'name' => 'save_shops',
            'className' => 'save_btn',
            'value' => _l('Save')
        ]
    ];
}


$form = array_merge($formShops,$formUnits);




function getShopCheckbox($config)
{
    $allShops = SCI::getAllShops();
    $defaultShop = array_column($allShops, null, 'id_shop')[Configuration::get('PS_SHOP_DEFAULT')];

    $shopCheckboxes = [];
    foreach ($allShops as $shop) {
        $shopName = $shop['name'];
        $shopName .= $defaultShop['id_shop'] === $shop['id_shop'] ? ' (' . _l('default shop', 1) . ')' : '';
        $shopCheckboxes[] = [
            'type' => 'checkbox',
            'label' =>$shopName,
            'position' => 'label-right',
            'name' => 'shops[' . $shop['id_shop'] . ']',
            "value" => $shop['id_shop'],
            "checked" => in_array($shop['id_shop'], explode(',', $config['importToShop']['value']))
        ];
    }

    return $shopCheckboxes;
}


echo json_encode($form);


?>



