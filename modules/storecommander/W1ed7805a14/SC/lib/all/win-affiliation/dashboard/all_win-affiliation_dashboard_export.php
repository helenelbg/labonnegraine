<?php

$id_lang = (int) Tools::getValue('id_lang', 0);
$start_month = (int) Tools::getValue('start_month', 0);
$start_year = (int) Tools::getValue('start_year', 0);
$end_month = (int) Tools::getValue('end_month', 0);
$end_year = (int) Tools::getValue('end_year', 0);
$id_shop = (int) Tools::getValue('id_shop', '0');

$message = _l('Your dates are incorrect. Please check your dates.');
$rows = array();

if (!empty($start_month) && !empty($start_year) && !empty($end_month) && !empty($end_year))
{
    if ($end_year.'-'.$end_month >= $start_year.'-'.$start_month)
    {
        $i = 0;
        for ($y = $start_year; $y <= $end_year; ++$y)
        {
            for ($m = 1; $m <= 12; ++$m)
            {
                $add = true;
                if ($start_year == $y && $m < $start_month)
                {
                    $add = false;
                }
                if ($end_year == $y && $m > $end_month)
                {
                    $add = false;
                    break;
                }
                if ($add)
                {
                    $day_start = $y.'-'.$m.'-01';
                    $day_end = $y.'-'.$m.'-'.SCI::days_in_month(CAL_GREGORIAN, $m, $y);

                    /*$current_amount = rand(100, 10000);
                    $nb_cmd = rand(1, 20);*/
                    $orders_totals = SCAffCommission::GetTotalsAffiliatesOrdersByDates($day_start, $day_end, null, $id_shop);
                    $current_amount = $orders_totals['sum_total_products'];
                    $nb_cmd = (int) $orders_totals['nb_orders'];
                    $click = SCAffClick::GetNbClickByDate($day_start, $day_end, null, $id_shop);
                    $visiteur = SCAffClick::GetNbVisiteurByDate($day_start, $day_end, null, $id_shop);
                    $rows[] = array(
                        str_pad($m, 2, '0', STR_PAD_LEFT).'/'.$y,
                        number_format($current_amount, 2),
                        $click,
                        $visiteur,
                        $nb_cmd,
                        ((!empty($visiteur)) ? number_format($nb_cmd / $visiteur * 100) : '0').'%',
                    );
                    ++$i;
                }
            }
        }
    }
    else
    {
        $message = _l('Your dates are incorrect. Please check your dates.');
    }
}

// output headers so that the file is downloaded rather than displayed
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=affiliation_'.$start_month.'/'.$start_year.'-'.$end_month.'/'.$end_year.'.csv');

// create a file pointer connected to the output stream
$output = fopen('php://output', 'w');

// output the column headings
fputcsv($output, array(_l('Month'), _l('Total sales'), _l('Clicks'), _l('Visitors'), _l('Conversions'), _l('Rate')));

// loop over the rows, outputting them
if (!empty($rows) && count($rows) > 0)
{
    foreach ($rows as $row)
    {
        fputcsv($output, $row);
    }
}
else
{
    fputcsv($output, array($message, '', '', '', '', ''));
}
