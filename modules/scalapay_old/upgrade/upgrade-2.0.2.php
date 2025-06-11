<?php
/**
 * 2007-2022 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2022 PrestaShop SA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * This function updates your module from previous versions to the version 1.1,
 * usefull when you modify your database, or register a new hook ...
 * Don't forget to create one file per version.
 */
function upgrade_module_2_0_2($module)
{
    foreach (Shop::getShops(true, null, true) as $shop) {
        Configuration::updateValue(Scalapay::SCALAPAY_PAY_IN_3_PRODUCT_PAGE_CUSTOM_CSS, '', false, null, $shop);
        // @phpstan-ignore-next-line
        if (version_compare(_PS_VERSION_, "1.7.0", "<")) {
            Configuration::updateValue(Scalapay::SCALAPAY_PAY_IN_3_CART_PAGE_CUSTOM_CSS, 'display: flex; justify-content: end;', false, null, $shop);
            Configuration::updateValue(Scalapay::SCALAPAY_PAY_IN_3_CHECKOUT_PAGE_CUSTOM_CSS, 'margin-left:150px; margin-bottom:20px;', false, null, $shop);

            Configuration::updateValue(Scalapay::SCALAPAY_PAY_IN_4_PRODUCT_PAGE_CUSTOM_CSS, '', false, null, $shop);
            Configuration::updateValue(Scalapay::SCALAPAY_PAY_IN_4_CART_PAGE_CUSTOM_CSS, 'display: flex; justify-content: end;', false, null, $shop);
            Configuration::updateValue(Scalapay::SCALAPAY_PAY_IN_4_CHECKOUT_PAGE_CUSTOM_CSS, 'margin-left:150px; margin-bottom:20px;', false, null, $shop);

            Configuration::updateValue(Scalapay::SCALAPAY_PAY_LATER_PRODUCT_PAGE_CUSTOM_CSS, 'margin-top:10px;', false, null, $shop);
            Configuration::updateValue(Scalapay::SCALAPAY_PAY_LATER_CART_PAGE_CUSTOM_CSS, 'margin-bottom:10px; margin-top:10px;', false, null, $shop);
            Configuration::updateValue(Scalapay::SCALAPAY_PAY_LATER_CHECKOUT_PAGE_CUSTOM_CSS, 'margin-left:150px; margin-bottom:20px;', false, null, $shop);
        } else {
            Configuration::updateValue(Scalapay::SCALAPAY_PAY_IN_3_CART_PAGE_CUSTOM_CSS, 'margin-left:20px;', false, null, $shop);
            Configuration::updateValue(Scalapay::SCALAPAY_PAY_IN_3_CHECKOUT_PAGE_CUSTOM_CSS, 'margin-bottom:20px;', false, null, $shop);

            Configuration::updateValue(Scalapay::SCALAPAY_PAY_IN_4_PRODUCT_PAGE_CUSTOM_CSS, '', false, null, $shop);
            Configuration::updateValue(Scalapay::SCALAPAY_PAY_IN_4_CART_PAGE_CUSTOM_CSS, 'margin-left:20px;', false, null, $shop);
            Configuration::updateValue(Scalapay::SCALAPAY_PAY_IN_4_CHECKOUT_PAGE_CUSTOM_CSS, 'margin-bottom:20px;', false, null, $shop);

            Configuration::updateValue(Scalapay::SCALAPAY_PAY_LATER_PRODUCT_PAGE_CUSTOM_CSS, '', false, null, $shop);
            Configuration::updateValue(Scalapay::SCALAPAY_PAY_LATER_CART_PAGE_CUSTOM_CSS, 'margin-top:10px;', false, null, $shop);
            Configuration::updateValue(Scalapay::SCALAPAY_PAY_LATER_CHECKOUT_PAGE_CUSTOM_CSS, 'margin-bottom:20px;', false, null, $shop);
        }
    }

    return true;
}
