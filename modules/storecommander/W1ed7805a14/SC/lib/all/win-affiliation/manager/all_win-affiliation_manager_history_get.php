<?php
$partners = Tools::getValue('partners', 0);
$id_lang = (int) Tools::getValue('id_lang', 0);

$xml = '';

if (!empty($partners))
{
    $sql = 'SELECT sh.*,c.firstname, c.lastname
            FROM '._DB_PREFIX_.'scaff_history sh
                INNER JOIN '._DB_PREFIX_.'scaff_partner sp ON (sh.id_partner = sp.id_partner)
                    INNER JOIN '._DB_PREFIX_.'customer c ON (sp.customer_id = c.id_customer)
            WHERE sh.id_partner IN ('.pInSQL($partners).')
            ORDER BY sh.date_add DESC, sh.id_scaff_history DESC';
    $res = Db::getInstance()->ExecuteS($sql);
    foreach ($res as $row)
    {
        $name = '';
        if ($row['name'] == 'code')
        {
            $name = _l('Code');
        }
        elseif ($row['name'] == 'percent_comm')
        {
            $name = _l('Percent');
        }
        elseif ($row['name'] == 'mode')
        {
            $name = _l('Mode');
        }
        elseif ($row['name'] == 'duration')
        {
            $name = _l('Duration');
        }

        $xml .= "<row id='".$row['id_scaff_history']."'>";
        $xml .= '<cell><![CDATA['.($row['firstname'].' '.$row['lastname']).']]></cell>';
        $xml .= '<cell><![CDATA['.$name.']]></cell>';
        $xml .= '<cell><![CDATA['.($row['old_value']).']]></cell>';
        $xml .= '<cell><![CDATA['.($row['value']).']]></cell>';
        $xml .= '<cell><![CDATA['.($row['date_add']).']]></cell>';
        $xml .= '</row>';
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
<beforeInit>
<call command="attachHeader"><param><![CDATA[#select_filter,#select_filter,#text_filter,#text_filter,#text_filter]]></param></call>
</beforeInit>
<column id="partner" width="200" type="ro" align="right" sort="int"><?php echo _l('Partner'); ?></column>
<column id="name" width="100" type="ro" align="left" sort="str_custom"><?php echo _l('Field'); ?></column>
<column id="old_value" width="100" type="ro" align="left" sort="str_custom"><?php echo _l('Old value'); ?></column>
<column id="value" width="100" type="ro" align="left" sort="str_custom"><?php echo _l('New value'); ?></column>
<column id="date_add" width="120" type="ro" align="right" sort="date"><?php echo _l('Date'); ?></column>
<afterInit>
<call command="enableHeaderMenu"></call>
</afterInit>
</head>
<?php
    echo $xml;
?>
</rows>