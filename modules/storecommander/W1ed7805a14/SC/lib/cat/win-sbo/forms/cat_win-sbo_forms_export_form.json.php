<?php

if (!defined('STORE_COMMANDER'))
{
    exit;
}

use Sc\Service\Shippingbo\ShippingboService;

$shippingBoService = ShippingboService::getInstance();
$config = $shippingBoService->getConfig();
$defaultDataExport = (array) json_decode($shippingBoService->getConfigValue('defaultDataExport'));

$form = [
    [
        'type' => 'settings',
        'position' => 'label-right',
        'labelAlign' => 'left',
    ],
    [
        'type' => 'label',
        'label' => _l('Export parameters'),
        'className' => 'section',
    ],
    [
        'type' => 'block',
        'className' => 'sbo_settings_forms',
        'list' => getShippingboExportFields($defaultDataExport),
    ],
    [
        'type' => 'label',
        'label' => '<a href="">'._l('How does export work ?').'</a>',
        'className' => 'message notice',
    ],
    [
        'type' => 'button',
        'name' => 'save_export',
        'className' => 'save_btn',
        'value' => _l('Save'),
    ],
];

function getShippingboExportFields($defaultDataExport)
{
    $fields = [];
    foreach ($defaultDataExport as $name => $value)
    {
        $fields[] = [
            'type' => 'checkbox',
            'name' => 'fields_export['.$name.']',
            'label' => ucfirst(_l($name)),
            'checked' => (bool) $defaultDataExport[$name],
            'disabled' => $name === 'userRef',
        ];
    }

    return $fields;
}

echo json_encode($form);
