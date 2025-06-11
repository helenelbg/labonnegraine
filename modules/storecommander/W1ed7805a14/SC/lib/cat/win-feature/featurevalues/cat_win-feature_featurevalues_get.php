<?php

    $feature_valuegroups = array();
    $id_lang = (int) Tools::getValue('id_lang');
    $id_feature = (int) Tools::getValue('id_feature');
    $iscolor = (int) Tools::getValue('iscolor');

    $sql = 'SELECT fv.id_feature_value,fvl.value,fvl.id_lang,(SELECT count(fp.id_feature_value) FROM '._DB_PREFIX_.'feature_product fp WHERE fp.id_feature_value=fv.id_feature_value )as nb
                FROM '._DB_PREFIX_.'feature_value fv LEFT JOIN '._DB_PREFIX_.'feature_value_lang fvl ON (fvl.id_feature_value=fv.id_feature_value) WHERE fv.id_feature='.(int) $id_feature;
    $rows = Db::getInstance()->ExecuteS($sql);
    $names = array();
    $nb = array();
    foreach ($rows as $row)
    {
        $names[$row['id_feature_value']][$row['id_lang']] = $row['value'];
        $nb[$row['id_feature_value']] = $row['nb'];
    }

    $xml = '';
    $cols = '';
    $filters = '';
    foreach ($languages as $lang)
    {
        $cols .= '<column id="value¤'.$lang['iso_code'].'" width="150" type="edtxt" align="left" sort="str">'._l('Name').' '.strtoupper($lang['iso_code']).'</column>';
        $filters .= ',#text_filter';
    }

    $feature_values = FeatureValue::getFeatureValuesWithLang($id_lang, $id_feature);

    foreach ($feature_values as $row)
    {
        $xml .= "<row id='".$row['id_feature_value']."'>";
        $xml .= '<cell style="color:#999999">'.$row['id_feature_value'].'</cell>';
        foreach ($languages as $lang)
        {
            @$xml .= '<cell><![CDATA['.$names[$row['id_feature_value']][$lang['id_lang']].']]></cell>';
        }
        $xml .= '<cell style="color:#999999">'.$nb[$row['id_feature_value']].'</cell>';
        $xml .= '</row>';
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
<call command="attachHeader"><param><![CDATA[#text_filter<?php if ($iscolor)
{
    echo ',#text_filter,';
}  echo $filters; ?>,#numeric_filter]]></param></call>
<call command="enableMultiselect"><param>1</param></call>
</beforeInit>
<column id="id_feature_value" width="40" type="ro" align="right" sort="int"><?php echo _l('ID'); ?></column>
<?php
    echo $cols;
?>
<column id="usedby" width="40" type="ro" align="right" sort="int"><?php echo _l('Used by'); ?></column>
</head>
<?php
    echo '<userdata name="uisettings">'.uisettings::getSetting('cat_win-feature_value').'</userdata>'."\n";
    echo $xml;
?>
</rows>
