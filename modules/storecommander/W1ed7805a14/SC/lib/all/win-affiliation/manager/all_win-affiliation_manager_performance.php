<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>SC - Affiliation</title>
<link type="text/css" rel="stylesheet" href="<?php echo SC_CSSDHTMLX; ?>" />
<link href="<?php echo SC_CSSSTYLE; ?>" rel="stylesheet" type="text/css"/>
<link href="lib/js/skins/message_default_02.css" rel="stylesheet" type="text/css"/>

<script src="<?php echo SC_JQUERY; ?>" type="text/javascript"></script>
<script type="text/javascript" src="<?php echo SC_JSDHTMLX; ?>"></script>
<script type="text/javascript" src="lib/js/message.js"></script>
</head>
<body>
<?php $id_lang = (int) Tools::getValue('id_lang', 0);
$id_partner = (int) Tools::getValue('id_partner', 0);

if (!empty($id_partner))
{
    ?>
<script type="text/javascript">

// Create interface
var dhxLayout = new dhtmlXLayoutObject(document.body, "2E");

dhxlAffPartPerfLayoutTop = dhxLayout.cells("a").attachLayout("2U");
dhxlAffPartPerfLayoutBottom = dhxLayout.cells("b").attachLayout("2U");

dhxlAffPartPerfChartClick = dhxlAffPartPerfLayoutTop.cells("a");
dhxlAffPartPerfChartClick.setText('<?php echo _l('Clicks & number of unique visitors', 1); ?>');
dhxlAffPartPerfChartCA = dhxlAffPartPerfLayoutTop.cells("b");
dhxlAffPartPerfChartCA.setText('<?php echo _l('Generated turnover excl. taxes', 1); ?>');
dhxlAffPartPerfChartAffilie = dhxlAffPartPerfLayoutBottom.cells("a");
dhxlAffPartPerfChartAffilie.setText('<?php echo _l('New affiliates & Sales generated', 1); ?>');
dhxlAffPartPerfChartTaux = dhxlAffPartPerfLayoutBottom.cells("b");
dhxlAffPartPerfChartTaux.setText('<?php echo _l('Conversion rate', 1); ?>');

// CHART CLICK
var clicks=[
    <?php
    // debut
        $year_moins = 0;
    $start_month = date('m');
    if ($start_month != 12)
    {
        $start_month = $start_month + 1;
        $year_moins = 1;
    }
    else
    {
        $start_month = '01';
    }

    $start_year = date('Y');
    $start_year = $start_year - $year_moins;

    // fin
    $end_month = date('m');
    $end_year = date('Y');

    // génération
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
                if ($i > 0)
                {
                    echo ",\n";
                }

                $day_start = $y.'-'.$m.'-01';
                $day_end = $y.'-'.$m.'-'.SCI::days_in_month(CAL_GREGORIAN, $m, $y);

                $click = SCAffClick::GetNbClickByDate($day_start, $day_end, $id_partner);
                $visiteur = SCAffClick::GetNbVisiteurByDate($day_start, $day_end, $id_partner);

                $monthName = date('M', mktime(0, 0, 0, $m, 10));

                echo '{"nbClick":"'.$click.'","nbVisitor":"'.$visiteur.'","month":"'._l($monthName).'"}';
                ++$i;
            }
        }
    } ?>
];

var chartConfig = {
        view: "line",
        value: "#nbClick#",
        item: {
            borderColor: "#58dccd",
            color: "#ffffff"
        },
        line: {
            color: "#58dccd",
            width: 3
        },
        tooltip: {
            template: "#nbClick#"
        },
        offset: 0,
        xAxis: {
            template: "#month#"
        },
        origin: 0,
        yAxis: {
            start: 0,
            template: function(obj) {
                return (obj % 20 ? "": obj);
            }
        },
        legend: {
            layout: "x",
            width: 75,
            align: "center",
            valign: "bottom",
            values: [{
                text: "<?php echo _l('Number of clicks', 1); ?>",
                color: "#58dccd"
            }, {
                text: "<?php echo _l('Number of unique visitors', 1); ?>",
                color: "#a7ee70"
            }],
            margin: 10
        }
    };

var chartClick = dhxlAffPartPerfChartClick.attachChart(chartConfig);

chartClick.addSeries({
    value: "#nbVisitor#",
    item: {
        borderColor: "#a7ee70",
        color: "#ffffff"
    },
    line: {
        color: "#a7ee70",
        width: 3
    },
    tooltip: {
        template: "#nbVisitor#"
    }
});
chartClick.parse(clicks, "json");


//CHART CA
var CAs=[
    <?php
    // debut
        $year_moins = 0;
    $start_month = date('m');
    if ($start_month != 12)
    {
        $start_month = $start_month + 1;
        $year_moins = 1;
    }
    else
    {
        $start_month = '01';
    }

    $start_year = date('Y');
    $start_year = $start_year - $year_moins;

    // fin
    $end_month = date('m');
    $end_year = date('Y');

    // génération
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
                if ($i > 0)
                {
                    echo ",\n";
                }

                $day_start = $y.'-'.$m.'-01';
                $day_end = $y.'-'.$m.'-'.SCI::days_in_month(CAL_GREGORIAN, $m, $y);

                //$amount = rand(100, 10000);
                $orders_totals = SCAffCommission::GetTotalsAffiliatesOrdersByDates($day_start, $day_end, $id_partner);
                $amount = $orders_totals['sum_total_products'];

                if ($i % 2 == 0)
                {
                    $color = '#58dccd';
                }
                else
                {
                    $color = '#a7ee70';
                }

                $monthName = date('M', mktime(0, 0, 0, $m, 10));

                echo '{"sales":"'.$amount.'","color":"'.$color.'","month":"'._l($monthName).'"}';
                ++$i;
            }
        }
    } ?>
];

var chartConfig = {
        view: "bar",
        value: "#sales#",
        label: "#sales#",
        color: "#color#",
        width: 30,
        padding: {
            left: 80
        },
        xAxis: {
            template: "#month#",
            title: "<?php echo _l('Months'); ?>"
        },
        yAxis: {
            title: "<?php echo _l('Generated turnover excl. taxes'); ?>",
            start: 0,
            step: 1000,
            template:function(value){
                return (value%20?"":value);
            }
        }
    };
var chartCA = dhxlAffPartPerfChartCA.attachChart(chartConfig);
chartCA.parse(CAs, "json");

// CHART AFFILIE
var Affilies=[
    <?php
    // debut
        $year_moins = 0;
    $start_month = date('m');
    if ($start_month != 12)
    {
        $start_month = $start_month + 1;
        $year_moins = 1;
    }
    else
    {
        $start_month = '01';
    }

    $start_year = date('Y');
    $start_year = $start_year - $year_moins;

    // fin
    $end_month = date('m');
    $end_year = date('Y');

    // génération
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
                if ($i > 0)
                {
                    echo ",\n";
                }

                $day_start = $y.'-'.$m.'-01';
                $day_end = $y.'-'.$m.'-'.SCI::days_in_month(CAL_GREGORIAN, $m, $y);

                $affilie = SCAffPartner::GetNbAffiliatesByDate($day_start, $day_end, $id_partner);
                /*$affilie = rand(30,100);
                $order = rand(1, 60);*/
                $orders_totals = SCAffCommission::GetTotalsAffiliatesOrdersByDates($day_start, $day_end, $id_partner);
                $nb_cmd = (int) $orders_totals['nb_orders'];

                $monthName = date('M', mktime(0, 0, 0, $m, 10));

                echo '{"affilie":"'.$affilie.'","order":"'.$nb_cmd.'","month":"'._l($monthName).'"}';
                ++$i;
            }
        }
    } ?>
];

var chartConfig = {
        view: "bar",
        value: "#affilie#",
        label: "#affilie#",
        color: "#58dccd",
        width: 30,
        padding: {
            left: 30
        },
        xAxis: {
            template: "#month#"
        },
        yAxis: {
            start: 0,
            template:function(value){
                return (value%20?"":value)
            }
        },
        legend: {
            values: [{
                text: "<?php echo _l('New affiliates'); ?>",
                color: "#58dccd"
            }, {
                text: "<?php echo _l('Generated sales'); ?>",
                color: "#a7ee70"
            }],
            valign: "middle",
            align: "right",
            layout: "y"
        }
    };
var chartAffilie = dhxlAffPartPerfChartAffilie.attachChart(chartConfig);
chartAffilie.addSeries({
    value: "#order#",
    label: "#order#",
    color: "#a7ee70",
    tooltip: {
        template: "#order#"
    }
});
chartAffilie.parse(Affilies, "json");

// CHART TAUX
var Taux=[
    <?php
    // debut
        $year_moins = 0;
    $start_month = date('m');
    if ($start_month != 12)
    {
        $start_month = $start_month + 1;
        $year_moins = 1;
    }
    else
    {
        $start_month = '01';
    }

    $start_year = date('Y');
    $start_year = $start_year - $year_moins;

    // fin
    $end_month = date('m');
    $end_year = date('Y');

    // génération
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
                if ($i > 0)
                {
                    echo ",\n";
                }

                $day_start = $y.'-'.$m.'-01';
                $day_end = $y.'-'.$m.'-'.SCI::days_in_month(CAL_GREGORIAN, $m, $y);

                $visiteur = SCAffClick::GetNbVisiteurByDate($day_start, $day_end, $id_partner);
                //$nb_cmd = rand(1, 60);
                $orders_totals = SCAffCommission::GetTotalsAffiliatesOrdersByDates($day_start, $day_end, $id_partner);
                $nb_cmd = (int) $orders_totals['nb_orders'];

                if (!empty($visiteur))
                {
                    $rate = number_format($nb_cmd / $visiteur * 100, 0, '.', '');
                }
                else
                {
                    $rate = 0;
                }

                $monthName = date('M', mktime(0, 0, 0, $m, 10));

                echo '{"rate":"'.$rate.'","month":"'._l($monthName).'"}';
                ++$i;
            }
        }
    } ?>
];

var chartConfig = {
        view: "line",
        value: "#rate#",
        item: {
            borderColor: "#58dccd",
            color: "#ffffff",
                label: "#rate#"
        },
        line: {
            color: "#58dccd",
            width: 3
        },
        tooltip: {
            template: "#rate#"
        },
        offset: 0,
        xAxis: {
            template: "#month#"
        },
        origin: 0,
        yAxis: {
            title: "<?php echo _l('Conversion rate', 1); ?> (%)",
            start: 0,
            template: function(obj) {
                return (obj % 20 ? "": obj);
            }
        },
    };

var chartTaux = dhxlAffPartPerfChartTaux.attachChart(chartConfig);
chartTaux.parse(Taux, "json");

</script>
<?php
} ?>
</body>
</html>