<?php
if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) { ?>

    <?php if (_r('GRI_CAT_PROPERTIES_GRID_SUPPLIER')) { ?>
    prop_tb.addListOption('panel', 'supplier', 14, "button", '<?php echo _l('Suppliers', 1); ?>', "fad fa-parachute-box");
    allowed_properties_panel[allowed_properties_panel.length] = "supplier";
    <?php } ?>

    prop_tb.addButton("supplier_refresh",1000, "", "fa fa-sync green", "fa fa-sync green");
    prop_tb.setItemToolTip('supplier_refresh','<?php echo _l('Refresh grid', 1); ?>');
    prop_tb.addButton("supplier_selectall",1000, "", "fa fa-bolt yellow", "fad fa-bolt grey");
    prop_tb.setItemToolTip('supplier_selectall','<?php echo _l('Select all', 1); ?>');
    prop_tb.addButton("supplier_add_select",1000, "", "fad fa-link yellow", "fad fa-link yellow");
    prop_tb.setItemToolTip('supplier_add_select','<?php echo _l('Add all selected products to all selected suppliers', 1); ?>');
    prop_tb.addButton("supplier_del_select",1000, "", "fad fa-unlink red", "fad fa-unlink red");
    prop_tb.setItemToolTip('supplier_del_select','<?php echo _l('Remove all selected products from all selected suppliers', 1); ?>');
    getTbSettingsButton(prop_tb, {'grideditor':1,'settings':0}, 'prop_supplier_',1000);


    needInitSupplier = 1;
    function initSupplier()
    {
        if (needInitSupplier)
        {
            prop_tb._supplierLayout = dhxLayout.cells('b').attachLayout('1C');
            prop_tb._supplierLayout.cells('a').hideHeader();
            dhxLayout.cells('b').showHeader();

            prop_tb._supplierGrid = prop_tb._supplierLayout.cells('a').attachGrid();
            prop_tb._supplierGrid._name='_supplierGrid';
            prop_tb._supplierGrid.setImagePath("lib/js/imgs/");
              prop_tb._supplierGrid.enableDragAndDrop(false);
            prop_tb._supplierGrid.enableMultiselect(true);
            
            // UISettings
            prop_tb._supplierGrid._uisettings_prefix='cat_supplier';
            prop_tb._supplierGrid._uisettings_name=prop_tb._supplierGrid._uisettings_prefix;
            prop_tb._supplierGrid._uisettings_limited=true;
               prop_tb._supplierGrid._first_loading=1;
               
            // UISettings
            initGridUISettings(prop_tb._supplierGrid);
            
            prop_tb._supplierGrid.attachEvent("onEditCell",function(stage,rId,cInd,nValue,oValue)
            {
                <?php sc_ext::readCustomPropSupplierGridConfigXML('onEditCell'); ?>
                if(stage==1)
                {
                    idxPresent=prop_tb._supplierGrid.getColIndexById('present');
                    idxDefault=prop_tb._supplierGrid.getColIndexById('default');                
                    var action = "";
                    if(cInd==idxPresent)
                    {
                        action = "present";
                        var is_default = prop_tb._supplierGrid.cells(rId,idxDefault).isChecked();
                        var value = prop_tb._supplierGrid.cells(rId,cInd).isChecked();
                        if(is_default && value==0)
                        {
                            var nb_present = 1;
                            prop_tb._supplierGrid.forEachRow(function(id){
                                var is_present = prop_tb._supplierGrid.cells(id,idxPresent).isChecked();
                                if(is_present==1)
                                    nb_present = nb_present*1 + 1;
                            });
                            if(nb_present>1)
                            {
                                prop_tb._supplierGrid.cells(rId,idxPresent).setValue(1);
                                return false;
                            }
                        }
                    }
                    else if(cInd==idxDefault)
                        action = "default";
                    
                    if(action=="present" || action=="default")
                    {
                        <?php sc_ext::readCustomPropSupplierGridConfigXML('onBeforeUpdate'); ?>
                        var value = prop_tb._supplierGrid.cells(rId,cInd).isChecked();
                        $.post("index.php?ajax=1&act=cat_supplier_update&id_supplier="+rId+"&action="+action+"&value="+value+"&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{"idlist":cat_grid.getSelectedRowId()},function(data){
                            displaySupplier();
                            <?php sc_ext::readCustomPropSupplierGridConfigXML('onAfterUpdate'); ?>
                        });
                    }
                    
                }
                else if(stage==2)
                {
                    let field = prop_tb._supplierGrid.getColumnId(cInd);
                    if(['id','name','present','default'].includes(field)){
                       return false;
                    }
                    let action = "fields";
                    if(field!=undefined && field!=null && field!="")
                    {
                        <?php sc_ext::readCustomPropSupplierGridConfigXML('onBeforeUpdate'); ?>
                        var value = prop_tb._supplierGrid.cells(rId,cInd).getValue();
                        $.post("index.php?ajax=1&act=cat_supplier_update&id_supplier="+rId+"&action="+action+"&field="+field+"&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{"value":value,"idlist":cat_grid.getSelectedRowId()},function(data){
                            <?php sc_ext::readCustomPropSupplierGridConfigXML('onAfterUpdate'); ?>
                        });
                    }
                }
                return true;
            });
                        
            needInitSupplier=0;
        }
    }

    function setPropertiesPanel_supplier(id){
        if (id=='supplier')
        {
            if(lastProductSelID!=undefined && lastProductSelID!="")
            {
                dhxLayout.cells('b').setText('<?php echo _l('Properties', 1).' '._l('of', 1); ?> '+getSelectedItemValueOrID(cat_grid,lastProductSelID,'name'));
            }
            hidePropTBButtons();
            prop_tb.showItem('supplier_refresh');
            prop_tb.showItem('supplier_selectall');
            prop_tb.showItem('supplier_add_select');
            prop_tb.showItem('supplier_del_select');
            prop_tb.showItem('prop_supplier_settings_menu');
            prop_tb.setItemText('panel', '<?php echo _l('Suppliers', 1); ?>');
            prop_tb.setItemImage('panel', 'fad fa-parachute-box');
            needInitSupplier = 1;
            initSupplier();
            propertiesPanel='supplier';
            if (lastProductSelID!=0)
            {
                displaySupplier();
            }
        }
        if (id=='supplier_add_select')
        {
            if(prop_tb._supplierGrid.getSelectedRowId()!="" && prop_tb._supplierGrid.getSelectedRowId()!=null)
            {
                $.post("index.php?ajax=1&act=cat_supplier_update&action=mass_present&value=true&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{"idlist":cat_grid.getSelectedRowId(), "id_supplier":prop_tb._supplierGrid.getSelectedRowId()},function(data){
                    displaySupplier();                    
                });
            }
        }
        if (id=='supplier_del_select')
        {
            if(prop_tb._supplierGrid.getSelectedRowId()!="" && prop_tb._supplierGrid.getSelectedRowId()!=null)
            {
                $.post("index.php?ajax=1&act=cat_supplier_update&action=mass_present&value=false&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{"idlist":cat_grid.getSelectedRowId(), "id_supplier":prop_tb._supplierGrid.getSelectedRowId()},function(data){
                    displaySupplier();
                });
            }
        }
        if (id=='supplier_refresh')
        {
            displaySupplier();
        }
        if (id=='prop_supplier_grideditor'){
            openWinGridEditor('type_propsupplier');
        }
        if (id=='supplier_selectall')
        {
            prop_tb._supplierGrid.selectAll();
        }
    }
    prop_tb.attachEvent("onClick", setPropertiesPanel_supplier);
    
    function displaySupplier()
    {
        prop_tb._supplierGrid.clearAll(true);
        var tempIdList = (cat_grid.getSelectedRowId()!=null?cat_grid.getSelectedRowId():"");
        $.post("index.php?ajax=1&act=cat_supplier_get&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{'idlist': tempIdList},function(data) //,'with_combi':with_combi
        {
            prop_tb._supplierGrid.parse(data);
            nb=prop_tb._supplierGrid.getRowsNum();
            prop_tb._sb.setText(nb+(nb>1?" <?php echo _l('suppliers'); ?>":" <?php echo _l('supplier'); ?>"));
            prop_tb._supplierGrid._rowsNum=nb;
            
               // UISettings
            loadGridUISettings(prop_tb._supplierGrid);
            prop_tb._supplierGrid._first_loading=0;
            
            <?php sc_ext::readCustomPropSupplierGridConfigXML('afterGetRows'); ?>
        });
    }


    let supplier_current_id = 0;
    cat_grid.attachEvent("onRowSelect",function (idproduct){
        if (propertiesPanel=='supplier' && (cat_grid.getSelectedRowId()!==null && supplier_current_id!=idproduct)){
            displaySupplier();
            supplier_current_id=idproduct;
        }
    });
<?php } ?>