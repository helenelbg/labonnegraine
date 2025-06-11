<script type="text/javascript">
<?php
#####################################
############ Create interface
#####################################
?>
    const dhxLayout = new dhtmlXLayoutObject(document.body, "2U");
    dhxLayout.cells('b').setText('<?php echo _l('Properties', 1); ?>');

    var start_supplier_size_prop = getParamUISettings('start_supplier_size_prop');
    if (start_supplier_size_prop == null || start_supplier_size_prop <= 0 || start_supplier_size_prop == "") {
        start_supplier_size_prop = 450;
    }
    dhxLayout.cells('b').setWidth(start_supplier_size_prop);
    dhxLayout.attachEvent("onPanelResizeFinish", function () {
        saveParamUISettings('start_supplier_size_prop', dhxLayout.cells('b').getWidth())
    });

    var dhxLayoutStatus = dhxLayout.attachStatusBar();
    layoutStatusText = '<div id="layoutstatusqueue" style="float: right; color: #ff0000; font-weight: bold;">' + loader_gif + ' <span></span></div>' + "<?php echo SC_COPYRIGHT.' '.(SC_DEMO ? '- Demonstration' : '- '._l('License').' '.SCLIMREF.' - '.$SC_SHOP_SUPPLIER_COUNT.' '._l('supplier')).' - Version '.SC_VERSION.(SC_BETA ? ' BETA' : '').(SC_GRIDSEDITOR_INSTALLED ? ' GE'.(SC_GRIDSEDITOR_PRO_INSTALLED ? 'P' : '') : '').' (PS '._PS_VERSION_.(defined('_PS_CACHE_ENABLED_') && _PS_CACHE_ENABLED_ ? ' (cache)' : '').' - PHP '.sc_phpversion().') '.$NOTEPAD_BUTTON.' <span id=\"layoutstatusloadingtime\"></span>'; ?>";
    dhxLayoutStatus.setText(layoutStatusText);

    <?php createMenu(); ?>
    supplierselection = 0;
    shopselection = $.cookie('sc_shop_selected') * 1;
    shop_list = $.cookie('sc_shop_list');
    last_supplierID = 0;
    propertiesPanel = '<?php echo _s('SUP_SUPPLIER_PROP_GRID_DEFAULT'); ?>';
    tree_mode = 'single';
    displaySuppliersFrom='all';
    copytocateg = false;
    dragdropcache = '';
    draggedSupplier = 0;
    clipboardValue = null;
    clipboardType = null;

<?php
#####################################
############ Categories toolbar
#####################################
?>

    gridView = '<?php echo _s('SUP_SUPPLIER_GRID_DEFAULT'); ?>';
    oldGridView = '';

<?php
echo SCI::getShopUrlArrayJs();
if (SCMS)
{
    ?>
    const sup = new dhtmlXLayoutObject(dhxLayout.cells("a"), "2U");
    sup_firstcolcontent = sup.cells("a").attachLayout("1C");
    sup_storePanel = sup_firstcolcontent.cells('a');
    sup_supplierPanel = sup.cells('b');


<?php
#####################################
############ Boutiques Tree
#####################################
?>
    var has_shop_restrictions = false;

    sup.cells("a").setText('<?php echo _l('Stores', 1); ?>');
    sup.cells("a").showHeader();
    sup_storePanel.hideHeader();
    let start_supplier_size_store = getParamUISettings('start_supplier_size_store');
    if (start_supplier_size_store == null || start_supplier_size_store <= 0 || start_supplier_size_store == "") {
        start_supplier_size_store = 250;
    }
    sup_storePanel.setWidth(start_supplier_size_store);
    sup_firstcolcontent.attachEvent("onPanelResizeFinish", function (names) {
        $.each(names, function (num, name) {
            if (name == "a") {
                saveParamUISettings('start_supplier_size_store', sup_storePanel.getWidth())
            }
        });
    });

    sup_shoptree = sup_storePanel.attachTree();
    sup_shoptree._name = 'shoptree';
    sup_shoptree.autoScroll = false;
    sup_shoptree.setImagePath('lib/js/imgs/dhxtree_material/');
    sup_shoptree.enableSmartXMLParsing(true);
    sup_shoptree.enableCheckBoxes(true, false);

    const sup_ShoptreeTB = sup_storePanel.attachToolbar();
    sup_ShoptreeTB.setIconset('awesome');
    sup_ShoptreeTB.addButton("help", 0, "", "fad fa-question-circle blue", "fad fa-question-circle blue");
    sup_ShoptreeTB.setItemToolTip('help', '<?php echo _l('Help'); ?>');
    sup_ShoptreeTB.attachEvent("onClick", function (id) {
        switch(id) {
            case 'help':
                let display = "";
                let update = [];
                if (shopselection > 0) {
                    display = sup_shoptree.getItemText(shopselection);
                } else if (shopselection == 0) {
                    display = sup_shoptree.getItemText("all");
                }

                for(const id of $.cookie('sc_shop_list').split(",")){
                    if (id !== "all" && Number(id) !== 0 && id.search("G") < 0) {
                        update.push(sup_shoptree.getItemText(id));
                    }
                }
                let msg = '<strong><?php echo addslashes(_l('Display:')); ?></strong> ' + display + '<br/><br/><strong><?php echo addslashes(_l('Update:')); ?></strong> ' + update.join(',');
                dhtmlx.message({text: msg, type: 'info', expire: 10000});
                break;
        }
    });


    displayShopTree();

    function checkWhenSelection(idshop) {
        let allShops_item_found = sup_shoptree.getIndexById('all');
        let children = (allShops_item_found !== null ? sup_shoptree.getAllSubItems("all").split(",") : sup_shoptree.getAllChildless().split(","));
        if ((idshop == 'all' || idshop == 0) && has_shop_restrictions == 0) {
            sup_shoptree.setCheck("all", 1);
            $.each(children, function (index, id) {
                sup_shoptree.setCheck(id, 1);
                sup_shoptree.disableCheckbox(id, 1);
            });
        } else {
            $.each(children, function (index, id) {
                sup_shoptree.disableCheckbox(id, 0);
            });
            if (idshop > 0) {
                sup_shoptree.setCheck(idshop, 1);
                sup_shoptree.disableCheckbox(idshop, 1);
            }
        }
    }

    function deSelectParents(idshop) {
        if (sup_shoptree.getParentId(idshop) != "") {
            let parent_id = sup_shoptree.getParentId(idshop);
            sup_shoptree.setCheck(parent_id, 0);
            deSelectParents(parent_id);
        }
    }

    function saveCheckSelection() {
        let checked = sup_shoptree.getAllChecked();
        if (shopselection == "all" || shopselection == "0") {
            let allShops_item_found = sup_shoptree.getIndexById('all');
            if (allShops_item_found !== null) {
                checked = sup_shoptree.getAllSubItems("all");
            } else {
                checked = sup_shoptree.getAllChildless();
            }
        }

        let cookie_checked = [];

        for(const id of checked.split(",")) {
            if (id !== "all" && id.search("G") < 0 && id !== "") {
                cookie_checked.push(Number(id));
            }
        }

        if (shopselection !== undefined && shopselection !== "" && shopselection > 0) {
            cookie_checked.push(Number(shopselection));
        }

        $.cookie('sc_shop_list', cookie_checked.join(','), {expires: 60,path: cookiePath});
    }

    function displayShopTree(callback) {
        sup_shoptree.deleteChildItems(0);
        sup_shoptree.load("index.php?ajax=1&act=sup_shop_get&id_lang=" + SC_ID_LANG + "&" + new Date().getTime(), function () {
            has_shop_restrictions = sup_shoptree.getUserData(0, "has_shop_restrictions");

            if (shopselection != null && shopselection != undefined)
                checkWhenSelection(shopselection);
            if (shop_list != null && shop_list != "") {
                for(const id of shop_list.split(",")) {
                    sup_shoptree.setCheck(id, 1);
                }
            }
            if (shopselection != null && shopselection != undefined && shopselection != 0) {
                sup_shoptree.openItem(shopselection);
                sup_shoptree.selectItem(shopselection, true);
            }

            if (has_shop_restrictions) {
                selected = sup_shoptree.getSelectedItemId();
                if (selected == undefined || selected == null || selected == "") {
                    let all = sup_shoptree.getAllSubItems(0);
                    if (all != undefined && all != null && all != "") {
                        let id_to_select = "";
                        for(const id of all.split(",")) {
                            if (id.search("G") < 0) {
                                if (id_to_select == "") {
                                    id_to_select = id;
                                }
                            }
                        }
                        shopselection = id_to_select;
                        sup_shoptree.openItem(shopselection);
                        sup_shoptree.selectItem(shopselection, true);
                        $.cookie('sc_shop_selected', shopselection, {expires: 60,path: cookiePath});
                    }
                }
            }

            if (callback != '') {
                eval(callback);
            }
            sup_shoptree.openAllItems(0);
        });
    }

    sup_shoptree.attachEvent("onClick", onClickShopTree);

    function onClickShopTree(idshop, param, callback) {
        if (idshop[0] == 'G') {
            sup_shoptree.clearSelection();
            sup_shoptree.selectItem(shopselection, false);
            return false;
        }
        if (idshop == 'all') {
            idshop = 0;
        }
        checkWhenSelection(idshop);
        if (idshop != shopselection) {
            if (shopselection != 0 && idshop != 0 && idshop[0] != 'G')
                sup_shoptree.setCheck(shopselection, 0);
            else if (shopselection == 0 && idshop != 0 && idshop[0] != 'G') {
                if (has_shop_restrictions == 0) {
                    sup_shoptree.setCheck("all", 0);
                    for(const id of sup_shoptree.getAllSubItems("all").split(",")) {
                        if (id != idshop) {
                            sup_shoptree.setCheck(id, 0);
                        }
                    }
                } else {
                    sup_shoptree.setCheck("all", 0);
                    for(const id of sup_shoptree.getAllSubItems(0).split(",")) {
                        if (id != idshop) {
                            sup_shoptree.setCheck(id, 0);
                        }
                    }
                }

            }
            shopselection = idshop;
            $.cookie('sc_shop_selected', shopselection, {expires: 60,path: cookiePath});
        } else {
            var callback_refresh = "";
            if (callback != undefined && callback != null && callback != "") {
                callback_refresh = callback_refresh + callback;
            }
        }
        saveCheckSelection();
    }

    sup_shoptree.attachEvent("onCheck", function (idshop, state) {
        if (idshop == "all") {
            var children = sup_shoptree.getAllSubItems("all").split(",");
            for(const id of sup_shoptree.getAllSubItems("all").split(",")) {
                sup_shoptree.setCheck(id, state);
            }
        } else if (idshop.search("G") >= 0) {
            for(const id of sup_shoptree.getAllSubItems(idshop).split(",")) {
                sup_shoptree.setCheck(id, state);
            }
        } else {
            deSelectParents(idshop);
        }
        saveCheckSelection();
    });

<?php
#####################################
############ Context menu
#####################################
?>
    sup_shop_cmenu_tree = new dhtmlXMenuObject();
    sup_shop_cmenu_tree.renderAsContextMenu();

    function onTreeContextButtonClickForShop(itemId) {
        if (itemId == "goshop") {
            tabId = sup_shoptree.contextID;
            let supCatActive = (sup_shoptree.getItemImage(tabId, 0, false) == 'catalog.png' ? 0 : 1);
            if (supCatActive == 1) {
                return false;
            }
            <?php
            if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
            {
                if (SCMS)
                {
                    ?>
            if (shopUrls[tabId] != undefined && shopUrls[tabId] != "" && shopUrls[tabId] != null) {
                window.open(shopUrls[tabId]);
            }
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

    sup_shop_cmenu_tree.attachEvent("onClick", onTreeContextButtonClickForShop);

    var contextMenuXML = '<menu absolutePosition="auto" mode="popup" maxItems="8"  globalCss="contextMenu" globalSecondCss="contextMenu" globalTextCss="contextMenuItem">' +
        '<item text="Object" id="object" enabled="false"/>' +
        '<item text="<?php echo _l('See shop'); ?>" id="goshop"/>' +
        '</menu>';
    sup_shop_cmenu_tree.loadStruct(contextMenuXML);
    sup_shoptree.enableContextMenu(sup_shop_cmenu_tree);

    sup_shoptree.attachEvent("onBeforeContextMenu", function (itemId) {

        let display_id = itemId;
        let display_text = '<?php echo _l('Shop:'); ?> ';
        if (itemId == "all") {
            return false;
        } else if (itemId.search("G") >= 0) {
            display_id = itemId.replace("G", "");
            display_text = '';
        }

        sup_shop_cmenu_tree.setItemText('object', 'ID' + display_id + ': ' + display_text + sup_shoptree.getItemText(itemId));

        <?php if (SCMS) { ?>
        if (shopUrls[itemId] != undefined && shopUrls[itemId] != "" && shopUrls[itemId] != null) {
            sup_shop_cmenu_tree.setItemEnabled('goshop');
        } else {
            sup_shop_cmenu_tree.setItemDisabled('goshop');
        }
        <?php } ?>

        return true;
    });
<?php
}
else
{ ?>
    const sup = new dhtmlXLayoutObject(dhxLayout.cells("a"), "1C");
    sup_supplierPanel = sup.cells('a');
<?php
}
?>

</script>