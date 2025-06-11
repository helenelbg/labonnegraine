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
class Ets_Loyalty_Config extends ObjectModel
{
    /**
     * @var int
     */
    public $id_ets_am_loy_reward;
    /**
     * @var
     */
    public $id_product;
    /**
     * @var
     */
    public $id_shop;
    /**
     * @var
     */
    public $use_default;
    /**
     * @var
     */
    public $base_on;
    /**
     * @var
     */
    public $amount;
    /**
     * @var
     */
    public $amount_per;
    /**
     * @var
     */
    public $gen_percent;
    /**
     * @var
     */
    public $qty_min;
    public static $definition = array(
        'table' => 'ets_am_loy_reward',
        'primary' => 'id_ets_am_loy_reward',
        'multilang_shop' => true,
        'fields' => array(
            'id_ets_am_loy_reward' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt'
            ),
            'id_product' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt'
            ),
            'id_shop' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt'
            ),
            'use_default' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isInt'
            ),
            'base_on' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
            ),
            'amount' => array(
                'type' => self::TYPE_FLOAT,
            ),
            'amount_per' => array(
                'type' => self::TYPE_FLOAT,
            ),
            'gen_percent' => array(
                'type' => self::TYPE_FLOAT,
            ),
            'qty_min' => array(
                'type' => self::TYPE_INT,
            ),
        )
    );
    public static function getProductRewardSetting($id_product)
    {
        return Db::getInstance()->getRow("SELECT * FROM `" . _DB_PREFIX_ . "ets_am_loy_reward` WHERE `id_product` = " . (int)$id_product);
    }
}