<?php

    $id_lang = (int) Tools::getValue('id_lang');
    $id = Tools::getValue('gr_id', '_');
    list($id_product, $id_shop) = explode('_', $id);

    $list_lang_fields = 'name,link_rewrite,available_now,available_later';
    $list_shop_fields = 'active,visibility,on_sale,online_only,show_price,minimal_quantity,ecotax,id_tax_rules_group,price,wholesale_price,unity,unit_price_ratio,additional_shipping_cost,available_for_order,available_date,condition';
    if (version_compare(_PS_VERSION_, '1.7.0.0', '>='))
    {
        $list_shop_fields .= ',show_condition';
    }
    if (isset($_POST['!nativeeditor_status']) && trim($_POST['!nativeeditor_status']) == 'updated' && !empty($id_product) && !empty($id_shop))
    {
        $ecotaxrate = SCI::getEcotaxTaxRate();

        // LANG
        $fields = explode(',', $list_lang_fields);
        $todo = array();
        $link_rewrite = '';
        foreach ($fields as $field)
        {
            if (isset($_POST[$field]))
            {
                $val = Tools::getValue($field);
                $todo[] = '`'.bqSQL($field)."`='".psql(html_entity_decode($val))."'";

                if ($field == 'name' && _s('CAT_SEO_NAME_TO_URL'))
                {
                    $link_rewrite = "`link_rewrite`='".link_rewrite($val, Language::getIsoById($id_lang))."'";
                }
            }
        }

        if (!empty($link_rewrite))
        {
            $todo[] = $link_rewrite;
        }

        if (count($todo))
        {
            $sql = 'UPDATE '._DB_PREFIX_.'product_lang SET '.join(' , ', $todo)." WHERE id_product=" .(int) $id_product . " AND id_shop=" .(int) $id_shop . " AND id_lang=" .(int) $id_lang;
            Db::getInstance()->Execute($sql);
        }

        // SHOP
        $fields = explode(',', $list_shop_fields);
        $todo = array();
        foreach ($fields as $field)
        {
            if (isset($_POST[$field]))
            {
                $val = Tools::getValue($field);

                if ($field == 'ecotax' && !empty($val))
                {
                    $val = $val / $ecotaxrate;
                }

                $todo[] = '`'.bqSQL($field)."`='".psql(html_entity_decode($val))."'";
            }
        }
        if (count($todo))
        {
            $sql = 'UPDATE '._DB_PREFIX_.'product_shop SET '.join(' , ', $todo)." WHERE id_product=" .(int) $id_product . " AND id_shop=" .(int) $id_shop;
            Db::getInstance()->Execute($sql);
        }

        // REF
        $todo = array();
        if (isset($_POST['reference']))
        {
            $val = Tools::getValue('reference');
            $todo[] = "`reference`='".psql(html_entity_decode($val))."'";
        }
        if (isset($_POST['supplier_reference']))
        {
            $val = Tools::getValue('supplier_reference');
            $todo[] = "`supplier_reference`='".psql(html_entity_decode($val))."'";

            $product = new Product($id_product);
            if (!empty($product->id_supplier))
            {
                $sql_supplier = 'SELECT * FROM '._DB_PREFIX_."product_supplier WHERE id_product=" .(int) $id_product . " AND id_supplier=" .(int) $product->id_supplier;
                $actual_product_supplier = Db::getInstance()->getRow($sql_supplier);
                if (!empty($actual_product_supplier['id_product_supplier']))
                {
                    $sql = 'UPDATE '._DB_PREFIX_."product_supplier SET `product_supplier_reference`='".psql(html_entity_decode($val))."' WHERE id_product_supplier=" .(int) $actual_product_supplier['id_product_supplier'];
                    Db::getInstance()->Execute($sql);
                }
                else
                {
                    $sql = 'INSERT INTO '._DB_PREFIX_."product_supplier
                            (id_product, id_product_attribute, id_supplier, product_supplier_reference)
                            VALUES(" .(int) $id_product . ",'0','".$product->id_supplier."','".psql(html_entity_decode($val))."')";
                    Db::getInstance()->Execute($sql);
                }
            }
        }
        if (isset($_POST['ecotax']))
        {
            $ecotax = Tools::getValue('ecotax', 0) / $ecotaxrate;
            $todo[] = "`ecotax`='".psql(html_entity_decode($ecotax))."'";
        }
        if (count($todo))
        {
            $sql = 'UPDATE '._DB_PREFIX_.'product SET '.join(' , ', $todo)." WHERE id_product=" .(int) $id_product;
            Db::getInstance()->Execute($sql);
        }

        $newId = Tools::getValue('gr_id');
        $action = 'update';
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
    echo '<data>';
    echo "<action type='".$action."' sid='".Tools::getValue('gr_id')."' tid='".$newId."'/>";
    echo $debug && isset($sql) ? '<sql><![CDATA['.$sql.']]></sql>' : '';
    echo $debug && isset($sql2) ? '<sql><![CDATA['.$sql2.']]></sql>' : '';
    echo $debug && isset($sql3) ? '<sql><![CDATA['.$sql3.']]></sql>' : '';
    echo '</data>';
