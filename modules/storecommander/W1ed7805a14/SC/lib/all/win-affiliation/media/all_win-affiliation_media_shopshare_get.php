<?php
    $idlist = Tools::getValue('idlist', 0);
    $id_lang = (int) Tools::getValue('id_lang');
    $cntBanners = count(explode(',', $idlist));
    $used = array();

    function getShops()
    {
        global $idlist,$id_lang,$used, $cntBanners;

        $multiple = false;
        if (strpos($idlist, ',') !== false)
        {
            $multiple = true;
        }

        $sql = 'SELECT *
                    FROM '._DB_PREFIX_."shop
                    WHERE
                        deleted != '1'
                    ORDER BY id_shop_group ASC, name ASC";
        $res = Db::getInstance()->ExecuteS($sql);

        if (!$multiple)
        {
            $banner = new SCAffBanner((int) $idlist);
            $banner_shops = $banner->GetShops();
            foreach ($res as $shop)
            {
                $used[$shop['id_shop']] = array(0, '');

                if (in_array($shop['id_shop'], $banner_shops))
                {
                    $used[$shop['id_shop']][0] = 1;
                }
            }
        }
        else
        {
            foreach ($res as $shop)
            {
                $used[$shop['id_shop']] = array(0, 'DDDDDD');
                $nb_present = 0;

                $lists = explode(',', $idlist);
                foreach ($lists as $list)
                {
                    $banner = new SCAffBanner((int) $list);
                    $banner_shops = $banner->GetShops();
                    if (in_array($shop['id_shop'], $banner_shops))
                    {
                        ++$nb_present;
                    }
                }

                if ($nb_present == $cntBanners)
                {
                    $used[$shop['id_shop']][0] = 1;
                    $used[$shop['id_shop']][1] = '7777AA';
                }
                elseif ($nb_present < $cntBanners && $nb_present > 0)
                {
                    $used[$shop['id_shop']][1] = '777777';
                }
            }
        }

        foreach ($res as $row)
        {
            echo '<row id="'.$row['id_shop'].'">';
            echo '<cell><![CDATA['.$row['id_shop'].']]></cell>';
            echo '<cell><![CDATA['.$row['name'].']]></cell>';
            echo '<cell style="background-color:'.((!empty($used[$row['id_shop']][1])) ? '#'.$used[$row['id_shop']][1] : '').'">'.((!empty($used[$row['id_shop']][0])) ? '1' : '0').'</cell>';
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
<call command="attachHeader"><param><![CDATA[#select_filter,#select_filter,]]></param></call>
</beforeInit>
<column id="id" width="80" type="ro" align="right" sort="str"><?php echo _l('ID'); ?></column>
<column id="shop" width="200" type="ro" align="left" sort="str"><?php echo _l('Shop'); ?></column>
<column id="present" width="80" type="ch" align="center" sort="int"><?php echo _l('Present'); ?></column>
</head>
<?php
    getShops();
    //echo '</rows>';
?>
</rows>