<?php
/**
* 2022 - Keyrnel
*
* NOTICE OF LICENSE
*
* The source code of this module is under a commercial license.
* Each license is unique and can be installed and used on only one shop.
* Any reproduction or representation total or partial of the module, one or more of its components,
* by any means whatsoever, without express permission from us is prohibited.
* If you have not received this module from us, thank you for contacting us.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future.
*
* @author    Keyrnel
* @copyright 2022 - Keyrnel
* @license   commercial
* International Registered Trademark & Property of Keyrnel
*/
if (!defined('_PS_VERSION_')) {
    exit;
}

if (!defined('_MYSQL_ENGINE_')) {
    define('_MYSQL_ENGINE_', 'MyISAM');
}

require_once _PS_MODULE_DIR_ . 'flashsales/models/FlashSale.php';
if (version_compare(_PS_VERSION_, '1.7', '>=')) {
    require_once _PS_MODULE_DIR_ . 'flashsales/controllers/front/FlashSaleListingFrontController.php';
    require_once _PS_MODULE_DIR_ . 'flashsales/controllers/front/FlashSaleProductSearchProvider.php';
}

class flashsales extends Module
{
    private $_html = '';

    private $_post_errors = [];

    private $_layouts = ['home_page', 'flashsale_page', 'product_page', 'product_list', 'column'];

    private $_configs = [
        ['name' => 'FLASHSALE_DEL_SPECIFICPRICE', 'value' => 1, 'validate' => 'isUnsignedInt'],
        ['name' => 'FLASHSALE_DISPLAY_TOPMENU', 'value' => 1, 'validate' => 'isBool'],
        ['name' => 'FLASHSALE_PRODUCT_LIST', 'value' => 1, 'validate' => 'isBool'],
        ['name' => 'FLASHSALE_PRODUCTS_NB', 'layouts' => ['home_page', 'column', 'flashsale_page'], 'value' => 4, 'validate' => 'isUnsignedInt'],
        ['name' => 'FLASHSALE_TITLE', 'layouts' => ['home_page', 'flashsale_page', 'column'], 'lang' => true, 'value' => ['default' => 'Flash sales', 'fr' => 'Ventes flash'], 'validate' => 'isCleanHTML'],
        ['name' => 'FLASHSALE_COUNTDOWN_STRING', 'lang' => true, 'layouts' => ['home_page', 'product_page', 'product_list', 'column'], 'value' => ['default' => 'Flash sales', 'fr' => 'Ventes flash'], 'validate' => 'isCleanHTML'],
        ['name' => 'FLASHSALE_DESCRIPTION', 'layouts' => ['home_page', 'flashsale_page'], 'value' => 1, 'validate' => 'isBool'],
        ['name' => 'FLASHSALE_BANNER', 'layouts' => ['home_page', 'flashsale_page'], 'value' => 1, 'validate' => 'isBool'],
        ['name' => 'FLASHSALE_CAROUSEL', 'layouts' => ['home_page', 'column', 'flashsale_page'], 'value' => 1, 'validate' => 'isBool'],
    ];

    private $_css_fields = [
        ['property' => 'width', 'field' => 'box_width', 'layouts' => ['product_page', 'product_list']],
        ['property' => 'border-width', 'field' => 'box_border_width', 'layouts' => ['product_page', 'product_list']],
        ['property' => 'border-style', 'field' => 'box_border_style', 'layouts' => ['product_page', 'product_list']],
        ['property' => 'border-color', 'field' => 'box_border_color', 'layouts' => ['product_page', 'product_list']],
        ['property' => 'background-color', 'field' => 'box_background_color', 'layouts' => ['product_page', 'product_list']],
        ['property' => 'padding-top', 'field' => 'box_padding_top', 'layouts' => ['product_page', 'product_list']],
        ['property' => 'padding-bottom', 'field' => 'box_padding_bottom', 'layouts' => ['product_page', 'product_list']],
        ['property' => 'padding-left', 'field' => 'box_padding_left', 'layouts' => ['product_page', 'product_list']],
        ['property' => 'padding-right', 'field' => 'box_padding_right', 'layouts' => ['product_page', 'product_list']],
        ['property' => 'margin-top', 'field' => 'box_margin_top', 'layouts' => ['product_page', 'product_list']],
        ['property' => 'margin-bottom', 'field' => 'box_margin_bottom', 'layouts' => ['product_page', 'product_list']],
        ['class' => '.content i.icon-clock-o', 'property' => 'color', 'field' => 'icon_color', 'layouts' => ['product_page', 'product_list']],
        ['class' => '.content i.icon-clock-o', 'property' => 'font-size', 'field' => 'icon_font_size', 'layouts' => ['product_page', 'product_list']],
        ['class' => '.content i.icon-clock-o', 'property' => 'display', 'field' => 'icon_display', 'layouts' => ['product_page', 'product_list']],
        ['class' => '.content span.title', 'property' => 'color', 'field' => 'title_color', 'layouts' => ['product_page', 'product_list']],
        ['class' => '.content span.title', 'property' => 'font-size', 'field' => 'title_font_size', 'layouts' => ['product_page', 'product_list']],
        ['class' => '.content span.countdown', 'property' => 'color', 'field' => 'countdown_color', 'layouts' => ['product_page', 'product_list']],
        ['class' => '.content span.countdown', 'property' => 'font-size', 'field' => 'countdown_font_size', 'layouts' => ['product_page', 'product_list']],
    ];

    protected static $cache_flash_sales = [];

    private $templateFileHome;
    private $templateFileHomeTab;
    private $templateFileHomeTabContent;
    private $templateFileColumn;
    private $templateFileCountdown;

    public function __construct()
    {
        $this->name = 'flashsales';
        $this->tab = 'front_office_features';
        $this->version = '2.4.0';
        $this->author = 'Keyrnel';
        $this->module_key = '36c6c0e545ecf7134526a92f293d06da';
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Flash Sales - Extended');
        $this->description = $this->l('Add, edit, customize your flash sales effectively and intuitively to boost your sales!');
        $this->ps_versions_compliancy = ['min' => '1.6.0', 'max' => _PS_VERSION_];

        $this->templateFileHome = version_compare(_PS_VERSION_, '1.7', '>=') ? 'module:flashsales/views/templates/hook/home.tpl' : 'home.tpl';
        $this->templateFileHomeTab = version_compare(_PS_VERSION_, '1.7', '>=') ? 'module:flashsales/views/templates/hook/home-tab.tpl' : 'home-tab.tpl';
        $this->templateFileHomeTabContent = version_compare(_PS_VERSION_, '1.7', '>=') ? 'module:flashsales/views/templates/hook/home-tab-content.tpl' : 'home-tab-content.tpl';
        $this->templateFileColumn = version_compare(_PS_VERSION_, '1.7', '>=') ? 'module:flashsales/views/templates/hook/column.tpl' : 'column.tpl';
        $this->templateFileCountdown = version_compare(_PS_VERSION_, '1.7', '>=') ? 'module:flashsales/views/templates/hook/countdown.tpl' : 'countdown.tpl';
    }

    public function install($delete_params = true)
    {
        if (!parent::install()
            || !$this->registerHook('actionAdminControllerSetMedia')
            || !$this->registerHook('header')
            || !$this->registerHook('displayHome')
            || !$this->registerHook('displayHomeTab')
            || !$this->registerHook('displayHomeTabContent')
            || !$this->registerHook('rightColumn')
            || !$this->registerHook('leftColumn')
            || !$this->registerHook('displayProductListReviews')
            || !$this->registerHook('actionObjectUpdateAfter')
            || !$this->registerHook('actionObjectDeleteAfter')
            || !$this->registerHook('displayProductAdditionalInfo')
            || !$this->registerHook('displayProductFlash')
            || !$this->registerHook('displayRightColumnProduct')) {
            return false;
        }

        // Install database
        if ($delete_params) {
            if (!$this->installDb()) {
                return false;
            }
        }

        // Instal conf
        if (!$this->installConf()) {
            return false;
        }

        $languages = Language::getLanguages();

        // Install Tab
        $tab = new Tab();
        foreach ($languages as $lang) {
            $tab->name[$lang['id_lang']] = $this->l('Flash Sales');
        }
        $tab->class_name = 'AdminFlashSales';
        $parent = version_compare(_PS_VERSION_, '1.7', '>=') ? 'AdminParentCartRules' : 'AdminPriceRule';
        $tab->id_parent = (int) Tab::getIdFromClassName($parent);
        $tab->module = $this->name;
        if (!$tab->add()) {
            return false;
        }

        // Install Meta
        $meta = new Meta();
        $meta->page = 'module-' . $this->name . '-page';
        $meta->configurable = 1;
        foreach ($languages as $language) {
            if ($language['iso_code'] == 'fr') {
                $meta->title[(int) $language['id_lang']] = 'Ventes flash';
                $meta->description[(int) $language['id_lang']] = 'Ventes flash';
                $meta->url_rewrite[(int) $language['id_lang']] = 'ventes-flash';
            } else {
                $meta->title[(int) $language['id_lang']] = 'Flash sales';
                $meta->description[(int) $language['id_lang']] = 'Flash sales';
                $meta->url_rewrite[(int) $language['id_lang']] = 'flash-sales';
            }
        }
        $meta->add();

        if (version_compare(_PS_VERSION_, '1.7', '<')) {
            $themes = Theme::getThemes();
            $theme_meta_value = [];
            foreach ($themes as $theme) {
                $theme_meta_value[] = [
                    'id_theme' => $theme->id,
                    'id_meta' => (int) $meta->id,
                    'left_column' => (int) $theme->default_left_column,
                    'right_column' => (int) $theme->default_right_column,
                ];
            }
            if (count($theme_meta_value) > 0) {
                Db::getInstance()->insert('theme_meta', (array) $theme_meta_value, false, true, Db::INSERT_IGNORE);
            }
        }

        // init css file
        $file = 'countdown.css';
        $path = _PS_MODULE_DIR_ . $this->name . '/views/css/front/';
        $dest = version_compare(_PS_VERSION_, '1.7', '>=') ? _PS_THEME_DIR_ . 'modules/' . $this->name . '/views/css/front/' : _PS_THEME_DIR_ . 'css/modules/' . $this->name . '/views/css/front/';
        if (!file_exists($dest) && mkdir($dest, 0777, true)) {
            copy($path . '/' . $file, $dest . '/' . $file);
        }

        return true;
    }

    public function installDb()
    {
        $sql = [];
        include dirname(__FILE__) . '/sql/install.php';
        if (!$this->executeSql($sql)) {
            return false;
        }

        return true;
    }

    public function installConf()
    {
        $languages = Language::getLanguages();

        foreach ($this->getConfigs() as $config) {
            $name = $config['name'];
            $value = $config['value'];

            if (isset($config['lang']) && $config['lang']) {
                $value = [];
                foreach ($languages as $language) {
                    $value[$language['id_lang']] = isset($config['value'][$language['iso_code']]) ? $config['value'][$language['iso_code']] : $config['value']['default'];
                }
            }

            if (isset($config['layouts']) && count($config['layouts'])) {
                foreach ($config['layouts'] as $layout) {
                    $name = $config['name'] . '_' . Tools::strtoupper($layout);
                    if (!Configuration::updateValue($name, $value)) {
                        return false;
                    }
                }
            } else {
                if (!Configuration::updateValue($name, $value)) {
                    return false;
                }
            }
        }

        return true;
    }

    public function uninstall($delete_params = true)
    {
        // Uninstall Module
        if (!parent::uninstall()) {
            return false;
        }

        // Delete conf
        if (!$this->uninstallConf()) {
            return false;
        }

        // Delete Tabs
        $tabs = Tab::getCollectionFromModule($this->name);
        foreach ($tabs as $tab) {
            $tab->delete();
        }

        // Delete Meta
        $metas = Meta::getMetaByPage('module-' . $this->name . '-page', (int) $this->context->language->id);
        $meta = new Meta((int) $metas['id_meta']);
        if ($meta->delete() && version_compare(_PS_VERSION_, '1.7', '<')) {
            Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'theme_meta` WHERE id_meta=' . (int) $meta->id);
        }

        // Delete top-menu link
        $topmenu_class = version_compare(_PS_VERSION_, '1.7', '>=') ? 'Ps_MenuTopLinks' : 'MenuTopLinks';
        if (class_exists($topmenu_class)) {
            $shop_id = (int) Shop::getContextShopID();
            $shop_group_id = Shop::getGroupFromShop($shop_id);
            $conf = Configuration::get('MOD_BLOCKTOPMENU_ITEMS', null, $shop_group_id, $shop_id);

            $id_linksmenutop = $this->addTopMenuLink(true);
            $topmenu_class::remove($id_linksmenutop, $shop_id);

            Configuration::updateValue(
                'MOD_BLOCKTOPMENU_ITEMS',
                str_replace(['LNK' . $id_linksmenutop . ',', 'LNK' . $id_linksmenutop], '', $conf),
                false,
                $shop_group_id,
                $shop_id
            );
        }

        // Delete specific prices & database
        if ($delete_params) {
            if (!$this->deleteSpecificPrice() || !$this->uninstallDb()) {
                return false;
            }
        }

        return true;
    }

    public function deleteSpecificPrice()
    {
        $ids_flash_sale = FlashSale::getFlashSalesLite();
        foreach ($ids_flash_sale as $id_flash_sale) {
            $flash_sale = new FlashSale((int) $id_flash_sale);
            $flash_sale->deleteSpecificPrice();
        }

        return true;
    }

    public function uninstallDb()
    {
        $sql = [];
        include dirname(__FILE__) . '/sql/uninstall.php';
        if (!$this->executeSql($sql)) {
            return false;
        }

        return true;
    }

    public function uninstallConf()
    {
        foreach ($this->getConfigs() as $config) {
            $name = $config['name'];

            if (isset($config['layouts']) && count($config['layouts'])) {
                foreach ($config['layouts'] as $layout) {
                    $name = $config['name'] . '_' . Tools::strtoupper($layout);
                    Configuration::deleteByName($name);
                }
            } else {
                Configuration::deleteByName($name);
            }
        }

        return true;
    }

    public function reset()
    {
        if (!$this->uninstall(false)) {
            return false;
        }
        if (!$this->install(false)) {
            return false;
        }

        return true;
    }

    protected function executeSql($sql = [])
    {
        foreach ($sql as $s) {
            if (!Db::getInstance()->execute($s)) {
                return false;
            }
        }

        return true;
    }

    public function getContent()
    {
        if (Tools::isSubmit('submitFlashSales')) {
            $this->updateConfigFieldsValues();

            $topmenu_module = version_compare(_PS_VERSION_, '1.7', '>=') ? 'ps_mainmenu' : 'blocktopmenu';
            $topmenu = Module::getInstanceByName($topmenu_module);
            if ($topmenu && $topmenu->active) {
                $this->updateTopMenuDisplay();

                if (version_compare(_PS_VERSION_, '1.7', '>=')) {
                    $dir = _PS_CACHE_DIR_ . DIRECTORY_SEPARATOR . 'ps_mainmenu';
                    if (is_dir($dir)) {
                        foreach (scandir($dir) as $entry) {
                            if (preg_match('/\.json$/', $entry)) {
                                unlink($dir . DIRECTORY_SEPARATOR . $entry);
                            }
                        }
                    }
                } else {
                    $topmenu->_clearCache('blocktopmenu.tpl');
                }
            }

            $this->updateCssFile();

            if (!count($this->_post_errors)) {
                $this->clearCache();
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name . '&conf=4&token=' . Tools::getAdminTokenLite('AdminModules'));
            } else {
                foreach ($this->_post_errors as $err) {
                    $this->_html .= $this->displayError($err);
                }
            }
        }

        $this->_html .= $this->renderFlashSalesForm();

        return $this->_html;
    }

    public function renderFlashSalesForm()
    {
        $languages = Language::getLanguages();
        $topmenu_module = version_compare(_PS_VERSION_, '1.7', '>=') ? 'ps_mainmenu' : 'blocktopmenu';
        $topmenu = Module::getInstanceByName($topmenu_module);

        $css_path = _PS_MODULE_DIR_ . $this->name . '/views/css/front/countdown.css';
        $css_override_path = version_compare(_PS_VERSION_, '1.7', '>=') ? _PS_THEME_DIR_ . 'modules/' . $this->name . '/views/css/front/countdown.css' : _PS_THEME_DIR_ . 'css/modules/' . $this->name . '/views/css/front/countdown.css';
        if (file_exists($css_override_path)) {
            $css_path = $css_override_path;
        }

        $css_file = Tools::file_get_contents($css_path);
        $css_fields = $this->loadCssFields($css_file);

        $this->context->smarty->assign([
            'languages' => Language::getLanguages(true, Context::getContext()->shop->id),
            'default_language' => (int) Configuration::get('PS_LANG_DEFAULT'),
            'currentIndex' => $this->context->link->getAdminLink('AdminModules', true) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name,
            'currentToken' => Tools::getAdminTokenLite('AdminModules'),
            'css_file' => $css_file,
            'css_fields' => $css_fields,
            'topmenu' => (bool) ($topmenu && $topmenu->active),
            'tpl_general' => _PS_MODULE_DIR_ . $this->name . '/views/templates/admin/settings/general.tpl',
            'tpl_display' => _PS_MODULE_DIR_ . $this->name . '/views/templates/admin/settings/display.tpl',
            'tpl_tab_home_page' => _PS_MODULE_DIR_ . $this->name . '/views/templates/admin/settings/tabs/home_page.tpl',
            'tpl_tab_product_page' => _PS_MODULE_DIR_ . $this->name . '/views/templates/admin/settings/tabs/product_page.tpl',
            'tpl_tab_flashsale_page' => _PS_MODULE_DIR_ . $this->name . '/views/templates/admin/settings/tabs/flashsale_page.tpl',
            'tpl_tab_product_list' => _PS_MODULE_DIR_ . $this->name . '/views/templates/admin/settings/tabs/product_list.tpl',
            'tpl_tab_column' => _PS_MODULE_DIR_ . $this->name . '/views/templates/admin/settings/tabs/column.tpl',
            'tpl_fields' => _PS_MODULE_DIR_ . $this->name . '/views/templates/admin/settings/_partials/fields.tpl',
        ]);

        $this->context->controller->addJS([
            _MODULE_DIR_ . $this->name . '/views/js/tools/colorpicker.js',
            _MODULE_DIR_ . $this->name . '/views/js/admin/settings.js',
        ]);

        return $this->context->smarty->createTemplate(_PS_MODULE_DIR_ . $this->name . '/views/templates/admin/settings/form.tpl', $this->context->smarty)->fetch();
    }

    protected function updateConfigFieldsValues()
    {
        $config = $this->getConfigFieldsValues();
        foreach ($config as $key => $val) {
            Configuration::updateValue($key, $val);
        }

        return true;
    }

    protected function loadCssFields($content)
    {
        if (empty($content)) {
            return [];
        }

        $fields = [];

        foreach ($this->getLayouts() as $layout) {
            $properties = [];

            foreach ($this->getCssFields() as $css_field) {
                if (isset($css_field['layouts']) && !in_array($layout, $css_field['layouts'])) {
                    continue;
                }
                $pos = stripos($content, '.flashsale-countdown-box.' . $layout);
                if (isset($css_field['class'])) {
                    $pos = stripos($content, $css_field['class'], $pos);
                }

                if (preg_match('#' . $css_field['property'] . '[^-]#', $content, $matches, PREG_OFFSET_CAPTURE, $pos)) {
                    $pos = stripos($content, ':', $matches[0][1]);
                    $start = $pos + 1;
                    $end = stripos($content, ';', $start);
                    $output = Tools::substr($content, $start, $end - $start);

                    // replace pattern
                    $pattern = ['/px/', '/%/', '/inline-block/', '/solid/', '/none/'];
                    $replace = ['', '', 1, 1, 0];
                    $output = preg_replace($pattern, $replace, $output);
                    // delete spaces
                    $output = trim($output);

                    $properties[$css_field['field']] = $output;
                }
            }

            $fields[$layout] = $properties;
        }

        return $fields;
    }

    protected function updateCssFile()
    {
        $content = strip_tags(Tools::getValue('css_file'));

        // replace correct end of line
        $content = str_replace("\r\n", PHP_EOL, $content);
        // Magic Quotes shall... not.. PASS!
        if (_PS_MAGIC_QUOTES_GPC_) {
            $content = stripslashes($content);
        }

        if (Validate::isCleanHTML($content)) {
            $path = version_compare(_PS_VERSION_, '1.7', '>=') ? _PS_THEME_DIR_ . 'modules/' . $this->name . '/views/css/front/countdown.css' : _PS_THEME_DIR_ . 'css/modules/' . $this->name . '/views/css/front/countdown.css';
            if (!file_put_contents($path, $content)) {
                $this->_post_errors[] = sprintf($this->l('File "%s" cannot be created'), dirname($path));
            }
        } else {
            $this->_post_errors[] = $this->l('Your css cannot contain JavaScript code.');
        }
    }

    protected function updateTopMenuDisplay()
    {
        $shop_id = (int) Shop::getContextShopID();
        $shop_group_id = Shop::getGroupFromShop($shop_id);
        $conf = Configuration::get('MOD_BLOCKTOPMENU_ITEMS', null, $shop_group_id, $shop_id);

        $display = Tools::getValue('flashsale_display_topmenu');
        Configuration::updateValue('FLASHSALE_DISPLAY_TOPMENU', (int) $display);

        $id_linksmenutop = $this->addTopMenuLink();

        if (!$display) {
            $topmenu_class = version_compare(_PS_VERSION_, '1.7', '>=') ? 'Ps_MenuTopLinks' : 'MenuTopLinks';
            $topmenu_class::remove((int) $id_linksmenutop, $shop_id);

            Configuration::updateValue('MOD_BLOCKTOPMENU_ITEMS', str_replace(['LNK' . (int) $id_linksmenutop . ',', 'LNK' . (int) $id_linksmenutop], '', $conf), false, (int) $shop_group_id, (int) $shop_id);
        } else {
            $menu_items = Tools::strlen($conf) ? explode(',', $conf) : [];
            if (!in_array('LNK' . (int) $id_linksmenutop, $menu_items)) {
                $menu_items[] = 'LNK' . (int) $id_linksmenutop;
            }

            Configuration::updateValue('MOD_BLOCKTOPMENU_ITEMS', implode(',', $menu_items), false, (int) $shop_group_id, (int) $shop_id);
        }

        return true;
    }

    protected function addTopMenuLink($remove = false)
    {
        $id_linksmenutop = 0;
        $topmenu_class = version_compare(_PS_VERSION_, '1.7', '>=') ? 'Ps_MenuTopLinks' : 'MenuTopLinks';
        $labels = [];
        $page_link = [];
        $languages = Language::getLanguages();

        foreach ($languages as $language) {
            $label = ($language['iso_code'] == 'fr') ? 'Ventes flash' : 'Flash sales';
            $labels[(int) $language['id_lang']] = $label;
            $page_link[(int) $language['id_lang']] = $this->context->link->getModuleLink($this->name, 'page', [], null, (int) $language['id_lang']);
            $links = $topmenu_class::gets((int) $language['id_lang'], null, (int) Shop::getContextShopID());
            foreach ($links as $link) {
                if ($link['link'] == $page_link[(int) $language['id_lang']]) {
                    $id_linksmenutop = (int) $link['id_linksmenutop'];
                    break 2;
                }
            }
        }
        if ($id_linksmenutop == 0 && !$remove) {
            $topmenu_class::add($page_link, $labels, 0, (int) Shop::getContextShopID());
            $id_linksmenutop = $this->addTopMenuLink();
        }

        return $id_linksmenutop;
    }

    public function getConfigFieldsValues()
    {
        $fields = [];
        $languages = Language::getLanguages();

        foreach ($this->getConfigs() as $config) {
            $name = $config['name'];

            if (isset($config['layouts']) && count($config['layouts'])) {
                foreach ($config['layouts'] as $layout) {
                    $name = $config['name'] . '_' . Tools::strtoupper($layout);
                    if (isset($config['lang']) && $config['lang']) {
                        foreach ($languages as $language) {
                            $value = Tools::getValue(Tools::strtolower($name) . '_' . $language['id_lang'], Configuration::get($name, $language['id_lang']));
                            if ($this->validateFieldValue($name, $value, $config['validate'])) {
                                $fields[$name][$language['id_lang']] = Tools::getValue(Tools::strtolower($name) . '_' . $language['id_lang'], Configuration::get($name, $language['id_lang']));
                            }
                        }
                    } else {
                        $value = Tools::getValue(Tools::strtolower($name), Configuration::get($name));
                        if ($this->validateFieldValue($name, $value, $config['validate'])) {
                            $fields[$name] = $value;
                        }
                    }
                }
            } else {
                if (isset($config['lang']) && $config['lang']) {
                    foreach ($languages as $language) {
                        $value = Tools::getValue(Tools::strtolower($name) . '_' . $language['id_lang'], Configuration::get($name, $language['id_lang']));
                        if ($this->validateFieldValue($name, $value, $config['validate'])) {
                            $fields[$name][$language['id_lang']] = Tools::getValue(Tools::strtolower($name) . '_' . $language['id_lang'], Configuration::get($name, $language['id_lang']));
                        }
                    }
                } else {
                    $value = Tools::getValue(Tools::strtolower($name), Configuration::get($name));
                    if ($this->validateFieldValue($name, $value, $config['validate'])) {
                        $fields[$name] = $value;
                    }
                }
            }
        }

        return $fields;
    }

    public function validateFieldValue($field, $value, $validate)
    {
        if (Validate::{$validate}($value)) {
            return true;
        }

        $this->_post_errors[] = sprintf($this->l('Unvalid field : "%s"'), $field);

        return false;
    }

    public function hookActionAdminControllerSetMedia()
    {
        if (!($this->context->controller->controller_name == 'AdminModules' && Tools::getValue('configure') == $this->name)
            && $this->context->controller->controller_name != 'AdminFlashSales') {
            return false;
        }

        $this->context->controller->addCSS($this->_path . 'views/css/admin/admin-design.css', 'all');
    }

    public function hookHeader($params)
    {
        $clearCache = false;
        $flash_sale_ids = FlashSale::getFlashSalesLite();

        foreach ($flash_sale_ids as $flash_sale_id) {
            $now = date('Y-m-d H:i:s');
            $flash_sale = new FlashSale((int) $flash_sale_id);
            if ($flash_sale->active && !$flash_sale->cache && $flash_sale->getStart() <= $now && $flash_sale->getEnd() > $now) {
                $flash_sale->cache = 1;
                $flash_sale->update();
                $clearCache = true;
            } elseif ($flash_sale->active && $flash_sale->getEnd() <= $now) {
                $flash_sale->cache = 0;
                $flash_sale->active = 0;
                $flash_sale->update();
                $clearCache = true;
            }
        }

        if ($clearCache) {
            $this->clearCache();
        }

        $this->context->controller->addJS($this->_path . 'views/js/front/countdown.js');
        $this->context->controller->addCSS($this->_path . 'views/css/front/countdown.css', 'all');
        $this->context->controller->addCSS($this->_path . 'views/css/front/global.css', 'all');

        if (Configuration::get('FLASHSALE_PRODUCT_LIST')
            && !($this->context->controller instanceof FlashsalesPageModuleFrontController && (int) Tools::getValue('id_flash_sale') > 0)
        ) {
            if (version_compare(_PS_VERSION_, '1.7', '<')) {
                $this->context->controller->addCSS($this->_path . 'views/css/front/miniature16.css', 'all');
            } else {
                $this->context->controller->addCSS($this->_path . 'views/css/front/miniature17.css', 'all');
            }
        }

        foreach ($this->getConfigs() as $config) {
            if ($config['name'] != 'FLASHSALE_CAROUSEL') {
                continue;
            }

            foreach ($config['layouts'] as $layout) {
                $name = $config['name'] . '_' . Tools::strtoupper($layout);
                if (Configuration::get($name)) {
                    $this->context->controller->addJS($this->_path . 'views/js/tools/owl.carousel.min.js');
                    $this->context->controller->addCSS($this->_path . 'views/css/tools/owl.carousel.min.css', 'all');
                    $this->context->controller->addCSS($this->_path . 'views/css/tools/owl.theme.default.min.css', 'all');
                    break 2;
                }
            }
        }
    }

    public function hookActionObjectUpdateAfter($params)
    {
        $object = $params['object'];

        if (get_class($object) === 'Product') {
            $this->clearCache();
        }

        return true;
    }

    public function hookActionObjectDeleteAfter($params)
    {
        $object = $params['object'];
        $clear_cache = true;

        if (get_class($object) === 'Product') {
            Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'flash_sale_products` WHERE `id_product` = ' . (int) $object->id);
            $flush_modules = false;
        } elseif (get_class($object) === 'Category') {
            Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'flash_sale_products` WHERE `id_category` = ' . (int) $object->id);
        } elseif (get_class($object) === 'Manufacturer') {
            Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'flash_sale_products` WHERE `id_manufacturer` = ' . (int) $object->id);
        } elseif (get_class($object) === 'SpecificPrice') {
            Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'flash_sale_specific_prices` WHERE `id_specific_price` = ' . (int) $object->id);
        } else {
            $clear_cache = false;
        }

        if ($clear_cache) {
            $this->clearCache();
        }

        return true;
    }

    public function hookDisplayHome($params)
    {
        if (!($cache_id = $this->retrieveCacheId())) {
            return;
        }

        if (!$this->isCached($this->templateFileHome, 'flashsale-home|' . $cache_id)) {
            if (!isset(flashsales::$cache_flash_sales['home'])) {
                $flash_sales = FlashSale::getFlashSales((int) $this->context->language->id, 'home');

                foreach ($flash_sales as $key => $flash_sale) {
                    $flash_sales[$key]['products'] = $this->getProducts('home', (int) $flash_sale['id_flash_sale']);
                    if (!$flash_sales[$key]['products']) {
                        unset($flash_sales[$key]);
                        continue;
                    }

                    $banner = _PS_MODULE_DIR_ . $this->name . '/views/img/banner/' . (int) $flash_sale['id_flash_sale'] . '.jpg';
                    $flash_sales[$key]['banner'] = file_exists($banner)
                        ? $this->context->link->getMediaLink(_MODULE_DIR_ . $this->name . '/views/img/banner/' . (int) $flash_sale['id_flash_sale'] . '.jpg') . '?' . filemtime($banner)
                        : false;
                }

                if (!count($flash_sales)) {
                    $flash_sales = false;
                }

                flashsales::$cache_flash_sales['home'] = $flash_sales;
            }

            if (flashsales::$cache_flash_sales['home'] === false) {
                return;
            }

            $this->smarty->assign([
                'flashsales' => [
                    'layout' => 'home_page',
                    'flash_sales' => flashsales::$cache_flash_sales['home'],
                    'txt' => Configuration::get('FLASHSALE_COUNTDOWN_STRING_HOME_PAGE', (int) $this->context->language->id),
                    'tpl_product_list' => $this->getTemplateProductListPath(),
                ],
            ]);
        }

        return $this->renderContent($this->templateFileHome, 'flashsale-home|' . $cache_id);
    }

    public function hookDisplayHomeTab($params)
    {
        if (!($cache_id = $this->retrieveCacheId())) {
            return;
        }

        if (!$this->isCached($this->templateFileHomeTab, 'flashsale-home-tab|' . $cache_id)) {
            if (!isset(flashsales::$cache_flash_sales['home_tab'])) {
                flashsales::$cache_flash_sales['home_tab'] = $this->getProducts('home_tab');
            }

            if (flashsales::$cache_flash_sales['home_tab'] === false) {
                return;
            }
        }

        return $this->renderContent($this->templateFileHomeTab, 'flashsale-home-tab|' . $cache_id);
    }

    public function hookDisplayHomeTabContent($params)
    {
        if (!($cache_id = $this->retrieveCacheId())) {
            return;
        }

        if (!$this->isCached($this->templateFileHomeTabContent, 'flashsale-home-tab-content|' . $cache_id)) {
            if (!isset(flashsales::$cache_flash_sales['home_tab'])) {
                flashsales::$cache_flash_sales['home_tab'] = $this->getProducts('home_tab');
            }

            if (flashsales::$cache_flash_sales['home_tab'] === false) {
                return;
            }

            $imageType = version_compare(_PS_VERSION_, '1.7', '>=')
                ? ImageType::getFormattedName('home')
                : ImageType::getFormatedName('home')
            ;

            $this->smarty->assign([
                'flashsales' => [
                    'flash_sales' => flashsales::$cache_flash_sales['home_tab'],
                    'homeSize' => Image::getSize($imageType),
                ],
            ]);
        }

        return $this->renderContent($this->templateFileHomeTabContent, 'flashsale-home-tab-content|' . $cache_id);
    }

    public function hookRightColumn($params)
    {
        if (!($cache_id = $this->retrieveCacheId())) {
            return;
        }

        if (!$this->isCached($this->templateFileColumn, 'flashsale-column|' . $cache_id)) {
            if (!isset(flashsales::$cache_flash_sales['column'])) {
                flashsales::$cache_flash_sales['column'] = $this->getProducts('column');
            }

            if (flashsales::$cache_flash_sales['column'] === false) {
                return;
            }

            foreach (flashsales::$cache_flash_sales['column'] as &$product) {
                $product['price'] = Tools::displayPrice($product['price']);
                $product['price_tax_exc'] = Tools::displayPrice($product['price_tax_exc']);
                $product['price_without_reduction'] = Tools::displayPrice($product['price_without_reduction']);
            }

            $this->smarty->assign([
                'flashsales' => [
                    'layout' => 'column',
                    'flash_sales' => flashsales::$cache_flash_sales['column'],
                    'priceDisplay' => Product::getTaxCalculationMethod((int) $this->context->customer->id),
                    'tpl_product_list' => $this->getTemplateProductListPath(),
                ],
            ]);
        }

        return $this->renderContent($this->templateFileColumn, 'flashsale-column|' . $cache_id);
    }

    public function hookLeftColumn($params)
    {
        return $this->hookRightColumn($params);
    }

    public function hookDisplayRightColumnProduct($params)
    {
        //$id_product = (int) Tools::getValue('id_product');
		$id_product = (int) Tools::getValue('id_product') ?: $params['product']['id'];
        if (!($specific_price = FlashSale::getProductSpecificPrice($id_product))) {
            return;
        }

        $cache_id = null;
        $id_product_attribute = (int) Tools::getValue('id_product_attribute', Product::getDefaultAttribute($id_product));
        if ($specific_price_cached = FlashSale::getProductSpecificPrice($id_product, $id_product_attribute)) {
            $cache_id = $specific_price_cached['id_specific_price'] . '|' . (int) Context::getContext()->language->id;
        }

        if (!$cache_id || !$this->isCached($this->templateFileCountdown, 'flashsale-product|' . $cache_id)) {
            $ids = [];
            $ids_combination = Product::getProductAttributesIds($id_product);
            foreach ($ids_combination as $id_combination) {
                $ids[] = (int) $id_combination['id_product_attribute'];
            }

            array_unshift($ids, 0);

            $combinations = [];
            $to = false;
            foreach ($ids as $id) {
                if (!($specific_price = FlashSale::getProductSpecificPrice($id_product, $id)) || $specific_price['reduction'] == 0) {
                    continue;
                }

                $combinations[$id] = strtotime($specific_price['to']);
                if ($id_product_attribute == $id) {
                    $to = strtotime($specific_price['to']);
                }
            }

            $this->smarty->assign([
                'flashsales' => [
                    'to' => $to,
                    'layout' => 'product_page',
                    'combinations' => json_encode($combinations),
                    'txt' => Configuration::get('FLASHSALE_COUNTDOWN_STRING_PRODUCT_PAGE', (int) $this->context->language->id),
                    'id_product' => $id_product,
                    'id_product_attribute' => $id_product_attribute,
                ],
            ]);
        }

        return $this->renderContent($this->templateFileCountdown, 'flashsale-product|' . $cache_id);
    }

    public function hookDisplayProductListReviews($params)
    {
        $id_product = (int) $params['product']['id_product'];
        if (!($specific_price = FlashSale::getProductSpecificPrice($id_product))) {
            return;
        }

        $cache_id = $specific_price['id_specific_price'] . '|' . (int) Context::getContext()->language->id;

        if (!$this->isCached($this->templateFileCountdown, 'flashsale-miniature|' . $cache_id)) {
            $this->smarty->assign([
                'flashsales' => [
                    'layout' => isset($params['layout']) && !empty($params['layout']) ? $params['layout'] : 'product_list',
                    'to' => (int) strtotime($specific_price['to']),
                    'txt' => Configuration::get('FLASHSALE_COUNTDOWN_STRING_PRODUCT_LIST', (int) $this->context->language->id),
                    'id_product' => $id_product,
                ],
            ]);
        }

        return $this->renderContent($this->templateFileCountdown, 'flashsale-miniature|' . $cache_id);
    }

    public function hookDisplayProductAdditionalInfo($params)
    {
        return $this->hookDisplayRightColumnProduct($params);
    }
	
	public function hookDisplayProductFlash($params)
    {
        return $this->hookDisplayRightColumnProduct($params);
    }

    public function renderContent($template, $cacheId = null)
    {
        if (version_compare(_PS_VERSION_, '1.7', '<')) {
            return $this->display(__FILE__, $template, $cacheId);
        } else {
            return $this->fetch($template, $cacheId);
        }
    }

    public function getProducts($filter = null, $id_flash_sale = null)
    {
        if (Configuration::get('PS_CATALOG_MODE')) {
            return false;
        }

        $nbProducts = false;
        if ($filter) {
            $conf = $filter;
            if ($filter == 'home') {
                $conf = 'home_page';
            } elseif ($filter == 'page') {
                $conf = 'flashsale_page';
            }

            $nbProducts = (int) Configuration::get('FLASHSALE_PRODUCTS_NB_' . Tools::strtoupper($conf));
        }

        if (version_compare(_PS_VERSION_, '1.7', '<')) {
            return FlashSale::getAllProducts((int) $this->context->language->id, $id_flash_sale, $filter, false, 0, $nbProducts ? $nbProducts : 8);
        } else {
            $flashsale = new FlashSale((int) $id_flash_sale);

            $searchProvider = new FlashSaleProductSearchProvider(
                $this->context->getTranslator(),
                $flashsale
            );

            $context = new PrestaShop\PrestaShop\Core\Product\Search\ProductSearchContext($this->context);

            $query = new PrestaShop\PrestaShop\Core\Product\Search\ProductSearchQuery();
            $query
                ->setQueryType('flash-sales')
                ->setResultsPerPage($nbProducts)
                ->setPage(1)
                ->setSortOrder(new PrestaShop\PrestaShop\Core\Product\Search\SortOrder('product', 'position', 'asc'))
            ;

            $result = $searchProvider->runQuery(
                $context,
                $query
            );

            $assembler = new ProductAssembler($this->context);

            $presenterFactory = new ProductPresenterFactory($this->context);
            $presentationSettings = $presenterFactory->getPresentationSettings();
            $presenter = new PrestaShop\PrestaShop\Core\Product\ProductListingPresenter(
                new PrestaShop\PrestaShop\Adapter\Image\ImageRetriever(
                    $this->context->link
                ),
                $this->context->link,
                new PrestaShop\PrestaShop\Adapter\Product\PriceFormatter(),
                new PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever(),
                $this->context->getTranslator()
            );

            $products_for_template = [];

            foreach ($result->getProducts() as $rawProduct) {
                $products_for_template[] = $presenter->present(
                    $presentationSettings,
                    $assembler->assembleProduct($rawProduct),
                    $this->context->language
                );
            }

            return $products_for_template;
        }
    }

    public function clearCache()
    {
        Context::getContext()->smarty->clearCache($this->getTemplatePath($this->templateFileHome));
        Context::getContext()->smarty->clearCache($this->getTemplatePath($this->templateFileColumn));
        Context::getContext()->smarty->clearCache($this->getTemplatePath($this->templateFileHomeTabContent));
        Context::getContext()->smarty->clearCache($this->getTemplatePath($this->templateFileHomeTab));
        Context::getContext()->smarty->clearCache($this->getTemplatePath($this->templateFileCountdown));
    }

    public function getTemplateProductListPath()
    {
        $path = null;

        if (version_compare(_PS_VERSION_, '1.7', '>=')) {
            if (Configuration::get('FLASHSALE_PRODUCT_LIST')) {
                $path = 'module:flashsales/views/templates/front/1.7/product-list.tpl';
            } else {
                $path = 'catalog/_partials/miniatures/product.tpl';
            }
        } else {
            if (Configuration::get('FLASHSALE_PRODUCT_LIST')) {
                $path = _PS_MODULE_DIR_ . $this->name . '/views/templates/front/1.6/product-list.tpl';
            } else {
                $path = _PS_THEME_DIR_ . 'product-list.tpl';
            }
        }

        return $path;
    }

    public function retrieveCacheId()
    {
        if (!isset(flashsales::$cache_flash_sales['cache_id'])) {
            $cache_id = [];
            $flash_sales = FlashSale::getFlashSales(Context::getContext()->language->id);

            foreach ($flash_sales as $flash_sale) {
                $cache_id[] = $flash_sale['id_flash_sale'];
            }

            flashsales::$cache_flash_sales['cache_id'] = count($cache_id)
                ? implode('-', $cache_id) . '|' . (int) Context::getContext()->currency->id . '|' . (int) Context::getContext()->language->id
                : false
            ;
        }

        return flashsales::$cache_flash_sales['cache_id'];
    }

    public function getLayouts()
    {
        return $this->_layouts;
    }

    public function getConfigs()
    {
        return $this->_configs;
    }

    public function getCssFields()
    {
        return $this->_css_fields;
    }
}
