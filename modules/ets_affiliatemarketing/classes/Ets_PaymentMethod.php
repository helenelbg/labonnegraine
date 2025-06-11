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

if (!defined('_PS_VERSION_')) {
    exit();
}
class Ets_PaymentMethod extends ObjectModel
{
    /**
     * @var int
     */
    public $id_ets_am_payment_method;
    /**
     * @var int
     */
    public $id_shop;
    /**
     * @var string
     */
    public $fee_type;
    /**
     * @var int
     */
    public $fee_fixed;
    /**
     * @var int
     */
    public $fee_percent;
    /**
     * @var int
     */
    public $enable;
    public $deleted;
    public $sort;
    public $estimated_processing_time;
    public static $definition = array(
        'table' => 'ets_am_payment_method',
        'primary' => 'id_ets_am_payment_method',
        'multilangshop' => true,
        'fields' => array(
            'id_ets_am_payment_method' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt'
            ),
            'id_shop' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt'
            ),
            'title' => array(
                'type' => self::TYPE_STRING,
                'lang' => true,
                'validate' => 'isString'
            ),
            'fee_type' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isString'
            ),
            'fee_fixed' => array(
                'type' => self::TYPE_FLOAT,
                'validate' => 'isFloat'
            ),
            'fee_percent' => array(
                'type' => self::TYPE_FLOAT,
                'validate' => 'isFloat'
            ),
            'estimated_processing_time' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt'
            ),
            'enable' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt'
            ),
            'sort' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt'
            ),
            'deleted' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt'
            ),
        )
    );
    public function createPaymentMethod($params)
    {
        $pm_title = $params['title'];
        $pm_fee_type = $params['fee_type'];
        $pm_fee_fixed = $params['fee_fixed'];
        $pm_fee_percent = $params['fee_percent'];
        $pm_enable = $params['enable'];
        $pm_estimated = $params['estimate_processing_time'];
        $pm_desc = $params['desc'];
        $pm_note = $params['note'];
        $max_sort = (int)Db::getInstance()->getValue("SELECT MAX(sort) as max_sort FROM " . _DB_PREFIX_ . "ets_am_payment_method");
        $this->fee_type = $pm_fee_type;
        $this->fee_fixed = $pm_fee_fixed;
        $this->fee_percent = $pm_fee_percent;
        $this->estimated_processing_time = $pm_estimated;
        $this->enable = $pm_enable;
        $this->sort = $max_sort + 1;
        $this->id_shop = Context::getContext()->shop->id;
        $this->add();
        $id_pm = $this->id;
        $default_title = null;
        foreach ($pm_title as $pt) {
            if ($pt) {
                $default_title = $pt;
                break;
            }
        }
        $languages = Language::getLanguages(false);
        $sqls = array();
        foreach ($languages as $lang) {
            $desc = isset($pm_desc[$lang['id_lang']]) && $pm_desc[$lang['id_lang']] ? $pm_desc[$lang['id_lang']] : null;
            $title = isset($pm_title[$lang['id_lang']]) && $pm_title[$lang['id_lang']] ? $pm_title[$lang['id_lang']] : $default_title;
            $note = isset($pm_note[$lang['id_lang']]) && $pm_note[$lang['id_lang']] ? $pm_note[$lang['id_lang']] : null;
            $sqls[] = "INSERT INTO `" . _DB_PREFIX_ . "ets_am_payment_method_lang` (id_payment_method, title, description, note, id_lang) VALUES (".(int)$id_pm.", '" . pSQL($title) . "', '" . pSQL($desc, true) . "', '" . pSQL($note) . "'," . (int)$lang['id_lang'] . ");";
        }
        if ($sqls) {
            foreach($sqls as $sql)
                Db::getInstance()->execute($sql);
        }
        return $id_pm;
    }
}
