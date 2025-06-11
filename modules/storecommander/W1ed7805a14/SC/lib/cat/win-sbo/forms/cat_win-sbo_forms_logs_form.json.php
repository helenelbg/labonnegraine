<?php

if (!defined('STORE_COMMANDER'))
{
    exit;
}

use Sc\Service\Shippingbo\ShippingboService;

$shippingBoService = ShippingboService::getInstance();
$config = $shippingBoService->getConfig();

$form = [
    [
        'type' => 'settings',
        'position' => 'label-left',
        'labelAlign' => 'left',
    ],

    [
        'type' => 'label',
        'label' => _l('Debug'),
    ],

    [
        'type' => 'block',
        'className' => 'sbo_settings_forms',
        'list' => [
                [
                    'type' => 'input',
                    'name' => 'logFilesToKeep',
                    'label' => _l('Log keep treshold (days)', 1),
                    'value' => $config['logFilesToKeep']['value'],
                ],

                [
                    'type' => 'button',
                    'name' => 'save_logs',
                    'value' => _l('Save'),
                    'className' => 'save_btn',
                ],
            ],
    ],
];

echo json_encode($form);
