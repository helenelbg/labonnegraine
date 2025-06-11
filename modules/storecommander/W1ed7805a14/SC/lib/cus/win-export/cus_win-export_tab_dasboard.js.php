<?php
if (!defined('STORE_COMMANDER')) { exit; }
?>
/**
 * Init
 */
var exportCustomerDashboardTab = ExportCustomersTabbar.tabs('dashboard');
var exportCustomerDashboardTab_layout = exportCustomerDashboardTab.attachLayout("3W");

exportCustomerDashboardTab_layout._displayAllColumnGrid = function () {
    exportCustomerDashboardExportList_grid._displayGrid();
    exportCustomerDashboardExport_result._grid._displayGrid();
}

/**
 * Export list
 */
var exportCustomerDashboardExportList = exportCustomerDashboardTab_layout.cells('a');
exportCustomerDashboardExportList.setText('<?php echo _l('Export list', true); ?>');
exportCustomerDashboardExportList.setWidth((dhxExportCustomers.cells('a').getWidth()/2)-80);

var exportCustomerDashboardExportList_toolbar = exportCustomerDashboardExportList.attachToolbar();
exportCustomerDashboardExportList_toolbar.setIconset('awesome');
exportCustomerDashboardExportList_toolbar.addButton("refresh", 1000, "", "fa fa-sync green", "fa fa-sync green");
exportCustomerDashboardExportList_toolbar.setItemToolTip('refresh', '<?php echo _l('Refresh grid', true); ?>');
exportCustomerDashboardExportList_toolbar.addButton("add", 1000, "", "fa fa-plus-circle", "fa fa-plus-circle");
exportCustomerDashboardExportList_toolbar.setItemToolTip('add', '<?php echo _l('Add an export', true); ?>');
exportCustomerDashboardExportList_toolbar.addButton("delete", 1000, "", "fa fa-minus-circle", "fa fa-minus-circle");
exportCustomerDashboardExportList_toolbar.setItemToolTip('delete', '<?php echo _l('Delete selected export',true); ?>');
exportCustomerDashboardExportList_toolbar.addButton("duplicate", 1000, "", "fad fa-copy", "fad fa-copy");
exportCustomerDashboardExportList_toolbar.setItemToolTip('duplicate', '<?php echo _l('Duplicate selected export', true); ?>');
exportCustomerDashboardExportList_toolbar.addButton("help", 1000, "", "fad fa-question-circle blue", "fad fa-question-circle blue");
exportCustomerDashboardExportList_toolbar.setItemToolTip('help', '<?php echo _l('Help', true); ?>');

exportCustomerDashboardExportList_toolbar.attachEvent("onClick", function (item) {
    switch (item) {
        case 'refresh':
            exportCustomerDashboardExportList_grid._displayGrid();
            break;
        case 'add':
            $.post('index.php?ajax=1&act=cus_win-export_export_update', {
                action: 'add_export'
            },function (response) {
                let dataResponse = JSON.parse(response);
                dhtmlx.message({text: dataResponse.message, type: dataResponse.state, expire: 5000});
                exportCustomerDashboardExportList_grid._displayGrid();
            });
            break;
        case 'delete':
            if(exportCustomerDashboardExportList_grid.getSelectedRowId() && confirm('<?php echo _l('Do you really want to delete this export?', true); ?>')) {
                $.post('index.php?ajax=1&act=cus_win-export_export_update', {
                    action: 'delete_export',
                    '<?php echo ExportCustomer::$definition['primary']; ?>': exportCustomerDashboardExportList_grid.getSelectedRowId()
                }, function (response) {
                    let dataResponse = JSON.parse(response);
                    dhtmlx.message({text: dataResponse.message, type: dataResponse.state, expire: 5000});
                    exportCustomerDashboardExportList_grid._displayGrid();
                });
            }
            break;
        case 'duplicate':
            $.post('index.php?ajax=1&act=cus_win-export_export_update', {
                action: 'duplicate_export',
                '<?php echo ExportCustomer::$definition['primary']; ?>': exportCustomerDashboardExportList_grid.getSelectedRowId()
            }, function (response) {
                let dataResponse = JSON.parse(response);
                dhtmlx.message({text: dataResponse.message, type: dataResponse.state, expire: 5000});
                exportCustomerDashboardExportList_grid._displayGrid();
            });
            break;
        case 'help':
            window.open('<?php echo getScExternalLink('support_export_customers'); ?>');
            break;
        default:
    }
});

exportCustomerDashboardExportList_grid = exportCustomerDashboardExportList.attachGrid();
exportCustomerDashboardExportList_grid._name = 'grid';


exportCustomerDashboardExportList_grid._displayGrid = function () {
    exportCustomerDashboardExportList_grid.clearAll(true);
    $.post('index.php?ajax=1&act=cus_win-export_export_get', function (data) {
        exportCustomerDashboardExportList_grid.parse(data);
    });
};
exportCustomerDashboardExportList_grid._last_id_export = null;
exportCustomerDashboardExportList_grid.attachEvent("onRowSelect", function (id_export) {
    if(exportCustomerDashboardExportList_grid._last_id_export !== id_export) {
        exportCustomerDashboardExportList_grid._last_id_export = id_export;
        exportCustomerDashboardPreview_grid._displayGrid();
    }
});
exportCustomerDashboardExportList_grid.attachEvent("onEditCell",function(stage,rId,cInd,nValue,oValue){
    switch (stage)
    {
        case 2:
            if(nValue !== oValue) {
                $.post('index.php?ajax=1&act=cus_win-export_export_update', {
                    action: 'update_export',
                    '<?php echo ExportCustomer::$definition['primary']; ?>': rId,
                    field: exportCustomerDashboardExportList_grid.getColumnId(cInd),
                    value: nValue
                },function (response) {
                    let dataResponse = JSON.parse(response);
                    if(dataResponse.filename)
                    {
                        exportCustomerDashboardExportList_grid.cells(rId,exportCustomerDashboardExportList_grid.getColIndexById('filename')).setValue(dataResponse.filename);
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
var exportCustomerDashboardPreview = exportCustomerDashboardTab_layout.cells('b');
exportCustomerDashboardPreview.setText('<?php echo _l('Overview limited to %s results', true,array(ExportCustomerFilter::$preview_limit)); ?>');
var exportCustomerDashboardPreview_statusbar = exportCustomerDashboardPreview.attachStatusBar();
exportCustomerDashboardPreview_statusbar.setText('0 <?php echo _l('customer'); ?>');

exportCustomerDashboardPreview._toolbar = exportCustomerDashboardPreview.attachToolbar();
exportCustomerDashboardPreview._toolbar.setIconset('awesome');
exportCustomerDashboardPreview._toolbar.addButton("refresh", 1000, "", "fa fa-sync green", "fa fa-sync green");
exportCustomerDashboardPreview._toolbar.setItemToolTip('refresh', '<?php echo _l('Refresh grid', true); ?>');
exportCustomerDashboardPreview._toolbar.addButton("export", 1000, "<?php echo _l('Export', true); ?>", "fad fa-sign-out fa-flip-horizontal", "fad fa-sign-out fa-flip-horizontal");
exportCustomerDashboardPreview._toolbar.setItemToolTip('export', '<?php echo _l('Export', true); ?>');

exportCustomerDashboardPreview._toolbar.attachEvent("onClick", function (item) {
    switch (item) {
        case 'refresh':
            exportCustomerDashboardPreview_grid._displayGrid();
            break;
        case 'export':
            let exportSelection = exportCustomerDashboardExportList_grid.getSelectedRowId();
            if(!exportSelection)
            {
                alert('<?php echo _l('Please select an export first', true); ?>');
                return false;
            }
            exportCustomerDashboardExport_result.progressOn();
            $.post('index.php?ajax=1&act=cus_win-export_process', {
                '<?php echo ExportCustomerTools::getDefaultHash();?>': exportSelection,
                expectedNumberOfCustomers:Number(exportCustomerDashboardPreview_grid.getUserData('','numberFound'))
            },function (response) {
                exportCustomerDashboardExport_result.progressOff();
                let dataResponse = JSON.parse(response);
                dhtmlx.message({text: dataResponse.message, type: dataResponse.state, expire: 15000});
                exportCustomerDashboardExport_result._grid._displayGrid();
                exportCustomerDashboardExportList_grid.cells(exportSelection, exportCustomerDashboardExportList_grid.getColIndexById('date_last_export')).setValue(dataResponse.date_last_export);
            });
        default:
    }
});
var exportCustomerDashboardPreview_grid = exportCustomerDashboardPreview.attachGrid();
exportCustomerDashboardPreview_grid._name = 'grid';
exportCustomerDashboardPreview_grid._number_customers = 0;
exportCustomerDashboardPreview_grid._displayGrid = function () {
    exportCustomerDashboardPreview_grid.clearAll(true);
    let id_filter = exportCustomerDashboardExportList_grid.cells(exportCustomerDashboardExportList_grid.getSelectedRowId(),exportCustomerDashboardExportList_grid.getColIndexById('<?php echo ExportCustomerFilter::$definition['primary']; ?>')).getValue();
    exportCustomerDashboardPreview.progressOn();
    $.post('index.php?ajax=1&act=cus_win-export_preview_get', {
        '<?php echo ExportCustomerFilter::$definition['primary']; ?>': id_filter
    }, function (data) {
        exportCustomerDashboardPreview.progressOff();
        exportCustomerDashboardPreview_grid.parse(data);
        let nb = exportCustomerDashboardPreview_grid._number_customers = Number(exportCustomerDashboardPreview_grid.getUserData('', 'numberFound'));
        exportCustomerDashboardPreview_statusbar.setText(nb + (nb > 1 ? " <?php echo _l('customers'); ?>" : " <?php echo _l('customer'); ?>"));
    });
};

/**
 * Export Results
 */
var exportCustomerDashboardExport_result = exportCustomerDashboardTab_layout.cells('c');
exportCustomerDashboardExport_result.setWidth(380)
exportCustomerDashboardExport_result.setText('<?php echo _l('Exported files', true); ?>');
exportCustomerDashboardExport_result._toolbar = exportCustomerDashboardExport_result.attachToolbar();
exportCustomerDashboardExport_result._toolbar.setIconset('awesome');
exportCustomerDashboardExport_result._toolbar.addButton("refresh", 1000, "", "fa fa-sync green", "fa fa-sync green");
exportCustomerDashboardExport_result._toolbar.setItemToolTip('refresh', '<?php echo _l('Refresh grid', true); ?>');
exportCustomerDashboardExport_result._toolbar.addButton('delete',1000,'','fa fa-minus-circle red','fa fa-minus-circle red');
exportCustomerDashboardExport_result._toolbar.setItemToolTip('delete','<?php echo _l('Delete exported files', 1); ?>');

exportCustomerDashboardExport_result._toolbar.attachEvent("onClick", function (item) {
    switch (item) {
        case 'refresh':
            exportCustomerDashboardExport_result._grid._displayGrid();
            break;
        case 'delete':
            let fileSelection = exportCustomerDashboardExport_result._grid.getSelectedRowId();
            if(!fileSelection)
            {
                return false;
            }
            if(confirm('<?php echo _l('Are you sure you want to delete the selected items?', 1); ?>'))
            {
                $.post('index.php?ajax=1&act=cus_win-export_files_update', {
                    action : 'delete',
                    selection: exportCustomerDashboardExport_result._grid.getUserData(fileSelection,'token')
                },function (response) {
                    let dataResponse = JSON.parse(response);
                    dhtmlx.message({text: dataResponse.message, type: dataResponse.state, expire: 5000});
                    exportCustomerDashboardExport_result._grid._displayGrid();
                });
            }
            break;
        default:
    }
});


exportCustomerDashboardExport_result._grid = exportCustomerDashboardExport_result.attachGrid();
exportCustomerDashboardExport_result._grid._name = 'grid';


exportCustomerDashboardExport_result._grid._displayGrid = function () {
    exportCustomerDashboardExport_result._grid.clearAll(true);
    $.post('index.php?ajax=1&act=cus_win-export_files_get', function (data) {
        exportCustomerDashboardExport_result._grid.parse(data);
    });
};