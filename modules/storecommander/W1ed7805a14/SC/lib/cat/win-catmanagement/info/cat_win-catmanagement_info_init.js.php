// INITIALISATION TOOLBAR
cat_prop_tb.addListOption('cat_prop_subproperties', 'cat_prop_info', 1, "button", '<?php echo _l('Name & description', 1); ?>', "fad fa-align-left");

cat_prop_tb.attachEvent("onClick", function(id){
    if(id=="cat_prop_info")
    {
        hideCatManagementSubpropertiesItems();
        cat_prop_tb.setItemText('cat_prop_subproperties', '<?php echo _l('Name & description', 1); ?>');
        cat_prop_tb.setItemImage('cat_prop_subproperties', 'fad fa-align-left');
        actual_catmanagement_subproperties = "cat_prop_info";
        initCatManagementPropInfo();
    }
});

cat_treegrid_grid.attachEvent("onRowSelect", function(id,ind){
<?php if (version_compare(_PS_VERSION_, '8.0.0', '>=')) { ?>
    if(actual_catmanagement_subproperties == "cat_prop_info"){
        getCatManagementPropInfo();
    }
<?php }
else
{ ?>
    if (!dhxlCatManagement.cells('b').isCollapsed())
    {
        if(actual_catmanagement_subproperties == "cat_prop_info"){
            cat_prop_info.cells('b').collapse();
             getCatManagementPropInfo();
        }
    }
<?php } ?>
});

cat_prop_tb.addButton('cat_prop_info_refresh',100,'','fa fa-sync green','fa fa-sync green');
cat_prop_tb.setItemToolTip('cat_prop_info_refresh','<?php echo _l('Refresh grid', 1); ?>');
if (lightNavigation)
{
    cat_prop_tb.addButtonTwoState('cat_prop_info_lightNavigation', 100, "", "fa fa-mouse-pointer", "fa fa-mouse-pointer");
    cat_prop_tb.setItemToolTip('cat_prop_info_lightNavigation','<?php echo _l('Light navigation (simple click on grid)', 1); ?>');
}
cat_prop_tb.addButton('cat_prop_info_selectall',100,'','fa fa-bolt yellow','fa fa-bolt yellow');
cat_prop_tb.setItemToolTip('cat_prop_info_selectall','<?php echo _l('Select all', 1); ?>');
hideCatManagementSubpropertiesItems();

cat_prop_tb.attachEvent("onClick", function(id){
    if (id=='cat_prop_info_refresh')
    {
        getCatManagementPropInfo();
    }
    if (id=='cat_prop_info_selectall')
    {
        cat_prop_info_grid.selectAll();
    }
});


    cat_prop_tb.attachEvent("onStateChange",function(id,state){
        if (id=='cat_prop_info_lightNavigation')
        {
            if (state)
            {
                cat_prop_info_grid.enableLightMouseNavigation(true);
            }else{
                cat_prop_info_grid.enableLightMouseNavigation(false);
            }
        }
    });

// FUNCTIONS
var cat_prop_info = null;
var clipboardType_CatPropInfo = null;
function initCatManagementPropInfo()
{
    cat_prop_tb.showItem('cat_prop_info_refresh');
    cat_prop_tb.showItem('cat_prop_info_selectall');
    cat_prop_tb.showItem('cat_prop_info_lightNavigation');
    cat_prop_info = dhxlCatManagement.cells('b').attachLayout("<?php echo version_compare(_PS_VERSION_, '8.0.0', '>=') ? '3T' : '2E'; ?>");
    dhxlCatManagement.cells('b').showHeader();

    // GRID
        cat_prop_info.cells('a').hideHeader();

        cat_prop_info_grid = cat_prop_info.cells('a').attachGrid();
        cat_prop_info_grid.setImagePath("lib/js/imgs/");
          cat_prop_info_grid.enableDragAndDrop(false);
        cat_prop_info_grid.enableMultiselect(true);

        // UISettings
        cat_prop_info_grid._uisettings_prefix='cat_prop_info_grid';
        cat_prop_info_grid._uisettings_name=cat_prop_info_grid._uisettings_prefix;
        cat_prop_info_grid._first_loading=1;

        // UISettings
        initGridUISettings(cat_prop_info_grid);

        getCatManagementPropInfo();

         cat_prop_info_grid.attachEvent("onEditCell", function(stage,rId,cInd,nValue,oValue){
            idxDescription=cat_treegrid_grid.getColIndexById('description');

            if(stage==0 || stage==1)
            {
                if(idxDescription==cInd)
                     return false;
            }
            return true;
        });

        cat_prop_info_DataProcessorURLBase="index.php?ajax=1&act=cat_win-catmanagement_info_update&id_lang="+SC_ID_LANG;
        cat_prop_info_DataProcessor = new dataProcessor(cat_prop_info_DataProcessorURLBase);
        cat_prop_info_DataProcessor.enableDataNames(true);
        cat_prop_info_DataProcessor.setTransactionMode("POST");
        cat_prop_info_DataProcessor.enablePartialDataSend(true);
        cat_prop_info_DataProcessorURLBase="index.php?ajax=1&act=cat_win-catmanagement_info_update&id_lang="+SC_ID_LANG;
        cat_prop_info_DataProcessor.serverProcessor=cat_prop_info_DataProcessorURLBase;
        cat_prop_info_DataProcessor.init(cat_prop_info_grid);


        // Context menu for grid
        cat_prop_info_cmenu=new dhtmlXMenuObject();
        cat_prop_info_cmenu.renderAsContextMenu();
        function onGridCatPropInfoContextButtonClick(itemId){
            tabId=cat_prop_info_grid.contextID.split('_');
            tabId=tabId[0]+"_"+tabId[1]<?php if (SCMS) { ?>+"_"+tabId[2]<?php } ?>;
            if (itemId=="copy"){
                if (lastColumnRightClicked_CatPropInfo!=0)
                {
                    clipboardValue_CatPropInfo=cat_prop_info_grid.cells(tabId,lastColumnRightClicked_CatPropInfo).getValue();
                    cat_prop_info_cmenu.setItemText('paste' , '<?php echo _l('Paste'); ?> '+cat_prop_info_grid.cells(tabId,lastColumnRightClicked_CatPropInfo).getTitle());
                    clipboardType_CatPropInfo=lastColumnRightClicked_CatPropInfo;
                }
            }
            if (itemId=="paste"){
                if (lastColumnRightClicked_CatPropInfo!=0 && clipboardValue_CatPropInfo!=null && clipboardType_CatPropInfo==lastColumnRightClicked_CatPropInfo)
                {
                    selection=cat_prop_info_grid.getSelectedRowId();
                    if (selection!='' && selection!=null)
                    {
                        selArray=selection.split(',');
                        for(i=0 ; i < selArray.length ; i++)
                        {
                            cat_prop_info_grid.cells(selArray[i],lastColumnRightClicked_CatPropInfo).setValue(clipboardValue_CatPropInfo);
                            cat_prop_info_grid.cells(selArray[i],lastColumnRightClicked_CatPropInfo).cell.wasChanged=true;
                            cat_prop_info_DataProcessor.setUpdated(selArray[i],true,"updated");
                        }
                    }
                }
            }
        }
        cat_prop_info_cmenu.attachEvent("onClick", onGridCatPropInfoContextButtonClick);
        var contextMenuXML='<menu absolutePosition="auto" mode="popup" maxItems="8"  globalCss="contextMenu" globalSecondCss="contextMenu" globalTextCss="contextMenuItem">'+
                '<item text="Object" id="object" enabled="false"/>'+
                '<item text="Object" id="object2" enabled="false"/>'+
                <?php if (SCMS) { ?>'<item text="Object" id="object3" enabled="false"/>'+<?php } ?>
                '<item text="<?php echo _l('Copy'); ?>" id="copy"/>'+
                '<item text="<?php echo _l('Paste'); ?>" id="paste"/>'+
            '</menu>';
        cat_prop_info_cmenu.loadStruct(contextMenuXML);
        cat_prop_info_grid.enableContextMenu(cat_prop_info_cmenu);

        cat_prop_info_grid.attachEvent("onBeforeContextMenu", function(rowid,colidx,grid){
            var enableOnCols=new Array(
                    cat_prop_info_grid.getColIndexById('name'),
                    cat_prop_info_grid.getColIndexById('description')
                    );
            if (!in_array(colidx,enableOnCols))
            {
                return false;
            }
            lastColumnRightClicked_CatPropInfo=colidx;
            cat_prop_info_cmenu.setItemText('object', '<?php echo _l('Category:'); ?> '+cat_prop_info_grid.cells(rowid,cat_prop_info_grid.getColIndexById('id_category')).getTitle());
            cat_prop_info_cmenu.setItemText('object2', '<?php echo _l('Lang:'); ?> '+cat_prop_info_grid.cells(rowid,cat_prop_info_grid.getColIndexById('lang')).getTitle());
            <?php if (SCMS) { ?>cat_prop_info_cmenu.setItemText('object3', '<?php echo _l('Shop:'); ?> '+cat_prop_info_grid.cells(rowid,cat_prop_info_grid.getColIndexById('shop')).getTitle());<?php } ?>
            if (lastColumnRightClicked_CatPropInfo==clipboardType_CatPropInfo)
            {
                cat_prop_info_cmenu.setItemEnabled('paste');
            }else{
                cat_prop_info_cmenu.setItemDisabled('paste');
            }
            return true;
        });

    // form description
        let cat_prop_description_cell = cat_prop_info.cells('b');
        cat_prop_description_cell.setText('<?php echo _l('Edit description', 1); ?>');
        cat_prop_description_cell.setHeight(320);
<?php if (version_compare(_PS_VERSION_, '8.0.0', '<')) { ?>
        cat_prop_description_cell.collapse();
<?php } ?>
        let cat_prop_description_cell_tb = cat_prop_description_cell.attachToolbar();
        cat_prop_description_cell_tb.setIconset('awesome');
        cat_prop_description_cell_tb.addButton('desc_save',100,'','fa fa-save blue','fa fa-save blue');
        cat_prop_description_cell_tb.setItemToolTip('desc_save','<?php echo _l('Save', 1); ?>');
        <?php if (SC_CREATIVE_ELEMENTS_ACTIVE){ ?>
        cat_prop_description_cell_tb.addButton('edit_description_with_ce', 100, "", "ce_editor_link", "ce_editor_link");
        cat_prop_description_cell_tb.setItemToolTip('edit_description_with_ce','<?php echo _l('Edit with CreativeElements', 1); ?>');
        <?php } ?>
        cat_prop_description_cell_tb.attachEvent("onClick", function(id){
            switch(id){
                case 'desc_save':
                    cat_prop_description_cell.getFrame().contentWindow.ajaxSave();
                    break;
                case 'edit_description_with_ce':
                    <?php if (SC_CREATIVE_ELEMENTS_ACTIVE){ ?>
                    openCreativeElements(cat_treegrid_grid.getSelectedRowId(), '<?php echo CE\UId::CATEGORY; ?>');
                    <?php } ?>
                    break;
            }
        });
<?php if (version_compare(_PS_VERSION_, '8.0.0', '>=')) { ?>
    // form additional description
        let cat_prop_additionalDescription_cell = cat_prop_info.cells('c');
        cat_prop_additionalDescription_cell.setText('<?php echo _l('Edit additional description', 1); ?>');
        cat_prop_additionalDescription_cell.setHeight(320);
        let cat_prop_additionalDescription_cell_tb = cat_prop_additionalDescription_cell.attachToolbar();
        cat_prop_additionalDescription_cell_tb.setIconset('awesome');
        cat_prop_additionalDescription_cell_tb.addButton('desc_save',100,'','fa fa-save blue','fa fa-save blue');
        cat_prop_additionalDescription_cell_tb.setItemToolTip('desc_save','<?php echo _l('Save', 1); ?>');
        <?php if (SC_CREATIVE_ELEMENTS_ACTIVE){ ?>
        cat_prop_additionalDescription_cell_tb.addButton('edit_description_with_ce', 100, "", "ce_editor_link", "ce_editor_link");
        cat_prop_additionalDescription_cell_tb.setItemToolTip('edit_description_with_ce','<?php echo _l('Edit with CreativeElements', 1); ?>');
        <?php } ?>
        cat_prop_additionalDescription_cell_tb.attachEvent("onClick", function(id){
            switch(id){
                case 'desc_save':
                    cat_prop_additionalDescription_cell.getFrame().contentWindow.ajaxSave();
                    break;
                case 'edit_description_with_ce':
                    <?php if (SC_CREATIVE_ELEMENTS_ACTIVE){ ?>
                    openCreativeElements(cat_treegrid_grid.getSelectedRowId(), '<?php echo CE\UId::CATEGORY; ?>');
                    <?php } ?>
                    break;
            }
        });
<?php } ?>
        cat_prop_info_grid.attachEvent("onRowSelect", function(id,ind){
            var ids_split = null;
            var ids = cat_prop_info_grid.getSelectedRowId();
            if(ids!=undefined && ids!=null && ids!=0)
                ids_split = ids.split(",");
            if(ids_split!=null && ids_split.length>1)
                dhtmlx.message({text:'<?php echo _l('To update description, you must select one row only.', 1); ?>',type:'error',expire:10000});
            else if(ids_split!=null)
            {
<?php if (version_compare(_PS_VERSION_, '8.0.0', '<')) { ?>
                cat_prop_description_cell.expand();
<?php } ?>
                cat_prop_description_cell.attachURL("index.php?ajax=1&act=cat_win-catmanagement_info_form",null,{field:'description',id_row:cat_prop_info_grid.getSelectedRowId()});

<?php if (version_compare(_PS_VERSION_, '8.0.0', '>=')) { ?>
                cat_prop_additionalDescription_cell.attachURL("index.php?ajax=1&act=cat_win-catmanagement_info_form",null,{field:'additional_description',id_row:cat_prop_info_grid.getSelectedRowId()});
<?php } ?>
            }
        });
}

function getCatManagementPropInfo()
{
    oldFilters=new Array();
    for(var i=0,l=cat_prop_info_grid.getColumnsNum();i<l;i++)
    {
        if (cat_prop_info_grid.getFilterElement(i)!=null && cat_prop_info_grid.getFilterElement(i).value!='') {
            oldFilters[cat_prop_info_grid.getColumnId(i)]=cat_prop_info_grid.getFilterElement(i).value;
        }
    }

    cat_prop_info_grid.clearAll(true);
        var tempIdList = (cat_treegrid_grid.getSelectedRowId()!=null?cat_treegrid_grid.getSelectedRowId():"");
        $.post("index.php?ajax=1&act=cat_win-catmanagement_info_get&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{'idlist': tempIdList},function(data)
        {
            cat_prop_info_grid.parse(data);

            for(var i=0;i<cat_prop_info_grid.getColumnsNum();i++)
            {
                if (cat_prop_info_grid.getFilterElement(i)!=null && oldFilters[cat_prop_info_grid.getColumnId(i)]!=undefined)
                {
                    cat_prop_info_grid.getFilterElement(i).value=oldFilters[cat_prop_info_grid.getColumnId(i)];
                }
            }
            cat_prop_info_grid.filterByAll();

            // UISettings
                loadGridUISettings(cat_prop_info_grid);
                cat_prop_info_grid._first_loading=0;
        });
}
