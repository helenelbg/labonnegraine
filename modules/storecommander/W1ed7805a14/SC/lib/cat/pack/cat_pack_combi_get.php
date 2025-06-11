<?php

    $id_pack = Tools::getValue('id_pack', 0);
    $id_product = Tools::getValue('id_product', 0);
    $id_lang = (int) Tools::getValue('id_lang');

    // SETTINGS, FILTERS AND COLONNES
    $sourceGridFormat = SCI::getGridViews('proppackproduct');
    $sql_gridFormat = $sourceGridFormat;
    sc_ext::readCustomPropPackProductGridConfigXML('gridConfig');

    $sourceGridFormat = $sourceGridFormat['grid_proppackcombi'];

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
    $xml = '';
    foreach ($cols as $id => $col)
    {
        $xml .= '<column id="'.$col.'"'.(sc_array_key_exists('format', $colSettings[$col]) ?
                ' format="'.$colSettings[$col]['format'].'"' : '').
            ' width="'.$colSettings[$col]['width'].'"'.
            ' hidden="0"'.
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
        $xml .= '<userdata name="uisettings">'.uisettings::getSetting('cat_combi_pack').'</userdata>'."\n";
        $xml .= '</column>'."\n";
    }

    return $xml;
}

function generateValue($col, $row, $attributes, $id_product)
{
    global $colSettings,$id_lang;
    $return = '';

    switch ($col){
        case 'name':
            $name = [];
            foreach ($attributes as $attr)
            {
                if (!empty($attr['gp']) && !empty($attr['name']))
                {
                    $name[]= $attr['gp'].' : '.$attr['name'];
                }
            }
            $return .= '<cell><![CDATA['.implode(', ', $name).']]></cell>';
            break;
        case 'present':
            $return .= '<cell><![CDATA['.(!empty($row['id_product_attribute_item']) ? '1' : '0').']]></cell>';
            break;
        case 'id':
            $return .= '<cell>'.$row['id_product_attribute'].'</cell>';
            break;
        case 'quantity':
            $return .=  '<cell  type="'.(!empty($row['id_product_attribute_item']) ? 'edn' : 'ro').'"><![CDATA['.$row['quantity'].']]></cell>';
            break;
        case 'stock_available':
            $return .=  '<cell><![CDATA['.SCI::getProductQty((int) $id_product, (int) $row['id_product_attribute'], null, SCI::getSelectedShop()).']]></cell>';
            break;
        default:
            $return .= '<cell><![CDATA['.$row[$col].']]></cell>';
            break;
    }

    $return .= '<userdata name="idpack">'.$row['id_product_pack'].'</userdata>'."\n";

    return $return;
}


function getPdtCombiPack($id_product)
{
    global $id_pack,$id_lang,$id_product,$cols,$colSettings;

    $sql = 'SELECT pa.id_product_attribute,pa.reference,pa.ean13,pa.upc,pa.isbn,pk.*
                FROM '._DB_PREFIX_.'product_attribute pa
                    LEFT JOIN '._DB_PREFIX_."pack pk ON (pk.id_product_attribute_item=pa.id_product_attribute AND pk.id_product_pack =" .(int) $id_pack . ")";
    if(version_compare(_PS_VERSION_, '1.5.0.0', '>=')){
            $sql .= ' INNER JOIN '._DB_PREFIX_.'product_attribute_shop pas ON pa.id_product_attribute = pas.id_product_attribute AND pas.id_shop ='.(int)SCI::getSelectedShop().' ';
    }
    $sql.=" WHERE  pa.id_product='".(int) $id_product."'
                ORDER BY pa.id_product_attribute ASC";
    if(version_compare(_PS_VERSION_, '1.7.7.0', '>=')){
        $sql = str_replace('pk.*', 'pa.mpn,pk.*', $sql);
    }
    $res = Db::getInstance()->ExecuteS($sql);
    $xml = '';
    foreach ($res as $row)
    {
        $sql_attr = 'SELECT agl.name as gp, al.name';
        sc_ext::readCustomproppackproductGridConfigXML('SQLSelectDataSelect');
        $sql_attr .= ' FROM '._DB_PREFIX_.'product_attribute_combination pac
                            INNER JOIN '._DB_PREFIX_.'attribute a ON pac.id_attribute = a.id_attribute
                                INNER JOIN '._DB_PREFIX_.'attribute_group_lang agl ON a.id_attribute_group = agl.id_attribute_group
                            INNER JOIN '._DB_PREFIX_."attribute_lang al ON pac.id_attribute = al.id_attribute";
        sc_ext::readCustomproppackproductGridConfigXML('SQLSelectDataLeftJoin');
        $sql_attr .= " WHERE pac.id_product_attribute = '".$row['id_product_attribute']."'
                            AND agl.id_lang = '".$id_lang."'
                            AND al.id_lang = '".$id_lang."'
                        GROUP BY a.id_attribute
                        ORDER BY agl.name";

        $res_attr = Db::getInstance()->executeS($sql_attr);

        $xml .= '<row id="'.$row['id_product_attribute'].'">';
        sc_ext::readCustomproppackproductGridConfigXML('rowUserData');
        sc_ext::readCustomproppackproductGridConfigXML('rowData');
        foreach ($cols as $field)
        {
            if (!empty($field) && !empty($colSettings[$field]))
            {
                $xml .= generateValue($field, $row, $res_attr, $id_product);

            }
        }
        $xml .= '</row>';
    }
    return $xml;
}
$xml = getPdtCombiPack($id_product);
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
