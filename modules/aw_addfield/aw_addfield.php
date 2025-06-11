<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

class Aw_Addfield extends Module {
    public function __construct() {
        $this->name = 'aw_addfield';
        $this->version = '1.0.0';
        $this->author = 'Anjouweb';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = [
            'min' => '1.7',
            'max' => _PS_VERSION_
        ];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Aw add field');
        $this->description = $this->l('Module pour ajouter le champ not_available_message');

        $this->confirmUninstall = $this->l('Êtes-vous sûr de vouloir désinstaller ce module ?');
    }

    public function install() {
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        if (!parent::install() || !$this->registerHook('displayAdminProductsQuantitiesStepBottom')) {
            return false;
        }

        return true;
    }

    public function uninstall()
    {
        if (!parent::uninstall()) {
            return false;
        }

        return true;
    }

    public function hookDisplayAdminProductsQuantitiesStepBottom($params){
        $product = new Product($params["id_product"]);
        $this->context->smarty->assign(['not_available_message' => $product->not_available_message[1]]);
 
        return $this->display(__FILE__, 'views/templates/hook/admin/displayAdminProductsQuantitiesStepBottom.tpl');
    }
}