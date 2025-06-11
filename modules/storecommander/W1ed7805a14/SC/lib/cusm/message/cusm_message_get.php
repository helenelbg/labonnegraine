<?php
    $id_discussion = (int) Tools::getValue('id_discussion');

    function getRowsFromDB($id_customer_thread)
    {
        $sql = 'SELECT cm.*,
                        CONCAT(c.`firstname`," ",c.`lastname`) as customer,
                        IF(cm.id_employee > 0 , CONCAT(e.firstname," ",e.lastname),"") as last_employee
                FROM `'._DB_PREFIX_.'customer_message` cm
                LEFT JOIN `'._DB_PREFIX_.'employee` e
                    ON (e.id_employee = cm.id_employee)
                LEFT JOIN `'._DB_PREFIX_.'customer_thread` ct
                    ON (ct.id_customer_thread = cm.id_customer_thread)
                LEFT JOIN `'._DB_PREFIX_.'customer` c
                    ON (c.id_customer = ct.id_customer)
                WHERE cm.id_customer_thread = '.(int) $id_customer_thread.'
                ORDER BY cm.`date_add` DESC';

        $res = Db::getInstance()->ExecuteS($sql);
        $xml = '';
        if (empty($res))
        {
            return $xml;
        }
        foreach ($res as $row)
        {
            $background_color = 'e1ffe1';
            $name = $row['customer'].' ('._l('Customer').')';
            if (!empty($row['last_employee']))
            {
                $name = $row['last_employee'].' ('._l('Advisor').')';
                $background_color = 'f8dfff';
            }

            $text_color = '';
            if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
            {
                if ($row['private'])
                {
                    $row['private'] = _l('Yes');
                    $text_color = '666666';
                }
                else
                {
                    $row['private'] = _l('No');
                    $text_color = '000000';
                }
            }

            if (version_compare(_PS_VERSION_, '1.5.0.0', '<'))
            {
                $row['message'] = html_entity_decode($row['message'], ENT_COMPAT, 'UTF-8');
            }

            $attach = '';
            if (!empty($row['file_name']))
            {
                if (file_exists(SC_MAIL_ATTACHMENT_DIR.$row['file_name']))
                {
                    $filePath = Tools::getShopDomainSsl(true).__PS_BASE_URI__.'upload/'.$row['file_name'];
                }
                if (isset($filePath))
                {
                    $attach = '<a href="'.$filePath.'" target="_blank"><img src="lib/img/picture_go.png" alt="'._l('download').'" title="'._l('download').'"/></a>';
                }
            }

            $xml .= "<row id='".$row['id_customer_message']."' style='color: #".$text_color.';background-color: #'.$background_color."; border-bottom: 1px solid #9ca0a8;'>";
            $xml .= '<cell><![CDATA['.$name.']]></cell>';
            $xml .= '<cell><![CDATA['.$row['date_add'].']]></cell>';
            if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
            {
                $xml .= '<cell><![CDATA['.$row['private'].']]></cell>';
            }

            $xml .= '<cell><![CDATA['.$attach.']]></cell>';
            $xml .= "<cell style='white-space: normal;color: #".$text_color.';background-color: #'.$background_color.";border-bottom: 1px solid #9ca0a8;'><![CDATA[<br/>".nl2br($row['message']).'<br/><br/>]]></cell>';
            $xml .= '</row>';
        }

        return $xml;
    }

    $xml = getRowsFromDB($id_discussion);

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
<call command="attachHeader"><param><![CDATA[#select_filter,#text_filter,<?php if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
{
    echo '#select_filter';
}  ?>,#text_filter]]></param></call>
</beforeInit>
<column id="customer_name" width="140" type="ro" align="left" sort="str"><?php echo _l('Sender'); ?></column>
<column id="date_add" width="140" type="ro" align="left" sort="str"><?php echo _l('Date add'); ?></column>
<?php if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) { ?>
<column id="private" width="40" type="ro" align="center" sort="str"><?php echo _l('Private'); ?></column>
<?php } ?>
    <column id="attachment" width="50" type="ro" align="left" sort="str"><?php echo _l('Attachment'); ?></column>
    <column id="message" width="*" type="txt" align="left" sort="str"><?php echo _l('Message'); ?></column>
</head>
<?php
    echo '<userdata name="uisettings">'.uisettings::getSetting('cusm_message').'</userdata>'."\n";
    echo $xml;
?>
</rows>
