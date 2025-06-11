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

class Ets_affiliatemarketingVoucherModuleFrontController extends  Ets_affiliatemarketingAllModuleFrontController
{
    public $auth = true;
    public $guestAllowed = false;
    public $authRedirection = URL_EAM_VOUCHER;
    /**
     * @throws PrestaShopException
     */
    public function __contruct()
    {
        parent::__construct();
    }

    /**
     * @throws PrestaShopException
     */
    public function init()
    {
        parent::init();
        if (!$this->module->is17) {
            $this->display_column_left = false;
            $this->display_column_right = false;
        }
    }

    /**
     * @throws PrestaShopException
     */
    public function initContent()
    {
        parent::initContent();
        if(!$this->context->customer->isLogged())
            Tools::redirect($this->context->link->getPageLink('my-account'));
        if (! Configuration::get('ETS_AM_AFF_ALLOW_CONVERT_TO_VOUCHER')) {
            Tools::redirect('404');
        }
        //Set meta
        $page= 'module-'.$this->module->name.'-voucher';
        $meta = Meta::getMetaByPage($page,$this->context->language->id);
        $this->setMetas(array(
            'title' => isset($meta['title']) && $meta['title'] ? $meta['title'] :  $this->module->l('Vouchers', 'voucher'),
            'keywords' => isset($meta['keywords']) && $meta['keywords'] ? $meta['keywords'] : $this->module->l('Vouchers', 'voucher'),
            'description' =>isset($meta['description']) && $meta['description'] ? $meta['description'] : $this->module->l('Vouchers', 'voucher'),
        ));
        $context = $this->context;
        if (Tools::isSubmit('eam_apply_voucher')) {
            if(!$this->isTokenValid()){
                die(json_encode(array(
                    'success' => false,
                    'message' => $this->module->l('Token is invalid', 'voucher')
                )));
            }
            $response = array();
            $id_cart_rule = (int)Tools::getValue('id_cart_rule');
            $cart_rule = new CartRule((int) $id_cart_rule);
            $cart = $this->context->cart;
            if (! count($cart->getProducts())) {
                $response['success'] = false;
                $response['message'] = $this->module->l('Your cart is empty.', 'voucher');
                die(json_encode($response));
            }
            if (! Validate::isUnsignedInt($id_cart_rule) || !$cart_rule->id) {
                $response['success'] = false;
                $response['message'] = $this->module->l('Could not find your voucher', 'voucher');
                die(json_encode($response));
            }
            if ((int)$cart_rule->quantity <= 0 || $cart_rule->active == 0) {
                $response['success'] = false;
                $response['message'] = $this->module->l('Your voucher has been used.', 'voucher');
                die(json_encode($response));
            }
            $count = Ets_Reward_Usage::getCountRewardUsageByIdVoucher($cart_rule->id,$this->context->customer->id);
            if ((int)$count <= 0) {
                $response['success'] = false;
                $response['message'] = $this->module->l('Your voucher code is not available for your account.', 'voucher');
                die(json_encode($response));
            }
            if ($error = $cart_rule->checkValidity($this->context)) {
                die(json_encode(array(
                    'success' => false,
                    'message' => $error
                )));
            }
            $voucherCode ='';
            if(!Ets_Voucher::canUseCartRule($this->context->cart->id, $id_cart_rule, $voucherCode)){
                die(json_encode(array(
                    'success' => false,
                    'message' => sprintf($this->module->l('Cannot use voucher code %s with others voucher code','voucher'), $voucherCode),
                )));
            }
            if ($cart->addCartRule((int)$cart_rule->id)) {
                die(json_encode(array(
                    'success' => true,
                    'message' => $this->module->l('The voucher code applied to cart successfully.', 'voucher')
                )));
            }
            die(json_encode(array(
                'success' => false,
                'message' => $this->module->l('There was problem while trying to apply voucher code', 'voucher')
            )));
        }
        $allow_convert = (bool)Configuration::get('ETS_AM_AFF_ALLOW_CONVERT_TO_VOUCHER');
        $assign = array(
            'eam_url' => Ets_AM::getBaseUrl(),
            'eam_confirm' => null,
            'link_reward' => Ets_AM::getBaseUrlDefault('dashboard'),
            'link_reward_history' => Ets_AM::getBaseUrlDefault('history'),
            'link_withdraw' => Ets_AM::getBaseUrlDefault('withdraw'),
            'link_voucher' =>Ets_AM::getBaseUrlDefault('voucher'),
            'allow_convert_voucher' => $allow_convert,
            'ETS_AM_VOUCHER_AVAILABILITY' => (int)Configuration::get('ETS_AM_VOUCHER_AVAILABILITY'),
            'eam_voucher_min' => Configuration::get('ETS_AM_MIN_BALANCE_REQUIRED_FOR_VOUCHER') !== null && Configuration::get('ETS_AM_MIN_BALANCE_REQUIRED_FOR_VOUCHER') !== false &&Configuration::get('ETS_AM_MIN_BALANCE_REQUIRED_FOR_VOUCHER') !== '' ? Tools::displayPrice(Tools::convertPrice((float)Configuration::get('ETS_AM_MIN_BALANCE_REQUIRED_FOR_VOUCHER'))) : '',
            'eam_voucher_max' => Configuration::get('ETS_AM_MAX_BALANCE_REQUIRED_FOR_VOUCHER') !== null && Configuration::get('ETS_AM_MAX_BALANCE_REQUIRED_FOR_VOUCHER') !== false &&Configuration::get('ETS_AM_MAX_BALANCE_REQUIRED_FOR_VOUCHER') !== '' ? Tools::displayPrice(Tools::convertPrice((float)Configuration::get('ETS_AM_MAX_BALANCE_REQUIRED_FOR_VOUCHER'))) : '',
        );
        $config = $this->getVoucherConfiguration();
        $errors = array();
        $total_earn = Ets_Reward_Usage::getTotalEarn(null, $context->customer->id, $context);
        $total_spent = Ets_Reward_Usage::getTotalSpent($context->customer->id, false, null, $context);
        $total_balance = $total_earn - $total_spent;

        if (Tools::isSubmit('eam-submit-voucher')) {
            $amount = Tools::getValue('EAM_VOUCHER_AMOUNT');
            $amount_post = $amount;
            if (!$amount) {
                $errors['EAM_VOUCHER_AMOUNT'] = $this->module->l('The amount field is required.', 'voucher');
            } else {
                if (! Validate::isFloat($amount)) {
                    $errors['EAM_VOUCHER_AMOUNT'] = $this->module->l('The amount field must be a number.', 'voucher');
                }
            }
            if (count($errors)) {
                $assign['eam_form_error'] = $errors;
                $old_data = array(
                    'EAM_VOUCHER_AMOUNT' => $amount
                );
                $assign['eam_form_data'] = $old_data;
            } else {
                $amount = (float)$amount;
                if (Ets_AM::needExchange($this->context)) {
                    $amount = Tools::ps_round(Tools::convertPrice($amount, null, false),2);
                }
                if ($config['min'] >0 && $amount < $config['min']) {
                    $errors['EAM_VOUCHER_AMOUNT'] = $this->module->l('Min amount to convert ', 'voucher') . Tools::displayPrice($config['min']);

                } elseif ($config['max'] >0 && $amount > $config['max']) {
                    $errors['EAM_VOUCHER_AMOUNT'] = $this->module->l('Max amount to convert ', 'voucher') . Tools::displayPrice($config['max']);

                } elseif (Tools::ps_round($amount,6) > Tools::ps_round($total_balance,6)) {
                    $errors['EAM_VOUCHER_AMOUNT'] = $this->module->l('Your balance is not enough for convert.', 'voucher');
                }
                if (count($errors)) {
                    $assign['eam_form_error'] = $errors;
                    $old_data = array(
                        'EAM_VOUCHER_AMOUNT' => $amount_post
                    );
                    $assign['eam_form_data'] = $old_data;
                } else {
                    $ETS_AM_VOUCHER_AVAILABILITY = (int)Configuration::get('ETS_AM_VOUCHER_AVAILABILITY');
                    $cart_rule = new CartRule();
                    $cart_rule->id_customer = $context->customer->id;
                    $cart_rule->date_from = date('Y-m-d H:i:s');
                    $cart_rule->date_to = $ETS_AM_VOUCHER_AVAILABILITY ? date('Y-m-d H:i:s', strtotime("+$ETS_AM_VOUCHER_AVAILABILITY days")): date('Y-m-d H:i:s', strtotime("+3600 days"));
                    $cart_rule->quantity = 1;
                    $cart_rule->highlight=1;
                    $cart_rule->active =1;
                    $languages = Language::getLanguages(false);
                    if ($languages)
                    {
                        $rule_name = array();
                        foreach ($languages as $lang){
                            $rule_name[(int)$lang['id_lang']] = $this->module->l('Converted from reward balance', 'voucher');
                        }
                        $cart_rule->name = $rule_name;
                    }
                    else
                        $cart_rule->name = array($this->context->language->id => $this->module->l('Converted from reward balance', 'voucher'));
                    $code = Ets_AM::generatePromoCode(null, $context);
                    $cart_rule->code = $code;
                    $cart_rule->reduction_amount = $amount;
                    $cart_rule->reduction_tax = 1;
                    if ($cart_rule->add()) {
                        $totalLoy = Ets_Reward_Usage::getTotalEarn('loy', $context->customer->id, $context);
                        $totalSpentLoy = Ets_Reward_Usage::getTotalSpentLoy($context->customer->id, false, null, $context);
                        $remainLoy = (float)$totalLoy - (float)$totalSpentLoy;

                        $usageLOY = 0;
                        $usageANR = 0;
                        if($remainLoy > (float)$amount){
                            $usageLOY = $amount;
                        }
                        else{
                            if($remainLoy > 0){
                                $usageLOY = $remainLoy;
                                $usageANR = (float)$amount - (float)$usageLOY;
                            }
                            else{
                                $usageANR = (float)$amount;
                            }
                        }

                        if($usageLOY > 0){
                            $rewardUsage = new Ets_Reward_Usage();
                            $rewardUsage->id_customer = $this->context->customer->id;
                            $rewardUsage->id_currency = (int)Configuration::get('PS_CURRENCY_DEFAULT');
                            $rewardUsage->id_shop = $context->shop->id;
                            $rewardUsage->id_voucher = $cart_rule->id;
                            $rewardUsage->status = 1;
                            $rewardUsage->type = 'loy';
                            $rewardUsage->datetime_added = date('Y-m-d H:i:s');
                            $rewardUsage->amount = $usageLOY;
                            $rewardUsage->note = $this->module->l('Convert into voucher', 'voucher');
                            $rewardUsage->save();
                            Ets_Loyalty::loyRewardUsed($usageLOY,$rewardUsage->id,$rewardUsage->id_customer);
                        }
                        if($usageANR > 0){
                            $programs = array('mnu','aff','ref');
                            foreach($programs as $program)
                            {
                                $total = Ets_Reward_Usage::getTotalEarn($program, $context->customer->id, $context);
                                $totalSpent = Ets_Reward_Usage::getTotalSpent($context->customer->id, false, null, $context,$program);
                                $remain = (float)$total - (float)$totalSpent;
                                if($remain >0)
                                {
                                    if($usageANR < $remain)
                                    {
                                        $usage = $usageANR;
                                        $continue = false;
                                    }
                                    else
                                    {
                                        $usage = $remain;
                                        $continue=true;
                                        $usageANR = $usageANR-$remain;
                                    }
                                    $rewardUsage = new Ets_Reward_Usage();
                                    $rewardUsage->id_customer = $this->context->customer->id;
                                    $rewardUsage->id_currency = (int)Configuration::get('PS_CURRENCY_DEFAULT');
                                    $rewardUsage->id_shop = $context->shop->id;
                                    $rewardUsage->id_voucher = $cart_rule->id;
                                    $rewardUsage->status = 1;
                                    $rewardUsage->type = $program;
                                    $rewardUsage->datetime_added = date('Y-m-d H:i:s');
                                    $rewardUsage->amount = $usage;
                                    $rewardUsage->note =  $this->module->l('Convert into voucher', 'voucher');;
                                    $rewardUsage->save();
                                    if(!$continue)
                                        break;
                                }
                            }
                            
                        }
                        $data = array(
                            '{customer}' => $this->context->customer->firstname.' '.$this->context->customer->lastname,
                            '{discount_code}' => $code, 
                            '{discount_value}' => Ets_affiliatemarketing::displayPrice($amount)
                        );
                        $subjects = array(
                            'translation' => $this->module->l('You have successfully converted a voucher!','voucher'),
                            'origin'=> 'You have successfully converted a voucher!',
                            'specific'=>'voucher'
                        );
                        Ets_aff_email::send($this->context->language->id,'convert_into_voucher',$subjects,$data,$this->context->customer->email);
                    }
                    $message = $this->module->l('You have successfully converted [r_reward] into voucher code: [r_code]', 'voucher');
                    if (Ets_AM::needExchange()) {
                        $amount = Tools::convertPrice($amount, $this->context->currency->id, true);
                    }
                    $converted = Ets_affiliatemarketing::displayPrice($amount);
                    $message = str_replace('[r_reward]', $converted , $message);
                    $message = str_replace('[r_code]', $code, $message);
                    $this->context->cookie->__set('eam_voucher_success_message', $message);
                    $this->context->cookie->__set('eam_voucher_id', $cart_rule->id);
                    
                    Tools::redirect(Ets_AM::getBaseUrlDefault('voucher'));
                }
            }
        }
        $info = $this->module->l('You have [strong][r_total_balance][endstrong] in your balance. It can be converted into voucher code. Fill in required fields below to convert your reward balance into voucher code. Voucher code can be used to checkout your shopping cart.', 'voucher');
        $info = str_replace('[r_total_balance]', Ets_AM::displayRewardInMsg($total_balance, $this->context), $info);
        $assign['total_balance'] = Tools::ps_round($total_balance,_PS_PRICE_COMPUTE_PRECISION_? :2);
        $assign['eam_voucher_info'] = $info;
        $vouchers = $this->getTemplateVoucher();
        $assign['cart_rules'] = $vouchers;
        $confirm = $this->module->l('Are you sure you want to convert reward to voucher?', 'voucher');
        $assign['eam_confirm_convert_voucher'] = $confirm;
        if ($this->context->cookie->__get('eam_voucher_success_message') && $this->context->cookie->__get('eam_voucher_id')) {
            $msg = $this->context->cookie->__get('eam_voucher_success_message');
            $voucher_id = $this->context->cookie->__get('eam_voucher_id');
            $this->context->cookie->__unset('eam_voucher_success_message');
            $this->context->cookie->__unset('eam_voucher_id');
            $assign['eam_voucher_success_message'] = $msg;
            $assign['eam_voucher_id'] = $voucher_id;
        }
        $assign['controller'] = 'voucher';
        $assign['currency'] = (array)$this->context->currency;
        $this->context->smarty->assign($assign);
        if ($this->module->is17) {
            $this->setTemplate('module:ets_affiliatemarketing/views/templates/front/customer_reward.tpl');
        } else {
            $this->setTemplate('customer_reward16.tpl');
        }
    }

    protected function getTemplateVoucher()
    {
        $cart_rules = array();
        $vouchers = Ets_Reward_Usage::getVouchers(Context::getContext()->customer->id, array(
            'page' => Tools::getValue('page'),
            'limit' => Tools::getValue('limit'),
        ));
        $results = $vouchers['results'];
        unset($vouchers['results']);
        foreach ($results as $key => $voucher) {
            $cart_rules[$key] = $voucher;
            $cart_rules[$key]['voucher_date'] = $voucher['date_to'];
            $cart_rules[$key]['voucher_minimal'] = ($voucher['minimum_amount'] > 0) ? Ets_affiliatemarketing::displayPrice($voucher['minimum_amount'], (int)$voucher['minimum_amount_currency']) : $this->module->l('None', 'voucher');
            $cart_rules[$key]['voucher_cumulable'] = $this->getCombinableVoucherTranslation($voucher);
            $cartRuleValue = $this->accumulateCartRuleValue($voucher);
            $now = new DateTime();
            $from = new DateTime($voucher['date_from']);
            $to = new DateTime($voucher['date_to']);
            if (0 === count($cartRuleValue)) {
                $cart_rules[$key]['value'] = '-';
            } else {
                $cart_rules[$key]['value'] = implode(' + ', $cartRuleValue);
            }
            if ($voucher['quantity'] <= 0 || $voucher['quantity_per_user'] <= 0) {
                $cart_rules[$key]['status'] = 1; 
            } elseif ($now < $from && $now > $to) {
                $cart_rules[$key]['status'] = -1;
            } else {
                $cart_rules[$key]['status'] = 0;
            }
        }
        $vouchers['results'] = $cart_rules;
        return $vouchers;
    }

    /**
     * @param $voucher
     * @return mixed
     */
    protected function getCombinableVoucherTranslation($voucher)
    {
        if ($voucher['cart_rule_restriction']) {
            $combinableVoucherTranslation = $this->module->l('No', 'voucher');
        } else {
            $combinableVoucherTranslation = $this->module->l('Yes', 'voucher');
        }

        return $combinableVoucherTranslation;
    }
    protected function accumulateCartRuleValue($voucher)
    {
        $cartRuleValue = array();

        if ($voucher['reduction_percent'] > 0) {
            $cartRuleValue[] = $this->formatReductionInPercentage($voucher['reduction_percent']);
        }

        if ($voucher['reduction_amount'] > 0) {
            $cartRuleValue[] = $this->formatReductionAmount($voucher['reduction_amount']);
        }

        if ($voucher['free_shipping']) {
            $cartRuleValue[] = $this->module->l('Free shipping', 'voucher');
        }

        if ($voucher['gift_product'] > 0) {
            $cartRuleValue[] = Product::getProductName(
                $voucher['gift_product'],
                $voucher['gift_product_attribute']
            );
        }

        return $cartRuleValue;
    }

    protected function formatReductionAmount($amount)
    {
        if (Ets_AM::needExchange($this->context)) {
            $amount = Tools::convertPrice($amount, $this->context->currency->id, true);
        }
        return Ets_affiliatemarketing::displayPrice($amount);
    }

    /**
     * @param $percentage
     * @return string
     */
    protected function formatReductionInPercentage($percentage)
    {
        return sprintf('%s%%', $percentage);
    }

    protected function getVoucherConfiguration()
    {
        $config = array();
        $min = Configuration::get('ETS_AM_MIN_BALANCE_REQUIRED_FOR_VOUCHER');
        $max = Configuration::get('ETS_AM_MAX_BALANCE_REQUIRED_FOR_VOUCHER');
        $min = ($min === '' || $min === false) ? -INF : (float)$min;
        $max = ($max === '' || $max === false) ? INF : (float)$max;
        $config['min'] = Tools::convertPrice($min);
        $config['max'] = Tools::convertPrice($max);
        return $config;
    }
}
