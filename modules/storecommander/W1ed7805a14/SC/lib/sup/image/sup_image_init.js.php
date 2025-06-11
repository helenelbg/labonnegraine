<?php if (_r('GRI_SUP_PROPERTIES_GRID_IMG')) { ?>
prop_tb.addListOption('panel', 'images', 3, "button", '<?php echo _l('Images', 1); ?>', "fad fa-image");
allowed_properties_panel[allowed_properties_panel.length] = "images";
prop_tb.addButton("image_refresh",1000, "", "fa fa-sync green", "fa fa-sync green");
prop_tb.setItemToolTip('image_refresh', '<?php echo _l('Refresh grid', 1); ?>');
prop_tb.addButton("image_add",1000, "", "fa fa-plus-circle green", "fa fa-plus-circle green");
prop_tb.setItemToolTip('image_add', '<?php echo _l('Upload new images', 1); ?>');
prop_tb.addButton('image_del',1000, '', 'fa fa-minus-circle red', 'fa fa-minus-circle red');
prop_tb.setItemToolTip('image_del', '<?php echo _l('Delete selected images', 1); ?>');
prop_tb.addButton("image_selectall",1000, "", "fa fa-bolt yellow", "fad fa-bolt grey");
prop_tb.setItemToolTip('image_selectall', '<?php echo _l('Select all images', 1); ?>');

prop_tb._imagesUploadWindow = [];


var supplier_shop_default = 0;

clipboardType_Images = null;
needInitImages = 1;
function initImages() {
    if (needInitImages) {
        prop_tb._imagesLayout = dhxLayout.cells('b').attachLayout('1C');
        prop_tb._imagesLayout.cells('a').hideHeader();
        dhxLayout.cells('b').showHeader();
        prop_tb._imagesGrid = prop_tb._imagesLayout.cells('a').attachGrid();
        prop_tb._imagesGrid.setImagePath("lib/js/imgs/");
        prop_tb._imagesGrid.enableDragAndDrop(true);
        prop_tb._imagesGrid.setDragBehavior('child');
        prop_tb._imagesGrid.enableMultiselect(true);

        // UISettings
        prop_tb._imagesGrid._uisettings_prefix = 'sup_image';
        prop_tb._imagesGrid._uisettings_name = prop_tb._imagesGrid._uisettings_prefix;
        prop_tb._imagesGrid._first_loading = 1;

        // UISettings
        initGridUISettings(prop_tb._imagesGrid);

        needInitImages = 0;
    }
}

function setPropertiesPanel_images(id) {
    switch (id) {
        case 'images':
            if (last_supplierID !== undefined && last_supplierID > 0) {
                idxSupplierName = sup_grid.getColIndexById('name');
                dhxLayout.cells('b').setText('<?php echo _l('Properties', 1).' '._l('of', 1); ?> ' + sup_grid.cells(last_supplierID, idxSupplierName).getValue());
            }
            hidePropTBButtons();
            prop_tb.showItem('image_refresh');
            prop_tb.showItem('image_add');
            prop_tb.showItem('image_del');
            prop_tb.showItem('image_selectall');
            prop_tb.setItemText('panel', '<?php echo _l('Images', 1); ?>');
            prop_tb.setItemImage('panel', 'fad fa-image');
            needInitImages = 1;
            initImages();
            propertiesPanel = 'images';
            if (last_supplierID > 0) {
                displayImages();
            }
            break;
        case 'image_refresh':
            displayImages();
            break;
        case 'image_add':
            let suppliers_ids = sup_grid.getSelectedRowId();
            if (suppliers_ids) {
                if (!dhxWins.isWindow("wProductImages" + suppliers_ids)) {
                    prop_tb._imagesUploadWindow[suppliers_ids] = dhxWins.createWindow("prop_tb._imagesUploadWindow[suppliers_ids]", 50, 50, 600, 450);

                    if (suppliers_ids.search(",") < 0) {
                        prop_tb._imagesUploadWindow[suppliers_ids].setText('<?php echo _l('Upload images', 1); ?>: ' + sup_grid.cells(suppliers_ids, idxSupplierName).getValue());
                    } else {
                        prop_tb._imagesUploadWindow[suppliers_ids].setText('<?php echo _l('Upload images', 1); ?>');
                    }
                    ll = new dhtmlXLayoutObject(prop_tb._imagesUploadWindow[suppliers_ids], "1C");
                    ll.cells('a').hideHeader();

                    ll_toolbar = ll.cells('a').attachToolbar();
                    ll_toolbar.setIconset('awesome');
                    ll_toolbar.addButtonTwoState("auto_upload", 0, "", "fad fa-external-link green", "fad fa-external-link green");
                    ll_toolbar.setItemToolTip('auto_upload', '<?php echo _l('If enabled: Images will be automatically uploaded once selected', 1); ?>');
                    ll_toolbar.setItemState('auto_upload', ($.cookie('sc_sup_img_auto_upload') == 1 ? 1 : 0));

                    ll_toolbar.attachEvent("onStateChange", function (id, state) {
                        if (id == 'auto_upload') {
                            let auto_upload = 0;
                            if (state) {
                                auto_upload = 1;
                            }
                            $.cookie('sc_sup_img_auto_upload', auto_upload, {expires: 60,path: cookiePath});
                        }
                    });

                    ll.cells('a').attachURL("index.php?ajax=1&act=sup_image_upload&supplier_list=" + suppliers_ids + "&id_lang=" + SC_ID_LANG);
                    prop_tb._imagesUploadWindow[suppliers_ids].attachEvent("onClose", function (win) {
                        win.hide();
                        displayImages();
                        return false;
                    });
                } else {
                    prop_tb._imagesUploadWindow[suppliers_ids].show();
                }
            } else {
                alert('<?php echo _l('Please select a supplier', 1); ?>');
            }
            break;
        case 'image_del':
            if (prop_tb._imagesGrid.getSelectedRowId() == null) {
                alert('<?php echo _l('Please select an image', 1); ?>');
            } else {
                if (last_supplierID != 0) {
                    if (confirm('<?php echo _l('Are you sure you want to delete the selected items?', 1); ?>')) {
                        $.post("index.php?ajax=1&act=sup_image_update&action=delete&id_lang=" + SC_ID_LANG, {
                            id_supplier: last_supplierID,
                            list_id_image: prop_tb._imagesGrid.getSelectedRowId()
                        }, function () {
                            displayImages();
                            displaySuppliers();
                        });
                    }
                } else {
                    alert('<?php echo _l('Please select a supplier', 1); ?>');
                }
            }
            break;
        case 'image_selectall':
            prop_tb._imagesGrid.selectAll();
            break;
    }
}
prop_tb.attachEvent("onClick", setPropertiesPanel_images);

function displayImages(callback) {
    prop_tb._imagesGrid.clearAll(true);
    let loadUrl = 'index.php?ajax=1&act=sup_image_get';
    ajaxPostCalling(dhxLayout.cells('b'), prop_tb._imagesGrid, loadUrl, {
        id_supplier: sup_grid.getSelectedRowId(),
        id_shop: shopselection,
        id_lang: SC_ID_LANG
    }, function (data) {
        prop_tb._imagesGrid.parse(data);
        let nb = prop_tb._imagesGrid.getRowsNum();
        prop_tb._sb.setText(nb + (nb > 1 ? " <?php echo _l('images'); ?>" : " <?php echo _l('image'); ?>"));

        // UISettings
        loadGridUISettings(prop_tb._imagesGrid);
        prop_tb._imagesGrid._first_loading = 0;

        <?php sc_ext::readCustomImageGridConfigXML('afterGetRows'); ?>

        if (callback !== '') {
            eval(callback);
        }
    });
}

let sup_images_current_id = 0;
sup_grid.attachEvent("onRowSelect", function (idsupplier) {
    if (propertiesPanel == 'images' && (sup_grid.getSelectedRowId() !== null && sup_images_current_id != idsupplier)) {
        initImages();
        displayImages();
        sup_images_current_id = idsupplier;
    }
});
<?php } ?>