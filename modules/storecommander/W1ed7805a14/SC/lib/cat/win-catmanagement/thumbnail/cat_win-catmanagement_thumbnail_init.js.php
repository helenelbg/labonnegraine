// INITIALISATION TOOLBAR
cat_prop_tb.addListOption('cat_prop_subproperties', 'cat_prop_thumbnail', 3, "button", '<?php echo _l('Image thumbnail', 1); ?>', "fad fa-image");


cat_prop_tb.attachEvent("onClick", function(id){
    if(id=="cat_prop_thumbnail")
    {
        hideCatManagementSubpropertiesItems();
        cat_prop_tb.setItemText('cat_prop_subproperties', '<?php echo _l('Image thumbnail', 1); ?>');
        cat_prop_tb.setItemImage('cat_prop_subproperties', 'fad fa-image');
        actual_catmanagement_subproperties = "cat_prop_thumbnail";
        initCatManagementPropThumbnail();
    }
});
                
cat_treegrid_grid.attachEvent("onRowSelect", function(id,ind){
    if (!dhxlCatManagement.cells('b').isCollapsed())
    {
        if(actual_catmanagement_subproperties == "cat_prop_thumbnail"){
            cat_prop_thumbnail.cells('b').collapse();
             getCatManagementPropThumbnail();
        }
    }
});
        
cat_prop_tb.addButton('cat_prop_thumbnail_refresh',100,'','fa fa-sync green','fa fa-sync green');
cat_prop_tb.setItemToolTip('cat_prop_thumbnail_refresh','<?php echo _l('Refresh grid', 1); ?>');
cat_prop_tb.addButton("cat_prop_thumbnail_add", 100, "", "fa fa-plus-circle green", "fa fa-plus-circle green");
cat_prop_tb.setItemToolTip('cat_prop_thumbnail_add','<?php echo _l('Add file', 1); ?>');
cat_prop_tb.addButton("cat_prop_thumbnail_delete", 100, "", "fa fa-minus-circle red", "fa fa-minus-circle red");
cat_prop_tb.setItemToolTip('cat_prop_thumbnail_delete','<?php echo _l('Delete file', 1); ?>');
hideCatManagementSubpropertiesItems();

cat_prop_tb.attachEvent("onClick", function(id){
    if (id=='cat_prop_thumbnail_refresh')
    {
        getCatManagementPropThumbnail();
    }
    if (id=='cat_prop_thumbnail_add')
    {
        var ids = cat_treegrid_grid.getSelectedRowId();
        if(ids!=null)
        {
            cat_prop_thumbnail.cells('b').expand();
            cat_prop_thumbnail.cells('b').attachURL("index.php?ajax=1&act=cat_win-catmanagement_thumbnail_upload&ids="+ids+"&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),function(data){});
        }
    }
    if (id=='cat_prop_thumbnail_delete')
    {
        if (cat_prop_thumbnail_grid.getSelectedRowId()==null)
        {
            alert('<?php echo _l('Please select an image', 1); ?>');
        }else{
        if (confirm('<?php echo _l('Are you sure you want to delete the selected images?', 1); ?>'))
            {
                $.post("index.php?ajax=1&act=cat_win-catmanagement_thumbnail_delete&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{"ids":cat_prop_thumbnail_grid.getSelectedRowId()},function(data){
                    getCatManagementPropThumbnail();
                });
            }
        }
    }
});

// FUNCTIONS
var cat_prop_thumbnail = null;
function initCatManagementPropThumbnail()
{
    cat_prop_tb.showItem('cat_prop_thumbnail_refresh');
    cat_prop_tb.showItem('cat_prop_thumbnail_add');
    cat_prop_tb.showItem('cat_prop_thumbnail_delete');
    
    cat_prop_thumbnail = dhxlCatManagement.cells('b').attachLayout("2E");
    dhxlCatManagement.cells('b').showHeader();
    
    // GRID
    cat_prop_thumbnail.cells('a').hideHeader();

    cat_prop_thumbnail_grid = cat_prop_thumbnail.cells('a').attachGrid();
    cat_prop_thumbnail_grid.setImagePath("lib/js/imgs/");
    cat_prop_thumbnail_grid.enableDragAndDrop(false);
    cat_prop_thumbnail_grid.enableMultiselect(true);

    // UISettings
    cat_prop_thumbnail_grid._uisettings_prefix='cat_prop_thumbnail_grid';
    cat_prop_thumbnail_grid._uisettings_name=cat_prop_thumbnail_grid._uisettings_prefix;
    cat_prop_thumbnail_grid._first_loading=1;

    // UISettings
    initGridUISettings(cat_prop_thumbnail_grid);

    getCatManagementPropThumbnail();
    
    // UPLOAD
    cat_prop_thumbnail.cells('b').setText('<?php echo _l('Upload file', 1); ?>');
    cat_prop_thumbnail.cells('b').collapse();
}

function getCatManagementPropThumbnail()
{
    cat_prop_thumbnail_grid.clearAll(true);
    var tempIdList = (cat_treegrid_grid.getSelectedRowId()!=null?cat_treegrid_grid.getSelectedRowId():"");
    $.post("index.php?ajax=1&act=cat_win-catmanagement_thumbnail_get&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{'idlist': tempIdList},function(data)
    {
        cat_prop_thumbnail_grid.parse(data);
        // UISettings
        loadGridUISettings(cat_prop_thumbnail_grid);
        cat_prop_thumbnail_grid._first_loading=0;
    });
}