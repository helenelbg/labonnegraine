<?php

    $id_lang = (int) Tools::getValue('id_lang');
    $exportConfig = array();

    // SUPPLIER
    $sql = 'SELECT `id_supplier`, `name` AS supplier_name
            FROM `'._DB_PREFIX_.'supplier`
            ORDER BY `name` ASC';
    $name_supplier = array();
    $res = Db::getInstance()->executeS($sql);
    foreach ($res as $s)
    {
        $name_supplier[$s['id_supplier']] = $s['supplier_name'];
    }

    function getFiles()
    {
        global $exportConfig;
        $files = array_diff(scandir(SC_TOOLS_DIR.'cat_export/'), array_merge(array('.', '..', 'index.php', '.htaccess', 'export.conf.xml')));

        // tri par date de modification si pref
        if(_s('CAT_EXPORT_DEFAULT_SCRIPT_SORT') && _s('CAT_EXPORT_DEFAULT_SCRIPT_SORT') > 0){
            $files = array_map(function($e){
                return array(
                    'lastUpdated'=> filemtime(SC_TOOLS_DIR.'cat_export/'.$e),
                    'file'=> $e,
                );
            },$files);
            $files = array_column($files, null, 'lastUpdated');
            krsort($files);
            $files = array_column($files, 'file');
        }

        foreach ($files as $file)
        {
            if (strtolower(substr($file, strlen($file) - 11, 11) == '.script.xml'))
            {
                readExportConfigXML($file);
                echo '<row id="'.$file.'">';
                echo '<cell></cell>';
                echo '<cell>'.substr($file, 0, strlen($file) - 11).'</cell>';
                if (SCMS)
                {
                    if (!empty($exportConfig['shops']))
                    {
                        echo '<cell>'.$exportConfig['shops'].'</cell>';
                    }
                    else
                    {
                        echo '<cell>0</cell>';
                    }
                }
                echo '<cell><![CDATA['.$exportConfig['mapping'].']]></cell>';
                echo '<cell><![CDATA['.$exportConfig['categoriessel'].']]></cell>';
                echo '<cell><![CDATA['.$exportConfig['exportfilename'].']]></cell>';
                echo '<cell><![CDATA['.$exportConfig['supplier'].']]></cell>';
                echo '<cell>'.$exportConfig['exportdisabledproducts'].'</cell>';
                echo '<cell>'.$exportConfig['exportcombinations'].'</cell>';
                echo '<cell>'.$exportConfig['exportoutofstock'].'</cell>';
                echo '<cell>'.$exportConfig['exportbydefaultcategory'].'</cell>';
                echo '<cell>'.$exportConfig['iso'].'</cell>';
                echo '<cell>'.$exportConfig['fieldsep'].'</cell>';
                echo '<cell>'.$exportConfig['valuesep'].'</cell>';
                echo '<cell>'.$exportConfig['categorysep'].'</cell>';
                echo '<cell>'.$exportConfig['shippingfee'].'</cell>';
                echo '<cell>'.$exportConfig['shippingfeefreefrom'].'</cell>';
                echo '<cell>'.$exportConfig['enclosedby'].'</cell>';
                echo '<cell><![CDATA['.$exportConfig['firstlinecontent'].']]></cell>';
                echo '<cell>'.$exportConfig['lastexportdate'].'</cell>';
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
<?php if (SCMS) { ?>
<column id="shops" width="80" type="coro" align="left" sort="na"><?php echo _l('Shops'); ?>
    <option value="0"><?php echo _l('Selected shop'); ?></option>
<?php
    $content = '';
    $shops = Shop::getShops(false, null, true);
    foreach ($shops as $shop_id)
    {
        $shop = new Shop($shop_id);
        if (!empty($shop->id))
        {
            $name = str_replace('&', _l('and'), $shop->name);
            $content .= '<option value="'.$shop->id.'">'.$name.'</option>';
        }
    }
    echo $content;
?>
</column>
<?php } ?>
<column id="mapping" width="80" type="coro" align="left" sort="na"><?php echo _l('Mapping'); ?>
    <option value=""></option>
<?php
    $files = array_diff(scandir(SC_TOOLS_DIR.'cat_export/'), array_merge(array('.', '..', 'index.php', '.htaccess')));
    $content = '';
    foreach ($files as $file)
    {
        if (substr($file, strlen($file) - 8, 8) == '.map.xml')
        {
            $content .= '<option value="'.$file.'">'.substr($file, 0, strlen($file) - 8).'</option>';
        }
    }
    echo $content;
?>
</column>
<column id="categoriessel" width="80" type="coro" align="left" sort="na"><?php echo _l('Categories to export'); ?>
    <option value="all_enabled"><?php echo _l('All enabled categories'); ?></option>
    <option value="all_disabled"><?php echo _l('All disabled categories'); ?></option>
    <option value="all"><?php echo _l('All categories'); ?></option>
<?php
    $files = array_diff(scandir(SC_TOOLS_DIR.'cat_categories_sel/'), array_merge(array('.', '..', 'index.php', '.htaccess')));
    $content = '';
    foreach ($files as $file)
    {
        if (substr($file, strlen($file) - 8, 8) == '.sel.xml')
        {
            $content .= '<option value="'.$file.'">'.substr($file, 0, strlen($file) - 8).'</option>';
        }
    }
    echo $content;
?>
</column>
<column id="exportfilename" width="160" type="edtxt" align="left" sort="str"><?php echo _l('Export filename'); ?></column>
<column id="supplier" width="75" type="coro" align="left" sort="na"><?php echo _l('Supplier'); ?>
    <option value=""></option>
    <?php
    foreach ($name_supplier as $k => $v)
    {
        echo '<option value="'.$k.'"><![CDATA['.$v.']]></option>';
    }
    ?>
</column>
<column id="exportdisabledproducts" width="70" type="ch" align="center" sort="na"><?php echo _l('Export disabled products'); ?></column>
<column id="exportcombinations" width="70" type="ch" align="center" sort="na"><?php echo _l('Export combinations'); ?></column>
<column id="exportoutofstock" width="75" type="ch" align="center" sort="na"><?php echo _l('Export out of stock products'); ?></column>
<column id="exportbydefaultcategory" width="75" type="ch" align="center" sort="na"><?php echo _l('Export by default category'); ?></column>
<column id="iso" width="50" type="ch" align="center" sort="na"><?php echo _l('ISO encoded'); ?></column>
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
<column id="shippingfee" width="70" type="edtxt" align="right" sort="str"><?php echo _l('Shipping fee'); ?></column>
<column id="shippingfeefreefrom" width="70" type="edtxt" align="right" sort="str"><?php echo _l('Free shipping fee from'); ?></column>
<column id="enclosedby" width="60" type="coro" align="right" sort="na"><?php echo _l('Enclosed by'); ?>
    <option value=""></option>
    <option value="quote">"</option>
</column>
<column id="firstlinecontent" width="120" type="edtxt" align="left" sort="na"><?php echo _l('First line content'); ?></column>
<column id="lastexportdate" width="140" type="ro" align="left" sort="na"><?php echo _l('Last export date'); ?></column>
</head>
<?php
    getFiles();
?>
</rows>