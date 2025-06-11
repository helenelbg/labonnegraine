<?php
if (SCMS)
{
    ?>
// INITIALISATION TOOLBAR
wFeatures.tbAttr.addListOption('win_feat_prop_subproperties', 'win_feat_prop_shopshare', 3, "button", '<?php echo _l('Multistore sharing manager', 1); ?>', "fa fa-layer-group");

wFeatures.tbAttr.attachEvent("onClick", function(id){
    if(id=="win_feat_prop_shopshare")
    {
        hideWinFeatureSubpropertiesItems();
        wFeatures.tbAttr.setItemText('win_feat_prop_subproperties', '<?php echo _l('Multistore sharing manager', 1); ?>');
        wFeatures.tbAttr.setItemImage('win_feat_prop_subproperties', 'fa fa-layer-group');
        actual_winfeature_subproperties = "win_feat_prop_shopshare";
        initWinFeaturePropShopShare();
    }
});
wFeatures.gridFeatures.attachEvent("onRowSelect", function(id,ind){
    if (!dhxlFeatures.cells('b').isCollapsed())
    {
        if(actual_winfeature_subproperties == "win_feat_prop_shopshare"){
             getWinFeaturePropShopShare();
        }
    }
});

wFeatures.tbAttr.addButton('win_feature_prop_shopshare_refresh',100,'','fa fa-sync green','fa fa-sync green');
wFeatures.tbAttr.setItemToolTip('win_feature_prop_shopshare_refresh','<?php echo _l('Refresh grid', 1); ?>');
wFeatures.tbAttr.addButton("win_feature_prop_shopshare_add_select", 100, "", "fad fa-link yellow", "fad fa-link yellow");
wFeatures.tbAttr.setItemToolTip('win_feature_prop_shopshare_add_select','<?php echo _l('Add all selected categories to all selected shops', 1); ?>');
wFeatures.tbAttr.addButton("win_feature_prop_shopshare_del_select", 100, "", "fad fa-unlink red", "fad fa-unlink red");
wFeatures.tbAttr.setItemToolTip('win_feature_prop_shopshare_del_select','<?php echo _l('Delete all selected categories from all selected shops', 1); ?>');
hideWinFeatureSubpropertiesItems();

wFeatures.tbAttr.attachEvent("onClick", function(id){
    if (id=='win_feature_prop_shopshare_refresh')
    {
        getWinFeaturePropShopShare();
    }
    if (id=='win_feature_prop_shopshare_add_select')
    {
        $.post("index.php?ajax=1&act=cat_win-feature_shopshare_update&action=mass_present&value=true&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{"idlist":wFeatures.gridFeatures.getSelectedRowId(),"id_shop":win_feature_prop_shopshare_grid.getSelectedRowId()},function(data){
            getWinFeaturePropShopShare();
        });
    }
    if (id=='win_feature_prop_shopshare_del_select')
    {
        $.post("index.php?ajax=1&act=cat_win-feature_shopshare_update&action=mass_present&value=false&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{"idlist":wFeatures.gridFeatures.getSelectedRowId(),"id_shop":win_feature_prop_shopshare_grid.getSelectedRowId()},function(data){
            var doDisplay = true;
            if(data!="noRefreshProduct")
            {
                var shops=win_feature_prop_shopshare_grid.getSelectedRowId().split(',');
                $.each( shops, function( num, shopId ) {
                    if(shopId==shopselection)
                    {
                        displayProducts('getWinFeaturePropShopShare()');
                        doDisplay = false;
                        return false;
                    }
                });
            }
            if(doDisplay==true)
                getWinFeaturePropShopShare();
        });
    }
});

// FUNCTIONS
var win_feature_prop_shopshare = null;
function initWinFeaturePropShopShare()
{
    wFeatures.tbAttr.showItem('win_feature_prop_shopshare_refresh');
    wFeatures.tbAttr.showItem('win_feature_prop_shopshare_add_select');
    wFeatures.tbAttr.showItem('win_feature_prop_shopshare_del_select');

    win_feature_prop_shopshare = dhxlFeatures.cells('b').attachLayout("1C");
    dhxlFeatures.cells('b').showHeader();

    // GRID
    win_feature_prop_shopshare.cells('a').hideHeader();

    win_feature_prop_shopshare_grid = win_feature_prop_shopshare.cells('a').attachGrid();
    win_feature_prop_shopshare_grid.setImagePath("lib/js/imgs/");
    win_feature_prop_shopshare_grid.enableDragAndDrop(false);
    win_feature_prop_shopshare_grid.enableMultiselect(true);

    // UISettings
    win_feature_prop_shopshare_grid._uisettings_prefix='win_feature_prop_shopshare_grid';
    win_feature_prop_shopshare_grid._uisettings_name=win_feature_prop_shopshare_grid._uisettings_prefix;
    win_feature_prop_shopshare_grid._first_loading=1;

    // UISettings
    initGridUISettings(win_feature_prop_shopshare_grid);

    getWinFeaturePropShopShare();

    win_feature_prop_shopshare_grid.attachEvent("onEditCell",function(stage,rId,cInd,nValue,oValue)
    {
        if(stage==1)
        {
            idxPresent=win_feature_prop_shopshare_grid.getColIndexById('present');

            var action = "";
            if(cInd==idxPresent)
                action = "present";

            if(action!="")
            {
                var value = win_feature_prop_shopshare_grid.cells(rId,cInd).isChecked();
                $.post("index.php?ajax=1&act=cat_win-feature_shopshare_update&id_shop="+rId+"&action="+action+"&value="+value+"&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{"idlist":wFeatures.gridFeatures.getSelectedRowId()},function(data){
                    getWinFeaturePropShopShare();
                });
            }
        }
        return true;
    });
}

function getWinFeaturePropShopShare()
{
    win_feature_prop_shopshare_grid.clearAll();
    var tempIdList = (wFeatures.gridFeatures.getSelectedRowId()!=null?wFeatures.gridFeatures.getSelectedRowId():"");
    $.post("index.php?ajax=1&act=cat_win-feature_shopshare_get&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{'idlist': tempIdList},function(data)
    {
        win_feature_prop_shopshare_grid.parse(data);
        // UISettings
        loadGridUISettings(win_feature_prop_shopshare_grid);
        win_feature_prop_shopshare_grid._first_loading=0;
    });
}
<?php
} ?>