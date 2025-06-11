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

class Ets_affiliatemarketingDashboardModuleFrontController extends Ets_affiliatemarketingAllModuleFrontController
{
    public $auth = false;
    public $guestAllowed = false;
    public $authRedirection = URL_CUSTOMER_REWARD;
    public $html = '';
    public $errors = array();
    public function init()
    {
        parent::init();
        if (!$this->module->is17) {
            $this->display_column_left = false;
            $this->display_column_right = false;
        }
    }
    public function initContent()
    {
        parent::initContent();
        if (Tools::isSubmit('ajax')) {
            $cart = $this->context->cart;
            $message = Ets_Loyalty::getCartMessage($cart);
            die(json_encode(array('message' => $message)));
        }
        if(!$this->context->customer->isLogged())
            Tools::redirect($this->context->link->getPageLink('my-account'));
        $page= 'module-'.$this->module->name.'-dashboard';
        $meta = Meta::getMetaByPage($page,$this->context->language->id);
        $this->setMetas(array(
            'title' => isset($meta['title']) && $meta['title'] ? $meta['title'] : $this->module->l('Dashboard','dashboard'),
            'keywords' => isset($meta['keywords']) && $meta['keywords'] ? $meta['keywords'] : $this->module->l('Dashboard','dashboard'),
            'description' => isset($meta['description']) && $meta['description'] ? $meta['description'] : $this->module->l('Dashboard','dashboard'),
        ));

        if (!$this->module->is17) {
            $this->context->smarty->assign(array('controller' => 'dashboard'));
        }

        if (! $this->context) {
            $context = Context::getContext();
        } else {
            $context = $this->context;
        }
        $baseUrl = Ets_AM::getBaseUrl();
        $this->context->smarty->assign(array(
            'eam_url' => $baseUrl,
            'link_reward' => Ets_AM::getBaseUrlDefault('dashboard'),
            'link_reward_history' => Ets_AM::getBaseUrlDefault('history'),
            'link_withdraw' =>Ets_AM::getBaseUrlDefault('withdraw'),
            'link_voucher' => Ets_AM::getBaseUrlDefault('voucher'),
        ));

        if (Tools::isSubmit('get_stat_reward') || Tools::isSubmit('get_pie_reward')) {
            $p = array();
            $p['get_stat_reward'] = true;
            $filter_status = Tools::getValue('filter_status', false);
            if ($filter_status && isset($filter_status[0])) {
                $filter_status = $filter_status[0];
                if ($filter_status === 'approved') {
                    $p['status'] = 1;
                } elseif ($filter_status === 'pending') {
                    $p['status'] = 0;
                } elseif ($filter_status === 'canceled') {
                    $p['status'] = -1;
                } elseif ($filter_status === 'all') {
                    $p['status'] = '';
                } else {
                    $p['status'] = -2;
                }
            }
            if (($filter_date_from = Tools::getValue('filter_date_from')) && Validate::isDate($filter_date_from)) {
                $p['date_from'] = $filter_date_from;
            }
            if (($filter_date_to = Tools::getValue('filter_date_to')) && Validate::isDate($filter_date_to)) {
                $p['date_to'] = $filter_date_to;
            }
            if (($filter_date_type = Tools::strtolower(Tools::getValue('filter_date_type'))) && in_array($filter_date_type,array('this_year','this_month','all_times','time_ranger'))) {
                $p['date_type'] = $filter_date_type;
            }
            if (($program = Tools::getValue('program')) && Validate::isCleanHtml($program) ) {
                $p['program'] = $program;
            }
            $p['stats_type'] = 'reward';
            $p['id_customer'] = (int)$context->customer->id;
            die(
                json_encode(
                    array(
                        'pie_reward' => Ets_AM::getPercentReward($p, true),
                        'stat_reward' => Ets_AM::getStatsReward($p, $this->context, true, true),
                    )
                )
            );
        }
        $params = array();
        $params['id_customer'] = (int)$context->customer->id;
        $stats_rewards = Ets_AM::getStatsReward($params, $this->context, true, true);
        $total_reward_usage = Ets_Reward_Usage::getTotalSpent($this->context->customer->id);
        $total_reward_balance = Ets_Reward_Usage::getTotalBalance();
        $this->context->smarty->assign(array(
            'eam_allow_withdraw_loyalty' => 0,
            'eam_total_usage' => Ets_AM::displayReward($total_reward_usage, true),
            'eam_total_reward_balance' => Ets_Am::displayReward($total_reward_balance, true)
        ));            
        $totalSpentLoy = Ets_Reward_Usage::getTotalSpentLoy($context->customer->id, false, null, $context);
        $totalLoy = Ets_Reward_Usage::getTotalEarn('loy', $context->customer->id, $context);
        $total_loyalty_left = (float)$totalLoy - (float)$totalSpentLoy;
        $total_earning_left = (float)$total_reward_balance - $total_loyalty_left;
        $this->context->smarty->assign(array(
            'eam_total_loyalty_left' =>Ets_AM::displayReward($total_loyalty_left),
            'eam_total_earning_left' => Ets_AM::displayReward($total_earning_left)
        ));
        $pie_reward = Ets_Am::getPercentReward(array('id_customer' => $context->customer->id, 'status' => 1), true);
        $message_title_reward ='';
        if(Configuration::get('ETS_AM_AFF_ALLOW_BALANCE_TO_PAY'))
            $message_title_reward .=$this->module->l('pay for orders','dashboard').', ';
        if(Configuration::get('ETS_AM_AFF_ALLOW_CONVERT_TO_VOUCHER'))
        {
            $message_title_reward .=$this->module->l('convert into voucher code','dashboard').', ';
        }
        $message_title_reward_earning= $message_title_reward;
        if(Configuration::get('ETS_AM_AFF_ALLOW_WITHDRAW') && Configuration::get('ETS_AM_ALLOW_WITHDRAW_LOYALTY_REWARDS'))
            $message_title_reward .=$this->module->l('withdraw','dashboard').', ';
        if(Configuration::get('ETS_AM_AFF_ALLOW_WITHDRAW'))
            $message_title_reward_earning .=$this->module->l('withdraw','dashboard').', ';
        $this->context->smarty->assign(array(
            'eam_allow_withdraw_loyalty' => 0,
            'data_stats' => $stats_rewards,
            'eam_currency_code' => Configuration::get('ETS_AM_REWARD_DISPLAY') == 'point' ? (Configuration::get('ETS_AM_REWARD_UNIT_LABEL', $this->context->language->id) ? Configuration::get('ETS_AM_REWARD_UNIT_LABEL', $this->context->language->id) : Configuration::get('ETS_AM_REWARD_UNIT_LABEL', (int)Configuration::get('PS_LANG_DEFAULT'))) : $this->context->currency->iso_code,
            'pie_reward' => $pie_reward,
            'message_title_reward' => trim($message_title_reward,', '),
            'message_title_reward_earning' => trim($message_title_reward_earning,', '),
            'controller'=>'dashboard',
        ));

        if ($this->module->is17) {
            $this->setTemplate('module:ets_affiliatemarketing/views/templates/front/customer_reward.tpl');
        } else {
            $this->setTemplate('customer_reward16.tpl');
        }
    }
    public function flash($key)
    {
        $flash = '';
        if ($this->context->cookie->__get($key)) {
            $flash = $this->context->cookie->__get($key);
            $this->context->cookie->__set($key, null);
        }
        return $flash;
    }
}
