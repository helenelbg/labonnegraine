<?php
if (!defined('STORE_COMMANDER')) { exit; }
?>
/**
 * Init
 */
var exportOrderMappingTab = exportOrdersTabbar.tabs('mapping');
var exportOrderMappingTab_layout = exportOrderMappingTab.attachLayout("2U");

/**
 * Mapping list
 */
var exportOrderMappingTab_list = exportOrderMappingTab_layout.cells('a');
exportOrderMappingTab_list.setText('<?php echo _l('Template list', true); ?>');
exportOrderMappingTab_list.setWidth(400);

var exportOrderMappingTab_list_toolbar = exportOrderMappingTab_list.attachToolbar();
exportOrderMappingTab_list_toolbar.setIconset('awesome');
exportOrderMappingTab_list_toolbar.addButton("refresh", 1000, "", "fa fa-sync green", "fa fa-sync green");
exportOrderMappingTab_list_toolbar.setItemToolTip('refresh', '<?php echo _l('Refresh grid', true); ?>');
exportOrderMappingTab_list_toolbar.addButton("add", 1000, "", "fa fa-plus-circle", "fa fa-plus-circle");
exportOrderMappingTab_list_toolbar.setItemToolTip('add', '<?php echo _l('Add a template', true); ?>');
exportOrderMappingTab_list_toolbar.addButton("delete", 1000, "", "fa fa-minus-circle", "fa fa-minus-circle");
exportOrderMappingTab_list_toolbar.setItemToolTip('delete', '<?php echo _l('Delete selected template', true); ?>');
exportOrderMappingTab_list_toolbar.addButton("duplicate", 1000, "", "fad fa-copy", "fad fa-copy");
exportOrderMappingTab_list_toolbar.setItemToolTip('duplicate', '<?php echo _l('Duplicate selected template', true); ?>');
exportOrderMappingTab_list_toolbar.addButton("help", 1000, "", "fad fa-question-circle blue", "fad fa-question-circle blue");
exportOrderMappingTab_list_toolbar.setItemToolTip('help', '<?php echo _l('Help', true); ?>');

exportOrderMappingTab_list_toolbar.attachEvent("onClick", function (item) {
    switch (item) {
        case 'add':
            $.post('index.php?ajax=1&act=ord_win-export_mapping_update', {
                action: 'add_mapping'
            },function (response) {
                let dataResponse = JSON.parse(response);
                dhtmlx.message({text: dataResponse.message, type: dataResponse.state, expire: 5000});
                exportOrderMappingTab_list_grid._displayGrid();
            });
            break;
        case 'delete':
            if(exportOrderMappingTab_list_grid.getSelectedRowId() && confirm('<?php echo _l('Do you really want to delete this template?', true); ?>')) {
                $.post('index.php?ajax=1&act=ord_win-export_mapping_update', {
                    action: 'delete_mapping',
                    '<?php echo ExportOrderMapping::$definition['primary']; ?>': exportOrderMappingTab_list_grid.getSelectedRowId()
                }, function (response) {
                    let dataResponse = JSON.parse(response);
                    dhtmlx.message({text: dataResponse.message, type: dataResponse.state, expire: 5000});
                    exportOrderMappingTab_list_grid._displayGrid();
                });
            }
            break;
        case 'refresh':
            exportOrderMappingTab_list_grid._displayGrid();
            break;
        case 'help':
            window.open('<?php echo getScExternalLink('support_export_orders'); ?>');
            break;
        case 'duplicate':
            $.post('index.php?ajax=1&act=ord_win-export_mapping_update', {
                action: 'duplicate_mapping',
                '<?php echo ExportOrderMapping::$definition['primary']; ?>': exportOrderMappingTab_list_grid.getSelectedRowId()
            }, function (response) {
                let dataResponse = JSON.parse(response);
                dhtmlx.message({text: dataResponse.message, type: dataResponse.state, expire: 5000});
                exportOrderMappingTab_list_grid._displayGrid();
            });
            break;
        default:
    }
});

exportOrderMappingTab_list_grid = exportOrderMappingTab_list.attachGrid();
exportOrderMappingTab_list_grid._name = 'grid';

exportOrderMappingTab_list_grid._displayGrid = function () {
    exportOrderMappingTab_list_grid.clearAll(true);
    $.post('index.php?ajax=1&act=ord_win-export_mapping_get', function (data) {
        exportOrderMappingTab_list_grid.parse(data);
        exportOrderMappingTab_list_grid.enableHeaderMenu();
    });
};

exportOrderMappingTab_list_grid._setFormCellText = function () {
    let idxMappingName = exportOrderMappingTab_list_grid.getColIndexById('name');
    let mappingName = exportOrderMappingTab_list_grid.cells(exportOrderMappingTab_list_grid._last_id_mapping,idxMappingName).getValue();
    exportOrderMappingTab_configuration_layout.setText("<?php echo _l('Configure your template', true)._l(':'); ?> "+mappingName);
};

exportOrderMappingTab_list_grid._last_id_mapping = null;
exportOrderMappingTab_list_grid.attachEvent("onRowSelect", function (id_mapping) {
    if(exportOrderMappingTab_list_grid._last_id_mapping !== id_mapping) {
        exportOrderMappingTab_list_grid._last_id_mapping = id_mapping;
        exportOrderMappingTab_list_grid._setFormCellText();
        exportOrderMappingTab_configuration._loadFormData(id_mapping);
    }
});

exportOrderMappingTab_list_grid.attachEvent("onEditCell",function(stage,rId,cInd,nValue,oValue){
    switch (stage)
    {
        case 2:
            if(nValue !== oValue) {
                $.post('index.php?ajax=1&act=ord_win-export_mapping_update', {
                    action: 'update_mapping',
                    '<?php echo ExportOrderMapping::$definition['primary']; ?>': rId,
                    field: exportOrderMappingTab_list_grid.getColumnId(cInd),
                    value: nValue
                },function (response) {
                    let dataResponse = JSON.parse(response);
                    dhtmlx.message({text: dataResponse.message, type: dataResponse.state, expire: 5000});
                    exportOrderMappingTab_list_grid._setFormCellText();
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
var exportOrderMappingTab_configuration_layout = exportOrderMappingTab_layout.cells('b');
exportOrderMappingTab_configuration_layout.setText("<?php echo _l('Select a template first', true); ?>");

exportOrderMappingTab_configuration_layout._toolbar = exportOrderMappingTab_configuration_layout.attachToolbar();
exportOrderMappingTab_configuration_layout._toolbar.setIconset('awesome');
exportOrderMappingTab_configuration_layout._toolbar.addButton("refresh", 1000, "", "fa fa-sync", "fa fa-sync");
exportOrderMappingTab_configuration_layout._toolbar.setItemToolTip('refresh', '<?php echo _l('Refresh the configuration', true); ?>');
exportOrderMappingTab_configuration_layout._toolbar.addButton("save", 1000, "", "fa fa-save", "fa fa-save");
exportOrderMappingTab_configuration_layout._toolbar.setItemToolTip('save', '<?php echo _l('Save the configuration', true); ?>');

exportOrderMappingTab_configuration_layout._toolbar.attachEvent("onClick", function (item) {
    let id_mapping = exportOrderMappingTab_list_grid.getSelectedRowId();
    switch (item) {
        case 'refresh':
            if(id_mapping && confirm('<?php echo _l('Do you really want to refresh this form? You will loose all progression', true); ?>')) {
                exportOrderMappingTab_configuration._loadFormData(id_mapping);
            }
            break;
        case 'save':
            if(id_mapping && confirm('<?php echo _l('Do you really want to save this form?', true); ?>'))
            {
                let formData = exportOrderMappingTab_configuration.form.getFormData();
                formData.selected_fields = exportOrderMappingTab_configuration._selected_fields_grid.getAllRowIds('__');
                $.post('index.php?ajax=1&act=ord_win-export_mapping_update', {
                    action: 'submit_mapping_form',
                    '<?php echo ExportOrderMapping::$definition['primary']; ?>': id_mapping,
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


<?php $exportOrderMappingFormObject = new ExportOrderMappingForm($sc_agent->id_lang); ?>
var exportOrderMappingTab_configuration = {};
exportOrderMappingTab_configuration.selectList = {
    'orderFields':<?php echo $exportOrderMappingFormObject->getFieldArrayforJsSelectField('orderFields', true); ?>,
    'orderTotalFields':<?php echo $exportOrderMappingFormObject->getFieldArrayforJsSelectField('orderTotalFields', true); ?>,
    'orderDetailFields':<?php echo $exportOrderMappingFormObject->getFieldArrayforJsSelectField('orderDetailFields', true); ?>,
    'customerFields':<?php echo $exportOrderMappingFormObject->getFieldArrayforJsSelectField('customerFields', true); ?>,
    'addressDeliveryFields':<?php echo $exportOrderMappingFormObject->getFieldArrayforJsSelectField('addressDeliveryFields', true); ?>,
    'addressInvoiceFields':<?php echo $exportOrderMappingFormObject->getFieldArrayforJsSelectField('addressInvoiceFields', true); ?>,
    'calculatedFields':<?php echo $exportOrderMappingFormObject->getFieldArrayforJsSelectField('calculatedFields', true); ?>
};
exportOrderMappingTab_configuration.field_labels = <?php echo $exportOrderMappingFormObject->getAllFieldsLabelforJs(); ?>;
exportOrderMappingTab_configuration.getFieldLabel = function (id_field) {
    let explodedField = id_field.split('|');
    let keyField = explodedField[1];
    return exportOrderMappingTab_configuration.field_labels[keyField];
}
//function templateTextWarning(name,label){
//    return '<i>'+label+'</i>';
//}
exportOrderMappingTab_configuration.customTemplateTextWarning = function (name,label){
    return '<i>'+label+'</i>';
}
exportOrderMappingTab_configuration.structure = [
    {
        type: "block",
        width:exportOrderMappingTab_configuration_layout.getWidth()/2,
        list: [
            {type: "label", label: "<?php echo _l('Global configuration', true); ?>"},
            {
                type: "block",
                list: [
                    {
                        type: "select",
                        width: 70,
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
                        width: 70,
                        labelWidth: 200,
                        name: 'separator',
                        label: "<?php echo _l('Separator', true); ?>",
                        options: [
                            {value: 1, text: "."},
                            {value: 2, text: ","}
                        ]
                    },
                    {
                        width: 42,
                        type: "btn2state",
                        labelWidth: 200,
                        name: 'CSV_display_breakdown_shipping',
                        label: "<?php echo _l('Taxes breakdown - display shippings row', true); ?>",
                        checked: false
                    },
                    {
                        width: 42,
                        type: "btn2state",
                        labelWidth: 200,
                        name: 'CSV_display_breakdown_discounts',
                        label: "<?php echo _l('Taxes breakdown - display discounts row', true); ?>",
                        checked: false
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
                        name: 'orderFields',
                        label: "<?php echo _l('Orders', true); ?>",
                        options: exportOrderMappingTab_configuration.selectList.orderFields
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
                        name: 'orderTotalFields',
                        label: "<?php echo _l('Amounts', true); ?>",
                        options: exportOrderMappingTab_configuration.selectList.orderTotalFields
                    },
                    {type: "newcolumn"},
                    {type: "button", name: "orderTotalFields_btn", value: "<?php echo _l('Add', true); ?>"}
                ]
            },
            {
                type: "block",
                list: [
                    {
                        type: "select",
                        width: 200,
                        labelWidth: 200,
                        name: 'orderDetailFields',
                        label: "<?php echo _l('Order details', true); ?> (1)",
                        options: exportOrderMappingTab_configuration.selectList.orderDetailFields
                    },
                    {type: "newcolumn"},
                    {type: "button", name: "orderDetailFields_btn", value: "<?php echo _l('Add', true); ?>"}
                ]
            },
            {
                type: "block",
                list: [
                    {
                        type: "select",
                        width: 200,
                        labelWidth: 200,
                        name: 'customerFields',
                        label: "<?php echo _l('Customers', true); ?> (2)",
                        options: exportOrderMappingTab_configuration.selectList.customerFields
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
                        name: 'addressDeliveryFields',
                        label: "<?php echo _l('Delivery address', true); ?> (2)",
                        options: exportOrderMappingTab_configuration.selectList.addressDeliveryFields
                    },
                    {type: "newcolumn"},
                    {type: "button", name: "addressDeliveryFields_btn", value: "<?php echo _l('Add', true); ?>"}
                ]
            },
            {
                type: "block",
                list: [
                    {
                        type: "select",
                        width: 200,
                        labelWidth: 200,
                        name: 'addressInvoiceFields',
                        label: "<?php echo _l('Invoice address', true); ?> (2)",
                        options: exportOrderMappingTab_configuration.selectList.addressInvoiceFields
                    },
                    {type: "newcolumn"},
                    {type: "button", name: "addressInvoiceFields_btn", value: "<?php echo _l('Add', true); ?>"}
                ]
            },
            {
                type: "block",
                list: [
                    {
                        type: "select",
                        width: 200,
                        labelWidth: 200,
                        name: 'calculatedFields',
                        label: "<?php echo _l('Calculated fields', true); ?> (3)",
                        options: exportOrderMappingTab_configuration.selectList.calculatedFields
                    },
                    {type: "newcolumn"},
                    {type: "button", name: "calculatedFields_btn", value: "<?php echo _l('Add', true); ?>"}
                ]
            },
            {
                type: "block",
                list: [
                    {
                        type: "template",
                        name: "b",
                        value: '(1) <?php echo _l('Warning : if an order contains more than one product, by choosing one of the "Order details" fields, it will be exported as one line per product', true); ?>',
                        format: exportOrderMappingTab_configuration.customTemplateTextWarning
                    },
                    {
                        type: "template",
                        name: "a",
                        value: '(2) <?php echo _l('Warning : if you choose some order details and customer group, it may exports many lines duplicated. For example, an order with 3 products with a customer in 2 groups, will be exported as 2x3=6 lines.', true); ?>',
                        format: exportOrderMappingTab_configuration.customTemplateTextWarning
                    },
                    {
                        type: "template",
                        name: "a",
                        value: '(3) <?php echo _l('Warning : if an order contains more than one product, by choosing "Taxes breakdown" field, it will be exported as one line per product', true); ?>',
                        format: exportOrderMappingTab_configuration.customTemplateTextWarning
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
        inputHeight: exportOrderMappingTab_configuration_layout.getHeight()-150
    }
];
exportOrderMappingTab_configuration.form = exportOrderMappingTab_configuration_layout.attachForm(exportOrderMappingTab_configuration.structure);
exportOrderMappingTab_configuration.form.lock();

exportOrderMappingTab_configuration._loadFormData = function(id_mapping){
    exportOrderMappingTab_configuration.form.unlock();
    $.post('index.php?ajax=1&act=ord_win-export_mapping_form_data_get', {
        '<?php echo ExportOrderMapping::$definition['primary']; ?>': id_mapping
    },function (response) {
        let dataResponse = JSON.parse(response);
        if(dataResponse.state==='success')
        {
            exportOrderMappingTab_configuration._selected_fields_grid.clearAll();
            exportOrderMappingTab_configuration.form.setItemValue('CSV_delimitor_choice', dataResponse.message.properties.delimitor);
            exportOrderMappingTab_configuration.form.setItemValue('CSV_display_header', dataResponse.message.properties.display_header);
            exportOrderMappingTab_configuration.form.setItemValue('separator', dataResponse.message.separator);
            exportOrderMappingTab_configuration.form.setItemValue('CSV_display_breakdown_shipping', dataResponse.message.properties.display_breakdown_shipping);
            exportOrderMappingTab_configuration.form.setItemValue('CSV_display_breakdown_discounts', dataResponse.message.properties.display_breakdown_discounts);
            for(const field of dataResponse.message.fields)
            {
                exportOrderMappingTab_configuration._selected_fields_grid.addRow(field,exportOrderMappingTab_configuration.getFieldLabel(field));
            }

        } else {
            dhtmlx.message({text: dataResponse.message, type: dataResponse.state, expire: 5000});
        }
    });
};

// button add
exportOrderMappingTab_configuration.form.attachEvent("onButtonClick", function (name) {
    let explodedName = name.split('_');
    let selectFieldName = explodedName[0];
    let selectFieldValue = exportOrderMappingTab_configuration.form.getItemValue(selectFieldName);
    exportOrderMappingTab_configuration._selected_fields_grid.addRow(selectFieldValue,exportOrderMappingTab_configuration.getFieldLabel(selectFieldValue));
});

/**
 *
 * Mapping form grid selected field
 */
exportOrderMappingTab_configuration._selected_fields_layout = new dhtmlXLayoutObject(exportOrderMappingTab_configuration.form.getContainer("selected_fields"), "1C");
exportOrderMappingTab_configuration._selected_fields_layout_cell = exportOrderMappingTab_configuration._selected_fields_layout.cells('a');
exportOrderMappingTab_configuration._selected_fields_layout_cell.hideHeader();
exportOrderMappingTab_configuration._selected_fields_toolbar = exportOrderMappingTab_configuration._selected_fields_layout.attachToolbar();
exportOrderMappingTab_configuration._selected_fields_toolbar.setIconset('awesome');
exportOrderMappingTab_configuration._selected_fields_toolbar.addButton("remove", 1000, "", "fa fa-minus-circle", "fa fa-minus-circle");
exportOrderMappingTab_configuration._selected_fields_toolbar.setItemToolTip('remove', '<?php echo _l('Remove field', true); ?>');

exportOrderMappingTab_configuration._selected_fields_toolbar.attachEvent("onClick", function (item) {
    switch (item) {
        case 'remove':
            exportOrderMappingTab_configuration._selected_fields_grid.deleteSelectedRows();
            break;
        default:
    }
});

exportOrderMappingTab_configuration._selected_fields_grid = exportOrderMappingTab_configuration._selected_fields_layout_cell.attachGrid();
exportOrderMappingTab_configuration._selected_fields_grid.enableMultiselect(true);
exportOrderMappingTab_configuration._selected_fields_grid.enableDragAndDrop(true);
exportOrderMappingTab_configuration._selected_fields_grid.setHeader("<?php echo _l('Fields name', true); ?>");
exportOrderMappingTab_configuration._selected_fields_grid.setInitWidths("*");
exportOrderMappingTab_configuration._selected_fields_grid.setColAlign("left");
exportOrderMappingTab_configuration._selected_fields_grid.setColTypes("ro");
exportOrderMappingTab_configuration._selected_fields_grid.setColSorting("str");
exportOrderMappingTab_configuration._selected_fields_grid.init();



