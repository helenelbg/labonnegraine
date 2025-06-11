<script type="text/javascript">
    var selectedPartnerIds = "";
    var selectedAffiliateIds = "";
    var selectedOrderIds = "";
    var selectedCommIds = "";


    dhxlAffRows = dhxlGenAffViewManager.cells("a").attachLayout("2E");

    dhxlAffTopRow = dhxlAffRows.cells("a").attachLayout("2U");
    dhxlAffPartners = new Object();
    dhxlAffPartners.cell = dhxlAffTopRow.cells("a");
    dhxlAffAffiliates = new Object();
    dhxlAffAffiliates.cell = dhxlAffTopRow.cells("b");

    dhxlAffBottomRow = dhxlAffRows.cells("b").attachLayout("2U");
    dhxlAffOrders = new Object();
    dhxlAffOrders.cell = dhxlAffBottomRow.cells("a");
    dhxlAffCommissions = new Object();
    dhxlAffCommissions.cell = dhxlAffBottomRow.cells("b");

    // PARTNERS
    clipboardType_AffPartners = null;
    dhxlAffPartners.cell.setText("<?php echo _l('Partners'); ?>");
    dhxlAffPartners.toolbar=dhxlAffPartners.cell.attachToolbar();
    dhxlAffPartners.toolbar.setIconset('awesome');
    dhxlAffPartners.toolbar.addButton("refresh", 100, "", "fa fa-sync green", "fa fa-sync green");
    dhxlAffPartners.toolbar.setItemToolTip('refresh','<?php echo _l('Refresh grid', 1); ?>');
    dhxlAffPartners.toolbar.addButton("add_partner", 100, "", "fa fa-plus-circle green", "fa fa-plus-circle green");
    dhxlAffPartners.toolbar.setItemToolTip('add_partner','<?php echo _l('New partner', 1); ?>');
    dhxlAffPartners.toolbar.addButton("del_partner", 100, "", "fa fa-minus-circle red", "fa fa-minus-circle red");
    dhxlAffPartners.toolbar.setItemToolTip('del_partner','<?php echo _l('Delete partner', 1); ?>');
    dhxlAffPartners.toolbar.addButton("chart_partner", 100, "", "fa fa-chart-bar", "fa fa-chart-bar");
    dhxlAffPartners.toolbar.setItemToolTip('chart_partner','<?php echo _l('Performance', 1); ?>');
    dhxlAffPartners.toolbar.addButton("coupon_partner", 100, "", "fad fa-edit yellow", "fad fa-edit yellow");
    dhxlAffPartners.toolbar.setItemToolTip('coupon_partner','<?php echo _l('Edit discount coupon', 1); ?>');
    dhxlAffPartners.toolbar.addButton("coupon_partner_remove", 100, "", "fad fa-unlink red", "fad fa-unlink red");
    dhxlAffPartners.toolbar.setItemToolTip('coupon_partner_remove','<?php echo _l('Remove coupon', 1); ?>');
    dhxlAffPartners.toolbar.addButton("reset_ppa", 100, "", "fad fa-check-circle grey", "fad fa-check-circle grey");
    dhxlAffPartners.toolbar.setItemToolTip('reset_ppa','<?php echo _l('Reset agreements of all partners', 1); ?>');
    dhxlAffPartners.toolbar.addButton("history_partner", 100, "", "fad fa-list-alt blue", "fad fa-list-alt blue");
    dhxlAffPartners.toolbar.setItemToolTip('history_partner','<?php echo _l('Show ChangeLog', 1); ?>');
    dhxlAffPartners.toolbar.addButton("user_go", 100, "", "fad fa-walking orange", "fad fa-walking orange");
    dhxlAffPartners.toolbar.setItemToolTip('user_go','<?php echo _l('Login as selected user on the front office', 1); ?>');
    dhxlAffPartners.toolbar.addButton("selectall", 100, "", "fa fa-bolt yellow", "fa fa-bolt yellow");
    dhxlAffPartners.toolbar.setItemToolTip('selectall','<?php echo _l('Select all', 1); ?>');
    dhxlAffPartners.toolbar.addButton("export_grid", 100, "", "fad fa-file-csv green", "fad fa-file-csv green");
    dhxlAffPartners.toolbar.setItemToolTip('export_grid','<?php echo _l('Export grid to clipboard in CSV format for MSExcel with tab delimiter.', 1); ?>');
    dhxlAffPartners.toolbar.addButton("color_help", 100, "", "fad fa-question-circle blue", "fad fa-question-circle blue");
    dhxlAffPartners.toolbar.setItemToolTip('color_help','<?php echo _l('Show help for color codes used', 1); ?>');
    getTbSettingsButton(dhxlAffPartners.toolbar, {'grideditor':1,'settings':0}, 'partners_grid_', 100);
    dhxlAffPartners.toolbar.attachEvent("onClick",
        function(id){
            if (id=='refresh')
            {
                displayAffiliation();
                displayAffiliates(selectedPartnerIds);
                displayCommissions(selectedPartnerIds);
            }
            if (id=='export_grid'){
                displayQuickExportWindow(dhxlAffPartners.grid, 1);
                //window.clipboardData.setData('Text',csv);
            }
            if (id=='partners_grid_grideditor'){
                openWinGridEditor('type_gmapartner');
            }
            if (id=='user_go'){
                var sel=dhxlAffPartners.grid.getSelectedRowId();
                if (sel)
                {
                    var tabId=sel.split(',');
                    if (tabId.length==1){
                        idxIdCustomer=dhxlAffPartners.grid.getColIndexById('customer_id');
                        id_customer=dhxlAffPartners.grid.cells(tabId[0],idxIdCustomer).getValue();
                        var id_shop = 0;
                        <?php if (SCMS) { ?>
                            var id_shop = dhxlAffPartners.grid.getUserData(tabId[0],"id_shop");
                        <?php } ?>
                        connectAsUser("<?php echo Configuration::get('SC_SALT'); ?>","<?php echo $sc_agent->id_employee; ?>",id_customer,id_shop);
                    }else{
                        dhtmlx.message({text:'<?php echo _l('Alert: You need to select only one customer', 1); ?>',type:'error'});
                    }
                }
            }
            if(id=='chart_partner')
            {
                var sel=dhxlAffPartners.grid.getSelectedRowId();
                if (sel)
                {
                    var tabId=sel.split(',');
                    for (var i=0;i<tabId.length;i++)
                    {
                        idxIdPartner=dhxlAffPartners.grid.getColIndexById('id_partner');
                        id_partner=dhxlAffPartners.grid.cells(tabId[i],idxIdPartner).getValue();
                        wViewPerformance = dhxWins.createWindow(i+"wViewPerformance"+new Date().getTime(), 50+i*40, 50+i*40, 1200, $(window).height()-75);
                        wViewPerformance.setText('<?php echo _l('Partners Performance', 1); ?> : <?php echo _l('ID n°', 1); ?>'+id_partner);
                        wViewPerformance.attachURL("index.php?ajax=1&act=all_win-affiliation_manager_performance&id_partner="+id_partner);
                    }
                }
            }
            if(id=='coupon_partner')
            {
                var ids=dhxlAffPartners.grid.getSelectedRowId();
                if (ids)
                {
                    var tabId=ids.split(',');
                    if(tabId[0]!=undefined && tabId[0]!=null && tabId[0]!="" && tabId[0]!="0")
                    {
                        rowid = tabId[0];
                        var id_coupon = dhxlAffPartners.grid.getUserData(rowid,"id_coupon");
                        if(id_coupon[0]!=undefined && id_coupon[0]!=null && id_coupon[0]!="" && id_coupon[0]!="0")
                        {
                            wViewReduction = dhxWins.createWindow("wOpenReduction", 50, 50, $(window).width()-75, $(window).height()-75);
                            wViewReduction.setText('<?php echo _l('Reduction code in Prestashop Backoffice', 1); ?>');
                            <?php if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) { ?>
                             wViewReduction.attachURL("<?php echo SC_PS_PATH_ADMIN_REL; ?>index.php?tab=AdminCartRules&updatecart_rule&id_cart_rule="+id_coupon+"&id_lang="+SC_ID_LANG+"&token=<?php echo $sc_agent->getPSToken('AdminCartRules'); ?>");
                            <?php }
else
{ ?>
                            wViewReduction.attachURL("<?php echo SC_PS_PATH_ADMIN_REL; ?>index.php?tab=AdminDiscounts&updatediscount&id_discount="+id_coupon+"&id_lang="+SC_ID_LANG+"&token=<?php echo $sc_agent->getPSToken('AdminDiscounts'); ?>");
                            <?php } ?>
                            wViewReduction.attachEvent("onClose", function(win){
                                displayAffiliation();
                                displayAffiliates(selectedPartnerIds);
                                displayCommissions(selectedPartnerIds);
                                return true;
                            });
                        }
                    }
                }
            }
            if(id=='coupon_partner_remove')
            {
                var ids=dhxlAffPartners.grid.getSelectedRowId();
                if (ids && confirm('<?php echo _l('Are you sure you want to dissociate these coupons?', 1); ?>'))
                {
                    var remove_coupons = 0;
                    if(confirm('<?php echo _l('Do you want to delete them too?', 1); ?>'))
                        remove_coupons = 1;

                    affiliationDataProcessorURLBase="index.php?ajax=1&act=all_win-affiliation_manager_update&remove_coupons="+remove_coupons;
                    affiliationDataProcessor.serverProcessor=affiliationDataProcessorURLBase;
                    var tabId=ids.split(',');
                    idxCouponCode = dhxlAffPartners.grid.getColIndexById('coupon_code');

                    $.each(tabId, function(num, id){
                        dhxlAffPartners.grid.cells(id,idxCouponCode).setValue("");
                        dhxlAffPartners.grid.cells(id,idxCouponCode).cell.wasChanged=true;
                        affiliationDataProcessor.setUpdated(id,true,"updated");
                    });
                    affiliationDataProcessorURLBase="index.php?ajax=1&act=all_win-affiliation_manager_update";
                    affiliationDataProcessor.serverProcessor=affiliationDataProcessorURLBase;
                }
            }
            if (id=='add_partner')
            {
                affiliationDataProcessorURLBase="index.php?ajax=1&act=all_win-affiliation_manager_update";
                affiliationDataProcessor.serverProcessor=affiliationDataProcessorURLBase;
                var newId = new Date().getTime();
                dhxlAffPartners.grid.addRow(newId,[newId,<?php if (SCMS)
{
    echo "'',";
} ?>'1','','','','','','code','10.00','','','unlimited', '', '0', '0', '0','','']);

            }
            if (id=='del_partner')
            {
                if (confirm('<?php echo _l('Are you sure you want to delete this partner ?', 1); ?>'))
                {
                    dhxlAffPartners.grid.deleteSelectedRows();
                }
            }
            if (id=='selectall')
            {
                dhxlAffPartners.grid.selectAll();
            }
            if (id=='color_help')
            {
                showColorHelp();
            }
            if(id=="reset_ppa")
            {
                var conf = prompt('<?php echo _l('Please, write "DELETE" to confirm reset', 1); ?>');
                if (conf=="DELETE")
                {
                    $.post("index.php?ajax=1&act=all_win-affiliation_manager_update&action=reset_ppa&id_lang=" + SC_ID_LANG, function (data) {
                        displayAffiliation();
                        displayAffiliates(selectedPartnerIds);
                        displayCommissions(selectedPartnerIds);
                    });
                }
            }
            if (id=='history_partner' && selectedPartnerIds!="")
            {
                if (!dhxWins.isWindow("wAffHistory"))
                {
                    wAffHistory = dhxWins.createWindow("wAffHistory", ($(window).width()/2-320), 100, 640, 300);
                    wAffHistory.setText("<?php echo _l('ChangeLog', 1); ?>");
                    wAffHistory.show();

                    dhxlAffHistory=wAffHistory.attachLayout("1C");
                    dhxlAffHistory_w = dhxlAffHistory.cells('a');
                    dhxlAffHistory_w.hideHeader();
                    dhxlAffHistory_add = dhxlAffHistory_w;
                    AffHistoryGrid = dhxlAffHistory_w.attachGrid();
                    AffHistoryGrid._name='AffHistoryGrid';
                    AffHistoryGrid.setImagePath("lib/js/imgs/");
                    AffHistoryGrid.enableDragAndDrop(false);
                    AffHistoryGrid.enableMultiselect(false);


                    AffHistoryGrid.clearAll();
                    $.post("index.php?ajax=1&act=all_win-affiliation_manager_history_get&partners="+selectedPartnerIds+"&id_lang="+SC_ID_LANG,function(data)
                    {
                        AffHistoryGrid.parse(data);
                    });
                }else{
                    wAffHistory.setDimension(320, 300);
                    wAffHistory.show();

                    AffHistoryGrid.clearAll();
                    $.post("index.php?ajax=1&act=all_win-affiliation_manager_history_get&partners="+selectedPartnerIds+"&id_lang="+SC_ID_LANG,function(data)
                    {
                        AffHistoryGrid.parse(data);
                    });
                }
            }
        });
    function str_custom(a,b,order){
        return (a.toLowerCase()>b.toLowerCase()?1:-1)*(order=="asc"?1:-1);
    }
    dhxlAffPartners.grid=dhxlAffPartners.cell.attachGrid();
    dhxlAffPartners.grid.setImagePath("lib/js/imgs/");
    dhxlAffPartners.grid.enableMultiselect(true);

    dhxlAffPartners.grid.setDateFormat("%Y-%m-%d %H:%i:%s","%Y-%m-%d %H:%i:%s");
    dhxlAffPartners.grid.attachEvent("onDhxCalendarCreated",function(calendar){
        calendar.loadUserLanguage("<?php echo $user_lang_iso; ?>");
    });
    dhxlAffPartners.grid.attachEvent("onEditCell", function(stage,rId,cInd,nValue,oValue){
        if (stage==1 && this.editor && this.editor.obj) this.editor.obj.select();
        <?php sc_ext::readCustomGMAPartnerGridConfigXML('onEditCell'); ?>
        if(stage==0)
        {
            idxCouponCode = dhxlAffPartners.grid.getColIndexById('coupon_code');
            if(idxCouponCode==cInd)
            {
                askCouponCode(rId);
                return false;
            }
        }
        return true;
    });
    dhxlAffPartners.grid.attachEvent("onSelectStateChanged", function(id,ind){
        selectedPartnerIds = dhxlAffPartners.grid.getSelectedRowId();
        if(selectedPartnerIds==null || selectedPartnerIds=="")
            selectedPartnerIds = 0;
        displayAffiliates(selectedPartnerIds);
        displayCommissions(selectedPartnerIds);
    });


    // UISettings
    dhxlAffPartners.grid._uisettings_prefix='gmapartner';
    dhxlAffPartners.grid._uisettings_name=dhxlAffPartners.grid._uisettings_prefix;
    dhxlAffPartners.grid._uisettings_limited=true;
    dhxlAffPartners.grid._first_loading=1;
    initGridUISettings(dhxlAffPartners.grid);

    affiliationDataProcessorURLBase="index.php?ajax=1&act=all_win-affiliation_manager_update&id_lang="+SC_ID_LANG;
    affiliationDataProcessor = new dataProcessor(affiliationDataProcessorURLBase);
    affiliationDataProcessor.enableDataNames(true);
    affiliationDataProcessor.enablePartialDataSend(true);
    affiliationDataProcessor.setUpdateMode('cell');
    affiliationDataProcessor.attachEvent("onBeforeUpdate", function(id, state, data){
        <?php sc_ext::readCustomGMAPartnerGridConfigXML('onBeforeUpdate'); ?>
        return true;
    });
    affiliationDataProcessor.attachEvent("onAfterUpdate",function(sid,action,tid,xml_node){
            idxName = dhxlAffPartners.grid.getColIndexById('name');
            idxCode = dhxlAffPartners.grid.getColIndexById('code');
            idxId = dhxlAffPartners.grid.getColIndexById('id_partner');
            if (action=='insert')
            {
                dhxlAffPartners.grid.cells(tid,idxName).setValue($(xml_node).attr("name"));
                dhxlAffPartners.grid.cells(tid,idxCode).setValue($(xml_node).attr("code"));
                dhxlAffPartners.grid.cells(tid,idxId).setValue(tid);
            }
            if (action=='update')
            {
                if($(xml_node).attr("error") !== undefined) {
                    dhtmlx.message({text:$(xml_node).attr("error"),type:'error'});
                } else {
                    dhxlAffPartners.grid.cells(sid,idxName).setValue($(xml_node).attr("name"));
                    if ($(xml_node).attr("code") != undefined && $(xml_node).attr("code") != "" && $(xml_node).attr("code") != null && $(xml_node).attr("code") != 0) {
                        dhxlAffPartners.grid.cells(tid, idxCode).setValue($(xml_node).attr("code"));
                        dhtmlx.message({text: '<?php echo _l('This code already exists or is unvalid.', 1); ?>', type: 'error'});
                    }
                }
            }
            colorMode(true,false);
            <?php sc_ext::readCustomGMAPartnerGridConfigXML('onAfterUpdate'); ?>
            return true;
        });
    affiliationDataProcessor.init(dhxlAffPartners.grid);


    // Context menu for MultiShops Info Product grid
    dhxlAffPartners_cmenu=new dhtmlXMenuObject();
    dhxlAffPartners_cmenu.renderAsContextMenu();
    function onGridAffPartnersContextButtonClick(itemId){
        tabId=dhxlAffPartners.grid.contextID.split("_");
        tabId=tabId[0];
        if (itemId=="copy"){
            if (lastColumnRightClicked_AffPartners!=0)
            {
                clipboardValue_AffPartners=dhxlAffPartners.grid.cells(tabId,lastColumnRightClicked_AffPartners).getValue();
                dhxlAffPartners_cmenu.setItemText('paste' , '<?php echo _l('Paste', 1); ?> '+dhxlAffPartners.grid.cells(tabId,lastColumnRightClicked_AffPartners).getTitle());
                clipboardType_AffPartners=lastColumnRightClicked_AffPartners;
            }
        }
        if (itemId=="paste"){
            if (lastColumnRightClicked_AffPartners!=0 && clipboardValue_AffPartners!=null && clipboardType_AffPartners==lastColumnRightClicked_AffPartners)
            {
                selection=dhxlAffPartners.grid.getSelectedRowId();
                if (selection!='' && selection!=null)
                {
                    selArray=selection.split(',');
                    for(i=0 ; i < selArray.length ; i++)
                    {
                        dhxlAffPartners.grid.cells(selArray[i],lastColumnRightClicked_AffPartners).setValue(clipboardValue_AffPartners);
                        dhxlAffPartners.grid.cells(selArray[i],lastColumnRightClicked_AffPartners).cell.wasChanged=true;
                        affiliationDataProcessor.setUpdated(selArray[i],true,"updated");
                    }
                }
            }
        }
    }
    dhxlAffPartners_cmenu.attachEvent("onClick", onGridAffPartnersContextButtonClick);
    var contextMenuXML='<menu absolutePosition="auto" mode="popup" maxItems="8"  globalCss="contextMenu" globalSecondCss="contextMenu" globalTextCss="contextMenuItem">'+
            '<item text="Object" id="object" enabled="false"/>'+
            '<item text="<?php echo _l('Copy', 1); ?>" id="copy"/>'+
            '<item text="<?php echo _l('Paste', 1); ?>" id="paste"/>'+
        '</menu>';
    dhxlAffPartners_cmenu.loadStruct(contextMenuXML);
    dhxlAffPartners.grid.enableContextMenu(dhxlAffPartners_cmenu);


    dhxlAffPartners.grid.attachEvent("onBeforeContextMenu", function(rowid,colidx,grid){
        var disableOnCols=new Array(
                dhxlAffPartners.grid.getColIndexById('id_partner'),
                dhxlAffPartners.grid.getColIndexById('customer_id'),
                dhxlAffPartners.grid.getColIndexById('name'),
                dhxlAffPartners.grid.getColIndexById('code'),
                dhxlAffPartners.grid.getColIndexById('quantity'),
                dhxlAffPartners.grid.getColIndexById('total_invoiced'),
                dhxlAffPartners.grid.getColIndexById('total_gained'),
                dhxlAffPartners.grid.getColIndexById('id_shop'),

                dhxlAffPartners.grid.getColIndexById('coupon_code'),
                dhxlAffPartners.grid.getColIndexById('ppa_date')
                );
        if (in_array(colidx,disableOnCols))
        {
            return false;
        }
        lastColumnRightClicked_AffPartners=colidx;
        dhxlAffPartners_cmenu.setItemText('object', '<?php echo _l('Partner', 1); ?>: '+dhxlAffPartners.grid.cells(rowid,dhxlAffPartners.grid.getColIndexById('id_partner')).getTitle());
        if (lastColumnRightClicked_AffPartners==clipboardType_AffPartners)
        {
            dhxlAffPartners_cmenu.setItemEnabled('paste');
        }else{
            dhxlAffPartners_cmenu.setItemDisabled('paste');
        }
        return true;
    });

    displayAffiliation();

    // AFFILIATES
    clipboardType_AffPartnersAff = null;
    dhxlAffAffiliates.cell.setText("<?php echo _l('Affiliates'); ?>");
    dhxlAffAffiliates.toolbar = dhxlAffAffiliates.cell.attachToolbar();
    dhxlAffAffiliates.toolbar.setIconset('awesome');
    dhxlAffAffiliates.toolbar.addButton("color_help", 0, "", "fad fa-question-circle blue", "fad fa-question-circle blue");
    dhxlAffAffiliates.toolbar.setItemToolTip('color_help','<?php echo _l('Show help for color codes used', 1); ?>');
    dhxlAffAffiliates.toolbar.addButton("export_grid", 0, "", "fad fa-file-csv green", "fad fa-file-csv green");
    dhxlAffAffiliates.toolbar.setItemToolTip('export_grid','<?php echo _l('Export grid to clipboard in CSV format for MSExcel with tab delimiter.', 1); ?>');
    dhxlAffAffiliates.toolbar.addButton("selectall", 0, "", "fa fa-bolt yellow", "fa fa-bolt yellow");
    dhxlAffAffiliates.toolbar.setItemToolTip('selectall','<?php echo _l('Select all', 1); ?>');
    dhxlAffAffiliates.toolbar.addButton("del_aff", 0, "", "fa fa-minus-circle red", "fa fa-minus-circle red");
    dhxlAffAffiliates.toolbar.setItemToolTip('del_aff','<?php echo _l('Delete affiliate', 1); ?>');
    dhxlAffAffiliates.toolbar.addButton("openps", 0, "", "fa fa-prestashop", "fa fa-prestashop");
    dhxlAffAffiliates.toolbar.setItemToolTip('openps','<?php echo _l('Open customer page in Prestashop', 1); ?>');
    dhxlAffAffiliates.toolbar.addButton("add_affiliate", 0, "", "fa fa-plus-circle green", "fa fa-plus-circle green");
    dhxlAffAffiliates.toolbar.setItemToolTip('add_affiliate','<?php echo _l('New affiliate', 1); ?>');
    dhxlAffAffiliates.toolbar.addButton("refresh", 0, "", "fa fa-sync green", "fa fa-sync green");
    dhxlAffAffiliates.toolbar.setItemToolTip('refresh','<?php echo _l('Refresh grid', 1); ?>');

    dhxlAffAffiliates.toolbar.attachEvent("onClick",
        function(id){
            if (id=='refresh')
            {
                displayAffiliates(selectedPartnerIds);
            }
            if (id=='add_affiliate')
            {
                var promptValue = prompt('<?php echo _l('Please enter an email or a customer ID', 1); ?>');
                if(promptValue!=undefined && promptValue!=null && promptValue!="" && promptValue!=0)
                {
                    var partner_id = dhxlAffPartners.grid.getSelectedRowId();
                    var patt=/,/g;
                    var multiple=patt.test( dhxlAffPartners.grid.getSelectedRowId() );
                    if(multiple)
                    {
                        var ids = dhxlAffPartners.grid.getSelectedRowId().split(",");
                        partner_id = ids[0];
                    }

                    $.post( "index.php?ajax=1&act=all_win-affiliation_manager_affiliates_update&id_lang="+SC_ID_LANG, {"action":"add_affiliate", "value":promptValue, "partner_id":partner_id }, function( data ) {
                       displayAffiliates(selectedPartnerIds);
                    });
                }
            }
            if (id=='export_grid'){
                displayQuickExportWindow(dhxlAffAffiliates.grid, 1);

            }
            if (id=='selectall')
            {
                dhxlAffAffiliates.grid.selectAll();
                selectedAffiliateIds = dhxlAffAffiliates.grid.getSelectedRowId();
                if(selectedAffiliateIds==null || selectedAffiliateIds=="")
                    selectedAffiliateIds = 0;
                displayAffOrders(selectedAffiliateIds);
                colorCommissionsConcernedByAffiliate(selectedAffiliateIds);
            }
            if (id=='del_aff')
            {
                if (confirm('<?php echo _l('Are you sure you want to delete this affiliate ?', 1); ?>'))
                {
                    dhxlAffAffiliates.grid.deleteSelectedRows();
                }
            }
            if (id=="openps"){
                var selectedCustomerIds = dhxlAffAffiliates.grid.getSelectedRowId();
                if(selectedCustomerIds==null || selectedCustomerIds=="")
                    selectedCustomerIds = 0;
                if(selectedCustomerIds!=0)
                {
                    ids=selectedCustomerIds.split(",");
                    wViewCustomer = dhxWins.createWindow("wOpenMembers", 50, 50, 1000, $(window).height()-75);
                    wViewCustomer.setText('<?php echo _l('Customer page in Prestashop Backoffice', 1); ?>');
                    wViewCustomer.attachURL("<?php echo SC_PS_PATH_ADMIN_REL; ?>index.php?tab=AdminCustomers&updatecustomer&id_customer="+ids[0]+"&id_lang="+SC_ID_LANG+"&adminlang=1&token=<?php echo $sc_agent->getPSToken('AdminCustomers'); ?>");
                    pushOneUsage('win-affiliation_manager-bo-link-admincustomers_updatecustomer');
                }
            }
            if (id=='color_help')
            {
                showColorHelp();
            }
        });


    dhxlAffAffiliates.grid=dhxlAffAffiliates.cell.attachGrid();
    dhxlAffAffiliates.grid.setImagePath("lib/js/imgs/");
    dhxlAffAffiliates.grid.enableMultiselect(true);
    dhxlAffAffiliates.grid.enableSmartRendering(true);
    dhxlAffAffiliates.grid.setDateFormat("%Y-%m-%d %H:%i:%s","%Y-%m-%d %H:%i:%s");
    dhxlAffAffiliates.grid.attachEvent("onDhxCalendarCreated",function(calendar){
            calendar.loadUserLanguage("<?php echo $user_lang_iso; ?>");
        });
    dhxlAffAffiliates.grid.attachEvent("onEditCell", function(stage, rId, cIn, newValue, oldValue){
        if (stage==1 && this.editor && this.editor.obj) this.editor.obj.select();
        if (stage==2)
        {
            idxDateAdd = dhxlAffAffiliates.grid.getColIndexById('scaff_partner_date_add');
            if(idxDateAdd==cIn)
            {
                var regex=new RegExp("([0-9]{4}[-](0[1-9]|1[0-2])[-]([0-2]{1}[0-9]{1}|3[0-1]{1})[ ]([0-1]{1}[0-9]{1}|2[0-3]{1})[:]([0-5]{1}[0-9]{1})[:]([0-5]{1}[0-9]{1}))");
                var dateOk=regex.test(newValue);
                if(dateOk){
                    return true;
                }else{
                    return false;
                }
            }
        }
            return true;
    });

    dhxlAffAffiliates.grid.attachEvent("onSelectStateChanged", function(id,ind){
        selectedAffiliateIds = dhxlAffAffiliates.grid.getSelectedRowId();
        if(selectedAffiliateIds==null || selectedAffiliateIds=="")
            selectedAffiliateIds = 0;
        displayAffOrders(selectedAffiliateIds);

        colorCommissionsConcernedByAffiliate(selectedAffiliateIds);
    });

    // UISettings
    dhxlAffAffiliates.grid._uisettings_prefix='gmaaffiliate';
    dhxlAffAffiliates.grid._uisettings_name=dhxlAffAffiliates.grid._uisettings_prefix;
    dhxlAffAffiliates.grid._first_loading=1;
    initGridUISettings(dhxlAffAffiliates.grid);

    affiliationDataProcessor_affiliatesURLBase="index.php?ajax=1&act=all_win-affiliation_manager_affiliates_update&id_lang="+SC_ID_LANG;
    affiliationDataProcessor_affiliates = new dataProcessor(affiliationDataProcessor_affiliatesURLBase);
    affiliationDataProcessor_affiliates.enableDataNames(true);
    affiliationDataProcessor_affiliates.enablePartialDataSend(true);
    affiliationDataProcessor_affiliates.setUpdateMode('cell');




    affiliationDataProcessor_affiliates.init(dhxlAffAffiliates.grid);


    // Context menu for MultiShops Info Product grid
    dhxlAffPartnersAff_cmenu=new dhtmlXMenuObject();
    dhxlAffPartnersAff_cmenu.renderAsContextMenu();
    function onGridAffPartnersAffContextButtonClick(itemId){
        tabId=dhxlAffAffiliates.grid.contextID.split("_");
        tabId=tabId[0];
        if (itemId=="copy"){
            if (lastColumnRightClicked_AffPartnersAff!=0)
            {
                clipboardValue_AffPartnersAff=dhxlAffAffiliates.grid.cells(tabId,lastColumnRightClicked_AffPartnersAff).getValue();
                dhxlAffPartnersAff_cmenu.setItemText('paste' , '<?php echo _l('Paste', 1); ?> '+dhxlAffAffiliates.grid.cells(tabId,lastColumnRightClicked_AffPartnersAff).getTitle());
                clipboardType_AffPartnersAff=lastColumnRightClicked_AffPartnersAff;
            }
        }
        if (itemId=="paste"){
            if (lastColumnRightClicked_AffPartnersAff!=0 && clipboardValue_AffPartnersAff!=null && clipboardType_AffPartnersAff==lastColumnRightClicked_AffPartnersAff)
            {
                selection=dhxlAffAffiliates.grid.getSelectedRowId();
                if (selection!='' && selection!=null)
                {
                    selArray=selection.split(',');
                    for(i=0 ; i < selArray.length ; i++)
                    {
                        dhxlAffAffiliates.grid.cells(selArray[i],lastColumnRightClicked_AffPartnersAff).setValue(clipboardValue_AffPartnersAff);
                        dhxlAffAffiliates.grid.cells(selArray[i],lastColumnRightClicked_AffPartnersAff).cell.wasChanged=true;
                        affiliationDataProcessor_affiliates.setUpdated(selArray[i],true,"updated");
                    }
                }
            }
        }
    }
    dhxlAffPartnersAff_cmenu.attachEvent("onClick", onGridAffPartnersAffContextButtonClick);
    var contextMenuXML='<menu absolutePosition="auto" mode="popup" maxItems="8"  globalCss="contextMenu" globalSecondCss="contextMenu" globalTextCss="contextMenuItem">'+
            '<item text="Object" id="object" enabled="false"/>'+
            '<item text="<?php echo _l('Copy', 1); ?>" id="copy"/>'+
            '<item text="<?php echo _l('Paste', 1); ?>" id="paste"/>'+
        '</menu>';
    dhxlAffPartnersAff_cmenu.loadStruct(contextMenuXML);
    dhxlAffAffiliates.grid.enableContextMenu(dhxlAffPartnersAff_cmenu);

    dhxlAffAffiliates.grid.attachEvent("onBeforeContextMenu", function(rowid,colidx,grid){
        var disableOnCols=new Array(
                dhxlAffAffiliates.grid.getColIndexById('id_customer'),
                dhxlAffAffiliates.grid.getColIndexById('firstname'),
                dhxlAffAffiliates.grid.getColIndexById('lastname'),
                dhxlAffAffiliates.grid.getColIndexById('email'),

                dhxlAffAffiliates.grid.getColIndexById('id_shop')
                );
        if (in_array(colidx,disableOnCols))
        {
            return false;
        }
        lastColumnRightClicked_AffPartnersAff=colidx;
        dhxlAffPartnersAff_cmenu.setItemText('object', '<?php echo _l('Affiliate', 1); ?>: '+dhxlAffAffiliates.grid.cells(rowid,dhxlAffAffiliates.grid.getColIndexById('id_customer')).getTitle());
        if (lastColumnRightClicked_AffPartnersAff==clipboardType_AffPartnersAff)
        {
            dhxlAffPartnersAff_cmenu.setItemEnabled('paste');
        }else{
            dhxlAffPartnersAff_cmenu.setItemDisabled('paste');
        }
        return true;
    });


    // COMMISIONS
    dhxlAffCommissions.cell.setText("<?php echo _l('Commissions'); ?>");
    dhxlAffCommissions.toolbar=dhxlAffCommissions.cell.attachToolbar();
    dhxlAffCommissions.toolbar.setIconset('awesome');
    dhxlAffCommissions.toolbar.addButton("color_help", 0, "", "fad fa-question-circle blue", "fad fa-question-circle blue");
    dhxlAffCommissions.toolbar.setItemToolTip('color_help','<?php echo _l('Show help for color codes used', 1); ?>');
    dhxlAffCommissions.toolbar.addButton("paid", 0, "", "fad fa-money-bill-alt", "fad fa-money-bill-alt");
    dhxlAffCommissions.toolbar.setItemToolTip('paid','<?php echo _l('Pay all these invoiced commissions', 1); ?>');
    dhxlAffCommissions.toolbar.addButton("export_grid", 0, "", "fad fa-file-csv green", "fad fa-file-csv green");
    dhxlAffCommissions.toolbar.setItemToolTip('export_grid','<?php echo _l('Export grid to clipboard in CSV format for MSExcel with tab delimiter.', 1); ?>');
    dhxlAffCommissions.toolbar.addButton("selectall", 0, "", "fa fa-bolt yellow", "fa fa-bolt yellow");
    dhxlAffCommissions.toolbar.setItemToolTip('selectall','<?php echo _l('Select all', 1); ?>');
    dhxlAffCommissions.toolbar.addButton("del_comm", 0, "", "fa fa-minus-circle red", "fa fa-minus-circle red");
    dhxlAffCommissions.toolbar.setItemToolTip('del_comm','<?php echo _l('Delete commission', 1); ?>');
    dhxlAffCommissions.toolbar.addButton("add_comm", 0, "", "fa fa-plus-circle green", "fa fa-plus-circle green");
    dhxlAffCommissions.toolbar.setItemToolTip('add_comm','<?php echo _l('New commission', 1); ?>');
    dhxlAffCommissions.toolbar.addButton("refresh", 0, "", "fa fa-sync green", "fa fa-sync green");
    dhxlAffCommissions.toolbar.setItemToolTip('refresh','<?php echo _l('Refresh grid', 1); ?>');

    dhxlAffCommissions.toolbar.attachEvent("onClick",
        function(id){
            if (id=='refresh')
            {
                displayCommissions(selectedPartnerIds);
            }
            if (id=='export_grid'){
                displayQuickExportWindow(dhxlAffCommissions.grid, 1);

            }
            if (id=='add_comm')
            {

                affiliationDataProcessor_commissionsURLBase="index.php?ajax=1&act=all_win-affiliation_manager_commissions_update";
                affiliationDataProcessor_commissions.serverProcessor=affiliationDataProcessor_commissionsURLBase;
                var newId = new Date().getTime();

                var partner_id = dhxlAffPartners.grid.getSelectedRowId();
                var patt=/,/g;
                var multiple=patt.test( dhxlAffPartners.grid.getSelectedRowId() );
                if(multiple)
                {
                    var ids = dhxlAffPartners.grid.getSelectedRowId().split(",");
                    partner_id = ids[0];
                }

                dhxlAffCommissions.grid.addRow(newId,[newId,<?php if (SCMS)
{
    echo "'',";
} ?>partner_id,'','<?php echo date('Y-m-d'); ?>','<?php echo _l('Awaiting invoice', 1); ?>','0.0','0']);
                colorCommissions();
            }
            if (id=='del_comm')
            {
                if (confirm('<?php echo _l('Are you sure you want to delete this commission ?', 1); ?>'))
                {
                    dhxlAffCommissions.grid.deleteSelectedRows();
                }
            }
            if (id=='selectall')
            {
                dhxlAffCommissions.grid.selectAll();
            }
            if (id=='paid')
            {
                selectedCommIds = dhxlAffCommissions.grid.getSelectedRowId();
                if(selectedCommIds==null || selectedCommIds=="")
                    selectedCommIds = 0;
                $.post("index.php?ajax=1&act=all_win-affiliation_manager_paid&id_lang="+SC_ID_LANG, { "ids": selectedCommIds },
                    function(data){
                        displayCommissions(selectedPartnerIds);
                    });
            }
            if (id=='color_help')
            {
                showColorHelp();
            }
        });

    function askPartnerForComm(prefix)
    {
        var ask = '<?php echo _l('Please enter a valid Partner ID', 1); ?>';
        if(prefix!=undefined && prefix!="")
            ask = prefix+" "+ask;
        var partner = prompt(ask, '');
        if(partner!=null)
        {
            if(isNan(partner))
                askPartnerForComm('<?php echo _l('Is an unvalid ID.', 1); ?>');
            else
            {
                affiliationDataProcessor_commissionsURLBase="index.php?ajax=1&act=all_win-affiliation_manager_commissions_update";
                affiliationDataProcessor_commissions.serverProcessor=affiliationDataProcessor_commissionsURLBase;
                var newId = new Date().getTime();
                dhxlAffCommissions.grid.addRow(newId,[newId,<?php if (SCMS)
{
    echo "'',";
} ?>partner,'','<?php echo date('Y-m-d'); ?>','<?php echo _l('Awaiting invoice', 1); ?>','0.0'],1);
            }
        }
    }
    dhxlAffCommissions.grid=dhxlAffCommissions.cell.attachGrid();
    dhxlAffCommissions.grid.setImagePath("lib/js/imgs/");
    dhxlAffCommissions.grid.enableMultiselect(true);
    dhxlAffCommissions.grid.enableSmartRendering(false);
    dhxlAffCommissions.grid.attachEvent("onEditCell", function(stage, rId, cIn){
            if (stage==1 && this.editor && this.editor.obj) this.editor.obj.select();
            return true;
        });

    // UISettings
    dhxlAffCommissions.grid._uisettings_prefix='gmacommission';
    dhxlAffCommissions.grid._uisettings_name=dhxlAffCommissions.grid._uisettings_prefix;
    dhxlAffCommissions.grid._first_loading=1;
    initGridUISettings(dhxlAffCommissions.grid);

    affiliationDataProcessor_commissionsURLBase="index.php?ajax=1&act=all_win-affiliation_manager_commissions_update&id_lang="+SC_ID_LANG;
    affiliationDataProcessor_commissions = new dataProcessor(affiliationDataProcessor_commissionsURLBase);
    affiliationDataProcessor_commissions.enableDataNames(true);

    affiliationDataProcessor_commissions.setUpdateMode('cell');
    affiliationDataProcessor_commissions.attachEvent("onAfterUpdate",function(sid,action,tid,xml_node){
            dhxlAffCommissions.grid.cells(tid,0).setValue(tid);

            colorCommissions();
            return true;
        });
    affiliationDataProcessor_commissions.init(dhxlAffCommissions.grid);

    dhxlAffCommissions.grid.attachEvent("onSelectStateChanged", function(id,ind){
        selectedCommIds = dhxlAffCommissions.grid.getSelectedRowId();
        if(selectedCommIds==null || selectedCommIds=="")
            selectedCommIds = 0;
        colorOrderAndAffiliateConcernedByCommission(selectedCommIds);
    });

    // ORDERS
    dhxlAffOrders.cell.setText("<?php echo _l('Orders'); ?>");
    dhxlAffOrders.toolbar=dhxlAffOrders.cell.attachToolbar();
    dhxlAffOrders.toolbar.setIconset('awesome');
    dhxlAffOrders.toolbar.addButton("color_help", 0, "", "fad fa-question-circle blue", "fad fa-question-circle blue");
    dhxlAffOrders.toolbar.setItemToolTip('color_help','<?php echo _l('Show help for color codes used', 1); ?>');
    dhxlAffOrders.toolbar.addButton("export_grid", 0, "", "fad fa-file-csv green", "fad fa-file-csv green");
    dhxlAffOrders.toolbar.setItemToolTip('export_grid','<?php echo _l('Export grid to clipboard in CSV format for MSExcel with tab delimiter.', 1); ?>');
    dhxlAffOrders.toolbar.addButton("selectall", 0, "", "fa fa-bolt yellow", "fa fa-bolt yellow", 1);
    dhxlAffOrders.toolbar.setItemToolTip('selectall','<?php echo _l('Select all', 1); ?>');
    dhxlAffOrders.toolbar.addButton("openps", 0, "", "fa fa-prestashop", "fa fa-prestashop");
    dhxlAffOrders.toolbar.setItemToolTip('openps','<?php echo _l('Open order in Prestashop', 1); ?>');
    dhxlAffOrders.toolbar.addButton("refresh", 0, "", "fa fa-sync green", "fa fa-sync green");
    dhxlAffOrders.toolbar.setItemToolTip('refresh','<?php echo _l('Refresh grid', 1); ?>');

    dhxlAffOrders.toolbar.attachEvent("onClick",
        function(id){
            if (id=='refresh')
            {
                displayAffOrders(selectedAffiliateIds);
            }
            if (id=='export_grid'){
                displayQuickExportWindow(dhxlAffOrders.grid, 1);

            }
            if (id=='selectall')
            {
                dhxlAffOrders.grid.selectAll();
            }
            if (id=="openps"){
                selectedOrderIds = dhxlAffOrders.grid.getSelectedRowId();
                if(selectedOrderIds==null || selectedOrderIds=="")
                    selectedOrderIds = 0;
                if(selectedOrderIds!=0)
                {
                    ids=selectedOrderIds.split(",");
                    wViewOrder = dhxWins.createWindow("wOpenOrders", 50, 50, 1000, $(window).height()-75);
                    wViewOrder.setText('<?php echo _l('Order page in Prestashop Backoffice', 1); ?>');
                    wViewOrder.attachURL("<?php echo SC_PS_PATH_ADMIN_REL; ?>index.php?tab=AdminOrders&vieworder&id_order="+ids[0]+"&id_lang="+SC_ID_LANG+"&adminlang=1&token=<?php echo $sc_agent->getPSToken('AdminOrders'); ?>");
                    pushOneUsage('win-affiliation_manager-bo-link-adminorders');
                }
            }
            if (id=='color_help')
            {
                showColorHelp();
            }
        });

    dhxlAffOrders.grid=dhxlAffOrders.cell.attachGrid();
    dhxlAffOrders.grid.setImagePath("lib/js/imgs/");
    dhxlAffOrders.grid.enableMultiselect(true);
    dhxlAffOrders.grid.enableSmartRendering(true);

    dhxlAffOrders.grid.attachEvent("onSelectStateChanged", function(id,ind){
        selectedOrderIds = dhxlAffOrders.grid.getSelectedRowId();
        if(selectedOrderIds==null || selectedOrderIds=="")
            selectedOrderIds = 0;
        colorCommissionsAndAffiliateConcernedByOrder(selectedOrderIds);
    });

    // UISettings
    dhxlAffOrders.grid._uisettings_prefix='gmaorder';
    dhxlAffOrders.grid._uisettings_name=dhxlAffOrders.grid._uisettings_prefix;
    dhxlAffOrders.grid._first_loading=1;
    initGridUISettings(dhxlAffOrders.grid);

//#####################################
//############ Load functions
//#####################################

function displayAffiliation()
{
    oldFilters=new Array();
    for(var i=0,l=dhxlAffPartners.grid.getColumnsNum();i<l;i++)
    {
        if (dhxlAffPartners.grid.getFilterElement(i)!=null && dhxlAffPartners.grid.getFilterElement(i).value!='')
            oldFilters[dhxlAffPartners.grid.getColumnId(i)]=dhxlAffPartners.grid.getFilterElement(i).value;
    }
    dhxlAffPartners.grid.clearAll(true);
    dhxlAffPartners.grid.load("index.php?ajax=1&act=all_win-affiliation_manager_get&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),function()
        {
            nb=dhxlAffPartners.grid.getRowsNum();
            for(var i=0;i<dhxlAffPartners.grid.getColumnsNum();i++)
            {
                if (dhxlAffPartners.grid.getFilterElement(i)!=null && oldFilters[dhxlAffPartners.grid.getColumnId(i)]!=undefined)
                {
                    dhxlAffPartners.grid.getFilterElement(i).value=oldFilters[dhxlAffPartners.grid.getColumnId(i)];
                }
            }
            dhxlAffPartners.grid.filterByAll();

            colorMode(true,false);

            // UISettings
            loadGridUISettings(dhxlAffPartners.grid);
            dhxlAffPartners.grid._first_loading=0;

            <?php sc_ext::readCustomGMAPartnerGridConfigXML('afterGetRows'); ?>
        });
}

function displayAffiliates(ids)
{
    oldFilters=new Array();
    for(var i=0,l=dhxlAffAffiliates.grid.getColumnsNum();i<l;i++)
    {
        if (dhxlAffAffiliates.grid.getFilterElement(i)!=null && dhxlAffAffiliates.grid.getFilterElement(i).value!='')
            oldFilters[dhxlAffAffiliates.grid.getColumnId(i)]=dhxlAffAffiliates.grid.getFilterElement(i).value;
    }
    dhxlAffAffiliates.grid.clearAll(true);
    dhxlAffAffiliates.grid.load("index.php?ajax=1&act=all_win-affiliation_manager_affiliates_get&ids="+ids+"&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),function()
    {
        nb=dhxlAffAffiliates.grid.getRowsNum();
        for(var i=0;i<dhxlAffAffiliates.grid.getColumnsNum();i++)
        {
            if (dhxlAffAffiliates.grid.getFilterElement(i)!=null && oldFilters[dhxlAffAffiliates.grid.getColumnId(i)]!=undefined)
            {
                dhxlAffAffiliates.grid.getFilterElement(i).value=oldFilters[dhxlAffAffiliates.grid.getColumnId(i)];
            }
        }
        dhxlAffAffiliates.grid.filterByAll();

        colorMode(false,true);

        var Affids = "";
        dhxlAffAffiliates.grid.forEachRow(function(id){
            if(Affids!="")
                Affids += ",";
            Affids += id;
         });
         if(Affids!="")
            displayAffOrders(Affids);
         else
             displayAffOrders("");
         clearColorCommissions();

        // UISettings
        loadGridUISettings(dhxlAffAffiliates.grid);
        dhxlAffAffiliates.grid._first_loading=0;
    });
}

function displayCommissions(ids)
{
    oldFilters=new Array();
    for(var i=0,l=dhxlAffCommissions.grid.getColumnsNum();i<l;i++)
    {
        if (dhxlAffCommissions.grid.getFilterElement(i)!=null && dhxlAffCommissions.grid.getFilterElement(i).value!='')
            oldFilters[dhxlAffCommissions.grid.getColumnId(i)]=dhxlAffCommissions.grid.getFilterElement(i).value;
    }
    dhxlAffCommissions.grid.clearAll(true);
    dhxlAffCommissions.grid.load("index.php?ajax=1&act=all_win-affiliation_manager_commissions_get&ids="+ids+"&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),function()
    {
        nb=dhxlAffCommissions.grid.getRowsNum();
        for(var i=0;i<dhxlAffCommissions.grid.getColumnsNum();i++)
        {
            if (dhxlAffCommissions.grid.getFilterElement(i)!=null && oldFilters[dhxlAffCommissions.grid.getColumnId(i)]!=undefined)
            {
                dhxlAffCommissions.grid.getFilterElement(i).value=oldFilters[dhxlAffCommissions.grid.getColumnId(i)];
            }
        }
        dhxlAffCommissions.grid.filterByAll();

        idxStatus=dhxlAffCommissions.grid.getColIndexById('status');
        dhxlAffCommissions.grid.forEachRow(function(rid){
            var status = dhxlAffCommissions.grid.cells(rid, idxStatus).getValue();
            if(status=="waiting_active")
            {
                dhxlAffCommissions.grid.lockRow(rid,true);
            }
         });

        colorCommissions();

        // UISettings
        loadGridUISettings(dhxlAffCommissions.grid);
        dhxlAffCommissions.grid._first_loading=0;
    });
}

function displayAffOrders(ids)
{
    oldFilters=new Array();
    for(var i=0,l=dhxlAffOrders.grid.getColumnsNum();i<l;i++)
    {
        if (dhxlAffOrders.grid.getFilterElement(i)!=null && dhxlAffOrders.grid.getFilterElement(i).value!='')
            oldFilters[dhxlAffOrders.grid.getColumnId(i)]=dhxlAffOrders.grid.getFilterElement(i).value;
    }
    dhxlAffOrders.grid.clearAll(true);
    if(ids==undefined || ids==null || ids=="" || ids==0)
    {
        var Affids = "";
        dhxlAffAffiliates.grid.forEachRow(function(id){
            if(Affids!="")
                Affids += ",";
            Affids += id;
         });
         if(Affids!="")
             ids = Affids;
    }
    if(ids!=undefined && ids!=null && ids!="" && ids!=0)
    {
        // passer param pour "pas de sélection" => utiliser inner pour avoir que commandes avec commission
        if (dhxlAffAffiliates.grid.getSelectedRowId() == null){
            hasAffiliateSelection = 0;
        }else{
            hasAffiliateSelection = 1;
        }
        $.post("index.php?ajax=1&act=all_win-affiliation_manager_orders_get&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{'ids': ids,'hasAffiliateSelection':hasAffiliateSelection},function(data)
        {
            dhxlAffOrders.grid.parse(data);
            nb=dhxlAffOrders.grid.getRowsNum();
            for(var i=0;i<dhxlAffOrders.grid.getColumnsNum();i++)
            {
                if (dhxlAffOrders.grid.getFilterElement(i)!=null && oldFilters[dhxlAffOrders.grid.getColumnId(i)]!=undefined)
                {
                    dhxlAffOrders.grid.getFilterElement(i).value=oldFilters[dhxlAffOrders.grid.getColumnId(i)];
                }
            }
            dhxlAffOrders.grid.filterByAll();

            // UISettings
            loadGridUISettings(dhxlAffOrders.grid);
            dhxlAffOrders.grid._first_loading=0;
        });
    }
}


//#####################################
//############ Color functions
//#####################################
function colorCommissions()
{
    var invoiceds = dhxlAffCommissions.grid.findCell("invoiced",4);
    $.each( invoiceds, function( num, infos ) {
        dhxlAffCommissions.grid.cells(infos[0],infos[1]).setBgColor('f9ca6e');
    });
    var paids = dhxlAffCommissions.grid.findCell("paid",4);
    $.each( paids, function( num, infos ) {
        dhxlAffCommissions.grid.cells(infos[0],infos[1]).setBgColor('8cc085');
    });
    var paids = dhxlAffCommissions.grid.findCell("paid_order",4);
    $.each( paids, function( num, infos ) {
        dhxlAffCommissions.grid.cells(infos[0],infos[1]).setBgColor('8cc085');
    });
    var cancelleds = dhxlAffCommissions.grid.findCell("cancelled",4);
    $.each( cancelleds, function( num, infos ) {
        dhxlAffCommissions.grid.cells(infos[0],infos[1]).setBgColor('db9797');
    });
    var waitings = dhxlAffCommissions.grid.findCell("waiting",4);
    $.each( waitings, function( num, infos ) {
        dhxlAffCommissions.grid.cells(infos[0],infos[1]).setBgColor('D0E5FF');
    });
}

function colorCommissionsConcernedByAffiliate(affiliate_ids)
{
    clearColorOrders();
    clearColorAffiliates();
    clearColorCommissions();

    dhxlAffCommissions.grid.clearSelection();
    dhxlAffOrders.grid.clearSelection();

    $.post("index.php?ajax=1&act=all_win-affiliation_manager_commissions_concerned_by_affiliate&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{'ids':affiliate_ids},function(data){
        if(data.liste!="")
        {
            $.each( data.liste, function( num, id ) {
                if(dhxlAffCommissions.grid.getRowIndex(id)>=0)
                    dhxlAffCommissions.grid.setRowColor(id,"e2c7d4");
            });
        }
    }, "json");
}

function colorCommissionsAndAffiliateConcernedByOrder(order_ids)
{
    clearColorOrders();
    clearColorAffiliates();
    clearColorCommissions();

    dhxlAffCommissions.grid.clearSelection();
    dhxlAffAffiliates.grid.clearSelection();

    $.post("index.php?ajax=1&act=all_win-affiliation_manager_commissions_concerned_by_order&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{'ids':order_ids},function(data){
        if(data.liste!="")
        {
            $.each( data.liste, function( num, id ) {
                if(dhxlAffCommissions.grid.getRowIndex(id)>=0)
                    dhxlAffCommissions.grid.setRowColor(id,"e2c7d4");
            });
        }
        if(data.liste_aff!="")
        {
            $.each( data.liste_aff, function( num, id ) {
                if(dhxlAffAffiliates.grid.getRowIndex(id)>=0)
                    dhxlAffAffiliates.grid.setRowColor(id,"e2c7d4");
            });
        }
    }, "json");
}

function clearColorCommissions()
{
    dhxlAffCommissions.grid.forEachRow(function(id){
        dhxlAffCommissions.grid.setRowColor(id,"");
     });
    colorCommissions();
}

function colorOrderAndAffiliateConcernedByCommission(id_commissions)
{
    clearColorOrders();
    clearColorAffiliates();
    clearColorCommissions();

    dhxlAffOrders.grid.clearSelection();
    dhxlAffAffiliates.grid.clearSelection();

    $.post("index.php?ajax=1&act=all_win-affiliation_manager_concerned_by_commission&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{'ids':id_commissions},function(data){
        if(data.listecom!="")
        {
            $.each( data.listecom, function( num, id ) {
                if(dhxlAffCommissions.grid.getRowIndex(id)>=0)
                    dhxlAffCommissions.grid.setRowColor(id,"ffe1f0");
            });
        }
        if(data.listepaid!="")
        {
            $.each( data.listepaid, function( num, id ) {
                if(dhxlAffCommissions.grid.getRowIndex(id)>=0)
                    dhxlAffCommissions.grid.setRowColor(id,"96e1ef");
            });
        }
        if(data.listebis!="")
        {
            $.each( data.listebis, function( num, id ) {
                if(dhxlAffAffiliates.grid.getRowIndex(id)>=0)
                    dhxlAffAffiliates.grid.setRowColor(id,"e2c7d4");
            });
        }
        if(data.liste!="")
        {
            $.each( data.liste, function( num, id ) {
                if(dhxlAffOrders.grid.getRowIndex(id)>=0)
                    dhxlAffOrders.grid.setRowColor(id,"e2c7d4");
            });
        }
    }, "json");
}

function clearColorOrders()
{
    dhxlAffOrders.grid.forEachRow(function(id){
        dhxlAffOrders.grid.setRowColor(id,"");
     });
}

function clearColorAffiliates()
{
    dhxlAffAffiliates.grid.forEachRow(function(id){
        dhxlAffAffiliates.grid.setRowColor(id,"");
     });
}

function showColorHelp()
{
    if (!dhxWins.isWindow("wHelpAff"))
    {
        wHelpAff = dhxWins.createWindow("wHelpAff", ($(window).width()/2-400), 100, 800, 300);
        wHelpAff.setText("<?php echo _l('Help for color codes used', 1); ?>");
        wHelpAff.show();

        dhxlHelpAff=wHelpAff.attachLayout("1C");
        dhxlHelpAff_w = dhxlHelpAff.cells('a');
        dhxlHelpAff_w.hideHeader();
        dhxlHelpAff_add = dhxlHelpAff_w;
        HelpAffGrid = dhxlHelpAff_w.attachGrid();
        HelpAffGrid._name='HelpAffGrid';
        HelpAffGrid.setImagePath("lib/js/imgs/");
        HelpAffGrid.enableDragAndDrop(false);
        HelpAffGrid.enableMultiselect(false);

        $.post("index.php?ajax=1&act=all_win-affiliation_manager_help_xml&id_lang="+SC_ID_LANG,function(data)
        {
            HelpAffGrid.parse(data);
        });
    }else{
        wHelpAff.setDimension(800, 300);
        wHelpAff.show();
    }
}

function colorMode(partner,affiliate)
{
    if(partner!=undefined && partner==true)
    {
        idxMode=dhxlAffPartners.grid.getColIndexById('mode');
        idxDuration=dhxlAffPartners.grid.getColIndexById('duration');
        dhxlAffPartners.grid.forEachRow(function(rid){
            var mode = dhxlAffPartners.grid.cells(rid, idxMode).getValue();
            var duration = dhxlAffPartners.grid.cells(rid, idxDuration).getValue();

            if(mode=="limited" && (duration==undefined || duration==null || duration=="" || duration<=0))
                dhxlAffPartners.grid.cells(rid,idxDuration).setBgColor('#ff9b9b');
            else
                dhxlAffPartners.grid.cells(rid,idxDuration).setBgColor('');
         });
    }
    if(affiliate!=undefined && affiliate==true)
    {
        idxMode=dhxlAffAffiliates.grid.getColIndexById('scaff_partner_mode');
        idxDuration=dhxlAffAffiliates.grid.getColIndexById('scaff_partner_duration');
        dhxlAffAffiliates.grid.forEachRow(function(rid){
            var mode = dhxlAffAffiliates.grid.cells(rid, idxMode).getValue();
            var duration = dhxlAffAffiliates.grid.cells(rid, idxDuration).getValue();

            if(mode=="limited" && (duration==undefined || duration==null || duration=="" || duration<=0))
                dhxlAffAffiliates.grid.cells(rid,idxDuration).setBgColor('#ff9b9b');
            else
                dhxlAffAffiliates.grid.cells(rid,idxDuration).setBgColor('');
         });
    }
}


function askCouponCode(id_partner, prefix)
{
    var ask = '<?php echo _l('Please enter reduction code', 1); ?>';
    if(prefix!=undefined && prefix!="")
        ask = prefix+" "+ask;
    var code = prompt(ask, '');
    if(code!=undefined && code!=null && code!="")
    {
        $.post("index.php?ajax=1&act=all_win-affiliation_manager_reductioncode_exist&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{'id_partner':id_partner,'code':code},function(data){
            if(data=="OK")
            {
                $.post("index.php?ajax=1&act=all_win-affiliation_manager_reductioncode_update&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{'id_partner':id_partner,'code':code},function(data){
                    if(data!=undefined && data!="" && data!=null && data!=0)
                    {
                        idxCouponCode = dhxlAffPartners.grid.getColIndexById('coupon_code');
                        dhxlAffPartners.grid.cells(id_partner, idxCouponCode).setValue(code);

                        dhxlAffPartners.grid.setUserData(id_partner,"id_coupon", code);

                        wViewReduction = dhxWins.createWindow("wOpenReduction", 50, 50, $(window).width()-75, $(window).height()-75);
                        wViewReduction.setText('<?php echo _l('Reduction code in Prestashop Backoffice', 1); ?>');
                        <?php if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) { ?>
                         wViewReduction.attachURL("<?php echo SC_PS_PATH_ADMIN_REL; ?>index.php?tab=AdminCartRules&updatecart_rule&id_cart_rule="+data+"&id_lang="+SC_ID_LANG+"&token=<?php echo $sc_agent->getPSToken('AdminCartRules'); ?>");
                        <?php }
else
{ ?>
                        wViewReduction.attachURL("<?php echo SC_PS_PATH_ADMIN_REL; ?>index.php?tab=AdminDiscounts&updatediscount&id_discount="+data+"&id_lang="+SC_ID_LANG+"&token=<?php echo $sc_agent->getPSToken('AdminDiscounts'); ?>");
                        <?php } ?>
                    }
                });
            }
            else if(data=="KO")
            {
                askCouponCode(id_partner, '<?php echo _l('This code already exists or is invalid.', 1); ?>');
            }
        });
    }
}
</script>
