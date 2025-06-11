<?php

    $id_lang = (int) Tools::getValue('id_lang');
    $idlist = Tools::getValue('idlist', 0);
    $view = Tools::getValue('view', 'grid_feedbiz_option');

    $colSettings = array();
    $grids = array();
    // SETTINGS, FILTERS AND COLONNES
    include 'cat_feedbiz_data_fields.php';
    include 'cat_feedbiz_data_views.php';
    $cols = explode(',', $grids[$view]);

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
    global $cols,$colSettings,$view;

    $uiset = uisettings::getSetting('cat_feedbiz_'.$view);
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

    function getFeedBizOptions()
    {
        global $idlist,$cols,$colSettings,$view,$id_lang;

        $sql = 'SELECT p.id_product, IFNULL(pa.id_product_attribute, 0) as id_product_attribute
                FROM `'._DB_PREFIX_.'product` p
                LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa ON pa.id_product = p.id_product
                WHERE p.id_product IN ('.pInSQL($idlist).')';
        $initial_products = Db::getInstance()->executeS($sql);

        $init_cache = array();
        foreach ($initial_products as $prd)
        {
            $init_cache[$prd['id_product']][$prd['id_product_attribute']] = $prd['id_product_attribute'];
        }
        foreach ($init_cache as $id_product => $row)
        {
            if (!array_key_exists(0, $row))
            {
                $init_cache[$id_product][0] = 0;
            }
            sort($init_cache[$id_product]);
        }

        $initial_products = array();
        foreach ($init_cache as $id_product => $row)
        {
            foreach ($row as $k => $id_product_attribute)
            {
                $initial_products[] = array(
                    'id_product' => (int) $id_product,
                    'id_product_attribute' => (int) $id_product_attribute,
                );
            }
        }

        if (version_compare(_PS_VERSION_, '1.7.7.6', '>='))
        {
            $_POST['setShopContext'] = 's-'.SCI::getSelectedShop();
            $context = Context::getContext();
            $context->currency = Currency::getCurrencyInstance((int) SCI::getConfigurationValue('PS_CURRENCY_DEFAULT'));
        }

        $xml = '';
        switch ($view) {
            case 'grid_feedbiz_option':
                ## Clean Table
                Db::getInstance()->execute('DELETE
                                            FROM `'._DB_PREFIX_.'feedbiz_product_option`
                                            WHERE `force` IS NULL
                                            AND `disable` IS NULL
                                            AND `price` IS NULL
                                            AND `shipping` IS NULL
                                            AND `text` IS NULL');
                $sql_prd_options = 'SELECT *
                                    FROM `'._DB_PREFIX_.'feedbiz_product_option`
                                    WHERE id_product IN ('.pInSQL($idlist).')';
                $prd_options = Db::getInstance()->executeS($sql_prd_options);
                $opt_cache = array();
                foreach ($prd_options as $option)
                {
                    $opt_cache[$option['id_product']][$option['id_product_attribute']][$option['id_lang']] = $option;
                }
                foreach ($initial_products as $res)
                {
                    $iso_langs = Language::getIsoIds();
                    foreach ($iso_langs as $lang)
                    {
                        $lang_id = (int) $lang['id_lang'];
                        $id_product = $res['id_product'];
                        $id_product_attribute = $res['id_product_attribute'];
                        $xml .= '<row id="'.$id_product.'_'.(int) $id_product_attribute.'_'.(int) $lang_id.'">';
                        $prod = new Product((int) $id_product, null, (int) $lang_id);
                        $prices = SCI::getPrice((int) $id_product, (int) $id_product_attribute, SCI::getSelectedShop(), true);
                        foreach ($cols as $col)
                        {
                            switch ($col) {
                                case 'id_product':
                                    $xml .= '<cell>'.$id_product.'</cell>';
                                    break;
                                case 'id_product_attribute':
                                    $xml .= '<cell>'.$id_product_attribute.'</cell>';
                                    break;
                                case 'name':
                                    $xml .= '<cell><![CDATA['.$prod->name.']]></cell>';
                                    break;
                                case 'disable':
                                    $xml .= '<cell>'.(int) $opt_cache[(int) $id_product][(int) $id_product_attribute][(int) $lang_id][$col].'</cell>';
                                    break;
                                case 'language_iso':
                                    $xml .= '<cell>'.strtoupper($lang_id).'</cell>';
                                    break;
                                case 'price_inc_tax':
                                    $xml .= '<cell>'.(float) $prices['price_reduction_it'].'</cell>';
                                    break;
                                case 'attribute_name':
                                    if (!empty($id_product_attribute))
                                    {
                                        if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                                        {
                                            $attributes = $prod->getAttributesResume((int) $lang_id);
                                            foreach ($attributes as $attr)
                                            {
                                                if ($attr['id_product_attribute'] == $id_product_attribute)
                                                {
                                                    $combination_detail = $attr['attribute_designation'];
                                                    break;
                                                }
                                            }
                                        }
                                        else
                                        {
                                            $detail = array();
                                            $attributes = SCI::getAttributeCombinations($prod, (int) $lang_id);
                                            foreach ($attributes as $attr)
                                            {
                                                if ($attr['id_product_attribute'] == $id_product_attribute)
                                                {
                                                    $detail[] = $attr['group_name'].' : '.$attr['attribute_name'];
                                                }
                                            }
                                            $combination_detail = implode(', ', $detail);
                                        }
                                    }
                                    else
                                    {
                                        $combination_detail = '';
                                    }
                                    $xml .= '<cell><![CDATA['.$combination_detail.']]></cell>';
                                    break;
                                default:
                                    $xml .= '<cell><![CDATA['.$opt_cache[(int) $id_product][(int) $id_product_attribute][(int) $lang_id][$col].']]></cell>';
                            }
                        }
                        $xml .= '</row>';
                    }
                }
                break;
            case 'grid_feedbiz_amazon_option':
                ## Clean Table
                Db::getInstance()->execute('DELETE
                                            FROM `'._DB_PREFIX_.'feedbiz_amazon_options`
                                            WHERE `force` IS NULL
                                            AND `nopexport` IS NULL
                                            AND `noqexport` IS NULL
                                            AND `fba` IS NULL
                                            AND `fba_value` IS NULL
                                            AND `latency` IS NULL
                                            AND `disable` IS NULL
                                            AND `price` IS NULL
                                            AND `asin1` IS NULL
                                            AND `asin2` IS NULL
                                            AND `asin3` IS NULL
                                            AND `text` IS NULL
                                            AND `bullet_point1` IS NULL
                                            AND `bullet_point2` IS NULL
                                            AND `bullet_point3` IS NULL
                                            AND `bullet_point4` IS NULL
                                            AND `bullet_point5` IS NULL
                                            AND `shipping` IS NULL
                                            AND `shipping_type` IS NULL
                                            AND `gift_wrap` IS NULL
                                            AND `gift_message` IS NULL
                                            AND `browsenode` IS NULL
                                            AND `repricing_min` IS NULL
                                            AND `repricing_max` IS NULL
                                            AND `repricing_gap` IS NULL
                                            AND `shipping_group` IS NULL');
                $module_feedbiz = Module::getInstanceByName('feedbiz');
                $amazon_regions = $module_feedbiz::$amazon_regions;
                $market_place = unserialize(Configuration::get('FEEDBIZ_MARKETPLACE_TAB'));
                $amazon_long_region = explode(';', $market_place['amazon']);
                foreach ($amazon_regions as $long_region => $iso)
                {
                    if (!in_array($long_region, $amazon_long_region))
                    {
                        unset($amazon_regions[$long_region]);
                    }
                }

                $sql_prd_options = 'SELECT *
                                    FROM `'._DB_PREFIX_.'feedbiz_amazon_options`
                                    WHERE id_product IN ('.pInSQL($idlist).')';
                $prd_options = Db::getInstance()->executeS($sql_prd_options);
                $opt_cache = array();
                foreach ($prd_options as $option)
                {
                    $opt_cache[$option['id_product']][$option['id_product_attribute']][$option['region']] = $option;
                }
                foreach ($initial_products as $res)
                {
                    foreach ($amazon_regions as $region)
                    {
                        $id_product = $res['id_product'];
                        $id_product_attribute = $res['id_product_attribute'];
                        $xml .= '<row id="'.$id_product.'_'.(int) $id_product_attribute.'_'.$region.'">';
                        $prod = new Product((int) $id_product, null, (int) $id_lang);
                        $prices = SCI::getPrice((int) $id_product, (int) $id_product_attribute, SCI::getSelectedShop(), true);
                        foreach ($cols as $col)
                        {
                            switch ($col) {
                                case 'id_product':
                                    $xml .= '<cell>'.$id_product.'</cell>';
                                    break;
                                case 'id_product_attribute':
                                    $xml .= '<cell>'.$id_product_attribute.'</cell>';
                                    break;
                                case 'name':
                                    $xml .= '<cell><![CDATA['.$prod->name.']]></cell>';
                                    break;
                                case 'nopexport':case 'noqexport':case 'fba':case 'shipping_type':case 'disable':
                                    $xml .= '<cell>'.(int) $opt_cache[(int) $id_product][(int) $id_product_attribute][$region][$col].'</cell>';
                                    break;
                                case 'region':
                                    $xml .= '<cell>'.$region.'</cell>';
                                    break;
                                case 'price_inc_tax':
                                    $xml .= '<cell>'.(float) $prices['price_reduction_it'].'</cell>';
                                    break;
                                case 'attribute_name':
                                    $combination_detail = '';
                                    if (!empty($id_product_attribute))
                                    {
                                        if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                                        {
                                            $attributes = $prod->getAttributesResume((int) $id_lang);
                                            foreach ($attributes as $attr)
                                            {
                                                if ($attr['id_product_attribute'] == $id_product_attribute)
                                                {
                                                    $combination_detail = $attr['attribute_designation'];
                                                    break;
                                                }
                                            }
                                        }
                                        else
                                        {
                                            $detail = array();
                                            $attributes = SCI::getAttributeCombinations($prod, (int) $id_lang);
                                            foreach ($attributes as $attr)
                                            {
                                                if ($attr['id_product_attribute'] == $id_product_attribute)
                                                {
                                                    $detail[] = $attr['group_name'].' : '.$attr['attribute_name'];
                                                }
                                            }
                                            $combination_detail = implode(', ', $detail);
                                        }
                                    }
                                    $xml .= '<cell><![CDATA['.$combination_detail.']]></cell>';
                                    break;
                                default:
                                    $xml .= '<cell><![CDATA['.$opt_cache[(int) $id_product][(int) $id_product_attribute][$region][$col].']]></cell>';
                            }
                        }
                        $xml .= '</row>';
                    }
                }
                break;
            case 'grid_feedbiz_cdiscount_option':
                ## Clean Table
                Db::getInstance()->execute('DELETE
                                            FROM `'._DB_PREFIX_.'feedbiz_cdiscount_options`
                                            WHERE `force` IS NULL
                                            AND `disable` IS NULL
                                            AND `price` IS NULL
                                            AND `price_up` IS NULL
                                            AND `price_down` IS NULL
                                            AND `shipping` IS NULL
                                            AND `shipping_delay` IS NULL
                                            AND `clogistique` IS NULL
                                            AND `valueadded` IS NULL
                                            AND `text` IS NULL ');
                $module_feedbiz = Module::getInstanceByName('feedbiz');
                $cdiscount_regions = $module_feedbiz::$cdiscount_regions;

                $sql_prd_options = 'SELECT *
                                    FROM `'._DB_PREFIX_.'feedbiz_cdiscount_options`
                                    WHERE id_product IN ('.pInSQL($idlist).')';
                $prd_options = Db::getInstance()->executeS($sql_prd_options);
                $opt_cache = array();
                foreach ($prd_options as $option)
                {
                    $opt_cache[$option['id_product']][$option['id_product_attribute']][$option['region']] = $option;
                }
                foreach ($initial_products as $res)
                {
                    foreach ($cdiscount_regions as $region)
                    {
                        $id_product = $res['id_product'];
                        $id_product_attribute = $res['id_product_attribute'];
                        $xml .= '<row id="'.$id_product.'_'.(int) $id_product_attribute.'_'.$region.'">';
                        $prod = new Product((int) $id_product, null, (int) $id_lang);
                        $prices = SCI::getPrice((int) $id_product, (int) $id_product_attribute, SCI::getSelectedShop(), true);
                        foreach ($cols as $col)
                        {
                            switch ($col) {
                                case 'id_product':
                                    $xml .= '<cell>'.$id_product.'</cell>';
                                    break;
                                case 'id_product_attribute':
                                    $xml .= '<cell>'.$id_product_attribute.'</cell>';
                                    break;
                                case 'name':
                                    $xml .= '<cell><![CDATA['.$prod->name.']]></cell>';
                                    break;
                                case 'disable':
                                    $xml .= '<cell>'.(int) $opt_cache[(int) $id_product][(int) $id_product_attribute][$region][$col].'</cell>';
                                    break;
                                case 'region':
                                    $xml .= '<cell>'.$region.'</cell>';
                                    break;
                                case 'price_inc_tax':
                                    $xml .= '<cell>'.(float) $prices['price_reduction_it'].'</cell>';
                                    break;
                                case 'attribute_name':
                                    $combination_detail = '';
                                    if (!empty($id_product_attribute))
                                    {
                                        if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                                        {
                                            $attributes = $prod->getAttributesResume((int) $id_lang);
                                            foreach ($attributes as $attr)
                                            {
                                                if ($attr['id_product_attribute'] == $id_product_attribute)
                                                {
                                                    $combination_detail = $attr['attribute_designation'];
                                                    break;
                                                }
                                            }
                                        }
                                        else
                                        {
                                            $detail = array();
                                            $attributes = SCI::getAttributeCombinations($prod, (int) $id_lang);
                                            foreach ($attributes as $attr)
                                            {
                                                if ($attr['id_product_attribute'] == $id_product_attribute)
                                                {
                                                    $detail[] = $attr['group_name'].' : '.$attr['attribute_name'];
                                                }
                                            }
                                            $combination_detail = implode(', ', $detail);
                                        }
                                    }
                                    $xml .= '<cell><![CDATA['.$combination_detail.']]></cell>';
                                    break;
                                default:
                                    $xml .= '<cell><![CDATA['.$opt_cache[(int) $id_product][(int) $id_product_attribute][$region][$col].']]></cell>';
                            }
                        }
                        $xml .= '</row>';
                    }
                }
                break;
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

    echo '<userdata name="uisettings">'.uisettings::getSetting('cat_feedbiz').'</userdata>'."\n";
    sc_ext::readCustomPropSpePriceGridConfigXML('gridUserData');

    echo getFeedBizOptions();
?>
</rows>
