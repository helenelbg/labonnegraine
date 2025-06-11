<script type="text/javascript">
    sup_grid = sup_supplierPanel.attachGrid();
    sup_grid._name = 'grid';
    sup_supplierPanel.setText('<?php echo _l('Suppliers', 1); ?>');

    var open_sup_grid = "auto";
    const open_sup_id_page = 0;
    loadingtime = 0;

    // UISettings
    sup_grid._uisettings_prefix = 'sup_grid_';
    sup_grid._uisettings_name = sup_grid._uisettings_prefix;
    sup_grid._first_loading = 1;

    sup_grid.enableDragAndDrop(true);
    sup_grid.setDragBehavior('child');
    sup_grid.enableSmartRendering(true);
    const sup_grid_sb = sup_supplierPanel.attachStatusBar();
    <?php if (_s('APP_DISABLED_COLUMN_MOVE')) { ?>
    sup_grid.enableColumnMove(false);
    <?php } ?>

    <?php
    if (SCMS)
    {
        ?>
    sup_shoptree.attachEvent("onClick", onClickShopTree);

    function onClickShopTree(idshop) {
        if (idshop == 'all') {
            displaySuppliers(null, true);
        } else {
            displaySuppliers();
        }

    }
    <?php
    }
    else
    {
        ?>
    displaySuppliers(null,true);
    <?php
    }
    ?>

    sup_grid._key_events.k9_0_0 = function () {
        sup_grid.editStop();
        sup_grid.selectCell(sup_grid.getRowIndex(sup_grid.getSelectedRowId()) + 1, sup_grid.getSelectedCellIndex(), true, false, true, true);
    };

    sup_grid_tb = sup_supplierPanel.attachToolbar();
    sup_grid_tb.setIconset('awesome');
    sup_grid_tb.addButton("refresh", 100, "", "fa fa-sync green", "fa fa-sync green");
    sup_grid_tb.setItemToolTip('refresh', '<?php echo _l('Refresh grid'); ?>');
    sup_grid_tb.addButton("add", 100, "", "fa fa-plus-circle green", "fa fa-plus-circle green");
    sup_grid_tb.setItemToolTip('add', '<?php echo _l('Create new supplier'); ?>');
    sup_grid_tb.addButton("delete", 100, "", "fa fa-minus-circle red", "fa fa-minus-circle red");
    sup_grid_tb.setItemToolTip('delete', '<?php echo _l('This will permanently delete the selected supplier'); ?>');
    sup_grid_tb.addButton("selectall", 100, "", "fa fa-bolt yellow", "fad fa-bolt grey");
    sup_grid_tb.setItemToolTip('selectall', '<?php echo _l('Select all suppliers'); ?>');
    <?php if (_r('ACT_CAT_FAST_EXPORT')) { ?>
    sup_grid_tb.addButton("exportcsv", 100, "", "fad fa-file-csv green", "fad fa-file-csv green");
    sup_grid_tb.setItemToolTip('exportcsv', '<?php echo _l('Export grid to clipboard in CSV format for MSExcel with tab delimiter.'); ?>');
    <?php } ?>
    sup_grid_tb.addButton("help", 100, "", "fad fa-question-circle blue", "fad fa-question-circle blue");
    sup_grid_tb.setItemToolTip('help', '<?php echo _l('Help'); ?>');

    <?php
    $tmp = array();
    $clang = _l('Language');
    $optlang = '';
    foreach ($languages as $lang)
    {
        if ($lang['id_lang'] == $sc_agent->id_lang)
        {
            $clang = $lang['iso_code'];
            $optlang = 'sup_lang_'.$lang['iso_code'];
        }
        $tmp[] = "['sup_lang_".$lang['iso_code']."', 'obj', '".$lang['name']."', '']";
    }
    if (count($tmp) > 1)
    {
        ?>
    sup_grid_tb.addButtonSelect('lang', 0, '<?php echo $clang; ?>',<?php echo '['.join(',', $tmp).']'; ?>, 'fad fa-flag blue', 'fad fa-flag blue', false, true);
    sup_grid_tb.setItemToolTip('lang', '<?php echo _l('Select supplier language'); ?>');
    sup_grid_tb.setListOptionSelected('lang', '<?php echo $optlang; ?>');
    <?php
    }
    ?>
    const gridnames = {}
    <?php if (_r('GRI_MAN_VIEW_GRID_LIGHT')) { ?>gridnames['grid_light'] = '<?php echo _l('Light view', 1); ?>';<?php } ?>
    <?php if (_r('GRI_MAN_VIEW_GRID_LARGE')) { ?>gridnames['grid_large'] = '<?php echo _l('Large view', 1); ?>';<?php } ?>
    <?php if (_r('GRI_MAN_VIEW_GRID_SEO')) { ?>gridnames['grid_seo'] = '<?php echo _l('SEO', 1); ?>';<?php } ?>
    <?php if (_r('GRI_MAN_VIEW_GRID_ADDRESS')) { ?>gridnames['grid_address'] = '<?php echo _l('Address', 1); ?>';<?php } ?>

    const SupViewsOpts = [];
    for (const [index, value] of Object.entries(gridnames)) {
        SupViewsOpts.push([index, 'obj', value, '']);
    }
    if (SupViewsOpts.length > 25) {
        $('div.dhx_toolbar_poly_dhx_skyblue').addClass('dhx_toolbar_poly_dhx_skyblue_SCROLLBAR');
    }

    var gridView = (in_array('<?php echo _s('SUP_SUPPLIER_GRID_DEFAULT'); ?>', Object.keys(gridnames)) ? '<?php echo _s('SUP_SUPPLIER_GRID_DEFAULT'); ?>' : SupViewsOpts[0][0]);
    var oldGridView = gridView;
    // UISettings
    sup_grid._uisettings_name = sup_grid._uisettings_prefix + gridView;

    sup_grid_tb.addButtonSelect("gridview", 0, "<?php echo _l('Light view'); ?>", SupViewsOpts, "fad fa-ruler-triangle", "fad fa-ruler-triangle", false, true);
    sup_grid_tb.setItemToolTip('gridview', '<?php echo _l('Grid view settings'); ?>');

    function gridToolBarOnClick(id) {
        if (sup_grid_tb.getParentId(id) === 'gridview') {
            oldGridView = gridView;
            gridView = id;

            // UISettings
            sup_grid._uisettings_name = sup_grid._uisettings_prefix + gridView;

            sup_grid_tb.setItemText('gridview', gridnames[id]);
            displaySuppliers();
        }
        if (id == 'help') {
            <?php echo "window.open('".getScExternalLink('support_suppliers')."');"; ?>
        }
        if (id == 'filters_reset') {
            for (const colIndex of sup_grid.columnIds.keys()) {
                if (sup_grid.getFilterElement(colIndex) != null) {
                    sup_grid.getFilterElement(colIndex).value = "";
                    sup_grid.getFilterElement(colIndex).old_value = "";
                }
            }
            sup_grid.filterByAll();
            sup_grid_tb.setListOptionSelected('filters', '');
        }
        if (id == 'filters_cols_show') {
            for (const colIndex of sup_grid.columnIds.keys()) {
                sup_grid.setColumnHidden(colIndex, false);
            }
            sup_grid_tb.setListOptionSelected('filters', '');
        }
        if (id == 'filters_cols_hide') {
            idxSupplierID = sup_grid.getColIndexById('id');
            idxName = sup_grid.getColIndexById('meta_title');
            sup_grid_tb.setListOptionSelected('filters', '');
        }
        if (id == 'exportcsv') {
            displayQuickExportWindow(sup_grid, 1);
        }
        flagLang = false; // changelang ; lang modified?
        <?php
        $tmp = array();
        $clang = _l('Language');
        foreach ($languages as $lang)
        {
            echo '
            if (id==\'sup_lang_'.$lang['iso_code'].'\')
            {
                SC_ID_LANG='.$lang['id_lang'].';
                sup_grid_tb.setItemText(\'lang\',\''.$lang['iso_code'].'\');
                flagLang=true;
            }';
        }
        ?>
        if (flagLang) {
            displaySuppliers();
        }
        if (id == 'refresh') {
            displaySuppliers();
        }
        if (id == 'add') {
            var newId = new Date().getTime();
            newRow = ['', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''];
            newRow = newRow.slice(0, sup_grid.getColumnsNum() - 1);
            idxID = sup_grid.getColIndexById('id_supplier');
            idxName = sup_grid.getColIndexById('name');
            idxActive = sup_grid.getColIndexById('active');
            newRow[idxID] = newId;
            newRow[idxName] = 'new';
            if (idxActive) newRow[idxActive] = '<?php echo _s('SUP_SUPPLIER_CREA_ACTIVE'); ?>';
            // INSERT
            sup_grid.addRow(newId, newRow);
            sup_grid.setRowHidden(newId, true);

            var params = {
                name: "sup_supplier_update_queue",
                row: newId,
                action: "insert",
                params: {callback: "callbackSupplierUpdate('" + newId + "','insert','{newid}',{data});"}
            };

            // COLUMN VALUES
            sup_grid.forEachCell(newId, function (cellObj, ind) {
                params.params[sup_grid.getColumnId(ind)] = sup_grid.cells(newId, ind).getValue();
            });
            params.params['id_lang'] = SC_ID_LANG;
            // USER DATA
            $.each(sup_grid.UserData.gridglobaluserdata.keys, function (i, key) {
                params.params[key] = sup_grid.UserData.gridglobaluserdata.values[i];
            });

            sendInsert(params, sup_supplierPanel);
        }
        if (id == "delete") {
            if (confirm('<?php echo _l('Permanently delete the selected supplier everywhere in the shop', 1); ?>')) {
                selection = sup_grid.getSelectedRowId();
                ids = selection.split(',');
                $.each(ids, function (num, rId) {
                    var params =
                        {
                            name: "sup_supplier_update_queue",
                            row: rId,
                            action: "delete",
                            params: {},
                            callback: "callbackSupplierUpdate('" + rId + "','delete','" + rId + "');"
                        };
                    params.params = JSON.stringify(params.params);
                    sup_grid.setRowTextStyle(rId, "text-decoration: line-through;");
                    addInUpdateQueue(params, sup_grid);
                });
            }
        }
        if (id == 'selectall') {
            sup_grid.enableSmartRendering(false);
            sup_grid.selectAll();
            getGridStat();
        }

        if (id == 'cols123') {
            sup.cells("a").expand();
            sup.cells("a").setWidth(300);
            sup.cells("b").expand();
            dhxLayout.cells('b').expand();
            dhxLayout.cells('b').setWidth(500);
        }
        if (id == 'cols12') {
            sup.cells("a").expand();
            sup.cells("a").setWidth($(document).width() / 3);
            sup.cells("b").expand();
            dhxLayout.cells('b').collapse();
        }
        if (id == 'cols23') {
            sup.cells("a").collapse();
            sup.cells("b").expand();
            sup.cells("b").setWidth($(document).width() / 2);
            dhxLayout.cells('b').expand();
            dhxLayout.cells('b').setWidth($(document).width() / 2);
        }
    }

    sup_grid_tb.attachEvent("onClick", gridToolBarOnClick);

    sup_grid.setImagePath('lib/js/imgs/');
    sup_grid.enableMultiselect(true);

    // multiedition context menu
    sup_grid.attachEvent("onBeforeContextMenu", function (rowid, colidx) {
        lastColumnRightClicked = colidx;

        sup_menu.setItemText('object', '<?php echo _l('Supplier :'); ?> ' + sup_grid.cells(rowid, sup_grid.getColIndexById('name')).getValue());
        // paste function
        if (lastColumnRightClicked == clipboardType) {
            sup_menu.setItemEnabled('paste');
        } else {
            sup_menu.setItemDisabled('paste');
        }
        var colType = sup_grid.getColType(colidx);
        if (colType == 'ro') {
            sup_menu.setItemDisabled('copy');
            sup_menu.setItemDisabled('paste');
        } else {
            sup_menu.setItemEnabled('copy');
        }

        <?php if (SCI::getConfigurationValue('PS_DISPLAY_SUPPLIERS')){ ?>
        <?php if (SCMS) { ?>
        if (shopUrls[shopselection] != undefined && shopUrls[shopselection] != "" && shopUrls[shopselection] != null) {
            sup_menu.setItemEnabled('goshop');
        } else {
            sup_menu.setItemDisabled('goshop');
        }
        <?php } ?>
        <?php } ?>
        return true;
    });

    function onEditCell(stage, rId, cInd, nValue, oValue) {
        var coltype = sup_grid.getColType(cInd);
        if (stage == 1 && this.editor && this.editor.obj && coltype != 'txt' && coltype != 'txttxt') this.editor.obj.select();
        lastEditedCell = cInd;
        if (nValue != oValue) {
            sup_grid.setRowColor(rId, 'BlanchedAlmond');
            idxActive = sup_grid.getColIndexById('active');
            idxName = sup_grid.getColIndexById('name');
            idxMetaDescription = sup_grid.getColIndexById('meta_description');
            idxMetaKeywords = sup_grid.getColIndexById('meta_keywords');
            if (cInd == idxName) {
                sup_grid.cells(rId, idxName).setValue(sup_grid.cells(rId, idxName).getValue());
            }
            if (cInd == idxMetaDescription) {
                sup_grid.cells(rId, idxMetaDescription).setValue(sup_grid.cells(rId, idxMetaDescription).getValue().substr(0,<?php echo _s('SUP_SUPPLIER_META_DESC_SIZE'); ?>));
            }
            if (cInd == idxMetaKeywords) {
                sup_grid.cells(rId, idxMetaKeywords).setValue(sup_grid.cells(rId, idxMetaKeywords).getValue().substr(0,<?php echo _s('SUP_SUPPLIER_META_KEYWORDS_SIZE'); ?>));
            }
            if (cInd == idxActive) { //Active update
                if (nValue == 0) {
                    sup_grid.cells(rId, idxName).setBgColor('#D7D7D7');
                } else {
                    sup_grid.cells(rId, idxName).setBgColor(sup_grid.cells(rId, 0).getBgColor());
                }
            }
        }

        if (nValue != oValue) {
            addSupplierInQueue(rId, "update", cInd);
            return true;
        }
    }

    sup_grid.attachEvent("onEditCell", onEditCell);

    sup_grid.attachEvent("onMouseOver", function (rId, cInd) {
        if (sup_grid.getColType(cInd) === 'wysiwyg') {
            return false;
        }
        return true;
    });

    // grid events
    sup_grid.attachEvent("onRowDblClicked", function (rId, cInd) {
        if (sup_grid.getColType(cInd) === 'wysiwyg') {
            openWysiwygWindow(sup_grid, rId, cInd);
            return false;
        }
        return true;
    });

    // Context menu for Grid
    sup_menu = new dhtmlXMenuObject();
    sup_menu.renderAsContextMenu();

    function onGridSupplierContextButtonClick(itemId) {
        tabId = sup_grid.contextID.split('_');
        tabId = tabId[0];
        if (itemId == "gopsbo") {
            wModifySupplier = dhxWins.createWindow("wModifySupplier", 50, 50, 1260, $(window).height() - 75);
            wModifySupplier.setText('<?php echo _l('Modify the supplier and close this window to refresh the grid', 1); ?>');
            wModifySupplier.attachURL("<?php echo SC_PS_PATH_ADMIN_REL; ?>index.php?<?php echo version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? 'controller=AdminSuppliers' : 'tab=AdminSuppliers'; ?>&updatesupplier&id_supplier=" + tabId + "&id_lang=" + SC_ID_LANG + "&adminlang=1&token=<?php echo $sc_agent->getPSToken('AdminSuppliers'); ?>");
            wModifySupplier.attachEvent("onClose", function () {
                displaySuppliers();
                return true;
            });
        }
        <?php if (SCI::getConfigurationValue('PS_DISPLAY_SUPPLIERS')){ ?>
        if (itemId == "goshop") {
            let sel = sup_grid.getSelectedRowId();
            if (sel) {
                let k = 1;
                for(const id_supplier of sel.split(',')) {
                    let previewUrl = 0;

                    if (k > <?php echo _s('SUP_SUPPLIER_OPEN_URL'); ?>) {
                        return false
                    }
                    let idxActive = sup_grid.getColIndexById('active');
                    <?php if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) { ?>
                    if (idxActive) {
                        if (sup_grid.cells(id_supplier, idxActive).getValue() == 0) {
                            previewUrl = "<?php echo '&adtoken='.$sc_agent->getPSToken('AdminSuppliers').'&id_employee='.$sc_agent->id_employee; ?>";
                        }
                    }
                    <?php }
                    else
                    { ?>
                    if (idxActive) {
                        if (sup_grid.cells(id_supplier, idxActive).getValue() == 0) {
                            continue;
                        }
                    }
                    <?php } ?>
                    <?php if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                    {
                        if (SCMS)
                        {
                            ?>
                    if (previewUrl != 0) {
                        if (shopUrls[shopselection] != undefined && shopUrls[shopselection] != "" && shopUrls[shopselection] != null)
                            window.open(shopUrls[shopselection] + 'index.php?id_supplier=' + id_supplier + '&controller=supplier&id_lang=' + SC_ID_LANG + previewUrl);
                    } else {
                        if (shopUrls[shopselection] != undefined && shopUrls[shopselection] != "" && shopUrls[shopselection] != null)
                            window.open(shopUrls[shopselection] + 'index.php?id_supplier=' + id_supplier + '&controller=supplier&id_lang=' + SC_ID_LANG);
                    }
                    <?php
                        }
                        else
                        {
                            ?>
                    if (previewUrl != 0) {
                        window.open('<?php echo SC_PS_PATH_REL; ?>index.php?id_supplier=' + id_supplier + '&controller=supplier&id_lang=' + SC_ID_LANG + previewUrl);
                    } else {
                        window.open('<?php echo SC_PS_PATH_REL; ?>index.php?id_supplier=' + id_supplier + '&controller=supplier&id_lang=' + SC_ID_LANG);
                    }
                    <?php
                        }
                    }
                    else
                    {
                        ?>
                    window.open('<?php echo SC_PS_PATH_REL; ?>supplier.php?id_supplier=' + id_supplier);
                    <?php
                    } ?>
                    k++;
                }
            } else {
                var tabId = sup_grid.contextID.split('_');
                <?php if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                    {
                        if (SCMS)
                        {
                            ?>
                if (shopUrls[shopselection] != undefined && shopUrls[shopselection] != "" && shopUrls[shopselection] != null)
                    window.open(shopUrls[shopselection] + 'index.php?id_supplier=' + tabId[0] + '&controller=supplier');
                <?php
                        }
                        else
                        {
                            ?>
                window.open('<?php echo SC_PS_PATH_REL; ?>index.php?id_supplier=' + tabId[0] + '&controller=supplier');
                <?php
                        }
                    }
                else
                {
                    ?>
                window.open('<?php echo SC_PS_PATH_REL; ?>supplier.php?id_supplier=' + tabId[0]);
                <?php
                }
                ?>
            }
        }
        <?php } ?>
        if (itemId == "copy") {
            if (lastColumnRightClicked != 0) {
                clipboardValue = sup_grid.cells(tabId, lastColumnRightClicked).getValue();
                sup_menu.setItemText('paste', '<?php echo _l('Paste'); ?> ' + sup_grid.cells(tabId, lastColumnRightClicked).getTitle().substr(0, 30) + '...');
                clipboardType = lastColumnRightClicked;
            }
        }
        if (itemId == "paste") {
            if (lastColumnRightClicked != 0 && clipboardValue != null && clipboardType == lastColumnRightClicked) {
                selection = sup_grid.getSelectedRowId();
                if (selection != '' && selection != null) {
                    selArray = selection.split(',');
                    for (i = 0; i < selArray.length; i++) {
                        sup_grid.cells(selArray[i], lastColumnRightClicked).setValue(clipboardValue);
                        sup_grid.cells(selArray[i], lastColumnRightClicked).cell.wasChanged = true;
                        onEditCell(null, selArray[i], lastColumnRightClicked, clipboardValue, null);
                    }
                }
            }
        }
    }

    sup_menu.attachEvent("onClick", onGridSupplierContextButtonClick);
    var contextMenuXML = '<menu absolutePosition="auto" mode="popup" maxItems="8"  globalCss="contextMenu" globalSecondCss="contextMenu" globalTextCss="contextMenuItem">' +
        '<item text="Object" id="object" enabled="false"/>' +
        <?php if (SCI::getConfigurationValue('PS_DISPLAY_SUPPLIERS')){ ?>'<item text="<?php echo _l('See on shop'); ?>" id="goshop"/>' +<?php } ?>
        '<item text="<?php echo _l('Edit in PrestaShop BackOffice'); ?>" id="gopsbo"/>' +
        '<item text="<?php echo _l('Copy'); ?>" id="copy"/>' +
        '<item text="<?php echo _l('Paste'); ?>" id="paste"/>' +
        '</menu>';
    sup_menu.loadStruct(contextMenuXML);
    sup_grid.enableContextMenu(sup_menu);

    //#####################################
    //############ Events
    //#####################################

    // Click on a supplier
    function doOnRowSelected(idsupplier) {
        if (!dhxLayout.cells('b').isCollapsed() && last_supplierID != idsupplier) {
            last_supplierID = idsupplier;
            idxName = sup_grid.getColIndexById('name');
            let countSelection = sup_grid.getSelectedRowId().split(',').length;
            let propTitle = '<?php echo _l('Properties', 1).' '._l('of', 1); ?> ' + sup_grid.cells(last_supplierID, idxName).getValue();
            if (countSelection > 1) {
                propTitle = '<?php echo _l('Properties', 1).' '._l('for', 1); ?> ' + countSelection + ' <?php echo strtolower(_l('Suppliers')); ?>';
            }

            if (propertiesPanel != 'descriptions') {
                dhxLayout.cells('b').setText(propTitle);
            }
            <?php
            echo eval('?>'.$pluginSupplierProperties['doOnSupplierRowSelected'].'<?php ');
            ?>
        }
    }

    sup_grid.attachEvent("onRowSelect", doOnRowSelected);

    // UISettings
    initGridUISettings(sup_grid);

    sup_grid.attachEvent("onFilterEnd", function () {
        getGridStat();
    });
    sup_grid.attachEvent("onSelectStateChanged", function () {
        getGridStat();
    });

    function displaySuppliers(callback, firsttime = null) {
        if (firsttime != undefined && firsttime != null && firsttime != "") {
            <?php
            $sql_shop = 'SELECT GROUP_CONCAT(id_shop)
                    FROM '._DB_PREFIX_.'shop
                    WHERE deleted = 0';
            $value = Db::getInstance()->getValue($sql_shop);
            echo 'shopselection = "'.$value.'";';
            ?>
        }
        if (shopselection >= 0) {
            oldFilters = [];
            for (const colIndex of sup_grid.columnIds.keys()) {
                if (sup_grid.getFilterElement(colIndex) != null && sup_grid.getFilterElement(colIndex).value != '') {
                    oldFilters[sup_grid.getColumnId(colIndex)] = sup_grid.getFilterElement(colIndex).value;
                }
            }
            sup_grid.editStop(true);
            sup_grid.clearAll(true);
            sup_grid_sb.setText('');
            oldGridView = gridView;
            sup_grid_sb.setText('<?php echo _l('Loading in progress, please wait...', 1); ?>');

            loadingtime = new Date().getTime();

            let loadUrl = 'index.php?ajax=1&act=sup_supplier_get';
            ajaxPostCalling(sup_supplierPanel, sup_grid, loadUrl, {
                tree_mode: tree_mode,
                supplierfrom: displaySuppliersFrom,
                idshop: shopselection,
                view: gridView,
                id_lang: SC_ID_LANG,
            }, function (data) {
                sup_grid.parse(data);
                sup_grid._rowsNum = sup_grid.getRowsNum();

                var limit_smartrendering = 0;
                if (sup_grid.getUserData("", "LIMIT_SMARTRENDERING") != undefined
                    && sup_grid.getUserData("", "LIMIT_SMARTRENDERING") != 0
                    && sup_grid.getUserData("", "LIMIT_SMARTRENDERING") != null) {
                    limit_smartrendering = sup_grid.getUserData("", "LIMIT_SMARTRENDERING");
                }

                if (limit_smartrendering != 0 && sup_grid._rowsNum > limit_smartrendering) {
                    sup_grid.enableSmartRendering(true);
                } else {
                    sup_grid.enableSmartRendering(false);
                }

                idxID = sup_grid.getColIndexById('id_supplier');
                idxName = sup_grid.getColIndexById('meta_title');
                idxMetaDesc = sup_grid.getColIndexById('meta_description');
                idxMetaKey = sup_grid.getColIndexById('meta_keywords');
                idxContent = sup_grid.getColIndexById('content');
                idxLinkRew = sup_grid.getColIndexById('link_rewrite');
                idxPosition = sup_grid.getColIndexById('position');
                if (idxName !== false) {
                    sup_grid.setCustomSorting(function (a, b, ord, a_id, b_id) {
                        a = sanitizeString(replaceAccentCharacters(latinise(sup_grid.cells(a_id, idxName).getTitle()).toLowerCase()));
                        b = sanitizeString(replaceAccentCharacters(latinise(sup_grid.cells(b_id, idxName).getTitle()).toLowerCase()));
                        return ord == "asc" ? (a > b ? 1 : -1) : (a > b ? -1 : 1);
                    }, idxName);
                }
                if (idxMetaDesc !== false) {
                    sup_grid.setCustomSorting(function (a, b, ord, a_id, b_id) {
                        a = sanitizeString(replaceAccentCharacters(latinise(sup_grid.cells(a_id, idxMetaDesc).getTitle()).toLowerCase()));
                        b = sanitizeString(replaceAccentCharacters(latinise(sup_grid.cells(b_id, idxMetaDesc).getTitle()).toLowerCase()));
                        return ord == "asc" ? (a > b ? 1 : -1) : (a > b ? -1 : 1);
                    }, idxMetaDesc);
                }
                if (idxLinkRew !== false) {
                    sup_grid.setCustomSorting(function (a, b, ord, a_id, b_id) {
                        a = sanitizeString(replaceAccentCharacters(latinise(sup_grid.cells(a_id, idxLinkRew).getTitle()).toLowerCase()));
                        b = sanitizeString(replaceAccentCharacters(latinise(sup_grid.cells(b_id, idxLinkRew).getTitle()).toLowerCase()));
                        return ord == "asc" ? (a > b ? 1 : -1) : (a > b ? -1 : 1);
                    }, idxLinkRew);
                }

                lastEditedCell = 0;
                lastColumnRightClicked = 0;
                for (const colIndex of sup_grid.columnIds.keys()) {
                    if (sup_grid.getFilterElement(colIndex) != null && oldFilters[sup_grid.getColumnId(colIndex)] != undefined) {
                        sup_grid.getFilterElement(colIndex).value = oldFilters[sup_grid.getColumnId(colIndex)];
                    }
                }
                sup_grid.filterByAll();

                // UISettings
                loadGridUISettings(sup_grid);

                getGridStat();
                let loadingtimedisplay = (new Date().getTime() - loadingtime) / 1000;
                $('#layoutstatusloadingtime').html(" - T: " + loadingtimedisplay + "s");

                if (!sup_grid.doesRowExist(last_supplierID)) {
                    last_supplierID = 0;
                } else {
                    sup_grid.selectRowById(last_supplierID);
                }

                // UISettings
                sup_grid._first_loading = 0;

                <?php if (_s('APP_DISABLED_COLUMN_MOVE')) { ?>
                sup_grid.enableColumnMove(false);
                <?php } ?>

                if (open_sup_grid == true) {
                    if (callback == undefined || callback == null || callback == '') {
                        callback = ' ';
                    }
                    callback = callback + 'last_supplierID=0;sup_grid.selectRowById(' + open_sup_id_page + ',false,true,true);';
                    open_sup_grid = false;
                }

                if (callback != '') {
                    eval(callback);
                }
            });
        }
    }

    function getGridStat() {
        var filteredRows = sup_grid.getRowsNum();
        var selectedRows = (sup_grid.getSelectedRowId() ? sup_grid.getSelectedRowId().split(',').length : 0);
        sup_grid_sb.setText(sup_grid._rowsNum + ' ' + (sup_grid._rowsNum > 1 ? '<?php echo _l('Supplier'); ?>' : '<?php echo _l('Supplier'); ?>') + (tree_mode == 'all' ? ' <?php echo _l('in this category and all subcategories'); ?>' : ' <?php echo _l('in this category'); ?>') + " - <?php echo _l('Filter')._l(':'); ?> " + filteredRows + " - <?php echo _l('Selection')._l(':'); ?> " + selectedRows);
    }

    function addSupplierInQueue(rId, action, cIn, vars) {
        var params = {
            name: "sup_supplier_update_queue",
            row: rId,
            action: action,
            params: {},
            updated_field: sup_grid.getColumnId(cIn),
            callback: "callbackSupplierUpdate('" + rId + "','" + action + "','" + rId + "',{data});"
        };

        // COLUMN VALUES
        if (cIn != undefined && cIn != "" && cIn != null && cIn != 0) {
            params.params[sup_grid.getColumnId(cIn)] = sup_grid.cells(rId, cIn).getValue();
        }
        params.params['id_lang'] = SC_ID_LANG;

        if (vars != undefined && vars != null && vars != "" && vars != 0) {
            $.each(vars, function (key, value) {
                params.params[key] = value;
            });
        }

        // USER DATA
        if (rId != undefined && rId != null && rId != "" && rId != 0) {
            if (sup_grid.UserData[rId] != undefined && sup_grid.UserData[rId] != null && sup_grid.UserData[rId] != "" && sup_grid.UserData[rId] != 0) {
                $.each(sup_grid.UserData[rId].keys, function (i, key) {
                    params.params[key] = sup_grid.UserData[rId].values[i];
                });
            }
        }
        $.each(sup_grid.UserData.gridglobaluserdata.keys, function (i, key) {
            params.params[key] = sup_grid.UserData.gridglobaluserdata.values[i];
        });

        params.params = JSON.stringify(params.params);
        addInUpdateQueue(params, sup_grid);
    }

    function callbackSupplierUpdate(sid, action, tid) {
        switch(action){
            case 'insert':
                sup_grid.cells(sid, sup_grid.getColIndexById('id_supplier')).setValue(tid);
                sup_grid.changeRowId(sid, tid);
                sup_grid.setRowHidden(tid, false);
                sup_grid.showRow(tid);
                sup_supplierPanel.progressOff();
                break;
            case 'update':
                sup_grid.setRowTextNormal(sid);
                break;
            case 'delete':
                sup_grid.deleteRow(sid);
                break;
            case 'position':
                displaySuppliers('sup_grid.sortRows(' + sup_grid.getColIndexById('position') + ', "int", "asc");');
                break;
        }
    }

</script>
