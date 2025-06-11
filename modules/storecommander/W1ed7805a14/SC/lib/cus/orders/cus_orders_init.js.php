<?php
    if (_r('GRI_CUS_PROPERTIES_GRID_ORDERS'))
    {
        ?>
    prop_tb.addListOption('panel', 'customerorder', 1, "button", '<?php echo _l('Orders and products', 1); ?>', "fa fa-shopping-cart");
    allowed_properties_panel[allowed_properties_panel.length] = "customerorder";

    prop_tb.addButton("customerorder_refresh",1000, "", "fa fa-sync green", "fa fa-sync green");
    prop_tb.setItemToolTip('customerorder_refresh','<?php echo _l('Refresh grid', 1); ?>');
    prop_tb.addButton("customerorder_gobo",1000, "", "fa fa-prestashop", "fa fa-prestashop");
    prop_tb.setItemToolTip('customerorder_gobo','<?php echo _l('View selected orders in Prestashop', 1); ?>');
    prop_tb.addButton("customerorder_selectall",1000, "", "fa fa-bolt yellow", "fad fa-bolt grey");
    prop_tb.setItemToolTip('customerorder_selectall','<?php echo _l('Select all', 1); ?>');
    prop_tb.addButton("customerorder_exportcsv",1000, "", "fad fa-file-csv green", "fad fa-file-csv green");
    prop_tb.setItemToolTip('customerorder_exportcsv','<?php echo _l('Export grid to clipboard in CSV format for MSExcel with tab delimiter.'); ?>');


    needinitCustomerOrder = 1;
    function initCustomerOrder(){
        if (needinitCustomerOrder)
        {
            prop_tb._customerOrderLayout = dhxLayout.cells('b').attachLayout('2E');
            prop_tb._customerOrderLayout.cells('a').hideHeader();
            dhxLayout.cells('b').showHeader();
            prop_tb._customerOrderGrid = prop_tb._customerOrderLayout.cells('a').attachGrid();
            prop_tb._customerOrderGrid.setImagePath("lib/js/imgs/");
            prop_tb._customerOrderGrid.enableMultiselect(true);

            // UISettings
            prop_tb._customerOrderGrid._uisettings_prefix='cus_orders';
            prop_tb._customerOrderGrid._uisettings_name=prop_tb._customerOrderGrid._uisettings_prefix;
               prop_tb._customerOrderGrid._first_loading=1;

            // UISettings
            initGridUISettings(prop_tb._customerOrderGrid);

            prop_tb._customerOrderGrid.attachEvent("onRowSelect",function (idorder){
                if (propertiesPanel=='customerorder' && !dhxLayout.cells('b').isCollapsed()){
                    displayCustomerOrderProducts();
                }
            });

            prop_tb._customerOrderLayout.cells('b').setText('<?php echo _l('Products', 1); ?>');
            prop_tb._customerProductGrid_TB = prop_tb._customerOrderLayout.cells('b').attachToolbar();
             prop_tb._customerProductGrid_TB.setIconset('awesome');
            prop_tb._customerProductGrid = prop_tb._customerOrderLayout.cells('b').attachGrid();
            prop_tb._customerProductGrid.setImagePath("lib/js/imgs/");

            prop_tb._customerProductGrid_TB.addButton("customerordersProducts_refresh", 100, "", "fa fa-sync green", "fa fa-sync green");
            prop_tb._customerProductGrid_TB.setItemToolTip('customerordersProducts_refresh','<?php echo _l('Refresh grid', 1); ?>');
            prop_tb._customerProductGrid_TB.addButton("customerordersProducts_exportcsv", 100, "", "fad fa-file-csv green", "fad fa-file-csv green");
            prop_tb._customerProductGrid_TB.setItemToolTip('customerordersProducts_exportcsv','<?php echo _l('Export grid to clipboard in CSV format for MSExcel with tab delimiter.'); ?>');
            prop_tb._customerProductGrid_TB.addButton("gotocatalog", 100, "", "fad fa-external-link green", "fad fa-external-link green");
            prop_tb._customerProductGrid_TB.setItemToolTip('gotocatalog','<?php echo _l('Go to the product in catalog.'); ?>');

            prop_tb._customerProductGrid_TB.attachEvent("onClick", setPropertiesPanel_orderOrdersProducts);

            // UISettings
            prop_tb._customerProductGrid._uisettings_prefix='cus_orders_products';
            prop_tb._customerProductGrid._uisettings_name=prop_tb._customerProductGrid._uisettings_prefix;
               prop_tb._customerProductGrid._first_loading=1;

            // UISettings
            initGridUISettings(prop_tb._customerProductGrid);

            needinitCustomerOrder=0;
        }
    }


    function setPropertiesPanel_customerorder(id){
        if (id=='customerorder')
        {
            if(lastCustomerSelID!=undefined && lastCustomerSelID!="")
            {
                idxLastname=cus_grid.getColIndexById('lastname');
                idxFirstname=cus_grid.getColIndexById('firstname');
                dhxLayout.cells('b').setText('<?php echo _l('Properties', 1).' '._l('of', 1); ?> '+cus_grid.cells(lastCustomerSelID,idxFirstname).getValue()+" "+cus_grid.cells(lastCustomerSelID,idxLastname).getValue());
            }
            hidePropTBButtons();
            prop_tb.showItem('customerorder_refresh');
            prop_tb.showItem('customerorder_gobo');
            prop_tb.showItem('customerorder_selectall');
            prop_tb.showItem('customerorder_exportcsv');
            prop_tb.setItemText('panel', '<?php echo _l('Orders and products', 1); ?>');
            prop_tb.setItemImage('panel', 'fa fa-shopping-cart');
            needinitCustomerOrder = 1;
            initCustomerOrder();
            propertiesPanel='customerorder';
            if (lastCustomerSelID!=0)
            {
                displayCustomerOrders();
            }
        }
        if (id=='customerorder_refresh')
        {
            displayCustomerOrders();
            prop_tb._customerProductGrid.clearAll(true);
        }
        if (id=='customerorder_gobo')
        {
            var sel = prop_tb._customerOrderGrid.getSelectedRowId();

            if (sel)
            {
                var tabId=sel.split(',');

                tabId.forEach(function(id_order) {
                    if (mustOpenBrowserTab){
                        window.open("<?php echo SC_PS_PATH_ADMIN_REL; ?>index.php?tab=AdminOrders&vieworder&id_order="+id_order+"&token=<?php echo $sc_agent->getPSToken('AdminOrders'); ?>");
                    }else{
                    <?php if (version_compare(_PS_VERSION_, '1.6.0.0', '>=')) { ?>
                        wViewOrderOnPS = dhxWins.createWindow(i+"wViewOrderOnPS"+new Date().getTime(), 50+i*40, 50+i*40, 1250, $(window).height()-75);
                    <?php }
        else
        { ?>
                        wViewOrderOnPS = dhxWins.createWindow(i+"wViewOrderOnPS"+new Date().getTime(), 50+i*40, 50+i*40, 1000, $(window).height()-75);
                    <?php } ?>
                        wViewOrderOnPS.setText('<?php echo _l('Order', 1); ?> '+id_order);
                        wViewOrderOnPS.attachURL("<?php echo SC_PS_PATH_ADMIN_REL; ?>index.php?tab=AdminOrders&vieworder&id_order="+id_order+"&token=<?php echo $sc_agent->getPSToken('AdminOrders'); ?>");
                    }
                });
                pushOneUsage('orders_init-bo-link-adminorders_vieworder','cus');
            }

        }
        if (id=='customerorder_selectall'){
            prop_tb._customerOrderGrid.selectAll();
            displayCustomerOrderProducts();
            prop_tb._customerProductGrid.clearAll(true);
        }
        if (id=='customerorder_exportcsv'){
            displayQuickExportWindow(prop_tb._customerOrderGrid,1);
        }
    }
    prop_tb.attachEvent("onClick", setPropertiesPanel_customerorder);

    function setPropertiesPanel_orderOrdersProducts(id){
        if (id=='customerordersProducts_refresh')
        {
            displayCustomerOrderProducts();
            prop_tb._customerProductGrid.clearAll(true);
        }
        if (id=='customerordersProducts_exportcsv'){
             displayQuickExportWindow(prop_tb._customerProductGrid,1);
        }
        if(id=='gotocatalog')
        {
            selection=prop_tb._customerProductGrid.getSelectedRowId();
            if (selection!='' && selection!=null)
            {
                var rowIds = selection.split(",");
                var rowId = rowIds[0];

                var open_cat_grid_ids  = prop_tb._customerProductGrid.getUserData(rowId, "open_cat_grid");
                if (open_cat_grid_ids!='' && open_cat_grid_ids!=null)
                {
                    var url = "?page=cat_tree&open_cat_grid="+open_cat_grid_ids;
                    window.open(url,'_blank');
                }
            }
        }
    }


    function displayCustomerOrders()
    {
        var customers_id = "";
        let idxIdAddress=cus_grid.getColIndexById('id_address');
        if(gridView!="grid_address" && idxIdAddress==undefined) {
            customers_id = cus_grid.getSelectedRowId();
        } else {
            idxIdCustomer=cus_grid.getColIndexById('id_customer');
            $.each( cus_grid.getSelectedRowId().split(','), function( num, rowid ) {
                if(customers_id!="")
                    customers_id = customers_id+",";

                customers_id = customers_id+cus_grid.cells(rowid,idxIdCustomer).getValue();
            });
        }
        prop_tb._customerOrderGrid.clearAll(true);
        prop_tb._customerOrderGrid.load("index.php?ajax=1&act=cus_orders_get&id_customer="+customers_id+"&id_lang="+SC_ID_LANG,function()
        {
            nb=prop_tb._customerOrderGrid.getRowsNum();
            prop_tb._sb.setText('');

            // UISettings
            loadGridUISettings(prop_tb._customerOrderGrid);

            // UISettings
            prop_tb._customerOrderGrid._first_loading=0;
        });
    }

    function displayCustomerOrderProducts()
    {
        prop_tb._customerProductGrid.clearAll(true);
        prop_tb._customerProductGrid.load("index.php?ajax=1&act=cus_orders_products_get&id_order="+prop_tb._customerOrderGrid.getSelectedRowId()+"&id_lang="+SC_ID_LANG,function()
        {
            nb=prop_tb._customerProductGrid.getRowsNum();
            prop_tb._sb.setText('');

            // UISettings
            loadGridUISettings(prop_tb._customerProductGrid);

            // UISettings
            prop_tb._customerProductGrid._first_loading=0;
        });
    }



    let customerorder_current_id = 0;
    cus_grid.attachEvent("onRowSelect",function (idcustomer){
        if (propertiesPanel=='customerorder' && !dhxLayout.cells('b').isCollapsed() && (cus_grid.getSelectedRowId()!==null && customerorder_current_id!=idcustomer)){
            displayCustomerOrders();
            displayCustomerOrderProducts();
            customerorder_current_id=idcustomer;
        }
    });

<?php
    } // end permission
?>
