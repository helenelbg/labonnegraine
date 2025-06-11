<?php

    $xml = '';
    $multiple = false;

    if (!empty($_GET['ids']))
    {
        if (SCMS)
        {
            $shops = array();
            $shops_array = Shop::getShops(false);
            foreach ($shops_array as $shop)
            {
                $shops[$shop['id_shop']] = $shop['name'];
            }
        }

        if (strpos($_GET['ids'], ',') !== false)
        {
            $multiple = true;
        }

        $ids = (Tools::getValue('ids'));

        if (!empty($ids))
        {
            $sql = 'SELECT *
                            FROM '._DB_PREFIX_.'customer
                            WHERE scaff_partner_id IN ('.pInSQL($ids).')
                            ORDER BY id_customer';

            $res = Db::getInstance()->ExecuteS($sql);
            foreach ($res as $row)
            {
                $ce = null;
                if (!empty($row['scaff_partner_id']))
                {
                    $sql = 'SELECT code
                            FROM '._DB_PREFIX_."scaff_partner
                            WHERE id_partner = '".psql($row['scaff_partner_id'])."'";
                    $partner = Db::getInstance()->getRow($sql);
                }
                $xml .= "<row id='".$row['id_customer']."'>";
                if ($multiple && !empty($partner['code']))
                {
                    $xml .= '<cell><![CDATA['.($partner['code']).']]></cell>';
                }
                elseif ($multiple)
                {
                    $xml .= '<cell><![CDATA[]]></cell>';
                }
                $xml .= '<cell>'.$row['id_customer'].'</cell>';
                if (SCMS)
                {
                    $xml .= '<cell><![CDATA['.$shops[$row['id_shop']].']]></cell>';
                }
                $xml .= '<cell><![CDATA['.($row['firstname']).']]></cell>';
                $xml .= '<cell><![CDATA['.($row['lastname']).']]></cell>';
                $xml .= '<cell><![CDATA['.$row['email'].']]></cell>';
                $xml .= '<cell>'.$row['scaff_partner_date_add'].'</cell>';
                $xml .= '<cell>'.$row['scaff_partner_status'].'</cell>';
                $xml .= '<cell>'.$row['scaff_partner_mode'].'</cell>';
                $xml .= '<cell>'.$row['scaff_partner_duration'].'</cell>';
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
<call command="attachHeader"><param><![CDATA[<?php if ($multiple)
{
    echo '#text_filter,';
} ?>#numeric_filter<?php if (SCMS) { ?>,#select_filter<?php } ?>,#text_filter,#text_filter,#text_filter,#text_filter,#select_filter,#select_filter,#text_filter]]></param></call>
<call command="attachFooter"><param><![CDATA[<?php if ($multiple)
{
    echo '#,';
} ?>{#stat_count} <?php echo _l('Affiliates'); ?><?php if (SCMS) { ?>,#cspan<?php } ?>,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan]]></param></call>
</beforeInit>
<?php if ($multiple) { ?>
<column id="id_ce" width="80" type="ro" align="left" sort="int"><?php echo _l('Partner'); ?></column>
<?php } ?>
<column id="id_customer" width="40" type="ro" align="right" sort="int"><?php echo _l('ID'); ?></column>
<?php if (SCMS) { ?>
<column id="id_shop" width="80" type="ro" align="left" sort="str"><?php echo _l('Shop'); ?></column>
<?php } ?>
<column id="firstname" width="100" type="ro" align="left" sort="str_custom"><?php echo _l('Firtname'); ?></column>
<column id="lastname" width="100" type="ro" align="left" sort="str_custom"><?php echo _l('Lastname'); ?></column>
<column id="email" width="140" type="ro" align="left" sort="int"><?php echo _l('Email'); ?></column>
<column id="scaff_partner_date_add" width="110" type="ed" align="left" sort="date"><?php echo _l('Affiliated'); ?></column>
<column id="scaff_partner_status" width="80" type="coro" align="right"><?php echo _l('Status'); ?>
    <option value="active"><?php echo _l('Active'); ?></option>
    <option value="expired"><?php echo _l('Expired'); ?></option>
</column>
<column id="scaff_partner_mode" width="80" type="coro" align="right" sort="str"><?php echo _l('Mode'); ?>
    <option value="unlimited"><?php echo _l('Unlimited'); ?></option>
    <option value="limited"><?php echo _l('Limited'); ?></option>
    <option value="firstorder"><?php echo _l('First order'); ?></option>
</column>
<column id="scaff_partner_duration" width="100" type="dhxCalendarA" format="%Y-%m-%d" align="right" sort="date"><?php echo _l('Duration'); ?></column>
<afterInit>
<call command="enableHeaderMenu"></call>
</afterInit>
</head>
<?php
echo '<userdata name="uisettings">'.uisettings::getSetting('gmaaffiliate').'</userdata>'."\n";
    echo $xml;
?>
</rows>
