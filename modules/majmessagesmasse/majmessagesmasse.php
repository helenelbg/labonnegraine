<?php


class MajmessagesMasse extends Module
{

    private $_html = '';
    private $_query = '';
    private $_option = 0;
    private $_id_product = 0;

    function __construct()
    {
        $this->name = 'majmessagesmasse';
        $this->tab = 'Product';
        $this->version = '2.0.0';
        $this->author = 'Anjouweb';
        $this->ps_versions_compliancy = [
            'min' => '1.7',
            'max' => _PS_VERSION_
        ];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = 'Mise &agrave; jour de messages en masse';
        $this->description = '';

        $this->confirmUninstall = $this->l('Êtes-vous sûr de vouloir désinstaller ce module ?');
    }

    public function install()
    {
        return (parent::install() AND $this->installModuleTab('MajmsgMasse', array(intval(Configuration::get('PS_LANG_DEFAULT')) => 'Mise a jour messages de masse'), Tab::getIdFromClassName('AdminCatalog')));
    }
    
    private function installModuleTab($tabClass, $tabName, $idTabParent) {
    	//@copy(_PS_MODULE_DIR_.$this->name."/".$tabClass.".gif", _PS_IMG_DIR_."t/".$tabClass.".gif");
    	$tab = new Tab();
    	$tab->name = $tabName;
    	$tab->class_name = $tabClass;
    	$tab->module = $this->name;
    	$tab->id_parent = $idTabParent;
    	return $tab->save();
    }
    
    function uninstall()	{
   	return parent::uninstall() && $this->uninstallModuleTab('MajmsgMasse');
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


    ?>
