<?php

if (version_compare(_PS_VERSION_, '1.7.2.0', '>=') && _r('GRI_CAT_PROPERTIES_STOCK_MOVEMENT_HISTORY')) { ?>
prop_tb.addListOption('panel', 'stockmvthistory', 8, "button", '<?php echo _l('Stock movement history', 1); ?>', "fad fa-external-link green");
allowed_properties_panel[allowed_properties_panel.length] = "stockmvthistory";
<?php } ?>

prop_tb.addButton("stockmvthistory_refresh",1000, "", "fa fa-sync green", "fa fa-sync green");
prop_tb.setItemToolTip('stockmvthistory_refresh', '<?php echo _l('Refresh grid', 1); ?>');
prop_tb.addButton("stockmvthistory_view_order",1000, "", "fa fa-shopping-cart", "fa fa-shopping-cart");
prop_tb.setItemToolTip('stockmvthistory_view_order', '<?php echo _l('View selected orders in StoreCommander', 1); ?>');
prop_tb.addButton("stockmvthistory_exportcsv",1000, "", "fad fa-file-csv green", "fad fa-file-csv green");
prop_tb.setItemToolTip('stockmvthistory_exportcsv', '<?php echo _l('Export grid to clipboard in CSV format for MSExcel with tab delimiter.'); ?>');


var needinitStockMovementHistory = 1;
function initStockMovementHistory() {
    if (needinitStockMovementHistory) {
        prop_tb._StockMvtHistoryLayout = dhxLayout.cells('b').attachLayout('1C');
        prop_tb._StockMvtHistoryLayout.cells('a').hideHeader();
        dhxLayout.cells('b').showHeader();
        prop_tb._StockMvtHistory_grid = prop_tb._StockMvtHistoryLayout.cells('a').attachGrid();
        prop_tb._StockMvtHistory_grid.setImagePath('lib/js/imgs/');
        prop_tb._StockMvtHistory_grid.enableSmartRendering(true);
        prop_tb._StockMvtHistory_grid.enableMultiselect(true);

        // UISettings
        prop_tb._StockMvtHistory_grid._uisettings_prefix = 'cat_StockMvtHistory';
        prop_tb._StockMvtHistory_grid._uisettings_name = prop_tb._StockMvtHistory_grid._uisettings_prefix;
        initGridUISettings(prop_tb._StockMvtHistory_grid);

        needinitStockMovementHistory = 0;
    }
}

prop_tb.attachEvent("onClick", function (id) {
    switch (id) {
        case 'stockmvthistory':
            if (lastProductSelID !== undefined && lastProductSelID !== "") {
                if (lastProductSelID > 0) {
                    dhxLayout.cells('b').setText('<?php echo _l('Properties', 1).' '._l('of', 1); ?> ' + getSelectedItemValueOrID(cat_grid,lastProductSelID,'name'));
                }
            }
            hidePropTBButtons();
            prop_tb.showItem('stockmvthistory_refresh');
            prop_tb.showItem('stockmvthistory_view_order');
            prop_tb.showItem('stockmvthistory_exportcsv');
            prop_tb.setItemText('panel', '<?php echo _l('Stock movement history', 1); ?>');
            prop_tb.setItemImage('panel', 'fad fa-external-link green');
            needinitStockMovementHistory = 1;
            initStockMovementHistory();
            displayStockMovementHistory();
            propertiesPanel = 'stockmvthistory';
            break;
        case 'stockmvthistory_refresh':
            displayStockMovementHistory();
            break;
        case 'stockmvthistory_view_order':
            let movement_selection = prop_tb._StockMvtHistory_grid.getSelectedRowId();
            if (movement_selection) {
                let idxIdOrder = prop_tb._StockMvtHistory_grid.getColIndexById('id_order');
                let movement_list = movement_selection.split(',');
                let id_order_list = [];
                for (const id_movement of movement_list) {
                    let id_order = Number(prop_tb._StockMvtHistory_grid.cells(id_movement, idxIdOrder).getValue());
                    if (id_order > 0 && !id_order_list.includes(id_order)) {
                        id_order_list.push(id_order);
                    }
                }
                if (id_order_list.length > 0) {
                    for (const id_order of id_order_list) {
                        let url = "?page=ord_tree&open_ord=" + id_order;
                        window.open(url, '_blank');
                    }
                }
            }
            break;
        case 'stockmvthistory_exportcsv':
            displayQuickExportWindow(prop_tb._StockMvtHistory_grid, 1);
            break;
    }
});

function displayStockMovementHistory() {
    let movement_selection = cat_grid.getSelectedRowId();
    if (movement_selection !== null) {
        prop_tb._StockMvtHistory_grid.clearAll(true);
        $.post("index.php?ajax=1&act=cat_stockmvthistory_get", {
            'id_lang': SC_ID_LANG,
            'product_list': movement_selection
        }, function (data) {
            if (data !== '') {
                prop_tb._StockMvtHistory_grid.parse(data);

                // UISettings
                loadGridUISettings(prop_tb._StockMvtHistory_grid);
            }
        });
    }
}

let StockMvtHistory_current_id = 0;
cat_grid.attachEvent("onRowSelect", function (idproduct) {
    if (propertiesPanel === 'stockmvthistory' && (cat_grid.getSelectedRowId() !== null && StockMvtHistory_current_id !== idproduct)) {
        displayStockMovementHistory();
        StockMvtHistory_current_id = idproduct;
    }
});
