<?php
 if (SCMS) { ?>

    <?php if (_r('GRI_CAT_PROPERTIES_GRID_MB_SHARE')) { ?>
        prop_tb.addListOption('panel', 'shopshare', 11, "button", '<?php echo _l('Multistore sharing manager', 1); ?>', "fa fa-layer-group");
        allowed_properties_panel[allowed_properties_panel.length] = "shopshare";
    <?php } ?>

    var auto_share_imgs = <?php echo _s('CAT_PROD_IMAGE_AUTO_SHOP_SHARE'); ?>;

    prop_tb.addButton("shopshare_refresh",1000, "", "fa fa-sync green", "fa fa-sync green");
    prop_tb.setItemToolTip('shopshare_refresh','<?php echo _l('Refresh grid', 1); ?>');
    prop_tb.addButtonTwoState('shopshare_auto_share_imgs', 1000, "", "fad fa-image", "fad fa-image");
    prop_tb.setItemToolTip('shopshare_auto_share_imgs','<?php echo _l('Automatically share the products images', 1); ?>');
    prop_tb.addButton("shopshare_inactive_select",1000, "", "fad fa-lightbulb grey", "fad fa-lightbulb grey");
    prop_tb.setItemToolTip('shopshare_inactive_select','<?php echo _l('Desactivate all selected products for the selected shops', 1); ?>');
    prop_tb.addButton("shopshare_active_select",1000, "", "fa fa-lightbulb-on yellow", "fa fa-lightbulb-on yellow");
    prop_tb.setItemToolTip('shopshare_active_select','<?php echo _l('Activate all selected products for the selected shops', 1); ?>');
    prop_tb.addButton("shopshare_add_select",1000, "", "fad fa-link yellow", "fad fa-link yellow");
    prop_tb.setItemToolTip('shopshare_add_select','<?php echo _l('Add all the products selected to all the selected shops', 1); ?>');
    prop_tb.addButton("shopshare_del_select",1000, "", "fad fa-unlink red", "fad fa-unlink red");
    prop_tb.setItemToolTip('shopshare_del_select','<?php echo _l('Delete all the products selected to all the selected shops', 1); ?>');

    needInitShopshare = 1;
    function initShopshare()
    {
        if (needInitShopshare)
        {
            prop_tb._shopshareLayout = dhxLayout.cells('b').attachLayout('1C');
            prop_tb._shopshareLayout.cells('a').hideHeader();
            dhxLayout.cells('b').showHeader();

            prop_tb._shopshareGrid = prop_tb._shopshareLayout.cells('a').attachGrid();
            prop_tb._shopshareGrid._name='_shopshareGrid';
            prop_tb._shopshareGrid.setImagePath("lib/js/imgs/");
              prop_tb._shopshareGrid.enableDragAndDrop(false);
            prop_tb._shopshareGrid.enableMultiselect(true);

            // UISettings
            prop_tb._shopshareGrid._uisettings_prefix='cat_shopshare';
            prop_tb._shopshareGrid._uisettings_name=prop_tb._shopshareGrid._uisettings_prefix;
               prop_tb._shopshareGrid._first_loading=1;

            // UISettings
            initGridUISettings(prop_tb._shopshareGrid);

            prop_tb._shopshareGrid.attachEvent("onCheckbox",function(rId,cInd,value){
                let action = null;
                switch(cInd) {
                    case prop_tb._shopshareGrid.getColIndexById('present'):
                        action = 'present';
                        break;
                    case prop_tb._shopshareGrid.getColIndexById('active'):
                        action = 'active';
                        break;
                    case prop_tb._shopshareGrid.getColIndexById('is_default'):
                        action = 'default';
                        break;
                }

                if(action)
                {
                    let value = prop_tb._shopshareGrid.cells(rId,cInd).isChecked();
                    let ids = cat_grid.getSelectedRowId();
                    let p_ids = ids.split(',');
                    let nb_rows = p_ids.length*1 - 1;

                    $.each(p_ids, function(num, p_id){
                        var data = "";
                        if(nb_rows!=num)
                            data = "noRefreshShop";

                        var params = {
                            name: "cat_shopshare_update_queue",
                            row: "",
                            action: 'update',
                            params: {},
                            callback: "callbackShopShare('"+rId+"','update','"+rId+"','"+data+"');"
                        };
                        // COLUMN VALUES
                        params.params['auto_share_imgs'] = auto_share_imgs;
                        params.params['action_upd'] = action;
                        params.params['value'] = value;
                        params.params['id_lang'] = SC_ID_LANG;
                        params.params['gr_id'] = p_id;
                        params.params['id_shop'] = rId;
                        // USER DATA

                        params.params = JSON.stringify(params.params);
                        addInUpdateQueue(params,prop_tb._shopshareGrid);
                    });
                }
            });

            needInitShopshare=0;
        }
    }

    function setPropertiesPanel_shopshare(id){
        if (id=='shopshare')
        {
            if(lastProductSelID!=undefined && lastProductSelID!="")
            {
                dhxLayout.cells('b').setText('<?php echo _l('Properties', 1).' '._l('of', 1); ?> '+getSelectedItemValueOrID(cat_grid,lastProductSelID,'name'));
            }
            hidePropTBButtons();
            prop_tb.showItem('shopshare_refresh');
            prop_tb.showItem('shopshare_add_select');
            prop_tb.showItem('shopshare_del_select');
            prop_tb.showItem('shopshare_active_select');
            prop_tb.showItem('shopshare_inactive_select');
            prop_tb.showItem('shopshare_auto_share_imgs');
            prop_tb.setItemState("shopshare_auto_share_imgs", auto_share_imgs);
            prop_tb.setItemText('panel', '<?php echo _l('Multistore sharing manager', 1); ?>');
            prop_tb.setItemImage('panel', 'fa fa-layer-group');
             needInitShopshare = 1;
            initShopshare();
            propertiesPanel='shopshare';
            if (lastProductSelID!=0)
            {
                displayShopshare(false);
            }
        }
        if (id=='shopshare_add_select')
        {
            var value = true;
            var ids = cat_grid.getSelectedRowId();
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
                    name: "cat_shopshare_update_queue",
                    row: "",
                    action: 'update',
                    params: {},
                    callback: "callbackShopShare('','update','','"+data+"');"
                };
                // COLUMN VALUES
                params.params['auto_share_imgs'] = auto_share_imgs;
                params.params['action_upd'] = "mass_present";
                params.params['value'] = value;
                params.params['id_lang'] = SC_ID_LANG;
                 params.params['gr_id'] = p_id;
                params.params['id_shop'] = prop_tb._shopshareGrid.getSelectedRowId();
                // USER DATA

                params.params = JSON.stringify(params.params);
                addInUpdateQueue(params,prop_tb._shopshareGrid);
            });
        }
        if (id=='shopshare_del_select')
        {
            var value = false;
            var ids = cat_grid.getSelectedRowId();
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
                    name: "cat_shopshare_update_queue",
                    row: "",
                    action: 'update',
                    params: {},
                    callback: "callbackShopShare('','update','','"+data+"');"
                };
                // COLUMN VALUES
                params.params['auto_share_imgs'] = auto_share_imgs;
                params.params['action_upd'] = "mass_present";
                params.params['value'] = value;
                params.params['id_lang'] = SC_ID_LANG;
                 params.params['gr_id'] = p_id;
                params.params['id_shop'] = prop_tb._shopshareGrid.getSelectedRowId();
                // USER DATA

                params.params = JSON.stringify(params.params);
                addInUpdateQueue(params,prop_tb._shopshareGrid);
            });
        }
        if (id=='shopshare_active_select')
        {
            var value = true;
            var ids = cat_grid.getSelectedRowId();
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
                    name: "cat_shopshare_update_queue",
                    row: "",
                    action: 'update',
                    params: {},
                    callback: "callbackShopShare('','update','','"+data+"');"
                };
                // COLUMN VALUES
                params.params['auto_share_imgs'] = auto_share_imgs;
                params.params['action_upd'] = "mass_active";
                params.params['value'] = value;
                params.params['id_lang'] = SC_ID_LANG;
                 params.params['gr_id'] = p_id;
                params.params['id_shop'] = prop_tb._shopshareGrid.getSelectedRowId();
                // USER DATA

                params.params = JSON.stringify(params.params);
                addInUpdateQueue(params,prop_tb._shopshareGrid);
            });
        }
        if (id=='shopshare_inactive_select')
        {
            var value = false;
            var ids = cat_grid.getSelectedRowId();
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
                    name: "cat_shopshare_update_queue",
                    row: "",
                    action: 'update',
                    params: {},
                    callback: "callbackShopShare('','update','','"+data+"');"
                };
                // COLUMN VALUES
                params.params['auto_share_imgs'] = auto_share_imgs;
                params.params['action_upd'] = "mass_active";
                params.params['value'] = value;
                 params.params['gr_id'] = p_id;
                 params.params['id_lang'] = SC_ID_LANG;
                params.params['id_shop'] = prop_tb._shopshareGrid.getSelectedRowId();
                // USER DATA

                params.params = JSON.stringify(params.params);
                addInUpdateQueue(params,prop_tb._shopshareGrid);
            });
        }
        if (id=='shopshare_refresh')
        {
            displayShopshare(false);
        }
    }
    prop_tb.attachEvent("onClick", setPropertiesPanel_shopshare);
    prop_tb.attachEvent("onStateChange",function(id,state){
        if (id=='shopshare_auto_share_imgs')
        {
            if (state)
            {
                auto_share_imgs = 1;
            }else{
                auto_share_imgs = 0;
            }
        }
    });

    function displayShopshare(reloadJustChecbox)
    {
        reloadJustChecbox = false;
        if (reloadJustChecbox==true)
        {
            prop_tb._shopshareGrid.uncheckAll();
            $.post("index.php?ajax=1&act=cat_shopshare_relation_get&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{"idlist":cat_grid.getSelectedRowId()},function(data)
            {
                idxPresent=prop_tb._shopshareGrid.getColIndexById('present');
                idxActive=prop_tb._shopshareGrid.getColIndexById('active');
                idxDefault=prop_tb._shopshareGrid.getColIndexById('is_default');

                if (data!='')
                {
                    var shops=data.split(';');
                    for(i=0 ; i < shops.length ; i++)
                    {
                        var values = shops[i].split(',');

                        if (prop_tb._shopshareGrid.doesRowExist(values[0]))
                        {
                            prop_tb._shopshareGrid.cells(values[0],idxDefault).setValue(values[3]);
                            if(values[1]=="1")
                                prop_tb._shopshareGrid.cells(values[0],idxPresent).setValue(1);
                            if(values[2]=="1")
                                prop_tb._shopshareGrid.cells(values[0],idxActive).setValue(1);
                        }
                    }
                }

                prop_tb._shopshareGrid.forEachRow(function(id){
                    if(prop_tb._shopshareGrid.cells(id,idxDefault).isChecked())
                        prop_tb._shopshareGrid.cells(id,idxPresent).setDisabled(true);
                    else
                        prop_tb._shopshareGrid.cells(id,idxPresent).setDisabled(false);
               });
            });
        }else{
            prop_tb._shopshareGrid.clearAll(true);
            var tempIdList = (cat_grid.getSelectedRowId()!=null?cat_grid.getSelectedRowId():"");
            $.post("index.php?ajax=1&act=cat_shopshare_get&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{'idlist': tempIdList},function(data)
            {
                prop_tb._shopshareGrid.parse(data);
                nb=prop_tb._shopshareGrid.getRowsNum();
                prop_tb._sb.setText(nb+(nb>1?" <?php echo _l('shops'); ?>":" <?php echo _l('shop'); ?>"));
                prop_tb._shopshareGrid._rowsNum=nb;

            // UISettings
                loadGridUISettings(prop_tb._shopshareGrid);
                prop_tb._shopshareGrid._first_loading=0;

                idxPresent=prop_tb._shopshareGrid.getColIndexById('present');
                idxActive=prop_tb._shopshareGrid.getColIndexById('active');
                idxDefault=prop_tb._shopshareGrid.getColIndexById('is_default');

                prop_tb._shopshareGrid.forEachRow(function(id){
                    if(prop_tb._shopshareGrid.cells(id,idxDefault).isChecked())
                        prop_tb._shopshareGrid.cells(id,idxPresent).setDisabled(true);
                    else
                        prop_tb._shopshareGrid.cells(id,idxPresent).setDisabled(false);
               });
            });
        }
    }


    let shopshare_current_id = 0;
    cat_grid.attachEvent("onRowSelect",function (idproduct){
        if (propertiesPanel=='shopshare' && (cat_grid.getSelectedRowId()!==null && shopshare_current_id!=idproduct)){
            displayShopshare(false);
            shopshare_current_id=idproduct;
        }
    });

    // CALLBACK FUNCTION
    function callbackShopShare(sid,action,tid, data)
    {
        if (action=='update')
        {
            var doDisplay = true;
            if(data!="noRefreshProduct")
            {
                if(sid==shopselection)
                {
                    displayProducts('displayShopshare(false)');
                    doDisplay = false;
                }
            }
            if(data=="noRefreshShop")
                doDisplay = false;
            if(doDisplay==true)
            {
                displayShopshare(false);
            }
        }
    }

<?php } ?>
