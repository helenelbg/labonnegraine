<?php if (_r('GRI_SUP_PROPERTIES_GRID_DESC')) { ?>
window.description_conf = {
    id: 'description',
    name: '<?php echo _l('Description'); ?>',
    icon: 'fad fa-align-left',
    editor: '<?php echo _s('APP_RICH_EDITOR') == 1 ? 'tinymce' : 'ckeditor'; ?>',
    interface: 'sup',
    url_options: '',
    note_save: 0,
    need_init: 1,
    current_id: 0
};
description_conf.urlProp = [description_conf.interface,description_conf.id].join('_');
description_conf.urlEditor = [description_conf.urlProp,description_conf.editor].join('_');
prop_tb.addListOption('panel', description_conf.id, 2, 'button', description_conf.name, "fad fa-align-left");
allowed_properties_panel[allowed_properties_panel.length] = description_conf.id;
prop_tb.addButton(description_conf.id + '_refresh',1000, '', 'fa fa-sync green', 'fa fa-sync green');
prop_tb.setItemToolTip(description_conf.id + '_refresh', '<?php echo _l('Refresh', 1); ?>');
prop_tb.addButton(description_conf.id + '_save',1000, '', 'fa fa-save blue', 'fa fa-save blue');
prop_tb.setItemToolTip(description_conf.id + '_save', '<?php echo _l('Save description', 1); ?>');

function init_description() {
    if (description_conf.need_init) {
        prop_tb._Layout_description = dhxLayout.cells('b').attachLayout('1C');
        prop_tb._Layout_description.cells('a').hideHeader();
        prop_tb._Layout_description.cells('a').attachURL('index.php?ajax=1&act=' + description_conf.urlEditor + description_conf.url_options);
        dhxLayout.cells('b').showHeader();

        prop_tb._Layout_description.attachEvent('onCollapse', function () {
            saveParamUISettings('start_'+description_conf.interface+'_' + description_conf.id, 1);
        });
        prop_tb._Layout_description.attachEvent('onExpand', function () {
            saveParamUISettings('start_'+description_conf.interface+'_' + description_conf.id, 0);
        });

        description_conf.need_init = 0;
    }
}

function setPropertiesPanel_description(id) {
    // ask to save description if modified
    if (propertiesPanel === description_conf.id
        && id !== description_conf.id + '_save'
        && typeof prop_tb._Layout_description != 'undefined') {
        checkBeforeChangeRow_description();
    }

    switch(id) {
        case description_conf.id:
            hidePropTBButtons();
            prop_tb.showItem(description_conf.id + '_refresh');
            prop_tb.showItem(description_conf.id + '_save');
            prop_tb.setItemText('panel', description_conf.name);
            prop_tb.setItemImage('panel', description_conf.icon);
            description_conf.url_options = '';
            if (last_supplierID !== 0) {
                description_conf.url_options = '&id_supplier=' + last_supplierID + '&id_lang=' + SC_ID_LANG;
            }
            description_conf.need_init = 1;
            init_description();
            propertiesPanel = description_conf.id;
            break;
        case description_conf.id + '_save':
            description_conf.not_save = 0;
            <?php if (_s('APP_RICH_EDITOR') != 1) { ?>
            prop_tb._Layout_description.cells('a').progressOn();
            <?php } ?>
            prop_tb._Layout_description.cells('a').getFrame().contentWindow.ajaxSave();
            break;
        case description_conf.id + '_refresh':
            if (last_supplierID !== 0) {
                description_conf.url_options = '&id_supplier=' + last_supplierID + '&id_lang=' + SC_ID_LANG;
            }
            prop_tb._Layout_description.cells('a').attachURL('index.php?ajax=1&act=' + description_conf.urlEditor + description_conf.url_options);
            break;
    }
}
prop_tb.attachEvent('onClick', setPropertiesPanel_description);

sup_grid.attachEvent('onBeforeSelect', function () {
    checkBeforeChangeRow_description();
    return true;
});

sup_grid.attachEvent('onRowSelect', function (id_supplier) {
    last_supplierID = id_supplier;
    idxSupplierName = sup_grid.getColIndexById('name');
    if (propertiesPanel === description_conf.id
        && (sup_grid.getSelectedRowId() !== null && description_conf.current_id !== id_supplier)) {
        dhxLayout.cells('b').setText('<?php echo _l('Properties', 1).' '._l('of', 1); ?> ' + sup_grid.cells(last_supplierID, idxSupplierName).getValue());
        <?php if (_s('APP_RICH_EDITOR') != 1) { ?>
        prop_tb._Layout_description.cells('a').progressOn();
        <?php } ?>
        prop_tb._Layout_description.cells('a').getFrame().contentWindow.ajaxLoad('&id_supplier=' + last_supplierID + '&id_lang=' + SC_ID_LANG, last_supplierID, SC_ID_LANG);
        description_conf.current_id = id_supplier;
    }
});

function checkBeforeChangeRow_description() {
    if (propertiesPanel === description_conf.id) {
        if (description_conf.not_save !== 0) {
            prop_tb._Layout_description.cells('a').getFrame().contentWindow.checkChange();
        }
    }
}
<?php } ?>