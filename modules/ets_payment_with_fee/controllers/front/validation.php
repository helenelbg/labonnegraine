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

class Ets_payment_with_feeValidationModuleFrontController extends ModuleFrontController
{
    public $ssl = true;
    public function __construct()
	{
		parent::__construct();
		$this->context = Context::getContext();
	}
	public function postProcess()
	{
		$cart = $this->context->cart;
		if ($cart->id_customer == 0 || $cart->id_address_delivery == 0 || $cart->id_address_invoice == 0 || !$this->module->active)
			Tools::redirect('index.php?controller=order&step=1');

		// Check that this payment option is still available in case the customer changed his address just before the end of the checkout process
		$authorized = false;
		foreach (Module::getPaymentModules() as $module)
			if ($module['name'] == 'ets_payment_with_fee')
			{
				$authorized = true;
				break;
			}
		if (!$authorized)
			die($this->module->l('This payment method is not available.','validation'));

		$customer = new Customer($cart->id_customer);
		if (!Validate::isLoadedObject($customer))
			Tools::redirect('index.php?controller=order&step=1');
        $id_payment_method= (int)Tools::getValue('id_payment_method');
        $id_group = Customer::getDefaultGroupId((int)$customer->id);
        $id_carrier= (int)$cart->id_carrier;
        $id_address_delivery= (int)$cart->id_address_delivery;
        $total = (float)$cart->getOrderTotal(true, Cart::BOTH);
        if(!$this->module->checkPaymentMethod($id_payment_method,$id_group,$id_carrier,$id_address_delivery,$total))
            die($this->module->l('This payment method is not available.','validation'));
        else
            $paymentmethod= new Ets_paymentmethod_class($id_payment_method,$this->context->language->id);
		$currency = $this->context->currency;
		$this->module->validateOrder($cart->id, $paymentmethod->order_status, $total,$paymentmethod->method_name, NULL, array(), (int)$currency->id, false, $customer->secure_key);
		Tools::redirect('index.php?controller=order-confirmation&id_cart='.$cart->id.'&id_module='.$this->module->id.'&id_order='.$this->module->currentOrder.'&key='.$customer->secure_key);
	}
}
?>