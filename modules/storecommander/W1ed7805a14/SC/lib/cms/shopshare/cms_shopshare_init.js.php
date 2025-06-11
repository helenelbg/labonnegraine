<?php
 if (SCMS) { ?>
    
    <?php if (_r('GRI_CMS_PROPERTIES_GRID_MB_SHARE')) { ?>
        prop_tb.addListOption('panel', 'cms_shopshare', 11, "button", '<?php echo _l('Multistore sharing manager', 1); ?>', "fa fa-layer-group");
        allowed_properties_panel[allowed_properties_panel.length] = "cms_shopshare";
    <?php } ?>
    
    prop_tb.addButton("cms_shopshare_refresh",1000, "", "fa fa-sync green", "fa fa-sync green");
    prop_tb.setItemToolTip('cms_shopshare_refresh','<?php echo _l('Refresh grid', 1); ?>');
    prop_tb.addButton("cms_shopshare_add_select",1000, "", "fad fa-link yellow", "fad fa-link yellow");
    prop_tb.setItemToolTip('cms_shopshare_add_select','<?php echo _l('Add all the CMS selected to all the selected shops', 1); ?>');
    prop_tb.addButton("cms_shopshare_del_select",1000, "", "fad fa-unlink red", "fad fa-unlink red");
    prop_tb.setItemToolTip('cms_shopshare_del_select','<?php echo _l('Delete all the CMS selected to all the selected shops', 1); ?>');
    
    needInitCmsShopshare = 1;
    function initCmsShopshare()
    {
        if (needInitCmsShopshare)
        {
            prop_tb._cms_shopshareLayout = dhxLayout.cells('b').attachLayout('1C');
            prop_tb._cms_shopshareLayout.cells('a').hideHeader();
            dhxLayout.cells('b').showHeader();

            prop_tb._cms_shopshareGrid = prop_tb._cms_shopshareLayout.cells('a').attachGrid();
            prop_tb._cms_shopshareGrid._name='_cms_shopshareGrid';
            prop_tb._cms_shopshareGrid.setImagePath("lib/js/imgs/");
              prop_tb._cms_shopshareGrid.enableDragAndDrop(false);
            prop_tb._cms_shopshareGrid.enableMultiselect(true);
            
            // UISettings
            prop_tb._cms_shopshareGrid._uisettings_prefix='cms_shopshare';
            prop_tb._cms_shopshareGrid._uisettings_name=prop_tb._cms_shopshareGrid._uisettings_prefix;
               prop_tb._cms_shopshareGrid._first_loading=1;
               
            // UISettings
            initGridUISettings(prop_tb._cms_shopshareGrid);

            prop_tb._cms_shopshareGrid.attachEvent("onEditCell",function(stage,rId,cInd,nValue,oValue)
            {
                if(stage==1)
                {
                    idxPresent=prop_tb._cms_shopshareGrid.getColIndexById('present');
                    idxActive=prop_tb._cms_shopshareGrid.getColIndexById('active');
                
                    var action = "";
                    if(cInd==idxActive)
                        action = "active";
                    else if(cInd==idxPresent)
                        action = "present";
                    
                    if(action!="")
                    {
                        var value = prop_tb._cms_shopshareGrid.cells(rId,cInd).isChecked();
                        var ids = cms_grid.getSelectedRowId();
                        var p_ids = new Array();
                        if(ids.search(",")>=0)
                            p_ids = ids.split(",");
                        else
                            p_ids[0] = ids;
                    
                        var nb_rows = p_ids.length*1 - 1;
                    
                        $.each(p_ids, function(num, p_id){
                            var data = "";
                            if(nb_rows!=num)
                                data = "noRefreshShop";
                        
                            var params = {
                                name: "cms_shopshare_update_queue",
                                row: "",
                                action: 'update',
                                params: {},
                                callback: "callbackCmsShopShare('"+rId+"','update','"+rId+"','"+data+"');"
                            };
                            // COLUMN VALUES
                            params.params['action_upd'] = action;
                            params.params['value'] = value;
                            params.params['id_lang'] = SC_ID_LANG;
                            params.params['gr_id'] = p_id;
                            params.params['id_shop'] = rId;
                            
                            params.params = JSON.stringify(params.params);
                            addInUpdateQueue(params,prop_tb._cms_shopshareGrid);
                        });
                    }
                }
                return true;
            });
            
            needInitCmsShopshare=0;
        }
    }

    function setPropertiesPanel_cms_shopshare(id){
        if (id=='cms_shopshare')
        {
            if(lastcms_pageID!=undefined && lastcms_pageID!="")
            {
                idxCmsName=cms_grid.getColIndexById('meta_title');
                dhxLayout.cells('b').setText('<?php echo _l('Properties', 1).' '._l('of', 1); ?> '+cms_grid.cells(lastcms_pageID,idxCmsName).getValue());
            }
            hidePropTBButtons();
            prop_tb.showItem('cms_shopshare_refresh');
            prop_tb.showItem('cms_shopshare_add_select');
            prop_tb.showItem('cms_shopshare_del_select');
            prop_tb.setItemText('panel', '<?php echo _l('Multistore sharing manager', 1); ?>');
            prop_tb.setItemImage('panel', 'fa fa-layer-group');
             needInitCmsShopshare = 1;
            initCmsShopshare();
            propertiesPanel='cms_shopshare';
            if (lastcms_pageID!=0)
            {
                displayCmsShopshare(false);
            }
        }
        if (id=='cms_shopshare_add_select')
        {
            var value = true;
            var ids = cms_grid.getSelectedRowId();
            var p_ids = new Array();
            if(ids.search(",")>=0)
                p_ids = ids.split(",");
            else
                p_ids[0] = ids;
        
            var nb_rows = p_ids.length*1 - 1;
        
            $.each(p_ids, function(num, p_id){
                var data = "noRefreshShop";
                if(nb_rows==num)
                    data = "";
            
                var params = {
                    name: "cms_shopshare_update_queue",
                    row: "",
                    action: 'update',
                    params: {},
                    callback: "callbackCmsShopShare('','update','','"+data+"');"
                };
                // COLUMN VALUES
                params.params['action_upd'] = "mass_present";
                params.params['value'] = value;
                params.params['id_lang'] = SC_ID_LANG;
                 params.params['gr_id'] = p_id;
                params.params['id_shop'] = prop_tb._cms_shopshareGrid.getSelectedRowId();
                
                params.params = JSON.stringify(params.params);
                addInUpdateQueue(params,prop_tb._cms_shopshareGrid);
            });
        }
        if (id=='cms_shopshare_del_select')
        {
            var value = false;
            var ids = cms_grid.getSelectedRowId();
            var p_ids = new Array();
            if(ids.search(",")>=0)
                p_ids = ids.split(",");
            else
                p_ids[0] = ids;
        
            var nb_rows = p_ids.length*1 - 1;
        
            $.each(p_ids, function(num, p_id){
                var data = "noRefreshShop";
                if(nb_rows==num)
                    data = "";
            
                var params = {
                    name: "cms_shopshare_update_queue",
                    row: "",
                    action: 'update',
                    params: {},
                    callback: "callbackCmsShopShare('','update','','"+data+"');"
                };
                // COLUMN VALUES
                params.params['action_upd'] = "mass_present";
                params.params['value'] = value;
                params.params['id_lang'] = SC_ID_LANG;
                 params.params['gr_id'] = p_id;
                params.params['id_shop'] = prop_tb._cms_shopshareGrid.getSelectedRowId();
                
                params.params = JSON.stringify(params.params);
                addInUpdateQueue(params,prop_tb._cms_shopshareGrid);
            });
        }

        if (id=='cms_shopshare_refresh')
        {
            displayCmsShopshare(false);
        }
    }
    prop_tb.attachEvent("onClick", setPropertiesPanel_cms_shopshare);

    function displayCmsShopshare(reloadJustChecbox)
    {
        reloadJustChecbox = false;
        if (reloadJustChecbox==true)
        {
            prop_tb._cms_shopshareGrid.uncheckAll();
            $.post("index.php?ajax=1&act=cms_shopshare_relation_get&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{"idlist":cms_grid.getSelectedRowId()},function(data)
            {
                idxPresent=prop_tb._cms_shopshareGrid.getColIndexById('present');
                idxActive=prop_tb._cms_shopshareGrid.getColIndexById('active');
                
                if (data!='')
                {
                    var shops=data.split(';');
                    for(i=0 ; i < shops.length ; i++)
                    {
                        var values = shops[i].split(',');
                        
                        if (prop_tb._cms_shopshareGrid.doesRowExist(values[0]))
                        {
                            if(values[1]=="1")
                                prop_tb._cms_shopshareGrid.cells(values[0],idxPresent).setValue(1);
                            if(values[2]=="1")
                                prop_tb._cms_shopshareGrid.cells(values[0],idxActive).setValue(1);
                        }
                    }
                }
                
                prop_tb._cms_shopshareGrid.forEachRow(function(id){
                    prop_tb._cms_shopshareGrid.cells(id,idxPresent).setDisabled(false);
               });
            });
        }else{
            prop_tb._cms_shopshareGrid.clearAll(true);
            var tempIdList = (cms_grid.getSelectedRowId()!=null?cms_grid.getSelectedRowId():"");
            $.post("index.php?ajax=1&act=cms_shopshare_get&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{'idlist': tempIdList},function(data)
            {
                prop_tb._cms_shopshareGrid.parse(data);
                nb=prop_tb._cms_shopshareGrid.getRowsNum();
                prop_tb._sb.setText(nb+(nb>1?" <?php echo _l('shops'); ?>":" <?php echo _l('shop'); ?>"));
                prop_tb._cms_shopshareGrid._rowsNum=nb;
                
            // UISettings
                loadGridUISettings(prop_tb._cms_shopshareGrid);
                prop_tb._cms_shopshareGrid._first_loading=0;
                
                idxPresent=prop_tb._cms_shopshareGrid.getColIndexById('present');
                idxActive=prop_tb._cms_shopshareGrid.getColIndexById('active');
                
                prop_tb._cms_shopshareGrid.forEachRow(function(id){
                     prop_tb._cms_shopshareGrid.cells(id,idxPresent).setDisabled(false);
               });
            });
        }
    }


    let cms_shopshare_current_id = 0;
    cms_grid.attachEvent("onRowSelect",function (idcms){
        if (propertiesPanel=='cms_shopshare' && (cms_grid.getSelectedRowId()!==null && cms_shopshare_current_id!=idcms)){
            displayCmsShopshare(false);
            cms_shopshare_current_id=idcms;
        }
    });
        
    // CALLBACK FUNCTION
    function callbackCmsShopShare(sid,action,tid, data)
    {
        if (action=='update')
        {
            var doDisplay = true;
            if(data!="noRefreshCMS")
            {
                if(sid==shopselection)
                {
                    displayCms('displayCmsShopshare(false)');
                    doDisplay = false;
                }
            }
            if(data=="noRefreshShop")
                doDisplay = false;
            if(doDisplay==true)
            {
                displayCmsShopshare(false);
            }
        }
    }
     
<?php } ?>
