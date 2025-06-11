<?php
$defaultLanguageId = (int) Configuration::get('PS_LANG_DEFAULT');
?>
<?php echo '<script type="text/javascript">'; ?>
    // IMPORT
    lastManCSVFile = '';
    mapping = '';
    arrayFieldLang = ['short_description', 'description', 'meta_title', 'meta_description', 'meta_keywords'];
    arrayFieldOption = [];
    var comboArray = null;
    var comboValuesArray = null;
    var optionLabelArray = null;
    var progress_interval = null;

    // AutoImport
    var autoImportRunning = false; // check and auto import?
    var autoImportUnit = 0; // counter
    var autoImportLastState = 0; // 0 : nothing - 1 : waiting reply from server
    var autoImportTODOSize1 = 0; // Size of file stored in var 1
    var autoImportTODOSize2 = 0; // Size of file stored in var 2 to compare with autoImportTODOSize1

    // Import Window
    dhxlImport = wManImport.attachLayout("3T");
    wManImport._sb = dhxlImport.attachStatusBar();

    // Files Settings
    dhxlImport.cells('a').hideHeader();
    dhxlImport.cells('a').setHeight(200);

    // Files Settings Toolbar
    wManImport.tbOptions = dhxlImport.cells('a').attachToolbar();
    wManImport.tbOptions.setIconset('awesome');
    wManImport.tbOptions.addButton("refresh", 100, "", "fa fa-sync green", "fa fa-sync green");
    wManImport.tbOptions.setItemToolTip('refresh', '<?php echo _l('Refresh', 1); ?>');
    wManImport.tbOptions.addText('txt_filter_name',100, '<?php echo _l('Filter by name'); ?>');
    wManImport.tbOptions.addInput("filter_name", 100, "", 100);
    wManImport.tbOptions.setItemToolTip('filter_name', '<?php echo _l('Filter by name'); ?>');
    wManImport.tbOptions.addButton("upload", 100, "", "fa fa-plus-circle green", "fa fa-plus-circle green");
    wManImport.tbOptions.setItemToolTip('upload', '<?php echo _l('Upload CSV file', 1); ?>');
    wManImport.tbOptions.addButton("delete", 100, "", "fa fa-minus-circle red", "fa fa-minus-circle red");
    wManImport.tbOptions.setItemToolTip('delete', '<?php echo _l('Delete marked files', 1); ?>');
    wManImport.tbOptions.addButton("download", 100, "", "fad fa-external-link green", "fad fa-external-link green");
    wManImport.tbOptions.setItemToolTip('download', '<?php echo _l('Download selected file', 1); ?>');
    wManImport.tbOptions.addButton("readEditCsv", 100, "", "fad fa-edit yellow", "fad fa-edit yellow");
    wManImport.tbOptions.setItemToolTip('readEditCsv', '<?php echo _l('Read and edit rows from csv file.', 1); ?>');
    wManImport.tbOptions.addButton("help", 1200, "", "fad fa-question-circle blue", "fad fa-question-circle blue");
    wManImport.tbOptions.setItemToolTip('help', '<?php echo _l('Help', 1); ?>');

    // Files Settings Toolbar Event
    wManImport.tbOptions.attachEvent("onClick",
        function (id) {
            if (id === 'help') {
                <?php echo "window.open('".getScExternalLink('support_csv_import_catalog')."');"; ?>
            }
            if (id === 'refresh') {
                displayOptionsMan();
            }
            if (id === 'win_man_import_settings') {
                openSettingsWindow('Manufacturers', 'Import');
            }
            if (id === 'download') {
                if (typeof idxFilename !== 'undefined' && idxFilename !== '') {
                    window.open("index.php?ajax=1&act=all_get-file&type=import&path=manufacturers&file=" + wManImport.gridFiles.cells(wManImport.gridFiles.getSelectedRowId(), idxFilename).getValue());
                } else {
                    dhtmlx.message({
                        text: "<?php echo _l('You should mark at least one file to download'); ?>",
                        type: 'info'
                    });
                }
            }
            if (id === 'delete') {
                let idxMarkedFile = wManImport.gridFiles.getColIndexById('markedfile');
                let filesManList = [];
                wManImport.gridFiles.forEachRow(function (id) {
                    if (Number(wManImport.gridFiles.cells(id, idxMarkedFile).getValue()) === 1) {
                        let idxCurrentFilename = wManImport.gridFiles.getColIndexById('filename');
                        filesManList.push(wManImport.gridFiles.cells(id, idxCurrentFilename).getValue());
                    }
                });
                $.post('index.php?ajax=1&act=man_win-import_process&action=conf_delete', {'imp_opt_files': filesManList.join(';')}, function (data) {
                    dhtmlx.message({text: data, type: 'info'});
                    displayOptionsMan();
                });
            }
            if (id === 'upload') {
                if (!dhxWins.isWindow("wManImportUpload")) {
                    wManImport._uploadWindow = dhxWins.createWindow("wManImportUpload", 50, 50, 585, 400);
                    wManImport._uploadWindow.setText('<?php echo _l('Upload CSV files', 1); ?>');
                    wManImport._uploadWindow.attachURL('index.php?ajax=1&act=man_win-import_upload' + "&id_lang=" + SC_ID_LANG + "&" + new Date().getTime());
                    wManImport._uploadWindow.attachEvent("onClose", function (win) {
                        win.hide();
                        return false;
                    });
                } else {
                    wManImport._uploadWindow.attachURL('index.php?ajax=1&act=man_win-import_upload' + "&id_lang=" + SC_ID_LANG + "&" + new Date().getTime());
                    wManImport._uploadWindow.show();
                    wManImport._uploadWindow.bringToTop();
                }
            }
            if (id === 'readEditCsv') {
                if (typeof idxFilename !== 'undefined' && idxFilename !== '') {
                    let filename = wManImport.gridFiles.cells(wManImport.gridFiles.getSelectedRowId(), idxFilename).getValue();
                    let fileSize = wManImport.gridFiles.getUserData(wManImport.gridFiles.getSelectedRowId(), "real_size");
                    let fieldSep = wManImport.gridFiles.cells(wManImport.gridFiles.getSelectedRowId(), idxFieldsep).getValue();
                    let forceUTF8 = wManImport.gridFiles.cells(wManImport.gridFiles.getSelectedRowId(), idxForceUTF8).getValue();
                    let nbRowEStart = 0;
                    let nbRowEnd = 20;
                    let stringAfterSAved = '<div id="export_contener" style="height: 100%; overflow: auto;">' + loader_gif + '<div id="export_message" style="padding-left: 10px;font-family: Tahoma; font-size: 11px !important; line-height: 18px;"></div></div>';

                    if (fileSize === "0") {
                        dhtmlx.message({text: "<?php echo _l('File is empty'); ?>", type: 'error'});
                        return false;
                    }
                    <?php
                    $domain = Tools::getShopDomain();
                    $url = (SC_INSTALL_MODE == 0 ? SC_PS_PATH_ADMIN_REL.'import/manufacturers/' : SC_CSV_IMPORT_DIR.'manufacturers/');
                    if ($domain == '127.0.0.1' || $domain == 'localhost')
                    {
                        $url = str_replace('\\', '/', $url);
                    }
                    ?>
                    let url = "<?php echo $url; ?>" + filename;
                    wManImport._editorWindow = dhxWins.createWindow("wManImportEditor", 50, 50, 1300, 650);
                    wManImport._editorWindow.setText('<?php echo _l('Edit rows of', 1); ?> ' + filename);
                    wManImport._editorWindow.show();
                    wManImport._editorWindow.bringToTop();

                    let ll = new dhtmlXLayoutObject(wManImport._editorWindow, "3U");
                    wManImport.leftPanel = ll.cells('a');
                    wManImport.leftPanel.setText('<?php echo _l('Raw content', 1); ?>');
                    wManImport.leftPanel.setHeight(500);
                    wManImport.winLeftToolbar = wManImport.leftPanel.attachToolbar();
                    wManImport.winLeftToolbar.setIconset('awesome');
                    wManImport.winLeftToolbar.addButton("saveLeftRows", 0, "", "fa fa-save blue", "fa fa-save blue");
                    wManImport.winLeftToolbar.setItemToolTip('saveLeftRows', '<?php echo _l('Save change', 1); ?>');
                    wManImport.leftPanel.attachHTMLString('<textarea id="rawContent" style="-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;width:100%;height:100%"></textarea>');
                    $.post("index.php?ajax=1&act=man_win-import_editor_get&" + new Date().getTime(), {
                        url: url,
                        type: "raw",
                        utf8: forceUTF8,
                        nbrowstart: nbRowEStart,
                        nbrowend: nbRowEnd,
                        fieldsep: fieldSep
                    }, function (data) {
                        $('textarea#rawContent').text(data);
                    });
                    wManImport.winLeftToolbar.attachEvent("onClick",
                        function (id) {
                            if (id === 'saveLeftRows') {
                                let csv = $('textarea#rawContent').val();
                                wManImport.bottomPanel.attachHTMLString(stringAfterSAved);
                                setTimeout(function () {
                                    wManImport.bottomPanel.attachURL("index.php?ajax=1&act=man_win-import_editor_update&" + new Date().getTime(), true, {
                                        save: 1,
                                        type: "raw",
                                        utf8: forceUTF8,
                                        nbrowstart: nbRowEStart,
                                        nbrowend: nbRowEnd,
                                        data: csv,
                                        url: url,
                                        fieldsep: fieldSep
                                    });
                                }, 1000);
                            }
                        }
                    );


                    wManImport.righPanel = ll.cells('b');
                    wManImport.righPanel.setText('<?php echo _l('CSV content', 1); ?>');
                    wManImport.righPanel.setHeight(500);
                    wManImport.winRightToolbar = wManImport.righPanel.attachToolbar();
                    wManImport.winRightToolbar.setIconset('awesome');
                    wManImport.winRightToolbar.addButton("saveRightRows", 0, "", "fa fa-save blue", "fa fa-save blue");
                    wManImport.winRightToolbar.setItemToolTip('saveRightRows', '<?php echo _l('Save change', 1); ?>');


                    wManImport.bottomPanel = ll.cells('c');
                    wManImport.bottomPanel.setText('<?php echo _l('Result', 1); ?>');
                    wManImport.bottomPanel.showHeader();
                    wManImport._editorGrid = wManImport.righPanel.attachGrid();
                    let postData = "url=" + url + "&fieldsep=" + fieldSep + "&utf8=" + forceUTF8 + "&nbrowstart=" + nbRowEStart + "&nbrowend=" + nbRowEnd;
                    wManImport._editorGrid.post("index.php?ajax=1&act=man_win-import_editor_get&" + new Date().getTime(), postData, function () {
                    }, "xml");
                    wManImport.winRightToolbar.attachEvent("onClick",
                        function (id) {
                            if (id === 'saveRightRows') {
                                wManImport._editorGrid.enableCSVHeader(true);
                                if(fieldSep == 'tab') {
                                    wManImport._editorGrid.setCSVDelimiter("{tab}");
                                } else {
                                    wManImport._editorGrid.setCSVDelimiter(fieldSep);
                                }
                                let csv = wManImport._editorGrid.serializeToCSV() + "\n";
                                wManImport.bottomPanel.attachHTMLString(stringAfterSAved);
                                setTimeout(function () {
                                    wManImport.bottomPanel.attachURL("index.php?ajax=1&act=man_win-import_editor_update&" + new Date().getTime(), true, {
                                        save: 1,
                                        type: "grid",
                                        utf8: forceUTF8,
                                        nbrowstart: nbRowEStart,
                                        nbrowend: nbRowEnd,
                                        data: csv,
                                        url: url,
                                        fieldsep: fieldSep
                                    });
                                }, 1000);
                            }
                        }
                    );
                } else {
                    dhtmlx.message({
                        text: "<?php echo _l('You should mark at least one file to edit'); ?>",
                        type: 'info'
                    });
                }
            }
        }
    );

    //Files Settings Grid
    wManImport.gridFiles = dhxlImport.cells('a').attachGrid();
    wManImport.gridFiles.setImagePath("lib/js/imgs/");

    displayOptionsMan();

    //Files Settings Events
    wManImport.gridFiles.attachEvent("onRowSelect", function (id, ind) {
        if (id !== lastManCSVFile) {
            idxFilename = wManImport.gridFiles.getColIndexById('filename');
            idxFileSize = wManImport.gridFiles.getColIndexById('size');
            idxMapping = wManImport.gridFiles.getColIndexById('mapping');
            idxLimit = wManImport.gridFiles.getColIndexById('importlimit');
            idxFieldsep = wManImport.gridFiles.getColIndexById('fieldsep');
            idxForceUTF8 = wManImport.gridFiles.getColIndexById('utf8');
            wManImport.tbProcess.setValue('importlimit', wManImport.gridFiles.cells(id, idxLimit).getValue());
            filename = wManImport.gridFiles.cells(id, idxFilename).getValue();
            mapping = wManImport.gridFiles.cells(id, idxMapping).getValue();
            dhxlImport.cells('b').setText("<?php echo _l('Mapping'); ?> " + filename);
            displayMappingMan(filename, mapping);
            getCheck();
            lastManCSVFile = id;
            setProgressBar();
        }
    });

    wManImport.gridFiles.attachEvent('onEditCell', function (stage, rId, cInd, nValue, oValue) {
        idxfieldsep = wManImport.gridFiles.getColIndexById('fieldsep');
        idxvaluesep = wManImport.gridFiles.getColIndexById('valuesep');
        if (stage == 2 && (cInd == idxfieldsep || cInd == idxvaluesep)) {
            idxFilename = wManImport.gridFiles.getColIndexById('filename');
            idxMapping = wManImport.gridFiles.getColIndexById('mapping');
            filename = wManImport.gridFiles.cells(rId, idxFilename).getValue();
            mapping = wManImport.gridFiles.cells(rId, idxMapping).getValue();
            setTimeout("displayMappingMan('" + filename + "','" + mapping + "')", 500);
        }
        return true;
    });

    wManImport.gridFilesDataProcessor = new dataProcessor('index.php?ajax=1&act=man_win-import_config_update');
    wManImport.gridFilesDataProcessor.enableDataNames(true);
    wManImport.gridFilesDataProcessor.enablePartialDataSend(true);
    wManImport.gridFilesDataProcessor.setUpdateMode('cell', true);
    wManImport.gridFilesDataProcessor.setDataColumns(Array(false, false, false, true, true, true, true, true, true, true, true, true, true, true, true, true, false));
    <?php if (_s('CAT_NOTICE_EXPORT_SEPARATOR')) { ?>
    wManImport.gridFilesDataProcessor.attachEvent("onBeforeUpdate", function (rid, status) {
        let idxfieldsep = wManImport.gridFiles.getColIndexById('fieldsep');
        let idxvaluesep = wManImport.gridFiles.getColIndexById('valuesep');
        let valField = wManImport.gridFiles.cells(rid, idxfieldsep).getValue();
        let valValue = wManImport.gridFiles.cells(rid, idxvaluesep).getValue();
        if (valField == valValue || valValue == valField) {
            dhtmlx.message({
                text: '<?php echo _l('The field separator and the value separator could not be the same character.'); ?><br/><a href="javascript:disableThisNotice(\'CAT_NOTICE_EXPORT_SEPARATOR\');"><?php echo _l('Disable this notice', 1); ?></a>',
                type: 'error'
            });
            return false;
        }
        return true;
    });
    <?php } ?>
    wManImport.gridFilesDataProcessor.attachEvent("onAfterUpdate", function (id, status) {
        getCheck();
        return true;
    });
    wManImport.gridFilesDataProcessor.init(wManImport.gridFiles);


    // Mapping
    dhxlImport.cells('b').setText("<?php echo _l('Mapping'); ?>");
    dhxlImport.cells('b').setWidth(650);

    // Mapping Toolbar
    wManImport.tbMapping = dhxlImport.cells('b').attachToolbar();
    wManImport.tbMapping.setIconset('awesome');
    wManImport.tbMapping.addButton("load_by_name", 0, "", "fad fa-bolt green", "fad fa-bolt green");
    wManImport.tbMapping.setItemToolTip('load_by_name', '<?php echo _l('Load fields by name', 1); ?>');
    wManImport.tbMapping.addButton("delete", 0, "", "fa fa-minus-circle red", "fa fa-minus-circle red");
    wManImport.tbMapping.setItemToolTip('delete', '<?php echo _l('Delete mapping and reset grid'); ?>');
    wManImport.tbMapping.addButton("saveasbtn", 0, "", "fa fa-save blue", "fa fa-save blue");
    wManImport.tbMapping.setItemToolTip('saveasbtn', '<?php echo _l('Save mapping'); ?>');
    wManImport.tbMapping.addInput("saveas", 0, "", 200);
    wManImport.tbMapping.setItemToolTip('saveas', '<?php echo _l('Save mapping as'); ?>');
    wManImport.tbMapping.addText('txt_saveas', 0, '<?php echo _l('Save mapping as'); ?>');
    var opts = [
        <?php
        @$files = array_diff(scandir(SC_CSV_IMPORT_DIR), array_merge(array('.', '..', 'index.php', '.htaccess', SC_CSV_IMPORT_CONF)));
        $content = '';
        foreach ($files as $file)
        {
            if (substr($file, strlen($file) - 8, 8) == '.map.xml')
            {
                $file = str_replace('.map.xml', '', $file);
                $content .= "['loadmapping".$file."', 'obj', '".$file."', ''],";
            }
        }
        if ($content == '')
        {
            echo "['0', 'obj', '"._l('No map available')."', ''],";
        }
        echo substr($content, 0, -1);
        ?>
    ];
    wManImport.tbMapping.addButtonSelect("loadmapping", 0, "<?php echo _l('Load'); ?>", opts, "fad fa-american-sign-language-interpreting blue", "fad fa-american-sign-language-interpreting blue", false, true);
    wManImport.tbMapping.setItemToolTip('loadmapping', '<?php echo _l('Load mapping'); ?>');
    wManImport.tbMapping.addButton("refresh", 0, "", "fa fa-sync green", "fa fa-sync green");
    wManImport.tbMapping.setItemToolTip('refresh', '<?php echo _l('Refresh'); ?>');

    // Mapping Toolbar Event
    wManImport.tbMapping.attachEvent("onClick", function (id) {
        if (id.substr(0, 11) === 'loadmapping') {
            let tmp = id.substr(11, id.length).replace('.map.xml', '');
            wManImport.tbMapping.setValue('saveas', tmp);
            $.get('index.php?ajax=1&act=man_win-import_process&action=mapping_load&filename=' + tmp, function (data) {
                if (data !== '') {
                    mapping = data.split(';');
                    wManImport.gridMapping.forEachRow(function (id) {
                        wManImport.gridMapping.cells(wManImport.gridMapping.getRowIndex(id), 0).setValue("0");
                        wManImport.gridMapping.cells(wManImport.gridMapping.getRowIndex(id), 2).setValue("");
                        wManImport.gridMapping.cells(wManImport.gridMapping.getRowIndex(id), 3).setValue("");

                        if (wManImport.gridMapping.cells(wManImport.gridMapping.getRowIndex(id), 1).getValue() !== '')
                            for (var i = 0; i < mapping.length; i++) {
                                map = (mapping[i]).split(',');
                                if (wManImport.gridMapping.cells(wManImport.gridMapping.getRowIndex(id), 1).getValue() == map[0]) {
                                    wManImport.gridMapping.cells(wManImport.gridMapping.getRowIndex(id), 0).setValue("1");
                                    wManImport.gridMapping.cells(wManImport.gridMapping.getRowIndex(id), 2).setValue(map[1]);

                                    <?php
                                    ## Todo pas pour le moment. utile?
                                    ## sc_ext::readImportCSVConfigXML('importMappingLoadMappingOption');
                                    ?>
                                    wManImport.gridMapping.cells(wManImport.gridMapping.getRowIndex(id), 3).setValue(map[2]);
                                }
                            }
                    });
                }
                getCheck();
                setOptionsBGColorMan();
            });
        }
        if (id === 'refresh') {
            if (typeof filename == 'undefined') return;
            if (typeof mapping == 'undefined') {
                idxMapping = wManImport.gridFiles.getColIndexById('mapping');
                mapping = wManImport.gridFiles.cells(lastManCSVFile, idxMapping).getValue();
                //if (mapping=='') return;
            }
            displayMappingMan(filename, mapping);
            getCheck();
        }
        if (id === 'load_by_name') {
            comboArray = new Object();
            comboValuesArray = new Object();
            optionLabelArray = new Object();
            $.each(comboDBField.getKeys(), function (num, value) {
                var label = comboDBField.get(value);
                if (label != undefined && label != null && label != "" && label != 0) {
                    comboArray[label] = value;
                    comboValuesArray[value] = value;

                    if (in_array(value, arrayFieldOption))
                        optionLabelArray[value] = label;
                }
            });

            idxFileField = wManImport.gridMapping.getColIndexById('file_field');
            idxDBField = wManImport.gridMapping.getColIndexById('db_field');
            idxOptions = wManImport.gridMapping.getColIndexById('options');
            idxUse = wManImport.gridMapping.getColIndexById('use');

            wManImport.gridMapping.forEachRow(function (row_id) {
                var name = $.trim(wManImport.gridMapping.cells(row_id, idxFileField).getValue());
                var field = wManImport.gridMapping.cells(row_id, idxDBField).getValue();
                name = replaceAll("&amp;", "&", name);

                if (name != undefined && name != null && name != "" && name != 0 && field != undefined && (field == null || field == "" || field == 0)) {
                    // check field image
                    var patt = new RegExp("image_id");
                    var isImgId = patt.test(name);
                    if (isImgId)
                        name = "image_id";

                    var check = false;
                    var value = comboArray[name];
                    var value_bis = comboValuesArray[name];

                    without_supplier = false;
                    <?php if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) { ?>
                    if (value === 'supplier_reference' || value === 'wholesale_price') {
                        value = undefined;
                        value_bis = undefined;
                        name = name + " noneeee";
                    }
                    <?php } ?>

                    if (value != undefined && value != null && value != "" && value != 0) {
                        wManImport.gridMapping.cells(row_id, idxDBField).setValue(value);
                        check = true;
                    } else if (value_bis != undefined && value_bis != null && value_bis != "" && value_bis != 0) {
                        wManImport.gridMapping.cells(row_id, idxDBField).setValue(value_bis);
                        value = value_bis;
                        check = true;
                    } else {
                        // check field image
                        var patt = new RegExp("image_legend");
                        var isImgLegend = patt.test(name);
                        if (isImgLegend)
                            name = $.trim(name.substring(0, name.length - 2));

                        var original_name = name;
                        var lang = $.trim(name.slice(-2).toLowerCase());
                        name = $.trim(name.substring(0, name.length - 3));

                        var patt = new RegExp("link_to_image");
                        var isImg = patt.test(name);
                        var patt = new RegExp("link_to_cover_image");
                        var isImg_bis = patt.test(name);
                        var patt = new RegExp("image_link");
                        var isImg_ter = patt.test(name);
                        if (isImg || isImg_bis || isImg_ter) {
                            wManImport.gridMapping.cells(row_id, idxDBField).setValue("imageURL");
                            value = "imageURL";
                            check = true;
                        } else {
                            var value = comboArray[name];
                            var value_bis = comboValuesArray[name];
                            if (value != undefined && value != null && value != "" && value != 0) {
                                wManImport.gridMapping.cells(row_id, idxDBField).setValue(value);
                                check = true;
                            } else if (value_bis != undefined && value_bis != null && value_bis != "" && value_bis != 0) {
                                wManImport.gridMapping.cells(row_id, idxDBField).setValue(value_bis);
                                value = value_bis;
                                check = true;
                            } else {
                                var encoded_name = unescape(encodeURIComponent(name));
                                var value = comboArray[encoded_name];
                                var value_bis = comboValuesArray[encoded_name];
                                if (value != undefined && value != null && value != "" && value != 0) {
                                    wManImport.gridMapping.cells(row_id, idxDBField).setValue(value);
                                    check = true;
                                } else if (value_bis != undefined && value_bis != null && value_bis != "" && value_bis != 0) {
                                    wManImport.gridMapping.cells(row_id, idxDBField).setValue(value_bis);
                                    value = value_bis;
                                    check = true;
                                } else {
                                    var decoded_name = decodeURIComponent(unescape(name));
                                    var value = comboArray[decoded_name];
                                    var value_bis = comboValuesArray[decoded_name];
                                    if (value != undefined && value != null && value != "" && value != 0) {
                                        wManImport.gridMapping.cells(row_id, idxDBField).setValue(value);
                                        check = true;
                                    } else if (value_bis != undefined && value_bis != null && value_bis != "" && value_bis != 0) {
                                        wManImport.gridMapping.cells(row_id, idxDBField).setValue(value_bis);
                                        value = value_bis;
                                        check = true;
                                    }
                                }
                            }

                            if (in_array(value, arrayFieldLang)) {
                                wManImport.gridMapping.cells(row_id, idxOptions).setValue(lang);
                                onEditCellmappingMan(2, row_id, idxOptions, lang);
                            }
                            if (!check) {
                                $.each(optionLabelArray, function (id, label) {
                                    var finded = false;
                                    var option = "";
                                    if (name.search(label) >= 0) {
                                        finded = true;
                                        <?php if (SCAS) { ?>
                                        if (id === 'quantity' || id === 'location' || id === 'add_quantity' || id === 'remove_quantity' || id === 'supplier_reference' || id === 'wholesale_price' || id === 'quantity_on_sale')
                                            option = $.trim(original_name.replace(label + " ", ""));
                                        <?php }
                                    else
                                    { ?>
                                        if (id === 'supplier_reference' || id === 'wholesale_price')
                                            option = $.trim(original_name.replace(label + " ", ""));
                                        <?php } ?>
                                        else
                                            option = $.trim(name.replace(label + " ", ""));
                                        <?php if (SCAS) { ?>
                                        if (id === 'quantity') {
                                            if (
                                                name.search('<?php echo _l('physical stock', 1); ?>') >= 0
                                                || name.search('<?php echo _l('available stock', 1); ?>') >= 0
                                                || name.search('<?php echo _l('live stock', 1); ?>') >= 0
                                            ) {
                                                finded = false;
                                                option = "";
                                            }
                                        }
                                        <?php } ?>
                                    } else {
                                        var encoded_name = unescape(encodeURIComponent(name));
                                        if (encoded_name.search(label) >= 0) {
                                            finded = true;
                                            <?php if (SCAS) { ?>
                                            if (id === 'quantity' || id === 'location' || id === 'add_quantity' || id === 'remove_quantity' || id === 'supplier_reference' || id === 'wholesale_price' || id === 'quantity_on_sale')
                                                option = $.trim(original_name.replace(label + " ", ""));
                                            <?php }
                                    else
                                    { ?>
                                            if (id === 'supplier_reference' || id === 'wholesale_price')
                                                option = $.trim(original_name.replace(label + " ", ""));
                                            <?php } ?>
                                            else
                                                option = $.trim(encoded_name.replace(label + " ", ""));
                                            <?php if (SCAS) { ?>
                                            if (id === 'quantity') {
                                                if (
                                                    encoded_name.search('<?php echo _l('physical stock', 1); ?>') >= 0
                                                    || encoded_name.search('<?php echo _l('available stock', 1); ?>') >= 0
                                                    || encoded_name.search('<?php echo _l('live stock', 1); ?>') >= 0
                                                ) {
                                                    finded = false;
                                                    option = "";
                                                }
                                            }
                                            <?php } ?>
                                        } else {
                                            var decoded_name = decodeURIComponent(unescape(name));
                                            if (encoded_name.search(label) >= 0) {
                                                finded = true;
                                                <?php if (SCAS) { ?>
                                                if (id === 'quantity' || id === 'location' || id === 'add_quantity' || id === 'remove_quantity' || id === 'supplier_reference' || id === 'wholesale_price' || id === 'quantity_on_sale')
                                                    option = $.trim(original_name.replace(label + " ", ""));
                                                <?php }
                                    else
                                    { ?>
                                                if (id === 'supplier_reference' || id === 'wholesale_price')
                                                    option = $.trim(original_name.replace(label + " ", ""));
                                                <?php } ?>
                                                else
                                                    option = $.trim(decoded_name.replace(label + " ", ""));
                                            }
                                        }
                                    }

                                    if (finded) {
                                        wManImport.gridMapping.cells(row_id, idxDBField).setValue(id);
                                        value = id;
                                        check = true;

                                        if (option != undefined && option != null && option != "") {
                                            wManImport.gridMapping.cells(row_id, idxOptions).setValue(option);
                                        }
                                    }
                                });
                            }
                        }
                    }

                    if (check)
                        onEditCellmappingMan(2, row_id, idxDBField, value);
                }
            });
        }
        if (id === 'saveasbtn') {
            if (wManImport.tbMapping.getValue('saveas') === '') {
                dhtmlx.message({text: '<?php echo _l('Mapping name should not be empty'); ?>', type: 'error'});
            } else {
                var mapping = '';
                wManImport.gridMapping.forEachRow(function (id) {
                    if (wManImport.gridMapping.cells(wManImport.gridMapping.getRowIndex(id), 0).getValue() == "1") {
                        mapping += wManImport.gridMapping.cells(wManImport.gridMapping.getRowIndex(id), 1).getValue() + ',' +
                            wManImport.gridMapping.cells(wManImport.gridMapping.getRowIndex(id), 2).getValue() + ',' +
                            wManImport.gridMapping.cells(wManImport.gridMapping.getRowIndex(id), 3).getValue() + ';';
                    }
                });
                wManImport.tbMapping.setValue('saveas', getLinkRewriteFromStringLightWithCase(wManImport.tbMapping.getValue('saveas')));
                $.post('index.php?ajax=1&act=man_win-import_mapping_update&action=mapping_saveas', {
                    'filename': wManImport.tbMapping.getValue('saveas'),
                    'mapping': mapping
                }, function (data) {
                    dhtmlx.message({text: data, type: 'info'});
                    if (!in_array('loadmapping' + wManImport.tbMapping.getValue('saveas'), wManImport.tbMapping.getAllListOptions('loadmapping'))) {
                        wManImport.tbMapping.addListOption('loadmapping', 'loadmapping' + wManImport.tbMapping.getValue('saveas'), 0, 'button', wManImport.tbMapping.getValue('saveas'))
                        wManImport.tbMapping.setListOptionSelected('loadmapping', 'loadmapping' + wManImport.tbMapping.getValue('saveas'));
                    }
                    displayOptionsMan();
                    getCheck();
                });
            }
        }
        if (id === 'delete') {
            if (wManImport.tbMapping.getValue('saveas') === '') {
                dhtmlx.message({text: '<?php echo _l('Mapping name should not be empty'); ?>', type: 'error'});
            } else {
                if (confirm('<?php echo _l('Do you want to delete the current mapping?', 1); ?>'))
                    $.get('index.php?ajax=1&act=man_win-import_mapping_update&action=mapping_delete&filename=' + wManImport.tbMapping.getValue('saveas'), function (data) {
                        wManImport.gridMapping.clearAll(true);
                        wManImport.tbMapping.removeListOption('loadmapping', 'loadmapping' + wManImport.tbMapping.getValue('saveas'));
                        wManImport.tbMapping.setValue('saveas', '');
                        wManImport.tbMapping.callEvent("onClick", ['refresh']);
                        getCheck();
                    });
            }
        }
    });

    // Mapping Grid
    wManImport.gridMapping = dhxlImport.cells('b').attachGrid();
    wManImport.gridMapping.setImagePath("lib/js/imgs/");

    // Mapping Grid Event
    wManImport.gridMapping.attachEvent('onEditCell', function (stage, rId, cInd, nValue, oValue) {
        if (stage == 1 && (cInd == 2 || cInd == 3)) {
            var editor = this.editor;
            var pos = this.getPosition(editor.cell);
            var y = document.body.offsetHeight - pos[1];
            if (y < editor.list.offsetHeight)
                editor.list.style.top = pos[1] - editor.list.offsetHeight + 'px';
        }
        idxMark = wManImport.gridMapping.getColIndexById('use');
        idxDBField = wManImport.gridMapping.getColIndexById('db_field');
        idxOptions = wManImport.gridMapping.getColIndexById('options');
        comboDBField = wManImport.gridMapping.getCombo(idxOptions);
        if (cInd == idxDBField && nValue != oValue) {
            wManImport.gridMapping.cells(rId, idxMark).setValue(1);
            setOptionsBGColorMan();
        }
        if (cInd == idxOptions) {
            comboDBField.clear();
            if (in_array(wManImport.gridMapping.cells(rId, idxDBField).getValue(), arrayFieldLang)) {
                <?php
                foreach ($languages as $lang)
                {
                    echo '          comboDBField.put("'.$lang['iso_code'].'","'.$lang['iso_code'].'");';
                }
                ?>
                return true;
            }
            <?php
            ## Todo pas pour le moment. utile?
            ## sc_ext::readImportCSVConfigXML('importMappingFillCombo');
            ?>
            return false;
        }
        return true;
    });


    // Result
    dhxlImport.cells('c').setText("<?php echo _l('Process'); ?>");

    // Result Toolbar
    wManImport.tbProcess = dhxlImport.cells('c').attachToolbar();
    wManImport.tbProcess.setIconset('awesome');
    var start_import = 0;
    wManImport.tbProcess.addButton("loop_tool", 0, "", "fa fa-clock", "fa fa-clock");
    wManImport.tbProcess.setItemToolTip('loop_tool', '<?php echo _l('Auto-import tool'); ?>');
    wManImport.tbProcess.addButton("go_process", 0, "", "fad fa-sign-in", "fad fa-sign-in");
    wManImport.tbProcess.setItemToolTip('go_process', '<?php echo _l('Import data'); ?>');
    wManImport.tbProcess.addSeparator("sep01", 0);
    wManImport.tbProcess.addButton("check", 0, "", "fa fa-check-circle green", "fa fa-check-circle green");
    wManImport.tbProcess.setItemToolTip('check', '<?php echo _l('Votre import est-il pr�t ?'); ?>');
    wManImport.tbProcess.addSeparator("sep02", 0);
    wManImport.tbProcess.addInput("importlimit", 0, 500, 30);
    wManImport.tbProcess.setItemToolTip('importlimit', '<?php echo _l('Number of the first lines to import from the CSV file'); ?>');
    $(wManImport.tbProcess.getInput('importlimit')).change(function () {
        getCheck();
    });
    wManImport.tbProcess.addText('txtimportlimit', 0, '<?php echo _l('Lines to import')._l(':'); ?>');

    // Result Toolbar Event
    wManImport.tbProcess.attachEvent("onClick",
        function (id) {
            if (id == 'check') {
                window.open("<?php echo getScExternalLink('support_csv_import_checklist'); ?>");
            }
            if (id == 'go_process') {
                if (!autoImportRunning) {
                    displayProcessMan();
                } else {
                    dhtmlx.message({text: '<?php echo _l('AutoImport already running'); ?>', type: 'error'});
                }
            }
            if (id == 'loop_tool') {
                displayAutoImportTool();
            }
        }
    );

    // Functions display
    function displayOptionsMan(callback) {
        wManImport.gridFiles.clearAll(true);
        wManImport.gridFiles.load("index.php?ajax=1&act=man_win-import_config_get&id_lang=" + SC_ID_LANG + "&" + new Date().getTime(), function () {
            filterScriptName();
            if (callback) {
                eval(callback);
            } else if (lastManCSVFile !== '') {
                wManImport.gridFiles.selectRowById(lastManCSVFile);
            }
        });
    }

    function displayMappingMan(filename, mappingMan) {
        wManImport.gridMapping.clearAll(true);
        wManImport.gridMapping.load("index.php?ajax=1&act=man_win-import_mapping_get&id_lang=" + SC_ID_LANG + "&imp_opt_file=" + filename + "&" + new Date().getTime(), function () {
            let idxDBField = wManImport.gridMapping.getColIndexById('db_field');
            comboDBField = wManImport.gridMapping.getCombo(idxDBField);
            comboDBField.clear();
            <?php
            global $array;
            $array = array();
            $array[_l('id_manufacturer', 1)] = "comboDBField.put('id_manufacturer','"._l('id_manufacturer', 1)."');";
            $array[_l('active', 1)] = "comboDBField.put('active','"._l('active', 1)."');";
            $array[_l('name', 1)] = "comboDBField.put('name','"._l('name', 1)."');";
            $array[_l('description', 1)] = "comboDBField.put('description','"._l('description', 1)."');";
            $array[_l('description_short', 1)] = "comboDBField.put('short_description','"._l('description_short', 1)."');";
            $array[_l('meta_title', 1)] = "comboDBField.put('meta_title','"._l('meta_title', 1)."');";
            $array[_l('meta_description', 1)] = "comboDBField.put('meta_description','"._l('meta_description', 1)."');";
            $array[_l('meta_keywords', 1)] = "comboDBField.put('meta_keywords','"._l('meta_keywords', 1)."');";
            $array[_l('imageURL', 1)] = "comboDBField.put('imageURL','"._l('imageURL', 1)."');";
            if (SCMS)
            {
                $array['id_shop_list'] = "comboDBField.put('id_shop_list','id_shop_list');";
            }

            ## Todo pas pour le moment. utile?
            ## sc_ext::readImportCSVConfigXML('definition');

            ksort($array);
            echo join("\n", $array);
            ?>
            if (mappingMan !== '') {
                onClickMappingMan('loadmapping' + mappingMan);
            } else {
                onClickMappingMan('loadmapping' + filename.replace('.csv', '').replace('.CSV', ''));
            }
        });
    }

    function displayProcessMan() {
        start_import = 1;
        var mapping = '';
        if (!checkOptions() || lastManCSVFile == '') {
            dhtmlx.message({text: '<?php echo _l('Some options are missing'); ?>', type: 'error'});
            return false;
        }
        wManImport.gridMapping.forEachRow(function (id) {
            if (wManImport.gridMapping.cells(wManImport.gridMapping.getRowIndex(id), 0).getValue() == "1") {
                mapping += wManImport.gridMapping.cells(wManImport.gridMapping.getRowIndex(id), 1).getValue() + ',' +
                    wManImport.gridMapping.cells(wManImport.gridMapping.getRowIndex(id), 2).getValue() + ',' +
                    wManImport.gridMapping.cells(wManImport.gridMapping.getRowIndex(id), 3).getValue() + ';';
            }
        });
        mapping = mapping.substr(0, mapping.length - 1);
        autoImportLastState = 1;
        setProgressBar();
        var needToReload = 0;
        let jqhxhr_process = $.post('index.php?ajax=1&act=man_win-import_process&action=mapping_process', {
            'mapping': mapping,
            'filename': lastManCSVFile,
            'importlimit': wManImport.tbProcess.getValue('importlimit'),
        }, function (data) {
            document.onselectstart = new Function("return true;");
            dhxlImport.cells('c').attachHTMLString(data);
            if (start_import === 1) {
                if ($('#progress_bar').length) {
                    let regex = /(<b id="process_ending">)/g;
                    let process_ending_good = data.match(regex);
                    if (process_ending_good) {
                        $('#progress_bar > #processed').css({"width": '100%'});
                        $('#progress_bar > #processed').text(' - 100%');
                    } else {
                        needToReload = 1;
                    }
                }
            }
        }).fail(function (data) {
            dhxlImport.cells('c').attachHTMLString(data.responseText);
        });

        jqhxhr_process.always(function () {
            window.clearInterval(progress_interval);
            start_import = 0;
            if (needToReload === 1) {
                setProgressBar();
            } else {
                $.post('index.php?ajax=1&act=man_win-import_progressbar', {'file': lastManCSVFile}, function (data) {
                    $('#progress_bar > #processed').css({"width": data + '%'});
                    $('#progress_bar > #processed').text(' - ' + data + '%');
                    $('#progress_bar').removeClass('in_process_awesome');
                });
            }
        });
        getProgressBar();

        setTimeout("displayOptionsMan('wManImport.gridFiles.selectRowById(getTODOName(lastManCSVFile), false, true, false)');", 500);
    }

    function displayAutoImportTool() {
        if (!dhxWins.isWindow("wManAutoImport")) {
            wManAutoImport = dhxWins.createWindow("wManAutoImport", 550, 350, 220, 68);
            wManAutoImport.setMinDimension(220, 68);
            wManAutoImport.setText("<?php echo _l('Auto-import tool'); ?>");
            wManAutoImport.button('park').hide();
            wManAutoImport.button('minmax').hide();
            wManAutoImport._tb = wManAutoImport.attachToolbar();
            wManAutoImport._tb.setIconset('awesome');
            wManAutoImport._tb.addButton("help", 0, "", "fad fa-question-circle blue", "fad fa-question-circle blue");
            wManAutoImport._tb.setItemToolTip('help', '<?php echo _l('Help', 1); ?>');
            wManAutoImport._tb.addText('txtsecs', 0, '<?php echo _l('sec'); ?>');
            wManAutoImport._tb.addInput("importinterval", 0, 60, 30);
            wManAutoImport._tb.setItemToolTip('importinterval', '<?php echo _l('Launch import every X seconds if possible', 1); ?>');
            wManAutoImport._tb.addText('txtinterval', 0, '<?php echo _l('Interval:', 1); ?>');
            wManAutoImport._tb.addButtonTwoState("play", 0, "", "fad fa-play-circle blue", "fad fa-play-circle blue");
            wManAutoImport._tb.setItemToolTip('play', '<?php echo _l('Start', 1); ?>');
            wManAutoImport._tb.attachEvent("onClick",
                function (id) {
                    if (id == 'help') {
                        <?php echo "window.open('".getScExternalLink('support_csv_auto_import')."');"; ?>
                    }
                    if (id == 'stop') {
                        stopAutoImportMan();
                    }
                });
            wManAutoImport._tb.attachEvent("onStateChange", function (id, state) {
                if (id == 'play') {
                    if (state) {
                        startAutoImport();
                    } else {
                        stopAutoImportMan();
                    }
                }
            });
            wManAutoImport._tb.setListOptionSelected("alertsound", 0);
            wManAutoImport._tb.setListOptionSelected("alertvisual", 0);
            wManAutoImport.attachObject('alertbox');
        } else {
            wManAutoImport.bringToTop();
        }
    }

    // Functions progressbar
    function setProgressBar() {
        if ($('#progress_bar').length) {
            $('#progress_bar').remove();
        }
        wManImport._sb.setText('<div id="progress_bar" data-bar="<?php echo _l('Skipped lines or lines to be processed', 1); ?>"><div id="processed" data-processed="<?php echo _l('Processed lines', 1); ?>"></div></div>');
        callEasterEgg();
    }

    function callEasterEgg() {
        $('#progress_bar').click(function () {
            $('body').append('<div class="easteregg"><img src="../SC/lib/img/easteregg.gif" height="100%" width="150"></div>');
            $('.easteregg').click(function () {
                $(this).remove();
            });
            return false;
        });
    }

    function getProgressBar() {
        if (start_import === 1) {
            $('#progress_bar').addClass('in_process_awesome');
            progress_interval = window.setInterval(function () {
                $.post('index.php?ajax=1&act=man_win-import_progressbar', {'file': lastManCSVFile}, function (data) {
                    if (data <= 100) {
                        $('#progress_bar > #processed').css({"width": data + '%'});
                        $('#progress_bar > #processed').text(' - ' + data + '%');
                    }
                });
            }, 3000);
        }
    }

    // Functions autoimport
    function startAutoImport() {
        autoImportUnit = 0;
        autoImportRunning = true;
        autoImportTODOSize1 = 0;
        autoImportTODOSize2 = 0;
        processAutoImport();
        displayProcessMan();
    }

    function stopAutoImportMan(showAlert) {
        if (dhxWins.isWindow("wManAutoImport")) {
            autoImportUnit = 0;
            autoImportRunning = false;
            autoImportTODOSize1 = 0;
            autoImportTODOSize2 = 0;
            autoImportLastState = 0;
            wManAutoImport._tb.setItemState('play', false);
            if (showAlert) {
                $('#alertbox').css('background-color', '#FF0000');
                wManAutoImport.setDimension(350, 168);
            }
        }
    }

    function processAutoImport() {
        if (!dhxWins.isWindow("wManAutoImport")) stopAutoImportMan();
        if (!autoImportRunning) return 0;
        autoImportUnit++;
        if (autoImportUnit >= wManAutoImport._tb.getValue('importinterval') * 1) {
            if (autoImportLastState == 1 || (autoImportTODOSize1 > 0 && autoImportTODOSize1 == autoImportTODOSize2)) { // still waiting reply OR TODO file didn't change
                stopAutoImportMan(true);
                return 0;
            }
            autoImportUnit = 0;
            displayProcessMan();
        }
        setTimeout('processAutoImport()', 1000);
    }

    // Functions tools
    function onEditCellmappingMan(stage, rId, cInd, nValue, oValue) {
        if (stage === 1 && (cInd === 2 || cInd === 3)) {
            var editor = this.editor;
            var pos = this.getPosition(editor.cell);
            var y = document.body.offsetHeight - pos[1];
            if (y < editor.list.offsetHeight)
                editor.list.style.top = pos[1] - editor.list.offsetHeight + 'px';
        }
        let idxMark = wManImport.gridMapping.getColIndexById('use');
        let idxDBField = wManImport.gridMapping.getColIndexById('db_field');
        let idxOptions = wManImport.gridMapping.getColIndexById('options');
        comboDBField = wManImport.gridMapping.getCombo(idxOptions);
        if (cInd == idxDBField && nValue != oValue) {
            wManImport.gridMapping.cells(rId, idxMark).setValue(1);
            setOptionsBGColorMan();
        }
        if (cInd == idxOptions) {
            comboDBField.clear();
            <?php
            foreach ($languages as $lang)
            {
                echo '          comboDBField.put("'.$lang['iso_code'].'","'.$lang['iso_code'].'");';
            }
            $features = Feature::getFeatures($sc_agent->id_lang);
            foreach ($features as $feature)
            {
                echo '          comboDBField.put("'.addslashes($feature['name']).'","'.addslashes($feature['name']).'");';
            }
            ## Todo pas pour le moment. utile?
            ## sc_ext::readImportCustomerCSVConfigXML('importmappingManFillCombo');
            ?>
            return false;
        }
        return true;
    }

    function onClickMappingMan(id) {
        if (id.substr(0, 11) === 'loadmapping') {
            let tmp = id.substr(11, id.length).replace('.map.xml', '');
            wManImport.tbMapping.setValue('saveas', tmp);
            $.get('index.php?ajax=1&act=man_win-import_process&action=mapping_load&filename=' + tmp, function (data) {
                if (data != '') {
                    mappingMan = data.split(';');
                    wManImport.gridMapping.forEachRow(function (id) {
                        if (wManImport.gridMapping.cells(wManImport.gridMapping.getRowIndex(id), 1).getValue() != '')
                            for (var i = 0; i < mappingMan.length; i++) {
                                map = (mappingMan[i]).split(',');
                                if (wManImport.gridMapping.cells(wManImport.gridMapping.getRowIndex(id), 1).getValue() == map[0]) {


                                    wManImport.gridMapping.cells(wManImport.gridMapping.getRowIndex(id), 0).setValue("1");
                                    wManImport.gridMapping.cells(wManImport.gridMapping.getRowIndex(id), 2).setValue(map[1]);
                                    wManImport.gridMapping.cells(wManImport.gridMapping.getRowIndex(id), 3).setValue(map[2]);
                                }
                            }
                    });
                }
                setOptionsBGColorMan();
            });
        }
        if (id === 'refresh') {
            if (typeof filename == 'undefined') return;
            if (typeof mappingMan == 'undefined') {
                idxmappingMan = wManImport.gridFiles.getColIndexById('mapping');
                mappingMan = wManImport.gridFiles.cells(lastManCSVFileMan, idxmappingMan).getValue();
            }
            displayMappingMan(filename, mappingMan);
        }
        if (id === 'saveasbtn') {
            if (wManImport.tbMapping.getValue('saveas') === '') {
                dhtmlx.message({text: '<?php echo _l('mapping name should not be empty'); ?>', type: 'error'});
            } else {
                var mappingMan = '';
                wManImport.gridMapping.forEachRow(function (id) {
                    if (wManImport.gridMapping.cells(wManImport.gridMapping.getRowIndex(id), 0).getValue() == "1") {
                        mappingMan += wManImport.gridMapping.cells(wManImport.gridMapping.getRowIndex(id), 1).getValue() + ',' +
                            wManImport.gridMapping.cells(wManImport.gridMapping.getRowIndex(id), 2).getValue() + ',' +
                            wManImport.gridMapping.cells(wManImport.gridMapping.getRowIndex(id), 3).getValue() + ';';
                    }
                });
                wManImport.tbMapping.setValue('saveas', getLinkRewriteFromStringLight(wManImport.tbMapping.getValue('saveas')));
                $.post('index.php?ajax=1&act=man_win-import_process&action=mapping_saveas', {
                    'filename': wManImport.tbMapping.getValue('saveas'),
                    'mapping': mappingMan
                }, function (data) {
                    dhtmlx.message({text: data, type: 'info'});
                    if (!in_array('loadmapping' + wManImport.tbMapping.getValue('saveas'), wManImport.tbMapping.getAllListOptions('loadmapping'))) {
                        wManImport.tbMapping.addListOption('loadmapping', 'loadmapping' + wManImport.tbMapping.getValue('saveas'), 0, 'button', wManImport.tbMapping.getValue('saveas'))
                        wManImport.tbMapping.setListOptionSelected('loadmapping', 'loadmapping' + wManImport.tbMapping.getValue('saveas'));
                    }
                    displayOptionsMan();
                });
            }
        }
        if (id === 'delete') {
            if (wManImport.tbMapping.getValue('saveas') === '') {
                dhtmlx.message({text: '<?php echo _l('mapping name should not be empty'); ?>', type: 'error'});
            } else {
                if (confirm('<?php echo _l('Do you want to delete the current mapping?', 1); ?>'))
                    $.get('index.php?ajax=1&act=man_win-import_process&action=mapping_delete&filename=' + wManImport.tbMapping.getValue('saveas'), function (data) {
                        wManImport.gridMapping.clearAll(true);
                        wManImport.tbMapping.removeListOption('loadmapping', 'loadmapping' + wManImport.tbMapping.getValue('saveas'));
                        wManImport.tbMapping.setValue('saveas', '');
                    });
            }
        }
        if (id === 'load_by_name') {
            comboArrayMan = new Object();
            comboValuesArrayMan = new Object();
            optionLabelArrayMan = new Object();
            $.each(comboDBField.getKeys(), function (num, value) {
                var label = comboDBField.get(value);
                if (label != undefined && label != null && label != "" && label != 0) {
                    comboArrayMan[label] = value;
                    comboValuesArrayMan[value] = value;

                    if (in_array(value, arrayFieldOptionMan))
                        optionLabelArrayMan[value] = label;
                }
            });

            idxFileField = wManImport.gridMapping.getColIndexById('file_field');
            idxDBField = wManImport.gridMapping.getColIndexById('db_field');
            idxOptions = wManImport.gridMapping.getColIndexById('options');
            idxUse = wManImport.gridMapping.getColIndexById('use');

            wManImport.gridMapping.forEachRow(function (row_id) {
                var name = $.trim(wManImport.gridMapping.cells(row_id, idxFileField).getValue());
                var field = wManImport.gridMapping.cells(row_id, idxDBField).getValue();

                if (name != undefined && name != null && name != "" && name != 0 && field != undefined && (field == null || field == "" || field == 0)) {
                    var check = false;
                    var value = comboArrayMan[name];
                    var value_bis = comboValuesArrayMan[name];
                    if (value != undefined && value != null && value != "" && value != 0) {
                        wManImport.gridMapping.cells(row_id, idxDBField).setValue(value);
                        check = true;
                    } else if (value_bis != undefined && value_bis != null && value_bis != "" && value_bis != 0) {
                        wManImport.gridMapping.cells(row_id, idxDBField).setValue(value_bis);
                        value = value_bis;
                        check = true;
                    } else {
                        var original_name = name;
                        var lang = $.trim(name.slice(-2).toLowerCase());
                        name = $.trim(name.substring(0, name.length - 3));
                        var value = comboArrayMan[name];
                        var value_bis = comboValuesArrayMan[name];
                        if (value != undefined && value != null && value != "" && value != 0) {
                            wManImport.gridMapping.cells(row_id, idxDBField).setValue(value);
                            check = true;
                        } else if (value_bis != undefined && value_bis != null && value_bis != "" && value_bis != 0) {
                            wManImport.gridMapping.cells(row_id, idxDBField).setValue(value_bis);
                            value = value_bis;
                            check = true;
                        } else {
                            var encoded_name = unescape(encodeURIComponent(name));
                            var value = comboArrayMan[encoded_name];
                            var value_bis = comboValuesArrayMan[encoded_name];
                            if (value != undefined && value != null && value != "" && value != 0) {
                                wManImport.gridMapping.cells(row_id, idxDBField).setValue(value);
                                check = true;
                            } else if (value_bis != undefined && value_bis != null && value_bis != "" && value_bis != 0) {
                                wManImport.gridMapping.cells(row_id, idxDBField).setValue(value_bis);
                                value = value_bis;
                                check = true;
                            } else {
                                var decoded_name = decodeURIComponent(unescape(name));
                                var value = comboArrayMan[decoded_name];
                                var value_bis = comboValuesArrayMan[decoded_name];
                                if (value != undefined && value != null && value != "" && value != 0) {
                                    wManImport.gridMapping.cells(row_id, idxDBField).setValue(value);
                                    check = true;
                                } else if (value_bis != undefined && value_bis != null && value_bis != "" && value_bis != 0) {
                                    wManImport.gridMapping.cells(row_id, idxDBField).setValue(value_bis);
                                    value = value_bis;
                                    check = true;
                                }
                            }
                        }

                        if (in_array(value, arrayFieldLangMan)) {
                            wManImport.gridMapping.cells(row_id, idxOptions).setValue(lang);
                            onEditCellmappingMan(2, row_id, idxOptions, lang);
                        }

                        if (!check) {
                            $.each(optionLabelArrayMan, function (id, label) {
                                var finded = false;
                                var option = "";
                                if (name.search(label) >= 0) {
                                    finded = true;
                                    option = $.trim(name.replace(label + " ", ""));
                                } else {
                                    var encoded_name = unescape(encodeURIComponent(name));
                                    if (encoded_name.search(label) >= 0) {
                                        finded = true;
                                        option = $.trim(encoded_name.replace(label + " ", ""));
                                    } else {
                                        var decoded_name = decodeURIComponent(unescape(name));
                                        if (encoded_name.search(label) >= 0) {
                                            finded = true;
                                            option = $.trim(decoded_name.replace(label + " ", ""));
                                        }
                                    }
                                }

                                if (finded) {
                                    wManImport.gridMapping.cells(row_id, idxDBField).setValue(id);
                                    value = id;
                                    check = true;
                                    if (option != undefined && option != null && option != "")
                                        wManImport.gridMapping.cells(row_id, idxOptions).setValue(option);
                                }
                            });
                        }
                    }

                    if (check)
                        onEditCellmappingMan(2, row_id, idxDBField, value);
                }
            });
        }
    }

    function filterScriptName() {
        let inputFilterName = wManImport.tbOptions.getInput('filter_name');
        inputFilterName.onkeyup = function () {
            let search = $(this).val();
            wManImport.gridFiles.filterBy(1, search);
        };
    }

    function sort_dateFR(a, b, order) {
        var a_array = a.split('/');
        var b_array = b.split('/');
        var new_a = a_array[2] * 10000 + a_array[1] * 100 + a_array[0];
        var new_b = b_array[2] * 10000 + b_array[1] * 100 + b_array[0];
        if (order == "asc")
            return new_a > new_b ? 1 : -1;
        else
            return new_a < new_b ? 1 : -1;
    }

    function setOptionsBGColorMan() {
        let idxDBField = wManImport.gridMapping.getColIndexById('db_field');
        let idxOptions = wManImport.gridMapping.getColIndexById('options');
        wManImport.gridMapping.forEachRow(function (rId) {
            wManImport.gridMapping.cells(rId, idxOptions).setBgColor(wManImport.gridMapping.cells(rId, idxDBField).getBgColor());
            var flag = false;
            if (in_array(wManImport.gridMapping.cells(rId, idxDBField).getValue(), arrayFieldLang)) {
                wManImport.gridMapping.cells(rId, idxOptions).setBgColor('#CCCCEE');
                flag = true;
            }
            <?php
            ## Todo pas pour le moment. utile?
            ## sc_ext::readImportCSVConfigXML('importMappingPrepareGrid');
            ?>
            if (!flag) wManImport.gridMapping.cells(rId, idxOptions).setValue('');
        });
    }

    function checkOptions() {
        var flag = true;
        idxDBField = wManImport.gridMapping.getColIndexById('db_field');
        idxOptions = wManImport.gridMapping.getColIndexById('options');
        wManImport.gridMapping.forEachRow(function (rId) {
            if (wManImport.gridMapping.cells(rId, 0).getValue() == "1") {
                if (in_array(wManImport.gridMapping.cells(rId, idxDBField).getValue(), arrayFieldLang)
                    && wManImport.gridMapping.cells(rId, idxOptions).getValue() === '')
                    flag = false;
                <?php
                ## Todo pas pour le moment. utile?
                ## sc_ext::readImportCSVConfigXML('importMappingCheckGrid');
                ?>
            }
        });
        return flag;
    }

    function prepareNextStepMan(TODOFileSize) {
        if (TODOFileSize == 0) {
            stopAutoImportMan(true);
            return 0;
        }
        autoImportTODOSize2 = autoImportTODOSize1;
        autoImportTODOSize1 = TODOFileSize;
        autoImportLastState = 0;
    }

    function stopAlert() {
        $('#alertbox').css('background-color', '#FFFFFF');
        wManAutoImport.setDimension(350, 68);
    }

    function getTODOName(str) {
        if (str.substr(0, str.length - 9) == '.TODO.csv') {
            return str;
        } else {
            return str.substr(0, str.length - 4) + '.TODO.csv';
        }
    }

    function getCheck() {
        var selectedRow = wManImport.gridFiles.getSelectedRowId();
        if (selectedRow !== null && selectedRow.search(",") <= 0) {
            dhxlImport.cells('c').attachHTMLString('<br/><br/><center>' + loader_gif + '</center>');
            $.post('index.php?ajax=1&act=man_win-import_check&id_lang=' + SC_ID_LANG, {
                'mapping': mapping,
                'mappingname': wManImport.tbMapping.getValue('saveas'),
                'mapppinggridlength': wManImport.gridMapping.getRowsNum(),
                'filename': selectedRow,
                'importlimit': wManImport.tbProcess.getValue('importlimit'),
            }, function (data) {
                dhxlImport.cells('c').attachHTMLString(data);
            });
        }
    }
<?php echo '</script>'; ?>
<div id="alertbox" style="width:400px;height:200px;color:#FFFFFF" onclick="stopAlert();">Click here to close alert.
</div>