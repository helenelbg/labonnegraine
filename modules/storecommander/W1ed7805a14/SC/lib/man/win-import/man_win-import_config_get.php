<?php
$id_lang = (int) Tools::getValue('id_lang');
$importConfig = array();

function getFiles()
{
    global $importConfig;
    $files = array_diff(scandir(SC_CSV_IMPORT_DIR.'manufacturers/'), array_merge(array('.', '..', 'index.php', '.htaccess', SC_CSV_IMPORT_CONF)));
    readManImportConfigXML($files);
    $xml = '';
    foreach ($files as $file)
    {
        if (strtolower(substr($file, strlen($file) - 4, 4)) == '.csv' && strpos($file, '&') === false)
        {
            $xml .= '<row id="'.$file.'">';
            $xml .= '    <cell></cell>';
            $xml .= '    <cell>'.$file.'</cell>';
            $xml .= '    <cell>'.date('d/m/Y', filemtime(SC_CSV_IMPORT_DIR.'manufacturers/'.$file)).'</cell>';
            $xml .= '    <cell>'.sizeFormat(filesize(SC_CSV_IMPORT_DIR.'manufacturers/'.$file)).'</cell>';
            $xml .= '    <cell>'.str_replace('.map.xml', '', $importConfig[$file]['mapping']).'</cell>';
            $xml .= '    <cell>'.$importConfig[$file]['fieldsep'].'</cell>';
            $xml .= '    <cell>'.$importConfig[$file]['valuesep'].'</cell>';
            $xml .= '    <cell>'.$importConfig[$file]['utf8'].'</cell>';
            $xml .= '    <cell>'.$importConfig[$file]['idby'].'</cell>';
            $xml .= '    <cell>'.$importConfig[$file]['fornewmanufacturer'].'</cell>';
            $xml .= '    <cell>'.$importConfig[$file]['forfoundmanufacturer'].'</cell>';
            $xml .= '    <cell>'.$importConfig[$file]['firstlinecontent'].'</cell>';
            $xml .= '    <cell>'.$importConfig[$file]['importlimit'].'</cell>';
            $xml .= '</row>';
        }
    }

    return $xml;
}

$data_xml = getFiles();

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
        <column id="markedfile" width="30" type="ch" align="center" sort="na"></column>
        <column id="filename" width="160" type="ro" align="left" sort="str"><?php echo _l('Name'); ?></column>
        <column id="date" width="60" type="ro" align="right" sort="sort_dateFR"><?php echo _l('File date'); ?></column>
        <column id="size" width="60" type="ro" align="right" sort="na"><?php echo _l('File size'); ?></column>
        <column id="mapping" width="80" type="coro" align="left" sort="na"><?php echo _l('Mapping'); ?>
            <option value=""></option>
            <?php
            $files = array_diff(scandir(SC_CSV_IMPORT_DIR.'manufacturers/'), array_merge(array('.', '..', 'index.php', '.htaccess', SC_CSV_IMPORT_CONF)));
            $content = '';
            foreach ($files as $file)
            {
                if (substr($file, strlen($file) - 8, 8) == '.map.xml')
                {
                    $file = str_replace('.map.xml', '', $file);
                    $content .= '<option value="'.$file.'"><![CDATA['.$file."]]></option>\n";
                }
            }
            echo $content;
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
        <column id="utf8" width="50" type="ch" align="center" sort="na"><?php echo _l('Force UTF8'); ?></column>
        <column id="idby" width="115" type="coro" align="left" sort="na"><?php echo _l('Manufacturers are identified by'); ?>
            <option value="id_manufacturer"><?php echo _l('id_manufacturer'); ?></option>
            <option value="name"><?php echo _l('Name'); ?></option>
        </column>
        <column id="fornewmanufacturer" width="120" type="coro" align="left" sort="na"><?php echo _l('Action for new manufacturers'); ?>
            <option value="skip"><?php echo _l('Skip'); ?></option>
            <option value="create"><?php echo _l('Create new manufacturer'); ?></option>
        </column>
        <column id="forfoundmanufacturer" width="120" type="coro" align="left" sort="na"><?php echo _l('Action for existing manufacturers'); ?>
            <option value="skip"><?php echo _l('Skip'); ?></option>
            <option value="update"><?php echo _l('Update manufacturer'); ?></option>
        </column>
        <column id="firstlinecontent" width="120" type="edtxt" align="left" sort="na"><?php echo _l('First line content'); ?></column>
        <column id="importlimit" width="60" type="edtxt" align="right" sort="na"><?php echo _l('Lines to import'); ?></column>
    </head>
<?php echo $data_xml."\n</rows>"; ?>