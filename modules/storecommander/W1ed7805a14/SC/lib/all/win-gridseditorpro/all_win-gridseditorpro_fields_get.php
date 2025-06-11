<?php

require_once dirname(__FILE__).'/all_win-gridseditorpro_function.php';

$type = str_replace('type_', '', Tools::getValue('type', 'products'));
$id_lang = (int) Tools::getValue('id_lang', 0);
$iso = 'en';
if (strtolower(Language::getIsoById($id_lang)) == 'fr')
{
    $iso = 'fr';
}
$grids = array();
$xml = '';

$filename = getConfXmlName($type);

if (file_exists($filename))
{
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
    elseif ($type == 'categories')
    {
        $type_temp = 'category';
    }
    else
    {
        $type_temp = $type;
    }

    $params_fields = array();
    $params_fields = SCI::getGridFields($type_temp);

    $xml_conf = simplexml_load_file($filename);
    if ($type == 'productimport' || $type == 'productexport' || $type == 'customersimport')
    {
        $array = array();
        eval((string) $xml_conf->definition);
        if (!empty($array))
        {
            foreach ($array as $name => $field)
            {
                if ($type == 'productimport')
                {
                    $id = str_replace('comboDBField.put(', '', $field);
                    $id = str_replace(');', '', $id);
                    $id = str_replace(",'".$name."'", '', $id);
                    $id = str_replace("'", '', $id);
                }
                elseif ($type == 'customersimport')
                {
                    $id = str_replace('comboDBField.put(', '', $field);
                    $id = str_replace(');', '', $id);
                    $id = str_replace(",'".$name."'", '', $id);
                    $id = str_replace("'", '', $id);
                }
                elseif ($type == 'productexport')
                {
                    $id = $field;
                }
                $xml .= "<row id='".(string) $id."'>";
                $xml .= '<cell><![CDATA['.(string) $id.']]></cell>';
                $xml .= '<cell><![CDATA['.(string) $name.']]></cell>';
                $xml .= '</row>';
            }
        }
    }
    else
    {
        foreach ($xml_conf->fields->field as $field)
        {
            if (!empty($params_fields[(string) $field->name]))
            {
                continue;
            }
            $xml .= "<row id='".(string) $field->name."'>";
            $xml .= '<cell><![CDATA['.(string) $field->name.']]></cell>';
            $xml .= '<cell><![CDATA['.(string) $field->table.']]></cell>';
            $xml .= '<cell><![CDATA['.(string) $field->text->{$iso}.']]></cell>';
            $xml .= '<cell><![CDATA['.(string) $field->celltype.']]></cell>';
            if ($type == 'products')
            {
                $xml .= '<cell><![CDATA['.(string) $field->forceUpdateCombinationsGrid.']]></cell>';
            }
            $xml .= '</row>';
        }
    }
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
<?php if ($type == 'productimport' || $type == 'productexport' || $type == 'customersimport')
{
    ?>
    <column id="id_field" width="150" type="ro" align="left" sort="int"><?php echo _l('ID'); ?></column>
    <column id="name" width="220" type="ed" align="left" sort="str"><?php echo _l('Name'); ?></column>
    <?php
}
else
{
    ?>
        <column id="id_field" width="100" type="ro" align="left" sort="int"><?php echo _l('ID'); ?></column>
        <column id="table" width="80" type="coro" align="left" sort="str"><?php echo _l('Table'); ?>
            <?php
            if ($type == 'products')
            { ?>
                <option value="none"><?php echo _l('Another table'); ?></option>
                <option value="product">product - <?php echo _l('Modifications applied automatically'); ?></option>
                <option value="product_lang">product_lang - <?php echo _l('Modifications applied automatically'); ?></option>
                <?php if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) { ?>
                <option value="product_shop">product_shop</option>
                <?php } ?>
            <?php }
    elseif ($type == 'combinations')
            { ?>
                <option value="none"><?php echo _l('Another table'); ?></option>
                <option value="product_attribute">product_attribute - <?php echo _l('Modifications applied automatically'); ?></option>
                <?php if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) { ?>
                    <option value="product_attribute_shop">product_attribute_shop - <?php echo _l('Modifications applied automatically'); ?></option>
                <?php } ?>
                <option value="product">product</option>
                <?php if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) { ?>
                <option value="product_shop">product_shop</option>
                <option value="product_supplier">product_supplier</option>
                <?php } ?>
            <?php }
    elseif ($type == 'combinationmultiproduct')
            { ?>
                <option value="none"><?php echo _l('Another table'); ?></option>
                <option value="product_attribute">product_attribute - <?php echo _l('Modifications applied automatically'); ?></option>
                <?php if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) { ?>
                <option value="product_attribute_shop">product_attribute_shop - <?php echo _l('Modifications applied automatically'); ?></option>
                <?php } ?>
            <?php }
    elseif ($type == 'orders')
            { ?>
                <option value="none"><?php echo _l('Another table'); ?></option>
                <option value="orders">orders - <?php echo _l('Modifications applied automatically'); ?></option>
                <option value="order_detail">order_detail</option>
                <option value="product">product</option>
                <?php if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) { ?>
                    <option value="product_shop">product_shop</option>
                <?php } ?>
                <option value="product_lang">product_lang</option>
                <option value="category_lang">category_lang</option>
                <option value="customer">customer</option>
                <option value="address_delivery">address_delivery</option>
                <option value="address_invoice">address_invoice</option>
                <option value="warehouse">warehouse</option>
            <?php }
    elseif ($type == 'customers')
            { ?>
                <option value="none"><?php echo _l('Another table'); ?></option>
                <option value="customer">customer - <?php echo _l('Modifications applied automatically'); ?></option>
                <option value="address">address - <?php echo _l('Modifications applied automatically'); ?></option>
            <?php }
    elseif ($type == 'image')
            { ?>
                <option value="none"><?php echo _l('Another table'); ?></option>
                <option value="image">image - <?php echo _l('Modifications applied automatically'); ?></option>
                <option value="image_lang">image_lang - <?php echo _l('Modifications applied automatically'); ?></option>
                <option value="product">product</option>
                <option value="image_shop">image_shop</option>
                <option value="product_lang">product_lang</option>
            <?php }
    elseif ($type == 'productsort')
            { ?>
                <option value="none"><?php echo _l('Another table'); ?></option>
                <option value="product">product</option>
                <option value="product_lang">product_lang</option>
                <?php if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) { ?>
                <option value="product_shop">product_shop</option>
            <?php } ?>
            <?php }
    else
    { ?>
                <option value="none">-</option>
            <?php } ?>
        </column>
        <column id="name" width="120" type="ed" align="left" sort="str"><?php echo _l('Name'); ?></column>
        <column id="celltype" width="100" type="coro" align="left" sort="na"><?php echo _l('Type'); ?>
            <option value="ro"><?php echo _l('Just display'); ?></option>
            <option value="ed"><?php echo _l('Editable'); ?></option>
            <option value="edtxt"><?php echo _l('Secure text'); ?></option>
            <option value="edn"><?php echo _l('Numeric'); ?></option>
            <option value="txt"><?php echo _l('Long text'); ?></option>
            <option value="wysiwyg"><?php echo _l('Full HTML text editor (window)'); ?></option>
            <option value="coro"><?php echo _l('Multiple choices'); ?></option>
            <option value="co"><?php echo _l('Multiple choices or write value'); ?></option>
            <option value="dhxCalendarA"><?php echo _l('Date'); ?></option>
        </column>
    <?php
        if ($type == 'products')
        {
            ?>
        <column id="refreshcombi" width="80" type="coro" align="center" sort="str"><?php echo _l('Refresh combination grid'); ?><option value="1"><![CDATA[<?php echo _l('Yes'); ?>]]></option><option value="0"><![CDATA[<?php echo _l('No'); ?>]]></option></column>
    <?php
        }
}
?>
</head>
<?php
    echo $xml;
?>
</rows>
