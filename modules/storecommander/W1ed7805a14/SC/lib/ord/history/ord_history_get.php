<?php

    $id_orders = (string) Tools::getValue('id_orders');

    // get order status
    $orderStatusPS = SCI::getScDisplayableOrderStates($sc_agent->id_lang);

    function getRowsFromDB($id_orders, $orderStatusPS)
    {
        $orderStatus = array();
        foreach ($orderStatusPS as $status)
        {
            $orderStatus[$status['id_order_state']] = $status;
        }

        $sql = 'SELECT oh.*,e.lastname,e.firstname
                FROM '._DB_PREFIX_.'order_history oh
                LEFT JOIN '._DB_PREFIX_.'employee e ON (oh.id_employee=e.id_employee)
                WHERE oh.id_order IN ('.pInSQL($id_orders).')
                ORDER BY oh.id_order DESC, oh.date_add DESC, oh.id_order_history DESC';
        $res = Db::getInstance()->ExecuteS($sql);
        $xml = '';
        if (!empty($res))
        {
            foreach ($res as $history)
            {
                $xml .= "<row id='".$history['id_order'].'-'.$history['id_order_history']."'>";
                $xml .= '<cell style="color:#999999">'.$history['id_order'].'</cell>';
                $xml .= '<cell style="color:#999999">'.$history['id_order_history'].'</cell>';
                $xml .= '<cell><![CDATA['.($history['id_employee'] != 0 ? $history['firstname'][0].'. '.$history['lastname'] : '').']]></cell>';
                $xml .= '<cell><![CDATA['.(isset($orderStatus[$history['id_order_state']]) ? $orderStatus[$history['id_order_state']]['name'] : '').']]></cell>';
                $xml .= '<cell>'.$history['date_add'].'</cell>';
                $xml .= '</row>';
            }
        }

        return $xml;
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

    $xml = getRowsFromDB($id_orders, $orderStatusPS);
?>
<rows id="0">
<head>
<column id="id_order" width="45" type="ro" align="right" sort="int"><?php echo _l('id order'); ?></column>
<column id="id_order_history" width="45" type="ro" align="right" sort="int"><?php echo _l('id history'); ?></column>
<column id="id_employee" width="80" type="ro" align="left" sort="str"><?php echo _l('Employee'); ?></column>
<column id="id_order_state" width="120" type="ro" align="left" sort="str"><?php echo _l('Order status'); ?></column>
<column id="date_add" width="140" type="ro" align="left" sort="str"><?php echo _l('Creation date'); ?></column>
</head>
<?php
    echo '<userdata name="uisettings">'.uisettings::getSetting('ord_history').'</userdata>'."\n";
    echo $xml;
?>
</rows>
