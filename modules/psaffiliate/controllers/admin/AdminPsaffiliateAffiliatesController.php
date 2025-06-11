<?php
/**
 * NOTICE OF LICENSE
 *
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * You must not modify, adapt or create derivative works of this source code.
 *
 * @author    Active Design <office@activedesign.ro>
 * @copyright 2016-2018 Active Design
 * @license   LICENSE.txt
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class AdminPsaffiliateAffiliatesController extends AdminController
{
    public $module;
    public $fields_list;
    protected $_defaultOrderBy = 'id_affiliate';
    protected $_defaultOrderWay = 'ASC';
    public $id_affiliate = 0;
    public $obj;

    public function __construct()
    {
        $this->moduleObj = Module::getInstanceByName('psaffiliate');
        $this->moduleObj->loadClasses('Affiliate');

        $this->bootstrap = true;
        $this->required_database = false;
        $this->table = 'aff_affiliates';
        $this->identifier = 'id_affiliate';
        $this->className = 'Affiliate';
        $this->lang = false;
        $this->explicitSelect = true;

        $this->allow_export = true;

        $this->addRowAction('view');
        $this->addRowAction('edit');
        $this->addRowAction('delete');
        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'confirm' => $this->l('Deleting an affiliate will erase all data about his sales, traffic, payments and so on. Are you sure you want to do that? Instead deleting the affiliates you could just disable them.'),
                'icon' => 'icon-trash',
            ),
        );

        $this->context = Context::getContext();

        $this->default_form_language = $this->context->language->id;

        $this->_use_found_rows = false;
        $this->fields_list = array(
            'id_affiliate' => array(
                'title' => $this->l('ID'),
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
                'search' => true,
                'filter_type' => 'int',
                'filter_key' => 'a!id_affiliate',
            ),
            'name' => array(
                'title' => $this->l('Name'),
                'align' => 'text-left',
                'width' => 'auto',
                'search' => true,
                'havingFilter' => true,
            ),
            'email' => array(
                'title' => $this->l('Email'),
                'align' => 'text-left',
                'width' => 'auto',
                'search' => true,
                'havingFilter' => true,
            ),
            'website' => array(
                'title' => $this->l('Website'),
                'align' => 'text-left',
                'width' => 'auto',
                'search' => true,
                'filter_key' => 'a!website',
            ),
            'per_click' => array(
                'title' => $this->l('per click'),
                'align' => 'text-left',
                'width' => 'auto',
                'type' => 'price',
                'search' => true,
                'havingFilter' => false,
            ),
            'per_unique_click' => array(
                'title' => $this->l('per unique click'),
                'align' => 'text-left',
                'width' => 'auto',
                'type' => 'price',
                'search' => true,
                'havingFilter' => false,
            ),
            'per_sale' => array(
                'title' => $this->l('per sale'),
                'align' => 'text-left',
                'width' => 'auto',
                'type' => 'price',
                'search' => true,
                'havingFilter' => false,
            ),
            'per_sale_percent' => array(
                'title' => $this->l('per sale')." (%)",
                'align' => 'text-left',
                'width' => 'auto',
                'search' => true,
                'havingFilter' => false,
                'suffix' => '%',
            ),
            'balance' => array(
                'title' => $this->l('Balance'),
                'align' => 'text-left',
                'width' => 'auto',
                'type' => 'price',
                'search' => true,
                'havingFilter' => false,
            ),
            'active' => array(
                'title' => $this->l('Enabled'),
                'align' => 'text-center',
                'active' => 'status',
                'type' => 'bool',
                'orderby' => false,
                'filter_key' => 'a!active',
            ),
        );

        $this->_join = 'LEFT JOIN `'._DB_PREFIX_.'customer` c ON (c.`id_customer` = a.`id_customer`)';
        $this->_select = '
            IF(a.`id_customer` <> 0, CONCAT(c.`firstname`, " ", c.`lastname`), CONCAT(a.`firstname`, " ", a.`lastname`)) as `name`,
            IF(a.`id_customer` <> 0, c.`email`, a.`email`) as `email`,
            ROUND(IFNULL((SELECT cm.`value` FROM `'._DB_PREFIX_.'aff_commission` cm WHERE cm.`type`="click" AND cm.`id_affiliate`=a.`id_affiliate` ORDER BY `date` DESC LIMIT 1), (SELECT cm.`value` FROM `'._DB_PREFIX_.'aff_commission` cm WHERE cm.`type`="click" AND cm.`id_affiliate`="0" ORDER BY `date` DESC LIMIT 1)), 2) as `per_click`,
            ROUND(IFNULL((SELECT cm.`value` FROM `'._DB_PREFIX_.'aff_commission` cm WHERE cm.`type`="unique_click" AND cm.`id_affiliate`=a.`id_affiliate` ORDER BY `date` DESC LIMIT 1), (SELECT cm.`value` FROM `'._DB_PREFIX_.'aff_commission` cm WHERE cm.`type`="unique_click" AND cm.`id_affiliate`="0" ORDER BY `date` DESC LIMIT 1)), 2) as `per_unique_click`,
            ROUND(IFNULL((SELECT cm.`value` FROM `'._DB_PREFIX_.'aff_commission` cm WHERE cm.`type`="sale" AND cm.`id_affiliate`=a.`id_affiliate` ORDER BY `date` DESC LIMIT 1), (SELECT cm.`value` FROM `'._DB_PREFIX_.'aff_commission` cm WHERE cm.`type`="sale" AND cm.`id_affiliate`="0" ORDER BY `date` DESC LIMIT 1)), 2) as `per_sale`,
            ROUND(IFNULL((SELECT cm.`value` FROM `'._DB_PREFIX_.'aff_commission` cm WHERE cm.`type`="sale_percent" AND cm.`id_affiliate`=a.`id_affiliate` ORDER BY `date` DESC LIMIT 1), (SELECT cm.`value` FROM `'._DB_PREFIX_.'aff_commission` cm WHERE cm.`type`="sale_percent" AND cm.`id_affiliate`="0" ORDER BY `date` DESC LIMIT 1)), 2) as `per_sale_percent`,
            (IFNULL(((SELECT IFNULL(SUM(`commission`), 0) FROM `'._DB_PREFIX_.'aff_tracking` t WHERE t.`id_affiliate`=a.`id_affiliate`)+(SELECT IFNULL(SUM(`commission`), 0) FROM `'._DB_PREFIX_.'aff_sales` s WHERE s.`id_affiliate`=a.`id_affiliate` AND s.`approved`="1")-(SELECT IFNULL(SUM(`amount`), 0) FROM `'._DB_PREFIX_.'aff_payments` p WHERE p.`id_affiliate`=a.`id_affiliate`)), 0)) as `balance`
        ';
        $this->shopLinkType = '';
        parent::__construct();
    }

    public function init()
    {
        parent::init();
        $this->context->controller->addJquery();
        $this->addJS(_PS_MODULE_DIR_.'psaffiliate/views/js/admin/bootstrap-select.min.js');
        $this->addCSS(_PS_MODULE_DIR_.'psaffiliate/views/css/bootstrap-select.min.css');
        $this->addJS(_PS_MODULE_DIR_.'psaffiliate/views/js/admin/ajax-bootstrap-select.min.js');
        $this->addCSS(_PS_MODULE_DIR_.'psaffiliate/views/css/ajax-bootstrap-select.min.css');
        Media::addJsDef(array(
            'ajaxtools_url' => $this->context->link->getAdminLink('AdminPsaffiliateAjax'),
        ));
        $this->addJS(_PS_MODULE_DIR_.'psaffiliate/views/js/admin/ajaxtools.js');
    }

    public function initContent()
    {
        parent::initContent();

        $this->meta_title = $this->l('Affiliates');
    }

    public function renderList()
    {
        $this->context->controller->addJs(_PS_MODULE_DIR_.'psaffiliate/views/js/admin/affiliateslist.js');
        $js_def = array(
            'urlForAffiliateCheck' => $this->context->link->getModuleLink(
                'psaffiliate',
                'getaffiliatesdetails',
                array('getHasBeenReviewed' => true)
            ),
            'hasNotBeenReviewedText' => $this->l('Has not been reviewed yet'),
        );
        if (method_exists('Media', 'addJsDef')) {
            Media::addJsDef($js_def);
        } else {
            $script = '<script>'.PHP_EOL;
            foreach ($js_def as $k => $v) {
                if (!is_numeric($v)) {
                    $v = '"'.$v.'"';
                }
                $script .= 'var '.$k.' = '.$v.';'.PHP_EOL;
            }
            $script .= '</script>'.PHP_EOL;
        }
        $display = parent::renderList();
        if (isset($script)) {
            $display .= PHP_EOL.$script;
        }

        return $display;
    }

    public function renderView()
    {
        $this->moduleObj->loadClasses(array('Tracking', 'Sale', 'Payment', 'Customfield'));

        $this->context->controller->addJs($this->moduleObj->getPath().'views/js/admin/statistics.js');
        $this->context->controller->addJs($this->moduleObj->getPath().'views/js/admin/highcharts.min.js');
        $this->context->controller->addJqueryUi('ui.datepicker');

        $currency = new Currency((int)Configuration::get('PS_CURRENCY_DEFAULT'));
        $js_def = array(
            'currencySign' => $currency->sign,
            'currencyFormat' => $currency->format,
            'currencyBlank' => $currency->blank,
            'priceDisplayPrecision' => _PS_PRICE_DISPLAY_PRECISION_,
        );
        if (method_exists('Media', 'addJsDef')) {
            Media::addJsDef($js_def);
        } else {
            $script = '<script>'.PHP_EOL;
            foreach ($js_def as $k => $v) {
                if (!is_numeric($v)) {
                    $v = '"'.$v.'"';
                }
                $script .= 'var '.$k.' = '.$v.';'.PHP_EOL;
            }
            $script .= '</script>'.PHP_EOL;
        }

        $affiliate = new Affiliate(Tools::getValue('id_affiliate'));
        $affiliate = (array)$affiliate;
        $this->context->smarty->assign('affiliate', $affiliate);
        if ($affiliate['id_customer']) {
            $this->context->smarty->assign('customer', (array)new Customer((int)$affiliate['id_customer']));
        }
        $days_current_summary = AffConf::getConfig('days_current_summary');
        $this->context->smarty->assign('days_current_summary', $days_current_summary);
        $traffic = Tracking::getAffiliateTraffic($affiliate['id_affiliate'], 10);
        $this->context->smarty->assign('traffic', $traffic);
        $sales = Sale::getAffiliateSales($affiliate['id_affiliate'], 10);
        $this->context->smarty->assign('sales', $sales);
        $payments = Payment::getAffiliatePayments($affiliate['id_affiliate'], 10);
        $this->context->smarty->assign('payments', $payments);
        $rates = Affiliate::getRatesHistory($affiliate['id_affiliate']);
        $this->context->smarty->assign('rates', $rates);
        $campaigns = $this->object->getCampaigns($affiliate['id_affiliate']);
        $this->context->smarty->assign('campaigns', $campaigns);

        // Meta data
        $customFields = Customfield::allByAffiliate($this->object);
        $this->context->smarty->assign('custom_fields', $customFields);

        // Lifetime affiliation data
        $lifetime_affiliations = $this->moduleObj->getLifetimeAffiliations($this->object->id);
        $this->context->smarty->assign('lifetime_affiliations', $lifetime_affiliations);

        $display = $this->context->smarty->fetch(_PS_MODULE_DIR_.'psaffiliate/views/templates/admin/affiliate-view.tpl');
        if (isset($script)) {
            $display .= PHP_EOL.$script;
        }

        return parent::renderView().$display;
    }

    public function renderForm()
    {
        $id_currency = Configuration::get('PS_CURRENCY_DEFAULT');
        $currency = new Currency($id_currency);
        $this->fields_form = array(
            'legend' => array(
                'title' => (isset($this->object)) ? $this->l('Affiliate')." #".$this->object->id : $this->l('Affiliate'),
                'icon' => 'icon-user',
            ),
            'input' => array(
                array(
                    //'type' => 'select',
                    'type' => 'text',
                    'label' => $this->l('ID Client (à récupérer depuis l\'onglet CLIENTS)'),
                    'name' => 'id_customer',
                    'col' => '4',
                    // 'class' => 'ajaxselectpicker', // très lent quand un grand nombre de clients
                    /*'options' => array(
                        'query' => Affiliate::getCustomersList(
                            false,
                            (!is_null($this->object) ? $this->object->id_customer : 0)
                        ),
                        'id' => 'id_customer',
                        'name' => 'idandname',
                    ),*/
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('First name'),
                    'name' => 'firstname',
                    'col' => '4',
                    'hint' => $this->l('Invalid characters:').' 0-9!&lt;&gt;,;?=+()@#"°{}_$%:',
                    'disabled' => $this->object ? (bool)$this->object->id_customer : false,
                    'desc' => $this->object ? ((bool)$this->object->id_customer && $this->object->edit_customer_link ? $this->l('You can edit this in the Customers menu, ')."<a href='".$this->object->edit_customer_link."'>".$this->l('here')."</a>." : '') : '',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Last name'),
                    'name' => 'lastname',
                    'col' => '4',
                    'hint' => $this->l('Invalid characters:').' 0-9!&lt;&gt;,;?=+()@#"°{}_$%:',
                    'disabled' => $this->object ? (bool)$this->object->id_customer : '',
                    'desc' => $this->object ? ((bool)$this->object->id_customer && $this->object->edit_customer_link ? $this->l('You can edit this in the Customers menu, ')."<a href='".$this->object->edit_customer_link."'>".$this->l('here')."</a>." : '') : '',
                ),
                array(
                    'type' => 'text',
                    'prefix' => '<i class="icon-envelope-o"></i>',
                    'label' => $this->l('Email address'),
                    'name' => 'email',
                    'col' => '4',
                    'autocomplete' => false,
                    'disabled' => $this->object ? (bool)$this->object->id_customer : '',
                    'desc' => $this->object ? ((bool)$this->object->id_customer && $this->object->edit_customer_link ? $this->l('You can edit this in the Customers menu, ')."<a href='".$this->object->edit_customer_link."'>".$this->l('here')."</a>." : '') : '',
                ),
                array(
                    'type' => 'text',
                    'prefix' => '<i class="icon-question"></i>',
                    'label' => $this->l('Registration question'),
                    'name' => 'textarea_registration_label',
                    'col' => '4',
                    'autocomplete' => false,
                ),
                array(
                    'type' => 'textarea',
                    'prefix' => '<i class="icon-info"></i>',
                    'label' => $this->l('Registration answer'),
                    'name' => 'textarea_registration',
                    'col' => '4',
                    'autocomplete' => false,
                ),
                array(
                    'type' => 'text',
                    'suffix' => $currency->iso_code,
                    'label' => $this->l('Commission per click'),
                    'name' => 'per_click',
                    'col' => '4',
                    'autocomplete' => false,
                ),
                array(
                    'type' => 'text',
                    'suffix' => $currency->iso_code,
                    'label' => $this->l('Commission per unique click'),
                    'name' => 'per_unique_click',
                    'col' => '4',
                    'autocomplete' => false,
                ),
                array(
                    'type' => 'text',
                    'suffix' => $currency->iso_code,
                    'label' => $this->l('Commission per sale'),
                    'name' => 'per_sale',
                    'col' => '4',
                    'autocomplete' => false,
                ),
                array(
                    'type' => 'text',
                    'suffix' => '%',
                    'label' => $this->l('Commission per sale'),
                    'name' => 'per_sale_percent',
                    'col' => '4',
                    'autocomplete' => false,
                ),
            ),
        );
        $this->fields_form['input'][] = array(
            'type' => 'switch',
            'label' => $this->l('Enabled'),
            'name' => 'active',
            'required' => false,
            'class' => 't',
            'is_bool' => true,
            'values' => array(
                array(
                    'id' => 'active_on',
                    'value' => 1,
                    'label' => $this->l('Enabled'),
                ),
                array(
                    'id' => 'active_off',
                    'value' => 0,
                    'label' => $this->l('Disabled'),
                ),
            ),
            'hint' => $this->l('Enable or disable affiliate.'),
        );
        $this->fields_form['input'][] = array(
            'type' => 'switch',
            'label' => $this->l('Has been reviewed'),
            'name' => 'has_been_reviewed',
            'required' => false,
            'class' => 't',
            'is_bool' => true,
            'values' => array(
                array(
                    'id' => 'active_on',
                    'value' => 1,
                    'label' => $this->l('Enabled'),
                ),
                array(
                    'id' => 'active_off',
                    'value' => 0,
                    'label' => $this->l('Disabled'),
                ),
            ),
            'hint' => $this->l('Has the request approval been reviewed?'),
        );
        if (isset($this->object->aff_meta) && sizeof($this->object->aff_meta)) {
            foreach ($this->object->aff_meta as $meta) {
                $this->fields_form['input'][] = array(
                    'type' => 'text',
                    'label' => sprintf($this->l('Meta: %s'), $meta['name']),
                    'name' => 'custom_field_'.$meta['id_field'],
                    'required' => false,
                );
            }
        }
        $this->fields_form['submit'] = array(
            'title' => $this->l('Save'),
        );
        if (isset($this->object->aff_meta) && sizeof($this->object->aff_meta)) {
            foreach ($this->object->aff_meta as $meta) {
                $cf_name = 'custom_field_'.$meta['id_field'];
                $this->object->$cf_name = $meta['value'];
            }
        }
        $this->fields_value = (array)$this->object;

        return parent::renderForm();
    }

    public function processAdd()
    {
        if (Tools::getValue('email')) {
            $email = Tools::getValue('email');
            $num_rows = Db::getInstance()->getValue('SELECT COUNT(*) FROM `'._DB_PREFIX_.'aff_affiliates` WHERE `email`="'.pSQL($email).'"');
            if ($num_rows) {
                $this->errors[] = sprintf(
                    Tools::displayError('The email address %s is already registered as an affiliate'),
                    pSQL($email)
                );
            }
        }
        if (Tools::getValue('id_customer')) {
            $id_customer = (int)Tools::getValue('id_customer');
            $num_rows = Db::getInstance()->getValue('SELECT COUNT(*) FROM `'._DB_PREFIX_.'aff_affiliates` WHERE `id_customer`="'.(int)$id_customer.'"');
            if ($num_rows) {
                $this->errors[] = sprintf(
                    Tools::displayError('Customer #%s is already registered as an affiliate'),
                    (int)$id_customer
                );
            }
        }

        return parent::processAdd();
    }

    public function processUpdate()
    {
        $id_affiliate = (int)$this->object->id;
        if (Tools::getValue('id_customer')) {
            $id_customer = Tools::getValue('id_customer');
            $num_rows = Db::getInstance()->getValue('SELECT COUNT(*) FROM `'._DB_PREFIX_.'aff_affiliates` WHERE `id_customer`="'.(int)$id_customer.'" AND `id_affiliate` != "'.(int)$id_affiliate.'"');
            if ($num_rows) {
                $this->errors[] = sprintf(
                    Tools::displayError('Customer #%s is already registered as an affiliate'),
                    $id_customer
                );
            }
        }
        if (Tools::getValue('email')) {
            $email = Tools::getValue('email');
            $num_rows = Db::getInstance()->getValue('SELECT COUNT(*) FROM `'._DB_PREFIX_.'aff_affiliates` WHERE `email`="'.pSQL($email).'" AND `id_affiliate` != "'.(int)$id_affiliate.'"');
            if ($num_rows) {
                $this->errors[] = sprintf(
                    Tools::displayError('The email address %s is already registered as an affiliate'),
                    $email
                );
            }
        }

        /* @TODO send approval / disapproval email to customer and admin */
        return parent::processUpdate();
    }

    public function displayDeleteLink($token = null, $id_affiliate = 0, $name = null)
    {
        $tpl = $this->createTemplate('helpers/list/list_action_delete.tpl');

        $affiliate = new Affiliate($id_affiliate);
        $name = $affiliate->firstname.' '.$affiliate->lastname;
        $name = '\n\n'.$this->l('Name:', 'helper').' '.$name;

        $tpl->assign(array(
            'href' => self::$currentIndex.'&'.$this->identifier.'='.$id_affiliate.'&delete'.$this->table.'&token='.($token != null ? $token : $this->token),
            'confirm' => $this->l('Deleting an affiliate will erase all data about his sales, traffic, payments and so on. Are you sure you want to do that? Instead deleting the affiliates you could just disable them.').$name,
            'action' => $this->l('Delete'),
            'id' => $id_affiliate,
        ));

        return $tpl->fetch();
    }

    public function l($string, $class = null, $addslashes = false, $htmlentities = true)
    {
        return Translate::getModuleTranslation('psaffiliate', $string, get_class($this));
    }
}
