<?php

    $id_lang = (int) Tools::getValue('id_lang');
    $mode = Tools::getValue('mode');
    $displayProductsFrom = Tools::getValue('displayProductsFrom', 'all');
    $id_categoryTarget = Tools::getValue('categoryTarget');
    $id_categorySource = Tools::getValue('categorySource');
    $droppedProducts = Tools::getValue('products');
    $products = explode(',', $droppedProducts);

    if (empty($id_categoryTarget) || empty($id_categorySource))
    {
        exit();
    }

    $sql = 'SELECT MAX(position) AS max FROM '._DB_PREFIX_.'category_product WHERE id_category='.(int) $id_categoryTarget;
    $res = Db::getInstance()->getRow($sql);
    $max = $res['max'];

    $sql = 'SELECT * FROM '._DB_PREFIX_.'category_product WHERE id_category='.(int) $id_categoryTarget;
    $res = Db::getInstance()->ExecuteS($sql);
    $plist = array();
    foreach ($res as $row)
    {
        $plist[] = $row['id_product'];
    }
    if ($mode == 'copy')
    {
        foreach ($products as $id_product)
        {
            if ($displayProductsFrom == 'default')
            {
                $sql = 'SELECT id_category_default FROM '._DB_PREFIX_.'product WHERE id_product='.(int) $id_product;
                $res = Db::getInstance()->getRow($sql);
                if ($res['id_category_default'] == $id_categorySource)
                {
                    Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'product SET date_upd=NOW(),id_category_default='.(int) $id_categoryTarget.' WHERE id_product='.(int) $id_product);
                    if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                    {
                        Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'product_shop SET date_upd=NOW(),id_category_default='.(int) $id_categoryTarget.' WHERE id_product='.(int) $id_product.' AND id_shop IN ('.SCI::getSelectedShopActionList(true).')');
                    }
                }
            }
            if (!sc_in_array($id_product, $plist, 'catDropproductoncategory_plist'))
            {
                ++$max;
                $sql = 'INSERT INTO '._DB_PREFIX_.'category_product (id_category,id_product,position) VALUES('.(int) $id_categoryTarget.','.(int) $id_product.','.(int) $max.')';
                Db::getInstance()->Execute($sql);
                $plist[] = $id_product;
                addToHistory('catalog_tree', 'relation_add', 'id_product', (int) $id_product, $id_lang, _DB_PREFIX_.'category_product', 'Parent added:'.(int) $id_categoryTarget);
            }
            if (SCMS)
            {
                $cat_shops = SCI::getShopsByCategory((int) $id_categoryTarget);
                $product_shops = SCI::getShopsByProduct((int) $id_product);
                // si le produit est lié à au moins un boutique
                // on vérifie s'il y a une intersection
                $intersec = array_intersect($cat_shops, $product_shops);
                if (!empty($intersec) && count($intersec) > 0)
                {
                }
                else
                {
                    $checked_shops = SCI::getSelectedShopActionList();
                    $intersec = array_intersect($cat_shops, $checked_shops);
                    if (!empty($intersec) && count($intersec) > 0)
                    {
                        $product = new Product($id_product);
                        $product->id_shop_list = $intersec;
                        $product->save();
                    }
                }
            }
        }
    }
    else
    {
        $id_categorySources = explode(',', $id_categorySource);
        foreach ($id_categorySources as $id_categorySource)
        {
            foreach ($products as $id_product)
            {
                $sql = 'SELECT id_category_default FROM '._DB_PREFIX_.'product WHERE id_product='.(int) $id_product;
                $res = Db::getInstance()->getRow($sql);
                if ($res['id_category_default'] == $id_categorySource)
                {
                    Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'product SET date_upd=NOW(),id_category_default='.(int) $id_categoryTarget.' WHERE id_product='.(int) $id_product);
                    if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                    {
                        Db::getInstance()->execute( 'UPDATE '._DB_PREFIX_.'product_shop SET date_upd=NOW(),id_category_default='.(int) $id_categoryTarget.' WHERE id_product='.(int) $id_product.' AND id_shop IN ('.SCI::getSelectedShopActionList(true).')');
                    }
                }
                if ((int) $id_categorySource != (int) $id_categoryTarget)
                {
                    $sql = 'DELETE FROM '._DB_PREFIX_.'category_product WHERE id_product='.(int) $id_product.' AND id_category='.(int) $id_categorySource;
                    Db::getInstance()->Execute($sql);
                }

                if (!sc_in_array($id_product, $plist, 'catDropproductoncategory_plist'))
                {
                    ++$max;
                    $sql = 'INSERT INTO '._DB_PREFIX_.'category_product (id_category,id_product,position) VALUES('.(int) $id_categoryTarget.','.(int) $id_product.','.(int) $max.')';
                    $plist[] = $id_product;
                    addToHistory('catalog_tree', 'relation_move', 'id_product', (int) $id_product, $id_lang, _DB_PREFIX_.'category_product', 'New parent:'.(int) $id_categoryTarget);
                    Db::getInstance()->Execute($sql);
                }
                if (SCMS)
                {
                    $cat_shops = SCI::getShopsByCategory((int) $id_categoryTarget);
                    $product_shops = SCI::getShopsByProduct((int) $id_product);
                    // si le produit est lié à au moins un boutique
                    // on vérifie s'il y a une intersection
                    $intersec = array_intersect($cat_shops, $product_shops);
                    if (!empty($intersec) && count($intersec) > 0)
                    {
                    }
                    else
                    {
                        $checked_shops = SCI::getSelectedShopActionList();
                        $intersec = array_intersect($cat_shops, $checked_shops);
                        if (!empty($intersec) && count($intersec) > 0)
                        {
                            $product = new Product($id_product);
                            $product->id_shop_list = $intersec;
                            $product->save();
                        }
                    }
                }
            }
        }
    }

    $eservices_id_project = Tools::getValue('eservices_id_project', '');
    if (!empty($eservices_id_project))
    {
        eServices_sendListItems($eservices_id_project);
    }

    if (_s('CAT_APPLY_ALL_CART_RULES'))
    {
        SpecificPriceRule::applyAllRules($products);
    }
