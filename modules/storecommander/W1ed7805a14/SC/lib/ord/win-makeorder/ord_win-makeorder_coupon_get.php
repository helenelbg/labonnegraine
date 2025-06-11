<?php

$id_lang = (int) Tools::getValue('id_lang');
$id_customer = (int) Tools::getValue('id_customer');
$id_cart = (int) Tools::getValue('id_cart');
$id_shop = (int) Tools::getValue('id_shop');

function getRowsFromDB()
{
    global $id_lang,$id_shop,$id_cart,$id_customer;

    $cart_rules = SCI::getCustomerCartRules($id_lang, $id_customer, true);
    $xml = '';
    if (!empty($cart_rules))
    {
        foreach ($cart_rules as $row)
        {
            if (version_compare(_PS_VERSION_, '1.4.11.1', '<='))
            {
                $xml .= "<row id='".$row['id_discount']."'>";
                $xml .= '<cell>'.$row['id_discount'].'</cell>';
                $xml .= '<cell><![CDATA['.$row['name'].']]></cell>';
                $xml .= '<cell><![CDATA['.$row['description'].']]></cell>';
                $xml .= '<cell><![CDATA['.($row['id_discount_type'] == 1 ? $row['value'] : 0).']]></cell>'; // percent
                $xml .= '<cell><![CDATA['.($row['id_discount_type'] == 2 ? $row['value'] : 0).']]></cell>'; // value
                $xml .= '<cell><![CDATA['.($row['id_discount_type'] == 3 ? 1 : 0).']]></cell>'; // free shipping
            }
            else
            {
                $xml .= "<row id='".$row['id_cart_rule']."'>";
                $xml .= '<cell>'.$row['id_cart_rule'].'</cell>';
                $xml .= '<cell><![CDATA['.$row['code'].']]></cell>';
                $xml .= '<cell><![CDATA['.$row['name'].']]></cell>';
                $xml .= '<cell><![CDATA['.$row['reduction_percent'].']]></cell>';
                $xml .= '<cell><![CDATA['.$row['reduction_amount'].']]></cell>';
                $xml .= '<cell><![CDATA['.$row['free_shipping'].']]></cell>';
            }
            $xml .= '<cell><![CDATA['.$row['date_to'].']]></cell>';
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

$xml = getRowsFromDB();
?>
<rows id="0">
    <head>
        <beforeInit>
            <call command="attachHeader"><param><![CDATA[#numeric_filter,#text_filter,#text_filter,#numeric_filter,#numeric_filter,#numeric_filter,#text_filter]]></param></call>
        </beforeInit>
        <column id="id_cart_rule" width="45" type="ro" align="right" sort="int"><?php echo _l('ID'); ?></column>

        <column id="code" width="100" type="edtxt" align="left" sort="str"><?php echo _l('Code'); ?></column>
        <column id="name" width="120" type="ro" align="left" sort="str"><?php echo _l('Name'); ?></column>

        <column id="reduction_percent" width="80" type="ro" align="left" sort="str"><?php echo _l('reduction_percent'); ?></column>
        <column id="reduction_amount" width="80" type="ro" align="left" sort="str"><?php echo _l('reduction_price'); ?></column>
        <column id="free_shipping" width="80" type="ro" align="left" sort="str"><?php echo _l('Free shipping'); ?></column>
        <column id="date_to" width="120" type="ro" align="left" sort="str"><?php echo _l('Date to'); ?></column>
    </head>
    <?php
    echo '<userdata name="uisettings">'.uisettings::getSetting('makeOrder_coupon_grid').'</userdata>'."\n";
    echo $xml;
    ?>
</rows>
