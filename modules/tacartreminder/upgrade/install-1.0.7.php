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

function upgrade_module_1_0_7($object, $install = false)
{
    if ($object->active || $install) {
        $result = true;
        // table creation ta_cartreminder_rule_match_cache
        if (!Configuration::updateValue('TA_CARTR_DEBUG', 0)) {
            $result &= false;
        }
        if (!Db::getInstance()->Execute('CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ta_cartreminder_rule_match_cache` (
            `id_rule_match_cache` int(11) unsigned NOT NULL auto_increment,
            `id_cart` int(11) unsigned,
            `return_jc` tinyint(1) NOT NULL DEFAULT \'0\',
            `date_check` datetime NOT NULL,
            `result` longtext,
            `date_add` datetime NOT NULL,
            `date_upd` datetime NOT NULL,
            PRIMARY KEY (`id_rule_match_cache`),
            KEY `id_cart` (`id_cart`)
            ) ENGINE=' . _MYSQL_ENGINE_ . '  DEFAULT CHARSET=utf8;')) {
            $result &= false;
        }

        $result_ind = Db::getInstance()->ExecuteS(
            'SHOW INDEX FROM `' . _DB_PREFIX_ . 'ta_cartreminder_rule_reminder` WHERE `key_name` = "id_rule"'
        );
        if (!$result_ind || !Db::getInstance()->numRows()) {
            Db::getInstance()->Execute(
                'ALTER TABLE `' . _DB_PREFIX_ . 'ta_cartreminder_rule_reminder`
                ADD INDEX `id_rule` ( `id_rule`)'
            );
        }
    }

    return $result;
}
