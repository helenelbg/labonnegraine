<?php

$category_ids = Tools::getValue('idlist', 0);

function getRowsFromDB()
{
    global $category_ids;

    $category_ids = explode(',', $category_ids);
    $xml = '';
    foreach ($category_ids as $id_category)
    {
        $filename = '/'.(int) $id_category.'_thumb.jpg';
        $image = '';
        if (file_exists(_PS_CAT_IMG_DIR_.$filename))
        {
            $image = '<img src="'.SC_PS_PATH_REL.'img/c'.$filename.'?'.filemtime(_PS_CAT_IMG_DIR_.$filename).'" height="120px" alt="" />';
        }
        elseif (file_exists(_PS_TMP_IMG_DIR_.'category_'.$id_category.'-thumb.jpg'))
        {
            $image = '<img src="'.SC_PS_PATH_REL.'img/tmp/category_'.$id_category.'-thumb.jpg'.'?'.time().'" height="120px" alt="" />';
        }

        if (!empty($image))
        {
            $xml .= "<row id='".(int) $id_category."'>";
            $xml .= '<cell>'.(int) $id_category.'</cell>';
            $xml .= '<cell><![CDATA['.$image.']]></cell>';
            $xml .= '</row>';
        }
    }

    return $xml;
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
if (!empty($category_ids))
{
    $xml = getRowsFromDB();
}
?>
<rows id="0">
    <head>
        <beforeInit>
            <call command="attachHeader"><param><![CDATA[#text_filter]]></param></call>
        </beforeInit>
        <column id="id_category" width="100" type="ro" align="right" sort="int"><?php echo _l('ID'); ?></column>
        <column id="image" width="500" type="ro" align="center" sort="int"><?php echo _l('Image'); ?></column>
        <afterInit>
            <call command="enableMultiselect">
                <param>
                1</param></call>
        </afterInit>
    </head>
    <?php
    echo '<userdata name="uisettings">'.uisettings::getSetting('cat_prop_image_grid').'</userdata>'."\n";
    echo $xml;
    ?>
</rows>
