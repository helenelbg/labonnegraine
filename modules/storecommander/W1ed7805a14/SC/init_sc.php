<?php

header('P3P: CP="IDC DSP COR CURa ADMa OUR IND PHY ONL COM STA"');
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
@ini_set('upload_max_filesize', '100M');
@ini_set('default_charset', 'utf-8');
@ini_set('max_execution_time', 0);
@ini_set('auto_detect_line_endings', '1'); // correct Mac error on eof
define('MAX_LINE_SIZE', 8192);

define('SC_DIR', dirname(__FILE__).'/');
define('SC_INSTALL_MODE', 1);
define('SC_PS_PATH_DIR', realpath(SC_DIR.'../../../../').'/');
define('SC_PS_PATH_REL', '../../../../');
define('SC_PS_MODULE_PATH_DIR', realpath(SC_DIR.'../../').'/'); // ..../modules/storecommander/
define('SC_PS_MODULE_PATH_REL', '../../');
@define('SC_COPYRIGHT', '<img src=\'lib/img/logo.png\' style=\'max-height: 16px;display: inline-block;vertical-align: text-top;\'/> Copyright 2009-'.date('Y').' SAS Mise En Prod');
define('PS_WEB_PATH', $_SERVER['SERVER_NAME']);

define('_PS_ADMIN_DIR_', 1); // for PS1.5
define('SC_CSSDHTMLX', 'lib/js/material_dhtmlx_001.css');
define('SC_CSSSTYLE', 'lib/js/material_custom_011.css');
define('SC_CSS_FONTAWESOME', 'lib/css/fontawesome/all.css');
define('SC_JQUERY', 'lib/js/jquery-1.7.1.min.js');
define('SC_JSFUNCTIONS', 'lib/js/functions_045.js');
define('SC_JSDHTMLX', 'lib/js/dhtmlx_010.js');
define('SC_CKEDITOR_CONFIG', 'config10.js');
define('SC_PLUPLOAD', 'lib/all/upload/'); // 1.5.2
define('SC_UISETTINGS_VERSION', '5');
define('SC_EXPORT_VERSION', '5');
define('SC_EXTENSION_VERSION', '2');
ob_start();
require_once SC_PS_PATH_DIR.'config/config.inc.php';
ob_end_clean();

if(!defined('SCR_DIR') &&
    version_compare(PHP_VERSION, '8.0.0', '>=') &&
    version_compare(_PS_VERSION_, '8.0.0', '>=') &&
    file_exists(realpath(SC_DIR.'../SCR')) &&
    !Tools::getValue('ajax', null))
{
    $scrParam = array();
    $scrUrlQuery = '';
    if(Tools::isSubmit('ide'))
    {
        $scrParam['ide'] = Tools::getValue('ide');
    }
    if(Tools::isSubmit('psap'))
    {
        $scrParam['psap'] = Tools::getValue('psap');
    }
    if(Tools::isSubmit('key'))
    {
        $scrParam['key'] = Tools::getValue('key');
    }
    if(!empty($scrParam))
    {
        $scrUrlQuery = '?'.http_build_query($scrParam);
    }
    header('Location: ../SCR/index.php'.$scrUrlQuery);
    exit;
}

require_once SC_DIR.'lib/php/polyfill.php';
require_once SC_DIR.'lib/php/agent.php';
require_once SC_DIR.'lib/php/uisettings.php';
require_once SC_DIR.'lib/php/uisettings_convert.php';
require_once SC_DIR.'lib/php/extension_convert.php';
require_once SC_DIR.'lib/php/extension.php';
require_once SC_DIR.'lib/php/db_update.php';

## recup list modules hook
$sql = 'SELECT h.name'.(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? ', hm.id_shop' : '').'
        FROM '._DB_PREFIX_.'hook_module hm
        LEFT JOIN '._DB_PREFIX_.'hook h ON h.id_hook = hm.id_hook';
$hook_module_list = Db::getInstance()->executeS($sql);
$cached_hook_module_list = array();
if (!empty($hook_module_list))
{
    foreach ($hook_module_list as $data)
    {
        if (!empty($data['name']))
        {
            if (isset($data['id_shop']))
            {
                $cached_hook_module_list[$data['id_shop']][strtolower($data['name'])] = $data['name'];
            }
            else
            {
                $cached_hook_module_list[strtolower($data['name'])] = $data['name'];
            }
        }
    }
}
define('SC_HOOK_MODULE_LIST', json_encode($cached_hook_module_list));

$licence = Configuration::get('SC_LICENSE_KEY');
define('IS_SUB', 1);
if (file_exists(SC_DIR.'autoload.php'))
{
    require_once SC_DIR.'autoload.php';
}
else
{
    require_once SC_DIR.'lib/php/utf16.php';
}


define('COOKIE_PATH', __PS_BASE_URI__.'modules/'.SC_MODULE_FOLDER_NAME.'/'.SCI::getConfigurationValue('SC_FOLDER_HASH'));

$ajax = Tools::getValue('ajax', 0);
$forceNoUpdateInstallModule = Tools::getValue('forceNoUpdateInstallModule', 0);
if (!$ajax && empty($forceNoUpdateInstallModule))
{
    require SC_DIR.'../../'.SC_MODULE_FOLDER_NAME.'.php';
    if (IS_RBM)
    {
        $sc_module = new StoreCommanderPs();
    }
    else
    {
        $sc_module = new StoreCommander();
    }
    $updateInstallVersion = Tools::getValue('updateInstallVersion', 0);
    if (!empty($updateInstallVersion) && $updateInstallVersion == '1')
    {
        SCI::updateConfigurationValue('SC_INSTALL_MODULE_VERSION', $sc_module->version);

        $sql = 'UPDATE '._DB_PREFIX_."module SET version='".psql($sc_module->version)."' WHERE name='".pSQL($sc_module->name)."'";
        Db::getInstance()->Execute($sql);
    }
    else
    {
        $protocol = getShopProtocol();
        $actual_link = $protocol.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        $get_prms = parse_url($actual_link, PHP_URL_QUERY);
        $separator = (empty($get_prms) ? '?' : '&');

        $actual_version = SCI::getConfigurationValue('SC_INSTALL_MODULE_VERSION');
        if (IS_RBM)
        {
            $sc_zip_filename = 'sc_rbm.zip';
        }
        else
        {
            $sc_zip_filename = 'sc.zip';
        }
        if (empty($actual_version) || $actual_version != $sc_module->version)
        {
            if (!file_exists(SC_DIR.'tmp'))
            {
                mkdir(SC_DIR.'tmp', 0777, true);
            }
            if (file_exists(SC_DIR.'tmp'))
            {
                $module_install = sc_file_get_contents('https://www.storecommander.com/'.$sc_zip_filename);
                file_put_contents(SC_DIR.'tmp/'.$sc_zip_filename, $module_install);
                if (file_exists(SC_DIR.'tmp/'.$sc_zip_filename))
                {
                    $good = extractArchive(SC_DIR.'tmp/'.$sc_zip_filename);
                    if ($good && file_exists(SC_DIR.'tmp/'.SC_MODULE_FOLDER_NAME))
                    {
                        dirMove(SC_DIR.'tmp/'.SC_MODULE_FOLDER_NAME, _PS_MODULE_DIR_, true);
                        dirRemove(SC_DIR.'tmp');
                        $actual_link .= $separator.'updateInstallVersion=1';
                        header('location: '.$actual_link);
                    }
                }
            }

            // Si arrive ici c'est que pas possible de remplacer
            $actual_link .= $separator.'forceNoUpdateInstallModule=1';
            $message = _l('Store Commander installation module cannot be updated (probably due to insufficient FTP permissions).').'<br/>';
            $message .= _l('Although you can still continue with Store Commander, it will be in degraded mode as long as the issue is not resolved:');
            $message .= ' '._l('<a href="'.$actual_link.'">Access Store Commander</a>');
            exit($message);
        }
    }
}

// check Creative Elements
define('SC_CREATIVE_ELEMENTS_ACTIVE', (
    SCI::moduleIsInstalled('creativeelements') &&
    SCI::moduleIsEnabled('creativeelements') &&
    (bool) _s('APP_ENABLE_CREATIVE_ELEMENTS') &&
    file_exists(_PS_MODULE_DIR_.'creativeelements/classes/wrappers/UId.php')
));
if (SC_CREATIVE_ELEMENTS_ACTIVE)
{
    require _PS_MODULE_DIR_.'creativeelements/classes/wrappers/UId.php'; // needed to generate uid param for CreativeElements module CE\UId()
    require SC_DIR.'lib/php/extension/ScCreativeElements.php'; // helper for creating links
}

require_once SC_DIR.'lib/php/queue_log.php';
require_once SC_DIR.'lib/php/export_convert.php';
require_once SC_DIR.'lib/php/import_convert.php';
require_once SC_DIR.'lib/php/custom_settings.php';
require_once SC_DIR.'lib/php/cutout.php';
require_once SC_DIR.'lib/php/eservices.php';

require_once SC_DIR.'lib/php/dixit.php';

## desactivation du cache sur les requetes
if (version_compare(_PS_VERSION_, '1.6.1.0', '>=') && method_exists('DbPDO', 'disableCache'))
{
    Db::getInstance()->disableCache();
}

## desactivation du cache sur les ObjectModels répétitifs
if (version_compare(_PS_VERSION_, '1.6.0.12', '>='))
{
    Product::disableCache();
    Category::disableCache();
}
