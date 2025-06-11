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
class Ets_Affiliate extends Ets_AM
{
    /**
     * Ets_Affiliate constructor.
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
    /**
     * @return bool
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public static function isCustomerApplicableAffiliatedProgram()
    {
        $allow = false;
        $customer = Context::getContext()->customer;
        if ($customer->isLogged()) {
            $config = Configuration::get('ETS_AM_AFF_GROUPS');
            if (!$config || $config == 'ALL') {
                $allow = true;
            }
            $customerGroups = $customer->getGroups();
            $configs = explode(', ', $config);
            if ($customerGroups) {
                foreach ($customerGroups as $customerGroup)
                    if (in_array($customerGroup, $configs))
                        $allow = true;
            }
        }
        if ($allow) {
            if ($min = Configuration::get('ETS_AM_AFF_MIN_ORDER')) {
                $minSpent = self::calculateCustomerSpent();
                if ($minSpent >= (float)$min) {
                    return true;
                }
            }
        }
        return false;
    }
    /**
     * @param $customer
     * @return bool
     */
    public static function isCustomerBelongToValidAffiliateGroup($customer)
    {
        $config = Configuration::get('ETS_AM_AFF_GROUPS');
        if ($config) {
            if ($config == 'ALL') {
                return true;
            } else {
                $configs = explode(',', $config);
                $customerGroups = $customer->getGroups();
                if ($customerGroups) {
                    foreach ($customerGroups as $customerGroup)
                        if (in_array($customerGroup, $configs))
                            return true;
                }
            }
        }
        return false;
    }
    /**
     * @return bool
     */
    public static function isCustomerCanJoinAffiliateProgram($customer = null)
    {
        $context = Context::getContext();
        if (!$customer)
            $customer = $context->customer;
        elseif (!is_object($customer))
            $customer = new Customer($customer);
        if (Configuration::get('ETS_AM_AFF_ENABLED')) {
            if (self::isCustomerBelongToValidAffiliateGroup($customer)) {
                $minOrder = Configuration::get('ETS_AM_AFF_MIN_ORDER');
                if ($minOrder) {
                    $minOrder = (float)$minOrder;
                    $customerOrder = (float)Ets_AM::getCustomerTotalOrder($customer->id, $context);
                    return $customerOrder >= $minOrder;
                }
                return true;
            }
        }
        return false;
    }
    public static function isCustomerCanJoinAffiliateProgramReturn()
    {
        $context = Context::getContext();
        $customer = $context->customer;
        if (Configuration::get('ETS_AM_AFF_ENABLED')) {
            if (self::isCustomerBelongToValidAffiliateGroup($customer)) {
                $minOrder = Configuration::get('ETS_AM_AFF_MIN_ORDER');
                if ($minOrder) {
                    $minOrder = (float)$minOrder;
                    $customerOrder = (float)Ets_AM::getTotalOrder($customer->id, $context);
                    if ($customerOrder < $minOrder) {
                        return array(
                            'success' => false,
                            'min_order' => $minOrder,
                            'total_order' => $customerOrder
                        );
                    }
                }
                return array(
                    'success' => true,
                );
            } else {
                return array(
                    'success' => false,
                    'not_in_group' => true,
                );
            }
        }
        return false;
    }
    /**
     * @return bool
     */
    public static function isCustomerCanJoinAffiliateProgram2($id_customer)
    {
        $context = Context::getContext();
        if (Configuration::get('ETS_AM_AFF_ENABLED')) {
            $customer = new Customer($id_customer);
            if (self::isCustomerBelongToValidAffiliateGroup($customer)) {
                $minOrder = Configuration::get('ETS_AM_AFF_MIN_ORDER');
                if ($minOrder) {
                    $minOrder = (float)$minOrder;
                    $customerOrder = (float)Ets_AM::getCustomerTotalOrder($id_customer, $context);
                    return $customerOrder >= $minOrder;
                }
                return true;
            }
        }
        return false;
    }
    /**
     * @param $product
     * @return bool
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public static function productValidAffiliateProgram($product)
    {
        if (!$product)
            return;
        if (is_array($product)) {
            $id_product = isset($product['id_product']) && $product['id_product'] ? (int)$product['id_product'] : (isset($product['id']) && $product['id'] ? (int)$product['id'] : 0);
        } elseif (Validate::isLoadedObject($product)) {
            $id_product = (int)$product->id;
        }
        if (!Configuration::get('ETS_AM_AFF_ENABLED')) {
            return false;
        }
        $valid = false;
        $catType = Configuration::get('ETS_AM_AFF_CAT_TYPE');
        if (!$catType) {
            $valid = false;
        }
        if ($catType == 'ALL') {
            $valid = true;
        } elseif ($catType !== '') {
            $categories = explode(',', Configuration::get('ETS_AM_AFF_CATEGORIES'));
            $valid = self::validateProductCat($id_product, $categories);
        }
        $spec = Configuration::get('ETS_AM_AFF_SPECIFIC_PRODUCTS');
        if (!$valid && $spec && $spec !== '') {
            $specificProducts = explode(',', $spec);
            return in_array($id_product, $specificProducts);
        }
        $exc = Configuration::get('ETS_AM_AFF_PRODUCTS_EXCLUDED');
        if ($valid && $exc && $exc !== '') {
            $excludeProducts = explode(',', $exc);
            $valid = !in_array($id_product, $excludeProducts);
            if ($valid && Configuration::get('ETS_AM_AFF_PRODUCTS_EXCLUDED_DISCOUNT')) {
                return !(bool)Db::getInstance()->getRow('SELECT id_specific_price FROM `' . _DB_PREFIX_ . 'specific_price` WHERE id_product ="' . (int)$id_product . '" AND (`from` = "0000-00-00 00:00:00" OR `from` <="' . pSQL(date('Y-m-d H:i:s')) . '" ) AND (`to` = "0000-00-00 00:00:00" OR `to` >="' . pSQL(date('Y-m-d H:i:s')) . '" )');
            }
        } elseif ($valid) {
            if (Configuration::get('ETS_AM_AFF_PRODUCTS_EXCLUDED_DISCOUNT')) {
                return !(bool)Db::getInstance()->getRow('SELECT id_specific_price FROM `' . _DB_PREFIX_ . 'specific_price` WHERE id_product ="' . (int)$id_product . '" AND (`from` = "0000-00-00 00:00:00" OR `from` <="' . pSQL(date('Y-m-d H:i:s')) . '" ) AND (`to` = "0000-00-00 00:00:00" OR `to` >="' . pSQL(date('Y-m-d H:i:s')) . '" )');
            }
        }
        return $valid;
    }
    /**
     * @param $product
     * @param null $context
     * @return float
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public static function calculateAffRewardForSingleProduct($product, $context = null)
    {
        if (!$context) {
            $context = Context::getContext();
        }
        $id_shop = $context->shop->id;
        $check = "SELECT * FROM `" . _DB_PREFIX_ . "ets_am_aff_reward`
                           WHERE `id_product` = " . (int)$product['id_product'] . "
                           AND `id_shop` = " . (int)$id_shop . "
                           AND `use_default` != 1";
        $result = Db::getInstance()->getRow($check);
        if ($result && count($result) > 0) {
            $reward = (float)self::affiliateReward($product, false, $result, $context);
        } else {
            $reward = (float)self::affiliateReward($product, true, array(), $context);
        }
        return $reward;
    }
    protected static function affiliateReward($product, $default = true, $settings = array(), $context = null)
    {
        if (!$context) {
            $context = Context::getContext();
        }
        $reward = 0;
        if (!$default && !count($settings)) {
            return $reward;
        }
        if ($default) {
            $baseOn = Configuration::get('ETS_AM_AFF_HOW_TO_CALCULATE');
        } else {
            $baseOn = $settings['how_to_calculate'];
        }
        if (!$baseOn) {
            $reward += 0;
        } else {
            $excludeTax = (bool)Configuration::get('ETS_AM_AFF_TAX_EXCLUDED');
            switch ($baseOn) {
                case 'NO_REWARD':
                    $reward = 0;
                    break;
                case 'PERCENT':
                    if ($default) {
                        $percentage = (float)Configuration::get('ETS_AM_AFF_DEFAULT_PERCENTAGE');
                    } else {
                        $percentage = (float)$settings['default_percentage'];
                    }
                    if (!$percentage) {
                        $reward = 0;
                    } else {
                        $productPrice = $excludeTax ? (isset($product['price_without_tax_without_reduction']) ? $product['price_without_tax_without_reduction'] : $product['price_with_reduction_without_tax']) : (isset($product['price_with_tax_without_reduction']) ? $product['price_with_tax_without_reduction'] : $product['price_with_reduction']);
                        if (Ets_AM::needExchange($context)) {
                            $conversion_rate = $context->currency->conversion_rate;
                            if ($conversion_rate)
                                $productPrice = (float)$productPrice / $conversion_rate;
                        }
                        $reward = $productPrice * $percentage / 100;
                    }
                    break;
                case 'FIXED':
                    if ($default) {
                        $fixedAmount = (float)Configuration::get('ETS_AM_AFF_DEFAULT_FIXED_AMOUNT');
                    } else {
                        $fixedAmount = (float)$settings['default_fixed_amount'];
                    }
                    $reward = $fixedAmount;
                    break;
            }
        }
        return $reward;
    }
    public static function getAffiliateMessage($product, $context = null)
    {
        if (!$context) {
            $context = Context::getContext();
        }
        if (!$context->customer->isLogged(false)) {
            return null;
        }
        if (!Configuration::get('ETS_AM_AFF_ENABLED')) {
            return null;
        }
        if (!self::productValidAffiliateProgram($product)) {
            return null;
        }
        if (!self::isCustomerCanJoinAffiliateProgram()) {
            return null;
        }
        $commission = self::calculateAffRewardForSingleProduct($product, $context);
        if (!$commission || (float)$commission == 0) {
            return null;
        }
        if (!self::isCustomerJoinedAffiliate()) {
            if ((int)Configuration::get('ETS_AM_AFF_ENABLED') && (int)Configuration::get('ETS_AM_AFF_REGISTER_REQUIRED') && Ets_Participation::getProgramRegistered((int)$context->customer->id, 'aff')) {
                return 'wating_confirm';
            }
            $message =  Configuration::get('ETS_AM_AFF_PROPOSE_MSG', $context->language->id);
            if (!$message) {
                return null;
            }
            $link = Ets_AM::getBaseUrlDefault('register', array('p' => 'aff'));
            return array(
                'is_aff' => false,
                'message' => $message,
                'link' => $link,
                'commission' => Ets_AM::displayReward($commission)
            );
        } else {
            $message = Configuration::get('ETS_AM_AFF_AFFILIATE_LINK_MSG', $context->language->id);
            if (!$message) {
                return null;
            }
            $p = new Product($product['id_product']);
            $link = self::generateAffiliateLinkForProduct($p);
            return array(
                'is_aff' => true,
                'message' => $message,
                'link' => $link,
                'commission' => Ets_AM::displayReward($commission)
            );
        }
    }
    /**
     * @param $product
     * @param null $context
     * @return string
     * @throws PrestaShopException
     */
    public static function generateAffiliateLinkForProduct(Product $product, $context = null, $aff_link = true)
    {
        if (!$context) {
            $context = Context::getContext();
        }
        $id_default_attribute = $product->cache_default_attribute;
        $cat_param = '';
        $cat_id = $product->id_category_default;
        $cat = new Category($cat_id);
        if ($cat->id) {
            $cat_param = $cat->link_rewrite[$context->language->id];
        }
        $link_rewrite = '';
        if (isset($product->link_rewrite[$context->language->id]) && $product->link_rewrite[$context->language->id]) {
            $link_rewrite = $product->link_rewrite[$context->language->id];
        } else {
            foreach ($product->link_rewrite as $lrw) {
                if ($lrw) {
                    $link_rewrite = $lrw;
                }
            }
        }
        $product_link = $context->link->getProductLink($product, $link_rewrite, $cat_param, null, null, null, $id_default_attribute);
        if ($aff_link) {
            if (Tools::strpos($product_link, '#') > 0) {
                $text_extra = Tools::substr($product_link, Tools::strpos($product_link, '#'));
                $product_link = Tools::substr($product_link, 0, Tools::strpos($product_link, '#'));
            } else
                $text_extra = '';
            if (strpos($product_link, '?')) {
                $product_link .= '&';
            } else {
                $product_link .= '?';
            }
            $product_link .= 'affp=' . $context->customer->id . $text_extra;
        }
        return $product_link;
    }
    /**
     * @return bool
     */
    public static function isCustomerJoinedAffiliate()
    {
        $customer = Context::getContext()->customer;
        if ((bool)Configuration::get('ETS_AM_AFF_REGISTER_REQUIRED')) {
            $sql = "SELECT COUNT(*) as `total` FROM `" . _DB_PREFIX_ . "ets_am_participation` WHERE `id_customer` = " . (int)$customer->id . " AND `program` = 'aff' AND `status` = 1";
            $result = Db::getInstance()->getRow($sql);
            $user = (int)Db::getInstance()->getValue("SELECT id_customer FROM `"._DB_PREFIX_."ets_am_user` WHERE id_customer=".(int)$customer->id." AND aff=1");
            return (int)$result['total'] > 0 || $user;
        } else {
            return self::isCustomerBelongToValidAffiliateGroup($customer);
        }
    }
    /**
     * @param Cart $cart
     * @param null $context
     * @param bool $get_product
     * @return array|float|int
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public static function calculateAffiliateCartReward($cart, $context = null, $get_product = false, $id_customer = false, $product_list = array(), $last_customer = false)
    {
        if (!$context) {
            $context = Context::getContext();
        }
        if (!Configuration::get('ETS_AM_AFF_ENABLED')) {
            return 0;
        }
        if (!Configuration::get('ETS_AM_AFF_BY_SELLER') && $id_customer == $context->customer->id)
            return 0;
        $aff = $id_customer ? $id_customer : (int)$context->cookie->__get(EAM_AFF_CUSTOMER_COOKIE);
        if (!$aff) {
            return 0;
        }
        if (Ets_Affiliate::isCustomerSuspendedOrBannedAffiliateProgram($aff)) {
            return 0;
        }
        if (!Ets_Affiliate::isCustomerCanJoinAffiliateProgram2($aff) || !Ets_Affiliate::isCustomerJoinedAffiliateProgram($aff, 1)) {
            return 0;
        }
        $affiliateReward = 0;
        $total = (float)$cart->getOrderTotal(Configuration::get('ETS_AM_AFF_TAX_EXCLUDED'));
        if ($minOrderTotal = Configuration::get('ETS_AM_AFF_MIN_ORDER')) {
            if ($minOrderTotal && $total < (float)$minOrderTotal) {
                return $affiliateReward;
            }
        }
        $products = $cart->getProducts();
        if (!count($products)) {
            return $affiliateReward;
        }
        $p = array();
        foreach ($products as $product) {
            if (self::productValidAffiliateProgram($product) && (in_array($product['id_product'], $product_list) || ($last_customer && self::checkOtherProduct($product['id_product'])))) {
                $product_reward = self::calculateAffRewardForSingleProduct($product);
                $product['reward_amount'] = $product_reward;
                if ($product_reward != 0) {
                    $p[] = $product;
                }
                if (Configuration::get('ETS_AM_AFF_MULTIPLE')) {
                    $product_reward *= $product['quantity'];
                }
                $affiliateReward += $product_reward;
            }
        }
        if ($get_product) {
            return array(
                'reward' => $affiliateReward,
                'products' => $p
            );
        }
        return $affiliateReward;
    }
    public static function checkOtherProduct($id_product)
    {
        $context = Context::getContext();
        if (Configuration::get('ETS_AM_AFF_REWARD_ON_OTHER_PRODUCTS')) {
            $aff_product = $context->cookie->__get(EAM_AFF_PRODUCT_COOKIE);
            if ($aff_product) {
                $listProducts = explode('-', $aff_product);
                if (!in_array($id_product, $listProducts)) {
                    return true;
                }
            }
        }
        return false;
    }
    /**
     * @param $subject
     * @param $reward
     */
    public static function sendEmailWhenAffiliateRewardCreated($subject, $reward, $is_admin = false)
    {
        $module = Module::getInstanceByName('ets_affiliatemarketing');
        $customer = new Customer($reward->id_customer);
        $trans = Module::getInstanceByName('ets_affiliatemarketing')->getTranslates();
        $program_name = 'Affiliate program';
        $friend = new Customer($reward->id_friend);
        $status = '';
        if ($reward->status == 0) {
            $status = 'Pending';
        } elseif ($reward->status == 1) {
            $status = 'Approved';
        }
        if ($reward->status == -1) {
            $status = 'Canceled';
        }
        if ($reward->status == -2) {
            $status = 'Expired';
        }
        $friendName = '';
        if ($friend && isset($friend->firstname)) {
            $friendName = $friend->firstname . ' ' . $friend->lastname;
        }
        $data = array(
            '{title}' => $trans['new-reward'],
            '{customer}' => $customer->firstname . ' ' . $customer->lastname,
            '{friend}' => $friendName,
            '{program}' => $program_name,
            '{status}' => $status,
            '{reward}' => $reward->id,
            '{reward_id}' => $reward->id,
            '{date_created}' => $reward->datetime_added,
            '{amount}' => Ets_affiliatemarketing::displayPrice(Tools::convertPrice($reward->amount), Configuration::get('PS_CURRENCY_DEFAULT')),
        );
        if ($is_admin) {
            $adminEmail = Configuration::get('ETS_AM_EMAILS_CONFIRM');
            if ($adminEmail) {
                $adminEmail = explode(',', $adminEmail);
                $subject = array(
                    'translation' => $module->l('A new reward was created', 'ets_affiliate'),
                    'origin' => 'A new reward was created',
                    'specific' => 'ets_affiliate'
                );
                foreach ($adminEmail as $to) {
                    Ets_aff_email::send(0, 'new_reward_affiliate_admin', $subject, $data, array('employee' => trim($to)));
                }
            }
        } else {
            if ($customer) {
                $subject = array(
                    'translation' => $module->l('A new reward created for you', 'ets_affiliate'),
                    'origin' => 'A new reward created for you',
                    'specific' => 'ets_affiliate'
                );
                Ets_aff_email::send($customer->id_lang, 'reward_created_customer', $subject, $data, $customer->email);
            }
        }
    }
    /**
     * @param $subject
     * @param $reward
     */
    public static function senEmailWhenAffiliateRewardValidated($reward, $is_admin = false)
    {
        $customer = new Customer($reward->id_customer);
        $trans = Module::getInstanceByName('ets_affiliatemarketing')->getTranslates();
        $module = Module::getInstanceByName('ets_affiliatemarketing');
        $program = '';
        if ($reward->program == 'loy') {
            $program = $trans['loyalty_program'];
        } elseif ($reward->program == 'ref') {
            $program = $trans['referral_program'];
        } elseif ($reward->program == 'aff') {
            $program = $trans['affiliate_program'];
        } elseif ($reward->program == 'anr') {
            $program = $trans['referral_and_affiliate_program'];
        }
        $curency_default = new Currency((int)Configuration::get('PS_CURRENCY_DEFAULT'));
        $data = array(
            '{title}' => $trans['reward_validated'],
            '{customer}' => $customer->firstname . ' ' . $customer->lastname,
            '{reward}' => $reward->id,
            '{amount}' => Tools::ps_round($reward->amount,2).'('.$curency_default->iso_code.')',
            '{type}' => $reward->program,
            '{reward_id}' => $reward->id,
            '{program}' => $program,
            '{date_created}' => $reward->datetime_added,
            '{date_validated}' => $reward->datetime_validated,
            '{note}' => $reward->note
        );
        if ($is_admin) {
            $adminEmail = Configuration::get('ETS_AM_EMAILS_CONFIRM');
            if ($adminEmail) {
                $adminEmail = explode(',', $adminEmail);
                foreach ($adminEmail as $to) {
                    $subject = array(
                        'translation' => $module->l('A reward was approved', 'ets_affiliate'),
                        'origin' => 'A reward was approved',
                        'specific' => 'ets_affiliate'
                    );
                    Ets_aff_email::send(0, 'reward_validated', $subject, $data, array('employee' => trim($to)));
                }
            }
        } else {
            if ($customer) {
                $subject = array(
                    'translation' => $module->l('Your reward was approved', 'ets_affiliate'),
                    'origin' => 'Your reward was approved',
                    'specific' => 'ets_affiliate'
                );
                Ets_aff_email::send($customer->id_lang, 'reward_validated', $subject, $data, $customer->email);
            }
        }
    }
    /**
     * @param $subject
     * @param $reward
     */
    public static function sendEmailWhenAffiliateCanceled($reward, $is_admin = false)
    {
        $module = Module::getInstanceByName('ets_affiliatemarketing');
        $customer = new Customer($reward->id_customer);
        $data = array(
            '{customer}' => $customer->firstname . ' ' . $customer->lastname,
            '{amount}' => Ets_affiliatemarketing::displayPrice(Tools::convertPrice($reward->amount), Configuration::get('PS_CURRENCY_DEFAULT')),
            '{type}' => $reward->program,
            '{program}' => 'Affiliate program',
            '{date_created}' => $reward->datetime_added,
            '{date_canceled}' => date('Y-m-d H:i:s'),
            '{reward_id}' => $reward->id,
            '{reward}' => $reward->id,
            '{note}' => $reward->note
        );
        if ($is_admin) {
            $adminEmail = Configuration::get('ETS_AM_EMAILS_CONFIRM');
            if ($adminEmail) {
                $subject = array(
                    'translation' => $module->l('A reward was canceled', 'ets_affiliate'),
                    'origin' => 'A reward was canceled',
                    'specific' => 'ets_affiliate'
                );
                $adminEmail = explode(',', $adminEmail);
                foreach ($adminEmail as $to) {
                    Ets_aff_email::send(0, 'reward_canceled_admin', $subject, $data, array('employee' => trim($to)));
                }
            }
        } else {
            if ($customer) {
                $subject = array(
                    'translation' => $module->l('Your reward was canceled', 'ets_affiliate'),
                    'origin' => 'Your reward was canceled',
                    'specific' => 'ets_affiliate'
                );
                Ets_aff_email::send($customer->id_lang, 'reward_canceled', $subject, $data, $customer->email);
            }
        }
    }
    /**
     * @param null $context
     * @param string $program
     * @return array
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public static function getSales($program = 'aff', $filter = array())
    {
        $context = Context::getContext();
        $query = array();
        $type_date_filter = isset($filter['type_date_filter']) && Validate::isString($filter['type_date_filter']) && in_array($filter['type_date_filter'], array('all_times', 'this_year', 'this_month')) ? $filter['type_date_filter'] : 'all_times';
        $query['type_date_filter'] = $type_date_filter;
        $limit = 20;
        $page = isset($filter['page']) && (int)$filter['page'] > 0 ? (int)$filter['page'] : 1;
        $offset = ($page - 1) * $limit;
        $sql_part = " FROM `" . _DB_PREFIX_ . "ets_am_reward_product` rp
                       INNER JOIN `" . _DB_PREFIX_ . "product_lang` pl ON pl.id_product = rp.id_product
                WHERE rp.id_seller = " . (int)$context->customer->id . "
                  AND pl.id_lang = " . (int)$context->language->id . "
                  AND pl.id_shop = " . (int)$context->shop->id;
        $condition = '';
        $condition .= " AND rp.program = '" . pSQL($program) . "'";
        if ($type_date_filter == 'this_month') {
            $condition .= " AND rp.datetime_added >= '" . date('Y-m-01 00:00:00') . "' AND rp.datetime_added <= '" . date('Y-m-t 23:59:59') . "'";
        } elseif ($type_date_filter == 'this_year') {
            $condition .= " AND rp.datetime_added >= '" . date('Y-01-01 00:00:00') . "' AND rp.datetime_added <= '" . date('Y-12-31 23:59:59') . "'";
        } elseif ($type_date_filter == 'all_times') {
            $max_time = Db::getInstance()->getValue("SELECT MAX(datetime_added) FROM `" . _DB_PREFIX_ . "ets_am_reward` WHERE id_shop = " . (int)$context->shop->id);
            $min_time = Db::getInstance()->getValue("SELECT Min(datetime_added) FROM `" . _DB_PREFIX_ . "ets_am_reward` WHERE id_shop = " . (int)$context->shop->id);
            $start_date = $min_time;
            $end_date = $max_time;
            $condition .= " AND rp.datetime_added >= '" . pSQL($start_date) . "' AND rp.datetime_added <= '" . pSQL($end_date) . "'";
        } else {
            $condition .= " AND rp.datetime_added >= '" . date('Y-m-01 00:00:00') . "' AND rp.datetime_added <= '" . date('Y-m-t 23:59:59') . "'";
        }
        $order_by = 'rp.id_ets_am_reward';
        $fields_filter = array('id_product', 'product_name', 'number_sales', 'number_orders', 'earning_rewards', 'earning_rewards', 'total_views');
        $ord = isset($filter['orderBy']) && ($ord = Tools::strtolower($filter['orderBy'])) && in_array($ord, $fields_filter) ? $ord : 'product_name';
        $order_way = isset($filter['orderWay']) && ($order_way = Tools::strtolower($filter['orderWay'])) && in_array($order_way, array('asc', 'desc')) ? $order_way : 'desc';
        if ($ord) {
            $query['orderBy'] = $ord;
            $query['orderWay'] = $order_way;
            switch ($ord) {
                case 'id_product';
                    $order_clause = ' ORDER BY id_product ' . pSQL($order_way);
                    break;
                case 'product_name';
                    $order_clause = ' ORDER BY product_name ' . pSQL($order_way);
                    break;
                case 'number_sales':
                    $order_clause = ' ORDER BY number_sales ' . pSQL($order_way);
                    break;
                case 'number_orders':
                    $order_clause = ' ORDER BY number_orders ' . pSQL($order_way);
                    break;
                case 'earning_rewards':
                    $order_clause = ' ORDER BY earning_rewards ' . pSQL($order_way);
                    break;
                case 'total_views':
                    $order_clause = ' ORDER BY view_count ' . pSQL($order_way);
                    break;
            }
        } else {
            $query['orderBy'] = 'rp.id_ets_am_reward';
            $query['orderWay'] = 'desc';
            $order_clause = ' ORDER BY ' . pSQL($order_by) . ' ' . pSQL($order_way);
        }
        $sql_total = "SELECT COUNT(DISTINCT rp.id_product) as `total` " . (string)$sql_part . (string)$condition;
        $sql_part .= $condition;
        $total = Db::getInstance()->getValue($sql_total);
        $total_page = ceil($total / $limit);
        $sql_part .= " GROUP BY rp.id_product ";
        $sql = "SELECT rp.id_product                            as id_product,
               pl.name                                          as product_name,
               COUNT(DISTINCT rp.id_order)                      as number_orders,
               COUNT(DISTINCT rp.id_order)                      as total_order,
               SUM(rp.quantity) as number_sales,
               SUM(rp.quantity*rp.amount) as earning_rewards,
               (SELECT SUM(`count`)     FROM `" . _DB_PREFIX_ . "ets_am_product_view` WHERE id_product = rp.id_product AND id_seller=rp.id_seller) as view_count,                              
               rp.amount                                        as reward_per_product";
        $sql_part .= $order_clause;
        $sql .= $sql_part;
        $sql .= " LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
        $results = Db::getInstance()->executeS($sql);
        $temp = array();
        $total_filter = 0;
        foreach ($results as $result) {
            if ($result['total_order'] && $result['total_order'] > $result['view_count']) {
                $result['view_count'] = $result['total_order'];
            }
            if ($result['view_count'])
                $result['conversion_rate'] = Tools::ps_round((float)$result['total_order'] * 100 / $result['view_count'], 2);
            else
                $result['conversion_rate'] = 0;
            $product = new Product($result['id_product']);
            $result['link'] = self::generateAffiliateLinkForProduct($product, $context, false);
            $total_filter += $result['earning_rewards'];
            $total_earn = $result['earning_rewards'];
            $result['display_total_earn'] = Ets_AM::displayReward($total_earn);
            $result['c_rate'] = $result['conversion_rate'] . '%';
            $result['action'] = array(
                'link' => Ets_AM::getBaseUrlDefault('my_sale', array('id_product' => $result['id_product'])),
                'class' => 'btn btn-default',
            );
            $temp[] = $result;
        }
        $response = array();
        $response['current_page'] = $page;
        $response['total_page'] = (int)$total_page;
        $response['results'] = $temp;
        $response['total_data'] = (int)$total;
        $response['per_page'] = $limit;
        $response['total_filter'] = Ets_AM::displayReward($total_filter);
        $response['query'] = $query;
        return $response;
    }
    /**
     * @param $id_product
     * @param null $context
     * @return array
     * @throws PrestaShopDatabaseException
     */
    public static function getAffiliateCustomerInfo($id_product, $filter = null)
    {
        $context = Context::getContext();
        $type_date_filter = isset($filter['product_sale_filter']) && Validate::isString($filter['product_sale_filter']) && in_array($filter['product_sale_filter'], array('all_times', 'this_month', 'this_year', 'time_ranger')) ? $filter['product_sale_filter'] : 'all_times';
        $date_from_reward = isset($filter['product_sale_from']) && Validate::isDate($filter['product_sale_from']) ? pSQL($filter['product_sale_from']) : '';
        $date_to_reward = isset($filter['product_sale_to']) && Validate::isDate($filter['product_sale_to']) ? pSQL($filter['product_sale_to']) : '';
        $query = array();
        $limit = 20;
        $page = 1;
        $program = 'aff';
        $offset = ($page - 1) * $limit;
        $sql_part = "FROM `" . _DB_PREFIX_ . "ets_am_reward_product` r 
        INNER JOIN `" . _DB_PREFIX_ . "ets_am_reward` reward ON (r.id_ets_am_reward = reward.id_ets_am_reward)
        INNER JOIN `" . _DB_PREFIX_ . "orders` o ON r.id_order = o.id_order 
        WHERE r.program = '" . pSQL($program) . "'";
        $status = isset($filter['product_sale_status']) && Validate::isString($filter['product_sale_status']) && in_array($filter['product_sale_status'], array('all', 'approved', 'pending', 'canceled')) ? $filter['product_sale_status'] : 'all';
        if (!$status) {
            $query['product_sale_status'] = 'all';
        } else {
            if (in_array($status, array('pending', 'approved', 'canceled'))) {
                if ($status == 'pending') {
                    $s = 0;
                } elseif ($status == 'approved') {
                    $s = 1;
                } else {
                    $s = -1;
                }
                $sql_part .= " AND reward.status = " . (int)$s;
                $query['product_sale_status'] = $status;
            }
        }
        $query['product_sale_filter'] = $type_date_filter;
        if ($type_date_filter == 'this_month') {
            $sql_part .= " AND r.datetime_added >= '" . date('Y-m-01 00:00:00') . "' AND r.datetime_added <= '" . date('Y-m-t 23:59:59') . "'";
        } else if ($type_date_filter == 'this_year') {
            $sql_part .= " AND r.datetime_added >= '" . date('Y-01-01 00:00:00') . "' AND r.datetime_added <= '" . date('Y-12-31 23:59:59') . "'";
        } else if ($type_date_filter == 'time_ranger' && $date_from_reward && $date_to_reward) {
            $sql_part .= " AND r.datetime_added >= '" . date('Y-m-d 00:00:00', strtotime($date_from_reward)) . "' AND r.datetime_added <= '" . date('Y-m-d 23:59:59', strtotime($date_to_reward)) . "'";
        } else if ($type_date_filter == 'all_times') {
            $max_time = Db::getInstance()->getValue("SELECT MAX(datetime_added) FROM `" . _DB_PREFIX_ . "ets_am_reward` WHERE id_shop = " . (int)$context->shop->id);
            $min_time = Db::getInstance()->getValue("SELECT Min(datetime_added) FROM `" . _DB_PREFIX_ . "ets_am_reward` WHERE id_shop = " . (int)$context->shop->id);
            $start_date = $min_time;
            $end_date = $max_time;
            $sql_part .= " AND r.datetime_added >= '" . pSQL($start_date) . "' AND r.datetime_added <= '" . pSQL($end_date) . "'";
        } else {
            $sql_part .= " AND r.datetime_added >= '" . date('Y-m-01 00:00:00') . "' AND r.datetime_added <= '" . date('Y-m-t 23:59:59') . "'";
        }
        $sql_part .= ' AND r.id_product = ' . (int)$id_product . " AND r.id_seller = " . (int)$context->customer->id;
        $total = Db::getInstance()->getValue("SELECT COUNT(*) as `total` " . (string)$sql_part);
        $total_page = ceil($total / $limit);
        $sql_part .= " ORDER BY r.datetime_added DESC LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
        $sql = "SELECT r.id_order as id_order,o.reference, r.quantity, reward.status as status, (SELECT CONCAT(firstname, ' ', lastname) FROM `" . _DB_PREFIX_ . "customer` WHERE id_customer = o.id_customer) as customer_name,
       (SELECT email FROM `" . _DB_PREFIX_ . "customer` WHERE id_customer = o.id_customer) as customer_email, r.datetime_added as datetime_added, (r.amount * r.quantity) as earning_reward " . (string)$sql_part;
        $results = Db::getInstance()->executeS($sql);
        foreach ($results as &$result) {
            $result['reward_status'] = $result['status'];
            $result['earning_reward'] = Ets_AM::displayReward($result['earning_reward']);
        }
        return array(
            'current_page' => $page,
            'total_page' => (int)$total_page,
            'results' => $results,
            'query' => $query,
            'per_page' => $limit
        );
    }
    /**
     * @param $id_customer
     * @param int $stauts
     * @return bool
     * @throws PrestaShopException
     */
    public static function isCustomerJoinedAffiliateProgram($id_customer, $stauts = 1)
    {
        return Ets_AM::isCustomerJoinedProgram($id_customer, EAM_AM_AFFILIATE_REWARD, $stauts);
    }
    /**
     * @param $id_customer
     * @return bool|array
     */
    public static function isCustomerSuspendedOrBannedAffiliateProgram($id_customer)
    {
        $context = Context::getContext();
        $sql = "SELECT COUNT(*) FROM `" . _DB_PREFIX_ . "ets_am_user` WHERE id_customer = " . (int)$id_customer . " AND (`status` = -1 OR `aff` = -1 OR `aff` = -2) AND id_shop =  " . (int)$context->shop->id;
        $user = Db::getInstance()->getValue($sql);
        if ($user) {
            return true;
        }
        return false;
    }
    public static function isActive($id_customer = null)
    {
        $program_ready = (int)Configuration::get('ETS_AM_AFF_ENABLED');
        $enable_register = (int)Configuration::get('ETS_AM_AFF_REGISTER_REQUIRED');
        $context = Context::getContext();
        if (!$id_customer) {
            $id_customer = $context->customer->id;
        }
        if ($program_ready && self::isCustomerCanJoinAffiliateProgram2($id_customer)) {
            $user = Ets_User::getUserByCustomerId($id_customer);
            if ($enable_register) {
                if ($user && $user['status'] == 1 && $user['aff'] == 1) {
                    return true;
                }
            } else {
                if (!$user) {
                    return true;
                } else {
                    if ((int)$user['status'] == 1 && ((int)$user['aff'] == 1 || (int)$user['aff'] == 0)) {
                        return true;
                    }
                }
            }
        }
        return false;
    }
    public static function setAffCustomer($id_customer,$id_product){
        $aff = false;
        if (($aff_customer = (int)$id_customer) && ($customer = new Customer($aff_customer)) && Validate::isLoadedObject($customer) && Ets_Affiliate::isCustomerBelongToValidAffiliateGroup($customer)) {
            $aff = true;
        }
        if ($aff) {
            $module = Module::getInstanceByName('ets_affiliatemarketing');
            if (($aff_product = (int)$id_product) && ($product = new Product($aff_product)) && Validate::isLoadedObject($product)) {
                if ($aff_customer) {
                    $aff_products_cookie = $module->getCookie(EAM_AFF_PRODUCT_COOKIE);
                    $aff_customers = $module->getCookie(EAM_AFF_CUSTOMER_COOKIE);
                    if ($aff_customers)
                        $aff_customers_array = explode('-', $aff_customers);
                    else
                        $aff_customers_array = array();
                    if ($aff_products_cookie) {
                        $aff_products_cookie = explode('-', $aff_products_cookie);
                        if (!in_array($aff_product, $aff_products_cookie)) {
                            $aff_products_cookie[] = $aff_product;
                            $aff_customers_array[] = $aff_customer;
                        } else {
                            foreach ($aff_products_cookie as $key => $id_product) {
                                if ($id_product == $aff_product)
                                    $aff_customers_array[$key] = $aff_customer;
                            }
                        }
                    } else {
                        $aff_products_cookie = array($aff_product);
                        $aff_customers_array[] = $aff_customer;
                    }
                    if ($aff_customers_array)
                        $module->setCookie(EAM_AFF_CUSTOMER_COOKIE, implode('-', $aff_customers_array));
                    $aff_products_cookie = implode('-', $aff_products_cookie);
                    $module->setCookie(EAM_AFF_PRODUCT_COOKIE, $aff_products_cookie);
                    if (Context::getContext()->customer->logged)
                        $module->hookActionCartSave();
                }
                $visited_products = $module->getCookie(EAM_AFF_VISITED_PRODUCTS);
                if ($visited_products) {
                    $visited_products = explode('-', $visited_products);
                    if (!in_array($aff_product, $visited_products)) {
                        $visited_products[] = $aff_product;
                    }
                } else {
                    $visited_products = array($aff_product);
                }
                $visited_products = $visited_products ? implode('-', $visited_products):'';
                $module->setCookie(EAM_AFF_VISITED_PRODUCTS, $visited_products);
            }
        }
    }
    public static function getProductSaleS($id_product)
    {
        $sql = "SELECT rp.id_product as id_product, pl.name as product_name,
        (SELECT SUM(`quantity`) FROM `" . _DB_PREFIX_ . "ets_am_reward_product` WHERE id_product = rp.id_product AND program='aff' AND id_seller=rp.id_seller) as number_sale ,
        COUNT(DISTINCT rp.id_order) as total_order, 
        (SELECT SUM(`count`)     FROM `" . _DB_PREFIX_ . "ets_am_product_view` WHERE id_product = rp.id_product AND id_seller=rp.id_seller) as view_count, 
        (SELECT COUNT(`status`) FROM `" . _DB_PREFIX_ . "ets_am_reward_product` WHERE status = 0 AND id_product = rp.id_product AND program='aff' AND rp.id_seller) as pending, 
        (SELECT COUNT(`status`) FROM `" . _DB_PREFIX_ . "ets_am_reward_product` WHERE status = 1 AND id_product = rp.id_product AND program='aff' AND rp.id_seller) as approved, 
        (SELECT COUNT(`status`) FROM `" . _DB_PREFIX_ . "ets_am_reward_product` WHERE status = -1 AND id_product = rp.id_product AND program='aff' AND rp.id_seller) as canceled, 
        (SELECT COUNT(`status`) FROM `" . _DB_PREFIX_ . "ets_am_reward_product` WHERE status = -2 AND id_product = rp.id_product AND program='aff' AND rp.id_seller) as expired
        FROM `" . _DB_PREFIX_ . "ets_am_reward_product` rp
              LEFT JOIN `"._DB_PREFIX_."product_lang` pl ON pl.id_product = rp.id_product AND pl.id_lang ='".(int)Context::getContext()->language->id."'
              LEFT JOIN `" . _DB_PREFIX_ . "ets_am_reward` r ON r.id_ets_am_reward = rp.id_ets_am_reward
        WHERE rp.id_seller = " . (int)Context::getContext()->customer->id . "
              AND r.id_shop = " . (int)Context::getContext()->shop->id . "
              AND r.program = 'aff'
              AND rp.id_product = " . (int)$id_product;
        return Db::getInstance()->getRow($sql);
    }
}
