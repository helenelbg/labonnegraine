<?php

use PrestaShop\PrestaShop\Core\Payment\PaymentOption;

if (!defined('_PS_VERSION_')) {
    exit;
}

class Mandatadministratif extends PaymentModule
{
    private $_html = '';
    private $_postErrors = [];

    public $checkName;
    public $address;
    public $extra_mail_vars;

    public function __construct()
    {
        $this->name = 'mandatadministratif';
        $this->tab = 'payments_gateways';
        $this->version = '2.0.5';
        $this->author = 'Anjouweb';
        $this->controllers = ['payment', 'validation'];

        $this->currencies = true;
        $this->currencies_mode = 'checkbox';

		$config = Configuration::getMultiple(array('MANDAT_DETAILS', 'MANDAT_OWNER', 'MANDAT_ADDRESS'));
		if (isset($config['MANDAT_OWNER']))
			$this->owner = $config['MANDAT_OWNER'];
		if (isset($config['MANDAT_DETAILS']))
			$this->details = $config['MANDAT_DETAILS'];
		if (isset($config['MANDAT_ADDRESS']))
			$this->address = $config['MANDAT_ADDRESS'];

        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->trans('Mandat administratif', [], 'Modules.Mandatadministratif.Admin');
        $this->description = $this->trans('Ajoute un systeme de paiement Mandat administratif.', [], 'Modules.Mandatadministratif.Admin');
        $this->confirmUninstall = $this->trans('Are you sure you want to delete these details?', [], 'Modules.Mandatadministratif.Admin');
        $this->ps_versions_compliancy = ['min' => '1.7.1.0', 'max' => _PS_VERSION_];

        $this->extra_mail_vars = [
                                    '{check_name}' => Configuration::get('CHEQUE_NAME'),
                                    '{check_address}' => Configuration::get('CHEQUE_ADDRESS'),
                                    '{check_address_html}' => Tools::nl2br(Configuration::get('CHEQUE_ADDRESS')),
                                ];
    }

    public function install()
    {
        return parent::install()
            && $this->registerHook('paymentOptions')
            && $this->registerHook('paymentReturn')
        ;
    }

    public function uninstall()
    {
        return Configuration::deleteByName('CHEQUE_NAME')
            && Configuration::deleteByName('CHEQUE_ADDRESS')
            && parent::uninstall()
        ;
    }

    private function _postValidation()
    {

    }

    private function _postProcess()
    {
        if (Tools::isSubmit('btnSubmit')) {
			Configuration::updateValue('MANDAT_DETAILS', Tools::getValue('MANDAT_DETAILS'));
			Configuration::updateValue('MANDAT_OWNER', Tools::getValue('MANDAT_OWNER'));
			Configuration::updateValue('MANDAT_ADDRESS', Tools::getValue('MANDAT_ADDRESS'));
        }
        $this->_html .= $this->displayConfirmation($this->trans('Settings updated', [], 'Admin.Notifications.Success'));
    }

    private function _displayCheck()
    {
        return $this->display(__FILE__, './views/templates/hook/infos.tpl');
    }

    public function getContent()
    {
        $this->_html = '';

        if (Tools::isSubmit('btnSubmit')) {
            $this->_postValidation();
            if (!count($this->_postErrors)) {
                $this->_postProcess();
            } else {
                foreach ($this->_postErrors as $err) {
                    $this->_html .= $this->displayError($err);
                }
            }
        }

        $this->_html .= $this->_displayCheck();
        $this->_html .= $this->renderForm();

        return $this->_html;
    }

    public function hookPaymentOptions($params)
    {
        if (!$this->active) {
            return;
        }
        if (!$this->checkCurrency($params['cart'])) {
            return;
        }

        $this->smarty->assign(
            $this->getTemplateVars()
        );

        $newOption = new PaymentOption();
        $newOption->setModuleName($this->name)
                ->setCallToActionText('Payer par mandat administratif (réservé aux administrations, écoles, collectivités)')
                ->setAction($this->context->link->getModuleLink($this->name, 'validation', [], true))
                ->setAdditionalInformation($this->fetch('module:mandatadministratif/views/templates/front/payment_infos.tpl'));

        return [$newOption];
    }

    public function hookPaymentReturn($params)
    {
        if (!$this->active) {
            return;
        }

        $state = $params['order']->getCurrentState();
        $rest_to_paid = $params['order']->getOrdersTotalPaid() - $params['order']->getTotalPaid();
        if (in_array($state, [Configuration::get('PS_OS_MANDAT'), Configuration::get('PS_OS_OUTOFSTOCK'), Configuration::get('PS_OS_OUTOFSTOCK_UNPAID')])) {
            $this->smarty->assign([
                'total_to_pay' => Tools::displayPrice(
                    $rest_to_paid,
                    new Currency($params['order']->id_currency),
                    false
                ),
                'shop_name' => $this->context->shop->name,
                'checkName' => $this->checkName,
                'checkAddress' => Tools::nl2br($this->address),
                'status' => 'ok',
                'id_order' => $params['order']->id,
            ]);
            if (isset($params['order']->reference) && !empty($params['order']->reference)) {
                $this->smarty->assign('reference', $params['order']->reference);
            }
        } else {
            $this->smarty->assign('status', 'failed');
        }

        return $this->fetch('module:mandatadministratif/views/templates/hook/payment_return.tpl');
    }

    public function checkCurrency($cart)
    {
        $currency_order = new Currency((int) ($cart->id_currency));
        $currencies_module = $this->getCurrency((int) $cart->id_currency);

        if (is_array($currencies_module)) {
            foreach ($currencies_module as $currency_module) {
                if ($currency_order->id == $currency_module['id_currency']) {
                    return true;
                }
            }
        }

        return false;
    }

    public function renderForm()
    {
        $fields_form = [
            'form' => [
                'legend' => [
                    'title' => $this->trans('Contact details', [], 'Modules.Mandatadministratif.Admin'),
                    'icon' => 'icon-envelope',
                ],
                'input' => [
                    [
                        'type' => 'text',
                        'label' => 'Titulaire',
                        'name' => 'MANDAT_OWNER',
                        'required' => true,
                    ],
                    [
                        'type' => 'textarea',
                        'label' => 'Détails',
                        'name' => 'MANDAT_DETAILS',
                        'required' => true,
                    ],
					[
                        'type' => 'textarea',
                        'label' => 'Adresse de la banque',
                        'name' => 'MANDAT_ADDRESS',
                        'required' => true,
                    ],
                ],
                'submit' => [
                    'title' => $this->trans('Save', [], 'Admin.Actions'),
                ],
            ],
        ];

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->id = (int) Tools::getValue('id_carrier');
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'btnSubmit';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = [
            'fields_value' => $this->getConfigFieldsValues(),
        ];

        return $helper->generateForm([$fields_form]);
    }

    public function getConfigFieldsValues()
    {
        return [
            'MANDAT_DETAILS' => Tools::getValue('MANDAT_DETAILS', Configuration::get('MANDAT_DETAILS')),
            'MANDAT_OWNER' => Tools::getValue('MANDAT_OWNER', Configuration::get('MANDAT_OWNER')),
            'MANDAT_ADDRESS' => Tools::getValue('MANDAT_ADDRESS', Configuration::get('MANDAT_ADDRESS')),
        ];
    }

    public function getTemplateVars()
    {
        $cart = $this->context->cart;
        $total = $this->trans(
            '%amount% (tax incl.)',
            [
                '%amount%' => Tools::displayPrice($cart->getOrderTotal(true, Cart::BOTH)),
            ],
            'Modules.Mandatadministratif.Admin'
        );

        $details = Configuration::get('MANDAT_DETAILS');
        if (!$details) {
            $details = '___________';
        }

        $owner = Tools::nl2br(Configuration::get('MANDAT_OWNER'));
        if (!$owner) {
            $owner = '___________';
        }
		
		$address = Tools::nl2br(Configuration::get('MANDAT_ADDRESS'));
        if (!$address) {
            $address = '___________';
        }

        return [
            'checkTotal' => $total,
            'details' => $details,
            'owner' => $owner,
            'address' => $address,
        ];
    }
}
