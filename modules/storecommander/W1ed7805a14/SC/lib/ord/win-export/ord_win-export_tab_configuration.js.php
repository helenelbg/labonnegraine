<?php
if (!defined('STORE_COMMANDER')) { exit; }

if(_s('ORD_EXPORT_CSV')) {
?>
/**
 * Init
 */
var exportOrderConfigurationTab = exportOrdersTabbar.tabs('configuration');
var exportOrderConfigurationTab_layout = exportOrderConfigurationTab.attachLayout("1C");
var exportOrderConfigurationTab_configuration_layout = exportOrderConfigurationTab_layout.cells('a');
exportOrderConfigurationTab_configuration_layout.hideHeader();
/**
 * toolbar
 */
var exportOrderConfigurationTab_form_toolbar = exportOrderConfigurationTab_configuration_layout.attachToolbar();
exportOrderConfigurationTab_form_toolbar.setIconset('awesome');
exportOrderConfigurationTab_form_toolbar.addButton("save", 1000, "", "fa fa-save", "fa fa-save");
exportOrderConfigurationTab_form_toolbar.setItemToolTip('save', '<?php echo _l('Save the configuration', true); ?>');
exportOrderConfigurationTab_form_toolbar.addButton("reset_indexes", 1000, "", "fa fa-eraser", "fa fa-eraser");
exportOrderConfigurationTab_form_toolbar.setItemToolTip('reset_indexes', '<?php echo _l('Reset indexes', true); ?>');

exportOrderConfigurationTab_form_toolbar.attachEvent("onClick", function (item) {
    switch (item) {
        case 'save':
            if(confirm('<?php echo _l('Do you really want to save this form?', true); ?>'))
            {
                exportOrderConfigurationTab_form._form.validate();
                if(exportOrderConfigurationTab_form._form.getItemValue('error_margin') !== '')
                {
                    if(!exportOrderConfigurationTab_form._form.validateItem('error_margin'))
                    {
                        dhtmlx.message({text:'<?php echo _l('Invalid margin for error', true); ?>', type: 'error', expire: 5000});
                        return false;
                    }
                }

                $.post('index.php?ajax=1&act=ord_win-export_configuration_update', {
                    action: 'submit_configuration_form',
                    formData: exportOrderConfigurationTab_form._form.getFormData()
                },function (response) {
                    let dataResponse = JSON.parse(response);
                    dhtmlx.message({text: dataResponse.message, type: dataResponse.state, expire: 5000});
                });
            }
            break;
        case 'reset_indexes':
            if(confirm('<?php echo _l('Do you really want to reset indexes ?', true); ?>')) {
                $.post('index.php?ajax=1&act=ord_win-export_configuration_update', {
                    action: 'reset_indexes'
                }, function (response) {
                    let dataResponse = JSON.parse(response);
                    dhtmlx.message({text: dataResponse.message, type: dataResponse.state, expire: 5000});
                });
            }
            break;
        default:
    }
});
/**
 * form
 */
var exportOrderConfigurationTab_form = {};
exportOrderConfigurationTab_form._structure = [
    {
        type: "block",
        list: [
            {type: "label",
                label: "<?php echo _l('Settings to be modify only if your export includes a VAT breakdown and exported rate values are incorrect (ex. 20,1 or 19,99)', true); ?><br><?php echo _l('It is not recommended to changes these settings.', true); ?><br><?php echo _l('Before validating the modifications, make sure the values are correct so that your next exports includes the right data.', true); ?>"},
            {
                type:"input",
                label: "<?php echo _l('Margin for error on tax rate calculation', true); ?>",
                labelWidth: 200,
                offsetLeft: 25,
                inputWidth:80,
                name:"error_margin",
                maxLength:6,
                numberFormat:"000.00",
                value:"",
                validate:"ValidNumeric"
            },
            {
                type: "multiselect",
                label: "<?php echo _l('Tax rate to exclude', true); ?>",
                labelWidth: 200,
                name:"excluded_tax_rate",
                offsetLeft: 25,
                inputHeight: exportOrderConfigurationTab_configuration_layout.getHeight()-250,
                inputWidth:250,
                options: []
            }
        ]
    }
];
exportOrderConfigurationTab_form._form = exportOrderConfigurationTab_configuration_layout.attachForm(exportOrderConfigurationTab_form._structure);
exportOrderConfigurationTab_form._form.enableLiveValidation(true);

exportOrderConfigurationTab_form._loadFormData = function() {
    $.post('index.php?ajax=1&act=ord_win-export_configuration_form_data_get', {
    }, function (response) {
        let dataResponse = JSON.parse(response);
        exportOrderConfigurationTab_form._form.setItemValue('error_margin', dataResponse.margin);
        exportOrderConfigurationTab_form._form.reloadOptions('excluded_tax_rate', dataResponse.taxe_rate_list)
    });
}
<?php
}
?>