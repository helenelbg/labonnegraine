<?php
    $id_lang = Tools::getValue('id_lang');
    $filters = Tools::getValue('filters', null);
    $fields = Tools::getValue('fields', null);

if ($filters != null)
{
    $sql_conditions = array();
    if (!empty($filters))
    {
        $filter_arr = explode(',', $filters);
        foreach ($filter_arr as $filter)
        {
            list($field, $id) = explode('#', $filter);
            $sql_conditions[$field][] = $id;
        }
    }

    ini_set('memory_limit', '1000000000000');

    ## Countries
    $countries = Country::getCountries($id_lang);
    $countries_name = array();
    foreach ($countries as $country)
    {
        $countries_name[$country['id_country']] = $country['name'];
    }

    ## Carriers
    $sql = 'SELECT id_carrier, name FROM '._DB_PREFIX_.'carrier';
    $carriers = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    $carriers_name = array();
    foreach ($carriers as $carrier)
    {
        $carriers_name[$carrier['id_carrier']] = $carrier['name'];
    }

    ## Groups customers
    $sql = 'SELECT id_group, name FROM '._DB_PREFIX_.'group_lang WHERE id_lang = '.(int) $id_lang;
    $customer_group = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    $groups_name = array();
    foreach ($customer_group as $group)
    {
        $groups_name[$group['id_group']] = $group['name'];
    }

    ## marge par commande
    if (version_compare(_PS_VERSION_, '1.5.0.2', '>='))
    {
        $sql = 'SELECT id_order,SUM(total_price_tax_excl - purchase_supplier_price) as order_margin
                FROM '._DB_PREFIX_.'order_detail
                GROUP BY id_order';
        $order_detail = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        $order_margin = array();
        foreach ($order_detail as $order)
        {
            $order_margin[$order['id_order']] = (float) $order['order_margin'];
        }
    }

    ## Récupération des commandes
    $sql = 'SELECT *, '.(version_compare(_PS_VERSION_, '1.5.0.4', '>=') ? '(SELECT osl.`name`
            FROM `'._DB_PREFIX_.'order_state_lang` osl
            WHERE osl.`id_order_state` = o.`current_state`
            AND osl.`id_lang` = '.(int) $id_lang.'
            LIMIT 1
        ) AS `state_name`,' : '').'o.`date_add` AS `date_add`, o.`date_upd` AS `date_upd`, car.id_carrier as carrier, l.name as language'.(SCMS ? ', sh.name as shop' : '').',
        addr.id_country  as country
        '.(version_compare(_PS_VERSION_, '1.5.0.2', '>=') ? ', o.id_order as id_order_for_margin' : '').'
        FROM `'._DB_PREFIX_.'orders` o
       '.(SCMS ? ' LEFT JOIN `'._DB_PREFIX_.'shop` sh ON (sh.`id_shop` = o.`id_shop`)' : '').'
        LEFT JOIN `'._DB_PREFIX_.'lang` l ON (l.`id_lang` = o.`id_lang`)
        LEFT JOIN `'._DB_PREFIX_.'customer` c ON (c.`id_customer` = o.`id_customer`)
        LEFT JOIN `'._DB_PREFIX_.'carrier` car ON (car.`id_carrier` = o.`id_carrier`)
        LEFT JOIN `'._DB_PREFIX_.'address` addr ON (addr.`id_address` = o.`id_address_delivery`)
        WHERE o.valid = 1
        '.(!empty($sql_conditions) ? generateSqlConditions($sql_conditions) : '').'
        ORDER BY o.`date_add` DESC';
    $orders = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    $orders_encoded = json_encode($orders);

    ## Traductions
    $month_full = array(_l('January'), _l('February'), _l('March'), _l('April'), _l('May'), _l('June'), _l('July'), _l('August'), _l('September'), _l('October'), _l('November'), _l('December'));
    $month_short = array(_l('Jan'), _l('Feb'), _l('Mar'), _l('Apr'), _l('May'), _l('Jun'), _l('Jul'), _l('Aug'), _l('Sep'), _l('Oct'), _l('Nov'), _l('Dec'));
    $day_full = array(_l('Sunday'), _l('Monday'), _l('Tuesday'), _l('Wednesday'), _l('Thursday'), _l('Friday'), _l('Saturday'));
    $day_short = array(_l('Sun.'), _l('Mon.'), _l('Tue.'), _l('Wed.'), _l('Thu.'), _l('Fri.'), _l('Sat.'));
    $quarter = array(_l('Q1'), _l('Q2'), _l('Q3'), _l('Q4'));

    ## Champs disponibles
    $field_list = array(
        array('id' => 'id_order', 'label' => _l('Order'), 'sortDir' => 'desc'),
        array('id' => 'date_add', 'label' => _l('Order creation date'), 'type' => 'date', 'format' => '%Y-%m-%d %H:%i:%s', 'group' => 'dateByYear', 'sortDir' => 'desc'),
        array('id' => 'payment', 'label' => _l('Payment method')),
        array('id' => 'language', 'label' => _l('Language')),
        array('id' => 'country', 'label' => _l('Country'), 'aliases' => $countries_name),
    );
    $field_list[] = array('id' => 'id_default_group', 'label' => _l('Customer group'), 'aliases' => $groups_name);

    if (version_compare(_PS_VERSION_, '1.5.0.2', '>='))
    {
        $field_list[] = array('id' => 'carrier', 'label' => _l('Carrier'), 'aliases' => $carriers_name);
        $field_list[] = array('id' => 'total_shipping_tax_excl', 'label' => _l('Total shipping Tax excl'));
        $field_list[] = array('id' => 'total_shipping_tax_incl', 'label' => _l('Total shipping Tax incl'));
        $field_list[] = array('id' => 'total_paid_tax_incl', 'label' => _l('Total Paid Tax incl.'));
        $field_list[] = array('id' => 'total_paid_tax_excl', 'label' => _l('Total Paid Tax excl.'));
        $field_list[] = array('id' => 'id_order_for_margin', 'label' => _l('Order margin'), 'aliases' => $order_margin);
    }
    else
    {
        $field_list[] = array('id' => 'total_paid', 'label' => _l('Total Paid Tax incl.'));
    }
    if (SCMS)
    {
        $field_list[] = array('id' => 'shop', 'label' => _l('Shop'));
    }

    $field_list_encoded = json_encode($field_list);
}
    /*
     * FUNCTIONS
     */
    function generateSqlConditions($conditions)
    {
        $return = '';
        foreach ($conditions as $field => $condition)
        {
            $ids = implode(',', $condition);
            switch ($field) {
                case 'date_start':
                    $return .= ' AND o.date_add >= DATE('.$ids.')';
                    break;
                case 'date_end':
                    $return .= ' AND o.date_add < DATE('.$ids.')';
                    break;
                case 'payment_method':
                    $return .= ' AND o.module IN ("'.str_replace(',', '","', pInSQL($ids)).'")';
                    break;
                case 'carrier':
                    $sql = 'SELECT id_carrier
                            FROM `'._DB_PREFIX_.'carrier`
                            WHERE `id_reference` IN ('.pInSQL($ids).')';
                    $return .= ' AND o.id_carrier IN ('.$sql.')';
                    break;
                case 'country':
                    $return .= ' AND addr.id_country IN ('.pInSQL($ids).')';
                    break;
                case 'customer_group':
                    $return .= ' AND c.id_default_group IN ('.pInSQL($ids).')';
                    break;
                default:
                    $return .= ' AND o.id_'.pSQL($field).' IN ('.pInSQL($ids).')';
                    break;
            }
        }

        return $return;
    }
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <script type="text/javascript" src="lib/js/pivot/pivot.min.js?<?php echo date('Ymd'); ?>"></script>
    <link href="lib/js/pivot/pivot.min.css?<?php echo date('Ymd'); ?>" rel="stylesheet">
    <style>
        html,body{
            height:100%;
            box-sizing: content-box;
        }
        p {
            font-family: Tahoma, Arial,sans-serif;
            margin-top: 40px;
            text-align: center;
        }
        .dhx_config_item,
        .dhx_pivot_layout{
            font-family:Tahoma;
        }
    </style>
</head>
<body>
<?php if ($filters == null) { ?>
    <script type="text/javascript">
        window.parent.dhxlTrendsShopContent.progressOff();
    </script>
    <p><?php echo _l("You must select a filter and click on the 'refresh' icon of the central panel"); ?></p>
<?php }
else
{ ?>
<script type="text/javascript">
    window.parent.dhxlTrendsShopContent.progressOff();

    <?php
        echo 'var month_full_arr = ["'.implode('","', $month_full).'"];'."\n";
        echo 'var month_short_arr = ["'.implode('","', $month_short).'"];'."\n";
        echo 'var day_full_arr = ["'.implode('","', $day_full).'"];'."\n";
        echo 'var day_short_arr = ["'.implode('","', $day_short).'"];'."\n";
        echo 'var quarter_arr = ["'.implode('","', $quarter).'"];'."\n";
        $config_field = json_decode($fields, true);
        if (!empty($config_field) && $config_field != 'undefined' && $config_field != 'null' && $config_field != null)
        {
            echo 'var dataFields = '.json_encode($config_field[0]).';';
        }
        else
        {
            ## moyens de paiements
            if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
            {
                echo "var dataFields = {
                        rows: ['country','payment'],
                        columns: [{id:'date_add',group:'dateByYear'}],
                        values: [{id:'id_order',method:'count'},{id:'total_paid_tax_excl',method:'sum'},{id:'total_paid_tax_incl',method:'sum'},]
                    };";
            }
            else
            {
                echo "var dataFields = {
                    rows: ['country','payment'],
                    columns: [{id:'date_add',group:'dateByYear'}],
                    values: [{id:'id_order',method:'count'},{id:'total_paid',method:'sum'},]
                };";
            }
        }

    ?>

    dhx.i18n.setLocale("pivot", {
        availableFields: '<?php echo _l('available fields'); ?>',
        values: '<?php echo _l('data'); ?>',
        columns: '<?php echo _l('columns'); ?>',
        rows: '<?php echo _l('rows'); ?>',
        moveFieldsHere: '<?php echo _l('Move fields here'); ?>',
        hideSettings: '<?php echo _l('Hide Settings'); ?>',
        showSettings: '<?php echo _l('Show Settings'); ?>',
        apply: '<?php echo _l('Apply'); ?>',
        day: '<?php echo _l('Day'); ?>',
        week: '<?php echo _l('Week'); ?>',
        month: '<?php echo _l('Month'); ?>',
        quarter: '<?php echo _l('Quarter'); ?>',
        year: '<?php echo _l('Year'); ?>',
        min: '<?php echo _l('Min'); ?>',
        max: '<?php echo _l('Max'); ?>',
        sum: '<?php echo _l('Sum'); ?>',
        count: '<?php echo _l('Values nb.'); ?>',
        equal: '<?php echo _l('Equal'); ?>',
        notEqual: '<?php echo _l('Not Equal'); ?>',
        contains: '<?php echo _l('Contains'); ?>',
        notContains: '<?php echo _l('Not Contains'); ?>',
        typeHere: '<?php echo _l('Type Here'); ?>',
        selectAll: '<?php echo _l('Select All'); ?>',
        unselectAll: '<?php echo _l('Unselect All'); ?>',
        cancel: '<?php echo _l('Cancel'); ?>',
        ok: '<?php echo _l('Ok'); ?>',
        date: {
            monthFull: month_full_arr,
            monthShort: month_short_arr,
            dayFull: day_full_arr,
            dayShort: day_short_arr,
            quarter: quarter_arr,
            week: '<?php echo _l('Week'); ?>'
        }
    });

    pivotDataSet = <?php echo $orders_encoded; ?>;
    for_fieldlist = <?php echo $field_list_encoded; ?>;

    var pivot = new dhx.Pivot(document.body, {
        data: pivotDataSet,
        fields: dataFields,
        fieldList: for_fieldlist,
        layout: {
            columnsWidth:"auto",
            rowsHeadersWidth: "auto",
            footer:true
        }
    });
</script>
<?php } ?>
</body>
</html>
