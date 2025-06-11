<?php
/**
* 2022 - Keyrnel
*
* NOTICE OF LICENSE
*
* The source code of this module is under a commercial license.
* Each license is unique and can be installed and used on only one shop.
* Any reproduction or representation total or partial of the module, one or more of its components,
* by any means whatsoever, without express permission from us is prohibited.
* If you have not received this module from us, thank you for contacting us.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future.
*
* @author    Keyrnel
* @copyright 2022 - Keyrnel
* @license   commercial
* International Registered Trademark & Property of Keyrnel
*/
$sql = [];

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'flash_sale`
	(
	`id_flash_sale` int(10) unsigned auto_increment NOT NULL,
	`id_customer` int(10) unsigned NOT NULL,
	`reduction` decimal(20, 6) NOT NULL,
	`reduction_tax` tinyint(1) unsigned DEFAULT 1,
	`reduction_type` enum("amount", "percentage") NOT NULL,
	`from` datetime NOT NULL,
	`to` datetime NOT NULL,
	`depends_on_stock` tinyint(1) NOT NULL DEFAULT 0,
	`display_home` tinyint(1) NOT NULL,
	`display_home_tab` tinyint(1) NOT NULL,
	`display_page` tinyint(1) NOT NULL,
	`display_column` tinyint(1) NOT NULL,
	`active` tinyint(1) NOT NULL,
    `cache` tinyint(1) NOT NULL DEFAULT 0,
	PRIMARY KEY  (`id_flash_sale`)
	) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'flash_sale_lang`
	(
	`id_flash_sale` int(10) unsigned NOT NULL,
	`id_lang` int(10) unsigned NOT NULL,
	`name` varchar(128) NOT NULL,
	`description` text DEFAULT NULL,
	PRIMARY KEY  (`id_flash_sale`, `id_lang`)
	) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'flash_sale_shops`
	(
	`id_flash_sale` int(10) unsigned NOT NULL,
	`id_shop` int(10) unsigned NOT NULL,
	PRIMARY KEY  (`id_flash_sale`, `id_shop`)
	) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'flash_sale_countries`
	(
	`id_flash_sale` int(10) unsigned NOT NULL,
	`id_country` int(10) unsigned NOT NULL,
	PRIMARY KEY  (`id_flash_sale`, `id_country`)
	) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'flash_sale_currencies`
	(
	`id_flash_sale` int(10) unsigned NOT NULL,
	`id_currency` int(10) unsigned NOT NULL,
	PRIMARY KEY  (`id_flash_sale`, `id_currency`)
	) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'flash_sale_groups`
	(
	`id_flash_sale` int(10) unsigned NOT NULL,
	`id_group` int(10) unsigned NOT NULL,
	PRIMARY KEY  (`id_flash_sale`, `id_group`)
	) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'flash_sale_products`
	(
	`id_flash_sale_products` int(10) unsigned auto_increment NOT NULL,
	`id_flash_sale` int(10) unsigned NOT NULL,
	`id_manufacturer` int(10) unsigned NOT NULL DEFAULT 0,
	`id_category` int(10) unsigned NOT NULL DEFAULT 0,
	`id_product` int(10) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY  (`id_flash_sale_products`)
	) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'flash_sale_specific_prices`
	(
	`id_flash_sale_specific_prices` int(10) unsigned auto_increment NOT NULL,
	`id_flash_sale` int(10) unsigned NOT NULL,
	`id_specific_price` int(10) unsigned NOT NULL,
	PRIMARY KEY  (`id_flash_sale_specific_prices`)
	) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'flash_sale_custom_specific_prices`
	(
	`id_flash_sale_custom_specific_prices` int(10) unsigned auto_increment NOT NULL,
	`id_flash_sale` int(10) unsigned NOT NULL,
	`id_product` int(10) unsigned NOT NULL,
	`id_product_attribute` int(10) unsigned NOT NULL,
	`reduction` decimal(20, 6) NOT NULL,
	`reduction_type` enum("amount", "percentage") NOT NULL,
    `from` datetime NOT NULL,
	`to` datetime NOT NULL,
    `custom_reduction` tinyint(1) NOT NULL,
	PRIMARY KEY  (`id_flash_sale_custom_specific_prices`)
	) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'flash_sale_custom_specific_prices_group`
	(
	`id_flash_sale_custom_specific_prices_group` int(10) unsigned auto_increment NOT NULL,
	`id_flash_sale` int(10) unsigned NOT NULL,
	`item` enum("category", "manufacturer") NOT NULL,
	`id_item` int(10) unsigned NOT NULL,
	`reduction` decimal(20, 6) NOT NULL,
	`reduction_type` enum("amount", "percentage") NOT NULL,
    `from` datetime NOT NULL,
	`to` datetime NOT NULL,
	PRIMARY KEY  (`id_flash_sale_custom_specific_prices_group`)
	) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';
