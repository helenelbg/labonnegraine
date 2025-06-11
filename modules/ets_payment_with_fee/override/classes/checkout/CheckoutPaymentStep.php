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
 
use Symfony\Component\Translation\TranslatorInterface;

class CheckoutPaymentStep extends CheckoutPaymentStepCore
{
    private $selected_payment_option;
    public function render(array $extraParams = array())
    {
        $isFree = 0 == (float) $this->getCheckoutSession()->getCart()->getOrderTotal(true, Cart::BOTH);
        $paymentOptions = $this->paymentOptionsFinder->present($isFree);
        $conditionsToApprove = $this->conditionsToApproveFinder->getConditionsToApproveForTemplate();
        $deliveryOptions = $this->getCheckoutSession()->getDeliveryOptions();
        $deliveryOptionKey = $this->getCheckoutSession()->getSelectedDeliveryOption();

        if (isset($deliveryOptions[$deliveryOptionKey])) {
            $selectedDeliveryOption = $deliveryOptions[$deliveryOptionKey];
        } else {
            $selectedDeliveryOption = 0;
        }
        unset($selectedDeliveryOption['product_list']);
        $ets_payment_with_fee = Module::getInstanceByName('ets_payment_with_fee');
        $ets_payment_with_fee->renderPaymentOptions($paymentOptions);
        $assignedVars = array(
            'is_free' => $isFree,
            'payment_options' => $paymentOptions,
            'conditions_to_approve' => $conditionsToApprove,
            'selected_payment_option' => $this->selected_payment_option,
            'selected_delivery_option' => $selectedDeliveryOption,
            'show_final_summary' => Configuration::get('PS_FINAL_SUMMARY_ENABLED'),
        );
        return $this->renderTemplate($this->getTemplate(), $extraParams, $assignedVars);
    }
}