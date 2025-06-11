<?php

$id_lang = (int) Tools::getValue('id_lang');
$type = (Tools::getValue('type', 'all'));

require dirname(__FILE__).'/tools.php';
$terminatorTools = new terminatorTools();

require dirname(__FILE__).'/actions.php';
$xml = '';
foreach ($actions as $id => $action)
{
    if ($type == 'all' || $action['type'] == $type)
    {
        $xml .= "<row id='".$id."'>";
        $xml .= '<cell>0</cell>';
        $xml .= '<cell><![CDATA['.$action['name'].']]></cell>';

        $currently = '-';
        if (!empty($action['currently']))
        {
            $currently = eval('return '.$action['currently'].';');
        }
        $xml .= '<cell><![CDATA['.$currently.']]></cell>';

        if (!empty($action['param']))
        {
            if ($action['default_value'])
            {
                $xml .= '<cell><![CDATA['.$action['default_value'].']]></cell>';
            }
            else
            {
                $xml .= '<cell><![CDATA[]]></cell>';
            }
            $xml .= '<userdata name="has_param">1</userdata>';
        }
        else
        {
            $xml .= '<cell type="ro" style="background-color:#dddddd;"><![CDATA[]]></cell>';
            $xml .= '<userdata name="has_param">0</userdata>';
        }
        $xml .= '</row>';
    }
}

//XML HEADER
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
    <beforeInit>
    <call command="attachHeader"><param><![CDATA[,#text_filter,,]]></param></call>
    </beforeInit>
    <column id="todo" width="80" type="ch" align="center" sort="int"><?php echo _l('Perform'); ?></column>
    <column id="name" width="500" type="ro" align="left" sort="str"><?php echo _l('Name'); ?></column>
    <column id="currently" width="100" type="ro" align="center" sort="str"><?php echo _l('Currently'); ?></column>
    <column id="param" width="90" type="ed" align="right" sort="str"><?php echo _l('Parameter'); ?></column>
    <afterInit>
    <call command="enableMultiselect"><param>1</param></call>
    </afterInit>
</head>
<?php

    echo $xml;
?>
</rows>
