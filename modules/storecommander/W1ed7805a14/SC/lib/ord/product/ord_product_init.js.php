<?php if (_r('GRI_ORD_PROPERTIES_GRID_PRODUCT')) { ?>
    prop_tb.addListOption('panel', 'orderproduct', 1, "button", '<?php echo _l('Products', 1); ?>', "fa fa-cubes");
    allowed_properties_panel[allowed_properties_panel.length] = "orderproduct";

    prop_tb.addButton("orderproduct_refresh", 1000, "", "fa fa-sync green", "fa fa-sync green");
    prop_tb.setItemToolTip('orderproduct_refresh','<?php echo _l('Refresh grid', 1); ?>');

    prop_tb.addButton("exportcsv", 1000, "", "fad fa-file-csv green", "fad fa-file-csv green");
    prop_tb.setItemToolTip('exportcsv','<?php echo _l('Export grid to clipboard in CSV format for MSExcel with tab delimiter.'); ?>');

    prop_tb.addButton("gotocatalog", 1000, "", "fad fa-external-link green", "fad fa-external-link green");
    prop_tb.setItemToolTip('gotocatalog','<?php echo _l('Go to the product in catalog.'); ?>');
    getTbSettingsButton(prop_tb, {'grideditor':1,'settings':0}, 'prop_product_', 1000);

    needinitOrderProduct = 1;
    function initOrderProduct(){
        if (needinitOrderProduct)
        {
            prop_tb._orderProductLayout = dhxLayout.cells('b').attachLayout('1C');
            prop_tb._orderProductLayout.cells('a').hideHeader();
            dhxLayout.cells('b').showHeader();
            prop_tb._orderProductGrid = prop_tb._orderProductLayout.cells('a').attachGrid();
            prop_tb._orderProductGrid.setImagePath("lib/js/imgs/");
            
            // UISettings
            prop_tb._orderProductGrid._uisettings_prefix='ord_product';
            prop_tb._orderProductGrid._uisettings_name=prop_tb._orderProductGrid._uisettings_prefix;
               prop_tb._orderProductGrid._first_loading=1;
               
            // UISettings
            initGridUISettings(prop_tb._orderProductGrid);
            
<?php
if (version_compare(_PS_VERSION_, '1.5.0.0', '<'))
{
    ?>
            prop_tb._orderProductGrid_sb=prop_tb._orderProductLayout.cells('a').attachStatusBar();
            prop_tb._orderProductGrid_sb.setText('<span style="color:#CC0000"><?php echo _l('Warning: Store Commander doesn\'t recalculate order\'s totals.', 1); ?></span></>');
<?php
}
?>

            function onEditCellOrderProductGrid(stage, rId, cIn,nValue,oValue)
            {
                if (stage==1 && this.editor && this.editor.obj) this.editor.obj.select();

                <?php sc_ext::readCustomPropSpePriceGridConfigXML('onEditCell'); ?>
                if (nValue!=oValue)
                {
                    if(stage==2)
                    {
                        <?php sc_ext::readCustomPropSpePriceGridConfigXML('onBeforeUpdate'); ?>
                        var params = {
                            name: "ord_product_update",
                            row: rId,
                            action: "update",
                            params: {},
                            callback: "callbackOrderProductGrid('"+rId+"','update','"+rId+"');"
                        };

                        // COLUMN VALUES
                        params.params[prop_tb._orderProductGrid.getColumnId(cIn)] = prop_tb._orderProductGrid.cells(rId,cIn).getValue();
                        // col id_order
                        var idxIDOrder = prop_tb._orderProductGrid.getColIndexById("id_order");
                        params.params["id_order"] = prop_tb._orderProductGrid.cells(rId,idxIDOrder).getValue();

                        params.params = JSON.stringify(params.params);
                        addInUpdateQueue(params,prop_tb._orderProductGrid);
                    }
                }

                return true;
            }
            prop_tb._orderProductGrid.attachEvent("onEditCell", onEditCellOrderProductGrid);
        }
    }

    function setPropertiesPanel_orderproduct(id){
        if (id=='orderproduct')
        {
            hidePropTBButtons();
            prop_tb.showItem('exportcsv');
            prop_tb.showItem('orderproduct_refresh');
            prop_tb.showItem('gotocatalog');
            prop_tb.showItem('prop_product_settings_menu');
            prop_tb.setItemText('panel', '<?php echo _l('Products', 1); ?>');
            prop_tb.setItemImage('panel', 'fa fa-cubes');
            needinitOrderProduct = 1;
            initOrderProduct();
            propertiesPanel='orderproduct';
            if (lastOrderSelID!=0)
            {
                displayOrderProducts();
            }
        }
        if(id=='gotocatalog')
        {
            selection=prop_tb._orderProductGrid.getSelectedRowId();
            if (selection!='' && selection!=null)
            {
                var rowIds = selection.split(",");
                var rowId = rowIds[0];
        
                var open_cat_grid_ids  = prop_tb._orderProductGrid.getUserData(rowId, "open_cat_grid");
                if (open_cat_grid_ids!='' && open_cat_grid_ids!=null)
                {
                    var url = "?page=cat_tree&open_cat_grid="+open_cat_grid_ids;
                    window.open(url,'_blank');
                }
            }
        }
        if (id=='orderproduct_refresh')
        {
            displayOrderProducts();
        }
        if (id=='prop_product_grideditor'){
            openWinGridEditor('type_order_product');
        }
        if (id=='exportcsv'){
            displayQuickExportWindow(prop_tb._orderProductGrid,1);
        }

    }
    prop_tb.attachEvent("onClick", setPropertiesPanel_orderproduct);


    function displayOrderProducts()
    {
        prop_tb._orderProductGrid.clearAll(true);
        let loadUrl = "index.php?ajax=1&act=ord_product_get";
        ajaxPostCalling(dhxLayout.cells('b'), prop_tb._orderProductGrid, loadUrl, {id_order:lastOrderSelIDs}, function(data)
        {
            prop_tb._orderProductGrid.parse(data);
            nb=prop_tb._orderProductGrid.getRowsNum();
            prop_tb._sb.setText('');

            // UISettings
            loadGridUISettings(prop_tb._orderProductGrid);

            // UISettings
            prop_tb._orderProductGrid._first_loading=0;
        });
    }


    let orderproduct_current_id = 0;
    ord_grid.attachEvent("onRowSelect",function (idproduct){
        if (propertiesPanel=='orderproduct' && !dhxLayout.cells('b').isCollapsed() && (ord_grid.getSelectedRowId()!==null && orderproduct_current_id!=idproduct)){
            displayOrderProducts();
            orderproduct_current_id=idproduct;
        }
    });

    // CALLBACK FUNCTION
    function callbackOrderProductGrid(sid,action,tid)
    {
        <?php sc_ext::readCustomPropSpePriceGridConfigXML('onAfterUpdate'); ?>
        if (action=='update') {
            prop_tb._orderProductGrid.setRowTextNormal(sid);
        }
    }

<?php
    } // end permission
?>