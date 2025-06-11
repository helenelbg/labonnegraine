<?php

    $idlist = Tools::getValue('idlist', 0);
    $id_lang = (int) Tools::getValue('id_lang');
    $cntProducts = count(explode(',', $idlist));
    $sc_active = SCI::getConfigurationValue('SC_PLUG_DISABLECOMBINATIONS', 0);

    $reference_check = (bool) Tools::getValue('reference_check', null);

    SCI::messageNotCompatibleWithAdvancedPack($idlist);

    if (!empty($reference_check))
    {
        list($id_product, $id_product_attribute) = explode('_', Tools::getValue('reference', null));
        $reference = Tools::getValue('reference', null);
        $error = null;
        $sql = 'SELECT id_product,reference 
                FROM '._DB_PREFIX_.'product 
                WHERE reference = "'.psql($reference).'" 
                AND id_product != '.(int) $id_product;
        $res = Db::getInstance()->executeS($sql);
        if (!empty($res))
        {
            foreach ($res as $row)
            {
                $error .= _l('Duplicate reference found with product ID:').' '.$row['id_product'].'<br/>';
            }
        }
        $sql = 'SELECT id_product_attribute,reference 
                FROM '._DB_PREFIX_.'product_attribute 
                WHERE reference = "'.psql($reference).'" 
                AND id_product != '.(int) $id_product;
        $res = Db::getInstance()->executeS($sql);
        if (!empty($res))
        {
            foreach ($res as $row)
            {
                $error .= _l('Duplicate reference found with combination ID:').' '.$row['id_product_attribute'].'<br/>';
            }
        }
        if (!empty($error))
        {
            exit($error);
        }
        else
        {
            exit('OK');
        }
        exit();
    }

    $filters = '';
    $colonnes = '';
    $xml = '';

    // Tax
    $arrTax = array(0 => '-');
    $tax = array(0 => 0);
    $sql = 'SELECT trg.name, trg.id_tax_rules_group,t.rate
    FROM `'._DB_PREFIX_.'tax_rules_group` trg
    LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (trg.`id_tax_rules_group` = tr.`id_tax_rules_group` AND tr.`id_country` = '.(int) SCI::getDefaultCountryId().' AND tr.`id_state` = 0)
LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)
WHERE trg.active=1';
    $res = Db::getInstance()->ExecuteS($sql);
    foreach ($res as $row)
    {
        if ($row['name'] == '')
        {
            $row['name'] = ' ';
        }
        $arrTax[$row['id_tax_rules_group']] = $row['name'];
        $tax[$row['id_tax_rules_group']] = $row['rate'];
    }

    // SETTINGS, FILTERS AND COLONNES
    $sourceGridFormat = SCI::getGridViews('combinationmultiproduct');
    $sql_gridFormat = $sourceGridFormat;
    sc_ext::readCustomCombinationMultiProductGridConfigXML('gridConfig');
    if (empty($sc_active))
    {
        $sourceGridFormat = str_replace(',sc_active,', ',', $sourceGridFormat);
    }
    $gridFormat = $sourceGridFormat;
    $cols = explode(',', $gridFormat);
    $all_cols = explode(',', $gridFormat);

    $colSettings = array();
    $colSettings = SCI::getGridFields('combinationmultiproduct');
    sc_ext::readCustomCombinationMultiProductGridConfigXML('colSettings');

    /*
     0: coef = PV HT - PV HT
    1: coef = (PV HT - PA HT) / PA HT
    2: coef = PV HT / PA HT
    3: coef = PV TTC / PA HT
    4: coef = (PV TTC - PA HT) / PA HT
    */
    function getColIndex($col)
    {
        global $list_shop_fields;
        if ($list_shop_fields)
        {
            $tmp = explode(',', $list_shop_fields);
            foreach ($tmp as $key => $field)
            {
                if ($field == $col)
                {
                    return $key + 7;
                }
            }
        }

        return -1;
    }
    $marginMatrix = array(
            0 => '[=c'.getColIndex('priceextax').'-c'.getColIndex('wholesale_price').']',
            1 => '[=(c'.getColIndex('priceextax').'-c'.getColIndex('wholesale_price').')/c'.getColIndex('wholesale_price').']',
            2 => '[=c'.getColIndex('priceextax').'/c'.getColIndex('wholesale_price').']',
            3 => '[=c'.getColIndex('price').'/c'.getColIndex('wholesale_price').']',
            4 => '[=(c'.getColIndex('price').'-c'.getColIndex('wholesale_price').')*100/c'.getColIndex('wholesale_price').']',
            5 => '[=(c'.getColIndex('priceextax').'-c'.getColIndex('wholesale_price').')*100/c'.getColIndex('priceextax').']',
    );
    $marginMatrix_form = array(
            0 => '{price}-{wholesale_price}',
            1 => '({price}-{wholesale_price})*100/{wholesale_price}',
            2 => '{price}/{wholesale_price}',
            3 => '{price_inc_tax}/{wholesale_price}',
            4 => '({price_inc_tax}-{wholesale_price})*100/{wholesale_price}',
            5 => '({price}-{wholesale_price})*100/{price}',
    );

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

        $uiset = uisettings::getSetting('cat_combinationmultiproduct');
        $hidden = $sizes = array();
        if (!empty($uiset))
        {
            $tmp = explode('|', $uiset);
            $tmp = explode('-', $tmp[2]);
            foreach ($tmp as $v)
            {
                $s = explode(':', $v);
                $sizes[$s[0]] = $s[1];
            }
            $tmp = explode('|', $uiset);
            $tmp = explode('-', $tmp[0]);
            foreach ($tmp as $v)
            {
                $s = explode(':', $v);
                $hidden[$s[0]] = $s[1];
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

    function generateValue($col, $product_attr, $product, $taxrate, $combis)
    {
        global $colSettings,$id_lang,$cols,$all_cols,$combination,$product_attribute;

        $product_attribute = (array) $product_attr;
        $combination = (array) $combis;
        $product = (array) $product;

        sc_ext::readCustomCombinationMultiProductGridConfigXML('rowUserData', $product_attribute);
        $return = '';
        switch ($col){
            case 'id_product':
                $return .= '<cell>'.$product['id'].'</cell>';
                break;
            case 'id_product_attribute':
                $return .= '<cell style="color:'.($product_attribute['default_on'] ? '#0000FF' : '#999999').'">'.$product_attribute['id'].'</cell>';
                break;
            case 'ecotax':
                $ecotax = 0;
                if (!empty($product_attribute['ecotax']))
                {
                    $ecotax = $product_attribute['ecotax'];
                }
                else
                {
                    $ecotax = $product['ecotax'];
                }
                if (_s('CAT_PROD_ECOTAXINCLUDED'))
                {
                    $ecotax = $ecotax * SCI::getEcotaxTaxRate();
                }
                $return .= '<cell>'.($ecotax).'</cell>';
                break;
            case 'taxrate':
                $return .= '<cell>'.number_format($taxrate, 2, '.', '').'</cell>';
                break;
            case 'price':
                if (version_compare(_PS_VERSION_, '1.7.7.6', '>='))
                {
                    $ecotax = $product_attribute['ecotax'] * SCI::getEcotaxTaxRate();
                }
                elseif (_s('CAT_PROD_ECOTAXINCLUDED'))
                {
                    $ecotax = $product['ecotax'] * SCI::getEcotaxTaxRate();
                }
                else
                {
                    $ecotax = 0;
                }
                $return .= ('<cell>'.($product_attribute['price'] * ($taxrate / 100 + 1) + $product['price'] * ($taxrate / 100 + 1) + $ecotax).'</cell>');
                break;
            case 'pprice':
                $ecotax = 0;
                if (_s('CAT_PROD_ECOTAXINCLUDED'))
                {
                    $ecotax = $product['ecotax'] * SCI::getEcotaxTaxRate();
                }
                $return .= ('<cell>'.number_format($product['price'] * ($taxrate / 100 + 1) + $ecotax, 2, '.', '').'</cell>');
                break;
            case 'ppriceextax':
                $return .= ('<cell>'.number_format($product['price'], 6, '.', '').'</cell>');
                break;
            case 'priceextax':
                $return .= ('<cell>'.($product_attribute['price'] + $product['price']).'</cell>');
                break;
            case 'margin':
                $return .= '<cell></cell>';
                break;
            case 'weight':
                $return .= '<cell>'.number_format($product_attribute['weight'] + $product['weight'], 6, '.', '').'</cell>';
                break;
            case 'wholesale_price':
                $return .= '<cell>'.number_format($product_attribute['wholesale_price'], (_s('CAT_PROD_WHOLESALEPRICE4DEC') ? 4 : 2), '.', '').'</cell>';
                break;
            case 'quantity':
                $return .= '<cell>'.SCI::getProductQty((int) $product['id'], (int) $product_attribute['id'], null, (int) $product['id_selected_shop']).'</cell>';
                break;
            case 'supplier_reference':
                $ref_supplier = (string) $product_attribute[$col];
                if (empty($ref_supplier))
                {
                    $sql_supplier = 'SELECT product_supplier_reference
                                        FROM '._DB_PREFIX_.'product_supplier
                                        WHERE id_product = '.(int) $product['id'].'
                                        AND id_product_attribute = '.(int) $product_attribute['id'].'
                                        AND id_supplier = '.(int) $product['id_supplier'];
                    $ref_supplier = Db::getInstance()->getValue($sql_supplier);
                }
                $return .= '<cell><![CDATA['.$ref_supplier.']]></cell>';
                break;
            case 'product_name':
                $return .= '<cell><![CDATA['.$product['name'].']]></cell>';
                break;
            case 'combination_name':
                $name = '';
                $sql_attr = 'SELECT agl.name as gp, al.name
                        FROM '._DB_PREFIX_.'product_attribute_combination pac
                            INNER JOIN '._DB_PREFIX_.'attribute a ON pac.id_attribute = a.id_attribute
                                INNER JOIN '._DB_PREFIX_.'attribute_group_lang agl ON a.id_attribute_group = agl.id_attribute_group
                            INNER JOIN '._DB_PREFIX_."attribute_lang al ON pac.id_attribute = al.id_attribute
                        WHERE pac.id_product_attribute = '".$product_attribute['id']."'
                            AND agl.id_lang = '".$id_lang."'
                            AND al.id_lang = '".$id_lang."'
                        GROUP BY a.id_attribute
                        ORDER BY agl.name";
                $res_attr = Db::getInstance()->executeS($sql_attr);
                $attr_values = array();
                foreach ($res_attr as $attr)
                {
                    if (!empty($attr['gp']) && !empty($attr['name']))
                    {
                        $attr_values[] = substr($attr['name'], 0, 4);
                        if (!empty($name))
                        {
                            $name .= ', ';
                        }
                        $name .= $attr['gp'].' : '.$attr['name'];
                    }
                }
                $return .= '<userdata name="attr_name"><![CDATA['.implode('-', $attr_values).']]></userdata>';
                $return .= '<cell><![CDATA['.$name.']]></cell>';
                break;
            default:
                sc_ext::readCustomCombinationMultiProductGridConfigXML('rowData');
                if (sc_array_key_exists('buildDefaultValue', $colSettings[$col]) && $colSettings[$col]['buildDefaultValue'] != '')
                {
                    if ($colSettings[$col]['buildDefaultValue'] == 'ID')
                    {
                        $return .= '<cell>ID'.$product_attribute['id_product'].'</cell>';
                    }
                }
                elseif (empty($product_attribute[$col]) && !empty($combination[$col]))
                {
                    $return .= '<cell><![CDATA['.$combination[$col].']]></cell>';
                }
                else
                {
                    $return .= '<cell><![CDATA['.$product_attribute[$col].']]></cell>';
                }
        }

        return $return;
    }

    /*
     * PRODUCT SHOP
     */
    if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
    {
        $id_shop = SCI::getSelectedShop();
        $sql = 'SELECT pas.id_product_attribute,pas.id_shop, pa.id_product '.($sc_active ? ' ,pa.sc_active ' : '');
        if (version_compare(_PS_VERSION_, '1.7.5.0', '>='))
        {
            $sql .= ',sa.physical_quantity, sa.reserved_quantity';
        }
        sc_ext::readCustomCombinationMultiProductGridConfigXML('SQLSelectDataSelect');
        $sql .= ' FROM '._DB_PREFIX_.'product_attribute_shop pas
                    INNER JOIN '._DB_PREFIX_.'product_attribute pa ON pas.id_product_attribute = pa.id_product_attribute
                '.((!empty($sc_agent->id_employee)) ? ' INNER JOIN '._DB_PREFIX_."employee_shop es ON (es.id_shop = pas.id_shop AND es.id_employee = '".(int) $sc_agent->id_employee."') " : '').' ';
        if (version_compare(_PS_VERSION_, '1.7.5.0', '>='))
        {
            $sql .= 'LEFT JOIN '._DB_PREFIX_.'stock_available sa ON (sa.id_product_attribute = pas.id_product_attribute AND sa.id_shop = pas.id_shop)';
        }
        sc_ext::readCustomCombinationMultiProductGridConfigXML('SQLSelectDataLeftJoin');
        $sql .= ' WHERE pas.id_shop = '.(int) $id_shop.' 
                AND pa.id_product IN ('.pInSQL($idlist).')
                ORDER BY pa.id_product, pas.id_shop';
        $res = Db::getInstance()->executeS($sql);
        if (!empty($res))
        {
            foreach ($res as $combination)
            {
                if (!empty($combination['id_product']) && !empty($combination['id_shop']) && !empty($combination['id_product_attribute']))
                {
                    $product = new Product($combination['id_product'], false, $id_lang, $combination['id_shop']);
                    $product_attr = new Combination($combination['id_product_attribute'], (version_compare(_PS_VERSION_, '1.6.0.0', '>=') ? $id_lang : null), $combination['id_shop']);
                    $shop = new Shop($combination['id_shop']);
                    $product->id_selected_shop = $combination['id_shop'];

                    $sql = 'SELECT t.rate
                        FROM `'._DB_PREFIX_.'product_shop` p
                        LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (p.`id_tax_rules_group` = tr.`id_tax_rules_group` AND tr.`id_country` = '.(int) SCI::getConfigurationValue('PS_COUNTRY_DEFAULT', null, 0, (int) $combination['id_shop']).' AND tr.`id_state` = 0)
                        LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)
                        WHERE p.id_product='.(int) $combination['id_product'].'
                            AND p.id_shop = '.(int) $combination['id_shop'];
                    $taxrate = Db::getInstance()->getValue($sql);
                    if (!empty($sc_active))
                    {
                        $product_attr->sc_active = $combination['sc_active'];
                    }

                    $xml .= '<row id="'.$combination['id_product'].'_'.$combination['id_product_attribute'].'">';
                    $xml .= '<userdata name="reference_product"><![CDATA['.$product->reference.']]></userdata>';
                    $xml .= '<userdata name="taxrate">'.$taxrate.'</userdata>';
                    $combArray = (array) $product_attr;

                    if (version_compare(_PS_VERSION_, '1.7.5.0', '>='))
                    {
                        $combArray['soft_qty_physical'] = (int) $combination['physical_quantity'];
                        $combArray['soft_qty_reserved'] = (int) $combination['reserved_quantity'];
                    }

                    sc_ext::readCustomCombinationMultiProductGridConfigXML('rowUserData', $combArray);
                    foreach ($cols as $field)
                    {
                        if (!empty($field) && !empty($colSettings[$field]))
                        {
                            $xml .= generateValue($field, $combArray, $product, $taxrate, $combination);
                        }
                    }
                    $xml .= '</row>';
                }
            }
        }
    }
    else
    {
        $sql = 'SELECT pa.id_product_attribute, pa.id_product '.($sc_active ? ' ,pa.sc_active ' : '');
        sc_ext::readCustomCombinationMultiProductGridConfigXML('SQLSelectDataSelect');
        $sql .= ' FROM '._DB_PREFIX_.'product_attribute pa
                '.((!empty($sc_agent->id_employee)) ? ' INNER JOIN '._DB_PREFIX_."employee es ON (es.id_employee = '".(int) $sc_agent->id_employee."') " : '').' ';
        sc_ext::readCustomCombinationMultiProductGridConfigXML('SQLSelectDataLeftJoin');
        $sql .= ' WHERE pa.id_product IN ('.pInSQL($idlist).')
                ORDER BY pa.id_product';
        $res = Db::getInstance()->executeS($sql);
        if (!empty($res))
        {
            foreach ($res as $combination)
            {
                if (!empty($combination['id_product']) && !empty($combination['id_product_attribute']))
                {
                    $product = new Product($combination['id_product'], false, $id_lang);
                    $product_attr = new Combination($combination['id_product_attribute']);

                    $sql = 'SELECT t.rate
                        FROM `'._DB_PREFIX_.'product` p
                        LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (p.`id_tax_rules_group` = tr.`id_tax_rules_group` AND tr.`id_country` = '.(int) SCI::getConfigurationValue('PS_COUNTRY_DEFAULT', null, 0, (int) $combination['id_shop']).' AND tr.`id_state` = 0)
                        LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)
                        WHERE p.id_product='.(int) $combination['id_product'];
                    $taxrate = Db::getInstance()->getValue($sql);

                    if (!empty($sc_active))
                    {
                        $product_attr->sc_active = $combination['sc_active'];
                    }

                    $xml .= '<row id="'.$combination['id_product'].'_'.$combination['id_product_attribute'].'">';
                    $xml .= '<userdata name="reference_product"><![CDATA['.$product->reference.']]></userdata>';
                    $combArray = (array) $product_attr;
                    sc_ext::readCustomCombinationMultiProductGridConfigXML('rowUserData', $combArray);
                    foreach ($cols as $field)
                    {
                        if (!empty($field) && !empty($colSettings[$field]))
                        {
                            $xml .= generateValue($field, $combArray, $product, $taxrate, $combination);
                        }
                    }
                    $xml .= '</row>';
                }
            }
        }
    }

    if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
    {
        $sql = 'SELECT COUNT(pas.id_product_attribute)
            FROM '._DB_PREFIX_.'product_attribute_shop pas
            INNER JOIN '._DB_PREFIX_.'product_attribute pa ON pas.id_product_attribute = pa.id_product_attribute
            WHERE pas.id_shop = '.(int) $id_shop.' 
            AND pa.id_product IN ('.pInSQL($idlist).')
            GROUP BY pas.id_product_attribute';
    }
    else
    {
        $sql = 'SELECT COUNT(pas.id_product_attribute)
            FROM '._DB_PREFIX_.'product_attribute pa 
            WHERE pa.id_product IN ('.pInSQL($idlist).')
            GROUP BY pa.id_product_attribute';
    }
    $nb_combinations = (int) Db::getInstance()->getValue($sql);

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

    echo '<userdata name="uisettings">'.uisettings::getSetting('cat_combinationmultiproduct').'</userdata>'."\n";
    echo '<userdata name="nb_combinations">'.$nb_combinations.'</userdata>'."\n";
    echo '<userdata name="marginMatrix_form">'.$marginMatrix_form[_s('CAT_PROD_GRID_MARGIN_OPERATION')].'</userdata>'."\n";
    sc_ext::readCustomCombinationMultiProductGridConfigXML('gridUserData');

    echo $xml;
?>
</rows>
