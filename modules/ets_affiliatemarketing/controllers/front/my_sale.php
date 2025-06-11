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

class Ets_affiliatemarketingMy_saleModuleFrontController extends Ets_affiliatemarketingAllModuleFrontController
{
    public $auth = true;
    public $guestAllowed = false;
    public $authRedirection;

    protected $valid = true;

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
        $this->authRedirection = Ets_AM::getBaseUrlDefault('affiliate');
        parent::initContent();
        if(!$this->context->customer->isLogged())
            Tools::redirect($this->context->link->getPageLink('my-account'));
        $page= 'module-'.$this->module->name.'-my_sale';
        
        $meta = Meta::getMetaByPage($page,$this->context->language->id);
        $this->setMetas(array(
            'title' => isset($meta['title']) && $meta['title'] ? $meta['title'] : $this->module->l('My sales','my_sale'),
            'keywords' => isset($meta['keywords']) && $meta['keywords'] ? $meta['keywords'] : $this->module->l('My sales','my_sale'),
            'description' => isset($meta['description']) && $meta['description'] ? $meta['description'] : $this->module->l('My sales','my_sale'),
        ));
        $tabActive = Tools::getValue('tab_active');
        if (!$tabActive || !Validate::isCleanHtml($tabActive)) {
            $tabActive = 'product_sales';
        }
        $product_link = '';
        if($id_product = (int)Tools::getValue('id_product', false)){
           $product_link = Ets_AM::getBaseUrlDefault('my_sale',array('id_product'=>$id_product));
        }
        $this->context->smarty->assign(array(
            'product_link' => $product_link
        ));
        if (!$this->module->is17) {
            $this->context->smarty->assign(array('controller' => 'my_sale', 'tab_active' => $tabActive));
        }
        $this->context->smarty->assign(array(
            'aff_product_url' => Ets_AM::getBaseUrlDefault('aff_products'),
            'history_url' => Ets_AM::getBaseUrlDefault('affiliate'),
            'my_sale_url' => Ets_AM::getBaseUrlDefault('my_sale'),
            'controller' => 'my_sale',
            'eam_currency_code' => Configuration::get('ETS_AM_REWARD_DISPLAY') == 'point' ? (Configuration::get('ETS_AM_REWARD_UNIT_LABEL', $this->context->language->id) ? Configuration::get('ETS_AM_REWARD_UNIT_LABEL', $this->context->language->id) : Configuration::get('ETS_AM_REWARD_UNIT_LABEL', (int)Configuration::get('PS_LANG_DEFAULT'))) : $this->context->currency->iso_code,
        ));
        $alert_type = '';
        $message = '';
        $user = Ets_User::getUserByCustomerId($this->context->customer->id);
        if (!Configuration::get('ETS_AM_AFF_ENABLED')) {
            $this->valid = false;
            $alert_type = 'DISABLED';
        } else {
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
                    } elseif ($user['status'] = 1 && !$user[EAM_AM_AFFILIATE_REWARD]) {
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
                            $url_register =  Ets_AM::getBaseUrlDefault('register',array('p'=>EAM_AM_AFFILIATE_REWARD));
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
                        $url_register =Ets_AM::getBaseUrlDefault('register',array('p'=>EAM_AM_AFFILIATE_REWARD));
                        Tools::redirect($url_register);
                    }
                }
            } else {
                $this->valid = true;
            }
        }
        
        $this->context->smarty->assign(array('alert_type' => $alert_type));
        if ($this->valid) {
            $this->context->smarty->assign(array('valid' => $this->valid));
            if ($id_product = (int)Tools::getValue('id_product')) {
                if (Tools::isSubmit('get_stat_reward')) {
                    $response = array(
                        'stats' => Ets_AM::getProductStats($id_product, EAM_AM_AFFILIATE_REWARD,array(
                            'statistic' => Tools::getValue('statistic'),
                            'time_frame' => Tools::getValue('time_frame'),
                            'date_from' => Tools::getValue('date_from'),
                            'date_to' => Tools::getValue('date_to'),
                        )),
                        'countable' => Ets_AM::getProductSaleCount($id_product, EAM_AM_AFFILIATE_REWARD, array(
                            'time_frame' => Tools::getValue('time_frame'),
                        ))
                    );
                    die(json_encode($response));
                } 
                $product_sale = Ets_affiliate::getProductSaleS($id_product);
                if($product_sale['total_order'] && $product_sale['total_order'] > $product_sale['view_count'])
                {
                    $product_sale['view_count'] = $product_sale['total_order'];
                }
                if ((float)$product_sale['view_count'] > 0) {
                    $product_sale['c_rate'] = Tools::ps_round(((float)$product_sale['total_order'] / (float)$product_sale['view_count']) * 100,2) . ' %';
                } else {
                    $product_sale['c_rate'] = '0%';
                }
                $product= new Product($product_sale['id_product'],true,$this->context->language->id);
                $id_default_attribute = $product->cache_default_attribute;
                $product_sale['link'] = $this->context->link->getProductLink($product,null,null,null,null,null,$id_default_attribute);
                if ($tabActive === 'statistics') {
                    $template = 'my_sale_statistics.tpl';
                    $product_stats = Ets_AM::getProductStats($id_product, 'aff', array(
                        'statistic' => Tools::getValue('statistic'),
                        'time_frame' => Tools::getValue('time_frame'),
                        'date_from' => Tools::getValue('date_from'),
                        'date_to' => Tools::getValue('date_to'),
                    ));
                    $score_counter = Ets_AM::getProductSaleCount($id_product, 'aff', array(
                        'time_frame' => Tools::getValue('time_frame'),
                    ));
                    $this->context->smarty->assign(array(
                        'ets_am_product_stats' => json_encode($product_stats),
                        'score_counter' => $score_counter,
                        'default_currency' => Currency::getDefaultCurrency(),
                    ));
                } else {
                    $template = 'my_sale_products.tpl';
                    $customer_orders = Ets_Affiliate::getAffiliateCustomerInfo($id_product, array(
                        'product_sale_filter' => Tools::getValue('product_sale_filter'),
                        'product_sale_from' => Tools::getValue('product_sale_from'),
                        'product_sale_to' => Tools::getValue('product_sale_to'),
                        'product_sale_status' =>Tools::getValue('product_sale_status'),
                    ));
                    $query = $customer_orders['query'];
                    unset($customer_orders['query']);
                    $this->context->smarty->assign(array(
                        'ets_am_customer_orders' => $customer_orders,
                        'ETS_AM_DISPLAY_ID_ORDER' => Configuration::get('ETS_AM_DISPLAY_ID_ORDER'),
                        'query' => $query
                    ));
                }
                $this->context->smarty->assign(array(
                    'ets_am_product_sale' => $product_sale,
                    'template' => $template,
                    'tab_active' => $tabActive ? $tabActive : '',
                ));
            } else {
                $template = 'my_sale.tpl';
                $sales = Ets_Affiliate::getSales('aff',array(
                    'type_date_filter' => Tools::getValue('type_date_filter'),
                    'orderBy' => Tools::getValue('orderBy'),
                    'orderWay' => Tools::getValue('orderWay'),
                    'page' => (int)Tools::getValue('page'),
                ));
                $query = $sales['query'];
                unset($sales['query']);
                $this->context->smarty->assign(array(
                    'ets_am_sales' => $sales,
                    'template' => $template,
                    'valid' => true,
                    'tab_active' => '',
                    'query' => $query,
                    'back_link' => Ets_AM::getBaseUrlDefault('my_sale'),
                ));
            }
        }
        if ($this->module->is17) {
            $this->setTemplate('module:ets_affiliatemarketing/views/templates/front/_partials/affiliate_layout.tpl');
        } else {
            $this->setTemplate('_partials/affiliate_layout16.tpl');
        }
    }
}
