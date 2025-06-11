<?php
if (SCMS && _r('GRI_CAT_PROPERTIES_GRID_MB_SHARE') && version_compare(_PS_VERSION_, '1.6.0.0', '>=')) { ?>

// INITIALISATION TOOLBAR
prop_tb.attachEvent("onClick", function setPropertiesPanel_combinationmultiproduct(id){
    if (id=='combinationmultiproduct')
    {
        prop_tb.combimulprd_subproperties_tb.addListOption('combimulprdSubProperties', 'combimulprd_shop', 9, "button", '<?php echo _l('Multistore sharing manager', 1); ?>', "fa fa-sitemap white");

        prop_tb.combimulprd_subproperties_tb.attachEvent("onClick", function(id){
            if(id=="combimulprd_shop")
            {
                hideCombinationMultiProduct_SubpropertiesItems();
                prop_tb.combimulprd_subproperties_tb.setItemText('combimulprdSubProperties', '<?php echo _l('Multistore sharing manager', 1); ?>');
                prop_tb.combimulprd_subproperties_tb.setItemImage('combimulprdSubProperties', 'fa fa-sitemap white');
                actual_subproperties = "combimulprd_shop";
                initCombinationMultiProductshopshare();
            }
        });

        prop_tb._combinationmultiproductGrid.attachEvent("onRowSelect", function(id,ind){
            if (!prop_tb._combinationmultiproductLayout.cells('b').isCollapsed())
            {
                if(actual_subproperties == "combimulprd_shop"){
                     getCombinationMultiProductshopshares();
                }
            }
        });
        
        prop_tb.combimulprd_subproperties_tb.addButton("shopshare_refresh", 100, "", "fa fa-sync green", "fa fa-sync green");
        prop_tb.combimulprd_subproperties_tb.setItemToolTip('shopshare_refresh','<?php echo _l('Refresh grid', 1); ?>');
        prop_tb.combimulprd_subproperties_tb.addButton("shopshare_add_select", 100, "", "fa fa-plus-circle green", "fa fa-plus-circle green");
        prop_tb.combimulprd_subproperties_tb.setItemToolTip('shopshare_add_select','<?php echo _l('Add all selected combinations to all selected shop', 1); ?>');
        prop_tb.combimulprd_subproperties_tb.addButton("shopshare_del_select", 100, "", "fa fa-minus-circle red", "fa fa-minus-circle red");
        prop_tb.combimulprd_subproperties_tb.setItemToolTip('shopshare_del_select','<?php echo _l('Delete all selected combinations from all selected shop', 1); ?>');
        
        prop_tb.combimulprd_subproperties_tb.attachEvent("onClick", function(id){
                if (id=='shopshare_add_select')
                {
                    if(prop_tb._combinationmultiproductshopGrid.getSelectedRowId()!="" && prop_tb._combinationmultiproductshopGrid.getSelectedRowId()!=null)
                    {
                        $.post("index.php?ajax=1&act=cat_combinationmultiproduct_shopshare_update&action=mass_present&value=true&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{"id_shop":prop_tb._combinationmultiproductshopGrid.getSelectedRowId(),"idlist":prop_tb._combinationmultiproductGrid.getSelectedRowId()},function(data){
                            getCombinationMultiProductshopshares();
                        });
                    }
                }
                if (id=='shopshare_del_select')
                {
                    if(prop_tb._combinationmultiproductshopGrid.getSelectedRowId()!="" && prop_tb._combinationmultiproductshopGrid.getSelectedRowId()!=null)
                    {
                        $.post("index.php?ajax=1&act=cat_combinationmultiproduct_shopshare_update&id_product="+lastProductSelID+"&action=mass_present&value=false&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{"id_shop":prop_tb._combinationmultiproductshopGrid.getSelectedRowId(),"idlist":prop_tb._combinationmultiproductGrid.getSelectedRowId()},function(data){
                            getCombinationMultiProductshopshares();
                        });
                    }
                }
                if (id=='shopshare_refresh')
                {
                    getCombinationMultiProductshopshares();
                }
        });
    }
});

// INIT GRID
function initCombinationMultiProductshopshare()
{
    hideCombinationMultiProduct_SubpropertiesItems();
    prop_tb.combimulprd_subproperties_tb.showItem('shopshare_refresh');
    prop_tb.combimulprd_subproperties_tb.showItem('shopshare_add_select');
    prop_tb.combimulprd_subproperties_tb.showItem('shopshare_del_select');
    
    prop_tb._combinationmultiproductshopGrid = prop_tb._combinationmultiproductLayout.cells('b').attachGrid();
    prop_tb._combinationmultiproductshopGrid.setImagePath("lib/js/imgs/");
    
    prop_tb._combinationmultiproductshopGrid.enableDragAndDrop(false);
    prop_tb._combinationmultiproductshopGrid.enableMultiselect(true);

    // UISettings
    prop_tb._combinationmultiproductshopGrid._uisettings_prefix='cat_combinationmultiproduct_shopshare';
    prop_tb._combinationmultiproductshopGrid._uisettings_name=prop_tb._combinationmultiproductshopGrid._uisettings_prefix;
       prop_tb._combinationmultiproductshopGrid._first_loading=1;
       
    // UISettings
    initGridUISettings(prop_tb._combinationmultiproductshopGrid);
    
    prop_tb._combinationmultiproductshopGrid.attachEvent("onEditCell",function(stage,rId,cInd,nValue,oValue)
    {
        if(stage==1)
        {
            idxPresent=prop_tb._combinationmultiproductshopGrid.getColIndexById('present');
        
            var action = "";
            if(cInd==idxPresent)
                action = "present";
            
            if(action=="present")
            {
                var has_one_check = false;
               var value = prop_tb._combinationmultiproductshopGrid.cells(rId,cInd).isChecked();
                   if(value!="1")
                   {
                    prop_tb._combinationmultiproductshopGrid.forEachRow(function(id){
                        var value_row = prop_tb._combinationmultiproductshopGrid.cells(id,cInd).isChecked();
                        if(value_row=="1")
                            has_one_check = true;
                   });
                }
                else
                    has_one_check=true;
                    
                if(has_one_check==true)
                {
                    $.post("index.php?ajax=1&act=cat_combinationmultiproduct_shopshare_update&id_product="+lastProductSelID+"&id_shop="+rId+"&action="+action+"&value="+value+"&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{"idlist":prop_tb._combinationmultiproductGrid.getSelectedRowId()},function(data){
                        getCombinationMultiProductshopshares();
                    });
                }
                else
                {
                    dhtmlx.message({text:'<?php echo _l('At least one shop needs to be ticked', 1); ?>',type:'error',expire:10000});
                    prop_tb._combinationmultiproductshopGrid.cells(rId,cInd).setValue(1);
                }
            }
            
        }
        return true;
    });
    
    getCombinationMultiProductshopshares();
}

function getCombinationMultiProductshopshares()
{
    prop_tb._combinationmultiproductshopGrid.clearAll(true);
    var tempIdList = (prop_tb._combinationmultiproductGrid.getSelectedRowId()!=null?prop_tb._combinationmultiproductGrid.getSelectedRowId():"");
    $.post("index.php?ajax=1&act=cat_combinationmultiproduct_shopshare_get&id_product="+lastProductSelID+"&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{'idlist': tempIdList},function(data)
    {
        prop_tb._combinationmultiproductshopGrid.parse(data);
        nb=prop_tb._combinationmultiproductshopGrid.getRowsNum();
        prop_tb._combinationmultiproductshopGrid._rowsNum=nb;
        
       // UISettings
        loadGridUISettings(prop_tb._combinationmultiproductshopGrid);
        prop_tb._combinationmultiproductshopGrid._first_loading=0;
        
        idxPresent=prop_tb._combinationmultiproductshopGrid.getColIndexById('present');
    });
}

<?php } ?>