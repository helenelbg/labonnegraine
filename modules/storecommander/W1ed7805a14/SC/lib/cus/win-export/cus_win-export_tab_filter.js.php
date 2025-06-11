<?php
if (!defined('STORE_COMMANDER')) { exit; }
?>
/**
 * Init
 */
var exportCustomerFilterTab = ExportCustomersTabbar.tabs('filter');
var exportCustomerFilterTab_layout = exportCustomerFilterTab.attachLayout("2U");

/**
 * Filter list
 */
var exportCustomerFilterTab_list = exportCustomerFilterTab_layout.cells('a');
exportCustomerFilterTab_list.setText('<?php echo _l('Filter list', true); ?>');
exportCustomerFilterTab_list.setWidth(350);

var exportCustomerFilterTab_list_toolbar = exportCustomerFilterTab_list.attachToolbar();
exportCustomerFilterTab_list_toolbar.setIconset('awesome');
exportCustomerFilterTab_list_toolbar.addButton("refresh", 1000, "", "fa fa-sync green", "fa fa-sync green");
exportCustomerFilterTab_list_toolbar.setItemToolTip('refresh', '<?php echo _l('Refresh grid', true); ?>');
exportCustomerFilterTab_list_toolbar.addButton("add", 1000, "", "fa fa-plus-circle", "fa fa-plus-circle");
exportCustomerFilterTab_list_toolbar.setItemToolTip('add', '<?php echo _l('Add a filter', true); ?>');
exportCustomerFilterTab_list_toolbar.addButton("delete", 1000, "", "fa fa-minus-circle", "fa fa-minus-circle");
exportCustomerFilterTab_list_toolbar.setItemToolTip('delete', '<?php echo _l('Delete selected filter'); ?>');
exportCustomerFilterTab_list_toolbar.addButton("duplicate", 1000, "", "fad fa-copy", "fad fa-copy");
exportCustomerFilterTab_list_toolbar.setItemToolTip('duplicate', '<?php echo _l('Duplicate selected export', true); ?>');
exportCustomerFilterTab_list_toolbar.addButton("help", 1000, "", "fad fa-question-circle blue", "fad fa-question-circle blue");
exportCustomerFilterTab_list_toolbar.setItemToolTip('help', '<?php echo _l('Help', true); ?>');

exportCustomerFilterTab_list_toolbar.attachEvent("onClick", function (item) {
    switch (item) {
        case 'add':
            $.post('index.php?ajax=1&act=cus_win-export_filter_update', {
                action: 'add_filter'
            },function (response) {
                let dataResponse = JSON.parse(response);
                dhtmlx.message({text: dataResponse.message, type: dataResponse.state, expire: 5000});
                exportCustomerFilterTab_list_grid._displayGrid();
            });
            break;
        case 'delete':
            if(exportCustomerFilterTab_list_grid.getSelectedRowId() && confirm('<?php echo _l('Do you really want to delete this filter?', true); ?>')) {
                $.post('index.php?ajax=1&act=cus_win-export_filter_update', {
                    action: 'delete_filter',
                    '<?php echo ExportCustomerFilter::$definition['primary']; ?>': exportCustomerFilterTab_list_grid.getSelectedRowId()
                }, function (response) {
                    let dataResponse = JSON.parse(response);
                    dhtmlx.message({text: dataResponse.message, type: dataResponse.state, expire: 5000});
                    exportCustomerFilterTab_list_grid._displayGrid();
                });
            }
            break;
        case 'refresh':
            exportCustomerFilterTab_list_grid._displayGrid();
            break;
        case 'help':
            window.open('<?php echo getScExternalLink('support_export_customers'); ?>');
            break;
        case 'duplicate':
            $.post('index.php?ajax=1&act=cus_win-export_filter_update', {
                action: 'duplicate_filter',
                '<?php echo ExportCustomerFilter::$definition['primary']; ?>': exportCustomerFilterTab_list_grid.getSelectedRowId()
            }, function (response) {
                let dataResponse = JSON.parse(response);
                dhtmlx.message({text: dataResponse.message, type: dataResponse.state, expire: 5000});
                exportCustomerFilterTab_list_grid._displayGrid();
            });
            break;
        default:
    }
});

exportCustomerFilterTab_list_grid = exportCustomerFilterTab_list.attachGrid();
exportCustomerFilterTab_list_grid._name = 'grid';

exportCustomerFilterTab_list_grid._displayGrid = function () {
    exportCustomerFilterTab_list_grid.clearAll(true);
    $.post('index.php?ajax=1&act=cus_win-export_filter_get', function (data) {
        exportCustomerFilterTab_list_grid.parse(data);
        exportCustomerFilterTab_list_grid.enableHeaderMenu();
    });
};

exportCustomerFilterTab_list_grid._setFormCellText = function () {
    let idxFilterName = exportCustomerFilterTab_list_grid.getColIndexById('name');
    let filtername = exportCustomerFilterTab_list_grid.cells(exportCustomerFilterTab_list_grid._last_id_filter,idxFilterName).getValue();
    exportCustomerFilterTab_configuration_layout.setText("<?php echo _l('Configure your filter', true)._l(':'); ?> "+filtername);
};

exportCustomerFilterTab_list_grid._last_id_filter = null;
exportCustomerFilterTab_list_grid.attachEvent("onRowSelect", function (id_filter) {
    if(exportCustomerFilterTab_list_grid._last_id_filter !== id_filter) {
        exportCustomerFilterTab_list_grid._last_id_filter = id_filter;
        exportCustomerFilterTab_list_grid._setFormCellText();
        exportCustomerFilterTab_configuration_layout.attachURL('index.php?ajax=1&act=cus_win-export_filter_form', null, {
            '<?php echo ExportCustomerFilter::$definition['primary']; ?>': id_filter
        });
    }
});

exportCustomerFilterTab_list_grid.attachEvent("onEditCell",function(stage,rId,cInd,nValue,oValue){
    switch (stage)
    {
        case 2:
            if(nValue !== oValue) {
                $.post('index.php?ajax=1&act=cus_win-export_filter_update', {
                    action: 'update_filter',
                    '<?php echo ExportCustomerFilter::$definition['primary']; ?>': rId,
                    field: exportCustomerFilterTab_list_grid.getColumnId(cInd),
                    value: nValue
                },function (response) {
                    let dataResponse = JSON.parse(response);
                    dhtmlx.message({text: dataResponse.message, type: dataResponse.state, expire: 5000});
                    exportCustomerFilterTab_list_grid._setFormCellText();
                });
            }
            break;
        default:
    }
    return true;
});


/**
 * Filter form
 */
var exportCustomerFilterTab_configuration_layout = exportCustomerFilterTab_layout.cells('b');
exportCustomerFilterTab_configuration_layout.setText("<?php echo _l('Select a filter first', true); ?>");
exportCustomerFilterTab_configuration_layout._toolbar = exportCustomerFilterTab_configuration_layout.attachToolbar();
exportCustomerFilterTab_configuration_layout._toolbar.setIconset('awesome');
exportCustomerFilterTab_configuration_layout._toolbar.addButton("refresh", 1000, "", "fa fa-sync", "fa fa-sync");
exportCustomerFilterTab_configuration_layout._toolbar.setItemToolTip('refresh', '<?php echo _l('Refresh the configuration', true); ?>');
exportCustomerFilterTab_configuration_layout._toolbar.addButton("save", 1000, "", "fad fa-save", "fad fa-save");
exportCustomerFilterTab_configuration_layout._toolbar.setItemToolTip('save', '<?php echo _l('Save the configuration', true); ?>');

exportCustomerFilterTab_configuration_layout._toolbar.attachEvent("onClick", function (item) {
    let id_filter = exportCustomerFilterTab_list_grid.getSelectedRowId();
    switch (item) {
        case 'refresh':
            if(id_filter && confirm('<?php echo _l('Do you really want to refresh this form? You will loose all progression', true); ?>')) {
                exportCustomerFilterTab_configuration_layout.attachURL('index.php?ajax=1&act=cus_win-export_filter_form', null, {
                    '<?php echo ExportCustomerFilter::$definition['primary']; ?>': id_filter
                });
            }
            break;
        case 'save':
            if(id_filter && confirm('<?php echo _l('Do you really want to save this form?', true); ?>'))
            {
                exportCustomerFilterTab_configuration_layout.getFrame().contentWindow.submitMainForm();
            }
            break;
        default:
    }
});