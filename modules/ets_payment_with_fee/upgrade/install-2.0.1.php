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
/**
 * @param Ets_payment_with_fee $object
 * @return bool
 */
function upgrade_module_2_0_1($object)
{
    $object->registerHook('displayHeader');
    $sqls = array();
    if(!Ets_paymentmethod_class::checkCreatedColumn('ets_paymentmethod_lang','confirmation_message'))
        $sqls[] = 'ALTER TABLE `'._DB_PREFIX_.'ets_paymentmethod_lang` ADD `confirmation_message` TEXT NOT NULL AFTER `description`';
    $sqls[] ='CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_paymentmethod_module` ( 
            `id_module` INT(11) NOT NULL , 
            `fee_type` VARCHAR(33) NOT NULL, 
            `fee_amount` FLOAT(10,2) NULL, 
            `percentage` FLOAT(10,2) NULL, 
            `max_fee` FLOAT(10,2) NULL, 
            `min_fee` FLOAT(10,2) NULL, 
            `free_for_order_over` FLOAT(10,2) NULL,
             PRIMARY KEY (`id_module`)) ENGINE = InnoDB';
    if($sqls)
    {
        foreach($sqls as $sql)
            Db::getInstance()->execute($sql);
    }
    $object->_installTab();
    return true;
}