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
 
if (!defined('_PS_VERSION_')) {
    exit;
}
class EtsAmAdmin extends ObjectModel
{
    /**
     * EtsAmAdmin constructor.
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function __construct()
    {
    }
    /**
     * @param $query
     * @param null $context
     * @throws PrestaShopDatabaseException
     */
    public static function searchProducts($query)
    {
        $context = Context::getContext();
        $query = trim(strip_tags($query));
        if ($query == '')
            exit;
        // search product.
        $imageType =  Ets_affiliatemarketing::getFormattedName('cart');
        $sql = 'SELECT p.`id_product`, pl.`link_rewrite`, p.`reference`, pl.`name`, image_shop.`id_image` id_image, il.`legend`, p.`cache_default_attribute`
        		FROM `' . _DB_PREFIX_ . 'product` p
        		' . Shop::addSqlAssociation('product', 'p') . '
        		LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (pl.id_product = p.id_product AND pl.id_lang = ' . (int)$context->language->id . Shop::addSqlRestrictionOnLang('pl') . ')
        		LEFT JOIN `' . _DB_PREFIX_ . 'image_shop` image_shop
        			ON (image_shop.`id_product` = p.`id_product` AND image_shop.id_shop=' . (int)$context->shop->id . ')
        		LEFT JOIN `' . _DB_PREFIX_ . 'image_lang` il ON (image_shop.`id_image` = il.`id_image` AND il.`id_lang` = ' . (int)$context->language->id . ')
        		WHERE  (pl.id_product LIKE \'%' . pSQL($query) . '%\' OR pl.name LIKE \'%' . pSQL($query) . '%\' OR p.reference LIKE \'%' . pSQL($query) . '%\' ' . (Validate::isInt($query) && (int)$query > 0 ? ' OR p.id_product=' . (int)$query : '') . ') GROUP BY p.id_product LIMIT 10';
        $items = Db::getInstance()->executeS($sql);
        if (is_array($items) && $items) {
            $results = array();
            foreach ($items as $item) {
                if(isset($item['id_image']) && $item['id_image'])
                {
                    $image = $context->link->getImageLink($item['link_rewrite'], $item['id_image'], $imageType);
                }
                else
                    $image = $context->link->getImageLink($item['link_rewrite'], Context::getContext()->language->iso_code.'-default', $imageType);
                $results[] = array(
                    'id_product' => (int)($item['id_product']),
                    'id_product_attribute' => 0,
                    'name' => $item['name'],
                    'attribute' => '',
                    'ref' => (!empty($item['reference']) ? $item['reference'] : ''),
                    'image' => str_replace('http://', Tools::getShopProtocol(), $image),
                );
            }
            if ($results) {
                foreach ($results as &$item) {
                    echo trim($item['id_product'] . '|' . (int)($item['id_product_attribute']) . '|' . Tools::ucfirst($item['name']) . '|' . $item['attribute'] . '|' . $item['ref'] . '|' . $item['image']) . "\n";
                }
            }
        }
        exit;
    }
    /**
     * @param null $prd_id
     * @param null $shop_id
     * @return array|bool|null|object
     */
    public static function getLoyaltySettings($prd_id = null, $shop_id = null)
    {
        $where = ' WHERE 1';
        if ($prd_id) {
            $where .= " AND `id_product` = " . (int)$prd_id;
        }
        if ($shop_id) {
            $where .= " AND `id_shop` = " . (int)$shop_id;
        }
        $sql = "SELECT * FROM `" . _DB_PREFIX_ . "ets_am_loy_reward` " . (string)$where;
        return Db::getInstance()->getRow($sql);
    }
    /**
     * @param null $prd_id
     * @param null $shop_id
     * @return array|bool|null|object
     */
    public static function getAffiliateSettings($prd_id = null, $shop_id = null)
    {
        $where = ' WHERE 1';
        if ($prd_id) {
            $where .= " AND `id_product` = " . (int)$prd_id;
        }
        if ($shop_id) {
            $where .= " AND `id_shop` = " . (int)$shop_id;
        }
        $sql = "SELECT * FROM `" . _DB_PREFIX_ . "ets_am_aff_reward` " . (string)$where;
        return Db::getInstance()->getRow($sql);
    }
    public static function createOrUpdateSetting($type, $params = array())
    {
        if (!$params || !is_array($params)) {
            return false;
        }
        if (!isset($params['id_product']) || !$params['id_product'] || !isset($params['id_shop']) || !$params['id_shop']) {
            return false;
        }
        $id_product = (int)$params['id_product'];
        $id_shop = (int)$params['id_shop'];
        $table = 'ets_am_aff_reward';
        if ($type == 'loyalty') {
            $table = 'ets_am_loy_reward';
            $fields = array('use_default', 'id_product', 'base_on', 'amount', 'qty_min', 'gen_percent', 'id_shop');
        } elseif ($type == 'affiliate') {
            $table = 'ets_am_aff_reward';
            $fields = array('use_default', 'id_product', 'how_to_calculate', 'default_fixed_amount', 'default_percentage', 'single_min_product', 'id_shop');
        }
        //Check setting exists or not
        $sql = "SELECT * FROM `" . _DB_PREFIX_ . pSQL($table) . "` WHERE id_product = " . (int)$id_product . " AND id_shop = " . (int)$id_shop;
        $setting = Db::getInstance()->getRow($sql);
        //Update if exists
        if ($setting) {
            $id_setting = 0;
            $id_col = '';
            if ($table == 'ets_am_aff_reward') {
                $id_setting = (int)$setting['id_ets_am_aff_reward'];
                $id_col = 'id_ets_am_aff_reward';
            } elseif ($table == 'ets_am_loy_reward') {
                $id_setting = (int)$setting['id_ets_am_loy_reward'];
                $id_col = 'id_ets_am_loy_reward';
            }
            $update_data = "";
            $count = 0;
            foreach ($params as $key => $value) {
                if (in_array($key, $fields)) {
                    if ($count < count($params) - 1) {
                        $update_data .= pSQL($key) . " = '" . pSQL($value) . "',";
                    } else {
                        $update_data .= pSQL($key) . " = '" . pSQL($value) . "'";
                    }
                    $count++;
                }
            }
            if ($update_data) {
                $sql_update = "UPDATE `" . _DB_PREFIX_ . pSQL($table) . "` SET " . trim($update_data, ',') . " WHERE " . pSQL($id_col) . " = " . (int)$id_setting;
                return Db::getInstance()->execute($sql_update);
            }
        } else { //Create if not exists
            $create_col = "";
            $create_val = "";
            $count = 0;
            foreach ($params as $key => $value) {
                if (in_array($key, $fields)) {
                    if ($count < count($params) - 1) {
                        $create_col .= pSQL($key) . ",";
                        $create_val .= "'" . pSQL($value) . "',";
                    } else {
                        $create_col .= pSQL($key);
                        $create_val .= "'" . pSQL($value) . "'";
                    }
                    $count++;
                }
            }
            if ($create_col && $create_val) {
                $sql_create = "INSERT INTO `" . _DB_PREFIX_ . pSQL($table) . "` (" . trim($create_col, ',') . ") VALUES(" . trim($create_val, ',') . ")";
                return Db::getInstance()->execute($sql_create);
            }
        }
        return false;
    }
    public static function getDataApplications($filter = array())
    {
        $context = Context::getContext();
        $type_date_filter = isset($filter['type_date_filter']) && in_array($filter['type_date_filter'], array('all_times', 'this_month', 'this_year', 'time_ranger')) ? $filter['type_date_filter'] : 'all_times';
        $date_from_reward = isset($filter['date_from_reward']) && Validate::isDate($filter['date_from_reward']) ? $filter['date_from_reward'] : '';
        $date_to_reward = isset($filter['date_to_reward']) && Validate::isDate($filter['date_to_reward']) ? $filter['date_to_reward'] : '';
        $status = isset($filter['status']) && in_array($filter['status'], array('all', '1', '0', '-1')) ? $filter['status'] : 'all';
        $search = isset($filter['search']) ? $filter['search'] : false;
        $limit = isset($filter['limit']) && (int)$filter['limit'] ? (int)$filter['limit'] : 10;
        $page = isset($filter['page']) && (int)$filter['page'] ? (int)$filter['page'] : 1;
        $filter_where = "p.id_shop = " . (int)$context->shop->id;
        if ($search && is_array($search) && !empty($search['value']) && Validate::isCleanHtml($search['value'])) {
            $q = $search['value'];
            $filter_where .= " AND p.id_ets_am_participation = '" . (int)$q . "' 
                            OR p.status LIKE '%" . pSQL($q) . "%' 
                            OR c.email LIKE '%" . pSQL($q) . "%'";
        }
        if ($status !== false && $status !== 'all') {
            $filter_where .= " AND p.status = " . (int)$status;
        }
        if ($type_date_filter == 'this_month') {
            $filter_where .= " AND p.datetime_added >= '" . pSQL(date('Y-m-01 00:00:00')) . "' AND p.datetime_added <= '" . pSQL(date('Y-m-t 23:59:59')) . "'";
        } else if ($type_date_filter == 'this_year') {
            $filter_where .= " AND p.datetime_added >= '" . pSQL(date('Y-01-01 00:00:00')) . "' AND p.datetime_added <= '" . pSQL(date('Y-12-31 23:59:59')) . "'";
        } else if ($type_date_filter == 'time_ranger' && $date_from_reward && $date_to_reward) {
            $filter_where .= " AND p.datetime_added >= '" . pSQL(date('Y-m-d 00:00:00', strtotime($date_from_reward))) . "' AND p.datetime_added <= '" . pSQL(date('Y-m-d 23:59:59', strtotime($date_to_reward))) . "'";
            $filter_where .= " AND p.datetime_added >= '" . pSQL(date('Y-m-d 00:00:00', strtotime($date_from_reward)) ). "' AND p.datetime_added <= '" . pSQL(date('Y-m-d 23:59:59', strtotime($date_to_reward))) . "'";
        }
        $total_data = (int)Db::getInstance()->executeS("SELECT COUNT(*) as total 
                FROM `" . _DB_PREFIX_ . "ets_am_participation` p
                LEFT JOIN `" . _DB_PREFIX_ . "customer` c ON p.id_customer = c.id_customer
                WHERE $filter_where")[0]['total'];
        $total_page = ceil($total_data / $limit);
        $offset = ($page - 1) * $limit;
        $sql = "SELECT p.id_ets_am_participation as id, p.datetime_added as date_added, p.status as status, p.program as program,
                c.email as email_customer, c.firstname as firstname, c.lastname as lastname, p.id_customer as id_customer
                FROM `" . _DB_PREFIX_ . "ets_am_participation` p
                LEFT JOIN `" . _DB_PREFIX_ . "customer` c ON p.id_customer = c.id_customer
                WHERE $filter_where
                ORDER BY p.id_ets_am_participation DESC
                LIMIT " . (int)$offset . ", " . (int)$limit . "
            ";
        $results = DB::getInstance()->executeS($sql);
        $trans = Module::getInstanceByName('ets_affiliatemarketing')->getTranslates();
        foreach ($results as &$result) {
            $actions = array();
            $actions[] = array(
                'label' => $trans['View'],
                'id' => $result['id'],
                'icon' => 'search',
                'class' => '',
                'action' => '',
                'href' => $context->link->getAdminLink('AdminEtsAmApp', true) . '&id_application=' . $result['id'] . '&viewapp=1'
            );
            if ($result['status'] == 0) {
                $actions[] = array(
                    'label' => $trans['Approve'],
                    'id' => $result['id'],
                    'icon' => 'check',
                    'class' => 'js-btn-action-app',
                    'action' => 'approve'
                );
                $actions[] = array(
                    'label' => $trans['Decline'],
                    'id' => $result['id'],
                    'icon' => 'close',
                    'class' => 'js-btn-action-app',
                    'action' => 'decline'
                );
            }
            $actions[] = array(
                'label' => $trans['Delete'],
                'id' => $result['id'],
                'icon' => 'trash',
                'class' => 'js-btn-action-app',
                'action' => 'delete'
            );
            $result['actions'] = $actions;
            $title_program = '';
            if ($result['program'] == 'loy') {
                $title_program = $trans['loyalty_program'];
            } else if ($result['program'] == 'aff') {
                $title_program = $trans['affiliate_program'];
            } else if ($result['program'] == 'ref') {
                $title_program = $trans['referral_program'];
            }
            $result['program'] = $title_program;
        }
        return array(
            'results' => $results,
            'total_page' => $total_page,
            'total_data' => $total_data,
            'current_page' => $page,
            'per_page' => $limit
        );
    }
    public static function actionCustomer($id, $action, $reason = null)
    {
        $context = Context::getContext();
        $module = Module::getInstanceByName('ets_affiliatemarketing');
        $trans = $module->getTranslates();
        if ($id) {
            $p = new Ets_Participation($id);
            if (!Validate::isLoadedObject($p)) {
                return array(
                    'success' => false,
                    'actions' => array()
                );
            }
            $actions = array();
            $actions[] = array(
                'label' => $trans['View'],
                'id' => $id,
                'icon' => 'search',
                'class' => '',
                'action' => '',
                'href' => $context->link->getAdminLink('AdminEtsAmApp', true) . '&id_application=' . (int)$id . '&viewapp=1'
            );
            $actions[] = array(
                'label' => $trans['Delete'],
                'id' => $id,
                'icon' => 'trash',
                'class' => 'js-btn-action-app',
                'action' => 'delete'
            );
            if ($action == 'approve') {
                $p->status = 1;
                $p->update();
                $userExists = Ets_User::getUserByCustomerId($p->id_customer);
                if ($userExists && $userExists['status'] != -1) {
                    $user = new Ets_User((int)$userExists['id_ets_am_user']);
                    $index = $p->program;
                    $user->{$index} = 1;
                    $user->status = 1;
                    $user->id_shop = $context->shop->id;
                    $user->update();
                } else if (!$userExists) {
                    $user = new Ets_User();
                    $user->id_customer = $p->id_customer;
                    $index = $p->program;
                    $user->{$index} = 1;
                    $user->id_shop = $context->shop->id;
                    $user->status = 1;
                    $user->add();
                }
                //Send mail
                self::sendMailApplicationApprovedWithoutConfig($id, $reason);
                return array(
                    'success' => true,
                    'actions' => $actions
                );
            } elseif ($action == 'delete') {
                if ($p->delete()) {
                    Db::getInstance()->execute("UPDATE `" . _DB_PREFIX_ . "ets_am_user` SET " . pSQL($p->program) . " = 0 WHERE id_shop = " . (int)$context->shop->id . " AND id_customer = " . (int)$p->id_customer);
                }
                return array(
                    'success' => true,
                    'actions' => $actions
                );
            } elseif ($action == 'decline') {
                $p->status = -1;
                $p->update();
                $program = '';
                if ($p->program == 'loy') {
                    $program = $trans['loyalty_program'];
                } elseif ($p->program == 'ref') {
                    $program = $trans['referral_program'];
                } elseif ($p->program == 'aff') {
                    $program = $trans['affiliate_program'];
                }
                if ($user = Ets_User::getUserByCustomerId($p->id_customer)) {
                    Db::getInstance()->execute("UPDATE `" . _DB_PREFIX_ . "ets_am_user` SET " . pSQL($p->program) . " = -2 WHERE id_shop = " . (int)$context->shop->id . " AND id_customer = " . (int)$p->id_customer);
                } else {
                    $user = new Ets_User();
                    $user->id_customer = $p->id_customer;
                    $index = $p->program;
                    $user->{$index} = -2;
                    $user->status = 1;
                    $user->id_shop = $context->shop->id;
                    $user->add();
                }
                $customer = new Customer($p->id_customer);
                $data = array(
                    '{title}' => 'Your application declined',
                    '{username}' => $customer->firstname . ' ' . $customer->lastname,
                    '{email}' => $customer->email,
                    '{reason}' => $reason,
                    '{date_declined}' => date('Y-m-d H:i:s'),
                    '{program}' => $program,
                );
                $subject = array(
                    'translation' => $module->l('Your application was declined', 'etsamadmin'),
                    'origin' => 'Your application was declined',
                    'specific' => 'etsamadmin',
                );
                Ets_aff_email::send($customer->id_lang, 'application_declined', $subject, $data, $customer->email);
                return array(
                    'success' => true,
                    'actions' => $actions
                );
            }
        }
        return array(
            'success' => false,
            'actions' => array()
        );
    }
    public static function sendMailApplicationApprovedWithoutConfig($id_app, $reason = '')
    {
        $module = Module::getInstanceByName('ets_affiliatemarketing');
        $trans = $module->getTranslates();
        if ($id_app) {
            $sql = "SELECT pa.*, customer.firstname as firstname, customer.lastname as lastname, customer.email as email,customer.id_lang
                    FROM `" . _DB_PREFIX_ . "ets_am_participation` pa 
                    JOIN `" . _DB_PREFIX_ . "customer` customer ON pa.id_customer = customer.id_customer
                    WHERE pa.id_ets_am_participation = " . (int)$id_app;
            $app = Db::getInstance()->getRow($sql);
            if ($app) {
                $program = '';
                if ($app['program'] == 'loy') {
                    $program = $trans['loyalty_program'];
                } elseif ($app['program'] == 'ref') {
                    $program = $trans['referral_program'];
                } elseif ($app['program'] == 'aff') {
                    $program = $trans['affiliate_program'];
                } elseif ($app['program'] == 'anr') {
                    $program = $trans['referral_and_affiliate_program'];
                }
                $data = array(
                    '{title}' => 'Your account is approved to use services of affiliate marketing',
                    '{username}' => $app['firstname'] . ' ' . $app['lastname'],
                    '{email}' => $app['email'],
                    '{status}' => 'Approved',
                    '{date_created}' => $app['datetime_added'],
                    '{reason}' => $reason,
                    '{program}' => $program,
                );
                $email = $app['email'];
                $subject = array(
                    'translation' => $module->l('Your application was approved', 'etsamadmin'),
                    'origin' => 'Your application was approved',
                    'specific' => 'etsamadmin',
                );
                Ets_aff_email::send($app['id_lang'], 'application_approved', $subject, $data, trim($email));
            }
        }
    }
    /**
     * @param null $id_user
     * @param null $program
     * @param bool $frontend
     * @return array
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public static function getRewardHistory($id_user = null, $program = null, $frontend = false, $get_product_name = false, $filter = array())
    {
        $context = Context::getContext();
        $query = array();
        $type_date_filter = isset($filter['type_date_filter']) && in_array($filter['type_date_filter'], array('all_times', 'this_year', 'this_month', 'time_ranger')) ? $filter['type_date_filter'] : 'all_times';
        $date_from_reward = isset($filter['date_from_reward']) && $filter['date_from_reward'] && Validate::isDate($filter['date_from_reward']) ? $filter['date_from_reward'] : '';
        $date_to_reward = isset($filter['date_to_reward']) && $filter['date_to_reward'] && Validate::isDate($filter['date_to_reward']) ? $filter['date_to_reward'] : '';
        $program = isset($filter['program']) && in_array($filter['program'], array('loy', 'ref', 'aff', 'reward_used')) ? $filter['program'] : $program;
        $status = isset($filter['status']) && in_array($filter['status'], array('1', '0', '-1', '-2')) ? $filter['status'] : false;
        if ($status !== false) {
            $query['status'] = (int)$status;
        } else {
            $query['status'] = 'all';
        }
        $query['type_date_filter'] = $type_date_filter;
        $query['date_from_reward'] = $date_from_reward;
        $query['date_to_reward'] = $date_to_reward;
        $query['program'] = $date_to_reward;
        $id_customer = !$frontend && isset($filter['id_customer']) && (int)$filter['id_customer'] ? (int)$filter['id_customer'] : 0;
        $limit = isset($filter['limit']) && (int)$filter['limit'] ? (int)$filter['limit'] : 30;
        $page = isset($filter['page']) && (int)$filter['page'] ? $filter['page'] : 1;
        $offset = ($page - 1) * $limit;
        $filter_where = " AND reward.`deleted` = 0 AND reward.id_shop = " . (int)$context->shop->id;
        if ((int)$id_user) {
            $filter_where .= " AND reward.id_customer = " . (int)$id_user;
        }
        if ($program && $program !== 'all') {
            if ($program != 'reward_used') {
                $filter_where .= " AND reward.program = '" . pSQL($program) . "'";
            } else {
                $filter_where .= " AND reward.type = 'usage'";
            }
        }
        if ((int)$id_customer) {
            $filter_where .= " AND reward.id_customer = " . (int)$id_customer;
        }
        if ($status !== false && $status !== 'all' && $program != 'reward_used') {
            $filter_where .= " AND reward.type = 'reward' AND reward.status = " . (int)$status;
        }
        if ($type_date_filter == 'this_month') {
            $filter_where .= " AND reward.datetime_added >= '" . pSQL(date('Y-m-01 00:00:00')) . "' AND reward.datetime_added <= '" . pSQL(date('Y-m-t 23:59:59')) . "'";
        } else if ($type_date_filter == 'this_year') {
            $filter_where .= " AND reward.datetime_added >= '" . pSQL(date('Y-01-01 00:00:00')) . "' AND reward.datetime_added <= '" . pSQL(date('Y-12-31 23:59:59')) . "'";
        } else if ($type_date_filter == 'time_ranger' && $date_from_reward && $date_to_reward) {
            $filter_where .= " AND reward.datetime_added >= '" . pSQL(date('Y-m-d 00:00:00', strtotime($date_from_reward))) . "' AND reward.datetime_added <= '" . pSQL(date('Y-m-d 23:59:59', strtotime($date_to_reward))) . "'";
        }
        $resultTotal = DB::getInstance()->executeS("SELECT COUNT(*) as total
                FROM 
                (SELECT DISTINCT id_ets_am_reward as id, 'reward' as type, program, status,id_customer, amount, id_order,id_shop,deleted,note,datetime_added, expired_date FROM `" . _DB_PREFIX_ . "ets_am_reward`                         UNION ALL
                    SELECT id_ets_am_reward_usage as id ,'usage' as type, type as program, status ,id_customer, SUM(amount) as amount, id_order,id_shop,deleted,note,datetime_added, NULL as expired_date FROM `" . _DB_PREFIX_ . "ets_am_reward_usage` 
                ) reward 
                LEFT JOIN `" . _DB_PREFIX_ . "orders` orders ON reward.id_order = orders.id_order
                LEFT JOIN `" . _DB_PREFIX_ . "order_state` order_state ON orders.current_state = order_state.id_order_state
                LEFT JOIN `" . _DB_PREFIX_ . "order_state_lang` order_state_lang ON order_state.id_order_state = order_state_lang.id_order_state
                WHERE (CASE
                    WHEN order_state_lang.id_lang IS NULL
                    THEN 1
                    ELSE order_state_lang.id_lang = " . (int)Configuration::get('PS_LANG_DEFAULT') . "
                    END
                )" . (string)$filter_where);
        $total_data = (int)$resultTotal[0]['total'];
        $total_page = ceil($total_data / $limit);
        $sql = "SELECT reward.*, order_state_lang.name as order_state, orders.reference as order_reference,
                customer.firstname as firstname, customer.lastname as lastname
                FROM
                (
                    SELECT DISTINCT  r.id_ets_am_reward as id, 'reward' as type, r.program, r.status,id_customer,
                    IF(rp.amount IS NOT NULL AND rp.amount > 0, rp.amount*rp.quantity, r.amount) as amount, r.id_order,r.id_shop,deleted,note,r.datetime_added, expired_date,pl.name as product_name,rp.id_product FROM `" . _DB_PREFIX_ . "ets_am_reward` r
                    LEFT JOIN `" . _DB_PREFIX_ . "ets_am_reward_product` rp ON (rp.id_ets_am_reward = r.id_ets_am_reward)
                    LEFT JOIN `" . _DB_PREFIX_ . "product_lang` pl ON (pl.id_product= rp.id_product AND pl.id_lang =" . (int)$context->language->id . " AND pl.id_shop =" . (int)$context->shop->id . ")
                    UNION ALL
                    SELECT id_ets_am_reward_usage as id ,'usage' as type,type as program, status ,id_customer, amount as amount, id_order,id_shop,deleted,note,datetime_added, '' as expired_date,'' as product_name,'' as id_product  FROM `" . _DB_PREFIX_ . "ets_am_reward_usage`                 ) reward 
                LEFT JOIN `" . _DB_PREFIX_ . "orders` orders ON reward.id_order = orders.id_order
                LEFT JOIN `" . _DB_PREFIX_ . "order_state` order_state ON orders.current_state = order_state.id_order_state
                LEFT JOIN `" . _DB_PREFIX_ . "order_state_lang` order_state_lang ON order_state.id_order_state = order_state_lang.id_order_state
                LEFT JOIN `" . _DB_PREFIX_ . "customer` customer ON reward.id_customer = customer.id_customer
                WHERE (CASE
                    WHEN order_state_lang.id_lang IS NULL
                    THEN 1
                    ELSE order_state_lang.id_lang = " . (int)Configuration::get('PS_LANG_DEFAULT') . "
                    END
                )" . (string)$filter_where .
            " ORDER BY reward.datetime_added DESC
                 LIMIT " . (int)$offset . "," . (int)$limit;
        $results = DB::getInstance()->executeS($sql);
        $trans = Module::getInstanceByName('ets_affiliatemarketing')->getTranslates();
        foreach ($results as &$result) {
            $result['query'] = $query;
            $result['id_ets_am_reward'] = $result['type'] == 'reward' ? 'R-' . $result['id'] : 'S-' . $result['id'];
            $id_order = $result['id_order'];
            $actions = array();
            if ($get_product_name) {
                $products = self::getRewardProductByOrderId($id_order, $result['program'], $context);
                $result['products'] = $products;
            }
            $expired = false;
            if ($result['expired_date'] && $result['expired_date'] !== '000-00-00 00:00:00') {
                $today = date("Y-m-d H:i:s");
                $startdate = $result['expired_date'];
                $offset = strtotime("+1 day");
                $enddate = date($startdate, $offset);
                $today_date = new DateTime($today);
                $expiry_date = new DateTime($enddate);
                if ($expiry_date < $today_date) {
                    $expired = true;
                }
            }
            if ($result['status'] == -2 || $expired) {
                if ($result['type'] == 'reward') {
                    if ($result['firstname']) {
                        $actions[] = array(
                            'label' => $trans['Approve'],
                            'class' => 'js-approve-reward-item',
                            'id' => $result['id'],
                            'icon' => 'check'
                        );
                    }
                    $actions[] = array(
                        'label' => $trans['Cancel'],
                        'class' => 'js-cancel-reward-item',
                        'id' => $result['id'],
                        'icon' => 'times'
                    );
                }
            } else if ($result['status'] == -1) {
                if ($result['type'] == 'reward') {
                    if ($result['firstname']) {
                        $actions[] = array(
                            'label' => $trans['Approve'],
                            'class' => 'js-approve-reward-item',
                            'id' => $result['id'],
                            'icon' => 'check'
                        );
                    }
                } else {
                    if ($result['firstname']) {
                        $actions[] = array(
                            'label' => $trans['Approve'],
                            'class' => 'js-approve-reward-usage-item',
                            'id' => $result['id'],
                            'icon' => 'check'
                        );
                    }
                }
            } else if ($result['status'] == 0) {
                if ($result['type'] == 'reward') {
                    if ($result['firstname']) {
                        $actions[] = array(
                            'label' => $trans['Approve'],
                            'class' => 'js-approve-reward-item',
                            'id' => $result['id'],
                            'icon' => 'check'
                        );
                    }
                    $actions[] = array(
                        'label' => $trans['Cancel'],
                        'class' => 'js-cancel-reward-item',
                        'id' => $result['id'],
                        'icon' => 'times'
                    );
                } else {
                    if ($result['firstname']) {
                        $actions[] = array(
                            'label' => $trans['Approve'],
                            'class' => 'js-approve-reward-usage-item',
                            'id' => $result['id'],
                            'icon' => 'check'
                        );
                    }
                    $actions[] = array(
                        'label' => $trans['Cancel'],
                        'class' => 'js-cancel-reward-usage-item',
                        'id' => $result['id'],
                        'icon' => 'times'
                    );
                }
            } else {
                if ($result['type'] == 'reward') {
                    $actions[] = array(
                        'label' => $trans['Cancel'],
                        'class' => 'js-cancel-reward-item',
                        'id' => $result['id'],
                        'icon' => 'times'
                    );
                } else {
                    $actions[] = array(
                        'label' => $trans['Cancel'],
                        'class' => 'js-cancel-reward-usage-item',
                        'id' => $result['id'],
                        'icon' => 'times'
                    );
                }
            }
            if ($result['type'] == 'reward') {
                $actions[] = array(
                    'label' => $trans['Delete'],
                    'class' => 'js-delete-reward-item',
                    'id' => $result['id'],
                    'icon' => 'trash'
                );
            } else {
                $actions[] = array(
                    'label' => $trans['Delete'],
                    'class' => 'js-delete-reward-usage-item',
                    'id' => $result['id'],
                    'icon' => 'trash'
                );
            }
            if ($result['program'] == 'ref') {
                $result['program'] = $trans['referral_program'];
            } elseif ($result['program'] == 'aff') {
                $result['program'] = $trans['affiliate_program'];
            } elseif ($result['program'] == 'loy') {
                $result['program'] = $trans['loyalty_program'];
            } elseif ($result['program'] == 'mnu') {
                $result['program'] = '--';
            } elseif ($result['program'] == 'anr') {
                $result['program'] = $trans['referral_and_affiliate_program'];
            }
            $result['order_reference'] = $result['order_reference'] ? $result['order_reference'] : '-';
            $result['order_state'] = $result['order_state'] ? $result['order_state'] : '-';
            $result['actions'] = $actions;
            if ($result['type'] == 'usage') {
                $result['amount'] = '-' . ($frontend ? Ets_AM::displayReward($result['amount'], true) : Ets_AM::displayRewardAdmin($result['amount']));
            } else {
                $result['amount'] = $frontend ? Ets_AM::displayReward($result['amount'], true) : Ets_AM::displayRewardAdmin($result['amount']);
            }
            if ($result['product_name'] && $result['id_product']) {
                $result['product_name'] = EtsAffDefine::displayText($result['product_name'], 'a', '', '', $context->link->getProductLink($result['id_product']));
            } else
                $result['product_name'] = '--';
        }
        return array(
            'results' => $results,
            'total_page' => (int)$total_page,
            'total_data' => $total_data,
            'current_page' => $page,
            'per_page' => $limit,
            'query' => $query
        );
    }
    public static function getRewardProductByOrderId($id_order, $program)
    {
        $context = Context::getContext();
        $sql = "SELECT  rp.id_product, pl.name FROM `" . _DB_PREFIX_ . "ets_am_reward_product` rp 
        INNER JOIN `" . _DB_PREFIX_ . "product_lang` pl ON rp.id_product = pl.id_product 
        WHERE pl.id_lang = " . (int)$context->language->id . " AND rp.id_order = " . (int)$id_order . " AND `program` = '" . pSQL($program) . "' AND pl.id_shop = " . (int)$context->shop->id;
        $results = Db::getInstance()->executeS($sql);
        $response = array();
        if ($results) {
            foreach ($results as $result) {
                $p_link = Ets_Affiliate::generateAffiliateLinkForProduct(new Product($result['id_product']), $context, false);
                $response[] = array(
                    'link' => $p_link,
                    'name' => $result['name']
                );
            }
        }
        return $response;
    }
    public static function getTotalPendingApplications()
    {
        $sql = "SELECT COUNT(*) as total FROM `" . _DB_PREFIX_ . "ets_am_participation` WHERE status = 0";
        $total = (int)Db::getInstance()->getValue($sql);
        return $total;
    }
    public static function getListPaymentMethods()
    {
        $context = Context::getContext();
        $id_shop = (int)$context->shop->id;
        $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
        $sql = "SELECT pm.* FROM `" . _DB_PREFIX_ . "ets_am_payment_method` pm WHERE pm.id_shop = " . (int)$id_shop . " AND pm.`deleted` = 0
                ORDER BY pm.sort ASC";
        $results = Db::getInstance()->executeS($sql);
        $pml = Db::getInstance()->executeS("SELECT id_payment_method, title, id_lang FROM `" . _DB_PREFIX_ . "ets_am_payment_method_lang` pml");
        foreach ($results as &$result) {
            $result['title'] = '';
            if ($pml) {
                foreach ($pml as $p) {
                    if ($p['id_payment_method'] == $result['id_ets_am_payment_method']) {
                        if ($p['title']) {
                            $result['title'] = $p['title'];
                            if ($p['id_lang'] == $default_lang) {
                                break;
                            }
                        }
                    }
                }
            }
            $result['fee_fixed'] = Ets_affiliatemarketing::displayPrice($result['fee_fixed'], (int)Configuration::get('PS_CURRENCY_DEFAULT'));
            $result['fee_percent'] = (float)$result['fee_percent'];
        }
        return $results;
    }
    public static function getPaymentMethod($id)
    {
        $sql = "SELECT * FROM `" . _DB_PREFIX_ . "ets_am_payment_method` WHERE id_ets_am_payment_method = " . (int)$id;
        $payment_method = Db::getInstance()->getRow($sql);
        if ($payment_method) {
            $payment_method['fee_percent'] = (float)$payment_method['fee_percent'];
            $payment_method['fee_fixed'] = (float)$payment_method['fee_fixed'];
            $sqlLang = "SELECT * FROM `" . _DB_PREFIX_ . "ets_am_payment_method_lang` WHERE id_payment_method = " . (int)$id;
            $payment_method_langs = Db::getInstance()->executeS($sqlLang);
            $payment_method['langs'] = array();
            foreach ($payment_method_langs as $pml) {
                $payment_method['langs'][$pml['id_lang']] = array(
                    'id' => $pml['id_ets_am_payment_method_lang'],
                    'title' => $pml['title'],
                    'description' => $pml['description'],
                    'note' => $pml['note'],
                    'id_lang' => $pml['id_lang'],
                );
            }
        }
        return $payment_method;
    }
    public static function getListPaymentMethodField($id_pm, $id_lang = null)
    {
        $filter_where = '';
        if ($id_lang) {
            $filter_where .= "AND pmfl.id_lang = " . (int)$id_lang;
        }
        $sql = "SELECT pmf.*, pmfl.title, pmfl.description, pmfl.id_lang FROM (
            SELECT * FROM `" . _DB_PREFIX_ . "ets_am_payment_method_field` WHERE id_payment_method = " . (int)$id_pm . " AND `deleted` = 0
        ) pmf
        JOIN `" . _DB_PREFIX_ . "ets_am_payment_method_field_lang` pmfl ON pmf.id_ets_am_payment_method_field = pmfl.id_payment_method_field
        WHERE 1 " . (string)$filter_where . "
        ORDER BY pmf.sort ASC";
        $payment_method_fields = Db::getInstance()->executeS($sql);
        if (!$id_lang && $payment_method_fields) {
            $results = array();
            foreach ($payment_method_fields as $field) {
                $results[$field['id_ets_am_payment_method_field']]['id'] = $field['id_ets_am_payment_method_field'];
                $results[$field['id_ets_am_payment_method_field']]['type'] = $field['type'];
                $results[$field['id_ets_am_payment_method_field']]['enable'] = $field['enable'];
                $results[$field['id_ets_am_payment_method_field']]['description'][$field['id_lang']] = $field['description'];
                $results[$field['id_ets_am_payment_method_field']]['required'] = $field['required'];
                $results[$field['id_ets_am_payment_method_field']]['title'][$field['id_lang']] = $field['title'];
            }
            return $results;
        }
        return $payment_method_fields;
    }
    public static function updatePaymentMethod($id_pm, $pm_title = array(), $pm_fee_type = null, $pm_fee_fixed = null, $pm_fee_percent = null, $pm_enable = null, $pm_estimated = null, $pm_desc = array(), $pm_fields = array(), $pm_note = array())
    {
        if ($id_pm) {
            $languages = Language::getLanguages(false);
            //update
            if ($pm_fee_type != 'FIXED' && $pm_fee_type != 'PERCENT' && $pm_fee_type != 'NO_FEE')
                $pm_fee_type = 'NO_FEE';
            $pm = new Ets_PaymentMethod($id_pm);
            $pm->fee_type = $pm_fee_type;
            $pm->fee_fixed = $pm_fee_fixed;
            $pm->fee_percent = $pm_fee_percent;
            $pm->estimated_processing_time = $pm_estimated;
            $pm->enable = $pm_enable;
            $pm->update();
            $sqls = array();
            foreach ($languages as $lang) {
                $id_lang = (int)$lang['id_lang'];
                $exists = Db::getInstance()->getRow("SELECT * FROM `" . _DB_PREFIX_ . "ets_am_payment_method_lang` WHERE id_payment_method =" . (int)$id_pm . " AND id_lang = " . (int)$id_lang);
                if ($exists) {
                    $sqls[] = "UPDATE `" . _DB_PREFIX_ . "ets_am_payment_method_lang` SET title = '" . pSQL(isset($pm_title[$id_lang]) ? $pm_title[$id_lang] : '') . "', description = '" . pSQL(isset($pm_desc[$id_lang]) ? $pm_desc[$id_lang] : '') . "', note = '" . pSQL(isset($pm_note[$id_lang]) ? $pm_note[$id_lang] : '') . "' WHERE id_payment_method = " . (int)$id_pm . " AND id_lang =" . (int)$id_lang . ";";
                } else {
                    $sqls[] = "INSERT INTO `" . _DB_PREFIX_ . "ets_am_payment_method_lang` (id_payment_method, title, description, note,id_lang) VALUES (" . (int)$id_pm . ", '" . pSQL(isset($pm_title[$id_lang]) ? $pm_title[$id_lang] : '') . "', '" . pSQL(isset($pm_desc[$id_lang]) ? $pm_desc[$id_lang] : '') . "', '" . pSQL(isset($pm_note[$id_lang]) ? $pm_note[$id_lang] : '') . "', " . (int)$id_lang . ");";
                }
            }
            if ($sqls) {
                foreach($sqls as $sql)
                    Db::getInstance()->execute($sql);
            }
            if ($pm_fields) {
                $sql_fields = array();
                $ids_live = array(0);
                foreach ($pm_fields as $key => $field) {
                    if (isset($field['id']) && $field['id']) {
                        $ids_live[] = (int)$field['id'];
                    }
                }
                //Set deleted for item deleted
                if($ids_live)
                Db::getInstance()->execute("UPDATE `" . _DB_PREFIX_ . "ets_am_payment_method_field` SET `deleted` = 1 WHERE id_ets_am_payment_method_field NOT IN (" . implode(',', array_map('intval', $ids_live)) . ") AND id_payment_method = " . (int)$id_pm);
                //Update data
                foreach ($pm_fields as $key => $field) {
                    if (isset($field['id']) && $field['id']) {
                        if ($field['type']) {
                            $sql_fields[] = " UPDATE `" . _DB_PREFIX_ . "ets_am_payment_method_field` SET type = '" . pSQL($field['type']) . "', enable = " . (int)$field['enable'] . ", required = " . (int)$field['required'] . " WHERE id_ets_am_payment_method_field = " . (int)$field['id'] . ";";
                        }
                        $default_title = '';
                        foreach ($languages as $lang) {
                            $id_lang = $lang['id_lang'];
                            if (Db::getInstance()->getRow('SELECT * FROM `' . _DB_PREFIX_ . 'ets_am_payment_method_field_lang` WHERE id_payment_method_field ="' . (int)$field['id'] . '" AND id_lang="' . (int)$id_lang . '"'))
                                $sql_fields[] = " UPDATE `" . _DB_PREFIX_ . "ets_am_payment_method_field_lang` SET title = '" . pSQL(isset($field['title'][$id_lang]) ? $field['title'][$id_lang] : '') . "', description = '" . pSQL(isset($field['description'][$id_lang]) ? $field['description'][$id_lang] : '') . "' WHERE id_payment_method_field = " . (int)$field['id'] . " AND id_lang = " . (int)$id_lang . ";";
                            else
                                $sql_fields[] = " INSERT INTO `" . _DB_PREFIX_ . "ets_am_payment_method_field_lang` (id_payment_method_field, id_lang, title, description) VALUES(" . (int)$field['id'] . ", " . (int)$id_lang . ", '" . pSQL(isset($field['title'][$id_lang]) && $field['title'][$id_lang] ? pSQl($field['title'][$id_lang]) : pSQL($default_title)) . "', '" . pSQL(isset($field['description'][$id_lang]) ? pSQL($field['description'][$id_lang], true) : '') . "');";
                        }
                    } else {
                        $sql_fields[] = "INSERT INTO `" . _DB_PREFIX_ . "ets_am_payment_method_field` (id_payment_method, `type`, enable, required) VALUES (" . (int)$id_pm . ", '" . pSQL($field['type']) . "', " . (int)$field['enable'] . ", " . (int)$field['required'] . ");";
                        $sql_fields[] = "SET @id_pmf_" . pSQL($key) . " = LAST_INSERT_ID()";
                        $default_title = '';
                        foreach ($field['title'] as $ft) {
                            if ($ft) {
                                $default_title = $ft;
                                break;
                            }
                        }
                        foreach ($languages as $lang) {
                            $id_lang = $lang['id_lang'];
                            $sql_fields[] = " INSERT INTO `" . _DB_PREFIX_ . "ets_am_payment_method_field_lang` (id_payment_method_field, id_lang, title, description) VALUES(@id_pmf_" . (string)$key . ", " . (int)$id_lang . ", '" . pSQL(isset($field['title'][$id_lang]) && $field['title'][$id_lang] ? $field['title'][$id_lang] : $default_title) . "', '" . pSQL(isset($field['description'][$id_lang]) ? $field['description'][$id_lang] : '') . "');";
                        }
                    }
                }
                if ($sql_fields) {
                    foreach($sql_fields as $sql_field)
                        Db::getInstance()->execute($sql_field);
                }
            }
        }
        return true;
    }
    public static function createPaymentMethod($pm_title = array(), $pm_fee_type = null, $pm_fee_fixed = null, $pm_fee_percent = null, $pm_enable = null, $pm_estimated = null, $pm_desc = array(), $pm_note = array())
    {
        //update
        $max_sort = (int)Db::getInstance()->getValue("SELECT MAX(sort) as max_sort FROM " . _DB_PREFIX_ . "ets_am_payment_method");
        $pm = new Ets_PaymentMethod();
        $pm->fee_type = $pm_fee_type;
        $pm->fee_fixed = $pm_fee_fixed;
        $pm->fee_percent = $pm_fee_percent;
        $pm->estimated_processing_time = $pm_estimated;
        $pm->enable = $pm_enable;
        $pm->sort = $max_sort + 1;
        $pm->id_shop = Context::getContext()->shop->id;
        $pm->add();
        $id_pm = $pm->id;
        $sqls = array();
        $default_title = null;
        foreach ($pm_title as $pt) {
            if ($pt) {
                $default_title = $pt;
                break;
            }
        }
        $languages = Language::getLanguages(false);
        foreach ($languages as $lang) {
            $desc = isset($pm_desc[$lang['id_lang']]) && $pm_desc[$lang['id_lang']] ? $pm_desc[$lang['id_lang']] : null;
            $title = isset($pm_title[$lang['id_lang']]) && $pm_title[$lang['id_lang']] ? $pm_title[$lang['id_lang']] : $default_title;
            $note = isset($pm_note[$lang['id_lang']]) && $pm_note[$lang['id_lang']] ? $pm_note[$lang['id_lang']] : null;
            $sqls[] = "INSERT INTO `" . _DB_PREFIX_ . "ets_am_payment_method_lang` (id_payment_method, title, description, note, id_lang) VALUES (" . (int)$id_pm . ", '" . pSQL($title) . "', '" . pSQL($desc) . "', '" . pSQL($note) . "'," . (int)$lang['id_lang'] . ");";
        }
        if ($sqls) {
            foreach($sqls as $sql)
                Db::getInstance()->execute($sql);
        }
        return $id_pm;
    }
    public static function deletePaymentMethod($id_pm)
    {
        if ($id_pm) {
            $pm = new Ets_PaymentMethod($id_pm);
            $pm->deleted = 1;
            $pm->update();
        }
        return true;
    }
    public static function updateSortPaymentMethod($data)
    {
        if ($data) {
            $sqls = array();
            foreach ($data as $key => $item) {
                $index = $key + 1;
                $item = (int)$item;
                $sqls[] = " UPDATE `" . _DB_PREFIX_ . "ets_am_payment_method` SET sort = " . (int)$index . " WHERE id_ets_am_payment_method =" . (int)$item;
            }
            if ($sqls) {
                foreach($sqls as $sql)
                    Db::getInstance()->execute($sql);
                return true;
            }
        }
        return false;
    }
    public static function updateSortPaymentMethodfield($data)
    {
        if ($data) {
            $sqls = array();
            foreach ($data as $key => $item) {
                $index = $key + 1;
                $item = (int)$item;
                $sqls[] = " UPDATE `" . _DB_PREFIX_ . "ets_am_payment_method_field` SET sort = " . (int)$index . " WHERE id_ets_am_payment_method_field =" . (int)$item;
            }
            if ($sqls) {
                foreach($sqls as $sql)
                    Db::getInstance()->execute($sql);
                return true;
            }
        }
        return false;
    }
    public static function actionReward($id, $type = null)
    {
        if ((int)$id && $type) {
            $trans = Module::getInstanceByName('ets_affiliatemarketing')->getTranslates();
            $reward = new Ets_AM($id);
            $actions = array();
            if (!$reward || !$reward->id) {
                return array(
                    'success' => false,
                    'actions' => array()
                );
            }
            $customer = new Customer($reward->id_customer);
            if ($type == 'approve') {
                $reward->status = 1;
                $reward->last_modified = date('Y-m-d H:i:s');
                $reward->datetime_validated = date('Y-m-d H:i:s');
                $reward->datetime_canceled = null;
                $reward->expired_date = null;
                $reward->update();
                $actions[] = array(
                    'label' => $trans['Cancel'],
                    'class' => 'js-cancel-reward-item',
                    'id' => $id,
                    'icon' => 'times'
                );
                $actions[] = array(
                    'label' => $trans['Delete'],
                    'class' => 'js-delete-reward-item',
                    'id' => $id,
                    'icon' => 'trash'
                );
                if ($reward->program == 'loy') {
                    if ((int)Configuration::get('ETS_AM_ENABLED_EMAIL_CUSTOMER_RVOC')) {
                        Ets_Loyalty::sendEmailToCustomerWhenRewardValidated($reward);
                    }
                    if ((int)Configuration::get('ETS_AM_ENABLED_EMAIL_ADMIN_RVOC')) {
                        Ets_Loyalty::sendEmailToAdminWhenRewardValidated($reward);
                    }
                }
                if ($reward->program == 'ref') {
                    if ((int)Configuration::get('ETS_AM_ENABLED_EMAIL_CUSTOMER_RVOC')) {
                        Ets_Sponsor::sendMailRewardValidated(null, $id);
                    }
                    if ((int)Configuration::get('ETS_AM_ENABLED_EMAIL_ADMIN_RVOC')) {
                        Ets_Sponsor::sendMailAdminRewardValidated(null, $id);
                    }
                }
                if ($reward->program == 'aff') {
                    if ((int)Configuration::get('ETS_AM_ENABLED_EMAIL_CUSTOMER_RVOC')) {
                        Ets_Affiliate::senEmailWhenAffiliateRewardValidated( $reward);
                    }
                    if ((int)Configuration::get('ETS_AM_ENABLED_EMAIL_ADMIN_RVOC')) {
                        Ets_Affiliate::senEmailWhenAffiliateRewardValidated( $reward, true);
                    }
                }
                return array(
                    'success' => true,
                    'actions' => $actions,
                    'user' => EtsAmAdmin::getUserInfo($reward->id_customer),
                );
            } elseif ($type == 'delete') {
                $reward->deleted = 1;
                $reward->last_modified = date('Y-m-d H:i:s');
                $reward->update();
                return array(
                    'success' => true,
                    'actions' => $actions,
                    'user' => EtsAmAdmin::getUserInfo($reward->id_customer),
                );
            } elseif ($type == 'cancel') {
                $reward->status = -1;
                $reward->last_modified = date('Y-m-d H:i:s');
                $reward->datetime_canceled = date('Y-m-d H:i:s');
                $reward->expired_date = null;
                $reward->update();
                if ($customer) {
                    $actions[] = array(
                        'label' => $trans['Approve'],
                        'class' => 'js-approve-reward-item',
                        'id' => $id,
                        'icon' => 'check'
                    );
                }
                $actions[] = array(
                    'label' => $trans['Delete'],
                    'class' => 'js-delete-reward-item',
                    'id' => $id,
                    'icon' => 'trash'
                );
                if ($reward->program == 'loy') {
                    if (Configuration::get('ETS_AM_ENABLED_EMAIL_CUSTOMER_RVOC')) {
                        Ets_Loyalty::sendEmailToCustomerWhenRewardCanceled($reward);
                    }
                    if (Configuration::get('ETS_AM_ENABLED_EMAIL_ADMIN_RVOC')) {
                        Ets_Loyalty::sendEmailToAdminWhenRewardCanceled($reward);
                    }
                }
                if ($reward->program == 'ref') {
                    if ((int)Configuration::get('ETS_AM_ENABLED_EMAIL_CUSTOMER_RVOC')) {
                        Ets_Sponsor::sendMailRewardCanceled(null, $id);
                    }
                    if ((int)Configuration::get('ETS_AM_ENABLED_EMAIL_ADMIN_RVOC')) {
                        Ets_Sponsor::sendMailAdminRewardCanceled(null, $id);
                    }
                }
                if ($reward->program == 'aff') {
                    if ((int)Configuration::get('ETS_AM_ENABLED_EMAIL_CUSTOMER_RVOC')) {
                        Ets_Affiliate::sendEmailWhenAffiliateCanceled($reward);
                    }
                    if ((int)Configuration::get('ETS_AM_ENABLED_EMAIL_ADMIN_RVOC')) {
                        Ets_Affiliate::sendEmailWhenAffiliateCanceled( $reward, true);
                    }
                }
                return array(
                    'success' => true,
                    'actions' => $actions,
                    'user' => EtsAmAdmin::getUserInfo($reward->id_customer),
                );
            }
        }
        return array(
            'success' => false,
            'actions' => $actions
        );
    }
    public static function actionRewardUsage($id, $type = null)
    {
        if ((int)$id && $type) {
            $trans = Module::getInstanceByName('ets_affiliatemarketing')->getTranslates();
            $reward = new Ets_Reward_Usage($id);
            $actions = array();
            if (!$reward) {
                return array(
                    'success' => true,
                    'actions' => array()
                );
            }
            $customer = new Customer($reward->id_customer);
            if ($type == 'approve') {
                $status = 1;
                $reward->status = $status;
                $reward->update();
                $actions[] = array(
                    'label' => $trans['Refund'],
                    'class' => 'js-cancel-reward-usage-item',
                    'id' => $id,
                    'icon' => 'rotate-right'
                );
                $actions[] = array(
                    'label' => $trans['Delete'],
                    'class' => 'js-delete-reward-usage-item',
                    'id' => $id,
                    'icon' => 'trash'
                );
            } elseif ($type == 'delete') {
                $deleted = 1;
                $reward->deleted = $deleted;
                $reward->update();
            } elseif ($type == 'cancel') {
                $status = 0;
                $reward->status = $status;
                $reward->update();
                if ($customer) {
                    $actions[] = array(
                        'label' => $trans['Deduct'],
                        'class' => 'js-approve-reward-usage-item',
                        'id' => $id,
                        'icon' => 'minus'
                    );
                }
                $actions[] = array(
                    'label' => $trans['Delete'],
                    'class' => 'js-delete-reward-usage-item',
                    'id' => $id,
                    'icon' => 'trash'
                );
            }
            return array(
                'actions' => $actions,
                'success' => true,
                'user' => EtsAmAdmin::getUserInfo($reward->id_customer),
            );
        }
        return array(
            'actions' => isset($actions) ? $actions : array(),
            'success' => false,
        );
    }
    public static function getRewardUsers($nb = false, $filter_where = '', $_orderBy = '', $_orderWay = '', $page = false, $limit = 25)
    {
        $context = Context::getContext();
        if($nb)
            $sql =' SELECT COUNT(DISTINCT c.id_customer)';
        else
            $sql ='SELECT c.id_customer,CONCAT(c.firstname,\' \',c.lastname) AS username,IFNULL(u.status, 1) AS user_status,
            r.has_reward,
            (r.reward_balance - COALESCE(ru2.reward_balance, 0) ) as reward_balance,
            (r.loy_rewards - COALESCE(ru2.loy_rewards,0) ) AS loy_rewards,
            (r.ref_rewards - COALESCE(ru2.ref_rewards,0) ) AS ref_rewards,
            (r.aff_rewards - COALESCE(ru2.aff_rewards,0) ) AS aff_rewards,
            (r.mnu_rewards - COALESCE(ru2.mnu_rewards,0)) AS mnu_rewards,
            ru2.total_withdraws';
        $sql .=' FROM `'._DB_PREFIX_.'customer` c
            LEFT JOIN `'._DB_PREFIX_.'ets_am_participation` p ON (c.id_customer = p.id_customer AND p.id_shop = "'.(int)Context::getContext()->shop->id.'")
            LEFT JOIN (
                SELECT id_customer,id_shop,
                IF(SUM(amount) >0,1,0) as has_reward,
                SUM(IF(deleted = 0 AND `status` = 1,amount,0)) as reward_balance,
                SUM(IF(program = \'loy\' AND deleted = 0 AND `status` = 1,amount,0)) as loy_rewards,
                SUM( IF( program = \'ref\' AND deleted = 0 AND `status` = 1, amount, 0 ) ) as ref_rewards,
                SUM( IF( program = \'aff\' AND deleted = 0 AND `status` = 1, amount, 0 ) ) as aff_rewards,
                SUM( IF( program = \'mnu\' AND deleted = 0 AND `status` = 1, amount, 0 ) ) as mnu_rewards
                FROM `'._DB_PREFIX_.'ets_am_reward`
                WHERE id_shop = "'.(int)Context::getContext()->shop->id.'"
                GROUP BY id_customer
            ) r ON (r.id_customer = c.id_customer AND r.id_shop="'.(int)Context::getContext()->shop->id.'")
            LEFT JOIN `'._DB_PREFIX_.'ets_am_sponsor` s ON (s.id_parent =c.id_customer AND s.`level` = 1 AND s.id_shop = "'.(int)Context::getContext()->shop->id.'")
            LEFT JOIN `'._DB_PREFIX_.'ets_am_user` u ON (u.id_customer = c.id_customer AND u.id_shop ="'.(int)Context::getContext()->shop->id.'")
            LEFT JOIN
            (
                SELECT id_customer, SUM(IF(ru.`status` != 0 AND ru.deleted = 0,ru.amount,0)) as reward_balance, SUM(IF(ru.type = \'loy\' AND ru.`status` != 0 AND ru.deleted = 0,ru.amount,0)) as loy_rewards,
                SUM( IF( ru.type = \'ref\' AND ru.`status` != 0 AND ru.deleted = 0, ru.amount, 0 ) ) as ref_rewards,
                SUM( IF( ru.type = \'aff\' AND ru.`status` != 0 AND ru.deleted = 0, ru.amount, 0 ) ) as aff_rewards,
                SUM( IF( ru.type = \'mnu\' AND ru.`status` != 0 AND ru.deleted = 0, ru.amount, 0 ) ) as mnu_rewards,
                SUM( IF( ru.id_withdraw > 0 AND ru.`status` IN(1, 0) AND ru.deleted = 0, ru.amount, 0 ) ) as total_withdraws
                FROM `'._DB_PREFIX_.'ets_am_reward_usage` ru
                GROUP BY ru.id_customer
            )
            ru2 ON (ru2.id_customer = c.id_customer)
            WHERE c.id_shop= "'.(int)Context::getContext()->shop->id.'" AND (p.id_customer is not null OR r.id_customer is not null OR s.id_parent is not null OR u.id_customer is not null)'.(string)$filter_where;
        if ($nb) {
            return Db::getInstance()->getValue($sql);
        }
        $order_col = 'c.id_customer';
        $order_dir = 'DESC';
        $cols = array('reward_balance', 'loy_rewards', 'ref_rewards', 'ref_orders', 'aff_rewards', 'aff_orders', 'total_withdraws');
        if (!$_orderBy && Context::getContext()->cookie->reward_usersOrderby)
            $_orderBy = Context::getContext()->cookie->reward_usersOrderby;
        if (in_array($_orderBy, $cols)) {
            $order_col = $_orderBy;
        } else {
            if ($_orderBy == 'id_customer') {
                $order_col = 'c.id_customer';
            } elseif ($_orderBy == 'username') {
                $order_col = 'username';
            } elseif ($_orderBy == 'user_status') {
                $order_col = 'user_status';
            }
        }
        if (!$_orderWay && Context::getContext()->cookie->reward_usersOrderway)
            $_orderWay = Context::getContext()->cookie->reward_usersOrderway;
        $_orderWay = Tools::strtoupper($_orderWay);
        if ($_orderWay == 'DESC' || $_orderWay == 'ASC') {
            $order_dir = $_orderWay;
        }
        $_page = (int)Context::getContext()->cookie->submitFilterreward_users;
        if (!$_page)
            $_page = (int)$page;
        $page = (int)$_page > 0 ? (int)$_page : 1;
        $offset = ($page - 1) * $limit;
        $sql .=' GROUP BY c.id_customer ORDER BY '.pSQL($order_col).' '.pSQL($order_dir).'
                LIMIT ' . (int)$offset . ', ' . (int)$limit;
        $results = Db::getInstance()->executeS($sql);
        if ($results) {
            foreach ($results as &$result) {
                $result['id_reward_users'] = $result['id_customer'];
            }
        }
        if ($ids = array_column($results, 'id_customer'))
            foreach ($ids as $key => $id_item) {
                if (!$id_item) {
                    unset($ids[$key]);
                }
            }
        $ids[] = 0;
        $e = Module::getInstanceByName('ets_affiliatemarketing');
        $trans = $e
            ->getTranslates();
        if ($results) {
            $sponsors = Db::getInstance()->executeS("SELECT id_parent, level, COUNT(*) as total_sponsor FROM `" . _DB_PREFIX_ . "ets_am_sponsor` WHERE id_parent IN (" . implode(',', array_map('intval', $ids)) . ") GROUP BY id_parent, level");
            foreach ($results as $key => &$result) {
                $result['sponsors'] = '--';
                $list_sponsors = '';
                foreach ($sponsors as $k => $sponsor) {
                    if ($sponsor['id_parent'] == $result['id_customer']) {
                        $list_sponsors .= $trans['level'] . ' ' . $sponsor['level'] . ': ' . $sponsor['total_sponsor'] . $e->getHtmlColum(array(
                                'type' => 'br'
                            ));
                        unset($sponsors[$k]);
                    }
                }
                $result['has_reward'] = (int)$result['has_reward'] == 1 ? $trans['yes'] : $trans['no'];
                $result['sponsors'] = $list_sponsors ? $list_sponsors : '--';
                $result['reward_balance'] = Ets_AM::displayRewardAdmin($result['reward_balance'], true, true);
                $result['loy_rewards'] = Ets_AM::displayRewardAdmin($result['loy_rewards'], true, true);
                $result['ref_rewards'] = Ets_AM::displayRewardAdmin($result['ref_rewards'], true, true);
                $result['aff_rewards'] = Ets_AM::displayRewardAdmin($result['aff_rewards'], true, true);
                $result['mnu_rewards'] = Ets_AM::displayRewardAdmin($result['mnu_rewards'], true, true);
                $result['total_withdraws'] = Ets_AM::displayRewardAdmin((float)$result['total_withdraws'], true, true);
                if ($result['id_customer'] && !(isset($result['username']) && trim($result['username']))) {
                    $c = new Customer($result['id_customer']);
                    if ($c && $c->id) {
                        $result['username'] = $c->firstname . ' ' . $c->lastname;
                    }
                    $result['user_status'] = 1;
                }
                if (!$result['username']) {
                    $result['user_status'] = $e->getHtmlColum(array(
                        'type' => 'label',
                        'class' => 'label warning-deleted',
                        'text' => $trans['user_deleted']
                    ));
                } elseif ($result['user_status'] == -1) {
                    $result['user_status'] = $e->getHtmlColum(array(
                        'type' => 'label',
                        'class' => 'label label-default',
                        'text' => $trans['Suspended']
                    ));
                } else if ($result['user_status'] == 1) {
                    $result['user_status'] = $e->getHtmlColum(array(
                        'type' => 'label',
                        'class' => 'label-success',
                        'text' => $trans['Active']
                    ));
                } else if ($result['user_status'] == 0) {
                    $result['user_status'] = $e->getHtmlColum(array(
                        'type' => 'label',
                        'class' => 'label label-warning',
                        'text' => $trans['Pending']
                    ));
                }
                $result['username'] = $e->getHtmlColum(array(
                    'type' => 'link',
                    'id' => $result['id_customer'],
                    'link' => $context->link->getAdminLink('AdminModules', true) . '&configure=ets_affiliatemarketing&tabActive=reward_users&id_reward_users=' . $result['id_customer'] . '&viewreward_users',
                    'class' => '',
                    'user_deleted' => $result['username'] ? false : true,
                    'text' => $result['username']
                ));
            }
        }
        return $results;
    }
    public static function getUserInfo($id_user)
    {
        $id_user = (int)$id_user;
        $context = Context::getContext();
        $loy_required_register = (int)Configuration::get('ETS_AM_LOYALTY_REGISTER');
        $ref_required_register = (int)Configuration::get('ETS_AM_REF_REGISTER_REQUIRED');
        $aff_required_register = (int)Configuration::get('ETS_AM_AFF_REGISTER_REQUIRED');
        $totalReward = (float)Db::getInstance()->getValue("SELECT SUM(amount) as total_amount FROM `" . _DB_PREFIX_ . "ets_am_reward` WHERE id_customer=" . (int)$id_user);
        $sql = "SELECT app.id_customer as id_customer, customer.firstname as firstname, customer.lastname as lastname, customer.email as email,
                customer.date_add as date_add, customer.birthday as birthday, 
                CASE WHEN $ref_required_register > 0 AND `user`.ref != 0 THEN `user`.ref
                    WHEN $ref_required_register > 0 AND (`user`.ref = 0 OR `user`.ref IS NULL) THEN 0
                    WHEN $ref_required_register = 0 AND `user`.ref < 0 THEN `user`.ref
                    ELSE 1 END as ref_program,
                CASE 
                    WHEN $loy_required_register > 0 AND `user`.loy != 0 THEN `user`.loy
                    WHEN $loy_required_register > 0 AND (`user`.loy = 0 OR `user`.loy IS NULL) THEN 0
                    WHEN $loy_required_register = 0 AND `user`.loy < 0 THEN `user`.loy
                    ELSE 1 END as loy_program,
                CASE 
                    WHEN $aff_required_register > 0 AND `user`.aff != 0 THEN `user`.aff
                    WHEN $aff_required_register > 0 AND (`user`.aff = 0 OR `user`.aff IS NULL) THEN 0
                    WHEN $aff_required_register = 0 AND `user`.aff < 0 THEN `user`.aff
                    ELSE 1 END as aff_program, 
                COALESCE(`user`.`status`, 1) as active,
                SUM(IF(reward.status = 1 , reward.amount, 0)) as total_point, 
                SUM(CASE WHEN reward.program = 'loy' AND reward.status = 1 THEN reward.amount ELSE 0 END) as loy_rewards, 
                SUM(CASE WHEN reward.program = 'ref' AND reward.status = 1 THEN reward.amount ELSE 0 END) as ref_rewards, 
                SUM(CASE WHEN reward.program = 'aff' AND reward.status = 1 THEN reward.amount ELSE 0 END) as aff_rewards,
                SUM(CASE WHEN reward.program = 'mnu' AND reward.status = 1 THEN reward.amount ELSE 0 END) as mnu_rewards,
                (SELECT SUM(amount) FROM `" . _DB_PREFIX_ . "ets_am_reward_usage` ug WHERE id_withdraw > 0 AND deleted = 0 AND status = 1 AND id_shop=" . (int)$context->shop->id . " AND  id_customer = " . (int)$id_user . ") as withdrawn,
                (SELECT SUM(amount) FROM `" . _DB_PREFIX_ . "ets_am_reward_usage` ug WHERE id_order > 0  AND deleted = 0 AND status = 1 AND id_shop=" . (int)$context->shop->id . " AND id_customer = " . (int)$id_user . ") as pay_for_order,
                (SELECT SUM(amount) FROM `" . _DB_PREFIX_ . "ets_am_reward_usage` ug WHERE id_voucher > 0  AND deleted = 0 AND status = 1 AND id_shop=" . (int)$context->shop->id . " AND id_customer = " . (int)$id_user . ") as convert_to_voucher,
                (SELECT SUM(amount) FROM `" . _DB_PREFIX_ . "ets_am_reward_usage` ug WHERE id_customer = " . (int)$id_user . " AND deleted = 0 AND status = 1 AND id_shop=" . (int)$context->shop->id . ") as total_usage
                FROM (
                    SELECT id_customer FROM `" . _DB_PREFIX_ . "ets_am_participation`  WHERE id_customer = " . (int)$id_user . " AND id_shop = " . (int)$context->shop->id . "
                    UNION
                    SELECT id_customer FROM `" . _DB_PREFIX_ . "ets_am_reward` r WHERE r.id_customer = " . (int)$id_user . " AND id_shop = " . (int)$context->shop->id . "
                    UNION
                    SELECT id_parent FROM `" . _DB_PREFIX_ . "ets_am_sponsor` s WHERE s.`level` = 1 AND s.id_parent = " . (int)$id_user . " AND id_shop = " . (int)$context->shop->id . "
                    UNION
                    SELECT id_customer FROM `" . _DB_PREFIX_ . "ets_am_user` WHERE id_customer = " . (int)$id_user . " AND id_shop = " . (int)$context->shop->id . "
                    )  app
                LEFT JOIN `" . _DB_PREFIX_ . "ets_am_reward` reward ON app.id_customer = reward.id_customer
                LEFT JOIN `" . _DB_PREFIX_ . "customer` customer ON customer.id_customer = app.id_customer
                LEFT JOIN `" . _DB_PREFIX_ . "orders` ord ON reward.id_order = ord.id_order
                LEFT JOIN `" . _DB_PREFIX_ . "ets_am_user` user ON (app.id_customer = user.id_customer AND user.id_shop = " . (int)$context->shop->id . ")
                LEFT JOIN `" . _DB_PREFIX_ . "currency` currency ON ord.id_currency = currency.id_currency
                WHERE app.id_customer = " . (int)$id_user . " " . ($totalReward > 0 ? " AND reward.deleted=0" : "") . "
                GROUP BY app.id_customer";
        $result = Db::getInstance()->getRow($sql);
        if (!$result) {
            Tools::redirectAdmin(404);
        }
        $result['total_balance'] = Ets_Am::displayRewardAdmin((float)$result['total_point'] - (float)$result['total_usage'], true, true);
        $result['total_point'] = Ets_Am::displayRewardAdmin((float)$result['total_point'], true, true);
        $result['loy_rewards'] = Ets_Am::displayRewardAdmin((float)$result['loy_rewards'] - Ets_Reward_Usage::getTotalSpent($result['id_customer'], false, null, $context, 'loy'), true, true);
        $result['ref_rewards'] = Ets_Am::displayRewardAdmin((float)$result['ref_rewards'] - Ets_Reward_Usage::getTotalSpent($result['id_customer'], false, null, $context, 'ref'), true, true);
        $result['aff_rewards'] = Ets_Am::displayRewardAdmin((float)$result['aff_rewards'] - Ets_Reward_Usage::getTotalSpent($result['id_customer'], false, null, $context, 'aff'), true, true);
        $result['mnu_rewards'] = Ets_Am::displayRewardAdmin((float)$result['mnu_rewards'] - Ets_Reward_Usage::getTotalSpent($result['id_customer'], false, null, $context, 'mnu'), true, true);
        $result['withdrawn'] = Ets_Am::displayRewardAdmin((float)$result['withdrawn'], true, true);
        $result['pay_for_order'] = Ets_Am::displayRewardAdmin((float)$result['pay_for_order'], true, true);
        $result['convert_to_voucher'] = Ets_Am::displayRewardAdmin((float)$result['convert_to_voucher'], true, true);
        $result['total_usage'] = Ets_Am::displayRewardAdmin((float)$result['total_usage'], true, true);
        if ((int)Configuration::get('ETS_AM_LOYALTY_ENABLED')) {
            if ((int)Configuration::get('ETS_AM_LOYALTY_REGISTER')) {
                $pa = Ets_Participation::getProgramRegistered($id_user, 'loy');
                if ($pa && $pa['status'] == 0) {
                    $result['loy_status'] = 'pending';
                } else {
                    $result['loy_status'] = (int)$result['loy_program'];
                }
            } else {
                $result['loy_status'] = $result['loy_program'] === null || $result['loy_program'] === '' ? 1 : (int)$result['loy_program'];
            }
        }
        if ((int)Configuration::get('ETS_AM_REF_ENABLED')) {
            if ((int)Configuration::get('ETS_AM_REF_REGISTER_REQUIRED')) {
                $pa = Ets_Participation::getProgramRegistered($id_user, 'ref');
                if ($pa && $pa['status'] == 0) {
                    $result['ref_status'] = 'pending';
                } else {
                    $result['ref_status'] = (int)$result['ref_program'];
                }
            } else {
                $result['ref_status'] = $result['ref_program'] === null || $result['ref_program'] === '' ? 1 : (int)$result['ref_program'];
            }
        }
        if ((int)Configuration::get('ETS_AM_AFF_ENABLED')) {
            if ((int)Configuration::get('ETS_AM_AFF_REGISTER_REQUIRED')) {
                $pa = Ets_Participation::getProgramRegistered($id_user, 'aff');
                if ($pa && $pa['status'] == 0) {
                    $result['aff_status'] = 'pending';
                } else {
                    $result['aff_status'] = (int)$result['aff_program'];
                }
            } else {
                $result['aff_status'] = $result['aff_program'] === null || $result['aff_program'] === '' ? 1 : (int)$result['aff_program'];
            }
        }
        return $result;
    }
    public static function getSearchSuggestionsReward($query, $type)
    {
        if ($query) {
            $query = trim(strip_tags($query));
            $query = str_replace('', '%', $query);
            if ($type == 'withdraw') {
                $sql = "SELECT customer.id_customer as id, customer.firstname as firstname, customer.lastname as lastname 
                    FROM `" . _DB_PREFIX_ . "ets_am_reward_usage` ug
                    LEFT JOIN `" . _DB_PREFIX_ . "customer` customer ON ug.id_customer = customer.id_customer
                    WHERE
                    ug.id_withdraw > 0 AND ug.deleted = 0 AND (customer.id_customer = " . (int)$query . " 
                        OR CONCAT(customer.firstname, ' ',customer.lastname) LIKE '%" . pSQL($query) . "%'
                        OR CONCAT(customer.lastname, ' ',customer.firstname) LIKE '%" . pSQL($query) . "%')
                    GROUP BY ug.id_customer
                    LIMIT 5";
            } else {
                $sql = "SELECT customer.id_customer as id, customer.firstname as firstname, customer.lastname as lastname 
                    FROM `" . _DB_PREFIX_ . "ets_am_reward` reward
                    LEFT JOIN `" . _DB_PREFIX_ . "customer` customer ON reward.id_customer = customer.id_customer
                    WHERE reward.deleted = 0 AND (customer.id_customer = " . (int)$query . " 
                        OR CONCAT(customer.firstname, ' ',customer.lastname) LIKE '%" . pSQL($query) . "%'
                        OR CONCAT(customer.lastname, ' ',customer.firstname) LIKE '%" . pSQL($query) . "%')
                    GROUP BY reward.id_customer
                    LIMIT 5";
            }
            return Db::getInstance()->executeS($sql);
        }
        return array();
    }
    public static function createRequiresTable()
    {
        $sqls = array();
        $sqls[] = "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "ets_am_reward` (
                  `id_ets_am_reward` int(10) unsigned NOT NULL AUTO_INCREMENT,
                  `amount` decimal(20,6) DEFAULT 0,
                  `program` varchar(3) DEFAULT NULL,
                  `sub_program` varchar(3) DEFAULT NULL,
                  `status` tinyint(2) NOT NULL DEFAULT 0,
                  `datetime_added` datetime DEFAULT NULL,
                  `datetime_validated` datetime DEFAULT NULL,
                  `expired_date` datetime DEFAULT NULL,
                  `datetime_canceled` datetime DEFAULT NULL,
                  `note` varchar(55) DEFAULT NULL,
                  `id_customer` int(11) NOT NULL,
                  `id_friend` int(11) DEFAULT NULL,
                  `id_order` int(11) DEFAULT NULL,
                  `id_shop` int(11) NOT NULL,
                  `id_currency` int(11) NOT NULL,
                  `await_validate` int(11) DEFAULT 0,
                  `send_expired_email` datetime DEFAULT NULL,
                  `send_going_expired_email` datetime DEFAULT NULL,
                  `last_modified` datetime DEFAULT NULL,
                  `deleted` TINYINT UNSIGNED DEFAULT 0,
                  `used` INT(11) NOT NULL,
                  PRIMARY KEY (`id_ets_am_reward`)
                  
                ) ENGINE = " . _MYSQL_ENGINE_ . " DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
        $sqls[] = "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "ets_am_loy_reward` (
                    `id_ets_am_loy_reward` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                    `id_product` INT(10) UNSIGNED NOT NULL,
                    `id_shop` INT(10) UNSIGNED DEFAULT NULL,
                    `use_default` TINYINT UNSIGNED DEFAULT 1,
                    `base_on` VARCHAR(20) DEFAULT NULL,
                    `amount` DECIMAL(20,6) UNSIGNED DEFAULT 0,
                    `amount_per` DECIMAL(20,6) UNSIGNED DEFAULT 0,
                    `gen_percent` DECIMAL(20,6) UNSIGNED DEFAULT 0,
                    `qty_min` INT(10) UNSIGNED DEFAULT 0,
                    PRIMARY KEY (`id_ets_am_loy_reward`)
            ) ENGINE = " . _MYSQL_ENGINE_ . " DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
        $sqls[] = "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "ets_am_aff_reward`
            (
                `id_ets_am_aff_reward` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `id_product` INT(10) UNSIGNED NOT NULL,
                `id_shop` INT(10) UNSIGNED DEFAULT NULL,
                `use_default` TINYINT UNSIGNED DEFAULT 1,
                `how_to_calculate` VARCHAR(20) DEFAULT NULL,
                `default_percentage` DECIMAL(20,6) UNSIGNED DEFAULT NULL,
                `default_fixed_amount` DECIMAL(20,6) UNSIGNED DEFAULT NULL,
                PRIMARY KEY (`id_ets_am_aff_reward`)
            ) ENGINE = " . _MYSQL_ENGINE_ . " DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
        $sqls[] = "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "ets_am_sponsor`
            (
                `id_ets_am_sponsor` INT(10) unsigned NOT NULL AUTO_INCREMENT,
                `id_customer` INT(10) UNSIGNED NOT NULL,
                `id_parent` INT(10) UNSIGNED NOT NULL,
                `level` TINYINT UNSIGNED DEFAULT 1,
                `id_shop` INT(10) UNSIGNED DEFAULT NULL,
                `datetime_added` DATETIME DEFAULT NULL,
                PRIMARY KEY (`id_ets_am_sponsor`)
            ) ENGINE = " . _MYSQL_ENGINE_ . " DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
        $sqls[] = "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "ets_am_participation` (
                  `id_ets_am_participation` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                  `id_customer` INT(10) UNSIGNED NOT NULL,
                  `datetime_added` datetime DEFAULT NULL,
                  `status` TINYINT DEFAULT 0,
                  `program` VARCHAR(3) DEFAULT NULL,
                  `id_shop` INT(10) NOT NULL,
                  `intro` TEXT DEFAULT NULL,
                  PRIMARY KEY (`id_ets_am_participation`)
                ) ENGINE = " . _MYSQL_ENGINE_ . " DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
        $sqls[] = "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "ets_am_reward_usage` (
                    `id_ets_am_reward_usage` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                    `type` varchar(50) DEFAULT 'loy',
                    `amount` decimal(20,6) DEFAULT 0.00,
                    `id_customer` int(10) NOT NULL,
                    `id_shop` int(10) NOT NULL,
                    `id_order` int(10) DEFAULT null,
                    `id_withdraw` int(10) DEFAULT null,
                    `id_voucher` int(10) DEFAULT NULL,
                    `id_currency` int(10) DEFAULT NULL,
                    `status` tinyint(2) default 0 not null,
                    `note` varchar(55) DEFAULT null,
                    `datetime_added` datetime DEFAULT null,
                    `deleted` tinyint(2) default 0 not null,
                    PRIMARY KEY (`id_ets_am_reward_usage`)
                ) ENGINE = " . _MYSQL_ENGINE_ . " DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
        $sqls[] = "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "ets_am_invitation`(
                    `id_ets_am_invitation` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                    `email` VARCHAR(255) NOT NULL,
                    `name` VARCHAR(255) DEFAULT NULL,
                    `datetime_sent` DATETIME NULL,
                    `id_friend` INT(10) UNSIGNED DEFAULT NULL,
                    `id_sponsor` INT(10) UNSIGNED NOT NULL,
                    PRIMARY KEY (`id_ets_am_invitation`)
                ) ENGINE = " . _MYSQL_ENGINE_ . " DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
        $sqls[] = "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "ets_am_banner`(
                    `id_ets_am_banner` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                    `id_sponsor` INT(10) UNSIGNED NOT NULL,
                    `datetime_added` DATETIME NULL,
                    `img` VARCHAR(255) NULL,
                    PRIMARY KEY (`id_ets_am_banner`)
                ) ENGINE = " . _MYSQL_ENGINE_ . " DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
        $sqls[] = "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "ets_am_payment_method`(
                    `id_ets_am_payment_method` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                    `id_shop` INT(10) UNSIGNED NOT NULL,
                    `fee_type` VARCHAR(10) DEFAULT 'FIXED',
                    `fee_fixed` DECIMAL(20,6) UNSIGNED DEFAULT NULL,
                    `fee_percent` DECIMAL(20,6) UNSIGNED DEFAULT NULL,
                    `estimated_processing_time` INT(10) DEFAULT NULL,
                    `enable` TINYINT UNSIGNED DEFAULT 0,
                    `deleted` TINYINT UNSIGNED DEFAULT 0,
                    `sort` TINYINT UNSIGNED DEFAULT 0,
                    PRIMARY KEY (`id_ets_am_payment_method`)
                ) ENGINE = " . _MYSQL_ENGINE_ . " DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
        $sqls[] = "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "ets_am_payment_method_lang`(
                    `id_ets_am_payment_method_lang` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                    `id_payment_method` INT(10) UNSIGNED NOT NULL,
                    `id_lang` INT(10) UNSIGNED NOT NULL,
                    `title` VARCHAR(255) NULL,
                    `description` TEXT DEFAULT NULL,
                    `note` TEXT DEFAULT NULL,
                    PRIMARY KEY (`id_ets_am_payment_method_lang`)
                ) ENGINE = " . _MYSQL_ENGINE_ . " DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
        $sqls[] = "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "ets_am_payment_method_field`(
                    `id_ets_am_payment_method_field` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                    `id_payment_method` INT(10) UNSIGNED NOT NULL,
                    `type` VARCHAR(20) DEFAULT 'text',
                    `sort` TINYINT UNSIGNED DEFAULT 0,
                    `required` TINYINT UNSIGNED DEFAULT 0,
                    `enable` TINYINT UNSIGNED DEFAULT 0,
                    `deleted` TINYINT UNSIGNED DEFAULT 0,
                    PRIMARY KEY (`id_ets_am_payment_method_field`)
                ) ENGINE = " . _MYSQL_ENGINE_ . " DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
        $sqls[] = "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "ets_am_payment_method_field_lang`(
                    `id_ets_am_payment_method_field_lang` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                    `id_payment_method_field` INT(10) UNSIGNED NOT NULL,
                    `id_lang` INT(10) UNSIGNED NOT NULL,
                    `title` VARCHAR(255) NULL,
                    `description` TEXT NULL,
                    PRIMARY KEY (`id_ets_am_payment_method_field_lang`)
                ) ENGINE = " . _MYSQL_ENGINE_ . " DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
        $sqls[] = "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "ets_am_withdrawal`(
                    `id_ets_am_withdrawal` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                    `id_payment_method` INT(10) UNSIGNED NOT NULL,
                    `status` TINYINT DEFAULT 0,
                    `invoice` VARCHAR(255) DEFAULT NULL,
                    `fee` FLOAT(10,2) DEFAULT NULL,
                    `fee_type` VARCHAR(255) DEFAULT NULL,
                    PRIMARY KEY (`id_ets_am_withdrawal`)
                ) ENGINE = " . _MYSQL_ENGINE_ . " DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
        $sqls[] = "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "ets_am_withdrawal_field`(
                    `id_ets_am_withdrawal_field` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                    `id_withdrawal` INT(10) UNSIGNED NOT NULL,
                    `id_payment_method_field` INT(10) UNSIGNED NOT NULL,
                    `value` TEXT DEFAULT NULL,
                    PRIMARY KEY (`id_ets_am_withdrawal_field`)
                ) ENGINE = " . _MYSQL_ENGINE_ . " DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
        $sqls[] = "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "ets_am_voucher`(
                    `id_ets_am_voucher` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                    `id_cart_rule` INT(10) UNSIGNED NOT NULL,
                    `id_customer` INT(10) UNSIGNED NOT NULL,
                    `id_product` INT(10) UNSIGNED DEFAULT NULL,
                    `id_cart` INT(10) UNSIGNED DEFAULT NULL,
                    PRIMARY KEY (`id_ets_am_voucher`)
                ) ENGINE = " . _MYSQL_ENGINE_ . " DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
        $sqls[] = "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "ets_am_user`(
                    `id_ets_am_user` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                    `id_customer` INT(10) UNSIGNED NOT NULL,
                    `loy` TINYINT DEFAULT 0,
                    `ref` TINYINT DEFAULT 0,
                    `aff` TINYINT DEFAULT 0,
                    `status` TINYINT DEFAULT 0,
                    `id_shop` INT(10) UNSIGNED DEFAULT NULL,
                    PRIMARY KEY (`id_ets_am_user`)
                ) ENGINE = " . _MYSQL_ENGINE_ . " DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
        $sqls[] = "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "ets_am_reward_product` (
                    `id_ets_am_reward_product` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                    `id_product` INT(10) UNSIGNED NOT NULL,
                    `id_ets_am_reward` INT(10) UNSIGNED NOT NULL,
                    `program` VARCHAR(3) DEFAULT NULL,
                    `quantity` INT(10) UNSIGNED NOT NULL,
                    `amount` DECIMAL (20,6) DEFAULT 0.00,
                    `id_seller` INT(10) UNSIGNED NULL,
                    `id_order` INT (10) UNSIGNED NULL,
                    `status` INT(3) DEFAULT 0,
                    `datetime_added` DATETIME DEFAULT NULL,
                    PRIMARY KEY (`id_ets_am_reward_product`)
                ) ENGINE = " . _MYSQL_ENGINE_ . " DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
        $sqls[] = "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "ets_am_access_key` (
                    `id_ets_am_access_key` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                    `key` VARCHAR(32) NOT NULL,
                    `ip_address` VARCHAR(30) DEFAULT NULL,
                    `id_seller` INT(10) UNSIGNED NOT NULL,
                    `id_product` INT(10) UNSIGNED NOT NULL,
                    `datetime_added` DATETIME DEFAULT NULL,
                    PRIMARY KEY (`id_ets_am_access_key`)
                ) ENGINE = " . _MYSQL_ENGINE_ . " DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
        $sqls[] = "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "ets_am_product_view` (
                    `id_ets_am_product_view` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                    `count` INT(10) UNSIGNED NOT NULL,
                    `id_product` INT(10) UNSIGNED NOT NULL,
                    `id_seller` INT(10) UNSIGNED NOT NULL,
                    `date_added` DATE DEFAULT NULL,
                    PRIMARY KEY (`id_ets_am_product_view`)
                ) ENGINE = " . _MYSQL_ENGINE_ . " DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
        $sqls[] = "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "ets_am_cart_rule_seller` (
                    `id_ets_am_cart_rule_seller` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                    `id_customer` INT(10) UNSIGNED NOT NULL,
                    `id_cart_rule` INT(10) UNSIGNED NOT NULL,
                    `code` VARCHAR(32),
                    `date_added` DATE DEFAULT NULL,
                    PRIMARY KEY (`id_ets_am_cart_rule_seller`)
                ) ENGINE = " . _MYSQL_ENGINE_ . " DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
        $sqls[] = "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "ets_am_voucher_combination` ( 
                `id_ets_am_voucher_combination` INT(11) NOT NULL AUTO_INCREMENT , 
                `id_cart_rule` INT(11) NOT NULL , 
                `type` VARCHAR(20) NOT NULL , 
                `use_other_voucher` TINYINT(1) NOT NULL , 
                PRIMARY KEY (`id_ets_am_voucher_combination`), INDEX (`id_cart_rule`), INDEX (`use_other_voucher`)) ENGINE = " . _MYSQL_ENGINE_ . " DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
        foreach ($sqls as $sql) {
            Db::getInstance()->execute($sql);
        }
        return true;
    }
    public static function addIndexTable()
    {
        Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'ets_am_access_key` ADD KEY `id_seller` (`id_seller`),ADD KEY `id_product` (`id_product`)');
        Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'ets_am_aff_reward` ADD KEY `id_product` (`id_product`),ADD KEY `id_shop` (`id_shop`),ADD KEY `use_default` (`use_default`)');
        Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'ets_am_cart_rule_seller` ADD KEY `id_customer` (`id_customer`),ADD KEY `id_cart_rule` (`id_cart_rule`)');
        Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'ets_am_invitation` ADD KEY `email` (`email`), ADD KEY `id_friend` (`id_friend`),ADD KEY `id_sponsor` (`id_sponsor`)');
        Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'ets_am_loy_reward` ADD KEY `id_product` (`id_product`),ADD KEY `id_shop` (`id_shop`),ADD KEY `use_default` (`use_default`)');
        Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'ets_am_participation` ADD KEY `id_customer` (`id_customer`),ADD KEY `status` (`status`),ADD KEY `program` (`program`),ADD KEY `id_shop` (`id_shop`)');
        Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'ets_am_payment_method` ADD KEY `enable` (`enable`),ADD KEY `deleted` (`deleted`),ADD KEY `id_shop` (`id_shop`)');
        Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'ets_am_product_view` ADD KEY `id_product` (`id_product`),ADD KEY `id_seller` (`id_seller`)');
        Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'ets_am_reward` ADD KEY `program` (`program`),ADD KEY `sub_program` (`sub_program`),ADD KEY `status` (`status`),ADD KEY `id_customer` (`id_customer`),ADD KEY `id_friend` (`id_friend`),ADD KEY `id_order` (`id_order`),ADD KEY `id_shop` (`id_shop`),ADD KEY `id_currency` (`id_currency`)');
        Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'ets_am_reward_product` ADD KEY `id_ets_am_reward` (`id_ets_am_reward`),ADD KEY `id_product` (`id_product`),ADD KEY `program` (`program`),ADD KEY `id_seller` (`id_seller`),ADD KEY `id_order` (`id_order`)');
        Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'ets_am_reward_usage` ADD KEY `id_customer` (`id_customer`),ADD KEY `id_shop` (`id_shop`),ADD KEY `id_order` (`id_order`),ADD KEY `id_withdraw` (`id_withdraw`),ADD KEY `id_voucher` (`id_voucher`),ADD KEY `deleted` (`deleted`)');
        Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'ets_am_sponsor` ADD KEY `id_customer` (`id_customer`),ADD KEY `id_shop` (`id_shop`),ADD KEY `id_parent` (`id_parent`)');
        Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'ets_am_user` ADD KEY `ets_am_user_index_c` (`id_customer`)');
        Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'ets_am_voucher` ADD KEY `id_cart_rule` (`id_cart_rule`), ADD KEY `id_customer` (`id_customer`), ADD KEY `id_product` (`id_product`), ADD KEY `id_cart` (`id_cart`)');
        Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'ets_am_banner` ADD KEY `id_sponsor` (`id_sponsor`)');
        Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'ets_am_payment_method_lang` ADD KEY `id_lang` (`id_lang`),ADD KEY `id_payment_method`(`id_payment_method`)');
        Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'ets_am_payment_method_field` ADD KEY `id_payment_method` (`id_payment_method`),ADD KEY `type`(`type`),ADD KEY `enable`(`enable`),ADD KEY `deleted`(`deleted`)');
        Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'ets_am_payment_method_field_lang` ADD KEY `id_payment_method_field` (`id_payment_method_field`),ADD KEY `id_lang`(`id_lang`)');
        Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'ets_am_withdrawal` ADD KEY `id_payment_method` (`id_payment_method`),ADD KEY `status`(`status`)');
        Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'ets_am_withdrawal_field` ADD KEY `id_withdrawal` (`id_withdrawal`),ADD KEY `id_payment_method_field`(`id_payment_method_field`)');
        return true;
    }
    public static function removeModuleTable()
    {
        Configuration::deleteByName('ETS_AM_SAVE_LOG');
        $sqls = array();
        $sqls[] = "DROP TABLE IF EXISTS `" . _DB_PREFIX_ . "ets_am_reward`";
        $sqls[] = "DROP TABLE IF EXISTS `" . _DB_PREFIX_ . "ets_am_loy_reward`";
        $sqls[] = "DROP TABLE IF EXISTS `" . _DB_PREFIX_ . "ets_am_aff_reward`";
        $sqls[] = "DROP TABLE IF EXISTS `" . _DB_PREFIX_ . "ets_am_sponsor`";
        $sqls[] = "DROP TABLE IF EXISTS `" . _DB_PREFIX_ . "ets_am_participation`";
        $sqls[] = "DROP TABLE IF EXISTS `" . _DB_PREFIX_ . "ets_am_reward_usage`";
        $sqls[] = "DROP TABLE IF EXISTS `" . _DB_PREFIX_ . "ets_am_invitation`";
        $sqls[] = "DROP TABLE IF EXISTS `" . _DB_PREFIX_ . "ets_am_banner`";
        $sqls[] = "DROP TABLE IF EXISTS `" . _DB_PREFIX_ . "ets_am_payment_method`";
        $sqls[] = "DROP TABLE IF EXISTS `" . _DB_PREFIX_ . "ets_am_payment_method_lang`";
        $sqls[] = "DROP TABLE IF EXISTS `" . _DB_PREFIX_ . "ets_am_payment_method_field`";
        $sqls[] = "DROP TABLE IF EXISTS `" . _DB_PREFIX_ . "ets_am_payment_method_field_lang`";
        $sqls[] = "DROP TABLE IF EXISTS `" . _DB_PREFIX_ . "ets_am_withdrawal`";
        $sqls[] = "DROP TABLE IF EXISTS `" . _DB_PREFIX_ . "ets_am_withdrawal_field`";
        $sqls[] = "DROP TABLE IF EXISTS `" . _DB_PREFIX_ . "ets_am_voucher`";
        $sqls[] = "DROP TABLE IF EXISTS `" . _DB_PREFIX_ . "ets_am_user`";
        $sqls[] = "DROP TABLE IF EXISTS `" . _DB_PREFIX_ . "ets_am_reward_product`";
        $sqls[] = "DROP TABLE IF EXISTS `" . _DB_PREFIX_ . "ets_am_access_key`";
        $sqls[] = "DROP TABLE IF EXISTS `" . _DB_PREFIX_ . "ets_am_product_view`";
        $sqls[] = "DROP TABLE IF EXISTS `" . _DB_PREFIX_ . "ets_am_cart_rule_seller`";
        foreach ($sqls as $sql) {
            Db::getInstance()->execute($sql);
        }
        return true;
    }
    public static function checkMetaExist($url_rewrite,$controller)
    {
        return Db::getInstance()->getRow('SELECT * FROM `' . _DB_PREFIX_ . 'meta_lang` WHERE url_rewrite ="' . pSQL($url_rewrite) . '"') || Db::getInstance()->getRow('SELECT * FROM `' . _DB_PREFIX_ . 'meta` WHERE page ="module-ets_affiliatemarketing-' . pSQL($controller) . '"');
    }
    public static function checkSpecificProudct($id_product)
    {
        $sql = 'SELECT id_specific_price FROM `' . _DB_PREFIX_ . 'specific_price` WHERE id_product ="' . (int)$id_product . '" AND (`from` = "0000-00-00 00:00:00" OR `from` <="' . pSQL(date('Y-m-d H:i:s')) . '" ) AND (`to` = "0000-00-00 00:00:00" OR `to` >="' . pSQL(date('Y-m-d H:i:s')) . '" )';
        return Db::getInstance()->getRow($sql);
    }
    public static function delelteConfig($configs)
    {
        if ($configs) {
            foreach (array_keys($configs) as $key) {
                Configuration::deleteByName($key);
                if ($key == 'ETS_AM_REF_SPONSOR_COST_LEVEL_1') {
                    Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'configuration` where name like "ETS_AM_REF_SPONSOR_COST_LEVEL_%"');
                }
            }
        }
    }
    public static function getOrderStateByModule($module_name)
    {
        return Db::getInstance()->getValue("SELECT id_order_state FROM `" . _DB_PREFIX_ . "order_state` WHERE `module_name` = '" . pSQL($module_name) . "'");
    }
}
