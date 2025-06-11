<?php

$id_lang = (int) Tools::getValue('id_lang');
$shop_ids = pInSQl(Tools::getValue('idshop', 0));

$view = Tools::getValue('view', 'grid_light');
$grids = SCI::getGridViews('supplier');

$exportedCms = array();
$cdata = (isset($_COOKIE['cg_sup_treegrid_col_'.$view]) ? $_COOKIE['cg_sup_treegrid_col_'.$view] : '');
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
$colSettings = SCI::getGridFields('supplier');

$tempCountries = Db::getInstance()->executeS('SELECT cl.* 
                                            FROM '._DB_PREFIX_.'country c
                                            LEFT JOIN '._DB_PREFIX_.'country_lang cl ON (cl.id_country = c.id_country)
                                            WHERE cl.id_lang = '.(int) $id_lang);

$colSettings['id_country']['options'] = array_column($tempCountries, 'name', 'id_country');
$colSettings['id_country']['options'][0] = '-';

$tempStates = Db::getInstance()->executeS('SELECT g.id_state, cl.name as cname, g.name as sname, g.id_country, CONCAT(cl.name," - ",g.name) AS name
                FROM '._DB_PREFIX_.'state g
                    INNER JOIN '._DB_PREFIX_."country_lang cl ON (cl.id_country = g.id_country AND cl.id_lang = '".(int) $id_lang."')".
                    ((SCMS && SCI::getSelectedShop() > 0) ? ' INNER JOIN '._DB_PREFIX_."country_shop cs ON (cs.id_country = g.id_country AND cs.id_shop = '".(int) SCI::getSelectedShop()."') " : "")."
                ORDER BY  cl.name ASC, g.name ASC");
$colSettings['id_state']['options'] = array_column($tempStates, 'name', 'id_state');
$colSettings['id_state']['options'][0] = '-';

function getColSettingsAsXML()
{
    global $cols, $colSettings, $view;

    $uiset = uisettings::getSetting('sup_grid_'.$view);
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

function getSuppliers()
{
    global $sql, $col, $id_lang, $cols, $view, $colSettings, $user_lang_iso, $fields, $fields_lang, $fieldsWithHTML, $shop_ids, $dd;
    $fields = array('id_supplier', 'name', 'date_add', 'date_upd', 'active', 'nb_products');
    $fields_lang = array('description', 'meta_title', 'meta_description', 'meta_keywords');
    $fields_address = array( 'id_address', 'phone', 'phone_mobile', 'alias', 'firstname', 'lastname', 'company', 'address1', 'address2', 'postcode', 'city', 'id_state', 'id_country', 'dni', 'other');
    $return = $fieldsWithHTML = $sql_fields = array();

    foreach ($cols as $col)
    {
        switch (true) {
            case sc_in_array($col, $fields, 'fields'):
                switch ($col) {
                    case 'nb_products':
                        $sql_fields[] = '(SELECT COUNT(id_product) FROM '._DB_PREFIX_.'product p WHERE p.id_supplier = sup.id_supplier) AS '.$col;
                        break;
                    default:
                        $sql_fields[] = 'sup.`'.bqSQL($col).'`';
                }
                break;
            case sc_in_array($col, $fields_lang, 'fields_lang'):
                $sql_fields[] = 'supl.`'.bqSQL($col).'`';
                break;
            case sc_in_array($col, $fields_address, 'fields_address'):
                $sql_fields[] = 'addr.`'.bqSQL($col).'`';
                break;
        }
    }
    $sql_fields = implode(',', $sql_fields);
    if (!empty($sql_fields))
    {
        $sql = 'SELECT '.$sql_fields.' 
                FROM '._DB_PREFIX_.'supplier sup
                LEFT JOIN '._DB_PREFIX_.'supplier_lang supl ON (supl.id_supplier= sup.id_supplier AND supl.id_lang='.(int) $id_lang.')
                LEFT JOIN '._DB_PREFIX_.'address addr ON (addr.id_supplier= sup.id_supplier)
                WHERE 1=1 
                ORDER BY sup.id_supplier DESC';
        if (version_compare(_PS_VERSION_, '1.5.0.10', '>'))
        {
            $sql = 'SELECT '.$sql_fields.' 
                    FROM '._DB_PREFIX_.'supplier sup
                    LEFT JOIN '._DB_PREFIX_.'supplier_lang supl ON (supl.id_supplier= sup.id_supplier AND supl.id_lang='.(int) $id_lang.')
                    LEFT JOIN '._DB_PREFIX_.'supplier_shop sups ON (sups.id_supplier= sup.id_supplier)
                    LEFT JOIN '._DB_PREFIX_.'address addr ON (addr.id_supplier= sup.id_supplier)
                    WHERE 1=1 
                    AND sups.id_shop IN ('.pInSQL($shop_ids).')
                    ORDER BY sup.id_supplier DESC';
        }

        $dd = $sql;
//        die($sql);
        $res = Db::getInstance()->ExecuteS($sql);
        if (!empty($res))
        {
            $currentTime = time();
            if (in_array('image', $cols))
            {
                $id_shop = (int) SCI::getSelectedShop();
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
            }
            foreach ($res as $supRow)
            {
                $return[] = '<row id="'.$supRow['id_supplier'].'">';
                $return[] = "\t<userdata name=\"id_supplier\">".(int) $supRow['id_supplier'].'</userdata>';
                foreach ($cols as $key => $col)
                {
                    switch ($col) {
                        case 'id':
                            $return[] = "\t<cell>".$supRow['id_supplier'].'</cell>'; //  style=\"color:tan\"
                            break;
                        case 'image':
                            $to_img = rtrim($shop_url, '/').'/img/su/'.$supRow['id_supplier'].'.jpg';
                            $path = _PS_SUPP_IMG_DIR_.$supRow['id_supplier'].'.jpg';
                            if (file_exists($path))
                            {
                                $return[] = "\t".'<cell><![CDATA[<img loading="lazy" src="'.$to_img.'?time='.$currentTime.'" width="auto" height="80"/>]]></cell>"';
                            }
                            else
                            {
                                $return[] = "\t<cell></cell>";
                            }
                            break;
                        case 'meta_title':
                        case 'meta_description':
                        case 'meta_keywords':
                            $return[] = "\t<cell><![CDATA[".$supRow[$col].']]></cell>';
                            break;
                        default:
                            if (sc_array_key_exists('buildDefaultValue', $colSettings[$col]) && $colSettings[$col]['buildDefaultValue'] != '')
                            {
                                if ($colSettings[$col]['buildDefaultValue'] == 'ID')
                                {
                                    $return[] = "\t<cell>ID".$supRow['id_supplier'].'</cell>';
                                }
                            }
                            else
                            {
                                if ($supRow[$col] == '' || $supRow[$col] === 0 || $supRow[$col] === 1)
                                { // opti perf is_numeric($supRow[$col]) ||
                                    $return[] = "\t<cell>".$supRow[$col].'</cell>';
                                }
                                else
                                {
                                    $return[] = "\t<cell><![CDATA[".$supRow[$col].']]></cell>';
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

$suppliers = getSuppliers();
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

$uiset = uisettings::getSetting('sup_grid_'.$view);
if (!empty($uiset))
{
    $tmp = explode('|', $uiset);
    $uiset = '|'.$tmp[1].'||'.$tmp[3];
}
echo '<userdata name="uisettings">'.$uiset.'</userdata>'."\n";
echo '<userdata name="LIMIT_SMARTRENDERING">'.(int) _s('SUP_SUPPLIER_LIMIT_SMARTRENDERING')."</userdata>\n";
echo $suppliers;
if (isset($_GET['DEBUG']))
{
    echo '<az><![CDATA['.$dd.']]></az>';
}
echo '</rows>';
