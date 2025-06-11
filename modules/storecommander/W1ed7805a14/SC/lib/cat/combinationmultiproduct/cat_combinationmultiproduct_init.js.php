<?php
$title = _l('Multi-product combinations', 1);
$icon = 'fad fa-ball-pile';
?>

    <?php if (_r('GRI_CAT_PROPERTIES_GRID_COMBI_MULTI_PRODUCTS')) { ?>
        prop_tb.addListOption('panel', 'combinationmultiproduct', 1, "button", '<?php echo $title; ?>', "<?php echo $icon; ?>");
        allowed_properties_panel[allowed_properties_panel.length] = "combinationmultiproduct";
    <?php } ?>

    var opts = [['combinationmultiproduct_filters_reset', 'obj', '<?php echo _l('Reset filters', 1); ?>', ''],
        ['separator1', 'sep', '', ''],
        ['combinationmultiproduct_filters_cols_show', 'obj', '<?php echo _l('Show all columns', 1); ?>', ''],
        ['combinationmultiproduct_filters_cols_hide', 'obj', '<?php echo _l('Hide all columns', 1); ?>', '']
    ];
    prop_tb.addButtonSelect("combinationmultiproduct_filters", 1000, "", opts, "fa fa-filter", "fa fa-filter",false,true);
    prop_tb.setItemToolTip('combinationmultiproduct_filters','<?php echo _l('Filter options', 1); ?>');
    prop_tb.addButton("combinationmultiproduct_refresh",1000, "", "fa fa-sync green", "fa fa-sync green");
    prop_tb.setItemToolTip('combinationmultiproduct_refresh','<?php echo _l('Refresh grid', 1); ?>');
    prop_tb.addButton("combinationmultiproduct_win_attributes",1000, "", "fa fa-asterisk yellow", "fa fa-asterisk yellow");
    prop_tb.setItemToolTip('combinationmultiproduct_win_attributes','<?php echo _l('Open attributes and groups window', 1); ?>');
    <?php if(version_compare(_PS_VERSION_, '1.7.2.0', '>=')){ ?>
        prop_tb.addButton("combinationmultiproduct_refresh_physical_stocks",1000, "", "fad fa-retweet-alt green", "fad fa-retweet-alt green");
        prop_tb.setItemToolTip('combinationmultiproduct_refresh_physical_stocks','<?php echo _l('Refresh stocks'); ?>');
    <?php } ?>
    prop_tb.addButton("combinationmultiproduct_exportcsv",1000, "", "fad fa-file-csv green", "fad fa-file-csv green");
    prop_tb.setItemToolTip('combinationmultiproduct_exportcsv','<?php echo _l('Export grid to clipboard in CSV format for MSExcel with tab delimiter.', 1); ?>');
    prop_tb.addButton('combinationmultiproduct_del',1000,'','fa fa-minus-circle red','fa fa-minus-circle red');
    prop_tb.setItemToolTip('combinationmultiproduct_del','<?php echo _l('Delete combination', 1); ?>');
    prop_tb.addButton("combinationmultiproduct_selectall",1000, "", "fa fa-bolt yellow", "fad fa-bolt grey");
    prop_tb.setItemToolTip('combinationmultiproduct_selectall','<?php echo _l('Select all combinations', 1); ?>');
    var options_for_references = [
        ['orm_delete', 'obj', '<?php echo _l('Delete references'); ?>', ''],
        ['orm_prodref_auto', 'obj', '<?php echo _l('product reference %s unique id', true, array(_s('CAT_PROPERTIES_COMBINATION_AUTO_REF_SEPARATOR'))); ?>', ''],
        ['orm_prodref_attrid', 'obj', '<?php echo _l('product reference %s id_product_attribute', true, array(_s('CAT_PROPERTIES_COMBINATION_AUTO_REF_SEPARATOR'))); ?>', ''],
        ['orm_prodref_auto_name', 'obj', '<?php echo _l('product reference %1$s unique id %1$s name', true, array(_s('CAT_PROPERTIES_COMBINATION_AUTO_REF_SEPARATOR'))); ?>', '']
    ]
    prop_tb.addButtonSelect('combinationmultiproduct_reference_generator',1000,'<?php echo _l('Combination references'); ?>',options_for_references,'fa fa-plus-circle green','fa fa-plus-circle green',false,true);
    getTbSettingsButton(prop_tb, {'grideditor':1,'settings':0}, 'prop_combinationmultiproduct_',1000);

    var marginMatrix_form = "";
    function calculMarginCombinationMultiProduct(rId)
    {
        if(prop_tb._combinationmultiproductGrid.getColIndexById('margin')!=undefined && prop_tb._combinationmultiproductGrid.getColIndexById('wholesale_price')!=undefined)
        {
            var formule = marginMatrix_form;

            idxPriceIncTaxes=prop_tb._combinationmultiproductGrid.getColIndexById('price');
            idxPriceWithoutTaxes=prop_tb._combinationmultiproductGrid.getColIndexById('priceextax');
            idxWholeSalePrice=prop_tb._combinationmultiproductGrid.getColIndexById('wholesale_price');

            idxMargin=prop_tb._combinationmultiproductGrid.getColIndexById('margin');

            var price = prop_tb._combinationmultiproductGrid.cells(rId,idxPriceWithoutTaxes).getValue();
            if(price==null || price=="")
                price = 0;
            formule = formule.replace("{price}",price)
                            .replace("{price}",price)
                            .replace("{price}",price);

            var price_inc_tax = prop_tb._combinationmultiproductGrid.cells(rId,idxPriceIncTaxes).getValue();
            if(price_inc_tax==null || price_inc_tax=="")
                price_inc_tax = 0;
            formule = formule.replace("{price_inc_tax}",price_inc_tax)
                            .replace("{price_inc_tax}",price_inc_tax)
                            .replace("{price_inc_tax}",price_inc_tax);

            var wholesale_price = prop_tb._combinationmultiproductGrid.cells(rId,idxWholeSalePrice).getValue();
            if(wholesale_price==null || wholesale_price=="")
                wholesale_price = 0;
            formule = formule.replace("{wholesale_price}",wholesale_price)
                            .replace("{wholesale_price}",wholesale_price)
                            .replace("{wholesale_price}",wholesale_price);

            if(wholesale_price>0 && price>0)
                var margin = eval(formule);
            else
                var margin = 0;
            prop_tb._combinationmultiproductGrid.cells(rId,idxMargin).setValue(priceFormat(margin));

            <?php if (_s('CAT_PROD_GRID_MARGIN_COLOR') != '') { ?>
            if (idxMargin)
            {
                var rules=('<?php echo str_replace("'", '', _s('CAT_PROD_GRID_MARGIN_COLOR')); ?>').split(';');
                for(var i=(rules.length-1) ; i >= 0 ; i--){
                    var rule=rules[i].split(':');
                    if ( Number(prop_tb._combinationmultiproductGrid.cells(rId,idxMargin).getValue()) < Number(rule[0])){
                        prop_tb._combinationmultiproductGrid.cells(rId,idxMargin).setBgColor(rule[1]);
                        prop_tb._combinationmultiproductGrid.cells(rId,idxMargin).setTextColor('#FFFFFF');
                    }
                }
            }
            <?php } ?>
        }
    }

    function onEditCellCombinationMultiProduct(stage,rId,cInd,nValue,oValue)
    {
        if (stage==1 && this.editor && this.editor.obj) this.editor.obj.select();
        if (stage==2)
        {
            idxRef=prop_tb._combinationmultiproductGrid.getColIndexById('reference');
            if (cInd == idxRef)
            {
                 var splitted = rId.split("_");
                var product_id = splitted[0]+"_"+splitted[1];
                if(product_id!=null && product_id!=undefined)
                {
                    prop_tb._combinationmultiproductGrid.forEachRow(function(id){
                        var temp_id = "_"+id;
                        if(temp_id.search("_"+product_id)>=0)
                        {
                            prop_tb._combinationmultiproductGrid.cells(id,idxRef).setValue(nValue);
                        }
                   });
                }
            }

            idxRefSupplier=prop_tb._combinationmultiproductGrid.getColIndexById('supplier_reference');
            if (cInd == idxRefSupplier)
            {
                 var splitted = rId.split("_");
                var product_id = splitted[0]+"_"+splitted[1];
                if(product_id!=null && product_id!=undefined)
                {
                    prop_tb._combinationmultiproductGrid.forEachRow(function(id){
                        var temp_id = "_"+id;
                        if(temp_id.search("_"+product_id)>=0)
                        {
                            prop_tb._combinationmultiproductGrid.cells(id,idxRefSupplier).setValue(nValue);
                        }
                   });
                }
            }

            idxPrice=prop_tb._combinationmultiproductGrid.getColIndexById('price');
            idxPriceExTax=prop_tb._combinationmultiproductGrid.getColIndexById('priceextax');
            idxEcotax=prop_tb._combinationmultiproductGrid.getColIndexById('ecotax');
            idxTaxrate=prop_tb._combinationmultiproductGrid.getColIndexById('taxrate');
            idxWholeSalePrice=prop_tb._combinationmultiproductGrid.getColIndexById('wholesale_price');

            /* define ecotax to use */
            let ecotax = 0;
            if (idxEcotax) {
                ecotax = noComma(prop_tb._combinationmultiproductGrid.cells(rId,idxEcotax).getValue());
            }
            let ecotax_to_use = ecotax;

            /* define ecotax tax to use */
            let tax_product = noComma(prop_tb._combinationmultiproductGrid.getUserData(rId,'taxrate')/100+1);

            if (cInd == idxEcotax){ //ecotax
                let eco = noComma(nValue);
                prop_tb._combinationmultiproductGrid.cells(rId,idxEcotax).setValue(priceFormat6Dec(nValue));
                 <?php if (_s('CAT_PROD_ECOTAXINCLUDED')) { ?>
                let price = noComma(prop_tb._combinationmultiproductGrid.cells(rId,idxPrice).getValue());
                prop_tb._combinationmultiproductGrid.cells(rId,idxPriceExTax).setValue(priceFormat6Dec((price - eco) / tax_product));
                <?php } ?>
                calculMarginCombinationMultiProduct(rId);
            }
            if (cInd == idxPriceExTax){ //priceExTax update
                prop_tb._combinationmultiproductGrid.cells(rId,idxPriceExTax).setValue(priceFormat6Dec(nValue));
                prop_tb._combinationmultiproductGrid.cells(rId,idxPrice).setValue(priceFormat6Dec(noComma(nValue) * tax_product + ecotax_to_use));
                calculMarginCombinationMultiProduct(rId);
            }
            if (cInd == idxWholeSalePrice){ //Wholesale price
                calculMarginCombinationMultiProduct(rId);
            }
            if (cInd == idxPrice){ //price update
                prop_tb._combinationmultiproductGrid.cells(rId,idxPrice).setValue(priceFormat6Dec(nValue));
                prop_tb._combinationmultiproductGrid.cells(rId,idxPriceExTax).setValue(priceFormat6Dec((noComma(nValue) - ecotax_to_use) / tax_product));
                cInd = idxPriceExTax;
                calculMarginCombinationMultiProduct(rId);
            }
            <?php sc_ext::readCustomCombinationMultiProductGridConfigXML('onEditCell'); ?>
            if(nValue!=oValue)
            {
                <?php sc_ext::readCustomCombinationMultiProductGridConfigXML('onBeforeUpdate'); ?>
                addCombinationMInQueue(rId, "update", cInd);
            }
        }
        return true;
    }

    clipboardType_CombinationMultiProduct = null;
    needInitCombinationMultiProduct = 1;
    function initCombinationMultiProduct()
    {
        if (needInitCombinationMultiProduct)
        {
            dhxLayout.cells('b').detachObject(true);
            prop_tb._combinationmultiproductLayout = dhxLayout.cells('b').attachLayout('2U');
            prop_tb._combinationmultiproductLayout.cells('b').setText("<?php echo $title; ?>");
            dhxLayout.cells('b').showHeader();

            actual_subproperties = "combimulprd_images";

            prop_tb._combinationmultiproductLayout.cells('b').setWidth(210);

            prop_4column_layout = prop_tb._combinationmultiproductLayout;

            prop_tb.combimulprd_subproperties_tb=prop_tb._combinationmultiproductLayout.cells('b').attachToolbar();
            prop_tb.combimulprd_subproperties_tb.setIconset('awesome');

            var start_cat_combimulti_size_prop = getParamUISettings('start_cat_combimulti_size_prop');
            if(start_cat_combimulti_size_prop==null || start_cat_combimulti_size_prop<=0 || start_cat_combimulti_size_prop=="")
                start_cat_combimulti_size_prop = 210;
            prop_tb._combinationmultiproductLayout.cells('b').setWidth(start_cat_combimulti_size_prop);
            prop_tb._combinationmultiproductLayout.attachEvent("onPanelResizeFinish", function(){
                saveParamUISettings('start_cat_combimulti_size_prop', prop_tb._combinationmultiproductLayout.cells('b').getWidth())
            });

            var opts = new Array();
            prop_tb.combimulprd_subproperties_tb.addButtonSelect("combimulprdSubProperties", 0, "<?php echo _l('Images'); ?>", opts, "fad fa-image", "fad fa-image",false,true);
            hideCombinationMultiProduct_SubpropertiesItems();

            prop_tb._combinationmultiproductLayout.cells('a').hideHeader();
            prop_tb._combinationmultiproductGrid = prop_tb._combinationmultiproductLayout.cells('a').attachGrid();
            prop_tb._combinationmultiproductGrid._name='_combinationmultiproductGrid';
            prop_tb._combinationmultiproductGrid.setImagePath("lib/js/imgs/");
              prop_tb._combinationmultiproductGrid.enableDragAndDrop(false);
            prop_tb._combinationmultiproductGrid.enableMultiselect(true);

            // UISettings
            prop_tb._combinationmultiproductGrid._uisettings_prefix='cat_combinationmultiproduct';
            prop_tb._combinationmultiproductGrid._uisettings_name=prop_tb._combinationmultiproductGrid._uisettings_prefix;
            prop_tb._combinationmultiproductGrid._uisettings_limited=true;
               prop_tb._combinationmultiproductGrid._first_loading=1;

            // UISettings
            initGridUISettings(prop_tb._combinationmultiproductGrid);

            prop_tb._combinationmultiproductGrid.attachEvent("onEditCell", onEditCellCombinationMultiProduct);

            prop_tb._combinationmultiproductGrid.attachEvent("onScroll",function(){
                marginMatrix_form = prop_tb._combinationmultiproductGrid.getUserData("", "marginMatrix_form");
                   prop_tb._combinationmultiproductGrid.forEachRow(function(id){
                  calculMarginCombinationMultiProduct(id);
               });
            });

            prop_tb._combinationmultiproductGrid.attachEvent("onRowSelect",function(){
                propMultiCombiGridStat();
            });

            prop_tb._combinationmultiproductGrid.attachEvent("onDhxCalendarCreated",function(calendar){
                calendar.hideTime();
                calendar.setSensitiveRange("2012-01-01",null);

                dhtmlXCalendarObject.prototype.langData["<?php echo $user_lang_iso; ?>"] = lang_calendar;
                calendar.loadUserLanguage("<?php echo $user_lang_iso; ?>");
            });


            // Context menu
            combinationmultiproduct_cmenu=new dhtmlXMenuObject();
            combinationmultiproduct_cmenu.renderAsContextMenu();
            function onGridCombinationMultiProductContextButtonClick(itemId){
                tabId=prop_tb._combinationmultiproductGrid.contextID.split('_');
                tabId=tabId[0]+"_"+tabId[1];
                if (itemId=="copy"){
                    if (lastColumnRightClicked_CombinationMultiProduct!=0)
                    {
                        clipboardValue_CombinationMultiProduct=prop_tb._combinationmultiproductGrid.cells(tabId,lastColumnRightClicked_CombinationMultiProduct).getValue();
                        combinationmultiproduct_cmenu.setItemText('paste' , '<?php echo _l('Paste'); ?> '+prop_tb._combinationmultiproductGrid.cells(tabId,lastColumnRightClicked_CombinationMultiProduct).getTitle());
                        clipboardType_CombinationMultiProduct=lastColumnRightClicked_CombinationMultiProduct;
                    }
                }
                if (itemId=="paste"){
                    if (lastColumnRightClicked_CombinationMultiProduct!=0 && clipboardValue_CombinationMultiProduct!=null && clipboardType_CombinationMultiProduct==lastColumnRightClicked_CombinationMultiProduct)
                    {
                        selection=prop_tb._combinationmultiproductGrid.getSelectedRowId();
                        if (selection!='' && selection!=null)
                        {
                            selArray=selection.split(',');
                            for(i=0 ; i < selArray.length ; i++)
                            {
                                prop_tb._combinationmultiproductGrid.cells(selArray[i],lastColumnRightClicked_CombinationMultiProduct).setValue(clipboardValue_CombinationMultiProduct);
                                prop_tb._combinationmultiproductGrid.cells(selArray[i],lastColumnRightClicked_CombinationMultiProduct).cell.wasChanged=true;
                                onEditCellCombinationMultiProduct(2,selArray[i],lastColumnRightClicked_CombinationMultiProduct,clipboardValue_CombinationMultiProduct,null);
                            }
                        }
                    }
                }
                <?php
                $massupdate_suffix = '';
                if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                {
                    $massupdate_suffix = '15';
                }
                ?>
                if (itemId=="massupdate_round_price_1"){
                    if (confirm('<?php echo _l('Are you sure that you want to round all these prices up?', 1); ?>')){
                        selection=prop_tb._combinationmultiproductGrid.getSelectedRowId();
                        if (selection!='' && selection!=null)
                        {
                            var column=prop_tb._combinationmultiproductGrid.getColumnId(lastColumnRightClicked_CombinationMultiProduct);
                            $.post('index.php?ajax=1&act=cat_combinationmultiproduct_massupdate<?php echo $massupdate_suffix; ?>&field=mass_round&todo=1&column='+column,{'combilist':selection},function(data){
                                displayCombinationMultiProduct();
                            });
                        }
                    }
                }
                if (itemId=="massupdate_round_price_2"){
                    if (confirm('<?php echo _l('Are you sure that you want to round all these prices up?', 1); ?>')){
                        selection=prop_tb._combinationmultiproductGrid.getSelectedRowId();
                        if (selection!='' && selection!=null)
                        {
                            var column=prop_tb._combinationmultiproductGrid.getColumnId(lastColumnRightClicked_CombinationMultiProduct);
                            $.post('index.php?ajax=1&act=cat_combinationmultiproduct_massupdate<?php echo $massupdate_suffix; ?>&field=mass_round&todo=2&column='+column,{'combilist':selection},function(data){
                                displayCombinationMultiProduct();
                            });
                        }
                    }
                }
                if (itemId=="massupdate_round_price_3"){
                    if (confirm('<?php echo _l('Are you sure that you want to round all these prices up?', 1); ?>')){
                        selection=prop_tb._combinationmultiproductGrid.getSelectedRowId();
                        if (selection!='' && selection!=null)
                        {
                            var column=prop_tb._combinationmultiproductGrid.getColumnId(lastColumnRightClicked_CombinationMultiProduct);
                            $.post('index.php?ajax=1&act=cat_combinationmultiproduct_massupdate<?php echo $massupdate_suffix; ?>&field=mass_round&todo=3&column='+column,{'combilist':selection},function(data){
                                displayCombinationMultiProduct();
                            });
                        }
                    }
                }
                if (itemId=="massupdate_round_price_4"){
                    if (confirm('<?php echo _l('Are you sure that you want to round all these prices up?', 1); ?>')){
                        selection=prop_tb._combinationmultiproductGrid.getSelectedRowId();
                        if (selection!='' && selection!=null)
                        {
                            var column=prop_tb._combinationmultiproductGrid.getColumnId(lastColumnRightClicked_CombinationMultiProduct);
                            $.post('index.php?ajax=1&act=cat_combinationmultiproduct_massupdate<?php echo $massupdate_suffix; ?>&field=mass_round&todo=4&column='+column,{'combilist':selection},function(data){
                                displayCombinationMultiProduct();
                            });
                        }
                    }
                }
                if (itemId=="massupdate_round_price_5"){
                    if (confirm('<?php echo _l('Are you sure that you want to round all these prices up?', 1); ?>')){
                        selection=prop_tb._combinationmultiproductGrid.getSelectedRowId();
                        if (selection!='' && selection!=null)
                        {
                            var column=prop_tb._combinationmultiproductGrid.getColumnId(lastColumnRightClicked_CombinationMultiProduct);
                            $.post('index.php?ajax=1&act=cat_combinationmultiproduct_massupdate<?php echo $massupdate_suffix; ?>&field=mass_round&todo=5&column='+column,{'combilist':selection},function(data){
                                displayCombinationMultiProduct();
                            });
                        }
                    }
                }
                if (itemId=="massupdate_round_price_6"){
                    if (confirm('<?php echo _l('Are you sure that you want to round all these prices up?', 1); ?>')){
                        selection=prop_tb._combinationmultiproductGrid.getSelectedRowId();
                        if (selection!='' && selection!=null)
                        {
                            var column=prop_tb._combinationmultiproductGrid.getColumnId(lastColumnRightClicked_CombinationMultiProduct);
                            $.post('index.php?ajax=1&act=cat_combinationmultiproduct_massupdate<?php echo $massupdate_suffix; ?>&field=mass_round&todo=6&column='+column,{'combilist':selection},function(data){
                                displayCombinationMultiProduct();
                            });
                        }
                    }
                }
                if (itemId=="massupdate_round_price_7"){
                    if (confirm('<?php echo _l('Are you sure that you want to round all these prices up?', 1); ?>')){
                        selection=prop_tb._combinationmultiproductGrid.getSelectedRowId();
                        if (selection!='' && selection!=null)
                        {
                            var column=prop_tb._combinationmultiproductGrid.getColumnId(lastColumnRightClicked_CombinationMultiProduct);
                            $.post('index.php?ajax=1&act=cat_combinationmultiproduct_massupdate<?php echo $massupdate_suffix; ?>&field=mass_round&todo=7&column='+column,{'combilist':selection},function(data){
                                displayCombinationMultiProduct();
                            });
                        }
                    }
                }
                if (itemId=="massupdate_round_price_8"){
                    if (confirm('<?php echo _l('Are you sure that you want to round all these prices up?', 1); ?>')){
                        selection=prop_tb._combinationmultiproductGrid.getSelectedRowId();
                        if (selection!='' && selection!=null)
                        {
                            var column=prop_tb._combinationmultiproductGrid.getColumnId(lastColumnRightClicked_CombinationMultiProduct);
                            $.post('index.php?ajax=1&act=cat_combinationmultiproduct_massupdate<?php echo $massupdate_suffix; ?>&field=mass_round&todo=8&column='+column,{'combilist':selection},function(data){
                                displayCombinationMultiProduct();
                            });
                        }
                    }
                }
                if (itemId=="massupdate_round_price_9"){
                    if (confirm('<?php echo _l('Are you sure that you want to round all these prices up?', 1); ?>')){
                        selection=prop_tb._combinationmultiproductGrid.getSelectedRowId();
                        if (selection!='' && selection!=null)
                        {
                            var column=prop_tb._combinationmultiproductGrid.getColumnId(lastColumnRightClicked_CombinationMultiProduct);
                            $.post('index.php?ajax=1&act=cat_combinationmultiproduct_massupdate<?php echo $massupdate_suffix; ?>&field=mass_round&todo=9&column='+column,{'combilist':selection},function(data){
                                displayCombinationMultiProduct();
                            });
                        }
                    }
                }
                if (itemId=="massupdate_round_price_10"){
                    if (confirm('<?php echo _l('Are you sure that you want to round all these prices up?', 1); ?>')){
                        selection=prop_tb._combinationmultiproductGrid.getSelectedRowId();
                        if (selection!='' && selection!=null)
                        {
                            var column=prop_tb._combinationmultiproductGrid.getColumnId(lastColumnRightClicked_CombinationMultiProduct);
                            $.post('index.php?ajax=1&act=cat_combinationmultiproduct_massupdate<?php echo $massupdate_suffix; ?>&field=mass_round&todo=10&column='+column,{'combilist':selection},function(data){
                                displayCombinationMultiProduct();
                            });
                        }
                    }
                }
                if (itemId=="massupdate_round_price_help"){
                    window.open('<?php echo getScExternalLink('support_massupdate_round_price'); ?>','_blank');
                }
            }
            combinationmultiproduct_cmenu.attachEvent("onClick", onGridCombinationMultiProductContextButtonClick);
            var contextMenuXML='<menu absolutePosition="auto" mode="popup" maxItems="8"  globalCss="contextMenu" globalSecondCss="contextMenu" globalTextCss="contextMenuItem">'+
                    '<item text="Object" id="object" enabled="false"/>'+
                    '<item text="Attribute" id="attribute" enabled="false"/>'+
                    '<item text="<?php echo _l('Copy'); ?>" id="copy"/>'+
                    '<item text="<?php echo _l('Paste'); ?>" id="paste"/>'+
                    '<item text="<?php echo _l('Rounding the price up', 1); ?>" id="massupdate_round_price">'+
                        '<item text="<?php echo _l('X,00'); ?>" id="massupdate_round_price_1"/>'+
                        '<item text="<?php echo _l('X,X0'); ?>" id="massupdate_round_price_2"/>'+
                        '<item text="<?php echo _l('X,X0'); ?> <?php echo _l('or'); ?> <?php echo _l('X,X5'); ?>" id="massupdate_round_price_3"/>'+
                        '<item text="<?php echo _l('X,00'); ?> <?php echo _l('or'); ?> <?php echo _l('X,50'); ?>" id="massupdate_round_price_4"/>'+
                        '<item text="<?php echo _l('X,49'); ?> <?php echo _l('or'); ?> <?php echo _l('X,99'); ?>" id="massupdate_round_price_7"/>'+
                        '<item text="<?php echo _l('X,90'); ?>" id="massupdate_round_price_5"/>'+
                        '<item text="<?php echo _l('X,99'); ?>" id="massupdate_round_price_6"/>'+
                        '<item text="<?php echo _l('X9'); ?>" id="massupdate_round_price_8"/>'+
                        '<item text="<?php echo _l('X99'); ?>" id="massupdate_round_price_9"/>'+
                        '<item text="<?php echo _l('X,95'); ?> <?php echo _l('or'); ?> <?php echo _l('X,05'); ?>" id="massupdate_round_price_10"/>'+
                        '<item text="<?php echo _l('Help'); ?>" id="massupdate_round_price_help" img="fad fa-question-circle blue" imgdis="fad fa-question-circle blue"/>'+
                    '</item>'+
                '</menu>';
            combinationmultiproduct_cmenu.setIconset('awesome');
            combinationmultiproduct_cmenu.loadStruct(contextMenuXML);
            prop_tb._combinationmultiproductGrid.enableContextMenu(combinationmultiproduct_cmenu);

            prop_tb._combinationmultiproductGrid.attachEvent("onBeforeContextMenu", function(rowid,colidx,grid){
                var disableOnCols=new Array(
                        prop_tb._combinationmultiproductGrid.getColIndexById('id_product'),
                        prop_tb._combinationmultiproductGrid.getColIndexById('id_product_attribute'),
                        prop_tb._combinationmultiproductGrid.getColIndexById('combination_name'),
                        prop_tb._combinationmultiproductGrid.getColIndexById('pprice'),
                        prop_tb._combinationmultiproductGrid.getColIndexById('ppriceextax'),
                        prop_tb._combinationmultiproductGrid.getColIndexById('margin')
                        );
                if (in_array(colidx,disableOnCols))
                {
                    return false;
                }
                lastColumnRightClicked_CombinationMultiProduct=colidx;
                combinationmultiproduct_cmenu.setItemText('object', '<?php echo _l('Product:'); ?> '+prop_tb._combinationmultiproductGrid.cells(rowid,prop_tb._combinationmultiproductGrid.getColIndexById('id_product')).getValue());
                combinationmultiproduct_cmenu.setItemText('attribute', '<?php echo _l('Attribute:'); ?> '+prop_tb._combinationmultiproductGrid.cells(rowid,prop_tb._combinationmultiproductGrid.getColIndexById('combination_name')).getTitle());
                if (lastColumnRightClicked_CombinationMultiProduct==clipboardType_CombinationMultiProduct)
                {
                    combinationmultiproduct_cmenu.setItemEnabled('paste');
                }else{
                    combinationmultiproduct_cmenu.setItemDisabled('paste');
                }

                idxPrice=prop_tb._combinationmultiproductGrid.getColIndexById('priceextax');
                idxPriceInTax=prop_tb._combinationmultiproductGrid.getColIndexById('price');
                idxWholesalePrice=prop_tb._combinationmultiproductGrid.getColIndexById('wholesale_price');
                if(colidx==idxPrice || colidx==idxPriceInTax || colidx==idxWholesalePrice)
                {
                    combinationmultiproduct_cmenu.setItemEnabled('massupdate_round_price');
                    combinationmultiproduct_cmenu.setItemEnabled('massupdate_round_price_1');
                    combinationmultiproduct_cmenu.setItemEnabled('massupdate_round_price_2');
                    combinationmultiproduct_cmenu.setItemEnabled('massupdate_round_price_3');
                    combinationmultiproduct_cmenu.setItemEnabled('massupdate_round_price_4');
                    combinationmultiproduct_cmenu.setItemEnabled('massupdate_round_price_5');
                    combinationmultiproduct_cmenu.setItemEnabled('massupdate_round_price_6');
                    combinationmultiproduct_cmenu.setItemEnabled('massupdate_round_price_7');
                    combinationmultiproduct_cmenu.setItemEnabled('massupdate_round_price_8');
                    combinationmultiproduct_cmenu.setItemEnabled('massupdate_round_price_9');
                    combinationmultiproduct_cmenu.setItemEnabled('massupdate_round_price_help');
                    var title = "<?php echo _l('price'); ?>";
                    if(colidx==idxPrice)
                        title = "<?php echo _l('Price excl. Tax'); ?>";
                    else if(colidx==idxPriceInTax)
                        title = "<?php echo _l('Price incl. Tax'); ?>";
                    else if(colidx==idxWholesalePrice)
                        title = "<?php echo _l('Wholesale price'); ?>";
                    combinationmultiproduct_cmenu.setItemText('massupdate_round_price', '<?php echo _l('Rounding up the'); ?> '+title);
                }
                else
                {
                    combinationmultiproduct_cmenu.setItemDisabled('massupdate_round_price');
                    combinationmultiproduct_cmenu.setItemDisabled('massupdate_round_price_1');
                    combinationmultiproduct_cmenu.setItemDisabled('massupdate_round_price_2');
                    combinationmultiproduct_cmenu.setItemDisabled('massupdate_round_price_3');
                    combinationmultiproduct_cmenu.setItemDisabled('massupdate_round_price_4');
                    combinationmultiproduct_cmenu.setItemDisabled('massupdate_round_price_5');
                    combinationmultiproduct_cmenu.setItemDisabled('massupdate_round_price_6');
                    combinationmultiproduct_cmenu.setItemDisabled('massupdate_round_price_7');
                    combinationmultiproduct_cmenu.setItemDisabled('massupdate_round_price_8');
                    combinationmultiproduct_cmenu.setItemDisabled('massupdate_round_price_9');
                    combinationmultiproduct_cmenu.setItemDisabled('massupdate_round_price_help');
                    combinationmultiproduct_cmenu.setItemText('massupdate_round_price', '<?php echo _l('Rounding the price up', 1); ?>');
                }
                return true;
            });

            needInitCombinationMultiProduct=0;
        }
    }

    function setPropertiesPanel_combinationmultiproduct(id){
        if (id=='combinationmultiproduct')
        {
            displayCombinationMultiProductPanel();
        }
        if (id=='combinationmultiproduct_refresh')
        {
            displayCombinationMultiProduct();
        }
        if (id=='prop_combinationmultiproduct_grideditor'){
            openWinGridEditor('type_combinationmultiproduct');
        }
        if (id=='combinationmultiproduct_selectall')
        {
            prop_tb._combinationmultiproductGrid.enableSmartRendering(false);
            prop_tb._combinationmultiproductGrid.selectAll();
            propMultiCombiGridStat();
        }
        if(id=='combinationmultiproduct_refresh_physical_stocks'){
            $.post("index.php?ajax=1&act=cat_product_update&action=stocks&"+new Date().getTime(),{},function(){
                displayCombinationMultiProduct();
            });
        }
        if (id=='combinationmultiproduct_filters_reset')
        {
            if (lastProductSelID!=0)
            {
                for(var i=0, l=prop_tb._combinationmultiproductGrid.getColumnsNum();i<l;i++)
                {
                    if (prop_tb._combinationmultiproductGrid.getFilterElement(i)!=null) prop_tb._combinationmultiproductGrid.getFilterElement(i).value="";
                }
                prop_tb._combinationmultiproductGrid.filterByAll();
            }
            prop_tb.setListOptionSelected('combinationmultiproduct_filters','');
        }
        if (id=='combinationmultiproduct_filters_cols_show')
        {
            for(var i=0 , l=prop_tb._combinationmultiproductGrid.getColumnsNum() ; i < l ; i++)
            {
                prop_tb._combinationmultiproductGrid.setColumnHidden(i,false);
            }
            prop_tb.setListOptionSelected('combinationmultiproduct_filters','');
        }
        if (id=='combinationmultiproduct_filters_cols_hide')
        {
            idxCombiID=prop_tb._combinationmultiproductGrid.getColIndexById('id_product_attribute');
            idxCombiReference=prop_tb._combinationmultiproductGrid.getColIndexById('reference');
            for(i=0, l=prop_tb._combinationmultiproductGrid.getColumnsNum() ; i < l ; i++)
            {
                if (i!=idxCombiID && i!=idxCombiReference && (prop_tb._combinationmultiproductGrid.getColumnId(i).substr(0,4)!='attr'))
                {
                    prop_tb._combinationmultiproductGrid.setColumnHidden(i,true);
                }else{
                    prop_tb._combinationmultiproductGrid.setColumnHidden(i,false);
                }
            }
            prop_tb.setListOptionSelected('combinationmultiproduct_filters','');
        }
        if (id=='combinationmultiproduct_win_attributes')
        {
            if (!dhxWins.isWindow("wAttributes"))
            {
                wAttributes = dhxWins.createWindow("wAttributes", 50, 50, 900, $(window).height()-75);
                wAttributes.setText("<?php echo _l('Attributes and groups'); ?>");
                $.get("index.php?ajax=1&act=cat_win-attribute_init",function(data){
                        $('#jsExecute').html(data);
                    });
                wAttributes.attachEvent("onClose", function(win){
                        wAttributes.hide();
                        return false;
                    });
            }else{
                wAttributes.show();
            }
        }
        if (id=='combinationmultiproduct_exportcsv'){
            displayQuickExportWindow(prop_tb._combinationmultiproductGrid,1);
        }
        if (id=='combinationmultiproduct_del')
        {
            if (prop_tb._combinationmultiproductGrid.getSelectedRowId()==null)
            {
                alert('<?php echo _l('Please select a combination', 1); ?>');
            }else{
                if (lastProductSelID!=0)
                {
                    if (confirm('<?php echo _l('Are you sure you want to delete the selected items?', 1); ?>'))
                    {
                        var tmp_ids = prop_tb._combinationmultiproductGrid.getSelectedRowId();
                        var ids = new Array();
                        if(tmp_ids.search(",")>=0)
                            ids = tmp_ids.split(",");
                        else
                            ids[0] =    tmp_ids;
                        var nb = ids.length*1-1;
                        $.each(ids, function(ind, id){
                            var params = {
                                name: "cat_combinationmultiproduct_update_queue",
                                row: id,
                                action: "delete",
                                params: {},
                                callback: "displayCombinationMultiProduct('updateProductQuantity()');"
                            };
                            params.params["row_id"]=id;
                            params.params = JSON.stringify(params.params);
                            if(nb!=ind)
                                params.callback = "";
                            addInUpdateQueue(params,prop_tb._combinationmultiproductGrid);
                            });
                    }
                }else{
                    alert('<?php echo _l('Please select a product', 1); ?>');
                }
            }
        }
        if (id=='combinationmultiproduct_selectall')
        {
            prop_tb._combinationmultiproductGrid.enableSmartRendering(false);
            prop_tb._combinationmultiproductGrid.selectAll();
        }
        /*
         * Ref generator
        */
        if (prop_tb.getParentId(id) == 'combinationmultiproduct_reference_generator')
        {
            generateCombinationMRef(id);
        }
    }
    prop_tb.attachEvent("onClick", setPropertiesPanel_combinationmultiproduct);

    function displayCombinationMultiProduct(reloadJustChecbox)
    {
        prop_tb._combinationmultiproductGrid.clearAll(true);
        prop_tb._combinationmultiproductGrid.post("index.php?ajax=1&act=cat_combinationmultiproduct_get&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),"idlist="+cat_grid.getSelectedRowId(),function()
        {
            nb=prop_tb._combinationmultiproductGrid.getRowsNum();
            prop_tb._combinationmultiproductGrid._rowsNum=nb;

               // UISettings
            loadGridUISettings(prop_tb._combinationmultiproductGrid);
            prop_tb._combinationmultiproductGrid._first_loading=0;

            propMultiCombiGridStat();

            marginMatrix_form = prop_tb._combinationmultiproductGrid.getUserData("", "marginMatrix_form");
               prop_tb._combinationmultiproductGrid.forEachRow(function(id){
              calculMarginCombinationMultiProduct(id);
            });

            <?php sc_ext::readCustomCombinationMultiProductGridConfigXML('afterGetRows'); ?>
        });
    }



    let combinationmultiproduct_current_id = 0;
    cat_grid.attachEvent("onRowSelect",function (idproduct){
        if (propertiesPanel=='combinationmultiproduct' && !dhxLayout.cells('b').isCollapsed() && (cat_grid.getSelectedRowId()!==null && combinationmultiproduct_current_id!=idproduct)){
            lastProductSelID = idproduct;
            var list_products = cat_grid.getSelectedRowId();
            var count_products = list_products.split(',');
            if(count_products.length > 1 ) {
                displayCombinationMultiProduct();
            } else {
                prop_tb.callEvent("onClick", ["combinations"]);
            }
            combinationmultiproduct_current_id=idproduct;
        }
    });

    function propMultiCombiGridStat()
    {
        let total_nb = prop_tb._combinationmultiproductGrid.getRowsNum();
        let total_selected = (prop_tb._combinationmultiproductGrid.getSelectedRowId()?prop_tb._combinationmultiproductGrid.getSelectedRowId().split(',').length:0);
        prop_tb._sb.setText(total_nb+(total_nb>1?" <?php echo _l('combinations'); ?>":" <?php echo _l('combination'); ?>")+" - <?php echo _l('Selection')._l(':'); ?> "+total_selected);
    }

    function combimulprdWriteRefresh()
    {
        idxQty=prop_tb._combinationmultiproductGrid.getColIndexById('quantity');
        idxQtyUpdate=prop_tb._combinationmultiproductGrid.getColIndexById('quantityupdate');
        idxQtyUse=prop_tb._combinationmultiproductGrid.getColIndexById('quantity_usable');
        idxQtyPhy=prop_tb._combinationmultiproductGrid.getColIndexById('quantity_physical');
        idxQtyRea=prop_tb._combinationmultiproductGrid.getColIndexById('quantity_real');

        var ids = prop_tb._combinationmultiproductGrid.getSelectedRowId().split(',');
        $.each(ids, function(num, rId) {
            if (idxQty){
                prop_tb._combinationmultiproductGrid.setCellExcellType(rId,idxQty,"ro");
                prop_tb._combinationmultiproductGrid.cells(rId,idxQty).setValue('<?php echo _l('Refresh', 1); ?>');
            }

            if (idxQtyUpdate){
                prop_tb._combinationmultiproductGrid.setCellExcellType(rId,idxQtyUpdate,"ro");
                prop_tb._combinationmultiproductGrid.cells(rId,idxQtyUpdate).setValue('<?php echo _l('Refresh', 1); ?>');
            }

            if (idxQtyUse){
                prop_tb._combinationmultiproductGrid.setCellExcellType(rId,idxQtyUse,"ro");
                prop_tb._combinationmultiproductGrid.cells(rId,idxQtyUse).setValue('<?php echo _l('Refresh', 1); ?>');
            }

            if (idxQtyPhy){
                prop_tb._combinationmultiproductGrid.setCellExcellType(rId,idxQtyPhy,"ro");
                prop_tb._combinationmultiproductGrid.cells(rId,idxQtyPhy).setValue('<?php echo _l('Refresh', 1); ?>');
            }

            if (idxQtyRea){
                prop_tb._combinationmultiproductGrid.setCellExcellType(rId,idxQtyRea,"ro");
                prop_tb._combinationmultiproductGrid.cells(rId,idxQtyRea).setValue('<?php echo _l('Refresh', 1); ?>');
            }

        });
    }

    function addCombinationMInQueue(rId, action, cIn)
    {
        var params = {
            name: "cat_combinationmultiproduct_update_queue",
            row: rId,
            action: "update",
            params: {},
            callback: "callbackCombinationM('"+rId+"','update','"+rId+"');"
        };
        // COLUMN VALUES
            params.params["id_lang"] = SC_ID_LANG;
            params.params[prop_tb._combinationmultiproductGrid.getColumnId(cIn)] = prop_tb._combinationmultiproductGrid.cells(rId,cIn).getValue();

            cInPPriceHT = prop_tb._combinationmultiproductGrid.getColIndexById('ppriceextax');
            params.params['ppriceextax'] = prop_tb._combinationmultiproductGrid.cells(rId,cInPPriceHT).getValue();
        // USER DATA
            if(rId!=undefined && rId!=null && rId!="" && rId!=0)
            {
                if(prop_tb._combinationmultiproductGrid.UserData[rId]!=undefined && prop_tb._combinationmultiproductGrid.UserData[rId]!=null && prop_tb._combinationmultiproductGrid.UserData[rId]!="")
                {
                    $.each(prop_tb._combinationmultiproductGrid.UserData[rId].keys, function(i, key){
                        params.params[key] = prop_tb._combinationmultiproductGrid.UserData[rId].values[i];
                    });
                }
            }
            if(prop_tb._combinationmultiproductGrid.UserData.gridglobaluserdata.keys!=undefined && prop_tb._combinationmultiproductGrid.UserData.gridglobaluserdata.keys!=null && prop_tb._combinationmultiproductGrid.UserData.gridglobaluserdata.keys!="")
            {
                $.each(prop_tb._combinationmultiproductGrid.UserData.gridglobaluserdata.keys, function(i, key){
                    params.params[key] = prop_tb._combinationmultiproductGrid.UserData.gridglobaluserdata.values[i];
                });
            }

        params.params = JSON.stringify(params.params);
        addInUpdateQueue(params,prop_tb._combinationmultiproductGrid);
    }

    function hideCombinationMultiProduct_SubpropertiesItems()
    {
        prop_tb.combimulprd_subproperties_tb.forEachItem(function(itemId){
            if(itemId!="combimulprdSubProperties") {
                prop_tb.combimulprd_subproperties_tb.hideItem(itemId);
            }
        });
    }

    function generateCombinationMRef(id)
    {
        if (confirm('<?php echo _l('Are you sure you want to do this action ?', 1); ?>'))
        {
            var idxProductID=prop_tb._combinationmultiproductGrid.getColIndexById('id_product');
            var idxProductAttributeName=prop_tb._combinationmultiproductGrid.getColIndexById('combination_name');
            var idxProductAttributeRef=prop_tb._combinationmultiproductGrid.getColIndexById('reference');
            var id_auto = 0;
            var attr_cId_arr = [];
            prop_tb._combinationmultiproductGrid.columnIds.forEach(function(item,cId){
                if (item.indexOf('attr_')!=-1)
                {
                    attr_cId_arr.push(cId);
                }
            });

            var previous_id_product = Number(0);
            let ref_separator = '<?php echo _s('CAT_PROPERTIES_COMBINATION_AUTO_REF_SEPARATOR'); ?>';
            prop_tb._combinationmultiproductGrid.forEachRow(function(rId)
            {
                var product_ref=prop_tb._combinationmultiproductGrid.getUserData(rId,'reference_product');
                var ids = rId.split('_');
                var id_product = Number(ids[0]);
                if(previous_id_product !== id_product) {
                    id_auto = 0;
                }
                var id_product_attribute = ids[1];
                if(product_ref == '') {
                    product_ref = id_product;
                }
                switch(id){
                    case 'orm_delete':
                        onEditCellCombinationMultiProduct(2,rId,idxProductAttributeRef,'',null);
                        break;
                    case 'orm_prodref_auto':
                        var new_ref = String(product_ref+ref_separator+id_auto);
                        checkCombinationMultiProductDuplicateRefBeforeSave(rId,idxProductAttributeRef,new_ref);
                        break;
                    case 'orm_prodref_attrid':
                        var new_ref = String(product_ref+ref_separator+id_product_attribute);
                        checkCombinationMultiProductDuplicateRefBeforeSave(rId,idxProductAttributeRef,new_ref);
                        break;
                    case 'orm_prodref_auto_name':
                        var attr_name=prop_tb._combinationmultiproductGrid.getUserData(rId,'attr_name');
                        var new_ref = String(product_ref+ref_separator+id_auto+ref_separator+attr_name);
                        checkCombinationMultiProductDuplicateRefBeforeSave(rId,idxProductAttributeRef,new_ref);
                        break;
                }
                id_auto++;
                previous_id_product = id_product;
            });
        } else {
            return false;
        }
    }

    function displayCombinationMultiProductPanel()
    {
        if(lastProductSelID!=undefined && lastProductSelID!="")
        {
            dhxLayout.cells('b').setText('<?php echo _l('Properties', 1).' '._l('of', 1); ?> '+getSelectedItemValueOrID(cat_grid,lastProductSelID,'name'));
        }
        hidePropTBButtons();
        prop_tb.showItem("combinationmultiproduct_filters");
        prop_tb.showItem("combinationmultiproduct_refresh");
        prop_tb.showItem("combinationmultiproduct_win_attributes");
        prop_tb.showItem("combinationmultiproduct_exportcsv");
        prop_tb.showItem("combinationmultiproduct_del");
        prop_tb.showItem("combinationmultiproduct_selectall");
        prop_tb.showItem("combinationmultiproduct_reference_generator");
        prop_tb.showItem('prop_combinationmultiproduct_settings_menu');
        <?php if(version_compare(_PS_VERSION_, '1.7.2.0', '>=')){ ?>
            prop_tb.showItem('combinationmultiproduct_refresh_physical_stocks');
        <?php } ?>
        prop_tb.setItemText('panel', '<?php echo $title; ?>');
        prop_tb.setItemImage('panel', '<?php echo $icon; ?>');
        needInitCombinationMultiProduct = 1;
        is_prop_4columns = true;
        initCombinationMultiProduct();
        needInitCombinationMultiProductImage=1;
        initCombinationMultiProductImage();
        propertiesPanel='combinationmultiproduct';
        if (lastProductSelID!=0)
        {
            displayCombinationMultiProduct();
        }
    }

    function checkCombinationMultiProductDuplicateRefBeforeSave(rId,cId,reference)
    {
        $.post('index.php?ajax=1&act=cat_combinationmultiproduct_get&'+new Date().getTime(),{'reference_check':1,'reference':reference,'ids':rId},function(data){
            if(data == 'OK') {
                onEditCellCombinationMultiProduct(2,rId,cId,reference,null);
            } else {
                dhtmlx.message({text:data,type:'error'});
            }
        });
    }

    // CALLBACK FUNCTION
    function callbackCombinationM(sid,action,tid)
    {
        <?php sc_ext::readCustomCombinationMultiProductGridConfigXML('onAfterUpdate'); ?>
        if (action=='update')
        {
            prop_tb._combinationmultiproductGrid.setRowTextNormal(sid);
        }
    }