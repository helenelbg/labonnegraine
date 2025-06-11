<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

class Aw_Bo extends Module
{
    public function __construct()
    {
        $this->name = 'aw_bo';
        $this->version = '1.0.0';
        $this->author = 'Guillaume - Anjou Web';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = [
            'min' => '1.6',
            'max' => _PS_VERSION_
        ];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('aw_bo');
        $this->description = $this->l('Modification du backoffice');

        $this->confirmUninstall = $this->l('Êtes-vous sûr de vouloir désinstaller ce module ?');
    }

    public function install()
    {
        return parent::install();
    }

    public function uninstall()
    {
        return parent::uninstall();
    }

    public function hookDisplaybackOfficeHeader(){
        //$this->context->controller->addJS("/modules/".$this->name."/aw_impressionadresse.js?v=6");
    }

}
