<?php
if (!defined('STORE_COMMANDER')) { exit; }

$product_ids = Tools::getValue('product_ids', 0);
$id_lang = (int) Tools::getValue('id_lang');
$insert = (int) Tools::getValue('insert');

## Avant tout on supprime les doublons avant affichage
$products = explode(',', $product_ids);

$multiple = false;
if (count($products)>1) $multiple = true;

$sql = 'SELECT upc.*, upe.*, upetl.name as type, upm.name as manufacturer, pl.name as product_name
            FROM '._DB_PREFIX_.'ukooparts_compatibility upc
            LEFT JOIN '._DB_PREFIX_.'ukooparts_engine upe ON upe.id_ukooparts_engine = upc.id_ukooparts_engine
            LEFT JOIN '._DB_PREFIX_.'ukooparts_engine_type upet ON upe.id_ukooparts_engine_type = upet.id_ukooparts_engine_type
            INNER JOIN '._DB_PREFIX_.'ukooparts_engine_type_lang upetl ON (upet.id_ukooparts_engine_type = upetl.id_ukooparts_engine_type AND upetl.id_lang = '.$id_lang.' )
            LEFT JOIN '._DB_PREFIX_.'ukooparts_manufacturer upm ON upe.id_ukooparts_manufacturer = upm.id_ukooparts_manufacturer
            LEFT JOIN '._DB_PREFIX_.'product_lang pl ON (pl.id_product= upc.id_product AND pl.id_lang='.(int) $id_lang.') 
            WHERE upc.id_product IN ('.pInSQL($product_ids).')
            ORDER BY upc.id_product, upc.id_ukooparts_engine ASC';

$res = Db::getInstance()->ExecuteS($sql);

$all_compatibilities = array();
if (!empty($res))
{
    foreach ($res as $data)
    {
        $all_compatibilities[$data['id_product'].'_'.$data['id_ukooparts_engine'].'_'.$data['year']] = $data;
    }
}

function getCompatibilities($all_compatibilities, $multiple)
{
    foreach ($all_compatibilities as $id_compat => $compat)
    {
        $return = '';
        $return .= '<row id="'.$id_compat.'">';

        $return .= '<userdata name="type_id">'.$compat['id_ukooparts_engine_type'].'</userdata>';
        $return .= '<userdata name="manu_id">'.$compat['id_ukooparts_manufacturer'].'</userdata>';
        $return .= '<userdata name="model_id">'.$compat['id_ukooparts_engine'].'</userdata>';

        $return .= '<cell><![CDATA['.$compat['id_product'].']]></cell>';
        if ($multiple) $return .= '<cell><![CDATA['.$compat['product_name'].']]></cell>';
        $return .= '<cell value="'.$compat['id_ukooparts_engine_type'].'" title="'.$compat['id_ukooparts_engine_type'].'"><![CDATA['.$compat['type'].']]></cell>';
        $return .= '<cell value="'.$compat['id_ukooparts_manufacturer'].'" title="'.$compat['id_ukooparts_manufacturer'].'"><![CDATA['.$compat['manufacturer'].']]></cell>';
        $return .= '<cell value="'.$compat['id_ukooparts_engine'].'" title="'.$compat['id_ukooparts_engine'].'"><![CDATA['.$compat['model'].' : '.$compat['year_start'].' - '.$compat['year_end'].']]></cell>';
        $return .= '<cell><![CDATA['.((!empty($compat['year'])) ? $compat['year'] : "").']]></cell>';

        $return .= '</row>';
        echo $return;
    }
}

function getTypeOptions($id_lang)
{
    $sql = 'SELECT upet.id_ukooparts_engine_type, upetl.name
            FROM '._DB_PREFIX_.'ukooparts_engine_type upet
            INNER JOIN '._DB_PREFIX_.'ukooparts_engine_type_lang upetl ON (upet.id_ukooparts_engine_type = upetl.id_ukooparts_engine_type AND upetl.id_lang = '.$id_lang.' )
            ORDER BY upetl.name ASC';

    $res = Db::getInstance()->ExecuteS($sql);

    if (count($res) > 0)
    {
        foreach ($res as $type)
        {
            $options = '';
            $options .='<option value="'.$type['id_ukooparts_engine_type'].'"><![CDATA['.$type['name'].']]></option>';

            echo $options;
        }
    }
}

function getManufacturerOptions()
{
    $sql = 'SELECT id_ukooparts_manufacturer, name
            FROM '._DB_PREFIX_.'ukooparts_manufacturer
            ORDER BY name ASC';

    $res = Db::getInstance()->ExecuteS($sql);

    if (count($res) > 0)
    {
        foreach ($res as $man)
        {
            $options = '';
            $options .='<option value="'.$man['id_ukooparts_manufacturer'].'">'.$man['name'].'</option>';

            echo $options;
        }
    }
}

function getEngineOptions()
{
    $sql = 'SELECT id_ukooparts_engine, model
            FROM '._DB_PREFIX_.'ukooparts_engine
            ORDER BY model ASC';

    $res = Db::getInstance()->ExecuteS($sql);

    if (count($res) > 0)
    {
        foreach ($res as $mod)
        {
            $options = '';
            $options .='<option value="'.$mod['id_ukooparts_engine'].'">'.$mod['model'].'</option>';

            echo $options;
        }
    }
}

if (stristr($_SERVER['HTTP_ACCEPT'], 'application/xhtml+xml') || stristr($_SERVER['HTTP_ACCEPT'], '*/*'))
{
    header('Content-type: application/xhtml+xml');
}
else
{
    header('Content-type: text/xml');
}
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
?>
<?php if (!empty($res) || $insert ) { ?>
<rows>
    <head>
        <beforeInit>
            <call command="attachHeader"><param><![CDATA[#numeric_filter,#select_filter,#select_filter,#select_filter,#text_filter]]></param></call>
        </beforeInit>
        <column id="id_product" width="80" type="ro" align="center" sort="int"><?php echo _l('ID Product'); ?></column>
        <?php if ($multiple) { ?>
            <column id="product_name" width="120" type="ro" align="center" sort="str"><?php echo _l('Product name'); ?></column>
        <?php } ?>
        <!--<column id="id_engine" width="80" type="ro" align="center" sort="int"><?php echo _l('ID engine'); ?></column>-->
        <column id="type" width="150" type="cororo" align="center" sort="str"><?php echo _l('Type'); ?>
            <?php if ($insert) getTypeOptions($id_lang); ?>
        </column>
        <column id="manufacturer" width="120" type="cororo" align="center" sort="str"><?php echo _l('Manufacturer'); ?>
        </column>
		<column id="model" width="200" type="cororo" align="center" sort="str"><?php echo _l('Model'); ?>
        </column>
		<column id="year" width="80" type="ed" align="center" sort="int"><?php echo _l('Year'); ?></column>
    </head>
<?php
    echo '<userdata name="uisettings">'.uisettings::getSetting('cat_partscompatibility').'</userdata>'."\n";
    getCompatibilities($all_compatibilities, $multiple);
?>
</rows>
<?php } else { ?>
<rows>
    <head>
        <column id="info" width="*" type="ro" align="center" sort="int"></column>
    </head>
    <row id="no_compat">
        <?php
        echo '<cell style="height:300px;"><![CDATA['._l("No compatibility found.")."<br>"._l("Tips : you can insert compatibilities from another product here with right click.").']]></cell>';
        ?>
    </row>
</rows>
<?php } ?>
