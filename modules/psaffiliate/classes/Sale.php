<?php
/**
 * NOTICE OF LICENSE
 *
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * You must not modify, adapt or create derivative works of this source code.
 *
 * @author    Active Design <office@activedesign.ro>
 * @copyright 2016-2018 Active Design
 * @license   LICENSE.txt
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class Sale extends ObjectModel
{
    public $id;
    public $id_sale;
    public $id_order;
    public $id_affiliate;
    public $id_campaign;
    public $approved = 0;
    public $commission = 0;
    public $order_total = 0;
    public $order_total_wt = 0;
    public $date;


    public static $definition = array(
        'table' => 'aff_sales',
        'primary' => 'id_sale',
        'fields' => array(
            'id_sale' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'copy_post' => false),
            'id_order' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'copy_post' => false),
            'id_affiliate' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'copy_post' => false),
            'id_campaign' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'copy_post' => false),
            'approved' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'copy_post' => false),
            'commission' => array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat', 'copy_post' => false),
            'date' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
        ),
    );

    public function toggleApproved()
    {
        $id_sale = (int)Tools::getValue('id_sale');
        if ($id_sale) {
            $sql = Db::getInstance()->execute("UPDATE `"._DB_PREFIX_."aff_sales` SET `approved` = (CASE WHEN `approved`='1' THEN '0' WHEN `approved`='0' THEN '1' END) WHERE `id_sale`='".(int)$id_sale."' LIMIT 1;");

            return $sql;
        }

        return false;
    }

    public static function getAffiliateSales($id_affiliate = false, $limit = false, $reverse = true)
    {
        $sql = "SELECT s.*, (o.`total_products_wt` - o.`total_discounts_tax_incl`) as `order_total`";
        if (!$id_affiliate) {
            $sql .= ', CONCAT("#", af.`id_affiliate`, " - ", IF(af.`id_customer` <> 0, CONCAT(c.`firstname`, " ", c.`lastname`), CONCAT(af.`firstname`, " ", af.`lastname`))) as `affiliate_name`';
        }

        $sql .= " FROM `"._DB_PREFIX_."aff_sales` s LEFT JOIN `"._DB_PREFIX_."orders` o ON (s.`id_order` = o.`id_order`)";
        if ($id_affiliate) {
            $sql .= " WHERE `id_affiliate`='".(int)$id_affiliate."'";
        } else {
            $sql .= ' LEFT JOIN `'._DB_PREFIX_.'aff_affiliates` af on (af.`id_affiliate`=s.`id_affiliate`)
            LEFT JOIN `'._DB_PREFIX_.'customer` c ON (c.`id_customer` = af.`id_customer`)
            ';
        }
        if ($reverse) {
            $sql .= " ORDER BY s.`id_sale` DESC";
        }
        if ($limit) {
            $sql .= " LIMIT ".(int)$limit;
        }

        return Db::getInstance()->executeS($sql);
    }

    public static function getCampaignSales($id_campaign = false, $limit = false, $reverse = true)
    {
        $sql = "SELECT s.*, (o.`total_products_wt` - o.`total_discounts_tax_incl`) as `order_total` FROM `"._DB_PREFIX_."aff_sales` s LEFT JOIN `"._DB_PREFIX_."orders` o ON (s.`id_order` = o.`id_order`)";
        if ($id_campaign) {
            $sql .= " WHERE `id_campaign`='".(int)$id_campaign."'";
        }
        if ($reverse) {
            $sql .= " ORDER BY s.`id_sale` DESC";
        }
        if ($limit) {
            $sql .= " LIMIT ".(int)$limit;
        }

        return Db::getInstance()->executeS($sql);
    }

    public static function getAllOrders($id_order = null)
    {
        $sql = 'SELECT o.`id_order`, CONCAT("#", o.`id_order`, " - ",o.`reference`, " - ", c.`firstname`, " ", c.`lastname`, " - ", c.`email`) name FROM `'._DB_PREFIX_.'orders` o LEFT JOIN `'._DB_PREFIX_.'customer` c ON (o.`id_customer`=c.`id_customer`)';
        if (!is_null($id_order)) {
            $sql .= ' WHERE `id_order` = "'.(int)$id_order.'"';
        }
        $sql .= ' ORDER BY `id_order` DESC';
        $orders = Db::getInstance()->executeS($sql);
        $orders[] = array('name' => '--', 'id_order' => 0);

        return $orders;
    }
}
