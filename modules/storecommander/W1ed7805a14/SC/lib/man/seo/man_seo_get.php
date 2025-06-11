<?php

$id_lang = (int) Tools::getValue('id_lang');
$idlist = (Tools::getValue('idlist', 0));
$link = new Link();

function getRowsFromDB()
{
    global $id_lang,$idlist,$link;

    $array_langs = array();
    $langs = Language::getLanguages(false);
    foreach ($langs as $lang)
    {
        $array_langs[$lang['id_lang']] = strtoupper($lang['iso_code']);
    }

    if (SCMS)
    {
        $array_shops = array();
        $shops = Shop::getShops(false);
        foreach ($shops as $shop)
        {
            $shop['name'] = str_replace('&', _l('and'), $shop['name']);
            $array_shops[$shop['id_shop']] = $shop['name'];
        }
    }

    $sql = '
        SELECT m.name, ml.*'.(SCMS ? ',ms.id_shop' : '').'
        FROM '._DB_PREFIX_.'manufacturer_lang ml
            '.((!_s('MAN_PROD_LANGUAGE_ALL')) ? ' INNER JOIN '._DB_PREFIX_.'lang l ON (pl.id_lang = l.id_lang AND l.active = 1)' : '').'
            LEFT JOIN '._DB_PREFIX_.'manufacturer m ON (ml.id_manufacturer = m.id_manufacturer)
            '.(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? ' INNER JOIN '._DB_PREFIX_.'manufacturer_shop ms ON (ms.id_manufacturer = ml.id_manufacturer) ' : '').'
        WHERE ml.id_manufacturer IN ('.pInSQL($idlist).')
        ORDER BY ml.id_manufacturer, ml.id_lang';

    $res = Db::getInstance()->ExecuteS($sql);

    $xml = '';
    foreach ($res as $row)
    {
        if (SCMS)
        {
            $url = $link->getManufacturerLink($row['id_manufacturer'], null, $row['id_lang'], $row['id_shop']);
        }
        else
        {
            $url = $link->getManufacturerLink($row['id_manufacturer'], null, $row['id_lang']);
        }

        $xml .= "<row id='".$row['id_manufacturer'].'_'.$row['id_lang'].(SCMS ? '_'.$row['id_shop'] : '')."'>";
        $xml .= '<userdata name="url"><![CDATA['.$url.']]></userdata>';
        $xml .= '<cell>'.$row['id_manufacturer'].'</cell>';
        if (SCMS)
        {
            $xml .= '<cell>'.$array_shops[$row['id_shop']].'</cell>';
        }
        $xml .= '<cell><![CDATA['.$row['name'].']]></cell>';
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
    if (!empty($idlist))
    {
        $xml = getRowsFromDB();
    }
    ?>
<rows id="0">
<head>
<beforeInit>
<call command="attachHeader"><param><![CDATA[#text_filter<?php if (SCMS){ ?>,#select_filter<?php } ?>,#select_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter]]></param></call>
</beforeInit>
<column id="id_manufacturer" width="40" type="ro" align="right" sort="int"><?php echo _l('ID'); ?></column>
<?php if (SCMS){ ?>
    <column id="shop" width="100" type="ro" align="left" sort="int"><?php echo _l('Shop'); ?></column>
<?php } ?>
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
//  format="%Y-%m-%d 00:00:00"
    echo '<userdata name="uisettings">'.uisettings::getSetting('man_ManufacturerSeo').'</userdata>'."\n";
    echo $xml;
?>
</rows>
