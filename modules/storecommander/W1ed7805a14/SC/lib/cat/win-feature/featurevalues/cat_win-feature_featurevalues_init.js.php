// INITIALISATION TOOLBAR
<?php $propname = _l('Feature values', 1); ?>
wFeatures.tbAttr.addListOption('win_feat_prop_subproperties', 'win_feat_prop_featurevalues', 1, "button", '<?php echo $propname; ?>', "fad fa-align-left");

wFeatures.tbAttr.attachEvent("onClick", function(id){
    if(id=="win_feat_prop_featurevalues")
    {
        hideWinFeatureSubpropertiesItems();
        wFeatures.tbAttr.setItemText('win_feat_prop_subproperties', '<?php echo $propname; ?>');
        wFeatures.tbAttr.setItemImage('win_feat_prop_subproperties', 'fad fa-align-left');
        actual_winfeature_subproperties = "win_feat_prop_featurevalues";
        initWinFeaturePropFeatureValues();
        displayFValues();
    }
});

wFeatures.gridFeatures.attachEvent("onRowSelect", function(id,ind){
    if (!dhxlFeatures.cells('b').isCollapsed())
    {
        if(actual_winfeature_subproperties == "win_feat_prop_featurevalues"){
            lastFeatureSelID = id;
             displayFValues();
        }
    }
});

wFeatures.tbAttr.addButton("refresh", 100, "", "fa fa-sync green", "fa fa-sync green");
wFeatures.tbAttr.setItemToolTip('refresh','<?php echo _l('Refresh grid'); ?>');
wFeatures.tbAttr.addInput("add_input", 100,"1",30);
wFeatures.tbAttr.setItemToolTip('add_input','<?php echo _l('Number of values to create when clicking on the Create button'); ?>');
wFeatures.tbAttr.addButton("add_attr", 100, "", "fa fa-plus-circle green", "fa fa-plus-circle green");
wFeatures.tbAttr.setItemToolTip('add_attr','<?php echo _l('Create new feature value'); ?>');
wFeatures.tbAttr.addButton("del_attr", 100, "", "fa fa-minus-circle red", "fa fa-minus-circle red");
wFeatures.tbAttr.setItemToolTip('del_attr','<?php echo _l('Delete selected values'); ?>');
wFeatures.tbAttr.addButton("merge_feat", 100, "", "fad fa-bring-front blue", "fad fa-bring-front blue");
wFeatures.tbAttr.setItemToolTip('merge_feat','<?php echo _l('Merge selected Features', 1); ?>');
wFeatures.tbAttr.addButton("exportcsv_features_value",100, "", "fad fa-file-csv green", "fad fa-file-csv green");
wFeatures.tbAttr.setItemToolTip('exportcsv_features_value','<?php echo _l('Export grid to clipboard in CSV format for MSExcel with tab delimiter.'); ?>');
wFeatures.tbAttr.addButton('select_all_featurevalues', 100, "", "fa fa-bolt yellow", "fa fa-bolt yellow");
wFeatures.tbAttr.setItemToolTip('select_all_featurevalues', '<?php echo _l('Select all values', 1); ?>');

if (lightNavigation)
{
    wFeatures.tbAttr.addButtonTwoState('lightNavigation', 100, "", "fa fa-mouse-pointer", "fa fa-mouse-pointer");
    wFeatures.tbAttr.setItemToolTip('lightNavigation','<?php echo _l('Light navigation (simple click on grid)', 1); ?>');
}

hideWinFeatureSubpropertiesItems();


function initWinFeaturePropFeatureValues()
{
    wFeatures.tbAttr.showItem('exportcsv_features_value');
    wFeatures.tbAttr.showItem('merge_feat');
    wFeatures.tbAttr.showItem('del_attr');
    wFeatures.tbAttr.showItem('add_attr');
    wFeatures.tbAttr.showItem('add_input');
    wFeatures.tbAttr.showItem('refresh');
    wFeatures.tbAttr.showItem('select_all_featurevalues');

    wFeatures.gridFValues=dhxlFeatures.cells('b').attachGrid();
    wFeatures.gridFValues.setImagePath("lib/js/imgs/");

    // UISettings
    wFeatures.gridFValues._uisettings_prefix='cat_win-feature_value';
    wFeatures.gridFValues._uisettings_name=wFeatures.gridFValues._uisettings_prefix;
    wFeatures.gridFValues._first_loading=1;

    // UISettings
    initGridUISettings(wFeatures.gridFValues);

    wFeatures.gridFValues.attachEvent("onEditCell", function(stage, rId, cIn){
            if (stage==1 && this.editor && this.editor.obj) this.editor.obj.select();
            return true;
        });

    FValuesDataProcessorURLBase="index.php?ajax=1&act=cat_win-feature_featurevalues_update&id_lang="+SC_ID_LANG;
    FValuesDataProcessor = new dataProcessor(FValuesDataProcessorURLBase);
    FValuesDataProcessor.enableDataNames(true);
    FValuesDataProcessor.enablePartialDataSend(true);
    FValuesDataProcessor.setUpdateMode('cell');
    FValuesDataProcessor.attachEvent("onAfterUpdate",function(sid,action,tid,xml_node){
            if (action=='insert')
            {
                wFeatures.gridFValues.cells(tid,0).setValue(tid);
            }
            return true;
        });
    FValuesDataProcessor.init(wFeatures.gridFValues);

    lastFeatureSelID=0;

    wFeatures.tbAttr.attachEvent("onClick",function(id){
        if (id=='merge_feat')
        {
            if (wFeatures.gridFValues.getSelectedRowId()==null || wFeatures.gridFValues.getSelectedRowId().split(',').length<2)
            {
                alert('<?php echo _l('You must select at least two items', 1); ?>');
            }else if (confirm('<?php echo _l('Are you sure you want to merge the selected items?', 1); ?>'))
            {
                $.post("index.php?ajax=1&act=cat_win-feature_featurevalues_update&action=merge",{'featlist':wFeatures.gridFValues.getSelectedRowId()},function(data){
                    displayFValues(lastFeatureSelID);
                });
            }
        }
        if (id=='refresh')
        {
            displayFValues(lastFeatureSelID);
        }
        if (id=='add_attr')
        {
            if (lastFeatureSelID!=0)
            {
                var newId = new Date().getTime();
                nb=wFeatures.tbAttr.getValue('add_input');
                if (isNaN(nb)) nb=1;
                for (i=1;i<=nb;i++)
                {
                    col2data="";
                    if (wFeatures.gridFeatures.cells(lastFeatureSelID,1).getValue()==1) col2data="#000000";
                    wFeatures.gridFValues.addRow(newId*100+i,[newId*100+i,col2data]);
                }
            }
        }
        if (id=='del_attr')
        {
            if (confirm('<?php echo _l('Are you sure you want to delete the selected items?', 1); ?>'))
                wFeatures.gridFValues.deleteSelectedRows();
        }
        if (id=='exportcsv_features_value')
        {
            displayQuickExportWindow(wFeatures.gridFValues, 1);
        }
        if (id=="select_all_featurevalues"){
            wFeatures.gridFValues.selectAll();
        }
    });

    wFeatures.tbFeatures.attachEvent("onStateChange",function(id,state){
        if (id=='lightNavigation')
        {
            if (state)
            {
                wFeatures.gridFeatures.enableLightMouseNavigation(true);
            }else{
                wFeatures.gridFeatures.enableLightMouseNavigation(false);
            }
        }
    });

    wFeatures.tbAttr.attachEvent("onStateChange",function(id,state){
        if (id=='lightNavigation')
        {
            if (state)
            {
                wFeatures.gridFValues.enableLightMouseNavigation(true);
            }else{
                wFeatures.gridFValues.enableLightMouseNavigation(false);
            }
        }
    });
}

function getWinFeaturePropFeatureValues()
{
    oldFilters=new Array();
    for(var i=0,l=win_feat_prop_featurevalues_grid.getColumnsNum();i<l;i++)
    {
        if (win_feat_prop_featurevalues_grid.getFilterElement(i)!=null && win_feat_prop_featurevalues_grid.getFilterElement(i).value!='') {
            oldFilters[win_feat_prop_featurevalues_grid.getColumnId(i)]=win_feat_prop_featurevalues_grid.getFilterElement(i).value;
        }
    }

    win_feat_prop_featurevalues_grid.clearAll(true);
    var tempIdList = (wFeatures.gridFeatures.getSelectedRowId()!=null?wFeatures.gridFeatures.getSelectedRowId():"");
    $.post("index.php?ajax=1&act=cat_win-catmanagement_info_get&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{'idlist': tempIdList},function(data)
    {
        win_feat_prop_featurevalues_grid.parse(data);

        for(var i=0;i<win_feat_prop_featurevalues_grid.getColumnsNum();i++)
        {
            if (win_feat_prop_featurevalues_grid.getFilterElement(i)!=null && oldFilters[win_feat_prop_featurevalues_grid.getColumnId(i)]!=undefined)
            {
                win_feat_prop_featurevalues_grid.getFilterElement(i).value=oldFilters[win_feat_prop_featurevalues_grid.getColumnId(i)];
            }
        }
        win_feat_prop_featurevalues_grid.filterByAll();

        // UISettings
        loadGridUISettings(win_feat_prop_featurevalues_grid);
        win_feat_prop_featurevalues_grid._first_loading=0;
    });
}

function displayFValues()
{
    wFeatures.gridFValues.clearAll(true);
    if(lastFeatureSelID == 0) {
        let feaSelect = wFeatures.gridFeatures.getSelectedRowId();
        let featSelectArr = feaSelect.split(',');
        lastFeatureSelID = featSelectArr[0];
    }


    if (lastFeatureSelID!=0) {
        wFeatures.gridFValues.load("index.php?ajax=1&act=cat_win-feature_featurevalues_get&id_feature=" + lastFeatureSelID + "&id_lang=" + SC_ID_LANG + "&" + new Date().getTime(), function () {
            FValuesDataProcessor.serverProcessor = FValuesDataProcessorURLBase + "&id_feature=" + lastFeatureSelID;
            nb = wFeatures.gridFeatures.getRowsNum();
            nb2 = wFeatures.gridFValues.getRowsNum();
            wFeatures._sb.setText(nb + (nb > 1 ? " <?php echo _l('features'); ?>" : " <?php echo _l('feature'); ?>") + " / " + nb2 + (nb2 > 1 ? " <?php echo _l('feature values'); ?>" : " <?php echo _l('feature values'); ?>"));
            // UISettings
            loadGridUISettings(wFeatures.gridFValues);
            wFeatures.gridFValues._first_loading = 0;
        });
    }
}
