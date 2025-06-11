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

class SoColissimoTools
{

    public static function cleanup()
    {
        $output_dir = _PS_MODULE_DIR_.'sonice_etiquetage/download/';

        if (!is_dir($output_dir)) {
            return null;
        }

        $parcel_numbers = self::arrayColumn(Db::getInstance()->executeS(
            'SELECT `parcel_number`
            FROM `'._DB_PREFIX_.'sonice_etq_label`'
        ), 'parcel_number');

        foreach (new DirectoryIterator($output_dir) as $file) {
            if ($file->isDir() || !in_array(pathinfo($file->getRealPath(), PATHINFO_EXTENSION), array('pdf', 'prn'))) {
                continue;
            } elseif (!in_array($file->getBasename('.'.pathinfo($file->getRealPath(), PATHINFO_EXTENSION)), $parcel_numbers) &&
                !in_array(Tools::substr($file->getBasename('.'.pathinfo($file->getRealPath(), PATHINFO_EXTENSION)), 0, -5), $parcel_numbers)) {
                unlink($file->getRealPath());
            } elseif ($file->getSize() < 1000) {
                unlink($file->getRealPath());
            } elseif ($file->getMTime() > (time() - (86400 * 31))) {
                continue;
            } else {
                unlink($file->getRealPath());
            }
        }

        return true;
    }

    public static function moduleIsInstalled($module_name)
    {
        if (method_exists('Module', 'isInstalled')) {
            return (Module::isInstalled($module_name));
        } else {
            Db::getInstance()->executeS(
                'SELECT `id_module`
                FROM `'._DB_PREFIX_.'module`
                WHERE `name` = "'.pSQL($module_name).'"');
            return (bool)Db::getInstance()->numRows();
        }
    }

    public static function arrayMapCastInteger($value)
    {
        return (int)$value;
    }

    public static function arrayColumn($array, $column_name)
    {
        if (function_exists('array_column')) {
            return array_column($array, $column_name);
        }

        return array_map(
            array('SoColissimoTools', 'arrayColumnCallback'),
            $array,
            array_fill(0, count($array), $column_name)
        );
    }

    private static function arrayColumnCallback($element, $column_name)
    {
        return $element[$column_name];
    }

    public static function getShopDomainSsl($http = false, $entities = false)
    {
        if (method_exists('Tools', 'getShopDomainSsl')) {
            return Tools::getShopDomainSsl($http, $entities);
        } else {
            $domain = Configuration::get('PS_SHOP_DOMAIN_SSL');

            if ($entities) {
                $domain = htmlspecialchars($domain, ENT_COMPAT, 'UTF-8');
            }

            if ($http) {
                $domain = (Configuration::get('PS_SSL_ENABLED') ? 'https://' : 'http://').$domain;
            }

            return $domain;
        }
    }

    public static function moduleIsEnabled($module_name)
    {
        if (version_compare(_PS_VERSION_, '1.5', '>=')) {
            return Module::isEnabled($module_name);
        }

        $module = new $module_name();

        return self::moduleIsInstalled($module_name) && $module->active;
    }
}
