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

class AdminPsaffiliateTrafficController extends AdminController
{
    public $module;
    public $fields_list;
    protected $_defaultOrderBy = 'id_tracking';
    protected $_defaultOrderWay = 'DESC';
    public $id;
    public $id_tracking = 0;
    public $id_affiliate = 0;
    public $id_customer = 0;
    public $ip;
    public $unique_visit;
    public $date;
    public $referral;
    public $url;
    public $commission;

    public $obj;

    public function __construct()
    {
        $this->moduleObj = Module::getInstanceByName('psaffiliate');
        $this->moduleObj->loadClasses(array('Tracking', 'Affiliate'));
        if (Tools::getValue('id_tracking')) {
            $this->obj = new Tracking((int)Tools::getValue('id_tracking'));
        }

        $this->bootstrap = true;
        $this->required_database = false;
        $this->table = 'aff_tracking';
        $this->identifier = 'id_tracking';
        $this->className = 'Tracking';
        $this->lang = false;
        $this->explicitSelect = true;

        $this->allow_export = true;

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
            'id_tracking' => array(
                'title' => $this->l('Tracking ID'),
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
                'search' => true,
                'filter_type' => 'int',
                'filter_key' => 'a!id_tracking',
            ),
            'affiliate_name' => array(
                'title' => $this->l('Affiliate'),
                'align' => 'text-left',
                'width' => 'auto',
                'search' => true,
                'type' => 'select',
                'list' => $this->moduleObj->getAffiliatesList(),
                'filter_key' => 'a!id_affiliate',
            ),
            'campaign_name' => array(
                'title' => $this->l('Campaign'),
                'align' => 'text-left',
                'width' => 'auto',
                'search' => true,
                'type' => 'select',
                'list' => $this->moduleObj->getCampaignsList(),
                'filter_key' => 'a!id_campaign',
            ),
            'ip' => array(
                'title' => $this->l('IP'),
                'align' => 'text-left',
                'width' => 'auto',
                'search' => true,
            ),
            'unique_visit' => array(
                'title' => $this->l('Unique'),
                'align' => 'text-center',
                'active' => 'unique_visit',
                'type' => 'bool',
                'orderby' => false,
                'filter_key' => 'a!unique_visit',
            ),
            'date' => array(
                'title' => $this->l('Date'),
                'align' => 'text-left',
                'width' => 'auto',
                'search' => true,
                'type' => 'datetime',
            ),
            'referral' => array(
                'title' => $this->l('Referral URL'),
                'align' => 'text-left',
                'width' => 'auto',
                'search' => true,
            ),
            'url' => array(
                'title' => $this->l('URL'),
                'align' => 'text-left',
                'width' => 'auto',
                'search' => true,
            ),
            'commission' => array(
                'title' => $this->l('Commission'),
                'align' => 'text-left',
                'width' => 'auto',
                'search' => true,
                'filter_type' => 'float',
                'type' => 'price',
            ),
        );


        $this->_join = 'LEFT JOIN `'._DB_PREFIX_.'aff_affiliates` af on (af.`id_affiliate`=a.`id_affiliate`)';
        $this->_join .= ' LEFT JOIN `'._DB_PREFIX_.'customer` c ON (c.`id_customer` = af.`id_customer`)';
        $this->_join .= ' LEFT JOIN `'._DB_PREFIX_.'aff_campaigns` ca ON (ca.`id_campaign` = a.`id_campaign`)';
        $this->_select = '
            CONCAT("#", af.`id_affiliate`, " - ", IF(af.`id_customer` <> 0, CONCAT(c.`firstname`, " ", c.`lastname`), CONCAT(af.`firstname`, " ", af.`lastname`))) as `affiliate_name`, IF(a.`id_campaign`<>0, CONCAT("#", ca.`id_campaign`, " - ", ca.`name`), "--") as `campaign_name`
            
        ';
        if (Tools::getValue('id_affiliate')) {
            $this->_where = ' AND a.`id_affiliate`="'.(int)Tools::getValue('id_affiliate').'"';
        }
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
        if (Tools::isSubmit('uniqueaff_tracking') !== false) {
            $this->obj->toggleUnique();
        }
        parent::initContent();

        $this->meta_title = $this->l('Traffic');
    }

    public function renderList()
    {
        return parent::renderList();
    }

    public function renderView()
    {
        return parent::renderView();
    }

    public function renderForm()
    {
        $this->context->controller->addJs($this->moduleObj->getPath().'views/js/admin/campaign.js');
        $link = new Link;
        $js_def = array('campaigns_controller_link' => $link->getAdminLink('AdminPsaffiliateCampaigns'));
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

        $this->moduleObj->loadClasses('Affiliate');
        $id_currency = Configuration::get('PS_CURRENCY_DEFAULT');
        $currency = new Currency($id_currency);
        $this->fields_form = array(
            'legend' => array(
                'title' => isset($this->obj->id) ? $this->l('Tracking')." #".$this->obj->id : $this->l('Tracking'),
                'icon' => 'icon-user',
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
                    'type' => 'select',
                    'label' => $this->l('Campaign'),
                    'name' => 'id_campaign',
                    'col' => '4',
                    'options' => array(
                        'query' => isset($this->obj) ? $this->moduleObj->getCampaignsList(
                            $this->obj->id_affiliate,
                            true
                        ) : array(),
                        'id' => 'id',
                        'name' => 'value',
                    ),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('IP Address'),
                    'name' => 'ip',
                    'col' => '4',
                    'autocomplete' => false,
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Unique'),
                    'name' => 'unique_visit',
                    'required' => false,
                    'class' => 't',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'unique_visit_on',
                            'value' => 1,
                            'label' => $this->l('Yes'),
                        ),
                        array(
                            'id' => 'unique_visit_off',
                            'value' => 0,
                            'label' => $this->l('No'),
                        ),
                    ),
                    'hint' => $this->l('Is the click unique?'),
                ),
                array(
                    'type' => 'datetime',
                    'label' => $this->l('Date'),
                    'name' => 'date',
                    'col' => '4',
                    'autocomplete' => false,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Referral URL'),
                    'name' => 'referral',
                    'col' => '4',
                    'autocomplete' => false,
                ),
                array(
                    'type' => 'text',

                    'label' => $this->l('URL'),
                    'name' => 'url',
                    'col' => '4',
                    'autocomplete' => false,
                ),
                array(
                    'type' => 'text',
                    'suffix' => $currency->iso_code,
                    'label' => $this->l('Commission'),
                    'name' => 'commission',
                    'col' => '4',
                    'autocomplete' => false,
                ),
            ),
        );
        $this->fields_form['submit'] = array(
            'title' => $this->l('Save'),
        );
        $this->fields_value = (array)$this->obj;
        $display = parent::renderForm();
        if (isset($script)) {
            $display .= PHP_EOL.$script;
        }

        return $display;
    }

    public function l($string, $class = null, $addslashes = false, $htmlentities = true)
    {
        return Translate::getModuleTranslation('psaffiliate', $string, get_class($this));
    }
}
