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
        'label' => _l('API parameters'),
        'className' => 'section',
    ],
    [
        'type' => 'block',
        'className' => 'sbo_settings_forms',
        'list' => [
                [
                    'type' => 'input',
                    'name' => 'apiUser',
                    'label' => _l('API user', 1),
                    'value' => $config['apiUser']['value'],
                ],
                [
                    'type' => 'input',
                    'name' => 'apiToken',
                    'label' => _l('API token', 1),
                    'value' => $config['apiToken']['value'] ? $config['apiToken']['value'] : '',
                ],
                [
                    'type' => 'input',
                    'name' => 'apiVersion',
                    'label' => _l('API version', 1),
                    'value' => $config['apiVersion']['value'],
                ],
                [
                    'type' => 'label',
                    'label' => '<a href="">'._l('How do i find this information ?').'</a>',
                    'className' => 'message notice',
                ],
                [
                    'type' => 'button',
                    'name' => 'save_api',
                    'value' => _l('Save'),
                    'className' => 'save_btn',
                ],
            ],
    ],
];

echo json_encode($form);
