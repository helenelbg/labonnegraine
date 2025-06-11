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
            $field = Tools::getValue('field', '');
            foreach ($ids as $id)
            {
                if (isset($value))
                {
                    $combination = new Combination((int) $id);
                    $id_product = $combination->id_product;
                    $sql = '
                        SELECT *
                        FROM `'._DB_PREFIX_.'product_supplier` ps
                        WHERE ps.`id_supplier` = "'.(int) $id_supplier.'"
                        AND ps.`id_product_attribute` = "'.(int) $id.'"';
                    $check_in_supplier = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
                    if (empty($check_in_supplier[0]['id_product_supplier']))
                    {
                        $new = new ProductSupplier();
                        $new->id_product = (int) $id_product;
                        $new->id_supplier = (int) $id_supplier;
                        $new->id_product_attribute = (int) $id;
                        $new->$field = $value;
                        $new->save();
                    }
                    else
                    {
                        $new = new ProductSupplier($check_in_supplier[0]['id_product_supplier']);
                        $new->$field = $value;
                        $new->save();
                    }

                    $product = new Product((int) $id_product, false, (int) $id_lang, (int) SCI::getSelectedShop());
                    $combi = new Combination((int) $id);
                    // Si le champs modifié est la reference
                    // et que fournisseur par défaut
                    // on remplace la valeur par défaut
                    // par la nouvelle référence
                    if ($field == 'product_supplier_reference' && !empty($product->id_supplier) && $product->id_supplier == $id_supplier && _s('CAT_PROD_REFERENCE_SUPPLIER') == 1)
                    {
                        $combi->supplier_reference = $new->product_supplier_reference;
                        $combi->save();
                    }
                    if ($field == 'product_supplier_price_te' && !empty($product->id_supplier) && $product->id_supplier == $id_supplier && _s('CAT_PROD_WHOLESALEPRICE_SUPPLIER') == 1)
                    {
                        $combi->wholesale_price = $new->product_supplier_price_te;
                        $combi->save();
                    }

                    if (version_compare(_PS_VERSION_, '1.5.0.10', '>=') && !empty($product->id_supplier) && $product->id_supplier == $id_supplier && _s('CAT_PROD_WHOLESALEPRICE_SUPPLIER') == 1)
                    {
                        $wholesale_price = $new->product_supplier_price_te;
                        $sql = 'UPDATE '._DB_PREFIX_.'product_attribute_shop 
                                SET wholesale_price = '.(float) $wholesale_price.' 
                                WHERE id_product_attribute = '.(int) $id;
                        $sql .= (_s('CAT_PRODPROP_SUPPLIER_WHOLESALEPRICE_SAVING_METHOD') == 1 ? ' AND id_shop IN ('.pInSQL(SCI::getSelectedShopActionList(true, $id_product)).')' : '');
                        Db::getInstance()->execute($sql);
                    }
                }
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
                if ($value == '1')
                {
                    $combination = new Combination((int) $id);
                    $id_product = $combination->id_product;
                    $sql = '
                        SELECT *
                        FROM `'._DB_PREFIX_.'product_supplier` ps
                        WHERE ps.`id_supplier` = "'.(int) $id_supplier.'"
                        AND ps.`id_product_attribute` = "'.(int) $id.'"';
                    $check_in_supplier = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
                    if (empty($check_in_supplier[0]['id_product_supplier']))
                    {
                        $new = new ProductSupplier();
                        $new->id_product = (int) $id_product;
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
                    if ($value == '1')
                    {
                        $combination = new Combination((int) $id);
                        $id_product = $combination->id_product;
                        $sql = '
                            SELECT *
                            FROM `'._DB_PREFIX_.'product_supplier` ps
                            WHERE ps.`id_supplier` = "'.(int) $id_supplier.'"
                            AND ps.`id_product_attribute` = "'.(int) $id.'"';
                        $check_in_supplier = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
                        if (empty($check_in_supplier[0]['id_product_supplier']))
                        {
                            $new = new ProductSupplier();
                            $new->id_product = (int) $id_product;
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
        break;
    }

    if (!empty($ids[0]))
    {
        $id_product = SCI::getIdPdtFromCombi((int) $ids[0]);
    }
    if (!empty($id_product))
    {
        ExtensionPMCM::clearFromIdsProduct($id_product);
    }
}
