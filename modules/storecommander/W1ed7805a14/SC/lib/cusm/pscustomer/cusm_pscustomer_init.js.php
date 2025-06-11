    prop_tb.addListOption('panel', 'pscustomer', 2, "button", '<?php echo _l('Customer', 1); ?>', "fa fa-prestashop");
    allowed_properties_panel[allowed_properties_panel.length] = "pscustomer";

    prop_tb.addButton("pscustomer_refresh",1000, "", "fa fa-sync green", "fa fa-sync green");
    prop_tb.setItemToolTip('pscustomer_refresh','<?php echo _l('Refresh grid', 1); ?>');


    needinitCusmPSCustomerPage = 1;
    function initCusmPSCustomerPage(){
        if (needinitCusmPSCustomerPage)
        {
            prop_tb._cusmPSCustomerPageLayout = dhxLayout.cells('b').attachLayout('1C');
            prop_tb._cusmPSCustomerPageLayout.cells('a').hideHeader();
            dhxLayout.cells('b').showHeader();
            needinitCusmPSCustomerPage=0;
        }
    }


    function setPropertiesPanel_pscustomer(id){
        if (id=='pscustomer')
        {
            if(lastDiscussionSelID!=undefined && lastDiscussionSelID!="")
            {
                idxDiscussionID=cusm_grid.getColIndexById('id_customer_thread');
                dhxLayout.cells('b').setText('<?php echo _l('Properties', 1).' '._l('of', 1); ?> '+cusm_grid.cells(lastDiscussionSelID,idxDiscussionID).getValue());
            }
            hidePropTBButtons();
            prop_tb.showItem('pscustomer_refresh');
            prop_tb.setItemText('panel', '<?php echo _l('Customer', 1); ?>');
            prop_tb.setItemImage('panel', 'fa fa-prestashop');
            needinitCusmPSCustomerPage = 1;
            initCusmPSCustomerPage();
            propertiesPanel='pscustomer';
            if (lastDiscussionSelID!=0)
            {
                displayCusmPSCustomerPage();
            }
        }
        if (id=='pscustomer_refresh')
        {
            displayCusmPSCustomerPage();
        }

    }
    prop_tb.attachEvent("onClick", setPropertiesPanel_pscustomer);


    function displayCusmPSCustomerPage()
    {
        var id_customer =  cusm_grid.getUserData(lastDiscussionSelID,"id_customer");
        if(id_customer!=null && id_customer>0){
            if (mustOpenBrowserTab){
                window.open("<?php echo SC_PS_PATH_ADMIN_REL; ?>index.php?tab=AdminCustomers&viewcustomer&id_customer="+id_customer+"&token=<?php echo $sc_agent->getPSToken('AdminCustomers'); ?>");
            }else{
                prop_tb._cusmPSCustomerPageLayout.cells('a').attachURL("<?php echo SC_PS_PATH_ADMIN_REL; ?>index.php?tab=AdminCustomers&viewcustomer&id_customer="+id_customer+"&token=<?php echo $sc_agent->getPSToken('AdminCustomers'); ?>");
            }
        }
    }

    let pscustomer_current_id = 0;
    cusm_grid.attachEvent("onRowSelect",function (idcustomer){
        if (propertiesPanel=='pscustomer' && !dhxLayout.cells('b').isCollapsed() && (cusm_grid.getSelectedRowId()!==null && pscustomer_current_id!=idcustomer)){
            displayCusmPSCustomerPage();
            pscustomer_current_id=idcustomer;
        }
    });