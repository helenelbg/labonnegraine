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

class Affiliate extends ObjectModel
{
    public $id;
    public $id_affiliate;
    public $id_customer;
    public $email;
    public $password;
    public $firstname;
    public $lastname;
    public $active;
    public $date_created;
    public $date_lastseen;
    public $website;
    public $textarea_registration;
    public $textarea_registration_label;
    public $has_been_reviewed;
    public $clicks = 0;
    public $unique_clicks = 0;
    public $pending_sales = 0;
    public $approved_sales = 0;
    public $clicks_total = 0;
    public $unique_clicks_total = 0;
    public $approved_sales_total = 0;
    public $per_click = 0;
    public $per_unique_click = 0;
    public $per_sale = 0;
    public $per_sale_percent = 0;
    public $edit_customer_link = false;
    public $balance = 0;
    public $payments = 0;
    public $pending_payments;
    public $earnings = 0;
    public $earnings_total;
    public $aff_meta = array();

    protected static $metaTable = 'aff_affiliates_meta';

    public static $definition = array(
        'table' => 'aff_affiliates',
        'primary' => 'id_affiliate',
        'fields' => array(
            'id_affiliate' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'copy_post' => false),
            'id_customer' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'copy_post' => false),
            'firstname' => array('type' => self::TYPE_STRING, 'validate' => 'isName', 'size' => 32),
            'lastname' => array('type' => self::TYPE_STRING, 'validate' => 'isName', 'size' => 32),
            'email' => array('type' => self::TYPE_STRING, 'validate' => 'isEmail', 'size' => 128),
            'password' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 32),
            'active' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'copy_post' => false),
            'date_created' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
            'date_lastseen' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
            'website' => array('type' => self::TYPE_STRING, 'validate' => 'isUrl', 'copy_post' => false),
            'textarea_registration' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
                'copy_post' => false,
            ),
            'textarea_registration_label' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
                'copy_post' => false,
            ),
            'has_been_reviewed' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'copy_post' => false),
        ),
    );

    public function __construct($id = null)
    {
        $this->moduleObj = Module::getInstanceByName('psaffiliate');
        $this->moduleObj->loadClasses('AffConf');
        parent::__construct($id);
        $days_current_summary = AffConf::getConfig('days_current_summary');
        if ($id) {
            $this->clicks = (int)Db::getInstance()->getValue('SELECT COUNT(*) FROM `'._DB_PREFIX_.'aff_tracking` WHERE `id_affiliate`="'.(int)$id.'" AND `unique_visit`="0" AND `date` >= DATE_SUB(NOW(), INTERVAL '.(int)$days_current_summary.' DAY)');
            $this->unique_clicks = (int)Db::getInstance()->getValue('SELECT COUNT(*) FROM `'._DB_PREFIX_.'aff_tracking` WHERE `id_affiliate`="'.(int)$id.'" AND `unique_visit`="1" AND `date` >= DATE_SUB(NOW(), INTERVAL '.(int)$days_current_summary.' DAY)');
            $this->pending_sales = (int)Db::getInstance()->getValue('SELECT COUNT(*) FROM `'._DB_PREFIX_.'aff_sales` WHERE `id_affiliate`="'.(int)$id.'" AND `approved`="0" AND `date` >= DATE_SUB(NOW(), INTERVAL '.(int)$days_current_summary.' DAY)');
            $this->approved_sales = (int)Db::getInstance()->getValue('SELECT COUNT(*) FROM `'._DB_PREFIX_.'aff_sales` WHERE `id_affiliate`="'.(int)$id.'" AND `approved`="1" AND `date` >= DATE_SUB(NOW(), INTERVAL '.(int)$days_current_summary.' DAY)');
            $this->clicks_total = (int)Db::getInstance()->getValue('SELECT COUNT(*) FROM `'._DB_PREFIX_.'aff_tracking` WHERE `id_affiliate`="'.(int)$id.'" AND `unique_visit`="0"');
            $this->unique_clicks_total = (int)Db::getInstance()->getValue('SELECT COUNT(*) FROM `'._DB_PREFIX_.'aff_tracking` WHERE `id_affiliate`="'.(int)$id.'" AND `unique_visit`="1"');
            $this->approved_sales_total = (int)Db::getInstance()->getValue('SELECT COUNT(*) FROM `'._DB_PREFIX_.'aff_sales` WHERE `id_affiliate`="'.(int)$id.'" AND `approved`="1"');
            $context = Context::getContext();
            if ($this->id_customer && isset($context->employee)) {
                $customer = new Customer((int)$this->id_customer);
                if (Validate::isLoadedObject($customer)) {
                    $this->firstname = $customer->firstname;
                    $this->lastname = $customer->lastname;
                    $this->email = $customer->email;
                    $this->edit_customer_link = Context::getContext()->link->getAdminLink('AdminCustomers', false)
                        .'&id_customer='.$this->id_customer.'&updatecustomer&token='.Tools::getAdminTokenLite('AdminCustomers');
                }
            }
            $this->earnings = $this->getEarnings(true);
            $this->earnings_formatted = Tools::displayPrice($this->earnings);
            $this->earnings_total = $this->getEarnings(true, true);
            $this->earnings_total_formatted = Tools::displayPrice($this->earnings_total);
            $this->payments = $this->getPayments();
            $this->payments_formatted = Tools::displayPrice($this->payments);
            $this->pending_payments = $this->getPendingPayments();
            $this->pending_payments_formatted = Tools::displayPrice($this->pending_payments);
            $this->balance = (float)$this->earnings_total - (float)$this->payments - (float)$this->pending_payments;
            $this->balance_formatted = Tools::displayPrice($this->balance);
        }
        $this->getRates();
        $this->getMetas();
    }

    public function toggleStatus()
    {
        $this->active = !$this->active;
        $this->has_been_reviewed = 1;
        return $this->save();
    }

    public function delete()
    {
        $id_affiliate = Tools::getValue('id_affiliate', $this->id);
        $delete = false;
        if ($id_affiliate) {
            $delete = Db::getInstance()->delete('aff_affiliates', '`id_affiliate`="'.(int)$id_affiliate.'"', 1);
        }
        if ($delete) {
            $tables_to_delete_from = array('aff_payments', 'aff_sales', 'aff_tracking', 'aff_commission');
            foreach ($tables_to_delete_from as $tbl) {
                if (!Db::getInstance()->delete($tbl, 'id_affiliate="'.(int)$id_affiliate.'"')) {
                    return false;
                }
            }

            // Delete meta data.
            if (!$this->deleteMeta()) {
                return false;
            }
        }

        return $delete;
    }

    public function setFieldsToUpdate(?array $fields)
    {
        $params = false;
        $return = false;
        if (Tools::getValue('submitBulkenableSelectionaff_affiliates') !== false) {
            $params = array('active' => true);
        } elseif (Tools::getValue('submitBulkdisableSelectionaff_affiliates') !== false) {
            $params = array('active' => false);
        }
        if ($params && is_array($params) && sizeof($params)) {
            if (Tools::getValue('aff_affiliatesBox')) {
                $selected_affiliates = implode(",", Tools::getValue('aff_affiliatesBox'));
                $return = Db::getInstance()->update(
                    'aff_affiliates',
                    $params,
                    '`id_affiliate` IN ('.pSQL($selected_affiliates).')'
                );
            }
        }

        return $return;
    }

    public function add($auto_date = true, $null_values = false)
    {
        $this->date_created = $this->date_lastseen = date('Y-m-d H:i:s');
        parent::add($auto_date, $null_values);
        $context = Context::getContext();
        if (isset($context->controller->controller_name)) {
            $controller_name = $context->controller->controller_name;
        }
        if (isset($controller_name) && Tools::substr(Tools::strtolower($controller_name), 0, 5) != "admin") {
            $rates = Db::getInstance()->executeS("SELECT `type`, `value` FROM `"._DB_PREFIX_."aff_commission` WHERE `id_affiliate`='0' GROUP BY `type` ORDER BY `date` DESC");
            if ($rates) {
                foreach ($rates as $rate) {
                    $_POST["per_".$rate['type']] = (float)$rate['value'];
                }
            }
        }
        $this->id_affiliate = $this->id;

        return $this->update($null_values);
    }

    public function update($null_values = false)
    {
        $db = Db::getInstance();
        $aff = new Affiliate($this->id);
        if (Tools::getValue('password')) {
            $this->password = Tools::encrypt(Tools::getValue('password'));
        } else {
            $this->password = $aff->password;
        }
        $aff_commission = array();
        $rates_to_check = array(
            'per_click' => 'click',
            'per_unique_click' => 'unique_click',
            'per_sale' => 'sale',
            'per_sale_percent' => 'sale_percent',
        );
        $rates = Db::getInstance()->executeS("SELECT `type`, `value` FROM `"._DB_PREFIX_."aff_commission` WHERE `id_affiliate`='0' GROUP BY `type` ORDER BY `date` DESC");
        if ($rates) {
            foreach ($rates as $rate) {
                $aff_commission[$rate['type']] = (float)$rate['value'];
            }
        }
        foreach ($rates_to_check as $rate => $rate_name) {
            if (Tools::getValue($rate) !== false) {
                $aff_commission[$rate_name] = Tools::getValue($rate);
            }
        }
        if (sizeof($aff_commission)) {
            foreach ($aff_commission as $type => $value) {
                $insert = array();
                $insert['id_affiliate'] = $this->id;
                $insert['date'] = date('Y-m-d H:i:s');
                $insert['type'] = pSQL($type);
                $insert['value'] = (float)$value;
                $db->insert('aff_commission', $insert);
            }
        }
        if ($this->date_lastseen == "0000-00-00 00:00:00") {
            $this->date_lastseen = $this->date_created;
        }
        $metas = $this->getMetas();
        foreach ($metas as $meta) {
            $val = Tools::getValue('custom_field_'.$meta['id_field']);
            $old_val = Db::getInstance()->getValue('SELECT `value` FROM `'._DB_PREFIX_.'aff_affiliates_meta` WHERE `id_affiliate` = "'.(int)$this->id.'" AND `key` = "custom_field_'.(int)$meta['id_field'].'"');
            if($val !== $old_val) {
                Db::getInstance()->delete('aff_affiliates_meta', '`id_affiliate` = "'.(int)$this->id.'" AND `key` = "custom_field_'.(int)$meta['id_field'].'"');
                Db::getInstance()->insert('aff_affiliates_meta', array(
                    'id_affiliate' => (int)$this->id,
                    'key' => pSQL('custom_field_'.$meta['id_field']),
                    'value' => pSQL($val),
                ), false, true, Db::REPLACE);
            }
        }

        if (!$aff->has_been_reviewed && $this->has_been_reviewed && !$aff->active && $this->active)
        {
            $customer = new Customer($this->id_customer);
            $iso = Language::getIsoById((int)$customer->id_lang);
            $dir_mail = false;

            if (file_exists($this->moduleObj->getPathDir().'/mails/'.$iso.'/affiliate_approved.txt') &&
                file_exists($this->moduleObj->getPathDir().'/mails/'.$iso.'/affiliate_approved.html')) {
                $dir_mail = $this->moduleObj->getPathDir().'/mails/';
            }

            if (file_exists(_PS_MAIL_DIR_.$iso.'/affiliate_approved.txt') &&
                file_exists(_PS_MAIL_DIR_.$iso.'/affiliate_approved.html')) {
                $dir_mail = _PS_MAIL_DIR_;
            }

            if (!$dir_mail) {
                $iso = 'en';
                if (file_exists($this->moduleObj->getPathDir().'/mails/'.$iso.'/affiliate_approved.txt') &&
                    file_exists($this->moduleObj->getPathDir().'/mails/'.$iso.'/affiliate_approved.html')) {
                    $dir_mail = $this->moduleObj->getPathDir().'/mails/';
                }

                if (file_exists(_PS_MAIL_DIR_.$iso.'/affiliate_approved.txt') &&
                    file_exists(_PS_MAIL_DIR_.$iso.'/affiliate_approved.html')) {
                    $dir_mail = _PS_MAIL_DIR_;
                }
            }

            $mail_id_lang = (int)$customer->id_lang;
            if ($dir_mail) {
                $template_vars = array(
                    '{name}' => $this->firstname." ".$this->lastname,
                );
                Mail::Send(
                    $mail_id_lang,
                    'affiliate_approved',
                    Mail::l('Your affiliate request has been approved', $mail_id_lang),
                    $template_vars,
                    $aff->email,
                    null,
                    Configuration::get('PS_SHOP_EMAIL'),
                    Configuration::get('PS_SHOP_NAME'),
                    null,
                    null,
                    $dir_mail,
                    null,
                    (int)$customer->id_shop
                );
            }
        }

        return parent::update($null_values);
    }

    public function getRates($type = false)
    {
        $db = Db::getInstance();
        if (!$type) {
            $type = array('click', 'unique_click', 'sale', 'sale_percent');
        } elseif (is_string($type)) {
            $type[0] = $type;
        }
        $sql_array = array();
        $sql = '
        SELECT ';
        foreach ($type as $t) {
            $sql_array[] = ' ROUND(IFNULL((SELECT cm.`value` FROM `'._DB_PREFIX_.'aff_commission` cm WHERE cm.`type`="'.pSQL($t).'" AND cm.`id_affiliate`="'.(int)$this->id.'" ORDER BY `date` DESC LIMIT 1), (SELECT cm.`value` FROM `'._DB_PREFIX_.'aff_commission` cm WHERE cm.`type`="'.pSQL($t).'" AND cm.`id_affiliate`="0" ORDER BY `date` DESC LIMIT 1)), 2) as `per_'.pSQL($t).'`';
        }
        $sql = $sql.implode(",", $sql_array);
        $rows = $db->getRow($sql);
        foreach ($rows as $k => &$r) {
            $r = (float)$r;
            $this->$k = (float)$r;
        }

        return $rows;
    }

    public static function getRatesHistory($id_affiliate = false, $limit = false, $reverse = true)
    {
        $sql = "SELECT * FROM `"._DB_PREFIX_."aff_commission` WHERE `id_affiliate`='".(int)$id_affiliate."'";
        if ($reverse) {
            $sql .= " ORDER BY `id_commission` DESC";
        }
        if ($limit) {
            $sql .= " LIMIT ".(int)$limit;
        }
        $return = Db::getInstance()->executeS($sql);
        if (!count($return) && $id_affiliate) {
            $return = self::getRatesHistory(0, $limit, $reverse);
        }

        return $return;
    }

    public function getEarnings($total = false, $overall = false)
    {
        $days_current_summary = AffConf::getConfig('days_current_summary');
        $db = Db::getInstance();
        $return = array();

        $sql_clicks = "SELECT ROUND(SUM(`commission`),2) FROM `"._DB_PREFIX_."aff_tracking` WHERE `id_affiliate`='".(int)$this->id."' AND `commission` > 0 AND `unique_visit`='0'";
        if (!$overall) {
            $sql_clicks .= " AND `date` >= DATE_SUB(NOW(), INTERVAL ".(int)$days_current_summary." DAY)";
        }
        $return['clicks'] = (float)$db->getValue($sql_clicks);

        $sql_unique_clicks = "SELECT ROUND(SUM(`commission`),2) FROM `"._DB_PREFIX_."aff_tracking` WHERE `id_affiliate`='".(int)$this->id."' AND `commission` > 0 AND `unique_visit`='1'";
        if (!$overall) {
            $sql_unique_clicks .= " AND `date` >= DATE_SUB(NOW(), INTERVAL ".(int)$days_current_summary." DAY)";
        }
        $return['unique_clicks'] = (float)$db->getValue($sql_unique_clicks);

        $sql_sales = "SELECT ROUND(SUM(`commission`), 2) FROM `"._DB_PREFIX_."aff_sales` WHERE `id_affiliate`='".(int)$this->id."' AND `commission` > 0 AND `approved`='1'";
        if (!$overall) {
            $sql_sales .= " AND `date` >= DATE_SUB(NOW(), INTERVAL ".(int)$days_current_summary." DAY)";
        }
        $return['sales'] = (float)$db->getValue($sql_sales);
        if (!$total) {
            return $return;
        } else {
            $total = 0;
            foreach ($return as $r) {
                $total += $r;
            }

            return $total;
        }
    }

    public static function getCustomersList($only_active = false, $id_customer = null)
    {
        $sql = 'SELECT `id_customer`, `email`, `firstname`, `lastname`, CONCAT(`firstname`, " ", `lastname`) as `name`, CONCAT("#", `id_customer`, " - ", `firstname`, " ", `lastname`, " - ", `email`) as `idandname`
                FROM `'._DB_PREFIX_.'customer`
                WHERE 1 '.Shop::addSqlRestriction(Shop::SHARE_CUSTOMER).
            ($only_active ? ' AND `active` = 1' : '').
            (!is_null($id_customer) ? ' AND `id_customer` = "'.(int)$id_customer.'"' : '').'
                ORDER BY `id_customer` ASC';

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    }

    public static function getAffiliates(
        $active = false,
        $limit = false,
        $reverse = false,
        $details = false,
        $sort_by = false,
        $id_affiliate = null
    ) {
        $sql = 'SELECT a.`id_affiliate`, a.`id_customer`, a.`active`, IF(a.`id_customer` <> 0, CONCAT(c.`firstname`, " ", c.`lastname`), CONCAT(a.`firstname`, " ", a.`lastname`)) as `name`, CONCAT("#", a.`id_affiliate`, " - ", IF(a.`id_customer` <> 0, CONCAT(c.`firstname`, " ", c.`lastname`, " - ", c.`email`), CONCAT(a.`firstname`, " ", a.`lastname`, " - ", c.`email`))) as `idandname` , IF(a.`id_customer` <> 0, c.`email`, a.`email`) as `email`';
        if ($details) {
            $sql .= ', (SELECT COUNT(*) FROM `'._DB_PREFIX_.'aff_tracking` at WHERE at.`id_affiliate`=a.`id_affiliate` AND `unique_visit`="0") as `clicks`';
            $sql .= ', (SELECT COUNT(*) FROM `'._DB_PREFIX_.'aff_tracking` at WHERE at.`id_affiliate`=a.`id_affiliate` AND `unique_visit`="1") as `unique_clicks`';
            $sql .= ', (SELECT COUNT(*) FROM `'._DB_PREFIX_.'aff_tracking` at WHERE at.`id_affiliate`=a.`id_affiliate`) as `total_clicks`';
            $sql .= ', (SELECT COUNT(*) FROM `'._DB_PREFIX_.'aff_sales` asa WHERE asa.`id_affiliate`=a.`id_affiliate`) as `sales`';
            $sql .= ', ((SELECT ROUND(SUM(`commission`),2) FROM `'._DB_PREFIX_.'aff_tracking` at WHERE at.`id_affiliate`=a.`id_affiliate` AND `commission` > 0) + (SELECT ROUND(SUM(`commission`),2) FROM `'._DB_PREFIX_.'aff_sales` asa WHERE asa.`id_affiliate`=a.`id_affiliate` AND `commission` > 0)) as `overall_commission`';
        }
        $sql .= ' FROM `'._DB_PREFIX_.'aff_affiliates` a';
        $sql .= ' LEFT JOIN `'._DB_PREFIX_.'customer` c ON (c.`id_customer` = a.`id_customer`)';
        $sql .= ' WHERE 1 ';
        if ($active) {
            $sql .= ' AND `active`="1"';
        }
        if (!is_null($id_affiliate)) {
            $sql .= ' AND a.`id_affiliate` = "'.(int)$id_affiliate.'" ';
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

    public static function getAffiliateList($active = false)
    {
        $sql = 'SELECT a.`id_affiliate`, IF(a.`id_customer` <> 0, CONCAT(c.`firstname`, " ", c.`lastname`), CONCAT(a.`firstname`, " ", a.`lastname`)) as `name` FROM `'._DB_PREFIX_.'aff_affiliates` a ';
        if ($active) {
            $sql .= 'WHERE `active`="1" ';
        }
        $sql .= 'LEFT JOIN `'._DB_PREFIX_.'customer` c ON (c.`id_customer` = a.`id_customer`)';
        $list = Db::getInstance()->executeS($sql);

        $array = array();

        foreach ($list as $el) {
            $array[(int)$el['id_affiliate']] = $el['name'];
        }

        return $array;
    }

    public function getPayments()
    {
        if ($this->id) {
            $sql = "SELECT IFNULL(SUM(`amount`), 0) as `amount` FROM `"._DB_PREFIX_."aff_payments` WHERE `id_affiliate`='".(int)$this->id."' AND `paid`='1'";

            return Db::getInstance()->getValue($sql);
        }

        return false;
    }

    public function getPendingPayments()
    {
        if ($this->id) {
            $sql = "SELECT IFNULL(SUM(`amount`), 0) as `amount` FROM `"._DB_PREFIX_."aff_payments` WHERE `id_affiliate`='".(int)$this->id."' AND `paid`='0'";

            return Db::getInstance()->getValue($sql);
        }

        return false;
    }

    public function getCampaigns($with_links = false)
    {
        if ($this->id) {
            $sql = 'SELECT *, (SELECT COUNT(*) FROM `'._DB_PREFIX_.'aff_tracking` WHERE `id_campaign`=c.`id_campaign`) as `clicks`, CONCAT((SELECT COUNT(*) FROM `'._DB_PREFIX_.'aff_sales` WHERE `id_campaign`=c.`id_campaign` AND `approved`="1"), "/", (SELECT COUNT(*) FROM `'._DB_PREFIX_.'aff_sales` WHERE `id_campaign`=c.`id_campaign`)) as `sales`, (SELECT IFNULL(SUM(`commission`), 0) FROM `'._DB_PREFIX_.'aff_tracking` WHERE `id_campaign`=c.`id_campaign`) as `total_earnings_clicks`, (SELECT IFNULL(SUM(`commission`), 0) FROM `'._DB_PREFIX_.'aff_sales` WHERE `id_campaign`=c.`id_campaign` AND `approved`="1") as `total_earnings_sales`, ((SELECT IFNULL(SUM(`commission`), 0) FROM `'._DB_PREFIX_.'aff_sales` WHERE `id_campaign`=c.`id_campaign` AND `approved`="1")+(SELECT IFNULL(SUM(`commission`), 0) FROM `'._DB_PREFIX_.'aff_tracking` WHERE `id_campaign`=c.`id_campaign`)) as `total_earnings` FROM `'._DB_PREFIX_.'aff_campaigns` c WHERE `id_affiliate`="'.(int)$this->id.'" ;';
            $return = Db::getInstance()->executeS($sql);
            if ($with_links) {
                foreach ($return as &$r) {
                    $r['campaign_link'] = $this->moduleObj->getAffiliateLink($this->id, false, $r['id_campaign']);
                }
            }

            return $return;
        }

        return false;
    }

    public function getVouchers()
    {
        if (!$this->id) {
            return array();
        }

        $pr = _DB_PREFIX_;

        $voucherIds = Db::getInstance()->executeS("
            SELECT `id_voucher` FROM `{$pr}aff_payments`
            WHERE `id_voucher` != 0
            AND `id_affiliate` = ".(int)$this->id."
        ");

        if (!$voucherIds) {
            return array();
        }

        $voucherIds_array = array();
        foreach ($voucherIds as $row) {
            $voucherIds_array[] = $row['id_voucher'];
        }


        $voucherIds = array_map('intval', $voucherIds_array);

        $vouchers = (new PrestaShopCollection('CartRule', (int)Context::getContext()->language->id))
            ->where('id_cart_rule', 'in', $voucherIds)
            ->orderBy('date_add', 'desc')
            ->getAll()->getResults();

        return $vouchers ?: array();
    }

    public function getMetaQuery()
    {
        return (new DbQuery())
            ->from(static::$metaTable)
            ->where('`id_affiliate` = '.(int)$this->id);
    }

    public function getMetaWhere($key = null)
    {
        $where = '`id_affiliate` = '.(int)$this->id;

        if ($key === null) {
            return $where;
        }

        return $where.' and '.static::getMetaWhereByKey($key);
    }

    public function getMeta($key = null)
    {
        // If no key, get all metas.
        if ($key === null) {
            return Db::getInstance()->executeS($this->getMetaQuery());
        }

        // Get single meta
        $row = Db::getInstance()->getRow(
            $this->getMetaQuery()->where('`key` = "'.pSQL($key).'"')
        );

        if ($row) {
            return $row;
        }
        return null;
    }

    public function hasMeta($key)
    {
        return (bool)$this->getMeta($key);
    }

    /**
     * Save meta data / multiple meta data.
     *
     * @param  string|array $key
     * @param  mixed $value
     * @return bool
     */
    public function saveMeta($key, $value = null)
    {
        // if single meta data
        if (func_num_args() === 2) {
            return $this->saveMetaSingle($key, $value);
        }

        // if multiple meta data
        return $this->saveMetaMultiple($key);
    }

    protected function saveMetaSingle($key, $value)
    {
        if (empty($value)) {
            $value = null;
        }

        // Update existing meta
        if ($this->hasMeta($key)) {
            return (bool)Db::getInstance()->update(static::$metaTable, compact('value'), $this->getMetaWhere($key));
        }

        // Create new meta
        return (bool)Db::getInstance()->insert(
            static::$metaTable,
            array('id_affiliate' => (int)$this->id, 'key' => $key, 'value' => $value)
        );
    }

    protected function saveMetaMultiple($data)
    {
        if (!is_array($data) || empty($data)) {
            return false;
        }

        foreach ($data as $key => $value) {
            if (!$this->saveMetaSingle($key, $value)) {
                return false;
            }
        }

        return true;
    }

    public function deleteMeta($key = null)
    {
        return (bool)Db::getInstance()->delete(
            static::$metaTable,
            $this->getMetaWhere($key)
        );
    }

    public static function deleteMetaByKey($key)
    {
        return (bool)Db::getInstance()->delete(
            static::$metaTable,
            static::getMetaWhereByKey($key)
        );
    }

    protected static function getMetaWhereByKey($key)
    {
        if (is_array($key)) {
            $keys = array_map(function ($k) {
                return '"'.pSQL($k).'"';
            }, $key);

            return ' `key` in('.implode(',', $keys).')';
        }

        return ' `key` = "'.pSQL($key).'"';
    }

    public static function getMetaTable()
    {
        return static::$metaTable;
    }

    public function getMetas()
    {
        $context = Context::getContext();
        $return = Db::getInstance()->executeS('
            SELECT cf.`id_field`, cfl.`name`, am.`id_affiliate`, am.`value`
            FROM `'._DB_PREFIX_.'aff_custom_fields` cf
            LEFT JOIN `'._DB_PREFIX_.'aff_custom_fields_lang` cfl
              ON (cf.`id_field` = cfl.`id_field` AND cfl.`id_lang` = "'.(int)$context->language->id.'")
            LEFT JOIN `'._DB_PREFIX_.'aff_affiliates_meta` am
              ON (am.`key` = CONCAT("custom_field_", cf.`id_field`))
            WHERE am.`id_affiliate` = "'.(int)$this->id.'"');

        $return_array = array();
        foreach($return as $row) {
            $return_array[$row['id_field']] = $row;
        }

        $all_metas = Db::getInstance()->executeS('
            SELECT cf.`id_field`, cfl.`name`
            FROM `'._DB_PREFIX_.'aff_custom_fields` cf
            LEFT JOIN `'._DB_PREFIX_.'aff_custom_fields_lang` cfl
              ON (cf.`id_field` = cfl.`id_field` AND cfl.`id_lang` = "'.(int)$context->language->id.'")');
        
        foreach($all_metas as $meta) {
            if(!isset($return_array[$meta['id_field']])) {
                $return_array[$meta['id_field']] = array(
                    'id_field' => $meta['id_field'],
                    'name' => $meta['name'],
                    'id_affiliate' => $this->id,
                    'value' => "",
                );
            }
        }

        $this->aff_meta = $return_array;

        return $return_array;
    }
}
