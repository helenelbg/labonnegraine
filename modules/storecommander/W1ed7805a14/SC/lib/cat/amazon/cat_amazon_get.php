<?php

    $id_lang = (int) Tools::getValue('id_lang');
    $idlist = Tools::getValue('idlist', 0);

    $colSettings = array();
    $grids = array();
    // SETTINGS, FILTERS AND COLONNES

    ## check alternative fields enable
    $alternate_fields = array('alternative_title', 'alternative_description');
    $query = Db::getInstance()->ExecuteS('SHOW COLUMNS
                                                FROM '._DB_PREFIX_.'marketplace_product_option
                                                WHERE Field IN ("'.implode('","', $alternate_fields).'")');
    $alternate_fields_enable = false;
    if (!empty($query) && (count($query) == count($alternate_fields)))
    {
        $alternate_fields_enable = true;
    }

    include 'cat_amazon_data_fields.php';
    include 'cat_amazon_data_views.php';
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

    function getAmazonConfig($config_name)
    {
        $data = Db::getInstance()->getValue('SELECT value
                                                    FROM '._DB_PREFIX_.'amazon_configuration
                                                    WHERE name = "'.(string) $config_name.'"
                                                    AND id_shop ='.(int) SCI::getSelectedShop());
        if (!empty($data))
        {
            return unserialize($data);
        }
        else
        {
            $data = Db::getInstance()->getValue('SELECT value
                                                    FROM '._DB_PREFIX_.'amazon_configuration
                                                    WHERE name = "'.(string) $config_name.'"
                                                    AND id_shop IS NULL');
            if (!empty($data))
            {
                return unserialize($data);
            }
            else
            {
                $data = Db::getInstance()->getValue('SELECT value
                                                        FROM '._DB_PREFIX_.'amazon_configuration
                                                        WHERE name = "'.(string) $config_name.'"
                                                        AND id_shop = 0');
                if (!empty($data))
                {
                    return unserialize($data);
                }
                else
                {
                    if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                    {
                        $data = Configuration::get((string) $config_name, null, null, (int) SCI::getSelectedShop());
                    }
                    else
                    {
                        $data = Configuration::get((string) $config_name);
                    }
                    if (!empty($data))
                    {
                        if (base64_encode(base64_decode($data, true)) === $data)
                        {
                            $data = base64_decode($data, true);
                        }

                        return unserialize($data);
                    }
                }
            }
        }

        return null;
    }

function getColSettingsAsXML()
{
    global $cols,$colSettings;

    $uiset = uisettings::getSetting('cat_amazon');
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

    function getAmazonMatrix($id_lang, $price_rule, $currency, $price)
    {
        $price_rule_return = null;
        if (array_key_exists($id_lang, $price_rule))
        {
            foreach ($price_rule[$id_lang]['rule']['from'] as $k => $step)
            {
                $cle = (int) $k;
                if ($price >= $step && $price <= $price_rule[$id_lang]['rule']['to'][$cle])
                {
                    $type = $price_rule[$id_lang]['type'];
                    if (!empty($price_rule[$id_lang]['rule'][$type][$cle]))
                    {
                        $value = $price_rule[$id_lang]['rule'][$type][$cle];

                        $reduc = null;
                        if (substr($value, 0, 1) == '-')
                        {
                            $reduc = 1;
                        }
                        if ($type == 'percent')
                        {
                            if (!empty($reduc))
                            {
                                $value = str_replace('-', '', $value);
                                $percent = (($value / 100) + 1);
                                $price = $price / $percent;
                                $price_rule_return = $value.'%';
                                break;
                            }
                            else
                            {
                                $percent = (($value / 100) + 1);
                                $price = $price * $percent;
                                $price_rule_return = '+'.$value.'%';
                                break;
                            }
                        }
                        else
                        {
                            if (!empty($reduc))
                            {
                                $value = str_replace('-', '', $value);
                                $price = $price - $value;
                                $price_rule_return = '-'.$value.$currency['sign'];
                                break;
                            }
                            else
                            {
                                $price = $price + $value;
                                $price_rule_return = '+'.$value.$currency['sign'];
                                break;
                            }
                        }
                    }
                }
            }
        }

        $price_data = array(
            'price' => $price,
            'type_price_rule' => $price_rule_return,
        );

        return $price_data;
    }

    function getAmazonOptions()
    {
        global $idlist,$cols,$id_lang,$alternate_fields_enable;

        $current_selected_shop = (int) SCI::getSelectedShop();

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

        $xml = '';
        ## Clean Table
        Db::getInstance()->execute('DELETE
                                    FROM `'._DB_PREFIX_.'marketplace_product_option`
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
                                    AND `repricing_gap` IS NULL'
                                    .(!empty($alternate_fields_enable) ? ' AND `alternative_title` IS NULL AND `alternative_description` IS NULL' : ''));

        $sql_prd_options = 'SELECT *, IFNULL(disable,-1) as disable
                            FROM `'._DB_PREFIX_.'marketplace_product_option`
                            WHERE id_product IN ('.pInSQL($idlist).')
                            AND id_shop ='.$current_selected_shop;
        $prd_options = Db::getInstance()->executeS($sql_prd_options);
        $opt_cache = array();
        if (!empty($prd_options))
        {
            foreach ($prd_options as $option)
            {
                $opt_cache[(int) $option['id_product']][(int) $option['id_product_attribute']][(int) $option['id_lang']] = $option;
            }
        }

        $amazon_selected_categories = unserialize(Db::getInstance()->getValue('SELECT value FROM '._DB_PREFIX_.'amazon_configuration WHERE id_shop = '.(int) $current_selected_shop.' AND name = "AMAZON_CATEGORIES"'));
        if (empty($amazon_selected_categories))
        {
            $amazon_selected_categories = unserialize(Db::getInstance()->getValue('SELECT value FROM '._DB_PREFIX_.'amazon_configuration WHERE id_shop IS NULL AND name = "AMAZON_CATEGORIES"'));
            if (empty($amazon_selected_categories))
            {
                $amazon_selected_categories = unserialize(Db::getInstance()->getValue('SELECT value FROM '._DB_PREFIX_.'amazon_configuration WHERE id_shop = 0 AND name = "AMAZON_CATEGORIES"'));
                if (empty($amazon_selected_categories))
                {
                    $amazon_selected_categories = unserialize(base64_decode(Db::getInstance()->getValue('SELECT value FROM '._DB_PREFIX_.'marketplace_configuration WHERE marketplace = "amazon" AND configuration = "categories"')));
                }
            }
        }
        $sql = 'SELECT p.id_product
                FROM `'._DB_PREFIX_.'product` p
                LEFT JOIN `'._DB_PREFIX_.'category_product` c ON (c.`id_product` = p.`id_product`)
                WHERE p.`active` = 1
                AND p.`reference` > ""
                AND (p.`ean13` > "" OR p.`upc` > "")
                AND c.id_category IN ('.pInSQL(implode(',', $amazon_selected_categories)).')
                AND p.id_product IN ('.pInSQL($idlist).')';
        $amazon_available_product = Db::getInstance()->ExecuteS($sql);
        $cache_available_amazon = array();
        if (!empty($amazon_available_product))
        {
            foreach ($amazon_available_product as $row)
            {
                $cache_available_amazon[] = (int) $row['id_product'];
            }
        }

        $regions = getAmazonConfig('AMAZON_REGION');
        $price_rule = getAmazonConfig('AMAZON_PRICE_RULE');
        $currencies = getAmazonConfig('AMAZON_CURRENCY');

        $market_place_action_cache = array();
        $sql = 'SELECT GROUP_CONCAT(CONCAT(id_product,"_",IFNULL(id_product_attribute,0),"_",id_lang) SEPARATOR ",")
                FROM '._DB_PREFIX_.'marketplace_product_action
                WHERE marketplace="amazon"
                AND id_product IN ('.pInSQL($idlist).')
                AND id_shop ='.$current_selected_shop;
        $market_place_action = Db::getInstance()->getValue($sql);
        if (!empty($market_place_action))
        {
            $market_place_action_cache = explode(',', $market_place_action);
        }
        foreach ($initial_products as $res)
        {
            foreach ($regions as $region_lang_id => $region)
            {
                $id_product = (int) $res['id_product'];
                $id_product_attribute = (int) $res['id_product_attribute'];
                $region_lang_id = (int) $region_lang_id;
                $xml .= '<row id="'.$id_product.'_'.$id_product_attribute.'_'.$region_lang_id.'">';
                $prod = new Product((int) $id_product, null, (int) $region_lang_id, $current_selected_shop);
                $id_currency = Currency::getIdByIsoCode($currencies[$region_lang_id], (version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? $current_selected_shop : null));
                $currency = Currency::getCurrency($id_currency);
                if (version_compare(_PS_VERSION_, '1.7.0.0', '>='))
                {
                    $_POST['setShopContext'] = 's-'.$current_selected_shop;
                    $context = Context::getContext();
                    $context->currency = Currency::getCurrencyInstance((int) $id_currency);
                }
                $price_init = SCI::getPrice((int) $id_product, (int) $id_product_attribute, (version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? $current_selected_shop : 1), true);
                $price_detail = getAmazonMatrix($region_lang_id, $price_rule, $currency, $price_init['price_reduction_it']);

                foreach ($cols as $col)
                {
                    switch ($col) {
                        case 'region':
                            $xml .= '<cell>'.$region.'</cell>';
                            break;
                        case 'id_product':
                            $xml .= '<cell>'.$id_product.'</cell>';
                            break;
                        case 'send_to_creation':
                            $key = $id_product.'_'.$id_product_attribute.'_'.$region_lang_id;
                            if (in_array($key, $market_place_action_cache))
                            {
                                $xml .= '<cell><![CDATA['._l('Yes').']]></cell>';
                            }
                            else
                            {
                                $xml .= '<cell><![CDATA['._l('No').']]></cell>';
                            }
                            break;
                        case 'disable':
                            switch (true) {
                                case isset($opt_cache[$id_product][$id_product_attribute][$region_lang_id][$col]) && $opt_cache[$id_product][$id_product_attribute][$region_lang_id][$col] >= 0:
                                    $tv = (int) $opt_cache[$id_product][$id_product_attribute][$region_lang_id][$col];
                                    break;
                                default:
                                    if (in_array($id_product, $cache_available_amazon))
                                    {
                                        $tv = 0;
                                    }
                                    else
                                    {
                                        $tv = -1;
                                    }
                            }
                            $xml .= '<cell>'.$tv.'</cell>';
                            break;
                        case 'id_product_attribute':
                            $xml .= '<cell>'.$id_product_attribute.'</cell>';
                            break;
                        case 'name':
                            $xml .= '<cell><![CDATA['.$prod->name.']]></cell>';
                            break;
                        case 'price_inc_tax':
                            $xml .= '<cell>'.(float) $price_init['price_reduction_it'].'</cell>';
                            break;
                        case 'nopexport':
                        case 'noqexport':
                        case 'fba':
                        case 'shipping_type':
                        case 'force':
                            $xml .= '<cell>'.(isset($opt_cache[$id_product][$id_product_attribute][$region_lang_id][$col]) ? $opt_cache[$id_product][$id_product_attribute][$region_lang_id][$col] : '').'</cell>';
                            break;
                        case 'amazon_price':
                            $amazon_price = (!empty($price_detail['price']) ? $price_detail['price'] : $price_init);
                            $xml .= '<cell>'.Tools::ps_round($amazon_price, 2).'</cell>';
                            break;
                        case 'price_rule':
                            $amazon_price_rule = (!empty($price_detail['type_price_rule']) ? $price_detail['type_price_rule'] : '');
                            $xml .= '<cell>'.$amazon_price_rule.'</cell>';
                            break;
                        case 'attribute_name':
                            $combination_detail = '';
                            if (!empty($id_product_attribute))
                            {
                                if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                                {
                                    $attributes = $prod->getAttributesResume((int) $region_lang_id);
                                    foreach ($attributes as $attr)
                                    {
                                        if ((int) $attr['id_product_attribute'] == $id_product_attribute)
                                        {
                                            $combination_detail = $attr['attribute_designation'];
                                            break;
                                        }
                                    }
                                }
                                else
                                {
                                    $detail = array();
                                    $attributes = SCI::getAttributeCombinations($prod, (int) $region_lang_id);
                                    foreach ($attributes as $attr)
                                    {
                                        if ((int) $attr['id_product_attribute'] == $id_product_attribute)
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
                            $xml .= '<cell><![CDATA['.(isset($opt_cache[$id_product][$id_product_attribute][$region_lang_id][$col]) ? $opt_cache[$id_product][$id_product_attribute][$region_lang_id][$col] : '').']]></cell>';
                    }
                }
                $xml .= '</row>';
            }
        }

        return $xml;
    }

    $xml = getAmazonOptions();

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

    echo '<userdata name="uisettings">'.uisettings::getSetting('cat_amazon').'</userdata>'."\n";
    sc_ext::readCustomPropSpePriceGridConfigXML('gridUserData');

    echo $xml;
?>
</rows>
