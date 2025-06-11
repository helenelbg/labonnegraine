<?php
/**
 * Scalapay
 *
 * @author Scalapay Plugin Integration Team
 * @copyright 2022 Scalapay
 * @license LICENCE.md for license details.
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * This function updates your module from previous versions to the version 1.1,
 * usefull when you modify your database, or register a new hook ...
 * Don't forget to create one file per version.
 */
function upgrade_module_2_0_1($module)
{
    foreach (Shop::getShops(true, null, true) as $shop) {
        // @phpstan-ignore-next-line
        if (version_compare(_PS_VERSION_, '1.7.0', '<')) {
            Configuration::updateValue(Scalapay::SCALAPAY_PAY_IN_3_CHECKOUT_PAGE_AMOUNT_SELECTORS, '"#total_price"', false, null, $shop);
            Configuration::updateValue(Scalapay::SCALAPAY_PAY_IN_4_CHECKOUT_PAGE_AMOUNT_SELECTORS, '"#total_price"', false, null, $shop);
            Configuration::updateValue(Scalapay::SCALAPAY_PAY_LATER_CHECKOUT_PAGE_AMOUNT_SELECTORS, '"#total_price"', false, null, $shop);
        } else {
            Configuration::updateValue(Scalapay::SCALAPAY_PAY_IN_3_CHECKOUT_PAGE_AMOUNT_SELECTORS, '".cart-summary-line.cart-total .value"', false, null, $shop);
            Configuration::updateValue(Scalapay::SCALAPAY_PAY_IN_4_CHECKOUT_PAGE_AMOUNT_SELECTORS, '".cart-summary-line.cart-total .value"', false, null, $shop);
            Configuration::updateValue(Scalapay::SCALAPAY_PAY_LATER_CHECKOUT_PAGE_AMOUNT_SELECTORS, '".cart-summary-line.cart-total .value"', false, null, $shop);
        }

        Configuration::updateValue(Scalapay::SCALAPAY_PAY_IN_3_CHECKOUT_PAGE_SHOW_TITLE, true, false, null, $shop);
        Configuration::updateValue(Scalapay::SCALAPAY_PAY_IN_3_CHECKOUT_PAGE_HIDE_PRICE, false, false, null, $shop);
        Configuration::updateValue(Scalapay::SCALAPAY_PAY_IN_3_CHECKOUT_PAGE_CURRENCY_POSITION, 'after', false, null, $shop);
        Configuration::updateValue(Scalapay::SCALAPAY_PAY_IN_3_CHECKOUT_PAGE_CURRENCY_TYPE, 'symbol', false, null, $shop);

        Configuration::updateValue(Scalapay::SCALAPAY_PAY_IN_4_CHECKOUT_PAGE_SHOW_TITLE, true, false, null, $shop);
        Configuration::updateValue(Scalapay::SCALAPAY_PAY_IN_4_CHECKOUT_PAGE_HIDE_PRICE, false, false, null, $shop);
        Configuration::updateValue(Scalapay::SCALAPAY_PAY_IN_4_CHECKOUT_PAGE_CURRENCY_POSITION, 'after', false, null, $shop);
        Configuration::updateValue(Scalapay::SCALAPAY_PAY_IN_4_CHECKOUT_PAGE_CURRENCY_TYPE, 'symbol', false, null, $shop);

        Configuration::updateValue(Scalapay::SCALAPAY_PAY_LATER_CHECKOUT_PAGE_SHOW_TITLE, true, false, null, $shop);

        Configuration::updateValue(Scalapay::SCALAPAY_HOOK_WIDGET, 'displayHeader', false, null, $shop);
    }

    return true;
}
