<?php

$id_lang = (int) Tools::getValue('id_lang');
$idlist = (Tools::getValue('idlist', 0));

function getRowsFromDB()
{
    global $id_lang,$idlist;

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
        SELECT pl.*
        FROM '._DB_PREFIX_.'product_lang pl
            '.((!_s('CAT_PROD_LANGUAGE_ALL')) ? ' INNER JOIN '._DB_PREFIX_.'lang l ON (pl.id_lang = l.id_lang AND l.active = 1)' : '').'
            '.(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? ' INNER JOIN '._DB_PREFIX_.'product_shop ps ON (ps.id_product = pl.id_product AND ps.id_shop = pl.id_shop) ' : '').'
        WHERE pl.id_product IN ('.pInSQL($idlist).') AND pl.id_lang IN (SELECT DISTINCT(id_lang) FROM '._DB_PREFIX_.'lang) 
        ORDER BY pl.id_product, pl.id_lang';
    if (SCMS)
    {
        $sql .= ',id_shop';
    }
    $res = Db::getInstance()->ExecuteS($sql);
    $xml = '';
    foreach ($res as $row)
    {
        $url = getUrl($row['id_product'], $row['id_lang'], (SCMS ? $row['id_shop'] : '0'));

        $xml .= "<row id='".$row['id_product'].'_'.$row['id_lang'].(SCMS ? '_'.$row['id_shop'] : '')."'>";
        $xml .= '<userdata name="url"><![CDATA['.$url.']]></userdata>';
        $xml .= '<cell>'.$row['id_product'].'</cell>';
        if (SCMS)
        {
            $xml .= '<cell>'.$array_shops[$row['id_shop']].'</cell>';
        }
        $xml .= '<cell>'.$array_langs[$row['id_lang']].'</cell>';
        $xml .= '<cell><![CDATA['.$row['name'].']]></cell>';
        $xml .= '<cell><![CDATA['.$row['link_rewrite'].']]></cell>';
        $meta_title_length = (int) strlen($row['meta_title']);
        if ($meta_title_length >= _s('CAT_SEO_META_TITLE_COLOR'))
        {
            $xml .= "<cell style='background-color: #FE9730'><![CDATA[".$row['meta_title'].']]></cell>';
        }
        elseif ($meta_title_length > 0 && $meta_title_length < _s('CAT_SEO_META_TITLE_COLOR_MIN'))
        {
            $xml .= "<cell style='background-color: #a7e1f7'><![CDATA[".$row['meta_title'].']]></cell>';
        }
        else
        {
            $xml .= '<cell><![CDATA['.$row['meta_title'].']]></cell>';
        }
        $xml .= '<cell><![CDATA['.strlen($row['meta_title']).']]></cell>';
        $xml .= '<cell>0</cell>';
        $xml .= '<cell><![CDATA['.$row['meta_description'].']]></cell>';
        $xml .= '<cell><![CDATA['.strlen($row['meta_description']).']]></cell>';
        $xml .= '<cell>0</cell>';
        $xml .= '<cell><![CDATA['.$row['meta_keywords'].']]></cell>';
        $xml .= '<cell><![CDATA['.strlen($row['meta_keywords']).']]></cell>';
        $xml .= '</row>';
    }

    return $xml;
}

$cache_product = array();
$link = new Link();
function getUrl($id_product, $id_lang, $id_shop = 0)
{
    global $cache_product,$link;
    $url = '';
    if (empty($cache_product[$id_product.'_'.$id_shop]))
    {
        if (SCMS)
        {
            $cache_product[$id_product] = new Product((int) $id_product, false, null, (int) $id_shop);
        }
        else
        {
            $cache_product[$id_product] = new Product((int) $id_product, false, null);
        }
    }
    $p = $cache_product[$id_product];

    $alias = $p->link_rewrite[$id_lang];
    $category = '';
    if ($p->id_category_default)
    {
        $category = Category::getLinkRewrite((int) $p->id_category_default, (int) $id_lang);
    }
    $force = (bool) Configuration::get('PS_REWRITING_SETTINGS');
    if (SCMS)
    {
        $url = $link->getProductLink($p->id, $alias, $category, $p->ean13, $id_lang, (int) $id_shop, 0, $force);
    }
    else
    {
        if (!defined('_PS_BASE_URL_'))
        {
            define('_PS_BASE_URL_', Tools::getShopDomain(true));
        }
        if (version_compare(_PS_VERSION_, '1.6.0.0', '>='))
        {
            $url = $link->getProductLink($p->id, $alias, $category, $p->ean13, $id_lang, $id_shop, 0, $force);
        }
        else
        {
            $url = $link->getProductLink($p->id, $alias, $category, $p->ean13, $id_lang, 0, 0, $force);
        }
    }

    return $url;
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
<call command="attachHeader"><param><![CDATA[#numeric_filter<?php if (SCMS){ ?>,#select_filter<?php } ?>,#select_filter,#text_filter,#text_filter,#text_filter,#numeric_filter,#numeric_filter,#text_filter,#numeric_filter,#numeric_filter,#text_filter,#numeric_filter]]></param></call>
</beforeInit>
<column id="id_product" width="40" type="ro" align="right" sort="int"><?php echo _l('ID'); ?></column>
<?php if (SCMS){ ?>
<column id="shop" width="100" type="ro" align="left" sort="int"><?php echo _l('Shop'); ?></column>
<?php } ?>
<column id="lang" width="60" type="ro" align="center" sort="str"><?php echo _l('Lang'); ?></column>
<column id="name" width="120" type="ed" align="left" sort="str"><?php echo _l('Name'); ?></column>
<column id="link_rewrite" width="120" type="ed" align="left" sort="str"><?php echo _l('Link rewrite'); ?></column>

<column id="meta_title" width="120" type="ed" align="left" sort="str"><?php echo _l('META title'); ?></column>
<column id="meta_title_width" width="60" type="ro" align="right" sort="str"><?php echo _l('META title length'); ?></column>
<column id="meta_title_pixel_width" width="60" type="ro" align="right" sort="str"><?php echo _l('META title pixel length'); ?></column>
<column id="meta_description" width="200" type="txttxt" align="left" sort="str"><?php echo _l('META description'); ?></column>
<column id="meta_description_width" width="60" type="ro" align="right" sort="str"><?php echo _l('META description length'); ?></column>
<column id="meta_description_pixel_width" width="60" type="ro" align="right" sort="str"><?php echo _l('META description pixel length'); ?></column>
<column id="meta_keywords" width="120" type="ed" align="left" sort="str"><?php echo _l('META keywords'); ?></column>
<column id="meta_keywords_width" width="40" type="ro" align="right" sort="str"><?php echo _l('META keywords length'); ?></column>
<afterInit>
<call command="enableMultiselect"><param>1</param></call>
</afterInit>
</head>
<?php
    echo '<userdata name="uisettings">'.uisettings::getSetting('cat_PdtSeo').'</userdata>'."\n";
    echo $xml;
?>
</rows>
