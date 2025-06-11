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

class Ets_Product_View extends ObjectModel
{
    /**
     * @var int
     */
    public $id_ets_am_product_view;
    /**
     * @var int
     */
    public $count;
    /**
     * @var int
     */
    public $id_product;
    /**
     * @var int
     */
    public $id_seller;
    /**
     * @var datetime
     */
    public $date_added;

    public static $definition = array(
        'table' => 'ets_am_product_view',
        'primary' => 'id_ets_am_product_view',
        'multilang_shop' => true,
        'fields' => array(
            'id_ets_am_product_view' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt'
            ),
            'count' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt'
            ),
            'id_product' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt',
            ),
            'id_seller' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt'
            ),
            'date_added' => array(
                'type' => self::TYPE_DATE,
                'validate' => 'isDate',
                'allow_null' => true
            )
        )
    );
    public static function getId($id_product,$id_seller)
    {
        return Db::getInstance()->getValue('SELECT id_ets_am_product_view FROM `' . _DB_PREFIX_ . 'ets_am_product_view` WHERE `id_product` = ' . (int)$id_product . ' AND `id_seller` = "' . pSQL($id_seller). '" AND `date_added` = "' . pSQL(date('Y-m-d')) . '"');
    }
}