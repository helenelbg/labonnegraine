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
class Ets_Sponsor extends Ets_AM
{
    public $id_customer;
    public $id_parent;
    public $level;
    public $id_shop;
    public $datetime_added;
    public static $definition = array(
        'table' => 'ets_am_sponsor',
        'primary' => 'id_ets_am_sponsor',
        'fields' => array(
            'id_customer' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt'
            ),
            'id_parent' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt'
            ),
            'level' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt'
            ),
            'id_shop' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt'
            ),
            'datetime_added' => array(
                'type' => self::TYPE_DATE,
                'validate' => 'isDate',
                'allow_null' => true
            )
        )
    );
    public function __construct()
    {
        parent::__construct();
    }
    /**
     * @param null $context
     * @return bool
     */
    public static function canUseRefferalProgram($id_customer)
    {
        $id_customer = (int)$id_customer;
        $context = Context::getContext();
        $customer = new Customer($id_customer);
        if (!$customer->id)
            return false;
        $cg = $customer->getGroups();
        $customer_groups = $cg;
        if ($id_customer) {
            if (self::isRefferalProgramReady()) { //1st condition
                //Check group user
                $groups_user_allow = Configuration::get('ETS_AM_REF_GROUPS');
                if ($groups_user_allow) {
                    if ($groups_user_allow !== 'ALL') { // 2nd
                        $group_user_arr = explode(',', $groups_user_allow);
                        if (!array_intersect($group_user_arr, $customer_groups)) { // Not in group allow
                            return false;
                        }
                    } else {
                        /*$group_user_arr = array();
                        $groups = Group::getGroups($context->language->id);
                        foreach ($groups as $g) {
                            if ((int)$g['id_group'] > 2) {
                                $group_user_arr[] = (int)$g['id_group'];
                            }
                        }
                        if (!array_intersect($group_user_arr, $customer_groups)) { // Not in group allow
                            return false;
                        }*/
                    }
                }
                $minimun_order_required = (float)Configuration::get('ETS_AM_REF_MIN_ORDER');
                if ($minimun_order_required) {
                    $list_states = Configuration::get('ETS_AM_VALIDATED_STATUS');
                    if (!$list_states) {
                        $list_states = '0';
                    }
                    $list_states = trim($list_states, ',');
                    $total_orders = (float)Db::getInstance()->getValue("SELECT SUM(total_paid) FROM `" . _DB_PREFIX_ . "orders` WHERE current_state IN(" . implode(',',array_map('intval',explode(',',$list_states))) . ") AND id_customer = " . (int)$id_customer);
                    if ($total_orders < (float)$minimun_order_required) { //3rd confitions
                        return false;
                    }
                }
                return true;
            }
        }
        return false;
    }
    public static function canUseRefferalProgramReturn($id_customer)
    {
        $context = Context::getContext();
        $id_customer = (int)$id_customer;
        $customer = new Customer($id_customer);
        if (!$customer->id)
            return array(
                'success' => false
            );
        $cg = $customer->getGroups();
        $customer_groups = $cg;
        if ($id_customer) {
            if (self::isRefferalProgramReady()) { //1st condition
                //Check group user
                $groups_user_allow = Configuration::get('ETS_AM_REF_GROUPS');
                if ($groups_user_allow) {
                    if ($groups_user_allow !== 'ALL') { // 2nd
                        $group_user_arr = explode(',', $groups_user_allow);
                        if (!array_intersect($group_user_arr, $customer_groups)) { // Not in group allow
                            return array(
                                'success' => false,
                                'not_in_group' => true
                            );
                        }
                    } else {
                        /*$group_user_arr = array();
                        $groups = Group::getGroups($context->language->id);
                        foreach ($groups as $g) {
                            if ((int)$g['id_group'] > 2) {
                                $group_user_arr[] = (int)$g['id_group'];
                            }
                        }
                        if (!array_intersect($group_user_arr, $customer_groups)) { // Not in group allow
                            return array(
                                'success' => false,
                                'not_in_group' => true
                            );
                        }*/
                    }
                }
                $minimun_order_required = (float)Configuration::get('ETS_AM_REF_MIN_ORDER');
                if ($minimun_order_required) {
                    $list_states = Configuration::get('ETS_AM_VALIDATED_STATUS');
                    if (!$list_states) {
                        $list_states = '0';
                    }
                    $list_states = trim($list_states, ',');
                    $total_orders = (float)Db::getInstance()->getValue("SELECT SUM(total_paid) FROM `" . _DB_PREFIX_ . "orders` WHERE current_state IN(" . pSQL($list_states) . ") AND id_customer = " . (int)$id_customer);
                    if ($total_orders < $minimun_order_required) { //3rd confitions
                        return array(
                            'success' => false,
                            'min_order' => $minimun_order_required,
                            'total_order' => $total_orders
                        );
                    }
                }
                return array(
                    'success' => true
                );
            }
        }
        return array(
            'success' => false
        );
    }
    /**
     * @return bool
     */
    public static function isRefferalProgramReady()
    {
        return (int)Configuration::get('ETS_AM_REF_ENABLED') ? true : false;
    }
    /**
     * @return bool
     */
    public static function registeredReferralProgram($id_customer)
    {
        $id_customer = (int)$id_customer;
        $context = Context::getContext();
        $id_shop = (int)$context->shop->id;
        $enable_register = (int)Configuration::get('ETS_AM_REF_REGISTER_REQUIRED');
        if (!self::isRefferalProgramReady()) {
            return false;
        }
        if (!$enable_register) {
            return self::canUseRefferalProgram($id_customer);
        }
        $user = Ets_User::getUserByCustomerId($id_customer);
        if ($user) {
            if ((int)$user['status'] == 1) {
                if (!$enable_register) {
                    if ((int)$user['ref'] >= 0) {
                        return true;
                    }
                } else {
                    if ((int)$user['ref'] == 1) {
                        return true;
                    }
                }
            }
            return false;
        } else {
            if (!$enable_register) {
                return true;
            } else {
                $exists = Db::getInstance()->getValue("SELECT program FROM `" . _DB_PREFIX_ . "ets_am_participation`
                 WHERE `id_customer` = " . (int)$id_customer . " 
                 AND `id_shop` = " . (int)$id_shop . " 
                 AND `program` = 'ref'
                 AND `status` IN (0, 1)");
                if ($exists) {
                    return true;
                }
            }
        }
        return false;
    }
    /**
     * @param $ref
     * @param null $context
     * @return bool
     * @throws Exception
     */
    public static function setCookieRef($ref)
    {
        $context = Context::getContext();
        $ref = (int)$ref;
        if ($ref) {
            $ref_exists = (int)$context->cookie->__get(EAM_REFS);
            if (!$ref_exists || $ref !== $ref_exists) {
                if (($customer = new Customer($ref)) && $customer->id) {
                    $setcookie = false;
                    if (!(int)Configuration::get('ETS_AM_REF_REGISTER_REQUIRED')) {
                        if (self::canUseRefferalProgram($ref)) {
                            $setcookie = true;
                        }
                    } else {
                        if (self::joinedReferralProgram($ref)) {
                            $setcookie = true;
                        }
                    }
                    if ($setcookie) {
                        $context->cookie->__set(EAM_REFS, $ref);
                        if ($redirect = Configuration::get('ETS_AM_REF_URL_REDIRECT')) {
                            Tools::redirect($redirect);
                        }
                    }
                }
            }
        }
        return true;
    }
    public static function getSponsorIdByCustomerId($id_customer)
    {
        $id_customer = (int)$id_customer;
        $id_shop = (int)Context::getContext()->shop->id;
        $sql = "SELECT id_parent FROM `" . _DB_PREFIX_ . "ets_am_sponsor` WHERE id_customer = ".(int)$id_customer." AND level = 1 AND id_shop = " . (int)$id_shop;
        return (int)Db::getInstance()->getValue($sql);
    }
    /**
     * @param int $status
     * @param null $sub_point_type
     * @return bool
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public static function addPointReward($id_customer, $status = 0, $sub_program = null, $id_order = 0)
    {
        $id_customer = (int)$id_customer;
        $status = (int)$status;
        $id_order = (int)$id_order;
        $context = Context::getContext();
        $ref = (int)$context->cookie->__get(EAM_REFS);
        if ((int)$context->customer->id == $ref) {
            return false;
        }
        if (!self::isActive($id_customer)) {
            return false;
        }
        $reward = (float)Configuration::get('ETS_AM_REF_FRIEND_EACH_REG_COST');
        if ($reward <= 0) {
            return false;
        }
        if ($sub_program == Ets_AM::TYPE_REG) {
            $max_sponsor = Configuration::get('ETS_AM_REF_MAX_FRIEND');
            $max_give_reward = Configuration::get('ETS_AM_REF_FRIEND_FIRST_REG_ONLY');
            $num_friend_give_reward = 'unlimited';
            if ($max_sponsor !== '' && $max_sponsor !== false && $max_sponsor !== NULL) {
                if ($max_give_reward !== '' && $max_give_reward !== false && $max_give_reward !== NULL) {
                    if ((int)$max_sponsor > (int)$max_give_reward) {
                        $num_friend_give_reward = (int)$max_give_reward;
                    } else {
                        $num_friend_give_reward = (int)$max_sponsor;
                    }
                } else {
                    $num_friend_give_reward = (int)$max_sponsor;
                }
            } else {
                if ($max_give_reward !== '' && $max_give_reward !== false && $max_give_reward !== NULL) {
                    $num_friend_give_reward = (int)$max_give_reward;
                }
            }
        }
        if ($num_friend_give_reward !== 'unlimited') {
            $friends = self::getFriendsOfSponsor($id_customer, $num_friend_give_reward, 'ASC');
            if (!empty($friends)) {
                if (!in_array((int)$context->customer->id, array_column($friends, 'id_customer'))) {
                    return false;
                }
            } else {
                return false;
            }
        }
        //add point
        $trans = Module::getInstanceByName('ets_affiliatemarketing')->getTranslates();
        $eam = new Ets_AM;
        $eam->id_customer = $id_customer;
        $eam->program = EAM_AM_REF_REWARD;
        $eam->amount = $reward;
        $eam->sub_program = $sub_program;
        $eam->status = $status;
        $eam->id_order = ($order = new Order((int)$id_order)) && $order->id ? $order->id : 0;
        $eam->id_friend = (int)$context->customer->id;
        $eam->datetime_added = date('Y-m-d H:i:s');
        $eam->note = sprintf($trans['note_reward_ref_user'], (int)$context->customer->id);
        if ($status == 1) {
            $eam->datetime_validated = date('Y-m-d H:i:s');
        }
        if ($eam->add()) {
            self::sendMailRewardCreated(null, $eam->id);
            if ($status == 1) {
                self::sendMailRewardValidated(null, $eam->id);
            } else if ($status == -1) {
                self::sendMailRewardCanceled(null, $eam->id);
            }
            return true;
        }
        return false;
    }
    /**
     * @param $cart
     * @return bool
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public static function getRewardWithFirstOrder($cart, $order)
    {
        $context = Context::getContext();
        if (!$order || !$order->id)
            return false;
        if ((int)Configuration::get('ETS_AM_REF_FRIEND_REG') && (int)Configuration::get('ETS_AM_REF_FRIEND_ORDER_REQUIRED')) {
            if (self::countOrderCustomer((int)$context->customer->id) === 1 && self::checkRewardRefRegister($cart)) {
                $res = Ets_AM::mapOrderStateToRewardState(new OrderState($order->current_state));
                if($res)
                {
                    $status = $res['status'];
                    $id_customer = self::getSponsorIdByCustomerId($context->customer->id);
                    if ($id_customer) {
                        return self::addPointReward($id_customer, $status, Ets_AM::TYPE_REG, $order->id);
                    }
                }

            }
        }
        return false;
    }
    public static function checkRewardRefRegister($cart)
    {
        if (!$cart || !$cart->id)
            return false;
        $count_proudct_in_cart = Db::getInstance()->getValue('SELECT COUNT(DISTINCT id_product) FROM `' . _DB_PREFIX_ . 'cart_product` WHERE id_cart="' . (int)$cart->id . '"');
        if (Configuration::get('ETS_AM_REF_REGISTER_CATEGORIES') || Configuration::get('ETS_AM_REF_REGISTER_PRODUCTS_EXCLUDED') || Configuration::get('ETS_AM_REF_REGISTER_PRODUCTS_EXCLUDED_DISCOUNT')) {
            $where = '';
            $sql = 'SELECT COUNT(DISTINCT cart_product.id_product) FROM `' . _DB_PREFIX_ . 'cart_product` cart_product';
            if (Configuration::get('ETS_AM_REF_REGISTER_CATEGORIES')) {
                $categories = explode(',', Configuration::get('ETS_AM_REF_REGISTER_CATEGORIES'));
                $sql .= ' LEFT JOIN `' . _DB_PREFIX_ . 'category_product` cp ON (cp.id_product= cart_product.id_product)';
                $where .= ' OR cp.id_category IN (' . pSQL(implode(',', array_map('intval', $categories))) . ')';
            }
            if (Configuration::get('ETS_AM_REF_REGISTER_PRODUCTS_EXCLUDED')) {
                $idProducts = explode(',', Configuration::get('ETS_AM_REF_REGISTER_PRODUCTS_EXCLUDED'));
                $where .= ' OR cart_product.id_product IN (' . pSQL(implode(',', array_map('intval', $idProducts))) . ')';
            }
            if (Configuration::get('ETS_AM_REF_REGISTER_PRODUCTS_EXCLUDED_DISCOUNT')) {
                $sql .= ' LEFT JOIN `' . _DB_PREFIX_ . 'specific_price` sp ON (sp.id_product=cart_product.id_product)';
                $where .= ' OR (sp.reduction AND (`from` = "0000-00-00 00:00:00" OR `from` <="' . pSQL(date('Y-m-d H:i:s')) . '" ) AND (`to` = "0000-00-00 00:00:00" OR `to` >="' . pSQL(date('Y-m-d H:i:s')) . '" ))';
            }
            if ($count_proudct_in_cart == Db::getInstance()->getValue($sql . ' WHERE cart_product.id_cart="' . (int)$cart->id . '" AND ( 0 ' . (string)$where . ' )'))
                return false;
        }
        return true;
    }
    /**
     * @return bool|int
     */
    public static function countOrderCustomer($id_customer)
    {
        if ((int)$id_customer) {
            $sql = "SELECT COUNT(id_order) as `total`
                 FROM `" . _DB_PREFIX_ . "orders` 
                 WHERE id_customer = " . (int)$id_customer;
            $total = (int)Db::getInstance()->getValue($sql);
            return $total;
        }
        return false;
    }
    /**
     * @return bool
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public static function getRewardWithoutOrder($ref = 0)
    {
        $context = Context::getContext();
        $ref = (int)$ref;
        if (!(int)Configuration::get('ETS_AM_REF_FRIEND_ORDER_REQUIRED') && (int)Configuration::get('ETS_AM_REF_FRIEND_REG')) {
            if (!$ref) {
                $ref = (int)self::getSponsorIdByCustomerId((int)$context->customer->id);
            }
            if ($context->customer->id == $ref) {
                return false;
            }
            if ($ref) {
                return self::addPointReward($ref, 1, Ets_AM::TYPE_REG);
            }
        }
        return false;
    }
    public static function checkRewardRef($cart)
    {
        if (!$cart || !$cart->id)
            return false;
        $count_proudct_in_cart = Db::getInstance()->getValue('SELECT COUNT(DISTINCT id_product) FROM `' . _DB_PREFIX_ . 'cart_product` WHERE id_cart="' . (int)$cart->id . '"');
        if (Configuration::get('ETS_AM_REF_CATEGORIES') || Configuration::get('ETS_AM_REF_PRODUCTS_EXCLUDED') || Configuration::get('ETS_AM_REF_PRODUCTS_EXCLUDED_DISCOUNT')) {
            $where = '';
            $sql = 'SELECT DISTINCT cart_product.id_product FROM `' . _DB_PREFIX_ . 'cart_product` cart_product';
            if (Configuration::get('ETS_AM_REF_CATEGORIES')) {
                $categories = explode(',', Configuration::get('ETS_AM_REF_CATEGORIES'));
                $sql .= ' LEFT JOIN `' . _DB_PREFIX_ . 'category_product` cp ON (cp.id_product= cart_product.id_product)';
                $where .= ' OR cp.id_category IN (' . pSQL(implode(',', array_map('intval', $categories))) . ')';
            }
            if (Configuration::get('ETS_AM_REF_PRODUCTS_EXCLUDED')) {
                $idProducts = explode(',', Configuration::get('ETS_AM_REF_PRODUCTS_EXCLUDED'));
                $where .= ' OR cart_product.id_product IN (' . pSQL(implode(',', array_map('intval', $idProducts))) . ')';
            }
            if (Configuration::get('ETS_AM_REF_PRODUCTS_EXCLUDED_DISCOUNT')) {
                $sql .= ' LEFT JOIN `' . _DB_PREFIX_ . 'specific_price` sp ON (sp.id_product=cart_product.id_product)';
                $where .= ' OR (sp.reduction AND (`from` = "0000-00-00 00:00:00" OR `from` <="' . pSQL(date('Y-m-d H:i:s')) . '" ) AND (`to` = "0000-00-00 00:00:00" OR `to` >="' . pSQL(date('Y-m-d H:i:s')) . '" ))';
            }
            $products = Db::getInstance()->executeS($sql . ' WHERE cart_product.id_cart="' . (int)$cart->id . '" AND ( 0 ' . $where . ' )');
            if ($count_proudct_in_cart == count($products))
                return false;
            elseif ($products)
                return $products;
            else
                return true;
        }
        return true;
    }
    /**
     * @param $objOrder
     * @throws PrestaShopDatabaseException
     */
    public static function getRewardOnOrder($objOrder)
    {
        if (!$objOrder || !isset($objOrder['order']) || !isset($objOrder['cart']))
            return false;
        $context = Context::getContext();
        $id_customer = (int)$context->customer->id;
        $id_shop = (int)$context->shop->id;
        $order = $objOrder['order'];
        $cart = $objOrder['cart'];
        //Check customer sponsored to reward program or not
        $sql = "SELECT * FROM `" . _DB_PREFIX_ . "ets_am_sponsor` WHERE id_customer = " . (int)$id_customer . " AND id_parent > 0 AND level = 1 AND id_shop = " . (int)$id_shop;
        $sponsored = Db::getInstance()->getRow($sql);
        if ($sponsored) {
            if ((int)Configuration::get('ETS_AM_REF_GIVE_REWARD_ON_ORDER') && $excludeProducts = self::checkRewardRef($cart)) { //if enable give reward on order
                $friend_limited = Configuration::get('ETS_AM_REF_REWARD_FRIEND_LIMIT');
                $order_limited = Configuration::get('ETS_AM_REF_REWARD_ORDER_LIMIT');
                $reward_by = Configuration::get('ETS_AM_REF_HOW_TO_CALCULATE');
                $max_friend_sponsor = (int)Configuration::get('ETS_AM_REF_MAX_FRIEND');
                $min_order_to_get_reward = Configuration::get('ETS_AM_REF_REWARD_ORDER_MIN');
                $total_order_paid = $order->total_paid_tax_incl;
                if ($min_order_to_get_reward != '' && $min_order_to_get_reward != NULL && $min_order_to_get_reward != false && (float)$min_order_to_get_reward > $total_order_paid) {
                    return false;
                }
                $sql_order_customer = "SELECT COUNT(*) AS total FROM `" . _DB_PREFIX_ . "orders` as orders 
                                    WHERE orders.id_customer = " . (int)$id_customer;
                $total_order_got_reward = (int)Db::getInstance()->getValue($sql_order_customer);
                if ($order_limited != '' && $order_limited != NULL && $order_limited != false && $total_order_got_reward > (int)$order_limited) {
                    return false;
                }
                if (!$friend_limited) {
                    $friend_limited = $max_friend_sponsor;
                } elseif ($max_friend_sponsor && (int)$friend_limited > (int)$max_friend_sponsor) {
                    $friend_limited = $max_friend_sponsor;
                }
                $sql_limit = '';
                if ($friend_limited) {
                    $sql_limit = " LIMIT " . (int)$friend_limited;
                }
                $sql_sponsor = "SELECT `id_customer` FROM `" . _DB_PREFIX_ . "ets_am_sponsor`                              WHERE id_parent = " . (int)$sponsored['id_parent'] . " 
                             AND level = 1
                             AND id_shop = " . (int)$id_shop . "
                             ORDER BY id_ets_am_sponsor ASC " . (string)$sql_limit;
                $sponsor_data = Db::getInstance()->executeS($sql_sponsor);
                $sponsor_data_ids = array();
                if ($sponsor_data) {
                    foreach ($sponsor_data as $item) {
                        $sponsor_data_ids[] = $item['id_customer'];
                    }
                }
                if (!in_array($id_customer, $sponsor_data_ids)) {
                    return false;
                }
                if ($reward_by) {
                    $reward_by_money = 0;
                    if ($reward_by == 'PERCENTATE') {
                        $cost_percent = (float)Configuration::get('ETS_AM_REF_SPONSOR_COST_PERCENT');
                        $tax_exclude = (int)Configuration::get('ETS_AM_REF_TAX_EXCLUDED');
                        if ($cart) {
                            if ($tax_exclude) {
                                $total_money_order = (float)$order->total_paid_tax_excl - (float)$order->total_shipping;
                            } else {
                                $total_money_order = (float)$order->total_paid_tax_incl - (float)$order->total_shipping;
                            }
                            if (is_array($excludeProducts)) {
                                foreach ($excludeProducts as $product) {
                                    if ($tax_exclude)
                                        $total_money_order -= Db::getInstance()->getValue('SELECT SUM(total_price_tax_excl) FROM `' . _DB_PREFIX_ . 'order_detail` WHERE id_order="' . (int)$order->id . '" AND product_id="' . (int)$product['id_product'] . '"');
                                    else
                                        $total_money_order -= Db::getInstance()->getValue('SELECT SUM(total_price_tax_incl) FROM `' . _DB_PREFIX_ . 'order_detail` WHERE id_order="' . (int)$order->id . '" AND product_id="' . (int)$product['id_product'] . '"');
                                }
                            }
                            $reward_by_money = $cost_percent * $total_money_order / 100;
                        }
                    } else {
                        $cost_fixed = (float)Configuration::get('ETS_AM_REF_SPONSOR_COST_FIXED');
                        $reward_by_money = $cost_fixed;
                    }
                    if ($reward_by_money > 0) {
                        self::caculateRewardEachLevel($reward_by_money, $objOrder);
                    }
                }
            }
        }
    }
    /**
     * @return bool
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public static function addFriendSponsored($ref = null)
    {
        $context = Context::getContext();
        if (!$ref) {
            $ref = $context->cookie->__get(EAM_REFS);
        }
        if ($ref && $context->customer->id != $ref && self::isActive($ref)) {
            $id_sponsor = (int)$ref;
            $id_customer = (int)$context->customer->id;
            $id_shop = (int)$context->shop->id;
            $sql_limit = "SELECT COUNT(*) as total FROM `" . _DB_PREFIX_ . "ets_am_sponsor` WHERE id_parent = " . (int)$id_sponsor . " AND level = 1 AND id_shop = " . (int)$id_shop;
            $total_limit = Db::getInstance()->getValue($sql_limit);
            $max_friend_sponsor = Configuration::get('ETS_AM_REF_MAX_FRIEND');
            if ($max_friend_sponsor !== '' && $max_friend_sponsor !== false && $max_friend_sponsor !== NULL && (int)$total_limit >= (int)$max_friend_sponsor) {
                return false;
            }
            $sql = "SELECT COUNT(*) as total FROM `" . _DB_PREFIX_ . "ets_am_sponsor` WHERE id_customer = " . (int)$id_customer . " AND id_shop = " . (int)$id_shop;
            $sponsor_exists = (int)Db::getInstance()->getValue($sql);
            $sql = "SELECT COUNT(*) as total FROM `" . _DB_PREFIX_ . "ets_am_sponsor` WHERE id_parent = " . (int)$id_customer . " AND id_shop = " . (int)$id_shop;
            $parent_exists = (int)Db::getInstance()->getValue($sql);
            if (!$parent_exists && !$sponsor_exists && $id_sponsor !== $id_customer) {
                $eam_sponsor = new Ets_Sponsor();
                $eam_sponsor->id_customer = $id_customer;
                $eam_sponsor->id_parent = $id_sponsor;
                $eam_sponsor->level = 1;
                $eam_sponsor->id_shop = $id_shop;
                $eam_sponsor->datetime_added = date('Y-m-d H:i:s');
                $eam_sponsor->add();
                $sqlGetParent = "SELECT * FROM `" . _DB_PREFIX_ . "ets_am_sponsor` WHERE id_customer = " . (int)$id_sponsor . " AND id_shop = " . (int)$id_shop;
                $parents = Db::getInstance()->executeS($sqlGetParent);
                if (count($parents)) {
                    $values_insert = '';
                    $datetime_added = date('Y-m-d H:i:s');
                    foreach ($parents as $parent) {
                        $values_insert .= "(".(int)$id_customer.", " . (int)$parent['id_parent'] . ", " . ((int)$parent['level'] + 1) . ", " . (int)$id_shop . ", '".pSQL($datetime_added)."'),";
                    }
                    $sql_insert = "INSERT INTO `" . _DB_PREFIX_ . "ets_am_sponsor` (`id_customer`, `id_parent`, `level`, `id_shop`, `datetime_added`) VALUES " . trim($values_insert, ',');
                    Db::getInstance()->execute($sql_insert);
                }
                return true;
            }
        }
        return false;
    }
    /**
     * @param $reward_by_money
     * @param $friend_limited
     * @param $objOrder
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public static function caculateRewardEachLevel($reward_by_money, $objOrder)
    {
        if (!$objOrder || !isset($objOrder['order']))
            return false;
        $context = Context::getContext();
        $trans =Module::getInstanceByName('ets_affiliatemarketing')->getTranslates();
        $conversion_rate = $context->currency->conversion_rate;
        if ($conversion_rate)
            $reward_by_money = (float)$reward_by_money / $conversion_rate;
        if ($context->customer) {
            $id_customer = (int)$context->customer->id;
            $id_shop = $objOrder['order']->id_shop;
            $sql = "SELECT * FROM `" . _DB_PREFIX_ . "ets_am_sponsor` WHERE id_customer = " . (int)$id_customer . " AND id_shop = " . (int)$id_shop . " ORDER BY level ASC";
            $sponsors = Db::getInstance()->executeS($sql);
            if ($reward_by_money > 0) {
                $status = ($state = Ets_AM::mapOrderStateToRewardState(new OrderState($objOrder['order']->current_state))) ? $state['status'] : 0;
                $datetime_validate = null;
                if ($status == 1) {
                    $datetime_validate = "'" . date('Y-m-d H:i:s') . "'";
                }
                $id_order = $objOrder['order']->id;
                $datetime_added = date('Y-m-d H:i:s');
                $lower_level = Configuration::get('ETS_AM_REF_SPONSOR_COST_LEVEL_LOWER');
                $point_level_1 = 0;
                $id_reward_1 = 0;
                $id_reward_min = 0;
                $ETS_AM_REF_ENABLED_MULTI_LEVEL = (int)Configuration::get('ETS_AM_REF_ENABLED_MULTI_LEVEL');
                if (count($sponsors)) {
                    foreach ($sponsors as $sponsor) {
                        $level = $sponsor['level'];
                        $id_sponsor = (int)$sponsor['id_parent'];
                        if (!$ETS_AM_REF_ENABLED_MULTI_LEVEL && $level > 1)
                            break;
                        $level_percent = (float)Configuration::get('ETS_AM_REF_SPONSOR_COST_LEVEL_' . $level);
                        if ($level_percent && $level_percent > 0) {
                            $reward_sponsor = $level_percent / 100 * $reward_by_money;
                            $reward_by_money = $reward_by_money - $reward_sponsor;
                            if (!self::isActive($id_sponsor)) {
                                continue;
                            }
                            $point = $reward_sponsor;
                            $r = new Ets_AM();
                            $r->amount = $point;
                            $r->program = EAM_AM_REF_REWARD;
                            $r->status = $status;
                            $r->datetime_added = $datetime_added;
                            $r->datetime_validated = $datetime_validate;
                            $r->id_customer = $id_sponsor;
                            $r->id_friend = $id_customer;
                            $r->id_order = $id_order;
                            $r->id_shop = $id_shop;
                            $r->note = sprintf($trans['note_reward_ref_order'], $id_order, $level);
                            $r->add();
                            if (!$id_reward_min)
                                $id_reward_min = $r->id;
                            if ($level == 1) {
                                $point_level_1 = $point;
                                $id_reward_1 = $r->id;
                            }
                        } else {
                            if ($lower_level !== '' && $lower_level !== false && $lower_level !== NULL && (float)$lower_level > 0) {
                                $reward_sponsor = (float)$lower_level / 100 * $reward_by_money;
                                $reward_by_money = $reward_by_money - $reward_sponsor;
                                if (!self::isActive($id_sponsor)) {
                                    continue;
                                }
                                $point = $reward_sponsor;
                                $r = new Ets_AM();
                                $r->amount = $point;
                                $r->program = EAM_AM_REF_REWARD;
                                $r->status = $status;
                                $r->datetime_added = $datetime_added;
                                $r->datetime_validated = $datetime_validate;
                                $r->id_customer = $id_sponsor;
                                $r->id_friend = $id_customer;
                                $r->id_order = $id_order;
                                $r->id_shop = $id_shop;
                                $r->note = sprintf($trans['note_reward_ref_order'], $id_order, $level);
                                $r->add();
                                if (!$id_reward_min)
                                    $id_reward_min = $r->id;
                            } else {
                                break;
                            }
                        }
                    }
                    if ($reward_by_money > 0) {
                        if ((int)Configuration::get('ETS_AM_REF_SPONSOR_COST_REST_TO_FIRST')) {
                            $point = $reward_by_money;
                            $point = $point_level_1 + $point;
                            $r = new Ets_AM($id_reward_1);
                            $r->amount = $point;
                            $r->update();
                        }
                    }
                    self::sendMailRewardCreated($id_order, $id_reward_min);
                    if ($status == 1) {
                        self::sendMailRewardValidated($id_order);
                    } else if ($status == -1) {
                        self::sendMailRewardCanceled($id_order);
                    }
                }
            }
        }
    }
    /**
     * @param null $id_order
     * @param bool $get_last_reward
     * @return bool
     * @throws PrestaShopDatabaseException
     */
    public static function sendMailRewardValidated($id_order = null, $id_reward = null)
    {
        $enableEmail = (int)Configuration::get('ETS_AM_ENABLED_EMAIL_CUSTOMER_RC');
        if ($enableEmail && ($customers = self::getRewards($id_order,$id_reward))) {
            $module = Module::getInstanceByName('ets_affiliatemarketing');
            $subjects = array(
                'translation' => $module->l('Your reward was approved', 'ets_sponsor'),
                'origin' => 'Your reward was approved',
                'specific' => 'ets_sponsor'
            );
            foreach ($customers as $customer) {
                if(($email = $customer['email']) && Validate::isEmail($email))
                {
                    self::sendMail(0,'reward_validated',$subjects,$customer,array('customer' => trim($email)));
                }
            }
        }
    }
    public static function sendMailAdminRewardValidated($id_order = null, $id_reward = null)
    {
        $enableEmail = (int)Configuration::get('ETS_AM_ENABLED_EMAIL_ADMIN_RVOC');
        if ($enableEmail && ($adminEmail = Configuration::get('ETS_AM_EMAILS_CONFIRM')) && ($adminEmail = explode(',', $adminEmail))  && ($rewards = self::getRewards($id_order,$id_reward))) { //Send mail if enable
            $module = Module::getInstanceByName('ets_affiliatemarketing');
            $subjects = array(
                'translation' => $module->l('A reward was approved', 'ets_sponsor'),
                'origin' => 'A reward was approved',
                'specific' => 'ets_sponsor'
            );
            foreach ($adminEmail as $to) {
                if($to && Validate::isEmail($to))
                foreach($rewards as $reward)
                {
                    self::sendMail(0, 'reward_validated_admin', $subjects, $reward, array('employee' => trim($to)));
                }
            }
        }
    }
    /**
     * @param null $id_order
     * @param bool $get_last_reward
     * @return bool
     * @throws PrestaShopDatabaseException
     */
    public static function sendMailRewardCanceled($id_order = null, $id_reward = null)
    {
        $enableEmail = (int)Configuration::get('ETS_AM_ENABLED_EMAIL_CUSTOMER_RC');
        if ($enableEmail && ($customers = self::getRewards($id_order,$id_reward))) {
            $module = Module::getInstanceByName('ets_affiliatemarketing');
            $subjects = array(
                'translation' => $module->l('Your reward was canceled', 'ets_sponsor'),
                'origin' => 'Your reward was canceled',
                'specific' => 'ets_sponsor'
            );
            foreach ($customers as $customer) {
                if(($email = trim($customer['email'])) && Validate::isEmail($email))
                {
                    self::sendMail(0,'reward_canceled',$subjects,$customer,array('customer' => $email));
                }
            }
        }
    }
    public static function sendMailAdminRewardCanceled($id_order = null, $id_reward = null)
    {
        $enableEmail = (int)Configuration::get('ETS_AM_ENABLED_EMAIL_CUSTOMER_RC');
        if ($enableEmail && ($adminEmail = Configuration::get('ETS_AM_EMAILS_CONFIRM')) && ($adminEmail = explode(',',$adminEmail)) && ($customers = self::getRewards($id_order,$id_reward))) {
            $module = Module::getInstanceByName('ets_affiliatemarketing');
            $subjects = array(
                'translation' => $module->l('A reward was canceled', 'ets_sponsor'),
                'origin' => 'A reward was canceled',
                'specific' => 'ets_sponsor'
            );
            foreach ($customers as $customer) {
                foreach ($adminEmail as $to) {
                    self::sendMail(0, 'reward_canceled_admin', $subjects, $customer, array('employee' => trim($to)));
                }
            }
        }
    }
    public static function getRewards($id_order = null, $id_reward = null)
    {
        if ($id_order) {
            $sql = "SELECT reward.*, customer.email as email, customer.firstname, customer.lastname,customer.id_lang
                        FROM `" . _DB_PREFIX_ . "ets_am_reward` reward
                        LEFT JOIN `" . _DB_PREFIX_ . "customer` customer ON reward.id_customer = customer.id_customer
                        WHERE reward.program = 'ref' AND reward.id_order = " . (int)$id_order . ($id_reward ? ' AND reward.id_ets_am_reward >="' . (int)$id_reward . '"' : '');
            return Db::getInstance()->executeS($sql);
        } else if ($id_reward) {
            $sql = "SELECT reward.*, customer.email as email, customer.firstname, customer.lastname,customer.id_lang
                        FROM `" . _DB_PREFIX_ . "ets_am_reward` reward
                        LEFT JOIN `" . _DB_PREFIX_ . "customer` customer ON reward.id_customer = customer.id_customer
                        WHERE reward.program = 'ref' AND reward.id_ets_am_reward = " . (int)$id_reward;
            return Db::getInstance()->executeS($sql);
        } else {
            return false;
        }
    }
    public static function sendMail($id_lang,$template,$subjects,$reward,$email)
    {
        if($reward)
        {
            $trans = Module::getInstanceByName('ets_affiliatemarketing')->getTranslates();
            $status = '';
            if ($reward['status'] == 0) {
                $status = 'En attente';
            } elseif ($reward['status'] == 1) {
                $status = 'Approuvé';
            }
            if ($reward['status'] == -1) {
                $status = 'Annulé';
            }
            if ($reward['status'] == -2) {
                $status = 'Expiré';
            }
            $data = array(
                '{reward_id}' => $reward['id_ets_am_reward'],
                '{reward}' => $reward['id_ets_am_reward'],
                '{customer}' => $reward['firstname'] . ' ' . $reward['lastname'],
                '{program}' => $trans['referral_program'],
                '{date_created}' => $reward['datetime_added'],
                '{amount}' => Ets_affiliatemarketing::displayPrice($reward['amount'], (int)Configuration::get('PS_CURRENCY_DEFAULT')),
                '{status}' => $status,
                '{note}' => $reward['note'],
                '{date_validated}' => $reward['datetime_validated'],
                '{date_canceled}' => date('Y-m-d H:i:s'),

            );
            Ets_aff_email::send($id_lang, $template, $subjects, $data, $email);
        }

    }
    /**
     * @param null $id_order
     * @param null $get_last_reward
     * @return bool
     * @throws PrestaShopDatabaseException
     */
    public static function sendMailRewardCreated($id_order = null, $id_reward = null)
    {
        $mail_to_admin = (int)Configuration::get('ETS_AM_ENABLED_EMAIL_ADMIN_RVOC');
        $mail_to_sponsor = (int)Configuration::get('ETS_AM_ENABLED_EMAIL_CUSTOMER_RVOC');
        $module = Module::getInstanceByName('ets_affiliatemarketing');
        if (($mail_to_sponsor || $mail_to_admin) && ($rewards = self::getRewards($id_order,$id_reward))) {
            if ($mail_to_admin && ($mails = Configuration::get('ETS_AM_EMAILS_CONFIRM')) && ($mails = explode(',',$mails))) {
                $subjects = array(
                    'translation' => $module->l('A new reward was created', 'ets_sponsor'),
                    'origin' => 'A new reward was created',
                    'specific' => 'ets_sponsor'
                );
                foreach ($mails as $mail) {
                    if(Validate::isEmail($mail))
                    {
                        foreach ($rewards as $reward) {
                            self::sendMail(0,'new_reward_referral_admin',$subjects,$reward,array('employee' => $mail));
                        }
                    }
                }
            }
            if ($mail_to_sponsor) {
                $subjects = array(
                    'translation' => $module->l('A new reward created for you', 'ets_sponsor'),
                    'origin' => 'A new reward created for you',
                    'specific' => 'ets_sponsor'
                );
                foreach ($rewards as $reward) {
                    $mail = trim($reward['email']);
                    if($mail && Validate::isEmail($mail))
                    {
                        self::sendMail(0,'reward_created_customer',$subjects,$reward,array('customer' => $mail));
                    }
                }
            }
        }
    }
    /**
     * @return bool
     */
    public static function allowGetVoucher()
    {
        if ((int)Configuration::get('ETS_AM_REF_ENABLED')) {
            $context = Context::getContext();
            $customer = $context->customer;
            $cookie_ref = $context->cookie->__get(EAM_REFS);
            if ($cookie_ref == $customer->id)
                return false;
            $cookie_voucher = $context->cookie->__get('ets_am_show_voucher_ref');
            if ((int)$cookie_voucher != (int)$customer->id) {
                $cookie_voucher = null;
            }
            if ($customer->id && ($cookie_ref || $cookie_voucher)) {
                $sql = "SELECT * FROM `" . _DB_PREFIX_ . "ets_am_voucher` WHERE id_customer = " . (int)$customer->id . " AND (id_product = 0 OR id_product = '' OR id_product IS NULL)";
                $voucher = Db::getInstance()->getRow($sql);
                if (!$voucher) {
                    if ($cookie_ref && !self::isActive($cookie_ref)) {
                        return false;
                    }
                    if ($cookie_voucher) {
                        $context->cookie->__set('ets_am_show_voucher_ref', null);
                    }
                    return true;
                }
            }
        }
        return false;
    }
    public static function joinedReferralProgram($id_customer)
    {
        $context = Context::getContext();
        $id_customer = (int)$id_customer;
        $id_shop = (int)$context->shop->id;
        if (!$id_customer) {
            return false;
        }
        if (!self::isRefferalProgramReady()) {
            return false;
        }
        if (!self::isActive($id_customer)) {
            return false;
        }
        if (!(int)Configuration::get('ETS_AM_REF_REGISTER_REQUIRED')) {
            return self::canUseRefferalProgram($id_customer);
        }
        $sql = "SELECT program FROM `" . _DB_PREFIX_ . "ets_am_participation`
                 WHERE `id_customer` = " . (int)$id_customer . " 
                 AND `id_shop` = " . (int)$id_shop . " 
                 AND `program` = 'ref'
                 AND `status` = 1";
        $exists = Db::getInstance()->getvalue($sql);
        if (!$exists) {
            $sql = "SELECT * FROM `" . _DB_PREFIX_ . "ets_am_user`
                 WHERE `id_customer` = " . (int)$id_customer . " 
                 AND `id_shop` = " . (int)$id_shop . " 
                 AND `ref` = 1
                 AND `status` = 1";
            $exists = Db::getInstance()->getvalue($sql);
        }
        if ($exists) {
            $user = Db::getInstance()->getRow("SELECT * FROM `" . _DB_PREFIX_ . "ets_am_user` user WHERE id_customer =  " . (int)$id_customer . " AND id_shop = " . (int)$context->shop->id);
            if ($user && $user['status'] == 1) {
                return true;
            }
        }
        return false;
    }
    public static function getDetailSponsors($id_customer = null, $params = array(), $frontend = false)
    {
        $id_customer = (int)$id_customer;
        if (!$id_customer) {
            return array();
        }
        $orderby = isset($params['orderby']) && ($orderby = Tools::strtolower((string)$params['orderby'])) && in_array($orderby, array('id', 'firstname', 'email', 'order', 'reward', 'friend', 'date_add')) ? $orderby : "id";
        $orderway = isset($params['orderway']) && ($orderway = Tools::strtolower((string)$params['orderway'])) && in_array($orderway, array('asc', 'desc')) ? $orderway : "desc";
        $type_date_filter = isset($params['customer_sale_filter']) && ($type_date_filter = Tools::strtolower((string)$params['customer_sale_filter'])) && in_array($type_date_filter, array('this_month', 'this_year')) ? $type_date_filter : false;
        $page = isset($params['page']) && ($page = (int)$params['page']) && $page > 0 ? $page : 1;
        $limit = isset($params['limit']) && ($limit = (int)$params['limit']) && $limit > 0 ? $limit : 20;
        $offset = ($page - 1) * $limit;
        $context = Context::getContext();
        $where = '';
        if ($type_date_filter == 'this_month') {
            $where .= " AND customer.date_add >= '" . pSQL(date('Y-m-01 00:00:00')) . "' AND customer.date_add <= '" . pSQL(date('Y-m-t 23:59:59')) . "'";
        } elseif ($type_date_filter == 'this_year') {
            $where .= " AND customer.date_add >= '" . pSQL(date('Y-01-01 00:00:00')) . "' AND customer.date_add <= '" . pSQL(date('Y-12-31 23:59:59')) . "'";
        }
        $sql_total = "SELECT COUNT(DISTINCT am.id_customer) as total
                    FROM `" . _DB_PREFIX_ . "ets_am_sponsor` am
                    INNER JOIN `" . _DB_PREFIX_ . "customer` customer ON (am.id_customer=customer.id_customer)
                    WHERE am.id_shop = " . (int)$context->shop->id . " AND am.id_parent = " . (int)$id_customer . (string)$where;
        $total_result = (int)Db::getInstance()->getValue($sql_total);
        if ($total_result) {
            $total_page = ceil($total_result / $limit);
            switch ($orderby) {
                case 'id':
                    $orderby = 'id_ets_am_sponsor';
                    break;
                case 'firstname':
                    $orderby = 'customer.firstname';
                    break;
                case 'email':
                    $orderby = 'customer.email';
                    break;
                case 'order':
                    $orderby = 'total_order';
                    break;
                case 'reward':
                    $orderby = 'total_point';
                    break;
                case 'friend':
                    $orderby = 'total_friend';
                    break;
                case 'date_add':
                    $orderby = 'customer.date_add';
                    break;
            }
            $sql = "SELECT reward.id_ets_am_reward, ord.id_currency,sponsor.*, sponsor.id_customer as id_customer, customer.firstname as firstname, customer.lastname as lastname, customer.email as email , customer.date_add as date_add, 
                            (SELECT SUM(o2.total_paid_tax_incl) FROM `" . _DB_PREFIX_ . "orders` o2 WHERE id_order IN (SELECT id_order FROM `" . _DB_PREFIX_ . "ets_am_reward` er where er.id_friend = sponsor.id_customer AND er.id_customer='" . (int)$id_customer . "')) total_order, SUM(IF(reward.id_customer = " . (int)$id_customer . " AND reward.status = 1,reward.amount, 0)) as total_point, (SELECT COUNT(id_customer) FROM `" . _DB_PREFIX_ . "ets_am_sponsor` sponsor1 WHERE sponsor1.id_parent = sponsor.id_customer) AS `total_friend`
                    FROM `" . _DB_PREFIX_ . "ets_am_sponsor` sponsor
                    LEFT JOIN `" . _DB_PREFIX_ . "customer` customer ON sponsor.id_customer = customer.id_customer
                    LEFT JOIN `" . _DB_PREFIX_ . "ets_am_reward` reward ON sponsor.id_customer = reward.id_friend AND reward.program='ref'
                    LEFT JOIN `" . _DB_PREFIX_ . "orders` ord ON (ord.id_order = reward.id_order OR IFNULL(ord.id_order, 0) = reward.id_order)
                    LEFT JOIN `" . _DB_PREFIX_ . "currency` currency ON (ord.id_currency = currency.id_currency)
                    WHERE sponsor.id_shop = " . (int)$context->shop->id . " AND sponsor.id_parent = " . (int)$id_customer . " " . (string)$where . "
                    GROUP BY sponsor.id_customer
                    ORDER BY " . pSQL($orderby) . " " . pSQL($orderway) . "
                    LIMIT " . (int)$offset . ", " . (int)$limit;
            $results = Db::getInstance()->executeS($sql);
            foreach ($results as &$result) {
                if ($frontend) {
                    $result['total_point'] = ETS_AM::displayReward((float)$result['total_point'], true);
                } else {
                    $result['total_point'] = Ets_AM::displayRewardAdmin((float)$result['total_point']);
                }
                if ($result['id_currency'])
                    $currency = new Currency($result['id_currency']);
                else
                    $currency = $context->currency;
                $result['total_order'] = Ets_affiliatemarketing::displayPrice($result['total_order'] ? $result['total_order'] : 0, $currency);
                $result['link_view'] = Ets_AM::getBaseUrlDefault('myfriend', array('id_customer' => $result['id_customer']));
            }
            if (!$results) {
                $results = array(
                    array(
                        'total_point' => ETS_AM::displayReward(0, true),
                        'total_order' => ETS_AM::displayReward(0, true),
                        'link_view' => '',
                    )
                );
            }
            if (isset($result)) {
                unset($result);
            }
            return array(
                'total_result' => $total_result,
                'total_page' => $total_page,
                'result' => $results,
                'current_page' => $page,
                'per_page' => $limit
            );
        }
        return array(
            'total_result' => 0,
            'total_page' => 1,
            'result' => array(),
            'current_page' => 1,
            'per_page' => $limit
        );
    }
    public static function checkSponsorCode($code)
    {
        $sql = "SELECT id_customer FROM `" . _DB_PREFIX_ . "customer` WHERE " . (Validate::isEmail($code) ? "email = '" . pSQL($code) . "'" : "id_customer=" . (int)$code);
        $customer = Db::getInstance()->getRow($sql);
        if ($customer['id_customer'] != Context::getContext()->customer->id) {
            if (self::joinedReferralProgram((int)$customer['id_customer'])) {
                return (int)$customer['id_customer'];
            }
        }
        return false;
    }
    public static function isJoinedRef($id_customer)
    {
        $context = Context::getContext();
        if (!$id_customer) {
            $id_customer = $context->customer->id;
        }
        if (!$id_customer) {
            return false;
        }
        if (!Ets_Sponsor::isRefferalProgramReady()) {
            return false;
        }
        if (!(int)Configuration::get('ETS_AM_REF_REGISTER_REQUIRED')) {
            return Ets_Sponsor::canUseRefferalProgram($id_customer);
        } else {
            $user = Ets_User::getUserByCustomerId($id_customer);
            if ($user && $user['status'] > 0 && $user['ref'] == 1) {
                return true;
            }
        }
        return false;
    }
    public static function getFriendsOfSponsor($id_sponsor, $limit, $id_sort = 'ASC')
    {
        if(!in_array(Tools::strtoupper($id_sort),array('ASC','DESC')))
            $id_sort = 'ASC';
        return Db::getInstance()->executeS("SELECT * FROM `" . _DB_PREFIX_ . "ets_am_sponsor` WHERE id_parent = " . (int)$id_sponsor . " AND `level` = 1 ORDER BY id_ets_am_sponsor ".pSQL($id_sort)."  LIMIT " . (int)$limit);
    }
    public static function isActive($id_customer = null)
    {
        $program_ready = (int)Configuration::get('ETS_AM_REF_ENABLED');
        $enable_register = (int)Configuration::get('ETS_AM_REF_REGISTER_REQUIRED');
        $context = Context::getContext();
        if (!$id_customer) {
            $id_customer = $context->customer->id;
        }
        if ($program_ready && self::canUseRefferalProgram($id_customer)) {
            $user = Ets_User::getUserByCustomerId($id_customer);
            if ($enable_register) {
                if ($user && $user['status'] == 1 && $user['ref'] == 1) {
                    return true;
                }
            } else {
                if (!$user) {
                    return true;
                } else {
                    if ((int)$user['status'] == 1 && ((int)$user['ref'] == 1 || (int)$user['ref'] == 0)) {
                        return true;
                    }
                }
            }
        }
        return false;
    }
    public static function getIdRefByCart($id_cart, $id_customer)
    {
        return (int)Db::getInstance()->getValue('SELECT acrs.id_customer FROM `' . _DB_PREFIX_ . 'ets_am_cart_rule_seller` acrs
                INNER JOIN `' . _DB_PREFIX_ . 'cart_rule` cr ON (cr.id_cart_rule = acrs.id_cart_rule)
                INNER JOIN `' . _DB_PREFIX_ . 'cart_cart_rule` ccr ON (ccr.id_cart_rule = cr.id_cart_rule)
                WHERE ccr.id_cart = "' . (int)$id_cart . '" AND acrs.id_customer!="' . (int)$id_customer . '"');
    }
    public static function getIdParentByIdCustomer($id_customer)
    {
        return (int)Db::getInstance()->getValue('SELECT id_parent FROM `'._DB_PREFIX_.'ets_am_sponsor` WHERE id_customer='.(int)$id_customer.' AND level=1');
    }
    public static function searchFriends($query,$id_customer)
    {
        $customers= Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'customer` WHERE id_customer!="'.(int)$id_customer.'" AND id_shop = "'.(int)Context::getContext()->shop->id.'" AND (id_customer="'.(int)$query.'" OR email like "%'.pSQL($query).'%" OR firstname like "%'.pSQL($query).'%" OR lastname like "%'.pSQL($query).'%" OR CONCAT(firstname," ",lastname) LIKE "%'.pSQL($query).'%")');
        if($customers)
        {
            foreach($customers as &$item)
            {
                $sponsor = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'ets_am_sponsor` WHERE id_customer="'.(int)$item['id_customer'].'" AND level=1');
                if($sponsor)
                {
                    if($sponsor['id_parent'] == $id_customer)
                        $item['friend'] =1;
                    else
                        $item['friend'] = 2;
                }
                elseif(Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'ets_am_sponsor` WHERE id_parent="'.(int)$item['id_customer'].'"'))
                    $item['friend']=3;
                else
                    $item['friend'] =0;

            }
        }
        return $customers;
    }
    public static function addFriend($id_sponsor,$id_customer)
    {
        $id_shop = Context::getContext()->shop->id;
        if (!Db::getInstance()->getRow('SELECT * FROM `' . _DB_PREFIX_ . 'ets_am_sponsor` WHERE id_customer="' . (int)$id_customer . '"'))
        {
            $eam_sponsor = new Ets_Sponsor();
            $eam_sponsor->id_customer = $id_customer;
            $eam_sponsor->id_parent = $id_sponsor;
            $eam_sponsor->level = 1;
            $eam_sponsor->id_shop = (int)$id_shop;
            $eam_sponsor->datetime_added = date('Y-m-d H:i:s');
            if ($eam_sponsor->add()) {
                $sqlGetParent = "SELECT * FROM `" . _DB_PREFIX_ . "ets_am_sponsor` WHERE id_customer = " . (int)$id_sponsor . " AND id_shop = " . (int)$id_shop;
                $parents = Db::getInstance()->executeS($sqlGetParent);
                if (count($parents)) {
                    $values_insert = '';
                    $datetime_added = date('Y-m-d H:i:s');
                    foreach ($parents as $parent) {
                        $values_insert .= "(".(int)$id_customer.", " . (int)$parent['id_parent'] . ", " . ((int)$parent['level'] + 1) . ", " . (int)$id_shop . ", '".pSQL($datetime_added)."'),";

                    }
                    $sql_insert = "INSERT INTO `" . _DB_PREFIX_ . "ets_am_sponsor` (`id_customer`, `id_parent`, `level`, `id_shop`, `datetime_added`) VALUES " . trim($values_insert, ',');
                    Db::getInstance()->execute($sql_insert);
                }
                $sponsor = Db::getInstance()->getRow('SELECT * FROM `' . _DB_PREFIX_ . 'customer` c 
                INNER JOIN `' . _DB_PREFIX_ . 'ets_am_sponsor` eas ON (c.id_customer = eas.id_customer)
                WHERE c.id_customer="' . (int)$id_customer . '"');
                $sponsor['link'] = Context::getContext()->link->getAdminLink('AdminCustomers') . '&viewcustomer&id_customer=' . $id_customer;
                $sponsor['reward'] = Ets_affiliatemarketing::displayPrice(0);
                $sponsor['total_order'] = Db::getInstance()->getValue('SELECT SUM(o2.total_paid_tax_incl) FROM `' . _DB_PREFIX_ . 'orders` o2 WHERE id_order IN (SELECT id_order FROM `' . _DB_PREFIX_ . 'ets_am_reward` er WHERE er.id_friend=' . (int)$id_customer . ')');
                $sponsor['total_order'] = $sponsor['total_order'] ? Ets_AM::displayRewardAdmin($sponsor['total_order']) : Ets_AM::displayRewardAdmin(0);
                return $sponsor;
            } else {
                return false;
            }
        }
        return false;
    }
    public static function getCustomerSponsorInfor($id_customer)
    {
        if(!Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'ets_am_sponsor` WHERE id_customer="'.(int)$id_customer.'" AND id_parent='.(int)Context::getContext()->customer->id))
            return false;
        $customer_info = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'customer` WHERE id_customer='.(int)$id_customer);
        $customer_info['orders'] = Db::getInstance()->getValue('SELECT COUNT(DISTINCT o.id_order) FROM `'._DB_PREFIX_.'orders` o,'._DB_PREFIX_.'ets_am_reward r WHERE o.id_order=r.id_order AND o.id_customer='.(int)$id_customer.' AND program="ref" AND sub_program!="REG"');
        $customer_info['level'] = Db::getInstance()->getValue('SELECT level FROM `'._DB_PREFIX_.'ets_am_sponsor` WHERE id_customer='.(int)$id_customer.' AND id_parent='.(int)Context::getContext()->customer->id);
        $customer_info['approved'] = Db::getInstance()->getValue('SELECT COUNT(DISTINCT o.id_order) FROM `'._DB_PREFIX_.'orders` o,'._DB_PREFIX_.'ets_am_reward r WHERE o.id_order=r.id_order AND o.id_customer='.(int)$id_customer.' AND r.status=1');
        $customer_info['friends'] = Db::getInstance()->getValue('SELECT COUNT(DISTINCT id_customer) FROM `'._DB_PREFIX_.'ets_am_sponsor` WHERE id_parent='.(int)$id_customer);
        $sql = 'SELECT *,o.id_currency as currency FROM `'._DB_PREFIX_.'orders` o,'._DB_PREFIX_.'ets_am_reward r WHERE o.id_order=r.id_order AND o.id_customer='.(int)$id_customer.' AND r.id_friend='.(int)$id_customer.' AND r.id_customer='.(int)Context::getContext()->customer->id.' AND program="ref" AND sub_program!="REG"';
        if(($order_sale_status = Tools::getValue('order_sale_status')) || ($order_sale_status!=='' && $order_sale_status!== false && $order_sale_status!== null))
            $sql .= ' AND r.status='.(int)$order_sale_status;
        if($type_date_filter = Tools::getValue('order_sale_filter'))
        {
            if ($type_date_filter == 'this_month') {
                $sql .= " AND r.datetime_added >= '" . date('Y-m-01 00:00:00') . "' AND r.datetime_added <= '" . date('Y-m-t 23:59:59') . "'";
            } else if ($type_date_filter == 'this_year') {
                $sql .= " AND r.datetime_added >= '" . date('Y-01-01 00:00:00') . "' AND r.datetime_added <= '" . date('Y-12-31 23:59:59') . "'";
            }
        }
        $sql .=' ORDER BY o.id_order DESC';
        if($customer_info['level']==1)
        {
            $customer_info['price_register'] = (float)Db::getInstance()->getValue('SELECT amount FROM `'._DB_PREFIX_.'ets_am_reward` WHERE id_friend="'.(int)$id_customer.'" AND id_customer="'.(int)Context::getContext()->customer->id.'" AND sub_program="REG" AND status=1');
            if($customer_info['price_register'])
                $customer_info['price_register'] = Ets_AM::displayReward($customer_info['price_register'],true);
        }

        $customer_info['list_orders'] = Db::getInstance()->executeS($sql);
        if($customer_info['list_orders'])
        {
            foreach($customer_info['list_orders'] as &$order)
            {
                $order['total_paid_tax_incl'] = Ets_affiliatemarketing::displayPrice($order['total_paid_tax_incl'],(int)$order['currency']);
                $order['amount'] = Ets_AM::displayReward($order['amount']);
            }
        }
        return $customer_info;
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
        $aff->_clearCache('*',$aff->_getCacheId('dashboard',false));
        return true;
    }
}
