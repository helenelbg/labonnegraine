<?php
$id_lang = (int) Tools::getValue('id_lang', 0);
$id_segment = (Tools::getValue('id_segment'));

$xml = '';

if (!empty($id_segment))
{
    $res = array();
    $ids = explode(',', $id_segment);
    foreach ($ids as $id_segment)
    {
        $res_segment = array();
        $segment = new ScSegment($id_segment);
        if ($segment->type == 'manual')
        {
            $sql = "SELECT *, 'manual' AS type
                        FROM "._DB_PREFIX_."sc_segment_element
                        WHERE id_segment =" .(int) $id_segment . "
                        ORDER BY type_element, id_element";

            $res_segment_temp = Db::getInstance()->ExecuteS($sql);

            if (!is_array($res_segment_temp))
            {
                $res_segment_temp = array();
            }
            foreach ($res_segment_temp as $id => $values)
            {
                $res_segment[$values['type_element'].'_'.$values['id_element']] = $values;
            }
        }
        elseif ($segment->type == 'auto')
        {
            $res_segment_temp = SegmentHook::hookByIdSegment('segmentAutoSqlQueryGrid', $segment, array('id_lang' => $id_lang));
            if (!is_array($res_segment_temp))
            {
                $res_segment_temp = array();
            }
            foreach ($res_segment_temp as $id => $values)
            {
                $res_segment[$values['id']] = $values;
                $res_segment[$values['id']]['type'] = 'auto';
            }
        }
        if (!empty($res_segment))
        {
            $res = array_merge($res, $res_segment);
        }
    }

    foreach ($res as $row)
    {
        $id = '';
        $type = '';
        $name = '';
        $infos = '';
        if ($row['type'] == 'manual')
        {
            if ($row['type_element'] == 'product')
            {
                $type = _l('Product');
                if (SCMS)
                {
                    $element = new Product($row['id_element'], true);
                }
                else
                {
                    $element = new Product($row['id_element']);
                }
                $id = $row['id_element'];
                $name = $element->name[$id_lang];
                $infos = $element->reference;
            }
            elseif ($row['type_element'] == 'customer')
            {
                $type = _l('Customer');
                $id = $row['id_element'];
                $element = new Customer($row['id_element']);
                $name = $element->firstname.' '.$element->lastname;
                $infos = $element->email;
            }
            elseif ($row['type_element'] == 'order')
            {
                $type = _l('Order');
                $id = $row['id_element'];
                $element = new Order($row['id_element']);
                $name = $element->reference;
                $infos = _l('Order placed ').$element->date_add;
            }
            elseif ($row['type_element'] == 'customer_service')
            {
                $type = _l('Customer service');
                $id = $row['id_customer_thread'];
                $element = new CustomerThread($row['id_customer_thread']);
                $customer = new Customer($element->id_customer);
                $name = _l('Discussion with ').$customer->firstname.' '.$customer->lastname;
                $infos = $element->date_add.' / '._l('Customer').' #'.$element->id_customer.' '.$customer->email;
            }
            $row['id'] = $row['id_segment_element'];
        }
        elseif ($row['type'] == 'auto')
        {
            $id = (!empty($row['id_display']) ? $row['id_display'] : $row['id']);
            $type = $row[0];
            $name = $row[1];
            $infos = $row[2];
        }

        $xml .= "<row id='".$row['id']."'>";
        $xml .= '<cell><![CDATA['.$id.']]></cell>';
        $xml .= '<cell><![CDATA['.$type.']]></cell>';
        $xml .= '<cell><![CDATA['.$name.']]></cell>';
        $xml .= '<cell><![CDATA['.$infos.']]></cell>';
        $xml .= '</row>';
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
<rows id="0">
    <head>
        <beforeInit>
            <call command="attachHeader"><param><![CDATA[#numeric_filter,#select_filter,#text_filter,#text_filter]]></param></call>
        </beforeInit>
        <column id="id" width="100" type="ro" align="left" sort="int"><?php echo _l('Id'); ?></column>
        <column id="type_element" width="100" type="ro" align="left" sort="str"><?php echo _l('Type'); ?></column>
        <column id="element" width="200" type="ro" align="left" sort="str"><?php echo _l('Element'); ?></column>
        <column id="infos" width="300" type="ro" align="left" sort="str"><?php echo _l('Additional information'); ?></column>
        <afterInit>
            <call command="enableHeaderMenu"></call>
        </afterInit>
    </head>
    <?php
    echo $xml;
    ?>
</rows>
