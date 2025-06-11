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

class Ets_Affiliate_Config extends ObjectModel
{
	/**
     * @var int
     */
    public $id_ets_am_aff_reward;
   
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
    public $how_to_calculate;
    /**
     * @var
     */
    public $default_percentage;
    /**
     * @var
     */
    public $default_fixed_amount;
    

    public static $definition = array(
        'table' => 'ets_am_aff_reward',
        'primary' => 'id_ets_am_aff_reward',
        'multilang_shop' => true,
        'fields' => array(
            'id_ets_am_aff_reward' => array(
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
            'how_to_calculate' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
            ),
            'default_percentage' => array(
                'type' => self::TYPE_FLOAT,
                'validate' => 'isUnsignedFloat'
            ),
            'default_fixed_amount' => array(
                'type' => self::TYPE_FLOAT,
                'validate' => 'isUnsignedFloat'
            ),
        )
    );
}