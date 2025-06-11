    prop_tb.addListOption('panel', 'stats', 4, "button", '<?php echo _l('Stats', 1); ?>', "fa fa-chart-area");
    allowed_properties_panel[allowed_properties_panel.length] = "stats";

    prop_tb.addButton("stats_refresh",1000, "", "fa fa-sync green", "fa fa-sync green");
    prop_tb.setItemToolTip('stats_refresh','<?php echo _l('Refresh grid', 1); ?>');


    needinitCusmStatsPage = 1;
    function initCusmStatsPage(){
        if (needinitCusmStatsPage)
        {
            prop_tb._cusmStatsPageLayout = dhxLayout.cells('b').attachLayout('1C');
            prop_tb._cusmStatsPageLayout.cells('a').hideHeader();
            dhxLayout.cells('b').showHeader();
            needinitCusmStatsPage=0;
        }
    }


    function setPropertiesPanel_stats(id){
        if (id=='stats')
        {
            if(lastDiscussionSelID!=undefined && lastDiscussionSelID!="")
            {
                idxCustomerName=cusm_grid.getColIndexById('customer_name');
                dhxLayout.cells('b').setText('<?php echo _l('Properties', 1).' '._l('of', 1); ?> '+cusm_grid.cells(lastDiscussionSelID,idxCustomerName).getValue());
            }
            hidePropTBButtons();
            prop_tb.showItem('stats_refresh');
            prop_tb.setItemText('panel', '<?php echo _l('Stats', 1); ?>');
            prop_tb.setItemImage('panel', 'fa fa-chart-area');
            needinitCusmStatsPage = 1;
            initCusmStatsPage();
            propertiesPanel='stats';
            if (lastDiscussionSelID!=0)
            {
                displayCusmStatsPage();
            }
        }
        if (id=='stats_refresh')
        {
            displayCusmStatsPage();
        }

    }
    prop_tb.attachEvent("onClick", setPropertiesPanel_stats);


    function displayCusmStatsPage()
    {
        if(lastDiscussionSelID!=null && lastDiscussionSelID>0)
        {
            prop_tb._cusmStatsPageLayout.cells('a').attachURL("index.php?ajax=1&act=cusm_stats_get&id_discussion="+lastDiscussionSelID);
        }
    }

    let cusm_stats_current_id = 0;
    cusm_grid.attachEvent("onRowSelect",function (idcustomer){
        if (propertiesPanel=='stats' && !dhxLayout.cells('b').isCollapsed() && (cusm_grid.getSelectedRowId()!==null && cusm_stats_current_id!=idcustomer)){
            displayCusmStatsPage();
            cusm_stats_current_id=idcustomer;
        }
    });