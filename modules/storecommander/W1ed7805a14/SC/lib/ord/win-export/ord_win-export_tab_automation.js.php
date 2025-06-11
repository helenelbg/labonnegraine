<?php
if (!defined('STORE_COMMANDER')) { exit; }
?>
/**
 * Init
 */
var exportOrderAutomationTab = exportOrdersTabbar.tabs('cron');
var exportOrderAutomationTab_layout = exportOrderAutomationTab.attachLayout("1C");
var exportOrderAutomationTab_list = exportOrderAutomationTab_layout.cells('a');
exportOrderAutomationTab_list.setText('<?php echo _l('List', true); ?>');

var exportOrderAutomationTab_list_toolbar = exportOrderAutomationTab_list.attachToolbar();
exportOrderAutomationTab_list_toolbar.setIconset('awesome');
exportOrderAutomationTab_list_toolbar.addButton("refresh", 1000, "", "fa fa-sync", "fa fa-sync");
exportOrderAutomationTab_list_toolbar.setItemToolTip('refresh', '<?php echo _l('Refresh grid', true); ?>');
exportOrderAutomationTab_list_toolbar.addButton("selectAll", 1000, "", "fa fa-bolt", "fa fa-bolt");
exportOrderAutomationTab_list_toolbar.setItemToolTip('selectAll', '<?php echo _l('Select all', true); ?>');
exportOrderAutomationTab_list_toolbar.addButton("reset_token", 1000, "", "fa fa-lock-alt", "fa fa-lock-alt");
exportOrderAutomationTab_list_toolbar.setItemToolTip('reset_token', '<?php echo _l('Regenerate a new security key for selected rows', true); ?>');
exportOrderAutomationTab_list_toolbar.addButton("help", 1000, "", "fad fa-question-circle blue", "fad fa-question-circle blue");
exportOrderAutomationTab_list_toolbar.setItemToolTip('help', '<?php echo _l('Help', true); ?>');

exportOrderAutomationTab_list_toolbar.attachEvent("onClick", function (item) {
    let selectedCron = exportOrderCron_grid.getSelectedRowId();
    switch (item) {
        case 'selectAll':
            exportOrderCron_grid.selectAll();
            break;
        case 'reset_token':
            if(selectedCron && confirm('<?php echo _l('Do you really want to reset selected tokens?'); ?>'))
            {
                for(const id_row of selectedCron.split(','))
                {
                    exportOrderCron_grid.setRowTextBold(id_row);
                }
                $.post('index.php?ajax=1&act=ord_win-export_export_update', {
                    action: 'reset_token',
                    export_list: selectedCron
                },function (response) {
                    let dataResponse = JSON.parse(response);
                    dhtmlx.message({text: dataResponse.message, type: dataResponse.state, expire: 5000});
                    if(dataResponse.state === 'success')
                    {
                        exportOrderCron_grid._displayGrid();
                    }
                });
            }
            break;
        case 'refresh':
            exportOrderCron_grid._displayGrid();
            break;
        case 'help':
            window.open('<?php echo getScExternalLink('support_export_orders'); ?>');
            break;
        default:
    }
});


var exportOrderCron_grid = exportOrderAutomationTab_list.attachGrid();
exportOrderCron_grid._name = 'grid';
exportOrderCron_grid.enableMultiselect(true);

exportOrderCron_grid._displayGrid = function () {
    exportOrderCron_grid.clearAll(false);
    $.post('index.php?ajax=1&act=ord_win-export_cron_get', function (data) {
        exportOrderCron_grid.parse(data);
    });
};

exportOrderCron_grid.attachEvent("onRowDblClicked",function(rId,cInd){
    if(cInd === exportOrderCron_grid.getColIndexById('url_export'))
    {
        let urlCron = exportOrderCron_grid.cells(rId,cInd).getValue();
        copyToClipBoard(urlCron);
        dhtmlx.message({text: '<?php echo _l('Url successfully copied to clipboard', true); ?>', type: 'success', expire: 5000});

    }
    return false;
});

exportOrderCron_grid.attachEvent("onEditCell",function(stage){
    switch (stage)
    {
        case 2:
            return false;
        default:
    }
    return true;
});

function copyCronUrlToClipboard(rId)
{
    exportOrderCron_grid.callEvent("onRowDblClicked", [rId,exportOrderCron_grid.getColIndexById('url_export')]);
}