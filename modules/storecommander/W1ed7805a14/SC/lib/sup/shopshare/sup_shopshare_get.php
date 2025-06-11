<?php

    $idlist = Tools::getValue('idlist', 0);
    $id_lang = (int)Tools::getValue('id_lang');
    $cntSuppliers = count(explode(',', $idlist));
    $used = array();

    function getShopsSuppliers($idlist, $used, $cntSuppliers)
    {
        $xml_array = array();

        $multiple = false;
        if ($cntSuppliers > 1)
        {
            $multiple = true;
        }

        $sql = 'SELECT *
                    FROM '._DB_PREFIX_.'shop
                    WHERE deleted = 0
                    ORDER BY id_shop_group ASC, name ASC';
        $res = Db::getInstance()->ExecuteS($sql);

        if (!empty($res))
        {
            if (!$multiple)
            {
                foreach ($res as $shop)
                {
                    $sql2 = 'SELECT id_supplier
                                FROM '._DB_PREFIX_.'supplier_shop
                                WHERE id_supplier IN ('.pInSQL($idlist).')
                                AND id_shop = '.(int) $shop['id_shop'];
                    $res2 = Db::getInstance()->getRow($sql2);
                    if (!empty($res2['id_supplier']))
                    {
                        $used[$shop['id_shop']][0] = 1;
                    }
                }
            }
            else
            {
                foreach ($res as $shop)
                {
                    $used[$shop['id_shop']] = array(0, 0, 'DDDDDD', 'DDDDDD', 0, 'DDDDDD');
                    $nb_present = 0;

                    $sql2 = 'SELECT id_supplier
                                FROM '._DB_PREFIX_.'supplier_shop
                                WHERE id_supplier IN ('.pInSQL($idlist).')
                                AND id_shop = '.(int) $shop['id_shop'];
                    $res2 = Db::getInstance()->ExecuteS($sql2);
                    foreach ($res2 as $supplier)
                    {
                        if (!empty($supplier['id_supplier']))
                        {
                            ++$nb_present;
                        }
                    }

                    if ($nb_present == $cntSuppliers)
                    {
                        $used[$shop['id_shop']][0] = 1;
                        $used[$shop['id_shop']][2] = '7777AA';
                    }
                    elseif ($nb_present < $cntSuppliers && $nb_present > 0)
                    {
                        $used[$shop['id_shop']][2] = '777777';
                    }
                }
            }

            foreach ($res as $row)
            {
                $xml_array[] = '<row id="'.$row['id_shop'].'">';
                $xml_array[] = '<cell><![CDATA['.$row['id_shop'].']]></cell>';
                $xml_array[] = '<cell><![CDATA['.$row['name'].']]></cell>';
                $xml_array[] = '<cell style="background-color:'.((!empty($used[$row['id_shop']][2])) ? '#'.$used[$row['id_shop']][2] : '').'">'.((!empty($used[$row['id_shop']][0])) ? '1' : '0').'</cell>';
                $xml_array[] = '</row>';
            }
        }

        return implode("\n", $xml_array);
    }

    $supplier_shopshar = '';
    if (!empty($idlist))
    {
        $supplier_shopshar = getShopsSuppliers($idlist, $used, $cntSuppliers);
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
<afterInit>
<call command="attachHeader"><param><![CDATA[#select_filter,#select_filter,,]]></param></call>
</afterInit>
<column id="id" width="80" type="ro" align="right" sort="str"><?php echo _l('ID'); ?></column>
<column id="shop" width="200" type="ro" align="left" sort="str"><?php echo _l('Shop'); ?></column>
<column id="present" width="80" type="ch" align="center" sort="int"><?php echo _l('Present'); ?></column>
</head>
<?php
    echo '<userdata name="uisettings">'.uisettings::getSetting('sup_supplier_langshopshare').'</userdata>'."\n";
    echo $supplier_shopshar;
?>
</rows>
