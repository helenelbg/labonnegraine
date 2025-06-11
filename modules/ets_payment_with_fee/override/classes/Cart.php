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

class Cart extends CartCore
{
    public function getOrderTotal($withTaxes = true, $type = Cart::BOTH, $products = null, $id_carrier = null, $use_cache = false, bool $keepOrderPrices = false, $fee_payment = false, $only_cart = false)
    {
        $total = parent::getOrderTotal($withTaxes,$type,$products,$id_carrier,$use_cache,$keepOrderPrices);
        if($only_cart || $type!=Cart::BOTH)
            return $total;
        if($type== Cart::BOTH)
        {
            $custom_payment = Module::getInstanceByName('ets_payment_with_fee');
            $fee = $custom_payment->getFeePayOrderTotal($products,$withTaxes);
        }
        else
            $fee = 0;

        if($fee_payment)
            return $fee;
        return $fee + $total;
    }
    public function getProducts($refresh = false, $id_product = false, $id_country = null, $fullInfos = true, bool $keepOrderPrices = false,$default=false)
    {

        if($keepOrderPrices || $default || !Module::isEnabled('ets_extraoptions'))
        {
            $products  = parent::getProducts($refresh,$id_product,$id_country,$fullInfos,$keepOrderPrices);
            $custom_payment = Module::getInstanceByName('ets_payment_with_fee');
            $custom_payment->getProductsPaypal($products);
            return $products;
        }
        else
        {
            $this->_products = Module::getInstanceByName('ets_extraoptions')->getProducts($this,$refresh,$id_product,$id_country,$fullInfos,$keepOrderPrices);
            $custom_payment = Module::getInstanceByName('ets_payment_with_fee');
            $custom_payment->getProductsPaypal($this->_products);
            return $this->_products;
        }
    }
}