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

class Campaign extends ObjectModel
{
    public $id;
    public $id_campaign;
    public $id_affiliate;
    public $name;
    public $description;
    public $date_created;
    public $date_lastactive;
    public $clicks = 0;
    public $unique_clicks = 0;
    public $sales = 0;
    public $sales_total = 0;
    public $total_earnings_clicks = 0;
    public $total_earnings_sales = 0;
    public $link;

    public static $definition = array(
        'table' => 'aff_campaigns',
        'primary' => 'id_campaign',
        'fields' => array(
            'id_campaign' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'copy_post' => false),
            'id_affiliate' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'copy_post' => false),
            'name' => array('type' => self::TYPE_NOTHING, 'validate' => 'isGenericName', 'copy_post' => false),
            'description' => array('type' => self::TYPE_NOTHING, 'validate' => 'isCleanHtml', 'copy_post' => false),
            'date_created' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
            'date_lastactive' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
        ),
    );

    public function __construct($id = null)
    {
        parent::__construct($id);
        if ($id) {
            $this->clicks = Db::getInstance()->getValue('SELECT COUNT(*) FROM `'._DB_PREFIX_.'aff_tracking` WHERE `id_campaign`="'.(int)$id.'" AND `unique_visit`="0"');
            $this->unique_clicks = Db::getInstance()->getValue('SELECT COUNT(*) FROM `'._DB_PREFIX_.'aff_tracking` WHERE `id_campaign`="'.(int)$id.'" AND `unique_visit`="1"');
            $this->sales_total = Db::getInstance()->getValue('SELECT COUNT(*) FROM `'._DB_PREFIX_.'aff_sales` WHERE `id_campaign`="'.(int)$id.'"');
            $this->sales = Db::getInstance()->getValue('SELECT COUNT(*) FROM `'._DB_PREFIX_.'aff_sales` WHERE `id_campaign`="'.(int)$id.'" AND `approved`="1"');
            $this->total_earnings_clicks = number_format(
                (float)Db::getInstance()->getValue('SELECT IFNULL(SUM(`commission`), 0) FROM `'._DB_PREFIX_.'aff_tracking` WHERE `id_campaign`="'.(int)$id.'"'),
                2
            );
            $this->total_earnings_clicks_formatted = Tools::displayPrice($this->total_earnings_clicks);

            $this->total_earnings_sales = number_format(
                (float)Db::getInstance()->getValue('SELECT IFNULL(SUM(`commission`), 0) FROM `'._DB_PREFIX_.'aff_sales` WHERE `id_campaign`="'.(int)$id.'" AND `approved`="1"'),
                2
            );
            $this->total_earnings_sales_formatted = Tools::displayPrice($this->total_earnings_sales);

            $this->total_earnings = number_format($this->total_earnings_clicks + $this->total_earnings_sales, 2);
            $this->total_earnings_formatted = Tools::displayPrice($this->total_earnings);

            $psaffiliate = new PsAffiliate;
            $this->link = $psaffiliate->getAffiliateLink($this->id_affiliate, false, $this->id);
        }
    }

    public static function campaignBelongsToAffiliate($id_campaign, $id_affiliate)
    {
        return (bool)Db::getInstance()->getValue('SELECT COUNT(*) FROM `'._DB_PREFIX_.'aff_campaigns` WHERE `id_campaign`="'.(int)$id_campaign.'" AND `id_affiliate`="'.(int)$id_affiliate.'"');
    }

    public static function setLastActive($id_campaign)
    {
        $update = array();
        $update['date_lastactive'] = date('Y-m-d H:i:s');

        return Db::getInstance()->update('aff_campaigns', $update, '`id_campaign`="'.(int)$id_campaign.'"', 1);
    }

    public function add($auto_date = true, $null_values = false)
    {
        $this->date_created = $this->date_lastactive = date('Y-m-d H:i:s');

        return parent::add($auto_date, $null_values);
    }

    public static function getCampaigns(
        $active = false,
        $limit = false,
        $reverse = false,
        $details = false,
        $sort_by = false
    ) {
        $sql = 'SELECT ca.*, CONCAT("#", a.`id_affiliate`, " - ", IF(a.`id_customer` <> 0, CONCAT(c.`firstname`, " ", c.`lastname`), CONCAT(a.`firstname`, " ", a.`lastname`))) as `idandname` , IF(a.`id_customer` <> 0, c.`email`, a.`email`) as `email`';
        if ($details) {
            $sql .= ', (SELECT COUNT(*) FROM `'._DB_PREFIX_.'aff_tracking` at WHERE at.`id_campaign`=ca.`id_campaign` AND `unique_visit`="0") as `clicks`';
            $sql .= ', (SELECT COUNT(*) FROM `'._DB_PREFIX_.'aff_tracking` at WHERE at.`id_campaign`=ca.`id_campaign` AND `unique_visit`="1") as `unique_clicks`';
            $sql .= ', (SELECT COUNT(*) FROM `'._DB_PREFIX_.'aff_tracking` at WHERE at.`id_campaign`=ca.`id_campaign`) as `total_clicks`';
            $sql .= ', (SELECT COUNT(*) FROM `'._DB_PREFIX_.'aff_sales` asa WHERE asa.`id_campaign`=ca.`id_campaign`) as `sales`';
            $sql .= ', ((SELECT ROUND(SUM(`commission`),2) FROM `'._DB_PREFIX_.'aff_tracking` at WHERE at.`id_campaign`=ca.`id_campaign` AND `commission` > 0) + (SELECT ROUND(SUM(`commission`),2) FROM `'._DB_PREFIX_.'aff_sales` asa WHERE asa.`id_campaign`=ca.`id_campaign` AND `commission` > 0)) as `overall_commission`';
        }
        $sql .= ' FROM `'._DB_PREFIX_.'aff_campaigns` ca';
        $sql .= ' LEFT JOIN `'._DB_PREFIX_.'aff_affiliates` a ON (ca.`id_affiliate` = a.`id_affiliate`)';
        $sql .= ' LEFT JOIN `'._DB_PREFIX_.'customer` c ON (c.`id_customer` = a.`id_customer`)';
        if ($active) {
            $sql .= ' WHERE `active`="1"';
        }
        if ($reverse) {
            $sql .= ' ORDER BY ';
            if (!$sort_by) {
                $sql .= 'a.`id_affiliate`';
            } else {
                $sql .= pSQL($sort_by);
            }
            $sql .= ' DESC';
        }
        if ($limit) {
            $sql .= ' LIMIT '.(int)$limit;
        }

        return Db::getInstance()->executeS($sql);
    }
}
