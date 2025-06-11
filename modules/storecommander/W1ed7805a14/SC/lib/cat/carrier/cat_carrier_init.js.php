<?php
if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) { ?>
    
    <?php if (_r('GRI_CAT_PROPERTIES_GRID_CARRIER')) { ?>
        prop_tb.addListOption('panel', 'carrier', 5, "button", '<?php echo _l('Carriers', 1); ?>', "fad fa-truck");
        allowed_properties_panel[allowed_properties_panel.length] = "carrier";
    <?php } ?>

    prop_tb.addButton("carrier_refresh",1000, "", "fa fa-sync green", "fa fa-sync green");
    prop_tb.setItemToolTip('carrier_refresh','<?php echo _l('Refresh grid', 1); ?>');
    prop_tb.addButton("carrier_selectall",1000, "", "fa fa-bolt yellow", "fad fa-bolt grey");
    prop_tb.setItemToolTip('carrier_selectall','<?php echo _l('Select all', 1); ?>');
    prop_tb.addButton("carrier_mass_add",1000, "", "fad fa-link yellow", "fad fa-link yellow");
    prop_tb.setItemToolTip('carrier_mass_add','<?php echo _l('Add all the selected products to all the selected carriers', 1); ?>');
    prop_tb.addButton("carrier_mass_delete",1000, "", "fad fa-unlink red", "fad fa-unlink red");
    prop_tb.setItemToolTip('carrier_mass_delete','<?php echo _l('Delete all the selected products to all the selected carriers', 1); ?>');
    
    
    needInitCarrier = 1;
    function initCarrier()
    {
        if (needInitCarrier)
        {
            prop_tb._carrierLayout = dhxLayout.cells('b').attachLayout('1C');
            prop_tb._carrierLayout.cells('a').hideHeader();
            dhxLayout.cells('b').showHeader();

            prop_tb._carrierGrid = prop_tb._carrierLayout.cells('a').attachGrid();
            prop_tb._carrierGrid._name='_carrierGrid';
            prop_tb._carrierGrid.setImagePath("lib/js/imgs/");
              prop_tb._carrierGrid.enableDragAndDrop(false);
            prop_tb._carrierGrid.enableMultiselect(true);


            
            // UISettings
            prop_tb._carrierGrid._uisettings_prefix='cat_carrier';
            prop_tb._carrierGrid._uisettings_name=prop_tb._carrierGrid._uisettings_prefix;
               prop_tb._carrierGrid._first_loading=1;
               
            // UISettings
            initGridUISettings(prop_tb._carrierGrid);

            prop_tb._carrierGrid.attachEvent("onEditCell",function(stage,rId,cInd,nValue,oValue)
            {
                let idxDelay=prop_tb._carrierGrid.getColIndexById('delay');
                if (stage==1 && cInd !== idxDelay)
                {
                    var value = prop_tb._carrierGrid.cells(rId,cInd).isChecked();
                    var selection = cat_grid.getSelectedRowId();
                    ids=selection.split(',');
                    $.each(ids, function(num, pId){
                        var vars = {"sub_action":"present","value":value,"idlist":pId};
                        addCarrierInQueue(rId, "update", cInd, vars);
                    });
                }
                return true;
            });
            
            needInitCarrier=0;
        }
    }
    function setPropertiesPanel_carrier(id){
        if (id=='carrier')
        {
            if(lastProductSelID!=undefined && lastProductSelID!="")
            {
                dhxLayout.cells('b').setText('<?php echo _l('Properties', 1).' '._l('of', 1); ?> '+getSelectedItemValueOrID(cat_grid,lastProductSelID,'name'));
            }
            hidePropTBButtons();
            prop_tb.showItem('carrier_refresh');
            prop_tb.showItem('carrier_selectall');
            prop_tb.showItem('carrier_mass_add');
            prop_tb.showItem('carrier_mass_delete');
            prop_tb.setItemText('panel', '<?php echo _l('Carriers', 1); ?>');
            prop_tb.setItemImage('panel', 'fad fa-truck');
            needInitCarrier = 1;
            initCarrier();
            propertiesPanel='carrier';
            if (lastProductSelID!=0)
            {
                displayCarrier();
            }
        }
        if (id=='carrier_refresh')
        {
            displayCarrier();
        }
        if (id=='carrier_selectall')
        {
            prop_tb._carrierGrid.selectAll();
        }
        if (id=='carrier_mass_add')
        {
            var selection = cat_grid.getSelectedRowId();
            ids=selection.split(',');
            $.each(ids, function(num, pId){
                var vars = {"sub_action":"mass_add","carriers":prop_tb._carrierGrid.getSelectedRowId(),"idlist":pId};
                addCarrierInQueue("", "update", "", vars);
            });
        }
        if (id=='carrier_mass_delete')
        {
            var selection = cat_grid.getSelectedRowId();
            ids=selection.split(',');
            $.each(ids, function(num, pId){
                var vars = {"sub_action":"mass_delete","carriers":prop_tb._carrierGrid.getSelectedRowId(),"idlist":pId};
                addCarrierInQueue("", "update", "", vars);
            });
        }
    }
    prop_tb.attachEvent("onClick", setPropertiesPanel_carrier);
    
    function displayCarrier()
    {
        prop_tb._carrierGrid.clearAll(true);
        var tempIdList = (cat_grid.getSelectedRowId()!=null?cat_grid.getSelectedRowId():"");
        $.post("index.php?ajax=1&act=cat_carrier_get&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{'idlist': tempIdList},function(data)
        {
            prop_tb._carrierGrid.parse(data);
            nb=prop_tb._carrierGrid.getRowsNum();
            prop_tb._sb.setText(nb+(nb>1?" <?php echo _l('carriers'); ?>":" <?php echo _l('carrier'); ?>"));
                prop_tb._carrierGrid._rowsNum=nb;
                
            // UISettings
                loadGridUISettings(prop_tb._carrierGrid);
                prop_tb._carrierGrid._first_loading=0;
        });
    }


    let carrier_current_id = 0;
    cat_grid.attachEvent("onRowSelect",function (idproduct){
        if (propertiesPanel=='carrier' && (cat_grid.getSelectedRowId()!==null && carrier_current_id!=idproduct)){

            displayCarrier();
            carrier_current_id=idproduct;
        }
    });


    function addCarrierInQueue(rId, action, cIn, vars)
{
    var params = {
        name: "cat_carrier_update_queue",
        row: rId,
        action: "update",
        params: {},
        callback: "callbackCarrier('"+rId+"','update','"+rId+"');"
    };
    // COLUMN VALUES
        params.params["id_lang"] = SC_ID_LANG;
        if(vars!=undefined && vars!=null && vars!="" && vars!=0)
        {
            $.each(vars, function(key, value){
                params.params[key] = value;
            });
        }        
    // USER DATA
    
    params.params = JSON.stringify(params.params);
    addInUpdateQueue(params,prop_tb._carrierGrid);
}
        
// CALLBACK FUNCTION
function callbackCarrier(sid,action,tid)
{
    if (action=='update')
    {
        prop_tb._carrierGrid.setRowTextNormal(sid);
        displayCarrier('',0);
    }
}
    
<?php } ?>