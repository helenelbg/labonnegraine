<?php
$subprop_name = 'combinationmultiproduct_stats';
$subprop_title = _l('Stats', 1);
$icon = 'fa fa-chart-area';
?>

// INIT TOOLBAR
prop_tb.attachEvent("onClick", function setPropertiesPanel_combinationmultiproduct(id) {
    if (id == 'combinationmultiproduct') {
        prop_tb.combimulprd_subproperties_tb.addListOption('combimulprdSubProperties', '<?php echo $subprop_name; ?>', 9, "button", '<?php echo $subprop_title; ?>', "<?php echo $icon; ?>");

        prop_tb.combimulprd_subproperties_tb.attachEvent("onClick", function (id) {
            if (id == "<?php echo $subprop_name; ?>") {
                hideCombinationMultiProduct_SubpropertiesItems();
                prop_tb.combimulprd_subproperties_tb.setItemText('combimulprdSubProperties', '<?php echo $subprop_title; ?>');
                prop_tb.combimulprd_subproperties_tb.setItemImage('combimulprdSubProperties', '<?php echo $icon; ?>');
                actual_subproperties = "<?php echo $subprop_name; ?>";
                initCombinationMultiProductStats();
            }
        });

        prop_tb._combinationmultiproductGrid.attachEvent("onRowSelect", function (id, ind) {
            if (!prop_tb._combinationmultiproductLayout.cells('b').isCollapsed()) {
                if (actual_subproperties == "<?php echo $subprop_name; ?>") {
                    getCombinationMultiProductStats();
                }
            }
        });

        prop_tb.combimulprd_subproperties_tb.addButton("stats_refresh", 100, "", "fa fa-sync green", "fa fa-sync green");
        prop_tb.combimulprd_subproperties_tb.setItemToolTip('stats_refresh', '<?php echo _l('Refresh', 1); ?>');
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
        var select_all_combimulti_auto = false;
        prop_tb.combimulprd_subproperties_tb.addButtonSelect('options_view', 100, '<?php echo _l('Total amount of combinations sold'); ?>', options_stats_view, 'fad fa-flag blue', 'fad fa-flag blue', false, true);
        prop_tb.combimulprd_subproperties_tb.addButtonTwoState('select_all_auto', 100, "", "fa fa-bolt yellow", "fa fa-bolt yellow");
        prop_tb.combimulprd_subproperties_tb.setItemToolTip('select_all_auto', '<?php echo _l('Auto select all combinations', 1); ?>');

        prop_tb.combimulprd_subproperties_tb.attachEvent("onClick", function (id) {
            switch (id) {
                case 'stats_refresh':
                    getCombinationMultiProductStats(id);
                    break;
                case 'combination_quantity':
                    prop_tb.combimulprd_subproperties_tb.setItemText('options_view', options_stats_text_lang[id]);
                    getCombinationMultiProductStats(id);
                    break;
                case 'combination_total_price':
                    prop_tb.combimulprd_subproperties_tb.setItemText('options_view', options_stats_text_lang[id]);
                    getCombinationMultiProductStats(id);
                    break;
            <?php if (version_compare(_PS_VERSION_, '1.6.1.1', '>=')) { ?>
                case 'sales_margin':
                    prop_tb.combimulprd_subproperties_tb.setItemText('options_view', options_stats_text_lang[id]);
                    getCombinationMultiProductStats(id);
                    break;
            <?php } ?>
            }
        });

        prop_tb.combimulprd_subproperties_tb.attachEvent("onStateChange", function (id, state) {
            switch (id) {
                case 'select_all_auto':
                    if (state) {
                        select_all_combimulti_auto = true;
                        prop_tb._combinationmultiproductGrid.selectAll();
                        getCombinationMultiProductStats();
                    } else {
                        select_all_combimulti_auto = false;
                    }
                    break;
            }
        });

        prop_tb._combinationmultiproductGrid.attachEvent("onXLE", function () {
            if (select_all_combimulti_auto === true) {
                prop_tb._combinationmultiproductGrid.selectAll();
                getCombinationMultiProductStats();
            }
        });
    }
});

// INIT GRID
function initCombinationMultiProductStats() {
    hideCombinationMultiProduct_SubpropertiesItems();
    prop_tb.combimulprd_subproperties_tb.showItem('stats_refresh');
    prop_tb.combimulprd_subproperties_tb.showItem('options_view');
    prop_tb.combimulprd_subproperties_tb.showItem('select_all_auto');
    prop_tb._combinationmultiproductLayout.cells('b').setWidth(680);
    prop_tb._combinationsMultiProductStatLayout = prop_tb._combinationmultiproductLayout.cells('b').attachLayout('1C');
    prop_tb._combinationsMultiProductStatLayout.cells('a').hideHeader();
    getCombinationMultiProductStats();
}

// FUNCTIONS
function getCombinationMultiProductStats(options_multi_combi_stats_view_selected=null) {
    if (options_multi_combi_stats_view_selected === null) {
        options_multi_combi_stats_view_selected = 'combination_quantity';
    }
    prop_tb._combinationsMultiProductStatLayout.cells('a').attachURL('index.php?ajax=1&act=cat_combinationmultiproduct_stats_get&stat_view=' + options_multi_combi_stats_view_selected + '&id_product_attribute=' + prop_tb._combinationmultiproductGrid.getSelectedRowId(), true);
}