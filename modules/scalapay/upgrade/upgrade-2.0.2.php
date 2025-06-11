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
function upgrade_module_2_0_2($module)
{
    foreach (Shop::getShops(true, null, true) as $shop) {
        Configuration::updateValue(Scalapay::SCALAPAY_PAY_IN_3_PRODUCT_PAGE_CUSTOM_CSS, '', false, null, $shop);
        // @phpstan-ignore-next-line
        if (version_compare(_PS_VERSION_, '1.7.0', '<')) {
            Configuration::updateValue(Scalapay::SCALAPAY_PAY_IN_3_CART_PAGE_CUSTOM_CSS, 'display: flex; justify-content: end;', false, null, $shop);
            Configuration::updateValue(Scalapay::SCALAPAY_PAY_IN_3_CHECKOUT_PAGE_CUSTOM_CSS, 'margin: 0 0 0 15px;', false, null, $shop);

            Configuration::updateValue(Scalapay::SCALAPAY_PAY_IN_4_PRODUCT_PAGE_CUSTOM_CSS, '', false, null, $shop);
            Configuration::updateValue(Scalapay::SCALAPAY_PAY_IN_4_CART_PAGE_CUSTOM_CSS, 'display: flex; justify-content: end;', false, null, $shop);
            Configuration::updateValue(Scalapay::SCALAPAY_PAY_IN_4_CHECKOUT_PAGE_CUSTOM_CSS, 'margin: 0 0 0 15px;', false, null, $shop);

            Configuration::updateValue(Scalapay::SCALAPAY_PAY_LATER_PRODUCT_PAGE_CUSTOM_CSS, 'margin-top:10px;', false, null, $shop);
            Configuration::updateValue(Scalapay::SCALAPAY_PAY_LATER_CART_PAGE_CUSTOM_CSS, 'margin-bottom:10px; margin-top:10px;', false, null, $shop);
            Configuration::updateValue(Scalapay::SCALAPAY_PAY_LATER_CHECKOUT_PAGE_CUSTOM_CSS, 'margin: 0 0 0 15px;', false, null, $shop);
        } else {
            Configuration::updateValue(Scalapay::SCALAPAY_PAY_IN_3_CART_PAGE_CUSTOM_CSS, 'margin-left:20px;', false, null, $shop);
            Configuration::updateValue(Scalapay::SCALAPAY_PAY_IN_3_CHECKOUT_PAGE_CUSTOM_CSS, 'margin: 0 0 20px 0;', false, null, $shop);

            Configuration::updateValue(Scalapay::SCALAPAY_PAY_IN_4_PRODUCT_PAGE_CUSTOM_CSS, '', false, null, $shop);
            Configuration::updateValue(Scalapay::SCALAPAY_PAY_IN_4_CART_PAGE_CUSTOM_CSS, 'margin-left:20px;', false, null, $shop);
            Configuration::updateValue(Scalapay::SCALAPAY_PAY_IN_4_CHECKOUT_PAGE_CUSTOM_CSS, 'margin: 0 0 20px 0;', false, null, $shop);

            Configuration::updateValue(Scalapay::SCALAPAY_PAY_LATER_PRODUCT_PAGE_CUSTOM_CSS, '', false, null, $shop);
            Configuration::updateValue(Scalapay::SCALAPAY_PAY_LATER_CART_PAGE_CUSTOM_CSS, 'margin-top:10px;', false, null, $shop);
            Configuration::updateValue(Scalapay::SCALAPAY_PAY_LATER_CHECKOUT_PAGE_CUSTOM_CSS, 'margin: 0 0 20px 0;', false, null, $shop);
        }
    }

    return true;
}
