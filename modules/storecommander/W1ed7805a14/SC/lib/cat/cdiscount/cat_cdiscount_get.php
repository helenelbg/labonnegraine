<?php

    $id_lang = (int) Tools::getValue('id_lang');
    $idlist = Tools::getValue('idlist', 0);

    $colSettings = array();
    $grids = array();
    // SETTINGS, FILTERS AND COLONNES
    include 'cat_cdiscount_data_fields.php';
    include 'cat_cdiscount_data_views.php';
    $cols = explode(',', $grids);

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

        $uiset = uisettings::getSetting('cat_cdiscount');
        $tmp = explode('|', $uiset);
        $tmp = explode('-', $tmp[2]);
        $sizes = array();
        foreach ($tmp as $v)
        {
            $s = explode(':', $v);
            $sizes[$s[0]] = $s[1];
        }
        $tmp = explode('|', $uiset);
        $tmp = explode('-', $tmp[0]);
        $hidden = array();
        foreach ($tmp as $v)
        {
            $s = explode(':', $v);
            $hidden[$s[0]] = $s[1];
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
            if (!empty($colSettings[$col]['options']))
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

    function getCdiscountOptions()
    {
        global $idlist,$cols,$id_lang;

        $sql = 'SELECT id_product
                FROM `'._DB_PREFIX_.'product` p
                WHERE id_product IN ('.pInSQL($idlist).')';
        $initial_products = Db::getInstance()->executeS($sql);

        $xml = '';
        ## Clean Table
        Db::getInstance()->execute('DELETE 
                                    FROM `'._DB_PREFIX_.'cdiscount_product_option` 
                                    WHERE `force` = 0 
                                    AND `disable` IS NULL 
                                    AND `price` IS NULL 
                                    AND `price_up` IS NULL 
                                    AND `price_down` IS NULL 
                                    AND `shipping` IS NULL 
                                    AND `shipping_delay` IS NULL 
                                    AND `clogistique` = 0 
                                    AND `valueadded` IS NULL 
                                    AND `text` IS NULL');

        $sql_prd_options = 'SELECT * 
                            FROM `'._DB_PREFIX_.'cdiscount_product_option`
                            WHERE id_product IN ('.pInSQL($idlist).')';
        $prd_options = Db::getInstance()->executeS($sql_prd_options);
        $opt_cache = array();
        foreach ($prd_options as $option)
        {
            $opt_cache[$option['id_product']] = $option;
        }
        foreach ($initial_products as $res)
        {
            $id_product = $res['id_product'];
            $xml .= '<row id="'.$id_product.'">';
            $prod = new Product((int) $id_product, null, (int) $id_lang, (version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? SCI::getSelectedShop() : null));
            $prices = SCI::getPrice((int) $id_product, null, (version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? SCI::getSelectedShop() : 1), true);
            foreach ($cols as $col)
            {
                switch ($col) {
                    case 'id_product':
                        $xml .= '<cell>'.$id_product.'</cell>';
                        break;
                    case 'disable':
                        $xml .= '<cell>'.(int) $opt_cache[(int) $id_product][$col].'</cell>';
                        break;
                    case 'name':
                        $xml .= '<cell><![CDATA['.$prod->name.']]></cell>';
                        break;
                    case 'price_inc_tax':
                            $xml .= '<cell>'.(float) $prices['price_reduction_it'].'</cell>';
                            break;
                    default:
                        $xml .= '<cell><![CDATA['.$opt_cache[(int) $id_product][$col].']]></cell>';
                }
            }
            $xml .= '</row>';
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
    echo '<rows><head>';
    echo getColSettingsAsXML();
    echo '<afterInit><call command="attachHeader"><param>'.getFilterColSettings().'</param></call>
            <call command="attachFooter"><param><![CDATA['.getFooterColSettings().']]></param></call></afterInit>';
    echo '</head>'."\n";

    echo '<userdata name="uisettings">'.uisettings::getSetting('cat_cdiscount').'</userdata>'."\n";
    sc_ext::readCustomPropSpePriceGridConfigXML('gridUserData');

    echo getCdiscountOptions();
?>
</rows>
