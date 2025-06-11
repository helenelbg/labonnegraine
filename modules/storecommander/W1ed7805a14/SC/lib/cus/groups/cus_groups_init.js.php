<?php
    if (
        _r('GRI_CUS_PROPERTIES_GRID_GROUPS')
        &&
            (
                (version_compare(_PS_VERSION_, '1.6.0.0', '>=') && SCI::getConfigurationValue('PS_GROUP_FEATURE_ACTIVE') > 0)
                ||
                (version_compare(_PS_VERSION_, '1.6.0.0', '<'))
            )
    ) {
        ?>
    prop_tb.addListOption('panel', 'customergroup', 4, "button", '<?php echo _l('Groups', 1); ?>', "fa fa-user-friends");
    allowed_properties_panel[allowed_properties_panel.length] = "customergroup";

    prop_tb.addButton("customergroup_refresh",1000, "", "fa fa-sync green", "fa fa-sync green");
    prop_tb.setItemToolTip('customergroup_refresh','<?php echo _l('Refresh grid', 1); ?>');
    prop_tb.addButton("customergroup_add_select",1000, "", "fad fa-link yellow", "fad fa-link yellow");
    prop_tb.setItemToolTip('customergroup_add_select','<?php echo _l('Add all selected customers to all selected groups', 1); ?>');
    prop_tb.addButton("customergroup_del_select",1000, "", "fad fa-unlink red", "fad fa-unlink red");
    prop_tb.setItemToolTip('customergroup_del_select','<?php echo _l('Remove all selected customers from all selected groups', 1); ?>');
    


    needinitCustomerGroup = 1;
    function initCustomerGroup(){
        if (needinitCustomerGroup)
        {
            prop_tb._customerGroupLayout = dhxLayout.cells('b').attachLayout('1C');
            prop_tb._customerGroupLayout.cells('a').hideHeader();
            dhxLayout.cells('b').showHeader();
            prop_tb._customerGroupGrid = prop_tb._customerGroupLayout.cells('a').attachGrid();
            prop_tb._customerGroupGrid.setImagePath("lib/js/imgs/");
            prop_tb._customerGroupGrid.enableMultiselect(true);
            
            // UISettings
            prop_tb._customerGroupGrid._uisettings_prefix='cus_groups';
            prop_tb._customerGroupGrid._uisettings_name=prop_tb._customerGroupGrid._uisettings_prefix;
               prop_tb._customerGroupGrid._first_loading=1;
               
            // UISettings
            initGridUISettings(prop_tb._customerGroupGrid);
            
            prop_tb._customerGroupGrid.attachEvent("onEditCell",function(stage,rId,cInd,nValue,oValue)
            {
                if(stage==1)
                {
                    idxPresent=prop_tb._customerGroupGrid.getColIndexById('present');
                    idxDefault=prop_tb._customerGroupGrid.getColIndexById('is_default');
                    prop_tb._customerGroupGrid.forEachRow(function(id){
                        if(id!=rId)
                            prop_tb._customerGroupGrid.cells(id,idxDefault).setValue(0);
                   });
                
                    var action = "";
                    if(cInd==idxPresent)
                        action = "present";
                    else if(cInd==idxDefault)
                        action = "default";
                    
                    if(action!="")
                    {
                        var customers_id = "";
                        let idxIdAddress=cus_grid.getColIndexById('id_address');
                        if(gridView!="grid_address" && idxIdAddress==undefined) {
                            customers_id = cus_grid.getSelectedRowId();
                        } else {
                            idxIdCustomer=cus_grid.getColIndexById('id_customer');
                            $.each( cus_grid.getSelectedRowId().split(','), function( num, rowid ) {
                                if(customers_id!="") {
                                    customers_id = customers_id+",";
                                }
                                customers_id = customers_id+cus_grid.cells(rowid,idxIdCustomer).getValue();
                            });
                        }
                    
                        var value = prop_tb._customerGroupGrid.cells(rId,cInd).isChecked();
                        $.post("index.php?ajax=1&act=cus_groups_update&action="+action+"&value="+value+"&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{"idlist":customers_id,"id_group":rId},function(data){
                            displayCustomerGroups();
                        });
                    }
                }
                return true;
            });
            
            needinitCustomerGroup=0;
        }
    }


    function setPropertiesPanel_customergroup(id){
        if (id=='customergroup')
        {
            if(lastCustomerSelID!=undefined && lastCustomerSelID!="")
            {
                idxLastname=cus_grid.getColIndexById('lastname');
                idxFirstname=cus_grid.getColIndexById('firstname');
                dhxLayout.cells('b').setText('<?php echo _l('Properties', 1).' '._l('of', 1); ?> '+cus_grid.cells(lastCustomerSelID,idxFirstname).getValue()+" "+cus_grid.cells(lastCustomerSelID,idxLastname).getValue());
            }
            hidePropTBButtons();
            prop_tb.showItem('customergroup_refresh');
            prop_tb.showItem('customergroup_add_select');
            prop_tb.showItem('customergroup_del_select');
            prop_tb.setItemText('panel', '<?php echo _l('Groups', 1); ?>');
            prop_tb.setItemImage('panel', 'fa fa-user-friends');
            needinitCustomerGroup = 1;
            initCustomerGroup();
            propertiesPanel='customergroup';
            if (lastCustomerSelID!=0)
            {
                displayCustomerGroups();
            }
        }
        if (id=='customergroup_refresh')
        {
            displayCustomerGroups();
        }
        if (id=='customergroup_add_select')
        {
            if(prop_tb._customerGroupGrid.getSelectedRowId()!="" && prop_tb._customerGroupGrid.getSelectedRowId()!=null)
            {
                var customers_id = "";
                let idxIdAddress=cus_grid.getColIndexById('id_address');
                if(gridView!="grid_address" && idxIdAddress==undefined) {
                    customers_id = cus_grid.getSelectedRowId();
                } else {
                    idxIdCustomer=cus_grid.getColIndexById('id_customer');
                    $.each( cus_grid.getSelectedRowId().split(','), function( num, rowid ) {
                        if(customers_id!="") {
                            customers_id = customers_id+",";
                        }
                        customers_id = customers_id+cus_grid.cells(rowid,idxIdCustomer).getValue();
                    });
                }
                $.post("index.php?ajax=1&act=cus_groups_update&action=mass_present&value=1&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{"id_group":prop_tb._customerGroupGrid.getSelectedRowId(),"idlist":customers_id},function(data){
                    displayCustomerGroups();
                });
            }
        }
        if (id=='customergroup_del_select')
        {
            if(prop_tb._customerGroupGrid.getSelectedRowId()!="" && prop_tb._customerGroupGrid.getSelectedRowId()!=null)
            {
                var customers_id = "";
                let idxIdAddress=cus_grid.getColIndexById('id_address');
                if(gridView!="grid_address" && idxIdAddress==undefined) {
                    customers_id = cus_grid.getSelectedRowId();
                } else {
                    idxIdCustomer=cus_grid.getColIndexById('id_customer');
                    $.each( cus_grid.getSelectedRowId().split(','), function( num, rowid ) {
                        if(customers_id!="") {
                            customers_id = customers_id+",";
                        }
                        customers_id = customers_id+cus_grid.cells(rowid,idxIdCustomer).getValue();
                    });
                }
                $.post("index.php?ajax=1&act=cus_groups_update&action=mass_present&value=0&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{"id_group":prop_tb._customerGroupGrid.getSelectedRowId(),"idlist":customers_id},function(data){
                    displayCustomerGroups();
                });
            }
        }
        

    }
    prop_tb.attachEvent("onClick", setPropertiesPanel_customergroup);


    function displayCustomerGroups()
    {
        var customers_id = "";
        let idxIdAddress=cus_grid.getColIndexById('id_address');
        if(gridView!="grid_address" && idxIdAddress==undefined) {
            customers_id = cus_grid.getSelectedRowId();
        } else {
            idxIdCustomer=cus_grid.getColIndexById('id_customer');
            $.each( cus_grid.getSelectedRowId().split(','), function( num, rowid ) {
                if(customers_id!="") {
                    customers_id = customers_id+",";
                }
                customers_id = customers_id+cus_grid.cells(rowid,idxIdCustomer).getValue();
            });
        }
        prop_tb._customerGroupGrid.clearAll(true);
        $.post("index.php?ajax=1&act=cus_groups_get&id_lang="+SC_ID_LANG,{'id_customer': customers_id},function(data)
        {
             prop_tb._customerGroupGrid.parse(data);
            nb=prop_tb._customerGroupGrid.getRowsNum();
            prop_tb._sb.setText('');

            // UISettings
            loadGridUISettings(prop_tb._customerGroupGrid);

            // UISettings
            prop_tb._customerGroupGrid._first_loading=0;
        });
    }
    


    let customergroup_current_id = 0;
    cus_grid.attachEvent("onRowSelect",function (idcustomer){
        if (propertiesPanel=='customergroup' && !dhxLayout.cells('b').isCollapsed() && (cus_grid.getSelectedRowId()!==null && customergroup_current_id!=idcustomer)){
            displayCustomerGroups();
            customergroup_current_id=idcustomer;
        }
    });

<?php
    } // end permission
?>