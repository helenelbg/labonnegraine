<?php

$type = str_replace('type_', '', Tools::getValue('type', 'products'));
$id_lang = (int) Tools::getValue('id_lang', 0);
$iso = 'en';
if (strtolower(Language::getIsoById($id_lang)) == 'fr')
{
    $iso = 'fr';
}

$soloGrids = array();
$soloGrids['combination'] = 'combination';
$soloGrids['combinationmultiproduct'] = 'combinationmultiproduct';
$soloGrids['productsort'] = 'productsort';
$soloGrids['msproduct'] = 'msproduct';
$soloGrids['mscombination'] = 'mscombination';
$soloGrids['image'] = 'image';
$soloGrids['propspeprice'] = 'propspeprice';
$soloGrids['winspeprice'] = 'winspeprice';
$soloGrids['propsupplier'] = 'propsupplier';
$soloGrids['gmapartner'] = 'gmapartner';
$soloGrids['order_product'] = 'order_product';
$soloGrids['propcustomers'] = 'propcustomers';


if ($type == 'products')
{
    $type_temp = 'product';
}
elseif ($type == 'customers')
{
    $type_temp = 'customer';
}
elseif ($type == 'orders')
{
    $type_temp = 'order';
}
elseif ($type == 'combinations')
{
    $type_temp = 'combination';
}
elseif ($type == 'combinationmultiproduct')
{
    $type_temp = 'combinationmultiproduct';
}
else
{
    $type_temp = $type;
}

    $xml = '';

    $default_grids_id_list = array();
    $grids = array();
    if (!empty($soloGrids[$type_temp]))
    {
        if ($type_temp == 'combination')
        {
            $default_grids_id_list[] = 'grid_'.$type_temp;
            $grids['grid_'.$type_temp]['name'] = _l('Combinations');
        }
        elseif ($type_temp == 'combinationmultiproduct')
        {
            $default_grids_id_list[] = 'grid_'.$type_temp;
            $grids['grid_'.$type_temp]['name'] = _l('Multi-product combinations');
        }
        elseif ($type_temp == 'productsort')
        {
            $default_grids_id_list[] = 'grid_'.$type_temp;
            $grids['grid_'.$type_temp]['name'] = _l('Product sort');
        }
        elseif ($type_temp == 'msproduct')
        {
            $default_grids_id_list[] = 'grid_'.$type_temp;
            $grids['grid_'.$type_temp]['name'] = _l('Multistore - products information');
        }
        elseif ($type_temp == 'mscombination')
        {
            $default_grids_id_list[] = 'grid_'.$type_temp;
            $grids['grid_'.$type_temp]['name'] = _l('Multistore - combinations');
        }
        elseif ($type_temp == 'image')
        {
            $default_grids_id_list[] = 'grid_'.$type_temp;
            $grids['grid_'.$type_temp]['name'] = _l('Product images');
        }
        elseif ($type_temp == 'propspeprice')
        {
            $default_grids_id_list[] = 'grid_'.$type_temp;
            $grids['grid_'.$type_temp]['name'] = _l('Properties - specific prices');
        }elseif ($type_temp == 'propcustomers')
        {
            $default_grids_id_list[] = 'grid_'.$type_temp;
            $grids['grid_'.$type_temp]['name'] = _l('Properties').' - '._l('customers');
        }
        elseif ($type_temp == 'winspeprice')
        {
            $default_grids_id_list[] = 'grid_'.$type_temp;
            $grids['grid_'.$type_temp]['name'] = _l('Specific prices management');
        }
        elseif ($type_temp == 'propsupplier')
        {
            $default_grids_id_list[] = 'grid_'.$type_temp;
            $grids['grid_'.$type_temp]['name'] = _l('Suppliers');
        }
        elseif ($type_temp == 'gmapartner')
        {
            $default_grids_id_list[] = 'grid_'.$type_temp;
            $grids['grid_'.$type_temp]['name'] = _l('Affiliation program').' '._l('Partners');
        }
        elseif ($type_temp == 'order_product')
        {
            $default_grids_id_list[] = 'grid_'.$type_temp;
            $grids['grid_'.$type_temp]['name'] = _l('Products');
        }

        $grids['grid_'.$type_temp]['color'] = '#dddddd';

        if (file_exists(SC_TOOLS_DIR.'grids_'.$type.'_conf.xml'))
        {
            $grids_xml_conf = simplexml_load_file(SC_TOOLS_DIR.'grids_'.$type.'_conf.xml');
            foreach ($grids_xml_conf->grids->grid as $grid)
            {
                $grids['grid_'.$type_temp]['color'] = '';
            }
        }
    }
    else
    {
        $grids_default = SCI::getGridViews($type_temp);
        $grids = array();
        foreach ($grids_default as $id => $value)
        {
            $grids[$id]['color'] = '#dddddd';
            if ($id == 'grid_light')
            {
                $default_grids_id_list[] = $id;
                $grids[$id]['name'] = _l('Light view');
            }
            elseif ($id == 'grid_large')
            {
                $default_grids_id_list[] = $id;
                $grids[$id]['name'] = _l('Large view');
            }
            elseif ($id == 'grid_delivery')
            {
                $default_grids_id_list[] = $id;
                $grids[$id]['name'] = _l('Delivery');
            }
            elseif ($id == 'grid_price')
            {
                $default_grids_id_list[] = $id;
                $grids[$id]['name'] = _l('Prices');
            }
            elseif ($id == 'grid_discount')
            {
                $default_grids_id_list[] = $id;
                $grids[$id]['name'] = _l('Discounts');
            }
            elseif ($id == 'grid_discount_2')
            {
                $default_grids_id_list[] = $id;
                $grids[$id]['name'] = _l('Discounts and margins');
            }
            elseif ($id == 'grid_seo')
            {
                $default_grids_id_list[] = $id;
                $grids[$id]['name'] = _l('SEO');
            }
            elseif ($id == 'grid_reference')
            {
                $default_grids_id_list[] = $id;
                $grids[$id]['name'] = _l('References');
            }
            elseif ($id == 'grid_description')
            {
                if (!_r('GRI_CAT_VIEW_GRID_DESCRIPTION') || !(int) _s('CAT_PROD_GRID_DESCRIPTION'))
                {
                    unset($grids[$id]);
                }
                else
                {
                    $default_grids_id_list[] = $id;
                    $grids[$id]['name'] = _l('Descriptions');
                }
            }
            elseif ($id == 'grid_combination_price')
            {
                unset($grids[$id]);
            }
            elseif ($id == 'grid_address')
            {
                $default_grids_id_list[] = $id;
                $grids[$id]['name'] = _l('Addresses');
            }
            elseif ($id == 'grid_convert')
            {
                $default_grids_id_list[] = $id;
                $grids[$id]['name'] = _l('Convert');
            }
            elseif ($id == 'grid_picking')
            {
                $default_grids_id_list[] = $id;
                $grids[$id]['name'] = _l('Picking');
            }
            elseif ($id == 'grid_pack')
            {
                $default_grids_id_list[] = $id;
                $grids[$id]['name'] = _l('Pack');
            }
            elseif ($id == 'grid_proppackproduct')
            {
                $default_grids_id_list[] = $id;
                $grids[$id]['name'] = _l('Products').' ('._l('Pack').')';
            }
            elseif ($id == 'grid_proppackcombi')
            {
                $default_grids_id_list[] = $id;
                $grids[$id]['name'] = _l('Combinations').' ('._l('Pack').')';
            }
        }
        if (file_exists(SC_TOOLS_DIR.'grids_'.$type.'_conf.xml'))
        {
            $grids_xml_conf = simplexml_load_file(SC_TOOLS_DIR.'grids_'.$type.'_conf.xml');
            foreach ($grids_xml_conf->grids->grid as $grid)
            {
                $override_grid_name = (string) $grid->name;
                if (!in_array($override_grid_name, $default_grids_id_list))
                {
                    $grids[$override_grid_name]['name'] = (string) $grid->text->{$iso};
                }
                $grids[$override_grid_name]['color'] = '';
            }
        }
    }

    foreach ($grids as $id => $row)
    {
        $need_color = (!empty($row['color']) ? 'bgColor="'.$row['color'].'"' : '');
        $id_default_grid = (in_array($id, $default_grids_id_list) ? ' type="ro"' : '');
        $xml .= "<row id='".$id."'>";
        $xml .= '<userdata name="is_default">'.(!empty($row['color']) ? '1' : '0').'</userdata>';
        $xml .= '<userdata name="name_is_editable">'.((in_array($id, $default_grids_id_list)) ? '0' : '1').'</userdata>';
        $xml .= '<cell '.$need_color.'><![CDATA['.$id.']]></cell>';
        $xml .= '<cell '.$need_color.$id_default_grid.'><![CDATA['.$row['name'].']]></cell>';
        $xml .= '</row>';
    }

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
    <column id="id_grid" width="80" type="ro" align="left" sort="int"><?php echo _l('ID'); ?></column>
    <column id="name" width="200" type="ed" align="left" sort="str"><?php echo _l('Grid'); ?></column>
</head>
<?php
    echo $xml;
?>
</rows>
