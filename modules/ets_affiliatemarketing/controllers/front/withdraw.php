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

/**
 * Class Ets_affiliatemarketingWithdrawModuleFrontController
 * @property Ets_affiliatemarketing $module;
 */
class Ets_affiliatemarketingWithdrawModuleFrontController extends Ets_affiliatemarketingAllModuleFrontController
{
    public $auth = true;
    public $guestAllowed = false;
    public $authRedirection = URL_EAM_WITHDRAW;
    /**
     * @var array
     */
    protected $_errors = array();
    /**
     * @var array
     */
    public $_messages = array();

    public function init()
    {
        parent::init();
        if (!$this->module->is17) {
            $this->display_column_left = false;
            $this->display_column_right = false;
        }
    }

    /**
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     * @throws Exception
     */
    public function initContent()
    {
        parent::initContent();
        if(!$this->context->customer->isLogged())
            Tools::redirect($this->context->link->getPageLink('my-account'));
        if (!$this->context) {
            $context = Context::getContext();
        } else {
            $context = $this->context;
        }
        $page= 'module-'.$this->module->name.'-withdraw';
        $meta = Meta::getMetaByPage($page,$this->context->language->id);
        $this->setMetas(array(
            'title' =>isset($meta['title']) && $meta['title'] ? $meta['title'] : $this->module->l('Withdrawals', 'withdraw'),
            'keywords' => isset($meta['keywords']) && $meta['keywords'] ? $meta['keywords'] : $this->module->l('Withdrawals', 'withdraw'),
            'description' =>isset($meta['description']) && $meta['description'] ? $meta['description'] : $this->module->l('Withdrawals', 'withdraw'),
        ));

        $baseUrl = Ets_AM::getBaseUrl();
        $this->context->smarty->assign(array(
            'eam_url' => $baseUrl,
            'link_reward' => Ets_AM::getBaseUrlDefault('dashboard'),
            'link_reward_history' => Ets_AM::getBaseUrlDefault('history'),
            'link_withdraw' =>  Ets_AM::getBaseUrlDefault('withdraw'),
            'link_voucher' => Ets_AM::getBaseUrlDefault('voucher'),
        ));
        $config = $this->getDefaultConfiguration();
        $allow_customer_withdraw = Configuration::get('ETS_AM_AFF_ALLOW_WITHDRAW');
        if (! $allow_customer_withdraw) {
            Tools::redirect('404');
        }
        $this->context->smarty->assign(array(
            'controller' => 'withdraw'
        ));
        $total_can_withdraw = Ets_Reward_Usage::getAmountCanWithdrawRewards($context->customer->id, $context);
        $d_total_balance = Ets_AM::displayRewardInMsg($total_can_withdraw, $this->context);
        if (($id_payment = (int)Tools::getValue('id_payment')) && ($payment_method = Ets_Withdraw::getPaymentMethods($id_payment))) {
            if (Validate::isUnsignedInt($id_payment)) {
                $latest_payment = Ets_Withdraw::getLatestCustomerPaymentInfo($id_payment);
                $withdraw_condition = array();
                $withdraw_condition['min'] = $config['min_amount'] ?  $config['min_amount'] : null;
                $withdraw_condition['max'] = $config['max_amount'] ? $config['max_amount'] : null;
                if($withdraw_condition['min'] || $withdraw_condition['max']){
                    if($withdraw_condition['max'] > $total_can_withdraw)
                        $withdraw_condition['max'] = $total_can_withdraw;
                    if($withdraw_condition['min'])
                        $withdraw_condition['min'] = Ets_AM::displayPriceOnly($withdraw_condition['min']);
                    if($withdraw_condition['max'])
                        $withdraw_condition['max'] = Ets_AM::displayPriceOnly($withdraw_condition['max']);
                }

                $require_invoice = Configuration::get('ETS_AM_AFF_WITHDRAW_INVOICE_REQUIRED');
                $withdraw_condition['require_invoice'] = $require_invoice;
                if (!$allow_customer_withdraw) {
                    $this->context->smarty->assign(array(
                        'eam_allow_withdraw' => false,
                        'is_request_withdraw_page' => true,
                        'eam_reward_enough' => false,
                        'eam_reward_has_pending' => false,
                        'message' => null,
                        'eam_withdraw_condition' => $withdraw_condition
                    ));
                } else {
                    $assign = array(
                        'eam_url' => $baseUrl,
                        'eam_allow_withdraw' => true,
                        'is_request_withdraw_page' => true,
                        'eam_payment_history' => $latest_payment,
                        'eam_confirm' => $this->module->l('Please confirm that you want to withdraw :%d ', 'withdraw') . $this->context->currency->iso_code,
                        'eam_withdraw_condition' => $withdraw_condition
                    );
                    if ($payment_method && count($payment_method)) {
                        $fee_type = $payment_method['fee_type'];
                        if($fee_type!='NO_FEE')
                        {
                            if ($fee_type == 'FIXED') {
                                $value = (float)$payment_method['fee_fixed'];
                                if ($value == 0) {
                                    $fee = $this->module->l('No fee','withdraw');
                                } else {
                                    $fee = Ets_AM::displayPriceOnly($value);
                                }
                            } else {
                                $value = (float)$payment_method['fee_percent'];
                                if ($value == 0) {
                                    $fee = $this->module->l('No fee','withdraw');
                                } else {
                                    $fee = (float)$value . '%';
                                }
                            }
                        }
                        else
                            $fee = $this->module->l('No fee','withdraw');
                        $payment_method['fee'] = $fee;
                        $payment_method['note'] = trim($payment_method['note']);
                        $payment_fields = Ets_Withdraw::getPaymentMethodFields($id_payment);
                        if ($require_invoice) {
                            $payment_fields[] = array(
                                'field_id' => null,
                                'field_type' => 'file',
                                'field_alias' => 'invoice',
                                'required' => 1,
                                'field_title' => $this->module->l('Invoice', 'withdraw'),
                                'description' => ''
                            );
                        }
                        $assign['eam_payment_method'] = $payment_method;
                        $assign['eam_payment_fields'] = $payment_fields;
                        $assign['eam_can_withdraw'] = $d_total_balance;
                        $last_withdrawal = Ets_Withdraw::getFieldsOfLastWithdrawal($this->context->customer->id,$id_payment);
                        $assign['field_values'] = $last_withdrawal;
                        if (Tools::isSubmit('check_withdraw_amount')) {
                            if(!$this->isTokenValid()){
                                die(json_encode(array(
                                    'success' => false,
                                    'message' => $this->module->l('Token is invalid', 'withdraw')
                                )));
                            }
                            $amount = Tools::getValue('amount');
                            if(Validate::isFloat($amount))
                            {
                                $amount = Tools::convertPrice($amount,null,false) ;
                                $this->checkAmount($amount, $config, $payment_method);
                            }

                        }
                        if ($total_can_withdraw == 0 || $total_can_withdraw < (float) $config['min_amount']) {
                            if($config['min_amount']<=0)
                                $message = $this->module->l('Withdrawal is not available. You are required to have positive balance in order to submit your withdrawal.', 'withdraw');
                            else
                                $message = $this->module->l('Withdrawal is not available. You are required to have at least [r_d] in your "Available balance for withdrawal" in order to be able to submit your withdrawal request', 'withdraw');
                            $message = str_replace('[r_d]', Ets_AM::displayPriceOnly($config['min_amount']), $message);
                            $assign['eam_reward_enough'] = false;
                            $assign['eam_reward_has_pending'] = false;
                            $assign['message'] = $message;
                            $this->context->smarty->assign($assign);
                        } elseif (Configuration::get('ETS_AM_AFF_WITHDRAW_ONE_ONLY')) {
                            if (Ets_Reward_Usage::isCustomerHasPendingWithdrawal((int)$context->customer->id)) {
                                $message = $this->module->l('Your last withdrawal request is pending to be processed. Please wait for the last request to be processed before submitting new one', 'withdraw');
                                $assign['eam_reward_enough'] = true;
                                $assign['eam_reward_has_pending'] = true;
                                $assign['message'] = $message;
                                $this->context->smarty->assign($assign);
                            } else {
                                $this->withdrawReward($payment_fields, $payment_method, $config);
                                $assign['eam_reward_enough'] = true;
                                $assign['eam_reward_has_pending'] = false;
                                $this->context->smarty->assign($assign);
                            }
                        } else {
                            $this->withdrawReward($payment_fields, $payment_method, $config);
                            $assign['eam_reward_enough'] = true;
                            $assign['eam_reward_has_pending'] = false;
                            $this->context->smarty->assign($assign);
                        }
                    }
                }
                if ($this->module->is17) {
                    $this->setTemplate('module:ets_affiliatemarketing/views/templates/front/customer_reward.tpl');
                } else {
                    $this->setTemplate('customer_reward16.tpl');
                }
            } else {
                Tools::redirect(404);
            }
        }
        else {
            if ($this->context->cookie->__get('eam_success_message')) {
                $eam_succes_message = $this->context->cookie->__get('eam_success_message');
                $this->context->cookie->__unset('eam_success_message');
                $this->context->smarty->assign(array(
                    'eam_success_message' => $eam_succes_message
                ));
            }
            $requests = Ets_Withdraw::getCustomerWithdrawalRequests($context->customer->id,null,(int)Tools::getValue('page'),(int)Tools::getValue('limit'));
            if ($allow_customer_withdraw) {
                $payment_methods = Ets_Withdraw::getListPayments();
                $methods = array();
                foreach ($payment_methods as $method) {
                    $method['link'] =  Ets_AM::getBaseUrlDefault('withdraw', array('id_payment' => $method['id_ets_am_payment_method']));
                    if($method['fee_type']=='NO_FEE')
                    {
                        $fee = $this->module->l('No fee','withdraw');
                    }
                    else
                    {
                        if ($method['fee_type'] == 'FIXED') {
                            $value = (float)$method['fee_fixed'];
                            if ($value == 0) {
                                $fee = $this->module->l('No fee','withdraw');
                            } else {
                                $fee = Ets_AM::displayPriceOnly($value);
                            }
                        } else {
                            $value = (float)$method['fee_percent'];
                            if ($value == 0) {
                                $fee = $this->module->l('No fee','withdraw');
                            } else {
                                $fee = (float)$value . '%';
                            }
                        }
                    }
                    $method['fee'] = $fee;
                    $methods[] = $method;
                }
                $this->context->smarty->assign(array(
                    'eam_allow_withdraw' => true,
                    'eam_payment_methods' => $methods,
                    'eam_can_withdraw' => $d_total_balance,
                    'eam_withdrawal_requests' => $requests,
                    'controller' => 'withdraw',
                    'is_request_withdraw_page' => false,
                    'eam_confirm' => ''
                ));
            } else {
                $this->context->smarty->assign(array(
                    'eam_allow_withdraw' => false
                ));
            }

        }
        if ($this->module->is17) {
            $this->setTemplate('module:ets_affiliatemarketing/views/templates/front/customer_reward.tpl');
        } else {
            $this->setTemplate('customer_reward16.tpl');
        }
    }


    /**
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function postProcess()
    {
        parent::postProcess();
    }
    /**
     * @return array
     */
    protected function getDefaultConfiguration()
    {
        $min = Configuration::get('ETS_AM_MIN_BALANCE_REQUIRED_FOR_WITHDRAW');
        if ($min)
            $min = (float)$min;
        else
            $min = 0;
        $max = Configuration::get('ETS_AM_MAX_WITHDRAW');
        if (!$max)
            $max = 0;
        else
            $max = (float)$max;
        return array(
            'min_amount' => $min,
            'max_amount' => $max,
        );
    }


    /**
     * @param $key
     * @return string
     */
    public function flash($key)
    {
        $flash = '';
        if ($this->context->cookie->__get($key)) {
            $flash = $this->context->cookie->__get($key);
            $this->context->cookie->__set($key, null);
        }
        return $flash;
    }

    /**
     * @param $payment_methods
     * @param $amount
     * @return float|int
     */
    protected function calculateRewardWithdraw($payment_methods, $amount)
    {
        $type = $payment_methods['fee_type'];
        if($type=='NO_FEE')
        {
            $fee=0;
        }
        else
        {
            if ($type == 'FIXED') {
                $fee = (float)$payment_methods['fee_fixed'];
            } else {
                $fee = (float)$payment_methods['fee_percent'];
                $fee = ($fee * $amount) / 100;
            }
        }
        $amount -= $fee;
        return $amount;
    }

    /**
     * @param $amount
     * @param $config
     * @param $payment_method
     * @param bool $want_json
     * @return bool|array
     */
    protected function checkAmount($amount, $config, $payment_method, $want_json = true)
    {
        $can_withdraw = Ets_Reward_Usage::getAmountCanWithdrawRewards($this->context->customer->id, $this->context);
        if (Validate::isFloat($amount)) {
            $amount_inc_fee = $amount;
            $fee = $this->getMethodFee($payment_method, $amount);
            $amount_inc_fee -= $fee;
            if ($amount_inc_fee < (float)$config['min_amount'] || $amount_inc_fee<=0) {
                $fee_of_min = 0;
                if($payment_method['fee_type'] == 'PERCENT'){
                    $fee_of_min = (float)$config['min_amount'] *(float)$payment_method['fee_percent'] / 100;
                }
                elseif($payment_method['fee_type'] == 'FIXED'){
                    $fee_of_min = (float)$payment_method['fee_fixed'];
                }
                $msg = $this->module->l('Amount to withdraw must be greater than ', 'withdraw') . Ets_AM::displayPriceOnly($config['min_amount'] + $fee_of_min);
                if (!$want_json) {
                    return array(
                        'valid' => false,
                        'msg' => $msg
                    );
                }
                die(json_encode(array(
                    'valid' => false,
                    'msg' => $msg
                )));
            }
            if (($amount_inc_fee > (float)$config['max_amount']) && (float)$config['max_amount'] > 0) {
                $fee_of_max = 0;
                if($payment_method['fee_type'] == 'PERCENT'){
                    $fee_of_max = (float)$config['max_amount'] *(float)$payment_method['fee_percent'] / 100;
                }
                elseif($payment_method['fee_type'] == 'FIXED'){
                    $fee_of_max = (float)$payment_method['fee_fixed'];
                }
                $msg = $this->module->l('Maximum amount to withdraw ', 'withdraw') . Ets_AM::displayPriceOnly($config['max_amount'] + $fee_of_max);
                if (!$want_json) {
                    return array(
                        'valid' => false,
                        'msg' => $msg
                    );
                }
                die(json_encode(array(
                    'valid' => false,
                    'msg' => $msg
                )));
            }
            if (Tools::ps_round($amount,2) > Tools::ps_round($can_withdraw,2)) {
                $msg = $this->module->l('Your reward is not enough for withdraw.', 'withdraw');
                if ($want_json) {
                    die(json_encode(array(
                        'valid' => false,
                        'msg' => $msg
                    )));
                }
                return array(
                    'valid' => false,
                    'msg' => $msg
                );
            }

            if ($want_json) {
                $rec_amount = $this->calculateRewardWithdraw($payment_method, $amount);
                if (Ets_AM::needExchange($this->context)) {
                    $rec_amount = Ets_AM::displayPriceOnly($rec_amount);
                }
                die(json_encode(array(
                    'valid' => true,
                    'amount' => Tools::ps_round($rec_amount,2),
                    'can_withdraw' => $can_withdraw,
                    '$amount_inc_fee'=> Tools::convertPrice($amount_inc_fee),
                    '$fee' => $fee,
                    '$amount' => $amount,
                )));
            } else {
                return array(
                    'valid' => true,
                );
            }

        }
        $msg = $this->module->l('Amount must be a number.', 'withdraw');
        if (!$want_json) {
            return $msg;
        }
        die(json_encode(array(
            'valid' => false,
            'msg' => $msg
        )));

    }

    /**
     * @param $payment_fields
     * @param $payment_method
     * @param $config
     * @param $can_withdraw
     * @throws Exception
     */
    protected function withdrawReward($payment_fields, $payment_method, $config)
    {
        $errors = array();
        if (Tools::isSubmit('eam_withdraw_submit')) {
            //send mail
            $amount = Tools::getValue('EAM_AMOUNT_WITHDRAW');
            if (!$amount) {
                $errors['EAM_AMOUNT_WITHDRAW'] = $this->module->l('The amount to withdraw is required.', 'withdraw');
            } elseif (!(Validate::isUnsignedInt($amount) || Validate::isFloat($amount))) {
                $errors['EAM_AMOUNT_WITHDRAW'] = $this->module->l('The amount must be a number.', 'withdraw');
            }
            else
                $amount = Tools::convertPrice($amount,null,false) ;
            if (count($errors)) {
                $this->displayFormErrors($payment_fields, $errors);
            } else {
                $check = $this->checkAmount($amount, $config, $payment_method, false);
                if (!$check['valid']) {
                    $errors['EAM_AMOUNT_WITHDRAW'] = $check['msg'];
                }
                if (count($errors)) {
                    $this->displayFormErrors($payment_fields, $errors);
                } else {
                    if (count($payment_fields)) {
                        foreach ($payment_fields as $field) {
                            if (isset($field['required']) && $field['required']) {
                                if ($field['field_type'] !== 'file') {
                                    if (!($field_alias = Tools::getValue($field['field_alias'])) ) {
                                        $errors[$field['field_alias']] = $this->module->l('This field is required.', 'withdraw');
                                    }elseif($field_alias && !Validate::isCleanHtml($field_alias))
                                        $errors[$field['field_alias']] = $this->module->l('This field is not valid.', 'withdraw');
                                } else {
                                    if (!isset($_FILES[$field['field_alias']]) || !$_FILES[$field['field_alias']]['tmp_name']) {
                                        $errors[$field['field_alias']] = $this->module->l('The invoice is required.', 'withdraw');
                                    } else {
                                        $name = str_replace(' ', '_', $_FILES[$field['field_alias']]["name"]);
                                        $ext = pathinfo($name, PATHINFO_EXTENSION);
                                        $allowExtentions = array('pdf');
                                        if (!in_array($ext, $allowExtentions)) {
                                            $errors[$field['field_alias']] = $this->module->l('Only pdf file type is accepted.', 'withdraw');
                                        }
                                    }
                                }
                            }
                        }
                        if (count($errors)) {
                            $this->displayFormErrors($payment_fields, $errors);
                        } else {
                            if (! $amount) {
                                $this->displayFormErrors($payment_fields, array('EAM_AMOUNT_WITHDRAW' => $this->module->l('Your reward is not enough for withdraw.', 'withdraw')));
                            } else {
                                if (isset($_FILES['invoice']) && $_FILES['invoice']['tmp_name']) {
                                    if(!is_dir(_PS_DOWNLOAD_DIR_ . EAM_INVOICE_PATH))
                                        Ets_AM::createPath(_PS_DOWNLOAD_DIR_ . EAM_INVOICE_PATH);
                                    $file_name = Tools::passwdGen(32) . '.pdf';
                                    $path_img = _PS_DOWNLOAD_DIR_ . EAM_INVOICE_PATH . '/' . $file_name;
                                    $moved = move_uploaded_file($_FILES['invoice']['tmp_name'], $path_img);
                                    if ($moved) {
                                        $withdraw = $this->withdrawSave($payment_method, $amount, $payment_fields,$file_name);
                                    }
                                } else {
                                    $withdraw = $this->withdrawSave($payment_method, $amount, $payment_fields);
                                }
                                if(isset($withdraw) && $withdraw)
                                {
                                    $processed_date=date('Y-m-d H:i:s');
                                    $data = array(
                                        '{customer}' => $this->context->customer->firstname.' '.$this->context->customer->lastname,
                                        '{withdrawal_ID}' => $withdraw->id,
                                        '{amount}' => Ets_affiliatemarketing::displayPrice($amount),
                                        '{payment_method}' => $payment_method['title'],
                                        '{processed_date}' => $processed_date,
                                        '{invoice_withdrawal}' => isset($path_img) && $path_img ? EtsAffDefine::displayText($this->module->l('Invoice','withdraw'),'a','','',$this->context->link->getModuleLink($this->module->name,'download',array('downloadInvoiceWithdraw'=>1,'id_withdraw'=>$withdraw->id)),'_blank') :'',
                                    );
                                    $subjects = array(
                                        'translation' => $this->module->l('You have submitted a withdrawal request','withdraw'),
                                        'origin'=> 'You have submitted a withdrawal request',
                                        'specific'=>'withdraw'
                                    );
                                    Ets_aff_email::send($this->context->language->id,'customer_withdraw',$subjects,$data,$this->context->customer->email);
                                    $adminEmail = Configuration::get('ETS_AM_EMAILS_CONFIRM');
                                    if ($adminEmail) {
                                        $data['{invoice_withdrawal}']='';
                                        $adminEmail = explode(',', $adminEmail);
                                        foreach ($adminEmail as $to) {
                                            if(Validate::isEmail($to))
                                            {
                                                $subjects = array(
                                                    'translation' => $this->module->l('New withdrawal request waiting for approval','withdraw'),
                                                    'origin'=> 'New withdrawal request waiting for approval',
                                                    'specific'=>'withdraw'
                                                );
                                                Ets_aff_email::send(0,'admin_withdraw',$subjects,$data,array('employee'=> $to));
                                            }
                                        }
                                    }
                                    $this->context->cookie->__set('eam_success_message', $this->module->l('You have successfully submit your withdrawal request.', 'withdraw'));
                                    $url_redirect = Ets_AM::getBaseUrlDefault('withdraw');
                                    Tools::redirect($url_redirect);
                                }
                                else
                                {
                                    $errors['EAM_AMOUNT_WITHDRAW'] = $this->module->l('Saving withdraw request failed','withdraw');
                                    $this->displayFormErrors($payment_fields, $errors);
                                }

                            }
                        }
                    }
                }

            }
        }
    }

    /**
     * @param $payment_fields
     * @param array $errors
     */
    protected function displayFormErrors($payment_fields, $errors = array())
    {
        if (count($errors)) {
            $old_data = array();
            $old_data['EAM_AMOUNT_WITHDRAW'] = Tools::getValue('EAM_AMOUNT_WITHDRAW');
            foreach ($payment_fields as $field) {
                $old_data[$field['field_alias']] = Tools::getValue($field['field_alias']);
            }
            $this->context->smarty->assign(array('eam_form_errors' => $errors, 'eam_form_old_data' => $old_data));
            return;
        }
    }

    /**
     *
     * @param $payment_method
     * @param $amount <DEFAULT SHOP CURRENCY AMOUNT >
     * @param $payment_fields
     * @param string $invoice
     */
    protected function withdrawSave($payment_method, $amount, $payment_fields, $invoice = '')
    {
        $context = $this->context;
        $withdraw = new Ets_Withdraw();
        $withdraw->id_payment_method = $payment_method['id_ets_am_payment_method'];
        $withdraw->fee_type = $payment_method['fee_type'];
        if($payment_method['fee_type']!='NO_FEE')
        {
            if($payment_method['fee_type']=='FIXED')
                $withdraw->fee = $payment_method['fee_fixed'];
            else
                $withdraw->fee = $payment_method['fee_percent'];
        }
        else
            $withdraw->fee=0;
        if ($invoice) {
            $withdraw->invoice = $invoice;
        }
        $withdraw = $withdraw->add() ? $withdraw : false;
        if ($withdraw) {
            $usageLOY = 0;
            $usageANR = 0;
            if((int)Configuration::get('ETS_AM_ALLOW_WITHDRAW_LOYALTY_REWARDS')){
                $totalLoy = Ets_Reward_Usage::getTotalEarn('loy', $context->customer->id, $context);
                $totalSpentLoy = Ets_Reward_Usage::getTotalSpentLoy($context->customer->id, false, null, $context);
                $remainLoy = (float)$totalLoy - (float)$totalSpentLoy;
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
            }
            else{
                $usageANR = (float)$amount;
            }
            $r = false;
            if($usageLOY > 0){
                $rewardUsage = new Ets_Reward_Usage();
                $rewardUsage->amount = $usageLOY;
                $rewardUsage->type = 'loy';
                $rewardUsage->id_customer = $this->context->customer->id;
                $rewardUsage->id_shop = $this->context->shop->id;
                $rewardUsage->id_withdraw = $withdraw->id;
                $rewardUsage->id_currency = Configuration::get('PS_CURRENCY_DEFAULT');
                $rewardUsage->status = 1;
                $rewardUsage->datetime_added = date('Y-m-d H:i:s');
                $rewardUsage->deleted = 0;
                $rewardUsage->note = sprintf($this->module->l('Withdrawn (%s, ID withdrawal: %s)', 'withdraw'), trim($payment_method['title']),  $withdraw->id);
                $r = $rewardUsage->add() ? $rewardUsage : false;
                Ets_Loyalty::loyRewardUsed($usageLOY,$rewardUsage->id);
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
                        $rewardUsage->type = $program;
                        $rewardUsage->amount = $usage;
                        $rewardUsage->id_customer = $this->context->customer->id;
                        $rewardUsage->id_shop = $this->context->shop->id;
                        $rewardUsage->id_withdraw = $withdraw->id;
                        $rewardUsage->id_currency = Configuration::get('PS_CURRENCY_DEFAULT');
                        $rewardUsage->status = 1;
                        $rewardUsage->datetime_added = date('Y-m-d H:i:s');
                        $rewardUsage->deleted = 0;
                        $rewardUsage->note = sprintf($this->module->l('Withdrawn (%s, ID withdrawal: %s)','withdraw'), trim($payment_method['title']),  $withdraw->id);
                        $r = $rewardUsage->add() ? $rewardUsage : false;
                        if(!$continue)
                            break;
                    }
                }
            }
            if($r){
                foreach ($payment_fields as $field) {
                    if ($field['field_alias'] !== 'file') {
                        $wf = new Ets_Withdraw_Field();
                        $value = Tools::getValue($field['field_alias']);
                        $wf->value = Validate::isCleanHtml($value) ? $value :'';
                        $wf->id_withdrawal = $withdraw->id;
                        $wf->id_payment_method_field = $field['field_id'];
                        $wf->save();
                    }
                }
            }
            return $withdraw;
        }
        else
            return false;
    }

    protected function getMethodFee($payment_method, $amount)
    {
        $fee_type = $payment_method['fee_type'];
        if($fee_type=='NO_FEE')
            $fee=0;
        else
        {
            if ($fee_type == 'FIXED') {
                $fee = (float) $payment_method['fee_fixed'];
            } else {
                $fee = ($amount * (float) $payment_method['fee_percent']) / 100;
            }
        }
        return $fee;
    }

}
