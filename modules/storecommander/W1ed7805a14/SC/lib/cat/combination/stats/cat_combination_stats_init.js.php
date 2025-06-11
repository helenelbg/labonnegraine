<?php
$subprop_name = 'combination_stats';
$subprop_title = _l('Stats', 1);
$icon = 'fa fa-chart-area';
?>

// INIT TOOLBAR
prop_tb.attachEvent("onClick", function setPropertiesPanel_combinations(id) {
    if (id == 'combinations') {
        prop_tb.combi_subproperties_tb.addListOption('combiSubProperties', '<?php echo $subprop_name; ?>', 9, "button", '<?php echo $subprop_title; ?>', "<?php echo $icon; ?>");


        prop_tb.combi_subproperties_tb.attachEvent("onClick", function (id) {
            if (id == "<?php echo $subprop_name; ?>") {
                hideSubpropertiesItems();
                prop_tb.combi_subproperties_tb.setItemText('combiSubProperties', '<?php echo $subprop_title; ?>');
                prop_tb.combi_subproperties_tb.setItemImage('combiSubProperties', '<?php echo $icon; ?>');
                actual_subproperties = "<?php echo $subprop_name; ?>";
                initCombinationStats();
            }
        });

        prop_tb._combinationsGrid.attachEvent("onRowSelect", function (id, ind) {
            if (!prop_tb._combinationsLayout.cells('b').isCollapsed()) {
                if (actual_subproperties == "<?php echo $subprop_name; ?>") {
                    getCombinationStats();
                }
            }
        });
    }
});

// INIT GRID
function initCombinationStats() {
    prop_tb.combi_subproperties_tb.addButton("stats_refresh", 100, "", "fa fa-sync green", "fa fa-sync green");
    prop_tb.combi_subproperties_tb.setItemToolTip('stats_refresh', '<?php echo _l('Refresh', 1); ?>');
    var options_stats_text_lang = {
        'combination_quantity': "<?php echo _l('Total amount of combinations sold'); ?>",
        'combination_total_price': "<?php echo _l('Total sales tax excl.'); ?>",
        'sales_margin': "<?php echo _l('Sales margin'); ?>"
    };
    var options_stats_view = [
        ['combination_quantity', 'obj', options_stats_text_lang['combination_quantity'], ''],
        ['combination_total_price', 'obj', options_stats_text_lang['combination_total_price'], ''],
        <?php if (version_compare(_PS_VERSION_, '1.6.1.1', '>=')) { ?>
        ['sales_margin', 'obj', options_stats_text_lang['sales_margin'], '']
        <?php } ?>
    ];
    var select_all_combi_auto = false;
    prop_tb.combi_subproperties_tb.addButtonSelect('options_view', 100, '<?php echo _l('Total amount of combinations sold'); ?>', options_stats_view, 'fa fa-chart-area', 'fa fa-chart-area', false, true);
    prop_tb.combi_subproperties_tb.addButtonTwoState('select_all_auto', 100, "", "fa fa-bolt yellow", "fa fa-bolt yellow");
    prop_tb.combi_subproperties_tb.setItemToolTip('select_all_auto', '<?php echo _l('Auto select all combinations', 1); ?>');

    prop_tb.combi_subproperties_tb.attachEvent("onClick", function (id) {
        switch (id) {
            case 'stats_refresh':
                getCombinationStats();
                break;
            case 'combination_quantity':
                prop_tb.combi_subproperties_tb.setItemText('options_view', options_stats_text_lang[id]);
                getCombinationStats(id);
                break;
            case 'combination_total_price':
                prop_tb.combi_subproperties_tb.setItemText('options_view', options_stats_text_lang[id]);
                getCombinationStats(id);
                break;
        <?php if (version_compare(_PS_VERSION_, '1.6.1.1', '>=')) { ?>
            case 'sales_margin':
                prop_tb.combi_subproperties_tb.setItemText('options_view', options_stats_text_lang[id]);
                getCombinationStats(id);
                break;
        <?php } ?>
        }

    });

    prop_tb.combi_subproperties_tb.attachEvent("onStateChange", function (id, state) {
        switch (id) {
            case 'select_all_auto':
                if (state) {
                    select_all_combi_auto = true;
                    prop_tb._combinationsGrid.selectAll();
                    getCombinationStats();
                } else {
                    select_all_combi_auto = false;
                }
                break;
        }
    });

    prop_tb._combinationsGrid.attachEvent("onXLE", function () {
        if (select_all_combi_auto === true) {
            prop_tb._combinationsGrid.selectAll();
            getCombinationStats();
        }
    });

    prop_tb.combi_subproperties_tb.showItem('stats_refresh');
    prop_tb.combi_subproperties_tb.showItem('options_view');
    prop_tb._combinationsLayout.cells('b').setWidth(680);
    prop_tb._combinationsStatLayout = prop_tb._combinationsLayout.cells('b').attachLayout('1C');
    prop_tb._combinationsStatLayout.cells('a').hideHeader();
    getCombinationStats();
}

// FUNCTIONS
function getCombinationStats(options_combi_stats_view_selected = null) {
    if (options_combi_stats_view_selected === null) {
        options_combi_stats_view_selected = 'combination_quantity';
    }
    prop_tb._combinationsStatLayout.cells('a').attachURL('index.php?ajax=1&act=cat_combination_stats_get&stat_view=' + options_combi_stats_view_selected + '&id_product_attribute=' + prop_tb._combinationsGrid.getSelectedRowId(), true);
}