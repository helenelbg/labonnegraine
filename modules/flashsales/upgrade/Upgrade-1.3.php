<?php
/**
* 2022 - Keyrnel
*
* NOTICE OF LICENSE
*
* The source code of this module is under a commercial license.
* Each license is unique and can be installed and used on only one shop.
* Any reproduction or representation total or partial of the module, one or more of its components,
* by any means whatsoever, without express permission from us is prohibited.
* If you have not received this module from us, thank you for contacting us.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future.
*
* @author    Keyrnel
* @copyright 2022 - Keyrnel
* @license   commercial
* International Registered Trademark & Property of Keyrnel
*/
if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_3($object)
{
    if (!$object->registerHook('displayHomeTab')
        || !$object->registerHook('displayHomeTabContent')
        || !$object->registerHook('rightColumn')
        || !$object->registerHook('leftColumn')) {
        return false;
    }

    Configuration::updateValue('PS_DISPLAY_FLASHSALES', '1');
    Configuration::updateValue('PS_DISPLAY_HOME_FLASHSALES', '1');
    Configuration::updateValue('PS_DISPLAY_BLOCK_FLASHSALES', '1');
    Configuration::updateValue('PS_DISPLAY_BLOCK_FLASHSALES', '1');
    Configuration::updateValue('FLASHSALE_PRODUCTS_NB', '8');
    Configuration::updateValue('PS_FS_IMG', 'flash_sales.jpg');

    $values = [];
    foreach (Language::getLanguages(false) as $language) {
        $values['NAME'] = [$language['id_lang'] => $language['iso_code'] == 'fr' ? 'Ventes Flash' : 'Flash Sales'];
        $values['DESC'] = [$language['id_lang'] => $language['iso_code'] == 'fr' ? 'Toutes les ventes flash' : 'All flash sales'];

        foreach ($values as $key => $value) {
            Configuration::updateValue('PS_FS_' . $key, $value);
        }
    }

    return true;
}
