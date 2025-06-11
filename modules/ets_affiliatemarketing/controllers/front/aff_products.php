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
 * Class Ets_affiliatemarketingAff_productsModuleFrontController
 * @property Ets_affiliatemarketing $module;
 */
class Ets_affiliatemarketingAff_productsModuleFrontController extends Ets_affiliatemarketingAllModuleFrontController
{
    public $auth = true;
    public $guestAllowed = false;
    public $authRedirection = URL_EAM_AFF_PRODUCT;
    public $valid = true;
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
        if (Tools::isSubmit('affSubmitSharEmail')) {
            $this->submitShareMail();
        }
        $page= 'module-'.$this->module->name.'-aff_products';
        $meta = Meta::getMetaByPage($page,$this->context->language->id);
        $this->setMetas(array(
            'title' => isset($meta['title']) && $meta['title'] ? $meta['title'] : $this->module->l('Affiliate Products','aff_products'),
            'keywords' => isset($meta['keywords']) && $meta['keywords'] ? $meta['keywords'] : $this->module->l('Affiliate Products','aff_products'),
            'description' => isset($meta['description']) && $meta['description'] ? $meta['description'] : $this->module->l('Affiliate Products','aff_products'),
        ));
        if (!$this->module->is17) {
            $this->context->smarty->assign(array('controller' => 'aff_products'));
        }
        $alert_type = '';
        $user = Ets_User::getUserByCustomerId($this->context->customer->id);
        
        $message = '';
        if (!Configuration::get('ETS_AM_AFF_ENABLED')) {
            $this->valid = false;
            $alert_type = 'DISABLED';
        } else {
            if ($user) {
                if ($user['status'] == -1) {
                    $this->valid = false;
                    $alert_type = 'ACCOUNT_BANNED';
                    
                } else {
                    if ($user[EAM_AM_AFFILIATE_REWARD] == -1) {
                        $this->valid = false;
                        $alert_type = 'PROGRAM_SUSPENDED';
                    } elseif ($user[EAM_AM_AFFILIATE_REWARD] == -2) {
                        $this->valid = false;
                        $alert_type = 'PROGRAM_DECLINED';
                    }
                }
            }
            if($this->valid){
                $res_data = Ets_Affiliate::isCustomerCanJoinAffiliateProgramReturn();
                if (!$res_data['success']) {
                    $this->valid = false;
                    $alert_type = 'NEED_CONDITION';
                    
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
        }
        
        if (!$this->valid) {
            $this->context->smarty->assign(array(
                'alert_type' => $alert_type,
                'message' => $message
            ));
            if ($this->module->is17) {
                $this->setTemplate('module:ets_affiliatemarketing/views/templates/front/loyalty_program.tpl');
            } else {
                $this->setTemplate('loyalty_program16.tpl');
            }
        } else {
            if (Configuration::get('ETS_AM_AFF_REGISTER_REQUIRED')) {
                if ($user) {
                    if ($user[EAM_AM_AFFILIATE_REWARD] == 1) {
                        $this->valid = true;
                        $alert_type = 'REGISTERED';
                    } elseif ($user['status'] = 1 && ! $user[EAM_AM_AFFILIATE_REWARD]) {
                        $p = Ets_Participation::getProgramRegistered($this->context->customer->id, EAM_AM_AFFILIATE_REWARD);
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
                            $url_register = Ets_AM::getBaseUrlDefault('register',array('p'=>EAM_AM_AFFILIATE_REWARD));
                            Tools::redirect($url_register);
                        }
                    }
                } else {
                    $p = Ets_Participation::getProgramRegistered($this->context->customer->id, EAM_AM_AFFILIATE_REWARD);
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
                        $url_register = Ets_AM::getBaseUrlDefault('register',array('p'=>EAM_AM_AFFILIATE_REWARD));
                        Tools::redirect($url_register);
                    }
                }
            }
        }
        if (!$alert_type) {
            $this->valid = true;
        }
        $this->context->smarty->assign(array('alert_type' => $alert_type));
        if ($this->valid) {
            $categories = Ets_Reward_Product::getAffiliateProductCat();
            $template = 'affiliate_product.tpl';
            $aff_products = Ets_Reward_Product::getAffiliateProducts(array(
                'orderby' => Tools::getValue('orderBy'),
                'orderway' => Tools::getValue('orderWay'),
                'page' => Tools::getValue('page'),
                'limit' => Tools::getValue('limit'),
                'category' => Tools::getValue('category'),
                'search' => Tools::getValue('product_name'),
            ));
            $query = $aff_products['query'];
            $this->context->smarty->assign(array(
                'controller' => 'aff_products',
                'template' => $template,
                'eam_aff_products' => $aff_products,
                'eam_cats' => $categories,
                'query' => $query,
                'valid' => $this->valid
            ));
        }
        $this->context->smarty->assign(array(
            'aff_product_url' => Ets_AM::getBaseUrlDefault('aff_products'),
            'history_url' => Ets_AM::getBaseUrlDefault('affiliate'),
            'my_sale_url' => Ets_AM::getBaseUrlDefault('my_sale'),
        ));
        if ($this->module->is17) {
            $this->setTemplate('module:ets_affiliatemarketing/views/templates/front/_partials/affiliate_layout.tpl');
        } else {
            $this->setTemplate('_partials/affiliate_layout16.tpl');
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
    public function submitShareMail()
    {
        $errors = array();
        if (!$this->isTokenValid()) {
            $errors[] = $this->module->l('Token is not valid','aff_products');
        }
        else
        {
            $to = trim(Tools::getValue('aff_mails'));
            $aff_product_share_link = Tools::getValue('aff_product_share_link');
            $aff_message = Tools::getValue('aff_message');
            $aff_name = Tools::getValue('aff_name');
            $aff_product_share_name = Tools::getValue('aff_product_share_name');
            if (!Configuration::get('ETS_AM_AFF_ENABLED'))
                $errors[] = $this->module->l('You do not have permission to send this email');
            else {
                if (!$to)
                    $errors[] = $this->module->l('Email is required','aff_products');
                elseif (!Validate::isEmail($to))
                    $errors[] = $this->module->l('Email is not valid','aff_products');
                if ($aff_product_share_link && !Validate::isCleanHtml($aff_product_share_link))
                    $errors[] =$this->module->l('Product link is not valid','aff_products');
                if ($aff_message && !Validate::isCleanHtml($aff_message))
                    $errors[] = $this->module->l('Message is not valid','aff_products');
                if ($aff_name && !Validate::isCleanHtml($aff_name))
                    $errors[] = $this->module->l('Name is not valid','aff_products');
                if ($aff_product_share_name && !Validate::isCleanHtml($aff_product_share_name))
                    $errors[] = $this->module->l('Product name is not valid','aff_products');
            }
        }
        if (!$errors) {
            $data = array(
                '{link_product}' => $aff_product_share_link,
                '{aff_message}' => $aff_message,
                '{aff_name}' => $aff_name,
                '{name_product}' => $aff_product_share_name,
                '{customer_name}' => $this->context->customer->firstname . ' ' . $this->context->customer->lastname,
            );
            if (filter_var($to, FILTER_VALIDATE_EMAIL)) {
                $subjects = array(
                    'translation' => $this->module->l('Your friend has shared a product with you','aff_products'),
                    'origin' => 'Your friend has shared a product with you',
                    'specific' => false
                );
                Ets_aff_email::send(0, 'aff_share_product', $subjects, $data, array('customer'=>$to));
            }
            die(
                json_encode(
                    array(
                        'success' => $this->module->displaySuccessMessage($this->module->l('Email was sent successfully','aff_products')),
                    )
                )
            );
        } else {
            die(
            json_encode(
                array(
                    'errors' => $this->module->displayError($errors),
                )
            )
            );
        }
    }
}