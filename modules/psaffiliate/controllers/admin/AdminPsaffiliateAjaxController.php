<?php
/**
 * NOTICE OF LICENSE
 *
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * You must not modify, adapt or create derivative works of this source code.
 *
 * @author    Active Design <office@activedesign.ro>
 * @copyright 2016-2018 Active Design
 * @license   LICENSE.txt
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class AdminPsaffiliateAjaxController extends AdminController
{
    public function init()
    {
        parent::init();
        $action = Tools::getValue('action');
        if ($action == 'getCustomers') {
            $search_key = Tools::getValue('search_key');
            if (Tools::substr($search_key, 0, 1) == '#') {
                $result = Db::getInstance()->executeS('SELECT `id_customer`, `firstname`, `lastname`, `email` FROM `'._DB_PREFIX_.'customer` WHERE `id_customer` LIKE "%'.(int)Tools::substr(
                        $search_key,
                        1
                    ).'%"');
            } else {
                $result = Db::getInstance()->executeS('SELECT `id_customer`, `firstname`, `lastname`, `email` FROM `'._DB_PREFIX_.'customer` WHERE `id_customer` = "'.pSQL($search_key).'" OR `firstname` LIKE "%'.pSQL($search_key).'%" OR `lastname` LIKE "%'.pSQL($search_key).'%" OR `email` LIKE "%'.pSQL($search_key).'%" OR CONCAT(`firstname`, " ", `lastname`) LIKE "%'.pSQL($search_key).'%" OR CONCAT(`lastname`, " ", `firstname`) LIKE "%'.pSQL($search_key).'%"');
            }

            $good_result = array();
            foreach ($result as $row) {
                $good_result[] = array(
                    'id_object' => $row['id_customer'],
                    'value' => '#'.$row['id_customer'].' - '.$row['firstname'].' '.$row['lastname'].' - '.$row['email'],
                );
            }

            die(Tools::jsonEncode(array('success' => true, 'result' => $good_result)));
        } elseif ($action == 'getAffiliates') {
            $search_key = Tools::getValue('search_key');
            if (Tools::substr($search_key, 0, 1) == '#') {
                $result = Db::getInstance()->executeS('SELECT a.`id_affiliate`, c.`firstname`, c.`lastname`, c.`email` FROM `'._DB_PREFIX_.'aff_affiliates` a LEFT JOIN `'._DB_PREFIX_.'customer` c ON (c.`id_customer` = a.`id_customer`) WHERE a.`id_affiliate` LIKE "%'.(int)Tools::substr(
                        $search_key,
                        1
                    ).'%"');
            } else {
                $result = Db::getInstance()->executeS('SELECT a.`id_affiliate`, c.`firstname`, c.`lastname`, c.`email` FROM `'._DB_PREFIX_.'aff_affiliates` a LEFT JOIN `'._DB_PREFIX_.'customer` c ON (c.`id_customer` = a.`id_customer`) WHERE a.`id_affiliate` = "'.pSQL($search_key).'" OR c.`firstname` LIKE "%'.pSQL($search_key).'%" OR c.`lastname` LIKE "%'.pSQL($search_key).'%" OR c.`email` LIKE "%'.pSQL($search_key).'%" OR CONCAT(c.`firstname`, " ", c.`lastname`) LIKE "%'.pSQL($search_key).'%" OR CONCAT(c.`lastname`, " ", c.`firstname`) LIKE "%'.pSQL($search_key).'%"');
            }

            $good_result = array();
            foreach ($result as $row) {
                $good_result[] = array(
                    'id_object' => $row['id_affiliate'],
                    'value' => '#'.$row['id_affiliate'].' - '.$row['firstname'].' '.$row['lastname'].' - '.$row['email'],
                );
            }

            die(Tools::jsonEncode(array('success' => true, 'result' => $good_result)));
        } elseif ($action == 'getOrders') {
            $search_key = Tools::getValue('search_key');
            $result = Db::getInstance()->executeS('SELECT o.`id_order`, o.`reference`, o.`date_add`, c.`email`, CONCAT(c.`firstname`, " ", c.`lastname`) as `name` FROM `'._DB_PREFIX_.'orders` o LEFT JOIN `'._DB_PREFIX_.'customer` c ON (c.`id_customer` = o.`id_customer`) WHERE o.`id_order` LIKE "%'.pSQL($search_key).'%" OR o.`reference` LIKE "%'.pSQL($search_key).'%" OR o.`date_add` LIKE "%'.pSQL($search_key).'%" OR c.`firstname` LIKE "%'.pSQL($search_key).'%" OR c.`lastname` LIKE "%'.pSQL($search_key).'%" OR CONCAT(c.`firstname`, " ", c.`lastname`) LIKE "%'.pSQL($search_key).'%" OR CONCAT(c.`lastname`, " ", c.`firstname`) LIKE "%'.pSQL($search_key).'%" OR c.`email` LIKE "%'.pSQL($search_key).'%"');


            $good_result = array();
            foreach ($result as $row) {
                $good_result[] = array(
                    'id_object' => $row['id_order'],
                    'value' => '#'.$row['id_order'].' - '.$row['reference'].' - '.$row['name'].' - '.$row['email'],
                );
            }

            die(Tools::jsonEncode(array('success' => true, 'result' => $good_result)));
        } else {
            die(Tools::jsonEncode(array('success' => false, 'error' => 'wrong_action')));
        }
    }
}
