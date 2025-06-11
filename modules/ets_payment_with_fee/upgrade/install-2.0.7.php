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

function upgrade_module_2_0_7()
{
    Ets_paymentmethod_class::fee_check_colum('ets_paymentmethod_module', 'minimum_order', 'FLOAT(10,2) NULL AFTER `free_for_order_over`');
    Ets_paymentmethod_class::fee_check_colum('ets_paymentmethod_module', 'maximum_order', 'FLOAT(10,2) NULL AFTER `minimum_order`');
    return true;
}