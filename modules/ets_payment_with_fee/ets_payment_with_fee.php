<?php
/**
 * Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
 */

if (!defined('_PS_VERSION_')) { exit; }
include_once(_PS_MODULE_DIR_ . 'ets_payment_with_fee/classes/ets_paymentmethod_class.php');
include_once(_PS_MODULE_DIR_ . 'ets_payment_with_fee/classes/ets_payment_cart_class.php');
include_once(_PS_MODULE_DIR_ . 'ets_payment_with_fee/classes/ets_payment_utils.php');
if (!defined('_PS_ETS_PAYMENT_FEE_IMG_DIR_')) {
    define('_PS_ETS_PAYMENT_FEE_IMG_DIR_', _PS_IMG_DIR_.'ets_payment_fee/');
}
if (!defined('_PS_ETS_PAYMENT_FEE_IMG_')) {
    define('_PS_ETS_PAYMENT_FEE_IMG_', __PS_BASE_URI__.'img/ets_payment_fee/');
}
use PrestaShop\PrestaShop\Adapter\StockManager;

class Ets_payment_with_fee extends PaymentModule
{
    private $baseAdminPath;
    private $errorMessage;
    public $_html;
    public $is17 = false;
    public $configTabs = array();
    public $module_dir;
    public $add_product_to_paypal = false;
    public $add_product_to_klarna = false;
    public $payment_fee_text;
    public $fields_form;

    public function __construct()
    {
        $this->name = 'ets_payment_with_fee';
        $this->tab = 'payments_gateways';
        $this->version = '2.4.5';
        $this->author = 'PrestaHero';
        $this->need_instance = 0;
        $this->bootstrap = true;
        parent::__construct();
        if (version_compare(_PS_VERSION_, '1.7', '>='))
            $this->is17 = true;
        $this->displayName = $this->l('Payment With Fee');
        $this->description = $this->l('Set extra fee for any payment method such as cash on delivery (COD), bank wire, Paypal, Stripe, etc. Create unlimited number of custom payment method with/without fee');
        $this->module_key = '2562440dd1523b501cfd6f62a5093416';
        $this->ps_versions_compliancy = array('min' => '1.7.0.0', 'max' => _PS_VERSION_);
        $this->configTabs = array(
            'general' => $this->l('General'),
            'visibility' => $this->l('Visibility'),
        );
        $this->module_dir = $this->_path;
        if (isset($this->context->controller->controller_type) && $this->context->controller->controller_type == 'admin')
            $this->baseAdminPath = $this->context->link->getAdminLink('AdminModules') . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $this->payment_fee_text = Configuration::get('ETS_PMF_TEXT_PAYMENT_FEE', $this->context->language->id) ?: $this->l('Payment fee');
       /*$this->context->smarty->assign(
            array(
                'text_payment_fee_incl' => sprintf($this->l('%s (tax incl.)'), $this->payment_fee_text),
                'text_payment_fee_excl' => sprintf($this->l('%s (tax excl.)'), $this->payment_fee_text),
            )
        );*/
        $this->context->smarty->assign(
            array(
                'text_payment_fee_incl' => sprintf($this->l('%s'), $this->payment_fee_text),
                'text_payment_fee_excl' => sprintf($this->l('%s'), $this->payment_fee_text),
            )
        );

    }

    /**
     * @see Module::install()
     */
    public function install()
    {
        Ets_paymentmethod_class::installDb();
        return parent::install()
            && $this->registerHook('paymentOptions')
            && $this->registerHook('paymentReturn')
            && $this->registerHook('displayHeader')
            && $this->registerHook('actionOrderStatusPostUpdate')
            && $this->registerHook('actionValidateOrder')
            && $this->registerHook('actionEmailSendBefore')
            && $this->registerHook('displayPaymentFeeOrder')
            && $this->registerHook('displayBackOfficeHeader') && $this->registerHook('displayOverrideTemplate')
            && $this->_installDb() && $this->_installTab() && $this->_installDbDefault() && Ets_paymentmethod_class::addIndexTable() && $this->updatePositionHookValidate();
    }

    /**
     * @see Module::uninstall()
     */
    public function uninstall()
    {
        return parent::uninstall() && $this->_uninstallDb() && $this->_uninstallTab();
    }

    public function _installTab()
    {
        $languages = Language::getLanguages(false);
        $tabId = $this->is17 ? Tab::getIdFromClassName('AdminParentPayment') : Tab::getIdFromClassName('AdminParentModules');
        if ($tabId) {
            $tab = new Tab();
            $tab->class_name = 'AdminPaymentFee';
            $tab->module = $this->name;
            $tab->id_parent = $tabId;
            $tab->icon = '';
            foreach ($languages as $lang) {
                $tab->name[$lang['id_lang']] = $this->getTextLang('Payment fees', $lang) ?: $this->l('Payment fees');
            }
            $tab->save();
        }
        return true;
    }

    public function _uninstallTab()
    {
        $tabId = Tab::getIdFromClassName('AdminPaymentFee');
        if ($tabId) {
            $tab = new Tab($tabId);
            $tab->delete();
        }
        return true;
    }

    public function _installDbDefault()
    {
        $ETS_PMF_TEXT_PAYMENT_FEEs = array();
        foreach (Language::getLanguages(false) as $language) {
            $ETS_PMF_TEXT_PAYMENT_FEEs[$language['id_lang']] = $this->getTextLang('Payment fee', $language) ?: $this->l('Payment fee');
        }
        Configuration::updateValue('ETS_PMF_TEXT_PAYMENT_FEE', $ETS_PMF_TEXT_PAYMENT_FEEs);
        return true;
    }

    public function _installDb()
    {
        $cache_id = 'Module::getModuleIdByName_' . pSQL($this->name);
        Cache::clean($cache_id);
        if (version_compare(_PS_VERSION_, '1.7', '<='))
            $this->copy_directory(dirname(__FILE__) . '/views/templates/admin/templates', _PS_OVERRIDE_DIR_ . 'controllers/admin/templates');
        return true;
    }

    private function _uninstallDb()
    {
        if (version_compare(_PS_VERSION_, '1.7', '<='))
            $this->delete_directory(_PS_OVERRIDE_DIR_ . 'controllers/admin/templates');
        $files = glob(_PS_ETS_PAYMENT_FEE_IMG_DIR_ . '*');
        foreach ($files as $file) {
            if (is_file($file) && file_exists($file) && $file != _PS_ETS_PAYMENT_FEE_IMG_DIR_ . 'index.php')
                @unlink($file);
        }
        return Ets_paymentmethod_class::unInstallDb();
    }

    public function checkDisplayTax($id_customer)
    {
        $id_group = null;
        if ($id_customer) {
            $id_group = Customer::getDefaultGroupId((int)$id_customer);
        }
        if (!$id_group) {
            $id_group = (int)Group::getCurrent()->id;
        }
        $group = new Group($id_group);
        if ($group->price_display_method)
            $tax = false;
        else
            $tax = true;
        return $tax;
    }

    public function hookDisplayHeader()
    {
        $id_customer = ($this->context->customer->id) ? (int)($this->context->customer->id) : 0;
        $tax = $this->checkDisplayTax($id_customer);
        if (Tools::isSubmit('ets_set_payment_option')) {
            $module_name = Tools::getValue('module_name');
            if (in_array($module_name, array('express_checkout_schortcut', 'braintree', 'paypal_plus', 'paypal_plus_schortcut', 'paypal-ec','paypal_bnpl')))
                $module_name = 'paypal';
            if (in_array($module_name, array('ps_checkout_hostedFields', 'ps_checkout_paypal', 'ps_checkout-card', 'ps_checkout-paypal')))
                $module_name = 'ps_checkout';
            if (in_array($module_name, array('klarna_payments', 'klarnapayments_pay_later_module', 'klarnapayments_pay_over_time_module')))
                $module_name = 'klarnapaymentsofficial';
            $id_payment_method = (int)Tools::getValue('id_payment_method');
            $payment_option = Tools::getValue('payment_option');
            if (($idCart = (int)$this->context->cart->id) && $payment_option && Validate::isCleanHtml($payment_option) && Validate::isModuleName($module_name)) {
                $ets_cart_module = new Ets_payment_cart_class($idCart);
                $ets_cart_module->id_cart = $idCart;
                $ets_cart_module->ets_payment_module_name = $module_name;
                $ets_cart_module->id_payment_method = $id_payment_method;
                $ets_cart_module->payment_option = $payment_option;
                $ets_cart_module->save();
            }
            if ($module_name == $this->name && $id_payment_method) {
                $total_fee = $this->getFeePayment($id_payment_method, null, $tax);
            } else {
                $module = Module::getInstanceByName($module_name);
                $ets_paymentmethod_module = Ets_paymentmethod_class::getPaymentMethodByIdModule($module->id);
                $totalCartOnly = $this->context->cart->getOrderTotal(true, Cart::BOTH, null, null, false, false, false, true);
                if ($ets_paymentmethod_module) {
                    if ((($minOrder = $ets_paymentmethod_module['minimum_order']) && (float)$minOrder > (float)$totalCartOnly) || (($maxOrder = $ets_paymentmethod_module['maximum_order']) && (float)$maxOrder < (float)$totalCartOnly)) {
                        $total_fee = false;
                        $text_percentage = false;
                    } else {
                        if ($ets_paymentmethod_module && $ets_paymentmethod_module['fee_type'] == 'percentage' && $ets_paymentmethod_module['percentage'])
                            $text_percentage = '(' . $ets_paymentmethod_module['percentage'] . '%)';
                        $total_fee = $this->context->cart->getOrderTotal($tax, Cart::BOTH, null, null, false, false, true);
                    }
                }
            }
            $priceFormatter = new PrestaShop\PrestaShop\Adapter\Product\PriceFormatter();
            $total_incl = Tools::ps_round($this->context->cart->getOrderTotal(true), isset($this->context->currency->precision) && $this->context->currency->precision ? $this->context->currency->precision : 2);
            $total_excl = Tools::ps_round($this->context->cart->getOrderTotal(false), isset($this->context->currency->precision) && $this->context->currency->precision ? $this->context->currency->precision : 2);
            die(
            json_encode(
                array(
                    'payment_fee' => isset($total_fee) && $total_fee ? $priceFormatter->format($total_fee, $this->context->currency) : false,
                    'text_percentage' => isset($text_percentage) ? $text_percentage : '',
                    'total_cart' => $priceFormatter->format($total_incl, $this->context->currency),
                    'total_tax' => $priceFormatter->format($total_incl - $total_excl, $this->context->currency),
                    'total_cart_excl' => $priceFormatter->format($total_excl, $this->context->currency),
                )
            )
            );
        }
        if (Tools::isSubmit('ets_get_payment_fee')) {
            $paymentCart = new Ets_payment_cart_class($this->context->cart->id);
            if ($paymentCart && $module_name = $paymentCart->ets_payment_module_name) {
                if ($module_name == $this->name && $id_payment_method = $paymentCart->id_payment_method) {
                    $total_fee = $this->getFeePayment($id_payment_method, null);
                } else
                    $total_fee = $this->context->cart->getOrderTotal(true, Cart::BOTH, null, null, false, false, true);
                $priceFormatter = new PrestaShop\PrestaShop\Adapter\Product\PriceFormatter();
                die(
                json_encode(
                    array(
                        'payment_fee' => $total_fee ? $priceFormatter->format($total_fee, $this->context->currency) : false,
                        'total_cart' => $priceFormatter->format($this->context->cart->getOrderTotal($tax), $this->context->currency),
                    )
                )
                );
            } else {
                die(
                json_encode(
                    array(
                        'payment_fee' => false,
                    )
                )
                );
            }
        }
        $controller = Tools::getValue('controller');
        if ($controller == 'orderopc' || $controller == 'order') {
            $this->context->controller->addJS($this->_path . 'views/js/order.js');
            $this->context->controller->addCSS($this->_path . 'views/css/frontend.css');
            $paymentCart = new Ets_payment_cart_class($this->context->cart->id);
            $this->context->smarty->assign(
                array(
                    'ets_cookie_module_name' => isset($paymentCart->ets_payment_module_name) && $paymentCart->ets_payment_module_name ? $paymentCart->ets_payment_module_name : '',
                    'ets_cookie_id_payment_method' => isset($paymentCart->id_payment_method) && $paymentCart->id_payment_method ? $paymentCart->id_payment_method : '',
                    'ets_cookie_payment_option' => isset($paymentCart->payment_option) && $paymentCart->payment_option ? $paymentCart->payment_option : '',
                    'ets_pmwf_use_tax' => $tax,
                )
            );
            return $this->display(__FILE__, 'head.tpl');
        }
    }
    public function getContent()
    {
        $this->context->controller->addJqueryUI('ui.sortable');
        $action = Tools::getValue('action');
        if ($action == 'updatePaymentOrdering')
            $this->updatePositionPayment();
        $errors = array();
        if (Tools::isSubmit('savePaymentMethod')) {
            $fee_type = Tools::getValue('fee_type');
            $fee_amount = Tools::getValue('fee_amount');
            $id_lang_default = (int)Configuration::get('PS_LANG_DEFAULT');
            $free_for_order_over = Tools::getValue('free_for_order_over');
            if(!in_array($fee_type,array('free','fixed_fee','percentage','percentage_fixed')))
            {
                $errors[] = $this->l('Fee type is not valid');
            }
            $method_name_default = Tools::getValue('method_name_'.$id_lang_default);
            if (!$method_name_default)
                $errors[] = $this->l('Method name is required');
            if ($free_for_order_over != '' && (!Validate::isFloat($free_for_order_over) || (float)$free_for_order_over <= 0))
                $errors = $this->l('Free for order over is invalid');
            if ($fee_type == 'fixed_fee' || $fee_type=='percentage_fixed') {
                if ($fee_amount != '' && (!Validate::isFloat($fee_amount) || (float)$fee_amount <= 0))
                    $errors[] = $this->l('Fee amount is invalid');
                elseif (!$fee_amount)
                    $errors[] = $this->l('Fee amount is required');
            }
            $percentage = Tools::getValue('percentage');
            $max_fee = Tools::getValue('max_fee');
            $min_fee = Tools::getValue('min_fee');
            if ($fee_type == 'percentage' || $fee_type=='percentage_fixed') {
                if ($percentage != '' && (!Validate::isFloat($percentage) || $percentage <= 0 || $percentage >100))
                    $errors[] = $this->l('Percentage is invalid');
                elseif (!$percentage)
                    $errors[] = $this->l('Percentage amount is required');
                if ($max_fee != '' && (!Validate::isFloat($max_fee) || (float)$max_fee <= 0))
                    $errors[] = $this->l('Maximum fee is invalid');
                if ($min_fee != '' && (!Validate::isFloat($min_fee) || (float)$min_fee <= 0))
                    $errors[] = $this->l('Minimum fee is invalid');
                if ($max_fee != '' && $min_fee != '' && Validate::isFloat($min_fee) && Validate::isFloat($max_fee) && (float)$min_fee > (float)$max_fee && (float)$max_fee >= 0 && (float)$min_fee >= 0) {
                    $errors[] = $this->l('Maximum fee must be greater than Minimum fee');
                }
            }
            $minimum_order = Tools::getValue('minimum_order');
            if (($minimum_order != '' && !Validate::isFloat($minimum_order)) || (float)$minimum_order < 0)
                $errors[] = $this->l('Minimum order amount is invalid');
            $maximum_order = Tools::getValue('maximum_order');
            if (($maximum_order != '' && !Validate::isFloat($maximum_order)) || (float)$maximum_order < 0)
                $errors[] = $this->l('Maximum order amount is invalid');
            if ($maximum_order != '' && $minimum_order != '' && Validate::isFloat($minimum_order) && Validate::isFloat($maximum_order) && (float)$maximum_order < (float)$minimum_order && (float)$maximum_order >= 0 && (float)$minimum_order >= 0)
                $errors[] = $this->l('Maximum order must be greater than minimum order');
            $order_status = (int)Tools::getValue('order_status');
            if (!$order_status)
                $errors[] = $this->l('Order status is required');
            elseif(!Validate::isUnsignedId($order_status) || !Validate::isLoadedObject(new OrderState($order_status)))
                $errors[] = $this->l('Order status is not valid');
            $languages = Language::getLanguages(false);
            $method_names = array();
            $descriptions = array();
            $confirmation_messages = array();
            $return_message = array();
            if ($languages) {
                foreach ($languages as $language) {
                    $method_names[$language['id_lang']] = Tools::getValue('method_name_' . $language['id_lang']);
                    if (!Validate::isCleanHtml($method_names[$language['id_lang']])) {
                        $errors[] = sprintf($this->l('Method name is invalid in language %s'),$language['iso_code']);
                    }
                    $descriptions[$language['id_lang']] = Tools::getValue('description_' . $language['id_lang']);
                    if (!Validate::isCleanHtml($descriptions[$language['id_lang']])) {
                        $errors[] = sprintf($this->l('Description is invalid in language %s'),$language['iso_code']);
                    }
                    $confirmation_messages[$language['id_lang']] = Tools::getValue('confirmation_message_' . $language['id_lang']);
                    if (!Validate::isCleanHtml($confirmation_messages[$language['id_lang']])) {
                        $errors[] = sprintf($this->l('Confirmation message is invalid in language %s'),$language['iso_code']);
                    }
                    $return_message[$language['id_lang']] = Tools::getValue('return_message_' . $language['id_lang']);
                    if (!Validate::isCleanHtml($return_message[$language['id_lang']])) {
                        $errors[] = sprintf($this->l('Return message is invalid in language %s'),$language['iso_code']);
                    }
                }
            }
            if (!$errors) {
                if (($id_ets_paymentmethod = (int)Tools::getValue('id_ets_paymentmethod')) && Ets_paymentmethod_class::checkExists($id_ets_paymentmethod)) {
                    $paymentmethod = new Ets_paymentmethod_class($id_ets_paymentmethod);
                } else
                {
                    $paymentmethod = new Ets_paymentmethod_class();
                    $paymentmethod->id_shop = (int)$this->context->shop->id;
                }
                $languages = Language::getLanguages(false);
                if ($languages) {
                    foreach ($languages as $language) {
                        $paymentmethod->method_name[$language['id_lang']] = $method_names[$language['id_lang']] ? : $method_names[$id_lang_default];
                        $paymentmethod->description[$language['id_lang']] = $descriptions[$language['id_lang']] ? : $descriptions[$id_lang_default];
                        $paymentmethod->confirmation_message[$language['id_lang']] = $confirmation_messages[$language['id_lang']] ? :$confirmation_messages[$id_lang_default];
                        $paymentmethod->return_message[$language['id_lang']] = $return_message[$language['id_lang']] ? :$return_message[$id_lang_default];
                    }
                }
                $paymentmethod->fee_type = $fee_type;
                $active = (int)Tools::getValue('active');
                $paymentmethod->active = (int)$active;
                $customer_group = Tools::getValue('customer_group');
                if ($customer_group && Ets_payment_with_fee::validateArray($customer_group))
                    $paymentmethod->customer_group = implode(',', $customer_group);
                else
                    $paymentmethod->customer_group = '';
                $countries = Tools::getValue('countries');
                if ($countries && Ets_payment_with_fee::validateArray($countries,'isInt'))
                    $paymentmethod->countries = implode(',', $countries);
                else
                    $paymentmethod->countries = '';
                $carriers = Tools::getValue('carriers');
                if ($carriers && Ets_payment_with_fee::validateArray($carriers,'isInt'))
                    $paymentmethod->carriers = implode(',', $carriers);
                else
                    $paymentmethod->carriers = '';
                $paymentmethod->order_status = (int)$order_status;
                $paymentmethod->fee_amount = $fee_amount != '' ? (float)$fee_amount / $this->context->currency->conversion_rate : '';
                $paymentmethod->minimum_order = $minimum_order != '' ? (float)$minimum_order / $this->context->currency->conversion_rate : '';
                $paymentmethod->maximum_order = $maximum_order != '' ? (float)$maximum_order / $this->context->currency->conversion_rate : '';
                $paymentmethod->percentage = (float)$percentage;
                $paymentmethod->max_fee = $max_fee != '' ? (float)$max_fee / $this->context->currency->conversion_rate : '';
                $paymentmethod->min_fee = $min_fee != '' ? (float)$min_fee / $this->context->currency->conversion_rate : '';
                $paymentmethod->free_for_order_over = $free_for_order_over ? $free_for_order_over / $this->context->currency->conversion_rate : '';
                $fee_based_on = (int)Tools::getValue('fee_based_on');
                $paymentmethod->fee_based_on = (int)$fee_based_on;
                $id_tax_rules_group = (int)Tools::getValue('id_tax_rules_group');
                $paymentmethod->id_tax_rules_group = $id_tax_rules_group;
                $key = 'logo_payment';
                if ($logo_payment = $this->UploadImage($key, $errors)) {
                    $oldImage = _PS_ETS_PAYMENT_FEE_IMG_DIR_ . $paymentmethod->logo_payment;
                    if (file_exists($oldImage))
                        @unlink($oldImage);
                    $paymentmethod->logo_payment = $logo_payment;
                }
                if (count($errors)) {
                    $this->errorMessage = $this->displayError($errors);
                } else {
                    if ($paymentmethod->id) {
                        $paymentmethod->update(true);
                        Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true) . '&conf=4&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name . '&control=payment_method&list=true');
                    } else {
                        $position = Ets_paymentmethod_class::getMaxPosition();
                        $paymentmethod->position = $position + 1;
                        $paymentmethod->add(true, true);
                        Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true) . '&conf=3&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name . '&control=payment_method&list=true');
                    }

                }
            } else
                $this->errorMessage = $this->displayError($errors);

        }
        if (Tools::isSubmit('delete_payment_logo') && ($id_ets_paymentmethod = (int)Tools::getValue('id_ets_paymentmethod')) && ($paymentmethod = new Ets_paymentmethod_class($id_ets_paymentmethod)) && Validate::isLoadedObject($paymentmethod)) {
            $oldImage = _PS_ETS_PAYMENT_FEE_IMG_DIR_ . $paymentmethod->logo_payment;
            if (file_exists($oldImage))
                @unlink($oldImage);
            $paymentmethod->logo_payment = '';
            $paymentmethod->update(true);
        }
        if (Tools::isSubmit('change_enabled') && ($id_ets_paymentmethod = (int)Tools::getValue('id_ets_paymentmethod')) && Ets_paymentmethod_class::checkExists($id_ets_paymentmethod)){
            $change_enabled = (int)Tools::getValue('change_enabled');
            $paymentmethod = new Ets_paymentmethod_class($id_ets_paymentmethod);
            $paymentmethod->active = (int)$change_enabled;
            if($paymentmethod->update())
            {
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true) . '&conf=5&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name . '&control=payment_method&list=true');
            }
        }
        $del = Tools::getValue('del');
        if ($del == 'yes' && ($id_ets_paymentmethod = (int)Tools::getValue('id_ets_paymentmethod')) && Ets_paymentmethod_class::checkExists($id_ets_paymentmethod)) {
            $paymentmethod = new Ets_paymentmethod_class($id_ets_paymentmethod);
            $paymentmethod->delete();
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true) . '&conf=2&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name . '&control=payment_method&list=true');
        }
        $this->context->smarty->assign(
            array(
                'link' => $this->context->link,
            )
        );
        return $this->getAminHtml();
    }

    public function hookDisplayBackOfficeHeader()
    {
        $controller = Tools::getValue('controller');
        $configure = Tools::getValue('configure');
        if ($controller == 'AdminPaymentFee' || ($controller == 'AdminModules' && $configure == $this->name)) {
            $this->context->controller->addCSS($this->_path . 'views/css/admin.css');
            $this->context->controller->addJquery();
        }
        if ($controller == 'AdminOrders') {
            $this->context->controller->addJS($this->_path . 'views/js/admin_orders.js');
        }
    }

    public function getAminHtml()
    {
        $this->smarty->assign(array(
            'ets_custom_payment_ajax_url' => $this->context->link->getAdminLink('AdminModules', true) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name,
            'ets_custom_payment_default_lang' => (int)Configuration::get('PS_LANG_DEFAULT'),
            'ets_custom_payment_module_dir' => $this->_path,
            'ets_custom_payment_body_html' => $this->renderAdminBodyHtml(),
            'ets_custom_payment_error_message' => $this->errorMessage, 
        ));
        return (!$this->active ? $this->displayWarning($this->l('You must enable "Payment with fee" module to configure its features')):'').$this->display(__FILE__, 'admin.tpl');
    }
    public static function getBaseModLink()
    {
        $context = Context::getContext();
        return (Configuration::get('PS_SSL_ENABLED_EVERYWHERE') ? 'https://' : 'http://') . $context->shop->domain . $context->shop->getBaseURI();
    }
    public static function file_get_contents($url, $use_include_path = false, $stream_context = null, $curl_timeout = 60)
    {
        if ($stream_context == null && preg_match('/^https?:\/\//', $url)) {
            $stream_context = stream_context_create(array(
                "http" => array(
                    "timeout" => $curl_timeout,
                    "max_redirects" => 101,
                    "header" => 'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.3865.90 Safari/537.36'
                ),
                "ssl" => array(
                    "allow_self_signed" => true,
                    "verify_peer" => false,
                    "verify_peer_name" => false,
                ),
            ));
        }
        if (function_exists('curl_init')) {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_URL => html_entity_decode($url),
                CURLOPT_USERAGENT => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.3865.90 Safari/537.36',
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_TIMEOUT => $curl_timeout,
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_FOLLOWLOCATION => true,
            ));
            $content = curl_exec($curl);
            curl_close($curl);
            return $content;
        } elseif (in_array(ini_get('allow_url_fopen'), array('On', 'on', '1')) || !preg_match('/^https?:\/\//', $url)) {
            return Tools::file_get_contents($url, $use_include_path, $stream_context);
        } else {
            return false;
        }
    }
    public function renderAdminBodyHtml()
    {
        $this->renderPaymentMethodForm();
        return $this->_html;
    }

    public function renderPaymentMethodForm()
    {
        if(Tools::isSubmit('addnewPayment') || Tools::isSubmit('editpayment'))
            return $this->renderFormPayment();
        $order_states = OrderState::getOrderStates($this->context->language->id);
        $fields_list = array(
            'id_ets_paymentmethod' => array(
                'title' => $this->l('Id'),
                'width' => 40,
                'type' => 'text',
                'filter' => true,
                'sort' => 'p.id_ets_paymentmethod',
            ),
            'logo_payment' => array(
                'title' => $this->l('Logo'),
                'width' => 57,
                'type' => 'text',
            ),
            'method_name' => array(
                'title' => $this->l('Payment method name'),
                'type' => 'text',
                'filter' => true,
                'sort' => 'pl.method_name',
            ),
            'fee' => array(
                'title' => $this->l('Fee'),
                'width' => 140,
                'type' => 'text',
            ),
            'fee_tax' => array(
                'title' => $this->l('Fee tax'),
                'width' => 140,
                'type' => 'text',
            ),
            'order_status' => array(
                'title' => $this->l('Order status'),
                'strip_tag' => false,
                'filter' => true,
                'type' => 'select',
                'filter_list' => array(
                    'id_option' => 'id_order_state',
                    'value' => 'name',
                    'list' => $order_states,
                )
            ),
            'position' => array(
                'title' => $this->l('Position'),
                'type' => 'text',
                'sort' => 'position',
                'update_position' => true,
            ),
            'active' => array(
                'title' => $this->l('Enabled'),
                'width' => 50,
                'type' => 'active',
                'strip_tag' => false,
                'filter' => true,
                'sort' => 'p.active',
                'filter_list' => array(
                    'id_option' => 'enabled',
                    'value' => 'title',
                    'list' => array(
                        0 => array(
                            'enabled' => 1,
                            'title' => $this->l('Yes')
                        ),
                        1 => array(
                            'enabled' => 0,
                            'title' => $this->l('No')
                        )
                    )
                )
            )
        );
        $payments = $this->getPaymentsWithFilter();
        if ($payments) {
            foreach ($payments as &$payment) {
                if ($payment['logo_payment'])
                    $payment['logo_payment'] = array(
                        'image_field' => true,
                        'img_url' => _PS_ETS_PAYMENT_FEE_IMG_ . $payment['logo_payment'],
                        'width' => 57
                    );
                else
                   $payment['logo_payment'] ='--'; 
                if ($payment['fee_type'] == 'fixed_fee')
                    $payment['fee'] = $payment['fee_amount'] == 0 ? $this->l('Free') : Tools::displayPrice($payment['fee_amount'] * $this->context->currency->conversion_rate) . ($payment['free_for_order_over'] ? '(Free for order over: ' . Tools::displayPrice((float)$payment['free_for_order_over'] * $this->context->currency->conversion_rate) . ')' : '');
                elseif ($payment['fee_type'] == 'percentage' || $payment['fee_type']=='percentage_fixed') {
                    $html_extra = '';
                    if ($payment['min_fee'])
                        $html_extra .= 'Minimum fee: ' . Tools::displayPrice((float)$payment['min_fee'] * $this->context->currency->conversion_rate) . ', ';
                    if ($payment['max_fee'])
                        $html_extra .= 'Maximum fee: ' . Tools::displayPrice((float)$payment['max_fee'] * $this->context->currency->conversion_rate) . ', ';
                    if ($payment['free_for_order_over'])
                        $html_extra .= 'Free for order over: ' . Tools::displayPrice((float)$payment['free_for_order_over'] * $this->context->currency->conversion_rate);
                    $payment['fee'] = $payment['percentage'] . '%' .($payment['fee_type']=='percentage_fixed' && $payment['fee_amount'] ? ' + '. Tools::displayPrice($payment['fee_amount'] * $this->context->currency->conversion_rate):'') . ($payment['max_fee'] || $payment['min_fee'] || $payment['free_for_order_over'] ? ' (' . trim($html_extra, ', ') . ')' : '');
                } else
                    $payment['fee'] = $this->l('Free');
                $payment['fee_tax'] = $payment['fee_type'] == 'free' || !$payment['fee_type'] ? '--' : ($payment['fee_tax'] ?: $this->l('No tax'));
                $payment['order_status'] = $this->displayOrderStatus($payment['order_status']);
            }
        }
        $order_status = trim(Tools::getValue('order_status'));
        $id_ets_paymentmethod = Tools::getValue('id_ets_paymentmethod');
        $method_name = Tools::getValue('method_name');
        $active = Tools::getValue('active');
        $sort_type = Tools::getValue('sort_type', 'asc');
        if(!in_array($sort_type,array('desc','asc')))
            $sort_type = 'desc';
        $sort = Tools::getValue('sort', 'position');
        if(!in_array($sort,array('position','order_status','id_ets_paymentmethod','method_name','active')))
            $sort ='position';
        $listData = array(
            'name' => 'payment',
            'actions' => array('edit', 'delete', 'view'),
            'currentIndex' => $this->context->link->getAdminLink('AdminModules', true) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name . '&control=payment_method',
            'identifier' => 'id_ets_paymentmethod',
            'show_action' => true,
            'title' => $this->l('Custom payment methods'),
            'fields_list' => $fields_list,
            'field_values' => $payments,
            'show_toolbar' => true,
            'sort' => $sort,
            'sort_type' => $sort_type,
            'filter_params' => $this->getFilterParams($fields_list),
            'show_reset' => ($order_status != '' && Validate::isCleanHtml($order_status)) || (trim($id_ets_paymentmethod) != '' && Validate::isCleanHtml($id_ets_paymentmethod) && !Tools::isSubmit('del')) || (trim($method_name) != '' && Validate::isCleanHtml($method_name)) || (trim($active) != '' && Validate::isCleanHtml($active)) ? true : false,
            'show_add_new' => true,
        );
        $this->_html .= $this->renderList($listData);
        $this->renderFormConfig();
    }
    public function renderFormPayment()
    {
        $order_status_default = array(
            array(
                'id_order_state' => '',
                'name' => '--',
                'color' => '#ffffff',
            )
        );
        $order_status = OrderState::getOrderStates($this->context->language->id);
        $order_status = array_merge($order_status_default, $order_status);
        if ($id_ets_paymentmethod = (int)Tools::getValue('id_ets_paymentmethod'))
        {
            if(Ets_paymentmethod_class::checkExists($id_ets_paymentmethod))
                $paymentmethod = new Ets_paymentmethod_class($id_ets_paymentmethod, $this->context->language->id);
            else
                return $this->_html .= $this->display(__FILE__,'no_payment.tpl');
        }
        else
            $paymentmethod = new Ets_paymentmethod_class();
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => ($id_ets_paymentmethod ? $this->l('Edit') : $this->l('Add new')) . ' ' . $this->l('payment method') . ($id_ets_paymentmethod ? ' #' . $id_ets_paymentmethod : ''),
                    'icon' => 'icon-cogs'
                ),
                'input' => array(
                    array(
                        'name' => 'method_name',
                        'type' => 'text',
                        'label' => $this->l('Payment method name'),
                        'required' => true,
                        'lang' => true,
                    ),
                    array(
                        'name' => 'description',
                        'type' => 'textarea',
                        'label' => $this->l('Description'),
                        'lang' => true,
                        'desc' => $this->l('This text will be displayed below payment method name on checkout page. Custom variable: ') . $this->display(__FILE__, 'varible_description.tpl'),
                    ),
                    array(
                        'name' => 'confirmation_message',
                        'label' => $this->l('Confirmation message'),
                        'type' => 'textarea',
                        'lang' => true,
                        'desc' => $this->l('This message appears on order confirmation page (the page that customer is redirected to when they complete their order). Custom variable: ') . $this->display(__FILE__, 'varible_confirmation_message.tpl'),
                    ),
                    array(
                        'name' => 'return_message',
                        'label' => $this->l('Return message'),
                        'type' => 'textarea',
                        'lang' => true,
                        'autoload_rte' => true,
                        'desc' => $this->l('This message appears on order confirmation page (the page that customer is redirected to when they complete their order). Custom variable: ') . $this->display(__FILE__, 'varible_return_message.tpl'),
                    ),
                    array(
                        'name' => 'logo_payment',
                        'type' => 'file',
                        'label' => $this->l('Payment logo'),
                        'desc' => sprintf($this->l('Accepted formats: jpg, png, gif. Limit: %sMb Recommended size: 57X57 (px)'), Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE')),
                        'image' => $paymentmethod->logo_payment
                            ? Ets_payment_utils::html([
                                'tag' => 'img',
                                'atts' => [
                                    'width' => 86,
                                    'height' => 49,
                                    'src' => _PS_ETS_PAYMENT_FEE_IMG_ . $paymentmethod->logo_payment,
                                    'alt' => $paymentmethod->method_name,
                                    'title' => $paymentmethod->method_name
                                ]
                            ])
                            : null,
                        'delete_url' => defined('PS_ADMIN_DIR') ? 'index.php?controller=AdminModules&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name . '&delete_payment_logo=1&control=payment_method&id_ets_paymentmethod=' . (int)$paymentmethod->id . '&token=' . Tools::getAdminToken('AdminModules' . (int)(Tab::getIdFromClassName('AdminModules')) . (int)$this->context->employee->id) : '',
                    ),
                    array(
                        'name' => 'order_status',
                        'type' => 'select',
                        'label' => $this->l('Order status'),
                        'required' => true,
                        'options' => array(
                            'query' => $order_status,
                            'id' => 'id_order_state',
                            'name' => 'name',
                        ),
                        'desc' => $this->l('Order status to be set when customer selects this payment method '),
                    ),
                    array(
                        'name' => 'fee_type',
                        'type' => 'select',
                        'label' => $this->l('Fee type'),
                        'options' => array(
                            'query' => array(
                                array(
                                    'id' => 'free',
                                    'name' => $this->l('Free (no payment fee)')
                                ),
                                array(
                                    'id' => 'fixed_fee',
                                    'name' => $this->l('Fixed amount'),
                                ),
                                array(
                                    'name' => $this->l('Percentage'),
                                    'id' => 'percentage'
                                ),
                                array(
                                    'name' => $this->l('Percentage + Fixed amount'),
                                    'id' => 'percentage_fixed',
                                ),
                            ),
                            'id' => 'id',
                            'name' => 'name',
                        ),
                    ),
                    array(
                        'name' => 'fee_amount',
                        'label' => $this->l('Fee amount'),
                        'type' => 'text',
                        'required' => true,
                        'form_group_class' => 'custom fixed_fee percentage_fixed',
                        'suffix' => $this->context->currency->iso_code,
                    ),
                    array(
                        'name' => 'percentage',
                        'label' => $this->l('Percentage'),
                        'type' => 'text',
                        'required' => true,
                        'form_group_class' => 'custom percentage percentage_fixed',
                        'suffix' => '%',
                        'desc' => $this->l('Tax and shipping cost included before calculating payment fee'),
                    ),
                    array(
                        'name' => 'fee_based_on',
                        'type' => 'radio',
                        'label' => $this->l('Calculate fee based on'),
                        'values' => array(
                            array(
                                'label' => $this->l('Total (tax include)'),
                                'id' => 'fee_based_on_1',
                                'value' => '1'
                            ),
                            array(
                                'label' => $this->l('Total (tax exclude)'),
                                'id' => 'fee_based_on_0',
                                'value' => '0'
                            ),
                        ),
                        'form_group_class' => 'custom percentage percentage_fixed',
                        'default' => '0',
                    ),
                    array(
                        'name' => 'id_tax_rules_group',
                        'type' => 'select',
                        'label' => $this->l('Fee tax'),
                        'options' => array(
                            'query' => TaxRulesGroup::getTaxRulesGroupsForOptions(),
                            'id' => 'id_tax_rules_group',
                            'name' => 'name'
                        ),
                        'form_group_class' => 'custom  percentage fixed_fee percentage_fixed',
                        'default' => '0',
                    ),
                    array(
                        'name' => 'max_fee',
                        'label' => $this->l('Maximum fee'),
                        'type' => 'text',
                        'form_group_class' => 'custom percentage percentage_fixed',
                        'suffix' => $this->context->currency->iso_code,
                        'desc' => $this->l('Leave blank to ignore this limit'),
                    ),
                    array(
                        'name' => 'min_fee',
                        'label' => $this->l('Minimum fee'),
                        'type' => 'text',
                        'form_group_class' => 'custom percentage percentage_fixed',
                        'suffix' => $this->context->currency->iso_code,
                        'desc' => $this->l('Leave blank to ignore this limit'),
                    ),
                    array(
                        'name' => 'free_for_order_over',
                        'label' => $this->l('Free for order over'),
                        'type' => 'text',
                        'form_group_class' => 'custom percentage fixed_fee percentage_fixed',
                        'suffix' => $this->context->currency->iso_code,
                        'desc' => $this->l('Tax and shipping cost included. Leave blank to apply payment fee for all orders'),
                    ),
                    array(
                        'label' => $this->l('Minimum total order value'),
                        'type' => 'text',
                        'name' => 'minimum_order',
                        'suffix' => $this->context->currency->iso_code,
                        'desc' => $this->l('This payment fee is only available if total order value satisfies this condition. Leave blank to ignore this condition.'),
                    ),
                    array(
                        'label' => $this->l('Maximum total order value'),
                        'type' => 'text',
                        'name' => 'maximum_order',
                        'suffix' => $this->context->currency->iso_code,
                        'desc' => $this->l('This payment fee is only available if total order value satisfies this condition. Leave blank to ignore this condition.'),
                    ),
                    array(
                        'label' => $this->l('Customer groups'),
                        'type' => 'select',
                        'name' => 'customer_group',
                        'id' => 'customer_group',
                        'multiple' => true,
                        'options' => array(
                            'query' => array_merge(array(array('id_group' => '0', 'name' => $this->l('All'))), Group::getGroups($this->context->language->id) ),
                            'id' => 'id_group',
                            'name' => 'name'
                        ),
                        'desc' => $this->l('This payment method is available for these customer groups'),),
                    array(
                        'label' => $this->l('Countries'),
                        'type' => 'select',
                        'name' => 'countries',
                        'id' => 'countries',
                        'multiple' => true,
                        'options' => array(
                            'query' => array_merge(array(array('id_country' => '0', 'name' => $this->l('All'))), Country::getCountries($this->context->language->id, true)),
                            'id' => 'id_country',
                            'name' => 'name'
                        ),
                        'desc' => $this->l('This payment method is available for these countries'),
                    ),
                    array(
                        'label' => $this->l('Carriers'),
                        'type' => 'select',
                        'name' => 'carriers',
                        'multiple' => true,
                        'id' => 'carriers',
                        'options' => array(
                            'query' => array_merge(array(array('id_reference' => '0', 'name' => $this->l('All'))), $this->getcarriers()),
                            'id' => 'id_reference',
                            'name' => 'name'
                        ),
                        'desc' => $this->l('This payment method is available for these carriers'),
                    ),
                    array(
                        'name' => 'active',
                        'label' => $this->l('Active'),
                        'type' => 'switch',
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        ),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
                'buttons' => array(
                    array(
                        'href' => $this->context->link->getAdminLink('AdminModules', true) . '&configure=ets_payment_with_fee&tab_module=payments_gateways&module_name=ets_payment_with_fee&list=true',
                        'icon' => 'process-icon-cancel',
                        'title' => $this->l('Back'),
                    )
                )
            ),
        );
        if ($id_ets_paymentmethod)
            $fields_form['form']['input'][] = array('type' => 'hidden', 'name' => 'id_ets_paymentmethod');
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $this->fields_form = array();
        $helper->module = $this;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'savePaymentMethod';
        $helper->currentIndex ='';
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $language = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->override_folder = '/';
        $helper->tpl_vars = array(
            'base_url' => $this->context->shop->getBaseURL(),
            'language' => array(
                'id_lang' => $language->id,
                'iso_code' => $language->iso_code
            ),
            'fields_value' => $this->getFiledPaymentMethod(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
            'cancel_url' => $this->baseAdminPath . '&control=payment_method&list=true',
            'currencies' => Currency::getCurrencies(),
            'defaultFormCurrency' => (int)Configuration::get('PS_CURRENCY_DEFAULT'),
            'add_new' => $id_ets_paymentmethod ? true : false,
            'image_baseurl' => _PS_ETS_PAYMENT_FEE_IMG_,
        );
        $this->_html .= $helper->generateForm(array($fields_form));
    }
    public function renderListModules()
    {
        $id_module = (int)Tools::getValue('id_module');
        if ($id_module && !Tools::isSubmit('ets_payment_submit_module')) {
            $modules = $this->getModuleWithFilter(' AND pm.id_shop ="'.(int)$this->context->shop->id.'" AND m.id_module=' . (int)$id_module);
            if(!$modules)
                $modules = $this->getModuleWithFilter(' AND m.id_module=' . (int)$id_module);
            if ($modules) {
                $module = $modules[0];
                $fields_form = array(
                    'form' => array(
                        'legend' => array(
                            'title' => $this->l('Edit payment fee: ') . $module['name'],
                            'icon' => 'icon-cogs'
                        ),
                        'input' => array(
                            array(
                                'name' => 'fee_type',
                                'type' => 'select',
                                'label' => $this->l('Fee type'),
                                'options' => array(
                                    'query' => array(
                                        array(
                                            'id' => 'free',
                                            'name' => $this->l('Free (no payment fee)')
                                        ),
                                        array(
                                            'id' => 'fixed_fee',
                                            'name' => $this->l('Fixed amount '),
                                        ),
                                        array(
                                            'name' => $this->l('Percentage'),
                                            'id' => 'percentage'
                                        ),
                                        array(
                                            'name' => $this->l('Percentage + Fixed amount'),
                                            'id' => 'percentage_fixed'
                                        )
                                    ),
                                    'id' => 'id',
                                    'name' => 'name',
                                ),
                            ),
                            array(
                                'name' => 'fee_amount',
                                'label' => $this->l('Fee amount'),
                                'type' => 'text',
                                'required' => true,
                                'form_group_class' => 'custom fixed_fee percentage_fixed',
                                'suffix' => $this->context->currency->iso_code,
                            ),
                            array(
                                'name' => 'percentage',
                                'label' => $this->l('Percentage'),
                                'type' => 'text',
                                'required' => true,
                                'form_group_class' => 'custom percentage percentage_fixed',
                                'suffix' => '%',
                                'desc' => $this->l('Tax and shipping cost included before calculating payment fee'),
                            ),
                            array(
                                'name' => 'fee_based_on',
                                'type' => 'radio',
                                'label' => $this->l('Calculate fee based on'),
                                'values' => array(
                                    array(
                                        'label' => $this->l('Total (tax include)'),
                                        'id' => 'fee_based_on_1',
                                        'value' => '1'
                                    ),
                                    array(
                                        'label' => $this->l('Total (tax exclude)'),
                                        'id' => 'fee_based_on_0',
                                        'value' => '0'
                                    ),
                                ),
                                'form_group_class' => 'custom percentage percentage_fixed',
                                'default' => '0',
                            ),
                            array(
                                'name' => 'id_tax_rules_group',
                                'type' => 'select',
                                'label' => $this->l('Fee tax'),
                                'options' => array(
                                    'query' => TaxRulesGroup::getTaxRulesGroupsForOptions(),
                                    'id' => 'id_tax_rules_group',
                                    'name' => 'name'
                                ),
                                'form_group_class' => 'custom  percentage fixed_fee percentage_fixed',
                                'default' => '0',
                            ),
                            array(
                                'name' => 'max_fee',
                                'label' => $this->l('Maximum fee'),
                                'type' => 'text',
                                'form_group_class' => 'custom percentage percentage_fixed',
                                'suffix' => $this->context->currency->iso_code,
                                'desc' => $this->l('Leave blank to ignore this limit'),
                            ),
                            array(
                                'name' => 'min_fee',
                                'label' => $this->l('Minimum fee'),
                                'type' => 'text',
                                'form_group_class' => 'custom percentage percentage_fixed',
                                'suffix' => $this->context->currency->iso_code,
                                'desc' => $this->l('Leave blank to ignore this limit'),
                            ),
                            array(
                                'name' => 'free_for_order_over',
                                'label' => $this->l('Free for order over'),
                                'type' => 'text',
                                'form_group_class' => 'custom percentage fixed_fee',
                                'suffix' => $this->context->currency->iso_code,
                                'desc' => $this->l('Tax and shipping cost included. Leave blank to apply payment fee for all orders'),
                            ),
                            array(
                                'label' => $this->l('Minimum total order value'),
                                'type' => 'text',
                                'name' => 'minimum_order',
                                'suffix' => $this->context->currency->iso_code,
                                'desc' => $this->l('This payment fee is only available if total order value satisfies this condition. Leave blank to ignore this condition.'),
                            ),
                            array(
                                'label' => $this->l('Maximum total order value'),
                                'type' => 'text',
                                'name' => 'maximum_order',
                                'suffix' => $this->context->currency->iso_code,
                                'desc' => $this->l('This payment fee is only available if total order value satisfies this condition. Leave blank to ignore this condition.'),
                            ),


                        ),
                        'submit' => array(
                            'title' => $this->l('Save'),
                        ),
                        'buttons' => array(
                            array(
                                'href' => $this->context->link->getAdminLink('AdminPaymentFee', true),
                                'icon' => 'process-icon-cancel',
                                'title' => $this->l('Back'),
                            )
                        )
                    ),
                );
                $fields_form['form']['input'][] = array('type' => 'hidden', 'name' => 'id_module');
                $helper = new HelperForm();
                $helper->show_toolbar = false;
                $helper->table = $this->table;
                $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
                $helper->default_form_language = $lang->id;
                $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
                $this->fields_form = array();
                $helper->module = $this;
                $helper->identifier = $this->identifier;
                $helper->submit_action = 'saveModulePaymentFee';
                $helper->currentIndex = $this->context->link->getAdminLink('AdminPaymentFee', false);
                $helper->token = Tools::getAdminTokenLite('AdminPaymentFee');
                $language = new Language((int)Configuration::get('PS_LANG_DEFAULT'));

                $helper->tpl_vars = array(
                    'base_url' => $this->context->shop->getBaseURL(),
                    'language' => array(
                        'id_lang' => $language->id,
                        'iso_code' => $language->iso_code
                    ),
                    'fields_value' => $this->getFieldModulePaymentFee($module),
                    'languages' => $this->context->controller->getLanguages(),
                    'id_language' => $this->context->language->id,
                    'cancel_url' => $this->context->link->getAdminLink('AdminPaymentFee', true),
                    'currencies' => Currency::getCurrencies(),
                    'defaultFormCurrency' => (int)Configuration::get('PS_CURRENCY_DEFAULT'),
                    'add_new' => true,
                    'image_baseurl' => _PS_ETS_PAYMENT_FEE_IMG_,
                );

                return $helper->generateForm(array($fields_form));
            }
        }
        $fields_list = array(
            'id_module' => array(
                'title' => $this->l('ID'),
                'width' => 40,
                'type' => 'text',
                'filter' => true,
                'sort' => 'm.id_module',
            ),
            'logo' => array(
                'title' => $this->l('Logo'),
                'width' => 57,
                'type' => 'text',
                'image_field' => true,
            ),
            'name' => array(
                'title' => $this->l('Payment method'),
                'type' => 'text',
            ),
            'fee' => array(
                'title' => $this->l('Fee'),
                'width' => 140,
                'type' => 'text',
            ),
            'fee_tax' => array(
                'title' => $this->l('Fee tax'),
                'width' => 140,
                'type' => 'text',
            ),
        );
        $filter = '';
        $show_reset = false;
        if (Tools::isSubmit('ets_payment_submit_module')) {
            if (($id_module = Tools::getValue('id_module')) || $id_module!='')
            {
                if(Validate::isInt($id_module))
                    $filter .= ' AND m.id_module = "' . (int)$id_module . '"';
                $show_reset = true;
            }
            if (($name= Tools::getValue('name')) || $name!='')
            {
                if(Validate::isCleanHtml($name))
                    $filter .= ' AND m.name like "' . pSQL($name) . '%"';
                $show_reset = true;
            }

        }
        $modules = $this->getModuleWithFilter($filter);
        if ($modules) {
            foreach ($modules as $key => &$module) {
                $module['logo'] = array(
                    'image_field' => true,
                    'img_url' => $this->_path . '../' . $module['name'] . '/logo.png',
                    'width' => 57
                );
                if ($module['fee_type'] == 'fixed_fee')
                    $module['fee'] = $module['fee_amount'] == 0 ? $this->l('Free') : Tools::displayPrice($module['fee_amount']) . ($module['free_for_order_over'] ? ' (Free for order over: ' . Tools::displayPrice($module['free_for_order_over'] * $this->context->currency->conversion_rate) . ')' : '');
                elseif ($module['fee_type'] == 'percentage' || $module['fee_type']=='percentage_fixed') {
                    $html_extra = '';
                    if ($module['min_fee'])
                        $html_extra .= 'Minimum fee: ' . Tools::displayPrice((float)$module['min_fee'] * $this->context->currency->conversion_rate) . ', ';
                    if ($module['max_fee'])
                        $html_extra .= 'Maximum fee: ' . Tools::displayPrice((float)$module['max_fee'] * $this->context->currency->conversion_rate) . ', ';
                    if ($module['free_for_order_over'])
                        $html_extra .= 'Free for order over: ' . Tools::displayPrice((float)$module['free_for_order_over'] * $this->context->currency->conversion_rate);
                    $module['fee'] = $module['percentage'] . '%' .($module['fee_type']=='percentage_fixed' && $module['fee_amount'] ? ' + '.Tools::displayPrice($module['fee_amount']):'' ). ($module['max_fee'] || $module['min_fee'] || $module['free_for_order_over'] ? ' (' . trim($html_extra, ', ') . ')' : '');
                } else
                    $module['fee'] = $this->l('Free');
                $module['fee_tax'] = $module['fee_type'] == 'free' || !$module['fee_type'] ? '--' : ($module['fee_tax'] ?: $this->l('No tax'));
                $module_class = Module::getInstanceByName($module['name']);
                if ($module_class) {
                    $module['name'] = $module_class->displayName;
                } else
                    unset($modules[$key]);

            }
        }
        $sort = Tools::getValue('sort', 'id_module');
        if(!in_array($sort,array('id_module','name')))
            $sort = 'id_module';
        $sort_type = Tools::getValue('sort_type', 'asc');
        if(!in_array($sort_type,array('asc','desc')))
            $sort_type ='desc';
        $listData = array(
            'name' => 'module',
            'actions' => array('edit'),
            'currentIndex' => $this->context->link->getAdminLink('AdminPaymentFee', true),
            'identifier' => 'id_module',
            'show_action' => true,
            'title' => $this->l('Payment fees'),
            'fields_list' => $fields_list,
            'field_values' => $modules,
            'show_toolbar' => false,
            'sort' => $sort,
            'sort_type' => $sort_type,
            'filter_params' => $this->getFilterParams($fields_list),
            'show_reset' => $show_reset,
            'show_add_new' => false,
        );
        return $this->renderList($listData).$this->renderFormConfig();
    }

    public function getcarriers()
    {
        $carriers = Carrier::getCarriers($this->context->language->id,true,false,false,null,Carrier::ALL_CARRIERS);
        if ($carriers)
            foreach ($carriers as &$carrier) {
                if ($carrier['name'] == '0')
                    $carrier['name'] = $this->l('Demo shop');
            }
        return $carriers;
    }

    public function getFiledPaymentMethod()
    {
        if ($id_ets_paymentmethod = (int)Tools::getValue('id_ets_paymentmethod'))
            $paymentmethod = new Ets_paymentmethod_class($id_ets_paymentmethod);
        else
            $paymentmethod = new Ets_paymentmethod_class();
        $languages = Language::getLanguages(false);
        $fields = array();
        foreach ($languages as $language) {
            $fields['method_name'][$language['id_lang']] = Tools::getValue('method_name_' . $language['id_lang'], isset($paymentmethod->method_name[$language['id_lang']]) ? $paymentmethod->method_name[$language['id_lang']] :'');
            $fields['description'][$language['id_lang']] = Tools::getValue('description_' . $language['id_lang'], isset($paymentmethod->description[$language['id_lang']]) ? $paymentmethod->description[$language['id_lang']] :'');
            $fields['confirmation_message'][$language['id_lang']] = Tools::getValue('confirmation_message_' . $language['id_lang'], isset($paymentmethod->confirmation_message[$language['id_lang']]) ? $paymentmethod->confirmation_message[$language['id_lang']] :'');
            $fields['return_message'][$language['id_lang']] = Tools::getValue('return_message_' . $language['id_lang'], isset($paymentmethod->return_message[$language['id_lang']]) ? $paymentmethod->return_message[$language['id_lang']] :'');
        }
        $fields['customer_group[]'] = !$paymentmethod->id ? array('0') : (Tools::getValue('customer_group', $paymentmethod->customer_group ? explode(',', $paymentmethod->customer_group) : flae) ? Tools::getValue('customer_group', $paymentmethod->customer_group ? explode(',', $paymentmethod->customer_group) : false) : array());
        $fields['countries[]'] = !$paymentmethod->id ? array('0') : (Tools::getValue('countries', $paymentmethod->countries ? explode(',', $paymentmethod->countries) : false) ? Tools::getValue('countries', $paymentmethod->countries ? explode(',', $paymentmethod->countries) : false) : array());
        $fields['carriers[]'] = !$paymentmethod->id ? array('0') : (Tools::getValue('carriers', $paymentmethod->carriers ? explode(',', $paymentmethod->carriers) : false) ? Tools::getValue('carriers', $paymentmethod->carriers ? explode(',', $paymentmethod->carriers) : false) : array());
        $fields['fee_type'] = Tools::getValue('fee_type', $paymentmethod->fee_type);
        $fields['order_status'] = Tools::getValue('order_status', $paymentmethod->order_status);
        $fields['active'] = !$paymentmethod->id ? 1 : Tools::getValue('active', $paymentmethod->active);
        $fields['fee_amount'] = Tools::getValue('fee_amount', $paymentmethod->fee_amount != '' ? $paymentmethod->fee_amount * $this->context->currency->conversion_rate : '');
        $fields['minimum_order'] = Tools::getValue('minimum_order', $paymentmethod->minimum_order != '' ? $paymentmethod->minimum_order * $this->context->currency->conversion_rate : '');
        $fields['maximum_order'] = Tools::getValue('maximum_order', $paymentmethod->maximum_order != '' ? $paymentmethod->maximum_order * $this->context->currency->conversion_rate : '');
        $fields['percentage'] = Tools::getValue('percentage', $paymentmethod->percentage);
        $fields['max_fee'] = Tools::getValue('max_fee', $paymentmethod->max_fee != '' ? $paymentmethod->max_fee * $this->context->currency->conversion_rate : '');
        $fields['min_fee'] = Tools::getValue('min_fee', $paymentmethod->min_fee != '' ? $paymentmethod->min_fee * $this->context->currency->conversion_rate : '');
        $fields['free_for_order_over'] = Tools::getValue('free_for_order_over', $paymentmethod->free_for_order_over != '' ? $paymentmethod->free_for_order_over * $this->context->currency->conversion_rate : '');
        if ($id_ets_paymentmethod = (int)Tools::getValue('id_ets_paymentmethod'))
            $fields['id_ets_paymentmethod'] = $id_ets_paymentmethod;
        $fields['fee_based_on'] = $paymentmethod->id ? (int)$paymentmethod->fee_based_on : 1;
        $fields['id_tax_rules_group'] = $paymentmethod->id_tax_rules_group;
        return $fields;
    }

    public function getFieldModulePaymentFee($module)
    {
        $fields = array();
        $fields['fee_type'] = Tools::getValue('fee_type', $module['fee_type']);
        $fields['fee_amount'] = Tools::getValue('fee_amount', $module['fee_amount'] != '' ? $module['fee_amount'] * $this->context->currency->conversion_rate : '');
        $fields['percentage'] = Tools::getValue('percentage', $module['percentage']);
        $fields['max_fee'] = Tools::getValue('max_fee', $module['max_fee'] != '' ? $module['max_fee'] * $this->context->currency->conversion_rate : '');
        $fields['min_fee'] = Tools::getValue('min_fee', $module['min_fee'] != '' ? $module['min_fee'] * $this->context->currency->conversion_rate : '');
        $fields['free_for_order_over'] = Tools::getValue('free_for_order_over', $module['free_for_order_over'] != '' ? $module['free_for_order_over'] * $this->context->currency->conversion_rate : '');
        $fields['id_module'] = $module['id_module'];
        $fields['minimum_order'] = Tools::getValue('minimum_order', $module['minimum_order']);
        $fields['maximum_order'] = Tools::getValue('maximum_order', $module['maximum_order']);
        $fields['id_tax_rules_group'] = Tools::getValue('id_tax_rules_group', $module['id_tax_rules_group']);
        $fields['fee_based_on'] = Tools::getValue('fee_based_on', (int)$module['fee_based_on']);
        return $fields;
    }
    public function UploadImage($key, &$errors = array())
    {
        if(!is_dir(_PS_ETS_PAYMENT_FEE_IMG_DIR_))
            mkdir(_PS_ETS_PAYMENT_FEE_IMG_DIR_,'0755');
        if (isset($_FILES[$key]['tmp_name']) && isset($_FILES[$key]['name']) && $_FILES[$key]['name']) {
            if (!Validate::isFileName($_FILES[$key]['name']))
                $errors[] = $this->l('File name is not valid');
            $type = Tools::strtolower(Tools::substr(strrchr($_FILES[$key]['name'], '.'), 1));
            $imageName = str_replace(' ', '-', $_FILES[$key]['name']);
            $fileName = _PS_ETS_PAYMENT_FEE_IMG_DIR_. $imageName;
            if (file_exists($fileName)) {
                $imageName = $this->genSecure(5) . '-' . $imageName;
                $fileName = _PS_ETS_PAYMENT_FEE_IMG_DIR_ . $imageName;
            }
            $imagesize = @getimagesize($_FILES[$key]['tmp_name']);
            if (!$errors && isset($_FILES[$key]) &&
                !empty($_FILES[$key]['tmp_name']) &&
                !empty($imagesize) &&
                in_array($type, array('jpg', 'gif', 'jpeg', 'png'))
            ) {
                $temp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS');
                if ($error = ImageManager::validateUpload($_FILES[$key], Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE') * 1024 * 1024))
                    $errors[] = $error;
                elseif (!$temp_name || !move_uploaded_file($_FILES[$key]['tmp_name'], $temp_name))
                    $errors[] = $this->l('Can not upload the file');
                elseif (!ImageManager::resize($temp_name, $fileName, null, null, $type))
                    $errors[] = $this->displayError($this->l('An error occurred during the image upload process.'));
                if (isset($temp_name) && file_exists($temp_name))
                    @unlink($temp_name);
                if (!$errors) {
                    return $imageName;
                }
            } elseif (!in_array($type, array('jpg', 'gif', 'jpeg', 'png')))
                $errors[] = $this->l('Logo is not valid');
        }
        return '';
    }

    public function getPaymentsWithFilter()
    {
        $filter = '';
        if (Tools::isSubmit('ets_payment_submit_payment')) {
            if ((($id_ets_paymentmethod = Tools::getValue('id_ets_paymentmethod')) || $id_ets_paymentmethod!='') && Validate::isInt($id_ets_paymentmethod))
                $filter .= ' AND p.id_ets_paymentmethod="' . (int)$id_ets_paymentmethod . '"';
            if ((($method_name = trim(Tools::getValue('method_name')))  || $method_name!= '') && Validate::isCleanHtml($method_name))
                $filter .= ' AND pl.method_name like "%' . pSQL($method_name) . '%"';
            if ((($active=  Tools::getValue('active')) || $active != '') && Validate::isInt($active))
                $filter .= ' AND p.active ="' . (int)$active . '"';
            if ((($order_status = Tools::getValue('order_status')) || $order_status != '') && Validate::isInt($order_status))
                $filter .= ' AND p.order_status="' . (int)$order_status . '"';
        }
        $sort  = Tools::getValue('sort', 'position');
        if(!Validate::isCleanHtml($sort) || !in_array($sort,array('position','id_ets_paymentmethod','method_name','active','order_status')))
            $sort= 'position';
        $sort_type = Tools::getValue('sort_type', 'asc');
        if(!in_array($sort_type,array('desc','asc')))
            $sort_type = 'asc';
        return Ets_paymentmethod_class::getPayments($filter,$sort,$sort_type);
    }

    public function getUrlExtra($field_list)
    {
        $params = '';
        $sort = trim(Tools::getValue('sort'));
        $sort_type = Tools::getValue('sort_type','desc');
        if(!in_array($sort_type,array('desc','asc')))
            $sort_type='desc';
        if ($sort && isset($field_list[$sort])) {
            $params .= '&sort=' . $sort . '&sort_type=' . $sort_type;
        }
        if ($field_list) {
            foreach ($field_list as $key => $val) {
                $value = Tools::getValue($key);
                if ($value != '' && Validate::isCleanHtml($value)) {
                    $params .= '&' . $key . '=' . urlencode($value);
                }
            }
            unset($val);
        }
        return $params;
    }

    public function renderList($listData)
    {
        if (isset($listData['fields_list']) && $listData['fields_list']) {
            foreach ($listData['fields_list'] as $key => &$val) {
                $value = Tools::getValue($key);
                $val['active'] = Validate::isCleanHtml($value) ? trim($value):'';
            }
        }
        $this->context->smarty->assign($listData);
        return $this->display(__FILE__, 'list_helper.tpl');
    }

    public function getFilterParams($field_list)
    {
        $params = '';
        if ($field_list) {
            foreach ($field_list as $key => $val) {
                $value = Tools::getValue($key);
                if ($value != '' && Validate::isCleanHtml($value)) {
                    $params .= '&' . $key . '=' . urlencode($value);
                }
            }
            unset($val);
        }
        return $params;
    }

    public static function displayPaymentMethodCustom()
    {
        $context = Context::getContext();
        $context->smarty->assign(
            array(
                'paymentmethods' => Ets_paymentmethod_class::getPayments(' AND p.active=1','position','ASC'),
            )
        );
        return $context->smarty->fetch(_PS_MODULE_DIR_ . 'ets_payment_with_fee/views/templates/hook/admin_payments.tpl');
    }

    public function hookPaymentOptions($params)
    {
        if (!$this->active) {
            return;
        }
        $type_checkout_options = Tools::getValue('type_checkout_options','create');
        $id_customer = ($this->context->customer->id) ? (int)($this->context->customer->id) : 0;
        if ($id_customer) {
            $id_group = Customer::getDefaultGroupId((int)$id_customer);
        }
        elseif($type_checkout_options=='guest' || $type_checkout_options=='create')
        {
            if($type_checkout_options=='guest')
            {
                $id_group = (int)Configuration::get('PS_GUEST_GROUP');
            }
            elseif($type_checkout_options=='create')
            {
                $id_group = (int)Configuration::get('PS_CUSTOMER_GROUP');
            }
        }
        if (!isset($id_group)) {
            $id_group = (int)Group::getCurrent()->id;
        }
        $tax = $this->checkDisplayTax($id_customer);
        $this->context->smarty->assign('tax_incl', $tax);
        $id_carrier = (int)$this->context->cart->id_carrier;
        $id_address_delivery = (int)$this->context->cart->id_address_delivery;
        $address_type  = Tools::getValue('address_type');
        if($address_type=='shipping_address')
            $id_address_delivery = (int)Tools::getValue('id_address',$id_address_delivery);
        $totalorder = $this->context->cart->getOrderTotal(true,Cart::BOTH,null,null,false,false,false,true);
        $this->context->smarty->assign('totalOrder', Tools::displayPrice($totalorder));
        if ($paymentmethods = Ets_paymentmethod_class::getPayments(' AND p.active=1','position','ASC')) {
            $priceFormatter = new PrestaShop\PrestaShop\Adapter\Product\PriceFormatter();
            foreach ($paymentmethods as $key => &$paymentmethod) {
                if ($this->checkPaymentMethod($paymentmethod, $id_group, $id_carrier, $id_address_delivery, $totalorder)) {

                    if ($paymentmethod['fee_type'] == 'free' || ($totalorder > $paymentmethod['free_for_order_over'] && $paymentmethod['free_for_order_over'] != 0)) {
                        $payment_fee = 0;
                    } else {
                        if ($paymentmethod['fee_type'] == 'fixed_fee') {
                            $payment_fee = $paymentmethod['fee_amount'];
                            $payment_fee = $payment_fee * $this->context->currency->conversion_rate;
                        } else {
                            if ($paymentmethod['fee_based_on'] == 1)
                                $total = $totalorder;
                            else
                                $total = $this->context->cart->getOrderTotal(false,Cart::BOTH,null,null,false,false,false,true);
                            $payment_fee = $paymentmethod['percentage'] * $total / 100;
                            if ($payment_fee > $paymentmethod['max_fee'] && $paymentmethod['max_fee']) {
                                $payment_fee = $paymentmethod['max_fee'];
                            } else if ($payment_fee < $paymentmethod['min_fee'] && $paymentmethod['min_fee']) {
                                $payment_fee = $paymentmethod['min_fee'];
                            }
                            if($paymentmethod['fee_type']=='percentage_fixed')
                            {
                                $payment_fee += $paymentmethod['fee_amount']; 
                            }
                            $payment_fee = $payment_fee * $this->context->currency->conversion_rate;
                        }
                        if ($tax && $paymentmethod['id_tax_rules_group'])
                            $payment_fee = $this->getPriceIncl($payment_fee, $paymentmethod['id_tax_rules_group']);
                    }
                    $paymentmethod['fee_price'] = $payment_fee ? $priceFormatter->format($payment_fee) : $this->l('Free');
                    $paymentmethod['fee'] = $payment_fee;
                    $paymentmethod['description'] = str_replace('[fee]', $paymentmethod['fee_price'], $paymentmethod['description']);
                } else
                    unset($paymentmethods[$key]);
            }
        }
        if (!$paymentmethods)
            return;
        unset($paymentmethod);
        $payment_options = array();
        $module = Tools::getValue('module');
        if ($paymentmethods) {
            foreach ($paymentmethods as $paymentmethod) {
                $this->context->smarty->assign(
                    array(
                        'paymentMethod' => $paymentmethod,
                        'link_module' => $this->_path,
                    )
                );
                $newOption = new PrestaShop\PrestaShop\Core\Payment\PaymentOption;
                $newOption->setModuleName($module =='ets_onepagecheckout' ? $this->name.'_'.$paymentmethod['id_ets_paymentmethod']: $this->name)
                    ->setCallToActionText($paymentmethod['method_name'])
                    ->setAction($this->context->link->getModuleLink($this->name, 'validation', array('id_payment_method' => $paymentmethod['id_ets_paymentmethod']), true))
                    ->setAdditionalInformation($this->fetch('module:ets_payment_with_fee/views/templates/hook/ets_custom_payment_intro.tpl'))
                    ->setInputs(array(array('type' => 'hidden', 'name' => 'id_payment_method', 'value' => $paymentmethod['id_ets_paymentmethod'])));
                $payment_options[] = $newOption;
            }
        }
        return $payment_options;
    }

    public function hookPaymentReturn($params)
    {
        if (!$this->active) {
            return;
        }
        $id_order = (int)Tools::getValue('id_order');
        if (($order = new Order($id_order)) && Validate::isLoadedObject($order) && ($order_method = Ets_paymentmethod_class::getPaymentMethodByIdOrder($id_order)) && $order_method['id_paymentmethod']) {
            if ($order_method['fee'])
                $fee = Tools::displayPrice($order_method['fee']);
            else
                $fee = $this->l('Free');
            $paymentMothod = new Ets_paymentmethod_class($order_method['id_paymentmethod'], $this->context->language->id);
            $message = $paymentMothod->return_message;
            $currency = new Currency($order->id_currency);
            $search = array(
                '[payment_method]' => $order_method['method_name'],
                '[fee]' => $fee,
                '[email]' =>$this->context->customer->email,
                '[shop_name]' => Configuration::get('PS_SHOP_NAME'),
                '[order_reference]' => $order->reference,
                '[amount]' => Tools::displayPrice($order->total_paid, Validate::isLoadedObject($currency) ? $currency:null),
            );
            $message = str_replace(array_keys($search), $search, $message);
            return $message;
        }

    }

    public function checkPaymentMethod($paymentmethod, $id_group, $id_carrier, $id_address_delivery, $totalorder)
    {
        if (!is_array($paymentmethod))
            $paymentmethod = Ets_paymentmethod_class::getPaymentMethodByIdMethod($paymentmethod);
        if (!$paymentmethod)
            return false;
        $groups = explode(',', $paymentmethod['customer_group']);
        $countries = explode(',', $paymentmethod['countries']);
        $carriers = explode(',', $paymentmethod['carriers']);
        $address = new Address($id_address_delivery);
        $id_country = Tools::getValue('id_country',$address->id_country);
        if(!$id_country) 
            $id_country = (int)$this->context->country->id;
        $carrier = new Carrier($id_carrier);
        $id_reference = $carrier->id_reference;
        if (($groups[0] && !in_array($id_group, $groups)) || ($countries[0] && !in_array($id_country, $countries)) || ($carriers[0] && !in_array($id_reference, $carriers)) || ($paymentmethod['minimum_order'] != '' && $paymentmethod['minimum_order'] > $totalorder) || ($paymentmethod['maximum_order'] != '' && $paymentmethod['maximum_order'] < $totalorder))
            return false;
        else
            return true;
    }

    public function getPriceIncl($price, $id_tax_group)
    {
        if ($id_tax_group) {
            $context = $this->context;
            if (is_object($context->cart) && $context->cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')} != null) {
                $id_address = $context->cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')};
                $address = new Address($id_address);
            } else {
                $address = new Address();
            }
            $address = Address::initialize($address->id, true);
            $tax_manager = TaxManagerFactory::getManager($address, $id_tax_group);
            $product_tax_calculator = $tax_manager->getTaxCalculator();
            $priceTax = $product_tax_calculator->addTaxes($price);
            return $priceTax;
        }
        return $price;
    }

    public function getFeePayment($id_payment_method, $products, $withTax = true)
    {
        if ($payment_method = Ets_paymentmethod_class::getPaymentMethodByIdMethod($id_payment_method)) {
            $totalorder = $this->context->cart->getOrderTotal(true, Cart::BOTH, $products, null, null, false,false, true);
            if ($payment_method['fee_type'] == 'free' || ($totalorder > $payment_method['free_for_order_over'] && $payment_method['free_for_order_over'] != 0))
                $payment_method['fee'] = 0;
            else {
                if ($payment_method['fee_type'] == 'fixed_fee') {
                    $payment_method['fee'] = $payment_method['fee_amount'];
                    $payment_method['fee'] = $payment_method['fee'] * $this->context->currency->conversion_rate;
                } else {
                    if ($payment_method['fee_based_on'] == 1)
                        $total = $totalorder;
                    else
                        $total = $this->context->cart->getOrderTotal(false, Cart::BOTH, $products, null, null,false, false, true);
                    $payment_method['fee'] = $payment_method['percentage'] * $total / 100;
                    $payment_method['fee'] = (float)$payment_method['fee'] / $this->context->currency->conversion_rate;
                    if($payment_method['fee_type']=='percentage_fixed')
                    {
                        $payment_method['fee'] += $payment_method['fee_amount'];
                    }
                    if ($payment_method['fee'] > $payment_method['max_fee'] && (float)$payment_method['max_fee'])
                        $payment_method['fee'] = $payment_method['max_fee'];
                    else if ($payment_method['fee'] < $payment_method['min_fee'] && (float)$payment_method['min_fee'])
                        $payment_method['fee'] = $payment_method['min_fee'];
                    $payment_method['fee'] = $payment_method['fee'] * $this->context->currency->conversion_rate;

                }
                if ($withTax && $payment_method['id_tax_rules_group']) {
                    $payment_method['fee'] = $this->getPriceIncl($payment_method['fee'], $payment_method['id_tax_rules_group']);
                }
            }

            return $payment_method['fee'];
        }
        return 0;
    }

    public function getFeePaymentModule($module_name, $products, $withTax)
    {
        $module = Module::getInstanceByName($module_name);
        if($module && Validate::isLoadedObject($module))
        {
            $totalorder = $this->context->cart->getOrderTotal(true, Cart::BOTH, $products, null, null,false, false, true);
            $payment_method = Ets_paymentmethod_class::getPaymentMethodByIdModule($module->id);
            if ($payment_method) {
                if ($payment_method['fee_type'] == 'free' || ($totalorder > $payment_method['free_for_order_over'] && $payment_method['free_for_order_over'] != 0) || (($minOrder = $payment_method['minimum_order']) && (float)$minOrder > (float)$totalorder) || (($maxOrder = $payment_method['maximum_order']) && (float)$maxOrder < (float)$totalorder))
                    $payment_method['fee'] = 0;
                else {
                    if ($payment_method['fee_type'] == 'fixed_fee') {
                        $payment_method['fee'] = $payment_method['fee_amount'];
                        $payment_method['fee'] = $payment_method['fee'] * $this->context->currency->conversion_rate;
                    } else {
                        if ($payment_method['fee_based_on'] == 1)
                            $total = $totalorder;
                        else
                            $total = $this->context->cart->getOrderTotal(false, Cart::BOTH, $products, null, null,false, false, true);
                        $payment_method['fee'] = $payment_method['percentage'] * $total / 100;
                        $payment_method['fee'] = (float)$payment_method['fee'] / $this->context->currency->conversion_rate;
                        if($payment_method['fee_type']=='percentage_fixed')
                        {
                            $payment_method['fee'] += $payment_method['fee_amount'];
                        }
                        if ($payment_method['fee'] > $payment_method['max_fee'] && (float)$payment_method['max_fee'])
                            $payment_method['fee'] = $payment_method['max_fee'];
                        else if ($payment_method['fee'] < $payment_method['min_fee'] && (float)$payment_method['min_fee'])
                            $payment_method['fee'] = $payment_method['min_fee'];
                        
                        $payment_method['fee'] = $payment_method['fee'] * $this->context->currency->conversion_rate;
                    }
                    if ($withTax && $payment_method['id_tax_rules_group']) {
                        $payment_method['fee'] = $this->getPriceIncl($payment_method['fee'], $payment_method['id_tax_rules_group']);
                    }
                }
                return $payment_method['fee'];
            }
        }
        
    }
    public function hookDisplayOverrideTemplate($params)
    {
        $id_order = (int)Tools::getValue('id_order');
        if (isset($params['template_file']) && $params['template_file'] == 'checkout/order-confirmation' && ($order_method = Ets_paymentmethod_class::getPaymentMethodByIdOrder($id_order)) && $order_method['id_paymentmethod']) {
            if ($order_method['fee'])
                $fee = Tools::displayPrice($order_method['fee']);
            else
                $fee = $this->l('Free');
            $paymentMothod = new Ets_paymentmethod_class($order_method['id_paymentmethod'], $this->context->language->id);
            $message = $paymentMothod->confirmation_message;
            $message = str_replace(array('[payment_method]', '[fee]', '[email]'), array($order_method['method_name'], $fee, $this->context->customer->email), $message);
            $this->context->smarty->assign('message', $message);
            return $this->getTemplatePath('checkout/order-confirmation.tpl');
        }
    }

    public function updatePositionPayment()
    {
        $page = (int)Tools::getValue('page',1);
        $payments = Tools::getValue('payment');
        if ($payments && Ets_payment_with_fee::validateArray($payments,'isInt')) {
            Ets_paymentmethod_class::updatePosition($payments);
        }
        die(json_encode(
            array(
                'page' => $page,
                'success' => $this->l('Updated successfully')
            )
        ));
    }

    public function renderViewOrder(&$tpl_view_vars)
    {
        $id_order = (int)Tools::getValue('id_order');
        if ($method_order = Ets_paymentmethod_class::getPaymentMethodByIdOrder($id_order)) {
            $fee_payment = $method_order['fee_incl'];
        }
        $tpl_view_vars['fee_payment'] = isset($fee_payment) && $fee_payment ? $fee_payment : false;
        $tpl_view_vars['custom_payment'] = isset($fee_payment) && $fee_payment ? $fee_payment : false;
    }

    public function initContentOrderConfirmation($id_cart, $order_presenter)
    {
        $order = new Order(Order::getIdByCartId((int)$id_cart));
        $tax = $this->checkDisplayTax($order->id_customer);
        $presentedOrder = $order_presenter ?  $order_presenter->present($order): (new PrestaShop\PrestaShop\Adapter\Presenter\Order\OrderPresenter())->present($order);
        if ($method_order = Ets_paymentmethod_class::getPaymentMethodByIdOrder($order->id)) {
            $price = $tax ? (float)$method_order['fee_incl'] : (float)$method_order['fee'];
            if ($price <= 0)
                return '';
            $priceFormatter = new PrestaShop\PrestaShop\Adapter\Product\PriceFormatter();
            
            $presentedOrder['subtotals']['fee_payment'] = array(
                'type' => 'fee_payment',
                //'label' => $tax ? sprintf($this->l('%s (tax incl.)'),$this->payment_fee_text) : sprintf($this->l('%s (tax excl.)'),$this->payment_fee_text),
                'label' => $tax ? sprintf($this->l('%s'),$this->payment_fee_text) : sprintf($this->l('%s'),$this->payment_fee_text),
                'amount' => $price,
                'value' => $price ? $priceFormatter->format($price, Currency::getCurrencyInstance((int)$order->id_currency)) : $this->l('Free'),
            );
        }
        $this->context->smarty->assign(
            array(
                'order' => $presentedOrder,
            )
        );
    }

    public function initContentOrderDetail(&$order_to_display)
    {
        $id_order = (int)Tools::getValue('id_order');
        $order = new Order($id_order);
        $tax = $this->checkDisplayTax($order->id_customer);
        if ($method_order = Ets_paymentmethod_class::getPaymentMethodByIdOrder($order->id)) {
            $price = $tax ? (float)$method_order['fee_incl'] : (float)$method_order['fee'];
            if ($price > 0) {
                $priceFormatter = new PrestaShop\PrestaShop\Adapter\Product\PriceFormatter();
                $order_to_display['subtotals']['fee_payment'] = array(
                    'type' => 'fee_payment',
                    //'label' => $tax ? sprintf($this->l('%s (tax incl.)'),$this->payment_fee_text) : sprintf($this->l('%s (tax excl.)'),$this->payment_fee_text),
                    'label' => $tax ? sprintf($this->l('%s'),$this->payment_fee_text) : sprintf($this->l('%s'),$this->payment_fee_text),
                    'amount' => $price,
                    'value' => $price ? $priceFormatter->format($price, Currency::getCurrencyInstance((int)$order->id_currency)) : $this->l('Free'),
                );
            }

        }
    }

    public function initContentHTMLTemplateInvoice($id_order, $smarty)
    {
        $order = new Order($id_order);
        $tax = $this->checkDisplayTax($order->id_customer);
        if ($method_order = Ets_paymentmethod_class::getPaymentMethodByIdOrder($order->id)) {
            $fee_payment = $tax ? $method_order['fee_incl'] : $method_order['fee'];
            if ($fee_payment) {
                $this->payment_fee_text = Configuration::get('ETS_PMF_TEXT_PAYMENT_FEE',$this->context->language->id) ? : $this->l('Payment fee');
                $smarty->assign(
                    array(
                        'fee_payment' => isset($fee_payment) ? $fee_payment : false,
                        'custom_payment' => isset($fee_payment) ? true : false,
                        'display_tax' => $tax,
                        //'text_payment_fee_incl' => sprintf($this->l('%s (tax incl.)'),$this->payment_fee_text),
                       //'text_payment_fee_excl' => sprintf($this->l('%s (tax excl.)'),$this->payment_fee_text),
                       'text_payment_fee_incl' => sprintf($this->l('%s'),$this->payment_fee_text),
                      'text_payment_fee_excl' => sprintf($this->l('%s'),$this->payment_fee_text),
                        'Total_Products_text' => $this->l('Total Products'),
                        'Total_Discounts_text' => $this->l('Total Discounts'),
                        'Shipping_Costs_text' => $this->l('Shipping Costs'),
                        'Free_Shipping_text' => $this->l('Free Shipping'),
                        'Free_text' => $this->l('Free'),
                        'Wrapping_Costs_text' => $this->l('Wrapping Costs'),
                        'Total_tax_excl_text' => $this->l('Total (Tax excl.)'),
                        'Total_tax_text' => $this->l('Total Tax'),
                        'Total_text' => $this->l('Total'),
                    )
                );
                $smarty->assign(
                    array(
                        'total_tab' => $smarty->fetch(_PS_MODULE_DIR_ . 'ets_payment_with_fee/views/templates/hook/invoice.total-tab.tpl'),
                    )
                );
            }
        }
    }
    public function hookActionOrderStatusPostUpdate($params)
    {
        if (isset($params['id_order']) && $params['id_order']) {
            $order = new Order($params['id_order']);
            $id_order_payment = Ets_paymentmethod_class::getInvoicePaymentByIdOrder($order->id);
            if ($id_order_payment && $order->module == 'ets_payment_with_fee' && $order->total_paid != 0) {
                $order_payment = new OrderPayment($id_order_payment);
                $order_payment->payment_method = $order->payment;
                $order_payment->update();
            }
        }
    }

    public function copy_directory($src, $dst,$sup_folder = true)
    {
        $dir = opendir($src);
        @mkdir($dst);
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src . '/' . $file)) {
                {
                    if($sup_folder)
                        $this->copy_directory($src . '/' . $file, $dst . '/' . $file);
                }
                } else {
                    if (file_exists($dst . '/' . $file) && $file != 'index.php' && ($content = Tools::file_get_contents($dst . '/' . $file)) && Tools::strpos($content, 'overried_custom_payment by chung_ets') === false && Tools::strpos($content, 'overried by chung_ets') === false)
                        copy($dst . '/' . $file, $dst . '/backup_' . $file);
                    if (!file_exists($dst . '/' . $file) || (file_exists($dst . '/' . $file) && ($content = Tools::file_get_contents($dst . '/' . $file)) && Tools::strpos($content, 'overried by chung_ets') === false))
                        copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }

    public function delete_directory($directory)
    {
        $dir = opendir($directory);
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($directory . '/' . $file)) {
                    $this->delete_directory($directory . '/' . $file);
                } else {
                    if (file_exists($directory . '/' . $file) && $file != 'index.php' && ($content = Tools::file_get_contents($directory . '/' . $file)) && Tools::strpos($content, 'overried_custom_payment by chung_ets') !== false) {
                        @unlink($directory . '/' . $file);
                        if (file_exists($directory . '/backup_' . $file))
                            copy($directory . '/backup_' . $file, $directory . '/' . $file);
                    }

                }
            }
        }
        closedir($dir);
    }

    public function hookActionEmailSendBefore($params)
    {
        if (isset($this->context->cart->id)) {
            $id_order = Order::getIdByCartId($this->context->cart->id);
            if ($id_order && ($order = new Order($id_order)) && Validate::isLoadedObject($order) && ($params['template'] == 'order_conf' || $params['template'] == 'new_order') ) {
                $paymentMethod = Ets_paymentmethod_class::getPaymentMethodByIdOrder($order->id);
                if ($paymentMethod && $paymentMethod['fee'] > 0) {
                    $tax = $this->checkDisplayTax($order->id_customer);
                    $template = array(
                        '{fee_payment}' => $paymentMethod['fee'] ? Tools::displayPrice($tax ? $paymentMethod['fee_incl'] : $paymentMethod['fee'], new Currency($order->id_currency), false) : $this->l('Free'),
                        //'{fee_payment_text}' => $tax ? sprintf($this->l('%s (tax incl.)'),$this->payment_fee_text) : sprintf($this->l('%s (tax excl.)'),$this->payment_fee_text),
                        '{fee_payment_text}' => $tax ? sprintf($this->l('%s'),$this->payment_fee_text) : sprintf($this->l('%s'),$this->payment_fee_text),
                    );
                    $params['templateVars'] = array_merge($params['templateVars'], $template);
                    $params['templatePath'] = dirname(__FILE__) . '/mails/';
                }
            }
        }
    }

    public function validateOrder($id_cart,
                                  $id_order_state,
                                  $amount_paid,
                                  $payment_method = 'Unknown',
                                  $message = null,
                                  $extra_vars = array(),
                                  $currency_special = null,
                                  $dont_touch_amount = false,
                                  $secure_key = false,
                                  Shop $shop = null ,
                                  string $order_reference = null)
    {
        if ($id_ets_paymentmethod = (int)Tools::getValue('id_ets_paymentmethod_admin')) {
            $payment_method = new Ets_paymentmethod_class($id_ets_paymentmethod, $this->context->language->id);
            return parent::validateOrder($id_cart, $id_order_state, $amount_paid, $payment_method->method_name, $message, $extra_vars, $currency_special, $dont_touch_amount, $secure_key, $shop,$order_reference);
        } else
            return parent::validateOrder($id_cart, $id_order_state, $amount_paid, $payment_method, $message, $extra_vars, $currency_special, $dont_touch_amount, $secure_key, $shop,$order_reference);
    }

    public function hookActionValidateOrder($params)
    {
        if (Tools::isSubmit('payment_fee_order') && Tools::isSubmit('id_ets_paymentmethod_admin')) {
            if ($payment_fee = (float)Tools::getValue('payment_fee_order')) {
                $id_ets_paymentmethod = (int)Tools::getValue('id_ets_paymentmethod_admin');
                $order = $params['order'];
                $tax = ($order->total_paid_tax_incl - $order->total_paid_tax_excl) / $order->total_paid_tax_excl;
                $payment_fee_incl = Tools::ps_round($payment_fee + $payment_fee * $tax, 2);
                Ets_paymentmethod_class::addPaymentOrder($id_ets_paymentmethod,$order->id,$payment_fee,$payment_fee_incl,$order->payment);
                $order->total_paid_tax_incl = $order->total_paid_tax_incl + $payment_fee_incl;
                $order->total_paid_tax_excl = $order->total_paid_tax_excl + $payment_fee;
                $order->total_paid = $order->total_paid_tax_incl;
                $order->update();
            }
        } else {
            $paymentCart = new Ets_payment_cart_class($params['cart']->id);
            if ($paymentCart && $paymentCart->ets_payment_module_name) {
                $order = $params['order'];
                $cart = $params['cart'];
                if (($id_payment_method = (int)Tools::getValue('id_payment_method')) && $order->module == $this->name) {
                    $total_fee_payment_incl = $this->getFeePayment($id_payment_method, null, true);
                    $total_fee_payment_excl = $this->getFeePayment($id_payment_method, null, false);
                    $total_order_fee_payment_incl = $this->getFeePayment($id_payment_method, $order->product_list, true);
                    $total_order_fee_payment_excl = $this->getFeePayment($id_payment_method, $order->product_list, false);
                } else {
                    $total_order_fee_payment_incl = $cart->getOrderTotal(true, Cart::BOTH, $order->product_list, null,false, false, true);
                    $total_order_fee_payment_excl = $cart->getOrderTotal(false, Cart::BOTH, $order->product_list, null,false, false, true);
                    $total_fee_payment_incl = $cart->getOrderTotal(true, Cart::BOTH, null, null, false,false, true);
                    $total_fee_payment_excl = $cart->getOrderTotal(false, Cart::BOTH, null, null, false,false, true);
                }
                if (!Ets_paymentmethod_class::getPaymentMethodByIdOrder($order->id)) {
                    Ets_paymentmethod_class::addPaymentOrder($id_payment_method,$order->id,$total_fee_payment_excl,$total_fee_payment_incl,$order->payment);
                    $total_paid_tax_incl = $order->total_paid_tax_incl - $total_order_fee_payment_incl + $total_fee_payment_incl;
                    $total_paid_tax_excl = $order->total_paid_tax_excl - $total_order_fee_payment_excl + $total_fee_payment_excl;
                } else {
                    $total_paid_tax_incl = $order->total_paid_tax_incl - $total_order_fee_payment_incl;
                    $total_paid_tax_excl = $order->total_paid_tax_excl - $total_order_fee_payment_excl;
                }
                if ($order->total_paid_tax_incl != $total_paid_tax_incl || $order->total_paid_tax_excl != $total_paid_tax_excl) {
                    $order->total_paid_tax_incl = $total_paid_tax_incl;
                    $order->total_paid = $total_fee_payment_incl;
                    $order->total_paid_tax_excl = $total_paid_tax_excl;
                    Ets_paymentmethod_class::updateFeePaymentOrder($order->id,$total_paid_tax_excl,$total_paid_tax_incl);
                }
                if ($paymentCart->ets_payment_module_name != $order->module) {
                    $order_state = new OrderState(Configuration::get('PS_OS_ERROR'));
                    if (Validate::isLoadedObject($order_state)) {
                        $current_order_state = $order->getCurrentOrderState();
                        if (!$current_order_state ||  $current_order_state->id != $order_state->id) {
                            //Create new OrderHistory
                            $history = new OrderHistory();
                            $history->id_order = $order->id;
                            $history->id_employee = 0;

                            $use_existings_payment = false;
                            if (!$order->hasInvoice()) {
                                $use_existings_payment = true;
                            }
                            $history->changeIdOrderState((int)$order_state->id, $order, $use_existings_payment);
                            $carrier = new Carrier($order->id_carrier, $order->id_lang);
                            $templateVars = array();
                            if ($history->id_order_state == Configuration::get('PS_OS_SHIPPING') && $order->shipping_number) {
                                $templateVars = array('{followup}' => str_replace('@', $order->shipping_number, $carrier->url));
                            }
                            if ($history->addWithemail(true, $templateVars)) {
                                if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT')) {
                                    foreach ($order->getProducts() as $product) {
                                        if (StockAvailable::dependsOnStock($product['product_id'])) {
                                            StockAvailable::synchronize($product['product_id'], (int)$product['id_shop']);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

    }

    public function getFeePayOrderTotal($products = null, $withTaxes = true)
    {
        if(isset($this->context->cart->id))
        {
            $paymentCart = new Ets_payment_cart_class($this->context->cart->id);
            $module_name = isset($paymentCart->ets_payment_module_name) ? $paymentCart->ets_payment_module_name : false;
            $id_payment_method = isset($paymentCart->id_payment_method) ? $paymentCart->id_payment_method : 0;
            if ($module_name == $this->name && $id_payment_method) {
                return $this->getFeePayment($id_payment_method, $products, $withTaxes);
            }
            if ($module_name) {
                return $this->getFeePaymentModule($module_name, $products, $withTaxes);
            }
        }
        
        return 0;
    }

    public function assignGeneralPurposeVariables($presented_cart)
    {
        $id_customer = ($this->context->customer->id) ? (int)($this->context->customer->id) : 0;
        $tax = $this->checkDisplayTax($id_customer);
        $price = (float)$this->context->cart->getOrderTotal($tax, Cart::BOTH, null, null, false,false, true);
        if ($price > 0) {
            $priceFormatter = new PrestaShop\PrestaShop\Adapter\Product\PriceFormatter();
            $presented_cart['subtotals']['fee_payment'] = array(
                'type' => 'fee_payment',
                //'label' => $tax ? sprintf($this->l('%s (tax incl.)'),$this->payment_fee_text) : sprintf($this->l('%s (tax excl.)'),$this->payment_fee_text),
                'label' => $tax ? sprintf($this->l('%s'),$this->payment_fee_text) : sprintf($this->l('%s'),$this->payment_fee_text),
                'amount' => $price,
                'value' => $price ? $priceFormatter->format($price, $this->context->currency) : $this->l('Free'),
            );
            $this->context->smarty->assign([
                'cart' => $presented_cart,
            ]);
        }
    }
    public function getModuleWithFilter($filter = false)
    {
        $sort = Tools::getValue('sort', 'm.id_module');
        if(!Validate::isCleanHtml($sort))
            $sort = 'm.id_module';
        $sort_type = Tools::getValue('sort_type','asc');
        if(!in_array($sort_type,array('asc','desc')))
            $sort_type = 'asc';
        $hook_payment = $this->is17 ? 'paymentOptions' : 'payment';
        $filter .=' AND m.id_module !="' . (int)$this->id . '" AND h.name = "' . pSQL($hook_payment) . '"';
        return Ets_paymentmethod_class::getModulesByFilter($filter,$sort,$sort_type);
    }
    public function genSecure($size)
    {
        $chars = md5(time());
        $code = '';
        for ($i = 1; $i <= $size; ++$i) {
            $char = Tools::substr($chars, rand(0, Tools::strlen($chars) - 1), 1);
            if ($char == 'e')
                $char = 'a';
            $code .= $char;
        }
        return $code;
    }

    public function displayOrderStatus($id_state)
    {
        $this->context->smarty->assign(
            array(
                'order_state' => new OrderState($id_state, $this->context->language->id),
            )
        );
        return $this->display(__FILE__, 'order_state.tpl');
    }
    public function renderPaymentOptions(&$paymentOptions)
    {
        if ($paymentOptions) {
            foreach ($paymentOptions as $key => &$paymentOption) {
                if ($paymentOption) {
                    foreach ($paymentOption as &$payment) {
                        if (!$payment['module_name'])
                            $payment['module_name'] = $key;
                    }
                }
            }
        }
        return $paymentOptions;
    }
    public function getProductsPaypal(&$products)
    {
        $fc = Tools::getValue('fc');
        $module =  Tools::getValue('module');
        $controller = Tools::getValue('controller');
        if ($fc == 'module' && ($module == 'paypal' || $module=='ps_checkout') && $products && !$this->add_product_to_paypal && Validate::isControllerName($controller) && !in_array($controller,array('ecValidation','mbValidation','pppValidation'))) {
            $this->add_product_to_paypal = true;
            $fee_payment = (float)$this->context->cart->getOrderTotal(false, Cart::BOTH, null, null, false,false, true);
            $fee_payment_wt = (float)$this->context->cart->getOrderTotal(true, Cart::BOTH, null, null, false,false, true);
            if ($fee_payment > 0) {
                $product = $products[0];
                $product['price'] = $fee_payment;
                $product['price_wt'] = $fee_payment_wt;
                $product['quantity'] = 1;
                $product['name'] = Configuration::get('ETS_PMF_TEXT_PAYMENT_FEE',$this->context->language->id);
                $product['specific_prices'] = array();
                $product['total'] = $fee_payment;
                $product['total_wt'] = $fee_payment_wt;
                $product['cart_quantity'] = 1;
                $product['attributes'] = '';
                $product['attributes_small'] = '';
                $product['features'] = array();
                $product['ets_product'] = true;
                $products[] = $product;
            }
        }
        if ($fc == 'module' && $module == 'klarnaofficial' && $products && !$this->add_product_to_klarna && Validate::isControllerName($controller)) {
            $this->add_product_to_klarna = true;
            $fee_payment = (float)$this->context->cart->getOrderTotal(false, Cart::BOTH, null, null, false,false, true);
            $fee_payment_wt = (float)$this->context->cart->getOrderTotal(true, Cart::BOTH, null, null, false,false, true);
            if ($fee_payment > 0) {
                $product = $products[0];
                $product['price'] = $fee_payment;
                $product['price_wt'] = $fee_payment_wt;
                $product['quantity'] = 1;
                $product['name'] = Configuration::get('ETS_PMF_TEXT_PAYMENT_FEE',$this->context->language->id);
                $product['specific_prices'] = array();
                $product['total'] = $fee_payment;
                $product['total_wt'] = $fee_payment_wt;
                $product['cart_quantity'] = 1;
                $product['attributes'] = '';
                $product['attributes_small'] = '';
                $product['features'] = array();
                $product['ets_product'] = true;
                $products[] = $product;
            }
        }
        return $products;
    }
    public function getTextLang($text, $lang,$file_name='')
    {
        if(is_array($lang))
            $iso_code = $lang['iso_code'];
        elseif(is_object($lang))
            $iso_code = $lang->iso_code;
        else
        {
            $language = new Language($lang);
            $iso_code = $language->iso_code;
        }
		$modulePath = rtrim(_PS_MODULE_DIR_, '/').'/'.$this->name;
        $fileTransDir = $modulePath.'/translations/'.$iso_code.'.'.'php';
        if(!@file_exists($fileTransDir)){
            return $text;
        }
        $fileContent = Tools::file_get_contents($fileTransDir);
        $text_tras = preg_replace("/\\\*'/", "\'", $text);
        $strMd5 = md5($text_tras);
        $keyMd5 = '<{' . $this->name . '}prestashop>' . ($file_name ? : $this->name) . '_' . $strMd5;
        preg_match('/(\$_MODULE\[\'' . preg_quote($keyMd5) . '\'\]\s*=\s*\')(.*)(\';)/', $fileContent, $matches);
        if($matches && isset($matches[2])){
           return  $matches[2];
        }
        return $text;
    }
    public static function validateArray($array,$validate='isCleanHtml')
    {
        if(!is_array($array))
            return false;
        if(method_exists('Validate',$validate))
        {
            if($array && is_array($array))
            {
                $ok= true;
                foreach($array as $val)
                {
                    if(!is_array($val))
                    {
                        if($val && !Validate::$validate($val))
                        {
                            $ok= false;
                            break;
                        }
                    }
                    else
                        $ok = self::validateArray($val,$validate);
                }
                return $ok;
            }
        }
        return true;
    }
    public function renderFormConfig()
    {
        if(Tools::isSubmit('btnSubmit'))
        {
            $errors =array();
            $id_lang_default = (int)Configuration::get('PS_LANG_DEFAULT');
            $languages = Language::getLanguages(false);
            $ETS_PMF_TEXT_PAYMENT_FEE_default = Tools::getValue('ETS_PMF_TEXT_PAYMENT_FEE_'.$id_lang_default);
            if(!$ETS_PMF_TEXT_PAYMENT_FEE_default)
                $errors[] = $this->l('"Payment fee" text is required');
            $ETS_PMF_TEXT_PAYMENT_FEEs = array();
            foreach($languages as $language)
            {
                $ETS_PMF_TEXT_PAYMENT_FEEs[$language['id_lang']] = Tools::getValue('ETS_PMF_TEXT_PAYMENT_FEE_'.$language['id_lang']);
                if($ETS_PMF_TEXT_PAYMENT_FEEs[$language['id_lang']] && !Validate::isCleanHtml($ETS_PMF_TEXT_PAYMENT_FEEs[$language['id_lang']]))
                    $errors[] = sprintf($this->l('"Payment fee" text is not valid in language %s'),$language['iso_code']);
                if(!$ETS_PMF_TEXT_PAYMENT_FEEs[$language['id_lang']])
                    $ETS_PMF_TEXT_PAYMENT_FEEs[$language['id_lang']] = $ETS_PMF_TEXT_PAYMENT_FEE_default;
            }
            if($errors)
                $this->_html .= $this->displayError($errors);
            else
            {
                Configuration::updateValue('ETS_PMF_TEXT_PAYMENT_FEE',$ETS_PMF_TEXT_PAYMENT_FEEs);
                $this->_html .= $this->displayConfirmation($this->l('Updated successfully'));
            }
        }
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Global configuration'),
                    'icon' => ''
                ),
                'input' => array(
                    array(
                        'type' => 'text',
                        'label' => $this->l('"Payment fee" text'),
                        'name' => 'ETS_PMF_TEXT_PAYMENT_FEE',
                        'required' => true,
                        'lang' => true,
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                )
            ),
        );
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? : 0;
        $this->fields_form = array();
        $helper->id = (int)$this->id;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'btnSubmit';
        $controller = Tools::getValue('controller');
        $helper->currentIndex = $this->context->link->getAdminLink($controller, false).'&configure='
            .$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&list=true';
        $helper->token = Tools::getAdminTokenLite($controller);
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );

        return $this->_html .=$helper->generateForm(array($fields_form));
    }
    public function getConfigFieldsValues()
    {
        $fields = array();
        $languages = Language::getLanguages(false);
        foreach($languages as $language)
        {
            $fields['ETS_PMF_TEXT_PAYMENT_FEE'][$language['id_lang']] = Tools::getValue('ETS_PMF_TEXT_PAYMENT_FEE_'.$language['id_lang'],Configuration::get('ETS_PMF_TEXT_PAYMENT_FEE',$language['id_lang']));
        }
        return $fields;
    }
    public function hookDisplayPaymentFeeOrder($params)
    {
        if(isset($params['orderId']) &&  ($id_order = (int)$params['orderId']) && ($order = new Order($id_order)) && Validate::isLoadedObject($order))
        {
            if ($method_order = Ets_paymentmethod_class::getPaymentMethodByIdOrder($order->id)) {
                $fee_payment = $method_order['fee_incl'];
                if($fee_payment)
                {
                    $this->context->smarty->assign(
                        array(
                            'fee_payment' => Tools::displayPrice($fee_payment,new Currency($order->id_currency)),
                            'ETS_PMF_TEXT_PAYMENT_FEE' => Configuration::get('ETS_PMF_TEXT_PAYMENT_FEE',Context::getContext()->language->id) ? : $this->l('Payment fee')
                        )
                    );
                    return $this->display(__FILE__,'fee_order.tpl');
                }
            }
        }
        return '';
    }
    public function updatePositionHookValidate()
    {
        if($id_hook = (int)Hook::getIdByName('actionValidateOrder'))
        {
            return Ets_paymentmethod_class::updatePostionHook($this->id,$id_hook);
        }
        return true;
    }

    /* Use new translate system*/
    public function isUsingNewTranslationSystem()
    {
        return false;
    }
    /**
     * @param string $path
     * @param int $permission
     *
     * @return bool
     *
     * @throws \PrestaShopException
     */
    private function safeMkDir($path, $permission = 0755)
    {
        if (!@mkdir($concurrentDirectory = $path, $permission) && !is_dir($concurrentDirectory)) {
            throw new \PrestaShopException(sprintf('Directory "%s" was not created', $concurrentDirectory));
        }

        return true;
    }
    private function checkOverrideDir()
    {
        if (defined('_PS_OVERRIDE_DIR_')) {
            $psOverride = @realpath(_PS_OVERRIDE_DIR_) . DIRECTORY_SEPARATOR;
            if (!is_dir($psOverride)) {
                $this->safeMkDir($psOverride);
            }
            $base = str_replace('/', DIRECTORY_SEPARATOR, $this->getLocalPath() . 'override');
            $iterator = new RecursiveDirectoryIterator($base, FilesystemIterator::SKIP_DOTS);
            /** @var RecursiveIteratorIterator|\SplFileInfo[] $iterator */
            $iterator = new RecursiveIteratorIterator($iterator, RecursiveIteratorIterator::SELF_FIRST);
            $iterator->setMaxDepth(4);
            foreach ($iterator as $k => $item) {
                if (!$item->isDir()) {
                    continue;
                }
                $path = str_replace($base . DIRECTORY_SEPARATOR, '', $item->getPathname());
                if (!@file_exists($psOverride . $path)) {
                    $this->safeMkDir($psOverride . $path);
                    @touch($psOverride . $path . DIRECTORY_SEPARATOR . '_do_not_remove');
                }
            }
            if (!file_exists($psOverride . 'index.php')) {
                Tools::copy($this->getLocalPath() . 'index.php', $psOverride . 'index.php');
            }
        }
    }
    public function uninstallOverrides(){
        return true;
        $this->replaceOverridesBeforeInstall();
        $this->replaceOverridesOtherModuleBeforeInstall();
        if(parent::uninstallOverrides())
        {

            require_once(dirname(__FILE__) . '/classes/OverrideUtil');
            $class= 'Ets_pwf_overrideUtil';
            $method = 'restoreReplacedMethod';
            call_user_func_array(array($class, $method),array($this));
            $this->replaceOverridesBeforeInstall();
            $this->replaceOverridesOtherModuleAfterInstall();
            return true;
        }
        $this->replaceOverridesBeforeInstall();
        $this->replaceOverridesOtherModuleAfterInstall();
        return false;
    }
    public function installOverrides()
    {
        return true;
        $this->replaceOverridesBeforeInstall();
        $this->replaceOverridesOtherModuleBeforeInstall();
        require_once(dirname(__FILE__) . '/classes/OverrideUtil');
        $class= 'Ets_pwf_overrideUtil';
        $method = 'resolveConflict';
        call_user_func_array(array($class, $method),array($this));
        if(parent::installOverrides())
        {
            call_user_func_array(array($class, 'onModuleEnabled'),array($this));
            $this->replaceOverridesAfterInstall();
            $this->replaceOverridesOtherModuleAfterInstall();
            return true;
        }
        $this->replaceOverridesAfterInstall();
        $this->replaceOverridesOtherModuleAfterInstall();
        return false;
    }
    public function replaceOverridesBeforeInstall()
    {
        if(version_compare(_PS_VERSION_,'1.7.7.0','<'))
        {
            $file_cart_content = Tools::file_get_contents(dirname(__FILE__).'/override/classes/Cart.php');
            $search = array(
                'public function getOrderTotal($withTaxes = true, $type = Cart::BOTH, $products = null, $id_carrier = null, $use_cache = false, bool $keepOrderPrices = false, $fee_payment = false, $only_cart = false)',
                'public function getProducts($refresh = false, $id_product = false, $id_country = null, $fullInfos = true, bool $keepOrderPrices = false,$default=false)'
            );
            $replace = array(
                'public function getOrderTotal($withTaxes = true, $type = Cart::BOTH, $products = null, $id_carrier = null, $use_cache = false, $keepOrderPrices = false, $fee_payment = false, $only_cart = false)',
                'public function getProducts($refresh = false, $id_product = false, $id_country = null, $fullInfos = true, $keepOrderPrices = false,$default=false)'
            );
            $file_cart_content = str_replace($search,$replace,$file_cart_content);
            file_put_contents(dirname(__FILE__).'/override/classes/Cart.php',$file_cart_content);
        }
    }
    public function replaceOverridesAfterInstall()
    {
        if(version_compare(_PS_VERSION_,'1.7.7.0','<'))
        {
            $file_cart_content = Tools::file_get_contents(dirname(__FILE__).'/override/classes/Cart.php');
            $search = array(
                'public function getOrderTotal($withTaxes = true, $type = Cart::BOTH, $products = null, $id_carrier = null, $use_cache = false, $keepOrderPrices = false, $fee_payment = false, $only_cart = false)',
                'public function getProducts($refresh = false, $id_product = false, $id_country = null, $fullInfos = true, $keepOrderPrices = false,$default=false)'
            );
            $replace= array(
                'public function getOrderTotal($withTaxes = true, $type = Cart::BOTH, $products = null, $id_carrier = null, $use_cache = false, bool $keepOrderPrices = false, $fee_payment = false, $only_cart = false)',
                'public function getProducts($refresh = false, $id_product = false, $id_country = null, $fullInfos = true, bool $keepOrderPrices = false,$default=false)'
            );
            $file_cart_content = str_replace($search,$replace,$file_cart_content);
            file_put_contents(dirname(__FILE__).'/override/classes/Cart.php',$file_cart_content);
        }
    }
    public function replaceOverridesOtherModuleBeforeInstall()
    {
        if(Module::isInstalled('ets_promotion') && ($ets_promotion = Module::getInstanceByName('ets_promotion')) && method_exists($ets_promotion,'replaceOverridesBeforeInstall'))
        {
            $ets_promotion->replaceOverridesBeforeInstall();
        }
        if(Module::isInstalled('ets_extraoptions') && ($ets_extraoptions = Module::getInstanceByName('ets_extraoptions')) && method_exists($ets_extraoptions,'replaceOverridesBeforeInstall'))
        {
            $ets_extraoptions->replaceOverridesBeforeInstall();
        }
    }
    public function replaceOverridesOtherModuleAfterInstall()
    {
        if(Module::isInstalled('ets_promotion') && ($ets_promotion = Module::getInstanceByName('ets_promotion')) && method_exists($ets_promotion,'replaceOverridesAfterInstall'))
        {
            $ets_promotion->replaceOverridesAfterInstall();
        }
        if(Module::isInstalled('ets_extraoptions') && ($ets_extraoptions = Module::getInstanceByName('ets_extraoptions')) && method_exists($ets_extraoptions,'replaceOverridesBeforeInstall'))
        {
            $ets_extraoptions->replaceOverridesBeforeInstall();
        }
    }
    public function enable($force_all = false)
    {
        if(!$force_all && Ets_paymentmethod_class::checkEnableOtherShop($this->id) && $this->getOverrides() != null)
        {
            try {
                $this->uninstallOverrides();
            }
            catch (Exception $e)
            {
                if($e)
                {
                    //
                }
            }
        }
        $this->checkOverrideDir();
        return parent::enable($force_all);
    }
    public function disable($force_all = false)
    {
        if(parent::disable($force_all))
        {
            if(!$force_all && Ets_paymentmethod_class::checkEnableOtherShop($this->id))
            {
                if(property_exists('Tab','enabled') && method_exists($this, 'get') && $dispatcher = $this->get('event_dispatcher')){
                    /** @var \Symfony\Component\EventDispatcher\Debug\TraceableEventDispatcher|\Symfony\Component\EventDispatcher\EventDispatcherInterface $dispatcher */
                    $dispatcher->addListener(\PrestaShopBundle\Event\ModuleManagementEvent::DISABLE, function (\PrestaShopBundle\Event\ModuleManagementEvent $event) {
                        Ets_paymentmethod_class::activeTab($this->name);
                    });
                }
                if($this->getOverrides() != null)
                {
                    try {
                        $this->installOverrides();
                    }
                    catch (Exception $e)
                    {
                        if($e)
                        {
                            //
                        }
                    }
                }
            }
            return true;
        }
        return false;
    }
}