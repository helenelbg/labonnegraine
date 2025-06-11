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

class Ets_payment_cart_class extends ObjectModel{
    public $id_cart;
    public $ets_payment_module_name;
    public $id_payment_method;
    public $payment_option;

    public static $definition = array(
        'table' => 'ets_payment_cart',
        'primary' => 'id_cart',
        'fields' => array(
            'id_cart' => array('type' => self::TYPE_INT),
            'ets_payment_module_name' => array('type' => self::TYPE_STRING),
            'id_payment_method' => array('type' => self::TYPE_INT),
            'payment_option' => array('type' => self::TYPE_STRING),
        )
    );

    public	function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        parent::__construct($id, $id_lang, $id_shop);
    }
}