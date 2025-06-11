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

class Ets_affiliatemarketingValidationModuleFrontController extends ModuleFrontController
{
    public function postProcess()
    {
        if(!$this->context->customer->isLogged())
            Tools::redirect($this->context->link->getPageLink('my-account'));
        $cart = $this->context->cart;
        if ($cart->id_customer == 0 || $cart->id_address_delivery == 0 || $cart->id_address_invoice == 0 || !$this->module->active)
            Tools::redirect('index.php?controller=order&step=1');
        $authorized = false;
        foreach (Module::getPaymentModules() as $module)
            if ($module['name'] == 'ets_affiliatemarketing') {
                $authorized = true;
                break;
            }
        if (!$authorized)
            die($this->module->l('This payment method is not available.', 'validation'));
        if (! Configuration::get('ETS_AM_AFF_ALLOW_BALANCE_TO_PAY'))
            die($this->module->l('You are not allowed to use rewards to pay for order.','validation'));
        $customer = new Customer($cart->id_customer);
        if (!Validate::isLoadedObject($customer))
            Tools::redirect('index.php?controller=order&step=1');
        $currency = $this->context->currency;
        $total = (float)$cart->getOrderTotal(true, Cart::BOTH);
        if (Ets_AM::needExchange($this->context)) {
            $total = Tools::convertPrice($total, null, false);
        }
        if ($min = Configuration::get('ETS_AM_MIN_BALANCE_REQUIRED_FOR_ORDER')) {
            $min = (float) $min;
            $displayMin = Ets_AM::needExchange($this->context) ? Ets_affiliatemarketing::displayPrice(Tools::convertPrice($min, $this->context->currency->id, true)) : (float) Ets_affiliatemarketing::displayPrice($min);
            if ($min !== 0 && $total < $min)
                die($this->module->l('Minimum reward balance required to be usable to pay for order: ','validation').$displayMin);
        }

        if ($max = Configuration::get('ETS_AM_MAX_BALANCE_REQUIRED_FOR_ORDER')) {
            $max = (float) $max;
            $displayMax = Ets_AM::needExchange($this->context) ? Ets_affiliatemarketing::displayPrice(Tools::convertPrice($max, $this->context->currency->id, true)) : Ets_affiliatemarketing::displayPrice($max);
            if ($max !== 0 && $total > $max)
                die($this->module->l('Maximum reward balance required to be usable to pay for order: ', 'validation').$displayMax);
        }
        $total_balance = Ets_Reward_Usage::getTotalBalance($customer->id, $this->context);
        if (Tools::ps_round($total_balance,6) < Tools::ps_round($total,6))
            die($this->module->l('Your balance in reward is not enough for pay this order, please use other payment method!', 'validation'));
        $mailVars = array(
            '{reward_owner}' => $this->context->customer->firstname . ' ' . $this->context->customer->lastname . '(Id: ' . $this->context->customer->id . ')',
            '{reward_amount}' => nl2br(Ets_affiliatemarketing::displayPrice($total))
        );
        if ($this->module->validateOrder($cart->id, Configuration::get('PS_ETS_AM_REWARD_PAID'), $total, $this->module->l('Pay by reward','validation'), NULL, $mailVars, (int)$currency->id, false, $customer->secure_key)) {
            Tools::redirect('index.php?controller=order-confirmation&id_cart=' . $cart->id . '&id_module=' . $this->module->id . '&id_order=' . $this->module->currentOrder . '&key=' . $customer->secure_key);
        }

    }

}
