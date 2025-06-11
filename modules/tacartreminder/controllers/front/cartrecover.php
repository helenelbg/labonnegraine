<?php
/**
 *    @author    EIRL Timactive Romain De Véra
 *    @copyright Copyright (c) TIMACTIVE 2017 -EIRL Timactive De Véra
 *    @license   Commercial license
 *
 *    @version 1.0.48
 *
 *    @name tacartreminder
 *  _____ _            ___       _   _
 * |_   _(_)          / _ \     | | (_)
 *   | |  _ _ __ ___ / /_\ \ ___| |_ ___   _____
 *   | | | | '_ ` _ \|  _  |/ __| __| \ \ / / _ \
 *   | | | | | | | | | | | | (__| |_| |\ V /  __/
 *   \_/ |_|_| |_| |_\_| |_/\___|\__|_| \_/ \___|
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class TACartReminderCartRecoverModuleFrontController extends ModuleFrontController
{
    public $ssl = true;

    public function __construct()
    {
        parent::__construct();
        $this->context = Context::getContext();
    }

    /**
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        parent::initContent();
        $this->assign();
    }

    // http://ps.local/ps16/fr/module/tacartreminder/cart_recover?recover_cart=30&id_reminder=0&cadd=0&token_cart=8ac425a1f981c416bb75a3217e633eb1&step=3
    public function assign()
    {
        $id_reminder = (int) Tools::getValue('ta_re');
        $id_cart = (int) Tools::getValue('ta_c');
        $token = Tools::getValue('ta_token');
        $step = (int) Tools::getValue('ta_st');
        $cart_rule_add = (int) Tools::getValue('ta_cr');
        $url = $this->context->link->getPageLink(
            'index',
            null,
            $this->context->language->id
        );
        if ($token == md5(_COOKIE_KEY_ . 'recover_cart_' . $id_cart)
            && ($cart = new Cart($id_cart))
            && Validate::isLoadedObject($cart)) {
            $journal = TACartReminderJournal::getWithCart($id_cart);
            if ((int) $id_reminder && $journal && $journal->id) {
                TACartReminderJournal::markReminderIsClick(
                    (int) $journal->id,
                    $id_reminder
                );
            }
            $customer = new Customer($cart->id_customer);
            if (Validate::isLoadedObject($customer)) {
                // log if user is not logged
                $customer->logged = 1;
                $this->context->customer = $customer;
                $this->context->cookie->id_customer = (int) $customer->id;
                $this->context->cookie->customer_lastname = $customer->lastname;
                $this->context->cookie->customer_firstname = $customer->firstname;
                $this->context->cookie->logged = 1;
                $this->context->cookie->check_cgv = 1;
                $this->context->cookie->is_guest = $customer->isGuest();
                $this->context->cookie->passwd = $customer->passwd;
                $this->context->cookie->email = $customer->email;
            }
            if (method_exists($this->context, 'updateCustomer')) {
                $this->context->updateCustomer($customer);
            } else {
                $this->updateContext($customer);
            }
            Hook::exec('actionAuthentication', ['customer' => $this->context->customer]);
            $order_process = version_compare(_PS_VERSION_, '1.7.0.0', '<')
            && Configuration::get('PS_ORDER_PROCESS_TYPE') ?
                'order-opc' : 'order';
            $step = $cart->checkQuantities() ? $step : 1;
            if ($step > 1) {
                $url = $this->context->link->getPageLink(
                    $order_process,
                    true,
                    (int) $cart->id_lang,
                    'step=' . $step . '&recover_cart=' . (int) $cart->id . '&cadd=' . $cart_rule_add . '&token_cart=' . md5(
                        _COOKIE_KEY_ . 'recover_cart_' . (int) $cart->id
                    ),
                    null,
                    (int) $cart->id_shop
                );
            } else {
                if (version_compare(_PS_VERSION_, '1.7.0.0', '>')) {
                    $url = $this->context->link->getPageLink(
                        'cart',
                        null,
                        null,
                        [
                            'action' => 'show',
                            'recover_cart' => (int) $cart->id,
                            'token_cart' => md5(_COOKIE_KEY_ . 'recover_cart_' . (int) $cart->id),
                        ],
                        (int) $cart->id_shop
                    );
                } else {
                    $url = $this->context->link->getPageLink(
                        $order_process,
                        true,
                        (int) $cart->id_lang,
                        'recover_cart=' . (int) $cart->id . '&cadd=' . $cart_rule_add . '&token_cart=' . md5(
                            _COOKIE_KEY_ . 'recover_cart_' . (int) $cart->id
                        ),
                        null,
                        (int) $cart->id_shop
                    );
                }
            }
        }
        Tools::redirect($url);
    }

    protected function updateContext(Customer $customer)
    {
        $customer->logged = 1;
        $this->context->customer = $customer;
        $this->context->cookie->id_customer = (int) $customer->id;
        $this->context->cookie->customer_lastname = $customer->lastname;
        $this->context->cookie->customer_firstname = $customer->firstname;
        $this->context->cookie->passwd = $customer->passwd;
        $this->context->cookie->logged = 1;
        $this->context->cookie->email = $customer->email;
        $this->context->cookie->is_guest = $customer->is_guest;
        $this->context->cart->secure_key = $customer->secure_key;
        $this->context->cookie->write();
        $this->context->cart->update();
        $customer->update();
    }
}
