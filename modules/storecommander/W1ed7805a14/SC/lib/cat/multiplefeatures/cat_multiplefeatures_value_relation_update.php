<?php

$action = Tools::getValue('action', '0');
$id_feature = (int) Tools::getValue('id_feature', 0);
$id_feature_value = (int) Tools::getValue('id_feature_value', 0);
$value = Tools::getValue('value', '');
$product_list = Tools::getValue('product_list', '0');
$updated_products = explode(',', $product_list);

$hasPosition = false;
if (isField('position', 'feature_product'))
{
    $hasPosition = true;
}

    if ($action == 'update' && $id_feature > 0)
    {
        $position = Tools::getValue('position', '0');

        $sql = 'DELETE FROM '._DB_PREFIX_.'feature_product WHERE id_feature='.(int) $id_feature.' AND id_feature_value='.(int) $id_feature_value.' AND id_product IN ('.pInSQL($product_list).')';
        Db::getInstance()->Execute($sql);

        $sql = 'SELECT fv.id_feature_value
                        FROM `'._DB_PREFIX_.'feature_value` fv
                            INNER JOIN `'._DB_PREFIX_.'feature_product` fp ON (fv.id_feature_value=fp.id_feature_value)
                        WHERE fv.custom = 1
                            AND fv.id_feature = "'.(int) $id_feature.'"
                            AND fp.id_product IN ('.pInSQL($product_list).')';
        $to_deletes = Db::getInstance()->ExecuteS($sql);
        foreach ($to_deletes as $to_delete)
        {
            $sql = 'DELETE FROM '._DB_PREFIX_.'feature_value_lang WHERE id_feature_value = "'.(int) $to_delete['id_feature_value'].'"';
            Db::getInstance()->Execute($sql);

            $sql = 'DELETE FROM '._DB_PREFIX_.'feature_product WHERE id_feature_value = "'.(int) $to_delete['id_feature_value'].'"';
            Db::getInstance()->Execute($sql);

            $sql = 'DELETE FROM '._DB_PREFIX_.'feature_value WHERE id_feature_value = "'.(int) $to_delete['id_feature_value'].'"';
            Db::getInstance()->Execute($sql);
        }

        if ($value == 0)
        {
            foreach ($updated_products as $product)
            {
                addToHistory('cat_prop_multiplefeature', 'modification', 'feature_value', $product, (int) Configuration::get('PS_LANG_DEFAULT'), 'feature_product', null, (int) $id_feature_value, (int) SCI::getSelectedShop());
            }
        }

        $sqlstr = '';
        foreach ($updated_products as $idProduct)
        {
            if ($hasPosition)
            {
                if ($idProduct != 0)
                {
                    $sqlstr .= '('.(int) $id_feature.','.(int) $idProduct.','.(int) $id_feature_value.','.(int) $position.'),';
                }
            }
            else
            {
                if ($idProduct != 0)
                {
                    $sqlstr .= '('.(int) $id_feature.','.(int) $idProduct.','.(int) $id_feature_value.'),';
                }
            }
        }
        $sqlstr = trim($sqlstr, ',');
        if ($value == 1 && $sqlstr != '')
        {
            if ($hasPosition)
            {
                $sql = 'INSERT INTO `'._DB_PREFIX_.'feature_product` (id_feature,id_product,id_feature_value,position) VALUES '.psql($sqlstr);
            }
            else
            {
                $sql = 'INSERT INTO `'._DB_PREFIX_.'feature_product` (id_feature,id_product,id_feature_value) VALUES '.psql($sqlstr);
            }
            Db::getInstance()->Execute($sql);
            foreach ($updated_products as $idProduct)
            {
                addToHistory('cat_prop_multiplefeature', 'modification', 'feature_value', $idProduct, (int) Configuration::get('PS_LANG_DEFAULT'), 'feature_product', $id_feature_value, null, (int) SCI::getSelectedShop());
            }
        }

        foreach ($updated_products as $idProduct)
        {
            if (_s('APP_COMPAT_HOOK') && !_s('APP_COMPAT_EBAY'))
            {
                $product = new Product((int) $idProduct);
                SCI::hookExec('updateProduct', array('id_product' => (int) $product->id, 'product' => $product));
            }
        }
    }
    elseif ($action == 'add_custom' && $id_feature > 0)
    {
        $idProduct = (int) Tools::getValue('id_product', '0');
        $iso = (Tools::getValue('iso', '0'));
        $lang = 0;
        if (!empty($iso))
        {
            $lang = Language::getIdByIso($iso);
        }

        if (!empty($idProduct) && !empty($lang))
        {
            // DELETE
            $sql = 'SELECT fv.id_feature_value
                        FROM `'._DB_PREFIX_.'feature_value` fv
                            INNER JOIN `'._DB_PREFIX_.'feature_product` fp ON (fv.id_feature_value=fp.id_feature_value)
                        WHERE fv.custom = 1
                            AND fv.id_feature = "'.(int) $id_feature.'"
                            AND fp.id_product = "'.(int) $idProduct.'"';
            $to_deletes = Db::getInstance()->ExecuteS($sql);
            $id_feature_value = 0;
            foreach ($to_deletes as $to_delete)
            {
                $sql = 'DELETE FROM '._DB_PREFIX_.'feature_value_lang WHERE id_feature_value = "'.(int) $to_delete['id_feature_value'].'" AND id_lang="'.(int) $lang.'"';
                Db::getInstance()->Execute($sql);
                $id_feature_value = $to_delete['id_feature_value'];
            }

            $sql = 'DELETE FROM '._DB_PREFIX_.'feature_product WHERE id_feature='.(int) $id_feature." AND id_product = '".(int) $idProduct."'";
            if (!empty($id_feature_value))
            {
                $sql .= " AND id_feature_value != '".$id_feature_value."'";
            }
            Db::getInstance()->Execute($sql);

            // INSERT
            if (!empty($value))
            {
                if (empty($id_feature_value))
                {
                    $sql = 'INSERT INTO `'._DB_PREFIX_."feature_value` (id_feature,custom)
                            VALUES ('".(int) $id_feature."','1')";
                    Db::getInstance()->Execute($sql);
                    $id_feature_value = Db::getInstance()->Insert_ID();

                    $sql = 'INSERT INTO `'._DB_PREFIX_."feature_product` (id_feature,id_product,id_feature_value)
                            VALUES ('".(int) $id_feature."','".(int) $idProduct."','".(int) $id_feature_value."')";
                    Db::getInstance()->Execute($sql);
                    addToHistory('cat_prop_multiplefeature', 'modification', 'feature_value_custom', $idProduct, (int) Configuration::get('PS_LANG_DEFAULT'), 'feature_product', $id_feature_value, null, (int) SCI::getSelectedShop());
                }
                if (!empty($id_feature_value))
                {
                    $sql = 'INSERT INTO `'._DB_PREFIX_."feature_value_lang` (id_feature_value,id_lang,value)
                        VALUES ('".(int) $id_feature_value."','".(int) $lang."','".pSQL($value)."')";
                    Db::getInstance()->Execute($sql);
                }
            }
            else
            {
                $nb_empty_val = (int) Db::getInstance()->getValue('SELECT COUNT(*) FROM '._DB_PREFIX_.'feature_value_lang WHERE id_feature_value = '.(int) $id_feature_value.' AND `value` != "" AND `value` IS NOT NULL');
                if (empty($nb_empty_val))
                {
                    Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'feature_product WHERE id_feature_value='.(int) $id_feature_value.' AND id_product = '.(int) $idProduct);
                    Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'feature_value WHERE id_feature_value='.(int) $id_feature_value);
                }
                addToHistory('cat_prop_multiplefeature', 'modification', 'feature_value_custom', $idProduct, (int) Configuration::get('PS_LANG_DEFAULT'), 'feature_product', null, $id_feature_value, (int) SCI::getSelectedShop());
            }

            if (_s('APP_COMPAT_HOOK') && !_s('APP_COMPAT_EBAY'))
            {
                $product = new Product((int) $idProduct);
                SCI::hookExec('updateProduct', array('id_product' => (int) $product->id, 'product' => $product));
            }
        }
    }
    elseif ($action == 'position' && $id_feature > 0 && $hasPosition)
    {
        $positions = Tools::getValue('positions');
        if (!empty($positions) && count($positions) > 0 && !empty($updated_products) && count($updated_products) > 0)
        {
            foreach ($updated_products as $idProduct)
            {
                foreach ($positions as $position => $id_feature_value)
                {
                    if (!empty($id_feature_value))
                    {
                        $sql2 = '    SELECT id_feature_value
                        FROM '._DB_PREFIX_."feature_product
                        WHERE id_product = '".(int) $idProduct."'
                            AND id_feature_value = '".(int) $id_feature_value."'";
                        $exist = Db::getInstance()->ExecuteS($sql2);
                        if (!empty($exist[0]['id_feature_value']))
                        {
                            $sql = 'UPDATE `'._DB_PREFIX_."feature_product` SET position='".(int) $position."'
                            WHERE id_product = '".(int) $idProduct."'
                                AND id_feature_value = '".(int) $id_feature_value."'";
                            Db::getInstance()->Execute($sql);
                        }
                    }
                }

                if (_s('APP_COMPAT_HOOK') && !_s('APP_COMPAT_EBAY'))
                {
                    $product = new Product((int) $idProduct);
                    SCI::hookExec('updateProduct', array('id_product' => (int) $product->id, 'product' => $product));
                }
            }
        }
    }
    else
    {
        $id_feature_values = Tools::getValue('id_feature_values', null);
        if ($action == 'mass_used' && !empty($id_feature_values))
        {
            $sql = 'DELETE FROM '._DB_PREFIX_.'feature_product WHERE id_feature='.(int) $id_feature.' AND id_feature_value IN ('.pInSQL($id_feature_values).') AND id_product IN ('.pInSQL($product_list).')';
            Db::getInstance()->Execute($sql);

            $expl_id_feature_value = explode(',', $id_feature_values);
            if ($value == 0)
            {
                foreach ($updated_products as $product)
                {
                    foreach ($expl_id_feature_value as $id_fv)
                    {
                        addToHistory('cat_prop_multiplefeature', 'modification', 'feature_value', $product, (int) Configuration::get('PS_LANG_DEFAULT'), 'feature_product', null, $id_fv, (int) SCI::getSelectedShop());
                    }
                }
            }
            $sql = 'SELECT fv.id_feature_value
                            FROM `'._DB_PREFIX_.'feature_value` fv
                                INNER JOIN `'._DB_PREFIX_.'feature_product` fp ON (fv.id_feature_value=fp.id_feature_value)
                            WHERE fv.custom = 1
                                AND fv.id_feature = "'.(int) $id_feature.'"
                                AND fp.id_product IN ('.pInSQL($product_list).')';
            $to_deletes = Db::getInstance()->ExecuteS($sql);
            foreach ($to_deletes as $to_delete)
            {
                $sql = 'DELETE FROM '._DB_PREFIX_.'feature_value_lang WHERE id_feature_value = "'.(int) $to_delete['id_feature_value'].'"';
                Db::getInstance()->Execute($sql);

                $sql = 'DELETE FROM '._DB_PREFIX_.'feature_product WHERE id_feature_value = "'.(int) $to_delete['id_feature_value'].'"';
                Db::getInstance()->Execute($sql);

                $sql = 'DELETE FROM '._DB_PREFIX_.'feature_value WHERE id_feature_value = "'.(int) $to_delete['id_feature_value'].'"';
                Db::getInstance()->Execute($sql);
            }

            if ($value == 1)
            {
                foreach ($expl_id_feature_value as $id_feature_value)
                {
                    foreach ($updated_products as $idProduct)
                    {
                        $sql = 'INSERT INTO `'._DB_PREFIX_."feature_product` (id_feature,id_product,id_feature_value)
                            VALUES ('".(int) $id_feature."','".(int) $idProduct."','".(int) $id_feature_value."')";
                        Db::getInstance()->Execute($sql);
                        addToHistory('cat_prop_multiplefeature', 'modification', 'feature_value', $idProduct, (int) Configuration::get('PS_LANG_DEFAULT'), 'feature_product', (int) $id_feature_value, null, (int) SCI::getSelectedShop());

                        if (_s('APP_COMPAT_HOOK') && !_s('APP_COMPAT_EBAY'))
                        {
                            $product = new Product((int) $idProduct);
                            SCI::hookExec('updateProduct', array('id_product' => (int) $product->id, 'product' => $product));
                        }
                    }
                }
            }
        }
    }
    if (!empty($updated_products))
    {
        Db::getInstance()->execute('UPDATE '._DB_PREFIX_."product SET date_upd = '".pSQL(date('Y-m-d H:i:s'))."' WHERE id_product IN (".pInSQL($product_list).')');
        if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
        {
            Db::getInstance()->execute('UPDATE '._DB_PREFIX_."product_shop SET date_upd = '".pSQL(date('Y-m-d H:i:s'))."' WHERE id_product IN (".pInSQL($product_list).') AND id_shop IN ('.pInSQL(SCI::getSelectedShopActionList(true)).')');
        }

        if (_s('CAT_APPLY_ALL_CART_RULES'))
        {
            SpecificPriceRule::applyAllRules($updated_products);
        }
        // PM Cache
        ExtensionPMCM::clearFromIdsProduct($updated_products);
    }
