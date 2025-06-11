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

if (!defined('_PS_VERSION_') || !defined('_ETS_AM_MODULE_'))
    exit;
class EtsAffDefine{

    protected static $instance;
    public $context;
    public $module;
	public function __construct($module = null){
        if (!(is_object($module)) || !$module) {
            $module = Module::getInstanceByName(_ETS_AM_MODULE_);
        }
        $this->module = $module;
        $context = Context::getContext();
        $this->context = $context;
	}
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new EtsAffDefine();
        }
        return self::$instance;
    }
    public function def_dashboard(){
        return '';
    }
    public function getMenuTabs()
    {
        return array(
            'dashboard' => array(
                'title' => $this->l('Dashboard'),
                'icon' => 'line-chart',
                'icon17' => 'eam_show_chart fa fa-line-chart eamfs0',
                'class' => 'AdminEtsAmDashboard',
            ),
            'marketing' => array(
                'title' => $this->l('Marketing program'),
                'icon' => 'marketing',
                'icon17' => 'marketing',
                'class'=> 'AdminEtsAmMarketing',
                'subs' => array(
                    'loyalty_program' => array(
                        'title' => $this->l('Loyalty program'),
                        'icon' => 'heart',
                        'icon17' => 'favorite',
                        'class' => 'AdminEtsAmLoyalty',
                    ),
                    'rs_program' => array(
                        'title' => $this->l('Referral / sponsorship program'),
                        'icon' => 'sitemap',
                        'icon17' => 'eam-device_hub fa fa-sitemap eamfs0',
                        'class' => 'AdminEtsAmRS',
                    ),
                    'affiliate_program' => array(
                        'title' => $this->l('Affiliate program'),
                        'icon' => 'share-alt',
                        'icon17' => 'share',
                        'class' => 'AdminEtsAmAffiliate',
                    ),
                )
            ),
            'rewards' => array(
                'title' => $this->l('Rewards'),
                'icon' => 'rewards',
                'icon17' => 'rewards',
                'class'=> 'AdminEtsAmRewards',
                'subs' => array(
                    'usage_settings' => array(
                        'title' => $this->l('Reward usage'),
                        'icon' => 'usd',
                        'icon17' => 'eam_usd fa fa-usd eamfs0',
                        'class' => 'AdminEtsAmRU',
                    ),
                    'reward_history' => array(
                        'title' => $this->l('Reward history'),
                        'icon' => 'history',
                        'icon17' => 'history',
                        'class' => 'AdminEtsAmRewardHistory',
                    ),
                    'withdraw_list' => array(
                        'title' => $this->l('Withdrawals'),
                        'icon' => 'list-ul',
                        'icon17' => 'format_list_bulleted',
                        'class' => 'AdminEtsAmWithdrawals',
                    ),
                ),
            ),
            'customer' => array(
                'title' => $this->l('Customers'),
                'icon' => 'customer',
                'icon17' => 'customer',
                'class'=> 'AdminEtsAmCustomers',
                'subs' => array(
                    'applications' => array(
                        'title' => $this->l('Applications'),
                        'icon' => 'user-plus',
                        'icon17' => 'eam_person_add fa fa-user-plus eamfs0',
                        'class' => 'AdminEtsAmApp',
                    ),
                    'reward_users' => array(
                        'title' => $this->l('Users'),
                        'icon' => 'user',
                        'icon17' => 'person',
                        'class' => 'AdminEtsAmUsers',
                    )
                ),
            ),
            'import_export' => array(
                'title' => $this->l('Backup / Restore'),
                'icon' => 'cloud-download',
                'icon17' => 'swap_horiz',
                'icon_admin' => 'swap_horiz',
                'img' => 'import_export.png',
                'class'=> 'AdminEtsAmBackup',
                'description' => $this->l('Create backup or restore module data and configuration'),
                'link'=> $this->context->link->getAdminLink('AdminEtsAmBackup'),
            ),
            'cronjob_settings' => array(
                'title' => $this->l('Cronjob'),
                'icon' => 'tasks',
                'icon17' => 'tasks',
                'class' => 'AdminEtsAmCronjob',
            ),
            'general' => array(
                'title' => $this->l('General settings'),
                'icon' => 'cogs',
                'icon17' => 'cogs',
                'class' => 'AdminEtsAmGeneral',
            ),
        );
    }
	public function def_config_tabs(){
		return  array(
            'dashboard' => array(
                'title' => $this->l('Dashboard'),
                'description' => $this->l('wrire_description'),
                'icon' => 'line-chart',
                'icon17' => 'eam_show_chart fa fa-line-chart eamfs0',
                'icon_admin' => 'show_chart',
                'class' => 'AdminEtsAmDashboard',
                'img' => 'dashboard.png',
                'link' => $this->context->link->getAdminLink('AdminEtsAmDashboard'),
            ),
            'loyalty_program' => array(
                'title' => $this->l('Loyalty program'),
                'icon' => 'heart',
                'icon17' => 'favorite',
                'icon_admin' => 'favorite',
                'class' => 'AdminEtsAmLoyalty',
                'img' => 'loyalty.png',
                'desc' => $this->l('Purchase products to get reward'),
                'description' => $this->l('Allow your customers to earn rewards when they purchase products'),
                'link' => $this->context->link->getAdminLink('AdminEtsAmLoyalty'),
                'subtabs' => array(
                    'loyalty_conditions' => array(
                        'title' => $this->l('Conditions'),
                        'icon' => 'check-square-o',
                        'link' => $this->context->link->getAdminLink('AdminEtsAmLoyalty'),
                    ),
                    'reward_settings' => array(
                        'title' => $this->l('Reward settings'),
                        'icon' => 'bookmark',
                        'link' => $this->context->link->getAdminLink('AdminEtsAmLoyalty'),
                    ),
                    'loyalty_messages' => array(
                        'title' => $this->l('Messages'),
                        'icon' => 'comment',
                        'link' => $this->context->link->getAdminLink('AdminEtsAmLoyalty'),
                    ),
                )
            ),
            'rs_program' => array(
                'title' => $this->l('Referral / sponsorship program'),
                'icon' => 'sitemap',
                'icon17' => 'eam-device_hub fa fa-sitemap eamfs0',
                'icon_admin' => 'device_hub',
                'class' => 'AdminEtsAmRS',
                'img' => 'referral.png',
                'desc' => $this->l('Refer friends to get reward'),
                'description' => $this->l('Turn your existing customers into sponsors and give rewards to the sponsors every time their friends register an account or make an order on your website'),
                'link' => $this->context->link->getAdminLink('AdminEtsAmRS'),
                'subtabs' => array(
                    'rs_program_conditions' => array(
                        'title' => $this->l('Conditions'),
                        'icon' => 'check-square-o',
                        'link' => $this->context->link->getAdminLink('AdminEtsAmRS'),
                    ),
                    'rs_program_reward_caculation' => array(
                        'title' => $this->l('Reward settings '),
                        'icon' => 'bookmark',
                        'link' => $this->context->link->getAdminLink('AdminEtsAmRS'),
                    ),
                    'rs_program_voucher' => array(
                        'title' => $this->l('Voucher'),
                        'icon' => 'gift',
                        'link' => $this->context->link->getAdminLink('AdminEtsAmRS'),
                    ),
                    'rs_program_suab' => array(
                        'title' => $this->l('Sponsor URL & Banner'),
                        'icon' => 'link',
                        'link' => $this->context->link->getAdminLink('AdminEtsAmRS'),
                    ),
                    'rs_program_messages' => array(
                        'title' => $this->l('Messages'),
                        'icon' => 'comment',
                        'link' => $this->context->link->getAdminLink('AdminEtsAmRS'),
                    ),
                )
            ),
            'affiliate_program' => array(
                'title' => $this->l('Affiliate program'),
                'icon' => 'share-alt',
                'icon17' => 'share',
                'icon_admin' => 'share',
                'class' => 'AdminEtsAmAffiliate',
                'img' => 'affiliate.png',
                'desc' => $this->l('Sell products to get reward'),
                'description' => $this->l('Turn your customers into hardworking sellers, they can sell your products and get commission (rewards)'),
                'link' => $this->context->link->getAdminLink('AdminEtsAmAffiliate'),
                'subtabs' => array(
                    'affiliate_conditions' => array(
                        'title' => $this->l('Conditions'),
                        'icon' => 'check-square-o',
                        'link' => $this->context->link->getAdminLink('AdminEtsAmAffiliate'),
                    ),
                    'affiliate_reward_caculation' => array(
                        'title' => $this->l('Reward settings'),
                        'icon' => 'bookmark',
                        'link' => $this->context->link->getAdminLink('AdminEtsAmAffiliate'),
                    ),
                    'affiliate_voucher' => array(
                        'title' => $this->l('Voucher'),
                        'icon' => 'gift',
                        'link' => $this->context->link->getAdminLink('AdminEtsAmAffiliate'),
                    ),
                    'affiliate_messages' => array(
                        'title' => $this->l('Messages'),
                        'icon' => 'comment',
                        'link' => $this->context->link->getAdminLink('AdminEtsAmAffiliate'),
                    ),
                )
            ),
            'usage_settings' => array(
                'title' => $this->l('Reward usage'),
                'icon' => 'usd',
                'icon17' => 'eam_usd fa fa-usd eamfs0',
                'icon_admin' => 'attach_money',
                'class' => 'AdminEtsAmRU',
                'img' => 'usage.png',
                'desc' => $this->l('Set up how to use reward'),
                'description' => $this->l('How customer/seller/sponsor uses their rewards and withdrawal methods for reward withdrawal'),
                'link' => $this->context->link->getAdminLink('AdminEtsAmRU'),
                'subtabs' => array(
                    'reward_usage' => array(
                        'title' => $this->l('Usage settings'),
                        'icon' => 'cog',
                        'link' => $this->context->link->getAdminLink('AdminEtsAmRU'),
                    ),
                    'payment_settings' => array(
                        'title' => $this->l('Withdrawal methods'),
                        'icon' => 'credit-card',
                        'link' => $this->context->link->getAdminLink('AdminEtsAmRU'),
                    ),
                )
            ),
            'applications' => array(
                'title' => $this->l('Applications'),
                'icon' => 'user-plus',
                'icon17' => 'eam_person_add fa fa-user-plus eamfs0',
                'icon_admin' => 'person_add',
                'class' => 'AdminEtsAmApp',
                'img' => 'application.png',
                'desc' => $this->l('Requests to join marketing programs'),
                'link' => $this->context->link->getAdminLink('AdminEtsAmApp'),
                'description' => $this->l('If you require customers to submit an application to join any marketing program, their applications will appear on this tab'),
            ),
            'reward_history' => array(
                'title' => $this->l('Reward history'),
                'icon' => 'history',
                'icon17' => 'history',
                'icon_admin' => 'history',
                'class' => 'AdminEtsAmRewardHistory',
                'img' => 'reward_history.png',
                'desc' => $this->l('Rewards given to customers'),
                'description' => $this->l('All the rewards generated by marketing programs'),
                'link' => $this->context->link->getAdminLink('AdminEtsAmRewardHistory'),
            ),
            'withdraw_list' => array(
                'title' => $this->l('Withdrawals'),
                'icon' => 'list-ul',
                'icon17' => 'format_list_bulleted',
                'icon_admin' => 'format_list_bulleted',
                'class' => 'AdminEtsAmWithdrawals',
                'img' => 'withdraw_list.png',
                'desc' => $this->l('Withdrawal requests from customers'),
                'description' => $this->l('Manage the withdrawal requests from customers'),
                'link' => $this->context->link->getAdminLink('AdminEtsAmWithdrawals'),
            ),
            'reward_users' => array(
                'title' => $this->l('Users'),
                'icon' => 'user',
                'icon17' => 'person',
                'class' => '',
                'img' => 'users.png',
                'desc' => $this->l('Users who applied to marketing programs'),
                'description' => $this->l('Users joined marketing programs'),
                'link' => $this->context->link->getAdminLink('AdminEtsAmUsers'),
            ),
            'import_export' => array(
                'title' => $this->l('Backup / Restore'),
                'icon' => 'cloud-download',
                'icon17' => 'swap_horiz',
                'icon_admin' => 'swap_horiz',
                'img' => 'import_export.png',
                'class'=> 'AdminEtsAmBackup',
                'description' => $this->l('Create backup or restore module data and configuration'),
                'link'=> $this->context->link->getAdminLink('AdminEtsAmBackup'),
            ),
            'cronjob_settings' => array(
                'title' => $this->l('Cronjob'),
                'icon' => 'tasks',
                'icon17' => 'eam_settings fa fa-tasks eamfs0',
                'icon_admin' => 'settings',
                'class' => 'AdminEtsAmCronjob',
                'img' => 'cronjob.png',
                'description' => $this->l('Send notification emails automatically via cronjob'),
                'link' => $this->context->link->getAdminLink('AdminEtsAmCronjob'),
                'subtabs' => array(
                    'cronjob_config' => array(
                        'title' => $this->l('Configuration'),
                        'icon' => 'wrench',
                        'link' => $this->context->link->getAdminLink('AdminEtsAmCronjob'),
                    ),
                    'cronjob_history' => array(
                        'title' => $this->l('Cronjob log'),
                        'icon' => 'list-ol',
                        'link' => $this->context->link->getAdminLink('AdminEtsAmCronjob'),
                    ),
                )
            ),
            'general' => array(
                'title' => $this->l('General settings'),
                'description' => $this->l('General settings for reward and notification email'),
                'icon' => 'cogs',
                'icon17' => 'eam_settings fa fa-cogs eamfs0',
                'icon_admin' => 'settings',
                'class' => 'AdminEtsAmGeneral',
                'img' => 'general.png',
                'link' => $this->context->link->getAdminLink('AdminEtsAmGeneral'),
                'subtabs' => array(
                    'general_settings' => array(
                        'title' => $this->l('General'),
                        'icon' => 'cog',
                        'link' => $this->context->link->getAdminLink('AdminEtsAmGeneral'),
                    ),
                    'general_email' => array(
                        'title' => $this->l('Email'),
                        'icon' => 'envelope',
                        'link' => $this->context->link->getAdminLink('AdminEtsAmGeneral'),
                    ),
                )
            ),
        );
	}
    public static function getBaseLink()
    {
        $context = Context::getContext();
        return (Configuration::get('PS_SSL_ENABLED_EVERYWHERE')?'https://':'http://').$context->shop->domain.$context->shop->getBaseURI();
    }
    public function def_reward_history(){
        return array(
            'list' => array(
                'title' => null,
                'orderBy' => 'id_ets_am_reward',
                'orderWay' => 'DESC',
                'action' => array(),
                'list' => 'getRewardHistory',
                'nb' => 'getRewardHistory',
                'no_link' => true,
                'bulk_actions' => array(),
                'list_id' => 'ets_am_reward',
                'alias' => 'r'
            ),
            'fields' => array(
                'id_ets_am_reward' => array(
                    'title' => $this->l('Reward ID'),
                    'align' => 'center',
                    'type' => 'text',
                    'class' => 'id',
                    'orderby' => true,
                ),
                'program' => array(
                    'title' => $this->l('Program'),
                    'align' => 'center',
                    'type' => 'text',
                    'class' => 'program',
                    'orderby' => true,
                ),

                'customer' => array(
                    'title' => $this->l('Customer'),
                    'align' => 'left',
                    'type' => 'text',
                    'orderby' => true,
                ),

                'amount' => array(
                    'title' => $this->l('Reward'),
                    'align' => 'center',
                    'type' => 'text',
                    'orderby' => true,
                ),
                'status' => array(
                    'title' => $this->l('Reward status'),
                    'align' => 'center',
                    'type' => 'text',
                    'orderby' => true,
                ),
                'datetime_added' => array(
                    'title' => $this->l('Date'),
                    'align' => 'left',
                    'type' => 'datetime',
                    'orderby' => true,
                ),
                'note' => array(
                    'title' => $this->l('Note'),
                    'align' => 'left',
                    'type' => 'text',
                    'orderby' => true,
                ),
                'product_name' => array(
                    'title' => $this->l('Product'),
                    'align' => 'left',
                    'type' => 'text',
                    'orderby' => true,
                ),
                'actions' => array(
                    'title' => $this->l('Action'),
                )
            ),
        );
    }

    public function def_loyalty_conditions(){
        return array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Conditions'),
                ),
                'input' => array(),
                'submit' => array(
                    'title' => $this->l('Save'),
                    'class' => 'btn btn-success pull-right'
                ),
                'name' => 'loyalty_conditions'
            ),
            'config' => array(
                'ETS_AM_LOYALTY_ENABLED' => array(
                    'type' => 'switch',
                    'label' => $this->l('Enable loyalty program'),
                    'default' => 0,
                    'col' => '3',
                    'desc' => $this->l('This program allows customers to earn rewards when they purchase products. Encourage customer to purchase more to get more rewards thus increase your sales'),
                ),
                'ETS_AM_LOYALTY_GROUPS' => array(
                    'type' => 'ets_checkbox_group',
                    'col' => 3,
                    'label' => $this->l('Applicable customer group'),
                    'values' => $this->getLoyaltyGroup(false),
                    'desc' => $this->l('Select customer groups who can join loyalty program.'),
                    'required' => true
                ),
                'ETS_AM_LOYALTY_REGISTER' => array(
                    'type' => 'switch',
                    'label' => $this->l('Require customer to submit an application to join loyalty program'),
                    'default' => 0,
                    'col' => 3,
                    'values' => array(
                        array(
                            'label' => $this->l('Yes'),
                            'value' => 1,
                            'id' => 'ETS_AM_LOYALTY_REGISTER_ON'
                        ),
                        array(
                            'label' => $this->l('No'),
                            'value' => 0,
                            'id' => 'ETS_AM_LOYALTY_REGISTER_OFF'
                        )
                    ),
                    'desc' => $this->l('This is to make sure the loyalty program is only available for customers who are really interested in the program. You (admin) will need to manually approve the applications')
                ),
                'ETS_AM_LOY_INTRO_REG' => array(
                    'type' => 'switch',
                    'col' => 3,
                    'label' => $this->l('Require customer to enter an introduction about them?'),
                    'default' => 0,
                    'values' => array(
                        array(
                            'label' => $this->l('Yes'),
                            'value' => 1,
                            'id' => 'ETS_AM_LOY_INTRO_REG_ON'
                        ),
                        array(
                            'label' => $this->l('No'),
                            'value' => 0,
                            'id' => 'ETS_AM_LOY_INTRO_REG_OFF'
                        )
                    ),
                ),
                'ETS_AM_LOYALTY_TIME' => array(
                    'type' => 'ets_radio_group',
                    'col' => 3,
                    'label' => $this->l('Availability'),
                    'default' => '',
                    'values' => $this->getLoyTimes(),
                    'desc' => $this->l('Availability time of the loyalty program')
                ),
                'ETS_AM_LOYALTY_TIME_FROM' => array(
                    'type' => 'text',
                    'label' => $this->l('From'),
                    'col' => 3,
                    'class' => 'ets_am_datepicker',
                    'validate' => 'isDate'
                ),
                'ETS_AM_LOYALTY_TIME_TO' => array(
                    'type' => 'text',
                    'label' => $this->l('To'),
                    'col' => 3,
                    'class' => 'ets_am_datepicker',
                    'validate' => 'isDate'
                ),
                'ETS_AM_LOYALTY_MIN_SPENT' => array(
                    'type' => 'text',
                    'label' => $this->l('Minimum amount spent to join loyalty program'),
                    'suffix' => $this->context->currency->iso_code,
                    'col' => 3,
                    'validate' => 'isUnsignedFloat',
                    'default' => '0',
                    'desc' => $this->l('Calculated by total cost of past orders. This condition makes sure customer who has spent a specific amount of money on your website will receive loyalty rewards if they purchase more. Leave blank to ignore this condition')
                ),
            ),
        );
    }

    public function def_reward_settings(){
        Ets_affiliatemarketing::registerPlugins();
        return array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Reward settings'),
                ),
                'input' => array(),
                'submit' => array(
                    'title' => $this->l('Save'),
                    'class' => 'btn btn-success pull-right'
                ),
                'name' => 'reward_settings'
            ),
            'config' => array(
                'ETS_AM_LOYALTY_BASE_ON' => array(
                    'type' => 'ets_radio_group',
                    'label' => $this->l('How to calculate loyalty reward when customer purchase this product?'),
                    'desc' => $this->l('This is default setting. You can select particular setting for each product on product information page. Loyalty products are products available for loyalty program and are selected in "Conditions" tab'),
                    'col' => 3,
                    'default' => 'CART',
                    'values' => array(
                        'FIXED' => array(
                            'id' => 'ETS_AM_LOYALTY_BASE_ON_FIXED',
                            'title' => $this->l('Fixed amount per loyalty product'),
                            'value' => 'FIXED',
                            'data_decide' => 'ETS_AM_LOYALTY_AMOUNT,ETS_AM_QTY_MIN',
                            'default' => true
                        ),
                        'CART' => array(
                            'id' => 'ETS_AM_LOYALTY_BASE_ON_CART',
                            'title' => $this->l('Percentage of loyalty product price'),
                            'value' => 'CART',
                            'data_decide' => 'ETS_AM_LOYALTY_GEN_PERCENT,ETS_AM_QTY_MIN'
                        ),
                        'SPC_FIXED' => array(
                            'id' => 'ETS_AM_LOYALTY_SPC_FIXED',
                            'title' => $this->l('Fixed amount per shopping cart (Exclude shipping)'),
                            'value' => 'SPC_FIXED',
                            'data_decide' => 'ETS_AM_LOYALTY_AMOUNT,ETS_AM_QTY_MIN',
                        ),
                        'SPC_PERCENT' => array(
                            'id' => 'ETS_AM_LOYALTY_SPC_PERCENT',
                            'title' => $this->l('Percentage of shopping cart value (Exclude shipping)'),
                            'value' => 'SPC_PERCENT',
                            'data_decide' => 'ETS_AM_LOYALTY_GEN_PERCENT,ETS_AM_QTY_MIN',
                        ),
                        'NOREWARD' => array(
                            'id' => 'ETS_AM_LOYALTY_NO_REWARD',
                            'title' => $this->l('No reward'),
                            'value' => 'NOREWARD',
                            'data_decide' => 'ets-am-not-show'
                        ),
                    ),
                ),
                'ETS_AM_LOYALTY_AMOUNT' => array(
                    'type' => 'text',
                    'label' => $this->l('Amount'),
                    'suffix' => $this->context->currency->iso_code,
                    'col' => 8,
                    'default' => 0,
                    'fill' => true,
                    'validate' => 'isUnsignedFloat',
                ),
                'ETS_AM_LOYALTY_GEN_PERCENT' => array(
                    'type' => 'text',
                    'label' => $this->l('Percentage'),
                    'suffix' => '%',
                    'col' => 3,
                    'default' => 0,
                    'fill' => true,
                ),
                'ETS_AM_LOY_CAT_TYPE' => array(
                    'type' => 'ets_radio_group',
                    'col' => 3,
                    'label' => $this->l('Applicable product categories'),
                    'values' => array(
                        array(
                            'id' => 'ETS_AM_LOY_CAT_TYPE_ALL',
                            'title' => $this->l('All product categories'),
                            'value' => 'ALL',
                            'is_all' => true,
                            'default' => true,
                            'data_decide' => 'ets-am-not-show'
                        ),
                        array(
                            'id' => 'ETS_AM_LOY_CAT_TYPE_SPECIFIC',
                            'title' => $this->l('Specific product categories'),
                            'value' => 'SPECIFIC',
                            'data_decide' => 'ETS_AM_LOYALTY_CATEGORIES[]'
                        ),
                    ),
                ),
                'ETS_AM_LOYALTY_CATEGORIES' => array(
                    'label' => $this->l('Categories include products in subcategories'),
                    'type' => 'categories',
                    'fill' => true,
                    'tree' => array(
                        'id' => 'loyalty-categories-tree',
                        'selected_categories' => Tools::isSubmit('ETS_AM_LOYALTY_CATEGORIES') && ($ETS_AM_LOYALTY_CATEGORIES = Tools::getValue('ETS_AM_LOYALTY_CATEGORIES')) &&  $ETS_AM_LOYALTY_CATEGORIES !== 'ALL' ? $ETS_AM_LOYALTY_CATEGORIES : explode(',', Configuration::get('ETS_AM_LOYALTY_CATEGORIES')),
                        'disabled_categories' => null,
                        'use_checkbox' => true,
                        'use_search' => true,
                        'root_category' => Category::getRootCategory()->id
                    ),
                    'class' => 'ETS_AM_LOYALTY_CATEGORIES',
                    'desc' => $this->l('Select product categories in which customer will get loyalty reward when purchasing a product.')
                ),
                'ETS_AM_LOYALTY_INCLUDE_SUB' => array(
                    'type' => 'switch',
                    'col' => 3,
                    'label' => $this->l('Include products in subcategories'),
                    'default' => 1
                ),
                'ETS_AM_LOYALTY_SPECIFIC' => array(
                    'type' => 'text_search_prd',
                    'label' => $this->l('Include specific products'),
                    'default' => '',
                    'desc' => $this->l('You can add specific products that are out of the selected categories to the loyalty product list.')
                ),
                'ETS_AM_LOYALTY_EXCLUDED' => array(
                    'type' => 'text_search_prd',
                    'label' => $this->l('Excluded products'),
                    'default' => '',
                    'desc' => $this->l('Customer will not get loyalty reward when purchasing those products even when they are in selected categories above')
                ),
                'ETS_AM_LOYALTY_MULTIPE_BY_PRODUCT' => array(
                    'type' => 'switch',
                    'label' => $this->l('Multiply loyalty reward by product number'),
                    'default' => '1',
                    'col' => 3,
                    'desc' => $this->l('Customer will get more reward if they buy more of the same product. Will apply if not specified for single product')
                ),
                'ETS_AM_RECALCULATE_COMMISSION' => array(
                    'type' => 'switch',
                    'col' => 3,
                    'label' => $this->l('Recalculate commission when admin add/edit the products for customer\'s order'),
                    'desc' => $this->l('The module does not recalculate points/rewards and does not interfere further when the admin adds a discount code to the order. The module will only recalculate the points/rewards when changing the product of the order. If the admin manually adds a discount code to the order, they will also need to manually edit the points/rewards for the customer.'),
                    'default' => '0',
                ),
                'ETS_AM_LOYALTY_EXCLUDE_TAX' => array(
                    'type' => 'switch',
                    'col' => 3,
                    'label' => $this->l('Exclude taxes before calculating reward'),
                    'default' => '0',
                ),
                'ETS_AM_LOYALTY_NOT_FOR_DISCOUNTED' => array(
                    'type' => 'switch',
                    'col' => 3,
                    'label' => $this->l('Do not give reward on discounted products'),
                    'default' => '0'
                ),
                'ETS_AM_LOYALTY_MIN' => array(
                    'type' => 'text',
                    'label' => $this->l('Minimum cart total to get loyalty reward'),
                    'value' => 1,
                    'col' => 3,
                    'default' => 0,
                    'validate' => 'isUnsignedFloat',
                    'suffix' => $this->context->currency->iso_code,
                    'desc' => $this->l('Customer is required to have total cost of all products in their shopping cart exceeds this value to be eligible to get loyalty rewards'),
                ),
                'ETS_AM_QTY_MIN' => array(
                    'type' => 'text',
                    'label' => $this->l('Minimum quantity required (each product) to get loyalty reward'),
                    'col' => 3,
                    'validate' => 'isUnsignedInt',
                    'suffix' => $this->l('item(s)'),
                    'desc' => $this->l('This is default value. You can set particular value for each product on product information page. Leave blank to ignore this condition'),
                ),
                'ETS_AM_LOYALTY_MAX' => array(
                    'type' => 'text',
                    'label' => $this->l('Maximum reward amount per shopping cart'),
                    'value' => 1,
                    'col' => 3,
                    'validate' => 'isUnsignedFloat',
                    'suffix' => $this->context->currency->iso_code,
                    'desc' => $this->l('Maximum reward amount that customer will be given when they complete an order. If reward amount is greater than this value (after being calculated based on set rules), it will be lowered to this value. Leave this field blank to ignore this limitation'),
                ),

                'ETS_AM_LOYALTY_MAX_DAY' => array(
                    'type' => 'text',
                    'label' => $this->l('Reward availability'),
                    'suffix' => $this->l('day(s)'),
                    'col' => 3,
                    'validate' => 'isUnsignedInt',
                    'desc' => $this->l('Reward will be expired if not used by customers within this limited time since they get it. Leave blank to make reward available all the time. If you set any value for this field, please also set up cronjob so that when this condition is satisfied, cronjob will update status of the reward.')
                ),
            )
        );
    }

    public function def_loyalty_messages(){
        return array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Messages'),
                ),
                'input' => array(),
                'submit' => array(
                    'title' => $this->l('Save'),
                    'class' => 'btn btn-success pull-right'
                ),
                'name' => 'reward_settings'
            ),
            'config' => array(
                'ETS_AM_LOYALTY_MSG_PRODUCT' => array(
                    'label' => $this->l('Message on product page when minimum cart amount is NOT required or has been satisfied'),
                    'type' => 'textarea',
                    'rows' => 3,
                    'lang' => true,
                    'default' => $this->l('You will get [amount] in reward when purchase [min_productnumber] of this product. The reward can be used to pay for your next orders, converted into voucher code or withdrawn to your bank account. '),
                    'desc' => $this->l('Available data-tags: [highlight][amount][end_highlight], [highlight][min_productnumber][end_highlight]. Leave blank to not display this message.')
                ),
                'ETS_AM_LOYALTY_MSG_CART_REQUIRED' => array(
                    'label' => $this->l('Message on product page when minimum cart amount is required'),
                    'type' => 'textarea',
                    'rows' => 3,
                    'lang' => true,
                    'default' => $this->l('You will get [amount] in reward when purchase [min_productnumber] of this product. The reward can be used to pay for your next orders, converted into voucher code or withdrawn to your bank account. Shopping cart minimum amount of [cart_minimum_amount] is required'),
                    'desc' => $this->l('Available data-tags: [highlight][amount][end_highlight], [highlight][min_productnumber][end_highlight], [highlight][cart_minimum_amount][end_highlight]. Leave blank to not display this message')
                ),

                'ETS_AM_LOYALTY_MSG_CART_1' => array(
                    'label' => $this->l('Message on shopping cart page when minimum shopping cart amount required'),
                    'type' => 'textarea',
                    'rows' => 3,
                    'lang' => true,
                    'default' => $this->l('You\'re nearly to be eligible to join our loyalty program and get [amount] in reward, just buy [amount_left] more to get ready!'),
                    'desc' => $this->l('Available data-tags: [highlight][amount][end_highlight], [highlight][amount_left][end_highlight]. Leave blank to not display this message.')
                ),
                'ETS_AM_LOYALTY_MSG_CART_2' => array(
                    'label' => $this->l('Message on shopping cart page when customer is eligible to get reward '),
                    'type' => 'textarea',
                    'rows' => 3,
                    'lang' => true,
                    'default' =>  $this->l('Congratulations! You\'re eligible to join our loyalty program. You will get [amount] in reward when complete checking out this shopping cart.'),
                    'desc' => $this->l('Available data-tags: [highlight][amount][end_highlight]. Leave blank to not display this message.')
                ),
                'ETS_AM_LOYALTY_MSG_ORDER' => array(
                    'label' => $this->l('Message on confirmation page when customer complete order'),
                    'type' => 'textarea',
                    'rows' => 3,
                    'lang' => true,
                    'default' =>  $this->l('Congratulations! You get [amount] in reward. Reward status: [reward_status].'),
                    'desc' => $this->l('Available data-tags: [highlight][amount][end_highlight], [highlight][reward_status][end_highlight]. Leave blank to not display this message.')
                ),
                'ETS_AM_LOY_INTRO_PROGRAM' => array(
                    'label' => $this->l('Introduction about program'),
                    'type' => 'textarea',
                    'rows' => 3,
                    'lang' => true,
                    'default' => $this->l('Join our loyalty program to purchase products at cheaper prices. This program is not public to all customers, please submit an application with additional information about you. Our moderator team will review your application to decide if you are eligible for attending the program.'),
                    'desc' => $this->l('This message will display on the program\'s registration page. Leave blank to not display this message')
                ),
                'ETS_AM_LOY_MSG_CONDITION' => array(
                    'label' => $this->l('Message on "My account" page when customer is not eligible to join loyalty program due to total order spent doesn\'t meet minimum amount required'),
                    'type' => 'textarea',
                    'rows' => 3,
                    'lang' => true,
                    'default' => $this->l('You are NOT eligible to join our loyalty program because your total past order value [total_past_order], doesn\'t meet minimum amount required [min_order_total]. Please purchase [amount_left] more to be eligible for loyalty program.'),
                    'desc' => $this->l('Available data-tags: [highlight][total_past_order][end_highlight], [highlight][min_order_total][end_highlight], [highlight][amount_left][end_highlight]. Leave this field blank to hide "Loyalty program" tab from "My account" page when minimum total order doesn\'t reach the required amount')
                )
            )
        );
    }

    public function def_loyalty_email(){
        return array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Email settings'),
                ),
                'input' => array(),
                'submit' => array(
                    'title' => $this->l('Save'),
                    'class' => 'btn btn-success pull-right'
                ),
                'name' => 'reward_settings'
            ),
            'config' => array(
                
            )
        );
    }

    public function def_rs_program_conditions(){
        return array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Conditions'),
                ),
                'input' => array(),
                'submit' => array(
                    'title' => $this->l('Save'),
                    'class' => 'btn btn-success pull-right'
                ),
                'name' => 'rs_program_conditions'
            ),
            'config' => array(
                'ETS_AM_REF_ENABLED' => array(
                    'type' => 'switch',
                    'label' => $this->l('Enable referral/sponsorship program'),
                    'default' => 0,
                    'col' => 3,
                    'desc' => $this->l('This program allows customers to become sponsors of your website. They can earn reward when their friends CREATE ACCOUNT or when their friends PURCHASE PRODUCTS on the website')
                ),
                'ETS_AM_REF_GROUPS' => array(
                    'type' => 'ets_checkbox_group',
                    'col' => 3,
                    'label' => $this->l('Applicable customer groups'),
                    'desc' => $this->l('Select customer groups who can join referral program'),
                    'values' => $this->getRefGroup(),
                    'required' => true,
                    'col' => 3,
                ),
                'ETS_AM_REF_REGISTER_REQUIRED' => array(
                    'type' => 'switch',
                    'col' => 3,
                    'label' => $this->l('Require customer to submit an application to join referral/sponsorship program'),
                    'desc' => $this->l('This option makes sure only customers who are really interested in the referral/sponsorship program will be sponsor for your website. You (admin) will need to manually approve the applications to allow customer to join referral/sponsorship program'),
                    'default' => 0,
                ),
                'ETS_AM_REF_INTRO_REG' => array(
                    'type' => 'switch',
                    'col' => 3,
                    'label' => $this->l('Require customer to enter an introduction about them?'),
                    'default' => 0,
                    'values' => array(
                        array(
                            'label' => $this->l('Yes'),
                            'value' => 1,
                            'id' => 'ETS_AM_REF_INTRO_REG_ON'
                        ),
                        array(
                            'label' => $this->l('No'),
                            'value' => 0,
                            'id' => 'ETS_AM_REF_INTRO_REG_OFF'
                        )
                    ),
                ),
                'ETS_AM_REF_MIN_ORDER' => array(
                    'type' => 'text',
                    'label' => $this->l('Minimum order total required to become a sponsor'),
                    'desc' => $this->l('Calculated by total cost of all past orders. This is to make sure you only allow quality customers to become sponsor. Leave blank to allow all customers from selected groups to become sponsor'),
                    'col' => 3,
                    'default' => '',
                    'validate' => 'isUnsignedFloat',
                    'suffix' => $this->context->currency->iso_code
                ),
                'ETS_AM_REF_MAX_FRIEND' => array(
                    'type' => 'text',
                    'label' => $this->l('Maximum number of friend can be sponsored '),
                    'desc' => $this->l('This helps limit the width of multi-level marketing tree by making sure a sponsor can only have a limited number of directed sponsored friends. Leave blank if you do not want to limit the number of friend that a sponsor can refer to your website'),
                    'col' => 3,
                    'default' => '',
                    'validate' => 'isUnsignedFloat'
                ),
                'ETS_AM_REF_EMAIL_INVITE_FRIEND' => array(
                    'label' => $this->l('Allow sponsor to invite their friends via email'),
                    'type' => 'switch',
                    'col' => 3,
                    'default' => 1,
                    'desc' => $this->l('This option allows sponsor to enter their friend email and name into an invitation form on the front office to send invitation to the friends encouraging them to create new account on the website.')
                ),
                'ETS_AM_REF_MAX_INVITATION' => array(
                    'label' => $this->l('Maximum number of invitation'),
                    'type' => 'text',
                    'default' => '',
                    'col' => 5,
                    'validate' => 'isUnsignedInt',
                    'desc' => $this->l('Maximum number of invitation that sponsor can send to their friends via email. Leave blank to not limit the number of invitation'),
                ),
            )
        );
    }

    public function def_rs_program_reward_caculation(){
        Ets_affiliatemarketing::registerPlugins();
        return array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Reward settings'),
                ),
                'input' => array(),
                'submit' => array(
                    'title' => $this->l('Save'),
                    'class' => 'btn btn-default pull-right'
                ),
                'name' => 'rs_program_reward_caculation'
            ),
            'config' => array(
                'ETS_AM_REF_FRIEND_REG' => array(
                    'type' => 'switch',
                    'label' => $this->l('Give reward to sponsor when their friends register new account'),
                    'desc' => $this->l('This is to encourage customers to invite their friends to create a new account on the website'),
                    'default' => 1,
                    'col' => 3,
                    'caption_before' => $this->l('How to give reward when new accounts generated? (Invite friends to create account to earn reward)'),
                ),
                'ETS_AM_REF_FRIEND_EACH_REG_COST' => array(
                    'type' => 'text',
                    'label' => $this->l('How much do you pay for each new registration?'),
                    'default' => 0,
                    'validate' => 'isUnsignedFloat',
                    'col' => 3,
                    'required' => (int)Tools::getValue('ETS_AM_REF_FRIEND_REG'),
                    'showrequired' => true,
                    'suffix' => $this->context->currency->iso_code,
                ),
                'ETS_AM_REF_FRIEND_FIRST_REG_ONLY' => array(
                    'type' => 'text',
                    'label' => $this->l('Only pay for'),
                    'validate' => 'isUnsignedInt',
                    'col' => 3,
                    'suffix' => $this->l('first new registrations'),
                    'desc' => $this->l('Leave blank if you want to pay for all new accounts generated by a sponsor')
                ),
                'ETS_AM_REF_FRIEND_ORDER_REQUIRED' => array(
                    'type' => 'switch',
                    'label' => $this->l('Only give reward to sponsor when their friend completes first order (They have paid for the first order)'),
                    'default' => 1,
                    'col' => 3,
                    'desc' => $this->l('This is to make sure new account is REAL and only pay if new user has bought something on the website.')
                ),
                'ETS_AM_REF_REGISTER_CATEGORIES' => array(
                    'label' => $this->l('Exclude certain categories'),
                    'type' => 'categories',
                    'tree' => array(
                        'id' => 'ref-register-categories-tree',
                        'selected_categories' => Tools::getValue('ETS_AM_REF_REGISTER_CATEGORIES',Configuration::get('ETS_AM_REF_REGISTER_CATEGORIES')? explode(',',Configuration::get('ETS_AM_REF_REGISTER_CATEGORIES')) :array() ),
                        'disabled_categories' => null,
                        'use_checkbox' => true,
                        'use_search' => true,
                        'root_category' => Category::getRootCategory()->id
                    ),
                    'class' => 'ETS_AM_REF_REGISTER_CATEGORIES',
                    'form_group_class' => 'register'
                ),
                'ETS_AM_REF_REGISTER_PRODUCTS_EXCLUDED' => array(
                    'type' => 'text_search_prd',
                    'label' => $this->l('Exclude certain products'),
                    'form_group_class' => 'register'
                ),
                'ETS_AM_REF_REGISTER_PRODUCTS_EXCLUDED_DISCOUNT' => array(
                    'type' => 'switch',
                    'col' => 3,
                    'label' => $this->l('Exclude products with discount'),
                    'default' => 0,
                    'values' => array(
                        array(
                            'label' => $this->l('Yes'),
                            'value' => 1,
                            'id' => 'ETS_AM_AFF_PRODUCTS_EXCLUDED_DISCOUNT_on'
                        ),
                        array(
                            'label' => $this->l('No'),
                            'value' => 0,
                            'id' => 'ETS_AM_AFF_PRODUCTS_EXCLUDED_DISCOUNT_off'
                        )
                    ),
                    'form_group_class' => 'register'
                ),
                'ETS_AM_REF_GIVE_REWARD_ON_ORDER' => array(
                    'type' => 'switch',
                    'col' => 3,
                    'label' => $this->l('Give reward to sponsor when their friend purchase products? (Multi level marketing)'),
                    'default' => 1,
                    'caption_before' => $this->l('How to give reward when new orders generated? (Multi-level marketing - Earn reward on sponsored friends\' orders)'),
                    'desc' => $this->l('This allows sponsors to earn reward when their sponsored friends purchase products. Support multi-level marketing (sponsors can also earn reward when customers from all lower levels purchase products)'),
                ),
                'ETS_AM_REF_REWARD_FRIEND_LIMIT' => array(
                    'type' => 'text',
                    'label' => $this->l('Only give reward on orders of'),
                    'suffix' => $this->l('first new friends'),
                    'validate' => 'isUnsignedInt',
                    'desc' => $this->l('This is to limit the number of orders that sponsors will earn reward by limiting the number of direct sponsored friends that sponsors can earn rewards on their orders. When an order is placed by a customer who is out of this limit, all sponsors (any level) will not receive reward.  Leave blank if you want to give reward to sponsors when any of friends that they sponsored purchase products from your shop')
                ),
                'ETS_AM_REF_REWARD_ORDER_LIMIT' => array(
                    'type' => 'text',
                    'label' => $this->l('Only give reward for'),
                    'suffix' => $this->l('first orders of each friend'),
                    'validate' => 'isUnsignedInt',
                    'desc' => $this->l('This is to limit the number of orders that sponsors will earn reward by limiting the number of order to give reward to sponsors (all level) of each friend. Leave blank if you want to give reward to sponsor on all their friend\'s orders')
                ),
                'ETS_AM_REF_REWARD_ORDER_MIN' => array(
                    'type' => 'text',
                    'label' => $this->l('Minimum order total of sponsor friend required to get reward'),
                    'validate' => 'isUnsignedInt',
                    'desc' => $this->l('Apply for the order of sponsor friend. This is to make sure you earn money more than the reward you give. Leave blank to give the reward to sponsor for any amount order of their friend'),
                    'suffix' => $this->context->currency->iso_code

                ),
                'ETS_AM_REF_HOW_TO_CALCULATE' => array(
                    'type' => 'ets_radio_group',
                    'col' => 3,
                    'label' => $this->l('How to calculate reward for sponsor when their friends purchase products?'),
                    'values' => array(
                        array(
                            'id' => 'ETS_AM_REF_HOW_TO_CALCULATE_PERCENTATE',
                            'title' => $this->l('Percentage of order total cost (shipping excluded)'),
                            'value' => 'PERCENTATE',
                            'data_decide' => 'ETS_AM_REF_TAX_EXCLUDED,ETS_AM_REF_SPONSOR_COST_PERCENT',
                            'default' => true,
                        ),
                        array(
                            'id' => 'ETS_AM_REF_HOW_TO_CALCULATE_FIXED',
                            'title' => $this->l('Fixed amount each order'),
                            'value' => 'FIXED',
                            'data_decide' => 'ETS_AM_REF_SPONSOR_COST_FIXED'
                        ),
                    ),
                ),
                'ETS_AM_REF_SPONSOR_COST_PERCENT' => array(
                    'type' => 'text',
                    'validate' => 'isUnsignedFloat',
                    'label' => $this->l('Sponsor cost (by order percentage)'),
                    'suffix' => $this->l('% of order total cost'),
                    'required' => (int)Tools::getValue('ETS_AM_REF_GIVE_REWARD_ON_ORDER') && Tools::getValue('ETS_AM_REF_HOW_TO_CALCULATE')=='PERCENTATE',
                    'showrequired' => true,
                    'desc' => $this->l('This is maximum total of money that you want to pay for the sponsorship system when an order is generated, calculated by percentage based on order total cost. This is to make sure you will not pay so much marketing cost when an order is generated by sponsorship system. How much in total would you want to pay to sponsors for each order of their friend? Leave this field blank will stop giving reward to all sponsors when a new order is generated.')
                ),
                'ETS_AM_REF_TAX_EXCLUDED' => array(
                    'type' => 'switch',
                    'col' => 3,
                    'label' => $this->l('Exclude taxes before calculating reward'),
                    'default' => 0
                ),
                'ETS_AM_REF_SPONSOR_COST_FIXED' => array(
                    'type' => 'text',
                    'validate' => 'isUnsignedFloat',
                    'label' => $this->l('Sponsor cost (fixed amount)'),
                    'suffix' => $this->context->currency->iso_code,
                    'required' => (int)Tools::getValue('ETS_AM_REF_GIVE_REWARD_ON_ORDER') && Tools::getValue('ETS_AM_REF_HOW_TO_CALCULATE')=='FIXED',
                    'showrequired' => true,
                    'desc' => $this->l('This is maximum total of money that you want to pay for the sponsorship system when an order is generated. This is to make sure you will not pay so much marketing cost when an order is generated by sponsorship system. How much in total would you want to pay to sponsors for each order of their friend?')
                ),
                'ETS_AM_REF_SPONSOR_COST_LEVEL_1' => array(
                    'type' => 'text',
                    'validate' => 'isUnsignedFloat',
                    'label' => $this->l('Level 1'),
                    'suffix' => $this->l('% of initial sponsor cost'),
                    'required' => (int)Tools::getValue('ETS_AM_REF_GIVE_REWARD_ON_ORDER'),
                    'showrequired' => true,
                    'desc' => $this->l('This is direct level of a sponsored account.')
                ),
                'ETS_AM_REF_ENABLED_MULTI_LEVEL' => array(
                    'type' => 'switch',
                    'col' => 3,
                    'label' => $this->l('Enable multiple levels'),
                    'default' => 0
                ),
                'ETS_AM_REF_SPONSOR_COST_LEVEL_LOWER' => array(
                    'type' => 'text',
                    'validate' => 'isUnsignedFloat',
                    'label' => $this->l('Lower levels '),
                    'suffix' => $this->l('% of remaining sponsor cost after paying for higher levels'),
                    'desc' => $this->l('Leave blank if you don\'t want to give reward for lower levels. Leave this field blank will help limit the depth of multi-level marketing tree by not giving rewards to all sponsor levels')
                ),

                'ETS_AM_REF_SPONSOR_COST_REST_TO_FIRST' => array(
                    'type' => 'switch',
                    'label' => $this->l('Add the rest sponsor cost (if have) to "Level 1" if no more level found'),
                    'desc' => $this->l('Enable this if you would want to give the rest money from "Sponsor cost" (if have after calculating reward for all sponsor levels defined above) to "Level 1" (direct sponsor). Otherwise the rest money won\'t be give to any sponsors, just keep it for you!'),
                    'default' => 0,
                    'col' => 3
                ),
                'ETS_AM_REF_CATEGORIES' => array(
                    'label' => $this->l('Exclude certain categories'),
                    'type' => 'categories_tree',
                    'tree' => $this->module->renderCategoryTree(
                        array(
                            'tree' => array(
                                'id' => 'ref-categories-tree',
                                'selected_categories' => Tools::getValue('ETS_AM_REF_CATEGORIES',Configuration::get('ETS_AM_REF_CATEGORIES') ? explode(',',Configuration::get('ETS_AM_REF_CATEGORIES')) :array() ),
                                'disabled_categories' => null,
                                'use_checkbox' => true,
                                'use_search' => true,
                                'root_category' => Category::getRootCategory()->id
                            ),
                            'name' => 'ETS_AM_REF_CATEGORIES',
                        )
                    ),
                    'class' => 'ETS_AM_REF_CATEGORIES',
                ),
                'ETS_AM_REF_PRODUCTS_EXCLUDED' => array(
                    'type' => 'text_search_prd',
                    'label' => $this->l('Exclude certain products'),
                ),
                'ETS_AM_REF_PRODUCTS_EXCLUDED_DISCOUNT' => array(
                    'type' => 'switch',
                    'col' => 3,
                    'label' => $this->l('Exclude products with discount'),
                    'default' => 0,
                    'values' => array(
                        array(
                            'label' => $this->l('Yes'),
                            'value' => 1,
                            'id' => 'ETS_AM_AFF_PRODUCTS_EXCLUDED_DISCOUNT_on'
                        ),
                        array(
                            'label' => $this->l('No'),
                            'value' => 0,
                            'id' => 'ETS_AM_AFF_PRODUCTS_EXCLUDED_DISCOUNT_off'
                        )
                    ),
                ),
            )
        );
    }

    public function def_rs_program_voucher(){
        return array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Voucher'),
                ),
                'input' => array(),
                'submit' => array(
                    'title' => $this->l('Save'),
                    'class' => 'btn btn-default pull-right'
                ),
                'name' => 'rs_program_voucher'
            ),
            'config' => array(
                'ETS_AM_REF_OFFER_VOUCHER' => array(
                    'label' => $this->l('Offer a voucher code to sponsored friend?'),
                    'type' => 'switch',
                    'col' => 3,
                    'default' => 0,
                    'desc' => $this->l('Give a voucher code to sponsored friend will help improve the efficiency of the Referral / sponsorship program. Sponsored friends will see the voucher code after they finish creating account'),
                ),
                'ETS_AM_REF_VOUCHER_TYPE' => array(
                    'label' => $this->l('Voucher type'),
                    'type' => 'ets_radio_group',
                    'col' => 3,
                    'values' => array(
                        array(
                            'title' => $this->l('Fixed voucher code').EtsAffDefine::displayText($this->l('Configure discounts'),'a',null,null,$this->context->link->getAdminLink('AdminCartRules')),
                            'value' => 'FIXED',
                            'id' => 'ETS_AM_REF_VOUCHER_TYPE_FIXED',
                            'default' => true,
                        ),
                        array(
                            'title' => $this->l('Dynamic voucher code'),
                            'value' => 'DYNAMIC',
                            'id' => 'ETS_AM_REF_VOUCHER_TYPEDYNAMIC',
                        ),
                    )
                ),
                'ETS_AM_REF_VOUCHER_CODE' => array(
                    'label' => $this->l('Voucher code'),
                    'type' => 'text',
                    'col' => 3,
                    'fill' => true,
                    'desc' => $this->l('You need to generate voucher code by creating a new cart rule that can be used by any customer.'),
                ),
                //Voucher form here
                'ETS_AM_REF_FREE_SHIPPING' => array(
                    'label' => $this->l('Free shipping'),
                    'type' => 'switch',
                    'col' => 3,
                    'default' => 0,
                ),
                'ETS_AM_REF_APPLY_DISCOUNT' => array(
                    'label' => $this->l('Apply a discount'),
                    'type' => 'ets_radio_group',
                    'col' => 3,
                    'values' => array(
                        array(
                            'id' => 'ETS_AM_REF_APPLY_DISCOUNT_PERCENT',
                            'value' => 'PERCENT',
                            'title' => $this->l('Percentage (%)'),
                            'default' => true
                        ),
                        array(
                            'id' => 'ETS_AM_REF_APPLY_DISCOUNT_AMOUNT',
                            'value' => 'AMOUNT',
                            'title' => $this->l('Amount')
                        ),
                        array(
                            'id' => 'ETS_AM_REF_APPLY_DISCOUNT_OFF',
                            'value' => 'OFF',
                            'title' => $this->l('None')
                        ),
                    ),
                ),
                'ETS_AM_REF_REDUCTION_PERCENT' => array(
                    'label' => $this->l('Discount percentage'),
                    'type' => 'text',
                    'suffix' => '%',
                    'default' => '',
                    'col' => 3,
                    'fill' => true,
                    'validate' => 'isPercentage',
                    'desc' => $this->l('Does not apply to the shipping costs'),
                ),
                'ETS_AM_REF_REDUCTION_AMOUNT' => array(
                    'label' => $this->l('Amount'),
                    'type' => 'custom_amount',
                    'col' => 3,
                    'id' => 'ETS_AM_REF_REDUCTION_AMOUNT',
                    'default' => 1,
                    'items' => ['ETS_AM_REF_ID_CURRENCY','ETS_AM_REF_REDUCTION_TAX'],
                    'fill' => true,
                    '_currencies' => $this->getCurrencies(),
                    'default_currency' => (int)Configuration::get('PS_CURRENCY_DEFAULT'),
                    'validate' => 'isUnsignedFloat'
                ),
                
                'ETS_AM_REF_ID_CURRENCY' => array(
                    'label' => null,
                    'type' => 'custom_discount_1',
                    'options' => array(
                        'query' => $this->getCurrencies(),
                        'id' => 'id_currency',
                        'name' => 'name',
                    ),
                    'default' => isset($this->getCoutries()[0]['id_currency']) ? $this->getCoutries()[0]['id_currency'] : 0
                ),
                'ETS_AM_REF_REDUCTION_TAX' => array(
                    'label' => null,
                    'type' => 'custom_discount_2',
                    'fill' => true,
                    'options' => array(
                        'query' => array(
                            array(
                                'id_option' => 0,
                                'name' => $this->l('Tax excluded')
                            ),
                            array(
                                'id_option' => 1,
                                'name' => $this->l('Tax included')
                            ),
                        ),
                        'id' => 'id_option',
                        'name' => 'name',
                    ),
                    'default' => '0',
                ),
                'ETS_AM_REF_EXCLUDE_SPECIAL' => array(
                    'label' => $this->l('Exclude discounted products'),
                    'type' => 'switch',
                    'col' => 3,
                    'default' => 0,
                ),
                'ETS_AM_REF_DISCOUNT_MIN_AMOUNT' => array(
                    'label' => $this->l('Minimum amount'),
                    'type' => 'custom_discount',
                    'id' => 'ETS_AM_REF_DISCOUNT_MIN_AMOUNT',
                    'items' => ['ETS_AM_REF_DISCOUNT_MIN_AMOUNT_CURRENCY', 'ETS_AM_REF_DISCOUNT_MIN_AMOUNT_TAX', 'ETS_AM_REF_DISCOUNT_MIN_AMOUNT_SHIPPING'],
                    'default' => '',
                    'validate' => 'isUnsignedFloat',
                    '_currencies' => $this->getCurrencies(),
                    'default_currency' => (int)Configuration::get('PS_CURRENCY_DEFAULT'),
                    'desc' => $this->l('Minimum amount in shopping cart to apply the voucher code.'),
                    'col' => 9,
                    'class' => 'aff_min_amount',
                ),
                'ETS_AM_REF_DISCOUNT_MIN_AMOUNT_CURRENCY' => array(
                    'label' => '',
                    'type' => 'custom_discount_2',
                    'default' => '',
                ),
                'ETS_AM_REF_DISCOUNT_MIN_AMOUNT_TAX' => array(
                    'label' => '',
                    'type' => 'custom_discount_3',
                    'default' => '',
                ),
                'ETS_AM_REF_DISCOUNT_MIN_AMOUNT_SHIPPING' => array(
                    'label' => '',
                    'type' => 'custom_discount_4',
                    'default' => '',
                ),
                'ETS_AM_REF_DISCOUNT_PREFIX' => array(
                    'label' => $this->l('Discount prefix'),
                    'type' => 'text',
                    'default' => 'REF_',
                    'col' => 3
                ),
                'ETS_AM_REF_DISCOUNT_DESC' => array(
                    'label' => $this->l('Discount name'),
                    'type' => 'text',
                    'rows' => 3,
                    'lang' => true,
                    'required'=>true,
                    'col' => 5,
                    'fill' => true,
                    'validate' => 'isImageTypeName',
                    'desc' => $this->l('Lorem: chi chap nhan: a-zA-Z0-9_ - '),
                    'default' => $this->l('Sponsorship program'),
                ),
                'ETS_AM_REF_APPLY_DISCOUNT_IN' => array(
                    'label' => $this->l('Discount availability'),
                    'type' => 'text',
                    'required' => true,
                    'suffix' => 'days',
                    'col' => 3,
                    'fill' => true,
                    'default' => '30',
                    'validate' => 'isUnsignedInt',
                ),
                'ETS_AM_REF_WELCOME_MSG' => array(
                    'label' => $this->l('Welcome message'),
                    'type' => 'textarea',
                    'lang' => true,
                    'fill' => true,
                    'default' => $this->l('Congratulations! You get a voucher code of [discount_value] for your next orders. Time is limited!! Hurry up.'),
                    'desc' => $this->l(' Available tags: [highlight][discount_value][end_highlight]'),
                ),
                'ETS_AM_REF_USE_OTHER_VOUCHER' => array(
                    'label' => $this->l('Can use with other voucher in the same shopping cart'),
                    'type' => 'switch',
                    'col' => 3,
                    'default' => 0,
                ),
                'ETS_AM_SELL_OFFER_VOUCHER' => array(
                    'label' => $this->l('Allow sponsors to generate their voucher code?'),
                    'type' => 'switch',
                    'col' => 3,
                    'default' => 0,
                    'desc' => $this->l('Sponsor can generate their own voucher code to send to their friends. When their friends use the voucher code, they will get discount and sponsors will get commission'),
                ),
                'ETS_AM_REF_VOUCHER_CODE_DESC' => array(
                    'label' => $this->l('Sponsor voucher code description'),
                    'type' => 'textarea',
                    'lang' => true,
                    'fill' => true,
                    'required'=>true,
                    'default' => $this->l('Share this voucher code to your friends. They will get [discount_value] off for their order and you will also get commission on your friend\'s orders.'),
                    'desc' => $this->l('Display explanation of how this "sponsor voucher code" works to your sponsors. Available tags: [highlight][discount_value][end_highlight]')
                ),
                'ETS_AM_SELL_FREE_SHIPPING' => array(
                    'label' => $this->l('Free shipping'),
                    'type' => 'switch',
                    'col' => 3,
                    'default' => 0,
                ),
                'ETS_AM_SELL_APPLY_DISCOUNT' => array(
                    'label' => $this->l('Apply a discount'),
                    'type' => 'ets_radio_group',
                    'col' => 3,
                    'values' => array(
                        array(
                            'id' => 'ETS_AM_SELL_APPLY_DISCOUNT_PERCENT',
                            'value' => 'PERCENT',
                            'title' => $this->l('Percentage (%)'),
                            'default' => true
                        ),
                        array(
                            'id' => 'ETS_AM_SELL_APPLY_DISCOUNT_AMOUNT',
                            'value' => 'AMOUNT',
                            'title' => $this->l('Amount')
                        ),
                        array(
                            'id' => 'ETS_AM_SELL_APPLY_DISCOUNT_OFF',
                            'value' => 'OFF',
                            'title' => $this->l('None')
                        ),
                    ),
                ),
                'ETS_AM_SELL_REDUCTION_PERCENT' => array(
                    'label' => $this->l('Discount percentage'),
                    'type' => 'text',
                    'suffix' => '%',
                    'default' => '',
                    'col' => 3,
                    'fill' => true,
                    'validate' => 'isPercentage',
                    'desc' => $this->l('Does not apply to the shipping costs'),
                ),
                'ETS_AM_SELL_REDUCTION_AMOUNT' => array(
                    'label' => $this->l('Amount'),
                    'type' => 'custom_amount',
                    'col' => 3,
                    'default' => 1,
                    'fill' => true,
                    'id' => 'ETS_AM_SELL_REDUCTION_AMOUNT',
                    'items' => ['ETS_AM_SELL_ID_CURRENCY','ETS_AM_SELL_REDUCTION_TAX'],
                    '_currencies' => $this->getCurrencies(),
                    'default_currency' => (int)Configuration::get('PS_CURRENCY_DEFAULT'),
                    'validate' => 'isUnsignedFloat'
                ),
                'ETS_AM_SELL_ID_CURRENCY' => array(
                    'label' => null,
                    'type' => 'custom_discount_1',
                    'options' => array(
                        'query' => $this->getCurrencies(),
                        'id' => 'id_currency',
                        'name' => 'name',
                    ),
                    'default' => isset($this->getCoutries()[0]['id_currency']) ? $this->getCoutries()[0]['id_currency'] : 0
                ),
                'ETS_AM_SELL_REDUCTION_TAX' => array(
                    'label' => null,
                    'type' => 'custom_discount_2',
                    'fill' => true,
                    'options' => array(
                        'query' => array(
                            array(
                                'id_option' => 0,
                                'name' => $this->l('Tax excluded')
                            ),
                            array(
                                'id_option' => 1,
                                'name' => $this->l('Tax included')
                            ),
                        ),
                        'id' => 'id_option',
                        'name' => 'name',
                    ),
                    'default' => '0',
                ),
                'ETS_AM_SELL_EXCLUDE_SPECIAL' => array(
                    'label' => $this->l('Exclude discounted products'),
                    'type' => 'switch',
                    'col' => 3,
                    'default' => 0,
                ),
                'ETS_AM_SELL_DISCOUNT_MIN_AMOUNT' => array( 
                    'label' => $this->l('Minimum amount'),
                    'type' => 'custom_discount',
                    'id' => 'ETS_AM_SELL_DISCOUNT_MIN_AMOUNT',
                    'items' => ['ETS_AM_SELL_DISCOUNT_MIN_AMOUNT_CURRENCY', 'ETS_AM_SELL_DISCOUNT_MIN_AMOUNT_TAX', 'ETS_AM_SELL_DISCOUNT_MIN_AMOUNT_SHIPPING'],
                    'default' => '',
                    'validate' => 'isUnsignedFloat',
                    '_currencies' => $this->getCurrencies(),
                    'default_currency' => (int)Configuration::get('PS_CURRENCY_DEFAULT'),
                    'col' => 9,
                    'class'=> 'aff_min_amount',
                    'desc' => $this->l('Minimum amount in shopping cart to apply the voucher code'),
                ),
                'ETS_AM_SELL_DISCOUNT_MIN_AMOUNT_CURRENCY' => array(
                    'label' => '',
                    'type' => 'custom_discount_2',
                    'default' => '',
                ),
                'ETS_AM_SELL_DISCOUNT_MIN_AMOUNT_TAX' => array(
                    'label' => '',
                    'type' => 'custom_discount_3',
                    'default' => '',
                ),
                'ETS_AM_SELL_DISCOUNT_MIN_AMOUNT_SHIPPING' => array(
                    'label' => '',
                    'type' => 'custom_discount_4',
                    'default' => '',
                ),
                'ETS_AM_SELL_DISCOUNT_PREFIX' => array(
                    'label' => $this->l('Discount prefix'),
                    'type' => 'text',
                    'default' => 'SPCODE_',
                    'col' => 3
                ),
                'ETS_AM_SELL_DISCOUNT_DESC' => array(
                    'label' => $this->l('Discount name'),
                    'type' => 'text',
                    'required'=>true,
                    'rows' => 3,
                    'lang' => true,
                    'col' => 5,
                    'fill' => true,
                    'default' => $this->l('Sponsor voucher code'),
                    'validate' => 'isImageTypeName',
                    'desc' => $this->l('Lorem: chi chap nhan: a-zA-Z0-9_ - '),
                ),
                'ETS_AM_SELL_APPLY_DISCOUNT_IN' => array(
                    'label' => $this->l('Discount availability'),
                    'type' => 'text',
                    'required' => true,
                    'suffix' => 'days',
                    'col' => 3,
                    'fill' => true,
                    'default' => '30',
                    'validate' => 'isUnsignedInt',
                ),
                'ETS_AM_SELL_QUANTITY' => array(
                    'label' => $this->l('Total available'),
                    'type' => 'text',
                    'required' => true,
                    'col' => 3,
                    'fill' => true,
                    'default' => '999999',
                ),
                'ETS_AM_SELL_USE_OTHER_VOUCHER' => array(
                    'label' => $this->l('Can use with other voucher in the same shopping cart'),
                    'type' => 'switch',
                    'col' => 3,
                    'default' => 0,
                ),
            )
        );
    }

    public function def_rs_program_suab(){
        return array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Sponsor url and banner'),
                ),
                'input' => array(),
                'submit' => array(
                    'title' => $this->l('Save'),
                    'class' => 'btn btn-success pull-right'
                ),
                'name' => 'rs_program_voucher'
            ),
            'config' => array(
                'ETS_AM_REF_DISPLAY_URL' => array(
                    'type' => 'switch',
                    'label' => $this->l('Display sponsor url'),
                    'default' =>1,
                    'caption_before' => $this->l('Sponsor url'),
                ),
                'ETS_AM_REF_DEFAULT_BANNER' => array(
                    'type' => 'file',
                    'label' => $this->l('Default sponsor banner'),
                    'file_type' => 'png,jpg,jpeg,gif',
                    'file_size' => Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE')*1024,
                    'is_image' => true,
                    'desc' => $this->l('Sponsor banner is used for advertisement of Referral / sponsorship program. Sponsor can copy banner\'s embed code then paste it on their website, blog, forum, etc. When someone click on the banner, they will be redirected to the sponsor\'s URL where the sponsor\'s ID will be saved in cookie to calculate reward for the sponsor if the customer buy products or register new account'),
                    'caption_before' => $this->l('Sponsor banner'),
                    'default' => 'default_banner.jpg'
                ),
                'ETS_AM_RESIZE_BANNER' => array(
                    'label' => $this->l('Resize banner image before uploading'),
                    'type' => 'switch',
                    'col' => 3,
                    'default' => 1,
                ),
                'ETS_AM_RESIZE_BANNER_WITH' => array(
                    'type' => 'text',
                    'label' => $this->l('Banner width'),
                    'validate' => 'isInt',
                    'suffix' => 'px',
                    'class' => 'resize_banner_width',
                    'col' => 3,
                    'default' => 400
                ),
                'ETS_AM_RESIZE_BANNER_HEIGHT' => array(
                    'type' => 'text',
                    'label' => $this->l('Banner height'),
                    'validate' => 'isInt',
                    'suffix' => 'px',
                    'class' => 'resize_banner_height',
                    'col' => 3,
                    'default' => 300
                ),
                'ETS_AM_REF_ALLOW_CUSTOM_BANNER' => array(
                    'label' => $this->l('Allow sponsor to upload their custom banner'),
                    'type' => 'switch',
                    'col' => 3,
                    'default' => 0,
                    'desc' => $this->l('Enable this option to allow customer to upload their own banner from their sponsor account management area on the front office. If no custom banner is uploaded, the default banner will be used'),
                ),
                'ETS_AM_REF_URL_REDIRECT' => array(
                    'type' => 'text',
                    'label' => $this->l('Redirect URL'),
                    'desc' => $this->l('Redirect customer to another URL when they land on a sponsor\'s URL. Sponsor\'s ID is saved in cookie before the redirection in order to identify if customer come from referrence of a sponsor when they register new account or purchase products. Leave this field blank to keep customer stay on the sponsor\'s URL'),
                ),
                'ETS_AM_REF_SOCIAL_TITLE' => array(
                    'type' => 'text',
                    'label' => $this->l('Title'),
                    'lang' => true,
                    'desc' => $this->l('Custom title appears on social networks (Facebook, Twitter, Linked in, etc) when sponsor\'s URL is shared. Leave blank to use default website title.'),
                    'caption_before' => $this->l('Social network appearance'),
                ),
                'ETS_AM_REF_SOCIAL_DESC' => array(
                    'type' => 'textarea',
                    'lang' => true,
                    'label' => $this->l('Description'),
                    'desc' => $this->l('Custom description appears on social networks when sponsor\'s URL is shared. Leave blank to use default website description.')
                ),
                'ETS_AM_REF_SOCIAL_IMG' => array(
                    'type' => 'file',
                    'label' => $this->l('Image'),
                    'file_type' => 'png,jpg,jpeg,gif',
                    'file_size' => Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE')*1024,
                    'is_image' => true,
                    'desc' => $this->l('Custom image appears on social networks when sponsor\'s URL is shared. Leave blank to use default image (specified by social network when it feeds your website content)')
                ),
                'ETS_AM_REF_INTRO_ENABLED' => array(
                    'type' => 'switch',
                    'col' => 3,
                    'label' => $this->l('Enable introduction popup'),
                    'caption_before' => $this->l('Referral/sponsorship program introduction popup'),
                    'desc' => $this->l('Enable this option to display an introduction popup to introduce/invite customers to join referral/sponsorship program when they land on the website.')
                ),
                'ETS_AM_REF_INTRO_BANNER' => array(
                    'label' => $this->l('Popup banner'),
                    'type' => 'file',
                    'file_type' => 'png,jpg,jpeg,gif',
                    'file_size' => Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE')*1024,
                    'is_image' => true,
                    'default' => 'ets_am_ref_intro_banner.jpg'
                ),
                'ETS_AM_REF_INTRO_TITLE' => array(
                    'label' => $this->l('Popup title'),
                    'type' => 'text',
                    'lang' => true,
                    'required' => true,
                    'default' => $this->l('Refer your friends, get our money'),
                ),
                'ETS_AM_REF_INTRO_CONTENT' => array(
                    'label' => $this->l('Popup content'),
                    'type' => 'textarea',
                    'autoload_rte' => true,
                    'rows' => 3,
                    'lang' => true,
                    'required' => true,
                    'default' => $this->module->getPopupDefault(),
                ),
                'ETS_AM_REF_INTRO_REDISPLAY' => array(
                    'label' => $this->l('Redisplay popup in'),
                    'type' => 'text',
                    'desc' => $this->l('Leave blank to display the popup just once for each customer (do not display the popup again if customer closes it)'),
                    'suffix' => $this->l('day(s)'),
                    'col' => 3
                ),
                'ETS_AM_REF_INTRO_DISPLAY_JUST_ONCE' => array(
                    'label' => $this->l('Do not display the popup if customer has invited their friends'),
                    'type' => 'switch',
                    'col' => 3,
                    'default' => true,
                ),
                'ETS_AM_REF_INTRO_CUSTOMER_GROUP' => array(
                    'label' => $this->l('Customer group to see the popup'),
                    'type' => 'ets_checkbox_group',
                    'col' => 3,
                    'values' => $this->getLoyaltyGroup(array(Configuration::get('PS_CUSTOMER_GROUP')))
                ),
            )
        );
    }

    public function def_rs_program_email(){
        return array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Email'),
                ),
                'input' => array(),
                'submit' => array(
                    'title' => $this->l('Save'),
                    'class' => 'btn btn-success pull-right'
                ),
                'name' => 'rs_program_email'
            ),
            'config' => array(
                
            )
        );
    }

    public function def_rs_program_messages(){
        return array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Message'),
                ),
                'input' => array(),
                'submit' => array(
                    'title' => $this->l('Save'),
                    'class' => 'btn btn-success pull-right'
                ),
                'name' => 'rs_program_messages'
            ),
            'config' => array(
                'ETS_AM_REF_TEXT_EXPLANATION' => array(
                    'label' => $this->l('Explanation message about referral / sponsorship program'),
                    'type' => 'textarea',
                    'lang' => true,
                    'default' => null,
                    'desc' => $this->l('This message appears on "Referral program" area to explain to customer/sponsor how the program works. Leave this field blank if you do not want to display any explanation message.'),
                ),
                'ETS_AM_REF_INTRO_PROGRAM' => array(
                    'label' => $this->l('Introduction about program'),
                    'type' => 'textarea',
                    'rows' => 3,
                    'lang' => true,
                    'default' => $this->l('Join our referral program and invite your friends to create new accounts or purchase products to earn rewards. This program is not public to all customers, please submit an application with additional information about you. Our moderator team will review your application to decide if you are eligible for attending the program.'),
                    'desc' => $this->l('This message will display on the program\'s registration page')
                ),
                'ETS_AM_REF_MSG_CONDITION' => array(
                    'label' => $this->l('Message on "My account" page when customer is not eligible to join referral program due to total order spent doesn\'t meet minimum amount required'),
                    'type' => 'textarea',
                    'rows' => 3,
                    'lang' => true,
                    'default' => $this->l('You are NOT eligible to join our referral program because your total past order value [total_past_order], doesn\'t meet minimum amount required [min_order_total]. Please purchase [amount_left] more to be eligible for referral program.'),
                    'desc' => $this->l('Available data-tags: [highlight][total_past_order][end_highlight], [highlight][min_order_total][end_highlight], [highlight][amount_left][end_highlight]. Leave this field blank to hide "Referral program" tab from "My account" page when minimum total order doesn\'t reach the required amount')
                )
            )
        );
    }

    public function def_affiliate_conditions(){
        Ets_affiliatemarketing::registerPlugins();
        return array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Conditions'),
                ),
                'input' => array(),
                'submit' => array(
                    'title' => $this->l('Save'),
                    'class' => 'btn btn-success pull-right'
                ),
                'name' => 'affiliate_conditions'
            ),
            'config' => array(
                'ETS_AM_AFF_ENABLED' => array(
                    'label' => $this->l('Enable affiliate program'),
                    'type' => 'switch',
                    'default' => 0,
                    'col' => 3,
                    'desc' => $this->l('This program allows customers to become an affiliate of your website who can refer/sell your website\'s products to their friends and earn commission (reward) when someone buys the products they refer')
                ),
                'ETS_AM_AFF_GROUPS' => array(
                    'label' => $this->l('Applicable customer groups'),
                    'type' => 'ets_checkbox_group',
                    'fill' => true,
                    'col' => 3,
                    'required' => true,
                    'values' => $this->getAfffGroup(),
                    'desc' => $this->l('Select customer groups who can join affiliate program')
                ),
                'ETS_AM_AFF_REGISTER_REQUIRED' => array(
                    'label' => $this->l('Require customer to submit an application to join affiliate program'),
                    'type' => 'switch',
                    'default' => 0,
                    'col' => 3,
                    'desc' => $this->l('This is to make sure the affiliate program is only available for customers who are really interested in the program. You (admin) will need to manually approve the applications'),
                ),
                'ETS_AM_AFF_INTRO_REG' => array(
                    'type' => 'switch',
                    'col' => 3,
                    'label' => $this->l('Require customer to enter an introduction about them?'),
                    'default' => 0,
                    'values' => array(
                        array(
                            'label' => $this->l('Yes'),
                            'value' => 1,
                            'id' => 'ETS_AM_AFF_INTRO_REG_ON'
                        ),
                        array(
                            'label' => $this->l('No'),
                            'value' => 0,
                            'id' => 'ETS_AM_AFF_INTRO_REG_OFF'
                        )
                    ),
                ),
                'ETS_AM_AFF_MIN_ORDER' => array(
                    'type' => 'text',
                    'validate' => 'isUnsignedFloat',
                    'label' => $this->l('Minimum order total required to join affiliate program'),
                    'desc' => $this->l('This option is to make sure only quality customers are able to join the affiliate program. Leave blank to ignore this condition'),
                    'suffix' => $this->context->currency->iso_code,
                    'col' => 3
                ),
                'ETS_AM_AFF_CAT_TYPE' => array(
                    'type' => 'ets_radio_group',
                    'label' => $this->l('Applicable product categories'),
                    'col' => 3,
                    'values' => array(
                        array(
                            'title' => $this->l('All product categories '),
                            'value' => 'ALL',
                            'id' => 'ETS_AM_AFF_CAT_TYPE_ALL',
                            'data_decide' => 'ets-am-not-show',
                            'default' => true
                        ),
                        array(
                            'title' => $this->l('Specific product categories'),
                            'value' => 'SPECIFIC',
                            'id' => 'ETS_AM_AFF_CAT_TYPE_SPECIFIC',
                            'data_decide' => 'ETS_AM_AFF_CATEGORIES[]'
                        ),
                    )
                ),
                'ETS_AM_AFF_CATEGORIES' => array(
                    'label' => $this->l('Categories (does not include products in subcategories)'),
                    'type' => 'categories',
                    'fill' => true,
                    'tree' => array(
                        'id' => 'affiliate-categories-tree',
                        'selected_categories' => Tools::isSubmit('ETS_AM_AFF_CATEGORIES')&& ($ETS_AM_AFF_CATEGORIES = Tools::getValue('ETS_AM_AFF_CATEGORIES')) && $ETS_AM_AFF_CATEGORIES !== 'ALL' ? $ETS_AM_AFF_CATEGORIES : explode(',', Configuration::get('ETS_AM_AFF_CATEGORIES')),
                        'disabled_categories' => null,
                        'use_checkbox' => true,
                        'use_search' => true,
                        'root_category' => Category::getRootCategory()->id
                    ),
                    'class' => 'ETS_AM_AFF_CATEGORIES',
                    'desc' => $this->l('Select product categories in which affiliate can sell and earn commission'),
                ),
                'ETS_AM_AFF_SPECIFIC_PRODUCTS' => array(
                    'type' => 'text_search_prd',
                    'label' => $this->l('Include specific products'),
                    'desc' => $this->l('You can add specific products if they are out of the selected categories to the affiliate product list'),
                ),
                'ETS_AM_AFF_PRODUCTS_EXCLUDED' => array(
                    'type' => 'text_search_prd',
                    'label' => $this->l('Excluded products'),
                    'desc' => $this->l('Those products will not be available for affiliate program even if they are in the selected categories'),
                ),
                'ETS_AM_AFF_PRODUCTS_EXCLUDED_DISCOUNT' => array(
                    'type' => 'switch',
                    'col' => 3,
                    'label' => $this->l('Exclude products with discount'),
                    'default' => 0,
                    'values' => array(
                        array(
                            'label' => $this->l('Yes'),
                            'value' => 1,
                            'id' => 'ETS_AM_AFF_PRODUCTS_EXCLUDED_DISCOUNT_on'
                        ),
                        array(
                            'label' => $this->l('No'),
                            'value' => 0,
                            'id' => 'ETS_AM_AFF_PRODUCTS_EXCLUDED_DISCOUNT_off'
                        )
                    ),
                ),
            )
        );
    }

    public function def_affiliate_reward_caculation(){
        return array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Reward settings'),
                ),
                'input' => array(),
                'submit' => array(
                    'title' => $this->l('Save'),
                    'class' => 'btn btn-success pull-right'
                ),
                'name' => 'affiliate_reward_caculation'
            ),
            'config' => array(
                'ETS_AM_AFF_REWARD_ON_OTHER_PRODUCTS' => array(
                    'type' => 'switch',
                    'label' => $this->l('Give reward if customer buy other affiliate products after viewing a directly referred product'),
                    'desc' => $this->l('When affiliate refers a customer to a specific product, the customer also buys other affiliate products, enable this option if you want to give reward to affiliate for the sales the other products.'),
                    'default' => 1,
                    'col' => 3,
                ),
                'ETS_AM_AFF_HOW_TO_CALCULATE' => array(
                    'type' => 'ets_radio_group',
                    'col' => 3,
                    'label' => $this->l('How to calculate reward when affiliate successfully sell an affiliate product?'),
                    'desc' => $this->l('This is default setting. You can select particular setting for each product on product information page.'),
                    'default' => 1,
                    'values' => array(
                        array(
                            'id' => 'ETS_AM_AFF_HOW_TO_CALCULATE_PERCENT',
                            'value' => 'PERCENT',
                            'title' => $this->l('Percentage of product price'),
                            'default' => true,
                            'data_decide' => 'ETS_AM_AFF_DEFAULT_PERCENTAGE,ETS_AM_AFF_TAX_EXCLUDED'
                        ),
                        array(
                            'id' => 'ETS_AM_AFF_HOW_TO_CALCULATE_FIXED',
                            'value' => 'FIXED',
                            'title' => $this->l('Fixed amount each product'),
                            'data_decide' => 'ETS_AM_AFF_DEFAULT_FIXED_AMOUNT'
                        ),
                        array(
                            'id' => 'ETS_AM_AFF_HOW_TO_CALCULATE_NOREWARD',
                            'value' => 'NO_REWARD',
                            'title' => $this->l('No reward'),
                            'data_decide' => 'ets-am-not-show'
                        ),
                    )
                ),
                'ETS_AM_AFF_DEFAULT_PERCENTAGE' => array(
                    'type' => 'text',
                    'label' => $this->l('Percentage'),
                    'suffix' => '%',
                    'col' => 3,
                    'fill' => true,
                    'validate' => 'isUnsignedFloat',
                    'desc' => $this->l('This is default value. You can set particular value for each product on product information page.'),
                ),
                'ETS_AM_AFF_DEFAULT_FIXED_AMOUNT' => array(
                    'type' => 'text',
                    'label' => $this->l('Fixed amount'),
                    'suffix' => $this->context->currency->iso_code,
                    'col' => 3,
                    'fill' => true,
                    'validate' => 'isUnsignedFloat',
                    'desc' => $this->l('This is default value. You can set particular value for each product on product information page.'),
                ),
                'ETS_AM_AFF_TAX_EXCLUDED' => array(
                    'type' => 'switch',
                    'col' => 3,
                    'label' => $this->l('Exclude taxes before calculating reward'),
                    'default' => 0
                ),
                'ETS_AM_AFF_MULTIPLE' => array(
                    'type' => 'switch',
                    'label' => $this->l('Multiply reward value by the number of products'),
                    'default' => 1,
                    'col' => 3,
                    'desc' => $this->l('The reward that the affiliate receives for an order will be multiplied many times if the customer buys more than one quantity unit of a product.'),
                ),
                'ETS_AM_AFF_BY_SELLER' => array(
                    'type' => 'switch',
                    'label' => $this->l('Allow sellers to get reward when they purchase an affiliate product using their own affiliate link'),
                    'default' => 1,
                    'col' => 3,
                ),
            )
        );
    }

    public function def_affiliate_voucher(){
        return array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Reward settings'),
                ),
                'input' => array(),
                'submit' => array(
                    'title' => $this->l('Save'),
                    'class' => 'btn btn-success pull-right'
                ),
                'name' => 'affiliate_reward_caculation'
            ),
            'config' => array(
                'ETS_AM_AFF_OFFER_VOUCHER' => array(
                    'type' => 'switch',
                    'col' => 3,
                    'label' => $this->l('Offer a voucher code to customer if they come from an affiliate link?'),
                    'desc' => $this->l('Give voucher code to customer if they buy affiliate product will improve the efficiency of your affiliate program. The voucher code will be presented on a popup if customer visits product page with a link shared by an affiliate (affiliate link)'),
                    'default' => 0,
                ),
                'ETS_AM_AFF_VOUCHER_TYPE' => array(
                    'type' => 'ets_radio_group',
                    'col' => 3,
                    'label' => $this->l('Voucher type'),
                    'default' => 0,
                    'values' => array(
                        array(
                            'title' => $this->l('Fixed voucher code (general voucher code used for all customers)').EtsAffDefine::displayText($this->l('Configure discounts'),'a',null,null,$this->context->link->getAdminLink('AdminCartRules')),
                            'value' => 'FIXED',
                            'default' => true,
                            'id' => 'ETS_AM_AFF_VOUCHER_TYPE_FIXED',
                            'data_decide' => 'ETS_AM_AFF_VOUCHER_CODE,ETS_AM_AFF_WELCOME_MSG'
                        ),
                        array(
                            'title' => $this->l('Dynamic voucher code (each customer 1 voucher code)'),
                            'value' => 'DYNAMIC',
                            'id' => 'ETS_AM_AFF_VOUCHER_TYPE_DYNAMIC',
                            'data_decide' => 'ets-am-not-show'
                        ),
                    )
                ),
                'ETS_AM_AFF_VOUCHER_CODE' => array(
                    'type' => 'text',
                    'col' => 3,
                    'fill' => true,
                    'label' => $this->l('Voucher code'),
                    'desc' => $this->l('You need to generate a voucher code by creating a new cart rule that customer can use when checkout to get discounted on their order.')
                ),

                //Voucher form here
                'ETS_AM_AFF_FREE_SHIPPING' => array(
                    'label' => $this->l('Free shipping'),
                    'type' => 'switch',
                    'col' => 3,
                    'default' => 0,
                ),
                'ETS_AM_AFF_APPLY_DISCOUNT' => array(
                    'label' => $this->l('Apply a discount'),
                    'type' => 'ets_radio_group',
                    'col' => 3,
                    'values' => array(
                        array(
                            'id' => 'ETS_AM_AFF_APPLY_DISCOUNT_PERCENT',
                            'value' => 'PERCENT',
                            'title' => $this->l('Percentage (%)'),
                            'default' => true
                        ),
                        array(
                            'id' => 'ETS_AM_AFF_APPLY_DISCOUNT_AMOUNT',
                            'value' => 'AMOUNT',
                            'title' => $this->l('Amount')
                        ),
                        array(
                            'id' => 'ETS_AM_AFF_APPLY_DISCOUNT_OFF',
                            'value' => 'OFF',
                            'title' => $this->l('None')
                        ),
                    ),
                ),
                'ETS_AM_AFF_REDUCTION_PERCENT' => array(
                    'label' => $this->l('Discount percentage'),
                    'type' => 'text',
                    'fill' => true,
                    'suffix' => '%',
                    'default' => '',
                    'col' => 3,
                    'validate' => 'isPercentage',
                    'desc' => $this->l('Does not apply to the shipping costs'),
                ),
                'ETS_AM_AFF_REDUCTION_AMOUNT' => array(
                    'label' => $this->l('Amount'),
                    'type' => 'custom_amount',
                    'fill' => true,
                    'id' => 'ETS_AM_AFF_REDUCTION_AMOUNT',
                    'items' => ['ETS_AM_AFF_ID_CURRENCY','ETS_AM_AFF_REDUCTION_TAX'],
                    '_currencies' => $this->getCurrencies(),
                    'default_currency' => (int)Configuration::get('PS_CURRENCY_DEFAULT'),
                    'col' => 3,
                    'validate' => 'isUnsignedFloat',
                    'default' => '1',
                ),
                'ETS_AM_AFF_ID_CURRENCY' => array(
                    'label' => null,
                    'type' => 'custom_discount_1',
                    'fill' => true,
                    'options' => array(
                        'query' => $this->getCurrencies(),
                        'id' => 'id_currency',
                        'name' => 'name',
                    ),
                    'default' => isset($this->getCoutries()[0]['id_currency']) ? $this->getCoutries()[0]['id_currency'] : 0
                ),
                'ETS_AM_AFF_REDUCTION_TAX' => array(
                    'label' => null,
                    'type' => 'custom_discount_2',
                    'fill' => true,
                    'options' => array(
                        'query' => array(
                            array(
                                'id_option' => 0,
                                'name' => $this->l('Tax excluded')
                            ),
                            array(
                                'id_option' => 1,
                                'name' => $this->l('Tax included')
                            ),
                        ),
                        'id' => 'id_option',
                        'name' => 'name',
                    ),
                    'default' => '0',
                ),
                'ETS_AM_AFF_EXCLUDE_SPECIAL' => array(
                    'label' => $this->l('Exclude discounted products'),
                    'type' => 'switch',
                    'col' => 3,
                    'default' => 0,
                ),
                'ETS_AM_AFF_DISCOUNT_MIN_AMOUNT' => array(
                    'label' => $this->l('Minimum amount'),
                    'type' => 'custom_discount',
                    'id' => 'ETS_AM_AFF_DISCOUNT_MIN_AMOUNT',
                    'items' => ['ETS_AM_AFF_DISCOUNT_MIN_AMOUNT_CURRENCY', 'ETS_AM_AFF_DISCOUNT_MIN_AMOUNT_TAX', 'ETS_AM_AFF_DISCOUNT_MIN_AMOUNT_SHIPPING'],
                    'default' => '',
                    'validate' => 'isUnsignedFloat',
                    '_currencies' => $this->getCurrencies(),
                    'default_currency' => (int)Configuration::get('PS_CURRENCY_DEFAULT'),
                    'col' => 9,
                    'desc' => $this->l('Minimum amount in shopping cart to apply the voucher code'),
                ),
                'ETS_AM_AFF_DISCOUNT_MIN_AMOUNT_CURRENCY' => array(
                    'label' => '',
                    'type' => 'custom_discount_2',
                    'default' => '',
                ),
                'ETS_AM_AFF_DISCOUNT_MIN_AMOUNT_TAX' => array(
                    'label' => '',
                    'type' => 'custom_discount_3',
                    'default' => '',
                ),
                'ETS_AM_AFF_DISCOUNT_MIN_AMOUNT_SHIPPING' => array(
                    'label' => '',
                    'type' => 'custom_discount_4',
                    'default' => '',
                ),
                'ETS_AM_AFF_DISCOUNT_PREFIX' => array(
                    'label' => $this->l('Discount prefix'),
                    'type' => 'text',
                    'default' => 'AFF_',
                    'col' => 3
                ),
                'ETS_AM_AFF_DISCOUNT_DESC' => array(
                    'label' => $this->l('Discount name'),
                    'type' => 'text',
                    'required'=>true,
                    'rows' => 3,
                    'lang' => true,
                    'default' => 'Afiliate marketing',
                    'col' => 5,
                    'validate' => 'isImageTypeName',
                    'desc' => $this->l('Lorem: chi chap nhan: a-zA-Z0-9_ - '),
                ),
                'ETS_AM_AFF_APPLY_DISCOUNT_IN' => array(
                    'label' => $this->l('Discount availability'),
                    'type' => 'text',
                    'required' => true,
                    'suffix' => 'day(s)',
                    'col' => 3,
                    'default' => '30',
                    'validate' => 'isUnsignedInt',
                ),
                'ETS_AM_AFF_WELCOME_MSG' => array(
                    'type' => 'textarea',
                    'label' => $this->l('Welcome message'),
                    'desc' => $this->l('This message appears on a popup when customer land on product page by clicking on an affiliate link. Available tags: [highlight][discount_value][end_highlight]'),
                    'default' => $this->l('You will get [discount_value] off if you purchase this product right now. A discount code will be added automatically to your shopping cart.'),
                    'lang' => true,
                    'fill' => true,
                    'rows' => 5
                ),
                'ETS_AM_AFF_FIST_PRODUCT' => array(
                    'label' => $this->l('Only give voucher to customer for the first time they purchase an affiliate product'),
                    'type' => 'switch',
                    'col' => 3,
                    'default' => 1,
                ),
                'ETS_AM_AFF_VOUCHER_SELLER' => array(
                    'label' => $this->l('Allow seller to get voucher when they purchase an affiliate product using their own affiliate link'),
                    'type' => 'switch',
                    'col' => 3,
                    'default' => 0,
                ),
                'ETS_AM_AFF_USE_OTHER_VOUCHER' => array(
                    'label' => $this->l('Can use with other voucher in the same shopping cart'),
                    'type' => 'switch',
                    'col' => 3,
                    'default' => 0,
                ),
            )
        );
    }
    public function def_affiliate_messages(){
        return array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Messages'),
                ),
                'input' => array(),
                'submit' => array(
                    'title' => $this->l('Save'),
                    'class' => 'btn btn-success pull-right'
                ),
                'name' => 'affiliate_messages'
            ),
            'config' => array(
                'ETS_AM_AFF_PROPOSE_MSG' => array(
                    'type' => 'textarea',
                    'label' => $this->l('Propose affiliate program on product page'),
                    'desc' => $this->l('This message is displayed if customer has not joined affiliate program yet and they are eligible to join the program. Leave blank to disable this message. Available tags: [highlight][commission_value][end_highlight], [highlight][join_button][end_highlight]'),
                    'default' =>  $this->l('Join our affiliate program to sell this product and earn commission ([commission_value]) every time you get sale'). '[join_button]',
                    'rows' => '5',
                    'lang' => true
                ),
                'ETS_AM_AFF_AFFILIATE_LINK_MSG' => array(
                    'type' => 'textarea',
                    'label' => $this->l('How to share'),
                    'desc' => $this->l('This message is displayed if customer has joined affiliate program. Leave blank to disable this message. Available tags: [highlight][commission_value][end_highlight], [highlight][affiliate_link][end_highlight]'),
                    'default' => $this->l('Share this product to your friends, on your social networks, email, blog, etc. using affiliate link below to earn commission ([commission_value]) when someone buys the product'). '[affiliate_link]',
                    'rows' => '5',
                    'lang' => true
                ),
                'ETS_AM_AFF_INTRO_PROGRAM' => array(
                    'label' => $this->l('Introduction about program'),
                    'type' => 'textarea',
                    'rows' => 3,
                    'lang' => true,
                    'default' => $this->l('Join our affiliate program and help us promote our products, you will earn commission for each sold item. This program is not public to all customers, please submit an application with additional information about you. Our moderator team will review your application to decide if you are eligible for attending the program.'),
                    'desc' => $this->l('This message will display on the program\'s registration page')
                ),
                'ETS_AM_AFF_MSG_CONDITION' => array(
                    'label' => $this->l('Message on "My account" page when customer is not eligible to join affiliate program due to total order spent doesn\'t meet minimum amount required'),
                    'type' => 'textarea',
                    'rows' => 3,
                    'lang' => true,
                    'default' => $this->l('You are NOT eligible to join our affiliate program because your total past order value [total_past_order], doesn\'t meet minimum amount required [min_order_total]. Please purchase [amount_left] more to be eligible for affiliate program.'),
                    'desc' => $this->l('Available data-tags: [highlight][total_past_order][end_highlight], [highlight][min_order_total][end_highlight], [highlight][amount_left][end_highlight]. Leave this field blank to hide "Affiliate program" tab from "My account" page when minimum total order doesn\'t reach the required amount')
                )
            )
        );
    }

    public function def_affiliate_email(){
        return array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Email'),
                ),
                'input' => array(),
                'submit' => array(
                    'title' => $this->l('Save'),
                    'class' => 'btn btn-success pull-right'
                ),
                'name' => 'affiliate_email'
            ),

            'config' => array(
                
            )
        );
    }

    public function def_reward_usage(){
        return array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Reward usage'),
                ),
                'input' => array(),
                'submit' => array(
                    'title' => $this->l('Save'),
                    'class' => 'btn btn-success pull-right'
                ),
                'name' => 'reward_usage'
            ),
            'config' => array(
                'ETS_AM_AFF_ALLOW_BALANCE_TO_PAY' => array(
                    'type' => 'switch',
                    'col' => 3,
                    'label' => $this->l('Allow customer to pay for their order using reward balance'),
                    'default' => 1,
                    'desc' => $this->l('Customers can use their reward balance to pay for their order if their reward balance is greater than or equal to the order\'s total value.'),
                ),
                'ETS_AM_MIN_BALANCE_REQUIRED_FOR_ORDER' => array(
                    'type' => 'text',
                    'validate' => 'isUnsignedFloat',
                    'label' => $this->l('Minimum reward balance required to be usable to pay for order'),
                    'desc' => $this->l('Reward balance need to exceed this value in order to customer can use it to checkout. Leave blank to allow customer to use reward balance without this limit'),
                    'col' => 5,
                    'suffix' => $this->context->currency->iso_code
                ),

                'ETS_AM_MAX_BALANCE_REQUIRED_FOR_ORDER' => array(
                    'type' => 'text',
                    'validate' => 'isUnsignedFloat',
                    'label' => $this->l('Maximum reward balance can be used to pay for each order'),
                    'desc' => $this->l('The maximum amount of reward that can be used to pay for each order when customer checkout. Leave blank to allow customer to pay for their orders using any amount of reward they have in their account'),
                    'col' => 5,
                    'suffix' => $this->context->currency->iso_code
                ),
                'ETS_AM_AFF_ALLOW_CONVERT_TO_VOUCHER' => array(
                    'type' => 'switch',
                    'col' => 3,
                    'label' => $this->l('Allow customer to convert reward balance into voucher'),
                    'desc' => $this->l('They can use this voucher when checking out their order'),
                    'default' => 1,
                    'divider_before' => true
                ),
                'ETS_AM_MIN_BALANCE_REQUIRED_FOR_VOUCHER' => array(
                    'type' => 'text',
                    'validate' => 'isUnsignedFloat',
                    'label' => $this->l('Minimum reward balance required to be usable to convert into voucher'),
                    'desc' => $this->l('Reward balance need to exceed this value in order to customer can use it to convert into voucher. Leave blank to allow customer to use reward balance without this limit'),
                    'col' => 5,
                    'suffix' => $this->context->currency->iso_code
                ),
                'ETS_AM_MAX_BALANCE_REQUIRED_FOR_VOUCHER' => array(
                    'type' => 'text',
                    'validate' => 'isUnsignedFloat',
                    'label' => $this->l('Maximum reward balance that can be converted into voucher (each time)'),
                    'desc' => $this->l('The maximum amount of reward balance that customer can convert into voucher code (each time they do that). Leave blank to allow customer to convert any amount of reward balance into voucher code.'),
                    'col' => 5,
                    'suffix' => $this->context->currency->iso_code
                ),
                'ETS_AM_VOUCHER_AVAILABILITY' => array(
                    'type' => 'text',
                    'validate' => 'isUnsignedInt',
                    'label' => $this->l('Voucher availability'),
                    'col' => 5,
                    'default' => 7,
                    'suffix' => $this->l('Days')
                ),
                'ETS_AM_AFF_ALLOW_VOUCHER_IN_CART' => array(
                    'type' => 'switch',
                    'col' => 3,
                    'label' => $this->l('Display "Convert voucher" message in shopping cart'),
                    'default' => 1,
                ),
                'ETS_AM_AFF_CONVERT_VOUCHER_MSG' => array(
                    'type' => 'textarea',
                    'label' => $this->l('"Convert voucher" message'),
                    'desc' => $this->l('This message is displayed in shopping cart page to inform customer their own available reward which can be used to convert into voucher code. Available tags: [highlight][available_reward_to_convert][end_highlight], [highlight][Convert_now][end_highlight]'),
                    'default' =>  $this->l('You have [available_reward_to_convert] in your balance. It can be converted into voucher code.').' [Convert_now]',
                    'rows' => '5',
                    'lang' => true
                ),
                'ETS_AM_CAN_USE_OTHER_VOUCHER' => array(
                    'type' => 'switch',
                    'col' => 3,
                    'label' => $this->l('Can use with other voucher in the same shopping cart? '),
                    'default' => 1,
                    'values' => array(
                        array(
                            'label' => $this->l('Yes'),
                            'value' => 1,
                            'id' => 'ETS_AM_CAN_USE_OTHER_VOUCHER_on'
                        ),
                        array(
                            'label' => $this->l('No'),
                            'value' => 0,
                            'id' => 'ETS_AM_CAN_USE_OTHER_VOUCHER_off'
                        )
                    ),
                ),
                'ETS_AM_AFF_ALLOW_WITHDRAW' => array(
                    'type' => 'switch',
                    'col' => 3,
                    'label' => $this->l('Allow customer to withdraw reward'),
                    'default' => 1,
                    'divider_before' => true,
                    'desc' => $this->l('Enable this feature to allow customer to withdraw their reward balance to their bank account, Paypal account, Amazon gift card, etc. Create withdrawal methods you want in "Withdrawal methods" tab')
                ),
                'ETS_AM_MIN_BALANCE_REQUIRED_FOR_WITHDRAW' => array(
                    'type' => 'text',
                    'validate' => 'isUnsignedFloat',
                    'label' => $this->l('Minimum reward balance required to be usable to withdraw'),
                    'desc' => $this->l('Reward balance need to exceed this value in order to customer can use it to withdraw. Leave blank to allow customer to use reward balance without this limit'),
                    'col' => 5,
                    'suffix' => $this->context->currency->iso_code
                ),
                'ETS_AM_MAX_WITHDRAW' => array(
                    'type' => 'text',
                    'validate' => 'isUnsignedFloat',
                    'label' => $this->l('Maximum amount can withdraw each request'),
                    'col' => 5,
                    'suffix' => $this->context->currency->iso_code,
                    'desc' => $this->l('Maximum amount of reward balance that customer can withdraw (each time). Leave blank to allow customer to withdraw any amount of reward balance they have in their account.'),
                ),
                'ETS_AM_ALLOW_WITHDRAW_LOYALTY_REWARDS' => array(
                    'type' => 'switch',
                    'col' => 3,
                    'label' => $this->l('Allow customer to withdraw loyalty rewards'),
                    'default' => 1,
                ),
                'ETS_AM_AFF_WITHDRAW_INVOICE_REQUIRED' => array(
                    'type' => 'switch',
                    'col' => 3,
                    'label' => $this->l('Require invoice?'),
                    'default' => 0,
                    'desc' => $this->l('Ask customer to submit an invoice when they withdraw their reward balance.')
                ),
                'ETS_AM_AFF_WITHDRAW_ONE_ONLY' => array(
                    'type' => 'switch',
                    'col' => 3,
                    'label' => $this->l('Require customer to wait until the last pending withdrawal request to be processed to submit new one?'),
                    'default' => 0,
                    'desc' => $this->l('Enable this option will limit customer to be able to submit new withdrawal request if the last one has been processed')
                ),
            )
        );
    }

    public function def_payment_settings(){
        return '';
    }

    public function def_applications(){
        return array(
            'list' => array(
                'title' => 'Application',
                'action' => array('details'),
                'orderBy' => 'program',
                'orderWay' => 'DESC',
                'nb' => 'getApplications',
                'list' => 'getApplications',
                'no_link' => true,
                'bulk_actions' => array(
                    'enableSelection' => array(
                        'text' => $this->l('Enable selection'),
                        'icon' => 'icon-power-off text-success'
                    ),
                    'disableSelection' => array(
                        'text' => $this->l('Disable selection'),
                        'icon' => 'icon-power-off text-danger'
                    ),
                    'divider' => array(
                        'text' => 'divider'
                    ),
                    'delete' => array(
                        'text' => $this->l('Delete selected'),
                        'icon' => 'icon-trash',
                        'confirm' => $this->l('Do you want to delete selected items?')
                    )
                ),
                'alias' => 'app',
                'list_id' => 'ets_am_participation',
            ),
            'fields' => array(
                'id_ets_am_participation' => array(
                    'title' => $this->l('#'),
                    'align' => 'center',
                    'type' => 'text',
                    'class' => 'id',
                    'orderby' => false,
                    'search' => false,
                ),
                'id_customer' => array(
                    'title' => $this->l('Customer'),
                    'align' => 'center',
                    'type' => 'text',
                    'class' => 'customer',
                    'orderby' => false,
                    'search' => false,
                ),
                'program' => array(
                    'title' => $this->l('Program'),
                    'align' => 'center',
                    'type' => 'text',
                    'class' => 'program',
                    'orderby' => false,
                    'search' => false,
                ),
                'sss' => array(
                    'title' => $this->l('Status'),
                    'align' => 'center',
                    'type' => 'select',
                    'list' => array(
                        'Hinh', 'Ha', "my"
                    ),
                    'class' => 'status',
                    'orderby' => false,
                    'search' => false,
                    'filter_key' => 'status'
                ),
            )
        );
    }

    public function def_withdraw_list(){
        return array(
            'list' => array(
                'title' => 'Withdraw List',
                'action' => array('details'),
                'orderBy' => 'id',
                'orderWay' => 'DESC',
                'nb' => 'getWithdrawals',
                'list' => 'getWithdrawals',
                'no_link' => true,
                'alias' => 'withdraw-list',
                'list_id' => 'ets_am_withdrawal',
                'withdrawal' => true
            ),
            'fields' => array(
                'id_ets_am_withdrawal' => array(
                    'title' => $this->l('ID'),
                    'align' => 'center',
                    'type' => 'text',
                    'class' => 'id',
                    'orderby' => true,
                    'search' => false,
                ),
                'customer' => array(
                    'title' => $this->l('Customer'),
                    'align' => 'center',
                    'type' => 'text',
                    'class' => 'customer',
                    'orderby' => false,
                    'search' => false,
                ),
                'title' => array(
                    'title' => $this->l('Payment method'),
                    'align' => 'center',
                    'type' => 'text',
                    'class' => 'payment_method',
                    'orderby' => false,
                    'search' => false,
                ),
                'display_amount_backend' => array(
                    'title' => $this->l('Amount'),
                    'align' => 'center',
                    'type' => 'text',
                    'class' => 'amount',
                    'orderby' => false,
                    'search' => false,
                ),
                'status' => array(
                    'title' => $this->l('Status'),
                    'align' => 'center',
                    'type' => 'text',
                    'class' => 'status',
                    'orderby' => false,
                    'search' => false,
                ),
                'note' => array(
                    'title' => $this->l('Note'),
                    'align' => 'center',
                    'type' => 'text',
                    'class' => 'status',
                    'orderby' => false,
                    'search' => false,
                ),
                'actions' => array(
                    'title' => $this->l('Action'),
                    'align' => 'center',
                    'type' => 'text',
                    'orderby' => false,
                    'search' => false,
                )
            )
        );
    }

    public function def_reward_users(){
        
        return array(
            'list' => array(
                'title' => null,
                'orderBy' => 'id_customer',
                'orderWay' => 'DESC',
                'actions' => array(),
                'list' => 'getRewardUsers',
                'nb' => 'getRewardUsers',
                'no_link' => true,
                'bulk_actions' => array(),
                'list_id' => 'reward_users',
                'primary_key' => 'id_reward_users',
                'alias' => 'r'
            ),
            'fields' => array(
                'id_customer' => array(
                    'title' => $this->l('ID'),
                    'align' => 'center',
                    'type' => 'text',
                    'class' => 'id',
                    'orderby' => true,
                    'search' => true,
                    'width' => 30,
                ),
                'username' => array(
                    'title' => $this->l('Name'),
                    'align' => 'left',
                    'type' => 'text',
                    'orderby' => true,
                    'search' => true,
                    'float' => true,
                    'width' => 100
                ),
                'reward_balance' => array(
                    'title' => $this->l('Balance'),
                    'align' => 'center',
                    'type' => 'text',
                    'orderby' => true,
                    'search' => true
                ),
                'loy_rewards' => array(
                    'title' => $this->l('Loyalty'),
                    'align' => 'center',
                    'type' => 'text',
                    'orderby' => true,
                    'search' => true
                ),
                'ref_rewards' => array(
                    'title' => $this->l('Referral'),
                    'align' => 'center',
                    'class' => 'fixed-width-xs',
                    'type' => 'text',
                    'orderby' => true,
                ),
                'aff_rewards' => array(
                    'title' => $this->l('Affiliate'),
                    'align' => 'center',
                    'type' => 'text',
                ),
                'mnu_rewards' => array(
                    'title' => $this->l('Other reward'),
                    'align' => 'center max-width-86',
                    'type' => 'int',
                ),
                'total_withdraws' => array(
                    'title' => $this->l('Withdrawals'),
                    'align' => 'center',
                    'type' => 'text',
                    'orderby' => true,
                ),
                'sponsors' => array(
                    'title' => $this->l('Sponsored'),
                    'align' => 'left',
                    'type' => 'text',
                    'float' => true,
                    'search' => false,
                    'orderby' => false,
                ),
                'user_status' => array(
                    'title' => $this->l('Status'),
                    'align' => 'center',
                    'type' => 'select',
                    'float' => true,
                    'value' => 1,
                    'list' => array(
                        '1' => $this->l('Active'),
                        '-1' => $this->l('Suspended'),
                    ),
                    'filter_key' => 'user_status',
                    'orderby' => true,
                ),
                'has_reward' => array(
                    'title' => $this->l('Has reward'),
                    'align' => 'center',
                    'type' => 'select',
                    'orderby' => true,
                    'float' => true,
                    'value' => 1,
                    'list' => array(
                        '1' => $this->l('Yes'),
                        '0' => $this->l('No'),
                    ),
                    'filter_key' => 'has_reward',
                ),
            ),
        );
    }

    public function def_import_export(){
        return array();
    }

    public function def_cronjob_config(){
        return array();
    }

    public function def_cronjob_history(){
        return array();
    }

    public function def_general_settings(){
        $order_states = OrderState::getOrderStates($this->context->language->id);
        $input_states_wating = array();
        $input_states_validated = array();
        $input_states_canceled = array();

        foreach ($order_states as $state) {
                $order_state = array(
                    'title' => $state['name'],
                    'value' => $state['id_order_state'],
                    'template' => $state['template'],// 'shipped'
                );
                if ($state['template'] == 'payment' || $state['template'] == 'shipped' || $state['template']=='') {
                    $order_state['default'] = true;
                }
                $order_state['id'] = 'order_state_validated_' . $state['id_order_state'];
                array_push($input_states_validated, $order_state);
                $order_state['default'] = false;
    
                if ($state['template'] == 'order_canceled' || $state['template'] == 'refund' || $state['template'] == 'payment_error') {
                    $order_state['default'] = true;
                }
                $order_state['id'] = 'order_state_canceled_' . $state['id_order_state'];
                array_push($input_states_canceled, $order_state);
                $order_state['default'] = false;
    
                if ($state['template'] == 'cheque' || $state['template'] == 'bankwire' || $state['template'] == 'cashondelivery' || $state['template'] == 'outofstock'
                     || $state['template'] == 'preparation') {
                    $order_state['default'] = true;
                }
                $order_state['id'] = 'order_state_wating_' . $state['id_order_state'];
                array_push($input_states_wating, $order_state);
                $order_state['default'] = false;
            
        }
        return array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('General settings'),
                ),
                'input' => array(),
                'submit' => array(
                    'title' => $this->l('Save'),
                    'class' => 'btn btn-success pull-right'
                ),
                'name' => 'reward_settings'
            ),
            'config' => array(
                'ETS_AM_REWARD_DISPLAY' => array(
                    'type' => 'ets_radio_group',
                    'col' => 3,
                    'label' => $this->l('How to display reward on front office?'),
                    'values' => array(
                        array(
                            'id' => 'ETS_AM_REWARD_DISPLAY_CURRENCY',
                            'title' => $this->l('Currency (that customer selected)'),
                            'value' => 'money',
                            'default' => true,
                        ),
                        array(
                            'id' => 'ETS_AM_REWARD_DISPLAY_POINT',
                            'title' => $this->l('Custom unit') . ' (' . (($unit = Tools::getValue('ETS_AM_REWARD_UNIT_LABEL_' . $this->context->language->id, false) ? : Configuration::get('ETS_AM_REWARD_UNIT_LABEL', $this->context->language->id)) && Validate::isUnsignedFloat($unit) ? $unit : $this->l('Point')) . ')',
                            'value' => 'point',
                            'default' => true,
                        ),
                    ),
                    'desc' => $this->l('You can choose to display rewards to customer on the front office in "customer\'s selected currency" unit or ') . '"' . ($unit ? $unit : $this->l('Point')) . '"',
                ),

                'ETS_AM_REWARD_UNIT_LABEL' => array(
                    'type' => 'text',
                    'label' => $this->l('Reward unit label'),
                    'col' => 3,
                    'default' => $this->l('Point'),
                    'lang' => true,
                    'fill' => true,
                    'desc' => $this->l('You can choose to display rewards to customer on the front office in "Currency" unit or ') . '"' . ($unit ? $unit : $this->l('Point')) . '"',
                ),
                'ETS_AM_CONVERSION' => array(
                    'type' => 'text',
                    'label' => $this->l('Conversion rate: 1') . $this->context->currency->iso_code . ' = ',
                    'col' => 3,
                    'default' => 10,
                    'fill' => true,
                    'validate' => 'isUnsignedFloat',
                    'suffix' => ($unit ? $unit : $this->l('Point')),
                    'desc' => sprintf($this->l('Convert money value to custom unit. This rate is used for default currency (%s), other currencies (if have) will be converted to points based on its exchange rate with default currency (%s)'), $this->context->currency->iso_code, $this->context->currency->iso_code),
                ),
                'ETS_AM_REWARD_DISPLAY_BO' => array(
                    'type' => 'ets_radio_group',
                    'col' => 3,
                    'label' => $this->l('How to display reward on back office?'),
                    'values' => array(
                        array(
                            'id' => 'ETS_AM_REWARD_DISPLAY_CURRENCY_BO',
                            'title' => $this->l('Default currency') . ' (' . $this->context->currency->iso_code . ')',
                            'value' => 'money',
                            'default' => true,
                        ),
                        array(
                            'id' => 'ETS_AM_REWARD_DISPLAY_POINT_BO',
                            'title' => $this->l('Custom unit') . ' (' . (($unit = Tools::getValue('ETS_AM_REWARD_UNIT_LABEL_' . $this->context->language->id, false) ? : Configuration::get('ETS_AM_REWARD_UNIT_LABEL', $this->context->language->id)) && Validate::isUnsignedFloat($unit) ? $unit : $this->l('Point')) . ')',
                            'value' => 'point',
                            'default' => true,
                        ),
                    ),
                    'desc' => sprintf($this->l('You can choose to display rewards in back office in "default currency (%s)" unit or '), $this->context->currency->iso_code) . '"' . ($unit ? $unit : $this->l('Point')) . '"',
                ),
                'ETS_AM_WAITING_STATUS' => array(
                    'type' => 'ets_checkbox_group',
                    'col' => 3,
                    'label' => $this->l('Reward is created with "Pending" status if order status is'),
                    'values' => $input_states_wating,
                    'required' => true
                ),
                'ETS_AM_VALIDATED_STATUS' => array(
                    'type' => 'ets_checkbox_group',
                    'col' => 3,
                    'label' => $this->l('Reward is validated and changed to "Approved" status if order status is'),
                    'values' => $input_states_validated,
                    'required' => true,
                    'desc' => $this->l('These statuses are also used to consider an order as PAID when calculating total spent for a customer')
                ),
                'ETS_AM_VALIDATED_DAYS' => array(
                    'type' => 'text',
                    'label' => $this->l('Only validate reward if order has been changed to statuses above for '),
                    'col' => 3,
                    'default' => '',
                    'validate' => 'isUnsignedInt',
                    'desc' => $this->l('Reward status will remain "Pending" until the required days exceeded. Leave blank to validate reward immediately when order status is changed to one of the statuses above. If you set any value for this field, please also set up cronjob so that when this condition is satisfied, cronjob will update status of the reward.'),
                    'suffix' => $this->l('day(s)')
                ),
                'ETS_AM_CANCELED_STATUS' => array(
                    'type' => 'ets_checkbox_group',
                    'col' => 3,
                    'label' => $this->l('Cancel reward if order status is '),
                    'values' => $input_states_canceled,
                    'required' => true
                ),
                'ETS_AM_TERM_AND_COND_REQUIRED' => array( 
                    'type' => 'switch',
                    'col' => 3,
                    'label' => $this->l('Require customer to accept Terms & conditions when submit application to join marketing program?'),
                    'default' => 0,
                    'values' => array(
                        array(
                            'label' => $this->l('Yes'),
                            'value' => 1,
                            'id' => 'ETS_AM_TERM_AND_COND_REQUIRED_ON'
                        ),
                        array(
                            'label' => $this->l('No'),
                            'value' => 0,
                            'id' => 'ETS_AM_TERM_AND_COND_REQUIRED_OFF'
                        )
                    ),
                ),
                'ETS_AM_TERM_AND_COND_URL' => array(
                    'type' => 'text',
                    'label' => $this->l('Terms & conditions page URL'),
                    'col' => 3,
                    'lang' => true,
                    'default' => '#',
                ),
                'ETS_AM_REF_DISPLAY_EMAIL_SPONSOR' => array(
                    'type' => 'switch',
                    'col' => 3,
                    'label' => $this->l('Display customer email on front office'),
                    'desc' => $this->l('Enable this to allow sponsor to see their friend\'s email addresses'),
                    'default' => 0,
                    'values' => array(
                        array(
                            'label' => $this->l('Yes'),
                            'value' => 1,
                            'id' => 'ETS_AM_REF_DISPLAY_EMAIL_SPONSOR_ON'
                        ),
                        array(
                            'label' => $this->l('No'),
                            'value' => 0,
                            'id' => 'ETS_AM_REF_DISPLAY_EMAIL_SPONSOR_OFF'
                        )
                    ),
                ),
                'ETS_AM_REF_DISPLAY_NAME_SPONSOR' => array(
                    'type' => 'switch',
                    'col' => 3,
                    'label' => $this->l('Display customer name on front office'),
                    'desc' => $this->l('Enable this to allow sponsor to see their friend\'s name'),
                    'default' => 1,
                    'values' => array(
                        array(
                            'label' => $this->l('Yes'),
                            'value' => 1,
                            'id' => 'ETS_AM_REF_DISPLAY_NAME_SPONSOR_ON'
                        ),
                        array(
                            'label' => $this->l('No'),
                            'value' => 0,
                            'id' => 'ETS_AM_REF_DISPLAY_NAME_SPONSOR_OFF'
                        )
                    ),
                ),
                'ETS_AM_DISPLAY_ID_ORDER' => array(
                    'type' => 'switch',
                    'col' => 3,
                    'label' => $this->l('Display order ID on front office'),
                    'desc' => $this->l('Enable this to allow sponsor to see order ID'),
                    'default' => 0,
                    'values' => array(
                        array(
                            'label' => $this->l('Yes'),
                            'value' => 1,
                            'id' => 'ETS_AM_DISPLAY_ID_ORDER_on'
                        ),
                        array(
                            'label' => $this->l('No'),
                            'value' => 0,
                            'id' => 'ETS_AM_DISPLAY_ID_ORDER_off'
                        )
                    ),
                ),
                
            )
        );
    }

    public function def_general_email(){
        return array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Email'),
                ),
                'input' => array(),
                'submit' => array(
                    'title' => $this->l('Save'),
                    'class' => 'btn btn-default pull-right'
                ),
                'name' => 'general_email'

            ),
            'config' => array(
                'ETS_AM_EMAILS_CONFIRM' => array(
                    'type' => 'text',
                    'label' => $this->l('Email addresses to receive notifications'),
                    'desc' => $this->l('Notifcation messages ("New reward created","Reward validated","New withdrawal request", etc.) will be sent to these emails. Enter emails separated by a comma (",") if you want to send notification messages to more than 1 email'),
                    'default' => Configuration::get('PS_SHOP_EMAIL'),
                ),
                'ETS_AM_ENABLED_EMAIL_CONFIRM_REG' => array(
                    'type' => 'switch',
                    'col' => 3,
                    'label' => $this->l('Send email to admin when customer submits an application to join promotion programs'),
                    'default' => '1',
                    'divider_before' => true
                ),
                'ETS_AM_ENABLED_EMAIL_RES_REG' => array(
                    'type' => 'switch',
                    'col' => 3,
                    'label' => $this->l('Send email to customer when their application is approved'),
                    'default' => '1'
                ),
                'ETS_AM_ENABLED_EMAIL_DECLINE_APP' => array(
                    'type' => 'switch',
                    'col' => 3,
                    'label' => $this->l('Send email to customer when their application is declined'),
                    'default' => '1'
                ),

                'ETS_AM_ENABLED_EMAIL_ADMIN_RC' => array(
                    'type' => 'switch',
                    'col' => 3,
                    'label' => $this->l('Send email to admin when a reward is created for customer'),
                    'default' => '1',
                    'divider_before' => true
                ),
                'ETS_AM_ENABLED_EMAIL_ADMIN_RVOC' => array(
                    'type' => 'switch',
                    'col' => 3,
                    'label' => $this->l('Send email to admin when a reward is validated or canceled'),
                    'default' => '1',
                ),
                'ETS_AM_ENABLED_EMAIL_CUSTOMER_RC' => array(
                    'type' => 'switch',
                    'col' => 3,
                    'label' => $this->l('Send email to customer when reward is created for them'),
                    'default' => '1',
                    'divider_before' => true
                ),
                'ETS_AM_ENABLED_EMAIL_CUSTOMER_RVOC' => array(
                    'type' => 'switch',
                    'col' => 3,
                    'label' => $this->l('Send email to customer when reward is validated or canceled'),
                    'default' => '1',
                ),
                'ETS_AM_LOYALTY_EMAIL_GOING_EXPIRED' => array(
                    'type' => 'switch',
                    'col' => 3,
                    'label' => $this->l('Send email to customer when loyalty reward is going to be expired '),
                    'default' => 1
                ),
                'ETS_AM_LOYALTY_EMAIL_EXPIRED_DAY' => array(
                    'type' => 'text',
                    'label' => $this->l('Before expiration'),
                    'col' => 3,
                    'default' => 1,
                    'required' => true,
                    'validate' => 'isUnsignedInt',
                    'desc' => $this->l('You need to enable cronjob to activate this feature. See more detail in "Cronjob" tab '),
                    'suffix' => $this->l('day(s)')
                ),
                'ETS_AM_LOYALTY_EMAIL_EXPIRED' => array(
                    'type' => 'switch',
                    'col' => 3,
                    'label' => $this->l('Send email to customer when reward is expired '),
                    'default' => 1,
                    'desc' => $this->l('You need to enable cronjob to activate this feature')
                ),
                'ETS_AM_LOYALTY_EMAIL_EXPIRED' => array(
                    'type' => 'switch',
                    'col' => 3,
                    'label' => $this->l('Send email to customer when reward is expired '),
                    'default' => 1,
                    'desc' => $this->l('You need to enable cronjob to activate this feature')
                ),
                'ETS_EMAIL_ADMIN_ADD_REWARD' => array(
                    'type' => 'switch',
                    'col' => 3,
                    'label' => $this->l('Send email to customers when admin add a reward for them'),
                    'default' => 1,
                ),
                'ETS_EMAIL_ADMIN_DEDUCT_REWARD' => array(
                    'type' => 'switch',
                    'col' => 3,
                    'label' => $this->l('Send email to customer when admin deduct a reward of them'),
                    'default' => 1,
                ),
            )
        );
    }
    public function getLoyTimes(){
        return array(
            array(
                'id' => 'loyalty_time_all',
                'title' => $this->l('All the time'),
                'value' => 'ALL',
                'is_all' => true,
                'default' => true,
                'data_decide' => 'ets-am-not-show'
            ),
            array(
                'id' => 'loyalty_time_visistor',
                'title' => $this->l('Specific time range'),
                'value' => 'specific',
                'data_decide' => 'ETS_AM_LOYALTY_TIME_FROM,ETS_AM_LOYALTY_TIME_TO'
            )
        );
    }

    public function getLoyaltyGroup($default=false){
        $customer_groups = Group::getGroups($this->context->language->id);
        $loyalty_customer_groups = array(
            array(
                'id' => 'loyalty_group_all',
                'title' => $this->l('All'),
                'value' => 'ALL',
                'is_all' => true,
                'default' => !$default ? true :false,
            ),
        );
         foreach ($customer_groups as $group) {
            array_push($loyalty_customer_groups, array(
                'id' => 'ETS_AM_LOYALTY_GROUPS_' . $group['id_group'],
                'title' => $group['name'],
                'value' => $group['id_group'],
                'default' => isset($default) && $default && in_array($group['id_group'],$default) ? true:false,
            ));
        }

        return $loyalty_customer_groups;
    }

    public function getRefGroup(){
        $customer_groups = Group::getGroups($this->context->language->id);
        $groups = array(
            array(
                'id' => 'loyalty_group_all',
                'title' => $this->l('All'),
                'value' => 'ALL',
                'is_all' => true,
                'default' => true
            ),
        );
         foreach ($customer_groups as $group) {
            if ((int)$group['id_group'] > 2) {
                array_push($groups, array(
                    'id' => 'ETS_AM_LOYALTY_GROUPS_' . $group['id_group'],
                    'title' => $group['name'],
                    'value' => $group['id_group']
                ));
            }
        }

        return $groups;
    }

    public function getAfffGroup(){
        $customer_groups = Group::getGroups($this->context->language->id);
        $groups = array(
            array(
                'id' => 'loyalty_group_all',
                'title' => $this->l('All'),
                'value' => 'ALL',
                'is_all' => true,
                'default' => true
            ),
        );
         foreach ($customer_groups as $group) {
            if ((int)$group['id_group'] > 2) {
                array_push($groups, array(
                    'id' => 'ETS_AM_LOYALTY_GROUPS_' . $group['id_group'],
                    'title' => $group['name'],
                    'value' => $group['id_group']
                ));
            }
        }

        return $groups;
    }

    public function l($string)
    {
        return Translate::getModuleTranslation(_ETS_AM_MODULE_, $string, pathinfo(__FILE__, PATHINFO_FILENAME));
    }

    public function getCurrencies(){
        return ($res = Currency::getCurrenciesByIdShop($this->context->shop->id)) ? $res : array();
    }
    public function getCoutries(){
        return ($res = Country::getCountriesByIdShop($this->context->shop->id, $this->context->language->id)) ? $res : array();
    }

    public function display($template){
        if (!$this->module)
            return;
        return $this->module->display($this->module->getLocalPath(), $template);
    }
    public static function displayText($content = null, $tag=null, $class = null, $id = null, $href = null, $blank = false, $src = null, $name = null, $value = null, $type = null, $data_id_product = null, $rel = null, $attr_datas = null)
    {
        $text ='';
        if($tag)
        {
            $text .= '<'.$tag.($class ? ' class="'.$class.'"':'').($id ? ' id="'.$id.'"':'');
            if($href)
                $text .=' href="'.$href.'"';
            if($blank && $tag ='a')
                $text .=' target="_blank"';
            if($src)
                $text .=' src ="'.$src.'"';
            if($name)
                $text .=' name="'.$name.'"';
            if($value)
                $text .=' value ="'.$value.'"';
            if($type)
                $text .= ' type="'.$type.'"';
            if($data_id_product)
                $text .=' data-id_product="'.(int)$data_id_product.'"';
            if($rel)
                $text .=' rel="'.$rel.'"';
            if($attr_datas)
            {
                foreach($attr_datas as $data)
                {
                    $text .=' '.$data['name'].'='.'"'.$data['value'].'"';
                }
            }
            if($tag=='img' || $tag=='br' || $tag=='input')
                $text .='/>';
            else
                $text .='>';
            if ($tag && $tag != 'img' && $tag != 'input' && $tag != 'br' && !is_null($content))
                $text .= $content;
            if ($tag && $tag != 'img' && $tag != 'path' && $tag != 'input' && $tag != 'br')
                $text .= '<'.'/' . $tag . '>';
            return $text;
        }
        return '';
    }
}