prop_tb.addListOption('panel', 'supplierseo', 15, "button", '<?php echo _l('SEO', 1); ?>', "fad fa-at");
allowed_properties_panel[allowed_properties_panel.length] = "supplierseo";

clipboardType_SupplierSeo = null;
needInitSupplierSeo = 1;
function initSupplierSeo() {
    if (needInitSupplierSeo) {
        prop_tb._SupplierSeoLayout = dhxLayout.cells('b').attachLayout('2E');
        dhxLayout.cells('b').showHeader();

        // SEO
        prop_tb._supplierSeo = prop_tb._SupplierSeoLayout.cells('a');
        prop_tb._supplierSeo.setText('<?php echo _l('SEO', 1); ?>');

        prop_tb._supplierSeo_tb = prop_tb._supplierSeo.attachToolbar();
        prop_tb._supplierSeo_tb.setIconset('awesome');
        prop_tb._supplierSeo_tb.addButton("SupplierSeo_refresh", 100, "", "fa fa-sync green", "fa fa-sync green");
        prop_tb._supplierSeo_tb.setItemToolTip('SupplierSeo_refresh', '<?php echo _l('Refresh grid', 1); ?>');
        prop_tb._supplierSeo_tb.addButton("exportcsv", 100, "", "fad fa-file-csv green", "fad fa-file-csv green");
        prop_tb._supplierSeo_tb.setItemToolTip('exportcsv', '<?php echo _l('Export grid to clipboard in CSV format for MSExcel with tab delimiter.'); ?>');
        prop_tb._supplierSeo_tb.addButton("seo_selectall", 100, "", "fa fa-bolt yellow", "fa fa-bolt yellow");
        prop_tb._supplierSeo_tb.setItemToolTip('seo_selectall','<?php echo _l('Select all'); ?>');

        prop_tb._supplierSeo_tb.attachEvent("onClick", function (id) {
            switch(id) {
                case 'SupplierSeo_refresh':
                    displaySupplierSeo();
                    break;
                case 'exportcsv':
                    displayQuickExportWindow(prop_tb._supplierSeoGrid, 1);
                    break;
                case 'seo_selectall':
                    prop_tb._supplierSeoGrid.selectAll();
                    break;
            }
        });

        prop_tb._supplierSeoGrid = prop_tb._supplierSeo.attachGrid();
        prop_tb._supplierSeoGrid._name = '_supplierSeoGrid';
        prop_tb._supplierSeoGrid.setImagePath("lib/js/imgs/");
        prop_tb._supplierSeoGrid.enableDragAndDrop(false);
        prop_tb._supplierSeoGrid.enableMultiselect(false);

        // UISettings
        prop_tb._supplierSeoGrid._uisettings_prefix = 'sup_SupplierSeo';
        prop_tb._supplierSeoGrid._uisettings_name = prop_tb._supplierSeoGrid._uisettings_prefix;
        prop_tb._supplierSeoGrid._first_loading = 1;

        // UISettings
        initGridUISettings(prop_tb._supplierSeoGrid);

        prop_tb._supplierSeoGrid.attachEvent("onEditCell", onEditCellSupplierSeo);


        prop_tb._supplierSeoGrid.attachEvent("onRowSelect", function (idstock) {
            if (propertiesPanel == 'supplierseo') {
                displayGoogleAdwords();
            }
        });

        // Context menu for MultiShops Info Product grid
        supplierSeo_cmenu = new dhtmlXMenuObject();
        supplierSeo_cmenu.renderAsContextMenu();

        function onGridSupplierSeoContextButtonClick(itemId) {
            let tabId = prop_tb._supplierSeoGrid.contextID.split('_');
            tabId = tabId[0] + "_" + tabId[1]<?php if (SCMS) { ?>+ "_" + tabId[2]<?php } ?>;
            if (itemId == "copy") {
                if (lastColumnRightClicked_SupplierSeo != 0) {
                    clipboardValue_SupplierSeo = prop_tb._supplierSeoGrid.cells(tabId, lastColumnRightClicked_SupplierSeo).getValue();
                    supplierSeo_cmenu.setItemText('paste', '<?php echo _l('Paste'); ?> ' + prop_tb._supplierSeoGrid.cells(tabId, lastColumnRightClicked_SupplierSeo).getTitle());
                    clipboardType_SupplierSeo = lastColumnRightClicked_SupplierSeo;
                }
            }
            if (itemId == "paste") {
                if (lastColumnRightClicked_SupplierSeo != 0 && clipboardValue_SupplierSeo != null && clipboardType_SupplierSeo == lastColumnRightClicked_SupplierSeo) {
                    selection = prop_tb._supplierSeoGrid.getSelectedRowId();
                    if (selection != '' && selection != null) {
                        selArray = selection.split(',');
                        for (i = 0; i < selArray.length; i++) {
                            var oValue = prop_tb._supplierSeoGrid.cells(selArray[i], lastColumnRightClicked_SupplierSeo).getValue();
                            prop_tb._supplierSeoGrid.cells(selArray[i], lastColumnRightClicked_SupplierSeo).setValue(clipboardValue_SupplierSeo);
                            prop_tb._supplierSeoGrid.cells(selArray[i], lastColumnRightClicked_SupplierSeo).cell.wasChanged = true;
                            onEditCellSupplierSeo(2, selArray[i], lastColumnRightClicked_SupplierSeo, clipboardValue_SupplierSeo, oValue);
                        }
                    }
                }
            }
        }

        supplierSeo_cmenu.attachEvent("onClick", onGridSupplierSeoContextButtonClick);
        var contextMenuXML = '<menu absolutePosition="auto" mode="popup" maxItems="8"  globalCss="contextMenu" globalSecondCss="contextMenu" globalTextCss="contextMenuItem">' +
            '<item text="Object" id="object" enabled="false"/>' +
            '<item text="Lang" id="lang" enabled="false"/>' +
            <?php if (SCMS) { ?>'<item text="Shop" id="shop" enabled="false"/>' +<?php } ?>
            '<item text="<?php echo _l('Copy'); ?>" id="copy"/>' +
            '<item text="<?php echo _l('Paste'); ?>" id="paste"/>' +
            '</menu>';
        supplierSeo_cmenu.loadStruct(contextMenuXML);
        prop_tb._supplierSeoGrid.enableContextMenu(supplierSeo_cmenu);

        prop_tb._supplierSeoGrid.attachEvent("onBeforeContextMenu", function (rowid, colidx, grid) {
            var disableOnCols = [
                prop_tb._supplierSeoGrid.getColIndexById('id_supplier'),
                <?php if (SCMS) { ?>prop_tb._supplierSeoGrid.getColIndexById('shop'),<?php } ?>
                prop_tb._supplierSeoGrid.getColIndexById('lang'),
                prop_tb._supplierSeoGrid.getColIndexById('meta_title_width'),
                prop_tb._supplierSeoGrid.getColIndexById('meta_description_width'),
                prop_tb._supplierSeoGrid.getColIndexById('meta_keywords_width')
            ];
            if (in_array(colidx, disableOnCols)) {
                return false;
            }
            lastColumnRightClicked_SupplierSeo = colidx;
            supplierSeo_cmenu.setItemText('object', '<?php echo _l('Supplier:'); ?> ' + prop_tb._supplierSeoGrid.cells(rowid, prop_tb._supplierSeoGrid.getColIndexById('name')).getTitle());
            <?php if (SCMS) { ?>supplierSeo_cmenu.setItemText('shop', '<?php echo _l('Shop:'); ?> ' + prop_tb._supplierSeoGrid.cells(rowid, prop_tb._supplierSeoGrid.getColIndexById('shop')).getTitle());<?php } ?>
            supplierSeo_cmenu.setItemText('lang', '<?php echo _l('Lang:'); ?> ' + prop_tb._supplierSeoGrid.cells(rowid, prop_tb._supplierSeoGrid.getColIndexById('lang')).getTitle());
            if (lastColumnRightClicked_SupplierSeo == clipboardType_SupplierSeo) {
                supplierSeo_cmenu.setItemEnabled('paste');
            } else {
                supplierSeo_cmenu.setItemDisabled('paste');
            }
            return true;
        });

        // GOOGLE ADD
        prop_tb._googleAdwords = prop_tb._SupplierSeoLayout.cells('b');
        prop_tb._googleAdwords.setHeight(150);
        prop_tb._googleAdwords.setText('<?php echo _l('Google Adwords', 1); ?>');

        prop_tb._googleAdwords_tb = prop_tb._googleAdwords.attachToolbar();
        prop_tb._googleAdwords_tb.setIconset('awesome');
        prop_tb._googleAdwords_tb.addButton("googleAdwords_refresh", 100, "", "fa fa-sync green", "fa fa-sync green");
        prop_tb._googleAdwords_tb.setItemToolTip('googleAdwords_refresh', '<?php echo _l('Refresh grid', 1); ?>');
        prop_tb._googleAdwords_tb.attachEvent("onClick", function (id) {
            if (id == 'googleAdwords_refresh') {
                displayGoogleAdwords();
            }

        });

        needInitSupplierSeo = 0;
    }
}



function onEditCellSupplierSeo(stage, rId, cInd, nValue, oValue) {
    if (stage == 1 && this.editor && this.editor.obj) this.editor.obj.select();

    if (stage == 2 && nValue != oValue) {
        idxLinkRewrite = prop_tb._supplierSeoGrid.getColIndexById('link_rewrite');
        if (nValue != "" && cInd == idxLinkRewrite) {
            <?php $accented = Configuration::get('PS_ALLOW_ACCENTED_CHARS_URL');
            if ($accented == 1) {    ?>
            prop_tb._supplierSeoGrid.cells(rId, idxLinkRewrite).setValue(getAccentedLinkRewriteFromString(nValue.substr(0,<?php echo _s('CAT_LINK_REWRITE_SIZE'); ?>)));
            <?php }
            else
            { ?>
            let rId_splitted = rId.split('_');
            let id_lang = Number(rId_splitted[1]);
            prop_tb._supplierSeoGrid.cells(rId, idxLinkRewrite).setValue(getLinkRewriteFromString(nValue.substr(0,<?php echo _s('CAT_LINK_REWRITE_SIZE'); ?>),id_lang));
            <?php } ?>
        }

        var params = {
            name: "sup_seo_update_queue",
            row: rId,
            action: "update",
            params: {},
            callback: "callbackSupplierSeo('" + rId + "','update','" + rId + "');"
        };
        // COLUMN VALUES

        params.params[prop_tb._supplierSeoGrid.getColumnId(cInd)] = prop_tb._supplierSeoGrid.cells(rId, cInd).getValue();
        // USER DATA

        params.params = JSON.stringify(params.params);
        addInUpdateQueue(params, prop_tb._supplierSeoGrid);
    }
    return true;
}
// CALLBACK FUNCTION
function callbackSupplierSeo(sid, action, tid) {
    if (action == 'update') {
        prop_tb._supplierSeoGrid.setRowTextNormal(sid);
        displaySupplierSeo();
        displaySuppliers();
    }
}

function setPropertiesPanel_SupplierSeo(id) {
    if (id == 'supplierseo') {
        if (last_supplierID != undefined && last_supplierID != "") {
            idxProductName = sup_grid.getColIndexById('name');
            dhxLayout.cells('b').setText('<?php echo _l('Properties', 1).' '._l('of', 1); ?> ' + sup_grid.cells(last_supplierID, idxProductName).getValue());
        }
        hidePropTBButtons();
        prop_tb.setItemText('panel', '<?php echo _l('SEO', 1); ?>');
        prop_tb.setItemImage('panel', 'fad fa-at');
        needInitSupplierSeo = 1;
        initSupplierSeo();
        propertiesPanel = 'supplierseo';
        if (last_supplierID != 0) {
            displaySupplierSeo();
            displayGoogleAdwords();
        }
    }
}
prop_tb.attachEvent("onClick", setPropertiesPanel_SupplierSeo);

function displaySupplierSeo() {
    prop_tb._supplierSeoGrid.clearAll(true);
    let tempIdList = (sup_grid.getSelectedRowId() != null ? sup_grid.getSelectedRowId() : "");
    let loadUrl = 'index.php?ajax=1&act=sup_seo_get';
    ajaxPostCalling(dhxLayout.cells('b'), prop_tb._supplierSeoGrid, loadUrl, {
        idlist: tempIdList
    }, function (data) {
        prop_tb._supplierSeoGrid.parse(data);
        let nb = prop_tb._supplierSeoGrid.getRowsNum();
        prop_tb._supplierSeoGrid._rowsNum = nb;

        // UISettings
        loadGridUISettings(prop_tb._supplierSeoGrid);
        prop_tb._supplierSeoGrid._first_loading = 0;
    });
}

function displayGoogleAdwords() {
    prop_tb._googleAdwords.setHeight(150);
    prop_tb._googleAdwords.attachURL("index.php?ajax=1&act=sup_seo_add_get&id_lang=" + SC_ID_LANG);
}


let supplierseo_current_id = 0;
sup_grid.attachEvent("onRowSelect", function (id_supplier) {
    if (propertiesPanel == 'supplierseo' && (sup_grid.getSelectedRowId() !== null && supplierseo_current_id != id_supplier)) {
        displaySupplierSeo();
        displayGoogleAdwords();
        supplierseo_current_id = id_supplier;
    }
});
