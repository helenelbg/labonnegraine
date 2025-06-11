<?php

?>
<script type="text/javascript">

<?php if (!_r('GRI_CUSM_VIEW_CUSM')) { ?>
document.location.href="index.php";
<?php } ?>

    // Create interface
    var dhxLayout = new dhtmlXLayoutObject(document.body, "2U");
    dhxLayout.cells('a').setText('<?php echo _l('Discussions service', 1); ?>');
    dhxLayout.cells('b').setText('<?php echo _l('Properties', 1); ?>');
    var start_cusm_size_prop = getParamUISettings('start_cusm_size_prop');
    if(start_cusm_size_prop==null || start_cusm_size_prop<=0 || start_cusm_size_prop==="") {
        start_cusm_size_prop = 350;
    }
    dhxLayout.cells('b').setWidth(start_cusm_size_prop);
    dhxLayout.attachEvent("onPanelResizeFinish", function(){
        saveParamUISettings('start_cusm_size_prop', dhxLayout.cells('b').getWidth())
    });
    var dhxLayoutStatus = dhxLayout.attachStatusBar();
    layoutStatusText = "<?php echo SC_COPYRIGHT.' '.(SC_DEMO ? '- Demonstration' : '- '._l('License').' '.SCLIMREF.' - '.$SC_SHOP_PRODUCTSCOUNT.' ').' - Version '.SC_VERSION.(SC_BETA ? ' BETA' : '').' (PS '._PS_VERSION_.(defined('_PS_CACHE_ENABLED_') && _PS_CACHE_ENABLED_ ? ' (cache)' : '').') '.$NOTEPAD_BUTTON; ?>";
    dhxLayoutStatus.setText(layoutStatusText);
<?php
    createMenu();
?>
    cookie_selection = $.cookie('sc_cusm_filters_selected');
    if(cookie_selection!=null && cookie_selection!="" && cookie_selection!=0)
        filterselection=$.cookie('sc_cusm_filters_selected');
    else
    {
        filterselection = "st_open";
        $.cookie('sc_cusm_filters_selected',filterselection, {path: cookiePath});
    }
    shopselection=$.cookie('sc_shop_selected')*1;
    shop_list=$.cookie('sc_shop_list');
    lastDiscussionSelID=0;
    propertiesPanel='message';
    lastColumnRightClicked_Combi=0;
    clipboardValue=null;
    clipboardType=null;

<?php //#####################################
            //############ Categories toolbar
            //#####################################
    echo SCI::getShopUrlArrayJs();
    if (SCMS)
    {
        ?>
    
    cusm = new dhtmlXLayoutObject(dhxLayout.cells("a"), "2U");
    
    cusm_firstcolcontent = cusm.cells("a").attachLayout("3E");
    cusm_discussionPanel = cusm.cells('b');
    
    cusm_storePanel = cusm_firstcolcontent.cells('a');
    cusm_filterPanel = cusm_firstcolcontent.cells('b');
    cusm_statPanel = cusm_firstcolcontent.cells('c');
    
    cusm_storePanel.setText('<?php echo _l('Stores', 1); ?>');
    var start_cusm_size_store = getParamUISettings('start_cusm_size_store');
    if(start_cusm_size_store==null || start_cusm_size_store<=0 || start_cusm_size_store==="") {
        start_cusm_size_store = 200;
    }
    cusm_storePanel.setHeight(start_cusm_size_store);
    cusm_firstcolcontent.attachEvent("onPanelResizeFinish", function(){
        saveParamUISettings('start_cusm_size_store', cusm_storePanel.getHeight())
    });
    cusm_shoptree=cusm_storePanel.attachTree();
    cusm_shoptree._name='shoptree';
    cusm_shoptree.autoScroll=false;
    cusm_shoptree.setImagePath('lib/js/imgs/dhxtree_material/');
    cusm_shoptree.enableSmartXMLParsing(true);
//    cusm_shoptree.enableCheckBoxes(true, false);

    var cusmShoptreeTB = cusm_storePanel.attachToolbar();
      cusmShoptreeTB.setIconset('awesome');
    cusmShoptreeTB.addButton("help", 0, "", "fad fa-question-circle blue", "fad fa-question-circle blue");
    cusmShoptreeTB.setItemToolTip('help','<?php echo _l('Help'); ?>');
    cusmShoptreeTB.attachEvent("onClick", function(id) {
        if (id=='help')
        {
            var display = "";
            var update = "";
            if(shopselection>0)
            {
                display = cusm_shoptree.getItemText(shopselection);
            }
            else if(shopselection==0)
            {
                display = cusm_shoptree.getItemText("all");
            }

            
            var msg = '<strong><?php echo addslashes(_l('Display:')); ?></strong> '+display+'<br/><br/><strong><?php echo addslashes(_l('Update:')); ?></strong> '+update;
            dhtmlx.message({text:msg,type:'info',expire:10000});
        }
    });
    
    
    displayShopTree();

    function displayShopTree(callback) {
        cusm_shoptree.deleteChildItems(0);
        cusm_shoptree.load("index.php?ajax=1&act=cusm_shop_get&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),function(){

                if (shopselection!=null && shopselection!=undefined && shopselection!=0)
                {
                    cusm_shoptree.openItem(shopselection);
                    cusm_shoptree.selectItem(shopselection,true);
                }
                
                if (callback!='') eval(callback);
                cusm_shoptree.openAllItems(0);
            });
    }
    cusm_shoptree.attachEvent("onClick",function(idshop){
        if (idshop[0]=='G'){
            cusm_shoptree.clearSelection();
            cusm_shoptree.selectItem(shopselection,false);
            return false;
        }
        if (idshop == 'all'){
            idshop = 0;
        }

        if (idshop != shopselection)
        {

            shopselection = idshop;
            $.cookie('sc_shop_selected',shopselection, { expires: 60 , path: cookiePath});
            cusm_statPanel.attachURL("index.php?ajax=1&act=cusm_statsget&id_lang="+SC_ID_LANG+"&"+new Date().getTime());
            displayFilters('displayDiscussions()');
        }

    });

<?php
    }
    else
    {
        ?>
    cusm = new dhtmlXLayoutObject(dhxLayout.cells("a"), "2U");
    
    cusm_firstcolcontent = cusm.cells("a").attachLayout("2E");
    cusm_discussionPanel = cusm.cells('b');
    
    cusm_filterPanel = cusm_firstcolcontent.cells('a');
    cusm_statPanel = cusm_firstcolcontent.cells('b');
    
<?php
    }
?>
    var start_cusm_size_stats = getParamUISettings('start_cusm_size_stats');
    if(start_cusm_size_stats==null || start_cusm_size_stats<=0 || start_cusm_size_stats==="") {
        start_cusm_size_stats = 180;
    }
    cusm_statPanel.setHeight(start_cusm_size_stats);
    cusm_statPanel.attachEvent("onPanelResizeFinish", function(){
        saveParamUISettings('start_cusm_size_stats', cusm_statPanel.getHeight())
    });
    var start_cusm_size_tree = getParamUISettings('start_cusm_size_tree');
    if(start_cusm_size_tree==null || start_cusm_size_tree<=0 || start_cusm_size_tree==="") {
        start_cusm_size_tree = 300;
    }
    <?php if (SCMS) { ?>
        cusm.cells("a").setWidth(start_cusm_size_tree);
        cusm.attachEvent("onPanelResizeFinish", function(){
            saveParamUISettings('start_cusm_size_tree', cusm.cells("a").getWidth())
        }); 
    <?php }
else
{ ?>
        cusm_filterPanel.setWidth(getParamUISettings('start_cusm_size_tree'));
        cusm.attachEvent("onPanelResizeFinish", function(){
            saveParamUISettings('start_cusm_size_tree',cusm_filterPanel.getWidth())
        });
    <?php }

            //#####################################
            //############ filters tree
            //#####################################
?>

    cusm_filter=cusm_filterPanel.attachTree();
    cusm_filter._name='filter';
    cusm_filterPanel.setText('<?php echo _l('Filters', 1); ?>');
    cusm_filter.autoScroll=false;
    cusm_filter.setImagePath('lib/js/imgs/dhxtree_material/');
    cusm_filter.enableCheckBoxes(true);
    cusm_filter.enableThreeStateCheckboxes(true);
    
    
<?php //#####################################
            //############ Events
            //#####################################
?>

<?php if (SCSG) { ?>
    var id_selected_segment = 0;
    cusm_filter.enableDragAndDrop(true);
    cusm_filter.enableDragAndDropScrolling(true);

    cusm_filter.attachEvent("onDragIn",function doOnDragIn(idSource,idTarget,sourceobject,targetobject){
        var is_segment = cusm_filter.getUserData(idSource,"is_segment");
        var in_segment = cusm_filter.getUserData(idTarget,"is_segment");
        if(sourceobject._name=='filter')
             return false;

        // Si produit est déplacé dans segment
        // mais celui-ci n'accepte pas l'ajout manuel de produits
        var manuel_add = cusm_filter.getUserData(idTarget,"manuel_add");
        if(sourceobject._name=='grid' && in_segment==1 && manuel_add==1)
            return true;
        return false;
    });
    cusm_filter.attachEvent("onDrop",function doOnDrop(idSource,idTarget,idBefore,sourceobject,targetTree){
        var is_segment = cusm_filter.getUserData(idTarget,"is_segment");
        if(sourceobject._name=='filter' && is_segment==1)
             return false;
    });
    cusm_filter.attachEvent("onBeforeDrag",function(sourceid){
         return false;    
    });
    cusm_filter.attachEvent("onDrag",function(sourceid,targetid,sibling,sourceobject,targetobject){
        var is_segment = cusm_filter.getUserData(sourceid,"is_segment");
        var in_segment = cusm_filter.getUserData(targetid,"is_segment");
        
        if (sourceobject._name=='grid')
        {
            var manuel_add = cusm_filter.getUserData(targetid,"manuel_add");

            // Si ce n'est pas un segment et qu'il est déplacé dans un segment (client dans un segment)
            // et accepte l'ajout manuel de produits
            if(is_segment!=1 && in_segment==1 && manuel_add==1)
            {
                $.post("index.php?ajax=1&act=cusm_segment_dropproductonsegment&mode=move&id_lang="+SC_ID_LANG,{'segmentTarget':targetid,'discussions':sourceid},function(){});
            }
            return false;
        }
        else
             return false;
    });


    // Context menu for grid
    cusm_tree_cmenu=new dhtmlXMenuObject();
    cusm_tree_cmenu.renderAsContextMenu();
    var lastColumnRightClicked_CusTree = null;
    function onCusTreeContextButtonClick(itemId){
        tabId=cusm_filter.contextID.split('_');
        tabId=tabId[0];
        if (itemId=="open_segment"){
            tabId=cusm_filter.contextID;

            if (!dhxWins.isWindow("toolsSegmentationWindow"))
            {
                toolsSegmentationWindow = dhxWins.createWindow("toolsSegmentationWindow", 50, 50, $(window).width()-100, $(window).height()-100);
                toolsSegmentationWindow.setText("Segmentation");
                toolsSegmentationWindow.attachEvent("onClose", function(win){
                        toolsSegmentationWindow.hide();
                        return false;
                    });
                $.get("index.php?ajax=1&act=all_win-segmentation_init&selectedSegmentId="+tabId.replace("seg_",""),function(data){
                        $('#jsExecute').html(data);
                    });
                
            }else{
                $.get("index.php?ajax=1&act=all_win-segmentation_init&selectedSegmentId="+tabId.replace("seg_",""),function(data){
                        $('#jsExecute').html(data);
                    });
                toolsSegmentationWindow.show();
            }
        }
    }
    cusm_tree_cmenu.attachEvent("onClick", onCusTreeContextButtonClick);
    var contextMenuXML='<menu absolutePosition="auto" mode="popup" maxItems="8"  globalCss="contextMenu" globalSecondCss="contextMenu" globalTextCss="contextMenuItem">'+
            '<item text="Object" id="object" enabled="false"/>'+
            '<item text="<?php echo _l('Properties'); ?>" id="open_segment"/>'+
        '</menu>';
    cusm_tree_cmenu.loadStruct(contextMenuXML);
    cusm_filter.enableContextMenu(cusm_tree_cmenu);

    cusm_filter.attachEvent("onBeforeContextMenu", function(itemId){
        var is_segment = cusm_filter.getUserData(itemId,"is_segment");
        if(is_segment==1)
        {
            cusm_tree_cmenu.setItemText('object', '<?php echo _l('Segment:'); ?> '+cusm_filter.getItemText(itemId));                            
            return true;
        }
        else
            return false;
    });
<?php } ?>
    
    cusm_filter.attachEvent("onCheck",function(idfilter, state){
            if(idfilter == 'from_to')
            {
                periodselection=idfilter;
                $.cookie('sc_cusm_periodselection',periodselection, { expires: 60, path: cookiePath });

                if (dhxWins.isWindow("wCusmFilterFromTo"))
                    wCusmFilterFromTo.close();

                wCusmFilterFromTo = dhxWins.createWindow("wCusmFilterFromTo", 170, 150, 340, 400);
                wCusmFilterFromTo.denyPark();
                wCusmFilterFromTo.denyResize();
                wCusmFilterFromTo.setText('<?php echo _l('Select the date interval to filter', 1); ?>');
                $.get("index.php?ajax=1&act=cusm_filter_dates",function(data){
                    $('#jsExecute').html(data);
                });
            }
            filterselection=cusm_filter.getAllChecked();
            $.cookie('sc_cusm_filters_selected', filterselection, {path: cookiePath});
            id_selected_segment = null;
            displayDiscussions();
        });
    cusm_filter.attachEvent("onClick",function(idfilter){
            var is_segment = cusm_filter.getUserData(idfilter,"is_segment");
    
            if(is_segment!="1")
            {
                state=cusm_filter.isItemChecked(idfilter);
                cusm_filter.setCheck(idfilter,!state);
                filterselection=cusm_filter.getAllChecked();
                $.cookie('sc_cusm_filters_selected', filterselection, {path: cookiePath});
                cusm_filter.clearSelection();
            }
            else
            {
                if(id_selected_segment === idfilter){
                    cusm_filter.clearSelection(idfilter);
                    id_selected_segment = 0;
                } else {
                    id_selected_segment = idfilter;
                }
            }
            displayDiscussions();
        });

    displayFilters('displayDiscussions()');


<?php //#####################################
            //############ stats
            //#####################################
?>
        cusm_statPanel.setText('<?php echo _l('Global statistics', 1); ?>');
        cusm_statPanel.attachURL("index.php?ajax=1&act=cusm_statsget&id_lang="+SC_ID_LANG+"&"+new Date().getTime());

<?php //#####################################
            //############ Display
            //#####################################
?>

    function displayFilters(callback)
    {
        cusm_filter.deleteChildItems(0);
        cusm_filter.load("index.php?ajax=1&act=cusm_filter_get&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),function(){
                if(filterselection!=undefined && filterselection!=null && filterselection!="" && filterselection!=0)
                {
                    var filters = filterselection.split(",");
                    $.each(filters, function(index, filter) {
                        cusm_filter.setCheck(filter,1);
                    });
                }
            
                if (callback!='') eval(callback);
            });
    }
</script>
