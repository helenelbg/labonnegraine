<?php
/**
* 2022 - Keyrnel
*
* NOTICE OF LICENSE
*
* The source code of this module is under a commercial license.
* Each license is unique and can be installed and used on only one shop.
* Any reproduction or representation total or partial of the module, one or more of its components,
* by any means whatsoever, without express permission from us is prohibited.
* If you have not received this module from us, thank you for contacting us.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future.
*
* @author    Keyrnel
* @copyright 2022 - Keyrnel
* @license   commercial
* International Registered Trademark & Property of Keyrnel
*/
class AdminFlashSalesController extends ModuleAdminController
{
    public $items = ['product', 'category', 'manufacturer'];

    /**
     *  @var flashsales
     */
    public $module;

    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'flash_sale';
        $this->className = 'FlashSale';
        $this->display = 'view';
        $this->lang = false;
        $this->context = Context::getContext();
        $this->fieldImageSettings = [
            'name' => 'image',
            'dir' => _PS_MODULE_DIR_ . 'flashsales/views/img/banner',
        ];

        parent::__construct();
    }

    public function initPageHeaderToolbar()
    {
        parent::initPageHeaderToolbar();

        $this->page_header_toolbar_title = $this->l('Flash sales');
        $this->page_header_toolbar_btn['new_flashsale'] = [
            'href' => self::$currentIndex . '&addflash_sale&token=' . $this->token,
            'desc' => $this->l('Add new Flash Sale'),
            'icon' => 'process-icon-new',
        ];
        $this->page_header_toolbar_btn['settings'] = [
            'href' => $this->context->link->getAdminLink('AdminModules') . '&configure=' . $this->module->name . '&tab_module=' . $this->module->tab . '&module_name=' . $this->module->name,
            'desc' => $this->l('Settings'),
            'icon' => 'process-icon-configure',
        ];
    }

    public function initToolbar()
    {
        parent::initToolbar();

        $this->toolbar_btn['back']['href'] = $this->context->link->getAdminLink('AdminFlashSales');
        if ($this->display == 'edit' || $this->display == 'add') {
            unset($this->toolbar_btn['save']);
        } elseif ($this->display == 'view') {
            $this->toolbar_btn['new'] = [
                'href' => self::$currentIndex . '&addflash_sale&token=' . $this->token,
                'desc' => $this->l('Add new'),
            ];
            unset($this->toolbar_btn['back']);
        }
        $this->toolbar_btn['edit'] = [
            'href' => $this->context->link->getAdminLink('AdminModules') . '&configure=' . $this->module->name . '&tab_module=' . $this->module->tab . '&module_name=' . $this->module->name,
            'desc' => $this->l('Settings'),
        ];
        $this->toolbar_title = $this->breadcrumbs;
    }

    public function renderView()
    {
        $flash_sales = [];
        $flash_sales['active']['content'] = $this->initRenderList('active');
        $flash_sales['pending']['content'] = $this->initRenderList('pending');
        $flash_sales['expired']['content'] = $this->initRenderList('expired');

        // assign vars to context
        $this->context->smarty->assign([
            'flash_sales' => $flash_sales,
            'tpl_list' => _PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/view/list.tpl',
        ]);

        return $this->context->smarty->createTemplate(_PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/view/dashboard.tpl', $this->context->smarty)->fetch();
    }

    public function initRenderList($name = null)
    {
        $this->list_id = $name;
        $this->list_simple_header = true;
        $this->actions = ['edit', 'delete'];
        $this->bulk_actions = [
            'delete' => [
                'text' => $this->l('Delete selected'),
                'confirm' => $this->l('Are you sure that you want to delete the selected items?'),
            ],
        ];

        $this->fields_list = [
            'id_flash_sale' => [
                'title' => $this->l('ID'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
            ],
            'name' => [
                'title' => $this->l('Name'),
                'width' => 'auto',
            ],
            'shops' => [
                'title' => $this->l('Shops'),
                'class' => 'fixed-width-lg',
            ],
            'countries' => [
                'title' => $this->l('Countries'),
                'class' => 'fixed-width-lg',
            ],
            'currencies' => [
                'title' => $this->l('Currencies'),
                'class' => 'fixed-width-lg',
            ],
            'customer_groups' => [
                'title' => $this->l('Groups'),
                'class' => 'fixed-width-lg',
            ],
            'customer' => [
                'title' => $this->l('Customer'),
                'class' => 'fixed-width-lg',
            ],
            'nb' => [
                'title' => $this->l('Products'),
                'align' => 'center',
                'class' => 'fixed-width-md',
            ],
            'start' => [
                'title' => $this->l('Start'),
                'type' => 'datetime',
                'class' => 'fixed-width-lg',
            ],
            'end' => [
                'title' => $this->l('End'),
                'type' => 'datetime',
                'class' => 'fixed-width-lg',
            ],
            'active' => [
                'title' => $this->l('Status'),
                'active' => 'status',
                'align' => 'center',
                'type' => 'bool',
                'class' => 'fixed-width-sm',
            ],
        ];

        $this->_select = '(SELECT fsl.`name`
		FROM `' . _DB_PREFIX_ . 'flash_sale_lang` fsl
		WHERE fsl.`id_flash_sale` = a.`id_flash_sale`
        AND fsl.`id_lang` = ' . (int) $this->context->language->id . ') as name,';

        $this->_select .= 'IF (EXISTS (SELECT * FROM `' . _DB_PREFIX_ . 'flash_sale_shops` fss WHERE fss.`id_flash_sale` = a.`id_flash_sale`),
            (SELECT GROUP_CONCAT(s.`name` SEPARATOR ", ")
    		FROM `' . _DB_PREFIX_ . 'shop` s
            INNER JOIN `' . _DB_PREFIX_ . 'flash_sale_shops` fss ON (fss.`id_shop` = s.`id_shop`)
    		WHERE fss.`id_flash_sale` = a.`id_flash_sale`),
            \'' . $this->l('All shops') . '\'
        ) as shops,';

        $this->_select .= 'IF (EXISTS (SELECT * FROM `' . _DB_PREFIX_ . 'flash_sale_groups` fsg WHERE fsg.`id_flash_sale` = a.`id_flash_sale`),
            (SELECT GROUP_CONCAT(gl.`name` SEPARATOR ", ")
            FROM `' . _DB_PREFIX_ . 'group_lang` gl
            INNER JOIN `' . _DB_PREFIX_ . 'flash_sale_groups` fsg ON (fsg.`id_group` = gl.`id_group`)
            WHERE fsg.`id_flash_sale` = a.`id_flash_sale`
            AND gl.`id_lang` = ' . (int) $this->context->language->id . '),
            \'' . $this->l('All groups') . '\'
        ) as customer_groups,';

        $this->_select .= 'IF (EXISTS (SELECT * FROM `' . _DB_PREFIX_ . 'flash_sale_countries` fsco WHERE fsco.`id_flash_sale` = a.`id_flash_sale`),
            (SELECT GROUP_CONCAT(cl.`name` SEPARATOR ", ")
            FROM `' . _DB_PREFIX_ . 'country_lang` cl
            INNER JOIN `' . _DB_PREFIX_ . 'flash_sale_countries` fsco ON (fsco.`id_country` = cl.`id_country`)
            WHERE fsco.`id_flash_sale` = a.`id_flash_sale`
            AND cl.`id_lang` = ' . (int) $this->context->language->id . '),
            \'' . $this->l('All countries') . '\'
        ) as countries,';

        $this->_select .= 'IF (EXISTS (SELECT * FROM `' . _DB_PREFIX_ . 'flash_sale_currencies` fscu WHERE fscu.`id_flash_sale` = a.`id_flash_sale`),
            (SELECT GROUP_CONCAT(cl.`name` SEPARATOR ", ")
    		FROM `' . _DB_PREFIX_ . (version_compare(_PS_VERSION_, '1.7.6', '>=') ? 'currency_lang' : 'currency') . '` cl
    		INNER JOIN `' . _DB_PREFIX_ . 'flash_sale_currencies` fscu ON (fscu.`id_currency` = cl.`id_currency`)
    		WHERE fscu.`id_flash_sale` = a.`id_flash_sale`
            ' . (version_compare(_PS_VERSION_, '1.7.6', '>=') ? 'AND cl.`id_lang` = ' . $this->context->language->id : '') . '),
            \'' . $this->l('All currencies') . '\'
        ) as currencies,';

        $this->_select .= '(SELECT COUNT(fsp.`id_product`)
		FROM `' . _DB_PREFIX_ . 'flash_sale_products` fsp
		WHERE fsp.`id_flash_sale` = a.`id_flash_sale`) as nb,';

        $this->_select .= ' (SELECT MIN(fscsp.`from`)
		FROM `' . _DB_PREFIX_ . 'flash_sale_custom_specific_prices` fscsp
		WHERE fscsp.`id_flash_sale` = a.`id_flash_sale`) as start,';

        $this->_select .= ' (SELECT MAX(fscsp.`to`)
		FROM `' . _DB_PREFIX_ . 'flash_sale_custom_specific_prices` fscsp
		WHERE fscsp.`id_flash_sale` = a.`id_flash_sale`) as end,';

        $this->_select .= ' IF(a.`id_customer` > 0 , c.`email`, \'' . $this->l('All customers') . '\') as customer,';

        $this->_join = ' LEFT JOIN `' . _DB_PREFIX_ . 'customer` c ON (c.`id_customer` = a.`id_customer`)';

        $now = date('Y-m-d H:i:s');
        if ($name == 'active') {
            $this->_having = ' `start` <= "' . pSQL($now) . '" AND `end` > "' . pSQL($now) . '"';
        } elseif ($name == 'pending') {
            $this->_having = ' `start` > "' . pSQL($now) . '" AND `end` > "' . pSQL($now) . '"';
        } elseif ($name == 'expired') {
            $this->_having = ' `start` < "' . pSQL($now) . '" AND `end` <= "' . pSQL($now) . '"';
        }

        $this->show_toolbar = false;
        $this->toolbar_title = $this->l('flash sales');

        return parent::renderList();
    }

    public function renderForm()
    {
        /** @var FlashSale $current_object */
        $current_object = $this->loadObject(true);
        $languages = Language::getLanguages(true, Context::getContext()->shop->id);
        $currencies = Currency::getCurrencies(false, true, true);
        $countries = Country::getCountries($this->context->language->id);
        $groups = Group::getGroups($this->context->language->id);
        $iso = $this->context->language->iso_code;

        if (Validate::isUnsignedId($current_object->id_customer)
            && Validate::isLoadedObject($customer = new Customer($current_object->id_customer))) {
            $customer_full_name = $customer->firstname . ' ' . $customer->lastname;
        }

        $ids_product_list = [];
        $selected_products = [];
        $item_properties = [];

        foreach ($this->items as $item) {
            $item_properties[$item] = [];
            if (is_array($items = Tools::getValue($item, $current_object->getItems($item))) && count($items)) {
                foreach ($items as $id_item => $values) {
                    $selected_product = [
                        'item' => $item,
                        'id_item' => $id_item,
                        'ids_product' => $values['ids_product'],
                        'custom_reduction' => false,
                    ];

                    if ($item != 'product' && isset($values['reduction']) && isset($values['reduction_type']) && isset($values['from']) && isset($values['to'])) {
                        $selected_product['reduction'] = $values['reduction'];
                        $selected_product['reduction_type'] = $values['reduction_type'];
                        $selected_product['from'] = $values['from'];
                        $selected_product['to'] = $values['to'];
                        $selected_product['custom_reduction'] = true;
                    }

                    $selected_products[] = $selected_product;
                    $ids_product = explode(',', $values['ids_product']);
                    $item_properties[$item][] = $this->displayItem($item, (int) $id_item, $ids_product);
                    $ids_product_list = array_merge($ids_product_list, $ids_product);
                }
            }
        }

        // load image banner
        $image = _PS_MODULE_DIR_ . 'flashsales/views/img/banner/' . $current_object->id . '.' . $this->imageType;
        $image_url = ImageManager::thumbnail(
            $image,
            $this->table . '_' . (int) $current_object->id . '.' . $this->imageType,
            350,
            $this->imageType,
            true,
            true
        );
        $image_size = file_exists($image) ? filesize($image) / 1000 : false;
        $image_delete_url = self::$currentIndex . '&id_flash_sale=' . $current_object->id . '&token=' . $this->token . '&deleteImage=1';

        $image_uploader = new HelperImageUploader();
        $image_uploader->setId('image')
            ->setName('image')
            ->setFiles([$image_url ? [
                    'type' => HelperUploader::TYPE_IMAGE,
                    'image' => $image_url,
                    'size' => $image_size,
                    'delete_url' => $image_delete_url,
                ] : [],
            ]);

        $this->context->smarty->assign([
            'show_toolbar' => true,
            'toolbar_btn' => $this->toolbar_btn,
            'toolbar_scroll' => $this->toolbar_scroll,
            'title' => $this->toolbar_title,
            'default_shop' => (int) Configuration::get('PS_SHOP_DEFAULT'),
            'default_language' => (int) Configuration::get('PS_LANG_DEFAULT'),
            'customer_name' => isset($customer_full_name) ? $customer_full_name : $this->l('All customers'),
            'selected_products' => $selected_products,
            'item_properties' => $item_properties,
            'languages' => $languages,
            'shops' => Shop::getShops(),
            'selected_shops' => Tools::getValue('shops', $current_object->getRestrictionsByKey('shops')),
            'currencies' => $currencies,
            'selected_currencies' => Tools::getValue('currencies', $current_object->getRestrictionsByKey('currencies')),
            'countries' => $countries,
            'selected_countries' => Tools::getValue('countries', $current_object->getRestrictionsByKey('countries')),
            'groups' => $groups,
            'selected_groups' => Tools::getValue('groups', $current_object->getRestrictionsByKey('groups')),
            'item_card' => _PS_MODULE_DIR_ . 'flashsales/views/templates/admin/form/item-card.tpl',
            'custom_price' => _PS_MODULE_DIR_ . 'flashsales/views/templates/admin/form/custom-price.tpl',
            'selected' => true,
            'uploader' => $image_uploader->render(),
            'currentIndex' => self::$currentIndex,
            'currentToken' => $this->token,
            'currentObject' => $current_object,
            'currentTab' => $this,
            'tinymce' => true,
            'iso' => file_exists(__DIR__ . '/js/tiny_mce/langs/' . $iso . '.js') ? $iso : 'en',
            'path_css' => _THEME_CSS_DIR_,
            'ad' => __PS_BASE_URI__,
        ]);

        $this->content .= $this->context->smarty->createTemplate(
            _PS_MODULE_DIR_ . 'flashsales/views/templates/admin/form/form.tpl',
            $this->context->smarty
        )->fetch();

        $this->addCss(_MODULE_DIR_ . 'flashsales/views/css/tools/bootstrap-select.min.css', 'all');

        $this->addjQueryPlugin(['autocomplete', 'fancybox']);

        $this->addJS([
            _PS_JS_DIR_ . 'tiny_mce/tiny_mce.js',
            _PS_JS_DIR_ . 'tinymce.inc.js',
            _PS_JS_DIR_ . 'admin/tinymce.inc.js',
            _PS_JS_DIR_ . 'jquery/plugins/timepicker/jquery-ui-timepicker-addon.js',
            _MODULE_DIR_ . 'flashsales/views/js/admin/form.js',
            _MODULE_DIR_ . 'flashsales/views/js/tools/functions.js',
            _MODULE_DIR_ . 'flashsales/views/js/tools/typewatch.js',
            _MODULE_DIR_ . 'flashsales/views/js/tools/bootstrap-select.min.js',
        ]);

        return parent::renderForm();
    }

    public function processSave()
    {
        $_POST['cache'] = 0;

        $now = date('Y-m-d H:i:s');
        if (Tools::getValue('reduction_type') == 'percentage' && ((float) Tools::getValue('reduction') < 0 || (float) Tools::getValue('reduction') > 100)) {
            $this->errors[] = $this->module->l('Submitted reduction value (0-100) is out-of-range');
        } elseif (Tools::getValue('from') && Tools::getValue('to') && Tools::getValue('from') > Tools::getValue('to')) {
            $this->errors[] = $this->module->l('The to date must be higher than from date');
        }

        return parent::processSave();
    }

    public function processStatus()
    {
        parent::processStatus();

        /** @var FlashSale $object */
        $object = $this->loadObject();

        if (!Validate::isLoadedObject($object)) {
            return false;
        }

        if ($object->active == 0) {
            $object->deleteSpecificPrice();
        } else {
            $default_shop = Shop::isFeatureActive() ? 0 : (int) Configuration::get('PS_SHOP_DEFAULT');
            $shops = array_merge($object->getRestrictionsByKey('shops'), [$default_shop]);
            $groups = array_merge($object->getRestrictionsByKey('groups'), [0]);
            $countries = array_merge($object->getRestrictionsByKey('countries'), [0]);
            $currencies = array_merge($object->getRestrictionsByKey('currencies'), [0]);
            $ids_product = $object->getProducts();
            foreach ($ids_product as $id_product) {
                $ids = [];
                $ids_combination = Product::getProductAttributesIds($id_product);
                foreach ($ids_combination as $id_combination) {
                    $ids[] = (int) $id_combination['id_product_attribute'];
                }
                array_unshift($ids, 0);

                foreach ($ids as $id_product_attribute) {
                    if ($custom_specific_price = $object->getCustomReduction((int) $id_product, (int) $id_product_attribute)) {
                        $from = $custom_specific_price['from'];
                        $to = $custom_specific_price['to'];
                    } else {
                        $from = $object->from;
                        $to = $object->to;
                    }

                    $object->updateSpecificPrices($id_product, $id_product_attribute, $shops, $groups, $countries, $currencies, $from, $to);
                }
            }
        }

        $this->module->clearCache();

        return $object;
    }

    protected function uploadImage($id, $name, $dir, $ext = false, $width = null, $height = null)
    {
        if (isset($_FILES[$name]['tmp_name']) && !empty($_FILES[$name]['tmp_name'])) {
            // Delete old image
            if (Validate::isLoadedObject($object = $this->loadObject())) {
                $object->deleteImage();
            } else {
                return false;
            }

            // Check image validity
            $max_size = isset($this->max_image_size) ? $this->max_image_size : 0;
            if ($error = ImageManager::validateUpload($_FILES[$name], Tools::getMaxUploadSize($max_size))) {
                $this->errors[] = $error;
            }

            $tmp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS');
            if (!$tmp_name) {
                return false;
            }

            if (!move_uploaded_file($_FILES[$name]['tmp_name'], $tmp_name)) {
                return false;
            }

            // Evaluate the memory required to resize the image: if it's too much, you can't resize it.
            if (!ImageManager::checkImageMemoryLimit($tmp_name)) {
                $this->errors[] = Tools::displayError('Due to memory limit restrictions, this image cannot be loaded. Please increase your memory_limit value via your server\'s configuration settings. ');
            }

            // Copy new image
            if (empty($this->errors) && !ImageManager::resize($tmp_name, $dir . $id . '.' . $this->imageType, (int) $width, (int) $height, $ext ? $ext : $this->imageType)) {
                $this->errors[] = Tools::displayError('An error occurred while uploading the image.');
            }

            if (count($this->errors)) {
                return false;
            }
            if ($this->afterImageUpload()) {
                unlink($tmp_name);

                return true;
            }

            return false;
        }

        return true;
    }

    /**
     * @param FlashSale $currentObject
     *
     * @return bool
     */
    protected function afterUpdate($currentObject)
    {
        $currentObject->deleteSpecificPrice();
        $currentObject->deleteCustomReductionGroup();
        $currentObject->deleteCustomReduction();

        $ids_product = $currentObject->getProducts();
        if (count($ids_product)) {
            SpecificPriceRule::applyAllRules($ids_product);
        }
        $currentObject->deleteProducts();

        foreach ($currentObject->restrictions as $key => $restriction) {
            $currentObject->deleteRestrictionsByKey($key);
        }

        $this->afterAdd($currentObject);

        return true;
    }

    /**
     * @param FlashSale $currentObject
     *
     * @return bool
     */
    protected function afterAdd($currentObject)
    {
        $default_shop = Shop::isFeatureActive() ? 0 : (int) Configuration::get('PS_SHOP_DEFAULT');
        $shops = array_map('intval', array_unique(Tools::getValue('shops', [$default_shop])));
        $groups = array_map('intval', array_unique(Tools::getValue('groups', [0])));
        $countries = array_map('intval', array_unique(Tools::getValue('countries', [0])));
        $currencies = array_map('intval', array_unique(Tools::getValue('currencies', [0])));

        foreach ($currentObject->restrictions as $key => $restriction) {
            $currentObject->setRestrictionsByKey($key, Tools::getValue($key));
        }

        foreach ($this->items as $item) {
            if (is_array($items = Tools::getValue($item)) && count($items)) {
                foreach ($items as $id_item => $values) {
                    if ($item != 'products' && isset($values['reduction']) && isset($values['reduction_type']) && isset($values['from']) && isset($values['to'])) {
                        $currentObject->addCustomReductionGroup($item, (int) $id_item, $values['reduction'], $values['reduction_type'], $values['from'], $values['to']);
                    }

                    $currentObject->setProducts($item, $id_item, explode(',', $values['ids_product']));
                }
            }
        }

        if (is_array($reductions = Tools::getValue('reductions')) && count($reductions)) {
            foreach ($reductions as $id_product => $attributes) {
                foreach ($attributes as $id_product_attribute => $data) {
                    $currentObject->addCustomReduction((int) $id_product, (int) $id_product_attribute, $data['reduction'], $data['reduction_type'], $data['from'], $data['to'], $data['custom_reduction']);

                    if (!$currentObject->active) {
                        continue;
                    }

                    $currentObject->updateSpecificPrices($id_product, $id_product_attribute, $shops, $groups, $countries, $currencies, $data['from'], $data['to']);
                }
            }
        }

        $this->module->clearCache();

        return true;
    }

    protected function displayItem($item, $id_item, $products = [])
    {
        $item_properties = [];
        $selected = true;

        if ($item != 'product') {
            $class_name = Tools::toCamelCase($item, true);
            $object = new $class_name((int) $id_item);
            if (!Validate::isLoadedObject($object)) {
                return [];
            }

            $item_properties['id_item'] = (int) $object->id;
            switch ($item) {
                case 'category':
                    $item_properties['name'] = $object->name[$this->context->language->id];
                    $img_dir = _THEME_CAT_DIR_;
                    $type = 'categories';
                    break;
                case 'manufacturer':
                    $item_properties['name'] = $object->name;
                    $img_dir = _THEME_MANU_DIR_;
                    $type = 'manufacturers';
                    break;
                default:
                    $item_properties['name'] = $object->name[$this->context->language->id];
                    $img_dir = _THEME_CAT_DIR_;
                    $type = 'categories';
            }

            $image_type = ImageType::getByNameNType('%', $type, 'width');
            if (isset($image_type['name'])) {
                $image_type = $image_type['name'];
            } else {
                $image_type = version_compare(_PS_VERSION_, '1.7', '>=')
                    ? ImageType::getFormattedName('small')
                    : ImageType::getFormatedName('small')
                ;
            }

            $item_properties['image_link'] = $this->context->link->getMediaLink($img_dir . $object->id . '-' . $image_type . '.jpg');
        }

        $product_list = [];
        if (!count($products)) {
            if ($item == 'product') {
                $selected = false;
            }
            $products = FlashSale::getProductsWs($item, (int) $id_item);
        }

        foreach ($products as $product) {
            if (is_array($product) && count($product) > 0) {
                $id_product = (int) $product['id'];
            } else {
                $id_product = (int) $product;
            }
            $product_list[] = $this->displayProduct((int) $id_product);
        }

        Context::getContext()->smarty->assign([
            'products' => $product_list,
            'selected' => $selected,
        ]);

        $content = Context::getContext()->smarty->createTemplate(
            _PS_MODULE_DIR_ . 'flashsales/views/templates/admin/form/product-card.tpl',
            Context::getContext()->smarty
        )->fetch();

        $item_properties['nb_products'] = count($product_list);
        $item_properties['products'] = $content;

        return $item_properties;
    }

    /**
     * @param Product|int $product
     *
     * @return array
     */
    protected function displayProduct($product)
    {
        if (is_int($product)) {
            $product = new Product((int) $product);
        }

        if (!Validate::isLoadedObject($product)) {
            return [];
        }

        $image_type = ImageType::getByNameNType('%', 'products', 'width');
        if (isset($image_type['name'])) {
            $image_type = $image_type['name'];
        } else {
            $image_type = version_compare(_PS_VERSION_, '1.7', '>=')
                ? ImageType::getFormattedName('small')
                : ImageType::getFormatedName('small')
            ;
        }

        $prod = [];
        $prod['id_product'] = $product->id;
        $prod['name'] = $product->name[$this->context->language->id];
        $prod['reference'] = $product->reference;
        $prod['cover'] = Product::getCover((int) $product->id);
        $prod['link_rewrite'] = $product->link_rewrite[$this->context->language->id];
        $prod['image_link'] = $this->context->link->getImageLink($prod['link_rewrite'], $prod['cover']['id_image'], $image_type);
        $prod['description_short'] = $product->description_short[$this->context->language->id];
        $prod['combinations'] = $this->getProductCombinations($product);

        return $prod;
    }

    /**
     * @param Product $product
     *
     * @return array
     */
    protected function getProductCombinations($product)
    {
        $combinations = [];
        $combinations[0]['id_product_attribute'] = 0;
        $combinations[0]['attributes'] = $this->l('All combinations');
        $combinations[0]['price_tax_incl'] = Product::getPriceStatic((int) $product->id, true, 0, 2, null, false, false);
        $combinations[0]['formatted_price'] = Tools::displayPrice(Tools::convertPrice($combinations[0]['price_tax_incl'], $this->context->currency), $this->context->currency);
        $combinations[0]['stock'] = StockAvailable::getQuantityAvailableByProduct((int) $product->id, 0, (int) $this->context->shop->id);

        $attributes = $product->getAttributesGroups((int) $this->context->language->id);
        foreach ($attributes as $attribute) {
            $combinations[$attribute['id_product_attribute']]['id_product_attribute'] = $attribute['id_product_attribute'];
            if (!isset($combinations[$attribute['id_product_attribute']]['attributes'])) {
                $combinations[$attribute['id_product_attribute']]['attributes'] = '';
            }
            $combinations[$attribute['id_product_attribute']]['attributes'] .= $attribute['attribute_name'] . ' - ';

            if (!isset($combinations[$attribute['id_product_attribute']]['price_tax_incl'])) {
                $price_tax_incl = Product::getPriceStatic((int) $product->id, true, $attribute['id_product_attribute'], 2, null, false, false);
                $combinations[$attribute['id_product_attribute']]['price_tax_incl'] = Tools::ps_round(Tools::convertPrice($price_tax_incl, $this->context->currency), 2);
                $combinations[$attribute['id_product_attribute']]['formatted_price'] = Tools::displayPrice(Tools::convertPrice($price_tax_incl, $this->context->currency), $this->context->currency);
            }
            if (!isset($combinations[$attribute['id_product_attribute']]['stock'])) {
                $combinations[$attribute['id_product_attribute']]['stock'] = StockAvailable::getQuantityAvailableByProduct(
                    (int) $product->id,
                    $attribute['id_product_attribute'],
                    (int) $this->context->shop->id
                );
            }
        }

        foreach ($combinations as &$combination) {
            $combination['attributes'] = rtrim($combination['attributes'], ' - ');

            if (is_array($reductions = Tools::getValue('reductions', FlashSale::getReductions((int) Tools::getValue('id_flash_sale')))) && count($reductions)) {
                foreach ($reductions as $id_product => $attributes) {
                    if ($id_product != $product->id) {
                        continue;
                    }

                    foreach ($attributes as $id_product_attribute => $data) {
                        if ($id_product_attribute != $combination['id_product_attribute']) {
                            continue;
                        }

                        $combination['reduction'] = $data['reduction'];
                        $combination['reduction_type'] = $data['reduction_type'];
                        $combination['from'] = $data['from'];
                        $combination['to'] = $data['to'];
                        $combination['custom_reduction'] = isset($data['custom_reduction']) && $data['custom_reduction'];
                    }
                }
            }
        }

        return $combinations;
    }

    public function ajaxProcessSearchItems()
    {
        $items = [];
        $item = Tools::getValue('item');
        if ($rows = FlashSale::searchByName($item, Tools::getValue('ids_item'), (int) $this->context->language->id, Tools::getValue('product_search'))) {
            foreach ($rows as $row) {
                $items[] = $this->displayItem($item, (int) $row['id_' . $item]);
            }
        }

        Context::getContext()->smarty->assign([
            'items' => $items,
            'key' => $item,
            'selected' => false,
        ]);

        $content = Context::getContext()->smarty->createTemplate(
            _PS_MODULE_DIR_ . 'flashsales/views/templates/admin/form/item-card.tpl',
            Context::getContext()->smarty
        )->fetch();

        Context::getContext()->smarty->assign('content', $content);

        exit(json_encode($content));
    }
}
