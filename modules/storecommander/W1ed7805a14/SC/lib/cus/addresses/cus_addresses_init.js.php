<?php
    if (_r('GRI_CUS_PROPERTIES_GRID_ADDRESS'))
    {
        ?>
    prop_tb.addListOption('panel', 'customeraddress', 2, "button", '<?php echo _l('Addresses', 1); ?>', "fad fa-map-marked-alt");
    allowed_properties_panel[allowed_properties_panel.length] = "customeraddress";
    prop_tb.addButton("customeraddress_refresh",1000, "", "fa fa-sync green", "fa fa-sync green");
    prop_tb.setItemToolTip('customeraddress_refresh','<?php echo _l('Refresh grid', 1); ?>');
    prop_tb.addButton("customeraddress_addresses_add_form",1000, "", "fa fa-plus-circle green", "fa fa-plus-circle green");
    prop_tb.setItemToolTip('customeraddress_addresses_add_form','<?php echo _l('Create new address'); ?>');
    prop_tb.addButton("customeraddress_addresses_add_ps",1000, "", "fa fa-prestashop", "fa fa-prestashop");
    prop_tb.setItemToolTip('customeraddress_addresses_add_ps','<?php echo _l('Create new address with the PrestaShop form'); ?>');


    needinitCustomerAddress = 1;
    function initCustomerAddress(){
        if (needinitCustomerAddress)
        {
            prop_tb._customerAddressLayout = dhxLayout.cells('b').attachLayout('1C');
            prop_tb._customerAddressLayout.cells('a').hideHeader();
            dhxLayout.cells('b').showHeader();
            prop_tb._customerAddressGrid = prop_tb._customerAddressLayout.cells('a').attachGrid();
            prop_tb._customerAddressGrid.setImagePath("lib/js/imgs/");

            // UISettings
            prop_tb._customerAddressGrid._uisettings_prefix='cus_addresses';
            prop_tb._customerAddressGrid._uisettings_name=prop_tb._customerAddressGrid._uisettings_prefix;
               prop_tb._customerAddressGrid._first_loading=1;

            // UISettings
            initGridUISettings(prop_tb._customerAddressGrid);

            customerAddressDataProcessorURLBase="index.php?ajax=1&act=cus_addresses_update&id_lang="+SC_ID_LANG;
            customerAddressDataProcessor = new dataProcessor(customerAddressDataProcessorURLBase);
            customerAddressDataProcessor.enableDataNames(true);
            customerAddressDataProcessor.enablePartialDataSend(true);
            customerAddressDataProcessor.setTransactionMode("POST");
            customerAddressDataProcessor.setUpdateMode('cell',true);
            customerAddressDataProcessor.serverProcessor=customerAddressDataProcessorURLBase;
            customerAddressDataProcessor.init(prop_tb._customerAddressGrid);

            needinitCustomerAddress=0;
        }
    }


    function setPropertiesPanel_customeraddress(id){
        if (id=='customeraddress')
        {
            if(lastCustomerSelID!=undefined && lastCustomerSelID!="")
            {
                idxLastname=cus_grid.getColIndexById('lastname');
                idxFirstname=cus_grid.getColIndexById('firstname');
                dhxLayout.cells('b').setText('<?php echo _l('Properties', 1).' '._l('of', 1); ?> '+cus_grid.cells(lastCustomerSelID,idxFirstname).getValue()+" "+cus_grid.cells(lastCustomerSelID,idxLastname).getValue());
            }
            hidePropTBButtons();
            prop_tb.showItem('customeraddress_refresh');
            prop_tb.showItem('customeraddress_addresses_add_form');
            prop_tb.showItem('customeraddress_addresses_add_ps');
            prop_tb.setItemText('panel', '<?php echo _l('Addresses', 1); ?>');
            prop_tb.setItemImage('panel', 'fad fa-map-marked-alt');
            needinitCustomerAddress = 1;
            initCustomerAddress();
            propertiesPanel='customeraddress';
            if (lastCustomerSelID!=0)
            {
                displayCustomerAddresses();
            }
        }
        if (id=='customeraddress_refresh')
        {
            displayCustomerAddresses();
        }
        if(id=="customeraddress_addresses_add_ps")
        {
            let customer_ids = cus_grid.getSelectedRowId();
            if(customer_ids !== null && customer_ids !== '') {
                let customer_ids_array = customer_ids.split(',');
                if(customer_ids_array.length > 1) {
                    alert("<?php echo _l('Please select only one customer', 1); ?>");
                } else {
                    if (!dhxWins.isWindow("wNewAddress"))
                    {
                        <?php if (version_compare(_PS_VERSION_, '1.7.0.0', '>=')) { ?>
                        wNewAddress = dhxWins.createWindow("wNewAddress", 50, 50, 1460, $(window).height()-75);
                        <?php }
        else
        { ?>
                        wNewAddress = dhxWins.createWindow("wNewAddress", 50, 50, 1000, $(window).height()-75);
                        <?php } ?>
                        wNewAddress.setText('<?php echo _l('Create the new address and close this window to refresh the grid', 1); ?>');
                        wNewAddress.attachURL("<?php echo SC_PS_PATH_ADMIN_REL; ?>index.php?<?php echo version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? 'controller=AdminAddresses' : 'tab=AdminAddresses'; ?>&addaddress=1&token=<?php echo $sc_agent->getPSToken('AdminAddresses'); ?>&id_customer="+customer_ids);

                        pushOneUsage('addresses_init-bo-link-adminaddresses_addaddress','cus');
                        wNewAddress.attachEvent("onClose", function(win){
                            displayCustomerAddresses();
                            return true;
                        });
                    }
                }
            } else {
                alert("<?php echo _l('Please select a customer first', 1); ?>");
            }
        }
        if(id=='customeraddress_addresses_add_form')
        {
            if (!dhxWins.isWindow("wCreateNewAddress"))
            {
                wCreateNewAddress = dhxWins.createWindow("wCreateNewAddress", 120, 50, 370, 580);
                wCreateNewAddress.denyPark();
                wCreateNewAddress.denyResize();
                wCreateNewAddress.setText('<?php echo _l('Quick address creation', 1); ?>');
                $.get("index.php?ajax=1&act=ord_win-makeorder_address_create_form&id_lang="+SC_ID_LANG,{id_customer:lastCustomerSelID,from_cus_prop:1},function(data){
                    $('#jsExecute').html(data);
                });
            }
        }

    }
    prop_tb.attachEvent("onClick", setPropertiesPanel_customeraddress);


    function displayCustomerAddresses()
    {
        var customers_id = "";

        idxIdAddress=cus_grid.getColIndexById('id_address');
        if(idxIdAddress==undefined)
            customers_id = cus_grid.getSelectedRowId();
        else
        {
            idxIdCustomer=cus_grid.getColIndexById('id_customer');
            $.each( cus_grid.getSelectedRowId().split(','), function( num, rowid ) {
                if(customers_id!="")
                    customers_id = customers_id+",";

                customers_id = customers_id+cus_grid.cells(rowid,idxIdCustomer).getValue();
            });
        }
        prop_tb._customerAddressGrid.clearAll(true);
        $.post("index.php?ajax=1&act=cus_addresses_get&id_lang="+SC_ID_LANG,{'id_customer': customers_id},function(data)
        {
            prop_tb._customerAddressGrid.parse(data);
            nb=prop_tb._customerAddressGrid.getRowsNum();
            prop_tb._sb.setText('');

        // UISettings
            loadGridUISettings(prop_tb._customerAddressGrid);

            // UISettings
            prop_tb._customerAddressGrid._first_loading=0;
                });
    }



    let customeraddress_current_id = 0;
        cus_grid.attachEvent("onRowSelect",function (idcustomer){
        if (propertiesPanel=='customeraddress' && !dhxLayout.cells('b').isCollapsed() && (cus_grid.getSelectedRowId()!==null && customeraddress_current_id!=idcustomer)){
            displayCustomerAddresses();
            if (dhxWins.isWindow("wCreateNewAddress"))
            {
                $.get("index.php?ajax=1&act=ord_win-makeorder_address_create_form&id_lang="+SC_ID_LANG,{id_customer:lastCustomerSelID,from_cus_prop:1},function(data){
                    $('#jsExecute').html(data);
                });
            }
            customeraddress_current_id=idcustomer;
        }
    });

<?php
    } // end permission
?>
