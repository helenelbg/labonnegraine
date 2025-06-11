<?php
if (!defined('STORE_COMMANDER')) { exit; }
?>
/**
 * Init
 */
var exportCustomerMappingTab = ExportCustomersTabbar.tabs('mapping');
var exportCustomerMappingTab_layout = exportCustomerMappingTab.attachLayout("2U");

/**
 * Mapping list
 */
var exportCustomerMappingTab_list = exportCustomerMappingTab_layout.cells('a');
exportCustomerMappingTab_list.setText('<?php echo _l('Template list', true); ?>');
exportCustomerMappingTab_list.setWidth(400);

var exportCustomerMappingTab_list_toolbar = exportCustomerMappingTab_list.attachToolbar();
exportCustomerMappingTab_list_toolbar.setIconset('awesome');
exportCustomerMappingTab_list_toolbar.addButton("refresh", 1000, "", "fa fa-sync green", "fa fa-sync green");
exportCustomerMappingTab_list_toolbar.setItemToolTip('refresh', '<?php echo _l('Refresh grid', true); ?>');
exportCustomerMappingTab_list_toolbar.addButton("add", 1000, "", "fa fa-plus-circle", "fa fa-plus-circle");
exportCustomerMappingTab_list_toolbar.setItemToolTip('add', '<?php echo _l('Add a template', true); ?>');
exportCustomerMappingTab_list_toolbar.addButton("delete", 1000, "", "fa fa-minus-circle", "fa fa-minus-circle");
exportCustomerMappingTab_list_toolbar.setItemToolTip('delete', '<?php echo _l('Delete selected template', true); ?>');
exportCustomerMappingTab_list_toolbar.addButton("duplicate", 1000, "", "fad fa-copy", "fad fa-copy");
exportCustomerMappingTab_list_toolbar.setItemToolTip('duplicate', '<?php echo _l('Duplicate selected export', true); ?>');
exportCustomerMappingTab_list_toolbar.addButton("help", 1000, "", "fad fa-question-circle blue", "fad fa-question-circle blue");
exportCustomerMappingTab_list_toolbar.setItemToolTip('help', '<?php echo _l('Help', true); ?>');

exportCustomerMappingTab_list_toolbar.attachEvent("onClick", function (item) {
    switch (item) {
        case 'add':
            $.post('index.php?ajax=1&act=cus_win-export_mapping_update', {
                action: 'add_mapping'
            },function (response) {
                let dataResponse = JSON.parse(response);
                dhtmlx.message({text: dataResponse.message, type: dataResponse.state, expire: 5000});
                exportCustomerMappingTab_list_grid._displayGrid();
            });
            break;
        case 'delete':
            if(exportCustomerMappingTab_list_grid.getSelectedRowId() && confirm('<?php echo _l('Do you really want to delete this template?', true); ?>')) {
                $.post('index.php?ajax=1&act=cus_win-export_mapping_update', {
                    action: 'delete_mapping',
                    '<?php echo ExportCustomerMapping::$definition['primary']; ?>': exportCustomerMappingTab_list_grid.getSelectedRowId()
                }, function (response) {
                    let dataResponse = JSON.parse(response);
                    dhtmlx.message({text: dataResponse.message, type: dataResponse.state, expire: 5000});
                    exportCustomerMappingTab_list_grid._displayGrid();
                });
            }
            break;
        case 'refresh':
            exportCustomerMappingTab_list_grid._displayGrid();
            break;
        case 'help':
            window.open('<?php echo getScExternalLink('support_export_customers'); ?>');
            break;
        case 'duplicate':
            $.post('index.php?ajax=1&act=cus_win-export_mapping_update', {
                action: 'duplicate_mapping',
                '<?php echo ExportCustomerMapping::$definition['primary']; ?>': exportCustomerMappingTab_list_grid.getSelectedRowId()
            }, function (response) {
                let dataResponse = JSON.parse(response);
                dhtmlx.message({text: dataResponse.message, type: dataResponse.state, expire: 5000});
                exportCustomerMappingTab_list_grid._displayGrid();
            });
            break;
        default:
    }
});

exportCustomerMappingTab_list_grid = exportCustomerMappingTab_list.attachGrid();
exportCustomerMappingTab_list_grid._name = 'grid';

exportCustomerMappingTab_list_grid._displayGrid = function () {
    exportCustomerMappingTab_list_grid.clearAll(true);
    $.post('index.php?ajax=1&act=cus_win-export_mapping_get', function (data) {
        exportCustomerMappingTab_list_grid.parse(data);
        exportCustomerMappingTab_list_grid.enableHeaderMenu();
    });
};

exportCustomerMappingTab_list_grid._setFormCellText = function () {
    let idxMappingName = exportCustomerMappingTab_list_grid.getColIndexById('name');
    let mappingName = exportCustomerMappingTab_list_grid.cells(exportCustomerMappingTab_list_grid._last_id_mapping,idxMappingName).getValue();
    exportCustomerMappingTab_configuration_layout.setText("<?php echo _l('Configure your template', true)._l(':'); ?> "+mappingName);
};

exportCustomerMappingTab_list_grid._last_id_mapping = null;
exportCustomerMappingTab_list_grid.attachEvent("onRowSelect", function (id_mapping) {
    if(exportCustomerMappingTab_list_grid._last_id_mapping !== id_mapping) {
        exportCustomerMappingTab_list_grid._last_id_mapping = id_mapping;
        exportCustomerMappingTab_list_grid._setFormCellText();
        exportCustomerMappingTab_configuration._loadFormData(id_mapping);
    }
});

exportCustomerMappingTab_list_grid.attachEvent("onEditCell",function(stage,rId,cInd,nValue,oValue){
    switch (stage)
    {
        case 2:
            if(nValue !== oValue) {
                $.post('index.php?ajax=1&act=cus_win-export_mapping_update', {
                    action: 'update_mapping',
                    '<?php echo ExportCustomerMapping::$definition['primary']; ?>': rId,
                    field: exportCustomerMappingTab_list_grid.getColumnId(cInd),
                    value: nValue
                },function (response) {
                    let dataResponse = JSON.parse(response);
                    dhtmlx.message({text: dataResponse.message, type: dataResponse.state, expire: 5000});
                    exportCustomerMappingTab_list_grid._setFormCellText();
                });
            }
            break;
        default:
    }
    return true;
});


/**
 * Mapping form
 */
var exportCustomerMappingTab_configuration_layout = exportCustomerMappingTab_layout.cells('b');
exportCustomerMappingTab_configuration_layout.setText("<?php echo _l('Select a template first', true); ?>");

exportCustomerMappingTab_configuration_layout._toolbar = exportCustomerMappingTab_configuration_layout.attachToolbar();
exportCustomerMappingTab_configuration_layout._toolbar.setIconset('awesome');
exportCustomerMappingTab_configuration_layout._toolbar.addButton("refresh", 1000, "", "fa fa-sync", "fa fa-sync");
exportCustomerMappingTab_configuration_layout._toolbar.setItemToolTip('refresh', '<?php echo _l('Refresh the configuration', true); ?>');
exportCustomerMappingTab_configuration_layout._toolbar.addButton("save", 1000, "", "fa fa-save", "fa fa-save");
exportCustomerMappingTab_configuration_layout._toolbar.setItemToolTip('save', '<?php echo _l('Save the configuration', true); ?>');

exportCustomerMappingTab_configuration_layout._toolbar.attachEvent("onClick", function (item) {
    let id_mapping = exportCustomerMappingTab_list_grid.getSelectedRowId();
    switch (item) {
        case 'refresh':
            if(id_mapping && confirm('<?php echo _l('Do you really want to refresh this form? You will loose all progression', true); ?>')) {
                exportCustomerMappingTab_configuration._loadFormData(id_mapping);
            }
            break;
        case 'save':
            if(id_mapping && confirm('<?php echo _l('Do you really want to save this form?', true); ?>'))
            {
                let formData = exportCustomerMappingTab_configuration.form.getFormData();
                formData.selected_fields = exportCustomerMappingTab_configuration._selected_fields_grid.getAllRowIds('__');
                $.post('index.php?ajax=1&act=cus_win-export_mapping_update', {
                    action: 'submit_mapping_form',
                    '<?php echo ExportCustomerMapping::$definition['primary']; ?>': id_mapping,
                    formData: formData
                },function (response) {
                    let dataResponse = JSON.parse(response);
                    dhtmlx.message({text: dataResponse.message, type: dataResponse.state, expire: 5000});
                });
            }
            break;
        default:
    }
});


<?php $exportCustomerMappingFormObject = new ExportCustomerMappingForm($sc_agent->id_lang); ?>
var exportCustomerMappingTab_configuration = {};
exportCustomerMappingTab_configuration.selectList = {
    'customerFields':<?php echo $exportCustomerMappingFormObject->getFieldArrayforJsSelectField('customerFields', true); ?>,
    'addressFields':<?php echo $exportCustomerMappingFormObject->getFieldArrayforJsSelectField('addressFields', true); ?>,
    'orderFields':<?php echo $exportCustomerMappingFormObject->getFieldArrayforJsSelectField('orderFields', true); ?>,
    'miscellaneousFields':<?php echo $exportCustomerMappingFormObject->getFieldArrayforJsSelectField('miscellaneousFields', true); ?>
};
exportCustomerMappingTab_configuration.field_labels = <?php echo $exportCustomerMappingFormObject->getAllFieldsLabelforJs(); ?>;
exportCustomerMappingTab_configuration.getFieldLabel = function (id_field) {
    let explodedField = id_field.split('|');
    let keyField = explodedField[1];
    return exportCustomerMappingTab_configuration.field_labels[keyField];
}

exportCustomerMappingTab_configuration.customTemplateTextWarning = function (name,label){
    return '<i>'+label+'</i>';
}
exportCustomerMappingTab_configuration.structure = [
    {
        type: "block",
        width:exportCustomerMappingTab_configuration_layout.getWidth()/2,
        list: [
            {type: "label", label: "<?php echo _l('Global configuration', true); ?>"},
            {
                type: "block",
                list: [
                    {
                        type: "select",
                        width: 90,
                        labelWidth: 200,
                        name: 'CSV_delimitor_choice',
                        label: "<?php echo _l('Delimitor'); ?>",
                        options: [
                            {value: ";", text: ";"},
                            {value: ",", text: ","},
                            {value: "|", text: "| pipe"},
                            {value: "tab", text: "Tabulation"}
                        ]
                    },
                    {
                        width: 42,
                        type: "btn2state",
                        labelWidth: 200,
                        name: 'CSV_display_header',
                        label: "<?php echo _l('Display header', true); ?>",
                        checked: false
                    },
                    {
                        type: "select",
                        width: 90,
                        labelWidth: 200,
                        name: 'separator',
                        label: "<?php echo _l('Separator', true); ?>",
                        options: [
                            {value: 1, text: "."},
                            {value: 2, text: ","}
                        ]
                    }
                ]
            },
            {type: "label", label: "<?php echo _l('Choose a new field to add', true); ?>"},
            {
                type: "block",
                list: [
                    {
                        type: "select",
                        width: 200,
                        labelWidth: 200,
                        name: 'customerFields',
                        label: "<?php echo _l('Customer fields', true); ?>",
                        options: exportCustomerMappingTab_configuration.selectList.customerFields
                    },
                    {type: "newcolumn"},
                    {type: "button", name: "customerFields_btn", value: "<?php echo _l('Add', true); ?>"}
                ]
            },
            {
                type: "block",
                list: [
                    {
                        type: "select",
                        width: 200,
                        labelWidth: 200,
                        name: 'addressFields',
                        label: "<?php echo _l('Address fields', true); ?>",
                        options: exportCustomerMappingTab_configuration.selectList.addressFields
                    },
                    {type: "newcolumn"},
                    {type: "button", name: "addressFields_btn", value: "<?php echo _l('Add', true); ?>"}
                ]
            },
            {
                type: "block",
                list: [
                    {
                        type: "select",
                        width: 200,
                        labelWidth: 200,
                        name: 'orderFields',
                        label: "<?php echo _l('Order fields', true); ?> (1)",
                        options: exportCustomerMappingTab_configuration.selectList.orderFields
                    },
                    {type: "newcolumn"},
                    {type: "button", name: "orderFields_btn", value: "<?php echo _l('Add', true); ?>"}
                ]
            },
            {
                type: "block",
                list: [
                    {
                        type: "select",
                        width: 200,
                        labelWidth: 200,
                        name: 'miscellaneousFields',
                        label: "<?php echo _l('Miscellaneous fields', true); ?>",
                        options: exportCustomerMappingTab_configuration.selectList.miscellaneousFields
                    },
                    {type: "newcolumn"},
                    {type: "button", name: "miscellaneousFields_btn", value: "<?php echo _l('Add', true); ?>"}
                ]
            },
            {
                type: "block",
                list: [
                    {
                        type: "template",
                        name: "b",
                        value: '(1) <?php echo _l('Warning : if you choose some order fields, you must select order status in customer list filters.', true); ?>',
                        format: exportCustomerMappingTab_configuration.customTemplateTextWarning
                    }
                ]
            }
        ]
    },
    {type: "newcolumn", offset:20},
    {type: "label", label: "<?php echo _l('Selected fields', true); ?>"},
    {
        type:"container",
        offsetLeft: 25,
        name: "selected_fields",
        inputWidth: 330,
        inputHeight: exportCustomerMappingTab_configuration_layout.getHeight()-150
    }
];
exportCustomerMappingTab_configuration.form = exportCustomerMappingTab_configuration_layout.attachForm(exportCustomerMappingTab_configuration.structure);
exportCustomerMappingTab_configuration.form.lock();

exportCustomerMappingTab_configuration._loadFormData = function(id_mapping){
    exportCustomerMappingTab_configuration.form.unlock();
    $.post('index.php?ajax=1&act=cus_win-export_mapping_form_data_get', {
        '<?php echo ExportCustomerMapping::$definition['primary']; ?>': id_mapping
    },function (response) {
        let dataResponse = JSON.parse(response);
        if(dataResponse.state==='success')
        {
            exportCustomerMappingTab_configuration._selected_fields_grid.clearAll();
            exportCustomerMappingTab_configuration.form.setItemValue('CSV_delimitor_choice', dataResponse.message.properties.delimitor);
            exportCustomerMappingTab_configuration.form.setItemValue('CSV_display_header', dataResponse.message.properties.display_header);
            exportCustomerMappingTab_configuration.form.setItemValue('separator', dataResponse.message.separator);
            for(const field of dataResponse.message.fields)
            {
                exportCustomerMappingTab_configuration._selected_fields_grid.addRow(field,exportCustomerMappingTab_configuration.getFieldLabel(field));
            }

        } else {
            dhtmlx.message({text: dataResponse.message, type: dataResponse.state, expire: 5000});
        }
    });
};

// button add
exportCustomerMappingTab_configuration.form.attachEvent("onButtonClick", function (name) {
    let explodedName = name.split('_');
    let selectFieldName = explodedName[0];
    let selectFieldValue = exportCustomerMappingTab_configuration.form.getItemValue(selectFieldName);
    exportCustomerMappingTab_configuration._selected_fields_grid.addRow(selectFieldValue,exportCustomerMappingTab_configuration.getFieldLabel(selectFieldValue));
});

/**
 *
 * Mapping form grid selected field
 */
exportCustomerMappingTab_configuration._selected_fields_layout = new dhtmlXLayoutObject(exportCustomerMappingTab_configuration.form.getContainer("selected_fields"), "1C");
exportCustomerMappingTab_configuration._selected_fields_layout_cell = exportCustomerMappingTab_configuration._selected_fields_layout.cells('a');
exportCustomerMappingTab_configuration._selected_fields_layout_cell.hideHeader();
exportCustomerMappingTab_configuration._selected_fields_toolbar = exportCustomerMappingTab_configuration._selected_fields_layout.attachToolbar();
exportCustomerMappingTab_configuration._selected_fields_toolbar.setIconset('awesome');
exportCustomerMappingTab_configuration._selected_fields_toolbar.addButton("remove", 1000, "", "fa fa-minus-circle", "fa fa-minus-circle");
exportCustomerMappingTab_configuration._selected_fields_toolbar.setItemToolTip('remove', '<?php echo _l('Remove field', true); ?>');

exportCustomerMappingTab_configuration._selected_fields_toolbar.attachEvent("onClick", function (item) {
    switch (item) {
        case 'remove':
            exportCustomerMappingTab_configuration._selected_fields_grid.deleteSelectedRows();
            break;
        default:
    }
});

exportCustomerMappingTab_configuration._selected_fields_grid = exportCustomerMappingTab_configuration._selected_fields_layout_cell.attachGrid();
exportCustomerMappingTab_configuration._selected_fields_grid.enableMultiselect(true);
exportCustomerMappingTab_configuration._selected_fields_grid.enableDragAndDrop(true);
exportCustomerMappingTab_configuration._selected_fields_grid.setHeader("<?php echo _l('Fields name', true); ?>");
exportCustomerMappingTab_configuration._selected_fields_grid.setInitWidths("*");
exportCustomerMappingTab_configuration._selected_fields_grid.setColAlign("left");
exportCustomerMappingTab_configuration._selected_fields_grid.setColTypes("ro");
exportCustomerMappingTab_configuration._selected_fields_grid.setColSorting("str");
exportCustomerMappingTab_configuration._selected_fields_grid.init();



