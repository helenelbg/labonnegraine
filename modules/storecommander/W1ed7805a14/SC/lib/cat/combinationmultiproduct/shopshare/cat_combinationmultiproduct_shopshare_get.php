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

    $cntCombis = count($idlist_arr);
    $multiple = false;
    if ($cntCombis > 1)
    {
        $multiple = true;
    }

    function getshops()
    {
        global $idlist,$multiple,$used,$cntCombis,$id_product_list;

        if (empty($idlist))
        {
            return false;
        }

        $sql_shop = 'SELECT ps.id_shop, s.name
            FROM '._DB_PREFIX_.'product_shop ps
                INNER JOIN  '._DB_PREFIX_."shop s ON (ps.id_shop = s.id_shop)
            WHERE ps.id_product IN ('".pInSQL($id_product_list)."')
            GROUP BY ps.id_shop
            ORDER BY s.name";
        $shops = Db::getInstance()->ExecuteS($sql_shop);

        if (!$multiple)
        {
            foreach ($shops as $shop)
            {
                $used[$shop['id_shop']] = array(0, '');

                $sql_in_shop = 'SELECT id_product_attribute
                    FROM '._DB_PREFIX_."product_attribute_shop
                    WHERE id_product_attribute = '".(int) $idlist."'
                        AND  id_shop = '".(int) $shop['id_shop']."'";
                $in_shop = Db::getInstance()->ExecuteS($sql_in_shop);
                if (!empty($in_shop[0]['id_product_attribute']))
                {
                    $used[$shop['id_shop']][0] = 1;
                }
            }
        }
        else
        {
            foreach ($shops as $shop)
            {
                $used[$shop['id_shop']] = array(0, 'DDDDDD');

                $sql2 = 'SELECT COUNT(id_product_attribute)
                    FROM '._DB_PREFIX_.'product_attribute_shop
                    WHERE id_product_attribute IN ('.pInSQL($idlist).")
                        AND id_shop = '".(int) $shop['id_shop']."'";
                $nb_present = Db::getInstance()->getValue($sql2);
                if ($nb_present == $cntCombis)
                {
                    $used[$shop['id_shop']][0] = 1;
                    $used[$shop['id_shop']][1] = '7777AA';
                }
                elseif ($nb_present < $cntCombis && $nb_present > 0)
                {
                    $used[$shop['id_shop']][1] = '777777';
                }
            }
        }

        foreach ($shops as $row)
        {
            echo '<row id="'.$row['id_shop'].'">';
            echo '<cell><![CDATA['.$row['name'].']]></cell>';
            echo '<cell style="background-color:'.((!empty($used[$row['id_shop']][1])) ? '#'.$used[$row['id_shop']][1] : '').'">'.$used[$row['id_shop']][0].'</cell>';
            echo '</row>';
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
?>
<rows>
<head>
<beforeInit>
<call command="attachHeader"><param><![CDATA[#select_filter_strict,#select_filter]]></param></call>
</beforeInit>
<column id="id" width="200" type="ro" align="left" sort="str"><?php echo _l('Shop'); ?></column>
<column id="present" width="80" type="ch" align="center" sort="int"><?php echo _l('Present'); ?></column>
</head>
<?php
    echo '<userdata name="uisettings">'.uisettings::getSetting('cat_combination_shopshare').'</userdata>'."\n";
    getshops();

?>
</rows>