<?php

    $id_lang = (int) Tools::getValue('id_lang');
    $id_product = (Tools::getValue('id_product', 0));

    SCI::messageNotCompatibleWithAdvancedPack($id_product);

    if (SCMS)
    {
        $sql = 'SELECT s.*
                FROM '._DB_PREFIX_.'shop s
                '.((!empty($sc_agent->id_employee)) ? ' INNER JOIN '._DB_PREFIX_."employee_shop es ON (es.id_shop = s.id_shop AND es.id_employee = '".(int) $sc_agent->id_employee."') " : '').'
                ORDER BY s.name';
        $res = Db::getInstance()->ExecuteS($sql);
        $shops = array();
        $shops[0] = _l('All');
        foreach ($res as $shop)
        {
            $shops[$shop['id_shop']] = $shop['name'];
        }

        $has_shops_restrictions = false;
        $all_shops = Db::getInstance()->ExecuteS('SELECT id_shop FROM '._DB_PREFIX_.'shop');
        if (count($all_shops) != count($res))
        {
            $has_shops_restrictions = true;
        }

        $group_shops = array();
        $group_shops[0] = _l('All');
        if (!$has_shops_restrictions)
        {
            $sql = 'SELECT *
                            FROM '._DB_PREFIX_.'shop_group
                            ORDER BY name';
            $res = Db::getInstance()->ExecuteS($sql);
            foreach ($res as $group)
            {
                $group_shops[$group['id_shop_group']] = $group['name'];
            }
        }
    }

    $sql = 'SELECT *
                    FROM '._DB_PREFIX_.'group_lang
                    WHERE id_lang='.(int) $id_lang.'
                    ORDER BY id_group';
    $res = Db::getInstance()->ExecuteS($sql);
    $groups = array();
    $groups[0] = _l('All');
    foreach ($res as $group)
    {
        $groups[$group['id_group']] = $group['name'];
    }

    $sql = 'SELECT cl.id_country,cl.name
                    FROM '._DB_PREFIX_.'country_lang cl
                    LEFT JOIN '._DB_PREFIX_.'country c ON (c.id_country=cl.id_country)
                    WHERE cl.id_lang='.(int) $id_lang.' AND c.active=1
                    ORDER BY cl.name';
    $res = Db::getInstance()->ExecuteS($sql);
    $countries = array();
    $countries[0] = _l('All');
    foreach ($res as $country)
    {
        $countries[$country['id_country']] = $country['name'];
    }

    $sql = 'SELECT id_manufacturer,name
                    FROM '._DB_PREFIX_.'manufacturer';
    $res = Db::getInstance()->ExecuteS($sql);
    $manus = array();
    foreach ($res as $manu)
    {
        $manus[$manu['id_manufacturer']] = $manu['name'];
    }

    $sql = 'SELECT id_supplier,name
                    FROM '._DB_PREFIX_.'supplier';
    $res = Db::getInstance()->ExecuteS($sql);
    $suppliers = array();
    foreach ($res as $supplier)
    {
        $suppliers[$supplier['id_supplier']] = $supplier['name'];
    }

    $sql = 'SELECT id_currency,iso_code
                    FROM '._DB_PREFIX_.'currency
                    WHERE active=1
                    ORDER BY iso_code';
    $res = Db::getInstance()->ExecuteS($sql);
    $currencies = array();
    $currencies[0] = _l('All');
    foreach ($res as $currency)
    {
        $currencies[$currency['id_currency']] = $currency['iso_code'];
    }

    $defaultimg = 'lib/img/i.gif';
    if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
    {
        if (file_exists(SC_PS_PATH_DIR.'img/p/'.$user_lang_iso.'-default-'._s('CAT_PROD_GRID_IMAGE_SIZE').'_default.jpg'))
        {
            $defaultimg = SC_PS_PATH_REL.'img/p/'.$user_lang_iso.'-default-'._s('CAT_PROD_GRID_IMAGE_SIZE').'_default.jpg';
        }
    }
    else
    {
        if (file_exists(SC_PS_PATH_DIR.'img/p/'.$user_lang_iso.'-default-'._s('CAT_PROD_GRID_IMAGE_SIZE').'.jpg'))
        {
            $defaultimg = SC_PS_PATH_REL.'img/p/'.$user_lang_iso.'-default-'._s('CAT_PROD_GRID_IMAGE_SIZE').'.jpg';
        }
    }

    // SETTINGS, FILTERS AND COLONNES
    $sourceGridFormat = SCI::getGridViews('propspeprice');
    $sql_gridFormat = $sourceGridFormat;
    sc_ext::readCustomPropSpePriceGridConfigXML('gridConfig');
    $gridFormat = $sourceGridFormat;
    $cols = explode(',', $gridFormat);
    $all_cols = explode(',', $gridFormat);

    $colSettings = array();
    $colSettings = SCI::getGridFields('propspeprice');
    sc_ext::readCustomPropSpePriceGridConfigXML('colSettings');

    $tax = array(0 => 0);
    if (sc_in_array('id_tax', $cols, 'cols') || sc_in_array('id_tax_rules_group', $cols, 'cols') || sc_in_array('price_inc_tax', $cols, 'cols') || sc_in_array('price_with_reduction_tax_incl', $cols, 'cols'))
    {
        if (version_compare(_PS_VERSION_, '1.6.0.10', '>='))
        {
            $inner = '';

            if (SCMS && SCI::getSelectedShop() > 0)
            {
                $inner = ' INNER JOIN '._DB_PREFIX_."tax_rules_group_shop trgs ON (trgs.id_tax_rules_group = trg.id_tax_rules_group AND trgs.id_shop = '".(int) SCI::getSelectedShop()."')";
            }

            $sql = 'SELECT trg.name, trg.id_tax_rules_group,t.rate, trg.deleted
            FROM `'._DB_PREFIX_.'tax_rules_group` trg
            LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (trg.`id_tax_rules_group` = tr.`id_tax_rules_group` AND tr.`id_country` = '.(int) SCI::getDefaultCountryId().' AND tr.`id_state` = 0)
                    LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)
                '.$inner.'
            WHERE trg.active=1
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
                    $row['name'] .= ' '._l('(deleted)');
                }

                $tax[$row['id_tax_rules_group']] = $row['rate'];
            }
        }
        else
        {
            $inner = '';

            if (version_compare(_PS_VERSION_, '1.6.0.0', '>=') && SCMS && SCI::getSelectedShop() > 0)
            {
                $inner = ' INNER JOIN '._DB_PREFIX_."tax_rules_group_shop trgs ON (trgs.id_tax_rules_group = trg.id_tax_rules_group AND trgs.id_shop = '".(int) SCI::getSelectedShop()."')";
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
    }

    function getFooterColSettings()
    {
        global $cols,$colSettings;

        $footer = '';
        foreach ($cols as $id => $col)
        {
            if (sc_array_key_exists($col, $colSettings) && sc_array_key_exists('footer', $colSettings[$col]))
            {
                $footer .= $colSettings[$col]['footer'].',';
            }
            else
            {
                $footer .= ',';
            }
        }

        return $footer;
    }

    function getFilterColSettings()
    {
        global $cols,$colSettings;

        $filters = '';
        foreach ($cols as $id => $col)
        {
            if ($colSettings[$col]['filter'] == 'na')
            {
                $colSettings[$col]['filter'] = '';
            }
            $filters .= $colSettings[$col]['filter'].',';
        }
        $filters = trim($filters, ',');

        return $filters;
    }

    function getColSettingsAsXML()
    {
        global $cols,$colSettings;

        $uiset = uisettings::getSetting('cat_specificprice');
        $tmp = explode('|', $uiset);
        if (isset($tmp[2]))
        {
            $tmp = explode('-', $tmp[2]);
            $sizes = array();
            foreach ($tmp as $v)
            {
                $s = explode(':', $v);
                $sizes[$s[0]] = $s[1];
            }
        }
        $tmp = explode('|', $uiset);
        if (isset($tmp[0]))
        {
            $tmp = explode('-', $tmp[0]);
            $hidden = array();
            foreach ($tmp as $v)
            {
                $s = explode(':', $v);
                if (isset($s[0]) && isset($s[1]))
                {
                    $hidden[$s[0]] = $s[1];
                }
            }
        }

        $xml = '';
        foreach ($cols as $id => $col)
        {
            $xml .= '<column id="'.$col.'"'.(sc_array_key_exists('format', $colSettings[$col]) ?
                    ' format="'.$colSettings[$col]['format'].'"' : '').
                    ' width="'.(sc_array_key_exists($col, $sizes) ? $sizes[$col] : $colSettings[$col]['width']).'"'.
                    ' hidden="'.(sc_array_key_exists($col, $hidden) ? $hidden[$col] : 0).'"'.
                    ' align="'.$colSettings[$col]['align'].'"
                    type="'.$colSettings[$col]['type'].'"
                    '.($colSettings[$col]['type'] == 'combo' ? 'source="index.php?ajax=1&amp;act=cat_specificprice_customer_get&amp;ajaxCall=1" auto="true" cache="false"' : '').'
                    sort="'.$colSettings[$col]['sort'].'"
                    color="'.$colSettings[$col]['color'].'">'.$colSettings[$col]['text'];
            if (is_array($colSettings[$col]) && sc_array_key_exists('options', $colSettings[$col]) && is_array($colSettings[$col]['options']) && !empty($colSettings[$col]['options']))
            {
                foreach ($colSettings[$col]['options'] as $k => $v)
                {
                    $xml .= '<option value="'.str_replace('"', '\'', $k).'"><![CDATA['.$v.']]></option>';
                }
            }
            $xml .= '</column>'."\n";
        }

        return $xml;
    }

    function generateValue($col, $row, $p, $pa = null)
    {
        global $colSettings,$id_lang,$tax,$defaultimg,$manus,$suppliers;

        $round = (_s('CAT_PRODPROP_SPECIFICPRICE_4DEC') ? 4 : 2);
        $taxrate = $row['product_taxrate'];
        $ecotax = $row['product_ecotax'];
        $return = '';
        switch ($col){
            case 'id_specific_price':
                $return .= ('<cell style="color:#999999">'.$row['id_specific_price'].'</cell>');
                break;
            case 'from_quantity':
                if (_s('APP_COMPAT_MODULE_PPE'))
                {
                    $row['from_quantity'] = number_format($row['from_quantity'], 6, '.', '');
                }
                $return .= ('<cell><![CDATA['.$row['from_quantity'].']]></cell>');
                break;
            case 'price':
                $return .= ('<cell>'.($row['price'] != -1 || version_compare(_PS_VERSION_, '1.5.0.0', '<') ? number_format($row['price'], $round) : '-1').'</cell>');
                break;
            case 'reduction':
                $return .= ('<cell>'.($row['reduction_type'] == 'percentage' ? (number_format($row['reduction'] * 100, 2)).'%' : number_format($row['reduction'], $round)).'</cell>');
                break;
            case 'name':
                $name = $p->name;
                if (is_array($name))
                {
                    $name = $name[$id_lang];
                }
                $return .= '<cell><![CDATA['.$name.']]></cell>';
                break;
            case 'image':
                $f = '';
                $image = Image::getCover((int) $p->id);
                if (empty($image['id_image']))
                {
                    $f = '<i class="fad fa-file-image" ></i>';
                }
                else
                {
                    $f = "<img src='".SC_PS_PATH_REL.'img/p/'.getImgPath((int) $p->id, (int) $image['id_image'], _s('CAT_PROD_GRID_IMAGE_SIZE'))."'/>";
                }
                $return .= '<cell><![CDATA['.$f.']]></cell>';
                break;
            case 'active':
                $f = '';
                if (!empty($p->active))
                {
                    $f = _l('Yes');
                }
                else
                {
                    $f = _l('No');
                }
                $return .= '<cell><![CDATA['.$f.']]></cell>';
                break;
            case 'id_manufacturer':
                $return .= '<cell><![CDATA['.$manus[$p->id_manufacturer].']]></cell>';
                break;
            case 'id_supplier':
                $return .= '<cell><![CDATA['.$suppliers[$p->id_supplier].']]></cell>';
                break;
            case 'id_customer':
                if ($row['id_customer'] > 0)
                {
                    $sql = 'SELECT firstname, lastname FROM '._DB_PREFIX_.'customer WHERE id_customer = '.(int) $row['id_customer'];
                    $customer = Db::getInstance()->getRow($sql);
                    $return .= '<cell><![CDATA['.$customer['firstname'].' '.$customer['lastname'].']]></cell>';
                }
                else
                {
                    $return .= '<cell><![CDATA['._l('All').']]></cell>';
                }
                break;
            case 'price_exl_tax':
            case 'price_with_reduction_tax_excl':
                if (empty($row['id_product_attribute']))
                {
                    $price_tax_excl = $p->price;
                }
                else
                {
                    $price_tax_excl = $pa->price + $p->price;
                }

                ## with reduction
                if ($col == 'price_with_reduction_tax_excl')
                {
                    if ($row['price'] > 1)
                    {
                        $price_tax_excl = $row['price'];
                    }
                    if ($row['reduction_type'] == 'amount')
                    {
                        if ($row['reduction_tax'])
                        {
                            $price_tax_excl -= ($row['reduction'] / ($taxrate / 100 + 1));
                        }
                        else
                        {
                            $price_tax_excl -= $row['reduction'];
                        }
                    }
                    else
                    {
                        $price_tax_excl -= $price_tax_excl * $row['reduction'];
                    }
                }
                $return .= '<cell><![CDATA['.number_format($price_tax_excl, 6, '.', '').']]></cell>';
                break;
            case 'price_inc_tax':
                if (empty($row['id_product_attribute']))
                {
                    $price_tax_incl = $p->price * ($taxrate / 100 + 1) + $ecotax;
                }
                else
                {
                    if (!empty($taxrate))
                    {
                        $price_tax_incl = $pa->price * ($taxrate / 100 + 1) + $p->price * ($taxrate / 100 + 1) + $ecotax;
                    }
                    else
                    {
                        $price_tax_incl = $pa->price + $p->price + $ecotax;
                    }
                }
                $return .= '<cell>'.number_format($price_tax_incl, 6, '.', '').'</cell>';
                break;
            case 'price_with_reduction_tax_incl':
                if ($row['price'] > 1)
                {
                    $p_price = $pa_price = $row['price'];
                }
                else
                {
                    $p_price = $p->price;
                    $pa_price = $pa->price + $p_price;
                }
                $reduction_tax_excl = false;
                ## reduction tax excl
                if (empty($row['reduction_tax']))
                {
                    $pa_price -= ($row['reduction_type'] == 'amount' ? $row['reduction'] : $pa_price * $row['reduction']);
                    $p_price -= ($row['reduction_type'] == 'amount' ? $row['reduction'] : $p_price * $row['reduction']);

                    $reduction_tax_excl = true;
                }
                if (empty($row['id_product_attribute']))
                {
                    $price_tax_incl = $p_price * ($tax[(int) $p->id_tax_rules_group] / 100 + 1) + $ecotax;
                }
                else
                {
                    if (!empty($taxrate))
                    {
                        $price_tax_incl = $pa_price * ($taxrate / 100 + 1) + $ecotax;
                    }
                    else
                    {
                        $price_tax_incl = $pa_price + $ecotax;
                    }
                }

                if (!$reduction_tax_excl)
                {
                    $price_tax_incl -= ($row['reduction_type'] == 'amount' ? $row['reduction'] : $price_tax_incl * $row['reduction']);
                }
                $return .= '<cell>'.number_format($price_tax_incl, 6, '.', '').'</cell>';
                break;
            case 'reference':case 'supplier_reference':case 'ean13':case 'upc':
                if (empty($row['id_product_attribute']))
                {
                    $return .= '<cell><![CDATA['.$p->{$col}.']]></cell>';
                }
                else
                {
                    $return .= '<cell><![CDATA['.$pa->{$col}.']]></cell>';
                }
                break;
            case 'id_specific_price_rule':
                if ($row['id_specific_price_rule'] > 0)
                {
                    $return .= '<cell><![CDATA['._l('Catalog price rule').']]></cell>';
                }
                else
                {
                    $return .= '<cell><![CDATA['._l('Special price').']]></cell>';
                }
                break;
            case 'reduction_tax':
                if (version_compare(_PS_VERSION_, '1.6.0.11', '>=') && $row['price'] > -1)
                {
                    return '<cell type="ro"><![CDATA['._l('Excl. tax').']]></cell>';
                }

                return '<cell><![CDATA['.(version_compare(_PS_VERSION_, '1.6.0.11', '>=') ? (int) $row[$col] : _l('Incl. tax')).']]></cell>';
                break;
            default:
                $return .= '<cell><![CDATA['.$row[$col].']]></cell>';
        }

        return $return;
    }

    function getRowsFromDB()
    {
        global $id_lang,$id_product,$cols,$colSettings,$tax,$sql;

        $where = '';

        $sql = ' SELECT sp.* ';
        sc_ext::readCustomPropSpePriceGridConfigXML('SQLSelectDataSelect');
        $sql .= ' FROM `'._DB_PREFIX_.'specific_price` sp';
        sc_ext::readCustomPropSpePriceGridConfigXML('SQLSelectDataLeftJoin');
        $sql .= ' WHERE sp.id_product IN ('.pInSQL($id_product).')'
        .(version_compare(_PS_VERSION_, '1.5.0.2', '>=') ? ' AND sp.id_cart = 0 ' : '').
        'ORDER BY sp.`from` DESC';
        $res = Db::getInstance()->ExecuteS($sql);
        $xml = '';
        $ecotaxTaxRate = SCI::getEcotaxTaxRate();
        $selectedShop = SCI::getSelectedShop();

        $disabled_fields_by_id_speprice = array();

        foreach ($res as $specific_price)
        {
            $row_color = '';
            $id_product = $specific_price['id_product'];
            $pa = null;

            if ($specific_price['price'] > -1)
            {
                $disabled_fields_by_id_speprice[] = $specific_price['id_specific_price'];
            }

            if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
            {
                $product = new Product((int) $id_product, false, (int) $id_lang, (int) $selectedShop);
            }
            else
            {
                $product = new Product((int) $id_product, (int) $id_lang);
            }

            if (version_compare(_PS_VERSION_, '1.5.0.0', '>=') && !empty($specific_price['id_product_attribute']))
            {
                $row_color = 'color:#999999';
                $id_product .= '_'.$specific_price['id_product_attribute'];
                if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                {
                    $pa = new Combination((int) $specific_price['id_product_attribute'], (int) $id_lang, (int) $selectedShop);
                }
                else
                {
                    $pa = new Combination((int) $specific_price['id_product_attribute'], (int) $id_lang);
                }
            }
            $from_base = $specific_price['from'];
            $to_base = $specific_price['to'];
            if ($specific_price['from'] == $specific_price['to'])
            {
                $specific_price['from'] = date('Y-01-01 00:00:00');
                $specific_price['to'] = (date('Y') + 1).date('-m-d 00:00:00');
            }
            if ($specific_price['from'] == '0000-00-00 00:00:00')
            {
                $specific_price['from'] = date('Y-01-01 00:00:00');
            }
            if ($specific_price['to'] == '0000-00-00 00:00:00')
            {
                $specific_price['to'] = (date('Y') + 1).date('-m-d 00:00:00');
            }
            // DATES NUMERIQUES
            if ($from_base != '0000-00-00 00:00:00')
            {
                $specific_price['from_num'] = formatDateNum($specific_price['from']);
            }
            else
            {
                $specific_price['from_num'] = _l('Unlimited');
            }
            if ($to_base != '0000-00-00 00:00:00')
            {
                $specific_price['to_num'] = formatDateNum($specific_price['to']);
            }
            else
            {
                $specific_price['to_num'] = _l('Unlimited');
            }

            $xml .= "<row id='".$specific_price['id_specific_price']."' style=\"".$row_color.'">';
            if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
            {
                $xml .= '<userdata name="is_combination">'.((!empty($row_color)) ? '1' : '0').'</userdata>';
                $xml .= '<userdata name="id_specific_price_rule">'.$specific_price['id_specific_price_rule'].'</userdata>';
            }
            if (empty($specific_price['id_product_attribute']))
            {
                $specific_price['product_taxrate'] = (array_key_exists((int) $product->id_tax_rules_group, $tax) ? $tax[(int) $product->id_tax_rules_group] : 0);
                $specific_price['product_ecotax'] = (_s('CAT_PROD_ECOTAXINCLUDED') ? $product->ecotax * $ecotaxTaxRate : $product->ecotax);
            }
            else
            {
                $specific_price['product_taxrate'] = (array_key_exists((int) $product->id_tax_rules_group, $tax) ? $tax[(int) $product->id_tax_rules_group] : 0);
                $specific_price['product_ecotax'] = (_s('CAT_PROD_ECOTAXINCLUDED') ? $pa->ecotax * $ecotaxTaxRate : 0);
            }
            sc_ext::readCustomPropSpePriceGridConfigXML('rowUserData', (array) $specific_price);
            foreach ($cols as $field)
            {
                if (!empty($field) && !empty($colSettings[$field]))
                {
                    $xml .= generateValue($field, $specific_price, $product, $pa);
                }
            }
            $xml .= '</row>';
        }

        $userdata_supp = '<userdata name="disabled_fields_by_id_speprice">'.implode(',', $disabled_fields_by_id_speprice).'</userdata>'."\n";

        return $userdata_supp.$xml;
    }
/*
* FUNCTIONS
*/
function formatDateNum($date)
{
    $date = explode(' ', $date);

    return date('Ymd', strtotime($date[0]));
}

$xml = getRowsFromDB();

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
    echo '<rows><head>';
    echo getColSettingsAsXML();
    echo '<afterInit><call command="attachHeader"><param>'.getFilterColSettings().'</param></call>
            <call command="attachFooter"><param><![CDATA['.getFooterColSettings().']]></param></call></afterInit>';
    echo '</head>'."\n";

    echo '<userdata name="uisettings">'.uisettings::getSetting('cat_specificprice').'</userdata>'."\n";
    sc_ext::readCustomPropSpePriceGridConfigXML('gridUserData');

    echo $xml;
?>
</rows>
