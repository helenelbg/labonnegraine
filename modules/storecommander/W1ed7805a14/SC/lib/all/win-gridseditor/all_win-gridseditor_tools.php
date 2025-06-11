<?php

$compulsoryFields = array(
    'products' => array('id'),
    'combinations' => array('id_product_attribute', 'ATTR', 'price', 'priceextax', 'pprice', 'ppriceextax'),
    'combinationmultiproduct' => array('id_product', 'id_product_attribute', 'ATTR', 'price', 'priceextax', 'pprice', 'ppriceextax'),
    'customers' => array('id_customer'),
    'orders' => array('id_order'),
    'order_product' => array('product_id'),
    'productsort' => array('id_product'),
    'msproduct' => array('id_product', 'id_shop'),
    'mscombination' => array('id_product', 'id_product_attribute', 'id_shop', 'ppriceextax'),
    'propspeprice' => array('id_specific_price', 'id_product', 'id_product_attribute', 'reduction_tax'),
    'winspeprice' => array('id_specific_price', 'id_product', 'id_product_attribute', 'reduction_tax'),
    'proppackproduct' => array('id'),
    'image' => array('id_image', 'id_product', 'position', 'cover', '_SHOPS_', 'width', 'height'),
    'propsupplier' => array('id', 'present', 'default'),
    'gmapartner' => array('id_partner', 'customer_id'),
    'cms' => array('id_cms'),
    'propcustomers' => array('id_order','id_customer'),
);
$notMoveFields = array(
    'products' => array('id'),
    'combinations' => array('id_product_attribute', 'default_on'),
    'combinationmultiproduct' => array('id_product', 'id_product_attribute', 'default_on'),
    'customers' => array('id_customer'),
    'orders' => array('id_order'),
    'order_product' => array('id_order'),
    'productsort' => array('id_product'),
    'msproduct' => array('id_product'),
    'mscombination' => array('id_product', 'id_product_attribute'),
    'propspeprice' => array('id_specific_price'),
    'winspeprice' => array('id_specific_price'),
    'proppackproduct' => array('id'),
    'image' => array('id_image'),
    'propsupplier' => array('id', 'default'),
    'gmapartner' => array('id_partner'),
    'cms' => array('id_cms'),
    'propcustomers' => array('id_customer'),
);

// CHECK IF GRID EXIST IN THIS FILE
// WITHOUT DomDocument
function gridIsInXML($id, $content)
{
    $return = false;

    if (strpos($content, '<name><![CDATA['.$id.']]></name>') !== false || strpos($content, '<name>'.$id.'</name>') !== false)
    {
        $return = true;
    }

    return $return;
}
function fieldIsInXML($id, $content)
{
    $return = false;

    if (strpos($content, '<name><![CDATA['.$id.']]></name>') !== false || strpos($content, '<name>'.$id.'</name>') !== false)
    {
        $return = true;
    }

    return $return;
}

// CHECK IF GRID NAME ALREADY EXIST
// WITHOUT DomDocument
function testName($name, $content, $basename = null, $i = 2)
{
    if (empty($basename))
    {
        $basename = $name;
    }
    if (!empty($name))
    {
        if (gridIsInXML($name, $content))
        {
            $temp_name = $basename.'_'.$i;

            $name = testName($temp_name, $content, $basename, ($i + 1));
        }
    }

    return $name;
}
function testNameField($name, $content, $basename = null, $i = 2)
{
    if (empty($basename))
    {
        $basename = $name;
    }
    if (!empty($name))
    {
        if (fieldIsInXML($name, $content))
        {
            $temp_name = $basename.'_'.$i;

            $name = testNameField($temp_name, $content, $basename, ($i + 1));
        }
    }

    return $name;
}

// GET FIELD CONFIG (IF EXIST) FROM FILE
// WITHOUT DomDocument
function getFieldInXML($name, $xml)
{
    $return = null;
    foreach ($xml->fields->field as $field)
    {
        if ($field->name == $name)
        {
            $return = $field;
            break;
        }
    }

    return $return;
}

// ADD A NEW GRID IN FILE
// WITHOUT DomDocument
function addNewGrid($type, $content, $name, $text = null, $fields = 'by_default')
{
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
    elseif ($type == 'images')
    {
        $type_temp = 'image';
    }
    else
    {
        $type_temp = $type;
    }

    $soloGrids = array();
    $soloGrids['combination'] = 'combination';
    $soloGrids['productsort'] = 'productsort';
    $soloGrids['msproduct'] = 'msproduct';
    $soloGrids['mscombination'] = 'mscombination';
    $soloGrids['images'] = 'image';
    $soloGrids['propspeprice'] = 'propspeprice';
    $soloGrids['propcustomers'] = 'propcustomers';
    $soloGrids['winspeprice'] = 'winspeprice';
    $soloGrids['propsupplier'] = 'propsupplier';
    $soloGrids['gmapartner'] = 'gmapartner';
    $soloGrids['order_product'] = 'order_product';

    if (empty($text))
    {
        if ($name == 'grid_light')
        {
            $original_name_en = ('Light view');
        }
        elseif ($name == 'grid_large')
        {
            $original_name_en = ('Large view');
        }
        elseif ($name == 'grid_delivery')
        {
            $original_name_en = ('Delivery');
        }
        elseif ($name == 'grid_price')
        {
            $original_name_en = ('Prices');
        }
        elseif ($name == 'grid_discount')
        {
            $original_name_en = ('Discounts');
        }
        elseif ($name == 'grid_discount_2')
        {
            $original_name_en = ('Discounts and margins');
        }
        elseif ($name == 'grid_seo')
        {
            $original_name_en = ('SEO');
        }
        elseif ($name == 'grid_reference')
        {
            $original_name_en = ('References');
        }
        elseif ($name == 'grid_address')
        {
            $original_name_en = ('Addresses');
        }
        elseif ($name == 'grid_convert')
        {
            $original_name_en = ('Convert');
        }
        elseif ($name == 'grid_picking')
        {
            $original_name_en = ('Picking');
        }
        elseif ($name == 'grid_pack')
        {
            $original_name_en = ('Pack');
        }
        else
        {
            $original_name_en = $name;
        }

        $original_name_fr = _l($original_name_en);
    }
    else
    {
        $original_name_fr = $text;
        $original_name_en = $text;
    }

    if (!empty($fields) && $fields == 'by_default')
    {
        $fields = '';
        $grids_default = SCI::getGridViews($type_temp);
        if ($type != 'combinations' && !empty($grids_default[$name]))
        {
            $fields = $grids_default[$name];
        }
        elseif ((!empty($soloGrids[$type_temp])) && !empty($grids_default))
        {
            $fields = $grids_default;
        }

        if (empty($fields))
        {
            $required_field_by_type = array(
                'products' => array('id'),
                'customers' => array('id_customer'),
                'orders' => array('id_order'),
                'productsort' => array('id_product'),
                'cms' => array('id_cms'),
            );
            if (array_key_exists($type, $required_field_by_type))
            {
                $fields = implode(',', $required_field_by_type[$type]);
            }
        }
    }

    $grid_xml = '    <grid>
        <name><![CDATA['.$name.']]></name>
        <text>
            <fr><![CDATA['.$original_name_fr.']]></fr>
            <en><![CDATA['.$original_name_en.']]></en>
        </text>
        <value><![CDATA['.$fields.']]></value>
    </grid>';
    $content = str_replace('</grids>', $grid_xml."\n".'</grids>', $content);
    file_put_contents($file, $content);
}

// ADD A NEW FIELD IN FILE
// WITHOUT DomDocument
function addNewField($type, $content, $name)
{
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
    else
    {
        $type_temp = $type;
    }

    $field_xml = '    <field>
        <name><![CDATA['.$name.']]></name>
        <text>
            <fr><![CDATA['.$name.']]></fr>
            <en><![CDATA['.$name.']]></en>
        </text>
        <table><![CDATA[none]]></table>
        <width><![CDATA[60]]></width>
        <align><![CDATA[left]]></align>
        <celltype><![CDATA[ro]]></celltype>
        <answertype><![CDATA[]]></answertype>
        <sort><![CDATA[str]]></sort>
        <color><![CDATA[]]></color>
        <filter><![CDATA[#text_filter]]></filter>
        <footer><![CDATA[]]></footer>
        <forceUpdateCombinationsGrid><![CDATA[]]></forceUpdateCombinationsGrid>
        <options><![CDATA[]]></options>
        <onEditCell><![CDATA[]]></onEditCell>
        <onAfterUpdate><![CDATA[]]></onAfterUpdate>
        <onBeforeUpdate><![CDATA[]]></onBeforeUpdate>
        <SQLSelectDataSelect><![CDATA[]]></SQLSelectDataSelect>
        <rowData><![CDATA[]]></rowData>
        <afterGetRows><![CDATA[]]></afterGetRows>
    </field>';
    $content = str_replace('</fields>', $field_xml."\n".'</fields>', $content);
    file_put_contents($file, $content);
}

function sortFields($a, $b)
{
    return strcasecmp($a, $b);
}
