<?php

    if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
    {
        $shop_id = SCI::getSelectedShop();
        $shop = new Shop($shop_id);
        $shop_group = $shop->getGroup();
    }

    $id_lang = (int) Tools::getValue('id_lang');
    $view = Tools::getValue('view', 'grid_light');
    $filters = Tools::getValue('filters', '');
    $filter_params = Tools::getValue('filter_params', '');
    $group = '';
    $filters_exp = explode(',', $filters);
    foreach ($filters_exp as $filter_exp)
    {
        if (strpos($filter_exp, 'gr') !== false)
        {
            $id = str_replace('gr', '', $filter_exp);
            if (is_numeric($id))
            {
                if (!empty($group))
                {
                    $group .= ',';
                }
                $group .= $id;
            }
        }
    }

    $groupFilter = explode(',', $group);

    foreach ($groupFilter as $k => $s)
    {
        if ($s == 'groups')
        {
            unset($groupFilter[$k]);
        }
    }
    if (isset($groupFilter[0]) && $groupFilter[0] == '')
    {
        unset($groupFilter[0]);
    }

    $segment = null;
    $id_segment = 0;
    $id_segment_get = Tools::getValue('id_segment', 0);
    if (!empty($id_segment_get))
    {
        if (substr($id_segment_get, 0, 4) == 'seg_' && SCSG)
        {
            $id_segment = (int) str_replace('seg_', '', $id_segment_get);
            $segment = new ScSegment($id_segment);
        }
    }

    $grids = SCI::getGridViews('customer');
    sc_ext::readCustomCustomersGridsConfigXML('gridConfig');

    $cols = explode(',', $grids[$view]);
    // Groupes
    $arrGroupes = array();
    if (sc_in_array('id_default_group', $cols, 'cusGet_cols') || sc_in_array('groups', $cols, 'cusGet_cols'))
    {
        $arrGroupes[0] = '-';
        $inner = '';
        if (SCMS && SCI::getSelectedShop() > 0)
        {
            $inner = ' INNER JOIN '._DB_PREFIX_."group_shop gs ON (gs.id_group = g.id_group AND gs.id_shop = '".(int) SCI::getSelectedShop()."') ";
        }

        $sql = 'SELECT g.id_group, gl.name
                FROM `'._DB_PREFIX_.'group` g
                    INNER JOIN `'._DB_PREFIX_."group_lang` gl ON (gl.id_group = g.id_group AND gl.id_lang = '".(int) $id_lang."')
                    ".$inner.'
                ORDER BY gl.name';
        $res = Db::getInstance()->ExecuteS($sql);
        foreach ($res as $row)
        {
            if ($row['name'] == '')
            {
                $row['name'] = ' ';
            }
            $arrGroupes[$row['id_group']] = $row['name'];
        }
    }

    // Genders
    $arrGenders = array();
    $arrGenders[0] = _l('Unk.');
    if (sc_in_array('id_gender', $cols, 'cusGet_cols'))
    {
        if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
        {
            $sql = 'SELECT g.id_gender, gl.name
                    FROM '._DB_PREFIX_.'gender g
                        INNER JOIN '._DB_PREFIX_."gender_lang gl ON (gl.id_gender = g.id_gender AND gl.id_lang = '".(int) $id_lang."')
                    ORDER BY gl.name";
            $res = Db::getInstance()->ExecuteS($sql);
            foreach ($res as $row)
            {
                if ($row['name'] == '')
                {
                    $row['name'] = ' ';
                }
                $arrGenders[$row['id_gender']] = $row['name'];
            }
        }
        else
        {
            $arrGenders[1] = _l('Mr.');
            $arrGenders[2] = _l('Ms.');
            $arrGenders[3] = _l('Miss');
            $arrGenders[4] = _l('Unk.');
            $arrGenders[9] = _l('Unk.');
        }
    }

    // Country
    $arrCountrys = array();
    if (sc_in_array('id_country', $cols, 'cusGet_cols'))
    {
        $inner = '';
        if (SCMS && SCI::getSelectedShop() > 0)
        {
            $inner = ' INNER JOIN '._DB_PREFIX_."country_shop gs ON (gs.id_country = g.id_country AND gs.id_shop = '".(int) SCI::getSelectedShop()."') ";
        }

        $sql = 'SELECT g.id_country, gl.name
                FROM '._DB_PREFIX_.'country g
                    INNER JOIN '._DB_PREFIX_."country_lang gl ON (gl.id_country = g.id_country AND gl.id_lang = '".(int) $id_lang."')
                    ".$inner.'
                ORDER BY gl.name';
        $res = Db::getInstance()->ExecuteS($sql);
        foreach ($res as $row)
        {
            if ($row['name'] == '')
            {
                $row['name'] = ' ';
            }
            $arrCountrys[$row['id_country']] = $row['name'];
        }
    }

    // State
    $arrStates = array();
    if (sc_in_array('id_state', $cols, 'cusGet_cols'))
    {
        $arrStates[0] = '-';
        $inner = '';
        if (SCMS && SCI::getSelectedShop() > 0)
        {
            $inner = ' INNER JOIN '._DB_PREFIX_."country_shop cs ON (cs.id_country = g.id_country AND cs.id_shop = '".(int) SCI::getSelectedShop()."') ";
        }

        $sql = 'SELECT g.id_state, g.name, g.id_country
                FROM '._DB_PREFIX_.'state g
                    INNER JOIN '._DB_PREFIX_."country_lang cl ON (cl.id_country = g.id_country AND cl.id_lang = '".(int) $id_lang."')
                    ".$inner.'
                ORDER BY  cl.name ASC, g.name ASC';
        $res = Db::getInstance()->ExecuteS($sql);
        foreach ($res as $row)
        {
            if ($row['name'] == '')
            {
                $row['name'] = ' ';
            }
            $arrStates[$row['id_state']] = $arrCountrys[$row['id_country']].' - '.$row['name'];
        }
    }

    // LANGS
    $languages = Language::getLanguages(true);
    $language_arr = array();
    foreach ($languages as $language)
    {
        $language_arr[$language['id_lang']] = $language['name'];
    }

    $colSettings = array();
    $colSettings = SCI::getGridFields('customer');
    sc_ext::readCustomCustomersGridsConfigXML('colSettings');

    function getColSettingsAsXML()
    {
        global $cols,$colSettings,$view;

        $uiset = uisettings::getSetting('cus_grid_'.$view);
        if ($uiset)
        {
            $tmp = explode('|', $uiset);
            if (isset($tmp[2]))
            {
                $tmp = explode('-', $tmp[2]);
                $sizes = array();
                foreach ($tmp as $v)
                {
                    if (!empty($v))
                    {
                        $s = explode(':', $v);
                        $sizes[$s[0]] = $s[1];
                    }
                }
            }
            $tmp = explode('|', $uiset);
            if (isset($tmp[0]))
            {
                $tmp = explode('-', $tmp[0]);
                $hidden = array();
                foreach ($tmp as $v)
                {
                    if (!empty($v))
                    {
                        $s = explode(':', $v);
                        $hidden[$s[0]] = $s[1];
                    }
                }
            }
        }

        $xml = '';
        foreach ($cols as $id => $col)
        {
            if (!sc_array_key_exists($col, $colSettings))
            {
                continue;
            }
            $xml .= '<column id="'.$col.'"'.(sc_array_key_exists('format', $colSettings[$col]) ?
                    ' format="'.$colSettings[$col]['format'].'"' : '').
                    ' width="'.(sc_array_key_exists($col, $sizes) ? $sizes[$col] : ($view == 'grid_combination_price' && $col == 'id' ? $colSettings[$col]['width'] + 50 : $colSettings[$col]['width'])).'"'.
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

    function getCustomers($segment_object = null)
    {
        global $sql,$id_segment,$groupFilter,$filter_params,$group,$sc_agent,$arrGroupes,$language_arr,$id_lang,$cols,$view,$colSettings,$user_lang_iso,$fields_address,$fields_customer,$col,$gridrow;

        if (!empty($segment_object))
        {
            $segment = $segment_object;
        }

        $fields_customer = array('id_shop', 'id_customer', 'id_gender', 'firstname', 'lastname', 'email', 'active', 'newsletter', 'optin', 'date_add', 'birthday', 'id_default_group', 'note');
        if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
        {
            $fields_customer[] = 'company';
            $fields_customer[] = 'siret';
            $fields_customer[] = 'ape';
            $fields_customer[] = 'website';
        }
        if (version_compare(_PS_VERSION_, '1.5.4.0', '>='))
        {
            $fields_customer[] = 'id_lang';
        }
        if ($view == 'grid_address' || sc_in_array('id_address', $cols, 'cusGet_cols'))
        {
            $fields_address = array('id_address', 'alias', 'id_customer', 'company', 'lastname', 'firstname', 'address1', 'address2', 'postcode', 'city', 'id_state', 'id_country', 'other', 'phone', 'phone_mobile', 'dni', 'vat_number');
            sc_ext::readCustomCustomersGridsConfigXML('updateSettings');
            $blacklistfields = array();
            $sqlAddress = '';
            foreach ($cols as $col)
            {
                if (sc_in_array($col, $fields_address, 'cusGet_fields_address'))
                {
                    $sqlAddress .= ',a.`'.bqSQL($col).'`';
                }
                elseif (sc_in_array($col, $fields_customer, 'cusGet_fields_customer'))
                {
                    $sqlAddress .= ',c.`'.bqSQL($col).'`';
                }
            }
            $sqlAddress = trim($sqlAddress, ',');

            $sql = 'SELECT a.id_address,a.dni,'.psql($sqlAddress).(version_compare(_PS_VERSION_, '1.5.0.0', '<') ? ',0 as id_shop' : ',c.id_shop');
            sc_ext::readCustomCustomersGridsConfigXML('SQLSelectDataSelect');
            $sql .= ' FROM '._DB_PREFIX_.'address a ';
            if (SCMS && SCI::getSelectedShop() > 0)
            {
                $sql .= ' INNER JOIN '._DB_PREFIX_."customer c ON (a.id_customer = c.id_customer AND c.id_shop = '".(int) SCI::getSelectedShop()."') ";
            }
            else
            {
                $sql .= ' INNER JOIN '._DB_PREFIX_.'customer c ON (a.id_customer = c.id_customer) ';
            }
            sc_ext::readCustomCustomersGridsConfigXML('SQLSelectDataLeftJoin');
            $sql .= ' WHERE a.active = 1 
                        AND a.deleted = 0 
                        AND a.id_customer != 0';
            if (!empty($group))
            {
                $sql .= ' AND (
                        c.id_default_group IN ('.pInSQL($group).')
                        OR
                        c.id_customer IN (SELECT cg.id_customer FROM '._DB_PREFIX_.'customer_group cg WHERE cg.id_customer = c.id_customer AND cg.id_group IN ('.pInSQL($group).'))
                    ) ';
            }
            if (!empty($id_segment) && SCSG)
            {
                if ($segment->type == 'manual')
                {
                    $sql .= ' AND a.id_customer IN (SELECT id_element FROM '._DB_PREFIX_."sc_segment_element WHERE type_element='customer' AND id_segment=" .(int) $id_segment . ")";
                }
                elseif ($segment->type == 'auto')
                {
                    $params = array('id_lang' => $id_lang, 'id_segment' => $id_segment, 'access' => 'customers');
                    for ($i = 1; $i <= 15; ++$i)
                    {
                        $param = Tools::getValue('segment_params_'.$i);
                        if (!empty($param))
                        {
                            $params['segment_params_'.$i] = $param;
                        }
                    }
                    if (SCMS)
                    {
                        $params['id_shop'] = (int) SCI::getSelectedShop();
                    }
                    $params['is_customer'] = '0';
                    $sql .= SegmentHook::hookByIdSegment('segmentAutoSqlQuery', $segment, $params);
                }
            }
            if (!empty($filter_params))
            {
                $filters = explode(',', $filter_params);
                foreach ($filters as $filter)
                {
                    list($field, $search) = explode('|||', $filter);
                    if (!empty($field) && !empty($search) && sc_in_array($field, $cols, 'cusGet_cols'))
                    {
                        if (sc_in_array($field, array('id_customer', 'id_order'), 'cusGet_searchfields'))
                        {
                            $sql .= ' AND ( a.`'.bqSQL($field)."` LIKE '".pSQL($search)."%' ) ";
                        }
                        elseif (in_array($field, $fields_address))
                        {
                            $sql .= ' AND ( a.`'.bqSQL($field)."` LIKE '%".pSQL($search)."%' ) ";
                        }
                        else
                        {
                            $sql .= ' AND ( c.`'.bqSQL($field)."` LIKE '%".pSQL($search)."%' ) ";
                        }
                    }
                }
            }
            $sql .= ' ORDER BY a.id_customer DESC, a.id_address ASC
                     LIMIT '.(int) _s('CUS_MAX_CUSTOMERS');
        }
        else
        {
            sc_ext::readCustomCustomersGridsConfigXML('updateSettings');
            $blacklistfields = array();
            $sqlCustomer = array('c.`id_customer`');

            if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
            {
                $sqlCustomer[] = 'c.`id_shop`';
            }

            foreach ($cols as $col)
            {
                if (sc_in_array($col, $fields_customer, 'cusGet_fields_customer'))
                {
                    $sqlCustomer[] = 'c.`'.bqSQL($col).'`';
                }
            }

            $sql = 'SELECT '.psql(implode(',', $sqlCustomer));
            sc_ext::readCustomCustomersGridsConfigXML('SQLSelectDataSelect');
            $sql .= ' FROM '._DB_PREFIX_.'customer c ';
            sc_ext::readCustomCustomersGridsConfigXML('SQLSelectDataLeftJoin');
            $sql .= ' WHERE 1 ';
            if (_s('CUS_DISPLAY_DELETED') != 1)
            {
                $sql .= ' AND c.deleted=0 ';
            }
            if (SCMS && SCI::getSelectedShop() > 0)
            {
                $sql .= " AND c.id_shop = '".(int) SCI::getSelectedShop()."' ";
            }
            if (!empty($group))
            {
                $sql .= ' AND (
                            c.id_default_group IN ('.pInSQL($group).')
                            OR
                            c.id_customer IN (SELECT cg.id_customer FROM '._DB_PREFIX_.'customer_group cg WHERE cg.id_customer = c.id_customer AND cg.id_group IN ('.pInSQL($group).'))
                        ) ';
            }
            if (!empty($id_segment) && SCSG)
            {
                if ($segment->type == 'manual')
                {
                    $sql .= ' AND c.id_customer IN (SELECT id_element FROM '._DB_PREFIX_."sc_segment_element WHERE type_element='customer' AND id_segment=" .(int) $id_segment . ")";
                }
                elseif ($segment->type == 'auto')
                {
                    $params = array('id_lang' => $id_lang, 'id_segment' => $id_segment, 'access' => 'customers');
                    for ($i = 1; $i <= 15; ++$i)
                    {
                        $param = Tools::getValue('segment_params_'.$i);
                        if (!empty($param))
                        {
                            $params['segment_params_'.$i] = $param;
                        }
                    }
                    if (SCMS)
                    {
                        $params['id_shop'] = (int) SCI::getSelectedShop();
                    }
                    $params['is_customer'] = '1';
                    $sql .= SegmentHook::hookByIdSegment('segmentAutoSqlQuery', $segment, $params);
                }
            }
            if (!empty($filter_params))
            {
                $filters = explode(',', $filter_params);
                foreach ($filters as $filter)
                {
                    list($field, $search) = explode('|||', $filter);
                    if (!empty($field) && !empty($search) && sc_in_array($field, $cols, 'cusGet_fields_cols'))
                    {
                        if (sc_in_array($field, array('id_customer', 'id_order'), 'cusGet_searchfields'))
                        {
                            $sql .= ' AND ( c.`'.bqSQL($field)."` LIKE '".pSQL($search)."%' ) ";
                        }
                        else
                        {
                            $sql .= ' AND ( c.`'.bqSQL($field)."` LIKE '%".pSQL($search)."%' ) ";
                        }
                    }
                }
            }
            $sql .= ' ORDER BY c.id_customer DESC
                     LIMIT '.(int) _s('CUS_MAX_CUSTOMERS');
        }
        global $dd;
        $dd = $sql;
        //echo "\n\n\n\n".$sql."\n\n\n\n";die();
        $res = Db::getInstance()->ExecuteS($sql);

        $languages = Language::getLanguages(true);
        $language_arr = array();
        foreach ($languages as $language)
        {
            $language_arr[$language['id_lang']] = $language['name'];
        }

        if (SCMS)
        {
            $tmp_shops = Db::getInstance()->executeS('SELECT id_shop,name FROM '._DB_PREFIX_.'shop');
            $shops = array();
            foreach ($tmp_shops as $shop)
            {
                $shops[$shop['id_shop']] = $shop['name'];
            }
        }

        foreach ($res as $gridrow)
        {
            $color = '';
            $valid_orders = '0';
            $valid_orders_sql = Db::getInstance()->executeS('
                    SELECT id_order
                    FROM '._DB_PREFIX_.'orders
                    WHERE `id_customer` = '.(int) $gridrow['id_customer'].'
                        AND valid="1"');
            if (!empty($valid_orders_sql))
            {
                $valid_orders = count($valid_orders_sql);
            }
            if ($view == 'grid_address' || sc_in_array('id_address', $cols, 'cusGet_fields_cols'))
            {
                echo '<row id="'.$gridrow['id_address'].'">';
                echo '  <userdata name="id_customer">'.(int) $gridrow['id_customer'].'</userdata>';
            }
            else
            {
                echo '<row id="'.$gridrow['id_customer'].'">';
            }
            echo '  <userdata name="id_shop_customer">'.(int) $gridrow['id_shop'].'</userdata>';
//            echo         "<userdata name=\"id_specific_price\">".(int) $user_data["id_specific_price"]."</userdata>";
            sc_ext::readCustomCustomersGridsConfigXML('rowUserData', $gridrow);
            foreach ($cols as $key => $col)
            {
                switch ($col){
                    case 'id_address':
                        echo '<cell>'.$gridrow['id_address'].'</cell>';
                        break;
                    case 'id_customer':
                        echo '<cell>'.$gridrow['id_customer'].'</cell>';
                        break;
                    case 'firstname':
                        echo '<cell '.((!empty($valid_orders)) ? 'style="background-color:#95ca82"' : '').'><![CDATA['.$gridrow[$col].']]></cell>';
                        break;
                    case 'lastname':
                        echo '<cell '.((!empty($valid_orders)) ? 'style="background-color:#95ca82"' : '').'><![CDATA['.$gridrow[$col].']]></cell>';
                        break;
                    case 'note':
                        echo '<cell><![CDATA['.str_replace("\n", ' ', $gridrow['note']).']]></cell>';
                        break;
                    case 'invoice':
                        $invoice = _l('No');
                        $invoice_sql = Db::getInstance()->executeS('
                            SELECT o.id_order
                            FROM '._DB_PREFIX_.'orders o
                            WHERE o.valid = 1 AND o.`id_address_invoice` = '.(int) $gridrow['id_address']);
                        if (!empty($invoice_sql) && count($invoice_sql) > 0)
                        {
                            $invoice = _l('Yes');
                        }
                        echo '<cell><![CDATA['.$invoice.']]></cell>';
                        break;
                    case 'delivery':
                        $delivery = _l('No');
                        $delivery_sql = Db::getInstance()->executeS('
                            SELECT o.id_order
                            FROM '._DB_PREFIX_.'orders o
                            WHERE o.valid = 1 AND o.`id_address_delivery` = '.(int) $gridrow['id_address']);
                        if (!empty($delivery_sql) && count($delivery_sql) > 0)
                        {
                            $delivery = _l('Yes');
                        }
                        echo '<cell><![CDATA['.$delivery.']]></cell>';
                        break;

                    case 'birthday':
                        $birthday = '';
                        if (!empty($gridrow['birthday']) && $gridrow['birthday'] != '0000-00-00')
                        {
                            $birthday = $gridrow['birthday'];
                        }
                        echo '<cell><![CDATA['.$birthday.']]></cell>';
                        break;
                    case 'discount_codes':
                        $discount_codes = '';

                        if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                        {
                            $discounts = SCI::getCustomerCartRules((int) $id_lang, $gridrow['id_customer'], true, true);
                            if (!empty($discounts))
                            {
                                foreach ($discounts as $discount)
                                {
                                    if (!empty($discount['code']))
                                    {
                                        if (!empty($discount_codes))
                                        {
                                            $discount_codes .= ',';
                                        }
                                        $discount_codes .= $discount['code'];
                                    }
                                }
                            }
                        }
                        else
                        {
                            $discounts = Discount::getCustomerDiscounts((int) $id_lang, $gridrow['id_customer'], true, true);
                            if (!empty($discounts))
                            {
                                foreach ($discounts as $discount)
                                {
                                    if (!empty($discount['name']))
                                    {
                                        if (!empty($discount_codes))
                                        {
                                            $discount_codes .= ',';
                                        }
                                        $discount_codes .= $discount['name'];
                                    }
                                }
                            }
                        }
                        echo '<cell><![CDATA['.$discount_codes.']]></cell>';
                        break;
                    case 'nb_cart_product':
                        $nb_cart_product = '0';

                        $carts = Cart::getCustomerCarts($gridrow['id_customer'], true);
                        if (!empty($carts[0]['id_cart']))
                        {
                            $is_ordered = Db::getInstance()->executeS('
                            SELECT id_cart
                            FROM '._DB_PREFIX_.'orders
                            WHERE `id_cart` = '.(int) $carts[0]['id_cart']);
                            if (empty($is_ordered))
                            {
                                $nb_cart_product_sql = Db::getInstance()->executeS('
                                SELECT cp.id_product, cp.quantity
                                FROM '._DB_PREFIX_.'cart_product cp
                                WHERE cp.`id_cart` = '.(int) $carts[0]['id_cart']);
                                if (!empty($nb_cart_product_sql))
                                {
                                    foreach ($nb_cart_product_sql as $product_cart)
                                    {
                                        $nb_cart_product += $product_cart['quantity'];
                                    }
                                }
                            }
                        }
                        echo '<cell><![CDATA['.$nb_cart_product.']]></cell>';
                        break;
                    case 'total_cart_product':
                        $total_cart_product = '0';

                        $carts = Cart::getCustomerCarts($gridrow['id_customer'], true);
                        if (!empty($carts[0]['id_cart']))
                        {
                            $is_ordered = Db::getInstance()->executeS('
                            SELECT id_cart
                            FROM '._DB_PREFIX_.'orders
                            WHERE `id_cart` = '.(int) $carts[0]['id_cart']);
                            if (empty($is_ordered))
                            {
                                $total_cart_product_sql = Db::getInstance()->executeS('
                                SELECT cp.id_product, cp.quantity
                                FROM '._DB_PREFIX_.'cart_product cp
                                WHERE cp.`id_cart` = '.(int) $carts[0]['id_cart']);
                                if (!empty($total_cart_product_sql))
                                {
                                    foreach ($total_cart_product_sql as $product_cart)
                                    {
                                        $product = new Product($product_cart['id_product'], false, null, (int) SCI::getSelectedShop());
                                        $temp_price = $product_cart['quantity'] * $product->getPrice(true);
                                        $total_cart_product += $temp_price;
                                    }
                                }
                            }
                        }
                        echo '<cell><![CDATA['.number_format($total_cart_product, 2, '.', '').']]></cell>';
                        break;
                    case 'last_delivery_address':
                        $last_delivery_address = '';
                        $last_delivery_address_color = '';

                        $last_delivery_address_sql = Db::getInstance()->executeS('
                        SELECT o.id_address_delivery, o.id_address_invoice, a.*, cl.*
                        FROM '._DB_PREFIX_.'orders o
                            INNER JOIN '._DB_PREFIX_.'address a ON (o.id_address_delivery = a.id_address)
                                INNER JOIN '._DB_PREFIX_.'country_lang cl ON (a.id_country = cl.id_country AND cl.id_lang = "'.(int) $id_lang.'")
                        WHERE o.`id_customer` = '.(int) $gridrow['id_customer'].'
                            AND o.valid="1"
                        ORDER BY o.invoice_date DESC
                        LIMIT 1');
                        if (!empty($last_delivery_address_sql[0]))
                        {
                            $num = '';
                            if (!empty($last_delivery_address_sql[0]['phone']))
                            {
                                $num = $last_delivery_address_sql[0]['phone'];
                            }
                            if (!empty($last_delivery_address_sql[0]['phone_mobile']))
                            {
                                if (!empty($num))
                                {
                                    $num = $num.' / ';
                                }
                                $num = $last_delivery_address_sql[0]['phone_mobile'];
                            }
                            if (!empty($num))
                            {
                                $num = ' (<strong>'.$num.'</strong>)';
                            }

                            $last_delivery_address = $last_delivery_address_sql[0]['address1']
                                                    .' '.$last_delivery_address_sql[0]['address2']
                                                    .' '.$last_delivery_address_sql[0]['postcode']
                                                    .' '.$last_delivery_address_sql[0]['city']
                                                    .' '.$last_delivery_address_sql[0]['name'].$num;

                            if (!empty($last_delivery_address_sql[0]['id_address_delivery'])
                                && !empty($last_delivery_address_sql[0]['id_address_invoice'])
                                && $last_delivery_address_sql[0]['id_address_delivery'] != $last_delivery_address_sql[0]['id_address_invoice']
                            ) {
                                $last_delivery_address_color = 'style="background-color:#e76c6c"';
                            }
                        }

                        echo '<cell '.$last_delivery_address_color.'><![CDATA['.$last_delivery_address.']]></cell>';
                        break;
                    case 'last_invoice_address':
                        $last_invoice_address = '';

                        $last_invoice_address_sql = Db::getInstance()->executeS('
                        SELECT o.id_address_invoice, a.*, cl.*
                        FROM '._DB_PREFIX_.'orders o
                            INNER JOIN '._DB_PREFIX_.'address a ON (o.id_address_invoice = a.id_address)
                                INNER JOIN '._DB_PREFIX_.'country_lang cl ON (a.id_country = cl.id_country AND cl.id_lang = "'.(int) $id_lang.'")
                        WHERE o.`id_customer` = '.(int) $gridrow['id_customer'].'
                            AND o.valid="1"
                        ORDER BY o.invoice_date DESC
                        LIMIT 1');
                        if (!empty($last_invoice_address_sql[0]))
                        {
                            $num = '';
                            if (!empty($last_invoice_address_sql[0]['phone']))
                            {
                                $num = $last_invoice_address_sql[0]['phone'];
                            }
                            if (!empty($last_invoice_address_sql[0]['phone_mobile']))
                            {
                                if (!empty($num))
                                {
                                    $num = $num.' / ';
                                }
                                $num = $last_invoice_address_sql[0]['phone_mobile'];
                            }
                            if (!empty($num))
                            {
                                $num = ' (<strong>'.$num.'</strong>)';
                            }

                            $last_invoice_address = $last_invoice_address_sql[0]['address1']
                                                    .' '.$last_invoice_address_sql[0]['address2']
                                                    .' '.$last_invoice_address_sql[0]['postcode']
                                                    .' '.$last_invoice_address_sql[0]['city']
                                                    .' '.$last_invoice_address_sql[0]['name'].$num;
                        }

                        echo '<cell><![CDATA['.$last_invoice_address.']]></cell>';
                        break;
                    case 'last_date_order':
                        $last_date_order = '';

                        $last_date_order_sql = Db::getInstance()->executeS('
                        SELECT o.date_add
                        FROM '._DB_PREFIX_.'orders o
                        WHERE o.`id_customer` = '.(int) $gridrow['id_customer'].'
                            AND o.valid="1"
                        ORDER BY o.invoice_date DESC
                        LIMIT 1');
                        if (!empty($last_date_order_sql[0]))
                        {
                            $last_date_order = $last_date_order_sql[0]['date_add'];
                        }

                        echo '<cell><![CDATA['.$last_date_order.']]></cell>';
                        break;
                    case 'last_date_cart':
                        $date_sql = Db::getInstance()->getValue('SELECT cp.date_add
                                                                        FROM `'._DB_PREFIX_.'cart` c
                                                                        RIGHT JOIN '._DB_PREFIX_.'cart_product cp ON cp.id_cart = c.id_cart
                                                                        WHERE c.`id_customer` = '.(int) $gridrow['id_customer'].'
                                                                        ORDER BY cp.id_cart DESC,cp.date_add DESC');
                        echo '<cell><![CDATA['.$date_sql.']]></cell>';
                        break;
                    case 'valid_orders':
                        echo '<cell '.((!empty($valid_orders)) ? 'style="background-color:#95ca82"' : '').'>'.$valid_orders.'</cell>';
                        break;
                    case 'total_valid_orders':
                        $total_valid_orders = '0';

                        $total_valid_orders_sql = Db::getInstance()->getValue('
                        SELECT SUM(total_paid) as total
                        FROM '._DB_PREFIX_.'orders
                        WHERE `id_customer` = '.(int) $gridrow['id_customer'].'
                            AND valid="1"');
                        if (!empty($total_valid_orders_sql))
                        {
                            $total_valid_orders = $total_valid_orders_sql;
                        }

                        echo '<cell><![CDATA['.number_format($total_valid_orders, 2, '.', '').']]></cell>';
                        break;
                    case 'groups':
                        $groups = '';
                            $groups_sql = Customer::getGroupsStatic((int) $gridrow['id_customer']);
                            if (!empty($groups_sql) && count($groups_sql) > 0)
                            {
                                foreach ($groups_sql as $group_id)
                                {
                                    if (!empty($groups))
                                    {
                                        $groups .= ', ';
                                    }
                                    $groups .= $arrGroupes[$group_id];
                                }
                            }
                        echo '<cell><![CDATA['.$groups.']]></cell>';
                        break;
                    case 'cart_lang':
                        $langue = '';
                        $lang_sql = Db::getInstance()->executeS('
                        SELECT l.name
                        FROM '._DB_PREFIX_.'cart c
                            INNER JOIN '._DB_PREFIX_.'lang l ON (c.id_lang = l.id_lang)
                        WHERE c.`id_customer` = '.(int) $gridrow['id_customer'].'
                        ORDER BY c.`date_add` DESC
                        LIMIT 1');
                        if (!empty($lang_sql[0]['name']))
                        {
                            $langue = $lang_sql[0]['name'];
                        }
                        echo '<cell><![CDATA['.$langue.']]></cell>';
                        break;

                    case 'date_connection':
                        $connection = '';

                        $connection_sql = Db::getInstance()->executeS('
                        SELECT c.date_add
                        FROM `'._DB_PREFIX_.'guest` g
                        LEFT JOIN `'._DB_PREFIX_.'connections` c ON c.id_guest = g.id_guest
                        LEFT JOIN `'._DB_PREFIX_.'connections_page` cp ON c.id_connections = cp.id_connections
                        WHERE g.`id_customer` = '.(int) $gridrow['id_customer'].'
                        GROUP BY c.`id_connections`
                        ORDER BY c.date_add DESC
                        LIMIT 1');
                        if (!empty($connection_sql[0]['date_add']))
                        {
                            $connection = $connection_sql[0]['date_add'];
                        }

                        echo '<cell><![CDATA['.$connection.']]></cell>';
                        break;
                    case 'shop_name':
                        if (SCMS && !empty($shops) && array_key_exists($gridrow['id_shop'], $shops))
                        {
                            echo '<cell><![CDATA['.$shops[$gridrow['id_shop']].']]></cell>';
                        }
                        break;
                    default:
                        sc_ext::readCustomCustomersGridsConfigXML('rowData');
                        if (!empty($colSettings[$col]['buildDefaultValue']))
                        {// && sc_array_key_exists('buildDefaultValue',$colSettings[$col]) && $colSettings[$col]['buildDefaultValue']!='')
                            if ($colSettings[$col]['buildDefaultValue'] == 'ID')
                            {
                                echo '<cell>ID'.$gridrow['id_product'].'</cell>';
                            }
                        }
                        else
                        {
                            if ($gridrow[$col] == '' || $gridrow[$col] === 0 || $gridrow[$col] === 1)
                            { // opti perf is_numeric($gridrow[$col]) ||
                                echo '<cell><![CDATA['.$gridrow[$col].']]></cell>';
                            }
                            else
                            {
                                echo '<cell><![CDATA['.$gridrow[$col].']]></cell>';
                            }
                        }
                }
            }
            echo "</row>\n";
        }
    }

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
    echo '</head>';

    $uiset = uisettings::getSetting('cus_grid_'.$view);
    if ($uiset)
    {
        $tmp = explode('|', $uiset);
        if (!empty($tmp) && isset($tmp[1]) && isset($tmp[3]))
        {
            $uiset = '|'.$tmp[1].'||'.$tmp[3];
        }
    }
    echo '<userdata name="uisettings">'.$uiset.'</userdata>'."\n";
    //echo '<userdata name="uisettings">'.uisettings::getSetting('cus_grid_'.$view).'</userdata>'."\n";

    echo '<userdata name="LIMIT_SMARTRENDERING">'.(int) _s('CAT_PROD_LIMIT_SMARTRENDERING').'</userdata>';
    if ($id_segment > 0 && is_object($segment) && $segment->type == 'manual')
    {
        echo '<userdata name="manual_segment">1</userdata>'."\n";
    }
    sc_ext::readCustomCustomersGridsConfigXML('gridUserData');
    echo "\n";
    getCustomers($segment);
    echo '</rows>';
