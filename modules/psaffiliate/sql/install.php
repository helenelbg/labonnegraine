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

$sql = array();

$sql[] = "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."aff_affiliates` (
`id_affiliate` int(10) NOT NULL AUTO_INCREMENT,
`id_customer` int(10) DEFAULT NULL,
`email` varchar(40) DEFAULT NULL,
`password` varchar(64) DEFAULT NULL,
`firstname` varchar(40) DEFAULT NULL,
`lastname` varchar(40) DEFAULT NULL,
`active` int(1) NOT NULL DEFAULT '0',
`date_created` datetime DEFAULT NULL,
`date_lastseen` datetime DEFAULT NULL,
`website` varchar(255) DEFAULT NULL,
`textarea_registration` text,
`textarea_registration_label` varchar(255) DEFAULT NULL,
`has_been_reviewed` int(11) NOT NULL DEFAULT '1',
PRIMARY KEY (`id_affiliate`)
) ENGINE = '"._MYSQL_ENGINE_."' charset=utf8;";

$sql[] = "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."aff_affiliates_meta` (
`id_meta` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`id_affiliate` int(11) UNSIGNED NOT NULL,
`key` varchar(128) NOT NULL,
`value` text NOT NULL,
PRIMARY KEY (`id_meta`)
) ENGINE = '"._MYSQL_ENGINE_."' charset=utf8;";

$sql[] = "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."aff_banners` (
`id_banner` int(10) NOT NULL AUTO_INCREMENT,
`title` varchar(255) NOT NULL,
`image` text NOT NULL,
`active` int(1) DEFAULT NULL,
PRIMARY KEY (`id_banner`)
) ENGINE = '"._MYSQL_ENGINE_."' charset=utf8;";
$sql[] = "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."aff_campaigns` (
`id_campaign` int(11) NOT NULL AUTO_INCREMENT,
`id_affiliate` int(11) NOT NULL,
`name` varchar(255) NOT NULL,
`description` text NOT NULL,
`date_created` datetime NOT NULL,
`date_lastactive` datetime NOT NULL,
PRIMARY KEY (`id_campaign`)
) ENGINE = '"._MYSQL_ENGINE_."' charset=utf8;";
$sql[] = "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."aff_commission` (
`id_commission` int(11) NOT NULL AUTO_INCREMENT,
`id_affiliate` int(11) NOT NULL,
`date` datetime NOT NULL,
`type` varchar(32) NOT NULL,
`value` float NOT NULL,
PRIMARY KEY (`id_commission`)
) ENGINE = '"._MYSQL_ENGINE_."';";
$hasConfigurationTable = Db::getInstance()->executeS("SHOW TABLES LIKE '"._DB_PREFIX_."aff_configuration'");
if (!sizeof($hasConfigurationTable)) {
    $sql[] = "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."aff_configuration` (
    `name` varchar(64) NOT NULL,
    `value` varchar(255) DEFAULT NULL,
    PRIMARY KEY (`name`)
    ) ENGINE = '"._MYSQL_ENGINE_."' charset=utf8;";
    $sql[] = "INSERT INTO `"._DB_PREFIX_."aff_configuration` (`name`, `value`) VALUES
    ('affiliates_require_approval', '1'),
    ('ask_for_website', '1'),
    ('days_current_summary', '30'),
    ('days_remember_affiliate', '3'),
    ('include_cart_rules', '1'),
    ('include_shipping_tax', '0'),
    ('include_tax_rules', '1'),
    ('cat_prod_commission_bonus', '0'),
    ('general_rate_value_per_product', '0'),
    ('minimum_payment_amount', '100'),
    ('new_customers_affiliates_directly', '0'),
    ('order_states_approve', '[\"5\",\"4\"]'),
    ('order_states_cancel', '[\"6\"]'),
    ('textarea_at_registration', '1'),
    ('textarea_at_registration_required', '1'),
    ('enable_terms_at_signup', '1'),
    ('enable_voucher_payments', '0'),
    ('vouchers_for_affiliates_only', '0'),
    ('vouchers_partial_use', '1'),
    ('vouchers_exchange_rate', '1'),
    ('vouchers_always_approved', '0'),
    ('override_previous_affiliate', '0'),
    ('affiliate_id_parameter', 'aff'),
    ('affiliate_link_type', '0'),
    ('affiliate_year_prefix_parameter', 'y'),
    ('enable_invoices', '0'),
    ('first_order_multiplier', '1'),
    ('commissions_for_life', '0'),
    ('override_commissions_for_life', '0'),
    ('commission_for_life_multiplier', '1'),
    ('groups_allowed', ''),
    ('commissions_for_life_at_registration', '0'),
    ('multiply_with_category', '0');";
}

$hasConfigurationLangTable = Db::getInstance()->executeS("SHOW TABLES LIKE '"._DB_PREFIX_."aff_configuration_lang'");
if (!sizeof($hasConfigurationLangTable)) {
    $sql[] = "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."aff_configuration_lang` (
          `name` varchar(255) NOT NULL,
          `id_lang` int(11) NOT NULL,
          `value` text NOT NULL,
        PRIMARY KEY (`name`,`id_lang`)
        ) ENGINE="._MYSQL_ENGINE_." charset=utf8;";
}

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

$sql[] = "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."aff_category_rates` (
`id_category` int(11) NOT NULL,
`rate_percent` FLOAT NOT NULL,
`rate_value` FLOAT NOT NULL,
`multiplier` FLOAT NOT NULL DEFAULT 1,
PRIMARY KEY (`id_category`)
) ENGINE = '"._MYSQL_ENGINE_."';";
$sql[] = "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."aff_product_rates` (
`id_product` int(11) NOT NULL,
`rate_percent` FLOAT NOT NULL,
`rate_value` FLOAT NOT NULL,
`multiplier` FLOAT NOT NULL DEFAULT 1,
PRIMARY KEY (`id_product`)
) ENGINE = '"._MYSQL_ENGINE_."';";
$sql[] = "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."aff_payments` (
`id_payment` int(10) NOT NULL AUTO_INCREMENT,
`date` datetime NOT NULL,
`id_affiliate` int(10) NOT NULL,
`id_voucher` int(10) NOT NULL,
`approved` int(1) NOT NULL,
`amount` float NOT NULL,
`paid` int(1) NOT NULL,
`payment_method` int(10) NOT NULL,
`payment_details` text,
`notes` text NOT NULL,
`invoice` varchar(255) DEFAULT NULL,
PRIMARY KEY (`id_payment`)
) ENGINE = '"._MYSQL_ENGINE_."' charset=utf8;";
$sql[] = "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."aff_payment_methods` (
`id_payment_method` int(10) NOT NULL AUTO_INCREMENT,
`name` varchar(255) NOT NULL,
`description` varchar(255) DEFAULT NULL,
PRIMARY KEY (`id_payment_method`)
) ENGINE = '"._MYSQL_ENGINE_."' charset=utf8;";
$sql[] = "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."aff_payment_methods_fields` (
`id_payment_method_field` int(10) NOT NULL AUTO_INCREMENT,
`id_payment_method` int(10) NOT NULL,
`field_name` varchar(255) NOT NULL,
PRIMARY KEY (`id_payment_method_field`)
) ENGINE = '"._MYSQL_ENGINE_."' charset=utf8;";
$sql[] = "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."aff_sales` (
`id_sale` int(11) NOT NULL AUTO_INCREMENT,
`id_order` int(10) NOT NULL,
`id_affiliate` int(10) NOT NULL,
`id_campaign` int(11) NOT NULL DEFAULT '0',
`approved` int(1) NOT NULL,
`commission` float NOT NULL,
`date` datetime DEFAULT NULL,
PRIMARY KEY (`id_sale`)) ENGINE = '"._MYSQL_ENGINE_."' charset=utf8;";
$sql[] = "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."aff_texts` (
`id_text` int(10) NOT NULL AUTO_INCREMENT,
`title` varchar(255) NOT NULL,
`text` text NOT NULL,
`active` int(1) DEFAULT NULL,
PRIMARY KEY (`id_text`)
) ENGINE = '"._MYSQL_ENGINE_."' charset=utf8;";
$sql[] = "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."aff_tracking` (
`id_tracking` int(11) NOT NULL AUTO_INCREMENT,
`id_affiliate` int(11) NOT NULL,
`id_campaign` int(11) NOT NULL DEFAULT '0',
`id_customer` int(11) NOT NULL DEFAULT '0',
`ip` varchar(48) NOT NULL,
`unique_visit` int(1) NOT NULL DEFAULT '0',
`date` datetime NOT NULL,
`referral` varchar(255) DEFAULT NULL,
`url` VARCHAR(255) DEFAULT NULL,
`commission` float DEFAULT NULL,
PRIMARY KEY (`id_tracking`)
) ENGINE = '"._MYSQL_ENGINE_."' charset=utf8;";
$sql[] = "CREATE TABLE IF NOT EXISTS`"._DB_PREFIX_."aff_customers` (
          `id_aff_customer` int(11) NOT NULL AUTO_INCREMENT,
          `id_affiliate` int(11) NOT NULL,
          `id_customer` int(11) NOT NULL,
          `date_add` DATETIME NULL,
        PRIMARY KEY (`id_aff_customer`)
        ) ENGINE="._MYSQL_ENGINE_.";";
$sql[] = "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."aff_cart` (
          `id_cart` INT NOT NULL,
          `id_affiliate` INT NOT NULL,
          `id_campaign` INT NOT NULL,
          `date` DATETIME NOT NULL,
          PRIMARY KEY (`id_cart`)
          ) ENGINE="._MYSQL_ENGINE_.";";
foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}
$langs = Language::getLanguages();
$res = true;
foreach ($langs as $lang) {
    $res &= Db::getInstance()->insert('aff_configuration_lang', array(
        'name' => 'invoicing_details',
        'id_lang' => (int)$lang['id_lang'],
        'value' => 'Company info',
    ));
    $res &= Db::getInstance()->insert('aff_configuration_lang', array(
        'name' => 'textarea_at_registration_label',
        'id_lang' => (int)$lang['id_lang'],
        'value' => 'Your website url',
    ));
}
if (!$res) {
    return $res;
}
