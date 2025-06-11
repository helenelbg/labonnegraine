<?php
 if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) { ?>
    // INITIALISATION TOOLBAR
    prop_tb.attachEvent("onClick", function setPropertiesPanel_combinations(id){
        if (id=='combinations')
        {
            prop_tb.combi_subproperties_tb.addListOption('combiSubProperties', 'combi_suppliers', 9, "button", '<?php echo _l('Suppliers', 1); ?>', "fad fa-parachute-box");

            prop_tb.combi_subproperties_tb.attachEvent("onClick", function(id){
                if(id=="combi_suppliers")
                {
                    hideSubpropertiesItems();
                    prop_tb.combi_subproperties_tb.setItemText('combiSubProperties', '<?php echo _l('Suppliers', 1); ?>');
                    prop_tb.combi_subproperties_tb.setItemImage('combiSubProperties', 'fad fa-parachute-box');
                    actual_subproperties = "combi_suppliers";
                    initCombinationSuppliershare();
                }
            });
                    
            prop_tb._combinationsGrid.attachEvent("onRowSelect", function(id,ind){
                if (!prop_tb._combinationsLayout.cells('b').isCollapsed())
                {
                    if(actual_subproperties == "combi_suppliers"){
                         getCombinationsSuppliers();
                    }
                }
            });
        }
    });
    
            
    // INIT GRID
    function initCombinationSuppliershare()
    {
        prop_tb.combi_subproperties_tb.addButton("supplier_refresh", 100, "", "fa fa-sync green", "fa fa-sync green");
        prop_tb.combi_subproperties_tb.setItemToolTip('supplier_refresh','<?php echo _l('Refresh grid', 1); ?>');
        prop_tb.combi_subproperties_tb.addButton("supplier_add_select", 100, "", "fad fa-link yellow", "fad fa-link yellow");
        prop_tb.combi_subproperties_tb.setItemToolTip('supplier_add_select','<?php echo _l('Add all selected products to all selected suppliers', 1); ?>');
        prop_tb.combi_subproperties_tb.addButton("supplier_del_select", 100, "", "fad fa-unlink red", "fad fa-unlink red");
        prop_tb.combi_subproperties_tb.setItemToolTip('supplier_del_select','<?php echo _l('Remove all selected products from all selected suppliers', 1); ?>');


        prop_tb.combi_subproperties_tb.attachEvent("onClick", function(id){
            if (id=='supplier_add_select')
            {
                if(prop_tb._combinationsSuppliersGrid.getSelectedRowId()!="" && prop_tb._combinationsSuppliersGrid.getSelectedRowId()!=null)
                {
                    $.post("index.php?ajax=1&act=cat_combination_supplier_update&action=mass_present&value=true&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{"idlist":prop_tb._combinationsGrid.getSelectedRowId(),"id_supplier":prop_tb._combinationsSuppliersGrid.getSelectedRowId()},function(data){
                        getCombinationsSuppliers();
                    });
                }
            }
            if (id=='supplier_del_select')
            {
                if(prop_tb._combinationsSuppliersGrid.getSelectedRowId()!="" && prop_tb._combinationsSuppliersGrid.getSelectedRowId()!=null)
                {
                    $.post("index.php?ajax=1&act=cat_combination_supplier_update&action=mass_present&value=false&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{"idlist":prop_tb._combinationsGrid.getSelectedRowId(),"id_supplier":prop_tb._combinationsSuppliersGrid.getSelectedRowId()},function(data){
                        getCombinationsSuppliers();
                    });
                }
            }
            if (id=='supplier_refresh')
            {
                getCombinationsSuppliers();
            }
        });
        prop_tb.combi_subproperties_tb.showItem('supplier_refresh');
        prop_tb.combi_subproperties_tb.showItem('supplier_add_select');
        prop_tb.combi_subproperties_tb.showItem('supplier_del_select');
        
        prop_tb._combinationsSuppliersGrid = prop_tb._combinationsLayout.cells('b').attachGrid();
        prop_tb._combinationsSuppliersGrid.setImagePath("lib/js/imgs/");
        
        prop_tb._combinationsSuppliersGrid.enableDragAndDrop(false);
        prop_tb._combinationsSuppliersGrid.enableMultiselect(true);
    
        // UISettings
        prop_tb._combinationsSuppliersGrid._uisettings_prefix='cat_combination_supplier';
        prop_tb._combinationsSuppliersGrid._uisettings_name=prop_tb._combinationsSuppliersGrid._uisettings_prefix;
           prop_tb._combinationsSuppliersGrid._first_loading=1;
           
        // UISettings
        initGridUISettings(prop_tb._combinationsSuppliersGrid);
        
        prop_tb._combinationsSuppliersGrid.attachEvent("onEditCell",function(stage,rId,cInd,nValue,oValue)
        {
            if(stage==1)
            {
                idxPresent=prop_tb._combinationsSuppliersGrid.getColIndexById('present');
                idxDefault=prop_tb._combinationsSuppliersGrid.getColIndexById('default');                
                var action = "";
                if(cInd==idxPresent)
                    action = "present";
                else if(cInd==idxDefault)
                    action = "default";
                
                if(action=="present" || action=="default")
                {
                    var value = prop_tb._combinationsSuppliersGrid.cells(rId,cInd).isChecked();
                    $.post("index.php?ajax=1&act=cat_combination_supplier_update&id_supplier="+rId+"&action="+action+"&value="+value+"&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{"idlist":prop_tb._combinationsGrid.getSelectedRowId()},function(data){
                        getCombinationsSuppliers();
                    });
                }
                
            }
            else if(stage==2)
            {
                idxProductSupplierReference=prop_tb._combinationsSuppliersGrid.getColIndexById('product_supplier_reference');
                idxProductSupplierPriceTe=prop_tb._combinationsSuppliersGrid.getColIndexById('product_supplier_price_te');
                idxIdCurrency=prop_tb._combinationsSuppliersGrid.getColIndexById('id_currency');
                
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
                    var value = prop_tb._combinationsSuppliersGrid.cells(rId,cInd).getValue();
                    $.post("index.php?ajax=1&act=cat_combination_supplier_update&id_supplier="+rId+"&action="+action+"&field="+field+"&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{"value":value, "idlist":prop_tb._combinationsGrid.getSelectedRowId()},function(data){
                    });
                }
            }
            return true;
        });
        
        getCombinationsSuppliers();
    }
    
    function getCombinationsSuppliers()
    {
        prop_tb._combinationsSuppliersGrid.clearAll(true);
        var tempIdList = (prop_tb._combinationsGrid.getSelectedRowId()!=null?prop_tb._combinationsGrid.getSelectedRowId():"");
        $.post("index.php?ajax=1&act=cat_combination_supplier_get&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{'idlist': tempIdList, 'id_product': lastProductSelID},function(data)
        {
            prop_tb._combinationsSuppliersGrid.parse(data);
            nb=prop_tb._combinationsSuppliersGrid.getRowsNum();
            prop_tb._combinationsSuppliersGrid._rowsNum=nb;
            
           // UISettings
            loadGridUISettings(prop_tb._combinationsSuppliersGrid);
            prop_tb._combinationsSuppliersGrid._first_loading=0;
        });
    }
<?php } ?>