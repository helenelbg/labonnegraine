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
class Ets_AM extends ObjectModel
{
    /**
     * @var float
     */
    public $amount;
    /**
     * @var string
     */
    public $program;
    /**
     * @var string
     */
    public $sub_program;
    /**
     * @var bool
     */
    public $status;
    /**
     * @var datetime
     */
    public $datetime_added;
    /**
     * @var datetime
     */
    public $datetime_validated;
    /**
     * @var datetime
     */
    public $datetime_canceled;
    /**
     * @var string
     */
    public $note;
    /**
     * @var int
     */
    public $id_customer;
    /**
     * @var int
     */
    public $id_order;
    /**
     * @var int
     */
    public $id_shop;
    /**
     * @var int
     */
    public $id_currency;
    /**
     * @var datetime;
     */
    public $expired_date;
    /**
     * @var int
     */
    public $await_validate;
    /**
     * @var datetime
     */
    public $send_expired_email;
    /**
     * @var datetime
     */
    public $send_going_expired_email;
    /**
     * @var datetime
     */
    public $last_modified;
    /**
     * @var int
     */
    public $deleted;
    /**
     * @var int
     */
    public $id_friend;
    /**
     * @var int
     */
    public $used;
    public static $instance = null;
    const TYPE_REF = 'REF';
    const TYPE_AFF = 'AFF';
    const TYPE_LOY = 'LOY';
    const TYPE_REG = 'REG';
    /**
     * @var array
     */
    public static $definition = array(
        'table' => 'ets_am_reward',
        'primary' => 'id_ets_am_reward',
        'multilang_shop' => true,
        'fields' => array(
            'amount' => array(
                'type' => self::TYPE_FLOAT,
                'validate' => 'isFloat'
            ),
            'program' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isString'
            ),
            'sub_program' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isString'
            ),
            'status' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isInt'
            ),
            'datetime_added' => array(
                'type' => self::TYPE_DATE,
                'allow_null' => true
            ),
            'datetime_validated' => array(
                'type' => self::TYPE_DATE,
                'allow_null' => true
            ),
            'expired_date' => array(
                'type' => self::TYPE_DATE,
                'allow_null' => true
            ),
            'datetime_canceled' => array(
                'type' => self::TYPE_DATE,
                'allow_null' => true
            ),
            'note' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isString'
            ),
            'id_customer' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt'
            ),
            'id_friend' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt'
            ),
            'id_shop' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt'
            ),
            'id_currency' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt'
            ),
            'id_order' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt'
            ),
            'await_validate' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt'
            ),
            'send_expired_email' => array(
                'type' => self::TYPE_DATE,
            ),
            'send_going_expired_email' => array(
                'type' => self::TYPE_DATE,
            ),
            'last_modified' => array(
                'type' => self::TYPE_DATE,
            ),
            'deleted' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt'
            ),
            'used' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt'
            )
        )
    );
    /**
     * Ets_AM constructor.
     * @param null $id_item
     * @param null $id_lang
     * @param null $id_shop
     * @param Context|null $context
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function __construct($id_item = null, $id_lang = null, $id_shop = null)
    {
        parent::__construct($id_item, $id_lang, $id_shop);
    }
    public function update($null_values = false)
    {
        if ($this->status && $this->program == EAM_AM_LOYALTY_REWARD && !$this->expired_date && ($day = Configuration::get('ETS_AM_LOYALTY_MAX_DAY'))) {
            $this->expired_date = date('Y-m-d H:i:s', strtotime($day . ' days'));
        }
        self::_clearCache();
        return parent::update($null_values);
    }
    public function l($string)
    {
        return Translate::getModuleTranslation(_ETS_AM_MODULE_, $string, pathinfo(__FILE__, PATHINFO_FILENAME));
    }
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new Ets_AM();
        }
        return self::$instance;
    }
    /**
     * @param $customer_id
     * @return float
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public static function calculateCustomerSpent()
    {
        $context = Context::getContext();
        $customer = $context->customer;
        $sql = 'SELECT SUM(`total_paid` / o.`conversion_rate`) AS total_orders
                FROM `' . _DB_PREFIX_ . 'orders` o
                WHERE o.`id_customer` = ' . (int)$customer->id . '
                AND o.valid = 1';
        $results = Db::getInstance()->executeS($sql);
        if (count($results)) {
            return $results[0]['total_orders'];
        }
        return 0;
    }
    /**
     * @return bool
     */
    public static function isInLoyaltyTime()
    {
        $config = Configuration::get('ETS_AM_LOYALTY_TIME');
        if ($config) {
            if ($config == 'ALL') {
                return true;
            }
            $from = Configuration::get('ETS_AM_LOYALTY_TIME_FROM');
            $to = Configuration::get('ETS_AM_LOYALTY_TIME_TO');
            if ($from) {
                $begin = date('Y-m-d H:i:s', strtotime($from . ' 00:00:00'));
                if ($to) {
                    $end = date('Y-m-d H:i:s', strtotime($to . ' 23:59:59'));
                    return date('Y-m-d H:i:s') >= $begin && date('Y-m-d H:i:s') <= $end;
                }
                return date('Y-m-d H:i:s') >= $begin;
            } else {
                if ($to) {
                    $end = date('Y-m-d H:i:s', strtotime($to . ' 23:59:59'));
                    return date('Y-m-d H:i:s') <= $end;
                }
            }
        }
        return true;
    }
    /**
     * @param $product
     * @return bool
     * @throws PrestaShopException
     */
    public static function validateLoyaltyProduct($product)
    {
        if (!isset($product['id_product']))
            return false;
        $configs = Configuration::getMultiple(array(
            'ETS_AM_LOY_CAT_TYPE',
            'ETS_AM_LOYALTY_CATEGORIES',
            'ETS_AM_LOYALTY_SPECIFIC',
            'ETS_AM_LOYALTY_EXCLUDED',
            'ETS_AM_LOYALTY_NOT_FOR_DISCOUNTED',
        ));
        $valid = false;
        if ($configs['ETS_AM_LOY_CAT_TYPE']) {
            if ($configs['ETS_AM_LOY_CAT_TYPE'] == 'ALL') {
                $valid = true;
            } else {
                if ($configs['ETS_AM_LOYALTY_CATEGORIES'] != '') {
                    $configCategories = $configs['ETS_AM_LOYALTY_CATEGORIES'];
                    $configCategories = array_map('intval', explode(',', $configCategories));
                    if ((bool)Configuration::get('ETS_AM_LOYALTY_INCLUDE_SUB')) {
                        $sql = "SELECT parent.id_category
                        FROM `" . _DB_PREFIX_ . "category` AS node LEFT JOIN `" . _DB_PREFIX_ . "category_product` cp ON cp.id_category = node.id_category AND cp.id_product = " . (int)$product['id_product'] . ",
                             `" . _DB_PREFIX_ . "category` AS parent
                        WHERE node.nleft BETWEEN parent.nleft AND parent.nright AND cp.id_category is NOT NULL AND parent.id_parent != 0
                        GROUP BY parent.id_category
                        ORDER BY parent.nleft";
                        $results = Db::getInstance()->executeS($sql);
                        $parents = array();
                        if (count($results)) {
                            foreach ($results as $result) {
                                $parents[] = $result['id_category'];
                            }
                        }
                        $valid = count(array_intersect($parents, $configCategories)) ? true : false;
                    } else {
                        $productCat = Product::getProductCategories((int)$product['id_product']);
                        $valid = count(array_intersect($productCat, $configCategories)) ? true : false;
                    }
                } else {
                    $valid = false;
                }
            }
        }
        if (!$valid) {
            if ($configs['ETS_AM_LOYALTY_SPECIFIC'] && $configs['ETS_AM_LOYALTY_SPECIFIC'] != '') {
                $specifics = explode(',', $configs['ETS_AM_LOYALTY_SPECIFIC']);
                $valid = in_array($product['id_product'], $specifics);
            }
        }
        if ($valid) {
            if ($configs['ETS_AM_LOYALTY_EXCLUDED'] && $configs['ETS_AM_LOYALTY_EXCLUDED'] != '') {
                $excludes = explode(',', $configs['ETS_AM_LOYALTY_EXCLUDED']);
                $valid = !in_array($product['id_product'], $excludes);
            }
        }
        if ((isset($product['reduction']) && $product['reduction'] > 0) || (isset($product['has_discount']) && $product['has_discount'])) {
            if ($configs['ETS_AM_LOYALTY_NOT_FOR_DISCOUNTED']) {
                $valid = false;
            }
        }
        return $valid;
    }
    /**
     * @param $orderState
     * @return array
     * @throws PrestaShopException
     */
    public static function mapOrderStateToRewardState($orderState)
    {
        if (!Validate::isLoadedObject($orderState))
            return 0;
        $validated = array_map('intval', explode(',', Configuration::get('ETS_AM_VALIDATED_STATUS')));
        if ($validated && in_array($orderState->id, $validated))
        {
            return array(
                'status'=>1,
            );
        }
        return array(
            'status'=>0,
        );
    }
    /**
     * @param $currency_reward
     * @param bool $to_point
     * @param bool $for_display
     * @return float|int|string
     */
    public static function displayReward($currency_reward, $for_display = true)
    {
        $displayType = Configuration::get('ETS_AM_REWARD_DISPLAY');
        $to_point = $displayType && $displayType == 'point' ? true : false;
        $context = Context::getContext();
        if (!$to_point) {
            if (Ets_AM::needExchange($context)) {
                $currency_reward = Tools::convertPrice($currency_reward, $context->currency->id, true);
                if ($for_display) {
                    return Ets_affiliatemarketing::displayPrice($currency_reward);
                }
                return $currency_reward;
            }
            if ($for_display) {
                return Ets_affiliatemarketing::displayPrice($currency_reward);
            }
            return $currency_reward;
        }
        $rate = (float)Configuration::get('ETS_AM_CONVERSION');
        $point = Tools::ps_round($rate * Tools::ps_round($currency_reward, _PS_PRICE_COMPUTE_PRECISION_ ?: 2), (_PS_PRICE_COMPUTE_PRECISION_ ?: 2));
        if ($for_display) {
            $unit = Configuration::get('ETS_AM_REWARD_UNIT_LABEL', $context->language->id);
            return $point . ' ' . $unit;
        }
        return $point;
    }
    /**
     * @param $product_id
     * @param $configCates
     * @return bool
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public static function validateProductCat($product_id, $configCates)
    {
        $sql = "SELECT c.id_category FROM `" . _DB_PREFIX_ . "category` c
        INNER JOIN `" . _DB_PREFIX_ . "category_product` cp ON (cp.id_category = c.id_category) 
        WHERE cp.id_product = " . (int)$product_id;
        $categories = Db::getInstance()->executeS($sql);
        if ($categories && count($categories)) {
            $return = false;
            foreach ($categories as $item) {
                if (in_array($item['id_category'], $configCates)) {
                    $return = true;
                    break;
                }
            }
            return $return;
        }
        return false;
    }
    public static function isCustomerBelongToValidGroup($customer, $groupConfigKey)
    {
        $groupConfig = Configuration::get($groupConfigKey);
        if (!$groupConfig) {
            return false;
        }
        if ($groupConfig == 'ALL') {
            return true;
        }
        $configs = array_map('intval', explode(',', $groupConfig));
        $groups = $customer->getGroups();
        if ($groups) {
            foreach ($groups as $group)
                if (in_array($group, $configs))
                    return true;
        }
        return false;
    }
    public static function enableSendEmailRegister()
    {
        if ((int)Configuration::get('ETS_AM_ENABLED_EMAIL_CONFIRM_REG')) {
            return true;
        }
        return false;
    }
    /**
     * @param $reward
     * @param null $newOrderStatus
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public static function changeRewardStatus($reward, $newOrderStatus)
    {
        if ($newOrderStatus && Validate::isLoadedObject($newOrderStatus) && isset($reward['id_ets_am_reward'])) {
            $statusChanged = 0;
            $configStatus = self::getConfigOrderStatus();
            $orderState = $newOrderStatus->id;
            foreach ($configStatus as $key => $status) {
                $date = date('Y-m-d H:i:s');
                $r = new Ets_AM($reward['id_ets_am_reward']);
                if (in_array($orderState, $status)) {
                    if ($key == 'pending') {
                        $s = 0;
                        $r->status = $s;
                        if ($r->save()) {
                            self::updateRewardProductStatus((int)$reward['id_ets_am_reward'], $s);
                        }
                    } elseif ($key == 'validated') {
                        $s = 1;
                        if ($r->status != $s) {
                            $statusChanged = 1;
                            if ($rangeValidate = Configuration::get('ETS_AM_VALIDATED_DAYS')) {
                                $dateAdd = date('Y-m-d H:i:s');
                                $validate = strtotime($dateAdd . ' ' . $rangeValidate . ' days');
                                if ($validate > strtotime(date('Y-m-d H:i:s'))) {
                                    $r->await_validate = (int)$rangeValidate;
                                    $r->datetime_validated = date('Y-m-d H:i:s', $validate);
                                    $r->save();
                                    return;
                                }
                            }
                        }
                        $r->status = $s;
                        $r->datetime_validated = $date;
                        $r->datetime_canceled = null;
                        if ($reward['program'] == EAM_AM_LOYALTY_REWARD) {
                            if (($availability = Configuration::get('ETS_AM_LOYALTY_MAX_DAY')) && !$r->used) {
                                $availabilityTime = $date . ' + ' . $availability . ' days';
                                $r->expired_date = date('Y-m-d H:i:s', strtotime($availabilityTime));
                            }
                        }
                        if ($r->save()) {
                            self::updateRewardProductStatus((int)$reward['id_ets_am_reward'], $s);
                        }
                        $customer = new Customer($r->id_customer);
                        if ($reward['program'] == EAM_AM_LOYALTY_REWARD && $statusChanged) {
                            if (Configuration::get('ETS_AM_ENABLED_EMAIL_CUSTOMER_RVOC')) {
                                Ets_Loyalty::sendEmailToCustomerWhenRewardValidated($r);
                            }
                            if (Configuration::get('ETS_AM_ENABLED_EMAIL_ADMIN_RVOC')) {
                                Ets_Loyalty::sendEmailToAdminWhenRewardValidated($r);
                            }
                        }
                        if ($reward['program'] == EAM_AM_AFFILIATE_REWARD && $statusChanged) {
                            if ((int)Configuration::get('ETS_AM_ENABLED_EMAIL_CUSTOMER_RVOC')) {
                                Ets_Affiliate::senEmailWhenAffiliateRewardValidated($r);
                            }
                            if ((int)Configuration::get('ETS_AM_ENABLED_EMAIL_ADMIN_RVOC')) {
                                Ets_Affiliate::senEmailWhenAffiliateRewardValidated( $r, true);
                            }
                        }
                        if ($reward['program'] == EAM_AM_REF_REWARD && $statusChanged) {
                            if ((int)Configuration::get('ETS_AM_ENABLED_EMAIL_CUSTOMER_RVOC')) {
                                Ets_Sponsor::sendMailRewardValidated(null, $r->id);
                            }
                            if ((int)Configuration::get('ETS_AM_ENABLED_EMAIL_ADMIN_RVOC')) {
                                Ets_Sponsor::sendMailAdminRewardValidated(null, $r->id);
                            }
                        }
                    } elseif ($key == 'canceled') {
                        $s = -1;
                        if ($r->status != $s) {
                            $statusChanged = 1;
                        }
                        $r->status = $s;
                        $r->datetime_canceled = $date;
                        if ($r->save()) {
                            self::updateRewardProductStatus((int)$reward['id_ets_am_reward'], $s);
                        }
                        if ($reward['program'] == EAM_AM_LOYALTY_REWARD && $statusChanged) {
                            if (Configuration::get('ETS_AM_ENABLED_EMAIL_CUSTOMER_RVOC')) {
                                Ets_Loyalty::sendEmailToCustomerWhenRewardCanceled($r);
                            }
                            if (Configuration::get('ETS_AM_ENABLED_EMAIL_ADMIN_RVOC')) {
                                Ets_Loyalty::sendEmailToAdminWhenRewardCanceled($r);
                            }
                        }
                        if ($reward['program'] == EAM_AM_AFFILIATE_REWARD && $statusChanged) {
                            if ((int)Configuration::get('ETS_AM_ENABLED_EMAIL_CUSTOMER_RVOC')) {
                                Ets_Affiliate::sendEmailWhenAffiliateCanceled($r);
                            }
                            if ((int)Configuration::get('ETS_AM_ENABLED_EMAIL_ADMIN_RVOC')) {
                                Ets_Affiliate::sendEmailWhenAffiliateCanceled( $r, true);
                            }
                        }
                        if ($reward['program'] == EAM_AM_REF_REWARD && $statusChanged) {
                            if ((int)Configuration::get('ETS_AM_ENABLED_EMAIL_CUSTOMER_RVOC')) {
                                Ets_Sponsor::sendMailRewardCanceled(null, $r->id);
                            }
                            if ((int)Configuration::get('ETS_AM_ENABLED_EMAIL_ADMIN_RVOC')) {
                                Ets_Sponsor::sendMailAdminRewardCanceled(null, $r->id);
                            }
                        }
                    }
                }
            }
        }
    }
    /**
     * @param $id_reward
     * @param $status
     * @return bool
     */
    protected static function updateRewardProductStatus($id_reward, $status)
    {
        $sql = "UPDATE `" . _DB_PREFIX_ . "ets_am_reward_product` SET status = '" . pSQL($status) . "' WHERE id_ets_am_reward = " . (int)$id_reward;
        return Db::getInstance()->execute($sql);
    }
    /**
     * @return array
     */
    public static function getConfigOrderStatus()
    {
        $validated = $pending = $canceled = array();
        if ($validatedConfig = Configuration::get('ETS_AM_VALIDATED_STATUS')) {
            $validated = array_map('intval', explode(',', $validatedConfig));
        }
        if ($pendingConfig = Configuration::get('ETS_AM_WAITING_STATUS')) {
            $pending = array_map('intval', explode(',', $pendingConfig));
        }
        if ($canceledConfig = Configuration::get('ETS_AM_CANCELED_STATUS')) {
            $canceled = array_map('intval', explode(',', $canceledConfig));
        }
        return array(
            'pending' => $pending,
            'validated' => $validated,
            'canceled' => $canceled
        );
    }
    /**
     * @param $params
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public static function actionWhenOrderStatusChange($params)
    {
        if (!isset($params['id_order']) || !isset($params['newOrderStatus']))
            return false;
        $order_id = (int)$params['id_order'];
        $newOrderStatus = $params['newOrderStatus'];
        $sql = "SELECT * FROM `" . _DB_PREFIX_ . "ets_am_reward` WHERE `id_order` = " . (int)$order_id;
        $rewards = Db::getInstance()->executeS($sql);
        if (count($rewards)) {
            foreach ($rewards as $reward) {
                if ($reward['status'] != -1) {
                    self::changeRewardStatus($reward, $newOrderStatus);
                }
            }
        }
    }
    public static function getRewardHistory($program, $count = false)
    {
        if ($count) {
            return (int)Db::getInstance()->getValue("SELECT COUNT(*) as total FROM `" . _DB_PREFIX_ . "ets_am_reward` WHERE `program` = '" . pSQL($program) . "'");
        }
        $language = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $sql = "SELECT reward.*, order_state_lang.name as order_state, orders.reference as order_reference FROM (
                    SELECT * FROM `" . _DB_PREFIX_ . "ets_am_reward` WHERE `program` = '" . pSQL($program) . "'
                ) reward
                JOIN `" . _DB_PREFIX_ . "orders` orders ON reward.id_order = orders.id_order
                JOIN `" . _DB_PREFIX_ . "order_state` order_state ON orders.current_state = order_state.id_order_state
                JOIN `" . _DB_PREFIX_ . "order_state_lang` order_state_lang ON order_state.id_order_state = order_state_lang.id_order_state
                WHERE order_state_lang.id_lang = " . (int)$language->id;
        $results = Db::getInstance()->executeS($sql);
        return $results;
    }
    public static function getBaseUrl($skip_lang = false)
    {
        $context = Context::getContext();
        $language = Language::countActiveLanguages($context->shop->id);
        $uri = Tools::getHttpHost(true) . __PS_BASE_URI__;
        if ($language > 1 && !$skip_lang) {
            $friendly_url = (int)Configuration::get('PS_REWRITING_SETTINGS');
            if ($friendly_url) {
                $uri = $uri . Context::getContext()->language->iso_code . '/';
            } else {
                $uri = $uri . 'index.php?id_lang=' . Context::getContext()->language->id;
            }
        }
        return $uri;
    }
    public static function createPath($path)
    {
        if (is_dir($path)) {
            return true;
        }
        $prev_path = Tools::substr($path, 0, strrpos($path, '/', -2) + 1);
        $return = self::createPath($prev_path);
        return ($return && is_writable($prev_path)) ? mkdir($path, 0755, true) : false;
    }
    /**
     * @param null $prefix
     * @param null $context
     * @return string
     */
    public static function generatePromoCode($prefix = null, $context = null)
    {
        if (!$context) {
            $context = Context::getContext();
        }
        if ($prefix) {
            $code = $prefix . Tools::passwdGen(5);
            if (CartRule::getCartsRuleByCode($code, $context->language->id)) {
                $code = self::generatePromoCode($prefix, $context);
            }
        } else {
            $code = Tools::passwdGen(8);
            if (CartRule::getCartsRuleByCode($code, $context->language->id)) {
                $code = self::generatePromoCode(null, $context);
            }
        }
        return Tools::strtoupper($code);
    }
    public static function getDiscountVoucher($type = 'aff')
    {
        $context = Context::getContext();
        $program = 'ETS_AM_REF';
        if ($type == 'aff') {
            $program = 'ETS_AM_AFF';
        }
        $enable = (int)Configuration::get($program . '_OFFER_VOUCHER');
        if ($enable) {
            $id_lang_default = $context->language->id;
            $voucher_type = Configuration::get($program . '_VOUCHER_TYPE');
            if ($voucher_type == 'FIXED') {
                $voucher_code = Configuration::get($program . '_VOUCHER_CODE');
                $cartRule = CartRule::getCartsRuleByCode($voucher_code, $id_lang_default);
                if ($cartRule) {
                    if ((float)$cartRule[0]['reduction_percent'] > 0) {
                        return (float)$cartRule[0]['reduction_percent'] . '%';
                    } elseif ($cartRule[0]['reduction_amount']) {
                        $currency = new Currency((int)$cartRule[0]['reduction_currency']);
                        return Ets_affiliatemarketing::displayPrice($cartRule[0]['reduction_amount'], $currency);
                    }
                }
            } else if ($voucher_type == 'DYNAMIC') {
                $discount = Configuration::get($program . '_APPLY_DISCOUNT');
                if ($discount == 'PERCENT') {
                    $discount_percent = Configuration::get($program . '_REDUCTION_PERCENT');
                    if ($discount_percent)
                        return $discount_percent . '%';
                } else if ($discount == 'AMOUNT') {
                    $discount_amount = Configuration::get($program . '_REDUCTION_AMOUNT');
                    $id_currency = Configuration::get($program . '_ID_CURRENCY');
                    $currency = new Currency((int)$id_currency);
                    if ($discount_amount)
                        return Ets_affiliatemarketing::displayPrice($discount_amount, $currency);
                } else {
                    return false;
                }
            }
            return false;
        }
        return false;
    }
    public static function generateVoucher($type = 'ref', $id_product = 0, $id_cart = 0)
    {
        $context = Context::getContext();
        if ($type == 'ref')
            $highlight = 1;
        else
            $highlight = 0;
        $program = 'ETS_AM_REF';
        $discount_key = '[discount_value]';
        if ($type == 'aff') {
            $discount_key = '[discount_value]';
            $program = 'ETS_AM_AFF';
        }
        $enable = (int)Configuration::get($program . '_OFFER_VOUCHER');
        if ($enable) {
            $id_lang_default = $context->language->id;
            $desc = Configuration::get($program . '_DISCOUNT_DESC', $id_lang_default);
            $name = $program . '_DISCOUNT_DESC';
            $welcome_msg = Configuration::get($program . '_WELCOME_MSG', $id_lang_default);
            $voucher_type = Configuration::get($program . '_VOUCHER_TYPE');
            $voucher_min_amount = (float)Configuration::get($program . '_DISCOUNT_MIN_AMOUNT');
            $voucher_min_amount_tax = (int)Configuration::get($program . '_DISCOUNT_MIN_AMOUNT_TAX');
            $voucher_min_amount_currency = (int)Configuration::get($program . '_DISCOUNT_MIN_AMOUNT_CURRENCY');
            $voucher_min_amount_shipping = (int)Configuration::get($program . '_DISCOUNT_MIN_AMOUNT_SHIPPING');
            $results = array();
            if ($voucher_type == 'FIXED') {
                $voucher_code = Configuration::get($program . '_VOUCHER_CODE');
                $cartRule = CartRule::getCartsRuleByCode($voucher_code, $id_lang_default);
                if ($cartRule) {
                    $id_cart_rule = $cartRule[0]['id_cart_rule'];
                    $voucher = new Ets_Voucher();
                    $voucher->id_cart_rule = $id_cart_rule;
                    $voucher->id_customer = $context->customer->id;
                    $voucher->id_product = $id_product;
                    $voucher->id_cart = $id_cart;
                    $voucher->add(true, true);
                    if ((float)$cartRule[0]['reduction_percent'] > 0) {
                        $welcome_msg = str_replace('[discount_value]', (float)$cartRule[0]['reduction_percent'] . '%', $welcome_msg);
                    } else {
                        $currency = new Currency((int)$cartRule[0]['reduction_currency']);
                        $welcome_msg = str_replace('[discount_value]', $currency->sign . (float)$cartRule[0]['reduction_amount'], $welcome_msg);
                    }
                    $results = array(
                        'code' => $voucher_code,
                        'message' => $welcome_msg,
                        'id_cart_rule' => $id_cart_rule,
                        'from' => date('d-m-Y', strtotime($cartRule[0]['date_from'])),
                        'to' => date('d-m-Y', strtotime($cartRule[0]['date_to'])),
                    );
                } else {
                    $results = array(
                        'code' => $voucher_code,
                        'message' => $welcome_msg,
                        'from' => '00-00-0000',
                        'to' => '00-00-0000',
                    );
                }
            } else if ($voucher_type == 'DYNAMIC') {
                $free_shipping = (int)Configuration::get($program . '_FREE_SHIPPING');
                $discount = Configuration::get($program . '_APPLY_DISCOUNT');
                $prefix_code = Configuration::get($program . '_DISCOUNT_PREFIX');
                $discount_in = (int)Configuration::get($program . '_APPLY_DISCOUNT_IN');
                $code = self::generatePromoCode($prefix_code, $context);
                $reduction_exclude_special = (int)Configuration::get($program . '_EXCLUDE_SPECIAL');
                if ($discount == 'PERCENT') {
                    $discount_percent = Configuration::get($program . '_REDUCTION_PERCENT');
                    $voucher_obj = self::addCodeToCartRules(
                        $code,
                        $name,
                        $desc,
                        $id_product,
                        $id_cart,
                        $free_shipping,
                        $discount_in,
                        $discount_percent,
                        0, 0, 0,
                        $voucher_min_amount,
                        $voucher_min_amount_tax,
                        $voucher_min_amount_currency,
                        $voucher_min_amount_shipping,
                        $highlight,
                        $reduction_exclude_special);
                    if ($voucher_obj->id) {
                        $use_other_voucher = (int)Configuration::get('ETS_AM_' . Tools::strtoupper($type) . '_USE_OTHER_VOUCHER');
                        Db::getInstance()->execute('INSERT INTO `' . _DB_PREFIX_ . 'ets_am_voucher_combination` (id_cart_rule,type,use_other_voucher) VALUES("' . (int)$voucher_obj->id . '","' . pSQL($type) . '","' . (int)$use_other_voucher . '")');
                        $welcome_msg = str_replace('[discount_value]', (float)$voucher_obj->reduction_percent . '%', $welcome_msg);
                        $results = array(
                            'code' => $code,
                            'message' => $welcome_msg,
                            'id_cart_rule' => $voucher_obj->id,
                            'from' => date('d-m-Y', strtotime($voucher_obj->date_from)),
                            'to' => date('d-m-Y', strtotime($voucher_obj->date_to)),
                        );
                    } else
                        return false;
                } elseif ($discount == 'AMOUNT') {
                    $discount_amount = Configuration::get($program . '_REDUCTION_AMOUNT');
                    $id_currency = Configuration::get($program . '_ID_CURRENCY');
                    $reduction_tax = Configuration::get($program . '_REDUCTION_TAX');
                    $voucher_obj = self::addCodeToCartRules(
                        $code,
                        $name,
                        $desc,
                        $id_product,
                        $id_cart,
                        $free_shipping,
                        $discount_in,
                        0,
                        $discount_amount,
                        $id_currency,
                        $reduction_tax,
                        $voucher_min_amount,
                        $voucher_min_amount_tax,
                        $voucher_min_amount_currency,
                        $voucher_min_amount_shipping,
                        $highlight,
                        $reduction_exclude_special
                    );
                    if ($voucher_obj->id) {
                        $currency = new Currency((int)$voucher_obj->reduction_currency);
                        $welcome_msg = str_replace('[discount_value]', $currency->sign . (float)$voucher_obj->reduction_amount, $welcome_msg);
                        $use_other_voucher = (int)Configuration::get('ETS_AM_' . Tools::strtoupper($type) . '_USE_OTHER_VOUCHER');
                        Db::getInstance()->execute('INSERT INTO `' . _DB_PREFIX_ . 'ets_am_voucher_combination` (id_cart_rule,type,use_other_voucher) VALUES("' . (int)$voucher_obj->id . '","' . pSQL($type) . '","' . (int)$use_other_voucher . '")');
                        $results = array(
                            'code' => $code,
                            'message' => $welcome_msg,
                            'id_cart_rule' => $voucher_obj->id,
                            'from' => date('d-m-Y', strtotime($voucher_obj->date_from)),
                            'to' => date('d-m-Y', strtotime($voucher_obj->date_to)),
                        );
                    } else
                        return false;
                } else {
                    return false;
                }
            }
            if ($results) {
                $discount = Ets_AM::getFixedVoucherDiscount($results['code']);
                $discount_value = '';
                if ($discount) {
                    $discount_value = $discount['amount'] . ($discount['type'] == 'amount' ? $discount['currency'] : '%');
                }
                $results['message'] = str_replace($discount_key, $discount_value, $results['message']);
                return $results;
            }
            return false;
        }
        return false;
    }
    public static function getFixedVoucherDiscount($fixed_voucher_code)
    {
        $context = Context::getContext();
        $cart_rule = CartRule::getCartsRuleByCode($fixed_voucher_code, $context->language->id);
        if (count($cart_rule)) {
            $cart_rule = $cart_rule[0];
            if ((float)$cart_rule['reduction_percent'] != 0) {
                $discounted = array(
                    'type' => 'percent',
                    'amount' => (float)$cart_rule['reduction_percent']
                );
            } elseif ($amount = (float)$cart_rule['reduction_amount'] != 0) {
                $currency = Currency::getCurrency((int)$cart_rule['reduction_currency']);
                $discounted = array(
                    'type' => 'amount',
                    'amount' => (float)$amount,
                    'currency' => $currency['iso_code']
                );
            } else {
                $discounted = array();
            }
            return $discounted;
        }
        return array();
    }
    public static function addCodeToCartRules($code, $name, $desc, $id_product = 0, $id_cart = 0, $free_shipping = 0,
                                              $discount_in = 0, $discount_percent = 0, $discount_amount = 0, $id_currency = 0, $reduction_tax = 0,
                                              $voucher_min_amount = 0,
                                              $voucher_min_amount_tax = 0,
                                              $voucher_min_amount_currency = 0,
                                              $voucher_min_amount_shipping = 0,
                                              $highlight = 0,
                                              $reduction_exclude_special = 0)
    {
        if ($id_product && Context::getContext()->customer->id) {
            $id_cart_rule = Db::getInstance()->getValue('SELECT id_cart_rule FROM `' . _DB_PREFIX_ . 'cart_rule` WHERE id_customer="' . (int)Context::getContext()->customer->id . '" AND reduction_product="' . (int)$id_product . '"');
        }
        $context = Context::getContext();
        $languages = Language::getLanguages(false);
        if (isset($id_cart_rule) && $id_cart_rule)
            $cartRuleObj = new CartRule($id_cart_rule);
        else {
            $cartRuleObj = new CartRule();
            $cartRuleObj->code = $code;
        }
        $cartRuleObj->date_from = date('Y-m-d H:i:s');
        $cartRuleObj->date_to = date('Y-m-d H:i:s', strtotime('+' . $discount_in . 'days', strtotime(date('Y-m-d H:i:s'))));
        foreach ($languages as $lang) {
            $cartRuleObj->name[(int)$lang['id_lang']] = Configuration::get($name, (int)$lang['id_lang']);
        }
        $cartRuleObj->quantity = 1;
        $cartRuleObj->quantity_per_user = 1;
        $cartRuleObj->reduction_percent = $discount_percent;
        $cartRuleObj->reduction_amount = $discount_amount;
        $cartRuleObj->reduction_currency = $id_currency;
        $cartRuleObj->reduction_product = $id_product;
        $cartRuleObj->reduction_tax = $reduction_tax;
        $cartRuleObj->free_shipping = $free_shipping;
        $cartRuleObj->active = 1;
        $cartRuleObj->highlight = $highlight;
        $cartRuleObj->reduction_exclude_special = $reduction_exclude_special;
        $cartRuleObj->minimum_amount = $voucher_min_amount;
        if ($voucher_min_amount) {
            $cartRuleObj->minimum_amount_tax = $voucher_min_amount_tax;
            $cartRuleObj->minimum_amount_currency = $voucher_min_amount_currency;
            $cartRuleObj->minimum_amount_shipping = $voucher_min_amount_shipping;
        }
        $cartRuleObj->id_customer = $context->customer->id;
        $cartRuleObj->description = $desc;
        if ($cartRuleObj->id)
            $cartRuleObj->update();
        else {
            $cartRuleObj->add();
            $voucher = new Ets_Voucher();
            $voucher->id_cart_rule = $cartRuleObj->id;
            $voucher->id_customer = $context->customer->id;
            $voucher->id_product = $id_product;
            $voucher->id_cart = $id_cart;
            $voucher->add();
        }
        return $cartRuleObj;
    }
    public static function getStatsReward($params = array(), $context = null, $for_dashboard = false, $frontend = false)
    {
        if (!$context) {
            $context = Context::getContext();
        }
        $start_date = date('Y-m-01 00:00:00');
        $end_date = date('Y-m-t 23:59:59');
        $time_group = "DATE(reward.datetime_added)";
        $time_select = "DATE(reward.datetime_added)";
        $by_month = false;
        $by_year = false;
        $trans = Module::getInstanceByName('ets_affiliatemarketing')->getTranslates();
        $score_select = "SUM(reward.amount)";
        $line_color = '#2ca121';
        $line_x_label = $trans['reward'];
        $filter_where = '';
        $filter_where_2 = '';
        if (isset($params['program']) && $params['program']) {
            $filter_where .= " AND reward.program = '" . pSQL($params['program']) . "'";
        }
        if (isset($params['status']) && ($params['status'] || $params['status'] == '0')) {
            $filter_where .= " AND reward.status = " . (int)$params['status'] . "";
            $filter_where_2 .= " AND (ru.status = " . (int)$params['status'] . ")";
        } elseif (isset($params['reward_status']) && $params['reward_status'] != '' && $params['reward_status'] !== 'all') {
            $filter_where .= " AND reward.status = " . (int)$params['reward_status'] . "";
            $filter_where_2 .= " AND (ru.status = " . (int)$params['reward_status'] . ")";
        }
        if (!isset($params['status']) && !isset($params['reward_status'])) {
            $filter_where .= " AND reward.status = 1";
            $filter_where_2 .= " AND ru.status = 1";
        }
        if (!isset($params['date_type']) || !$params['date_type']) {
            $params['date_type'] = 'this_year';
        }
        if (isset($params['date_from']) && $params['date_from'] && isset($params['date_to']) && $params['date_to']) {
            $start_date = $params['date_from'] . ' 00:00:00';
            $end_date = $params['date_to'] . ' 23:59:59';
            if (date('Y', strtotime($start_date)) != date('Y', strtotime($end_date))) {
                $by_year = true;
                $time_group = "YEAR(reward.datetime_added)";
                $time_select = "YEAR(reward.datetime_added)";
            } else if ((int)date('m', strtotime($start_date)) != (int)date('m', strtotime($end_date))) {
                $by_month = true;
                $time_group = "MONTH(reward.datetime_added), YEAR(reward.datetime_added)";
                $time_select = "DATE_FORMAT(reward.datetime_added, '%Y-%m')";
            }
        } elseif (isset($params['date_type']) && $params['date_type']) {
            if ($params['date_type'] == 'this_year') {
                $start_date = date('Y-01-01 00:00:00');
                $end_date = date('Y-12-01 23:59:59');
                $by_month = true;
                $time_group = "MONTH(reward.datetime_added), YEAR(reward.datetime_added)";
                $time_select = "DATE_FORMAT(reward.datetime_added, '%Y-%m')";
            } else {
                $max_time = Db::getInstance()->getValue("SELECT MAX(datetime_added) FROM `" . _DB_PREFIX_ . "ets_am_reward` WHERE id_shop = " . (int)$context->shop->id . " AND (datetime_added IS NOT NULL AND datetime_added != '0000-00-00 00:00:00')");
                $min_time = Db::getInstance()->getValue("SELECT Min(datetime_added) FROM `" . _DB_PREFIX_ . "ets_am_reward` WHERE id_shop = " . (int)$context->shop->id . " AND (datetime_added IS NOT NULL AND datetime_added != '0000-00-00 00:00:00')");
                $start_date = $min_time;
                $end_date = $max_time;
                if (date('Y', strtotime($max_time)) != date('Y', strtotime($min_time))) {
                    $by_year = true;
                    $time_group = "YEAR(reward.datetime_added)";
                    $time_select = "YEAR(reward.datetime_added)";
                } elseif (date('m', strtotime($max_time)) != date('m', strtotime($min_time))) {
                    $by_month = true;
                    $time_group = "MONTH(reward.datetime_added), YEAR(reward.datetime_added)";
                    $time_select = "DATE_FORMAT(reward.datetime_added, '%Y-%m')";
                }
            }
        }
        if (isset($params['id_customer']) && $params['id_customer']) {
            $filter_where .= " AND reward.id_customer = " . (int)$params['id_customer'];
            $filter_where_2 .= " AND ru.id_customer = " . (int)$params['id_customer'];
        }
        $filter_where .= " AND reward.datetime_added >= '" . pSQL($start_date) . "' AND reward.datetime_added <= '" . pSQL($end_date) . "'";
        $filter_where_2 .= " AND ru.datetime_added >= '" . pSQL($start_date) . "' AND ru.datetime_added <= '" . pSQL($end_date) . "'";
        $sql = "SELECT $score_select as total_score, " . (string)$time_select . " AS date_added 
                    FROM `" . _DB_PREFIX_ . "ets_am_reward` reward
                    LEFT JOIN  `" . _DB_PREFIX_ . "orders` ord ON (reward.id_order = ord.id_order OR IFNULL(ord.id_order, 0) = reward.id_order)
                    LEFT JOIN `" . _DB_PREFIX_ . "currency` currency ON ord.id_currency = currency.id_currency
                    WHERE reward.`deleted` = 0 AND reward.id_shop = " . (int)$context->shop->id . (string)$filter_where . " 
                    GROUP BY " . pSQL($time_group);
        $results = Db::getInstance()->executeS($sql);
        $sql_usage = "SELECT SUM(ru.amount) as total_score, " . (string)str_replace('reward.', 'ru.', $time_select) . " AS date_added 
                    FROM `" . _DB_PREFIX_ . "ets_am_reward_usage` ru
                    WHERE ru.`deleted` = 0 AND ru.id_shop = " . (int)$context->shop->id . (string)$filter_where_2 . "
                    GROUP BY " . str_replace('reward.', 'ru.', $time_group);
        if ($for_dashboard) {
            $results_usage = Db::getInstance()->executeS($sql_usage);
        }
        if ($by_month) {
            $datas_date = self::getDateRanger($start_date, $end_date, 'Y-m-01', true, 'month');
        } else if ($by_year) {
            $datas_date = self::getYearRanger($start_date, $end_date, 'Y-01-01', true);
        } else {
            $datas_date = self::getDateRanger($start_date, $end_date, 'Y-m-d', true, 'date');
        }
        $total_score = $datas_date;
        $total_score_2 = $datas_date;
        $datas = array();
        $total_score_data = array();
        $total_score_data_2 = array();
        if ($results) {
            foreach ($results as &$result) {
                if ($by_month) {
                    $key_data = $result['date_added'] . '-01';
                } elseif ($by_year) {
                    $key_data = $result['date_added'] . '-01-01';
                } else {
                    $key_data = $result['date_added'];
                }
                $total_score[$key_data] = $frontend ? Ets_AM::displayReward((float)$result['total_score'], false) : Ets_AM::displayRewardAdmin((float)$result['total_score'], false);
            }
            foreach ($total_score as $date => $data) {
                if ($data) {
                }
                $total_score_data[] = array(
                    'x' => strtotime($date) * 1000,
                    'y' => floor((float)$total_score[$date] * 100) / 100,
                );
            }
        } else {
            foreach ($total_score as $date => $data) {
                if ($data) {
                }
                $total_score_data[] = array(
                    'x' => strtotime($date) * 1000,
                    'y' => 0,
                );
            }
        }
        if (isset($results_usage) && $results_usage) {
            foreach ($results_usage as &$result) {
                if ($by_month) {
                    $key_data = $result['date_added'] . '-01';
                } elseif ($by_year) {
                    $key_data = $result['date_added'] . '-01-01';
                } else {
                    $key_data = $result['date_added'];
                }
                $total_score[$key_data] = $frontend ? Ets_AM::displayReward((float)$result['total_score'], false) : Ets_AM::displayRewardAdmin((float)$result['total_score'], false);
            }
            foreach ($total_score as $date => $data) {
                if ($data) {
                }
                $total_score_data_2[] = array(
                    'x' => strtotime($date) * 1000,
                    'y' => floor((float)$total_score[$date] * 100) / 100,
                );
            }
        } else {
            foreach ($total_score_2 as $date => $data) {
                if ($data) {
                }
                $total_score_data_2[] = array(
                    'x' => strtotime($date) * 1000,
                    'y' => 0,
                );
            }
        }
        $datas['x_asis'] = 'date';
        if ($by_month) {
            $datas['x_asis'] = 'month';
        }
        if ($by_year) {
            $datas['x_asis'] = 'year';
        }
        $datas['count_values'] = count($total_score_data);
        $datas['count_values_2'] = count($total_score_data_2);
        $datas['data'] = array(
            array(
                'key' => $line_x_label,
                'values' => $total_score_data,
                'color' => $line_color,
                'area' => 1,
            )
        );
        if ($for_dashboard) {
            $datas['data'][] = array(
                'key' => $trans['reward_usage'],
                'values' => $total_score_data_2,
                'color' => '#F06295',
                'area' => 1,
            );
        }
        return $datas;
    }
    public static function getStatsCustomer($params = array())
    {
        $context = Context::getContext();
        $start_date = date('Y-m-01 00:00:00');
        $end_date = date('Y-m-t 23:59:59');
        $time_group = "DATE(sponsor.datetime_added)";
        $time_select = "DATE(sponsor.datetime_added)";
        $by_month = false;
        $by_year = false;
        $trans = Module::getInstanceByName('ets_affiliatemarketing')->getTranslates();
        $score_select = "COUNT(DISTINCT sponsor.id_customer)";
        $line_color = '#ff3399';
        $line_x_label = $trans['customers'];
        $filter_where = '';
        if (isset($params['date_from']) && $params['date_from'] && isset($params['date_to']) && $params['date_to']) {
            $start_date = $params['date_from'] . ' 00:00:00';
            $end_date = $params['date_to'] . ' 23:59:59';
            if (date('Y', strtotime($start_date)) != date('Y', strtotime($end_date))) {
                $by_year = true;
                $time_group = "YEAR(sponsor.datetime_added)";
                $time_select = "YEAR(sponsor.datetime_added)";
            } else if ((int)date('m', strtotime($start_date)) != (int)date('m', strtotime($end_date))) {
                $by_month = true;
                $time_group = "MONTH(sponsor.datetime_added), YEAR(sponsor.datetime_added)";
                $time_select = "DATE_FORMAT(sponsor.datetime_added, '%Y-%m')";
            }
        } else {
            if (isset($params['date_type']) && $params['date_type']) {
                if ($params['date_type'] == 'this_year') {
                    $start_date = date('Y-01-01 00:00:00');
                    $end_date = date('Y-12-31 23:59:59');
                    $by_month = true;
                    $time_group = "MONTH(sponsor.datetime_added), YEAR(sponsor.datetime_added)";
                    $time_select = "DATE_FORMAT(sponsor.datetime_added, '%Y-%m')";
                } else if ($params['date_type'] == 'all_times') {
                    $max_time = Db::getInstance()->getValue("SELECT MAX(datetime_added) FROM `" . _DB_PREFIX_ . "ets_am_sponsor` WHERE id_shop = " . (int)$context->shop->id);
                    $min_time = Db::getInstance()->getValue("SELECT Min(datetime_added) FROM `" . _DB_PREFIX_ . "ets_am_sponsor` WHERE id_shop = " . (int)$context->shop->id);
                    $start_date = $min_time;
                    $end_date = $max_time;
                    if (date('Y', strtotime($max_time)) != date('Y', strtotime($min_time))) {
                        $by_year = true;
                        $time_group = "YEAR(sponsor.datetime_added)";
                        $time_select = "YEAR(sponsor.datetime_added)";
                    } else if (date('m', strtotime($max_time)) != date('m', strtotime($min_time))) {
                        $by_month = true;
                        $time_group = "MONTH(sponsor.datetime_added), YEAR(sponsor.datetime_added)";
                        $time_select = "DATE_FORMAT(sponsor.datetime_added, '%Y-%m')";
                    }
                }
            }
        }
        if (isset($params['id_customer']) && $params['id_customer']) {
            $filter_where .= " AND sponsor.id_customer = " . (int)$params['id_customer'];
        }
        $sql = "SELECT sponsor.*, $score_select as total_score, $time_select AS date_added 
                    FROM `" . _DB_PREFIX_ . "ets_am_sponsor` sponsor
                    WHERE sponsor.level = 1 " . (string)$filter_where . " AND sponsor.datetime_added >= '" . pSQL($start_date) . "' AND sponsor.datetime_added <= '" . pSQL($end_date) . "'
                    GROUP BY " . (string)$time_group;
        $results = Db::getInstance()->executeS($sql);
        if ($by_month) {
            $datas_date = self::getDateRanger($start_date, $end_date, 'Y-m-01', true, 'month');
        } else if ($by_year) {
            $datas_date = self::getYearRanger($start_date, $end_date, 'Y-01-01', true);
        } else {
            $datas_date = self::getDateRanger($start_date, $end_date, 'Y-m-d', true, 'date');
        }
        $total_score = $datas_date;
        $datas = array();
        $total_score_data = array();
        if ($results) {
            foreach ($results as &$result) {
                if ($by_month) {
                    $key_data = $result['date_added'] . '-01';
                } elseif ($by_year) {
                    $key_data = $result['date_added'] . '-01-01';
                } else {
                    $key_data = $result['date_added'];
                }
                $total_score[$key_data] = (float)$result['total_score'];
            }
            foreach ($total_score as $date => $data) {
                if ($data) {
                }
                $total_score_data[] = array(
                    'x' => strtotime($date) * 1000,
                    'y' => $total_score[$date],
                );
            }
        } else {
            foreach ($total_score as $date => $data) {
                if ($data) {
                }
                $total_score_data[] = array(
                    'x' => strtotime($date) * 1000,
                    'y' => 0,
                );
            }
        }
        $datas['x_asis'] = 'date';
        if ($by_month) {
            $datas['x_asis'] = 'month';
        }
        if ($by_year) {
            $datas['x_asis'] = 'year';
        }
        $datas['data'] = array(
            array(
                'key' => $line_x_label,
                'values' => $total_score_data,
                'color' => $line_color,
            ),
        );
        return $datas;
    }
    public static function getStartChartReward($params)
    {
        $context = Context::getContext();
        if (isset($params['stats_type']) && $params['stats_type'] == 'customers') {
            return self::getStatsCustomer($params);
        }
        $start_date = date('Y-m-01 00:00:00');
        $end_date = date('Y-m-t 23:59:59');
        $time_group = "DATE(reward.datetime_added)";
        $time_select = "DATE(reward.datetime_added)";
        $by_month = false;
        $by_year = false;
        $trans = Module::getInstanceByName('ets_affiliatemarketing')->getTranslates();
        $score_select = "SUM(IF(ord.valid = 1, ord.total_paid / currency.conversion_rate, 0))*count(DISTINCT ord.id_order)/count(*)";
        $line_color = '#f06295';
        $line_x_label = $trans['turnover'];
        $score_select_2 = "SUM(reward.amount)";
        $line_color_2 = '#57c2a0';
        $line_x_label_2 = $trans['reward'];
        $filter_where = '';
        if (isset($params['program']) && $params['program']) {
            $filter_where .= " AND reward.program = '" . pSQL($params['program']) . "'";
        }
        if (isset($params['stats_type']) && $params['stats_type'] == 'reward') {
            if (isset($params['status']) && $params['status'] != '') {
                $filter_where .= " AND (reward.status = " . (int)$params['status'] . ")";
            } elseif (isset($params['reward_status']) && $params['reward_status'] != '' && $params['reward_status'] !== 'all') {
                $filter_where .= " AND (reward.status = " . (int)$params['reward_status'] . ")";
            }
            if (!isset($params['status']) && !isset($params['reward_status'])) {
                $filter_where .= " AND reward.status = 1";
            }
        }
        if (isset($params['stats_type']) && $params['stats_type'] == 'orders') {
            if (isset($params['order_status']) && $params['order_status'] != '') {
                $filter_where .= " AND ord.current_state = " . (int)$params['order_status'];
            }
        }
        if (!isset($params['date_type']) || !$params['date_type']) {
            $distance = (int)Db::getInstance()->getValue("
                SELECT (YEAR(MAX(datetime_added)) - YEAR(MIN(datetime_added))) as `distance` FROM `" . _DB_PREFIX_ . "ets_am_reward` 
                WHERE id_shop = " . (int)$context->shop->id . " AND (datetime_added IS NOT NULL AND datetime_added != '0000-00-00 00:00:00' AND datetime_added != '0000-00-00 00:00:00.000000')
            ");
            $params['date_type'] = ($distance <= 5 ? 'this_year' : 'all_times');
        }
        if (isset($params['date_type']) && $params['date_type']) {
            if ($params['date_type'] == 'this_year') {
                $start_date = date('Y-01-01 00:00:00');
                $end_date = date('Y-m-t 00:01:00', strtotime(date('Y-12-01')));
                $by_month = true;
                $time_group = "MONTH(reward.datetime_added), YEAR(reward.datetime_added)";
                $time_select = "DATE_FORMAT(reward.datetime_added, '%Y-%m')";
            } elseif ($params['date_type'] == 'all_times') {
                $max_time = Db::getInstance()->getValue("SELECT MAX(datetime_added) FROM `" . _DB_PREFIX_ . "ets_am_reward` WHERE id_shop = " . (int)$context->shop->id . " AND (datetime_added IS NOT NULL AND datetime_added != '0000-00-00 00:00:00' AND datetime_added != '0000-00-00 00:00:00.000000')");
                $min_time = Db::getInstance()->getValue("SELECT MIN(datetime_added) FROM `" . _DB_PREFIX_ . "ets_am_reward` WHERE id_shop = " . (int)$context->shop->id . " AND (datetime_added IS NOT NULL AND datetime_added != '0000-00-00 00:00:00' AND datetime_added != '0000-00-00 00:00:00.000000')");
                $start_date = $min_time;
                $end_date = $max_time;
                if (date('Y', strtotime($max_time)) != date('Y', strtotime($min_time))) {
                    $by_year = true;
                    $time_group = "YEAR(reward.datetime_added)";
                    $time_select = "YEAR(reward.datetime_added)";
                } else if (date('m', strtotime($max_time)) != date('m', strtotime($min_time))) {
                    $by_month = true;
                    $time_group = "MONTH(reward.datetime_added), YEAR(reward.datetime_added)";
                    $time_select = "DATE_FORMAT(reward.datetime_added, '%Y-%m')";
                }
            } elseif ($params['date_type'] == 'time_ranger') {
                if (isset($params['date_from']) && $params['date_from'] && isset($params['date_to']) && $params['date_to']) {
                    $start_date = $params['date_from'] . ' 00:00:00';
                    $end_date = $params['date_to'] . ' 23:59:59';
                    if (date('Y', strtotime($start_date)) != date('Y', strtotime($end_date))) {
                        $by_year = true;
                        $time_group = "YEAR(reward.datetime_added)";
                        $time_select = "YEAR(reward.datetime_added)";
                    } else if ((int)date('m', strtotime($start_date)) != (int)date('m', strtotime($end_date))) {
                        $by_month = true;
                        $time_group = "MONTH(reward.datetime_added), YEAR(reward.datetime_added)";
                        $time_select = "DATE_FORMAT(reward.datetime_added, '%Y-%m')";
                    }
                }
            }
        }
        if (isset($params['id_customer']) && $params['id_customer']) {
            $filter_where .= " AND reward.id_customer = " . (int)$params['id_customer'];
        }
        $filter_where .= " AND reward.datetime_added >= '" . pSQl($start_date) . "' AND reward.datetime_added <= '" . pSQL($end_date) . "'";
        $sql = "SELECT " . (string)$score_select . " as total_score, " . (string)$score_select_2 . " as total_score_2," . (string)$time_select . " AS date_added 
                    FROM `" . _DB_PREFIX_ . "ets_am_reward` reward
                    INNER JOIN  `" . _DB_PREFIX_ . "orders` ord ON (reward.id_order = ord.id_order)
                    LEFT JOIN `" . _DB_PREFIX_ . "currency` currency ON ord.id_currency = currency.id_currency
                    WHERE reward.`deleted` = 0 AND reward.id_shop = " . (int)$context->shop->id . " " . (string)$filter_where . "
                    GROUP BY " . (string)$time_group;
        $results = Db::getInstance()->executeS($sql);
        if ($by_month) {
            $datas_date = self::getDateRanger($start_date, $end_date, 'Y-m-01', true, 'month');
        } else if ($by_year) {
            $datas_date = self::getYearRanger($start_date, $end_date, 'Y-01-01', true);
        } else {
            $datas_date = self::getDateRanger($start_date, $end_date, 'Y-m-d', true, 'date');
        }
        $total_score = $datas_date;
        $total_score_2 = $datas_date;
        $datas = array();
        $total_score_data = array();
        $total_score_data_2 = array();
        if ($results) {
            foreach ($results as &$result) {
                if ($by_month) {
                    $key_data = $result['date_added'] . '-01';
                } elseif ($by_year) {
                    $key_data = $result['date_added'] . '-01-01';
                } else {
                    $key_data = $result['date_added'];
                }
                $total_score[$key_data] = (float)$result['total_score'];
                $total_score_2[$key_data] = (float)$result['total_score_2'];
            }
            foreach ($total_score as $date => $data) {
                if ($data) {
                }
                $total_score_data[] = array(
                    'x' => strtotime($date) * 1000,
                    'y' => $total_score[$date],
                );
                $total_score_data_2[] = array(
                    'x' => strtotime($date) * 1000,
                    'y' => $total_score_2[$date],
                );
            }
        }
        if (isset($distance)) {
            $datas['distance'] = $distance;
        }
        $datas['x_asis'] = 'date';
        if ($by_month) {
            $datas['x_asis'] = 'month';
        }
        if ($by_year) {
            $datas['x_asis'] = 'year';
        }
        $datas['count_values'] = max(count($total_score_data), count($total_score_data_2));
        $datas['data'] = array(
            array(
                'key' => $line_x_label,
                'values' => $total_score_data,
                'color' => $line_color,
                'area' => '1',
            ),
            array(
                'key' => $line_x_label_2,
                'values' => $total_score_data_2,
                'color' => $line_color_2,
                'area' => '1',
            ),
        );
        return $datas;
    }
    public static function getDateRanger($start, $end, $format = 'Y-m-d', $list_data_by_date = false, $type = 'date')
    {
        $array = array();
        $interval = new DateInterval('P1D');
        if ($type == 'month') {
            $interval = DateInterval::createFromDateString('1 month');
        }
        $period = new DatePeriod(
            new DateTime($start),
            $interval,
            new DateTime($end));
        foreach ($period as $date) {
            if ($list_data_by_date) {
                $array[$date->format($format)] = 0;
            } else {
                $array[] = $date->format($format);
            }
        }
        return $array;
    }
    public static function getYearRanger($start, $end, $format = 'Y', $list_data_by_date = false)
    {
        $array = array();
        $getRangeYear = range(gmdate('Y', strtotime($start)), gmdate('Y', strtotime($end)));
        foreach ($getRangeYear as $year) {
            if ($list_data_by_date) {
                $array[date($format, strtotime($year . '-01-01 00:00:00'))] = 0;
            } else {
                $array[] = date($format, strtotime($year . '-01-01 00:00:00'));
            }
        }
        return $array;
    }
    public static function getStatsCounter()
    {
        $context = Context::getContext();
        $sql = "SELECT (
                SELECT SUM(IF (o.valid = 1,o.total_paid / cu.conversion_rate,0))
                FROM `" . _DB_PREFIX_ . "ets_am_reward` r
                JOIN `" . _DB_PREFIX_ . "orders` o ON( r.id_order = o.id_order AND r.id_order > 0)
                JOIN (SELECT id_order, id_ets_am_reward as max_id
                            FROM `" . _DB_PREFIX_ . "ets_am_reward` r2
                            GROUP BY id_order
                ) rr ON r.id_order = rr.id_order AND r.id_ets_am_reward = rr.max_id
                JOIN `" . _DB_PREFIX_ . "currency` cu ON (o.id_currency = cu.id_currency)
                WHERE r.`deleted` = 0 AND r.id_shop = " . (int)$context->shop->id . " AND r.status = 1
                ) AS turnover, 
            (SELECT SUM(amount) FROM `" . _DB_PREFIX_ . "ets_am_reward` WHERE `deleted` = 0 AND id_shop=" . (int)$context->shop->id . " AND status = 1) AS point_reward, 
            COUNT(DISTINCT ord.id_order) AS orders, 
            (SELECT COUNT(*) FROM `" . _DB_PREFIX_ . "ets_am_sponsor` WHERE level = 1 AND id_shop=" . (int)$context->shop->id . ") AS customer,
             (SUM(reward.amount) / SUM(ord.total_paid / currency.conversion_rate) * 100) AS conversion_rate,
             SUM(IF(reward.status = 1 OR reward.status = 0,reward.amount, 0)) AS reward_validated,
             (
            SELECT SUM(total_paid / cu.conversion_rate)
                FROM `" . _DB_PREFIX_ . "ets_am_reward` r
                JOIN `" . _DB_PREFIX_ . "orders` o ON( r.id_order = o.id_order AND r.id_order > 0)
                JOIN (SELECT id_order, MAX(id_ets_am_reward) as max_id
                                    FROM `" . _DB_PREFIX_ . "ets_am_reward` r2
                                    GROUP BY id_order
                ) rr ON r.id_order = rr.id_order AND r.id_ets_am_reward = rr.max_id
                JOIN `" . _DB_PREFIX_ . "currency` cu ON (o.id_currency = cu.id_currency)
            ) AS order_of_reward_validated
                FROM `" . _DB_PREFIX_ . "ets_am_reward` reward
                LEFT JOIN  `" . _DB_PREFIX_ . "orders` ord ON reward.id_order = ord.id_order
                LEFT JOIN `" . _DB_PREFIX_ . "currency` currency ON ord.id_currency = currency.id_currency
                WHERE reward.`deleted` = 0 AND reward.id_shop = " . (int)$context->shop->id;
        $scores = Db::getInstance()->getRow($sql);
        if ($scores) {
            $mp = -1;
            $scores['net_profit'] = $mp >= 0 ? Ets_affiliatemarketing::displayPrice(($scores['turnover'] * $mp / 100) - $scores['point_reward'], (int)Configuration::get('PS_CURRENCY_DEFAULT')) : Ets_affiliatemarketing::displayPrice(0.00, (int)Configuration::get('PS_CURRENCY_DEFAULT'));
            $scores['point_reward'] = ETS_AM::displayRewardAdmin((float)$scores['point_reward']);
            $scores['turnover'] = Ets_affiliatemarketing::displayPrice((float)$scores['turnover'], (int)Configuration::get('PS_CURRENCY_DEFAULT'));
            $scores['conversion_rate'] = (float)$scores['order_of_reward_validated'] > 0 ? round((float)$scores['reward_validated'] / (float)$scores['order_of_reward_validated'] * 100, 2) : 0.00;
        }
        return $scores;
    }
    /**
     * @param array $params
     * @return array
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public static function getStatsTopTrending($params = array())
    {
        $context = Context::getContext();
        $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
        $page = isset($params['page']) && ($page = (int)$params['page']) && $page > 0 ? $page : 1;
        $limit = isset($params['limit']) && ($limit = (int)$params['limit']) && $limit > 0 ? $limit : 10;
        $type = isset($params['type']) && ($type = Tools::strtolower((string)$params['type'])) && in_array($type, array('recent_orders', 'best_seller', 'top_sponsor', 'top_affiliate', 'top_customer', 'top_reward_accounts')) ? $type : "recent_orders";
        $data_filter = isset($params['data_filter']) && is_array($params['data_filter']) ? $params['data_filter'] : array();
        $order_state = isset($data_filter['order_state']) && $data_filter['order_state'] ? (int)$data_filter['order_state'] : false;
        $type_date = isset($data_filter['type_date_filter']) && ($type_date = Tools::strtolower((string)$data_filter['type_date_filter'])) && in_array($type_date, array('all_times', 'this_month', 'this_year', 'time_ranger')) ? $type_date : "all_times";
        $date_from = isset($data_filter['date_from']) && Validate::isDate($data_filter['date_from']) ? $data_filter['date_from'] . ' 00:00:00' : false;
        $date_to = isset($data_filter['date_to']) && Validate::isDate($data_filter['date_to']) ? $data_filter['date_to'] . ' 23:59:59' : false;
        $filter_reward_status = isset($data_filter['reward_status']) && $data_filter['reward_status']!='' ? $data_filter['reward_status'] : false;
        $filter_where = " AND reward.`deleted` = 0";
        $start_date = '';
        $end_date = '';
        $trans = Module::getInstanceByName('ets_affiliatemarketing')->getTranslates();
        if ($order_state !== false) {
            $filter_where .= " AND ord.current_state = " . (int)$order_state;
        }
        if ($type_date == 'this_month') {
            $start_date = date('Y-m-01 00:00:00');
            $end_date = date('Y-m-t 23:59:59');
            if ($type == 'recent_orders') {
                $filter_where .= " AND ord.date_add >= '" . pSQL($start_date) . "' AND ord.date_add <= '" . pSQL($end_date) . "'";
            } else {
                $filter_where .= " AND reward.datetime_added >= '" . pSQL($start_date) . "' AND reward.datetime_added <= '" . pSQL($end_date) . "'";
            }
        }
        if ($type_date == 'this_year') {
            $start_date = date('Y-01-01 00:00:00');
            $end_date = date('Y-12-31 23:59:59');
            if ($type == 'recent_orders') {
                $filter_where .= " AND ord.date_add >= '" . pSQL($start_date) . "' AND ord.date_add <= '" . pSQL($end_date) . "'";
            } else {
                $filter_where .= " AND reward.datetime_added >= '" . pSQL($start_date) . "' AND reward.datetime_added <= '" . pSQL($end_date) . "'";
            }
        }
        if ($type_date == 'time_ranger' && ($date_from !== false || $date_to !== false)) {
            if ($type == 'recent_orders') {
                $filter_where .= ($date_from !== false ? " AND ord.date_add >= '" . pSQL($date_from) . "'" : "") . ($date_to !== false ? " AND ord.date_add <= '" . pSQL($date_to) . "'" : "");
            } else {
                $filter_where .= ($date_from !== false ? " AND reward.datetime_added >= '" . pSQL($date_from) . "'" : "") . ($date_to !== false ? " AND reward.datetime_added <= '" . pSQL($date_to) . "'" : "");
            }
        }
        if ($filter_reward_status !== false) {
            if ($type !== 'recent_orders' && $type !== 'best_seller') {
                $filter_where .= " AND (reward.status = " . (int)$filter_reward_status . ")";
                $filter_reward_status = 1;
            }
        }
        if ($filter_reward_status === false)
            $filter_reward_status = 0;
        $filter_time = "1";
        if ($start_date && $end_date) {
            $filter_time = "datetime_added >= '" . pSQL($start_date) . "' AND datetime_added <= '" . pSQL($end_date) . "'";
        }
        if ($type == 'recent_orders') {
            $sql_total = "SELECT COUNT(DISTINCT ord.id_order) as total
                    FROM `" . _DB_PREFIX_ . "orders`  ord
                JOIN `" . _DB_PREFIX_ . "ets_am_reward` reward ON (reward.id_order = ord.id_order)
                JOIN (SELECT id_order,MAX(id_ets_am_reward) AS max_id
                    FROM
                        `" . _DB_PREFIX_ . "ets_am_reward`                     GROUP BY
                            id_order
                    ) r ON (
                        r.id_order = reward.id_order
                        AND reward.id_ets_am_reward = r.max_id
                    )
                LEFT JOIN `" . _DB_PREFIX_ . "order_state_lang` osl ON ord.current_state = osl.id_order_state

                WHERE CASE WHEN osl.id_lang > 0 THEN osl.id_lang = " . (int)$default_lang . " ELSE TRUE END AND reward.`deleted` = 0 AND reward.id_shop = " . (int)$context->shop->id . " " . (string)$filter_where;
            $total_result = (int)Db::getInstance()->getValue($sql_total);
            if ($total_result > 0) {
                $total_page = (int)ceil($total_result / $limit);
                if ($page > $total_page) {
                    $page = $total_page;
                }
                $offset = ($page - 1) * $limit;
                $sql = "SELECT  CONCAT(customer.firstname, ' ',customer.lastname) AS username, reward.id_customer AS id_customer, ord.id_order AS id_order, 
                SUM(od.product_quantity) as total_product, 
                ord.total_paid_tax_incl / currency.conversion_rate AS total_turnover,
                reward.datetime_added as datetime_added, osl.name as order_state, osl.name as status, osl.template as state_template
                    FROM  `" . _DB_PREFIX_ . "ets_am_reward`  reward
                JOIN `" . _DB_PREFIX_ . "orders` ord ON (reward.id_order = ord.id_order AND IFNULL(reward.id_order, 0) = ord.id_order)
                JOIN (SELECT id_order,MAX(id_ets_am_reward) AS max_id
                    FROM
                        `" . _DB_PREFIX_ . "ets_am_reward`                     GROUP BY
                            id_order
                    ) r ON (
                        r.id_order = reward.id_order
                        AND reward.id_ets_am_reward = r.max_id
                    )
                LEFT JOIN `" . _DB_PREFIX_ . "customer` customer ON reward.id_customer = customer.id_customer
                LEFT JOIN `" . _DB_PREFIX_ . "currency` currency ON ord.id_currency = currency.id_currency
                LEFT JOIN `" . _DB_PREFIX_ . "order_state_lang` osl ON ord.current_state = osl.id_order_state
                LEFT JOIN `" . _DB_PREFIX_ . "order_detail` od ON ord.id_order = od.id_order
                WHERE CASE WHEN osl.id_lang > 0 THEN osl.id_lang = " . (int)$default_lang . " ELSE TRUE END AND reward.id_shop = " . (int)$context->shop->id . " $filter_where 
                GROUP BY ord.id_order
                ORDER BY ord.date_add DESC
                LIMIT " . (int)$offset . ", " . (int)$limit;
                $results = Db::getInstance()->executeS($sql);
                foreach ($results as &$result) {
                    $result['total_turnover'] = Ets_affiliatemarketing::displayPrice((float)$result['total_turnover'], (int)Configuration::get('PS_CURRENCY_DEFAULT'));
                }
                return array(
                    'total_result' => $total_result,
                    'total_page' => $total_page,
                    'per_page' => $limit,
                    'results' => $results,
                    'current_page' => $page,
                );
            } else {
                return array(
                    'total_result' => 0,
                    'total_page' => 1,
                    'per_page' => 10,
                    'results' => array(),
                    'current_page' => 1,
                );
            }
        } elseif ($type == 'best_seller') {
            $sql_total = "SELECT COUNT(DISTINCT od.product_id) AS total
                    FROM `" . _DB_PREFIX_ . "orders`  ord
                JOIN `" . _DB_PREFIX_ . "ets_am_reward` reward ON reward.id_order = ord.id_order
                JOIN (SELECT id_order,MAX(id_ets_am_reward) AS max_id
                    FROM
                        `" . _DB_PREFIX_ . "ets_am_reward`                     GROUP BY
                            id_order
                    ) r ON (
                        r.id_order = reward.id_order
                        AND reward.id_ets_am_reward = r.max_id
                    )
                LEFT JOIN `" . _DB_PREFIX_ . "order_state_lang` osl ON ord.current_state = osl.id_order_state
                LEFT JOIN `" . _DB_PREFIX_ . "order_detail` od ON ord.id_order = od.id_order
                WHERE CASE WHEN osl.id_lang > 0 THEN osl.id_lang = " . (int)$default_lang . " ELSE TRUE END AND reward.`deleted` = 0 AND reward.id_shop = " . (int)$context->shop->id . " $filter_where";
            $total_result = (int)Db::getInstance()->getValue($sql_total);
            if ($total_result) {
                $total_page = ceil($total_result / $limit);
                if ($page > $total_page) {
                    $page = $total_page;
                }
                $offset = ($page - 1) * $limit;
                $sql = "SELECT max_id, p.id_product as id_product, pl.name as product_name, pl.link_rewrite as link_rewrite, 
                SUM(od.product_quantity) as total_sold, 
                SUM(od.total_price_tax_incl/currency.conversion_rate) as sales
                FROM  `" . _DB_PREFIX_ . "orders` ord
                JOIN `" . _DB_PREFIX_ . "ets_am_reward` reward ON (reward.id_order = ord.id_order)
                JOIN (SELECT id_order,MAX(id_ets_am_reward) AS max_id
                        FROM
                            `" . _DB_PREFIX_ . "ets_am_reward`                         GROUP BY
                                id_order
                        ) r ON (
                            r.id_order = reward.id_order
                            AND reward.id_ets_am_reward = r.max_id
                        )
                LEFT JOIN `" . _DB_PREFIX_ . "customer` customer ON reward.id_customer = customer.id_customer
                LEFT JOIN `" . _DB_PREFIX_ . "currency` currency ON ord.id_currency = currency.id_currency
                LEFT JOIN `" . _DB_PREFIX_ . "order_state_lang` osl ON ord.current_state = osl.id_order_state
                LEFT JOIN `" . _DB_PREFIX_ . "order_detail` od ON ord.id_order = od.id_order
                LEFT JOIN `" . _DB_PREFIX_ . "product` p ON od.product_id = p.id_product
                LEFT JOIN `" . _DB_PREFIX_ . "product_lang` pl ON p.id_product = pl.id_product
                WHERE CASE WHEN osl.id_lang > 0 THEN osl.id_lang = " . (int)$default_lang . " ELSE TRUE END AND  reward.id_shop = " . (int)$context->shop->id . " AND pl.id_lang = " . (int)$default_lang . "  AND  pl.id_shop = " . (int)$context->shop->id . " $filter_where
                GROUP BY p.id_product
                ORDER BY total_sold DESC
                LIMIT $offset, $limit";
                $results = Db::getInstance()->executeS($sql);
                foreach ($results as &$result) {
                    $result['sales'] = Ets_affiliatemarketing::displayPrice($result['sales'], (int)Configuration::get('PS_CURRENCY_DEFAULT'));
                    $image = Image::getCover((int)$result['id_product']);
                    if($image)
                    {
                        $result['product_image'] = $context->link->getImageLink($result['link_rewrite'], $image['id_image']);
                    }
                    else
                        $result['product_image'] ='';
                    $product = new Product((int)$result['id_product']);
                    $result['link_product'] = Ets_Affiliate::generateAffiliateLinkForProduct($product, $context, false);
                }
                return array(
                    'total_result' => $total_result,
                    'total_page' => $total_page,
                    'per_page' => $limit,
                    'results' => $results,
                    'current_page' => $page,
                );
            } else {
                return array(
                    'total_result' => 0,
                    'total_page' => 1,
                    'per_page' => 10,
                    'results' => array(),
                    'current_page' => 1,
                );
            }
        } elseif ($type == 'top_sponsor') {
            $sql_total = "SELECT COUNT(DISTINCT reward.id_customer) AS total
                    FROM `" . _DB_PREFIX_ . "ets_am_reward`  reward
                LEFT JOIN `" . _DB_PREFIX_ . "orders` ord ON (reward.id_order = ord.id_order OR IFNULL(reward.id_order, 0) = ord.id_order)
                LEFT JOIN `" . _DB_PREFIX_ . "order_state_lang` osl ON ord.current_state = osl.id_order_state
                LEFT JOIN `" . _DB_PREFIX_ . "ets_am_sponsor` sponsor ON reward.id_customer = sponsor.id_parent
                WHERE CASE WHEN osl.id_lang > 0 THEN osl.id_lang = " . (int)$default_lang . " ELSE TRUE END AND reward.program = 'ref' AND sponsor.level = 1 AND reward.`deleted` = 0 AND reward.id_shop = " . (int)$context->shop->id . " $filter_where";
            $total_result = (int)Db::getInstance()->getValue($sql_total);
            if ($total_result) {
                $total_page = ceil($total_result / $limit);
                if ($page > $total_page) {
                    $page = $total_page;
                }
                $offset = ($page - 1) * $limit;
                $sql = "SELECT reward.id_customer as id_customer, CONCAT(customer.firstname, ' ',customer.lastname) AS username,sponsor.id_parent,
                            COUNT(DISTINCT sponsor.id_customer) AS total_friend,
                             COUNT(DISTINCT IF (reward.id_order > 0, reward.id_order, null)) as total_order,
                            (SELECT SUM(ord.total_paid_tax_incl / currency.conversion_rate) as total_sale
                                        FROM `" . _DB_PREFIX_ . "ets_am_reward` rw
                                         JOIN (SELECT id_order,MAX(id_ets_am_reward) AS max_id
                                                FROM
                                                        `" . _DB_PREFIX_ . "ets_am_reward`                                                 GROUP BY
                                                                id_order
                                                ) r ON (
                                                        r.id_order = rw.id_order
                                                        AND rw.id_ets_am_reward = r.max_id
                                                )
                                        JOIN `" . _DB_PREFIX_ . "orders` ord ON rw.id_order = ord.id_order
                                        LEFT JOIN `" . _DB_PREFIX_ . "currency` currency ON ord.id_currency = currency.id_currency
                                WHERE rw.id_customer = sponsor.id_parent
                            ) total_sale,
                            (SELECT SUM(amount) FROM `" . _DB_PREFIX_ . "ets_am_reward` r WHERE id_customer = reward.id_customer AND program = 'ref' AND IF( $filter_reward_status = 1, status = reward.status, 1) AND $filter_time) as total_point
                            FROM `" . _DB_PREFIX_ . "ets_am_reward` reward
                            LEFT JOIN `" . _DB_PREFIX_ . "orders` ord ON (reward.id_order = ord.id_order)

                            LEFT JOIN `" . _DB_PREFIX_ . "customer` customer ON reward.id_customer = customer.id_customer
                            JOIN `" . _DB_PREFIX_ . "ets_am_sponsor` sponsor ON reward.id_customer = sponsor.id_parent
                            WHERE  reward.id_shop = " . (int)$context->shop->id . " AND reward.program = 'ref' AND sponsor.level = 1 $filter_where
                            GROUP BY reward.id_customer
                            ORDER BY total_point DESC
                            LIMIT " . (int)$offset . ", " . (int)$limit;
                $results = Db::getInstance()->executeS($sql);
                foreach ($results as &$result) {
                    $text_friends = '';
                    if ($result['total_friend']) {
                        $sql = 'SELECT s.level,count(DISTINCT s.id_customer) as total_customer FROM `' . _DB_PREFIX_ . 'ets_am_sponsor` s WHERE s.id_parent = "' . (int)$result['id_parent'] . '" GROUP BY level';
                    }
                    $friends = Db::getInstance()->executeS($sql);
                    if ($friends) {
                        foreach ($friends as $friend) {
                            if ($friend['total_customer']) {
                                $text_friends .= $trans['level'] . ' ' . $friend['level'] . ': ' . $friend['total_customer'] . EtsAffDefine::displayText('', 'br');
                            }
                        }
                    }
                    if ($result['total_friend']) {
                        $sql = 'SELECT s.level,count(DISTINCT s.id_customer) as total_customer,count(DISTINCT reward.id_order) as total_order, SUM(ord.total_paid_tax_incl / c.conversion_rate) as total_sale,SUM(reward.amount) as total_point FROM `' . _DB_PREFIX_ . 'ets_am_sponsor` s,' . _DB_PREFIX_ . 'ets_am_reward reward,' . _DB_PREFIX_ . 'orders ord,' . _DB_PREFIX_ . 'currency c  WHERE ord.id_order = reward.id_order AND ord.id_currency= c.id_currency AND s.id_customer=reward.id_friend AND reward.program="ref" AND s.id_parent = "' . (int)$result['id_parent'] . '" ' . $filter_where . ' GROUP BY level';
                    }
                    $friends = Db::getInstance()->executeS($sql);
                    $text_orders = '';
                    $text_sales = '';
                    $text_points = '';
                    if ($friends) {
                        foreach ($friends as $friend) {
                            if ($friend['total_order']) {
                                $text_orders .= $trans['level'] . ' ' . $friend['level'] . ': ' . $friend['total_order'] . EtsAffDefine::displayText('', 'br');
                            }
                            if ($friend['total_sale']) {
                                $text_sales .= $trans['level'] . ' ' . $friend['level'] . ': ' . Ets_affiliatemarketing::displayPrice($friend['total_sale'], (int)Configuration::get('PS_CURRENCY_DEFAULT')) . EtsAffDefine::displayText('', 'br');
                            }
                            if ($friend['total_point']) {
                                $text_points .= $trans['level'] . ' ' . $friend['level'] . ': ' . Ets_AM::displayRewardAdmin($friend['total_point'], true) . EtsAffDefine::displayText('', 'br');
                            }
                        }
                    }
                    $result['total_friend'] = $text_friends;
                    $result['total_order'] = $text_orders;
                    $result['total_point'] = $text_points;// Ets_AM::displayRewardAdmin((float)$result['total_point']);
                    $result['total_sale'] = $text_sales;
                }
                return array(
                    'total_result' => $total_result,
                    'total_page' => $total_page,
                    'per_page' => $limit,
                    'results' => $results,
                    'current_page' => $page,
                );
            } else {
                return array(
                    'total_result' => 0,
                    'total_page' => 1,
                    'per_page' => $limit,
                    'results' => array(),
                    'current_page' => 1,
                );
            }
        } elseif ($type == 'top_affiliate') {
            $sql_total = "SELECT COUNT(DISTINCT reward.id_customer) AS total
                    FROM `" . _DB_PREFIX_ . "ets_am_reward`  reward
                LEFT JOIN `" . _DB_PREFIX_ . "orders` ord ON (reward.id_order = ord.id_order OR IFNULL(reward.id_order, 0) = ord.id_order)
                LEFT JOIN `" . _DB_PREFIX_ . "order_state_lang` osl ON ord.current_state = osl.id_order_state
                WHERE CASE WHEN osl.id_lang > 0 THEN osl.id_lang = " . (int)$default_lang . " ELSE TRUE END AND reward.program = 'aff' AND reward.`deleted` = 0 AND reward.id_shop = " . (int)$context->shop->id . " $filter_where";
            $total_result = (int)Db::getInstance()->getValue($sql_total);
            if ($total_result) {
                $total_page = ceil($total_result / $limit);
                if ($page > $total_page) {
                    $page = $total_page;
                }
                $offset = ($page - 1) * $limit;
                $incl_product = null;
                $excl_product = null;
                $incl_array = array();
                $excl_array = array();
                if ($incl = Configuration::get('ETS_AM_AFF_SPECIFIC_PRODUCTS')) {
                    $incl_product = $incl;
                    $incl_array = explode(',', $incl_product);
                }
                if ($excl = Configuration::get('ETS_AM_AFF_PRODUCTS_EXCLUDED')) {
                    $excl_product = $excl;
                    $excl_array = explode(',', $excl_product);
                }
                if (Configuration::get('ETS_AM_AFF_PRODUCTS_EXCLUDED_DISCOUNT')) {
                    $sql = 'SELECT id_product FROM `' . _DB_PREFIX_ . 'specific_price` WHERE (`from` = "0000-00-00 00:00:00" OR `from` <="' . pSQL(date('Y-m-d H:i:s')) . '" ) AND (`to` = "0000-00-00 00:00:00" OR `to` >="' . pSQL(date('Y-m-d H:i:s')) . '" )';
                    $products = Db::getInstance()->executeS($sql);
                    if ($products) {
                        foreach ($products as $product)
                            $excl_array[] = $product['id_product'];
                    }
                }
                if (empty($incl_array) && empty($excl_array)) {
                    $intersect = array_intersect($incl_array, $excl_array);
                    if (count($intersect) > 0) {
                        foreach ($excl_array as $key => $value) {
                            foreach ($intersect as $it) {
                                if ($value == $it) {
                                    unset($excl_array[$key]);
                                }
                            }
                        }
                        $excl_product = $excl_array ? implode(',', $excl_array):'';
                    }
                }
                $where_sale = "";
                if ($incl_product) {
                    $where_sale .= "AND product_id IN (" . pSQL($incl_product) . ")";
                }
                if ($excl_product) {
                    $where_sale .= "AND product_id NOT IN (" . pSQL($excl_product) . ")";
                }
                $sql = "SELECT reward.id_customer, CONCAT(customer.firstname, ' ',customer.lastname) AS username,
                         COUNT(DISTINCT IF (reward.id_order > 0, reward.id_order, null)) as total_orders,
                        (SELECT SUM(total_price_tax_incl / currency.conversion_rate) FROM `" . _DB_PREFIX_ . "order_detail` WHERE id_order = ord.id_order $where_sale) total_sale,
                        (SELECT SUM(amount) FROM `" . _DB_PREFIX_ . "ets_am_reward` r WHERE id_customer = reward.id_customer AND program = 'aff' AND $filter_time  AND IF( $filter_reward_status = 1, status = reward.status, 1)) as total_point
                        FROM `" . _DB_PREFIX_ . "ets_am_reward` reward
                        LEFT JOIN `" . _DB_PREFIX_ . "orders` ord ON (reward.id_order = ord.id_order)
                        LEFT JOIN `" . _DB_PREFIX_ . "customer` customer ON reward.id_customer = customer.id_customer
                        LEFT JOIN `" . _DB_PREFIX_ . "currency` currency ON ord.id_currency = currency.id_currency
                        LEFT JOIN `" . _DB_PREFIX_ . "order_state_lang` osl ON ord.current_state = osl.id_order_state
                        WHERE CASE WHEN osl.id_lang > 0 THEN osl.id_lang = " . (int)$default_lang . " ELSE TRUE END AND reward.program = 'aff' AND reward.id_shop = " . (int)$context->shop->id . " $filter_where
                    GROUP BY reward.id_customer
                    ORDER BY total_point DESC
                    LIMIT " . (int)$offset . ", " . (int)$limit;
                $results = Db::getInstance()->executeS($sql);
                foreach ($results as &$result) {
                    $total_sale = Db::getInstance()->getValue('SELECT sum(od.total_price_tax_incl/currency.conversion_rate) FROM `' . _DB_PREFIX_ . 'order_detail` od
                    INNER JOIN `' . _DB_PREFIX_ . 'orders` ord ON (ord.id_order=od.id_order)
                    INNER JOIN `' . _DB_PREFIX_ . 'ets_am_reward` reward ON (reward.id_order=ord.id_order)
                    INNER JOIN `' . _DB_PREFIX_ . 'ets_am_reward_product` rp ON (rp.id_product=od.product_id AND rp.id_ets_am_reward= reward.id_ets_am_reward)
                    LEFT JOIN `' . _DB_PREFIX_ . 'currency` currency ON ord.id_currency = currency.id_currency
                    WHERE reward.program="aff" AND reward.id_customer = "' . (int)$result['id_customer'] . '" ' . (string)$where_sale);
                    $result['total_point'] = Ets_AM::displayRewardAdmin((float)$result['total_point']);
                    $result['total_sale'] = Ets_affiliatemarketing::displayPrice((float)$total_sale, (int)Configuration::get('PS_CURRENCY_DEFAULT'));
                }
                return array(
                    'total_result' => $total_result,
                    'total_page' => $total_page,
                    'per_page' => $limit,
                    'results' => $results,
                    'current_page' => $page,
                );
            } else {
                return array(
                    'total_result' => 0,
                    'total_page' => 1,
                    'per_page' => 10,
                    'results' => array(),
                    'current_page' => 1,
                );
            }
        } elseif ($type == 'top_customer') {
            $sql_total = "SELECT COUNT(DISTINCT reward.id_customer) AS total
                    FROM `" . _DB_PREFIX_ . "ets_am_reward`  reward
                LEFT JOIN `" . _DB_PREFIX_ . "orders` ord ON (reward.id_order = ord.id_order OR IFNULL(reward.id_order, 0) = ord.id_order)
                LEFT JOIN `" . _DB_PREFIX_ . "order_state_lang` osl ON ord.current_state = osl.id_order_state
                WHERE CASE WHEN osl.id_lang > 0 THEN osl.id_lang = " . (int)$default_lang . " ELSE TRUE END AND reward.program = 'loy' AND reward.`deleted` = 0 AND reward.id_shop = " . (int)$context->shop->id . " $filter_where";
            $total_result = (int)Db::getInstance()->getValue($sql_total);
            if ($total_result) {
                $total_page = ceil($total_result / $limit);
                if ($page > $total_page) {
                    $page = $total_page;
                }
                $offset = ($page - 1) * $limit;
                $incl_product = null;
                $excl_product = null;
                $incl_array = array();
                $excl_array = array();
                if ($incl = Configuration::get('ETS_AM_LOYALTY_SPECIFIC')) {
                    $incl_product = $incl;
                    $incl_array = explode($incl_product, ',');
                }
                if ($excl = Configuration::get('ETS_AM_LOYALTY_EXCLUDED_SEARCH')) {
                    $excl_product = $excl;
                    $excl_array = explode($excl_array, ',');
                }
                if (empty($incl_array) && empty($excl_array)) {
                    $intersect = array_intersect($incl_array, $excl_array);
                    if (count($intersect) > 0) {
                        foreach ($excl_array as $key => $value) {
                            foreach ($intersect as $it) {
                                if ($value == $it) {
                                    unset($excl_array[$key]);
                                }
                            }
                        }
                        $excl_product = $excl_array ? implode(',', $excl_array):'';
                    }
                }
                $where_sale = "";
                if ($incl_product) {
                    $where_sale .= "AND product_id IN (" . pSQL($incl_product) . ")";
                }
                if ($excl_product) {
                    $where_sale .= "AND product_id NOT IN (" . pSQL($excl_product) . ")";
                }
                $sql = "SELECT reward.id_customer, CONCAT(customer.firstname, ' ',customer.lastname) AS username,
                         COUNT(DISTINCT IF (reward.id_order > 0, reward.id_order, null)) as total_order,
                        (SELECT SUM(total_price_tax_incl / currency.conversion_rate) FROM `" . _DB_PREFIX_ . "order_detail` WHERE id_order = ord.id_order $where_sale) total_sale,
                        (SELECT SUM(amount) FROM `" . _DB_PREFIX_ . "ets_am_reward` r WHERE id_customer = reward.id_customer AND program = 'loy' AND $filter_time  AND IF( $filter_reward_status = 1, status = reward.status, 1)) as total_point
                        FROM `" . _DB_PREFIX_ . "ets_am_reward` reward
                        LEFT JOIN `" . _DB_PREFIX_ . "orders` ord ON (reward.id_order = ord.id_order)
                        LEFT JOIN `" . _DB_PREFIX_ . "customer` customer ON reward.id_customer = customer.id_customer
                        LEFT JOIN `" . _DB_PREFIX_ . "currency` currency ON ord.id_currency = currency.id_currency
                        LEFT JOIN `" . _DB_PREFIX_ . "order_state_lang` osl ON ord.current_state = osl.id_order_state
                         WHERE CASE WHEN osl.id_lang > 0 THEN osl.id_lang = " . (int)$default_lang . " ELSE TRUE END AND reward.program = 'loy' AND reward.id_shop = " . (int)$context->shop->id . " $filter_where
                    GROUP BY reward.id_customer
                    ORDER BY total_point DESC
                    LIMIT $offset, $limit";
                $results = Db::getInstance()->executeS($sql);
                foreach ($results as &$result) {
                    $total_sale = Db::getInstance()->getValue('SELECT sum(ord.total_paid_tax_incl/currency.conversion_rate) FROM `' . _DB_PREFIX_ . 'orders` ord
                    INNER JOIN `' . _DB_PREFIX_ . 'ets_am_reward` reward ON (reward.id_order=ord.id_order)
                    INNER JOIN `'._DB_PREFIX_.'order_detail` od ON (od.id_order=ord.id_order)
                    LEFT JOIN `' . _DB_PREFIX_ . 'currency` currency ON ord.id_currency = currency.id_currency
                    WHERE reward.program="loy" AND reward.id_customer = "' . (int)$result['id_customer'] . '" ' . $where_sale);
                    $result['total_sale'] = Ets_affiliatemarketing::displayPrice((float)$total_sale, (int)Configuration::get('PS_CURRENCY_DEFAULT'));
                    $result['total_point'] = Ets_AM::displayRewardAdmin((float)$result['total_point']);
                }
            } else {
                $results = array();
            }
            return array(
                'total_result' => $total_result,
                'total_page' => $total_page,
                'per_page' => $limit,
                'results' => $results,
                'current_page' => $page,
            );
        } elseif ($type == 'top_reward_accounts') {
            $sql_total = "SELECT COUNT(DISTINCT reward.id_customer) AS total
                    FROM `" . _DB_PREFIX_ . "ets_am_reward`  reward
                LEFT JOIN `" . _DB_PREFIX_ . "orders` ord ON (reward.id_order = ord.id_order OR IFNULL(reward.id_order, 0) = ord.id_order)
                LEFT JOIN `" . _DB_PREFIX_ . "order_state_lang` osl ON ord.current_state = osl.id_order_state
                WHERE CASE WHEN osl.id_lang > 0 THEN osl.id_lang = " . (int)$default_lang . " ELSE TRUE END AND reward.`deleted` = 0 AND reward.id_shop = " . (int)$context->shop->id . " $filter_where";
            $total_result = (int)Db::getInstance()->getValue($sql_total);
            if ($total_result) {
                $total_page = ceil($total_result / $limit);
                if ($page > $total_page) {
                    $page = $total_page;
                }
                $offset = ($page - 1) * $limit;
                $sql = "SELECT reward.id_customer as id_customer, 
                CONCAT(customer.firstname, ' ',customer.lastname) AS username, 
                SUM(IF(reward.program = 'loy', reward.amount, 0)) as loy_reward, 
                SUM(IF(reward.program = 'ref', reward.amount, 0)) as ref_reward, 
                SUM(IF(reward.program = 'aff', reward.amount, 0)) as aff_reward,
                SUM(IF(reward.program = 'mnu', reward.amount, 0)) as mnu_reward,                                
                (SELECT COALESCE(SUM(amount), 0) FROM `" . _DB_PREFIX_ . "ets_am_reward` WHERE status = 1 AND deleted = 0 AND id_customer = reward.id_customer) as total_point,
                (SELECT COALESCE(SUM(amount), 0) FROM `" . _DB_PREFIX_ . "ets_am_reward_usage` WHERE status =1 AND deleted = 0 AND id_customer = reward.id_customer ) as total_usage
                    FROM `" . _DB_PREFIX_ . "ets_am_reward` reward
                LEFT JOIN `" . _DB_PREFIX_ . "customer` customer ON reward.id_customer = customer.id_customer
                WHERE  reward.id_shop = " . (int)$context->shop->id . " AND reward.deleted = 0  $filter_where
                GROUP BY reward.id_customer
                ORDER BY total_point DESC
                LIMIT " . (int)$offset . ", " . (int)$limit;
                $results = Db::getInstance()->executeS($sql);
                if ($results) {
                    for ($i = 0; $i <= count($results) - 2; $i++)
                        for ($j = $i + 1; $j <= count($results) - 1; $j++) {
                            $total1 = $results[$i]['ref_reward'] + $results[$i]['loy_reward'] + $results[$i]['aff_reward'] + $results[$i]['mnu_reward'];
                            $total2 = $results[$j]['ref_reward'] + $results[$j]['loy_reward'] + $results[$j]['aff_reward'] + $results[$j]['mnu_reward'];
                            if ($total2 > $total1) {
                                $temp = $results[$i];
                                $results[$i] = $results[$j];
                                $results[$j] = $temp;
                            }
                        }
                    foreach ($results as &$result) {
                        $result['reward_balance'] = Ets_AM::displayRewardAdmin((float)$result['total_point'] - (float)$result['total_usage']);
                        $result['ref_reward'] = Ets_AM::displayRewardAdmin((float)$result['ref_reward']);
                        $result['loy_reward'] = Ets_AM::displayRewardAdmin((float)$result['loy_reward']);
                        $result['aff_reward'] = Ets_AM::displayRewardAdmin((float)$result['aff_reward']);
                        $result['mnu_reward'] = Ets_AM::displayRewardAdmin((float)$result['mnu_reward']);
                    }
                }
                return array(
                    'total_result' => $total_result,
                    'total_page' => $total_page,
                    'per_page' => $limit,
                    'results' => $results,
                    'current_page' => $page,
                );
            } else {
                return array(
                    'total_result' => 0,
                    'total_page' => 1,
                    'per_page' => 10,
                    'results' => array(),
                    'current_page' => 1,
                );
            }
        }
        return array(
            'total_result' => 0,
            'total_page' => 1,
            'per_page' => 10,
            'results' => array(),
            'current_page' => 1,
        );
    }
    /**
     * @param null $context
     * @return bool
     */
    public static function needExchange($context = null)
    {
        if (!$context) {
            $context = Context::getContext();
        }
        if ($context->currency->id != (int)Configuration::get('PS_CURRENCY_DEFAULT')) {
            return true;
        }
        return false;
    }
    /**
     * @param string $controller
     * @param array $params
     * @return string
     */
    public static function getBaseUrlDefault($controller = '', $params = array())
    {
        $link = Context::getContext()->link->getModuleLink('ets_affiliatemarketing', $controller, $params);
        return $link;
    }
    /**
     * @param $amount
     * @param bool $with_currency
     * @return float|int|string
     */
    public static function displayRewardAdmin($amount, $with_currency = true, $display_amount = false)
    {
        $id_lang_default = (int)Configuration::get('PS_LANG_DEFAULT');
        if (Configuration::get('ETS_AM_REWARD_DISPLAY') == 'point' && Configuration::get('ETS_AM_REWARD_DISPLAY_BO') == 'point') {
            if (Configuration::get('ETS_AM_CONVERSION') !== false && Configuration::get('ETS_AM_CONVERSION') !== '' && Configuration::get('ETS_AM_CONVERSION') !== NULL) {
                $point = Tools::ps_round($amount, _PS_PRICE_COMPUTE_PRECISION_ ?: 2) * (float)Configuration::get('ETS_AM_CONVERSION');
                $point_num = Tools::ps_round($point, (_PS_PRICE_COMPUTE_PRECISION_ ?: 2));
                if ($with_currency) {
                    return $point_num . ' ' . Configuration::get('ETS_AM_REWARD_UNIT_LABEL', $id_lang_default) . ($display_amount ? ' (' . Ets_affiliatemarketing::displayPrice($amount, (int)Configuration::get('PS_CURRENCY_DEFAULT')) . ')' : '');
                }
                return $point_num . ($display_amount ? ' (' . Ets_affiliatemarketing::displayPrice($amount, (int)Configuration::get('PS_CURRENCY_DEFAULT')) . ')' : '');
            }
            return '--';
        } else {
            if ($with_currency) {
                return Ets_affiliatemarketing::displayPrice($amount, (int)Configuration::get('PS_CURRENCY_DEFAULT'));
            }
            return (float)$amount;
        }
    }
    /**
     * @return bool|int
     */
    public static function usingCustomUnit()
    {
        if (Configuration::get('ETS_AM_REWARD_DISPLAY') == 'point' && Configuration::get('ETS_AM_REWARD_DISPLAY_BO') == 'point') {
            return (float)Configuration::get('ETS_AM_CONVERSION');
        }
        return false;
    }
    /**
     * @param $id_product
     * @param string $program
     * @param null $context
     * @return array
     * @throws PrestaShopDatabaseException
     */
    public static function getProductStats($id_product, $program = 'aff', $params = array())
    {
        $context = Context::getContext();
        $type = isset($params['statistic']) && ($type = Tools::strtoupper((string)$params['statistic'])) && in_array($type, array('TURNOVER', 'ORDERS', 'VIEWS', 'REWARDS', 'CONVERSION_RATE', 'NET_PROFIT')) ? $type : "TURNOVER";
        $time_frame = isset($params['time_frame']) && ($time_frame = Tools::strtolower((string)$params['time_frame'])) && in_array($time_frame, array('this_year', 'all_times')) ? $time_frame : "all_times";
        $date_from = isset($params['date_from']) && Validate::isDate($params['date_from']) ? $params['date_from'] : false;
        $date_to = isset($params['date_to']) && Validate::isDate($params['date_to']) ? $params['date_to'] : false;
        $start_date = date('Y-m-01 00:00:00');
        $end_date = date('Y-m-t 23:59:59');
        $time_group = "DATE(rp.datetime_added)";
        $time_select = "DATE(rp.datetime_added)";
        $by_month = false;
        $by_year = false;
        $select = '';
        $trans = Module::getInstanceByName('ets_affiliatemarketing')->getTranslates();
        switch ($type) {
            case "TURNOVER":
                $select = " SUM(ord.total_paid / currency.conversion_rate) ";
                $line_color = '#1777B6';
                $line_x_label = $trans['turnover'];
                break;
            case "ORDERS":
                $select = "COUNT(DISTINCT rp.id_order)";
                $line_color = '#E61409';
                $line_x_label = $trans['total_order'];
                break;
            case 'VIEWS':
                $select = "SUM(DISTINCT v.count)";
                $line_color = '#ff6600';
                $line_x_label = $trans['total_view'];
                break;
            case 'REWARDS':
                $select = "SUM(rp.quantity * rp.amount)";
                $line_color = '#2ca121';
                $line_x_label = $trans['earning_reward'];
                break;
            case 'CONVERSION_RATE':
                $select = "(COUNT(DISTINCT rp.id_order) / SUM(DISTINCT v.count))";
                $line_color = '#ff3399';
                $line_x_label = $trans['conversion_rate'];
                break;
            case 'NET_PROFIT':
                $mp = -1;
                $select = "((SUM(ord.total_paid / currency.conversion_rate) * " . ($mp / 100) . ") - SUM(rp.quantity * rp.amount))";
                $line_color = '#ff6600';
                $line_x_label = $trans['net_profit'];
                break;
        }
        $select .= ' AS total_score ';
        $sql_part = "FROM `" . _DB_PREFIX_ . "ets_am_reward_product` rp LEFT JOIN `" . _DB_PREFIX_ . "ets_am_product_view` v ON rp.id_product = v.id_product LEFT JOIN `" . _DB_PREFIX_ . "orders` ord on rp.id_order = ord.id_order LEFT JOIN `" . _DB_PREFIX_ . "currency` currency ON ord.id_currency = currency.id_currency WHERE rp.id_product = " . (int)$id_product . " AND rp.id_seller = " . (int)$context->customer->id . " AND rp.program =  '" . pSQL($program) . "'";
        if ($date_from && $date_to) {
            $start_date = $date_from . ' 00:00:00';
            $end_date = $date_to . ' 23:59:59';
            if (date('Y', strtotime($start_date)) != date('Y', strtotime($end_date))) {
                $by_year = true;
                $time_group = "YEAR(rp.datetime_added)";
                $time_select = "YEAR(rp.datetime_added)";
            } else if ((int)date('m', strtotime($start_date)) != (int)date('m', strtotime($end_date))) {
                $by_month = true;
                $time_group = "MONTH(rp.datetime_added), YEAR(rp.datetime_added)";
                $time_select = "DATE_FORMAT(rp.datetime_added, '%Y-%m')";
            }
        } elseif ($time_frame == 'this_year') {
            $start_date = date('Y-01-01 00:00:00');
            $end_date = date('Y-m-t 00:01:00', strtotime(date('Y-12-01')));
            $by_month = true;
            $time_group = "MONTH(rp.datetime_added), YEAR(rp.datetime_added)";
            $time_select = "DATE_FORMAT(rp.datetime_added, '%Y-%m')";
        } elseif ($time_frame == 'all_times') {
            $max_time = Db::getInstance()->getValue("SELECT MAX(`datetime_added`) FROM " . _DB_PREFIX_ . "ets_am_reward_product");
            $min_time = Db::getInstance()->getValue("SELECT MIN(`datetime_added`) FROM " . _DB_PREFIX_ . "ets_am_reward_product");
            $start_date = $min_time;
            $end_date = $max_time;
            if (date('Y', strtotime($max_time)) != date('Y', strtotime($min_time))) {
                $by_year = true;
                $time_group = "YEAR(rp.datetime_added)";
                $time_select = "YEAR(rp.datetime_added)";
            } else if (date('m', strtotime($max_time)) != date('m', strtotime($min_time))) {
                $by_month = true;
                $time_group = "MONTH(rp.datetime_added), YEAR(rp.datetime_added)";
                $time_select = "DATE_FORMAT(rp.datetime_added, '%Y-%m')";
            }
        }
        $select .= ', ' . $time_select . 'as date_added ';
        $sql_part .= " AND rp.datetime_added >= '" . pSQL($start_date) . "' AND rp.datetime_added <= '" . pSQL($end_date) . "'";
        $sql_part .= " GROUP BY " . (string)$time_group;
        $sql = "SELECT " . (string)$select . (string)$sql_part;
        $results = Db::getInstance()->executeS($sql);
        if ($by_month) {
            $data_date = self::getDateRanger($start_date, $end_date, 'Y-m-01', true, 'month');
        } else if ($by_year) {
            $data_date = self::getYearRanger($start_date, $end_date, 'Y-01-01', true);
        } else {
            $data_date = self::getDateRanger($start_date, $end_date, 'Y-m-d', true, 'date');
        }
        $total_score = $data_date;
        $values = array();
        $total_score_data = array();
        if ($results) {
            foreach ($results as &$result) {
                if ($result['total_score'] == null) {
                    $result['total_score'] = 0;
                }
                if ($type == 'CONVERSION_RATE') {
                    $result['total_score'] = (float)$result['total_score'] * 100;
                }
                if ($by_month) {
                    $key_data = $result['date_added'] . '-01';
                } elseif ($by_year) {
                    $key_data = $result['date_added'] . '-01-01';
                } else {
                    $key_data = $result['date_added'];
                }
                if ($type == 'REWARDS') {
                    $total_score[$key_data] = Ets_AM::displayReward((float)$result['total_score'], false);
                } else {
                    $total_score[$key_data] = (float)$result['total_score'];
                }
            }
            foreach ($total_score as $date => $data) {
                if ($data) {
                }
                $total_score_data[] = array(
                    'x' => strtotime($date) * 1000,
                    'y' => $total_score[$date],
                );
            }
        } else {
            foreach ($total_score as $date => $data) {
                if ($data) {
                }
                $total_score_data[] = array(
                    'x' => strtotime($date) * 1000,
                    'y' => 0,
                );
            }
        }
        if ($by_month) {
            $values['x_asis'] = 'month';
        } elseif ($by_year) {
            $values['x_asis'] = 'year';
        } else {
            $values['x_asis'] = 'date';
        }
        $values['data'] = array(
            array(
                'key' => $line_x_label,
                'values' => $total_score_data,
                'color' => $line_color,
                'area' => 1
            ),
        );
        $values['type'] = $type;
        return $values;
    }
    public static function getProductSaleCount($id_product, $program = 'aff', $params = array())
    {
        $context = Context::getContext();
        $time_frame = isset($params['time_frame']) && ($time_frame = Tools::strtolower((string)$params['time_frame'])) && in_array($time_frame, array('this_year', 'all_times')) ? $time_frame : "all_times";
        $time_group = "DATE(rp.datetime_added)";
        if ($time_frame == 'this_year') {
            $start_date = date('Y-01-01 00:00:00');
            $end_date = date('Y-m-t 00:01:00', strtotime(date('Y-12-01')));
            $time_group = "MONTH(rp.datetime_added), YEAR(rp.datetime_added)";
        } elseif ($time_frame == 'all_times') {
            $max_time = Db::getInstance()->getValue("SELECT MAX(`datetime_added`) FROM " . _DB_PREFIX_ . "ets_am_reward_product");
            $min_time = Db::getInstance()->getValue("SELECT MIN(`datetime_added`) FROM " . _DB_PREFIX_ . "ets_am_reward_product");
            $start_date = $min_time;
            $end_date = $max_time;
            if (date('Y', strtotime($max_time)) != date('Y', strtotime($min_time))) {
                $time_group = "YEAR(rp.datetime_added)";
            } else if (date('m', strtotime($max_time)) != date('m', strtotime($min_time))) {
                $time_group = "MONTH(rp.datetime_added), YEAR(rp.datetime_added)";
            }
        }
        $mp = -1;
        $sql = "SELECT SUM(ord.total_paid / currency.conversion_rate) as turnover, COUNT(DISTINCT rp.id_order) as total_order, SUM(rp.quantity * rp.amount) as total_earn, IF(COUNT(rp.id_order), IF(SUM(v.count), IF(COUNT(rp.id_order) > SUM(v.count), COUNT(rp.id_order), SUM(v.count)), COUNT(rp.id_order)), SUM(DISTINCT v.count)) as view_count, rp.amount as reward_per_product, SUM(rp.quantity) * rp.amount as earning_rewards, IF(COUNT(rp.id_order), COUNT(rp.id_order) / IF(COUNT(rp.id_order), IF(SUM(v.count), IF(COUNT(rp.id_order) > SUM(v.count), COUNT(rp.id_order), SUM(v.count)), COUNT(rp.id_order)), SUM(v.count)),  null) as conversion_rate,
       ((SUM(ord.total_paid / currency.conversion_rate) * ($mp / 100)) - SUM(rp.quantity * rp.amount)) as net_profit
            FROM `" . _DB_PREFIX_ . "ets_am_reward_product` rp LEFT JOIN `" . _DB_PREFIX_ . "ets_am_product_view` v ON rp.id_product = v.id_product LEFT JOIN `" . _DB_PREFIX_ . "orders` ord on rp.id_order = ord.id_order INNER JOIN `" . _DB_PREFIX_ . "currency` currency ON ord.id_currency = currency.id_currency WHERE rp.id_product = " . (int)$id_product . " AND rp.program = '" . pSQL($program) . "' AND rp.id_seller = " . (int)$context->customer->id;
        $sql .= " AND rp.datetime_added >= '" . pSQL($start_date) . "' AND rp.datetime_added <= '" . pSQL($end_date) . "'";
        $sql .= " GROUP BY " . (string)$time_group;
        $results = Db::getInstance()->getRow($sql);
        if ($results && count($results)) {
            $results['turnover'] = Ets_AM::displayPriceOnly($results['turnover']);
            $results['total_earn'] = Ets_AM::displayReward($results['total_earn']);
            $results['net_profit'] = $mp >= 0 ? Ets_AM::displayPriceOnly($results['net_profit']) : Ets_AM::displayPriceOnly(0.00);
            $results['conversion_rate'] = (float)$results['conversion_rate'] * 100 . "%";
            if (!$results['view_count']) {
                $results['view_count'] = 0;
            }
        } else {
            $results = array(
                "turnover" => 0,
                "total_order" => 0,
                "view_count" => 0,
                "total_earn" => 0,
                "conversion_rate" => '0%',
                "net_profit" => 0,
            );
        }
        return $results;
    }
    /**
     * @param $amount
     * @param null $context
     * @return string
     */
    public static function displayPriceOnly($amount, $context = null)
    {
        if (!$context) {
            $context = Context::getContext();
        }
        if (self::needExchange($context)) {
            $amount = Tools::convertPrice($amount, $context->currency->id, true);
        }
        return Ets_affiliatemarketing::displayPrice($amount);
    }
    /**
     * @param $id_customer
     * @param string $program
     * @param int $status
     * @return bool
     */
    public static function isCustomerJoinedProgram($id_customer, $program, $status = 1)
    {
        $context = Context::getContext();
        if ($program == EAM_AM_LOYALTY_REWARD) {
            $config = 'ETS_AM_LOYALTY_REGISTER';
            $sql_part = ' AND `loy` = 1';
        } elseif ($program == EAM_AM_AFFILIATE_REWARD) {
            $sql_part = ' AND `aff` = 1';
            $config = 'ETS_AM_AFF_REGISTER_REQUIRED';
        } else {
            $sql_part = ' AND `ref` = 1';
            $config = 'ETS_AM_REF_REGISTER_REQUIRED';
        }
        if (Configuration::get($config)) {
            $sql = "SELECT COUNT(*) as `total` FROM `" . _DB_PREFIX_ . "ets_am_user` WHERE `id_customer` = " . (int)$id_customer . " AND `status` = '" . pSQL($status) . "' AND id_shop = " . (int)$context->shop->id;
            $sql .= $sql_part;
            $total = Db::getInstance()->getValue($sql);
            if ($total && (int)$total > 0) {
                return true;
            }
            return false;
        }
        return true;
    }
    /**
     * @param $id_customer
     * @param null $context
     * @return false|int|string|null
     */
    public static function getCustomerTotalOrder($id_customer, $context = null)
    {
        if (!$context) {
            $context = Context::getContext();
        }
        $sql = "SELECT SUM(`total_paid`) as `total` FROM `" . _DB_PREFIX_ . "orders` WHERE id_customer = " . (int)$id_customer . " AND  id_shop = " . (int)$context->shop->id . " AND `valid` = 1 ";
        $total = Db::getInstance()->getValue($sql);
        if ($total) {
            return $total;
        }
        return 0;
    }
    public static function getTotalOrder($id_customer, $context = null)
    {
        if (!$context) {
            $context = Context::getContext();
        }
        $list_states = Configuration::get('ETS_AM_VALIDATED_STATUS');
        if (!$list_states) {
            $list_states = '0';
        }
        $list_states = trim($list_states, ',');
        $total_orders = (float)Db::getInstance()->getValue("SELECT SUM(total_paid) FROM `" . _DB_PREFIX_ . "orders` WHERE current_state IN(" . implode(',', array_map('intval', explode(',', $list_states))) . ") AND id_customer = " . (int)$id_customer);
        return $total_orders;
    }
    public static function getRecentReward($limit = 5)
    {
        $rewards = Db::getInstance()->executeS("SELECT r.amount, r.id_customer, c.firstname, c.lastname, r.datetime_added, r.status 
        FROM `" . _DB_PREFIX_ . "ets_am_reward` r
            LEFT JOIN `" . _DB_PREFIX_ . "customer` c ON r.id_customer = c.id_customer
            WHERE r.deleted = 0 AND r.id_shop = '" . (int)Context::getContext()->shop->id . "'
            ORDER BY r.id_ets_am_reward DESC LIMIT " . (int)$limit);
        if ($rewards) {
            foreach ($rewards as &$reward) {
                $reward['amount'] = Ets_AM::displayRewardAdmin($reward['amount']);
                $reward['time_ago'] = self::getTimeAgo(strtotime($reward['datetime_added']))['text'];
                $reward['time_type'] = self::getTimeAgo(strtotime($reward['datetime_added']))['type'];
            }
        }
        return $rewards;
    }
    public static function getPercentReward($params = array(), $frontend = false)
    {
        $context = Context::getContext();
        $filter_where = "";
        $start_date = '';
        $end_date = '';
        if (isset($params['status']) && $params['status']!=='' && Validate::isInt($params['status'])) {
            $filter_where .= " AND r.status = " . (int)$params['status'];
        }
        if (isset($params['id_customer']) && (int)$params['id_customer']) {
            $filter_where .= " AND r.id_customer = " . (int)$params['id_customer'];
        }
        if(isset($params['date_type']))
            $params['date_type'] = Tools::strtolower($params['date_type']);
        if (!isset($params['date_type']) || !$params['date_type'] || !in_array($params['date_type'],array('this_year','all_times','time_ranger'))) {
            $distance = (int)Db::getInstance()->getValue("
                SELECT (YEAR(MAX(datetime_added)) - YEAR(MIN(datetime_added))) as `distance` FROM `" . _DB_PREFIX_ . "ets_am_reward` 
                WHERE id_shop = " . (int)$context->shop->id . " AND (datetime_added IS NOT NULL AND datetime_added != '0000-00-00 00:00:00' AND datetime_added != '0000-00-00 00:00:00.000000')
            ");
            $params['date_type'] = ($distance <= 5 ? 'this_year' : 'all_times');
        }
        if (isset($params['date_type'])) {
            if ($params['date_type'] == 'this_month') {
                $start_date = date('Y-m-01 00:00:00');
                $end_date = date('Y-m-t 23:59:59');
            } elseif ($params['date_type'] == 'this_year') {
                $start_date = date('Y-01-01 00:00:00');
                $end_date = date('Y-12-t 23:59:59');
            } elseif ($params['date_type'] == 'time_ranger' && isset($params['date_from']) && Validate::isDate($params['date_from'])&& isset($params['date_to']) && Validate::isDate($params['date_to'])) {
                $start_date = date('Y-m-d 00:00:00', strtotime($params['date_from'] . ' 00:00:00'));
                $end_date = date('Y-m-d 23:59:59', strtotime($params['date_to'] . ' 23:59:59'));
            }
        }
        $filter_date = '';
        if ($start_date && $end_date) {
            $filter_date .= " AND r.datetime_added >= '" . pSQL($start_date) . "' AND  r.datetime_added <= '" .pSQL($end_date). "'";
        }
        $reward = Db::getInstance()->getRow("SELECT SUM(IF(r.program = 'loy', r.amount, 0)) as loy_reward,
            SUM(IF(r.program = 'ref', r.amount, 0)) as ref_reward,
            SUM(IF(r.program = 'aff', r.amount, 0)) as aff_reward,
            SUM(IF(r.program != 'aff' AND r.program != 'ref' AND r.program != 'loy', r.amount, 0)) as other_reward
            FROM `" . _DB_PREFIX_ . "ets_am_reward` r
            WHERE r.deleted = 0 " . (string)$filter_where . $filter_date);
        $data = array();
        if ($reward && ($reward['loy_reward'] || $reward['ref_reward'] || $reward['aff_reward'] || $reward['other_reward'])) {
            $data = array(
                array(
                    'label' => 'Loyalty',
                    'value' => $frontend ? Ets_AM::displayReward((float)$reward['loy_reward'], false) : (float)$reward['loy_reward']
                ),
                array(
                    'label' => 'Referral',
                    'value' => $frontend ? Ets_AM::displayReward((float)$reward['ref_reward'], false) : (float)$reward['ref_reward']
                ),
                array(
                    'label' => 'Affiliate',
                    'value' => $frontend ? Ets_AM::displayReward((float)$reward['aff_reward'], false) : (float)$reward['aff_reward']
                ),
                array(
                    'label' => 'Others',
                    'value' => $frontend ? Ets_AM::displayReward((float)$reward['other_reward'], false) : (float)$reward['other_reward']
                ),
            );
        }
        return $data;
    }
    public static function getTimeAgo($time)
    {
        $trans = Module::getInstanceByName('ets_affiliatemarketing')->getTranslates();
        $time_difference = time() - $time;
        if ($time_difference < 1) {
            return array('type' => 'ms', 'text' => $trans['less_than_1s_ago']);
        }
        $condition = array(12 * 30 * 24 * 60 * 60 => array(
            'type' => 'year',
            'text' => $trans['year_ago']
        ),
            30 * 24 * 60 * 60 => array(
                'type' => 'month',
                'text' => $trans['month_ago']
            ),
            24 * 60 * 60 => array(
                'type' => 'day',
                'text' => $trans['day_ago'],
            ),
            60 * 60 => array(
                'type' => 'hour',
                'text' => $trans['hour_ago'],
            ),
            60 => array(
                'type' => 'minute',
                'text' => $trans['minute_ago']
            ),
            1 => array(
                'type' => 'second',
                'text' => $trans['second_ago']
            )
        );
        foreach ($condition as $secs => $str) {
            $d = $time_difference / $secs;
            if ($d >= 1) {
                $t = round($d);
                return array(
                    'type' => $str['type'],
                    'text' => $t . ' ' . $str['text']
                );
            }
        }
    }
    public static function displayRewardInMsg($reward, $context = null)
    {
        if (!$context) {
            $context = Context::getContext();
        }
        if (Configuration::get('ETS_AM_REWARD_DISPLAY') == 'point') {
            $unit = Configuration::get('ETS_AM_REWARD_UNIT_LABEL', $context->language->id);
            $unit_default = Configuration::get('ETS_AM_REWARD_UNIT_LABEL', (int)Configuration::get('PS_LANG_DEFAULT'));
            if (!$unit) {
                $unit = $unit_default;
            }
            if (Configuration::get('ETS_AM_CONVERSION') != '') {
                $point = Tools::ps_round(Tools::ps_round($reward, _PS_PRICE_COMPUTE_PRECISION_ ?: 2) * (float)Configuration::get('ETS_AM_CONVERSION'), _PS_PRICE_COMPUTE_PRECISION_ ?: 2);
            }
            if (self::needExchange($context)) {
                $reward = Tools::convertPrice($reward, $context->currency->id, true);
            }
            return $point . ' ' . $unit . ' (' . Tools::displayPrice($reward) . ')';
        }
        if (self::needExchange($context)) {
            $reward = Tools::convertPrice($reward, $context->currency->id, true);
        }
        return Tools::displayPrice($reward);
    }
    public function runCronjob()
    {
        $str = "";
        $limit_record_per_execute = Configuration::getGlobalValue('ETS_AM_CRONJOB_NUMBER_EMAIL') ? :5;
        $dateScan = date('Y-m-d 23:59:59');
        $count_mail_sent = 0;
        $count_reward_expired = 0;
        $log_path = _PS_ETS_EAM_LOG_DIR_ . '/aff_cronjob.log';
        if (!is_dir(_PS_ETS_EAM_LOG_DIR_))
            @mkdir(_PS_ETS_EAM_LOG_DIR_, 0755, true);
        if ($shops = Shop::getShops(false)) {
            foreach ($shops as $shop) {
                $expired = "SELECT * FROM `" . _DB_PREFIX_ . "ets_am_reward`
        WHERE `expired_date` IS NOT NULL
          AND `expired_date` < '" . pSQL($dateScan) . "'
          AND `status` =1         
          AND `id_shop`=" . (int)$shop['id_shop'] . " AND used=0
        ORDER BY `id_ets_am_reward` 
        LIMIT " . (int)$limit_record_per_execute;
                $expiredRewards = DB::getInstance()->executeS($expired);
                if (count($expiredRewards)) {
                    foreach ($expiredRewards as $reward) {
                        $r = new Ets_AM($reward['id_ets_am_reward']);
                        $r->status = -2;
                        $str .= date('Y-m-d H:i:s') . " R-" . $reward['id_ets_am_reward'] . " expired\n";
                        $count_reward_expired++;
                        if ($r->save() && Configuration::get('ETS_AM_LOYALTY_EMAIL_EXPIRED', null, null, $shop['id_shop'])) {
                            Ets_Loyalty::sendEmailToCustomerWhenRewardExpired($r);
                            $r->send_expired_email = date('Y-m-d H:i:s');
                            $r->save();
                            $customer = new Customer($r->id_customer);
                            if ($customer->id)
                                $str .= date('Y-m-d H:i:s') . " Mail to " . $customer->email . ": R-" . $r->id . " is expired\n";
                        }
                    }
                }
                if (Configuration::get('ETS_AM_LOYALTY_EMAIL_GOING_EXPIRED', null, null, $shop['id_shop'])) {
                    if ($beforeExpired = Configuration::get('ETS_AM_LOYALTY_EMAIL_EXPIRED_DAY', null, null, $shop['id_shop'])) {
                        $addDay = $dateScan . ' +' . $beforeExpired . ' days';
                        $dateScan = date('Y-m-d', strtotime($addDay));
                        $goingToExpired = "SELECT * FROM `" . _DB_PREFIX_ . "ets_am_reward` 
                           WHERE `expired_date` IS NOT NULL
                           AND DATE(`expired_date`) = DATE('" . pSQL($dateScan) . "')
                           AND `send_going_expired_email` IS NULL
                           AND `id_shop`=" . (int)$shop['id_shop'] . " AND used=0
                           ORDER BY `id_ets_am_reward`
                           LIMIT " . (int)$limit_record_per_execute;
                        $isGoingExpired = Db::getInstance()->executeS($goingToExpired);
                        if (count($isGoingExpired)) {
                            foreach ($isGoingExpired as $reward) {
                                $r = new Ets_AM($reward['id_ets_am_reward']);
                                Ets_Loyalty::sendEmailToCustomerWhenRewardIsGoingToBeExpired($r);
                                $r->send_going_expired_email = date('Y-m-d H:i:s');
                                $r->save();
                                $count_mail_sent++;
                                $customer = new Customer($r->id_customer);
                                if ($customer->id)
                                    $str .= date('Y-m-d H:i:s') . " Mail to " . $customer->email . ": R-" . $r->id . " will expire in $beforeExpired day(s)\n";
                            }
                        }
                    }
                }
                $wait_validate_rewards = Db::getInstance()->executeS('SELECT * FROM `' . _DB_PREFIX_ . 'ets_am_reward` WHERE datetime_validated <="' . pSQL(date('Y-m-d H:i:s')) . '" AND await_validate!=0 AND status=0');
                if ($wait_validate_rewards) {
                    foreach ($wait_validate_rewards as $validate_reward) {
                        $r = new Ets_AM($validate_reward['id_ets_am_reward']);
                        $r->await_validate = 0;
                        $r->datetime_validated = date('Y-m-d H:i:s');
                        $r->status = 1;
                        $r->save();
                        if ($validate_reward['program'] == EAM_AM_LOYALTY_REWARD) {
                            if (Configuration::get('ETS_AM_ENABLED_EMAIL_CUSTOMER_RVOC')) {
                                Ets_Loyalty::sendEmailToCustomerWhenRewardValidated($r);
                            }
                            if (Configuration::get('ETS_AM_ENABLED_EMAIL_ADMIN_RVOC')) {
                                Ets_Loyalty::sendEmailToAdminWhenRewardValidated($r);
                            }
                        }
                        if ($validate_reward['program'] == EAM_AM_AFFILIATE_REWARD) {
                            if ((int)Configuration::get('ETS_AM_ENABLED_EMAIL_CUSTOMER_RVOC')) {
                                Ets_Affiliate::senEmailWhenAffiliateRewardValidated( $r);
                            }
                            if ((int)Configuration::get('ETS_AM_ENABLED_EMAIL_ADMIN_RVOC')) {
                                Ets_Affiliate::senEmailWhenAffiliateRewardValidated( $r, true);
                            }
                        }
                        if ($validate_reward['program'] == EAM_AM_REF_REWARD) {
                            if ((int)Configuration::get('ETS_AM_ENABLED_EMAIL_CUSTOMER_RVOC')) {
                                Ets_Sponsor::sendMailRewardValidated(null, $r->id);
                            }
                            if ((int)Configuration::get('ETS_AM_ENABLED_EMAIL_ADMIN_RVOC')) {
                                Ets_Sponsor::sendMailAdminRewardValidated(null, $r->id);
                            }
                        }
                        $str .= date('Y-m-d H:i:s') . " R-" . $r->id . " Approved \n";
                    }
                }
            }
        }
        Configuration::updateGlobalValue('ETS_AM_TIME_RUN_CRONJOB', date('Y-m-d H:i:s'));
        if ($str) {
            if (Configuration::getGlobalValue('ETS_AM_SAVE_LOG')) {
                file_put_contents($log_path, $str, FILE_APPEND | LOCK_EX);
            }
            echo nl2br($str);
            /** @var Ets_affiliatemarketing $aff */
            $aff = Module::getInstanceByName('ets_affiliatemarketing');
            $aff->_clearCache('*',$aff->_getCacheId('dashboard',false));
        } else {
            echo "Cronjob run but nothing to do!";
            if (Configuration::getGlobalValue('ETS_AM_SAVE_LOG')) {
                file_put_contents($log_path, date('Y-m-d H:i:s') . ': Cronjob run but nothing to do!' . "\n", FILE_APPEND | LOCK_EX);
            }
        }
        exit;
    }
    public static function getRewardByIDOrder($id_order, $program)
    {
        if (in_array($program, array('aff', 'loy', 'ref')))
            return Db::getInstance()->getRow('SELECT id_order,status,SUM(amount) total_amount FROM `' . _DB_PREFIX_ . 'ets_am_reward` WHERE id_order=' . (int)$id_order . ' AND program="' . pSQL($program) . '" GROUP BY id_order,status');
        return false;
    }
    public static function getOtherOrder($id_order,$reference)
    {
        return Db::getInstance()->executeS('SELECT id_order FROM `'._DB_PREFIX_.'orders` WHERE id_order!="'.(int)$id_order.'" AND reference ="'.pSQL($reference).'"');
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
        $aff->_clearCache('*',$aff->_getCacheId('list_reward',false));
        return true;
    }
}
