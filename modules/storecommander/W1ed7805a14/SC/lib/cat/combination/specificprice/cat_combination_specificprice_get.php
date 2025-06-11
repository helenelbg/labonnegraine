<?php

    $id_lang = (int) Tools::getValue('id_lang', 0);
    $id_product_attribute = (Tools::getValue('id_product_attribute', 0));

    function getRowsFromDB()
    {
        global $id_lang,$id_product_attribute;

        $sql = '
        SELECT *
        FROM '._DB_PREFIX_.'specific_price
        WHERE id_product_attribute IN ('.pInSQL($id_product_attribute).')'
        .(version_compare(_PS_VERSION_, '1.5.0.2', '>=') ? ' AND id_cart = 0 ' : '').
        'ORDER BY from_quantity';
        $res = Db::getInstance()->ExecuteS($sql);

        $selectedShop = SCI::getSelectedShop();
        $ecotaxTaxRate = SCI::getEcotaxTaxRate();
        $tax = array(0 => 0);
        $inner = '';
        if (version_compare(_PS_VERSION_, '1.6.0.0', '>=') && SCMS && $selectedShop > 0)
        {
            $inner = ' INNER JOIN '._DB_PREFIX_.'tax_rules_group_shop trgs ON (trgs.id_tax_rules_group = trg.id_tax_rules_group AND trgs.id_shop = "'.(int) $selectedShop.'")';
        }
        $sql = 'SELECT trg.name, trg.id_tax_rules_group,t.rate'.(version_compare(_PS_VERSION_, '1.6.0.10', '>=') ? ',trg.deleted' : '').'
        FROM `'._DB_PREFIX_.'tax_rules_group` trg
        LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (trg.`id_tax_rules_group` = tr.`id_tax_rules_group` AND tr.`id_country` = '.(int) SCI::getDefaultCountryId().' AND tr.`id_state` = 0)
            LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)
        '.$inner.'
        WHERE trg.active=1'
            .(version_compare(_PS_VERSION_, '1.6.0.10', '>=') ? ' ORDER BY trg.deleted ASC, trg.name ASC' : '');
        $taxRes = Db::getInstance()->ExecuteS($sql);
        if (!empty($taxRes))
        {
            foreach ($taxRes as $row)
            {
                if ($row['name'] == '')
                {
                    $row['name'] = ' ';
                }

                if (version_compare(_PS_VERSION_, '1.6.0.10', '>=') && $row['deleted'] == '1')
                {
                    $row['name'] .= ' '._l('(deleted)');
                }

                $tax[$row['id_tax_rules_group']] = $row['rate'];
            }
        }

        $disabled_fields_by_id_speprice = array();
        $xml = '';
        foreach ($res as $specific_price)
        {
            if ($specific_price['price'] > -1)
            {
                $disabled_fields_by_id_speprice[] = $specific_price['id_specific_price'];
            }

            $product_attribute_obj = null;
            if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
            {
                $product_obj = new Product((int) $specific_price['id_product'], false, (int) $id_lang, (int) $selectedShop);
            }
            else
            {
                $product_obj = new Product((int) $specific_price['id_product'], (int) $id_lang);
            }

            if (version_compare(_PS_VERSION_, '1.5.0.0', '>=') && !empty($specific_price['id_product_attribute']))
            {
                if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                {
                    $product_attribute_obj = new Combination((int) $specific_price['id_product_attribute'], (int) $id_lang, (int) $selectedShop);
                }
                else
                {
                    $product_attribute_obj = new Combination((int) $specific_price['id_product_attribute'], (int) $id_lang);
                }
            }
            if (empty($specific_price['id_product_attribute']))
            {
                $specific_price['product_taxrate'] = (array_key_exists((int) $product_obj->id_tax_rules_group, $tax) ? $tax[(int) $product_obj->id_tax_rules_group] : 0);
                $specific_price['product_ecotax'] = (_s('CAT_PROD_ECOTAXINCLUDED') ? $product_obj->ecotax * $ecotaxTaxRate : 0);
            }
            else
            {
                $specific_price['product_taxrate'] = (array_key_exists((int) $product_obj->id_tax_rules_group, $tax) ? $tax[(int) $product_obj->id_tax_rules_group] : 0);
                $specific_price['product_ecotax'] = (_s('CAT_PROD_ECOTAXINCLUDED') ? $product_attribute_obj->ecotax * $ecotaxTaxRate : 0);
            }

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
            $xml .= "<row id='".$specific_price['id_specific_price']."'>";
            $xml .= '<cell style="color:#999999">'.$specific_price['id_specific_price'].'</cell>';
            $xml .= '<cell>'.$specific_price['id_product_attribute'].'</cell>';
            if (SCMS)
            {
                $xml .= '<cell>'.$specific_price['id_shop'].'</cell>';
                $xml .= '<cell>'.$specific_price['id_shop_group'].'</cell>';
            }
            $xml .= '<cell>'.$specific_price['id_group'].'</cell>';
            $xml .= '<cell>'.$specific_price['from_quantity'].'</cell>';
            $xml .= '<cell>'.($specific_price['price'] != -1 || version_compare(_PS_VERSION_, '1.5.0.0', '<') ? number_format($specific_price['price'], 2) : '-1').'</cell>';
            $xml .= '<cell>'.getPriceField($specific_price, $tax, $product_obj, $product_attribute_obj, 'price_exl_tax').'</cell>';
            $xml .= '<cell>'.getPriceField($specific_price, $tax, $product_obj, $product_attribute_obj, 'price_inc_tax').'</cell>';
            $xml .= '<cell>'.($specific_price['reduction_type'] == 'percentage' ? (number_format($specific_price['reduction'] * 100, 2)).'%' : number_format($specific_price['reduction'], 2)).'</cell>';
            if (version_compare(_PS_VERSION_, '1.6.0.11', '>='))
            {
                $xml .= '<cell>'.$specific_price['reduction_tax'].'</cell>';
            }
            else
            {
                $xml .= '<cell type="ro"><![CDATA['._l('Incl. tax').']]></cell>';
            }
            $xml .= '<cell>'.getPriceField($specific_price, $tax, $product_obj, $product_attribute_obj, 'price_with_reduction_tax_excl').'</cell>';
            $xml .= '<cell>'.getPriceField($specific_price, $tax, $product_obj, $product_attribute_obj, 'price_with_reduction_tax_incl').'</cell>';
            $xml .= '<cell>'.$specific_price['from'].'</cell>';
            $xml .= '<cell>'.$specific_price['to'].'</cell>';
            $xml .= '<cell>'.$specific_price['id_country'].'</cell>';
            $xml .= '<cell>'.$specific_price['id_currency'].'</cell>';
            if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
            {
                if ($specific_price['id_customer'] > 0)
                {
                    $sql = 'SELECT firstname, lastname FROM '._DB_PREFIX_.'customer WHERE id_customer = '.(int) $specific_price['id_customer'];
                    $customer = Db::getInstance()->getRow($sql);
                    $xml .= '<cell><![CDATA['.$customer['firstname'].' '.$customer['lastname'].']]></cell>';
                }
                else
                {
                    $xml .= '<cell><![CDATA['._l('All').']]></cell>';
                }
            }
            $xml .= '</row>';
        }

        $userdata_supp = '<userdata name="disabled_fields_by_id_speprice">'.implode(',', $disabled_fields_by_id_speprice).'</userdata>'."\n";

        return $userdata_supp.$xml;
    }

    function getPriceField($row, $tax, $product_obj, $product_attribute_obj = null, $field = null)
    {
        $taxrate = $row['product_taxrate'];
        $ecotax = $row['product_ecotax'];
        $priveValue = 0;
        switch ($field){
            case 'price_exl_tax':
            case 'price_with_reduction_tax_excl':
                if (empty($row['id_product_attribute']))
                {
                    $price_tax_excl = $product_obj->price;
                }
                else
                {
                    $price_tax_excl = $product_attribute_obj->price + $product_obj->price;
                }

                ## with reduction
                if ($field == 'price_with_reduction_tax_excl')
                {
                    if ($row['price'] > -1)
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
                $priveValue = number_format($price_tax_excl, 6, '.', '');
                break;
            case 'price_inc_tax':
                if (empty($row['id_product_attribute']))
                {
                    $price_tax_incl = $product_obj->price * ($taxrate / 100 + 1) + $ecotax;
                }
                elseif (!empty($taxrate))
                {
                    $price_tax_incl = $product_attribute_obj->price * ($taxrate / 100 + 1) + $product_obj->price * ($taxrate / 100 + 1) + $ecotax;
                }
                else
                {
                    $price_tax_incl = $product_attribute_obj->price + $product_obj->price + $ecotax;
                }

                $priveValue = number_format($price_tax_incl, 6, '.', '');
                break;
            case 'price_with_reduction_tax_incl':
                if ($row['price'] > 1)
                {
                    $p_price = $pa_price = $row['price'];
                }
                else
                {
                    $p_price = $product_obj->price;
                    $pa_price = $product_attribute_obj->price + $p_price;
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
                    $price_tax_incl = $p_price * ($tax[(int) $product_obj->id_tax_rules_group] / 100 + 1) + $ecotax;
                }
                elseif (!empty($taxrate))
                {
                    $price_tax_incl = $pa_price * ($taxrate / 100 + 1) + $ecotax;
                }
                else
                {
                    $price_tax_incl = $pa_price + $ecotax;
                }

                if (!$reduction_tax_excl)
                {
                    $price_tax_incl -= ($row['reduction_type'] == 'amount' ? $row['reduction'] : $price_tax_incl * $row['reduction']);
                }
                $priveValue = number_format($price_tax_incl, 6, '.', '');
                break;
        }

        return $priveValue;
    }
    if (SCMS)
    {
        $sql = 'SELECT *
                        FROM '._DB_PREFIX_.'shop
                        ORDER BY name';
        $res = Db::getInstance()->ExecuteS($sql);
        $shops = '';
        foreach ($res as $shop)
        {
            $shop['name'] = str_replace('&', _l('and'), $shop['name']);
            $shops .= '<option value="'.$shop['id_shop'].'">'.$shop['name'].'</option>';
        }

        $sql = 'SELECT *
                        FROM '._DB_PREFIX_.'shop_group
                        ORDER BY name';
        $res = Db::getInstance()->ExecuteS($sql);
        $group_shops = '';
        foreach ($res as $group)
        {
            $group_shops .= '<option value="'.$group['id_shop_group'].'">'.$group['name'].'</option>';
        }
    }

    $sql = 'SELECT *
                    FROM '._DB_PREFIX_.'group_lang
                    WHERE id_lang='.(int) $id_lang.'
                    ORDER BY id_group';
    $res = Db::getInstance()->ExecuteS($sql);
    $groups = '';
    foreach ($res as $group)
    {
        $group['name'] = str_replace('&', _l('and'), $group['name']);
        $groups .= '<option value="'.$group['id_group'].'">'.$group['name'].'</option>';
    }

    $sql = 'SELECT cl.id_country,cl.name
                    FROM '._DB_PREFIX_.'country_lang cl
                    LEFT JOIN '._DB_PREFIX_.'country c ON (c.id_country=cl.id_country)
                    WHERE cl.id_lang='.(int) $id_lang.' AND c.active=1
                    ORDER BY cl.name';
    $res = Db::getInstance()->ExecuteS($sql);
    $countries = '';
    foreach ($res as $country)
    {
        $country['name'] = str_replace('&', _l('and'), $country['name']);
        $countries .= '<option value="'.$country['id_country'].'">'.$country['name'].'</option>';
    }

    $sql = 'SELECT id_currency,iso_code
                    FROM '._DB_PREFIX_.'currency
                    WHERE active=1
                    ORDER BY iso_code';
    $res = Db::getInstance()->ExecuteS($sql);
    $currencies = '';
    foreach ($res as $currency)
    {
        $currencies .= '<option value="'.$currency['id_currency'].'">'.$currency['iso_code'].'</option>';
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

    $xml = '';
    if (!empty($id_product_attribute))
    {
        $xml = getRowsFromDB();
    }
?>
<rows id="0">
<head>
<beforeInit>
<call command="attachHeader"><param><![CDATA[#text_filter,#text_filter<?php if (SCMS){ ?>,#select_filter,#select_filter<?php } ?>,#select_filter,#text_filter,#text_filter,#numeric_filter,#numeric_filter,#text_filter,#select_filter,#numeric_filter,#numeric_filter,#text_filter,#text_filter,#select_filter,#select_filter,#text_filter]]></param></call>
</beforeInit>
<column id="id_specific_price" width="40" type="ro" align="right" sort="int"><?php echo _l('ID'); ?></column>
<column id="id_product_attribute" width="40" type="ro" align="right" sort="int"><?php echo _l('id_product_attr'); ?></column>
<?php if (SCMS){ ?>
<column id="id_shop" width="50" type="coro" align="right" sort="int"><?php echo _l('Shop'); ?><option value="0"><?php echo _l('All'); ?></option><?php echo $shops; ?></column>
<column id="id_shop_group" width="50" type="coro" align="right" sort="int"><?php echo _l('Shop group'); ?><option value="0"><?php echo _l('All'); ?></option><?php echo $group_shops; ?></column>
<?php } ?>
<column id="id_group" width="50" type="coro" align="right" sort="int"><?php echo _l('Customer group'); ?><option value="0"><?php echo _l('All'); ?></option><?php echo $groups; ?></column>
<column id="from_quantity" width="50" type="ed" align="right" sort="int"><?php echo _l('Minimum quantity'); ?></column>
<column id="price" width="50" type="ed" align="right" sort="int"><?php echo _l('Fixed price excl. Tax'); ?></column>
<column id="price_exl_tax" width="65" type="ro" align="right" sort="int"><?php echo _l('Price excl. Tax'); ?></column>
<column id="price_inc_tax" width="65" type="ro" align="right" sort="int"><?php echo _l('Price incl. Tax'); ?></column>
<column id="reduction" width="65" type="ed" align="right" sort="int"><?php echo _l('Reduction'); ?></column>
<column id="reduction_tax" width="70" type="coro" align="right" sort="int"><?php echo _l('Reduction tax'); ?>
    <option value="0"><?php echo _l('Excl. tax'); ?></option>
    <option value="1"><?php echo _l('Incl. tax'); ?></option>
</column>
<column id="price_with_reduction_tax_excl" width="65" type="edn" align="left" sort="int" format="0.000000"><?php echo _l('Price tax excl after reduction'); ?></column>
<column id="price_with_reduction_tax_incl" width="65" type="edn" align="left" sort="int" format="0.000000"><?php echo _l('Price tax incl after reduction'); ?></column>
<column id="from" width="90" type="dhxCalendarA" align="left" sort="date"><?php echo _l('Reduction from'); ?></column>
<column id="to" width="90" type="dhxCalendarA" align="left" sort="date"><?php echo _l('Reduction to'); ?></column>
<column id="id_country" width="50" type="coro" align="right" sort="int"><?php echo _l('Country'); ?><option value="0"><?php echo _l('All'); ?></option><?php echo $countries; ?></column>
<column id="id_currency" width="50" type="coro" align="right" sort="int"><?php echo _l('Currency'); ?><option value="0"><?php echo _l('All'); ?></option><?php echo $currencies; ?></column>
<?php if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) { ?>
<column id="id_customer" width="100" type="combo" align="left" sort="str" source="index.php?ajax=1&amp;act=cat_specificprice_customer_get&amp;ajaxCall=1" auto="true" cache="false"><?php echo _l('Customer'); ?></column>
<?php } ?>
<afterInit>
<call command="enableMultiselect"><param>1</param></call>
</afterInit>
</head>
<?php

    echo '<userdata name="uisettings">'.uisettings::getSetting('cat_combination_specificprice').'</userdata>'."\n";
    echo $xml;
?>
</rows>
