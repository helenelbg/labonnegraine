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

class AdminPsaffiliateBannersController extends AdminController
{
    public $module;
    public $fields_list;
    protected $_defaultOrderBy = 'id_banner';
    protected $_defaultOrderWay = 'ASC';
    public $id_banner = 0;
    public $obj;

    public function __construct()
    {
        $this->moduleObj = Module::getInstanceByName('psaffiliate');
        $this->moduleObj->loadClasses('Banner');
        if (Tools::getValue('id_banner')) {
            $this->obj = new Banner((int)Tools::getValue('id_banner'));
        }

        $this->bootstrap = true;
        $this->required_database = false;
        $this->table = 'aff_banners';
        $this->identifier = 'id_banner';
        $this->className = 'Banner';
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
            'id_banner' => array(
                'title' => $this->l('Banner ID'),
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
                'search' => true,
                'filter_type' => 'int',
                'filter_key' => 'a!id_banner',
            ),
            'title' => array(
                'title' => $this->l('Title'),
                'align' => 'text-left',
                'width' => 'auto',
                'search' => true,
            ),
            'image' => array(
                'title' => $this->l('Banner'),
                'align' => 'text-left',
                'width' => 'auto',
                'prefix' => '<img height="80" src="'._PS_BASE_URL_SSL_._MODULE_DIR_.'psaffiliate/views/img/banners/',
                'suffix' => '"/>',
                'orderby' => false,
                'filter' => false,
                'search' => false,
                'filter_key' => 'a!image',
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
        if (Tools::isSubmit('enabledaff_banners') !== false) {
            $this->obj->toggleActive();
        } elseif (Tools::isSubmit('deleteImage') !== false) {
            $this->obj->deleteImage();
        }
        parent::initContent();

        $this->meta_title = $this->l('Banners');
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
        $this->moduleObj->loadClasses('Banner');

        $this->fields_value = (array)$this->obj;
        $image_url = false;
        $image_size = false;
        if (isset($this->fields_value['image']) && $this->fields_value['image']) {
            $image = _PS_MODULE_DIR_.'psaffiliate/views/img/banners/'.$this->fields_value['image'];
            if ($image && file_exists($image)) {
                $image_url = ImageManager::thumbnail(
                    $image,
                    'thumbnail_'.$this->fields_value['image'],
                    350,
                    'png',
                    true,
                    true
                );
                $image_size = file_exists($image) ? filesize($image) / 1000 : false;
            }
        }
        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Banner'),
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
                    'type' => 'file',
                    'label' => $this->l('Banner'),
                    'name' => 'image',
                    'col' => '4',
                    'size' => $image_size,
                    'delete_url' => (isset($this->obj)) ? self::$currentIndex.'&'.$this->identifier.'='.$this->obj->id.'&token='.$this->token.'&deleteImage=1' : "",
                    'display_image' => true,
                    'image' => $image_url ? $image_url : false,
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

        return parent::renderForm();
    }

    protected function processBulkDelete()
    {
        if (is_array($this->boxes) && !empty($this->boxes)) {
            foreach ($this->boxes as $id_banner) {
                $banner = new Banner($id_banner);
                $banner->deleteImage();
            }
        }

        return parent::processBulkDelete();
    }

    public function validateRules($class_name = false)
    {
        if (isset($_FILES['image']) && isset($_FILES['image']['tmp_name']) && !empty($_FILES['image']['tmp_name'])) {
            $error = ImageManager::validateUpload($_FILES['image'], 4000000);
            if ($error !== false) {
                $this->errors[] = $error;
            }
        }

        parent::validateRules($class_name);
    }

    public function l($string, $class = null, $addslashes = false, $htmlentities = true)
    {
        return Translate::getModuleTranslation('psaffiliate', $string, get_class($this));
    }
}
