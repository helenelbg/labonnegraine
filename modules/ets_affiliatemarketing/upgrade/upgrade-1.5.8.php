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
function upgrade_module_1_5_8($module)
{
    try {
        Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'ets_am_reward` DROP INDEX IF EXISTS `ets_am_reward_index_c`');
        Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'ets_am_loy_reward` DROP INDEX IF EXISTS `ets_am_loy_reward_index_c`');
        Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'ets_am_aff_reward` DROP INDEX IF EXISTS `ets_am_aff_reward_index_c`');
        Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'ets_am_sponsor` DROP INDEX IF EXISTS `ets_am_sponsor_index_c`');
        Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'ets_am_participation` DROP INDEX IF EXISTS `ets_am_participation_index_c`');
        Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'ets_am_reward_usage` DROP INDEX IF EXISTS `ets_am_reward_usage_index_c`');
        Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'ets_am_invitation` DROP INDEX IF EXISTS `ets_am_invitation_index_c`');
        Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'ets_am_banner` DROP INDEX IF EXISTS `ets_am_banner_index_c`');
        Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'ets_am_payment_method` DROP INDEX IF EXISTS `ets_am_payment_method_index_c`');
        Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'ets_am_payment_method_lang` DROP INDEX IF EXISTS `ets_am_payment_method_lang_index_c`');
        Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'ets_am_payment_method_field` DROP INDEX IF EXISTS `ets_am_payment_method_field_index_c`');
        Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'ets_am_payment_method_field_lang` DROP INDEX IF EXISTS `ets_am_payment_method_field_lang_index_c`');
        Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'ets_am_withdrawal` DROP INDEX IF EXISTS `ets_am_withdrawal_index_c`');
        Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'ets_am_withdrawal_field` DROP INDEX IF EXISTS `ets_am_withdrawal_field_index_c`');
        Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'ets_am_voucher` DROP INDEX IF EXISTS `ets_am_voucher_index_c`');
        Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'ets_am_user` DROP INDEX IF EXISTS `ets_am_user_index_c`');
        Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'ets_am_reward_product` DROP INDEX IF EXISTS `ets_am_reward_product_index_c`');
        Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'ets_am_access_key` DROP INDEX IF EXISTS `ets_am_access_key_index_c`');
        Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'ets_am_product_view` DROP INDEX IF EXISTS `ets_am_product_view_index_c`');
        Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'ets_am_cart_rule_seller` DROP INDEX IF EXISTS `ets_am_cart_rule_seller_index_c`');
        EtsAmAdmin::addIndexTable();
    }
    catch (Exception $e)
    {
        unset($e);
    }
    return true;
}