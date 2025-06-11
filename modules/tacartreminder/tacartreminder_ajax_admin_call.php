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
 **          V 1.0.0                 *
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

if (!file_exists(dirname(__FILE__) . '/../../config/config.inc.php')) {
    if (file_exists(dirname(__FILE__) . '/../prestashop16mb/config/config.inc.php')) {
        include dirname(__FILE__) . '/../prestashop16mb/config/config.inc.php';
    } else {
        include dirname(__FILE__) . '/../prestashop15mb/config/config.inc.php';
    }
} else {
    include dirname(__FILE__) . '/../../config/config.inc.php';
}
include dirname(__FILE__) . '/tacartreminder.php';
if (Tools::substr(Tools::encrypt('tacartreminder/index'), 0, 10) != Tools::getValue('token')
    || !Module::isInstalled('tacartreminder')
) {
    exit('Bad token');
}
$ta_cart_reminder = new TACartReminder();
$ta_cart_reminder->ajaxAdminCall();
