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

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_2_1_10($module)
{
    $configList  = [
        'SC_FOLDER_HASH',
        'SC_INSTALLED',
        'SC_SETTINGS',
        'SC_LICENSE_DATA',
        'SC_LICENSE_KEY',
        'SC_VERSIONS',
        'SC_VERSIONS_LAST',
        'SC_VERSIONS_LAST_CHECK'
    ];
    foreach($configList as $configName) {
        Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'configuration` 
                                            SET `id_shop` = NULL, `id_shop_group` = NULL
                                            WHERE `name` = "'.pSQL($configName).'"');
    }
    return true;
}