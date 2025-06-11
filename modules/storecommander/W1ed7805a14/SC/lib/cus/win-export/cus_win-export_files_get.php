<?php
if (!defined('STORE_COMMANDER')) { exit; }

if (!defined('SC_ExportCustomers_ACTIVE') || (int)SC_ExportCustomers_ACTIVE !== 1)
{
    exit;
}

$files = ExportCustomer::getExportFiles();
$xml = array();
if (!empty($files))
{

    foreach ($files as $filePath)
    {
        $token = generateToken($filePath);
        $urlParams = array(
            'source' => 'export',
            'detail' => 'customers',
            'retrieve' => $token
        );
        $url = SC_ORK_EXTERNAL_URL.'cron/cron_init.php?'.http_build_query($urlParams).'&'.time();
        $row_xml = array();
        $row_xml[] = '<userdata name="token"><![CDATA['.$token.']]></userdata>';
        $row_xml[] = '<cell><![CDATA[<a href="'.$url.'" target="_blank">'.basename($filePath).'</a>]]></cell>';
        $row_xml[] = '<cell title="'._l('Copy to ClipBoard').'"><![CDATA[<button onclick="copyToClipBoard(\''.$url.'\',\'' . _l('Url successfully copied to clipboard', true). '\')" ><i class="fad fa-copy"></i></button>]]></cell>';
        $row_xml[] = '<cell><![CDATA['.number_format(filesize($filePath) / 1024, 2).']]></cell>';
        $row_xml[] = '<cell><![CDATA['.(date('Y-m-d H:i:s', filemtime($filePath))).']]></cell>';
        $xml[filemtime($filePath)] = '<row id="'.basename($filePath).'">'.implode("\r\n\t", $row_xml).'</row>';
    }
}
krsort($xml);
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
                <param><![CDATA[#text_filter,,#text_filter,#text_filter]]></param>
            </call>
        </afterInit>
        <column id="filename" width="130" type="ro" align="left" sort="int"><?php echo _l('Filename'); ?></column>
        <column id="share" width="60" type="ro" align="center" sort="str"><?php echo _l('Share'); ?></column>
        <column id="filesize" width="50" type="ed" align="left" sort="str"><?php echo _l('Filesize'); ?> (Ko)</column>
        <column id="date_upd" width="140" type="ro" align="left" sort="str"><?php echo _l('Date update'); ?></column>
    </head>
    <?php
    echo $xml;
    ?>
</rows>