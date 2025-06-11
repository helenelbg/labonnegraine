<?php echo '<script type="text/javascript">'; ?>

    dhxlNotePad_layout = wNotePad.attachLayout("1C");
    dhxlNotePad_layout_a = dhxlNotePad_layout.cells('a');
    dhxlNotePad_layout_a.hideHeader();

    // Toolbar
    dhxlNotePad_tb = wNotePad.attachToolbar();
    dhxlNotePad_tb.setIconset('awesome');
    dhxlNotePad_tb.addButton("refresh", 0, "", "fa fa-sync green", "fa fa-sync green");
    dhxlNotePad_tb.setItemToolTip('refresh', '<?php echo _l('Refresh'); ?>');
    dhxlNotePad_tb.addButton("save", 1, "", "fa fa-save blue", "fa fa-save blue");
    dhxlNotePad_tb.setItemToolTip('save', '<?php echo _l('Save notes'); ?>');
    dhxlNotePad_tb.addButton("delete", 2, "", "fa fa-minus-circle red", "fa fa-minus-circle red");
    dhxlNotePad_tb.setItemToolTip('delete', '<?php echo _l('Erase notes'); ?>');
    dhxlNotePad_tb.attachEvent("onClick", function (item) {
        switch (item) {
            case 'refresh':
                displayContentEditor();
                break;
            case 'save':
                $.post("index.php?ajax=1&act=all_win-employeenotepad_update",
                    {
                        'action': 'save',
                        'note': dhxlNotePad_editor.getContent()
                    }, function (data) {
                        let res = JSON.parse(data);
                        if (res.error == 1) {
                            dhtmlx.message({text: res.detail, type: 'error'});
                        } else {
                            dhtmlx.message({text: '<?php echo _l('Notes saved'); ?>', type: 'success'});
                        }
                    });
                break;
            case 'delete':
                $.post("index.php?ajax=1&act=all_win-employeenotepad_update",
                    {
                        'action': 'delete',
                    }, function (data) {
                        let res = JSON.parse(data);
                        if (res.error == 1) {
                            dhtmlx.message({text: res.detail, type: 'error'});
                        } else {
                            dhtmlx.message({text: '<?php echo _l('Notes erased'); ?>', type: 'success'});
                            displayContentEditor();
                        }
                    });
                break;
        }
    });

    //Editor
    dhxlNotePad_editor = wNotePad.attachEditor();
    displayContentEditor();

    function displayContentEditor() {
        $.post("index.php?ajax=1&act=all_win-employeenotepad_get", function (data) {
            dhxlNotePad_editor.setContent(data);
        });
    }
<?php echo '</script>'; ?>