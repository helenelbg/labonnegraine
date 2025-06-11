<?php if (_r('GRI_ORD_PROPERTIES_GRID_PRODUCT')) { ?>
    prop_tb.addListOption('panel', 'orderpsorderpage', 4, "button", '<?php echo _l('Prestashop: order page', 1); ?>', "fa fa-prestashop");
    allowed_properties_panel[allowed_properties_panel.length] = "orderpsorderpage";

    prop_tb.addButton("orderpsorderpage_refresh",1000, "", "fa fa-sync green", "fa fa-sync green");
    prop_tb.setItemToolTip('orderpsorderpage_refresh','<?php echo _l('Refresh grid', 1); ?>');


    needinitOrderPSOrderPage = 1;
    function initOrderPSOrderPage(){
        if (needinitOrderPSOrderPage)
        {
            prop_tb._orderPSOrderPageLayout = dhxLayout.cells('b').attachLayout('1C');
            prop_tb._orderPSOrderPageLayout.cells('a').hideHeader();
            dhxLayout.cells('b').showHeader();
            needinitOrderPSOrderPage=0;
        }
    }


    function setPropertiesPanel_orderpsorderpage(id){
        if (id=='orderpsorderpage')
        {
             if(lastOrderSelID!=undefined && lastOrderSelID!="")
             {
                 var rowId = ord_grid.getSelectedRowId();
                 idxOrderID=ord_grid.getColIndexById('id_order');
                 dhxLayout.cells('b').setText('<?php echo _l('Properties', 1).' '._l('of', 1); ?> '+ord_grid.cells(rowId,idxOrderID).getValue());
             }
            hidePropTBButtons();
            prop_tb.showItem('orderpsorderpage_refresh');
            prop_tb.setItemText('panel', '<?php echo _l('Prestashop: order page', 1); ?>');
            prop_tb.setItemImage('panel', 'fa fa-prestashop');
            needinitOrderPSOrderPage = 1;
            initOrderPSOrderPage();
            propertiesPanel='orderpsorderpage';
            if (lastOrderSelID!=0)
            {
                displayOrderPSOrderPage();
            }
        }
        if (id=='orderpsorderpage_refresh')
        {
            displayOrderPSOrderPage();
        }

    }
    prop_tb.attachEvent("onClick", setPropertiesPanel_orderpsorderpage);


    function displayOrderPSOrderPage()
    {
        selectedRow=0;
        selectedRows=(ord_grid.getSelectedRowId()?ord_grid.getSelectedRowId().split(','):0);
        if (selectedRows)
            selectedRow=selectedRows[0];
        idxIdOrder=ord_grid.getColIndexById('id_order');
        id_order=ord_grid.cells(selectedRow,idxIdOrder).getValue();
        if (selectedRow)
        {
            if (mustOpenBrowserTab){
                window.open("<?php echo SC_PS_PATH_ADMIN_REL; ?>index.php?tab=AdminOrders&vieworder&id_order="+id_order+"&token=<?php echo $sc_agent->getPSToken('AdminOrders'); ?>");
            }else{
                prop_tb._orderPSOrderPageLayout.cells('a').attachURL("<?php echo SC_PS_PATH_ADMIN_REL; ?>index.php?tab=AdminOrders&vieworder&id_order="+id_order+"&token=<?php echo $sc_agent->getPSToken('AdminOrders'); ?>");
            }
        }
    }

    let orderpsorderpage_current_id = 0;
    ord_grid.attachEvent("onRowSelect",function (idorder){
        lastOrderSelID = idorder;
        if (propertiesPanel=='orderpsorderpage' && !dhxLayout.cells('b').isCollapsed() && (!ord_grid_lightNavigation) && (ord_grid.getSelectedRowId()!==null && orderpsorderpage_current_id!=idorder)){
            displayOrderPSOrderPage();
            if(lastOrderSelID!=undefined && lastOrderSelID!="")
            {
                idxOrderID=ord_grid.getColIndexById('id_order');
                dhxLayout.cells('b').setText('<?php echo _l('Properties', 1).' '._l('of', 1); ?> '+ord_grid.cells(lastOrderSelID,idxOrderID).getValue());
            }
            orderpsorderpage_current_id=idorder;
        }
    });

<?php
    } // end permission
?>