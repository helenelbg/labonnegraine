<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

class MajStockMasse extends Module
{


    private $_html = '';
    private $_query = '';
    private $_option = 0;
    private $_id_product = 0;

    function __construct()
    {
        $this->name = 'majstockmasse';
        $this->tab = 'Product';
        $this->version = '2.0.0';
        $this->author = 'Anjouweb';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = [
            'min' => '1.7',
            'max' => _PS_VERSION_
        ];
        $this->bootstrap = true; 

        parent::__construct();

        $this->displayName = 'Mise à jour de stock en masse';
        $this->description = "";

        $this->confirmUninstall = $this->l('Êtes-vous sûr de vouloir désinstaller ce module ?');
    }

    public function install()
    {
        return (parent::install() AND $this->installModuleTab('MajQtyMasse', array(intval(Configuration::get('PS_LANG_DEFAULT')) => 'Mise a jour quantites de masse'), Tab::getIdFromClassName('AdminCatalog')));
    }
    
    private function installModuleTab($tabClass, $tabName, $idTabParent) {
    	$tab = new Tab();
    	$tab->name = $tabName;
    	$tab->class_name = $tabClass;
    	$tab->module = $this->name;
    	$tab->id_parent = $idTabParent;
    	return $tab->save();
    }
    
    function uninstall()	{
   	    return parent::uninstall() && $this->uninstallModuleTab('MajQtyMasse');
    }

  
    private function uninstallModuleTab($tabClass) {
    	$idTab = Tab::getIdFromClassName($tabClass);
     	if($idTab != 0) {
    		$tab = new Tab($idTab);
    		$tab->delete();
     		return true;
    	}
    	return false;
    }
}