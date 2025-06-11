<script type="text/javascript">

    // Create interface
    var dhxLayout = new dhtmlXLayoutObject(document.body, "2U");
    dhxLayout.cells('a').setText('<?php echo _l('Catalog', 1).' '.addslashes(Configuration::get('PS_SHOP_NAME')); ?>');
    dhxLayout.cells('b').setText('<?php echo _l('Properties', 1); ?>');
    var start_cat_size_prop = getParamUISettings('start_cat_size_prop');
    if(start_cat_size_prop==null || start_cat_size_prop<=0 || start_cat_size_prop=="")
        start_cat_size_prop = 450;
    dhxLayout.cells('b').setWidth(start_cat_size_prop);
    dhxLayout.attachEvent("onPanelResizeFinish", function(){
        saveParamUISettings('start_cat_size_prop', dhxLayout.cells('b').getWidth())
    });
    var dhxLayoutStatus = dhxLayout.attachStatusBar();
    layoutStatusText = '<div id="layoutstatusqueue" style="float: right; color: #ff0000; font-weight: bold;">'+loader_gif+' <span></span></div>'+"<?php echo SC_COPYRIGHT.' '.(SC_DEMO ? '- Demonstration' : '- '._l('License').' '.SCLIMREF.' - '.$SC_SHOP_PRODUCTSCOUNT.' '._l('products')).' - Version '.SC_VERSION.(SC_BETA ? ' BETA' : '').(SC_GRIDSEDITOR_INSTALLED ? ' GE'.(SC_GRIDSEDITOR_PRO_INSTALLED ? 'P' : '') : '').' (PS '._PS_VERSION_.(defined('_PS_CACHE_ENABLED_') && _PS_CACHE_ENABLED_ ? ' (cache)' : '').' - PHP '.sc_phpversion().') '.$NOTEPAD_BUTTON.' <span id=\"layoutstatusloadingtime\"></span>'; ?>";
    dhxLayoutStatus.setText(layoutStatusText);

    var EcotaxTaxRate=<?php echo SCI::getEcotaxTaxRate(); ?>;
    var tax_values={};
<?php
    createMenu();
    echo "tax_values['-']=1;\n";
    $sql = 'SELECT trg.name, trg.id_tax_rules_group,'.(version_compare(_PS_VERSION_, '1.6.0.10', '>=') ? 'trg.deleted, ' : '').'t.rate
    FROM `'._DB_PREFIX_.'tax_rules_group` trg
    LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr
        ON (trg.`id_tax_rules_group` = tr.`id_tax_rules_group`
        AND tr.`id_country` = '.(int) SCI::getDefaultCountryId().' AND tr.`id_state` = 0)
    LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)
    WHERE trg.active=1';
    if (version_compare(_PS_VERSION_, '1.6.0.10', '>='))
    {
        $sql .= ' ORDER BY trg.deleted ASC';
    }
    $res = Db::getInstance()->ExecuteS($sql);
    foreach ($res as $row)
    {
        $tv_name = (string) $row['name'];
        if (version_compare(_PS_VERSION_, '1.6.0.10', '>=') && (int) $row['deleted'] > 0)
        {
            $tv_name = _l('(deleted)').' '.$tv_name;
        }
        echo "tax_values['".addslashes($tv_name)."']=".($row['rate'] / 100 + 1).";\n";
    }
    echo "var tax_identifier='id_tax_rules_group';\n";
?>
    catselection=0;
    segselection=0;
    shopselection=$.cookie('sc_shop_selected')*1
    <?php if(SCMS) { ?>
    if($.cookie('sc_shop_selected') != null){
        if(shopselection === '0'){
            displayWarningAllShopsSelected();
        }
    } else {
        shopselection=<?php echo (int) Configuration::get('PS_SHOP_DEFAULT'); ?>;
        $.cookie('sc_shop_selected',shopselection, { expires: 60 , path: cookiePath});
    }
    <?php } ?>
    shop_list=$.cookie('sc_shop_list');
    warehouseselection=$.cookie('sc_warehouse_selected')*1;
    warehouse_list=$.cookie('sc_warehouse_list');
    lastProductSelID=0;
    lightMouseNavigation=0;
    propertiesPanel='<?php echo _s('CAT_PRODPROP_GRID_DEFAULT'); ?>';
    tree_mode='single';
    hide_disable_cat=0;
    displayProductsFrom='all'; // all = all categories ; default = by id_category_default
    lastColumnRightClicked_Combi=0;
    clipboardValue_Combi=null;
    clipboardType_Combi=null;
    copytocateg=false;
    clipboardValue=null;
    clipboardType=null;
    tmpcollapsedcell=false;
    featuresFilter=0;
    categoriesFilter=0;
    draggedProduct=0;
    firstProductsLoading=1;
    firstCombinationsLoading=1;
    dragdropcache='';
    combiAttrValues=new Array();
    msgFixCategories=true;
    var catTreeIsLoading = false;
    enalbeDynamicTreeLoading = <?php echo (int) _s('CAT_CATEGORY_TREE_AJAX'); ?>;
    var catselection_path = [];

<?php
$ids = Tools::getValue('open_cat_grid', 0);
$has_auto_open = false;
if (!empty($ids))
{
    $has_auto_open = true;
    $tmps = explode('-', $ids);
    $catSelectionList = array();
    if((int) _s('CAT_CATEGORY_TREE_AJAX') && !empty($tmps) && isset($tmps[0]))
    {
        $searchCategory = new Category($tmps[0]);
        $sql = 'SELECT id_category
        FROM '._DB_PREFIX_.'category 
        WHERE nleft < '.(int)$searchCategory->nleft.'
        AND nright > '.(int)$searchCategory->nright.'
        AND id_parent > 0 
        AND is_root_category = 0
        ORDER BY nleft';
        $res = Db::getInstance()->executeS($sql);
        if($res)
        {
            $catSelectionList = array_column($res,'id_category');
            $catSelectionList[] = $tmps[0];
        }
    }
    echo '
    catselection_path = '.json_encode($catSelectionList).';
    open_cat_grid = true;
    open_cat_id_cat = '.$tmps[0].';
    open_cat_id_product = '.$tmps[1].';
    open_cat_id_attr = '.(!empty($tmps[2]) ? $tmps[2] : '0').';

    catselection=open_cat_id_cat;
    ';
}
else
{
    echo ' open_cat_grid = false; ';
}

//#####################################
//############ Categories toolbar
//#####################################
?>

    gridView='<?php echo _s('CAT_PROD_GRID_DEFAULT'); ?>';
    oldGridView='';
<?php
    echo SCI::getShopUrlArrayJs();
    if (SCMS)
    {
        ?>
    cat = new dhtmlXLayoutObject(dhxLayout.cells("a"), "2U");

    <?php if (SCAS) { ?>
        cat_firstcolcontent = cat.cells("a").attachLayout("3E");

        cat_storePanel = cat_firstcolcontent.cells('a');
        cat_warehousePanel = cat_firstcolcontent.cells('c');
        cat_warehousePanel_name = 'c';
        cat_categoryPanel = cat_firstcolcontent.cells('b');
    <?php }
        else
        { ?>
        cat_firstcolcontent = cat.cells("a").attachLayout("2E");

        cat_storePanel = cat_firstcolcontent.cells('a');
        cat_categoryPanel = cat_firstcolcontent.cells('b');
    <?php } ?>

    cat_productPanel = cat.cells('b');


    <?php //#####################################
                //############ Boutiques Tree
                //#####################################
    ?>
    var has_shop_restrictions = false;

    cat.cells("a").setText('<?php echo _l('Stores', 1); ?>');
    cat.cells("a").showHeader();
    cat_storePanel.hideHeader();
    var start_cat_size_store = getParamUISettings('start_cat_size_store');
    if(start_cat_size_store==null || start_cat_size_store<=0 || start_cat_size_store=="")
        start_cat_size_store = 150;
    cat_storePanel.setHeight(start_cat_size_store);
    cat_firstcolcontent.attachEvent("onPanelResizeFinish", function(names){
        $.each(names, function(num, name){
            if(name=="a")
                saveParamUISettings('start_cat_size_store', cat_storePanel.getHeight())
        });
    });
    cat_shoptree=cat_storePanel.attachTree();
    cat_shoptree._name='shoptree';
    cat_shoptree.autoScroll=false;
    cat_shoptree.setImagePath('lib/js/imgs/dhxtree_material/');
    cat_shoptree.enableSmartXMLParsing(true);
    cat_shoptree.enableCheckBoxes(true, false);

    var catShoptreeTB = cat_storePanel.attachToolbar();
    catShoptreeTB.setIconset('awesome');
    catShoptreeTB.addButton("help", 0, "", "fad fa-question-circle blue", "fad fa-question-circle blue");
    catShoptreeTB.setItemToolTip('help','<?php echo _l('Help'); ?>');
    catShoptreeTB.attachEvent("onClick", function(id) {
        if (id=='help')
        {
            var display = "";
            var update = "";
            if(shopselection>0)
            {
                display = cat_shoptree.getItemText(shopselection);
            }
            else if(shopselection==0)
            {
                display = cat_shoptree.getItemText("all");
            }

            var all_checked = $.cookie('sc_shop_list').split(",");
            $.each(all_checked, function(index, id) {
                if(id!="all" && id.search("G")<0)
                {
                    if(update!="")
                        update += ", ";
                    update += cat_shoptree.getItemText(id);
                }
            });

            var msg = '<strong><?php echo addslashes(_l('Display:')); ?></strong> '+display+'<br/><br/><strong><?php echo addslashes(_l('Update:')); ?></strong> '+update;
            dhtmlx.message({text:msg,type:'info',expire:10000});
        }
    });


    displayShopTree();
    function checkWhenSelection(idshop)
    {
        let allShops_item_found = cat_shoptree.getIndexById('all');
        if(allShops_item_found !== null) {
            var children = cat_shoptree.getAllSubItems("all").split(",");
        } else {
            var children = cat_shoptree.getAllChildless().split(",");
        }
        if ((idshop == 'all' || idshop==0) && has_shop_restrictions==0)
        {
            cat_shoptree.setCheck("all",1);
            $.each(children, function(index, id) {
                cat_shoptree.setCheck(id,1);
                cat_shoptree.disableCheckbox(id,1);
            });
        }
        else
        {
            $.each(children, function(index, id) {
                cat_shoptree.disableCheckbox(id,0);
            });
            if(idshop>0)
            {
                cat_shoptree.setCheck(idshop,1);
                cat_shoptree.disableCheckbox(idshop,1);
            }
        }
    }
    function deSelectParents(idshop)
    {
        if(cat_shoptree.getParentId(idshop)!="")
        {
            var parent_id = cat_shoptree.getParentId(idshop);
            cat_shoptree.setCheck(parent_id,0);

            deSelectParents(parent_id);
        }
    }
    function saveCheckSelection()
    {
        var checked = cat_shoptree.getAllChecked();
        if(shopselection=="all" || shopselection=="0")
        {
            let allShops_item_found = cat_shoptree.getIndexById('all');
            if(allShops_item_found !== null) {
                checked = cat_shoptree.getAllSubItems("all");
            } else {
                checked = cat_shoptree.getAllChildless();
            }
        }
        var all_checked = checked.split(",");
        var cookie_checked = "";
        $.each(all_checked, function(index, id) {
            if(id!="all" && id.search("G")<0)
            {
                if(cookie_checked!="")
                    cookie_checked += ",";
                cookie_checked += id;
            }
        });
        if(shopselection!=undefined && shopselection!="")
        {
            if(cookie_checked!="")
                cookie_checked += ",";
            cookie_checked += shopselection;
        }
        $.cookie('sc_shop_list',cookie_checked, { expires: 60 , path: cookiePath});
    }
    function displayShopTree(callback) {


        cat_shoptree.deleteChildItems(0);
        cat_shoptree.load("index.php?ajax=1&act=cat_shop_get&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),function(){
                has_shop_restrictions = cat_shoptree.getUserData(0, "has_shop_restrictions");
                // shop selectionné (appel depuis FixMyPs)
                <?php
                $id_shop = (int) Tools::getValue('only_shop', 0);
                if ($id_shop !== 0)
                {
                    echo 'shopselection=\''.$id_shop.'\';';
                    echo 'shop_list=\''.$id_shop.'\';';
                }?>


                if(shopselection!=null && shopselection!=undefined)
                    checkWhenSelection(shopselection);
                if(shop_list!=null && shop_list!="")
                {
                    var selected = shop_list.split(",");
                    $.each(selected, function(index, id) {
                        cat_shoptree.setCheck(id,1);
                    });
                }
                if (shopselection!=null && shopselection!=undefined && shopselection!=0)
                {
                    cat_shoptree.openItem(shopselection);
                    cat_shoptree.selectItem(shopselection,true);
                }

                if(has_shop_restrictions)
                {
                    selected = cat_shoptree.getSelectedItemId();
                    if(selected==undefined || selected==null || selected=="")
                    {
                        var all = cat_shoptree.getAllSubItems(0);
                        if(all!=undefined && all!=null && all!="")
                        {
                            all = all.split(",");
                            var id_to_select = "";
                            $.each(all, function(index, id) {
                                if(id.search("G")<0)
                                {
                                    if(id_to_select=="")
                                        id_to_select = id;
                                }
                            });
                            shopselection = id_to_select;
                            cat_shoptree.openItem(shopselection);
                            cat_shoptree.selectItem(shopselection,true);
                            $.cookie('sc_shop_selected',shopselection, { expires: 60 , path: cookiePath});
                        }
                    }
                }

                if (callback!='') eval(callback);
                cat_shoptree.openAllItems(0);
            });
    }
    cat_shoptree.attachEvent("onClick",onClickShopTree);
    function onClickShopTree(idshop, param,callback){

        if (idshop[0]=='G'){
            cat_shoptree.clearSelection();
            cat_shoptree.selectItem(shopselection,false);
            return false;
        }
        if (idshop == 'all'){
            idshop = 0;
            displayWarningAllShopsSelected();
        }
        checkWhenSelection(idshop);
        if (idshop != shopselection)
        {
            if(shopselection!=0 && idshop!=0 && idshop[0]!='G')
                cat_shoptree.setCheck(shopselection,0);
            else if(shopselection==0 && idshop!=0 && idshop[0]!='G')
            {
                if(has_shop_restrictions==0)
                {
                    var children = cat_shoptree.getAllSubItems("all").split(",");
                    cat_shoptree.setCheck("all",0);
                    $.each(children, function(index, id) {
                        if(id!=idshop)
                            cat_shoptree.setCheck(id,0);
                    });
                }
                else
                {
                    var children = cat_shoptree.getAllSubItems(0).split(",");
                    cat_shoptree.setCheck("all",0);
                    $.each(children, function(index, id) {
                        if(id!=idshop)
                            cat_shoptree.setCheck(id,0);
                    });
                }

            }
            shopselection = idshop;
            $.cookie('sc_shop_selected',shopselection, { expires: 60 , path: cookiePath});
            cat_categoryPanel.setText('<?php echo _l('Categories', 1).' '._l('of', 1); ?> '+cat_shoptree.getItemText(shopselection));
            displayTree(callback_refresh);
        }
        else
        {
            var callback_refresh = "";
            if(callback!=undefined && callback!=null && callback!="")
                callback_refresh = callback_refresh + callback;

            displayTree(callback_refresh);
        }
        saveCheckSelection();
    }

    cat_shoptree.attachEvent("onCheck",function(idshop, state){
        if(idshop=="all")
        {
            var children = cat_shoptree.getAllSubItems("all").split(",");
            $.each(children, function(index, id) {
                cat_shoptree.setCheck(id,state);
            });
        }
        else if(idshop.search("G")>=0)
        {
            var children = cat_shoptree.getAllSubItems(idshop).split(",");
            $.each(children, function(index, id) {
                cat_shoptree.setCheck(id,state);
            });
        }
        else
        {
            deSelectParents(idshop);
        }
        saveCheckSelection();
    });




    <?php //#####################################
                //############ Context menu
                //#####################################
    ?>
        var drag_disabled_for_sort = true; // for disable the drag  in tree after  sort and  "sort and save"
        cat_shop_cmenu_tree=new dhtmlXMenuObject();
        cat_shop_cmenu_tree.renderAsContextMenu();
        function onTreeContextButtonClickForShop(itemId){
            if (itemId=="goshop"){
                tabId=cat_shoptree.contextID;
    <?php
        if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
        {
            if (SCMS)
            {
                ?>
                if(shopUrls[tabId] != undefined && shopUrls[tabId] != "" && shopUrls[tabId] != null)
                    window.open(shopUrls[tabId]);
    <?php
            }
            else
            { ?>
                window.open('<?php echo SC_PS_PATH_REL; ?>');
            <?php }
        }
        else
        {
            ?>
                window.open('<?php echo SC_PS_PATH_REL; ?>');
    <?php
        } ?>
            }
        }
        cat_shop_cmenu_tree.attachEvent("onClick", onTreeContextButtonClickForShop);
        var contextMenuXML='<menu absolutePosition="auto" mode="popup" maxItems="8"  globalCss="contextMenu" globalSecondCss="contextMenu" globalTextCss="contextMenuItem">'+
            '<item text="Object" id="object" enabled="false"/>'+
            '<item text="<?php echo _l('See shop'); ?>" id="goshop"/>'+
        '</menu>';
        cat_shop_cmenu_tree.loadStruct(contextMenuXML);
        cat_shoptree.enableContextMenu(cat_shop_cmenu_tree);

        cat_shoptree.attachEvent("onBeforeContextMenu", function(itemId){

            var display_id = itemId;
            var display_text = '<?php echo _l('Shop:'); ?> ';
            if(itemId=="all")
            {
                return false;
            }
            else if(itemId.search("G")>=0)
            {
                var display_id = itemId.replace("G","");
                var display_text = '';
            }

            cat_shop_cmenu_tree.setItemText('object', 'ID'+display_id+': '+display_text+cat_shoptree.getItemText(itemId));

            <?php if (SCMS) { ?>
            if(shopUrls[itemId] != undefined && shopUrls[itemId] != "" && shopUrls[itemId] != null)
            {
                cat_shop_cmenu_tree.setItemEnabled('goshop');
            }else{
                cat_shop_cmenu_tree.setItemDisabled('goshop');
            }
            <?php } ?>

            return true;
        });
<?php
    }
    else
    {
        ?>
    cat = new dhtmlXLayoutObject(dhxLayout.cells("a"), "2U");

    <?php if (SCAS) { ?>
    cat_firstcolcontent = cat.cells("a").attachLayout("2E");

    cat_warehousePanel = cat_firstcolcontent.cells('b');
    cat_warehousePanel_name = 'b';
    cat_categoryPanel = cat_firstcolcontent.cells('a');
    <?php }
        else
        { ?>
    cat_firstcolcontent = cat_categoryPanel = cat.cells('a');
    <?php } ?>
    cat_productPanel = cat.cells('b');
<?php
    }
    //#####################################
    //############ WAREHOUSE TREE
    //#####################################
    if (SCAS) { ?>
    cat_warehousePanel.setText('<?php echo _l('Warehouses', 1); ?>');
    cat_warehousePanel.showHeader();
    var start_cat_size_warehouse = getParamUISettings('start_cat_size_warehouse');
    if(start_cat_size_warehouse==null || start_cat_size_warehouse<=0 || start_cat_size_warehouse=="")
        start_cat_size_warehouse = 150;
    cat_warehousePanel.setHeight(start_cat_size_warehouse);
    cat_firstcolcontent.attachEvent("onPanelResizeFinish", function(names){
        $.each(names, function(num, name){
            if(name==cat_warehousePanel_name)
                saveParamUISettings('start_cat_size_warehouse', cat_warehousePanel.getHeight())
        });
    });

    cat_warehousetree=cat_warehousePanel.attachTree();
    cat_warehousetree._name='warehousetree';
    cat_warehousetree.autoScroll=false;
    cat_warehousetree.setImagePath('lib/js/imgs/dhxtree_material/');
    cat_warehousetree.enableSmartXMLParsing(true);

    <?php if (_r('ACT_CAT_ADVANCED_STOCK_MANAGEMENT')) { ?>
    var catWarehousetreeTB = cat_warehousePanel.attachToolbar();
      catWarehousetreeTB.setIconset('awesome');
    catWarehousetreeTB.addButton("warehouses_manage", 100, "", "fad fa-edit yellow", "fad fa-edit yellow");
    catWarehousetreeTB.setItemToolTip('warehouses_manage','<?php echo _l('Manage warehouses', 1); ?>');
    catWarehousetreeTB.attachEvent("onClick", function(id) {
        if (id=='warehouses_manage')
        {
            if (!dhxWins.isWindow("wWarehouseManag"))
            {
                wWarehouseManag = dhxWins.createWindow("wWarehouseManag", 50, 50, 1000, $(window).height()-75);
                wWarehouseManag.button('park').hide();
                wWarehouseManag.button('minmax').hide();
                wWarehouseManag.setText('<?php echo _l('Manage warehouses', 1); ?>');
                wWarehouseManag.attachURL("<?php echo SC_PS_PATH_ADMIN_REL; ?>index.php?controller=AdminWarehouses&token=<?php echo $sc_agent->getPSToken('AdminWarehouses'); ?>");
                wWarehouseManag.attachEvent("onClose", function(win){
                    displayWarehouseTree();
                    return true;
                });
            }
        }
    });
    <?php } ?>

    <?php if (SCMS) { ?>
    cat_shoptree.attachEvent("onClick",function(idshop){
        displayWarehouseTree();
    });
    <?php } ?>

    function checkWhenWarehouseSelection(idwarehouse)
    {
        if(idwarehouse>0)
        {
            cat_warehousetree.setCheck(idwarehouse,1);
            cat_warehousetree.disableCheckbox(idwarehouse,1);
        }
    }
    function saveCheckWarehouseSelection()
    {
        var checked = cat_warehousetree.getAllChecked();
        var all_checked = checked.split(",");
        var cookie_checked = "";
        $.each(all_checked, function(index, id) {
            if(cookie_checked!="")
                cookie_checked += ",";
            cookie_checked += id;
        });
        if(warehouseselection!=undefined && warehouseselection!="")
        {
            if(cookie_checked!="")
                cookie_checked += ",";
            cookie_checked += warehouseselection;
        }
        $.cookie('sc_warehouse_list',cookie_checked, { expires: 60 , path: cookiePath});
        warehouse_list = cookie_checked;
    }
    function displayWarehouseTree(callback) {
        cat_warehousetree.deleteChildItems("0");
        cat_warehousetree.load("index.php?ajax=1&act=cat_warehouse_get&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),function(){
                if(
                        (warehouseselection==null || warehouseselection==0 || warehouseselection==undefined)
                        ||
                        (
                               cat_warehousetree.getIndexById(warehouseselection)==undefined
                            || cat_warehousetree.getIndexById(warehouseselection)==0
                            || cat_warehousetree.getIndexById(warehouseselection)==null
                        )
                    )
                {
                    var children = cat_warehousetree.getAllSubItems("0").split(",");
                    warehouseselection = cat_warehousetree.getItemIdByIndex(0,0);
                }

                if (warehouseselection!=null && warehouseselection!=undefined && warehouseselection!=0)
                {
                    cat_warehousetree.selectItem(warehouseselection,false);
                    $.cookie('sc_warehouse_selected',warehouseselection, { expires: 60 , path: cookiePath});
                }

                if (callback!='') eval(callback);
            });
    }
    <?php if (SCMS) { ?>
    if(shopselection==0)
        displayWarehouseTree();
    <?php }
    else
    { ?>
    displayWarehouseTree();
    <?php } ?>
    cat_warehousetree.attachEvent("onClick",function(idwarehouse){
        //checkWhenWarehouseSelection(idwarehouse);
        if (idwarehouse != warehouseselection)
        {
            warehouseselection = idwarehouse;
            $.cookie('sc_warehouse_selected',warehouseselection, { expires: 60 , path: cookiePath});
        }
        displayProducts();
    });
    cat_warehousetree.attachEvent("onCheck",function(idwarehouse, state){
    });



    <?php //#####################################
                //############ Context menu
                //#####################################
    ?>
        var id_selected_warehouse = 0;
        cat_warehouse_cmenu_tree=new dhtmlXMenuObject();
        cat_warehouse_cmenu_tree.renderAsContextMenu();
        function onTreeWarehouseContextButtonClick(itemId){
            if (itemId=="truncate"){
                askConfirmation(itemId);
            }
            if (itemId=="empty"){
                askConfirmation(itemId, 1);
            }
            if (itemId=="transfert"){
                if (dhxWins.isWindow("wWarehouseStockTransfert"))
                    wWarehouseStockTransfert.close();
                wWarehouseStockTransfert = dhxWins.createWindow("wWarehouseStockTransfert", 50, 50, 450, 180);
                wWarehouseStockTransfert.setText('<?php echo _l('Transfert stock between two warehouses', 1); ?>');
                $.get("index.php?ajax=1&act=cat_warehouse_transfert_window",function(data){
                        $('#jsExecute').html(data);
                    });
            }
            if (itemId=="synchronize"){
                $.post("index.php?ajax=1&act=cat_warehouse_synchronize&id_lang="+SC_ID_LANG,{'id_warehouse':id_selected_warehouse},function(data){
                    if (data.type=='success')
                        dhtmlx.message({text:'<?php echo addslashes(_l('This warehouse was successfully synchronized')); ?>',type:'success',expire:5000});
                    else if (data.type=='error')
                        dhtmlx.message({text:'<?php echo addslashes(_l('An error occured during synchronize operation')); ?>',type:'error',expire:5000});
                    if(data.debug!=undefined && data.debug!="")
                        console.log(data.debug);
                }, "JSON");
            }
        }

        function askConfirmation(itemId, history)
        {
            var confirmation = prompt('<?php echo _l('Enter "ok" and click "Validate" (?) to empty the warehouse.', 1); ?>',"");
            confirmation = confirmation.toLowerCase();
            if (confirmation!=undefined && confirmation=="ok")
            {
                truncateWarehouse(itemId, history);
            }
            else if (confirmation!=undefined && confirmation!="ok")
            {
                askConfirmation(itemId, history);
            }
        }
        function truncateWarehouse(itemId, history)
        {
            if(itemId!=undefined && itemId!=null && itemId!="")
            {
                if(history==undefined && history==null && history=="")
                    history = 0;
                $.post("index.php?ajax=1&act=cat_warehouse_truncate&id_lang="+SC_ID_LANG,{'id_warehouse':id_selected_warehouse, 'history':history},function(data){
                    if (data.type=='success')
                        dhtmlx.message({text:'<?php echo addslashes(_l('This warehouse was successfully truncated')); ?>',type:'success',expire:5000});
                    else if (data.type=='error')
                        dhtmlx.message({text:'<?php echo addslashes(_l('An error occured during truncate')); ?>',type:'error',expire:5000});

                    if(data.debug!=undefined && data.debug!="")
                        console.log(data.debug);
                }, "JSON");
            }
        }

        function transfertWarehouse(itemId, truncate_A)
        {
            if(truncate_A==undefined || truncate_A=="" || truncate_A==null)
                truncate_A = 0;
            if(itemId!=undefined && itemId!=null && itemId!="" &&  itemId!=0 && itemId!=id_selected_warehouse)
            {
                $.post("index.php?ajax=1&act=cat_warehouse_transfert&id_lang="+SC_ID_LANG,{'id_warehouse_A':id_selected_warehouse,'id_warehouse_B':itemId, 'truncate_A':truncate_A},function(data){
                    if (data.type=='success')
                        dhtmlx.message({text:'<?php echo addslashes(_l('The stock was successfully transfered')); ?>',type:'success',expire:5000});
                    else if (data.type=='error')
                        dhtmlx.message({text:'<?php echo addslashes(_l('An error occured during transfer')); ?>',type:'error',expire:5000});

                    if(data.debug!=undefined && data.debug!="")
                        console.log(data.debug);
                }, "JSON");
            }
        }

        cat_warehouse_cmenu_tree.attachEvent("onClick", onTreeWarehouseContextButtonClick);
        var contextMenuXMLWarehouse='<menu absolutePosition="auto" mode="popup" maxItems="8"  globalCss="contextMenu" globalSecondCss="contextMenu" globalTextCss="contextMenuItem">'+
            '<item text="Object" id="object" enabled="false"/>'+
            '<item text="<?php echo _l('Clear warehouse', 1); ?>" id="truncate"/>'+
            '<item text="<?php echo _l('Clear warehouse (keep history)', 1); ?>" id="empty"/>'+
            '<item text="<?php echo _l('Transfert', 1); ?>" id="transfert"/>'+
            '<item text="<?php echo _l('Synchronize', 1); ?>" id="synchronize"/>'+
        '</menu>';

        cat_warehouse_cmenu_tree.loadStruct(contextMenuXMLWarehouse);
        cat_warehousetree.enableContextMenu(cat_warehouse_cmenu_tree);

        cat_warehousetree.attachEvent("onBeforeContextMenu", function(itemId){
            id_selected_warehouse = itemId;
            cat_warehouse_cmenu_tree.setItemText('object', 'ID'+itemId+': <?php echo _l('Warehouse:', 1); ?> '+cat_warehousetree.getItemText(itemId));
            return true;
        });
    <?php } ?>

    /* CATEGORIES */
    var start_cat_size_tree = getParamUISettings('start_cat_size_tree');
    if(start_cat_size_tree==null || start_cat_size_tree<=0 || start_cat_size_tree=="")
        start_cat_size_tree = 300;
    <?php if (SCAS || SCMS) { ?>
        cat.cells("a").setWidth(start_cat_size_tree);
        cat.attachEvent("onPanelResizeFinish", function(){
            saveParamUISettings('start_cat_size_tree', cat.cells("a").getWidth())
        });
    <?php }
    else
    { ?>
        cat_categoryPanel.setWidth(start_cat_size_tree);
        cat.attachEvent("onPanelResizeFinish", function(){
            saveParamUISettings('start_cat_size_tree',cat_categoryPanel.getWidth())
        });
    <?php } ?>
    cat_tb=cat_categoryPanel.attachToolbar();
    cat_tb.setIconset('awesome');
    cat_tb.addButton("help", 0, "", "fad fa-question-circle blue", "fad fa-question-circle blue");
    cat_tb.setItemToolTip('help','<?php echo _l('Help', 1); ?>');
    <?php if (_r('ACT_CAT_EMPTY_RECYCLE_BIN')) { ?>
    cat_tb.addButton("bin", 0, "", "fa fa-trash-alt red", "fa fa-trash-alt red");
    cat_tb.setItemToolTip('bin','<?php echo _l('Empty bin', 1); ?>');
    <?php } ?>
    cat_tb.addButton("cat_treegrid_in_bin", 0, "", "fad fa-trash grey", "fad fa-trash grey");
    cat_tb.setItemToolTip('cat_treegrid_in_bin','<?php echo _l('Put in the bin/trash', 1); ?>');
    <?php if (_r('ACT_CAT_ADD_CATEGORY')) { ?>
    cat_tb.addButton("add_ps", 0, "", "fa fa-prestashop", "fa fa-prestashop");
    cat_tb.setItemToolTip('add_ps','<?php echo _l('Create new category with the PrestaShop form', 1); ?>');
    cat_tb.addButton("add", 0, "", "fa fa-plus-circle green", "fa fa-plus-circle green");
    cat_tb.setItemToolTip('add','<?php echo _l('Create new category', 1); ?>');
    <?php } ?>
    cat_tb.addButton("cat_management", 0, "", "fa fa-cog yellow", "fa fa-cog yellow");
    cat_tb.setItemToolTip('cat_management','<?php echo _l('Categories management', 1); ?>');
    cat_tb.addButtonTwoState("fromIDCategDefault", 0, "", "fad fa-at", "fad fa-at");
    cat_tb.setItemToolTip('fromIDCategDefault','<?php echo _l('If enabled: display products only from their default category', 1); ?>');
    cat_tb.addButtonTwoState("withSubCateg", 0, "", "fad fa-link green", "fad fa-link green");
    cat_tb.setItemToolTip('withSubCateg','<?php echo _l('If enabled: display products from all subcategories', 1); ?>');
    cat_tb.addButton("refresh", 0, "", "fa fa-sync green", "fa fa-sync green");
    cat_tb.setItemToolTip('refresh','<?php echo _l('Refresh tree', 1); ?>');
    cat_tb.addButtonTwoState("hide_disable_cat", 0, "", "fa fa-filter", "fa fa-filter");
    cat_tb.setItemToolTip('hide_disable_cat','<?php echo _l('If enabled: hide disable categories', 1); ?>');
    cat_tb.attachEvent("onClick",
        function(id){
            if (id=='help'){
                <?php echo "window.open('".getScExternalLink('support_categories')."');"; ?>
            }
            if (id=='refresh'){
                displayTree();
            }
            if (id=='cat_management'){
                if (!dhxWins.isWindow("wCatManagement"))
                {
                    wCatManagement = dhxWins.createWindow("wCatManagement", 0, 28, $(window).width(), $(window).height()-28);
                    wCatManagement.setText('<?php echo _l('Categories management', 1); ?>');
                    $.get("index.php?ajax=1&act=cat_win-catmanagement_init",function(data){
                            $('#jsExecute').html(data);
                        });
                    wCatManagement.attachEvent("onClose", function(win){
                            wCatManagement.hide();
                            return false;
                        });
                }else{
                    $.get("index.php?ajax=1&act=cat_win-catmanagement_init",function(data){
                            $('#jsExecute').html(data);
                        });
                    wCatManagement.show();
                }
            }
            if (id=='bin'){
                if (confirm('<?php echo _l('Are you sure to delete all categories and products placed in the recycled bin?', 1); ?>'))
                {
                    var id_bin=cat_tree.findItemIdByLabel('<?php echo _l('SC Recycle Bin'); ?>',0,1);
                    if (id_bin==null)
                        id_bin=cat_tree.findItemIdByLabel('SC Recycle Bin',0,1);
                    if (id_bin!=null)
                        $.get("index.php?ajax=1&act=cat_category_update&action=emptybin&id_category="+id_bin+'&id_lang='+SC_ID_LANG,function(id){
                                lastProductSelID=0;
                                childlist=cat_tree.getAllSubItems(id_bin).split(',');
                                displayTree();
                                if (catselection==id_bin || in_array(catselection,childlist))
                                {
                                    lastProductSelID=0;
                                    cat_grid.clearAll();
                                    cat_grid_sb.setText('');
                                }
                            });
                }
            }
            if (id=='add'){
                if (catselection!=0)
                {
                    var cname=prompt('<?php echo _l('Create a category:', 1); ?>');
                    if (cname!=null)
                        $.post("index.php?ajax=1&act=cat_category_update&action=insert&id_parent="+catselection+'&id_lang='+SC_ID_LANG,{name: (cname)},function(id){
                            cat_tree.insertNewChild(catselection,id,cname,0,'folder_grey.png','folder_grey.png','folder_grey.png');
                            let current_proptb = propertiesPanel;
                            if(propertiesPanel === null) {
                                current_proptb = '<?php echo _s('CAT_PRODPROP_GRID_DEFAULT'); ?>';
                            }
                            if(current_proptb === 'categories') {
                                displayCategories('',true);
                            }
                        });
                }else{
                    alert('<?php echo _l('You need to select a parent category before creating a category', 1); ?>');
                }
            }
            if (id=='add_ps'){
                if (!dhxWins.isWindow("wNewCategory"))
                {
                    wNewCategory = dhxWins.createWindow("wNewCategory", 50, 50, 1000, $(window).height()-75);
                    wNewCategory.setText('<?php echo _l('Create the new category and close this window to refresh the tree', 1); ?>');
<?php if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')){ ?>
                    wNewCategory.attachURL("<?php echo SC_PS_PATH_ADMIN_REL; ?>index.php?controller=admincategories&addcategory&id_parent="+catselection+"&token=<?php echo $sc_agent->getPSToken('AdminCategories'); ?>");
<?php }
    else
    { ?>
                    wNewCategory.attachURL("<?php echo SC_PS_PATH_ADMIN_REL; ?>index.php?tab=AdminCatalog&addcategory&id_parent="+catselection+"&token=<?php echo $sc_agent->getPSToken('AdminCatalog'); ?>");
<?php } ?>
                    pushOneUsage('tree-bo-link-admincategories_addcategory','cat');
                    wNewCategory.attachEvent("onClose", function(win){
                                displayTree();
                                return true;
                            });
                }
            }
            if (id=='cat_treegrid_in_bin'){
                let cat_selection=cat_tree.getSelectedItemId();
                if (cat_selection!='' && cat_selection!=null)
                {
                    let id_bin= cat_tree.findItem('<?php echo _l('SC Recycle Bin'); ?>',0,1);
                    if (id_bin!=null)
                    {
                        cat_tree.moveItem(cat_selection,'item_child',id_bin);
                    }
                }
            }
        }
        );
    cat_tb.attachEvent("onStateChange", function(id,state){
            if (id=='withSubCateg'){
                if (state) {
                    tree_mode='all';
                  cat_grid_tb.disableItem('setposition');
                }else{
                    tree_mode='single';
                  cat_grid_tb.enableItem('setposition');
                }
                displayProducts();
            }
            if (id=='fromIDCategDefault'){
                if (state) {
                    displayProductsFrom='default';
                }else{
                    displayProductsFrom='all';
                }
                displayProducts();
            }
            if (id=='hide_disable_cat'){
                if(state){
                    hide_disable_cat=1;
                } else {
                    hide_disable_cat=0;
                }
                displayTree();
            }
        });
        $(document).ready(function(){
                if (<?php echo Tools::getValue('displayAllProducts', 0); ?>)
                    onMenuClick('cat_grid','','');
        });

<?php //#####################################
            //############ cat_tree
            //#####################################
?>

    cat_tree=cat_categoryPanel.attachTree();
    cat_tree._name='tree';
    cat_categoryPanel.setText('<?php echo _l('Categories', 1); ?><?php if (SCSG)
{
    echo ' '._l('& segments', 1);
} ?>');
    cat_productPanel.setText('<?php echo _l('Products', 1); ?>');
    cat_tree.autoScroll=false;
    cat_tree.setImagePath('lib/js/imgs/dhxtree_material/');
    cat_tree.enableSmartXMLParsing(true);
    <?php if (!SCSG && !_r('ACT_CAT_MOVE_CATEGORY') && !_r('ACT_CAT_MOVE_PRODUCTS_IN_CATEGORY')) { ?>
        cat_tree.enableDragAndDrop(false);
    <?php }
else
{ ?>
        cat_tree.enableDragAndDrop(true);
    <?php } ?>
    cat_tree.setDragBehavior("complex");
    cat_tree._dragBehavior="complex";


    function nameSortCatTree(idA,idB)
    {
        var a = latinise(cat_tree.getItemText(idA)).toLowerCase();
        var b = latinise(cat_tree.getItemText(idB)).toLowerCase();
        if ( a < b )
            return -1;
        if ( a > b )
            return 1;
        return 0;
    }

<?php if (!SCMS){ ?>
        displayTree();
<?php } ?>


<?php //#####################################
            //############ Context menu
            //#####################################
?>
    cat_cmenu_tree=new dhtmlXMenuObject();
    cat_cmenu_tree.renderAsContextMenu();
    function onTreeContextButtonClick(itemId){
        if (itemId=="gopsbo"){
            tabId=cat_tree.contextID;
            wModifyCategory = dhxWins.createWindow("wModifyCategory", 50, 50, 1000, $(window).height()-75);
            wModifyCategory.setText('<?php echo _l('Modify the category and close this window to refresh the tree', 1); ?>');
            wModifyCategory.attachURL("<?php echo SC_PS_PATH_ADMIN_REL; ?>index.php?<?php echo version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? 'controller=AdminCategories' : 'tab=AdminCatalog'; ?>&updatecategory&id_category="+tabId+"&id_lang="+SC_ID_LANG+"&adminlang=1&token=<?php echo $sc_agent->getPSToken((version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? 'AdminCategories' : 'AdminCatalog')); ?>");
            wModifyCategory.attachEvent("onClose", function(win){
                        displayTree();
                        return true;
                    });
        }
        if (itemId=="goshop"){
            tabId=cat_tree.contextID;
<?php
    if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
    {
        if (SCMS)
        {
            ?>
            if(shopUrls[shopselection] != undefined && shopUrls[shopselection] != "" && shopUrls[shopselection] != null)
                window.open(shopUrls[shopselection]+'index.php?id_category='+tabId+'&controller=category&id_lang='+SC_ID_LANG);
<?php
        }
        else
        { ?>
            window.open('<?php echo SC_PS_PATH_REL; ?>index.php?id_category='+tabId+'&controller=category&id_lang='+SC_ID_LANG);
        <?php }
    }
    else
    {
        ?>
            window.open('<?php echo SC_PS_PATH_REL; ?>category.php?id_category='+tabId);
<?php
    }
?>
        }
        if (itemId=="expand"){
            tabId=cat_tree.contextID;
            cat_tree.openAllItems(tabId);
        }
        if (itemId=="collapse"){
            tabId=cat_tree.contextID;
            cat_tree.closeAllItems(tabId);
            if (tabId==1) cat_tree.openItem(1);
        }
        if (itemId=="sort"){
            drag_disabled_for_sort = false;
            tabId=cat_tree.contextID;
            cat_tree.setCustomSortFunction(nameSortCatTree);
            cat_tree.sortTree(tabId,'ASC',1);
            dhtmlx.message({text:'<?php echo addslashes(_l('Category sorted, click on the Refresh icon to allow reorder (drag and drop) on the categories tree.')); ?>',type:'info',expire:5000});
        }
        if (itemId=="sort_and_save"){
            drag_disabled_for_sort = false;
            tabId=cat_tree.contextID;
            cat_tree.setCustomSortFunction(nameSortCatTree);

            var children = cat_tree.getSubItems(tabId).split(",");
            cat_tree.sortTree(tabId,'ASC',1);
            children = cat_tree.getSubItems(tabId);

            $.post("index.php?ajax=1&act=cat_category_update&action=sort_and_save&id_category="+tabId,{'children':children},function(){
                dhtmlx.message({text:'<?php echo addslashes(_l('Category sorted and positions recorded')); ?>',type:'success',expire:5000});});

        }
        if (itemId=="enable"){
            tabId=cat_tree.contextID;
            todo=(cat_tree.getItemImage(tabId,0,false)=='catalog.png'?0:1);
            $.get("index.php?ajax=1&act=cat_category_update&action=enable&id_category="+tabId+'&enable='+todo,function(id){
                    if (todo){
                        cat_tree.setItemImage2(tabId,'catalog.png','catalog.png','catalog.png');
                    }else{
                        cat_tree.setItemImage2(tabId,'folder_grey.png','folder_grey.png','folder_grey.png');
                    }
                });
        }
        if (itemId=="open_segment"){
            tabId=cat_tree.contextID;

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
        <?php if (SCSG)
{
    echo SegmentHook::hook('productSegmentRightClickItemsAction');
} ?>
    }
    cat_cmenu_tree.attachEvent("onClick", onTreeContextButtonClick);
    var contextMenuXML='<menu absolutePosition="auto" mode="popup" maxItems="8"  globalCss="contextMenu" globalSecondCss="contextMenu" globalTextCss="contextMenuItem">'+
        '<item text="Object" id="object" enabled="false"/>'+
        '<item text="<?php echo _l('Expand'); ?>" id="expand"/>'+
        '<item text="<?php echo _l('Collapse'); ?>" id="collapse"/>'+
        '<item text="<?php echo _l('Sort'); ?>" id="sort"/>'+
        '<item text="<?php echo _l('Sort and save'); ?>" id="sort_and_save"/>'+
        '<item text="<?php echo _l('See on shop'); ?>" id="goshop"/>'+
        '<item text="<?php echo _l('Edit in PrestaShop BackOffice'); ?>" id="gopsbo"/>'+
        <?php if (_r('ACT_CAT_CONTEXTMENU_SHOHIDE_CATEGORY')) { ?>
        '<item text="<?php echo _l('Enable / Disable'); ?>" id="enable"/>'+
        <?php } ?>
        <?php if (SCSG) { ?>
        '<item text="<?php echo _l('Properties'); ?>" id="open_segment"/>'+
        <?php echo SegmentHook::hook('productSegmentRightClickDefinition'); ?>
        <?php } ?>
    '</menu>';
    cat_cmenu_tree.loadStruct(contextMenuXML);
    cat_tree.enableContextMenu(cat_cmenu_tree);

<?php

//#####################################
//############ Events
//#####################################
?>
    cat_tree.attachEvent("onClick",function(idcategory){
            var is_segment = cat_tree.getUserData(idcategory,"is_segment");

            if(is_segment==1)
            {
                if (idcategory!=catselection)
                {
                    catselection=idcategory;
                    segselection = idcategory;
                    displayProducts();
                    if (propertiesPanel=='accessories' && accessoriesFilter)
                    {
                        prop_tb._accessoriesGrid.clearAll(true);
                        prop_tb._accessoriesGrid._rowsNum=0;
                        displayAccessories('',0);
                    }
                }
                cat_productPanel.setText('<?php echo _l('Products', 1).' '._l('of segment', 1); ?> '+cat_tree.getItemText(catselection));
            }
            else
            {
                if (idcategory!=catselection || SCMS)
                {
                    catselection=idcategory;
                    segselection = 0;
                    displayProducts();
                    if (propertiesPanel=='accessories' && accessoriesFilter)
                    {
                        prop_tb._accessoriesGrid.clearAll(true);
                        prop_tb._accessoriesGrid._rowsNum=0;
                        displayAccessories('',0);
                    }
                }
                cat_productPanel.setText('<?php echo _l('Products', 1).' '._l('of', 1); ?> '+cat_tree.getItemText(catselection)+(shopselection?' / '+cat_shoptree.getItemText(shopselection):''));
            }

            if(enalbeDynamicTreeLoading) {
                <?php
                $current_shop = new Shop((int) SCI::getSelectedShop());
                $root = Category::getRootCategory((int) $sc_agent->id_lang, $current_shop);
                ?>
                let root_categ = <?php echo (int) $root->id; ?>;
                let parentId = catselection;
                catselection_path = [];
                catselection_path.push(catselection);
                while (parentId > root_categ) {
                    catselection_path.push(cat_tree.getParentId(parentId));
                    parentId = cat_tree.getParentId(parentId);
                }
                catselection_path.reverse();
            }
        });
    cat_tree.attachEvent("onDragIn",function doOnDragIn(idSource,idTarget,sourceobject,targetobject){
            var is_segment = cat_tree.getUserData(idSource,"is_segment");
            var in_segment = cat_tree.getUserData(idTarget,"is_segment");
            if (drag_disabled_for_sort ==  false){
                if ( sourceobject._name=='tree' ){
                    return false ;
                }
            }
            if(sourceobject._name=='tree' && is_segment==1)
                 return false;
            if(sourceobject._name=='tree' && in_segment==1)
                 return false;

            var is_eservices = cat_tree.getUserData(idSource,"is_eservices");
            var in_eservices = cat_tree.getUserData(idTarget,"is_eservices");
            var not_associate_eservices = cat_tree.getUserData(idTarget,"not_associate_eservices");
            if(sourceobject._name=='tree' && is_eservices==1)
                return false;
            if(sourceobject._name=='tree' && in_eservices==1)
                return false;
            if(sourceobject._name=='grid' && in_eservices==1 && not_associate_eservices==1)
                return false;

            if(idSource!=undefined && idSource!=0 && is_segment!=1)
            {
                var is_home = cat_tree.getUserData(idSource,"is_home");
                if(is_home==1)
                {
                    return false;
                }
            }
            <?php if (!_r('ACT_CAT_MOVE_CATEGORY')) { ?>
                if (sourceobject._name=='tree') return false;
            <?php } ?>
            <?php if (!_r('ACT_CAT_MOVE_PRODUCTS_IN_CATEGORY')) { ?>
                if (sourceobject._name=='grid' && targetobject._name=='tree' && is_segment!=1) return false;
            <?php } ?>

            // Si produit est déplacé dans segment
            // mais celui-ci n'accepte pas l'ajout manuel de produits
            var manuel_add = cat_tree.getUserData(idTarget,"manuel_add");
            if(sourceobject._name=='grid' && in_segment==1 && manuel_add!=1)
                return false;

            if (sourceobject._name=='tree' || sourceobject._name=='grid') return true;
            return false;
        });
    cat_tree.attachEvent("onDrop",function doOnDrop(idSource,idTarget,idBefore,sourceobject,targetTree){
            var is_segment = cat_tree.getUserData(idTarget,"is_segment");
            if(sourceobject._name=='tree' && is_segment==1)
                 return false;

            if(idTarget === 0) {
                if (confirm("<?php echo _l('Move the category out of the home category?'); ?>") === false) {
                    displayTree();
                    return false;
                }
            }

            var real_parent_id = idTarget;
            if(real_parent_id==0)
            {
                real_parent_id = cat_tree.getUserData(idSource,"parent_root");
            }

            if (sourceobject._name=='tree')
            {


                $.get("index.php?ajax=1&act=cat_category_update&action=move&idCateg="+idSource+"&idNewParent="+real_parent_id+"&idNextBrother="+idBefore+'&id_lang='+SC_ID_LANG, function(data){
<?php
$sqlc = 'SELECT COUNT(*) AS nbc FROM '._DB_PREFIX_.'category';
$nbCateg = Db::getInstance()->getValue($sqlc);
if ($nbCateg > 10)
{
    ?>
                        if (msgFixCategories)
                        {
                            dhtmlx.message({text:'<?php echo addslashes(_l('Note: you will need to use the menu "Catalog > Tools > Check and fix categories" after your moves operation.')); ?>',type:'info',expire:10000});
                            msgFixCategories=false;
                        }
<?php
}
?>
                    });
            }
        });
    cat_tree.attachEvent("onBeforeContextMenu", function(itemId){
            var is_segment = cat_tree.getUserData(itemId,"is_segment");
            if(is_segment==1)
            {
                cat_cmenu_tree.setItemText('object', '<?php echo _l('Segment:'); ?> '+cat_tree.getItemText(itemId));
                cat_cmenu_tree.hideItem('sort');
                cat_cmenu_tree.hideItem('goshop');
                cat_cmenu_tree.hideItem('gopsbo');
                <?php if (_r('ACT_CAT_CONTEXTMENU_SHOHIDE_CATEGORY')) { ?>
                cat_cmenu_tree.hideItem('enable');
                <?php } ?>
                cat_cmenu_tree.showItem('open_segment');
                <?php if (SCSG)
{
    echo SegmentHook::hook('productSegmentRightClickShowItems');
} ?>
            }
            else
            {
                cat_cmenu_tree.setItemText('object', 'ID'+itemId+': <?php echo _l('Category:'); ?> '+cat_tree.getItemText(itemId));
                cat_cmenu_tree.showItem('sort');
                cat_cmenu_tree.showItem('goshop');
                cat_cmenu_tree.showItem('gopsbo');
                <?php if (_r('ACT_CAT_CONTEXTMENU_SHOHIDE_CATEGORY')) { ?>
                cat_cmenu_tree.showItem('enable');
                <?php } ?>
                cat_cmenu_tree.hideItem('open_segment');
                <?php if (SCMS) { ?>
                if(shopUrls[shopselection] != undefined && shopUrls[shopselection] != "" && shopUrls[shopselection] != null)
                {
                    cat_cmenu_tree.setItemEnabled('goshop');
                }else{
                    cat_cmenu_tree.setItemDisabled('goshop');
                }
                <?php } ?>
                <?php if (SCSG)
{
    echo SegmentHook::hook('productSegmentRightClickHideItems');
} ?>
            }
            return true;
        });
    cat_tree.attachEvent("onBeforeDrag",function(sourceid){
        var is_segment = cat_tree.getUserData(sourceid,"is_segment");

        if(is_segment==1)
             return false;

         return true;
    });
    cat_tree.attachEvent("onDrag",function(sourceid,targetid,sibling,sourceobject,targetobject){
        var is_segment = cat_tree.getUserData(sourceid,"is_segment");
        var in_segment = cat_tree.getUserData(targetid,"is_segment");

        if(sourceobject._name=='tree' && is_segment==1)
             return false;
        if(sourceobject._name=='tree' && in_segment==1)
             return false;

        if(sourceid!=undefined && sourceid!=0 && targetid!=undefined && targetid!=0 && is_segment!=1)
        {
            var is_recycle_bin = cat_tree.getUserData(targetid,"is_recycle_bin");
            if(is_recycle_bin==1)
            {
                var not_deletable = cat_tree.getUserData(sourceid,"not_deletable");
                if(not_deletable==1)
                    return false;
            }
            var is_home = cat_tree.getUserData(sourceid,"is_home");
            if(is_home==1)
            {
                return false;
            }
        }

        <?php if (!_r('ACT_CAT_MOVE_CATEGORY')) { ?>
            if (sourceobject._name=='tree') return false;
        <?php } ?>
        <?php if (!_r('ACT_CAT_MOVE_PRODUCTS_IN_CATEGORY')) { ?>
            if (sourceobject._name=='grid' && targetobject._name=='tree' && is_segment!=1) return false;
        <?php } ?>
        if (targetid==0) {targetid=cat_tree.getUserData("","parent_root");}
        if (sourceobject._name=='grid')
        {
            var manuel_add = cat_tree.getUserData(targetid,"manuel_add");

            // Si ce n'est pas un segment et qu'il n'est pas déplacé dans un segment (produit dans une catégorie)
            if(is_segment!=1 && in_segment!=1)
            {
                if (copytocateg)
                {
                    targetobject.setItemStyle(targetid,'background-color:#fedead;');
                    var products=cat_grid.getSelectedRowId();
                    if (products==null && draggedProduct!=0) products=draggedProduct;
                    draggedProduct=0;
                    if (dragdropcache!=catselection+'-'+targetid+'-'+products)
                    {

                        $.post("index.php?ajax=1&act=cat_category_dropproductoncategory&mode=copy&id_lang="+SC_ID_LANG,{'displayProductsFrom':displayProductsFrom,'categoryTarget':targetid,'categorySource':catselection,'products':products, 'eservices_id_project':cat_tree.getUserData(targetid,"eservices_id_project")},function(){
                            if (propertiesPanel=='categories')
                                displayCategories();
                            });
                        dragdropcache=catselection+'-'+targetid+'-'+products;
                    }
                }else{
                    targetobject.setItemStyle(targetid,'background-color:#fedead;');
                    var products=cat_grid.getSelectedRowId();
                    if (products==null && draggedProduct!=0) products=draggedProduct;
                    if (dragdropcache!=catselection+'-'+targetid+'-'+products)
                    {
                        var categorySource = catselection;
                        if(tree_mode=='all')
                        {
                            categorySource = cat_tree.getAllSubItems(catselection);
                        }
                    $.post("index.php?ajax=1&act=cat_category_dropproductoncategory&mode=move&id_lang="+SC_ID_LANG,{'categoryTarget':targetid,'categorySource':categorySource,'products':products, 'eservices_id_project':cat_tree.getUserData(targetid,"eservices_id_project")},function(){
                        if (draggedProduct>0)
                        {
                            setTimeout('cat_grid.deleteRow('+draggedProduct+');',200);
                        }else{
                            setTimeout('cat_grid.deleteSelectedRows();',200);
                        }
                        if (propertiesPanel=='categories')
                            displayCategories();
                        draggedProduct=0;
                        });
                        dragdropcache=catselection+'-'+targetid+'-'+products;
                    }
                }
            }
            // Si ce n'est pas un segment et qu'il est déplacé dans un segment (produit dans un segment)
            // et accepte l'ajout manuel de produits
            else if(is_segment!=1 && in_segment==1 && manuel_add==1)
            {
                var products=cat_grid.getSelectedRowId();
                if (products==null && draggedProduct!=0) products=draggedProduct;
                $.post("index.php?ajax=1&act=cat_segment_dropproductonsegment&mode=move&id_lang="+SC_ID_LANG,{'segmentTarget':targetid,'products':products},function(){});
            }
            return false;
        }else{
            if (sourceobject._name=='tree')
                return true;
            return false;
        }
    });
    cat_tree.attachEvent("onOpenStart",function(idcategory,state){
        let is_eservices = cat_tree.getUserData(idcategory,"is_eservices");
        if(is_eservices == 1) {
            if(state <= 0) {
                saveParamUISettings('start_cat_eservice_open',1);
            } else {
                saveParamUISettings('start_cat_eservice_open',0);
            }
        }
        return true;
    });
    cat_tree.attachEvent("onXLE",function()
    {
        openDynamic();
    })

<?php //#####################################
            //############ Display
            //#####################################
?>

    function displayTree(callback)
    {
        if(catTreeIsLoading === false){
            catTreeIsLoading = true;
            cat_tree.deleteChildItems(0);
            let e_service_cat_need_opened = Number(getParamUISettings('start_cat_eservice_open'));
            let id_eservice_cat = "<?php echo (int) SCI::getConfigurationValue('SC_ESERVICES_CATEGORY'); ?>";
            if(enalbeDynamicTreeLoading){
                cat_tree.setXMLAutoLoadingBehaviour("id");
                cat_tree.setChildCalcMode("disabled");
                let url_dynamic_load = "index.php?ajax=1&act=cat_category_get&id_lang=" + SC_ID_LANG + "&id_shop=" + shopselection + "&hide_disable_cat=" + hide_disable_cat + "&dynamic=1&" + new Date().getTime();
                cat_tree.setXMLAutoLoading(url_dynamic_load);
                cat_tree.load(url_dynamic_load,function(){
                    catTreeIsLoading = false;
                    if (catselection > 0) {
                        if (callback != '') eval(callback);
                    }
                    drag_disabled_for_sort = true;
                    if(e_service_cat_need_opened === 1) {
                        cat_tree.openItemsDynamic(id_eservice_cat,true);
                    }
                });
            } else {
                cat_tree.load("index.php?ajax=1&act=cat_category_get&id_lang=" + SC_ID_LANG + "&id_shop=" + shopselection + "&hide_disable_cat=" + hide_disable_cat + "&" + new Date().getTime(), function () {
                    catTreeIsLoading = false;
                    if (catselection != 0)
                    {
                        var cat_pos = cat_tree.getIndexById(catselection);
                        if ((cat_pos != undefined && cat_pos !== false && cat_pos != null && cat_pos != "") || cat_pos === 0) {
                            cat_tree.openItem(catselection);
                            cat_tree.selectItem(catselection, true);

                            if (callback != '') eval(callback);
                        }
                        else {
                            cat_grid.clearAll(true);
                        }

                    }
                    else {
                        if (callback != '') eval(callback);
                    }
                    drag_disabled_for_sort = true;
                    if(e_service_cat_need_opened === 1) {
                        cat_tree.openItem(id_eservice_cat);
                    }
                });
            }
        }
    }

    function openDynamic()
    {
        if(enalbeDynamicTreeLoading){
            if (catselection > 0 && catselection_path.length > 0) {
                if (catselection) {
                    cat_tree.openItemsDynamic(catselection_path.join(','),true);
                    cat_tree.selectItem(catselection,true);
                }
            }
        }
    }


    <?php if (SCMS) { ?>
    if(shopselection=="all" || shopselection=="0"){
        displayWarningAllShopsSelected();
        displayTree();
    }
    <?php } ?>
</script>
