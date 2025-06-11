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
function upgrade_module_1_6_4()
{
    try {
        Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'ets_am_reward_product` set quantity =1 where program ="loy"');
        $affs = Db::getInstance()->executeS('SELECT SUM(rp.amount*rp.quantity) as total_amount,r.amount,rp.id_ets_am_reward 
        FROM `'._DB_PREFIX_.'ets_am_reward` r
        JOIN `'._DB_PREFIX_.'ets_am_reward_product` rp ON(r.id_ets_am_reward = rp.id_ets_am_reward AND rp.program ="aff")
        GROUP BY rp.id_ets_am_reward
        HAVING total_amount !=r.amount');
        $ids = array();
        if($affs)
        {
            foreach($affs as $aff)
                $ids[] = (int)$aff['id_ets_am_reward'];
            Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'ets_am_reward_product` SET quantity =1 WHERE id_ets_am_reward IN ('.implode(',',array_map('intval',$ids)).') AND program ="aff"');
        }
    }
    catch (Exception $e)
    {
        unset($e);
    }
    return true;
}