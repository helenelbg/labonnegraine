<?php $id_shop = 0;
if (SCMS)
{
    $shop = (int) Tools::getValue('id_shop', '0');
    if (!empty($shop) && is_numeric($shop))
    {
        $id_shop = $shop;
    }
}

?><!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>SC - Affiliation</title>
<style type="text/css">

html, body, div, span, applet, object, iframe,
h1, h2, h3, h4, h5, h6, p, blockquote, pre,
a, abbr, acronym, address, big, cite, code,
del, dfn, em, img, ins, kbd, q, s, samp,
small, strike, strong, sub, sup, tt, var,
b, u, i, center,
dl, dt, dd, ol, ul, li,
fieldset, form, label, legend,
table, caption, tbody, tfoot, thead, tr, th, td,
article, aside, canvas, details, embed, 
figure, figcaption, footer, header, hgroup, 
menu, nav, output, ruby, section, summary,
time, mark, audio, video {
    margin: 0;
    padding: 0;
    border: 0;
    font-size: 100%;
    font: inherit;
    vertical-align: baseline;
}
/* HTML5 display-role reset for older browsers */
article, aside, details, figcaption, figure, 
footer, header, hgroup, menu, nav, section {
    display: block;
}
body {
    line-height: 1;
    color: #000000;
    font-family: Tahoma;
}
ol, ul {
    list-style: none;
}
blockquote, q {
    quotes: none;
}
blockquote:before, blockquote:after,
q:before, q:after {
    content: '';
    content: none;
}
table {
    border-collapse: collapse;
    border-spacing: 0;
}

h2 {
    text-align: center;
    color: #000000;
    font-family: Tahoma;
    font-size: 16px;
    font-weight: bold;
    line-height: 29px;
    margin-top: 1em;
}
h3 {
    text-align: center;
    color: #000000;
    font-family: Tahoma;
    font-size: 14px;
    font-weight: bold;
    line-height: 29px;
    margin-top: 1em;
}

.col_left {
    width: 400px;
    float: left;
    overflow: hidden;
}
.col_left .col_left-in {
    padding-left: 50px;
    width: 350px;
}

.col_right {
    width: 400px;
    float: left;
    overflow: auto;
    padding: 0px 50px;
}

.input_bloc {
width: 350px;
margin-bottom: 1em;
height: 20px;
}
.input_bloc label {
    width: 130px;
    font-size: 11px;
    color: #000000;
    font-weight: bold;
    float: left;
    line-height: 20px;
}
.input_bloc .month {
    width: 120px;
    float: left;
    margin-left: 20px;
}
.input_bloc .year {
    width: 60px;
    float: left;
    margin-left: 20px;
}

.btn {
    background: none repeat scroll 0 0 #e2edf2;
    border: 1px solid #A4BED4;
    font-size: 11px;
    height: 27px;
    overflow: hidden;
    position: relative;
    font-weight: bold;
    cursor: pointer;
}
.btn.submit {float: right;}
.btn.reset {float: left;}

.table {
    width: 100;
    border: 1px solid #A4BED4;
    font-size: 11px;
}
.table td, .table th {
    border: 1px solid #A4BED4;
    padding: 10px;
}
.table td.aright {
    text-align: right;
}
.table th {
    font-weight: bold;
    background: #E2EDF2;
}
.table .edd td {
    background: #E2EDF2;
}
</style>
<link type="text/css" rel="stylesheet" href="<?php echo SC_CSSDHTMLX; ?>" />
<link href="<?php echo SC_CSSSTYLE; ?>" rel="stylesheet" type="text/css"/>
<link href="<?php echo SC_CSS_FONTAWESOME; ?>" rel="stylesheet" type="text/css"/>

<script src="<?php echo SC_JQUERY; ?>" type="text/javascript"></script>
<script type="text/javascript" src="<?php echo SC_JSDHTMLX; ?>"></script>
<script type="text/javascript" src="lib/js/message.js"></script>
<script type="text/javascript">
SC_ID_LANG=<?php echo $sc_agent->id_lang; ?>;

$(document).ready(function(){

    $(".btn.submit").click(function(){
        $.post("index.php?ajax=1&act=all_win-affiliation_dashboard_get&id_lang="+SC_ID_LANG<?php if (!empty($id_shop))
{
    echo '+"&id_shop='.$id_shop.'"';
} ?>, { "start_month": $("#start_month").val(),"start_year": $("#start_year").val(),"end_month": $("#end_month").val(),"end_year": $("#end_year").val()}, function(data){
                if(data.type=="success")
            {
                $(".table .deletable").remove();

                $(".table .not_deletable").after(data.message);
            }
            else if(data.type=="error")
            {
                dhtmlx.message({text:data.message,type:'error',expire: 10000});
            }
        }, "json");
    });

    $(".btn.reset").click(function(){
        $("#start_month").val($("#start_month").attr("default"));
        $("#start_year").val($("#start_year").attr("default"));
        $("#end_month").val($("#end_month").attr("default"));
        $("#end_year").val($("#end_year").attr("default"));
    });

    
    $("#export").click(function(){
        window.open("index.php?ajax=1&act=all_win-affiliation_dashboard_export&id_lang="+SC_ID_LANG+"&start_month="+$("#start_month").val()+"&start_year="+$("#start_year").val()+"&end_month="+$("#end_month").val()+"&end_year="+$("#end_year").val()<?php if (!empty($id_shop))
{
    echo '+"&id_shop='.$id_shop.'"';
} ?>);
    });
});
</script>
</head>
<body>

<div class="col_left">
    <h2><?php echo _l('Income'); ?></h2>
    <img src="lib/all/win-affiliation/dashboard/money.png" />
    <br/><br/><br/>
    
    <div class="col_left-in">
        
        <div class="input_bloc">
            <label><?php echo _l('Start Month'); ?></label>
            <?php $year_moins = 0;
            $now = date('m');
            if ($now != 12)
            {
                $now = $now + 1;
                $year_moins = 1;
            }
            else
            {
                $now = '01';
            }
            $start_period = $now.'/';
            ?>
            <select id="start_month" class="month" default="<?php echo $now * 1; ?>">
                <option value="1" <?php if ($now == '01')
            {
                echo 'selected';
            } ?>><?php echo _l('January'); ?> (01)</option>
                <option value="2" <?php if ($now == '02')
            {
                echo 'selected';
            } ?>><?php echo _l('February'); ?> (02)</option>
                <option value="3" <?php if ($now == '03')
            {
                echo 'selected';
            } ?>><?php echo _l('March'); ?> (03)</option>
                <option value="4" <?php if ($now == '04')
            {
                echo 'selected';
            } ?>><?php echo _l('April'); ?> (04)</option>
                <option value="5" <?php if ($now == '05')
            {
                echo 'selected';
            } ?>><?php echo _l('May'); ?> (05)</option>
                <option value="6" <?php if ($now == '06')
            {
                echo 'selected';
            } ?>><?php echo _l('June'); ?> (06)</option>
                <option value="7" <?php if ($now == '07')
            {
                echo 'selected';
            } ?>><?php echo _l('July'); ?> (07)</option>
                <option value="8" <?php if ($now == '08')
            {
                echo 'selected';
            } ?>><?php echo _l('August'); ?> (08)</option>
                <option value="9" <?php if ($now == '09')
            {
                echo 'selected';
            } ?>><?php echo _l('September'); ?> (09)</option>
                <option value="10" <?php if ($now == '10')
            {
                echo 'selected';
            } ?>><?php echo _l('October'); ?> (10)</option>
                <option value="11" <?php if ($now == '11')
            {
                echo 'selected';
            } ?>><?php echo _l('November'); ?> (11)</option>
                <option value="12" <?php if ($now == '12')
            {
                echo 'selected';
            } ?>><?php echo _l('December'); ?> (12)</option>
            </select>
            <?php $now = date('Y');
            $start = $now - 10;
            $now_moins = $now - $year_moins;
            $start_period .= $now_moins;
            ?>
            <select id="start_year" class="year" default="<?php echo $now_moins; ?>">
                <?php
                for ($i = $start; $i <= $now; ++$i) { ?>
                    <option value="<?php echo $i; ?>" <?php if ($now_moins == $i)
                {
                    echo 'selected';
                } ?>><?php echo $i; ?></option>
                <?php } ?>
            </select>
        </div>
        
        <div class="input_bloc">
            <label><?php echo _l('End Month'); ?></label>
            <?php $now = date('m');
            $end_period = $now.'/'; ?>
            <select id="end_month" class="month" default="<?php echo $now * 1; ?>">
                <option value="1" <?php if ($now == '01')
            {
                echo 'selected';
            } ?>><?php echo _l('January'); ?> (01)</option>
                <option value="2" <?php if ($now == '02')
            {
                echo 'selected';
            } ?>><?php echo _l('February'); ?> (02)</option>
                <option value="3" <?php if ($now == '03')
            {
                echo 'selected';
            } ?>><?php echo _l('March'); ?> (03)</option>
                <option value="4" <?php if ($now == '04')
            {
                echo 'selected';
            } ?>><?php echo _l('April'); ?> (04)</option>
                <option value="5" <?php if ($now == '05')
            {
                echo 'selected';
            } ?>><?php echo _l('May'); ?> (05)</option>
                <option value="6" <?php if ($now == '06')
            {
                echo 'selected';
            } ?>><?php echo _l('June'); ?> (06)</option>
                <option value="7" <?php if ($now == '07')
            {
                echo 'selected';
            } ?>><?php echo _l('July'); ?> (07)</option>
                <option value="8" <?php if ($now == '08')
            {
                echo 'selected';
            } ?>><?php echo _l('August'); ?> (08)</option>
                <option value="9" <?php if ($now == '09')
            {
                echo 'selected';
            } ?>><?php echo _l('September'); ?> (09)</option>
                <option value="10" <?php if ($now == '10')
            {
                echo 'selected';
            } ?>><?php echo _l('October'); ?> (10)</option>
                <option value="11" <?php if ($now == '11')
            {
                echo 'selected';
            } ?>><?php echo _l('November'); ?> (11)</option>
                <option value="12" <?php if ($now == '12')
            {
                echo 'selected';
            } ?>><?php echo _l('December'); ?> (12)</option>
            </select>
            <?php $now = date('Y');
            $start = $now - 10;
            $end_period .= $now;
            ?>
            <select id="end_year" class="year" default="<?php echo $now; ?>">
                <?php
                for ($i = $start; $i <= $now; ++$i) { ?>
                    <option value="<?php echo $i; ?>" <?php if ($now == $i)
                {
                    echo 'selected';
                } ?>><?php echo $i; ?></option>
                <?php } ?>
            </select>
        </div>
        
        <button class="btn submit"><?php echo _l('Submit'); ?></button>
        
        <button class="btn reset"><?php echo _l('Reset'); ?></button>
    
    </div>
</div>

<div class="col_right">

    <h3><?php echo _l('Current month'); ?></h3>
    <table class="table" width="100%">
        <tr>
            <th><?php echo _l('Total sales'); ?></th>
            <th><?php echo _l('Clicks'); ?></th>
            <th><?php echo _l('Visitors'); ?></th>
            <th><?php echo _l('Conversions'); ?></th>
            <th><?php echo _l('Rate'); ?></th>
        </tr>
        <?php $day_start = date('Y-m-01');
        $day_end = date('Y-m-t');

        /*$current_amount = rand(100, 10000);
        $nb_cmd = rand(1, 20);*/
        $orders_totals = SCAffCommission::GetTotalsAffiliatesOrdersByDates($day_start, $day_end, null, $id_shop);
        $current_amount = $orders_totals['sum_total_products'];
        $nb_cmd = (int) $orders_totals['nb_orders'];
        $click = SCAffClick::GetNbClickByDate($day_start, $day_end, null, $id_shop);
        $visiteur = SCAffClick::GetNbVisiteurByDate($day_start, $day_end, null, $id_shop);
        ?>
        <tr>
            <td class="aright"><?php echo number_format((float) $current_amount, 2); ?></td>
            <td class="aright"><?php echo $click; ?></td>
            <td class="aright"><?php echo $visiteur; ?></td>
            <td class="aright"><?php echo $nb_cmd; ?></td>
            <td class="aright"><?php if (!empty($visiteur))
        {
            echo number_format($nb_cmd / $visiteur * 100);
        }
        else
        {
            echo '0';
        } ?>%</td>
        </tr>        
    </table>

    <br/><br/>
    <h3><?php echo _l('Period'); ?></h3>
    <span id="export" title="<?php echo _l('Export'); ?>" style="float: right;cursor: pointer;margin-top: -35px;font-size: 29px;color: green;"><i class="fad fa-file-csv green"></i></span>
    <table class="table" width="100%">
        <tr class="not_deletable">
            <th><?php echo _l('Month'); ?></th>
            <th><?php echo _l('Total sales'); ?></th>
            <th><?php echo _l('Clicks'); ?></th>
            <th><?php echo _l('Visitors'); ?></th>
            <th><?php echo _l('Conversions'); ?></th>
            <th><?php echo _l('Rate'); ?></th>
        </tr>
        <?php list($start_month, $start_year) = explode('/', $start_period);
        list($end_month, $end_year) = explode('/', $end_period);

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
                    $visiteur = SCAffClick::GetNbVisiteurByDate($day_start, $day_end, null, $id_shop); ?>
                    <tr class="deletable <?php if ($i > 0 && ($i % 2))
                    {
                        echo 'edd';
                    } ?>">
                        <td><?php echo str_pad($m, 2, '0', STR_PAD_LEFT).'/'.$y; ?></td>
                        <td class="aright"><?php echo number_format((float) $current_amount, 2); ?></td>
                        <td class="aright"><?php echo $click; ?></td>
                        <td class="aright"><?php echo $visiteur; ?></td>
                        <td class="aright"><?php echo $nb_cmd; ?></td>
                        <td class="aright"><?php if (!empty($visiteur))
                    {
                        echo number_format($nb_cmd / $visiteur * 100);
                    }
                    else
                    {
                        echo '0';
                    } ?>%</td>
                    </tr>    
        <?php ++$i;
                }
            }
        } ?>    
    </table>
    
</div>

</body>
</html>