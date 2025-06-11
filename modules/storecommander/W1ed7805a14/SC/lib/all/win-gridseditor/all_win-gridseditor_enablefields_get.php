<?php

$type = str_replace('type_', '', Tools::getValue('type', 'products'));
$grid_selected = Tools::getValue('grid');
$is_default = (int) Tools::getValue('is_default');

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

require_once dirname(__FILE__).'/all_win-gridseditor_tools.php';

    $xml = '';
    $array_fields = array();

    $file = SC_TOOLS_DIR.'grids_'.$type.'_conf.xml';

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
    $params_fields = array();
    $params_fields = SCI::getGridFields($type_temp);
    if ($type_temp == 'combination'
        || $type_temp == 'combinationmultiproduct')
    {
        $params_fields['ATTR'] = array('text' => _l('Attributes'), 'width' => 80, 'align' => 'right', 'type' => 'coro', 'sort' => 'str', 'color' => '', 'filter' => '#select_filter');
    }

    $authorized_fields = array();
    $views = SCI::getGridViews($type_temp);
    if (!empty($soloGrids[$type_temp]))
    {
        $views = array($views);
    }
    foreach ($views as $view)
    {
        $view_fields = explode(',', $view);
        $authorized_fields = array_merge($authorized_fields, $view_fields);
    }

    if ($type == 'orders')
    {
        $authorized_fields[] = 'wholesale_price';
        $authorized_fields[] = 'product_price';
        $authorized_fields[] = 'product_quantity_in_stock';
        $authorized_fields[] = 'customer_note';
        $authorized_fields[] = 'total_assets';
        $authorized_fields[] = 'default_category';
        $authorized_fields[] = 'total_wholesale_price';
        $authorized_fields[] = 'customization';

        $authorized_fields[] = 'inv_company';
        $authorized_fields[] = 'inv_firstname';
        $authorized_fields[] = 'inv_lastname';
        $authorized_fields[] = 'inv_address1';
        $authorized_fields[] = 'inv_address2';
        $authorized_fields[] = 'inv_postcode';
        $authorized_fields[] = 'inv_city';
        $authorized_fields[] = 'inv_id_country';
        $authorized_fields[] = 'inv_id_state';
        $authorized_fields[] = 'inv_other';
        $authorized_fields[] = 'inv_phone';
        $authorized_fields[] = 'inv_phone_mobile';

        $authorized_fields[] = 'actual_product_price_wt';
        $authorized_fields[] = 'actual_product_price_it';
        $authorized_fields[] = 'actual_product_price_reduction_wt';
        $authorized_fields[] = 'actual_product_price_reduction_it';

        if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
        {
            $authorized_fields[] = 'product_price_tax_incl';
            $authorized_fields[] = 'quantity_physical';
            $authorized_fields[] = 'quantity_usable';
            $authorized_fields[] = 'quantity_real';
            $authorized_fields[] = 'location_old';
        }
    }
    elseif ($type == 'customers')
    {
        $authorized_fields[] = 'alias';
        $authorized_fields[] = 'vat_number';
        $authorized_fields[] = 'other';
        $authorized_fields[] = 'discount_codes';
        if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
        {
            $authorized_fields[] = 'website';
        }
    }
    elseif ($type == 'productsort')
    {
        $authorized_fields[] = 'ean13';
        $authorized_fields[] = 'upc';
        $authorized_fields[] = 'active';
        $authorized_fields[] = 'image';
    }
    elseif ($type == 'msproduct')
    {
        $authorized_fields[] = 'ean13';
        $authorized_fields[] = 'upc';
        $authorized_fields[] = 'location';
        $authorized_fields[] = 'out_of_stock';
        $authorized_fields[] = 'available_date';
        $authorized_fields[] = 'online_only';
    }
    elseif ($type == 'mscombination')
    {
        $authorized_fields[] = 'ean13';
        $authorized_fields[] = 'upc';
        $authorized_fields[] = 'location';
        $authorized_fields[] = 'pweight';
        $authorized_fields[] = 'default_on';
        $authorized_fields[] = 'unit_price_impact';
    }
    elseif ($type == 'propspeprice')
    {
        $authorized_fields[] = 'image';
        $authorized_fields[] = 'supplier_reference';
        $authorized_fields[] = 'ean13';
        $authorized_fields[] = 'upc';
        $authorized_fields[] = 'active';
        $authorized_fields[] = 'price_exl_tax';
        $authorized_fields[] = 'price_inc_tax';
        $authorized_fields[] = 'id_manufacturer';
        $authorized_fields[] = 'id_supplier';
        $authorized_fields[] = 'from_num';
        $authorized_fields[] = 'to_num';
    }elseif ($type == 'propcustomers')
    {
        $authorized_fields[] = 'id_order';
        if(SCMS){
            $authorized_fields[] = 'id_shop';
            $authorized_fields[] = 'shop_name';
        }
        $authorized_fields[] = 'id_customer';
        if(version_compare(_PS_VERSION_, '1.5.0.0', '>=')){
            $authorized_fields[] = 'cus_lang';
        }
        $authorized_fields[] = 'firstname';
        $authorized_fields[] = 'lastname';
        $authorized_fields[] = 'email';
        if(version_compare(_PS_VERSION_, '1.5.0.0', '>=')){
            $authorized_fields[] = 'company';
            $authorized_fields[] = 'newsletter';
        }
        $authorized_fields[] = 'product_id';
        $authorized_fields[] = 'product_attribute_id';
        $authorized_fields[] = 'product_name';
        $authorized_fields[] = 'product_quantity';
        $authorized_fields[] = 'id_order_state';
        $authorized_fields[] = 'payment';
        $authorized_fields[] = 'group_name';
        $authorized_fields[] = 'date_add';
        $authorized_fields[] = 'product_reference';
    }
    elseif ($type == 'winspeprice')
    {
        $authorized_fields[] = 'image';
        $authorized_fields[] = 'supplier_reference';
        $authorized_fields[] = 'ean13';
        $authorized_fields[] = 'upc';
        $authorized_fields[] = 'active';
    }
    elseif ($type == 'proppackproduct')
    {
        if($grid_selected === 'grid_proppackcombi'){
            $authorized_fields[] = 'id';
            $authorized_fields[] = 'active';
            $authorized_fields[] = 'image';
            $authorized_fields[] = 'reference';
            $authorized_fields[] = 'name';
            $authorized_fields[] = 'present';
            $authorized_fields[] = 'quantity';
            $authorized_fields[] = 'stock_available';
            $authorized_fields[] = 'supplier_reference';
            $authorized_fields[] = 'ean13';
            $authorized_fields[] = 'upc';
            $authorized_fields[] = 'mpn';
            $authorized_fields[] = 'isbn';
        } else{
            $authorized_fields[] = 'id';
            $authorized_fields[] = 'name';
            $authorized_fields[] = 'quantity';
            $authorized_fields[] = 'stock_available';
            $authorized_fields[] = 'ean13';
            $authorized_fields[] = 'upc';
            $authorized_fields[] = 'mpn';
            $authorized_fields[] = 'isbn';
        }
    }
    elseif ($type == 'gmapartner')
    {
        $authorized_fields[] = 'siret';
        $authorized_fields[] = 'ape';
        $authorized_fields[] = 'website';
    }
    elseif ($type == 'products')
    {
        if (version_compare(_PS_VERSION_, '1.6.0.11', '>='))
        {
            $authorized_fields[] = 'reduction_tax';
        }
        if (SCAS)
        {
            $authorized_fields[] = 'location_warehouse';
        }
        $authorized_fields[] = 'is_virtual';
    }
    elseif ($type == 'combinations')
    {
        if (version_compare(_PS_VERSION_, '1.7.5.0', '>='))
        {
            $authorized_fields[] = 'soft_qty_physical';
            $authorized_fields[] = 'soft_qty_reserved';
        }
    }
    elseif ($type == 'combinationmultiproduct')
    {
        if (version_compare(_PS_VERSION_, '1.7.5.0', '>='))
        {
            $authorized_fields[] = 'soft_qty_physical';
            $authorized_fields[] = 'soft_qty_reserved';
        }
    }

    $hiddenFields = array(
            'products' => array('margin', 'reduction_price', 'price_with_reduction', 'reduction_percent', 'price_with_reduction_percent', 'reduction_from', 'reduction_to'),
    );

    foreach ($params_fields as $name => $field)
    {
        if (!in_array($name, $authorized_fields))
        {
            continue;
        }
        $hidden = false;
        if (!empty($hiddenFields[$type]) && in_array($name, $hiddenFields[$type]))
        {
            $hidden = true;
        }

        if ($name == 'location_warehouse')
        {
            $field['text'] = _l('Location').' (warehouse)';
        }

        $compulsory = '';
        if (in_array($name, $compulsoryFields[$type]))
        {
            $compulsory = 1;
        }

        if (empty($field['onlyforgrids']))
        {
            $prefix = '';
            if (!empty($compulsory))
            {
                $prefix = '0';
            }

            $array_fields[$prefix._l($field['text'])] = array(
                'name' => $name,
                'text' => _l($field['text']),
                'celltype' => $field['type'],
                'align' => $field['align'],
                'sort' => $field['sort'],
                'filter' => $field['filter'],
                'width' => $field['width'],
                'color' => '',
                'bg_color' => '',
                'hidden' => $hidden,
                'compulsory' => $compulsory,
                'is_special' => '0',
            );
        }
        elseif (!empty($field['onlyforgrids']))
        {
            $prefix = '';
            if (!empty($compulsory))
            {
                $prefix = '0';
            }

            $add_fields = '';
            if ($type == 'orders' && in_array('grid_picking', $field['onlyforgrids']))
            {
                $add_fields = 'id_order_detail';
            }
            elseif ($type == 'customers' && in_array('grid_address', $field['onlyforgrids']))
            {
                $add_fields = 'id_address';
            }elseif (in_array($grid_selected, $field['onlyforgrids']))
            {
                $add_fields = $name;
            }
            if (!empty($add_fields))
            {
                $array_fields[$prefix._l($field['text'])] = array(
                        'name' => $prefix.$name,
                        'text' => _l($field['text']),
                        'celltype' => $field['type'],
                        'align' => $field['align'],
                        'sort' => $field['sort'],
                        'filter' => $field['filter'],
                        'width' => $field['width'],
                        'color' => '',
                        'bg_color' => '',
                        'hidden' => $hidden,
                        'is_special' => '0',
                        'add_fields' => $add_fields,
                        'compulsory' => $compulsory,
                );
            }
        }
    }

    if (file_exists($file))
    {
        $grids_xml_conf = simplexml_load_file($file);
        $grid_xml = null;
        foreach ($grids_xml_conf->fields->field as $field)
        {
            $field = (array) $field;

            $hidden = false;
            if ($type == 'products' && in_array($name, $hiddenFields[$type]))
            {
                $hidden = true;
            }

            $is_special = true;
            foreach ($array_fields as $text => $values)
            {
                if ((string) $field['name'] == $values['name'])
                {
                    unset($array_fields[$text]);
                    $is_special = false;
                }
            }

            $prefix = '';
            $compulsory = '';
            if (in_array((string) $field['name'], $compulsoryFields[$type]))
            {
                $prefix = '0';
                $compulsory = 1;
            }

            $array_fields[(string) $prefix.$field['text']->{$iso}] = array(
                    'name' => (string) $field['name'],
                    'text' => (string) $field['text']->{$iso},
                    'celltype' => (string) $field['celltype'],
                    'align' => (string) $field['align'],
                    'sort' => (string) $field['sort'],
                    'filter' => (string) $field['filter'],
                    'width' => (string) $field['width'],
                    'color' => '',
                    'bg_color' => '#9ECA92',
                    'hidden' => $hidden,
                    'is_special' => (int) $is_special,
                    'compulsory' => $compulsory,
            );
        }
    }

    uksort($array_fields, 'sortFields');

    // FIELDS GROUPS
        $group_fields = array();

        if ($type == 'products')
        {
            $name = '* '._l('Prices (incl. Tax, excl. Tax, Tax, Margin, Ecotax, Wholesale price)');
            $group_fields[$name] = array(
                    'name' => 'gp_prices:margin+price+id_tax_rules_group+price_inc_tax+ecotax+wholesale_price',
                    'text' => $name,
                    'celltype' => '',
                    'align' => '',
                    'sort' => '',
                    'filter' => '',
                    'width' => '',
                    'color' => '',
                    'bg_color' => '',
                    'is_special' => '0',
                    'compulsory' => '0',
            );

            if (SCAS)
            {
                $name = '* '._l('Quantities').' ('._l('Quantities + Advanced stock').')';
                $group_fields[$name] = array(
                    'name' => 'gp_quantities:quantity+advanced_stock_management+quantity_physical+quantity_usable+quantity_real+quantityupdate+minimal_quantity',
                    'text' => $name,
                    'celltype' => '',
                    'align' => '',
                    'sort' => '',
                    'filter' => '',
                    'width' => '',
                    'color' => '',
                    'bg_color' => '',
                    'is_special' => '0',
                    'compulsory' => '0',
            );
            }

            $name = '* '._l('Specific prices');
            $group_fields[$name] = array(
                    'name' => 'gp_reductions:reduction_price+price_with_reduction+reduction_percent+price_with_reduction_percent+reduction_from+reduction_to',
                    'text' => $name,
                    'celltype' => '',
                    'align' => '',
                    'sort' => '',
                    'filter' => '',
                    'width' => '',
                    'color' => '',
                    'bg_color' => '',
                    'is_special' => '0',
                    'compulsory' => '0',
            );
        }

        ksort($group_fields);

    $array_fields = array_merge($group_fields, $array_fields);

    foreach ($array_fields as $num => $row)
    {
        $xml .= ('<row id="'.$row['name'].'">')."\n";
        $xml .= '<userdata name="is_used">0</userdata>';
        $xml .= ('<userdata name="is_custom">'.(!empty($row['bg_color']) ? '1' : '0').'</userdata>')."\n";
        $xml .= ('<userdata name="hidden"><![CDATA['.(int) (isset($row['hidden']) ? $row['hidden'] : 0).']]></userdata>')."\n";
        $xml .= ('<userdata name="is_special"><![CDATA['.(int) $row['is_special'].']]></userdata>')."\n";
        $xml .= ('<userdata name="name"><![CDATA['.$row['name'].']]></userdata>')."\n";
        $xml .= ('<userdata name="text"><![CDATA['.$row['text'].']]></userdata>')."\n";
        $xml .= ('<userdata name="celltype"><![CDATA['.$row['celltype'].']]></userdata>')."\n";
        $xml .= ('<userdata name="align"><![CDATA['.$row['align'].']]></userdata>')."\n";
        $xml .= ('<userdata name="sort"><![CDATA['.$row['sort'].']]></userdata>')."\n";
        $xml .= ('<userdata name="filter"><![CDATA['.$row['filter'].']]></userdata>')."\n";
        $xml .= ('<userdata name="width"><![CDATA['.$row['width'].']]></userdata>')."\n";
        $xml .= ('<userdata name="color"><![CDATA['.$row['color'].']]></userdata>')."\n";
        $xml .= ('<userdata name="compulsory"><![CDATA['.$row['compulsory'].']]></userdata>')."\n";
        $xml .= ('<userdata name="add_fields"><![CDATA['.(!empty($row['add_fields']) ? $row['add_fields'] : '').']]></userdata>')."\n";
        $xml .= ('<cell '.(!empty($row['bg_color']) ? ' bgColor="'.$row['bg_color'].'"' : '').'><![CDATA['.$row['text'].']]></cell>')."\n";
        $xml .= ('</row>')."\n";
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
    <column id="text" width="300" type="ro" align="left" sort="str"><?php echo _l('Field'); ?></column>
</head>
<?php
    echo $xml;
?>
</rows>
