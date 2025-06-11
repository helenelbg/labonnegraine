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

class PsaffiliateRequestpaymentModuleFrontController extends ModuleFrontController
{
    public function __construct()
    {
        parent::__construct();
        $this->context = Context::getContext();
        $this->module->loadClasses('Affiliate');
    }

    public function initContent()
    {
        parent::initContent();
        Media::addJsDef(array(
            'wrongamount_error' => $this->l('The amount has to be between %min% and %max%.'),
            'choosemethod_error' => $this->l('Please choose a payment method'),
        ));

        return $this->displayTemplate();
    }

    public function displayTemplate()
    {
        if ($this->context->customer->isLogged()) {
            $id_currency = Configuration::get('PS_CURRENCY_DEFAULT');
            $currency = new Currency($id_currency);
            $this->module->loadClasses(array('AffConf', 'PaymentMethod'));
            $affiliate = new Affiliate($this->module->getAffiliateId());
            $minimum_payment_amount = (float)AffConf::getConfig('minimum_payment_amount');
            $invoices_enabled = (bool)AffConf::getConfig('enable_invoices');
            $invoices_mandatory = (bool)AffConf::getConfig('mandatory_invoices');
            $invoicing_details = trim(AffConf::getConfig('invoicing_details', true));

            $payment_methods = PaymentMethod::getPaymentMethodsWithFields();
            $currency_iso = $currency->iso_code;
            $this->context->smarty->assign('affiliate', (array)$affiliate);
            $this->context->smarty->assign('minimum_payment_amount', $minimum_payment_amount);
            $this->context->smarty->assign('payment_methods', $payment_methods);
            $this->context->smarty->assign('currency_iso', $currency_iso);
            $this->context->smarty->assign('default_currency', $currency->id);
            $this->context->smarty->assign('invoices_enabled', $invoices_enabled);
            $this->context->smarty->assign('invoices_mandatory', $invoices_mandatory);
            $this->context->smarty->assign('invoicing_details', $invoicing_details);

            if (Tools::version_compare(_PS_VERSION_, '1.7', '<')) {
                $this->setTemplate("requestpayment.tpl");
            } else {
                $this->setTemplate("module:psaffiliate/views/templates/front/ps17/requestpayment.tpl");
            }
        } else {
            Tools::redirect('index.php?controller=authentication&back='.urlencode($this->context->link->getModuleLink(
                    'psaffiliate',
                    'myaccount'
                )));
        }
    }

    public function postProcess()
    {
        if (Tools::getValue('submitRequestpayment')) {
            $this->module->loadClasses(array('Payment', 'PaymentMethod', 'AffConf'));

            $payment = new Payment;
            $payment->id_affiliate = $this->module->getAffiliateId();
            $payment->approved = 0;
            $payment->amount = (float)Tools::getValue('amount');
            $payment->paid = 0;
            $payment->payment_method = (int)Tools::getValue('payment_method');
            $payment->payment_details = "";
            $payment->date = date('Y-m-d H:i:s');
            $payment_method = new PaymentMethod($payment->payment_method);

            if (!Validate::isLoadedObject($payment_method)) {
                $this->errors[] = $this->l('Payment method not found.');

                return;
            }

            $invoices_enabled = (bool)AffConf::getConfig('enable_invoices');
            $invoices_mandatory = (bool)AffConf::getConfig('mandatory_invoices');

            if ($invoices_enabled) {
                $no_invoice = true;
                if (!isset($_FILES['invoice'])
                    || !isset($_FILES['invoice']['tmp_name'])
                    || empty($_FILES['invoice']['tmp_name'])
                ) {
                    if ($invoices_mandatory) {
                        $this->errors[] = $this->l('Invoice upload required for payment.');

                        return;
                    }
                } else {
                    $no_invoice = false;
                }
                if (!$no_invoice) {
                    $ext = Tools::substr($_FILES['invoice']['name'], strrpos($_FILES['invoice']['name'], '.') + 1);

                    if ($ext !== 'pdf' && $ext !== 'zip') {
                        $this->errors[] = $this->l('File must be in .pdf or .zip format.');

                        return;
                    }

                    $maxFileSize = 4000000;

                    if ((int)$maxFileSize > 0 && $_FILES['invoice']['size'] > (int)$maxFileSize) {
                        $this->errors[] = sprintf(
                            $this->l('File is too large (%1$d kB). Maximum allowed: %2$d kB'),
                            $_FILES['invoice']['size'] / 1024,
                            $maxFileSize / 1024
                        );

                        return;
                    }

                    if ($_FILES['invoice']['error']) {
                        $this->errors[] = $this->l('An error occurred while attempting to upload the file.');

                        return;
                    }

                    $fileName = md5($_FILES['invoice']['name']).'.'.$ext;

                    if (!move_uploaded_file(
                        $_FILES['invoice']['tmp_name'],
                        $this->module->getLocalPath()
                        .'uploads'.DIRECTORY_SEPARATOR
                        .'invoices'.DIRECTORY_SEPARATOR
                        .$fileName
                    )
                    ) {
                        $this->errors[] = $this->l('An error occurred while attempting to upload the file.');

                        return;
                    }

                    $payment->invoice = $fileName;
                }
            }

            $fields = $payment_method->getPaymentMethodFields($payment_method->id);
            foreach ($fields as $field) {
                if (Tools::getValue('paymentmethodfield_'.$field['id_payment_method_field'])) {
                    $payment->payment_details .= $field['field_name'].": ".pSQL(Tools::getValue('paymentmethodfield_'.$field['id_payment_method_field']))."\r\n";
                }
            }

            if ($payment->add()) {
                $this->context->smarty->assign('success', true);

                $admin_emails = $this->module->getAdminEmails();
                if ($admin_emails) {
                    $iso = Language::getIsoById((int) Configuration::get('PS_LANG_DEFAULT'));
                    $dir_mail = false;

                    if (file_exists($this->module->getPathDir().'/mails/'.$iso.'/admin_new_payment.txt') &&
                        file_exists($this->module->getPathDir().'/mails/'.$iso.'/admin_new_payment.html')) {
                        $dir_mail = $this->module->getPathDir().'/mails/';
                    }

                    if (file_exists(_PS_MAIL_DIR_.$iso.'/admin_new_payment.txt') &&
                        file_exists(_PS_MAIL_DIR_.$iso.'/admin_new_payment.html')) {
                        $dir_mail = _PS_MAIL_DIR_;
                    }

                    if (!$dir_mail) {
                        $iso = 'en';
                        if (file_exists($this->module->getPathDir().'/mails/'.$iso.'/admin_new_payment.txt') &&
                            file_exists($this->module->getPathDir().'/mails/'.$iso.'/admin_new_payment.html')) {
                            $dir_mail = $this->module->getPathDir().'/mails/';
                        }

                        if (file_exists(_PS_MAIL_DIR_.$iso.'/admin_new_payment.txt') &&
                            file_exists(_PS_MAIL_DIR_.$iso.'/admin_new_payment.html')) {
                            $dir_mail = _PS_MAIL_DIR_;
                        }
                    }

                    $mail_id_lang = (int)Configuration::get('PS_LANG_DEFAULT');
                    $affiliate = new Affiliate($payment->id_affiliate);
                    foreach ($admin_emails as $admin_email) {
                        if ($dir_mail) {
                            $template_vars = array(
                                '{email}' => $affiliate->email,
                                '{name}' => $affiliate->firstname." ".$affiliate->lastname,
                                '{amount}' => Tools::displayPrice($payment->amount, (int)Configuration::get('PS_CURRENCY_DEFAULT')),
                                '{payment_method}' => $payment_method->name,
                                '{payment_method_fields_html}' => nl2br($payment->payment_details),
                                '{payment_method_fields_txt}' => $payment->payment_details,
                            );
                            Mail::Send(
                                $mail_id_lang,
                                'admin_new_payment',
                                sprintf(Mail::l('New payment request: %s', $mail_id_lang), $affiliate->firstname." ".$affiliate->lastname),
                                $template_vars,
                                $admin_email,
                                null,
                                Configuration::get('PS_SHOP_EMAIL'),
                                Configuration::get('PS_SHOP_NAME'),
                                null,
                                null,
                                $dir_mail,
                                null,
                                (int)$this->context->shop->id
                            );
                        }
                    }
                }

            } else {
                $this->context->smarty->assign('success', false);

                // Saving payment failed, delete invoice if exists.
                if ($payment->invoice) {
                    @unlink(
                        $this->module->getLocalPath()
                        .'uploads'.DIRECTORY_SEPARATOR
                        .'invoices'.DIRECTORY_SEPARATOR
                        .$payment->invoice
                    );
                }
            }
        }
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
        return Translate::getModuleTranslation('psaffiliate', $string, 'requestpayment');
    }
}
