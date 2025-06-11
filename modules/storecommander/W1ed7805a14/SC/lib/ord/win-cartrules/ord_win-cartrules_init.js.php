<?php echo '<script type="text/javascript">'; ?>
    dhxlCartRules=wCartRules.attachLayout("2U");

    // GRID
    dhxlCartRules.cells('a').hideHeader();

    var cartrules_tb = dhxlCartRules.cells('a').attachToolbar();
      cartrules_tb.setIconset('awesome');

    cartrules_tb.addButton('cartrules_refresh',100,'','fa fa-sync green','fa fa-sync green');
    cartrules_tb.setItemToolTip('cartrules_refresh','<?php echo _l('Refresh', 1); ?>');
    cartrules_tb.addButton("cartrules_add", 100, "", "fa fa-plus-circle green", "fa fa-plus-circle green");
    cartrules_tb.setItemToolTip('cartrules_add','<?php echo _l('Create new cart rule', 1); ?>');
    cartrules_tb.addButton("cartrules_selectall", 100, "", "fa fa-bolt yellow", "fa fa-bolt yellow");
    cartrules_tb.setItemToolTip('cartrules_selectall','<?php echo _l('Select all', 1); ?>');
    cartrules_tb.addButton("delete", 100, "", "fa fa-minus-circle red", "fa fa-minus-circle red");
    cartrules_tb.setItemToolTip('delete','<?php echo _l('This will permanently delete the selected discount vouchers.'); ?>');
    cartrules_tb.addButton("exportcsv", 100, "", "fad fa-file-csv green", "fad fa-file-csv green");
    cartrules_tb.setItemToolTip('exportcsv','<?php echo _l('Export grid to clipboard in CSV format for MSExcel with tab delimiter.'); ?>');

    cartrules_tb.attachEvent("onClick", function (id){
        if(id=="cartrules_refresh")
        {
            displayCartRules();
        }
        if (id=='exportcsv'){
            displayQuickExportWindow(cartrules_grid,1);
        }
        if(id=="cartrules_add")
        {
            if (!dhxWins.isWindow("wNewCartRule"))
            {
                wNewCartRule = dhxWins.createWindow("wNewCartRule", 50, 50, 1000, $(window).height()-75);
                wNewCartRule.button('park').hide();
                wNewCartRule.button('minmax').hide();
                wNewCartRule.setText('<?php echo _l('Create the new cart rule and close this window to refresh the tree', 1); ?>');
                wNewCartRule.attachURL("<?php echo SC_PS_PATH_ADMIN_REL; ?>index.php?controller=AdminCartRules&addcart_rule&token=<?php echo $sc_agent->getPSToken('AdminCartRules'); ?>");
                wNewCartRule.attachEvent("onClose", function(win){
                    displayCartRules();
                    return true;
                });
            }
        }
        if(id=="cartrules_selectall")
        {
            cartrules_grid.selectAll();
            getCartRulesGridStat();
        }
        if(id=="delete") {
            if (confirm('<?php echo _l('Permanently delete the selected discount vouchers', 1); ?>'))
            {
                ids = cartrules_grid.getSelectedRowId();
                $.post("index.php?ajax=1&act=ord_win-cartrules_update&id_lang=" + SC_ID_LANG, {'ids': ids, 'action': 'delete'}, function(data)
                {
                   displayCartRules();
                });
            }
        }
    });

    cartrules_grid = dhxlCartRules.cells('a').attachGrid();
    cartrules_grid._name='cartrules_grid';
    cartrules_grid.setImagePath("lib/js/imgs/");
    cartrules_grid.enableSmartRendering(true);
    cartrules_grid.enableDragAndDrop(false);
    cartrules_grid.enableMultiselect(true);

    // UISettings
    cartrules_grid._uisettings_prefix='cartrules_grid';
    cartrules_grid._uisettings_name=cartrules_grid._uisettings_prefix;
    cartrules_grid._first_loading=1;

    // UISettings
    initGridUISettings(cartrules_grid);

    orderProductDataProcessorURLBase="index.php?ajax=1&act=ord_win-cartrules_update&id_lang="+SC_ID_LANG;
    orderProductDataProcessor = new dataProcessor(orderProductDataProcessorURLBase);
    orderProductDataProcessor.enableDataNames(true);
    orderProductDataProcessor.enablePartialDataSend(false);
    orderProductDataProcessor.setTransactionMode("POST");
    orderProductDataProcessor.setUpdateMode('cell',true);
    orderProductDataProcessor.serverProcessor=orderProductDataProcessorURLBase;
    orderProductDataProcessor.init(cartrules_grid);

    Wcartrules_sb=dhxlCartRules.cells('a').attachStatusBar();

    displayCartRules();

    function displayCartRules()
    {
        cartrules_grid.clearAll(true);
        cartrules_grid.load("index.php?ajax=1&act=ord_win-cartrules_get&id_lang="+SC_ID_LANG,function()
        {
            cartrules_grid._rowsNum=cartrules_grid.getRowsNum();
            getCartRulesGridStat();

            // UISettings
            loadGridUISettings(cartrules_grid);

            // UISettings
            cartrules_grid._first_loading=0;
        });
    }
    function getCartRulesGridStat(){
        filteredRows=cartrules_grid.getRowsNum();
        selectedRows=(cartrules_grid.getSelectedRowId()?cartrules_grid.getSelectedRowId().split(',').length:0);
        Wcartrules_sb.setText(cartrules_grid._rowsNum+' <?php echo _l('discount codes', 1); ?>'+" - <?php echo _l('Filter')._l(':'); ?> "+filteredRows+" - <?php echo _l('Selection')._l(':'); ?> "+selectedRows);
    }

    var lastCartRuleSelected = null;
    cartrules_grid.attachEvent("onRowSelect",function (id_cart_rule){
        lastCartRuleSelected = id_cart_rule;
    });
    cartrules_grid.attachEvent("onFilterEnd", function(elements){
        getCartRulesGridStat();
    });
    cartrules_grid.attachEvent("onSelectStateChanged", function(id){
        getCartRulesGridStat();
    });

    // Context menu for MultiShops Info Product grid
    cartrules_cmenu=new dhtmlXMenuObject();
    cartrules_cmenu.renderAsContextMenu();

    clipboardType_CartRule = null;
    function onGridCartRuleContextButtonClick(itemId){
        tabId=cartrules_grid.contextID.split('_');
        tabId=tabId[0];
        if (itemId=="copy"){
            if (lastColumnRightClicked_CartRule!=0)
            {
                clipboardValue_CartRule=cartrules_grid.cells(tabId,lastColumnRightClicked_CartRule).getValue();
                cartrules_cmenu.setItemText('paste' , '<?php echo _l('Paste'); ?> '+cartrules_grid.cells(tabId,lastColumnRightClicked_CartRule).getTitle());
                clipboardType_CartRule=lastColumnRightClicked_CartRule;
            }
        }
        if (itemId=="paste"){
            if (lastColumnRightClicked_CartRule!=0 && clipboardValue_CartRule!=null && clipboardType_CartRule==lastColumnRightClicked_CartRule)
            {
                selection=cartrules_grid.getSelectedRowId();
                if (selection!='' && selection!=null)
                {
                    selArray=selection.split(',');
                    for(i=0 ; i < selArray.length ; i++)
                    {
                        if (cartrules_grid.getColumnId(lastColumnRightClicked_CartRule).substr(0,5)!='attr_')
                        {
                            cartrules_grid.cells(selArray[i],lastColumnRightClicked_CartRule).setValue(clipboardValue_CartRule);
                            cartrules_grid.cells(selArray[i],lastColumnRightClicked_CartRule).cell.wasChanged=true;

                            orderProductDataProcessor.setUpdated(selArray[i],true,"updated");
                        }
                    }
                }
            }
        }
    }
    cartrules_cmenu.attachEvent("onClick", onGridCartRuleContextButtonClick);
    var contextMenuXML='<menu absolutePosition="auto" mode="popup" maxItems="8"  globalCss="contextMenu" globalSecondCss="contextMenu" globalTextCss="contextMenuItem">'+
        '<item text="Object" id="object" enabled="false"/>'+
        '<item text="<?php echo _l('Copy'); ?>" id="copy"/>'+
        '<item text="<?php echo _l('Paste'); ?>" id="paste"/>'+
        '</menu>';
    cartrules_cmenu.loadStruct(contextMenuXML);
    cartrules_grid.enableContextMenu(cartrules_cmenu);

    cartrules_grid.attachEvent("onBeforeContextMenu", function(rowid,colidx,grid){
        var disableOnCols=new Array(
            cartrules_grid.getColIndexById('id_cart_rule'),
            cartrules_grid.getColIndexById('name'),
            cartrules_grid.getColIndexById('code'),
            cartrules_grid.getColIndexById('minimum_amount'),
            cartrules_grid.getColIndexById('filter_shop')
        );
        if (in_array(colidx,disableOnCols))
        {
            return false;
        }
        lastColumnRightClicked_CartRule=colidx;
        cartrules_cmenu.setItemText('object', '<?php echo _l('Discount voucher:'); ?> '+cartrules_grid.cells(rowid,cartrules_grid.getColIndexById('name')).getTitle());
        if (lastColumnRightClicked_CartRule==clipboardType_CartRule)
        {
            cartrules_cmenu.setItemEnabled('paste');
        }else{
            cartrules_cmenu.setItemDisabled('paste');
        }
        return true;

    });

    function hideCartRulesSubpropertiesItems()
    {
        ord_wincartrules_prop_tb.forEachItem(function(itemId){
            if(itemId!="ord_prop_subproperties") {
                ord_wincartrules_prop_tb.hideItem(itemId);
            }
        });
    }

// Properties
    dhxlCartRules.cells('b').setText('<?php echo _l('Properties', 1); ?>');
    dhxlCartRules_prop = dhxlCartRules.cells('b');
    var ord_wincartrules_prop_tb = dhxlCartRules_prop.attachToolbar();
      ord_wincartrules_prop_tb.setIconset('awesome');
    actual_wincartrules_subproperties = "ord_prop_psdiscount";

    var opts = new Array();
    ord_wincartrules_prop_tb.addButtonSelect("ord_prop_subproperties", 100, "<?php echo _l('Edit the discount voucher'); ?>", opts, "fad fa-tags", "fa fa-search blue",false,true);

    <?php
    $parent_folder = 'lib/ord/win-cartrules';
    $prefix_file = 'ord_win-cartrules_';
    @$sub_files = scandir(SC_DIR.$parent_folder);
    foreach ($sub_files as $sub_item)
    {
        if ($sub_item != '.' && $sub_item != '..')
        {
            if (is_dir(SC_DIR.$parent_folder.'/'.$sub_item) && file_exists(SC_DIR.$parent_folder.'/'.$sub_item.'/'.$prefix_file.$sub_item.'_init.js.php'))
            {
                require_once SC_DIR.$parent_folder.'/'.$sub_item.'/'.$prefix_file.$sub_item.'_init.js.php';
            }
            elseif (is_dir(SC_DIR.$parent_folder.'/'.$sub_item) && file_exists(SC_DIR.$parent_folder.'/'.$sub_item.'/'.$prefix_file.$sub_item.'_init.php'))
            {
                require_once SC_DIR.$parent_folder.'/'.$sub_item.'/'.$prefix_file.$sub_item.'_init.php';
            }
        }
    }
    ?>
    initCartRulesPsEdit();
<?php echo '</script>'; ?>