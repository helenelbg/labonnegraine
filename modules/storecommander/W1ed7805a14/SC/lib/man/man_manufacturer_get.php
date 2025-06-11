<?php

$id_lang = (int) Tools::getValue('id_lang');
$shop_ids = pInSQl(Tools::getValue('idshop', 0));

$view = Tools::getValue('view', 'grid_light');
$grids = SCI::getGridViews('manufacturer');

$exportedCms = array();
$cdata = (isset($_COOKIE['cg_man_treegrid_col_'.$view]) ? $_COOKIE['cg_man_treegrid_col_'.$view] : '');
//check validity
$check = explode(',', $cdata);
foreach ($check as $c)
{
    if ($c == 'undefined')
    {
        $cdata = '';
        break;
    }
}
if ($cdata != '')
{
    $grids[$view] = $cdata;
}

$cols = explode(',', $grids[$view]);

$colSettings = array();
$colSettings = SCI::getGridFields('manufacturer');

function getColSettingsAsXML()
{
    global $cols, $colSettings, $view;

    $uiset = uisettings::getSetting('man_grid_'.$view);
    $hidden = $sizes = array();
    if (!empty($uiset))
    {
        $tmp = explode('|', $uiset);
        $tmp = explode('-', $tmp[2]);
        foreach ($tmp as $v)
        {
            $s = explode(':', $v);
            $sizes[$s[0]] = $s[1];
        }
        $tmp = explode('|', $uiset);
        $tmp = explode('-', $tmp[0]);
        foreach ($tmp as $v)
        {
            $s = explode(':', $v);
            $hidden[$s[0]] = $s[1];
        }
    }

    $xml = '';

    foreach ($cols as $id => $col)
    {
        $xml .= '<column id="'.$col.'"'.(sc_array_key_exists('format', $colSettings[$col]) ?
                ' format="'.$colSettings[$col]['format'].'"' : '').
            ' width="'.(sc_array_key_exists($col, $sizes) ? $sizes[$col] : $colSettings[$col]['width']).'"'.
            ' hidden="'.(sc_array_key_exists($col, $hidden) ? $hidden[$col] : 0).'"'.
            ' align="'.$colSettings[$col]['align'].'" type="'.$colSettings[$col]['type'].'" sort="'.$colSettings[$col]['sort'].'" color="'.$colSettings[$col]['color'].'">'.$colSettings[$col]['text'];
        if (!empty($colSettings[$col]['options']))
        {
            foreach ($colSettings[$col]['options'] as $k => $v)
            {
                $xml .= "\n\t".'<option value="'.str_replace('"', '\'', $k).'"><![CDATA['.$v.']]></option>';
            }
        }
        $xml .= (!empty($colSettings[$col]['options']) ? "\n" : '').'</column>'."\n";
    }

    return $xml;
}

function getFooterColSettings()
{
    global $cols, $colSettings;

    $footer = '';
    foreach ($cols as $id => $col)
    {
        if (sc_array_key_exists($col, $colSettings) && sc_array_key_exists('footer', $colSettings[$col]))
        {
            $footer .= $colSettings[$col]['footer'].',';
        }
        else
        {
            $footer .= ',';
        }
    }

    return $footer;
}

function getFilterColSettings()
{
    global $cols, $colSettings;

    $filters = '';
    foreach ($cols as $id => $col)
    {
        if ($colSettings[$col]['filter'] == 'na')
        {
            $colSettings[$col]['filter'] = '';
        }
        $filters .= $colSettings[$col]['filter'].',';
    }
    $filters = trim($filters, ',');

    return $filters;
}

function getManufacturers()
{
    global $sql, $col, $id_lang, $cols, $view, $colSettings, $user_lang_iso, $fields, $fields_lang, $fieldsWithHTML, $shop_ids, $dd;
    $fields = array('id_manufacturer', 'name', 'date_add', 'date_upd', 'active', 'nb_products');
    $fields_lang = array('meta_title', 'meta_description', 'meta_keywords');
    $return = $fieldsWithHTML = $sqlManufacturer = $sqlManufacturerLang = array();

    foreach ($cols as $col)
    {
        switch (true) {
            case sc_in_array($col, $fields, 'fields'):
                switch ($col) {
                    case 'nb_products':
                        $sqlManufacturer[] = '(SELECT COUNT(id_product) FROM '._DB_PREFIX_.'product p WHERE p.id_manufacturer = man.id_manufacturer) AS '.$col;
                        break;
                    default:
                        $sqlManufacturer[] = 'man.`'.bqSQL($col).'`';
                }
                break;
            case sc_in_array($col, $fields_lang, 'fields_lang'):
                switch ($col) {
                    default:
                        $sqlManufacturerLang[] = 'manl.`'.bqSQL($col).'`';
                }
                break;
        }
    }
    $sql_fields = implode(',', array_merge($sqlManufacturer, $sqlManufacturerLang));
    if (!empty($sql_fields))
    {
        if (version_compare(_PS_VERSION_, '1.5.0.10', '<'))
        {
            $sql = 'SELECT '.$sql_fields.' 
                    FROM '._DB_PREFIX_.'manufacturer man
                    LEFT JOIN '._DB_PREFIX_.'manufacturer_lang manl ON (manl.id_manufacturer= man.id_manufacturer AND manl.id_lang='.(int) $id_lang.')
                    WHERE 1=1 
                    ORDER BY man.id_manufacturer DESC';
        }
        else
        {
            $sql = 'SELECT '.$sql_fields.' 
                    FROM '._DB_PREFIX_.'manufacturer man
                    LEFT JOIN '._DB_PREFIX_.'manufacturer_lang manl ON (manl.id_manufacturer= man.id_manufacturer AND manl.id_lang='.(int) $id_lang.')
                    LEFT JOIN '._DB_PREFIX_.'manufacturer_shop mans ON (mans.id_manufacturer= man.id_manufacturer)
                    WHERE 1=1 
                    AND mans.id_shop IN ('.pInSQL($shop_ids).')
                    GROUP BY id_manufacturer
                    ORDER BY man.id_manufacturer DESC';
        }

        $dd = $sql;
//        die($sql);
        $res = Db::getInstance()->ExecuteS($sql);
        if (!empty($res))
        {
            foreach ($res as $manRow)
            {
                $return[] = '<row id="'.$manRow['id_manufacturer'].'">';
                $return[] = "\t<userdata name=\"id_manufacturer\">".(int) $manRow['id_manufacturer'].'</userdata>';

                foreach ($cols as $key => $col)
                {
                    switch ($col) {
                        case 'id':
                            $return[] = "\t<cell>".$manRow['id_manufacturer'].'</cell>'; //  style=\"color:tan\"
                            break;
                        case 'image':
                            $id_shop = (int) Configuration::get('PS_SHOP_DEFAULT');
                            if (version_compare(_PS_VERSION_, '1.5.0.10', '>='))
                            {
                                $shopUrl = new ShopUrl($id_shop);
                                $shop_url = $shopUrl->getURL(Configuration::get('PS_SSL_ENABLED'));
                            }
                            else
                            {

                                if (Configuration::get('PS_SSL_ENABLED'))
                                {
                                    $shop_url ='https://'.$_SERVER['HTTP_HOST'].__PS_BASE_URI__;
                                }
                                else
                                {
                                    $shop_url = 'http://'.$_SERVER['HTTP_HOST'].__PS_BASE_URI__;
                                }
                            }
                            $to_img = '/img/m/'.$manRow['id_manufacturer'].'.jpg';
                            if (file_exists(_PS_MANU_IMG_DIR_.$manRow['id_manufacturer'].'.jpg'))
                            {
                                $return[] = "\t<cell><![CDATA[<img src=\"".$shop_url.$to_img.'?time='.time().'" width="100%"/>]]></cell>';
                            }
                            else
                            {
                                $return[] = "\t<cell></cell>";
                            }
                            break;
                        case 'meta_title':
                        case 'meta_description':
                        case 'meta_keywords':
                            $return[] = "\t<cell><![CDATA[".$manRow[$col].']]></cell>';
                            break;
                        default:
                            if (sc_array_key_exists('buildDefaultValue', $colSettings[$col]) && $colSettings[$col]['buildDefaultValue'] != '')
                            {
                                if ($colSettings[$col]['buildDefaultValue'] == 'ID')
                                {
                                    $return[] = "\t<cell>ID".$manRow['id_manufacturer'].'</cell>';
                                }
                            }
                            else
                            {
                                if ($manRow[$col] == '' || $manRow[$col] === 0 || $manRow[$col] === 1)
                                { // opti perf is_numeric($manRow[$col]) ||
                                    $return[] = "\t<cell>".$manRow[$col].'</cell>';
                                }
                                else
                                {
                                    $return[] = "\t<cell><![CDATA[".$manRow[$col].']]></cell>';
                                }
                            }
                    }
                }
                $return[] = "</row>\n";
            }
        }
    }

    return implode("\n", $return);
}

$manufacturers = getManufacturers();
if (stristr($_SERVER['HTTP_ACCEPT'], 'application/xhtml+xml'))
{
    header('Content-type: application/xhtml+xml');
}
else
{
    header('Content-type: text/xml');
}
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
echo "<rows><head>\n";
echo getColSettingsAsXML();
echo "<afterInit>\n";
echo "\t<call command=\"attachHeader\"><param>".getFilterColSettings()."</param></call>\n";
echo "\t<call command=\"attachFooter\"><param><![CDATA[".getFooterColSettings()."]]></param></call>\n";
echo "</afterInit>\n";
echo "</head>\n";

$uiset = uisettings::getSetting('man_grid_'.$view);
if (!empty($uiset))
{
    $tmp = explode('|', $uiset);
    $uiset = '|'.$tmp[1].'||'.$tmp[3];
}
echo '<userdata name="uisettings">'.$uiset.'</userdata>'."\n";
echo '<userdata name="LIMIT_SMARTRENDERING">'.(int) _s('CMS_PAGE_LIMIT_SMARTRENDERING')."</userdata>\n";
echo $manufacturers;
if (isset($_GET['DEBUG']))
{
    echo '<az><![CDATA['.$dd.']]></az>';
}
echo '</rows>';
