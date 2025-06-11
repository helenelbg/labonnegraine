<?php
if (!defined('STORE_COMMANDER')) { exit; }
?>
/**
 * Init
 */
var exportOrderDashboardTab = exportOrdersTabbar.tabs('dashboard');
var exportOrderDashboardTab_layout = exportOrderDashboardTab.attachLayout("3W");

exportOrderDashboardTab_layout._displayAllColumnGrid = function () {
    exportOrderDashboardExportList_grid._displayGrid();
    exportOrderDashboardExport_result._grid._displayGrid();
}

/**
 * Export list
 */
var exportOrderDashboardExportList = exportOrderDashboardTab_layout.cells('a');
exportOrderDashboardExportList.setText('<?php echo _l('Export list', true); ?>');
exportOrderDashboardExportList.setWidth((dhxExportOrders.cells('a').getWidth()/2)-80);

var exportOrderDashboardExportList_toolbar = exportOrderDashboardExportList.attachToolbar();
exportOrderDashboardExportList_toolbar.setIconset('awesome');
exportOrderDashboardExportList_toolbar.addButton("refresh", 1000, "", "fa fa-sync green", "fa fa-sync green");
exportOrderDashboardExportList_toolbar.setItemToolTip('refresh', '<?php echo _l('Refresh grid', true); ?>');
exportOrderDashboardExportList_toolbar.addButton("add", 1000, "", "fa fa-plus-circle", "fa fa-plus-circle");
exportOrderDashboardExportList_toolbar.setItemToolTip('add', '<?php echo _l('Add an export', true); ?>');
exportOrderDashboardExportList_toolbar.addButton("delete", 1000, "", "fa fa-minus-circle", "fa fa-minus-circle");
exportOrderDashboardExportList_toolbar.setItemToolTip('delete', '<?php echo _l('Delete selected export',true); ?>');
exportOrderDashboardExportList_toolbar.addButton("duplicate", 1000, "", "fad fa-copy", "fad fa-copy");
exportOrderDashboardExportList_toolbar.setItemToolTip('duplicate', '<?php echo _l('Duplicate selected export', true); ?>');
exportOrderDashboardExportList_toolbar.addButton("help", 1000, "", "fad fa-question-circle blue", "fad fa-question-circle blue");
exportOrderDashboardExportList_toolbar.setItemToolTip('help', '<?php echo _l('Help', true); ?>');

exportOrderDashboardExportList_toolbar.attachEvent("onClick", function (item) {
    switch (item) {
        case 'refresh':
            exportOrderDashboardExportList_grid._displayGrid();
            break;
        case 'add':
            $.post('index.php?ajax=1&act=ord_win-export_export_update', {
                action: 'add_export'
            },function (response) {
                let dataResponse = JSON.parse(response);
                dhtmlx.message({text: dataResponse.message, type: dataResponse.state, expire: 5000});
                exportOrderDashboardExportList_grid._displayGrid();
            });
            break;
        case 'delete':
            if(exportOrderDashboardExportList_grid.getSelectedRowId() && confirm('<?php echo _l('Do you really want to delete this export?', true); ?>')) {
                $.post('index.php?ajax=1&act=ord_win-export_export_update', {
                    action: 'delete_export',
                    '<?php echo ExportOrder::$definition['primary']; ?>': exportOrderDashboardExportList_grid.getSelectedRowId()
                }, function (response) {
                    let dataResponse = JSON.parse(response);
                    dhtmlx.message({text: dataResponse.message, type: dataResponse.state, expire: 5000});
                    exportOrderDashboardExportList_grid._displayGrid();
                });
            }
            break;
        case 'duplicate':
            $.post('index.php?ajax=1&act=ord_win-export_export_update', {
                action: 'duplicate_export',
                '<?php echo ExportOrder::$definition['primary']; ?>': exportOrderDashboardExportList_grid.getSelectedRowId()
            }, function (response) {
                let dataResponse = JSON.parse(response);
                dhtmlx.message({text: dataResponse.message, type: dataResponse.state, expire: 5000});
                exportOrderDashboardExportList_grid._displayGrid();
            });
            break;
        case 'help':
            window.open('<?php echo getScExternalLink('support_export_orders'); ?>');
            break;
        default:
    }
});

exportOrderDashboardExportList_grid = exportOrderDashboardExportList.attachGrid();
exportOrderDashboardExportList_grid._name = 'grid';


exportOrderDashboardExportList_grid._displayGrid = function () {
    exportOrderDashboardExportList_grid.clearAll(true);
    $.post('index.php?ajax=1&act=ord_win-export_export_get', function (data) {
        exportOrderDashboardExportList_grid.parse(data);
    });
};
exportOrderDashboardExportList_grid._last_id_export = null;
exportOrderDashboardExportList_grid.attachEvent("onRowSelect", function (id_export) {
    if(exportOrderDashboardExportList_grid._last_id_export !== id_export) {
        exportOrderDashboardExportList_grid._last_id_export = id_export;
        exportOrderDashboardPreview_grid._displayGrid();
    }
});
exportOrderDashboardExportList_grid.attachEvent("onEditCell",function(stage,rId,cInd,nValue,oValue){
    switch (stage)
    {
        case 2:
            if(nValue !== oValue) {
                $.post('index.php?ajax=1&act=ord_win-export_export_update', {
                    action: 'update_export',
                    '<?php echo ExportOrder::$definition['primary']; ?>': rId,
                    field: exportOrderDashboardExportList_grid.getColumnId(cInd),
                    value: nValue
                },function (response) {
                    let dataResponse = JSON.parse(response);
                    if(dataResponse.filename)
                    {
                        exportOrderDashboardExportList_grid.cells(rId,exportOrderDashboardExportList_grid.getColIndexById('filename')).setValue(dataResponse.filename);
                    }
                    dhtmlx.message({text: dataResponse.message, type: dataResponse.state, expire: 5000});
                });
            }
            break;
        default:
    }
    return true;
});


/**
 * Export preview
 */
var exportOrderDashboardPreview = exportOrderDashboardTab_layout.cells('b');
exportOrderDashboardPreview.setText('<?php echo _l('Overview limited to %s results', true,array(ExportOrderFilter::$preview_limit)); ?>');
var exportOrderDashboardPreview_statusbar = exportOrderDashboardPreview.attachStatusBar();
exportOrderDashboardPreview_statusbar.setText('0 <?php echo _l('order'); ?>');

exportOrderDashboardPreview._toolbar = exportOrderDashboardPreview.attachToolbar();
exportOrderDashboardPreview._toolbar.setIconset('awesome');
exportOrderDashboardPreview._toolbar.addButton("refresh", 1000, "", "fa fa-sync green", "fa fa-sync green");
exportOrderDashboardPreview._toolbar.setItemToolTip('refresh', '<?php echo _l('Refresh grid', true); ?>');
exportOrderDashboardPreview._toolbar.addButton("export", 1000, "<?php echo _l('Export', true); ?>", "fad fa-sign-out fa-flip-horizontal", "fad fa-sign-out fa-flip-horizontal");
exportOrderDashboardPreview._toolbar.setItemToolTip('export', '<?php echo _l('Export', true); ?>');

exportOrderDashboardPreview._toolbar.attachEvent("onClick", function (item) {
    switch (item) {
        case 'refresh':
            exportOrderDashboardPreview_grid._displayGrid();
            break;
        case 'export':
            let exportSelection = exportOrderDashboardExportList_grid.getSelectedRowId();
            if(!exportSelection)
            {
                alert('<?php echo _l('Please select an export first', true); ?>');
                return false;
            }
            exportOrderDashboardExport_result.progressOn();
            $.post('index.php?ajax=1&act=ord_win-export_process', {
                '<?php echo ExportOrderTools::getDefaultHash();?>': exportSelection,
                expectedNumberOfOrders:Number(exportOrderDashboardPreview_grid.getUserData('','numberFound'))
            },function (response) {
                exportOrderDashboardExport_result.progressOff();
                let dataResponse = JSON.parse(response);
                dhtmlx.message({text: dataResponse.message, type: dataResponse.state, expire: 5000});
                exportOrderDashboardExport_result._grid._displayGrid();
                exportOrderDashboardExportList_grid.cells(exportSelection, exportOrderDashboardExportList_grid.getColIndexById('date_last_export')).setValue(dataResponse.date_last_export);
            });
            break;
        default:
    }
});
var exportOrderDashboardPreview_grid = exportOrderDashboardPreview.attachGrid();
exportOrderDashboardPreview_grid._name = 'grid';
exportOrderDashboardPreview_grid._number_orders = 0;
exportOrderDashboardPreview_grid._displayGrid = function () {
    exportOrderDashboardPreview_grid.clearAll(true);
    let id_filter = exportOrderDashboardExportList_grid.cells(exportOrderDashboardExportList_grid.getSelectedRowId(),exportOrderDashboardExportList_grid.getColIndexById('<?php echo ExportOrderFilter::$definition['primary']; ?>')).getValue();
    exportOrderDashboardPreview.progressOn();
    $.post('index.php?ajax=1&act=ord_win-export_preview_get', {
        '<?php echo ExportOrderFilter::$definition['primary']; ?>': id_filter
    }, function (data) {
        exportOrderDashboardPreview.progressOff();
        exportOrderDashboardPreview_grid.parse(data);
        let nb = exportOrderDashboardPreview_grid._number_orders = Number(exportOrderDashboardPreview_grid.getUserData('', 'numberFound'));
        exportOrderDashboardPreview_statusbar.setText(nb + (nb > 1 ? " <?php echo _l('orders'); ?>" : " <?php echo _l('order'); ?>"));
    });
};

/**
 * Export Results
 */
var exportOrderDashboardExport_result = exportOrderDashboardTab_layout.cells('c');
exportOrderDashboardExport_result.setWidth(380)
exportOrderDashboardExport_result.setText('<?php echo _l('Export files', true); ?>');
exportOrderDashboardExport_result._toolbar = exportOrderDashboardExport_result.attachToolbar();
exportOrderDashboardExport_result._toolbar.setIconset('awesome');
exportOrderDashboardExport_result._toolbar.addButton("refresh", 1000, "", "fa fa-sync green", "fa fa-sync green");
exportOrderDashboardExport_result._toolbar.setItemToolTip('refresh', '<?php echo _l('Refresh grid', true); ?>');
exportOrderDashboardExport_result._toolbar.addButton('delete',1000,'','fa fa-minus-circle red','fa fa-minus-circle red');
exportOrderDashboardExport_result._toolbar.setItemToolTip('delete','<?php echo _l('Delete exported files', 1); ?>');

exportOrderDashboardExport_result._toolbar.attachEvent("onClick", function (item) {
    switch (item) {
        case 'refresh':
            exportOrderDashboardExport_result._grid._displayGrid();
            break;
        case 'delete':
            let fileSelection = exportOrderDashboardExport_result._grid.getSelectedRowId();
            if(!fileSelection)
            {
                return false;
            }
            if(confirm('<?php echo _l('Are you sure you want to delete the selected items?', 1); ?>'))
            {
                $.post('index.php?ajax=1&act=ord_win-export_files_update', {
                    action : 'delete',
                    selection: exportOrderDashboardExport_result._grid.getUserData(fileSelection,'token')
                },function (response) {
                    let dataResponse = JSON.parse(response);
                    dhtmlx.message({text: dataResponse.message, type: dataResponse.state, expire: 5000});
                    exportOrderDashboardExport_result._grid._displayGrid();
                });
            }
            break;
        default:
    }
});


exportOrderDashboardExport_result._grid = exportOrderDashboardExport_result.attachGrid();
exportOrderDashboardExport_result._grid._name = 'grid';


exportOrderDashboardExport_result._grid._displayGrid = function () {
    exportOrderDashboardExport_result._grid.clearAll(true);
    $.post('index.php?ajax=1&act=ord_win-export_files_get', function (data) {
        exportOrderDashboardExport_result._grid.parse(data);
    });
};