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

function upgrade_module_1_32($object)
{
    Configuration::updateValue('PS_COUNTDOWN_BORDER', '1');
    Configuration::updateValue('PS_FS_NAME_COLOR', '#ffffff');
    $values = [];
    foreach (Language::getLanguages(false) as $language) {
        $values['COUNTDOWN_TEXT'] = [$language['id_lang'] => $language['iso_code'] == 'fr' ? 'Vente flash' : 'Flash sale'];
        foreach ($values as $key => $value) {
            Configuration::updateValue('PS_' . $key, $value);
        }
    }

    return true;
}
