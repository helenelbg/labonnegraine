<?php if (_r('GRI_ORD_PROPERTIES_GRID_ORDERHISTORY')) { ?>
    prop_tb.addListOption('panel', 'orderhistory', 2, "button", '<?php echo _l('Order history', 1); ?>', "fa fa-clock");
    allowed_properties_panel[allowed_properties_panel.length] = "orderhistory";

    prop_tb.addButton("orderhistory_refresh",1000, "", "fa fa-sync green", "fa fa-sync green");
    prop_tb.setItemToolTip('orderhistory_refresh','<?php echo _l('Refresh grid', 1); ?>');
    prop_tb.addButton("orderhistory_exportcsv",1000, "", "fad fa-file-csv green", "fad fa-file-csv green");
    prop_tb.setItemToolTip('orderhistory_exportcsv','<?php echo _l('Export grid to clipboard in CSV format for MSExcel with tab delimiter.'); ?>');


    needinitOrderHistory = 1;
    function initOrderHistory(){
        if (needinitOrderHistory)
        {
            prop_tb._orderHistoryLayout = dhxLayout.cells('b').attachLayout('1C');
            prop_tb._orderHistoryLayout.cells('a').hideHeader();
            dhxLayout.cells('b').showHeader();
            prop_tb._orderHistoryGrid = prop_tb._orderHistoryLayout.cells('a').attachGrid();
            prop_tb._orderHistoryGrid.setImagePath("lib/js/imgs/");
            
            // UISettings
            prop_tb._orderHistoryGrid._uisettings_prefix='ord_history';
            prop_tb._orderHistoryGrid._uisettings_name=prop_tb._orderHistoryGrid._uisettings_prefix;
               prop_tb._orderHistoryGrid._first_loading=1;
               
            // UISettings
            initGridUISettings(prop_tb._orderHistoryGrid);
            
            needinitOrderHistory=0;
        }
    }


    function setPropertiesPanel_orderhistory(id){
        switch(id) {
            case 'orderhistory':
                hidePropTBButtons();
                prop_tb.showItem('orderhistory_refresh');
                prop_tb.showItem('orderhistory_exportcsv');
                prop_tb.setItemText('panel', '<?php echo _l('Order history', 1); ?>');
                prop_tb.setItemImage('panel', 'fa fa-clock');
                needinitOrderHistory = 1;
                initOrderHistory();
                propertiesPanel='orderhistory';
                if (lastOrderSelID!=0)
                {
                    displayOrderHistory();
                }
                break;
            case 'orderhistory_refresh':
                displayOrderHistory();
                break;
            case 'orderhistory_exportcsv':
                displayQuickExportWindow(prop_tb._orderHistoryGrid,1);
                break;
        }

    }
    prop_tb.attachEvent("onClick", setPropertiesPanel_orderhistory);


    function displayOrderHistory()
    {
        prop_tb._orderHistoryGrid.clearAll(true);
        $.post("index.php?ajax=1&act=ord_history_get",{id_orders:ord_grid.getSelectedRowId()},function(data)
        {
            prop_tb._orderHistoryGrid.parse(data);
            nb=prop_tb._orderHistoryGrid.getRowsNum();
            prop_tb._sb.setText('');

            // UISettings
            loadGridUISettings(prop_tb._orderHistoryGrid);

            // UISettings
            prop_tb._orderHistoryGrid._first_loading=0;
        });
    }


    let orderhistory_current_id = 0;
    ord_grid.attachEvent("onRowSelect",function (idproduct){
        if (propertiesPanel=='orderhistory' && !dhxLayout.cells('b').isCollapsed() && (ord_grid.getSelectedRowId()!==null && orderhistory_current_id!=idproduct)){
            displayOrderHistory();
            orderhistory_current_id=idproduct;
        }
    });

<?php
    } // end permission
?>