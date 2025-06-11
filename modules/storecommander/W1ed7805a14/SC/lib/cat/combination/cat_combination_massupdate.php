<?php

    if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
    {
        exit();
    }

    $combiliststr = Tools::getValue('combilist');
    $id_lang = Tools::getValue('id_lang', SCI::getConfigurationValue('PS_LANG_DEFAULT'));
    $id_product = Tools::getValue('id_product', 0);
    $field = Tools::getValue('field', '');
    $todo = Tools::getValue('todo', '');
    $alert_msg_qty = '';
    if ($combiliststr != '' && $todo != '')
    {
        $needUpdateAttributeHook = false;
        $needUpdateProductHook = false;
        switch ($field) {
            case 'mass_round':
                $todo = Tools::getValue('todo', '0');
                $column = Tools::getValue('column', '');

                if (!empty($todo) && !empty($column))
                {
                    // PRODUCT
                    $sql = 'SELECT t.rate,p.price,p.ecotax
                    FROM `'._DB_PREFIX_.'product` p
                    LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (p.`id_tax_rules_group` = tr.`id_tax_rules_group` AND tr.`id_country` = '.(int) SCI::getDefaultCountryId().' AND tr.`id_state` = 0)
                        LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)
                    WHERE p.id_product='.(int) $id_product;

                    $p = Db::getInstance()->getRow($sql);
                    if (empty($p['rate']))
                    {
                        $p['rate'] = 1;
                    }
                    $taxrate = $p['rate'];
                    $pprice = $p['price'];
                    $pecotax = $p['ecotax'];

                    // COMBINATIONS
                    $combilist = explode(',', $combiliststr);
                    foreach ($combilist as $id_product_attribute)
                    {
                        $old_price = array(
                            'product' => 0,
                            'shops' => array(),
                        );
                        $eco_price = array(
                            'product' => 0,
                            'shops' => array(),
                        );

                        // Récupération du prix à modifier
                        if ($column == 'priceextax')
                        {
                            $select_column = 'price';

                            $sql = 'SELECT '.$select_column.'
                                    FROM '._DB_PREFIX_.'product_attribute
                                    WHERE id_product_attribute = "'.(int) $id_product_attribute.'"';
                            $rslt = Db::getInstance()->ExecuteS($sql);
                            if (isset($rslt[0][$select_column]))
                            {
                                $price = $rslt[0][$select_column];
                                $old_price['product'] = number_format($price + $pprice, 6, '.', '');
                            }
                        }
                        elseif ($column == 'wholesale_price')
                        {
                            $select_column = $column;
                            $sql = 'SELECT '.$select_column.' FROM '._DB_PREFIX_.'product_attribute WHERE id_product_attribute = "'.(int) $id_product_attribute.'"';
                            $rslt = Db::getInstance()->ExecuteS($sql);
                            if (!empty($rslt[0][$select_column]))
                            {
                                $old_price['product'] = $rslt[0][$select_column];
                            }
                        }
                        elseif ($column == 'price')
                        {
                            $select_column = 'price';

                            $sql = 'SELECT '.$select_column.',ecotax
                                        FROM '._DB_PREFIX_.'product_attribute
                                        WHERE id_product_attribute = "'.(int) $id_product_attribute.'"';

                            $rslt = Db::getInstance()->ExecuteS($sql);
                            if (!empty($rslt[0][$select_column]))
                            {
                                $price = $rslt[0][$select_column];

                                $ecotax_temp = (_s('CAT_PROD_ECOTAXINCLUDED') ? $rslt[0]['ecotax'] * SCI::getEcotaxTaxRate() : 0);
                                if (($ecotax_temp * 1) == 0)
                                {
                                    $ecotax_temp = (_s('CAT_PROD_ECOTAXINCLUDED') ? $pecotax * SCI::getEcotaxTaxRate() : 0);
                                }

                                if (!empty($taxrate))
                                {
                                    $old_price['product'] = number_format($price * ($taxrate / 100 + 1) + $pprice * ($taxrate / 100 + 1) + $ecotax_temp, 6, '.', '');
                                }
                                else
                                {
                                    $old_price['product'] = number_format($price + $pprice + $ecotax_temp, 6, '.', '');
                                }
                                if (_s('CAT_PROD_ECOTAXINCLUDED'))
                                {
                                    $eco_price['product'] = $ecotax_temp;
                                }
                            }
                        }

                        // Arrondir le prix
                        $new_price = SCI::roundPrice($old_price['product'], $todo);
                        $update_column = $column;
                        if ($column == 'price')
                        { // TTC
                            $update_column = 'price';
                            if (!empty($taxrate))
                            {
                                $new_price = floatval((floatval($new_price - $eco_price['product']) / ($taxrate / 100 + 1)) - ($pprice));
                            }
                            else
                            {
                                $new_price = floatval((floatval($new_price - $eco_price['product'])) - ($pprice));
                            }
                        }
                        elseif ($column == 'priceextax')
                        { // HT
                            $update_column = 'price';
                            $new_price = floatval($new_price) - floatval($pprice);
                        }
                        Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute SET '.$update_column.'="'.pSQL($new_price).'" WHERE id_product_attribute = "'.(int) $id_product_attribute.'"');
                    }
                }
            break;
        }

        if (!empty($updated_products))
        {
            ExtensionPMCM::clearFromIdsProduct($id_product);
        }
    }

    if (!empty($alert_msg_qty))
    {
        echo 'quantity|'.$alert_msg_qty;
    }
