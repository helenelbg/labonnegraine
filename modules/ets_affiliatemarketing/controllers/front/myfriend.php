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

class Ets_affiliatemarketingMyfriendModuleFrontController extends Ets_affiliatemarketingAllModuleFrontController
{

    public $auth = true;
    public $guestAllowed = false;
    public $authRedirection = URL_REF_PROGRAM;

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
        $friendly_url = (int)Configuration::get('PS_REWRITING_SETTINGS');
        $link_tab = array(
            'my_friends' =>  Ets_AM::getBaseUrlDefault('myfriend'),
            'ref_friends' => Ets_AM::getBaseUrlDefault('refer_friends'),
        );
        //Set meta
        $page= 'module-'.$this->module->name.'-sponsorship';
        $meta = Meta::getMetaByPage($page,$this->context->language->id);
        $this->setMetas(array(
            'title' => isset($meta['title']) && $meta['title'] ? $meta['title'] : $this->module->l('My friends','myfriend'),
            'keywords' => isset($meta['keywords']) && $meta['keywords'] ? $meta['keywords'] : $this->module->l('My friends','myfriend'),
            'description' => isset($meta['description']) && $meta['description'] ? $meta['description'] : $this->module->l('My friends','myfriend'),
        ));

        $this->context->smarty->assign(array(
            'link_tab' => $link_tab,
        ));
        if(Ets_Sponsor::isRefferalProgramReady()){
            $customer = $this->context->customer;

            /* == Check state program ======*/
            $alert_type = '';
            $userExists = Ets_User::getUserByCustomerId($this->context->customer->id);

            if($userExists){
                if( $userExists['status'] == -1){
                    $alert_type = 'account_banned';
                }
                elseif($userExists['status'] > 0 && $userExists['ref'] == 1 ){
                    $alert_type = 'registered';
                }
                elseif($userExists['status'] > 0 && $userExists['ref'] == -1 ){
                    $alert_type = 'program_suspened';
                }
                elseif($userExists['status'] > 0 && $userExists['ref'] == -2 ){
                    $alert_type = 'program_declined';
                } 
                else {
                    $p = Ets_Participation::getProgramRegistered($this->context->customer->id, 'ref');
                    if($p){
                        if($p['status'] == 0){
                            $alert_type = 'register_success';
                        }
                        elseif($p['status'] == 1){
                            $alert_type = 'registered';
                        }
                        elseif($p['status'] < 0){
                            $alert_type = 'program_declined';
                        }
                    }
                    else{

                        if(Configuration::get('ETS_AM_REF_REGISTER_REQUIRED')){
                            $url_register = Ets_AM::getBaseUrlDefault('register',array('p'=>'ref'));
                            Tools::redirect($url_register);
                        }
                    }
                }
            } else{
                $p = Ets_Participation::getProgramRegistered($this->context->customer->id, 'ref');
                if($p){
                    if($p['status'] == 0){
                        $alert_type = 'register_success';
                    }
                    elseif($p['status'] == 1){
                        $alert_type = 'registered';
                    }
                    elseif($p['status'] < 0){
                        $alert_type = 'program_declined';
                    }
                }
                else{

                    if(Configuration::get('ETS_AM_REF_REGISTER_REQUIRED')){
                        $url_register = Ets_AM::getBaseUrlDefault('register',array('p'=>'ref'));
                        Tools::redirect($url_register);
                    }
                }
            }

            $message = '';
            if(!$alert_type){
                $res_data = Ets_Sponsor::canUseRefferalProgramReturn(Context::getContext()->customer->id);
                if(!$res_data['success']){
                    $alert_type = 'need_condition';
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
            $this->context->smarty->assign(array(
                'alert_type' => $alert_type,
                'message' => $message
            ));
            /* == End Check state program ======*/
                
            if (Ets_Sponsor::isJoinedRef($customer->id)  && (!$alert_type || $alert_type == 'registered')) {
                $tab_active = 'my-friends';
                if(($id_customer = (int)Tools::getValue('id_customer')) && ($customerObj = new Customer($id_customer)) && Validate::isLoadedObject($customerObj))
                {
                    $template = 'sponsorship_customer.tpl';
                    if(!($customer_info = Ets_Sponsor::getCustomerSponsorInfor($id_customer)))
                        Tools::redirect($this->context->link->getPageLink('index'));
                    $this->context->smarty->assign(
                        array(
                            'customer_info' => $customer_info,
                            'ETS_AM_DISPLAY_ID_ORDER' => Configuration::get('ETS_AM_DISPLAY_ID_ORDER'),
                            'link_back' => Ets_AM::getBaseUrlDefault('myfriend')
                        )
                    );
                }
                else
                {
                    $template ='sponsorship_myfriend.tpl';

                    $this->setMetas(array(
                        'title' => $this->module->l('My friends','myfriend'),
                        'keywords' => $this->module->l('My friends','myfriend'),
                        'description' => $this->module->l('My friends','myfriend'),
                    ));
                    $friends = Ets_Sponsor::getDetailSponsors($customer->id, array(
                        'orderby' => Tools::getValue('orderBy'),
                        'orderway' => Tools::getValue('orderWay'),
                        'page' => Tools::getValue('page'),
                        'limit' => Tools::getValue('limit'),
                        'customer_sale_filter' => Tools::getValue('customer_sale_filter'),
                    ), true);
                    $query = Tools::getAllValues();
                    $query['orderBy'] = Tools::strtolower(Tools::getValue('orderBy','id'));
                    $query['orderWay'] = Tools::strtolower(Tools::getValue('orderWay','desc'));
                    $this->context->smarty->assign(array(
                        'friends' => $friends,
                        'query' => $query,
                        'display_email' => (int)Configuration::get('ETS_AM_REF_DISPLAY_EMAIL_SPONSOR'),
                        'display_name' => (int)Configuration::get('ETS_AM_REF_DISPLAY_NAME_SPONSOR'),
                    ));
                }
                $query = Tools::getAllValues();
                $query['orderBy'] = Tools::strtolower(Tools::getValue('orderBy','id'));
                $query['orderWay'] = Tools::strtolower(Tools::getValue('orderWay','desc'));
                $this->context->smarty->assign(array(
                    'template' => $template,
                    'tab_active' => $tab_active,
                    'query' => $query
                ));
            }
            else{
                if(!$alert_type){
                    $alert_type = 'register_success';
                }
                
                $template = 'my_friends.tpl';
                $this->context->smarty->assign(array(
                    'alert_type' => $alert_type,
                    'template' => $template,
                ));
            }
        }
        else{
            $this->context->smarty->assign(array(
                'alert_type' => 'disabled',
            ));
        };
        
        if ($this->module->is17) {
            $this->setTemplate('module:ets_affiliatemarketing/views/templates/front/sponsorship.tpl');
        } else {
            $this->setTemplate('sponsorship16.tpl');
        }
    }
    public function setMedia(){
        parent::setMedia();
        $this->addJs(_PS_MODULE_DIR_ . 'ets_affiliatemarketing/views/js/share_social.js');
    }
}