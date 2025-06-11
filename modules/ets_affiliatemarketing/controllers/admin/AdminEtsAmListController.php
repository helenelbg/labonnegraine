<?php
/**
 * 2007-2022 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2022 PrestaShop SA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

/**
 * Class AdminEtsAmFormController
 * @property Ets_affiliatemarketing $module;
 */
class AdminEtsAmListController extends ModuleAdminController
{
    public function init()
    {
        parent::init();
        if ((bool)Tools::isSubmit('searchSuggestion', false)) {
            $query = ($query = Tools::getValue('query', '')) && Validate::isCleanHtml($query) ? $query : '';
            $query_type = ($query_type = Tools::getValue('query_type', '')) && Validate::isCleanHtml($query_type) ? $query_type : '';
            if ($query) {
                die(json_encode(array(
                    'success' => true,
                    'html' => $this->getSearchSuggestions($query, $query_type)
                )));
            }
        }
    }
    public function getSearchSuggestions($query, $query_type)
    {
        $results = EtsAmAdmin::getSearchSuggestionsReward($query, $query_type);
        $this->context->smarty->assign(array(
            'results' => $results,
        ));
        return $this->module->display($this->module->getLocalPath(), 'search_suggestion.tpl');
    }
    public function _renderList($tabActive = null)
    {
        if(!$tabActive)
            $tabActive = Tools::getValue('tabActive');
        $defined = new EtsAffDefine();
        $func = 'def_' . $tabActive;
        $config_data = $defined->{$func}();
        if (isset($config_data['list'])) {
            $func = 'def_' . $tabActive;
            $list_data = $defined->{$func}();
            $params = $list_data['list'] + array(
                    'fields_list' => $list_data['fields'],
                );
            $this->renderDatatable($params);
        }
        $this->context->smarty->assign($this->module->getAssign($tabActive));
        return ($this->module->_errors ? $this->module->displayError($this->module->_errors) : '').$this->module->display(_PS_MODULE_DIR_.$this->module->name.DIRECTORY_SEPARATOR.$this->module->name.'.php', 'admin_form.tpl');
    }
    public function renderDatatable($params)
    {
        $link_withdraw = $this->context->link->getAdminLink('AdminEtsAmWithdrawals', true);
        if (isset($params['withdrawal']) && $params['withdrawal'] && ($view = Tools::getValue('view', false)) && Validate::isCleanHtml($view)) {
            if (($id_withdrawal = (int)Tools::getValue('id_withdrawal', false))) {
                $this->context->smarty->assign(
                    array(
                        'user' => Ets_Withdraw::getUserWithdrawal($id_withdrawal),
                        'user_link' => $this->context->link->getAdminLink('AdminEtsAmUsers', true) . '&tabActive=reward_users',
                        'link_withdraw' => $link_withdraw,
                        'id_data' => $id_withdrawal,
                        'link' => $this->context->link,
                    )
                );
                $this->module->_html .= $this->module->display(_PS_MODULE_DIR_.$this->module->name.DIRECTORY_SEPARATOR.$this->module->name.'.php', 'withdrawal_view.tpl');
            }
        } else {
            $type_date_filter = ($type_date_filter = Tools::getValue('type_date_filter')) && in_array($type_date_filter,array('all_times','this_month','this_year','time_ranger')) ? $type_date_filter:'all_times';
            $date_from_reward = ($date_from_reward = Tools::getValue('date_from_reward')) && Validate::isDate($date_from_reward) ? $date_from_reward:'';
            $date_to_reward = ($date_to_reward = Tools::getValue('date_to_reward')) && Validate::isDate($date_to_reward) ? $date_to_reward:'';
            $id_customer = (int)Tools::getValue('id_customer');
            $page = (int)Tools::getValue('page');
            $limit = ($limit = (int)Tools::getValue('limit')) && in_array($limit,array('30','50','100')) ? $limit:30;
            $status = ($status = Tools::getValue('status')) && in_array($status,array('all','1','0','-1','-2')) ? $status: 'all';
            $program = ($program = Tools::getValue('program')) && in_array($program,array('loy','aff','ref','reward_used')) ? $program:'all';
            if($type_date_filter=='all_times' && !$id_customer)
            {
                if(isset($params['withdrawal']) && $params['withdrawal'])
                {
                    $cache = array('list_withdrawal');
                }
                else
                {
                    $cache = array('list_reward',$program);
                }
                $cache['id_employee'] = $this->context->employee->id;
                $cache['page'] = (int)$page;
                $cache['limit'] = $limit;
                $cache['status'] = $status;
                $cacheID = $this->module->_getCacheId($cache);
            }
            else
                $cacheID = null;
            if(isset($params['withdrawal']) && $params['withdrawal'])
                $tpl = 'withdrawal.tpl';
            else
                $tpl ='datatable.tpl';
            if(!$cacheID || !$this->module->isCached($tpl,$cacheID))
            {
                if (isset($params['withdrawal']) && $params['withdrawal']) {
                    $pagination = Ets_Withdraw::getCustomerWithdrawalRequests(null, array(
                        'type_date_filter' => $type_date_filter,
                        'date_from_reward' => $date_from_reward,
                        'date_to_reward' => $date_to_reward,
                        'status' => $status,
                    ), $page, $limit);
                    $placeholder = $this->l('Customer\'s ID or name');
                } else {
                    $filter = array(
                        'type_date_filter' => $type_date_filter,
                        'date_from_reward' => $date_from_reward,
                        'date_to_reward' => $date_to_reward,
                        'program' => Tools::getValue('program'),
                        'status' => $status,
                        'limit' => $limit,
                        'page' => $page,
                        'id_customer' => $id_customer,
                    );
                    $pagination = EtsAmAdmin::getRewardHistory(null, null, false, false, $filter);
                    $placeholder = $this->l('Customer\'s ID or name');
                }
                $this->context->smarty->assign(array(
                    'fields' => $params['fields_list'],
                    'results' => $pagination['results'],
                    'current_page' => $pagination['current_page'],
                    'total_page' => $pagination['total_page'],
                    'total_data' => $pagination['total_data'],
                    'per_page' => $pagination['per_page'],
                    'search' => ($search = Tools::getValue('search', '')) && Validate::isCleanHtml($search) ? $search : '',
                    'limit' => (int)Tools::getValue('limit', 10),
                    'search_placeholder' => $placeholder,
                    'params' => Tools::getAllValues(),
                    'link_customer' => $this->context->link->getAdminLink('AdminModules', true)
                ));
            }
            $this->module->_html .= $this->module->display($this->module->getLocalPath(), $tpl,$cacheID);
        }
    }
}