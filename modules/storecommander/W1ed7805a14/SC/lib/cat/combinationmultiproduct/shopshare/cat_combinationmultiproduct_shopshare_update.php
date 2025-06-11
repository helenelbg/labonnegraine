<?php

$idlist = Tools::getValue('idlist', '');
$idlist_arr = explode(',', $idlist);
$id_product_by_id_product_attribute = array();
foreach ($idlist_arr as $row)
{
    list($id_product, $id_product_attribute) = explode('_', $row);
    $list['id_product_attribute_list'][] = (int) $id_product_attribute;
    $list['id_product_list'][] = (int) $id_product;
    $id_product_by_id_product_attribute[$id_product_attribute] = $id_product;
}
$list['id_product_list'] = array_unique($list['id_product_list']);

$action = Tools::getValue('action', '');
$id_lang = Tools::getValue('id_lang', '0');
$shop_selection = Tools::getValue('id_shop', '0');
$value = Tools::getValue('value', '0');

$multiple = false;
if (count($idlist_arr) > 1)
{
    $multiple = true;
}

$ids = $list['id_product_attribute_list'];

if ($action != '' && !empty($shop_selection) && !empty($idlist))
{
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
                    $sql_in_shop = 'SELECT COUNT(id_product_attribute)
                        FROM '._DB_PREFIX_.'product_attribute_shop
                        WHERE id_product_attribute = '.(int) $id_product_attribute.'
                        AND id_shop = '.(int) $shop_selection;
                    $in_shop = Db::getInstance()->getValue($sql_in_shop);
                    if ($in_shop == 0)
                    {
                        $sql_ref_shop = 'SELECT id_shop
                        FROM '._DB_PREFIX_."product_attribute_shop
                        WHERE id_product_attribute = '".(int) $id_product_attribute."'
                        AND id_shop != ".(int) $shop_selection.'
                        AND id_shop > 0';
                        $ref_shop = Db::getInstance()->getValue($sql_ref_shop);
                        if (!empty($ref_shop))
                        {
                            $new = new Combination($id_product_attribute, null, (int) $ref_shop);
                            $new->id_shop_list = array($shop_selection);
                            $new->save();
                        }
                    }
                }
                elseif (empty($value))
                {
                    $sql = 'DELETE FROM `'._DB_PREFIX_.'product_attribute_shop`
                            WHERE id_product_attribute = '.(int) $id_product_attribute.'
                            AND id_shop = '.(int) $shop_selection;
                    Db::getInstance()->execute($sql);
                    StockAvailable::removeProductFromStockAvailable((int) $id_product_by_id_product_attribute[$id_product_attribute], (int) $id_product_attribute, $shop_selection);
                }
            }
            foreach ($list['id_product_list'] as $id_product)
            {
                SCI::qtySumStockAvailable($id_product, array($shop_selection));
            }
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
                        $sql_in_shop = 'SELECT pas.id_product_attribute,p.id_shop_default
                            FROM '._DB_PREFIX_.'product_attribute_shop pas
                            LEFT JOIN '._DB_PREFIX_.'product p ON p.id_product = pas.id_product
                            WHERE pas.id_product_attribute = '.(int) $id_product_attribute.'
                            AND pas.id_shop = '.(int) $id_shop;
                        $in_shop = Db::getInstance()->getRow($sql_in_shop);
                        if (empty($in_shop['id_product_attribute']))
                        {
                            $new = new Combination($id_product_attribute, null, $in_shop['id_shop_default']);
                            $new->id_shop_list = array($id_shop);
                            $new->save();
                        }
                    }
                    elseif (empty($value))
                    {
                        $sql = 'DELETE FROM `'._DB_PREFIX_.'product_attribute_shop`
                                WHERE id_product_attribute = '.(int) $id_product_attribute.'
                                AND  id_shop = '.(int) $id_shop;
                        Db::getInstance()->execute($sql);
                        StockAvailable::removeProductFromStockAvailable((int) $id_product_by_id_product_attribute[$id_product_attribute], (int) $id_product_attribute, $id_shop);
                    }
                }
            }
            foreach ($list['id_product_list'] as $id_product)
            {
                SCI::qtySumStockAvailable($id_product, $shops);
            }
            break;
    }

    if (!empty($list['id_product_list']))
    {
        ExtensionPMCM::clearFromIdsProduct($list['id_product_list']);
    }
}
