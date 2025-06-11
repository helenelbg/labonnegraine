<?php
 if (_r('GRI_CAT_PROPERTIES_GRID_CUSTOMERBYPRODUCT')) { ?>
        prop_tb.addListOption('panel', 'customerbyproduct', 16, "button", '<?php echo _l('Customers', 1); ?>', "fa fa-user");
        allowed_properties_panel[allowed_properties_panel.length] = "customerbyproduct";


    prop_tb.addButton("customerbyproduct_refresh",1000, "", "fa fa-sync green", "fa fa-sync green");
    prop_tb.setItemToolTip('customerbyproduct_refresh','<?php echo _l('Refresh grid', 1); ?>');
    prop_tb.addButton("customerbyproduct_view_customer_ps",1000, "", "fa fa-prestashop", "fa fa-prestashop");
    prop_tb.setItemToolTip('customerbyproduct_view_customer_ps','<?php echo _l('View selected customers in Prestashop', 1); ?>');
    prop_tb.addButton("customerbyproduct_view_order",1000, "", "fa fa-shopping-cart", "fa fa-shopping-cart");
    prop_tb.setItemToolTip('customerbyproduct_view_order','<?php echo _l('View selected orders in StoreCommander', 1); ?>');
    prop_tb.addButton("customerbyproduct_exportcsv",1000, "", "fad fa-file-csv green", "fad fa-file-csv green");
    prop_tb.setItemToolTip('customerbyproduct_exportcsv','<?php echo _l('Export grid to clipboard in CSV format for MSExcel with tab delimiter.', 1); ?>');
    getTbSettingsButton(prop_tb, {'grideditor':1,'settings':0}, 'prop_customerbyproduct_',1000);
    customerbyproductFilter=0;


    needInitCustomerByProduct = 1;
    function initCustomerByProduct(){
        if (needInitCustomerByProduct)
        {
            prop_tb._customerByProductLayout = dhxLayout.cells('b').attachLayout('1C');
            prop_tb._customerByProductLayout.cells('a').hideHeader();
            dhxLayout.cells('b').showHeader();
            prop_tb._customerByProductGrid = prop_tb._customerByProductLayout.cells('a').attachGrid();
            prop_tb._customerByProductGrid._name='_customerByProductGrid';
            prop_tb._customerByProductGrid.setImagePath("lib/js/imgs/");
            prop_tb._customerByProductGrid.enableMultiselect(true);
            prop_tb._customerByProductGrid.attachEvent("onRowSelect",doOnRowSelected);

             prop_tb._customerByProductGrid.attachEvent("onFilterEnd", function(elements){
                getCusGridStat();
             });
             prop_tb._customerByProductGrid.attachEvent("onSelectStateChanged", function(id){
                 getCusGridStat();
             });

            // UISettings
            prop_tb._customerByProductGrid._uisettings_prefix='cat_customerbyproduct';
            prop_tb._customerByProductGrid._uisettings_name=prop_tb._customerByProductGrid._uisettings_prefix;
               prop_tb._customerByProductGrid._first_loading=1;

            // UISettings
            initGridUISettings(prop_tb._customerByProductGrid);

            customerbyproductFilter=0;
            needInitCustomerByProduct=0;
        }
    }

    function doOnRowSelected() {
        getCusGridStat();
    }

    function setPropertiesPanel_customerbyproduct(id){
        if (id=='customerbyproduct')
        {
            if(lastProductSelID!=undefined && lastProductSelID!="")
            {
                dhxLayout.cells('b').setText('<?php echo _l('Properties', 1).' '._l('of', 1); ?> '+getSelectedItemValueOrID(cat_grid,lastProductSelID,'name'));
            }
            hidePropTBButtons();
            prop_tb.showItem('customerbyproduct_view_customer_ps');
            prop_tb.showItem('customerbyproduct_view_order_ps');
            prop_tb.showItem('customerbyproduct_view_order');
            prop_tb.showItem('customerbyproduct_exportcsv');
            prop_tb.showItem('customerbyproduct_refresh');
            prop_tb.showItem('prop_customerbyproduct_settings_menu');
            prop_tb.setItemText('panel', '<?php echo _l('Customers', 1); ?>');
            prop_tb.setItemImage('panel', 'fa fa-user');
            needInitCustomerByProduct=1;
            initCustomerByProduct();
            propertiesPanel='customerbyproduct';
            if (lastProductSelID!=0)
            {
                displayCustomers();
            }
        }
        if (id=='customerbyproduct_exportcsv')
        {
            displayQuickExportWindow(prop_tb._customerByProductGrid, 1);
        }
         if (id=='customerbyproduct_view_order') {
            var sel=prop_tb._customerByProductGrid.getSelectedRowId();
            if (sel)
            {
                var tabId=sel.split(',');
                for (var i=0;i<tabId.length;i++)
                {
                    idxIdOrder=prop_tb._customerByProductGrid.getColIndexById('id_order');
                    id_order=prop_tb._customerByProductGrid.cells(tabId[i],idxIdOrder).getValue();
                    if (id_order!='' && id_order!=null)
                    {
                        var url = "?page=ord_tree&open_ord="+id_order;
                        window.open(url,'_blank');
                    }
                }
            }
        }
        if (id=='customerbyproduct_view_customer_ps'){
            var sel=prop_tb._customerByProductGrid.getSelectedRowId();
            if (sel)
            {
                var tabId=sel.split(',');
                for (var i=0;i<tabId.length;i++)
                {
                    idxIdCustomer=prop_tb._customerByProductGrid.getColIndexById('id_customer');
                    id_customer=prop_tb._customerByProductGrid.cells(tabId[i],idxIdCustomer).getValue();
                    if (mustOpenBrowserTab){
                        window.open("<?php echo SC_PS_PATH_ADMIN_REL; ?>index.php?tab=AdminCustomers&viewcustomer&id_customer="+id_customer+"&token=<?php echo $sc_agent->getPSToken('AdminCustomers'); ?>");
                    }else{
                         <?php if (version_compare(_PS_VERSION_, '1.6.0.0', '>=')) { ?>
                             wViewCustomer = dhxWins.createWindow(i+"wViewCustomer"+new Date().getTime(), 50+i*40, 50+i*40, 1250, $(window).height()-75);
                         <?php }
 else
 { ?>
                            wViewCustomer = dhxWins.createWindow(i+"wViewCustomer"+new Date().getTime(), 50+i*40, 50+i*40, 1000, $(window).height()-75);
                         <?php } ?>
                        wViewCustomer.setText('<?php echo _l('Customer', 1); ?> '+id_customer);
                        wViewCustomer.attachURL("<?php echo SC_PS_PATH_ADMIN_REL; ?>index.php?tab=AdminCustomers&viewcustomer&id_customer="+id_customer+"&token=<?php echo $sc_agent->getPSToken('AdminCustomers'); ?>");
                    }
                }
                pushOneUsage('customerbyproduct_init-bo-link-admincustomers_viewcustomer','cat');
            }
        }
        if (id=='customerbyproduct_refresh')
        {
            displayCustomers();
        }
        if (id=='prop_customerbyproduct_grideditor')
        {
            openWinGridEditor('type_propcustomers');
        }
    }
    prop_tb.attachEvent("onClick", setPropertiesPanel_customerbyproduct);

    function displayCustomers()
    {
        prop_tb._customerByProductGrid.clearAll(true);
        prop_tb._customerByProductGrid.load("index.php?ajax=1&act=cat_customerbyproduct_get&ids="+cat_grid.getSelectedRowId()+"&id_lang="+SC_ID_LANG,function()
                {
                    prop_tb._customerByProductGrid._rowsNum=prop_tb._customerByProductGrid.getRowsNum();
                    getCusGridStat();

                    // UISettings
                    loadGridUISettings(prop_tb._customerByProductGrid);

                    // UISettings
                    prop_tb._customerByProductGrid._first_loading=0;
                });
    }

    function getCusGridStat(){
        var customerCount=prop_tb._customerByProductGrid.getUserData(0, "customer-count");
        var filteredRows=prop_tb._customerByProductGrid.getRowsNum();
        var selectedRows=(prop_tb._customerByProductGrid.getSelectedRowId()?prop_tb._customerByProductGrid.getSelectedRowId().split(',').length:0);
        prop_tb._sb.setText(customerCount+' '+(customerCount>1?'<?php echo _l('customers'); ?>':'<?php echo _l('customer'); ?>')+" - <?php echo _l('Filter')._l(':'); ?> "+filteredRows+" - <?php echo _l('Selection')._l(':'); ?> "+selectedRows);
    }

    let customerbyproduct_current_id = 0;
    cat_grid.attachEvent("onRowSelect",function (idproduct){
        if (propertiesPanel=='customerbyproduct' && (cat_grid.getSelectedRowId()!==null && customerbyproduct_current_id!=idproduct))
        {
            if (cat_grid.getSelectedRowId()!=null) {
                displayCustomers();
            }
            customerbyproduct_current_id=idproduct;
        }
    });

    <?php } /* end permission */ ?>
