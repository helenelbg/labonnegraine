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

class HTMLTemplateInvoice extends HTMLTemplateInvoiceCore
{
    public function getContent()
    {
        parent::getContent();
        $ets_payment_with_fee = Module::getInstanceByName('ets_payment_with_fee');
        $ets_payment_with_fee->initContentHTMLTemplateInvoice($this->order->id,$this->smarty);
        $invoice_address = new Address((int)$this->order->id_address_invoice);
        $country = new Country((int)$invoice_address->id_country);
        return $this->smarty->fetch($this->getTemplateByCountry($country->iso_code));
    }
    public function getTaxTabContent()
    {
        parent::getTaxTabContent();
        return $this->smarty->fetch(_PS_MODULE_DIR_.'ets_payment_with_fee/views/templates/hook/invoice.tax-tab.tpl');
    }
    protected function getTaxBreakdown()
    {
        $breakdowns = parent::getTaxBreakdown();
        if($payment_fees = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'ets_paymentmethod_order` WHERE id_order='.(int)$this->order->id.' AND fee_incl > fee'))
        {
            $tax = $payment_fees['fee_incl'] - $payment_fees['fee'];
            $rate = Tools::ps_round($tax/$payment_fees['fee'],3)*100;
            $breakdowns['payment_fee'] = array(
                array(
                    'total_price_tax_excl' => $payment_fees['fee'],
                    'total_tax_excl' => $payment_fees['fee'],
                    'total_amount' => $tax,
                    'rate' => number_format($rate,3),
                    'id_tax' => 1,
                )
            );
        }
        return $breakdowns;
    }
}