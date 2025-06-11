<?php

    $idlist = Tools::getValue('idlist', 0);
    $id_lang = (int) Tools::getValue('id_lang');

    $used = array();

    $multiple = false;
    if (strpos($idlist, ',') !== false)
    {
        $multiple = true;
    }

    $cntProducts = 0;
    if (!empty($idlist))
    {
        $cntProducts = count(explode(',', $idlist));
    }

    $has_combi = false;
    if (!$multiple)
    {
        $exps = explode(',', $idlist);
        foreach ($exps as $id)
        {
            $combis = Product::getProductAttributesIds($id);
            if (count($combis) > 0)
            {
                $has_combi = true;
            }
        }
    }

    $sql = 'SELECT id_currency,iso_code
                    FROM '._DB_PREFIX_.'currency
                    WHERE active=1
                    ORDER BY iso_code';
    $res = Db::getInstance()->ExecuteS($sql);
    $currencies = array();
    $currencies[0] = ' ';
    foreach ($res as $currency)
    {
        $currencies[$currency['id_currency']] = $currency['iso_code'];
    }

    // SETTINGS, FILTERS AND COLONNES
    $sourceGridFormat = SCI::getGridViews('propsupplier');
    $sql_gridFormat = $sourceGridFormat;
    sc_ext::readCustomPropSupplierGridConfigXML('gridConfig');
    $gridFormat = $sourceGridFormat;
    $cols = explode(',', $gridFormat);
    $all_cols = explode(',', $gridFormat);

    $colSettings = array();
    $colSettings = SCI::getGridFields('propsupplier');
    sc_ext::readCustomPropSupplierGridConfigXML('colSettings');

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

        $uiset = uisettings::getSetting('cat_supplier');
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

    function generateValue($col, $row, $used)
    {
        global $colSettings,$id_lang;
        $return = '';
        switch ($col){
            case 'present':
                $return .= '<cell style="background-color:'.((!empty($used[$row['id_supplier']][1])) ? '#'.$used[$row['id_supplier']][1] : '').'"><![CDATA['.$used[$row['id_supplier']][0].']]></cell>';
                break;
            case 'default':
                $return .= '<cell style="background-color:'.((!empty($used[$row['id_supplier']][6])) ? '#'.$used[$row['id_supplier']][6] : '').'"><![CDATA['.$used[$row['id_supplier']][5].']]></cell>';
                break;
            case 'product_supplier_reference':
                $return .= '<cell><![CDATA['.((!empty($used[$row['id_supplier']][2])) ? $used[$row['id_supplier']][2] : '').']]></cell>';
                break;
            case 'product_supplier_price_te':
                $return .= '<cell><![CDATA['.((!empty($used[$row['id_supplier']][3])) ? $used[$row['id_supplier']][3] : '').']]></cell>';
                break;
            case 'id_currency':
                $return .= '<cell><![CDATA['.((!empty($used[$row['id_supplier']][4])) ? $used[$row['id_supplier']][4] : '').']]></cell>';
                break;
            case 'name':
                $return .= '<cell><![CDATA['.$row['name'].']]></cell>';
                break;
            case 'id':
                $return .= '<cell>'.$row['id_supplier'].'</cell>';
                break;
            default:
                $return .= '<cell><![CDATA['.$row[$col].']]></cell>';
                break;
        }

        return $return;
    }

    function getSuppliers()
    {
        global $sql,$id_product_list,$idlist,$multiple,$id_lang,$used, $cntProducts,$has_combi,$cols,$colSettings,$row;

        $id_product_list = $idlist;
        if (empty($id_product_list))
        {
            return false;
        }

        $shop = (int) SCI::getSelectedShop();
        if ($shop == 0)
        {
            $shop = (int) Configuration::get('PS_SHOP_DEFAULT');
        }

        $sql = 'SELECT s.*, sl.`description` ';
        sc_ext::readCustomPropSupplierGridConfigXML('SQLSelectDataSelect');
        $sql .= ' FROM '._DB_PREFIX_.'supplier s ';
        $sql .= '  LEFT JOIN '._DB_PREFIX_.'supplier_lang sl ON (s.`id_supplier` = sl.`id_supplier` AND sl.`id_lang` = '.(int) $id_lang.') ';
        $sql .= '  INNER JOIN '._DB_PREFIX_.'supplier_shop ss ON (s.`id_supplier` = ss.`id_supplier` AND ss.`id_shop` = '.(int) $shop.') ';
        sc_ext::readCustomPropSupplierGridConfigXML('SQLSelectDataLeftJoin');
        $sql .= 'GROUP BY s.id_supplier
             ORDER BY s.`name` ASC';
        $suppliers = Db::getInstance()->executeS($sql);

        if (!$multiple)
        {
            $product = new Product((int) $id_product_list);
            foreach ($suppliers as $supplier)
            {
                $used[$supplier['id_supplier']] = array(0, '', '', '', '', 0, '');

                $sql = '
                    SELECT *
                    FROM `'._DB_PREFIX_.'product_supplier` ps
                    WHERE ps.`id_supplier` = "'.(int) $supplier['id_supplier'].'"
                    AND ps.`id_product` = "'.(int) $id_product_list.'"
                    AND ps.`id_product_attribute` = 0';
                $check_in_supplier = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
                if (!empty($check_in_supplier[0]['id_product_supplier']))
                {
                    $used[$supplier['id_supplier']][0] = 1;

                    $used[$supplier['id_supplier']][2] = $check_in_supplier[0]['product_supplier_reference'];
                    $used[$supplier['id_supplier']][3] = $check_in_supplier[0]['product_supplier_price_te'];
                    $used[$supplier['id_supplier']][4] = $check_in_supplier[0]['id_currency'];

                    if ($product->id_supplier == $supplier['id_supplier'])
                    {
                        $used[$supplier['id_supplier']][5] = 1;
                    }
                }
            }
        }
        else
        {
            foreach ($suppliers as $supplier)
            {
                $used[$supplier['id_supplier']] = array(0, 'DDDDDD', '', '', '');
                $nb_present = 0;
                $nb_default = 0;

                $sql2 = 'SELECT DISTINCT(ps.id_product_supplier), ps.id_product, p.id_supplier
                    FROM '._DB_PREFIX_.'product_supplier ps
                        INNER JOIN '._DB_PREFIX_.'product p ON (p.id_product=ps.id_product)
                    WHERE ps.id_product IN ('.pInSQL($id_product_list).")
                        AND ps.id_supplier = '".(int) $supplier['id_supplier']."'
                        AND ps.id_product_attribute = 0";
                $res2 = Db::getInstance()->ExecuteS($sql2);
                foreach ($res2 as $product)
                {
                    if (!empty($product['id_product']))
                    {
                        ++$nb_present;
                        if (!empty($product['id_supplier']) && $product['id_supplier'] == $supplier['id_supplier'])
                        {
                            ++$nb_default;
                        }
                    }
                }

                if ($nb_present == $cntProducts)
                {
                    $used[$supplier['id_supplier']][0] = 1;
                    $used[$supplier['id_supplier']][1] = '7777AA';
                }
                elseif ($nb_present < $cntProducts && $nb_present > 0)
                {
                    $used[$supplier['id_supplier']][1] = '777777';
                }

                if ($nb_default == $cntProducts)
                {
                    $used[$supplier['id_supplier']][5] = 1;
                    $used[$supplier['id_supplier']][6] = '7777AA';
                }
                elseif ($nb_default < $cntProducts && $nb_default > 0)
                {
                    $used[$supplier['id_supplier']][6] = '777777';
                }
            }
        }
        $xml = '';
        foreach ($suppliers as $row)
        {
            $xml .= '<row id="'.$row['id_supplier'].'">';
            sc_ext::readCustomPropSupplierGridConfigXML('rowUserData');
            sc_ext::readCustomPropSupplierGridConfigXML('rowData');
            foreach ($cols as $field)
            {
                if (!empty($field) && !empty($colSettings[$field]))
                {
                    $xml .= generateValue($field, $row, $used);
                }
            }
            $xml .= '</row>';
        }

        return $xml;
    }

    $xml = getSuppliers();

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

    echo '<userdata name="uisettings">'.uisettings::getSetting('cat_supplier').'</userdata>'."\n";
    sc_ext::readCustomPropSupplierGridConfigXML('gridUserData');

    echo $xml;
?>
</rows>