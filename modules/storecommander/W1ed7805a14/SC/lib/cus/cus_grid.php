<script type="text/javascript">
    cus_grid=cus_customerPanel.attachGrid();
    cus_grid._name='grid';

    cus_grid.enableDistributedParsing(true,1000,100);

    <?php if (SCSG) { ?>
        cus_grid.enableDragAndDrop(true);
    <?php } ?>

    // UISettings
    cus_grid._uisettings_prefix='cus_grid_';
    cus_grid._uisettings_name=cus_grid._uisettings_prefix+gridView;
    cus_grid._uisettings_limited=true;
    cus_grid._first_loading=1;

    cus_grid.setColumnIds("id_customer,id_gender,firstname,lastname,email,active,newsletter,optin,cart_lang,date_add,date_connection");
    
    cus_grid_tb=cus_customerPanel.attachToolbar();
    cus_grid_tb.setIconset('awesome');
    var opts = [['cols123', 'obj', '<?php echo _l('Columns'); ?> 1 + 2 + 3', ''],
        ['cols12', 'obj', '<?php echo _l('Columns'); ?> 1 + 2', ''],
        ['cols23', 'obj', '<?php echo _l('Columns'); ?> 2 + 3', '']
    ];
    cus_grid_tb.addButtonSelect("layout", 100, "", opts, "fad fa-browser blue", "fad fa-browser blue",false,true);
    var gridnames=new Object();
    <?php if (_r('GRI_CUS_VIEW_GRID_LIGHT')) { ?>gridnames['grid_light']='<?php echo _l('Light view', 1); ?>';<?php } ?>
    <?php if (_r('GRI_CUS_VIEW_GRID_LARGE')) { ?>gridnames['grid_large']='<?php echo _l('Large view', 1); ?>';<?php } ?>
    <?php if (_r('GRI_CUS_VIEW_GRID_ADDRESS')) { ?>gridnames['grid_address']='<?php echo _l('Addresses', 1); ?>';<?php } ?>
    <?php if (_r('GRI_CUS_VIEW_GRID_CONVERT')) { ?>gridnames['grid_convert']='<?php echo _l('Convert', 1); ?>';<?php } ?>
    <?php
    sc_ext::readCustomCustomersGridsConfigXML('gridnames');
    ?>
    var opts = new Array();
    $.each(gridnames, function(index, value) {
        opts[opts.length] = new Array(index, 'obj', value, '');
    });
    cus_grid_tb.addButtonSelect("gridview", 100, "<?php echo _l('Light view'); ?>", opts, "fad fa-ruler-triangle", "fad fa-ruler-triangle",false,true);
    cus_grid_tb.setItemToolTip('gridview','<?php echo _l('Grid view settings'); ?>');
    var opts = [['filters_reset', 'obj', '<?php echo _l('Reset filters', 1); ?>', ''],
        ['separator1', 'sep', '', ''],
        ['filters_cols_show', 'obj', '<?php echo _l('Show all columns', 1); ?>', ''],
        ['filters_cols_hide', 'obj', '<?php echo _l('Hide all columns', 1); ?>', '']
    ];
    cus_grid_tb.addButtonSelect("filters", 100, "", opts, "fa fa-filter", "fa fa-filter",false,true);
    cus_grid_tb.setItemToolTip('filters','<?php echo _l('Filter options', 1); ?>');
    cus_grid_tb.addButton("refresh", 100, "", "fa fa-sync green", "fa fa-sync green");
    cus_grid_tb.setItemToolTip('refresh','<?php echo _l('Refresh grid', 1); ?>');
    if (lightNavigation){
        cus_grid_tb.addButtonTwoState('lightNavigation', 100, "", "fa fa-mouse-pointer", "fa fa-mouse-pointer");
        cus_grid_tb.setItemToolTip('lightNavigation','<?php echo _l('Light navigation (simple click on grid)', 1); ?>');
    }
    cus_grid_tb.addButton("add_ps", 100, "", "fa fa-prestashop", "fa fa-prestashop");
    cus_grid_tb.setItemToolTip('add_ps','<?php echo _l('Create new customer with the PrestaShop form', 1); ?>');
    cus_grid_tb.addButton("view_customer_ps", 100, "", "fad fa-eye", "fad fa-eye");
    cus_grid_tb.setItemToolTip('view_customer_ps','<?php echo _l('View selected customers in Prestashop', 1); ?>');
    <?php if (_r('ACT_CUS_LOGIN_AS_CUSTOMER')) { ?>
    cus_grid_tb.addButton("user_go", 100, "", "fad fa-walking orange", "fad fa-walking orange");
    cus_grid_tb.setItemToolTip('user_go','<?php echo _l('login as selected customer on the front office', 1); ?>');
    <?php } ?>
    cus_grid_tb.addButton("add_discount", 100, "", "fad fa-tags", "fad fa-tags");
    cus_grid_tb.setItemToolTip('add_discount','<?php echo _l('Create a new discount code', 1); ?>');
    <?php if (version_compare(_PS_VERSION_, '1.5.1.0', '>=') && _r('GRI_ORD_MAKEORDER_INTERFACE')) { ?>
    cus_grid_tb.addButton("make_order", 100, "", "fad fa-cart-plus", "fad fa-cart-plus");
    cus_grid_tb.setItemToolTip('make_order','<?php echo _l('Create an order'); ?>');
    <?php } ?>
    cus_grid_tb.addButton("send_mail", 100, "", "fad fa-paper-plane green", "fad fa-paper-plane green");
    cus_grid_tb.setItemToolTip('send_mail','<?php echo _l('Send mail to customer'); ?>');
    cus_grid_tb.addButton("delete", 100, "", "fa fa-minus-circle red", "fa fa-minus-circle red");
    cus_grid_tb.setItemToolTip('delete','<?php echo _l('Delete customer', 1); ?>');
    cus_grid_tb.addButton("selectall", 100, "", "fa fa-bolt yellow", "fad fa-bolt grey");
    cus_grid_tb.setItemToolTip('selectall','<?php echo _l('Select all products', 1); ?>');
    <?php if (_r('ACT_CUS_FAST_EXPORT')) { ?>
    cus_grid_tb.addButton("exportcsv", 100, "", "fad fa-file-csv green", "fad fa-file-csv green");
    cus_grid_tb.setItemToolTip('exportcsv','<?php echo _l('Export grid to clipboard in CSV format for MSExcel with tab delimiter.', 1); ?>');
    <?php } ?>
    cus_grid_tb.addButton("print", 100, "", "fad fa-print", "fad fa-print");
    cus_grid_tb.setItemToolTip('print','<?php echo _l('Print grid', 1); ?>');
    getTbSettingsButton(cus_grid_tb, {'grideditor':1,'settings':1}, '', 100);
    cus_grid_tb.addButton("help", 1200, "", "fad fa-question-circle blue", "fad fa-question-circle blue");
    cus_grid_tb.setItemToolTip('help','<?php echo _l('Help', 1); ?>');

    function gridToolBarOnClick(id){
            if (id.substr(0,5)=='grid_'){
                oldGridView=gridView;
                gridView=id;
                customer_columns = new Array();
                /* TODO a revoir avec filtres qui correspondent bien aux colonnes */
                //filter_params = "";
                //oldFilters = new Array();

                // UISettings
                cus_grid._uisettings_name=cus_grid._uisettings_prefix+gridView;

                cus_grid_tb.setItemText('gridview',gridnames[id]);
                $(document).ready(function(){displayCustomers();});
            }
            if (id=='help'){
                <?php echo "window.open('".getScExternalLink('support_customers')."');"; ?>
            }
            if (id=='grideditor'){
                openWinGridEditor('type_customers');
            }
            if (id=='settings'){
                openSettingsWindow('Customers','Interface');
            }
            if (id=='filters_reset')
            {
                for(var i=0,l=cus_grid.getColumnsNum();i<l;i++)
                {
                    if (cus_grid.getFilterElement(i)!=null) cus_grid.getFilterElement(i).value="";
                }
                cus_grid.filterByAll();
                cus_grid_tb.setListOptionSelected('filters','');
                oldFilters = new Array();
                //displayCustomers();
            }
            if (id=='filters_cols_show')
            {
                for(i=0,l=cus_grid.getColumnsNum() ; i < l ; i++)
                {
                    cus_grid.setColumnHidden(i,false);
                }
                cus_grid_tb.setListOptionSelected('filters','');
            }
            if (id=='filters_cols_hide')
            {
                idxCustomerID=cus_grid.getColIndexById('id_customer');
                idxCustomerEmail=cus_grid.getColIndexById('email');
                for(i=0 , l=cus_grid.getColumnsNum(); i < l ; i++)
                {
                    if (i!=idxCustomerID && i!=idxCustomerEmail)
                    {
                        cus_grid.setColumnHidden(i,true);
                    }else{
                        cus_grid.setColumnHidden(i,false);
                    }
                }
                cus_grid_tb.setListOptionSelected('filters','');
            }
            if (id=='refresh'){
                displayCustomers();
            }
            if (id=='print'){
                cus_grid.printView();
            }
            if (id=='user_go'){
                let sel=getSelectedCustomerId();
                if (sel)
                {
                    let tabId=sel.split(',');
                    if (tabId.length === 1){
                        let id_customer = tabId[0];
                        let id_row= tabId[0];
                        if(cus_grid.getColIndexById('id_address') !== undefined){
                            id_row= cus_grid.getSelectedRowId();
                        }
                        let id_shop = cus_grid.getUserData(id_row,'id_shop_customer');
                        connectAsUser("<?php echo SCI::getConfigurationValue('SC_SALT'); ?>","<?php echo $sc_agent->id_employee; ?>",id_customer,id_shop);
                    }else{
                        dhtmlx.message({text:'<?php echo addslashes(_l('Alert: You need to select only one customer')); ?>',type:'error'});
                    }
                }
            }
            if (id=='view_customer_ps'){
                let sel = getSelectedCustomerId();
                if (sel)
                {
                    for (const id_customer of sel.split(','))
                    {
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
                }
            }
            if (id=='add_discount'){
                if (mustOpenBrowserTab){
<?php
if (version_compare(_PS_VERSION_, '1.5.0.0', '<'))
    {
        ?>
                    window.open("<?php echo SC_PS_PATH_ADMIN_REL; ?>index.php?tab=AdminDiscounts&adddiscount&token=<?php echo $sc_agent->getPSToken('AdminDiscounts'); ?>");
<?php
    }
    else
    {
        ?>
                    window.open("<?php echo SC_PS_PATH_ADMIN_REL; ?>index.php?tab=AdminCartRules&addcart_rule&token=<?php echo $sc_agent->getPSToken('AdminCartRules'); ?>");
<?php
    }
?>
                }else{
                    wCreateDiscountCode = dhxWins.createWindow("wCreateDiscountCode"+new Date().getTime(), 50, 50, 1000, $(window).height()-75);
                    wCreateDiscountCode.setText('<?php echo _l('Create discount code', 1); ?>');
<?php
if (version_compare(_PS_VERSION_, '1.5.0.0', '<'))
{
    ?>
                    wCreateDiscountCode.attachURL("<?php echo SC_PS_PATH_ADMIN_REL; ?>index.php?tab=AdminDiscounts&adddiscount&token=<?php echo $sc_agent->getPSToken('AdminDiscounts'); ?>");
<?php
}
    else
    {
        ?>
                    wCreateDiscountCode.attachURL("<?php echo SC_PS_PATH_ADMIN_REL; ?>index.php?tab=AdminCartRules&addcart_rule&token=<?php echo $sc_agent->getPSToken('AdminCartRules'); ?>");
<?php
    }
?>
                    wCreateDiscountCode.attachEvent("onClose", function(win){
                        displayCustomers();
                        return true;
                    });
                }
            }
            if (id=='add_ps'){
                if (mustOpenBrowserTab){
                    window.open("<?php echo SC_PS_PATH_ADMIN_REL; ?>index.php?tab=AdminCustomers&addcustomer&token=<?php echo $sc_agent->getPSToken('AdminCustomers'); ?>");
                }else{
                    if (!dhxWins.isWindow("wNewCustomer"))
                    {
                        <?php if (version_compare(_PS_VERSION_, '1.6.0.0', '>=')) { ?>
                        wNewCustomer = dhxWins.createWindow("wNewCustomer", 50, 50, 1250, $(window).height()-75);
                        <?php }
else
{ ?>
                        wNewCustomer = dhxWins.createWindow("wNewCustomer", 50, 50, 1000, $(window).height()-75);
                        <?php } ?>
                        wNewCustomer.setText('<?php echo _l('Create the new customer and close this window to refresh the grid', 1); ?>');
                        wNewCustomer.attachURL("<?php echo SC_PS_PATH_ADMIN_REL; ?>index.php?tab=AdminCustomers&addcustomer&token=<?php echo $sc_agent->getPSToken('AdminCustomers'); ?>");
                        wNewCustomer.attachEvent("onClose", function(win){
                                    displayCustomers();
                                    return true;
                                });
                    }
                }
                pushOneUsage('grid-bo-link-admincustomers_addcustomer','cus');
            }
            if (id=='selectall'){
              cus_grid.enableSmartRendering(false);
              cus_grid.selectAll();
              getGridStat();
            }
            if (id=='exportcsv'){
                displayQuickExportWindow(cus_grid,1);
			}
			if (id=='cols123')
			{
				cus.cells("a").expand();
				cus.cells("a").setWidth(200);
				cus.cells("b").expand();
				dhxLayout.cells('b').expand();
				dhxLayout.cells('b').setWidth(500);
			}
			if (id=='cols12')
			{
				cus.cells("a").expand();
				cus.cells("a").setWidth(200);
				cus.cells("b").expand();
				dhxLayout.cells('b').collapse();
			}
			if (id=='cols23')
			{
				cus.cells("a").collapse();
				cus.cells("b").expand();
				cus.cells("b").setWidth($(document).width()/2);
				dhxLayout.cells('b').expand();
				dhxLayout.cells('b').setWidth($(document).width()/2);
			}
            if (id=='send_mail')
            {
            	if(dhxWins.window("wSendMail")){
					dhxWins.window("wSendMail").unload;
				}
                const params = {
                    id_shop: shopselection,
                    id_lang: SC_ID_LANG,
                    selectedIds: String(getSelectedCustomerId())
                };
                wSendMail = dhxWins.createWindow("wSendMail", 50, 50, 800, $(window).height() - 100);
                wSendMail.center();
                wSendMail.setText('<?php echo _l('Send an email', 1); ?>');
                const queryString = new URLSearchParams(params);
                $.get("index.php?ajax=1&act=all_win-mail_init&"+queryString.toString(), function
                    (data) {
                    $('#jsExecute').html(data);
                });
            }
			if(id=='delete'){
                let selection = cus_grid.getSelectedRowId();
                if(selection) {
                    dhtmlx.message({
                        type: "confirm",
                        text: "<?php echo _l('Do you want to delete thoses customer account?', 1); ?>",
                        ok: "<?php echo _l('Yes'); ?>",
                        cancel: "<?php echo _l('No'); ?>",
                        callback: function (res) {
                            var full_delete = (res ? 1 : 0);
                            if (full_delete == 0) {
                                return false;
                            }
                            dhtmlx.message({
                                type: "confirm",
                                text: "<?php echo _l('Allow : These customers will be able to register again.<br/><br/>Forbid : These customers will not be able to register anymore (data kept in database).', 1); ?>",
                                ok: "<?php echo _l('Allow'); ?>",
                                cancel: "<?php echo _l('Forbid'); ?>",
                                callback: function (res) {
                                    full_delete = (res ? 1 : 0);
                                    let customer_ids = getSelectedCustomerId();
                                    let row_is_address = (cus_grid.getColIndexById('id_address') !== undefined);
                                    if(row_is_address){
                                        customer_ids= cus_grid.getSelectedRowId();
                                    }
                                    let ids = customer_ids.split(',');
                                    $.each(ids, function (num, rId) {
                                        let id_customer =rId
                                        if(row_is_address)
                                        {
                                            id_customer = cus_grid.getUserData(rId,'id_customer');
                                        }
                                        var params =
                                            {
                                                name: "cus_customer_update_queue",
                                                row: id_customer,
                                                action: "delete",
                                                params: {
                                                    'full_delete': full_delete
                                                },
                                                callback: "callbackCustomerUpdate('" + rId + "','delete','" + rId + "');"
                                            };
                                        let idxIdCustomer = cus_grid.getColIndexById('id_customer');
                                        params.params[cus_grid.getColumnId(idxIdCustomer)] = cus_grid.cells(rId, idxIdCustomer).getValue();
                                        params.params = JSON.stringify(params.params);
                                        cus_grid.setRowTextStyle(rId, "text-decoration: line-through;");
                                        addInUpdateQueue(params, cus_grid);
                                    });
                                }
                            });
                        }
                    });
                }
            }
            if(id=='make_order')
            {
                <?php if ((defined('SC_DEMO') && SC_DEMO)
                            || (defined('SUB6TYP2') && in_array(SUB6TYP2, array(3, 4, 5, 7, 9, 10)))) { ?>
                    let params = '';
                    let customerId = lastCustomerSelID;
                    if(cus_grid.getUserData(lastCustomerSelID, "id_customer")){
                        customerId = cus_grid.getUserData(lastCustomerSelID, "id_customer");
                    }
                    if(cus_grid.getSelectedRowId() !== null) {
                        let id_shop_customer = cus_grid.getUserData(customerId,'id_shop_customer');
                        params = '&id_customer='+customerId+'&id_shop_customer='+id_shop_customer;
                    }
                    if (!dhxWins.isWindow('wMakeOrder')) {
                        wMakeOrder = dhxWins.createWindow('wMakeOrder', 50, 50, $(window).width()-75, $(window).height()-75);
                        wMakeOrder.maximize();
                        wMakeOrder.setText('<?php echo _l('Create an order', 1); ?>');
                        $.get('index.php?ajax=1&act=ord_win-makeorder_init'+params,function(data){
                            $('#jsExecute').html(data);
                        });
                        wMakeOrder.attachEvent('onClose', function(win){
                            wMakeOrder.hide();
                            return false;
                        });
                    }else{
                        $.get('index.php?ajax=1&act=ord_win-makeorder_init'+params,function(data){
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
        }
    cus_grid_tb.attachEvent("onClick",gridToolBarOnClick);

    cus_grid.setImagePath('lib/js/imgs/');
    cus_grid.setDateFormat("%Y-%m-%d %H:%i:%s","%Y-%m-%d %H:%i:%s");

    cus_grid.enableMultiselect(true);
    cus_grid_sb=cus_customerPanel.attachStatusBar();
    gridToolBarOnClick(gridView);

    // multiedition context menu
    cus_grid.attachEvent("onBeforeContextMenu", function(rowid,colidx,grid){
            lastColumnRightClicked=colidx;
            cus_cmenu.setItemText('object', '<?php echo _l('Customer:'); ?> '+cus_grid.cells(rowid,cus_grid.getColIndexById('lastname')).getValue());
            // paste function
            if (lastColumnRightClicked==clipboardType)
            {
                cus_cmenu.setItemEnabled('paste');
            }else{
                cus_cmenu.setItemDisabled('paste');
            }
            var colType=cus_grid.getColType(colidx);
            if (colType=='ro')
            {
                cus_cmenu.setItemDisabled('copy');
                cus_cmenu.setItemDisabled('paste');
            }else{
                cus_cmenu.setItemEnabled('copy');
            }

            let current_segment_is_manual = parseInt(cus_grid.getUserData("", "manual_segment"));
            if(current_segment_is_manual === 1){
                cus_cmenu.setItemEnabled('delete_from_manual_segment');
            } else {
                cus_cmenu.setItemDisabled('delete_from_manual_segment');
            }
            return true;
        });

    function onEditCell(stage,rId,cInd,nValue,oValue)
    {
        var coltype=cus_grid.getColType(cInd);
        if (stage==1 && this.editor && this.editor.obj && coltype!='txt' && coltype!='txttxt') this.editor.obj.select();
        lastEditedCell=cInd;
        if (nValue!=oValue){
            cus_grid.setRowColor(rId,'BlanchedAlmond');
        }
<?php
        sc_ext::readCustomCustomersGridsConfigXML('onEditCell');
?>
        if (nValue!=oValue){
            addCustomerInQueue(rId, "update", cInd);
            return true;
        }
        if(stage==1 && (cInd == -5)) // only for ed type
        {
                var editor = this.editor;
                var pos = this.getPosition(editor.cell);
                var y = document.body.offsetHeight-pos[1];
                if(y < editor.list.offsetHeight)
                    editor.list.style.top = (pos[1] - editor.list.offsetHeight)+'px';
        }
    }
    cus_grid.attachEvent("onEditCell",onEditCell);
    cus_grid.attachEvent("onDhxCalendarCreated",function(calendar){
        dhtmlXCalendarObject.prototype.langData["<?php echo $user_lang_iso; ?>"] = lang_calendar;
        calendar.loadUserLanguage("<?php echo $user_lang_iso; ?>");
    });

    // Context menu for Grid
    cus_cmenu=new dhtmlXMenuObject();
    cus_cmenu.renderAsContextMenu();
    function onGridCusContextButtonClick(itemId){
        tabId = cus_grid.getSelectedRowId().split(',');
        tabId=tabId[0];
        if (itemId=="gopsbo"){
            id_customer=tabId;
            if(cus_grid.getColIndexById('id_address') !== undefined){
                id_customer= cus_grid.getUserData(tabId, 'id_customer');
            }
            wViewCustomer = dhxWins.createWindow("wViewCustomer"+new Date().getTime(), 50+40, 50+40, 1000, $(window).height()-75);
            wViewCustomer.setText('<?php echo _l('Customer', 1); ?> '+id_customer);
            wViewCustomer.attachURL("<?php echo SC_PS_PATH_ADMIN_REL; ?>index.php?<?php echo version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? 'controller=AdminCustomers' : 'tab=AdminCustomers'; ?>&viewcustomer&id_customer="+id_customer+"&id_lang="+SC_ID_LANG+"&adminlang=1&token=<?php echo $sc_agent->getPSToken('AdminCustomers'); ?>");
            wViewCustomer.attachEvent("onClose", function(win){
                displayCustomers();
                return true;
            });
        }
        if (itemId=="copyToClipBoard"){
            if (lastColumnRightClicked!=0)
            {
                copyToClipBoard(cus_grid.cells(tabId,lastColumnRightClicked).getTitle());
            }
        }
        if (itemId=="copy"){
            if (lastColumnRightClicked!=0)
            {
                clipboardValue=cus_grid.cells(tabId,lastColumnRightClicked).getValue();
                cus_cmenu.setItemText('paste' , '<?php echo _l('Paste'); ?> '+cus_grid.cells(tabId,lastColumnRightClicked).getTitle().substr(0,30)+'...');
                clipboardType=lastColumnRightClicked;
            }
        }
        if (itemId=="paste"){
            if (lastColumnRightClicked!=0 && clipboardValue!=null && clipboardType==lastColumnRightClicked)
            {
                selection=cus_grid.getSelectedRowId();
                if (selection!='' && selection!=null)
                {
                    selArray=selection.split(',');
                    for(i=0 ; i < selArray.length ; i++)
                    {
                        cus_grid.cells(selArray[i],lastColumnRightClicked).setValue(clipboardValue);
                        cus_grid.cells(selArray[i],lastColumnRightClicked).cell.wasChanged=true;
                        onEditCell(null,selArray[i],lastColumnRightClicked,clipboardValue,null);
                    }
                }
            }
        }
        if(itemId==="delete_from_manual_segment"){
            if (confirm('<?php echo _l('Do you really want to remove data from current segment?', 1); ?>')){
                $.post("index.php?ajax=1&act=all_win-segmentation_element_update",{action:'delete_from_id_element',ids:getSelectedCustomerId(), segmentId: id_selected_segment},function(){
                    displayCustomers();
                });
            }
        }
    }

    /**
     * Dans le cas où il s'agit d'une grille contenant les données addresses
     * @returns {string|null}
     */
    function getSelectedCustomerId()
    {
        let idCustomerList = cus_grid.getSelectedRowId();
        if(idCustomerList === null) {
            return null;
        }
        let tempCus = idCustomerList.split(',');
        if(cus_grid.getUserData(tempCus[0], "id_customer") !== null) {
            let ids = [];
            for(const rId of tempCus)
            {
                ids.push(cus_grid.getUserData(rId, "id_customer"));
            }
            idCustomerList = [...new Set(ids)].join(',');
        }
        return idCustomerList;
    }

    cus_cmenu.attachEvent("onClick", onGridCusContextButtonClick);
    var contextMenuXML='<menu absolutePosition="auto" mode="popup" maxItems="8"  globalCss="contextMenu" globalSecondCss="contextMenu" globalTextCss="contextMenuItem">'+
        '<item text="Object" id="object" enabled="false"/>'+
        '<item text="<?php echo _l('Edit in PrestaShop BackOffice'); ?>" id="gopsbo"/>'+
        '<item text="<?php echo _l('Copy to ClipBoard'); ?>" id="copyToClipBoard"/>'+
        '<item text="<?php echo _l('Copy'); ?>" id="copy"/>'+
        '<item text="<?php echo _l('Paste'); ?>" id="paste"/>'+
        '<item text="<?php echo _l('Remove from manual segment'); ?>" id="delete_from_manual_segment"/>'+
    '</menu>';
    cus_cmenu.loadStruct(contextMenuXML);
    cus_grid.enableContextMenu(cus_cmenu);

    //#####################################
    //############ Events
    //#####################################

    // Click on a customer
    function doOnRowSelected(idrow){
        if(lastCustomerSelID!=idrow) {
            lastCustomerSelID=idrow;
            if (!dhxLayout.cells('b').isCollapsed())
            {
                idxLastname=cus_grid.getColIndexById('lastname');
                idxFirstame=cus_grid.getColIndexById('firstname');

                let countSelection = cus_grid.getSelectedRowId().split(',').length;
                let propTitle = '<?php echo _l('Properties', 1).' '._l('of', 1); ?> '+cus_grid.cells(lastCustomerSelID,idxFirstame).getValue()+" "+cus_grid.cells(lastCustomerSelID,idxLastname).getValue()
                if(countSelection>1) {
                    propTitle = '<?php echo _l('Properties', 1).' '._l('for', 1); ?> '+ countSelection + ' <?php echo _l('customers'); ?>';
                }

                if (propertiesPanel!='descriptions'
                    && propertiesPanel!='notes'){
                    dhxLayout.cells('b').setText(propTitle);
                }
                <?php
                echo eval('?>'.$pluginCustomerProperties['doOnCustomerRowSelected'].'<?php ');
                ?>
            }
        }
    }

    cus_grid.attachEvent("onRowSelect",doOnRowSelected);

    // UISettings
    initGridUISettings(cus_grid);

    cus_grid.attachEvent("onFilterEnd", function(elements){
        old_filter_params = filter_params;
        filter_params = "";
        var nb_cols = cus_grid.getColumnsNum();
        if(nb_cols>0)
        {
            for(var i=0; i<nb_cols; i++)
            {
                var colId=customer_columns[i];
                if(cus_grid.getFilterElement(i)!=null
                        && ( colId =="id_address"
                        || colId =="id_customer"
                        || colId =="firstname"
                        || colId =="lastname"
                        || colId =="email"
                        || colId =="date_add"
                        || colId =="postcode"
                        || colId =="city" )

                    )
                {
                    var colValue = cus_grid.getFilterElement(i).value;
                    if((colValue!=null && colValue!="") || (oldFilters[colId]!=null && oldFilters[colId]!=""))
                    {
                        if(filter_params!="")
                            filter_params = filter_params + ",";
                        filter_params = filter_params + colId+"|||"+colValue;
                        oldFilters[colId] = cus_grid.getFilterElement(i).value;
                    }
                }
            }
        }
        if(filter_params!="" && filter_params!=old_filter_params)
        {
            displayCustomers();
        }
        getGridStat();

    });

    cus_grid.attachEvent("onSelectStateChanged", function(id){
        getGridStat();
    });

    cus_grid.attachEvent("onDhxCalendarCreated",function(calendar){
        calendar.setSensitiveRange("2012-01-01",null);
    });

    cus_grid_tb.attachEvent("onStateChange",function(id,state){
        if (id=='lightNavigation')
        {
            if (state)
            {
                cus_grid.enableLightMouseNavigation(true);
            }else{
                cus_grid.enableLightMouseNavigation(false);
            }
        }
    });

    var customer_columns = new Array();
    var filter_params = "";
    var oldFilters = new Object();
    <?php if (!empty($_GET['open_cus'])) { ?>
    var need_cus_filter = 1;
    <?php } ?>

    function displayCustomers(callback)
    {
        oldFilters=new Array();
        for(var i=0,l=cus_grid.getColumnsNum();i<l;i++)
        {
            if (cus_grid.getFilterElement(i)!=null && cus_grid.getFilterElement(i).value!='')
                oldFilters[cus_grid.getColumnId(i)]=cus_grid.getFilterElement(i).value;

        }
        cus_grid.editStop(true);
        cus_grid.clearAll(true);
        cus_grid_sb.setText('');
        oldGridView=gridView;
        firstProductsLoading=0;
        cus_grid_sb.setText('<?php echo _l('Loading in progress, please wait...', 1); ?>');

        var loadUrl = "index.php?ajax=1&act=cus_customer_get&filters="+groupselection+"&filter_params="+filter_params+"&view="+gridView+"&id_lang="+SC_ID_LANG+"&"+new Date().getTime();
        <?php if (SCSG) { ?>
        if(id_selected_segment!=undefined && id_selected_segment!=null && id_selected_segment!=0)
            loadUrl = "index.php?ajax=1&act=cus_customer_get&id_segment="+id_selected_segment+"&filter_params="+filter_params+"&view="+gridView+"&id_lang="+SC_ID_LANG+"&"+new Date().getTime();
        <?php } ?>

        ajaxPostCalling(cus_customerPanel, cus_grid, loadUrl,function(data)
        {
            cus_grid.parse(data);
            <?php if (!empty($_GET['open_cus'])) { ?>
            if(need_cus_filter == 1)
            {
                need_cus_filter = 0;
                idxCustomerID = cus_grid.getColIndexById('id_customer');
                cus_grid.getFilterElement(idxCustomerID).value='<?php echo (int) Tools::getValue('open_cus'); ?>';
                setTimeout(function(){cus_grid.filterByAll();}, 1000);
            }
            <?php } ?>

            // Tri indifferemment de la case ou du caractere
            idxFirstname=cus_grid.getColIndexById('firstname');
            cus_grid.setCustomSorting(function(a,b,ord,a_id,b_id){
                a = sanitizeString(replaceAccentCharacters(latinise(cus_grid.cells(a_id,idxFirstname).getTitle()).toLowerCase()));
                b = sanitizeString(replaceAccentCharacters(latinise(cus_grid.cells(b_id,idxFirstname).getTitle()).toLowerCase()));
                return ord=="asc"?(a>b?1:-1):(a>b?-1:1);
            }, idxFirstname);
            idxLastname=cus_grid.getColIndexById('lastname');
            cus_grid.setCustomSorting(function(a,b,ord,a_id,b_id){
                a = sanitizeString(replaceAccentCharacters(latinise(cus_grid.cells(a_id,idxLastname).getTitle()).toLowerCase()));
                b = sanitizeString(replaceAccentCharacters(latinise(cus_grid.cells(b_id,idxLastname).getTitle()).toLowerCase()));
                return ord=="asc"?(a>b?1:-1):(a>b?-1:1);
            }, idxLastname);

            cus_grid._rowsNum=cus_grid.getRowsNum();

            var limit_smartrendering = 0;
            if(cus_grid.getUserData("", "LIMIT_SMARTRENDERING")!=undefined && cus_grid.getUserData("", "LIMIT_SMARTRENDERING")!=0 && cus_grid.getUserData("", "LIMIT_SMARTRENDERING")!=null)
                limit_smartrendering = cus_grid.getUserData("", "LIMIT_SMARTRENDERING");

            if(limit_smartrendering!=0 && cus_grid._rowsNum > limit_smartrendering)
                cus_grid.enableSmartRendering(true);
            else
                cus_grid.enableSmartRendering(false);

            lastEditedCell=0;
            lastColumnRightClicked=0;
            customer_columns = new Array();
            var nb_cols = cus_grid.getColumnsNum();
            if(nb_cols>0)
            {
                for(var i=0; i<nb_cols; i++)
                {
                    var colId=cus_grid.getColumnId(i);
                    customer_columns[i] = colId;
                }
            }

            // UISettings
            loadGridUISettings(cus_grid);

            getGridStat();

            var testCustomerAddress = cus_grid.getColIndexById('id_address');
            if (typeof testCustomerAddress !== "undefined")
            {
                idxCustomerID = cus_grid.getColIndexById('id_customer');
                CustomerIDs = cus_grid.findCell(lastCustomerSelID,idxCustomerID,0);
                preserv = 0;
                if (CustomerIDs.length)
                    for(var i = 0 ; i< CustomerIDs.length ; i++){
                        cus_grid.selectRowById(CustomerIDs[i][0],preserv,true,false);
                        preserv++;
                    }
            }else{

                if (!cus_grid.doesRowExist(lastCustomerSelID))
                {
                    lastCustomerSelID=0;
                }else{
                    cus_grid.selectRowById(lastCustomerSelID);
                }
            }

            for(var i=0;i<cus_grid.getColumnsNum();i++)
            {
                if (cus_grid.getFilterElement(i)!=null && oldFilters[cus_grid.getColumnId(i)]!=undefined)
                {
                    cus_grid.getFilterElement(i).value=oldFilters[cus_grid.getColumnId(i)];
                }
            }
            cus_grid.filterByAll();

            <?php sc_ext::readCustomCustomersGridsConfigXML('afterGetRows'); ?>

            // UISettings
            cus_grid._first_loading=0;

            <?php if (_s('APP_DISABLED_COLUMN_MOVE')) { ?>
            cus_grid.enableColumnMove(false);
            <?php } ?>

            if (callback!='') eval(callback);

        });
    }

    function getGridStat(){
        let filteredRows=cus_grid.getRowsNum();
        let selectedRows=(cus_grid.getSelectedRowId()?cus_grid.getSelectedRowId().split(',').length:0);
        let buttonToLimitSetting = ' - <a style="color:#737373;cursor:pointer;" onclick="openSettingsWindow(\'Customers\',\'Interface\',\'CUS_MAX_CUSTOMERS\');" href="javascript:void(0);"><?php echo _l('Update the display limit', 1); ?></a>';
        cus_grid_sb.setText(cus_grid._rowsNum+' '+(cus_grid._rowsNum>1?'<?php echo _l('customers'); ?>':'<?php echo _l('customer'); ?>')+" - <?php echo _l('Filter')._l(':'); ?> "+filteredRows+" - <?php echo _l('Selection')._l(':'); ?> "+selectedRows + buttonToLimitSetting);
    }

    function addCustomerInQueue(rId, action, cIn, vars)
    {
        var params = {
            name: "cus_customer_update_queue",
            row: rId,
            action: action,
            params: {},
            callback: "callbackCustomerUpdate('"+rId+"','"+action+"','"+rId+"',{data});"
        };

        // COLUMN VALUES
        if(cIn!=undefined && cIn!="" && cIn!=null && cIn!=0) {
            params.params[cus_grid.getColumnId(cIn)] = cus_grid.cells(rId, cIn).getValue();
        }
        idxIdAddress=cus_grid.getColIndexById('id_address');
        if(idxIdAddress!=undefined && idxIdAddress!=null) {
            params.params[cus_grid.getColumnId(idxIdAddress)] = cus_grid.cells(rId, idxIdAddress).getValue();
        }
        params.params['sc_id_lang'] = SC_ID_LANG;
        if(vars!=undefined && vars!=null && vars!="" && vars!=0)
        {
            $.each(vars, function(key, value){
                params.params[key] = value;
            });
        }

        // USER DATA
        if(rId!=undefined && rId!=null && rId!="" && rId!=0)
        {
            if (cus_grid.UserData[rId] != undefined && cus_grid.UserData[rId]!=null && cus_grid.UserData[rId]!="" && cus_grid.UserData[rId]!=0) {
                $.each(cus_grid.UserData[rId].keys, function (i, key) {
                    params.params[key] = cus_grid.UserData[rId].values[i];
                });
            }
        }
        $.each(cus_grid.UserData.gridglobaluserdata.keys, function(i, key){
            params.params[key] = cus_grid.UserData.gridglobaluserdata.values[i];
        });

        <?php
        sc_ext::readCustomCustomersGridsConfigXML('onBeforeUpdate');
        ?>

        params.params = JSON.stringify(params.params);
        addInUpdateQueue(params,cus_grid);
    }

    function callbackCustomerUpdate(sid,action,tid,xml)
    {
        <?php
        sc_ext::readCustomCustomersGridsConfigXML('onAfterUpdate');
        ?>
        if (action == 'insert') {
            //Todo
        } else if (action == 'update') {
            cus_grid.setRowTextNormal(sid);
        } else if (action == 'delete') {
            cus_grid.deleteRow(sid);
        }
    };

    function setFormatData(type)
    {
        switch(type) {
            case 'capitalize':
                if (confirm('<?php echo _l('Set customers data to format:Firstname Lastname?', 1); ?>')) {
                    $.post('index.php?ajax=1&act=cus_customer_update_queue',
                        {
                            action: 'format',
                            rows: '1',
                            type: type
                        }, function (data) {
                            if (data == 'OK') {
                                dhtmlx.message({
                                    text: '<?php echo _l('Customers data updated with format:Firstname Lastname'); ?>',
                                    type: 'success',
                                    expire: 5000
                                });
                            }
                        });
                }
                break;
            case 'uppercase':
                if (confirm('<?php echo _l('Set customers data to format:Firstname LASTNAME?', 1); ?>')) {
                    $.post('index.php?ajax=1&act=cus_customer_update_queue',
                        {
                            action: 'format',
                            rows: '1',
                            type: type
                        }, function (data) {
                            if (data == 'OK') {
                                dhtmlx.message({
                                    text: '<?php echo _l('Customers data updated with format:Firstname LASTNAME'); ?>',
                                    type: 'success',
                                    expire: 5000
                                });
                            }
                        });
                }
                break;
        }
    }


<?php if (_s('APP_DISABLED_COLUMN_MOVE')) { ?>
    cus_grid.enableColumnMove(false);
<?php } ?>
</script>
