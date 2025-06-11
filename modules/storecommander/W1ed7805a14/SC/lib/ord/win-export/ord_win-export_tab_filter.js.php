<?php
if (!defined('STORE_COMMANDER')) { exit; }
?>
/**
 * Init
 */
var exportOrderFilterTab = exportOrdersTabbar.tabs('filter');
var exportOrderFilterTab_layout = exportOrderFilterTab.attachLayout("2U");

/**
 * Filter list
 */
var exportOrderFilterTab_list = exportOrderFilterTab_layout.cells('a');
exportOrderFilterTab_list.setText('<?php echo _l('Filter list', true); ?>');
exportOrderFilterTab_list.setWidth(350);

var exportOrderFilterTab_list_toolbar = exportOrderFilterTab_list.attachToolbar();
exportOrderFilterTab_list_toolbar.setIconset('awesome');
exportOrderFilterTab_list_toolbar.addButton("refresh", 1000, "", "fa fa-sync green", "fa fa-sync green");
exportOrderFilterTab_list_toolbar.setItemToolTip('refresh', '<?php echo _l('Refresh grid', true); ?>');
exportOrderFilterTab_list_toolbar.addButton("add", 1000, "", "fa fa-plus-circle", "fa fa-plus-circle");
exportOrderFilterTab_list_toolbar.setItemToolTip('add', '<?php echo _l('Add a filter', true); ?>');
exportOrderFilterTab_list_toolbar.addButton("delete", 1000, "", "fa fa-minus-circle", "fa fa-minus-circle");
exportOrderFilterTab_list_toolbar.setItemToolTip('delete', '<?php echo _l('Delete selected filter'); ?>');
exportOrderFilterTab_list_toolbar.addButton("duplicate", 1000, "", "fad fa-copy", "fad fa-copy");
exportOrderFilterTab_list_toolbar.setItemToolTip('duplicate', '<?php echo _l('Duplicate selected filter', true); ?>');
exportOrderFilterTab_list_toolbar.addButton("help", 1000, "", "fad fa-question-circle blue", "fad fa-question-circle blue");
exportOrderFilterTab_list_toolbar.setItemToolTip('help', '<?php echo _l('Help', true); ?>');

exportOrderFilterTab_list_toolbar.attachEvent("onClick", function (item) {
    switch (item) {
        case 'add':
            $.post('index.php?ajax=1&act=ord_win-export_filter_update', {
                action: 'add_filter'
            },function (response) {
                let dataResponse = JSON.parse(response);
                dhtmlx.message({text: dataResponse.message, type: dataResponse.state, expire: 5000});
                exportOrderFilterTab_list_grid._displayGrid();
            });
            break;
        case 'delete':
            if(exportOrderFilterTab_list_grid.getSelectedRowId() && confirm('<?php echo _l('Do you really want to delete this filter?', true); ?>')) {
                $.post('index.php?ajax=1&act=ord_win-export_filter_update', {
                    action: 'delete_filter',
                    '<?php echo ExportOrderFilter::$definition['primary']; ?>': exportOrderFilterTab_list_grid.getSelectedRowId()
                }, function (response) {
                    let dataResponse = JSON.parse(response);
                    dhtmlx.message({text: dataResponse.message, type: dataResponse.state, expire: 5000});
                    exportOrderFilterTab_list_grid._displayGrid();
                });
            }
            break;
        case 'refresh':
            exportOrderFilterTab_list_grid._displayGrid();
            break;
        case 'duplicate':
            $.post('index.php?ajax=1&act=ord_win-export_filter_update', {
                action: 'duplicate_filter',
                '<?php echo ExportOrderFilter::$definition['primary']; ?>': exportOrderFilterTab_list_grid.getSelectedRowId()
            }, function (response) {
                let dataResponse = JSON.parse(response);
                dhtmlx.message({text: dataResponse.message, type: dataResponse.state, expire: 5000});
                exportOrderFilterTab_list_grid._displayGrid();
            });
            break;
        case 'help':
            window.open('<?php echo getScExternalLink('support_export_orders'); ?>');
            break;
        default:
    }
});

exportOrderFilterTab_list_grid = exportOrderFilterTab_list.attachGrid();
exportOrderFilterTab_list_grid._name = 'grid';

exportOrderFilterTab_list_grid._displayGrid = function () {
    exportOrderFilterTab_list_grid.clearAll(true);
    $.post('index.php?ajax=1&act=ord_win-export_filter_get', function (data) {
        exportOrderFilterTab_list_grid.parse(data);
        exportOrderFilterTab_list_grid.enableHeaderMenu();
    });
};

exportOrderFilterTab_list_grid._setFormCellText = function () {
    let idxFilterName = exportOrderFilterTab_list_grid.getColIndexById('name');
    let filtername = exportOrderFilterTab_list_grid.cells(exportOrderFilterTab_list_grid._last_id_filter,idxFilterName).getValue();
    exportOrderFilterTab_configuration_layout.setText("<?php echo _l('Configure your filter', true)._l(':'); ?> "+filtername);
};

exportOrderFilterTab_list_grid._last_id_filter = null;
exportOrderFilterTab_list_grid.attachEvent("onRowSelect", function (id_filter) {
    if(exportOrderFilterTab_list_grid._last_id_filter !== id_filter) {
        exportOrderFilterTab_list_grid._last_id_filter = id_filter;
        exportOrderFilterTab_list_grid._setFormCellText();
        exportOrderFilterTab_configuration_layout.attachURL('index.php?ajax=1&act=ord_win-export_filter_form', null, {
            '<?php echo ExportOrderFilter::$definition['primary']; ?>': id_filter
        });
    }
});

exportOrderFilterTab_list_grid.attachEvent("onEditCell",function(stage,rId,cInd,nValue,oValue){
    switch (stage)
    {
        case 2:
            if(nValue !== oValue) {
                $.post('index.php?ajax=1&act=ord_win-export_filter_update', {
                    action: 'update_filter',
                    '<?php echo ExportOrderFilter::$definition['primary']; ?>': rId,
                    field: exportOrderFilterTab_list_grid.getColumnId(cInd),
                    value: nValue
                },function (response) {
                    let dataResponse = JSON.parse(response);
                    dhtmlx.message({text: dataResponse.message, type: dataResponse.state, expire: 5000});
                    exportOrderFilterTab_list_grid._setFormCellText();
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
var exportOrderFilterTab_configuration_layout = exportOrderFilterTab_layout.cells('b');
exportOrderFilterTab_configuration_layout.setText("<?php echo _l('Select a filter first', true); ?>");
exportOrderFilterTab_configuration_layout._toolbar = exportOrderFilterTab_configuration_layout.attachToolbar();
exportOrderFilterTab_configuration_layout._toolbar.setIconset('awesome');
exportOrderFilterTab_configuration_layout._toolbar.addButton("refresh", 1000, "", "fa fa-sync", "fa fa-sync");
exportOrderFilterTab_configuration_layout._toolbar.setItemToolTip('refresh', '<?php echo _l('Refresh the configuration', true); ?>');
exportOrderFilterTab_configuration_layout._toolbar.addButton("save", 1000, "", "fad fa-save", "fad fa-save");
exportOrderFilterTab_configuration_layout._toolbar.setItemToolTip('save', '<?php echo _l('Save the configuration', true); ?>');

exportOrderFilterTab_configuration_layout._toolbar.attachEvent("onClick", function (item) {
    let id_filter = exportOrderFilterTab_list_grid.getSelectedRowId();
    switch (item) {
        case 'refresh':
            if(id_filter && confirm('<?php echo _l('Do you really want to refresh this form? You will loose all progression', true); ?>')) {
                exportOrderFilterTab_configuration_layout.attachURL('index.php?ajax=1&act=ord_win-export_filter_form', null, {
                    '<?php echo ExportOrderFilter::$definition['primary']; ?>': id_filter
                });
            }
            break;
        case 'save':
            if(id_filter && confirm('<?php echo _l('Do you really want to save this form?', true); ?>'))
            {
                exportOrderFilterTab_configuration_layout.getFrame().contentWindow.submitMainForm();
            }
            break;
        default:
    }
});