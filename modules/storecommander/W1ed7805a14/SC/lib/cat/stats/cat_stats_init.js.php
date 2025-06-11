<?php if (_r('GRI_CAT_PROPERTIES_GRID_STATS')) { ?>
prop_tb.addListOption('panel', 'stats', 2, "button", '<?php echo _l('Stats', 1); ?>', "fa fa-chart-area");
allowed_properties_panel[allowed_properties_panel.length] = "stats";
<?php } ?>

var options_stats_text_lang = {
    'product_quantity': "<?php echo _l('Total amount of products sold'); ?>",
    'product_total_price': "<?php echo _l('Total sales tax excl.'); ?>",
    'sales_margin': "<?php echo _l('Sales margin'); ?>"
};
var options_stats_view = [
    ['product_quantity', 'obj', options_stats_text_lang['product_quantity'], ''],
    ['product_total_price', 'obj', options_stats_text_lang['product_total_price'], ''],
    <?php if (version_compare(_PS_VERSION_, '1.6.1.1', '>=')) { ?>
    ['sales_margin', 'obj', options_stats_text_lang['sales_margin'], '']
    <?php } ?>
];
prop_tb.addButtonSelect('options_view', 1000, '<?php echo _l('Total amount of products sold'); ?>', options_stats_view, 'fad fa-analytics', 'fad fa-analytics', false, true);

prop_tb.addButton("stats_refresh",1000, "", "fa fa-sync green", "fa fa-sync green");
prop_tb.setItemToolTip('stats_refresh', '<?php echo _l('Refresh', 1); ?>');


var needInitStats = 1;
function initStats() {
    if (needInitStats) {
        prop_tb._statsLayout = dhxLayout.cells('b').attachLayout('1C');
        prop_tb._statsLayout.cells('a').hideHeader();
        dhxLayout.cells('b').showHeader();
        needInitStats = 0;
    }
}

var options_stats_view_selected = 'product_quantity';

function setPropertiesPanel_stats(id) {
    switch (id) {
        case 'stats':
            hidePropTBButtons();
            prop_tb.showItem('stats_refresh');
            prop_tb.showItem('options_view');
            prop_tb.setItemText('panel', '<?php echo _l('Stats', 1); ?>');
            prop_tb.setItemImage('panel', 'fa fa-chart-area');
            let URLOptions = '';
            if (lastProductSelID != 0) URLOptions = '&id_product=' + lastProductSelID + '&id_lang=' + SC_ID_LANG;
            needInitStats = 1;
            initStats();
            propertiesPanel = 'stats';
            dhxLayout.cells('b').setWidth(680);
            displayStats();
            break;
        case 'stats_refresh':
            displayStats();
            break;
        case 'product_quantity':
            options_stats_view_selected = id;
            prop_tb.setItemText('options_view', options_stats_text_lang[id]);
            displayStats();
            break;
        case 'product_total_price':
            options_stats_view_selected = id;
            prop_tb.setItemText('options_view', options_stats_text_lang[id]);
            displayStats();
            break;
        <?php if (version_compare(_PS_VERSION_, '1.6.1.1', '>=')) { ?>
        case 'sales_margin':
            options_stats_view_selected = id;
            prop_tb.setItemText('options_view', options_stats_text_lang[id]);
            displayStats();
            break;
        <?php } ?>
    }
}
prop_tb.attachEvent("onClick", setPropertiesPanel_stats);

let stats_current_id = 0;
cat_grid.attachEvent("onRowSelect", function (idproduct) {
    lastProductSelID = idproduct;
    if (propertiesPanel == 'stats' && (cat_grid.getSelectedRowId() !== null && stats_current_id != idproduct)) {
        dhxLayout.cells('b').setText('<?php echo _l('Properties', 1).' '._l('of', 1); ?> ' + getSelectedItemValueOrID(cat_grid,lastProductSelID,'name'));
        displayStats();
        stats_current_id = idproduct;
    }
});

function displayStats() {
    dhxLayout.cells('b').attachURL('index.php?ajax=1&act=cat_stats_get&stat_view=' + options_stats_view_selected + '&list_id_product=' + cat_grid.getSelectedRowId(), true);
}
