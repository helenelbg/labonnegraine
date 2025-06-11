// INITIALISATION TOOLBAR
cat_prop_tb.addListOption('cat_prop_subproperties', 'cat_prop_image', 2, "button", '<?php echo _l('Image', 1); ?>', "fad fa-image");


cat_prop_tb.attachEvent("onClick", function(id){
    if(id=="cat_prop_image")
    {
        hideCatManagementSubpropertiesItems();
        cat_prop_tb.setItemText('cat_prop_subproperties', '<?php echo _l('Image', 1); ?>');
        cat_prop_tb.setItemImage('cat_prop_subproperties', 'fad fa-image');
        actual_catmanagement_subproperties = "cat_prop_image";
        initCatManagementPropImage();
    }
});
                
cat_treegrid_grid.attachEvent("onRowSelect", function(id,ind){
    if (!dhxlCatManagement.cells('b').isCollapsed())
    {
        if(actual_catmanagement_subproperties == "cat_prop_image"){
            cat_prop_image.cells('b').collapse();
             getCatManagementPropImage();
        }
    }
});
        
cat_prop_tb.addButton('cat_prop_image_refresh',100,'','fa fa-sync green','fa fa-sync green');
cat_prop_tb.setItemToolTip('cat_prop_image_refresh','<?php echo _l('Refresh grid', 1); ?>');
cat_prop_tb.addButton("cat_prop_image_add", 100, "", "fa fa-plus-circle green", "fa fa-plus-circle green");
cat_prop_tb.setItemToolTip('cat_prop_image_add','<?php echo _l('Add file', 1); ?>');
cat_prop_tb.addButton("cat_prop_image_delete", 100, "", "fa fa-minus-circle red", "fa fa-minus-circle red");
cat_prop_tb.setItemToolTip('cat_prop_image_delete','<?php echo _l('Delete file', 1); ?>');
<?php if (version_compare(_PS_VERSION_, '1.5.0.5', '>=')) {?>
cat_prop_tb.addButton("thumbnail_regeneration", 100, "", "fad fa-tachometer-alt-fastest", "fad fa-tachometer-alt-fastest");
cat_prop_tb.setItemToolTip('thumbnail_regeneration','<?php echo _l('Regenerate thumbnails from image selection', 1); ?>');
<?php } ?>

hideCatManagementSubpropertiesItems();

cat_prop_tb.attachEvent("onClick", function(id){
    if (id=='cat_prop_image_refresh')
    {
        getCatManagementPropImage();
    }
    if (id=='cat_prop_image_add')
    {
        var ids = cat_treegrid_grid.getSelectedRowId();
        if(ids!=null)
        {
            cat_prop_image.cells('b').expand();
            
            cat_prop_image.cells('b').attachURL("index.php?ajax=1&act=cat_win-catmanagement_image_upload&ids="+ids+"&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),function(data){});
        }
    }
    if (id=='cat_prop_image_delete')
    {
        if (cat_prop_image_grid.getSelectedRowId()==null)
        {
            alert('<?php echo _l('Please select an image', 1); ?>');
        }else{
        if (confirm('<?php echo _l('Are you sure you want to delete the selected images?', 1); ?>'))
            {
                $.post("index.php?ajax=1&act=cat_win-catmanagement_image_delete&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{"ids":cat_treegrid_grid.getSelectedRowId()},function(data){
                    getCatManagementPropImage();
                });
            }
        }
    }
    if (id == 'thumbnail_regeneration') {
        let imagelist = cat_prop_image_grid.getSelectedRowId();
        if (imagelist !== null) {
            dhxLayout.cells('b').progressOn();
            $.post("index.php?ajax=1&act=cat_win-catmanagement_image_update", {
                'action': id,
                'list_id_image': imagelist
            }, function (response) {
                dhxLayout.cells('b').progressOff();
                let parsed_response = JSON.parse(response);
                if (parsed_response.success == 'OK') {
                    dhtmlx.message({
                        text: '<?php echo _l('Thumbnails successfully regenerated.', 1); ?>',
                        type: 'success',
                        expire: 5000
                    });
                    getCatManagementPropImage();
                } else {
                    dhtmlx.message({text: parsed_response.error, type: 'error', expire: 10000});
                }
            });
        }
    }
});

// FUNCTIONS
var cat_prop_image = null;
var clipboardType_CatPropImage = null;
function initCatManagementPropImage()
{
    cat_prop_tb.showItem('cat_prop_image_refresh');
    cat_prop_tb.showItem('cat_prop_image_add');
    cat_prop_tb.showItem('cat_prop_image_delete');
    cat_prop_tb.showItem('thumbnail_regeneration');
    
    cat_prop_image = dhxlCatManagement.cells('b').attachLayout("2E");
    dhxlCatManagement.cells('b').showHeader();
    
    // GRID
        cat_prop_image.cells('a').hideHeader();
        
        cat_prop_image_grid = cat_prop_image.cells('a').attachGrid();
        cat_prop_image_grid.setImagePath("lib/js/imgs/");
          cat_prop_image_grid.enableDragAndDrop(false);
        cat_prop_image_grid.enableMultiselect(true);
    
        // UISettings
        cat_prop_image_grid._uisettings_prefix='cat_prop_image_grid';
        cat_prop_image_grid._uisettings_name=cat_prop_image_grid._uisettings_prefix;
        cat_prop_image_grid._first_loading=1;
                   
        // UISettings
        initGridUISettings(cat_prop_image_grid);
        
        getCatManagementPropImage();
    
    // UPLOAD
        cat_prop_image.cells('b').setText('<?php echo _l('Upload file', 1); ?>');
        cat_prop_image.cells('b').collapse();
}

function getCatManagementPropImage()
{
    cat_prop_image_grid.clearAll(true);
        var tempIdList = (cat_treegrid_grid.getSelectedRowId()!=null?cat_treegrid_grid.getSelectedRowId():"");
        $.post("index.php?ajax=1&act=cat_win-catmanagement_image_get&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{'idlist': tempIdList},function(data)
        {
            cat_prop_image_grid.parse(data);
                
            // UISettings
                loadGridUISettings(cat_prop_image_grid);
                cat_prop_image_grid._first_loading=0;
        });
}