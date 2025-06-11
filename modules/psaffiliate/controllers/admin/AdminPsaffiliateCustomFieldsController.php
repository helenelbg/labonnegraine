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

class AdminPsaffiliateCustomFieldsController extends AdminController
{
    public $module;
    public $bootstrap = true;
    public $required_database = false;
    public $table = 'aff_custom_fields';
    public $identifier = 'id_field';
    public $className = 'Customfield';
    public $lang = true;
    public $explicitSelect = true;
    public $allow_export = true;
    public $shopLinkType = '';
    public $context;
    protected $_defaultOrderBy = 'id_field';
    protected $_defaultOrderWay = 'ASC';
    protected $_use_found_rows = false;

    public function __construct()
    {
        $this->context = Context::getContext();
        $this->module = Module::getInstanceByName('psaffiliate');
        $this->module->loadClasses('Customfield');
        $this->default_form_language = $this->context->language->id;

        if ($id_field = (int)Tools::getValue('id_field')) {
            $this->obj = new Customfield($id_field);
        } else {
            $this->obj = null;
        }

        $this->fields_list = array(
            'id_field' => array(
                'title' => $this->l('ID'),
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
                'search' => true,
                'filter_type' => 'int',
                'filter_key' => 'a!id_field',
            ),
            'type' => array(
                'title' => $this->l('Type'),
                'align' => 'text-left',
                'class' => 'fixed-width-xs',
                'search' => true,
                'type' => 'select',
                'list' => array('text' => 'text', 'textarea' => 'textarea', 'link' => 'link'),
                'filter_key' => 'a!type',
            ),
            'name' => array(
                'title' => $this->l('Name'),
                'align' => 'text-left',
                'width' => 'auto',
                'search' => true,
                'havingFilter' => true,
            ),
            'required' => array(
                'title' => $this->l('Required'),
                'align' => 'text-center',
                'active' => 'required',
                'type' => 'bool',
                'orderby' => false,
                'filter_key' => 'a!required',
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

        $this->addRowAction('edit');
        $this->addRowAction('delete');

        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'icon' => 'icon-trash',
            ),
        );

        parent::__construct();

        $this->_conf[] = $this->l('The required status has been successfully updated.');
    }

    public function renderForm()
    {
        $this->fields_form = array(
            'legend' => array(
                'title' => end($this->toolbar_title),
                'icon' => 'icon-cog',
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'name' => 'name',
                    'label' => $this->l('Name'),
                    'col' => '4',
                    'lang' => true,
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Type'),
                    'name' => 'type',
                    'col' => '4',
                    'options' => array(
                        'query' => array(
                            array('id' => 'text', 'name' => 'text'),
                            array('id' => 'textarea', 'name' => 'textarea'),
                            array('id' => 'link', 'name' => 'link'),
                        ),
                        'id' => 'id',
                        'name' => 'name',
                    ),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Required'),
                    'name' => 'required',
                    'required' => false,
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
                array(
                    'type' => 'switch',
                    'label' => $this->l('Enabled'),
                    'name' => 'active',
                    'required' => false,
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
            'submit' => array('title' => $this->l('Save')),
        );

        $this->fields_value = (array)$this->obj;

        return parent::renderForm();
    }

    public function initProcess()
    {
        if (Tools::getValue('required'.$this->table) !== false && Tools::getValue($this->identifier)) {
            if ($this->access('edit')) {
                $this->action = 'required';
            } else {
                $this->errors[] = $this->l('You do not have permission to edit this.');
            }
        }

        parent::initProcess();
    }

    public function processRequired()
    {
        if ($this->obj->toggleRequired()) {
            $lastKey = key(array_slice($this->_conf, -1, 1, true));

            $this->redirect_after = self::$currentIndex.'&token='.$this->token.'&conf='.$lastKey;
        } else {
            $this->errors[] = $this->l('Could not update required status.');
        }

        return $this->obj;
    }

    public function initContent()
    {
        parent::initContent();

        $this->meta_title = $this->l('Affiliates Custom Fields');
    }

    public function l($string, $class = null, $addslashes = false, $htmlentities = true)
    {
        return Translate::getModuleTranslation('psaffiliate', $string, get_class($this));
    }
}
