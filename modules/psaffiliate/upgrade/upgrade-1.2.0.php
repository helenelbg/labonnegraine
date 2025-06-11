<?php
/**
 * NOTICE OF LICENSE
 *
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * You must not modify, adapt or create derivative works of this source code.
 *
 * @author    Active Design <office@activedesign.ro>
 * @copyright 2016-2018 Active Design
 * @license   LICENSE.txt
 */

if (!defined('_PS_VERSION_')) {
    exit;
}


function upgrade_module_1_2_0($module)
{
    $sql = array();
    $sql[] = "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."aff_category_rates` (
    `id_category` int(11) NOT NULL,
    `rate_percent` FLOAT NOT NULL,
    `rate_value` FLOAT NOT NULL,
    PRIMARY KEY (`id_category`)
    ) ENGINE = '"._MYSQL_ENGINE_."';";
    $sql[] = "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."aff_product_rates` (
    `id_product` int(11) NOT NULL,
    `rate_percent` FLOAT NOT NULL,
    `rate_value` FLOAT NOT NULL,
    PRIMARY KEY (`id_product`)
    ) ENGINE = '"._MYSQL_ENGINE_."';";

    foreach ($sql as $s) {
        if (!Db::getInstance()->execute($s)) {
            return false;
        }
    }

    $primaryTabId = Tab::getIdFromClassName('PsaffiliateAdmin');
    if ($primaryTabId) {
        $secondaryControllers = array(
            'AdminPsaffiliateCategoryRates' => $module->l('Category Commission Rates'),
            'AdminPsaffiliateProductRates' => $module->l('Product Commission Rates'),
        );
        foreach ($secondaryControllers as $class_name => $name) {
            $tab = new Tab;

            $tab->class_name = $class_name;
            $tab->id_parent = $primaryTabId;
            $tab->module = $module->name;
            $tab->name[(int)(Configuration::get('PS_LANG_DEFAULT'))] = $name;
            if (!$tab->add()) {
                return false;
            }
        }

        return true;
    }
}
