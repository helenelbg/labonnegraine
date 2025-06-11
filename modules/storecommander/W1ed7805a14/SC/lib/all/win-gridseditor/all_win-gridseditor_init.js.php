<?php $extConvert = new ExtensionConvert();
$extConvert->convert('products');
$extConvert->convert('combinations');
$extConvert->convert('customers');
$extConvert->convert('orders');
?><?php echo '<script type="text/javascript">'; ?>
    var type_selected = "<?php echo Tools::getValue('defaultSelection', 'type_products'); ?>";
    var grid_selected = "";
    
    function replaceAll(find, replace, str) {
      return str.replace(new RegExp(find, 'g'), replace);
    }

    dhxlSCGridsEditor=toolsSCGridsEditor.attachLayout("3W");

    // Colonne des grilles
        dhxlSCGridsEditor.cells('a').setText("<?php echo _l('Grids'); ?>");
        dhxlSCGridsEditor.cells('a').setWidth(401);
    
        dhxlSCGridsEditor.tbGrids=dhxlSCGridsEditor.cells('a').attachToolbar();
        dhxlSCGridsEditor.tbGrids.setIconset('awesome');
        dhxlSCGridsEditor.tbGrids.addButton("delete", 0, "", "fa fa-minus-circle red", "fa fa-minus-circle red");
        dhxlSCGridsEditor.tbGrids.setItemToolTip('delete','<?php echo _l('Reset view/grid', 1); ?>');
        dhxlSCGridsEditor.tbGrids.addButton("copy", 0, "", "fa fa-copy blue", "fa fa-copy blue");
        dhxlSCGridsEditor.tbGrids.setItemToolTip('copy','<?php echo _l('Duplicate grid', 1); ?>');
        dhxlSCGridsEditor.tbGrids.addButton("add", 0, "", "fa fa-plus-circle green", "fa fa-plus-circle green");
        dhxlSCGridsEditor.tbGrids.setItemToolTip('add','<?php echo _l('Add new grid', 1); ?>');
        var opts = [['type_products', 'obj', '<?php echo _l('Catalog:', 1); ?> <?php echo _l('Products', 1); ?>', ''],
                    ['type_combinations', 'obj', '<?php echo _l('Catalog:', 1); ?> <?php echo _l('Combinations', 1); ?>', ''],
                    ['type_combinationmultiproduct', 'obj', '<?php echo _l('Catalog:', 1); ?> <?php echo _l('Multi-product combinations', 1); ?>', ''],
                    ['type_productsort', 'obj', '<?php echo _l('Catalog:', 1); ?> <?php echo _l('Products positions', 1); ?>', ''],
                    ['type_msproduct', 'obj', '<?php echo _l('Catalog:', 1); ?> <?php echo _l('MS - products information', 1); ?>', ''],
                    ['type_mscombination', 'obj', '<?php echo _l('Catalog:', 1); ?> <?php echo _l('MS - combinations', 1); ?>', ''],
                    ['type_image', 'obj', '<?php echo _l('Catalog:', 1); ?> <?php echo _l('Product images', 1); ?>', ''],
                    ['type_propsupplier', 'obj', '<?php echo _l('Catalog:', 1); ?> <?php echo _l('Suppliers', 1); ?>', ''],
                    ['type_propspeprice', 'obj', '<?php echo _l('Catalog:', 1); ?> <?php echo _l('Properties - specific prices', 1); ?>', ''],
                    ['type_propcustomers', 'obj', '<?php echo _l('Catalog:', 1); ?> <?php echo _l('Properties').' - '._l('customers', 1); ?>', ''],
                    ['type_winspeprice', 'obj', '<?php echo _l('Catalog:', 1); ?> <?php echo _l('Window - specific prices', 1); ?>', ''],
                    ['type_proppackproduct', 'obj', '<?php echo _l('Catalog:', 1); ?> <?php echo _l('Properties').' - '._l('Pack', 1); ?>', ''],
                    ['type_customers', 'obj', '<?php echo _l('Customers:', 1); ?> <?php echo _l('Customers', 1); ?>', ''],
                    ['type_orders', 'obj', '<?php echo _l('Orders:', 1); ?> <?php echo _l('Orders', 1); ?>', ''],
                    ['type_order_product', 'obj', '<?php echo _l('Orders:', 1); ?> <?php echo _l('Products', 1); ?>', ''],
                    ['type_cms', 'obj', '<?php echo _l('CMS:', 1); ?> <?php echo _l('CMS'); ?>', ''],
                    ['type_gmapartner', 'obj', '<?php echo _l('Addons:', 1); ?> <?php echo _l('Affiliation program', 1); ?> - <?php echo _l('Partners'); ?>', '']
        ];
        dhxlSCGridsEditor.tbGrids.addButtonSelect("type", 0, "<?php echo _l('Catalog:'); ?> <?php echo _l('Products'); ?>", opts, "fad fa-external-link green", "fad fa-external-link green",false,true);
        dhxlSCGridsEditor.tbGrids.setItemToolTip('type','<?php echo _l('Grid type', 1); ?>');
        dhxlSCGridsEditor.tbGrids.attachEvent("onClick",
            function(id){
            if (id=='type_products')
            {
                dhxlSCGridsEditor.gridFields.clearAll(true);
                type_selected = "type_products";
                displayGrids();
                displayEnableFields();
            }
            else if (id=='type_combinations')
            {
                dhxlSCGridsEditor.gridFields.clearAll(true);
                type_selected = "type_combinations";
                displayGrids();
                displayEnableFields();
            }
            else if (id=='type_combinationmultiproduct')
            {
                dhxlSCGridsEditor.gridFields.clearAll(true);
                type_selected = "type_combinationmultiproduct";
                displayGrids();
                displayEnableFields();
            }
            else if (id=='type_customers')
            {
                dhxlSCGridsEditor.gridFields.clearAll(true);
                type_selected = "type_customers";
                displayGrids();
                displayEnableFields();
            }
            else if (id=='type_cms')
            {
                dhxlSCGridsEditor.gridFields.clearAll(true);
                type_selected = "type_cms";
                displayGrids();
                displayEnableFields();
            }
            else if (id=='type_orders')
            {
                dhxlSCGridsEditor.gridFields.clearAll(true);
                type_selected = "type_orders";
                displayGrids();
                displayEnableFields();
            }
            else if (id=='type_order_product')
            {
                dhxlSCGridsEditor.gridFields.clearAll(true);
                type_selected = "type_order_product";
                displayGrids();
                displayEnableFields();
            }
            else if (id=='type_productsort')
            {
                dhxlSCGridsEditor.gridFields.clearAll(true);
                type_selected = "type_productsort";
                displayGrids();
                displayEnableFields();
            }
            else if (id=='type_msproduct')
            {
                dhxlSCGridsEditor.gridFields.clearAll(true);
                type_selected = "type_msproduct";
                displayGrids();
                displayEnableFields();
            }
            else if (id=='type_mscombination')
            {
                dhxlSCGridsEditor.gridFields.clearAll(true);
                type_selected = "type_mscombination";
                displayGrids();
                displayEnableFields();
            }
            else if (id=='type_image')
            {
                dhxlSCGridsEditor.gridFields.clearAll(true);
                type_selected = "type_image";
                displayGrids();
                displayEnableFields();
            }
            else if (id=='type_propspeprice')
            {
                dhxlSCGridsEditor.gridFields.clearAll(true);
                type_selected = "type_propspeprice";
                displayGrids();
                displayEnableFields();
            }
            else if (id=='type_winspeprice')
            {
                dhxlSCGridsEditor.gridFields.clearAll(true);
                type_selected = "type_winspeprice";
                displayGrids();
                displayEnableFields();
            }
            else if (id=='type_proppackproduct')
            {
                dhxlSCGridsEditor.gridFields.clearAll(true);
                dhxlSCGridsEditor.tbGrids.setListOptionSelected('type','type_proppackproduct');
                dhxlSCGridsEditor.tbGrids.setItemText('type', "<?php echo _l('Catalog:'); ?> <?php echo _l('Properties').' - '._l('Packs'); ?>");
                type_selected = "type_proppackproduct";
                displayGrids();
                displayEnableFields();
            }
            else if (id=='type_propsupplier')
            {
                dhxlSCGridsEditor.gridFields.clearAll(true);
                type_selected = "type_propsupplier";
                displayGrids();
                displayEnableFields();
            }
            else if (id=='type_gmapartner')
            {
                dhxlSCGridsEditor.gridFields.clearAll(true);
                type_selected = "type_gmapartner";
                displayGrids();
                displayEnableFields();
            }else if (id=='type_propcustomers')
            {
                dhxlSCGridsEditor.gridFields.clearAll(true);
                dhxlSCGridsEditor.tbGrids.setListOptionSelected('type','type_propcustomers');
                dhxlSCGridsEditor.tbGrids.setItemText('type', '<?php echo _l('Catalog:', 1); ?> <?php echo _l('Properties').' - '._l('customers', 1); ?>');
                type_selected = "type_propcustomers";
                displayGrids();
                displayEnableFields();
            }
            else if (id=='add')
            {
                let forbidden_type = [
                    "type_combinations",
                    "type_combinationmultiproduct",
                    "type_productsort",
                    "type_msproduct",
                    "type_mscombination",
                    "type_images",
                    "type_propspeprice",
                    "type_winspeprice",
                    "type_proppackproduct",
                    "type_propsupplier",
                    "type_gmapartner"
                ];
                if(forbidden_type.includes(type_selected) === false)
                {
                    let name = prompt('<?php echo _l('Please enter a name', 1); ?>',"custom");
                    if (name!==undefined && name!==null && name!=="")
                    {
                        $.post("index.php?ajax=1&act=all_win-gridseditor_update&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{"action":"insert","type":type_selected,"name":name},function(data){
                            displayGrids();
                            dhtmlx.message({text:'<?php echo _l('You need to refresh Store Commander to use the new settings.', 1); ?>',type:'error'});
                        });
                    }
                }
                else
                {
                    dhtmlx.message({text:'<?php echo _l('You can\'t do this action for this grid', 1); ?>',type:'error',expire:5000});
                }
                
            }
            else if (id=='delete')
            {
                if(confirm('<?php echo _l('Are you sure that you want to delete/reset this view/grid?', 1); ?>'))
                {
                
                    var name = dhxlSCGridsEditor.gridGrids.getSelectedRowId();
                    var is_default = dhxlSCGridsEditor.gridGrids.getUserData(name,"is_default");
    
                    if (name!=undefined && name!=null && name!="" && is_default!=1)
                    {
                        $.post("index.php?ajax=1&act=all_win-gridseditor_update&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{"action":"delete","type":type_selected,"name":name},function(data){
                            displayGrids();
                        });
                    }
                    else if (name!=undefined && name!=null && name!="" && is_default==1)
                    {
                        dhtmlx.message({text:'<?php echo _l('You can\'t remove this grid because it\'s a default grid', 1); ?>',type:'error',expire:5000});
                    }
                }
                
            }
            else if (id=='copy')
            {
                let forbidden_type = [
                    "type_combinations",
                    "type_combinationmultiproduct",
                    "type_productsort",
                    "type_msproduct",
                    "type_mscombination",
                    "type_images",
                    "type_propspeprice",
                    "type_winspeprice",
                    "type_proppackproduct",
                    "type_propsupplier",
                    "type_gmapartner"
                ];
                if(forbidden_type.includes(type_selected) === false)
                {
                    var duplicate = dhxlSCGridsEditor.gridGrids.getSelectedRowId();
                    var is_default = dhxlSCGridsEditor.gridGrids.getUserData(duplicate,"is_default");
                    var name = prompt('<?php echo _l('Please enter a name', 1); ?>',"custom");
    
                    if (duplicate!=undefined && duplicate!=null && duplicate!="" && name!=undefined && name!=null && name!="")
                    {
                        $.post("index.php?ajax=1&act=all_win-gridseditor_update&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{"action":"dupplicate","type":type_selected,"name":name,"dupplicate":duplicate,"is_default":is_default},function(data){
                            displayGrids();
                        });
                    }
                }
                else
                {
                    dhtmlx.message({text:'<?php echo _l('You can\'t do this action for this grid', 1); ?>',type:'error',expire:5000});
                }
            }
        });
    
    
        dhxlSCGridsEditor.gridGrids=dhxlSCGridsEditor.cells('a').attachGrid();
        dhxlSCGridsEditor.gridGrids.setImagePath("lib/js/imgs/");
        dhxlSCGridsEditor.gridGrids.enableSmartRendering(false);
        dhxlSCGridsEditor.gridGrids.enableMultiselect(false);

        dhxlSCGridsEditor.gridGrids.attachEvent("onRowSelect", function(id,ind){
            grid_selected=id;
            displayFields();
        });
        dhxlSCGridsEditor.gridGrids.attachEvent("onClick", function(id,ind){
            grid_selected=id;
            displayFields();
        });
        dhxlSCGridsEditor.gridGrids.attachEvent("onEditCell", function(stage,rId,cInd,nValue,oValue){
            idxName=dhxlSCGridsEditor.gridGrids.getColIndexById('name');
            if(cInd==idxName && stage==2 && nValue!=oValue)
            {
                var name = rId;
                var is_default = dhxlSCGridsEditor.gridGrids.getUserData(name,"is_default");
                if (name!=undefined && name!=null && name!="")
                {
                    $.post("index.php?ajax=1&act=all_win-gridseditor_update&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{"action":"update","type":type_selected,"name":name,"newvalue":nValue,"is_default":is_default},function(data){
                        displayGrids();
                    });
                }
            }
            return true;
        });

        dhxlSCGridsEditor.gridGrids.attachEvent("onRowDblClicked", function(rId,cInd){
            idxName=dhxlSCGridsEditor.gridGrids.getColIndexById('name');
            var name_is_editable = dhxlSCGridsEditor.gridGrids.getUserData(rId,"name_is_editable");
            if(cInd==idxName && name_is_editable == 0){
                dhtmlx.message({text:'<?php echo _l('The view name cannot be edited', 1); ?>',type:'error'});
                return false;
            }
            return true;
        });

        displayGrids();
        
        // Colonne des champs
            dhxlSCGridsEditor.cells('b').setText("<?php echo _l('Fields in selected grid'); ?>");
        
            dhxlSCGridsEditor.tbfields=dhxlSCGridsEditor.cells('b').attachToolbar();
            dhxlSCGridsEditor.tbfields.setIconset('awesome');
            dhxlSCGridsEditor.tbfields.addButton("save_position", 0, "", "fa fa-list-ol green", "fa fa-list-ol green");
            dhxlSCGridsEditor.tbfields.setItemToolTip('save_position','<?php echo _l('Save position', 1); ?>');
            dhxlSCGridsEditor.tbfields.addButton("delete", 0, "", "fa fa-minus-circle red", "fa fa-minus-circle red");
            dhxlSCGridsEditor.tbfields.setItemToolTip('delete','<?php echo _l('Remove field', 1); ?>');
            dhxlSCGridsEditor.tbfields.attachEvent("onClick",
                function(id){
                    if (id=='save_position')
                    {
                        var list_rows = "";
                        dhxlSCGridsEditor.gridFields.forEachRow(function(id){
                            if(list_rows!="")
                                list_rows = list_rows+";";
                            list_rows = list_rows+id+','+dhxlSCGridsEditor.gridFields.getRowIndex(id);
                        });
                        $.post("index.php?ajax=1&act=all_win-gridseditor_fields_update&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{"action":"update_position","type":type_selected,"grid_select":grid_selected,"newvalue":list_rows},function(data){
                            if(type_selected=="type_propspeprice")
                                dhtmlx.message({text:'<?php echo _l('You need to refresh Store Commander to use the new settings.', 1); ?>',type:'error'});
                        });
                    }
                    if (id=='delete')
                    {
                        if(confirm('<?php echo _l('Are you sure that you want to remove this field?', 1); ?>'))
                        {
                            let fields_id = dhxlSCGridsEditor.gridFields.getSelectedRowId();
                            if (fields_id !== null)
                            {
                                var dependencies = {};
                                if(type_selected === "type_products")
                                {
                                    dependencies['id_tax_rules_group,price_inc_tax']='margin,id_tax_rules_group,price_inc_tax';
                                    dependencies['reduction_price,price_with_reduction,reduction_percent,price_with_reduction_percent,reduction_from,reduction_to']='reduction_price,price_with_reduction,reduction_percent,price_with_reduction_percent,reduction_from,reduction_to';
                                <?php
                                if (SCAS)
                                {
                                    echo "dependencies['quantity,advanced_stock_management,quantity_physical']='quantity,advanced_stock_management,quantity_physical,quantity_usable,quantity_real';";
                                }
                                ?>
                                }
                                
                                let final_ids = [];
                                let compulsory_ids = [];
                                for(const id_field of fields_id.split(",")) {
                                    if(Number(dhxlSCGridsEditor.gridFields.getUserData(id_field,"compulsory")) === 1)
                                    {
                                        compulsory_ids.push(id_field);
                                        continue;
                                    }
                                    $.each(dependencies, function(key, element){
                                        let list_id_with_dep = key.split(',');
                                        let associated_list_id = element.split(',');
                                        if(list_id_with_dep.includes(id_field))
                                        {
                                            if(confirm(id_field+'<?php echo _l(': Associated fields will also be removed:', 1); ?> '+associated_list_id.join(', ')))
                                            {
                                                final_ids.push(id_field);
                                                for(const id_associated_field of associated_list_id) {
                                                    if(!final_ids.includes(id_associated_field)) {
                                                        final_ids.push(id_associated_field);
                                                    }
                                                    dhxlSCGridsEditor.gridFields.selectRowById(id_associated_field, true);
                                                }
                                            } else {
                                                return false;
                                            }
                                        }
                                    });
                                }

                                if(final_ids.length > 0) {
                                    fields_id = final_ids;
                                }

                                if(compulsory_ids.length > 0) {
                                    dhtmlx.message({text:'<?php echo _l('Some fields can\'t be removed.', 1); ?> : '+compulsory_ids.join(', '),type:'error'});
                                    return false;
                                }

                                if(fields_id.length > 0) {
                                    $.post("index.php?ajax=1&act=all_win-gridseditor_fields_update&id_lang=" + SC_ID_LANG + "&" + new Date().getTime(), {"action": "delete", "type": type_selected, "grid_select": grid_selected, "ids": fields_id}, function (data) {
                                        dhxlSCGridsEditor.gridFields.deleteSelectedRows();
                                        displayEnableFields();
                                        if (type_selected === "type_propspeprice") {
                                            dhtmlx.message({text: '<?php echo _l('You need to refresh Store Commander to use the new settings.', 1); ?>', type: 'error'});
                                        }
                                    });
                                }
                            }
                        }
                    }
                });
        
        
            dhxlSCGridsEditor.gridFields=dhxlSCGridsEditor.cells('b').attachGrid();
            dhxlSCGridsEditor.gridFields._name = "gridFields";
            dhxlSCGridsEditor.gridFields.setImagePath("lib/js/imgs/");
            dhxlSCGridsEditor.gridFields.enableSmartRendering(false);
            dhxlSCGridsEditor.gridFields.enableMultiselect(true);
            dhxlSCGridsEditor.gridFields.enableDragAndDrop(true);
            
            dhxlSCGridsEditor.gridFields.attachEvent("onEditCell", function(stage,rId,cInd,nValue,oValue){
                if(stage==0 && rId=="ATTR")
                {
                    return false;
                }
                else
                {
                    idxText=dhxlSCGridsEditor.gridFields.getColIndexById('text');
                    idxAlign=dhxlSCGridsEditor.gridFields.getColIndexById('align');
                    idxSort=dhxlSCGridsEditor.gridFields.getColIndexById('sort');
                    idxWidth=dhxlSCGridsEditor.gridFields.getColIndexById('width');
                    idxColor=dhxlSCGridsEditor.gridFields.getColIndexById('color');
                    idxFilter=dhxlSCGridsEditor.gridFields.getColIndexById('filter');
                    idxType=dhxlSCGridsEditor.gridFields.getColIndexById('type');
                    idxFooter=dhxlSCGridsEditor.gridFields.getColIndexById('footer');
                    if(stage==2 && nValue!=oValue)
                    {
                        var value_field = "";
                        if(cInd==idxText)
                            value_field = "text";
                        else if(cInd==idxAlign)
                            value_field = "align";
                        else if(cInd==idxSort)
                            value_field = "sort";
                        else if(cInd==idxWidth)
                            value_field = "width";
                        else if(cInd==idxColor)
                            value_field = "color";
                        else if(cInd==idxFilter)
                            value_field = "filter";
                        else if(cInd==idxType)
                            value_field = "type";
                        else if(cInd==idxFooter)
                            value_field = "footer";
    
                        if(value_field=="width" && nValue>500)
                            dhxlSCGridsEditor.gridFields.cells(rId,cInd).setValue(500);
                        else if(value_field=="width" && nValue<40)
                            dhxlSCGridsEditor.gridFields.cells(rId,cInd).setValue(40);
                        else if(value_field=="width" && (nValue==undefined || nValue==null || nValue=="" || nValue==0))
                            dhxlSCGridsEditor.gridFields.cells(rId,cInd).setValue(100);
                        
                        if (grid_selected!=undefined && grid_selected!=null && grid_selected!="" && value_field!="")
                        {
                            $.post("index.php?ajax=1&act=all_win-gridseditor_fields_update&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{"action":"update","type":type_selected,"grid_select":grid_selected,"field":rId,"value":value_field,"newvalue":nValue},function(data){
                                displayEnableFields();
                            });
                        }
                    }
                    return true;
                }
                return false;
            });
        
            dhxlSCGridsEditor.gridFields.attachEvent("onRowSelect", function(id,ind){
            });
            
        // Colonne de tous les champs dispo
            dhxlSCGridsEditor.cells('c').setText("<?php echo _l('Available fields'); ?>");
            dhxlSCGridsEditor.cells('c').setWidth(300);
        
            dhxlSCGridsEditor.tbEnableFields=dhxlSCGridsEditor.cells('c').attachToolbar();
            dhxlSCGridsEditor.tbEnableFields.setIconset('awesome');
            dhxlSCGridsEditor.tbEnableFields.addButton("reset", 0, "", "fa fa-minus-circle red", "fa fa-minus-circle red");
            dhxlSCGridsEditor.tbEnableFields.setItemToolTip('reset','<?php echo _l('Reset field', 1); ?>');
            dhxlSCGridsEditor.tbEnableFields.addButton("add", 0, "", "fa fa-plus-circle green", "fa fa-plus-circle green");
            dhxlSCGridsEditor.tbEnableFields.setItemToolTip('add','<?php echo _l('Add new field', 1); ?>');
            dhxlSCGridsEditor.tbEnableFields.attachEvent("onClick",
                    function(id){
                        if (id=='add')
                        {
                            <?php if (SC_GRIDSEDITOR_PRO_INSTALLED && (!defined('SC_DEMO') || !SC_DEMO)) { ?>
                                $.get("index.php?ajax=1&act=all_win-gridseditorpro_win&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),function(data){
                                    $('#jsExecute').html(data);
                                });
                            <?php }
                                else
                                { ?>
                                    window.open('<?php echo getScExternalLink('interface_customization_pro'); ?>');
                            <?php } ?>
                        }
                        if (id=='reset')
                        {
                            var ids = dhxlSCGridsEditor.gridEnableFields.getSelectedRowId();
                            if(ids!=undefined && ids!=null && ids!="")
                            {
                                $.post("index.php?ajax=1&act=all_win-gridseditor_fields_update&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{"action":"delete_fields","type":type_selected,"grid_select":grid_selected,"ids":ids},function(data){
                                    displayFields();
                                   });
                            }
                        }
                    });
        
        
            dhxlSCGridsEditor.gridEnableFields=dhxlSCGridsEditor.cells('c').attachGrid();
            dhxlSCGridsEditor.gridEnableFields._name = "gridEnableFields";
            dhxlSCGridsEditor.gridEnableFields.setImagePath("lib/js/imgs/");
            dhxlSCGridsEditor.gridEnableFields.enableSmartRendering(false);
            dhxlSCGridsEditor.gridEnableFields.enableMultiselect(true);
            dhxlSCGridsEditor.gridEnableFields.enableDragAndDrop(true);
        
            dhxlSCGridsEditor.gridEnableFields.attachEvent("onRowSelect", function(id,ind){
            });
            
            displayEnableFields();

            dhxlSCGridsEditor.gridEnableFields.attachEvent("onBeforeDrag", function(id){
                if(dhxlSCGridsEditor.gridEnableFields.getUserData(id,"is_used")==1)
                {
                    return false;
                }
                return true;
            });

            dhxlSCGridsEditor.gridFields.attachEvent("onBeforeDrag", function(id){
                if(dhxlSCGridsEditor.gridFields.getUserData(id,"notMove")==1)
                {
                    return false;
                }
                return true;
            });
            dhxlSCGridsEditor.gridFields.attachEvent("onDrag", function(sId,tId,sObj,tObj,sInd,tInd){
                if(sObj._name=="gridEnableFields" && tObj._name=="gridFields") dhxlSCGridsEditor.gridFields.dragContext.mode="copy";
                return true;
            });
            dhxlSCGridsEditor.gridFields.attachEvent("onDrop", function(sId,tId,dId,sObj,tObj,sCol,tCol){
                if(sObj._name=="gridEnableFields" && tObj._name=="gridFields")
                {
                    // IS GROUP
                    if(dId.indexOf("gp_")>=0)
                    {
                        var ids = dId.split(",");
                        var nb = ids.length;
                        for(var i=0;i<nb;i++)
                        {
                            var id = ids[i];
                            if(id.indexOf("gp_")>=0)
                            {
                                var exp = id.split(":");
                                if(exp[1]!=undefined && exp[1]!="" && exp[1]!=null)
                                {
                                    var index_group_row = dhxlSCGridsEditor.gridFields.getRowIndex(id);
                                    var list_rows = "";
                                    var fields = exp[1].split("+");
                                    var nb_fields = exp[1].length;
                                    for(var j=(nb_fields*1-1);j--;)
                                    {
                                        var field_to_add = fields[j];
                                        if(field_to_add!=undefined && field_to_add!="" && field_to_add!=null)
                                        {
                                            var index_to_add = index_group_row*1;
    
                                            var all_ids = dhxlSCGridsEditor.gridFields.getAllRowIds().split(",");
                                            var exist = all_ids.indexOf(field_to_add);
                                            if((exist*1) < 0)
                                            {
                                                dhxlSCGridsEditor.gridFields.addRow(field_to_add,"",index_to_add);
        
                                                if(list_rows!="")
                                                    list_rows = list_rows+",";
                                                list_rows = list_rows+field_to_add;
                                            }
                                        }
                                    }

                                    // DELETE GROUP ROW
                                     dhxlSCGridsEditor.gridFields.deleteRow(id);

                                    // ADDED ROWS GROUP IN LIST
                                    dId = dId.replace(id,list_rows);
                                    if(!(list_rows!=undefined && list_rows!="" && list_rows!=null))
                                        dId = dId.replace(",,",",");
                                }
                            }
                        }
                    }

                    // ADD LINKED FIELDS
                    var ids = dId.split(",");
                    var nb = ids.length;
                    for(var i=0;i<nb;i++)
                    {
                        if(ids[i]!=undefined && ids[i]!="" && ids[i]!=null)
                        {
                            var had_add_fields = dhxlSCGridsEditor.gridEnableFields.getUserData(ids[i],"add_fields");
                            if(had_add_fields!=undefined && had_add_fields!="" && had_add_fields!=null)
                            {
                                var index_group_row = dhxlSCGridsEditor.gridFields.getRowIndex(ids[i]);
                                if(had_add_fields.indexOf("+")>=0)
                                {
                                    var fields = had_add_fields.split("+");
                                    var nb_fields = had_add_fields.length;
                                }
                                else
                                {
                                    var fields = [had_add_fields];
                                    var nb_fields = 2;
                                }
                                for(var j=(nb_fields*1-1);j--;)
                                {
                                    var field_to_add = fields[j];
                                    if(field_to_add!=undefined && field_to_add!="" && field_to_add!=null)
                                    {
                                        var index_to_add = index_group_row*1;

                                        var all_ids = dhxlSCGridsEditor.gridFields.getAllRowIds().split(",");
                                        var exist = all_ids.indexOf(field_to_add);
                                        if((exist*1) < 0)
                                        {
                                            dhxlSCGridsEditor.gridFields.addRow(field_to_add,"",index_to_add);
                                            dId = dId+","+field_to_add;
                                        }
                                    }
                                }
                            }
                        }
                    }
                    
                    // SAVE
                    var list_rows = "";
                    dhxlSCGridsEditor.gridFields.forEachRow(function(id){
                        if(list_rows!="")
                            list_rows = list_rows+";";
                        list_rows = list_rows+id.trim()+','+dhxlSCGridsEditor.gridFields.getRowIndex(id);
                    });
                    $.post("index.php?ajax=1&act=all_win-gridseditor_fields_update&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{"action":"update_position","type":type_selected,"grid_select":grid_selected,"newvalue":list_rows},function(data){
                        colorEnableFields();
                        if(type_selected=="type_propspeprice")
                            dhtmlx.message({text:'<?php echo _l('You need to refresh Store Commander to use the new settings.', 1); ?>',type:'error'});
                    });

                    // FILL ROW
                    idxName=dhxlSCGridsEditor.gridFields.getColIndexById('name');
                    idxText=dhxlSCGridsEditor.gridFields.getColIndexById('text');
                    idxAlign=dhxlSCGridsEditor.gridFields.getColIndexById('align');
                    idxSort=dhxlSCGridsEditor.gridFields.getColIndexById('sort');
                    idxWidth=dhxlSCGridsEditor.gridFields.getColIndexById('width');
                    idxColor=dhxlSCGridsEditor.gridFields.getColIndexById('color');
                    idxFilter=dhxlSCGridsEditor.gridFields.getColIndexById('filter');
                    idxType=dhxlSCGridsEditor.gridFields.getColIndexById('type');
                    var ids = dId.split(",");
                    var nb = ids.length;
                    for(var i=0;i<nb;i++)
                    {
                        if(ids[i]!=undefined && ids[i]!="" && ids[i]!=null)
                        {
                            dhxlSCGridsEditor.gridFields.cells(ids[i],idxName).setValue(dhxlSCGridsEditor.gridEnableFields.getUserData(ids[i],"name"));
                            dhxlSCGridsEditor.gridFields.cells(ids[i],idxText).setValue(dhxlSCGridsEditor.gridEnableFields.getUserData(ids[i],"text"));
                            dhxlSCGridsEditor.gridFields.cells(ids[i],idxAlign).setValue(dhxlSCGridsEditor.gridEnableFields.getUserData(ids[i],"align"));
                            dhxlSCGridsEditor.gridFields.cells(ids[i],idxSort).setValue(dhxlSCGridsEditor.gridEnableFields.getUserData(ids[i],"sort"));
                            dhxlSCGridsEditor.gridFields.cells(ids[i],idxWidth).setValue(dhxlSCGridsEditor.gridEnableFields.getUserData(ids[i],"width"));
                            dhxlSCGridsEditor.gridFields.cells(ids[i],idxColor).setValue(dhxlSCGridsEditor.gridEnableFields.getUserData(ids[i],"color"));
                            dhxlSCGridsEditor.gridFields.cells(ids[i],idxFilter).setValue(dhxlSCGridsEditor.gridEnableFields.getUserData(ids[i],"filter"));


                            
                            dhxlSCGridsEditor.gridEnableFields.setRowColor(ids[i],"#dddddd");
                            
                            if(dhxlSCGridsEditor.gridEnableFields.getUserData(ids[i],"is_custom")==1)
                            {
                                dhxlSCGridsEditor.gridFields.setRowColor(ids[i],"#9ECA92");
                                dhxlSCGridsEditor.gridEnableFields.setRowColor(ids[i],"#caddc5");
                            }
                            if(dhxlSCGridsEditor.gridEnableFields.getUserData(ids[i],"is_special")==1)
                            {
                                dhtmlx.message({text:'<?php echo _l('You need to refresh Store Commander to use the new settings.', 1); ?>',type:'error'});
                                dhxlSCGridsEditor.gridEnableFields.setRowColor(ids[i],"#FFBF7F");
                            }
                        }
                    }
                }
            });
            dhxlSCGridsEditor.gridEnableFields.attachEvent("onDrag", function(sId,tId,sObj,tObj,sInd,tInd){
                if(tObj._name=="gridEnableFields")
                    return false;
                return true;
            });

    //#####################################
    //############ Load functions
    //#####################################

    function displayGrids()
    {
        let typeText = dhxlSCGridsEditor.tbGrids.getListOptionText('type', type_selected);


        dhxlSCGridsEditor.tbGrids.setItemText('type',typeText);
        dhxlSCGridsEditor.tbGrids.setListOptionSelected('type',type_selected);

        dhxlSCGridsEditor.gridGrids.clearAll(true);
        dhxlSCGridsEditor.gridGrids.load("index.php?ajax=1&act=all_win-gridseditor_get&id_lang="+SC_ID_LANG+"&type="+type_selected+"&"+new Date().getTime(),function()
        {
            selectDefaultIfSolo();
            colorEnableFields();
        });
    }

    function selectDefaultIfSolo() {
        if(dhxlSCGridsEditor.gridGrids.getRowsNum() === 1){
            let firstRowId = dhxlSCGridsEditor.gridGrids.getRowId(0);
            dhxlSCGridsEditor.gridGrids.selectRow(0);
            dhxlSCGridsEditor.gridGrids.callEvent("onRowSelect", [firstRowId]);

        }
    }

    function displayFields()
    {
        dhxlSCGridsEditor.gridFields.clearAll(true);
        if(grid_selected!=undefined && grid_selected!=null && grid_selected!="" && grid_selected!=0)
        {
            var is_default = dhxlSCGridsEditor.gridGrids.getUserData(grid_selected,"is_default");
            dhxlSCGridsEditor.gridFields.load("index.php?ajax=1&act=all_win-gridseditor_fields_get&id_lang="+SC_ID_LANG+"&type="+type_selected+"&grid="+grid_selected+"&is_default="+is_default+"&"+new Date().getTime(),function()
            {displayEnableFields();});
        }
    }

    function displayEnableFields()
    {
        dhxlSCGridsEditor.gridEnableFields.clearAll(true);
        dhxlSCGridsEditor.gridEnableFields.load("index.php?ajax=1&act=all_win-gridseditor_enablefields_get&id_lang="+SC_ID_LANG+"&type="+type_selected+"&grid="+grid_selected+"&"+new Date().getTime(),function()
        {
            colorEnableFields();
        });
    }


    //#####################################
    //############ Functions
    //#####################################
    function colorEnableFields()
    {
        dhxlSCGridsEditor.gridEnableFields.forEachRow(function(id){
            if(dhxlSCGridsEditor.gridEnableFields.getUserData(id,"hidden")==1)
                dhxlSCGridsEditor.gridEnableFields.setRowHidden(id,true);
            
            dhxlSCGridsEditor.gridEnableFields.setUserData(id,"is_used",0);
            dhxlSCGridsEditor.gridEnableFields.setRowColor(id,"");

            if(dhxlSCGridsEditor.gridEnableFields.getUserData(id,"is_custom")==1)
                dhxlSCGridsEditor.gridEnableFields.setRowColor(id,"#9ECA92");

            if(dhxlSCGridsEditor.gridEnableFields.getUserData(id,"is_special")==1)
                dhxlSCGridsEditor.gridEnableFields.setRowColor(id,"#FFBF00");


            if(dhxlSCGridsEditor.gridEnableFields.getUserData(id,"compulsory")==1)
                dhxlSCGridsEditor.gridEnableFields.setRowColor(id,"#FF6666");
        });
        dhxlSCGridsEditor.gridFields.forEachRow(function(id){
            dhxlSCGridsEditor.gridEnableFields.setUserData(id,"is_used",1);
            dhxlSCGridsEditor.gridEnableFields.setRowColor(id,"#dddddd");

            if(dhxlSCGridsEditor.gridEnableFields.getUserData(id,"is_custom")==1)
                dhxlSCGridsEditor.gridEnableFields.setRowColor(id,"#caddc5");

            if(dhxlSCGridsEditor.gridEnableFields.getUserData(id,"is_special")==1)
                dhxlSCGridsEditor.gridEnableFields.setRowColor(id,"#FFBF7F");
        });
    }
<?php echo '</script>'; ?>
