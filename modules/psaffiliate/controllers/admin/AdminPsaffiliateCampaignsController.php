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

class AdminPsaffiliateCampaignsController extends AdminController
{
    public $module;
    public $fields_list;
    protected $_defaultOrderBy = 'id_campaign';
    protected $_defaultOrderWay = 'DESC';
    public $id_campaign = 0;
    public $obj;

    public function __construct()
    {
        $this->moduleObj = Module::getInstanceByName('psaffiliate');
        $this->moduleObj->loadClasses('Campaign');
        if (Tools::getValue('id_campaign')) {
            $this->obj = new Campaign((int)Tools::getValue('id_campaign'));
        }

        $this->bootstrap = true;
        $this->required_database = false;
        $this->table = 'aff_campaigns';
        $this->identifier = 'id_campaign';
        $this->className = 'Campaign';
        $this->lang = false;
        $this->explicitSelect = true;

        $this->allow_export = true;

        $this->addRowAction('view');
        $this->addRowAction('edit');
        $this->addRowAction('delete');
        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'confirm' => $this->l('Delete selected items?'),
                'icon' => 'icon-trash',
            ),
        );

        $this->context = Context::getContext();

        $this->default_form_language = $this->context->language->id;

        $this->_use_found_rows = false;
        $this->fields_list = array(
            'id_campaign' => array(
                'title' => $this->l('Campaign ID'),
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
                'search' => true,
                'filter_type' => 'int',
                'filter_key' => 'a!id_campaign',
            ),
            'affiliate_name' => array(
                'title' => $this->l('Affiliate'),
                'align' => 'text-left',
                'width' => 'auto',
                'search' => true,
                //'havingFilter' => true,
                'type' => "select",
                'list' => $this->moduleObj->getAffiliatesList(),
                'filter_key' => 'a!id_affiliate',
            ),
            'name' => array(
                'title' => $this->l('Name'),
                'align' => 'text-left',
                'width' => 'auto',
                'search' => true,
                'filter_key' => 'a!name',
                'callback' => 'strip_tags',
            ),
            'date_created' => array(
                'title' => $this->l('Date created'),
                'align' => 'text-center',
                'orderby' => false,
                'filter_key' => 'a!date_created',
                'type' => 'datetime',
            ),
            'date_lastactive' => array(
                'title' => $this->l('Date last earnings'),
                'align' => 'text-center',
                'orderby' => false,
                'filter_key' => 'a!date_lastactive',
                'type' => 'datetime',
            ),
            'clicks' => array(
                'title' => $this->l('Clicks'),
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
                'search' => true,
                'filter_type' => 'int',
                'havingFilter' => true,
            ),
            'sales' => array(
                'title' => $this->l('Approved Sales'),
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
                'search' => true,
                'filter_type' => 'int',
                'havingFilter' => true,
            ),
            'earnings' => array(
                'title' => $this->l('Earnings'),
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
                'search' => true,
                'type' => 'price',
                'filter_type' => 'int',
                'havingFilter' => true,
            ),
        );

        $this->_join = 'LEFT JOIN `'._DB_PREFIX_.'aff_affiliates` af on (af.`id_affiliate`=a.`id_affiliate`)';
        $this->_join .= ' LEFT JOIN `'._DB_PREFIX_.'customer` c ON (c.`id_customer` = af.`id_customer`)';
        $this->_select = '
            CONCAT("#", af.`id_affiliate`, " - ", IF(af.`id_customer` <> 0, CONCAT(c.`firstname`, " ", c.`lastname`), CONCAT(af.`firstname`, " ", af.`lastname`))) as `affiliate_name`,
            (SELECT COUNT(*) FROM `'._DB_PREFIX_.'aff_tracking` WHERE `id_campaign`=a.`id_campaign`) as `clicks`,
            CONCAT((SELECT COUNT(*) FROM `'._DB_PREFIX_.'aff_sales` WHERE `id_campaign`=a.`id_campaign` AND `approved`="1"), "/", (SELECT COUNT(*) FROM `'._DB_PREFIX_.'aff_sales` WHERE `id_campaign`=a.`id_campaign`)) as `sales`,
            (SELECT IFNULL(SUM(`commission`), 0) FROM `'._DB_PREFIX_.'aff_sales` WHERE `id_campaign`=a.`id_campaign` AND `approved`="1")+(SELECT IFNULL(SUM(`commission`), 0) FROM `'._DB_PREFIX_.'aff_tracking` WHERE `id_campaign`=a.`id_campaign`) as `earnings`
            
        ';

        $this->shopLinkType = '';

        parent::__construct();
    }

    public function init()
    {
        parent::init();
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
        if (Tools::getValue('action') == "getCampaignsOfAffiliate") {
            $data = array();
            $data['data'] = $this->moduleObj->getCampaignsList(Tools::getValue('id_affiliate'));
            die(Tools::jsonEncode($data));
        }
        parent::initContent();

        $this->meta_title = $this->l('Campaigns');
    }

    public function renderList()
    {
        return parent::renderList();
    }

    public function renderView()
    {
        $this->moduleObj->loadClasses(array('Affiliate', 'Tracking', 'Sale'));

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

        $campaign = new Campaign($this->obj->id);
        $affiliate = new Affiliate($this->obj->id_affiliate);
        $viewaff_token = Tools::getAdminTokenLite('AdminPsaffiliateAffiliates');
        $traffic = Tracking::getCampaignTraffic($this->obj->id, 10);
        $sales = Sale::getCampaignSales($this->obj->id, 10);
        $this->context->smarty->assign(array(
            'campaign' => (array)$campaign,
            'affiliate' => (array)$affiliate,
            'viewaff_token' => $viewaff_token,
            'traffic' => $traffic,
            'sales' => $sales,
        ));
        $display = $this->context->smarty->fetch(_PS_MODULE_DIR_.'psaffiliate/views/templates/admin/campaign-view.tpl');
        if (isset($script)) {
            $display .= PHP_EOL.$script;
        }

        return parent::renderView().$display;
    }

    public function renderForm()
    {
        $this->moduleObj->loadClasses(array('Campaign', 'Affiliate'));
        $this->fields_form = array(
            'legend' => array(
                'title' => isset($this->obj->id) ? $this->l('Campaign')." #".$this->obj->id : $this->l('Campaign'),
                'icon' => 'icon-bullhorn',
            ),
            'input' => array(
                array(
                    'type' => 'select',
                    'label' => $this->l('Affiliate'),
                    'name' => 'id_affiliate',
                    'class' => 'ajaxselectpicker',
                    'col' => '4',
                    'options' => array(
                        'query' => Affiliate::getAffiliates(
                            false,
                            false,
                            false,
                            false,
                            false,
                            (isset($this->obj) ? $this->obj->id_affiliate : 0)
                        ),
                        'id' => 'id_affiliate',
                        'name' => 'idandname',
                    ),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Name'),
                    'name' => 'name',
                    'col' => '4',
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->l('Description'),
                    'name' => 'description',
                    'col' => '4',
                ),
                array(
                    'type' => 'datetime',
                    'label' => $this->l('Date created'),
                    'name' => 'date_created',
                    'col' => '4',
                    'autocomplete' => false,
                ),
                array(
                    'type' => 'datetime',
                    'label' => $this->l('Date last active'),
                    'name' => 'date_lastactive',
                    'col' => '4',
                    'autocomplete' => false,
                ),
            ),
        );
        $this->fields_form['submit'] = array(
            'title' => $this->l('Save'),
        );
        $this->fields_value = (array)$this->obj;

        return parent::renderForm();
    }

    public function strip_tags($str)
    {
        return strip_tags($str);
    }

    public function l($string, $class = null, $addslashes = false, $htmlentities = true)
    {
        return Translate::getModuleTranslation('psaffiliate', $string, get_class($this));
    }
}
