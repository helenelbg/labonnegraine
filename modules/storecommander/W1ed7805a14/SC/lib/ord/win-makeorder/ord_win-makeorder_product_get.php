<?php

$id_lang = (int) Tools::getValue('id_lang');
$id_shop = (int) Tools::getValue('id_shop', 1);
$id_customer = (int) Tools::getValue('id_customer');
$filter_params = Tools::getValue('filter_params');

function getRowsFromDB()
{
    global $id_lang,$sc_agent,$id_shop,$filter_params,$id_customer;

    $shop_where = 0;
    if (version_compare(_PS_VERSION_, '1.5.0.10', '>='))
    {
        $shop_where = $id_shop;
    }

    if (version_compare(_PS_VERSION_, '1.6.0.10', '>='))
    {
        $inner = '';

        if ($shop_where > 0)
        {
            $inner = ' INNER JOIN '._DB_PREFIX_."tax_rules_group_shop trgs ON (trgs.id_tax_rules_group = trg.id_tax_rules_group AND trgs.id_shop = '".(int) $shop_where."')";
        }

        $sql = 'SELECT trg.name, trg.id_tax_rules_group,t.rate, trg.deleted
            FROM `'._DB_PREFIX_.'tax_rules_group` trg
            LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (trg.`id_tax_rules_group` = tr.`id_tax_rules_group` AND tr.`id_country` = '.(int) SCI::getDefaultCountryId().' AND tr.`id_state` = 0)
                    LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)
                '.$inner.'
            WHERE 1
                ORDER BY trg.deleted ASC, trg.name ASC';
        $res = Db::getInstance()->ExecuteS($sql);
        foreach ($res as $row)
        {
            if ($row['name'] == '')
            {
                $row['name'] = ' ';
            }

            if ($row['deleted'] == '1')
            {
                $row['name'] = _l('(deleted)').' '.$row['name'];
            }

            $tax[$row['id_tax_rules_group']] = $row['rate'];
        }
    }
    else
    {
        $inner = '';

        if (version_compare(_PS_VERSION_, '1.5.0.10', '>=') && $shop_where > 0)
        {
            $inner = ' INNER JOIN '._DB_PREFIX_."tax_rules_group_shop trgs ON (trgs.id_tax_rules_group = trg.id_tax_rules_group AND trgs.id_shop = '".(int) $shop_where."')";
        }

        $sql = 'SELECT trg.name, trg.id_tax_rules_group,t.rate
            FROM `'._DB_PREFIX_.'tax_rules_group` trg
            LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (trg.`id_tax_rules_group` = tr.`id_tax_rules_group` AND tr.`id_country` = '.(int) SCI::getDefaultCountryId().' AND tr.`id_state` = 0)
                    LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)
                '.$inner.'
            WHERE trg.active=1';
        $res = Db::getInstance()->ExecuteS($sql);
        foreach ($res as $row)
        {
            if ($row['name'] == '')
            {
                $row['name'] = ' ';
            }
            $tax[$row['id_tax_rules_group']] = $row['rate'];
        }
    }

    $decimal = (_s('CAT_PROD_PRICEWITHOUTTAX4DEC') == '1' ? 4 : 2);

    $where = '';
    if (!empty($filter_params))
    {
        $filter_params = explode(',', $filter_params);
        foreach ($filter_params as $filter_param)
        {
            list($field, $value) = explode('|||', $filter_param);
            if (!empty($value))
            {
                if ($field == 'id_product')
                {
                    $where .= ' AND p.id_product='.(int) $value.' ';
                }
                elseif ($field == 'id_product_attribute')
                {
                    $where .= ' AND pa.id_product_attribute='.(int) $value.' ';
                }
                elseif ($field == 'reference')
                {
                    $where .= " AND (p.reference LIKE '%".pSQL($value)."%' OR pa.reference LIKE '%".pSQL($value)."%') ";
                }
                elseif ($field == 'ean13')
                {
                    $where .= " AND (p.ean13 LIKE '%".pSQL($value)."%' OR pa.ean13 LIKE '%".pSQL($value)."%') ";
                }
                elseif ($field == 'product')
                {
                    $where .= " AND LOWER(pl.name) LIKE '%".pSQL(strtolower($value))."%' ";
                }
            }
        }
    }

    if (!empty($where))
    {
        $sql = 'SELECT p.id_product,p.id_category_default,pl.name as p_name,pa.id_product_attribute, p.ean13, p.reference,
            pa.ean13 as pa_ean13, pa.reference as pa_reference,
            p.price, p.id_tax_rules_group, p.ecotax,
            pa.price AS pa_price, pa.ecotax AS pa_ecotax
            '.(version_compare(_PS_VERSION_, '1.5.0.10', '>=') ? ' ,ps.id_category_default,pas.default_on,ps.price, ps.id_tax_rules_group, ps.ecotax,pas.price AS pa_price, pas.ecotax AS pa_ecotax,ps.active' : ',p.active').'
        FROM `'._DB_PREFIX_.'product` p
            INNER JOIN `'._DB_PREFIX_.'product_lang` pl ON (p.id_product=pl.id_product AND pl.id_lang='.(int) $sc_agent->id_lang.' '.(version_compare(_PS_VERSION_, '1.5.0.10', '>=') ? 'AND pl.id_shop='.$shop_where : '').')
            '.(version_compare(_PS_VERSION_, '1.5.0.10', '>=') ? ' INNER JOIN `'._DB_PREFIX_.'product_shop` ps ON (ps.id_product = p.id_product AND ps.id_shop='.$shop_where.') ' : '').'
            LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa ON (p.id_product=pa.id_product)
            '.(version_compare(_PS_VERSION_, '1.5.0.10', '>=') ? ' LEFT JOIN `'._DB_PREFIX_.'product_attribute_shop` pas ON (pas.id_product_attribute = pa.id_product_attribute AND pas.id_shop='.$shop_where.') ' : '').'
        WHERE 1 '.(!empty($where) ? ' AND ( 1 '.$where.') ' : '').'
            '.(version_compare(_PS_VERSION_, '1.5.0.10', '>=') ? ' AND ps.id_shop='.$shop_where : '').'
        GROUP BY p.id_product,pa.id_product_attribute
        ORDER BY pl.name ASC,'.(version_compare(_PS_VERSION_, '1.5.0.10', '>=') ? 'pas' : 'pa').'.default_on DESC
        LIMIT 100';
    }
    else
    {
        $sql = '
            SELECT od.*, od.product_id AS id_product, od.product_attribute_id AS id_product_attribute, pl.name as p_name, o.date_add as order_date, p.ean13, p.reference,
                pa.ean13 as pa_ean13, pa.reference as pa_reference,
                p.price, p.id_tax_rules_group, p.ecotax,
                pa.price AS pa_price, pa.ecotax AS pa_ecotax
                '.(version_compare(_PS_VERSION_, '1.5.0.10', '>=') ? ',ps.id_category_default,ps.price, ps.id_tax_rules_group, ps.ecotax,pas.price AS pa_price, pas.ecotax AS pa_ecotax,ps.active' : ',p.id_category_default,p.active').'
            FROM '._DB_PREFIX_.'order_detail od
                INNER JOIN '._DB_PREFIX_.'orders o ON (o.id_order=od.id_order)
                INNER JOIN '._DB_PREFIX_.'product p ON (p.id_product = od.product_id)
                '.(version_compare(_PS_VERSION_, '1.5.0.10', '>=') ? 'INNER JOIN '._DB_PREFIX_.'product_shop ps ON (ps.id_product = od.product_id AND ps.id_shop = od.id_shop)' : '').'
                INNER JOIN '._DB_PREFIX_.'product_lang pl ON ('.(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? 'ps.id_product' : 'p.id_product').' = pl.id_product AND pl.id_lang='.(int) $id_lang.(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? ' AND pl.id_shop=o.id_shop' : '').')
                LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa ON (od.product_attribute_id=pa.id_product_attribute)
                '.(version_compare(_PS_VERSION_, '1.5.0.10', '>=') ? ' LEFT JOIN `'._DB_PREFIX_.'product_attribute_shop` pas ON (pas.id_product_attribute = pa.id_product_attribute AND pas.id_shop='.$shop_where.') ' : '').'
            WHERE o.id_customer = "'.(int) $id_customer.'"
                '.(version_compare(_PS_VERSION_, '1.5.0.10', '>=') ? ' AND o.id_shop='.$shop_where.' ' : '').'
            GROUP BY od.product_id, od.product_attribute_id
            ORDER BY o.date_add DESC';
    }
//    echo $sql;
//    exit;
    $res = Db::getInstance()->ExecuteS($sql);
    $xml = '';
    if (!empty($res))
    {
        foreach ($res as $row)
        {
            $combination_detail = null;
            if (!empty($row['id_product_attribute']))
            {
                $prod = new Product($row['id_product']);
                if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                {
                    if (version_compare(_PS_VERSION_, '1.7.7.6', '>='))
                    {
                        $_POST['setShopContext'] = 's-'.(int) $row['id_shop'];
                        $context = Context::getContext();
                        $context->currency = Currency::getCurrencyInstance((array_key_exists('id_currenty', $row) ? (int) $row['id_currency'] : (int) SCI::getConfigurationValue('PS_CURRENCY_DEFAULT')));
                    }
                    $attributes = $prod->getAttributesResume($id_lang);
                    if (!empty($attributes))
                    {
                        foreach ($attributes as $attr)
                        {
                            if ($attr['id_product_attribute'] == $row['id_product_attribute'])
                            {
                                $combination_detail = $attr['attribute_designation'];
                                break;
                            }
                        }
                    }
                }
                else
                {
                    $detail = array();
                    $attributes = $prod->getAttributeCombinaisons($id_lang);
                    if (!empty($attributes))
                    {
                        foreach ($attributes as $attr)
                        {
                            if ($attr['id_product_attribute'] == $row['id_product_attribute'])
                            {
                                $detail[] = $attr['group_name'].' : '.$attr['attribute_name'];
                            }
                        }
                        $combination_detail = implode(', ', $detail);
                    }
                }
            }

            if (empty($row['id_product_attribute']))
            {
                $row['id_product_attribute'] = '0';
            }

            $price = $row['price'] + (!empty($row['pa_price']) ? $row['pa_price'] : 0);
            $price = number_format($price, $decimal, '.', '');
            $row['id_tax'] = $row['id_tax_rules_group'];
            $taxrate = $tax[(int) $row['id_tax']];
            if (!empty($row['pa_price']))
            {
                if (version_compare(_PS_VERSION_, '1.6.0.0', '>=') || ($row['pa_ecotax'] * 1) == 0)
                {
                    $row['pa_ecotax'] = $row['ecotax'];
                }
                $ecotax = (_s('CAT_PROD_ECOTAXINCLUDED') ? SCI::getEcotaxTaxRate() : 0);

                if (!empty($taxrate))
                {
                    $price_it = number_format(($row['price'] + $row['pa_price']) * ($taxrate / 100 + 1) + $ecotax, $decimal, '.', '');
                }
                else
                {
                    $price_it = number_format($row['price'] + $row['pa_price'] + $ecotax, $decimal, '.', '');
                }
            }
            else
            {
                $ecotax = (_s('CAT_PROD_ECOTAXINCLUDED') ? $row['ecotax'] * SCI::getEcotaxTaxRate() : 0);

                $price_it = number_format($row['price'] * ($taxrate / 100 + 1) + $ecotax, $decimal, '.', '');
            }

            $xml .= "<row id='".$row['id_product'].'_'.$row['id_product_attribute']."'>";
            $xml .= '      <userdata name="path_pdt">'.$row['id_category_default'].'-'.$row['id_product'].(!empty($row['id_product_attribute']) ? '-'.$row['id_product_attribute'] : '').'</userdata>';
            $xml .= '      <userdata name="active">'.$row['active'].'</userdata>';
            $xml .= '<cell>'.$row['id_product'].'</cell>';
            $xml .= '<cell>'.$row['id_product_attribute'].'</cell>';
            $xml .= '<cell>'.$row['reference'].(!empty($row['reference']) && !empty($row['pa_reference']) ? '-' : '').$row['pa_reference'].'</cell>';
            $xml .= '<cell>'.$row['ean13'].(!empty($row['ean13']) && !empty($row['pa_ean13']) ? '-' : '').$row['pa_ean13'].'</cell>';
            $xml .= '<cell'.($row['active'] == 0 ? ' bgColor="#D7D7D7"' : '').'><![CDATA['.$row['p_name'].(!empty($combination_detail) ? ' '.$combination_detail : '').']]></cell>';
            $xml .= '<cell>'.$price.'</cell>';
            $xml .= '<cell>'.$price_it.'</cell>';
            $xml .= '<cell><![CDATA['.SCI::getProductQty($row['id_product'], $row['id_product_attribute'], null, (!empty($shop_where) ? $shop_where : null)).']]></cell>';
            $xml .= '<cell>1</cell>';
            $xml .= '</row>';
        }
    }

    return $xml;
}

//XML HEADER
if (stristr($_SERVER['HTTP_ACCEPT'], 'application/xhtml+xml'))
{
    header('Content-type: application/xhtml+xml');
}
else
{
    header('Content-type: text/xml');
}
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";

$xml = getRowsFromDB();
?>
<rows id="0">
    <head>
        <beforeInit>
            <call command="attachHeader"><param><![CDATA[#numeric_filter,#numeric_filter,#text_filter,#text_filter,#text_filter,#numeric_filter,#numeric_filter,#numeric_filter]]></param></call>
        </beforeInit>
        <column id="id_product" width="60" type="ro" align="right" sort="int"><?php echo _l('id prod.'); ?></column>
        <column id="id_product_attribute" width="60" type="ro" align="right" sort="int"><?php echo _l('id prod. attr.'); ?></column>
        <column id="reference" width="100" type="ro" align="left" sort="str"><?php echo _l('Reference'); ?></column>
        <column id="ean13" width="100" type="ro" align="left" sort="int"><?php echo _l('EAN'); ?></column>
        <column id="product" width="300" type="ro" align="left" sort="str"><?php echo _l('Product'); ?></column>
        <column id="price" width="80" type="ro" align="right" sort="int"><?php echo _l('Price excl. Tax'); ?></column>
        <column id="price_it" width="80" type="ro" align="right" sort="int"><?php echo _l('Price incl. Tax'); ?></column>
        <column id="quantity" width="80" type="ro" align="right" sort="int"><?php echo _l('Stock available'); ?></column>
        <column id="quantity_needed" width="60" type="edn" align="right" sort="int"><?php echo _l('Quantity to add:'); ?></column>
    </head>
    <?php
    echo '<userdata name="uisettings">'.uisettings::getSetting('makeOrder_product_grid').'</userdata>'."\n";
    echo $xml;
    ?>
</rows>
