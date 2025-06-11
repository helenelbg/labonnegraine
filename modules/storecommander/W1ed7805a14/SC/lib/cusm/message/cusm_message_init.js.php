
    prop_tb.addListOption('panel', 'message', 1, "button", '<?php echo _l('Discussion history', 1); ?>', "fad fa-comments");
    allowed_properties_panel[allowed_properties_panel.length] = "message";

    prop_tb.addButton("message_refresh",1000, "", "fa fa-sync green", "fa fa-sync green");
    prop_tb.setItemToolTip('message_refresh','<?php echo _l('Refresh grid', 1); ?>');


    needinitmessage = 1;
    function initmessage(){
        if (needinitmessage)
        {
            prop_tb._messageLayout = dhxLayout.cells('b').attachLayout('1C');
            prop_tb._messageLayout.cells('a').hideHeader();
            dhxLayout.cells('b').showHeader();
            prop_tb._messageGrid = prop_tb._messageLayout.cells('a').attachGrid();
            prop_tb._messageGrid.setImagePath("lib/js/imgs/");

            prop_tb._messageGrid.attachEvent("onEditCell", function(stage,rId,cInd,nValue,oValue){
                idxMsg=prop_tb._messageGrid.getColIndexById('message');
                if(stage=="2" && cInd==idxMsg)
                    return false;
            });

            // UISettings
            prop_tb._messageGrid._uisettings_prefix='cusm_message';
            prop_tb._messageGrid._uisettings_name=prop_tb._messageGrid._uisettings_prefix;
               prop_tb._messageGrid._first_loading=1;
               
            // UISettings
            initGridUISettings(prop_tb._messageGrid);
            
            needinitmessage=0;
        }
    }


    function setPropertiesPanel_message(id){
        if (id=='message')
        {
            if(lastDiscussionSelID!=undefined && lastDiscussionSelID!="")
            {
                idxDiscussionID=cusm_grid.getColIndexById('id_customer_thread');
                dhxLayout.cells('b').setText('<?php echo _l('Properties', 1).' '._l('of', 1); ?> '+cusm_grid.cells(lastDiscussionSelID,idxDiscussionID).getValue());
            }
            hidePropTBButtons();
            prop_tb.showItem('message_refresh');
            prop_tb.setItemText('panel', '<?php echo _l('Discussion history', 1); ?>');
            prop_tb.setItemImage('panel', 'fad fa-comments');
            needinitmessage = 1;
            initmessage();
            propertiesPanel='message';
            {
                displayMessage();
            }
        }
        if (id=='message_refresh')
        {
            displayMessage();
        }

    }
    prop_tb.attachEvent("onClick", setPropertiesPanel_message);


    function displayMessage()
    {
        if(lastDiscussionSelID!=undefined && lastDiscussionSelID!=null && lastDiscussionSelID>0)
        {
            idxDiscussionID=cusm_grid.getColIndexById('id_customer_thread');
            var id_discussion =  cusm_grid.cells(lastDiscussionSelID,idxDiscussionID).getValue();
            prop_tb._messageGrid.clearAll(true);
            prop_tb._messageGrid.load("index.php?ajax=1&act=cusm_message_get&id_discussion="+id_discussion,function()
                    {
                        nb=prop_tb._messageGrid.getRowsNum();
                        prop_tb._sb.setText('');
                    
                        // UISettings
                        loadGridUISettings(prop_tb._messageGrid);
                        
                        // UISettings
                        prop_tb._messageGrid._first_loading=0;
                    });
        }
    }


    let cusm_message_current_id = 0;
    cusm_grid.attachEvent("onRowSelect",function (idcustomer){
        if (propertiesPanel=='message' && !dhxLayout.cells('b').isCollapsed() && (cusm_grid.getSelectedRowId()!==null && cusm_message_current_id!=idcustomer)){
            displayMessage();
            cusm_message_current_id=idcustomer;
        }
    });
