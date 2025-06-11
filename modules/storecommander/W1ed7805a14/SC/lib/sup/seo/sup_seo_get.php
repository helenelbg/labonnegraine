<?php

$idlist = (string) Tools::getValue('idlist', 0);
$link = new Link();

function getRowsFromDB($idlist, $link)
{
    $array_langs = array();
    $langs = Language::getLanguages(false);
    foreach ($langs as $lang)
    {
        $array_langs[$lang['id_lang']] = strtoupper($lang['iso_code']);
    }

    $sql = 'SELECT s.name, sl.*, ssho.id_shop
            FROM '._DB_PREFIX_.'supplier_lang sl
            '.((!_s('SUP_SUPPLIER_LANGUAGE_ALL')) ? ' INNER JOIN '._DB_PREFIX_.'lang l ON (pl.id_lang = l.id_lang AND l.active = 1)' : '').'
            LEFT JOIN '._DB_PREFIX_.'supplier s ON (sl.id_supplier = s.id_supplier)
            '.(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? ' LEFT JOIN '._DB_PREFIX_.'supplier_shop ssho ON (ssho.id_supplier = s.id_supplier)' : '').'
            WHERE sl.id_supplier IN ('.pInSQL($idlist).') 
            '.(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? ' AND ssho.id_shop IN ('.pInSQL(SCI::getSelectedShopActionList(true)).')' : ' ').'
            ORDER BY sl.id_supplier, sl.id_lang';
    $res = Db::getInstance()->ExecuteS($sql);

    $xml = '';
    if (!empty($res))
    {
        foreach ($res as $row)
        {
            if (SCMS)
            {
                $url = $link->getSupplierLink($row['id_supplier'], null, $row['id_lang'], $row['id_shop']);
            }
            else
            {
                $url = $link->getSupplierLink($row['id_supplier'], null, $row['id_lang']);
            }

            $xml .= "<row id='".$row['id_supplier'].'_'.$row['id_lang']."'>";
            $xml .= '<userdata name="url"><![CDATA['.$url.']]></userdata>';
            $xml .= '<cell>'.$row['id_supplier'].'</cell>';
            $xml .= '<cell>'.$row['name'].'</cell>';
            $xml .= '<cell>'.$array_langs[$row['id_lang']].'</cell>';
            $meta_title_length = (int) strlen($row['meta_title']);
            if ($meta_title_length >= _s('MAN_SEO_META_TITLE_COLOR'))
            {
                $xml .= "<cell style='background-color: #FE9730'><![CDATA[".$row['meta_title'].']]></cell>';
            }
            elseif ($meta_title_length > 0 && $meta_title_length < _s('MAN_SEO_META_TITLE_COLOR_MIN'))
            {
                $xml .= "<cell style='background-color: #a7e1f7'><![CDATA[".$row['meta_title'].']]></cell>';
            }
            else
            {
                $xml .= '<cell><![CDATA['.$row['meta_title'].']]></cell>';
            }
            $xml .= '<cell><![CDATA['.strlen($row['meta_title']).']]></cell>';
            $xml .= '<cell><![CDATA['.$row['meta_description'].']]></cell>';
            $xml .= '<cell><![CDATA['.strlen($row['meta_description']).']]></cell>';
            $xml .= '<cell><![CDATA['.$row['meta_keywords'].']]></cell>';
            $xml .= '<cell><![CDATA['.strlen($row['meta_keywords']).']]></cell>';
            $xml .= '</row>';
        }
    }

    return $xml;
}

$xml = '';
if (!empty($idlist))
{
    $xml = getRowsFromDB($idlist, $link);
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

    ?>
<rows id="0">
<head>
<afterInit>
<call command="attachHeader"><param><![CDATA[#text_filter,#select_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter]]></param></call>
</afterInit>
<column id="id_supplier" width="40" type="ro" align="right" sort="int"><?php echo _l('ID'); ?></column>
<column id="name" width="120" type="ed" align="center" sort="str"><?php echo _l('Name'); ?></column>
<column id="lang" width="60" type="ro" align="center" sort="str"><?php echo _l('Lang'); ?></column>
<column id="meta_title" width="120" type="ed" align="left" sort="str"><?php echo _l('META title'); ?></column>
<column id="meta_title_width" width="40" type="ro" align="right" sort="str"><?php echo _l('META title length'); ?></column>
<column id="meta_description" width="200" type="ed" align="left" sort="str"><?php echo _l('META description'); ?></column>
<column id="meta_description_width" width="40" type="ro" align="right" sort="str"><?php echo _l('META description length'); ?></column>
<column id="meta_keywords" width="120" type="ed" align="left" sort="str"><?php echo _l('META keywords'); ?></column>
<column id="meta_keywords_width" width="40" type="ro" align="right" sort="str"><?php echo _l('META keywords length'); ?></column>
<afterInit>
<call command="enableMultiselect"><param>1</param></call>
</afterInit>
</head>
<?php
    echo '<userdata name="uisettings">'.uisettings::getSetting('sup_SupplierSeo').'</userdata>'."\n";
    echo $xml;
?>
</rows>
