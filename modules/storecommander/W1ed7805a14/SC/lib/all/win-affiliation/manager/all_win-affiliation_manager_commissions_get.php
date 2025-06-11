<?php

    $xml = '';
    $multiple = false;

    if (SCMS)
    {
        $shops = array();
        $shops_array = Shop::getShops(false);
        foreach ($shops_array as $shop)
        {
            $shop['name'] = str_replace('&', _l('and'), $shop['name']);
            $shops[$shop['id_shop']] = $shop['name'];
        }
    }

    if (!empty($_GET['ids']))
    {
        if (strpos($_GET['ids'], ',') !== false)
        {
            $multiple = true;
        }

        $ids = Tools::getValue('ids');
        if (!empty($ids))
        {
            $sql = 'SELECT *
                            FROM '._DB_PREFIX_.'scaff_commission
                            WHERE id_partner IN ('.pInSQL($ids).')
                            ORDER BY id_commission';

            $res = Db::getInstance()->ExecuteS($sql);
            foreach ($res as $row)
            {
                $bgcolor = '';

                if ($row['active'] != 1)
                {
                    $row['status'] = 'waiting_active';
                    $bgcolor = 'background-color: #cccccc;';
                }

                $xml .= "<row id='".$row['id_commission']."'>";
                $xml .= '<cell>'.$row['id_commission'].'</cell>';
                if (SCMS)
                {
                    $xml .= '<cell><![CDATA['.$row['id_shop'].']]></cell>';
                }
                $xml .= '<cell>'.$row['id_partner'].'</cell>';
                $xml .= '<cell>'.$row['order_id'].'</cell>';
                $xml .= '<cell>'.$row['date_add'].'</cell>';
                $xml .= "<cell style='".$bgcolor."'>".$row['status'].'</cell>';
                $xml .= '<cell>'.$row['price'].'</cell>';
                $xml .= '<cell>'.$row['hidden'].'</cell>';
                $xml .= '</row>';
            }
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
<call command="attachHeader"><param><![CDATA[#numeric_filter<?php if (SCMS) { ?>,#select_filter<?php } ?>,#numeric_filter,#numeric_filter,#text_filter,#select_filter_strict,#numeric_filter,#select_filter]]></param></call>
<call command="attachFooter"><param><![CDATA[,<?php if (SCMS) { ?>,<?php } ?>,,,,#stat_total,]]></param></call>
</beforeInit>
<column id="id_commission" width="40" type="ro" align="right" sort="int"><?php echo _l('ID'); ?></column>
<?php if (SCMS) { ?>
<column id="id_shop" width="80" type="coro" align="left"><?php echo _l('Shop'); ?> 
    <?php
    foreach ($shops as $id => $name) { ?>
        <option value="<?php echo $id; ?>"><?php echo addslashes($name); ?></option>
    <?php } ?>
</column>
<?php } ?>
<column id="id_partner" width="80" type="edtxt" align="right" sort="str_custom"><?php echo _l('Partner ID'); ?></column>
<column id="order_id" width="80" type="edtxt" align="right" sort="str_custom"><?php echo _l('Order ID'); ?></column>
<column id="date_add" width="80" type="dhxCalendarA" format="%Y-%m-%d" align="right" sort="date"><?php echo _l('Date'); ?></column>
<column id="status" width="100" type="coro" align="left"><?php echo _l('Status'); ?>
    <option value="waiting_active"><?php echo _l('Awaiting order validation'); ?></option>
    <option value="waiting"><?php echo _l('Awaiting invoice'); ?></option>
    <option value="invoiced"><?php echo _l('Invoiced'); ?></option>
    <option value="paid"><?php echo _l('Paid'); ?></option>
    <option value="paid_order"><?php echo _l('Paid on order'); ?></option>
    <option value="cancelled"><?php echo _l('Cancelled'); ?></option>
</column>
<column id="price" width="100" type="edtxt" align="right" sort="str_custom"><?php echo _l('Amount'); ?></column>
<column id="hidden" width="40" type="ch" align="center" sort="int"><?php echo _l('Hidden'); ?></column>
<afterInit>
<call command="enableHeaderMenu"></call>
</afterInit>
</head>
<?php
echo '<userdata name="uisettings">'.uisettings::getSetting('gmacommission').'</userdata>'."\n";
    echo $xml;
?>
</rows>
