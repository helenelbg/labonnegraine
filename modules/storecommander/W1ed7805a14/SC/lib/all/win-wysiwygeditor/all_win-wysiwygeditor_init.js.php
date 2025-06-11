<?php echo '<script type="text/javascript">'; ?>
let wysiwyg_rId = "<?php echo Tools::getValue('rId', null); ?>";
let wysiwyg_cInd = "<?php echo Tools::getValue('cInd', null); ?>";

let current_wysiwyg_cell = last_selected_grid.cells(wysiwyg_rId,wysiwyg_cInd);

if (!dhxLayout.dhxWins.isWindow("wWysiwyg")) {
    var wWysiwyg = dhxLayout.dhxWins.createWindow("wWysiwyg", 0, 0, 600, 400);
    wWysiwyg.center();
    let text_window = [];
    let cId_name = last_selected_grid.getColIndexById('name');
    let cId_id = last_selected_grid.getColIndexById('id');
    if(cId_id !== undefined) {
        let id = last_selected_grid.cells(wysiwyg_rId,cId_id).getValue();
        text_window.push(' - ('+id+')');
    }
    if(cId_name !== undefined) {
        let name = last_selected_grid.cells(wysiwyg_rId,cId_name).getValue();
        text_window.push(' <?php echo _l(':', 1); ?> '+name);
    }
    wWysiwyg.setText("<?php echo _l('Edit the field', 1); ?> " + last_selected_grid.getColLabel(wysiwyg_cInd) + text_window.join(''));
}

var wWysiwyg_layout = new dhtmlXLayoutObject(wWysiwyg, "1C");
var wWysiwyg_layout_editor = wWysiwyg_layout.cells("a");
wWysiwyg_layout_editor.hideHeader();
displayEditor();


wWysiwyg_layout_tb = wWysiwyg_layout_editor.attachToolbar();
wWysiwyg_layout_tb.setIconset('awesome');
wWysiwyg_layout_tb.addButton("refresh", 1, "", "fa fa-sync", "fa fa-sync");
wWysiwyg_layout_tb.setItemToolTip("refresh", "<?php echo _l('Refresh', 1); ?>");
wWysiwyg_layout_tb.addButton("save", 1, "", "fa fa-save", "fa fa-save");
wWysiwyg_layout_tb.setItemToolTip("save", "<?php echo _l('Save', 1); ?>");

wWysiwyg_layout_tb.attachEvent("onClick", function (id) {
    switch(id) {
        case 'save':
            let html_content = wWysiwyg_layout_editor.getFrame().contentWindow.getContentSourceCode();
            let old_val = last_selected_grid.cells(wysiwyg_rId, wysiwyg_cInd).getValue();
            last_selected_grid.cells(wysiwyg_rId, wysiwyg_cInd).setValue(html_content);
            last_selected_grid.cells(wysiwyg_rId, wysiwyg_cInd).cell.wasChanged = true;
            let isCellEdited = last_selected_grid.callEvent('onEditCell',[2, wysiwyg_rId, wysiwyg_cInd, html_content, old_val]);
            if(Boolean(isCellEdited) === true) {
                dhtmlx.message({text:"<?php echo _l('Data saved!'); ?>",type:'success'});
            }
            break;
        case 'refresh':
            displayEditor();
            break;
    }
});

function displayEditor() {
    wWysiwyg_layout_editor.progressOn();
    let type_wysiwyg = '<?php echo _s('APP_RICH_EDITOR') == 1 ? 'tinymce' : 'ckeditor'; ?>';
    wWysiwyg_layout_editor.attachURL('index.php?ajax=1&act=all_win-wysiwygeditor_'+type_wysiwyg, null, {
        rId: wysiwyg_rId,
        cInd: wysiwyg_cInd
    });
}
<?php echo '</script>'; ?>