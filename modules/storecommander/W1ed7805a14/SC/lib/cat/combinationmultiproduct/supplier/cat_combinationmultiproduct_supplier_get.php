<?php

    $idlist = Tools::getValue('idlist', 0);
    $idlist_arr = explode(',', $idlist);
    $idlist = array();
    foreach ($idlist_arr as $row)
    {
        list($id_product, $id_product_attribute) = explode('_', $row);
        $idlist[] = (int) $id_product_attribute;
        $id_product_list[] = (int) $id_product;
    }
    $id_product_list = implode(',', $id_product_list);
    $idlist = implode(',', $idlist);
    $id_lang = (int) Tools::getValue('id_lang');
    $id_product = (int) Tools::getValue('id_product');

    $used = array();

    $multiple = false;
    if (strpos($idlist, ',') !== false)
    {
        $multiple = true;
    }

    $cntProductAttrs = 0;
    if (!empty($idlist))
    {
        $cntProductAttrs = count($idlist_arr);
    }

    function getSuppliers()
    {
        global $idlist,$multiple,$id_lang,$id_product,$used, $cntProductAttrs;

        if (empty($idlist))
        {
            return false;
        }

        $shop = (int) SCI::getSelectedShop();
        if ($shop == 0)
        {
            $shop = null;
        }

        $query = 'SELECT s.*, sl.`description`
                FROM '._DB_PREFIX_.'supplier s
                LEFT JOIN '._DB_PREFIX_.'supplier_lang sl
                ON s.`id_supplier` = sl.`id_supplier` AND sl.`id_lang` = '.(int) $id_lang;
        if (version_compare(_PS_VERSION_, '1.5.0.10', '>='))
        {
            $query .= ' LEFT JOIN '._DB_PREFIX_.'supplier_shop ss 
                ON s.`id_supplier` = ss.`id_supplier` AND ss.`id_shop` = '.(int) $shop;
        }
        $query .= ' GROUP BY s.id_supplier
         ORDER BY s.`name` ASC';

        $suppliers = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);

        if (!$multiple)
        {
            foreach ($suppliers as $supplier)
            {
                $used[$supplier['id_supplier']] = array(0, '', '', '', '', 0, '');

                $sql = '
                    SELECT ps.*,p.id_supplier as prd_supplier
                    FROM `'._DB_PREFIX_.'product_supplier` ps
                    LEFT JOIN '._DB_PREFIX_.'product p ON p.id_product = ps.id_product
                    WHERE ps.`id_supplier` = "'.(int) $supplier['id_supplier'].'"
                    AND ps.`id_product_attribute` = '.(int) $idlist;
                $check_in_supplier = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
                if (!empty($check_in_supplier['id_product_supplier']))
                {
                    $used[$supplier['id_supplier']][0] = 1;

                    $used[$supplier['id_supplier']][2] = $check_in_supplier['product_supplier_reference'];
                    $used[$supplier['id_supplier']][3] = $check_in_supplier['product_supplier_price_te'];
                    $used[$supplier['id_supplier']][4] = $check_in_supplier['id_currency'];

                    if ($check_in_supplier['prd_supplier'] == $supplier['id_supplier'])
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
                    WHERE ps.id_product_attribute IN ('.pInSQL($idlist).")
                        AND ps.id_supplier = '".(int) $supplier['id_supplier']."'";
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

                if ($nb_present == $cntProductAttrs)
                {
                    $used[$supplier['id_supplier']][0] = 1;
                    $used[$supplier['id_supplier']][1] = '7777AA';
                }
                elseif ($nb_present < $cntProductAttrs && $nb_present > 0)
                {
                    $used[$supplier['id_supplier']][1] = '777777';
                }

                if ($nb_default == $cntProductAttrs)
                {
                    $used[$supplier['id_supplier']][5] = 1;
                    $used[$supplier['id_supplier']][6] = '7777AA';
                }
                elseif ($nb_default < $cntProductAttrs && $nb_default > 0)
                {
                    $used[$supplier['id_supplier']][6] = '777777';
                }
            }
        }

        foreach ($suppliers as $row)
        {
            echo '<row id="'.(int) $row['id_supplier'].'">';
            echo '<cell>'.(int) $row['id_supplier'].'</cell>';
            echo '<cell><![CDATA['.$row['name'].']]></cell>';
            echo '<cell style="background-color:'.((!empty($used[$row['id_supplier']][1])) ? '#'.$used[$row['id_supplier']][1] : '').'">'.$used[$row['id_supplier']][0].'</cell>';
            if (!$multiple)
            {
                echo '<cell>'.((!empty($used[$row['id_supplier']][2])) ? $used[$row['id_supplier']][2] : '').'</cell>';
                echo '<cell>'.((!empty($used[$row['id_supplier']][3])) ? $used[$row['id_supplier']][3] : '').'</cell>';
                echo '<cell>'.((!empty($used[$row['id_supplier']][4])) ? $used[$row['id_supplier']][4] : '').'</cell>';
            }
            echo '</row>';
        }
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

    if (stristr($_SERVER['HTTP_ACCEPT'], 'application/xhtml+xml'))
    {
        header('Content-type: application/xhtml+xml');
    }
    else
    {
        header('Content-type: text/xml');
    }
    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
?>
<rows>
<head>
<beforeInit>
<call command="attachHeader"><param><![CDATA[#numeric_filter,#text_filter,#select_filter<?php if (!$multiple) { ?>,#text_filter,#text_filter,#select_filter<?php } ?>]]></param></call>
</beforeInit>
<column id="id" width="50" type="ro" align="left" sort="str"><?php echo _l('ID'); ?></column>
<column id="name" width="200" type="ro" align="left" sort="str"><?php echo _l('Supplier'); ?></column>
<column id="present" width="80" type="ch" align="center" sort="int"><?php echo _l('Present'); ?></column>
<?php if (!$multiple) { ?>
<column id="product_supplier_reference" width="100" type="ed" align="left" sort="str"><?php echo _l('Supplier reference'); ?></column>
<column id="product_supplier_price_te" width="100" type="ed" align="right" sort="int"><?php echo _l('Wholesale price'); ?></column>
<column id="id_currency" width="80" type="coro" align="right" sort="int"><?php echo _l('Currency'); echo $currencies; ?></column>
<?php }?>
</head>
<?php
    echo '<userdata name="uisettings">'.uisettings::getSetting('cat_supplier').'</userdata>'."\n";

    getSuppliers();

 ?>
</rows>