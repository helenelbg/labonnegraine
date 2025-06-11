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

class Payment extends ObjectModel
{
    public $id;
    public $id_payment;
    public $id_affiliate;
    public $id_voucher = 0;
    public $approved = 0;
    public $amount = 0;
    public $paid = 0;
    public $payment_method = 0;
    public $payment_details = null;
    public $notes = null;
    public $invoice = null;
    public $date;

    public static $definition = array(
        'table' => 'aff_payments',
        'primary' => 'id_payment',
        'fields' => array(
            'id_payment' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'copy_post' => false),
            'date' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
            'id_affiliate' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'copy_post' => false),
            'id_voucher' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'copy_post' => false),
            'approved' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'copy_post' => false),
            'amount' => array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat', 'copy_post' => false),
            'paid' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'copy_post' => false),
            'payment_method' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'copy_post' => false),
            'payment_details' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'copy_post' => false),
            'notes' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'copy_post' => false),
            'invoice' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'copy_post' => false),

        ),
    );

    public function __construct($id=0)
    {
        $this->moduleObj = Module::getInstanceByName('psaffiliate');
        $this->moduleObj->loadClasses('Affiliate');

        return parent::__construct($id);
    }

    public function update($null_values = false)
    {
        $payment = new Payment($this->id);
        if (!$payment->approved && $this->approved) {
            $aff = new Affiliate($this->id_affiliate);
            $customer = new Customer($aff->id_customer);
            $payment_method = new PaymentMethod($this->payment_method);
            $iso = Language::getIsoById((int)$customer->id_lang);
            $dir_mail = false;

            if (file_exists($this->moduleObj->getPathDir().'/mails/'.$iso.'/payment_approved.txt') &&
                file_exists($this->moduleObj->getPathDir().'/mails/'.$iso.'/payment_approved.html')) {
                $dir_mail = $this->moduleObj->getPathDir().'/mails/';
            }

            if (file_exists(_PS_MAIL_DIR_.$iso.'/payment_approved.txt') &&
                file_exists(_PS_MAIL_DIR_.$iso.'/payment_approved.html')) {
                $dir_mail = _PS_MAIL_DIR_;
            }

            if (!$dir_mail) {
                $iso = 'en';
                if (file_exists($this->moduleObj->getPathDir().'/mails/'.$iso.'/payment_approved.txt') &&
                    file_exists($this->moduleObj->getPathDir().'/mails/'.$iso.'/payment_approved.html')) {
                    $dir_mail = $this->moduleObj->getPathDir().'/mails/';
                }

                if (file_exists(_PS_MAIL_DIR_.$iso.'/payment_approved.txt') &&
                    file_exists(_PS_MAIL_DIR_.$iso.'/payment_approved.html')) {
                    $dir_mail = _PS_MAIL_DIR_;
                }
            }

            $mail_id_lang = (int)$customer->id_lang;
            if ($dir_mail) {
                $template_vars = array(
                    '{name}' => $aff->firstname." ".$aff->lastname,
                    '{amount}' => Tools::displayPrice($this->amount, (int)Configuration::get('PS_CURRENCY_DEFAULT')),
                    '{payment_method}' => $payment_method->name,
                    '{payment_method_fields_html}' => nl2br($this->payment_details),
                    '{payment_method_fields_txt}' => $this->payment_details,
                );
                Mail::Send(
                    $mail_id_lang,
                    'payment_approved',
                    Mail::l('Your payment has been approved', $mail_id_lang),
                    $template_vars,
                    $aff->email,
                    null,
                    Configuration::get('PS_SHOP_EMAIL'),
                    Configuration::get('PS_SHOP_NAME'),
                    null,
                    null,
                    $dir_mail,
                    null,
                    (int)$customer->id_shop
                );
            }
        }
        return parent::update($null_values);
    }

    public function toggleApproved()
    {
        $this->approved = !$this->approved;

        return $this->save();
    }

    public function togglePaid()
    {
        $this->paid = !$this->paid;

        return $this->save();
    }

    public static function getAffiliatePayments($id_affiliate = false, $limit = false, $reverse = true)
    {
        $sql = "SELECT ap.*";
        if (!$id_affiliate) {
            $sql .= ', CONCAT("#", af.`id_affiliate`, " - ", IF(af.`id_customer` <> 0, CONCAT(c.`firstname`, " ", c.`lastname`), CONCAT(af.`firstname`, " ", af.`lastname`))) as `affiliate_name`';
        }
        $sql .= " FROM `"._DB_PREFIX_."aff_payments` ap";
        if ($id_affiliate) {
            $sql .= " WHERE ap.`id_affiliate`='".(int)$id_affiliate."'";
        } else {
            $sql .= ' LEFT JOIN `'._DB_PREFIX_.'aff_affiliates` af on (af.`id_affiliate`=ap.`id_affiliate`)
            LEFT JOIN `'._DB_PREFIX_.'customer` c ON (c.`id_customer` = af.`id_customer`)
            ';
        }
        if ($reverse) {
            $sql .= " ORDER BY ap.`id_payment` DESC";
        }
        if ($limit) {
            $sql .= " LIMIT ".(int)$limit;
        }

        return Db::getInstance()->executeS($sql);
    }

    public static function getVoucherCartRuleMock()
    {
        $cart_rule = new CartRule();
        $date_from = (new DateTime())->modify('-100 years');
        $date_to = (new DateTime())->modify('+100 years');

        $cart_rule->date_from = $date_from->format('Y-m-d H:i:s');
        $cart_rule->date_to = $date_to->format('Y-m-d H:i:s');
        $cart_rule->code = static::genVoucherCode();
        $cart_rule->minimum_amount = 0.00;
        $cart_rule->minimum_amount_tax = 0;
        $cart_rule->minimum_amount_currency = (int)Configuration::get('PS_CURRENCY_DEFAULT');
        $cart_rule->minimum_amount_shipping = 0;
        $cart_rule->country_restriction = 0;
        $cart_rule->carrier_restriction = 0;
        $cart_rule->group_restriction = 0;
        $cart_rule->cart_rule_restriction = 0;
        $cart_rule->product_restriction = 0;
        $cart_rule->shop_restriction = 0;
        $cart_rule->free_shipping = 0;
        $cart_rule->reduction_percent = 0.00;
        $cart_rule->reduction_tax = 1;
        $cart_rule->reduction_currency = (int)Configuration::get('PS_CURRENCY_DEFAULT');
        $cart_rule->reduction_product = 0;
        $cart_rule->reduction_exclude_special = 0;
        $cart_rule->gift_product = 0;
        $cart_rule->gift_product_attribute = 0;
        $cart_rule->highlight = 0;
        $cart_rule->active = 0;

        return $cart_rule;
    }

    protected static function genVoucherCode($length = 8)
    {
        $chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $code = '';

        for ($i = 0; $i < $length; $i++) {
            $code .= $chars[mt_rand(0, Tools::strlen($chars) - 1)];
        }

        return $code;
    }

    public function delete()
    {
        // Delete invoice as well if exists.
        if ($status = parent::delete() && $this->invoice) {
            @unlink(
                _PS_MODULE_DIR_
                .'psaffiliate'.DIRECTORY_SEPARATOR
                .'uploads'.DIRECTORY_SEPARATOR
                .'invoices'.DIRECTORY_SEPARATOR
                .$this->invoice
            );
        }

        return $status;
    }

    public function getInvoiceLink()
    {
        if (!$this->invoice) {
            return null;
        }

        return _PS_BASE_URL_SSL_.__PS_BASE_URI__.'modules/psaffiliate/uploads/invoices/'.$this->invoice;
    }
}
