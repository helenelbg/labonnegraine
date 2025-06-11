<?php

$filter = (int) Tools::getValue('filter', 0);
$id_category = (int) Tools::getValue('id_category');
$id_lang = (int) Tools::getValue('id_lang');
$product_list = Tools::getValue('product_list');
$hasPosition = isField('position', 'feature_product') ? true : false;
$multiple = false;
if (strpos($product_list, ',') !== false)
{
    $multiple = true;
}

    if ($filter)
    {
        $sql = '
        SELECT id_product
        FROM `'._DB_PREFIX_.'category_product`
        WHERE id_category='.(int) $id_category;
        $res = Db::getInstance()->ExecuteS($sql);
        foreach ($res as $val)
        {
            $productList[] = (int) $val['id_product'];
        }
        $sql = '
        SELECT id_feature
        FROM `'._DB_PREFIX_.'feature_product`
        WHERE id_product IN ('.join(',', $productList).')';
        $res = Db::getInstance()->ExecuteS($sql);
        foreach ($res as $val)
        {
            $featuresSelection[] = (int) $val['id_feature'];
        }
    }

    if (SCMS)
    {
        $id_shop = SCI::getSelectedShop();
        if (empty($id_shop))
        {
            $id_shop = SCI::getConfigurationValue('PS_SHOP_DEFAULT');
        }
    }

    if (SCMS)
    {
        $sql = 'SELECT fl.*
                FROM '._DB_PREFIX_.'feature_lang fl
                    INNER JOIN '._DB_PREFIX_."feature_shop fs ON (fl.id_feature=fs.id_feature AND fs.id_shop='".(int) $id_shop."')
                WHERE fl.id_lang=".(int) $id_lang.
                    ($filter && count($featuresSelection) ? ' AND fl.id_feature IN ('.join(',', $featuresSelection).')' : '');
    }
    else
    {
        $sql = 'SELECT *
                FROM '._DB_PREFIX_.'feature_lang
                WHERE id_lang='.(int) $id_lang.
                        ($filter && count($featuresSelection) ? ' AND id_feature IN ('.join(',', $featuresSelection).')' : '');
    }

    $rows = Db::getInstance()->ExecuteS($sql);
    $names = array();
    foreach ($rows as $row)
    {
        $names[$row['id_feature']][$row['id_lang']]['name'] = $row['name'];
    }
    $xml = '';

    $Features = Db::getInstance()->executeS('
        SELECT DISTINCT f.id_feature, f.*, fl.*
        FROM `'._DB_PREFIX_.'feature` f
        '.(SCMS ? 'INNER JOIN '._DB_PREFIX_."feature_shop fs ON (f.id_feature=fs.id_feature AND fs.id_shop='".(int) $id_shop."')" : '').'
        INNER JOIN `'._DB_PREFIX_.'feature_lang` fl ON (f.`id_feature` = fl.`id_feature` AND fl.`id_lang` = '.(int) $id_lang.')
        ORDER BY f.`position` ASC');
    foreach ($Features as $row)
    {
        $nb_of_values = Db::getInstance()->getValue('SELECT COUNT(*) 
                                                    FROM '._DB_PREFIX_.'feature_product 
                                                    WHERE id_product IN ('.pInSQL($product_list).') 
                                                    AND id_feature = '.(int) $row['id_feature']);
        if (!$filter || ($filter && in_array($row['id_feature'], $featuresSelection)))
        {
            $customValues = array();
            if (!$multiple && !empty($product_list))
            {
                $langs = array();
                $sql = 'SELECT fvl.*,fp.*
                    FROM '._DB_PREFIX_.'feature_value_lang fvl
                        INNER JOIN '._DB_PREFIX_.'feature_value fv ON (fvl.id_feature_value = fv.id_feature_value)
                            INNER JOIN '._DB_PREFIX_.'feature_product fp ON (fp.id_feature_value = fv.id_feature_value)
                    WHERE fp.id_product IN ('.pInSQL($product_list).")
                        AND fp.id_feature = '".(int) $row['id_feature']."'
                        AND fv.custom=1";
                if ($hasPosition)
                {
                    $sql .= ' ORDER BY fp.position ASC';
                }
                $row_langs = Db::getInstance()->ExecuteS($sql);

                foreach ($row_langs as $row_lang)
                {
                    if (!isset($langs[$row_lang['id_lang']]))
                    {
                        $langs[$row_lang['id_lang']] = '';
                    }
                    $langs[$row_lang['id_lang']] .= $row_lang['value'];
                    if (!isset($customValues[$row['id_feature']][$row_lang['id_lang']]))
                    {
                        $customValues[$row['id_feature']][$row_lang['id_lang']] = '';
                    }
                    $customValues[$row['id_feature']][$row_lang['id_lang']] .= $row_lang['value'].'<br/>';
                }
            }

            $xml .= "<row id='".$row['id_feature']."'>";
            $xml .= '<cell style="color:#999999;'.(!empty($nb_of_values) ? 'background-color:#b3f5bb;' : '').'">'.$row['id_feature'].'</cell>';
            @$xml .= '<cell '.(!empty($nb_of_values) ? 'style="background-color:#b3f5bb;"' : '').'><![CDATA['.$names[$row['id_feature']][$id_lang]['name'].']]></cell>';
            if (!$multiple && !empty($product_list) && !empty($customValues))
            {
                foreach ($languages as $lang)
                {
                    $xml .= '<cell style="line-height:1.3em;"><![CDATA['.trim($customValues[$row['id_feature']][$lang['id_lang']], '<br/>').']]></cell>';
                }
            }
            $xml .= '</row>';
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
        </beforeInit>
        <column id="id_feature" width="40" type="ro" align="right" sort="int"><?php echo _l('ID'); ?></column>
        <column id="name" width="150" type="ro" align="left" sort="str"><?php echo _l('Name'); ?></column>
        <?php if (!$multiple && !empty($product_list))
{
    foreach ($languages as $lang)
    {
        echo '<column id="custom_'.$lang['iso_code'].'" width="100" type="ed" align="left" sort="str">'._l('Custom').'_'.$lang['iso_code'].'</column>';
    }
} ?>
        <afterInit>
            <call command="enableHeaderMenu"></call>
            <call command="attachHeader"><param><![CDATA[#text_filter,#text_filter<?php if (!$multiple && !empty($product_list))
{
    foreach ($languages as $lang)
    {
        echo ',#text_filter';
    }
} ?>]]></param></call>
        </afterInit>
    </head>
    <?php
    echo $xml;
    ?>
</rows>
