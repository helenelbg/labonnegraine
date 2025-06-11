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

if (!defined('_PS_VERSION_')) { exit; }
class Ets_paymentmethod_class extends ObjectModel
{
    public $id_ets_paymentmethod;
    public $id_shop;
    public $fee_type;
    public $order_status;
    public $logo_payment;
    public $active;
    public $customer_group;
    public $countries;
	public $carriers;
	public $method_name;
    public $description;
    public $confirmation_message;
    public $return_message;
    public $fee_amount;
    public $minimum_order;
    public $maximum_order;
    public $percentage;
    public $max_fee;
    public $min_fee;
    public $position;
    public $free_for_order_over;
    public $fee_based_on;
    public $id_tax_rules_group;
    public static $definition = array(
		'table' => 'ets_paymentmethod',
		'primary' => 'id_ets_paymentmethod',
		'multilang' => true,
		'fields' => array(
            'id_shop' => array('type' => self::TYPE_INT),
			'active' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true),
            'fee_type' => array('type' => self::TYPE_STRING), 
            'order_status' => array('type'=>self::TYPE_INT),
            'logo_payment' =>array('type'=>self::TYPE_STRING),
            'customer_group' => array('type' => self::TYPE_STRING),           
            'countries' => array('type' => self::TYPE_STRING),
            'carriers' => array('type' => self::TYPE_STRING),
            'fee_amount' =>array('type'=>self::TYPE_STRING),
            'minimum_order' =>array('type'=>self::TYPE_STRING),
            'maximum_order' =>array('type'=>self::TYPE_STRING),
            'percentage' =>array('type'=>self::TYPE_STRING),
            'max_fee' =>array('type'=>self::TYPE_STRING),
            'min_fee' =>array('type'=>self::TYPE_STRING),
            'position'=>array('type'=>self::TYPE_INT),
            'fee_based_on'=>array('type'=>self::TYPE_INT),
            'id_tax_rules_group'=>array('type'=>self::TYPE_INT),
            'free_for_order_over' =>array('type'=>self::TYPE_STRING),
            'method_name' => array('type' => self::TYPE_STRING,'lang'=>true),
            'description' => array('type' => self::TYPE_STRING,'lang'=>true),
            'confirmation_message' => array('type' => self::TYPE_STRING,'lang'=>true),
            'return_message' => array('type' => self::TYPE_HTML,'lang'=>true),
        )
	);
    public	function __construct($id_item = null, $id_lang = null, $id_shop = null)
	{
		parent::__construct($id_item, $id_lang, $id_shop);
	}
    public static function checkExists($id_paymentmethod)
    {
        return (int)Db::getInstance()->getValue('SELECT id_ets_paymentmethod FROM `'._DB_PREFIX_.'ets_paymentmethod` WHERE id_ets_paymentmethod="'.(int)$id_paymentmethod.'" AND (id_shop=0 OR id_shop="'.(int)Context::getContext()->shop->id.'")');
    }
    public static function getMaxPosition()
    {
        return Db::getInstance()->getValue('select MAX(position) FROM `' . _DB_PREFIX_ . 'ets_paymentmethod` WHERE id_shop=0 OR id_shop="'.(int)Context::getContext()->shop->id.'"');
    }
    public static function getPaymentMethodByIdModule($id_module)
    {
        $cache_key ='Ets_paymentmethod_class::getPaymentMethodByIdModule_'.$id_module;
        if(!Cache::isStored($cache_key))
        {
            $payment_method = Db::getInstance()->getRow('SELECT * FROM `' . _DB_PREFIX_ . 'ets_paymentmethod_module` WHERE id_module=' . (int)$id_module.' AND id_shop='.(int)Context::getContext()->shop->id);
            if(!$payment_method)
                $payment_method = Db::getInstance()->getRow('SELECT * FROM `' . _DB_PREFIX_ . 'ets_paymentmethod_module` WHERE id_module=' . (int)$id_module);
            Cache::store($cache_key,$payment_method);
        }
        else
            $payment_method = Cache::retrieve($cache_key);
        return $payment_method;
    }
    public static function getPaymentMethodByIdMethod($id_paymentmethod)
    {
       return Db::getInstance()->getRow('SELECT * FROM `' . _DB_PREFIX_ . 'ets_paymentmethod` WHERE id_ets_paymentmethod=' . (int)$id_paymentmethod.' AND (id_shop=0 OR id_shop="'.(int)Context::getContext()->shop->id.'")');
    }
    public static function addFeeToModule($id_module,$fee_type,$fee_amount,$free_for_order_over,$percentage,$max_fee,$min_fee,$minimum_order,$maximum_order,$fee_based_on,$id_tax_rules_group)
    {
        if(Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'ets_paymentmethod_module` WHERE id_module='.(int)$id_module.' AND id_shop="'.(int)Context::getContext()->shop->id.'"'))
        {
            $sql = 'UPDATE `'._DB_PREFIX_.'ets_paymentmethod_module` SET fee_type="'.pSQL($fee_type).'",fee_amount='.((float)$fee_amount ? : 'NULL').',free_for_order_over='.((float)$free_for_order_over ? : 'NULL').',percentage='.((float)$percentage ? : 'NULL').',max_fee = '.((float)$max_fee ? : 'NULL').',min_fee='.((float)$min_fee ? : 'NULL').',minimum_order='.((float)$minimum_order ? :'NULL').',maximum_order='.((float)$maximum_order ? :'NULL').',fee_based_on="'.(int)$fee_based_on.'",id_tax_rules_group="'.(int)$id_tax_rules_group.'" WHERE id_module='.(int)$id_module.' AND id_shop='.(int)Context::getContext()->shop->id;
        }
        else
            $sql ='INSERT INTO `'._DB_PREFIX_.'ets_paymentmethod_module` SET id_shop="'.(int)Context::getContext()->shop->id.'", id_module="'.(int)$id_module.'", fee_type="'.pSQL($fee_type).'",fee_amount='.((float)$fee_amount ? (float)$fee_amount : 'NULL').',free_for_order_over='.((float)$free_for_order_over ? (float)$free_for_order_over : 'NULL').',minimum_order='.((float)$minimum_order ? :'NULL').',maximum_order='.((float)$maximum_order ? :'NULL').',percentage='.((float)$percentage ?:'NULL').',max_fee = '.((float)$max_fee ? : 'NULL').',fee_based_on="'.(int)$fee_based_on.'",id_tax_rules_group="'.(int)$id_tax_rules_group.'",min_fee='.((float)$min_fee ? : 'NULL');
        return Db::getInstance()->execute($sql);
    }
    public static function installDb()
    {
        return Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ets_paymentmethod`( 
            `id_ets_paymentmethod` INT(11) NOT NULL AUTO_INCREMENT , 
            `id_shop` INT(11) NOT NULL ,
            `logo_payment` VARCHAR(222) NULL ,
            `fee_type` VARCHAR(33) NULL ,
            `order_status` INT(11),
            `active` INT(1) NOT NULL ,
            `customer_group` VARCHAR(222) NULL , 
            `countries` VARCHAR(222) NULL , 
            `carriers` VARCHAR(222) NULL , 
            `fee_amount` FLOAT(10,2) NULL ,
            `minimum_order` FLOAT(10,2) NULL , 
            `maximum_order` FLOAT(10,2) NULL , 
            `percentage` FLOAT(10,2) NULL , 
            `max_fee` FLOAT(10,2) NULL , 
            `min_fee` FLOAT(10,2) NULL , 
            `position` INT(11),
            `free_for_order_over` FLOAT(10,2) NULL,
            `id_tax_rules_group` INT(11) NULL , 
            `fee_based_on` INT(11) NULL , 
        PRIMARY KEY (`id_ets_paymentmethod`)) ENGINE = InnoDB')
        && Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ets_paymentmethod_lang` (
            `id_ets_paymentmethod` INT(11) NOT NULL ,
            `id_lang` INT(11) NOT NULL , 
            `method_name` VARCHAR(222) NULL , 
            `description` TEXT NULL , 
            `confirmation_message` TEXT NULL , 
            `return_message` TEXT NULL ,
        PRIMARY KEY (`id_ets_paymentmethod`, `id_lang`)) ENGINE = InnoDB')
        && Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ets_paymentmethod_order` (
            `id_ets_paymentmethod_order` INT(11) NOT NULL AUTO_INCREMENT ,
            `id_paymentmethod` INT(11) NULL , 
            `id_order` INT (11) NULL,
            `method_name` VARCHAR(222) NULL , 
            `fee` FLOAT(10,2) NOT NULL , 
            `fee_incl` FLOAT(10,2) NOT NULL ,
        PRIMARY KEY (`id_ets_paymentmethod_order`)) ENGINE = InnoDB')
        && Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ets_paymentmethod_module` ( 
            `id_module` INT(11) NOT NULL , 
            `id_shop` INT(11) NOT NULL ,
            `fee_type` VARCHAR(33) NULL , 
            `fee_amount` FLOAT(10,2) NULL , 
            `percentage` FLOAT(10,2) NULL , 
            `max_fee` FLOAT(10,2) NULL , 
            `min_fee` FLOAT(10,2) NULL , 
            `free_for_order_over` FLOAT(10,2) NULL ,
            `minimum_order` FLOAT(10,2) NULL , 
            `maximum_order` FLOAT(10,2) NULL , 
            `id_tax_rules_group` INT(11) NULL , 
            `fee_based_on` INT(11) NULL , 
         PRIMARY KEY (`id_module`,`id_shop`)) ENGINE = InnoDB')
        && Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ets_payment_cart` ( 
            `id_cart` INT(11) NOT NULL, 
            `ets_payment_module_name` VARCHAR(50) NOT NULL, 
            `id_payment_method` INT(11) NOT NULL, 
            `payment_option` VARCHAR(50) NOT NULL,
             PRIMARY KEY (`id_cart`)) ENGINE = InnoDB');
    }
    public static function unInstallDb()
    {
        return Db::getInstance()->execute('DROP TABLE IF EXISTS ' . _DB_PREFIX_ . 'ets_paymentmethod') && Db::getInstance()->execute('DROP TABLE IF EXISTS ' . _DB_PREFIX_ . 'ets_paymentmethod_lang') && Db::getInstance()->execute('DROP TABLE IF EXISTS ' . _DB_PREFIX_ . 'ets_paymentmethod_module');
    }
    public static function getPayments($filter='',$sort='',$sort_type='')
    {
        $sql = 'SELECT p.*,pl.method_name,pl.description, trg.name as fee_tax
                FROM `' . _DB_PREFIX_ . 'ets_paymentmethod` p
                LEFT JOIN `'._DB_PREFIX_.'ets_paymentmethod_lang` pl ON (p.id_ets_paymentmethod=pl.id_ets_paymentmethod AND pl.id_lang="' . (int)Context::getContext()->language->id . '")
                LEFT JOIN `'._DB_PREFIX_.'tax_rules_group` trg ON p.id_tax_rules_group=trg.id_tax_rules_group
                WHERE (p.id_shop="'.(int)Context::getContext()->shop->id.'" or p.id_shop=0) ' . ($filter ? $filter : '') . ' ORDER BY ' . pSQl($sort) . ' ' . pSQL($sort_type);
        return Db::getInstance()->executeS($sql);
    }
    public static function getPaymentMethodByIdOrder($id_order)
    {
        $cache_key ='EtsPaymentMethodClass::getPaymentMethodByIdOrder_'.$id_order;
        if(!Cache::isStored($cache_key))
        {
            $result =  Db::getInstance()->getRow('SELECT * FROM `' . _DB_PREFIX_ . 'ets_paymentmethod_order` WHERE id_order =' . (int)$id_order);
            Cache::store($cache_key,$result);
        }
        else
            $result = Cache::retrieve($cache_key);
        return $result;
    }
    public static function addPaymentOrder($id_ets_paymentmethod,$id_order,$payment_fee,$payment_fee_incl,$payment)
    {
        $sql = "INSERT INTO `" . _DB_PREFIX_ . "ets_paymentmethod_order` (`id_paymentmethod`,`id_order`,`fee`,`fee_incl`,`method_name`) values('" . (int)$id_ets_paymentmethod . "','" . (int)$id_order . "','" . (float)$payment_fee . "','" . (float)$payment_fee_incl . "','" . pSQL($payment) . "')";
        return Db::getInstance()->execute($sql);
    }
    public static function updateFeePaymentOrder($id_order,$total_tax_excl,$total_tax_incl)
    {
        return Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'orders` SET total_paid_tax_incl="' . (float)$total_tax_incl . '",total_paid_tax_excl="' . (float)$total_tax_excl . '",total_paid="' . (float)$total_tax_incl . '" WHERE id_order="' . (int)$id_order . '"');
    }
    public static function updatePosition($payments)
    {
        foreach ($payments as $key => $id) {
            Db::getInstance()->execute('update `' . _DB_PREFIX_ . 'ets_paymentmethod` set position="' . (int)($key + 1) . '" WHERE id_ets_paymentmethod=' . (int)$id);
        }
        return true;
    }
    public static function getModulesByFilter($filter,$sort,$sort_type)
    {
        $sql = 'SELECT m.*,pm.percentage,pm.fee_amount,pm.fee_type,pm.max_fee,pm.min_fee,pm.free_for_order_over,pm.minimum_order,pm.maximum_order,pm.id_tax_rules_group,pm.fee_based_on,trg.name as fee_tax
        FROM `' . _DB_PREFIX_ . 'module` m
        INNER JOIN `' . _DB_PREFIX_ . 'hook_module` hm ON (m.id_module = hm.id_module)
        INNER JOIN `' . _DB_PREFIX_ . 'hook` h ON (hm.id_hook= h.id_hook)
        LEFT JOIN `' . _DB_PREFIX_ . 'ets_paymentmethod_module` pm ON (m.id_module = pm.id_module AND (pm.id_shop=0 OR pm.id_shop="'.(int)Context::getContext()->shop->id.'")) 
        LEFT JOIN `'._DB_PREFIX_.'tax_rules_group` trg ON pm.id_tax_rules_group=trg.id_tax_rules_group
        WHERE m.active=1 AND hm.id_shop = ' . (int)Context::getContext()->shop->id . ($filter ? $filter : ''). ' ORDER BY ' . pSQL($sort) . ' ' . pSQL($sort_type);
        return Db::getInstance()->executeS($sql);
    }
    public static function fee_check_colum($table, $column, $suffix)
    {
        $sqls = array(
            'SET @dbname = DATABASE()',
            'SET @tablename = "' . _DB_PREFIX_ . pSQL($table) . '"',
            'SET @columnname = "' . pSQL($column) . '"',
            'SET @suffix = "' . pSQL($suffix) . '"',
            'SET @preparedStatement = (SELECT IF(
            (
                SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
                WHERE
                  (table_name = @tablename)
                  AND (table_schema = @dbname)
                  AND (column_name = @columnname)
                ) > 0,
                "SELECT 1",
                CONCAT("ALTER TABLE ", @tablename, " ADD ", @columnname," ", @suffix)
            ))',
            'PREPARE alterIfNotExists FROM @preparedStatement',
            'EXECUTE alterIfNotExists',
            'DEALLOCATE PREPARE alterIfNotExists',
        );
        foreach($sqls as $sql)
            Db::getInstance()->execute($sql);
        return true;
    }
    public static function checkCreatedColumn($table, $column)
    {
        $fieldsCustomers = Db::getInstance()->ExecuteS('DESCRIBE ' . _DB_PREFIX_ . bqSQL($table));
        $check_add = false;
        foreach ($fieldsCustomers as $field) {
            if ($field['Field'] == $column) {
                $check_add = true;
                break;
            }
        }
        return $check_add;
    }
    public static function getInvoicePaymentByIdOrder($id_order)
    {
        return Db::getInstance()->getValue('SELECT id_order_payment FROM `' . _DB_PREFIX_ . 'order_invoice_payment` WHERE id_order=' . (int)$id_order);
    }
    public static function addIndexTable()
    {
        Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'ets_paymentmethod` ADD INDEX (`id_shop`)');
        Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'ets_paymentmethod_module` ADD INDEX(`id_tax_rules_group`)');
        Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'ets_paymentmethod_order` ADD INDEX(`id_paymentmethod`),ADD INDEX(`id_order`)');
        Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'ets_payment_cart` ADD INDEX(`id_payment_method`)');
        return true;
    }
    public static function updatePostionHook($id_module,$id_hook)
    {
        $positioOld = Db::getInstance()->getValue('SELECT position FROM `'._DB_PREFIX_.'hook_module` WHERE id_module="'.(int)$id_module.'" AND id_hook ="'.(int)$id_hook.'"');
        if($positioOld)
        {
           Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'hook_module` SET `position` = "'.(int)$positioOld.'" WHERE `position` = 1  AND `id_hook` = '.(int)$id_hook);
           Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'hook_module` SET `position` = 1 WHERE `id_module` = "'.(int)$id_module.'"  AND `id_hook` = '.(int)$id_hook);
        }
        return true;
    }
    public static function checkEnableOtherShop($id_module)
    {
        $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'module_shop` WHERE `id_module` = ' . (int) $id_module . ' AND `id_shop` NOT IN(' . implode(', ', Shop::getContextListShopID()) . ')';
        return Db::getInstance()->executeS($sql);
    }
    public static function activeTab($module_name)
    {
        return Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'tab` SET enabled=1 where module ="'.pSQL($module_name).'"');
    }
}