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
class Ets_Invitation extends ObjectModel
{
    public $email;
    public $name;
    public $datetime_sent;
    public $id_friend;
    public $id_sponsor;
    public static $definition = array(
        'table' => 'ets_am_invitation',
        'primary' => 'id_ets_am_invitation',
        'fields' => array(
            'email' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isString'
            ),
            'name' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isString'
            ),
            'datetime_sent' => array(
                'type' => self::TYPE_DATE,
                'validate' => 'isDateFormat',
                'allow_null' => true
            ),
            'id_friend' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt'
            ),
            'id_sponsor' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt'
            ),
        )
    );
    public static function totalEmailInvited($id_customer)
    {
        $sql = "SELECT COUNT(*) as total FROM `" . _DB_PREFIX_ . "ets_am_invitation` WHERE id_sponsor = " . (int)$id_customer;
        $total_email = (int)Db::getInstance()->getValue($sql);
        return $total_email;
    }
    public static function emailIsInvited($email)
    {
        if (!Validate::isEmail($email))
            return false;
        $sql = "SELECT COUNT(*) as total 
            FROM (SELECT email FROM `" . _DB_PREFIX_ . "customer` WHERE id_shop='" . (int)Context::getContext()->shop->id . "'
            UNION
                SELECT email FROM " . _DB_PREFIX_ . "ets_am_invitation) c
            WHERE c.email = '" . pSQL($email) . "'";
        return (int)Db::getInstance()->getValue($sql);
    }
    public static function getIdCustomerByEmail($email)
    {
        $customers = Customer::getCustomersByEmail($email);
        if ($customers) {
            return (int)$customers[0]['id_customer'];
        }
        return 0;
    }
    public static function updateIdFriend($id_customer, $email)
    {
        if(!Validate::isEmail($email))
            return false;
        $sql = "SELECT id_ets_am_invitation, email FROM `" . _DB_PREFIX_ . "ets_am_invitation` WHERE email = '" . pSQL($email) . "'";
        if (($invitation = Db::getInstance()->getRow($sql)) && isset($invitation['id_ets_am_invitation']) && $invitation['id_ets_am_invitation']) {
            $inv = new Ets_Invitation($invitation['id_ets_am_invitation']);
            $inv->id_friend = $id_customer;
            $inv->update();
            return true;
        }
        return false;
    }
    public static function getInvitations($params = array())
    {
        $page = isset($params['page']) && ($page = (int)$params['page']) && $page > 0 ? $page : 1;
        $limit = isset($params['limit']) && ($limit = (int)$params['limit']) && $limit > 0 ? $limit : 10;
        $filter_where = "";
        if (isset($params['id_customer']) && $params['id_customer']) {
            $filter_where .= " AND inv.id_sponsor = " . (int)$params['id_customer'];
        }
        $sql_total = "SELECT COUNT(*) AS total 
                    FROM `" . _DB_PREFIX_ . "ets_am_invitation` inv
                    LEFT JOIN `" . _DB_PREFIX_ . "customer` customer ON inv.email = customer.email
                    WHERE 1 $filter_where";
        $total_result = (int)Db::getInstance()->getValue($sql_total);
        if ($total_result > 0) {

            $total_page = ceil($total_result / $limit);
            $offset = ($page - 1) * $limit;
            $sql = "SELECT inv.*, customer.firstname as firstname, customer.lastname as lastname, IF(inv.id_friend > 0, 1, 0) as status 
                FROM `" . _DB_PREFIX_ . "ets_am_invitation` inv
                LEFT JOIN `" . _DB_PREFIX_ . "customer` customer ON inv.email = customer.email
                WHERE 1 $filter_where
                ORDER BY inv.datetime_sent DESC
                LIMIT " . (int)$offset . ", " . (int)$limit;
            $results = Db::getInstance()->executeS($sql);
            foreach ($results as &$result) {
                $result['username'] = $result['firstname'] ? $result['firstname'] . $result['lastname'] : $result['name'];
            }
            return array(
                'total_result' => $total_result,
                'total_page' => $total_page,
                'current_page' => $page,
                'per_page' => $limit,
                'result' => $results
            );
        }
        return array(
            'total_result' => 0,
            'total_page' => 1,
            'current_page' => 1,
            'per_page' => $limit,
            'result' => array()
        );
    }
}