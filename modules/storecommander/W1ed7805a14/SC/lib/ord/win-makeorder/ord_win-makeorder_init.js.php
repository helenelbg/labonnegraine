<?php
    $cus_id_customer = Tools::getValue('id_customer', 'null');
    $cus_id_shop_customer = Tools::getValue('id_shop_customer', 'null');
    $init_default_data = (!empty($cus_id_customer) && !empty($cus_id_shop_customer) ? 'true' : 'false');
?>
<?php echo '<script type="text/javascript">'; ?>
    let preselected_id_customer = <?php echo $cus_id_customer; ?>;
    let preselected_id_shop_customer = <?php echo $cus_id_shop_customer; ?>;
    let cus_default_data = (preselected_id_customer !== null && preselected_id_shop_customer !== null);
    dhxlMakeOrderLayout=wMakeOrder.attachLayout("3E");

    var makeOrder_selected_customer = null;
    var makeOrder_selected_cart = null;

    var makeOrderRow1 = dhxlMakeOrderLayout.cells('a');
    var makeOrderRow1Layout=makeOrderRow1.attachLayout("3W");



    var makeOrder_cell_customer = makeOrderRow1Layout.cells('a');
    var makeOrder_cell_customerInfo = makeOrderRow1Layout.cells('b');
    var makeOrder_cell_coupon = makeOrderRow1Layout.cells('c');

    var makeOrderRow2 = dhxlMakeOrderLayout.cells('b');

    var makeOrderRow2Col1Layout=makeOrderRow2.attachLayout("2U");
    var makeOrder_cell_searchProduct = makeOrderRow2Col1Layout.cells('a');
    var makeOrder_cell_cart = makeOrderRow2Col1Layout.cells('b');

    var makeOrderRow3Layout=dhxlMakeOrderLayout.cells('c').attachLayout("3W");
    var makeOrder_cell_crossSelling = makeOrderRow3Layout.cells('a');
    var makeOrder_cell_addresses = makeOrderRow3Layout.cells('b');
    var makeOrder_cell_validationForm= makeOrderRow3Layout.cells('c');

    /*
    CUSTOMER
     */

    makeOrder_cell_customer.hideHeader();

    var makeOrder_customer_tb = makeOrder_cell_customer.attachToolbar();
      makeOrder_customer_tb.setIconset('awesome');
    makeOrder_customer_tb.addText("title", 1, '<b><?php echo _l('Customer', 1); ?><b/>');

    makeOrder_customer_tb.addButton('makeOrder_customer_refresh',100,'','fa fa-sync green','fa fa-sync green');
    makeOrder_customer_tb.setItemToolTip('makeOrder_customer_refresh','<?php echo _l('Refresh', 1); ?>');

    <?php $default_shop = Configuration::get('PS_SHOP_DEFAULT'); ?>
    var makeOrder_shop = '<?php echo !empty($default_shop) ? $default_shop : 1; ?>';
    <?php if (SCMS)
{
    $name = ''; ?>
        var opts = [
            <?php
            $shops = Shop::getShops(false);
    foreach ($shops as $shop) { ?>
            ['shop-<?php echo $shop['id_shop']; ?>', 'obj', '<?php echo str_replace("'", "\'", $shop['name']); ?>', ''],
            <?php } ?>
        ];
        makeOrder_shop = (cus_default_data?preselected_id_shop_customer:makeOrder_shop);
        let default_shop_name = '';
        opts.forEach(function(opt_item){
            let opt_item_shop_split = opt_item[0].split('-');
            let current_id_shop = opt_item_shop_split[1];
            if(current_id_shop==makeOrder_shop) {
                default_shop_name = opt_item[2];
            }
        });
        makeOrder_customer_tb.addButtonSelect("makeOrder_shop", 100, default_shop_name, opts, "","",false,true);
        makeOrder_customer_tb.setItemToolTip('makeOrder_shop','<?php echo _l('Shop'); ?>');
    <?php
} ?>
    makeOrder_customer_tb.addButton("makeOrder_add_customer", 100, "", "fa fa-plus-circle green", "fa fa-plus-circle green");
    makeOrder_customer_tb.setItemToolTip('makeOrder_add_customer','<?php echo _l('Create a new customer', 1); ?>');
    makeOrder_customer_tb.addButton("makeOrder_add_ps", 100, "", "fa fa-prestashop", "fa fa-prestashop");
    makeOrder_customer_tb.setItemToolTip('makeOrder_add_ps','<?php echo _l('Create new customer with the PrestaShop form', 1); ?>');
    makeOrder_customer_tb.addButton("makeOrder_view_customer_ps", 100, "", "fad fa-eye", "fad fa-eye");
    makeOrder_customer_tb.setItemToolTip('makeOrder_view_customer_ps','<?php echo _l('View selected customer in Prestashop', 1); ?>');
    <?php if (_r('ACT_CUS_LOGIN_AS_CUSTOMER')) { ?>
    makeOrder_customer_tb.addButton("makeOrder_user_go", 100, "", "fad fa-walking orange", "fad fa-walking orange");
    makeOrder_customer_tb.setItemToolTip('makeOrder_user_go','<?php echo _l('login as selected customer on the front office', 1); ?>');
    <?php } ?>
    makeOrder_customer_tb.addButton('makeOrder_customer_select',100,'','fa fa-check green','fa fa-check green');
    makeOrder_customer_tb.setItemToolTip('makeOrder_customer_select','<?php echo _l('Load customer', 1); ?>');

    makeOrder_customer_tb.attachEvent("onClick", function (id) {
        <?php if (SCMS) { ?>
        var shop = id.split("-");
        if(shop[0]!=undefined && shop[0]=="shop")
        {
            if(shop[1]!=undefined && shop[1]=="all")
            {
                makeOrder_customer_tb.setItemText('makeOrder_shop','<?php echo _l('All shops', 1); ?>');
                makeOrder_shop = 'all';
            }
            <?php foreach ($shops as $shop) { ?>
            if(shop[1]!=undefined && shop[1]=="<?php echo $shop['id_shop']; ?>")
            {
                makeOrder_customer_tb.setItemText('makeOrder_shop','<?php echo str_replace("'", "\'", $shop['name']); ?>');
                makeOrder_shop = "<?php echo $shop['id_shop']; ?>";
            }
            <?php } ?>
            makeOrder_customer_tb.setListOptionSelected('makeOrder_shop',id);
            displayMOCustomers();
        }
        <?php } ?>
        if(id=='makeOrder_add_customer')
        {
            if (!dhxWins.isWindow("wCreateNewCustomer"))
            {
                let wCreateNewCustomerHeight = 250;
                <?php if (_s('CUS_USE_COMPANY_FIELDS') && SCI::getConfigurationValue('PS_B2B_ENABLE')){ ?>
                wCreateNewCustomerHeight = 350;
                <?php } ?>
                wCreateNewCustomer = dhxWins.createWindow("wCreateNewCustomer", 120, 50, 370, wCreateNewCustomerHeight);
                wCreateNewCustomer.denyPark();
                wCreateNewCustomer.denyResize();
                wCreateNewCustomer.setText('<?php echo _l('Quick customer creation', 1); ?>');
                $.get("index.php?ajax=1&act=ord_win-makeorder_customer_create_form&id_lang="+SC_ID_LANG,function(data){
                    $('#jsExecute').html(data);
                });
            }
        }
        if(id=="makeOrder_add_ps")
        {
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
                pushOneUsage('win-makeorder_init-bo-link-admincustomers_addcustomer','ord');
                wNewCustomer.attachEvent("onClose", function(win){
                    displayMOCustomers();
                    return true;
                });
            }
        }
        if(id=="makeOrder_view_customer_ps")
        {
            let selectedId = makeOrder_customer_grid.getSelectedRowId();
            if(selectedId!=undefined && selectedId!=null && selectedId!="" && selectedId!=0)
            {
                let wName= selectedId+"wViewCustomer"+new Date().getTime();
                <?php if (version_compare(_PS_VERSION_, '1.6.0.0', '>=')) { ?>
                wViewCustomer = dhxWins.createWindow(wName, 20, 20, $(window).width()-40, $(window).height()-40);
                <?php }
    else
    { ?>
                wViewCustomer = dhxWins.createWindow(wName, 20, 20, $(window).width()-40, $(window).height()-40);
                <?php } ?>
                wViewCustomer.setText('<?php echo _l('Customer', 1); ?> '+selectedId);
                wViewCustomer.attachURL("<?php echo SC_PS_PATH_ADMIN_REL; ?>index.php?tab=AdminCustomers&viewcustomer&id_customer="+selectedId+"&token=<?php echo $sc_agent->getPSToken('AdminCustomers'); ?>");
            }
        }
        if(id=="makeOrder_user_go")
        {
            let selectedId = makeOrder_customer_grid.getSelectedRowId();
            if(selectedId!=undefined && selectedId!=null && selectedId!="" && selectedId!=0)
            {
                connectAsUser("<?php echo SCI::getConfigurationValue('SC_SALT'); ?>","<?php echo $sc_agent->id_employee; ?>",selectedId,makeOrder_shop);
            }
        }
        if(id=="makeOrder_customer_refresh")
        {
            preselected_id_customer = null;
            makeOrder_selected_customer = 0;
            displayMOCustomers();
        }
        if(id=="makeOrder_customer_select")
        {
            var selectedId = makeOrder_customer_grid.getSelectedRowId();
            if(selectedId!=undefined && selectedId!=null && selectedId!="" && selectedId!=0)
            {
                makeOrder_selected_customer = selectedId;
                makeOrder_selected_cart = null;
                displayMOCart();
                displayMOsearchProduct();
                displayMOaddresses();
                displayMOCustomerInfo();
                displayMOcoupons();
                displayMOvalidationForm();
            }
        }
    });

    makeOrder_customer_grid = makeOrder_cell_customer.attachGrid();
    makeOrder_customer_grid._name='makeOrder_customer_grid';
    makeOrder_customer_grid.setImagePath("lib/js/imgs/");
    makeOrder_customer_grid.enableDragAndDrop(false);
    makeOrder_customer_grid.enableMultiselect(false);

    var makeOrder_customer_old_filter_params = "";
    var makeOrder_customer_filter_params = "";
    var makeOrder_customer_oldFilters = new Object();
    makeOrder_customer_grid.attachEvent("onRowDblClicked", function(rId){
        if(rId!=undefined && rId!=null && rId!="" && rId!=0)
        {
            makeOrder_selected_customer = rId;
            makeOrder_selected_cart = null;
            displayMOCart();
            displayMOsearchProduct();
            displayMOaddresses();
            displayMOCustomerInfo();
            displayMOcoupons();
            displayMOvalidationForm();
        }
        return true;
    });
    makeOrder_customer_grid.attachEvent("onFilterEnd", function(elements){
        makeOrder_customer_old_filter_params = makeOrder_customer_filter_params;
        makeOrder_customer_filter_params = "";
        var nb_cols = makeOrder_customer_grid.getColumnsNum();
        var need_refresh = false;
        if(nb_cols>0)
        {
            for(var i=0; i<nb_cols; i++)
            {
                var colId=makeOrder_customer_grid.getColumnId(i);
                if(makeOrder_customer_grid.getFilterElement(i)!=null
                    && ( colId =="id_customer"
                        || colId =="firstname"
                        || colId =="lastname"
                        || colId =="email"
                        || colId =="company"
                    )
                )
                {
                    var colValue = makeOrder_customer_grid.getFilterElement(i).value;
                    if((colValue!=null && colValue!="") || (makeOrder_customer_oldFilters[colId]!=null && makeOrder_customer_oldFilters[colId]!=""))
                    {
                        if(makeOrder_customer_filter_params!="")
                            makeOrder_customer_filter_params = makeOrder_customer_filter_params + ",";
                        makeOrder_customer_filter_params = makeOrder_customer_filter_params + colId+"|||"+colValue;
                        makeOrder_customer_oldFilters[colId] = makeOrder_customer_grid.getFilterElement(i).value;
                        var need_refresh = true;
                    }
                }
            }
        }
        if(need_refresh && makeOrder_customer_filter_params!="" && makeOrder_customer_filter_params!=makeOrder_customer_old_filter_params)
        {
            displayMOCustomers();
        }
    });

    function displayMOCustomers()
    {
        makeOrder_customer_oldFilters=new Array();
        for(var i=0,l=makeOrder_customer_grid.getColumnsNum();i<l;i++)
        {
            if (makeOrder_customer_grid.getFilterElement(i)!=null && makeOrder_customer_grid.getFilterElement(i).value!='')
                makeOrder_customer_oldFilters[makeOrder_customer_grid.getColumnId(i)]=makeOrder_customer_grid.getFilterElement(i).value;

        }

        if(makeOrder_customer_filter_params === '' && preselected_id_customer){
            makeOrder_customer_filter_params = ['id_customer|||'+preselected_id_customer];
            makeOrder_customer_filter_params = makeOrder_customer_filter_params.join();
        }

        makeOrder_cell_customer.progressOn();
        makeOrder_customer_grid.clearAll(true);
        $.post("index.php?ajax=1&act=ord_win-makeorder_customer_get&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{'id_shop': makeOrder_shop, "filter_params": makeOrder_customer_filter_params},function(data)
        {
            makeOrder_cell_customer.progressOff();
            makeOrder_customer_grid.parse(data);

            for(var i=0;i<makeOrder_customer_grid.getColumnsNum();i++)
            {
                if (makeOrder_customer_grid.getFilterElement(i)!=null && makeOrder_customer_oldFilters[makeOrder_customer_grid.getColumnId(i)]!=undefined)
                {
                    makeOrder_customer_grid.getFilterElement(i).value=makeOrder_customer_oldFilters[makeOrder_customer_grid.getColumnId(i)];
                }
            }
            makeOrder_customer_grid.filterByAll();
            if(makeOrder_customer_filter_params === '' && preselected_id_customer){
                makeOrder_selected_customer = preselected_id_customer;
                makeOrder_customer_grid.selectRowById(preselected_id_customer,true,true,true);
                makeOrder_customer_grid.callEvent('onRowDblClicked',[preselected_id_customer,2]);
            }
        });
    }
    displayMOCustomers();

    /*
    INFO CUSTOMER
     */
    makeOrder_cell_customerInfo.setWidth(340);
    makeOrder_cell_customerInfo_tabs=makeOrder_cell_customerInfo.attachTabbar({
        tabs: [
            {id: "stats", text: "<?php echo _l('Stats'); ?>", active: true},
            {id: "notes", text: "<?php echo _l('Private notes'); ?>"}
        ]
    });

    let makeOrder_cell_customerInfo_tabs_stats_tb = makeOrder_cell_customerInfo_tabs.tabs("notes").attachToolbar();
    makeOrder_cell_customerInfo_tabs_stats_tb.setIconset('awesome');
    makeOrder_cell_customerInfo_tabs_stats_tb.addButton("save_note", 100, "", "fa fa-save blue", "fa fa-save blue");
    makeOrder_cell_customerInfo_tabs_stats_tb.setItemToolTip('save_note','<?php echo _l('Save'); ?>');
    makeOrder_cell_customerInfo_tabs_stats_tb.attachEvent("onClick", function(id){
        if(id=='save_note'){
            let makeOrder_note = $('#makeOrder_note_textarea').val();
            $.post('index.php?ajax=1&act=cus_notes_update',{id_customer:makeOrder_selected_customer,content:makeOrder_note},function(data)
            {
                if(data == 'OK') {
                    dhtmlx.message({text:'<?php echo _l('Note saved'); ?>',type:'success',expire:5000});
                } else {
                    dhtmlx.message({text:'<?php echo _l('Error'); ?>',type:'error',expire:5000});
                }
            });
        }
    });
    function displayMOCustomerInfo()
    {
        makeOrder_cell_customerInfo_tabs.tabs("stats").setActive();
        $.post('index.php?ajax=1&act=cus_notes_get',{id_customer:makeOrder_selected_customer},function(data)
        {
            makeOrder_cell_customerInfo_tabs.tabs("notes").attachHTMLString('<textarea id="makeOrder_note_textarea" style="resize: none;box-sizing: border-box;width: 100%;height: 100%;">'+data+'</textarea>');
            if(data !== undefined && data !== '') {
                makeOrder_cell_customerInfo_tabs.tabs("notes").setActive();
            }
        });

        if(makeOrder_selected_customer!=undefined && makeOrder_selected_customer!=null && makeOrder_selected_customer!='' && makeOrder_selected_customer!=0)
        {
            makeOrder_cell_customerInfo_tabs.tabs("stats").attachURL("index.php?ajax=1&act=ord_win-makeorder_customerinfo_get&id_lang=" + SC_ID_LANG + "&id_customer="+makeOrder_selected_customer+"&" + new Date().getTime());
        }
    }


    /*
    COUPONS
     */
    makeOrder_cell_coupon.hideHeader();
    var makeOrder_cell_coupon_tb = makeOrder_cell_coupon.attachToolbar();
      makeOrder_cell_coupon_tb.setIconset('awesome');
    makeOrder_cell_coupon_tb.addText("title",1,'<b><?php echo _l('Available discount vouchers', 1); ?></b>');
    makeOrder_cell_coupon_tb.addButton('makeOrder_coupon_refresh',100,'','fa fa-sync green','fa fa-sync green');
    makeOrder_cell_coupon_tb.setItemToolTip('makeOrder_coupon_refresh','<?php echo _l('Refresh', 1); ?>');
    makeOrder_cell_coupon_tb.addButton("manage_coupon_add_bo", 100, "", "fa fa-prestashop", "fa fa-prestashop");
    makeOrder_cell_coupon_tb.setItemToolTip('manage_coupon_add_bo','<?php echo _l('Create new voucher with the PrestaShop form'); ?>');
    makeOrder_cell_coupon_tb.addButton("manage_coupon_bo", 100, "", "fad fa-search", "fad fa-search");
    makeOrder_cell_coupon_tb.setItemToolTip('manage_coupon_bo','<?php echo _l('Edit voucher in PrestaShop'); ?>');
    makeOrder_cell_coupon_tb.attachEvent("onClick", function (id) {
        if(id=="makeOrder_coupon_refresh")
        {
            displayMOcoupons();
        }
        if(id=="manage_coupon_add_bo")
        {
            if(makeOrder_selected_customer!=undefined && makeOrder_selected_customer!=null && makeOrder_selected_customer!='' && makeOrder_selected_customer!=0)
            {
                if (!dhxWins.isWindow("wNewCoupon"))
                {
                    <?php if (version_compare(_PS_VERSION_, '1.7.0.0', '>=')) { ?>
                    wNewCoupon = dhxWins.createWindow("wNewCoupon", 50, 50, 1460, $(window).height()-75);
                    <?php }
    else
    { ?>
                    wNewCoupon = dhxWins.createWindow("wNewCoupon", 50, 50, 1000, $(window).height()-75);
                    <?php } ?>
                    wNewCoupon.setText('<?php echo _l('Create the new coupon and close this window to refresh the grid', 1); ?>');
                    wNewCoupon.attachURL("<?php echo SC_PS_PATH_ADMIN_REL; ?>index.php?<?php echo version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? 'controller=AdminCartRules' : 'tab=AdminCartRules'; ?>&addcart_rule&token=<?php echo $sc_agent->getPSToken('AdminCartRules'); ?>");
                    wNewCoupon.attachEvent("onClose", function(win){
                        displayMOcoupons();
                        return true;
                    });
                }
            }
        }
        if(id=="manage_coupon_bo")
        {
            if (!dhxWins.isWindow("wViewCoupon"))
            {
                <?php if (version_compare(_PS_VERSION_, '1.7.0.0', '>=')) { ?>
                wViewCoupon = dhxWins.createWindow("wViewCoupon", 50, 50, 1460, $(window).height()-75);
                <?php }
    else
    { ?>
                wViewCoupon = dhxWins.createWindow("wViewCoupon", 50, 50, 1000, $(window).height()-75);
                <?php } ?>
                wViewCoupon.setText('<?php echo _l('View coupon in back office', 1); ?>');
                let id_cart_rule = makeOrder_coupon_grid.getSelectedRowId();
                wViewCoupon.attachURL("<?php echo SC_PS_PATH_ADMIN_REL; ?>index.php?<?php echo version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? 'controller=AdminCartRules' : 'tab=AdminCartRules'; ?>&id_cart_rule="+id_cart_rule+"&updatecart_rule&token=<?php echo $sc_agent->getPSToken('AdminCartRules'); ?>");
                wViewCoupon.attachEvent("onClose", function(win){
                    displayMOcoupons();
                    return true;
                });
            }
        }
    });

    makeOrder_coupon_grid = makeOrder_cell_coupon.attachGrid();
    makeOrder_coupon_grid._name='makeOrder_coupon_grid';
    makeOrder_coupon_grid.setImagePath("lib/js/imgs/");
    makeOrder_coupon_grid.enableDragAndDrop(false);
    makeOrder_coupon_grid.enableMultiselect(false);

    function displayMOcoupons(){
        makeOrder_coupon_grid.clearAll(true);
        makeOrder_cell_coupon.progressOn();
        $.post("index.php?ajax=1&act=ord_win-makeorder_coupon_get&id_lang=" + SC_ID_LANG + "&" + new Date().getTime(),
            {
                "id_customer": makeOrder_selected_customer,
                "id_lang":SC_ID_LANG,
                "id_shop": makeOrder_shop,
                "id_cart": makeOrder_selected_cart,
            }
            , function (data) {
                makeOrder_cell_coupon.progressOff();
                makeOrder_coupon_grid.parse(data);
            });
    }

    makeOrder_coupon_grid.attachEvent("onEditCell",function(stage,rId,cInd){
        let coltype=makeOrder_coupon_grid.getColType(cInd);
        if (stage==1 && this.editor && this.editor.obj && coltype!='txt' && coltype!='txttxt') this.editor.obj.select();

        if(stage == 2){
            return false;
        }
        return true;
    });

    /*
    PRODUCT SEARCH
     */
    makeOrder_cell_searchProduct.hideHeader();

    var makeOrder_searchProduct_tb = makeOrder_cell_searchProduct.attachToolbar();
      makeOrder_searchProduct_tb.setIconset('awesome');
    makeOrder_searchProduct_tb.addText("title",1,'<b><?php echo _l('Search for products', 1); ?><b/>');
    makeOrder_searchProduct_tb.addButton('makeOrder_searchProduct_refresh',100,'','fa fa-sync green','fa fa-sync green');
    makeOrder_searchProduct_tb.setItemToolTip('makeOrder_searchProduct_refresh','<?php echo _l('Refresh', 1); ?>');
    makeOrder_searchProduct_tb.addButton('makeOrder_searchProduct_addincart',100,'','fa fa-plus-circle green','fa fa-plus-circle green');
    makeOrder_searchProduct_tb.setItemToolTip('makeOrder_searchProduct_addincart','<?php echo _l('Add to cart', 1); ?>');
    makeOrder_searchProduct_tb.addButton("gotocatalog", 100, "", "fad fa-external-link green", "fad fa-external-link green");
    makeOrder_searchProduct_tb.setItemToolTip('gotocatalog','<?php echo _l('Open the product in Sc catalog'); ?>');

    makeOrder_searchProduct_tb.attachEvent("onClick", function (id) {
        if(id=="makeOrder_searchProduct_refresh")
        {
            displayMOsearchProduct();
        }
        if(id=="makeOrder_searchProduct_addincart")
        {
            var selectedIds = makeOrder_searchProduct_grid.getSelectedRowId();
            if(selectedIds!=undefined && selectedIds!=null && selectedIds!='' && selectedIds!=0)
            {
                let qty_list = {};
                let id_list = selectedIds.split(',');
                id_list.forEach(function(id){
                    let idxQtyNeeded = makeOrder_searchProduct_grid.getColIndexById('quantity_needed');
                    let nQty = makeOrder_searchProduct_grid.cells(id,idxQtyNeeded).getValue();
                    if(nQty=== null || nQty === undefined || nQty === '') {
                        nQty = 1;
                    }
                    qty_list[id] = nQty;
                });

                addInCart(selectedIds,qty_list);
            }
        }
        if (id=='gotocatalog')
        {
            var selectedIds = makeOrder_searchProduct_grid.getSelectedRowId();
            if(selectedIds!=undefined && selectedIds!=null && selectedIds!='' && selectedIds!=0)
            {
                var path = makeOrder_searchProduct_grid.getUserData(selectedIds, "path_pdt");
                let url = "?page=cat_tree&open_cat_grid="+path;
                window.open(url,'_blank');
            }
        }
    });

    makeOrder_searchProduct_grid = makeOrder_cell_searchProduct.attachGrid();
    makeOrder_searchProduct_grid._name='makeOrder_searchProduct_grid';
    makeOrder_searchProduct_grid.setImagePath("lib/js/imgs/");
    makeOrder_searchProduct_grid.enableDragAndDrop(false);
    makeOrder_searchProduct_grid.enableMultiselect(true);

    makeOrder_searchProduct_grid.attachEvent("onSelectStateChanged", function(rId){
        displayMOcrossSelling();
    });

    var makeOrder_searchProduct_old_filter_params = "";
    var makeOrder_searchProduct_filter_params = "";
    var makeOrder_searchProduct_oldFilters = new Object();
    makeOrder_searchProduct_grid.attachEvent("onFilterEnd", function(elements){
        makeOrder_searchProduct_old_filter_params = makeOrder_searchProduct_filter_params;
        makeOrder_searchProduct_filter_params = "";
        var nb_cols = makeOrder_searchProduct_grid.getColumnsNum();
        var need_refresh = false;
        if(nb_cols>0)
        {
            for(var i=0; i<nb_cols; i++)
            {
                var colId=makeOrder_searchProduct_grid.getColumnId(i);
                if(makeOrder_searchProduct_grid.getFilterElement(i)!=null
                    && ( colId =="id_product"
                        || colId =="id_product_attribute"
                        || colId =="reference"
                        || colId =="ean13"
                        || colId =="product"
                    )
                )
                {
                    var colValue = makeOrder_searchProduct_grid.getFilterElement(i).value;
                    if((colValue!=null && colValue!="") || (makeOrder_searchProduct_oldFilters[colId]!=null && makeOrder_searchProduct_oldFilters[colId]!=""))
                    {
                        if(makeOrder_searchProduct_filter_params!="")
                            makeOrder_searchProduct_filter_params = makeOrder_searchProduct_filter_params + ",";
                        makeOrder_searchProduct_filter_params = makeOrder_searchProduct_filter_params + colId+"|||"+colValue;
                        makeOrder_searchProduct_oldFilters[colId] = makeOrder_searchProduct_grid.getFilterElement(i).value;
                        var need_refresh = true;
                    }
                }
            }
        }
        if(need_refresh && makeOrder_searchProduct_filter_params!="" && makeOrder_searchProduct_filter_params!=makeOrder_searchProduct_old_filter_params)
        {
            displayMOsearchProduct();
        }
    });

    function displayMOsearchProduct()
    {
        if(makeOrder_selected_customer!=undefined && makeOrder_selected_customer!=null && makeOrder_selected_customer!='' && makeOrder_selected_customer!=0)
        {
            makeOrder_searchProduct_oldFilters=new Array();
            for(var i=0,l=makeOrder_searchProduct_grid.getColumnsNum();i<l;i++)
            {
                if (makeOrder_searchProduct_grid.getFilterElement(i)!=null && makeOrder_searchProduct_grid.getFilterElement(i).value!='')
                    makeOrder_searchProduct_oldFilters[makeOrder_searchProduct_grid.getColumnId(i)]=makeOrder_searchProduct_grid.getFilterElement(i).value;

            }

            makeOrder_cell_searchProduct.progressOn();
            makeOrder_searchProduct_grid.clearAll(true);
            makeOrder_crossSelling_grid.clearAll(true);
            $.post("index.php?ajax=1&act=ord_win-makeorder_product_get&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{"id_customer": makeOrder_selected_customer,"id_shop": makeOrder_shop, 'filter_params': makeOrder_searchProduct_filter_params},function(data)
            {
                makeOrder_cell_searchProduct.progressOff();
                makeOrder_searchProduct_grid.parse(data);

                for(var i=0;i<makeOrder_searchProduct_grid.getColumnsNum();i++)
                {
                    if (makeOrder_searchProduct_grid.getFilterElement(i)!=null && makeOrder_searchProduct_oldFilters[makeOrder_searchProduct_grid.getColumnId(i)]!=undefined)
                    {
                        makeOrder_searchProduct_grid.getFilterElement(i).value=makeOrder_searchProduct_oldFilters[makeOrder_searchProduct_grid.getColumnId(i)];
                    }
                }
                makeOrder_searchProduct_grid.filterByAll();
            });
        }
    }

    /*
    CROSS SELLING
     */
    makeOrder_cell_crossSelling.hideHeader();

    var makeOrder_crossSelling_tb = makeOrder_cell_crossSelling.attachToolbar();
      makeOrder_crossSelling_tb.setIconset('awesome');

    makeOrder_crossSelling_tb.addText("title",1,'<b><?php echo _l('Cross-selling: suggest accessories', 1); ?><b/>');
    makeOrder_crossSelling_tb.addButton('makeOrder_crossSelling_refresh',100,'','fa fa-sync green','fa fa-sync green');
    makeOrder_crossSelling_tb.setItemToolTip('makeOrder_crossSelling_refresh','<?php echo _l('Refresh', 1); ?>');
    makeOrder_crossSelling_tb.addButton('makeOrder_crossSelling_addincart',100,'','fa fa-plus-circle green','fa fa-plus-circle green');
    makeOrder_crossSelling_tb.setItemToolTip('makeOrder_crossSelling_addincart','<?php echo _l('Add to cart', 1); ?>');
    makeOrder_crossSelling_tb.addButton("gotocatalog", 100, "", "fad fa-external-link green", "fad fa-external-link green");
    makeOrder_crossSelling_tb.setItemToolTip('gotocatalog','<?php echo _l('Open the product in Sc catalog'); ?>');

    makeOrder_crossSelling_tb.attachEvent("onClick", function (id) {
        if(id=="makeOrder_crossSelling_refresh")
        {
            displayMOcrossSelling();
        }
        if(id=="makeOrder_crossSelling_addincart")
        {
            var selectedIds = makeOrder_crossSelling_grid.getSelectedRowId();
            if(selectedIds!=undefined && selectedIds!=null && selectedIds!='' && selectedIds!=0)
            {
                addInCart(selectedIds,1,makeOrder_crossSelling_grid);
            }
        }
        if (id=='gotocatalog')
        {
            var selectedIds = makeOrder_crossSelling_grid.getSelectedRowId();
            if(selectedIds!=undefined && selectedIds!=null && selectedIds!='' && selectedIds!=0)
            {
                var path = makeOrder_crossSelling_grid.getUserData(selectedIds, "path_pdt");
                let url = "?page=cat_tree&open_cat_grid="+path;
                window.open(url,'_blank');
            }
        }
    });

    makeOrder_crossSelling_grid = makeOrder_cell_crossSelling.attachGrid();
    makeOrder_crossSelling_grid._name='makeOrder_crossSelling_grid';
    makeOrder_crossSelling_grid.setImagePath("lib/js/imgs/");
    makeOrder_crossSelling_grid.enableDragAndDrop(false);
    makeOrder_crossSelling_grid.enableMultiselect(false);

    function displayMOcrossSelling()
    {
        var selectedIds = makeOrder_searchProduct_grid.getSelectedRowId();
        if(selectedIds!=undefined && selectedIds!=null && selectedIds!='' && selectedIds!=0)
        {
            makeOrder_cell_crossSelling.progressOn();
            makeOrder_crossSelling_grid.clearAll(true);
            $.post("index.php?ajax=1&act=ord_win-makeorder_crossselling_get&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{"id_shop": makeOrder_shop, "id_product":selectedIds},function(data)
            {
                makeOrder_cell_crossSelling.progressOff();
                makeOrder_crossSelling_grid.parse(data);
            });
        }
    }

    /*
    CART
     */
    makeOrder_cell_cart.hideHeader();

    var makeOrder_cart_tb = makeOrder_cell_cart.attachToolbar();
      makeOrder_cart_tb.setIconset('awesome');

    makeOrder_cart_tb.addText("title",1,'<b><?php echo _l('Cart', 1); ?><b/>');
    makeOrder_cart_tb.addButton('makeOrder_cart_refresh',100,'','fa fa-sync green','fa fa-sync green');
    makeOrder_cart_tb.setItemToolTip('makeOrder_cart_refresh','<?php echo _l('Refresh', 1); ?>');
    makeOrder_cart_tb.addButton('makeOrder_cart_selectall',100,'','fa fa-bolt yellow','fa fa-bolt yellow');
    makeOrder_cart_tb.setItemToolTip('makeOrder_cart_selectall','<?php echo _l('Select all', 1); ?>');
    makeOrder_cart_tb.addButton('makeOrder_cart_remove',100,'','fa fa-minus-circle red','fa fa-minus-circle red');
    makeOrder_cart_tb.setItemToolTip('makeOrder_cart_remove','<?php echo _l('Remove selected products from cart', 1); ?>');
    makeOrder_cart_tb.addButton("gotocatalog", 100, "", "fad fa-external-link green", "fad fa-external-link green");
    makeOrder_cart_tb.setItemToolTip('gotocatalog','<?php echo _l('Open the product in Sc catalog'); ?>');

    makeOrder_cart_tb.attachEvent("onClick", function (id) {
        if(id=="makeOrder_cart_refresh")
        {
            displayMOCart();
        }
        if(id=="makeOrder_cart_selectall")
        {
            makeOrder_cart_grid.selectAll();
        }
        if(id=="makeOrder_cart_remove")
        {
            var selectedIds = makeOrder_cart_grid.getSelectedRowId();
            if(selectedIds!=undefined && selectedIds!=null && selectedIds!='' && selectedIds!=0)
            {
                if(makeOrder_selected_customer!=undefined && makeOrder_selected_customer!=null && makeOrder_selected_customer!='' && makeOrder_selected_customer!=0)
                {
                    if(makeOrder_selected_cart!=undefined && makeOrder_selected_cart!=null && makeOrder_selected_cart!='' && makeOrder_selected_cart!=0)
                    {
                        makeOrder_cell_cart.progressOn();
                        $.post("index.php?ajax=1&act=ord_win-makeorder_cart_update&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),
                        {
                            "id_customer": makeOrder_selected_customer,
                            'id_cart': makeOrder_selected_cart,
                            'id_shop': makeOrder_shop,
                            'action': 'remove_product',
                            'id_product': selectedIds
                        },
                        function(data)
                        {
                            makeOrder_cell_cart.progressOff();
                            displayMOCart();
                        });
                    }
                }
            }
        }
        if (id=='gotocatalog')
        {
            var selectedIds = makeOrder_cart_grid.getSelectedRowId();
            if(selectedIds!=undefined && selectedIds!=null && selectedIds!='' && selectedIds!=0)
            {
                var ids = selectedIds.split(",");
                $.each(ids, function(num,rId){
                    var path = makeOrder_cart_grid.getUserData(rId, "path_pdt");
                    let url = "?page=cat_tree&open_cat_grid="+path;
                    window.open(url,'_blank');
                });
            }
        }
    });

    makeOrder_cart_sb=makeOrder_cell_cart.attachStatusBar();

    makeOrder_cart_grid = makeOrder_cell_cart.attachGrid();
    makeOrder_cart_grid._name='makeOrder_cart_grid';
    makeOrder_cart_grid.setImagePath("lib/js/imgs/");
    makeOrder_cart_grid.enableDragAndDrop(false);
    makeOrder_cart_grid.enableMultiselect(true);

    function onEditCell(stage,rId,cInd,nValue,oValue){
        var coltype=makeOrder_cart_grid.getColType(cInd);
        if (stage==1 && this.editor && this.editor.obj && coltype!='txt' && coltype!='txttxt') this.editor.obj.select();

        if(stage==2 && makeOrder_selected_customer!=undefined && makeOrder_selected_customer!=null && makeOrder_selected_customer!='' && makeOrder_selected_customer!=0)
        {
            if(makeOrder_selected_cart!=undefined && makeOrder_selected_cart!=null && makeOrder_selected_cart!='' && makeOrder_selected_cart!=0)
            {
                let idxPriceHt = makeOrder_cart_grid.getColumnId(cInd);
                if(idxPriceHt === 'price') {
                    makeOrder_cell_cart.progressOn();
                    let ids_product = rId.split('_');
                    $.post("<?php echo SC_PS_PATH_ADMIN_REL; ?>index.php?<?php echo version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? 'controller=AdminCarts' : 'tab=AdminCarts'; ?>",
                    {
                        ajax:1,
                        token:"<?php echo $sc_agent->getPSToken('AdminCarts'); ?>",
                        tab: "AdminCarts",
                        action: "updateProductPrice",
                        id_cart: makeOrder_selected_cart,
                        id_product: ids_product[0],
                        id_product_attribute: ids_product[1],
                        id_customer: makeOrder_selected_customer,
                        price: Number(nValue.replace(",",".")).toFixed(4).toString()
                    },
                    function(res)
                    {
                        if (res){
                            dhtmlx.message({text:"<?php echo _l('Price updated'); ?>",type:'success',expire:5000});
                        } else {
                            dhtmlx.message({text:"<?php echo _l('You are disconnected from back office. Please refresh this page to login again.'); ?>",type:'error',expire:10000});
                        }
                        makeOrder_cell_cart.progressOff();
                        displayMOCart();
                    },"json");
                } else {
                    makeOrder_cell_cart.progressOn();
                    $.post("index.php?ajax=1&act=ord_win-makeorder_cart_update&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),
                        {
                            "id_customer": makeOrder_selected_customer,
                            'id_cart': makeOrder_selected_cart,
                            'id_shop': makeOrder_shop,
                            'quantity': nValue,
                            'action': 'update_qty',
                            'id_product': rId
                        },
                        function(error)
                    {
                        makeOrder_cell_cart.progressOff();
                        if (error){
                            dhtmlx.message({text:error,type:'error',expire:5000});
                        }
                        displayMOCart();
                    });
                }
            }
        }

        return true;
    }
    makeOrder_cart_grid.attachEvent("onEditCell",onEditCell);
    // Context menu for Grid
    makeOrder_cart_cmenu=new dhtmlXMenuObject();
    makeOrder_cart_cmenu.renderAsContextMenu();
    makeOrder_cart_cmenu.attachEvent("onClick", function(itemId){
        let selection=makeOrder_cart_grid.getSelectedRowId();
        if (itemId==="reset_price"){
            if(selection !== null) {
                let ok_for_reset = confirm('<?php echo _l('Do you really want to retrieve the default price ?'); ?>');
                if(ok_for_reset){
                    $.post("index.php?ajax=1&act=ord_win-makeorder_cart_update&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),
                        {
                            "id_customer": makeOrder_selected_customer,
                            'id_cart': makeOrder_selected_cart,
                            'id_shop': makeOrder_shop,
                            'action': 'reset_price',
                            'selection_id':selection
                        },
                        function(error)
                        {
                            if (error){
                                dhtmlx.message({text:error,type:'error',expire:5000});
                            }
                            displayMOCart();
                        });
                }
            } else {
                dhtmlx.message({text:'<?php echo _l('Please select a product'); ?>',type:'error',expire:5000});
            }
        }
        if (itemId==="up_price_ht"){
            if(selection !== null) {
                let todo=String(prompt('<?php echo _l('Modify sell price exc. tax, possible values: -10.50%, +5.0, -5.25,...', 1); ?>',''));
                if (todo!=='' && todo!=="null" && todo!==null){
                    if (selection!=='' && selection!=null) {
                        let idxPrice = makeOrder_cart_grid.getColIndexById('price');
                        selection = selection.split(',');
                        let data_price = {};
                        selection.forEach(function(rId){
                            data_price[rId] = makeOrder_cart_grid.cells(rId,idxPrice).getValue();
                        });
                        $.post("index.php?ajax=1&act=ord_win-makeorder_cart_update&id_lang=" + SC_ID_LANG + "&" + new Date().getTime(),
                        {
                            "id_customer": makeOrder_selected_customer,
                            'id_cart': makeOrder_selected_cart,
                            'id_shop': makeOrder_shop,
                            'action': 'convert_price',
                            'todo': todo,
                            'data_price': data_price
                        },
                        function (error) {
                            if (error) {
                                dhtmlx.message({text: error, type: 'error', expire: 5000});
                            }
                            displayMOCart();
                        });
                    }
                }
            } else {
                dhtmlx.message({text:'<?php echo _l('Please select a product'); ?>',type:'error',expire:5000});
            }
        }
    });
    makeOrder_cart_grid.attachEvent("onBeforeContextMenu", function(rowid,colidx) {
        makeOrder_cart_cmenu.showItem('up_price_ht');
        makeOrder_cart_cmenu.showItem('reset_price');
        if(colidx !== makeOrder_cart_grid.getColIndexById('price')){
            makeOrder_cart_cmenu.hideItem('up_price_ht');
            makeOrder_cart_cmenu.hideItem('reset_price');
        }
        return true;
    });
    makeOrder_cart_cmenu.setIconset('awesome');
    let contextMenuXML='<menu absolutePosition="auto" mode="popup" maxItems="8"  globalCss="contextMenu" globalSecondCss="contextMenu" globalTextCss="contextMenuItem">'+
        '<item text="<?php echo _l('Sell price exc. tax').' - '._l('Products'); ?>..." id="up_price_ht"/>'+
        '<item text="<?php echo _l('Reset product price'); ?>" id="reset_price"/>'+
    '</menu>';
    makeOrder_cart_cmenu.loadStruct(contextMenuXML);
    makeOrder_cart_grid.enableContextMenu(makeOrder_cart_cmenu);

    function setFreeShipping(checkbox,id_cart)
    {
        $.post("<?php echo SC_PS_PATH_ADMIN_REL; ?>index.php?<?php echo version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? 'controller=AdminCarts' : 'tab=AdminCarts'; ?>",
        {
            ajax:1,
            token:"<?php echo $sc_agent->getPSToken('AdminCarts'); ?>",
            tab: "AdminCarts",
            action: "updateFreeShipping",
            id_customer: makeOrder_selected_customer,
            id_cart: makeOrder_selected_cart,
            free_shipping:(checkbox.checked===true?1:'')
        },
        function(res)
        {
            if(!res) {
                dhtmlx.message({text:"<?php echo _l('You are disconnected from back office. Please refresh this page to login again.'); ?>",type:'error',expire:10000});
            }
            displayMOcoupons();
            displayMOCart();
        },"json");
    }

    function displayMOCart()
    {
        if(makeOrder_selected_customer!=undefined && makeOrder_selected_customer!=null && makeOrder_selected_customer!='' && makeOrder_selected_customer!=0)
        {
            makeOrder_cell_cart.progressOn();
            makeOrder_cart_grid.clearAll(true);
            $.post("index.php?ajax=1&act=ord_win-makeorder_cart_get&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{"id_customer": makeOrder_selected_customer, 'id_shop': makeOrder_shop},function(data)
            {
                makeOrder_cell_cart.progressOff();
                makeOrder_cart_grid.parse(data);

                let id_cart = makeOrder_cart_grid.getUserData("","id_cart");
                if(id_cart!=undefined && id_cart!=null && id_cart!='' && id_cart!=0)
                {
                    var old = makeOrder_selected_cart;
                    makeOrder_selected_cart = id_cart;
                    if(old!=makeOrder_selected_cart)
                        displayMOaddresses();
                }


                let total_et = makeOrder_cart_grid.getUserData("","total_et");
                let total_it = makeOrder_cart_grid.getUserData("","total_it");
                let is_free_shipping = makeOrder_cart_grid.getUserData("","free_shipping");

                let free_shipping_html = '<span>\n';
                free_shipping_html += '    <input type="checkbox" id="free_shipping" name="free_shipping" onClick="setFreeShipping(this,'+id_cart+');return false;"'+(is_free_shipping==1?' checked':'')+'>\n';
                free_shipping_html += '    <label for="free_shipping" '+(is_free_shipping==1?' style="color:#DC143C;font-weight:bold;"':'')+'><?php echo _l('Free shipping'); ?></label>\n';
                free_shipping_html += '</span>';

                makeOrder_cart_sb.setText('<div style="text-align: right">'+free_shipping_html+'  -  <?php echo _l('Totals:', 1); ?> <span style="margin-left: 20px;">'+total_et+' <?php echo _l('Tax excl.', 1); ?></span> <span style="margin-left: 20px;">'+total_it+' <?php echo _l('Tax incl.', 1); ?></span></div>');

                let all_ids=makeOrder_cart_grid.getAllRowIds();
                if(all_ids !== undefined && all_ids !== '') {
                    all_ids = all_ids.split(',');
                    all_ids.forEach(function (rId) {
                        calculMarginProductMOCart(rId);
                    });
                }
            });
        }
    }

    function calculMarginProductMOCart(rId)
    {
        let marginMatrix_form = makeOrder_cart_grid.getUserData("","marginMatrix_form");
        if(makeOrder_cart_grid.getColIndexById('margin')!==undefined && makeOrder_cart_grid.getColIndexById('wholesale_price')!==undefined)
        {
            let formule = marginMatrix_form;

            let idxPriceIncTaxes=makeOrder_cart_grid.getColIndexById('price_it');
            let idxPriceWithoutTaxes=makeOrder_cart_grid.getColIndexById('price');
            let idxWholeSalePrice=makeOrder_cart_grid.getColIndexById('wholesale_price');
            let idxMargin=makeOrder_cart_grid.getColIndexById('margin');

            let price = makeOrder_cart_grid.cells(rId,idxPriceWithoutTaxes).getValue();
            if(price==null || price==="")
                price = 0;
            formule = formule.replace("{price}",price);

            let price_inc_tax = makeOrder_cart_grid.cells(rId,idxPriceIncTaxes).getValue();
            if(price_inc_tax==null || price_inc_tax==="")
                price_inc_tax = 0;
            formule = formule.replace("{price_inc_tax}",price_inc_tax);

            let wholesale_price = makeOrder_cart_grid.cells(rId,idxWholeSalePrice).getValue();
            if(wholesale_price==null || wholesale_price==="")
                wholesale_price = 0;
            formule = formule.replace("{wholesale_price}",wholesale_price);

            let margin = 0;
            if(wholesale_price>0 && price>0) {
                margin = eval(formule);
            }
            makeOrder_cart_grid.cells(rId,idxMargin).setValue(priceFormat6Dec(margin));

            <?php if (_s('CAT_PROD_GRID_MARGIN_COLOR') != '') { ?>
            if (idxMargin)
            {
                let rules=('<?php echo str_replace("'", '', _s('CAT_PROD_GRID_MARGIN_COLOR')); ?>').split(';');
                for(var i=(rules.length-1) ; i >= 0 ; i--){
                    var rule=rules[i].split(':');
                    if ( Number(makeOrder_cart_grid.cells(rId,idxMargin).getValue()) < Number(rule[0])){
                        makeOrder_cart_grid.cells(rId,idxMargin).setBgColor(rule[1]);
                        makeOrder_cart_grid.cells(rId,idxMargin).setTextColor('#FFFFFF');
                    }
                }
            }
            <?php } ?>
        }
    }

    /*
     ADDRESSES
     */
    makeOrder_cell_addresses.hideHeader();

    var makeOrder_addresses_tb = makeOrder_cell_addresses.attachToolbar();
      makeOrder_addresses_tb.setIconset('awesome');

    makeOrder_addresses_tb.addText("title",1,'<b><?php echo _l('Addresses', 1); ?><b/>');
    makeOrder_addresses_tb.addButton('makeOrder_addresses_refresh',100,'','fa fa-sync green','fa fa-sync green');
    makeOrder_addresses_tb.setItemToolTip('makeOrder_addresses_refresh','<?php echo _l('Refresh', 1); ?>');
    makeOrder_addresses_tb.addButton("makeOrder_addresses_add_form", 100, "", "fa fa-plus-circle green", "fa fa-plus-circle green");
    makeOrder_addresses_tb.setItemToolTip('makeOrder_addresses_add_form','<?php echo _l('Create new address'); ?>');
    makeOrder_addresses_tb.addButton("makeOrder_addresses_add_ps", 100, "", "fa fa-prestashop", "fa fa-prestashop");
    makeOrder_addresses_tb.setItemToolTip('makeOrder_addresses_add_ps','<?php echo _l('Create new address with the PrestaShop form'); ?>');
    makeOrder_addresses_tb.attachEvent("onClick", function (id) {
        if(id=="makeOrder_addresses_refresh")
        {
            displayMOaddresses();
        }
        if(id=="makeOrder_addresses_add_ps")
        {
            if(makeOrder_selected_customer!=undefined && makeOrder_selected_customer!=null && makeOrder_selected_customer!='' && makeOrder_selected_customer!=0)
            {
                if (!dhxWins.isWindow("wNewAddress"))
                {
                    <?php if (version_compare(_PS_VERSION_, '1.7.0.0', '>=')) { ?>
                    wNewAddress = dhxWins.createWindow("wNewAddress", 50, 50, 1460, $(window).height()-75);
                    <?php }
    else
    { ?>
                    wNewAddress = dhxWins.createWindow("wNewAddress", 50, 50, 1000, $(window).height()-75);
                    <?php } ?>
                    wNewAddress.setText('<?php echo _l('Create the new address and close this window to refresh the grid', 1); ?>');
                    wNewAddress.attachURL("<?php echo SC_PS_PATH_ADMIN_REL; ?>index.php?<?php echo version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? 'controller=AdminAddresses' : 'tab=AdminAddresses'; ?>&addaddress=1&token=<?php echo $sc_agent->getPSToken('AdminAddresses'); ?>&id_customer="+makeOrder_selected_customer);

                    wNewAddress.attachEvent("onClose", function(win){
                        displayMOaddresses();
                        return true;
                    });
                }
            }
        }
        if(id=='makeOrder_addresses_add_form')
        {
            if (!dhxWins.isWindow("wCreateNewAddress"))
            {
                wCreateNewAddress = dhxWins.createWindow("wCreateNewAddress", 120, 50, 370, 580);
                wCreateNewAddress.denyPark();
                wCreateNewAddress.denyResize();
                wCreateNewAddress.setText('<?php echo _l('Quick address creation', 1); ?>');
                $.get("index.php?ajax=1&act=ord_win-makeorder_address_create_form&id_lang="+SC_ID_LANG,{id_customer:makeOrder_selected_customer},function(data){
                    $('#jsExecute').html(data);
                });
            }
        }
    });

    makeOrder_addresses_grid = makeOrder_cell_addresses.attachGrid();
    makeOrder_addresses_grid._name='makeOrder_addresses_grid';
    makeOrder_addresses_grid.setImagePath("lib/js/imgs/");
    makeOrder_addresses_grid.enableDragAndDrop(false);
    makeOrder_addresses_grid.enableMultiselect(false);

    makeOrder_addresses_grid.attachEvent("onEditCell",function(stage,rId,cInd,nValue,oValue){
        <?php if (!_r('GRI_ORD_MAKEORDER_ADDRESS_EDITION')) { ?>
        return false;
        <?php } ?>
        let colIdinvoice=makeOrder_addresses_grid.getColIndexById("invoice");
        let colIddelivery=makeOrder_addresses_grid.getColIndexById("delivery");
        var coltype=makeOrder_addresses_grid.getColType(cInd);
        if (stage==1 && this.editor && this.editor.obj && coltype!='txt' && coltype!='txttxt' && rId !==colIdinvoice && rId !== colIddelivery) this.editor.obj.select();

        if(stage==2)
        {
            if(nValue !== oValue) {
                makeOrder_cell_addresses.progressOn();
                $.post("index.php?ajax=1&act=ord_win-makeorder_address_update&"+new Date().getTime(),
                {
                    action:"update",
                    id_lang: SC_ID_LANG,
                    id_customer:makeOrder_selected_customer,
                    id_address: rId,
                    item: makeOrder_addresses_grid.getColumnId(cInd),
                    value: makeOrder_addresses_grid.cells(rId,cInd).getValue(),
                },
                function(data)
                {
                    makeOrder_cell_addresses.progressOff();
                    if (data === 'OK'){
                        dhtmlx.message({text:"<?php echo _l('Address updated', 1); ?>",type:'success',expire:10000});
                    } else {
                        dhtmlx.message({text:data,type:'error',expire:5000});
                    }
                });
            }
        }

        return true;
    });

    makeOrder_addresses_grid.attachEvent("onCheck", function(rId,cInd,state){
        var colIdinvoice=makeOrder_addresses_grid.getColIndexById("invoice");
        var colIddelivery=makeOrder_addresses_grid.getColIndexById("delivery");
        if(cInd==colIddelivery || cInd==colIdinvoice)
        {
            if(makeOrder_selected_customer!=undefined && makeOrder_selected_customer!=null && makeOrder_selected_customer!='' && makeOrder_selected_customer!=0)
            {
                if(makeOrder_selected_cart!=undefined && makeOrder_selected_cart!=null && makeOrder_selected_cart!='' && makeOrder_selected_cart!=0)
                {
                    makeOrder_cell_addresses.progressOn();
                    $.post("index.php?ajax=1&act=ord_win-makeorder_cart_update&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),
                        {
                            "id_customer": makeOrder_selected_customer,
                            'id_cart': makeOrder_selected_cart,
                            'id_shop': makeOrder_shop,
                            'action': 'update_address',
                            'type': (cInd==colIddelivery?'delivery':'invoice'),
                            'id_address': rId
                        },
                        function(data)
                        {
                            makeOrder_cell_addresses.progressOff();
                        });
                }
            }
        }
    });

    function displayMOaddresses()
    {
        if(makeOrder_selected_customer!=undefined && makeOrder_selected_customer!=null && makeOrder_selected_customer!='' && makeOrder_selected_customer!=0)
        {
            if(makeOrder_selected_cart!=undefined && makeOrder_selected_cart!=null && makeOrder_selected_cart!='' && makeOrder_selected_cart!=0)
            {
                makeOrder_cell_addresses.progressOn();
                makeOrder_addresses_grid.clearAll(true);
                $.post("index.php?ajax=1&act=ord_win-makeorder_addresses_get&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{"id_customer": makeOrder_selected_customer, 'id_shop': makeOrder_shop, 'id_cart': makeOrder_selected_cart},function(data)
                {
                    makeOrder_cell_addresses.progressOff();
                    makeOrder_addresses_grid.parse(data);
                });
            }
        }
    }

    /*
     ADDRESS FORM
     */

    /*
     VALIDATION FORM
     */
    makeOrder_cell_validationForm.setWidth(320);
    makeOrder_cell_validationForm.hideHeader();

    function displayMOvalidationForm() {
        if(makeOrder_selected_customer!=undefined && makeOrder_selected_customer!=null && makeOrder_selected_customer!='' && makeOrder_selected_customer!=0) {
            let idxFirstname = makeOrder_customer_grid.getColIndexById('firstname');
            let idxLastname = makeOrder_customer_grid.getColIndexById('lastname');
            let idxEmail = makeOrder_customer_grid.getColIndexById('email');
            let makeOrder_selected_customer_detail = makeOrder_customer_grid.cells(makeOrder_selected_customer, idxFirstname).getValue() + ' ' + makeOrder_customer_grid.cells(makeOrder_selected_customer, idxLastname).getValue();
            let makeOrder_selected_customer_email = makeOrder_customer_grid.cells(makeOrder_selected_customer, idxEmail).getValue();
            let makeOrder_cell_validationForm_structure = [
                {type: "settings", position: "label-left"},
                {
                    type: "button",
                    className: "btn_order_as",
                    name: "order_as",
                    value: "<?php echo '<i class=\"fad fa-walking orange\"></i> '._l('Order as', 1); ?> " + makeOrder_selected_customer_detail
                },
                {
                    type: "button",
                    className: "btn_order_sendmail",
                    name: "order_sendmail",
                    value: "<?php echo '<i class=\"fad fa-paper-plane green\"></i> '._l('Send validation order mail to:', 1); ?><br/>" + makeOrder_selected_customer_email
                }
            ];
            //let wManageFields_properties_form = makeOrder_cell_validationForm.attachForm(makeOrder_cell_validationForm_structure);
            let htmlFormString = '<div id="order_form_container"></div>';
            makeOrder_cell_validationForm.attachHTMLString(htmlFormString);
            let wManageFields_properties_form = new dhtmlXForm("order_form_container",makeOrder_cell_validationForm_structure);
            wManageFields_properties_form.attachEvent("onButtonClick", function (id) {
                if(id=='order_as'){
                    <?php if (!SCMS){?>
                    makeOrder_shop = 0;
                    <?php } ?>
                    connectAsUser("<?php echo SCI::getConfigurationValue('SC_SALT'); ?>","<?php echo $sc_agent->id_employee; ?>",makeOrder_selected_customer,makeOrder_shop);
                }
                if(id=='order_sendmail'){
                    if (confirm("<?php echo _l('Would you really want send a validation order email to', 1); ?> "+makeOrder_selected_customer_email+"?") )
                    {
                        <?php
                            if (version_compare(_PS_VERSION_, '1.7.7.0', '>='))
                            {
                                $sfContainer = PrestaShop\PrestaShop\Adapter\SymfonyContainer::getInstance();
                                $sfRouter = null;
                                $sfRouteParams = array(
                                    'route' => 'admin_orders_send_process_order_email',
                                );
                                if ($sfContainer !== null)
                                {
                                    $sfRouter = $sfContainer->get('router');
                                    $legacyUrlConverter = $sfContainer->get('prestashop.bundle.routing.converter.legacy_url_converter');
                                }

                                if (!empty($sfRouteParams['route']) && $sfRouter !== null)
                                {
                                    $sfRoute = $sfRouteParams['route'];
                                    unset($sfRouteParams['route']);

                                    $send_mail_bo_url = SC_PS_PATH_ADMIN_REL.'index.php'.$sfRouter->generate($sfRoute, $sfRouteParams, 0);
                                } ?>
                                let mail_bo_url_params = {
                                    'cartId': makeOrder_selected_cart
                                }
                        <?php
                            }
                            else
                            {
                                $send_mail_bo_url = SC_PS_PATH_ADMIN_REL.'index.php?'.(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? 'controller=AdminOrders' : 'tab=AdminOrders'); ?>
                                let mail_bo_url_params = {
                                    'ajax':1,
                                    'token':"<?php echo $sc_agent->getPSToken('AdminOrders'); ?>",
                                    'tab': "AdminOrders",
                                    'action': "sendMailValidateOrder",
                                    'id_customer': makeOrder_selected_customer,
                                    'id_cart': makeOrder_selected_cart
                                }
                        <?php
                            }
                        ?>
                        makeOrder_cell_validationForm.progressOn();
                        $.post("<?php echo $send_mail_bo_url; ?>",
                        mail_bo_url_params,
                        function(res)
                        {
                            <?php if (version_compare(_PS_VERSION_, '1.7.7.0', '>=')) { ?>
                                dhtmlx.message({text:res.message,type:'success',expire:5000});
                            <?php }
                        else
                        { ?>
                            if (res.errors){
                                dhtmlx.message({text:res.result,type:'error',expire:10000});
                            } else {
                                dhtmlx.message({text:res.result,type:'success',expire:5000});
                            }
                            <?php } ?>
                            makeOrder_cell_validationForm.progressOff();
                        },"json");
                    }
                }
            });
        }
    }

    /*
    FUNCTIONS
     */
    function addInCart(ids_product,quantity=1,selectedGrid=makeOrder_searchProduct_grid)
    {
        if(makeOrder_selected_customer!=undefined && makeOrder_selected_customer!=null && makeOrder_selected_customer!='' && makeOrder_selected_customer!=0)
        {
            if(makeOrder_selected_cart!=undefined && makeOrder_selected_cart!=null && makeOrder_selected_cart!='' && makeOrder_selected_cart!=0)
            {
                if(ids_product!=undefined && ids_product!=null && ids_product!='' && ids_product!=0)
                {
                    let product_list_id = ids_product.split(',');
                    let inactive_products = false;
                    for (let i = 0; i < product_list_id.length; i++) {
                        let product_id = product_list_id[i];
                        let active = Number(selectedGrid.getUserData(product_id,'active'));

                        if (active === 0) {
                            inactive_products = true;
                            break;
                        }
                    }
                    if(inactive_products === true) {
                        if (!confirm("<?php echo _l('One of the selected products is inactive. Do you want to add it to the cart anyway?', 1); ?>")) {
                            return false;
                        }
                    }

                    makeOrder_cell_cart.progressOn();
                    $.post("index.php?ajax=1&act=ord_win-makeorder_cart_update&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),
                        {
                            "id_customer": makeOrder_selected_customer,
                            'id_cart': makeOrder_selected_cart,
                            'id_shop': makeOrder_shop,
                            'quantity': quantity,
                            'action': 'add_product',
                            'id_product': ids_product
                        },
                        function(error)
                        {
                            makeOrder_cell_cart.progressOff();
                            if (error){
                                dhtmlx.message({text:error,type:'error',expire:5000});
                            }
                            displayMOCart();
                        });
                }
            }
        }
    }
<?php echo '</script>'; ?>
