<?php

    $xml = array();

    if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
    {
        $sql = 'SELECT id_shop,name  
            FROM '._DB_PREFIX_.'shop
            WHERE deleted = 0';
        $shops = Db::getInstance()->executeS($sql);
        if (!empty($shops))
        {
            $tmp = array();
            foreach ($shops as $shop)
            {
                $tmp[(int) $shop['id_shop']] = (string) $shop['name'];
            }
            $shops = $tmp;
        }
    }
    else
    {
        $shops[1] = Db::getInstance()->getValue('SELECT value FROM '._DB_PREFIX_.'configuration WHERE name = "PS_SHOP_NAME"');
    }

    foreach ($shops as $id_shop => $shop_name)
    {
        $row = array();
        $row[] = "\t".'<row id="'.$id_shop.'">';
        $row[] = "\t\t".'<cell>'.$id_shop.'</cell>';
        $row[] = "\t\t".'<cell><![CDATA['.$shop_name.']]></cell>';
        $row[] = "\t\t".'<cell bgColor="red" style="color:white"><![CDATA[-]]></cell>';
        $row[] = "\t".'</row>';
        $xml[] = implode("\n", $row);
    }
    $xml = implode("\n", $xml);

    //include XML Header (as response will be in xml format)
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
<rows id="0">
<head>
    <column id="id_shop" width="60" type="ro" align="int" sort="int"><?php echo _l('id_shop'); ?></column>
    <column id="shop" width="200" type="ro" align="left" sort="str"><?php echo _l('Shop'); ?></column>
    <column id="status" width="*" type="ro" align="center" sort="str"><?php echo _l('Status'); ?></column>
</head>
<?php
    echo $xml;
?>
</rows>
