<?php
 if (version_compare(_PS_VERSION_, '1.5.0.0', '>=') && _r('GRI_CAT_PROPERTIES_GRID_SPECIFIC_PRICE')) { ?>

// INITIALISATION TOOLBAR
prop_tb.attachEvent("onClick", function setPropertiesPanel_combinations(id){
    if (id=='combinations')
    {
    
        prop_tb.combi_subproperties_tb.addListOption('combiSubProperties', 'combi_specificprices', 9, "button", '<?php echo _l('Specific prices', 1); ?>', "fad fa-money-check-edit-alt");

        prop_tb.combi_subproperties_tb.attachEvent("onClick", function(id){
            if(id=="combi_specificprices")
            {
                hideSubpropertiesItems();
                prop_tb.combi_subproperties_tb.setItemText('combiSubProperties', '<?php echo _l('Specific prices', 1); ?>');
                prop_tb.combi_subproperties_tb.setItemImage('combiSubProperties', 'fad fa-money-check-edit-alt');
                actual_subproperties = "combi_specificprices";
                initCombinationSpecificPrices();
            }
        });
                
        prop_tb._combinationsGrid.attachEvent("onRowSelect", function(id,ind){
            if (!prop_tb._combinationsLayout.cells('b').isCollapsed())
            {
                if(actual_subproperties == "combi_specificprices"){
                     getCombinationsSpecificPrices();
                }
            }
        });
    }
});
            
// INIT GRID
clipboardType_CombinationsSpecificprices = null;
combi_customername = null;
function initCombinationSpecificPrices()
{
    prop_tb.combi_subproperties_tb.addButton('specificprice_refresh',100,'','fa fa-sync green','fa fa-sync green');
    prop_tb.combi_subproperties_tb.setItemToolTip('specificprice_refresh','<?php echo _l('Refresh grid', 1); ?>');
    prop_tb.combi_subproperties_tb.addButton('specificprice_add',100,'','fa fa-plus-circle green','fa fa-plus-circle green');
    prop_tb.combi_subproperties_tb.setItemToolTip('specificprice_add','<?php echo _l('Create new specific price', 1); ?>');
    prop_tb.combi_subproperties_tb.addButton('specificprice_del',100,'','fa fa-minus-circle red','fa fa-minus-circle red');
    prop_tb.combi_subproperties_tb.setItemToolTip('specificprice_del','<?php echo _l('Delete selected item', 1); ?>');
    prop_tb.combi_subproperties_tb.addButton('specificprice_select_all',100,'','fa fa-bolt yellow','fa fa-bolt yellow');
    prop_tb.combi_subproperties_tb.setItemToolTip('specificprice_select_all','<?php echo _l('Select All', 1); ?>');


    prop_tb.combi_subproperties_tb.attachEvent("onClick", function(id){
        if (id=='specificprice_refresh')
        {
            if (lastCombiSelID!=0)
            getCombinationsSpecificPrices();
        }
        if (id=='specificprice_add')
        {
            if (lastCombiSelID==0){
                alert('<?php echo _l('Please select a combination', 1); ?>');
            }else{
                var newId = new Date().getTime();
                specificpricesDataProcessorURLBase="index.php?ajax=1&act=cat_combination_specificprice_update&id_product="+lastProductSelID+"&id_lang="+SC_ID_LANG;
                specificpricesDataProcessor.serverProcessor=specificpricesDataProcessorURLBase;
                specificpricesDataProcessor.enablePartialDataSend(false);
                var maxQuantity=1;
                var maxValue=10;
                var percent='';
                let newRow=[];
                    for(const cId of prop_tb._combinationsSpecificPricesGrid.columnIds) {
                        switch(cId) {
                            case 'id_specific_price':
                                newRow.push(newId);
                                break;
                            case 'id_product_attribute':
                                newRow.push(prop_tb._combinationsGrid.getSelectedRowId());
                                break;
                            case 'id_shop':
                            case 'id_shop_group':
                            case 'id_group':
                            case 'id_country':
                            case 'id_currency':
                                newRow.push(0);
                                break;
                            case 'from_quantity':
                                newRow.push(1);
                                break;
                            case 'price':
                                newRow.push(<?php echo version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? -1 : 0; ?>)
                                break;
                            case 'reduction_tax':
                                newRow.push(<?php echo version_compare(_PS_VERSION_, '1.6.0.11', '>=') ? "'"._s('CAT_PROD_SPECIFIC_PRICES_DEFAULT_TAX')."'" : "'"._l('Incl. tax')."'"; ?>);
                                break;
                            default:
                                newRow.push("");
                        }
                    }
                prop_tb._combinationsSpecificPricesGrid.addRow(newId,newRow);
            }
        }
        if (id=='specificprice_del')
        {
            if (prop_tb._combinationsSpecificPricesGrid.getSelectedRowId()==null)
            {
                alert('<?php echo _l('Please select an item', 1); ?>');
            }else{
                if (lastCombiSelID!=0)
                {
                    if (confirm('<?php echo _l('Are you sure you want to delete the selected items?', 1); ?>'))
                    {

                        selection=prop_tb._combinationsSpecificPricesGrid.getSelectedRowId();
                        $.post('index.php?ajax=1&act=cat_combination_specificprice_del',{'rowslist':selection},function(data){
                            if (selection!='' && selection!=null)
                            {
                                getCombinationsSpecificPrices();
                            }
                        });
                    }
                }else{
                    alert('<?php echo _l('Please select a product', 1); ?>');
                }
            }
        }
        if(id=='specificprice_select_all'){
            prop_tb._combinationsSpecificPricesGrid.selectAll();
        }
    });

    prop_tb.combi_subproperties_tb.showItem('specificprice_refresh');
    prop_tb.combi_subproperties_tb.showItem('specificprice_add');
    prop_tb.combi_subproperties_tb.showItem('specificprice_del');
    prop_tb.combi_subproperties_tb.showItem('specificprice_select_all');
    
    prop_tb._combinationsSpecificPricesGrid = prop_tb._combinationsLayout.cells('b').attachGrid();
    prop_tb._combinationsSpecificPricesGrid.setImagePath("lib/js/imgs/");
    prop_tb._combinationsSpecificPricesGrid.setDateFormat("%Y-%m-%d %H:%i:%s","%Y-%m-%d %H:%i:%s");
    
    // UISettings
    prop_tb._combinationsSpecificPricesGrid._uisettings_prefix='cat_combination_specificprice';
    prop_tb._combinationsSpecificPricesGrid._uisettings_name=prop_tb._combinationsSpecificPricesGrid._uisettings_prefix;
       prop_tb._combinationsSpecificPricesGrid._first_loading=1;
    
    prop_tb._combinationsSpecificPricesGrid.disableActionAfterRowInserted = false;
       
    // UISettings
    initGridUISettings(prop_tb._combinationsSpecificPricesGrid);
     prop_tb._combinationsSpecificPricesGrid.enableColumnMove(false);
    
    prop_tb._combinationsSpecificPricesGrid.attachEvent("onEditCell", function(stage, rId, cIn,nValue,oValue){
            if (stage==1 && this.editor && this.editor.obj) this.editor.obj.select();
     
            if(['price_with_reduction_tax_excl','price_with_reduction_tax_incl'].includes(prop_tb._combinationsSpecificPricesGrid.getColumnId(cIn)) && prop_tb._combinationsSpecificPricesGrid.disableActionAfterRowInserted) {
                return false;
            }
     
            if(stage==2 && nValue!=oValue)
             {
                 // CHECK ID_CUSTOMER
                 if (prop_tb._combinationsSpecificPricesGrid.getColumnId(cIn) == "id_customer") {
                     var cellValue = prop_tb._combinationsSpecificPricesGrid.cells(rId,cIn).getValue();
                     var cellValueInt = parseInt(cellValue);
                     if (!Number.isInteger(cellValueInt) && cellValue != 0) {
                         dhtmlx.message({text:'<?php echo _l('This customer in unknown'); ?>',type:'error',expire:3000});
                         return false;
                     }
                 }
     
                 if (combi_customername) {
                     prop_tb._combinationsSpecificPricesGrid.cells(rId,cIn).setValue(combi_customername);
                     combi_customername = null;
                 }
     
                // PRICE WITH REDUCTION
                let idxFixedPrice = prop_tb._combinationsSpecificPricesGrid.getColIndexById('price');
                let idxReductionTax = prop_tb._combinationsSpecificPricesGrid.getColIndexById('reduction_tax');
                let idxCurrentPriceHT = prop_tb._combinationsSpecificPricesGrid.getColIndexById('price_exl_tax');
                let idxCurrentPriceTTC = prop_tb._combinationsSpecificPricesGrid.getColIndexById('price_inc_tax');
                let idxReduction = prop_tb._combinationsSpecificPricesGrid.getColIndexById('reduction');
                switch(prop_tb._combinationsSpecificPricesGrid.getColumnId(cIn)) {
                    case 'price_with_reduction_tax_excl':
                        let current_price_ht = Number(prop_tb._combinationsSpecificPricesGrid.cells(rId,idxCurrentPriceHT).getValue());
                        let reduction_amount_ht = current_price_ht - nValue;
                        prop_tb._combinationsSpecificPricesGrid.cells(rId,idxReduction).setValue(reduction_amount_ht);
                        prop_tb._combinationsSpecificPricesGrid.cells(rId,idxReduction).cell.wasChanged=true;
                        prop_tb._combinationsSpecificPricesGrid.cells(rId,idxReductionTax).setValue('0');
                        prop_tb._combinationsSpecificPricesGrid.cells(rId,idxReductionTax).setLabel("<?php echo _l('Excl. tax'); ?>");
                        prop_tb._combinationsSpecificPricesGrid.cells(rId,idxReductionTax).cell.wasChanged=true;
                        prop_tb._combinationsSpecificPricesGrid.cells(rId,idxFixedPrice).setValue('-1');
                        prop_tb._combinationsSpecificPricesGrid.cells(rId,idxFixedPrice).cell.wasChanged=true;
                        break;
                    case 'price_with_reduction_tax_incl':
                        let current_price_ttc = Number(prop_tb._combinationsSpecificPricesGrid.cells(rId,idxCurrentPriceTTC).getValue());
                        let reduction_amount_ttc = current_price_ttc - nValue;
                        prop_tb._combinationsSpecificPricesGrid.cells(rId,idxReduction).setValue(reduction_amount_ttc);
                        prop_tb._combinationsSpecificPricesGrid.cells(rId,idxReduction).cell.wasChanged=true;
                        prop_tb._combinationsSpecificPricesGrid.cells(rId,idxReductionTax).setValue('1');
                        prop_tb._combinationsSpecificPricesGrid.cells(rId,idxReductionTax).setLabel("<?php echo _l('Incl. tax'); ?>");
                        prop_tb._combinationsSpecificPricesGrid.cells(rId,idxReductionTax).cell.wasChanged=true;
                        prop_tb._combinationsSpecificPricesGrid.cells(rId,idxFixedPrice).setValue('-1');
                        prop_tb._combinationsSpecificPricesGrid.cells(rId,idxFixedPrice).cell.wasChanged=true;
                        break;
                }
             }
            return true;
    });
    
    specificpricesDataProcessorURLBase="index.php?ajax=1&act=cat_combination_specificprice_update&id_product="+lastProductSelID+"&id_product_attribute="+prop_tb._combinationsGrid.getSelectedRowId()+"&id_lang="+SC_ID_LANG;
    specificpricesDataProcessor = new dataProcessor(specificpricesDataProcessorURLBase);
    specificpricesDataProcessor.enableDataNames(true);
    specificpricesDataProcessor.enablePartialDataSend(true);
    specificpricesDataProcessor.setTransactionMode("POST");
    specificpricesDataProcessor.attachEvent("onAfterUpdate", function (sid, action, tid, xml) {
        switch (action) {
            case 'insert':
                specificpricesDataProcessor.enablePartialDataSend(true);
                prop_tb._combinationsSpecificPricesGrid.cells(tid, 0).setValue(tid);
                prop_tb._combinationsSpecificPricesGrid.disableActionAfterRowInserted = true;
                break;
            case 'update':
                let idxFixedPrice = prop_tb._combinationsSpecificPricesGrid.getColIndexById('price');
                let newFixedPrice = Number(prop_tb._combinationsSpecificPricesGrid.cells(sid, idxFixedPrice).getValue());
                // activation/desactivation/coloration champs selon prix fixe
                for (const cId of [prop_tb._combinationsSpecificPricesGrid.getColIndexById('price_with_reduction_tax_excl'), prop_tb._combinationsSpecificPricesGrid.getColIndexById('price_with_reduction_tax_incl')]) {
                    if (cId !== undefined) {
                        let current_cell = prop_tb._combinationsSpecificPricesGrid.cells(sid, cId);
                        if (newFixedPrice > -1) {
                            current_cell.setDisabled(true);
                            current_cell.setBgColor('#D7D7D7');
                        } else {
                            current_cell.setBgColor('');
                            current_cell.setDisabled(false);
                        }
                    }
                }
                break;
        }
    });
    specificpricesDataProcessorURLBase="index.php?ajax=1&act=cat_combination_specificprice_update&id_product="+lastProductSelID+"&id_product_attribute="+prop_tb._combinationsGrid.getSelectedRowId()+"&id_lang="+SC_ID_LANG;
    specificpricesDataProcessor.serverProcessor=specificpricesDataProcessorURLBase;
    specificpricesDataProcessor.init(prop_tb._combinationsSpecificPricesGrid);
    
    prop_tb._combinationsSpecificPricesGrid.attachEvent("onDhxCalendarCreated",function(calendar){
        calendar.setSensitiveRange("2012-01-01",null);
        dhtmlXCalendarObject.prototype.langData["<?php echo $user_lang_iso; ?>"] = lang_calendar;
        calendar.loadUserLanguage("<?php echo $user_lang_iso; ?>");
    });
            
    // Context menu for grid
    combinationsspecificprices_cmenu=new dhtmlXMenuObject();
    combinationsspecificprices_cmenu.renderAsContextMenu();
    function onGridCombinationsSpecificpricesContextButtonClick(itemId){
        tabId=prop_tb._combinationsSpecificPricesGrid.contextID.split('_');
        tabId=tabId[0];
        if (itemId=="copy"){
            if (lastColumnRightClicked_CombinationsSpecificprices!=0)
            {
                clipboardValue_CombinationsSpecificprices=prop_tb._combinationsSpecificPricesGrid.cells(tabId,lastColumnRightClicked_CombinationsSpecificprices).getValue();

                if(lastColumnRightClicked_CombinationsSpecificprices == prop_tb._combinationsSpecificPricesGrid.getColIndexById('id_customer'))
                {
                     var mask = prop_tb._combinationsSpecificPricesGrid.cells(tabId,lastColumnRightClicked_CombinationsSpecificprices).getValue();
                     $.post('index.php?ajax=1&act=cat_specificprice_customer_get&ajaxCall=1&getIdCus=1',{'mask':mask},function(data)            {
                         var res = JSON.parse(data);
                         clipboardValue_CombinationsSpecificprices=parseInt(res.id_customer);
                         combi_customername = res.name;
                     });
                 } else {
                    clipboardValue_CombinationsSpecificprices=prop_tb._combinationsSpecificPricesGrid.cells(tabId,lastColumnRightClicked_CombinationsSpecificprices).getValue();
                 }

                 combinationsspecificprices_cmenu.setItemText('paste' , '<?php echo _l('Paste'); ?> '+prop_tb._combinationsSpecificPricesGrid.cells(tabId,lastColumnRightClicked_CombinationsSpecificprices).getTitle());
                clipboardType_CombinationsSpecificprices=lastColumnRightClicked_CombinationsSpecificprices;
            }
        }
        if (itemId=="paste"){
            if (lastColumnRightClicked_CombinationsSpecificprices!=0 && clipboardValue_CombinationsSpecificprices!=null && clipboardType_CombinationsSpecificprices==lastColumnRightClicked_CombinationsSpecificprices)
            {
                selection=prop_tb._combinationsSpecificPricesGrid.getSelectedRowId();
                if (selection!='' && selection!=null)
                {
                    selArray=selection.split(',');
                    for(i=0 ; i < selArray.length ; i++)
                    {
                        if (prop_tb._combinationsSpecificPricesGrid.getColumnId(lastColumnRightClicked_CombinationsSpecificprices).substr(0,5)!='attr_')
                        {
                            prop_tb._combinationsSpecificPricesGrid.cells(selArray[i],lastColumnRightClicked_CombinationsSpecificprices).setValue(clipboardValue_CombinationsSpecificprices);
                            prop_tb._combinationsSpecificPricesGrid.cells(selArray[i],lastColumnRightClicked_CombinationsSpecificprices).cell.wasChanged=true;
                            specificpricesDataProcessor.setUpdated(selArray[i],true,"updated");
                        }
                    }
                }
            }
        }
    }
    combinationsspecificprices_cmenu.attachEvent("onClick", onGridCombinationsSpecificpricesContextButtonClick);
    var contextMenuXML='<menu absolutePosition="auto" mode="popup" maxItems="8"  globalCss="contextMenu" globalSecondCss="contextMenu" globalTextCss="contextMenuItem">'+
            '<item text="Object" id="object" enabled="false"/>'+
            '<item text="<?php echo _l('Copy'); ?>" id="copy"/>'+
            '<item text="<?php echo _l('Paste'); ?>" id="paste"/>'+
        '</menu>';
    combinationsspecificprices_cmenu.loadStruct(contextMenuXML);
    prop_tb._combinationsSpecificPricesGrid.enableContextMenu(combinationsspecificprices_cmenu);

    prop_tb._combinationsSpecificPricesGrid.attachEvent("onBeforeContextMenu", function(rowid,colidx,grid){
        var disableOnCols=new Array(
                prop_tb._combinationsSpecificPricesGrid.getColIndexById('id_product_attribute'),
                prop_tb._combinationsSpecificPricesGrid.getColIndexById('id_specific_price')
                );
        if (in_array(colidx,disableOnCols))
        {
            return false;
        }
        lastColumnRightClicked_CombinationsSpecificprices=colidx;
        combinationsspecificprices_cmenu.setItemText('object', '<?php echo _l('Specific price:'); ?> '+prop_tb._combinationsSpecificPricesGrid.cells(rowid,prop_tb._combinationsSpecificPricesGrid.getColIndexById('id_specific_price')).getTitle());
        if (lastColumnRightClicked_CombinationsSpecificprices==clipboardType_CombinationsSpecificprices)
        {
            combinationsspecificprices_cmenu.setItemEnabled('paste');
        }else{
            combinationsspecificprices_cmenu.setItemDisabled('paste');
        }
        return true;
    });
    
    getCombinationsSpecificPrices();
}

// DISPLAY
    function getCombinationsSpecificPrices()
    {
        prop_tb._combinationsSpecificPricesGrid.disableActionAfterRowInserted=false;
        prop_tb._combinationsSpecificPricesGrid.clearAll(true);
        $.post("index.php?ajax=1&act=cat_combination_specificprice_get&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{'id_product_attribute': prop_tb._combinationsGrid.getSelectedRowId()},function(data)
        {
            prop_tb._combinationsSpecificPricesGrid.parse(data);

            let disabled_fields_by_id_speprice = prop_tb._combinationsSpecificPricesGrid.getUserData('','disabled_fields_by_id_speprice');
            if(disabled_fields_by_id_speprice !== '') {
                for(const rId of disabled_fields_by_id_speprice.split(',')) {
                    for(const cId of [prop_tb._combinationsSpecificPricesGrid.getColIndexById('price_with_reduction_tax_excl'),prop_tb._combinationsSpecificPricesGrid.getColIndexById('price_with_reduction_tax_incl')]) {
                        if(cId !== undefined) {
                            let current_cell = prop_tb._combinationsSpecificPricesGrid.cells(rId, cId);
                            current_cell.setBgColor('#D7D7D7');
                            current_cell.setDisabled(true);
                        }
                    }
                }
            }

               // UISettings
            loadGridUISettings(prop_tb._combinationsSpecificPricesGrid);
            prop_tb._combinationsSpecificPricesGrid._first_loading=0;
        });
    }

<?php } ?>