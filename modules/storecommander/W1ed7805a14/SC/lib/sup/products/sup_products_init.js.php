<?php if (_r('GRI_MAN_PROPERTIES_PRODUCTS')) { ?>
prop_tb.addListOption('panel', 'products', 3, "button", '<?php echo _l('Products', 1); ?>', "fa fa-cubes");
allowed_properties_panel[allowed_properties_panel.length] = "products";

prop_tb.addButtonTwoState("products_without_sup", 1000, "", "fad fa-eye green", "fad fa-eye green");
prop_tb.setItemToolTip('products_without_sup', '<?php echo _l('If enabled: show products without supplier', 1); ?>');
prop_tb.addButton("products_refresh",1000, "", "fa fa-sync green", "fa fa-sync green");
prop_tb.setItemToolTip('products_refresh', '<?php echo _l('Refresh grid', 1); ?>');
prop_tb.addButton("products_add",1000, "", "fad fa-link yellow", "fad fa-link yellow");
prop_tb.setItemToolTip('products_add', '<?php echo _l('Add selected products to a supplier', 1); ?>');
prop_tb.addButton('products_del',1000, '', 'fad fa-unlink red', 'fad fa-unlink red');
prop_tb.setItemToolTip('products_del', '<?php echo _l('Remove suppliers from selected products', 1); ?>');
prop_tb.addButton("products_selectall",1000, "", "fa fa-bolt yellow", "fad fa-unlink red");
prop_tb.setItemToolTip('products_selectall', '<?php echo _l('Select all products', 1); ?>');
prop_tb.addButton("gotocatalog", 1000, "", "fad fa-external-link green", "fad fa-external-link green");
prop_tb.setItemToolTip('gotocatalog', '<?php echo _l('Go to the product in catalog.'); ?>');
prop_tb.addButton("combi_exportcsv",1000, "", "fad fa-file-csv green", "fad fa-file-csv green");
prop_tb.setItemToolTip('combi_exportcsv', '<?php echo _l('Export grid to clipboard in CSV format for MSExcel with tab delimiter.', 1); ?>');

prop_tb._productsUploadWindow = [];


var products_without_sup = 0;

clipboardType_Products = null;
needInitProducts = 1;
function initProducts() {
    if (needInitProducts) {
        prop_tb._productsLayout = dhxLayout.cells('b').attachLayout('1C');
        prop_tb._productsLayout.cells('a').hideHeader();
        dhxLayout.cells('b').showHeader();
        prop_tb._productsGrid = prop_tb._productsLayout.cells('a').attachGrid();
        prop_tb._productsGrid.setImagePath("lib/js/imgs/");
        prop_tb._productsGrid.enableDragAndDrop(true);
        prop_tb._productsGrid.setDragBehavior('child');
        prop_tb._productsGrid.enableMultiselect(true);

        // UISettings
        prop_tb._productsGrid._uisettings_prefix = 'sup_product';
        prop_tb._productsGrid._uisettings_name = prop_tb._productsGrid._uisettings_prefix;
        prop_tb._productsGrid._first_loading = 1;

        // UISettings
        initGridUISettings(prop_tb._productsGrid);

        prop_tb._productsGrid.attachEvent("onEditCell", onEditCellProduct);
        needInitProducts = 0;
    }
}

function addSupplierProductsInQueue(rId, action, cIn, vars) {
    var params = {
        name: "sup_products_update_queue",
        row: rId,
        action: action,
        params: {},
        callback: "callbackSupplierProductsUpdate('" + rId + "','udpate','" + rId + "',{data});"
    };

    // COLUMN VALUES
    if (cIn != undefined && cIn != "" && cIn != null && cIn != 0) {
        params.params[prop_tb._productsGrid.getColumnId(cIn)] = prop_tb._productsGrid.cells(rId, cIn).getValue();
    }
    if (vars != undefined && vars != null && vars != "" && vars != 0) {
        $.each(vars, function (key, value) {
            params.params[key] = value;
        });
    }

    params.params = JSON.stringify(params.params);
    addInUpdateQueue(params, prop_tb._productsGrid);
}

function onEditCellProduct(stage, rId, cInd, nValue, oValue) {
    let idxSupplier = prop_tb._productsGrid.getColIndexById('id_supplier');
    let idxReference = prop_tb._productsGrid.getColIndexById('reference');
    let idxSupplierReference = prop_tb._productsGrid.getColIndexById('supplier_reference');
    let idxActive = prop_tb._productsGrid.getColIndexById('active');
    if (stage == 2 && nValue != oValue) {
        if (cInd == idxSupplier) {
            let vars = {};
            let local_action = 'update';
            if (Number(nValue) == 0) {
                local_action = 'dissociate';
                vars['selected_suppliers'] = sup_grid.getSelectedRowId();
            }
            addSupplierProductsInQueue(rId, local_action, cInd, vars);
            return true;
        }
        if (cInd == idxReference) {
            let vars = {};
            vars['reference'] = nValue;
            addSupplierProductsInQueue(rId, 'update', cInd, vars);
            return true;
        }
        if (cInd == idxSupplierReference) {
            let vars = {};
            vars['id_supplier'] = prop_tb._productsGrid.getUserData(rId, "id_supplier");
            vars['supplier_reference'] = nValue;
            addSupplierProductsInQueue(rId, 'update', cInd, vars);
            return true;
        }
        if (cInd == idxActive) {
            let vars = {};
            vars['active'] = nValue;
            addSupplierProductsInQueue(rId, 'update', cInd, vars);
            return true;
        }
    }
}

function setPropertiesPanel_products(id) {
    switch(id) {
        case 'products':
            if (last_supplierID != undefined && last_supplierID != "") {
                idxProductName = sup_grid.getColIndexById('name');
                dhxLayout.cells('b').setText('<?php echo _l('Properties', 1).' '._l('of', 1); ?> ' + sup_grid.cells(last_supplierID, idxProductName).getValue());
            }
            hidePropTBButtons();
            prop_tb.showItem('products_selectall');
            prop_tb.showItem('products_add');
            prop_tb.showItem('products_del');
            prop_tb.showItem('products_refresh');
            prop_tb.showItem('products_without_sup');
            prop_tb.showItem('gotocatalog');
            prop_tb.showItem('combi_exportcsv');
            prop_tb.setItemText('panel', '<?php echo _l('Products', 1); ?>');
            prop_tb.setItemImage('panel', 'fa fa-cubes');
            needInitProducts = 1;
            initProducts();
            propertiesPanel = 'products';
            if (last_supplierID != 0) {
                displayProducts();
            }
            break;
        case 'products_refresh':
            displayProducts();
            break;
        case 'products_selectall':
            prop_tb._productsGrid.selectAll();
            break;
        case 'products_add':
            idxSupplier = prop_tb._productsGrid.getColIndexById('id_supplier');
            let nValue = sup_grid.getSelectedRowId();
            if (nValue.indexOf(",") !== -1) {
                alert('<?php echo _l('Please select only one supplier', 1); ?>')
            } else {
                selection = prop_tb._productsGrid.getSelectedRowId();
                selArray = selection.split(',');
                for (i = 0; i < selArray.length; i++) {
                    prop_tb._productsGrid.cells(selArray[i], idxSupplier).setValue(nValue);
                    onEditCellProduct(2, selArray[i], idxSupplier, nValue, null);
                }
            }
            break;
        case 'products_del':
            idxSupplier = prop_tb._productsGrid.getColIndexById('id_supplier');
            selection = prop_tb._productsGrid.getSelectedRowId();
            selArray = selection.split(',');
            for (i = 0; i < selArray.length; i++) {
                prop_tb._productsGrid.cells(selArray[i], idxSupplier).setValue(0);
                onEditCellProduct(2, selArray[i], idxSupplier, 0, null);
            }
            break;
        case 'gotocatalog':
            selection = prop_tb._productsGrid.getSelectedRowId();
            if (selection != '' && selection != null) {
                var rowIds = selection.split(",");
                var rowId = rowIds[0];
    
                var open_cat_grid_ids = prop_tb._productsGrid.getUserData(rowId, "open_cat_grid");
                if (open_cat_grid_ids != '' && open_cat_grid_ids != null) {
                    window.open("?page=cat_tree&open_cat_grid=" + open_cat_grid_ids, '_blank');
                }
            }
            break;
        case 'combi_exportcsv':
            displayQuickExportWindow(prop_tb._productsGrid, 1);
            break;
    }
}
prop_tb.attachEvent("onClick", setPropertiesPanel_products);

prop_tb.attachEvent("onStateChange", function (id, state) {
    if (id == "products_without_sup") {
        if (state) {
            products_without_sup = 1;
        } else {
            products_without_sup = 0;
        }
        displayProducts();
    }
});



function displayProducts(callback) {
    prop_tb._productsGrid.clearAll(true);
    let loadUrl = 'index.php?ajax=1&act=sup_products_get';
    ajaxPostCalling(dhxLayout.cells('b'), prop_tb._productsGrid, loadUrl, {
        id_supplier: sup_grid.getSelectedRowId(),
        id_shop: shopselection,
        products_without_sup:products_without_sup,
        id_lang:SC_ID_LANG
    }, function (data) {
        prop_tb._productsGrid.parse(data);
        let nb = prop_tb._productsGrid.getRowsNum();
        prop_tb._sb.setText(nb + (nb > 1 ? " <?php echo _l('products'); ?>" : " <?php echo _l('product'); ?>"));

        // UISettings
        loadGridUISettings(prop_tb._productsGrid);
        prop_tb._productsGrid._first_loading = 0;

        if (callback != ''){
            eval(callback);
        }
    });
}

// CALLBACK FUNCTION
function callbackSupplierProductsUpdate(sid, action, tid) {
    if (action == 'update') {
        prop_tb._productsGrid.setRowTextNormal(sid);
    }
    displayProducts();
}

var sup_products_current_id = 0;
sup_grid.attachEvent("onRowSelect", function (id_supplier) {
    if (propertiesPanel == 'products' && (sup_grid.getSelectedRowId() !== null && sup_products_current_id != id_supplier)) {
        initProducts();
        displayProducts();
        sup_products_current_id = id_supplier;
    }
});
<?php } ?>
