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
class Ets_Loyalty extends Ets_AM
{
    /**
     * ETS_Loyalty constructor.
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
    public static function isCustomerCanJoinLoyaltyProgram()
    {
        if (!Configuration::get('ETS_AM_LOYALTY_ENABLED')) {
            return false;
        }
        if (!self::isInLoyaltyTime()) {
            return false;
        }
        if ($min = Configuration::get('ETS_AM_LOYALTY_MIN_SPENT')) {
            $minSpent = self::calculateCustomerSpent();
            if ($minSpent < (float)$min) {
                return false;
            }
        }
        $config = Configuration::get('ETS_AM_LOYALTY_GROUPS');
        if ($config) {
            if ($config == 'ALL') {
                return true;
            } else {
                $groups = Context::getContext()->customer->getGroups();
                $configGroups = explode(',', $config);
                if ($groups) {
                    foreach ($groups as $group) {
                        if (in_array($group, $configGroups))
                            return true;
                    }
                }
            }
        }
        return false;
    }
    /**
     * @param CartCore $cart
     * @return null|string
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public static function getCartMessage($cart)
    {
        $context = Context::getContext();
        if (!Ets_Loyalty::isCustomerCanJoinLoyaltyProgram() || !Ets_AM::isCustomerJoinedProgram($context->customer->id,EAM_AM_LOYALTY_REWARD) || !$cart || !$cart->id) {

            return '';
        }
        $cart_reward = self::getCartReward($cart, $context, false, true);
        if (self::needExchange($context))
            $cart_reward = Tools::convertPrice($cart_reward, null, false);
        $min_loyalty = Configuration::get('ETS_AM_LOYALTY_MIN');
        if ($min_loyalty) {
            $min_loyalty = (float)$min_loyalty * $context->currency->conversion_rate;
        }
        if ($cart_reward > 0 && $min_loyalty != '' && (float)$min_loyalty > ($cart_total = (float)$cart->getOrderTotal(!Configuration::get('ETS_AM_LOYALTY_EXCLUDE_TAX'),Cart::ONLY_PRODUCTS))) {
            $msg = str_replace('[amount_left]', Ets_AM::displayReward($min_loyalty - $cart_total),
                strip_tags(Configuration::get('ETS_AM_LOYALTY_MSG_CART_1', $context->language->id))
            );
            return str_replace('[amount]', Ets_AM::displayReward($cart_reward), $msg);
        } elseif ($cart_reward > 0) {
            $cart_reward = self::getCartReward($cart, $context, false);
            return str_replace('[amount]', Ets_AM::displayReward($cart_reward),
                strip_tags(Configuration::get('ETS_AM_LOYALTY_MSG_CART_2', $context->language->id))
            );
        }
        return '';
    }
    /**
     * @param $subject
     * @param $reward
     */
    public static function sendEmailToAdminWhenNewRewardCreated($reward)
    {
        if (!Configuration::get('ETS_AM_ENABLED_EMAIL_ADMIN_RC') || !$reward || !$reward->id) {
            return;
        }
        $module = Module::getInstanceByName('ets_affiliatemarketing');
        $trans = Module::getInstanceByName('ets_affiliatemarketing')->getTranslates();
        $customer = new Customer($reward->id_customer);
        if ($customer) {
            $program_name = $trans['loyalty_program'];
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
            $data = array(
                '{customer}' => $customer->firstname . ' ' . $customer->lastname,
                '{type}' => $reward->program,
                '{program}' => $program_name,
                '{status}' => $status,
                '{reward_id}' => (int)$reward->id,
                '{reward}' => (int)$reward->id,
                '{date_created}' => $reward->datetime_added,
                '{amount}' => Ets_affiliatemarketing::displayPrice($reward->amount, (int)Configuration::get('PS_CURRENCY_DEFAULT'))
            );
            $adminEmails = Configuration::get('ETS_AM_EMAILS_CONFIRM');
            if (!$adminEmails) {
                return;
            }
            $adminEmails = explode(',', $adminEmails);
            $subject = array(
                'translation' => $module->l('A new reward was created', 'ets_loyalty'),
                'origin' => 'A new reward was created',
                'specific' => 'ets_loyalty'
            );
            foreach ($adminEmails as $email) {
                if (filter_var(trim($email), FILTER_VALIDATE_EMAIL)) {
                    Ets_aff_email::send(0, 'new_reward_loyalty_admin', $subject, $data, array('employee' => trim($email)));
                }
            }
        }
    }
    public static function sendEmailToCustomerWhenNewRewardCreated($reward)
    {
        if (!Configuration::get('ETS_AM_ENABLED_EMAIL_CUSTOMER_RC')) {
            return;
        }
        $trans = Module::getInstanceByName('ets_affiliatemarketing')->getTranslates();
        $customer = new Customer($reward->id_customer);
        $module = Module::getInstanceByName('ets_affiliatemarketing');
        if ($customer) {
            $program_name = $trans['loyalty_program'];
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
            $data = array(
                '{customer}' => $customer->firstname . ' ' . $customer->lastname,
                '{program}' => $program_name,
                '{status}' => $status,
                '{reward_id}' => $reward->id,
                '{reward}' => $reward->id,
                '{date_created}' => $reward->datetime_added,
                '{amount}' => Ets_affiliatemarketing::displayPrice($reward->amount, (int)Configuration::get('PS_CURRENCY_DEFAULT'))
            );
            $subject = array(
                'translation' => $module->l('A new reward created for you', 'ets_loyalty'),
                'origin' => 'A new reward created for you',
                'specific' => 'ets_loyalty'
            );
            Ets_aff_email::send($customer->id_lang, 'reward_created_customer', $subject, $data, $customer->email);
        }
    }
    /**
     * @param $subject
     * @param $reward
     */
    public static function sendEmailToCustomerWhenRewardValidated($reward)
    {
        if(!$reward || !$reward->id)
            return false;
        $trans = Module::getInstanceByName('ets_affiliatemarketing')->getTranslates();
        $customer = new Customer($reward->id_customer);
        $module = Module::getInstanceByName('ets_affiliatemarketing');
        if ($customer->id) {
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
            $data = array(
                '{title}' => 'A reward was approved',
                '{customer}' => $customer->firstname . ' ' . $customer->lastname,
                '{reward}' => $reward->id,
                '{amount}' => Ets_affiliatemarketing::displayPrice($reward->amount, (int)Configuration::get('PS_CURRENCY_DEFAULT')),
                '{type}' => $reward->program,
                '{program}' => $program,
                '{date_created}' => $reward->datetime_added,
                '{date_validated}' => $reward->datetime_validated,
                '{reward_id}' => $reward->id,
                '{note}' => $reward->note
            );
            $subject = array(
                'translation' => $module->l('Your reward was approved', 'ets_loyalty'),
                'origin' => 'Your reward was approved',
                'specific' => 'ets_loyalty'
            );
            Ets_aff_email::send($customer->id_lang, 'reward_validated', $subject, $data, $customer->email);
        }
    }
    /**
     * @param $subject
     * @param $reward
     */
    public static function sendEmailToAdminWhenRewardValidated($reward)
    {
        if(!$reward || !$reward->id)
            return false;
        $trans = Module::getInstanceByName('ets_affiliatemarketing')->getTranslates();
        $customer = new Customer($reward->id_customer);
        $module = Module::getInstanceByName('ets_affiliatemarketing');
        if ($customer) {
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
            $data = array(
                '{title}' => $trans['reward_validated'],
                '{customer}' => $customer->firstname . ' ' . $customer->lastname,
                '{reward}' => $reward->id,
                '{amount}' => Ets_affiliatemarketing::displayPrice($reward->amount, (int)Configuration::get('PS_CURRENCY_DEFAULT')),
                '{type}' => $reward->program,
                '{program}' => $program,
                '{date_created}' => $reward->datetime_added,
                '{date_validated}' => $reward->datetime_validated,
                '{reward_id}' => $reward->id,
                '{note}' => $reward->note
            );
            $subject = array(
                'translation' => $module->l('A reward was approved', 'ets_loyalty'),
                'origin' => 'A reward was approved',
                'specific' => 'ets_loyalty'
            );
            $adminEmail = Configuration::get('ETS_AM_EMAILS_CONFIRM');
            if ($adminEmail) {
                $adminEmail = explode(',', $adminEmail);
                foreach ($adminEmail as $to) {
                    Ets_aff_email::send(0, 'reward_validated_admin', $subject, $data, array('employee' => trim($to)));
                }
            }
        }
    }
    /**
     * @param $subject
     * @param $reward
     */
    public static function sendEmailToCustomerWhenRewardCanceled($reward)
    {
        $customer = new Customer($reward->id_customer);
        $module = Module::getInstanceByName('ets_affiliatemarketing');
        if ($customer) {
            $data = array(
                '{customer}' => $customer->firstname . ' ' . $customer->lastname,
                '{amount}' => Ets_affiliatemarketing::displayPrice($reward->amount, (int)Configuration::get('PS_CURRENCY_DEFAULT')),
                '{type}' => $reward->program,
                '{date_created}' => $reward->datetime_added,
                '{date_canceled}' => date('Y-m-d H:i:s'),
                '{program}' => 'Loyalty program',
                '{reward_id}' => $reward->id,
                '{reward}' => $reward->id,
                '{note}' => $reward->note
            );
            $subject = array(
                'translation' => $module->l('Your reward was canceled', 'ets_loyalty'),
                'origin' => 'Your reward was canceled',
                'specific' => 'ets_loyalty'
            );
            Ets_aff_email::send($customer->id_lang, 'reward_canceled', $subject, $data, $customer->email);
        }
    }
    /**
     * @param $subject
     * @param $reward
     */
    public static function sendEmailToAdminWhenRewardCanceled($reward)
    {
        $customer = new Customer($reward->id_customer);
        $module = Module::getInstanceByName('ets_affiliatemarketing');
        if ($customer) {
            $data = array(
                '{customer}' => $customer->firstname . ' ' . $customer->lastname,
                '{amount}' => Ets_affiliatemarketing::displayPrice($reward->amount, (int)Configuration::get('PS_CURRENCY_DEFAULT')),
                '{type}' => $reward->program,
                '{date_created}' => $reward->datetime_added,
                '{date_canceled}' => date('Y-m-d H:i:s'),
                '{program}' => 'Loyalty program',
                '{reward_id}' => $reward->id,
                '{reward}' => $reward->id,
                '{note}' => $reward->note
            );
            $adminEmail = Configuration::get('ETS_AM_EMAILS_CONFIRM');
            if ($adminEmail) {
                $subject = array(
                    'translation' => $module->l('A reward was canceled', 'ets_loyalty'),
                    'origin' => 'A reward was canceled',
                    'specific' => 'ets_loyalty'
                );
                $adminEmail = explode(',', $adminEmail);
                foreach ($adminEmail as $to) {
                    Ets_aff_email::send(0, 'reward_canceled_admin', $subject, $data, array('employee' => trim($to)));
                }
            }
        }
    }
    /**
     * @param $subject
     * @param $reward
     */
    public static function sendEmailToCustomerWhenRewardIsGoingToBeExpired($reward)
    {
        $customer = new Customer($reward->id_customer);
        $module = Module::getInstanceByName('ets_affiliatemarketing');
        if ($customer) {
            $days = Configuration::get('ETS_AM_LOYALTY_EMAIL_EXPIRED_DAY');
            $for_order = (int)Configuration::get('ETS_AM_AFF_ALLOW_BALANCE_TO_PAY');
            $for_voucher = (int)Configuration::get('ETS_AM_AFF_ALLOW_CONVERT_TO_VOUCHER');
            $for_withdraw = (int)Configuration::get('ETS_AM_AFF_ALLOW_WITHDRAW');
            $text_pay = '';
            if ($for_order) {
                $text_pay .= ' pay for order,';
            }
            if ($for_voucher) {
                $text_pay .= ' convert to voucher,';
            }
            if ($for_withdraw) {
                $text_pay .= ' withdrawal,';
            }
            $text_pay = trim($text_pay);
            $text_pay = trim($text_pay, ',');
            $data = array(
                '{customer}' => $customer->firstname . ' ' . $customer->lastname,
                '{amount}' => Ets_affiliatemarketing::displayPrice($reward->amount, (int)Configuration::get('PS_CURRENCY_DEFAULT')),
                '{type}' => $reward->program,
                '{day}' => (int)$days,
                '{pay_for}' => $text_pay ? 'You can use them to ' . $text_pay : '',
                '{reward_id}' => $reward->id,
                '{reward}' => $reward->id,
                '{date_created}' => $reward->datetime_added,
            );
            $subject = array(
                'translation' => $module->l('Your reward is going to be expired', 'ets_loyalty'),
                'origin' => 'Your reward is going to be expired',
                'specific' => 'ets_loyalty'
            );
            Ets_aff_email::send($customer->id_lang, 'reward_going_to_be_expired', $subject, $data, $customer->email);
        }
    }
    /**
     * @param $subject
     * @param $reward
     */
    public static function sendEmailToCustomerWhenRewardExpired($reward)
    {
        $customer = new Customer($reward->id_customer);
        $module = Module::getInstanceByName('ets_affiliatemarketing');
        $trans = $module->getTranslates();
        if ($customer && Validate::isLoadedObject($customer)) {
            $data = array(
                '{customer}' => $customer->firstname . ' ' . $customer->lastname,
                '{title}' => $trans['reward_expired'],
                '{amount}' => Ets_affiliatemarketing::displayPrice($reward->amount, (int)Configuration::get('PS_CURRENCY_DEFAULT')),
                '{type}' => $reward->program,
                '{reward_id}' => $reward->id,
                '{reward}' => $reward->id,
                '{date_created}' => $reward->datetime_added,
            );
            $subject = array(
                'translation' => $module->l('Your reward was expired', 'ets_loyalty'),
                'origin' => 'Your reward was expired',
                'specific' => 'ets_loyalty'
            );
            Ets_aff_email::send($customer->id_lang, 'reward_expired', $subject, $data, $customer->email);
        }
    }
    /**
     * @param $product
     * @param null $context
     * @return string|null
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public static function getLoyaltyMessageOnProductPage($product, $context = null, $cart = null)
    {
        if (in_array(Configuration::get('ETS_AM_LOYALTY_BASE_ON'), array('SPC_FIXED', 'SPC_PERCENT'))) {
            return null;
        }
        if (!$context) {
            $context = Context::getContext();
        }
        if (!Ets_Loyalty::validateLoyaltyProduct($product)) {
            return null;
        }
        if ($cart && Configuration::get('ETS_AM_LOYALTY_MIN')) {
            $message = Configuration::get('ETS_AM_LOYALTY_MSG_CART_REQUIRED', $context->language->id);
        } else {
            $message = Configuration::get('ETS_AM_LOYALTY_MSG_PRODUCT', $context->language->id);
        }
        if (!$message) {
            return null;
        }
        if (!self::isCustomerCanJoinLoyaltyProgram()) {
            return null;
        }
        if (!self::isCustomerJoinedProgram($context->customer->id, EAM_AM_LOYALTY_REWARD)) {
            return null;
        }
        $amount = 0;
        $sql = "SELECT * FROM `" . _DB_PREFIX_ . "ets_am_loy_reward` WHERE `id_product` = " . (int)$product['id_product'] . " AND id_shop = " . (int)$context->shop->id;
        $productRewardSetting = Db::getInstance()->getRow($sql);
        if ($productRewardSetting && !(int)$productRewardSetting['use_default']) {
            if ($productRewardSetting['base_on'] == 'FIXED') {
                $amount = (float)$productRewardSetting['amount'];
            } elseif ($productRewardSetting['base_on'] == 'CART') {
                $excludeTax = (int)Configuration::get('ETS_AM_LOYALTY_EXCLUDE_TAX');
                if ($excludeTax) {
                    $amount = (float)Tools::convertPrice((float)$product['price_with_tax_with_reduction'], null, false) * (float)$productRewardSetting['gen_percent'] / 100;
                } else {
                    $amount = (float)Tools::convertPrice((float)$product['price_without_tax_with_reduction'], null, false) * (float)$productRewardSetting['gen_percent'] / 100;
                }
            }
        } else {
            $productReward = self::loyaltyProductReward($product);
            if (is_array($productReward)) {
                $amount = (float)$productReward['amount'];
            } else {
                $amount = (float)$productReward;
            }
        }
        if ($amount != 0) {
            $min = Configuration::get('ETS_AM_QTY_MIN');
            if ($productRewardSetting && count($productRewardSetting) > 0 && $productRewardSetting['use_default'] == 0) {
                $min = $productRewardSetting['qty_min'];
            }
            if (!$min) {
                $min = 1;
            } else {
                $min = (int)$min;
            }
            $maxReward = Configuration::get('ETS_AM_LOYALTY_MAX');
            if ($maxReward !== false && $maxReward !== NULL && $maxReward !== '') {
                if ($amount > (float)$maxReward) {
                    $amount = $maxReward;
                }
            }
            $message = strip_tags($message);
            $message = str_replace('[amount]', Ets_AM::displayReward($amount), $message);
            $message = str_replace('[min_productnumber]', $min, $message);
            if ($cart && Configuration::get('ETS_AM_LOYALTY_MIN')) {
                $message = str_replace('[cart_minimum_amount]', Ets_AM::displayReward((int)Configuration::get('ETS_AM_LOYALTY_MIN')), $message);
            }
            return $message;
        }
        return null;
    }
    /**
     * @param $product
     * @param string $type
     * @param null $context
     * @return float|int|array
     * @throws PrestaShopException
     */
    public static function loyaltyProductReward($product, $type = 'product', $context = null, $get_force_reward = false)
    {
        if (!$context) {
            $context = Context::getContext();
        }
        if (!self::validateLoyaltyProduct($product)) {
            return 0;
        }
        if ($type == 'cart') {
            $price = (float)$product['price_with_reduction'];
            $priceExclTax = (float)$product['price_with_reduction_without_tax'];
        } elseif ($type == 'order') {
            $price = (float)$product['unit_price_tax_incl'];
            $priceExclTax = (float)$product['unit_price_tax_excl'];
        } else {
            $price = $product['price_with_tax_with_reduction'];
            $priceExclTax = (float)$product['price_without_tax_with_reduction'];
        }
        $baseOn = Configuration::get('ETS_AM_LOYALTY_BASE_ON');
        $excludeTax = Configuration::get('ETS_AM_LOYALTY_EXCLUDE_TAX');
        $notForDiscount = Configuration::get('ETS_AM_LOYALTY_NOT_FOR_DISCOUNTED');
        if (self::needExchange($context)) {
            $price = Tools::convertPrice($price, null, false);
            $priceExclTax = Tools::convertPrice($priceExclTax, null, false);
        }
        if ($notForDiscount) {
            if (isset($product['reduction']) && $product['reduction'] > 0) {
                return 0;
            }
            if (isset($product['reduction_percent']) && $product['reduction_percent'] > 0)
                return 0;
            if (isset($product['reduction_amount']) && $product['reduction_amount'] > 0)
                return 0;
        }
        if ($excludeTax) {
            $p = $priceExclTax;
        } else {
            $p = $price;
        }
        $enableMultipleProduct = Configuration::get('ETS_AM_LOYALTY_MULTIPE_BY_PRODUCT');
        $sql = "SELECT * FROM `" . _DB_PREFIX_ . "ets_am_loy_reward` WHERE `id_product` = " . (int)$product['id_product'];
        $productRewardSetting = Db::getInstance()->getRow($sql);
        if ($productRewardSetting && count($productRewardSetting) && (int)$productRewardSetting['use_default'] != 1) {
            $default = false;
            $cal_by = $productRewardSetting['base_on'];
        } else {
            $default = true;
            $cal_by = $baseOn;
        }
        if ($type == 'cart') {
            $min = Configuration::get('ETS_AM_QTY_MIN');
            if (!$default) {
                if ($productRewardSetting['qty_min'] && $productRewardSetting['qty_min'] > 0) {
                    $min = $productRewardSetting['qty_min'];
                }
            }
            $cart_quantity = (isset($product['cart_quantity']) ? (float)$product['cart_quantity'] : (float)$product['quantity']);
            if ($cart_quantity < (int)$min && !$get_force_reward) {
                return 0;
            }
        }
        if ($type == 'order') {
            $min = Configuration::get('ETS_AM_QTY_MIN');
            if (!$default) {
                if ($productRewardSetting['qty_min'] && $productRewardSetting['qty_min'] > 0) {
                    $min = $productRewardSetting['qty_min'];
                }
            }
            if ($product['product_quantity'] < (int)$min && !$get_force_reward) {
                return 0;
            }
        }
        if ($default) {
            $minQty = Configuration::get('ETS_AM_QTY_MIN');
        } else {
            $minQty = $productRewardSetting['qty_min'];
        }
        if ($type == 'cart') {
            if ($minQty && $minQty != 0 && (int)$minQty > (int)$product['cart_quantity'] && !$get_force_reward) {
                return 0;
            }
        } elseif ($type == 'order') {
            if ($minQty && $minQty != 0 && (int)$minQty > (int)$product['product_quantity'] && !$get_force_reward) {
                return 0;
            }
        } else {
            if (!$minQty || $minQty == 0) {
                $minQty = 1;
            } elseif ($get_force_reward) {
                $minQty = 1;
            }
        }
        if ($cal_by == 'FIXED') {
            if ($default) {
                $amount = Configuration::get('ETS_AM_LOYALTY_AMOUNT');
            } else {
                $amount = $productRewardSetting['amount'];
            }
            if (!$amount) {
                return 0;
            }
            $amount = (float)$amount;
            if ($enableMultipleProduct)
                if ($type == 'cart') {
                    $reward = $amount * (isset($product['cart_quantity']) ? (float)$product['cart_quantity'] : (float)$product['quantity']);
                } elseif ($type == 'order') {
                    $reward = $amount * (float)$product['product_quantity'];
                } else {
                    if ($default) {
                        if ((float)$minQty == 0) {
                            $minQty = 1;
                        }
                    } else {
                        $minQty = $productRewardSetting['qty_min'];
                    }
                    $reward = $amount * $minQty;
                }
            else
                $reward = $amount;
            return $reward;
        } elseif ($cal_by == 'CART') {
            if ($default) {
                $percentage = Configuration::get('ETS_AM_LOYALTY_GEN_PERCENT');
            } else {
                $percentage = $productRewardSetting['gen_percent'];
            }
            if (!$percentage) {
                return 0;
            }
            $reward = $p * (float)$percentage / 100;
            if ($enableMultipleProduct) {
                if ($type == 'cart') {
                    $reward = $reward * (isset($product['cart_quantity']) ? (float)$product['cart_quantity'] : (float)$product['quantity']);
                } elseif ($type == 'order') {
                    $reward = $reward * $product['product_quantity'];
                } else {
                    if ($minQty == 0) {
                        $minQty = 1;
                    }
                    return $reward * $minQty;
                }
            }
            return $reward;
        } elseif ($cal_by == 'NOREWARD') {
            return 0;
        } else {
            $cart_total = 0;
            if ($context->cart && $context->cart->id)
                $cart_total = (float)$context->cart->getOrderTotal(!Configuration::get('ETS_AM_LOYALTY_EXCLUDE_TAX')) - (float)$context->cart->getOrderTotal(!Configuration::get('ETS_AM_LOYALTY_EXCLUDE_TAX'), Cart::ONLY_SHIPPING);
            $cart_reward = !$cart_total ? 0 : ($cal_by != 'SPC_PERCENT' ? (float)Configuration::get('ETS_AM_LOYALTY_AMOUNT') : ($cart_total * 0.01 * (float)Configuration::get('ETS_AM_LOYALTY_GEN_PERCENT')));
            $currency = new Currency($context->cart->id_currency);
            $cart_reward = (float)$cart_reward / $currency->conversion_rate;
            $min_loyalty = Configuration::get('ETS_AM_LOYALTY_MIN');
            $max_loyalty = Configuration::get('ETS_AM_LOYALTY_MAX');
            if ($min_loyalty) {
                $min_loyalty = (float)$min_loyalty * $context->currency->conversion_rate;
            }
            if ($min_loyalty != '' && (float)$min_loyalty > $cart_total && !$get_force_reward)
                return 0;
            if ($max_loyalty != '' && (float)$max_loyalty < $cart_reward)
                $cart_reward = $max_loyalty;
            return $cart_reward;
        }
        return 0;
    }

    public static function getCartReward(Cart $cart, $context = null, $need_product = false, $get_force_reward = false)
    {
        if(!$cart || !$cart->id)
            return 0;

        if (!$context)
            $context = Context::getContext();
        $loyalty_product = ($loyalty = Configuration::get('ETS_AM_LOYALTY_BASE_ON')) != 'SPC_FIXED' && $loyalty && $loyalty != 'SPC_PERCENT';
        $cart_total = (float)$cart->getOrderTotal(!Configuration::get('ETS_AM_LOYALTY_EXCLUDE_TAX'),Cart::ONLY_PRODUCTS) ;
        $min_loyalty = (float)Configuration::get('ETS_AM_LOYALTY_MIN');
        if ($min_loyalty) {
            $min_loyalty = (float)$min_loyalty * $context->currency->conversion_rate;
        }
        if ($min_loyalty != 0 && $min_loyalty > $cart_total && !$get_force_reward)
            return 0;
        $cart_reward = $loyalty_product ? 0 : ($loyalty != 'SPC_PERCENT' ? (float)Tools::convertPrice(Configuration::get('ETS_AM_LOYALTY_AMOUNT')) : ($cart_total * 0.01 * (float)Configuration::get('ETS_AM_LOYALTY_GEN_PERCENT')));
        $currentCurrency = new Currency($cart->id_currency);
        $cart_reward = (float)$cart_reward / (float)$currentCurrency->conversion_rate;
        $products = array();
        $max_loyalty = Configuration::get('ETS_AM_LOYALTY_MAX');
        if ($loyalty_product && ($cart_products = $cart->getProducts())) {
            foreach ($cart_products as $p) {
                $product_reward = self::loyaltyProductReward($p, 'cart', $context);
                if (isset($product_reward['calculate']) && $product_reward['calculate'] === 'FIXED')
                    $cart_reward = (float)$product_reward['amount'];
                else
                    $cart_reward += $product_reward;
                if (!$need_product && $max_loyalty != '' && $cart_reward > $max_loyalty)
                    break;
                if ($product_reward && $need_product) {
                    $p['reward_amount'] = (int)$p['quantity'] ? ($cart_reward / (int)$p['quantity']) : 0;
                    $products[] = $p;
                }
            }
        }
        if ($max_loyalty != 0 && (float)$max_loyalty < $cart_reward)
            $cart_reward = (float)$max_loyalty;
        if (!$loyalty_product || !$need_product)
            return $cart_reward;
        if ($need_product)
            return array('reward' => $cart_reward, 'products' => $products);
    }
    public static function getOrderReward(Order $order, $context = null, $need_product = false, $get_force_reward = false)
    {
        if(!$order || !$order->id)
            return 0;
        if (!$context)
            $context = Context::getContext();
        $loyalty_product = ($loyalty = Configuration::get('ETS_AM_LOYALTY_BASE_ON')) != 'SPC_FIXED' && $loyalty && $loyalty != 'SPC_PERCENT';
        $cart_total = Configuration::get('ETS_AM_LOYALTY_EXCLUDE_TAX') ? $order->total_paid_tax_excl - $order->total_shipping_tax_excl : $order->total_paid_tax_incl - $order->total_shipping_tax_incl;
        if (($min_loyalty = (float)Configuration::get('ETS_AM_LOYALTY_MIN')) != '' && $min_loyalty > $cart_total && !$get_force_reward) {
            return 0;
        }
        $cart_reward = $loyalty_product ? 0 : ($loyalty != 'SPC_PERCENT' ? (float)Configuration::get('ETS_AM_LOYALTY_AMOUNT') : ($cart_total * 0.01 * (float)Configuration::get('ETS_AM_LOYALTY_GEN_PERCENT')));
        $cart_reward = (float)$cart_reward / (float)$order->conversion_rate;
        $cartRewardPercent = $cart_reward;
        if ($loyalty == 'FIXED') {
            $cart_reward = (float)Configuration::get('ETS_AM_LOYALTY_AMOUNT');
        }
        $products = array();
        $max_loyalty = Configuration::get('ETS_AM_LOYALTY_MAX');
        $cart_products = $order->getProducts();
        if ($loyalty_product || $cart_products) {
            if ($loyalty == 'CART' && !isset($cart_products)) {
                $cart_products = $order->getProducts();
            }
            if (isset($cart_products) && $cart_products) {
                $cart_reward = 0;
                foreach ($cart_products as $p) {
                    $context->currency = new Currency($order->id_currency);
                    if (!$context->cart) {
                        $context->cart = new Cart($order->id_cart);
                    }
                    $product_reward = self::loyaltyProductReward($p, 'order', $context);
                    if ($product_reward && $loyalty == 'SPC_FIXED')
                        return $product_reward;
                    if (isset($product_reward['calculate']) && $product_reward['calculate'] === 'FIXED')
                        $cart_reward = (float)$product_reward['amount'];
                    elseif (isset($loyalty) && $loyalty == 'SPC_PERCENT')
                        $cart_reward = (float)$cartRewardPercent;
                    else
                        $cart_reward += $product_reward;
                    if (!$need_product && $max_loyalty != '' && $cart_reward > $max_loyalty)
                        break;
                    if ($product_reward && $need_product) {
                        $p['reward_amount'] = (int)$p['product_quantity'] ? $product_reward : 0;
                        $products[] = $p;
                    }
                }
            }
        }
        if ($max_loyalty != '' && $max_loyalty < $cart_reward)
            $cart_reward = $max_loyalty;
        if (!$loyalty_product || !$need_product)
            return $cart_reward;
        if ($need_product)
            return array('reward' => $cart_reward, 'products' => $products);
    }
    /**
     * @param $params
     * @param null $context
     * @param bool $need_product
     * @return array|float|int
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public static function calculateCartTotalReward($params, $need_product = false)
    {
        $context = Context::getContext();
        if (!(isset($params['order'])) || !$params['order'] || Ets_Loyalty::isCustomerSuspendedOrBannedLoyaltyProgram($context->customer->id)
            || !Ets_Loyalty::isCustomerCanJoinLoyaltyProgram() || !Ets_Loyalty::isCustomerJoinedProgram($context->customer->id,EAM_AM_LOYALTY_REWARD))
            return 0;
        return self::getOrderReward($params['order'], $context, $need_product);
    }
    /**
     * @param $id_customer
     * @return bool|array
     */
    public static function isCustomerSuspendedOrBannedLoyaltyProgram($id_customer)
    {
        $context = Context::getContext();
        $sql = "SELECT COUNT(*) FROM `" . _DB_PREFIX_ . "ets_am_user` WHERE `id_customer` = " . (int)$id_customer . " AND (`status` = -1 OR `loy` = -1 OR `loy` = -2) AND id_shop = " . (int)$context->shop->id;
        $user = (int)Db::getInstance()->getValue($sql);
        if ($user) {
            return true;
        }
        return false;
    }
    public static function loyRewardUsed($usageLOY, $id_reward_usage, $id_customer = 0)
    {
        if (!$id_customer)
            $id_customer = Context::getContext()->customer->id;
        $id_shop = Context::getContext()->shop->id;
        $sql = 'SELECT id_ets_am_reward,amount FROM `' . _DB_PREFIX_ . 'ets_am_reward` WHERE used=0 AND id_shop="' . (int)$id_shop . '" AND id_customer="' . (int)$id_customer . '" AND status=1 AND deleted=0 AND program="' . pSQL(EAM_AM_LOYALTY_REWARD) . '" ORDER BY datetime_added ASC';
        $rewards = Db::getInstance()->executeS($sql);
        if ($rewards) {
            foreach ($rewards as $reward) {
                if ($usageLOY > 0) {
                    Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'ets_am_reward` SET used="' . (int)$id_reward_usage . '" WHERE id_ets_am_reward= "' . (int)$reward['id_ets_am_reward'] . '"');
                    $usageLOY = Tools::ps_round($usageLOY - $reward['amount'], 2);
                    if ($usageLOY < 0) {
                        $usageLOY = -1 * $usageLOY;
                        $new_reward = new Ets_AM($reward['id_ets_am_reward']);
                        $new_reward->amount = $new_reward->amount - $usageLOY;
                        if ($new_reward->update()) {
                            $new_reward->amount = $usageLOY;
                            $new_reward->used = 0;
                            unset($new_reward->id);
                            $new_reward->add();
                        }
                        break;
                    }
                } else
                    break;
            }
        }
    }
}
