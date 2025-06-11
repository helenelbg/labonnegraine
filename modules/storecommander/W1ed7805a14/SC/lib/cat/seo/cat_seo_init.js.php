<?php
$icon = 'fad fa-at';
?>
    <?php if (_r('GRI_CAT_PROPERTIES_GRID_SEO')) { ?>
    prop_tb.addListOption('panel', 'pdtseo', 15, "button", '<?php echo _l('SEO', 1); ?>', "<?php echo $icon; ?>");
    allowed_properties_panel[allowed_properties_panel.length] = "pdtseo";
    <?php } ?>
    prop_tb.addButton("seo_refresh",1000, "", "fa fa-sync green", "fa fa-sync green");
    prop_tb.setItemToolTip('seo_refresh','<?php echo _l('Refresh grid', 1); ?>');
    prop_tb.addButton("seo_exportcsv",1000, "", "fad fa-file-csv green", "fad fa-file-csv green");
    prop_tb.setItemToolTip('seo_exportcsv','<?php echo _l('Export grid to clipboard in CSV format for MSExcel with tab delimiter.'); ?>');
    prop_tb.addButton("seo_selectall",1000, "", "fa fa-bolt yellow", "fad fa-bolt grey");
    prop_tb.setItemToolTip('seo_selectall','<?php echo _l('Select all', 1); ?>');

    prop_tb.addButtonSelect("seo_copy_name_to", 1000, "<?php echo _l('Copy product name to ...', 1); ?>",
        [
            ['seo_copy_mt', 'obj', 'META title', ''],
            ['seo_copy_mtmd', 'obj', 'META title & META description', ''],
            ['seo_copy_md', 'obj', 'META description', '']
        ],
        "fad fa-key yellow", "fad fa-key yellow",false,true);
    prop_tb.setItemToolTip("seo_copy_name_to",'<?php echo _l('Copy product name to ... for selected rows', 1); ?>');
    getTbSettingsButton(prop_tb, {'grideditor':0,'settings':1}, 'prop_seo_',1000);



    clipboardType_PdtSeo = null;
    needInitPdtSeo = 1;
    function initPdtSeo()
    {
        if (needInitPdtSeo)
        {
            prop_tb._PdtSeoLayout = dhxLayout.cells('b').attachLayout('2E');
            dhxLayout.cells('b').showHeader();
            
            // SEO
            prop_tb._pdtSeo = prop_tb._PdtSeoLayout.cells('a');
            prop_tb._pdtSeo.hideHeader();

            prop_tb.attachEvent("onClick", function(id){
                    if (id=='seo_refresh')
                    {
                        displayPdtSeo();
                    }
                    if (id=='seo_exportcsv'){
                        displayQuickExportWindow(prop_tb._pdtSeoGrid,1);
                    }
					if (id=='seo_selectall'){
						prop_tb._pdtSeoGrid.selectAll();
					}
                    if (id=='prop_seo_settings'){
                        openSettingsWindow('Catalog','SEO');
                    }
                    if (id.indexOf('seo_copy_') != -1)
                    {
                        let selected_rows = prop_tb._pdtSeoGrid.getSelectedRowId();
                        if(selected_rows !== null) {
                            selected_rows = selected_rows.split(',');
                            selected_rows.forEach(function (rId, val) {
                                let idxProductName = prop_tb._pdtSeoGrid.getColIndexById('name');
                                let idxMetaTitle = prop_tb._pdtSeoGrid.getColIndexById('meta_title');
                                let idxMetaDescription = prop_tb._pdtSeoGrid.getColIndexById('meta_description');
                                let product_name = String(prop_tb._pdtSeoGrid.cells(rId, idxProductName).getValue());
                                let current_MetaTitle = String(prop_tb._pdtSeoGrid.cells(rId, idxMetaTitle).getValue());
                                let current_MetaDescription = String(prop_tb._pdtSeoGrid.cells(rId, idxMetaDescription).getValue());

                                if (id == 'seo_copy_mt') {
                                    prop_tb._pdtSeoGrid.cells(rId, idxMetaTitle).setValue(product_name);
                                    prop_tb._pdtSeoGrid.cells(rId, idxMetaTitle).cell.wasChanged = true;
                                    onEditCellPdtSeo(2, rId, idxMetaTitle, product_name, current_MetaTitle);
                                } else if (id == 'seo_copy_mtmd') {
                                    prop_tb._pdtSeoGrid.cells(rId, idxMetaTitle).setValue(product_name);
                                    prop_tb._pdtSeoGrid.cells(rId, idxMetaTitle).cell.wasChanged = true;
                                    onEditCellPdtSeo(2, rId, idxMetaTitle, product_name, current_MetaTitle);
                                    prop_tb._pdtSeoGrid.cells(rId, idxMetaDescription).setValue(product_name);
                                    prop_tb._pdtSeoGrid.cells(rId, idxMetaDescription).cell.wasChanged = true;
                                    onEditCellPdtSeo(2, rId, idxMetaDescription, product_name, current_MetaDescription);
                                } else {
                                    prop_tb._pdtSeoGrid.cells(rId, idxMetaDescription).setValue(product_name);
                                    prop_tb._pdtSeoGrid.cells(rId, idxMetaDescription).cell.wasChanged = true;
                                    onEditCellPdtSeo(2, rId, idxMetaDescription, product_name, current_MetaDescription);
                                }
                            });
                        }
                    }
                });
            
            prop_tb._pdtSeoGrid = prop_tb._pdtSeo.attachGrid();
            prop_tb._pdtSeoGrid._name='_pdtSeoGrid';
            prop_tb._pdtSeoGrid.setImagePath("lib/js/imgs/");
              prop_tb._pdtSeoGrid.enableDragAndDrop(false);
            prop_tb._pdtSeoGrid.enableMultiselect(false);
            
            // UISettings
            prop_tb._pdtSeoGrid._uisettings_prefix='cat_PdtSeo';
            prop_tb._pdtSeoGrid._uisettings_name=prop_tb._pdtSeoGrid._uisettings_prefix;
               prop_tb._pdtSeoGrid._first_loading=1;
               
            // UISettings
            initGridUISettings(prop_tb._pdtSeoGrid);
            
            prop_tb._pdtSeoGrid.attachEvent("onEditCell",onEditCellPdtSeo);
            
            
            prop_tb._pdtSeoGrid.attachEvent("onRowSelect",function (idstock){
                if (propertiesPanel=='pdtseo'){
                    displayGoogleAdwords();
                }
            });
            
            // Context menu for MultiShops Info Product grid
            pdtSeo_cmenu=new dhtmlXMenuObject();
            pdtSeo_cmenu.renderAsContextMenu();
            function onGridPdtSeoContextButtonClick(itemId){
                tabId=prop_tb._pdtSeoGrid.contextID.split('_');
                tabId=tabId[0]+"_"+tabId[1]<?php if (SCMS) { ?>+"_"+tabId[2]<?php } ?>;
                if (itemId=="copy"){
                    if (lastColumnRightClicked_PdtSeo!=0)
                    {
                        clipboardValue_PdtSeo=prop_tb._pdtSeoGrid.cells(tabId,lastColumnRightClicked_PdtSeo).getValue();
                        pdtSeo_cmenu.setItemText('paste' , '<?php echo _l('Paste'); ?> '+prop_tb._pdtSeoGrid.cells(tabId,lastColumnRightClicked_PdtSeo).getTitle());
                        clipboardType_PdtSeo=lastColumnRightClicked_PdtSeo;
                    }
                }
                if (itemId=="paste"){
                    if (lastColumnRightClicked_PdtSeo!=0 && clipboardValue_PdtSeo!=null && clipboardType_PdtSeo==lastColumnRightClicked_PdtSeo)
                    {
                        selection=prop_tb._pdtSeoGrid.getSelectedRowId();
                        if (selection!='' && selection!=null)
                        {
                            selArray=selection.split(',');
                            for(i=0 ; i < selArray.length ; i++)
                            {
                                var oValue = prop_tb._pdtSeoGrid.cells(selArray[i],lastColumnRightClicked_PdtSeo).getValue();
                                prop_tb._pdtSeoGrid.cells(selArray[i],lastColumnRightClicked_PdtSeo).setValue(clipboardValue_PdtSeo);
                                prop_tb._pdtSeoGrid.cells(selArray[i],lastColumnRightClicked_PdtSeo).cell.wasChanged=true;
                                onEditCellPdtSeo(2,selArray[i],lastColumnRightClicked_PdtSeo,clipboardValue_PdtSeo,oValue);
                            }
                        }
                    }
                }
            }
            pdtSeo_cmenu.attachEvent("onClick", onGridPdtSeoContextButtonClick);
            var contextMenuXML='<menu absolutePosition="auto" mode="popup" maxItems="8"  globalCss="contextMenu" globalSecondCss="contextMenu" globalTextCss="contextMenuItem">'+
                    '<item text="Object" id="object" enabled="false"/>'+
                    '<item text="Lang" id="lang" enabled="false"/>'+
                    <?php if (SCMS) { ?>'<item text="Shop" id="shop" enabled="false"/>'+<?php } ?>
                    '<item text="<?php echo _l('Copy'); ?>" id="copy"/>'+
                    '<item text="<?php echo _l('Paste'); ?>" id="paste"/>'+
                '</menu>';
            pdtSeo_cmenu.loadStruct(contextMenuXML);
            prop_tb._pdtSeoGrid.enableContextMenu(pdtSeo_cmenu);

            prop_tb._pdtSeoGrid.attachEvent("onBeforeContextMenu", function(rowid,colidx,grid){
                var disableOnCols=new Array(
                        prop_tb._pdtSeoGrid.getColIndexById('id_product'),
                        <?php if (SCMS) { ?>prop_tb._pdtSeoGrid.getColIndexById('shop'),<?php } ?>
                        prop_tb._pdtSeoGrid.getColIndexById('lang'),
                        prop_tb._pdtSeoGrid.getColIndexById('meta_title_width'),
                        prop_tb._pdtSeoGrid.getColIndexById('meta_description_width'),
                        prop_tb._pdtSeoGrid.getColIndexById('meta_keywords_width')
                        );
                if (in_array(colidx,disableOnCols))
                {
                    return false;
                }
                lastColumnRightClicked_PdtSeo=colidx;
                pdtSeo_cmenu.setItemText('object', '<?php echo _l('Product:'); ?> '+prop_tb._pdtSeoGrid.cells(rowid,prop_tb._pdtSeoGrid.getColIndexById('name')).getTitle());
                <?php if (SCMS) { ?>pdtSeo_cmenu.setItemText('shop', '<?php echo _l('Shop:'); ?> '+prop_tb._pdtSeoGrid.cells(rowid,prop_tb._pdtSeoGrid.getColIndexById('shop')).getTitle());<?php } ?>
                pdtSeo_cmenu.setItemText('lang', '<?php echo _l('Lang:'); ?> '+prop_tb._pdtSeoGrid.cells(rowid,prop_tb._pdtSeoGrid.getColIndexById('lang')).getTitle());
                if (lastColumnRightClicked_PdtSeo==clipboardType_PdtSeo)
                {
                    pdtSeo_cmenu.setItemEnabled('paste');
                }else{
                    pdtSeo_cmenu.setItemDisabled('paste');
                }
                return true;
            });
            
            // GOOGLE ADD
            prop_tb._googleAdwords = prop_tb._PdtSeoLayout.cells('b');
            prop_tb._googleAdwords.setText('<?php echo _l('Google Adwords', 1); ?>');
            
            prop_tb._googleAdwords_tb = prop_tb._googleAdwords.attachToolbar();
             prop_tb._googleAdwords_tb.setIconset('awesome');
            prop_tb._googleAdwords_tb.addButton("googleAdwords_refresh", 1000, "", "fa fa-sync green", "fa fa-sync green");
            prop_tb._googleAdwords_tb.setItemToolTip('googleAdwords_refresh','<?php echo _l('Refresh grid', 1); ?>');
            prop_tb._googleAdwords_tb.attachEvent("onClick", function(id){
                if (id=='googleAdwords_refresh')
                {
                    displayGoogleAdwords();
                }
            });

            prop_tb._PdtSeoLayout.attachEvent("onPanelResizeFinish", function(name){
                name.forEach(function(id){
                    if(id=='b'){
                        saveParamUISettings('cat_Seo_Height', prop_tb._googleAdwords.getHeight());
                    }
                });
                return true;
            });
        
            needInitPdtSeo=0;
        }
    }
    
    
    
    function onEditCellPdtSeo(stage,rId,cInd,nValue,oValue)
    {
        if (stage==1 && this.editor && this.editor.obj) this.editor.obj.select();
        
        if (stage==2 && nValue!=oValue)
        {
            idxLinkRewrite=prop_tb._pdtSeoGrid.getColIndexById('link_rewrite');
            if (nValue!="" && cInd==idxLinkRewrite)
            {
                <?php
                $accented = Configuration::get('PS_ALLOW_ACCENTED_CHARS_URL');
                if ($accented == 1) {    ?>
                    prop_tb._pdtSeoGrid.cells(rId,idxLinkRewrite).setValue(getAccentedLinkRewriteFromString(nValue.substr(0,<?php echo _s('CAT_LINK_REWRITE_SIZE'); ?>)));
                <?php }
                else
                { ?>
                    let rId_splitted = rId.split('_');
                    let id_lang = Number(rId_splitted[1]);
                    prop_tb._pdtSeoGrid.cells(rId,idxLinkRewrite).setValue(getLinkRewriteFromString(nValue.substr(0,<?php echo _s('CAT_LINK_REWRITE_SIZE'); ?>),id_lang));
                <?php } ?>
            }
        
            var params = {
                name: "cat_seo_update_queue",
                row: rId,
                action: "update",
                params: {},
                callback: "callbackPdtSeo('"+rId+"','update','"+rId+"');"
            };
            // COLUMN VALUES
            params.params[prop_tb._pdtSeoGrid.getColumnId(cInd)] = prop_tb._pdtSeoGrid.cells(rId,cInd).getValue();
            // USER DATA
            
            params.params = JSON.stringify(params.params);
            addInUpdateQueue(params,prop_tb._pdtSeoGrid);
        }
        return true;
    }
    // CALLBACK FUNCTION
    function callbackPdtSeo(sid,action,tid)
    {
        if (action=='update') {
            prop_tb._pdtSeoGrid.setRowTextNormal(sid);
            checkPixelSize(sid);
        }
    }
    
    function setPropertiesPanel_PdtSeo(id){
        if (id=='pdtseo')
        {
            if(lastProductSelID!=undefined && lastProductSelID!="")
            {
                dhxLayout.cells('b').setText('<?php echo _l('Properties', 1).' '._l('of', 1); ?> '+getSelectedItemValueOrID(cat_grid,lastProductSelID,'name'));
            }
            hidePropTBButtons();
            prop_tb.setItemText('panel', '<?php echo _l('SEO', 1); ?>');
            prop_tb.setItemImage('panel', '<?php echo $icon; ?>');
            prop_tb.showItem('seo_refresh');
            prop_tb.showItem('seo_selectall');
            prop_tb.showItem('seo_exportcsv');
            prop_tb.showItem('seo_go_to_settings');
            prop_tb.showItem('seo_copy_name_to');
            prop_tb.showItem('prop_seo_settings_menu');
            needInitPdtSeo = 1;
            initPdtSeo();
            propertiesPanel='pdtseo';
            if (lastProductSelID!=0)
            {
                displayPdtSeo();
                displayGoogleAdwords();
            }
        }
    }
    prop_tb.attachEvent("onClick", setPropertiesPanel_PdtSeo);

    function displayPdtSeo()
    {
        prop_tb._pdtSeoGrid.clearAll(true);
        var tempIdList = (cat_grid.getSelectedRowId()!=null?cat_grid.getSelectedRowId():"");
        $.post("index.php?ajax=1&act=cat_seo_get&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{'idlist': tempIdList},function(data)
        {
            prop_tb._pdtSeoGrid.parse(data);
            nb=prop_tb._pdtSeoGrid.getRowsNum();
            prop_tb._pdtSeoGrid._rowsNum=nb;

               // UISettings
            loadGridUISettings(prop_tb._pdtSeoGrid);
            prop_tb._pdtSeoGrid._first_loading=0;
        }).success(function(){
            checkPixelSize();
        });
    }

function checkPixelSize(rId = null){
    let all_rows = null;
    if(rId !== null) {
        all_rows = [rId];
    } else {
        all_rows = prop_tb._pdtSeoGrid.getAllRowIds();
        all_rows = all_rows.split(',');
    }
    let idxMetaTitle = prop_tb._pdtSeoGrid.getColIndexById('meta_title');
    let idxMetaTitleSize = prop_tb._pdtSeoGrid.getColIndexById('meta_title_width');
    let idxMetaTitlePixelSize = prop_tb._pdtSeoGrid.getColIndexById('meta_title_pixel_width');
    let idxMetaDescription = prop_tb._pdtSeoGrid.getColIndexById('meta_description');
    let idxMetaDescriptionSize = prop_tb._pdtSeoGrid.getColIndexById('meta_description_width');
    let idxMetaDescriptionPixelSize = prop_tb._pdtSeoGrid.getColIndexById('meta_description_pixel_width');
    let maxTitlePx = Number(<?php echo _s('CAT_SEO_META_TITLE_PIXEL_COLOR'); ?>);
    let maxDescriptionPx = Number(<?php echo _s('CAT_SEO_META_DESCRIPTION_PIXEL_COLOR'); ?>);

    $('.seoToDelete').remove();
    all_rows.forEach(function(id){
        let title_size = Number(prop_tb._pdtSeoGrid.cells(id,idxMetaTitleSize).getValue());
        let description_size = Number(prop_tb._pdtSeoGrid.cells(id,idxMetaDescriptionSize).getValue());
        if(title_size > 0){
            let title = prop_tb._pdtSeoGrid.cells(id,idxMetaTitle).getValue();
            let title_in_px = Number(getItemSize(id,title,{font:'Arial',fontSize:'18px'}));
            prop_tb._pdtSeoGrid.cells(id,idxMetaTitlePixelSize).setValue(title_in_px);
            if(title_in_px > maxTitlePx) {
                prop_tb._pdtSeoGrid.setCellTextStyle(id,idxMetaTitlePixelSize,"background-color:#FE9730;");
            }
        }
        if(description_size > 0){
            let description = prop_tb._pdtSeoGrid.cells(id,idxMetaDescription).getValue();
            let description_in_px = Number(getItemSize(id,description,{font:'Arial',fontSize:'13px'}));
            prop_tb._pdtSeoGrid.cells(id,idxMetaDescriptionPixelSize).setValue(description_in_px);
            if(description_in_px > maxDescriptionPx) {
                prop_tb._pdtSeoGrid.setCellTextStyle(id,idxMetaDescriptionPixelSize,"background-color:#FE9730;");
            }
        }
    });
}

function getItemSize(id,text,config)
{
    let style = [
        'font-family:'+config.font,
        'font-size:'+config.fontSize,
        'font-weight:'+(config.fontWeight ? config.fontWeight : 'normal'),
        'position:absolute',
        'visibility:hidden',
        'left:-999px',
        'top:-999px',
        'z-index:-99',
        'width:auto',
        'height:auto',
    ];
    let current_obj = $('<div class="seoToDelete" style="'+style.join(';')+'">'+text+'</div>');
    $('body').append(current_obj);
    return current_obj.width();
}

    function displayGoogleAdwords()
    {
        let cell_height = Number(200);
        let ui_cell_height = getParamUISettings('cat_Seo_Height');
        if(ui_cell_height !== '' && ui_cell_height !== null) {
            cell_height = ui_cell_height;
        }

        prop_tb._googleAdwords.setHeight(cell_height);
        prop_tb._googleAdwords.attachURL("index.php?ajax=1&act=cat_seo_add_get&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),function(data){});
    }


    let pdtseo_current_id = 0;
    cat_grid.attachEvent("onRowSelect",function (idproduct){
        if (propertiesPanel=='pdtseo' && (cat_grid.getSelectedRowId()!==null && pdtseo_current_id!=idproduct)){
            displayPdtSeo();
            displayGoogleAdwords();
            pdtseo_current_id=idproduct;
        }
    });
