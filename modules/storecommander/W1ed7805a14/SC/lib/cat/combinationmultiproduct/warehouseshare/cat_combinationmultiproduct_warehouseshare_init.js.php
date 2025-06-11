<?php
 if (SCAS && _r('ACT_CAT_ADVANCED_STOCK_MANAGEMENT')) { ?>

// INITIALISATION TOOLBAR
prop_tb.attachEvent("onClick", function setPropertiesPanel_combinationmultiproduct(id){
    if (id=='combinationmultiproduct')
    {
        prop_tb.combimulprd_subproperties_tb.addListOption('combimulprdSubProperties', 'combimulprd_warehouses', 9, "button", '<?php echo _l('Warehouses', 1); ?>', "fa fa-building");

        prop_tb.combimulprd_subproperties_tb.attachEvent("onClick", function(id){
            if(id=="combimulprd_warehouses")
            {
                hideCombinationMultiProduct_SubpropertiesItems();
                prop_tb.combimulprd_subproperties_tb.setItemText('combimulprdSubProperties', '<?php echo _l('Warehouses', 1); ?>');
                prop_tb.combimulprd_subproperties_tb.setItemImage('combimulprdSubProperties', 'fa fa-building');
                actual_subproperties = "combimulprd_warehouses";
                initCombinationMultiProductWarehouseshare();
            }
        });
                
        prop_tb._combinationmultiproductGrid.attachEvent("onRowSelect", function(id,ind){
            if (!prop_tb._combinationmultiproductLayout.cells('b').isCollapsed())
            {
                if(actual_subproperties == "combimulprd_warehouses"){
                     getCombinationMultiProductWarehouseshares();
                }
            }
        });
        
        prop_tb.combimulprd_subproperties_tb.addButton("warehouseshare_refresh", 100, "", "fa fa-sync green", "fa fa-sync green");
        prop_tb.combimulprd_subproperties_tb.setItemToolTip('warehouseshare_refresh','<?php echo _l('Refresh grid', 1); ?>');
        prop_tb.combimulprd_subproperties_tb.addButton("warehouseshare_add_select", 100, "", "fa fa-plus-circle green", "fa fa-plus-circle green");
        prop_tb.combimulprd_subproperties_tb.setItemToolTip('warehouseshare_add_select','<?php echo _l('Add all selected products to all selected warehouses', 1); ?>');
        prop_tb.combimulprd_subproperties_tb.addButton("warehouseshare_del_select", 100, "", "fa fa-minus-circle red", "fa fa-minus-circle red");
        prop_tb.combimulprd_subproperties_tb.setItemToolTip('warehouseshare_del_select','<?php echo _l('Delete all selected products from all selected warehouses', 1); ?>');

        prop_tb.combimulprd_subproperties_tb.attachEvent("onClick", function(id){
                if (id=='warehouseshare_add_select')
                {
                    if(prop_tb._combinationmultiproductWarehousesGrid.getSelectedRowId()!="" && prop_tb._combinationmultiproductWarehousesGrid.getSelectedRowId()!=null)
                    {
                        $.post("index.php?ajax=1&act=cat_combinationmultiproduct_warehouseshare_update&action=mass_present&value=true&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{"idlist":prop_tb._combinationmultiproductGrid.getSelectedRowId(), "id_warehouse":prop_tb._combinationmultiproductWarehousesGrid.getSelectedRowId()},function(data){
                            getCombinationMultiProductWarehouseshares();
                            combimulprdWriteRefresh();
                        });
                    }
                }
                if (id=='warehouseshare_del_select')
                {
                    if(prop_tb._combinationmultiproductWarehousesGrid.getSelectedRowId()!="" && prop_tb._combinationmultiproductWarehousesGrid.getSelectedRowId()!=null)
                    {
                        $.post("index.php?ajax=1&act=cat_combinationmultiproduct_warehouseshare_update&action=mass_present&value=false&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{"idlist":prop_tb._combinationmultiproductGrid.getSelectedRowId(), "id_warehouse":prop_tb._combinationmultiproductWarehousesGrid.getSelectedRowId()},function(data){
                            getCombinationMultiProductWarehouseshares();
                            combimulprdWriteRefresh();
                        });
                    }
                }
                if (id=='warehouseshare_refresh')
                {
                    getCombinationMultiProductWarehouseshares();
                }
        });
    }
});
            
// INIT GRID
function initCombinationMultiProductWarehouseshare()
{
    hideCombinationMultiProduct_SubpropertiesItems();
    prop_tb.combimulprd_subproperties_tb.showItem('warehouseshare_refresh');
    prop_tb.combimulprd_subproperties_tb.showItem('warehouseshare_add_select');
    prop_tb.combimulprd_subproperties_tb.showItem('warehouseshare_del_select');
    
    prop_tb._combinationmultiproductWarehousesGrid = prop_tb._combinationmultiproductLayout.cells('b').attachGrid();
    prop_tb._combinationmultiproductWarehousesGrid.setImagePath("lib/js/imgs/");
    
    prop_tb._combinationmultiproductWarehousesGrid.enableDragAndDrop(false);
    prop_tb._combinationmultiproductWarehousesGrid.enableMultiselect(true);

    // UISettings
    prop_tb._combinationmultiproductWarehousesGrid._uisettings_prefix='cat_combinationmultiproduct_warehouseshare';
    prop_tb._combinationmultiproductWarehousesGrid._uisettings_name=prop_tb._combinationmultiproductWarehousesGrid._uisettings_prefix;
       prop_tb._combinationmultiproductWarehousesGrid._first_loading=1;
       
    // UISettings
    initGridUISettings(prop_tb._combinationmultiproductWarehousesGrid);
    
    prop_tb._combinationmultiproductWarehousesGrid.attachEvent("onEditCell",function(stage,rId,cInd,nValue,oValue)
    {
        if(stage==1)
        {
            idxPresent=prop_tb._combinationmultiproductWarehousesGrid.getColIndexById('present');
        
            var action = "";
            if(cInd==idxPresent)
                action = "present";
            
            if(action=="present")
            {
                var value = prop_tb._combinationmultiproductWarehousesGrid.cells(rId,cInd).isChecked();
                $.post("index.php?ajax=1&act=cat_combinationmultiproduct_warehouseshare_update&id_warehouse="+rId+"&action="+action+"&value="+value+"&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{"idlist":prop_tb._combinationmultiproductGrid.getSelectedRowId()},function(data){
                    getCombinationMultiProductWarehouseshares();
                    combimulprdWriteRefresh();
                });
            }
            
        }
        else if(stage==2)
        {
            idxLocation=prop_tb._combinationmultiproductWarehousesGrid.getColIndexById('location');
            if(idxLocation!=undefined && idxLocation!=null )
            {
                idxPresent=prop_tb._combinationmultiproductWarehousesGrid.getColIndexById('present');
                var action = "";
                if(cInd==idxLocation)
                    action = "location";
                
                if(action=="location")
                {
                    var value = prop_tb._combinationmultiproductWarehousesGrid.cells(rId,cInd).getValue();
                    $.post("index.php?ajax=1&act=cat_combinationmultiproduct_warehouseshare_update&id_warehouse="+rId+"&action="+action+"&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{"value":value,"idlist":prop_tb._combinationmultiproductGrid.getSelectedRowId()},function(data){
                        if(!prop_tb._combinationmultiproductWarehousesGrid.cells(rId,idxPresent).isChecked())
                            getCombinationMultiProductWarehouseshares();
                    });
                }
            }
        }
        return true;
    });
    
    prop_tb._combinationmultiproductWarehousesGrid.attachEvent("onRowDblClicked", function(rId,cInd){
        idxASM=cat_grid.getColIndexById('advanced_stock_management');
        
        idxQty=prop_tb._combinationmultiproductWarehousesGrid.getColIndexById('quantity');
        idxPresent=prop_tb._combinationmultiproductWarehousesGrid.getColIndexById('present');        

        if(cInd==idxQty && prop_tb._combinationmultiproductGrid.getSelectedRowId().split(",").length==1)
        {
            if(cat_grid.getUserData(cat_grid.getSelectedRowId(),"type_advanced_stock_management")=="2" && prop_tb._combinationmultiproductWarehousesGrid.cells(rId,idxPresent).isChecked())
            {
                if (!dhxWins.isWindow("wStockMvt"))
                {
                    wStockMvt = dhxWins.createWindow("wStockMvt", ($(window).width()/2-200), 50, 430, 600);
                    wStockMvt.setText("<?php echo _l('Create a new stock movement'); ?>");
                    wStockMvt.show();
                    $.post("index.php?ajax=1&act=cat_win-stockmvt_choose_init&id_product="+lastProductSelID+"&id_warehouse="+rId+"&id_lang="+SC_ID_LANG,{"id_product_attribute":prop_tb._combinationmultiproductGrid.getSelectedRowId()},function(data){
                            $('#jsExecute').html(data);
                        });
                }else{
                    wStockMvt.setDimension(430, 600);
                    wStockMvt.show();
                    $.post("index.php?ajax=1&act=cat_win-stockmvt_choose_init&id_product="+lastProductSelID+"&id_warehouse="+rId+"&id_lang="+SC_ID_LANG,{"id_product_attribute":prop_tb._combinationmultiproductGrid.getSelectedRowId()},function(data){
                            $('#jsExecute').html(data);
                        });
                }
                
                return false;
            }
        }
        return true;
    });
    
    getCombinationMultiProductWarehouseshares();
}

function getCombinationMultiProductWarehouseshares()
{
    prop_tb._combinationmultiproductWarehousesGrid.clearAll(true);
    var tempIdList = (prop_tb._combinationmultiproductGrid.getSelectedRowId()!=null?prop_tb._combinationmultiproductGrid.getSelectedRowId():"");
    $.post("index.php?ajax=1&act=cat_combinationmultiproduct_warehouseshare_get&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{'idlist': tempIdList},function(data)
    {
        prop_tb._combinationmultiproductWarehousesGrid.parse(data);
        nb=prop_tb._combinationmultiproductWarehousesGrid.getRowsNum();
        prop_tb._combinationmultiproductWarehousesGrid._rowsNum=nb;
        
       // UISettings
        loadGridUISettings(prop_tb._combinationmultiproductWarehousesGrid);
        prop_tb._combinationmultiproductWarehousesGrid._first_loading=0;
        
        idxPresent=prop_tb._combinationmultiproductWarehousesGrid.getColIndexById('present');
    });
}

<?php } ?>