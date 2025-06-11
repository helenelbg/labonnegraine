<?php

$id_product = Tools::getValue('id_product', 1);
$id_product_attribute = Tools::getValue('id_product_attribute', 1);
$name = Tools::getValue('name', 1);
$reference = Tools::getValue('reference', 1);
$supplier_reference = Tools::getValue('supplier_reference', 1);
$supplier_reference_all = Tools::getValue('supplier_reference_all', 1);
$ean = Tools::getValue('ean', 1);
$upc = 0;
$upc = Tools::getValue('upc', 1);
$mpn = Tools::getValue('mpn', 1);
$short_desc = Tools::getValue('short_desc', 0);
$desc = Tools::getValue('desc', 0);
$how_equal = Tools::getValue('how_equal', 0);
$limit = 25 * $nblanguages;
$res = '';

$shop_where = '';
if (SCMS)
{
    if (SCI::getSelectedShop() > 0)
    {
        $shop_where = " '".(int) SCI::getSelectedShop()."' ";
    }
    else
    {
        $shop_where = ' p.id_shop_default ';
    }
}

if (is_numeric($_GET['q']))
{
    $sql = 'SELECT p.id_product,p.id_category_default,pl.name as pname,cl.name as cname,pl2.name as pname2,pa.id_product_attribute
            '.(SCMS ? ' ,ps.id_category_default,pas.default_on ' : '').'
            FROM `'._DB_PREFIX_.'product` p
            LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (p.id_product=pl.id_product '.(SCMS ? 'AND pl.id_shop='.$shop_where : '').')
            LEFT JOIN `'._DB_PREFIX_.'product_lang` pl2 ON (p.id_product=pl2.id_product AND pl2.id_lang='.(int) $sc_agent->id_lang.' '.(SCMS ? 'AND pl2.id_shop='.$shop_where : '').')
            '.(SCMS ? ' LEFT JOIN `'._DB_PREFIX_.'product_shop` ps ON (ps.id_product = p.id_product AND ps.id_shop='.$shop_where.') ' : '').'
            LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa ON (p.id_product=pa.id_product)
            '.(SCMS ? ' LEFT JOIN `'._DB_PREFIX_.'product_attribute_shop` pas ON (pas.id_product_attribute = pa.id_product_attribute AND pas.id_shop='.$shop_where.') ' : '').'
            LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON (cl.id_category='.(SCMS ? 'ps' : 'p').'.id_category_default AND cl.id_lang='.(int) $sc_agent->id_lang.')
            '.(version_compare(_PS_VERSION_, '1.5.0.0', '>=') && $supplier_reference_all == 1 ? ' LEFT JOIN `'._DB_PREFIX_.'product_supplier` psup ON (psup.id_product=p.id_product) ' : '').'
            WHERE (0
                '.(($id_product == 1) ? " OR p.id_product = '".(float) Tools::getValue('q')."'" : '').'
                '.(($id_product_attribute == 1) ? " OR pa.id_product_attribute = '".(float) Tools::getValue('q')."'" : '').'
                '.(($ean == 1) ? ($how_equal == 1 ? " OR p.ean13 = '".psql(Tools::getValue('q'))."'" : " OR p.ean13 LIKE '%".psql(Tools::getValue('q'))."%'") : '').'
                '.(($ean == 1) ? ($how_equal == 1 ? " OR pa.ean13 = '".psql(Tools::getValue('q'))."'" : " OR pa.ean13 LIKE '%".psql(Tools::getValue('q'))."%'") : '').'
                '.(($reference == 1) ? ($how_equal == 1 ? " OR p.reference = '".psql(Tools::getValue('q'))."'" : " OR p.reference LIKE '%".psql(Tools::getValue('q'))."%'") : '').'
                '.(($supplier_reference == 1) ? ($how_equal == 1 ? " OR p.supplier_reference = '".psql(Tools::getValue('q'))."'" : " OR p.supplier_reference LIKE '%".psql(Tools::getValue('q'))."%'") : '').'
                '.(($reference == 1) ? ($how_equal == 1 ? " OR pa.reference = '".psql(Tools::getValue('q'))."'" : " OR pa.reference LIKE '%".psql(Tools::getValue('q'))."%'") : '').'
                '.(($supplier_reference == 1) ? ($how_equal == 1 ? " OR pa.supplier_reference = '".psql(Tools::getValue('q'))."'" : " OR pa.supplier_reference LIKE '%".psql(Tools::getValue('q'))."%'") : '').'
                '.(($upc == 1) ? ($how_equal == 1 ? " OR p.upc = '".psql(Tools::getValue('q'))."'" : " OR p.upc LIKE '%".psql(Tools::getValue('q'))."%'") : '').'
                '.(($upc == 1) ? ($how_equal == 1 ? " OR pa.upc = '".psql(Tools::getValue('q'))."'" : " OR pa.upc LIKE '%".psql(Tools::getValue('q'))."%'") : '').'
                '.(($mpn == 1 && version_compare(_PS_VERSION_, '1.7.7.0', '>=')) ? ($how_equal == 1 ? " OR p.mpn = '".psql(Tools::getValue('q'))."'" : " OR p.mpn LIKE '%".psql(Tools::getValue('q'))."%'") : '').'
                '.(($mpn == 1 && version_compare(_PS_VERSION_, '1.7.7.0', '>=')) ? ($how_equal == 1 ? " OR pa.mpn = '".psql(Tools::getValue('q'))."'" : " OR pa.mpn LIKE '%".psql(Tools::getValue('q'))."%'") : '').'
                '.(($supplier_reference_all == 1 && version_compare(_PS_VERSION_, '1.5.0.0', '>=')) ? ($how_equal == 1 ? " OR psup.product_supplier_reference = '".psql(Tools::getValue('q'))."'" : " OR psup.product_supplier_reference LIKE '%".psql(Tools::getValue('q'))."%'") : '').'
                )
                '.(SCMS ? ' AND ps.id_shop='.$shop_where : '').'
            GROUP BY p.id_product
            ORDER BY pl.name ASC,'.(SCMS ? 'pas' : 'pa').'.default_on DESC
            LIMIT '.(int) $limit;
    $res = Db::getInstance()->ExecuteS($sql);
}
else
{
    $sql = 'SELECT p.id_product,p.id_category_default,pl.name as pname,cl.name as cname,pl2.name as pname2,pa.id_product_attribute
            '.(SCMS ? ' ,ps.id_category_default ' : '').'
            FROM `'._DB_PREFIX_.'product` p
            LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (p.id_product=pl.id_product '.(SCMS ? 'AND pl.id_shop='.$shop_where : '').')
            LEFT JOIN `'._DB_PREFIX_.'product_lang` pl2 ON (p.id_product=pl2.id_product AND pl2.id_lang='.(int) $sc_agent->id_lang.' '.(SCMS ? 'AND pl2.id_shop='.$shop_where : '').')
            '.(SCMS ? ' LEFT JOIN `'._DB_PREFIX_.'product_shop` ps ON (ps.id_product = p.id_product AND ps.id_shop='.$shop_where.') ' : '').'
            LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa ON (p.id_product=pa.id_product)
            '.(SCMS ? ' LEFT JOIN `'._DB_PREFIX_.'product_attribute_shop` pas ON (pas.id_product_attribute = pa.id_product_attribute AND pas.id_shop='.$shop_where.') ' : '').'
            LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON (cl.id_category='.(SCMS ? 'ps' : 'p').'.id_category_default AND cl.id_lang='.(int) $sc_agent->id_lang.')
            '.(version_compare(_PS_VERSION_, '1.5.0.0', '>=') && $supplier_reference_all == 1 ? ' LEFT JOIN `'._DB_PREFIX_.'product_supplier` psup ON (psup.id_product=p.id_product) ' : '').'
            WHERE (0
                '.(($reference == 1) ? ($how_equal == 1 ? " OR p.reference = '".psql(Tools::getValue('q'))."'" : " OR p.reference LIKE '%".psql(Tools::getValue('q'))."%'") : '').'
                '.(($supplier_reference == 1) ? ($how_equal == 1 ? " OR p.supplier_reference = '".psql(Tools::getValue('q'))."'" : " OR p.supplier_reference LIKE '%".psql(Tools::getValue('q'))."%'") : '').'
                '.(($name == 1) ? ($how_equal == 1 ? " OR pl.name = '".psql(Tools::getValue('q'))."'" : " OR pl.name LIKE '%".psql(Tools::getValue('q'))."%'") : '').'
                '.(($reference == 1) ? ($how_equal == 1 ? " OR pa.reference = '".psql(Tools::getValue('q'))."'" : " OR pa.reference LIKE '%".psql(Tools::getValue('q'))."%'") : '').'
                '.(($supplier_reference == 1) ? ($how_equal == 1 ? " OR pa.supplier_reference = '".psql(Tools::getValue('q'))."'" : " OR pa.supplier_reference LIKE '%".psql(Tools::getValue('q'))."%'") : '').'
                '.(($mpn == 1 && version_compare(_PS_VERSION_, '1.7.7.0', '>=')) ? ($how_equal == 1 ? " OR p.mpn = '".psql(Tools::getValue('q'))."'" : " OR p.mpn LIKE '%".psql(Tools::getValue('q'))."%'") : '').'
                '.(($mpn == 1 && version_compare(_PS_VERSION_, '1.7.7.0', '>=')) ? ($how_equal == 1 ? " OR pa.mpn = '".psql(Tools::getValue('q'))."'" : " OR pa.mpn LIKE '%".psql(Tools::getValue('q'))."%'") : '').'
                '.(($supplier_reference_all == 1 && version_compare(_PS_VERSION_, '1.5.0.0', '>=')) ? ($how_equal == 1 ? " OR psup.product_supplier_reference = '".psql(Tools::getValue('q'))."'" : " OR psup.product_supplier_reference LIKE '%".psql(Tools::getValue('q'))."%'") : '').'
                '.(($short_desc == 1) ? ($how_equal == 1 ? " OR pl.description_short = '".psql(Tools::getValue('q'))."'" : " OR pl.description_short LIKE '%".psql(Tools::getValue('q'))."%'") : '').'
                '.(($desc == 1) ? ($how_equal == 1 ? " OR pl.description = '".psql(Tools::getValue('q'))."'" : " OR pl.description LIKE '%".psql(Tools::getValue('q'))."%'") : '').'
                )
                '.(SCMS ? ' AND ps.id_shop='.$shop_where : '').'
            GROUP BY p.id_product
            ORDER BY pl.name ASC,'.(SCMS ? 'pas' : 'pa').'.default_on DESC
            LIMIT '.(int) $limit;
    $res = Db::getInstance()->ExecuteS($sql);
}

if ($res != '')
{
    $content = '';
    $plist = array();
    echo '[';
    foreach ($res as $row)
    {
        if (!in_array($row['id_product'], $plist))
        {
            $content .= '{"id_category":"'.$row['id_category_default'].'","id_product":"'.$row['id_product'].'","id_product_attribute":"'.(int) $row['id_product_attribute'].'","pname":"'.str_replace("\'", '', addslashes($row['pname2'])).'","cname":"'.str_replace("\'", '', addslashes($row['cname'])).'"},';
            $plist[] = $row['id_product'];
        }
        if (count($plist) > 25)
        {
            break;
        }
    }
    $content = trim($content, ',');
    echo $content;
    echo ']';
}
