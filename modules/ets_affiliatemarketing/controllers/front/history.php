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

class Ets_affiliatemarketingHistoryModuleFrontController extends Ets_affiliatemarketingAllModuleFrontController
{
    public $auth = true;
    public $guestAllowed = false;
    public $authRedirection = URL_EAM_HISTORY;
    public function init()
    {
        parent::init();
        if (!$this->module->is17)
        {
            $this->display_column_left = false;
            $this->display_column_right = false;
        }
    }
    public function initContent()
    {
        parent::initContent();
        if(!$this->context->customer->isLogged())
            Tools::redirect($this->context->link->getPageLink('my-account'));
        $page= 'module-'.$this->module->name.'-history';
        $meta = Meta::getMetaByPage($page,$this->context->language->id);
         $this->setMetas(array(
            'title' => isset($meta['title']) && $meta['title'] ? $meta['title'] : $this->module->l('Reward history', 'history'),
            'keywords' => isset($meta['keywords']) && $meta['keywords'] ? $meta['keywords'] : $this->module->l('Reward history', 'history'),
            'description' =>isset($meta['description']) && $meta['description'] ? $meta['description'] : $this->module->l('Reward history', 'history'),
        ));
        if (! $this->context) {
            $context = Context::getContext();
        } else {
            $context = $this->context;
        }
        if (!$this->module->is17) {
            $this->context->smarty->assign(array('controller' => 'history'));
        }
        $id_customer = $context->customer->id;
        $filter = array(
            'type_date_filter' => Tools::getValue('type_date_filter'),
            'date_from_reward' => Tools::getValue('date_from_reward'),
            'date_to_reward' => Tools::getValue('date_to_reward'),
            'program' => Tools::getValue('program'),
            'status' => Tools::getValue('status'),
            'limit' => (int)Tools::getValue('limit'),
            'page' => (int)Tools::getValue('page'),
            'id_customer' => (int)Tools::getValue('id_customer'),
        );
        $reward_histories = EtsAmAdmin::getRewardHistory($id_customer, null, true, true,$filter);
        $this->context->smarty->assign(array(
            'eam_url' => Ets_AM::getBaseUrl(),
            'eam_confirm' => null,
            'reward_history' => $reward_histories,
            'link_reward' => Ets_AM::getBaseUrlDefault('dashboard'),
            'link_reward_history' => Ets_AM::getBaseUrlDefault('history'),
            'link_withdraw' => Ets_AM::getBaseUrlDefault('withdraw'),
            'link_voucher' => Ets_AM::getBaseUrlDefault('voucher'),
            'query' => Tools::getAllValues(),
            'controller'=>'history',
        ));

        if($this->module->is17){
            $this->setTemplate('module:ets_affiliatemarketing/views/templates/front/customer_reward.tpl');
        }
        else{
            $this->setTemplate('customer_reward16.tpl');
        }
    }

}
