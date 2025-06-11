<?php
if (SCAS) { ?>
    
    <?php if (_r('ACT_CAT_ADVANCED_STOCK_MANAGEMENT')) { ?>
        prop_tb.addListOption('panel', 'warehousestock', 15, "button", '<?php echo _l('Advanced stocks', 1); ?>', "fad fa-external-link green");
        allowed_properties_panel[allowed_properties_panel.length] = "warehousestock";
    <?php } ?>
    
    needInitwarehousestock = 1;
    var advancedStockGrid_groupByProduct = 0;
    function initwarehousestock()
    {
        if (needInitwarehousestock)
        {
            prop_tb._warehousestockLayout = dhxLayout.cells('b').attachLayout('2E');
            dhxLayout.cells('b').showHeader();
            
            // ADVANCED
            prop_tb._advancedStock = prop_tb._warehousestockLayout.cells('a');
            prop_tb._advancedStock.setText('<?php echo _l('Advanced stocks', 1); ?>');
            
            prop_tb._advancedStock_tb = prop_tb._advancedStock.attachToolbar();
             prop_tb._advancedStock_tb.setIconset('awesome');
            prop_tb._advancedStock_tb.addButton("warehousestock_refresh", 100, "", "fa fa-sync green", "fa fa-sync green");
            prop_tb._advancedStock_tb.setItemToolTip('warehousestock_refresh','<?php echo _l('Refresh grid', 1); ?>');
            prop_tb._advancedStock_tb.addButton("warehousestock_mvt_add", 100, "", "fa fa-plus-circle green", "fa fa-plus-circle green");
            prop_tb._advancedStock_tb.setItemToolTip('warehousestock_mvt_add','<?php echo _l('Create a new stock movement : Add product to stock', 1); ?>');
            prop_tb._advancedStock_tb.addButton("warehousestock_mvt_delete", 100, "", "fa fa-minus-circle red", "fa fa-minus-circle red");
            prop_tb._advancedStock_tb.setItemToolTip('warehousestock_mvt_delete','<?php echo _l('Create a new stock movement : Remove product from stock', 1); ?>');
            prop_tb._advancedStock_tb.addButton("warehousestock_mvt_transfert", 100, "", "fa fa-exchange-alt green", "fa fa-exchange-alt green");
            prop_tb._advancedStock_tb.setItemToolTip('warehousestock_mvt_transfert','<?php echo _l('Stock Transfert', 1); ?>');
            prop_tb._advancedStock_tb.addButtonTwoState('warehousestock_groupByProduct', 100, "", "fa fa-compress-arrows-alt green", "fa fa-compress-arrows-alt green");
            prop_tb._advancedStock_tb.setItemToolTip('warehousestock_groupByProduct','<?php echo _l('Group rows by product', 1); ?>');
            prop_tb._advancedStock_tb.addButton("exportcsv", 100, "", "fad fa-file-csv green", "fad fa-file-csv green");
            prop_tb._advancedStock_tb.setItemToolTip('exportcsv','<?php echo _l('Export grid to clipboard in CSV format for MSExcel with tab delimiter.'); ?>');
            prop_tb._advancedStock_tb.attachEvent("onClick", function(id){
                if (id=='warehousestock_refresh')
                {
                    displayAdvancedStock();
                }
                else if (id=='warehousestock_mvt_transfert')
                {
                    if(prop_tb._advancedStockGrid.getSelectedRowId())
                    {
                        var temp_ids = prop_tb._advancedStockGrid.getSelectedRowId().split("_");
                        var id_product = temp_ids[0];
                        var id_product_attribute = temp_ids[1];
                        var id_warehouse = temp_ids[2];
                    
                        if (!dhxWins.isWindow("wStockMvt"))
                        {
                            wStockMvt = dhxWins.createWindow("wStockMvt", ($(window).width()/2-215), 50, 430, 670);
                            wStockMvt.setText("<?php echo _l('Stock Transfer'); ?>");
                            $.get("index.php?ajax=1&act=cat_win-stockmvt_transfert_init&id_product="+id_product+"&id_product_attribute="+id_product_attribute+"&id_warehouse="+id_warehouse+"&id_lang="+SC_ID_LANG,function(data){
                                wStockMvt.show();
                                $('#jsExecute').html(data);
                            });
                        }else{
                            wStockMvt.setDimension(430, 670);
                            $.get("index.php?ajax=1&act=cat_win-stockmvt_transfert_init&id_product="+id_product+"&id_product_attribute="+id_product_attribute+"&id_warehouse="+id_warehouse+"&id_lang="+SC_ID_LANG,function(data){
                                wStockMvt.show();
                                $('#jsExecute').html(data);
                            });
                        }
                    }
                    else
                    {
                        alert('<?php echo _l('You have to select a row in the "Advanced Stock" grid, to be able to add stock to this reference', 1); ?>');
                    }
                }
                else if (id=='warehousestock_mvt_add')
                {
                    if(prop_tb._advancedStockGrid.getSelectedRowId())
                    {
                        var temp_ids = prop_tb._advancedStockGrid.getSelectedRowId().split("_");
                        var id_product = temp_ids[0];
                        var id_product_attribute = temp_ids[1];
                        var id_warehouse = temp_ids[2];
                    
                        if (!dhxWins.isWindow("wStockMvt"))
                        {
                            wStockMvt = dhxWins.createWindow("wStockMvt", ($(window).width()/2-215), 50, 430, 650);
                            wStockMvt.setText("<?php echo _l('Create a new stock movement'); ?>");
                            $.get("index.php?ajax=1&act=cat_win-stockmvt_add_init&id_product="+id_product+"&id_product_attribute="+id_product_attribute+"&id_warehouse="+id_warehouse+"&id_lang="+SC_ID_LANG,function(data){
                                wStockMvt.show();
                                $('#jsExecute').html(data);
                            });
                        }else{
                            wStockMvt.setDimension(430, 650);
                            $.get("index.php?ajax=1&act=cat_win-stockmvt_add_init&id_product="+id_product+"&id_product_attribute="+id_product_attribute+"&id_warehouse="+id_warehouse+"&id_lang="+SC_ID_LANG,function(data){
                                wStockMvt.show();
                                $('#jsExecute').html(data);
                            });;
                        }
                    }
                    else
                    {
                        alert('<?php echo _l('You have to select a row in the "Advanced Stock" grid, to be able to add stock to this reference', 1); ?>');
                    }
                }
                else if (id=='warehousestock_mvt_delete')
                {
                    if(prop_tb._advancedStockGrid.getSelectedRowId())
                    {
                        var temp_ids = prop_tb._advancedStockGrid.getSelectedRowId().split("_");
                        var id_product = temp_ids[0];
                        var id_product_attribute = temp_ids[1];
                        var id_warehouse = temp_ids[2];
                    
                        if (!dhxWins.isWindow("wStockMvt"))
                        {
                            wStockMvt = dhxWins.createWindow("wStockMvt", ($(window).width()/2-215), 100, 430, 475);
                            wStockMvt.setText("<?php echo _l('Create a new stock movement'); ?>");
                            $.get("index.php?ajax=1&act=cat_win-stockmvt_delete_init&id_product="+id_product+"&id_product_attribute="+id_product_attribute+"&id_warehouse="+id_warehouse+"&id_lang="+SC_ID_LANG,function(data){
                                wStockMvt.show();
                                $('#jsExecute').html(data);
                            });
                        }else{
                            wStockMvt.setDimension(430, 475);
                            $.get("index.php?ajax=1&act=cat_win-stockmvt_delete_init&id_product="+id_product+"&id_product_attribute="+id_product_attribute+"&id_warehouse="+id_warehouse+"&id_lang="+SC_ID_LANG,function(data){
                                wStockMvt.show();
                                $('#jsExecute').html(data);
                            });
                        }
                    }
                    else
                    {
                        alert('<?php echo _l('You have to select a row in the "Advanced Stock" grid, to be able to delete stock from this reference', 1); ?>');
                    }
                }
                else if (id=='exportcsv'){
                    displayQuickExportWindow(prop_tb._advancedStockGrid,1);
                }
            });

            prop_tb._advancedStock_tb.attachEvent("onStateChange",function(id,state){
                if (id=='warehousestock_groupByProduct')
                {
                    if (state)
                    {
                        advancedStockGrid_groupByProduct = 1;
                    }else{
                        advancedStockGrid_groupByProduct = 0;
                        prop_tb._advancedStockGrid.unGroup();
                    }
                    idxProductId=prop_tb._advancedStockGrid.getColIndexById('id_product');
                    if(idxProductId!=undefined)
                        displayAdvancedStock();
                }
            });

            prop_tb._advancedStockGrid = prop_tb._advancedStock.attachGrid();
            prop_tb._advancedStockGrid._name='_advancedStockGrid';
            prop_tb._advancedStockGrid.setImagePath("lib/js/imgs/");
              prop_tb._advancedStockGrid.enableDragAndDrop(false);
            prop_tb._advancedStockGrid.enableMultiselect(false);
            
            // UISettings
            prop_tb._advancedStockGrid._uisettings_prefix='cat_warehousestock';
            prop_tb._advancedStockGrid._uisettings_name=prop_tb._advancedStockGrid._uisettings_prefix;
               prop_tb._advancedStockGrid._first_loading=1;
               
            // UISettings
            initGridUISettings(prop_tb._advancedStockGrid);
            
            
            prop_tb._advancedStockGrid.attachEvent("onRowSelect",function (idstock){
                if (propertiesPanel=='warehousestock'){
                    displayStockMovements();
                }
            });
            prop_tb._advancedStockGrid.attachEvent("onRowDblClicked", function(rId,cInd){
                dhtmlx.message({text:'<?php echo _l('To update quantities, you need to create a stock movement.', 1); ?>',type:'error',expire:5000});
            });
            
            // MOVEMENTS
            prop_tb._stockMovement = prop_tb._warehousestockLayout.cells('b');
            prop_tb._stockMovement.setText('<?php echo _l('Stock movements history', 1); ?>');
            
            prop_tb._stockMovement_tb = prop_tb._stockMovement.attachToolbar();
             prop_tb._stockMovement_tb.setIconset('awesome');
            prop_tb._stockMovement_tb.addButton("warehousestock_mvt_refresh", 100, "", "fa fa-sync green", "fa fa-sync green");
            prop_tb._stockMovement_tb.setItemToolTip('warehousestock_mvt_refresh','<?php echo _l('Refresh grid', 1); ?>');
            prop_tb._stockMovement_tb.addButton("exportcsv", 100, "", "fad fa-file-csv green", "fad fa-file-csv green");
            prop_tb._stockMovement_tb.setItemToolTip('exportcsv','<?php echo _l('Export grid to clipboard in CSV format for MSExcel with tab delimiter.'); ?>');
            prop_tb._stockMovement_tb.attachEvent("onClick", function(id){
                if (id=='warehousestock_mvt_refresh')
                {
                    displayStockMovements();
                }
                else if (id=='exportcsv'){
                    displayQuickExportWindow(prop_tb._stockMovementGrid,1);
                }
            });

            prop_tb._stockMovementGrid = prop_tb._stockMovement.attachGrid();
            prop_tb._stockMovementGrid._name='_stockMovementGrid';
            prop_tb._stockMovementGrid.setImagePath("lib/js/imgs/");
              prop_tb._stockMovementGrid.enableDragAndDrop(false);
            prop_tb._stockMovementGrid.enableMultiselect(true);
            
            // UISettings
            prop_tb._stockMovementGrid._uisettings_prefix='cat_warehousestock_movement';
            prop_tb._stockMovementGrid._uisettings_name=prop_tb._stockMovementGrid._uisettings_prefix;
               prop_tb._stockMovementGrid._first_loading=1;
               
            // UISettings
            initGridUISettings(prop_tb._stockMovementGrid);
            
            needInitwarehousestock=0;
        }
    }

    function setPropertiesPanel_warehousestock(id){
        if (id=='warehousestock')
        {
            if(lastProductSelID!=undefined && lastProductSelID!="")
            {
                dhxLayout.cells('b').setText('<?php echo _l('Properties', 1).' '._l('of', 1); ?> '+getSelectedItemValueOrID(cat_grid,lastProductSelID,'name'));
            }
            hidePropTBButtons();
            prop_tb.setItemText('panel', '<?php echo _l('Advanced stocks', 1); ?>');
            prop_tb.setItemImage('panel', 'fad fa-external-link green');
            needInitwarehousestock = 1;
            initwarehousestock();
            propertiesPanel='warehousestock';
            if (lastProductSelID!=0)
            {
                displayAdvancedStock();
                displayStockMovements();
            }
        }
    }
    prop_tb.attachEvent("onClick", setPropertiesPanel_warehousestock);

    function displayAdvancedStock()
    {
        prop_tb._advancedStockGrid.clearAll(true);
        var tempIdList = (cat_grid.getSelectedRowId()!=null?cat_grid.getSelectedRowId():"");
        $.post("index.php?ajax=1&act=cat_warehousestock_get&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{'idlist': tempIdList},function(data)
        {
            prop_tb._advancedStockGrid.parse(data);
            nb=prop_tb._advancedStockGrid.getRowsNum();
            prop_tb._advancedStockGrid._rowsNum=nb;

            groupByProduct();
            
           // UISettings
            loadGridUISettings(prop_tb._advancedStockGrid);
            prop_tb._advancedStockGrid._first_loading=0;
        });
    }

    function displayStockMovements()
    {
        prop_tb._stockMovementGrid.clearAll(true);
        var tempIdList = (cat_grid.getSelectedRowId()!=null?cat_grid.getSelectedRowId():"");
        var id_product = 0;
        var id_product_attribute = 0;
        var id_warehouse = 0;
        if(prop_tb._advancedStockGrid.getSelectedRowId()!=null)
        {
            var temp_ids = prop_tb._advancedStockGrid.getSelectedRowId().split("_");
            var id_product = temp_ids[0];
            var id_product_attribute = temp_ids[1];
            var id_warehouse = temp_ids[2];
        }
        
        $.post("index.php?ajax=1&act=cat_warehousestock_movement_get&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{'idlist': tempIdList, 'id_product':id_product, 'id_product_attribute':id_product_attribute, 'id_warehouse':id_warehouse},function(data)
        {
            prop_tb._stockMovementGrid.parse(data);
            nb=prop_tb._stockMovementGrid.getRowsNum();
            prop_tb._stockMovementGrid._rowsNum=nb;
            
           // UISettings
            loadGridUISettings(prop_tb._stockMovementGrid);
            prop_tb._stockMovementGrid._first_loading=0;
        });
    }


    function groupByProduct()
    {
        idxProductName=prop_tb._advancedStockGrid.getColIndexById('name');
        if (advancedStockGrid_groupByProduct=="1")
        {
            if(idxProductName!=undefined)
                prop_tb._advancedStockGrid.groupBy(idxProductName);
        }
    }

    let warehousestock_current_id = 0;
    cat_grid.attachEvent("onRowSelect",function (idproduct){
        if (propertiesPanel=='warehousestock' && (cat_grid.getSelectedRowId()!==null && warehousestock_current_id!=idproduct)){
            //initwarehousestock();
            displayAdvancedStock();
            displayStockMovements();
            warehousestock_current_id=idproduct;
        }
    });
<?php } ?>