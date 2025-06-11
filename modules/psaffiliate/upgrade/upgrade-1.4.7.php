<?php
/**
 * NOTICE OF LICENSE
 *
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * You must not modify, adapt or create derivative works of this source code.
 *
 * @author    Active Design <office@activedesign.ro>
 * @copyright 2016-2018 Active Design
 * @license   LICENSE.txt
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_4_7($module)
{
    $res = Db::getInstance()->execute('
        CREATE TABLE IF NOT EXISTS`'._DB_PREFIX_.'aff_configuration_lang` (
          `name` varchar(255) NOT NULL,
          `id_lang` int(11) NOT NULL,
          `value` text NOT NULL,
        PRIMARY KEY (`name`,`id_lang`)
        ) ENGINE='._MYSQL_ENGINE_.';
    ');

    $langs = Language::getLanguages();
    $invoice_detail = Db::getInstance()->getValue('SELECT `value` FROM `'._DB_PREFIX_.'aff_configuration` WHERE `name` = "invoicing_details"');
    $textarea_at_registration_label = Db::getInstance()->getValue('SELECT `value` FROM `'._DB_PREFIX_.'aff_configuration` WHERE `name` = "textarea_at_registration_label"');
    foreach ($langs as $lang) {
        $res &= Db::getInstance()->insert('aff_configuration_lang', array(
            'name' => 'invoicing_details',
            'id_lang' => (int)$lang['id_lang'],
            'value' => $invoice_detail,
        ));
        $res &= Db::getInstance()->insert('aff_configuration_lang', array(
            'name' => 'textarea_at_registration_label',
            'id_lang' => (int)$lang['id_lang'],
            'value' => $textarea_at_registration_label,
        ));
    }
    $res &= Db::getInstance()->delete(
        'aff_configuration',
        'name = "textarea_at_registration_label" OR name = "invoicing_details"'
    );

    return (bool)$res;
}
