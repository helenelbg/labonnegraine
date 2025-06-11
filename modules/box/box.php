<?php
    if (!defined('_PS_VERSION_'))
        exit;

class box extends Module {

    private $page_name = '';

    public function __construct() {
        $this->name = 'box'; 
        $this->tab = 'administration'; 
        $this->version = 0.1;
        $this->author = 'Benoit';
        $this->secure_key=Tools::encrypt($this->name);
        $this->need_instance = 0;
        $this->controllers=array('AdminBox');
        parent::__construct();
        $this->displayName = $this->l('Box');
        $this->description = $this->l('Module Box');
        
    }
    public function install()
    { 

        return parent::install() && $this->installModuleTab();
    }
    public  function uninstall() {
        return $this->uninstallModuleTab() && parent::uninstall();
    }
    public function installModuleTab(){
        $langs=Language::getLanguages(true);
        $tab=new tab;
        foreach($langs as $lang)
        {
            $tab->name[$lang['id_lang']]='Box';
        }
        $tab->module = $this->name;
        $tab->id_parent= Tab::getIdFromClassName('AdminParentOrders'); // ParentOrders;
        $tab->class_name="AdminBox";
        return $tab->save();
        
    }
    public function uninstallModuleTab() {
        $id_tab=Tab::getIdFromClassName('AdminBox');
        if($id_tab){
              $tab = new Tab($id_tab);
              return($tab->delete());
        }
        return true;
    } 
}
?>