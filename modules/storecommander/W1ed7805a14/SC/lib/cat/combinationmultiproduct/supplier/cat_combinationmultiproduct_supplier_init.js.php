<?php
 if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) { ?>
    // INITIALISATION TOOLBAR
    prop_tb.attachEvent("onClick", function setPropertiesPanel_combinationmultiproduct(id){
        if (id=='combinationmultiproduct')
        {
            prop_tb.combimulprd_subproperties_tb.addListOption('combimulprdSubProperties', 'combimulprd_suppliers', 9, "button", '<?php echo _l('Suppliers', 1); ?>', "fa fa-cube yellow");

            prop_tb.combimulprd_subproperties_tb.attachEvent("onClick", function(id){
                if(id=="combimulprd_suppliers")
                {
                    hideCombinationMultiProduct_SubpropertiesItems();
                    prop_tb.combimulprd_subproperties_tb.setItemText('combimulprdSubProperties', '<?php echo _l('Suppliers', 1); ?>');
                    prop_tb.combimulprd_subproperties_tb.setItemImage('combimulprdSubProperties', 'fa fa-cube yellow');
                    actual_subproperties = "combimulprd_suppliers";
                    initCombinationMultiProductSuppliershare();
                }
            });
            
            prop_tb._combinationmultiproductGrid.attachEvent("onRowSelect", function(id,ind){
                if (!prop_tb._combinationmultiproductLayout.cells('b').isCollapsed())
                {
                    if(actual_subproperties == "combimulprd_suppliers"){
                         getCombinationMultiProductSuppliers();
                    }
                }
            });
            
            prop_tb.combimulprd_subproperties_tb.addButton("supplier_refresh", 100, "", "fa fa-sync green", "fa fa-sync green");
            prop_tb.combimulprd_subproperties_tb.setItemToolTip('supplier_refresh','<?php echo _l('Refresh grid', 1); ?>');
            prop_tb.combimulprd_subproperties_tb.addButton("supplier_add_select", 100, "", "fad fa-link yellow", "fad fa-link yellow");
            prop_tb.combimulprd_subproperties_tb.setItemToolTip('supplier_add_select','<?php echo _l('Add all selected products to all selected suppliers', 1); ?>');
            prop_tb.combimulprd_subproperties_tb.addButton("supplier_del_select", 100, "", "fad fa-unlink red", "fad fa-unlink red");
            prop_tb.combimulprd_subproperties_tb.setItemToolTip('supplier_del_select','<?php echo _l('Remove all selected products from all selected suppliers', 1); ?>');
            prop_tb.combimulprd_subproperties_tb.attachEvent("onClick", function(id){
                    if (id=='supplier_add_select')
                    {
                        if(prop_tb._combinationmultiproductSuppliersGrid.getSelectedRowId()!="" && prop_tb._combinationmultiproductSuppliersGrid.getSelectedRowId()!=null)
                        {
                            $.post("index.php?ajax=1&act=cat_combinationmultiproduct_supplier_update&action=mass_present&value=true&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{"idlist":prop_tb._combinationmultiproductGrid.getSelectedRowId(),"id_supplier":prop_tb._combinationmultiproductSuppliersGrid.getSelectedRowId()},function(data){
                                getCombinationMultiProductSuppliers();
                            });
                        }
                    }
                    if (id=='supplier_del_select')
                    {
                        if(prop_tb._combinationmultiproductSuppliersGrid.getSelectedRowId()!="" && prop_tb._combinationmultiproductSuppliersGrid.getSelectedRowId()!=null)
                        {
                            $.post("index.php?ajax=1&act=cat_combinationmultiproduct_supplier_update&action=mass_present&value=false&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{"idlist":prop_tb._combinationmultiproductGrid.getSelectedRowId(),"id_supplier":prop_tb._combinationmultiproductSuppliersGrid.getSelectedRowId()},function(data){
                                getCombinationMultiProductSuppliers();
                            });
                        }
                    }
                    if (id=='supplier_refresh')
                    {
                        getCombinationMultiProductSuppliers();
                    }
            });
        }
    });
    
    
    // INIT GRID
    function initCombinationMultiProductSuppliershare()
    {
         hideCombinationMultiProduct_SubpropertiesItems();
        prop_tb.combimulprd_subproperties_tb.showItem('supplier_refresh');
        prop_tb.combimulprd_subproperties_tb.showItem('supplier_add_select');
        prop_tb.combimulprd_subproperties_tb.showItem('supplier_del_select');
        
        prop_tb._combinationmultiproductSuppliersGrid = prop_tb._combinationmultiproductLayout.cells('b').attachGrid();
        prop_tb._combinationmultiproductSuppliersGrid.setImagePath("lib/js/imgs/");
        
        prop_tb._combinationmultiproductSuppliersGrid.enableDragAndDrop(false);
        prop_tb._combinationmultiproductSuppliersGrid.enableMultiselect(true);
    
        // UISettings
        prop_tb._combinationmultiproductSuppliersGrid._uisettings_prefix='cat_combinationmultiproduct_supplier';
        prop_tb._combinationmultiproductSuppliersGrid._uisettings_name=prop_tb._combinationmultiproductSuppliersGrid._uisettings_prefix;
           prop_tb._combinationmultiproductSuppliersGrid._first_loading=1;
           
        // UISettings
        initGridUISettings(prop_tb._combinationmultiproductSuppliersGrid);
        
        prop_tb._combinationmultiproductSuppliersGrid.attachEvent("onEditCell",function(stage,rId,cInd,nValue,oValue)
        {
            if(stage==1)
            {
                idxPresent=prop_tb._combinationmultiproductSuppliersGrid.getColIndexById('present');
                idxDefault=prop_tb._combinationmultiproductSuppliersGrid.getColIndexById('default');
                var action = "";
                if(cInd==idxPresent)
                    action = "present";
                else if(cInd==idxDefault)
                    action = "default";
                
                if(action=="present" || action=="default")
                {
                    var value = prop_tb._combinationmultiproductSuppliersGrid.cells(rId,cInd).isChecked();
                    $.post("index.php?ajax=1&act=cat_combinationmultiproduct_supplier_update&id_supplier="+rId+"&action="+action+"&value="+value+"&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{"idlist":prop_tb._combinationmultiproductGrid.getSelectedRowId()},function(data){
                        getCombinationMultiProductSuppliers();
                    });
                }
                
            }
            else if(stage==2)
            {
                idxProductSupplierReference=prop_tb._combinationmultiproductSuppliersGrid.getColIndexById('product_supplier_reference');
                idxProductSupplierPriceTe=prop_tb._combinationmultiproductSuppliersGrid.getColIndexById('product_supplier_price_te');
                idxIdCurrency=prop_tb._combinationmultiproductSuppliersGrid.getColIndexById('id_currency');
                
                var field = "";
                if(cInd==idxProductSupplierReference)
                    field = "product_supplier_reference";
                else if(cInd==idxProductSupplierPriceTe)
                    field = "product_supplier_price_te";
                else if(cInd==idxIdCurrency)
                    field = "id_currency";
                var action = "fields";
                if(field!=undefined && field!=null && field!="")
                {
                    var value = prop_tb._combinationmultiproductSuppliersGrid.cells(rId,cInd).getValue();
                    $.post("index.php?ajax=1&act=cat_combinationmultiproduct_supplier_update&id_supplier="+rId+"&action="+action+"&field="+field+"&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{"value":value, "idlist":prop_tb._combinationmultiproductGrid.getSelectedRowId()},function(data){
                    });
                }
            }
            return true;
        });
        
        getCombinationMultiProductSuppliers();
    }
    
    function getCombinationMultiProductSuppliers()
    {
        prop_tb._combinationmultiproductSuppliersGrid.clearAll(true);
        var tempIdList = (prop_tb._combinationmultiproductGrid.getSelectedRowId()!=null?prop_tb._combinationmultiproductGrid.getSelectedRowId():"");
        $.post("index.php?ajax=1&act=cat_combinationmultiproduct_supplier_get&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{'idlist': tempIdList, 'id_product': lastProductSelID},function(data)
        {
            prop_tb._combinationmultiproductSuppliersGrid.parse(data);
            nb=prop_tb._combinationmultiproductSuppliersGrid.getRowsNum();
            prop_tb._combinationmultiproductSuppliersGrid._rowsNum=nb;
            
           // UISettings
            loadGridUISettings(prop_tb._combinationmultiproductSuppliersGrid);
            prop_tb._combinationmultiproductSuppliersGrid._first_loading=0;
        });
    }
<?php } ?>