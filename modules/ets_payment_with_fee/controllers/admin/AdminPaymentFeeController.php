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
 * Class AdminPaymentFeeController
 * @property \Ets_payment_with_fee $module
 */
class AdminPaymentFeeController extends ModuleAdminController
{
    public function __construct()
    {
       parent::__construct();
       $this->bootstrap = true;
    }
    public function renderList()
    {
        $errors = array();
        if(Tools::isSubmit('saveModulePaymentFee') && $id_module = (int)Tools::getValue('id_module'))
        {
            $fee_type = Tools::getValue('fee_type');
            if(!in_array($fee_type,array('percentage','fixed_fee','free','percentage_fixed')))
                $errors[] = $this->l('Fee type is not valid');
            $free_for_order_over = Tools::getValue('free_for_order_over');
            if($free_for_order_over!='' && (!Validate::isFloat($free_for_order_over)||(float)$free_for_order_over<=0))
                $errors[] =$this->l('Free for order over is invalid'); 
            $free_for_order_over = (float)$free_for_order_over/$this->context->currency->conversion_rate;
            $fee_amount = Tools::getValue('fee_amount');
            if($fee_type=='fixed_fee' || $fee_type=='percentage_fixed')
            {
                if($fee_amount!=''&& (!Validate::isFloat($fee_amount)|| (float)$fee_amount<=0))
                    $errors[]=$this->l('Fee amount is invalid');
                elseif(!$fee_amount)
                    $errors[]=$this->l('Fee amount is required');
            }  
            $fee_amount= (float)$fee_amount/$this->context->currency->conversion_rate;
            $percentage = Tools::getValue('percentage');
            $max_fee = Tools::getValue('max_fee');
            $min_fee = Tools::getValue('min_fee');
            if($fee_type=='percentage' || $fee_type=='percentage_fixed')
            {
                if($percentage!='' && (!Validate::isFloat($percentage) || $percentage<=0 || $percentage > 100))
                    $errors[]= $this->l('Percentage is invalid');
                elseif(!$percentage)
                    $errors[]=$this->l('Percentage amount is required');
                if($max_fee!=''&& (!Validate::isFloat($max_fee)||(float)$max_fee<=0))
                    $errors[]=$this->l('Maximum fee is invalid');
                if($min_fee!='' && (!Validate::isFloat($min_fee)||(float)$min_fee<=0))
                    $errors[]=$this->l('Minimum fee is invalid');
                if($max_fee!='' && $min_fee!='' && Validate::isFloat($min_fee) && Validate::isFloat($max_fee)&& (float)$min_fee > (float)$max_fee &&(float)$max_fee>=0 && (float)$min_fee>=0 )
                {
                    $errors[]=$this->l('Maximum fee must be greater than Minimum fee');
                }
            }
            $max_fee  = (float)$max_fee/$this->context->currency->conversion_rate;
            $min_fee = (float)$min_fee/$this->context->currency->conversion_rate;
            $minimum_order = Tools::getValue('minimum_order');
            if($minimum_order!='' && (!Validate::isFloat($minimum_order) || $minimum_order<=0))
                $errors[]= $this->l('Minimum total order is invalid');
            $maximum_order = Tools::getValue('maximum_order');
            if($maximum_order!=''&& (!Validate::isFloat($maximum_order)||(float)$maximum_order<=0))
                $errors[]=$this->l('Maximum total order is invalid');
            if($maximum_order!='' && $minimum_order!='' && Validate::isFloat($minimum_order) && Validate::isFloat($maximum_order)&& (float)$minimum_order > (float)$maximum_order &&(float)$maximum_order >=0 && (float)$minimum_order>=0)
            {
                $errors[]=$this->l('Maximum total order must be greater than Minimum total order');
            }
            $fee_based_on = (int)Tools::getValue('fee_based_on');
            $id_tax_rules_group = (int)Tools::getValue('id_tax_rules_group');
            if(!$errors)
            {
                if(Ets_paymentmethod_class::addFeeToModule($id_module,$fee_type,$fee_amount,$free_for_order_over,$percentage,$max_fee,$min_fee,$minimum_order,$maximum_order,$fee_based_on,$id_tax_rules_group))
                    Tools::redirectAdmin($this->context->link->getAdminLink('AdminPaymentFee', true).'&conf=4');
            }
        }
        $this->context->smarty->assign(
            array(
                'list_module' =>$this->module->renderListModules(),
                'errors_module' => $errors ? $this->module->displayError($errors) : false,
                'ets_custom_payment_module_dir' => $this->module->module_dir,
            )
        );
        return (!$this->module->active ? $this->module->displayWarning($this->l('You must enable "Payment with fee" module to configure its features')):''). $this->module->display(_PS_MODULE_DIR_.$this->module->name.DIRECTORY_SEPARATOR.$this->module->name.'.php', 'modules.tpl');
    }
}