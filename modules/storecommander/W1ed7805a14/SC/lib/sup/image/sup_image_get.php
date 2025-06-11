<?php
    $id_lang = (int)Tools::getValue('id_lang');
    $supplier_ids = (string) Tools::getValue('id_supplier', null);
    $id_shop = (int) Tools::getValue('id_shop');
    $link = new Link();
    $supplier_ids = explode(',', $supplier_ids);

    $supplier_images = '';

    function generateValue($supplier_ids, $id_shop)
    {
        $xml_array = array();
        if (!empty($supplier_ids))
        {
            $currentTime = time();
            if (version_compare(_PS_VERSION_, '1.5.0.10', '>='))
            {
                $shopUrl = new ShopUrl($id_shop);
                $shop_url = $shopUrl->getURL(Configuration::get('PS_SSL_ENABLED'));
            }
            else
            {
                $shop = new Shop($id_shop);
                if (Configuration::get('PS_SSL_ENABLED'))
                {
                    $shop_url = 'https://'.$shop->domain_ssl.$shop->getBaseURI();
                }
                else
                {
                    $shop_url = 'http://'.$shop->domain.$shop->getBaseURI();
                }
            }
            foreach ($supplier_ids as $id_supplier)
            {
                $xml_array[] = '<row id="'.$id_supplier.'">';
                $xml_array[] = '<userdata name="id_supplier">'.(int) $id_supplier.'</userdata>';
                $cols = array('id_supplier', 'image');
                foreach ($cols as $col)
                {
                    switch ($col) {
                        case 'id_supplier':
                            $xml_array[] = '<cell>'.(int) $id_supplier.'</cell>';
                            break;
                        case 'image':
                            $to_img = rtrim($shop_url, '/').'/img/su/'.$id_supplier.'.jpg';
                            $path = _PS_SUPP_IMG_DIR_.$id_supplier.'.jpg';
                            if (file_exists($path))
                            {
                                $xml_array[] = '<cell><![CDATA[<img loading="lazy" src="'.$to_img.'?time='.$currentTime.'" width="auto" height="80"/>]]></cell>"';
                            }
                            else
                            {
                                $xml_array[] = '<cell></cell>';
                            }
                            break;
                    }
                }
                $xml_array[] = '</row>';
            }
        }

        return implode("\n", $xml_array);
    }

$supplier_images = generateValue($supplier_ids, $id_shop);

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
?>
<rows id="0">
    <head>
        <afterInit>
            <call command="attachHeader"><param><![CDATA[#text_filter,#text_filter]]></param></call>
        </afterInit>
        <column id="id_supplier" width="100" type="ro" align="center" sort="int"><?php echo _l('ID supplier'); ?></column>
        <column id="image" width="200" type="ro" align="center" sort="str" color=""><?php echo _l('Image'); ?></column>
        <afterInit>
            <call command="enableMultiselect"><param>1</param></call>
        </afterInit>
    </head>
    <?php
    echo '<userdata name="uisettings">'.uisettings::getSetting('sup_image').'</userdata>'."\n";
    echo $supplier_images;
    ?>
</rows>

