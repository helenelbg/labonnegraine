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

function upgrade_module_1_4_0($module)
{
    // Migrate new tables.
    $sql = array();

    $sql[] = "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."aff_affiliates_meta` (
    `id_meta` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_affiliate` int(11) UNSIGNED NOT NULL,
    `key` varchar(128) NOT NULL,
    `value` text NOT NULL,
    PRIMARY KEY (`id_meta`)
    ) ENGINE = '"._MYSQL_ENGINE_."' charset=utf8;";

    $sql[] = "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."aff_custom_fields` (
    `id_field` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `type` enum('text','textarea','link') NOT NULL DEFAULT 'text',
    `required` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
    `active` tinyint(1) UNSIGNED NOT NULL DEFAULT '1',
    PRIMARY KEY (`id_field`)
    ) ENGINE = '"._MYSQL_ENGINE_."' charset=utf8;";

    $sql[] = "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."aff_custom_fields_lang` (
    `id_field` int(11) UNSIGNED NOT NULL,
    `id_lang` int(11) UNSIGNED NOT NULL,
    `name` varchar(128) NOT NULL,
    PRIMARY KEY (`id_field`,`id_lang`)
    ) ENGINE = '"._MYSQL_ENGINE_."' charset=utf8;";

    foreach ($sql as $query) {
        if (Db::getInstance()->execute($query) == false) {
            return false;
        }
    }

    // Alter payments to include invoice file name column
    if (!Db::getInstance()->execute('
        ALTER TABLE `'._DB_PREFIX_.'aff_payments` ADD `invoice` VARCHAR(255) NULL AFTER `notes`
    ')
    ) {
        return false;
    }

    // Create new custom fields admin controller.
    $primaryTabId = (int)Tab::getIdFromClassName('PsaffiliateAdmin');

    if (!$primaryTabId) {
        return false;
    }

    $tab = new Tab();
    $tab->class_name = 'AdminPsaffiliateCustomFields';
    $tab->id_parent = $primaryTabId;
    $tab->module = $module->name;
    $tab->name[(int)(Configuration::get('PS_LANG_DEFAULT'))] = $module->l('Affiliates Custom Fields');

    if (!$tab->add()) {
        return false;
    }

    $affiliatesTabId = (int)Tab::getIdFromClassName('AdminPsaffiliateAffiliates');

    if (!$affiliatesTabId) {
        $tab->delete();

        return false;
    }

    $affiliatesTab = new Tab($affiliatesTabId);

    if (!$tab->updatePosition(true, (int)$affiliatesTab->position)) {
        $tab->delete();

        return false;
    }

    return true;
}
