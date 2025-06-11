<?php
/**
 * Cart Reminder
 *
 *    @author    EIRL Timactive De Véra
 *    @copyright Copyright (c) TIMACTIVE 2015 -EIRL Timactive De Véra
 *    @license   Commercial license
 *
 *    @category pricing_promotion
 *
 *    @version 1.1.0
 *************************************
 **         CART REMINDER            *
 **          V 1.0.0                 *
 *
 * |_   _(_)          / _ \     | | (_)
 *   | |  _ _ __ ___ / /_\ \ ___| |_ ___   _____
 *   | | | | '_ ` _ \|  _  |/ __| __| \ \ / / _ \
 *   | | | | | | | | | | | | (__| |_| |\ V /  __/
 *   \_/ |_|_| |_| |_\_| |_/\___|\__|_| \_/ \___|
 *
 * +
 * + Languages: EN, FR
 * + PS version: 1.5,1.6
 *
 * Admin Main Controller class use for all tab
 * Listing Abandonned cart / all actions
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class AdminLiveCartReminderController extends ModuleAdminController
{
    /**
     * @var string
     *             1 cart(abandonned cart without reminder)
     *             2 manual(only manual reminder to do)
     *             3 running(only abandonned cart with remind but not finished
     *             4 finished(all reminder finished)
     */
    private $tab_select;

    /**
     * AdminController::__construct() override
     *
     * @see AdminController::__construct()
     * the list select join depend at tab_select
     */
    public function __construct()
    {
        if (version_compare(_PS_VERSION_, '1.7.0', '>=') === true) {
            $this->translator = Context::getContext()->getTranslator();
        }
        $this->tab_select = Tools::getValue('tab_select', 'cart');
        $this->bootstrap = true;
        if (!$this->module) {
            $this->module = Module::getInstanceByName('tacartreminder');
        }
        switch ($this->tab_select) {
            case 'unsubscribes':
                $this->toolbar_title = $this->l('Unsubscribed Customer(s)');
                $this->table = 'ta_cartreminder_customer_unsubscribe';
                $this->className = 'TACartReminderCustomerUnsubscribe';
                $this->identifier = 'id_unsubscribe';
                $this->_select = 'a.id_unsubscribe, a.id_customer,
                    CONCAT(LEFT(c.`firstname`, 1), \'. \', c.`lastname`) `customer`, c.`email`';
                $this->_join = 'INNER JOIN ' . _DB_PREFIX_ . 'customer c ON (c.id_customer = a.id_customer)';
                $this->fields_list = [
                    'id_customer' => [
                        'title' => $this->l('ID'),
                        'align' => 'text-center',
                        'class' => 'fixed-width-xs',
                    ],
                    'customer' => [
                        'title' => $this->l('Customer'),
                        'filter_key' => 'c!lastname',
                    ],
                    'email' => [
                        'title' => $this->l('Email'),
                        'filter_key' => 'c!email',
                    ],
                    'date_upd' => [
                        'title' => $this->l('Date'),
                        'align' => 'text-right',
                        'type' => 'datetime',
                        'filter_key' => 'a!date_upd',
                    ],
                ];
                $this->addRowAction('delete');
                $this->_orderBy = 'id_unsubscribe';
                $this->_orderWay = 'DESC';
                break;
            case 'stats':
                break;
            default:
                $alias_cart = 'a';
                if ($this->tab_select == 'cart') {
                    $this->table = 'cart';
                    $this->className = 'Cart';
                    $this->identifier = 'id_cart';
                } else {
                    $this->table = 'ta_cartreminder_journal';
                    $this->className = 'TACartReminderJournal';
                    $this->identifier = 'id_journal';
                    $alias_cart = 'car';
                }
                $this->_group = ' GROUP BY a.' . $this->identifier;
                $this->_select = 'a.id_cart,' . $alias_cart .
                    '.date_upd as cart_date_upd,CONCAT(LEFT(c.`firstname`, 1), \'. \', c.`lastname`) `customer`,
                    c.id_customer, a.id_cart as relance,a.id_cart total,
                    ca.name carrier, GROUP_CONCAT( cou.`iso_code` SEPARATOR \' \' ) as iso_codes,
                    GROUP_CONCAT( addr.`phone` SEPARATOR \',\' ) as phones,
                    GROUP_CONCAT( addr.`phone_mobile` SEPARATOR \',\' ) as phone_mobiles' .
                    (($this->tab_select == 'running'
                        || $this->tab_select == 'finished'
                        || $this->tab_select == 'manual') ?
                        ',a.id_journal,a.id_journal as message_id_journal,a.id_order' : '');
                $this->_orderWay = 'DESC';
                $this->_join = ' INNER JOIN ' . _DB_PREFIX_ . 'customer c ON (c.id_customer = a.id_customer)' .
                    ($this->tab_select == 'running'
                    || $this->tab_select == 'finished'
                    || $this->tab_select == 'manual' ?
                        ' LEFT JOIN ' . _DB_PREFIX_ . 'cart car ON ' . $alias_cart . '.id_cart=a.id_cart' : '') . ' ' .
                    ($this->tab_select == 'cart' ?
                        ' LEFT JOIN ' . _DB_PREFIX_ . 'ta_cartreminder_journal j ON j.id_cart = a.id_cart
                        LEFT JOIN `' . _DB_PREFIX_ . 'ta_cartreminder_journal` jrunning ON jrunning.`email` = c.`email` AND jrunning.`state` = \'RUNNING\'' : '') . '
							' .
                    ($this->tab_select == 'manual' ?
                        ' INNER JOIN ' . _DB_PREFIX_ . 'ta_cartreminder_journal_reminder jr
                        ON jr.id_journal = a.id_journal' : '') .
                    ' LEFT JOIN ' . _DB_PREFIX_ . 'address addr ON (addr.id_customer = c.id_customer)
                    LEFT JOIN ' . _DB_PREFIX_ . 'country cou ON (cou.id_country = addr.id_country)
                    LEFT JOIN ' . _DB_PREFIX_ . 'currency cu ON (cu.id_currency = ' . $alias_cart . '.id_currency)
                    LEFT JOIN ' . _DB_PREFIX_ . 'carrier ca ON (ca.id_carrier = ' . $alias_cart . '.id_carrier)
                    LEFT JOIN ' . _DB_PREFIX_ . 'orders o ON (o.id_cart = ' . $alias_cart . '.id_cart)';
                if ($this->tab_select == 'finished' || $this->tab_select == 'running' || $this->tab_select == 'manual') {
                    $this->_select .= ', osl.name as order_state_name, os.color as order_state_color';
                    $this->_join .= '
                    LEFT JOIN ' . _DB_PREFIX_ . 'order_state os
                        ON (os.id_order_state = o.current_state)
                    LEFT JOIN ' . _DB_PREFIX_ . 'order_state_lang osl
                        ON (osl.id_order_state = os.id_order_state AND osl.id_lang=' .
                                            (int) Context::getContext()->language->id . ')';
                }
                $this->fields_list = [
                    'id_cart' => [
                        'title' => $this->l('ID'),
                        'align' => 'text-center',
                        'class' => 'fixed-width-xs',
                        'filter_key' => 'a!id_cart',
                        'callback' => 'getIdInfo',
                    ],
                    'customer' => [
                        'title' => $this->l('Customer'),
                        'filter_key' => 'c!lastname',
                        'callback' => 'getCustomerInfo',
                    ],
                    'iso_codes' => [
                        'title' => $this->l('Country'),
                        'callback' => 'getIsoCode',
                        'filter_key' => 'cou!iso_code',
                        'class' => 'fixed-width-xs',
                    ],
                    'id_order' => [
                        'title' => $this->l('Order'),
                        'filter_key' => 'a!id_order',
                        'align' => 'text-center',
                        'callback' => 'printOrder',
                        'badge_success' => true,
                    ],
                    'order_state_name' => [
                        'title' => $this->l('Order State'),
                        'filter_key' => 'osl!name',
                        'align' => 'text-center',
                        'callback' => 'printOrderState',
                    ],
                    'total' => [
                        'title' => $this->l('Total'),
                        'callback' => 'getOrderTotalUsingTaxCalculationMethod',
                        'orderby' => false,
                        'search' => false,
                        'align' => 'text-right',
                        'badge_success' => true,
                    ],
                    'relance' => [
                        'title' => $this->l('Reminder'),
                        'callback' => 'getReminders',
                        'filter_key' => 'a!rule_name',
                        'orderby' => false,
                    ],
                    'message_id_journal' => [
                        'title' => $this->l('Messages'),
                        'callback' => 'getMessages',
                        'align' => 'text-center',
                        'orderby' => false,
                        'search' => false,
                    ],
                    'cart_date_upd' => [
                        'title' => $this->l('Date'),
                        'align' => 'text-right',
                        'type' => 'datetime',
                        'filter_key' => $alias_cart . '!date_upd',
                    ],
                ];
                $this->shopLinkType = 'shop';
                $this->bulk_actions = [
                    'delete' => [
                        'text' => $this->l('Delete selected'),
                        'confirm' => $this->l('Delete selected items?'),
                        'icon' => 'icon-trash',
                    ],
                ];
        }
        $this->lang = false;
        $this->explicitSelect = true;
        $this->list_no_link = true;
        if ($this->tab_select == 'cart') {
            unset($this->fields_list['message_id_journal']);
            unset($this->fields_list['id_order']);
            unset($this->fields_list['order_state_name']);
            unset($this->fields_list['relance']['filter_key']);
            $this->fields_list['relance']['search'] = false;
            $this->bulk_actions = [];
        }
        if ($this->tab_select == 'running' || $this->tab_select == 'manual') {
            unset($this->fields_list['order_state_name']);
        }
        parent::__construct();
    }

    /**
     * @see AdminController::initContent
     */
    public function initContent()
    {
        $this->context->smarty->assign([
            'ta_cr_tab_select' => $this->tab_select,
            'currency' => $this->context->currency,
        ]);
        $this->tpl_list_vars['ta_cr_tab_select'] = $this->tab_select;
        $this->tpl_list_vars['base_url'] = preg_replace('#&tab_select=.*#', '', self::$currentIndex) .
            '&token=' . $this->token;
        $access = Profile::getProfileAccess(
            $this->context->employee->id_profile,
            (int) Tab::getIdFromClassName('AdminLiveCartReminder')
        );
        /**if ($access['view'] === '1' && Tools::isSubmit('render_process_manual')) {
            exit($this->renderProcessManualView());
        }**/
        if ($this->tab_select == 'stats') {
            if (!$this->viewAccess()) {
                $this->errors[] = Tools::displayError('You do not have permission to view this.');

                return;
            }
            $this->getLanguages();
            $this->initToolbar();
            if (version_compare(_PS_VERSION_, '1.6.0', '>=') === true) {
                if (version_compare(_PS_VERSION_, '8.0.0', '<') === true) {
                    $this->initTabModuleList();
                    $this->content .= $this->renderModulesList();
                    $this->content .= $this->renderKpis();
                }
                $this->initPageHeaderToolbar();
            }
            $this->content .= $this->renderList();
            $this->content .= $this->renderStats();
            $this->content .= $this->renderOptions();
            $this->context->smarty->assign([
                'tab_select',
                $this->tab_select,
                'content' => $this->content,
                'lite_display' => $this->lite_display,
                'url_post' => self::$currentIndex . '&token=' . $this->token,
            ]);
            if (version_compare(_PS_VERSION_, '1.6.0', '>=') === true) {
                $this->context->smarty->assign([
                    'page_header_toolbar_btn' => $this->page_header_toolbar_btn,
                    'show_page_header_toolbar' => $this->show_page_header_toolbar,
                    'page_header_toolbar_title' => $this->page_header_toolbar_title,
                    'title' => $this->page_header_toolbar_title,
                    'toolbar_btn' => $this->page_header_toolbar_btn,
                ]);
            } else {
                $this->context->smarty->assign([
                    'toolbar_btn' => $this->toolbar_btn,
                ]);
            }

            $this->content .= $this->context->smarty->fetch(parent::getTemplatePath() . 'footer-module.tpl');
        } else {
            parent::initContent();
        }
    }

    /**
     * Set title corresponding at the tab selected by user
     *
     * @see AdminController::initToolbarTitle
     */
    public function initToolbarTitle()
    {
        switch ($this->tab_select) {
            case 'cart':
                $this->toolbar_title = $this->l('Cart checking');
                break;
            case 'cart':
                $this->toolbar_title = $this->l('Cart reminder manual to do');
                break;
            case 'running':
                $this->toolbar_title = $this->l('Cart reminder running');
                break;
            case 'finished':
                $this->toolbar_title = $this->l('Cart reminder finished or cancelled');
                break;
            case 'unsubscribes':
                $this->toolbar_title = $this->l('Unsubscribed Customer(s)');
                break;
            case 'stats':
                $this->toolbar_title = $this->l('Statistic');
                break;
            default:
                parent::initToolbarTitle();
        }
    }

    /**
     * Check if employee have access and set action
     *
     * @see AdminController::initProcess
     */
    public function initProcess()
    {
        parent::initProcess();
        $access = Profile::getProfileAccess(
            $this->context->employee->id_profile,
            (int) Tab::getIdFromClassName('AdminLiveCartReminder')
        );
        if ($access['view'] === '1' && ($action = Tools::getValue('submitAction'))) {
            $this->action = $action;
        }
    }

    /**
     * Remove all action button not use
     *
     * @see AdminController::initToolbar
     */
    public function initToolbar()
    {
        parent::initToolbar();
        unset($this->toolbar_btn['new']);
    }

    /**
     * @see AdminController::setMedia
     */
    public function setMedia($isNewtheme = false)
    {
        if (version_compare(_PS_VERSION_, '1.7.0', '<')) {
            parent::setMedia();
        } else {
            parent::setMedia($isNewtheme);
        }
        $this->addJqueryUI('ui.datepicker');
        $this->addJqueryPlugin([
            'typewatch',
            'fancybox',
            'autocomplete',
            'date',
        ]);
        $this->addCSS([
            _MODULE_DIR_ . 'tacartreminder/views/css/icons/flaticon.css',
            _MODULE_DIR_ . 'tacartreminder/views/css/flipclock.css',
            _MODULE_DIR_ . 'tacartreminder/views/css/admin.css',
            _MODULE_DIR_ . 'tacartreminder/views/css/admin-ta-common.css',
            _MODULE_DIR_ . 'tacartreminder/views/css/vendor/nv.d3.css',
            _MODULE_DIR_ . 'tacartreminder/views/css/vendor/introjs.min.css',
            _MODULE_DIR_ . 'tacartreminder/views/css/vendor/introjs-rtl.min.css',
        ]);
        $tiny_setup_js = _PS_JS_DIR_ . 'admin/tinymce.inc.js';
        if (version_compare(_PS_VERSION_, '1.6.0.12', '<') === true) {
            $tiny_setup_js = _PS_JS_DIR_ . 'tinymce.inc.js';
        }
        $this->addJS([
            _PS_JS_DIR_ . 'tiny_mce/tiny_mce.js',
            $tiny_setup_js,
            _PS_JS_DIR_ . 'date.js',
            _MODULE_DIR_ . 'tacartreminder/views/js/flipclock.min.js',
            _MODULE_DIR_ . 'tacartreminder/views/js/admin_live_cart_reminder.js',
            _MODULE_DIR_ . 'tacartreminder/views/js/vendor/d3.v3.min.js',
            _MODULE_DIR_ . 'tacartreminder/views/js/vendor/nv.d3.min.js',
            _MODULE_DIR_ . 'tacartreminder/views/js/vendor/intro.min.js',
        ]);
        if (version_compare(_PS_VERSION_, '1.6.0', '<') === true) {
            $this->context->controller->addJqueryUI([
                'ui.core',
                'ui.widget',
                'ui.slider',
                'ui.datepicker',
            ]);
            $this->addJS(_MODULE_DIR_ . 'tacartreminder/views/js/ta-ps15.js');
            $this->addCSS(_MODULE_DIR_ . 'tacartreminder/views/css/admin-ta-commonps15.css');
            $this->addJS(_PS_JS_DIR_ . 'jquery/plugins/timepicker/jquery-ui-timepicker-addon.js');
            $this->addCSS(_PS_JS_DIR_ . 'jquery/plugins/timepicker/jquery-ui-timepicker-addon.css');
        }
        if ($this->tab_select == 'stats') {
            $this->addJS([
                _MODULE_DIR_ . 'tacartreminder/views/js/admin_live_cart_reminder_stats.js',
            ]);
        }
    }

    public function init()
    {
        parent::init();
        self::$currentIndex = self::$currentIndex . '&tab_select=' . $this->tab_select;
    }

    /**
     * Render list correspondant at tab select,
     * the field select is different at
     *
     * @return string
     */
    public function renderList()
    {
        switch ($this->tab_select) {
            case 'cart':
                $this->_where .= ' AND o.id_order is null AND j.id_journal IS NULL
							AND a.date_upd > DATE_SUB(NOW(),INTERVAL ' .
                    Configuration::get('TA_CARTR_STOPREMINDER_NB_HOUR') . ' HOUR)
                    AND a.date_upd >
                    (SELECT IFNULL(DATE_FORMAT(MAX(j2.date_upd_cart),\'%Y-%m-%d %H:%i:%s\'),\'0000-00-00 00:00:00\')
                    FROM `' . _DB_PREFIX_ . 'ta_cartreminder_journal` j2
                    WHERE j2.`email`=c.`email` AND j2.id_shop = a.id_shop)
                    AND a.id_cart =
                    (SELECT id_cart FROM `' . _DB_PREFIX_ . 'cart` c3
                    INNER JOIN `' . _DB_PREFIX_ . 'customer` cust2 ON cust2.`id_customer` = c3.`id_customer`
                    WHERE cust2.`email` = c.`email`
			        AND c3.`id_shop` = a.`id_shop`
			        ORDER BY c3.`date_upd`
                    DESC LIMIT 1)';
                break;
            case 'manual':
                $this->_where .= 'AND o.id_order is null AND a.`state` = \'RUNNING\'
						AND jr.`manual_process`=1 and (jr.`date_performed` <= \'0000-00-00 00:00:00\'
						OR jr.`date_performed` IS NULL)';
                break;
            case 'running':
                $this->_where .= 'AND o.id_order is null AND a.`state` = \'RUNNING\'';
                break;
            case 'finished':
                $this->_where .= 'AND (o.id_order is not null OR (a.`state` = \'FINISHED\'
                OR a.`state` = \'CANCELED\'))';
                break;
        }
        $iso = $this->context->language->iso_code;
        $html_list = parent::renderList();
        $this->context->smarty->assign([
            'ps_version' => _PS_VERSION_,
            'ad' => __PS_BASE_URI__ . basename(_PS_ADMIN_DIR_),
            'count_manual' => TACartReminderJournal::getManualToDo(true),
            'tinymce' => true,
            'list_total_result' => (int) $this->_listTotal,
            'path_css' => _THEME_CSS_DIR_,
            'iso' => file_exists(_PS_ROOT_DIR_ . '/js/tiny_mce/langs/' . $iso . '.js') ? $iso : 'en',
        ]);
        $html = '';
        $html .= $this->context->smarty->fetch(parent::getTemplatePath() . 'live_cart_reminder/header.tpl');
        $html .= '<div id="fancy-cart-reminder"/>';
        $html .= $html_list;
        $this->context->smarty->assign('tab_select', $this->tab_select);
        if ($this->tab_select != 'stats') {
            $html .= $this->context->smarty->fetch(parent::getTemplatePath() . 'footer-module.tpl');
        }

        return $html;
    }

    public function processDelete()
    {
        self::$currentIndex = self::$currentIndex . '&tab_select=' . $this->tab_select;

        return parent::processDelete();
    }

    /**
     * Render callback total order with tax include
     *
     * @param $id_cart
     * @param $cart_row
     *
     * @return string
     */
    public static function getOrderTotalUsingTaxCalculationMethod($id_cart, $cart_row)
    {
        $module_instance = new TACartReminder();
        $context = Context::getContext();
        $cart = new Cart($id_cart);
        if (Validate::isLoadedObject($cart) && $cart->id) {
            if (version_compare(_PS_VERSION_, '1.5.4', '<') === true) {
                Shop::initialize();
            }
            $context->cart = new Cart($id_cart);
            $context->currency = new Currency((int) $context->cart->id_currency);
            $context->customer = new Customer((int) $context->cart->id_customer);
            if (isset($cart_row['id_order']) && (int) $cart_row['id_order'] > 0) {
                return '<span class="ta-badge ta-badge-success" style="font-size: 14px;">' .
                Cart::getTotalCart($id_cart, true, Cart::BOTH_WITHOUT_SHIPPING) .
                '</span>';
            }

            return Cart::getTotalCart($id_cart, true, Cart::BOTH_WITHOUT_SHIPPING);
        }

        return '<span class="ta-badge ta-badge-danger" >' . $module_instance->l('Cart deleted') . '</span>';
    }

    /**
     * Render Order callback display in list
     *
     * @param $id_order
     * @param $row
     *
     * @return string
     */
    public function printOrder($id_order, $row)
    {
        if ($id_order && (int) $id_order) {
            return '<span class="ta-badge ta-badge-success" style="font-size: 14px;">' .
            '<i class="flaticon-check33"></i> #' . $id_order . '</span>';
        }

        return '<span class="ta-badge">' . $this->l('Not ordered') . '</span>';
    }

    /**
     * Render Order State callback display in list
     *
     * @param $id_order
     * @param $row
     *
     * @return string
     */
    public function printOrderState($id_order_state, $row)
    {
        if (isset($row['id_order']) && (int) $row['id_order']) {
            return '<span class="label color_field" style="background-color:' .
                $row['order_state_color'] . ';color:white">' . $row['order_state_name'] . '</span>';
        }

        return '--';
    }

    public function getIdInfo($id)
    {
        return $id;
    }

    /**
     * Render callback display customer information
     *
     * @param $name
     * @param $cart_row
     *
     * @return string
     */
    public function getCustomerInfo($name, $cart_row)
    {
        $str_mobiles = $cart_row['phone_mobiles'];
        $phone_mobiles = array_unique(explode(',', $str_mobiles));
        $str_phones = $cart_row['phones'];
        $phones = array_unique(explode(',', $str_phones));
        $link_customer = $this->context->link->getAdminLink('AdminCustomers') . '&id_customer=' .
            $cart_row['id_customer'] . '&viewcustomer';
        $html = '<a href="' . $link_customer . '" target="_blank">' . $name . '<br/>';
        // $html = $name . '<br/>';
        foreach ($phone_mobiles as $phone_mobile) {
            if (!empty($phone_mobile) && !empty($phone_mobile)) {
                $html .= '<i class="icon-mobile-phone"></i> ' . $phone_mobile . '<br/>';
            }
        }
        foreach ($phones as $phone) {
            if (!empty($phone) && !empty($phone)) {
                $html .= '<i class="icon-phone"></i> ' . $phone . '<br/>';
            }
        }
        $html .= '</a>';

        return $html;
    }

    /**
     * Render callback messages by journal display in list
     *
     * @param $id_journal
     * @param $cart_row
     *
     * @return string
     */
    public function getMessages($id_journal, $cart_row)
    {
        $nb_message = TACartReminderMessage::getMessages((int) $id_journal, null, null, true);
        $html = '';
        $this->context->smarty->assign([
            'id_journal' => (int) $id_journal,
            'nb_message' => $nb_message,
        ]);
        $html .= $this->context->smarty->fetch(
            parent::getTemplatePath() . 'live_cart_reminder/journal-message-summary.tpl'
        );

        return $html;
    }

    /**
     * Display a reminder in html by $id_cart & $cart_row
     *
     * @param $id_cart
     * @param $cart_row
     *
     * @return string
     *
     * @throws PrestaShopException
     */
    public function getReminders($id_cart, $cart_row)
    {
        $html = '';
        // select row
        $cart = new Cart((int) $id_cart);
        $journal = TACartReminderJournal::getWithCart((int) $id_cart);
        $cart_id_shop = (((int) !$journal->id) ? $cart->id_shop : $journal->id_shop);
        $cart_reminder_to_close = false;
        if (!$journal->id) {
            $rule = TACartReminderRule::getApplicableRule((int) $id_cart, $journal, false);
        } else {
            $rule = new TACartReminderRule((int) $journal->id_rule);
            if ($journal->state == 'RUNNING'
                && (!Validate::isLoadedObject($cart) || !TACartReminderRule::isApplicableRule($rule, $id_cart))
            ) {
                $cart_reminder_to_close = true;
            }
        }
        if (($rule && $rule->id) || $journal->id) {
            $html .= '<table id="live-reminders-table">';
            $html .= '<tr class="rule-title">';
            $html .= '<td colspan="2">
					<span class="ta-reminders-openorclose taopened flaticon-add133" data-id-cart="' . $id_cart . '">' .
                '</span>' . ($journal->id ? $journal->rule_name : $rule->name) .
                (!$rule->id || !Validate::isLoadedObject($rule) ?
                    '<br/>' . $this->l('This rule has been deleted') : '') . '</td>';
            $html .= '<td>' . ($rule->id && Validate::isLoadedObject($rule) ?
                    '<a href="javascript:;" data-id-cart="' . $id_cart . '" class="check_reminder_rules" >
					<i class="flaticon-seo5" style="font-size:17px"></i></a>' : '');
            $html .= '</tr>';
        } else {
            $html .= $this->l('No rule applicable') . '&nbsp;<a href="javascript:;" data-id-cart="' . $id_cart .
                '" class="check_reminder_rules" ><i class="flaticon-seo5" style="font-size:17px"></i></a>';
        }
        // règle toujours existante
        if ((($journal && $journal->state != 'FINISHED' && $journal->state != 'CANCELED') || !$journal) && $rule
            && Validate::isLoadedObject($rule) && $rule->id) {
            $reminders = $rule->getReminders();
            $time_now = time();
            $time_compare = strtotime((string) $cart_row['cart_date_upd']) +
                ((60 * 60) * (float) Configuration::get('TA_CARTR_ABANDONNED_NB_HOUR', null, null, $cart_id_shop)
                );
            $launch_next_reminder = true;
            foreach ($reminders as $reminder) {
                $to_accomplish = false;
                $journal_reminder = false;
                $to_launch = false;
                $nbsecond = 0;
                if ($journal->id) {
                    $journal_reminder = $journal->getJournalReminder((int) $reminder['id_reminder']);
                }
                if ($launch_next_reminder && !$journal_reminder) {
                    $to_launch = true;
                    $nbsecond = ($time_compare + ((float) $reminder['nb_hour'] * 3600)) - $time_now;
                    $launch_next_reminder = false;
                } elseif ($journal_reminder && (int) $journal_reminder['manual_process']
                    && !(int) $journal_reminder['performed']) {
                    $to_accomplish = true;
                    $launch_next_reminder = false;
                }
                if ($journal_reminder) {
                    $time_compare = strtotime((string) $journal_reminder['date_performed']);
                }
                $this->context->smarty->assign([
                    'reminder' => $reminder,
                    'id_cart' => $id_cart,
                    'nbsecond' => $nbsecond,
                    'journal_reminder' => $journal_reminder,
                    'to_launch' => $to_launch,
                    'to_accomplish' => $to_accomplish,
                    'cart_reminder_to_close' => $cart_reminder_to_close,
                ]);
                $html .= $this->context->smarty->fetch(
                    parent::getTemplatePath() . 'live_cart_reminder/reminder-line.tpl'
                );
                if ($to_launch && $cart_reminder_to_close) {
                    break;
                }
            }
            $html .= '</table>';
        } elseif ($journal && Validate::isLoadedObject($journal) && $journal->id) {
            $journal_reminders = $journal->getJournalReminders();
            $cptpos = 1;
            foreach ($journal_reminders as $journal_reminder) {
                $this->context->smarty->assign([
                    'reminder' => null,
                    'id_cart' => $id_cart,
                    'journal_reminder' => $journal_reminder,
                    'to_launch' => false,
                    'to_accomplish' => false,
                    'journal_reminder_position' => $cptpos,
                ]);
                $html .= $this->context->smarty->fetch(
                    parent::getTemplatePath() . 'live_cart_reminder/reminder-line.tpl'
                );
                ++$cptpos;
            }
            $html .= '</table>';
        }

        return $html;
    }

    /**
     * Ajax call list message
     * die html
     */
    public function processShowMessages()
    {
        $id_journal = (int) Tools::getValue('id_journal');
        $messages = TACartReminderMessage::getMessages((int) $id_journal);
        $this->context->smarty->assign([
            'messages' => $messages,
        ]);
        $html = '';
        $html .= $this->context->smarty->fetch(
            parent::getTemplatePath() . 'live_cart_reminder/journal-messages.tpl'
        );
        exit($html);
    }

    /**
     * Ajax call for display Manual form to launch reminder
     *
     * @return html with die
     */
    public function processShowReminderLaunchForm()
    {
        $id_cart = (int) Tools::getValue('id_cart');
        $cart = new Cart($id_cart);
        $customer = new Customer($cart->id_customer);
        $id_lang = ((isset($customer->id_lang) && (int) $customer->id_lang) ?
            (int) $customer->id_lang : (int) $cart->id_lang
        );
        $id_reminder = (int) Tools::getValue('id_reminder');
        $reminder = TACartReminderRule::getReminder($id_reminder);
        $mail_template = new TACartReminderMailTemplate((int) $reminder['id_mail_template']);
        if (!isset($mail_template->subject[$id_lang])
            || empty($mail_template->content_html[$id_lang])
            || empty($mail_template->content_txt[$id_lang])) {
            $id_lang = (int) Configuration::get('PS_LANG_DEFAULT', null, null, $cart->id_shop);
        }
        $language_mail = new Language($id_lang);
        $this->context->smarty->assign([
            'reminder' => $reminder,
            'customer' => $customer,
            'cart' => $cart,
            'mail_template' => $mail_template,
            'language_mail' => $language_mail,
        ]);
        $html = $this->context->smarty->fetch(
            parent::getTemplatePath() .
            'live_cart_reminder/reminder-launch-form.tpl'
        );
        exit($html);
    }

    public function ajaxProcessPerformReminder()
    {
        $this->processPerformReminder();
    }

    /**
     * Process Reminder in manual method
     * JSON return
     * Eg :
     * {'success':['success message'],'errors':['error message'], 'has_error':true}
     */
    public function processPerformReminder()
    {
        $return = [
            'has_error' => false,
            'errors' => [],
        ];
        $id_cart = (int) Tools::getValue('id_cart');
        $id_reminder = (int) Tools::getValue('id_reminder');
        $type_perform = Tools::getValue('type_perform', 'DONE');
        $message = (string) Tools::getValue('message', '');
        $cart = new Cart($id_cart);
        if (Validate::isLoadedObject($cart) && ((int) $cart->id_customer)) {
            try {
                TACartReminderJournal::performReminder(
                    $cart,
                    $id_reminder,
                    $message,
                    $this->context->employee->id,
                    null,
                    $type_perform
                );
                $return['success'][] = $this->l('Operation successful');
            } catch (PrestaShopException $e) {
                $return['errors'][] = $e->getMessage();
                $return['has_error'] = true;
            }
        } else {
            $return['has_error'] = true;
            $return['errors'][] = Tools::displayError('Can\'t load Cart object, the cart id %s is there?');
        }
        exit(json_encode($return));
    }

    public function ajaxProcessSaveMessage()
    {
        $this->processSaveMessage();
    }

    /**
     * Saving message
     */
    public function processSaveMessage()
    {
        $return = [
            'has_error' => false,
            'errors' => [],
        ];
        $id_journal = (int) Tools::getValue('id_journal');
        $id_reminder = (int) Tools::getValue('id_reminder');
        $txt_message = (string) Tools::getValue('message', '');
        if (!$txt_message || empty($txt_message)) {
            $return['errors'][] = Tools::displayError('Message is required');
        }
        if (!$id_journal) {
            $return['errors'][] = Tools::displayError('Journal is required');
        }
        if (!count($return['errors'])) {
            $message = new TACartReminderMessage();
            $message->id_employee = (int) $this->context->employee->id;
            $message->id_journal = $id_journal;
            if ($id_reminder) {
                $message->id_reminder = $id_reminder;
            }
            $message->message = $txt_message;
            $message->is_system = false;
            if (!$message->add()) {
                $return['errors'][] = Tools::displayError('Error when saving message');
            } else {
                $message_info = TACartReminderMessage::getMessageInfo($message->id);
                $this->context->smarty->assign('message', $message_info);
                $return['message'] = $this->context->smarty->fetch(
                    parent::getTemplatePath() . 'live_cart_reminder/message-item.tpl'
                );
            }
        }
        if (count($return['errors'])) {
            $return['has_error'] = true;
        }
        exit(json_encode($return));
    }

    /**
     * Return iso code concat with , in $html for render list
     *
     * @param $isocode
     *
     * @return string
     */
    public static function getIsoCode($isocode)
    {
        $html = '';
        $isocodes = array_unique(explode(' ', $isocode));
        foreach ($isocodes as $isocode) {
            $html .= $isocode . ', ';
        }
        if (!empty($html)) {
            $html = Tools::substr($html, 0, -2);
        }

        return $html;
    }

    /**
     * Display in HTML cart content & list of rules dependind the cart
     *
     * @throws PrestaShopException
     */
    public function processShowCheckRules()
    {
        $id_cart = (int) Tools::getValue('id_cart');
        /**
         * **** CART INFORMATION ****
         */
        $cart = new Cart($id_cart);
        $customer = new Customer($cart->id_customer);
        $currency = new Currency($cart->id_currency);
        $this->context->cart = $cart;
        $this->context->currency = $currency;
        $this->context->customer = $customer;
        $this->toolbar_title = sprintf($this->l('Cart #%06d'), $this->context->cart->id);
        $products = $cart->getProducts();
        $customized_datas = Product::getAllCustomizedDatas((int) $cart->id);
        Product::addCustomizationPrice($products, $customized_datas);
        $summary = $cart->getSummaryDetails();
        /* Display order information */
        $id_order = (int) Order::getOrderByCartId($cart->id);
        $order = new Order($id_order);
        if (Validate::isLoadedObject($order)) {
            $tax_calculation_method = $order->getTaxCalculationMethod();
            $id_shop = (int) $order->id_shop;
        } else {
            $id_shop = (int) $cart->id_shop;
            $tax_calculation_method = Group::getPriceDisplayMethod(Group::getCurrent()->id);
        }
        $total_products_ht = $summary['total_products'];
        if ($tax_calculation_method == PS_TAX_EXC) {
            $total_products = $summary['total_products'];
            $total_discounts = $summary['total_discounts_tax_exc'];
            $total_wrapping = $summary['total_wrapping_tax_exc'];
            $total_price = $summary['total_price_without_tax'];
            $total_shipping = $summary['total_shipping_tax_exc'];
        } else {
            $total_products = $summary['total_products_wt'];
            $total_discounts = $summary['total_discounts'];
            $total_wrapping = $summary['total_wrapping'];
            $total_price = $summary['total_price'];
            $total_shipping = $summary['total_shipping'];
        }
        foreach ($products as $k => &$product) {
            if ($tax_calculation_method == PS_TAX_EXC) {
                $product['product_price'] = $product['price'];
                $product['product_total'] = $product['total'];
            } else {
                $product['product_price'] = $product['price_wt'];
                $product['product_total'] = $product['total_wt'];
            }
            $image = [];
            if (isset($product['id_product_attribute']) && (int) $product['id_product_attribute']) {
                $image = Db::getInstance()->getRow(
                    'SELECT id_image FROM ' . _DB_PREFIX_ . 'product_attribute_image
                    WHERE id_product_attribute = ' . (int) $product['id_product_attribute']
                );
            }
            if (!isset($image['id_image'])) {
                $image = Db::getInstance()->getRow(
                    'SELECT id_image FROM ' . _DB_PREFIX_ . 'image
                    WHERE id_product = ' . (int) $product['id_product'] . ' AND cover = 1'
                );
            }
            $product['qty_in_stock'] = StockAvailable::getQuantityAvailableByProduct(
                $product['id_product'],
                isset($product['id_product_attribute']) ? $product['id_product_attribute'] : null,
                (int) $id_shop
            );
            $image_product = new Image($image['id_image']);
            $product['image'] = (isset($image['id_image']) ?
                ImageManager::thumbnail(
                    _PS_IMG_DIR_ . 'p/' . $image_product->getExistingImgPath() . '.jpg',
                    'product_mini_' . (int) $product['id_product'] .
                    (isset($product['id_product_attribute']) ? '_' . (int) $product['id_product_attribute'] : '') .
                    '.jpg',
                    45,
                    'jpg'
                ) : '--'
            );
        }
        /**
         * **** END CART INFORMATION ****
         */
        $journal = TACartReminderJournal::getWithCart((int) $id_cart);
        $checkrules = TACartReminderRule::getApplicableRule($id_cart, $journal, true);
        $display_not_applicable = false;
        if (isset($journal) && $journal && $journal->id) {
            $display_not_applicable = !TACartReminderRule::isApplicableRule($journal->id_rule, $cart);
        }
        $this->context->smarty->assign([
            'display_not_applicable' => $display_not_applicable,
            'journal' => $journal,
            'checkrules' => $checkrules,
            'products' => $products,
            'discounts' => $cart->getCartRules(),
            'link' => $this->context->link,
            'order' => $order,
            'cart' => $cart,
            'currency' => $currency,
            'customer' => $customer,
            'customer_stats' => $customer->getStats(),
            'total_products' => $total_products,
            'total_products_ht' => $total_products_ht,
            'total_discounts' => $total_discounts,
            'total_wrapping' => $total_wrapping,
            'total_price' => $total_price,
            'total_shipping' => $total_shipping,
            'customized_datas' => $customized_datas,
            'pic_dir' => _THEME_PROD_PIC_DIR_,
        ]);
        $html = $this->context->smarty->fetch(parent::getTemplatePath() . 'live_cart_reminder/checkrules.tpl');
        exit($html);
    }

    public function ajaxProcessGetMail()
    {
        $this->processGetMail();
    }

    /**
     * @return mail content depending on the POST
     *              POST param id_mail_template, id_lang, id_cart, id_reminder
     */
    public function processGetMail()
    {
        $return = [
            'has_error' => false,
            'errors' => [],
        ];
        $id_mail_template = (int) Tools::getValue('id_mail_template');
        $id_lang = (int) Tools::getValue('id_lang');
        $id_cart = (int) Tools::getValue('id_cart');
        $id_reminder = (int) Tools::getValue('id_reminder');
        $mail_template = new TACartReminderMailTemplate($id_mail_template, $id_lang);
        if (!$id_cart) {
            $return['errors'][] = Tools::displayError('Cart is required');
        }
        if (!$id_lang) {
            $return['errors'][] = Tools::displayError('Lang is required');
        }
        if (!$id_mail_template) {
            $return['errors'][] = Tools::displayError('Mail template is required');
        }
        $nb_error = (int) count($return['errors']);
        if ($nb_error == 0) {
            $cart = new Cart((int) $id_cart);
            $customer = new Customer($cart->id_customer);
            $voucher_code = '############';
            $journal = TACartReminderJournal::getLastByCustomer($customer->email);
            if ((int) $journal->id_cart_rule) {
                $cart_rule = new CartRule((int) $journal->id_cart_rule);
                $voucher_code = $cart_rule->code;
            }
            $content_html = TACartReminderTools::renderMail(
                $id_reminder,
                (int) $id_cart,
                $mail_template->content_html,
                $mail_template->title,
                $voucher_code,
                false
            );
            $return['content_html'] = $content_html;
            $return['subject'] = $mail_template->subject;
            $return['subject'] = str_replace('{customer_firstname}', $customer->firstname, $return['subject']);
            $return['subject'] = str_replace('{customer_lastname}', $customer->lastname, $return['subject']);
        }
        if (count($return['errors'])) {
            $return['has_error'] = true;
        }
        exit(json_encode($return));
    }

    public function ajaxProcessSendMail()
    {
        $this->processSendMail();
    }

    /**
     * Process to send email to customer in manual method
     */
    public function processSendMail()
    {
        $return = [
            'has_error' => false,
            'errors' => [],
        ];
        $id_cart = (int) Tools::getValue('id_cart');
        $cart = new Cart($id_cart);
        $customer = new Customer((int) $cart->id_customer);
        $id_journal = (int) Tools::getValue('id_journal');
        $id_reminder = (int) Tools::getValue('id_reminder');
        $content_html = Tools::getValue('content_html', '');
        $subject = Tools::getValue('subject', '');
        $mail_bcc = Tools::getValue('mail_bcc');
        $mail_to = Tools::getValue('mail_to');
        if (!$id_cart) {
            $return['errors'][] = Tools::displayError('Cart is required');
        }
        if (!Validate::isLoadedObject($cart) || !((int) $cart->id)) {
            $return['errors'][] = Tools::displayError('Cannot be load Cart object');
        }
        if (!Validate::isLoadedObject($customer) || !((int) $customer->id)) {
            $return['errors'][] = Tools::displayError('Cannot be load Customer object');
        }
        $content_html_safe = trim(Tools::safeOutput($content_html));
        if (!$content_html || empty($content_html) || empty($content_html_safe)) {
            $return['errors'][] = Tools::displayError('Mail is empty');
        }
        if (!$subject || empty($subject)) {
            $return['errors'][] = Tools::displayError('Subject is empty');
        }
        if (!$mail_to || empty($mail_to)) {
            $return['errors'][] = Tools::displayError('Mail recipient is required');
        }
        if (!empty($mail_bcc) && !Validate::isEmail($mail_bcc)) {
            $return['errors'][] = sprintf($this->l('Email %1$s is not a valid address'), $mail_bcc);
        }
        if (strpos($mail_to, ',') !== false) {
            $mail_to = explode(',', $mail_to);
            foreach ($mail_to as $key => $m_to) {
                $m_to = trim($m_to);
                if (!Validate::isEmail($m_to)) {
                    $return['errors'][] = sprintf($this->l('Email %1$s is not a valid address'), $m_to);
                }
            }
        } elseif (!empty($mail_to) && !Validate::isEmail($mail_to)) {
            $return['errors'][] = sprintf($this->l('Email %1$s is not a valid address'), $mail_to);
        }
        if (empty($mail_bcc)) {
            $mail_bcc = null;
        }
        if (!isset($return['errors']) || !count($return['errors'])) {
            if (TACartReminderTools::FORCE_USE_STD_PRESTASHOP_FUNCTION) {
                try {
                    $contentTxt = TACartReminderTools::convertHtmlToText($content_html);
                } catch (Exception $e) {
                    $contentTxt = '';
                }
                $resultsend = Mail::Send(
                    $cart->id_lang,
                    'generic_template',
                    $subject,
                    [
                        'content_html' => TACartReminderTools::cartCSSToInline($content_html),
                        'content_txt' => $contentTxt,
                    ],
                    $mail_to,
                    null,
                    null,
                    null,
                    null,
                    null,
                    _PS_MODULE_DIR_ . 'tacartreminder/mails/',
                    false,
                    $cart->id_shop,
                    $mail_bcc
                );
            } else {
                $resultsend = TACartReminderTools::send(
                    $cart->id_lang,
                    '',
                    $content_html,
                    $subject,
                    [],
                    $mail_to,
                    null,
                    $cart->id_shop,
                    false,
                    $mail_bcc
                );
            }
            if ($id_journal) {
                $message = new TACartReminderMessage();
                $message->id_employee = (int) $this->context->employee->id;
                $message->id_journal = $id_journal;
                if ($id_reminder) {
                    $message->id_reminder = $id_reminder;
                }
                $message->message = sprintf(
                    $this->l('The employee sent an email to the client %1$s, the subject is -%2$s-, result process : %3$s'),
                    is_array($mail_to) ? $mail_to[0] : $mail_to,
                    $subject,
                    $resultsend ? 'OK' : 'KO'
                );
                $message->is_system = true;
                if (!$message->add()) {
                    $return['errors'][] = Tools::displayError('Error when saving message');
                } else {
                    $message_info = TACartReminderMessage::getMessageInfo($message->id);
                    $this->context->smarty->assign('message', $message_info);
                    $return['message'] = $this->context->smarty->fetch(
                        parent::getTemplatePath() . 'live_cart_reminder/message-item.tpl'
                    );
                }
            }
            if (!$resultsend) {
                $return['errors'][] = $this->l('Error when sending email');
            } else {
                $return['success'][] = $this->l('Email sent successfully');
            }
        }
        if (count($return['errors'])) {
            $return['has_error'] = true;
        }
        exit(json_encode($return));
    }

    public function ajaxProcessGetCartRules()
    {
        return $this->processGetCartRules();
    }

    /**
     * @return json cart rules by full text research
     */
    public function processGetCartRules()
    {
        $search_query = trim(Tools::getValue('q'));
        $cart_rules = Db::getInstance()->executeS('
			SELECT c.`id_cart_rule`, cl.`name`, c.`code`
			FROM `' . _DB_PREFIX_ . 'cart_rule` c
			INNER JOIN `' . _DB_PREFIX_ . 'cart_rule_lang` cl ON cl.id_cart_rule = c.id_cart_rule
			AND cl.id_lang = ' . $this->context->language->id . '
			WHERE c.`id_cart_rule` = ' . (int) $search_query . '
					OR cl.`name` LIKE "%' . pSQL($search_query) . '%"
					OR c.`code` LIKE "%' . pSQL($search_query) . '%"
			ORDER BY cl.`name` ASC
			LIMIT 50');
        exit(json_encode($cart_rules));
    }

    /**
     * Show form for manual process
     */
    public function processShowReminderManualProcess()
    {
        $id_cart = (int) Tools::getValue('id_cart');
        $cart = new Cart($id_cart);
        $id_reminder = (int) Tools::getValue('id_reminder');
        $reminder = TACartReminderRule::getReminder($id_reminder);
        $journal = TACartReminderJournal::getWithCart((int) $id_cart);
        $journal_messages = [];
        $mails_templates = TACartReminderMailTemplate::getMailTemplates($this->context->language->id);
        if (Validate::isLoadedObject($journal) && (int) $journal->id) {
            $journal_messages = TACartReminderMessage::getMessages($journal->id, $id_reminder);
        }
        if (Validate::isLoadedObject($cart) && ((int) $cart->id)) {
            $this->context->cart = $cart;
            $customer = new Customer($cart->id_customer);
            $addr_sum = Db::getInstance()->getRow('
					SELECT GROUP_CONCAT( addr.`phone` SEPARATOR \',\' ) as phones,
						   GROUP_CONCAT( addr.`phone_mobile` SEPARATOR \',\' ) as phone_mobiles,
						   GROUP_CONCAT( cl.`name` SEPARATOR \',\' ) as countries
						   FROM ' . _DB_PREFIX_ . 'address addr
						   LEFT JOIN ' . _DB_PREFIX_ . 'country_lang cl ON cl.id_country = addr.id_country
						   AND cl.id_lang = 1
						   WHERE addr.id_customer = ' . (int) $customer->id . '
						   GROUP BY id_customer');
            if (isset($addr_sum['phone_mobiles']) && !empty($addr_sum['phone_mobiles'])) {
                $sum_phone_mobiles = array_unique(explode(',', $addr_sum['phone_mobiles']));
            } else {
                $sum_phone_mobiles = [];
            }
            if (isset($addr_sum['phones']) && !empty($addr_sum['phones'])) {
                $sum_phones = array_unique(explode(',', $addr_sum['phones']));
            } else {
                $sum_phones = [];
            }
            if (isset($addr_sum['countries']) && !empty($addr_sum['countries'])) {
                $sum_countries = array_unique(explode(',', $addr_sum['countries']));
            } else {
                $sum_countries = [];
            }
            $mobiles_summary = [];
            $phones_summary = [];
            $countries_summary = [];
            foreach ($sum_phone_mobiles as $phone_mobile) {
                if (!empty($phone_mobile) && !empty($phone_mobile)) {
                    $mobiles_summary[] = $phone_mobile;
                }
            }
            foreach ($sum_phones as $phone) {
                if (!empty($phone) && !empty($phone)) {
                    $phones_summary[] = $phone;
                }
            }
            foreach ($sum_countries as $country) {
                if (!empty($country) && !empty($country)) {
                    $countries_summary[] = $country;
                }
            }
            $currency = new Currency($cart->id_currency);
            $this->context->currency = $currency;
            $this->context->customer = $customer;
            $journal_reminder = false;
            if ((int) $journal->id) {
                $journal_reminder = $journal->getJournalReminder((int) $reminder['id_reminder']);
                if ($journal_reminder && !(int) $journal_reminder['id_employee']
                    && (int) $journal_reminder['id_reminder'] && (int) $journal_reminder['id_journal']) {
                    $jr_upd = [];
                    $jr_upd['id_employee'] = (int) $this->context->employee->id;
                    $jr_upd['date_upd'] = date('Y-m-d H:i:s');
                    Db::getInstance()->update(
                        'ta_cartreminder_journal_reminder',
                        $jr_upd,
                        '`id_reminder` = ' . (int) $journal_reminder['id_reminder'] .
                        ' AND `id_journal` = ' . (int) $journal->id
                    );
                }
            }
            $this->toolbar_title = sprintf($this->l('Cart #%06d, reminder position '), $this->context->cart->id);
            $products = $cart->getProducts();
            $customized_datas = Product::getAllCustomizedDatas((int) $cart->id);
            Product::addCustomizationPrice($products, $customized_datas);
            $summary = $cart->getSummaryDetails();
            $id_shop = (int) $cart->id_shop;
            $tax_calculation_method = Group::getPriceDisplayMethod(Group::getCurrent()->id);
            if ($tax_calculation_method == PS_TAX_EXC) {
                $total_products = $summary['total_products'];
                $total_discounts = $summary['total_discounts_tax_exc'];
                $total_wrapping = $summary['total_wrapping_tax_exc'];
                $total_price = $summary['total_price_without_tax'];
                $total_shipping = $summary['total_shipping_tax_exc'];
            } else {
                $total_products = $summary['total_products_wt'];
                $total_discounts = $summary['total_discounts'];
                $total_wrapping = $summary['total_wrapping'];
                $total_price = $summary['total_price'];
                $total_shipping = $summary['total_shipping'];
            }
            foreach ($products as $k => &$product) {
                if ($tax_calculation_method == PS_TAX_EXC) {
                    $product['product_price'] = $product['price'];
                    $product['product_total'] = $product['total'];
                } else {
                    $product['product_price'] = $product['price_wt'];
                    $product['product_total'] = $product['total_wt'];
                }
                $image = [];
                if (isset($product['id_product_attribute']) && (int) $product['id_product_attribute']) {
                    $image = Db::getInstance()->getRow(
                        'SELECT id_image FROM ' . _DB_PREFIX_ . 'product_attribute_image
                        WHERE id_product_attribute = ' . (int) $product['id_product_attribute']
                    );
                }
                if (!isset($image['id_image'])) {
                    $image = Db::getInstance()->getRow('SELECT id_image FROM ' . _DB_PREFIX_ . 'image
                    WHERE id_product = ' . (int) $product['id_product'] . ' AND cover = 1');
                }
                $product['qty_in_stock'] = StockAvailable::getQuantityAvailableByProduct(
                    $product['id_product'],
                    isset($product['id_product_attribute']) ? $product['id_product_attribute'] : null,
                    (int) $id_shop
                );
                $image_product = new Image($image['id_image']);
                $product['image'] = (isset($image['id_image']) ? ImageManager::thumbnail(
                    _PS_IMG_DIR_ . 'p/' . $image_product->getExistingImgPath() . '.jpg',
                    'product_mini_' . (int) $product['id_product'] .
                    (isset($product['id_product_attribute']) ? '_' . (int) $product['id_product_attribute'] : '') .
                    '.jpg',
                    45,
                    'jpg'
                ) : '--'
                );
            }
            $gender = new Gender($customer->id_gender, $this->context->language->id);
            $gender_image = $gender->getImage();
            $customer_stats = $customer->getStats();
            $sql = 'SELECT SUM(total_paid_real) FROM ' . _DB_PREFIX_ . 'orders WHERE id_customer = %d AND valid = 1';
            if ($total_customer = Db::getInstance()->getValue(sprintf($sql, $customer->id))) {
                $sql = 'SELECT SQL_CALC_FOUND_ROWS COUNT(*) FROM ' . _DB_PREFIX_ . 'orders
                WHERE valid = 1 AND id_customer != ' . (int) $customer->id .
                    ' GROUP BY id_customer HAVING SUM(total_paid_real) > %d';
                Db::getInstance()->getValue(sprintf($sql, (int) $total_customer));
                $count_better_customers = (int) Db::getInstance()->getValue('SELECT FOUND_ROWS()') + 1;
            } else {
                $count_better_customers = '-';
            }
            $orders = Order::getCustomerOrders($customer->id, true);
            $total_orders = count($orders);
            for ($i = 0; $i < $total_orders; ++$i) {
                $orders[$i]['total_paid_real_not_formated'] = $orders[$i]['total_paid_real'];
                $orders[$i]['total_paid_real'] = Tools::displayPrice(
                    $orders[$i]['total_paid_real'],
                    new Currency((int) $orders[$i]['id_currency'])
                );
            }
            $messages = CustomerThread::getCustomerMessages((int) $customer->id);
            $total_messages = count($messages);
            for ($i = 0; $i < $total_messages; ++$i) {
                $messages[$i]['message'] = Tools::substr(
                    strip_tags(html_entity_decode($messages[$i]['message'], ENT_NOQUOTES, 'UTF-8')),
                    0,
                    75
                );
                $messages[$i]['date_add'] = Tools::displayDate($messages[$i]['date_add'], null, true);
            }
            $groups = $customer->getGroups();
            $total_groups = count($groups);
            for ($i = 0; $i < $total_groups; ++$i) {
                $group = new Group($groups[$i]);
                $groups[$i] = [];
                $groups[$i]['id_group'] = $group->id;
                $groups[$i]['name'] = $group->name[$this->context->language->id];
            }
            $total_ok = 0;
            $orders_ok = [];
            $orders_ko = [];
            foreach ($orders as $order) {
                if (!isset($order['order_state'])) {
                    $order['order_state'] = $this->l('There is no status defined for this order.');
                }
                if ($order['valid']) {
                    $orders_ok[] = $order;
                    $total_ok += $order['total_paid_real_not_formated'];
                } else {
                    $orders_ko[] = $order;
                }
            }
            $products_bought = $customer->getBoughtProducts();
            $sql = 'SELECT DISTINCT cp.id_product, c.id_cart, c.id_shop, cp.id_shop AS cp_id_shop
							FROM ' . _DB_PREFIX_ . 'cart_product cp
							JOIN ' . _DB_PREFIX_ . 'cart c ON (c.id_cart = cp.id_cart)
							JOIN ' . _DB_PREFIX_ . 'product p ON (cp.id_product = p.id_product)
							WHERE c.id_customer = ' . (int) $customer->id . '
							AND cp.id_product NOT IN (
										SELECT product_id
							FROM ' . _DB_PREFIX_ . 'orders o
							JOIN ' . _DB_PREFIX_ . 'order_detail od ON (o.id_order = od.id_order)
							WHERE o.valid = 1 AND o.id_customer = ' . (int) $customer->id . '
							)';
            $interested = Db::getInstance()->executeS($sql);
            $total_interested = count($interested);
            for ($i = 0; $i < $total_interested; ++$i) {
                $product_interest = new Product(
                    $interested[$i]['id_product'],
                    false,
                    $this->context->language->id,
                    $interested[$i]['id_shop']
                );
                if (!Validate::isLoadedObject($product_interest)) {
                    continue;
                }
                $interested[$i]['url'] = $this->context->link->getProductLink(
                    $product_interest->id,
                    $product_interest->link_rewrite,
                    Category::getLinkRewrite(
                        $product_interest->id_category_default,
                        $this->context->language->id
                    ),
                    null,
                    null,
                    $interested[$i]['cp_id_shop']
                );
                $interested[$i]['id'] = (int) $product_interest->id;
                $interested[$i]['name'] = Tools::htmlentitiesUTF8($product_interest->name);
            }
            $connections = $customer->getLastConnections();
            if (!is_array($connections)) {
                $connections = [];
            }
            $total_connections = count($connections);
            for ($i = 0; $i < $total_connections; ++$i) {
                $connections[$i]['http_referer'] = $connections[$i]['http_referer'] ?
                    preg_replace(
                        '/^www./',
                        '',
                        parse_url(
                            $connections[$i]['http_referer'],
                            PHP_URL_HOST
                        )
                    ) : $this->l('Direct link');
            }
            $referrers = [];
            if (version_compare(_PS_VERSION_, '8.0.0', '<')) {
                $referrers = Referrer::getReferrers($customer->id);
            }
            $total_referrers = count($referrers);
            for ($i = 0; $i < $total_referrers; ++$i) {
                $referrers[$i]['date_add'] = Tools::displayDate($referrers[$i]['date_add'], null, true);
            }
            if (isset($customer->id_lang) && (int) $customer->id_lang) {
                $customer_language = new Language($customer->id_lang);
            } else {
                $customer_language = new Language($cart->id_lang);
            }
            $shop = new Shop($customer->id_shop);
            $this->context->smarty->assign([
                'journal_messages' => $journal_messages,
                'mobiles_summary' => $mobiles_summary,
                'phones_summary' => $phones_summary,
                'countries_summary' => $countries_summary,
                'customer' => $customer,
                'languages' => Language::getLanguages(true),
                'gender' => $gender,
                'gender_image' => $gender_image,
                'tax_calculation_method' => $tax_calculation_method,
                // General information of the customer
                'registration_date' => Tools::displayDate($customer->date_add, null, true),
                'customer_stats' => $customer_stats,
                'last_visit' => Tools::displayDate($customer_stats['last_visit'], null, true),
                'count_better_customers' => $count_better_customers,
                'shop_is_feature_active' => Shop::isFeatureActive(),
                'name_shop' => $shop->name,
                'customer_birthday' => Tools::displayDate($customer->birthday, null),
                'last_update' => Tools::displayDate($customer->date_upd, null, true),
                'customer_exists' => Customer::customerExists($customer->email),
                'id_lang' => (isset($customer->id_lang) && (int) $customer->id_lang ?
                    $customer->id_lang :
                    $cart->id_lang
                ),
                'customerLanguage' => $customer_language,
                'customer_note' => Tools::htmlentitiesUTF8($customer->note),
                'messages' => $messages,
                'mails_templates' => $mails_templates,
                'groups' => $groups,
                'link' => $this->context->link,
                'orders' => $orders,
                'orders_ok' => $orders_ok,
                'orders_ko' => $orders_ko,
                'total_ok' => Tools::displayPrice($total_ok, $this->context->currency->id),
                'products_bought' => $products_bought,
                // Addresses
                'addresses' => $customer->getAddresses($this->context->language->id),
                'interested' => $interested,
                // Connections
                'connections' => $connections,
                // Referrers
                'referrers' => $referrers,
                'products' => $products,
                'discounts' => $cart->getCartRules(),
                // Discounts
                'customer_discounts' => CartRule::getCustomerCartRules(
                    $this->context->language->id,
                    $customer->id,
                    false,
                    false
                ),
                // 'order' => $order,
                'journal' => $journal,
                'journal_reminder' => $journal_reminder,
                'reminder' => $reminder,
                'tpl_lvc_path' => parent::getTemplatePath() . 'live_cart_reminder',
                'cart' => $cart,
                'currency' => $currency,
                'total_products' => $total_products,
                'total_discounts' => $total_discounts,
                'total_wrapping' => $total_wrapping,
                'total_price' => $total_price,
                'total_shipping' => $total_shipping,
                'customized_datas' => $customized_datas,
                'pic_dir' => _THEME_PROD_PIC_DIR_,
            ]);
            exit($this->context->smarty->fetch(
                parent::getTemplatePath() . 'live_cart_reminder/reminder_manual_process.tpl'
            ));
        } else {
            return;
        }
    }

    public function renderStats()
    {
        return $this->context->smarty->fetch(parent::getTemplatePath() . 'live_cart_reminder/stats.tpl');
    }

    public function ajaxProcessGetStats()
    {
        $this->processGetStats();
    }

    /**
     * Same process dashboard module, return json
     */
    public function processGetStats()
    {
        $return = [
            'has_error' => false,
            'errors' => [],
        ];
        $date_from = (string) Tools::getValue('date-start') . ' 00:00:00';
        $date_to = (string) Tools::getValue('date-end') . ' 23:59:59';
        if (!Validate::isDate($date_from) || !Validate::isDate($date_to)) {
            $return['errors'][] = Tools::displayError('Specified date is invalid');
        }
        if (!count($return['errors'])) {
            /* ORDER AND CONVERSION */
            $start_time = strtotime($date_from);
            $end_time = strtotime($date_to);
            $order_granularity = (int) Tools::getValue('order_granularity', 10);
            $return['stats-order-summary'] = TACartReminderStats::getOrders($date_from, $date_to);
            $stats_order = TACartReminderStats::getOrders($date_from, $date_to, $order_granularity);
            $return['stats-trends'] = [];
            $conv_stat_order = new TAStackedStat();
            $conv_stat_order->key = $this->l('Cart reminders with customer order');
            $conv_stat_nb_reminders = new TAStackedStat();
            $conv_stat_nb_reminders->key = $this->l('Cart reminders');
            $order_stat = new TAStackedStat();
            $order_stat->key = $this->l('Sales');
            $order_stat->values = [];
            $conv_stat_line_order = new TAStatPieLine();
            $conv_stat_line_order->label = $this->l('Cart reminders with customer order');
            $conv_stat_line_order->value = (int) $return['stats-order-summary']['count_orders'];
            $conv_stat_line_noorder = new TAStatPieLine();
            $conv_stat_line_noorder->label = $this->l('Cart reminders without customer order');
            $conv_stat_line_noorder->value = (int) $return['stats-order-summary']['count_cart_reminders']
                - (int) $return['stats-order-summary']['count_orders'];
            for ($i = $start_time; $i <= $end_time; $i = $i + 86400) {
                $cur_date = (string) date('Y-m-d', $i);
                $total_sales = 0;
                $count_orders = 0;
                $count_cart_reminders = 0;
                if (isset($stats_order[$cur_date])) {
                    $total_sales = (float) $stats_order[$cur_date]['total_sales'];
                    $count_orders = (int) $stats_order[$cur_date]['count_orders'];
                    $count_cart_reminders = (int) $stats_order[$cur_date]['count_cart_reminders'];
                }
                $order_stat->values[] = [
                    $i,
                    $total_sales,
                ];
                $conv_stat_order->values[] = [
                    $i,
                    $count_orders,
                ];
                $conv_stat_nb_reminders->values[] = [
                    $i,
                    $count_cart_reminders,
                ];
            }
            $return['stats-trends']['conversion'] = [];
            $return['stats-trends']['conversion'][] = $conv_stat_order;
            $return['stats-trends']['conversion'][] = $conv_stat_nb_reminders;
            $return['stats-trends']['order'] = [];
            $return['stats-trends']['order'][] = $order_stat;
            $return['stats-trends']['conversion_pie'] = [];
            $return['stats-trends']['conversion_pie'][] = $conv_stat_line_order;
            $return['stats-trends']['conversion_pie'][] = $conv_stat_line_noorder;
            /* MAIL */
            $stats_mail_summary = TACartReminderStats::getMail($date_from, $date_to, true);
            $stats_mail_lines = TACartReminderStats::getMail($date_from, $date_to);
            $return['table-line']['mail'] = $stats_mail_lines;
            $return['stats-mail-summary'] = $stats_mail_summary;
            /* RULE */
            $stats_rule_lines = TACartReminderStats::getRule($date_from, $date_to);
            $return['table-line']['rule'] = $stats_rule_lines;
            $stats_employee_lines = TACartReminderStats::getEmployee($date_from, $date_to);
            $return['table-line']['employee'] = $stats_employee_lines;
        }
        if (count($return['errors'])) {
            $return['has_error'] = true;
        }
        exit(json_encode($return));
    }

    /**
     * Display fancy ajax help associate to the page
     */
    public function processShowHelp()
    {
        $helppage = Tools::getValue('helppage');
        $this->context->smarty->assign('tab_select', $this->tab_select);
        $this->context->smarty->assign('stopreminder_nbhour', Configuration::get('TA_CARTR_STOPREMINDER_NB_HOUR'));
        switch ($helppage) {
            case 'steps-manual':
                exit($this->context->smarty->fetch(
                    parent::getTemplatePath() . 'live_cart_reminder/help-steps-manual.tpl'
                ));
            case 'steps-cart':
                exit($this->context->smarty->fetch(
                    parent::getTemplatePath() . 'live_cart_reminder/help-steps-cart.tpl'
                ));
            case 'steps-running':
                exit($this->context->smarty->fetch(
                    parent::getTemplatePath() . 'live_cart_reminder/help-steps-running.tpl'
                ));
            case 'steps-finished':
                exit($this->context->smarty->fetch(
                    parent::getTemplatePath() . 'live_cart_reminder/help-steps-finished.tpl'
                ));
            case 'unsubscribes':
                exit($this->context->smarty->fetch(
                    parent::getTemplatePath() . 'live_cart_reminder/help-unscribes.tpl'
                ));
            case 'configuration':
                exit($this->context->smarty->fetch(
                    parent::getTemplatePath() . 'live_cart_reminder/help-configuration.tpl'
                ));
            case 'rule':
                exit($this->context->smarty->fetch(
                    parent::getTemplatePath() . 'live_cart_reminder/help-rule.tpl'
                ));
            default:
                exit('');
        }
    }
}
