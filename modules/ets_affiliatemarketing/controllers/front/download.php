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

class Ets_affiliatemarketingDownloadModuleFrontController extends ModuleFrontController
{
    public $auth = true;
    public $guestAllowed = false;
    public function initContent()
    {
        if(!$this->context->customer->isLogged())
            Tools::redirect($this->context->link->getPageLink('my-account'));
        if (Tools::isSubmit('downloadInvoiceWithdraw')
            && ($id_withdraw = (int)Tools::getValue('id_withdraw')) 
            && ($withdraw = new Ets_Withdraw($id_withdraw)) 
            && Validate::isLoadedObject($withdraw)
            && ($usage = Ets_Reward_Usage::getRewardUsageByIDWithdraw($id_withdraw))
            && $usage->id_customer == $this->context->customer->id
        ){
            if(!$withdraw->downloadInvoice()) {
                die($this->module->l('File Not Found','download'));
            }
        }
        else
            die($this->module->l('You do not have permission to download this file.','download'));
    }
}