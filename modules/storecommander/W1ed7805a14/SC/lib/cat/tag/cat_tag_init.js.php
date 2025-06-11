<?php

if (_r('GRI_CAT_PROPERTIES_GRID_TAG')) { ?>
        prop_tb.addListOption('panel', 'tags', 8, "button", '<?php echo _l('Tags', 1); ?>', "fad fa-clouds");
        allowed_properties_panel[allowed_properties_panel.length] = "tags";
    <?php } ?>

    prop_tb.addButton("tag_refresh",1000, "", "fa fa-sync green", "fa fa-sync green");
    prop_tb.setItemToolTip('tag_refresh','<?php echo _l('Refresh grid', 1); ?>');
    prop_tb.addButtonTwoState('tag_lightNavigation', 1000, "", "fa fa-mouse-pointer", "fa fa-mouse-pointer");
    prop_tb.setItemToolTip('tag_lightNavigation','<?php echo _l('Light navigation (simple click on grid)', 1); ?>');
    prop_tb.addButtonTwoState("tag_filter", 1000, "", "fa fa-filter", "fa fa-filter");
    prop_tb.setItemToolTip('tag_filter','<?php echo _l('View only used tags in the same category', 1); ?>');
    prop_tb.addButton("tag_gotofo",1000, "", "fad fa-external-link green", "fad fa-external-link green");
    prop_tb.setItemToolTip('tag_gotofo','<?php echo _l('View products with the selected tag on front office', 1); ?>');
    prop_tb.addButton("tag_add",1000, "", "fa fa-plus-circle green", "fa fa-plus-circle green");
    prop_tb.setItemToolTip('tag_add','<?php echo _l('Add tags', 1); ?>');
    prop_tb.addButton("tag_del",1000, "", "fa fa-minus-circle red", "fa fa-minus-circle red");
    prop_tb.setItemToolTip('tag_del','<?php echo _l('Delete selected tags', 1); ?>');
    prop_tb.addButton("tag_add_select",1000, "", "fad fa-link yellow", "fad fa-link yellow");
    prop_tb.setItemToolTip('tag_add_select','<?php echo _l('Add link between selected tags and selected products', 1); ?>');
    prop_tb.addButton("tag_del_select",1000, "", "fad fa-unlink red", "fad fa-unlink red");
    prop_tb.setItemToolTip('tag_del_select','<?php echo _l('Delete link between selected tags and selected products', 1); ?>');
    getTbSettingsButton(prop_tb, {'grideditor':0,'settings':1}, 'prop_tag_',1000);

    var filter_params = "";
    var oldFilters = new Object();
    needinitTags = 1;
    function initTags(){
        if (needinitTags)
        {
            prop_tb._tagsLayout = dhxLayout.cells('b').attachLayout('1C');
            prop_tb._tagsLayout.cells('a').hideHeader();
            dhxLayout.cells('b').showHeader();
            lTag = prop_tb._tagsLayout;
            tag_grid=lTag.cells('a').attachGrid();
            tag_grid.setImagePath('lib/js/imgs/');
            tag_grid.enableSmartRendering(true);
            tag_grid.enableMultiselect(true);
            
            // UISettings
            tag_grid._uisettings_prefix='cat_tag';
            tag_grid._uisettings_name=tag_grid._uisettings_prefix;
               tag_grid._first_loading=1;
               
            // UISettings
            initGridUISettings(tag_grid);
            
            tagsFilter=0;
            tag_grid.attachEvent("onEditCell",function onEditCellTags(stage,rId,cInd,nValue,oValue){
                idxUsed=tag_grid.getColIndexById('used');
                if (cInd == idxUsed){
                    if(stage==1)
                        $.post("index.php?ajax=1&act=cat_tag_update&tag_list="+rId+"&action=update&value="+tag_grid.cells(rId,idxUsed).getValue()+"&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{"product_list":cat_grid.getSelectedRowId()},function(data){});
                }
                idxName=tag_grid.getColIndexById('name');
                if (cInd == idxName){
                    if (stage==2)
                        $.post("index.php?ajax=1&act=cat_tag_update&tag_list="+rId+"&action=updateName&value="+nValue+"&"+new Date().getTime(),function(data){});
                }
                idxLang=tag_grid.getColIndexById('lang');
                if (cInd == idxLang){
                    if (stage==2)
                        $.post("index.php?ajax=1&act=cat_tag_update&tag_list="+rId+"&action=updateLang&value="+nValue+"&"+new Date().getTime(),function(data){});
                }
                return true;
            });

            // dynamic filter
            tag_grid.attachEvent("onFilterEnd", function(elements)
            {
                var nb_cols = tag_grid.getColumnsNum();
                old_filter_params = filter_params;
                filter_params = "";
                if(nb_cols>0)
                {
                    for(var i=0; i<nb_cols; i++)
                    {
                        var colId=tag_grid.getColumnId(i);
                        if(tag_grid.getFilterElement(i)!=null && (colId =="name"))
                        {
                            var colValue = tag_grid.getFilterElement(i).value;
                            if((colValue!=null && colValue!="") || (oldFilters[i]!=null && oldFilters[i]!=""))
                            {
                                if(filter_params!="")
                                    filter_params = filter_params + ",";
                                filter_params = filter_params + colId+"|||"+colValue;
                                oldFilters[i] = tag_grid.getFilterElement(i).value;
                            }
                        }
                    }
                }
                if(filter_params!="" && filter_params!=old_filter_params)
                {
                    tag_grid._rowsNum = 0;
                    displayTags();
                }
            });
            needinitTags=0;
        }
    }


    function setPropertiesPanel_tags(id){
        if (id=='tags')
        {
            if(lastProductSelID!=undefined && lastProductSelID!="")
            {
                dhxLayout.cells('b').setText('<?php echo _l('Properties', 1).' '._l('of', 1); ?> '+getSelectedItemValueOrID(cat_grid,lastProductSelID,'name'));
            }
            hidePropTBButtons();
            prop_tb.showItem('tag_go_to_settings');
            prop_tb.showItem('tag_del_select');
            prop_tb.showItem('tag_add_select');
            prop_tb.showItem('tag_del');
            prop_tb.showItem('tag_add');
            prop_tb.showItem('tag_gotofo');
            prop_tb.showItem('tag_filter');
            prop_tb.showItem('tag_lightNavigation');
            prop_tb.showItem('tag_refresh');
            prop_tb.showItem('prop_tag_settings_menu');
            prop_tb.setItemText('panel', '<?php echo _l('Tags', 1); ?>');
            prop_tb.setItemImage('panel', 'fad fa-clouds');
            needinitTags=1;
            initTags();
            displayTags();
            propertiesPanel='tags';
        }
        if (id=='tag_refresh'){
            displayTags('',true);
        }
        if (id=='prop_tag_settings'){
            openSettingsWindow('Catalog','Interface','CAT_PROPERTIES_TAGS_LIMIT');
        }
        if (id=='tag_gotofo'){
            if (tag_grid.getSelectedRowId()){
                selTag=tag_grid.getSelectedRowId().split(',');
                idxName=tag_grid.getColIndexById('name');
                <?php if (version_compare(_PS_VERSION_, '1.6.0.0', '>='))
{
    $link = new Link();
    $url = $link->getPageLink('search'); ?>
                    window.open('<?php echo $url; ?>?tag='+tag_grid.cells(selTag[0],idxName).getValue());
                <?php
}
    else
    { ?>
                    window.open('<?php echo SC_PS_PATH_REL; ?>search.php?tag='+tag_grid.cells(selTag[0],idxName).getValue());
                <?php } ?>
            }
        }
        if (id=='tag_add_select'){
            $.post("index.php?ajax=1&act=cat_tag_update&action=addSeltag&"+new Date().getTime(),{"product_list":cat_grid.getSelectedRowId(), "tag_list":tag_grid.getSelectedRowId()},function(data){
                    displayTags('',true)
                });
        }
        if (id=='tag_del_select'){
            $.post("index.php?ajax=1&act=cat_tag_update&action=deleteSeltag&"+new Date().getTime(),{"product_list":cat_grid.getSelectedRowId(), "tag_list":tag_grid.getSelectedRowId()},function(data){
                    displayTags('',true);
                });
        }
        if (id=='tag_add'){
            if (!dhxWins.isWindow("wCatAddTag"))
            {
                wCatAddTag = dhxWins.createWindow("wCatAddTag", 50, 50, 450, 460);
                wCatAddTag.setText("<?php echo _l('Add tags'); ?>");
                ll = new dhtmlXLayoutObject(wCatAddTag, "1C");
                ll.cells('a').hideHeader();
                wCatAddTag.attachEvent("onClose", function(win){
                        wCatAddTag.hide();
                        displayTags('',true);
                        return false;
                    });
                ll.cells('a').appendObject('divAddTags');
                $('#divAddTags').css('display','block');
                wCatAddTag._add_prop_tb=wCatAddTag.attachToolbar();
                wCatAddTag._add_prop_tb.setIconset('awesome');
                // checked status
                wCatAddTag._add_prop_tb.addButtonTwoState("tag_checked", 0, "", "fa fa-link green", "fa fa-link green");
                wCatAddTag._add_prop_tb.setItemToolTip('tag_checked','<?php echo _l('Link these tags to selected products when created', 1); ?>');
                // save tag list
                wCatAddTag._add_prop_tb.addButton("tag_save", 0, "", "fa fa-save blue", "fa fa-save blue");
                wCatAddTag._add_prop_tb.setItemToolTip('tag_save','<?php echo _l('Create tags', 1); ?>');
                wCatAddTag._add_prop_tb.setItemState('tag_checked', 1);
                wCatAddTag._linkToProducts=1;
                // events
                wCatAddTag._add_prop_tb.attachEvent("onStateChange",function(id,state){
                        if (id=='tag_checked')
                        {
                            if (state){
                                wCatAddTag._linkToProducts=1;
                            }else{
                                wCatAddTag._linkToProducts=0;
                            }
                        }
                    });
                wCatAddTag._add_prop_tb.attachEvent("onClick",function(id){
                        if (id=="tag_save")
                        {
                            $.post("index.php?ajax=1&act=cat_tag_update&action=add&"+new Date().getTime(),{'value':$('.dhxwin_active #taAddTags').val(),"product_list":cat_grid.getSelectedRowId(),"linktoproduct":wCatAddTag._linkToProducts},function(data){
                                    wCatAddTag.hide();
                                    displayTags('',true);
                                });
                        }
                    });
            }else{
                wCatAddTag.show();
            }
        }
        if (id=='tag_del'){
            if (confirm('<?php echo _l('Are you sure you want to delete the selected items?', 1); ?>'))
                $.post("index.php?ajax=1&act=cat_tag_update&action=delete&"+new Date().getTime(),{"product_list":cat_grid.getSelectedRowId(), "tag_list":tag_grid.getSelectedRowId()},function(data){
                        tag_grid.deleteSelectedRows();
                    });
        }
        if (id=='prop_settings'){
            openSettingsWindow('Catalog','Interface','CAT_PROPERTIES_TAGS_LIMIT');
        }
    }
    prop_tb.attachEvent("onClick", setPropertiesPanel_tags);

    prop_tb.attachEvent("onStateChange",function(id,state){
        if (id=='tag_filter')
        {
            if (state){
                tagsFilter=1;
            }else{
                tagsFilter=0;
            }
            displayTags('',true);
        }
        if (id=='tag_lightNavigation')
        {
            if (state)
            {
                tag_grid.enableLightMouseNavigation(true);
            }else{
                tag_grid.enableLightMouseNavigation(false);
            }
        }
    });


function displayTags(callback,force_refresh)
    {
        if (tag_grid._rowsNum >0 && force_refresh!=true)
        {
            $.post("index.php?ajax=1&act=cat_tag_relation_get&tagsFilter="+tagsFilter+"&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{"product_list":cat_grid.getSelectedRowId(),'id_category':catselection},function(data){
                if (data!='')
                {
                    dataArray=data.split(',');
                    tag_grid.uncheckAll();
                    tag_grid.forEachRow(function(id){
                        if (in_array(id,dataArray))
                        {
                            tag_grid.cellById(id,1).setValue(1);
                        }
                    });
                }else{
                    tag_grid.uncheckAll();
                }

                // UISettings
                loadGridUISettings(tag_grid);

                // UISettings
                tag_grid._first_loading=0;

            });
        }else{
            if ((cat_grid.getSelectedRowId()==null || cat_grid.getSelectedRowId()=='') && force_refresh!=true) return false;
            tag_grid.clearAll(true);
            prop_tb._sb.setText('');
            prop_tb._sb.setText('<?php echo _l('Loading in progress, please wait...', 1); ?>');
            $.post("index.php?ajax=1&act=cat_tag_get&tagsFilter="+tagsFilter+"&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{'id_category':catselection,'filter_params':filter_params},function(data){
                if(data != '') {
                    tag_grid.parse(data);
                    nb=tag_grid.getRowsNum();
                    let buttonToLimitSetting = ' - <a style="color:#737373;cursor:pointer;" onclick="openSettingsWindow(\'Catalog\',\'Interface\',\'CAT_PROPERTIES_TAGS_LIMIT\');" href="javascript:void(0);"><?php echo _l('Update the display limit', 1); ?></a>';
                    prop_tb._sb.setText(nb+' '+(nb>1?'<?php echo _l('tags', 1); ?>':'<?php echo _l('tag', 1); ?>')+buttonToLimitSetting);
                    tag_grid._rowsNum=nb;
                    if (nb) displayTags();

                    // UISettings
                    loadGridUISettings(tag_grid);

                    // UISettings
                    tag_grid._first_loading=0;
                    if(filter_params !== '') {
                        let filters=filter_params.split('|||');
                        let column_name = filters[0];
                        let column_value = filters[1];
                        let idx_column = tag_grid.getColIndexById(column_name);
                        tag_grid.getFilterElement(idx_column).value = column_value;
                    }
                    filter_params ="";


                    if (callback!='') eval(callback);
                }
            });
        }
    }


    let tags_current_id = 0;
    cat_grid.attachEvent("onRowSelect",function (idproduct){
        if (propertiesPanel=='tags' && (cat_grid.getSelectedRowId()!==null && tags_current_id!=idproduct)){
            displayTags();
            tags_current_id=idproduct;
        }
    });

</script>
<div id="divAddTags" style="margin:10px;display:none;"><textarea id ="taAddTags" style="width:400px;height:200px"></textarea><br/><?php echo _l('<p>Example 1:with default language of the shop<br />blue<br />black</p><p>Example 2:<br />blue,fr<br />black,en</p>'); ?></div>
<script type="text/javascript">
