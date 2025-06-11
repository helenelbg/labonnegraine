<?php
/**
 * Cart Reminder
 *
 *    @author    EIRL Timactive De Véra
 *    @copyright Copyright (c) TIMACTIVE 2015 -EIRL Timactive De Véra
 *    @license   Commercial license
 *
 *    @category pricing_promotion
 *
 *    @version 1.1.0
 *************************************
 **         CART REMINDER            *
 **          V 1.0.4                 *
 *************************************
 *  _____ _            ___       _   _
 * |_   _(_)          / _ \     | | (_)
 *   | |  _ _ __ ___ / /_\ \ ___| |_ ___   _____
 *   | | | | '_ ` _ \|  _  |/ __| __| \ \ / / _ \
 *   | | | | | | | | | | | | (__| |_| |\ V /  __/
 *   \_/ |_|_| |_| |_\_| |_/\___|\__|_| \_/ \___|
 *
 * +
 * + Languages: EN, FR
 * + PS version: 1.5,1.6
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_0_6($object, $install = false)
{
    $result_up = false;
    if ($object->active || $install) {
        $result_up = true;
        // INDEX ta_cartreminder_journal  id_cart
        $result = Db::getInstance()->ExecuteS(
            'SHOW INDEX FROM `' . _DB_PREFIX_ . 'ta_cartreminder_journal` WHERE `key_name` = "id_cart"'
        );
        if (!$result || !Db::getInstance()->numRows()) {
            Db::getInstance()->Execute(
                'ALTER TABLE `' . _DB_PREFIX_ . 'ta_cartreminder_journal`
                ADD INDEX `id_cart` ( `id_cart`)'
            );
        }
        // INDEX ta_cartreminder_journal  id_customer
        $result = Db::getInstance()->ExecuteS(
            'SHOW INDEX FROM `' . _DB_PREFIX_ . 'ta_cartreminder_journal` WHERE `key_name` = "id_customer"'
        );
        if (!$result || !Db::getInstance()->numRows()) {
            Db::getInstance()->Execute(
                'ALTER TABLE `' . _DB_PREFIX_ . 'ta_cartreminder_journal`
                ADD INDEX `id_customer` ( `id_customer`)'
            );
        }
        // INDEX ta_cartreminder_journal  state
        $result = Db::getInstance()->ExecuteS(
            'SHOW INDEX FROM `' . _DB_PREFIX_ . 'ta_cartreminder_journal` WHERE `key_name` = "state"'
        );
        if (!$result || !Db::getInstance()->numRows()) {
            Db::getInstance()->Execute(
                'ALTER TABLE `' . _DB_PREFIX_ . 'ta_cartreminder_journal`
                ADD INDEX `state` ( `state`)'
            );
        }
        // INDEX ta_cartreminder_journal_reminder  id_journal
        $result = Db::getInstance()->ExecuteS(
            'SHOW INDEX FROM `' . _DB_PREFIX_ . 'ta_cartreminder_journal_reminder` WHERE `key_name` = "id_journal"'
        );
        if (!$result || !Db::getInstance()->numRows()) {
            Db::getInstance()->Execute(
                'ALTER TABLE `' . _DB_PREFIX_ . 'ta_cartreminder_journal_reminder`
                ADD INDEX `state` ( `id_journal`)'
            );
        }
        // INDEX ta_cartreminder_journal_reminder  manual_process
        $result = Db::getInstance()->ExecuteS(
            'SHOW INDEX FROM `' . _DB_PREFIX_ . 'ta_cartreminder_journal_reminder` WHERE `key_name` = "manual_process"'
        );
        if (!$result || !Db::getInstance()->numRows()) {
            Db::getInstance()->Execute(
                'ALTER TABLE `' . _DB_PREFIX_ . 'ta_cartreminder_journal_reminder`
                ADD INDEX `manual_process` ( `manual_process`)'
            );
        }
    }

    return $result_up;
}
