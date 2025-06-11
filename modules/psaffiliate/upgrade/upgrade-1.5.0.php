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

function upgrade_module_1_5_0($module)
{
    $res = Db::getInstance()->insert('aff_configuration', array(
        array(
            'name' => 'first_order_multiplier',
            'value' => '1',
        ),
        array(
            'name' => 'commissions_for_life',
            'value' => '0',
        ),
        array(
            'name' => 'override_commissions_for_life',
            'value' => '0',
        ),
        array(
            'name' => 'commission_for_life_multiplier',
            'value' => '1',
        ),
    ), false, true, Db::REPLACE);

    $res &= Db::getInstance()->execute('
        CREATE TABLE IF NOT EXISTS`'._DB_PREFIX_.'aff_customers` (
          `id_aff_customer` int(11) NOT NULL AUTO_INCREMENT,
          `id_affiliate` int(11) NOT NULL,
          `id_customer` int(11) NOT NULL,
          `date_add` DATETIME NULL,
        PRIMARY KEY (`id_aff_customer`)
        ) ENGINE='._MYSQL_ENGINE_.';
    ');
    $res &= $module->registerHook('displayAdminCustomers');

    return (bool)$res;
}
