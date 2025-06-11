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
 * Class AdminEtsAmUsersController
 * @property Ets_affiliatemarketing $module;
 */
require_once dirname(__FILE__) . '/AdminEtsAmAppUsersController.php';
class AdminEtsAmUsersController extends AdminEtsAmAppUsersController
{
    public function init()
    {
        parent::init();
        $this->bootstrap = true;
    }

    public function renderList()
    {
        $tabActive = 'reward_users';
        if (($id_user = (int)Tools::getValue('id_reward_users', false)) && Tools::isSubmit('viewreward_users')) {
            $this->getDetailUser($id_user);
        } else {
            $this->renderListUser();
        }
        $this->context->smarty->assign($this->module->getAssign($tabActive));
        return $this->module->display(_PS_MODULE_DIR_.$this->module->name.DIRECTORY_SEPARATOR.$this->module->name.'.php', 'admin_form.tpl');
    }
    public function renderListUser()
    {
        $params = EtsAffDefine::getInstance()->def_reward_users();
        if (!$params)
            return '';
        $params = $params['list'] + array('fields_list' => $params['fields'], 'toolbar_btn' => isset($params['toolbar_btn']) ? $params['toolbar_btn'] : false);
        $fields_list = isset($params['fields_list']) && $params['fields_list'] ? $params['fields_list'] : false;
        if (!$fields_list)
            return false;
        $helper = new HelperList();
        $helper->title = isset($params['title']) && $params['title'] ? $params['title'] : '';
        $helper->table = isset($params['list_id']) && $params['list_id'] ? $params['list_id'] : $this->list_id;
        $helper->identifier = $params['primary_key'];
        if (version_compare(_PS_VERSION_, '1.6.1', '>=')) {
            $helper->_pagination = array(25, 50, 100);
            $helper->_default_pagination = 25;
        }
        $helper->_defaultOrderBy = $params['orderBy'];
        $this->_processFilter($params);
        //Sort order
        $table_orderBy = $helper->table . 'Orderby';
        $table_orderway = $helper->table . 'Orderway';
        $order_by = urldecode(Tools::getValue($table_orderBy));
        if (!$order_by || !Validate::isCleanHtml($order_by)) {
            if ($this->context->cookie->{$table_orderBy}) {
                $order_by = $this->context->cookie->{$table_orderBy};
            } elseif ($helper->orderBy) {
                $order_by = $helper->orderBy;
            } else {
                $order_by = $helper->_defaultOrderBy;
            }
        }
        $order_way = urldecode(Tools::getValue($table_orderway));
        if (!$order_way || !Validate::isCleanHtml($order_way)) {
            if ($this->context->cookie->{$table_orderway}) {
                $order_way = $this->context->cookie->{$table_orderway};
            } elseif ($helper->orderWay) {
                $order_way = $helper->orderWay;
            } else {
                $order_way = $params['orderWay'];
            }
        }
        if (isset($fields_list[$order_by]) && isset($fields_list[$order_by]['filter_key'])) {
            $order_by = $fields_list[$order_by]['filter_key'];
        }
        //Pagination.
        $key_pagination = $helper->table . '_pagination';
        $limit = (int)Tools::getValue($key_pagination);
        if (!$limit) {
            if (isset($this->context->cookie->{$key_pagination}) && $this->context->cookie->{$key_pagination})
                $limit = $this->context->cookie->{$key_pagination};
            else
                $limit = (version_compare(_PS_VERSION_, '1.6.1', '>=') ? $helper->_default_pagination : 20);
        }
        if ($limit) {
            $this->context->cookie->{$key_pagination} = $limit;
        } else {
            unset($this->context->cookie->{$key_pagination});
        }
        $start = 0;
        $key = $helper->table . '_start';
        $submit = (int)Tools::getValue('submitFilter' . $helper->table);
        if ($submit) {
            $start = ($submit - 1) * $limit;
        } elseif (empty($start) && isset($this->context->cookie->{$key})) {
            $start = $this->context->cookie->{$key};
        }
        if ($start) {
            $this->context->cookie->{$key} = $start;
        } elseif (isset($this->context->cookie->{$key})) {
            unset($this->context->cookie->{$key});
        }
        if (!Validate::isOrderBy($order_by) || !Validate::isOrderWay($order_way)
            || !is_numeric($start) || !is_numeric($limit)) {
            $this->module->_errors[] = $this->l('Parameter list is not valid');
        }
        $helper->orderBy = $order_by;
        $args = array(
            'filter' => $this->_filter,
            'having' => $this->_filterHaving,
        );
        if (isset($params['id_customer']) && $params['id_customer']) {
            $args += array('id_customer' => (int)$params['id_customer']);
        }
        if (isset($params['status'])) {
            $args['status'] = $params['status'];
        }
        $filter_where = "";
        if (!empty($_POST)) {
            $prefix = 'reward_users';
            $point = Ets_AM::usingCustomUnit();
            if (Tools::isSubmit('submitFilter' . $prefix, false)) {
                if (($_reward_balance_min = Tools::getValue($prefix . 'Filter_reward_balance', false)) && Validate::isCleanHtml($_reward_balance_min)) {
                    $filter_where .= " AND (r.reward_balance - COALESCE(ru2.reward_balance, 0) ) >= " . ($point == false ? (float)$_reward_balance_min : ((float)$_reward_balance_min / $point));
                }
                if (($_loy_rewards_min = Tools::getValue($prefix . 'Filter_loy_rewards', false)) && $_loy_rewards_min !== '' && Validate::isCleanHtml($_loy_rewards_min)) {
                    $filter_where .= " AND (r.loy_rewards - COALESCE(ru2.loy_rewards,0) ) >= " . ($point == false ? (float)$_loy_rewards_min : ((float)$_loy_rewards_min / $point));
                }
                if (($_ref_rewards_min = Tools::getValue($prefix . 'Filter_ref_rewards', false)) && $_ref_rewards_min !== '' && Validate::isCleanHtml($_ref_rewards_min)) {
                    $filter_where .= " AND (r.ref_rewards - COALESCE(ru2.ref_rewards,0) ) >= " . ($point == false ? (float)$_ref_rewards_min : ((float)$_ref_rewards_min / $point));
                }
                if (($_aff_rewards_min = Tools::getValue($prefix . 'Filter_aff_rewards', false)) && $_aff_rewards_min !== '' && Validate::isCleanHtml($_aff_rewards_min)) {
                    $filter_where .= " AND (r.aff_rewards - COALESCE(ru2.aff_rewards,0) ) >= " . ($point == false ? (float)$_aff_rewards_min : ((float)$_aff_rewards_min / $point));
                }
                if (($_mnu_rewards_min = Tools::getValue($prefix . 'Filter_mnu_rewards', false)) && $_mnu_rewards_min !== '' && Validate::isCleanHtml($_mnu_rewards_min)) {
                    $filter_where .= " AND (r.mnu_rewards - COALESCE(ru2.mnu_rewards,0)) >= " . ($point == false ? (float)$_mnu_rewards_min : ((float)$_mnu_rewards_min / $point));
                }
                if (($_total_withdraw_min = Tools::getValue($prefix . 'Filter_total_withdraws', false)) && $_total_withdraw_min !== '' && Validate::isCleanHtml($_total_withdraw_min)) {
                    $filter_where .= " AND ru2.total_withdraws >= " . ($point == false ? (float)$_total_withdraw_min : ((float)$_total_withdraw_min / $point));
                }
                if (($_reward_balance_max = Tools::getValue($prefix . 'Filter_reward_balance_max', false)) && $_reward_balance_max !== '' && Validate::isCleanHtml($_reward_balance_max)) {
                    $filter_where .= " AND (r.reward_balance - COALESCE(ru2.reward_balance, 0) ) <= " . ($point == false ? (float)$_reward_balance_max : ((float)$_reward_balance_max / $point));
                }
                if (($_loy_rewards_max = Tools::getValue($prefix . 'Filter_loy_rewards_max', false)) && $_loy_rewards_max !== '' && Validate::isCleanHtml($_loy_rewards_max)) {
                    $filter_where .= " AND (r.loy_rewards - COALESCE(ru2.loy_rewards,0) ) <= " . ($point == false ? (float)$_loy_rewards_max : ((float)$_loy_rewards_max / $point));
                }
                if (($_ref_rewards_max = Tools::getValue($prefix . 'Filter_ref_rewards_max', false)) && $_ref_rewards_max !== '' && Validate::isCleanHtml($_ref_rewards_max)) {
                    $filter_where .= " AND (r.ref_rewards - COALESCE(ru2.ref_rewards,0) ) <= " . ($point == false ? (float)$_ref_rewards_max : ((float)$_ref_rewards_max / $point));
                }
                if (($_aff_rewards_max = Tools::getValue($prefix . 'Filter_aff_rewards_max', false)) && $_aff_rewards_max !== '' && Validate::isCleanHtml($_aff_rewards_max)) {
                    $filter_where .= " AND (r.aff_rewards - COALESCE(ru2.aff_rewards,0) ) <= " . ($point == false ? (float)$_aff_rewards_max : ((float)$_aff_rewards_max / $point));
                }
                if (($_mnu_rewards_max = Tools::getValue($prefix . 'Filter_mnu_rewards_max', false)) && $_mnu_rewards_max !== '' && Validate::isCleanHtml($_mnu_rewards_max)) {
                    $filter_where .= " AND (r.mnu_rewards - COALESCE(ru2.mnu_rewards,0)) <= " . ($point == false ? (float)$_mnu_rewards_max : ((float)$_mnu_rewards_max / $point));
                }
                if (($_total_withdraw_max = Tools::getValue($prefix . 'Filter_total_withdraws_max', false)) && $_total_withdraw_max !== '' && Validate::isCleanHtml($_total_withdraw_max)) {
                    $filter_where .= " AND ru2.total_withdraws <= " . ($point == false ? (float)$_total_withdraw_max : ((float)$_total_withdraw_max / $point));
                }
                $_has_reward = Tools::getValue($prefix . 'Filter_has_reward', false);
                if ($_has_reward !== '' && $_has_reward !== false) {
                    if ((int)$_has_reward == 1) {
                        $filter_where .= " AND r.has_reward =1";
                    } else {
                        $filter_where .= " AND r.has_reward = 0";
                    }
                }
                if (($_id_customer = Tools::getValue($prefix . 'Filter_id_customer', false)) && Validate::isCleanHtml($_id_customer)) {
                    $filter_where .= " AND c.id_customer = " . (int)$_id_customer;
                }
                if (($_username = Tools::getValue($prefix . 'Filter_username', false)) && Validate::isCleanHtml($_username)) {
                    $filter_where .= " AND CONCAT(c.firstname,' ',c.lastname) LIKE '%" . pSQL($_username) . "%'";
                }
                $_status = Tools::getValue($prefix . 'Filter_user_status', false);
                if ($_status !== false && $_status !== '' && Validate::isCleanHtml($_status)) {
                    $filter_where .= " AND (IFNULL(u.status, 1) = " . (int)$_status . " " . ($_status == 1 ? ' OR IFNULL(u.status, 1) is null' : '') . ")";
                }
            }
            if (!Tools::getIsset('submitFilter' . $prefix) && !Tools::getIsset('submitReset' . $prefix)) {
                $filter_where .= " AND r.has_reward  = 1";
                $filter_where .= " AND IFNULL(u.status, 1) =1";
            }
        }
        else
        {
            $filter_where .= " AND r.has_reward  = 1";
            $filter_where .= " AND IFNULL(u.status, 1) =1";
        }
        $helper->listTotal = EtsAmAdmin::{$params['nb']}(true, $filter_where);
        if (!Tools::getIsset('submitFilter' . $helper->table) && !Tools::getIsset('submitReset' . $helper->table)) {
            $this->context->cookie->__set($helper->table . 'Filter_user_status', 1);
            $this->context->cookie->__set($helper->table . 'Filter_has_reward', 1);
        }
        if (Tools::getIsset('submitReset' . $helper->table)) {
            $this->context->cookie->__set($helper->table . 'Filter_has_reward', '');
        }
        $list = EtsAmAdmin::{$params['nb']}(false, $filter_where, Tools::getValue('orderBy'), Tools::getValue('orderWay'), (int)Tools::getValue('page'), (int)Tools::getValue('selected_pagination', 25));
        $helper->orderWay = Tools::strtoupper($order_way);
        $helper->shopLinkType = '';
        $helper->row_hover = true;
        $helper->no_link = $params['no_link'];
        $helper->simple_header = false;
        $helper->actions = !(isset($params['id_customer'])) ? $params['actions'] : array();
        $this->_helperlist = $helper;
        $helper->show_toolbar = false;
        $helper->page = 4;
        $helper->tpl_vars = array(
            'page' => $submit ?: (int)Context::getContext()->cookie->submitFilterreward_users,
        );
        if ($params['toolbar_btn'])
            $helper->toolbar_btn = $params['toolbar_btn'];
        $helper->module = $this->module;
        $helper->token = Tools::getAdminTokenLite('AdminEtsAmUsers');
        $helper->currentIndex = $this->context->link->getAdminLink('AdminEtsAmUsers',false).'&tabActive=reward_users';
        $helper->bulk_actions = $params['bulk_actions'] ? $params['bulk_actions'] : false;
        $helper->actions = array('view', 'active');
        $this->context->smarty->assign(
            array(
                'aff_link_search_customer' => $this->context->link->getAdminLink('AdminEtsAmAffiliate') . '&ajax_search_customer=1',
            )
        );
        if (isset($params['id_customer']) && $params['id_customer']) {
            return $helper->generateList($list, $fields_list);
        }
        $this->module->_html .= (!empty($params['html']) ? $params['html'] : '') . $helper->generateList($list, $fields_list);
    }
    public function _processFilter($params)
    {
        if (empty($params) || empty($params['list_id']))
            return false;
        if (!empty($_POST) && isset($params['list_id'])) {
            foreach ($_POST as $key => $value) {
                if ($value === '') {
                    unset($this->context->cookie->{$key});
                } elseif (stripos($key, $params['list_id'] . 'Filter_') === 0) {
                    $this->context->cookie->{$key} = !is_array($value) ? $value : serialize($value);
                } elseif (stripos($key, 'submitFilter') === 0) {
                    $this->context->cookie->$key = !is_array($value) ? $value : serialize($value);
                }
            }
        }
        if (!empty($_GET) && isset($params['list_id'])) {
            foreach ($_GET as $key => $value) {
                if (stripos($key, $params['list_id'] . 'Filter_') === 0) {
                    $this->maxFilter($key, $value);
                    $this->context->cookie->{$key} = !is_array($value) ? $value : serialize($value);
                } elseif (stripos($key, 'submitFilter') === 0) {
                    $this->context->cookie->{$key} = !is_array($value) ? $value : serialize($value);
                }
                if (stripos($key, $params['list_id'] . 'Orderby') === 0 && Validate::isOrderBy($value)) {
                    if ($value === '' || $value == $params['orderBy']) {
                        unset($this->context->cookie->{$key});
                    } else {
                        $this->context->cookie->{$key} = $value;
                    }
                } elseif (stripos($key, $params['list_id'] . 'Orderway') === 0 && Validate::isOrderWay($value)) {
                    if ($value === '' || $value == $params['orderWay']) {
                        unset($this->context->cookie->{$key});
                    } else {
                        $this->context->cookie->{$key} = $value;
                    }
                }
            }
        }
        $filters = $this->context->cookie->getFamily($params['list_id'] . 'Filter_');
        foreach ($filters as $key => $value) {
            /* Extracting filters from $_POST on key filter_ */
            if ($value != null && !strncmp($key, $params['list_id'] . 'Filter_', 7 + Tools::strlen($params['list_id']))) {
                $key = Tools::substr($key, 7 + Tools::strlen($params['list_id']));
                /* Table alias could be specified using a ! eg. alias!field */
                $tmp_tab = explode('!', $key);
                $filter = count($tmp_tab) > 1 ? $tmp_tab[1] : $tmp_tab[0];
                if (($field = $this->_filterToField($key, $filter, $params['fields_list']))) {
                    $type = (array_key_exists('filter_type', $field) ? $field['filter_type'] : (array_key_exists('type', $field) ? $field['type'] : false));
                    if (($type == 'date' || $type == 'datetime') && is_string($value))
                        $value = Tools::unSerialize($value);
                    $key = isset($tmp_tab[1]) ? $tmp_tab[0] . '.`' . $tmp_tab[1] . '`' : '`' . $tmp_tab[0] . '`';
                    $sql_filter = '';
                    /* Only for date filtering (from, to) */
                    if (is_array($value)) {
                        if (isset($value[0]) && !empty($value[0])) {
                            if (!Validate::isDate($value[0])) {
                                $this->errors[] = Tools::displayError('The \'From\' date format is invalid (YYYY-MM-DD)');
                            } else {
                                $sql_filter .= ' AND ' . pSQL($key) . ' >= \'' . pSQL(Tools::dateFrom($value[0])) . '\'';
                            }
                        }
                        if (isset($value[1]) && !empty($value[1])) {
                            if (!Validate::isDate($value[1])) {
                                $this->errors[] = Tools::displayError('The \'To\' date format is invalid (YYYY-MM-DD)');
                            } else {
                                $sql_filter .= ' AND ' . pSQL($key) . ' <= \'' . pSQL(Tools::dateTo($value[1])) . '\'';
                            }
                        }
                    } else {
                        $sql_filter .= ' AND ';
                        $check_key = ($key == 'id_' . $params['list_id'] || $key == '`id_' . $params['list_id'] . '`');
                        $alias = $params['alias'];
                        if ($type == 'int' || $type == 'bool') {
                            $sql_filter .= (($check_key || $key == '`active`') ? pSQL($alias) . '.' : '') . pSQL($key) . ' = ' . (int)($key == '`position`' ? $value - 1 : $value) . ' ';
                        } elseif ($type == 'decimal') {
                            $sql_filter .= ($check_key ? pSQL($alias) . '.' : '') . pSQL($key) . ' = ' . (float)$value . ' ';
                        } elseif ($type == 'select') {
                            $sql_filter .= ($check_key ? pSQL($alias) . '.' : '') . pSQL($key) . ' = \'' . pSQL($value) . '\' ';
                        } elseif ($type == 'price') {
                            $value = (float)str_replace(',', '.', $value);
                            $sql_filter .= ($check_key ? pSQL($alias) . '.' : '') . pSQL($key) . ' = ' . pSQL(trim($value)) . ' ';
                        } else {
                            $sql_filter .= ($check_key ? pSQL($alias) . '.' : '') . pSQL($key) . ' LIKE \'%' . pSQL(trim($value)) . '%\' ';
                        }
                    }
                    if (isset($field['havingFilter']) && $field['havingFilter'])
                        $this->_filterHaving .= $sql_filter;
                    else
                        $this->_filter .= $sql_filter;
                }
            }
        }
    }
    /**
     * @param $params
     * @return bool
     * @throws PrestaShopException
     */
    public function maxFilter($key, $value)
    {
        $search_max = array(
            'reward_balance',
            'loy_rewards',
            'ref_rewards',
            'ref_orders',
            'aff_rewards',
            'aff_orders',
            'total_withdraws',
        );
        foreach ($search_max as $max) {
            if (stripos($key, $max) !== -1) {
                $index = $key . '_max';
                $this->context->cookie->{$index} = !is_array($value) ? $value : serialize($value);
            }
        }
        $this->context->cookie->write();
    }
    /**
     * @param $key
     * @param $filter
     * @param $fields_list
     * @return bool
     */
    protected function _filterToField($key, $filter, $fields_list)
    {
        if (empty($fields_list))
            return false;
        foreach ($fields_list as $field)
            if (array_key_exists('filter_key', $field) && $field['filter_key'] == $key)
                return $field;
        if (array_key_exists($filter, $fields_list))
            return $fields_list[$filter];
        return false;
    }
    public function getDetailUser($id_customer)
    {
        if (Tools::isSubmit('deduct_reward_by_admin') || Tools::isSubmit('add_reward_by_admin')) {
            $amount = Tools::getValue('amount', false);
            $action = Tools::getValue('action', false);
            $reason = Tools::getValue('reason', false);
            $program = Tools::getValue('type_program', false);
            if (!in_array($program, array('loy', 'aff', 'ref', 'mnu')))
                $program = 'mnu';
            if (!$amount) {
                $this->module->_errors[] = $this->l('Amount is required');
            } elseif (!Validate::isPrice($amount)) {
                if ($action == 'deduct')
                    $this->module->_errors[] = $this->l('The reward is not enough to deduct');
                else
                    $this->module->_errors[] = $this->l('Amount must be a decimal');
            }
            if ($reason && !Validate::isCleanHtml($reason))
                $this->module->_errors[] = $this->l('Reason is not valid');
            if ($action != 'add' && $action != 'deduct')
                $this->module->_errors[] = $this->l('Action is not valid');
            if (!$this->module->_errors) {
                $customer = new Customer($id_customer);
                $type_program = Tools::getValue('type_program');
                if ($action == 'deduct') {
                    $remain = Ets_Reward_Usage::getTotalRemaining($id_customer, $program);
                    if ($remain < $amount) {
                        $this->module->_errors[] = $this->l('Reward remaining not enough to deduct.');
                    } else {
                        $usage = new Ets_Reward_Usage();
                        $usage->amount = $amount;
                        $usage->status = 1;
                        $usage->id_customer = $id_customer;
                        $usage->note = $reason ? $reason : null;
                        $usage->datetime_added = date('Y-m-d H:i:s');
                        $usage->type = $program;
                        $usage->id_shop = $this->context->shop->id;
                        $usage->save(true, true);
                        $this->module->_html .= $this->module->displayConfirmation($this->l('Deducted successfully'));
                        $data = array(
                            '{customer_name}' => $customer->firstname . ' ' . $customer->lastname,
                            '{id_reward}' => $usage->id,
                            '{amount}' => Tools::displayPrice($usage->amount),
                            '{program}' => ($type_program == 'loy' ? $this->l('Loyalty program') : ($type_program == 'aff' ? $this->l('Affiliate program') : ($type_program == 'ref' ? $this->l('Referral program') : '---'))),
                            '{reason}' => $usage->note,
                        );
                        if (Configuration::get('ETS_EMAIL_ADMIN_DEDUCT_REWARD') || Configuration::get('ETS_EMAIL_ADMIN_DEDUCT_REWARD') === false) {
                            $subjects = array(
                                'translation' => $this->l('Admin has deducted a reward from you'),
                                'origin' => 'Admin has deducted a reward from you',
                                'specific' => false
                            );
                            Ets_aff_email::send($customer->id_lang, 'admin_deduct_reward', $subjects, $data, $customer->email);
                        }
                    }
                } elseif ($action == 'add') {
                    $reward = new Ets_AM();
                    $reward->amount = $amount;
                    $reward->note = $reason ? $reason : null;
                    $reward->status = 1;
                    $reward->id_shop = $this->context->shop->id;
                    $reward->id_customer = $id_customer;
                    $reward->program = $program;
                    $reward->datetime_added = date('Y-m-d H:i:s');
                    $reward->datetime_validated = date('Y-m-d H:i:s');
                    $reward->save(true, true);
                    $this->module->_html .= $this->module->displayConfirmation($this->l('Added successfully'));
                    $data = array(
                        '{customer_name}' => $customer->firstname . ' ' . $customer->lastname,
                        '{id_reward}' => $reward->id,
                        '{amount}' => Tools::displayPrice($reward->amount),
                        '{program}' => ($type_program == 'loy' ? $this->l('Loyalty program') : ($type_program == 'aff' ? $this->l('Affiliate program') : ($type_program == 'ref' ? $this->l('Referral program') : '---'))),
                        '{reason}' => $reward->note,
                    );
                    if (Configuration::get('ETS_EMAIL_ADMIN_ADD_REWARD') || Configuration::get('ETS_EMAIL_ADMIN_ADD_REWARD') === false) {
                        $subjects = array(
                            'translation' => $this->l('Admin has added a reward to you'),
                            'origin' => 'Admin has added a reward to you',
                            'specific' => false
                        );
                        Ets_aff_email::send($customer->id_lang, 'admin_add_reward', $subjects, $data, array('customer' => $customer->email));
                    }
                }
            }
        }
        if (($id_parent = Ets_Sponsor::getIdParentByIdCustomer($id_customer)) && ($customerParent = new Customer($id_parent)) && Validate::isLoadedObject($customerParent)) {
            $this->context->smarty->assign(
                array(
                    'customerParent' => $customerParent,
                    'linkParent' => $this->module->getLinkCustomerAdmin($id_parent),
                )
            );
        }
        $currency = Currency::getDefaultCurrency();
        $filter = array(
            'type_date_filter' => Tools::getValue('type_date_filter'),
            'date_from_reward' => Tools::getValue('date_from_reward'),
            'date_to_reward' => Tools::getValue('date_to_reward'),
            'program' => Tools::getValue('program'),
            'status' => Tools::getValue('status'),
            'limit' => (int)Tools::getValue('limit'),
            'page' => (int)Tools::getValue('page'),
        );
        $this->context->smarty->assign(array(
            'user' => EtsAmAdmin::getUserInfo($id_customer),
            'reward_history' => EtsAmAdmin::getRewardHistory($id_customer, null, false, false, $filter),
            'sponsors' => Ets_Sponsor::getDetailSponsors($id_customer),
            'customer_link' => $this->module->getLinkCustomerAdmin($id_customer),
            'order_link' => $this->context->link->getAdminLink('AdminOrders', true),
            'link_admin' => $this->context->link->getAdminLink('AdminEtsAmApp', true),
            'currency' => $currency,
            'id_data' => $id_customer,
            'enable_email_approve_app' => (int)Configuration::get('ETS_AM_ENABLED_EMAIL_RES_REG'),
            'enable_email_decline_app' => (int)Configuration::get('ETS_AM_ENABLED_EMAIL_DECLINE_APP')
        ));
        $this->module->_html .= $this->module->display(_PS_MODULE_DIR_.$this->module->name.DIRECTORY_SEPARATOR.$this->module->name.'.php', 'user/view.tpl');
    }

}
