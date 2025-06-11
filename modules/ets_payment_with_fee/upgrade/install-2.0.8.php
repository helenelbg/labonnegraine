<?php
/**
 * Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
 */

if (!defined('_PS_VERSION_')) { exit; }

function upgrade_module_2_0_8()
{
    $sqls = array();
    $sqls[] ='CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_payment_cart` ( 
            `id_cart` INT(11) NOT NULL, 
            `ets_payment_module_name` VARCHAR(50) NOT NULL, 
            `id_payment_method` VARCHAR(50) NOT NULL, 
            `payment_option` INT(11) NOT NULL,
             PRIMARY KEY (`id_cart`)) ENGINE = InnoDB';
    if($sqls)
    {
        foreach($sqls as $sql)
            Db::getInstance()->execute($sql);
    }
    return true;
}