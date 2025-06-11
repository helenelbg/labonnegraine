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
 * Cron use remind all abandonned cart
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

ini_set('memory_limit', '600M');
if (function_exists('set_time_limit')) {
    @set_time_limit(1200);
}
include dirname(__FILE__) . '/../../config/config.inc.php';
include dirname(__FILE__) . '/../../init.php';
/**
 * @Deprecated for future version
 * Backward compatibility with legacy usage.
 */
$smartCartReminder = Module::getInstanceByName('tacartreminder');
$mvcController = Context::getContext()->link->getModuleLink(
    'tacartreminder',
    'cron',
    ['token' => Tools::getValue('token')],
    true
);
Tools::redirect($mvcController);
