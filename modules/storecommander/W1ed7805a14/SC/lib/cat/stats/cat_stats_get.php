<?php
$stat_view = Tools::getValue('stat_view', null);
$list_id_product = Tools::getValue('list_id_product', null);
$chart_data = array();
$dhtmlxChart_yAxis_title = '';
$dhtmlxChart_init = array(
    'view' => 'bar',
    'container' => '',
    'value' => '#sales#',
    'tooltip' => array(
        'template' => '#sales#',
        'dx' => -50,
        'dy' => 20,
    ),
    'color' => '#65dd65',
    'width' => 30,
    'radius' => 6,
    'border' => false,
    'seriesPadding' => 5,
    'xAxis' => array(
        'template' => '',
        'title' => '',
    ),
    'yAxis' => array(
        'title' => '',
    ),
);
$dhtmlxChart_types = array(
    'chart_one' => array(
        'container' => 'div_chart_one',
        'color' => '#98a1e6',
        'xAxis' => array(
            'template' => '#days#',
            'title' => _l('Last 30 days'),
        ),
    ),
    'chart_two' => array(
        'container' => 'div_chart_two',
        'color' => '#dd6363',
        'xAxis' => array(
            'template' => '#month#',
            'title' => _l('Last 24 months'),
        ),
    ),
    'chart_three' => array(
        'container' => 'div_chart_three',
        'color' => '#65dd65',
        'xAxis' => array(
            'template' => '#year#',
            'title' => _l('Last 10 years'),
        ),
    ),
);

if (!empty($stat_view) && !empty($list_id_product))
{
    switch ($stat_view) {
        case 'product_quantity':
            ## Y title
            $dhtmlxChart_yAxis_title = _l('Sales');
            ## 30 jours
            for ($i = 30; $i >= 0; --$i)
            {
                $sql = 'SELECT sum(od.product_quantity) AS qty, sum(od.`product_quantity_refunded`) AS refunded, DAY(o.date_add) as day 
                            FROM '._DB_PREFIX_.'orders o
                            LEFT JOIN '._DB_PREFIX_.'order_detail od ON (o.id_order = od.id_order)
                            WHERE o.valid = 1
                            AND od.product_id IN ('.pInSQL($list_id_product).')
                            AND DATE(o.date_add) = DATE_ADD(CURDATE(), INTERVAL -'.(int) $i.' DAY)'.
                            (version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? ' AND od.id_shop IN ('.pInSQL(SCI::getSelectedShopActionList(true)).')' : '');
                $result = Db::getInstance()->executeS($sql);
                $chart_data['chart_one'][] = array(
                    'sales' => (float) ($result[0]['qty'] - $result[0]['refunded']),
                    'days' => (string) $result[0]['day'],
                );
            }

            ## 24 mois
            $startYear = date('Y') - 2;
            $currentMonth = date('m');
            for ($i = $currentMonth; $i < 13; ++$i)
            {
                $sql = 'SELECT sum(od.product_quantity) AS qty, sum(od.`product_quantity_refunded`) AS refunded 
                            FROM '._DB_PREFIX_.'orders o
                            LEFT JOIN '._DB_PREFIX_.'order_detail od ON (o.id_order = od.id_order)
                            WHERE o.valid = 1
                            AND od.product_id IN ('.pInSQL($list_id_product).')
                            AND YEAR(o.date_add) = '.(int) $startYear.'
                            AND MONTH(o.date_add) = '.(int) $i.
                            (version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? ' AND od.id_shop IN ('.pInSQL(SCI::getSelectedShopActionList(true)).')' : '');
                $result = Db::getInstance()->getRow($sql);
                $chart_data['chart_two'][] = array(
                    'sales' => (float) ($result['qty'] - $result['refunded']),
                    'month' => (int) str_pad($i, 2, '0', STR_PAD_LEFT),
                );
            }
            for ($i = 1; $i < 13; ++$i)
            {
                $sql = 'SELECT sum(od.product_quantity) AS qty, sum(od.`product_quantity_refunded`) AS refunded 
                            FROM '._DB_PREFIX_.'orders o
                            LEFT JOIN '._DB_PREFIX_.'order_detail od ON (o.id_order = od.id_order)
                            WHERE o.valid = 1
                            AND od.product_id IN ('.pInSQL($list_id_product).')
                            AND YEAR(o.date_add) = '.(int) ($startYear + 1).'
                            AND MONTH(o.date_add) = '.(int) $i.
                            (version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? ' AND od.id_shop IN ('.pInSQL(SCI::getSelectedShopActionList(true)).')' : '');
                $result = Db::getInstance()->getRow($sql);
                $chart_data['chart_two'][] = array(
                    'sales' => (float) ($result['qty'] - $result['refunded']),
                    'month' => (int) str_pad($i, 2, '0', STR_PAD_LEFT),
                );
            }
            for ($i = 1; $i < ($currentMonth + 1); ++$i)
            {
                $sql = 'SELECT sum(od.product_quantity) AS qty, sum(od.`product_quantity_refunded`) AS refunded 
                            FROM '._DB_PREFIX_.'orders o
                            LEFT JOIN '._DB_PREFIX_.'order_detail od ON (o.id_order = od.id_order)
                            WHERE o.valid = 1
                            AND od.product_id IN ('.pInSQL($list_id_product).')
                            AND YEAR(o.date_add) = '.(int) ($startYear + 2).'
                            AND MONTH(o.date_add) = '.(int) $i.
                            (version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? ' AND od.id_shop IN ('.pInSQL(SCI::getSelectedShopActionList(true)).')' : '');
                $result = Db::getInstance()->getRow($sql);
                $chart_data['chart_two'][] = array(
                    'sales' => (float) ($result['qty'] - $result['refunded']),
                    'month' => (int) str_pad($i, 2, '0', STR_PAD_LEFT),
                );
            }

            ## 10 ans
            $startYear = date('Y') - 10;
            $currentMonth = date('m');
            for ($i = $startYear; $i <= $startYear + 10; ++$i)
            {
                $sql = 'SELECT sum(od.product_quantity) AS qty, sum(od.`product_quantity_refunded`) AS refunded 
                            FROM '._DB_PREFIX_.'orders o
                            LEFT JOIN '._DB_PREFIX_.'order_detail od ON (o.id_order = od.id_order)
                            WHERE o.valid = 1
                            AND od.product_id IN ('.pInSQL($list_id_product).')
                            AND YEAR(o.date_add) = '.(int) $i.
                            (version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? ' AND od.id_shop IN ('.pInSQL(SCI::getSelectedShopActionList(true)).')' : '');
                $result = Db::getInstance()->getRow($sql);
                $chart_data['chart_three'][] = array(
                    'sales' => (float) ($result['qty'] - $result['refunded']),
                    'year' => $i,
                );
            }
            break;
        case 'product_total_price':
            ## Y title
            $dhtmlxChart_yAxis_title = sprintf('%s (%s)', _l('Sales'), _l('Total products excl. tax'));
            ## 30 jours
            for ($i = 30; $i >= 0; --$i)
            {
                $sql = 'SELECT sum('.(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? 'od.`total_price_tax_excl`' : 'o.total_products').(version_compare(_PS_VERSION_, '1.7.7.0', '>=') ? '-od.`total_refunded_tax_excl`' : '').') AS total, DAY(o.date_add) as day 
                            FROM '._DB_PREFIX_.'orders o
                            LEFT JOIN '._DB_PREFIX_.'order_detail od ON (o.id_order = od.id_order)
                            WHERE o.valid = 1
                            AND od.product_id IN ('.pInSQL($list_id_product).')
                            AND DATE(o.date_add) = DATE_ADD(CURDATE(), INTERVAL -'.(int) $i.' DAY)'.
                            (version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? ' AND od.id_shop IN ('.pInSQL(SCI::getSelectedShopActionList(true)).')' : '');
                $result = Db::getInstance()->executeS($sql);
                $chart_data['chart_one'][] = array(
                    'sales' => (float) $result[0]['total'],
                    'days' => (string) $result[0]['day'],
                );
            }

            ## 24 mois
            $startYear = date('Y') - 2;
            $currentMonth = date('m');
            for ($i = $currentMonth; $i < 13; ++$i)
            {
                $sql = 'SELECT sum('.(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? 'od.`total_price_tax_excl`' : 'o.total_products').(version_compare(_PS_VERSION_, '1.7.7.0', '>=') ? '-od.`total_refunded_tax_excl`' : '').') AS total 
                            FROM '._DB_PREFIX_.'orders o
                            LEFT JOIN '._DB_PREFIX_.'order_detail od ON (o.id_order = od.id_order)
                            WHERE o.valid = 1
                            AND od.product_id IN ('.pInSQL($list_id_product).')
                            AND YEAR(o.date_add) = '.(int) $startYear.'
                            AND MONTH(o.date_add) = '.(int) $i.
                            (version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? ' AND od.id_shop IN ('.pInSQL(SCI::getSelectedShopActionList(true)).')' : '');
                $result = Db::getInstance()->getValue($sql);
                $chart_data['chart_two'][] = array(
                    'sales' => (float) $result,
                    'month' => (int) str_pad($i, 2, '0', STR_PAD_LEFT),
                );
            }
            for ($i = 1; $i < 13; ++$i)
            {
                $sql = 'SELECT sum('.(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? 'od.`total_price_tax_excl`' : 'o.total_products').(version_compare(_PS_VERSION_, '1.7.7.0', '>=') ? '-od.`total_refunded_tax_excl`' : '').') AS total 
                            FROM '._DB_PREFIX_.'orders o
                            LEFT JOIN '._DB_PREFIX_.'order_detail od ON (o.id_order = od.id_order)
                            WHERE o.valid = 1
                            AND od.product_id IN ('.pInSQL($list_id_product).')
                            AND YEAR(o.date_add) = '.(int) ($startYear + 1).'
                            AND MONTH(o.date_add) = '.(int) $i.
                            (version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? ' AND od.id_shop IN ('.pInSQL(SCI::getSelectedShopActionList(true)).')' : '');
                $result = Db::getInstance()->getValue($sql);
                $chart_data['chart_two'][] = array(
                    'sales' => (float) $result,
                    'month' => (int) str_pad($i, 2, '0', STR_PAD_LEFT),
                );
            }
            for ($i = 1; $i < ($currentMonth + 1); ++$i)
            {
                $sql = 'SELECT sum('.(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? 'od.`total_price_tax_excl`' : 'o.total_products').(version_compare(_PS_VERSION_, '1.7.7.0', '>=') ? '-od.`total_refunded_tax_excl`' : '').') AS total 
                            FROM '._DB_PREFIX_.'orders o
                            LEFT JOIN '._DB_PREFIX_.'order_detail od ON (o.id_order = od.id_order)
                            WHERE o.valid = 1
                            AND od.product_id IN ('.pInSQL($list_id_product).')
                            AND YEAR(o.date_add) = '.(int) ($startYear + 2).'
                            AND MONTH(o.date_add) = '.(int) $i.
                            (version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? ' AND od.id_shop IN ('.pInSQL(SCI::getSelectedShopActionList(true)).')' : '');
                $result = Db::getInstance()->getValue($sql);
                $chart_data['chart_two'][] = array(
                    'sales' => (float) $result,
                    'month' => (int) str_pad($i, 2, '0', STR_PAD_LEFT),
                );
            }

            ## 10 ans
            $startYear = date('Y') - 10;
            $currentMonth = date('m');
            for ($i = $startYear; $i <= $startYear + 10; ++$i)
            {
                $sql = 'SELECT sum('.(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? 'od.`total_price_tax_excl`' : 'o.total_products').(version_compare(_PS_VERSION_, '1.7.7.0', '>=') ? '-od.`total_refunded_tax_excl`' : '').') AS total 
                            FROM '._DB_PREFIX_.'orders o
                            LEFT JOIN '._DB_PREFIX_.'order_detail od ON (o.id_order = od.id_order)
                            WHERE o.valid = 1
                            AND od.product_id IN ('.pInSQL($list_id_product).')
                            AND YEAR(o.date_add) = '.(int) $i.
                            (version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? ' AND od.id_shop IN ('.pInSQL(SCI::getSelectedShopActionList(true)).')' : '');
                $result = Db::getInstance()->getValue($sql);
                $chart_data['chart_three'][] = array(
                    'sales' => (float) $result,
                    'year' => $i,
                );
            }
            break;
        case 'sales_margin':
            ## Y title
            $dhtmlxChart_yAxis_title = _l('Sales margin excl. tax');
            ## 30 jours
            for ($i = 30; $i >= 0; --$i)
            {
                $sql = 'SELECT SUM((od.`total_price_tax_excl`'.(version_compare(_PS_VERSION_, '1.7.7.0', '>=') ? '-od.`total_refunded_tax_excl`' : '').')-(od.`original_wholesale_price`*(od.`product_quantity`-od.`product_quantity_refunded`))) AS total,DAY(o.date_add) as day
                        FROM '._DB_PREFIX_.'orders o
                        LEFT JOIN '._DB_PREFIX_.'order_detail od ON (o.id_order = od.id_order)
                        WHERE o.valid = 1
                        AND od.product_id IN ('.pInSQL($list_id_product).')
                        AND DATE(o.date_add) = DATE_ADD(CURDATE(), INTERVAL -'.(int) $i.' DAY)
                        AND od.id_shop IN ('.pInSQL(SCI::getSelectedShopActionList(true)).')';
                $result = Db::getInstance()->executeS($sql);
                $chart_data['chart_one'][] = array(
                    'sales' => (float) $result[0]['total'],
                    'days' => (string) $result[0]['day'],
                );
            }

            ## 24 mois
            $startYear = date('Y') - 2;
            $currentMonth = date('m');
            for ($i = $currentMonth; $i < 13; ++$i)
            {
                $sql = 'SELECT SUM((od.`total_price_tax_excl`'.(version_compare(_PS_VERSION_, '1.7.7.0', '>=') ? '-od.`total_refunded_tax_excl`' : '').')-(od.`original_wholesale_price`*(od.`product_quantity`-od.`product_quantity_refunded`))) AS total
                            FROM '._DB_PREFIX_.'orders o
                            LEFT JOIN '._DB_PREFIX_.'order_detail od ON (o.id_order = od.id_order)
                            WHERE o.valid = 1
                            AND od.product_id IN ('.pInSQL($list_id_product).')
                            AND YEAR(o.date_add) = '.(int) $startYear.'
                            AND MONTH(o.date_add) = '.(int) $i.'
                            AND od.id_shop IN ('.pInSQL(SCI::getSelectedShopActionList(true)).')';
                $result = Db::getInstance()->getValue($sql);
                $chart_data['chart_two'][] = array(
                    'sales' => (float) $result,
                    'month' => (int) str_pad($i, 2, '0', STR_PAD_LEFT),
                );
            }
            for ($i = 1; $i < 13; ++$i)
            {
                $sql = 'SELECT SUM((od.`total_price_tax_excl`'.(version_compare(_PS_VERSION_, '1.7.7.0', '>=') ? '-od.`total_refunded_tax_excl`' : '').')-(od.`original_wholesale_price`*(od.`product_quantity`-od.`product_quantity_refunded`))) AS total
                            FROM '._DB_PREFIX_.'orders o
                            LEFT JOIN '._DB_PREFIX_.'order_detail od ON (o.id_order = od.id_order)
                            WHERE o.valid = 1
                            AND od.product_id IN ('.pInSQL($list_id_product).')
                            AND YEAR(o.date_add) = '.(int) ($startYear + 1).'
                            AND MONTH(o.date_add) = '.(int) $i.'
                            AND od.id_shop IN ('.pInSQL(SCI::getSelectedShopActionList(true)).')';
                $result = Db::getInstance()->getValue($sql);
                $chart_data['chart_two'][] = array(
                    'sales' => (float) $result,
                    'month' => (int) str_pad($i, 2, '0', STR_PAD_LEFT),
                );
            }
            for ($i = 1; $i < ($currentMonth + 1); ++$i)
            {
                $sql = 'SELECT SUM((od.`total_price_tax_excl`'.(version_compare(_PS_VERSION_, '1.7.7.0', '>=') ? '-od.`total_refunded_tax_excl`' : '').')-(od.`original_wholesale_price`*(od.`product_quantity`-od.`product_quantity_refunded`))) AS total
                            FROM '._DB_PREFIX_.'orders o
                            LEFT JOIN '._DB_PREFIX_.'order_detail od ON (o.id_order = od.id_order)
                            WHERE o.valid = 1
                            AND od.product_id IN ('.pInSQL($list_id_product).')
                            AND YEAR(o.date_add) = '.(int) ($startYear + 2).'
                            AND MONTH(o.date_add) = '.(int) $i.'
                            AND od.id_shop IN ('.pInSQL(SCI::getSelectedShopActionList(true)).')';
                $result = Db::getInstance()->getValue($sql);
                $chart_data['chart_two'][] = array(
                    'sales' => (float) $result,
                    'month' => (int) str_pad($i, 2, '0', STR_PAD_LEFT),
                );
            }

            ## 10 ans
            $startYear = date('Y') - 10;
            $currentMonth = date('m');
            for ($i = $startYear; $i <= $startYear + 10; ++$i)
            {
                $sql = 'SELECT SUM((od.`total_price_tax_excl`'.(version_compare(_PS_VERSION_, '1.7.7.0', '>=') ? '-od.`total_refunded_tax_excl`' : '').')-(od.`original_wholesale_price`*(od.`product_quantity`-od.`product_quantity_refunded`))) AS total
                            FROM '._DB_PREFIX_.'orders o
                            LEFT JOIN '._DB_PREFIX_.'order_detail od ON (o.id_order = od.id_order)
                            WHERE o.valid = 1
                            AND od.product_id IN ('.pInSQL($list_id_product).')
                            AND YEAR(o.date_add) = '.(int) $i.'
                            AND od.id_shop IN ('.pInSQL(SCI::getSelectedShopActionList(true)).')';
                $result = Db::getInstance()->getValue($sql);
                $chart_data['chart_three'][] = array(
                    'sales' => (float) $result,
                    'year' => $i,
                );
            }
            break;
    }
}
?>
<style>
    #all_charts {
        display: flex;
        flex-direction: column;
        -ms-flex-direction: column;
        -webkit-flex-direction: column;
        min-height: 100%;
        justify-content: space-between;
        -webkit-justify-content: space-between;
    }

    #all_charts .chart {
        height: 250px;
        display: block;
    }
</style>
<div id="all_charts">
    <?php foreach ($dhtmlxChart_types as $row)
{
    ## setting div for each Chart
    echo '<div id="'.$row['container'].'" class="chart"></div>'."\r\n\t";
} ?>
</div>
<script>
    <?php foreach ($dhtmlxChart_types as $var_name => $content)
{
    ## setting param for each Chart
    $dhtmlxChart_init['container'] = $content['container'];
    $dhtmlxChart_init['color'] = $content['color'];
    $dhtmlxChart_init['xAxis']['template'] = $content['xAxis']['template'];
    $dhtmlxChart_init['xAxis']['title'] = $content['xAxis']['title'];
    $dhtmlxChart_init['yAxis']['title'] = $dhtmlxChart_yAxis_title;
    ## setting Chart
    echo 'let '.$var_name.' = new dhtmlXChart('.json_encode($dhtmlxChart_init).');'."\r\n\t";
    ## parsing data
    echo $var_name.'.parse('.(array_key_exists($var_name, $chart_data) ? json_encode($chart_data[$var_name]) : array()).',"json");'."\n\n\t";
} ?>
</script>


