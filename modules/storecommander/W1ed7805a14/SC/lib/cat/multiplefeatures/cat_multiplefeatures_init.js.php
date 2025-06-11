<?php
if (
    version_compare(_PS_VERSION_, '1.7.3.0', '>=') || (
    (version_compare(_PS_VERSION_, '1.5.0.0', '<') || Feature::isFeatureActive())
    && (defined('SC_MultiplesFeatures_ACTIVE') && SC_MultiplesFeatures_ACTIVE == '1')
    && (SCI::moduleIsInstalled('pm_multiplefeatures')))
) {
    ?>
    <?php if (_r('GRI_CAT_PROPERTIES_GRID_FEATURE')) { ?>
    prop_tb.addListOption('panel', 'multiplefeatures', 5, "button", '<?php echo _l('Features', 1); ?> - <?php echo _l('multiple', 1); ?>', "fa fa-eye");
    allowed_properties_panel[allowed_properties_panel.length] = "multiplefeatures";
    <?php } ?>

    multiplefeaturesFilter=0;
    multiplefeaturesValuesFilter=0;
    multiplefeatures_id_group=0;

    function setPropertiesPanel_multiplefeatures(id)
    {
        if (id=='multiplefeatures')
        {
            id_product_multiplefeatures = lastProductSelID;
            hidePropTBButtons();
            prop_tb.setItemText('panel', '<?php echo _l('Features'); ?> - <?php echo _l('multiple'); ?>');
            prop_tb.addButton("cat_multipleFeatures_help", 1200, "", "fad fa-question-circle blue", "fad fa-question-circle blue");
            prop_tb.setItemToolTip('cat_multipleFeatures_help','<?php echo _l('Help'); ?>');
            prop_tb.hideItem('help');
            prop_tb.showItem('cat_multipleFeatures_help');

            prop_tb.attachEvent("onClick", function(id){
                if (id=='cat_multipleFeatures_help'){
                    window.open('<?php echo getScExternalLink('support_features'); ?>');
                }
            })

            prop_tb.setItemImage('panel', 'fa fa-eye');
            lastFeatureSPSelID=0;
            prop_tb._multipleFeaturesLayout = dhxLayout.cells('b').attachLayout('2U');
            prop_tb._multipleFeaturesLayout.cells('a').setText('<?php echo _l('Groups'); ?>');
            prop_tb._multipleFeaturesLayout.cells('b').setText('<?php echo _l('Values'); ?>');

            is_prop_4columns = true;
            prop_4column_layout = prop_tb._multipleFeaturesLayout;

            // GROUPS
            prop_tb._multipleFeaturesLayoutGridA=prop_tb._multipleFeaturesLayout.cells('a').attachGrid();
            prop_tb._multipleFeaturesLayoutGridA.enableMultiselect(false);

            prop_tb._multipleFeaturesLayoutGridA_tb=prop_tb._multipleFeaturesLayout.cells('a').attachToolbar();
            prop_tb._multipleFeaturesLayoutGridA_tb.setIconset('awesome');
            prop_tb._multipleFeaturesLayoutGridA_tb.addButton("refresh", 1000, "", "fa fa-sync green", "fa fa-sync green");
            prop_tb._multipleFeaturesLayoutGridA_tb.setItemToolTip('refresh','<?php echo _l('Refresh grid'); ?>');
            prop_tb._multipleFeaturesLayoutGridA_tb.addButtonTwoState('filter', 1000, "", "fa fa-filter", "fa fa-filter");
            prop_tb._multipleFeaturesLayoutGridA_tb.setItemToolTip('filter','<?php echo _l('Display only features used by products in the same category'); ?>');
            prop_tb._multipleFeaturesLayoutGridA_tb.addButton("win_features", 1000, "", "fa fa-asterisk yellow", "fa fa-asterisk yellow");
            prop_tb._multipleFeaturesLayoutGridA_tb.setItemToolTip('win_features','<?php echo _l('Open Features window', 1); ?>');
            prop_tb._multipleFeaturesLayoutGridA_tb.attachEvent("onClick", function(id)
            {
                if (id=='refresh')
                {
                    displayMultipleFeaturesGroups();
                }
                if (id=='win_features')
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
            });
            prop_tb._multipleFeaturesLayoutGridA_tb.attachEvent("onStateChange", function(id, state)
            {
                if (id=='filter')
                {
                    multiplefeaturesFilter=Number(state);
                    displayMultipleFeaturesGroups();
                }
            });
            prop_tb._multipleFeaturesLayoutGridA.attachEvent("onEditCell",function(stage,rId,cInd,nValue,oValue)
            {
                idxIdFeature=prop_tb._multipleFeaturesLayoutGridA.getColIndexById('id_feature');
                idxName=prop_tb._multipleFeaturesLayoutGridA.getColIndexById('name');
                idxCustom=prop_tb._multipleFeaturesLayoutGridA.getColumnId(cInd);
                if(idxCustom.includes('custom_')){
                    if(!dhxWins.isWindow('wCustomFeatureValueEditor')){
                        wCustomFeatureValueEditor = dhxWins.createWindow('wCustomFeatureValueEditor', 50, 50,200,200);
                        wCustomFeatureValueEditor.hide();
                        wCustomFeatureValueEditor.setText('<?php echo _l('Custom features'); ?>');
                        $.get('index.php?ajax=1&act=cat_win-multiplefeatures_init&featureId='+prop_tb._multipleFeaturesLayoutGridA.getSelectedRowId(),function(data){
                            $('#jsExecute').html(data);
                        });
                        wCustomFeatureValueEditor.attachEvent("onClose", function(win){
                            prop_tb._multipleFeaturesLayoutGridA_tb.callEvent("onClick",["refresh"]);
                            return true;
                        });
                    } else {
                        dhxWins.window('wCustomFeatureValueEditor').show();
                    }
                    return false;
                } else if (cInd !== idxIdFeature || cInd !== idxName)
                {
                    if(stage==2)
                    {
                        var colid = prop_tb._multipleFeaturesLayoutGridA.getColumnId(cInd);
                        var exp = colid.split("_");
                        var iso = exp[1];

                        $.post("index.php?ajax=1&act=cat_multiplefeatures_value_relation_update&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),
                            {
                                "id_product": cat_grid.getSelectedRowId(),
                                "id_feature": multiplefeatures_id_group,
                                "action": "add_custom",
                                "value": nValue,
                                "iso": iso
                            }, function(data){
                            displayMultipleFeaturesGroups();
                        });
                    }
                }
                return true;
            });

            displayMultipleFeaturesGroups();

            // VALUES
            prop_tb._multipleFeaturesLayoutGridB=prop_tb._multipleFeaturesLayout.cells('b').attachGrid();
            prop_tb._multipleFeaturesLayoutGridB.enableMultiselect(true);
            <?php if (isField('position', 'feature_product')) { ?>
            prop_tb._multipleFeaturesLayoutGridB.enableDragAndDrop(true);
            <?php } ?>
            prop_tb._multipleFeaturesLayoutGridB.setDragBehavior('child');

            prop_tb._multipleFeaturesLayoutGridB_tb=prop_tb._multipleFeaturesLayout.cells('b').attachToolbar();
            prop_tb._multipleFeaturesLayoutGridB_tb.setIconset('awesome');
            <?php if (isField('position', 'feature_product')) { ?>
            prop_tb._multipleFeaturesLayoutGridB_tb.addButton("save_position", 100, "", "fa fa-list-ol green", "fa fa-list-ol green");
            prop_tb._multipleFeaturesLayoutGridB_tb.setItemToolTip('save_position','<?php echo _l('Save positions for the selected products', 1); ?>');
            <?php } ?>
            prop_tb._multipleFeaturesLayoutGridB_tb.addButton("add_select", 100, "", "fad fa-link yellow", "fad fa-link yellow");
            prop_tb._multipleFeaturesLayoutGridB_tb.setItemToolTip('add_select','<?php echo _l('Add all the selected products to all the selected values', 1); ?>');
            prop_tb._multipleFeaturesLayoutGridB_tb.addButton("del_select", 100, "", "fad fa-unlink red", "fad fa-unlink red");
            prop_tb._multipleFeaturesLayoutGridB_tb.setItemToolTip('del_select','<?php echo _l('Delete all the selected products to all the selected values', 1); ?>');
            prop_tb._multipleFeaturesLayoutGridB_tb.addButtonTwoState('filter', 0, "", "fa fa-filter", "fa fa-filter");
            prop_tb._multipleFeaturesLayoutGridB_tb.setItemToolTip('filter','<?php echo _l('Display only features used by products in the same category'); ?>');
            prop_tb._multipleFeaturesLayoutGridB_tb.addButton("refresh", 0, "", "fa fa-sync green", "fa fa-sync green");
            prop_tb._multipleFeaturesLayoutGridB_tb.setItemToolTip('refresh','<?php echo _l('Refresh grid'); ?>');
            prop_tb._multipleFeaturesLayoutGridB_tb.attachEvent("onClick", function(id)
            {
                if (id=='refresh')
                {
                    displayMultipleFeaturesValues();
                }
                if (id=='add_select')
                {
                    $.post("index.php?ajax=1&act=cat_multiplefeatures_value_relation_update&id_feature="+multiplefeatures_id_group+"&value=1&action=mass_used&id_feature_values="+prop_tb._multipleFeaturesLayoutGridB.getSelectedRowId()+"&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{product_list:cat_grid.getSelectedRowId()},function(data)
                    {
                        let rId = getlastIdOfList(prop_tb._multipleFeaturesLayoutGridB.getSelectedRowId());
                        displayMultipleFeaturesValues(rId);
                    });
                }
                if (id=='del_select')
                {
                    $.post("index.php?ajax=1&act=cat_multiplefeatures_value_relation_update&id_feature="+multiplefeatures_id_group+"&value=0&action=mass_used&id_feature_values="+prop_tb._multipleFeaturesLayoutGridB.getSelectedRowId()+"&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{product_list:cat_grid.getSelectedRowId()},function(data){
                        let rId = getlastIdOfList(prop_tb._multipleFeaturesLayoutGridB.getSelectedRowId());
                        displayMultipleFeaturesValues(rId);
                    });
                }
                if (id=='save_position')
                {
                    var positions = new Array();

                    prop_tb._multipleFeaturesLayoutGridB.forEachRow(function(rId)
                    {
                        var rowIndex=prop_tb._multipleFeaturesLayoutGridB.getRowIndex(rId);
                        positions[rowIndex] = rId;
                    });

                    $.post("index.php?ajax=1&act=cat_multiplefeatures_value_relation_update&id_feature="+multiplefeatures_id_group+"&action=position&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{positions: positions,product_list:cat_grid.getSelectedRowId()},function(data){
                        displayMultipleFeaturesValues();
                    });
                }
            });
            prop_tb._multipleFeaturesLayoutGridB_tb.attachEvent("onStateChange", function(id, state)
            {
                if (id=='filter')
                {
                    multiplefeaturesValuesFilter=Number(state);
                    displayMultipleFeaturesValues();
                }
            });

            // EVENTS
            prop_tb._multipleFeaturesLayoutGridA.attachEvent("onRowSelect",function(idfeature)
            {
                multiplefeatures_id_group = idfeature;
                displayMultipleFeaturesValues();
            });
            prop_tb._multipleFeaturesLayoutGridB.attachEvent("onEditCell",function(stage,rId,cInd,nValue,oValue)
            {
                idxUsed=prop_tb._multipleFeaturesLayoutGridB.getColIndexById('used');
                if (cInd == idxUsed)
                {
                    if(stage==1)
                    {
                        $.post("index.php?ajax=1&act=cat_multiplefeatures_value_relation_update&id_feature="+multiplefeatures_id_group+"&id_feature_value="+rId+"&position="+prop_tb._multipleFeaturesLayoutGridB.getRowIndex(rId)+"&action=update&value="+prop_tb._multipleFeaturesLayoutGridB.cells(rId,idxUsed).getValue()+"&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{product_list:cat_grid.getSelectedRowId()},function(data){
                            prop_tb._multipleFeaturesLayoutGridA_tb.callEvent("onClick",["refresh"]);
                            displayMultipleFeaturesValues(rId);
                        });
                    }
                }
                return true;
            });

            propertiesPanel='multiplefeatures';
        }
    }
    prop_tb.attachEvent("onClick", setPropertiesPanel_multiplefeatures);

    function displayMultipleFeaturesGroups()
    {
        let selectedRowHolder = prop_tb._multipleFeaturesLayoutGridA.getSelectedRowId();
        prop_tb._multipleFeaturesLayoutGridA.post("index.php?ajax=1&act=cat_multiplefeatures_group_get&id_lang="+SC_ID_LANG+"&filter="+multiplefeaturesFilter +"&id_category="+catselection,"product_list="+cat_grid.getSelectedRowId(), function(){
            prop_tb._multipleFeaturesLayoutGridA.selectRowById(selectedRowHolder);
        });
    }


    function displayMultipleFeaturesValues(scrollToRid=null)
    {
        $.post("index.php?ajax=1&act=cat_multiplefeatures_value_get&id_feature="+multiplefeatures_id_group+"&filter="+multiplefeaturesValuesFilter+"&id_category="+catselection+"&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{"product_list":cat_grid.getSelectedRowId()},function(data){
            prop_tb._multipleFeaturesLayoutGridB.parse(data);
            if(scrollToRid!==null){
                prop_tb._multipleFeaturesLayoutGridB.showRow(scrollToRid);
            }
        });
    }

    function getlastIdOfList(list)
    {
        if(list !== null){
            let tmp = list.split(',');
            let lastId = Number(tmp[tmp.length-1]);
            if(!isNaN(lastId)){
                return lastId;
            }
        } else {
            return list
        }
    }

    let multiplefeatures_current_id = 0;
    cat_grid.attachEvent("onRowSelect",function (idproduct){
        if (propertiesPanel=='multiplefeatures' && (cat_grid.getSelectedRowId()!==null && multiplefeatures_current_id!=idproduct)){
            displayMultipleFeaturesGroups();
            displayMultipleFeaturesValues();
            multiplefeatures_current_id=idproduct;
        }
    });

<?php
}
