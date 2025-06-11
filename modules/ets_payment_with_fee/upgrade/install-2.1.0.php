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
/**
 * @param Ets_payment_with_fee $object
 * @return bool
 */
function upgrade_module_2_1_0($object)
{
    Ets_paymentmethod_class::fee_check_colum('ets_paymentmethod_module', 'id_tax_rules_group', 'INT(11) NULL AFTER `maximum_order`');
    Ets_paymentmethod_class::fee_check_colum('ets_paymentmethod_module', 'fee_based_on', 'INT(11) NULL AFTER `id_tax_rules_group`');
    Ets_paymentmethod_class::fee_check_colum('ets_paymentmethod', 'id_tax_rules_group', 'INT(11) NULL AFTER `free_for_order_over`');
    Ets_paymentmethod_class::fee_check_colum('ets_paymentmethod', 'fee_based_on', 'INT(11) NULL AFTER `id_tax_rules_group`');
    Ets_paymentmethod_class::fee_check_colum('ets_paymentmethod_order', 'fee_incl', 'FLOAT(10,2) NULL AFTER `fee`');
    Db::getInstance()->execute('update `'._DB_PREFIX_.'ets_paymentmethod_order` SET fee_incl = fee WHERE fee_incl=0');
    $object->copy_directory(dirname(__FILE__).'/../views/templates/admin/templates',_PS_OVERRIDE_DIR_.'controllers/admin/templates');
    return true;
}