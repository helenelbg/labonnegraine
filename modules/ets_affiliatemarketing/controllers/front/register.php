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

class Ets_affiliatemarketingRegisterModuleFrontController extends Ets_affiliatemarketingAllModuleFrontController
{
    const LOY = 'loy';
    const REF = 'ref';
    const AFF = 'aff';
    public $auth = true;
    public $guestAllowed = false;

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
     */
    public function initContent()
    {
        parent::initContent();
        if(!$this->context->customer->isLogged())
            Tools::redirect($this->context->link->getPageLink('my-account'));
        $page= 'module-'.$this->module->name.'-register';
        $meta = Meta::getMetaByPage($page,$this->context->language->id);
        $this->setMetas(array(
            'title' => isset($meta['title']) && $meta['title'] ? $meta['title'] : $this->module->l('Register program', 'register'),
            'keywords' => isset($meta['keywords']) && $meta['keywords'] ? $meta['keywords'] : $this->module->l('Register program', 'register'),
            'description' => isset($meta['description']) && $meta['description'] ? $meta['description'] : $this->module->l('Register program', 'register'),
        ));
        //Check ref exists and set cookie
        if ($ref = (int)Tools::getValue('refs')) {
            Ets_Sponsor::setCookieRef($ref);
        }

        //Register program
        $programs = array('loy', 'ref', 'aff');
        $errors = array();
        $_program = Tools::getValue('p', 'loy');
        if(!in_array($_program,$programs))
            $_program = 'loy';
        $assign = array();
        $required = (int)Configuration::get('ETS_AM_LOYALTY_REGISTER');

        if(($program = Tools::getValue('p', false)) && in_array($program, $programs)){
            
            switch ($program) {
                case 'loy':
                    $assign['title'] = $this->module->l('Loyalty program', 'register');
                    $assign['program'] = 'loy';
                    $assign['intro_program'] = Configuration::get('ETS_AM_LOY_INTRO_PROGRAM', (int)$this->context->language->id);
                    $required = (int)Configuration::get('ETS_AM_LOYALTY_REGISTER');
                    break;
                case 'ref':
                    $assign['title'] = $this->module->l('Referral program','register');
                    $assign['program'] = 'ref';
                    $assign['intro_program'] = Configuration::get('ETS_AM_REF_INTRO_PROGRAM', (int)$this->context->language->id);
                    $required = (int)Configuration::get('ETS_AM_REF_REGISTER_REQUIRED');
                    break;
                case 'aff':
                    $assign['title'] = $this->module->l('Affiliate program', 'register');
                    $assign['program'] = 'aff';
                    $assign['intro_program'] = Configuration::get('ETS_AM_AFF_INTRO_PROGRAM', (int)$this->context->language->id);
                    $required = (int)Configuration::get('ETS_AM_AFF_REGISTER_REQUIRED');
                    break;          
            }
            
        }
        else{
            $assign['title'] = $this->module->l('Loyalty program','register');
            $assign['program'] = 'loy';
            $assign['intro_program'] = Configuration::get('ETS_AM_LOY_INTRO_PROGRAM');
        }
        $id_customer = (int)$this->context->customer->id;

        if(!($alert_type = Ets_User::getTypeAlert($id_customer,$_program))){
            if($_program == 'ref'){
                if(!Ets_Sponsor::isRefferalProgramReady()){
                    $alert_type = 'disabled';
                }
                elseif(!Ets_Sponsor::canUseRefferalProgram($id_customer)){
                    $alert_type = 'need_condition';
                }
                
            }
            elseif($_program == 'loy'){
                if(!Configuration::get('ETS_AM_LOYALTY_ENABLED')){
                     $alert_type = 'disabled';
                }
                elseif(!Ets_Loyalty::isCustomerCanJoinLoyaltyProgram()){
                    $alert_type = 'need_condition';
                }
            }
            elseif($_program == 'aff'){
                if(!(int)Configuration::get('ETS_AM_AFF_ENABLED')){
                     $alert_type = 'disabled';
                }
                if(!Ets_Affiliate::isCustomerCanJoinAffiliateProgram()){
                    $alert_type = 'need_condition';
                }
            }
        }
        if(!$required && !$alert_type){
            $alert_type = 'not_required';
        }
        if(!$alert_type){
            if(Tools::isSubmit('submitEamRegisterProgram', false)){

                $intro = Tools::getValue('intro_yourself', false);
                $program = Tools::getValue('program', false);
                $_program = $program;
                $term_required = (int)Configuration::get('ETS_AM_TERM_AND_COND_REQUIRED'); 
                $accept_term = false;
                if($term_required){
                    if(($agree_term = Tools::getValue('agree_term', false)) && Validate::isCleanHtml($agree_term) ){
                        $accept_term = true;
                    }
                }
                else{
                    $accept_term = true;
                }
                if((int)Configuration::get('ETS_AM_'.Tools::strtoupper($program).'_INTRO_REG')){
                    if(!$intro){
                        $errors[] = $this->module->l('Introduction about you is required.', 'register');
                    }
                    elseif($intro && !Validate::isCleanHtml($intro))
                        $errors[] = $this->module->l('Introduction about you is not valid.', 'register');
                }
                if($accept_term){
                    if($program && in_array($program, $programs)){
                        $p = new Ets_Participation();
                        $p->id_customer = (int)$this->context->customer->id;
                        $p->id_shop = (int)$this->context->shop->id;
                        $p->program = (string)$program;
                        if(!$p->isExists()){

                            $p->intro = $intro;
                            $p->status = 0;
                            $p->datetime_added = date('Y-m-d H:i:s');
                            if(empty($errors)){
                                $p->add();
                                $alert_type = 'register_success';

                                //Check enable send mail
                                if (Ets_AM::enableSendEmailRegister()) {
                                    $emails = Configuration::get('ETS_AM_EMAILS_CONFIRM');
                                    if ($emails) {
                                        //Copy template mail if not exists
                                        $this->createTemplateDir();
                                        $programs_register = '';
                                        if ($program == 'loy') {
                                            $programs_register .= ' '.$this->module->l('Loyalty program,','register');
                                        }
                                        if ($program == 'ref') {
                                            $programs_register .= ' '.$this->module->l('Referral program,','register');
                                        }
                                        if ($program == 'aff') {
                                            $programs_register .= ' '.$this->module->l('Affiliate program,','register');
                                        }
                                        if ($program == 'anr') {
                                            $programs_register .= ' '.$this->module->l('Referral and Affiliate program,','register');
                                        }
                                        $programs_register = trim($programs_register, ',');

                                        $emails = explode(',', $emails);

                                        foreach ($emails as $email) {
                                            //send Mail
                                            $email = trim($email);
                                            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                                $data = array(
                                                    '{email}' => $this->context->customer->email, // sender email address
                                                    '{customer}' => $this->context->customer->firstname.' '.$this->context->customer->lastname,
                                                    '{program_register}' => $programs_register, // email content
                                                    '{intro}' => $intro ? 'Introduction: '. $intro : ''
                                                );
                                                $subjects = array(
                                                    'translation' => $this->module->l('New registration for affiliate program is submitted','register'),
                                                    'origin'=> 'New registration for affiliate program is submitted',
                                                    'specific'=>'register'
                                                );
                                                Ets_aff_email::send(0,'register_reward_program',$subjects,$data,array('employee'=>$email));
                                            }
                                        }
                                    }
                                }
                                Tools::redirect($this->context->link->getModuleLink($this->module->name,'register',array('p'=>$program)));
                            }
                            else{
                                $alert_type = 'error';
                            }
                        }
                    }
                }
                else{
                    $errors[] = $this->module->l('You need to agree to terms and conditions of use', 'register');
                }
            }
        }
        if($alert_type == 'registered'){
            if($_program == 'ref'){
                Tools::redirect( Ets_AM::getBaseUrlDefault('myfriend'));
            }
            elseif($_program == 'loy'){
                Tools::redirect(Ets_AM::getBaseUrlDefault('loyalty'));
            
            }elseif($_program == 'aff'){
                Tools::redirect(Ets_AM::getBaseUrlDefault('my_sale'));
            }
        }
        $message = '';
        if($alert_type == 'need_condition'){
            if($_program == 'ref'){
                $res_data = Ets_Sponsor::canUseRefferalProgramReturn(Context::getContext()->customer->id);
                if(!$res_data['success']){
                    $message = Configuration::get('ETS_AM_REF_MSG_CONDITION', $this->context->language->id) ? strip_tags(Configuration::get('ETS_AM_REF_MSG_CONDITION', $this->context->language->id)) : '';
                    if(isset($res_data['min_order']) && isset($res_data['total_order'])){
                        $message  = str_replace('[min_order_total]', Ets_affiliatemarketing::displayPrice(Tools::convertPrice($res_data['min_order'], $this->context->currency->id, true), $this->context->currency->id), $message);
                        $message  = str_replace('[total_past_order]', Ets_affiliatemarketing::displayPrice(Tools::convertPrice($res_data['total_order'], $this->context->currency->id, true), $this->context->currency->id), $message);
                        $message  = str_replace('[amount_left]', Ets_affiliatemarketing::displayPrice(Tools::convertPrice((float)$res_data['min_order'] - (float)$res_data['total_order'], $this->context->currency->id, true), $this->context->currency->id), $message);
                    }
                    elseif(isset($res_data['not_in_group'])){
                        $message = '';
                    }
                    if(!$message){
                        Tools::redirect($this->context->link->getPageLink('my-account', true));
                    }
                }
            }
            elseif($_program == 'loy'){
                if(!Ets_AM::isCustomerBelongToValidGroup($this->context->customer, 'ETS_AM_LOYALTY_GROUPS')){
                    $message = '';
                    if(!$message){
                        Tools::redirect($this->context->link->getPageLink('my-account', true));
                    }
                }
                else{
                    $total_order = Ets_AM::getTotalOrder($this->context->customer->id, $this->context);
                    $min_order = Configuration::get('ETS_AM_LOYALTY_MIN_SPENT');
                    if($min_order !== false && $min_order != '' && (float)$min_order > $total_order){
                        $this->valid = false;
                        
                        $message = Configuration::get('ETS_AM_LOY_MSG_CONDITION', $this->context->language->id) ? strip_tags(Configuration::get('ETS_AM_LOY_MSG_CONDITION', $this->context->language->id)) : '';
                        $message  = str_replace('[min_order_total]', Ets_affiliatemarketing::displayPrice(Tools::convertPrice($min_order, $this->context->currency->id, true), $this->context->currency->id), $message);
                        $message  = str_replace('[total_past_order]', Ets_affiliatemarketing::displayPrice(Tools::convertPrice($total_order, $this->context->currency->id, true), $this->context->currency->id), $message);
                        $message  = str_replace('[amount_left]', Ets_affiliatemarketing::displayPrice(Tools::convertPrice((float)$min_order - (float)$total_order, $this->context->currency->id, true), $this->context->currency->id), $message);
                        if(!$message){
                            Tools::redirect($this->context->link->getPageLink('my-account', true));
                        }
                    }
                }
                
            
            }elseif($_program == 'aff'){
                if(isset($res_data['min_order']) && isset($res_data['total_order'])){
                    $message = Configuration::get('ETS_AM_AFF_MSG_CONDITION', $this->context->language->id) ? strip_tags(Configuration::get('ETS_AM_AFF_MSG_CONDITION', $this->context->language->id)) : '';
                    $message  = str_replace('[min_order_total]', Ets_affiliatemarketing::displayPrice(Tools::convertPrice($res_data['min_order'], $this->context->currency->id, true), $this->context->currency->id), $message);
                    $message  = str_replace('[total_past_order]', Ets_affiliatemarketing::displayPrice(Tools::convertPrice($res_data['total_order'], $this->context->currency->id, true), $this->context->currency->id), $message);
                    $message  = str_replace('[amount_left]', Ets_affiliatemarketing::displayPrice(Tools::convertPrice((float)$res_data['min_order'] - (float)$res_data['total_order'], $this->context->currency->id, true), $this->context->currency->id), $message);
                }
                elseif(isset($res_data['not_in_group']) && $res_data['not_in_group']){
                    $message = '';
                }
                if(!$message){
                    Tools::redirect($this->context->link->getPageLink('my-account', true));
                }
            }
            
        }
        $assign['errors'] = $errors;
        $assign['alert_type'] = $alert_type;
        $assign['message'] = $message;
        $assign['register_customer'] = $this->context->customer;
        $assign['link_term'] = Configuration::get('ETS_AM_TERM_AND_COND_URL', $this->context->language->id);
        $assign['query'] = Tools::getAllValues();
        $assign['term_required'] = (int)Configuration::get('ETS_AM_TERM_AND_COND_REQUIRED');
        $assign['intro_required'] = (int)Configuration::get('ETS_AM_'.Tools::strtoupper($assign['program']).'_INTRO_REG');
        $this->context->smarty->assign($assign);
        if ($this->module->is17) {
            $this->setTemplate('module:ets_affiliatemarketing/views/templates/front/register.tpl');
        } else {
            $this->setTemplate('register16.tpl');
        }

    }

    public function postProcess(){
        parent::postProcess();

        
    }


    public function createTemplateDir()
    {
        $iso_code = $this->context->language->iso_code;
        $temp_path = _PS_MODULE_DIR_ . 'ets_affiliatemarketing/mails/' . $iso_code;
        $default_path = _PS_MODULE_DIR_ . 'ets_affiliatemarketing/mails/en';
        if ($iso_code) {
            if (!is_dir($temp_path)) {
                @mkdir($temp_path);
                if ($dir_files = opendir($default_path)) {
                    while (false !== ($file = readdir($dir_files))) {
                        if (($file != '.') && ($file != '..')) {
                            if (is_dir($default_path . '/' . $file)) {
                                
                            } else {
                                copy($default_path . '/' . $file, $temp_path . '/' . $file);
                            }
                        }
                    }
                    closedir($dir_files);
                }
            }
        }
    }
}