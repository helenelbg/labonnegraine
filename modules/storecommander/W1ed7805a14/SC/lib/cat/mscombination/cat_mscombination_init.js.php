<?php if (SCMS) { ?>
    <?php if (_r('GRI_CAT_PROPERTIES_GRID_MB_COMBI')) { ?>
        prop_tb.addListOption('panel', 'mscombination', 13, "button", '<?php echo _l('Multistore : combinations', 1); ?>', "fa fa-layer-group");
        allowed_properties_panel[allowed_properties_panel.length] = "mscombination";
    <?php } ?>
    prop_tb.addButton("mscombination_refresh",1000, "", "fa fa-sync green", "fa fa-sync green");
    prop_tb.setItemToolTip('mscombination_refresh','<?php echo _l('Refresh grid', 1); ?>');
    prop_tb.addButton("mscombination_selectall",1000, "", "fa fa-bolt yellow", "fad fa-bolt grey");
    prop_tb.setItemToolTip('mscombination_selectall','<?php echo _l('Select all combinations', 1); ?>');
    <?php if(version_compare(_PS_VERSION_, '1.7.2.0', '>=')){ ?>
        prop_tb.addButton("mscombination_refresh_physical_stocks",1000, "", "fad fa-retweet-alt green", "fad fa-retweet-alt green");
        prop_tb.setItemToolTip('mscombination_refresh_physical_stocks','<?php echo _l('Refresh stocks'); ?>');
    <?php } ?>
    prop_tb.addButton("mscombination_exportcsv",1000, "", "fad fa-file-csv green", "fad fa-file-csv green");
    prop_tb.setItemToolTip('mscombination_exportcsv','<?php echo _l('Export grid to clipboard in CSV format for MSExcel with tab delimiter.', 1); ?>');
    getTbSettingsButton(prop_tb, {'grideditor':1,'settings':0}, 'prop_mscombination_',1000);
    var marginMatrix_form = "";
    function calculMarginMscombination(rId)
    {
        if(prop_tb._mscombinationGrid.getColIndexById('margin')!=undefined && prop_tb._mscombinationGrid.getColIndexById('wholesale_price')!=undefined)
        {
            var formule = marginMatrix_form;
            
            idxPriceIncTaxes=prop_tb._mscombinationGrid.getColIndexById('price');
            idxPriceWithoutTaxes=prop_tb._mscombinationGrid.getColIndexById('priceextax');
            idxWholeSalePrice=prop_tb._mscombinationGrid.getColIndexById('wholesale_price');
            
            idxMargin=prop_tb._mscombinationGrid.getColIndexById('margin');
            
            var price = prop_tb._mscombinationGrid.cells(rId,idxPriceWithoutTaxes).getValue();
            if(price==null || price=="")
                price = 0;
            formule = formule.replace("{price}",price)
                            .replace("{price}",price)
                            .replace("{price}",price);
                        
            var price_inc_tax = prop_tb._mscombinationGrid.cells(rId,idxPriceIncTaxes).getValue();
            if(price_inc_tax==null || price_inc_tax=="")
                price_inc_tax = 0;
            formule = formule.replace("{price_inc_tax}",price_inc_tax)
                            .replace("{price_inc_tax}",price_inc_tax)
                            .replace("{price_inc_tax}",price_inc_tax);
                            
            var wholesale_price = prop_tb._mscombinationGrid.cells(rId,idxWholeSalePrice).getValue();
            if(wholesale_price==null || wholesale_price=="")
                wholesale_price = 0;
            formule = formule.replace("{wholesale_price}",wholesale_price)
                            .replace("{wholesale_price}",wholesale_price)
                            .replace("{wholesale_price}",wholesale_price);
                            
            if(wholesale_price>0 && price>0)
                var margin = eval(formule);
            else
                var margin = 0;
            prop_tb._mscombinationGrid.cells(rId,idxMargin).setValue(priceFormat(margin));
    
            <?php if (_s('CAT_PROD_GRID_MARGIN_COLOR') != '') { ?>
            if (idxMargin)
            {
                var rules=('<?php echo str_replace("'", '', _s('CAT_PROD_GRID_MARGIN_COLOR')); ?>').split(';');
                for(var i=(rules.length-1) ; i >= 0 ; i--){
                    var rule=rules[i].split(':');
                    if ( Number(prop_tb._mscombinationGrid.cells(rId,idxMargin).getValue()) < Number(rule[0])){
                        prop_tb._mscombinationGrid.cells(rId,idxMargin).setBgColor(rule[1]);
                        prop_tb._mscombinationGrid.cells(rId,idxMargin).setTextColor('#FFFFFF');
                    }
                }
            }
            <?php } ?>
        }
    }
    
    function onEditCellMscombination(stage,rId,cInd,nValue,oValue)
    {
        idxShop=prop_tb._mscombinationGrid.getColIndexById('id_shop');
        if(cInd==idxShop)
            return false;
    
        if (stage==2)
        {
        
            
            nValue = prop_tb._mscombinationGrid.cells(rId,cInd).getValue();
        
        
            idxRef=prop_tb._mscombinationGrid.getColIndexById('reference');
            if (cInd == idxRef)
            {
                 var splitted = rId.split("_");
                var product_id = splitted[0]+"_"+splitted[1];
                if(product_id!=null && product_id!=undefined)
                {
                    prop_tb._mscombinationGrid.forEachRow(function(id){
                        var temp_id = "_"+id;
                        if(temp_id.search("_"+product_id+"_")>=0)
                        {
                            prop_tb._mscombinationGrid.cells(id,idxRef).setValue(nValue);
                        }
                   });
                }
            }
        
            idxRefSupplier=prop_tb._mscombinationGrid.getColIndexById('supplier_reference');
            if (cInd == idxRefSupplier)
            {
                 var splitted = rId.split("_");
                var product_id = splitted[0]+"_"+splitted[1];
                if(product_id!=null && product_id!=undefined)
                {
                    prop_tb._mscombinationGrid.forEachRow(function(id){
                        var temp_id = "_"+id;
                        if(temp_id.search("_"+product_id+"_")>=0)
                        {
                            prop_tb._mscombinationGrid.cells(id,idxRefSupplier).setValue(nValue);
                        }
                   });
                }
            }
        
            idxPrice=prop_tb._mscombinationGrid.getColIndexById('price');
            idxPriceExTax=prop_tb._mscombinationGrid.getColIndexById('priceextax');
            idxEcotax=prop_tb._mscombinationGrid.getColIndexById('ecotax');
            idxTaxrate=prop_tb._mscombinationGrid.getColIndexById('taxrate');
            idxWholeSalePrice=prop_tb._mscombinationGrid.getColIndexById('wholesale_price');
            
            if (cInd == idxEcotax){ //ecotax
                var idsplit = rId.split("_");
                var productId = idsplit[0];
            
                var tax=prop_tb._mscombinationGrid.cells(rId,idxTaxrate).getValue()/100+1;
                var eco=noComma(nValue);
                prop_tb._mscombinationGrid.cells(rId,idxEcotax).setValue(priceFormat6Dec(nValue));
                prop_tb._mscombinationGrid.cells(rId,idxPriceExTax).setValue(priceFormat6Dec((prop_tb._mscombinationGrid.cells(rId,idxPrice).getValue() - eco)/tax));
                calculMarginMscombination(rId);
            }
            if (cInd == idxPriceExTax){ //priceExTax update
                var tax=prop_tb._mscombinationGrid.cells(rId,idxTaxrate).getValue()/100+1;
                var eco=0;
                if (idxEcotax)
                    eco=prop_tb._mscombinationGrid.cells(rId,idxEcotax).getValue()*1;
                prop_tb._mscombinationGrid.cells(rId,idxPriceExTax).setValue(priceFormat6Dec(nValue));
                prop_tb._mscombinationGrid.cells(rId,idxPrice).setValue(priceFormat6Dec(nValue*tax + eco));
                calculMarginMscombination(rId);
            }
            if (cInd == idxWholeSalePrice){ //Wholesale price
                calculMarginMscombination(rId);
            }
            if (cInd == idxPrice){ //price update
                var tax=prop_tb._mscombinationGrid.cells(rId,idxTaxrate).getValue()/100+1;
                var eco=0;
                if (idxEcotax)
                    eco=prop_tb._mscombinationGrid.cells(rId,idxEcotax).getValue()*1;
                prop_tb._mscombinationGrid.cells(rId,idxPrice).setValue(priceFormat6Dec(nValue));
                prop_tb._mscombinationGrid.cells(rId,idxPriceExTax).setValue(priceFormat6Dec((nValue - eco)/tax));
                cInd = idxPriceExTax;
                calculMarginMscombination(rId);
            }
            <?php sc_ext::readCustomMsCombinationGridConfigXML('onEditCell'); ?>
            if(nValue!=oValue)
            {
                <?php sc_ext::readCustomMsCombinationGridConfigXML('onBeforeUpdate'); ?>
                addMsCombinationInQueue(rId, "update", cInd);
            }
        }
        return true;
    }
    
    clipboardType_Mscombination = null;
    needInitMscombination = 1;
    function initMscombination()
    {
        if (needInitMscombination)
        {
            prop_tb._mscombinationLayout = dhxLayout.cells('b').attachLayout('1C');
            prop_tb._mscombinationLayout.cells('a').hideHeader();
            dhxLayout.cells('b').showHeader();

            prop_tb._mscombinationGrid = prop_tb._mscombinationLayout.cells('a').attachGrid();
            prop_tb._mscombinationGrid._name='_mscombinationGrid';
            prop_tb._mscombinationGrid.setImagePath("lib/js/imgs/");
              prop_tb._mscombinationGrid.enableDragAndDrop(false);
            prop_tb._mscombinationGrid.enableMultiselect(true);
            
            // UISettings
            prop_tb._mscombinationGrid._uisettings_prefix='cat_mscombination';
            prop_tb._mscombinationGrid._uisettings_name=prop_tb._mscombinationGrid._uisettings_prefix;
            prop_tb._mscombinationGrid._uisettings_limited=true;
               prop_tb._mscombinationGrid._first_loading=1;
               
            // UISettings
            initGridUISettings(prop_tb._mscombinationGrid);

            prop_tb._mscombinationGrid.attachEvent("onEditCell", onEditCellMscombination);
            
            prop_tb._mscombinationGrid.attachEvent("onScroll",function(){
                marginMatrix_form = prop_tb._mscombinationGrid.getUserData("", "marginMatrix_form");
                   prop_tb._mscombinationGrid.forEachRow(function(id){
                  calculMarginMscombination(id);
               });
            });
            
            prop_tb._mscombinationGrid.attachEvent("onDhxCalendarCreated",function(calendar){
                calendar.hideTime();
                calendar.setSensitiveRange("2012-01-01",null);
                
                dhtmlXCalendarObject.prototype.langData["<?php echo $user_lang_iso; ?>"] = lang_calendar;
                calendar.loadUserLanguage("<?php echo $user_lang_iso; ?>");
            });
            
            
            // Context menu for MultiShops Info Product grid
            mscombination_cmenu=new dhtmlXMenuObject();
            mscombination_cmenu.renderAsContextMenu();
            function onGridMscombinationContextButtonClick(itemId){
                tabId=prop_tb._mscombinationGrid.contextID.split('_');
                tabId=tabId[0]+"_"+tabId[1]+"_"+tabId[2];
                if (itemId=="copy"){
                    if (lastColumnRightClicked_Mscombination!=0)
                    {
                        clipboardValue_Mscombination=prop_tb._mscombinationGrid.cells(tabId,lastColumnRightClicked_Mscombination).getValue();
                        mscombination_cmenu.setItemText('paste' , '<?php echo _l('Paste'); ?> '+prop_tb._mscombinationGrid.cells(tabId,lastColumnRightClicked_Mscombination).getTitle());
                        clipboardType_Mscombination=lastColumnRightClicked_Mscombination;
                    }
                }
                if (itemId=="paste"){
                    if (lastColumnRightClicked_Mscombination!=0 && clipboardValue_Mscombination!=null && clipboardType_Mscombination==lastColumnRightClicked_Mscombination)
                    {
                        selection=prop_tb._mscombinationGrid.getSelectedRowId();
                        if (selection!='' && selection!=null)
                        {
                            selArray=selection.split(',');
                            for(i=0 ; i < selArray.length ; i++)
                            {
                                if (prop_tb._mscombinationGrid.getColumnId(lastColumnRightClicked_Mscombination).substr(0,5)!='attr_')
                                {
                                    prop_tb._mscombinationGrid.cells(selArray[i],lastColumnRightClicked_Mscombination).setValue(clipboardValue_Mscombination);
                                    prop_tb._mscombinationGrid.cells(selArray[i],lastColumnRightClicked_Mscombination).cell.wasChanged=true;
                                    onEditCellMscombination(2,selArray[i],lastColumnRightClicked_Mscombination);
                                    //groupsDataProcessor_mscombination.setUpdated(selArray[i],true,"updated");
                                }
                            }
                        }
                    }
                }
            }
            mscombination_cmenu.attachEvent("onClick", onGridMscombinationContextButtonClick);
            var contextMenuXML='<menu absolutePosition="auto" mode="popup" maxItems="8"  globalCss="contextMenu" globalSecondCss="contextMenu" globalTextCss="contextMenuItem">'+
                    '<item text="Object" id="object" enabled="false"/>'+
                    '<item text="Attribute" id="attribute" enabled="false"/>'+
                    '<item text="Shop" id="shop" enabled="false"/>'+
                    '<item text="<?php echo _l('Copy'); ?>" id="copy"/>'+
                    '<item text="<?php echo _l('Paste'); ?>" id="paste"/>'+
                '</menu>';
            mscombination_cmenu.loadStruct(contextMenuXML);
            prop_tb._mscombinationGrid.enableContextMenu(mscombination_cmenu);

            prop_tb._mscombinationGrid.attachEvent("onBeforeContextMenu", function(rowid,colidx,grid){
                var disableOnCols=new Array(
                        prop_tb._mscombinationGrid.getColIndexById('id_product'),
                        prop_tb._mscombinationGrid.getColIndexById('id_product_attribute'),
                        prop_tb._mscombinationGrid.getColIndexById('id_shop'),
                        prop_tb._mscombinationGrid.getColIndexById('name'),
                        prop_tb._mscombinationGrid.getColIndexById('pprice'),
                        prop_tb._mscombinationGrid.getColIndexById('ppriceextax'),
                        prop_tb._mscombinationGrid.getColIndexById('margin')
                        );
                if (in_array(colidx,disableOnCols))
                {
                    return false;
                }
                lastColumnRightClicked_Mscombination=colidx;
                mscombination_cmenu.setItemText('object', '<?php echo _l('Product:'); ?> '+prop_tb._mscombinationGrid.cells(rowid,prop_tb._mscombinationGrid.getColIndexById('id_product')).getValue());
                mscombination_cmenu.setItemText('attribute', '<?php echo _l('Attribute:'); ?> '+prop_tb._mscombinationGrid.cells(rowid,prop_tb._mscombinationGrid.getColIndexById('name')).getTitle());
                mscombination_cmenu.setItemText('shop', '<?php echo _l('Shop:'); ?> '+prop_tb._mscombinationGrid.cells(rowid,prop_tb._mscombinationGrid.getColIndexById('id_shop')).getTitle());
                if (lastColumnRightClicked_Mscombination==clipboardType_Mscombination)
                {
                    mscombination_cmenu.setItemEnabled('paste');
                }else{
                    mscombination_cmenu.setItemDisabled('paste');
                }
                return true;
            });
            
            needInitMscombination=0;
        }
    }

    function setPropertiesPanel_mscombination(id){
        if (id=='mscombination')
        {
            if(lastProductSelID!=undefined && lastProductSelID!="")
            {
                dhxLayout.cells('b').setText('<?php echo _l('Properties', 1).' '._l('of', 1); ?> '+getSelectedItemValueOrID(cat_grid,lastProductSelID,'name'));
            }
            hidePropTBButtons();
            prop_tb.showItem('mscombination_refresh');
            prop_tb.showItem('mscombination_selectall');
            prop_tb.showItem('mscombination_exportcsv');
            prop_tb.showItem('prop_mscombination_settings_menu');
            <?php if(version_compare(_PS_VERSION_, '1.7.2.0', '>=')){ ?>
                prop_tb.showItem('mscombination_refresh_physical_stocks');
            <?php } ?>
            prop_tb.setItemText('panel', '<?php echo _l('Multistore : combinations', 1); ?>');
            prop_tb.setItemImage('panel', 'fa fa-layer-group');
            needInitMscombination = 1;
            initMscombination();
            propertiesPanel='mscombination';
            if (lastProductSelID!=0)
            {
                displayMscombination();
            }
        }
        if (id=='mscombination_refresh')
        {
            displayMscombination();
        }
        if (id=='prop_mscombination_grideditor'){
            openWinGridEditor('type_mscombination');
        }
        if (id=='mscombination_selectall')
        {
            prop_tb._mscombinationGrid.enableSmartRendering(false);
            prop_tb._mscombinationGrid.selectAll();
        }
        if(id=='mscombination_refresh_physical_stocks'){
            $.post("index.php?ajax=1&act=cat_product_update&action=stocks&"+new Date().getTime(),{},function(){
                displayMscombination();
            });
        }
        if(id=='mscombination_exportcsv'){
            displayQuickExportWindow(prop_tb._mscombinationGrid,1,true);
        }
    }
    prop_tb.attachEvent("onClick", setPropertiesPanel_mscombination);

    function displayMscombination(reloadJustChecbox)
    {
        prop_tb._mscombinationGrid.clearAll(true);
        prop_tb._mscombinationGrid.load("index.php?ajax=1&act=cat_mscombination_get"+(cat_grid.getSelectedRowId()!=null?"&idlist="+cat_grid.getSelectedRowId():"")+"&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),function()
        {
            nb=prop_tb._mscombinationGrid.getRowsNum();
            prop_tb._mscombinationGrid._rowsNum=nb;
            
               // UISettings
                loadGridUISettings(prop_tb._mscombinationGrid);
                prop_tb._mscombinationGrid._first_loading=0;
                
            nb = prop_tb._mscombinationGrid.getUserData("", "nb_combinations");
            prop_tb._sb.setText(nb+(nb>1?" <?php echo _l('combinations'); ?>":" <?php echo _l('combination'); ?>"));
            
            marginMatrix_form = prop_tb._mscombinationGrid.getUserData("", "marginMatrix_form");
               prop_tb._mscombinationGrid.forEachRow(function(id){
              calculMarginMscombination(id);
            });
            
            <?php sc_ext::readCustomMsCombinationGridConfigXML('afterGetRows'); ?>
        });
    }


    let mscombination_current_id = 0;
    cat_grid.attachEvent("onRowSelect",function (idproduct){
        if (propertiesPanel=='mscombination' && (cat_grid.getSelectedRowId()!==null && mscombination_current_id!=idproduct)){
            //initMscombination();
            displayMscombination(false);
            mscombination_current_id=idproduct;
        }
    });

function addMsCombinationInQueue(rId, action, cIn)
{
    var params = {
        name: "cat_mscombination_update_queue",
        row: rId,
        action: "update",
        params: {},
        callback: "callbackMsCombination('"+rId+"','update','"+rId+"');"
    };
    // COLUMN VALUES
        params.params["id_lang"] = SC_ID_LANG;
        params.params[prop_tb._mscombinationGrid.getColumnId(cIn)] = prop_tb._mscombinationGrid.cells(rId,cIn).getValue();
        
        cInPPriceHT = prop_tb._mscombinationGrid.getColIndexById('ppriceextax');
        params.params['ppriceextax'] = prop_tb._mscombinationGrid.cells(rId,cInPPriceHT).getValue();
    // USER DATA
        if(rId!=undefined && rId!=null && rId!="" && rId!=0)
        {
            if(prop_tb._mscombinationGrid.UserData[rId]!=undefined && prop_tb._mscombinationGrid.UserData[rId]!=null && prop_tb._mscombinationGrid.UserData[rId]!="")
            {
                $.each(prop_tb._mscombinationGrid.UserData[rId].keys, function(i, key){
                    params.params[key] = prop_tb._mscombinationGrid.UserData[rId].values[i];
                });
            }
        }
        if(prop_tb._mscombinationGrid.UserData.gridglobaluserdata.keys!=undefined && prop_tb._mscombinationGrid.UserData.gridglobaluserdata.keys!=null && prop_tb._mscombinationGrid.UserData.gridglobaluserdata.keys!="")
        {
            $.each(prop_tb._mscombinationGrid.UserData.gridglobaluserdata.keys, function(i, key){
                params.params[key] = prop_tb._mscombinationGrid.UserData.gridglobaluserdata.values[i];
            });
        }
    
    params.params = JSON.stringify(params.params);
    addInUpdateQueue(params,prop_tb._mscombinationGrid);
}

// CALLBACK FUNCTION
function callbackMsCombination(sid,action,tid)
{
    <?php sc_ext::readCustomMsCombinationGridConfigXML('onAfterUpdate'); ?>
    if (action=='update')
    {
        prop_tb._mscombinationGrid.setRowTextNormal(sid);
    }
}
<?php } ?>