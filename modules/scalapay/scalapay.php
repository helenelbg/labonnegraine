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

require_once dirname(__FILE__) . '/vendor/autoload.php';

class Scalapay extends PaymentModule
{
    use Scalapay\Traits\ScalapayRequest;

    const SCALAPAY_SUBMIT_FORM = 'submitScalapayModule';
    const ERROR_MESSAGE_KEY = 'failed_payment_message';

    const SCALAPAY_DB = 'scalapay';

    const PRODUCT_PAY_IN_3 = 'pay-in-3';
    const PRODUCT_PAY_IN_4 = 'pay-in-4';
    const PRODUCT_PAY_LATER = 'later';

    const SCALAPAY_LIVE_KEY = 'SCALAPAY_LIVE_KEY';
    const SCALAPAY_TEST_KEY = 'SCALAPAY_PASSWORD';
    const SCALAPAY_TEST_URL = 'SCALAPAY_TEST_URL';
    const SCALAPAY_LIVE_URL = 'SCALAPAY_PRODUCTION_URL';
    const SCALAPAY_CSS_LOGO_TEXT = 'SCALAPAY_CSS_LOGO_TEXT';
    const SCALAPAY_ADD_WIDGET_SCRIPTS = 'SCALAPAY_enable_footer_hook';
    const SCALAPAY_ENABLE_VIRTUAL_PRODUCTS = 'SCALAPAY_enable_virtual_products';
    const SCALAPAY_PS_WAITING_CAPTURE_STATUS_ID = 'PS_CHECKOUT_STATE_SCALAPAY_WAITING_CAPTURE';
    const SCALAPAY_HOOK_WIDGET = 'SCALAPAY_HOOK_WIDGET';

    const IN_PAGE_CHECKOUT_CDN_JS = 'https://cdn.scalapay.com/in-page-checkout/popup.min.js';
    const IN_PAGE_CHECKOUT_CDN_HTML = 'https://cdn.scalapay.com/in-page-checkout/popup.html';
    const IN_PAGE_CHECKOUT_PAYMENT_SELECTORS = 'input[type=radio][data-module-name=scalapay]';

    const SCALAPAY_PAY_IN_3_ENABLED = 'SCALAPAY_ENABLE';
    const SCALAPAY_PAY_IN_3_LIVE_MODE_ENABLED = 'SCALAPAY_LIVE_MODE';
    const SCALAPAY_PAY_IN_3_ORDER_STATUS = 'SCALAPAY_ORDER_STATUS';
    const SCALAPAY_PAY_IN_3_MINIMUM_AMOUNT = 'SCALAPAY_MINIMUM_AMOUNT';
    const SCALAPAY_PAY_IN_3_MAXIMUM_AMOUNT = 'SCALAPAY_MAXIMUM_AMOUNT';
    const SCALAPAY_PAY_IN_3_ALLOWED_COUNTRIES = 'SCALAPAY_ALLOWED_COUNTRIES';
    const SCALAPAY_PAY_IN_3_ALLOWED_LANGUAGES = 'SCALAPAY_ALLOWED_LANGUAGES';
    const SCALAPAY_PAY_IN_3_ALLOWED_CURRENCIES = 'SCALAPAY_ALLOWED_CURRENCIES';
    const SCALAPAY_PAY_IN_3_RESTRICTED_CATEGORIES = 'scala_cat_restrictions';
    const SCALAPAY_PAY_IN_3_PRODUCT_PAGE_AMOUNT_SELECTORS = 'SCALAPAY_PRODUCT_PAGE_SELECTORS';
    const SCALAPAY_PAY_IN_3_PRODUCT_PAGE_LOGO_SIZE = 'SCALAPAY_logoSizeProductPage';
    const SCALAPAY_PAY_IN_3_PRODUCT_PAGE_HIDE_WIDGET = 'SCALAPAY_showLogoProductPage';
    const SCALAPAY_PAY_IN_3_PRODUCT_PAGE_WIDGET_POSITION = 'SCALAPAY_PRODUCT_TEXT_POSITIONS';
    const SCALAPAY_PAY_IN_3_PRODUCT_PAGE_HIDE_PRICE = 'SCALAPAY_hidePriceProductPage';
    const SCALAPAY_PAY_IN_3_PRODUCT_PAGE_AFTER_WIDGET_TEXT_ENABLED = 'SCALAPAY_enable_below_widget_text';
    const SCALAPAY_PAY_IN_3_PRODUCT_PAGE_AFTER_WIDGET_TEXT = 'SCALAPAY_below_widget_text';
    const SCALAPAY_PAY_IN_3_PRODUCT_PAGE_CURRENCY_POSITION = 'scalapay_currency_position';
    const SCALAPAY_PAY_IN_3_PRODUCT_PAGE_CURRENCY_TYPE = 'scalapay_currency_display';
    const SCALAPAY_PAY_IN_3_PRODUCT_PAGE_CUSTOM_CSS = 'SCALAPAY_PAY_IN_3_PRODUCT_PAGE_CUSTOM_CSS';
    const SCALAPAY_PAY_IN_3_CART_PAGE_AMOUNT_SELECTORS = 'SCALAPAY_CART_PAGE_SELECTORS';
    const SCALAPAY_PAY_IN_3_CART_PAGE_LOGO_SIZE = 'SCALAPAY_logoSizeCartPage';
    const SCALAPAY_PAY_IN_3_CART_PAGE_HIDE_WIDGET = 'SCALAPAY_showLogoCartPage';
    const SCALAPAY_PAY_IN_3_CART_PAGE_WIDGET_POSITION = 'SCALAPAY_CART_TEXT_POSITIONS';
    const SCALAPAY_PAY_IN_3_CART_PAGE_HIDE_PRICE = 'SCALAPAY_hidePriceCartPage';
    const SCALAPAY_PAY_IN_3_CART_PAGE_CURRENCY_POSITION = 'scalapay_currency_position_cart';
    const SCALAPAY_PAY_IN_3_CART_PAGE_CURRENCY_TYPE = 'scalapay_currency_display_cart';
    const SCALAPAY_PAY_IN_3_CART_PAGE_CUSTOM_CSS = 'SCALAPAY_PAY_IN_3_CART_PAGE_CUSTOM_CSS';
    const SCALAPAY_PAY_IN_3_CHECKOUT_PAGE_AMOUNT_SELECTORS = 'SCALAPAY_PAY_IN_3_CHECKOUT_PAGE_AMOUNT_SELECTORS';
    const SCALAPAY_PAY_IN_3_CHECKOUT_PAGE_SHOW_TITLE = 'SCALAPAY_PAY_IN_3_CHECKOUT_PAGE_SHOW_TITLE';
    const SCALAPAY_PAY_IN_3_CHECKOUT_PAGE_HIDE_PRICE = 'SCALAPAY_PAY_IN_3_CHECKOUT_PAGE_HIDE_PRICE';
    const SCALAPAY_PAY_IN_3_CHECKOUT_PAGE_CURRENCY_POSITION = 'SCALAPAY_PAY_IN_3_CHECKOUT_PAGE_CURRENCY_POSITION';
    const SCALAPAY_PAY_IN_3_CHECKOUT_PAGE_CURRENCY_TYPE = 'SCALAPAY_PAY_IN_3_CHECKOUT_PAGE_CURRENCY_TYPE';
    const SCALAPAY_PAY_IN_3_CHECKOUT_PAGE_CUSTOM_CSS = 'SCALAPAY_PAY_IN_3_CHECKOUT_PAGE_CUSTOM_CSS';

    const SCALAPAY_PAY_IN_4_ENABLED = 'SCALAPAY_ENABLE_PAY_FOUR';
    const SCALAPAY_PAY_IN_4_LIVE_MODE_ENABLED = 'SCALAPAY_LIVE_MODE_FOUR';
    const SCALAPAY_PAY_IN_4_ORDER_STATUS = 'SCALAPAY_ORDER_STATUS_FOUR';
    const SCALAPAY_PAY_IN_4_ALLOWED_COUNTRIES = 'SCALAPAY_ALLOWED_COUNTRIES_FOUR';
    const SCALAPAY_PAY_IN_4_ALLOWED_CURRENCIES = 'SCALAPAY_ALLOWED_CURRENCIES_FOUR';
    const SCALAPAY_PAY_IN_4_ALLOWED_LANGUAGES = 'SCALAPAY_ALLOWED_LANGUAGES_FOUR';
    const SCALAPAY_PAY_IN_4_RESTRICTED_CATEGORIES = 'scala_cat_restrictions_four';
    const SCALAPAY_PAY_IN_4_MINIMUM_AMOUNT = 'SCALAPAY_MINIMUM_AMOUNT_FOUR';
    const SCALAPAY_PAY_IN_4_MAXIMUM_AMOUNT = 'SCALAPAY_MAXIMUM_AMOUNT_FOUR';
    const SCALAPAY_PAY_IN_4_PRODUCT_PAGE_AFTER_WIDGET_TEXT_ENABLED = 'SCALAPAY_enable_below_widget_text_payfour';
    const SCALAPAY_PAY_IN_4_PRODUCT_PAGE_AFTER_WIDGET_TEXT = 'SCALAPAY_below_widget_text_payfour';
    const SCALAPAY_PAY_IN_4_PRODUCT_PAGE_AMOUNT_SELECTORS = 'SCALAPAY_PRODUCT_PAGE_SELECTORS_FOUR';
    const SCALAPAY_PAY_IN_4_PRODUCT_PAGE_WIDGET_POSITION = 'SCALAPAY_PRODUCT_TEXT_POSITIONS_FOUR';
    const SCALAPAY_PAY_IN_4_PRODUCT_PAGE_HIDE_WIDGET = 'SCALAPAY_showLogoProductPage_four';
    const SCALAPAY_PAY_IN_4_PRODUCT_PAGE_CURRENCY_POSITION = 'scalapay_currency_positionfour';
    const SCALAPAY_PAY_IN_4_PRODUCT_PAGE_HIDE_PRICE = 'SCALAPAY_hidePriceProductPage_FOUR';
    const SCALAPAY_PAY_IN_4_PRODUCT_PAGE_LOGO_SIZE = 'SCALAPAY_logoSizeProductPage_four';
    const SCALAPAY_PAY_IN_4_PRODUCT_PAGE_CURRENCY_TYPE = 'scalapay_currency_displayfour';
    const SCALAPAY_PAY_IN_4_PRODUCT_PAGE_CUSTOM_CSS = 'SCALAPAY_PAY_IN_4_PRODUCT_PAGE_CUSTOM_CSS';
    const SCALAPAY_PAY_IN_4_CART_PAGE_AMOUNT_SELECTORS = 'SCALAPAY_CART_PAGE_SELECTORS_FOUR';
    const SCALAPAY_PAY_IN_4_CART_PAGE_WIDGET_POSITION = 'SCALAPAY_CART_TEXT_POSITIONS_FOUR';
    const SCALAPAY_PAY_IN_4_CART_PAGE_HIDE_WIDGET = 'SCALAPAY_showLogoCartPage_FOUR';
    const SCALAPAY_PAY_IN_4_CART_PAGE_HIDE_PRICE = 'SCALAPAY_hidePriceCartPage_FOUR';
    const SCALAPAY_PAY_IN_4_CART_PAGE_CURRENCY_POSITION = 'scalapay_currency_position_four_cart';
    const SCALAPAY_PAY_IN_4_CART_PAGE_CURRENCY_TYPE = 'scalapay_currency_display_four_cart';
    const SCALAPAY_PAY_IN_4_CART_PAGE_LOGO_SIZE = 'SCALAPAY_logoSizeCartPage_FOUR';
    const SCALAPAY_PAY_IN_4_CART_PAGE_CUSTOM_CSS = 'SCALAPAY_PAY_IN_4_CART_PAGE_CUSTOM_CSS';
    const SCALAPAY_PAY_IN_4_CHECKOUT_PAGE_AMOUNT_SELECTORS = 'SCALAPAY_PAY_IN_4_CHECKOUT_PAGE_AMOUNT_SELECTORS';
    const SCALAPAY_PAY_IN_4_CHECKOUT_PAGE_SHOW_TITLE = 'SCALAPAY_PAY_IN_4_CHECKOUT_PAGE_SHOW_TITLE';
    const SCALAPAY_PAY_IN_4_CHECKOUT_PAGE_HIDE_PRICE = 'SCALAPAY_PAY_IN_4_CHECKOUT_PAGE_HIDE_PRICE';
    const SCALAPAY_PAY_IN_4_CHECKOUT_PAGE_CURRENCY_POSITION = 'SCALAPAY_PAY_IN_4_CHECKOUT_PAGE_CURRENCY_POSITION';
    const SCALAPAY_PAY_IN_4_CHECKOUT_PAGE_CURRENCY_TYPE = 'SCALAPAY_PAY_IN_4_CHECKOUT_PAGE_CURRENCY_TYPE';
    const SCALAPAY_PAY_IN_4_CHECKOUT_PAGE_CUSTOM_CSS = 'SCALAPAY_PAY_IN_4_CHECKOUT_PAGE_CUSTOM_CSS';

    const SCALAPAY_PAY_LATER_ENABLED = 'SCALAPAY_ENABLE_PAYLATER';
    const SCALAPAY_PAY_LATER_LIVE_MODE_ENABLED = 'SCALAPAY_LIVE_MODE_PAYLATER';
    const SCALAPAY_PAY_LATER_ALLOWED_COUNTRIES = 'ALLOWED_COUNTRIES_LATER';
    const SCALAPAY_PAY_LATER_ALLOWED_CURRENCIES = 'SCALAPAY_ALLOWED_CURRENCIES_LATER';
    const SCALAPAY_PAY_LATER_ALLOWED_LANGUAGES = 'SCALAPAY_ALLOWED_LANGUAGES_LATER';
    const SCALAPAY_PAY_LATER_RESTRICTED_CATEGORIES = 'scala_cat_restrictions_later';
    const SCALAPAY_PAY_LATER_MINIMUM_AMOUNT = 'SCALAPAY_MINIMUM_AMOUNT_LATER';
    const SCALAPAY_PAY_LATER_MAXIMUM_AMOUNT = 'SCALAPAY_MAXIMUM_AMOUNT_LATER';
    const SCALAPAY_PAY_LATER_PAY_AFTER_DAYS = 'PayLater_numberOfDays';
    const SCALAPAY_PAY_LATER_ORDER_STATUS = 'SCALAPAY_ORDER_STATUS_LATER';
    const SCALAPAY_PAY_LATER_PRODUCT_PAGE_AMOUNT_SELECTORS = 'SCALAPAY_PRODUCT_PAGE_SELECTORS_LATER';
    const SCALAPAY_PAY_LATER_PRODUCT_PAGE_WIDGET_POSITION = 'SCALAPAY_PRODUCT_TEXT_POSITIONS_LATER';
    const SCALAPAY_PAY_LATER_PRODUCT_PAGE_HIDE_WIDGET = 'SCALAPAY_HideProductPagelater';
    const SCALAPAY_PAY_LATER_PRODUCT_PAGE_LOGO_SIZE = 'SCALAPAY_logoSizeProductPageLater';
    const SCALAPAY_PAY_LATER_PRODUCT_PAGE_THEME = 'scalapay_paylater_theme';
    const SCALAPAY_PAY_LATER_PRODUCT_PAGE_CUSTOM_CSS = 'SCALAPAY_PAY_LATER_PRODUCT_PAGE_CUSTOM_CSS';
    const SCALAPAY_PAY_LATER_CART_PAGE_AMOUNT_SELECTORS = 'SCALAPAY_CART_PAGE_SELECTORS_LATER';
    const SCALAPAY_PAY_LATER_CART_PAGE_WIDGET_POSITION = 'SCALAPAY_CART_TEXT_POSITIONS_LATER';
    const SCALAPAY_PAY_LATER_CART_PAGE_HIDE_WIDGET = 'SCALAPAY_showLogoCartPageLater';
    const SCALAPAY_PAY_LATER_CART_PAGE_LOGO_SIZE = 'SCALAPAY_logoSizeCartPageLater';
    const SCALAPAY_PAY_LATER_CART_PAGE_THEME = 'scalapay_paylatercart_theme';
    const SCALAPAY_PAY_LATER_CART_PAGE_CUSTOM_CSS = 'SCALAPAY_PAY_LATER_CART_PAGE_CUSTOM_CSS';
    const SCALAPAY_PAY_LATER_CHECKOUT_PAGE_AMOUNT_SELECTORS = 'SCALAPAY_PAY_LATER_CHECKOUT_PAGE_AMOUNT_SELECTORS';
    const SCALAPAY_PAY_LATER_CHECKOUT_PAGE_SHOW_TITLE = 'SCALAPAY_PAY_LATER_CHECKOUT_PAGE_SHOW_TITLE';
    const SCALAPAY_PAY_LATER_CHECKOUT_PAGE_CUSTOM_CSS = 'SCALAPAY_PAY_LATER_CHECKOUT_PAGE_CUSTOM_CSS';

    const SCALAPAY_IN_PAGE_CHECKOUT_ENABLE = 'SCALAPAY_ENABLE_IN_PAGE_CHECKOUT';
    const SCALAPAY_IN_PAGE_CHECKOUT_AGREEMENT_SELECTORS = 'SCALAPAY_IN_PAGE_CHECKOUT_AGREEMENT_SELECTORS';
    const SCALAPAY_IN_PAGE_CHECKOUT_PLACE_ORDER_SELECTOR = 'SCALAPAY_IN_PAGE_CHECKOUT_PLACE_ORDER_SELECTOR';
    const SCALAPAY_IN_PAGE_CHECKOUT_PLACE_ORDER_WRAPPER_STYLE = 'SCALAPAY_IN_PAGE_CHECKOUT_PLACE_ORDER_WRAPPER_STYLE';

    protected $configs = [
        self::SCALAPAY_LIVE_KEY,
        self::SCALAPAY_TEST_KEY,
        self::SCALAPAY_CSS_LOGO_TEXT,
        self::SCALAPAY_ADD_WIDGET_SCRIPTS,
        self::SCALAPAY_ENABLE_VIRTUAL_PRODUCTS,
        self::SCALAPAY_TEST_URL,
        self::SCALAPAY_LIVE_URL,
        self::SCALAPAY_HOOK_WIDGET,

        self::SCALAPAY_PAY_IN_3_ENABLED,
        self::SCALAPAY_PAY_IN_3_LIVE_MODE_ENABLED,
        self::SCALAPAY_PAY_IN_3_ORDER_STATUS,
        self::SCALAPAY_PAY_IN_3_MINIMUM_AMOUNT,
        self::SCALAPAY_PAY_IN_3_MAXIMUM_AMOUNT,
        self::SCALAPAY_PAY_IN_3_ALLOWED_COUNTRIES,
        self::SCALAPAY_PAY_IN_3_ALLOWED_LANGUAGES,
        self::SCALAPAY_PAY_IN_3_ALLOWED_CURRENCIES,
        self::SCALAPAY_PAY_IN_3_RESTRICTED_CATEGORIES,
        self::SCALAPAY_PAY_IN_3_PRODUCT_PAGE_AMOUNT_SELECTORS,
        self::SCALAPAY_PAY_IN_3_PRODUCT_PAGE_LOGO_SIZE,
        self::SCALAPAY_PAY_IN_3_PRODUCT_PAGE_HIDE_WIDGET,
        self::SCALAPAY_PAY_IN_3_PRODUCT_PAGE_WIDGET_POSITION,
        self::SCALAPAY_PAY_IN_3_PRODUCT_PAGE_HIDE_PRICE,
        self::SCALAPAY_PAY_IN_3_PRODUCT_PAGE_AFTER_WIDGET_TEXT_ENABLED,
        self::SCALAPAY_PAY_IN_3_PRODUCT_PAGE_AFTER_WIDGET_TEXT,
        self::SCALAPAY_PAY_IN_3_PRODUCT_PAGE_CURRENCY_POSITION,
        self::SCALAPAY_PAY_IN_3_PRODUCT_PAGE_CURRENCY_TYPE,
        self::SCALAPAY_PAY_IN_3_PRODUCT_PAGE_CUSTOM_CSS,
        self::SCALAPAY_PAY_IN_3_CART_PAGE_AMOUNT_SELECTORS,
        self::SCALAPAY_PAY_IN_3_CART_PAGE_LOGO_SIZE,
        self::SCALAPAY_PAY_IN_3_CART_PAGE_HIDE_WIDGET,
        self::SCALAPAY_PAY_IN_3_CART_PAGE_WIDGET_POSITION,
        self::SCALAPAY_PAY_IN_3_CART_PAGE_HIDE_PRICE,
        self::SCALAPAY_PAY_IN_3_CART_PAGE_CURRENCY_POSITION,
        self::SCALAPAY_PAY_IN_3_CART_PAGE_CURRENCY_TYPE,
        self::SCALAPAY_PAY_IN_3_CART_PAGE_CUSTOM_CSS,
        self::SCALAPAY_PAY_IN_3_CHECKOUT_PAGE_AMOUNT_SELECTORS,
        self::SCALAPAY_PAY_IN_3_CHECKOUT_PAGE_SHOW_TITLE,
        self::SCALAPAY_PAY_IN_3_CHECKOUT_PAGE_HIDE_PRICE,
        self::SCALAPAY_PAY_IN_3_CHECKOUT_PAGE_CURRENCY_POSITION,
        self::SCALAPAY_PAY_IN_3_CHECKOUT_PAGE_CURRENCY_TYPE,
        self::SCALAPAY_PAY_IN_3_CHECKOUT_PAGE_CUSTOM_CSS,

        self::SCALAPAY_PAY_IN_4_ENABLED,
        self::SCALAPAY_PAY_IN_4_LIVE_MODE_ENABLED,
        self::SCALAPAY_PAY_IN_4_ORDER_STATUS,
        self::SCALAPAY_PAY_IN_4_ALLOWED_COUNTRIES,
        self::SCALAPAY_PAY_IN_4_ALLOWED_CURRENCIES,
        self::SCALAPAY_PAY_IN_4_ALLOWED_LANGUAGES,
        self::SCALAPAY_PAY_IN_4_RESTRICTED_CATEGORIES,
        self::SCALAPAY_PAY_IN_4_MINIMUM_AMOUNT,
        self::SCALAPAY_PAY_IN_4_MAXIMUM_AMOUNT,
        self::SCALAPAY_PAY_IN_4_PRODUCT_PAGE_AFTER_WIDGET_TEXT_ENABLED,
        self::SCALAPAY_PAY_IN_4_PRODUCT_PAGE_AFTER_WIDGET_TEXT,
        self::SCALAPAY_PAY_IN_4_PRODUCT_PAGE_AMOUNT_SELECTORS,
        self::SCALAPAY_PAY_IN_4_PRODUCT_PAGE_WIDGET_POSITION,
        self::SCALAPAY_PAY_IN_4_PRODUCT_PAGE_HIDE_WIDGET,
        self::SCALAPAY_PAY_IN_4_PRODUCT_PAGE_CURRENCY_POSITION,
        self::SCALAPAY_PAY_IN_4_PRODUCT_PAGE_HIDE_PRICE,
        self::SCALAPAY_PAY_IN_4_PRODUCT_PAGE_LOGO_SIZE,
        self::SCALAPAY_PAY_IN_4_PRODUCT_PAGE_CURRENCY_TYPE,
        self::SCALAPAY_PAY_IN_4_PRODUCT_PAGE_CUSTOM_CSS,
        self::SCALAPAY_PAY_IN_4_CART_PAGE_AMOUNT_SELECTORS,
        self::SCALAPAY_PAY_IN_4_CART_PAGE_WIDGET_POSITION,
        self::SCALAPAY_PAY_IN_4_CART_PAGE_HIDE_WIDGET,
        self::SCALAPAY_PAY_IN_4_CART_PAGE_HIDE_PRICE,
        self::SCALAPAY_PAY_IN_4_CART_PAGE_CURRENCY_POSITION,
        self::SCALAPAY_PAY_IN_4_CART_PAGE_CURRENCY_TYPE,
        self::SCALAPAY_PAY_IN_4_CART_PAGE_LOGO_SIZE,
        self::SCALAPAY_PAY_IN_4_CART_PAGE_CUSTOM_CSS,
        self::SCALAPAY_PAY_IN_4_CHECKOUT_PAGE_AMOUNT_SELECTORS,
        self::SCALAPAY_PAY_IN_4_CHECKOUT_PAGE_SHOW_TITLE,
        self::SCALAPAY_PAY_IN_4_CHECKOUT_PAGE_HIDE_PRICE,
        self::SCALAPAY_PAY_IN_4_CHECKOUT_PAGE_CURRENCY_POSITION,
        self::SCALAPAY_PAY_IN_4_CHECKOUT_PAGE_CURRENCY_TYPE,
        self::SCALAPAY_PAY_IN_4_CHECKOUT_PAGE_CUSTOM_CSS,

        self::SCALAPAY_PAY_LATER_ENABLED,
        self::SCALAPAY_PAY_LATER_LIVE_MODE_ENABLED,
        self::SCALAPAY_PAY_LATER_ALLOWED_COUNTRIES,
        self::SCALAPAY_PAY_LATER_ALLOWED_CURRENCIES,
        self::SCALAPAY_PAY_LATER_ALLOWED_LANGUAGES,
        self::SCALAPAY_PAY_LATER_RESTRICTED_CATEGORIES,
        self::SCALAPAY_PAY_LATER_MINIMUM_AMOUNT,
        self::SCALAPAY_PAY_LATER_MAXIMUM_AMOUNT,
        // self::SCALAPAY_PAY_LATER_PAY_AFTER_DAYS,
        self::SCALAPAY_PAY_LATER_ORDER_STATUS,
        self::SCALAPAY_PAY_LATER_PRODUCT_PAGE_AMOUNT_SELECTORS,
        self::SCALAPAY_PAY_LATER_PRODUCT_PAGE_WIDGET_POSITION,
        self::SCALAPAY_PAY_LATER_PRODUCT_PAGE_HIDE_WIDGET,
        self::SCALAPAY_PAY_LATER_PRODUCT_PAGE_LOGO_SIZE,
        self::SCALAPAY_PAY_LATER_PRODUCT_PAGE_THEME,
        self::SCALAPAY_PAY_LATER_PRODUCT_PAGE_CUSTOM_CSS,
        self::SCALAPAY_PAY_LATER_CART_PAGE_AMOUNT_SELECTORS,
        self::SCALAPAY_PAY_LATER_CART_PAGE_WIDGET_POSITION,
        self::SCALAPAY_PAY_LATER_CART_PAGE_HIDE_WIDGET,
        self::SCALAPAY_PAY_LATER_CART_PAGE_LOGO_SIZE,
        self::SCALAPAY_PAY_LATER_CART_PAGE_THEME,
        self::SCALAPAY_PAY_LATER_CART_PAGE_CUSTOM_CSS,
        self::SCALAPAY_PAY_LATER_CHECKOUT_PAGE_AMOUNT_SELECTORS,
        self::SCALAPAY_PAY_LATER_CHECKOUT_PAGE_SHOW_TITLE,
        self::SCALAPAY_PAY_LATER_CHECKOUT_PAGE_CUSTOM_CSS,

        self::SCALAPAY_IN_PAGE_CHECKOUT_ENABLE,
        self::SCALAPAY_IN_PAGE_CHECKOUT_AGREEMENT_SELECTORS,
        self::SCALAPAY_IN_PAGE_CHECKOUT_PLACE_ORDER_SELECTOR,
        self::SCALAPAY_IN_PAGE_CHECKOUT_PLACE_ORDER_WRAPPER_STYLE,
    ];
    /**
     * @var false
     */
    public $isCustomizedPlugin;

    public function __construct()
    {
        $this->name = 'scalapay';
        $this->tab = 'payments_gateways';
        $this->version = '2.1.13';
        $this->ps_versions_compliancy = ['min' => '1.6', 'max' => _PS_VERSION_];
        $this->author = 'Scalapay';

        $this->controllers = ['validation', 'return'];

        // @phpstan-ignore-next-line
        $this->bootstrap = true;
        $this->module_key = '486a8e1dff7737fda627154740d432f1';
        $this->displayName = 'Scalapay';
        $this->description = $this->l('Give to your customer the possibility to Pay in 3 or 4 easy instalments or Pay Later without interest using Scalapay.');

        // @phpstan-ignore-next-line
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall Scalapay?');

        $this->isCustomizedPlugin = false;
        parent::__construct();
    }

    public function install()
    {
        Configuration::updateValue(self::SCALAPAY_TEST_KEY, 'qhtfs87hjnc12kkos');
        Configuration::updateValue(self::SCALAPAY_TEST_URL, 'https://integration.api.scalapay.com');
        Configuration::updateValue(self::SCALAPAY_LIVE_URL, 'https://api.scalapay.com');
        Configuration::updateValue(self::SCALAPAY_HOOK_WIDGET, 'displayHeader');

        Configuration::updateValue(self::SCALAPAY_PAY_IN_3_ORDER_STATUS, 2);
        Configuration::updateValue(self::SCALAPAY_PAY_IN_4_ORDER_STATUS, 2);
        Configuration::updateValue(self::SCALAPAY_PAY_LATER_ORDER_STATUS, 2);
        Configuration::updateValue(self::SCALAPAY_ADD_WIDGET_SCRIPTS, true);

        Configuration::updateValue(self::SCALAPAY_PAY_IN_3_MINIMUM_AMOUNT, '5');
        Configuration::updateValue(self::SCALAPAY_PAY_IN_3_MAXIMUM_AMOUNT, '899');
        Configuration::updateValue(self::SCALAPAY_PAY_IN_3_PRODUCT_PAGE_LOGO_SIZE, '100');
        Configuration::updateValue(self::SCALAPAY_PAY_IN_3_CART_PAGE_LOGO_SIZE, '100');
        Configuration::updateValue(self::SCALAPAY_PAY_IN_3_ALLOWED_COUNTRIES, 'IT');
        Configuration::updateValue(self::SCALAPAY_PAY_IN_3_ALLOWED_LANGUAGES, 'it');
        Configuration::updateValue(self::SCALAPAY_PAY_IN_3_ALLOWED_CURRENCIES, 'EUR');
        Configuration::updateValue(self::SCALAPAY_PAY_IN_3_PRODUCT_PAGE_CURRENCY_TYPE, 'symbol');
        Configuration::updateValue(self::SCALAPAY_PAY_IN_3_CART_PAGE_CURRENCY_POSITION, 'after');
        Configuration::updateValue(self::SCALAPAY_PAY_IN_3_CART_PAGE_CURRENCY_TYPE, 'symbol');
        Configuration::updateValue(self::SCALAPAY_PAY_IN_3_CHECKOUT_PAGE_SHOW_TITLE, true);
        Configuration::updateValue(self::SCALAPAY_PAY_IN_3_CHECKOUT_PAGE_CURRENCY_POSITION, 'after');
        Configuration::updateValue(self::SCALAPAY_PAY_IN_3_CHECKOUT_PAGE_CURRENCY_TYPE, 'symbol');
        Configuration::updateValue(self::SCALAPAY_PAY_IN_3_PRODUCT_PAGE_CURRENCY_POSITION, 'after');

        // @phpstan-ignore-next-line
        if (version_compare(_PS_VERSION_, '1.7.0', '<')) {
            Configuration::updateValue(self::SCALAPAY_PAY_IN_3_PRODUCT_PAGE_AMOUNT_SELECTORS, '"#our_price_display"');
            Configuration::updateValue(self::SCALAPAY_PAY_IN_3_PRODUCT_PAGE_WIDGET_POSITION, '.content_prices');
            Configuration::updateValue(self::SCALAPAY_PAY_IN_3_PRODUCT_PAGE_CUSTOM_CSS, '');

            Configuration::updateValue(self::SCALAPAY_PAY_IN_3_CART_PAGE_AMOUNT_SELECTORS, '"#total_price"');
            Configuration::updateValue(self::SCALAPAY_PAY_IN_3_CART_PAGE_WIDGET_POSITION, '#order-detail-content');
            Configuration::updateValue(self::SCALAPAY_PAY_IN_3_CART_PAGE_CUSTOM_CSS, 'display: flex; justify-content: end;');

            Configuration::updateValue(self::SCALAPAY_PAY_IN_3_CHECKOUT_PAGE_AMOUNT_SELECTORS, '"#total_price"');
            Configuration::updateValue(self::SCALAPAY_PAY_IN_3_CHECKOUT_PAGE_CUSTOM_CSS, 'margin: 0 0 0 15px;');
        } else {
            Configuration::updateValue(self::SCALAPAY_PAY_IN_3_PRODUCT_PAGE_AMOUNT_SELECTORS, '".current-price-value"');
            Configuration::updateValue(self::SCALAPAY_PAY_IN_3_PRODUCT_PAGE_WIDGET_POSITION, '.product-prices');
            Configuration::updateValue(self::SCALAPAY_PAY_IN_3_PRODUCT_PAGE_CUSTOM_CSS, '');

            Configuration::updateValue(self::SCALAPAY_PAY_IN_3_CART_PAGE_AMOUNT_SELECTORS, '".cart-summary-line.cart-total .value"');
            Configuration::updateValue(self::SCALAPAY_PAY_IN_3_CART_PAGE_WIDGET_POSITION, '.cart-detailed-totals');
            Configuration::updateValue(self::SCALAPAY_PAY_IN_3_CART_PAGE_CUSTOM_CSS, 'margin-left:20px;');

            Configuration::updateValue(self::SCALAPAY_PAY_IN_3_CHECKOUT_PAGE_AMOUNT_SELECTORS, '".cart-summary-line.cart-total .value"');
            Configuration::updateValue(self::SCALAPAY_PAY_IN_3_CHECKOUT_PAGE_CUSTOM_CSS, 'margin: 0 0 20px 0;');
        }

        Configuration::updateValue(self::SCALAPAY_PAY_IN_4_PRODUCT_PAGE_CURRENCY_POSITION, 'after');
        Configuration::updateValue(self::SCALAPAY_PAY_IN_4_PRODUCT_PAGE_LOGO_SIZE, '100');

        Configuration::updateValue(self::SCALAPAY_PAY_IN_4_CART_PAGE_CURRENCY_POSITION, 'after');
        Configuration::updateValue(self::SCALAPAY_PAY_IN_4_CART_PAGE_CURRENCY_TYPE, 'symbol');
        Configuration::updateValue(self::SCALAPAY_PAY_IN_4_PRODUCT_PAGE_CURRENCY_TYPE, 'symbol');
        Configuration::updateValue(self::SCALAPAY_PAY_IN_4_CART_PAGE_LOGO_SIZE, '100');
        Configuration::updateValue(self::SCALAPAY_PAY_IN_4_ALLOWED_COUNTRIES, 'IT');
        Configuration::updateValue(self::SCALAPAY_PAY_IN_4_ALLOWED_CURRENCIES, 'EUR');
        Configuration::updateValue(self::SCALAPAY_PAY_IN_4_ALLOWED_LANGUAGES, 'it');
        Configuration::updateValue(self::SCALAPAY_PAY_IN_4_MINIMUM_AMOUNT, '900');
        Configuration::updateValue(self::SCALAPAY_PAY_IN_4_MAXIMUM_AMOUNT, '1500');

        Configuration::updateValue(self::SCALAPAY_PAY_IN_4_CHECKOUT_PAGE_SHOW_TITLE, true);
        Configuration::updateValue(self::SCALAPAY_PAY_IN_4_CHECKOUT_PAGE_HIDE_PRICE, false);
        Configuration::updateValue(self::SCALAPAY_PAY_IN_4_CHECKOUT_PAGE_CURRENCY_POSITION, 'after');
        Configuration::updateValue(self::SCALAPAY_PAY_IN_4_CHECKOUT_PAGE_CURRENCY_TYPE, 'symbol');

        // @phpstan-ignore-next-line
        if (version_compare(_PS_VERSION_, '1.7.0', '<')) {
            Configuration::updateValue(self::SCALAPAY_PAY_IN_4_PRODUCT_PAGE_AMOUNT_SELECTORS, '"#our_price_display"');
            Configuration::updateValue(self::SCALAPAY_PAY_IN_4_PRODUCT_PAGE_WIDGET_POSITION, '.content_prices');
            Configuration::updateValue(self::SCALAPAY_PAY_IN_4_PRODUCT_PAGE_CUSTOM_CSS, '');

            Configuration::updateValue(self::SCALAPAY_PAY_IN_4_CART_PAGE_AMOUNT_SELECTORS, '"#total_price"');
            Configuration::updateValue(self::SCALAPAY_PAY_IN_4_CART_PAGE_WIDGET_POSITION, '#order-detail-content');
            Configuration::updateValue(self::SCALAPAY_PAY_IN_4_CART_PAGE_CUSTOM_CSS, 'display: flex; justify-content: end;');

            Configuration::updateValue(self::SCALAPAY_PAY_IN_4_CHECKOUT_PAGE_AMOUNT_SELECTORS, '"#total_price"');
            Configuration::updateValue(self::SCALAPAY_PAY_IN_4_CHECKOUT_PAGE_CUSTOM_CSS, 'margin: 0 0 0 15px;');
        } else {
            Configuration::updateValue(self::SCALAPAY_PAY_IN_4_PRODUCT_PAGE_AMOUNT_SELECTORS, '".current-price-value"');
            Configuration::updateValue(self::SCALAPAY_PAY_IN_4_PRODUCT_PAGE_WIDGET_POSITION, '.product-prices');
            Configuration::updateValue(self::SCALAPAY_PAY_IN_4_PRODUCT_PAGE_CUSTOM_CSS, '');

            Configuration::updateValue(self::SCALAPAY_PAY_IN_4_CART_PAGE_AMOUNT_SELECTORS, '".cart-summary-line.cart-total .value"');
            Configuration::updateValue(self::SCALAPAY_PAY_IN_4_CART_PAGE_WIDGET_POSITION, '.cart-detailed-totals');
            Configuration::updateValue(self::SCALAPAY_PAY_IN_4_CART_PAGE_CUSTOM_CSS, 'margin-left:20px;');

            Configuration::updateValue(self::SCALAPAY_PAY_IN_4_CHECKOUT_PAGE_AMOUNT_SELECTORS, '".cart-summary-line.cart-total .value"');
            Configuration::updateValue(self::SCALAPAY_PAY_IN_4_CHECKOUT_PAGE_CUSTOM_CSS, 'margin: 0 0 20px 0;');
        }

        Configuration::updateValue(self::SCALAPAY_PAY_LATER_PRODUCT_PAGE_LOGO_SIZE, '100');
        Configuration::updateValue(self::SCALAPAY_PAY_LATER_CART_PAGE_LOGO_SIZE, '100');
        Configuration::updateValue(self::SCALAPAY_PAY_LATER_PRODUCT_PAGE_THEME, 'primary');
        Configuration::updateValue(self::SCALAPAY_PAY_LATER_CART_PAGE_THEME, 'primary');
        Configuration::updateValue(self::SCALAPAY_PAY_LATER_ALLOWED_COUNTRIES, 'IT');
        Configuration::updateValue(self::SCALAPAY_PAY_LATER_ALLOWED_CURRENCIES, 'EUR');
        Configuration::updateValue(self::SCALAPAY_PAY_LATER_ALLOWED_LANGUAGES, 'it');
        Configuration::updateValue(self::SCALAPAY_PAY_LATER_MINIMUM_AMOUNT, '5');
        Configuration::updateValue(self::SCALAPAY_PAY_LATER_MAXIMUM_AMOUNT, '1500');
        Configuration::updateValue(self::SCALAPAY_PAY_LATER_PAY_AFTER_DAYS, '14');
        Configuration::updateValue(self::SCALAPAY_PAY_LATER_CHECKOUT_PAGE_SHOW_TITLE, true);

        // @phpstan-ignore-next-line
        if (version_compare(_PS_VERSION_, '1.7.0', '<')) {
            Configuration::updateValue(self::SCALAPAY_PAY_LATER_PRODUCT_PAGE_AMOUNT_SELECTORS, '"#our_price_display"');
            Configuration::updateValue(self::SCALAPAY_PAY_LATER_PRODUCT_PAGE_WIDGET_POSITION, '#add_to_cart');
            Configuration::updateValue(self::SCALAPAY_PAY_LATER_PRODUCT_PAGE_CUSTOM_CSS, 'margin-bottom:20px;margin-top:10px;');

            Configuration::updateValue(self::SCALAPAY_PAY_LATER_CART_PAGE_AMOUNT_SELECTORS, '"#total_price"');
            Configuration::updateValue(self::SCALAPAY_PAY_LATER_CART_PAGE_WIDGET_POSITION, '.cart_navigation');
            Configuration::updateValue(self::SCALAPAY_PAY_LATER_CART_PAGE_CUSTOM_CSS, 'margin-bottom:10px;margin-top: 25px;margin-left: 50%;');

            Configuration::updateValue(self::SCALAPAY_PAY_LATER_CHECKOUT_PAGE_AMOUNT_SELECTORS, '"#total_price"');
            Configuration::updateValue(self::SCALAPAY_PAY_LATER_CHECKOUT_PAGE_CUSTOM_CSS, 'margin: 0 0 0 15px;');
        } else {
            Configuration::updateValue(self::SCALAPAY_PAY_LATER_PRODUCT_PAGE_AMOUNT_SELECTORS, '".current-price-value"');
            Configuration::updateValue(self::SCALAPAY_PAY_LATER_PRODUCT_PAGE_WIDGET_POSITION, 'div.product-add-to-cart');
            Configuration::updateValue(self::SCALAPAY_PAY_LATER_PRODUCT_PAGE_CUSTOM_CSS, '');

            Configuration::updateValue(self::SCALAPAY_PAY_LATER_CART_PAGE_AMOUNT_SELECTORS, '".cart-summary-line.cart-total .value"');
            Configuration::updateValue(self::SCALAPAY_PAY_LATER_CART_PAGE_WIDGET_POSITION, '.checkout');
            Configuration::updateValue(self::SCALAPAY_PAY_LATER_CART_PAGE_CUSTOM_CSS, 'margin-top:10px;');

            Configuration::updateValue(self::SCALAPAY_PAY_LATER_CHECKOUT_PAGE_AMOUNT_SELECTORS, '".cart-summary-line.cart-total .value"');
            Configuration::updateValue(self::SCALAPAY_PAY_LATER_CHECKOUT_PAGE_CUSTOM_CSS, 'margin: 0 0 20px 0;');
        }

        Configuration::updateValue(self::SCALAPAY_IN_PAGE_CHECKOUT_ENABLE, false);
        Configuration::updateValue(self::SCALAPAY_IN_PAGE_CHECKOUT_AGREEMENT_SELECTORS, '"input[name=\'conditions_to_approve[terms-and-conditions]\']"');
        Configuration::updateValue(self::SCALAPAY_IN_PAGE_CHECKOUT_PLACE_ORDER_SELECTOR, '"#payment-confirmation button[type=\'submit\']"');
        Configuration::updateValue(self::SCALAPAY_IN_PAGE_CHECKOUT_PLACE_ORDER_WRAPPER_STYLE, '');

        try {
            return parent::install()
                && $this->ensureAllCarriersSelected()
                && $this->installOrderState()
                && $this->installDb()
                // Prestashop 1.6
                && $this->registerHook('displayPayment')
                && $this->registerHook('paymentReturn')
                // Prestashop 1.7
                && $this->registerHook('displayInvoice')
                && $this->registerHook('displayFooter')
                && $this->registerHook('paymentOptions')
                && $this->registerHook('actionOrderSlipAdd')
                && $this->registerHook('actionAdminControllerSetMedia')
                && $this->registerHook('displayShoppingCart')
                && $this->registerHook('displayCheckoutSummaryTop')
                // prestashop 8!
                && $this->registerHook('actionPresentPaymentOptions');
        } catch (\Exception $exception) {
            PrestaShopLogger::addLog(
                "[Scalapay] Installation failed {$exception->getMessage()} -- " . json_encode($exception->getTrace()),
                3,
                null,
                null,
                null,
                true
            );

            throw $exception;
        }
    }

    protected function ensureAllCarriersSelected()
    {
        $carriers = array_map(function ($carrier) {
            return $carrier['id_carrier'];
        }, Db::getInstance()->executeS((new DbQuery())->select('id_carrier')
            ->from('carrier')));

        $moduleId = Db::getInstance()->getRow((new DbQuery())->select('id_module')
            ->from('module')
            ->where("name = '" . $this->name . "'"))['id_module'];

        $moduleCarriers = array_map(function ($moduleCarrier) {
            return $moduleCarrier['id_reference'];
        }, Db::getInstance()->executeS((new DbQuery())->select('id_reference')
            ->from('module_carrier')
            ->where('id_module = ' . (int) $moduleId)));

        foreach (Shop::getShops(true, null, true) as $shop) {
            $values = array_reduce(array_diff($carriers, $moduleCarriers), function ($values, $referenceId) use ($moduleId, $shop) {
                return "($moduleId, $shop, $referenceId)" . ($values !== '' ? ", \n $values" : '');
            }, '');

            if (!$values) {
                continue;
            }

            $query = 'INSERT INTO `' . _DB_PREFIX_ . 'module_carrier` (id_module, id_shop, id_reference) VALUES ' . $values;
            Db::getInstance()->execute($query);
        }

        return true;
    }

    public function installOrderState()
    {
        // Search if there is already an orderStatusId for Scalapay
        // array reduce will return null if not found the status already created.
        if ($orderStateId = array_reduce(OrderStateCore::getOrderStates($this->context->language->id), function ($result, $state) {
            return $state['module_name'] === $this->name ? $state['id_order_state'] : $result;
        })) {
            // for sake of having same statues
            Configuration::updateValue(self::SCALAPAY_PS_WAITING_CAPTURE_STATUS_ID, $orderStateId);

            return true;
        }

        $order_state = new OrderState();

        $order_state->name = [];

        foreach (Language::getLanguages() as $language) {
            // @phpstan-ignore-next-line
            $order_state->name[$language['id_lang']] = 'Waiting Scalapay capture';
        }

        $order_state->send_email = false;
        $order_state->color = '#4169E1';
        $order_state->hidden = false;
        $order_state->delivery = false;
        $order_state->logable = false;
        $order_state->invoice = false;
        $order_state->module_name = $this->name;
        if ($order_state->add()) {
            @copy(
                $this->_path . '/views/img/icon_status.gif',
                @dirname(__FILE__) . '/../../img/os/' . (int) $order_state->id . '.gif'
            );
        }

        foreach (Shop::getShops(true, null, true) as $shop) {
            Configuration::updateValue(self::SCALAPAY_PS_WAITING_CAPTURE_STATUS_ID, $order_state->id, false, null, $shop);
        }

        return true;
    }

    public function uninstall()
    {
        foreach ($this->configs as $name) {
            Configuration::deleteByName($name);
        }

        return parent::uninstall();
    }

    public function getContent()
    {
        $productLimits = $this->getProductLimits();

        $html = '';

        $missingCurrencies = $this->checkMissingCurrencies();
        if ($missingCurrencies) {
            $html .= $this->displayWarning(sprintf($this->l('Remember to enable the missing Currencies %s for this module on prestashop.'), json_encode($missingCurrencies)));
        }

        /*
         * If values have been submitted in the form, process.
         */
        if (Tools::isSubmit(self::SCALAPAY_SUBMIT_FORM)) {
            $errors = $this->validateLimits($productLimits);
            if (count($errors)) {
                $html .= '<div style="margin-top:60px;">';
                foreach ($errors as $err) {
                    $html .= $this->displayWarning($err);
                }
                $html .= '</div>';
            }

            $errors = $this->validateRequest($productLimits);
            if (count($errors)) {
                $html .= '<div style="margin-top:60px;">';
                foreach ($errors as $err) {
                    $html .= $this->displayError($err);
                }
                $html .= '</div>';
            } else {
                $this->postProcess();
                $html .= $this->displayConfirmation($this->l('Settings updated'));
            }
        }

        return $html . $this->renderForm($productLimits);
    }

    protected function checkMissingCurrencies()
    {
        $currencies = [];

        foreach (CurrencyCore::checkPaymentCurrencies($this->id) as $currency) {
            $currencies[] = $this->getIsoCodeById($currency['id_currency']);
        }
        $currentEnabled = [];

        foreach ([
                     self::SCALAPAY_PAY_IN_4_ALLOWED_CURRENCIES,
                     self::SCALAPAY_PAY_IN_3_ALLOWED_CURRENCIES,
                     self::SCALAPAY_PAY_LATER_ALLOWED_CURRENCIES,
                 ] as $key) {
            $currentEnabled = array_merge($currentEnabled, explode(',', Configuration::get($key) ?: ''));
        }

        return array_unique(array_diff($currentEnabled, array_intersect($currentEnabled, $currencies)));
    }

    protected function validateLimits(array $productLimits)
    {
        $errors = [];

        if (Tools::getValue(self::SCALAPAY_PAY_IN_3_ENABLED)) {
            $minAmount = Tools::getValue(self::SCALAPAY_PAY_IN_3_MINIMUM_AMOUNT);
            $maxAmount = Tools::getValue(self::SCALAPAY_PAY_IN_3_MAXIMUM_AMOUNT);

            if (isset($productLimits['online_pay-in-3'])
                && ($minAmount < $productLimits['online_pay-in-3']['min'] || $maxAmount > $productLimits['online_pay-in-3']['max'])) {
                $errors[] = sprintf($this->l('Amount should be between %s and %s for Pay in 3 Payment Option.'), $productLimits['online_pay-in-3']['min'], $productLimits['online_pay-in-3']['max']);
            }
        }

        if (Tools::getValue(self::SCALAPAY_PAY_IN_4_ENABLED)) {
            $minAmount = Tools::getValue(self::SCALAPAY_PAY_IN_4_MINIMUM_AMOUNT);
            $maxAmount = Tools::getValue(self::SCALAPAY_PAY_IN_4_MAXIMUM_AMOUNT);

            if (isset($productLimits['online_pay-in-4'])
                && ($minAmount < $productLimits['online_pay-in-4']['min'] || $maxAmount > $productLimits['online_pay-in-4']['max'])) {
                $errors[] = sprintf($this->l('Amount should be between %s and %s  for Pay in 4 Payment Option.'), $productLimits['online_pay-in-4']['min'], $productLimits['online_pay-in-4']['max']);
            }
        }

        if (Tools::getValue(self::SCALAPAY_PAY_LATER_ENABLED)) {
            $minAmount = Tools::getValue(self::SCALAPAY_PAY_LATER_MINIMUM_AMOUNT);
            $maxAmount = Tools::getValue(self::SCALAPAY_PAY_LATER_MAXIMUM_AMOUNT);
            if (isset($productLimits['online_later']) && $minAmount < $productLimits['online_later']['min'] || $maxAmount > $productLimits['online_later']['max']) {
                $errors[] = sprintf($this->l('Amount should be between %s and %s for Pay Later Payment Option.'), $productLimits['online_later']['min'], $productLimits['online_later']['max']);
            }
        }

        return $errors;
    }

    protected function validateRequest(array $productLimits)
    {
        if (!Tools::isSubmit(self::SCALAPAY_SUBMIT_FORM)) {
            return [];
        }

        $errors = [];

        if (!Tools::getValue(self::SCALAPAY_HOOK_WIDGET)) {
            $errors[] = $this->l('The Hook where to display the Widget is required.');
        }

        if (!Tools::getValue(self::SCALAPAY_TEST_KEY) and Tools::getValue(self::SCALAPAY_PAY_IN_3_LIVE_MODE_ENABLED)) {
            $errors[] = $this->l('The "Test API Key" field is required in test mode.');
        }

        if (!Tools::getValue(self::SCALAPAY_LIVE_KEY) and Tools::getValue(self::SCALAPAY_PAY_IN_3_LIVE_MODE_ENABLED)) {
            $errors[] = $this->l('The "Live API Key" field is required in Live mode.');
        }

        if (Tools::getValue(self::SCALAPAY_PAY_IN_3_ENABLED)) {
            $minAmount = Tools::getValue(self::SCALAPAY_PAY_IN_3_MINIMUM_AMOUNT);
            $maxAmount = Tools::getValue(self::SCALAPAY_PAY_IN_3_MAXIMUM_AMOUNT);

            if (Tools::getValue(self::SCALAPAY_PAY_IN_3_LIVE_MODE_ENABLED) && !isset($productLimits['online_pay-in-3'])) {
                $errors[] = $this->l('You are not enabled to use Pay in 3 Payment.');
            }

            if (!$minAmount) {
                $errors[] = $this->l('Minimum amount is required for Pay in 3 Payment Option.');
            }
            if (!$maxAmount) {
                $errors[] = $this->l('Maximum amount is required for Pay in 3 Payment Option.');
            }

            if ($minAmount >= $maxAmount) {
                $errors[] = $this->l('Min Amount cannot be greater that Max Amount for Pay in 3 Payment Option.');
            }

            if (!Tools::getValue(self::SCALAPAY_PAY_IN_3_ALLOWED_LANGUAGES)) {
                $errors[] = $this->l('You must select at least one language for Pay in 3 Payment Option.');
            }
            if (!Tools::getValue(self::SCALAPAY_PAY_IN_3_ALLOWED_CURRENCIES)) {
                $errors[] = $this->l('You must select at least one currency for Pay in 3 Payment Option.');
            }
            if (!Tools::getValue(self::SCALAPAY_PAY_IN_3_ALLOWED_COUNTRIES)) {
                $errors[] = $this->l('You must select at least one country for Pay in 3 Payment Option.');
            }
        }

        if (Tools::getValue(self::SCALAPAY_PAY_IN_4_ENABLED)) {
            $minAmount = Tools::getValue(self::SCALAPAY_PAY_IN_4_MINIMUM_AMOUNT);
            $maxAmount = Tools::getValue(self::SCALAPAY_PAY_IN_4_MAXIMUM_AMOUNT);

            if (Tools::getValue(self::SCALAPAY_PAY_IN_4_LIVE_MODE_ENABLED) && !isset($productLimits['online_pay-in-4'])) {
                $errors[] = $this->l('You are not enabled to use Pay in 4 Payment.');
            }

            if (!$minAmount) {
                $errors[] = $this->l('Minimum amount is required for Pay in 4 Payment Option.');
            }
            if (!$maxAmount) {
                $errors[] = $this->l('Maximum amount is required for Pay in 4 Payment Option.');
            }

            if ($minAmount >= $maxAmount) {
                $errors[] = $this->l('Min Amount cannot be greater that Max Amount for Pay in 4 Payment Option.');
            }

            if (!Tools::getValue(self::SCALAPAY_PAY_IN_4_ALLOWED_COUNTRIES)) {
                $errors[] = $this->l('You must select at least one country for Pay in 4 Payment Option.');
            }
            if (!Tools::getValue(self::SCALAPAY_PAY_IN_4_ALLOWED_CURRENCIES)) {
                $errors[] = $this->l('You must select at least one currency for Pay in 4 Payment Option.');
            }
            if (!Tools::getValue(self::SCALAPAY_PAY_IN_4_ALLOWED_LANGUAGES)) {
                $errors[] = $this->l('You must select at least one language for Pay in 4 Payment Option.');
            }
        }

        if (Tools::getValue(self::SCALAPAY_PAY_LATER_ENABLED)) {
            $minAmount = Tools::getValue(self::SCALAPAY_PAY_LATER_MINIMUM_AMOUNT);
            $maxAmount = Tools::getValue(self::SCALAPAY_PAY_LATER_MAXIMUM_AMOUNT);

            if (Tools::getValue(self::SCALAPAY_PAY_LATER_LIVE_MODE_ENABLED) && !isset($productLimits['online_later'])) {
                $errors[] = $this->l('You are not enabled to use Pay Later Payment.');
            }

            if (!$minAmount) {
                $errors[] = $this->l('Minimum amount is required for Pay Later Payment Option.');
            }
            if (!$maxAmount) {
                $errors[] = $this->l('Maximum amount is required for Pay Later Payment Option.');
            }
            if ($minAmount >= $maxAmount) {
                $errors[] = $this->l('Min Amount cannot be greater that Max Amount for Pay Later Payment Option.');
            }

            if (!Tools::getValue(self::SCALAPAY_PAY_LATER_ALLOWED_COUNTRIES)) {
                $errors[] = $this->l('You must select atleast one country for Pay Later Payment Option.');
            }
            if (!Tools::getValue(self::SCALAPAY_PAY_LATER_ALLOWED_CURRENCIES)) {
                $errors[] = $this->l('You must select atleast one currency for Pay Later Payment Option.');
            }
            if (!Tools::getValue(self::SCALAPAY_PAY_LATER_ALLOWED_LANGUAGES)) {
                $errors[] = $this->l('You must select atleast one language for Pay Later Payment Option.');
            }
            //            if (!Tools::getValue(self::SCALAPAY_PAY_LATER_PAY_AFTER_DAYS)) {
            //                $errors[] = $this->l('Frequency Number is required for Pay Later Payment Option.');
            //            }
        }

        return $errors;
    }

    protected function getProductLimits()
    {
        $data = [
            'online_later' => ['min' => 5, 'max' => 899],
            'online_pay-in-4' => ['min' => 900, 'max' => 2000],
            'online_pay-in-3' => ['min' => 5, 'max' => 899],
        ];

        if (!Configuration::get(self::SCALAPAY_LIVE_KEY) || !Configuration::get(self::SCALAPAY_TEST_KEY)) {
            return $data;
        }

        $result = $this->doRequest(self::PRODUCT_PAY_IN_3, 'GET', '/v3/configurations');

        if ($result['info']['http_code'] !== 200) {
            return $data;
        }

        return array_map(function ($v) {
            return ['min' => $v['configuration']['minimumAmount']['amount'], 'max' => $v['configuration']['maximumAmount']['amount']];
        }, array_combine(array_map(function ($v) {
            return "{$v['type']}_{$v['product']}";
        }, $result['data']), $result['data']));
    }

    protected function renderForm(array $productLimits)
    {
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG');

        $helper->identifier = $this->identifier;
        $helper->submit_action = self::SCALAPAY_SUBMIT_FORM;
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            . '&configure=' . $this->name . '&tab_module='
            . $this->tab . '&module_name='
            . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = [
            'fields_value' => $this->getConfigFormValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        ];

        $countries = array_map(function ($country) {
            return [
                'id' => $country['iso_code'],
                'name' => $country['name'],
            ];
        }, Country::getCountries((int) Configuration::get('PS_LANG_DEFAULT')));

        $currencies = array_map(function ($currency) {
            return [
                'id' => $currency->iso_code,
                'name' => $currency->name,
            ];
        }, Currency::getCurrencies(true, false, true));

        $languages = array_map(function ($language) {
            return [
                'id' => $language['iso_code'],
                'name' => $language['name'],
            ];
        }, Language::getLanguages(false));

        $statuses = array_map(function ($status) {
            return [
                'id' => $status['id_order_state'],
                'name' => $status['name'],
            ];
        }, OrderState::getOrderStates((int) $this->context->language->id));

        $categories = CategoryCore::getCategories(false, false);

        /**
         * !!! IMPORTANT !!!
         * if need to add additional configuration,
         * please remember to update the relative map on file: views/js/scalapay_admin.js
         **/
        $preHeader = '<div class="bootstrap" style="background-color: gainsboro;padding: 10px;font-weight: bold;"><ul scalapay-nav-bar class="nav nav-pills nav-justified"></div>';
        // $preHeader = '<nav class="navbar navbar-default navbar-fixed-bottom"> <div class="container-fluid"> <div class="navbar-header"> <button type="button" class="collapsed navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-7" aria-expanded="false"> <span class="sr-only">Toggle navigation</span> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span> </button> <a href="#" class="navbar-brand">Brand</a> </div> <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-7"> <ul class="nav navbar-nav"> <li class="active"><a href="#">Home</a></li> <li><a href="#">Link</a></li> <li><a href="#">Link</a></li> </ul> </div> </div> </nav>';

        $this->context->controller->addJS(
            $this->_path . '/views/js/scalapay_admin.js?version=' . $this->version
        );

        return $preHeader . $helper->generateForm(
            [
                'scalapay_common' => $this->getGeneralConfigFormCommon(),
                'scalapay_pay_in_3' => $this->getGeneralConfigForm($statuses),
                'scalapay_pay_in_3_restriction' => $this->getRestrictionConfigForm($countries, $currencies, $languages, $categories, $productLimits),
                'scalapay_pay_in_3_product_config' => $this->getProductPageConfigForm(),
                'scalapay_pay_in_3_cart_config' => $this->getCartPageConfigForm(),
                'scalapay_pay_in_3_checkout_config' => $this->getCheckoutPageConfigForm(),
                'scalapay_pay_in_4' => $this->getGeneralConfigFormPayFour($statuses),
                'scalapay_pay_in_4_restriction' => $this->getRestrictionConfigFormPayFour($countries, $currencies, $languages, $categories, $productLimits),
                'scalapay_pay_in_4_product_config' => $this->getProductPageConfigFormPayFour(),
                'scalapay_pay_in_4_cart_config' => $this->getCartPageConfigFormPayFour(),
                'scalapay_pay_in_4_checkout_config' => $this->getCheckoutPageConfigFormPayFour(),
                'scalapay_pay_later' => $this->getGeneralConfigFormLater($statuses),
                'scalapay_pay_later_restriction' => $this->getRestrictionConfigFormLater($countries, $currencies, $languages, $categories, $productLimits),
                'scalapay_pay_later_product_config' => $this->getProductPageConfigFormLater(),
                'scalapay_pay_later_cart_config' => $this->getCartPageConfigFormLater(),
                'scalapay_pay_later_checkout_config' => $this->getCheckoutPageConfigFormLater(),
                'scalapay_in_page_checkout' => $this->getGeneralConfigFormInPageCheckout(), ]
        );
    }

    protected function getGeneralConfigFormCommon()
    {
        return [
            'form' => [
                'legend' => [
                    'title' => sprintf('General Settings (%s - %s)', $this->version, _PS_VERSION_),
                    'icon' => 'icon-cogs',
                ],
                'input' => [
                    [
                        'col' => 3,
                        'type' => 'text',
                        'name' => self::SCALAPAY_TEST_KEY,
                        'label' => 'Test API Key',
                        'required' => true,
                    ],
                    [
                        'col' => 3,
                        'type' => 'text',
                        'name' => self::SCALAPAY_LIVE_KEY,
                        'label' => 'Live API Key',
                        'required' => true,
                    ],
                    [
                        'col' => 3,
                        'type' => 'text',
                        'readonly' => 'readonly',
                        'name' => self::SCALAPAY_TEST_URL,
                        'label' => 'Test Url',
                        'required' => true,
                    ],
                    [
                        'col' => 3,
                        'type' => 'text',
                        'readonly' => 'readonly',
                        'name' => self::SCALAPAY_LIVE_URL,
                        'label' => 'Production Url',
                        'required' => true,
                    ],

                    [
                        'col' => 3,
                        'type' => 'textarea',
                        'desc' => 'Custom CSS for Scalapay Widget on Product, Cart and Checkout pages',
                        'name' => self::SCALAPAY_CSS_LOGO_TEXT,
                        'label' => 'Custom CSS',
                    ],
                    [
                        'type' => 'switch',
                        'label' => 'Load Javascript',
                        'name' => self::SCALAPAY_ADD_WIDGET_SCRIPTS,
                        'is_bool' => true,
                        'desc' => 'Load Widget javascript on Product, Cart and Checkout pages',
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => true,
                                'label' => 'Enabled',
                            ],
                            [
                                'id' => 'active_off',
                                'value' => false,
                                'label' => 'Disabled',
                            ],
                        ],
                    ],
                    [
                        'type' => 'switch',
                        'label' => 'Enable Digital/Virtual Products',
                        'name' => self::SCALAPAY_ENABLE_VIRTUAL_PRODUCTS,
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => true,
                                'label' => 'Enabled',
                            ],
                            [
                                'id' => 'active_off',
                                'value' => false,
                                'label' => 'Disabled',
                            ],
                        ],
                    ],
                    [
                        'col' => 3,
                        'type' => 'text',
                        'label' => 'Hook Widget',
                        'name' => self::SCALAPAY_HOOK_WIDGET,
                        'required' => true,
                    ],
                ],
                'submit' => [
                    'title' => 'Save',
                ],
            ],
        ];
    }

    protected function getCartPageConfigForm()
    {
        return [
            'form' => [
                'legend' => [
                    'title' => 'Pay in 3 - Cart Page settings',
                    'icon' => 'icon-cogs',
                ],
                'input' => [
                    [
                        'col' => 3,
                        'type' => 'textarea',
                        'required' => true,
                        'name' => self::SCALAPAY_PAY_IN_3_CART_PAGE_AMOUNT_SELECTORS,
                        'label' => 'Price selectors',
                        'desc' => 'CSS selectors where to get the price. Can be a list, separated by comma. Ex: ".current-price-value",".price-selector-2" ',
                    ],

                    [
                        'col' => 3,
                        'type' => 'textarea',
                        'required' => true,
                        'label' => 'Widget Position',
                        'name' => self::SCALAPAY_PAY_IN_3_CART_PAGE_WIDGET_POSITION,
                        'desc' => 'Set Widget positions on Cart page based on classes used on front-end',
                    ],

                    [
                        'type' => 'switch',
                        'label' => 'Hide installment amount',
                        'name' => self::SCALAPAY_PAY_IN_3_CART_PAGE_HIDE_PRICE,
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => true,
                                'label' => 'Yes',
                            ],
                            [
                                'id' => 'active_off',
                                'value' => false,
                                'label' => 'No',
                            ],
                        ],
                    ],

                    [
                        'type' => 'select',
                        'lang' => true,
                        'label' => 'Currency Position',
                        'name' => self::SCALAPAY_PAY_IN_3_CART_PAGE_CURRENCY_POSITION,
                        'multiple' => false,
                        'options' => [
                            'query' => [
                                [
                                    'id' => 'after',
                                    'name' => 'After',
                                ],
                                [
                                    'id' => 'before',
                                    'name' => 'Before',
                                ],
                            ],
                            'id' => 'id',
                            'name' => 'name',
                        ],
                    ],

                    [
                        'type' => 'select',
                        'lang' => true,
                        'label' => 'Currency Display',
                        'name' => self::SCALAPAY_PAY_IN_3_CART_PAGE_CURRENCY_TYPE,
                        'multiple' => false,
                        'options' => [
                            'query' => [
                                [
                                    'id' => 'symbol',
                                    'name' => 'Symbol',
                                ],
                                [
                                    'id' => 'code',
                                    'name' => 'Code',
                                ],
                            ],
                            'id' => 'id',
                            'name' => 'name',
                        ],
                    ],
                    [
                        'col' => 3,
                        'type' => 'text',
                        'name' => self::SCALAPAY_PAY_IN_3_CART_PAGE_LOGO_SIZE,
                        'label' => 'Logo Size',
                    ],
                    [
                        'col' => 3,
                        'type' => 'textarea',
                        'name' => self::SCALAPAY_PAY_IN_3_CART_PAGE_CUSTOM_CSS,
                        'label' => 'Custom CSS',
                        'desc' => 'CSS added to the widget on the cart page',
                    ],

                    [
                        'type' => 'switch',
                        'label' => 'Hide Widget',
                        'name' => self::SCALAPAY_PAY_IN_3_CART_PAGE_HIDE_WIDGET,
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => true,
                                'label' => 'Yes',
                            ],
                            [
                                'id' => 'active_off',
                                'value' => false,
                                'label' => 'No',
                            ],
                        ],
                    ],
                ],
                'submit' => [
                    'title' => 'Save',
                ],
            ],
        ];
    }

    protected function getCheckoutPageConfigForm()
    {
        return [
            'form' => [
                'legend' => [
                    'title' => 'Pay in 3 - Checkout Page Settings',
                    'icon' => 'icon-cogs',
                ],
                'input' => [
                    [
                        'col' => 3,
                        'type' => 'textarea',
                        'required' => true,
                        'name' => self::SCALAPAY_PAY_IN_3_CHECKOUT_PAGE_AMOUNT_SELECTORS,
                        'label' => 'Price selectors',
                        'desc' => 'CSS selectors where to get the price. Can be a list, separated by comma. Ex: ".current-price-value",".price-selector-2" ',
                    ],
                    [
                        'type' => 'switch',
                        'label' => 'Show title',
                        'name' => self::SCALAPAY_PAY_IN_3_CHECKOUT_PAGE_SHOW_TITLE,
                        'is_bool' => true,
                        'values' => [
                            [
                                'value' => true,
                                'label' => 'Yes',
                            ],
                            [
                                'value' => false,
                                'label' => 'No',
                            ],
                        ],
                    ],
                    [
                        'type' => 'switch',
                        'label' => 'Hide installment amount',
                        'name' => self::SCALAPAY_PAY_IN_3_CHECKOUT_PAGE_HIDE_PRICE,
                        'is_bool' => true,
                        'values' => [
                            [
                                'value' => true,
                                'label' => 'Yes',
                            ],
                            [
                                'value' => false,
                                'label' => 'No',
                            ],
                        ],
                    ],

                    [
                        'type' => 'select',
                        'lang' => true,
                        'label' => 'Currency Position',
                        'name' => self::SCALAPAY_PAY_IN_3_CHECKOUT_PAGE_CURRENCY_POSITION,
                        'multiple' => false,
                        'options' => [
                            'query' => [
                                [
                                    'id' => 'after',
                                    'name' => 'After',
                                ],
                                [
                                    'id' => 'before',
                                    'name' => 'Before',
                                ],
                            ],
                            'id' => 'id',
                            'name' => 'name',
                        ],
                    ],

                    [
                        'type' => 'select',
                        'lang' => true,
                        'label' => 'Currency Display',
                        'name' => self::SCALAPAY_PAY_IN_3_CHECKOUT_PAGE_CURRENCY_TYPE,
                        'multiple' => false,
                        'options' => [
                            'query' => [
                                [
                                    'id' => 'symbol',
                                    'name' => 'Symbol',
                                ],
                                [
                                    'id' => 'code',
                                    'name' => 'Code',
                                ],
                            ],
                            'id' => 'id',
                            'name' => 'name',
                        ],
                    ],
                    [
                        'col' => 3,
                        'type' => 'textarea',
                        'name' => self::SCALAPAY_PAY_IN_3_CHECKOUT_PAGE_CUSTOM_CSS,
                        'label' => 'Custom CSS',
                        'desc' => 'CSS added to the widget on the checkout page',
                    ],
                ],
                'submit' => [
                    'title' => 'Save',
                ],
            ],
        ];
    }

    protected function getCheckoutPageConfigFormPayFour()
    {
        return [
            'form' => [
                'legend' => [
                    'title' => 'Pay in 4 - Checkout page settings',
                    'icon' => 'icon-cogs',
                ],
                'input' => [
                    [
                        'col' => 3,
                        'type' => 'textarea',
                        'required' => true,
                        'name' => self::SCALAPAY_PAY_IN_4_CHECKOUT_PAGE_AMOUNT_SELECTORS,
                        'label' => 'Price selectors',
                        'desc' => 'CSS selectors where to get the price. Can be a list, separated by comma. Ex: ".current-price-value",".price-selector-2" ',
                    ],
                    [
                        'type' => 'switch',
                        'label' => 'Show title',
                        'name' => self::SCALAPAY_PAY_IN_4_CHECKOUT_PAGE_SHOW_TITLE,
                        'is_bool' => true,
                        'values' => [
                            [
                                'value' => true,
                                'label' => 'Yes',
                            ],
                            [
                                'value' => false,
                                'label' => 'No',
                            ],
                        ],
                    ],
                    [
                        'type' => 'switch',
                        'label' => 'Hide installment amount',
                        'name' => self::SCALAPAY_PAY_IN_4_CHECKOUT_PAGE_HIDE_PRICE,
                        'is_bool' => true,
                        'values' => [
                            [
                                'value' => true,
                                'label' => 'Yes',
                            ],
                            [
                                'value' => false,
                                'label' => 'No',
                            ],
                        ],
                    ],

                    [
                        'type' => 'select',
                        'lang' => true,
                        'label' => 'Currency Position',
                        'name' => self::SCALAPAY_PAY_IN_4_CHECKOUT_PAGE_CURRENCY_POSITION,
                        'multiple' => false,
                        'options' => [
                            'query' => [
                                [
                                    'id' => 'after',
                                    'name' => 'After',
                                ],
                                [
                                    'id' => 'before',
                                    'name' => 'Before',
                                ],
                            ],
                            'id' => 'id',
                            'name' => 'name',
                        ],
                    ],

                    [
                        'type' => 'select',
                        'lang' => true,
                        'label' => 'Currency Display',
                        'name' => self::SCALAPAY_PAY_IN_4_CHECKOUT_PAGE_CURRENCY_TYPE,
                        'multiple' => false,
                        'options' => [
                            'query' => [
                                [
                                    'id' => 'symbol',
                                    'name' => 'Symbol',
                                ],
                                [
                                    'id' => 'code',
                                    'name' => 'Code',
                                ],
                            ],
                            'id' => 'id',
                            'name' => 'name',
                        ],
                    ],

                    [
                        'col' => 3,
                        'type' => 'textarea',
                        'name' => self::SCALAPAY_PAY_IN_4_CHECKOUT_PAGE_CUSTOM_CSS,
                        'label' => 'Custom CSS',
                        'desc' => 'CSS added to the widget on the checkout page',
                    ],
                ],
                'submit' => [
                    'title' => 'Save',
                ],
            ],
        ];
    }

    protected function getCheckoutPageConfigFormLater()
    {
        return [
            'form' => [
                'legend' => [
                    'title' => 'Pay Later - Checkout page settings',
                    'icon' => 'icon-cogs',
                ],
                'input' => [
                    [
                        'col' => 3,
                        'type' => 'textarea',
                        'required' => true,
                        'name' => self::SCALAPAY_PAY_LATER_CHECKOUT_PAGE_AMOUNT_SELECTORS,
                        'label' => 'Price selectors',
                        'desc' => 'CSS selectors where to get the price. Can be a list, separated by comma. Ex: ".current-price-value",".price-selector-2" ',
                    ],
                    [
                        'type' => 'switch',
                        'label' => 'Show title',
                        'name' => self::SCALAPAY_PAY_LATER_CHECKOUT_PAGE_SHOW_TITLE,
                        'is_bool' => true,
                        'values' => [
                            [
                                'value' => true,
                                'label' => 'Yes',
                            ],
                            [
                                'value' => false,
                                'label' => 'No',
                            ],
                        ],
                    ],

                    [
                        'col' => 3,
                        'type' => 'textarea',
                        'name' => self::SCALAPAY_PAY_LATER_CHECKOUT_PAGE_CUSTOM_CSS,
                        'label' => 'Custom CSS',
                        'desc' => 'CSS added to the widget on the checkout page',
                    ],
                ],
                'submit' => [
                    'title' => 'Save',
                ],
            ],
        ];
    }

    protected function getGeneralConfigForm(array $orderStatues)
    {
        return [
            'form' => [
                'legend' => [
                    'title' => 'Enable Pay in 3',
                    'icon' => 'icon-cogs',
                ],
                'input' => [
                    [
                        'type' => 'switch',
                        'label' => 'Enable Pay in 3',
                        'name' => self::SCALAPAY_PAY_IN_3_ENABLED,
                        'is_bool' => true,
                        'desc' => 'Pay in 3 will be Enabled in Test mode',
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => true,
                                'label' => 'Enabled',
                            ],
                            [
                                'id' => 'active_off',
                                'value' => false,
                                'label' => 'Disabled',
                            ],
                        ],
                    ],
                    [
                        'type' => 'switch',
                        'label' => 'Pay in 3 live mode',
                        'name' => self::SCALAPAY_PAY_IN_3_LIVE_MODE_ENABLED,
                        'is_bool' => true,
                        'desc' => 'Enable Pay in 3 in Live mode',
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => true,
                                'label' => 'Enabled',
                            ],
                            [
                                'id' => 'active_off',
                                'value' => false,
                                'label' => 'Disabled',
                            ],
                        ],
                    ],

                    [
                        'type' => 'select',
                        'lang' => true,
                        'label' => 'Order Status',
                        'name' => self::SCALAPAY_PAY_IN_3_ORDER_STATUS,
                        'options' => [
                            'query' => $orderStatues,
                            'id' => 'id',
                            'name' => 'name',
                        ],
                    ],
                ],
                'submit' => [
                    'title' => 'Save',
                ],
            ],
        ];
    }

    protected function getProductPageConfigFormPayFour()
    {
        return [
            'form' => [
                'legend' => [
                    'title' => 'Pay in 4 - Product page settings',
                    'icon' => 'icon-cogs',
                ],
                'input' => [
                    [
                        'col' => 3,
                        'type' => 'textarea',
                        'name' => self::SCALAPAY_PAY_IN_4_PRODUCT_PAGE_AMOUNT_SELECTORS,
                        'label' => 'Price selectors',
                        'required' => true,
                        'desc' => 'CSS selectors where to get the price. Can be a list, separated by comma. Ex: ".current-price-value",".price-selector-2" ',
                    ],
                    [
                        'type' => 'textarea',
                        'col' => 3,
                        'label' => 'Widget Position',
                        'name' => self::SCALAPAY_PAY_IN_4_PRODUCT_PAGE_WIDGET_POSITION,
                        'desc' => 'Set widget position on product page based on classes used in front-end',
                        'required' => true,
                    ],

                    [
                        'type' => 'switch',
                        'label' => 'Hide installment amount',
                        'name' => self::SCALAPAY_PAY_IN_4_PRODUCT_PAGE_HIDE_PRICE,
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => true,
                                'label' => 'Yes',
                            ],
                            [
                                'id' => 'active_off',
                                'value' => false,
                                'label' => 'No',
                            ],
                        ],
                    ],
                    [
                        'type' => 'select',
                        'lang' => true,
                        'label' => 'Currency Position',
                        'name' => self::SCALAPAY_PAY_IN_4_PRODUCT_PAGE_CURRENCY_POSITION,
                        'multiple' => false,
                        'options' => [
                            'query' => [
                                [
                                    'id' => 'after',
                                    'name' => 'After',
                                ],
                                [
                                    'id' => 'before',
                                    'name' => 'Before',
                                ],
                            ],
                            'id' => 'id',
                            'name' => 'name',
                        ],
                    ],

                    [
                        'type' => 'select',
                        'lang' => true,
                        'label' => 'Currency Display',
                        'name' => self::SCALAPAY_PAY_IN_4_PRODUCT_PAGE_CURRENCY_TYPE,
                        'multiple' => false,
                        'options' => [
                            'query' => [
                                [
                                    'id' => 'symbol',
                                    'name' => 'Symbol',
                                ],
                                [
                                    'id' => 'code',
                                    'name' => 'Code',
                                ],
                            ],
                            'id' => 'id',
                            'name' => 'name',
                        ],
                    ],
                    [
                        'col' => 3,
                        'type' => 'text',
                        'name' => self::SCALAPAY_PAY_IN_4_PRODUCT_PAGE_LOGO_SIZE,
                        'label' => 'Logo Size',
                    ],
                    [
                        'type' => 'switch',
                        'label' => 'Display Below Widget Text',
                        'name' => self::SCALAPAY_PAY_IN_4_PRODUCT_PAGE_AFTER_WIDGET_TEXT_ENABLED,
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => true,
                                'label' => 'Yes',
                            ],
                            [
                                'id' => 'active_off',
                                'value' => false,
                                'label' => 'No',
                            ],
                        ],
                    ],
                    [
                        'col' => 3,
                        'type' => 'text',
                        'lang' => true,
                        'name' => self::SCALAPAY_PAY_IN_4_PRODUCT_PAGE_AFTER_WIDGET_TEXT,
                        'label' => 'Below Widget Text',
                    ],
                    [
                        'col' => 3,
                        'type' => 'textarea',
                        'name' => self::SCALAPAY_PAY_IN_4_PRODUCT_PAGE_CUSTOM_CSS,
                        'label' => 'Custom CSS',
                        'desc' => 'CSS added to the widget on the product page',
                    ],
                    [
                        'type' => 'switch',
                        'label' => 'Hide Widget',
                        'name' => self::SCALAPAY_PAY_IN_4_PRODUCT_PAGE_HIDE_WIDGET,
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => true,
                                'label' => 'Yes',
                            ],
                            [
                                'id' => 'active_off',
                                'value' => false,
                                'label' => 'No',
                            ],
                        ],
                    ],
                ],
                'submit' => [
                    'title' => 'Save',
                ],
            ],
        ];
    }

    protected function getProductPageConfigForm()
    {
        return [
            'form' => [
                'legend' => [
                    'title' => 'Pay in 3 - Product Page settings',
                    'icon' => 'icon-cogs',
                ],
                'input' => [
                    [
                        'col' => 3,
                        'type' => 'textarea',
                        'name' => self::SCALAPAY_PAY_IN_3_PRODUCT_PAGE_AMOUNT_SELECTORS,
                        'required' => true,
                        'label' => 'Price selectors',
                        'desc' => 'CSS selectors where to get the price. Can be a list, separated by comma. Ex: ".current-price-value",".price-selector-2" ',
                    ],
                    [
                        'type' => 'textarea',
                        'col' => 3,
                        'label' => 'Widget Position',
                        'name' => self::SCALAPAY_PAY_IN_3_PRODUCT_PAGE_WIDGET_POSITION,
                        'desc' => 'Set widget positions on product page based on classes used in front-end',
                        'required' => true,
                    ],

                    [
                        'type' => 'switch',
                        'label' => 'Hide installment amount',
                        'name' => self::SCALAPAY_PAY_IN_3_PRODUCT_PAGE_HIDE_PRICE,
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => true,
                                'label' => 'Yes',
                            ],
                            [
                                'id' => 'active_off',
                                'value' => false,
                                'label' => 'No',
                            ],
                        ],
                    ],
                    [
                        'type' => 'select',
                        'lang' => true,
                        'label' => 'Currency Position',
                        'name' => self::SCALAPAY_PAY_IN_3_PRODUCT_PAGE_CURRENCY_POSITION,
                        'multiple' => false,
                        'options' => [
                            'query' => [
                                [
                                    'id' => 'after',
                                    'name' => 'After',
                                ],
                                [
                                    'id' => 'before',
                                    'name' => 'Before',
                                ],
                            ],
                            'id' => 'id',
                            'name' => 'name',
                        ],
                    ],

                    [
                        'type' => 'select',
                        'lang' => true,
                        'label' => 'Currency Display',
                        'name' => self::SCALAPAY_PAY_IN_3_PRODUCT_PAGE_CURRENCY_TYPE,
                        'multiple' => false,
                        'options' => [
                            'query' => [
                                [
                                    'id' => 'symbol',
                                    'name' => 'Symbol',
                                ],
                                [
                                    'id' => 'code',
                                    'name' => 'Code',
                                ],
                            ],
                            'id' => 'id',
                            'name' => 'name',
                        ],
                    ],
                    [
                        'col' => 3,
                        'type' => 'text',
                        'name' => self::SCALAPAY_PAY_IN_3_PRODUCT_PAGE_LOGO_SIZE,
                        'label' => 'Logo Size',
                    ],

                    [
                        'type' => 'switch',
                        'label' => 'Display Below Widget Text',
                        'name' => self::SCALAPAY_PAY_IN_3_PRODUCT_PAGE_AFTER_WIDGET_TEXT_ENABLED,
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => true,
                                'label' => 'Yes',
                            ],
                            [
                                'id' => 'active_off',
                                'value' => false,
                                'label' => 'No',
                            ],
                        ],
                    ],
                    [
                        'col' => 3,
                        'type' => 'text',
                        'lang' => true,
                        'name' => self::SCALAPAY_PAY_IN_3_PRODUCT_PAGE_AFTER_WIDGET_TEXT,
                        'label' => 'Below Widget Text',
                    ],
                    [
                        'col' => 3,
                        'type' => 'textarea',
                        'name' => self::SCALAPAY_PAY_IN_3_PRODUCT_PAGE_CUSTOM_CSS,
                        'label' => 'Custom CSS',
                        'desc' => 'CSS added to the widget on the product page',
                    ],
                    [
                        'type' => 'switch',
                        'label' => 'Hide Widget',
                        'name' => self::SCALAPAY_PAY_IN_3_PRODUCT_PAGE_HIDE_WIDGET,
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => true,
                                'label' => 'Yes',
                            ],
                            [
                                'id' => 'active_off',
                                'value' => false,
                                'label' => 'No',
                            ],
                        ],
                    ],
                ],
                'submit' => [
                    'title' => 'Save',
                ],
            ],
        ];
    }

    protected function getRestrictionConfigForm($countries, $currencies, $languages, $categories, $limits)
    {
        return [
            'form' => [
                'legend' => [
                    'title' => 'Pay in 3 - Restriction Settings',
                    'icon' => 'icon-cogs',
                ],
                'input' => [
                    [
                        'col' => 3,
                        'type' => 'text',
                        'name' => self::SCALAPAY_PAY_IN_3_MINIMUM_AMOUNT,
                        'label' => 'Scalapay Minimum Amount',
                        'required' => true,
                        'desc' => $this->getMinMaxText('online_pay-in-3', $limits),
                    ],
                    [
                        'col' => 3,
                        'type' => 'text',
                        'name' => self::SCALAPAY_PAY_IN_3_MAXIMUM_AMOUNT,
                        'label' => 'Scalapay Maximum Amount',
                        'required' => true,
                        'desc' => $this->getMinMaxText('online_pay-in-3', $limits),
                    ],
                    [
                        'type' => 'select',
                        'lang' => true,
                        'label' => 'Enable On Specific Countries',
                        'name' => self::SCALAPAY_PAY_IN_3_ALLOWED_COUNTRIES . '[]',
                        'desc' => 'Select the desired Countries',
                        'required' => true,
                        'multiple' => true,
                        'options' => [
                            'query' => $countries,
                            'id' => 'id',
                            'name' => 'name',
                        ],
                    ],
                    [
                        'type' => 'select',
                        'lang' => true,
                        'label' => 'Enable On Specific Currencies',
                        'name' => self::SCALAPAY_PAY_IN_3_ALLOWED_CURRENCIES . '[]',
                        'desc' => 'Select the desired Currencies',
                        'required' => true,
                        'multiple' => true,
                        'options' => [
                            'query' => $currencies,
                            'id' => 'id',
                            'name' => 'name',
                        ],
                    ],
                    [
                        'type' => 'select',
                        'lang' => true,
                        'label' => 'Enable On specific languages',
                        'name' => self::SCALAPAY_PAY_IN_3_ALLOWED_LANGUAGES . '[]',
                        'desc' => 'Select the desired Languages',
                        'required' => true,
                        'multiple' => true,
                        'options' => [
                            'query' => $languages,
                            'id' => 'id',
                            'name' => 'name',
                        ],
                    ],
                    [
                        'type' => 'select',
                        'lang' => true,
                        'label' => 'Restrict For Specific Categories',
                        'name' => self::SCALAPAY_PAY_IN_3_RESTRICTED_CATEGORIES . '[]',
                        'desc' => 'Select the Categories on which Pay in 3 will not appear',
                        'multiple' => true,
                        'options' => [
                            'query' => $this->buildCategoryHierarchy($categories),
                            'id' => 'id',
                            'name' => 'name',
                        ],
                    ],
                ],
                'submit' => [
                    'title' => 'Save',
                ],
            ],
        ];
    }

    protected function getCartPageConfigFormPayFour()
    {
        return [
            'form' => [
                'legend' => [
                    'title' => 'Pay in 4 - Cart page settings',
                    'icon' => 'icon-cogs',
                ],
                'input' => [
                    [
                        'col' => 3,
                        'type' => 'textarea',
                        'name' => self::SCALAPAY_PAY_IN_4_CART_PAGE_AMOUNT_SELECTORS,
                        'label' => 'Price selectors',
                        'required' => true,
                        'desc' => 'CSS selectors where to get the price. Can be a list, separated by comma. Ex: ".current-price-value",".price-selector-2" ',
                    ],
                    [
                        'col' => 3,
                        'type' => 'textarea',
                        'label' => 'Widget Position',
                        'name' => self::SCALAPAY_PAY_IN_4_CART_PAGE_WIDGET_POSITION,
                        'desc' => 'Set Widget positions on Cart page based on classes used on front-end',
                        'required' => true,
                    ],

                    [
                        'type' => 'switch',
                        'label' => 'Hide Installment Amount',
                        'name' => self::SCALAPAY_PAY_IN_4_CART_PAGE_HIDE_PRICE,
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => true,
                                'label' => 'Yes',
                            ],
                            [
                                'id' => 'active_off',
                                'value' => false,
                                'label' => 'No',
                            ],
                        ],
                    ],

                    [
                        'type' => 'select',
                        'lang' => true,
                        'label' => 'Currency Position',
                        'name' => self::SCALAPAY_PAY_IN_4_CART_PAGE_CURRENCY_POSITION,
                        'multiple' => false,
                        'options' => [
                            'query' => [
                                [
                                    'id' => 'after',
                                    'name' => 'After',
                                ],
                                [
                                    'id' => 'before',
                                    'name' => 'Before',
                                ],
                            ],
                            'id' => 'id',
                            'name' => 'name',
                        ],
                    ],

                    [
                        'type' => 'select',
                        'lang' => true,
                        'label' => 'Currency Display',
                        'name' => self::SCALAPAY_PAY_IN_4_CART_PAGE_CURRENCY_TYPE,
                        'multiple' => false,
                        'options' => [
                            'query' => [
                                [
                                    'id' => 'symbol',
                                    'name' => 'Symbol',
                                ],
                                [
                                    'id' => 'code',
                                    'name' => 'Code',
                                ],
                            ],
                            'id' => 'id',
                            'name' => 'name',
                        ],
                    ],
                    [
                        'col' => 3,
                        'type' => 'text',
                        'name' => self::SCALAPAY_PAY_IN_4_CART_PAGE_LOGO_SIZE,
                        'label' => 'Logo Size',
                    ],
                    [
                        'col' => 3,
                        'type' => 'textarea',
                        'name' => self::SCALAPAY_PAY_IN_4_CART_PAGE_CUSTOM_CSS,
                        'label' => 'Custom CSS',
                        'desc' => 'CSS added to the widget on the cart page',
                    ],
                    [
                        'type' => 'switch',
                        'label' => 'Hide Widget',
                        'name' => self::SCALAPAY_PAY_IN_4_CART_PAGE_HIDE_WIDGET,
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => true,
                                'label' => 'Yes',
                            ],
                            [
                                'id' => 'active_off',
                                'value' => false,
                                'label' => 'No',
                            ],
                        ],
                    ],
                ],
                'submit' => [
                    'title' => 'Save',
                ],
            ],
        ];
    }

    protected function getGeneralConfigFormPayFour(array $orderStatues)
    {
        return [
            'form' => [
                'legend' => [
                    'title' => 'Pay In 4',
                    'icon' => 'icon-cogs',
                ],
                'input' => [
                    [
                        'type' => 'switch',
                        'label' => 'Enable Pay in 4',
                        'name' => self::SCALAPAY_PAY_IN_4_ENABLED,
                        'is_bool' => true,
                        'desc' => 'Pay in 4 will be Enabled in Test mode',
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => true,
                                'label' => 'Enabled',
                            ],
                            [
                                'id' => 'active_off',
                                'value' => false,
                                'label' => 'Disabled',
                            ],
                        ],
                    ],
                    [
                        'type' => 'switch',
                        'label' => 'Pay in 4 live mode',
                        'name' => self::SCALAPAY_PAY_IN_4_LIVE_MODE_ENABLED,
                        'is_bool' => true,
                        'desc' => 'Enable Pay In 4 live mode',
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => true,
                                'label' => 'Enabled',
                            ],
                            [
                                'id' => 'active_off',
                                'value' => false,
                                'label' => 'Disabled',
                            ],
                        ],
                    ],
                    [
                        'type' => 'select',
                        'lang' => true,
                        'label' => 'Order Status',
                        'name' => self::SCALAPAY_PAY_IN_4_ORDER_STATUS,
                        'options' => [
                            'query' => $orderStatues,
                            'id' => 'id',
                            'name' => 'name',
                        ],
                    ],
                ],
                'submit' => [
                    'title' => 'Save',
                ],
            ],
        ];
    }

    protected function getRestrictionConfigFormPayFour($countries, $currencies, $languages, $categories, $limits)
    {
        return [
            'form' => [
                'legend' => [
                    'title' => 'Pay in 4 - Restriction Settings',
                    'icon' => 'icon-cogs',
                ],
                'input' => [
                    [
                        'col' => 3,
                        'type' => 'text',
                        'name' => self::SCALAPAY_PAY_IN_4_MINIMUM_AMOUNT,
                        'label' => 'Scalapay Minimum Amount',
                        'required' => true,
                        'desc' => $this->getMinMaxText('online_pay-in-4', $limits),
                    ],
                    [
                        'col' => 3,
                        'type' => 'text',
                        'name' => self::SCALAPAY_PAY_IN_4_MAXIMUM_AMOUNT,
                        'label' => 'Scalapay Maximum Amount',
                        'required' => true,
                        'desc' => $this->getMinMaxText('online_pay-in-4', $limits),
                    ],
                    [
                        'type' => 'select',
                        'lang' => true,
                        'label' => 'Enable On Specific Countries',
                        'name' => self::SCALAPAY_PAY_IN_4_ALLOWED_COUNTRIES . '[]',
                        'desc' => 'Select the desired Countries',
                        'required' => true,
                        'multiple' => true,
                        'options' => [
                            'query' => $countries,
                            'id' => 'id',
                            'name' => 'name',
                        ],
                    ],
                    [
                        'type' => 'select',
                        'lang' => true,
                        'label' => 'Enable On Specific Currencies',
                        'name' => self::SCALAPAY_PAY_IN_4_ALLOWED_CURRENCIES . '[]',
                        'multiple' => true,
                        'required' => true,
                        'desc' => 'Select the desired Currencies',
                        'options' => [
                            'query' => $currencies,
                            'id' => 'id',
                            'name' => 'name',
                        ],
                    ],
                    [
                        'type' => 'select',
                        'lang' => true,
                        'label' => 'Enable On Specific Languages',
                        'name' => self::SCALAPAY_PAY_IN_4_ALLOWED_LANGUAGES . '[]',
                        'desc' => 'Select the desired Languages',
                        'multiple' => true,
                        'required' => true,
                        'options' => [
                            'query' => $languages,
                            'id' => 'id',
                            'name' => 'name',
                        ],
                    ],
                    [
                        'type' => 'select',
                        'lang' => true,
                        'label' => 'Restrict For Specific Categories',
                        'name' => self::SCALAPAY_PAY_IN_4_RESTRICTED_CATEGORIES . '[]',
                        'desc' => 'Select the Categories on which Pay in 4 will not appear',
                        'multiple' => true,
                        'options' => [
                            'query' => $this->buildCategoryHierarchy($categories),
                            'id' => 'id',
                            'name' => 'name',
                        ],
                    ],
                ],
                'submit' => [
                    'title' => 'Save',
                ],
            ],
        ];
    }

    protected function getGeneralConfigFormLater(array $orderStatues)
    {
        return [
            'form' => [
                'legend' => [
                    'title' => 'Pay Later',
                    'icon' => 'icon-cogs',
                ],
                'input' => [
                    [
                        'type' => 'switch',
                        'label' => 'Enable Pay Later',
                        'name' => self::SCALAPAY_PAY_LATER_ENABLED,
                        'is_bool' => true,
                        'desc' => 'Pay Later will be Enabled in Test mode',
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => true,
                                'label' => 'Enabled',
                            ],
                            [
                                'id' => 'active_off',
                                'value' => false,
                                'label' => 'Disabled',
                            ],
                        ],
                    ],
//                    [
//                        'col' => 3,
//                        'type' => 'text',
//                        'desc' => 'Frequency Number',
//                        'name' => self::SCALAPAY_PAY_LATER_PAY_AFTER_DAYS,
//                        'required' => true,
//                        'label' => 'Frequency Number',
//
//                    ],
                    [
                        'type' => 'switch',
                        'label' => 'Pay Later live mode',
                        'name' => self::SCALAPAY_PAY_LATER_LIVE_MODE_ENABLED,
                        'is_bool' => true,
                        'desc' => 'Enable Pay Later in Live mode',
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => true,
                                'label' => 'Enabled',
                            ],
                            [
                                'id' => 'active_off',
                                'value' => false,
                                'label' => 'Disabled',
                            ],
                        ],
                    ],
                    [
                        'type' => 'select',
                        'lang' => true,
                        'label' => 'Order Status',
                        'name' => self::SCALAPAY_PAY_LATER_ORDER_STATUS,
                        'options' => [
                            'query' => $orderStatues,
                            'id' => 'id',
                            'name' => 'name',
                        ],
                    ],
                ],
                'submit' => [
                    'title' => 'Save',
                ],
            ],
        ];
    }

    protected function getProductPageConfigFormLater()
    {
        return [
            'form' => [
                'legend' => [
                    'title' => 'Pay Later - Product page settings',
                    'icon' => 'icon-cogs',
                ],
                'input' => [
                    [
                        'col' => 3,
                        'type' => 'textarea',
                        'name' => self::SCALAPAY_PAY_LATER_PRODUCT_PAGE_AMOUNT_SELECTORS,
                        'label' => 'Price selectors',
                        'required' => true,
                        'desc' => 'CSS selectors where to get the price. Can be a list, separated by comma. Ex: ".current-price-value",".price-selector-2" ',
                    ],
                    [
                        'type' => 'textarea',
                        'col' => 3,
                        'label' => 'Widget Position',
                        'name' => self::SCALAPAY_PAY_LATER_PRODUCT_PAGE_WIDGET_POSITION,
                        'desc' => 'Set Widget positions on product page based on classes used in front-end',
                        'required' => true,
                    ],

                    [
                        'col' => 3,
                        'type' => 'text',
                        'name' => self::SCALAPAY_PAY_LATER_PRODUCT_PAGE_LOGO_SIZE,
                        'label' => 'Logo Size',
                    ],
                    [
                        'type' => 'select',
                        'lang' => true,
                        'label' => 'Theme',
                        'name' => self::SCALAPAY_PAY_LATER_PRODUCT_PAGE_THEME,
                        'multiple' => false,
                        'options' => [
                            'query' => [
                                [
                                    'id' => 'primary',
                                    'name' => 'Primary',
                                ],
                                [
                                    'id' => 'variant',
                                    'name' => 'Variant',
                                ],
                            ],
                            'id' => 'id',
                            'name' => 'name',
                        ],
                    ],
                    [
                        'col' => 3,
                        'type' => 'textarea',
                        'name' => self::SCALAPAY_PAY_LATER_PRODUCT_PAGE_CUSTOM_CSS,
                        'label' => 'Custom CSS',
                        'desc' => 'CSS added to the widget on the product page',
                    ],
                    [
                        'type' => 'switch',
                        'label' => 'Hide Widget',
                        'name' => self::SCALAPAY_PAY_LATER_PRODUCT_PAGE_HIDE_WIDGET,
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => true,
                                'label' => 'Yes',
                            ],
                            [
                                'id' => 'active_off',
                                'value' => false,
                                'label' => 'No',
                            ],
                        ],
                    ],
                ],
                'submit' => [
                    'title' => 'Save',
                ],
            ],
        ];
    }

    protected function getCartPageConfigFormLater()
    {
        return [
            'form' => [
                'legend' => [
                    'title' => 'Pay Later - Cart page settings',
                    'icon' => 'icon-cogs',
                ],
                'input' => [
                    [
                        'col' => 3,
                        'type' => 'textarea',
                        'name' => self::SCALAPAY_PAY_LATER_CART_PAGE_AMOUNT_SELECTORS,
                        'label' => 'Price selectors',
                        'required' => true,
                        'desc' => 'CSS selectors where to get the price. Can be a list, separated by comma. Ex: ".current-price-value",".price-selector-2" ',
                    ],

                    [
                        'col' => 3,
                        'type' => 'textarea',
                        'label' => 'Widget Position',
                        'name' => self::SCALAPAY_PAY_LATER_CART_PAGE_WIDGET_POSITION,
                        'desc' => 'Set Widget positions on Cart page based on classes used on front-end',
                        'required' => true,
                    ],

                    [
                        'col' => 3,
                        'type' => 'text',
                        'name' => self::SCALAPAY_PAY_LATER_CART_PAGE_LOGO_SIZE,
                        'label' => 'Logo Size',
                    ],
                    [
                        'type' => 'select',
                        'lang' => true,
                        'label' => 'Theme',
                        'name' => self::SCALAPAY_PAY_LATER_CART_PAGE_THEME,
                        'multiple' => false,
                        'options' => [
                            'query' => [
                                [
                                    'id' => 'primary',
                                    'name' => 'Primary',
                                ],
                                [
                                    'id' => 'variant',
                                    'name' => 'Variant',
                                ],
                            ],
                            'id' => 'id',
                            'name' => 'name',
                        ],
                    ],
                    [
                        'col' => 3,
                        'type' => 'textarea',
                        'name' => self::SCALAPAY_PAY_LATER_CART_PAGE_CUSTOM_CSS,
                        'label' => 'Custom CSS',
                        'desc' => 'CSS added to the widget on the cart page',
                    ],
                    [
                        'type' => 'switch',
                        'label' => 'Hide Widget',
                        'name' => self::SCALAPAY_PAY_LATER_CART_PAGE_HIDE_WIDGET,
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => true,
                                'label' => 'Yes',
                            ],
                            [
                                'id' => 'active_off',
                                'value' => false,
                                'label' => 'No',
                            ],
                        ],
                    ],
                ],
                'submit' => [
                    'title' => 'Save',
                ],
            ],
        ];
    }

    protected function getRestrictionConfigFormLater($countries, $currencies, $languages, $categories, $limits)
    {
        return [
            'form' => [
                'legend' => [
                    'title' => 'Pay Later - Restriction Settings',
                    'icon' => 'icon-cogs',
                ],
                'input' => [
                    [
                        'col' => 3,
                        'type' => 'text',
                        'name' => self::SCALAPAY_PAY_LATER_MINIMUM_AMOUNT,
                        'label' => 'Scalapay Minimum Amount',
                        'required' => true,
                        'desc' => $this->getMinMaxText('online_later', $limits),
                    ],
                    [
                        'col' => 3,
                        'type' => 'text',
                        'name' => self::SCALAPAY_PAY_LATER_MAXIMUM_AMOUNT,
                        'label' => 'Scalapay Maximum Amount',
                        'required' => true,
                        'desc' => $this->getMinMaxText('online_later', $limits),
                    ],
                    [
                        'type' => 'select',
                        'lang' => true,
                        'label' => 'Enable On Specific Countries',
                        'desc' => 'Select the desired Countries',
                        'name' => self::SCALAPAY_PAY_LATER_ALLOWED_COUNTRIES . '[]',
                        'multiple' => true,
                        'required' => true,
                        'options' => [
                            'query' => $countries,
                            'id' => 'id',
                            'name' => 'name',
                        ],
                    ],
                    [
                        'type' => 'select',
                        'lang' => true,
                        'label' => 'Enable On Specific Currencies',
                        'desc' => 'Select the desired Currencies',
                        'name' => self::SCALAPAY_PAY_LATER_ALLOWED_CURRENCIES . '[]',
                        'multiple' => true,
                        'required' => true,
                        'options' => [
                            'query' => $currencies,
                            'id' => 'id',
                            'name' => 'name',
                        ],
                    ],
                    [
                        'type' => 'select',
                        'lang' => true,
                        'label' => 'Enable On Specific Languages',
                        'name' => self::SCALAPAY_PAY_LATER_ALLOWED_LANGUAGES . '[]',
                        'desc' => 'Select the desired Languages',
                        'multiple' => true,
                        'required' => true,
                        'options' => [
                            'query' => $languages,
                            'id' => 'id',
                            'name' => 'name',
                        ],
                    ],
                    [
                        'type' => 'select',
                        'lang' => true,
                        'label' => 'Restrict For Specific Categories',
                        'desc' => 'Select the Categories on which Pay Later will not appear',
                        'name' => self::SCALAPAY_PAY_LATER_RESTRICTED_CATEGORIES . '[]',
                        'multiple' => true,
                        'options' => [
                            'query' => $this->buildCategoryHierarchy($categories),
                            'id' => 'id',
                            'name' => 'name',
                        ],
                    ],
                ],
                'submit' => [
                    'title' => 'Save',
                ],
            ],
        ];
    }

    protected function getGeneralConfigFormInPageCheckout()
    {
        return [
            'form' => [
                'legend' => [
                    'title' => 'In Page Checkout',
                    'icon' => 'icon-cogs',
                ],
                'input' => [
                    [
                        'type' => 'switch',
                        'label' => 'Enable In Page Checkout',
                        'name' => self::SCALAPAY_IN_PAGE_CHECKOUT_ENABLE,
                        'is_bool' => true,
                        'desc' => 'Enable in page checkout mode',
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => true,
                                'label' => 'Enabled',
                            ],
                            [
                                'id' => 'active_off',
                                'value' => false,
                                'label' => 'Disabled',
                            ],
                        ],
                    ],

                    [
                        'col' => 3,
                        'type' => 'textarea',
                        'name' => self::SCALAPAY_IN_PAGE_CHECKOUT_PLACE_ORDER_SELECTOR,
                        'label' => 'Checkout Place Order Button Selector',
                        'required' => true,
                        'desc' => 'Insert the checkout place order button selector enclosed by double quotes. Example: "#payment-confirmation button[type=\'submit\']"',
                    ],

                    [
                        'col' => 3,
                        'type' => 'textarea',
                        'name' => self::SCALAPAY_IN_PAGE_CHECKOUT_AGREEMENT_SELECTORS,
                        'label' => 'Checkout Required Agreement Selectors',
                        'required' => false,
                        'desc' => 'Insert the checkout required agreement selectors putting each one in double quotes separated by comma. Example: "input[name=\'conditions_to_approve[terms-and-conditions]\']", "input#agreement-two"',
                    ],

                    [
                        'col' => 3,
                        'type' => 'textarea',
                        'name' => self::SCALAPAY_IN_PAGE_CHECKOUT_PLACE_ORDER_WRAPPER_STYLE,
                        'label' => 'Scalapay Place Order Button Wrapper Style',
                        'required' => false,
                        'desc' => 'Insert inline css to the wrapper element inside the "style" attribute. Example: border: 1px solid #000000; padding: 10px;',
                    ],
                ],
                'submit' => [
                    'title' => 'Save',
                ],
            ],
        ];
    }

    /**
     * @param string $product
     * @param array $limits
     *
     * @return string
     */
    protected function getMinMaxText($product, array $limits)
    {
        if (!isset($limits[$product])) {
            return '';
        }

        return sprintf($this->l('Insert a value between %s and %s'), $limits[$product]['min'] ?: 0, $limits[$product]['max'] ?: 0);
    }

    protected function getConfigFormValues()
    {
        $languages = Language::getLanguages(false) ?: [];
        $config = [];
        foreach ($this->configs as $key) {
            switch ($key) {
                case self::SCALAPAY_PAY_IN_3_ALLOWED_COUNTRIES:
                case self::SCALAPAY_PAY_IN_3_ALLOWED_CURRENCIES:
                case self::SCALAPAY_PAY_IN_3_ALLOWED_LANGUAGES:
                case self::SCALAPAY_PAY_IN_4_ALLOWED_COUNTRIES:
                case self::SCALAPAY_PAY_IN_4_ALLOWED_CURRENCIES:
                case self::SCALAPAY_PAY_IN_4_ALLOWED_LANGUAGES:
                case self::SCALAPAY_PAY_LATER_ALLOWED_COUNTRIES:
                case self::SCALAPAY_PAY_LATER_ALLOWED_CURRENCIES:
                case self::SCALAPAY_PAY_LATER_ALLOWED_LANGUAGES:
                    $config["{$key}[]"] = explode(',', Configuration::get($key) ?: '');
                    break;
                case self::SCALAPAY_PAY_IN_3_RESTRICTED_CATEGORIES:
                case self::SCALAPAY_PAY_IN_4_RESTRICTED_CATEGORIES:
                case self::SCALAPAY_PAY_LATER_RESTRICTED_CATEGORIES:
                    $config["{$key}[]"] = json_decode(Configuration::get($key) ?: '', true);
                    break;
                case self::SCALAPAY_PAY_IN_3_PRODUCT_PAGE_AFTER_WIDGET_TEXT:
                case self::SCALAPAY_PAY_IN_4_PRODUCT_PAGE_AFTER_WIDGET_TEXT:
                    $config[$key] = array_reduce($languages, function ($result, $lang) use ($key) {
                        $result[$lang['id_lang']] = Configuration::get($key, $lang['id_lang']) ?: '';

                        return $result;
                    }, []);
                    break;

                default:
                    $config[$key] = Configuration::get($key);
            }
        }

        return $config;
    }

    protected function postProcess()
    {
        $languages = LanguageCore::getLanguages(true);
        foreach ($this->configs as $key) {
            switch ($key) {
                case self::SCALAPAY_HOOK_WIDGET:
                    $this->unregisterHook(Configuration::get($key));
                    $this->registerHook(Tools::getvalue($key));
                    Configuration::updateValue($key, Tools::getValue($key));
                    break;

                case self::SCALAPAY_PAY_IN_3_ALLOWED_COUNTRIES:
                case self::SCALAPAY_PAY_IN_3_ALLOWED_CURRENCIES:
                case self::SCALAPAY_PAY_IN_3_ALLOWED_LANGUAGES:
                case self::SCALAPAY_PAY_IN_4_ALLOWED_COUNTRIES:
                case self::SCALAPAY_PAY_IN_4_ALLOWED_CURRENCIES:
                case self::SCALAPAY_PAY_IN_4_ALLOWED_LANGUAGES:
                case self::SCALAPAY_PAY_LATER_ALLOWED_COUNTRIES:
                case self::SCALAPAY_PAY_LATER_ALLOWED_CURRENCIES:
                case self::SCALAPAY_PAY_LATER_ALLOWED_LANGUAGES:
                    Configuration::updateValue($key, implode(',', Tools::getvalue($key) ?: []));
                    break;
                case self::SCALAPAY_PAY_IN_3_RESTRICTED_CATEGORIES:
                case self::SCALAPAY_PAY_IN_4_RESTRICTED_CATEGORIES:
                case self::SCALAPAY_PAY_LATER_RESTRICTED_CATEGORIES:
                    Configuration::updateValue($key, json_encode(Tools::getvalue($key) ?: []));
                    break;
                case self::SCALAPAY_PAY_IN_3_PRODUCT_PAGE_AFTER_WIDGET_TEXT:
                case self::SCALAPAY_PAY_IN_4_PRODUCT_PAGE_AFTER_WIDGET_TEXT:
                    Configuration::updateValue($key, array_reduce($languages, function ($result, $lang) use ($key) {
                        $result[$lang['id_lang']] = Tools::getValue("{$key}_{$lang['id_lang']}");

                        return $result;
                    }, []));
                    break;

                default:
                    Configuration::updateValue($key, Tools::getValue($key));
            }
        }
    }

    public function hookDisplayPaymentReturn($params)
    {
        return $this->hookPaymentReturn($params);
    }

    public function hookPaymentReturn($params)
    {
        /* @var Order $order */

        if (isset($params['order'])) {
            $order = $params['order'];
        } else {
            $order = $params['objOrder'];
        }

        $this->context->smarty->assign([
            'total_payed' => Tools::displayPrice(
                $order->getOrdersTotalPaid(),
                new Currency($order->id_currency),
                false
            ),
            'order_reference' => $order->reference ?: 'NA',
            'shop_name' => $this->context->shop->name,
            'scalapay_order_token' => Tools::getValue('scalapay_order_token'),
            'scalapay_method' => $order->payment,
        ]);

        return $this->context->smarty->fetch(_PS_MODULE_DIR_ . $this->name . '/views/templates/hook/paymentReturn.tpl');
    }

    public function hookDisplayShoppingCart()
    {
        return $this->getCookieErrorMessage();
    }

    public function hookDisplayCheckoutSummaryTop()
    {
        return $this->getCookieErrorMessage();
    }

    public function hookDisplayPayment()
    {
        $this->context->smarty->assign([
            'scalapayPayment' => [
                'logo' => _PS_BASE_URL_ . __PS_BASE_URI__ . 'modules/scalapay/views/img/logo.svg',
                'payments' => $this->getPaymentInfos(),
            ],
        ]);

        return $this->context->smarty->fetch(_PS_MODULE_DIR_ . $this->name . '/views/templates/hook/displayPayment.tpl');
    }

    public function hookActionPresentPaymentOptions($params)
    {
        //        $params["paymentOptions"] = array_merge(
        //            $params["paymentOptions"]
        //            //[$this->name => $this->createPaymentOptions($this->getPaymentInfos())]
        //        );
    }

    public function hookPaymentOptions($params)
    {
        return $this->createPaymentOptions($this->getPaymentInfos());
    }

    protected function getPaymentInfos()
    {
        // do not show if virtual product
        if (!Configuration::get(self::SCALAPAY_ENABLE_VIRTUAL_PRODUCTS)
            && array_reduce($this->context->cart->getProducts(true), function ($result, array $product) {
                return $result || $product['is_virtual'];
            }, false)) {
            return [];
        }

        return array_filter([
            $this->payIn3PaymentOption(),
            $this->payIn4PaymentOption(),
            $this->payLaterPaymentOption(),
        ]);
    }

    protected function createPaymentOptions($payments)
    {
        $return = [];
        foreach ($payments as $payment) {
            // !!! IMPORTANT !!! NOT IMPORT THE CLASS WITH use
            $return[] = (new \PrestaShop\PrestaShop\Core\Payment\PaymentOption())
                ->setModuleName($this->name)
                ->setCallToActionText($payment['callToActionText'])
                ->setLogo($payment['logo'])
                ->setAction($payment['action'])
                ->setAdditionalInformation($payment['additionalInformation']);
        }

        return $return;
    }

    public function hookActionAdminControllerSetMedia()
    {
        $controller = Tools::getValue('controller');

        if ($controller == 'AdminOrders'
            && ($orderId = Tools::getValue('id_order'))
            && $this->getScalapayTransactionInfo($orderId)) {
            Media::addJsDefL('chb_scalapay_refund', $this->l('Refund via Scalapay'));
            $this->context->controller->addJS($this->_path . '/views/js/scalapay_refund.js?version=' . $this->version);
        }
    }

    public function displayWidget()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return '';
        }

        $html = '';

        $config = [];

        $config['enabled'] = Configuration::get(self::SCALAPAY_IN_PAGE_CHECKOUT_ENABLE);
        $config['cdnJsUrl'] = self::IN_PAGE_CHECKOUT_CDN_JS;
        $config['cdnHtmlUrl'] = self::IN_PAGE_CHECKOUT_CDN_HTML;
        $config['paymentSelectors'] = self::IN_PAGE_CHECKOUT_PAYMENT_SELECTORS;
        $config['agreementSelectors'] = Configuration::get(self::SCALAPAY_IN_PAGE_CHECKOUT_AGREEMENT_SELECTORS);
        $config['checkoutPlaceOrderButtonSelector'] = Configuration::get(self::SCALAPAY_IN_PAGE_CHECKOUT_PLACE_ORDER_SELECTOR);
        $config['placeOrderStyle'] = Configuration::get(self::SCALAPAY_IN_PAGE_CHECKOUT_PLACE_ORDER_WRAPPER_STYLE);
        $config['ajaxMode'] = 'post';
        $config['ajaxContentTypeHeader'] = 'application/x-www-form-urlencoded';
        $config['ajaxController'] = $this->context->link->getModuleLink(
            $this->name,
            'validation',
            ['token' => Tools::getToken(false)],
            true
        );

        $this->context->smarty->assign(['scalapay_in_page_checkout' => $config]);

        $html .= $this->context->smarty->fetch(_PS_MODULE_DIR_ . $this->name . '/views/templates/hook/displayHeader.tpl');

        $smartData = [
            'scalapay' => [
                'requireScripts' => Configuration::get(self::SCALAPAY_ADD_WIDGET_SCRIPTS),
                'css' => Configuration::get(self::SCALAPAY_CSS_LOGO_TEXT),
                'widgets' => [],
            ],
        ];

        if (Tools::getValue('controller') === 'product'
            && ($productId = Tools::getValue('id_product'))
            && ($product = new Product($productId)) // @phpstan-ignore-line
            && Validate::isLoadedObject($product)) {
            $smartData['scalapay']['widgets'] = $this->configScalapayWidgetsInProductPage($product);
        }

        if (Tools::getValue('controller') == 'cart'
            // DO NOT MOVE OUTSIDE with `use Scalapay\ScalapayCompatibility`.
            || ((new \Scalapay\ScalapayCompatibility())->isCartControllerChanged(_PS_VERSION_) && Tools::getValue('controller') == 'order')
            || (version_compare(_PS_VERSION_, '1.7.0', '<') // @phpstan-ignore-line
                && Tools::getValue('controller') == 'order' && Tools::getValue('step', null) === null)
        ) {
            $smartData['scalapay']['widgets'] = $this->configScalapayWidgetsInCartPage($this->context->cart);
        }

        $this->context->smarty->assign($smartData);

        $html .= $this->context->smarty->fetch(_PS_MODULE_DIR_ . $this->name . '/views/templates/hook/displayDynamicHook.tpl');

        return $html;
    }

    public function hookDisplayInvoice()
    {
        $message = [];
        $refundStatus = $this->context->cookie->scalapay_status_refund;
        unset($this->context->cookie->scalapay_status_refund);
        $this->context->cookie->write();

        if ($refundStatus === 'failed') {
            $message['error'] = $this->l('Unfortunately, your attempt to refund the payment on Scalapay side failed. Retry it using the merchant portal.');
        }

        if ($refundStatus === 'success') {
            $message['success'] = $this->l('Refund successfully completed on Scalapay side.');
        }

        $this->context->smarty->assign([
            'scalapayMessage' => $message,
        ]);

        return $this->context->smarty->fetch(_PS_MODULE_DIR_ . $this->name . '/views/templates/hook/displayInvoice.tpl');
    }

    public function hookActionOrderSlipAdd($params)
    {
        /** @var OrderCore $order */
        $order = $params['order'];

        if (!Tools::isSubmit('refundWithScalapay')
            || !($transactionInfo = $this->getScalapayTransactionInfo($order->id))) {
            return false;
        }

        $amount = 0;

        foreach ($params['productList'] as $product) {
            $amount += round($product['amount'], 2);
        }

        $shippingAmount = isset(Tools::getValue('cancel_product')['shipping_amount']) ? Tools::getValue('cancel_product')['shipping_amount'] : 0;
        $amount += $shippingAmount ?: Tools::getValue('partialRefundShippingCost') ?: 0;

        $amount = sprintf('%0.2f', $amount);

        $allowedAmount = ($transactionInfo['payed_amount'] ?: 0) - ($transactionInfo['refund_amount'] ?: 0);

        if ($amount > $allowedAmount) {
            $this->context->cookie->scalapay_status_refund = 'failed';
            $this->context->cookie->write();
            PrestaShopLogger::addLog(
                "Amount greater that maximal refundable. (Amount: $amount vs Allowed: $allowedAmount)",
                2,
                null,
                'Order',
                $order->id,
                true
            );

            return false;
        }

        $request = [
            'refundAmount' => [
                'amount' => $amount,
                'currency' => (new CurrencyCore($order->id_currency))->iso_code,
            ],
            'merchantReference' => (string) $order->id,
            'merchantRefundReference' => 'order_#' . $order->id . '_orderSlip_#' . $this->getOrderSlipId($order->id),
        ];

        $result = $this->doRequest($transactionInfo['product'], 'POST', "/v2/payments/{$transactionInfo['scalapay_tid']}/refund", $request);

        if ($result['info']['http_code'] !== 200) {
            $this->context->cookie->scalapay_status_refund = 'failed';
            $this->context->cookie->write();
            PrestaShopLogger::addLog(
                sprintf("Trying to refund, Scalapay returned an invalid http code: %s.\nRequest:\n%s\n\nResponse:\n%s", $result['info']['http_code'], json_encode($request), json_encode($result)),
                3,
                null,
                'Order',
                $order->id,
                true
            );

            return false;
        }

        $this->updateTransactionWithRefundAmount($transactionInfo['scalapay_tid'], (float) $amount);

        $this->context->cookie->scalapay_status_refund = 'success';
        $this->context->cookie->write();

        PrestaShopLogger::addLog(
            "Order correctly refunded on Scalapay side. (Refund token: {$result['data']['token']})",
            1,
            null,
            'Order',
            $order->id,
            true
        );

        $partialRefundStatus = Configuration::get('PS_CHECKOUT_STATE_PARTIAL_REFUND')
            // fallback
            ?: Configuration::get('PS_OS_REFUND');

        $order->setCurrentState($amount != $allowedAmount ? (int) $partialRefundStatus : (int) Configuration::get('PS_OS_REFUND'));
        $order->save();

        return true;
    }

    protected function configScalapayWidgetsInCartPage(CartCore $cart)
    {
        // check if the virtual product are disabled and if this product is virtual
        if (!Configuration::get(self::SCALAPAY_ENABLE_VIRTUAL_PRODUCTS) && array_reduce($cart->getProducts(), function ($result, array $product) {
            return $result || $product['is_virtual'];
        }, false)) {
            return [];
        }

        return array_filter([
            'payIn3' => $this->payIn3CartData($cart),
            'payIn4' => $this->payIn4CartData($cart),
            'payLater' => $this->payLaterCartData($cart),
        ]);
    }

    protected function configScalapayWidgetsInProductPage(ProductCore $product)
    {
        // check if the virtual product are disabled and if this product is virtual
        if (!Configuration::get(self::SCALAPAY_ENABLE_VIRTUAL_PRODUCTS) && $product->is_virtual) {
            return [];
        }

        return array_filter([
            'payIn3' => $this->payIn3ProductData($product),
            'payIn4' => $this->payIn4ProductData($product),
            'payLater' => $this->payLaterProductData($product),
        ]);
    }

    protected function payIn3ProductData(ProductCore $product)
    {
        if (!$this->isPayIn3Enabled([$product->id])
            || Configuration::get(self::SCALAPAY_PAY_IN_3_PRODUCT_PAGE_HIDE_WIDGET)) {
            return [];
        }

        return [
            'type' => 'product',
            'style' => Configuration::get(self::SCALAPAY_PAY_IN_3_PRODUCT_PAGE_CUSTOM_CSS),
            'min' => Configuration::get(self::SCALAPAY_PAY_IN_3_MINIMUM_AMOUNT),
            'max' => Configuration::get(self::SCALAPAY_PAY_IN_3_MAXIMUM_AMOUNT),
            'logoSize' => Configuration::get(self::SCALAPAY_PAY_IN_3_PRODUCT_PAGE_LOGO_SIZE),
            'amountSelectors' => '[' . (Configuration::get(self::SCALAPAY_PAY_IN_3_PRODUCT_PAGE_AMOUNT_SELECTORS) ?: '') . ']',
            'hidePrice' => Configuration::get(self::SCALAPAY_PAY_IN_3_PRODUCT_PAGE_HIDE_PRICE) ? 'true' : 'false',
            'locale' => $this->context->language->iso_code,
            'numberOfInstallments' => 3,
            'frequencyNumber' => 30,
            'currencyPosition' => Configuration::get(self::SCALAPAY_PAY_IN_3_PRODUCT_PAGE_CURRENCY_POSITION),
            'currencyDisplay' => Configuration::get(self::SCALAPAY_PAY_IN_3_PRODUCT_PAGE_CURRENCY_TYPE),
            'afterWidgetText' => Configuration::get(self::SCALAPAY_PAY_IN_3_PRODUCT_PAGE_AFTER_WIDGET_TEXT_ENABLED) && Configuration::get(self::SCALAPAY_PAY_IN_3_PRODUCT_PAGE_AFTER_WIDGET_TEXT, $this->context->language->id),
            'position' => Configuration::get(self::SCALAPAY_PAY_IN_3_PRODUCT_PAGE_WIDGET_POSITION) ?: '.current-price',
        ];
    }

    protected function payIn3CartData(CartCore $cart)
    {
        if (!$this->isPayIn3Enabled(array_map(function (array $product) {
            return $product['id_product'];
        }, $cart->getProducts()))
            || Configuration::get(self::SCALAPAY_PAY_IN_3_CART_PAGE_HIDE_WIDGET)) {
            return [];
        }

        return [
            'type' => 'cart',
            'style' => Configuration::get(self::SCALAPAY_PAY_IN_3_CART_PAGE_CUSTOM_CSS),
            'min' => Configuration::get(self::SCALAPAY_PAY_IN_3_MINIMUM_AMOUNT),
            'max' => Configuration::get(self::SCALAPAY_PAY_IN_3_MAXIMUM_AMOUNT),
            'logoSize' => Configuration::get(self::SCALAPAY_PAY_IN_3_CART_PAGE_LOGO_SIZE),
            'amountSelectors' => '[' . (Configuration::get(self::SCALAPAY_PAY_IN_3_CART_PAGE_AMOUNT_SELECTORS) ?: '') . ']',
            'hidePrice' => Configuration::get(self::SCALAPAY_PAY_IN_3_CART_PAGE_HIDE_PRICE) ? 'true' : 'false',
            'locale' => $this->context->language->iso_code,
            'numberOfInstallments' => 3,
            'frequencyNumber' => 30,
            'currencyPosition' => Configuration::get(self::SCALAPAY_PAY_IN_3_CART_PAGE_CURRENCY_POSITION),
            'currencyDisplay' => Configuration::get(self::SCALAPAY_PAY_IN_3_CART_PAGE_CURRENCY_TYPE),
            'position' => Configuration::get(self::SCALAPAY_PAY_IN_3_CART_PAGE_WIDGET_POSITION) ?: '.cart-detailed-totals',
        ];
    }

    protected function payIn4ProductData(ProductCore $product)
    {
        if (!$this->isPayIn4Enabled([$product->id])
            || Configuration::get(self::SCALAPAY_PAY_IN_4_PRODUCT_PAGE_HIDE_WIDGET)) {
            return [];
        }

        return [
            'type' => 'product',
            'style' => Configuration::get(self::SCALAPAY_PAY_IN_4_PRODUCT_PAGE_CUSTOM_CSS),
            'min' => Configuration::get(self::SCALAPAY_PAY_IN_4_MINIMUM_AMOUNT),
            'max' => Configuration::get(self::SCALAPAY_PAY_IN_4_MAXIMUM_AMOUNT),
            'logoSize' => Configuration::get(self::SCALAPAY_PAY_IN_4_PRODUCT_PAGE_LOGO_SIZE),
            'amountSelectors' => '[' . (Configuration::get(self::SCALAPAY_PAY_IN_4_PRODUCT_PAGE_AMOUNT_SELECTORS) ?: '') . ']',
            'hidePrice' => Configuration::get(self::SCALAPAY_PAY_IN_4_PRODUCT_PAGE_HIDE_PRICE) ? 'true' : 'false',
            'locale' => $this->context->language->iso_code,
            'numberOfInstallments' => 4,
            'frequencyNumber' => 30,
            'currencyPosition' => Configuration::get(self::SCALAPAY_PAY_IN_4_PRODUCT_PAGE_CURRENCY_POSITION),
            'currencyDisplay' => Configuration::get(self::SCALAPAY_PAY_IN_4_PRODUCT_PAGE_CURRENCY_TYPE),
            'afterWidgetText' => Configuration::get(self::SCALAPAY_PAY_IN_4_PRODUCT_PAGE_AFTER_WIDGET_TEXT_ENABLED) && Configuration::get(self::SCALAPAY_PAY_IN_4_PRODUCT_PAGE_AFTER_WIDGET_TEXT, $this->context->language->id),
            'position' => Configuration::get(self::SCALAPAY_PAY_IN_4_PRODUCT_PAGE_WIDGET_POSITION) ?: '.current-price',
        ];
    }

    protected function payIn4CartData(CartCore $cart)
    {
        if (!$this->isPayIn4Enabled(array_map(function (array $product) {
            return $product['id_product'];
        }, $cart->getProducts()))
            || Configuration::get(self::SCALAPAY_PAY_IN_4_CART_PAGE_HIDE_WIDGET)) {
            return [];
        }

        return [
            'type' => 'cart',
            'style' => Configuration::get(self::SCALAPAY_PAY_IN_4_CART_PAGE_CUSTOM_CSS),
            'min' => Configuration::get(self::SCALAPAY_PAY_IN_4_MINIMUM_AMOUNT),
            'max' => Configuration::get(self::SCALAPAY_PAY_IN_4_MAXIMUM_AMOUNT),
            'logoSize' => Configuration::get(self::SCALAPAY_PAY_IN_4_CART_PAGE_LOGO_SIZE),
            'amountSelectors' => '[' . (Configuration::get(self::SCALAPAY_PAY_IN_4_CART_PAGE_AMOUNT_SELECTORS) ?: '') . ']',
            'hidePrice' => Configuration::get(self::SCALAPAY_PAY_IN_4_CART_PAGE_HIDE_PRICE) ? 'true' : 'false',
            'locale' => $this->context->language->iso_code,
            'numberOfInstallments' => 4,
            'frequencyNumber' => 30,
            'currencyPosition' => Configuration::get(self::SCALAPAY_PAY_IN_4_CART_PAGE_CURRENCY_POSITION),
            'currencyDisplay' => Configuration::get(self::SCALAPAY_PAY_IN_4_CART_PAGE_CURRENCY_TYPE),
            'position' => Configuration::get(self::SCALAPAY_PAY_IN_4_CART_PAGE_WIDGET_POSITION) ?: '.cart-summary-line.cart-total',
        ];
    }

    protected function payLaterProductData(ProductCore $product)
    {
        if (!$this->isPayLaterEnabled([$product->id])
            || Configuration::get(self::SCALAPAY_PAY_LATER_PRODUCT_PAGE_HIDE_WIDGET)) {
            return [];
        }

        return [
            'type' => 'product',
            'style' => Configuration::get(self::SCALAPAY_PAY_LATER_PRODUCT_PAGE_CUSTOM_CSS),
            'min' => Configuration::get(self::SCALAPAY_PAY_LATER_MINIMUM_AMOUNT),
            'max' => Configuration::get(self::SCALAPAY_PAY_LATER_MAXIMUM_AMOUNT),
            'logoSize' => Configuration::get(self::SCALAPAY_PAY_LATER_PRODUCT_PAGE_LOGO_SIZE),
            'amountSelectors' => '[' . (Configuration::get(self::SCALAPAY_PAY_LATER_PRODUCT_PAGE_AMOUNT_SELECTORS) ?: '') . ']',
            'locale' => $this->context->language->iso_code,
            'numberOfInstallments' => 1,
            'frequencyNumber' => Configuration::get(self::SCALAPAY_PAY_LATER_PAY_AFTER_DAYS),
            'currencyPosition' => 'after',
            'currencyDisplay' => '',
            'afterWidgetText' => '',
            'position' => Configuration::get(self::SCALAPAY_PAY_LATER_PRODUCT_PAGE_WIDGET_POSITION) ?: 'div.product-add-to-cart',
            'theme' => Configuration::get(self::SCALAPAY_PAY_LATER_PRODUCT_PAGE_THEME),
        ];
    }

    protected function payLaterCartData(CartCore $cart)
    {
        if (!$this->isPayLaterEnabled(array_map(function (array $product) {
            return $product['id_product'];
        }, $cart->getProducts()))
            || Configuration::get(self::SCALAPAY_PAY_LATER_CART_PAGE_HIDE_WIDGET)) {
            return [];
        }

        return [
            'type' => 'cart',
            'style' => Configuration::get(self::SCALAPAY_PAY_LATER_CART_PAGE_CUSTOM_CSS),
            'min' => Configuration::get(self::SCALAPAY_PAY_LATER_MINIMUM_AMOUNT),
            'max' => Configuration::get(self::SCALAPAY_PAY_LATER_MAXIMUM_AMOUNT),
            'logoSize' => Configuration::get(self::SCALAPAY_PAY_LATER_CART_PAGE_LOGO_SIZE),
            'amountSelectors' => '[' . (Configuration::get(self::SCALAPAY_PAY_LATER_CART_PAGE_AMOUNT_SELECTORS) ?: '') . ']',
            'locale' => $this->context->language->iso_code,
            'numberOfInstallments' => 1,
            'frequencyNumber' => Configuration::get(self::SCALAPAY_PAY_LATER_PAY_AFTER_DAYS),
            'currencyPosition' => 'after',
            'currencyDisplay' => '',
            'afterWidgetText' => '',
            'position' => Configuration::get(self::SCALAPAY_PAY_LATER_CART_PAGE_WIDGET_POSITION) ?: '.checkout',
            'theme' => Configuration::get(self::SCALAPAY_PAY_LATER_CART_PAGE_THEME),
        ];
    }

    protected function isPayIn4Enabled(array $productsIds)
    {
        return Configuration::get(self::SCALAPAY_PAY_IN_4_ENABLED)
            && in_array((new CurrencyCore($this->context->cookie->id_currency))->iso_code, explode(',', Configuration::get(self::SCALAPAY_PAY_IN_4_ALLOWED_CURRENCIES)))
            && in_array($this->context->language->iso_code, explode(',', Configuration::get(self::SCALAPAY_PAY_IN_4_ALLOWED_LANGUAGES)))
            && $this->areProductsEnabledForCategories(json_decode(Configuration::get(self::SCALAPAY_PAY_IN_4_RESTRICTED_CATEGORIES), true), $productsIds);
    }

    protected function isPayIn3Enabled(array $productIds)
    {
        return Configuration::get(self::SCALAPAY_PAY_IN_3_ENABLED)
            && in_array($this->context->currency->iso_code, explode(',', Configuration::get(self::SCALAPAY_PAY_IN_3_ALLOWED_CURRENCIES)))
            && in_array($this->context->language->iso_code, explode(',', Configuration::get(self::SCALAPAY_PAY_IN_3_ALLOWED_LANGUAGES)))
            && $this->areProductsEnabledForCategories(json_decode(Configuration::get(self::SCALAPAY_PAY_IN_3_RESTRICTED_CATEGORIES), true), $productIds);
    }

    protected function isPayLaterEnabled(array $productIds)
    {
        return Configuration::get(self::SCALAPAY_PAY_LATER_ENABLED)
            && in_array((new CurrencyCore($this->context->cookie->id_currency))->iso_code, explode(',', Configuration::get(self::SCALAPAY_PAY_LATER_ALLOWED_CURRENCIES)))
            && in_array($this->context->language->iso_code, explode(',', Configuration::get(self::SCALAPAY_PAY_LATER_ALLOWED_LANGUAGES)))
            && $this->areProductsEnabledForCategories(json_decode(Configuration::get(self::SCALAPAY_PAY_LATER_RESTRICTED_CATEGORIES), true), $productIds);
    }

    protected function areProductsEnabledForCategories($categoryIds = [], $productIds = [])
    {
        if (empty($categoryIds) || empty($productIds)) {
            return true;
        }

        $categories = [];
        // get all sub-categories
        foreach ($categoryIds as $value) {
            foreach ((new Category($value))->getSubCategories($this->context->language->id) as $arr) {
                $categories[] = $arr['id_category'];
            }
        }

        $categories = array_merge($categories, $categoryIds);

        foreach ($productIds as $productId) {
            // Todo: $intersect variable needed for compatibility with php v5.4
            $intersect = array_intersect(Product::getProductCategories($productId), $categories);
            if (!empty($intersect)) {
                return false;
            }
        }

        return true;
    }

    private function getScalapayTransactionInfo($id_order)
    {
        return Db::getInstance()->getRow((new DbQuery())->select('*')
            ->from(self::SCALAPAY_DB)
            ->where('order_id = ' . (int) $id_order));
    }

    protected function buildCategoryHierarchy($categories = [], $currentCategoryId = 0)
    {
        $hierarchyCategories = [];

        if (!isset($categories[$currentCategoryId])) {
            return $hierarchyCategories;
        }

        foreach ($categories[$currentCategoryId] as $category) {
            if (($deepLevel = $category['infos']['level_depth']) > 1) {
                $hierarchyCategories[] = [
                    'id' => $category['infos']['id_category'],
                    'name' => str_repeat('-', $deepLevel - 2) . ' ' . $category['infos']['name'],
                ];
            }

            $hierarchyCategories = array_merge(
                $hierarchyCategories,
                $this->buildCategoryHierarchy($categories, $category['infos']['id_category'])
            );
        }

        return $hierarchyCategories;
    }

    protected function installDb()
    {
        return Db::getInstance()->Execute(
            'CREATE TABLE IF NOT EXISTS ' . _DB_PREFIX_ . self::SCALAPAY_DB . ' 
            (
                `id`				INT(11) NOT NULL AUTO_INCREMENT,
                `order_id`			INT(11) NOT NULL,
                `product`           VARCHAR(15) default "-",
                `scalapay_tid`		VARCHAR(15) NOT NULL,
                `payed_amount`		DECIMAL(20, 2) NOT NULL,
                `captured`		    INT(1) default 0,
                `refund_amount`     DECIMAL(20, 2),
                
                `payed_at`			DATETIME NOT NULL,
                `refund_at`         DATETIME,
                PRIMARY KEY			(`id`)
                ) ENGINE=InnoDB		DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;'
        );
    }

    protected function payIn3PaymentOption()
    {
        /* Cart information */
        $orderTotal = $this->context->cart->getOrderTotal();
        $deliveryAddressCountryIso = CountryCore::getIsoById((new Address((int) $this->context->cart->id_address_invoice))->id_country ?: 0);

        if (!$this->isPayIn3Enabled(array_map(function (array $product) {
            return $product['id_product'];
        }, $this->context->cart->getProducts()))
            || $orderTotal < Configuration::get(self::SCALAPAY_PAY_IN_3_MINIMUM_AMOUNT)
            || $orderTotal > Configuration::get(self::SCALAPAY_PAY_IN_3_MAXIMUM_AMOUNT)
            || ($deliveryAddressCountryIso && !in_array($deliveryAddressCountryIso, explode(',', Configuration::get(self::SCALAPAY_PAY_IN_3_ALLOWED_COUNTRIES))))
        ) {
            return null;
        }

        $templateData = [
            'scalapayWidget' => [
                'productType' => self::PRODUCT_PAY_IN_3,
                'frequencyNumber' => 30,
                'numberOfInstallments' => 3,
                'locale' => $this->context->language->iso_code,
                'css' => Configuration::get(self::SCALAPAY_PAY_IN_3_CHECKOUT_PAGE_CUSTOM_CSS),
                'amount' => $orderTotal,
                'amountSelectors' => Configuration::get(self::SCALAPAY_PAY_IN_3_CHECKOUT_PAGE_AMOUNT_SELECTORS),
                'showTitle' => Configuration::get(self::SCALAPAY_PAY_IN_3_CHECKOUT_PAGE_SHOW_TITLE),
                'hidePrice' => Configuration::get(self::SCALAPAY_PAY_IN_3_CHECKOUT_PAGE_HIDE_PRICE) ? 'true' : 'false',
                'currencyPosition' => Configuration::get(self::SCALAPAY_PAY_IN_3_CHECKOUT_PAGE_CURRENCY_POSITION),
                'currencyDisplay' => Configuration::get(self::SCALAPAY_PAY_IN_3_CHECKOUT_PAGE_CURRENCY_TYPE),
            ],
        ];

        $this->context->smarty->assign($templateData);

        // set data for payment option to display

        return [
            'action' => $this->context->link->getModuleLink($this->name, 'validation', ['product' => self::PRODUCT_PAY_IN_3], true),
            'callToActionText' => sprintf($this->l('Pay in %s installments'), 3),
            'logo' => _MODULE_DIR_ . $this->name . '/views/img/logo.svg',
            'additionalInformation' => $this->context->smarty->fetch(_PS_MODULE_DIR_ . $this->name . '/views/templates/front/scalapayWidget.tpl'),
        ];
    }

    protected function payIn4PaymentOption()
    {
        $orderTotal = $this->context->cart->getOrderTotal();
        $deliveryAddressCountryIso = CountryCore::getIsoById((new Address((int) $this->context->cart->id_address_invoice))->id_country ?: 0);

        if (!$this->isPayIn4Enabled(array_map(function (array $product) {
            return $product['id_product'];
        }, $this->context->cart->getProducts()))
            || $orderTotal < Configuration::get(self::SCALAPAY_PAY_IN_4_MINIMUM_AMOUNT)
            || $orderTotal > Configuration::get(self::SCALAPAY_PAY_IN_4_MAXIMUM_AMOUNT)
            || !in_array($deliveryAddressCountryIso, explode(',', Configuration::get(self::SCALAPAY_PAY_IN_4_ALLOWED_COUNTRIES)))
        ) {
            return null;
        }

        $this->context->smarty->assign(
            [
                'scalapayWidget' => [
                    'productType' => self::PRODUCT_PAY_IN_4,
                    'frequencyNumber' => 30,
                    'numberOfInstallments' => 4,
                    'locale' => $this->context->language->iso_code,
                    'css' => Configuration::get(self::SCALAPAY_PAY_IN_4_CHECKOUT_PAGE_CUSTOM_CSS),
                    'amount' => $orderTotal,
                    'showTitle' => Configuration::get(self::SCALAPAY_PAY_IN_4_CHECKOUT_PAGE_SHOW_TITLE),
                    'hidePrice' => Configuration::get(self::SCALAPAY_PAY_IN_4_CHECKOUT_PAGE_HIDE_PRICE) ? 'true' : 'false',
                    'amountSelectors' => Configuration::get(self::SCALAPAY_PAY_IN_4_CHECKOUT_PAGE_AMOUNT_SELECTORS),
                    'currencyPosition' => Configuration::get(self::SCALAPAY_PAY_IN_4_CHECKOUT_PAGE_CURRENCY_POSITION),
                    'currencyDisplay' => Configuration::get(self::SCALAPAY_PAY_IN_4_CHECKOUT_PAGE_CURRENCY_TYPE),
                ],
            ]
        );

        return [
            'action' => $this->context->link->getModuleLink($this->name, 'validation', ['product' => self::PRODUCT_PAY_IN_4], true),
            'callToActionText' => sprintf($this->l('Pay in %s installments'), 4),
            'logo' => _MODULE_DIR_ . $this->name . '/views/img/logo.svg',
            'additionalInformation' => $this->context->smarty->fetch(_PS_MODULE_DIR_ . $this->name . '/views/templates/front/scalapayWidget.tpl'),
        ];
    }

    protected function payLaterPaymentOption()
    {
        /* Cart information */
        $orderTotal = $this->context->cart->getOrderTotal();
        $deliveryAddressCountryIso = Country::getIsoById((new Address((int) $this->context->cart->id_address_invoice))->id_country ?: 0);

        if (!$this->isPayLaterEnabled(array_map(function (array $product) {
            return $product['id_product'];
        }, $this->context->cart->getProducts()))
            || $orderTotal < Configuration::get(self::SCALAPAY_PAY_LATER_MINIMUM_AMOUNT)
            || $orderTotal > Configuration::get(self::SCALAPAY_PAY_LATER_MAXIMUM_AMOUNT)
            || !in_array($deliveryAddressCountryIso, explode(',', Configuration::get(self::SCALAPAY_PAY_LATER_ALLOWED_COUNTRIES)))
        ) {
            return null;
        }

        $this->context->smarty->assign(
            [
                'scalapayWidget' => [
                    'productType' => self::PRODUCT_PAY_LATER,
                    'css' => Configuration::get(self::SCALAPAY_PAY_LATER_CHECKOUT_PAGE_CUSTOM_CSS),
                    'amount' => $orderTotal,
                    'numberOfInstallments' => 1,
                    'frequencyNumber' => Configuration::get(self::SCALAPAY_PAY_LATER_PAY_AFTER_DAYS),
                    'locale' => $this->context->language->iso_code,
                    'showTitle' => Configuration::get(self::SCALAPAY_PAY_LATER_CHECKOUT_PAGE_SHOW_TITLE),
                    'amountSelectors' => Configuration::get(self::SCALAPAY_PAY_LATER_CHECKOUT_PAGE_AMOUNT_SELECTORS),
                ],
            ]
        );

        return [
            'action' => $this->context->link->getModuleLink($this->name, 'validation', ['product' => self::PRODUCT_PAY_LATER], true),
            'callToActionText' => $this->l('Try first, pay later'),
            'logo' => _MODULE_DIR_ . $this->name . '/views/img/logo.svg',
            'additionalInformation' => $this->context->smarty->fetch(_PS_MODULE_DIR_ . $this->name . '/views/templates/front/scalapayWidget.tpl'),
        ];
    }

    protected function getCookieErrorMessage()
    {
        $failedMessage = $this->context->cookie->__get(self::ERROR_MESSAGE_KEY);
        $this->context->cookie->__unset(self::ERROR_MESSAGE_KEY);

        if ($failedMessage) {
            return $this->displayError($this->l($failedMessage));
        }

        return '';
    }

    protected function updateTransactionWithRefundAmount($transactionId, $amount)
    {
        return Db::getInstance()->Execute(sprintf('UPDATE ' . _DB_PREFIX_ . self::SCALAPAY_DB . ' set `refund_amount` = IFNULL(`refund_amount`, 0) + ' . $amount . ', `refund_at`= NOW()
            where `scalapay_tid`="%s" ', pSQL($transactionId)));
    }

    /**
     * Get Currency ISO Code by ID
     * Compatibility with PS1.6
     *
     * @param int $id
     *
     * @return string
     */
    protected function getIsoCodeById($id)
    {
        return Db::getInstance((bool) _PS_USE_SQL_SLAVE_)->getValue('SELECT `iso_code` FROM ' . _DB_PREFIX_ . 'currency WHERE `id_currency` = ' . (int) $id);
    }

    public function __call($name, $params)
    {
        if (strtolower($name) !== strtolower(sprintf('hook%s', Configuration::get(self::SCALAPAY_HOOK_WIDGET)))) {
            return null;
        }

        return $this->displayWidget();
    }

    private function getOrderSlipId($orderId)
    {
        try {
            $query = (new DbQuery())
                ->select('id_order_slip')
                ->from('order_slip')
                ->where('id_order = ' . $orderId)
                ->orderBy('id_order_slip DESC');
            $result = Db::getInstance((bool) _PS_USE_SQL_SLAVE_)->getRow($query);

            return !empty($result['id_order_slip']) ? (string) $result['id_order_slip'] : '';
        } catch (Exception $exception) {
            return '';
        }
    }
}
