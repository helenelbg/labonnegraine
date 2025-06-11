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

class PsaffiliateRequestvoucherpaymentModuleFrontController extends ModuleFrontController
{
    protected $affiliate;

    public function __construct()
    {
        parent::__construct();
        $this->context = Context::getContext();
        $this->module->loadClasses(array('Affiliate', 'AffConf'));
    }

    public function initContent()
    {
        parent::initContent();

        return $this->displayTemplate();
    }

    public function displayTemplate()
    {
        if (!$this->context->customer->isLogged() ||
            !$this->isVoucherPaymentsEnabled() ||
            !$this->affiliateHasMinimumPaymentAmount()
        ) {
            Tools::redirect('index.php?controller=authentication&back='.urlencode(
                    $this->context->link->getModuleLink('psaffiliate', 'myaccount')
                ));
        }

        $currency = new Currency((int)Configuration::get('PS_CURRENCY_DEFAULT'));

        $this->context->smarty->assign('affiliate', (array)$this->getAffiliate());
        $this->context->smarty->assign('minimum_payment_amount', (float)$this->getConfig('minimum_payment_amount'));
        $this->context->smarty->assign('for_affiliates_only', (bool)$this->getConfig('vouchers_for_affiliates_only'));
        $this->context->smarty->assign('vouchers_exchange_rate', (float)$this->getConfig('vouchers_exchange_rate'));
        $this->context->smarty->assign('default_currency', $currency->id);
        $this->context->smarty->assign('currency_iso', $currency->iso_code);

        if (Tools::version_compare(_PS_VERSION_, '1.7', '<')) {
            $this->setTemplate("requestvoucherpayment.tpl");
        } else {
            $this->setTemplate("module:psaffiliate/views/templates/front/ps17/requestvoucherpayment.tpl");
        }
    }

    protected function getAffiliate()
    {
        if (!$this->affiliate) {
            $this->affiliate = new Affiliate($this->module->getAffiliateId());
        }

        return $this->affiliate;
    }

    protected function getConfig($config)
    {
        static $configs = array();

        return isset($configs[$config])
            ? $configs[$config]
            : $configs[$config] = AffConf::getConfig($config);
    }

    protected function isVoucherPaymentsEnabled()
    {
        return (bool)$this->getConfig('enable_voucher_payments');
    }

    protected function affiliateHasMinimumPaymentAmount()
    {
        $affiliate = $this->getAffiliate();
        $minimum_payment_amount = (float)$this->getConfig('minimum_payment_amount');

        return $affiliate->balance >= $minimum_payment_amount;
    }

    public function postProcess()
    {
        if (!Tools::isSubmit('submitRequestvoucherpayment')) {
            return;
        }

        if (!$this->context->customer->isLogged() ||
            !$this->isVoucherPaymentsEnabled() ||
            !$this->affiliateHasMinimumPaymentAmount()
        ) {
            return;
        }

        $amount = (float)Tools::getValue('amount');
        $exchange_rate = (float)$this->getConfig('vouchers_exchange_rate');
        $affiliate = $this->getAffiliate();

        if ($amount > $affiliate->balance) {
            return;
        }

        $this->module->loadClasses('Payment');

        $lang_ids = array_map('intval', Language::getIds());
        $names = array_fill(
            0,
            count($lang_ids),
            "PS Affiliate Voucher: {$affiliate->firstname} {$affiliate->lastname}"
        );

        $cart_rule = Payment::getVoucherCartRuleMock();
        $cart_rule->name = array_combine($lang_ids, $names);

        if ((bool)$this->getConfig('vouchers_for_affiliates_only')) {
            $cart_rule->id_customer = (int)$this->context->customer->id;
        }

        $cart_rule->partial_use = (int)$this->getConfig('vouchers_partial_use');
        $cart_rule->reduction_amount = Tools::ps_round($exchange_rate ? $amount * $exchange_rate : $amount, 2);
        $cart_rule->active = (int)$this->getConfig('vouchers_always_approved');

        if (!$cart_rule->add()) {
            $this->context->smarty->assign('success', false);

            return;
        }

        $payment_details = $exchange_rate
            ? "Amount requested: {$amount}\nVoucher exchange rate: ".$exchange_rate : '';

        $payment = new Payment();
        $payment->id_affiliate = (int)$this->getAffiliate()->id_affiliate;
        $payment->id_voucher = (int)$cart_rule->id;
        $payment->approved = (int)$this->getConfig('vouchers_always_approved');
        $payment->amount = $amount;
        $payment->paid = 1;
        $payment->payment_method = 0;
        $payment->payment_details = $payment_details;
        $payment->date = date('Y-m-d H:i:s');

        if (!$payment->add()) {
            $cart_rule->delete();
            $this->context->smarty->assign('success', false);

            return;
        }

        $this->context->smarty->assign('success', true);
    }

    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();

        $breadcrumb['links'][] = $this->addMyAccountToBreadcrumb();
        $breadcrumb['links'][] = array(
            'title' => $this->l('Affiliate Account'),
            'url' => $this->context->link->getModuleLink('psaffiliate', 'myaccount'),
        );

        return $breadcrumb;
    }

    public function l($string, $specific = false, $class = null, $addslashes = false, $htmlentities = true)
    {
        return Translate::getModuleTranslation('psaffiliate', $string, 'campaign');
    }
}
