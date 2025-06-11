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

function upgrade_module_1_0_10($object, $install = false)
{
    if ($object->active || $install) {
        $result = true;
        $result &= (bool) Db::getInstance()->Execute(
            'ALTER TABLE `' . _DB_PREFIX_ . 'ta_cartreminder_journal`
            ADD COLUMN `email` VARCHAR(128) NULL AFTER `id_customer`'
        );
        $result &= (bool) Db::getInstance()->Execute(
            'ALTER TABLE `' . _DB_PREFIX_ . 'ta_cartreminder_journal`
            ADD INDEX `email` ( `email`)'
        );
        $result &= (bool) Db::getInstance()->Execute(
            '
		    UPDATE `' . _DB_PREFIX_ . 'ta_cartreminder_journal` j
            JOIN `' . _DB_PREFIX_ . 'customer` c ON j.`id_customer` = c.`id_customer`
            SET j.`email` = c.`email`'
        );
    }

    return $result;
}
