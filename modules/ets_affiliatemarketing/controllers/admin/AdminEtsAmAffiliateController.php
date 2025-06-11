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

if (!defined('_PS_VERSION_'))
    exit;
require_once dirname(__FILE__) . '/AdminEtsAmFormController.php';
class AdminEtsAmAffiliateController extends AdminEtsAmFormController
{
    public function init()
    {
        parent::init();
        $this->bootstrap = true;
        if(Tools::isSubmit('ajax_search_customer'))
        {
            if(($q = (string)strip_tags(Tools::getValue('q'))) && Validate::isCleanHtml($q))
            {
                if(($customers = Ets_User::searchCustomer($q)))
                {
                    foreach($customers as $customer)
                        echo $customer['id_customer'].'|'.$customer['email'].'|'.$customer['firstname'].' '.$customer['lastname']."\n";
                }
            }
            exit();
        }
    }
    public function renderList()
    {
        $tabActive = Tools::getValue('tabActive','affiliate_conditions');
        if(!in_array($tabActive,array('affiliate_conditions','affiliate_reward_caculation','affiliate_voucher','affiliate_messages')))
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminEtsAmAffiliate'));
        return $this->_renderList($tabActive);
    }
}
