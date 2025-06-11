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

function upgrade_module_2_1_5($module)
{
    foreach (Shop::getShops(true, null, true) as $shop) {
        // @phpstan-ignore-next-line
        if (version_compare(_PS_VERSION_, '1.7.0', '<')) {
            Configuration::updateValue(Scalapay::SCALAPAY_PAY_IN_3_CHECKOUT_PAGE_CUSTOM_CSS, 'margin: 0 0 0 15px;', false, null, $shop);
            Configuration::updateValue(Scalapay::SCALAPAY_PAY_IN_4_CHECKOUT_PAGE_CUSTOM_CSS, 'margin: 0 0 0 15px;', false, null, $shop);
            Configuration::updateValue(Scalapay::SCALAPAY_PAY_LATER_CHECKOUT_PAGE_CUSTOM_CSS, 'margin: 0 0 0 15px;', false, null, $shop);
        } else {
            Configuration::updateValue(Scalapay::SCALAPAY_PAY_IN_3_CHECKOUT_PAGE_CUSTOM_CSS, 'margin: 0 0 20px 0;', false, null, $shop);
            Configuration::updateValue(Scalapay::SCALAPAY_PAY_IN_4_CHECKOUT_PAGE_CUSTOM_CSS, 'margin: 0 0 20px 0;', false, null, $shop);
            Configuration::updateValue(Scalapay::SCALAPAY_PAY_LATER_CHECKOUT_PAGE_CUSTOM_CSS, 'margin: 0 0 20px 0;', false, null, $shop);
        }
    }

    return true;
}
