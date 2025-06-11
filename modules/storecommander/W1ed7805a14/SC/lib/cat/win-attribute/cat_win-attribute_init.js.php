<?php echo '<script type="text/javascript">'; ?>
    dhxlAttributes=wAttributes.attachLayout("2U");
    wAttributes._sb=dhxlAttributes.attachStatusBar();
    dhxlAttributes.cells('a').setText("<?php echo _l('Groups'); ?>");
    wAttributes.tbGroups=dhxlAttributes.cells('a').attachToolbar();
    wAttributes.tbGroups.setIconset('awesome');

    wAttributes.tbGroups.addButton("show_all_groups", 0, "", "fad fa-bolt green", "fad fa-bolt green");
    wAttributes.tbGroups.setItemToolTip('show_all_groups','<?php echo _l('Show all attribute groups', 1); ?>');

    <?php if (_r('ACT_CAT_ADD_PRODUCT_COMBI')) { ?>
    wAttributes.tbGroups.addButton("create_combination", 0, "<?php echo _l('Create new combination', 1); ?>", "fa fa-magic yellow", "fa fa-magic yellow");
    wAttributes.tbGroups.setItemToolTip('create_combination','<?php echo _l('Create new combination with the selected groups', 1); ?>');
    <?php } ?>
    <?php if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) { ?>
        wAttributes.tbGroups.addButton("attr_group_setposition", 100, "", "fa fa-list-ol green", "fa fa-list-ol grey");
        wAttributes.tbGroups.setItemToolTip('attr_group_setposition','<?php echo _l('Save positions', 1); ?>');
    <?php } ?>
    wAttributes.tbGroups.addButton("del_group", 0, "", "fa fa-minus-circle red", "fa fa-minus-circle red");
    wAttributes.tbGroups.setItemToolTip('del_group','<?php echo _l('Delete group, all attributes and all combinations using this group', 1); ?>');
    wAttributes.tbGroups.addButton("duplicate_group", 0, "", "fa fa-copy blue", "fa fa-copy blue");
    wAttributes.tbGroups.setItemToolTip('duplicate_group','<?php echo _l('Duplicate selected groups and their attributes', 1); ?>');
    wAttributes.tbGroups.addButton("add_group", 0, "", "fa fa-plus-circle green", "fa fa-plus-circle green");
    wAttributes.tbGroups.setItemToolTip('add_group','<?php echo _l('Create a new group', 1); ?>');
    if (lightNavigation)
    {
        wAttributes.tbGroups.addButtonTwoState('lightNavigation', 0, "", "fa fa-mouse-pointer", "fa fa-mouse-pointer");
        wAttributes.tbGroups.setItemToolTip('lightNavigation','<?php echo _l('Light navigation (simple click on grid)', 1); ?>');
    }
    wAttributes.tbGroups.addButton("refresh", 0, "", "fa fa-sync green", "fa fa-sync green");
    wAttributes.tbGroups.setItemToolTip('refresh','<?php echo _l('Refresh grid', 1); ?>');
    wAttributes.tbGroups.attachEvent("onClick",
        function(id){
            if (id=='refresh')
            {
                displayGroups();
            }
            if (id=='add_group')
            {
                var newId = new Date().getTime();
                wAttributes.gridGroups.addRow(newId,[newId,"0","",""]);
            }
            if (id=='duplicate_group')
            {
                if (wAttributes.gridGroups.getSelectedRowId() && confirm('<?php echo _l('Are you sure to duplicate the selected groups and their attributes?', 1); ?>'))
                    $.post("index.php?ajax=1&act=cat_win-attribute_attributevalues_update&id_lang="+SC_ID_LANG,{'groups':wAttributes.gridGroups.getSelectedRowId(),'!nativeeditor_status':'duplicated'},function(data){displayGroups();});
            }
            if (id=='del_group')
            {
                if (confirm('<?php echo _l('Are you sure you want to delete the selected items?', 1); ?>'))
                {
                    wAttributes.gridGroups.deleteSelectedRows();
                    wAttributes.gridAttributes.clearAll(true);
                }
            }
            if (id=='attr_group_setposition'){
                if (wAttributes.gridGroups.getRowsNum()>0)
                {
                    var positions='';
                    var idx=0;
                    var i = 1 ;
                    wAttributes.gridGroups.forEachRow(function(id){
                            positions+=id+','+wAttributes.gridGroups.getRowIndex(id)+';';
                            idx++;
                        });
                    $.post("index.php?ajax=1&act=cat_win-attribute_group_update&action=position&"+new Date().getTime(),{ positions: positions },function(){
                        displayGroups();
                    });
                }
            }
            if (id=='create_combination')
            {
                error='';
                if (propertiesPanel!='combinations') error+="-<?php echo _l('The combination panel is displayed.'); ?>\n";
                if (lastProductSelID==0) error+="-<?php echo _l('A product is selected.'); ?>\n";
                if (prop_tb._combinationsGrid.getRowsNum()>0) error+="-<?php echo _l('No combinations already exist for the selected product.'); ?>\n";
                if (wAttributes.gridGroups.getSelectedRowId()==null) error+="-<?php echo _l('At least one group is selected.'); ?>\n";
                if (error=='')
                {
                    displayCombinations('prop_tb.callEvent("onClick",["force_combi_add"]);',wAttributes.gridGroups.getSelectedRowId());
                    wAttributes.hide();
                }else{
                    error="<?php echo _l('To create a new combination, check that:'); ?>\n"+error;
                    alert(error);
                }
            }

            if(id=='show_all_groups') {
                displayGroups(1);
            }

        });
    wAttributes.gridGroups=dhxlAttributes.cells('a').attachGrid();
    wAttributes.gridGroups._name='groups';
    wAttributes.gridGroups.setImagePath("lib/js/imgs/");
    wAttributes.gridGroups.enableMultiselect(true);
    wAttributes.gridGroups.enableSmartRendering(true);

    wAttributes.gridGroups.attachEvent("onFilterEnd", function(elements) {
        var attr_group_filter_params = "";
        var nb_cols = wAttributes.gridGroups.getColumnsNum();
        if(nb_cols>0)
        {
            for(var i=0; i<nb_cols; i++) {
                if(wAttributes.gridGroups.getFilterElement(i)!=null){
                    var colValue = wAttributes.gridGroups.getFilterElement(i).value;
                    var colID = wAttributes.gridGroups.getColumnId(i);
                    if((colValue!=null && colValue!=""))
                    {
                        if(attr_group_filter_params!="")
                            attr_group_filter_params = attr_group_filter_params + ",";
                        attr_group_filter_params = attr_group_filter_params + colID+"|||"+colValue;
                    }
                }
            }
        }
        if(attr_group_filter_params!="")
        {
            displayGroups(0,attr_group_filter_params);
        }
    });

    // UISettings
    wAttributes.gridGroups._uisettings_prefix='cat_win-attribute_group';
    wAttributes.gridGroups._uisettings_name=wAttributes.gridGroups._uisettings_prefix;
    wAttributes.gridGroups._first_loading=1;

    // UISettings
    initGridUISettings(wAttributes.gridGroups);

    wAttributes.gridGroups.attachEvent("onEditCell", function(stage, rId, cIn){
            if (stage==1 && this.editor && this.editor.obj) this.editor.obj.select();
            return true;
        });
    groupsDataProcessorURLBase="index.php?ajax=1&act=cat_win-attribute_group_update&id_lang="+SC_ID_LANG;
    groupsDataProcessor = new dataProcessor(groupsDataProcessorURLBase);
    groupsDataProcessor.enableDataNames(true);
    groupsDataProcessor.enablePartialDataSend(true);
    groupsDataProcessor.setUpdateMode('cell');
    groupsDataProcessor.attachEvent("onAfterUpdate",function(sid,action,tid,xml_node){
            if (action=='insert')
            {
                wAttributes.gridGroups.cells(tid,0).setValue(tid);
            }
            return true;
        });
    groupsDataProcessor.init(wAttributes.gridGroups);

    wAttributes.gridGroups.enableDragAndDrop(true);
    wAttributes.gridGroups.setDragBehavior("child");
    wAttributes.gridGroups.attachEvent("onDrag",function(sourceid,targetid,sourceobject,targetobject){
            if (sourceobject._name=='attributes' && targetid!=undefined && targetid!=null && targetid!=0)
            {
                var attributes=wAttributes.gridAttributes.getSelectedRowId();
                if (attributes==null && draggedAttribute!=0) attributes=draggedAttribute;
                $.post("index.php?ajax=1&act=cat_win-attribute_texture&action=duplicate&id_group="+targetid,{'attributes':attributes},function(data){
                    dhtmlx.message({text:'Error: '+data,type:'error'});
                });
                draggedAttribute=0;
            }
            <?php if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) { ?>
                if(sourceobject._name=='groups')
                    return true;
                else
                    return false;
            <?php }
else
{ ?>
                return false;
            <?php } ?>
        });

    lastGroupSelID=0;

    wAttributes.tbGroups.attachEvent("onStateChange",function(id,state){
        if (id=='lightNavigation')
        {
            if (state)
            {
                wAttributes.gridGroups.enableLightMouseNavigation(true);
            }else{
                wAttributes.gridGroups.enableLightMouseNavigation(false);
            }
        }
    });

// PROPERTIES
    dhxlAttributes.cells('b').setText("<?php echo _l('Attribute values'); ?>");
    wAttributes.tbAttr=dhxlAttributes.cells('b').attachToolbar();
    wAttributes.tbAttr.setIconset('awesome');

    actual_winattribute_subproperties = "win_attr_prop_attributevalues";

    var opts = new Array();
    wAttributes.tbAttr.addButtonSelect("win_attr_prop_subproperties", 0, "<?php echo _l('Attribute values'); ?>", opts, "fad fa-align-left", "fa fa-search blue",false,true);
    <?php
        $current_prop = 'win-attribute';
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

    displayGroups();
    initWinAttributePropAttributeValues();

//#####################################
//############ Load functions
//#####################################

function hideWinAttributeSubpropertiesItems()
{
    wAttributes.tbAttr.forEachItem(function(itemId){
        if(itemId!="win_attr_prop_subproperties")
            wAttributes.tbAttr.hideItem(itemId);
    });
}

function displayGroups(all_groups = '', params = '')
{
    wAttributes.gridGroups.clearAll(true);
    wAttributes.gridGroups.load("index.php?ajax=1&act=cat_win-attribute_group_get&id_lang="+SC_ID_LANG+"&all_groups="+all_groups+"&params="+params+"&"+new Date().getTime(),function()
    {
        nb=wAttributes.gridGroups.getRowsNum();
        wAttributes._sb.setText(nb+(nb>1?" <?php echo _l('groups'); ?>":" <?php echo _l('group'); ?>"));
        if(typeof AttrIdToOpen === "number") {
            wAttributes.gridGroups.selectRowById(AttrIdToOpen);
            lastGroupSelID = AttrIdToOpen;
            displayAttributes();
            AttrIdToOpen = null;
        }

        if(params != 'undefined' && params != '' && params != null) {
            oldFilters = {};
            var multi_params = params.split(',');
            multi_params.forEach(function(item){
                var data_params = item.split('|||');
                oldFilters[data_params[0]] = data_params[1];
                for(var i = 0 ; i < wAttributes.gridGroups.getColumnsNum() ; i++)
                {
                    if (wAttributes.gridGroups.getFilterElement(i)!=null && oldFilters[wAttributes.gridGroups.getColumnId(i)]!=undefined)
                    {
                        wAttributes.gridGroups.getFilterElement(i).value=oldFilters[wAttributes.gridGroups.getColumnId(i)];
                    }
                }
            });
        }

        // UISettings
        loadGridUISettings(wAttributes.gridGroups);
        wAttributes.gridGroups._first_loading=0;
    });
}
<?php echo '</script>'; ?>