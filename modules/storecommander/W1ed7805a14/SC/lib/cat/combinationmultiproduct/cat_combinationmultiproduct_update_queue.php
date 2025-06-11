<?php

@error_reporting(E_ERROR | E_PARSE);
@ini_set('display_errors', 'ON');

$id_lang = Tools::getValue('id_lang', '0');
$action = Tools::getValue('action', '');

$return = 'ERROR: Try again later';

// FUNCTIONS
$return_datas = array();
$updated_products = array();

// Récupération de toutes les modifications à effectuer
if (Tools::getValue('rows') || $action == 'insert')
{
    if ($action != 'insert')
    {
        if (_PS_MAGIC_QUOTES_GPC_)
        {
            $_POST['rows'] = Tools::getValue('rows');
        }
        $rows = json_decode($_POST['rows']);
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
                $id = $row->row;
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

                if (!empty($action) && $action == 'delete' && !empty($gr_id))
                {
                    list($id_product, $id_product_attribute) = explode('_', $id);
                    if (!empty($id_product))
                    {
                        $updated_products[$id_product] = $id_product;
                        Db::getInstance()->execute('UPDATE '._DB_PREFIX_."product SET `date_upd`='".psql(date('Y-m-d H:i:s'))."' WHERE id_product=".(int) $id_product);
                        if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                        {
                            Db::getInstance()->execute('UPDATE '._DB_PREFIX_."product_shop SET `date_upd`='".psql(date('Y-m-d H:i:s'))."' WHERE id_product=".(int) $id_product.' AND id_shop IN ('.pInSQL(SCI::getSelectedShopActionList(true)).')');
                        }

                        if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                        {
                            $shop = (int) SCI::getSelectedShop();

                            $p = new Product($id_product, false, (int) $id_lang, (int) SCI::getSelectedShop());
                            if (is_numeric($id_product_attribute) && $id_product_attribute)
                            {
                                $c = new Combination($id_product_attribute);
                                $c->id_shop_list = Shop::getShops(false, null, true);
                                $c->delete();
                                StockAvailable::removeProductFromStockAvailable((int) $id_product, (int) $id_product_attribute, $shop);

                                $sql = 'SELECT * FROM '._DB_PREFIX_."stock WHERE id_product_attribute = '".(int) $id_product_attribute."' ";
                                $stocks = Db::getInstance()->ExecuteS($sql);
                                foreach ($stocks as $stock)
                                {
                                    $sql = 'DELETE FROM '._DB_PREFIX_."stock_mvt WHERE id_stock='".(int) $stock['id_stock']."'";
                                    Db::getInstance()->Execute($sql);
                                }
                                $sql = 'DELETE FROM '._DB_PREFIX_."stock WHERE id_product_attribute = '".(int) $id_product_attribute."' ";
                                Db::getInstance()->Execute($sql);
                                $sql = 'DELETE FROM '._DB_PREFIX_."warehouse_product_location WHERE id_product_attribute = '".(int) $id_product_attribute."' ";
                                Db::getInstance()->Execute($sql);
                            }

                            $p->checkDefaultAttributes();
                            if (!$p->hasAttributes())
                            {
                                if(version_compare(_PS_VERSION_, '1.7.8.0', '>='))
                                {
                                    $sql = 'UPDATE ' . _DB_PREFIX_ . 'product SET cache_default_attribute=0, product_type = "'.pSQL(PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductType::TYPE_STANDARD).'" WHERE id_product=' . (int)$id_product;
                                }
                                else
                                {
                                    $sql = 'UPDATE ' . _DB_PREFIX_ . 'product SET cache_default_attribute=0 WHERE id_product=' . (int)$id_product;
                                }
                                Db::getInstance()->Execute($sql);
                                if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) {
                                    $sql = 'UPDATE ' . _DB_PREFIX_ . "product_shop SET cache_default_attribute='0' WHERE id_product='" . (int)$id_product . "' AND id_shop IN (" . SCI::getSelectedShopActionList(true) . ') ';
                                    Db::getInstance()->Execute($sql);
                                }
                            }
                            else
                            {
                                if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                                {
                                    $id_default_attribute = (int) Product::getDefaultAttribute($id_product);
                                    $shop = (int) SCI::getSelectedShop();

                                    $result = Db::getInstance()->update('product_shop', array(
                                            'cache_default_attribute' => $id_default_attribute,
                                    ), 'id_product = '.(int) $id_product.' AND id_shop = '.(int) $shop);

                                    $sql = 'UPDATE '._DB_PREFIX_."product_attribute_shop SET default_on='1' WHERE `id_product_attribute` = '".(int) $id_default_attribute."' AND id_shop = ".(int) $shop;
                                    Db::getInstance()->Execute($sql);

                                    $result &= Db::getInstance()->update('product', array(
                                            'cache_default_attribute' => $id_default_attribute,
                                    ), 'id_product = '.(int) $id_product);
                                }
                                else
                                {
                                    Product::updateDefaultAttribute((int) $id_product);
                                }
                            }

                            SCI::qtySumStockAvailable($id_product);
                        }
                        else
                        {
                            foreach ($idpa_array as $id_product_attribute)
                            {
                                if (is_numeric($id_product_attribute))
                                {
                                    $sql = 'DELETE FROM '._DB_PREFIX_.'product_attribute WHERE id_product_attribute='.(int) $id_product_attribute;
                                    Db::getInstance()->Execute($sql);
                                    $sql = 'DELETE FROM '._DB_PREFIX_.'product_attribute_combination WHERE id_product_attribute='.(int) $id_product_attribute;
                                    Db::getInstance()->Execute($sql);
                                    $sql = 'DELETE FROM `'._DB_PREFIX_.'cart_product` WHERE `id_product_attribute` = '.(int) $id_product_attribute;
                                    Db::getInstance()->Execute($sql);
                                    $sql = 'DELETE FROM '._DB_PREFIX_.'product_attribute_image WHERE id_product_attribute='.(int) $id_product_attribute;
                                    Db::getInstance()->Execute($sql);
                                    if (_s('APP_COMPAT_HOOK') && !_s('APP_COMPAT_EBAY'))
                                    {
                                        SCI::hookExec('deleteProductAttribute', array('id_product_attribute' => (int) $id_product_attribute, 'id_product' => (int) $id_product, 'deleteAllAttributes' => false));
                                    }
                                    elseif (_s('APP_COMPAT_EBAY'))
                                    {
                                        Configuration::updateValue('EBAY_SYNC_LAST_PRODUCT', min(Configuration::get('EBAY_SYNC_LAST_PRODUCT'), (int) $id_product));
                                    }
                                }
                            }

                            $default_id = checkDefaultAttributes((int) $id_product);

                            Db::getInstance()->Execute('
                                UPDATE `'._DB_PREFIX_.'product`
                                SET `cache_default_attribute` ='.(int) $default_id.'
                                WHERE `id_product` = '.(int) $id_product);
                            if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                            {
                                Db::getInstance()->Execute('
                                    UPDATE `'._DB_PREFIX_.'product_shop`
                                    SET `cache_default_attribute` ='.(int) $default_id.'
                                    WHERE `id_product` = '.(int) $id_product.' AND id_shop IN ('.SCI::getSelectedShopActionList(true, $id_product).')');
                            }

                            Db::getInstance()->Execute('
                                UPDATE `'._DB_PREFIX_.'product`
                                SET `quantity` =
                                    (
                                    SELECT SUM(`quantity`)
                                    FROM `'._DB_PREFIX_.'product_attribute`
                                    WHERE `id_product` = '.(int) $id_product.'
                                    )
                                WHERE `id_product` = '.(int) $id_product);
                        }
                    }
                }
                elseif (!empty($action) && $action == 'update' && !empty($gr_id))
                {
                    list($id_product, $id_product_attribute) = explode('_', $id);
                    if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                    {
                        $id_shop = (int) SCI::getSelectedShop();
                    }
                    if (!empty($id_product) && !empty($id_product_attribute))
                    {
                        $updated_products[$id_product] = $id_product;
                        $list_shop_fields = 'minimal_quantity,ecotax,available_date,unit_price_impact';
                        $todo_pa = array();
                        $ecotaxrate = SCI::getEcotaxTaxRate();

                        sc_ext::readCustomCombinationMultiProductGridConfigXML('onBeforeUpdateSQL');

                        // SHOP
                        $fields = explode(',', $list_shop_fields);
                        $todo = array();
                        foreach ($fields as $field)
                        {
                            if (isset($_POST[$field]) || isset($_POST[$field]))
                            {
                                $val = Tools::getValue($field);

                                if ($field == 'ecotax' && !empty($val))
                                {
                                    $val = $val / $ecotaxrate;
                                }

                                $todo[] = $field."='".psql(html_entity_decode($val))."'";
                            }
                        }

                        if (isset($_POST['wholesale_price']) && _s('CAT_PROD_WHOLESALEPRICE_SUPPLIER') == 1)
                        {
                            $sql = 'SELECT id_supplier FROM '._DB_PREFIX_.'product WHERE id_product='.(int) $id_product;
                            $row = Db::getInstance()->getRow($sql);
                            $id_supplier = (int) $row['id_supplier'];
                            $wholesalePriceValue = Tools::getValue('wholesale_price', 0);
                            if ($id_supplier > 0)
                            {
                                $id_product_supplier = (int) ProductSupplier::getIdByProductAndSupplier((int) $id_product, (int) $id_product_attribute, (int) $id_supplier);

                                if (!$id_product_supplier)
                                {
                                    //create new record
                                    $product_supplier_entity = new ProductSupplier();
                                    $product_supplier_entity->id_product = (int) $id_product;
                                    $product_supplier_entity->id_product_attribute = (int) $id_product_attribute;
                                    $product_supplier_entity->id_supplier = (int) $id_supplier;
                                    $product_supplier_entity->product_supplier_price_te = psql($wholesalePriceValue);
                                    $product_supplier_entity->id_currency = 0;
                                    $product_supplier_entity->save();
                                }
                                else
                                {
                                    $product_supplier_entity = new ProductSupplier((int) $id_product_supplier);
                                    $product_supplier_entity->product_supplier_price_te = psql($wholesalePriceValue);
                                    $product_supplier_entity->update();
                                }

                                if (version_compare(_PS_VERSION_, '1.5.0.10', '>='))
                                {
                                    $wholesale_price = $product_supplier_entity->product_supplier_price_te;
                                    $sql = 'UPDATE '._DB_PREFIX_.'product_attribute_shop
                                            SET wholesale_price = '.(float) $wholesale_price.'
                                            WHERE id_product_attribute = '.(int) $id_product_attribute;
                                    $sql .= (_s('CAT_PROD_WHOLESALEPRICE_SAVING_METHOD') == 1 ? ' AND id_shop IN ('.pInSQL(SCI::getSelectedShopActionList(true, $id_product)).')' : '');
                                    Db::getInstance()->execute($sql);
                                }
                            }
                        }

                        if (isset($_POST['priceextax']))
                        {
                            $todo[] = "`price`='".((floatval(Tools::getValue('priceextax')) - (floatval(Tools::getValue('ppriceextax')))))."'";
                        }

                        if (isset($_POST['weight']))
                        {
                            $product = new Product($id_product);

                            $todo[] = "`weight`='".((floatval(Tools::getValue('weight')) - (floatval($product->weight))))."'";
                        }

                        if (count($todo))
                        {
                            if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                            {
                                $sql = 'UPDATE '._DB_PREFIX_.'product_attribute_shop SET '.join(' , ', $todo).' WHERE id_product_attribute='.(int) $id_product_attribute.' AND id_shop='.(int) $id_shop;
                            }
                            else
                            {
                                $sql = 'UPDATE '._DB_PREFIX_.'product_attribute SET '.join(' , ', $todo).' WHERE id_product_attribute='.(int) $id_product_attribute;
                            }
                            Db::getInstance()->Execute($sql);
                        }

                        // REF
                        $todo = $todo_pa;
                        if (isset($_POST['reference']))
                        {
                            $val = Tools::getValue('reference');
                            $todo[] = "`reference`='".psql(html_entity_decode($val))."'";
                        }
                        if (isset($_POST['upc']))
                        {
                            $val = Tools::getValue('upc');
                            $todo[] = "`upc`='".psql(html_entity_decode($val))."'";
                        }
                        if (isset($_POST['ean13']))
                        {
                            $val = Tools::getValue('ean13');
                            $todo[] = "`ean13`='".psql(html_entity_decode($val))."'";
                        }
                        if (isset($_POST['mpn']))
                        {
                            $val = Tools::getValue('mpn');
                            $todo[] = "`mpn`='".psql(html_entity_decode($val))."'";
                        }
                        if (isset($_POST['supplier_reference']))
                        {
                            $val = Tools::getValue('supplier_reference');
                            $todo[] = "`supplier_reference`='".psql(html_entity_decode($val))."'";

                            $product = new Product($id_product);
                            if (!empty($product->id_supplier))
                            {
                                $sql_supplier = 'SELECT * FROM '._DB_PREFIX_.'product_supplier WHERE id_product='.(int) $id_product.' AND id_product_attribute='.(int) $id_product_attribute.' AND id_supplier='.(int) $product->id_supplier;
                                $actual_product_supplier = Db::getInstance()->getRow($sql_supplier);
                                if (!empty($actual_product_supplier['id_product_supplier']))
                                {
                                    $sql = 'UPDATE '._DB_PREFIX_."product_supplier SET `product_supplier_reference`='".psql(html_entity_decode($val))."' WHERE id_product_supplier=".(int) $actual_product_supplier['id_product_supplier'];
                                    Db::getInstance()->Execute($sql);
                                }
                                else
                                {
                                    $sql = 'INSERT INTO '._DB_PREFIX_.'product_supplier
                            (id_product, id_product_attribute, id_supplier, product_supplier_reference)
                            VALUES('.(int) $id_product.','.(int) $id_product_attribute.",'".$product->id_supplier."','".psql(html_entity_decode($val))."')";
                                    Db::getInstance()->Execute($sql);
                                }
                            }
                        }
                        if (isset($_POST['ecotax']))
                        {
                            $ecotax = Tools::getValue('ecotax', 0) / $ecotaxrate;
                            $todo[] = "`ecotax`='".psql(html_entity_decode($ecotax))."'";
                        }
                        if (isset($_POST['location']))
                        {
                            $location = Tools::getValue('location');
                            $todo[] = "`location`='".psql(($location))."'";
                        }
                        if (isset($_POST['sc_active']))
                        {
                            $sc_active = Tools::getValue('sc_active');
                            $todo[] = "`sc_active`='".(int) ($sc_active)."'";
                        }
                        if (count($todo))
                        {
                            $sql = 'UPDATE '._DB_PREFIX_.'product_attribute SET '.join(' , ', $todo).' WHERE id_product_attribute='.(int) $id_product_attribute;
                            Db::getInstance()->Execute($sql);
                        }

                        if (isset($_POST['quantity']))
                        {
                            if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                            {
                                $where = '';
                                if (SCMS)
                                {
                                    $id_shop_group = Shop::getGroupFromShop($id_shop, true);
                                    $shop_group = new ShopGroup($id_shop_group);
                                    if ($id_shop_group && $shop_group->share_stock == 1)
                                    {
                                        $where = " AND id_shop_group ='".(int) $id_shop_group."' ";
                                    }
                                    else
                                    {
                                        $where = " AND id_shop ='".(int) $id_shop."' ";
                                    }
                                    $old_value = Db::getInstance()->getValue('SELECT quantity FROM '._DB_PREFIX_."stock_available WHERE id_product='".(int) $id_product."' AND id_product_attribute='".(int) $id_product_attribute."' ".$where);
                                }
                                else
                                {
                                    $old_value = Db::getInstance()->getValue('SELECT * FROM '._DB_PREFIX_."stock_available WHERE id_product='".(int) $id_product."' AND id_product_attribute='".(int) $id_product_attribute."'");
                                }
                                SCI::setQuantity($id_product, $id_product_attribute, (int) Tools::getValue('quantity'), $id_shop);
                                if (version_compare(_PS_VERSION_, '8.0.0', '<'))
                                {
                                    addToHistory('cat_prop_attr_multiprod', 'modification', 'quantity', (int) $id_product_attribute, $id_lang, _DB_PREFIX_.'product_attribute', (int) Tools::getValue('quantity'), $old_value, $id_shop);
                                }
                            }
                            else
                            {
                                Db::getInstance()->Execute('
                                    UPDATE `'._DB_PREFIX_.'product`
                                    SET `quantity` =
                                        (
                                        SELECT SUM(`quantity`)
                                        FROM `'._DB_PREFIX_.'product_attribute`
                                        WHERE `id_product` = '.(int) $id_product.'
                                        )
                                    WHERE `id_product` = '.(int) $id_product);
                                addToHistory('cat_prop_attr_multiprod', 'modification', 'quantity', (int) $id_product_attribute, $id_lang, _DB_PREFIX_.'product_attribute', (int) Tools::getValue('quantity'));
                            }
                        }

                        sc_ext::readCustomCombinationMultiProductGridConfigXML('onAfterUpdateSQL');
                    }
                }

                QueueLog::delete(($log_ids[$num]));
            }
        }

        if (!empty($updated_products))
        {
            if (_s('CAT_APPLY_ALL_CART_RULES'))
            {
                SpecificPriceRule::applyAllRules($updated_products);
            }
            // PM Cache
            ExtensionPMCM::clearFromIdsProduct($updated_products);
        }

        // RETURN
        $return = json_encode(array('callback' => $callbacks));
    }
}
echo $return;
