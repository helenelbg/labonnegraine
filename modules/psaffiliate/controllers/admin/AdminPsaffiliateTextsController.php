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

class AdminPsaffiliateTextsController extends AdminController
{
    public $module;
    public $fields_list;
    protected $_defaultOrderBy = 'id_text';
    protected $_defaultOrderWay = 'ASC';
    public $id_text = 0;
    public $obj;

    public function __construct()
    {
        $this->moduleObj = Module::getInstanceByName('psaffiliate');
        $this->moduleObj->loadClasses('Text');
        if (Tools::getValue('id_text')) {
            $this->obj = new Text((int)Tools::getValue('id_text'));
        }

        $this->bootstrap = true;
        $this->required_database = false;
        $this->table = 'aff_texts';
        $this->identifier = 'id_text';
        $this->className = 'Text';
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
            'id_text' => array(
                'title' => $this->l('Text ID'),
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
                'search' => true,
                'filter_type' => 'int',
                'filter_key' => 'a!id_text',
            ),
            'title' => array(
                'title' => $this->l('Title'),
                'align' => 'text-left',
                'width' => 'auto',
                'search' => true,
            ),
            'text' => array(
                'title' => $this->l('Text'),
                'align' => 'text-left',
                'width' => 'auto',
                'search' => true,
                'callback' => 'strip_tags',
            ),
            'active' => array(
                'title' => $this->l('Active'),
                'align' => 'text-center',
                'active' => 'enabled',
                'type' => 'bool',
                'orderby' => false,
                'filter_key' => 'a!active',
            ),
        );

        $this->shopLinkType = '';

        parent::__construct();
    }

    public function initContent()
    {
        if (Tools::isSubmit('enabledaff_texts') !== false) {
            $this->obj->toggleActive();
        }
        parent::initContent();

        $this->meta_title = $this->l('Text Ads');
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
        $this->moduleObj->loadClasses('Text');
        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Text'),
                'icon' => 'icon-archive',
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Title'),
                    'name' => 'title',
                    'col' => '4',
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->l('Text'),
                    'name' => 'text',
                    'col' => '4',
                    'autoload_rte' => true,
                    'desc' => $this->l('Variables: %link%, %site_name%'),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Active'),
                    'name' => 'active',
                    'required' => false,
                    'class' => 't',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Yes'),
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('No'),
                        ),
                    ),
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
