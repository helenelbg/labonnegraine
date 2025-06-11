<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

class AutoCustomerSelect extends Module
{
    public function __construct()
    {
        $this->name = 'autocustomerselect';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'Votre Nom';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = [
            'min' => '8.0.0',
            'max' => _PS_VERSION_
        ];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Auto Customer Select');
        $this->description = $this->l('Présélectionne automatiquement un client lors de la création d\'une commande.');
    }

    public function install()
    {
        return parent::install() &&
            $this->registerHook('displayBackOfficeHeader');
    }

    public function uninstall()
    {
        return parent::uninstall();
    }

    public function hookDisplayBackOfficeHeader()
    {
        $currentPage = $_SERVER['REQUEST_URI'];
        if (strpos($currentPage, '/sell/orders/new') !== false && Tools::getValue('customerId')) {
            Media::addJsDef([
                'autoCustomerSelectId' => (int)Tools::getValue('customerId')
            ]);
            $this->context->controller->addJS($this->_path . 'views/js/order-create-override.js');
        }
    }
}