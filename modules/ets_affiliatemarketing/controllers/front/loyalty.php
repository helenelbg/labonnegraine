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

class Ets_affiliatemarketingLoyaltyModuleFrontController extends Ets_affiliatemarketingAllModuleFrontController
{
    public $auth = true;
    public $guestAllowed = false;
    public $authRedirection;

    protected $valid = true;

    public function init()
    {
        parent::init();
        $this->authRedirection = Ets_AM::getBaseUrlDefault('loyalty');
        $this->setMetas(array(
            'title' => $this->module->l('Loyalty program','loyalty'),
            'keywords' => $this->module->l('Loyalty program','loyalty'),
            'description' => $this->module->l('Loyalty program','loyalty'),
        ));
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
        $page= 'module-'.$this->module->name.'-loyalty';
        $meta = Meta::getMetaByPage($page,$this->context->language->id);
        $this->setMetas(array(
            'title' => isset($meta['title']) && $meta['title'] ? $meta['title'] : $this->module->l('Loyalty program','loyalty'),
            'keywords' => isset($meta['keywords']) && $meta['keywords'] ? $meta['keywords'] : $this->module->l('Loyalty program','loyalty'),
            'description' => isset($meta['description']) && $meta['description'] ? $meta['description'] : $this->module->l('Loyalty program','loyalty'),
        ));
        $alert_type = '';
        $user = Ets_User::getUserByCustomerId($this->context->customer->id);
        if (!Configuration::get('ETS_AM_LOYALTY_ENABLED')) {
            $this->valid = false;
            $alert_type = 'DISABLED';
        } else {
            if ($user) {
                if ($user['status'] == -1) {
                    $this->valid = false;
                    $alert_type = 'ACCOUNT_BANNED';
                } else {
                    if ($user[EAM_AM_LOYALTY_REWARD] == -1) {
                        $this->valid = false;
                        $alert_type = 'PROGRAM_SUSPENDED';
                    } elseif ($user[EAM_AM_LOYALTY_REWARD] == -2) {
                        $this->valid = false;
                        $alert_type = 'PROGRAM_DECLINED';
                    }
                }
            }
        }
        if (!$this->valid) {
            $this->context->smarty->assign(array(
                'alert_type' => $alert_type
            ));
            if ($this->module->is17) {
                $this->setTemplate('module:ets_affiliatemarketing/views/templates/front/loyalty_program.tpl');
            } else {
                $this->setTemplate('loyalty_program16.tpl');
            }
        } else {
            if (Configuration::get('ETS_AM_LOYALTY_REGISTER')) {
                if ($user) {
                    if ($user[EAM_AM_LOYALTY_REWARD] == 1) {
                        $this->valid = true;
                        $alert_type = 'REGISTERED';
                    } elseif ($user['status'] = 1 && ! $user[EAM_AM_LOYALTY_REWARD]) {
                        $p = Ets_Participation::getProgramRegistered($this->context->customer->id, EAM_AM_LOYALTY_REWARD);
                        if ($p) {
                            if ($p['status'] == 0) {
                                $this->valid = false;
                                $alert_type = 'REGISTER_SUCCESS';
                            } elseif ($p['status'] == 1) {
                                $this->valid = true;
                                $alert_type = 'REGISTERED';
                            } elseif ($p['status'] < 0) {
                                $this->valid = false;
                                $alert_type = 'PROGRAM_DECLINED';
                            }
                        } else {
                            $url_register = Ets_AM::getBaseUrlDefault('register',array('p'=>EAM_AM_LOYALTY_REWARD));
                            Tools::redirect($url_register);
                        }
                    }
                } else {
                    $p = Ets_Participation::getProgramRegistered($this->context->customer->id, EAM_AM_LOYALTY_REWARD);
                    if ($p) {
                        if ($p['status'] == 0) {
                            $this->valid = false;
                            $alert_type = 'REGISTER_SUCCESS';
                        } elseif ($p['status'] == 1) {
                            $this->valid = true;
                            $alert_type = 'REGISTERED';
                        } elseif ($p['status'] < 0) {
                            $this->valid = false;
                            $alert_type = 'PROGRAM_DECLINED';
                        }
                    } else {
                        $url_register = Ets_AM::getBaseUrlDefault('register',array('p'=>EAM_AM_LOYALTY_REWARD));
                        Tools::redirect($url_register);
                    }
                }
            }
        }
        if(!$alert_type){
            $message = '';
            if(!Ets_AM::isCustomerBelongToValidGroup($this->context->customer, 'ETS_AM_LOYALTY_GROUPS')){
                $alert_type = 'NEED_CONDITION';
                $this->valid = false;
                $this->context->smarty->assign(array(
                    'alert_type' => $alert_type,
                    'message' => $this->module->l('You are not in group customer to join this program', 'loyalty')
                ));
                if(!$message){
                    Tools::redirect($this->context->link->getPageLink('my-account', true));
                }
            }
            else{
                $total_order = Ets_AM::getTotalOrder($this->context->customer->id, $this->context);
                $min_order = Configuration::get('ETS_AM_LOYALTY_MIN_SPENT');
                if($min_order !== false && $min_order != '' && (float)$min_order > $total_order){
                    $alert_type = 'NEED_CONDITION';
                    $this->valid = false;
                    
                    $message = Configuration::get('ETS_AM_LOY_MSG_CONDITION', $this->context->language->id) ? strip_tags(Configuration::get('ETS_AM_LOY_MSG_CONDITION', $this->context->language->id)) : '';
                    $message  = str_replace('[min_order_total]', Ets_affiliatemarketing::displayPrice(Tools::convertPrice($min_order, $this->context->currency->id, true), $this->context->currency->id), $message);
                    $message  = str_replace('[total_past_order]', Ets_affiliatemarketing::displayPrice(Tools::convertPrice($total_order, $this->context->currency->id, true), $this->context->currency->id), $message);
                    $message  = str_replace('[amount_left]', Ets_affiliatemarketing::displayPrice(Tools::convertPrice((float)$min_order - (float)$total_order, $this->context->currency->id, true), $this->context->currency->id), $message);

                    $this->context->smarty->assign(array(
                        'alert_type' => $alert_type,
                        'message' => $message
                    ));
                    if(!$message){
                        Tools::redirect($this->context->link->getPageLink('my-account', true));
                    }
                }
            }
            
        }
        if (!$alert_type) {
            $this->valid = true;
        }
        $this->context->smarty->assign(array('alert_type' => $alert_type));
        $context= $this->context;
        if ($this->valid) {
            $rewardLink = Ets_AM::getBaseUrlDefault('history');

            //NEW LOYALTY REWARD
            $totalSpentLoy = Ets_Reward_Usage::getTotalSpentLoy($context->customer->id, false, null, $context);
            $totalLoy = Ets_Reward_Usage::getTotalEarn('loy', $context->customer->id, $context);
            $loyaltyReward = (float)$totalLoy - (float)$totalSpentLoy;
            
            $loyaltyReward = Ets_AM::displayReward($loyaltyReward);
            $this->context->smarty->assign(array(
                'eam_loyalty_reward' => $loyaltyReward,
                'eam_reward_link' => $rewardLink ,
                'valid' => $this->valid,
            ));
        } else {
            $this->context->smarty->assign(array(
                'alert_type' => $alert_type
            ));
        }
        if (!$this->module->is17) {
            $this->context->smarty->assign(array(
                'currency_code' => $this->context->currency->iso_code
            ));
        }
        $this->context->smarty->assign(
            array(
                'currency' => (array)$this->context->currency
            )
        );
        if ($this->module->is17) {
            $this->setTemplate('module:ets_affiliatemarketing/views/templates/front/loyalty_program.tpl');
        } else {
            $this->setTemplate('loyalty_program16.tpl');
        }
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
}
