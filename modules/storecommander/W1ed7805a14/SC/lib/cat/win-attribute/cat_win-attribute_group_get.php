<?php

    $attributegroups = array();
    $id_lang = (int) Tools::getValue('id_lang');
    $all_groups = (int) Tools::getValue('all_groups', null);
    $filter_params = Tools::getValue('params', null);

    $iso = null;
    $where_condition = null;
    if (!empty($filter_params))
    {
        $all_params = explode(',', $filter_params);
        foreach ($all_params as $param)
        {
            list($column, $value) = explode('|||', $param);
            if (!empty($value))
            {
                list($field, $iso) = explode('¤', $column);
                if (!empty($iso))
                {
                    $where_condition .= ' '.(!empty($where_condition) ? 'AND' : 'WHERE').' agl.'.$field.' LIKE "%'.$value.'%"';
                }
                else
                {
                    if ($field == 'is_color_group')
                    {
                        $value = (int) (strtolower($value) == 'oui' ? '1' : '0');
                    }
                    $where_condition .= ' '.(!empty($where_condition) ? 'AND' : 'WHERE').' ag.'.$field.' = "'.$value.'"';
                }
            }
        }
    }

    if (!empty($iso))
    {
        $id_lang = Language::getIdByIso($iso);
    }

    $sql = 'SELECT * FROM '._DB_PREFIX_.'attribute_group_lang';
    $rows = Db::getInstance()->ExecuteS($sql);
    $names = array();
    foreach ($rows as $row)
    {
        $names[$row['id_attribute_group']][$row['id_lang']]['name'] = $row['name'];
        $names[$row['id_attribute_group']][$row['id_lang']]['public_name'] = $row['public_name'];
    }

    $xml = '';
    $cols = '';
    $filters = '';
    foreach ($languages as $lang)
    {
        $cols .= '<column id="name¤'.$lang['iso_code'].'" width="100" type="edtxt" align="left" sort="str">'._l('Name').' '.strtoupper($lang['iso_code']).'</column>
<column id="public_name¤'.$lang['iso_code'].'" width="100" type="edtxt" align="left" sort="str">'._l('Public name').' '.strtoupper($lang['iso_code']).'</column>';
        $filters .= ',#text_filter,#text_filter';
    }

    $sql = 'SELECT DISTINCT agl.`name`, ag.*, agl.*
        FROM `'._DB_PREFIX_.'attribute_group` ag
        LEFT JOIN `'._DB_PREFIX_.'attribute_group_lang` agl
            ON (ag.`id_attribute_group` = agl.`id_attribute_group` AND `id_lang` = '.(int) $id_lang.')';
    if (!empty($where_condition))
    {
        $sql .= $where_condition;
    }
    $sql .= ' ORDER BY ag.`id_attribute_group` DESC'.(empty($all_groups) && empty($where_condition) ? ' LIMIT '._s('CAT_WIN_ATTRIBUTE_GROUP_LIMIT') : '');
    $groups = Db::getInstance()->executeS($sql);

    foreach ($groups as $row)
    {
        $xml .= "<row id='".$row['id_attribute_group']."'>";
        $xml .= '<cell style="color:#999999">'.$row['id_attribute_group'].'</cell>';
        if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
        {
            $xml .= '<cell>'.$row['position'].'</cell>';
        }
        if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
        {
            $xml .= '<cell><![CDATA['.$row['group_type'].']]></cell>';
        }
        $xml .= '<cell><![CDATA['.$row['is_color_group'].']]></cell>';
        foreach ($languages as $lang)
        {
            @$xml .= '<cell><![CDATA['.$names[$row['id_attribute_group']][$lang['id_lang']]['name'].']]></cell>';
            @$xml .= '<cell><![CDATA['.$names[$row['id_attribute_group']][$lang['id_lang']]['public_name'].']]></cell>';
        }
        $xml .= '<cell></cell>';
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
<call command="attachHeader"><param><![CDATA[#text_filter,<?php echo version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? '#text_filter,#select_filter,' : ''; ?>#select_filter<?php echo $filters; ?>]]></param></call>
</beforeInit>
<column id="id_attribute_group" width="40" type="ro" align="right" sort="int"><?php echo _l('ID'); ?></column>
<?php
if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
{
    ?>
<column id="position" width="40" type="ro" align="right" sort="int"><?php echo _l('Position'); ?></column>
<column id="group_type" width="60" type="coro" align="center" sort="str"><?php echo _l('Type'); ?><option value="select"><![CDATA[<?php echo _l('select'); ?>]]></option><option value="radio"><![CDATA[<?php echo _l('radio'); ?>]]></option><option value="color"><![CDATA[<?php echo _l('color'); ?>]]></option></column>
<?php
}?>
<column id="is_color_group" width="60" type="coro" align="center" sort="int"><?php echo _l('Color group?'); ?><option value="0"><![CDATA[<?php echo _l('No'); ?>]]></option>
<option value="1"><![CDATA[<?php echo _l('Yes'); ?>]]></option></column>
<?php
    echo $cols;
?>
</head>
<?php
    echo '<userdata name="uisettings">'.uisettings::getSetting('cat_win-attribute_group').'</userdata>'."\n";
    echo $xml;
?>
</rows>
