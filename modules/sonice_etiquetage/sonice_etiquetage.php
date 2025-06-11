<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to a commercial license from Common-Services Co., Ltd.
 * Use, copy, modification or distribution of this source file without written
 * license agreement from the SARL SMC is strictly forbidden.
 * In order to obtain a license, please contact us: contact@common-services.com
 * ...........................................................................
 * INFORMATION SUR LA LICENCE D'UTILISATION
 *
 * L'utilisation de ce fichier source est soumise a une licence commerciale
 * concedee par la societe Common-Services Co., Ltd.
 * Toute utilisation, reproduction, modification ou distribution du present
 * fichier source sans contrat de licence ecrit de la part de la Common-Services Co. Ltd. est
 * expressement interdite.
 * Pour obtenir une licence, veuillez contacter Common-Services Co., Ltd. a l'adresse: contact@common-services.com
 * ...........................................................................
 *
 * @package   SoNice_Etiquetage
 * @author    Alexandre D. <alexandre@common-services.com>
 * @copyright Copyright (c) 2011-2015 Common Services Co Ltd - 90/25 Sukhumvit 81 - 10260 Bangkok - Thailand
 * @license   Commercial license
 * Support by mail  :  support.sonice@common-services.com
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once(_PS_MODULE_DIR_.'sonice_etiquetage/classes/SoColissimoContext.php');
require_once(_PS_MODULE_DIR_.'sonice_etiquetage/classes/SoNiceEtiquetageHsCode.php');
if (!class_exists('SoColissimoTools')) {
    require_once(_PS_MODULE_DIR_.'sonice_etiquetage/classes/SoColissimoTools.php');
}
if (!class_exists('ConfigureMessage')) {
    require_once(_PS_MODULE_DIR_.'sonice_etiquetage/classes/shared/configure_message.class.php');
}

class SoNice_Etiquetage extends Module
{

    const ADD = 'a';
    const REMOVE = 'd';
    const UPDATE = 'u';

    protected $ps17x = false;
    protected $ps16x = false;
    protected $ps15x = false;

    public $id_lang;
    public $path;
    public $url;
    public $download_folder;
    public $http_download_folder;
    public $function_folder;

    /** @var boolean Specify if the module is run in debug mode or not */
    protected $debug = false;


    public function __construct()
    {
        $this->name = 'sonice_etiquetage';
        $this->tab = 'shipping_logistics';
        $this->version = '2.1.08';
        $this->author = 'Common-Services';
        $this->need_instance = 0;
        $this->module_key = '97992f91886920dfdc342479fa373a38';

        parent::__construct();

        $this->displayName = 'SoNice Etiquetage';
        $this->description = $this->l(
            'This web service permits to generate labels with or without proof of submission according to your profiles,
            defined during your contract submission to this label solution.'
        );

        $this->path = _PS_MODULE_DIR_.$this->name.'/';
        $this->url = __PS_BASE_URI__.basename(_PS_MODULE_DIR_).'/'.$this->name.'/';
        $this->download_folder = $this->path.'download/';
        $this->http_download_folder = $this->url.'download/';
        $this->function_folder = $this->url.'functions/';
        $this->bootstrap = true;

        $this->initContext();
    }


    /**
     * Init Prestashop context for version 1.5 & 1.4
     */
    private function initContext()
    {
        $this->ps17x = version_compare(_PS_VERSION_, '1.7', '>=');
        $this->ps16x = version_compare(_PS_VERSION_, '1.6', '>=');
        $this->ps15x = version_compare(_PS_VERSION_, '1.5', '>=');

        if ($this->ps15x) {
            $this->context = Context::getContext();
            $this->id_lang = (int)Context::getContext()->language->id;
        } else {
            require_once(_PS_MODULE_DIR_.'sonice_etiquetage/backward_compatibility/backward.php');

            $this->context = Context::getContext();
            $this->id_lang = (int)isset(Context::getContext()->language->id) ?
                Context::getContext()->language->id : Configuration::get('PS_LANG_DEFAULT');
        }

        $this->context->smarty->assign(
            array(
                'ps16x' => $this->ps16x,
                'ps15x' => $this->ps15x,
                'sne_url' => $this->url,
                'sne_css' => $this->url.'views/css/',
                'sne_js' => $this->url.'views/js/',
                'sne_img' => $this->url.'views/img/'
            )
        );

        $this->debug = (bool)Configuration::get('SONICE_ETQ_DEBUG');
    }


    /**
     * Set up hooks
     *
     * @param string $action
     * @return bool
     */
    private function hookSetup($action)
    {
        if ($this->ps15x) {
            $expected_hooks = array(
                'displayAdminOrder',
                'ActionCarrierUpdate'
            );
        } else {
            $expected_hooks = array(
                'adminOrder',
                'UpdateCarrier'
            );
        }

        $pass = true;

        if ($action == self::ADD) {
            foreach ($expected_hooks as $expected_hook) {
                if (!$this->registerHook($expected_hook)) {
                    $pass = false;
                }
            }
        }

        if ($action == self::REMOVE) {
            foreach ($expected_hooks as $expected_hook) {
                if (!$this->unregisterHook($expected_hook)) {
                    $pass = false;
                }
            }
        }

        return ($pass);
    }


    private function myAddJS($url)
    {
        if ($this->ps15x) {
            return ($this->context->controller->addJS($url));
        } else {
            echo '<script type="text/javascript" src="'.$url.'"></script>';
        }
    }


    private function myAddJQueryUI($name)
    {
        if ($this->ps15x) {
            return ($this->context->controller->addJqueryUI($name));
        }
    }


    private function myAddCSS($url, $media)
    {
        if ($this->ps15x) {
            return ($this->context->controller->addCSS($url, $media));
        } else {
            echo '<link type="text/css" rel="stylesheet" href="'.$url.'">';
        }
    }


    private function backofficeInformations()
    {
        $php_infos = array();
        $module_infos = array();
        $module_config = unserialize(Configuration::get('SONICE_ETQ_CONF'));
        $prestashop_infos = array();

        // Web service settings
        if (!isset($module_config['ContractNumber']) || empty($module_config['ContractNumber'])) {
            $module_infos['ws_login']['message'] = $this->l(
                'You did not set a login yet, please fill the login field in the Login tab.'
            );
            $module_infos['ws_login']['level'] = version_compare(_PS_VERSION_, '1.6', '>=') ?
                'alert alert-warning' : 'warn';
        }
        if (!isset($module_config['Password']) || empty($module_config['Password'])) {
            $module_infos['ws_pwd']['message'] = $this->l(
                'You did not set a password yet, please fill the password field in the Login tab.'
            );
            $module_infos['ws_pwd']['level'] = version_compare(_PS_VERSION_, '1.6', '>=') ?
                'alert alert-warning' : 'warn';
        }
        if (!isset($module_config['Line2']) || empty($module_config['Line2']) ||
            !isset($module_config['countryCode']) || empty($module_config['countryCode']) ||
            !isset($module_config['City']) || empty($module_config['City']) ||
            !isset($module_config['PostalCode']) || empty($module_config['PostalCode'])
        ) {
            $module_infos['ws_address']['message'] = $this->l(
                'You did not set an address yet, please fill the address fields in the Address tab.'
            );
            $module_infos['ws_address']['level'] = version_compare(_PS_VERSION_, '1.6', '>=') ?
                'alert alert-warning' : 'warn';
        }

        // PHP
        if (!is_writable(_PS_MODULE_DIR_.'sonice_etiquetage/download')) {
            $php_infos['dl_w']['message'] = sprintf($this->l(
                'You have to set write permissions to the %s directory'
            ), _PS_MODULE_DIR_.'sonice_etiquetage/download');
            $php_infos['dl_w']['level'] = version_compare(_PS_VERSION_, '1.6', '>=') ? 'alert alert-warning' : 'warn';
        }
        if (!method_exists('DOMDocument', 'createElement')) {
            $php_infos['domdocument']['message'] = $this->l(
                'PHP DOMDocument (XML Library) must be installed on this server. '.
                'The module require this library and can\'t work without it.'
            );
            $php_infos['domdocument']['level'] = version_compare(_PS_VERSION_, '1.6', '>=') ?
                'alert alert-danger' : 'error';
            $php_infos['domdocument']['link'] = 'http://php.net/manual/'.Language::getIsoById($this->id_lang).
                '/class.domdocument.php';
        }
        if (!function_exists('curl_init')) {
            $php_infos['curl']['message'] = $this->l('cURL extension must be available on your server.');
            $php_infos['cur;']['level'] = version_compare(_PS_VERSION_, '1.6', '>=') ? 'alert alert-danger' : 'error';
            $php_infos['curl']['link'] = 'http://php.net/manual/'.Language::getIsoById($this->id_lang).'/book.curl.php';
        }
        if (in_array(@Tools::strtolower(ini_get('display_errors')), array('1', 'on'))) {
            $php_infos['display_errors']['message'] = $this->l('PHP variable display_errors is On.');
            $php_infos['display_errors']['level'] = $this->ps16x ? 'alert alert-info' : 'info';
        }
        if (!function_exists('mb_convert_encoding')) {
            $php_infos['mbstring']['message'] = $this->l(
                'PHP mb_string functions must be installed on this server. '.
                'The module require this library and can\'t work without it.'
            );
            $php_infos['mbstring']['level'] = version_compare(_PS_VERSION_, '1.6', '>=') ?
                'alert alert-danger' : 'error';
            $php_infos['mbstring']['link'] = 'http://php.net/manual/'.Language::getIsoById($this->id_lang).
                '/mbstring.setup.php';
        }

        // PrestaShop
        if (!(int)Configuration::get('PS_SHOP_ENABLE')) {
            $prestashop_infos['maintenance']['message'] = $this->l(
                'Be carefull, your shop is in maintenance mode, the module might not work in that mode'
            );
            $prestashop_infos['maintenance']['level'] = version_compare(_PS_VERSION_, '1.6', '>=') ?
                'alert alert-warning' : 'warn';
        }
        if (_PS_MODE_DEV_) {
            $prestashop_infos['dev_mode']['message'] = $this->l('The Prestashop constant _PS_MODE_DEV_ is enabled.');
            $prestashop_infos['dev_mode']['level'] = $this->ps16x ? 'alert alert-info' : 'info';
        }

        // URL issues for Ajax
        $pass = true;
        if (version_compare(_PS_VERSION_, '1.5', '>=')) {
            if (Shop::isFeatureActive()) {
                $shop = Context::getContext()->shop;

                if ($_SERVER['HTTP_HOST'] != $shop->domain && $_SERVER['HTTP_HOST'] != $shop->domain_ssl) {
                    $pass = false;
                }
            } else {
                $urls = ShopUrl::getShopUrls($this->context->shop->id)->where('main', '=', 1)->getFirst();
                if ($_SERVER['HTTP_HOST'] != $urls->domain && $_SERVER['HTTP_HOST'] != $urls->domain_ssl) {
                    $pass = false;
                }
            }
        } elseif (version_compare(_PS_VERSION_, '1.4', '>=')) {
            if ($_SERVER['HTTP_HOST'] != Configuration::get('PS_SHOP_DOMAIN') &&
                $_SERVER['HTTP_HOST'] != Configuration::get('PS_SHOP_DOMAIN_SSL')
            ) {
                $pass = false;
            }
        }

        if (!$pass) {
            $prestashop_infos['wrong_domain']['message'] = $this->l(
                    'Your are currently connected with the following domain name:'
                ).' '.$_SERVER['HTTP_HOST'].'. '.$this->l(
                    'This one is different from the main shop domain name set in "Preferences > SEO & URLs":'
                ).' '.Configuration::get('PS_SHOP_DOMAIN');
            $prestashop_infos['wrong_domain']['level'] = version_compare(_PS_VERSION_, '1.6', '>=') ?
                'alert alert-danger' : 'error';
        }

        $view_params = array();
        $view_params['module_infos'] = $module_infos;
        $view_params['module_info_ok'] = !count($module_infos);
        $view_params['php_infos'] = $php_infos;
        $view_params['php_info_ok'] = !count($php_infos);
        $view_params['prestashop_infos'] = $prestashop_infos;
        $view_params['prestashop_info_ok'] = !count($prestashop_infos);

        ob_start();
        try {
            @phpinfo(INFO_ALL & ~INFO_CREDITS & ~INFO_LICENSE & ~INFO_ENVIRONMENT & ~INFO_VARIABLES);
        } catch (Exception $excp) {
            echo 'phpinfo()  has been disabled  for security reasons. '.$excp->getMessage();
        }
        $phpinfos = ob_get_clean();
        $phpinfos = preg_replace(
            '/(a:link.*)|(body, td, th, h1, h2.*)|(img.*)|(td, th.*)|(a:hover.*)|(class="center")/',
            '',
            $phpinfos
        );
        $view_params['phpinfo_str'] = empty($phpinfos) ?
            $this->l('phpinfo()  has been disabled  for security reasons.') : $phpinfos;
        $view_params['psinfo_str'] = $this->psInfo();
        $view_params['dbinfo_str'] = $this->dbInfo();

        return $view_params;
    }


    public function psInfo()
    {
        $prestashop_info = '';

        if ($this->ps15x) {
            $sort = 'ORDER by `name`,`id_shop`';
        } else {
            $sort = 'ORDER by `name`';
        }

        $results = Db::getInstance()->executeS(
            'SELECT *
            FROM `'._DB_PREFIX_.'configuration`
            WHERE `name` LIKE "PS_%"
            OR `name` LIKE "SONICE_ETQ_%" '.
            pSQL($sort)
        );
        $ps_configuration = null;

        foreach ($results as $result) {
            if (preg_match('/KEY|EMAIL|PASSWORD|PASSWD|CONTEXT_DATA/', $result['name'])) {
                continue;
            }

            $value = $result['value'];

            if (base64_encode(base64_decode($value, true)) === $value) {
                $value = base64_decode($value, true);
            }

            if (@serialize(@unserialize($value)) == $value) {
                $value = '<div class="print_r">'.print_r(unserialize($value), true).'</div>';
            } else {
                $value = Tools::strlen($result['value']) > 128 ?
                    Tools::substr($result['value'], 0, 128).'...' : $result['value'];
            }

            if ($this->ps15x) {
                $ps_configuration .= sprintf(
                    '%-50s %03d %03d : %s'."\n",
                    $result['name'],
                    $result['id_shop'],
                    $result['id_shop_group'],
                    $value
                );
            } else {
                $ps_configuration .= sprintf('%-50s : %s'."\n", $result['name'], $value);
            }
        }

        $prestashop_info .= '<h1>Prestashop</h1>';
        $prestashop_info .= '<pre>';
        $prestashop_info .= 'Version: '._PS_VERSION_."\n\n";

        $prestashop_info .= "\n";
        $prestashop_info .= $ps_configuration;

        $prestashop_info .= '</pre>'."\n\n";

        return ($prestashop_info);
    }


    public function dbInfo()
    {
        $tables_to_check = array(
            _DB_PREFIX_.'sonice_etq_label',
            _DB_PREFIX_.'sonice_etq_session',
            _DB_PREFIX_.'sonice_etq_session_detail'
        );

        $query = Db::getInstance()->executeS('SHOW TABLES');
        $tables = array();
        foreach ($query as $rows) {
            foreach ($rows as $t) {
                $tables[$t] = 1;
            }
        }

        $not_existing_tables = array();
        foreach ($tables_to_check as $to_check) {
            if (!isset($tables[$to_check])) {
                $not_existing_tables[] = $to_check;
                ConfigureMessage::error(
                    $this->l('The table').' `'.$to_check.'` '.$this->l('was not found in your database.')
                );
            }
        }

        $tables_column = array();
        foreach ($tables_to_check as $to_check) {
            if (in_array($to_check, $not_existing_tables)) {
                $tables_column[] = null;
                continue;
            }

            $result = Db::getInstance()->executeS('SHOW COLUMNS FROM `'.pSQL($to_check).'`');
            foreach ($result as $id => $column) {
                $result[$id] = $column['Field'];
            }
            $tables_column[] = $result;
        }

        $db_info = '<h1>'.$this->l('Database').'</h1>';
        $db_info .= '<pre>';
        foreach ($tables_to_check as $id => $to_check) {
            $db_info .= 'SHOW COLUMNS FROM `'.$to_check.'` : '.(
                in_array($to_check, $not_existing_tables) ? 'N/A<br>' : print_r($tables_column[$id], true)
                );
        }
        $db_info .= '</pre>';

        return ($db_info);
    }


    private function checkhttpDownloadFolder()
    {
        if (!is_writable($this->download_folder)) {
            @chmod($this->download_folder, 7777);
        }

        $content = 'Hello World !'.PHP_EOL;
        if (file_put_contents($this->download_folder.'TO_DELETE.txt', $content) === false) {
            printf(
                '%s/%d: %s - %s',
                basename(__FILE__),
                __LINE__,
                $this->l('Failed to write to the directory'),
                $this->download_folder
            );

            return (false);
        }

        return (true);
    }


    public function install()
    {
        $pass = true;

        if (!parent::install()) {
            $this->_errors[] = $this->l('An error occured while installing with parent::install().');
            $pass = false;
        }
        if (!$this->hookSetup(self::ADD)) {
            $this->_errors[] = $this->l('An error occured while registering hooks.');
            $pass = false;
        }
        if (!$this->checkhttpDownloadFolder()) {
            $this->_errors[] = $this->l('An error occured while checking folder permissions.');
            $pass = false;
        }

        // Basic configuration setup
        $shop_phone = Configuration::get('PS_SHOP_PHONE');
        $shop_phone = preg_replace('/\D/', '', $shop_phone);

        $compatibility_mode = 1;
        if (SoColissimoTools::moduleIsInstalled('soflexibilite')) {
            $compatibility_mode = (int)Configuration::get('SOFLEXIBILITE_MODE');
        } elseif (SoColissimoTools::moduleIsInstalled('soliberte')) {
            $compatibility_mode = (int)Configuration::get('SOLIBERTE_MODE');
        } elseif (SoColissimoTools::moduleIsInstalled('socolissimo')) {
            $compatibility_mode = 1;
        }

        $default_shop_informations = array(
            'ContractNumber' => null,
            'Password' => null,
            'bordereauNumber' => 1,
            'Name' => null,
            'Surname' => null,
            'ServiceInfo' => null,
            'companyName' => Configuration::get('PS_SHOP_NAME'),
            'Line0' => Configuration::get('PS_SHOP_ADDR2'),
            'Line1' => null,
            'Line2' => Configuration::get('PS_SHOP_ADDR1'),
            'Line3' => null,
            'PostalCode' => Configuration::get('PS_SHOP_CODE'),
            'City' => Configuration::get('PS_SHOP_CITY'),
            'countryCode' => 'FR',
            'phoneNumber' => $shop_phone,
            'Mail' => Configuration::get('PS_SHOP_EMAIL'),
            'demo' => null,
            'insurance' => null,
            'deposit_site' => null,
            'deposit_label' => null,
            'pickup_site' => null,
            'pickup_label' => null,
            'deposit_date' => null,
            'compatibility' => $compatibility_mode,
            'new_order_state_send' => (int)Db::getInstance()->getValue(
                'SELECT DISTINCT pos.`id_order_state`
				FROM `'._DB_PREFIX_.'order_state` pos, `'._DB_PREFIX_.'order_state_lang` posl
				WHERE pos.`id_order_state` = posl.`id_order_state`
				AND `logable` = 1
				'.(!$this->ps15x ? '-- ' : '').'AND `paid` = 1
				AND `delivery` = 1
				'.(!$this->ps15x ? '-- ' : '').'AND `shipped` = 1
				AND `template` = "shipped"'
            ),
            'new_order_state_created' => (int)Db::getInstance()->getValue(
                'SELECT `id_order_state`
				FROM `'._DB_PREFIX_.'order_state`
				WHERE `logable` = 1
				'.(!$this->ps15x ? '-- ' : '').'AND `paid` = 1
				AND `delivery` = 1
				'.(!$this->ps15x ? '-- ' : '').'AND `shipped` = 0'
            ),
            'send_mail_creation' => 1,
            'output_print_type' => 'PDF_A4_300dpi'
        );

        if (!$this->tabSetup(self::ADD)) {
            $this->_errors[] = $this->l('An error occured while installing tab.');
            $pass = false;
        }

        Configuration::updateValue('SONICE_ETQ_CONF', serialize($default_shop_informations));

        // Filters status setup
        $filtered_status = Db::getInstance()->executeS(
            'SELECT `id_order_state`
			FROM `'._DB_PREFIX_.'order_state`
			WHERE `logable` = 1
			'.(!$this->ps15x ? '-- ' : '').'AND `paid` = 1
			AND `delivery` = 0
			'.(!$this->ps15x ? '-- ' : '').'AND `shipped` = 0'
        );
        foreach ($filtered_status as $idx => $status) {
            $filtered_status[$idx] = reset($status);
        }
        Configuration::updateValue('SONICE_ETQ_STATUS', serialize($filtered_status));

        // Filters carriers
        $carriers = Db::getInstance()->executeS(
            'SELECT `id_carrier`
			FROM `'._DB_PREFIX_.'carrier`
			WHERE
			(
				`external_module_name` IN (
					"soflexibilite",
					"soliberte",
					"socolissimo"
				)
				OR `name` LIKE "%colissimo%"
			)
			AND `deleted` = 0'
        );
        foreach ($carriers as $idx => $carrier) {
            $carriers[$idx] = reset($carrier);
        }
        Configuration::updateValue('SONICE_ETQ_CARRIER', serialize($carriers));

        // Carriers mapping
        $carrier_mapping = array();
        foreach ($carriers as $id_carrier) {
            $carrier = new Carrier((int)$id_carrier);

            if (!Validate::isLoadedObject($carrier)) {
                continue;
            }

            // So Colissimo
            if (strpos(Tools::strtolower($carrier->name), 'domicile') !== false &&
                strpos(Tools::strtolower($carrier->name), 'avec') !== false
            ) {
                $carrier_mapping[$id_carrier] = 'DOS';
            } elseif (strpos(Tools::strtolower($carrier->name), 'domicile') !== false) {
                $carrier_mapping[$id_carrier] = 'DOM';
            } elseif (strpos(Tools::strtolower($carrier->name), 'bureau') !== false ||
                strpos(Tools::strtolower($carrier->name), 'la poste') !== false
            ) {
                $carrier_mapping[$id_carrier] = 'BPR';
            } elseif (strpos(Tools::strtolower($carrier->name), 'commer') !== false ||
                strpos(Tools::strtolower($carrier->name), 'relais') !== false
            ) {
                $carrier_mapping[$id_carrier] = 'A2P';
            } // Colissimo
            elseif (strpos(Tools::strtolower($carrier->name), 'expert') !== false &&
                strpos(Tools::strtolower($carrier->name), 'inter') !== false
            ) {
                $carrier_mapping[$id_carrier] = 'COLI';
            } elseif (strpos(Tools::strtolower($carrier->name), 'expert') !== false &&
                strpos(Tools::strtolower($carrier->name), 'om') !== false
            ) {
                $carrier_mapping[$id_carrier] = 'CDS';
            } elseif (strpos(Tools::strtolower($carrier->name), 'expert') !== false) {
                $carrier_mapping[$id_carrier] = 'COL';
            } elseif (strpos(Tools::strtolower($carrier->name), 'access') !== false &&
                strpos(Tools::strtolower($carrier->name), 'om') !== false
            ) {
                $carrier_mapping[$id_carrier] = 'COM';
            } elseif (strpos(Tools::strtolower($carrier->name), 'access') !== false) {
                $carrier_mapping[$id_carrier] = 'COLD';
            }
        }
        Configuration::updateValue('SONICE_ETQ_CARRIER_MAPPING', serialize($carrier_mapping));

        $pass &= $this->createTables();

        return ((bool)$pass);
    }

    private function createTables()
    {
        $pass = true;
        $sql = array();

        $sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'sonice_etq_label` (
                    `id_label` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                    `id_order` INT(11) UNSIGNED NOT NULL,
                    `parcel_number` VARCHAR(20) NOT NULL,
                    `pdfurl` VARCHAR(256) NOT NULL,
                    `file` LONGTEXT,
                    `sent` INT(1) NOT NULL DEFAULT "0",
                    `date_add` DATETIME NOT NULL,
                    PRIMARY KEY (`id_label`)
                ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';

        $sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'sonice_etq_session` (
                    `id_session` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                    `alias` VARCHAR(64) DEFAULT NULL,
                    `from` DATETIME NOT NULL,
                    `to` DATETIME NOT NULL,
                    `inter` INT(1) NOT NULL DEFAULT "0",
                    PRIMARY KEY (`id_session`)
                ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 AUTO_INCREMENT=2000';

        $sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'sonice_etq_session_detail` (
                    `id_session_detail` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                    `id_session` INT(11) UNSIGNED NOT NULL,
                    `id_order` INT(11) UNSIGNED NOT NULL,
                    `weight` DECIMAL(20,6) NOT NULL DEFAULT "0.000000",
                    PRIMARY KEY (`id_session_detail`)
                ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';

        $sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'socolissimo_delivery_info` (
                    `id_cart` INT(11) NOT NULL,
                    `id_customer` INT(11) NOT NULL,
                    `delivery_mode` VARCHAR(3) NOT NULL,
                    `prid` TEXT(10) NOT NULL,
                    `prname` VARCHAR(64) NOT NULL,
                    `prfirstname` VARCHAR(64) NOT NULL,
                    `prcompladress` TEXT NOT NULL,
                    `pradress1` TEXT NOT NULL,
                    `pradress2` TEXT NOT NULL,
                    `pradress3` TEXT NOT NULL,
                    `pradress4` TEXT NOT NULL,
                    `przipcode` TEXT(10) NOT NULL,
                    `prtown` VARCHAR(64) NOT NULL,
                    `cecountry` VARCHAR(10) NOT NULL,
                    `cephonenumber` VARCHAR(10) NOT NULL,
                    `ceemail` VARCHAR(64) NOT NULL,
                    `cecompanyname` VARCHAR(64) NOT NULL,
                    `cedeliveryinformation` TEXT NOT NULL,
                    `cedoorcode1` VARCHAR(10) NOT NULL,
                    `cedoorcode2` VARCHAR(10) NOT NULL,
                    PRIMARY KEY  (`id_cart`, `id_customer`)
                ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';

        $sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'so_delivery` (
					`id` INT(11) NOT NULL AUTO_INCREMENT,
					`order_id` INT(11) DEFAULT NULL,
					`cart_id` INT(11) DEFAULT NULL,
					`point_id` INT(11) NOT NULL,
					`customer_id` INT(11) NOT NULL,
					`firstname` VARCHAR(38) DEFAULT NULL,
					`lastname` VARCHAR(38) DEFAULT NULL,
					`company` VARCHAR(38) DEFAULT NULL,
					`telephone` VARCHAR(10) DEFAULT NULL,
					`email` VARCHAR(64) DEFAULT NULL,
					`type` VARCHAR(3) DEFAULT NULL,
					`libelle` VARCHAR(50) DEFAULT NULL,
					`indice` VARCHAR(70) DEFAULT NULL,
					`code_postal` VARCHAR(5) DEFAULT NULL,
					`commune` VARCHAR(32) DEFAULT NULL,
					`pays` VARCHAR(32) NOT NULL,              
					`adresse1` VARCHAR(38) DEFAULT NULL,
					`adresse2` VARCHAR(38) DEFAULT NULL,
					`lieudit` VARCHAR(38) DEFAULT NULL,
					`informations` TEXT,
					PRIMARY KEY (`id`),
					UNIQUE KEY `u_order_id` (`order_id`),
					UNIQUE KEY `u_cart_id` (`cart_id`)
				) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 AUTO_INCREMENT=10000000';

        $sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'sonice_etq_hscode` (
					`id` INT(11) NOT NULL AUTO_INCREMENT,
					`id_category` INT(11) NOT NULL,
					`hscode` VARCHAR(6) NOT NULL,
					PRIMARY KEY (`id`),
					UNIQUE KEY `u_id_category` (`id_category`)
				) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';

        $sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'sonice_etq_hscode` (
					`id` INT(11) NOT NULL AUTO_INCREMENT,
					`id_category` INT(11) NOT NULL,
					`hscode` VARCHAR(6) NOT NULL,
					PRIMARY KEY (`id`),
					UNIQUE KEY `u_id_category` (`id_category`)
				) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';

        foreach ($sql as $query) {
            if (!Db::getInstance()->execute($query)) {
                $this->_errors[] = $this->l('An error occured while creating table in database.');
                $pass = false;
            }
        }

        return $pass;
    }

    public function uninstall()
    {
        $pass = true;

        if (!$this->hookSetup(self::REMOVE)) {
            $this->_errors[] = $this->l('An error occured while unregistering hooks.');
            $pass = false;
        }
        if (!parent::uninstall()) {
            $this->_errors[] = $this->l('An error occured while uninstalling with parent::uninstall().');
            $pass = false;
        }

        if (!$this->tabSetup(self::REMOVE)) {
            $this->_errors[] = $this->l('An error occured while uninstalling tab.');
            $pass = false;
        }

        Configuration::deleteByName('SONICE_ETQ_CONF');
        Configuration::deleteByName('SONICE_ETQ_TARE');
        Configuration::deleteByName('SONICE_ETQ_STATUS');
        Configuration::deleteByName('SONICE_ETQ_CONTEXT');
        Configuration::deleteByName('SONICE_ETQ_DEBUG');
        Configuration::deleteByName('SONICE_ETQ_TEST');
        Configuration::deleteByName('SONICE_ETQ_LAST_SESSION_USED');
        Configuration::deleteByName('SONICE_ETQ_CARRIER');
        Configuration::deleteByName('SONICE_ETQ_CARRIER_MAPPING');
        Configuration::deleteByName('SONICE_ETQ_TOKEN');

        Db::getInstance()->execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'sonice_etq_label`');
        Db::getInstance()->execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'sonice_etq_session`');
        Db::getInstance()->execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'sonice_etq_session_detail`');

        return ($pass);
    }


    /**
     * Install, update or remove module's tab
     *
     * @param string $action Action to perform
     * @return bool
     */
    private function tabSetup($action)
    {
        require_once(dirname(__FILE__).'/classes/shared/tab.class.php');

        $parent_tab = $this->ps17x ? 'AdminParentOrders' : 'AdminOrders';
        $controller = $this->ps15x ? 'AdminOrdersSoniceEtiquetage' : 'OrdersSoniceEtiquetage';

        return (bool)CommonServicesTab::setup(
            $action,
            $controller,
            $this->displayName,
            $parent_tab
        );
    }


    public function getContent()
    {
        SoColissimoContext::save(Context::getContext());

        $output = null;

        if (Tools::isSubmit('submit'.$this->name)) {
            $pass = true;
            $values = Tools::getValue('return_info');
            $values['ContractNumber'] = trim($values['ContractNumber']);
            $values['Password'] = trim($values['Password']);
            $values['deposit_date'] = (int)$values['deposit_date'];

            if ($values['ContractNumber'] && $values['Password']) {
                Configuration::updateValue(
                    'SONICE_ETQ_TOKEN',
                    md5($values['companyName'].$values['ContractNumber'].$values['Password']),
                    false,
                    0,
                    0
                );
            }

            $pass &= Configuration::updateValue('SONICE_ETQ_CONF', serialize($values));
            $pass &= Configuration::updateValue('SONICE_ETQ_CARRIER', serialize(Tools::getValue('filtered_carriers')));
            $pass &= Configuration::updateValue(
                'SONICE_ETQ_CARRIER_MAPPING',
                serialize(Tools::getValue('carrier_mapping'))
            );
            $pass &= Configuration::updateValue('SONICE_ETQ_STATUS', serialize(Tools::getValue('filtered_status')));
            $pass &= Configuration::updateValue(
                'SONICE_ETQ_TARE',
                serialize($this->formatTare(Tools::getValue('tare')))
            );
            $pass &= Configuration::updateValue('SONICE_ETQ_DEBUG', Tools::getValue('sne_debug'));
            $pass &= Configuration::updateValue('SONICE_ETQ_TEST', Tools::getValue('sne_test_mode'));
            $pass &= SoNiceEtiquetageHsCode::saveHsCodes(Tools::getValue('sne_hscode'));
            $pass &= $this->tabSetup(self::ADD);
            $pass &= $this->createTables();

            if ($pass) {
                $output .= $this->displayConfirmation($this->l('Options updated.'));
            } else {
                $output .= $this->displayError($this->l('The system failed to save your options.'));
            }
        }

        return ($output.$this->displayForm());
    }


    public function displayForm()
    {
        require_once(dirname(__FILE__).'/classes/shared/configure_tab.class.php');
        require_once dirname(__FILE__).'/classes/CommonPrintServer.php';

        $html = '';
        $alert_class = array();
        $alert_class['danger'] = $this->ps16x ? 'alert alert-danger' : 'error';
        $alert_class['warning'] = $this->ps16x ? 'alert alert-warning' : 'warn';
        $alert_class['success'] = $this->ps16x ? 'alert alert-success' : 'conf';
        $alert_class['info'] = $this->ps16x ? 'alert alert-info' : 'hint';

        $module_configuration = unserialize(Configuration::get('SONICE_ETQ_CONF'));

        $this->context->smarty->assign(
            array(
                'sne_name' => $this->displayName,
                'sne_version' => $this->version,
                'ps_version' => _PS_VERSION_,
                'sne_description' => $this->description,
                'sne_module_dir' => $this->url,
                'sne_module_path' => $this->path,
                'sne_check_login' => $this->url.'functions/check_login.php',
                'sne_test_printers' => $this->url.'functions/test_printers.php',
                'sne_common_printserver' => $this->url.'functions/common-printserver.php',
                'sne_get_shop_info' => $this->url.'functions/get_shop_info.php?token='.
                    Configuration::get('SONICE_ETQ_TOKEN', null, null, null),
                'sne_config' => $module_configuration,
                'sne_carrier_mapping' => unserialize(Configuration::get('SONICE_ETQ_CARRIER_MAPPING')),
                'sne_tare_list' => unserialize(Configuration::get('SONICE_ETQ_TARE')),
                'sne_info' => $this->backofficeInformations(),
                'sne_carriers' => $this->carriers(),
                'sne_status' => $this->status(),
                'sne_hscode' => $this->hsCode(),
                'sne_weight_unit' => Tools::strtolower(Configuration::get('PS_WEIGHT_UNIT')),
                'selected_tab' => Tools::getValue('selected_tab') ? Tools::getValue('selected_tab') : '0',
                'sne_module_name' => $this->name,
                'alert_class' => $alert_class,
                'sne_debug' => Configuration::get('SONICE_ETQ_DEBUG'),
                'sne_test_mode' => Configuration::get('SONICE_ETQ_TEST'),
                'current_tab_level' => 0,
                'sne_printers_list' => array(), // CommonPrintServer::getPrinters()
                'sne_address_countries' => Db::getInstance()->executeS(
                    'SELECT c.`iso_code`, cl.`name`
                    FROM `'._DB_PREFIX_.'country` c
                    JOIN `'._DB_PREFIX_.'country_lang` cl ON c.`id_country` = cl.`id_country`
                    WHERE cl.`id_lang` = '.(int)$this->id_lang.'
                    ORDER BY cl.`name`'
                ) // Faster than Country::getCountries()
            )
        );

        $tab_list = array();
        $tab_list[] = array(
            'id' => 'sonice_etiquetage',
            'img' => 'sonice_etiquetage',
            'name' => 'SoNice &Eacutetiquetage',
            'selected' => true
        );
        $tab_list[] = array(
            'id' => 'informations',
            'img' => 'information',
            'name' => 'Informations',
            'selected' => false
        );
        $tab_list[] = array('id' => 'account', 'img' => 'key', 'name' => $this->l('Account'), 'selected' => false);
        $tab_list[] = array('id' => 'address', 'img' => 'house', 'name' => $this->l('Address'), 'selected' => false);
        $tab_list[] = array(
            'id' => 'filter',
            'img' => 'filter',
            'name' => $this->l('Filter statuses'),
            'selected' => false
        );
        $tab_list[] = array('id' => 'carrier', 'img' => 'lorry', 'name' => $this->l('Carriers'), 'selected' => false);
        $tab_list[] = array(
            'id' => 'carrier_mapping',
            'img' => 'lorry_link',
            'name' => $this->l('Carriers Mapping'),
            'selected' => false
        );
        $tab_list[] = array(
            'id' => 'international',
            'img' => 'world',
            'name' => $this->l('International'),
            'selected' => false
        );
        $tab_list[] = array(
            'id' => 'tare',
            'img' => 'weighing_mashine',
            'name' => $this->l('Packaging tare weight'),
            'selected' => false
        );
        $tab_list[] = array('id' => 'print', 'img' => 'printer', 'name' => $this->l('Printing'), 'selected' => false);
        $tab_list[] = array('id' => 'conf', 'img' => 'cog_edit', 'name' => $this->l('Settings'), 'selected' => false);

        $this->myAddCSS($this->url.'views/css/configuration16.css', 'all');
        $this->myAddJS($this->url.'views/js/configuration.js');

        if (isset($module_configuration['legacy']) && !$module_configuration['legacy'] ||
            !isset($module_configuration['legacy'])) {
            $this->myAddJS($this->url.'views/js/common-printserver.js');
        }

        if ($this->ps15x) {
            $this->myAddCSS($this->url.'views/css/jquery.qtip.min.css', 'all');
            $this->myAddJS($this->url.'views/js/jquery.qtip.min.js');
        }

        $html .= ConfigureMessage::display();
        $html .= $this->context->smarty->fetch(dirname(__FILE__).'/views/templates/admin/configuration/header.tpl');
        $html .= ConfigureTab::generateTabs($tab_list);
        $html .= $this->context->smarty->fetch(
            dirname(__FILE__).'/views/templates/admin/configuration/configuration.tpl'
        );

        return ($html);
    }

    private function hsCode()
    {
        $view_params = array();

        $categories = Category::getCategories((int)$this->id_lang, false);
        $index = array();

        $default_categories = array();
        $default_hscode2categories = SoNiceEtiquetageHsCode::getHsCodesPerCategory();

        if (version_compare(_PS_VERSION_, '1.5', '>=')) {
            $id_shop = Validate::isLoadedObject($this->context->shop) ? $this->context->shop->id : 1;

            $shop = new Shop($id_shop);
            $first = null;

            foreach ($categories as $categories1) {
                foreach ($categories1 as $category) {
                    if ($category['infos']['id_category'] == Category::getRootCategory(null, $shop)->id_category) {
                        $first = $category;
                    }
                }
            }

            $default_category = $shop->id_category;
        } else {
            foreach ($categories as $first1 => $categories_array) {
                break;
            }

            if (!isset($first1)) {
                $first1 = 0;
            }
            if (!isset($categories_array)) {
                $categories_array = array();
            }

            foreach ($categories_array as $first2 => $categories_array2) {
                break;
            }

            if (!isset($first2)) {
                $first2 = 0;
            }

            $first = $categories[$first1][$first2];

            $default_category = 1;
        }

        $view_params['html'] = self::recurseCategoryForInclude(
            $index,
            $categories,
            $first,
            $default_category,
            null,
            $default_categories,
            $default_hscode2categories,
            true
        );

        return $view_params;
    }

    public function recurseCategoryForInclude($indexedCategories, $categories, $current, $id_category = 1,
        $id_category_default = null, $default_categories = array(), $default_profiles = array(), $init = false)
    {
        static $done;
        static $irow;
        static $chtml;

        if (!isset($chtml)) {
            $chtml = null;
        }
        if (!isset($done) || !is_array($done)) {
            $done = array();
        }

        $chtml = isset($chtml) ? $chtml : null;

        if (!isset($done[$current['infos']['id_parent']])) {
            $done[$current['infos']['id_parent']] = 0;
        }
        $done[$current['infos']['id_parent']] += 1;

        $todo = count($categories[$current['infos']['id_parent']]);
        $doneC = $done[$current['infos']['id_parent']];

        $level = $current['infos']['level_depth'] + 1;
        $img = ($init == true) ? 'lv1.gif' : 'lv'.$level.'_'.($todo == $doneC ? 'f' : 'b').'.gif';

        $input = '<input type="text" name="sne_hscode['.$id_category.']" maxlength="6" ';
        if (is_array($default_profiles) && array_key_exists((int)$id_category, $default_profiles) &&
            $default_profiles[(int)$id_category]
        ) {
            $input .= 'value="'.$default_profiles[(int)$id_category].'"';
        }
        $input .= '>';

        $chtml .= '
            <tr class="cat-line'.($irow++ % 2 ? ' alt_row' : '').'">
            <td style="cursor:pointer">
                <img src="'.$this->url.'views/img/'.$img.'" alt="" /> &nbsp;<label for="category_'.$id_category.
            '" class="t">'.Tools::stripslashes($current['infos']['name']).'</label>
            </td>
            <td align="right">
                '.(!$init ? $input : '').'
            </td>
            </tr>';

        if (isset($categories[$id_category])) {
            if ($categories[$id_category]) {
                foreach ($categories[$id_category] as $key => $row) {
                    if ($key != 'infos') {
                        self::recurseCategoryForInclude(
                            $indexedCategories,
                            $categories,
                            $categories[$id_category][$key],
                            $key,
                            $id_category_default,
                            $default_categories,
                            $default_profiles
                        );
                    }
                }
            }
        }

        return ($chtml);
    }

    private function formatTare($tare_list)
    {
        if (!is_array($tare_list)) {
            return (array());
        }

        /* REMOVE entry 0 with null value */
        unset($tare_list[0]);

        /* Check each fields */
        foreach ($tare_list as $key => $tare) {
            if (count($tare) < 3 || !Validate::isFloat($tare['from']) || !Validate::isFloat($tare['to']) ||
                !Validate::isFloat($tare['weight'])) {
                unset($tare_list[$key]);
                continue;
            }

            if (Tools::strtolower(Configuration::get('PS_WEIGHT_UNIT')) === 'g') {
                $tare_list[$key]['from'] /= 1000;
                $tare_list[$key]['to'] /= 1000;
                $tare_list[$key]['weight'] /= 1000;
            }
        }

        return ($tare_list);
    }


    private function status()
    {
        $status = OrderState::getOrderStates($this->id_lang);
        $filtered_status = unserialize(Configuration::get('SONICE_ETQ_STATUS'));
        $available_status = array();
        $selected_status = array();

        if (!is_array($filtered_status)) {
            $filtered_status = array();
        }

        foreach ($status as $state) {
            if (!in_array($state['id_order_state'], $filtered_status)) {
                $available_status[] = $state;
            } else {
                $selected_status[] = $state;
            }
        }

        $view_params = array();
        $view_params['available'] = $available_status;
        $view_params['filtered'] = $selected_status;
        $view_params['all'] = $status;

        return ($view_params);
    }


    private function carriers()
    {
        $carrier_conf = unserialize(Configuration::get('SONICE_ETQ_CARRIER'));
        $carrier_list = Carrier::getCarriers(
            $this->id_lang,
            false,
            false,
            false,
            null,
            $this->ps15x ? Carrier::ALL_CARRIERS : 5
        );
        $carrier_list_deleted = Carrier::getCarriers(
            $this->id_lang,
            false,
            true,
            false,
            null,
            $this->ps15x ? Carrier::ALL_CARRIERS : 5
        );

        if (isset($carrier_conf) && is_array($carrier_conf)) {
            $filtered_carriers = array_flip($carrier_conf);
        } else {
            $filtered_carriers = null;
        }

        $available_carriers = array();
        $selected_carriers = array();
        $deleted_carriers = array();

        foreach ($carrier_list as $carrier) {
            if (isset($filtered_carriers) && isset($filtered_carriers[(int)$carrier['id_carrier']])) {
                $selected_carriers[] = $carrier;
            } else {
                $available_carriers[] = $carrier;
            }
        }

        foreach ($carrier_list_deleted as $deleted_carrier) {
            if (isset($filtered_carriers) && isset($filtered_carriers[(int)$deleted_carrier['id_carrier']])) {
                $deleted_carriers[] = $deleted_carrier;
            }
        }

        $view_params = array();
        $view_params['available'] = $available_carriers;
        $view_params['filtered'] = $selected_carriers;
        $view_params['deleted'] = $deleted_carriers;

        return ($view_params);
    }

    /*
     * Prestashop 1.5 hook list
     */


    public function hookDisplayAdminOrder($params)
    {
        $data = array();
        $order = new Order((int)$params['id_order']);
        if (!Validate::isLoadedObject($order)) {
            return false;
        }

        $carrier_configuration = unserialize(Configuration::get('SONICE_ETQ_CARRIER'));
        $status_configuration = unserialize(Configuration::get('SONICE_ETQ_STATUS'));
        $module_configuration = unserialize(Configuration::get('SONICE_ETQ_CONF'));

        if (!in_array((int)$order->id_carrier, $carrier_configuration)) {
            return false;
        }

        $url_base = Configuration::get('PS_SSL_ENABLED') ? 'https://' : 'http://';
        $url_base .= htmlspecialchars($_SERVER['HTTP_HOST'], ENT_COMPAT, 'UTF-8').__PS_BASE_URI__.
            'modules/sonice_etiquetage/download/';
        $base_path = _PS_MODULE_DIR_.$this->name.'/download/';

        if (!class_exists('SoColissimoPDF')) {
            require_once(_PS_MODULE_DIR_.'sonice_etiquetage/classes/SoColissimoPDF.php');
        }

        if (SoColissimoPDF::getParcelNumberByIdOrder((int)$params['id_order'])) {
            // Parcel existe
            $data['exists'] = true;
            $data['id_order'] = $params['id_order'];
            $data['info'] = SoColissimoPDF::getLabelInformationByIdOrder((int)$params['id_order']);
            $data['info']['session'] = SoColissimoSession::getOrderSession($params['id_order']);

            $this->context->smarty->assign(
                array(
                    'data' => $data,
                    'sne_webservice_url' => $this->url.'functions/get_labels.php',
                    'label_url' => $url_base.$data['info']['parcel_number'].(
                        in_array($module_configuration['output_print_type'], array('PDF_A4_300dpi', 'PDF_10x15_300dpi'))
                            ? '.pdf' : '.prn'
                    ),
                    'cn23_url' => file_exists($base_path.$data['info']['parcel_number'].'_CN23.pdf') ?
                        $url_base.$data['info']['parcel_number'].'_CN23.pdf' : false
                )
            );
        } else {
            // Parcel doesn't exist
            $data['exists'] = false;
            $data['id_order'] = $params['id_order'];
            $data['info'] = SoColissimoPDF::getLabelInformationByIdOrder((int)$params['id_order']);
            $data['info']['session'] = SoColissimoSession::getOrderSession($params['id_order']);

            $this->context->smarty->assign(
                array(
                    'data' => $data,
                    'sne_webservice_url' => $this->url.'functions/get_labels.php',
                    'eligible_status' => isset($order->current_state) &&
                        in_array($order->current_state, $status_configuration),
                    'label_url' => null,
                )
            );
        }

        $this->context->smarty->assign(
            array(
                'sne_module_dir' => $this->url,
                'print_type' => in_array(
                    $module_configuration['output_print_type'],
                    array('PDF_A4_300dpi', 'PDF_10x15_300dpi')
                ) ? 'PDF' : 'ZPL',
                'printer_name' => isset($module_configuration['printer2']) ? $module_configuration['printer2'] : '',
                'sne_config' => $module_configuration,
                'sne_module_url' => __PS_BASE_URI__.basename(_PS_MODULE_DIR_).'/'.$this->name.'/',
                'sne_common_printserver' => $this->url.'functions/common-printserver.php'
            )
        );

        if ($this->ps15x) {
            $this->context->controller->addJS($this->url.'views/js/order_recap.js');
            $this->context->controller->addJS($this->url.'views/js/plug.js');
            $this->context->controller->addCSS($this->url.'views/css/order_recap.css');
        } else {
            printf('<script type="text/javascript" src="'.$this->url.'views/js/order_recap.js"></script>');
            printf('<script type="text/javascript" src="'.$this->url.'views/js/plug.js"></script>');
            printf('<link type="text/css" rel="stylesheet" href="'.$this->url.'views/css/order_recap.css">');
        }

        if (isset($module_configuration['legacy']) && !$module_configuration['legacy']) {
            $this->context->controller->addJS($this->url.'views/js/common-printserver.js');
        }

        if (version_compare(_PS_VERSION_, '1.6', '>=')) {
            return $this->context->smarty->fetch($this->path.'views/templates/admin/order/hookDisplayAdminOrder16.tpl');
        }

        return $this->context->smarty->fetch($this->path.'views/templates/admin/order/hookDisplayAdminOrder.tpl');
    }

    public function hookUpdateCarrier($params)
    {
        return ($this->hookActionCarrierUpdate($params));
    }

    public function hookActionCarrierUpdate($params)
    {
        $shops = array(
            0 => null
        );

        if ($this->ps15x && Shop::isFeatureActive()) {
            $shops = Shop::getShops(true, null, true);
        }

        foreach ($shops as $id_shop) {
            $id_shop_group = method_exists('Shop', 'getGroupFromShop') ?
                (int)Shop::getGroupFromShop($id_shop, true) : null;

            $carriers = unserialize(Configuration::get('SONICE_ETQ_CARRIER', null, $id_shop_group, $id_shop));
            $carriers_conf = unserialize(Configuration::get(
                'SONICE_ETQ_CARRIER_MAPPING',
                null,
                $id_shop_group,
                $id_shop
            ));

            if (is_array($carriers) && in_array($params['id_carrier'], $carriers)) {
                $key = (int)array_search($params['id_carrier'], $carriers);
                $carriers[] = $params['id_carrier'];
                $carriers[$key] = $params['carrier']->id;
                Configuration::updateValue('SONICE_ETQ_CARRIER', serialize($carriers), false, $id_shop_group, $id_shop);

                $carriers_conf[(int)$params['carrier']->id] = $carriers_conf[(int)$params['id_carrier']];
                Configuration::updateValue(
                    'SONICE_ETQ_CARRIER_MAPPING',
                    serialize($carriers_conf),
                    false,
                    $id_shop_group,
                    $id_shop
                );
            }
        }
    }

    public function hookAdminOrder($params)
    {
        return ($this->hookDisplayAdminOrder($params));
    }
}
