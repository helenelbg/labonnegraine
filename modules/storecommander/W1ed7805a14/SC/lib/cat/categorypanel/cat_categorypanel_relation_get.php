<?php

$idlist = Tools::getValue('idlist');
$id_lang = (int) Tools::getValue('id_lang');
$cntProducts = count(explode(',', $idlist));
$used = array();
$filter_used = array();

$sql = 'SELECT distinct cp.id_category FROM '._DB_PREFIX_.'category_product cp
            WHERE cp.id_product IN ('.pInSQL($idlist).')';
$res = Db::getInstance()->ExecuteS($sql);

foreach ($res as $row)
{
    $used[$row['id_category']] = $row['id_category'];
    $filter_used[$row['id_category']] = $row['id_category'];
}
$cdefault = '';
$not_all_products_present = array();

if ($cntProducts == 1)
{
    if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
    {
        if (SCMS)
        {
            $sql = 'SELECT p.id_category_default, p.id_shop FROM '._DB_PREFIX_.'product_shop p
                    WHERE p.id_product IN ('.pInSQL($idlist).')';

            $res = Db::getInstance()->executeS($sql);
            foreach ($res as $row)
            {
                if (empty($row['id_shop']))
                {
                    continue;
                }
                if (!empty($cdefault))
                {
                    $cdefault .= ',';
                }
                $cdefault .= $row['id_shop'].'_'.$row['id_category_default'];
            }
        }
        else
        {
            $default_shop = SCI::getSelectedShop();
            if (empty($default_shop))
            {
                $product = new Product($idlist);
                $default_shop = $product->id_shop_default;
            }
            $sql = 'SELECT p.id_category_default FROM '._DB_PREFIX_.'product_shop p
                    WHERE p.id_product IN ('.pInSQL($idlist).') AND id_shop='.(int) $default_shop;
            $res = Db::getInstance()->getRow($sql);
            $cdefault = $res['id_category_default'];
        }
    }
    else
    {
        $sql = 'SELECT p.id_category_default FROM '._DB_PREFIX_.'product p
                WHERE p.id_product IN ('.pInSQL($idlist).')';
        $res = Db::getInstance()->getRow($sql);
        $cdefault = $res['id_category_default'];
    }
}
else
{
    if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
    {
        if (SCMS)
        {
            $sql = 'SELECT p.id_category_default, p.id_shop FROM '._DB_PREFIX_.'product_shop p
                    WHERE p.id_product IN ('.pInSQL($idlist).')';

            $res = Db::getInstance()->executeS($sql);
            $array_defaults = array();
            $array_returns = array();

            foreach ($res as $row)
            {
                if (empty($array_defaults[$row['id_shop']]))
                {
                    $array_defaults[$row['id_shop']] = $row['id_category_default'];
                    $array_returns[$row['id_shop']] = 1;
                }
                else
                {
                    if ($row['id_category_default'] != $array_defaults[$row['id_shop']])
                    {
                        $array_returns[$row['id_shop']] = 0;
                    }
                }
            }

            foreach ($array_returns as $id_shop => $is_good)
            {
                if ($is_good == 1)
                {
                    if (empty($id_shop))
                    {
                        continue;
                    }
                    if (!empty($cdefault))
                    {
                        $cdefault .= ',';
                    }
                    $cdefault .= $id_shop.'_'.$array_defaults[$id_shop];
                }
            }
        }
        else
        {
            $default_shop = SCI::getSelectedShop();
            if (empty($default_shop))
            {
                $product = new Product($idlist);
                $default_shop = $product->id_shop_default;
            }
            $sql = 'SELECT p.id_category_default FROM '._DB_PREFIX_.'product_shop p
                    WHERE p.id_product IN ('.pInSQL($idlist).') AND id_shop='.(int) $default_shop;
            $res = Db::getInstance()->getRow($sql);
            $cdefault = $res['id_category_default'];
        }
    }
    else
    {
        $sql = 'SELECT p.id_category_default FROM '._DB_PREFIX_.'product p
                WHERE p.id_product IN ('.pInSQL($idlist).')';
        $res = Db::getInstance()->getRow($sql);
        $cdefault = $res['id_category_default'];
    }

    ## color categories who dont own all product list
    $sql = 'SELECT id_category,COUNT(id_product) as nb_products
            FROM '._DB_PREFIX_.'category_product 
            WHERE id_product IN ('.pInSQL($idlist).')
            GROUP BY id_category';
    $res = Db::getInstance()->ExecuteS($sql);
    if (!empty($res))
    {
        foreach ($res as $row)
        {
            if ($row['nb_products'] != $cntProducts)
            {
                $not_all_products_present[] = $row['id_category'];
                if (array_key_exists($row['id_category'],$used))
                {
                    unset($used[$row['id_category']]);
                }
            }
        }
    }
}

echo join(',', $used).'|'.$cdefault.'|'.implode(',', $not_all_products_present).'|'.join(',', $filter_used);
