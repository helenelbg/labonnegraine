<?php
if (SCMS)
{
    $sql = 'SELECT s.id_shop, s.name
            FROM '._DB_PREFIX_.'shop s
            INNER JOIN '._DB_PREFIX_.'product_shop ps ON ps.id_shop = s.id_shop
            '.((!empty($sc_agent->id_employee)) ? ' INNER JOIN '._DB_PREFIX_."employee_shop es ON (es.id_shop = s.id_shop AND es.id_employee = '".(int) $sc_agent->id_employee."') " : '')."
            WHERE s.deleted!='1'
            GROUP BY s.id_shop
            ORDER BY s.name";
    $shops = Db::getInstance()->executeS($sql);
}
if (_r('GRI_CAT_PROPERTIES_GRID_CATEGORY'))
{ ?>
        prop_tb.addListOption('panel', 'categories', 4, "button", '<?php echo _l('Categories', 1); ?>', "fa fa-folder yellow");
        allowed_properties_panel[allowed_properties_panel.length] = "categories";
    <?php } ?>
    
    var for_mb = 0;

    prop_tb.addButton('categ_refresh',1000,'','fa fa-sync green','fa fa-sync green');
    prop_tb.setItemToolTip('categ_refresh','<?php echo _l('Refresh', 1); ?>');
    prop_tb.addButtonTwoState('categ_filter', 1000, "", "fa fa-filter", "fa fa-filter");
    prop_tb.setItemToolTip('categ_filter','<?php echo _l('Display only categories used by selected products', 1); ?>');
    <?php if (SCMS)
    { ?>
    prop_tb.addButtonTwoState('for_mb', 1000, "", "fa fa-folder-open", "fa fa-folder-open");
    prop_tb.setItemToolTip('for_mb','<?php echo _l('Only display categories associated to the selected shop', 1); ?>');
    prop_tb.setItemState('for_mb', 1);
    for_mb = 1;
    <?php } ?>
    prop_tb.addButton('categ_go',1000,'','fa fa-external-link green','fa fa-external-link green');
    prop_tb.setItemToolTip('categ_go','<?php echo _l('Open and select category', 1); ?>');
    prop_tb.addButton('categ_expand',1000,'','fa fa-expand-arrows-alt green','fa fa-expand-arrows-alt green');
    prop_tb.setItemToolTip('categ_expand','<?php echo _l('Expand all items', 1); ?>');
    prop_tb.addButton('categ_collapse',1000,'','fa fa-compress-arrows-alt green','fa fa-compress-arrows-alt green');
    prop_tb.setItemToolTip('categ_collapse','<?php echo _l('Collapse all items', 1); ?>');
    prop_tb.addButton('categ_multi_add',1000,'','fad fa-link yellow','fad fa-link yellow');
    prop_tb.setItemToolTip('categ_multi_add','<?php echo _l('Place selected products in selected categories', 1); ?>');
    prop_tb.addButton('categ_multi_del',1000,'','fad fa-unlink red','fad fa-unlink red');
    prop_tb.setItemToolTip('categ_multi_del','<?php echo _l('Remove selected products from selected categories (if not default category)', 1); ?>');


    needInitCategories = 1;
    function initCategories() {
        if (needInitCategories)
        {
            prop_tb._categoriesLayout = dhxLayout.cells('b').attachLayout('1C');
            prop_tb._categoriesLayout.cells('a').hideHeader();
            dhxLayout.cells('b').showHeader();
            prop_tb._categoriesGrid = prop_tb._categoriesLayout.cells('a').attachGrid();
            prop_tb._categoriesGrid.setIconset('awesome');
            prop_tb._categoriesGrid.setImagePath("lib/js/imgs/");
            prop_tb._categoriesGrid.setFiltrationLevel(-2);
            prop_tb._categoriesGrid.enableTreeCellEdit(0);
            prop_tb._categoriesGrid.enableSmartRendering(true);

            // UISettings
            prop_tb._categoriesGrid._uisettings_prefix='cat_categorypanel';
            prop_tb._categoriesGrid._uisettings_name=prop_tb._categoriesGrid._uisettings_prefix;
               prop_tb._categoriesGrid._first_loading=1;
               
            // UISettings
            initGridUISettings(prop_tb._categoriesGrid);
            
            prop_tb._categoriesGrid.attachEvent("onEditCell",function(stage,rId,cInd,nValue,oValue) {
                    idxUsed=prop_tb._categoriesGrid.getColIndexById('used');
<?php
    if (_s('CAT_PROD_CAT_DEF_EXT'))
    {
        ?>
        if (cInd == idxUsed){
            if(stage==1)
            {
                var selection = cat_grid.getSelectedRowId();
                ids=selection.split(',');
                $.each(ids, function(num, pId) {
                    var vars = {"sub_action":prop_tb._categoriesGrid.cells(rId,idxUsed).getValue(),"idlist":pId,"eservices_id_project":prop_tb._categoriesGrid.getUserData(rId,"eservices_id_project")};
                    addCategoryInQueue(rId, "update", cInd, vars);
                });
            }
        }
        <?php

        if (SCMS)
        {
            foreach ($shops as $values)
            {
                ?>
                idxDefaultShop_<?php echo $values['id_shop']; ?> = prop_tb._categoriesGrid.getColIndexById('default_shop_<?php echo $values['id_shop']; ?>');
                if (cInd == idxDefaultShop_<?php echo $values['id_shop']; ?>)
                {
                    if(stage==1 && prop_tb._categoriesGrid.cells(rId,idxDefaultShop_<?php echo $values['id_shop']; ?>).getValue()==1)
                    {
                        var selection = cat_grid.getSelectedRowId();
                        ids=selection.split(',');
                        $.each(ids, function(num, pId)
                        {
                            var vars =
                            {
                                "sub_action":"default"+prop_tb._categoriesGrid.cells(rId,idxDefaultShop_<?php echo $values['id_shop']; ?>).getValue(),
                                "idlist":pId,
                                "id_shop": <?php echo $values['id_shop']; ?>
                            };
                            addCategoryInQueue(rId, "update", cInd, vars);

                        });

                    }
                }
    <?php
            }
        }
        else
        { ?>
            idxDefault=prop_tb._categoriesGrid.getColIndexById('default');
            if (cInd == idxDefault)
            {
                if(stage==1 && prop_tb._categoriesGrid.cells(rId,idxDefault).getValue()==1)
                {
                    var selection = cat_grid.getSelectedRowId();
                    ids=selection.split(',');
                    $.each(ids, function(num, pId)
                    {
                        var vars = {"sub_action":"default"+prop_tb._categoriesGrid.cells(rId,idxDefault).getValue(),"idlist":pId};
                        addCategoryInQueue(rId, "update", cInd, vars);
                    });
                }
            }
            <?php
        } ?>
    <?php
    }
    else
    {
        if (!SCMS)
        { ?>
            idxDefault=prop_tb._categoriesGrid.getColIndexById('default');
        <?php } ?>
        if (cInd == idxUsed)
        {
            <?php if (SCMS)
            {
                foreach ($shops as $values)
                { ?>
                    idxDefaultShop_<?php echo $values['id_shop']; ?> = prop_tb._categoriesGrid.getColIndexById('default_shop_<?php echo $values['id_shop']; ?>');
            <?php }
                foreach ($shops as $i => $values)
                { ?>
                    <?php echo $i > 0 ? 'else ' : ''; ?>if(stage==0 && prop_tb._categoriesGrid.getColIndexById('idxDefaultShop_<?php echo $values['id_shop']; ?>') != undefined && prop_tb._categoriesGrid.cells(rId,idxUsed).getValue()==1 && prop_tb._categoriesGrid.cells(rId,idxDefaultShop_<?php echo $values['id_shop']; ?>).getValue()==1)
                    return false;
            <?php }
                }
        else
        { ?>
            if(stage==0 && prop_tb._categoriesGrid.cells(rId,idxUsed).getValue()==1 && prop_tb._categoriesGrid.cells(rId,idxDefault).getValue()==1)
                return false;
            <?php } ?>
            else if(stage==1)
            {
                var selection = cat_grid.getSelectedRowId();
                ids=selection.split(',');
                $.each(ids, function(num, pId) {
                    var vars = {"sub_action":prop_tb._categoriesGrid.cells(rId,idxUsed).getValue(),"idlist":pId,"eservices_id_project":prop_tb._categoriesGrid.getUserData(rId,"eservices_id_project")};
                    addCategoryInQueue(rId, "update", cInd, vars);
                });
            }
        }
        <?php
            if (SCMS)
            {
                foreach ($shops as $values)
                {
                    ?>
                    idxDefaultShop_<?php echo $values['id_shop']; ?> = prop_tb._categoriesGrid.getColIndexById('default_shop_<?php echo $values['id_shop']; ?>');
                    if (cInd == idxDefaultShop_<?php echo $values['id_shop']; ?>)
                    {
                        if(stage==1 && prop_tb._categoriesGrid.cells(rId,idxDefaultShop_<?php echo $values['id_shop']; ?>).getValue()==1)
                        {
                            prop_tb._categoriesGrid.cells(rId,idxUsed).setValue(1);
                            var selection = cat_grid.getSelectedRowId();
                            ids=selection.split(',');
                            $.each(ids, function(num, pId)
                            {
                                    var vars =
                                    {
                                        "sub_action":"default"+prop_tb._categoriesGrid.cells(rId,idxDefaultShop_<?php echo $values['id_shop']; ?>).getValue(),
                                        "idlist":pId,
                                        "id_shop": <?php echo $values['id_shop']; ?>
                                    };
                                    addCategoryInQueue(rId, "update", cInd, vars);
                            });

                        }
                    }
        <?php
                }
            }
        else
        { ?>
                if (cInd == idxDefault)
                {
                    if(stage==1 && prop_tb._categoriesGrid.cells(rId,idxDefault).getValue()==1)
                    {
                        prop_tb._categoriesGrid.cells(rId,idxUsed).setValue(1);
                        var selection = cat_grid.getSelectedRowId();
                        ids=selection.split(',');
                        $.each(ids, function(num, pId)
                        {
                            var vars = {"sub_action":"default"+prop_tb._categoriesGrid.cells(rId,idxDefault).getValue(),"idlist":pId};
                            addCategoryInQueue(rId, "update", cInd, vars);
                        });
                    }
                }
        <?php
        }
    }
?>
                    return true;
                });
            prop_tb._categoriesGrid.enableMultiselect(true);
            needInitCategories=0;
        }
    }

        prop_tb.attachEvent("onStateChange",function(id,state) {
            if (id=='categ_filter')
            {
                if (state)
                {
                    categoriesFilter=1;
                }else{
                    categoriesFilter=0;
                }
                cache_categorypanel_treeticks = [];
                displayCategories();
            }
            <?php if (SCMS)
            { ?>
            if (id=='for_mb')
            {
                if (state)
                {
                    for_mb=1;
                }else{
                    for_mb=0;
                }
                cache_categorypanel_treeticks = [];
                displayCategories('',true);
            }
            <?php } ?>
        });

    function setPropertiesPanel_categories(id) {
        if (id=='categories')
        {
            if(lastProductSelID!=undefined && lastProductSelID!="")
            {
                idxProductName=cat_grid.getColIndexById('name');
                dhxLayout.cells('b').setText('<?php echo _l('Properties', 1).' '._l('of', 1); ?> '+getSelectedItemValueOrID(cat_grid,lastProductSelID,'name'));
            }
            hidePropTBButtons();
            prop_tb.showItem('categ_multi_del');
            prop_tb.showItem('categ_multi_add');
            prop_tb.showItem('categ_expand');
            prop_tb.showItem('categ_collapse');
            prop_tb.showItem('categ_refresh');
            prop_tb.showItem('categ_filter');
            <?php if (SCMS)
            { ?>
            prop_tb.showItem('for_mb');
            <?php } ?>
            prop_tb.showItem('categ_go');
            prop_tb.setItemText('panel', '<?php echo _l('Categories', 1); ?>');
            prop_tb.setItemImage('panel', 'fa fa-folder yellow');
            needInitCategories = 1;
            initCategories();
            propertiesPanel='categories';
            if (lastProductSelID!=0)
            {
                cache_categorypanel_treeticks = [];
                displayCategories();
            }
        }
        if (id=='categ_refresh')
        {
            cache_categorypanel_treeticks = [];
            displayCategories(null, true);
        }
        if (id=='categ_go')
        {
            cat_tree.openItem(prop_tb._categoriesGrid.getSelectedRowId());
            cat_tree.selectItem(prop_tb._categoriesGrid.getSelectedRowId(),true);
        }
        if (id=='categ_expand')
        {
            prop_tb._categoriesGrid.expandAll();
			cache_categorypanel_treeticks = [];
            displayCategories();
        }
        if (id=='categ_collapse')
        {
            prop_tb._categoriesGrid.collapseAll();
        }
        if (id=='categ_multi_add')
        {
            if (prop_tb._categoriesGrid.getSelectedRowId()==null || cat_grid.getSelectedRowId()==null)
            {
                alert('<?php echo _l('Please select an item', 1); ?>');
            }else{
                var selection = cat_grid.getSelectedRowId();
                ids=selection.split(',');
                $.each(ids, function(num, pId) {
                    var vars = {"sub_action":"multi_add","idprod":pId,"idcateg":prop_tb._categoriesGrid.getSelectedRowId()};
                    addCategoryInQueue("", "update", "", vars);
                });
            }
        }
        if (id=='categ_multi_del')
        {
            if (prop_tb._categoriesGrid.getSelectedRowId()==null || cat_grid.getSelectedRowId()==null)
            {
                alert('<?php echo _l('Please select an item', 1); ?>');
            }else{
                var selection = cat_grid.getSelectedRowId();
                ids=selection.split(',');
                $.each(ids, function(num, pId) {
                    var vars = {"sub_action":"multi_del","idprod":pId,"idcateg":prop_tb._categoriesGrid.getSelectedRowId()};
                    addCategoryInQueue("", "update", "", vars);
                });
            }
        }
    }
    prop_tb.attachEvent("onClick", setPropertiesPanel_categories);


cache_categorypanel_treeticks = [];
function displayCategories(callback,force_refresh)
{
    idxColName=prop_tb._categoriesGrid.getColIndexById('name');
    idxColUsed=prop_tb._categoriesGrid.getColIndexById('used');
    idxColFilterUsed=prop_tb._categoriesGrid.getColIndexById('filter_used');
    if (prop_tb._categoriesGrid._rowsNum>0 && force_refresh!=true)
    {
        $.post("index.php?ajax=1&act=cat_categorypanel_relation_get&for_mb="+for_mb+"&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{"idlist":cat_grid.getSelectedRowId()},function(data) {
                if (data!='')
                {
                    prop_tb._categoriesGrid.filterTreeBy(idxColUsed,'',0);
                    if (cache_categorypanel_treeticks.length == 0)
                        prop_tb._categoriesGrid.uncheckAll();
                    dataArray=data.split('|');
                    selArray=dataArray[0].split(',');
                    selFilterArray=dataArray[3].split(',');
                    let not_all_products_present = dataArray[2];

                    selArray.forEach(function(id) {
                        var not_associate_eservices = prop_tb._categoriesGrid.getUserData(id,"not_associate_eservices");
                        if(not_associate_eservices == "" || not_associate_eservices == "undefined" || not_associate_eservices == null){
                            not_associate_eservices = 0;
                        }
                        if(not_associate_eservices=="0")
                        {
                            if (prop_tb._categoriesGrid.doesRowExist(id) && (!(id in cache_categorypanel_treeticks) || cache_categorypanel_treeticks[id] == 0)) {
                                if(prop_tb._categoriesGrid.getRowById(id) !== -1 && prop_tb._categoriesGrid.getRowById(id) !== null) {
                                    prop_tb._categoriesGrid.cellById(id,idxColUsed).setValue(1);
                                }
                                cache_categorypanel_treeticks[id] = 1;
                            }
                        }
                    });

                    selFilterArray.forEach(function(id) {
                        var not_associate_eservices = prop_tb._categoriesGrid.getUserData(id,"not_associate_eservices");
                        if(not_associate_eservices == "" || not_associate_eservices == "undefined" || not_associate_eservices == null){
                            not_associate_eservices = 0;
                        }
                        if(not_associate_eservices=="0")
                        {
                            if(prop_tb._categoriesGrid.getRowById(id) !== -1 && prop_tb._categoriesGrid.getRowById(id) !== null) {
                                prop_tb._categoriesGrid.cellById(id,idxColFilterUsed).setValue(1);
                            }
                        }
                    });

                    if (dataArray[1]!=undefined && dataArray[1]!='')
                    {
                    <?php if (SCMS)
                    { ?>
                            selArray=dataArray[1].split(',');
                            for(var i=0;i< selArray.length; i++)
                            {
                                middleArray=selArray[i].split('_');
                                idxColNum=prop_tb._categoriesGrid.getColIndexById('default_shop_'+middleArray[0]);
                                if (prop_tb._categoriesGrid.doesRowExist(middleArray[1]) && idxColNum)
                                    prop_tb._categoriesGrid.cellById(middleArray[1],idxColNum).setChecked(1);
                            }
                    <?php }
                    else
                    { ?>
                        if (prop_tb._categoriesGrid.doesRowExist(dataArray[1]))
                            prop_tb._categoriesGrid.cellById(dataArray[1],3).setChecked(1);
                    <?php } ?>
                    }
                    else
                    {
                        <?php if (SCMS)
                        { ?>
                            prop_tb._categoriesGrid.forEachRow(function(id)
                            {
                                <?php

                                foreach ($shops as $shop)
                                {
                                    ?>
                                    idxColNum=prop_tb._categoriesGrid.getColIndexById('default_shop_<?php echo $shop['id_shop']; ?>');
                                    if(idxColNum!=undefined && idxColNum!="" && idxColNum!=null && idxColNum!=0)
                                        prop_tb._categoriesGrid.cellById(id,idxColNum).setChecked(0);
                                <?php
                                }
                            ?>
                            });
                        <?php }
                        else
                        { ?>
                                prop_tb._categoriesGrid.forEachRow(function(id)
                                {
                                    prop_tb._categoriesGrid.cellById(id,3).setChecked(0);
                                });

                        <?php } ?>
                    }
                    if (categoriesFilter)
                    {
                        if (cat_grid.getSelectedRowId().split(',').length>1)
                        {
                            prop_tb._categoriesGrid.filterTreeBy(idxColFilterUsed,1,0);
                        }
                        else
                        {
                            prop_tb._categoriesGrid.filterTreeBy(idxColUsed,1,0);
                        }
                        prop_tb._categoriesGrid.expandAll();
                    }
                    if (prop_tb._categoriesGrid.getFilterElement(idxColName)!=null && prop_tb._categoriesGrid.getFilterElement(idxColName).value!='')
                        prop_tb._categoriesGrid.filterTreeBy(idxColName,prop_tb._categoriesGrid.getFilterElement(idxColName).value,1);
                
                    // UISettings
                    loadGridUISettings(prop_tb._categoriesGrid);

<?php
    if (_s('CAT_PRODPROP_CAT_SHOW_SUBCATCNT'))
    {
        ?>
                    setNbSelected();
<?php
    }
?>
                    
                    // UISettings
                    prop_tb._categoriesGrid._first_loading=0;

                    prop_tb._categoriesGrid.forEachRow(function(id_categ) {
                        if(prop_tb._categoriesGrid.getRowIndex(id_categ) > -1) {
                            prop_tb._categoriesGrid.cellById(id_categ,idxColUsed).setBgColor('transparent');
                            if (cat_grid.getSelectedRowId().split(',').length>1) {
                                if(not_all_products_present !== undefined && not_all_products_present !== '' && not_all_products_present.split(',').includes(id_categ)) {
                                    prop_tb._categoriesGrid.cellById(id_categ,idxColUsed).setBgColor('#777777');
                                }
                                else if (prop_tb._categoriesGrid.cellById(id_categ,idxColUsed).isChecked()) {
                                    prop_tb._categoriesGrid.cellById(id_categ,idxColUsed).setBgColor('#7777AA');
                                }
                                else {
                                    prop_tb._categoriesGrid.cellById(id_categ,idxColUsed).setBgColor('#DDDDDD');
                                }
                            }
                        }
                    });
                }
            });
    }else{
        if ((cat_grid.getSelectedRowId()==null || cat_grid.getSelectedRowId()=='') && force_refresh!=true) return false;
        prop_tb._categoriesGrid.clearAll(true);
        prop_tb._categoriesGrid.load("index.php?ajax=1&act=cat_categorypanel_get&for_mb="+for_mb+"&id_product="+lastProductSelID+"&id_lang="+SC_ID_LANG,function()
                {

                    nb=prop_tb._categoriesGrid.getRowsNum();
                    prop_tb._sb.setText(nb+(nb>1?" <?php echo _l('categories'); ?>":" <?php echo _l('category'); ?>"));
                    prop_tb._categoriesGrid._rowsNum=nb;
                
                    // UISettings
                    loadGridUISettings(prop_tb._categoriesGrid);
                    
                    // UISettings
                    prop_tb._categoriesGrid._first_loading=0;
                
                    displayCategories();
                    
                    if (callback!='') eval(callback);
            });

    }
}


    let categories_current_id = 0;
    cat_grid.attachEvent("onRowSelect",function (idproduct) {
        if (propertiesPanel=='categories' && (cat_grid.getSelectedRowId()!==null && categories_current_id!=idproduct)){
            cache_categorypanel_treeticks = [];
            if(categoriesFilter == 1) {
                displayCategories('',true);
            } else {
                displayCategories();
            }
            categories_current_id=idproduct;
        }
    });

function setNbSelected()
{
    _setNbSelected("");
}
function _setNbSelected(parent_id)
{
    idxColName=prop_tb._categoriesGrid.getColIndexById('name');
    idxColUsed=prop_tb._categoriesGrid.getColIndexById('used');
    var nb_count = 0;
    
    var row_n = prop_tb._categoriesGrid.getSubItems(parent_id);
    
    if(row_n!=undefined && row_n!=null && row_n!="")
    {
        var rows = row_n.split(",");
        $.each(rows, function(num, id) {
            var checked = prop_tb._categoriesGrid.cellById(id,idxColUsed).getValue();
            if(checked==true)
            {
                nb_count = nb_count*1 + 1;
            }
            
            var nb_children = _setNbSelected(id);
            
            var text_base = prop_tb._categoriesGrid.cellById(id,idxColName).getValue();
            var exp = text_base.split("<strong>");
            text_base = exp[0];
            var text = text_base+" <strong>["+nb_children+"]</strong>";
            prop_tb._categoriesGrid.cellById(id,idxColName).setValue(text);
            
            nb_count = nb_count*1 + nb_children;
        });
    }
    return nb_count;
}

function addCategoryInQueue(rId, action, cIn, vars)
{
    var params = {
        name: "cat_categorypanel_update_queue",
        row: rId,
        action: "update",
        params: {},
        callback: "callbackCategory('"+rId+"','update','"+rId+"',{data});"
    };
    // COLUMN VALUES
        params.params["id_lang"] = SC_ID_LANG;
        if(vars!=undefined && vars!=null && vars!="" && vars!=0)
        {
            $.each(vars, function(key, value) {
                params.params[key] = value;
            });
        }        
    // USER DATA

    
    params.params = JSON.stringify(params.params);
    addInUpdateQueue(params,prop_tb._categoriesGrid);
}
        
// CALLBACK FUNCTION
function callbackCategory(sid,action,tid,xml)
{
    if (action=='update')
    {
        prop_tb._categoriesGrid.setRowTextNormal(sid);
        
        if(xml!=undefined && xml!=null && xml!="" && xml!=0)
        {
            var reload_cat = xml.reload_cat;
            if (reload_cat=='1')
                displayTree();
            var refresh_cat = xml.refresh_cat;
            if (refresh_cat=='1')
            {
                cache_categorypanel_treeticks = [];
                displayCategories();
            }
        }
    }
}
