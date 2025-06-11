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

/**
 * Class AdminEtsAmDashboardController
 * @property Ets_affiliatemarketing $module;
 */
class AdminEtsAmDashboardController extends ModuleAdminController
{
    public function init()
    {
        parent::init();
        $this->bootstrap = true;
        if ((bool)Tools::isSubmit('getTabDataDasboard', false)) {
            $page = ($page = (int)Tools::getValue('page', 1)) ? $page : 1;
            $type = ($type = Tools::getValue('type', false)) && in_array($type, array('recent_orders', 'best_seller', 'top_reward_accounts','top_affiliate', 'top_customer', 'top_sponsor')) ? $type : 'recent_orders';
            $data_filter = ($data_filter = Tools::getValue('data_filter', array())) && is_array($data_filter) && Ets_affiliatemarketing::validateArray($data_filter) ? $data_filter : array();
            $params = array(
                'page' => $page,
                'type' => $type,
                'data_filter' => $data_filter
            );
            $results = Ets_Am::getStatsTopTrending($params);
            die(json_encode(array(
                'success' => true,
                'html' => $this->renderTableDashboard($results, $type)
            )));
        }
        if ((bool)Tools::isSubmit('get_stat_reward')) {
            $this->statsReward();
        }
        if ((bool)Tools::isSubmit('get_pie_chart_reward')) {
            $this->getPercentReward(array('status' => 1));
        }
    }
    public function renderList()
    {
        $this->renderStatisticReward();
        $this->context->smarty->assign($this->module->getAssign('dashboard'));
        return $this->module->display(_PS_MODULE_DIR_.$this->module->name.DIRECTORY_SEPARATOR.$this->module->name.'.php', 'admin_form.tpl');
    }
    protected function renderStatisticReward()
    {
        $params = array();
        if (($filter_status = Tools::getValue('filter_status')) && Validate::isCleanHtml($filter_status)) {
            $params['status'] = $filter_status;
        } else {
            $params['stats_type'] = 'reward';
            $params['status'] = 1;
        }
        if (($filter_date_from = Tools::getValue('filter_date_from')) && Validate::isDate($filter_date_from)) {
            $params['date_from'] = $filter_date_from;
        }
        if (($filter_date_to = Tools::getValue('filter_date_to')) && Validate::isDate($filter_date_to)) {
            $params['date_to'] = $filter_date_to;
        }
        if (($program = Tools::getValue('program')) && Validate::isCleanHtml($program)) {
            $params['program'] = $program;
        }
        $cache_params = array('dashboard',$this->context->employee->id,'main','status' => isset($params['status']) ? (int)$params['status']:'all');
        if(isset($params['program']) && $params['program'])
            $cache_params[] = $params['program'];
        $cacheID = $this->module->_getCacheId($cache_params);
        if((isset($params['date_from']) && $params['date_from']) || (isset($params['date_to']) && $params['date_to']) ||  !$this->module->isCached('stats.tpl',$cacheID))
        {
            $results = Ets_AM::getStartChartReward($params);
            $score_counter = Ets_Am::getStatsCounter();
            $order_states = OrderState::getOrderStates((int)Configuration::get('PS_LANG_DEFAULT'));
            $default_currency = Currency::getDefaultCurrency();
            $recently_rewards = Ets_Am::getRecentReward();
            $pie_reward = Ets_Am::getPercentReward(array('status' => 1));
            $last_cronjob = array();
            if ($cronjob_time = trim(Configuration::getGlobalValue('ETS_AM_TIME_RUN_CRONJOB'))) {
                $last_cronjob['time'] = $cronjob_time;
                $date1 = strtotime(date('Y-m-d H:i:s'));
                $date2 = strtotime($cronjob_time);
                $diff = $date1 - $date2;
                $diff_hour = $diff / 3600;
                $last_cronjob['warning'] = 0;
                if ($diff_hour > 12) {
                    $last_cronjob['warning'] = 1;
                }
            }
            $assignment = array(
                'data_stats' => $results,
                'pie_reward' => $pie_reward,
                'last_cronjob' => $last_cronjob,
                'recently_rewards' => $recently_rewards,
                'score_counter' => $score_counter,
                'order_states' => $order_states,
                'recent_orders' => Ets_AM::getStatsTopTrending(array('type' => 'recent_orders')),
                'customer_link' => $this->context->link->getAdminLink('AdminEtsAmUsers', true) .'&tabActive=reward_users',
                'order_link' => $this->context->link->getAdminLink('AdminOrders', true),
                'default_currency' => $default_currency,
                'reward_history_link' => $this->context->link->getAdminLink('AdminModules', true) . '&configure=' . $this->module->name . '&tabActive=reward_history',
                'module_link' => $this->context->link->getAdminLink('AdminModules', true) . '&configure=' . $this->module->name,
                'cronjob_closed_alert' => $this->context->cookie->closed_alert_cronjob,
                'loyaltyPrograEnabled' => Configuration::get('ETS_AM_LOYALTY_ENABLED'),
                'loyaltyRewardAvailability' => Configuration::get('ETS_AM_LOYALTY_MAX_DAY'),
                'eam_currency_code' => $this->context->currency->iso_code,
            );
            if ($this->module->is17) {
                $assignment['is17'] = true;
            } else {
                $assignment['is17'] = false;
            }
            $this->context->smarty->assign($assignment);
        }
        $this->module->_html = $this->module->display($this->module->getLocalPath(), 'stats.tpl',$cacheID).$this->displayInfoCronJob();
    }
    public function displayInfoCronJob()
    {
        $this->context->smarty->assign(
            array(
                'info_cronjob' => $this->module->displayInfoRunCronJob(),
            )
        );
        return $this->module->display($this->module->getLocalPath(),'stats_cron.tpl');
    }
    protected function renderTableDashboard($data, $type)
    {
        $default_currency = Currency::getDefaultCurrency();
        $temp = 'dashboard/recent_orders.tpl';
        switch ($type) {
            case 'recent_orders':
                $temp = 'dashboard/recent_orders.tpl';
                break;
            case 'best_seller':
                $temp = 'dashboard/best_seller.tpl';
                break;
            case 'top_sponsor':
                $temp = 'dashboard/top_sponsor.tpl';
                break;
            case 'top_affiliate':
                $temp = 'dashboard/top_affiliate.tpl';
                break;
            case 'top_customer':
                $temp = 'dashboard/top_customer.tpl';
                break;
            case 'top_reward_accounts':
                $temp = 'dashboard/top_reward_accounts.tpl';
                break;
        }
        $this->context->smarty->assign(array(
            'data' => $data,
            'default_currency' => $default_currency,
            'customer_link' => $this->context->link->getAdminLink('AdminEtsAmUsers', true) . '&tabActive=reward_users',
            'order_link' => $this->context->link->getAdminLink('AdminOrders', true),
        ));
        return $this->module->display(_PS_MODULE_DIR_.$this->module->name.DIRECTORY_SEPARATOR.$this->module->name.'.php', $temp);
    }
    protected function statsReward()
    {
        $params = array();
        if (Tools::isSubmit('filter_status')) {
            $params['status'] = (int)Tools::getValue('filter_status');
        } else {
            $params['stats_type'] = 'reward';
            $params['status'] = 1;
        }
        if (($filter_reward_status = Tools::strtolower(Tools::getValue('filter_reward_status'))) && ($filter_reward_status == 'all' || Validate::isInt($filter_reward_status))) {
            $params['reward_status'] = $filter_reward_status;
        }
        if (($filter_order_status = Tools::getValue('filter_order_status')) && Validate::isCleanHtml($filter_order_status)) {
            $params['order_status'] = $filter_order_status;
        }
        if (($filter_date_from = Tools::getValue('filter_date_from')) && Validate::isDate($filter_date_from)) {
            $params['date_from'] = $filter_date_from;
        }
        if (($filter_date_to = Tools::getValue('filter_date_to')) && Validate::isDate($filter_date_to)) {
            $params['date_to'] = $filter_date_to;
        }
        if (($filter_date_type = Tools::getValue('filter_date_type')) && in_array($filter_date_type,array('this_month','this_year','all_times','time_ranger'))) {
            $params['date_type'] = $filter_date_type;
        }
        if (($filter_type_stats = Tools::strtolower(Tools::getValue('filter_type_stats'))) && in_array($filter_type_stats, array('customers', 'reward', 'orders', 'turnover'))) {
            $params['stats_type'] = $filter_type_stats;
        }
        if (($program = Tools::strtolower(Tools::getValue('program'))) && Validate::isTablePrefix($program)) {
            $params['program'] = $program;
        }
        $results = Ets_AM::getStartChartReward($params);
        die(json_encode($results));
    }
    public function getPercentReward($params = array())
    {
        if (($filter_date_from = Tools::getValue('filter_date_from')) && Validate::isDate($filter_date_from)) {
            $params['date_from'] = $filter_date_from;
        }
        if (($filter_date_to = Tools::getValue('filter_date_to')) && Validate::isDate($filter_date_to)) {
            $params['date_to'] = $filter_date_to;
        }
        if (($filter_date_type = Tools::getValue('filter_date_type')) && Validate::isCleanHtml($filter_date_type)) {
            $params['date_type'] = $filter_date_type;
        }
        die(json_encode(Ets_AM::getPercentReward($params)));
    }
}
