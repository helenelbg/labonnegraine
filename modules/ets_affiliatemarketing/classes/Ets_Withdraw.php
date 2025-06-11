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
    exit();
}
class Ets_Withdraw extends ObjectModel
{
    /**
     * @var int
     */
    public $id_ets_am_withdrawal;
    /**
     * @var int
     */
    public $id_payment_method;
    /**
     * @var string
     */
    public $invoice;
    /**
     * @var int
     */
    public $status;
    /**
     * @var array
     */
    public $fee;
    public $fee_type;
    public static $definition = array(
        'table' => 'ets_am_withdrawal',
        'primary' => 'id_ets_am_withdrawal',
        'multilangshop' => true,
        'fields' => array(
            'id_ets_am_withdrawal' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt'
            ),
            'id_payment_method' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt'
            ),
            'invoice' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isString'
            ),
            'status' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isInt'
            ),
            'fee_type' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isString'
            ),
            'fee' => array(
                'type' => self::TYPE_FLOAT,
            )
        )
    );
    /**
     * REMOVE WHEN DONE (MvD NOTE)
     *
     * @param null $id_payment
     * @param null $context
     * @return array|bool|false|mysqli_result|null|PDOStatement|resource
     * @throws PrestaShopDatabaseException
     */
    public static function getPaymentMethods($id_payment = null)
    {
        $context = Context::getContext();
        $sql = "SELECT `epm`.`id_ets_am_payment_method`,
                       `epm`.`fee_type`,
                       `epm`.`fee_fixed`,
                       `epm`.`fee_percent`,
                       `epm`.`estimated_processing_time`,
                       `epml`.`title`,
                       `epml`.`note`
                FROM `" . _DB_PREFIX_ . "ets_am_payment_method` `epm`
                INNER JOIN `" . _DB_PREFIX_ . "ets_am_payment_method_lang` `epml`
                      ON `epm`.`id_ets_am_payment_method` = `epml`.`id_payment_method`
                WHERE `epm`.`id_shop` = " . (int)$context->shop->id . "
                AND `epml`.`id_lang` = " . (int)$context->language->id . "
                AND epm.deleted = 0";
        if ($id_payment) {
            $sql .= " AND `epm`.`id_ets_am_payment_method` = " . (int)$id_payment;
        }
        $results = Db::getInstance()->executes($sql);
        if ($id_payment && count($results)) {
            return $results[0];
        }
        return $results;
    }
    /**
     * @param null $context
     * @return array|false|mysqli_result|null|PDOStatement|resource
     * @throws PrestaShopDatabaseException
     */
    public static function getListPayments()
    {
        $context = Context::getContext();
        $sql = "SELECT mt.id_ets_am_payment_method, mt.id_shop, mt.fee_type, mt.fee_fixed, mt.fee_percent, mtl.description, mtl.title, mt.estimated_processing_time 
          FROM `" . _DB_PREFIX_ . "ets_am_payment_method` mt 
          INNER JOIN `" . _DB_PREFIX_ . "ets_am_payment_method_lang` mtl ON mt.id_ets_am_payment_method = mtl.id_payment_method
           WHERE mtl.id_lang = " . (int)$context->language->id . " AND mt.enable = 1 AND mt.deleted = 0
           ORDER BY mt.sort ASC";
        return Db::getInstance()->executeS($sql);
    }
    /**
     * @param $id_payment_method
     * @param null $context
     * @return array|false|mysqli_result|null|PDOStatement|resource
     * @throws PrestaShopDatabaseException
     */
    public static function getPaymentMethodFields($id_payment_method)
    {
        $context = Context::getContext();
        $sql = "SELECT eapmf.id_ets_am_payment_method_field as field_id,
                       eapmf.type as field_type,
                       eapmf.sort as sort,
                       eapmf.required as required,
                       eapmfl.title as field_title,
                       eapmfl.description as description
                    FROM `" . _DB_PREFIX_ . "ets_am_payment_method_field` eapmf
                INNER JOIN `" . _DB_PREFIX_ . "ets_am_payment_method_field_lang` eapmfl
                    ON eapmf.id_ets_am_payment_method_field = eapmfl.id_payment_method_field
                LEFT JOIN `" . _DB_PREFIX_ . "ets_am_payment_method` eapm
                    ON eapmf.id_payment_method = eapm.id_ets_am_payment_method
                WHERE eapmf.id_payment_method = " . (int)$id_payment_method . "
                AND eapmfl.id_lang = " . (int)$context->language->id . "
                AND eapm.id_shop = " . (int)$context->shop->id . "
                ORDER BY eapmf.sort ASC";
        $results = Db::getInstance()->executeS($sql);
        $return = array();
        if (count($results)) {
            foreach ($results as $result) {
                $result['field_alias'] = self::generateFieldName($result['field_title']);
                $return[] = $result;
            }
        }
        return $return;
    }
    /**
     * @param null $page
     * @param null $context
     * @param null $id_customer
     * @return array
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public static function getCustomerWithdrawalRequests($id_customer = null, $filter = array(), $page = 1, $limit = 30)
    {
        $context = Context::getContext();
        $page = ($page = (int)$page) && $page > 0 ? $page : 1;
        $limit = ($limit = (int)$limit) && $limit > 0 ? $limit : 30;
        $filter_where = "";
        $id_customer = (int)$id_customer;
        $type_date_filter = isset($filter['type_date_filter']) && in_array($filter['type_date_filter'], array('all_times', 'this_month', 'this_year', 'time_ranger')) ? $filter['type_date_filter'] : 'all_times';
        $date_from_reward = isset($filter['date_from_reward']) && Validate::isDate($filter['date_from_reward']) ? $filter['date_from_reward'] : null;
        $date_to_reward = isset($filter['date_to_reward']) && Validate::isDate($filter['date_to_reward']) ? $filter['date_to_reward'] : null;
        $status = isset($filter['status']) && $filter['status'] !== false && $filter['status'] !== '' ? (int)$filter['status'] : false;
        if ($type_date_filter == 'this_month') {
            $filter_where .= " AND earu.datetime_added >= '" . pSQL(date('Y-m-01 00:00:00')) . "' AND earu.datetime_added <= '" . pSQL(date('Y-m-t 23:59:59')) . "'";
        } else if ($type_date_filter == 'this_year') {
            $filter_where .= " AND earu.datetime_added >= '" . pSQL(date('Y-01-01 00:00:00')) . "' AND earu.datetime_added <= '" . pSQL(date('Y-12-31 23:59:59')) . "'";
        } else if ($type_date_filter == 'time_ranger' && $date_from_reward && $date_to_reward) {
            $filter_where .= " AND earu.datetime_added >= '" . pSQL($date_from_reward) . "' AND earu.datetime_added <= '" . pSQL($date_to_reward) . "'";
        }
        if ($id_customer) {
            $filter_where .= " AND earu.id_customer = " . (int)$id_customer;
        }
        if ($status !== false) {
            $filter_where .= " AND eaw.status = " . (int)$status;
        }
        $sql = "SELECT COUNT(*) as total
                FROM `" . _DB_PREFIX_ . "ets_am_withdrawal` eaw
                       INNER JOIN `" . _DB_PREFIX_ . "ets_am_payment_method` eapm ON eaw.id_payment_method = eapm.id_ets_am_payment_method
                       INNER JOIN `" . _DB_PREFIX_ . "ets_am_payment_method_lang` eapml ON eapm.id_ets_am_payment_method = eapml.id_payment_method
                       INNER JOIN (SELECT id_withdraw, MAX(id_ets_am_reward_usage) AS id_ug FROM `" . _DB_PREFIX_ . "ets_am_reward_usage` GROUP BY id_withdraw) ug ON eaw.id_ets_am_withdrawal = ug.id_withdraw
                       INNER JOIN `" . _DB_PREFIX_ . "ets_am_reward_usage` earu ON ug.id_ug = earu.id_ets_am_reward_usage
                       INNER JOIN `" . _DB_PREFIX_ . "customer` c ON earu.id_customer = c.id_customer 
                WHERE eapml.id_lang = " . (int)$context->language->id . "  AND earu.id_shop = " . (int)$context->shop->id . " AND earu.status != -2  AND earu.deleted = 0" . (string)$filter_where;
        $total = (int)Db::getInstance()->getValue($sql);
        $total_page = ceil($total / $limit);
        $offset = ($page - 1) * $limit;
        $results = array();
        if ($total) {
            $sql = "SELECT eaw.id_ets_am_withdrawal, SUM(earu.amount) AS amount, earu.note, earu.status as usage_status, eaw.status as withdrawal_status, earu.datetime_added, DATE(earu.datetime_added) as date_process, eapml.title, CONCAT(c.firstname, ' ', c.lastname) as customer, c.firstname, c.lastname, earu.id_customer as id_customer, earu.note as note, earu.deleted as deleted, earu.id_ets_am_reward_usage as id_usage, eapm.estimated_processing_time as estimated_time
                FROM `" . _DB_PREFIX_ . "ets_am_withdrawal` eaw
                       INNER JOIN `" . _DB_PREFIX_ . "ets_am_payment_method` eapm ON eaw.id_payment_method = eapm.id_ets_am_payment_method
                       INNER JOIN `" . _DB_PREFIX_ . "ets_am_payment_method_lang` eapml ON eapm.id_ets_am_payment_method = eapml.id_payment_method
                       INNER JOIN `" . _DB_PREFIX_ . "ets_am_reward_usage` earu ON eaw.id_ets_am_withdrawal = earu.id_withdraw
                    INNER JOIN `" . _DB_PREFIX_ . "customer` c ON earu.id_customer = c.id_customer 
                    WHERE earu.deleted = 0 AND eapml.id_lang = " . (int)$context->language->id . "  AND earu.id_shop = " . (int)$context->shop->id . "  AND earu.status != -2  AND earu.deleted = 0  " . (string)$filter_where . "
                    GROUP BY earu.id_withdraw
                    ORDER BY  earu.id_withdraw DESC LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
            $requests = Db::getInstance()->executeS($sql);
            $trans =Module::getInstanceByName('ets_affiliatemarketing')->getTranslates();
            if ($requests) {
                foreach ($requests as $result) {
                    $amount = (float)$result['amount'];
                    $result['display_amount'] = Ets_AM::displayPriceOnly($amount, $context);
                    $result['display_amount_backend'] = Ets_AM::displayRewardAdmin($amount, true, true);
                    $result['datetime_added'] = date('Y-m-d', strtotime($result['datetime_added']));
                    unset($result['amount']);
                    $actions = array(
                        array(
                            'label' => $trans['View'],
                            'link' => $id_customer ? '' : $context->link->getAdminLink('AdminEtsAmWithdrawals', true) . '&id_withdrawal=' . (int)$result['id_ets_am_withdrawal'] . '&view=1',
                            'icon' => 'search',
                            'class' => '',
                            'action' => '',
                            'id' => '',
                        )
                    );
                    if ($result['withdrawal_status'] == 0) {
                        $status = 0;
                        $actions[] = array(
                            'label' => $trans['Approve'],
                            'class' => 'ets-am-withdraw-action approve js-confirm-approve-withdraw',
                            'action' => 'APPROVE',
                            'id' => $result['id_usage'],
                            'icon' => 'check'
                        );
                        $actions[] = array(
                            'label' => $trans['Decline_return'],
                            'class' => 'ets-am-withdraw-action approve js-confirm-decline-return-withdraw',
                            'action' => 'DECLINE_RETURN',
                            'id' => $result['id_usage'],
                            'icon' => 'undo'
                        );
                        $actions[] = array(
                            'label' => $trans['Decline_deduct'],
                            'class' => 'ets-am-withdraw-action refuse js-confirm-decline-deduct-withdraw',
                            'action' => 'DECLINE_DEDUCT',
                            'id' => $result['id_usage'],
                            'icon' => 'close'
                        );
                    } elseif ($result['withdrawal_status'] == 1 && $result['usage_status'] == 1) {
                        $status = 1;
                    } elseif ($result['withdrawal_status'] == -1) {
                        $status = -1;
                    }
                    $result['status'] = $status;
                    $actions[] = array(
                        'label' => $trans['Delete'],
                        'class' => 'ets-am-withdraw-action cancel js-confirm-delete-withdraw',
                        'action' => 'DELETE',
                        'id' => $result['id_usage'],
                        'icon' => 'trash'
                    );
                    $result['actions'] = $actions;
                    if ($result['estimated_time'] && Validate::isInt($result['estimated_time']) && (int)$result['estimated_time'] > 0) {
                        $estimate = Date('Y-m-d',strtotime((int)$result['estimated_time'] . ' days'));
                        $result['date_process'] = Tools::displayDate($estimate,Context::getContext()->language->id,false) . '(' . $trans['estimated'] . ')';
                    }
                    $results[] = $result;
                }
            }
        }
        $response = array();
        $response['current_page'] = $page;
        $response['total_page'] = $total_page;
        $response['results'] = $results;
        $response['total_data'] = $total;
        $response['per_page'] = $limit;
        return $response;
    }
    /**
     * @param $id_payment_method
     * @param null $context
     * @return array|false|mysqli_result|null|PDOStatement|resource
     * @throws PrestaShopDatabaseException
     */
    public static function getLatestCustomerPaymentInfo($id_payment_method)
    {
        $context = Context::getContext();
        $sql = "SELECT earu.id_withdraw, eaw.id_payment_method, eawf.value, eawf.id_ets_am_withdrawal_field 
                FROM `" . _DB_PREFIX_ . "ets_am_reward_usage` earu
                INNER JOIN `" . _DB_PREFIX_ . "ets_am_withdrawal` eaw ON earu.id_withdraw = eaw.id_ets_am_withdrawal
                INNER JOIN `" . _DB_PREFIX_ . "ets_am_withdrawal_field` eawf ON eaw.id_ets_am_withdrawal = eawf.id_withdrawal
                WHERE earu.id_customer = " . (int)$context->customer->id . "
                AND earu.id_shop = " . (int)$context->shop->id . "
                AND eaw.id_payment_method = " . (int)$id_payment_method . "
                AND earu.status = 0
                AND earu.id_ets_am_reward_usage = (SELECT MAX(id_ets_am_reward_usage) FROM `" . _DB_PREFIX_ . "ets_am_reward_usage` WHERE id_customer = " . (int)$context->customer->id . " AND id_shop = " . (int)$context->shop->id . " AND status = 0 AND id_payment_method = " . (int)$id_payment_method . ")
                ORDER BY earu.datetime_added DESC";
        return Db::getInstance()->executeS($sql);
    }
    public static function generateFieldName($title)
    {
        $title = Tools::strtolower($title);
        $title = str_replace(' ', '_', $title);
        $title = preg_replace('/[^A-Za-z0-9\-_]/', '', $title);
        return $title;
    }
    public static function updateWithdrawAndReward($id_usage, $action)
    {
        $w_status = null;
        $u_status = null;
        $deleted = 0;
        $context = Context::getContext();
        $usage = new Ets_Reward_Usage($id_usage);
        if (!$usage->id)
            return false;
        $module = Module::getInstanceByName('ets_affiliatemarketing');
        $trans = $module->getTranslates();
        if (!$usage) {
            return array(
                'success' => false,
                'actions' => array()
            );
        }
        $actions = array(
            array(
                'label' => $trans['View'],
                'href' => $context->link->getAdminLink('AdminEtsAmWithdrawals', true) . '&id_withdrawal=' . (int)$usage->id_withdraw . '&view=1',
                'icon' => 'search',
                'class' => '',
                'action' => '',
                'id' => '',
            )
        );
        $note = '';
        if ($action == 'APPROVE') {
            $w_status = 1;
            $u_status = 1;
        } elseif ($action == 'DECLINE_DEDUCT') {
            $w_status = -1;
            $u_status = 1;
            $note = $trans['deduct_reward'];
        } elseif ($action == 'DECLINE_RETURN') {
            $w_status = -1;
            $u_status = 0;
            $note = $trans['return_reward'];
        } elseif ($action == 'DELETE') {
            $deleted = 1;
        }
        $actions[] = array(
            'label' => $trans['Delete'],
            'class' => 'ets-am-withdraw-action cancel js-confirm-delete-withdraw',
            'action' => 'DELETE',
            'id' => $id_usage,
            'icon' => 'trash'
        );
        if (($w_status !== null && $u_status !== null)) {
            if ($usage->id_withdraw) {
                Db::getInstance()->execute("UPDATE `" . _DB_PREFIX_ . "ets_am_reward_usage` SET status=" . (int)$u_status . ($note ? ", `note`='" . pSQL($usage->note . ' - ' . $note) . "'" : "") . " WHERE id_withdraw=" . (int)$usage->id_withdraw);
            } else {
                $usage->status = $u_status;
                if ($note)
                    $usage->note .= ' - ' . $note;
                $usage->update();
            }
            if ($usage->id_withdraw && ($withdrawal = new Ets_Withdraw($usage->id_withdraw)) && $withdrawal->id) {
                $withdrawal->status = $w_status;
                $withdrawal->update();
            }
            $withdrawal_info = Db::getInstance()->getRow('SELECT ru.total_amount as amount,ru.id_withdraw,c.firstname,c.lastname,c.email,pml.title FROM `' . _DB_PREFIX_ . 'ets_am_withdrawal` w
            INNER JOIN (SELECT SUM(`amount`) as total_amount,id_withdraw,id_customer FROM `' . _DB_PREFIX_ . 'ets_am_reward_usage` WHERE id_withdraw=' . (int)$withdrawal->id . ') ru ON (w.id_ets_am_withdrawal = ru.id_withdraw)  
            LEFT JOIN `' . _DB_PREFIX_ . 'customer` c ON (ru.id_customer=c.id_customer)
            LEFT JOIN `' . _DB_PREFIX_ . 'ets_am_payment_method_lang` pml ON (pml.id_payment_method = w.id_payment_method AND pml.id_lang="' . (int)$context->language->id . '")   
            WHERE w.id_ets_am_withdrawal = "' . (int)$withdrawal->id . '"');
            if ($withdrawal_info) {
                $data = array(
                    '{customer}' => $withdrawal_info['firstname'] . ' ' . $withdrawal_info['lastname'],
                    '{withdrawal_ID}' => $withdrawal_info['id_withdraw'],
                    '{amount}' => Ets_affiliatemarketing::displayPrice($withdrawal_info['amount']),
                    '{payment_method}' => $withdrawal_info['title'],
                );
                if ($withdrawal_info['email'])
                    if ($action == 'APPROVE') {
                        $subjects = array(
                            'translation' => $module->l('Your withdrawal request was approved!', 'ets_withdraw'),
                            'origin' => 'Your withdrawal request was approved!',
                            'specific' => 'ets_withdraw'
                        );
                        Ets_aff_email::send(0, 'approve_withdraw', $subjects, $data, array('customer' => $withdrawal_info['email']));
                    } else {
                        $subjects = array(
                            'translation' => $module->l('Your withdrawal request was declined!', 'ets_withdraw'),
                            'origin' => 'Your withdrawal request was declined!',
                            'specific' => 'ets_withdraw'
                        );
                        Ets_aff_email::send(0, 'decline_withdraw', $subjects, $data, array('customer' => $withdrawal_info['email']));
                    }
                $adminEmail = Configuration::get('ETS_AM_EMAILS_CONFIRM');
                if ($adminEmail) {
                    $adminEmail = explode(',', $adminEmail);
                    foreach ($adminEmail as $to) {
                        if (Validate::isEmail($to)) {
                            if (!$id_lang = Db::getInstance()->getValue('SELECT id_lang FROM `' . _DB_PREFIX_ . 'employee` WHERE email ="' . pSQL($to) . '"'))
                                $id_lang = Configuration::get('PS_LANG_DEFAULT');
                            if ($action == 'APPROVE') {
                                $subjects = array(
                                    'translation' => $module->l('You have approved a withdrawal request', 'ets_withdraw'),
                                    'origin' => 'You have approved a withdrawal request',
                                    'specific' => 'ets_withdraw'
                                );
                                Ets_aff_email::send($id_lang, 'admin_approve_withdraw', $subjects, $data, $to);
                            } else {
                                $subjects = array(
                                    'translation' => $module->l('You have declined a withdrawal request', 'ets_withdraw'),
                                    'origin' => 'You have declined a withdrawal request',
                                    'specific' => 'ets_withdraw'
                                );
                                Ets_aff_email::send($id_lang, 'admin_decline_withdraw', $subjects, $data, $to);
                            }
                        }
                    }
                }
            }
            return array(
                'success' => true,
                'actions' => $actions
            );
        } elseif ($deleted) {
            if ($usage->id_withdraw) {
                Db::getInstance()->execute("UPDATE `" . _DB_PREFIX_ . "ets_am_reward_usage` SET `deleted`=1 WHERE id_withdraw=" . (int)$usage->id_withdraw);
            } else {
                $usage = new Ets_Reward_Usage($id_usage);
                $usage->deleted = $deleted;
                $usage->update();
            }
            return array(
                'success' => true,
                'actions' => array()
            );
        }
        return array(
            'success' => false,
            'actions' => array()
        );
    }
    public static function getUserWithdrawal($id_withdrawal)
    {
        $id_lang_default = (int)Configuration::get('PS_LANG_DEFAULT');
        $sql = "SELECT pm.*, withdrawal.*, reward.id_customer as id_customer, customer.firstname as firstname, customer.lastname as lastname, ug.amount,
            SUM(IF(reward.status = 1 AND reward.deleted = 0,reward.amount, 0)) as total_point, 
            (SUM(IF(reward.status = 1 AND reward.deleted = 0,reward.amount, 0)) - (SELECT SUM(amount) FROM `" . _DB_PREFIX_ . "ets_am_reward_usage` WHERE id_withdraw > 0 AND id_customer = ug.id_customer)) as total_point_remain,
            SUM(CASE WHEN reward.program = 'loy' AND reward.status = 1 AND reward.deleted = 0 THEN reward.amount ELSE 0 END) as loy_rewards, 
            SUM(CASE WHEN reward.program = 'ref' AND reward.status = 1 AND reward.deleted = 0 THEN reward.amount ELSE 0 END) as ref_rewards, 
            SUM(CASE WHEN reward.program = 'aff' AND reward.status = 1 AND reward.deleted = 0 THEN reward.amount ELSE 0 END) as aff_rewards,
            SUM(CASE WHEN reward.program = 'mnu' AND reward.status = 1 AND reward.deleted = 0 THEN reward.amount ELSE 0 END) as mnu_rewards,
            (SELECT SUM(amount) FROM `" . _DB_PREFIX_ . "ets_am_reward_usage` WHERE id_withdraw > 0 AND id_customer = ug.id_customer AND deleted = 0 AND `status` = 1) as withdrawn,
            (SELECT SUM(amount) FROM `" . _DB_PREFIX_ . "ets_am_reward_usage` WHERE id_order >0 AND id_customer = ug.id_customer AND `status` = 1 AND deleted = 0 ) as pay_for_order,
            (SELECT SUM(amount) FROM `" . _DB_PREFIX_ . "ets_am_reward_usage` WHERE id_voucher >0 AND id_customer = ug.id_customer AND `status` = 1 AND deleted = 0 ) as convert_to_voucher,
            (SELECT SUM(amount) FROM `" . _DB_PREFIX_ . "ets_am_reward_usage` WHERE id_customer = ug.id_customer AND `status` = 1 AND deleted = 0) as total_usage,
            (SELECT SUM(amount) FROM `" . _DB_PREFIX_ . "ets_am_reward_usage` WHERE id_customer = ug.id_customer AND `status` = 1 AND deleted = 0 AND type='loy') as total_loy_usage,
            (SELECT SUM(amount) FROM `" . _DB_PREFIX_ . "ets_am_reward_usage` WHERE id_customer = ug.id_customer AND `status` = 1 AND deleted = 0 AND type='ref') as total_ref_usage,
            (SELECT SUM(amount) FROM `" . _DB_PREFIX_ . "ets_am_reward_usage` WHERE id_customer = ug.id_customer AND `status` = 1 AND deleted = 0 AND type='aff') as total_aff_usage,
            (SELECT SUM(amount) FROM `" . _DB_PREFIX_ . "ets_am_reward_usage` WHERE id_customer = ug.id_customer AND `status` = 1 AND deleted = 0 AND type='mnu') as total_mnu_usage,
            (SELECT SUM(amount) FROM `" . _DB_PREFIX_ . "ets_am_reward_usage` WHERE id_withdraw=" . (int)$id_withdrawal . ") as amount_withdraw, withdrawal.id_payment_method as id_payment_method, pml.title as payment_method_name, pml.description as payment_method_desciption, ug.note as note, ug.datetime_added as datetime_added, ug.status as usage_status, withdrawal.status as withdraw_status, ug.id_ets_am_reward_usage as id_usage, ug.id_withdraw as id_withdraw
            FROM (
                SELECT * FROM `" . _DB_PREFIX_ . "ets_am_withdrawal` WHERE id_ets_am_withdrawal = " . (int)$id_withdrawal . "
            ) AS withdrawal
            JOIN `" . _DB_PREFIX_ . "ets_am_reward_usage` ug ON withdrawal.id_ets_am_withdrawal = ug.id_withdraw
            LEFT JOIN `" . _DB_PREFIX_ . "customer` customer ON ug.id_customer = customer.id_customer
            JOIN `" . _DB_PREFIX_ . "ets_am_reward` reward ON ug.id_customer = reward.id_customer
            LEFT JOIN `" . _DB_PREFIX_ . "orders` ord ON reward.id_order = ord.id_order
            LEFT JOIN `" . _DB_PREFIX_ . "currency` currency ON ord.id_currency = currency.id_currency
            LEFT JOIN `" . _DB_PREFIX_ . "ets_am_payment_method` pm ON pm.id_ets_am_payment_method = withdrawal.id_payment_method
            LEFT JOIN `" . _DB_PREFIX_ . "ets_am_payment_method_lang` pml ON pm.id_ets_am_payment_method = pml.id_payment_method
            WHERE pml.id_lang = ".(int)$id_lang_default." AND ug.deleted = 0 AND reward.deleted = 0
            ";
        $result = Db::getInstance()->getRow($sql);
        if ($result['fee_type'] != 'NO_FEE') {
            if ($result['fee_type'] == 'FIXED')
                $result['fee_amount'] = $result['fee'];
            else
                $result['fee_amount'] = Tools::ps_round($result['amount'] * $result['fee'] / 100, 2);
        } else
            $result['fee_amount'] = 0;
        $result['amount_pay'] = $result['amount_withdraw'] - $result['fee_amount'];
        $pmf = Db::getInstance()->executeS("SELECT wf.*,pmf.*, pmfl.title, pmfl.description
            FROM `" . _DB_PREFIX_ . "ets_am_withdrawal_field` wf
            LEFT JOIN `" . _DB_PREFIX_ . "ets_am_payment_method_field` pmf ON wf.id_payment_method_field = pmf.id_ets_am_payment_method_field
            LEFT JOIN `" . _DB_PREFIX_ . "ets_am_payment_method_field_lang` pmfl ON pmf.id_ets_am_payment_method_field = pmfl.id_payment_method_field
            WHERE pmf.id_payment_method = " . (int)$result['id_payment_method'] . " AND wf.id_withdrawal = " . (int)$id_withdrawal . " AND pmf.deleted = 0 AND pmfl.id_lang = " . (int)$id_lang_default);
        $result['payment_method_field'] = array();
        foreach ($pmf as $field) {
            $result['payment_method_field'][] = $field;
        }
        $result['can_withdraw'] = 0;
        if ((float)$result['total_point_remain'] >= 0) {
            $result['can_withdraw'] = 1;
        }
        $widthdraw = new Ets_Withdraw((int)$id_withdrawal);
        if ($widthdraw->status == 0) {
            if (Configuration::get('ETS_AM_ALLOW_WITHDRAW_LOYALTY_REWARDS'))
                $result['remaining_withdrawable'] = (float)$result['total_point'] - (float)$result['total_usage'];
            else
                $result['remaining_withdrawable'] = (float)$result['total_point'] - (float)$result['total_usage'] - $result['loy_rewards'];
            if ($result['remaining_withdrawable'] < 0)
                $result['can_withdraw2'] = 0;
            else
                $result['can_withdraw2'] = 1;
            $result['remaining_withdrawable'] = Ets_AM::displayRewardAdmin((float)$result['remaining_withdrawable']);
        }
        $result['total_balance'] = Ets_Am::displayRewardAdmin((float)$result['total_point'] - (float)$result['total_usage'], true, true);
        $result['total_point'] = Ets_Am::displayRewardAdmin((float)$result['total_point'], true, true);
        $result['loy_balance'] = Ets_Am::displayRewardAdmin((float)$result['loy_rewards'] - $result['total_loy_usage'], true, true);
        $result['ref_balance'] = Ets_Am::displayRewardAdmin((float)$result['ref_rewards'] - $result['total_ref_usage'], true, true);
        $result['aff_balance'] = Ets_Am::displayRewardAdmin((float)$result['aff_rewards'] - $result['total_aff_usage'], true, true);
        $result['mnu_balance'] = Ets_Am::displayRewardAdmin((float)$result['mnu_rewards'] - $result['total_mnu_usage'], true);
        $result['withdrawn'] = Ets_Am::displayRewardAdmin((float)$result['withdrawn'], true, true);
        $result['pay_for_order'] = Ets_Am::displayRewardAdmin((float)$result['pay_for_order'], true, true);
        $result['convert_to_voucher'] = Ets_Am::displayRewardAdmin((float)$result['convert_to_voucher'], true, true);
        $result['total_usage'] = Ets_Am::displayRewardAdmin((float)$result['total_usage'], true, true);
        $result['amount_withdraw'] = Ets_Am::displayRewardAdmin((float)$result['amount_withdraw'], true, true);
        $result['withdraw_status'] = (int)$result['withdraw_status'];
        if ($result['fee_amount'])
            $result['fee_amount'] = Ets_AM::displayRewardAdmin($result['fee_amount'], true, true);
        $result['amount_pay'] = Ets_AM::displayRewardAdmin($result['amount_pay'], true, true);
        return $result;
    }
    public static function getFieldsOfLastWithdrawal($id_customer, $id_payment_method)
    {
        $withdraw = Db::getInstance()->getRow("SELECT * FROM `" . _DB_PREFIX_ . "ets_am_withdrawal` wd
                                    JOIN `" . _DB_PREFIX_ . "ets_am_payment_method` pm ON wd.id_payment_method = pm.id_ets_am_payment_method
                                    JOIN `" . _DB_PREFIX_ . "ets_am_reward_usage` ru ON wd.id_ets_am_withdrawal = ru.id_withdraw
                                    WHERE wd.id_payment_method =  " . (int)$id_payment_method . " AND ru.id_customer = " . (int)$id_customer . "
                                    ORDER BY wd.id_ets_am_withdrawal DESC");
        if ($withdraw) {
            $method_fields = Db::getInstance()->executeS("SELECT * FROM `" . _DB_PREFIX_ . "ets_am_withdrawal_field` wf WHERE id_withdrawal = " . (int)$withdraw['id_ets_am_withdrawal']);
            if ($method_fields) {
                $fields = array();
                foreach ($method_fields as $field) {
                    $fields[$field['id_payment_method_field']] = $field;
                }
                return $fields;
            }
        }
        return array();
    }
    public function downloadInvoice()
    {
        if ($this->invoice && file_exists(_PS_DOWNLOAD_DIR_ . EAM_INVOICE_PATH . '/' . $this->invoice)) {
            header("Pragma: public");
            header("Expires: 0");
            header("X-Robots-Tag: noindex, nofollow", true);
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("Cache-Control: private", false);
            header("Content-Type: application/pdf");
            header("Content-Disposition: attachment; filename=\"" . $this->invoice . "\";");
            header("Content-Transfer-Encoding: Binary");
            $file_url = _PS_DOWNLOAD_DIR_ . EAM_INVOICE_PATH . '/' . $this->invoice;
            if ($fsize = @filesize($file_url)) {
                header("Content-Length: " . $fsize);
            }
            ob_clean();
            flush();
            readfile($file_url);
            exit();
        }
        return false;
    }
    public function update($null_values = false)
    {
        if(parent::update($null_values))
        {
            self::_clearCache();
        }
        return false;
    }

    public function add($auto_date= true,$null_values = false)
    {
        if(parent::add($auto_date,$null_values))
        {
            self::_clearCache();
            return true;
        }

        return false;
    }
    public function delete()
    {
        if(parent::delete())
        {
            self::_clearCache();
            return true;
        }
        return false;
    }

    public static function _clearCache()
    {
        /** @var Ets_affiliatemarketing $aff */
        $aff = Module::getInstanceByName('ets_affiliatemarketing');
        $aff->_clearCache('*',$aff->_getCacheId('list_withdrawal',false));
        $aff->_clearCache('*',$aff->_getCacheId('list_reward',false));
        return true;
    }
}
