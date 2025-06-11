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

function upgrade_module_1_6_5($module)
{
    $res = Db::getInstance()->execute("CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."aff_cart` (
`id_cart` INT NOT NULL,
`id_affiliate` INT NOT NULL,
`id_campaign` INT NOT NULL,
`date` DATETIME NOT NULL,
PRIMARY KEY (`id_cart`)
) ENGINE="._MYSQL_ENGINE_.";");

    $res &= $module->registerHook('actionCartSave');

    return (bool)$res;
}
