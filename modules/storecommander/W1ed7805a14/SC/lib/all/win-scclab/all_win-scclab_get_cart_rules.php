<?php

function getCartRules()
{
    $id_scc_customer = Tools::getValue('cr_prefix');
    $id_shop = Tools::getValue('cr_idshop');
    $cart_rule_prefix = "SCC".$id_scc_customer;

    $avail = array();
    $total = array();

    if (version_compare(_PS_VERSION_, '1.5.0.1', '>=')) {
        $sql = 'SELECT cr.id_cart_rule as cart_rule_id, cr.code, cr.reduction_amount as amount, cr.quantity, cr.active, cr.date_from, cr.date_to
                FROM ' . _DB_PREFIX_ . 'cart_rule cr
                WHERE cr.code LIKE "' . pSQL($cart_rule_prefix) . '%"
                ORDER BY amount, cart_rule_id';

        $sql_avail = 'SELECT CAST(cr.reduction_amount as UNSIGNED) as amount, count(*) as count
                FROM ' . _DB_PREFIX_ . 'cart_rule cr
                WHERE cr.code LIKE "' . pSQL($cart_rule_prefix) . '%"
                AND cr.reduction_amount IN (20,30,50,100)
                AND cr.quantity = 1
                GROUP BY amount
                ORDER BY amount';

        $sql_total = 'SELECT count(*) as count, SUM(CAST(cr.reduction_amount as UNSIGNED)) as total
                FROM ' . _DB_PREFIX_ . 'cart_rule cr
                WHERE cr.code LIKE "' . pSQL($cart_rule_prefix) . '%"
                AND cr.reduction_amount IN (20,30,50,100)';
    } else {
        $sql = 'SELECT cr.id_discount as cart_rule_id, cr.code, cr.reduction_amount as amount, cr.quantity, cr.active, cr.date_from, cr.date_to
                FROM ' . _DB_PREFIX_ . 'discount cr
                WHERE cr.code LIKE "' . pSQL($cart_rule_prefix) . '%"
                ORDER BY amount, cart_rule_id';

        $sql_avail = 'SELECT CAST(cr.reduction_amount as UNSIGNED) as amount, count(*) as count
                FROM ' . _DB_PREFIX_ . 'discount cr
                WHERE cr.code LIKE "' . pSQL($cart_rule_prefix) . '%"
                AND cr.reduction_amount IN (20,30,50,100)
                AND cr.quantity = 1
                GROUP BY amount
                ORDER BY amount';

        $sql_total = 'SELECT count(*) as count, SUM(CAST(cr.reduction_amount as UNSIGNED)) as total
                FROM ' . _DB_PREFIX_ . 'discount cr
                WHERE cr.code LIKE "' . pSQL($cart_rule_prefix) . '%"
                AND cr.reduction_amount IN (20,30,50,100)';
    }

    // Userdata for totals of available gift cards by amount
    $res_avail = Db::getInstance()->ExecuteS($sql_avail);
    foreach ($res_avail as $r) {
        $avail[$r['amount']]=$r['count'];
    }
    echo '<userdata name="available">'.json_encode($avail).'</userdata>'."\n";

    // Userdata for total amount (global)
    $total = Db::getInstance()->ExecuteS($sql_total);
    echo '<userdata name="total">'.json_encode($total).'</userdata>'."\n";

    $res = Db::getInstance()->ExecuteS($sql);
    foreach ($res as $row) {
        echo '<row id="' . $row['cart_rule_id'] . '">';
        echo '<cell>' . $row['cart_rule_id'] . '</cell>';
        echo '<cell style="font-family: monospace">' . $row['code'] . '</cell>';
        echo '<cell>' . (int)$row['amount'] . '</cell>';
        echo '<cell>' . (int)$row['quantity'] . '</cell>';
        //echo '<cell>'. !empty($row['active']) ? _l('Yes') : _l('No') .'</cell>';
        echo '<cell>' . $row['date_from'] . '</cell>';
        echo '<cell>' . $row['date_to'] . '</cell>';
        echo '<cell>' . $id_shop . '</cell>';
        echo '<cell>' . $id_scc_customer . '</cell>';
        echo '</row>'."\n";
    }
}
//XML HEADER
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
<rows parent="0">
    <head>
        <afterInit>
            <call command="attachHeader"><param><![CDATA[#numeric_filter,#text_filter,#select_filter,#select_filter,#text_filter,#text_filter,,]]></param></call>
        </afterInit>
        <column id="id_cart_rule" width="100" type="ro" align="right" sort="int">ID <?php echo _l('Cart Rule'); ?></column>
        <column id="code" width="400" type="ro" align="left" sort="str"><?php echo _l('Code'); ?></column>
        <column id="amount" width="100" type="ro" align="right" sort="int"><?php echo _l('Amount'); ?></column>
        <column id="quantity" width="100" type="ro" align="right" sort="int"><?php echo _l('Quantity'); ?></column>
        <column id="date_from" width="250" type="ro" align="left" sort="str"><?php echo _l('Date from'); ?></column>
        <column id="date_to" width="250" type="ro" align="left" sort="str"><?php echo _l('Date to'); ?></column>
        <column id="id_shop" width="0" type="ro" align="left" sort="int">ID <?php echo _l('shop'); ?></column>
        <column id="id_scc_customer" width="0" type="ro" align="left" sort="int">ID SCC <?php echo _l('customer'); ?></column>
    </head>
    <?php
    getCartRules();
    ?>
</rows>
