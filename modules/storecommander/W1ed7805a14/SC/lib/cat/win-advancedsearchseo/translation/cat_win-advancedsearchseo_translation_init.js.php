wAdvancedSearchSeo.tbProperties.addListOption('win_advancedsearchsero_props', 'win-advancedsearchsero_prop_translation', 1, 'button', '<?php echo _l('Translation'); ?>', 'fad fa-at');


wAdvancedSearchSeo.tbProperties.addButton("refresh", 100, "", "fa fa-sync green", "fa fa-sync green");
wAdvancedSearchSeo.tbProperties.setItemToolTip('refresh', '<?php echo _l('Refresh', 1); ?>');
wAdvancedSearchSeo.tbProperties.addButton("select_all", 100, "", "fad fa-bolt green", "fad fa-bolt green");
wAdvancedSearchSeo.tbProperties.setItemToolTip('select_all', '<?php echo _l('Select all', 1); ?>');
wAdvancedSearchSeo.tbProperties.addButton("wAdvancedSearchSeo_prop_exportcsv", 100, "", "fad fa-file-csv green", "fad fa-file-csv green");
wAdvancedSearchSeo.tbProperties.setItemToolTip('wAdvancedSearchSeo_prop_exportcsv','<?php echo _l('Export grid to clipboard in CSV format for MSExcel with tab delimiter.'); ?>');

wAdvancedSearchSeo.tbProperties.attachEvent('onClick', function (itemId) {
    switch (itemId) {
        case 'win-advancedsearchsero_prop_translation':
            hidewAdvancedSearchSeoSubpropertiesItems();
            wAdvancedSearchSeo.tbProperties.setItemText('win_advancedsearchsero_props', '<?php echo _l('Translation'); ?>');
            wAdvancedSearchSeo.tbProperties.setItemImage('win_advancedsearchsero_props', 'fad fa-at');
            actual_wAdvancedSearchSeo_subproperty = itemId;
            initWinAdvancedSearchSeoTranslation();
            displayAdvancedSearchSeoTranslation();
            break;
        case 'refresh':
            displayAdvancedSearchSeoTranslation();
            break;
        case 'wAdvancedSearchSeo_prop_exportcsv':
            displayQuickExportWindow(wAdvancedSearchSeo.translation_grid,1);
            break;
        case 'select_all':
            wAdvancedSearchSeo.translation_grid.selectAll();
            break;
    }
});

function displayAdvancedSearchSeoTranslation() {
    let loadUrl = 'index.php?ajax=1&act=cat_win-advancedsearchseo_translation_get';
    ajaxPostCalling(wAdvancedSearchSeo.translation_layout.cells('a'), wAdvancedSearchSeo.translation_grid, loadUrl, {
        main_selected_id_lang: wAdvancedSearchSeo.tb.selected_lang,
        page_ids: wAdvancedSearchSeo.grid.getSelectedRowId()
    }, function (data) {
        wAdvancedSearchSeo.translation_grid.parse(data);
    });
}

let wAdvancedSearchSeo_current_id = 0;
wAdvancedSearchSeo.grid.attachEvent("onRowSelect", function (idItem) {
    if (actual_wAdvancedSearchSeo_subproperty === 'win-advancedsearchsero_prop_translation'
        && (wAdvancedSearchSeo.grid.getSelectedRowId() !== null && wAdvancedSearchSeo_current_id !== idItem)) {
        displayAdvancedSearchSeoTranslation();
        wAdvancedSearchSeo_current_id = idItem;
    }
});

function callbackAdvancedSearchSeo_translation(rId, action, jsonData = null) {
    switch (action) {
        case 'update':
            if (jsonData !== null) {
                for (const [col_id, nValue] of Object.entries(jsonData)) {
                    wAdvancedSearchSeo.translation_grid.cells(rId, wAdvancedSearchSeo.translation_grid.getColIndexById(col_id)).setValue(nValue);
                }
            }
            wAdvancedSearchSeo.translation_grid.setRowTextNormal(rId);
            break;
    }
}

function initWinAdvancedSearchSeoTranslation() {

    wAdvancedSearchSeo.translation_ContextMenuConfig = {
        'lastColRowSelection': {
            'rId': null,
            'cId': null
        },
        'clipboardColRowSelection': {
            'rId': null,
            'cId': null
        },
        'clipboardValue': null
    }

    wAdvancedSearchSeo.tbProperties.showItem('refresh');
    wAdvancedSearchSeo.translation_layout = dhxlAdvancedSearchSeo.cells('b').attachLayout('1C');
    wAdvancedSearchSeo.translation_layout.cells('a').hideHeader();
    dhxlAdvancedSearchSeo.cells('b').showHeader();

    wAdvancedSearchSeo.translation_grid = wAdvancedSearchSeo.translation_layout.cells('a').attachGrid();
    wAdvancedSearchSeo.translation_grid.enableMultiselect(true);
    wAdvancedSearchSeo.translation_grid.enableSmartRendering(true);

    // UISettings
    wAdvancedSearchSeo.translation_grid._uisettings_prefix = 'cat_win-advancedsearchsero_translation';
    wAdvancedSearchSeo.translation_grid._uisettings_name = wAdvancedSearchSeo.translation_grid._uisettings_prefix;
    wAdvancedSearchSeo.translation_grid._first_loading = 1;

    // UISettings
    initGridUISettings(wAdvancedSearchSeo.translation_grid);

    wAdvancedSearchSeo.translation_grid.attachEvent("onMouseOver", function (rId, cInd) {
        return wAdvancedSearchSeo.translation_grid.getColType(cInd) !== 'wysiwyg';

    });

    // grid events
    wAdvancedSearchSeo.translation_grid.attachEvent("onRowDblClicked", function (rId, cInd) {
        if (wAdvancedSearchSeo.translation_grid.getColType(cInd) === 'wysiwyg') {
            openWysiwygWindow(wAdvancedSearchSeo.translation_grid, rId, cInd);
            return false;
        }
        return true;
    });

    wAdvancedSearchSeo.translation_grid.attachEvent("onEditCell", function (stage, rId, cInd, nValue, oValue) {
        if (stage === 1 && this.editor && this.editor.obj) this.editor.obj.select();
        if (stage === 2 && nValue !== oValue) {
            let params = {
                name: "cat_win-advancedsearchseo_update_queue",
                row: rId,
                action: "update",
                params: {},
                callback: "callbackAdvancedSearchSeo_translation('" + rId + "', 'update',{jsonData});"
            };
            // COLUMN VALUES
            params.params[wAdvancedSearchSeo.translation_grid.getColumnId(cInd)] = wAdvancedSearchSeo.translation_grid.cells(rId, cInd).getValue();

            params.params = JSON.stringify(params.params);
            addInUpdateQueue(params, wAdvancedSearchSeo.translation_grid);
        }
        return true;
    });

    // context menu
    wAdvancedSearchSeo.translation_grid.context_menu = new dhtmlXMenuObject();
    wAdvancedSearchSeo.translation_grid.context_menu.renderAsContextMenu();
    wAdvancedSearchSeo.translation_grid.context_menu.loadStruct(`<menu absolutePosition="auto" mode="popup" maxItems="8"  globalCss="contextMenu" globalSecondCss="contextMenu" globalTextCss="contextMenuItem">
        <item text="ID" id="id_seo" enabled="false"/>
        <item text="<?php echo _l('Language'); ?>" id="iso_lang" enabled="false"/>
        <item text="<?php echo _l('Copy'); ?>" id="copy"/>
        <item text="<?php echo _l('Paste'); ?>" id="paste"/>
    '</menu>`);
    wAdvancedSearchSeo.translation_grid.enableContextMenu(wAdvancedSearchSeo.translation_grid.context_menu);

    // context menu events
    wAdvancedSearchSeo.translation_grid.context_menu.attachEvent("onClick", function (itemId) {
        switch (itemId) {
            case 'copy':
                if (wAdvancedSearchSeo.translation_ContextMenuConfig.lastColRowSelection.cId !== null) {
                    wAdvancedSearchSeo.translation_ContextMenuConfig.clipboardValue = wAdvancedSearchSeo.translation_grid.cells(wAdvancedSearchSeo.translation_ContextMenuConfig.lastColRowSelection.rId, wAdvancedSearchSeo.translation_ContextMenuConfig.lastColRowSelection.cId).getValue();
                    wAdvancedSearchSeo.translation_grid.context_menu.setItemText('paste', '<?php echo _l('Paste'); ?> ' + wAdvancedSearchSeo.translation_grid.cells(wAdvancedSearchSeo.translation_ContextMenuConfig.lastColRowSelection.rId, wAdvancedSearchSeo.translation_ContextMenuConfig.lastColRowSelection.cId).getTitle());
                    wAdvancedSearchSeo.translation_ContextMenuConfig.clipboardColRowSelection = wAdvancedSearchSeo.translation_ContextMenuConfig.lastColRowSelection;
                }
                break;
            case 'paste':
                if (wAdvancedSearchSeo.translation_ContextMenuConfig.lastColRowSelection.cId !== null
                    && wAdvancedSearchSeo.translation_ContextMenuConfig.clipboardValue !== null
                    && wAdvancedSearchSeo.translation_ContextMenuConfig.clipboardColRowSelection.cId === wAdvancedSearchSeo.translation_ContextMenuConfig.lastColRowSelection.cId) {
                    let selection = wAdvancedSearchSeo.translation_grid.getSelectedRowId();
                    if (selection !== null) {
                        for (const rId of selection.split(',')) {
                            let oValue = wAdvancedSearchSeo.translation_grid.cells(rId, wAdvancedSearchSeo.translation_ContextMenuConfig.lastColRowSelection.cId).getValue();
                            wAdvancedSearchSeo.translation_grid.cells(rId, wAdvancedSearchSeo.translation_ContextMenuConfig.lastColRowSelection.cId).setValue(wAdvancedSearchSeo.translation_ContextMenuConfig.clipboardValue);
                            wAdvancedSearchSeo.translation_grid.cells(rId, wAdvancedSearchSeo.translation_ContextMenuConfig.lastColRowSelection.cId).cell.wasChanged = true;
                            wAdvancedSearchSeo.translation_grid.callEvent('onEditCell', [2, rId, wAdvancedSearchSeo.translation_ContextMenuConfig.lastColRowSelection.cId, wAdvancedSearchSeo.translation_ContextMenuConfig.clipboardValue, oValue]);
                        }
                    }
                }
                break;
        }
    });

    wAdvancedSearchSeo.translation_grid.attachEvent("onBeforeContextMenu", function (rId, cId, grid) {
        let disableOnCols = [
            wAdvancedSearchSeo.translation_grid.getColIndexById('id_seo'),
            wAdvancedSearchSeo.translation_grid.getColIndexById('iso_lang')
        ];
        if (disableOnCols.includes(cId)) {
            return false;
        }
        wAdvancedSearchSeo.translation_ContextMenuConfig.lastColRowSelection = {
            'rId': rId,
            'cId': cId
        };
        wAdvancedSearchSeo.translation_grid.context_menu.setItemText('id_seo', '<?php echo _l('ID SEO page'); ?><?php echo _l(':'); ?> ' + wAdvancedSearchSeo.translation_grid.cells(rId, wAdvancedSearchSeo.translation_grid.getColIndexById('id_seo')).getTitle());
        wAdvancedSearchSeo.translation_grid.context_menu.setItemText('iso_lang', '<?php echo _l('Lang'); ?><?php echo _l(':'); ?> ' + wAdvancedSearchSeo.translation_grid.cells(rId, wAdvancedSearchSeo.translation_grid.getColIndexById('iso_lang')).getTitle());
        if (wAdvancedSearchSeo.translation_ContextMenuConfig.lastColRowSelection.cId === wAdvancedSearchSeo.translation_ContextMenuConfig.clipboardColRowSelection.cId) {
            wAdvancedSearchSeo.translation_grid.context_menu.setItemEnabled('paste');
        } else {
            wAdvancedSearchSeo.translation_grid.context_menu.setItemDisabled('paste');
        }
        return true;
    });
}