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
function upgrade_module_1_1_8($object)
{
    $sqls= array();
    if(!$object->checkCreatedColumn('ets_am_reward','used'))
        $sqls[]="ALTER TABLE `"._DB_PREFIX_."ets_am_reward` ADD COLUMN `used` INT(11) NOT NULL";
    if($sqls)
    {
        foreach($sqls as $sql)
            Db::getInstance()->execute($sql);
    }
    $customers = Db::getInstance()->executeS('SELECT DISTINCT id_customer FROM `'._DB_PREFIX_.'ets_am_reward` WHERE program="'.pSQL(EAM_AM_LOYALTY_REWARD).'"');
    if($customers)
    {
        foreach($customers as $customer)
        {
            $sql = "SELECT amount,id_ets_am_reward_usage FROM `" . _DB_PREFIX_ . "ets_am_reward_usage` WHERE `type` = '".pSQL(EAM_AM_LOYALTY_REWARD)."' AND `id_customer` = " . (int)$customer['id_customer'] . " AND `id_shop` = " . (int)Context::getContext()->shop->id . "  AND `deleted` = 0 AND status!=0";
            $totalSpentLoys  = Db::getInstance()->executeS($sql);
            if($totalSpentLoys)
            {
                foreach($totalSpentLoys as $totalSpentLoy)
                    Ets_Loyalty::loyRewardUsed($totalSpentLoy['amount'],$totalSpentLoy['id_ets_am_reward_usage'],$customer['id_customer']);
                
            }
        }
    }
    
    return true;
}