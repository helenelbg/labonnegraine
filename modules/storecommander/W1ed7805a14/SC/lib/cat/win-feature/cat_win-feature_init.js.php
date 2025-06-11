<?php echo '<script type="text/javascript">'; ?>

    dhxlFeatures=wFeatures.attachLayout("2U");
    wFeatures._sb=dhxlFeatures.attachStatusBar();
    dhxlFeatures.cells('a').setText("<?php echo _l('Features'); ?>");
    wFeatures.tbFeatures=dhxlFeatures.cells('a').attachToolbar();
    wFeatures.tbFeatures.setIconset('awesome');
    <?php if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) { ?>
    wFeatures.tbFeatures.addButton("feature_setposition", 100, "", "fa fa-list-ol green", "fa fa-list-ol grey");
    wFeatures.tbFeatures.setItemToolTip('feature_setposition','<?php echo _l('Save features positions', 1); ?>');
    <?php } ?>
    wFeatures.tbFeatures.addButton("del_feature", 0, "", "fa fa-minus-circle red", "fa fa-minus-circle red");
    wFeatures.tbFeatures.setItemToolTip('del_feature','<?php echo _l('Delete selected features and their values'); ?>');
    wFeatures.tbFeatures.addButton("duplicate_feature", 0, "", "fa fa-copy blue", "fa fa-copy blue");
    wFeatures.tbFeatures.setItemToolTip('duplicate_feature','<?php echo _l('Duplicate selected features'); ?>');
    wFeatures.tbFeatures.addButton("add_feature", 0, "", "fa fa-plus-circle green", "fa fa-plus-circle green");
    wFeatures.tbFeatures.setItemToolTip('add_feature','<?php echo _l('Create a new feature'); ?>');
    wFeatures.tbFeatures.addButton("exportcsv_features",100, "", "fad fa-file-csv green", "fad fa-file-csv green");
    wFeatures.tbFeatures.setItemToolTip('exportcsv_features','<?php echo _l('Export grid to clipboard in CSV format for MSExcel with tab delimiter.'); ?>');
    if (lightNavigation)
    {
        wFeatures.tbFeatures.addButtonTwoState('lightNavigation', 0, "", "fa fa-mouse-pointer", "fa fa-mouse-pointer");
        wFeatures.tbFeatures.setItemToolTip('lightNavigation','<?php echo _l('Light navigation (simple click on grid)', 1); ?>');
    }
    wFeatures.tbFeatures.addButton("refresh", 0, "", "fa fa-sync green", "fa fa-sync green");
    wFeatures.tbFeatures.setItemToolTip('refresh','<?php echo _l('Refresh grid'); ?>');
    wFeatures.tbFeatures.attachEvent("onClick",
        function(id){
            if (id=='refresh')
            {
                displayFeaturesList();
            }
            if (id=='add_feature')
            {
                var newId = new Date().getTime();
                wFeatures.gridFeatures.addRow(newId,[newId,"new"]);
            }
            if (id=='duplicate_feature')
            {
                if (wFeatures.gridFeatures.getSelectedRowId() && confirm('<?php echo _l('Are you sure to duplicate the selected features and their values?', 1); ?>'))
                    $.post("index.php?ajax=1&act=cat_win-feature_update",{'features':wFeatures.gridFeatures.getSelectedRowId(),'id_lang':SC_ID_LANG,'!nativeeditor_status':'duplicated'},function(data){displayFeaturesList();});
            }
            if (id=='del_feature')
            {
                if (confirm('<?php echo _l('Are you sure you want to delete the selected items?', 1); ?>'))
                {
                    wFeatures.gridFeatures.deleteSelectedRows();
                    displayFValues();
                }
            }
            if (id=='feature_setposition'){
                let confirm_save = confirm('<?php echo _l('Do you really want to save the feature positions?'); ?>');
                if(confirm_save) {
                    if (wFeatures.gridFeatures.getRowsNum() > 0) {
                        var positions = '';
                        var idx = 0;
                        var i = 1;
                        wFeatures.gridFeatures.forEachRow(function (id) {
                            positions += id + ',' + wFeatures.gridFeatures.getRowIndex(id) + ';';
                            idx++;
                        });
                        $.post("index.php?ajax=1&act=cat_win-feature_update&action=position&" + new Date().getTime(), {positions: positions}, function () {
                            displayFeaturesList();
                        });
                    }
                }
            }
            if (id=='exportcsv_features')
            {
                displayQuickExportWindow(wFeatures.gridFeatures, 1);
            }
        });
    wFeatures.gridFeatures=dhxlFeatures.cells('a').attachGrid();
    wFeatures.gridFeatures.setImagePath("lib/js/imgs/");
    wFeatures.gridFeatures.enableMultiselect(true);
    <?php if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) { ?>
    wFeatures.gridFeatures.enableDragAndDrop(true);
    <?php } ?>
    
    // UISettings
    wFeatures.gridFeatures._uisettings_prefix='cat_win-feature';
    wFeatures.gridFeatures._uisettings_name=wFeatures.gridFeatures._uisettings_prefix;
    wFeatures.gridFeatures._first_loading=1;
    
    // UISettings
    initGridUISettings(wFeatures.gridFeatures);
    
    wFeatures.gridFeatures.attachEvent("onEditCell", function(stage, rId, cIn){
            if (stage==1 && this.editor && this.editor.obj) this.editor.obj.select();
            return true;
        });
    featuresDataProcessorURLBase="index.php?ajax=1&act=cat_win-feature_update&id_lang="+SC_ID_LANG;
    featuresDataProcessor = new dataProcessor(featuresDataProcessorURLBase);
    featuresDataProcessor.enableDataNames(true);
    featuresDataProcessor.enablePartialDataSend(true);
    featuresDataProcessor.setUpdateMode('cell');
    featuresDataProcessor.attachEvent("onAfterUpdate",function(sid,action,tid,xml_node){
            if (action=='insert')
            {
                wFeatures.gridFeatures.cells(tid,0).setValue(tid);
            }
            return true;
        });
    featuresDataProcessor.init(wFeatures.gridFeatures);

// PROPERTIES
    dhxlFeatures.cells('b').setText("<?php echo _l('Feature values'); ?>");
    wFeatures.tbAttr=dhxlFeatures.cells('b').attachToolbar();
    wFeatures.tbAttr.setIconset('awesome');

    actual_winfeature_subproperties = "win_feat_prop_featurevalues";

    var opts = new Array();
    wFeatures.tbAttr.addButtonSelect("win_feat_prop_subproperties", 0, "<?php echo _l('Feature values'); ?>", opts, "fad fa-align-left", "fa fa-search blue",false,true);

    <?php
        $current_prop = 'win-feature';
        @$sub_files = scandir(SC_DIR.'lib/cat/'.$current_prop);
        foreach ($sub_files as $sub_item)
        {
            if ($sub_item != '.' && $sub_item != '..')
            {
                if (is_dir(SC_DIR.'lib/cat/'.$current_prop.'/'.$sub_item) && file_exists(SC_DIR.'lib/cat/'.$current_prop.'/'.$sub_item.'/cat_'.$current_prop.'_'.$sub_item.'_init.js.php'))
                {
                    require_once SC_DIR.'lib/cat/'.$current_prop.'/'.$sub_item.'/cat_'.$current_prop.'_'.$sub_item.'_init.js.php';
                }
                elseif (is_dir(SC_DIR.'lib/cat/'.$current_prop.'/'.$sub_item) && file_exists(SC_DIR.'lib/cat/'.$current_prop.'/'.$sub_item.'/cat_'.$current_prop.'_'.$sub_item.'_init.php'))
                {
                    require_once SC_DIR.'lib/cat/'.$current_prop.'/'.$sub_item.'/cat_'.$current_prop.'_'.$sub_item.'_init.php';
                }
            }
        }
    ?>
    displayFeaturesList();
    initWinFeaturePropFeatureValues();

//#####################################
//############ Load functions
//#####################################

function hideWinFeatureSubpropertiesItems()
{
    wFeatures.tbAttr.forEachItem(function(itemId){
        if(itemId!="win_feat_prop_subproperties")
            wFeatures.tbAttr.hideItem(itemId);
    });
}

function displayFeaturesList()
{
    wFeatures.gridFeatures.clearAll(true);
    wFeatures.gridFeatures.load("index.php?ajax=1&act=cat_win-feature_get&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),function()
    {
        nb=wFeatures.gridFeatures.getRowsNum();
        wFeatures._sb.setText(nb+(nb>1?" <?php echo _l('features'); ?>":" <?php echo _l('feature'); ?>"));
    // UISettings
        loadGridUISettings(wFeatures.gridFeatures);
        wFeatures.gridFeatures._first_loading=0;
    });
}
<?php echo '</script>'; ?>