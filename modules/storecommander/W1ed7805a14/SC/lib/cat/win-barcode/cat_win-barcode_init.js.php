<?php if (_r('ACT_CAT_BARCODE')) { ?>
<?php echo '<script type="text/javascript">'; ?>
    // INITIALIZE LAYOUT
    dhxlBarCodeImporter_layout = wBarCodeImporter.attachLayout("1C");
    dhxlBarCodeImporter_layout.cells('a').hideHeader();

    const operatorAdd = '+';
    const operatorRemove = '-';
    const operatorReplace = '==';

    // FORM
    let window_dimensions = dhxWins.window('wBarCodeImporter').getDimension();
    let input_width = 60;
    let label_width = 140;
    let form = [
        {
            type: "settings",
            labelWidth: label_width
        },
        {
            type: "block", width: window_dimensions[0] - 100, list: [
                {
                    type: "block", width: 350, list: [
                        {type: "label", label: "<?php echo _l('Action'); ?>"},
                        {
                            type: "radio",
                            name: "action",
                            checked: false,
                            value: "stock_add",
                            label: "<?php echo _l('Add to stock'); ?>",
                            labelAlign: "left",
                            position: "label-right",
                            list: [
                                {
                                    type: "input",
                                    name: "stock_add",
                                    numberFormat: "0",
                                    value: 1,
                                    position: "absolute",
                                    inputTop: -25,
                                    inputLeft: label_width,
                                    inputWidth: input_width
                                },
                            ]
                        },
                        {
                            type: "radio",
                            name: "action",
                            value: "stock_remove",
                            label: "<?php echo _l('Remove stock'); ?>",
                            labelAlign: "left",
                            position: "label-right",
                            list: [
                                {
                                    type: "input",
                                    name: "stock_remove",
                                    numberFormat: "0",
                                    value: 1,
                                    position: "absolute",
                                    inputTop: -25,
                                    inputLeft: label_width,
                                    inputWidth: input_width
                                },
                            ]
                        },
                        {
                            type: "radio",
                            name: "action",
                            value: "stock_replace",
                            label: "<?php echo _l('Replace stock'); ?>",
                            labelAlign: "left",
                            position: "label-right",
                            list: [
                                {
                                    type: "input",
                                    name: "stock_replace",
                                    numberFormat: "0",
                                    value: 1,
                                    position: "absolute",
                                    inputTop: -25,
                                    inputLeft: label_width,
                                    inputWidth: input_width
                                },
                            ]
                        },
                        {
                            type: "radio",
                            name: "action",
                            value: "open_sc",
                            label: "<?php echo _l('Open in Sc'); ?>",
                            labelAlign: "left",
                            position: "label-right",
                            list: [
                                {
                                    type: "select",
                                    position: "absolute",
                                    name: "open_sc",
                                    inputTop: -25,
                                    inputLeft: label_width,
                                    inputWidth: 150,
                                    options: [
                                        {value: "_self", text: "<?php echo _l('in background'); ?>", selected: true},
                                        {value: "_blank", text: "<?php echo _l('in new tab'); ?>"},
                                    ]
                                },
                            ]
                        },
                        {
                            type: "checkbox",
                            name: "auto_import",
                            label: "<?php echo _l('Automatically validate changes'); ?>",
                            labelWidth: 230,
                            position: "label-right",
                        },
                    ]
                },
                {type: "newcolumn"},
                {
                    type: "block", list: [
                        {
                            type: "label",
                            label: "<?php echo _l('Barcode'); ?>"
                        },
                        {
                            type: "input",
                            name: "code",
                            label: "<?php echo _l('Code'); ?>",
                            labelWidth: 80,
                            info: true,
                            tooltip: "<?php echo _l('Please select an action before'); ?>",
                            disabled: true,
                            offsetLeft: 25,
                            inputWidth: 250
                        },
                    ]
                },
                {type: "newcolumn"},
                {
                    type: "container",
                    name: "for_grid",
                    inputWidth: window_dimensions[0] - 100,
                    inputHeight: window_dimensions[1] - 350
                }
            ]
        }
    ];
    bc_importer_form = dhxlBarCodeImporter_layout.cells('a').attachForm(form);
    let bc_layout = new dhtmlXLayoutObject(bc_importer_form.getContainer("for_grid"), "1C");
    let bc_layout_panel = bc_layout.cells('a');
    bc_layout_panel.hideHeader();

    //TOOLBAR
    var bc_layout_tb = bc_layout_panel.attachToolbar();
      bc_layout_tb.setIconset('awesome');
    bc_layout_tb.addButton("validate_all", 0, "", "fa fa-check-circle green", "fa fa-check-circle green");
    bc_layout_tb.setItemToolTip('validate_all', '<?php echo _l('Validate all'); ?>');
    bc_layout_tb.addButton("delete_all", 5, "", "fa fa-minus-circle red", "fa fa-minus-circle red");
    bc_layout_tb.setItemToolTip('delete_all', '<?php echo _l('Delete all'); ?>');
    bc_layout_tb.addButton("go_sc", 10, "", "fad fa-file-search blue", "fad fa-file-search blue");
    bc_layout_tb.setItemToolTip('go_sc', '<?php echo _l('See in StoreCommander'); ?>');
    bc_layout_tb.addButton("exportcsv", 15, "", "fad fa-file-csv green", "fad fa-file-csv green");
    bc_layout_tb.setItemToolTip('exportcsv','<?php echo _l('Export grid to clipboard in CSV format for MSExcel with tab delimiter.'); ?>');
    bc_layout_tb.addButton("help", 20, "", "fad fa-question-circle blue", "fad fa-question-circle blue");
    bc_layout_tb.setItemToolTip('help','<?php echo _l('Help'); ?>');

    //GRID
    var bc_layout_grid = bc_layout_panel.attachGrid();
    bc_layout_grid.enableMultiselect(true);
    let bc_layout_grid_config = {
        'id_list': [
            'id_product',
            'id_product_attribute',
            'ean13',
            'name',
            'stock_before',
            'modification',
            'stock_after',
            'validate',
            'delete',
        ],
        'header': [
            "<?php echo _l('ID prod.'); ?>",
            "<?php echo _l('ID combi.'); ?>",
            "<?php echo _l('EAN'); ?>",
            "<?php echo _l('Name'); ?>",
            "<?php echo _l('Stock before'); ?>",
            "<?php echo _l('Modification'); ?>",
            "<?php echo _l('Stock after'); ?>",
            "<?php echo _l('Validate'); ?>",
            "<?php echo _l('Delete'); ?>",
        ],
        "width": [
            50,
            50,
            100,
            200,
            80,
            80,
            80,
            "*",
            "*"
        ],
        "align": [
            "center",
            "center",
            "left",
            "left",
            "center",
            "center",
            "center",
            "center",
            "center",
        ],
        "type": [
            "ro",
            "ro",
            "ro",
            "ro",
            "ro",
            "ed",
            "ro",
            "actionValidate",
            "actionDelete"
        ],
        "sort": [
            "int",
            "int",
            "str",
            "str",
            "int",
            "str",
            "int",
            "na",
            "na",
        ]
    };

    bc_layout_grid.setHeader(bc_layout_grid_config['header'].join(','));
    bc_layout_grid.setColumnIds(bc_layout_grid_config['id_list'].join(','));
    bc_layout_grid.setInitWidths(bc_layout_grid_config['width'].join(','));
    bc_layout_grid.setColAlign(bc_layout_grid_config['align'].join(','));
    bc_layout_grid.setColTypes(bc_layout_grid_config['type'].join(','));
    bc_layout_grid.setColSorting(bc_layout_grid_config['sort'].join(','));
    bc_layout_grid.init();

    // EVENTS
    var action_selected = null;
    var auto_import = 0;
    var barcode = null;

    // form
    bc_importer_form.attachEvent("onChange", function (id) {
        switch (id) {
            case "action":
                action_selected = bc_importer_form.getItemValue(id, true);
                if(action_selected == 'stock_replace') {
                    bc_importer_form.disableItem('auto_import');
                } else {
                    bc_importer_form.enableItem('auto_import');
                }
                bc_importer_form.enableItem('code');
                break;
            case "auto_import":
                auto_import = bc_importer_form.getItemValue(id, true);
                bc_importer_form.enableItem('code');
                break;
        }
    });

    onInputChangeWithDelay("code");

    // toolbar
    bc_layout_tb.attachEvent("onClick", function (id) {
        switch (id) {
            case "validate_all":
                processValidate('all');
                break;
            case "delete_all":
                processDelete('all');
                break;
            case "go_sc":
                let all_ids = bc_layout_grid.getSelectedRowId();
                if (all_ids == null) {
                    if (confirm('<?php echo _l('No row selected. Do you really want to open all product rows in new browser tabs?', 1); ?>')) {
                        all_ids = bc_layout_grid.getAllRowIds();
                    } else {
                        break;
                    }
                }
                all_ids = all_ids.split(',');
                all_ids = all_ids.slice(0, 9);
                all_ids.forEach(function (row_id) {
                    let tmp = row_id.split('_');
                    let id_product = tmp[0];
                    let id_category = tmp[2];
                    let product_url = "?page=cat_tree&open_cat_grid=" + id_category + "-" + id_product;
                    window.open(product_url, "_blank");
                });
                break;
            case "exportcsv":
                let idxValidate = bc_layout_grid.getColIndexById('validate');
                let idxDelete = bc_layout_grid.getColIndexById('delete');
                bc_layout_grid.setColumnHidden(idxValidate,true);
                bc_layout_grid.setColumnHidden(idxDelete,true);
                displayQuickExportWindow(bc_layout_grid,1);
                bc_layout_grid.setColumnHidden(idxValidate,false);
                bc_layout_grid.setColumnHidden(idxDelete,false);
                break;
            case "help":
                    <?php echo "window.open('".getScExternalLink('support_barcode')."');"; ?>
                break
        }
    });

    //grid
    bc_layout_grid.attachEvent("onEditCell", function (stage, rId, cInd, nValue, oValue) {
        let idxModification = bc_layout_grid.getColIndexById('modification');
        if (stage === 2 && cInd === idxModification) {
            if (nValue !== oValue && nValue !== '') {
                updateGrid(nValue, rId);
                return true;
            }
        }
    });

    // PROCESS
    function initializeProcess(action, code, auto = null) {
        if (action != null && code != null && code != "") {
            var process_value = bc_importer_form.getItemValue(action, true);
            var config = {
                'action': action,
                'code': code,
                'process_value': process_value,
            };
            if (auto !== null && auto !== 0) {
                switch (action) {
                    case 'stock_add':
                        config['process_value'] = operatorAdd + process_value;
                        processValidate('ref', config);
                        break;
                    case 'stock_remove':
                        config['process_value'] = operatorRemove+' ' + process_value;
                        break;
                    case 'open_sc':
                        $.post("index.php?ajax=1&act=cat_win-barcode_get", {
                            'id_lang': SC_ID_LANG,
                            'config': config
                        }, function (data) {
                            if (data !== '' && data !== undefined) {
                                let new_row = JSON.parse(data);
                                if (Object.keys(new_row).length > 0) {
                                    let open_type = bc_importer_form.getItemValue(action, true);
                                    let id_category = new_row['id_category_default'];
                                    let id_product = new_row['id_product'];
                                    if (open_type === '_blank') {
                                        let product_url = "?page=cat_tree&open_cat_grid=" + id_category + "-" + id_product;
                                        window.open(product_url, open_type);
                                    } else {
                                        cat_tree.selectItem(id_category, true);
                                        cat_grid.attachEvent("onDataReady", function () {
                                            cat_grid.selectRowById(id_product, true, true, true);
                                        });
                                    }
                                }
                            }
                        });
                        break;
                }
            } else {
                $.post("index.php?ajax=1&act=cat_win-barcode_get", {
                    'id_lang': SC_ID_LANG,
                    'config': config
                }, function (data) {
                    if (data !== '' && data !== undefined) {
                        let new_row = JSON.parse(data);
                        var new_row_id = new_row['id'];
                        if (Object.keys(new_row).length > 0) {
                            switch (action) {
                                case 'open_sc':
                                    let open_type = bc_importer_form.getItemValue(action, true);
                                    let id_category = new_row['id_category_default'];
                                    let id_product = new_row['id_product'];
                                    if (open_type === '_blank') {
                                        let product_url = "?page=cat_tree&open_cat_grid=" + id_category + "-" + id_product;
                                        window.open(product_url, open_type);
                                    } else {
                                        cat_tree.selectItem(id_category, true);
                                        cat_grid.attachEvent("onDataReady", function () {
                                            cat_grid.selectRowById(id_product, true, true, true);
                                        });
                                    }
                                    break;
                                default:
                                    let all_ids = bc_layout_grid.getAllRowIds();
                                    all_ids.split(',');
                                    let idx = all_ids.indexOf(new_row['id']);
                                    if (idx === -1) {
                                        let final_row = [];
                                        bc_layout_grid_config['id_list'].forEach(function (input_name) {
                                            final_row.push(new_row[input_name]);
                                        });
                                        bc_layout_grid.addRow(new_row_id, final_row);
                                    } else {
                                        // row already exist
                                        switch(action) {
                                            case 'stock_add':
                                                updateGrid(operatorAdd+' ' + process_value, new_row_id, 1);
                                                break;
                                            case 'stock_replace':
                                                updateGrid(operatorReplace+' ' + process_value, new_row_id, 1);
                                                break;
                                            default:
                                                updateGrid(operatorRemove+' ' + process_value, new_row_id, 1);
                                        }
                                    }
                            }
                        }
                    }
                });
            }
            bc_importer_form.setItemValue('code', '');

        }
    }

    function onInputChangeWithDelay(name,delay = 1500){
        let inputTarget = bc_importer_form.getInput(name);
        $(inputTarget).on('input',setDelay(function () {
            barcode = inputTarget.value;
            initializeProcess(action_selected, barcode, auto_import);
        },delay));
    }

    function setDelay(callback, ms) {
        var timer = 0;
        return function() {
            var context = this, args = arguments;
            clearTimeout(timer);
            timer = setTimeout(function () {
                callback.apply(context, args);
            }, ms || 0);
        };
    }

    // FUNCTION
    function updateGrid(nValue, rId, updateInRowGrid = null) {
        let idxModification = bc_layout_grid.getColIndexById('modification');
        let idxStockAfter = bc_layout_grid.getColIndexById('stock_after');
        let idxStockBefore = bc_layout_grid.getColIndexById('stock_before');
        let stockBeforeVal = bc_layout_grid.cells(rId, idxStockBefore).getValue();

        nValue = getValueOperatorCurrentValue(nValue);

        let operator = nValue['operator'];
        nValue = nValue['value'];
        let addition = 1;
        if (operator === operatorRemove) {
            addition = 0;
        }

        // modification in the row
        if (updateInRowGrid !== null) {
            if(operator === operatorReplace) {
                bc_layout_grid.cells(rId, idxStockAfter).setValue(nValue);
                bc_layout_grid.cells(rId, idxModification).setValue(operator + ' ' + nValue);
                return true;
            } else {
                let modificationVal = bc_layout_grid.cells(rId, idxModification).getValue();
                modificationVal = getValueOperatorCurrentValue(modificationVal);
                if (modificationVal['operator'] !== operator) {
                    // if col operator != value operator
                    switch (modificationVal['operator']) {
                        case operatorAdd:
                            nValue = bc_importer_form.getItemValue('stock_remove', true);
                            break;
                        case operatorRemove:
                            nValue = bc_importer_form.getItemValue('stock_add', true);
                    }
                } else {
                    switch (operator) {
                        case operatorReplace:
                            bc_layout_grid.cells(rId, idxStockAfter).setValue(nValue);
                            bc_layout_grid.cells(rId, idxModification).setValue(operator + ' ' + nValue);
                            return true;
                        default:
                            nValue = Number(nValue) + Number(modificationVal['value']);
                    }
                }
            }
        }

        if (addition) {
            let stockAfterVal = Number(stockBeforeVal) + Number(nValue);
            bc_layout_grid.cells(rId, idxStockAfter).setValue(stockAfterVal);
        } else {
            let stockAfterVal = Number(stockBeforeVal) - Number(nValue);
            bc_layout_grid.cells(rId, idxStockAfter).setValue(stockAfterVal);
        }

        bc_layout_grid.cells(rId, idxModification).setValue(operator + ' ' + nValue);
    }

    function getValueOperatorCurrentValue(value) {
        let posMinus = value.indexOf(operatorRemove);
        let posReplace = value.indexOf(operatorReplace);
        let operator = operatorAdd;
        if (posMinus !== -1) {
            operator = operatorRemove;
        } else if(posReplace !== -1) {
            operator = operatorReplace;
        }
        value = value.split(operator);
        if (value.length === 2) {
            value = value[1].trim();
        } else {
            value = value[0].trim();
        }
        return {
            'operator': operator,
            'value': value
        };
    }

    function processValidate(type, config = null) {
        let data = config;
        switch (type) {
            case 'ref':
            case 'selection':
                $.post('index.php?ajax=1&act=cat_win-barcode_update', {
                    'type': type,
                    'config': data
                }, function (data) {
                    callBackProcessValidate(data);
                });
                break;
            case 'all':
                let ids = bc_layout_grid.getSelectedRowId();
                if (ids === null) {
                    if (confirm('<?php echo _l('No row selected. Do you really want to validate all product rows?', 1); ?>')) {
                        bc_layout_grid.selectAll();
                        ids = bc_layout_grid.getSelectedRowId();
                    } else {
                        break;
                    }
                }
                ids = ids.split(',');
                let config = {};
                if (ids !== null) {
                    let idxModification = bc_layout_grid.getColIndexById('modification');
                    ids.forEach(function (id) {
                        let process_value = bc_layout_grid.cells(id, idxModification).getValue();
                        config[id] = {
                            'code': id,
                            'process_value': process_value
                        }
                    });
                    $.post('index.php?ajax=1&act=cat_win-barcode_update', {
                        'type': type,
                        'config': config
                    }, function (data) {
                        callBackProcessValidate(data);
                    });
                }
                break;

        }
    }

    function callBackProcessValidate(data) {
        let data_final = JSON.parse(data);
        let in_grid = bc_layout_grid.getAllRowIds();
        if (in_grid !== "" && data_final.pdt_updated.length > 0) {
            data_final.pdt_updated.forEach(function (id) {
                bc_layout_grid.deleteRow(id);
            });
        }
        if (data_final.error > 0) {
            dhtmlx.message({text: data_final.message, type: 'error', expire: 3000});
        } else {
            dhtmlx.message({text: data_final.message, type: 'success', expire: 3000});
        }
    }

    function processDelete(id) {
        if (id === 'all') {
            let ids = bc_layout_grid.getSelectedRowId();
            if (ids === null) {
                if (confirm('<?php echo _l('No row selected. Do you really want to delete all product rows?', 1); ?>')) {
                    bc_layout_grid.selectAll();
                    ids = bc_layout_grid.getSelectedRowId();
                } else {
                    return false;
                }
            }
            ids = ids.split(',');
            ids.forEach(function (id) {
                bc_layout_grid.deleteRow(id);
            })
        } else {
            bc_layout_grid.deleteRow(id);
        }
    }

    function processActionRow(id_row, validate = 0) {
        if (validate === 1) {
            let idxModification = bc_layout_grid.getColIndexById('modification');
            let process_value = bc_layout_grid.cells(id_row, idxModification).getValue();
            let config = {};
            config[id_row] = {
                'code': id_row,
                'process_value': process_value
            };
            processValidate('selection', config);
        } else {
            processDelete(id_row);

        }
    }

    // custom cell
    function eXcell_actionValidate(cell) {
        if (cell) {
            this.cell = cell;
            this.grid = this.cell.parentNode.grid;
        }
        this.edit = function () {
        };
        this.isDisabled = function () {
            return true;
        };
        this.setValue = function (id_row) {
            let icon = '<i class="fas fa-fa fa-check-circle green" style="color:#84ca84"></i>';
            let button_html = '<a href="#" onclick="processActionRow(\'' + id_row + '\',1);return false;">'+icon+'</a>';
            this.setCValue(button_html, null);
        }
    }

    function eXcell_actionDelete(cell) {
        if (cell) {
            this.cell = cell;
            this.grid = this.cell.parentNode.grid;
        }
        this.edit = function () {
        };
        this.isDisabled = function () {
            return true;
        };
        this.setValue = function (id_row) {
            let icon = '<i class="fas fa-fa fa-minus-circle red" style="color:#e83c3c"></i>';
            let button_html = '<a href="#" onclick="processActionRow(\'' + id_row + '\');return false;">'+icon+'</a>';
            this.setCValue(button_html, 21);
        }
    }

    eXcell_actionValidate.prototype = new eXcell;// nests all other methods from the base class
    eXcell_actionDelete.prototype = new eXcell;// nests all other methods from the base class
<?php echo '</script>'; ?>
<?php } ?>