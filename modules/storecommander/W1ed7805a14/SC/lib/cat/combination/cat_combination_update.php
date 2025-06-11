<?php

$debug = false;

    $gr_id = Tools::getValue('gr_id');
    $id_lang = Tools::getValue('id_lang', '0');
    $action = Tools::getValue('action', '');
    $callback = Tools::getValue('callback', '');
    $error = '';
    $return = '';
    $id_product = (int) Tools::getValue('id_product');
    $quantity = Tools::getValue('quantity', _s('CAT_PROD_COMBI_CREA_QTY'));
    $selected_shop_id = SCI::getSelectedShop();
    $checked_shop_list = SCI::getSelectedShopActionList(false, $id_product);

    if (substr($gr_id, 0, 3) == 'NEW' && $action == 'insert')
    {
        $newId = 0;
        if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
        {
            $product = new Product($id_product, false, (int) $id_lang, (int) $selected_shop_id);
        }
        else
        {
            $product = new Product($id_product);
        }
        if (Validate::isLoadedObject($product))
        {
            $has_small_num = false;

            $price = max(floatval(Tools::getValue('price', 0)) - floatval(Tools::getValue('pprice', 0)), 0);
            $price = number_format($price, 6, '.', '');

            $weight = max(floatval(Tools::getValue('weight', 0)) - floatval(Tools::getValue('pweight', 0)), 0);
            $weight = number_format($weight, 6, '.', '');

            ## détermine si cette combi doit être par défaut.
            if (version_compare(_PS_VERSION_, '1.6.1.0', '>='))
            {
                $default = (int) Db::getInstance()->getValue('SELECT IF(COUNT(id_product_attribute)>0, 0, 1)
                                                                FROM '._DB_PREFIX_.'product_attribute_shop
                                                                WHERE id_product = '.(int) $product->id);
            }
            else
            {
                $default = (int) Db::getInstance()->getValue('SELECT IF(COUNT(pas.id_product_attribute)>0, 0, 1)
                                                                FROM '._DB_PREFIX_.'product_attribute pa
                                                                LEFT JOIN '._DB_PREFIX_.'product_attribute_shop pas ON (pas.id_product_attribute = pa.id_product_attribute)
                                                                WHERE pa.id_product = '.(int) $product->id);
            }

            if (version_compare(_PS_VERSION_, '1.7.0.0', '>='))
            {
                Context::getContext()->shop = new Shop($selected_shop_id);
                Shop::setContext(Shop::CONTEXT_SHOP, $selected_shop_id);
                $combination = new Combination(null, null, $selected_shop_id);
                $combination->id_product = (int) $product->id;
                $combination->price = $price;
                $combination->weight = $weight;
                $combination->default_on = $default;
                $combination->minimal_quantity = 1;
                $combination->id_shop_list = $checked_shop_list;
                $res = $combination->add();
                $id_product_attribute = $combination->id;

                foreach ($checked_shop_list as $checked_shop_id)
                {
                    SCI::setQuantity($product->id, $id_product_attribute, $quantity, (int) $checked_shop_id);
                }
            }
            elseif (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
            {
                $id_product_attribute = $product->addAttribute(
                    $price,
                    $weight,
                    0,
                    0,
                    0,
                    Tools::getValue('reference'),
                    Tools::getValue('ean13'),
                    $default,
                    Tools::getValue('location'),
                    Tools::getValue('upc'),
                    1,
                    SCI::getShopsByProduct($product->id)
                    );

                SCI::setQuantity($product->id, $id_product_attribute, $quantity, SCI::getShopsByProduct($product->id));

                $combination = new Combination((int) $id_product_attribute);
                $combination->id_product = $product->id;
                $combination->minimal_quantity = max(1, (int) $combination->minimal_quantity);
                $combination->id_shop_list = SCI::getShopsByProduct($product->id);
                $combination->save();

                if (SCAS && $product->advanced_stock_management == '1')
                {
                    $row = Db::getInstance()->getRow('SELECT pa.id_product_attribute
                        FROM `'._DB_PREFIX_.'product_attribute` pa
                        INNER JOIN '._DB_PREFIX_.'product_attribute_shop pas
                        ON (pas.id_product_attribute = pa.id_product_attribute AND pas.id_shop = '.(int) $selected_shop_id.')
                        WHERE pas.`default_on` = 1
                        AND pa.`id_product` = '.(int) $product->id);
                    if (!empty($row['id_product_attribute']) && ($row['id_product_attribute'] != $id_product_attribute))
                    {
                        $sql = 'SELECT DISTINCT(id_warehouse) as id_warehouse
                                FROM `'._DB_PREFIX_.'warehouse_product_location`
                                WHERE id_product_attribute = "'.(int) $row['id_product_attribute'].'"';
                    }
                    else
                    {
                        $sql = 'SELECT DISTINCT(id_warehouse) as id_warehouse
                                FROM `'._DB_PREFIX_.'warehouse_product_location`
                                WHERE id_product = "'.(int) $product->id.'"
                                    AND id_product_attribute="0"';
                    }
                    $warehouses = Db::getInstance()->executeS($sql);
                    $inserted_row = false;
                    if (!empty($warehouses) && count($warehouses) > 0)
                    {
                        foreach ($warehouses as $warehouse)
                        {
                            if (!empty($warehouse['id_warehouse']))
                            {
                                $inserted_row[] = Db::getInstance()->execute('INSERT INTO '._DB_PREFIX_.'warehouse_product_location (id_product, id_product_attribute, id_warehouse)
                                VALUES ("'.(int) $product->id.'","'.(int) $id_product_attribute.'","'.(int) $warehouse['id_warehouse'].'")');
                            }
                        }
                    }

                    if (!empty($inserted_row))
                    {
                        Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'warehouse_product_location WHERE id_product = "'.(int) $product->id.'" AND id_product_attribute="0"');
                    }
                }
                $sql = array();
                Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'product_attribute SET `date_upd`=NOW() WHERE id_product_attribute='.(int) $id_product_attribute);
            }
            else
            {
                $id_product_attribute = $product->addProductAttribute(
                            number_format($price, 6, '.', ''),
                    number_format($weight, 6, '.', ''),
                            0,
                            0,
                            $quantity,
                            '',
                            Tools::getValue('reference'),
                            Tools::getValue('supplier_reference'),
                            Tools::getValue('ean13'),
                            0,
                            Tools::getValue('location'));

                if ($has_small_num && (!empty($small_price) || !empty($small_weight)))
                {
                    $set = '';

                    if (!empty($small_price))
                    {
                        $set .= ", price='".pSQL(number_format($small_price, 6, '.', ''))."'";
                    }

                    if (!empty($small_weight))
                    {
                        $set .= ", weight='".pSQL(number_format($small_weight, 6, '.', ''))."'";
                    }

                    if (!empty($set))
                    {
                        $sql = 'UPDATE '._DB_PREFIX_.'product_attribute SET `date_upd`=NOW() '.$set.' WHERE id_product_attribute='.(int) $id_product_attribute;
                        Db::getInstance()->Execute($sql);
                    }
                }
            }

            if (_s('CAT_APPLY_ALL_CART_RULES'))
            {
                SpecificPriceRule::applyAllRules(array((int) $product->id));
            }
            $product->checkDefaultAttributes();

            ExtensionPMCM::clearFromIdsProduct($id_product);

            $newId = $id_product_attribute;

            // RETURN
            if (!empty($newId))
            {
                $callback = str_replace('{newid}', $newId, $callback);
                $return = json_encode(array('callback' => $callback));
            }
        }
        else
        {
            $error = 'Product not found';
        }
    }

    sc_ext::readCustomCombinationsGridConfigXML('extraVars');

if (empty($return))
{
    $error = 'ERROR: Try again';
}

if (!empty($error))
{
    $return = $error;
}

echo $return;
