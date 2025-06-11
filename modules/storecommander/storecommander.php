<?php
/**
 * Store Commander
 *
 * @category administration
 * @author Store Commander - support@storecommander.com
 * @version 2.1.10
 * @uses Prestashop modules
 * @since 2009
 * @copyright Copyright &copy; 2009-2023, Store Commander
 * @license commercial
 * All rights reserved! Copying, duplication strictly prohibited
 *
 * *****************************************
 * *           STORE COMMANDER             *
 * *   http://www.StoreCommander.com       *
 * *            V 2.1.10                    *
 * *****************************************
 *
 * Compatibility: PS version: 1.6.1 to 8
 *
 **/

class StoreCommander extends Module
{

    public $currentUrl = '';
    public $baseParams = '';
    public $_err = array();
    private $url_zip_SC = "http://www.storecommander.com/files/StoreCommander.zip";
    public $context;

    public function __construct()
    {
        $this->name = 'storecommander';
        $this->tab = 'administration';
        $this->version = '2.1.10';
        $this->author = 'Store Commander';
        $this->module_key = '7d3e55b97635c528975fbd7e82089a67';
        $this->ps_versions_compliancy = array(
            'min' => '1.6.1.0',
            'max' => _PS_VERSION_,
        );
        parent::__construct();

        $this->currentUrl = $this->getCurrentUrl();
        $token = Tools::getValue("token", "");
        $this->baseParams = "?controller=AdminModules&configure=storecommander&token=" . $token;
        if (version_compare(_PS_VERSION_, '1.5.0.0', '<')) {
            $this->baseParams = "?tab=AdminModules&configure=storecommander&token=" . $token;
        }
        $this->page = basename(__FILE__, '.php');

        $this->displayName = $this->l('Store Commander Installer');
        $this->description = $this->l('Install Store Commander to boost your backoffice.');
        $this->confirmUninstall = $this->l('Warning! This action definitely uninstall Store Commander!');
        $warning = '';
        if (!is_writeable(_PS_ROOT_DIR_ . '/modules/' . $this->name)) {
            $warning .= ' ' . $this->l('The /modules/storecommander folder must be writable.');
        }
        if (!Configuration::get('SC_INSTALLED')) {
            $warning .= ' ' . $this->l('Store Commander is not installed!');
        }
        if ($warning != '') {
            $this->warning = $warning;
        }
    }

    public function install()
    {
        ## cohabitation storecommanderps et storecommander impossible, verification avant installation
        $sc_module_to_check = 'storecommanderps';
        if(version_compare(_PS_VERSION_, '1.7', '>=')){
            $legacyLogger = new PrestaShop\PrestaShop\Adapter\LegacyLogger();
            $moduleDataProvider = new PrestaShop\PrestaShop\Adapter\Module\ModuleDataProvider($legacyLogger, $this->getTranslator());
            $module_installed =$moduleDataProvider->isInstalled($sc_module_to_check);
        } else {
            $module_installed = Module::isInstalled($sc_module_to_check);
        }
        if(is_dir(_PS_ROOT_DIR_.'/modules/'.$sc_module_to_check) && $module_installed){
            $this->_errors[] = $this->l('Store Commander + is already installed on your store. You can launch the application from the "Modules > Store Commander" menu. If you want to install this version of the Store Commander module, please check that you don\'t have a current PrestaShop subscription (check in the module configuration page) and then uninstall the Store Commander + module. Then, restart the installation.');
            return false;
        }
        if (!parent::install()
            || !Configuration::updateGlobalValue('SC_FOLDER_HASH', 'W'.Tools::substr(md5(date("YmdHis") . _COOKIE_KEY_), 0, 10))
            || !$this->createSCFolder(Configuration::getGlobalValue('SC_FOLDER_HASH'))
            || !Configuration::updateValue('SC_INSTALLED', false)
            || !$this->registerHook('displayBackOfficeFooter')
        ) {
            return false;
        }
        if (version_compare(_PS_VERSION_, '1.7.0.0', '<'))
            Tools::redirectAdmin($this->currentUrl . $this->baseParams);
        return true;
    }

    public function uninstall()
    {
        $qaccess = Db::getInstance()->ExecuteS("SELECT GROUP_CONCAT(`id_quick_access`) AS qaccess FROM `" . _DB_PREFIX_ . "quick_access` WHERE `link` LIKE '%storecommander%'");
        if (count($qaccess) && $qaccess[0]['qaccess'] != '') {
            Db::getInstance()->Execute("DELETE FROM `" . _DB_PREFIX_ . "quick_access` WHERE id_quick_access IN (" . psql($qaccess[0]['qaccess']) . ")");
            Db::getInstance()->Execute("DELETE FROM `" . _DB_PREFIX_ . "quick_access_lang` WHERE id_quick_access IN (" . psql($qaccess[0]['qaccess']) . ")");
        }
        $tab = new Tab(Tab::getIdFromClassName('AdminStoreCommander'));
        $tab->delete();
        $this->removeSCFolder(Configuration::getGlobalValue('SC_FOLDER_HASH'));
        Configuration::deleteByName('SC_FOLDER_HASH');
        Configuration::deleteByName('SC_INSTALLED');
        Configuration::deleteByName('SC_SETTINGS');
        Configuration::deleteByName('SC_LICENSE_DATA');
        Configuration::deleteByName('SC_LICENSE_KEY');
        Configuration::deleteByName('SC_VERSIONS');
        Configuration::deleteByName('SC_VERSIONS_LAST');
        Configuration::deleteByName('SC_VERSIONS_LAST_CHECK');

        parent::uninstall();
        return true;
    }

    private function createSCFolder($folder)
    {
        if (!is_dir(dirname(__FILE__) . '/' . $folder)) {
            return mkdir(dirname(__FILE__) . '/' . $folder);
        }
    }

    private function removeSCFolder($folder)
    {
        if (is_dir(dirname(__FILE__) . '/' . $folder)) {
            $this->rrmdir(dirname(__FILE__) . '/' . $folder);
        }
        return true;
    }

    private function rrmdir($dir)
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir . "/" . $object) == "dir") {
                        $this->rrmdir($dir . "/" . $object);
                    } else {
                        unlink($dir . "/" . $object);
                    }
                }
            }
            reset($objects);
            @rmdir($dir);
        }
        return true;
    }

    public function getContent()
    {
        if (class_exists('Context'))
        {
            $this->context = Context::getContext();
        }
        else
        {
            global $smarty, $cookie;
            $this->context = new StdClass();
            $this->context->smarty = $smarty;
            $this->context->cookie = $cookie;
        }
        if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
        {
            $this->context->controller->addJS(__PS_BASE_URI__.'modules/' . $this->name . '/views/js/loader/jquery.loader-min.js');
            $this->context->controller->addCSS(__PS_BASE_URI__.'modules/' . $this->name . '/views/css/admin.css', 'all');
        }

        $this->createTab();

        $this->context->smarty->assign(array(
            'currentUrl' => $this->currentUrl,
            'baseParams' => $this->baseParams
        ));

        $_html = '';
        $_html .= $this->displayStep(Tools::getValue("sc_step"));
        return $_html;
    }

    private function displayStep($step)
    {
        $_html = '';
        switch ((int)$step) {
            case 1 :
                if (Configuration::get('SC_INSTALLED')) {
                    Tools::redirectAdmin($this->currentUrl . $this->baseParams . '&sc_step=3');
                } else {
                    if ($this->isSCFolderReady()) {
                        Tools::redirectAdmin($this->currentUrl . $this->baseParams . '&sc_step=3');
                    } else {
                        if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) {
                            return $_html.$this->display(__FILE__, 'etape_preinstall_1.5.tpl');
                        } else {
                            return $_html.$this->display(__FILE__, 'views/templates/hook/etape_preinstall_1.4.tpl');
                        }
                    }
                }
                break;
            case 2 :
                if (Configuration::get('SC_INSTALLED') || $this->isSCFolderReady()) {
                    Tools::redirectAdmin($this->currentUrl . $this->baseParams . '&sc_step=3');
                } else {
                    if (!$this->downloadExtractSC()) {
                        $this->_err[] = Tools::displayError('Error downloading StoreCommander');
                        $_html = $this->displayErrors($this->_err);
                    } else {
                        $this->createTab();
                        Configuration::updateValue('SC_INSTALLED', true);
                        if (file_exists(dirname(__FILE__).'/license.php'))
                            @copy(dirname(__FILE__).'/license.php',_PS_MODULE_DIR_.$this->name.'/'.Configuration::getGlobalValue('SC_FOLDER_HASH').'/SC/license.php');
                        Tools::redirectAdmin($this->currentUrl . $this->baseParams . '&sc_step=3');
                    }
                }
                break;
            case 3 :
                $update_validation = (int)Tools::getValue("SCupdateValidation");
                $SCupdate = Tools::getValue("SCupdate");
                if(!empty($SCupdate))
                {
                    $_html .= $this->makeSCupdate();
                }

                $licence = Configuration::get('SC_LICENSE_KEY');
                $call_cgu = "terms-";
                if(!empty($licence))
                {
                    $exp = explode("-", $licence);
                    if(count($exp)==3 && strpos($licence,"SC-PS-")===0)
                    {
                        $call_cgu = "terms-sub-";
                    }
                }
                else
                    $call_cgu = "terms-sub-";


                if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) {
                    if($this->context->language->iso_code=="fr")
                        $call_cgu .= "fr.html";
                    else
                        $call_cgu .= "en.html";

                    $this->context->smarty->assign(array(
                        'url_cgu' => $call_cgu,
                        'token' => Tools::getAdminToken('AdminStoreCommander' . (int)(Tab::getIdFromClassName('AdminStoreCommander')) . (int)($this->context->employee->id)),
                        'update_validation' => $update_validation
                    ));
                    return $_html.$this->display(__FILE__, 'etape_postinstall_1.5.tpl');
                } else {
                    global $cookie;
                    $iso_lang = Language::getIsoById((int)$cookie->id_lang);
                    if($iso_lang=="fr")
                        $call_cgu .= "fr.html";
                    else
                        $call_cgu .= "en.html";
                    $this->context->smarty->assign(array(
                        'url_cgu' => $call_cgu,
                        'token' => Tools::getAdminToken('AdminStoreCommander' . (int)(Tab::getIdFromClassName('AdminStoreCommander')) . (int)($cookie->id_employee)),
                        'update_validation' => $update_validation
                    ));
                    return $_html.$this->display(__FILE__, 'views/templates/hook/etape_postinstall_1.4.tpl');
                }
            default :
                if (!$this->isSCFolderReady()) {
                    Tools::redirectAdmin($this->currentUrl . $this->baseParams . '&sc_step=1');
                } else {
                    Tools::redirectAdmin($this->currentUrl . $this->baseParams . '&sc_step=3');
                }
                break;
        }
        return $_html;
    }

    private function makeSCupdate()
    {
        $_html = '';

        $data = $this->sc_file_get_contents($this->url_zip_SC);
        file_put_contents(_PS_MODULE_DIR_ . $this->name . '/' . basename($this->url_zip_SC), $data);
        $var = $this->extractArchive(_PS_MODULE_DIR_ . $this->name . '/' . basename($this->url_zip_SC), true);

        if (!$var) {
            $this->_err[] = Tools::displayError('Error downloading StoreCommander');
            $_html = $this->displayErrors($this->_err);
        }
        else
            Tools::redirectAdmin($this->currentUrl . $this->baseParams . '&sc_step=3&SCupdate=0&SCupdateValidation=1');
        return $_html;
    }

    private function createTab()
    {
        $languages = Language::getLanguages(false);
        $tabAdded = true;
        if (!Tab::getIdFromClassName('AdminStoreCommander')) {
            if(version_compare(_PS_VERSION_, '1.7.1.0', '>=')) {
                $parentClassName = 'AdminParentModulesSf';
            } elseif (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
            {
                $parentClassName = 'AdminParentModules';
            } else {
                $parentClassName = 'AdminModules';
                @copy(_PS_MODULE_DIR_ . $this->name . '/logo.gif', _PS_IMG_DIR_ . 't/AdminStoreCommander.gif');
            }

            $tab = new Tab();
            $tab->class_name = 'AdminStoreCommander';
            $tab->id_parent = (int)Tab::getIdFromClassName($parentClassName);
            $tab->module = $this->name;
            foreach ($languages AS $language) {
                $tab->name[$language["id_lang"]] = 'Store Commander';
            }
            $tabAdded = $tab->add();
        }

        $sql = 'SELECT COUNT(id_quick_access) AS id FROM `' . _DB_PREFIX_ . 'quick_access` q WHERE q.`link` LIKE \'%AdminStoreCommander%\'';
        $result = Db::getInstance()->getValue($sql);
        if (!$tabAdded && $result) {
            $quickAccess = new QuickAccess();
            $tmp = array();

            foreach ($languages AS $lang) {
                $tmp[$lang['id_lang']] = "Store Commander";
            }
            $quickAccess->name = $tmp;
            if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) {
                $quickAccess->link = "index.php?controller=AdminStoreCommander";
            } else {
                $quickAccess->link = "index.php?tab=AdminStoreCommander";
            }
            $quickAccess->new_window = true;
            $quickAccess->add();
        }
    }

    public function isSCFolderReady()
    {
        if (file_exists(dirname(__FILE__) . '/' . Configuration::getGlobalValue('SC_FOLDER_HASH') . '/SC/index.php')) {
            return true;
        }
        return false;
    }

    public function sc_file_get_contents($param, $querystring = '')
    {
        $result = '';
        if (function_exists('file_get_contents') && version_compare(_PS_VERSION_, '1.4.0.0', '>=')) {
            @$result = Tools::file_get_contents($param,false,null,30);
        }
        if ($result == '' && function_exists('curl_init')) {
            $curl = curl_init();
            $header = array();
            $header[] = "Accept: text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5";
            $header[] = "Cache-Control: max-age=0";
            $header[] = "Connection: keep-alive";
            $header[] = "Keep-Alive: 300";
            $header[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
            $header[] = "Accept-Language: en-us,en;q=0.5";
            $header[] = "Pragma: ";
            curl_setopt($curl, CURLOPT_URL, $param);
            curl_setopt($curl, CURLOPT_USERAGENT, 'Store Commander (http://www.storecommander.com)');
            curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
            curl_setopt($curl, CURLOPT_ENCODING, 'gzip,deflate');
            curl_setopt($curl, CURLOPT_AUTOREFERER, true);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $querystring);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 20);
            curl_setopt($curl, CURLOPT_TIMEOUT, 20);
            $result = curl_exec($curl);
            $info = curl_getinfo($curl);
            curl_close($curl);
            if ((int)$info['http_code'] != 200) {
                return '';
            }
        }
        return $result;
    }

    private function downloadExtractSC()
    {
        $data = $this->sc_file_get_contents($this->url_zip_SC);
        file_put_contents(_PS_MODULE_DIR_ . $this->name . '/' . basename($this->url_zip_SC), $data);
        return $this->extractArchive(_PS_MODULE_DIR_ . $this->name . '/' . basename($this->url_zip_SC));
    }


    private function extractArchive($file, $noPCLZIP=false)
    {
        $success = true;
		if(version_compare(_PS_VERSION_, '1.4.0.0', '>='))
		{
            if (file_exists(_PS_TOOL_DIR_ . 'pclzip/pclzip.lib.php') && empty($noPCLZIP)){
                require_once(_PS_TOOL_DIR_ . 'pclzip/pclzip.lib.php');
                $zip = new PclZip($file);
                $list = $zip->extract(PCLZIP_OPT_PATH, _PS_MODULE_DIR_ . $this->name . '/' . Configuration::getGlobalValue('SC_FOLDER_HASH'));
                foreach ($list as $extractedFile) {
                    if ($extractedFile['status'] != 'ok') {
                        $success = false;
                    }
                }
            } else {
                if (class_exists('ZipArchive', false))
                {
                    $zip = new ZipArchive();
                    if ($zip->open($file) === true AND $zip->extractTo(_PS_MODULE_DIR_ . $this->name . '/' . Configuration::getGlobalValue('SC_FOLDER_HASH')) AND $zip->close())
                        $success = true;
                    else
                        $success = false;
                }
                else
                    $success = false;
            }
		}
		else
		{
			if (Tools::substr($file, -4) == '.zip')
			{
				if (class_exists('ZipArchive', false))
				{
					$zip = new ZipArchive();
					if ($zip->open($file) === true AND $zip->extractTo(_PS_MODULE_DIR_ . $this->name . '/' . Configuration::getGlobalValue('SC_FOLDER_HASH')) AND $zip->close())
						$success = true;
					else
						$success = false;
				}
				else
					$success = false;
			}
			else
			{
				$archive = new Archive_Tar($file);
				if ($archive->extract(_PS_MODULE_DIR_ . $this->name . '/' . Configuration::getGlobalValue('SC_FOLDER_HASH')))
					$success = true;
				else
					$success = false;
			}
		}
        @unlink($file);
        return $success;
    }

    public function displayErrors($errors)
    {
        if (is_array($errors) && count($errors)) {

            $_html = '';
            $this->context->smarty->assign(array(
                'errors' => $errors
            ));
            $_html = $this->display(__FILE__, 'views/templates/hook/errors.tpl');
            return $_html;
        }
    }

    public function  getCurrentUrl()
    {
        if(version_compare(_PS_VERSION_, '1.5.0.2', '>=')) {
            $pageURL = Tools::getShopProtocol();
        } else {
            $pageURL = 'http';
            if (!empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {
                $pageURL .= "s";
            }
            $pageURL .= "://";
        }
        if ($_SERVER["SERVER_PORT"] != "80") {
            $pageURL .= $_SERVER["HTTP_HOST"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
        } else {
            $pageURL .= $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
        }
        $exp = explode("?", $pageURL);
        $pageURL = $exp[0];
        return $pageURL;
    }

    public function hookBackOfficeFooter()
    {
        $js_action = null;
        $js_id = null;
        if(version_compare(_PS_VERSION_, '1.6.0.5', '>=')) {
            $cookie = $this->context->cookie;
        } else {
            $cookie = new Cookie();
        }
        $show_msg = 1;
        if(version_compare(_PS_VERSION_, '1.7.7.0', '>=')) {
            $controller = '';
            switch(get_class($this->context->controller)) {
                case 'AdminImagesController':
                    $controller = 'image';
                    $js_action = '$(".alert.alert-info").last().after(' . $this->getMessage($controller, 177, array('class'=>'alert alert-danger')) . ')';
                    break;
            }
            switch($this->context->controller->php_self) {
                case 'AdminOrders':
                    $js_id = '#order_grid_panel';
                    $controller = 'order';
                    break;
                case 'AdminCustomers':
                    $js_id = '#customer_grid_panel';
                    $controller = 'customer';
                    break;
                case 'AdminProducts':
                    $js_id = '#main-div > .content-div > .row > div > .products-catalog';
                    $controller = 'catalog';
                    break;
                case 'AdminCategories':
                    $js_id = '#category_grid_panel';
                    $controller = 'catalog';
                    break;
            }
            if(empty($js_action) && !empty($js_id) && !empty($controller)) {
                $js_action = '$("' . $js_id . '").before(' . $this->getMessage($controller, 177) . ');';
            }
        } else if(version_compare(_PS_VERSION_, '1.7.6.5', '>=')) {
            $controller = '';
            switch(get_class($this->context->controller)) {
                case 'AdminOrdersController':
                    $js_id = '#order-empty-filters-alert';
                    $controller = 'order';
                    break;
                case 'AdminImagesController':
                    $controller = 'image';
                    $js_action = '$(".alert.alert-info").last().after(' . $this->getMessage($controller, 176, array('class'=>'alert alert-danger')) . ')';
                    break;
            }
            switch($this->context->controller->php_self){
                case 'AdminProducts':
                    $js_id = '#main-div > .content-div > .row > div > .products-catalog';
                    $controller = 'catalog';
                    break;
                case 'AdminCategories':
                    $js_id = '#category_grid_panel';
                    $controller = 'catalog';
                    break;
                case 'AdminCustomers':
                    $js_id = '#customer_grid_panel';
                    $controller = 'customer';
                    break;
            }
            if(empty($js_action) && !empty($js_id) && !empty($controller)) {
                $js_action = '$("' . $js_id . '").before(' . $this->getMessage($controller, 176) . ');';
            }
        } else if(version_compare(_PS_VERSION_, '1.7.6.0', '>=')) {
            $controller = '';
            switch(get_class($this->context->controller)) {
                case 'AdminOrdersController':
                    $js_id = '#order-empty-filters-alert';
                    $controller = 'order';
                    break;
                case 'AdminCustomersController':
                    $js_id = '#customer-empty-filters-alert';
                    $controller = 'customer';
                    break;
                case 'AdminImagesController':
                    $controller = 'image';
                    $js_action = '$(".alert.alert-info").last().after(' . $this->getMessage($controller, 176, array('class'=>'alert alert-danger')) . ')';
                    break;
            }
            switch($this->context->controller->php_self){
                case 'AdminProducts':
                    $js_id = '#main-div > .content-div > .row > div > .products-catalog';
                    $controller = 'catalog';
                    break;
                case 'AdminCategories':
                    $js_id = '#category_grid_panel';
                    $controller = 'catalog';
                    break;
            }
            if(empty($js_action) && !empty($js_id) && !empty($controller)) {
                $js_action = '$("' . $js_id . '").before(' . $this->getMessage($controller, 176) . ');';
            }
        } else if(version_compare(_PS_VERSION_, '1.7.5.0', '>=')) {
            $controller = '';
            switch(get_class($this->context->controller)) {
                case 'AdminOrdersController':
                    $js_id = '#order-empty-filters-alert';
                    $controller = 'order';
                    break;
                case 'AdminCustomersController':
                    $js_id = '#customer-empty-filters-alert';
                    $controller = 'customer';
                    break;
                case 'AdminCategoriesController':
                    $js_id = '#content > .row > div > .leadin';
                    $controller = 'catalog';
                    break;
                case 'AdminImagesController':
                    $controller = 'image';
                    $js_action = '$(".alert.alert-info").last().after(' . $this->getMessage($controller, 175, array('class'=>'alert alert-danger')) . ')';
                    break;
            }
            switch($this->context->controller->php_self){
                case 'AdminProducts':
                    $js_id = '#main-div > .content-div > .row > div > .products-catalog';
                    $controller = 'catalog';
                    break;
            }
            if(empty($js_action) && !empty($js_id) && !empty($controller)) {
                $js_action = '$("' . $js_id . '").before(' . $this->getMessage($controller, 175) . ');';
            }
        } else if(version_compare(_PS_VERSION_, '1.6.0.5', '>=')) {
            $controller = '';
            switch(get_class($this->context->controller)) {
                case 'AdminOrdersController':
                    $js_id = '#order-empty-filters-alert';
                    $controller = 'order';
                    break;
                case 'AdminCustomersController':
                    $js_id = '#customer-empty-filters-alert';
                    $controller = 'customer';
                    break;
                case 'AdminProductsController':
                case 'AdminCategoriesController':
                    $js_id = '#content > .row > div > .leadin';
                    $controller = 'catalog';
                    break;
                case 'AdminImagesController':
                    $controller = 'image';
                    $js_action = '$(".alert.alert-info").last().after(' . $this->getMessage($controller, 16, array('class'=>'alert alert-danger')) . ')';
                    break;
            }
            if(empty($js_action) && !empty($js_id) && !empty($controller)) {
                $js_action = '$("' . $js_id . '").before(' . $this->getMessage($controller, 16) . ');';
            }
        } else if(version_compare(_PS_VERSION_, '1.5.0.17', '>=')) {
            $controller = '';
            switch(get_class($this->context->controller)) {
                case 'AdminOrdersController':
                    $js_id = '#order_toolbar';
                    $controller = 'order';
                    break;
                case 'AdminCustomersController':
                    $js_id = '#customer_toolbar';
                    $controller = 'customer';
                    break;
                case 'AdminProductsController':
                    $js_id = '#product_toolbar';
                    $controller = 'catalog';
                    break;
                case 'AdminCategoriesController':
                    $js_id = '#category_toolbar';
                    $controller = 'catalog';
                    break;
                case 'AdminImagesController':
                    $controller = 'image';
                    $js_action = '$("input[name=submitRegenerateimage_type]").parent().before(' . $this->getMessage($controller, 15, array('class'=>'warn')) . ')';
                    break;
            }
            if(empty($js_action) && !empty($js_id) && !empty($controller)) {
                $js_action = '$("' . $js_id . '").after(' . $this->getMessage($controller, 15) . ');';
            }
        } else {
            $js_id = '#content > .path_bar';
            $controller = '';
            switch(Tools::getValue('tab')) {
                case 'AdminOrders':
                    $controller = 'order';
                    break;
                case 'AdminCustomers':
                    $controller = 'customer';
                    break;
                case 'AdminCatalog':
                    $controller = 'catalog';
                    break;
                case 'AdminImages':
                    $controller = 'image';
                    $js_action = '$("input[name=submitRegenerateimage_type]").parent().before(' . $this->getMessage($controller, 14, array('class'=>'warn')) . ')';
                    break;
            }
            if(empty($js_action) && !empty($js_id) && !empty($controller)) {
                $js_action = '$("' . $js_id . '").after(' . $this->getMessage($controller, 14) . ');';
            }
        }
        if(!empty($controller)) {
            $cookie_key = 'sc_no_msg_'.$controller;
            $cookie_no_msg = $cookie->__get($cookie_key);
            if(empty($cookie_no_msg)) {
                $no_msg = Tools::getValue($cookie_key,0);
                if($no_msg) {
                    $cookie->__set($cookie_key, 1);
                    $show_msg = 0;
                }
            } else {
                $show_msg = 0;
            }
        }
        if(!empty($js_action) && !empty($show_msg)) {
            $html = '<script type="text/javascript">' . "\n";
            $html .= '  $(document).ready(function(){' . "\n";
            $html .= $js_action . "\n";
            $html .= "\n" . '  });' . "\n";
            $html .= '</script>' . "\n";
            echo $html;
        }
    }

    private function getMessage($controller,$ps_version,$params = null){
        $class = 'ps'.$ps_version;
        $html = $sc_message_style = $bo_url = $hide_msg_url ='';
        switch($ps_version) {
            case 15:
                $class = 'hint clear';
                $sc_message_style = 'style="display:block!important;"';
                $iso_lang = $this->context->language->iso_code;
                $link = new Link();
                $bo_url = $link->getAdminLink('AdminStoreCommander');
                $cnt = Tools::getValue('controller');
                $hide_msg_url= $link->getAdminLink($cnt);
                break;
            case 16:
            case 175:
            case 176:
            case 177:
                $class = 'alert alert-info';
                $iso_lang = $this->context->language->iso_code;
                $link = new Link();
                $bo_url = $link->getAdminLink('AdminStoreCommander');
                $cnt = Tools::getValue('controller');
                $hide_msg_url= $link->getAdminLink($cnt);
                break;
            default:
                global $cookie;
                $class = 'hint clear';
                $sc_message_style = 'style="display:block!important;"';
                $iso_lang = Language::getIsoById((int)$cookie->id_lang);
                $bo_url = $_SERVER['SCRIPT_NAME'].'?tab=AdminStoreCommander&token='.Tools::getAdminTokenLite('AdminStoreCommander');
                $hide_msg_url = $_SERVER['REQUEST_URI'];
                break;
        }
        if(!empty($params) && is_array($params)) {
            if(array_key_exists('class',$params) && !empty($params['class'])){
                $class = (string)$params['class'];
            }
        }
        /*$text = array(
            'order' => array(
                'fr' => '<p><b>Store Commander</b> est installé sur votre boutique. Saviez-vous que vous pouvez :</p>
                        <ul style="list-style-type: disc;padding-left:40px">
                        <li>Filtrer plus finement vos dernières commandes pour changer leurs statuts en masse</li>
                        <li>Créer des commandes pour vos clients à partir d\'un simple appel téléphonique</li>
                        <li>Rechercher, trouver et imprimer les factures/avoirs/bons de livraison plus facilement</li>
                        <li>Analyser précisément l\'origine de vos commandes pour prendre des décisions sur vos différents canaux d\'acquisition</li>
                        <li>Exporter les données pour faciliter la gestion de votre comptabilité (ventillation TVA, Pays, Mini-guichet...)</li>
                        </ul><br/>
                        <p>Démarrez Store Commander pour gérer vos commandes plus efficacement, en <b><a href="'.$bo_url.'">cliquant ici</a></b> ou depuis le menu Module > Store Commander</p>
                        <p style="text-align:right"><a href="'.$hide_msg_url.'&sc_no_msg_order=1">Ne plus afficher ce message</a></p>',
                'en' => '<p><b>Store Commander</b> is installed on your shop. Did you know that you can:</p>
                        <ul style="list-style-type: disc;padding-left:40px">
                        <li>Filter more finely your latest orders to change their status en masse</li>
                        <li>Create orders for your customers from a simple phone call</li>
                        <li>Search, find and print invoices/credit notes/delivery notes more easily</li>
                        <li>Precisely analyze the origin of your orders to make decisions on your different acquisition channels</li>
                        <li>Export data to facilitate the management of your accounting (VAT breakdown, Country, Mini-window...)</li>
                        </ul><br/>
                        <p>Start Store Commander to manage your orders more efficiently, by <b><a href="'.$bo_url.'">clicking here</a></b> or from the Module > Store Commander menu</p>
                        <p style="text-align:right"><a href="'.$hide_msg_url.'&sc_no_msg_order=1">Do not display this message again</a></p>',
            ),
            'customer' => array(
                'fr' => '<p><b>Store Commander</b> est installé sur votre boutique. Saviez-vous que vous pouvez :</p>
                        <ul style="list-style-type: disc;padding-left:40px">
                        <li>Trouver rapidement toutes les informations se rapportant à un client</li>
                        <li>Gérer tout votre service client depuis une seule page pour plus d\'efficacité</li>
                        <li>Exporter et importer vos clients avec plus de souplesse</li>
                        <li>Segmenter vos clients en fonction de leur comportement pour des actions marketing ciblées</li>
                        <li>Se connecter en tant qu\'un client sur son compte dans la boutique pour voir ce qu\'il voit</li>
                        </ul><br/>
                        <p>Démarrez Store Commander pour gérer vos clients plus efficacement, en <b><a href="'.$bo_url.'">cliquant ici</a></b> ou depuis le menu Module > Store Commander</p>
                        <p style="text-align:right"><a href="'.$hide_msg_url.'&sc_no_msg_customer=1">Ne plus afficher ce message</a></p>',
                'en' => '<p><b>Store Commander</b> is installed on your shop. Did you know that you can:</p>
                        <ul style="list-style-type: disc;padding-left:40px">
                        <li>Quickly find all information related to a customer</li>
                        <li>Manage all your customer service from a single page for greater efficiency</li>
                        <li>Export and import your customers more flexibly</li>
                        <li>Segment your customers according to their behaviour for targeted marketing actions</li>
                        <li>Log in as a customer on his account in the shop to see what he sees</li>
                        </ul><br/>
                        <p>Start Store Commander to manage your customers more efficiently, by <b><a href="'.$bo_url.'">clicking here</a></b> or from the Module > Store Commander menu</p>
                        <p style="text-align:right"><a href="'.$hide_msg_url.'&sc_no_msg_customer=1">Do not display this message again</a></p>',
            ),
            'catalog' => array(
                'fr' => '<p><b>Store Commander</b> est installé sur votre boutique. Saviez-vous que vous pouvez :</p>
                        <ul style="list-style-type: disc;padding-left:40px">
                        <li>Trouver et modifier en masse toutes les informations de vos produits</li>
                        <li>Exporter et ré-importer tout votre catalogue ou une sélection de produits</li>
                        <li>Préparer les périodes de soldes et promotions vitesse turbo, en contrôlant vos marges</li>
                        <li>Améliorer tous les critères SEO de chaque fiche produit : meta tags, taille des meta tags...</li>
                        <li>Identifier et corriger des centaines de problèmes avec le menu Outils > FixMyPrestaShop</li>
                        </ul><br/>
                        <p>Démarrez Store Commander pour gérer votre catalogue plus efficacement, en <b><a href="'.$bo_url.'">cliquant ici</a></b> ou depuis le menu Module > Store Commander</p>
                        <p style="text-align:right"><a href="'.$hide_msg_url.'&sc_no_msg_catalog=1">Ne plus afficher ce message</a></p>',
                'en' => '<p><b>Store Commander</b> is installed on your shop. Did you know that you can:</p>
                        <ul style="list-style-type: disc;padding-left:40px">
                        <li>Find and modify in mass all the information of your products</li>
                        <li>Export and re-import your entire catalog or a selection of products</li>
                        <li>Prepare sales and turbo speed promotions periods, controlling your margins</li>
                        <li>Improve all SEO criteria of each product sheet: meta tags, size of meta tags...</li>
                        <li>Identify and fix hundreds of problems with the Tools menu > FixMyPrestaShop</li>
                        </ul><br/>
                        <p>Start Store Commander to manage your catalog more efficiently, by <b><a href="'.$bo_url.'">clicking here</a></b> or from the Module > Store Commander menu</p>
                        <p style="text-align:right"><a href="'.$hide_msg_url.'&sc_no_msg_catalog=1">Do not display this message again</a></p>',
            ),
            'image' => array(
                'fr' => '<p><b>Store Commander</b> est installé sur votre boutique.
                        <br/>Si la compression d\'image proposée par Store Commander est activée et que  vous regénérez vos miniatures, vous perdrez alors toute optimisation réalisée sur vos images produit.
                        <br/>Store Commander compressera de nouveau vos fichiers en utilisant les crédits restants.</p>
                        <p style="text-align:right"><a href="'.$hide_msg_url.'&sc_no_msg_image=1">Ne plus afficher ce message</a></p>',
                'en' => '<p><b>Store Commander</b> is installed on your shop.</p>'
            )
        );*/
        if(array_key_exists($controller,$text) && array_key_exists($iso_lang,$text[$controller])) {
            $html = '<div id="sc_message" class="' . $class . '"' . $sc_message_style . '>' . $text[$controller][$iso_lang] . '</a></div>';
            $html = json_encode($html);
        }
        return $html;
    }
}
