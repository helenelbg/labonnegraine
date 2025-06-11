<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

class SavPayment extends PaymentModule
{
    public function __construct()
    {
        $this->name = 'savpayment';
        $this->tab = 'payment';
        $this->version = '1.0.0';
        $this->author = 'La Bonne Graine';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = [
            'min' => '8.0.0',
            'max' => _PS_VERSION_
        ];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Paiement SAV');
        $this->description = $this->l('Module de paiement SAV');
        $this->confirmUninstall = $this->l('Êtes-vous sûr de vouloir désinstaller ce module ?');
    }

    public function install()
    {
        return parent::install() 
            && $this->registerHook('paymentOptions')
            && $this->registerHook('paymentReturn')
            && $this->registerHook('actionValidateOrderAfter');
    }

    public function uninstall()
    {
        return parent::uninstall();
    }

    public function hookActionValidateOrderAfter($params)
    {
        $order = $params['order'];
        
        // Vérifier que la commande est créée en back-office
        if (isset($this->context->employee) && $this->context->employee->id) 
        {
            $sql1 = 'UPDATE ' . _DB_PREFIX_ . 'orders 
            SET current_state = 2 WHERE id_order = '.$order->id.';';
            Db::getInstance()->execute($sql1);

            $sql2 = 'INSERT INTO ' . _DB_PREFIX_ . 'order_history 
            SET id_order = '.$order->id.', id_order_state = 2, date_add = NOW();';
            Db::getInstance()->execute($sql2);
        }
    }

    /*public function hookPaymentOptions($params)
    {
        if (!$this->active) {
            return [];
        }

        $payment_options = [
            $this->getSavPaymentOption()
        ];

        return $payment_options;
    }*/

    public function hookPaymentOptions($params)
    {
        // Vérifier si nous sommes dans le contexte du back-office
        if (!isset($this->context->employee) || !$this->context->employee) {
            return [];
        }

        // Vérifier si la requête vient bien du back-office
        if (!defined('_PS_ADMIN_DIR_') && !isset($_SERVER['HTTP_REFERER']) || !preg_match('/admin/i', $_SERVER['HTTP_REFERER'])) {
            return [];
        }

        if (!$this->active) {
            return [];
        }

        $payment_options = [
            $this->getSavPaymentOption()
        ];

        return $payment_options;
    }

    public function getSavPaymentOption()
    {
        $newOption = new \PrestaShop\PrestaShop\Core\Payment\PaymentOption();
        $newOption->setModuleName($this->name)
            ->setCallToActionText($this->l('SAV'))
            ->setAction($this->context->link->getModuleLink($this->name, 'validation', [], true));

        return $newOption;
    }

    public function hookPaymentReturn($params)
    {
        // Logique de retour après paiement
        return $this->fetch('module:savpayment/views/templates/hook/payment_return.tpl');
    }
}