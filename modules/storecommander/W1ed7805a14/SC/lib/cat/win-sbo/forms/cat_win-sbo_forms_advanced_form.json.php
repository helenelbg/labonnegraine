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
            'position' => 'label-right',
            'labelAlign' => 'left',
        ],
        [
            'type' => 'container',
            'name' => 'warning_message',
            'position' => 'label-left',
            'label' => '<p class="message warning">'._l('Actions in this panel may cause data loss, please use it only if you know what you\'re doing', 1).'</p>',
        ],
        [
            'type' => 'block',
            'className' => 'sbo_expert_form',
            'list' => [
                        [
                            'type' => 'checkbox',
                            'name' => 'clear_sbo_buffer',
                            'label' => _l('Clear buffer tables'),
                            'checked' => false,
                        ],
                        [
                            'type' => 'checkbox',
                            'name' => 'clear_sbo_relation',
                            'label' => _l('Clear relation table'),
                            'checked' => false,
                        ],
                        [
                            'type' => 'checkbox',
                            'name' => 'clear_sbo_segment',
                            'label' => _l('Remove Shippingbo Segmentation and all containing products'),
                            'checked' => false,
                        ],
                        [
                            'type' => 'checkbox',
                            'name' => 'clear_sbo_service',
                            'label' => _l('Clear Shippingbo settings'),
                            'checked' => false,
                        ],
                        [
                            'type' => 'button',
                            'name' => 'save_advanced',
                            'value' => _l('Validate'),
                            'className' => 'save_btn',
                        ],
                ],
        ],
];

echo json_encode($form);
