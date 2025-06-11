<?php

    $id_lang = (int) Tools::getValue('id_lang');
    $importConfig = array();

    // SUPPLIER
    $sql = '
        SELECT s.`id_supplier`, s.`name` AS supplier_name
        FROM `'._DB_PREFIX_.'supplier` s
        LEFT JOIN `'._DB_PREFIX_.'supplier_lang` sl ON (s.id_supplier=sl.id_supplier AND sl.id_lang='.(int) $id_lang.')
        ORDER BY sl.`description`';
    $name_supplier = array();
    $res = Db::getInstance()->executeS($sql);
    foreach ($res as $s)
    {
        $name_supplier[$s['id_supplier']] = $s['supplier_name'];
    }

    function getFiles()
    {
        global $importConfig;

        $files = array_diff(scandir(SC_CSV_IMPORT_DIR), array_merge(array('.', '..', 'index.php', '.htaccess', SC_CSV_IMPORT_CONF)));

        // tri par date de modification si pref
        if(_s('CAT_IMPORT_DEFAULT_SCRIPT_SORT') && _s('CAT_IMPORT_DEFAULT_SCRIPT_SORT') > 0){
            $files = array_map(function($e){
                return array(
                        'lastUpdated'=> filemtime(SC_CSV_IMPORT_DIR.$e),
                        'file'=> $e,
                );
            },$files);
            $files = array_column($files, null, 'lastUpdated');
            krsort($files);
            $files = array_column($files, 'file');
        }

        readImportConfigXML($files);
        ImportConvert::convertActionSettings($files);

        foreach ($files as $file)
        {

            if (strtolower(substr($file, strlen($file) - 4, 4)) == '.csv' && strpos($file, '&') === false)
            {
                echo '<row id="'.$file.'">';
                echo '<userdata name="real_size">'.filesize(SC_CSV_IMPORT_DIR.$file).'</userdata>';
                echo '<cell></cell>';
                echo '<cell><![CDATA['.$file.']]></cell>';
                echo '<cell><![CDATA['.sizeFormat(filesize(SC_CSV_IMPORT_DIR.$file)).']]></cell>';
                echo '<cell><![CDATA['.str_replace('.map.xml', '', $importConfig[$file]['mapping']).']]></cell>';
                echo '<cell><![CDATA['.$importConfig[$file]['idby'].']]></cell>';
                echo '<cell><![CDATA['.$importConfig[$file]['fornewproduct'].']]></cell>';
                echo '<cell><![CDATA['.$importConfig[$file]['forfoundproduct'].']]></cell>';
                echo '<cell><![CDATA['.$importConfig[$file]['supplier'].']]></cell>';
                echo '<cell><![CDATA['.$importConfig[$file]['fieldsep'].']]></cell>';
                echo '<cell><![CDATA['.$importConfig[$file]['valuesep'].']]></cell>';
                echo '<cell><![CDATA['.$importConfig[$file]['categorysep'].']]></cell>';
                echo '<cell><![CDATA['.$importConfig[$file]['utf8'].']]></cell>';
                echo '<cell><![CDATA['.$importConfig[$file]['createcategories'].']]></cell>';
                echo '<cell><![CDATA['.$importConfig[$file]['createelements'].']]></cell>';
                echo '<cell><![CDATA['.$importConfig[$file]['firstlinecontent'].']]></cell>';
                echo '<cell><![CDATA['.$importConfig[$file]['importlimit'].']]></cell>';
                $sc_timezone = (!empty(SCI::getConfigurationValue('PS_TIMEZONE')))?SCI::getConfigurationValue('PS_TIMEZONE'):date_default_timezone_get();
                echo '<cell><![CDATA['.scDateTime(filemtime(SC_CSV_IMPORT_DIR.$file),$sc_timezone)->format('d/m/Y H:i:s').']]></cell>';
                echo '</row>';
            }
        }
    }

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
<rows>
<head>
<column id="markedfile" width="30" type="ch" align="center" sort="na"> </column>
<column id="filename" width="160" type="ro" align="left" sort="str"><?php echo _l('Name'); ?></column>
<column id="size" width="60" type="ro" align="right" sort="na"><?php echo _l('File size'); ?></column>
<column id="mapping" width="120" type="coro" align="left" sort="na"><?php echo _l('Mapping'); ?>
    <option value=""></option>
<?php
    $files = array_diff(scandir(SC_CSV_IMPORT_DIR), array_merge(array('.', '..', 'index.php', '.htaccess', SC_CSV_IMPORT_CONF)));
    $content = '';
    foreach ($files as $file)
    {
        if (substr($file, strlen($file) - 8, 8) == '.map.xml')
        {
            $file = str_replace('.map.xml', '', $file);
            $content .= '<option value="'.$file.'">'.$file.'</option>';
        }
    }
    echo $content;
?>
</column>
<column id="idby" width="115" type="coro" align="left" sort="na"><?php echo _l('Products are identified by'); ?>
    <option value="prodname"><?php echo _l('Product name'); ?></option>
    <option value="prodref"><?php echo _l('Product reference'); ?></option>
    <option value="prodrefthenprodname"><?php echo _l('Prod. ref THEN prod. name'); ?></option>
    <option value="supref"><?php echo _l('Supplier reference'); ?></option>
    <option value="suprefthenprodname"><?php echo _l('Sup. ref THEN prod. name'); ?></option>
    <option value="prodrefandsupref"><?php echo _l('Product and supplier reference'); ?></option>
    <option value="prodnameandsupref"><?php echo _l('Product and supplier name'); ?></option>
    <option value="idproduct"><?php echo _l('id_product'); ?></option>
    <option value="idproductattribute"><?php echo _l('id_product_attribute'); ?></option>
    <option value="ean13"><?php echo _l('EAN'); ?></option>
    <option value="upc"><?php echo _l('UPC'); ?></option>
<?php
if (version_compare(_PS_VERSION_, '1.7.0.0', '>='))
{
    ?>
    <option value="mpn"><?php echo _l('MPN'); ?></option>
    <option value="isbn"><?php echo _l('ISBN'); ?></option>
    <?php
}
    if (SC_TOOLS && file_exists(SC_TOOLS_DIR.'import_csv_conf.xml'))
    {
        echo '<option value="specialIdentifier">'._l('specialIdentifier').'</option>';
    }
?>
</column>
<column id="fornewproduct" width="120" type="coro" align="left" sort="na"><?php echo _l('Action for new products'); ?>
    <option value="skip"><?php echo _l('Skip'); ?></option>
    <option value="create"><?php echo _l('Create new product'); ?></option>
</column>
<column id="forfoundproduct" width="120" type="coro" align="left" sort="na"><?php echo _l('Action for existing products'); ?>
    <option value="skip"><?php echo _l('Skip'); ?></option>
    <option value="update"><?php echo _l('Modify product'); ?></option>
    <option value="create"><?php echo _l('Create duplication'); ?></option>
</column>
<column id="supplier" width="75" type="coro" align="left" sort="na"><?php echo _l('Supplier'); ?>
    <option value=""></option>
<?php
        foreach ($name_supplier as $k => $v)
        {
            echo '<option value="'.$k.'"><![CDATA['.$v.']]></option>';
        }
?>
</column>
<column id="fieldsep" width="60" type="coro" align="right" sort="na"><?php echo _l('Field separator'); ?>
    <option value="dcomma">;</option>
    <option value="dcommamac">; Apple MAC</option>
    <option value=",">,</option>
    <option value="|">| pipe</option>
    <option value="tab">Tabulation</option>
</column>
<column id="valuesep" width="60" type="coro" align="right" sort="na"><?php echo _l('Value separator'); ?>
    <option value=",">,</option>
    <option value="|">| pipe</option>
    <option value="tab">Tabulation</option>
</column>
<column id="categorysep" width="75" type="coro" align="right" sort="na"><?php echo _l('Category separator'); ?>
    <option value=",">,</option>
    <option value="#">#</option>
</column>
<column id="utf8" width="50" type="ch" align="center" sort="na"><?php echo _l('Force UTF8'); ?></column>
<column id="createcategories" width="50" type="ch" align="center" sort="na"><?php echo _l('Create categories (auto)'); ?></column>
<column id="createelements" width="50" type="ch" align="center" sort="na"><?php echo _l('Create elements (auto)'); ?></column>
<column id="firstlinecontent" width="120" type="edtxt" align="left" sort="na"><?php echo _l('First line content'); ?></column>
<column id="importlimit" width="60" type="edtxt" align="right" sort="na"><?php echo _l('Lines to import'); ?></column>
<column id="date" width="180" type="ro" align="left" sort="sort_dateFR"><?php echo _l('File date'); ?></column>
</head>
<?php
    getFiles();
    echo '</rows>';
?>