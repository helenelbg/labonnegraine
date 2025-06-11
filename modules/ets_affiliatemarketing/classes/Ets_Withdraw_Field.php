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
class Ets_Withdraw_Field extends ObjectModel
{
    /**
     * @var int
     */
    public $id_ets_am_withdrawal_field;
    /**
     * @var int
     */
    public $id_withdrawal;
    /**
     * @var int
     */
    public $id_payment_method_field;
    /**
     * @var string
     */
    public $value;
    public static $definition = array(
        'table' => 'ets_am_withdrawal_field',
        'primary' => 'id_ets_am_withdrawal_field',
        'multilangshop' => true,
        'fields' => array(
            'id_ets_am_withdrawal_field' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt'
            ),
            'id_withdrawal' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt',
            ),
            'id_payment_method_field' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt'
            ),
            'value' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isString'
            )
        )
    );
}
