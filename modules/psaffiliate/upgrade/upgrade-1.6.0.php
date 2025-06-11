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

function upgrade_module_1_6_0($module)
{
    $res = Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'aff_category_rates` ADD COLUMN `multiplier` FLOAT NOT NULL DEFAULT 1;');
    $res &= Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'aff_product_rates` ADD COLUMN `multiplier` FLOAT NOT NULL DEFAULT 1;');

    return (bool)$res;
}
