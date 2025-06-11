<?php
if (!defined('STORE_COMMANDER')) { exit; }

$data = ExportCustomer::getExportCronList();
$xml = array();
if ($data)
{

    foreach ($data as $row)
    {
        $row_xml = array();
        $row_xml[] = '<cell>'.(int) $row[ExportCustomer::$definition['primary']].'</cell>';
        $row_xml[] = '<cell><![CDATA['.$row['filter_name'].']]></cell>';
        $row_xml[] = '<cell><![CDATA['.$row['mapping_name'].']]></cell>';
        $row_xml[] = '<cell><![CDATA['.$row['iso'].']]></cell>';
        $row_xml[] = '<cell><![CDATA['.$row['filename'].'.csv]]></cell>';
        $row_xml[] = '<cell><![CDATA['.$row['date_last_export'].']]></cell>';
        $urlParams = array(
            'source' => 'export',
            'detail' => 'customers',
            'process' => $row['token']
        );
        $url = SC_ORK_EXTERNAL_URL.'cron/cron_init.php?'.http_build_query($urlParams);
        $row_xml[] = '<cell><![CDATA['.$url.']]></cell>';
        $row_xml[] = '<cell title="'._l('Copy to ClipBoard').'"><![CDATA[<button onclick="copyCronUrlToClipboard('.(int) $row[ExportCustomer::$definition['primary']].')" ><i class="fad fa-copy"></i></button>]]></cell>';
        $xml[] = '<row id="'.(int) $row[ExportCustomer::$definition['primary']].'">'.implode("\r\n\t", $row_xml).'</row>';
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
                <param><![CDATA[#numeric_filter,#select_filter,#select_filter,#text_filter,#text_filter,#text_filter,#text_filter,]]></param>
            </call>
        </afterInit>
        <column id="<?php echo ExportCustomer::$definition['primary']; ?>" width="50" type="ro" align="left" sort="int"><?php echo _l('ID'); ?></column>
        <column id="filter" width="200" type="ro" align="left" sort="str"><?php echo _l('Filters'); ?></column>
        <column id="mapping" width="200" type="ro" align="left" sort="str"><?php echo _l('Templates'); ?></column>
        <column id="iso" width="50" type="ro" align="center" sort="str"><?php echo _l('Lang'); ?></column>
        <column id="filename" width="200" type="ro" align="left" sort="str"><?php echo _l('Exported filename'); ?></column>
        <column id="date_last_export" width="150" type="ro" align="left" sort="str"><?php echo _l('Date last export'); ?></column>
        <column id="url_export" width="*" type="rotxt" align="left" sort="str"><?php echo _l('CRON url'); ?></column>
        <column id="copy" width="60" type="ro" align="center" sort="str"><?php echo _l('Share'); ?></column>
    </head>
    <?php
    echo $xml;
    ?>
</rows>