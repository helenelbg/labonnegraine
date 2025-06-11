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
    include dirname(__FILE__) . '/../prestashop16mb/config/config.inc.php';
    include dirname(__FILE__) . '/../prestashop16mb/init.php';
} else {
    include dirname(__FILE__) . '/../../config/config.inc.php';
    include dirname(__FILE__) . '/../../init.php';
}
require_once dirname(__FILE__) . '/models/TACartReminderJournal.php';
/* For security uid is need */
$uid_trc = (string) Tools::getValue('uidtrc');
$id_reminder = (int) Tools::getValue('id_reminder');
if (isset($uid_trc) && !empty($uid_trc) && (int) $id_reminder) {
    // $uid_trc is already unscaped in function(pSQL)
    TACartReminderJournal::markReminderIsOpen($uid_trc, $id_reminder);
}
// image hack
$hex = '47494638396101000100800000ffffff00000021f90401000000002c00000000010001000002024401003b';
$img = '';
$t = Tools::strlen($hex) / 2;
for ($i = 0; $i < $t; ++$i) {
    $img .= chr(hexdec(Tools::substr($hex, $i * 2, 2)));
}
header('Last-Modified: Fri, 01 Jan 1999 00:00 GMT', true, 200);
header('Content-Length: ' . Tools::strlen($img));
header('Content-Type: image/gif');
echo $img;
