    prop_tb.addListOption('panel', 'psorder', 3, "button", '<?php echo _l('Order', 1); ?>', "fad fa-eye");
    allowed_properties_panel[allowed_properties_panel.length] = "psorder";

    prop_tb.addButton("psorder_refresh",1000, "", "fa fa-sync green", "fa fa-sync green");
    prop_tb.setItemToolTip('psorder_refresh','<?php echo _l('Refresh grid', 1); ?>');


    needinitCusmPSOrderPage = 1;
    function initCusmPSOrderPage(){
        if (needinitCusmPSOrderPage)
        {
            prop_tb._cusmPSOrderPageLayout = dhxLayout.cells('b').attachLayout('1C');
            prop_tb._cusmPSOrderPageLayout.cells('a').hideHeader();
            dhxLayout.cells('b').showHeader();
            needinitCusmPSOrderPage=0;
        }
    }


    function setPropertiesPanel_psorder(id){
        if (id=='psorder')
        {
            if(lastDiscussionSelID!=undefined && lastDiscussionSelID!="")
            {
                idxDiscussionID=cusm_grid.getColIndexById('id_customer_thread');
                dhxLayout.cells('b').setText('<?php echo _l('Properties', 1).' '._l('of', 1); ?> '+cusm_grid.cells(lastDiscussionSelID,idxDiscussionID).getValue());
            }
            hidePropTBButtons();
            prop_tb.showItem('psorder_refresh');
            prop_tb.setItemText('panel', '<?php echo _l('Order', 1); ?>');
            prop_tb.setItemImage('panel', 'fad fa-eye');
            needinitCusmPSOrderPage = 1;
            initCusmPSOrderPage();
            propertiesPanel='psorder';
            if (lastDiscussionSelID!=0)
            {
                displayCusmPSOrderPage();
            }
        }
        if (id=='psorder_refresh')
        {
            displayCusmPSOrderPage();
        }

    }
    prop_tb.attachEvent("onClick", setPropertiesPanel_psorder);


    function displayCusmPSOrderPage()
    {
        if(lastDiscussionSelID!=null && lastDiscussionSelID>0)
        {
            idxOrderID=cusm_grid.getColIndexById('id_order');
            var id_order = cusm_grid.cells(lastDiscussionSelID,idxOrderID).getValue();
            if(id_order!=null && id_order>0){
                if (mustOpenBrowserTab){
                    window.open("<?php echo SC_PS_PATH_ADMIN_REL; ?>index.php?tab=AdminOrders&vieworder&id_order="+id_order+"&token=<?php echo $sc_agent->getPSToken('AdminOrders'); ?>");
                }else{
                    prop_tb._cusmPSOrderPageLayout.cells('a').attachURL("<?php echo SC_PS_PATH_ADMIN_REL; ?>index.php?tab=AdminOrders&vieworder&id_order="+id_order+"&token=<?php echo $sc_agent->getPSToken('AdminOrders'); ?>");
                }
            }else
                prop_tb._cusmPSOrderPageLayout.cells('a').attachURL("index.php?ajax=1&act=cusm_psorder_empty");
        }
    }

    let psorder_current_id = 0;
    cusm_grid.attachEvent("onRowSelect",function (idcustomer){
        if (propertiesPanel=='psorder' && !dhxLayout.cells('b').isCollapsed() && (cusm_grid.getSelectedRowId()!==null && psorder_current_id!=idcustomer)){
            displayCusmPSOrderPage();
            psorder_current_id=idcustomer;
        }
    });