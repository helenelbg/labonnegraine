<?php
    if (
        version_compare(_PS_VERSION_, '1.7.3.0', '<') &&
        ((version_compare(_PS_VERSION_, '1.5.0.0', '<') || Feature::isFeatureActive())
        && !((defined('SC_MultiplesFeatures_ACTIVE') && SC_MultiplesFeatures_ACTIVE == '1') && (SCI::moduleIsInstalled('pm_multiplefeatures'))))
    ) {
        ?>
    <?php if (_r('GRI_CAT_PROPERTIES_GRID_FEATURE')) { ?>
        prop_tb.addListOption('panel', 'features', 5, "button", '<?php echo _l('Features', 1); ?>', "fa fa-eye");
        allowed_properties_panel[allowed_properties_panel.length] = "features";
    <?php } ?>
    prop_tb.addButtonTwoState('feature_filter', 1000, "", "fa fa-filter", "fa fa-filter");
    prop_tb.setItemToolTip('feature_filter','<?php echo _l('Display only features used by products in the same category', 1); ?>');    
    prop_tb.addButton("feature_refresh",1000, "", "fa fa-sync green", "fa fa-sync green");
    prop_tb.setItemToolTip('feature_refresh','<?php echo _l('Refresh grid', 1); ?>');
    prop_tb.addButtonTwoState('feature_lightNavigation', 1000, "", "fa fa-mouse-pointer", "fa fa-mouse-pointer");
    prop_tb.setItemToolTip('feature_lightNavigation','<?php echo _l('Light navigation (simple click on grid)', 1); ?>');
    prop_tb.addButton("dissociate_features",1000, "", "fad fa-unlink red", "fad fa-unlink red");
    prop_tb.setItemToolTip('dissociate_features','<?php echo _l('Dissociate selected features from selected products', 1); ?>');
    

    function featuresGrid_onEditCell(stage,rId,cInd,nValue,oValue){
        if (stage==1 && this.editor && this.editor.obj) this.editor.obj.select();
        idxID_feature_value=prop_tb._featuresGrid.getColIndexById('id_feature_value');
        if (cInd == idxID_feature_value)
                    {
                        if(stage==1){
                              var editor = this.editor;
                            var pos = this.getPosition(editor.cell);
                            var y = document.body.offsetHeight-pos[1];
                            if(y < editor.list.offsetHeight)
                                editor.list.style.top = pos[1] - editor.list.offsetHeight + 'px';
                        }
                        if (nValue>-2)
                        {
<?php
// Not custom
    foreach ($languages as $lang)
    {
        echo '                idxCustom'.$lang['iso_code']."=prop_tb._featuresGrid.getColIndexById('custom_".$lang['iso_code']."');";
        echo '                prop_tb._featuresGrid.cells(rId,idxCustom'.$lang['iso_code'].").setValue('');";
        echo '                prop_tb._featuresGrid.setCellExcellType(rId,idxCustom'.$lang['iso_code'].",'ro');";
    } ?>
                        }
                        if (nValue==-2){
<?php
    // Custom
    foreach ($languages as $lang)
    {
        echo '                idxCustom'.$lang['iso_code']."=prop_tb._featuresGrid.getColIndexById('custom_".$lang['iso_code']."');";
        echo '                prop_tb._featuresGrid.setCellExcellType(rId,idxCustom'.$lang['iso_code'].",'edtxt');";
    } ?>
                        }
                    }
                    if (nValue!=oValue)
                    {
                        var ids = cat_grid.getSelectedRowId();
                        var p_ids = new Array();
                        if(ids.search(",")>=0)
                            p_ids = ids.split(",");
                        else
                            p_ids[0] = ids;
                    
                        var nb_rows = p_ids.length*1 - 1;
                    
                        $.each(p_ids, function(num, p_id){
                            var data = "";
                            if(nb_rows!=num)
                                data = "noUnBold";
                        
                            var params = {
                                name: "cat_feature_productfeature_update_queue",
                                row: rId,
                                action: "update",
                                params: {},
                                callback: "callbackFeaturesProp('"+rId+"','update','"+rId+"','"+data+"');"
                            };
                            // COLUMN VALUES
                            prop_tb._featuresGrid.forEachCell(rId,function(cellObj,ind){
                                params.params[prop_tb._featuresGrid.getColumnId(ind)] = prop_tb._featuresGrid.cells(rId,ind).getValue();
                            });
                            params.params["id_product"] = p_id;
                            params.params["id_lang"] = SC_ID_LANG;
                            // USER DATA
                            
                            params.params = JSON.stringify(params.params);
                            addInUpdateQueue(params,prop_tb._featuresGrid);
                        });
                    }
                    return true;
                    

    }
            
    
    needInitFeatures = 1;
    function initFeatures(){
        if (needInitFeatures)
        {
            prop_tb._featuresLayout = dhxLayout.cells('b').attachLayout('1C');
            dhxLayout.cells('b').showHeader();
            prop_tb._featuresLayout.cells('a').hideHeader();
            prop_tb._featuresGrid = prop_tb._featuresLayout.cells('a').attachGrid();
            prop_tb._featuresGrid.setImagePath("lib/js/imgs/");
            prop_tb._featuresGrid.enableMultiselect(true);
            
            // UISettings
            prop_tb._featuresGrid._uisettings_prefix='cat_feature_productfeature';
            prop_tb._featuresGrid._uisettings_name=prop_tb._featuresGrid._uisettings_prefix;
               prop_tb._featuresGrid._first_loading=1;
               
            // UISettings
            initGridUISettings(prop_tb._featuresGrid);
            
            prop_tb._featuresGrid.attachEvent("onEditCell",featuresGrid_onEditCell);
            needInitFeatures=0;
        }
    }



    function setPropertiesPanel_features(id){
        if (id=='features')
        {
            if(lastProductSelID!=undefined && lastProductSelID!="")
            {
                dhxLayout.cells('b').setText('<?php echo _l('Properties', 1).' '._l('of', 1); ?> '+getSelectedItemValueOrID(cat_grid,lastProductSelID,'name'));
            }
            hidePropTBButtons();
            prop_tb.showItem('dissociate_features');
            prop_tb.showItem('feature_filter');
            prop_tb.showItem('feature_lightNavigation');
            prop_tb.showItem('feature_refresh');
            prop_tb.showItem('feature_win_attributes');
            prop_tb.setItemText('panel', '<?php echo _l('Features', 1); ?>');
            prop_tb.setItemImage('panel', 'fa fa-eye');
            needInitFeatures=1;
            initFeatures();
            propertiesPanel='features';
            if (lastProductSelID!=0)
            {
                displayFeatures();
            }
        }
        if (id=='feature_win_attributes')
        {
            if (!dhxWins.isWindow('wFeatures'))
            {
                wFeatures = dhxWins.createWindow('wFeatures', 50, 50, 900, $(window).height()-75);
                wFeatures.setText('"<?php echo _l('Features'); ?>"');
                $.get('../SC/index.php?ajax=1&act=cat_win-feature_init',function(data){
                    $('#jsExecute').html(data);
                });
                wFeatures.attachEvent('onClose', function(win){
                    wFeatures.hide();
                    return false;
                });
            }else{
                wFeatures.show();
            }
        }
        if (id=='feature_refresh')
        {
            if (lastProductSelID!=0)
            {
                displayFeatures();
            }
        }
        if(id == "dissociate_features"){
            var id_features = prop_tb._featuresGrid.getSelectedRowId();
            if(  id_features==undefined || id_features==null || id_features==''){
                dhtmlx.message({
                    text:'<?php echo _l('Select at least one feature', 1); ?>',
                    type:"error",
                    expire:5000
                });
            }
            else{
                var id_features = id_features.split(",");
                var nb = id_features.length;
                var idxFeaturesValue = prop_tb._featuresGrid.getColIndexById('id_feature_value');
                for(var i=0; i <=nb-1; i++){
                    var oValue = prop_tb._featuresGrid.cells(id_features[i],idxFeaturesValue).getValue();
                    var nValue = prop_tb._featuresGrid.cells(id_features[i],idxFeaturesValue).setValue("-1");
                    featuresGrid_onEditCell(2,id_features[i],idxFeaturesValue,oValue,nValue);
                    
                }
            }
        }
        
    }
    prop_tb.attachEvent("onClick", setPropertiesPanel_features);


    function setPropertiesPanelState_features(id,state){
        if (id=='feature_filter')
        {
            if (state)
            {
                featuresFilter=1;
            }else{
                featuresFilter=0;
            }
            displayFeatures();
        }
        if (id=='feature_lightNavigation')
        {
            if (state)
            {
                prop_tb._featuresGrid.enableLightMouseNavigation(true);
            }else{
                prop_tb._featuresGrid.enableLightMouseNavigation(false);
            }
        }
    }
    prop_tb.attachEvent("onStateChange", setPropertiesPanelState_features);


    function displayFeatures(){
        var tempIdList =     cat_grid.getSelectedRowId();
        if (tempIdList == null || tempIdList == '') return false;

        var oldFilters=new Array();
        for(var i=0,l=prop_tb._featuresGrid.getColumnsNum();i<l;i++)
        {
            if (prop_tb._featuresGrid.getFilterElement(i)!=null && prop_tb._featuresGrid.getFilterElement(i).value!='')
            {
                oldFilters[prop_tb._featuresGrid.getColumnId(i)]=prop_tb._featuresGrid.getFilterElement(i).value;
            }

        }

        prop_tb._featuresGrid.clearAll(true);
        $.post("index.php?ajax=1&act=cat_feature_productfeature_get&id_lang="+SC_ID_LANG+"&id_category="+catselection+"&filter="+featuresFilter+"&"+new Date().getTime(),{'id_product': tempIdList},function(data)
                {
                    prop_tb._featuresGrid.parse(data);
                    prop_tb._sb.setText("");

                    for(var i=0;i<prop_tb._featuresGrid.getColumnsNum();i++)
                    {
                        if (prop_tb._featuresGrid.getFilterElement(i)!=null && oldFilters[prop_tb._featuresGrid.getColumnId(i)]!=undefined)
                        {
                            prop_tb._featuresGrid.getFilterElement(i).value=oldFilters[prop_tb._featuresGrid.getColumnId(i)];
                        }
                    }
                    prop_tb._featuresGrid.filterByAll();
                
                    // UISettings
                    loadGridUISettings(prop_tb._featuresGrid);
                    
                    // UISettings
                    prop_tb._featuresGrid._first_loading=0;
                });
    }

    let features_current_id = 0;
    cat_grid.attachEvent("onRowSelect",function (idproduct){
        if (propertiesPanel=='features' && (cat_grid.getSelectedRowId()!==null && features_current_id!=idproduct)){
            //initFeatures();
            displayFeatures();
            features_current_id=idproduct;
        }
    });


        // CALLBACK FUNCTION
    function callbackFeaturesProp(sid,action,tid,data)
    {
        if (action=='update' && ((data!=undefined && data!="noUnBold") || data==undefined))
            prop_tb._featuresGrid.setRowTextNormal(sid);
    }

<?php
    }
