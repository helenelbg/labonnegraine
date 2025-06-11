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

class Psaffiliate extends Module
{
    protected static $config_prefix = 'PSAFF_';
    public $secondaryControllers;

    const ADDONS_API = 'https://api.addons.prestashop.com';

    public function __construct()
    {
        $this->name = 'psaffiliate';
        $this->tab = 'advertising_marketing';
        $this->version = '1.6.14';
        $this->author = 'Active Design';
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
        $this->need_instance = 0;
        $this->module_key = '7b2f06c363c6b53d93d78b51cf5df405';
        $this->author_address = '0xc0D7cE57752e47305707d7174B9686C0Afb229c3';
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('PS Affiliate');
        $this->description = $this->l('PS Affiliate is an affiliate tracking system which allows Prestashop store owners to easily deploy a full-fledged affiliate program. It is ridiculously easy to install, configure and use, has all the features and flexibility you\'d need, and runs by itself - just deploy it and let it make you money.');

        $this->secondaryControllers = array(
            'AdminPsaffiliateAdmin' => $this->l('Dashboard'),
            'AdminPsaffiliateConfiguration' => $this->l('Configuration'),
            'AdminPsaffiliateAffiliates' => $this->l('Affiliates'),
            'AdminPsaffiliateCustomFields' => $this->l('Affiliates Custom Fields'),
            'AdminPsaffiliatePayments' => $this->l('Payments'),
            'AdminPsaffiliatePaymentMethods' => $this->l('Payment Methods'),
            'AdminPsaffiliateBanners' => $this->l('Banners'),
            'AdminPsaffiliateTexts' => $this->l('Text Ads'),
            'AdminPsaffiliateRates' => $this->l('General Commission Rates'),
            'AdminPsaffiliateCategoryRates' => $this->l('Category Commission Rates'),
            'AdminPsaffiliateProductRates' => $this->l('Product Commission Rates'),
            'AdminPsaffiliateTraffic' => $this->l('Traffic'),
            'AdminPsaffiliateSales' => $this->l('Sales'),
            'AdminPsaffiliateCampaigns' => $this->l('Campaigns'),
            'AdminPsaffiliateStatistics' => $this->l('Statistics'),
        );

        $this->controllers = array('myaccount', 'requestpayment', 'texts', 'banners', 'campaign');
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        include(dirname(__FILE__).'/sql/install.php');
        if (Tools::version_compare(_PS_VERSION_, '1.7', '>=')) {
            $hookForProductPage = 'displayProductButtons';
        } else {
            $hookForProductPage = 'displayRightColumnProduct';
        }

        return parent::install() &&
        $this->addBackOfficeTabs() &&
        $this->registerHook('displayBackOfficeHeader') &&
        $this->registerHook('displayCustomerAccount') &&
        $this->registerHook('actionValidateOrder') &&
        $this->registerHook('actionOrderStatusUpdate') &&
        $this->registerHook('displayHeader') &&
        $this->registerHook($hookForProductPage) &&
        $this->registerHook('actionCustomerAccountAdd') &&
        $this->registerHook('displayAdminCustomers') &&
        $this->registerHook('actionCartSave') &&
        $this->installAjaxAdminController();
    }

    public function uninstall()
    {
        include(dirname(__FILE__).'/sql/uninstall.php');

        return parent::uninstall() && $this->deleteBackOfficeTabs();
    }

    public function addBackOfficeTabs()
    {
        $tab = new Tab;

        $tab->class_name = "PsaffiliateAdmin";
        $tab->id_parent = 0;
        $tab->module = $this->name;
        $tab->name[(int)(Configuration::get('PS_LANG_DEFAULT'))] = $this->displayName;
        if (!$tab->add()) {
            return false;
        }

        $primaryTabId = Tab::getIdFromClassName('PsaffiliateAdmin');
        if ($primaryTabId) {
            foreach ($this->secondaryControllers as $class_name => $name) {
                $tab = new Tab;

                $tab->class_name = $class_name;
                $tab->id_parent = $primaryTabId;
                $tab->module = $this->name;
                $tab->name[(int)(Configuration::get('PS_LANG_DEFAULT'))] = $name;
                if (!$tab->add()) {
                    return false;
                }
            }

            return true;
        }

        return false;
    }

    public function deleteBackOfficeTabs()
    {
        $tab = new Tab(Tab::getIdFromClassName('PsaffiliateAdmin'));
        if (!$tab->delete()) {
            return false;
        }

        foreach (array_keys($this->secondaryControllers) as $class_name) {
            $tab = new Tab(Tab::getIdFromClassName($class_name));
            $tab->delete();
        }

        return true;
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitPsaffiliateModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfiguration(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getConfigForm()));
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        $validateFields = $this->validateFields();
        if (!$validateFields) {
            $form_values = $this->getConfiguration();
            $db = Db::getInstance();
            $data = array();
            $i = 0;
            foreach (array_keys($form_values) as $key) {
                $data[$i]['name'] = pSQL($key);
                $data[$i]['value'] = pSQL(Tools::getValue($key, null));
                $i++;
            }

            return (bool)$db->insert('aff_configuration', $data, true, false, Db::REPLACE);
        } else {
            return $validateFields;
        }
    }

    public function validateFields()
    {
        $validate = true;
        $errors = array();
        foreach ($this->getConfigForm() as $configForm) {
            foreach ($configForm['input'] as $input) {
                $name = $input['name'];
                $label = $input['label'];
                if (isset($input['validate'])) {
                    $validate = $input['validate'];
                    if (!Validate::{$validate}(pSQL(Tools::getValue($name)))) {
                        $errors[$name] = $this->generateError($label, $validate);
                    }
                }
            }
        }

        return implode("<br />", $errors);
    }

    public function generateError($label = false, $validate = false)
    {
        if ($label && $validate) {
            switch ($validate) {
                case 'isFloat':
                    return sprintf($this->l('The field "%s" has to be a float value, separated by dot (".")'), $label);
                default:
                    return sprintf($this->l('The field "%1$s" is not validating the rule "%2$s"'), $label, $validate);
            }
        } else {
            return $this->l('Unknown error');
        }
    }

    public function hookDisplayBackOfficeHeader()
    {
        $this->context->controller->addCSS($this->_path.'views/css/back.css');

        if ($this->showDiscover()) {
            $this->context->controller->addCSS($this->getPathUri().'views/css/discover.css');
            $this->context->controller->addJS($this->getPathUri().'views/js/discover.js');
        }
    }

    public function hookDisplayHeader()
    {
        $this->startTracking();
        if (isset($this->context->controller->module->name) && $this->context->controller->module->name == $this->name) {
            $this->context->controller->addJS($this->_path.'views/js/front.js');
            $this->context->controller->addJS($this->_path.'views/js/clipboard.min.js');
            $this->context->controller->addCSS($this->_path.'views/css/front.css');
            if (Tools::version_compare(_PS_VERSION_, '1.7', '>=')) {
                $this->context->controller->addCSS($this->_path.'views/css/front_ps17.css');
            }

            if (Tools::version_compare(_PS_VERSION_, '1.6', '<')) {
                $this->context->controller->addCSS($this->_path.'views/css/bootstrap.modified.min.css');
                $this->context->controller->addJS($this->_path.'views/js/bootstrap.min.js');
                $this->context->controller->addJS($this->_path.'views/js/jquery.ui.tooltip.min.js');
            }
        }
        if (isset($this->context->controller->php_self) && $this->context->controller->php_self == 'product') {
            $this->context->controller->addJS($this->_path.'views/js/clipboard.min.js');
            $this->context->controller->addCSS($this->_path.'views/css/product.css');
            $this->context->controller->addJS($this->_path.'views/js/product.js');
        }
    }

    public function hookDisplayCustomerAccount()
    {
        $is_affiliate = $this->isAffiliate();
        $this->context->smarty->assign('isAffiliate', $is_affiliate);

        if (!$is_affiliate) {
            $is_group_allowed = self::isGroupAllowed($this->context->customer->id_default_group);
            if (!$is_group_allowed) {
                return false;
            }
        }

        if (Tools::version_compare(_PS_VERSION_, '1.7', '<')) {
            return $this->display(__FILE__, 'views/templates/front/my-account.tpl');
        } else {
            return $this->display(__FILE__, 'views/templates/front/ps17/my-account.tpl');
        }
    }

    public function hookActionValidateOrder($params)
    {
        // Exception for Amazon Order import module
        if (get_class($params['cart']) == 'AmazonCart') {
            return;
        }
        $this->loadClasses('AffConf');
        $checkSessionAffiliate = true;
        $has_affiliate = false;
        $is_lifetime_affiliate = false;
        if (AffConf::getConfig('commissions_for_life') && $id_affiliate_lifetime = $this->customerHasLifetimeAffiliate((int)$params['cart']->id_customer)) {
            $id_affiliate = $id_affiliate_lifetime;
            $has_affiliate = true;
            $is_lifetime_affiliate = true;
            $id_campaign = 0;
            if (!AffConf::getConfig('override_commissions_for_life')) {
                $checkSessionAffiliate = false;
            }
        }
        if ($checkSessionAffiliate && $this->hasSessionAffiliate()) {
            $id_affiliate = (int)$this->context->cookie->id_session_affiliate;
            $has_affiliate = true;
            $id_campaign = (int)$this->context->cookie->id_session_campaign;
        }
        if(!$has_affiliate) {
            $cart_data = self::getCartAffiliate($params['cart']->id);
            if($cart_data) {
                $id_affiliate = (int)$cart_data['id_affiliate'];
                $id_campaign = (int)$cart_data['id_campaign'];
                $has_affiliate = true;
            }
        }
        if ($has_affiliate) {
            $this->loadClasses(array('Sale', 'Campaign'));
            $sale = new Sale;
            $sale->id_affiliate = $id_affiliate;
            $sale->id_campaign = $id_campaign;
            $sale->id_order = (int)$params['order']->id;
            $sale->approved = 0;
            $sale->commission = $this->calculateCommission($params['order'], $id_affiliate, $is_lifetime_affiliate);
            $sale->date = date('Y-m-d H:i:s');
            if ($sale->add() && $id_campaign) {
                Campaign::setLastActive($id_campaign);
            }
        }
        /* Associate the affiliate as a lifetime one for this customer */
        if ($this->hasSessionAffiliate() && AffConf::getConfig('commissions_for_life') && !$this->customerHasLifetimeAffiliate((int)$params['cart']->id_customer)) {
            $this->associateCustomerToAffiliate((int)$params['cart']->id_customer, $id_affiliate);
        }
    }

    public function hookActionOrderStatusUpdate($params)
    {
        if ($params && isset($params['newOrderStatus']) && isset($params['id_order'])) {
            $id_status = $params['newOrderStatus']->id;
            $id_order = $params['id_order'];
            $this->loadClasses('AffConf');
            $affConf = new AffConf;
            $status_approve_commission = $affConf->getConfig('order_states_approve[]');
            $status_cancel_commission = $affConf->getConfig('order_states_cancel[]');
            if (in_array($id_status, $status_approve_commission)) {
                if (Db::getInstance()->getValue("SELECT COUNT(*) FROM `"._DB_PREFIX_."aff_sales` WHERE `id_order`='".(int)$id_order."'")) {
                    Db::getInstance()->execute("UPDATE `"._DB_PREFIX_."aff_sales` SET `approved` = '1' WHERE `id_order`='".(int)$id_order."' LIMIT 1;");
                }
            } elseif (in_array($id_status, $status_cancel_commission)) {
                if (Db::getInstance()->getValue("SELECT COUNT(*) FROM `"._DB_PREFIX_."aff_sales` WHERE `id_order`='".(int)$id_order."'")) {
                    Db::getInstance()->execute("UPDATE `"._DB_PREFIX_."aff_sales` SET `approved` = '0' WHERE `id_order`='".(int)$id_order."' LIMIT 1;");
                }
            }
        }
    }

    public function hookActionCustomerAccountAdd($params)
    {
        $this->loadClasses(array('Affiliate', 'AffConf'));
        if (AffConf::getConfig('new_customers_affiliates_directly') && isset($params['newCustomer']) && Validate::isLoadedObject($params['newCustomer'])) {
            $newCustomer = $params['newCustomer'];
            $affiliate = new Affiliate;
            $affiliate->id_customer = $newCustomer->id;
            $affiliate->active = (int)!AffConf::getConfig('affiliates_require_approval');
            $affiliate->has_been_reviewed = (int)!AffConf::getConfig('affiliates_require_approval');
            $affiliate->add();
        }

        if (AffConf::getConfig('commissions_for_life') && AffConf::getConfig('commissions_for_life_at_registration') && self::hasSessionAffiliate()) {
            $id_customer = $params['newCustomer']->id;
            $id_affiliate = (int)$this->context->cookie->id_session_affiliate;

            self::associateCustomerToAffiliate($id_customer, $id_affiliate);
        }
    }

    public function hookDisplayProductButtons($params)
    {
        if (!empty($params['product']) && $this->isAffiliate()) {
            if (is_object($params['product'])) {
                $id_product = (int)$params['product']->id;
            } else {
                $id_product = (int)$params['product']['id_product'];
            }
            $id_affiliate = $this->getAffiliateId();
            $this->context->smarty->assign(array(
                'product_affiliate_link' => $this->getAffiliateLink($id_affiliate, $id_product),
                'product_commision' => $this->formatProductRates($this->getRatesForProduct($id_product, $id_affiliate)),
            ));
            if (Tools::version_compare(_PS_VERSION_, '1.7', '>=')) {
                return $this->display(__FILE__, 'views/templates/front/ps17/product_buttons.tpl');
            } else {
                return $this->display(__FILE__, 'views/templates/front/product_buttons.tpl');
            }
        }
    }

    public function hookDisplayRightColumnProduct($params)
    {
        /*if (Tools::getValue('id_product') && $this->isAffiliate()) {
            $id_product = (int)Tools::getValue('id_product');
            $id_affiliate = $this->getAffiliateId();
            $this->context->smarty->assign(array(
                'product_affiliate_link' => $this->getAffiliateLink($id_affiliate, $id_product),
                'product_commision' => $this->formatProductRates($this->getRatesForProduct($id_product, $id_affiliate)),
            ));
            if (Tools::version_compare(_PS_VERSION_, '1.7', '>=')) {
                return $this->display(__FILE__, 'views/templates/front/ps17/product_buttons.tpl');
            } else {
                return $this->display(__FILE__, 'views/templates/front/product_buttons.tpl');
            }
        }*/
    }

    public function hookDisplayAdminCustomers($params)
    {
        $id_customer = (int)$params['id_customer'];
        $lifetime_affiliate_id = self::customerHasLifetimeAffiliate($id_customer);
        $lifetime_affiliate_name = "";
        if ($lifetime_affiliate_id) {
            $lifetime_affiliate_name = self::getAffiliateName($lifetime_affiliate_id);
        }
        $commissions_generated = self::getCustomerCommissionsGenerated($id_customer, 100);
        $this->context->smarty->assign(array(
            'id_affiliate' => self::getAffiliateId($id_customer),
            'lifetime_affiliate_id' => $lifetime_affiliate_id,
            'lifetime_affiliate_name' => $lifetime_affiliate_name,
            'commissions_generated' => $commissions_generated,
        ));

        return $this->display(__FILE__, 'views/templates/admin/customer_view.tpl');
    }

    public function calculateCommission($order, $id_affiliate = false, $is_lifetime_affiliate = false)
    {
        if ($order && $id_affiliate) {
            if (is_numeric($order)) {
                $order = new Order($order);
            }
            $this->loadClasses(array('Affiliate', 'AffConf'));
            $aff = new Affiliate($id_affiliate);
            if (Validate::isLoadedObject($aff)) {
                $commission_for_products = $this->getCommissionForProducts($order->getProducts(), $aff->id, $order->id_currency);
                $with_taxes = AffConf::getConfig('include_tax_rules');
                $total = 0;
                if (AffConf::getConfig('include_cart_rules')) {
                    if ($with_taxes) {
                        $total -= Tools::convertPrice($order->total_discounts_tax_incl, $order->id_currency, false);
                    } else {
                        $total -= Tools::convertPrice($order->total_discounts_tax_excl, $order->id_currency, false);
                    }
                }
                if (AffConf::getConfig('include_shipping_tax')) {
                    if ($with_taxes) {
                        $total += Tools::convertPrice($order->total_shipping_tax_incl, $order->id_currency, false);
                    } else {
                        $total += Tools::convertPrice($order->total_shipping_tax_excl, $order->id_currency, false);
                    }
                }
                $per_sale_value = $aff->per_sale;
                if (AffConf::getConfig('general_rate_value_per_product')) {
                    $per_sale_value = 0;
                }
                $calculatedCommission = $per_sale_value + ($total * ($aff->per_sale_percent / 100)) + $commission_for_products;

                $id_customer = (int)$order->id_customer;
                $first_order_multiplier = (float)AffConf::getConfig('first_order_multiplier');
                /* If customer makes his first order, multiply the commission of the affiliate */
                if ($id_customer && $first_order_multiplier != 1 && !self::customerHasOtherOrdersExcept(
                        $id_customer,
                        $order->id
                    )
                ) {
                    $calculatedCommission *= $first_order_multiplier;
                }
                if ($is_lifetime_affiliate) {
                    $commission_for_life_multiplier = (float)AffConf::getConfig('commission_for_life_multiplier');
                    if ($commission_for_life_multiplier) {
                        $calculatedCommission *= $commission_for_life_multiplier;
                    }
                }

                //$decimals = (int)Configuration::get('PS_PRICE_DISPLAY_PRECISION');
                $decimals = 2;
                $calculatedCommission = Tools::ps_round($calculatedCommission, $decimals);

                return $calculatedCommission;
            }
        }

        return 0;
    }

    public function calculateCommission_debug($order, $id_affiliate = false, $is_lifetime_affiliate = false)
    {
        if ($order && $id_affiliate) {
            if (is_numeric($order)) {
                $order = new Order($order);
            }
            $this->loadClasses(array('Affiliate', 'AffConf'));
            $aff = new Affiliate($id_affiliate);
            if (Validate::isLoadedObject($aff)) {
                $commission_for_products = $this->getCommissionForProducts_debug($order->getProducts(), $aff->id, $order->id_currency);
                $with_taxes = AffConf::getConfig('include_tax_rules');
                $total = 0;
                if (AffConf::getConfig('include_cart_rules')) {
                    if ($with_taxes) {
                        $total -= Tools::convertPrice($order->total_discounts_tax_incl, $order->id_currency, false);
                    } else {
                        $total -= Tools::convertPrice($order->total_discounts_tax_excl, $order->id_currency, false);
                    }
                }
                if (AffConf::getConfig('include_shipping_tax')) {
                    if ($with_taxes) {
                        $total += Tools::convertPrice($order->total_shipping_tax_incl, $order->id_currency, false);
                    } else {
                        $total += Tools::convertPrice($order->total_shipping_tax_excl, $order->id_currency, false);
                    }
                }
                echo 'total : '.$total.'<br />';
                $per_sale_value = $aff->per_sale;
                if (AffConf::getConfig('general_rate_value_per_product')) {
                    $per_sale_value = 0;
                }
                $calculatedCommission = $per_sale_value + ($total * ($aff->per_sale_percent / 100)) + $commission_for_products;
                echo $calculatedCommission .' = '.$per_sale_value.' + ('.$total.' * ('.$aff->per_sale_percent.' / 100)) + '.$commission_for_products.'<br />';
                echo 'calculatedCommission : '.$calculatedCommission.'<br />';
                $id_customer = (int)$order->id_customer;
                $first_order_multiplier = (float)AffConf::getConfig('first_order_multiplier');
                /* If customer makes his first order, multiply the commission of the affiliate */
                if ($id_customer && $first_order_multiplier != 1 && !self::customerHasOtherOrdersExcept(
                        $id_customer,
                        $order->id
                    )
                ) {
                    $calculatedCommission *= $first_order_multiplier;
                }
                if ($is_lifetime_affiliate) {
                    $commission_for_life_multiplier = (float)AffConf::getConfig('commission_for_life_multiplier');
                    if ($commission_for_life_multiplier) {
                        $calculatedCommission *= $commission_for_life_multiplier;
                    }
                }

                //$decimals = (int)Configuration::get('PS_PRICE_DISPLAY_PRECISION');
                $decimals = 2;
                $calculatedCommission = Tools::ps_round($calculatedCommission, $decimals);

                return $calculatedCommission;
            }
        }

        return 0;
    }

    public function getCommissionForProducts($products, $id_affiliate = 0, $id_currency = null)
    {
        $return = 0;
        foreach ($products as $product) {
            $return += $this->getCommissionForProduct($product, $id_affiliate, $id_currency);
        }

        return $return;
    }
    public function getCommissionForProducts_debug($products, $id_affiliate = 0, $id_currency = null)
    {
        $return = 0;
        foreach ($products as $product) {
            $aux = $this->getCommissionForProduct_debug($product, $id_affiliate, $id_currency);
            echo $product['id_product'].' // '.$aux.'<br />';
            $return += $aux;
        }
        echo 'Return : '.$return.'<br />';

        return $return;
    }

    public function getCommissionDataForProduct($id_product, $id_affiliate = 0)
    {
        $rates = $this->getRatesForProduct($id_product, $id_affiliate);

        return $rates;
    }

    public function getCommissionForProduct($product, $id_affiliate = 0, $id_currency = null)
    {
        if (is_null($id_currency)) {
            $id_currency = $this->context->currency->id;
        }
        $return = 0;
        $id_product = (int)$product['product_id'];
        $rates = $this->getCommissionDataForProduct($id_product, $id_affiliate);
        if ($rates) {
            if ((float)$rates['rate_percent'] > 0) {
                $with_taxes = AffConf::getConfig('include_tax_rules');
                if ($with_taxes) {
                    $return += Tools::convertPrice((float)$product['total_price_tax_incl'], $id_currency, false) * ((float)$rates['rate_percent'] / 100);
                } else {
                    $return += Tools::convertPrice((float)$product['total_price_tax_excl'], $id_currency, false) * ((float)$rates['rate_percent'] / 100);
                }
            }
            if ((float)$rates['rate_value'] > 0) {
                $with_taxes = AffConf::getConfig('include_tax_rules');
                $return += (float)$rates['rate_value'] * (float)$product['product_quantity'];
            }
        }
        if (isset($rates['multiplier'])) {
            $return *= $rates['multiplier'];
        }

        return $return;
    }

    public function getCommissionForProduct_debug($product, $id_affiliate = 0, $id_currency = null)
    {
        if (is_null($id_currency)) {
            $id_currency = $this->context->currency->id;
        }
        $return = 0;
        $id_product = (int)$product['product_id'];
        $rates = $this->getCommissionDataForProduct($id_product, $id_affiliate);
        if ($rates) {
            echo '$rates[rate_percent] : '.$rates['rate_percent'].'<br />'; 
            if ((float)$rates['rate_percent'] > 0) {
                $with_taxes = AffConf::getConfig('include_tax_rules');
                if ($with_taxes) {
                    echo 'Price : '.Tools::convertPrice((float)$product['total_price_tax_incl'], $id_currency, false).'<br />';
                    $return += Tools::convertPrice((float)$product['total_price_tax_incl'], $id_currency, false) * ((float)$rates['rate_percent'] / 100);
                } else {
                    $return += Tools::convertPrice((float)$product['total_price_tax_excl'], $id_currency, false) * ((float)$rates['rate_percent'] / 100);
                }
            }
            echo 'tmp : '.$return.'<br />';
            if ((float)$rates['rate_value'] > 0) {
                $with_taxes = AffConf::getConfig('include_tax_rules');
                echo '$product[product_quantity] : '.$product['product_quantity'].'<br />';
                echo '$rates[rate_value] : '.$rates['rate_value'].'<br />';
                $return += (float)$rates['rate_value'] * (float)$product['product_quantity'];
            }
            echo 'tmp2 : '.$return.'<br />';
        }
        if (isset($rates['multiplier'])) {
            $return *= $rates['multiplier'];
        }
        
        echo 'tmp3 : '.$return.'<br />';

        return $return;
    }

    public function getRatesForProduct($id_product, $id_affiliate = 0)
    {
        $this->loadClasses('AffConf');
        $rates_product = array();
        $product_rates_array = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'aff_product_rates` WHERE `id_product` = "'.(int)$id_product.'"');
        if (!$product_rates_array) {
            $product_rates_array = array(
                'id_product' => $id_product,
                'rate_percent' => -1,
                'rate_value' => -1,
                'multiplier' => 1,
            );
        }
        $rates_product = $product_rates_array;
        if ($product_rates_array['rate_percent'] == -1 || $product_rates_array['rate_value'] == -1 || $product_rates_array['multiplier'] == 1) {
            $category_rates_array = $this->getRatesForCategory($this->getCategoryOfProduct($id_product));
        }
        if ($rates_product['rate_percent'] == -1) {
            $rates_product['rate_percent'] = $category_rates_array['rate_percent'];
        }
        if ($rates_product['rate_value'] == -1) {
            $rates_product['rate_value'] = $category_rates_array['rate_value'];
        }
        if (AffConf::getConfig('multiply_with_category')) {
            $rates_product['multiplier'] *= $category_rates_array['multiplier'];
        } elseif ($rates_product['multiplier'] == 1) {
            $rates_product['multiplier'] = $category_rates_array['multiplier'];
        }
        if ($rates_product['rate_percent'] == -1 || $rates_product['rate_value'] == -1) {
            $this->loadClasses('Affiliate');
            $aff = new Affiliate($id_affiliate);
            if ($rates_product['rate_percent'] == -1) {
                $rates_product['rate_percent'] = $aff->per_sale_percent;
            }
            if ($rates_product['rate_value'] == -1) {
                $general_rate_value_per_product = AffConf::getConfig('general_rate_value_per_product');
                if ($general_rate_value_per_product) {
                    $rates_product['rate_value'] = $aff->per_sale;
                } else {
                    $rates_product['rate_value'] = 0;
                }
            }
        }

        return $rates_product;
    }

    public function formatProductRates($rates)
    {
        $rates['rate_value'] = (float)$rates['rate_value'];
        $rates['rate_percent'] = (float)$rates['rate_percent'];
        if (!isset($rates['multiplier'])) {
            $rates['multiplier'] = 1;
        }

        // If only rate value...
        if ($rates['rate_value'] && !$rates['rate_percent']) {
            return Tools::displayPrice($rates['rate_value'] * $rates['multiplier']);
        }

        if (!isset($rates['id_product'])) {
            return Tools::displayPrice(0.00);
        }

        $this->loadClasses('AffConf');

        $taxable = (bool)AffConf::getConfig('include_tax_rules');
        $spo = null;

        $product_price = Product::getPriceStatic(
            (int)$rates['id_product'],
            $taxable, /* $usetax */
            null,     /* $id_product_attribute */
            6,        /* $decimals */
            null,     /* $divisor */
            false,    /* $only_reduc */
            true,     /* $usereduc */
            1,        /* $quantity */
            false,    /* $force_associated_tax */
            null,     /* $id_customer */
            null,     /* $id_cart */
            null,     /* $id_address */
            $spo,     /* &$specific_price_output */
            $taxable, /* $with_ecotax */
            false     /* $use_group_reduction */
        );

        // if only rate percent...
        if ($rates['rate_percent'] && !$rates['rate_value']) {
            return Tools::displayPrice((($rates['rate_percent'] / 100) * $product_price) * $rates['multiplier']);
        }

        // if both percent and value are set...
        return Tools::displayPrice(($rates['rate_value'] + (($rates['rate_percent'] / 100) * $product_price)) * $rates['multiplier']);
    }

    public function getRatesForCategory($id_category)
    {
        $rates_array = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'aff_category_rates` WHERE `id_category` = "'.(int)$id_category.'"');
        if (!$rates_array) {
            $rates_array = array(
                'rate_percent' => -1,
                'rate_value' => -1,
                'multiplier' => 1,
            );
        }

        return $rates_array;
    }

    public function getCategoryOfProduct($id_product)
    {
        return (int)Db::getInstance()->getValue('SELECT `id_category_default` FROM `'._DB_PREFIX_.'product` WHERE `id_product` = "'.(int)$id_product.'"');
    }

    public function toggleStatus()
    {
        $id_affiliate = (int)Tools::getValue('id_affiliate');
        if ($id_affiliate) {
            $sql = Db::getInstance()->execute("UPDATE `"._DB_PREFIX_."aff_affiliates` SET `active` = (CASE WHEN `active`='1' THEN '0' WHEN `active`='0' THEN '1' END) WHERE `id_affiliate`='".(int)$id_affiliate."' LIMIT 1;");

            return $sql;
        }

        return false;
    }

    public function setFieldsToUpdate()
    {
        if (Tools::getValue('controller')) {
            $controller = Tools::getValue('controller')."Controller";
            $controller = new $controller();

            return $controller->setFieldsToUpdate();
        }

        return false;
    }

    public function update()
    {
        if (Tools::getValue('controller')) {
            $controller = Tools::getValue('controller')."Controller";
            $controller = new $controller();

            return $controller->update();
        }
    }

    public function delete()
    {
        if (Tools::getValue('controller')) {
            $controller = Tools::getValue('controller')."Controller";
            $controller = new $controller();

            return $controller->delete();
        }

        return false;
    }

    public static function loadClasses($classes = array("Affiliate"))
    {
        if (!is_array($classes)) {
            $classes = array($classes);
        }
        foreach ($classes as $class) {
            if (file_exists(_PS_MODULE_DIR_."psaffiliate/classes/".$class.".php")) {
                require_once(_PS_MODULE_DIR_."psaffiliate/classes/".$class.".php");
            }
        }
    }

    public function isAffiliate($id_customer = false)
    {
        if (!$id_customer) {
            if (!$this->context->customer->isLogged()) {
                return false;
            } else {
                $id_customer = $this->context->customer->id;
            }
        }
        $sql = "SELECT `id_affiliate` FROM `"._DB_PREFIX_."aff_affiliates` WHERE `id_customer`='".(int)$id_customer."'";
        $sql = Db::getInstance()->getValue($sql);

        return (bool)$sql;
    }

    public static function getAffiliateId($id_customer = false)
    {
        if (!$id_customer) {
            $context = Context::getContext();
            if (!$context->customer->isLogged()) {
                return false;
            } else {
                $id_customer = $context->customer->id;
            }
        }
        $sql = "SELECT `id_affiliate` FROM `"._DB_PREFIX_."aff_affiliates` WHERE `id_customer`='".(int)$id_customer."'";
        $sql = Db::getInstance()->getValue($sql);

        return (int)$sql;
    }

    public static function getCustomerId($id_affiliate = false)
    {
        if (!$id_affiliate) {
            $context = Context::getContext();
            if (!$context->customer->id) {
                return false;
            } else {
                return $context->customer->id;
            }
        } else {
            Psaffiliate::loadClasses('Affiliate');
            $affiliate = new Affiliate($id_affiliate);
            if (Validate::isLoadedObject($affiliate)) {
                return $affiliate->id_customer;
            }
        }

        return false;
    }

    public static function hasSessionAffiliate()
    {
        $context = Context::getContext();
        if (isset($context->cookie->id_session_affiliate) && (int)$context->cookie->id_session_affiliate) {
            $id_session_affiliate = $context->cookie->id_session_affiliate;
            $id_customer_affiliate = Psaffiliate::getCustomerId($id_session_affiliate);
            $id_customer = PsAffiliate::getCustomerId();

            if ((int)$id_customer != (int)$id_customer_affiliate && (int)$id_customer_affiliate) {
                return true;
            }
        }

        return false;
    }

    public static function getAffiliateLink($id_affiliate = false, $id_product = false, $id_campaign = false)
    {
        self::loadClasses(array('AffConf'));
        if (!$id_affiliate) {
            $id_affiliate = self::getAffiliateId();
        }
        if ($id_affiliate) {
            $link_type = AffConf::getConfig('affiliate_link_type');
            if ($link_type == 1) {
                $affiliate_register_year = self::getAffiliateRegisterYear($id_affiliate);
                $affiliate_register_year = Tools::substr($affiliate_register_year, -2);
                $year_prefix = AffConf::getConfig('affiliate_year_prefix_parameter');
                if (!$year_prefix) {
                    $year_prefix = 'y';
                }
                $id_affiliate = $year_prefix.$affiliate_register_year.$id_affiliate;
            }
            $link_param = AffConf::getConfig('affiliate_id_parameter');
            $request = array($link_param => $id_affiliate);
            if ($id_campaign) {
                $request['id_campaign'] = $id_campaign;
            }
            $link = new Link;
            $context = Context::getContext();
            if (!$id_product) {
                $url = $link->getPageLink('index', null, $context->language->id, $request);
            } else {
                if (Tools::version_compare(_PS_VERSION_, '1.7', '>=')) {
                    $url = $link->getProductLink(
                        $id_product,
                        null,
                        null,
                        null,
                        null,
                        null,
                        0,
                        false,
                        false,
                        false,
                        $request
                    );
                } else {
                    $url = $link->getProductLink($id_product);
                    if (strpos($url, '?') === false) {
                        $url .= '?'.$link_param.'='.$id_affiliate;
                    } else {
                        $url .= '&'.$link_param.'='.$id_affiliate;
                    }
                }
            }

            return $url;
        }

        return false;
    }

    public static function getAffiliateName($id_affiliate)
    {
        return Db::getInstance()->getValue('SELECT CONCAT(`firstname`, " ", `lastname`) FROM `'._DB_PREFIX_.'aff_affiliates` WHERE `id_affiliate` = "'.(int)$id_affiliate.'"');
    }

    public function startTracking()
    {
        $this->loadClasses('Tracking');
        $tracking = new Tracking;
        $tracking->startTracking();
    }

    public function hasTexts($active = false)
    {
        $this->loadClasses('Text');

        return Text::hasTexts($active);
    }

    public function hasBanners($active = false)
    {
        $this->loadClasses('Banner');

        return Banner::hasBanners($active);
    }

    public function getAffiliatesList()
    {
        $sql = 'SELECT af.`id_affiliate` as `id`, CONCAT("#", af.`id_affiliate`, " - ", IF(af.`id_customer` <> 0, CONCAT(c.`firstname`, " ", c.`lastname`), CONCAT(af.`firstname`, " ", af.`lastname`))) as `value` FROM `'._DB_PREFIX_.'aff_affiliates` af LEFT JOIN `'._DB_PREFIX_.'customer` c ON (c.`id_customer` = af.`id_customer`)';
        $result = Db::getInstance()->executeS($sql);
        $array = array();
        foreach ($result as $val) {
            $array[$val['id']] = $val['value'];
        }

        return $array;
    }

    public function getCampaignsList($id_affiliate = false, $for_edit_select = false)
    {
        $sql = 'SELECT c.`id_campaign` as `id`, CONCAT("#", c.`id_campaign`, " - ", c.`name`) as `value` FROM `'._DB_PREFIX_.'aff_campaigns` c';
        if ($id_affiliate) {
            $sql .= " WHERE c.`id_affiliate`='".(int)$id_affiliate."'";
        }
        $result = Db::getInstance()->executeS($sql);
        $array = array();
        if (!$for_edit_select) {
            foreach ($result as $val) {
                $array[$val['id']] = $val['value'];
            }

            return $array;
        }
        $result = array_merge(array(array('id' => '0', 'value' => '--')), $result);

        return $result;
    }

    public function getPath()
    {
        return $this->_path;
    }

    public function getPathDir()
    {
        return dirname(__FILE__);
    }

    private function makeRequestToAddons($data = array())
    {
        $data = array_merge(
            array(
                'version' => _PS_VERSION_,
                'iso_lang' => Tools::strtolower(Language::getIsoById((int)$this->context->cookie->id_lang)),
                'iso_code' => Tools::strtolower(Country::getIsoById((int)Configuration::get('PS_COUNTRY_DEFAULT'))),
                'module_key' => $this->module_key,
                'method' => 'contributor',
                'action' => 'all_products',
            ),
            $data
        );

        $postData = http_build_query($data);
        $context = stream_context_create(array(
            'http' => array(
                'method' => 'POST',
                'content' => $postData,
                'header' => 'Content-type: application/x-www-form-urlencoded',
                'timeout' => 15,
            ),
        ));

        $jsonResponse = Tools::file_get_contents(static::ADDONS_API, false, $context);
        $response = Tools::jsonDecode($jsonResponse, true);

        if (empty($jsonResponse) || empty($response)) {
            return false;
        }

        return $response;
    }

    private function getAddonsModules()
    {
        $modules = json_decode(Configuration::get(static::$config_prefix.'ADDONS_MODULES'), true);
        $modulesLastUpdate = Configuration::get(static::$config_prefix.'ADDONS_MODULES_LAST_UPDATE');

        if ($modules && $modulesLastUpdate && strtotime('+2 day', $modulesLastUpdate) > time()) {
            return $modules;
        }

        $response = $this->makeRequestToAddons();
        $freshModules = $response['products'];
        if (!$response || empty($freshModules)) {
            return array();
        }

        $newModules = array();
        foreach ($freshModules as $module) {
            $newModules[] = array(
                'id' => $module['id'],
                'name' => $module['name'],
                'url' => $module['url'],
                'img' => $module['img'],
                'price' => $module['price'],
                'displayName' => $module['displayName'],
                'description' => $module['description'],
                'compatibility' => $module['compatibility'],
                'version' => $module['version'],
            );
        }

        Configuration::updateValue(static::$config_prefix.'ADDONS_MODULES', Tools::jsonEncode($newModules));
        Configuration::updateValue(static::$config_prefix.'ADDONS_MODULES_LAST_UPDATE', time());

        return $newModules;
    }

    private function showDiscover()
    {
        return $this->context->controller instanceof AdminPsaffiliateAdminController;
    }

    public function getDiscoverTpl()
    {
        $modules = $this->getAddonsModules();

        if (empty($modules)) {
            $this->context->smarty->assign('addons_modules', $modules);

            return $this->getLocalPath().'views/templates/admin/discover';
        }

        $defaultCurrencyIso = Tools::strtolower((new Currency((int)Configuration::get('PS_CURRENCY_DEFAULT')))->iso_code);

        $currencyIsos = array('eur', 'usd', 'gbp');
        $currencyIso = in_array($defaultCurrencyIso, $currencyIsos) ? $defaultCurrencyIso : 'eur';

        array_walk($modules, function (&$module) use ($currencyIso) {
            $price = array_change_key_case($module['price'], CASE_LOWER);
            $priceAmount = $price[$currencyIso];

            $formatted = '';
            if ($currencyIso == 'eur') {
                $formatted = number_format($priceAmount, 2, ',', '.').' €';
            } elseif ($currencyIso == 'usd') {
                $formatted = '$'.number_format($priceAmount, 2, '.', ',');
            } elseif ($currencyIso == 'gbp') {
                $formatted = '£ '.number_format($priceAmount, 2, '.', ',');
            }

            $module['price_formatted'] = $formatted;
        });

        shuffle($modules);

        $this->context->smarty->assign('addons_modules', $modules);

        return $this->getLocalPath().'views/templates/admin/discover';
    }

    public static function getAffiliateRegisterYear($id_affiliate)
    {
        return Db::getInstance()->getValue('SELECT EXTRACT(YEAR FROM date_created) FROM `'._DB_PREFIX_.'aff_affiliates` WHERE id_affiliate="'.(int)$id_affiliate.'"');
    }

    public static function customerHasOtherOrdersExcept($id_customer, $id_order)
    {
        return (bool)Db::getInstance()->getValue('SELECT COUNT(*) FROM `'._DB_PREFIX_.'orders` WHERE `id_customer` = "'.(int)$id_customer.'" AND `id_order` != "'.(int)$id_order.'"');
    }

    public static function customerHasLifetimeAffiliate($id_customer)
    {
        return (int)Db::getInstance()->getValue('SELECT `id_affiliate` FROM `'._DB_PREFIX_.'aff_customers` WHERE `id_customer` = "'.(int)$id_customer.'"');
    }

    /* This function is used to associate the lifetime affiliate to a customer */
    public static function associateCustomerToAffiliate($id_customer, $id_affiliate)
    {
        return Db::getInstance()->insert('aff_customers', array(
            'id_affiliate' => (int)$id_affiliate,
            'id_customer' => (int)$id_customer,
            'date_add' => pSQL(date('Y-m-d H:i:s')),
        ), false, true, Db::REPLACE);
    }

    public static function getCustomerCommissionsGenerated($id_customer, $limit = 0)
    {
        $sql = 'SELECT `id_tracking` as `id`, `date`, `commission`, `id_affiliate`, (SELECT CONCAT(`firstname`, " ", `lastname`) FROM `'._DB_PREFIX_.'aff_affiliates` af WHERE af.`id_affiliate` = tr.`id_affiliate`) as `affiliate_name`, "tracking" as `type`, "1" as `approved`, "0" as `id_order`  FROM `'._DB_PREFIX_.'aff_tracking` tr WHERE `id_customer` = "'.(int)$id_customer.'"';
        $sql .= ' UNION SELECT `id_sale` as `id`, `date`, `commission`, `id_affiliate`, (SELECT CONCAT(`firstname`, " ", `lastname`) FROM `'._DB_PREFIX_.'aff_affiliates` af WHERE af.`id_affiliate` = sa.`id_affiliate`) as `affiliate_name`, "sale" as `type`, `approved`, `id_order` FROM `'._DB_PREFIX_.'aff_sales` sa WHERE `id_order` IN (SELECT `id_order` FROM `'._DB_PREFIX_.'orders` WHERE `id_customer` = "'.(int)$id_customer.'")';
        $sql .= ' ORDER BY `date` DESC';
        if ($limit) {
            $sql .= ' LIMIT '.(int)$limit;
        }

        return Db::getInstance()->executeS($sql);
    }

    public static function getLifetimeAffiliations($id_affiliate)
    {
        return Db::getInstance()->executeS('SELECT `id_affiliate`, `id_customer`, `date_add` as `date`, (SELECT CONCAT(`firstname`, " ", `lastname`) FROM `'._DB_PREFIX_.'customer` c WHERE c.`id_customer` = a.`id_customer`) as `customer_name`, ((SELECT IFNULL(SUM(`commission`), 0) FROM `'._DB_PREFIX_.'aff_tracking` WHERE `id_affiliate` = a.`id_affiliate` AND `id_customer` = a.`id_customer`) + (SELECT IFNULL(SUM(`commission`), 0) FROM `'._DB_PREFIX_.'aff_sales` WHERE `id_affiliate` = a.`id_affiliate` AND `id_order` IN (SELECT `id_order` FROM `'._DB_PREFIX_.'orders` WHERE `id_customer` = a.`id_customer`))) as `commission` FROM `'._DB_PREFIX_.'aff_customers` a WHERE a.`id_affiliate` = "'.(int)$id_affiliate.'" ORDER BY `date` DESC');
    }

    public function installAjaxAdminController()
    {
        $tab = new Tab;

        $tab->class_name = 'AdminPsaffiliateAjax';
        $tab->id_parent = '-1';
        $tab->module = $this->name;
        $tab->name[(int)(Configuration::get('PS_LANG_DEFAULT'))] = $this->displayName;
        if (!$tab->add()) {
            return false;
        }

        return true;
    }

    public static function getAllowedGroups()
    {
        self::loadClasses('AffConf');

        $allowed_groups = AffConf::getConfig('groups_allowed[]');

        return $allowed_groups;
    }

    public static function isGroupAllowed($id_group = 0)
    {
        $allowed_groups = self::getAllowedGroups();

        if (!$allowed_groups) {
            return true;
        } else {
            return in_array($id_group, $allowed_groups);
        }
    }

    public static function associateCartToAffiliate($id_cart, $id_affiliate, $id_campaign = 0)
    {
        return Db::getInstance()->insert('aff_cart', array(
            'id_cart' => (int)$id_cart,
            'id_affiliate' => (int)$id_affiliate,
            'id_campaign' => (int)$id_campaign,
            'date' => pSQL(date('Y-m-d H:i:s')),
        ), false, true, Db::REPLACE);
    }

    public static function getCartAffiliate($id_cart)
    {
        self::loadClasses('AffConf');
        $data = Db::getInstance()->getRow('SELECT `id_affiliate`, `id_campaign` FROM `'._DB_PREFIX_.'aff_cart` WHERE `id_cart` = "'.(int)$id_cart.'" AND `date` >= "'.pSQL(date('Y-m-d H:i:s', strtotime('-'.AffConf::getConfig('days_remember_affiliate').' days'))).'"');

        return $data;
    }

    public function hookActionCartSave($params)
    {
        if (!isset($this->context->cart)) {
            return;
        }
        $id_cart = (int)$this->context->cart->id;
        if ($this->hasSessionAffiliate()) {
            $id_affiliate = (int)$this->context->cookie->id_session_affiliate;
            $id_campaign = (int)$this->context->cookie->id_session_campaign;
            $cart_affiliate_data = self::getCartAffiliate($id_cart);
            if (!$cart_affiliate_data) {
                self::associateCartToAffiliate($id_cart, $id_affiliate, $id_campaign);
            } else {
                if ($cart_affiliate_data['id_affiliate'] != $id_affiliate || $cart_affiliate_data['id_campaign'] != $id_campaign) {
                    self::associateCartToAffiliate($id_cart, $id_affiliate, $id_campaign);
                }
            }
        }
    }

    public static function getAdminEmails()
    {
        self::loadClasses('AffConf');
        $admin_emails = AffConf::getConfig('emails');
        $admin_emails = explode(PHP_EOL, $admin_emails);
        $return = array();
        if ($admin_emails) {
            foreach ($admin_emails as $email) {
                $email = trim($email);
                if (Validate::isEmail($email)) {
                    $return[] = $email;
                }
            }
        }

        return $return;
    }
}
