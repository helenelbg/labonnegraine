<?php

    $attributegroups = array();
    $id_lang = (int) Tools::getValue('id_lang');
    $id_attribute_group = (int) Tools::getValue('id_attribute_group');
    $iscolor = (int) Tools::getValue('iscolor');
    $sc_active = SCI::getConfigurationValue('SC_PLUG_DISABLECOMBINATIONS', 0);

    $sql = 'SELECT *
                FROM '._DB_PREFIX_.'attribute a
                RIGHT JOIN '._DB_PREFIX_.'attribute_lang al ON (al.id_attribute=a.id_attribute)
                WHERE a.id_attribute_group='.$id_attribute_group;
    if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
    {
        $sql .= ' ORDER BY a.position ASC ';
    }
    $rows = Db::getInstance()->ExecuteS($sql);

    $sql = 'SELECT DISTINCT pac.id_attribute, pa.id_product
            FROM '._DB_PREFIX_.'product_attribute_combination pac
            LEFT JOIN '._DB_PREFIX_.'product_attribute pa ON (pac.id_product_attribute = pa.id_product_attribute)
            LEFT JOIN '._DB_PREFIX_.'attribute a ON (a.id_attribute = pac.id_attribute)
            WHERE a.id_attribute_group = '.$id_attribute_group;
    $cache_nb_product_by_attribute = Db::getInstance()->executes($sql);

    $tmp = array();
    if (!empty($cache_nb_product_by_attribute))
    {
        foreach ($cache_nb_product_by_attribute as $row)
        {
            if (isset($tmp[$row['id_attribute']]))
            {
                ++$tmp[$row['id_attribute']];
            }
            else
            {
                $tmp[$row['id_attribute']] = 1;
            }
        }
    }
    $cache_nb_product_by_attribute = $tmp;

    $names = array();
    $xml = '';
    $cols = '';
    $filters = '';

    if (!empty($rows))
    {
        foreach ($rows as $row)
        {
            $names[$row['id_attribute']][$row['id_lang']] = $row['name'];
        }

        foreach ($languages as $lang)
        {
            $cols .= '<column id="name¤'.$lang['iso_code'].'" width="100" type="edtxt" align="left" sort="str">'._l('Name').' '.strtoupper($lang['iso_code']).'</column>';
            $filters .= ',#text_filter';
        }

        $rand = rand(1, 100);
        foreach ($rows as $row)
        {
            if ($row['id_lang'] != $id_lang)
            {
                continue;
            }
            $xml .= "<row id='".$row['id_attribute']."'>";
            $xml .= '<cell style="color:#999999">'.$row['id_attribute'].'</cell>';
            if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
            {
                $xml .= '<cell>'.$row['position'].'</cell>';
            }
            if ($sc_active)
            {
                $xml .= '<cell>'.$row['sc_active'].'</cell>';
            }
            if ($iscolor)
            {
                $xml .= '<cell><![CDATA['.$row['color'].']]></cell>'.'<cell><![CDATA['.$row['color'].']]></cell>';
            }
            foreach ($languages as $lang)
            {
                @$xml .= '<cell><![CDATA['.$names[$row['id_attribute']][$lang['id_lang']].']]></cell>';
            }
            if ($iscolor)
            {
                $ext = checkAndGetImgExtension(_PS_COL_IMG_DIR_.$row['id_attribute']);
                $img = '';
                if (!empty($ext))
                {
                    $img = '<img src="'._THEME_COL_DIR_.$row['id_attribute'].'.'.$ext.'?'.$rand.'" height=40/>';
                }
                $xml .= '<cell><![CDATA['.$img.']]></cell>';
            }
            $xml .= '<cell style="color:#999999">'.(int) (array_key_exists($row['id_attribute'], $cache_nb_product_by_attribute) ? $cache_nb_product_by_attribute[$row['id_attribute']] : 0).'</cell>';
            $xml .= "</row>\n";
        }
    }

    //XML HEADER

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
<beforeInit>
<call command="attachHeader"><param><![CDATA[#numeric_filter<?php echo version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? ',#numeric_filter' : ''; if ($sc_active)
{
    echo ',#select_filter';
} if ($iscolor)
{
    echo ',#text_filter,';
}  echo $filters; if ($iscolor)
{
    echo ',';
} ?>,#numeric_filter]]></param></call>
<call command="enableMultiselect"><param>1</param></call>
</beforeInit>
<column id="id_attribute" width="40" type="ro" align="right" sort="int"><?php echo _l('ID'); ?></column>
<?php if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) {?>
<column id="position" width="40" type="ro" align="right" sort="int"><?php echo _l('Position'); ?></column>
<?php }
if ($sc_active) {?><column id="sc_active" width="50" type="co" align="center" sort="str"><?php echo _l('Used'); ?><option value="0"><![CDATA[<?php echo _l('No'); ?>]]></option><option value="1"><![CDATA[<?php echo _l('Yes'); ?>]]></option></column><?php } ?>
<?php if ($iscolor) {?><column id="color" width="60" type="edtxt" align="left" sort="str"><?php echo _l('Color code'); ?></column>
<column id="color2" width="60" type="cp" align="left" sort="str"><?php echo _l('Color'); ?></column><?php } ?>
<?php
    echo $cols;
?>
<?php if ($iscolor) {?><column id="image" width="120" type="ro" align="center" sort="na"><?php echo _l('Image'); ?></column><?php } ?>
<column id="usedby" width="40" type="ro" align="right" sort="int"><?php echo _l('Used by'); ?></column>
</head>
<?php
    echo '<userdata name="uisettings">'.uisettings::getSetting('cat_win-attribute').'</userdata>'."\n";
    echo $xml;
?>
</rows>
