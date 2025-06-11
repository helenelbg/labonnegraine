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

if (!defined('_PS_VERSION_'))
    exit;
function upgrade_module_1_1_0($object)
{
    $sqls= array();
    $sqls[]="CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "ets_am_cart_rule_seller` (
                    `id_ets_am_cart_rule_seller` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                    `id_customer` INT(10) UNSIGNED NOT NULL,
                    `id_cart_rule` INT(10) UNSIGNED NOT NULL,
                    `code` VARCHAR(32),
                    `date_added` DATE DEFAULT NULL,
                    PRIMARY KEY (`id_ets_am_cart_rule_seller`)
                ) ENGINE = "._MYSQL_ENGINE_." DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
    if(!$object->checkCreatedColumn('ets_am_reward_usage','type'))
        $sqls[]="ALTER TABLE `"._DB_PREFIX_."ets_am_reward_usage` ADD COLUMN `type` VARCHAR(50) DEFAULT 'loy'";
    if($sqls)
        foreach($sqls as $sql)
            Db::getInstance()->execute($sql);
    $object->registerHook('actionCustomerLogoutAfter');
    $object->registerHook('actionCartSave');
    return true;
}
