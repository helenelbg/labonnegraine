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
    exit;
}
if (!defined('_ETS_AM_MODULE_')) {
    define('_ETS_AM_MODULE_', 'ets_affiliatemarketing');
}
define('EAM_AFF_CUSTOMER_COOKIE', 'eam_aff_customer_cookie');
define('EAM_AFF_PRODUCT_COOKIE', 'eam_aff_product_cookie');
define('EAM_AFF_VISITED_PRODUCTS', 'eam_aff_visited_products');
define('EAM_CUSTOMER_VIEW_PRODUCT', 'eam_customer_view_product');
define('EAM_REFS', 'eam_refs');
define('EAM_AM_LOYALTY_REWARD', 'loy');
define('EAM_AM_AFFILIATE_REWARD', 'aff');
define('EAM_AM_REF_REWARD', 'ref');
define('URL_REGISTER_REWARD_PROGRAM', 'affiliate-marketing/register-programs');
define('URL_REF_PROGRAM', 'affiliate-marketing/referral-program');
define('URL_CUSTOMER_REWARD', 'affiliate-marketing/customer-reward');
define('URL_LOY_PROGRAM', 'affiliate-marketing/loyalty-program');
define('URL_AFF_PROGRAM', 'affiliate-marketing/affiliate');
define('URL_EAM_HISTORY', 'affiliate-marketing/reward-histories');
define('URL_EAM_WITHDRAW', 'affiliate-marketing/withdraw');
define('URL_EAM_VOUCHER', 'affiliate-marketing/voucher');
define('URL_EAM_AFF_PRODUCT', 'affiliate-marketing/affiliate-products');
define('URL_EAM_PRODUCT_VIEW', 'affiliate-marketing/product-view');
define('URL_EAM_MY_SALE', 'affiliate-marketing/my-sales');
define('ETS_AM_PROMO_PREFIX', 'EAM');
if (!defined('EAM_PATH_IMAGE_BANER')) {
    define('EAM_PATH_IMAGE_BANER', _PS_IMG_DIR_ . 'ets_affiliatemarketing/');
}
if (!defined('`_PS_ETS_EAM_IMG_`')) {
    define('_PS_ETS_EAM_IMG_', __PS_BASE_URI__ . 'img/ets_affiliatemarketing/');
}
if (!defined('_PS_ETS_EAM_LOG_DIR_')) {
    if (file_exists(_PS_ROOT_DIR_ . '/var/logs')) {
        define('_PS_ETS_EAM_LOG_DIR_', _PS_ROOT_DIR_ . '/var/logs/');
    } else
        define('_PS_ETS_EAM_LOG_DIR_', _PS_ROOT_DIR_ . '/log/');
}
define('EAM_INVOICE_PATH', 'invoices');
define('LOG_IP_CONFIGURATION_KEY', 'ETS_AM_IP_LOG');
require_once(dirname(__FILE__) . '/classes/EtsAmAdmin.php');
require_once(dirname(__FILE__) . '/classes/Ets_AM.php');
require_once(dirname(__FILE__) . '/classes/Ets_Loyalty.php');
require_once(dirname(__FILE__) . '/classes/Ets_Participation.php');
require_once(dirname(__FILE__) . '/classes/Ets_Affiliate.php');
require_once(dirname(__FILE__) . '/classes/Ets_Sponsor.php');
require_once(dirname(__FILE__) . '/classes/Ets_Reward_Usage.php');
require_once(dirname(__FILE__) . '/classes/Ets_Invitation.php');
require_once(dirname(__FILE__) . '/classes/Ets_Banner.php');
require_once(dirname(__FILE__) . '/classes/Ets_Withdraw.php');
require_once(dirname(__FILE__) . '/classes/Ets_Withdraw_Field.php');
require_once(dirname(__FILE__) . '/classes/Ets_PaymentMethod.php');
require_once(dirname(__FILE__) . '/classes/Ets_Voucher.php');
require_once(dirname(__FILE__) . '/controllers/front/all.php');
require_once(dirname(__FILE__) . '/classes/Ets_User.php');
require_once(dirname(__FILE__) . '/classes/Ets_Reward_Product.php');
require_once(dirname(__FILE__) . '/classes/Ets_Access_Key.php');
require_once(dirname(__FILE__) . '/classes/Ets_Product_View.php');
require_once(dirname(__FILE__) . '/classes/Ets_ImportExport.php');
require_once(dirname(__FILE__) . '/defines.php');
require_once(dirname(__FILE__) . '/classes/Ets_aff_email.php');
require_once(dirname(__FILE__) . '/classes/Ets_aff_qr_code.php');
class Ets_affiliatemarketing extends PaymentModule
{
    const SERVICE_LOCALE_REPOSITORY = 'prestashop.core.localization.locale.repository';
    public $_html;
    public $dashboard = array();
    public $fields_list = array();
    public $applications = array();
    protected static $trans = array();
    public $_errors = array();
    public $is17;
    public $currencies = array();
    public $countries = array();
    public $_id_product = null;
    public $list_id = null;
    public $toolbar_btn = array();
    protected $_filterHaving = "";
    protected $_filter = "";
    public function __construct()
    {
        $this->name = 'ets_affiliatemarketing';
        $this->tab = 'advertising_marketing';
        $this->version = '1.7.0';
        $this->author = 'PrestaHero';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
        $this->module_key = '54356e288958a33ac3a434d4f4d0d1eb';
        $this->bootstrap = true;
        parent::__construct();
        $this->displayName = $this->l('Loyalty, referral and affiliate program (reward points)');
        $this->description = $this->l('Allows customers to earn rewards (points or cash) when they buy, sell or refer new customers to your website. Includes 3 marketing programs: Loyalty, Referral and Affiliate programs to boost your sales and customers.');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
        if (version_compare(_PS_VERSION_, '1.7', '>=')) {
            $this->is17 = true;
        }
    }
    public function getTranslates()
    {
        if(!self::$trans)
        {
            self::$trans = array(
                'point' => $this->l('point'),
                'points' => $this->l('points'),
                'Edit' => $this->l('Edit'),
                'View' => $this->l('View'),
                'Expired' => $this->l('Expired'),
                'Delete' => $this->l('Delete'),
                'Approve' => $this->l('Approve'),
                'Active' => $this->l('Active'),
                'Approved' => $this->l('Approved'),
                'Pending' => $this->l('Pending'),
                'Decline' => $this->l('Decline'),
                'Declined' => $this->l('Declined'),
                'Suspended' => $this->l('Suspended'),
                'Refuse' => $this->l('Refuse'),
                'Cancel' => $this->l('Cancel'),
                'Canceled' => $this->l('Canceled'),
                'Stop' => $this->l('Stop'),
                'Validated' => $this->l('Validated'),
                'Validate' => $this->l('Validate'),
                'join_affiliate_btn' => $this->l('Join Affiliate program'),
                'copy-to-clipboard' => $this->l('Click to copy to clipboard'),
                'copy-to-clipboard-success' => $this->l('Copied to clipboard'),
                'new-reward' => $this->l('New reward was created.'),
                'customer' => $this->l('Customer'),
                'amount' => $this->l('Amount'),
                'type' => $this->l('Type'),
                'expiry' => $this->l('Expired in'),
                'reward_amount' => $this->l('Reward amount'),
                'reward_validated' => $this->l('Your reward was approved'),
                'reward_canceled' => $this->l('Your reward was canceled'),
                'reward_going_to_be_expired' => $this->l('Your reward is going to be expired!'),
                'reward_expired' => $this->l('Reward expired'),
                'turnover' => $this->l('Turnover'),
                'reward' => $this->l('Reward'),
                'net_profit' => $this->l('Net profit'),
                'orders' => $this->l('Orders'),
                'customers' => $this->l('Customers'),
                'conversion_rate' => $this->l('Conversion rate'),
                'error_fee_payment' => $this->l('Fee of payment method must be a decimal.'),
                'error_payment_method_string' => $this->l('Title of payment method must be a string.'),
                'error_payment_field_string' => $this->l('Title of payment field must be a string.'),
                'confirm_msg' => $this->l('Are you sure to do this action?'),
                'times' => $this->l('Time'),
                'dates' => $this->l('Date'),
                'level' => $this->l('Level'),
                'referral_program' => $this->l('Referral program'),
                'affiliate_program' => $this->l('Affiliate program'),
                'loyalty_program' => $this->l('Loyalty program'),
                'yes' => $this->l('Yes'),
                'no' => $this->l('No'),
                'user_deleted' => $this->l('User deleted'),
                'view_details' => $this->l('View details'),
                'views' => $this->l('Views'),
                'Deleted' => $this->l('Deleted'),
                'Decline_return' => $this->l('Decline - Return reward'),
                'Decline_deduct' => $this->l('Decline - Deduct reward'),
                'total_order' => $this->l('Total orders'),
                'total_view' => $this->l('Total views'),
                'earning_reward' => $this->l('Earning rewards'),
                'loyalty' => $this->l('Loyalty'),
                'affiliate' => $this->l('Affiliate'),
                'referral' => $this->l('Referral'),
                'estimated' => $this->l('estimated'),
                'view_user' => $this->l('View user'),
                'reward_unit_label_required' => $this->l('Reward unit label is required'),
                'coversion_rate_required' => $this->l('Conversion rate is required'),
                'email_receive_required' => $this->l('Email to receive is required'),
                'specific_time_required' => $this->l('Specific time is required'),
                'categories_required' => $this->l('Categories are required'),
                'discount_percent_required' => $this->l('Discount percentage is required'),
                'discount_availability_required' => $this->l('Discount availability is required'),
                'amount_required' => $this->l('Amount is required'),
                'percentage_required' => $this->l('Percentage is required'),
                'amount_fixed_required' => $this->l('Fixed amount is required'),
                'second_ago' => $this->l('second(s) ago'),
                'minute_ago' => $this->l('minute(s) ago'),
                'hour_ago' => $this->l('hour(s) ago'),
                'day_ago' => $this->l('day(s) ago'),
                'month_ago' => $this->l('month(s) ago'),
                'year_ago' => $this->l('year(s) ago'),
                'less_than_1s_ago' => $this->l('less than 1 second ago'),
                'reward_usage' => $this->l('Reward usage'),
                'suspend' => $this->l('Suspend'),
                'reward_used_label' => $this->l('Used reward'),
                'reward_earned_label' => $this->l('Earned reward'),
                'reward_created_for_you' => $this->l('A new reward created for you'),
                'a_reward_validated' => $this->l('A reward was approved'),
                'a_reward_canceled' => $this->l('A reward was canceled'),
                'a_reward_created' => $this->l('A new reward was created'),
                'voucher_sell_quantity_require' => $this->l('Quantity is required'),
                'voucher_sell_quantity_vaild' => $this->l('Quantity is not valid'),
                'subject_approve_width' => $this->l('Your withdrawal request was approved!'),
                'subject_admin_approve_width' => $this->l('You have approved a withdrawal request'),
                'subject_decline_width' => $this->l('Your withdrawal request was declined!'),
                'subject_admin_decline_width' => $this->l('You have declined a withdrawal request'),
                'Deduct' => $this->l('Deduct'),
                'Refund' => $this->l('Refund'),
                'deduct_reward' => $this->l('Deducted reward'),
                'return_reward' => $this->l('Returned reward'),
                'referral_and_affiliate_program' => $this->l('Referral and Affiliate program'),
                'your_application_was_declined' => $this->l('Your application was declined'),
                'your_application_was_approved' => $this->l('Your application was approved'),
                'your_reward_is_going_be_expired' => $this->l('Your reward is going to be expired'),
                'a_new_reward_was_created' => $this->l('A new reward was created'),
                'your_reward_was_expired' => $this->l('Your reward was expired'),
                'note_reward_ref_user' => $this->l('Refer new user (#%s)'),
                'note_reward_ref_order' => $this->l('Referral commission (Order: #%s, Level: %s)'),
                'categories_valid' => $this->l('Categories are not valid'),
            );
        }
        return self::$trans;
    }
    /**
     * @param $params
     * @return string
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function hookDisplaySnwProductList($params)
    {
        if (isset($params['ids']) && ($productIds = $params['ids'])) {
            $IDs = explode(',', $productIds);
            $products = array();
            foreach ($IDs as $id) {
                if($id && !isset($products[$id]))
                {
                    $product = new Product($id, false, (int)Configuration::get('PS_LANG_DEFAULT'));
                    if ($product && Validate::isLoadedObject($product)) {
                        $imageType = self::getFormattedName('small');
                        if(($image = Product::getCover($id)))
                        {
                            $imagePath = $this->context->link->getImageLink($product->link_rewrite, $image['id_image'],$imageType);
                            $product->image = $imagePath;
                        }
                        else
                        {
                            $imagePath = $this->context->link->getImageLink($product->link_rewrite, $this->context->language->iso_code.'-default',$imageType);
                            $product->image = $imagePath;
                        }
                        $product_url = $this->context->link->getProductLink((int)$id);

                        $product->product_url = $product_url;
                        $product->id_product = (int)$id;
                        $products[$id] = $product;
                    }
                }

            }
            $this->smarty->assign(array(
                'products' => $products,
                'default_lang' => (int)Configuration::get('PS_LANG_DEFAULT')
            ));
            return $this->display(__FILE__, 'block_prd_items.tpl');
        }
    }
    /**
     * Get formatted name.
     *
     * @param string $name
     *
     * @return string
     */
    public static function getFormattedName($name)
    {
        $themeName = Context::getContext()->shop->theme_name;
        $nameWithoutThemeName = str_replace(['_' . $themeName, $themeName . '_'], '', $name);

        //check if the theme name is already in $name if yes only return $name
        if ($themeName !== null && strstr($name, $themeName) && ImageType::getByNameNType($name)) {
            return $name;
        }

        if (ImageType::getByNameNType($nameWithoutThemeName . '_' . $themeName)) {
            return $nameWithoutThemeName . '_' . $themeName;
        }

        if (ImageType::getByNameNType($themeName . '_' . $nameWithoutThemeName)) {
            return $themeName . '_' . $nameWithoutThemeName;
        }

        return $nameWithoutThemeName . '_default';
    }
    public function actionAjax()
    {
        if (($query = Tools::getValue('q', false)) && $query != '' && Validate::isCleanHtml($query)) {
            EtsAmAdmin::searchProducts($query);
        }
        if (($productType = Tools::getValue('product_type', false)) && Validate::isCleanHtml($productType) && ($IDs = Tools::getValue('ids', false)) && Validate::isCleanHtml($IDs)) {
            die(json_encode(array(
                'html' => $this->hookDisplaySnwProductList(array('ids' => $IDs)),
            )));
        }
        if (($initSearch = Tools::getValue('initSearchProduct', false)) && Validate::isCleanHtml($initSearch) && ($ids = Tools::getValue('ids', false)) && Ets_affiliatemarketing::validateArray($ids)) {
            $this->getProductsAdded($ids);
        }
        if (($updateProduct = Tools::getValue('updateProductSetting', false)) && Validate::isCleanHtml($updateProduct) && ($data_settings = Tools::getValue('data', false))) {
            $setting_error = false;
            $aff_fields = array('use_default', 'id_product', 'how_to_calculate', 'default_fixed_amount', 'default_percentage', 'single_min_product');
            $loy_fields = array('use_default', 'id_product', 'base_on', 'amount', 'qty_min', 'gen_percent');
            if (isset($data_settings['loy_settings']) && $data_settings['loy_settings']) {
                foreach ($data_settings['loy_settings'] as $key => $data) {
                    if (in_array($key, $loy_fields)) {
                        if ($data && ((($key == 'amount' || $key == 'gen_percent') && !Validate::isUnsignedFloat($data)) || ($key == 'qty_min' && !Validate::isUnsignedInt($data)))) {
                            $setting_error = true;
                            break;
                        }
                    } else
                        unset($data_settings['loy_settings'][$key]);
                }
            }
            if (isset($data_settings['aff_settings']) && $data_settings['aff_settings'] && !$setting_error) {
                foreach ($data_settings['aff_settings'] as $key => $data) {
                    if (in_array($key, $aff_fields)) {
                        if ($key !== 'how_to_calculate') {
                            if ($key == 'single_min_product') {
                                if ($data && !Validate::isUnsignedInt($data)) {
                                    $setting_error = true;
                                    break;
                                }
                            } else {
                                if ($data && !Validate::isUnsignedFloat($data)) {
                                    $setting_error = true;
                                    break;
                                }
                            }
                        }
                    } else
                        unset($data_settings['aff_settings'][$key]);
                }
            }
            if ($setting_error) {
                die(json_encode(
                    array(
                        'success' => false,
                        'message' => $this->l('Update failed, data is invalid.')
                    )
                ));
            } else {
                //Create or update setting
                if ((isset($data_settings['loy_settings']) && $data_settings['loy_settings']) || (isset($data_settings['aff_settings']) && $data_settings['aff_settings'])) {
                    if ((isset($data_settings['loy_settings']) && $data_settings['loy_settings'])) {
                        $data_loy_setting = $data_settings['loy_settings'];
                        $data_loy_setting['id_shop'] = $this->context->shop->id;
                        EtsAmAdmin::createOrUpdateSetting('loyalty', $data_loy_setting);
                    }
                    if (isset($data_settings['aff_settings']) && $data_settings['aff_settings']) {
                        $data_aff_setting = $data_settings['aff_settings'];
                        $data_aff_setting['id_shop'] = $this->context->shop->id;
                        EtsAmAdmin::createOrUpdateSetting('affiliate', $data_aff_setting);
                    }
                    die(json_encode(
                        array(
                            'success' => true,
                            'message' => $this->l('Settings updated.')
                        )
                    ));
                }
                //Update fail
                die(json_encode(
                    array(
                        'success' => false,
                        'message' => $this->l('Update failed')
                    )
                ));
            }
        }
    }
    /**
     * @param $ids
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function getProductsAdded($ids)
    {
        if ($ids && is_array($ids)) {
            $ids_str = implode(',', array_map('intval', $ids));
            die(json_encode(array(
                'html' => $this->hookDisplaySnwProductList(array('ids' => $ids_str)),
            )));
        }
    }
    public function install()
    {
        EtsAmAdmin::createRequiresTable();
        EtsAmAdmin::addIndexTable();
        return parent::install()
            && $this->registerHook('displayShoppingCartFooter')
            && $this->registerHook('displayCustomerAccount')
            && $this->registerHook('displayProductAdditionalInfo')
            && $this->registerHook('actionValidateOrder')
            && $this->registerHook('actionCustomerAccountAdd')
            && $this->registerHook('displayHeader')
            && $this->registerHook('displayBackOfficeHeader')
            && $this->registerHook('displaySnwProductList')
            && $this->registerHook('displayAdminProductsExtra')
            && $this->registerHook('payment')
            && $this->registerHook('paymentOptions')
            && $this->registerHook('actionOrderStatusPostUpdate')
            && $this->registerHook('displayFooter')
            && $this->registerHook('displayCustomerAccountForm')
            && $this->registerHook('displayRightColumnProduct')
            && $this->registerHook('actionAuthentication')
            && $this->registerHook('actionCartSave')
            && $this->registerHook('actionCustomerLogoutAfter')
            && $this->registerHook('displayOrderConfirmation')
            && $this->registerHook('actionObjectOrderDetailDeleteAfter')
            && $this->registerHook('actionObjectOrderDetailAddAfter')
            && $this->registerHook('actionObjectOrderDetailUpdateAfter')
            && $this->registerHook('actionObjectOrderUpdateAfter')
            && $this->registerHook('actionFrontControllerAfterInit')
            && $this->setDefaultValues()
            && $this->__installTabs()
            && $this->setDefaultImage() && $this->installLinkDefault();
    }
    public function installLinkDefault()
    {
        $metas = array(
            array(
                'controller' => 'my_sale',
                'title' => $this->l('My sales'),
                'url_rewrite' => 'my-sales'
            ),
            array(
                'controller' => 'aff_products',
                'title' => $this->l('Affiliate Products'),
                'url_rewrite' => 'affiliate-products'
            ),
            array(
                'controller' => 'myfriend',
                'title' => $this->l('My friends'),
                'url_rewrite' => 'my-friends'
            ),
            array(
                'controller' => 'refer_friends',
                'title' => $this->l('How to refer friends'),
                'url_rewrite' => 'how-to-refer-friends'
            ),
            array(
                'controller' => 'loyalty',
                'title' => $this->l('Loyalty program'),
                'url_rewrite' => 'loyalty-program'
            ),
            array(
                'controller' => 'dashboard',
                'title' => $this->l('Dashboard'),
                'url_rewrite' => 'affiliate-dashboard'
            ),
            array(
                'controller' => 'history',
                'title' => $this->l('Reward history'),
                'url_rewrite' => 'reward-history'
            ),
            array(
                'controller' => 'withdraw',
                'title' => $this->l('Withdraw'),
                'url_rewrite' => 'affiliate-withdraw'
            ),
            array(
                'controller' => 'voucher',
                'title' => $this->l('Convert into vouchers'),
                'url_rewrite' => 'convert-into-vouchers'
            ),
            array(
                'controller' => 'register',
                'title' => $this->l('Register program'),
                'url_rewrite' => 'register-program'
            ),
        );
        $languages = Language::getLanguages(false);
        foreach ($metas as $meta) {
            if (!EtsAmAdmin::checkMetaExist($meta['url_rewrite'],$meta['controller'])) {
                $meta_class = new Meta();
                $meta_class->page = 'module-' . $this->name . '-' . $meta['controller'];
                $meta_class->configurable = 1;
                foreach ($languages as $language) {
                    $meta_class->title[$language['id_lang']] = $meta['title'];
                    $meta_class->url_rewrite[$language['id_lang']] = $meta['url_rewrite'];
                }
                $meta_class->add();
            }
        }
        return true;
    }
    /**
     * @return bool
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function uninstall()
    {
        return parent::uninstall()
            && EtsAmAdmin::removeModuleTable()
            && $this->__uninstallTabs()
            && $this->removeImages()
            && $this->clearLog();
    }
    /**
     * @return string
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function getContent()
    {
        $this->actionAjax(); // de lai
        if ((bool)Tools::isSubmit('getTotalUserAppPending', false)) { // de lai
            $total_pedning_app = EtsAmAdmin::getTotalPendingApplications();
            if ($total_pedning_app) {
                die(json_encode(array(
                    'success' => true,
                    'message' => $this->l('Successful'),
                    'total' => $total_pedning_app
                )));
            } else {
                die(json_encode(array(
                    'success' => false,
                    'message' => $this->l('Failed'),
                    'total' => 0
                )));
            }
        }
        if(Tools::isSubmit('viewreward_users') && ($tabActive = Tools::getValue('tabActive')) && $tabActive=='reward_users' && ($id_reward_users = (int)Tools::getValue('id_reward_users')))
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminEtsAmUsers').'&tabActive=reward_users&id_reward_users='.(int)$id_reward_users.'&viewreward_users');
        Tools::redirectAdmin($this->context->link->getAdminLink('AdminEtsAmDashboard'));
    }
    public function getAssign($activetab)
    {
        $cookie_filter = $this->context->cookie->getFamily('reward_usersFilter_');
        return array(
            'html' => $this->_html,
            'cookie_filter' => $cookie_filter,
            'submit_errors' => $this->_errors ? 1 : 0,
            'activetab' => $activetab,
            'admin_left_content' => $this->getAdminLeftContent($activetab),
            'idRewardUser' => (int)Tools::getValue('id_reward_users')
        );
    }
    public function getAdminLeftContent($activetab)
    {
        $cacheID =$this->_getCacheId(array('admin_left',$this->context->employee->id,$activetab));
        if(!$this->isCached('admin_left.tpl',$cacheID))
        {
            $setting_tabs = array();
            $breadcrumb_admin = array();
            $menuActive = $activetab;
            $defined = new EtsAffDefine();
            $def_config_tabs = $defined->def_config_tabs();
            $caption = array(
                'title' => '',
                'icon' => ''
            );
            foreach ($def_config_tabs as $key => $tab) {
                if ($key == $activetab) {
                    $caption['title'] = $tab['title'];
                    $caption['icon'] = $tab['icon'];
                } else {
                    if (isset($tab['subtabs']) && $tab['subtabs']) {
                        foreach ($tab['subtabs'] as $subkey => $subtab) {
                            if ($subtab) {
                                //
                            }
                            if ($subkey == $activetab) {
                                $caption['title'] = $tab['title'];
                                $caption['icon'] = $tab['icon'];
                                break;
                            }
                        }
                    }
                }
                if ($caption['title']) {
                    break;
                }
            }
            foreach ($def_config_tabs as $key => $tab) {
                if ($key == 'loyalty_program' || $key == 'affiliate_program' || $key == 'rs_program') {
                    if ($menuActive == $key) {
                        $menuActive = 'marketing_program';
                    }
                    $setting_tabs['marketing_program']['sub'][$key] = $tab;
                    $setting_tabs['marketing_program']['img'] = 'marketing_program.png';
                    $setting_tabs['marketing_program']['title'] = $this->l('Marketing programs');
                    $breadcrumb_admin['marketing_program']['title'] = $this->l('Marketing programs');
                    $breadcrumb_admin['marketing_program']['subtabs'][$key] = $tab;
                    if (isset($tab['subtabs']) && is_array($tab['subtabs'])) {
                        foreach ($tab['subtabs'] as $ks => $isub) {
                            if ($menuActive == $ks && $isub) {
                                $menuActive = 'marketing_program';
                                break;
                            }
                        }
                    }
                } else if ($key == 'usage_settings' || $key == 'reward_history' || $key == 'withdraw_list') {
                    if ($menuActive == $key) {
                        $menuActive = 'rewards';
                    }
                    $setting_tabs['rewards']['sub'][$key] = $tab;
                    $setting_tabs['rewards']['img'] = 'rewards.png';
                    $setting_tabs['rewards']['title'] = $this->l('Rewards');
                    $breadcrumb_admin['rewards']['title'] = $this->l('Rewards');
                    $breadcrumb_admin['rewards']['subtabs'][$key] = $tab;
                    if (isset($tab['subtabs']) && is_array($tab['subtabs'])) {
                        foreach ($tab['subtabs'] as $ks => $isub) {
                            if ($menuActive == $ks && $isub) {
                                $menuActive = 'rewards';
                                break;
                            }
                        }
                    }
                } else if ($key == 'applications' || $key == 'reward_users') {
                    if ($menuActive == $key) {
                        $menuActive = 'customers';
                    }
                    $setting_tabs['customers']['sub'][$key] = $tab;
                    $setting_tabs['customers']['img'] = 'customers.png';
                    $setting_tabs['customers']['title'] = $this->l('Customers');
                    $breadcrumb_admin['customers']['title'] = $this->l('Customers');
                    $breadcrumb_admin['customers']['subtabs'][$key] = $tab;
                    if (isset($tab['subtabs']) && is_array($tab['subtabs'])) {
                        foreach ($tab['subtabs'] as $ks => $isub) {
                            if ($menuActive == $ks && $isub) {
                                $menuActive = 'customers';
                                break;
                            }
                        }
                    }
                } else {
                    $setting_tabs[$key] = $tab;
                    if (isset($tab['subtabs']) && is_array($tab['subtabs'])) {
                        foreach ($tab['subtabs'] as $ks => $isub) {
                            if ($menuActive == $ks && $isub) {
                                $menuActive = $key;
                                break;
                            }
                        }
                    }
                }
            }
            $this->context->smarty->assign(
                array(
                    'linkJs' => $this->_path . 'views/js/admin.js',
                    'currency' => Currency::getDefaultCurrency(),
                    'caption' => $caption,
                    'config_tabs' => $def_config_tabs,
                    'setting_tabs' => $setting_tabs,
                    'activetab' => $activetab,
                    'menuActive' => $menuActive,
                    'breadcrumb_admin' => $breadcrumb_admin,
                    'linkImg' => $this->_path . 'views/img/',
                    'link_tab' => $this->context->link->getAdminLink('AdminModules', true) . '&configure=' . $this->name,
                )
            );
        }
        return $this->display(__FILE__,'admin_left.tpl',$cacheID);
    }
    /**
     * @return void
     */
    public function hookDisplayBackOfficeHeader()
    {
        $configure = Tools::getValue('configure');
        if (version_compare(_PS_VERSION_, '1.7.6.0', '>=') && version_compare(_PS_VERSION_, '1.7.7.0', '<'))
            $this->context->controller->addJS(_PS_JS_DIR_ . 'jquery/jquery-' . _PS_JQUERY_VERSION_ . '.min.js');
        elseif(version_compare(_PS_VERSION_, '1.7.7.0', '<='))
            $this->context->controller->addJquery();
        if ($configure == $this->name && !Tools::isSubmit('tabActive') && !Tools::isSubmit('getTotalUserAppPending')) {
            $this->context->cookie->closed_alert_cronjob = 0;
            $this->context->cookie->write();
        }
        $this->context->controller->addCss($this->_path . 'views/css/admin_all.css');
        $controller = Tools::getValue('controller');
        $controllers = array('AdminEtsAmDashboard','AdminEtsAmLoyalty','AdminEtsAmRS','AdminEtsAmAffiliate','AdminEtsAmApp','AdminEtsAmCronjob','AdminEtsAmRU','AdminEtsAmRewardHistory','AdminEtsAmApp','AdminEtsAmCronjob','AdminEtsAmGeneral','AdminEtsAmWithdrawals','AdminEtsAmBackup','AdminEtsAmUsers');
        if (($configure == $this->name && $controller == 'AdminModules') || in_array($controller,$controllers)) {
            $this->context->controller->addCss($this->_path . 'views/css/admin.css');
            $this->context->controller->addCss($this->_path . 'views/css/header.css');
            $this->context->controller->addJqueryPlugin('autocomplete');
            $this->context->controller->addCss($this->_path . 'views/css/daterangepicker.css');
            $this->context->controller->addJs($this->_path . 'views/js/moment.min.js');
            $this->context->controller->addJs($this->_path . 'views/js/daterangepicker.js');
        }
        if (!$this->is17) {
            $this->context->controller->addCss($this->_path . 'views/css/admin16.css');
        }
        if($controller=='AdminEtsAmRU')
            $this->context->controller->addJqueryUI('ui.sortable');
        if($controller=='AdminEtsAmDashboard')
        {
            $this->context->controller->addJS($this->_path . 'views/js/chart.js');
        }
        elseif ($controller == 'AdminProducts' && $this->active==1 && ((int)Tools::getValue('id_product') || (($request = $this->getRequestContainer()) && $request->get('id')))) {
            $this->context->controller->addCss($this->_path . 'views/css/admin_product.css');
            if ($this->is17) {
                $this->context->controller->addJs($this->_path . 'views/js/admin_product.js');
            }
        }
        $this->context->controller->addJs($this->_path . 'views/js/admin_all.js');
    }
    public function getRequestContainer()
    {
        if($sfContainer = $this->getSfContainer())
        {
            return $sfContainer->get('request_stack')->getCurrentRequest();
        }
        return null;
    }
    public function getSfContainer()
    {
        if(!class_exists('\PrestaShop\PrestaShop\Adapter\SymfonyContainer'))
        {
            $kernel = null;
            try{
                $kernel = new AppKernel('prod', false);
                $kernel->boot();
                return $kernel->getContainer();
            }
            catch (Exception $ex){
                return null;
            }
        }
        $sfContainer = call_user_func(array('\PrestaShop\PrestaShop\Adapter\SymfonyContainer', 'getInstance'));
        return $sfContainer;
    }

    /*== Add tab to sidebar admin === */
    /**
     * @return bool
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function __installTabs()
    {
        $languages = Language::getLanguages(false);
        if(!($eam_tab_id = Tab::getIdFromClassName('AdminEtsAm')))
        {   $tab = new Tab();
            $tab->class_name = 'AdminEtsAm';
            $tab->module = $this->name;
            if (!$this->is17) {
                $tab->icon = 'trophy';
            }
            $tab->id_parent = 0;
            foreach ($languages as $lang) {
                $tab->name[$lang['id_lang']] = $this->l('Marketing programs');
            }
            $tab->save();
            $eam_tab_id = $tab->id;
        }
        if ($eam_tab_id) {
            $defined = new EtsAffDefine();
            $menu_tabs = $defined->getMenuTabs();
            if($menu_tabs)
            {
                foreach($menu_tabs as $menu_tab)
                {
                    $id = $this->createTab($eam_tab_id,$menu_tab);
                    if($id && isset($menu_tab['subs']) && $menu_tab['subs'])
                    {
                        foreach($menu_tab['subs'] as $sub)
                        {
                            $this->createTab($id,$sub);
                        }
                    }
                }
            }
        }
        return true;
    }
    public function createTab($id_parent,$data)
    {
        $languages = Language::getLanguages(false);
        if($id = Tab::getIdFromClassName($data['class']))
        {
            $tab = new Tab($id);
            $tab->id_parent = $id_parent;
        }
        else
        {
            $tab = new Tab();
            $tab->class_name = $data['class'];
            $tab->module = $this->name;
            $tab->icon = $this->is17 ? $data['icon17']: $data['icon'];
            $tab->id_parent = $id_parent;
            foreach ($languages as $lang) {
                $tab->name[$lang['id_lang']] = $data['title'];
            }

        }
        $tab->save();
        return $tab->id;
    }
    /**
     * @return bool
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    private function __uninstallTabs()
    {
        $defined = new EtsAffDefine();
        $tabs = $defined->def_config_tabs();
        if (!empty($tabs)) {
            foreach ($tabs as $k => $item) {
                if (isset($item['subtabs']) && $item['subtabs']) {
                    foreach (array_keys($item['subtabs']) as $key_sub) {
                        $func = 'def_' . $key_sub;
                        if (!method_exists($defined, $func)) {
                            continue;
                        }
                        $cfg = $defined->{$func}();
                        if ($cfg && isset($cfg['config']) && $cfg['config']) {
                            $configs = $cfg['config'];
                            EtsAmAdmin::delelteConfig($configs);
                        }
                    }
                }
                $func = 'def_' . $k;
                if (!method_exists($defined, $func)) {
                    continue;
                }
                $cfg = $defined->{$func}();
                if ($cfg && isset($cfg['config']) && $cfg['config']) {
                    $configs = $cfg['config'];
                    EtsAmAdmin::delelteConfig($configs);
                }
            }
        }
        $menuTabs = $defined->getMenuTabs();
        if($menuTabs)
        {
            foreach($menuTabs as $menuTab)
            {
                if (isset($item['class']) && $menuTab['class']) {
                    if ($tabId = Tab::getIdFromClassName($menuTab['class'])) {
                        if(isset($menuTab['subs']) && $menuTab['subs'])
                        {
                            foreach($menuTab['subs'] as $sub)
                            {
                                if ($idsub = Tab::getIdFromClassName($sub['class'])) {
                                    $tab = new Tab($idsub);
                                    if ($tab) {
                                        $tab->delete();
                                    }
                                }
                            }
                        }
                        $tab = new Tab($tabId);
                        if ($tab) {
                            $tab->delete();
                        }
                    }
                }
            }
        }
        if ($tabId = Tab::getIdFromClassName('AdminEtsAm')) {
            $tab = new Tab($tabId);
            if ($tab) {
                $tab->delete();
            }
        }
        return true;
    }

    /**
     * @param $obj
     * @param $key
     * @param $values
     * @param bool $html
     */
    public function setFields($obj, $key, $values, $html = false)
    {
        if ($obj) {
            $obj->$key = $values;
        } else {
            Configuration::updateValue($key, $values, $html);
        }
    }
    /**
     * @param $type
     * @param null $label
     * @param null $value
     * @return bool|string
     */
    public function displaySmarty($type, $label = null, $value = null)
    {
        if (!$type)
            return false;
        $assign = array(
            'type' => $type,
            'label' => $label
        );
        if ($value)
            $assign = array_merge($assign, array(
                'value' => $value
            ));
        $this->smarty->assign($assign);
        return $this->display(__FILE__, 'admin-smarty.tpl');
    }
    /**
     * @param $params
     * @throws PrestaShopException
     */
    public function hookActionObjectOrderDetailDeleteAfter($params)
    {
        return $this->hookActionObjectOrderDetailUpdateAfter($params);
    }
    public function hookActionObjectOrderDetailUpdateAfter($params)
    {
        if (Configuration::get('ETS_AM_RECALCULATE_COMMISSION') && isset($params['object']) && ($order_detail = $params['object']) && Validate::isLoadedObject($order_detail) && isset($order_detail->id_order) && $order_detail->id_order) {
            $order = new Order($order_detail->id_order);
            $cart_loyalty = Ets_Loyalty::getOrderReward($order, null, true);
            if ($cart_loyalty) {
                $amount = is_array($cart_loyalty) ? (float)$cart_loyalty['reward'] : (float)$cart_loyalty;
                $data = array(
                    'id_friend' => (int)$order->id_customer,
                    'amount' => $amount,
                    'program' => EAM_AM_LOYALTY_REWARD,
                    'id_currency' => (int)Configuration::get('PS_CURRENCY_DEFAULT'),
                    'datetime_added' => date('Y-m-d H:i:s'),
                );
                $data['id_customer'] = (int)$order->id_customer;
                $data['id_order'] = (int)$order->id;
                $data['id_currency'] = (int)Configuration::get('PS_CURRENCY_DEFAULT');
                $products = is_array($cart_loyalty) && isset($cart_loyalty['products']) ? $cart_loyalty['products'] : array();
                if ($products)
                    $data['note'] = sprintf($this->l('Purchased loyalty product (Order: #%s)'), $order->id);
                else
                    $data['note'] = sprintf($this->l('Purchased loyalty shopping cart (Order: #%s)'), $order->id);
                Ets_Reward_Product::updateAmReward($data, $products);
            }
        }
        $this->_clearCache('*',$this->_getCacheId('dashboard',false));
    }
    public function hookActionObjectOrderUpdateAfter($params)
    {
        return $this->hookActionObjectOrderDetailUpdateAfter($params);
    }
    public function hookActionObjectOrderDetailAddAfter($params)
    {
        return $this->hookActionObjectOrderDetailUpdateAfter($params);
    }
    public function hookActionValidateOrder($params)
    {
        if (!(isset($params['cart'])) || !(isset($params['order'])) || !($cart = $params['cart']) || !($order = $params['order']))
            return;
        if ($order->module == $this->name) {
            $note = sprintf($this->l('Paid for order #%s'), $order->id);
            Ets_Reward_Usage::actionPaymentByReward($order,$note);
        }
        if(isset($params['orderStatus']) && $params['orderStatus'])
        {
            $order->current_state = $params['orderStatus']->id;
        }
        if (($cart_loyalty = Ets_Loyalty::calculateCartTotalReward($params, true)) && ($amount = is_array($cart_loyalty) ? (float)$cart_loyalty['reward'] : (float)$cart_loyalty)) {
            if ((float)$amount) {
                $data = array(
                    'id_friend' => $cart->id_customer,
                    'amount' => $amount,
                    'program' => EAM_AM_LOYALTY_REWARD,
                    'id_currency' => (int)Configuration::get('PS_CURRENCY_DEFAULT'),
                    'datetime_added' => date('Y-m-d H:i:s'),
                );
                $data['id_customer'] = (int)$this->context->customer->id;
                $data['id_shop'] = $cart->id_shop;
                $data['id_order'] = $order->id;
                $data['id_currency'] = Configuration::get('PS_CURRENCY_DEFAULT');
                $products = is_array($cart_loyalty) && isset($cart_loyalty['products']) ? $cart_loyalty['products'] : array();
                if ($products)
                    $data['note'] = sprintf($this->l('Purchased loyalty product (Order: #%s)'), $order->id);
                else
                    $data['note'] = sprintf($this->l('Purchased loyalty shopping cart (Order: #%s)'), $order->id);
                $rewardLoy = self::createNewAmReward($data, $products);
                Ets_Loyalty::sendEmailToAdminWhenNewRewardCreated($rewardLoy);
                Ets_Loyalty::sendEmailToCustomerWhenNewRewardCreated($rewardLoy);
            }
        }
        if ($aff_customer = $this->context->cookie->__get(EAM_AFF_CUSTOMER_COOKIE)) {
            $aff_customer = explode('-', $aff_customer);
            $aff_product = explode('-', $this->context->cookie->__get(EAM_AFF_PRODUCT_COOKIE));
            $customers = array();
            foreach ($aff_customer as $key => $customer) {
                if (!isset($customers[$customer]))
                    $customers[$customer] = array($aff_product[$key]);
                else
                    $customers[$customer][] = $aff_product[$key];
            }
            if ($customers) {
                $i = 0;
                foreach ($customers as $id_customer => $aff_pro) {
                    $i++;
                    $cartAffiliate = Ets_Affiliate::calculateAffiliateCartReward($cart, $this->context, true, $id_customer, $aff_pro, count($customers) == $i ? true : false);
                    if ($cartAffiliate && $cartAffiliate !== 0) {
                        if ($this->context->cookie->__get(EAM_AFF_CUSTOMER_COOKIE)) {
                            if ($this->context->cookie->__get(EAM_AFF_PRODUCT_COOKIE)) {
                                if (is_array($cartAffiliate)) {
                                    $products = $cartAffiliate['products'];
                                    $amount = $cartAffiliate['reward'];
                                } else {
                                    $products = array();
                                    $amount = $cartAffiliate;
                                }
                                if ($amount > 0) {
                                    $date = date('Y-m-d H:i:s');
                                    $data = array(
                                        'id_friend' => $cart->id_customer,
                                        'amount' => $amount,
                                        'program' => EAM_AM_AFFILIATE_REWARD,
                                        'datetime_added' => $date,
                                        'datetime_canceled' => null,
                                        'datetime_validated' => null,
                                        'id_customer' => $id_customer,
                                        'id_shop' => $params['cart']->id_shop,
                                        'id_order' => $params['order']->id,
                                        'note' => sprintf($this->l('Affiliate commission (Order: #%s)'), $params['order']->id),
                                        'id_currency' => Configuration::get('PS_CURRENCY_DEFAULT')
                                    );
                                    $data['expired_date'] = null;
                                    $reward = self::createNewAmReward($data, $products);
                                    if ((int)Configuration::get('ETS_AM_ENABLED_EMAIL_ADMIN_RC')) {
                                        Ets_Affiliate::sendEmailWhenAffiliateRewardCreated($this->l('A new reward was created'), $reward, true);
                                    }
                                    if ((int)Configuration::get('ETS_AM_ENABLED_EMAIL_CUSTOMER_RC')) {
                                        Ets_Affiliate::sendEmailWhenAffiliateRewardCreated($this->l('A new reward created for you'), $reward, false);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        if (!$this->context->cookie->__get(EAM_REFS)) {
            $ref = Ets_Sponsor::getIdRefByCart($cart->id, $this->context->customer->id);
        }
        if (isset($ref) && $ref) {
            $this->context->cookie->__unset(EAM_REFS);
            $this->context->cookie->__unset('ets_am_show_voucher_ref');
            if (Ets_Sponsor::addFriendSponsored($ref))
                Ets_Sponsor::getRewardWithoutOrder($ref);
        }
        Ets_Sponsor::getRewardWithFirstOrder($cart, $order);
        Ets_Sponsor::getRewardOnOrder($params);
        $this->_clearCache('*',$this->_getCacheId('dashboard',false));
    }
    /**
     * @param $data
     * @param array $products
     * @return bool|Ets_AM
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    protected function createNewAmReward($data, $products = array())
    {
        $reward = new Ets_AM();
        foreach ($data as $key => $value) {
            $reward->{$key} = $value;
        }
        $reward->add(true, true) ? $reward : false;
        if ($reward->id && $products) {
            foreach ($products as $product) {
                $product_reward = new Ets_Reward_Product();
                $product_reward->id_product = $product['id_product'];
                if(isset($data['program']) && $data['program']== EAM_AM_LOYALTY_REWARD)
                {
                    $product_reward->quantity = 1;
                }
                else
                {
                    if(Configuration::get('ETS_AM_AFF_MULTIPLE')) {
                        $product_reward->quantity = isset($product['product_quantity']) ? (int)$product['product_quantity'] : (int)$product['quantity'];
                    } else{
                        $product_reward->quantity = 1;
                    }
                }
                $product_reward->id_ets_am_reward = $reward->id;
                $product_reward->amount = $product['reward_amount'];
                $product_reward->id_order = $data['id_order'];
                $product_reward->id_seller = $data['id_customer'];
                $product_reward->program = $data['program'];
                $product_reward->datetime_added = date('Y-m-d H:i:s');
                $product_reward->add();
            }
        }
        return $reward ? $reward : false;
    }
    /**
     * @param $params
     * @return null|string
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function hookDisplayShoppingCartFooter($params)
    {
        $context = $this->context;
        if ($context->customer->id && (Configuration::get('ETS_AM_AFF_ALLOW_VOUCHER_IN_CART') === false || Configuration::get('ETS_AM_AFF_ALLOW_VOUCHER_IN_CART'))) {
            $total_earn = Ets_Reward_Usage::getTotalEarn(null, $context->customer->id, $context);
            $total_spent = Ets_Reward_Usage::getTotalSpent($context->customer->id, false, null, $context);
            $total_balance = $total_earn - $total_spent;
        }
        if (!Ets_Loyalty::isCustomerSuspendedOrBannedLoyaltyProgram($this->context->customer->id)) {
            $cart = $params['cart'];
            $message = Ets_Loyalty::getCartMessage($cart, $this->context);
        }
        $convert_message = Configuration::get('ETS_AM_AFF_ALLOW_CONVERT_TO_VOUCHER') ? ( Configuration::get('ETS_AM_AFF_CONVERT_VOUCHER_MSG', $this->context->language->id) ? Configuration::get('ETS_AM_AFF_CONVERT_VOUCHER_MSG', $this->context->language->id) : $this->l('You have [available_reward_to_convert] in your balance. It can be converted into voucher code. [Convert_now]')):'';
        $this->smarty->assign(array(
            'message' => isset($message) && $message ? $message : false,
            'link' => $this->context->link,
            'convert_message' => $convert_message,
            'total_balance' => isset($total_balance) && $total_balance ? Ets_AM::displayRewardInMsg($total_balance, $this->context) : false,
        ));
        $button = EtsAffDefine::displayText($this->l('Convert now'),'a','link-convert-now',null,$this->context->link->getModuleLink($this->name,'voucher'));
        $this->context->smarty->assign('convert_now_button', $button);
        return $this->display(__FILE__, 'cart-message.tpl');
    }
    /**
     * @return bool
     */
    private function setDefaultValues()
    {
        $this->generateTokenCronjob();
        $languages = Language::getLanguages(false);
        if (($id_order_state = (int)EtsAmAdmin::getOrderStateByModule($this->name))) {
            $orderState = new OrderState($id_order_state);
        } else
            $orderState = new OrderState();
        foreach ($languages as $lang) {
            $orderState->name[(int)$lang['id_lang']] = $this->l('Reward payment accepted');
        }
        $orderState->invoice = 0;
        $orderState->send_email = false;
        $orderState->module_name = $this->name;
        $orderState->color = '#32CD32';
        $orderState->unremovable = 1;
        $orderState->paid = 1;
        if ($orderState->save()) {
            $source = _PS_MODULE_DIR_ . $this->name . '/views/img/temp/os_payment.gif';
            $destination = _PS_ROOT_DIR_ . '/img/os/' . (int)$orderState->id . '.gif';
            copy($source, $destination);
            Configuration::updateValue('PS_ETS_AM_REWARD_PAID', $orderState->id);
        }
        $obj = false;
        $defined = new EtsAffDefine();
        $def_config_tabs = $defined->def_config_tabs();
        if ($def_config_tabs) {
            foreach ($def_config_tabs as $keytab => $tab) {
                if (isset($tab['subtabs']) && $tab['subtabs']) {
                    foreach ($tab['subtabs'] as $k => $subtab) {
                        if ($subtab) {
                            //
                        }
                        $func = 'def_' . $k;
                        if (!method_exists($defined, $func)) {
                            continue;
                        }
                        $cfg = $defined->{$func}();
                        if ($cfg && isset($cfg['form']) && isset($cfg['config']) && $cfg['config']) {
                            $configs = $cfg['config'];
                            $this->insertDefaultData($configs, $languages, $obj);
                        }
                    }
                } else {
                    $func = 'def_' . $keytab;
                    if (!method_exists($defined, $func)) {
                        continue;
                    }
                    $cfg = $defined->{$func}();
                    if ($cfg && isset($cfg['form']) && isset($cfg['config']) && $cfg['config']) {
                        $configs = $cfg['config'];
                        $this->insertDefaultData($configs, $languages, $obj);
                    }
                }
            }
        }
        //Create default payment method
        $pm_params = array();
        $pmf_params = array(
            array(
                'type' => 'text',
                'required' => 1,
                'enable' => 1,
                'sort' => 1,
            ),
            array(
                'type' => 'text',
                'required' => 1,
                'enable' => 1,
                'sort' => 2,
            ),
            array(
                'type' => 'text',
                'required' => 1,
                'enable' => 1,
                'sort' => 3,
            ),
            array(
                'type' => 'text',
                'required' => 1,
                'enable' => 1,
                'sort' => 4,
            ),
        );
        $languages = Language::getLanguages(false);
        foreach ($languages as $lang) {
            $pm_params['title'][$lang['id_lang']] = $this->l('PayPal');
            $pm_params['desc'][$lang['id_lang']] = $this->l('The fastest method to withdraw funds, directly to your local bank account!');
            $pm_params['note'][$lang['id_lang']] = null;
            foreach ($pmf_params as &$p) {
                if ($p['sort'] == 1) {
                    $p['title'][$lang['id_lang']] = $this->l('First name');
                    $p['desc'][$lang['id_lang']] = $this->l('Type your first name');
                } elseif ($p['sort'] == 2) {
                    $p['title'][$lang['id_lang']] = $this->l('Last name');
                    $p['desc'][$lang['id_lang']] = $this->l('Type your last name');
                } elseif ($p['sort'] == 3) {
                    $p['title'][$lang['id_lang']] = $this->l('PayPal email');
                    $p['desc'][$lang['id_lang']] = $this->l('Type your PayPal email to receive money');
                } elseif ($p['sort'] == 4) {
                    $p['title'][$lang['id_lang']] = $this->l('Phone');
                    $p['desc'][$lang['id_lang']] = $this->l('Type your phone');
                }
            }
        }
        $pm_params['fee_fixed'] = 1;
        $pm_params['fee_type'] = 'NO_FEE';
        $pm_params['fee_percent'] = null;
        $pm_params['enable'] = 1;
        $pm_params['estimate_processing_time'] = 7;
        $pm = new Ets_PaymentMethod();
        $id_pm = $pm->createPaymentMethod($pm_params);
        if ($id_pm) {
            foreach ($pmf_params as $pmf_param) {
                $pmf = new Ets_PaymentMethodField();
                $pmf_param['id_payment_method'] = $id_pm;
                $pmf->createPaymentMethodField($pmf_param);
            }
        }
        //Set cookie notification cronjob
        $this->context->cookie->closed_alert_cronjob = 1;
        $this->context->cookie->write();
        return true;
    }
    public function setDefaultImage()
    {
        if (!is_dir(EAM_PATH_IMAGE_BANER))
            @mkdir(EAM_PATH_IMAGE_BANER, 0755, true);
        @copy(_PS_ROOT_DIR_ . '/modules/ets_affiliatemarketing/views/img/temp/default_popup_banner.jpg', EAM_PATH_IMAGE_BANER . 'ets_am_ref_intro_banner.jpg');
        @copy(_PS_ROOT_DIR_ . '/modules/ets_affiliatemarketing/views/img/temp/default_banner.jpg', EAM_PATH_IMAGE_BANER . 'default_banner.jpg');
        return true;
    }
    private function insertDefaultData($configs, $languages, $obj)
    {
        foreach ($configs as $key => $config) {
            $default_value = false;
            if (isset($config['default']) && $config['default']) {
                $default_value = $config['default'];
            }
            if (isset($config['lang']) && $config['lang']) {
                $values = array();
                foreach ($languages as $lang) {
                    if ($config['type'] == 'switch') {
                        $values[$lang['id_lang']] = (int)$default_value ? 1 : 0;
                    } else {
                        $values[$lang['id_lang']] = $default_value ? $default_value : null;
                    }
                }
                $this->setFields($obj, $key, $values, true);
            } else {
                if ($config['type'] == 'switch') {
                    $this->setFields($obj, $key, (int)$default_value);
                } elseif ($config['type'] == 'select' && isset($config['multiple']) && $config['multiple']) {
                    if ($default_value) {
                        $this->setFields($obj, $key, $default_value);
                    } else {
                        $this->setFields($obj, $key, null);
                    }
                } elseif ($config['type'] == 'ets_checkbox_group') {
                    $checkbox_value = array();
                    if (isset($config['values']) && $config['values']) {
                        foreach ($config['values'] as $option) {
                            if (isset($option['default']) && $option['default'] && isset($option['value']) && $option['value']) {
                                $checkbox_value[] = $option['value'];
                            }
                        }
                    }
                    $this->setFields($obj, $key, $checkbox_value ? implode(',', $checkbox_value):'');
                } elseif ($config['type'] == 'ets_radio_group') {
                    $radio_value = null;
                    if (isset($config['values']) && $config['values']) {
                        foreach ($config['values'] as $option) {
                            if (isset($option['default']) && $option['default'] && isset($option['value']) && $option['value']) {
                                $radio_value = $option['value'];
                                break;
                            }
                        }
                    }
                    $this->setFields($obj, $key, $radio_value);
                } elseif ($config['type'] == 'ets_radio_group_tree') {
                    $tree_value = null;
                    if (isset($config['values']) && $config['values']) {
                        foreach ($config['values'] as $option) {
                            if (isset($option['default']) && $option['default'] && isset($option['value']) && $option['value']) {
                                $tree_value = $option['value'];
                                break;
                            }
                        }
                    }
                    $this->setFields($obj, $key, $tree_value);
                } elseif ($config['type'] == 'text_search_prd') {
                    $this->setFields($obj, $key, null);
                } elseif ($config['type'] == 'file' && isset($config['is_image']) && $config['is_image']) {
                    $this->setFields($obj, $key, $default_value);
                } elseif ($key != 'position') {
                    if ($default_value) {
                        $this->setFields($obj, $key, $default_value, true);
                    } else {
                        $this->setFields($obj, $key, null, true);
                    }
                }
            }
        }
    }
    /**
     * @return string
     * @throws Exception
     */
    public function hookDisplayHeader()
    {
        if (!$this->is17 && ($code = Tools::getValue('discount_name')) && (Tools::getValue('controller') == 'cart' || Tools::getValue('controller') == 'order') && Tools::isSubmit('addDiscount') && (Tools::isSubmit('ajax') || Tools::isSubmit('ajax_request')))
            Ets_Voucher::getInstance()->checkCartRuleValidity($code);
        $lang = $this->context->language->id;
        $aff_customer = (int)Tools::getValue('affp');
        $aff_product = (int)Tools::getValue('id_product');
        Ets_Affiliate::setAffCustomer($aff_customer,$aff_product);
        if (($ref = (int)Tools::getValue('refs')) && !$this->context->customer->isLogged()) {
            Ets_Sponsor::setCookieRef($ref);
            $this->smarty->assign(array(
                'og_url' => Ets_AM::getBaseUrl() . '?refs=' . $ref,
                'og_type' => 'article',
                'og_title' => Configuration::get('ETS_AM_REF_SOCIAL_TITLE', $lang) ? Configuration::get('ETS_AM_REF_SOCIAL_TITLE', $lang) : '',
                'og_description' => Configuration::get('ETS_AM_REF_SOCIAL_DESC', $lang) ? Configuration::get('ETS_AM_REF_SOCIAL_DESC', $lang) : '',
                'og_image' => Configuration::get('ETS_AM_REF_SOCIAL_IMG') ? Ets_AM::getBaseUrl() . EAM_PATH_IMAGE_BANER . Configuration::get('ETS_AM_REF_SOCIAL_IMG') : '',
                '_token' => Tools::getToken(false),
            ));
        }
        $controller = Tools::getValue('controller');
        if (($module = Tools::getValue('module')) && $module == 'ets_affiliatemarketing') {
            $this->context->controller->addJS($this->_path . 'views/js/front/ets_affiliatemarketing.js');
            if($controller =='dashboard')
            {
                $this->context->controller->addCss(_PS_MODULE_DIR_ . 'ets_affiliatemarketing/views/css/nv.d3.css');
                $this->context->controller->addJs(_PS_MODULE_DIR_ . 'ets_affiliatemarketing/views/js/d3.v3.min.js');
                $this->context->controller->addJs(_PS_MODULE_DIR_ . 'ets_affiliatemarketing/views/js/nv.d3.min.js');
            }
            $this->context->controller->addCss(_PS_MODULE_DIR_ . 'ets_affiliatemarketing/views/css/daterangepicker.css');
            $this->context->controller->addJs(_PS_MODULE_DIR_ . 'ets_affiliatemarketing/views/js/moment.min.js');
            $this->context->controller->addJs(_PS_MODULE_DIR_ . 'ets_affiliatemarketing/views/js/daterangepicker.js');
        }
        $this->context->controller->addCss($this->_path . 'views/css/front.css');
        if (!$this->is17) {
            $this->context->controller->addCss($this->_path . 'views/css/front16.css');
        }
        $this->context->controller->addJS($this->_path . 'views/js/front.js');
        if ( $controller == 'product' && $aff_customer) {
            $id_product = (int)Tools::getValue('id_product');
            $this->smarty->assign(array(
                'ets_am_product_view_link' => Ets_AM::getBaseUrlDefault('product_view', array('id_product' => $id_product, 'affp' => $aff_customer)),
                'eam_id_seller' => $aff_customer ? (int)$aff_customer : 0
            ));
        }
        $this->smarty->assign(array(
            'link_cart' => $this->context->link->getPageLink('cart', Tools::usingSecureMode() ? true : false),
            'link_reward' => $this->context->link->getModuleLink($this->name, 'dashboard', array('ajax' => 1), Tools::usingSecureMode() ? true : false),
            'link_shopping_cart' => $this->context->link->getModuleLink('ps_shoppingcart', 'ajax', array(), Tools::usingSecureMode() ? true : false),
            '_token' => Tools::getToken(false),
        ));
        return $this->display(__FILE__, 'head.tpl');
    }
    /**
     * @param $key
     * @param $value
     * @return void
     * @throws Exception
     */
    public function setCookie($key, $value)
    {
        $this->context->cookie->__set($key, $value);
    }
    /**
     * @param $key
     * @return string
     */
    public function getCookie($key)
    {
        return $this->context->cookie->__get($key);
    }
    /**
     * @param $params
     * @return string
     * @throws PrestaShopException
     */
    public function hookDisplayAdminProductsExtra($params)
    {
        if (!($loyaltyBaseOn = Configuration::get('ETS_AM_LOYALTY_BASE_ON')))
            return;
        $id_product = (int)Tools::getValue('id_product');
        if ((isset($params['id_product']) && (int)$params['id_product']) || $id_product) {
            $id_product = isset($params['id_product']) && (int)$params['id_product'] ? (int)$params['id_product'] : $id_product;
            $this->_id_product = $id_product;
            $id_shop = $this->context->shop->id;
            $loyalty_reward = array();
            $loyalty_reward_fields = array(
                'ETS_AM_LOYALTY_BASE_ON', 'ETS_AM_LOYALTY_AMOUNT', 'ETS_AM_LOYALTY_AMOUNT_PER', 'ETS_AM_LOYALTY_GEN_PERCENT', 'ETS_AM_QTY_MIN'
            );
            $aff_reward = array();
            $aff_reward_fields = array(
                'ETS_AM_AFF_HOW_TO_CALCULATE',
                'ETS_AM_AFF_DEFAULT_PERCENTAGE',
                'ETS_AM_AFF_DEFAULT_FIXED_AMOUNT'
            );
            $loyalty_reward_data = EtsAmAdmin::getLoyaltySettings($id_product, $id_shop);
            $aff_reward_data = EtsAmAdmin::getAffiliateSettings($id_product, $id_shop);
            $defined = new EtsAffDefine();
            if ($loyaltyBaseOn && $loyaltyBaseOn !== 'DYNAMIC') {
                if (isset($defined->def_reward_settings()['config']) && $defined->def_reward_settings()['config']) {
                    foreach ($defined->def_reward_settings()['config'] as $key => $config) {
                        if (in_array($key, $loyalty_reward_fields)) {
                            $name = Tools::strtolower(str_replace('ETS_AM_LOYALTY_', '', $key));
                            $name = str_replace('ets_am_', '', $name);
                            $config['class'] = $key;
                            if (!empty($loyalty_reward_data)) {
                                $config['value'] = $loyalty_reward_data[$name];
                            }
                            if ($key == 'ETS_AM_LOYALTY_BASE_ON') {
                                $loyalty_bases = $config['values'];
                                unset($loyalty_bases['SPC_FIXED'], $loyalty_bases['SPC_PERCENT']);
                                $config['values'] = $loyalty_bases;
                            }
                            $loyalty_reward[$name] = $config;
                        }
                    }
                    $loyalty_reward['use_default'] = !empty($loyalty_reward_data) ? $loyalty_reward_data['use_default'] : 1;
                }
            }
            if (isset($defined->def_affiliate_reward_caculation()['config']) && $defined->def_affiliate_reward_caculation()['config']) {
                foreach ($defined->def_affiliate_reward_caculation()['config'] as $key => $config) {
                    if (in_array($key, $aff_reward_fields)) {
                        $name = Tools::strtolower(str_replace('ETS_AM_AFF_', '', $key));
                        $config['class'] = $key;
                        if (!empty($aff_reward_data)) {
                            $config['value'] = $aff_reward_data[$name];
                        }
                        $aff_reward[$name] = $config;
                    }
                }
                $aff_reward['use_default'] = !empty($aff_reward_data) ? $aff_reward_data['use_default'] : 1;
            }
            $aff_excluded = Configuration::get('ETS_AM_AFF_PRODUCTS_EXCLUDED') ? explode(',', Configuration::get('ETS_AM_AFF_PRODUCTS_EXCLUDED')) : array();
            $discount_excluded = Configuration::get('ETS_AM_AFF_PRODUCTS_EXCLUDED_DISCOUNT') && EtsAmAdmin::checkSpecificProudct($id_product);
            $this->smarty->assign(array(
                'settings' => array(
                    'aff_reward' => !in_array($id_product, $aff_excluded) && !$discount_excluded ? $aff_reward : array(),
                    'loyalty_reward' => Configuration::get('ETS_AM_LOYALTY_BASE_ON') == 'SPC_FIXED' || Configuration::get('ETS_AM_LOYALTY_BASE_ON') == 'SPC_PERCENT' ? false : $loyalty_reward
                ),
                'loyalty_base_on' => $loyaltyBaseOn,
                'id_product' => $id_product,
                'linkAjax' => $this->context->link->getAdminLink('AdminModules', true) . '&conf=4&configure=' . $this->name,
                'using_cart' => Configuration::get('ETS_AM_LOYALTY_BASE_ON') == 'SPC_FIXED' || Configuration::get('ETS_AM_LOYALTY_BASE_ON') == 'SPC_PERCENT' ? 1 : 0,
                'is17' => $this->is17,
                'linkJs' => $this->_path . 'views/js/admin_product.js'
            ));
            return $this->display(__FILE__, 'product_settings.tpl');
        }
    }
    /**
     * @return string
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function hookDisplayCustomerAccount()
    {
        $customer = $this->context->customer;
        $output = '';
        $this->smarty->assign(array(
            'customer' => $customer,
            'is17' => $this->is17
        ));
        if (Ets_Sponsor::isRefferalProgramReady() && Ets_AM::isCustomerBelongToValidGroup($customer, 'ETS_AM_REF_GROUPS')) {
            if (Configuration::get('ETS_AM_REF_MSG_CONDITION', $this->context->language->id) != '') {
                $this->smarty->assign(array(
                    'refUrl' => Ets_AM::getBaseUrlDefault('refer_friends')
                ));
                $output .= $this->display(__FILE__, 'referral_box.tpl');
            } else {
                if (Ets_Sponsor::canUseRefferalProgram((int)$customer->id) || Ets_Sponsor::registeredReferralProgram((int)$customer->id)) {
                    $this->smarty->assign(array(
                        'refUrl' => Ets_AM::getBaseUrlDefault('refer_friends')
                    ));
                    $output .= $this->display(__FILE__, 'referral_box.tpl');
                }
            }
        }
        if ((int)Configuration::get('ETS_AM_LOYALTY_ENABLED') && Ets_AM::isCustomerBelongToValidGroup($customer, 'ETS_AM_LOYALTY_GROUPS')) {
            if (Configuration::get('ETS_AM_LOY_MSG_CONDITION', $this->context->language->id) != '') {
                $this->smarty->assign(array(
                    'refUrl' => Ets_AM::getBaseUrlDefault('loyalty')
                ));
                $output .= $this->display(__FILE__, 'loyalty_box.tpl');
            } else {
                if (Ets_Loyalty::isCustomerCanJoinLoyaltyProgram()) {
                    if ($min = Configuration::get('ETS_AM_LOYALTY_MIN_SPENT')) {
                        $minSpent = Ets_Loyalty::calculateCustomerSpent();
                        if ($minSpent >= (float)$min) {
                            $this->smarty->assign(array(
                                'refUrl' => Ets_AM::getBaseUrlDefault('loyalty')
                            ));
                            $output .= $this->display(__FILE__, 'loyalty_box.tpl');
                        }
                    } else {
                        $this->smarty->assign(array(
                            'refUrl' => Ets_AM::getBaseUrlDefault('loyalty')
                        ));
                        $output .= $this->display(__FILE__, 'loyalty_box.tpl');
                    }
                }
            }
        }
        if ((int)Configuration::get('ETS_AM_AFF_ENABLED') && Ets_AM::isCustomerBelongToValidGroup($customer, 'ETS_AM_AFF_GROUPS')) {
            if (Configuration::get('ETS_AM_AFF_MSG_CONDITION', $this->context->language->id) != '') {
                $this->smarty->assign(array(
                    'refUrl' => Ets_AM::getBaseUrlDefault('aff_products')
                ));
                $output .= $this->display(__FILE__, 'affiliate_box.tpl');
            } else {
                if (Configuration::get('ETS_AM_AFF_ENABLED')) {
                    $valid = false;
                    if (Ets_Affiliate::isCustomerCanJoinAffiliateProgram()) {
                        $valid = true;
                    }
                    if ($valid) {
                        $this->smarty->assign(array(
                            'refUrl' => Ets_AM::getBaseUrlDefault('aff_products')
                        ));
                        $output .= $this->display(__FILE__, 'affiliate_box.tpl');
                    }
                }
            }
        }
        if (!Configuration::get('ETS_AM_LOY_MSG_CONDITION', $this->context->language->id) && !Configuration::get('ETS_AM_REF_MSG_CONDITION', $this->context->language->id) && !Configuration::get('ETS_AM_AFF_MSG_CONDITION', $this->context->language->id)) {
            $this->smarty->assign(array(
                'refUrl' => Ets_AM::getBaseUrlDefault('dashboard')
            ));
            $output .= $this->display(__FILE__, 'customer_reward.tpl');
        } else {
            $this->smarty->assign(array(
                'refUrl' => Ets_AM::getBaseUrlDefault('dashboard')
            ));
            $output .= $this->display(__FILE__, 'customer_reward.tpl');
        }
        return $output;
    }
    /**
     * @return array
     */
    protected function getTemplateVarInfos()
    {
        $total_balance = Ets_Reward_Usage::getTotalBalance($this->context->customer->id);
        if (Ets_AM::needExchange($this->context)) {
            $total_balance = Tools::convertPrice($total_balance);
        }
        $show_point = Configuration::get('ETS_AM_REWARD_DISPLAY') == 'point' ? 1 : 0;
        return array(
            'eam_reward_total_balance' => Ets_affiliatemarketing::displayPrice($total_balance),
            'eam_reward_point' => $show_point ? Ets_AM::displayReward($total_balance) : 0,
            'show_point' => $show_point,
        );
    }
    public function getLinkCustomerAdmin($id_customer)
    {
        if (version_compare(_PS_VERSION_, '1.7.6', '>=')) {
            $sfContainer = call_user_func(array('\PrestaShop\PrestaShop\Adapter\SymfonyContainer', 'getInstance'));
            if (null !== $sfContainer) {
                $sfRouter = $sfContainer->get('router');
                $link_customer = $sfRouter->generate(
                    'admin_customers_view',
                    array('customerId' => $id_customer)
                );
            }
        } else
            $link_customer = $this->context->link->getAdminLink('AdminCustomers') . '&id_customer=' . (int)$id_customer . '&viewcustomer';
        return $link_customer;
    }
    /**
     * @param $params
     * @return string
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function hookDisplayProductAdditionalInfo($params)
    {
        if (!(isset($params['product'])) || !$params['product'])
            return;
        $cart = $this->context->cart;
        $id_product = (int)Tools::getValue('id_product');
        $product = $params['product'];
        $count_current_product = 0;
        if ($products = $cart->getProducts()) {
            foreach ($products as $p) {
                $idProduct = is_object($product) ? $product->id : (int)$product['id_product'];
                if ((int)$p['id_product'] == $idProduct) {
                    $count_current_product = (int)$p['cart_quantity'];
                    break;
                }
            }
        }
        $loyalty_suspended = Ets_Loyalty::isCustomerSuspendedOrBannedLoyaltyProgram($this->context->customer->id);
        $affiliate_suspended = Ets_Affiliate::isCustomerSuspendedOrBannedAffiliateProgram($this->context->customer->id);
        if (!$loyalty_suspended || !$affiliate_suspended) {
            $data_product = array();
            $p = is_object($product) && $product instanceof Product ? $product : new Product($product['id_product']);
            $data_product['price_with_reduction_without_tax'] = $p->getPrice(false);
            $data_product['price_with_reduction'] = $p->getPrice(true);
            $data_product['id_product'] = $p->id;
            $product['price_with_tax_with_reduction'] = $p->getPrice();
            $product['price_without_tax_with_reduction'] = $p->getPrice(false);
            $product['price_with_tax_without_reduction'] = $p->getPriceWithoutReduct(false);
            $product['price_without_tax_without_reduction'] = $p->getPriceWithoutReduct(true);
            $assignment = array(
                'loyalty_suspended' => $loyalty_suspended,
                'affiliate_suspended' => $affiliate_suspended,
            );
            $assignment['eam_product_addition_loy_message'] = 'ban';
            $assignment['eam_product_addition_aff_message'] = 'ban';
            if (!$loyalty_suspended) {
                $qty_min = Configuration::get('ETS_AM_QTY_MIN');
                $productRewardSetting = Ets_Loyalty_Config::getProductRewardSetting($product['id_product']);
                if ($productRewardSetting && count($productRewardSetting) && (int)$productRewardSetting['use_default'] != 1) {
                    $qty_min = $productRewardSetting['qty_min'];
                }
                if (!$qty_min || (int)$qty_min > $count_current_product) {
                    $eam_product_addition_loy_message = Ets_Loyalty::getLoyaltyMessageOnProductPage($product, $this->context, $cart);
                } else {
                    $eam_product_addition_loy_message = Ets_Loyalty::getLoyaltyMessageOnProductPage($product, $this->context);
                }
                $assignment['eam_product_addition_loy_message'] = $eam_product_addition_loy_message;
            }
            $productClass = new Product($product['id_product']);
            if (!$affiliate_suspended) {
                $data_message = Ets_Affiliate::getAffiliateMessage($data_product, $this->context);
                $eam_product_addition_aff_message = null;
                if ($data_message && is_array($data_message)) {
                    $eam_product_addition_aff_message = $this->getAffiliateMessage($data_message);
                    if ($data_message['is_aff'])
                        $assignment['link_share'] = $data_message['link'];
                }
                $assignment['eam_product_addition_aff_message'] = $eam_product_addition_aff_message;
            }
            $this->smarty->assign($assignment);
            if (Configuration::get('ETS_AM_AFF_ENABLED') && $this->getCookie(EAM_AFF_PRODUCT_COOKIE)) {
                $aff_products_cookie = explode('-', $this->getCookie(EAM_AFF_PRODUCT_COOKIE));
                $aff_customers = explode('-', $this->getCookie(EAM_AFF_CUSTOMER_COOKIE));
                if ($aff_products_cookie) {
                    foreach ($aff_products_cookie as $key => $aff_product)
                        if ($aff_product == $product['id_product'])
                            $aff_customer = isset($aff_customers[$key]) ? $aff_customers[$key] : 0;
                }
                $display_aff_promo_code = false;
                $aff_promo_code_msg = null;
                if (in_array($product['id_product'], $aff_products_cookie) && Ets_Voucher::canAddAffiliatePromoCode($product['id_product'], $aff_customer, true)) {
                    if (Ets_Affiliate::productValidAffiliateProgram($productClass) && $discount_value = Ets_AM::getDiscountVoucher('aff')) {
                        $display_aff_promo_code = true;
                        $mesage_code = strip_tags(Configuration::get('ETS_AM_AFF_WELCOME_MSG', $this->context->language->id));
                        $aff_promo_code_msg = str_replace('[discount_value]', $discount_value, $mesage_code);
                    }
                }
                if ($display_aff_promo_code && $aff_promo_code_msg) {
                    $this->smarty->assign(array(
                        'eam_display_aff_promo_code' => true,
                        'eam_aff_promo_code_message' => $aff_promo_code_msg,
                    ));
                }
            }
            $product_classs = new Product($id_product, false, $this->context->language->id);
            $this->smarty->assign(
                array(
                    'product' => $product_classs,
                    'link' => $this->context->link,
                )
            );
            return $this->display(__FILE__, 'product-additional.tpl');
        }
    }
    /**
     * @param $params
     * @return string
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function hookDisplayRightColumnProduct($params)
    {
        if (!$this->is17) {
            $loyalty_suspended = Ets_Loyalty::isCustomerSuspendedOrBannedLoyaltyProgram($this->context->customer->id);
            $affiliate_suspended = Ets_Affiliate::isCustomerSuspendedOrBannedAffiliateProgram($this->context->customer->id);
            if (!$loyalty_suspended || !$affiliate_suspended) {
                $id_product = (int)Tools::getValue('id_product');
                $p = new Product($id_product);
                $product = (array)$p;
                $cart = $this->context->cart;
                $count_current_product = 0;
                if ($products = $cart->getProducts()) {
                    foreach ($products as $prd) {
                        if ((int)$prd['id_product'] == (int)$id_product) {
                            $count_current_product = (int)$prd['cart_quantity'];
                            break;
                        }
                    }
                }
                $priceWithTaxWithReduct = $p->getPrice();
                $priceWithoutTaxWithReduct = $p->getPrice(false);
                $priceWithTaxWithoutReduct = $p->getPriceWithoutReduct(false);
                $priceWithoutTaxWithoutReduct = $p->getPriceWithoutReduct(true);
                $product['price_with_tax_with_reduction'] = $priceWithTaxWithReduct;
                $product['price_without_tax_with_reduction'] = $priceWithoutTaxWithReduct;
                $product['price_with_tax_without_reduction'] = $priceWithTaxWithoutReduct;
                $product['price_without_tax_without_reduction'] = $priceWithoutTaxWithoutReduct;
                $product['id_product'] = $p->id;
                $assignment = array(
                    'loyalty_suspended' => $loyalty_suspended,
                    'affiliate_suspended' => $affiliate_suspended,
                );
                $assignment['eam_product_addition_loy_message'] = '';
                $assignment['eam_product_addition_aff_message'] = '';
                if (!$loyalty_suspended) {
                    if (!Configuration::get('ETS_AM_QTY_MIN') || (int)Configuration::get('ETS_AM_QTY_MIN') > $count_current_product) {
                        $eam_product_addition_loy_message = Ets_Loyalty::getLoyaltyMessageOnProductPage($product, $this->context, $cart);
                    } else {
                        $eam_product_addition_loy_message = Ets_Loyalty::getLoyaltyMessageOnProductPage($product, $this->context);
                    }
                    $assignment['eam_product_addition_loy_message'] = $eam_product_addition_loy_message;
                }
                if (!$affiliate_suspended) {
                    $data_message = Ets_Affiliate::getAffiliateMessage($product, $this->context);
                    $eam_product_addition_aff_message = null;
                    if ($data_message && is_array($data_message)) {
                        $eam_product_addition_aff_message = $this->getAffiliateMessage($data_message);
                        $assignment['link_share'] = $data_message['link'];
                    }
                    $assignment['eam_product_addition_aff_message'] = $eam_product_addition_aff_message;
                }
                $this->smarty->assign($assignment);
                if (Configuration::get('ETS_AM_AFF_ENABLED') && $this->getCookie(EAM_AFF_PRODUCT_COOKIE)) {
                    $aff_products_cookie = explode('-', $this->getCookie(EAM_AFF_PRODUCT_COOKIE));
                    $display_aff_promo_code = false;
                    $aff_promo_code_msg = null;
                    if (in_array($product['id_product'], $aff_products_cookie) && Ets_Voucher::canAddAffiliatePromoCode($product['id_product'], $this->context->customer->id, true)) {
                        $productClass = new Product($product['id_product']);
                        if (Ets_Affiliate::productValidAffiliateProgram($productClass) && $discount_value = Ets_AM::getDiscountVoucher('aff')) {
                            $display_aff_promo_code = true;
                            $mesage_code = strip_tags(Configuration::get('ETS_AM_AFF_WELCOME_MSG', $this->context->language->id));
                            $aff_promo_code_msg = str_replace('[discount_value]', $discount_value, $mesage_code);
                        }
                    }
                    if ($display_aff_promo_code && $aff_promo_code_msg) {
                        $this->smarty->assign(array(
                            'eam_display_aff_promo_code' => true,
                            'eam_aff_promo_code_message' => $aff_promo_code_msg,
                        ));
                    }
                }
                $product_classs = new Product($id_product, false, $this->context->language->id);
                $this->smarty->assign(
                    array(
                        'product' => $product_classs,
                    )
                );
                return $this->display(__FILE__, 'product-additional.tpl');
            }
        }
    }
    public function hookActionCustomerAccountAdd($params)
    {
        $ref = null;
        if ((int)$this->context->cookie->__get(EAM_REFS)) {
            $ref = (int)$this->context->cookie->__get(EAM_REFS);
        } else {
            if (($code = Tools::getValue('eam_code_ref', false)) && Validate::isCleanHtml($code)) {
                $ref = Ets_Sponsor::checkSponsorCode($code);
                if (!$ref) {
                    $ref = null;
                } else {
                    if (Ets_Sponsor::isActive($ref)) {
                        $this->context->cookie->__set('ets_am_show_voucher_ref', (int)$this->context->customer->id);
                    }
                }
            }
        }
        $id_customer = $params['newCustomer']->id;
        $id_sponsor = 0;
        if ($this->context->cart->id) {
            $id_sponsor = Ets_Sponsor::getIdRefByCart($this->context->cart->id, $id_customer);
            if ($id_sponsor) {
                $this->context->cookie->__unset('ets_am_show_voucher_ref');
                $this->context->cookie->__unset(EAM_REFS);
            }
        }
        if (!$ref) {
            $ref = $id_sponsor;
        }
        if (Ets_Sponsor::addFriendSponsored($ref)) {
            Ets_Sponsor::getRewardWithoutOrder($ref);
        }
        $email_customer = $params['newCustomer']->email;
        Ets_Invitation::updateIdFriend($id_customer, $email_customer);
    }
    /**
     * @param $params
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function hookActionOrderStatusPostUpdate($params)
    {
        $this->_clearCache('*',$this->_getCacheId('dashboard',false));
        Ets_AM::actionWhenOrderStatusChange($params);
    }
    /**
     * @param $params
     * @return array
     * @throws Exception
     */
    public function hookPaymentOptions($params)
    {
        if (!Configuration::get('ETS_AM_AFF_ALLOW_BALANCE_TO_PAY')) {
            return;
        }
        $cart = $params['cart'];
        $cart_total = $cart->getOrderTotal(true, Cart::BOTH);
        if (Ets_AM::needExchange($this->context)) {
            $cart_total = Tools::convertPrice($cart_total, null, false);
        }
        if ($min = Configuration::get('ETS_AM_MIN_BALANCE_REQUIRED_FOR_ORDER')) {
            $min = (float)$min;
            if ($cart_total < $min) {
                return;
            }
        }
        if ($max = Configuration::get('ETS_AM_MAX_BALANCE_REQUIRED_FOR_ORDER')) {
            $max = (float)$max;
            if ($cart_total > $max) {
                return;
            }
        }
        $total_balance = Ets_Reward_Usage::getTotalBalance($this->context->customer->id);
        if (Tools::ps_round($total_balance,6) < Tools::ps_round($cart_total,6)) {
            return;
        }
        $this->smarty->assign(
            $this->getTemplateVarInfos()
        );
        $newOption = new PrestaShop\PrestaShop\Core\Payment\PaymentOption();
        $newOption->setModuleName($this->name)
            ->setCallToActionText($this->l('Pay by reward'))
            ->setAction($this->context->link->getModuleLink($this->name, 'validation', array(), true))
            ->setAdditionalInformation($this->fetch('module:ets_affiliatemarketing/views/templates/hook/payment_info.tpl'));
        $payment_options = array(
            $newOption,
        );
        return $payment_options;
    }
    public function hookPayment($params)
    {
        if (!Configuration::get('ETS_AM_AFF_ALLOW_BALANCE_TO_PAY')) {
            return;
        }
        $cart = $params['cart'];
        $cart_total = $cart->getOrderTotal(true, Cart::BOTH);
        if (Ets_AM::needExchange($this->context)) {
            $cart_total = Tools::convertPrice($cart_total, null, false);
        }
        if ($min = Configuration::get('ETS_AM_MIN_BALANCE_REQUIRED_FOR_ORDER')) {
            $min = (float)$min;
            if ($cart_total < $min) {
                return;
            }
        }
        if ($max = Configuration::get('ETS_AM_MAX_BALANCE_REQUIRED_FOR_ORDER')) {
            $max = (float)$max;
            if ($cart_total > $max) {
                return;
            }
        }
        $total_balance = Ets_Reward_Usage::getTotalBalance($this->context->customer->id);
        if (Tools::ps_round($total_balance,6) < Tools::ps_round($cart_total,6)) {
            return;
        }
        $this->smarty->assign(
            $this->getTemplateVarInfos()
        );
        return $this->display(__FILE__, 'payment.tpl');
    }
    /**
     * @return string
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function displayFooterBefore()
    {
        $output = '';
        $show_popup_banner = 0;
        if (Tools::isSubmit('action') || Tools::isSubmit('ajax'))
            return '';
        if ($this->context->customer && Ets_Sponsor::isRefferalProgramReady()) {
            if ((int)Configuration::get('ETS_AM_REF_INTRO_ENABLED')) {
                $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
                $banner = Configuration::get('ETS_AM_REF_INTRO_BANNER');
                if (!$banner) {
                    $banner = Configuration::get('ETS_AM_REF_DEFAULT_BANNER');
                }
                $banner_img = '';
                if ($banner) {
                    $banner_img = Context::getContext()->link->getMediaLink(_PS_ETS_EAM_IMG_ . $banner);
                }
                $title = Configuration::get('ETS_AM_REF_INTRO_TITLE', $default_lang);
                $content = Configuration::get('ETS_AM_REF_INTRO_CONTENT', $default_lang);
                $delay = (int)Configuration::get('ETS_AM_REF_INTRO_REDISPLAY');
                $link_ajax = $this->context->link->getModuleLink($this->name, 'exec');
                $link_ref = Ets_AM::getBaseUrlDefault('myfriend');
                $show_popup = (int)Ets_Banner::showPopupBanner($delay);
                $show_popup_banner = $show_popup;
                $this->smarty->assign(array(
                    'banner' => $banner_img,
                    'title' => $title,
                    'content' => $content,
                    'delay' => $delay,
                    'link_ref' => $link_ref,
                    'show_popup' => $show_popup,
                    'link_ajax' => $link_ajax
                ));
                $output .= $this->display(__FILE__, 'popup_referral.tpl');
            }
        }
        if ($this->context->customer->id && (int)Configuration::get('ETS_AM_REF_OFFER_VOUCHER') && Configuration::get('ETS_AM_REF_ENABLED')) {
            if (Ets_Sponsor::allowGetVoucher()) {
                $voucher = Ets_AM::generateVoucher('ref');
                if ($voucher && !$show_popup_banner) {
                    $link_ajax = $this->context->link->getModuleLink($this->name, 'exec');
                    $this->smarty->assign(array(
                        'voucher' => $voucher,
                        'show_popup_voucher' => 1,
                        'link_ajax' => $link_ajax
                    ));
                    $output .= $this->display(__FILE__, 'popup_voucher_ref.tpl');
                }
            }
        }
        if (Configuration::get('ETS_AM_AFF_ENABLED')) {
            $display_aff_promo_code = false;
            $aff_promo_code_msg = null;
            if (($aff_product = (int)Tools::getValue('affp')) && ($id_product = (int)Tools::getValue('id_product')) && Ets_Voucher::canAddAffiliatePromoCode($id_product, $aff_product, true)) {
                $product = new Product($id_product);
                if (Ets_Affiliate::productValidAffiliateProgram($product) && $discount_value = Ets_AM::getDiscountVoucher('aff')) {
                    $display_aff_promo_code = true;
                    $mesage_code = strip_tags(Configuration::get('ETS_AM_AFF_WELCOME_MSG', $this->context->language->id));
                    $aff_promo_code_msg = str_replace('[discount_value]', $discount_value, $mesage_code);
                }
            }
            if ($display_aff_promo_code && $aff_promo_code_msg) {
                $this->smarty->assign(array(
                    'eam_display_aff_promo_code' => true,
                    'eam_aff_promo_code_message' => $aff_promo_code_msg,
                ));
                $output .= $this->display(__FILE__, 'popup_voucher_aff.tpl');
            }
        }
        return $output;
    }
    public function hookActionCustomerLogoutAfter()
    {
        $this->setCookie(EAM_AFF_CUSTOMER_COOKIE, '');
        $this->setCookie(EAM_AFF_PRODUCT_COOKIE, '');
        $this->setCookie(EAM_REFS, '');
    }
    public function hookActionCartSave()
    {
        if (Configuration::get('ETS_AM_AFF_OFFER_VOUCHER') && Configuration::get('ETS_AM_AFF_ENABLED') && $this->getCookie(EAM_AFF_PRODUCT_COOKIE)) {
            $aff_products = explode('-', $this->getCookie(EAM_AFF_PRODUCT_COOKIE));
            $aff_customers = explode('-', $this->getCookie(EAM_AFF_CUSTOMER_COOKIE));
            if ($aff_products) {
                if (Configuration::get('ETS_AM_AFF_VOUCHER_TYPE') == 'FIXED') {
                    foreach ($aff_products as $key => $aff_product) {
                        $product = new Product($aff_product);
                        if (Ets_Affiliate::productValidAffiliateProgram($product) && Ets_Affiliate::isCustomerCanJoinAffiliateProgram($aff_customers[$key])) {
                            $voucher_code = Configuration::get('ETS_AM_AFF_VOUCHER_CODE');
                            $cartRule = CartRule::getCartsRuleByCode($voucher_code, $this->context->language->id);
                            if ($cartRule) {
                                $id_cart_rule = $cartRule[0]['id_cart_rule'];
                                $cartRuleClas = new CartRule($id_cart_rule);
                                if (!$cartRuleClas->checkValidity($this->context, false, true)) {
                                    $this->context->cart->addCartRule($cartRuleClas->id);
                                }
                            }
                            break;
                        }
                    }
                } else {
                    foreach ($aff_products as $key => $aff_product) {
                        $product = new Product($aff_product);
                        if (Ets_Affiliate::productValidAffiliateProgram($product) && Ets_Affiliate::isCustomerCanJoinAffiliateProgram($aff_customers[$key])) {
                            if (Ets_Voucher::canAddAffiliatePromoCode($aff_product, $aff_customers[$key]) && (!Ets_Voucher::hasOtherVoucherInCart() || Configuration::get('ETS_AM_AFF_USE_OTHER_VOUCHER'))) {
                                $promo_code = Ets_AM::generateVoucher('aff', $aff_product, 0);
                                if (isset($promo_code['id_cart_rule']) && $promo_code['id_cart_rule']) {
                                    $cartRuleClas = new CartRule($promo_code['id_cart_rule']);
                                    $this->context->cart->addCartRule($cartRuleClas->id);
                                }
                            }
                        }
                    }
                }
            }
            if($this->context->customer->isLogged())
            {
                Ets_Voucher::addVoucherToCustomer($this->context->customer->id);
                if(Configuration::get('ETS_AM_AFF_FIST_PRODUCT'))
                {
                   $aff_rules = Ets_Voucher::getCartRuleAff();
                   if($aff_rules)
                   {
                       foreach($aff_rules as $rule)
                       {
                            if(!Ets_Voucher::checkFirstRuleAff($rule['id_cart_rule'],$rule['id_product']))
                            {
                                $this->context->cart->removeCartRule($rule['id_cart_rule']);
                                $cartRule = new CartRule($rule['id_cart_rule']);
                                $cartRule->delete();
                            }
                       }
                   }
                }
            }
        }
    }
    public function hookDisplayFooter()
    {
        return $this->displayFooterBefore();
    }
    public function hookActionAuthentication()
    {
        $this->hookActionCartSave();
        if ($back = Tools::getValue('back', false)) {
            if ($back == URL_REF_PROGRAM) {
                Tools::redirect(Ets_AM::getBaseUrlDefault('myfriend'));
            } elseif ($back == URL_CUSTOMER_REWARD) {
                Tools::redirect(Ets_AM::getBaseUrlDefault('dashboard'));
            } elseif ($back == URL_AFF_PROGRAM) {
                Tools::redirect(Ets_AM::getBaseUrlDefault('affiliate'));
            } elseif ($back == URL_LOY_PROGRAM) {
                Tools::redirect(Ets_AM::getBaseUrlDefault('loyalty'));
            }
        }
    }

    public function getBaseLink()
    {
        $link = (Configuration::get('PS_SSL_ENABLED_EVERYWHERE') ? 'https://' : 'http://') . $this->context->shop->domain . $this->context->shop->getBaseURI();
        return trim($link, '/');
    }
    /**
     * @param $params
     */
    public function initToolbar($params)
    {
        $this->toolbar_btn['new'] = array(
            'href' => AdminController::$currentIndex . '&configure=' . $this->name . $params['list_id'] . '&token=' . Tools::getAdminTokenLite('AdminModules'),
            'desc' => $this->l('Add') . ' ' . Tools::strtolower($params['title']),
        );
    }
    public function hookDisplayCustomerAccountForm($params)
    {
        if ((int)Configuration::get('ETS_AM_REF_ENABLED') && !$this->context->customer->logged) {
            $ref = $this->context->cookie->__get(EAM_REFS);
            $email_sponsor = '';
            if ($ref) {
                $customer = new Customer((int)$ref);
                if ($customer) {
                    $email_sponsor = $customer->email;
                }
            }
            $this->smarty->assign(array(
                'query' => Tools::getAllValues(),
                'email_sponsor' => $email_sponsor,
                'is17' => $this->is17,
            ));
            return $this->display(__FILE__, 'reg_code_ref.tpl');
        }
        return '';
    }
    public function removeImages()
    {
        if (is_dir(EAM_PATH_IMAGE_BANER)) {
            $dir = scandir(EAM_PATH_IMAGE_BANER);
            if (!empty($dir)) {
                foreach ($dir as $file) {
                    if ($file !== '.' && $file !== '..' && $file !== 'index.php' && file_exists(EAM_PATH_IMAGE_BANER . $file)) {
                        @unlink(EAM_PATH_IMAGE_BANER . $file);
                    }
                }
            }
            Ets_affiliatemarketing::removeDir(EAM_PATH_IMAGE_BANER . 'qrcode');
        }
        return true;
    }
    public function clearLog()
    {
        if(file_exists( _PS_ETS_EAM_LOG_DIR_ . '/aff_cronjob.log'))
            @unlink(_PS_ETS_EAM_LOG_DIR_ . '/aff_cronjob.log');
        return true;
    }
    public function getAffiliateMessage($data)
    {
        $this->smarty->assign($data);
        return $this->display(__FILE__, 'affiliate_message.tpl');
    }
    public function getHtmlColum($params = array())
    {
        $this->smarty->assign($params);
        return $this->display(__FILE__, 'html_col.tpl');
    }

    public function displayInfoRunCronJob()
    {
        $cronjob_last = '';
        $run_cronjob = false;
        if ($cronjob_time = Configuration::getGlobalValue('ETS_AM_TIME_RUN_CRONJOB')) {
            $last_time = strtotime($cronjob_time);
            $time = strtotime(date('Y-m-d H:i:s')) - $last_time;
            if ($time <= 43200 && $time)
                $run_cronjob = true;
            else
                $run_cronjob = false;
            if ($time > 43200)
                $cronjob_last = '';
            elseif ($time) {
                if ($hours = floor($time / 3600)) {
                    $cronjob_last .= $hours . ' ' . $this->l('hours') . ' ';
                    $time = $time % 3600;
                }
                if ($minutes = floor($time / 60)) {
                    $cronjob_last .= $minutes . ' ' . $this->l('minutes') . ' ';
                    $time = $time % 60;
                }
                if ($time)
                    $cronjob_last .= $time . ' ' . $this->l('seconds') . ' ';
                $cronjob_last .= $this->l('ago');
            }
        }
        $this->context->smarty->assign(
            array(
                'cronjob_last' => $cronjob_last,
                'run_cronjob' => $run_cronjob,
            )
        );
        return $this->display(__FILE__,'info_cronjob.tpl');
    }
    public function generateTokenCronjob()
    {
        $code = $this->generateRandomString();
        Configuration::updateGlobalValue('ETS_AM_CRONJOB_TOKEN', $code);
    }
    public function generateRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = Tools::strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
    public function getPercentReward($params = array())
    {
        if (($filter_date_from = Tools::getValue('filter_date_from')) && Validate::isDate($filter_date_from)) {
            $params['date_from'] = $filter_date_from;
        }
        if (($filter_date_to = Tools::getValue('filter_date_to')) && Validate::isDate($filter_date_to)) {
            $params['date_to'] = $filter_date_to;
        }
        if (($filter_date_type = Tools::getValue('filter_date_type')) && Validate::isCleanHtml($filter_date_type)) {
            $params['date_type'] = $filter_date_type;
        }
        die(json_encode(Ets_AM::getPercentReward($params)));
    }
    public function getBreadcrumb()
    {
        $controller = Tools::getValue('controller');
        $node = array();
        $node[] = array(
            'title' => $this->l('Home'),
            'url' => $this->context->link->getPageLink('index', true),
        );
        $node[] = array(
            'title' => $this->l('Your account'),
            'url' => $this->context->link->getPageLink('my-account', true),
        );
        if ($controller == 'aff_products') {
            $node[] = array(
                'title' => $this->l('Affiliate program'),
                'url' => $this->context->link->getModuleLink($this->name, 'aff_products'),
            );
            $node[] = array(
                'title' => $this->l('Affiliate Products'),
                'url' => $this->context->link->getModuleLink($this->name, 'aff_products'),
            );
        }
        if ($controller == 'my_sale') {
            $node[] = array(
                'title' => $this->l('Affiliate program'),
                'url' => $this->context->link->getModuleLink($this->name, 'aff_products'),
            );
            $node[] = array(
                'title' => $this->l('My sales'),
                'url' => $this->getLinks('my_sale')
            );
            if (($id_product = (int)Tools::getValue('id_product', false))) {
                $product = new Product($id_product, false, (int)$this->context->language->id);
                $product_link = Ets_Affiliate::generateAffiliateLinkForProduct($product, $this->context, false);
                $node[] = array(
                    'title' => $product->name,
                    'url' => $product_link
                );
            } elseif (($tab_active = Tools::getValue('tab_active', false)) && $tab_active == 'statistics') {
                $node[] = array(
                    'title' => $this->l('Statistics'),
                    'url' => $this->getLinks('my_sale', array('tab_active' => 'statistics'))
                );
            }
        }
        if ($controller == 'myfriend') {
            if (($tab = Tools::getValue('tab', false)) && $tab == 'how-to-refer-friends') {
                $node[] = array(
                    'title' => $this->l('Referral program'),
                    'url' => $this->getLinks('refer_friends')
                );
                $node[] = array(
                    'title' => $this->l('How to refer friends'),
                    'url' => $this->getLinks('myfriend', array('tab' => 'how-to-refer-friends'))
                );
            } else {
                $node[] = array(
                    'title' => $this->l('Referral program'),
                    'url' => $this->getLinks('refer_friends')
                );
                $node[] = array(
                    'title' => $this->l('My friends'),
                    'url' => $this->getLinks('myfriend')
                );
            }
        }
        if ($controller == 'refer_friends') {
            $node[] = array(
                'title' => $this->l('Referral program'),
                'url' => $this->getLinks('refer_friends')
            );
            $node[] = array(
                'title' => $this->l('How to refer friends'),
                'url' => $this->getLinks('refer-friends')
            );
        }
        if ($controller == 'loyalty') {
            $node[] = array(
                'title' => $this->l('Loyalty program'),
                'url' => $this->getLinks('loyalty')
            );
        }
        if ($controller == 'register') {
            $node[] = array(
                'title' => $this->l('Register program'),
                'url' => $this->getLinks('register')
            );
        }
        if ($controller == 'dashboard') {
            $node[] = array(
                'title' => $this->l('My rewards'),
                'url' => $this->getLinks('dashboard')
            );
            $node[] = array(
                'title' => $this->l('Dashboard'),
                'url' => $this->getLinks('dashboard')
            );
        }
        if ($controller == 'history') {
            $node[] = array(
                'title' => $this->l('My rewards'),
                'url' => $this->getLinks('dashboard')
            );
            $node[] = array(
                'title' => $this->l('Reward history'),
                'url' => $this->getLinks('history')
            );
        }
        if ($controller == 'withdraw') {
            $node[] = array(
                'title' => $this->l('My rewards'),
                'url' => $this->getLinks('dashboard')
            );
            $node[] = array(
                'title' => $this->l('Withdrawals'),
                'url' => $this->getLinks('withdraw')
            );
        }
        if ($controller == 'voucher') {
            $node[] = array(
                'title' => $this->l('My rewards'),
                'url' => $this->getLinks('dashboard')
            );
            $node[] = array(
                'title' => $this->l('Convert into vouchers'),
                'url' => $this->getLinks('voucher')
            );
        }
        if ($this->is17)
            return array('links' => $node, 'count' => count($node));
        return $this->displayBreadcrumb($node);
    }
    public function getLinks($controller, $params = array())
    {
        if ($controller == 'aff_product') {
            return Ets_AM::getBaseUrlDefault('aff_product', $params);
        } elseif ($controller == 'my_sale') {
            return Ets_AM::getBaseUrlDefault('my_sale', $params);
        } elseif ($controller == 'myfriend') {
            if (!$params) {
                return Ets_AM::getBaseUrlDefault('myfriend', $params);
            } elseif ($params && isset($params['tab']) && $params['tab'] == 'how-to-refer-friends') {
                return Ets_AM::getBaseUrlDefault('myfriend', array('tab' => 'tab=how-to-refer-friends'));
            }
        } elseif ($controller == 'refer_friends')
            return Ets_AM::getBaseUrlDefault('refer_friends', $params);
        elseif ($controller == 'loyalty') {
            return Ets_AM::getBaseUrlDefault('loyalty', $params);
        } elseif ($controller == 'dashboard') {
            return Ets_AM::getBaseUrlDefault('dashboard', $params);
        } elseif ($controller == 'history') {
            return Ets_AM::getBaseUrlDefault('history', $params);
        } elseif ($controller == 'withdraw') {
            return Ets_AM::getBaseUrlDefault('withdraw', $params);
        } elseif ($controller == 'voucher') {
            return Ets_AM::getBaseUrlDefault('voucher', $params);
        } elseif ($controller == 'register') {
            return Ets_AM::getBaseUrlDefault('register', $params);
        }
        return '/';
    }
    public function displayBreadcrumb($node = array())
    {
        if ($node) {
            $this->smarty->assign(array(
                'nodes' => $node,
            ));
            return $this->display(__FILE__, 'breadcrumb.tpl');
        }
        return '';
    }
    public function saveCartRule($id_cart_rule = 0)
    {
        $languages = Language::getLanguages(false);
        if ($id_cart_rule) {
            $cartRuleObj = new CartRule($id_cart_rule);
            $cartRuleObj->active = Configuration::get('ETS_AM_SELL_OFFER_VOUCHER') ? 1 : 0;
        } else {
            $quantity = (int)Configuration::get('ETS_AM_SELL_QUANTITY') ?: 999;
            $prefix = Configuration::get('ETS_AM_SELL_DISCOUNT_PREFIX');
            $code = Ets_AM::generatePromoCode($prefix);
            $discount_in = Configuration::get('ETS_AM_SELL_APPLY_DISCOUNT_IN');
            $cartRuleObj = new CartRule();
            $cartRuleObj->quantity = $quantity;
            $cartRuleObj->code = $code;
            $cartRuleObj->date_from = date('Y-m-d H:i:s');
            $cartRuleObj->date_to = date('Y-m-d H:i:s', strtotime('+' . $discount_in . 'days', strtotime(date('Y-m-d H:i:s'))));
            foreach ($languages as $lang) {
                $cartRuleObj->name[(int)$lang['id_lang']] = Configuration::get('ETS_AM_SELL_DISCOUNT_DESC', $lang['id_lang']);
            }
            $cartRuleObj->active = 1;
            $cartRuleObj->id_customer = 0;
            $cartRuleObj->reduction_exclude_special = (int)Configuration::get('ETS_AM_SELL_EXCLUDE_SPECIAL');
        }
        $discount_percent = Configuration::get('ETS_AM_SELL_APPLY_DISCOUNT') == 'PERCENT' ? Configuration::get('ETS_AM_SELL_REDUCTION_PERCENT') : 0;
        $discount_amount = Configuration::get('ETS_AM_SELL_APPLY_DISCOUNT') == 'AMOUNT' ? Configuration::get('ETS_AM_SELL_REDUCTION_AMOUNT') : 0;
        $id_currency = Configuration::get('ETS_AM_SELL_ID_CURRENCY');
        $reduction_tax = Configuration::get('ETS_AM_SELL_REDUCTION_TAX');
        $free_shipping = Configuration::get('ETS_AM_SELL_FREE_SHIPPING');
        $voucher_min_amount = Configuration::get('ETS_AM_SELL_DISCOUNT_MIN_AMOUNT');
        $voucher_min_amount_currency = Configuration::get('ETS_AM_SELL_DISCOUNT_MIN_AMOUNT_CURRENCY');
        $voucher_min_amount_tax = Configuration::get('ETS_AM_SELL_DISCOUNT_MIN_AMOUNT_TAX');
        $voucher_min_amount_shipping = Configuration::get('ETS_AM_SELL_DISCOUNT_MIN_AMOUNT_SHIPPING');
        $cartRuleObj->quantity_per_user = 1;
        $cartRuleObj->reduction_percent = $discount_percent;
        $cartRuleObj->reduction_amount = $discount_amount;
        $cartRuleObj->reduction_currency = $id_currency;
        $cartRuleObj->reduction_product = 0;
        $cartRuleObj->reduction_tax = $reduction_tax;
        $cartRuleObj->free_shipping = $free_shipping;
        $cartRuleObj->minimum_amount = $voucher_min_amount;
        if ($voucher_min_amount) {
            $cartRuleObj->minimum_amount_tax = $voucher_min_amount_tax;
            $cartRuleObj->minimum_amount_currency = $voucher_min_amount_currency;
            $cartRuleObj->minimum_amount_shipping = $voucher_min_amount_shipping;
        }
        if ($id_cart_rule)
            $cartRuleObj->update();
        elseif (!$cartRuleObj->add())
            return false;
        if (!$id_cart_rule && $cartRuleObj->id) {
            Ets_Voucher::AddCartRuleCombination($cartRuleObj);
        }
        return $cartRuleObj;
    }
    public function hookDisplayOrderConfirmation()
    {
        if (($id_order = (int)Tools::getValue('id_order')) && ($order = new Order($id_order)) && Validate::isLoadedObject($order) && $order->id_customer == Context::getContext()->customer->id && ($reward = Ets_AM::getRewardByIDOrder($id_order, 'loy'))) {
            $reward['status'] = trim($this->getStatus($reward['status']));
            $msg = Configuration::get('ETS_AM_LOYALTY_MSG_ORDER', $this->context->language->id);
            if(($orders = Ets_AM::getOtherOrder($id_order,$order->reference)))
            {
                foreach($orders as $o)
                {
                    if(($r = Ets_AM::getRewardByIDOrder($o['id_order'], 'loy')))
                    {
                        $reward['total_amount'] += $r['total_amount'];
                    }
                }
            }
            return str_replace(array('[amount]', '[reward_status]'), array(Configuration::get('ETS_AM_REWARD_DISPLAY') == 'point' ? Ets_AM::displayReward($reward['total_amount']) : Ets_affiliatemarketing::displayPrice(Tools::convertPrice($reward['total_amount'])), $reward['status']), $msg);
        }
    }
    public function getStatus($status)
    {
        switch ($status) {
            case 0:
                return EtsAffDefine::displayText($this->l('Pending'), 'span', 'loy_status pending');
            case 1:
                return EtsAffDefine::displayText($this->l('Approved'), 'span', 'loy_status approved');
            case -1:
                return EtsAffDefine::displayText($this->l('Canceled'), 'span', 'loy_status canceled');
            case -2:
                return EtsAffDefine::displayText($this->l('Expired'), 'span', 'loy_status expired');
        }
        return '';
    }
    public function getPopupDefault()
    {
        return $this->display(__FILE__, 'ref_popup_default_content.tpl');
    }
    public function displaySuccessMessage($msg, $title = false, $link = false)
    {
        $this->smarty->assign(array(
            'msg' => $msg,
            'title' => $title,
            'link' => $link
        ));
        if ($msg)
            return $this->display(__FILE__, 'success_message.tpl');
    }
    public static function displayPrice($price, $currency = null)
    {
        if(!is_object($currency) && $currency && Validate::isInt($currency))
            $currency = (int)$currency;
        else
            $currency = null;
        return Tools::displayPrice($price ? Tools::ps_round($price,2):0, $currency);
    }
    public static function getContextLocale(Context $context)
    {
        $locale = $context->getCurrentLocale();
        if (null !== $locale) {
            return $locale;
        }
        $container = isset($context->controller) ? $context->controller->getContainer() : null;
        if (null === $container) {
            $container = call_user_func(array('SymfonyContainer', 'getInstance'));
        }
        /** @var LocaleRepository $localeRepository */
        $localeRepository = $container->get(self::SERVICE_LOCALE_REPOSITORY);
        $locale = $localeRepository->getLocale(
            $context->language->getLocale()
        );
        return $locale;
    }
    public function hookActionFrontControllerAfterInit()
    {
        if (($code = Tools::getValue('discount_name')) && (Tools::getValue('controller') == 'cart' || Tools::getValue('controller') == 'order') && Tools::isSubmit('addDiscount') && (Tools::isSubmit('ajax') || Tools::isSubmit('ajax_request')))
            Ets_Voucher::getInstance()->checkCartRuleValidity($code);
    }
    public function addOverride($classname)
    {
        if (Module::isInstalled('ets_abandonedcart') && $classname == 'CartRule')
            return true;
        return parent::addOverride($classname);
    }
    public function removeOverride($classname)
    {
        if (Module::isInstalled('ets_abandonedcart') && $classname == 'CartRule')
            return true;
        return parent::removeOverride($classname);
    }
    public function renderCategoryTree($params)
    {
        $tree = new HelperTreeCategories($params['tree']['id'], isset($params['tree']['title']) ? $params['tree']['title'] : null);
        if (isset($params['name'])) {
            $tree->setInputName($params['name']);
        }
        if (isset($params['tree']['selected_categories'])) {
            $tree->setSelectedCategories($params['tree']['selected_categories']);
        }
        if (isset($params['tree']['disabled_categories'])) {
            $tree->setDisabledCategories($params['tree']['disabled_categories']);
        }
        if (isset($params['tree']['root_category'])) {
            $tree->setRootCategory($params['tree']['root_category']);
        }
        if (isset($params['tree']['use_search'])) {
            $tree->setUseSearch($params['tree']['use_search']);
        }
        if (isset($params['tree']['use_checkbox'])) {
            $tree->setUseCheckBox($params['tree']['use_checkbox']);
        }
        if (isset($params['tree']['set_data'])) {
            $tree->setData($params['tree']['set_data']);
        }
        return $tree->render();
    }
    public function getTextLang($text, $lang, $file_name = '')
    {
        if (is_array($lang))
            $iso_code = $lang['iso_code'];
        elseif (is_object($lang))
            $iso_code = $lang->iso_code;
        else {
            $language = new Language($lang);
            $iso_code = $language->iso_code;
        }
        $modulePath = rtrim(_PS_MODULE_DIR_, '/') . '/' . $this->name;
        $fileTransDir = $modulePath . '/translations/' . $iso_code . '.' . 'php';
        if (!@file_exists($fileTransDir)) {
            return $text;
        }
        $fileContent = Tools::file_get_contents($fileTransDir);
        $text_tras = preg_replace("/\\\*'/", "\'", $text);
        $strMd5 = md5($text_tras);
        $keyMd5 = '<{' . $this->name . '}prestashop>' . ($file_name ?: $this->name) . '_' . $strMd5;
        preg_match('/(\$_MODULE\[\'' . preg_quote($keyMd5) . '\'\]\s*=\s*\')(.*)(\';)/', $fileContent, $matches);
        if ($matches && isset($matches[2])) {
            return $matches[2];
        }
        return $text;
    }
    public static function validateArray($array, $validate = 'isCleanHtml')
    {
        if (!is_array($array))
            return false;
        if (method_exists('Validate', $validate)) {
            if ($array && is_array($array)) {
                $ok = true;
                foreach ($array as $val) {
                    if (!is_array($val)) {
                        if ($val && !Validate::$validate($val)) {
                            $ok = false;
                            break;
                        }
                    } else
                        $ok = self::validateArray($val, $validate);
                }
                return $ok;
            }
        }
        return true;
    }
    public static function isImageName($name)
    {
        $allowedTypes = array('png', 'jpg', 'jpeg', 'gif');
        return Validate::isString($name) && $name != '' && in_array(Tools::substr(strrchr($name, '.'), 1), $allowedTypes) && in_array(pathinfo($name, PATHINFO_EXTENSION), $allowedTypes) ? true : false;
    }
    public static function removeDir($dir)
    {
        $dir = rtrim($dir, '/');
        if ($dir && is_dir($dir)) {
            if ($objects = scandir($dir)) {
                foreach ($objects as $object) {
                    if ($object != "." && $object != "..") {
                        if (is_dir($dir . "/" . $object) && !is_link($dir . "/" . $object))
                            self::removeDir($dir . "/" . $object);
                        else
                            @unlink($dir . "/" . $object);
                    }
                }
            }
            rmdir($dir);
        }
    }
    public static function copyDir($src, $dst)
    {
        if (!file_exists($src))
            return true;
        $dir = opendir($src);
        if (!is_dir($dst))
            @mkdir($dst);
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src . '/' . $file)) {
                    self::copyDir($src . '/' . $file, $dst . '/' . $file);
                } elseif (!file_exists($dst . '/' . $file)) {
                    @copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }
    public static function makeCacheDir()
    {
        $cacheDir = _PS_CACHE_DIR_ . 'ets_affiliatemarketing/';
        if (!is_dir($cacheDir))
            @mkdir($cacheDir, 0755, true);
    }
    public static function registerPlugins(){
        if(version_compare(_PS_VERSION_, '8.0.4', '>='))
        {
            $smarty = Context::getContext()->smarty->_getSmartyObj();
            if(!isset($smarty->registered_plugins[ 'modifier' ][ 'implode' ]))
                Context::getContext()->smarty->registerPlugin('modifier', 'implode', 'implode');
            if(!isset($smarty->registered_plugins[ 'modifier' ][ 'strpos' ]))
                Context::getContext()->smarty->registerPlugin('modifier', 'strpos', 'strpos');
        }
    }
    public function _getCacheId($params = null,$parentID = true)
    {
        $cacheId = $this->getCacheId($this->name);
        $cacheId = str_replace($this->name, '', $cacheId);
        $suffix ='';
        if($params)
        {
            if(is_array($params))
                $suffix .= '|'.implode('|',$params);
            else
                $suffix .= '|'.$params;
        }
        return $this->name . $suffix .($parentID ? $cacheId:'');
    }
    public function _clearCache($template,$cache_id = null, $compile_id = null)
    {
        if($cache_id===null)
            $cache_id = $this->name;
        if($template=='*')
        {
            Tools::clearCache(Context::getContext()->smarty,null, $cache_id, $compile_id);
        }
        else
        {
            Tools::clearCache(Context::getContext()->smarty, $this->getTemplatePath($template), $cache_id, $compile_id);
        }
    }
}
