<?php

    $id_pack = Tools::getValue('id_pack', 0);
    $id_lang = (int) Tools::getValue('id_lang');
    $has_combination = (int) Tools::getValue('has_combination', 0);

    if ($has_combination)
    {
        $msg = _l('This product can not be a pack because it contains combinations');
        exitWithXmlMessageForGrid($msg);
    }
    
    SCI::messageNotCompatibleWithAdvancedPack($id_pack);

// SETTINGS, FILTERS AND COLONNES
$sourceGridFormat = SCI::getGridViews('proppackproduct');
$sql_gridFormat = $sourceGridFormat;
sc_ext::readCustomPropPackProductGridConfigXML('gridConfig');
$sourceGridFormat = $sourceGridFormat['grid_proppackproduct'];
$gridFormat = $sourceGridFormat;
$cols = explode(',', $gridFormat);
$all_cols = explode(',', $gridFormat);

$colSettings = array();
$colSettings = SCI::getGridFields('proppackproduct');
$colSettings = array_intersect_key($colSettings,array_flip($cols));
sc_ext::readCustomproppackproductGridConfigXML('colSettings');

function getFooterColSettings()
{
    global $cols,$colSettings;

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
    global $cols,$colSettings;

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

function getColSettingsAsXML()
{
    global $cols,$colSettings;

    $uiset = uisettings::getSetting('cat_proppackproduct');
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
            ' align="'.$colSettings[$col]['align'].'"
                    type="'.$colSettings[$col]['type'].'"
                    sort="'.$colSettings[$col]['sort'].'"
                    color="'.$colSettings[$col]['color'].'">'.$colSettings[$col]['text'];
        if (is_array($colSettings[$col]) && sc_array_key_exists('options', $colSettings[$col]) && is_array($colSettings[$col]['options']) && !empty($colSettings[$col]['options']))
        {
            foreach ($colSettings[$col]['options'] as $k => $v)
            {
                $xml .= '<option value="'.str_replace('"', '\'', $k).'"><![CDATA['.$v.']]></option>';
            }
        }
        $xml .= '</column>'."\n";
    }

    return $xml;
}

function generateValue($col, $row, $has_combi)
{
    $return = '';
    switch ($col){
        case 'id_image':
            if (file_exists(SC_PS_PATH_REL.'img/p/'.getImgPath((int) $row['id_product_item'], (int) $row['id_image'], _s('CAT_PROD_GRID_IMAGE_SIZE'))))
            {
                $return .=  "<cell><![CDATA[<img src='".SC_PS_PATH_REL.'img/p/'.getImgPath((int) $row['id_product_item'], (int) $row['id_image'], _s('CAT_PROD_GRID_IMAGE_SIZE'))."'/>]]></cell>";
            }
            else
            {
                $return .=  '<cell><![CDATA[<i class="fad fa-file-image" ></i>--]]></cell>';
            }
            break;
        case 'active':
            $return .=  '<cell style="background-color:'.($row['active'] ? '' : '#888888').'"><![CDATA['.($row['active'] ? _l('Yes') : _l('No')).']]></cell>';
            break;
        case 'quantity':
            $return .= '<cell style="background-color:'.($has_combi ? '#888888;color:#ffffff' : '').'"  type="'.($has_combi ? 'ro' : 'edn').'"><![CDATA['.($has_combi ? $row['all_quantities'] : $row['quantity']).']]></cell>';
            break;
        case 'stock_available':
            $return .= '<cell><![CDATA['.SCI::getProductQty((int) $row['id_product_item'], 0, null, SCI::getSelectedShop()).']]></cell>';
            break;
        case 'name':
            $return .= '<cell><![CDATA['.$row['name'].']]></cell>';
            break;
        case 'id':
            $return .= '<cell>'.$row['id_product_item'].'</cell>';
            break;
        default:
            $return .= '<cell><![CDATA['.$row[$col].']]></cell>';
            break;
    }

    return $return;
}

function getPdtPack()
{

    global $sql,$id_pack,$id_lang,$has_combi,$cols,$colSettings,$row;

    $sql = 'SELECT pk.*, SUM(pk.quantity) as all_quantities, p.reference,p.ean13,p.supplier_reference,p.upc,pl.name '.(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? ', ps.active, p.id_shop_default' : ', p.active').', img.id_image';
    sc_ext::readCustomproppackproductGridConfigXML('SQLSelectDataSelect');
    $sql .= ' FROM '._DB_PREFIX_.'pack pk
                    LEFT JOIN '._DB_PREFIX_.'product p ON (pk.id_product_item=p.id_product)
                    LEFT JOIN '._DB_PREFIX_.'product_lang pl ON (pk.id_product_item=pl.id_product AND pl.id_lang='.(int) $id_lang.' '.(SCMS ? (SCI::getSelectedShop() > 0 ? ' AND pl.id_shop='.(int) SCI::getSelectedShop() : ' AND pl.id_shop=p.id_shop_default ') : '').')
                    '.(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? 'INNER JOIN '._DB_PREFIX_.'product_shop ps ON (p.id_product=ps.id_product AND ps.id_shop='.(SCI::getSelectedShop() > 0 ? (int) SCI::getSelectedShop() : 'p.id_shop_default').')' : '').'
                    LEFT JOIN '._DB_PREFIX_."image img ON img.id_product = p.id_product AND img.cover = 1";
    sc_ext::readCustomproppackproductGridConfigXML('SQLSelectDataLeftJoin');
    $sql .= " WHERE pk.id_product_pack =" .(int) $id_pack . "
                GROUP BY pk.id_product_item
                ORDER BY pl.name ASC";
    $res = Db::getInstance()->ExecuteS($sql);

    $xml = '';
    foreach ($res as $row)
    {
        $combis = Product::getProductAttributesIds($row['id_product_item']);
        $has_combi = count($combis) > 0;

        $xml .= '<row id="'.$row['id_product_item'].'">';
        sc_ext::readCustomproppackproductGridConfigXML('rowUserData');
        sc_ext::readCustomproppackproductGridConfigXML('rowData');

        foreach ($cols as $field)
        {
            if (!empty($field) && !empty($colSettings[$field]))
            {
                $xml .= generateValue($field, $row, $has_combi);
            }
        }
        $xml .= '</row>';
    }
    return $xml;
}
$xml = getPdtPack();
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
echo '<rows><head>';
echo getColSettingsAsXML();
echo '<afterInit><call command="attachHeader"><param>'.getFilterColSettings().'</param></call>
            <call command="attachFooter"><param><![CDATA['.getFooterColSettings().']]></param></call></afterInit>';
echo '</head>'."\n";

echo '<userdata name="uisettings">'.uisettings::getSetting('cat_proppackproduct').'</userdata>'."\n";
sc_ext::readCustomproppackproductGridConfigXML('gridUserData');

echo $xml;
?>
</rows>

