<script type="text/javascript">
    var updatedStatusOrders = new Array();
    var updatingStatusOrder = false;
    var actualUpdatingStatusOrder = 0;
    var updatingStatusOrderTimeout=null;
    var explodePacks=getParamUISettings('start_ord_explodePacks')?getParamUISettings('start_ord_explodePacks'):'0';
    lastOrderSelID=0;
    lastOrderRowSelID=0;

    ord_grid=ord_orderPanel.attachGrid();
    ord_grid._name='grid';

    <?php if (SCSG) { ?>
        ord_grid.enableDragAndDrop(true);
    <?php } ?>

    // UISettings
    ord_grid._uisettings_prefix='ord_grid_';
    ord_grid._uisettings_name=ord_grid._uisettings_prefix;
    ord_grid._uisettings_limited=true;
    ord_grid._first_loading=1;

    ord_grid_tb=ord_orderPanel.attachToolbar();
    ord_grid_tb.setIconset('awesome');


    var opts = [['cols123', 'obj', '<?php echo _l('Columns'); ?> 1 + 2 + 3', ''],
        ['cols12', 'obj', '<?php echo _l('Columns'); ?> 1 + 2', ''],
        ['cols23', 'obj', '<?php echo _l('Columns'); ?> 2 + 3', '']
    ];
    ord_grid_tb.addButtonSelect("layout", 100, "", opts, "fad fa-browser blue", "fad fa-browser blue",false,true);
    var gridnames=new Object();
    <?php if (_r('GRI_ORD_VIEW_GRID_LIGHT')) { ?>gridnames['grid_light']='<?php echo _l('Light view', 1); ?>';<?php } ?>
    <?php if (_r('GRI_ORD_VIEW_GRID_LARGE')) { ?>gridnames['grid_large']='<?php echo _l('Large view', 1); ?>';<?php } ?>
    <?php if (_r('GRI_ORD_VIEW_GRID_PICKING')) { ?>gridnames['grid_picking']='<?php echo _l('Picking', 1); ?>';<?php } ?>
    <?php if (_r('GRI_ORD_VIEW_GRID_DELIVERY')) { ?>gridnames['grid_delivery']='<?php echo _l('Delivery', 1); ?>';<?php } ?>
    <?php
    sc_ext::readCustomOrdersGridsConfigXML('gridnames');
    ?>
    var opts = new Array();
    $.each(gridnames, function(index, value) {
        opts[opts.length] = new Array(index, 'obj', value, '');
    });
    // UISettings
    ord_grid._uisettings_name=ord_grid._uisettings_prefix+gridView;
    ord_grid_tb.addButtonSelect("gridview",100, "<?php echo _l('Light view'); ?>", opts, "fad fa-ruler-triangle", "fad fa-ruler-triangle",false,true);
    ord_grid_tb.setItemToolTip('gridview','<?php echo _l('Grid view settings'); ?>');

    var opts = [['filters_reset', 'obj', '<?php echo _l('Reset filters'); ?>', ''],
        ['separator1', 'sep', '', ''],
        ['filters_cols_show', 'obj', '<?php echo _l('Show all columns'); ?>', ''],
        ['filters_cols_hide', 'obj', '<?php echo _l('Hide all columns'); ?>', '']
    ];
    ord_grid_tb.addButtonSelect("filters", 100, "", opts, "fa fa-filter", "fa fa-filter",false,true);
    ord_grid_tb.setItemToolTip('filters','<?php echo _l('Filter options'); ?>');
    ord_grid_tb.addButton("refresh", 100, "", "fa fa-sync green", "fa fa-sync green");
    ord_grid_tb.setItemToolTip('refresh','<?php echo _l('Refresh grid'); ?>');
    if (lightNavigation){
        ord_grid_tb.addButtonTwoState('lightNavigation', 100, "", "fa fa-mouse-pointer", "fa fa-mouse-pointer");
        ord_grid_tb.setItemToolTip('lightNavigation','<?php echo _l('Light navigation (simple click on grid)', 1); ?>');
    }
    <?php if (version_compare(_PS_VERSION_, '1.5.1.0', '>=') && _r('GRI_ORD_MAKEORDER_INTERFACE')) { ?>
        ord_grid_tb.addButton("make_order", 100, "", "fad fa-cart-plus", "fad fa-cart-plus");
        ord_grid_tb.setItemToolTip('make_order','<?php echo _l('Create an order'); ?>');
    <?php } ?>
    ord_grid_tb.addButton("view_order_ps", 100, "", "fad fa-eye", "fad fa-eye");
    ord_grid_tb.setItemToolTip('view_order_ps','<?php echo _l('View selected orders in Prestashop'); ?>');
    ord_grid_tb.addButton("view_customer_ps", 100, "", "fa fa-prestashop", "fa fa-prestashop");
    ord_grid_tb.setItemToolTip('view_customer_ps','<?php echo _l('View selected customers in Prestashop'); ?>');
    ord_grid_tb.addButton("user_go", 100, "", "fad fa-walking orange", "fad fa-walking orange");
    ord_grid_tb.setItemToolTip('user_go','<?php echo _l('login as selected customer on the front office'); ?>');
    ord_grid_tb.addButton("selectall", 100, "", "fa fa-bolt yellow", "fad fa-bolt grey");
    ord_grid_tb.setItemToolTip('selectall','<?php echo _l('Select all'); ?>');
    ord_grid_tb.addButtonTwoState('explodePacks', 100, "", "fa fa-cube", "fa fa-cube green");
    ord_grid_tb.setItemToolTip('explodePacks','<?php echo _l('Display each product from packs in a separate line', 1); ?>');
    ord_grid_tb.setItemState('explodePacks',explodePacks);
    ord_grid_tb.addButtonTwoState('groupByProduct', 100, "", "fa fa-compress-arrows-alt green", "fa fa-compress-arrows-alt green");
    ord_grid_tb.setItemToolTip('groupByProduct','<?php echo _l('Group rows by product', 1); ?>');
    var opts = [
        ['download_invoice', 'obj', '<?php echo _l('Download PDF invoices from selected orders'); ?>', ''],
        ['download_delivery', 'obj', '<?php echo _l('Download PDF delivery slips from selected orders'); ?>', '']
    ];
    ord_grid_tb.addButtonSelect("download", 100, "", opts, "fad fa-file-pdf", "fad fa-file-pdf",false,true);
    ord_grid_tb.setItemToolTip('download','<?php echo _l('Download'); ?>');
    <?php if (_r('ACT_ORD_FAST_EXPORT')) { ?>
        ord_grid_tb.addButton("exportcsv", 70, "", "fad fa-file-csv green", "fad fa-file-csv green");
        ord_grid_tb.setItemToolTip('exportcsv','<?php echo _l('Export grid to clipboard in CSV format for MSExcel with tab delimiter.'); ?>');
    <?php } ?>
    ord_grid_tb.addButton("print", 100, "", "fad fa-print", "fad fa-print");
    ord_grid_tb.setItemToolTip('print','<?php echo _l('Print grid'); ?>');

    getTbSettingsButton(ord_grid_tb, {'grideditor':1,'settings':1}, '', 100);
    ord_grid_tb.addButton("help", 1200, "", "fad fa-question-circle blue", "fad fa-question-circle blue");
    ord_grid_tb.setItemToolTip('help','<?php echo _l('Help'); ?>');
    function gridToolBarOnClick(id){
            if (id.substr(0,5)=='grid_'){

                oldGridView=gridView;
                gridView=id;

                // UISettings
                ord_grid._uisettings_name=ord_grid._uisettings_prefix+gridView;

                ord_grid_tb.setItemText('gridview',gridnames[id]);
                ord_grid_tb.hideItem('explodePacks');
                if(gridView=="grid_picking"){
                    ord_grid_tb.showItem('explodePacks');
                }
                $(document).ready(function(){displayOrders();});
            }
            if (id=='help'){
                <?php echo "window.open('".getScExternalLink('support_orders')."');"; ?>
            }
            if (id=='grideditor'){
                openWinGridEditor('type_orders');
            }
            if (id=='settings'){
                openSettingsWindow('Orders','Interface');
            }
            if (id=='filters_reset')
            {
                for(var i=0,l=ord_grid.getColumnsNum();i<l;i++)
                {
                    if (ord_grid.getFilterElement(i)!=null) ord_grid.getFilterElement(i).value="";
                }
                ord_grid.filterByAll();
                ord_grid_tb.setListOptionSelected('filters','');
            }
            if (id=='filters_cols_show')
            {
                for(i=0,l=ord_grid.getColumnsNum() ; i < l ; i++)
                {
                    ord_grid.setColumnHidden(i,false);
                }
                ord_grid_tb.setListOptionSelected('filters','');
            }
            if (id=='filters_cols_hide')
            {
                idxOrderID=ord_grid.getColIndexById('id_order');
                for(i=0 , l=ord_grid.getColumnsNum(); i < l ; i++)
                {
                    if (i!=idxOrderID)
                    {
                        ord_grid.setColumnHidden(i,true);
                    }else{
                        ord_grid.setColumnHidden(i,false);
                    }
                }
                ord_grid_tb.setListOptionSelected('filters','');
            }
            if (id=='refresh'){
                displayOrders();
            }
            if (id=='print'){
                ord_grid.printView(<?php echo isset($ord_PrintView_Before) ? '\''.addslashes($ord_PrintView_Before).'\'' : '\'\''; ?>,<?php echo isset($ord_PrintView_After) ? '\''.addslashes($ord_PrintView_After).'\'' : '\'\''; ?>);
            }
            if (id=='user_go'){
                var sel=ord_grid.getSelectedRowId();
                if (sel)
                {
                    var tabId=sel.split(',');
                    if (tabId.length==1){
                        idxIdCustomer=ord_grid.getColIndexById('id_customer');
                        id_customer=ord_grid.cells(tabId[0],idxIdCustomer).getValue();
                        var id_shop = ord_grid.getUserData(tabId[0],'id_shop_customer');
                        connectAsUser("<?php echo SCI::getConfigurationValue('SC_SALT'); ?>","<?php echo $sc_agent->id_employee; ?>",id_customer,id_shop);
                    }else{
                        dhtmlx.message({text:'<?php echo addslashes(_l('Alert: You need to select only one order')); ?>',type:'error'});
                    }
                }
            }
            if (id=='view_order_ps'){
                var sel=ord_grid.getSelectedRowId();
                if (sel)
                {
                    var tabId=sel.split(',');
                    for (var i=0;i<tabId.length;i++)
                    {
                        idxIdOrder=ord_grid.getColIndexById('id_order');
                        id_order=ord_grid.cells(tabId[i],idxIdOrder).getValue();
                        idxRefOrder=ord_grid.getColIndexById('ref_order');
                        if (mustOpenBrowserTab){
                            window.open("<?php echo SC_PS_PATH_ADMIN_REL; ?>index.php?tab=AdminOrders&vieworder&id_order="+id_order+"&token=<?php echo $sc_agent->getPSToken('AdminOrders'); ?>");
                        }else{
                            <?php if (version_compare(_PS_VERSION_, '1.6.0.0', '>=')) { ?>
                            wViewOrder = dhxWins.createWindow(i+"wViewOrder"+new Date().getTime(), 50+i*40, 50+i*40, 1250, $(window).height()-75);
                            <?php }
    else
    { ?>
                            wViewOrder = dhxWins.createWindow(i+"wViewOrder"+new Date().getTime(), 50+i*40, 50+i*40, 1000, $(window).height()-75);
                            <?php } ?>
                            wViewOrder.setText('<?php echo _l('Order', 1); ?> '+id_order);
                            wViewOrder.attachURL("<?php echo SC_PS_PATH_ADMIN_REL; ?>index.php?tab=AdminOrders&vieworder&id_order="+id_order+"&token=<?php echo $sc_agent->getPSToken('AdminOrders'); ?>");
                        }
                    }
                }
            }
            if(id=='make_order')
            {
                <?php if ((defined('SC_DEMO') && SC_DEMO)
                            || (defined('SUB6TYP2') && in_array(SUB6TYP2, array(3, 4, 5, 7, 9, 10)))) { ?>
                    if (!dhxWins.isWindow('wMakeOrder')) {
                        wMakeOrder = dhxWins.createWindow('wMakeOrder', 50, 50, $(window).width()-75, $(window).height()-75);
                        wMakeOrder.maximize();
                        wMakeOrder.setText('<?php echo _l('Create an order', 1); ?>');
                        $.get('index.php?ajax=1&act=ord_win-makeorder_init',function(data){
                            $('#jsExecute').html(data);
                        });
                        wMakeOrder.attachEvent('onClose', function(win){
                            wMakeOrder.hide();
                            return false;
                        });
                    }else{
                        $.get('index.php?ajax=1&act=ord_win-makeorder_init',function(data){
                            $('#jsExecute').html(data);
                        });
                        wMakeOrder.show();
                    }
                <?php }
                            else
                            { ?>
                    window.open('<?php echo getScExternalLink('support_creating_order'); ?>');
                <?php } ?>
            }
            if (id=='view_customer_ps'){
                var sel=ord_grid.getSelectedRowId();
                if (sel)
                {
                    var tabId=sel.split(',');
                    for (var i=0;i<tabId.length;i++)
                    {
                        idxIdCustomer=ord_grid.getColIndexById('id_customer');
                        id_customer=ord_grid.cells(tabId[i],idxIdCustomer).getValue();
                        if (mustOpenBrowserTab){
                            window.open("<?php echo SC_PS_PATH_ADMIN_REL; ?>index.php?tab=AdminCustomers&viewcustomer&id_customer="+id_customer+"&token=<?php echo $sc_agent->getPSToken('AdminCustomers'); ?>");
                        }else{
                            <?php if (version_compare(_PS_VERSION_, '1.6.0.0', '>=')) { ?>
                            wViewCustomer = dhxWins.createWindow(i+"wViewCustomer"+new Date().getTime(), 50+i*40, 50+i*40, 1250, $(window).height()-75);
                            <?php }
                            else
                            { ?>
                            wViewCustomer = dhxWins.createWindow(i+"wViewCustomer"+new Date().getTime(), 50+i*40, 50+i*40, 1000, $(window).height()-75);
                            <?php } ?>
                            wViewCustomer.setText('<?php echo _l('Customer', 1); ?> '+id_customer);
                            wViewCustomer.attachURL("<?php echo SC_PS_PATH_ADMIN_REL; ?>index.php?tab=AdminCustomers&viewcustomer&id_customer="+id_customer+"&token=<?php echo $sc_agent->getPSToken('AdminCustomers'); ?>");
                        }
                    }
                    pushOneUsage('grid-bo-link-admincustomers_viewcustomer','ord');
                }
            }
            if (id=='selectall'){
              ord_grid.enableSmartRendering(false);
              ord_grid.selectAll();
              getGridStat();
                lastOrderSelIDs = "";
                idxOrderID=ord_grid.getColIndexById('id_order');
                $.each(ord_grid.getSelectedRowId().split(","), function(index, id) {
                    if(lastOrderSelIDs!="")
                        lastOrderSelIDs = lastOrderSelIDs+",";
                    lastOrderSelIDs = lastOrderSelIDs+ord_grid.cells(id,idxOrderID).getValue();
                });
            }
            if (id.substr(0,8)=='download'){
                selection=ord_grid.getSelectedRowId();
                selOrd=new Array();
                idxIdOrder=ord_grid.getColIndexById('id_order');
                if (selection!='' && selection!=null)
                {
                    selArray=selection.split(',');
                    for(i=0 ; i < selArray.length ; i++)
                    {
                        selOrd[ord_grid.getRowIndex(selArray[i])]=ord_grid.cells(selArray[i],idxIdOrder).getValue();
                    }
                    selOrd_temp = selOrd;
                    selOrd=new Array();
                    i = 0;
                    $.each(selOrd_temp, function(num,id){
                        if(id!=undefined && id!="" && id!=null && id!=0)
                        {
                            selOrd[i] = id;
                            i++;
                        }
                    });
                  window.open('index.php?ajax=1&act=ord_order_download&type='+id+'&orders='+selOrd.getUnique().join(','));
                  /* TODO pour un téléchargement sans limite de nb de fact. (url limitée à 2000 caractères), à compléter pour sauvegarder sur le pc après un POST
                  $.post('index.php?ajax=1&act=ord_order_download',{'type':id,'orders':selOrd.join(',')},function(data){
                      });
                  */
                }
            }
            if (id=='exportcsv'){
                displayQuickExportWindow(ord_grid,1,null,['pdf']);
            }
            if (id=='cols123')
            {
                ord.cells("a").expand();
                ord.cells("a").setWidth(200);
                ord.cells("b").expand();
                dhxLayout.cells('b').expand();
                dhxLayout.cells('b').setWidth(500);
            }
            if (id=='cols12')
            {
                ord.cells("a").expand();
                ord.cells("a").setWidth(200);
                ord.cells("b").expand();
                dhxLayout.cells('b').collapse();
            }
            if (id=='cols23')
            {
                ord.cells("a").collapse();
                ord.cells("b").expand();
                ord.cells("b").setWidth($(document).width()/2);
                dhxLayout.cells('b').expand();
                dhxLayout.cells('b').setWidth($(document).width()/2);
            }
        }
    ord_grid_tb.attachEvent("onClick",gridToolBarOnClick);

    ord_grid.setImagePath('lib/js/imgs/');
    ord_grid.setDateFormat("%Y-%m-%d %H:%i:%s","%Y-%m-%d %H:%i:%s");
    ord_grid.enableMultiselect(true);
    ord_grid_sb=ord_orderPanel.attachStatusBar();
    gridToolBarOnClick(gridView);

    // multiedition context menu
    ord_grid.attachEvent("onBeforeContextMenu", function(rowid,colidx,grid){
            lastColumnRightClicked=colidx;
            idxMsgAvailableNow=ord_grid.getColIndexById('available_now');
            idxMsgAvailableLater=ord_grid.getColIndexById('available_later');
            idxReductionPrice=ord_grid.getColIndexById('reduction_price');
            idxReductionPercent=ord_grid.getColIndexById('reduction_percent');
            idxReductionFrom=ord_grid.getColIndexById('reduction_from');
            idxReductionTo=ord_grid.getColIndexById('reduction_to');
            ord_cmenu.setItemText('object', '<?php echo _l('Order')._l(':'); ?> '+ord_grid.cells(rowid,ord_grid.getColIndexById('id_order')).getValue());
            // paste function
            if (lastColumnRightClicked==clipboardType)
            {
                ord_cmenu.setItemEnabled('paste');
            }else{
                ord_cmenu.setItemDisabled('paste');
            }
            var colType=ord_grid.getColType(colidx);
            if (colType=='ro' || colType=='ron')
            {
                ord_cmenu.setItemDisabled('copy');
                ord_cmenu.setItemDisabled('paste');
            }else{
                ord_cmenu.setItemEnabled('copy');
            }

            if(gridView=="grid_picking")
                ord_cmenu.showItem('open_in_cat');
            else
                ord_cmenu.hideItem('open_in_cat');

            let current_segment_is_manual = parseInt(ord_grid.getUserData("", "manual_segment"));
            if(current_segment_is_manual === 1){
                ord_cmenu.setItemEnabled('delete_from_manual_segment');
            } else {
                ord_cmenu.setItemDisabled('delete_from_manual_segment');
            }
            return true;
        });

    function onEditCell(stage,rId,cInd,nValue,oValue){
        var coltype=ord_grid.getColType(cInd);
        if (stage==1 && this.editor && this.editor.obj && coltype!='txt' && coltype!='txttxt') this.editor.obj.select();
        lastEditedCell=cInd;
<?php
        sc_ext::readCustomOrdersGridsConfigXML('onEditCell');
?>
        idxStatus=ord_grid.getColIndexById('status');
        idxIdOrder=ord_grid.getColIndexById('id_order');


        if(stage==2 && cInd==idxStatus && nValue!=oValue)
        {
            updatedStatusOrders[updatedStatusOrders.length] = {"id":rId,"status":nValue};
            ord_grid_sb.setText('<span style="color:#CC0000"><?php echo _l('Orders status changed:', 1); ?> '+updatedStatusOrders.length+' <?php echo _l('staying.', 1); ?> <?php echo _l('Dont\'t reload page.', 1); ?></span>');
            ord_grid.setRowColor(rId,"d7d7d7");
            ord_grid.lockRow(rId,true);
            ordDataProcessor.setUpdated(rId,false,"updated");

            clearTimeout(updatingStatusOrderTimeout);
            updatingStatusOrderTimeout = setTimeout(function(){updateNextChangedOrder();},3000);

            return false;
        }
        else if (nValue!=oValue){
            ord_grid.setRowColor(rId,'BlanchedAlmond');
            return true;
        }
    }
    ord_grid.attachEvent("onEditCell",onEditCell);

    ord_grid.attachEvent("onDhxCalendarCreated",function(calendar){
            dhtmlXCalendarObject.prototype.langData["<?php echo $user_lang_iso; ?>"] = lang_calendar;
            calendar.loadUserLanguage("<?php echo $user_lang_iso; ?>");
        });
    ordDataProcessorURLBase="index.php?ajax=1&act=ord_order_update&id_lang="+SC_ID_LANG;
    ordDataProcessor = new dataProcessor(ordDataProcessorURLBase);

    ordDataProcessor.attachEvent("onAfterUpdate",function(sid,action,tid,xml){
<?php
    sc_ext::readCustomOrdersGridsConfigXML('onAfterUpdate');
?>
        var dbQty = xml.getAttribute("quantity");
        if (dbQty!='')
        {
            idxQty=ord_grid.getColIndexById('quantity');
            if (idxQty!=null)
                ord_grid.cells(sid,idxQty).setValue(dbQty);
        }
        var doUpdateCombinations = xml.getAttribute("doUpdateCombinations");
        if (doUpdateCombinations==1 && propertiesPanel=='combinations')
        {
            displayCombinations();
        }
    });
    ordDataProcessor.attachEvent("onBeforeUpdate",function(id,status, dat){
<?php
    sc_ext::readCustomOrdersGridsConfigXML('onBeforeUpdate');
?>
        var new_url = ordDataProcessorURLBase;
        if(ord_grid.getColIndexById("id_order_detail")!=undefined)
            new_url = new_url + "&id_order_detail="+ord_grid.cells(id,ord_grid.getColIndexById("id_order_detail")).getValue();
        ordDataProcessor.serverProcessor = new_url;
        return true;
    });
    ordDataProcessor.enableDataNames(true);
    ordDataProcessor.enablePartialDataSend(true);
    ordDataProcessor.setUpdateMode('cell',true);
    ordDataProcessor.setTransactionMode("POST");
    ordDataProcessor.init(ord_grid);

    // Context menu for Grid
    ord_cmenu=new dhtmlXMenuObject();
    ord_cmenu.renderAsContextMenu();
    function onGridOrdContextButtonClick(itemId){
        var testOrderDetails = ord_grid.getColIndexById('id_order_detail');
        if (typeof testOrderDetails !== "undefined")
            idxRowID = ord_grid.getColIndexById('id_order_detail');
        else
            idxRowID = ord_grid.getColIndexById('id_order');
        contextID=ord_grid.contextID.split('_');
        tabId=contextID[0];
        var idxIdOrder = ord_grid.getColIndexById('id_order');
        var valIdOrder = ord_grid.cells(tabId,idxIdOrder).getValue();
        if (itemId=="gopsbo"){

            wModifyOrder = dhxWins.createWindow("wModifyOrder", 50, 50, 1000, $(window).height()-75);
            wModifyOrder.setText('<?php echo _l('Modify the order and close this window to refresh the grid', 1); ?>');
            wModifyOrder.attachURL("<?php echo SC_PS_PATH_ADMIN_REL; ?>index.php?<?php echo version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? 'controller=AdminOrders' : 'tab=AdminOrders'; ?>&vieworder&id_order="+valIdOrder+"&id_lang="+SC_ID_LANG+"&adminlang=1&token=<?php echo $sc_agent->getPSToken('AdminOrders'); ?>");
            wModifyOrder.attachEvent("onClose", function(win){
                        displayOrders();
                        return true;
                    });
        }
        if (itemId=="copyToClipBoard"){
            if (lastColumnRightClicked!=0)
            {
                copyToClipBoard(ord_grid.cells(tabId,lastColumnRightClicked).getTitle());
            }
        }
        if (itemId=="copy"){
            if (lastColumnRightClicked!=0)
            {
                clipboardValue=ord_grid.cells(tabId,lastColumnRightClicked).getValue();
                ord_cmenu.setItemText('paste' , '<?php echo _l('Paste'); ?> '+ord_grid.cells(tabId,lastColumnRightClicked).getTitle().substr(0,30)+'...');
                clipboardType=lastColumnRightClicked;
            }
        }
        if (itemId=="paste"){
            if (lastColumnRightClicked!=0 && clipboardValue!=null && clipboardType==lastColumnRightClicked)
            {
                selection=ord_grid.getSelectedRowId();
                if (selection!='' && selection!=null)
                {
                    idxStatus=ord_grid.getColIndexById('status');
                    idxIdOrder=ord_grid.getColIndexById('id_order');
                    selArray=selection.split(',').sort();
                    for(i=0 ; i < selArray.length ; i++)
                    {
                        ord_grid.cells(selArray[i],lastColumnRightClicked).setValue(clipboardValue);
                        if(lastColumnRightClicked==idxStatus)
                        {
                            updatedStatusOrders[updatedStatusOrders.length] = {"id":selArray[i],"status":clipboardValue};
                            ord_grid_sb.setText('<span style="color:#CC0000"><?php echo _l('Orders status changed:', 1); ?> '+updatedStatusOrders.length+' <?php echo _l('staying.', 1); ?> <?php echo _l('Dont\'t reload page.', 1); ?></span>');
                            ord_grid.setRowColor(selArray[i],"d7d7d7");
                            ord_grid.lockRow(selArray[i],true);

                            clearTimeout(updatingStatusOrderTimeout);
                            updatingStatusOrderTimeout = setTimeout(function(){updateNextChangedOrder();},3000);
                        }
                        else
                        {
                            ord_grid.cells(selArray[i],lastColumnRightClicked).cell.wasChanged=true;
                            onEditCell(null,selArray[i],lastColumnRightClicked,clipboardValue,null);
                            ordDataProcessor.setUpdated(selArray[i],true,"updated");
                        }
                    }
                }
            }
        }
        if (itemId=="open_in_cat"){
            selection=ord_grid.getSelectedRowId();
            if (selection!='' && selection!=null)
            {
                var rowIds = selection.split(",");
                var rowId = rowIds[0];

                var open_cat_grid_ids  = ord_grid.getUserData(rowId, "open_cat_grid");
                if (open_cat_grid_ids!='' && open_cat_grid_ids!=null)
                {
                    var url = "?page=cat_tree&open_cat_grid="+open_cat_grid_ids;
                    window.open(url,'_blank');
                }
            }
        }
        if (itemId=="open_in_cus"){
            selection=ord_grid.getSelectedRowId();
            if (selection!='' && selection!=null)
            {
                let rowIds = selection.split(",");
                let rowId = rowIds[0];
                let idxIdCus = ord_grid.getColIndexById('id_customer');
                let id_cus  = ord_grid.cells(rowId, idxIdCus).getValue();
                if (id_cus!='' && id_cus!=null)
                {
                    let url = "?page=cus_tree&open_cus="+id_cus;
                    window.open(url,'_blank');
                }
            }
        }
        if(itemId==="delete_from_manual_segment"){
            if (confirm('<?php echo _l('Do you really want to remove data from current segment?', 1); ?>')){
                $.post("index.php?ajax=1&act=all_win-segmentation_element_update",{action:'delete_from_id_element',ids:ord_grid.getSelectedRowId(),segmentId:id_selected_segment},function(){
                    displayOrders();
                });
            }
        }
    }
    ord_cmenu.attachEvent("onClick", onGridOrdContextButtonClick);
    var contextMenuXML='<menu absolutePosition="auto" mode="popup" maxItems="8"  globalCss="contextMenu" globalSecondCss="contextMenu" globalTextCss="contextMenuItem">'+
        '<item text="Object" id="object" enabled="false"/>'+
        '<item text="<?php echo _l('Edit in PrestaShop BackOffice'); ?>" id="gopsbo"/>'+
        '<item text="<?php echo _l('Copy to ClipBoard'); ?>" id="copyToClipBoard"/>'+
        '<item text="<?php echo _l('Copy'); ?>" id="copy"/>'+
        '<item text="<?php echo _l('Paste'); ?>" id="paste"/>'+
        '<item text="<?php echo _l('Open this product in SC Catalog'); ?>" id="open_in_cat"/>'+
        '<item text="<?php echo _l('Open this customer in SC Customers'); ?>" id="open_in_cus"/>'+
        '<item text="<?php echo _l('Remove from manual segment'); ?>" id="delete_from_manual_segment"/>'+
    '</menu>';
    ord_cmenu.loadStruct(contextMenuXML);
    ord_grid.enableContextMenu(ord_cmenu);

    //#####################################
    //############ Events
    //#####################################

    // Click on an order
    function doOnRowSelected(idrow){
        idxOrderID=ord_grid.getColIndexById('id_order');
        lastOrderSelID=ord_grid.cells(idrow,idxOrderID).getValue();
        lastOrderRowSelID=idrow;

        lastOrderSelIDs = "";
        let order_list = ord_grid.getSelectedRowId().split(",");
        $.each(order_list, function(index, id) {
            if(lastOrderSelIDs!="")
                lastOrderSelIDs = lastOrderSelIDs+",";
            lastOrderSelIDs = lastOrderSelIDs+ord_grid.cells(id,idxOrderID).getValue();
        });
        let countSelection = order_list.length;
        let propTitle = '<?php echo _l('Properties of order', 1)._l(':'); ?> '+ord_grid.cells(idrow,idxOrderID).getValue();
        if(countSelection>1) {
            propTitle = '<?php echo _l('Properties', 1).' '._l('for', 1); ?> '+ countSelection + ' <?php echo _l('orders'); ?>';
        }

        if (!dhxLayout.cells('b').isCollapsed()
            && propertiesPanel!='orderpsorderpage')
        {
            dhxLayout.cells('b').setText(propTitle);
<?php
    echo eval('?>'.$pluginOrderProperties['doOnOrderRowSelected'].'<?php ');
?>
        }
    }

    ord_grid.attachEvent("onRowSelect",doOnRowSelected);

    // UISettings
    initGridUISettings(ord_grid);

var order_columns = new Array();
var filter_params = "";
var oldFilters = new Object();

ord_grid.attachEvent("onFilterEnd", function(elements){
    old_filter_params = filter_params;
    filter_params = "";
    var nb_cols = ord_grid.getColumnsNum();
    if(nb_cols>0)
    {
        for(var i=0; i<nb_cols; i++)
        {
            var colId=order_columns[i];
            if(ord_grid.getFilterElement(i)!=null
                    && ( colId =="id_order"
                    || colId =="reference" || colId =="invoice_number" || colId =="delivery_number")

                )
            {
                var colValue = ord_grid.getFilterElement(i).value;
                if((colValue!=null && colValue!="") || (oldFilters[colId]!=null && oldFilters[colId]!=""))
                {
                    if(filter_params!="")
                        filter_params = filter_params + ",";
                    filter_params = filter_params + colId+"|||"+colValue;
                }
            }
            if(ord_grid.getFilterElement(i)!=null)
                oldFilters[colId] = ord_grid.getFilterElement(i).value;
        }
    }
    if(filter_params!="" && filter_params!=old_filter_params)
    {
        displayOrders();
    }
    getGridStat();
});
ord_grid.attachEvent("onSelectStateChanged", function(id){
        getGridStat();
    });

ord_grid.attachEvent("onDhxCalendarCreated",function(calendar){
    calendar.setSensitiveRange("2012-01-01",null);
});

ord_grid.attachEvent("onMouseOver",function(rId,cId){
    let idxPdfcol=ord_grid.getColIndexById('pdf');
    let idxTrackingUrlcol=ord_grid.getColIndexById('tracking_url');
    switch(true){
        case cId === idxPdfcol:
        case cId === idxTrackingUrlcol:
            return false;
    }
    return true;
});

function groupByProduct()
{
    idxProductId=ord_grid.getColIndexById('product_id');
    idxProductName=ord_grid.getColIndexById('product_name');
    idxProductQty=ord_grid.getColIndexById('product_quantity');

    var totals_array = new Array();


    for(var i=0; i<idxProductQty; i++)
    {
        if(i==0)
            totals_array[totals_array.length] = "#title";
        else
            totals_array[totals_array.length] = "#cspan";
    }
    totals_array[totals_array.length] = "#stat_total";

    if (ord_grid_groupByProduct=="1")
    {
        <?php if (_s('ORD_ORDER_GROUP_BY') == '1') { ?>
        if(idxProductName!=undefined)
            ord_grid.groupBy(idxProductName,totals_array);
        <?php }
        elseif (_s('ORD_ORDER_GROUP_BY') == '2') { ?>
        if(idxProductId!=undefined)
            ord_grid.groupBy(idxProductId,totals_array);
        <?php } ?>
    }
}

var ord_grid_lightNavigation = 0;
var ord_grid_groupByProduct = 0;
ord_grid_tb.attachEvent("onStateChange",function(id,state){
    if (id=='lightNavigation')
    {
        if (state)
        {
            ord_grid.enableLightMouseNavigation(true);
            ord_grid_lightNavigation = 1;
        }else{
            ord_grid.enableLightMouseNavigation(false);
            ord_grid_lightNavigation = 0;
        }
    }
    if (id=='groupByProduct')
    {
        if (state)
        {
            ord_grid_groupByProduct = 1;
        }else{
            ord_grid_groupByProduct = 0;
            ord_grid.unGroup();
        }
        idxProductId=ord_grid.getColIndexById('product_id');
        if(idxProductId!=undefined)
            displayOrders();//groupByProduct();
    }

    if (id=='explodePacks')
    {
        explodePacks = +state;
        saveParamUISettings('start_ord_explodePacks', explodePacks);
        displayOrders();
    }
});

<?php if (!empty($_GET['open_ord'])) { ?>
var need_ord_filter = 1;
<?php } ?>

function displayOrders(callback)
{
    for(var i=0,l=ord_grid.getColumnsNum();i<l;i++)
    {
        if (ord_grid.getFilterElement(i)!=null && ord_grid.getFilterElement(i).value!='')
            oldFilters[ord_grid.getColumnId(i)]=ord_grid.getFilterElement(i).value;

    }
    ord_grid.editStop(true);
    ord_grid.clearAll(true);
    ord_grid_sb.setText('');
    oldGridView=gridView;
    firstProductsLoading=0;
    ord_grid_sb.setText('<?php echo _l('Loading in progress, please wait...', 1); ?>');

    // recuperation uisettings explodePacks au premier chargement
    var loadUrl = "index.php?ajax=1&act=ord_order_get&status="+statusselection+"&period="+periodselection+"&filter_params="+filter_params+"&view="+gridView+"&id_lang="+SC_ID_LANG+"&explodePacks="+explodePacks+"&"+new Date().getTime();
    <?php if (SCSG) { ?>
    if(id_selected_segment!=undefined && id_selected_segment!=null && id_selected_segment!=0)
        loadUrl = "index.php?ajax=1&act=ord_order_get&id_segment="+id_selected_segment+"&period="+periodselection+"&filter_params="+filter_params+"&view="+gridView+"&id_lang="+SC_ID_LANG+"&"+new Date().getTime();
    <?php } ?>

    ajaxPostCalling(ord_orderPanel, ord_grid, loadUrl,function(data)
    {
        ord_grid.parse(data);
        <?php if (!empty($_GET['open_ord'])) { ?>
        if(need_ord_filter == 1)
        {
            need_ord_filter = 0;
            idxOrderID = ord_grid.getColIndexById('id_order');
            ord_grid.getFilterElement(idxOrderID).value='<?php echo (int) Tools::getValue('open_ord'); ?>';
            ord_grid.filterByAll();
        }
        <?php } ?>

        ord_grid._rowsNum=ord_grid.getRowsNum();

        var limit_smartrendering = 0;
        if(ord_grid.getUserData("", "LIMIT_SMARTRENDERING")!=undefined && ord_grid.getUserData("", "LIMIT_SMARTRENDERING")!=0 && ord_grid.getUserData("", "LIMIT_SMARTRENDERING")!=null)
            limit_smartrendering = ord_grid.getUserData("", "LIMIT_SMARTRENDERING");

        if(limit_smartrendering!=0 && ord_grid._rowsNum > limit_smartrendering && ord_grid_groupByProduct!="1")
            ord_grid.enableSmartRendering(true);
        else
            ord_grid.enableSmartRendering(false);


        order_columns = new Array();
        var nb_cols = ord_grid.getColumnsNum();
        if(nb_cols>0)
        {
            for(var i=0; i<nb_cols; i++)
            {
                var colId=ord_grid.getColumnId(i);
                order_columns[i] = colId;
            }
        }

        lastEditedCell=0;
        lastColumnRightClicked=0;
        for(var i = 0 ; i < ord_grid.getColumnsNum() ; i++)
        {
            if (ord_grid.getFilterElement(i)!=null && oldFilters[ord_grid.getColumnId(i)]!=undefined)
            {
                ord_grid.getFilterElement(i).value=oldFilters[ord_grid.getColumnId(i)];
            }
        }
        ord_grid.filterByAll();

        // UISettings
        loadGridUISettings(ord_grid);
        ord_grid._first_loading=0;
        getGridStat();

        var testOrderDetails = ord_grid.getColIndexById('id_order_detail');
        idxOrderID = ord_grid.getColIndexById('id_order');
        if (typeof testOrderDetails !== "undefined")
        {
            OrderIDs = ord_grid.findCell(lastOrderSelID,idxOrderID,0);
            preserv = 0;
            if (OrderIDs.length)
                for(var i = 0 ; i< OrderIDs.length ; i++){
                    ord_grid.selectRowById(OrderIDs[i][0],preserv,true,false);
                    preserv++;
                }
        }else{
            if (!ord_grid.doesRowExist(lastOrderSelID))
            {
                lastOrderSelID=0;
                lastOrderRowSelID=0;
            }else{
                ord_grid.selectRowById(lastOrderSelID);
            }
        }

        if(filter_params!=undefined && filter_params!=null && filter_params!="" && filter_params!=0)
        {
            if (typeof testOrderDetails !== "undefined")
            {
                if(ord_grid._rowsNum==1)
                    ord_grid.selectAll();
                else
                {
                    var tmp_row_id = 0;
                    var selecting = true;
                    ord_grid.forEachRow(function(id){
                        var id_order = ord_grid.cells(id,idxOrderID).getValue();
                        if(tmp_row_id==0)
                            tmp_row_id = id_order;
                        if(tmp_row_id != id_order)
                            selecting = false;
                    });
                    if(selecting==true)
                        ord_grid.selectAll();
                }
            }
            else
            {
                if(ord_grid._rowsNum==1)
                    ord_grid.selectAll();
            }
        }

        groupByProduct();

        <?php if (_s('APP_DISABLED_COLUMN_MOVE')) { ?>
        ord_grid.enableColumnMove(false);
        <?php } ?>

        <?php sc_ext::readCustomOrdersGridsConfigXML('afterGetRows'); ?>

          if (callback!='') eval(callback);
        });
}
function cconfirm(){
    return confirm('sure?');
}
function getGridStat(){
  filteredRows=ord_grid.getRowsNum();
    selectedRows=(ord_grid.getSelectedRowId()?ord_grid.getSelectedRowId().split(',').length:0);
    ord_grid_sb.setText(ord_grid._rowsNum+' '+(ord_grid._rowsNum>1?'<?php echo _l('orders'); ?>':'<?php echo _l('order'); ?>')+" - <?php echo _l('Filter')._l(':'); ?> "+filteredRows+" - <?php echo _l('Selection')._l(':'); ?> "+selectedRows);
}
function getProductsNum(){
    var i=0;
    ord_grid.forEachRow(function(id){ i++ });
    return i;
}

function updateNextChangedOrder()
{
    if(updatedStatusOrders.length>0 && updatingStatusOrder==false)
    {
        idxStatus=ord_grid.getColIndexById('status');
        $.each(updatedStatusOrders, function(num, order) {
            if((order.id!=undefined && order.id!=null && order.id!=0) && (order.status!=undefined && order.status!=null && order.status!=0))
            {
                updatingStatusOrder = true;
                actualUpdatingStatusOrder = order.id;

                ord_grid.cells(order.id,idxStatus).setValue(order.status);
                ord_grid.cells(order.id,idxStatus).cell.wasChanged=true;
                onEditCell(null,order.id,idxStatus,order.status,null);
                ordDataProcessor.setUpdated(order.id,true,"updated");

                updatedStatusOrders.shift();


                changing = true;
                return false;
            }
            else
            {
                updatedStatusOrders.shift();
            }
        });
    }
}

ordDataProcessor.attachEvent("onAfterUpdate", function(sid, action, tid, xml){
    var is_status = xml.getAttribute("is_status");

    if(actualUpdatingStatusOrder!=undefined && actualUpdatingStatusOrder!=null && actualUpdatingStatusOrder>0)
        ord_grid.lockRow(actualUpdatingStatusOrder,false);

    if(updatedStatusOrders.length<=0)
        getGridStat();
    else
        ord_grid_sb.setText('<span style="color:#CC0000"><?php echo _l('Orders status changed:', 1); ?> '+updatedStatusOrders.length+' <?php echo _l('staying.', 1); ?> <?php echo _l('Dont\'t reload page.', 1); ?></span>');

    if(is_status!=undefined && is_status!=null && is_status==1 && sid==actualUpdatingStatusOrder)
    {
        updatingStatusOrder=false;
        updateNextChangedOrder();
    }
    else
    {
        actualUpdatingStatusOrder = 0;
    }
})
    <?php if (_s('APP_DISABLED_COLUMN_MOVE')) { ?>
    ord_grid.enableColumnMove(false);
    <?php } ?>
</script>
