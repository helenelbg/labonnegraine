<?php

@error_reporting(E_ERROR | E_PARSE);
@ini_set('display_errors', 'ON');

$id_lang = Tools::getValue('id_lang', '0');
$action = Tools::getValue('action', '');

$return = 'ERROR: Try again later';

// FUNCTIONS
$debug = false;
$extraVars = '';
$updated_products = array();
$return_datas = array(
    'doUpdateCombinationsOption' => false,
    'newQuantity' => '',
    'displayErrorMessage' => false,
);

// Récupération de toutes les modifications à effectuer
if (Tools::getValue('rows') || $action == 'insert')
{
    if ($action != 'insert')
    {
        if(_PS_MAGIC_QUOTES_GPC_)
            $_POST["rows"] = Tools::getValue('rows');
        $rows = json_decode($_POST["rows"]);
    }
    else
    {
        $rows = array();
        $rows[0] = new stdClass();
        $rows[0]->name = Tools::getValue('act', '');
        $rows[0]->action = Tools::getValue('action', '');
        $rows[0]->row = Tools::getValue('gr_id', '');
        $rows[0]->callback = Tools::getValue('callback', '');
        $rows[0]->params = $_POST;
    }

    if (is_array($rows) && count($rows) > 0)
    {
        $callbacks = '';

        // Première boucle pour remplir la table sc_queue_log
        // avec toutes ces modifications
        $log_ids = array();
        $date = date('Y-m-d H:i:s');

        foreach ($rows as $num => $row)
        {
            $id = QueueLog::add($row->name, $row->row, $row->action, (!empty($row->params) ? $row->params : array()), (!empty($row->callback) ? $row->callback : null), $date);
            $log_ids[$num] = $id;
        }

        // Deuxième boucle pour effectuer les
        // actions les une après les autres

        foreach ($rows as $num => $row)
        {
            if (!empty($log_ids[$num]))
            {
                $gr_id = (int) $row->row;
                $id_product = $row->row;
                $updated_products[$id_product] = $id_product;
                $action = $row->action;

                if (!empty($row->callback))
                {
                    $callbacks .= $row->callback.';';
                }

                if ($action != 'insert')
                {
                    $_POST = array();
                    $_POST = (array) json_decode($row->params);
                }

                if (!empty($action) && $action == 'insert')
                {
                    $id_category = (int) Tools::getValue('id_category', 1);
                    $newprod = new Product();
                    $newprod->id_category_default = $id_category;
                    $newprod->id_tax_rules_group = (int) (_s('CAT_PROD_CREA_TAX') ? _s('CAT_PROD_CREA_TAX') : 0);
                    if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                    {
                        $newprod->id_shop_list = SCI::getSelectedShopActionList();
                    }
                    $newprod->active = _s('CAT_PROD_CREA_ACTIVE');
                    $return_datas['newQuantity'] = (int) _s('CAT_PROD_CREA_QTY');
                    if (_s('CAT_PROD_CREA_REF') != '')
                    {
                        $newprod->reference = _s('CAT_PROD_CREA_REF');
                    }
                    if (_s('CAT_PROD_CREA_SUPREF') != '')
                    {
                        $newprod->supplier_reference = _s('CAT_PROD_CREA_SUPREF');
                    }
                    $newprod->quantity = $return_datas['newQuantity'];
                    foreach ($languages as $lang)
                    {
                        $newprod->link_rewrite[$lang['id_lang']] = 'product';
                        $newprod->name[$lang['id_lang']] = 'new';
                        $newprod->description_short[$lang['id_lang']] = '';
                        $newprod->description[$lang['id_lang']] = '';
                    }

                    if (version_compare(_PS_VERSION_, '1.7.8.0', '>=')) {
                        $newprod->product_type = PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductType::TYPE_STANDARD;
                    }

                    $newprod->save();
                    $newId = $newprod->id;
                    $sql = 'SELECT MAX(position) as maxpos FROM '._DB_PREFIX_.'category_product WHERE id_category='.(int) $id_category;
                    $row = Db::getInstance()->getRow($sql);
                    $sql = 'INSERT INTO '._DB_PREFIX_.'category_product (id_category,id_product,position) VALUE ('.(int) $id_category.','.(int) $newId.','.(int) ($row['maxpos'] + 1).')';
                    $row = Db::getInstance()->Execute($sql);

                    // Script pour empécher qu'un produit ai pour
                    // catégorie par défaut, pour chaque boutique,
                    // une catégorie non associée à la boutique
                    if (SCMS)
                    {
                        $shops_cats = array();
                        $shops_cats_tmp = Category::getShopsByCategory($id_category);
                        foreach ($shops_cats_tmp as $shops_cat_tmp)
                        {
                            $shops_cats[] = $shops_cat_tmp['id_shop'];
                        }
                        $shops = Shop::getShops(false);
                        foreach ($shops as $shop)
                        {
                            if (!sc_in_array($shop['id_shop'], $shops_cats, 'catProductUpdateQueue_shopsCats_'.$id_category))
                            {
                                $sql = 'UPDATE '._DB_PREFIX_."product_shop SET id_category_default='".psql($shop['id_category'])."'
                            WHERE id_product=".(int) $newId.' AND id_shop='.(int) $shop['id_shop'];
                                Db::getInstance()->Execute($sql);
                            }
                            else
                            {
                                $p = new Product($newId, false, null, $shop['id_shop']);
                                if (empty($p->id_category_default))
                                {
                                    $sql = 'UPDATE '._DB_PREFIX_."product_shop SET id_category_default='".psql($shop['id_category'])."'
                            WHERE id_product=".(int) $newId.' AND id_shop='.(int) $shop['id_shop'];
                                    Db::getInstance()->Execute($sql);
                                }
                            }
                        }
                    }

                    if (SCAS)
                    {
                        $type_default = _s('CAT_ADVANCEDSTOCK_DEFAULT');

                        if ($type_default == 2)
                        { // enabled
                            $id_selected_warehouse = SCI::getSelectedWarehouse();
                            if (!empty($id_selected_warehouse))
                            {
                                $stock_manager = StockManagerFactory::getManager();

                                // ADD IN WAREHOUSE
                                $wpl = new WarehouseProductLocation();
                                $wpl->id_product = (int) $newId;
                                $wpl->id_product_attribute = 0;
                                $wpl->id_warehouse = $id_selected_warehouse;
                                $wpl->save();
                            }

                            $value = 1;
                            $shops = SCI::getSelectedShopActionList(false, (int) $newId);
                            foreach ($shops as $shop)
                            {
                                StockAvailable::setProductDependsOnStock((int) $newId, true, $shop);
                            }
                        }
                        elseif ($type_default == 3)
                        { // enabled + manual
                            $value = 1;
                            $shops = SCI::getSelectedShopActionList(false, (int) $newId);
                            foreach ($shops as $shop)
                            {
                                StockAvailable::setProductDependsOnStock((int) $newId, false, $shop);
                            }

                            $id_selected_warehouse = SCI::getSelectedWarehouse();
                            if (!empty($id_selected_warehouse))
                            {
                                $wpl = new WarehouseProductLocation();
                                $wpl->id_product = (int) $newId;
                                $wpl->id_product_attribute = 0;
                                $wpl->id_warehouse = $id_selected_warehouse;
                                $wpl->save();
                            }
                        }

                        if (!empty($type_default) && sc_in_array($type_default, array(2, 3), 'catProductUpdateQueue_asmTypes'))
                        {
                            $sql = 'UPDATE '._DB_PREFIX_."product SET `advanced_stock_management`='".psql(html_entity_decode($value))."' WHERE id_product=".(int) $newId;
                            Db::getInstance()->Execute($sql);

                            $sql = 'UPDATE '._DB_PREFIX_."product_shop SET `advanced_stock_management`='".psql(html_entity_decode($value))."' WHERE id_product=".(int) $newId.' AND id_shop IN ('.pInSQL(SCI::getSelectedShopActionList(true)).')';
                            Db::getInstance()->Execute($sql);
                        }
                        else
                        {
                            if (!empty($return_datas['newQuantity']))
                            {
                                $p = new Product((int) $newId, false, null, (int) SCI::getSelectedShop());
                                $p->quantity = $return_datas['newQuantity'];
                                if (SCMS)
                                {
                                    $p->id_shop_default = (int) SCI::getSelectedShop();
                                    $p->id_shop_list = SCI::getSelectedShopActionList();
                                }
                                $p->save();
                                if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                                {
                                    foreach (SCI::getSelectedShopActionList(false, $newId) as $id_shop)
                                    {
                                        SCI::updateQuantity($newId, null, $return_datas['newQuantity'], $id_shop);
                                    }
                                }
                            }
                        }
                    }
                    else
                    {
                        if (!empty($return_datas['newQuantity']))
                        {
                            $p = new Product((int) $newId, false, null, (int) SCI::getSelectedShop());
                            $p->quantity = $return_datas['newQuantity'];
                            if (SCMS)
                            {
                                $p->id_shop_default = (int) SCI::getSelectedShop();
                                $p->id_shop_list = SCI::getSelectedShopActionList();
                            }
                            $p->save();
                            if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                            {
                                foreach (SCI::getSelectedShopActionList(false, $newId) as $id_shop)
                                {
                                    SCI::updateQuantity($newId, null, $return_datas['newQuantity'], $id_shop);
                                }
                            }
                        }
                    }

                    if (!empty($newId))
                    {
                        ## Amazon
                        if (defined('SC_Amazon_ACTIVE') && SC_Amazon_ACTIVE == 1 && SCI::moduleIsInstalled('amazon'))
                        {
                            $sql = 'UPDATE '._DB_PREFIX_.'marketplace_product_action SET action = "a" WHERE id_product = '.(int) $newId;
                            Db::getInstance()->execute($sql);
                        }

                        $callbacks = str_replace('{newid}', $newId, $callbacks);
                    }

                    if (_s('CAT_APPLY_ALL_CART_RULES'))
                    {
                        SpecificPriceRule::applyAllRules(array((int) $newId));
                    }
                }
                elseif (!empty($action) && $action == 'delete' && !empty($gr_id))
                {
                    $product = new Product((int) $gr_id);
                    if (SCMS)
                    {
                        $id_shop_list_array = Product::getShopsByProduct($product->id);
                        $id_shop_list = array();
                        foreach ($id_shop_list_array as $array_shop)
                        {
                            $id_shop_list[] = $array_shop['id_shop'];
                        }
                        $product->id_shop_list = $id_shop_list;
                    }
                    $product->delete();
                    addToHistory('catalog_tree', 'delete', 'product', (int) $product->id, null, _DB_PREFIX_.'product', null, null);
                }
                elseif (!empty($action) && $action == 'update' && !empty($gr_id))
                {
                    $return_datas['newQuantity'] = '';
                    $ecotaxrate = SCI::getEcotaxTaxRate();
                    $id_lang = (int) Tools::getValue('id_lang');
                    $id_specific_price = (int) Tools::getValue('id_specific_price', 0);
                    $id_product = $id_product; // for compatibility with old extensions - DO NOT REMOVE

                    $prod = new Product((int) $id_product, false, null, (int) SCI::getSelectedShop());
                    SCI::hookExec('actionObjectProductUpdateBefore', array('object' => $prod));

                    $fields = array('reference', 'wholesale_price', 'price', 'unit_price_ratio', 'unit_price_inc_tax', 'unity', 'ecotax', 'weight', 'supplier_reference', 'id_manufacturer', 'id_supplier',
                            'id_tax', 'id_tax_rules_group', 'ean13', 'location', 'reduction_price', 'reduction_percent', 'reduction_from', 'reduction_to', 'on_sale',
                            'out_of_stock', 'active', 'date_add', 'id_color_default', 'minimal_quantity', 'upc', 'width', 'height', 'depth', 'is_virtual',
                            'available_for_order', 'show_price', 'online_only', 'condition', 'additional_shipping_cost', 'visibility', 'available_date', 'redirect_type', 'id_product_redirected', );
                    $fields_allow_null_value_when_empty = array('low_stock_threshold');
                    if (SCAS)
                    {
                        $fields[] = 'advanced_stock_management';
                        $fields[] = 'location_warehouse';
                    }
                    // PACK
                    if (version_compare(_PS_VERSION_, '1.6.1.14', '>='))
                    {
                        $fields[] = 'cache_is_pack';
                        $fields[] = 'pack_stock_type';
                    }
                    if (version_compare(_PS_VERSION_, '1.7.1.0', '>='))
                    {
                        $fields[] = 'show_condition';
                        $fields[] = 'isbn';
                        $fields[] = 'id_type_redirected';
                    }
                    $fields_lang = array('name', 'available_now', 'available_later', 'link_rewrite', 'meta_title', 'meta_description', 'meta_keywords', 'description_short', 'description');
                    if (version_compare(_PS_VERSION_, '1.7.3.0', '>='))
                    {
                        $fields[] = 'additional_delivery_times';
                        $fields_lang[] = 'delivery_in_stock';
                        $fields_lang[] = 'delivery_out_stock';
                        $fields[] = 'low_stock_alert';
                        $fields[] = 'low_stock_threshold';
                    }
                    if (version_compare(_PS_VERSION_, '1.7.5.0', '>='))
                    {
                        $fields[] = 'location_new';
                    }
                    if (version_compare(_PS_VERSION_, '1.7.7.0', '>='))
                    {
                        $fields[] = 'mpn';
                    }
                    $forceUpdateCombinations = array('price_inc_tax', 'price', 'id_tax', 'id_tax_rules_group', 'ecotax');
                    $fieldsWithHTML = array('description', 'description_short');

                    sc_ext::readCustomGridsConfigXML('updateSettings');
                    sc_ext::readCustomGridsConfigXML('onBeforeUpdateSQL');
                    $reduction_updated_fields = array('from', 'to', 'reduction', 'reduction_type', 'id_group', 'id_currency', 'id_country', 'from_quantity', 'price');
                    if (version_compare(_PS_VERSION_, '1.6.0.11', '>='))
                    {
                        $reduction_updated_fields[] = 'reduction_tax';
                        $fields[] = 'reduction_tax';
                    }
                    $todo = array();
                    $todoshop = array();
                    $todo_lang = array();
                    $return_datas['newQuantity'] = '';
                    $versSuffix = '';
                    if (isset($_POST['price_inc_tax']) || isset($_POST['ecotax']))
                    {
                        $tax = Tools::getValue('tax', 1) * 1;
                        $ecotax = Tools::getValue('ecotax', 0) * 1;
                        if ($tax == 'NaN' || $tax == 0)
                        {
                            $tax = 1;
                        }
                        if (
                                (version_compare(_PS_VERSION_, '1.5.0.0', '>=') && (int) SCI::getConfigurationValue('PS_USE_ECOTAX', null, 0, SCI::getSelectedShop()) == 1)
                                ||
                                (version_compare(_PS_VERSION_, '1.5.0.0', '<') && (int) SCI::getConfigurationValue('PS_USE_ECOTAX') == 1)
                        ) {
                            $_POST['price'] = (Tools::getValue('price_inc_tax') * 1 - (_s('CAT_PROD_ECOTAXINCLUDED') ? $ecotax : 0)) / $tax;
                        }
                        else
                        {
                            $_POST['price'] = (Tools::getValue('price_inc_tax') * 1) / $tax;
                        }
                        if (isset($_POST['ecotax']))
                        {
                            $_POST['ecotax'] = Tools::getValue('ecotax') / $ecotaxrate;
                        }
                    }
                    foreach ($fields as $field)
                    {
                        if (isset($_POST[$field]))
                        {
                            if (sc_in_array($field, array('reduction_price', 'reduction_percent', 'reduction_from', 'reduction_to', 'reduction_tax', 'unit_price_ratio', 'unit_price_inc_tax', 'price'), 'catProductUpdateQueue_specialFields'))
                            {
                                if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                                {
                                    $versSuffix = '15';
                                }
                                switch ($field.$versSuffix){
                                    case 'id_supplier':
                                        $value = Tools::getValue($field);
                                        if (empty($value))
                                        {
                                            $sql = 'UPDATE '._DB_PREFIX_."product SET id_supplier='".psql($value)."',product_supplier_reference=NULL WHERE id_product=" .(int) $id_product;
                                            Db::getInstance()->Execute($sql);
                                        }
                                        else
                                        {
                                            $sql = 'UPDATE '._DB_PREFIX_."product SET id_supplier='".psql($value)."' WHERE id_product=" .(int) $id_product;
                                            Db::getInstance()->Execute($sql);
                                        }
                                        break;
                                    case 'reduction_price':
                                        $res = Db::getInstance()->getRow('SELECT COUNT(*) AS nb,reduction_type,reduction FROM '._DB_PREFIX_.'specific_price WHERE id_product='.(int) $id_product.' AND id_group=0 AND id_currency=0 AND id_country=0 AND from_quantity=1 GROUP BY reduction_type');
                                        if ($res['reduction_type'] == 'amount' && (int) Tools::getValue($field) == 0)
                                        {
                                            $sql = 'DELETE FROM '._DB_PREFIX_.'specific_price WHERE id_product='.(int) $id_product.' AND id_group=0 AND id_currency=0 AND id_country=0 AND from_quantity=1';
                                            Db::getInstance()->Execute($sql);
                                        }
                                        else
                                        {
                                            if ((int) $res['nb'] > 0)
                                            {
                                                $sql = 'UPDATE IGNORE '._DB_PREFIX_."specific_price SET reduction='".psql(Tools::getValue($field))."',reduction_type='amount' WHERE id_product=".(int) $id_product.' AND id_group=0 AND id_currency=0 AND id_country=0 AND from_quantity=1';
                                                Db::getInstance()->Execute($sql);
                                            }
                                            elseif ((int) Tools::getValue($field) != 0)
                                            {
                                                $sql = 'INSERT IGNORE INTO '._DB_PREFIX_."specific_price (`from`,`to`,reduction,reduction_type,id_product,id_group,id_currency,id_country,from_quantity) VALUES ('0000-00-00 00:00:00','0000-00-00 00:00:00','".psql(Tools::getValue($field))."','amount',".(int) $id_product.',0,0,0,1)';
                                                Db::getInstance()->Execute($sql);
                                            }
                                        }
                                        addToHistory('catalog_tree', 'modification', $field, (int) $id_product, $id_lang, _DB_PREFIX_.'specific_price', psql(Tools::getValue($field)), $res['reduction']);
                                        break;
                                    case 'reduction_tax15':
                                        // UPDATE
                                        if (!empty($id_specific_price))
                                        {
                                            $sql = 'UPDATE IGNORE '._DB_PREFIX_."specific_price SET reduction_tax='".(int) Tools::getValue($field)."' WHERE id_specific_price=" .(int) $id_specific_price;
                                            Db::getInstance()->Execute($sql);
                                        }
                                        // INSERT
                                        else
                                        {
                                            $sql = 'INSERT IGNORE INTO '._DB_PREFIX_."specific_price (`from`,`to`,reduction_type,id_product,id_group,id_currency,id_country,from_quantity,id_customer,id_product_attribute,price,id_shop,reduction_tax) VALUES ('0000-00-00 00:00:00','0000-00-00 00:00:00','amount',".(int) $id_product.',0,0,0,1,0,0,-1,'.SCI::getSelectedShop().",'".(int) Tools::getValue($field)."')";
                                            Db::getInstance()->Execute($sql);
                                            $id_specific_price = Db::getInstance()->Insert_ID();
                                        }

                                        if (SCMS && SCI::getSelectedShop() > 0)
                                        {
                                            $sql_specific_price = 'SELECT *
                                        FROM `'._DB_PREFIX_."specific_price`
                                        WHERE id_specific_price = '".$id_specific_price."'";
                                            $original_specific_price = Db::getInstance()->getRow($sql_specific_price);
                                            $shops = SCI::getSelectedShopActionList(false, $id_product);
                                            foreach ($shops as $shop_id)
                                            {
                                                // Si ce n'est pas la shop sélectionné
                                                // et si le produit est lié à cette shop
                                                if ($shop_id != SCI::getSelectedShop())
                                                {
                                                    $sql_specific_price = 'SELECT id_specific_price
                                        FROM `'._DB_PREFIX_."specific_price`
                                        WHERE id_product = '".$id_product."'
                                             AND `from` <= '".date('Y-m-d H:i:s')."'
                                             AND (`to` >= '".date('Y-m-d H:i:s')."' OR `to`='0000-00-00 00:00:00')
                                             AND (
                                                     `reduction` >= 0
                                                     OR `price` >= 0
                                                 )
                                             AND id_shop = '".$shop_id."'
                                         LIMIT 1";
                                                    $res_specific_price = Db::getInstance()->executeS($sql_specific_price);
                                                    // UPDATE
                                                    if (!empty($res_specific_price[0]['id_specific_price']))
                                                    {
                                                        $update = '';
                                                        foreach ($reduction_updated_fields as $reduction_updated_field)
                                                        {
                                                            if (!empty($update))
                                                            {
                                                                $update .= ', ';
                                                            }
                                                            $update .= '`'.bqSQL($reduction_updated_field)."` = '".psql($original_specific_price[$reduction_updated_field])."'";
                                                        }

                                                        $res_specific_price = $res_specific_price[0];
                                                        $sql = 'UPDATE IGNORE '._DB_PREFIX_.'specific_price SET '.$update." WHERE id_specific_price=" .(int) $res_specific_price['id_specific_price'];
                                                        Db::getInstance()->Execute($sql);
                                                    }
                                                    // INSERT
                                                    else
                                                    {
                                                        $insert = '';
                                                        $insert_values = '';
                                                        foreach ($reduction_updated_fields as $reduction_updated_field)
                                                        {
                                                            $insert .= ',`'.bqSQL($reduction_updated_field).'`';
                                                            $insert_values .= ",'".psql($original_specific_price[$reduction_updated_field])."'";
                                                        }
                                                        $sql = 'INSERT IGNORE INTO '._DB_PREFIX_.'specific_price (id_product,id_shop'.$insert.') VALUES ('.(int) $id_product.','.(int) $shop_id.$insert_values.')';
                                                        Db::getInstance()->Execute($sql);
                                                    }
                                                }
                                            }
                                        }
                                        addToHistory('catalog_tree', 'modification', $field, (int) $id_product, $id_lang, _DB_PREFIX_.'specific_price', psql(Tools::getValue($field)), $res['reduction']);
                                        break;
                                    case 'reduction_price15':
                                        // UPDATE
                                        if (!empty($id_specific_price))
                                        {
                                            $sql = 'UPDATE IGNORE '._DB_PREFIX_."specific_price SET reduction='".psql(Tools::getValue($field))."',reduction_type='amount' WHERE id_specific_price=" .(int) $id_specific_price;
                                            Db::getInstance()->Execute($sql);
                                        }
                                        // INSERT
                                        else
                                        {
                                            $sql = 'INSERT IGNORE INTO '._DB_PREFIX_.'specific_price (`from`,`to`,reduction,reduction_type,id_product,id_group,id_currency,id_country,from_quantity,id_shop,price'.(version_compare(_PS_VERSION_, '1.6.0.11', '>=') ? ',reduction_tax' : '').")
                                        VALUES ('0000-00-00 00:00:00','0000-00-00 00:00:00','".psql(Tools::getValue($field))."','amount',".(int) $id_product.',0,0,0,1,'.SCI::getSelectedShop().",'-1'".(version_compare(_PS_VERSION_, '1.6.0.11', '>=') ? ",'"._s('CAT_PROD_SPECIFIC_PRICES_DEFAULT_TAX')."'" : '').')';
                                            Db::getInstance()->Execute($sql);
                                            $id_specific_price = Db::getInstance()->Insert_ID();
                                        }

                                        if (SCMS && SCI::getSelectedShop() > 0)
                                        {
                                            $sql_specific_price = 'SELECT *
                                        FROM `'._DB_PREFIX_."specific_price`
                                        WHERE id_specific_price = '".$id_specific_price."'";
                                            $original_specific_price = Db::getInstance()->getRow($sql_specific_price);
                                            $shops = SCI::getSelectedShopActionList(false, $id_product);
                                            foreach ($shops as $shop_id)
                                            {
                                                // Si ce n'est pas la shop sélectionné
                                                // et si le produit est lié à cette shop
                                                if ($shop_id != SCI::getSelectedShop())
                                                {
                                                    $sql_specific_price = 'SELECT id_specific_price
                                        FROM `'._DB_PREFIX_."specific_price`
                                        WHERE id_product = '".$id_product."'
                                             AND `from` <= '".date('Y-m-d H:i:s')."'
                                             AND (`to` >= '".date('Y-m-d H:i:s')."' OR `to`='0000-00-00 00:00:00')
                                             AND (
                                                     `reduction` >= 0
                                                     OR `price` >= 0
                                                 )
                                             AND id_shop = '".$shop_id."'
                                         LIMIT 1";
                                                    $res_specific_price = Db::getInstance()->executeS($sql_specific_price);
                                                    // UPDATE
                                                    if (!empty($res_specific_price[0]['id_specific_price']))
                                                    {
                                                        $update = '';
                                                        foreach ($reduction_updated_fields as $reduction_updated_field)
                                                        {
                                                            if (!empty($update))
                                                            {
                                                                $update .= ', ';
                                                            }
                                                            $update .= '`'.bqSQL($reduction_updated_field)."` = '".psql($original_specific_price[$reduction_updated_field])."'";
                                                        }

                                                        $res_specific_price = $res_specific_price[0];
                                                        $sql = 'UPDATE IGNORE '._DB_PREFIX_.'specific_price SET '.$update." WHERE id_specific_price=" .(int) $res_specific_price['id_specific_price'];
                                                        Db::getInstance()->Execute($sql);
                                                    }
                                                    // INSERT
                                                    else
                                                    {
                                                        $insert = '';
                                                        $insert_values = '';
                                                        foreach ($reduction_updated_fields as $reduction_updated_field)
                                                        {
                                                            $insert .= ',`'.bqSQL($reduction_updated_field).'`';
                                                            $insert_values .= ",'".psql($original_specific_price[$reduction_updated_field])."'";
                                                        }
                                                        $sql = 'INSERT IGNORE INTO '._DB_PREFIX_.'specific_price (id_product,id_shop'.$insert.') VALUES ('.(int) $id_product.','.(int) $shop_id.$insert_values.')';
                                                        Db::getInstance()->Execute($sql);
                                                    }
                                                }
                                            }
                                        }
                                        addToHistory('catalog_tree', 'modification', $field, (int) $id_product, $id_lang, _DB_PREFIX_.'specific_price', psql(Tools::getValue($field)), '-');
                                        break;
                                    case 'reduction_percent':
                                        $res = Db::getInstance()->getRow('SELECT COUNT(*) AS nb,reduction_type,reduction FROM '._DB_PREFIX_.'specific_price WHERE id_product='.(int) $id_product.' AND id_group=0 AND id_currency=0 AND id_country=0 AND from_quantity=1 GROUP BY reduction_type');
                                        if ($res['reduction_type'] == 'percentage' && (int) Tools::getValue($field) == 0)
                                        {
                                            $sql = 'DELETE FROM '._DB_PREFIX_.'specific_price WHERE id_product='.(int) $id_product.' AND id_group=0 AND id_currency=0 AND id_country=0 AND from_quantity=1';
                                            Db::getInstance()->Execute($sql);
                                        }
                                        else
                                        {
                                            if ((int) $res['nb'] > 0)
                                            {
                                                $sql = 'UPDATE IGNORE '._DB_PREFIX_."specific_price SET reduction='".psql(Tools::getValue($field) / 100)."',reduction_type='percentage' WHERE id_product=".(int) $id_product.' AND id_group=0 AND id_currency=0 AND id_country=0 AND from_quantity=1';
                                                Db::getInstance()->Execute($sql);
                                            }
                                            elseif ((int) Tools::getValue($field) != 0)
                                            {
                                                $sql = 'INSERT IGNORE INTO `'._DB_PREFIX_."specific_price` (`from`,`to`,`reduction`,`reduction_type`,`id_product`,`id_group`,`id_currency`,`id_country`,`from_quantity`) VALUES ('0000-00-00 00:00:00','0000-00-00 00:00:00','".psql(Tools::getValue($field) / 100)."','percentage',".(int) $id_product.',0,0,0,1)';
                                                Db::getInstance()->Execute($sql);
                                            }
                                        }
                                        addToHistory('catalog_tree', 'modification', $field, (int) $id_product, $id_lang, _DB_PREFIX_.'specific_price', psql(Tools::getValue($field)), $res['reduction'] * 100);
                                        break;
                                    case 'reduction_percent15':
                                        // UPDATE
                                        if (!empty($id_specific_price))
                                        {
                                            $sql = 'UPDATE IGNORE '._DB_PREFIX_."specific_price SET reduction='".psql(Tools::getValue($field) / 100)."',reduction_type='percentage' WHERE id_specific_price=" .(int) $id_specific_price;
                                            Db::getInstance()->Execute($sql);
                                        }
                                        // INSERT
                                        else
                                        {
                                            $sql = 'INSERT IGNORE INTO '._DB_PREFIX_.'specific_price (`from`,`to`,reduction,reduction_type,id_product,id_group,id_currency,id_country,from_quantity,id_customer,id_product_attribute,price,id_shop'.(version_compare(_PS_VERSION_, '1.6.0.11', '>=') ? ',reduction_tax' : '').") VALUES ('0000-00-00 00:00:00','0000-00-00 00:00:00','".psql(Tools::getValue($field) / 100)."','percentage',".(int) $id_product.',0,0,0,1,0,0,-1,'.SCI::getSelectedShop().(version_compare(_PS_VERSION_, '1.6.0.11', '>=') ? ",'"._s('CAT_PROD_SPECIFIC_PRICES_DEFAULT_TAX')."'" : '').')';
                                            Db::getInstance()->Execute($sql);
                                            $id_specific_price = Db::getInstance()->Insert_ID();
                                        }

                                        if (SCMS && SCI::getSelectedShop() > 0)
                                        {
                                            $sql_specific_price = 'SELECT *
                                        FROM `'._DB_PREFIX_."specific_price`
                                        WHERE id_specific_price = '".$id_specific_price."'";
                                            $original_specific_price = Db::getInstance()->executeS($sql_specific_price);
                                            $original_specific_price = $original_specific_price[0];
                                            $shops = SCI::getSelectedShopActionList(false, $id_product);
                                            foreach ($shops as $shop_id)
                                            {
                                                // Si ce n'est pas la shop sélectionné
                                                // et si le produit est lié à cette shop
                                                if ($shop_id != SCI::getSelectedShop())
                                                {
                                                    $sql_specific_price = 'SELECT id_specific_price
                                        FROM `'._DB_PREFIX_."specific_price`
                                        WHERE id_product = '".$id_product."'
                                             AND `from` <= '".date('Y-m-d H:i:s')."'
                                             AND (`to` >= '".date('Y-m-d H:i:s')."' OR `to`='0000-00-00 00:00:00')
                                             AND (
                                                     `reduction` >= 0
                                                     OR `price` >= 0
                                                 )
                                             AND id_shop = '".$shop_id."'
                                         LIMIT 1";
                                                    $res_specific_price = Db::getInstance()->executeS($sql_specific_price);
                                                    // UPDATE
                                                    if (!empty($res_specific_price[0]['id_specific_price']))
                                                    {
                                                        $update = '';
                                                        foreach ($reduction_updated_fields as $reduction_updated_field)
                                                        {
                                                            if (!empty($update))
                                                            {
                                                                $update .= ', ';
                                                            }
                                                            $update .= '`'.bqSQL($reduction_updated_field)."` = '".psql($original_specific_price[$reduction_updated_field])."'";
                                                        }

                                                        $res_specific_price = $res_specific_price[0];
                                                        $sql = 'UPDATE IGNORE '._DB_PREFIX_.'specific_price SET '.$update." WHERE id_specific_price=" .(int) $res_specific_price['id_specific_price'];
                                                        Db::getInstance()->Execute($sql);
                                                    }
                                                    // INSERT
                                                    else
                                                    {
                                                        $insert = '';
                                                        $insert_values = '';
                                                        foreach ($reduction_updated_fields as $reduction_updated_field)
                                                        {
                                                            $insert .= ',`'.bqSQL($reduction_updated_field).'`';
                                                            $insert_values .= ",'".psql($original_specific_price[$reduction_updated_field])."'";
                                                        }
                                                        $sql = 'INSERT IGNORE INTO '._DB_PREFIX_.'specific_price (id_product,id_shop'.$insert.') VALUES ('.(int) $id_product.','.(int) $shop_id.$insert_values.')';
                                                        Db::getInstance()->Execute($sql);
                                                    }
                                                }
                                            }
                                        }
                                        addToHistory('catalog_tree', 'modification', $field, (int) $id_product, $id_lang, _DB_PREFIX_.'specific_price', psql(Tools::getValue($field)), '-');
                                        break;
                                    case 'reduction_from':
                                        $value = Tools::getValue($field);
                                        if ($value == '')
                                        {
                                            $value = '0000-00-00 00:00:00';
                                        }
                                        $res = Db::getInstance()->getRow('SELECT COUNT(*) AS nb,`from` AS dfrom,`to` AS dto FROM '._DB_PREFIX_.'specific_price WHERE id_product='.(int) $id_product.' AND id_group=0 AND id_currency=0 AND id_country=0 AND from_quantity=1 GROUP BY id_product');
                                        $othervalue = $res['dto'];
                                        if ($value == $othervalue)
                                        {
                                            $value = '0000-00-00 00:00:00';
                                            $othervalue = '0000-00-00 00:00:00';
                                        }
                                        if ((int) $res['nb'] > 0)
                                        {
                                            $sql = 'UPDATE IGNORE '._DB_PREFIX_."specific_price SET `from`='".psql($value)."',`to`='".psql($othervalue)."' WHERE id_product=".(int) $id_product.' AND id_group=0 AND id_currency=0 AND id_country=0 AND from_quantity=1';
                                            Db::getInstance()->Execute($sql);
                                        }
                                        else
                                        {
                                            $sql = 'INSERT IGNORE INTO '._DB_PREFIX_."specific_price (`from`,`to`,reduction_type,id_product,id_group,id_currency,id_country,from_quantity) VALUES ('".psql($value)."','".psql($value)."','amount',".(int) $id_product.',0,0,0,1)';
                                            Db::getInstance()->Execute($sql);
                                        }
                                        addToHistory('catalog_tree', 'modification', $field, (int) $id_product, $id_lang, _DB_PREFIX_.'specific_price', psql($value), $res['dfrom']);
                                        break;
                                    case 'reduction_from15':
                                        $value = Tools::getValue($field);
                                        if ($value == '')
                                        {
                                            $value = '0000-00-00 00:00:00';
                                        }
                                        $res = Db::getInstance()->getRow('SELECT COUNT(*) AS nb,`from` AS dfrom,`to` AS dto FROM '._DB_PREFIX_.'specific_price WHERE id_product='.(int) $id_product.' AND id_group=0 AND id_currency=0 AND id_country=0 AND from_quantity=1 AND id_customer=0 AND id_product_attribute=0 GROUP BY id_product');
                                        $othervalue = $res['dto'];
                                        if ($value == $othervalue && $value != '0000-00-00 00:00:00')
                                        {
                                            $othervalue = '0000-00-00 00:00:00';
                                        }
                                        // UPDATE
                                        if (!empty($id_specific_price))
                                        {
                                            $sql = 'UPDATE IGNORE '._DB_PREFIX_."specific_price SET `from`='".psql($value)."',`to`='".psql($othervalue)."' WHERE id_specific_price=" .(int) $id_specific_price;
                                            Db::getInstance()->Execute($sql);
                                        }
                                        // INSERT
                                        else
                                        {
                                            $sql = 'INSERT IGNORE INTO '._DB_PREFIX_.'specific_price (`from`,`to`,reduction_type,id_product,id_group,id_currency,id_country,from_quantity,id_customer,id_product_attribute,price,id_shop'.(version_compare(_PS_VERSION_, '1.6.0.11', '>=') ? ',reduction_tax' : '').") VALUES ('".psql($value)."','".psql($othervalue)."','amount',".(int) $id_product.',0,0,0,1,0,0,-1,'.SCI::getSelectedShop().(version_compare(_PS_VERSION_, '1.6.0.11', '>=') ? ",'"._s('CAT_PROD_SPECIFIC_PRICES_DEFAULT_TAX')."'" : '').')';
                                            Db::getInstance()->Execute($sql);
                                            $id_specific_price = Db::getInstance()->Insert_ID();
                                        }

                                        if (SCMS && SCI::getSelectedShop() > 0)
                                        {
                                            $sql_specific_price = 'SELECT *
                                        FROM `'._DB_PREFIX_."specific_price`
                                        WHERE id_specific_price = '".$id_specific_price."'";
                                            $original_specific_price = Db::getInstance()->getRow($sql_specific_price);
                                            $shops = SCI::getSelectedShopActionList(false, $id_product);
                                            foreach ($shops as $shop_id)
                                            {
                                                // Si ce n'est pas la shop sélectionné
                                                // et si le produit est lié à cette shop
                                                if ($shop_id != SCI::getSelectedShop())
                                                {
                                                    $sql_specific_price = 'SELECT id_specific_price
                                        FROM `'._DB_PREFIX_."specific_price`
                                        WHERE id_product = '".$id_product."'
                                             AND `from` <= '".date('Y-m-d H:i:s')."'
                                             AND (`to` >= '".date('Y-m-d H:i:s')."' OR `to`='0000-00-00 00:00:00')
                                             AND (
                                                     `reduction` >= 0
                                                     OR `price` >= 0
                                                 )
                                             AND id_shop = '".$shop_id."'
                                         LIMIT 1";
                                                    $res_specific_price = Db::getInstance()->executeS($sql_specific_price);
                                                    // UPDATE
                                                    if (!empty($res_specific_price[0]['id_specific_price']))
                                                    {
                                                        $update = '';
                                                        foreach ($reduction_updated_fields as $reduction_updated_field)
                                                        {
                                                            if (!empty($update))
                                                            {
                                                                $update .= ', ';
                                                            }
                                                            $update .= '`'.bqSQL($reduction_updated_field)."` = '".psql($original_specific_price[$reduction_updated_field])."'";
                                                        }

                                                        $res_specific_price = $res_specific_price[0];
                                                        $sql = 'UPDATE IGNORE '._DB_PREFIX_.'specific_price SET '.$update." WHERE id_specific_price=" .(int) $res_specific_price['id_specific_price'];
                                                        Db::getInstance()->Execute($sql);
                                                    }
                                                    // INSERT
                                                    else
                                                    {
                                                        $insert = '';
                                                        $insert_values = '';
                                                        foreach ($reduction_updated_fields as $reduction_updated_field)
                                                        {
                                                            $insert .= ',`'.bqSQL($reduction_updated_field).'`';
                                                            $insert_values .= ",'".psql($original_specific_price[$reduction_updated_field])."'";
                                                        }
                                                        $sql = 'INSERT IGNORE INTO '._DB_PREFIX_.'specific_price (id_product,id_shop'.$insert.') VALUES ('.(int) $id_product.','.(int) $shop_id.$insert_values.')';
                                                        Db::getInstance()->Execute($sql);
                                                    }
                                                }
                                            }
                                        }
                                        addToHistory('catalog_tree', 'modification', $field, (int) $id_product, $id_lang, _DB_PREFIX_.'specific_price', psql($value), $res['dfrom']);
                                        break;
                                    case 'reduction_to':
                                        $value = Tools::getValue($field);
                                        if ($value == '')
                                        {
                                            $value = '0000-00-00 00:00:00';
                                        }
                                        $res = Db::getInstance()->getRow('SELECT COUNT(*) AS nb,`from` AS dfrom,`to` AS dto FROM '._DB_PREFIX_.'specific_price WHERE id_product='.(int) $id_product.' AND id_group=0 AND id_currency=0 AND id_country=0 AND from_quantity=1 GROUP BY id_product');
                                        $othervalue = $res['dfrom'];
                                        if ($value == $othervalue)
                                        {
                                            $value = '0000-00-00 00:00:00';
                                            $othervalue = '0000-00-00 00:00:00';
                                        }
                                        if ((int) $res['nb'] > 0)
                                        {
                                            $sql = 'UPDATE IGNORE '._DB_PREFIX_."specific_price SET `from`='".psql($othervalue)."',`to`='".psql($value)."' WHERE id_product=".(int) $id_product.' AND id_group=0 AND id_currency=0 AND id_country=0 AND from_quantity=1';
                                            Db::getInstance()->Execute($sql);
                                        }
                                        else
                                        {
                                            $sql = 'INSERT IGNORE INTO '._DB_PREFIX_."specific_price (`from`,`to`,reduction_type,id_product,id_group,id_currency,id_country,from_quantity) VALUES ('".psql($value)."','".psql($value)."','amount',".(int) $id_product.',0,0,0,1)';
                                            Db::getInstance()->Execute($sql);
                                        }
                                        addToHistory('catalog_tree', 'modification', $field, (int) $id_product, $id_lang, _DB_PREFIX_.'specific_price', psql(Tools::getValue($field)), $res['dto']);
                                        break;
                                    case 'reduction_to15':
                                        $value = Tools::getValue($field);
                                        if ($value == '')
                                        {
                                            $value = '0000-00-00 00:00:00';
                                        }
                                        $res = Db::getInstance()->getRow('SELECT COUNT(*) AS nb,`from` AS dfrom,`to` AS dto FROM '._DB_PREFIX_.'specific_price WHERE id_product='.(int) $id_product.' AND id_group=0 AND id_currency=0 AND id_country=0 AND from_quantity=1 AND id_customer=0 AND id_product_attribute=0 GROUP BY id_product');
                                        $othervalue = $res['dfrom'];
                                        if ($value == $othervalue && $value != '0000-00-00 00:00:00')
                                        {
                                            $othervalue = '0000-00-00 00:00:00';
                                        }
                                        // UPDATE
                                        if (!empty($id_specific_price))
                                        {
                                            $sql = 'UPDATE IGNORE '._DB_PREFIX_."specific_price SET `from`='".psql($othervalue)."',`to`='".psql($value)."' WHERE id_specific_price=" .(int) $id_specific_price;
                                            Db::getInstance()->Execute($sql);
                                        }
                                        // INSERT
                                        else
                                        {
                                            $sql = 'INSERT IGNORE INTO '._DB_PREFIX_.'specific_price (`from`,`to`,reduction_type,id_product,id_group,id_currency,id_country,from_quantity,id_customer,id_product_attribute,price,id_shop'.(version_compare(_PS_VERSION_, '1.6.0.11', '>=') ? ',reduction_tax' : '').") VALUES ('".psql($othervalue)."','".psql($value)."','amount',".(int) $id_product.',0,0,0,1,0,0,-1,'.SCI::getSelectedShop().(version_compare(_PS_VERSION_, '1.6.0.11', '>=') ? ",'"._s('CAT_PROD_SPECIFIC_PRICES_DEFAULT_TAX')."'" : '').')';
                                            Db::getInstance()->Execute($sql);
                                            $id_specific_price = Db::getInstance()->Insert_ID();
                                        }

                                        if (SCMS && SCI::getSelectedShop() > 0)
                                        {
                                            $sql_specific_price = 'SELECT *
                                        FROM `'._DB_PREFIX_."specific_price`
                                        WHERE id_specific_price = '".$id_specific_price."'";
                                            $original_specific_price = Db::getInstance()->getRow($sql_specific_price);
                                            $shops = SCI::getSelectedShopActionList(false, $id_product);
                                            foreach ($shops as $shop_id)
                                            {
                                                // Si ce n'est pas la shop sélectionné
                                                // et si le produit est lié à cette shop
                                                if ($shop_id != SCI::getSelectedShop())
                                                {
                                                    $sql_specific_price = 'SELECT id_specific_price
                                        FROM `'._DB_PREFIX_."specific_price`
                                        WHERE id_product = '".$id_product."'
                                             AND `from` <= '".date('Y-m-d H:i:s')."'
                                             AND (`to` >= '".date('Y-m-d H:i:s')."' OR `to`='0000-00-00 00:00:00')
                                             AND (
                                                     `reduction` >= 0
                                                     OR `price` >= 0
                                                 )
                                             AND id_shop = '".$shop_id."'
                                         LIMIT 1";
                                                    $res_specific_price = Db::getInstance()->executeS($sql_specific_price);
                                                    // UPDATE
                                                    if (!empty($res_specific_price[0]['id_specific_price']))
                                                    {
                                                        $update = '';
                                                        foreach ($reduction_updated_fields as $reduction_updated_field)
                                                        {
                                                            if (!empty($update))
                                                            {
                                                                $update .= ', ';
                                                            }
                                                            $update .= '`'.bqSQL($reduction_updated_field)."` = '".psql($original_specific_price[$reduction_updated_field])."'";
                                                        }

                                                        $res_specific_price = $res_specific_price[0];
                                                        $sql = 'UPDATE IGNORE '._DB_PREFIX_.'specific_price SET '.$update." WHERE id_specific_price=" .(int) $res_specific_price['id_specific_price'];
                                                        Db::getInstance()->Execute($sql);
                                                    }
                                                    // INSERT
                                                    else
                                                    {
                                                        $insert = '';
                                                        $insert_values = '';
                                                        foreach ($reduction_updated_fields as $reduction_updated_field)
                                                        {
                                                            $insert .= ',`'.bqSQL($reduction_updated_field).'`';
                                                            $insert_values .= ",'".psql($original_specific_price[$reduction_updated_field])."'";
                                                        }
                                                        $sql = 'INSERT IGNORE INTO '._DB_PREFIX_.'specific_price (id_product,id_shop'.$insert.') VALUES ('.(int) $id_product.','.(int) $shop_id.$insert_values.')';
                                                        Db::getInstance()->Execute($sql);
                                                    }
                                                }
                                            }
                                        }
                                        addToHistory('catalog_tree', 'modification', $field, (int) $id_product, $id_lang, _DB_PREFIX_.'specific_price', psql(Tools::getValue($field)), $res['dto']);
                                        break;
                                    case 'unit_price_ratio':
                                    case 'unit_price_ratio15':
                                        if (floatval(Tools::getValue($field)) > 0)
                                        {
                                            $sql = 'UPDATE '._DB_PREFIX_.'product SET `unit_price_ratio`= price/'.floatval(Tools::getValue($field)).' WHERE id_product='.(int) $id_product;
                                            $todoshop[] = '`unit_price_ratio`=price/'.floatval(Tools::getValue($field));
                                        }
                                        Db::getInstance()->Execute($sql);

                                        addToHistory('catalog_tree', 'modification', $field, (int) $id_product, $id_lang, _DB_PREFIX_.'product', psql(Tools::getValue($field)), ($row->unit_price_ratio > 0 ? number_format($row->price / $row->unit_price_ratio, 2) : 0));
                                        break;
                                    case 'unit_price_inc_tax':
                                    case 'unit_price_inc_tax15':
                                        $unit_price_excl_tax = Tools::getValue('unity_price_excl_tax', 1) * 1;
                                        if (floatval($unit_price_excl_tax) > 0)
                                        {
                                            $sql = 'UPDATE '._DB_PREFIX_.'product SET `unit_price_ratio`= price/'.floatval($unit_price_excl_tax).' WHERE id_product='.(int) $id_product;
                                            $todoshop[] = '`unit_price_ratio`=price/'.floatval($unit_price_excl_tax);
                                        }
                                        Db::getInstance()->Execute($sql);
                                        break;
                                    case 'active':
                                        if (_r('ACT_CAT_ENABLE_PRODUCTS'))
                                        {
                                            if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                                            {
                                                $todoshop[] = "`active`='".psql(Tools::getValue($field))."'";
                                            }
                                            $todo[] = "`active`='".psql(Tools::getValue($field))."'";
                                        }
                                        break;
                                    case 'price':
                                    case 'price15':
                                        if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                                        {
                                            $sql = 'SELECT ps.unit_price_ratio,ps.price FROM '._DB_PREFIX_.'product_shop ps LEFT JOIN '._DB_PREFIX_.'product p ON (p.id_product=ps.id_product) WHERE ps.id_product='.(int) $id_product." AND ps.id_shop='".(SCI::getSelectedShop() > 0 ? (int) SCI::getSelectedShop() : 'p.id_shop_default')."'";
                                        }
                                        else
                                        {
                                            $sql = 'SELECT unit_price_ratio,price FROM '._DB_PREFIX_.'product WHERE id_product='.(int) $id_product;
                                        }
                                        $row = Db::getInstance()->getRow($sql);
                                        if ($row && $row['price'] > 0 && $row['unit_price_ratio'] > 0)
                                        {
                                            $ratio = floatval(Tools::getValue($field)) / ($row['price'] / $row['unit_price_ratio']);
                                        }
                                        else
                                        {
                                            $ratio = 0;
                                        }

                                        $sql = 'UPDATE '._DB_PREFIX_.'product SET `price`='.floatval(Tools::getValue($field)).',`unit_price_ratio`='.floatval($ratio).' WHERE id_product='.(int) $id_product;
                                        Db::getInstance()->Execute($sql);

                                        if (_s('CAT_PROD_PRICE_DEFAULT_COMBINATION') == 1)
                                        {
                                            $priceDiff = 0;
                                            $substract = 0;
                                            if (floatval($row['price']) > floatval(Tools::getValue($field)))
                                            {
                                                $priceDiff = floatval($row['price']) - floatval(Tools::getValue($field));
                                                $substract = 1;
                                            }
                                            else
                                            {
                                                $priceDiff = floatval(Tools::getValue($field)) - floatval($row['price']);
                                            }

                                            if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                                            {
                                                $sql = 'SELECT id_shop, id_product_attribute, price AS oldImpact
                                                    FROM '._DB_PREFIX_.'product_attribute_shop
                                                    WHERE id_product='.(int) $id_product.'
                                                    AND '.(SCI::getSelectedShop() > 0 ? (int) SCI::getSelectedShop() : 'p.id_shop_default').'
                                                    AND default_on IS NULL';
                                            }
                                            else
                                            {
                                                $sql = 'SELECT id_product_attribute, price AS oldImpact
                                                    FROM '._DB_PREFIX_.'product_attribute
                                                    WHERE id_product='.(int) $id_product.'
                                                    AND default_on IS NULL';
                                            }
                                            $row = Db::getInstance()->ExecuteS($sql);

                                            foreach ($row as $attribute)
                                            {
                                                if ($substract == 1)
                                                {
                                                    $newimpact = $attribute['oldImpact'] + $priceDiff;
                                                }
                                                else
                                                {
                                                    $newimpact = $attribute['oldImpact'] - $priceDiff;
                                                }

                                                Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'product_attribute
                                                                    SET price = '.floatval($newimpact).'
                                                                    WHERE id_product = '.(int) $id_product.'
                                                                     AND id_product_attribute = '.(int) $attribute['id_product_attribute']);
                                                if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                                                {
                                                    Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'product_attribute_shop
                                                                    SET price = '.floatval($newimpact).'
                                                                    WHERE id_product = '.(int) $id_product.'
                                                                     AND id_product_attribute = '.(int) $attribute['id_product_attribute'].'
                                                                     AND id_shop = '.(int) $attribute['id_shop']);
                                                }
                                            }
                                        }

                                        $todoshop[] = "`price`='".floatval(Tools::getValue($field))."'";
                                        $todoshop[] = "`unit_price_ratio`='".floatval($ratio)."'";
                                        addToHistory('catalog_tree', 'modification', $field, (int) $id_product, $id_lang, _DB_PREFIX_.'product', psql(Tools::getValue($field)), isset($row['price'])?$row['price']:'');
                                        break;
                                }
                            }
                            else
                            {
                                if (SCAS && $field == 'location_warehouse')
                                {
                                    $advanced_stock_management = Tools::getValue('type_advanced_stock_management');
                                    $val = Tools::getValue($field);
                                    if ($advanced_stock_management == 2 || $advanced_stock_management == 3)
                                    { // enabled OR enabled + manual
                                        $id_selected_warehouse = SCI::getSelectedWarehouse();
                                        if (!empty($id_selected_warehouse))
                                        {
                                            // ADD IN WAREHOUSE
                                            $exist = WarehouseProductLocation::getIdByProductAndWarehouse((int) $id_product, 0, (int) $id_selected_warehouse);
                                            if (empty($exist))
                                            {
                                                $wpl = new WarehouseProductLocation();
                                            }
                                            else
                                            {
                                                $wpl = new WarehouseProductLocation((int) $exist);
                                            }
                                            $wpl->id_product = (int) $id_product;
                                            $wpl->id_product_attribute = 0;
                                            $wpl->id_warehouse = (int) $id_selected_warehouse;
                                            $wpl->location = $val;
                                            $wpl->save();
                                        }
                                    }
                                }
                                elseif (SCAS && $field == 'advanced_stock_management')
                                {
                                    $value = 0;
                                    $val = Tools::getValue($field);
                                    if ($val == 1)
                                    { // disabled
                                        $value = 0;
                                        $shops = SCI::getSelectedShopActionList(false, (int) $id_product);
                                        foreach ($shops as $shop)
                                        {
                                            StockAvailable::setProductDependsOnStock((int) $id_product, false, $shop);
                                        }
                                    }
                                    elseif ($val == 2)
                                    { // enabled
                                        $id_selected_warehouse = SCI::getSelectedWarehouse();
                                        if (!empty($id_selected_warehouse))
                                        {
                                            $stock_manager = StockManagerFactory::getManager();

                                            // ADD IN WAREHOUSE
                                            $exist = WarehouseProductLocation::getIdByProductAndWarehouse((int) $id_product, 0, (int) $id_selected_warehouse);
                                            if (empty($exist))
                                            {
                                                $wpl = new WarehouseProductLocation();
                                                $wpl->id_product = (int) $id_product;
                                                $wpl->id_product_attribute = 0;
                                                $wpl->id_warehouse = (int) $id_selected_warehouse;
                                                $wpl->save();
                                            }

                                            $combinations = Db::getInstance()->executeS('
                                    SELECT *
                                    FROM `'._DB_PREFIX_.'product_attribute` pa
                                    WHERE pa.`id_product` = '.(int) $id_product);
                                            if (!empty($combinations) && count($combinations) > 0)
                                            {
                                                $warehouse = new Warehouse($id_selected_warehouse);

                                                foreach ($combinations as $combination)
                                                {
                                                    // ADD IN WAREHOUSE
                                                    $exist = WarehouseProductLocation::getIdByProductAndWarehouse((int) $id_product, (int) $combination['id_product_attribute'], (int) $id_selected_warehouse);
                                                    if (empty($exist))
                                                    {
                                                        $wpl = new WarehouseProductLocation();
                                                        $wpl->id_product = (int) $id_product;
                                                        $wpl->id_product_attribute = (int) $combination['id_product_attribute'];
                                                        $wpl->id_warehouse = (int) $id_selected_warehouse;
                                                        $wpl->save();
                                                    }

                                                    // EMPTY ACUTAL STOCK FOR COMBINATION
                                                    $query = new DbQuery();
                                                    $query->select('SUM(st.physical_quantity) as physical_quantity');
                                                    $query->from('stock', 'st');
                                                    $query->where('st.id_product = '.(int) $id_product);
                                                    $query->where('st.id_product_attribute = '.(int) $combination['id_product_attribute']);
                                                    $query->where('st.id_warehouse = '.(int) $id_selected_warehouse);
                                                    $avanced_quantities = Db::getInstance()->getRow($query);
                                                    if (!empty($avanced_quantities['physical_quantity']))
                                                    {
                                                        $stock_manager->removeProduct($id_product, $combination['id_product_attribute'], $warehouse, $avanced_quantities['physical_quantity'], 4, 1);
                                                    }

                                                    // ADD STOCK FOR COMBINATION
                                                    $price = 0;
                                                    $quantity = 0;
                                                    $res = Db::getInstance()->ExecuteS('SELECT sa.quantity, pas.wholesale_price
                                            FROM '._DB_PREFIX_.'stock_available sa
                                            INNER JOIN '._DB_PREFIX_.'product p ON (sa.id_product = p.id_product AND sa.id_shop = p.id_shop_default)
                                                INNER JOIN '._DB_PREFIX_."product_attribute_shop pas ON (pas.id_product_attribute = sa.id_product_attribute AND pas.id_shop = p.id_shop_default)
                                            WHERE sa.id_product='".(int) $id_product."'
                                            AND sa.id_product_attribute='".(int) $combination['id_product_attribute']."'");
                                                    if (!empty($res[0]['wholesale_price']))
                                                    {
                                                        $price = $res[0]['wholesale_price'];
                                                    }
                                                    if (!empty($res[0]['quantity']))
                                                    {
                                                        $quantity = $res[0]['quantity'];
                                                    }
                                                    if (!empty($quantity) && $quantity > 0)
                                                    {
                                                        $id_currency = (!empty($cookie->id_currency) ? $cookie->id_currency : SCI::getConfigurationValue('PS_CURRENCY_DEFAULT'));
                                                        if ($id_currency != $warehouse->id_currency)
                                                        {
                                                            $price_converted_to_default_currency = Tools::convertPrice($price, $id_currency, false);
                                                            $price = Tools::convertPrice($price_converted_to_default_currency, $warehouse->id_currency, true);
                                                        }
                                                        if ($quantity > 0)
                                                        {
                                                            $stock_manager->addProduct($id_product, $combination['id_product_attribute'], $warehouse, $quantity, 4, $price, 1);
                                                        }
                                                    }
                                                }
                                            }
                                            else
                                            {
                                                $warehouse = new Warehouse($id_selected_warehouse);

                                                // EMPTY ACUTAL STOCK FOR PRODUCT
                                                $query = new DbQuery();
                                                $query->select('SUM(st.physical_quantity) as physical_quantity');
                                                $query->from('stock', 'st');
                                                $query->where('st.id_product = '.(int) $id_product);
                                                $query->where('st.id_warehouse = '.(int) $id_selected_warehouse);
                                                $avanced_quantities = Db::getInstance()->getRow($query);
                                                if (!empty($avanced_quantities['physical_quantity']))
                                                {
                                                    $stock_manager->removeProduct($id_product, 0, $warehouse, $avanced_quantities['physical_quantity'], 4, 1);
                                                }

                                                // ADD STOCK FOR PRODUCT
                                                $price = 0;
                                                $quantity = 0;
                                                $res = Db::getInstance()->ExecuteS('SELECT sa.quantity, ps.wholesale_price
                                    FROM '._DB_PREFIX_.'stock_available sa
                                    INNER JOIN '._DB_PREFIX_.'product p ON (sa.id_product = p.id_product AND sa.id_shop = p.id_shop_default)
                                        INNER JOIN '._DB_PREFIX_."product_shop ps ON (ps.id_product = p.id_product AND ps.id_shop = p.id_shop_default)
                                    WHERE sa.id_product='".(int) $id_product."'
                                    AND sa.id_product_attribute=0");
                                                if (!empty($res[0]['wholesale_price']))
                                                {
                                                    $price = $res[0]['wholesale_price'];
                                                }
                                                if (!empty($res[0]['quantity']))
                                                {
                                                    $quantity = $res[0]['quantity'];
                                                }
                                                if (!empty($quantity) && $quantity > 0)
                                                {
                                                    $id_currency = SCI::getConfigurationValue('PS_CURRENCY_DEFAULT');
                                                    if (!empty($id_currency) && $id_currency != $warehouse->id_currency)
                                                    {
                                                        $price_converted_to_default_currency = Tools::convertPrice($price, $id_currency, false);
                                                        $price = Tools::convertPrice($price_converted_to_default_currency, $warehouse->id_currency, true);
                                                    }
                                                    if ($quantity > 0)
                                                    {
                                                        $stock_manager->addProduct($id_product, 0, $warehouse, $quantity, 4, $price, 1);
                                                    }
                                                }
                                            }
                                        }

                                        $value = 1;
                                        $shops = SCI::getSelectedShopActionList(false, (int) $id_product);
                                        foreach ($shops as $shop)
                                        {
                                            StockAvailable::setProductDependsOnStock((int) $id_product, true, $shop);
                                        }
                                    }
                                    elseif ($val == 3)
                                    { // enabled + manual
                                        $value = 1;
                                        $shops = SCI::getSelectedShopActionList(false, (int) $id_product);
                                        foreach ($shops as $shop)
                                        {
                                            StockAvailable::setProductDependsOnStock((int) $id_product, false, $shop);
                                        }

                                        $id_selected_warehouse = SCI::getSelectedWarehouse();
                                        if (!empty($id_selected_warehouse))
                                        {
                                            $exist = WarehouseProductLocation::getIdByProductAndWarehouse((int) $id_product, 0, (int) $id_selected_warehouse);
                                            if (empty($exist))
                                            {
                                                $wpl = new WarehouseProductLocation();
                                                $wpl->id_product = (int) $id_product;
                                                $wpl->id_product_attribute = 0;
                                                $wpl->id_warehouse = $id_selected_warehouse;
                                                $wpl->save();
                                            }

                                            $combinations = Db::getInstance()->executeS('
                                    SELECT *
                                    FROM `'._DB_PREFIX_.'product_attribute` pa
                                    WHERE pa.`id_product` = '.(int) $id_product);
                                            if (!empty($combinations) && count($combinations) > 0)
                                            {
                                                foreach ($combinations as $combination)
                                                {
                                                    $exist = WarehouseProductLocation::getIdByProductAndWarehouse((int) $id_product, (int) $combination['id_product_attribute'], (int) $id_selected_warehouse);
                                                    if (empty($exist))
                                                    {
                                                        $wpl = new WarehouseProductLocation();
                                                        $wpl->id_product = (int) $id_product;
                                                        $wpl->id_product_attribute = (int) $combination['id_product_attribute'];
                                                        $wpl->id_warehouse = (int) $id_selected_warehouse;
                                                        $wpl->save();
                                                    }
                                                }
                                            }
                                        }
                                    }
                                    $todo[] = '`'.bqSQL($field)."`='".psql(html_entity_decode($value))."'";
                                    $todoshop[] = '`'.bqSQL($field)."`='".psql(html_entity_decode($value))."'";
                                }
                                elseif (version_compare(_PS_VERSION_, '1.6.0.10', '>=')
                                    && $field == 'id_tax_rules_group'
                                    && count(SCI::getSelectedShopActionList())>1
                                ){ // si update taxe rule sur plusieurs boutiques, on verifie qu'elle est disponible pour tous les shops
                                    $val = Tools::getValue($field);
                                    $shopsToUpdate = SCI::getSelectedShopActionList();
                                    // sous requete pour recupérer le nom de la tax rule
                                    $taxNameSubQuery = new DbQuery();
                                    $taxNameSubQuery->select('name')
                                        ->from('tax_rules_group')
                                        ->where('id_tax_rules_group = '.(int)$val);

                                    // récupération des shops pour lesquels la taxe rule n'est pas disponible
                                    $shopsWithoutThisTaxRulePresentQuery = new DbQuery();
                                    $shopsWithoutThisTaxRulePresentQuery->select('s.id_shop,s.name as shop_name')
                                        ->select('('.$taxNameSubQuery->build().') as name')
                                        ->select('gs.id_tax_rules_group')
                                        ->from('shop','s')
                                        ->leftJoin('tax_rules_group_shop', 'gs', 's.id_shop = gs.id_shop AND gs.id_tax_rules_group = '.(int)$val)
                                        ->where('gs.id_tax_rules_group IS NULL')
                                        ->where('s.id_shop IN('.pInSQL(implode(',',array_values($shopsToUpdate))).')')
                                    ;
                                    $shopsWithoutThisTaxRulePresent = Db::getInstance()->executeS($shopsWithoutThisTaxRulePresentQuery->build());

                                    // construction du message
                                    if(!empty($shopsWithoutThisTaxRulePresent)){
                                        $shopsToExclude = array_column($shopsWithoutThisTaxRulePresent,'id_shop');
                                        $shopsToUpdate = array_diff($shopsToUpdate, $shopsToExclude);

                                        $readableShopList = implode(',',array_column($shopsWithoutThisTaxRulePresent,'shop_name'));
                                        $readableShopList = strrev(implode(strrev(' '._l('and').' '), explode(strrev(','), strrev($readableShopList), 2))); // one-liner pour remplacer la dernière occurence d'un caractère

                                        $message = _l('Taxe rule `%s` cannot be applied these shops: ', null,[$shopsWithoutThisTaxRulePresent[0]['name']]).$readableShopList;

                                        $return_datas['displayErrorMessage'] = $message;
                                    }


                                    $sql = 'UPDATE '._DB_PREFIX_.'product_shop SET id_tax_rules_group = '.(int)$val.' WHERE id_product='.(int) $id_product.' AND id_shop IN ('.pInSQL(implode(',',$shopsToUpdate)).')';
                                    Db::getInstance()->Execute($sql);
                                }
                                else
                                {
                                    $db_value = html_entity_decode(Tools::getValue($field));
                                    if ($field == 'date_add' && empty($db_value))
                                    {
                                        continue;
                                    }
                                    if ($field != 'location_new')
                                    {
                                        if ($db_value === "" && in_array($field, $fields_allow_null_value_when_empty))
                                        {
                                            $todo[] = '`'.bqSQL($field).'`=NULL';
                                        }
                                        else
                                        {
                                            switch($field) {
                                                case 'is_virtual':
                                                    $todo[] = '`' . bqSQL($field) . '`='.(int) $db_value;
                                                    if (version_compare(_PS_VERSION_, '1.7.8.0', '>=')) {
                                                        if($db_value) {
                                                            $product_type = PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductType::TYPE_VIRTUAL;
                                                        } else {
                                                            $product_type = PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductType::TYPE_STANDARD;
                                                        }
                                                        $todo[] = '`product_type`="' . psql($product_type) .'"';
                                                    }
                                                    break;
                                                default:
                                                    $todo[] = '`'.bqSQL($field)."`='".psql($db_value)."'";
                                            }
                                        }
                                    }
                                    if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')
                                            && ($def = ObjectModel::getDefinition('Product'))
                                            && isset($def['fields'][$field]['shop'])
                                            && $def['fields'][$field]['shop'])
                                    {
                                        if ($db_value === "" && in_array($field, $fields_allow_null_value_when_empty))
                                        {
                                            $todoshop[] = '`'.bqSQL($field).'`=NULL';
                                        }
                                        else
                                        {
                                            $todoshop[] = '`'.bqSQL($field)."`='".psql($db_value)."'";
                                        }
                                    }
                                }
                                if ($field == 'out_of_stock' && version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                                {
                                    $shops = SCI::getSelectedShopActionList(false, (int) $id_product);
                                    foreach ($shops as $shop)
                                    {
                                        StockAvailable::setProductOutOfStock($id_product, psql(html_entity_decode(Tools::getValue($field))), (int) $shop, 0);
                                    }
                                }
                                if ($field != 'location_warehouse' && $field != 'location_new')
                                {
                                    addToHistory('catalog_tree', 'modification', $field, (int) $id_product, $id_lang, _DB_PREFIX_.'product', psql(Tools::getValue($field)));
                                }
                                if ($field == 'location_new')
                                {
                                    StockAvailable::setLocation((int) $id_product, Tools::getValue($field), (int) SCI::getSelectedShop());
                                    addToHistory('catalog_tree', 'modification', $field, (int) $id_product, $id_lang, _DB_PREFIX_.'stock_available', psql(Tools::getValue($field)));
                                }
                            }
                        }
                    }

                    // force combinations update
                    foreach ($forceUpdateCombinations as $field)
                    {
                        if (isset($_POST[$field]))
                        {
                            $return_datas['doUpdateCombinationsOption'] = true;
                        }
                    }
                    ## Champ Combinations copy/paste
                    if (isset($_POST['combinations']) && substr(Tools::getValue('combinations'), 0, 13) == 'combinations_')
                    {
                        $doHookUpdateQuantity = false;
                        $return_datas['doUpdateCombinationsOption'] = true;
                        $return_datas['doRefreshGridColCombinations'] = true;
                        // get combination values
                        $prefixlen = strlen('combinations_');
                        $id_productsource = substr(Tools::getValue('combinations'), $prefixlen, strlen(Tools::getValue('combinations')));
                        if ($id_productsource != $id_product)
                        {
                            if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                            {
                                if(SCI::getSelectedShop() == 0){
                                    $id_shop_list_array = Product::getShopsByProduct($id_product);
                                    $id_shop_list = array();
                                    foreach ($id_shop_list_array as $array_shop)
                                    {
                                        $id_shop_list[] = $array_shop['id_shop'];
                                    }
                                } else {
                                    $id_shop_list[] = SCI::getSelectedShop();
                                }

                                $sql = 'SELECT pas.id_product_attribute, pas.price, pas.weight, pa.unit_price_impact, pas.ecotax, pa.reference, pa.ean13, pas.default_on,
                                   '.(SCAS ? ' (SELECT wpl.location FROM '._DB_PREFIX_.'warehouse_product_location wpl WHERE wpl.id_product_attribute=pa.id_product_attribute AND wpl.id_product='.(int) $id_productsource.' LIMIT 1) AS location, ' : (version_compare(_PS_VERSION_, '8.0.0', '>=') ? ' sa.location AS location, ' : ' pa.location AS location, ')).'
                                pa.upc, pas.minimal_quantity
                                FROM '._DB_PREFIX_.'product_attribute_shop pas
                                LEFT JOIN '._DB_PREFIX_.'product_attribute pa ON (pa.id_product_attribute=pas.id_product_attribute)
                                '.(version_compare(_PS_VERSION_, '8.0.0', '>=') ? ' LEFT JOIN '._DB_PREFIX_.'stock_available sa ON (sa.id_product_attribute=pas.id_product_attribute AND sa.id_shop=\''.(int) SCI::getSelectedShop().'\')' : '').'
                                WHERE pa.id_product='.(int) $id_productsource.' AND pas.id_shop IN ('.pInSql(implode(',', $id_shop_list)).')
                                GROUP BY pa.id_product_attribute, pas.id_shop';
                                $res = Db::getInstance()->ExecuteS($sql);
                                $dd = $sql;


                                $p = new Product($id_product, false, $id_lang, SCI::getSelectedShop());
                                $hasAttributes = (bool) $p->hasAttributes();
                                foreach ($res as $key => $row)
                                {
                                    if($hasAttributes){
                                        $row['default_on'] = 0;
                                    }
                                    $idco = $p->addAttribute($row['price'], $row['weight'], $row['unit_price_impact'], $row['ecotax'], 0, $row['reference'], $row['ean13'],
                                            $row['default_on'], $row['location'], $row['upc'], $row['minimal_quantity']);
                                    if ((int) $idco)
                                    {
                                        $sql = 'SELECT GROUP_CONCAT(id_attribute) AS ids FROM '._DB_PREFIX_.'product_attribute_combination WHERE id_product_attribute='.(int) $row['id_product_attribute'];
                                        $res = Db::getInstance()->getValue($sql);
                                        $ids_attribute = explode(',', $res);
                                        $combi = new Combination($idco);
                                        $combi->id_shop_list = $id_shop_list;
                                        $combi->setAttributes($ids_attribute);
                                        $combi->save();
                                    }
                                }
                            }
                            else
                            {
                                $sqlinsert = '';
                                $cols = array('`id_product`');
                                $sql = 'SELECT * FROM '._DB_PREFIX_.'product_attribute WHERE id_product='.(int) $id_productsource;
                                $res = Db::getInstance()->ExecuteS($sql);
                                foreach ($res as $key => $row)
                                {
                                    $sqlinsert = '';
                                    foreach ($row as $col => $val)
                                    {
                                        if ($col != 'id_product' && $col != 'id_product_attribute')
                                        {
                                            $sqlinsert .= '\''.psql($val).'\',';
                                        }
                                        if ($col != 'id_product' && $col != 'id_product_attribute' && !sc_in_array('`'.bqSQL($col).'`', $cols, 'catProductUpdateQueue_checkInCols'))
                                        {
                                            $cols[] = '`'.bqSQL($col).'`';
                                        }
                                    }
                                    $sql = 'INSERT INTO '._DB_PREFIX_.'product_attribute ('.join(',', $cols).') VALUES ('.(int) $id_product.','.trim($sqlinsert, ',').')';
                                    Db::getInstance()->Execute($sql);
                                    $newid = Db::getInstance()->Insert_ID();
                                    if ($newid)
                                    {
                                        $sql = 'SELECT GROUP_CONCAT(id_attribute) AS ids FROM '._DB_PREFIX_.'product_attribute_combination WHERE id_product_attribute='.(int) $row['id_product_attribute'];
                                        $res = Db::getInstance()->getValue($sql);
                                        $ids = explode(',', $res);
                                        if (count($ids))
                                        {
                                            $sql2 = 'INSERT INTO '._DB_PREFIX_.'product_attribute_combination (`id_attribute`,`id_product_attribute`) VALUES ('.join(','.(int) $newid.'),(', $ids).",'".(int) $newid."')";
                                            Db::getInstance()->Execute($sql2);
                                        }
                                    }
                                }
                            }
                        }
                    }
                    ## Champ Features copy/paste
                    if (isset($_POST['features']) && substr(Tools::getValue('features'), 0, 9) == 'features_')
                    {
                        $doHookUpdateQuantity = false;
                        // get features
                        $prefixlen = strlen('features_');
                        $id_productsource = substr(Tools::getValue('features'), $prefixlen, strlen(Tools::getValue('features')));
                        if ($id_productsource != $id_product)
                        {
                            $sql = 'SELECT * FROM '._DB_PREFIX_.'feature_product WHERE id_product='.(int) $id_productsource;
                            $source_features = Db::getInstance()->Executes($sql);
                            if (!empty($source_features))
                            {
                                Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'feature_product WHERE id_product='.(int) $id_product);
                                $sql_values = array();
                                foreach ($source_features as $row)
                                {
                                    $sql_values[] = '('.(int) $row['id_feature'].','.(int) $id_product.','.(int) $row['id_feature_value'].')';
                                }
                                $sql_values = implode(',', $sql_values);
                                Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'feature_product WHERE id_product='.(int) $id_product);
                                Db::getInstance()->execute('INSERT INTO '._DB_PREFIX_.'feature_product VALUES '.pSQL($sql_values));
                            }

                            $return_datas['refresh_multipleFeatures'] = '1';
                            $return_datas['doRefreshGridColMultipleFeatures'] = true;
                        }
                    }
                    ## Champ Categories copy/paste
                    if (isset($_POST['categories']) && substr(Tools::getValue('categories'), 0, 11) == 'categories_')
                    {
                        $doHookUpdateQuantity = false;
                        // get categories
                        $prefixlen = strlen('categories_');
                        $id_productsource = substr(Tools::getValue('categories'), $prefixlen, strlen(Tools::getValue('categories')));
                        if ($id_productsource != $id_product)
                        {
                            $sql = 'SELECT * FROM '._DB_PREFIX_.'category_product WHERE id_product='.(int) $id_productsource;
                            $source_categories = Db::getInstance()->executeS($sql);
                            if (!empty($source_categories))
                            {
                                $tmp_id_category = array();
                                foreach ($source_categories as $row)
                                {
                                    $tmp_id_category[] = $row['id_category'];
                                }
                                $sql = 'SELECT id_category, MAX(position+1) AS max_pos_plus FROM '._DB_PREFIX_.'category_product
                                        WHERE id_category IN ('.pSQL(implode(',', $tmp_id_category)).')
                                        GROUP BY id_category';
                                $categ_positions = Db::getInstance()->executeS($sql);
                                $cache_category_position_final = array();
                                foreach ($categ_positions as $row)
                                {
                                    $cache_category_position_final[$row['id_category']] = $row['max_pos_plus'];
                                }

                                $sql_values = array();
                                foreach ($source_categories as $row)
                                {
                                    $sql_values[] = '('.(int) $row['id_category'].','.(int) $id_product.','.(int) $cache_category_position_final[$row['id_category']].')';
                                }
                                $sql_values = implode(',', $sql_values);
                                Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'category_product WHERE id_product='.(int) $id_product);
                                Db::getInstance()->execute('INSERT INTO '._DB_PREFIX_.'category_product (`id_category`,`id_product`,`position`) VALUES '.pSQL($sql_values));

                                $id_category_default_source = null;
                                if (version_compare(_PS_VERSION_, '1.5.0.10', '>='))
                                {
                                    $ids_category_default_source = Db::getInstance()->executeS('SELECT id_shop,id_category_default
                                                                                            FROM '._DB_PREFIX_.'product_shop
                                                                                            WHERE id_product='.(int) $id_productsource);
                                    if (!empty($ids_category_default_source))
                                    {
                                        foreach ($ids_category_default_source as $row)
                                        {
                                            Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'product_shop
                                                                                SET id_category_default = '.(int) $row['id_category_default'].'
                                                                                WHERE id_product='.(int) $id_product.'
                                                                                AND id_shop = '.(int) $row['id_shop']);
                                        }
                                    }
                                }
                                if (empty($id_category_default_source))
                                {
                                    $id_category_default_source = Db::getInstance()->getValue('SELECT id_category_default
                                                                                                    FROM '._DB_PREFIX_.'product
                                                                                                    WHERE id_product='.(int) $id_productsource);
                                    if (!empty($id_category_default_source))
                                    {
                                        Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'product
                                                                            SET id_category_default = '.(int) $id_category_default_source.'
                                                                            WHERE id_product='.(int) $id_product);
                                    }
                                }

                                if (_s('APP_COMPAT_HOOK'))
                                {
                                    $product = new Product((int) $id_product);
                                    SCI::hookExec('updateProduct', array('id_product' => (int) $product->id, 'product' => $product));
                                }
                            }
                            $return_datas['refresh_cat'] = '1';
                            $return_datas['doRefreshGridColCategories'] = true;
                        }

                    }
                    if (isset($_POST['quantityupdate']) || isset($_POST['quantity']))
                    {
                        $quantity = (int) Tools::getValue('quantity');
                        $quantityUpdate = (int) Tools::getValue('quantityupdate', 0);

                        $old_rows = array();
                        if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                        {
                            if (SCMS)
                            {
                                foreach (SCI::getSelectedShopActionList(false, $id_product) as $id_shop)
                                {
                                    $old_rows[$id_shop] = array(
                                        'id_stock_available' => StockAvailable::getStockAvailableIdByProductId($id_product, null, $id_shop),
                                        'quantity' => StockAvailable::getQuantityAvailableByProduct($id_product, null, $id_shop),
                                    );
                                }
                            }
                            else
                            {
                                $old_rows[SCI::getConfigurationValue('PS_SHOP_DEFAULT')] = array(
                                    'id_stock_available' => StockAvailable::getStockAvailableIdByProductId($id_product, null, SCI::getSelectedShop()),
                                    'quantity' => StockAvailable::getQuantityAvailableByProduct($id_product, null, SCI::getSelectedShop()),
                                );
                            }
                        }
                        if ($quantityUpdate != 0)
                        {
                            $row = array();
                            if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                            {
                                $row['quantity'] = StockAvailable::getQuantityAvailableByProduct($id_product, null, SCI::getSelectedShop());
                            }
                            else
                            {
                                $row['quantity'] = Db::getInstance()->getRow('SELECT quantity FROM '._DB_PREFIX_.'product WHERE id_product='.(int) $id_product);
                            }

                            $return_datas['newQuantity'] = $row['quantity'] + $quantityUpdate;

                            if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                            {
                                foreach (SCI::getSelectedShopActionList(false, $id_product) as $id_shop)
                                {
                                    SCI::setQuantity($id_product, null, $return_datas['newQuantity'], $id_shop);
                                }
                            }
                        }
                        else
                        {
                            $return_datas['newQuantity'] = $quantity;
                            if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                            {
                                foreach (SCI::getSelectedShopActionList(false, $id_product) as $id_shop)
                                {
                                    SCI::setQuantity($id_product, null, $return_datas['newQuantity'], $id_shop);
                                }
                            }
                        }
                        $todo[] = '`quantity`='.(int) $return_datas['newQuantity'];

                        if (_s('CAT_ACTIVE_HOOK_UPDATE_QUANTITY') == '1' && version_compare(_PS_VERSION_, '1.5.0.0', '<'))
                        {
                            $doHookUpdateQuantity = true;
                        }
                        if (version_compare(_PS_VERSION_, '1.7.2.0', '>='))
                        {
                            foreach ($old_rows as $id_shop => $old_row)
                            {
                                $sign = 1;
                                if (empty($quantityUpdate))
                                {
                                    $quantityUpdate = $quantity - $old_row['quantity'];
                                }

                                if ($quantityUpdate < 0)
                                {
                                    $sign = -1;
                                    $quantityUpdate = $quantityUpdate * -1;
                                }

                                $stockMvt = new StockMvt();
                                $stockMvt->id_stock = (int) $old_row['id_stock_available'];
                                $stockMvt->id_stock_mvt_reason = SCI::getStockMvtEmployeeReasonId($sign);
                                $stockMvt->id_employee = (int) $sc_agent->id_employee;
                                $stockMvt->employee_lastname = $sc_agent->lastname;
                                $stockMvt->employee_firstname = $sc_agent->firstname;
                                $stockMvt->physical_quantity = (int) $quantityUpdate;
                                $stockMvt->date_add = date('Y-m-d H:i:s');
                                $stockMvt->sign = $sign;
                                $stockMvt->price_te = 0;
                                $stockMvt->last_wa = 0;
                                $stockMvt->current_wa = 0;
                                $stockMvt->add();
                            }
                        }

                        if (!empty($old_rows))
                        {
                            foreach ($old_rows as $old_id_shop => $old_values)
                            {
                                addToHistory('catalog_tree', 'modification', 'quantity', (int) $id_product, $id_lang, _DB_PREFIX_.'product', (int) $return_datas['newQuantity'], $old_values['quantity'], $old_id_shop);
                            }
                        }
                        else
                        {
                            addToHistory('catalog_tree', 'modification', 'quantity', (int) $id_product, $id_lang, _DB_PREFIX_.'product', (int) $return_datas['newQuantity']);
                        }
                    }
                    if (isset($_POST['discountprice']))
                    {
                        $sql = 'DELETE FROM '._DB_PREFIX_.'specific_price WHERE id_product='.(int) $id_product;
                        Db::getInstance()->Execute($sql);
                        $dpList = explode('_', Tools::getValue('discountprice'));
                        foreach ($dpList as $dp)
                        {
                            $val = explode('|', $dp);
                            if ((version_compare(_PS_VERSION_, '1.5.0.0', '<') && count($val) == 8) ||
                                    (version_compare(_PS_VERSION_, '1.5.0.0', '>=') && count($val) == 10))
                            {
                                if (strpos($val[1], '%') !== false)
                                {
                                    $type = 'percentage';
                                    $val[1] = floatval(trim($val[1], '%')) / 100;
                                }
                                else
                                {
                                    $type = 'amount';
                                }
                                if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                                {
                                    $sql = 'INSERT IGNORE INTO `'._DB_PREFIX_."specific_price` (`reduction_type`,`id_product`,`from_quantity`,`reduction`,`price`,`from`,`to`,`id_group`,`id_country`,`id_currency`,`id_customer`,`id_product_attribute`,`id_shop_group`,`id_shop`) VALUES ('".psql($type)."',".(int) $id_product.','.floatval($val[0]).','.floatval($val[1]).','.floatval($val[2]).",'".psql($val[3])."','".psql($val[4])."','".(int) ($val[5])."','".(int) ($val[6])."','".(int) ($val[7])."',0,0,'".(int) ($val[8])."','".(int) ($val[9])."')";
                                    Db::getInstance()->Execute($sql);
                                }
                                else
                                {
                                    $sql = 'INSERT IGNORE INTO `'._DB_PREFIX_."specific_price` (`reduction_type`,`id_product`,`from_quantity`,`reduction`,`price`,`from`,`to`,`id_group`,`id_country`,`id_currency`) VALUES ('".psql($type)."',".(int) $id_product.','.floatval($val[0]).','.floatval($val[1]).','.floatval($val[2]).",'".psql($val[3])."','".psql($val[4])."','".(int) ($val[5])."','".(int) ($val[6])."','".(int) ($val[7])."')";
                                    Db::getInstance()->Execute($sql);
                                }
                            }
                        }
                        addToHistory('specific_price', 'modification', 'value', (int) $id_product, $id_lang, _DB_PREFIX_.'specific_price', str_replace('_', "\n", Tools::getValue('discountprice')), '');
                    }
                    if (isset($_POST['cache_is_pack']))
                    {
                        $is_pack_up = (int) Tools::getValue('cache_is_pack');
                        if (!$is_pack_up)
                        {
                            Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'pack WHERE id_product_pack = '.(int) $id_product);
                        }
                    }

                    if (defined('SC_UkooProductCompat_ACTIVE') && SC_UkooProductCompat_ACTIVE == 1 && SCI::moduleIsInstalled('ukoocompat'))
                    {
                        include SC_DIR.'lib/cat/productcompatibility/cat_productcompatibility_update_queue.php';
                    }

                    foreach ($fields_lang as $field)
                    {
                        if (isset($_POST[$field]))
                        {
                            $value = psql(Tools::getValue($field), (sc_in_array($field, $fieldsWithHTML, 'catProductUpdateQueue_fieldsWithHTML') ? true : false));
                            if ($field == 'name' && _s('CAT_SEO_NAME_TO_URL'))
                            {
                                $todo_lang[] = "`link_rewrite`='".link_rewrite($value, Language::getIsoById($id_lang))."'";
                            }
                            $todo_lang[] = '`'.bqSQL($field)."`='".$value."'";
                            addToHistory('catalog_tree', 'modification', $field, (int) $id_product, $id_lang, _DB_PREFIX_.'product_lang', $value);
                        }
                    }

                    if (count($todo))
                    {
                        $todo[] = '`date_upd`=NOW()';
                        $sql = 'UPDATE '._DB_PREFIX_.'product SET '.join(' , ', $todo).' WHERE id_product='.(int) $id_product;
                        Db::getInstance()->Execute($sql);

                        if (!empty($doHookUpdateQuantity) && isset($return_datas['newQuantity']))
                        {
                            SCI::hookExec('actionUpdateQuantity',
                            array(
                            'id_product' => $id_product,
                            'id_product_attribute' => 0,
                            'quantity' => $return_datas['newQuantity'],
                            )
                            );
                        }
                    }
                    if (count($todo_lang))
                    {
                        Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'product SET date_upd=NOW(),indexed=0 WHERE id_product='.(int) $id_product);
                        if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                        {
                            Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'product_shop SET date_upd=NOW(),indexed=0 WHERE id_product='.(int) $id_product.' AND id_shop='.(int) SCI::getSelectedShop());
                        }

                        $sql2 = 'UPDATE '._DB_PREFIX_.'product_lang SET '.join(' , ', $todo_lang).' WHERE id_product='.(int) $id_product.' AND id_lang='.(int) $id_lang;
                        if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                        {
                            $sql2 .= ' AND id_shop IN ('.pInSQL(SCI::getSelectedShopActionList(true)).')';
                        }
                        if ($debug)
                        {
                            $dd .= $sql2."\n";
                        }
                        Db::getInstance()->Execute($sql2);
                        if (isset($_POST['name']))
                        {
                            $sql3 = 'UPDATE '._DB_PREFIX_."product_lang SET name='".pSQL(Tools::getValue('name'))."' WHERE id_product=".(int) $id_product." AND name='new'";
                            if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                            {
                                $sql3 .= ' AND id_shop IN ('.pInSQL(SCI::getSelectedShopActionList(true)).')';
                            }
                            Db::getInstance()->Execute($sql3);
                        }
                    }
                    if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                    {
                        if (isset($_POST['supplier_reference']))
                        {
                            $sql = 'SELECT id_supplier FROM '._DB_PREFIX_.'product WHERE id_product='.(int) $id_product;
                            $row = Db::getInstance()->getRow($sql);
                            $id_supplier = (int) $row['id_supplier'];
                            if ($id_supplier > 0)
                            {
                                $id_product_supplier = (int) ProductSupplier::getIdByProductAndSupplier((int) $id_product, 0, (int) $id_supplier);

                                if (!$id_product_supplier)
                                {
                                    //create new record
                                    $product_supplier_entity = new ProductSupplier();
                                    $product_supplier_entity->id_product = (int) $id_product;
                                    $product_supplier_entity->id_product_attribute = 0;
                                    $product_supplier_entity->id_supplier = (int) $id_supplier;
                                    $product_supplier_entity->product_supplier_reference = psql(Tools::getValue('supplier_reference'));
                                    $product_supplier_entity->product_supplier_price_te = 0;
                                    $product_supplier_entity->id_currency = 0;
                                    $product_supplier_entity->save();
                                }
                                else
                                {
                                    $product_supplier = new ProductSupplier((int) $id_product_supplier);
                                    $product_supplier->product_supplier_reference = psql(Tools::getValue('supplier_reference'));
                                    $product_supplier->update();
                                }
                            }
                        }
                        if (isset($_POST['wholesale_price']) && _s('CAT_PROD_WHOLESALEPRICE_SUPPLIER') == 1)
                        {
                            $sql = 'SELECT id_supplier FROM '._DB_PREFIX_.'product WHERE id_product='.(int) $id_product;
                            $id_supplier = (int) Db::getInstance()->getValue($sql);
                            if ($id_supplier > 0)
                            {
                                $id_product_supplier = (int) ProductSupplier::getIdByProductAndSupplier((int) $id_product, 0, (int) $id_supplier);

                                if (!$id_product_supplier)
                                {
                                    //create new record
                                    $product_supplier_entity = new ProductSupplier();
                                    $product_supplier_entity->id_product = (int) $id_product;
                                    $product_supplier_entity->id_product_attribute = 0;
                                    $product_supplier_entity->id_supplier = (int) $id_supplier;
                                    $product_supplier_entity->product_supplier_price_te = psql(Tools::getValue('supplier_reference'));
                                    $product_supplier_entity->id_currency = 0;
                                    $product_supplier_entity->save();
                                }
                                else
                                {
                                    $product_supplier_entity = new ProductSupplier((int) $id_product_supplier);
                                    $product_supplier_entity->product_supplier_price_te = psql(Tools::getValue('wholesale_price'));
                                    $product_supplier_entity->update();
                                }

                                if (version_compare(_PS_VERSION_, '1.5.0.10', '>='))
                                {
                                    $wholesale_price = $product_supplier_entity->product_supplier_price_te;
                                    $sql = 'UPDATE '._DB_PREFIX_.'product_shop
                                                SET wholesale_price = '.(float) $wholesale_price.'
                                                WHERE id_product = '.(int) $id_product;
                                    $sql .= (_s('CAT_PROD_WHOLESALEPRICE_SAVING_METHOD') == 1 ? ' AND id_shop IN ('.pInSQL(SCI::getSelectedShopActionList(true, $id_product)).')' : '');
                                    Db::getInstance()->execute($sql);
                                }
                            }
                        }
                        if (isset($_POST['id_supplier']))
                        {
                            $id_supplier = (int) Tools::getValue('id_supplier');
                            if ($id_supplier > 0)
                            {
                                $id_product_supplier = (int) ProductSupplier::getIdByProductAndSupplier((int) $id_product, 0, (int) $id_supplier);

                                if (!$id_product_supplier)
                                {
                                    //create new record
                                    $product_supplier_entity = new ProductSupplier();
                                    $product_supplier_entity->id_product = (int) $id_product;
                                    $product_supplier_entity->id_product_attribute = 0;
                                    $product_supplier_entity->id_supplier = (int) $id_supplier;
                                    $product_supplier_entity->product_supplier_reference = '';
                                    $product_supplier_entity->product_supplier_price_te = 0;
                                    $product_supplier_entity->id_currency = 0;
                                    $product_supplier_entity->save();
                                }
                                else
                                {
                                    $product_supplier = new ProductSupplier((int) $id_product_supplier);
                                    $sql = 'UPDATE '._DB_PREFIX_."product SET wholesale_price='".psql($product_supplier->product_supplier_price_te)."' WHERE id_product=" .(int) $id_product;
                                    Db::getInstance()->Execute($sql);
                                    $todoshop[] = " wholesale_price='".psql($product_supplier->product_supplier_price_te)."' ";
                                }

                                $combis = Product::getProductAttributesIds((int) $id_product);
                                if (!empty($combis))
                                {
                                    foreach ($combis as $combi)
                                    {
                                        if (empty($combi['id_product_attribute']))
                                        {
                                            continue;
                                        }
                                        $id_product_supplier = (int) ProductSupplier::getIdByProductAndSupplier((int) $id_product, (int) $combi['id_product_attribute'], (int) $id_supplier);

                                        if (!$id_product_supplier)
                                        {
                                            //create new record
                                            $product_supplier_entity = new ProductSupplier();
                                            $product_supplier_entity->id_product = (int) $id_product;
                                            $product_supplier_entity->id_product_attribute = (int) $combi['id_product_attribute'];
                                            $product_supplier_entity->id_supplier = (int) $id_supplier;
                                            $product_supplier_entity->product_supplier_reference = '';
                                            $product_supplier_entity->product_supplier_price_te = 0;
                                            $product_supplier_entity->id_currency = 0;
                                            $product_supplier_entity->save();
                                        }
                                    }
                                }
                            }
                        }
                        if (count($todoshop))
                        {
                            $sql = 'UPDATE '._DB_PREFIX_.'product_shop SET '.join(' , ', $todoshop).' WHERE id_product='.(int) $id_product.' AND id_shop IN ('.pInSQL(SCI::getSelectedShopActionList(true)).')';
                            Db::getInstance()->Execute($sql);
                        }
                    }
                    if (_s('APP_COMPAT_HOOK') && !_s('APP_COMPAT_EBAY'))
                    {
                        $product = new Product((int) $id_product);
                        SCI::hookExec('updateProduct', array('id_product' => (int) $product->id, 'product' => $product));
                        SCI::hookExec('actionObjectProductUpdateAfter', array('object' => $product));
                    }
                    elseif (_s('APP_COMPAT_EBAY'))
                    {
                        Configuration::updateValue('EBAY_SYNC_LAST_PRODUCT', min(Configuration::get('EBAY_SYNC_LAST_PRODUCT'), (int) $id_product));
                    }

                    /*
                     * FOR EXTENSIONS
                     */
                    if (defined('SC_FeedBiz_ACTIVE') && SC_FeedBiz_ACTIVE == 1 && SCI::moduleIsInstalled('feedbiz'))
                    {
                        $allowed_market_place = SCI::getFeedBizAllowedMarketPlace();
                        #product option
                        $extension_fields = array('fpo_force', 'fpo_disable', 'fpo_price', 'fpo_shipping', 'fpo_text');
                        #pour toutes les déclinaisons
                        $id_product_attribute = (int) 0;
                        $insert_field = array();
                        $insert_value = array();
                        $update_combo = array();
                        foreach ($extension_fields as $field)
                        {
                            if (isset($_POST[$field]))
                            {
                                $insert_field[] = '`'.bqSQL(str_replace('fpo_', '', $field)).'`';
                                $insert_value[] = '"'.pSQL(Tools::getValue($field)).'"';
                                $update_combo[] = '`'.bqSQL($field).'` = '.(!Tools::getValue($field) ? 'NULL' : '"'.pSQL(Tools::getValue($field)).'"');
                            }
                        }

                        $find = Db::getInstance()->getValue('SELECT COUNT(*) FROM '._DB_PREFIX_.'feedbiz_product_option WHERE id_product='.(int) $id_product.' AND id_product_attribute = '.(int) $id_product_attribute.' AND id_lang='.(int) $id_lang);
                        if (!empty($find) && !empty($update_combo))
                        {
                            $sql = 'UPDATE '._DB_PREFIX_.'feedbiz_product_option SET '.implode(', ', $update_combo).' WHERE id_product='.(int) $id_product.' AND id_product_attribute = '.(int) $id_product_attribute.' AND id_lang='.(int) $id_lang;
                            Db::getInstance()->Execute($sql);
                        }
                        elseif (!empty($insert_field) && !empty($insert_value))
                        {
                            $sql = 'INSERT INTO '._DB_PREFIX_.'feedbiz_product_option (`id_product`, `id_product_attribute`, `id_lang`,'.implode(',', $insert_field).')
                            VALUES ('.(int) $id_product.','.(int) $id_product_attribute.','.(int) $id_lang.', '.implode(', ', $insert_value).')';
                            Db::getInstance()->Execute($sql);
                        }
                        #amazon product option
                        if ($allowed_market_place['amazon'] == 1)
                        {
                            $extension_fields = array('fpao_force', 'fpao_disable', 'fpao_price', 'fpao_shipping', 'fpao_text', 'fpao_nopexport', 'fpao_noqexport', 'fpao_fba', 'fpao_fba_value', 'fpao_asin1', 'fpao_asin2', 'fpao_asin3', 'fpao_bullet_point1', 'fpao_bullet_point2', 'fpao_bullet_point3', 'fpao_bullet_point4', 'fpao_bullet_point5', 'fpao_shipping_type', 'fpao_gift_wrap', 'fpao_gift_message', 'fpao_browsenode', 'fpao_repricing_min', 'fpao_repricing_max', 'fpao_repricing_gap', 'fpao_shipping_group');
                            #pour toutes les déclinaisons
                            $id_product_attribute = (int) 0;
                            $insert_field = array();
                            $insert_value = array();
                            $update_combo = array();
                            foreach ($extension_fields as $field)
                            {
                                if (isset($_POST[$field]))
                                {
                                    $insert_field[] = '`'.bqSQL(str_replace('fpao_', '', $field)).'`';
                                    $insert_value[] = '"'.pSQL(Tools::getValue($field)).'"';
                                    $update_combo[] = '`'.bqSQL($field).'` = '.(!Tools::getValue($field) ? 'NULL' : '"'.pSQL(Tools::getValue($field)).'"');
                                }
                            }
                            $iso_fpao = Language::getIsoById($id_lang);
                            $find = Db::getInstance()->getValue('SELECT COUNT(*) FROM '._DB_PREFIX_.'feedbiz_amazon_options WHERE id_product='.(int) $id_product.' AND id_product_attribute = '.(int) $id_product_attribute." AND region='".pSQL($iso_fpao)."'");
                            if (!empty($find) && !empty($update_combo))
                            {
                                $sql = 'UPDATE '._DB_PREFIX_.'feedbiz_amazon_options SET '.implode(', ', $update_combo).' WHERE id_product='.(int) $id_product.' AND id_product_attribute = '.(int) $id_product_attribute." AND region='".pSQL($iso_fpao)."'";
                                Db::getInstance()->Execute($sql);
                            }
                            elseif (!empty($insert_field) && !empty($insert_value))
                            {
                                $sql = 'INSERT INTO '._DB_PREFIX_.'feedbiz_amazon_options (`id_product`, `id_product_attribute`, `region`,'.implode(',', $insert_field).')
                                VALUES ('.(int) $id_product.','.(int) $id_product_attribute.",'".pSQL($iso_fpao)."', ".implode(', ', $insert_value).')';
                                Db::getInstance()->Execute($sql);
                            }
                        }
                    }

                    //update date_upd
                    Db::getInstance()->execute('UPDATE '._DB_PREFIX_."product SET date_upd = '".pSQL(date('Y-m-d H:i:s'))."' WHERE id_product=".(int) $id_product);
                    if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                    {
                        Db::getInstance()->execute('UPDATE '._DB_PREFIX_."product_shop SET date_upd = '".pSQL(date('Y-m-d H:i:s'))."' WHERE id_product=".(int) $id_product.' AND id_shop IN ('.pInSQL(SCI::getSelectedShopActionList(true)).')');
                    }

                    sc_ext::readCustomGridsConfigXML('onAfterUpdateSQL');
                    $return_datas['id_specific_price'] = $id_specific_price;
                }
                elseif (!empty($action) && $action == 'position')
                {
                    $id_category = (int) Tools::getValue('id_category');
                    $todo = array();
                    $row = explode(';', Tools::getValue('positions'));
                    foreach ($row as $v)
                    {
                        if ($v != '')
                        {
                            $pos = explode(',', $v);
                            $todo[] = 'UPDATE '._DB_PREFIX_.'category_product SET position='.(int) $pos[1].' WHERE id_category='.(int) $id_category.' AND id_product='.(int) $pos[0];
                        }
                    }
                    foreach ($todo as $task)
                    {
                        Db::getInstance()->Execute($task);
                    }
                }

                $return_callback = '';

                foreach ($return_datas as $key => $val)
                {
                    if (!empty($key))
                    {
                        if (!empty($return_callback))
                        {
                            $return_callback .= ',';
                        }
                        $return_callback .= $key.":'".str_replace("'", "\'", $val)."'";
                    }
                }
                if (!empty($extraVars))
                {
                    if (!empty($return_callback))
                    {
                        $return_callback .= ',';
                    }
                    $return_callback .= $extraVars;
                }
                $return_callback = '{'.$return_callback.'}';
                $callbacks = str_replace('{data}', $return_callback, $callbacks);


                QueueLog::delete(($log_ids[$num]));
            }
        }

        // PM Cache
        if (!empty($updated_products))
        {
            if (_s('CAT_APPLY_ALL_CART_RULES'))
            {
                SpecificPriceRule::applyAllRules($updated_products);
            }
            ExtensionPMCM::clearFromIdsProduct($updated_products);
        }

        // RETURN
        $return = json_encode(array('callback' => $callbacks));
    }
}

echo $return;
