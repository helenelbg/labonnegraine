<?php
if (!defined('STORE_COMMANDER')) { exit; }

$data = ExportOrderMapping::getMappingList();
$xml = array();
if ($data)
{
    foreach ($data as $row)
    {
        $row_xml = array();
        $row_xml[] = '<cell>'.(int) $row[ExportOrderMapping::$definition['primary']].'</cell>';
        $row_xml[] = '<cell><![CDATA['.$row['name'].']]></cell>';
        $row_xml[] = '<cell><![CDATA['.$row['date_upd'].']]></cell>';
        $xml[] = '<row id="'.(int) $row[ExportOrderMapping::$definition['primary']].'">'.implode("\r\n\t", $row_xml).'</row>';
    }
}
$xml = implode("\r\n", $xml);

if (stristr($_SERVER['HTTP_ACCEPT'], 'application/xhtml+xml'))
{
    header('Content-type: application/xhtml+xml');
}
else
{
    header('Content-type: text/xml');
}
echo '<?xml version="1.0" encoding="UTF-8"?>'."\n";
?>
<rows id="0">
    <head>
        <afterInit>
            <call command="attachHeader">
                <param><![CDATA[#numeric_filter,#text_filter,#text_filter]]></param>
            </call>
        </afterInit>
        <column id="<?php echo ExportOrderMapping::$definition['primary']; ?>" width="40" type="ro" align="left" sort="int" hidden="true"><?php echo _l('ID'); ?></column>
        <column id="name" width="*" type="ed" align="left" sort="str"><?php echo _l('Name'); ?></column>
        <column id="date_upd" width="150" type="ed" align="left" sort="str" hidden="true"><?php echo _l('Date update'); ?></column>
    </head>
    <?php
    echo $xml;
    ?>
</rows>