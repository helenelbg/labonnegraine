<?php

$idlist = Tools::getValue('idlist', '');
$action = Tools::getValue('action', '');
$id_lang = Tools::getValue('id_lang', '0');
$id_supplier = Tools::getValue('id_supplier', '0');
$value = Tools::getValue('value', '0');

$multiple = false;
if (strpos($idlist, ',') !== false)
{
    $multiple = true;
}

$ids = explode(',', $idlist);

if ($action != '' && !empty($id_supplier) && !empty($idlist))
{
    switch ($action) {
        case 'fields':
            $field = $field_from_payload = Tools::getValue('field', '');

            foreach ($ids as $id_product)
            {
                if (isset($value) && in_array($field, array('product_supplier_reference', 'product_supplier_price_te', 'id_currency')))
                {
                    $sql = '
                        SELECT *
                        FROM `'._DB_PREFIX_.'product_supplier` ps
                        WHERE ps.`id_supplier` = "'.(int) $id_supplier.'"
                        AND ps.`id_product` = "'.(int) $id_product.'"
                        AND ps.`id_product_attribute` = 0';
                    $check_in_supplier = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
                    if (empty($check_in_supplier[0]['id_product_supplier']))
                    {
                        $product_supplier = new ProductSupplier();
                        $product_supplier->id_product = (int) $id_product;
                        $product_supplier->id_supplier = (int) $id_supplier;
                        $product_supplier->id_product_attribute = 0;
                        $product_supplier->$field = $value;
                        $product_supplier->save();
                    }
                    else
                    {
                        $product_supplier = new ProductSupplier($check_in_supplier[0]['id_product_supplier']);
                        $product_supplier->$field = $value;
                        $product_supplier->save();
                    }

                    $product = new Product((int) $id_product, false, (int) $id_lang, (int) SCI::getSelectedShop());
                    // Si pas de fournisseur par défaut
                    if (empty($product->id_supplier))
                    {
                        // on le met en défaut
                        $product->id_supplier = (int) $id_supplier;
                        // Si ref non vide
                        if (!empty($product_supplier->product_supplier_reference))
                        {
                            $product->supplier_reference = $product_supplier->product_supplier_reference;
                        }
                        // Si ref par défaut non vide et que ref vide
                        elseif (!empty($product->supplier_reference) && empty($product_supplier->product_supplier_reference))
                        {
                            $product_supplier->product_supplier_reference = $product->supplier_reference;
                            $product_supplier->save();
                        }

                        // Si prix d'achat vide
                        if (empty($product->wholesale_price) && !empty($product_supplier->product_supplier_price_te))
                        {
                            $product->wholesale_price = $product_supplier->product_supplier_price_te;
                        }
                        // Si prix d'achat par défaut non vide et que prix d'achat vide
                        elseif (!empty($product->wholesale_price) && empty($product_supplier->product_supplier_price_te))
                        {
                            $product_supplier->product_supplier_price_te = $product->wholesale_price;
                            $product_supplier->save();
                        }
                        if (version_compare(_PS_VERSION_, '1.5.0.10', '>=') && _s('CAT_PROD_WHOLESALEPRICE_SUPPLIER') == 1)
                        {
                            $sql = 'UPDATE '._DB_PREFIX_.'product_shop
                                        SET wholesale_price = '.(float) $product_supplier->product_supplier_price_te.'
                                        WHERE id_product = '.(int) $id_product;
                            $sql .= (_s('CAT_PRODPROP_SUPPLIER_WHOLESALEPRICE_SAVING_METHOD') == 1 ? ' AND id_shop IN ('.pInSQL(SCI::getSelectedShopActionList(true, $id_product)).')' : '');
                            Db::getInstance()->execute($sql);
                        }
                        $product->save();
                    }
                    else
                    {
                        // Si le champs modifié est la reference
                        // et que fournisseur par défaut
                        // on remplace la valeur par défaut
                        // par la nouvelle référence
                        if ($field == 'product_supplier_reference' && $product->id_supplier == $id_supplier && _s('CAT_PROD_REFERENCE_SUPPLIER') == 1)
                        {
                            $product->supplier_reference = $product_supplier->product_supplier_reference;
                            $product->save();
                        }
                        // Si le champs modifié est le prix d'achat
                        // et que fournisseur par défaut
                        // on remplace la valeur par défaut
                        // par la nouvelle référence
                        if ($field == 'product_supplier_price_te' && $product->id_supplier == $id_supplier && _s('CAT_PROD_WHOLESALEPRICE_SUPPLIER') == 1)
                        {
                            $product->wholesale_price = $product_supplier->product_supplier_price_te;
                            $product->save();
                            if (version_compare(_PS_VERSION_, '1.5.0.10', '>='))
                            {
                                $sql = 'UPDATE '._DB_PREFIX_.'product_shop
                                        SET wholesale_price = '.(float) $product_supplier->product_supplier_price_te.'
                                        WHERE id_product = '.(int) $id_product;
                                $sql .= (_s('CAT_PRODPROP_SUPPLIER_WHOLESALEPRICE_SAVING_METHOD') == 1 ? ' AND id_shop IN ('.pInSQL(SCI::getSelectedShopActionList(true, $id_product)).')' : '');
                                Db::getInstance()->execute($sql);
                            }
                        }
                    }
                }
                sc_ext::readCustomPropSupplierGridConfigXML('onAfterUpdateSQL');
            }
        break;
        case 'present':
            if ($value == 'true')
            {
                $value = 1;
            }
            else
            {
                $value = 0;
            }

            foreach ($ids as $id)
            {
                $product = new Product((int) $id, false, (int) $id_lang, (int) SCI::getSelectedShop());
                if ($value == '1')
                {
                    $sql = '
                        SELECT *
                        FROM `'._DB_PREFIX_.'product_supplier` ps
                        WHERE ps.`id_supplier` = "'.(int) $id_supplier.'"
                        AND ps.`id_product` = "'.(int) $id.'"
                        AND ps.`id_product_attribute` = 0';
                    $check_in_supplier = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
                    if (empty($check_in_supplier[0]['id_product_supplier']))
                    {
                        $new = new ProductSupplier();
                        $new->id_product = (int) $id;
                        $new->id_supplier = (int) $id_supplier;
                        $new->id_product_attribute = 0;
                        $new->save();
                    }
                    else
                    {
                        $new = new ProductSupplier($check_in_supplier[0]['id_product_supplier']);
                        $new->save();
                    }

                    // Si pas de fournisseur par défaut
                    if (empty($product->id_supplier))
                    {
                        // on le met en défaut
                        $sql = 'UPDATE '._DB_PREFIX_."product
                                SET id_supplier='".(int) $id_supplier."'
                                WHERE id_product=" .(int) $product->id;
                        Db::getInstance()->Execute($sql);

                        // Si ref par défaut non vide et que ref vide
                        if (!empty($product->supplier_reference) && empty($new->product_supplier_reference))
                        {
                            $new->product_supplier_reference = $product->supplier_reference;
                            $new->save();
                        }
                        // Si prix d'achat par défaut non vide et que prix d'achat vide
                        if (!empty($product->wholesale_price) && empty($new->product_supplier_price_te))
                        {
                            $new->product_supplier_price_te = $product->wholesale_price;
                            $new->save();
                        }
                    }
                    else
                    {
                        // Si ce fournisseur est le fournisseur par défaut
                        // mais qu'il n'était pas présent
                        // on lui met la référence par défaut
                        if (!empty($product->supplier_reference) && empty($new->product_supplier_reference) && $product->id_supplier == $id_supplier)
                        {
                            $new->product_supplier_reference = $product->supplier_reference;
                            $new->save();
                        }
                        if (!empty($product->wholesale_price) && empty($new->product_supplier_price_te) && $product->id_supplier == $id_supplier)
                        {
                            $new->product_supplier_price_te = $product->wholesale_price;
                            $new->save();
                        }
                    }
                }
                elseif (empty($value))
                {
                    $sql = '
                        SELECT *
                        FROM `'._DB_PREFIX_.'product_supplier` ps
                        WHERE ps.`id_supplier` = "'.(int) $id_supplier.'"
                        AND ps.`id_product` = "'.(int) $id.'"';
                    $check_in_supplier = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
                    if (!empty($check_in_supplier[0]['id_product_supplier']))
                    {
                        $sql = 'DELETE FROM `'._DB_PREFIX_.'product_supplier`
                        WHERE `id_supplier` = "'.(int) $id_supplier.'"
                            AND `id_product` = "'.(int) $id.'"';
                        Db::getInstance()->execute($sql);
                    }

                    // Si fournisseur par défaut
                    if (!empty($product->id_supplier) && $product->id_supplier == $id_supplier)
                    {
                        $sql = 'UPDATE '._DB_PREFIX_."product
                                SET id_supplier='0', supplier_reference=''
                                WHERE id_product=" .(int) $product->id;
                        Db::getInstance()->Execute($sql);
                    }
                }

                $combinations = SCI::getAttributeCombinations($product, (int) $id_lang);
                if (!empty($combinations))
                {
                    foreach ($combinations as $combination)
                    {
                        $id = $combination['id_product_attribute'];
                        if ($value == '1')
                        {
                            $sql = '
                            SELECT *
                            FROM `'._DB_PREFIX_.'product_supplier` ps
                            WHERE ps.`id_supplier` = "'.(int) $id_supplier.'"
                            AND ps.`id_product_attribute` = "'.(int) $id.'"';
                            $check_in_supplier = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
                            if (empty($check_in_supplier[0]['id_product_supplier']))
                            {
                                $new = new ProductSupplier();
                                $new->id_product = (int) $product->id;
                                $new->id_supplier = (int) $id_supplier;
                                $new->id_product_attribute = (int) $id;
                                $new->save();
                            }
                        }
                        elseif (empty($value))
                        {
                            $sql = '
                            SELECT *
                            FROM `'._DB_PREFIX_.'product_supplier` ps
                            WHERE ps.`id_supplier` = "'.(int) $id_supplier.'"
                            AND ps.`id_product_attribute` = "'.(int) $id.'"';
                            $check_in_supplier = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
                            if (!empty($check_in_supplier[0]['id_product_supplier']))
                            {
                                $sql = 'DELETE FROM `'._DB_PREFIX_.'product_supplier`
                            WHERE `id_supplier` = "'.(int) $id_supplier.'"
                                AND `id_product_attribute` = "'.(int) $id.'"';
                                Db::getInstance()->execute($sql);
                            }
                        }
                    }
                }
            }
        break;
        case 'default':
            if ($value == 'true')
            {
                $value = 1;
            }
            else
            {
                $value = 0;
            }

            foreach ($ids as $id)
            {
                if ($value == '1')
                {
                    $sql = '
                        SELECT *
                        FROM `'._DB_PREFIX_.'product_supplier` ps
                        WHERE ps.`id_supplier` = "'.(int) $id_supplier.'"
                        AND ps.`id_product` = "'.(int) $id.'"
                        AND ps.`id_product_attribute` = 0';
                    $check_in_supplier = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
                    if (empty($check_in_supplier[0]['id_product_supplier']))
                    {
                        $new = new ProductSupplier();
                        $new->id_product = (int) $id;
                        $new->id_supplier = (int) $id_supplier;
                        $new->id_product_attribute = 0;
                        $new->save();
                    }
                    else
                    {
                        $new = new ProductSupplier((int) $check_in_supplier[0]['id_product_supplier']);
                    }

                    $sql = 'UPDATE '._DB_PREFIX_."product
                                SET id_supplier='".(int) $id_supplier."'
                                    ".(!empty($new->product_supplier_reference) ? " , supplier_reference='".pSQl($new->product_supplier_reference)."' " : '').'
                                    '.(!empty($new->product_supplier_price_te) ? " , wholesale_price='".pSQl($new->product_supplier_price_te)."' " : '')."
                                WHERE id_product=" .(int) $id;
                    Db::getInstance()->Execute($sql);

                    if (version_compare(_PS_VERSION_, '1.5.0.0', '>=') && !empty($new->product_supplier_price_te))
                    {
                        $sql = 'UPDATE '._DB_PREFIX_."product_shop
                                SET wholesale_price='".pSQl($new->product_supplier_price_te)."'
                                WHERE id_product=" .(int) $id . " AND id_shop='".(int) SCI::getSelectedShop()."' ";
                        Db::getInstance()->Execute($sql);
                    }

                    $combinations = Product::getProductAttributesIds((int) $id);
                    if (!empty($combinations))
                    {
                        foreach ($combinations as $combination)
                        {
                            if (empty($combination['id_product_attribute']))
                            {
                                continue;
                            }
                            $id_product_attr = $combination['id_product_attribute'];

                            $id_product_supplier = (int) ProductSupplier::getIdByProductAndSupplier((int) $id, (int) $id_product_attr, (int) $id_supplier);
                            if (empty($id_product_supplier))
                            {
                                $product_supplier_entity = new ProductSupplier();
                                $product_supplier_entity->id_product = (int) $id;
                                $product_supplier_entity->id_product_attribute = (int) $id_product_attr;
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
        break;
        case 'mass_present':
            if ($value == 'true')
            {
                $value = 1;
            }
            else
            {
                $value = 0;
            }

            $suppliers = explode(',', $id_supplier);
            foreach ($suppliers as $id_supplier)
            {
                foreach ($ids as $id)
                {
                    $product = new Product((int) $id, false, (int) $id_lang, (int) SCI::getSelectedShop());
                    if ($value == '1')
                    {
                        $sql = '
                            SELECT *
                            FROM `'._DB_PREFIX_.'product_supplier` ps
                            WHERE ps.`id_supplier` = "'.(int) $id_supplier.'"
                            AND ps.`id_product` = "'.(int) $id.'"
                            AND ps.`id_product_attribute` = 0';
                        $check_in_supplier = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
                        if (empty($check_in_supplier[0]['id_product_supplier']))
                        {
                            $new = new ProductSupplier();
                            $new->id_product = (int) $id;
                            $new->id_supplier = (int) $id_supplier;
                            $new->id_product_attribute = 0;
                            $new->save();
                        }
                        else
                        {
                            $new = new ProductSupplier($check_in_supplier[0]['id_product_supplier']);
                            $new->save();
                        }

                        // Si pas de fournisseur par défaut
                        if (empty($product->id_supplier))
                        {
                            // on le met en défaut
                            $sql = 'UPDATE '._DB_PREFIX_."product
                                SET id_supplier='".(int) $id_supplier."'
                                WHERE id_product=" .(int) $product->id;
                            Db::getInstance()->Execute($sql);

                            // Si ref par défaut non vide et que ref vide
                            if (!empty($product->supplier_reference) && empty($new->product_supplier_reference))
                            {
                                $new->product_supplier_reference = $product->supplier_reference;
                                $new->save();
                            }
                            // Si prix d'achat par défaut non vide et que prix d'achat vide
                            if (!empty($product->wholesale_price) && empty($new->product_supplier_price_te))
                            {
                                $new->product_supplier_price_te = $product->wholesale_price;
                                $new->save();
                            }
                        }
                        else
                        {
                            // Si ce fournisseur est le fournisseur par défaut
                            // mais qu'il n'était pas présent
                            // on lui met la référence par défaut
                            if (!empty($product->supplier_reference) && empty($new->product_supplier_reference) && $product->id_supplier == $id_supplier)
                            {
                                $new->product_supplier_reference = $product->supplier_reference;
                                $new->save();
                            }
                            if (!empty($product->wholesale_price) && empty($new->product_supplier_price_te) && $product->id_supplier == $id_supplier)
                            {
                                $new->product_supplier_price_te = $product->wholesale_price;
                                $new->save();
                            }
                        }
                    }
                    elseif (empty($value))
                    {
                        $sql = '
                            SELECT *
                            FROM `'._DB_PREFIX_.'product_supplier` ps
                            WHERE ps.`id_supplier` = "'.(int) $id_supplier.'"
                            AND ps.`id_product` = "'.(int) $id.'"';
                        $check_in_supplier = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
                        if (!empty($check_in_supplier[0]['id_product_supplier']))
                        {
                            $sql = 'DELETE FROM `'._DB_PREFIX_.'product_supplier`
                            WHERE `id_supplier` = "'.(int) $id_supplier.'"
                                AND `id_product` = "'.(int) $id.'"';
                            Db::getInstance()->execute($sql);
                        }

                        // Si fournisseur par défaut
                        if (!empty($product->id_supplier) && $product->id_supplier == $id_supplier)
                        {
                            $sql = 'UPDATE '._DB_PREFIX_."product
                                SET id_supplier='0', supplier_reference=''
                                WHERE id_product=" .(int) $product->id;
                            Db::getInstance()->Execute($sql);
                        }
                    }

                    $combinations = SCI::getAttributeCombinations($product, (int) $id_lang);
                    if (!empty($combinations))
                    {
                        foreach ($combinations as $combination)
                        {
                            $id = $combination['id_product_attribute'];
                            if ($value == '1')
                            {
                                $sql = '
                            SELECT *
                            FROM `'._DB_PREFIX_.'product_supplier` ps
                            WHERE ps.`id_supplier` = "'.(int) $id_supplier.'"
                            AND ps.`id_product_attribute` = "'.(int) $id.'"';
                                $check_in_supplier = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
                                if (empty($check_in_supplier[0]['id_product_supplier']))
                                {
                                    $new = new ProductSupplier();
                                    $new->id_product = (int) $product->id;
                                    $new->id_supplier = (int) $id_supplier;
                                    $new->id_product_attribute = (int) $id;
                                    $new->save();
                                }
                            }
                            elseif (empty($value))
                            {
                                $sql = '
                            SELECT *
                            FROM `'._DB_PREFIX_.'product_supplier` ps
                            WHERE ps.`id_supplier` = "'.(int) $id_supplier.'"
                            AND ps.`id_product_attribute` = "'.(int) $id.'"';
                                $check_in_supplier = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
                                if (!empty($check_in_supplier[0]['id_product_supplier']))
                                {
                                    $sql = 'DELETE FROM `'._DB_PREFIX_.'product_supplier`
                            WHERE `id_supplier` = "'.(int) $id_supplier.'"
                                AND `id_product_attribute` = "'.(int) $id.'"';
                                    Db::getInstance()->execute($sql);
                                }
                            }
                        }
                    }
                }
            }
        break;
    }

    if (!empty($idlist))
    {
        if (_s('CAT_APPLY_ALL_CART_RULES'))
        {
            SpecificPriceRule::applyAllRules($ids);
        }
        //update date_upd
        Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'product SET date_upd = NOW() WHERE id_product IN ('.pInSQL($idlist).')');
        if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
        {
            Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'product_shop SET date_upd = NOW() WHERE id_product IN ('.pInSQL($idlist).') AND id_shop IN ('.pInSQL(SCI::getSelectedShopActionList(true)).')');
        }
        // PM Cache
        ExtensionPMCM::clearFromIdsProduct($ids);
    }
}
