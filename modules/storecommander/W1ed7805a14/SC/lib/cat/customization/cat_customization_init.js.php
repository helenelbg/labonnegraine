<?php if (_r('GRI_CAT_PROPERTIES_GRID_CUSTOMIZED_FIELDS')) { ?>
    prop_tb.addListOption('panel', 'customizations', 10, "button", '<?php echo _l('Customization fields', 1); ?>', "fad fa-i-cursor");
    allowed_properties_panel[allowed_properties_panel.length] = "customizations";
<?php } ?>

    prop_tb.addButton("customization_refresh",1000, "", "fa fa-sync green", "fa fa-sync green");
    prop_tb.setItemToolTip('customization_refresh','<?php echo _l('Refresh grid', 1); ?>');
    prop_tb.addButtonTwoState('customization_lightNavigation', 1000, "", "fa fa-mouse-pointer", "fa fa-mouse-pointer");
    prop_tb.setItemToolTip('customization_lightNavigation','<?php echo _l('Light navigation (simple click on grid)', 1); ?>');
    prop_tb.addButton("customization_add",1000, "", "fa fa-plus-circle green", "fa fa-plus-circle green");
    prop_tb.setItemToolTip('customization_add','<?php echo _l('Add fields', 1); ?>');
    prop_tb.addButton("customization_del",1000, "", "fa fa-minus-circle red", "fa fa-minus-circle red");
    prop_tb.setItemToolTip('customization_del','<?php echo _l('Delete selected fields', 1); ?>');
    prop_tb.addButton("customization_selectall",1000, "", 'fa fa-bolt yellow', 'fa fa-bolt yellow');
    prop_tb.setItemToolTip('customization_selectall','<?php echo _l('Select all'); ?>');
    prop_tb.addButton("exportcsv",1000, "", "fad fa-file-csv green", "fad fa-file-csv green");
    prop_tb.setItemToolTip('exportcsv','<?php echo _l('Export grid to clipboard in CSV format for MSExcel with tab delimiter.'); ?>');



    clipboardType_Customization = null;
    needInitCustomizations = 1;
    function initCustomizations(){
        if (needInitCustomizations)
        {
            prop_tb._customizationsLayout = dhxLayout.cells('b').attachLayout('1C');
            prop_tb._customizationsLayout.cells('a').hideHeader();
            customization_grid=prop_tb._customizationsLayout.cells('a').attachGrid();
            dhxLayout.cells('b').showHeader();
            customization_grid.setImagePath('lib/js/imgs/');
            customization_grid.enableSmartRendering(true);
            customization_grid.enableMultiselect(true);
            
            // UISettings
            customization_grid._uisettings_prefix='cat_customization';
            customization_grid._uisettings_name=customization_grid._uisettings_prefix;
               customization_grid._first_loading=1;
               
            // UISettings
            initGridUISettings(customization_grid);

            // update customization/product after used checkbox
            function onEditCellCustomizations(stage,rId,cInd,nValue,oValue){
                if (stage==1 && this.editor && this.editor.obj) this.editor.obj.select();
                if (nValue!=oValue)
                {
                    if(stage==2)
                    {
                        var params = {
                            name: "cat_customization_update",
                            row: rId,
                            action: "update",
                            params: {},
                            callback: "callbackCustomization('"+rId+"','update','"+rId+"');"
                        };

                        // COLUMN VALUES
                        params.params[customization_grid.getColumnId(cInd)] = customization_grid.cells(rId,cInd).getValue();

                        params.params = JSON.stringify(params.params);
                        addInUpdateQueue(params,customization_grid);
                    }
                }
                return true;
            }
            customization_grid.attachEvent("onEditCell",onEditCellCustomizations);

            // Context menu
            customization_cmenu=new dhtmlXMenuObject();
            customization_cmenu.renderAsContextMenu();
            function onGridCustomizationContextButtonClick(itemId){
                tabId=customization_grid.contextID.split('_');
                tabId=tabId[0];
                if (itemId=="copy"){
                    if (lastColumnRightClicked_Customization!=0)
                    {
                        clipboardValue_Customization=customization_grid.cells(tabId,lastColumnRightClicked_Customization).getValue();
                        customization_cmenu.setItemText('paste' , '<?php echo _l('Paste'); ?> '+customization_grid.cells(tabId,lastColumnRightClicked_Customization).getTitle());
                        clipboardType_Customization=lastColumnRightClicked_Customization;
                    }
                }
                if (itemId=="paste"){
                    if (lastColumnRightClicked_Customization!=0 && clipboardValue_Customization!=null && clipboardType_Customization==lastColumnRightClicked_Customization)
                    {
                        selection=customization_grid.getSelectedRowId();
                        if (selection!='' && selection!=null)
                        {
                            selArray=selection.split(',');
                            for(i=0 ; i < selArray.length ; i++)
                            {
                                if (customization_grid.getColumnId(lastColumnRightClicked_Customization) !='id_customization_field')
                                {
                                    customization_grid.cells(selArray[i],lastColumnRightClicked_Customization).setValue(clipboardValue_Customization);
                                    customization_grid.cells(selArray[i],lastColumnRightClicked_Customization).cell.wasChanged=true;
                                    onEditCellCustomizations(2,selArray[i],lastColumnRightClicked_Customization,clipboardValue_Customization);
                                }
                            }
                        }
                    }
                }
            }
            customization_cmenu.attachEvent("onClick", onGridCustomizationContextButtonClick);
            var contextMenuXML='<menu absolutePosition="auto" mode="popup" maxItems="8"  globalCss="contextMenu" globalSecondCss="contextMenu" globalTextCss="contextMenuItem">'+
                    '<item text="Object" id="object" enabled="false"/>'+
                    '<item text="<?php echo _l('Copy'); ?>" id="copy"/>'+
                    '<item text="<?php echo _l('Paste'); ?>" id="paste"/>'+
                '</menu>';
            customization_cmenu.loadStruct(contextMenuXML);
            customization_grid.enableContextMenu(customization_cmenu);

            customization_grid.attachEvent("onBeforeContextMenu", function(rowid,colidx,grid){
                var disableOnCols=new Array();
                disableOnCols.push(customization_grid.getColIndexById('id_customization_field'));
                if (in_array(colidx,disableOnCols))
                {
                    return false;
                }
                lastColumnRightClicked_Customization=colidx;
                customization_cmenu.setItemText('object', '<?php echo _l('Customization'); ?>');
                if (lastColumnRightClicked_Customization==clipboardType_Customization)
                {
                    customization_cmenu.setItemEnabled('paste');
                }else{
                    customization_cmenu.setItemDisabled('paste');
                }
                return true;
            });


            displayCustomizations();
            needInitCustomizations=0;
        }
    }



    function setPropertiesPanel_customizations(id){
        if (id=='customizations')
        {
            if(lastProductSelID!=undefined && lastProductSelID!="")
            {
                idxProductName=cat_grid.getColIndexById('name');
                dhxLayout.cells('b').setText('<?php echo _l('Properties', 1).' '._l('of', 1); ?> '+getSelectedItemValueOrID(cat_grid,lastProductSelID,'name'));
            }
            hidePropTBButtons();
            prop_tb.showItem('exportcsv');
            prop_tb.showItem('customization_selectall');
            prop_tb.showItem('customization_del');
            prop_tb.showItem('customization_add');
            prop_tb.showItem('customization_lightNavigation');
            prop_tb.showItem('customization_refresh');
            prop_tb.setItemText('panel', '<?php echo _l('Customization fields', 1); ?>');
            prop_tb.setItemImage('panel', 'fad fa-i-cursor');
            needInitCustomizations=1;
            initCustomizations();
            propertiesPanel='customizations';
        }
        if (id=='customization_add'){
            if (cat_grid.getSelectedRowId()===null)
            {
                alert('<?php echo _l('Please select a product', 1); ?>');
            }else{
                let newId = new Date().getTime();
                let newRow = [newId,cat_grid.getSelectedRowId(),0,0];
                customization_grid.addRow(newId,newRow);
                customization_grid.setRowHidden(newId, true);

                let params = {
                    name: "cat_customization_update",
                    row: newId,
                    action: "insert",
                    params: {callback: "callbackCustomization('"+newId+"','insert','{newid}');"}
                };
                // COLUMN VALUES
                customization_grid.forEachCell(newId,function(cellObj,ind){
                    params.params[customization_grid.getColumnId(ind)] = customization_grid.cells(newId,ind).getValue();
                });
                // USER DATA
                sendInsert(params,prop_tb._customizationsLayout.cells('a'));
            }
        }
        if (id=='customization_del'){
            if (customization_grid.getSelectedRowId()===null)
            {
                alert('<?php echo _l('Please select an item', 1); ?>');
            }else{
                if (confirm('<?php echo _l('Are you sure you want to delete the selected items?', 1); ?>'))
                {
                    let selection=customization_grid.getSelectedRowId();

                    ids=selection.split(',');
                    $.each(ids, function(num, rId){
                        var params = {
                            name: "cat_customization_update",
                            row: rId,
                            action: "delete",
                            params: {},
                            callback: "callbackCustomization('"+rId+"','delete','"+rId+"');"
                        };
                        params.params = JSON.stringify(params.params);
                        addInUpdateQueue(params,customization_grid);
                    });
                }
            }
        }
        if (id=='customization_selectall')
        {
            customization_grid.selectAll();
        }
        if (id=='customization_refresh'){
            displayCustomizations();
        }
        if (id=='exportcsv'){
            displayQuickExportWindow(customization_grid,1,1);
        }
    }
    prop_tb.attachEvent("onClick", setPropertiesPanel_customizations);

    function setPropertiesPanelState_customization(id,state){
        if (id=='customization_lightNavigation')
        {
            if (state)
            {
                customization_grid.enableLightMouseNavigation(true);
            }else{
                customization_grid.enableLightMouseNavigation(false);
            }
        }
    }
    prop_tb.attachEvent("onStateChange", setPropertiesPanelState_customization);

    function displayCustomizations(callback)
    {
        customization_grid.clearAll(true);
        prop_tb._sb.setText('');
        prop_tb._sb.setText('<?php echo _l('Loading in progress, please wait...', 1); ?>');
        customization_grid.load("index.php?ajax=1&act=cat_customization_get&product_list="+cat_grid.getSelectedRowId()+"&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),function(){
            nb=customization_grid.getRowsNum();
            prop_tb._sb.setText(nb+' '+(nb>1?'<?php echo _l('customization fields', 1); ?>':'<?php echo _l('customization field', 1); ?>'));
            customization_grid._rowsNum=nb;

        // UISettings
            loadGridUISettings(customization_grid);
            customization_grid._first_loading=0;

            if (callback!='') eval(callback);
        });
    }

    let customizations_current_id = 0;
    cat_grid.attachEvent("onRowSelect",function (idproduct){
        if (propertiesPanel=='customizations' && (cat_grid.getSelectedRowId()!==null && customizations_current_id!=idproduct)){
            displayCustomizations();
            customizations_current_id=idproduct;
        }
    });

    // CALLBACK FUNCTION
    function callbackCustomization(sid,action,tid)
    {
        if (action=='insert') {
            idxCustID=customization_grid.getColIndexById('id_customization_field');
            customization_grid.cells(sid,idxCustID).setValue(tid);
            customization_grid.changeRowId(sid,tid);
            customization_grid.setRowHidden(tid, false);
            customization_grid.showRow(tid);
            prop_tb._customizationsLayout.cells('a').progressOff();
        } else if (action=='update') {
            customization_grid.setRowTextNormal(sid);
        } else if(action=='delete') {
            customization_grid.deleteRow(sid);
        }
    }