<?php

    $xml = '';
    $multiple = false;

    if (Tools::getValue('ids'))
    {
        if (strpos(Tools::getValue('ids'), ',') !== false)
        {
            $multiple = true;
        }

        if (SCMS)
        {
            $shops = array();
            $shops_array = Shop::getShops(false);
            foreach ($shops_array as $shop)
            {
                $shops[$shop['id_shop']] = $shop['name'];
            }
        }

        $ids = Tools::getValue('ids');
        $hasAffiliateSelection = (int) Tools::getValue('hasAffiliateSelection');

        $sql = 'SELECT o.*
                        FROM '._DB_PREFIX_.'orders o'.
// ne pas utiliser inner si selection d'affilies
                            ($hasAffiliateSelection ? '' : ' INNER JOIN '._DB_PREFIX_.'scaff_commission scom ON o.id_order = scom.order_id').'
                        WHERE o.id_customer IN ('.pInSQL($ids).')
                        GROUP BY o.id_order
                        ORDER BY o.id_order';
        $res = Db::getInstance()->ExecuteS($sql);
        foreach ($res as $row)
        {
            $order = new Order($row['id_order']);
            $status = $order->getCurrentStateFull($cookie->id_lang);

            $membre = null;
            if (!empty($row['id_customer']))
            {
                $sql = 'SELECT *
                        FROM '._DB_PREFIX_."customer
                        WHERE id_customer = " .(int) $row['id_customer'] . "";
                // rajotuer left join pour recup date affiliation
                // skip commandes < date affiliation
                $membre = Db::getInstance()->getRow($sql);
                if ($hasAffiliateSelection && $row['date_add'] < $membre['scaff_partner_date_add'])
                {
                    continue;
                }
            }
            $xml .= "<row id='".$row['id_order']."'>";
            $xml .= '<cell>'.$row['id_order'].'</cell>';
            if (SCMS)
            {
                $xml .= '<cell><![CDATA['.$shops[$row['id_shop']].']]></cell>';
            }
            if (!empty($membre['firstname']) && !empty($membre['lastname']))
            {
                $xml .= '<cell><![CDATA['.($membre['firstname']).']]></cell>';
                $xml .= '<cell><![CDATA['.($membre['lastname']).']]></cell>';
            }
            else
            {
                $xml .= '<cell><![CDATA[]]></cell>';
                $xml .= '<cell><![CDATA[]]></cell>';
            }
            $xml .= '<cell>'.$row['date_add'].'</cell>';
            $xml .= '<cell><![CDATA['.$status['name'].']]></cell>';
            $xml .= '<cell>'.number_format($row['total_products'], 2, '.', '').'</cell>';
            $xml .= '<cell>'.number_format($row['total_paid'], 2, '.', '').'</cell>';
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
<call command="attachHeader"><param><![CDATA[#numeric_filter<?php if (SCMS) { ?>,#select_filter<?php } ?>,#text_filter,#text_filter,#text_filter,#select_filter,#numeric_filter,#numeric_filter]]></param></call>
<call command="attachFooter"><param><![CDATA[,<?php if (SCMS) { ?>,<?php } ?>,,,,#stat_total,#stat_total]]></param></call>
</beforeInit>
<column id="id_order" width="40" type="ro" align="right" sort="int"><?php echo _l('ID'); ?></column>
<?php if (SCMS) { ?>
<column id="id_shop" width="80" type="ro" align="left" sort="str"><?php echo _l('Shop'); ?></column>
<?php } ?>
<column id="firstname" width="100" type="ro" align="left" sort="str_custom"><?php echo _l('Firstname'); ?></column>
<column id="lastname" width="100" type="ro" align="left" sort="str_custom"><?php echo _l('Lastname'); ?></column>
<column id="date_add" width="110" type="ro" align="left" sort="date"><?php echo _l('Ordered on'); ?></column>
<column id="status" width="80" type="ro" align="left" sort="str_custom"><?php echo _l('Status'); ?></column>
<column id="total_products" width="150" type="ro" align="right" sort="int"><?php echo _l('Total product'); ?></column>
<column id="total_paid" width="100" type="ro" align="right" sort="int"><?php echo _l('Total paid'); ?></column>
<afterInit>
<call command="enableHeaderMenu"></call>
</afterInit>
</head>
<?php
    echo '<userdata name="uisettings">'.uisettings::getSetting('gmaorder').'</userdata>'."\n";
    echo $xml;
?>
</rows>
