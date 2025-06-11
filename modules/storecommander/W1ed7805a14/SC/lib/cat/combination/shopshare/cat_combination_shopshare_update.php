<?php

$idlist = Tools::getValue('idlist', '');
$action = Tools::getValue('action', '');
$id_lang = Tools::getValue('id_lang', '0');
$shop_selection = Tools::getValue('id_shop', '0');
$value = Tools::getValue('value', '0');
$id_product = (int) Tools::getValue('id_product');

$multiple = false;
if (strpos($idlist, ',') !== false)
{
    $multiple = true;
}

$ids = explode(',', $idlist);

if ($action != '' && !empty($shop_selection) && !empty($idlist) && !empty($id_product))
{
    $product = new Product($id_product);

    switch ($action) {
        // Modification de present pour le shop passé en params
        // pour une ou plusieurs déclinaisons passées en params
        case 'present':
            if ($value == 'true')
            {
                $value = 1;
            }
            else
            {
                $value = 0;
            }

            foreach ($ids as $id_product_attribute)
            {
                if ($value == '1')
                {
                    $sql_in_shop = 'SELECT id_product_attribute
                        FROM '._DB_PREFIX_."product_attribute_shop
                        WHERE id_product_attribute = '".(int) $id_product_attribute."'
                            AND  id_shop = '".(int) $shop_selection."'";
                    $in_shop = Db::getInstance()->getValue($sql_in_shop);
                    if (empty($in_shop))
                    {
                        $sql_ref_shop = 'SELECT id_shop
                        FROM '._DB_PREFIX_."product_attribute_shop
                        WHERE id_product_attribute = '".(int) $id_product_attribute."'
                            AND  id_shop != '".(int) $shop_selection."'
                            AND id_shop > 0";
                        $ref_shop = Db::getInstance()->getValue($sql_ref_shop);
                        if (!empty($ref_shop))
                        {
                            $combination = new Combination($id_product_attribute, null, (int) $ref_shop);
                            $combination->id_shop_list = array($shop_selection);
                            $combination->save();
                        }
                    }
                }
                elseif (empty($value))
                {
                    $sql = 'DELETE FROM `'._DB_PREFIX_.'product_attribute_shop`
                    WHERE id_product_attribute = '.(int) $id_product_attribute.' AND  id_shop = '.(int) $shop_selection;
                    Db::getInstance()->execute($sql);
                    StockAvailable::removeProductFromStockAvailable((int) $product->id, (int) $id_product_attribute, $shop_selection);
                }
            }
            SCI::qtySumStockAvailable($product->id, array($shop_selection));
            break;
        // Modification de present
        // pour un ou plusieurs shops passés en params
        // pour un ou plusieurs products passés en params
        case 'mass_present':
            if ($value == 'true')
            {
                $value = 1;
            }
            else
            {
                $value = 0;
            }

            $shops = explode(',', $shop_selection);
            foreach ($shops as $id_shop)
            {
                foreach ($ids as $id_product_attribute)
                {
                    if ($value == '1')
                    {
                        $sql_in_shop = 'SELECT id_product_attribute
                                        FROM '._DB_PREFIX_.'product_attribute_shop
                                        WHERE id_product_attribute = '.(int) $id_product_attribute.'
                                        AND  id_shop = '.(int) $id_shop;
                        $in_shop = Db::getInstance()->getValue($sql_in_shop);
                        if (empty($in_shop))
                        {
                            $new = new Combination($id_product_attribute, null, $product->id_shop_default);
                            $new->id_shop_list = array($id_shop);
                            $new->save();
                        }
                    }
                    elseif (empty($value))
                    {
                        $sql = 'DELETE FROM `'._DB_PREFIX_.'product_attribute_shop`
                                WHERE id_product_attribute = '.(int) $id_product_attribute.' AND  id_shop = '.(int) $id_shop;
                        Db::getInstance()->execute($sql);
                        StockAvailable::removeProductFromStockAvailable((int) $product->id, (int) $id_product_attribute, $id_shop);
                    }
                }
            }
            SCI::qtySumStockAvailable($product->id, $shops);
            break;
    }

    ExtensionPMCM::clearFromIdsProduct($id_product);
}
