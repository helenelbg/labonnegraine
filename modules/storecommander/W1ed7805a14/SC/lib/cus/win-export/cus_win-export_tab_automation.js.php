<?php
if (!defined('STORE_COMMANDER')) { exit; }
?>
/**
 * Init
 */
var exportCustomerAutomationTab = ExportCustomersTabbar.tabs('cron');
var exportCustomerAutomationTab_layout = exportCustomerAutomationTab.attachLayout("1C");
var exportCustomerAutomationTab_list = exportCustomerAutomationTab_layout.cells('a');
exportCustomerAutomationTab_list.setText('<?php echo _l('List', true); ?>');

var exportCustomerAutomationTab_list_toolbar = exportCustomerAutomationTab_list.attachToolbar();
exportCustomerAutomationTab_list_toolbar.setIconset('awesome');
exportCustomerAutomationTab_list_toolbar.addButton("refresh", 1000, "", "fa fa-sync", "fa fa-sync");
exportCustomerAutomationTab_list_toolbar.setItemToolTip('refresh', '<?php echo _l('Refresh grid', true); ?>');
exportCustomerAutomationTab_list_toolbar.addButton("selectAll", 1000, "", "fa fa-bolt", "fa fa-bolt");
exportCustomerAutomationTab_list_toolbar.setItemToolTip('selectAll', '<?php echo _l('Select all', true); ?>');
exportCustomerAutomationTab_list_toolbar.addButton("reset_token", 1000, "", "fa fa-lock-alt", "fa fa-lock-alt");
exportCustomerAutomationTab_list_toolbar.setItemToolTip('reset_token', '<?php echo _l('Regenerate a new security key for selected rows', true); ?>');
exportCustomerAutomationTab_list_toolbar.addButton("help", 1000, "", "fad fa-question-circle blue", "fad fa-question-circle blue");
exportCustomerAutomationTab_list_toolbar.setItemToolTip('help', '<?php echo _l('Help', true); ?>');

exportCustomerAutomationTab_list_toolbar.attachEvent("onClick", function (item) {
    let selectedCron = exportCustomerCron_grid.getSelectedRowId();
    switch (item) {
        case 'selectAll':
            exportCustomerCron_grid.selectAll();
            break;
        case 'reset_token':
            if(selectedCron && confirm('<?php echo _l('Do you really want to reset selected tokens?'); ?>'))
            {
                for(const id_row of selectedCron.split(','))
                {
                    exportCustomerCron_grid.setRowTextBold(id_row);
                }
                $.post('index.php?ajax=1&act=cus_win-export_export_update', {
                    action: 'reset_token',
                    export_list: selectedCron
                },function (response) {
                    let dataResponse = JSON.parse(response);
                    dhtmlx.message({text: dataResponse.message, type: dataResponse.state, expire: 5000});
                    if(dataResponse.state === 'success')
                    {
                        exportCustomerCron_grid._displayGrid();
                    }
                });
            }
            break;
        case 'refresh':
            exportCustomerCron_grid._displayGrid();
            break;
        case 'help':
            window.open('<?php echo getScExternalLink('support_export_customers'); ?>');
            break;
        default:
    }
});


var exportCustomerCron_grid = exportCustomerAutomationTab_list.attachGrid();
exportCustomerCron_grid._name = 'grid';
exportCustomerCron_grid.enableMultiselect(true);

exportCustomerCron_grid._displayGrid = function () {
    exportCustomerCron_grid.clearAll(false);
    $.post('index.php?ajax=1&act=cus_win-export_cron_get', function (data) {
        exportCustomerCron_grid.parse(data);
    });
};

exportCustomerCron_grid.attachEvent("onRowDblClicked",function(rId,cInd){
    if(cInd === exportCustomerCron_grid.getColIndexById('url_export'))
    {
        let urlCron = exportCustomerCron_grid.cells(rId,cInd).getValue();
        copyToClipBoard(urlCron, '<?php echo _l('Url successfully copied to clipboard', true); ?>');
    }
    return false;
});

exportCustomerCron_grid.attachEvent("onEditCell",function(stage){
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
    exportCustomerCron_grid.callEvent("onRowDblClicked", [rId,exportCustomerCron_grid.getColIndexById('url_export')]);
}