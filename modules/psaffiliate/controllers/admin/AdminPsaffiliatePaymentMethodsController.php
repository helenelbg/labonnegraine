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

class AdminPsaffiliatePaymentMethodsController extends AdminController
{
    public $module;
    public $fields_list;
    protected $_defaultOrderBy = 'id_payment_method';
    protected $_defaultOrderWay = 'ASC';
    public $id_payment_method = 0;
    public $obj;

    public function __construct()
    {
        $this->moduleObj = Module::getInstanceByName('psaffiliate');
        $this->bootstrap = true;
        $this->moduleObj->loadClasses('PaymentMethod');
        if (Tools::getValue('id_payment_method')) {
            $this->obj = new PaymentMethod((int)Tools::getValue('id_payment_method'));
        }

        $this->required_database = false;
        $this->table = 'aff_payment_methods';
        $this->identifier = 'id_payment_method';
        $this->className = 'PaymentMethod';
        $this->lang = false;
        $this->explicitSelect = false;

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
            'id_payment_method' => array(
                'title' => $this->l('ID'),
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
                'search' => true,
                'filter_type' => 'int',
                'filter_key' => 'a!id_payment_method',
            ),
            'name' => array(
                'title' => $this->l('Name'),
                'align' => 'text-left',
                'width' => 'auto',
                'search' => true,
            ),
            'description' => array(
                'title' => $this->l('Description'),
                'align' => 'text-left',
                'width' => 'auto',
                'search' => true,
            ),
            'fields' => array(
                'title' => $this->l('Fields'),
                'align' => 'text-left',
                'width' => 'auto',
                'search' => false,
            ),
        );
        $this->_select = '(SELECT IFNULL(GROUP_CONCAT(DISTINCT pmf.`field_name` SEPARATOR ", "), "No fields") FROM `'._DB_PREFIX_.'aff_payment_methods_fields` pmf WHERE a.`id_payment_method`=pmf.`id_payment_method`) fields';

        $this->shopLinkType = '';

        parent::__construct();
    }

    public function initContent()
    {
        parent::initContent();

        $this->meta_title = $this->l('Payment Methods');
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
        $fields = array();
        if (Tools::getValue('id_payment_method')) {
            $id_payment_method = (int)Tools::getValue('id_payment_method');
            $paymentMethod = new PaymentMethod($id_payment_method);
            $fields = $paymentMethod->getPaymentMethodFields();
        }
        $this->context->controller->addJs($this->moduleObj->getPath().'views/js/admin/paymentmethod.js');
        $js_def = array('payment_method_fields' => Tools::jsonEncode($fields));
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
        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Payment Method'),
                'icon' => 'icon-user',
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Name'),
                    'name' => 'name',
                    'col' => '4',
                    'autocomplete' => false,
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->l('Description'),
                    'name' => 'description',
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

    public function postProcess()
    {
        if (Tools::getValue('payment_method_field')) {
            $payment_method_field = Tools::getValue('payment_method_field');
        } else {
            $payment_method_field = false;
        }
        parent::postProcess();
        $id_payment_method = (int)Tools::getValue('id_payment_method');
        if (Tools::isSubmit('submitAddaff_payment_methods')) {
            if ($payment_method_field) {
                if (is_array($payment_method_field) && sizeof($payment_method_field)) {
                    $db = Db::getInstance();
                    foreach ($payment_method_field as $key => $name) {
                        if ($key != "new") {
                            $name = trim($name);
                            if ($name != "") {
                                $data = array();
                                $data['field_name'] = pSQL($name);
                                $db->update(
                                    'aff_payment_methods_fields',
                                    $data,
                                    'id_payment_method_field="'.pSQL($key).'"'
                                );
                            } else {
                                $db->delete(
                                    'aff_payment_methods_fields',
                                    'id_payment_method_field="'.pSQL($key).'"',
                                    1
                                );
                            }
                        } else {
                            foreach ($name as $field_name) {
                                $field_name = trim($field_name);
                                if ($field_name != "") {
                                    $data = array();
                                    $data['id_payment_method'] = (int)$id_payment_method;
                                    $data['field_name'] = pSQL($field_name);
                                    $db->insert('aff_payment_methods_fields', $data);
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    public function l($string, $class = null, $addslashes = false, $htmlentities = true)
    {
        return Translate::getModuleTranslation('psaffiliate', $string, get_class($this));
    }
}
