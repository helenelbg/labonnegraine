<?php
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
<beforeInit>
<call command="attachHeader"><param><![CDATA[,#text_filter]]></param></call>
<call command="enableColumnMove"><param>0</param></call>
</beforeInit>
<column id="color" width="60" type="ro" align="center" sort="str"><?php echo _l('Color'); ?></column>
<column id="help" width="640" type="ro" align="left" sort="str"><?php echo _l('Help'); ?></column>
<afterInit>
<call command="enableHeaderMenu"></call>
</afterInit>
</head>
<row id="e2c7d4">
    <cell bgColor="e2c7d4" ></cell>
    <cell><![CDATA[<?php echo _l('Affiliates, commissions and orders matching the selected element'); ?>]]></cell>
</row>
<row id="e7ab70">
    <cell bgColor="ffe1f0"></cell>
    <cell><![CDATA[<?php echo _l('Commissions included in paid total'); ?>]]></cell>
</row>
<row id="96e1ef">
    <cell bgColor="96e1ef"></cell>
    <cell><![CDATA[<?php echo _l('Total paid commissions (selected) – negative value'); ?>]]></cell>
</row>
<row id="D87B3C">
    <cell bgColor="D87B3C">MONCODE</cell>
    <cell><![CDATA[<?php echo _l('Discount voucher with restriction rules on products and/or on reduction amount'); ?>]]></cell>
</row>
</rows>