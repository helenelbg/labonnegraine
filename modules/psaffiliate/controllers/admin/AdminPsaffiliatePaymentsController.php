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

class AdminPsaffiliatePaymentsController extends AdminController
{
    public $module;
    public $fields_list;
    protected $_defaultOrderBy = 'id_payment';
    protected $_defaultOrderWay = 'DESC';
    public $id_payment = 0;
    public $obj;

    public function __construct()
    {
        $this->moduleObj = Module::getInstanceByName('psaffiliate');
        $this->moduleObj->loadClasses('Payment');
        if (Tools::getValue('id_payment')) {
            $this->obj = new Payment((int)Tools::getValue('id_payment'));
        }

        $this->bootstrap = true;
        $this->required_database = false;
        $this->table = 'aff_payments';
        $this->identifier = 'id_payment';
        $this->className = 'Payment';
        $this->lang = false;
        $this->explicitSelect = true;

        $this->allow_export = true;

        $this->addRowAction('edit');
        $this->addRowAction('delete');
        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'confirm' => $this->l('Delete selected items?'),
                'icon' => 'icon-trash',
            ),
        );

        $this->context = Context::getContext();

        $this->default_form_language = $this->context->language->id;

        $this->_use_found_rows = false;
        $this->fields_list = array(
            'id_payment' => array(
                'title' => $this->l('ID'),
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
                'search' => true,
                'filter_type' => 'int',
                'filter_key' => 'a!id_payment',
            ),
            'date' => array(
                'title' => $this->l('Date'),
                'align' => 'text-left',
                'width' => 'auto',
                'search' => true,
                'type' => 'datetime',
            ),
            'affiliate_name' => array(
                'title' => $this->l('Affiliate'),
                'align' => 'text-left',
                'width' => 'auto',
                'search' => true,
                //'havingFilter' => true,
                'type' => 'select',
                'list' => $this->moduleObj->getAffiliatesList(),
                'filter_key' => 'a!id_affiliate',
            ),
            'amount' => array(
                'title' => $this->l('Amount'),
                'align' => 'text-left',
                'width' => 'auto',
                'search' => true,
                'filter_type' => 'float',
                'type' => 'price',
            ),
            'payment_method_name' => array(
                'title' => $this->l('Payment method'),
                'align' => 'text-left',
                'width' => 'auto',
                'search' => true,
                'type' => 'select',
                'list' => $this->getPaymentMethodsList(),
                'filter_key' => 'a!payment_method',
            ),
            'approved' => array(
                'title' => $this->l('Approved'),
                'align' => 'text-center',
                'active' => 'approved',
                'type' => 'bool',
                'orderby' => false,
                'filter_key' => 'a!approved',
            ),
            'paid' => array(
                'title' => $this->l('Paid'),
                'align' => 'text-center',
                'active' => 'paid',
                'type' => 'bool',
                'orderby' => false,
                'filter_key' => 'a!paid',
            ),
        );

        $this->_join = 'LEFT JOIN `'._DB_PREFIX_.'aff_affiliates` af on (af.`id_affiliate`=a.`id_affiliate`)';
        $this->_join .= ' LEFT JOIN `'._DB_PREFIX_.'customer` c ON (c.`id_customer` = af.`id_customer`)';
        $this->_join .= ' LEFT JOIN `'._DB_PREFIX_.'aff_payment_methods` pm ON (a.`payment_method` = pm.`id_payment_method`)';
        $this->_select = '
            CONCAT("#", af.`id_affiliate`, " - ", IF(af.`id_customer` <> 0, CONCAT(c.`firstname`, " ", c.`lastname`), CONCAT(af.`firstname`, " ", af.`lastname`))) as `affiliate_name`,
            IF(pm.`name` IS NULL AND `id_voucher` != 0, "voucher", pm.`name`) as `payment_method_name`
        ';
        $this->shopLinkType = '';

        parent::__construct();
    }

    public function init()
    {
        parent::init();
        $this->addJS(_PS_MODULE_DIR_.'psaffiliate/views/js/admin/bootstrap-select.min.js');
        $this->addCSS(_PS_MODULE_DIR_.'psaffiliate/views/css/bootstrap-select.min.css');
        $this->addJS(_PS_MODULE_DIR_.'psaffiliate/views/js/admin/ajax-bootstrap-select.min.js');
        $this->addCSS(_PS_MODULE_DIR_.'psaffiliate/views/css/ajax-bootstrap-select.min.css');
        Media::addJsDef(array(
            'ajaxtools_url' => $this->context->link->getAdminLink('AdminPsaffiliateAjax'),
        ));
        $this->addJS(_PS_MODULE_DIR_.'psaffiliate/views/js/admin/ajaxtools.js');
    }

    public function initContent()
    {
        if (Tools::isSubmit('approvedaff_payments') !== false) {
            $this->obj->toggleApproved();
            // Toggle voucher too if exists.
            if ((int)$this->obj->id_voucher) {
                $cart_rule = new CartRule((int)$this->obj->id_voucher);
                // Inverse previous Payment approved value
                $cart_rule->active = $this->obj->approved == 1 ? 0 : 1;
                $cart_rule->save();
            }
        }
        if (Tools::isSubmit('paidaff_payments') !== false) {
            $this->obj->togglePaid();
        }
        parent::initContent();

        $this->meta_title = $this->l('Payments');
    }

    public function renderList()
    {
        return parent::renderList();
    }

    public function renderView()
    {
        return parent::renderView();
    }

    public function renderForm()
    {
        $this->moduleObj->loadClasses(array('Affiliate', 'PaymentMethod', 'AffConf'));

        $payment_methods = PaymentMethod::getPaymentMethods();
        $default_payment = 0;

        if (count($payment_methods)) {
            $default_payment = $payment_methods[0]['id_payment_method'];
        }

        if ((bool)AffConf::getConfig('enable_voucher_payments')) {
            $payment_methods[] = array(
                'id_payment_method' => '0',
                'name' => 'voucher',
                'description' => '',
            );
        }

        $id_currency = Configuration::get('PS_CURRENCY_DEFAULT');
        $currency = new Currency($id_currency);
        $has_voucher = $this->obj !== null && $this->obj->id_voucher;

        $this->fields_form = array(
            'legend' => array(
                'title' => isset($this->obj->id) ? $this->l('Payment')." #".$this->obj->id : $this->l('Payment'),
                'icon' => 'icon-user',
            ),
            'input' => array(
                array(
                    'type' => 'select',
                    'label' => $this->l('Affiliate'),
                    'name' => 'id_affiliate',
                    'class' => 'ajaxselectpicker',
                    'col' => '4',
                    'options' => array(
                        'query' => Affiliate::getAffiliates(
                            false,
                            false,
                            false,
                            false,
                            false,
                            $this->obj->id_affiliate
                        ),
                        'id' => 'id_affiliate',
                        'name' => 'idandname',
                    ),
                ),
                array(
                    'type' => 'datetime',
                    'label' => $this->l('Date'),
                    'name' => 'date',
                    'col' => '4',
                    'autocomplete' => false,
                ),
                array(
                    'type' => 'text',
                    'suffix' => $currency->iso_code,
                    'label' => $this->l('Amount'),
                    'name' => 'amount',
                    'col' => '4',
                    'autocomplete' => false,
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Payment method'),
                    'name' => 'payment_method',
                    'col' => '4',
                    'autocomplete' => false,
                    'options' => array(
                        'query' => $payment_methods,
                        'id' => 'id_payment_method',
                        'name' => 'name',
                    ),
                ),
                array(
                    'type' => $has_voucher ? 'text' : 'hidden',
                    'label' => $has_voucher ? $this->l('Voucher cart rule ID') : null,
                    'desc' => $this->l('Read-only field.'),
                    'hint' => $this->l('Cart rule used for the voucher.'),
                    'name' => 'id_voucher',
                    'col' => '1',
                    'readonly' => true,
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->l('Payment details'),
                    'name' => 'payment_details',
                    'col' => '4',
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->l('Payment notes'),
                    'name' => 'notes',
                    'col' => '4',
                ),
                array(
                    'type' => $this->obj->invoice ? 'html' : 'hidden',
                    'label' => $this->obj->invoice ? $this->l('Invoice') : '',
                    'name' => '',
                    'html_content' => $this->obj->invoice ?
                        $this->context->smarty
                            ->assign('invoice_link', $this->obj->getInvoiceLink())
                            ->fetch($this->moduleObj->getLocalPath().'views/templates/admin/invoice_btn.tpl') : '',
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Approved'),
                    'name' => 'approved',
                    'required' => false,
                    'class' => 't',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'approved_on',
                            'value' => 1,
                            'label' => $this->l('Yes'),
                        ),
                        array(
                            'id' => 'approved_off',
                            'value' => 0,
                            'label' => $this->l('No'),
                        ),
                    ),
                    'hint' => $this->l('Approve or disapprove payment request.'),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Paid'),
                    'name' => 'paid',
                    'required' => false,
                    'class' => 't',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'paid_on',
                            'value' => 1,
                            'label' => $this->l('Yes'),
                        ),
                        array(
                            'id' => 'paid_off',
                            'value' => 0,
                            'label' => $this->l('No'),
                        ),
                    ),
                    'hint' => $this->l('Has the commission been paid?'),
                ),
            ),
        );
        $this->fields_form['submit'] = array(
            'title' => $this->l('Save'),
        );
        $this->fields_value = (array)$this->obj;

        return parent::renderForm();
    }

    public function processAdd()
    {
        // If voucher was selected...
        if ((int)Tools::getValue('payment_method') === 0) {
            $this->createVoucherForPayment();
        }

        return parent::processAdd();
    }

    public function processUpdate()
    {
        // If payment method selected is not "voucher" and payment has id_voucher...
        if (Tools::getValue('payment_method') !== '0' && (int)$this->obj->id_voucher) {
            (new CartRule((int)$this->obj->id_voucher))->delete();
        }

        // If payment method selected is "voucher"...
        if (Tools::getValue('payment_method') === '0') {
            // If payment doesn't have voucher already...
            if (!(int)$this->obj->id_voucher) {
                $this->createVoucherForPayment();
            } else {
                $cart_rule = new CartRule((int)$this->obj->id_voucher);
                if ($this->obj->amount != Tools::getValue('amount')) {
                    // If amount changed, just create a new voucher (easier).
                    $cart_rule->delete();
                    $this->createVoucherForPayment();
                } else {
                    // Update voucher status if payment is approved.
                    $cart_rule->active = (int)Tools::getValue('approved');
                    $cart_rule->save();
                }
            }
        }

        return parent::processUpdate();
    }

    public function processDelete()
    {
        // If payment has id_voucher
        if ((int)$this->obj->id_voucher) {
            (new CartRule((int)$this->obj->id_voucher))->delete();
        }

        return parent::processDelete();
    }

    protected function createVoucherForPayment()
    {
        $this->moduleObj->loadClasses(array('Affiliate', 'AffConf', 'Payment'));

        $affiliate = new Affiliate((int)Tools::getValue('id_affiliate'));
        $amount = (float)Tools::getValue('amount');
        $exchange_rate = (float)AffConf::getConfig('vouchers_exchange_rate');
        $lang_ids = array_map('intval', Language::getIds());
        $names = array_fill(
            0,
            count($lang_ids),
            "PS Affiliate Voucher: {$affiliate->firstname} {$affiliate->lastname}"
        );

        $cart_rule = Payment::getVoucherCartRuleMock();
        $cart_rule->name = array_combine($lang_ids, $names);

        if ((bool)AffConf::getConfig('vouchers_for_affiliates_only') &&
            $id_affiliate = (int)Tools::getValue('id_affiliate')
        ) {
            $cart_rule->id_customer = $this->moduleObj->getCustomerId($id_affiliate);
        }

        $cart_rule->partial_use = (int)AffConf::getConfig('vouchers_partial_use');
        $cart_rule->reduction_amount = Tools::ps_round($exchange_rate ? $amount * $exchange_rate : $amount, 2);

        $cart_rule->active = (bool)Tools::getValue('approved');

        $cart_rule->add();

        // Adding this to the POST super global so it can be
        // picked up by parent::processAdd() or parent::processUpdate()
        $_POST['id_voucher'] = (int)$cart_rule->id;
        if ($exchange_rate) {
            $_POST['payment_details'] = $exchange_rate
                ? "Amount requested: {$amount}\nVoucher exchange rate: ".$exchange_rate : '';
        }
    }

    public function getPaymentMethodsList()
    {
        $this->moduleObj->loadClasses('PaymentMethod');
        $payment_methods = PaymentMethod::getPaymentMethods();
        $array = array();
        foreach ($payment_methods as $pm) {
            $array[$pm['id_payment_method']] = $pm['name'];
        }

        $array[0] = 'voucher';

        return $array;
    }

    public function l($string, $class = null, $addslashes = false, $htmlentities = true)
    {
        return Translate::getModuleTranslation('psaffiliate', $string, get_class($this));
    }
}
