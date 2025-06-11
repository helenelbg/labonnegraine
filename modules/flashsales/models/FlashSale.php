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
class FlashSale extends ObjectModel
{
    public $id_flash_sale;
    public $id_customer;
    public $reduction;
    public $reduction_tax = 1;
    public $reduction_type;
    public $from;
    public $to;
    public $depends_on_stock = 0;
    public $display_home = 1;
    public $display_home_tab = 1;
    public $display_column = 1;
    public $display_page = 1;
    public $active = 0;
    public $cache = 0;
    public $name;
    public $description;
    public $restrictions = [
        'shops' => ['identifier' => 'id_shop'],
        'countries' => ['identifier' => 'id_country'],
        'currencies' => ['identifier' => 'id_currency'],
        'groups' => ['identifier' => 'id_group'],
    ];

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'flash_sale',
        'primary' => 'id_flash_sale',
        'multilang' => true,
        'fields' => [
            'id_customer' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true],
            'reduction' => ['type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true],
            'reduction_tax' => ['type' => self::TYPE_INT, 'validate' => 'isBool', 'required' => true],
            'reduction_type' => ['type' => self::TYPE_STRING, 'validate' => 'isReductionType', 'required' => true],
            'from' => ['type' => self::TYPE_DATE, 'validate' => 'isDate', 'required' => true],
            'to' => ['type' => self::TYPE_DATE, 'validate' => 'isDate', 'required' => true],
            'depends_on_stock' => ['type' => self::TYPE_INT, 'validate' => 'isBool', 'required' => true],
            'display_home' => ['type' => self::TYPE_INT, 'validate' => 'isBool', 'required' => true],
            'display_home_tab' => ['type' => self::TYPE_INT, 'validate' => 'isBool', 'required' => true],
            'display_column' => ['type' => self::TYPE_INT, 'validate' => 'isBool', 'required' => true],
            'display_page' => ['type' => self::TYPE_INT, 'validate' => 'isBool', 'required' => true],
            'active' => ['type' => self::TYPE_INT, 'validate' => 'isBool', 'required' => true],
            'cache' => ['type' => self::TYPE_INT, 'validate' => 'isBool'],
            'name' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCatalogName', 'required' => true, 'size' => 128],
            'description' => ['type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml'],
        ],
    ];

    protected static $products_cache = [];

    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        parent::__construct($id, $id_lang, $id_shop);
        $this->image_dir = _PS_MODULE_DIR_ . 'flashsales/views/img/banner/';
    }

    public function add($autodate = true, $null_values = false)
    {
        $this->from = $this->parseDate($this->from);
        $this->to = $this->parseDate($this->to);

        return parent::add($autodate, $null_values);
    }

    public function update($null_values = false)
    {
        $this->from = $this->parseDate($this->from);
        $this->to = $this->parseDate($this->to);

        return parent::update($null_values);
    }

    public function parseDate($date)
    {
        $date = new DateTime($date);

        return $date->format('Y-m-d H:i:00');
    }

    public function delete()
    {
        if (!parent::delete()) {
            return false;
        }

        $this->deleteSpecificPrice();
        $this->deleteCustomReductionGroup();
        $this->deleteCustomReduction();

        $ids_product = $this->getProducts();
        if (count($ids_product)) {
            SpecificPriceRule::applyAllRules($ids_product);
        }
        $this->deleteProducts();

        foreach ($this->restrictions as $key => $restriction) {
            $this->deleteRestrictionsByKey($key);
        }

        return true;
    }

    public static function getFlashSalesLite()
    {
        $results = Db::getInstance()->executeS('
		SELECT id_flash_sale as id
		FROM ' . _DB_PREFIX_ . 'flash_sale fs
		');

        if (!$results) {
            return [];
        }

        $ids_flash_sale = [];
        foreach ($results as $result) {
            $ids_flash_sale[] = $result['id'];
        }

        return $ids_flash_sale;
    }

    public static function getActiveFlashSalesLite()
    {
        $now = date('Y-m-d H:i:s');

        $results = Db::getInstance()->executeS('
		SELECT id_flash_sale as id
		FROM ' . _DB_PREFIX_ . 'flash_sale fs
        WHERE fs.`active` = 1
		AND fs.`from` <= "' . pSQL($now) . '"
		AND fs.`to` > "' . pSQL($now) . '"');

        if (!$results) {
            return [];
        }

        $ids_flash_sale = [];
        foreach ($results as $result) {
            $ids_flash_sale[] = $result['id'];
        }

        return $ids_flash_sale;
    }

    /*
    ** Get required information on available flash sales for context customer
    **
    ** @param integer $id_lang Language id
    ** @param string $filter Filter display conf (optional)
    */
    public static function getFlashSales($id_lang, $filter = null)
    {
        $now = date('Y-m-d H:i:s');

        $groups = FrontController::getCurrentCustomerGroups();
        $id_shop = (int) Context::getContext()->shop->id;
        $id_customer = Validate::isLoadedObject(Context::getContext()->customer) ? (int) Context::getContext()->customer->id : 0;
        $id_currency = (int) Context::getContext()->currency->id;
        $id_country = (int) Context::getContext()->country->id;

        $results = Db::getInstance()->executeS('
		SELECT fs.*, fsl.*,
            (SELECT MIN(fscsp.`from`)
            FROM `' . _DB_PREFIX_ . 'flash_sale_custom_specific_prices` fscsp
            WHERE fscsp.`id_flash_sale` = fs.`id_flash_sale`) as start,
            (SELECT MAX(fscsp.`to`)
		    FROM `' . _DB_PREFIX_ . 'flash_sale_custom_specific_prices` fscsp
		   WHERE fscsp.`id_flash_sale` = fs.`id_flash_sale`) as end
		FROM `' . _DB_PREFIX_ . 'flash_sale` fs
		LEFT JOIN ' . _DB_PREFIX_ . 'flash_sale_lang fsl ON (fs.id_flash_sale = fsl.id_flash_sale)
		WHERE fsl.`id_lang` = ' . (int) $id_lang . '
            AND (EXISTS (SELECT * FROM `' . _DB_PREFIX_ . 'flash_sale_shops` fss WHERE fss.`id_flash_sale` = fs.`id_flash_sale` AND fss.`id_shop` = ' . (int) $id_shop . ')
                OR NOT EXISTS (SELECT * FROM `' . _DB_PREFIX_ . 'flash_sale_shops` fss WHERE fss.`id_flash_sale` = fs.`id_flash_sale`))
            AND (EXISTS (SELECT * FROM `' . _DB_PREFIX_ . 'flash_sale_currencies` fscu WHERE fscu.`id_flash_sale` = fs.`id_flash_sale` AND fscu.`id_currency` = ' . (int) $id_currency . ')
                OR NOT EXISTS (SELECT * FROM `' . _DB_PREFIX_ . 'flash_sale_currencies` fscu WHERE fscu.`id_flash_sale` = fs.`id_flash_sale`))
            AND (EXISTS (SELECT * FROM `' . _DB_PREFIX_ . 'flash_sale_countries` fsco WHERE fsco.`id_flash_sale` = fs.`id_flash_sale` AND fsco.`id_country` = ' . (int) $id_country . ')
                OR NOT EXISTS (SELECT * FROM `' . _DB_PREFIX_ . 'flash_sale_countries` fsco WHERE fsco.`id_flash_sale` = fs.`id_flash_sale`))
            ' . (count($groups)
                ? ' AND (EXISTS (SELECT * FROM `' . _DB_PREFIX_ . 'flash_sale_groups` fsg WHERE fsg.`id_flash_sale` = fs.`id_flash_sale` AND fsg.`id_group` IN (' . implode(',', array_map('intval', $groups)) . '))
                        OR NOT EXISTS (SELECT * FROM `' . _DB_PREFIX_ . 'flash_sale_groups` fsg WHERE fsg.`id_flash_sale` = fs.`id_flash_sale`))'
                : '') . '
    		AND fs.`active` = 1
            AND fs.`id_customer` IN (0, ' . (int) $id_customer . ')
    		' . (isset($filter) ? ' AND fs.`display_' . pSQL($filter) . '` = 1' : '') . '
        HAVING `start` <= "' . pSQL($now) . '"
		      AND  `end` > "' . pSQL($now) . '"');

        return $results;
    }

    /*
    ** Get required informations on flash sales products
    **
    ** @param integer $id_lang Language id
    ** @param string $filter Filter display conf (optional)
    ** @param integer $page_number Start from (optional)
    ** @param integer $nb_products Number of products to return (optional)
    ** @return array from Product::getProductProperties
    */
    public static function getAllProducts($id_lang, $id_flash_sale = null, $filter = null, $get_total = false, $page_number = 0, $nb_products = 10, $order_by = null, $order_way = null)
    {
        $now = date('Y-m-d H:i:s');

        $groups = FrontController::getCurrentCustomerGroups();
        $sql_groups = Group::isFeatureActive() ? 'WHERE cp.`id_product` IS NOT NULL AND cg.`id_group` ' . (count($groups) ? ' IN (' . implode(',', array_map('intval', $groups)) . ')' : '= 1') : '';
        $products = Db::getInstance()->executeS('
		SELECT cp.`id_product`
		FROM `' . _DB_PREFIX_ . 'category_group` cg
		INNER JOIN `' . _DB_PREFIX_ . 'category_product` cp ON (cp.`id_category` = cg.`id_category`)
		' . $sql_groups);

        $ids = [];
        foreach ($products as $product) {
            if (Validate::isUnsignedId($product['id_product'])) {
                $ids[$product['id_product']] = 1;
            }
        }
        $ids = array_keys($ids);
        $ids = array_filter($ids);
        sort($ids);
        $ids = count($ids) > 0 ? implode(',', array_map('intval', $ids)) : 'NULL';

        $id_shop = (int) Context::getContext()->shop->id;
        $id_customer = Validate::isLoadedObject(Context::getContext()->customer) ? (int) Context::getContext()->customer->id : 0;
        $id_currency = (int) Context::getContext()->currency->id;
        $id_country = (int) Context::getContext()->country->id;

        if ($page_number < 0) {
            $page_number = 0;
        }
        if ($nb_products < 1) {
            $nb_products = 10;
        }
        $final_order_by = $order_by;
        if (empty($order_by) || $order_by == 'position') {
            $order_by = 'date_add';
        }
        if (empty($order_way)) {
            $order_way = 'DESC';
        }
        if ($order_by == 'id_product' || $order_by == 'price' || $order_by == 'date_add' || $order_by == 'date_upd') {
            $order_table = 'p';
        } elseif ($order_by == 'name') {
            $order_table = 'pl';
        }
        if (!Validate::isOrderBy($order_by) || !Validate::isOrderWay($order_way)) {
            exit(Tools::displayError());
        }

        $interval = Validate::isUnsignedInt((int) Configuration::get('PS_NB_DAYS_NEW_PRODUCT')) ? (int) Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20;

        // Main query
        $sql = 'SELECT p.*, product_shop.*, stock.out_of_stock, IFNULL(stock.quantity, 0) as quantity,
				pl.`description`, pl.`description_short`, pl.`link_rewrite`, pl.`meta_description`,
				pl.`meta_keywords`, pl.`meta_title`, pl.`name`, pl.`available_now`, pl.`available_later`,
				m.`name` AS manufacturer_name, p.`id_manufacturer` as id_manufacturer,
				image_shop.`id_image` id_image, il.`legend`,
				t.`rate`, pl.`meta_keywords`, pl.`meta_title`, pl.`meta_description`,
				DATEDIFF(p.`date_add`, DATE_SUB(NOW(),
				INTERVAL ' . $interval . ' DAY)) > 0 AS new' . (Combination::isFeatureActive() ? ', MAX(product_attribute_shop.minimal_quantity) AS product_attribute_minimal_quantity,
				MAX(product_attribute_shop.id_product_attribute) id_product_attribute' : '')
                . ' FROM `' . _DB_PREFIX_ . 'flash_sale_products` fsp
				LEFT JOIN `' . _DB_PREFIX_ . 'flash_sale` fs ON (fs.`id_flash_sale` = fsp.`id_flash_sale`)
				LEFT JOIN `' . _DB_PREFIX_ . 'product` p ON (fsp.`id_product` = p.`id_product`)
                LEFT JOIN `' . _DB_PREFIX_ . 'flash_sale_custom_specific_prices` fscsp ON (fscsp.`id_flash_sale` = fs.`id_flash_sale` AND fscsp.`id_product` = p.`id_product`)
				' . Shop::addSqlAssociation('product', 'p', false) . '
				LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute` pa
				ON (p.`id_product` = pa.`id_product`)
				' . (Combination::isFeatureActive() ?
                Shop::addSqlAssociation('product_attribute', 'pa', false, 'product_attribute_shop.`default_on` = 1') . '
				' . Product::sqlStock('fscsp', 'fscsp', false, Context::getContext()->shop) : Product::sqlStock('p', 'product', false, Context::getContext()->shop)) . '
				LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl
					ON p.`id_product` = pl.`id_product`
					AND pl.`id_lang` = ' . (int) $id_lang . Shop::addSqlRestrictionOnLang('pl') . '
				LEFT JOIN `' . _DB_PREFIX_ . 'image` i ON (i.`id_product` = p.`id_product`)' .
                Shop::addSqlAssociation('image', 'i', true, 'image_shop.cover=1') . '
				LEFT JOIN `' . _DB_PREFIX_ . 'image_lang` il ON (image_shop.`id_image` = il.`id_image` AND il.`id_lang` = ' . (int) $id_lang . ')
				LEFT JOIN `' . _DB_PREFIX_ . 'manufacturer` m ON (m.`id_manufacturer` = p.`id_manufacturer`)
				LEFT JOIN `' . _DB_PREFIX_ . 'tax_rule` tr ON (product_shop.`id_tax_rules_group` = tr.`id_tax_rules_group`)
					AND tr.`id_country` = ' . (int) Context::getContext()->country->id . '
					AND tr.`id_state` = 0
				LEFT JOIN `' . _DB_PREFIX_ . 'tax` t ON (t.`id_tax` = tr.`id_tax`)
				WHERE product_shop.`active` = 1
                AND (fs.`depends_on_stock` = 0 OR (fs.`depends_on_stock` = 1 AND stock.quantity > 0))
                AND product_shop.`visibility` != \'none\'
                AND p.`id_product` IN (' . $ids . ')
                AND fs.`id_customer` IN (0, ' . (int) $id_customer . ')
                AND (EXISTS (SELECT * FROM `' . _DB_PREFIX_ . 'flash_sale_shops` fss WHERE fss.`id_flash_sale` = fs.`id_flash_sale` AND fss.`id_shop` = ' . (int) $id_shop . ')
                    OR NOT EXISTS (SELECT * FROM `' . _DB_PREFIX_ . 'flash_sale_shops` fss WHERE fss.`id_flash_sale` = fs.`id_flash_sale`))
                AND (EXISTS (SELECT * FROM `' . _DB_PREFIX_ . 'flash_sale_currencies` fscu WHERE fscu.`id_flash_sale` = fs.`id_flash_sale` AND fscu.`id_currency` = ' . (int) $id_currency . ')
                    OR NOT EXISTS (SELECT * FROM `' . _DB_PREFIX_ . 'flash_sale_currencies` fscu WHERE fscu.`id_flash_sale` = fs.`id_flash_sale`))
                AND (EXISTS (SELECT * FROM `' . _DB_PREFIX_ . 'flash_sale_countries` fsco WHERE fsco.`id_flash_sale` = fs.`id_flash_sale` AND fsco.`id_country` = ' . (int) $id_country . ')
                    OR NOT EXISTS (SELECT * FROM `' . _DB_PREFIX_ . 'flash_sale_countries` fsco WHERE fsco.`id_flash_sale` = fs.`id_flash_sale`))
                ' . (count($groups)
                    ? ' AND (EXISTS (SELECT * FROM `' . _DB_PREFIX_ . 'flash_sale_groups` fsg WHERE fsg.`id_flash_sale` = fs.`id_flash_sale` AND fsg.`id_group` IN (' . implode(',', array_map('intval', $groups)) . '))
                            OR NOT EXISTS (SELECT * FROM `' . _DB_PREFIX_ . 'flash_sale_groups` fsg WHERE fsg.`id_flash_sale` = fs.`id_flash_sale`))'
                    : '') . '
                AND fs.`active` = 1
                AND fscsp.`from` <= "' . pSQL($now) . '"
                AND fscsp.`to` > "' . pSQL($now) . '"
                ' . (isset($id_flash_sale) ? ' AND fs.`id_flash_sale` = ' . (int) $id_flash_sale : '') . '
                ' . (isset($filter) ? ' AND fs.`display_' . $filter . '` = 1' : '') . '
				GROUP BY product_shop.id_product
				ORDER BY ' . (!empty($order_table) ? '`' . pSQL($order_table) . '`.' : '') . '`' . pSQL($order_by) . '` ' . pSQL($order_way) . '
				LIMIT ' . (int) $page_number * (int) $nb_products . ', ' . (int) $nb_products;

        $result = Db::getInstance()->executeS($sql);

        if ($final_order_by == 'price') {
            Tools::orderbyPrice($result, $order_way);
        }
        if (!$result) {
            return false;
        }

        $product_list = Product::getProductsProperties($id_lang, $result);
        foreach ($product_list as $key => $product) {
            if (!self::getProductSpecificPrice((int) $product['id_product'])) {
                unset($product_list[$key]);
            }
        }

        if (!count($product_list)) {
            return false;
        }

        return $product_list;
    }

    public static function getNbProducts($id_flash_sale = null, $filter = null)
    {
        $now = date('Y-m-d H:i:s');

        $groups = FrontController::getCurrentCustomerGroups();
        $sql_groups = Group::isFeatureActive() ? 'WHERE cp.`id_product` IS NOT NULL AND cg.`id_group` ' . (count($groups) ? ' IN (' . implode(',', array_map('intval', $groups)) . ')' : '= 1') : '';
        $products = Db::getInstance()->executeS('
		SELECT cp.`id_product`
		FROM `' . _DB_PREFIX_ . 'category_group` cg
		INNER JOIN `' . _DB_PREFIX_ . 'category_product` cp ON (cp.`id_category` = cg.`id_category`)
		' . $sql_groups);

        $ids = [];
        foreach ($products as $product) {
            if (Validate::isUnsignedId($product['id_product'])) {
                $ids[$product['id_product']] = 1;
            }
        }
        $ids = array_keys($ids);
        $ids = array_filter($ids);
        sort($ids);
        $ids = count($ids) > 0 ? implode(',', array_map('intval', $ids)) : 'NULL';

        $id_shop = (int) Context::getContext()->shop->id;
        $id_customer = Validate::isLoadedObject(Context::getContext()->customer) ? (int) Context::getContext()->customer->id : 0;
        $id_currency = (int) Context::getContext()->currency->id;
        $id_country = (int) Context::getContext()->country->id;

        return Db::getInstance()->getValue('
		SELECT COUNT(DISTINCT fsp.id_product)
		FROM `' . _DB_PREFIX_ . 'flash_sale_products` fsp
		LEFT JOIN `' . _DB_PREFIX_ . 'flash_sale` fs ON fs.`id_flash_sale` = fsp.`id_flash_sale`
		LEFT JOIN `' . _DB_PREFIX_ . 'product` p ON fsp.`id_product` = p.`id_product`
        LEFT JOIN `' . _DB_PREFIX_ . 'flash_sale_custom_specific_prices` fscsp ON (fscsp.`id_flash_sale` = fs.`id_flash_sale` AND fscsp.`id_product` = p.`id_product`)
		' . Shop::addSqlAssociation('product', 'p', false) . '
        ' . (Combination::isFeatureActive()
            ? Product::sqlStock('fscsp', 'fscsp', false, Context::getContext()->shop)
            : Product::sqlStock('p', 'product', false, Context::getContext()->shop)) . '
		WHERE product_shop.`active` = 1
        AND (fs.`depends_on_stock` = 0 OR (fs.`depends_on_stock` = 1 AND stock.quantity > 0))
		AND product_shop.`visibility` != \'none\'
		AND p.`id_product` IN (' . $ids . ')
        AND fs.`id_customer` IN (0, ' . (int) $id_customer . ')
        AND (EXISTS (SELECT * FROM `' . _DB_PREFIX_ . 'flash_sale_shops` fss WHERE fss.`id_flash_sale` = fs.`id_flash_sale` AND fss.`id_shop` = ' . (int) $id_shop . ')
            OR NOT EXISTS (SELECT * FROM `' . _DB_PREFIX_ . 'flash_sale_shops` fss WHERE fss.`id_flash_sale` = fs.`id_flash_sale`))
        AND (EXISTS (SELECT * FROM `' . _DB_PREFIX_ . 'flash_sale_currencies` fscu WHERE fscu.`id_flash_sale` = fs.`id_flash_sale` AND fscu.`id_currency` = ' . (int) $id_currency . ')
            OR NOT EXISTS (SELECT * FROM `' . _DB_PREFIX_ . 'flash_sale_currencies` fscu WHERE fscu.`id_flash_sale` = fs.`id_flash_sale`))
        AND (EXISTS (SELECT * FROM `' . _DB_PREFIX_ . 'flash_sale_countries` fsco WHERE fsco.`id_flash_sale` = fs.`id_flash_sale` AND fsco.`id_country` = ' . (int) $id_country . ')
            OR NOT EXISTS (SELECT * FROM `' . _DB_PREFIX_ . 'flash_sale_countries` fsco WHERE fsco.`id_flash_sale` = fs.`id_flash_sale`))
        ' . (count($groups)
            ? ' AND (EXISTS (SELECT * FROM `' . _DB_PREFIX_ . 'flash_sale_groups` fsg WHERE fsg.`id_flash_sale` = fs.`id_flash_sale` AND fsg.`id_group` IN (' . implode(',', array_map('intval', $groups)) . '))
                    OR NOT EXISTS (SELECT * FROM `' . _DB_PREFIX_ . 'flash_sale_groups` fsg WHERE fsg.`id_flash_sale` = fs.`id_flash_sale`))'
            : '') . '
		AND fs.`active` = 1
		AND fscsp.`from` <= "' . pSQL($now) . '"
		AND fscsp.`to` > "' . pSQL($now) . '"
		' . (isset($id_flash_sale) ? ' AND fs.`id_flash_sale` = ' . (int) $id_flash_sale : '') . '
		' . (isset($filter) ? ' AND fs.`display_' . pSQL($filter) . '` = 1' : ''));
    }

    public function getStart()
    {
        if (!Validate::isLoadedObject($this)) {
            return false;
        }

        return Db::getInstance()->getValue('
		SELECT MIN(fscsp.from)
		FROM `' . _DB_PREFIX_ . 'flash_sale_custom_specific_prices` fscsp
		WHERE fscsp.`id_flash_sale` = ' . (int) $this->id);
    }

    public function getEnd()
    {
        if (!Validate::isLoadedObject($this)) {
            return false;
        }

        return Db::getInstance()->getValue('
		SELECT MAX(fscsp.to)
		FROM `' . _DB_PREFIX_ . 'flash_sale_custom_specific_prices` fscsp
		WHERE fscsp.`id_flash_sale` = ' . (int) $this->id);
    }

    public function getItems($filter)
    {
        if (!Validate::isLoadedObject($this)) {
            return [];
        }

        $sql = '';
        if ($filter == 'product') {
            $sql = ' AND fsp.`id_category` = 0 AND fsp.`id_manufacturer` = 0';
        }

        $ids_item = Db::getInstance()->executeS('
		SELECT `id_' . pSQL($filter) . '` as id
		FROM ' . _DB_PREFIX_ . 'flash_sale_products fsp
		WHERE fsp.id_flash_sale = ' . (int) $this->id . '
		AND fsp.`id_' . pSQL($filter) . '` > 0
		' . $sql . '
		GROUP BY fsp.`id_' . pSQL($filter) . '`');

        if (!$ids_item) {
            return [];
        }

        $items = [];
        foreach ($ids_item as $id_item) {
            $items[(int) $id_item['id']]['ids_product'] = ($filter != 'product' ? implode(',', $this->getProducts($filter, (int) $id_item['id'])) : (int) $id_item['id']);
            if ($filter != 'product' && ($custom_reduction = $this->getCustomReductionGroup($filter, (int) $id_item['id']))) {
                $items[(int) $id_item['id']]['reduction'] = $custom_reduction['reduction'];
                $items[(int) $id_item['id']]['reduction_type'] = $custom_reduction['reduction_type'];
                $items[(int) $id_item['id']]['from'] = $custom_reduction['from'];
                $items[(int) $id_item['id']]['to'] = $custom_reduction['to'];
            }
        }

        return $items;
    }

    public static function getReductions($id_flash_sale)
    {
        if ($id_flash_sale == 0) {
            return [];
        }

        $result = Db::getInstance()->executeS('
		SELECT *
		FROM ' . _DB_PREFIX_ . 'flash_sale_custom_specific_prices fscsp
		WHERE fscsp.id_flash_sale = ' . (int) $id_flash_sale);

        if (!$result) {
            return [];
        }

        $reductions = [];
        foreach ($result as $row) {
            $reductions[$row['id_product']][$row['id_product_attribute']]['reduction'] = $row['reduction'];
            $reductions[$row['id_product']][$row['id_product_attribute']]['reduction_type'] = $row['reduction_type'];
            $reductions[$row['id_product']][$row['id_product_attribute']]['from'] = $row['from'];
            $reductions[$row['id_product']][$row['id_product_attribute']]['to'] = $row['to'];
            $reductions[$row['id_product']][$row['id_product_attribute']]['custom_reduction'] = $row['custom_reduction'];
        }

        return $reductions;
    }

    public function getProducts($filter = null, $id_item = 0)
    {
        if (!Validate::isLoadedObject($this)) {
            return [];
        }

        $sql = isset($filter) ? ' AND `id_' . pSQL($filter) . '` = ' . (int) $id_item : '';

        $results = Db::getInstance()->executeS('
		SELECT `id_product`
		FROM ' . _DB_PREFIX_ . 'flash_sale_products
		WHERE `id_flash_sale` = ' . (int) $this->id
        . $sql);

        if (!$results) {
            return [];
        }

        $ids_product = [];
        foreach ($results as $result) {
            $ids_product[] = $result['id_product'];
        }

        return $ids_product;
    }

    public function setProducts($item = null, $id_item = 0, $list = [])
    {
        if (!Validate::isLoadedObject($this) || !$item || !$id_item) {
            return false;
        }

        $inserts = [];
        foreach ($list as $id) {
            $inserts[] = '(' . (int) $this->id . ', ' . ($item != 'product' ? (int) $id_item . ', ' : '') . (int) $id . ')';
        }

        return Db::getInstance()->execute('
        INSERT INTO `' . _DB_PREFIX_ . 'flash_sale_products` (`id_flash_sale`, ' . ($item != 'product' ? '`id_' . $item . '`, ' : '') . '`id_product`)
		VALUES ' . implode(',', $inserts));
    }

    public function deleteProducts()
    {
        if (!Validate::isLoadedObject($this)) {
            return false;
        }

        return Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'flash_sale_products` WHERE `id_flash_sale` = ' . (int) $this->id);
    }

    public static function getProductSpecificPrice($id_product, $id_product_attribute = null)
    {
        $specific_price = [];

        $id_customer = Validate::isLoadedObject(Context::getContext()->customer) ? (int) Context::getContext()->customer->id : 0;
        $id_group = null;
        if ($id_customer) {
            $id_group = Customer::getDefaultGroupId((int) $id_customer);
        }
        if (!$id_group) {
            $id_group = (int) Group::getCurrent()->id;
        }

        $specific_price = SpecificPrice::getSpecificPrice(
            (int) $id_product,
            Context::getContext()->shop->id,
            Context::getContext()->currency->id,
            Context::getContext()->country->id,
            $id_group,
            1,
            $id_product_attribute,
            $id_customer
        );

        if (isset($specific_price['id_specific_price']) && self::specificPriceExists($specific_price['id_specific_price'])) {
            return $specific_price;
        }

        return false;
    }

    public static function specificPriceExists($id_specific_price)
    {
        return (bool) Db::getInstance()->getValue('
		SELECT fs.`id_flash_sale`
		FROM `' . _DB_PREFIX_ . 'flash_sale` fs
		LEFT JOIN `' . _DB_PREFIX_ . 'flash_sale_specific_prices` fssp ON (fs.`id_flash_sale` = fssp.`id_flash_sale`)
		WHERE fssp.`id_specific_price` = ' . (int) $id_specific_price . '
		AND fs.`active` = 1');
    }

    public static function getFlashsaleIdBySpecificPriceId($id_specific_price)
    {
        return (int) Db::getInstance()->getValue('
		SELECT fs.`id_flash_sale`
		FROM `' . _DB_PREFIX_ . 'flash_sale` fs
		LEFT JOIN `' . _DB_PREFIX_ . 'flash_sale_specific_prices` fssp ON (fs.`id_flash_sale` = fssp.`id_flash_sale`)
		WHERE fssp.`id_specific_price` = ' . (int) $id_specific_price . '
        AND fs.`active` = 1');
    }

    public function getCustomReduction($id_product, $id_product_attribute = 0)
    {
        if (!Validate::isLoadedObject($this)) {
            return false;
        }

        return Db::getInstance()->getRow('
		SELECT fscsp.`reduction_type`, fscsp.`reduction`, fscsp.`from`, fscsp.`to`
		FROM ' . _DB_PREFIX_ . 'flash_sale_custom_specific_prices fscsp
		WHERE fscsp.id_flash_sale = ' . (int) $this->id . '
		AND fscsp.id_product = ' . (int) $id_product . '
		AND fscsp.id_product_attribute = ' . (int) $id_product_attribute);
    }

    public function deleteCustomReduction()
    {
        if (!Validate::isLoadedObject($this)) {
            return false;
        }

        return Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'flash_sale_custom_specific_prices` WHERE `id_flash_sale` = ' . (int) $this->id);
    }

    public function addCustomReduction($id_product, $id_product_attribute, $reduction, $reduction_type, $from, $to, $custom_reduction)
    {
        $from = $this->parseDate($from);
        $to = $this->parseDate($to);

        return Db::getInstance()->execute('
        INSERT INTO `' . _DB_PREFIX_ . 'flash_sale_custom_specific_prices` (`id_flash_sale`, `id_product`, `id_product_attribute`, `reduction`, `reduction_type`, `from`, `to`, `custom_reduction`)
		VALUES (' . (int) $this->id . ', ' . (int) $id_product . ', ' . (int) $id_product_attribute . ', ' . (float) $reduction . ', "' . pSQL($reduction_type) . '", "' . pSQL($from) . '", "' . pSQL($to) . '", ' . (int) $custom_reduction . ')');
    }

    public function getCustomReductionGroup($item, $id_item)
    {
        if (!Validate::isLoadedObject($this)) {
            return false;
        }

        return Db::getInstance()->getRow('
		SELECT fscspg.`reduction_type`, fscspg.`reduction`, fscspg.`from`, fscspg.`to`
		FROM ' . _DB_PREFIX_ . 'flash_sale_custom_specific_prices_group fscspg
		WHERE fscspg.id_flash_sale = ' . (int) $this->id . '
		AND fscspg.item = "' . pSQL($item) . '"
		AND fscspg.id_item = ' . (int) $id_item);
    }

    public function addCustomReductionGroup($item, $id_item, $reduction, $reduction_type, $from, $to)
    {
        $from = $this->parseDate($from);
        $to = $this->parseDate($to);

        return Db::getInstance()->execute('
            INSERT INTO `' . _DB_PREFIX_ . 'flash_sale_custom_specific_prices_group` (`id_flash_sale`, `item`, `id_item`, `reduction`, `reduction_type`, `from`, `to`)
		    VALUES (' . (int) $this->id . ', "' . pSQL($item) . '", ' . (int) $id_item . ', ' . (float) $reduction . ', "' . pSQL($reduction_type) . '", "' . pSQL($from) . '", "' . pSQL($to) . '")
        ');
    }

    public function deleteCustomReductionGroup()
    {
        if (!Validate::isLoadedObject($this)) {
            return false;
        }

        return Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'flash_sale_custom_specific_prices_group` WHERE `id_flash_sale` = ' . (int) $this->id);
    }

    public function getSpecificPrice($list = true)
    {
        if (!Validate::isLoadedObject($this)) {
            return [];
        }

        $specific_prices = Db::getInstance()->executeS('
		SELECT `id_specific_price`
		FROM ' . _DB_PREFIX_ . 'flash_sale_specific_prices
		WHERE `id_flash_sale` = ' . (int) $this->id);

        if (!$list) {
            return $specific_prices;
        }

        $ids = [];
        foreach ($specific_prices as $specific_price) {
            $ids[] = $specific_price['id_specific_price'];
        }

        return $ids;
    }

    public function deleteSpecificPrice()
    {
        if (!Validate::isLoadedObject($this)) {
            return false;
        }

        if (($specific_prices = $this->getSpecificPrice()) && count($specific_prices)) {
            array_walk($specific_prices, function (&$value, $key) {
                $value = '(' . (int) $value . ')';
            });

            Db::getInstance()->execute('
                DELETE FROM `' . _DB_PREFIX_ . 'specific_price`
                WHERE (`id_specific_price`) IN (' . implode(',', $specific_prices) . ')
            ');
        }

        return Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'flash_sale_specific_prices` WHERE `id_flash_sale` = ' . (int) $this->id);
    }

    public function addSpecificPrice($id_product, $id_product_attribute = 0, $id_shop = 0, $id_group = 0, $id_country = 0, $id_currency = 0)
    {
        if (!Validate::isLoadedObject($this) || !$id_product) {
            return false;
        }

        if ($custom_specific_price = $this->getCustomReduction((int) $id_product, (int) $id_product_attribute)) {
            $reduction_type = $custom_specific_price['reduction_type'];
            $reduction = $custom_specific_price['reduction'];
            $from = $custom_specific_price['from'];
            $to = $custom_specific_price['to'];
        } elseif ($id_product_attribute == 0) {
            $reduction_type = $this->reduction_type;
            $reduction = $this->reduction;
            $from = $this->from;
            $to = $this->to;
        } else {
            return true;
        }

        $date_to = new DateTime($to);
        $interval = new DateInterval('PT1S');
        $date_to->sub($interval);

        $specific_price = new SpecificPrice();
        $specific_price->id_specific_price_rule = 0;
        $specific_price->id_product = (int) $id_product;
        $specific_price->id_product_attribute = (int) $id_product_attribute;
        $specific_price->id_customer = (int) $this->id_customer;
        $specific_price->id_shop = (int) $id_shop;
        $specific_price->id_country = (int) $id_country;
        $specific_price->id_currency = (int) $id_currency;
        $specific_price->id_group = (int) $id_group;
        $specific_price->from_quantity = 1;
        $specific_price->price = '-1';
        $specific_price->reduction_type = $reduction_type;
        $specific_price->reduction_tax = $this->reduction_tax;
        $specific_price->reduction = ($reduction_type == 'percentage' ? $reduction / 100 : (float) $reduction);
        $specific_price->from = $from;
        $specific_price->to = $date_to->format('Y-m-d H:i:s');
        $specific_price->add();

        return Db::getInstance()->execute('INSERT INTO `' . _DB_PREFIX_ . 'flash_sale_specific_prices` (`id_flash_sale`, `id_specific_price`)
		VALUES (' . (int) $this->id . ', ' . (int) $specific_price->id . ')');
    }

    public function updateSpecificPrices(int $id_product, int $id_product_attribute, $shops, $groups, $countries, $currencies, $from, $to)
    {
        $ids = [];
        $override = (int) Configuration::get('FLASHSALE_DEL_SPECIFICPRICE');

        if ($override && in_array($override, [1, 2])) {
            $where_clause = 'WHERE (
                `to` < \'' . pSQL($from) . '\' AND
                `to` != "0000-00-00 00:00:00"
            ) OR
            `from` > \'' . pSQL($to) . '\'';

            $specific_prices = Db::getInstance()->executeS('
                SELECT `id_specific_price`
                FROM ' . _DB_PREFIX_ . 'specific_price
                WHERE `id_product`=' . (int) $id_product . ' AND
                `id_specific_price_rule` = 0 AND
                `id_specific_price` NOT IN (
                    SELECT `id_specific_price`
                    FROM ' . _DB_PREFIX_ . 'specific_price
                    ' . $where_clause . '
                )
            ');

            foreach ($specific_prices as $specific_price) {
                $ids[] = $specific_price['id_specific_price'];
            }
        }

        foreach ($shops as $shop) {
            foreach ($groups as $group) {
                foreach ($countries as $country) {
                    foreach ($currencies as $currency) {
                        if ($override && in_array($override, [1, 2])) {
                            $product_attribute_sql = $id_product_attribute > 0 || ($id_product_attribute === 0 && $override === 1)
                                ? ' AND `id_product_attribute` = ' . (int) $id_product_attribute
                                : '';
                            $shop_sql = is_int($shop) && ($shop > 0 || ($shop === 0 && $override === 1))
                                ? ' AND `id_shop` = ' . (int) $shop
                                : '';
                            $group_sql = is_int($group) && ($group > 0 || ($group === 0 && $override === 1))
                                ? ' AND `id_group` = ' . (int) $group
                                : '';
                            $country_sql = is_int($country) && ($country > 0 || ($country === 0 && $override === 1))
                                ? ' AND `id_country` = ' . (int) $country
                                : '';
                            $currency_sql = is_int($currency) && ($currency > 0 || ($currency === 0 && $override === 1))
                                ? ' AND `id_currency` = ' . (int) $currency
                                : '';
                            $customer_sql = is_int($this->id_customer) && ($this->id_customer > 0 || ($this->id_customer === 0 && $override == 1))
                                ? ' AND `id_customer` = ' . (int) $this->id_customer
                                : '';
                            $specific_prices_sql = count($ids)
                                ? ' AND `id_specific_price` IN (' . implode(',', $ids) . ')'
                                : '';

                            Db::getInstance()->execute(
                                'DELETE FROM `' . _DB_PREFIX_ . 'specific_price`
                                WHERE `id_product`=' . (int) $id_product .
                                $specific_prices_sql . $product_attribute_sql . $shop_sql .
                                $group_sql . $country_sql . $currency_sql . $customer_sql
                            );
                        }

                        $this->addSpecificPrice((int) $id_product, (int) $id_product_attribute, (int) $shop, (int) $group, (int) $country, (int) $currency);
                    }
                }
            }
        }

        return true;
    }

    public static function searchByName($item, $ids_item, $id_lang, $query)
    {
        $result = null;

        if (isset($ids_item) && !empty($ids_item)) {
            $ids = implode(',', array_map('intval', explode(',', $ids_item)));
        }

        switch ($item) {
            case 'product':
                $sql = new DbQuery();
                $sql->select('p.`id_product`, pl.`name`, p.`ean13`, p.`upc`, p.`active`, p.`reference`, m.`name` AS manufacturer_name, stock.`quantity`, product_shop.advanced_stock_management, p.`customizable`');
                $sql->from('product', 'p');
                $sql->join(Shop::addSqlAssociation('product', 'p'));
                $sql->leftJoin('product_lang', 'pl', 'p.`id_product` = pl.`id_product` AND pl.`id_lang` = ' . (int) $id_lang . Shop::addSqlRestrictionOnLang('pl'));
                $sql->leftJoin('manufacturer', 'm', 'm.`id_manufacturer` = p.`id_manufacturer`');

                $input = Combination::isFeatureActive() ? '(' : '';
                $where = $input . 'pl.`name` LIKE \'%' . pSQL($query) . '%\'
				OR p.`ean13` LIKE \'%' . pSQL($query) . '%\'
				OR p.`upc` LIKE \'%' . pSQL($query) . '%\'
				OR p.`reference` LIKE \'%' . pSQL($query) . '%\'
				OR p.`supplier_reference` LIKE \'%' . pSQL($query) . '%\'
				OR EXISTS(SELECT * FROM `' . _DB_PREFIX_ . 'product_supplier` sp WHERE sp.`id_product` = p.`id_product` AND `product_supplier_reference` LIKE \'%' . pSQL($query) . '%\')';

                if (Combination::isFeatureActive()) {
                    $where .= ' OR EXISTS(SELECT * FROM `' . _DB_PREFIX_ . 'product_attribute` `pa` WHERE pa.`id_product` = p.`id_product` AND (pa.`reference` LIKE \'%' . pSQL($query) . '%\'
					OR pa.`supplier_reference` LIKE \'%' . pSQL($query) . '%\'
					OR pa.`ean13` LIKE \'%' . pSQL($query) . '%\'
					OR pa.`upc` LIKE \'%' . pSQL($query) . '%\')))';
                }

                $where .= isset($ids) ? ' AND p.`id_product` NOT IN (' . $ids . ')' : '';

                $sql->where($where);
                $sql->join(Product::sqlStock('p', 0));

                $result = Db::getInstance()->executeS($sql);
                break;
            case 'category':
                $result = Db::getInstance()->executeS('
				SELECT c.*, cl.*
				FROM `' . _DB_PREFIX_ . 'category` c
				LEFT JOIN `' . _DB_PREFIX_ . 'category_lang` cl ON (c.`id_category` = cl.`id_category` AND `id_lang` = ' . (int) $id_lang . ' ' . Shop::addSqlRestrictionOnLang('cl') . ')
				WHERE `name` LIKE \'%' . pSQL($query) . '%\'
				AND c.`id_category` != ' . (int) Configuration::get('PS_HOME_CATEGORY')
                . (isset($ids) ? ' AND c.`id_category` NOT IN (' . $ids . ')' : ''));
                break;
            case 'manufacturer':
                $result = Db::getInstance()->executeS('
				SELECT *
				FROM `' . _DB_PREFIX_ . 'manufacturer`
				WHERE `name` LIKE \'%' . pSQL($query) . '%\'
				' . (isset($ids) ? ' AND `id_manufacturer` NOT IN (' . $ids . ')' : '') . '
				GROUP BY `id_manufacturer`');
                break;
        }

        if (!$result) {
            return [];
        }

        return $result;
    }

    public static function getProductsWs($item, $id_item)
    {
        $result = null;

        switch ($item) {
            case 'product':
                $result = [$id_item];
                break;
            case 'category':
                $result = Db::getInstance()->executeS('
				SELECT cp.`id_product` as id
				FROM `' . _DB_PREFIX_ . 'category_product` cp
				WHERE cp.`id_category` = ' . (int) $id_item . '
				ORDER BY `position` ASC');
                break;
            case 'manufacturer':
                $result = Db::getInstance()->executeS('
				SELECT p.`id_product` as id
				FROM `' . _DB_PREFIX_ . 'product` p
				WHERE p.`id_manufacturer` = ' . (int) $id_item);
                break;
        }

        if (!$result) {
            return [];
        }

        return $result;
    }

    public function getRestrictionsByKey($key = null, $list = true)
    {
        if (!Validate::isLoadedObject($this) || !$key || !array_key_exists($key, $this->restrictions)) {
            return [];
        }

        $restrictions = Db::getInstance()->executeS('
		SELECT *
		FROM `' . _DB_PREFIX_ . 'flash_sale_' . $key . '`
		WHERE `id_flash_sale` = ' . (int) $this->id);

        if (!$list) {
            return $restrictions;
        }

        $ids = [];
        foreach ($restrictions as $restriction) {
            $ids[] = (int) $restriction[$this->restrictions[$key]['identifier']];
        }

        return $ids;
    }

    public function setRestrictionsByKey($key = null, $list = [])
    {
        if (!Validate::isLoadedObject($this)
            || !$key
            || !array_key_exists($key, $this->restrictions)
            || !is_array($list)
            || !count($list)
        ) {
            return false;
        }

        $list = array_unique($list);
        $inserts = [];
        foreach ($list as $id) {
            if ($id) {
                $inserts[] = '(' . (int) $this->id . ', ' . (int) $id . ')';
            }
        }

        if (!count($inserts)) {
            return true;
        }

        return Db::getInstance()->execute('
        INSERT IGNORE INTO `' . _DB_PREFIX_ . 'flash_sale_' . $key . '` (`id_flash_sale`, `' . $this->restrictions[$key]['identifier'] . '`)
		VALUES ' . implode(',', $inserts));
    }

    public function deleteRestrictionsByKey($key = null)
    {
        if (!Validate::isLoadedObject($this) || !$key || !array_key_exists($key, $this->restrictions)) {
            return false;
        }

        return Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'flash_sale_' . $key . '` WHERE `id_flash_sale` = ' . (int) $this->id);
    }
}
