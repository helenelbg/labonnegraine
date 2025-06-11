    <?php
    $icon = 'fad fa-money-check-edit-alt';
    if (_r('GRI_CAT_PROPERTIES_GRID_SPECIFIC_PRICE')) { ?>
        prop_tb.addListOption('panel', 'specificprices', 6, "button", '<?php echo _l('Specific prices', 1); ?>', "<?php echo $icon; ?>");
        allowed_properties_panel[allowed_properties_panel.length] = "specificprices";
    <?php } ?>

    prop_tb.addButton('specificprice_refresh',1000,'','fa fa-sync green','fa fa-sync green');
    prop_tb.setItemToolTip('specificprice_refresh','<?php echo _l('Refresh grid', 1); ?>');
    prop_tb.addButtonTwoState('specificprice_lightNavigation', 1000, "", "fa fa-mouse-pointer", "fa fa-mouse-pointer");
    prop_tb.setItemToolTip('specificprice_lightNavigation','<?php echo _l('Light navigation (simple click on grid)', 1); ?>');
    prop_tb.addButton('specificprice_add',1000,'','fa fa-plus-circle green','fa fa-plus-circle green');
    prop_tb.setItemToolTip('specificprice_add','<?php echo _l('Create new specific price', 1); ?>');
    prop_tb.addButton('specificprice_del',1000,'','fa fa-minus-circle red','fa fa-minus-circle red');
    prop_tb.setItemToolTip('specificprice_del','<?php echo _l('Delete selected item', 1); ?>');
    prop_tb.addButton("specificprice_selectall",1000, "", "fa fa-bolt yellow", "fad fa-bolt grey");
    prop_tb.setItemToolTip('specificprice_selectall','<?php echo _l('Select all', 1); ?>');
    prop_tb.addButton("call_context_menu",1000, "", "fa fa-euro-sign yellow", "fa fa-euro-sign yellow");
    prop_tb.setItemToolTip('call_context_menu','<?php echo _l('Show context menu to mass edit', 1); ?>');
    prop_tb.addButton("specificprice_export_grid",1000, "", "fad fa-file-csv green", "fad fa-file-csv green");
    prop_tb.setItemToolTip('specificprice_export_grid','<?php echo _l('Export grid to clipboard in CSV format for MSExcel with tab delimiter.', 1); ?>');
    getTbSettingsButton(prop_tb, {'grideditor':1,'settings':0}, 'prop_specificprice_',1000);







    clipboardType_Specificprices = null;    
    needInitSpecificPrices = 1;
    customername = null;
    function initSpecificPrices(){
        if (needInitSpecificPrices)
        {
            prop_tb._specificpricesLayout = dhxLayout.cells('b').attachLayout('1C');
            prop_tb._specificpricesLayout.cells('a').hideHeader();
            prop_tb.base.classList.add('prop_tb');
            dhxLayout.cells('b').showHeader();
            prop_tb._specificpricesGrid = prop_tb._specificpricesLayout.cells('a').attachGrid();
            prop_tb._specificpricesGrid.setImagePath("lib/js/imgs/");
            prop_tb._specificpricesGrid.enableMultiselect(true);
            prop_tb._specificpricesGrid.setDateFormat("%Y-%m-%d %H:%i:%s","%Y-%m-%d %H:%i:%s");
            
            // UISettings
            prop_tb._specificpricesGrid._uisettings_prefix='cat_specificprice';
            prop_tb._specificpricesGrid._uisettings_name=prop_tb._specificpricesGrid._uisettings_prefix;
            prop_tb._specificpricesGrid._uisettings_limited=true;
               prop_tb._specificpricesGrid._first_loading=1;
        
            prop_tb._specificpricesGrid.disableActionAfterRowInserted = false;
               
            // UISettings
            initGridUISettings(prop_tb._specificpricesGrid);
            prop_tb._specificpricesGrid.enableColumnMove(false);
            
            function onEditCellSpecificpricesGrid(stage, rId, cIn,nValue,oValue){
                var checkTypeOfRule = prop_tb._specificpricesGrid.getUserData(rId,'id_specific_price_rule');
                if (checkTypeOfRule > 0){
                    return false;
                }

                <?php if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) { ?>
                    var is_combination = prop_tb._specificpricesGrid.getUserData(rId,"is_combination");
                    if (is_combination=="1")
                        return false;
                <?php } ?>
            
                if(['price_with_reduction_tax_excl','price_with_reduction_tax_incl'].includes(prop_tb._specificpricesGrid.getColumnId(cIn)) && prop_tb._specificpricesGrid.disableActionAfterRowInserted) {
                    return false;
                }
                        
                if (stage==1 && this.editor && this.editor.obj) this.editor.obj.select(); 
                
                
                <?php sc_ext::readCustomPropSpePriceGridConfigXML('onEditCell'); ?>
                if (nValue!=oValue)
                {
                    if(stage==2)
                    {
                    <?php sc_ext::readCustomPropSpePriceGridConfigXML('onBeforeUpdate'); ?>
                        var params = {
                            name: "cat_specificprice_update",
                            row: rId,
                            action: "update",
                            params: {},
                            callback: "callbackSpecificPrice('"+rId+"','update','"+rId+"');"
                        };
                        // CHECK ID_CUSTOMER
                        if (prop_tb._specificpricesGrid.getColumnId(cIn) == "id_customer") {
                            var cellValue = prop_tb._specificpricesGrid.cells(rId,cIn).getValue();
                            var cellValueInt = parseInt(cellValue);
                            if (!Number.isInteger(cellValueInt) && cellValue != 0) {
                                dhtmlx.message({text:'<?php echo _l('This customer in unknown'); ?>',type:'error',expire:3000});
                                return false;
                            }
                        }
                        // CHECK IF REDUCTION VALUE IS CORRECT ( char allowed [. , % space] )
                        let idxReduction = prop_tb._specificpricesGrid.getColIndexById('reduction');
                        let current_reduction = prop_tb._specificpricesGrid.cells(rId,idxReduction).getValue();
                        current_reduction=current_reduction.replace(',','.');
                        current_reduction=current_reduction.split('-').join('');
                        current_reduction=Number(current_reduction.split('%').join(''));
                        if (isNaN(current_reduction)){
                            dhtmlx.message({text:'<?php echo _l('Incorrect value for reduction'); ?>',type:'error',expire:3000});
                            return false;
                        }

                        // COLUMN VALUES
                        params.params[prop_tb._specificpricesGrid.getColumnId(cIn)] = prop_tb._specificpricesGrid.cells(rId,cIn).getValue();
                        // if fix price > -1 force tax excl
                        let idxFixedPrice = prop_tb._specificpricesGrid.getColIndexById('price');
                        let idxReductionTax = prop_tb._specificpricesGrid.getColIndexById('reduction_tax');
                       
        
                        // PRICE WITH REDUCTION
                        let idxCurrentPriceHT = prop_tb._specificpricesGrid.getColIndexById('price_exl_tax');
                        let idxCurrentPriceTTC = prop_tb._specificpricesGrid.getColIndexById('price_inc_tax');
                        switch(prop_tb._specificpricesGrid.getColumnId(cIn)) {
                            case 'price_with_reduction_tax_excl':
                                let current_price_ht = Number(prop_tb._specificpricesGrid.cells(rId,idxCurrentPriceHT).getValue());
                                let reduction_amount_ht = current_price_ht - nValue;
                                params.params['reduction_tax'] = 0;
                                params.params['price'] = '-1';
                                params.params['reduction'] = reduction_amount_ht;
                                prop_tb._specificpricesGrid.cells(rId,idxReduction).setValue(reduction_amount_ht);
                                prop_tb._specificpricesGrid.cells(rId,idxReductionTax).setValue("<?php echo _l('Excl. tax'); ?>");
                                prop_tb._specificpricesGrid.cells(rId,idxFixedPrice).setValue(-1);
                                break;
                            case 'price_with_reduction_tax_incl':
                                let current_price_ttc = Number(prop_tb._specificpricesGrid.cells(rId,idxCurrentPriceTTC).getValue());
                                let reduction_amount_ttc = current_price_ttc - nValue;
                                params.params['reduction_tax'] = 1;
                                params.params['price'] = '-1';
                                params.params['reduction'] = reduction_amount_ttc;
                                prop_tb._specificpricesGrid.cells(rId,idxReduction).setValue(reduction_amount_ttc);
                                prop_tb._specificpricesGrid.cells(rId,idxReductionTax).setValue("<?php echo _l('Incl. tax'); ?>");
                                prop_tb._specificpricesGrid.cells(rId,idxFixedPrice).setValue(-1);
                                break;
                            case 'price':
                                let newFixedPrice = Number(prop_tb._specificpricesGrid.cells(rId,idxFixedPrice).getValue());

                                // activation/desactivation/coloration champs selon prix fixe
                                for(const cId of [prop_tb._specificpricesGrid.getColIndexById('price_with_reduction_tax_excl'),prop_tb._specificpricesGrid.getColIndexById('price_with_reduction_tax_incl')]) {
                                    if(cId !== undefined) {
                                        let current_cell = prop_tb._specificpricesGrid.cells(rId, cId);
                                        if(newFixedPrice > -1) {
                                            current_cell.setBgColor('#D7D7D7');
                                            current_cell.setDisabled(true);
                                        } else {
                                            current_cell.setBgColor('');
                                            current_cell.setDisabled(false);
                                        }
                                    }
                                }

                                if(newFixedPrice > -1) {
                                    prop_tb._specificpricesGrid.setCellExcellType(rId,idxReductionTax,"ro");
                                    prop_tb._specificpricesGrid.cells(rId,idxReductionTax).setValue("<?php echo _l('Excl. tax'); ?>");
                                    params.params['reduction_tax'] = 0;
                                } else {
                                    prop_tb._specificpricesGrid.cells(rId,idxReductionTax).setLabel('');
                                    prop_tb._specificpricesGrid.setCellExcellType(rId,idxReductionTax,"coro");
                                }
                                break;
                        }
        
        
                        // USER DATA
                        
                        params.params = JSON.stringify(params.params);
                        addInUpdateQueue(params,prop_tb._specificpricesGrid);

                        if (customername) {
                            prop_tb._specificpricesGrid.cells(rId,cIn).setValue(customername);
                            customername = null;
                        }
                    }
                }
                
                return true;
            }
            prop_tb._specificpricesGrid.attachEvent("onEditCell", onEditCellSpecificpricesGrid);
            needInitSpecificPrices=0;
            
            prop_tb._specificpricesGrid.attachEvent("onDhxCalendarCreated",function(calendar){
                calendar.setSensitiveRange("2012-01-01",null);
                dhtmlXCalendarObject.prototype.langData["<?php echo $user_lang_iso; ?>"] = lang_calendar;
                 calendar.loadUserLanguage("<?php echo $user_lang_iso; ?>");
            });
            
            // Context menu for grid
            specificprices_cmenu=new dhtmlXMenuObject();
            specificprices_cmenu.renderAsContextMenu();
            function onGridSpecificpricesContextButtonClick(itemId){
                if (itemId=="copy" || itemId=="paste") {
                    tabId = prop_tb._specificpricesGrid.contextID.split('_');
                    tabId = tabId[0];
                }
                if (itemId=="copy"){
                    if (lastColumnRightClicked_Specificprices!=0)
                    {
                        clipboardValue_Specificprices=prop_tb._specificpricesGrid.cells(tabId,lastColumnRightClicked_Specificprices).getValue();
                        if(lastColumnRightClicked_Specificprices == prop_tb._specificpricesGrid.getColIndexById('id_customer')) {
                            var mask = prop_tb._specificpricesGrid.cells(tabId,lastColumnRightClicked_Specificprices).getValue();
                            $.post('index.php?ajax=1&act=cat_specificprice_customer_get&ajaxCall=1&getIdCus=1',{'mask':mask},function(data)            {
                                var res = JSON.parse(data);
                                clipboardValue_Specificprices=parseInt(res.id_customer);
                                customername = res.name;
                            });
                        } else {
                            clipboardValue_Specificprices=prop_tb._specificpricesGrid.cells(tabId,lastColumnRightClicked_Specificprices).getValue();
                        }
                        specificprices_cmenu.setItemText('paste' , '<?php echo _l('Paste'); ?> '+prop_tb._specificpricesGrid.cells(tabId,lastColumnRightClicked_Specificprices).getTitle());
                        clipboardType_Specificprices=lastColumnRightClicked_Specificprices;
                    }
                }
                if (itemId=="paste"){
                    if (lastColumnRightClicked_Specificprices!=0 && clipboardValue_Specificprices!=null && clipboardType_Specificprices==lastColumnRightClicked_Specificprices)
                    {
                        selection=prop_tb._specificpricesGrid.getSelectedRowId();
                        if (selection!='' && selection!=null)
                        {
                            selArray=selection.split(',');
                            for(i=0 ; i < selArray.length ; i++)
                            {
                                if (prop_tb._specificpricesGrid.getColumnId(lastColumnRightClicked_Specificprices).substr(0,5)!='attr_')
                                {
                                    <?php if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) { ?>
                                    var is_combination = prop_tb._specificpricesGrid.getUserData(selArray[i],"is_combination");
                                    if (is_combination=="0")
                                    {
                                    <?php } ?>
                                    prop_tb._specificpricesGrid.cells(selArray[i],lastColumnRightClicked_Specificprices).setValue(clipboardValue_Specificprices);
                                    onEditCellSpecificpricesGrid(2,selArray[i],lastColumnRightClicked_Specificprices,clipboardValue_Specificprices,null);
                                    <?php if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) { ?>
                                    }
                                    <?php } ?>
                                }
                            }
                        }
                    }
                }
                if (itemId=="massupdate_specificprices"){
                    todo=prompt('<?php echo _l('Modify fixed specific prices, ex : +5.0, -5.25,...', 1); ?>','');
                    if (todo!='' && todo!=null){
                        selection=prop_tb._specificpricesGrid.getSelectedRowId();
                        if (selection!='' && selection!=null){
                            var params = {"field": "edit_specificprices", "todo": todo};
                            propSpecificPricesMassUpdateInQueue(selection, params);
                        }
                    }
                }

            }
            specificprices_cmenu.attachEvent("onClick", onGridSpecificpricesContextButtonClick);
            var contextMenuXML='<menu absolutePosition="auto" mode="popup" maxItems="8"  globalCss="contextMenu" globalSecondCss="contextMenu" globalTextCss="contextMenuItem">'+
                    '<item text="Object" id="object" enabled="false"/>'+
                    '<item text="<?php echo _l('Copy'); ?>" id="copy"/>'+
                    '<item text="<?php echo _l('Paste'); ?>" id="paste"/>'+
                    '<item text="<?php echo _l('Mass update'); ?>" id="massupdate">'+
                        '<item text="<?php echo _l('Specific prices',1).': '._l('Fixed prices', 1); ?>..." id="massupdate_specificprices"/>'+
                '</item>'+
                '</menu>';
            specificprices_cmenu.loadStruct(contextMenuXML);
            prop_tb._specificpricesGrid.enableContextMenu(specificprices_cmenu);

            prop_tb._specificpricesGrid.attachEvent("onBeforeContextMenu", function(rowid,colidx,grid){
                var checkTypeOfRule = prop_tb._specificpricesGrid.getUserData(rowid,'id_specific_price_rule');
                if (checkTypeOfRule > 0) {
                    var disableOnCols=new Array(
                        prop_tb._specificpricesGrid.getColIndexById('id_product'),
                        prop_tb._specificpricesGrid.getColIndexById('id_specific_price'),
                        prop_tb._specificpricesGrid.getColIndexById('id_product_attribute'),
                        prop_tb._specificpricesGrid.getColIndexById('reference'),
                        prop_tb._specificpricesGrid.getColIndexById('name'),
                        prop_tb._specificpricesGrid.getColIndexById('id_shop'),
                        prop_tb._specificpricesGrid.getColIndexById('id_shop_group'),
                        prop_tb._specificpricesGrid.getColIndexById('id_group'),
                        prop_tb._specificpricesGrid.getColIndexById('from_quantity'),
                        prop_tb._specificpricesGrid.getColIndexById('price'),
                        prop_tb._specificpricesGrid.getColIndexById('reduction'),
                        prop_tb._specificpricesGrid.getColIndexById('reduction_tax'),
                        prop_tb._specificpricesGrid.getColIndexById('from'),
                        prop_tb._specificpricesGrid.getColIndexById('to'),
                        prop_tb._specificpricesGrid.getColIndexById('id_country'),
                        prop_tb._specificpricesGrid.getColIndexById('id_currency'),
                        prop_tb._specificpricesGrid.getColIndexById('image'),
                        prop_tb._specificpricesGrid.getColIndexById('supplier_reference'),
                        prop_tb._specificpricesGrid.getColIndexById('ean13'),
                        prop_tb._specificpricesGrid.getColIndexById('upc'),
                        prop_tb._specificpricesGrid.getColIndexById('active'),
                        prop_tb._specificpricesGrid.getColIndexById('price_exl_tax'),
                        prop_tb._specificpricesGrid.getColIndexById('price_inc_tax'),
                        prop_tb._specificpricesGrid.getColIndexById('id_manufacturer'),
                        prop_tb._specificpricesGrid.getColIndexById('id_supplier'),
                        prop_tb._specificpricesGrid.getColIndexById('id_specific_price_rule')
                    );
                } else {
                    var disableOnCols=new Array(
                        prop_tb._specificpricesGrid.getColIndexById('id_product'),
                        prop_tb._specificpricesGrid.getColIndexById('id_specific_price'),
                        prop_tb._specificpricesGrid.getColIndexById('id_specific_price_rule')
                    );
                }
                if (in_array(colidx,disableOnCols))
                {
                    return false;
                }
                lastColumnRightClicked_Specificprices=colidx;
                specificprices_cmenu.setItemText('object', '<?php echo _l('Specific price:'); ?> '+prop_tb._specificpricesGrid.cells(rowid,prop_tb._specificpricesGrid.getColIndexById('id_specific_price')).getTitle());
                if (lastColumnRightClicked_Specificprices==clipboardType_Specificprices)
                {
                    specificprices_cmenu.setItemEnabled('paste');
                }else{
                    specificprices_cmenu.setItemDisabled('paste');
                }
                return true;
            });
        }
    }




    function setPropertiesPanel_discounts(id){
        if (id=='specificprices')
        {
            if(lastProductSelID!=undefined && lastProductSelID!="")
            {
                dhxLayout.cells('b').setText('<?php echo _l('Properties', 1).' '._l('of', 1); ?> '+getSelectedItemValueOrID(cat_grid,lastProductSelID,'name'));
            }
            hidePropTBButtons();
            prop_tb.showItem('specificprice_del');
            prop_tb.showItem('specificprice_add');
            prop_tb.showItem('specificprice_refresh');
            prop_tb.showItem('specificprice_lightNavigation');
            prop_tb.showItem('specificprice_selectall');
            prop_tb.showItem('call_context_menu');
            prop_tb.showItem('specificprice_export_grid');
            prop_tb.showItem('prop_specificprice_settings_menu');
            prop_tb.setItemText('panel', '<?php echo _l('Specific prices', 1); ?>');
            prop_tb.setItemImage('panel', '<?php echo $icon; ?>');
            needInitSpecificPrices = 1;
            initSpecificPrices();
            propertiesPanel='specificprices';
            if (lastProductSelID!=0)
                displaySpecificPrices();
        }
        if(id=='call_context_menu'){
            let tb_context_button = $('.prop_tb div.dhx_toolbar_btn > i.fa.fa-euro-sign.yellow');
            let icon_off_top = parseInt(tb_context_button.offset().top+24);
            let icon_off_left = parseInt(tb_context_button.offset().left);
            selection=prop_tb._specificpricesGrid.getSelectedRowId();
            if(selection !== 'null' && selection !== '' && selection !== null) {
                using_tb_context_menu = 1;
                window.setTimeout(function () {
                    specificprices_cmenu.hideItem('object');
                    specificprices_cmenu.hideItem('copy');
                    specificprices_cmenu.hideItem('paste');
                    specificprices_cmenu.showContextMenu(icon_off_left, icon_off_top);
                }, 1);
            } else {
                alert('<?php echo _l('Please select at specific prices', 1); ?>');
            }
        }
        if (id=='specificprice_refresh')
        {
            if (lastProductSelID!=0)
                displaySpecificPrices();
        }
        if (id=='prop_specificprice_grideditor'){
            openWinGridEditor('type_propspeprice');
        }
        if (id=='specificprice_selectall')
        {
            prop_tb._specificpricesGrid.selectAll();
        }
        if (id=='specificprice_add')
        {
            if (lastProductSelID==0){
                alert('<?php echo _l('Please select a product', 1); ?>');
            }else{
                var newId = new Date().getTime();
                var maxQuantity=1;
                var maxValue=10;
                var percent='';
                
                
                // INSERT
                    <?php $sourceGridFormat = SCI::getGridViews('propspeprice');
                    $sql_gridFormat = $sourceGridFormat;
                    sc_ext::readCustomPropSpePriceGridConfigXML('gridConfig');
                    $gridFormat = $sourceGridFormat;
                    $cols = explode(',', $gridFormat);

                    $insert = '';
                    foreach ($cols as $col)
                    {
                        $default = "''";
                        if ($col == 'id_specific_price')
                        {
                            $default = 'newId';
                        }
                        elseif ($col == 'id_product')
                        {
                            $default = 'cat_grid.getSelectedRowId()';
                        }
                        elseif ($col == 'id_product_attribute')
                        {
                            $default = '0';
                        }
                        elseif ($col == 'id_shop')
                        {
                            $default = '0';
                        }
                        elseif ($col == 'id_shop_group')
                        {
                            $default = '0';
                        }
                        elseif ($col == 'id_group')
                        {
                            $default = '0';
                        }
                        elseif ($col == 'from_quantity')
                        {
                            $default = '1';
                        }
                        elseif ($col == 'price')
                        {
                            $default = (version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? -1 : 0);
                        }
                        elseif ($col == 'reduction_tax')
                        {
                            $default = (version_compare(_PS_VERSION_, '1.6.0.11', '>=') ? "'"._s('CAT_PROD_SPECIFIC_PRICES_DEFAULT_TAX')."'" : "'"._l('Incl. tax')."'");
                        }
                        elseif ($col == 'id_country')
                        {
                            $default = '0';
                        }
                        elseif ($col == 'id_currency')
                        {
                            $default = '0';
                        }

                        if (!empty($insert))
                        {
                            $insert .= ',';
                        }
                        $insert .= $default;
                    }
                    ?>
                    newRow=new Array(<?php echo $insert; ?>);
                    prop_tb._specificpricesGrid.addRow(newId,newRow);
                    prop_tb._specificpricesGrid.setRowHidden(newId, true);
                
                    var params = {
                        name: "cat_specificprice_update",
                        row: newId,
                        action: "insert",
                        params: {callback: "callbackSpecificPrice('"+newId+"','insert','{newid}');"}
                    };
                    // COLUMN VALUES
                    prop_tb._specificpricesGrid.forEachCell(newId,function(cellObj,ind){
                        params.params[prop_tb._specificpricesGrid.getColumnId(ind)] = prop_tb._specificpricesGrid.cells(newId,ind).getValue();
                    });
                    // USER DATA

                    sendInsert(params,prop_tb._specificpricesLayout.cells('a'));

            }
        }
        if (id=='specificprice_del')
        {
            if (prop_tb._specificpricesGrid.getSelectedRowId()==null)
            {
                alert('<?php echo _l('Please select an item', 1); ?>');
            }else{
                if (lastProductSelID!=0)
                {
                    if (confirm('<?php echo _l('Are you sure you want to delete the selected items?', 1); ?>'))
                    {
                        selection=prop_tb._specificpricesGrid.getSelectedRowId();
                            
                        ids=selection.split(',');
                        $.each(ids, function(num, rId){
                            var params = {
                                name: "cat_specificprice_update",
                                row: rId,
                                action: "delete",
                                params: {},
                                callback: "callbackSpecificPrice('"+rId+"','delete','"+rId+"');"
                            };                    
                            params.params = JSON.stringify(params.params);
                            addInUpdateQueue(params,prop_tb._specificpricesGrid);
                        });
                    }
                }else{
                    alert('<?php echo _l('Please select a product', 1); ?>');
                }
            }
        }
        if (id=='specificprice_export_grid')
        {
            displayQuickExportWindow(prop_tb._specificpricesGrid, 1);
        }
    }
    prop_tb.attachEvent("onClick", setPropertiesPanel_discounts);

    prop_tb.attachEvent("onStateChange",function(id,state){
        if (id=='specificprice_lightNavigation')
        {
            if (state)
            {
                prop_tb._specificpricesGrid.enableLightMouseNavigation(true);
            }else{
                prop_tb._specificpricesGrid.enableLightMouseNavigation(false);
            }
        }
    });    

    
    function displaySpecificPrices()
    {
        prop_tb._specificpricesGrid.disableActionAfterRowInserted=false;
        prop_tb._specificpricesGrid.clearAll(true);
        $.post("index.php?ajax=1&act=cat_specificprice_get&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{'id_product': cat_grid.getSelectedRowId()},function(data)
        {
            prop_tb._specificpricesGrid.parse(data);
            nb=prop_tb._specificpricesGrid.getRowsNum();
            prop_tb._sb.setText(nb+(nb>1?" <?php echo _l('specific prices'); ?>":" <?php echo _l('specific price'); ?>"));

            let disabled_fields_by_id_speprice = prop_tb._specificpricesGrid.getUserData('','disabled_fields_by_id_speprice');
            if(disabled_fields_by_id_speprice !== '') {
                for(const rId of disabled_fields_by_id_speprice.split(',')) {
                    for(const cId of [prop_tb._specificpricesGrid.getColIndexById('price_with_reduction_tax_excl'),prop_tb._specificpricesGrid.getColIndexById('price_with_reduction_tax_incl')]) {
                        if(cId !== undefined) {
                            let current_cell = prop_tb._specificpricesGrid.cells(rId, cId);
                            current_cell.setBgColor('#D7D7D7');
                            current_cell.setDisabled(true);
                        }
                    }
                }
            }
            
           // UISettings
            loadGridUISettings(prop_tb._specificpricesGrid);
            prop_tb._specificpricesGrid._first_loading=0;
            
             <?php sc_ext::readCustomPropSpePriceGridConfigXML('afterGetRows'); ?>
        });
    }


    let specificprices_current_id = 0;
    cat_grid.attachEvent("onRowSelect",function (idproduct){
        if (propertiesPanel=='specificprices' && (cat_grid.getSelectedRowId()!==null && specificprices_current_id!=idproduct)){
            displaySpecificPrices();
            specificprices_current_id=idproduct;
        }
    });

    function propSpecificPricesMassUpdateInQueue(selection, params)
    {
        var ids = selection.split(',');
        $.each(ids, function(num, pId){
            var vars = params;
            addPropSpecificPricesMassUpdateInQueue(pId, "update", null, vars);
        });
    }
    function addPropSpecificPricesMassUpdateInQueue(rId, action, cIn, vars)
    {
        var params = {
            name: "cat_specificprice_update_queue",
            rowId: rId,
            action: action,
            params: {},
            callback: "displaySpecificPrices();"
        };
        // COLUMN VALUES
        params.params["id_lang"] = SC_ID_LANG;
        if(vars!=undefined && vars!=null && vars!="" && vars!=0)
        {
            $.each(vars, function(key, value){
                params.params[key] = value;
            });
        }

        params.params = JSON.stringify(params.params);
        addInUpdateQueue(params,cat_grid);
    }
    // CALLBACK FUNCTION
    function callbackSpecificPrice(sid,action,tid)
    {
        <?php sc_ext::readCustomPropSpePriceGridConfigXML('onAfterUpdate'); ?>
        if (action=='insert')
        {
            idxSpeID=prop_tb._specificpricesGrid.getColIndexById('id_specific_price');
            prop_tb._specificpricesGrid.cells(sid,idxSpeID).setValue(tid);
            prop_tb._specificpricesGrid.changeRowId(sid,tid);
            prop_tb._specificpricesGrid.setRowHidden(tid, false);
            prop_tb._specificpricesGrid.showRow(tid);
            prop_tb._specificpricesLayout.cells('a').progressOff();
            prop_tb._specificpricesGrid.disableActionAfterRowInserted=true;
        } else if (action=='update') {
            prop_tb._specificpricesGrid.setRowTextNormal(sid);
        } else if(action=='delete') {
            prop_tb._specificpricesGrid.deleteRow(sid);
        }
    }
