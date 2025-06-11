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

class AdminPsaffiliateSalesController extends AdminController
{
    public $module;
    public $fields_list;
    protected $_defaultOrderBy = 'id_sale';
    protected $_defaultOrderWay = 'DESC';
    public $id_sale = 0;
    public $order_total = 0;
    public $obj;

    public function __construct()
    {
        $this->moduleObj = Module::getInstanceByName('psaffiliate');
        $this->moduleObj->loadClasses('Sale');
        if (Tools::getValue('id_sale')) {
            $this->obj = new Sale((int)Tools::getValue('id_sale'));
        }

        $this->bootstrap = true;
        $this->required_database = false;
        $this->table = 'aff_sales';
        $this->identifier = 'id_sale';
        $this->className = 'Sale';
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
            'id_sale' => array(
                'title' => $this->l('Sale ID'),
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
                'search' => true,
                'filter_type' => 'int',
                'filter_key' => 'a!id_sale',
            ),
            'id_order' => array(
                'title' => $this->l('Order ID'),
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
                'search' => true,
                'filter_type' => 'int',
                'filter_key' => 'a!id_order',
            ),
            'date' => array(
                'title' => $this->l('Date'),
                'search' => true,
                'type' => 'datetime',
                'filtery_key' => 'a!date',
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
            'campaign_name' => array(
                'title' => $this->l('Campaign'),
                'align' => 'text-left',
                'width' => 'auto',
                'search' => true,
                //'havingFilter' => true,
                'type' => "select",
                'list' => $this->moduleObj->getCampaignsList(),
                'filter_key' => 'a!id_campaign',
            ),
            'commission' => array(
                'title' => $this->l('Commission'),
                'align' => 'text-left',
                'width' => 'auto',
                'search' => true,
                'filter_type' => 'float',
                'type' => 'price',
            ),
            'order_total' => array(
                'title' => $this->l('Order total'),
                'align' => 'text-left',
                'width' => 'auto',
                'search' => true,
                'filter_type' => 'float',
                'type' => 'price',
                'havingFilter' => true,
            ),
            'approved' => array(
                'title' => $this->l('Approved'),
                'align' => 'text-center',
                'active' => 'approved',
                'type' => 'bool',
                'orderby' => false,
                'filter_key' => 'a!approved',
            ),
        );

        $this->_join = 'LEFT JOIN `'._DB_PREFIX_.'aff_affiliates` af on (af.`id_affiliate`=a.`id_affiliate`)';
        $this->_join .= ' LEFT JOIN `'._DB_PREFIX_.'customer` c ON (c.`id_customer` = af.`id_customer`)';
        $this->_join .= ' LEFT JOIN `'._DB_PREFIX_.'orders` o ON (a.`id_order`=o.`id_order`)';
        $this->_join .= ' LEFT JOIN `'._DB_PREFIX_.'aff_campaigns` ca ON (ca.`id_campaign` = a.`id_campaign`)';
        $this->_select = '
            CONCAT("#", af.`id_affiliate`, " - ", IF(af.`id_customer` <> 0, CONCAT(c.`firstname`, " ", c.`lastname`), CONCAT(af.`firstname`, " ", af.`lastname`))) as `affiliate_name`,
            (o.`total_paid_tax_incl` / o.`conversion_rate`) as `order_total`, IF(a.`id_campaign`<>0, CONCAT("#", ca.`id_campaign`, " - ", ca.`name`), "--") as `campaign_name`
        ';
        $this->shopLinkType = '';

        if (Tools::getValue('id_sale')) {
            $sql = 'SELECT (`total_paid_tax_incl` / `conversion_rate`) as `order_total` FROM `'._DB_PREFIX_.'orders` WHERE `id_order`=(SELECT `id_order` FROM `'._DB_PREFIX_.'aff_sales` WHERE `id_sale`="'.(int)Tools::getValue('id_sale').'")';
            $this->obj->order_total = number_format(Db::getInstance()->getValue($sql), 2);
        }

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
        if (Tools::isSubmit('approvedaff_sales') !== false) {
            $this->obj->toggleApproved();
        }
        parent::initContent();

        $this->meta_title = $this->l('Sales');
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
                'title' => isset($this->obj->id) ? $this->l('Sale')." #".$this->obj->id : $this->l('Sale'),
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
                    'type' => 'select',
                    'label' => $this->l('Order'),
                    'name' => 'id_order',
                    'class' => 'ajaxselectpicker',
                    'col' => '4',
                    'options' => array(
                        'query' => Sale::getAllOrders((isset($this->obj) && $this->obj) ? $this->obj->id_order : 0),
                        'id' => 'id_order',
                        'name' => 'name',
                    ),
                ),
                array(
                    'type' => 'text',
                    'suffix' => $currency->iso_code,
                    'label' => $this->l('Commission'),
                    'name' => 'commission',
                    'col' => '4',
                    'autocomplete' => false,
                ),
                array(
                    'type' => 'text',
                    'suffix' => $currency->iso_code,
                    'label' => $this->l('Order total'),
                    'name' => 'order_total',
                    'col' => '4',
                    'autocomplete' => false,
                    'disabled' => 'disabled',
                    'hint' => 'Total without shipping (taxes included)',
                    'desc' => (isset($this->obj) && $this->obj->id_order) ? $this->l('You can see the order details')." <a target='_blank' href='".Context::getContext()->link->getAdminLink(
                            'AdminOrders',
                            false
                        )
                        .'&id_order='.$this->obj->id_order.'&vieworder&token='.Tools::getAdminTokenLite('AdminOrders')."'>".$this->l('here').'</a>' : "",
                ),
                array(
                    'type' => 'datetime',
                    'label' => $this->l('Date'),
                    'name' => 'date',
                    'col' => '4',
                    'autocomplete' => false,
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Approved'),
                    'name' => 'approved',
                    'required' => false,
                    'class' => 't',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'approved_on',
                            'value' => 1,
                            'label' => $this->l('Yes'),
                        ),
                        array(
                            'id' => 'approved_off',
                            'value' => 0,
                            'label' => $this->l('No'),
                        ),
                    ),
                    'hint' => $this->l('Approve or disapprove sale commission'),
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

    public function processUpdate()
    {
        $id_sale = (int)$this->obj->id;
        $id_order = (int)Tools::getValue('id_order');
        if ($id_order) {
            $num_rows = Db::getInstance()->getValue('SELECT COUNT(*) FROM `'._DB_PREFIX_.'aff_sales` WHERE `id_order`="'.(int)$id_order.'" AND `id_sale` != "'.(int)$id_sale.'"');
            if ($num_rows) {
                $this->errors[] = sprintf(
                    Tools::displayError('There already is a commission assigned to order #%s'),
                    $id_order
                );
            }
        }

        parent::processUpdate();
    }

    public function processAdd()
    {
        $id_order = (int)Tools::getValue('id_order');
        if ($id_order) {
            $num_rows = Db::getInstance()->getValue('SELECT COUNT(*) FROM `'._DB_PREFIX_.'aff_sales` WHERE `id_order`="'.(int)$id_order.'"');
            if ($num_rows) {
                $this->errors[] = sprintf(
                    Tools::displayError('There already is a commission assigned to order #%s'),
                    $id_order
                );
            }
        }

        parent::processAdd();
    }

    public function l($string, $class = null, $addslashes = false, $htmlentities = true)
    {
        return Translate::getModuleTranslation('psaffiliate', $string, get_class($this));
    }
}
