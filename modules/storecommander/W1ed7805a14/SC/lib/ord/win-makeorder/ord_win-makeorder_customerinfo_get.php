<?php

$id_lang = (int) Tools::getValue('id_lang');
$id_customer = (int) Tools::getValue('id_customer');

if (!empty($id_customer))
{
    $customer = new Customer((int) $id_customer);

    $last_order_date = '';
    $nb_orders = 0;
    $total_paid_it = 0;
    $total_paid_et = 0;
    $total_paid_it_12 = 0;
    $total_paid_et_12 = 0;

    $one_year = date('Y-m-d', strtotime('-12 month', strtotime('now'))).' 00:00:00';

    $sql = '
            SELECT *
            FROM '._DB_PREFIX_.'orders
            WHERE id_customer = "'.(int) $id_customer.'"
                AND valid="1"
            ORDER BY date_add DESC';
    $orders = Db::getInstance()->ExecuteS($sql);
    if (!empty($orders))
    {
        foreach ($orders as $order)
        {
            if (empty($last_order_date))
            {
                $last_order_date = $order['date_add'];
            }
            elseif ($last_order_date < $order['date_add'])
            {
                $last_order_date = $order['date_add'];
            }

            $total_paid_et += $order['total_paid_tax_excl'];
            $total_paid_it += $order['total_paid_tax_incl'];

            if ($order['date_add'] >= $one_year)
            {
                $total_paid_et_12 += $order['total_paid_tax_excl'];
                $total_paid_it_12 += $order['total_paid_tax_incl'];
            }

            ++$nb_orders;
        }
    }

    $id_currency = Configuration::get('PS_CURRENCY_DEFAULT') * 1;
    $total_paid_it = Tools::displayPrice($total_paid_it, $id_currency, false);
    $total_paid_et = Tools::displayPrice($total_paid_et, $id_currency, false);
    $total_paid_et_12 = Tools::displayPrice($total_paid_et_12, $id_currency, false);
    $total_paid_it_12 = Tools::displayPrice($total_paid_it_12, $id_currency, false);

    $groups_list = '';
    $groups = $customer->getGroups();
    foreach ($groups as $group)
    {
        $group = new GroupCore((int) $group);
        if (!empty($groups_list))
        {
            $groups_list .= ', ';
        }
        $groups_list .= $group->name[$id_lang];
    } ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <style>
            body {
                padding:0;
                margin:0;
                font-family: Arial,sans-serif;
            }
            table{
                font-size: 13px;
            }
        </style>
    </head>
    <body>
        <table border="0" cellpadding="6px">
            <tr>
                <td align="right"><strong><?php echo _l('Create date:'); ?></strong></td>
                <td><?php echo Tools::displayDate($customer->date_add, $id_lang); ?></td>
            </tr>
            <tr>
                <td align="right"><strong><?php echo _l('Last order date:'); ?></strong></td>
                <td><?php echo !empty($last_order_date) ? Tools::displayDate($last_order_date, $id_lang) : '-'; ?></td>
            </tr>
            <tr>
                <td align="right"><strong><?php echo _l('Nb. orders:'); ?></strong></td>
                <td><?php echo $nb_orders; ?></td>
            </tr>
            <tr>
                <td align="right"><strong><?php echo _l('Total revenue:'); ?></strong></td>
                <td><?php echo $total_paid_et.' '._l('Tax excl.', 1).' / '.$total_paid_it.' '._l('Tax incl.', 1); ?></td>
            </tr>
            <tr>
                <td align="right"><strong><?php echo _l('Revenue last 12 months:'); ?></strong></td>
                <td><?php echo $total_paid_et_12.' '._l('Tax excl.', 1).' / '.$total_paid_it_12.' '._l('Tax incl.', 1); ?></td>
            </tr>
            <tr>
                <td align="right"><strong><?php echo _l('Group(s):'); ?></strong></td>
                <td><?php echo $groups_list; ?></td>
            </tr>
        </table>
    </body>
    </html>

    <?php
}
