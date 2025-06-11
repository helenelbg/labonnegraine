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
function upgrade_module_2_0_0($module)
{
    // update configuration names

    Configuration::deleteByName('SCALAPAY_minOrderValueProductPage');
    Configuration::deleteByName('SCALAPAY_minOrderValueCartPage');
    Configuration::deleteByName('SCALAPAY_PRODUCT_PAGE_PRICE_BOX_SELECTORS');
    Configuration::deleteByName('SCALAPAY_PRODUCT_PAGE_PRICE_BOX_SELECTORS_LATER');
    Configuration::deleteByName('SCALAPAY_PRODUCT_PAGE_PRICE_BOX_SELECTORS_FOUR');
    Configuration::deleteByName('SCALAPAY_CART_PAGE_PRICE_BOX_SELECTORS');
    Configuration::deleteByName('SCALAPAY_CART_PAGE_PRICE_BOX_SELECTORS_LATER');
    Configuration::deleteByName('SCALAPAY_CART_PAGE_PRICE_BOX_SELECTORS_FOUR');

    $configurations = [
        Scalapay::SCALAPAY_CSS_LOGO_TEXT => '',

        Scalapay::SCALAPAY_PAY_IN_3_PRODUCT_PAGE_AMOUNT_SELECTORS => '".current-price-value"',
        Scalapay::SCALAPAY_PAY_IN_3_PRODUCT_PAGE_WIDGET_POSITION => '.product-prices',
        Scalapay::SCALAPAY_PAY_IN_3_CART_PAGE_AMOUNT_SELECTORS => '".cart-summary-line.cart-total .value"',
        Scalapay::SCALAPAY_PAY_IN_3_CART_PAGE_WIDGET_POSITION => '.cart-detailed-totals',
        Scalapay::SCALAPAY_PAY_IN_3_CART_PAGE_LOGO_SIZE => '100%',
        Scalapay::SCALAPAY_PAY_IN_3_PRODUCT_PAGE_LOGO_SIZE => '100%',

        Scalapay::SCALAPAY_PAY_IN_4_MINIMUM_AMOUNT => 5,
        Scalapay::SCALAPAY_PAY_IN_4_MAXIMUM_AMOUNT => 900,
        Scalapay::SCALAPAY_PAY_IN_4_ALLOWED_COUNTRIES => 'IT',
        Scalapay::SCALAPAY_PAY_IN_4_ALLOWED_LANGUAGES => 'it',
        Scalapay::SCALAPAY_PAY_IN_4_ALLOWED_CURRENCIES => 'EUR',
        Scalapay::SCALAPAY_PAY_IN_4_PRODUCT_PAGE_AMOUNT_SELECTORS => '".current-price-value"',
        Scalapay::SCALAPAY_PAY_IN_4_PRODUCT_PAGE_WIDGET_POSITION => '.product-prices',
        Scalapay::SCALAPAY_PAY_IN_4_CART_PAGE_AMOUNT_SELECTORS => '".cart-summary-line.cart-total .value"',
        Scalapay::SCALAPAY_PAY_IN_4_CART_PAGE_WIDGET_POSITION => '.cart-detailed-totals',
        Scalapay::SCALAPAY_PAY_IN_4_CART_PAGE_LOGO_SIZE => '100%',
        Scalapay::SCALAPAY_PAY_IN_4_PRODUCT_PAGE_LOGO_SIZE => '100%',

        Scalapay::SCALAPAY_PAY_LATER_PAY_AFTER_DAYS => 14,
        Scalapay::SCALAPAY_PAY_LATER_MINIMUM_AMOUNT => 5,
        Scalapay::SCALAPAY_PAY_LATER_MAXIMUM_AMOUNT => 900,
        Scalapay::SCALAPAY_PAY_LATER_ALLOWED_COUNTRIES => 'IT',
        Scalapay::SCALAPAY_PAY_LATER_ALLOWED_LANGUAGES => 'it',
        Scalapay::SCALAPAY_PAY_LATER_ALLOWED_CURRENCIES => 'EUR',
        Scalapay::SCALAPAY_PAY_LATER_PRODUCT_PAGE_AMOUNT_SELECTORS => '".current-price-value"',
        Scalapay::SCALAPAY_PAY_LATER_PRODUCT_PAGE_WIDGET_POSITION => 'div.product-add-to-cart',
        Scalapay::SCALAPAY_PAY_LATER_CART_PAGE_AMOUNT_SELECTORS => '".cart-summary-line.cart-total .value"',
        Scalapay::SCALAPAY_PAY_LATER_CART_PAGE_WIDGET_POSITION => '.checkout',
        Scalapay::SCALAPAY_PAY_LATER_CART_PAGE_LOGO_SIZE => '100%',
        Scalapay::SCALAPAY_PAY_LATER_PRODUCT_PAGE_LOGO_SIZE => '100%',
    ];

    foreach (Shop::getShops(true, null, true) as $shop) {
        foreach ($configurations as $cc => $value) {
            if (!Configuration::get($cc, null, null, $shop)) {
                Configuration::updateValue($cc, $value, false, null, $shop);
            }
        }

        if (Configuration::get(Scalapay::SCALAPAY_ADD_WIDGET_SCRIPTS, null, null, $shop, 'na') === 'na') {
            Configuration::updateValue(Scalapay::SCALAPAY_ADD_WIDGET_SCRIPTS, true, false, null, $shop);
        }

        if (Configuration::get(Scalapay::SCALAPAY_PAY_IN_3_PRODUCT_PAGE_AMOUNT_SELECTORS, null, null, $shop) === '"#scalapay_product_price"') {
            Configuration::updateValue(Scalapay::SCALAPAY_PAY_IN_3_PRODUCT_PAGE_AMOUNT_SELECTORS, $configurations[Scalapay::SCALAPAY_PAY_IN_3_PRODUCT_PAGE_AMOUNT_SELECTORS], false, null, $shop);
        }
        if (Configuration::get(Scalapay::SCALAPAY_PAY_IN_3_CART_PAGE_AMOUNT_SELECTORS, null, null, $shop) === '"#scalapay_cart_price"') {
            Configuration::updateValue(Scalapay::SCALAPAY_PAY_IN_3_CART_PAGE_AMOUNT_SELECTORS, $configurations[Scalapay::SCALAPAY_PAY_IN_3_CART_PAGE_AMOUNT_SELECTORS], false, null, $shop);
        }

        if (Configuration::get(Scalapay::SCALAPAY_PAY_IN_4_PRODUCT_PAGE_AMOUNT_SELECTORS, null, null, $shop) === '"#scalapay_product_price"') {
            Configuration::updateValue(Scalapay::SCALAPAY_PAY_IN_4_PRODUCT_PAGE_AMOUNT_SELECTORS, $configurations[Scalapay::SCALAPAY_PAY_IN_4_PRODUCT_PAGE_AMOUNT_SELECTORS], false, null, $shop);
        }
        if (Configuration::get(Scalapay::SCALAPAY_PAY_IN_4_CART_PAGE_AMOUNT_SELECTORS, null, null, $shop) === '"#scalapay_cart_price"') {
            Configuration::updateValue(Scalapay::SCALAPAY_PAY_IN_4_CART_PAGE_AMOUNT_SELECTORS, $configurations[Scalapay::SCALAPAY_PAY_IN_4_CART_PAGE_AMOUNT_SELECTORS], false, null, $shop);
        }

        if (Configuration::get(Scalapay::SCALAPAY_PAY_LATER_PRODUCT_PAGE_AMOUNT_SELECTORS, null, null, $shop) === '"#scalapay_product_price"') {
            Configuration::updateValue(Scalapay::SCALAPAY_PAY_LATER_PRODUCT_PAGE_AMOUNT_SELECTORS, $configurations[Scalapay::SCALAPAY_PAY_LATER_PRODUCT_PAGE_AMOUNT_SELECTORS], false, null, $shop);
        }
        if (Configuration::get(Scalapay::SCALAPAY_PAY_LATER_CART_PAGE_AMOUNT_SELECTORS, null, null, $shop) == '"#scalapay_cart_price"') {
            Configuration::updateValue(Scalapay::SCALAPAY_PAY_LATER_CART_PAGE_AMOUNT_SELECTORS, $configurations[Scalapay::SCALAPAY_PAY_LATER_CART_PAGE_AMOUNT_SELECTORS], false, null, $shop);
        }

        if (Configuration::get(Scalapay::SCALAPAY_CSS_LOGO_TEXT, null, null, $shop) == '#scalapayPopUpTrigger{padding-top:5px;}#scalapayPopUpTriggerMain{padding-left:10px;padding-bottom:5px;}#scalapaywidgetpayfourcart{padding-left:10px;padding-bottom:5px;}') {
            Configuration::updateValue(Scalapay::SCALAPAY_CSS_LOGO_TEXT, $configurations[Scalapay::SCALAPAY_CSS_LOGO_TEXT], false, null, $shop);
        }

        if (Configuration::get('PS_CHECKOUT_STATE_SCALAPAY_WAITING_CAPTURE', null, null, $shop, 'na') === 'na') {
            foreach (OrderStateCore::getOrderStates(1) as $orderState) {
                if ($orderState['module'] == $module->name) {
                    Configuration::updateValue('PS_CHECKOUT_STATE_SCALAPAY_WAITING_CAPTURE', $orderState['id_order_state'], false, null, $shop);
                }
            }
        }
    }

    $module->unregisterHook('displayAdminOrder');
    $module->unregisterHook('displayProductAdditionalInfo');
    $module->unregisterHook('displayProductPriceBlock');
    $module->unregisterHook('displayReassurance');
    $module->unregisterHook('paymentReturn');
    $module->unregisterHook('displayTop');

    $module->registerHook('displayCheckoutSummaryTop');
    $module->registerHook('displayShoppingCart');

    Db::getInstance()->Execute(sprintf('RENAME TABLE %sscalapay_admin TO %s%s;', _DB_PREFIX_, _DB_PREFIX_, Scalapay::SCALAPAY_DB));

    Db::getInstance()->Execute(sprintf('ALTER TABLE %s%s ADD COLUMN refund_amount DECIMAL(20, 2);', _DB_PREFIX_, Scalapay::SCALAPAY_DB));
    Db::getInstance()->Execute(sprintf('ALTER TABLE %s%s ADD COLUMN product VARCHAR(15) default "-";', _DB_PREFIX_, Scalapay::SCALAPAY_DB));
    Db::getInstance()->Execute(sprintf('ALTER TABLE %s%s MODIFY COLUMN captured INT(1) default 0;', _DB_PREFIX_, Scalapay::SCALAPAY_DB));

    return true;
}
