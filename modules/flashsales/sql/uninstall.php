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
$sql[] = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'flash_sale`';
$sql[] = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'flash_sale_lang`';
$sql[] = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'flash_sale_shops`';
$sql[] = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'flash_sale_countries`';
$sql[] = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'flash_sale_currencies`';
$sql[] = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'flash_sale_groups`';
$sql[] = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'flash_sale_products`';
$sql[] = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'flash_sale_specific_prices`';
$sql[] = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'flash_sale_custom_specific_prices`';
$sql[] = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'flash_sale_custom_specific_prices_group`';
