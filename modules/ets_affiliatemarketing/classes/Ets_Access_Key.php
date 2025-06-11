<?php
/**
 * Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
 */

class Ets_Access_Key extends ObjectModel
{
    /**
     * @var int
     */
    public $id_ets_am_access_key;
    /**
     * @var string
     */
    public $key;
    /**
     * @var string
     */
    public $ip_address;
    /**
     * @var int
     */
    public $id_seller;
    /**
     * @var
     */
    public $id_product;
    /**
     * @var datetime
     */
    public $datetime_added;

    public static $definition = array(
        'table' => 'ets_am_access_key',
        'primary' => 'id_ets_am_access_key',
        'multilang_shop' => true,
        'fields' => array(
            'id_ets_am_access_key' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt'
            ),
            'key' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isString'
            ),
            'ip_address' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isString'
            ),
            'id_seller' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt'
            ),
            'id_product' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt'
            ),
            'datetime_added' => array(
                'type' => self::TYPE_DATE,
                'validate' => 'isDate',
                'allow_null' => true
            )
        )
    );

    /**
     * @return bool|string
     */
    public static function generateAccessKey()
    {
        $latest_clear = Configuration::get('ETS_AM_DATE_CLEAR_ACCESS_KEY');
        if (! $latest_clear)
            self::clearAccessKey();
        else {
            $date = date('Y-m-d');
            if ($latest_clear != $date)
                self::clearAccessKey();
        }
        $accessKey = Tools::passwdGen(32);
        $unique = "SELECT COUNT(*) FROM `" . _DB_PREFIX_ . "ets_am_access_key` WHERE `key` = '". pSQL($accessKey) ."'";
        if (Db::getInstance()->getValue($unique)) {
            $accessKey = self::generateAccessKey();
        }
        return $accessKey;
    }

    /**
     * @param $acces_key
     * @return bool
     * @throws PrestaShopDatabaseException
     */
    public static function isValidAccessKey($acces_key, $id_product, $id_seller)
    {
        if(Validate::isCleanHtml($acces_key))
        {
            $sql = "SELECT * FROM `" . _DB_PREFIX_ . "ets_am_access_key` WHERE `id_seller` = " . (int)$id_seller . " AND `id_product` = " . (int)$id_product . " AND `key` = '" . pSQL($acces_key) . "' LIMIT 1";
            $row = Db::getInstance()->executeS($sql);
            if (count($row)) {
                $row = $row[0];
                $datetime_added = new DateTime($row['datetime_added']);
                $time_confirm = new DateTime();
                $date_diff = date_diff($datetime_added, $time_confirm);
                $year = $date_diff->format('%Y');
                $month = $date_diff->format('%m');
                $day = $date_diff->format('%d');
                $hour = $date_diff->format('%H');
                $min = $date_diff->format('%i');
                $second = $date_diff->format('%s');
                if ($year == 0 && $month == 0 && $day == 0 && $hour == 0 && $min == 0 && $second <= 20 && $second >= 6 ) {
                    return true;
                } else {
                    return false;
                }
            }
        }
        return false;
    }

    /**
     * @return bool
     */
    public static function clearAccessKey()
    {
        $sql = "DELETE FROM `" . _DB_PREFIX_ . "ets_am_access_key` WHERE DATE(datetime_added) < '" . date('Y-m-d') . "'";
        if (Db::getInstance()->execute($sql)) {
            Configuration::updateValue('ETS_AM_DATE_CLEAR_ACCESS_KEY', date('Y-m-d'));
            return true;
        }
        return false;
    }
    public static function checkAccessKey($id_product,$id_seller)
    {
        $ip_address = Tools::getRemoteAddr();
        return Db::getInstance()->getValue("SELECT COUNT(*) FROM `" . _DB_PREFIX_ . "ets_am_access_key` WHERE `id_product` = " . (int)$id_product . " AND `id_seller` = " . (int)$id_seller . " AND `ip_address` = '" . pSQL($ip_address) . "'");
    }
}