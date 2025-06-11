prop_tb.attachEvent("onClick", function setPropertiesPanel_combinations(id){
    if (id=='combinations')
    {
        prop_tb.combi_subproperties_tb.addListOption('combiSubProperties', 'combi_images', 9, "button", '<?php echo _l('Images', 1); ?>', "fad fa-image");
    
        prop_tb.combi_subproperties_tb.attachEvent("onClick", function(id){
            if(id=="combi_images")
            {
                hideSubpropertiesItems();
                prop_tb.combi_subproperties_tb.setItemText('combiSubProperties', '<?php echo _l('Images', 1); ?>');
                prop_tb.combi_subproperties_tb.setItemImage('combiSubProperties', 'fad fa-image');
                actual_subproperties = "combi_images";
                needInitCombinationsImage = 1;
                initCombinationImage();
            }
        });
        
                        
        prop_tb._combinationsGrid.attachEvent("onRowSelect", function(id,ind){
            if (!prop_tb._combinationsLayout.cells('b').isCollapsed() && actual_subproperties === "combi_images")
            {
                let v_scroll=prop_tb._combinationsImagesGrid.objBox.scrollTop;
                getCombinationsImages('prop_tb._combinationsImagesGrid.objBox.scrollTop = "'+v_scroll+'";');
                displayCombiFastUpload();
                displayCombiFastMobileTabletUpload();
            }
        });
    }
});

var lastCheckedRow = 0;
function initCombinationImage()
{
    if(needInitCombinationsImage==1)
    {
        prop_tb.combi_subproperties_tb.addButton("combi_img_add", 100, "", "fa fa-plus-circle green", "fa fa-plus-circle green");
        prop_tb.combi_subproperties_tb.setItemToolTip('combi_img_add','<?php echo _l('Upload new images', 1); ?>');
        <?php if (_r('ACT_CAT_DELETE_IMAGE')) { ?>
            prop_tb.combi_subproperties_tb.addButton('combi_img_del',100,'','fa fa-minus-circle red','fa fa-minus-circle red');
            prop_tb.combi_subproperties_tb.setItemToolTip('combi_img_del','<?php echo _l('Delete selected item', 1); ?>');
        <?php } ?>
        prop_tb.combi_subproperties_tb.addButton("combi_img_all_association", 100, "", "fad fa-link yellow", "fad fa-link yellow");
        prop_tb.combi_subproperties_tb.setItemToolTip('combi_img_all_association','<?php echo _l('Assign all images to selected combination', 1); ?>');
        prop_tb.combi_subproperties_tb.addButton("combi_img_all_dissociation", 100, "", "fad fa-unlink red", "fad fa-unlink red");
        prop_tb.combi_subproperties_tb.setItemToolTip('combi_img_all_dissociation','<?php echo _l('Dissociate all images to selected combination', 1); ?>');
        <?php if (version_compare(_PS_VERSION_, '1.5.0.5', '>=')) {?>
        prop_tb.combi_subproperties_tb.addButton("thumbnail_regeneration", 100, "", "fad fa-tachometer-alt-fastest", "fad fa-tachometer-alt-fastest");
        prop_tb.combi_subproperties_tb.setItemToolTip('thumbnail_regeneration','<?php echo _l('Regenerate thumbnails from image selection', 1); ?>');
        <?php } ?>
        hideSubpropertiesItems();
        
        prop_tb.combi_subproperties_tb.attachEvent("onClick", function(id){
            if(id=='combi_img_add')
            {
                var products_ids = lastProductSelID;
                var products_attr_ids = prop_tb._combinationsGrid.getSelectedRowId();
                if (products_ids != undefined && products_ids!=0)
                {
                    if (!dhxWins.isWindow("wProductImages"+products_ids))
                    {
                        prop_tb._imagesUploadWindow[products_ids] = dhxWins.createWindow("prop_tb._imagesUploadWindow[products_ids]", 50, 50, 585, 400);

                        if(products_ids === parseInt(products_ids, 10))
                            prop_tb._imagesUploadWindow[products_ids].setText(cat_grid.cells(products_ids,idxProductName).getValue());
                        else
                            prop_tb._imagesUploadWindow[products_ids].setText('<?php echo _l('Upload images', 1); ?>');
                        
                        ll = new dhtmlXLayoutObject(prop_tb._imagesUploadWindow[products_ids], "2U");
                        ll.cells('a').hideHeader();
                        
                        // cell a
                        ll_toolbar=ll.cells('a').attachToolbar();
                        ll_toolbar.setIconset('awesome');
                        ll_toolbar.addButtonTwoState("auto_upload", 0, "", "fad fa-external-link green", "fad fa-external-link green");
                        ll_toolbar.setItemToolTip('auto_upload','<?php echo _l('If enabled: Images will be automatically uploaded once selected', 1); ?>');
                        ll_toolbar.setItemState('auto_upload', ($.cookie('sc_cat_img_auto_upload')==1?1:0));
                        
                        ll_toolbar.attachEvent("onStateChange", function(id,state){
                                if (id=='auto_upload'){
                                    var auto_upload = 0;
                                    if (state) {
                                      auto_upload=1;
                                    }else{
                                      auto_upload=0;
                                    }
                                    $.cookie('sc_cat_img_auto_upload',auto_upload, { expires: 60 , path: cookiePath});
                                }
                            });
        
                        ll.cells('a').attachURL("index.php?ajax=1&act=cat_image_upload&product_list="+products_ids+"&is_attr=1&attr_list="+products_attr_ids+"&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),function(data){});
                        prop_tb._imagesUploadWindow[products_ids].attachEvent("onClose", function(win){
                                prop_tb._imagesUploadWindow[products_ids].hide();
                                getCombinationsImages();
                                return false;
                            });

                        // cell b
						var _CombinationQrCodeCell = ll.cells('b');
						_CombinationQrCodeCell.hideHeader();
						_CombinationQrCodeCell.attachURL('index.php?ajax=1&act=cat_image_qrcode_get',null,{ids:lastProductSelID});
                    }else{
                        prop_tb._imagesUploadWindow[products_ids].show();
                    }
                }else{
                    alert('<?php echo _l('Please select a product', 1); ?>');
                }
            }
            if (id=='combi_img_del') {
                if (lastProductSelID == 0) {
                    alert('<?php echo _l('Please select a product', 1); ?>');
                    return false;
                }
                if (prop_tb._combinationsImagesGrid.getSelectedRowId() == null) {
                    alert('<?php echo _l('Please select an image', 1); ?>');
                    return false;
                }
                if (confirm('<?php echo _l('Are you sure you want to delete the selected items?', 1); ?>')) {
                    $.post("index.php?ajax=1&act=cat_image_update&action=delete&id_lang=" + SC_ID_LANG + "&" + new Date().getTime(), {
                        id_product: lastProductSelID,
                        list_id_image: prop_tb._combinationsImagesGrid.getSelectedRowId()
                    }, function () {
                        getCombinationsImages();
                    });
                }
            }
            if (id=='combi_img_all_association')
            {
                prop_tb._combinationsImagesGrid.checkAll(true);
                var state = true;
                var checkboxColumn = prop_tb._combinationsImagesGrid.getColIndexById('used');
                var imgsChecked = prop_tb._combinationsImagesGrid.getCheckedRows(checkboxColumn);
                $.post("index.php?ajax=1&act=cat_combination_image_update",{'action':"state",'state':state,'selection':prop_tb._combinationsGrid.getSelectedRowId(), 'ids':imgsChecked},function(data){
                    var v_scroll=prop_tb._combinationsImagesGrid.objBox.scrollTop;
                    getCombinationsImages('prop_tb._combinationsImagesGrid.objBox.scrollTop = "'+v_scroll+'";');
                });
            }
            if (id=='combi_img_all_dissociation')
            {
                if (cat_grid.getSelectedRowId()!=null && prop_tb._combinationsGrid.getSelectedRowId()!='' && confirm('<?php echo _l('Are you sure you want to dissociate the selected items?', 1); ?>'))
                {
                    prop_tb._combinationsImagesGrid.checkAll(true);
                    var checkboxColumn = prop_tb._combinationsImagesGrid.getColIndexById('used');
                    var imgsChecked = prop_tb._combinationsImagesGrid.getCheckedRows(checkboxColumn);
                    var state = false;
                    $.post("index.php?ajax=1&act=cat_combination_image_update",{'action':"state",'state':state,'selection':prop_tb._combinationsGrid.getSelectedRowId(), 'ids':imgsChecked},function(data){
                        var v_scroll=prop_tb._combinationsImagesGrid.objBox.scrollTop;
                        getCombinationsImages('prop_tb._combinationsImagesGrid.objBox.scrollTop = "'+v_scroll+'";');
                    });
                }
            }
            if (id == 'thumbnail_regeneration') {
                let imagelist = prop_tb._combinationsImagesGrid.getSelectedRowId();
                if (imagelist !== null) {
                    dhxLayout.cells('b').progressOn();
                    $.post("index.php?ajax=1&act=cat_image_update", {
                        'action': id,
                        'list_id_image': imagelist
                    }, function (response) {
                        dhxLayout.cells('b').progressOff();
                        let parsed_response = JSON.parse(response);
                        if (parsed_response.success == 'OK') {
                            dhtmlx.message({
                                text: '<?php echo _l('Thumbnails successfully regenerated.', 1); ?>',
                                type: 'success',
                                expire: 5000
                            });
                            getCombinationsImages();
                        } else {
                            dhtmlx.message({text: parsed_response.error, type: 'error', expire: 10000});
                        }
                    });
                }
            }
        });
        
        needInitCombinationsImage = 0;
    }


    prop_tb.combi_subproperties_tb.showItem('combi_img_add');
    prop_tb.combi_subproperties_tb.showItem('combi_img_del');
    prop_tb.combi_subproperties_tb.showItem('combi_img_all_association');
    prop_tb.combi_subproperties_tb.showItem('combi_img_all_dissociation');
    prop_tb.combi_subproperties_tb.showItem('thumbnail_regeneration');
    prop_tb.combi_subproperties_image_layout = prop_tb._combinationsLayout.cells('b').attachLayout('3T');
    prop_tb.combi_subproperties_image_layout.setAutoSize("a;b", "a");
    prop_tb._combinationsLayout.cells('b').showHeader();
    prop_tb.combi_subproperties_image_layout.cells('a').hideHeader();
    prop_tb._combinationsImagesGrid = prop_tb.combi_subproperties_image_layout.cells('a').attachGrid();
    prop_tb._combinationsImagesGrid.setImagePath("lib/js/imgs/");
    prop_tb._combinationsImagesGrid._lastCombination=0;


    /* Add Image fast */
    prop_tb.combi_subproperties_image_layout.cells('a').setHeight(Number(prop_tb._combinationsLayout.cells('b').conf.size.h-320));
    prop_tb._combi_imageFastUpload = prop_tb.combi_subproperties_image_layout.cells('b');
    prop_tb._combi_imageFastMobileTabletUpload = prop_tb.combi_subproperties_image_layout.cells('c');
    prop_tb._combi_imageFastUpload.hideHeader();
    prop_tb._combi_imageFastMobileTabletUpload.hideHeader();

    prop_tb._combinationsImagesGrid.attachEvent("onCheck", function(rId,cInd,state){
        $.post("index.php?ajax=1&act=cat_combination_image_update",{'action':"state",'state':state,'ids':rId,'selection':prop_tb._combinationsGrid.getSelectedRowId()},function(data){
            var v_scroll=prop_tb._combinationsImagesGrid.objBox.scrollTop;
            getCombinationsImages('prop_tb._combinationsImagesGrid.objBox.scrollTop = "'+v_scroll+'";');
        });
    });
    prop_tb._combinationsImagesGrid.attachEvent("onRowSelect", function(rId,cInd){
        let idxImage = prop_tb._combinationsImagesGrid.getColIndexById('image');
        if(Number(cInd) === idxImage){
            $.get("index.php?ajax=1&act=cat_combination_image_relation_get&id_image="+rId, function(data){
                prop_tb._combinationsGrid.clearSelection();
                list=data.split(',');
                prop_tb._combinationsGrid.forEachRow(function(id){
                    if (in_array(id,list)) {
                        prop_tb._combinationsGrid.selectRowById(id, true);
                    }
                });
                if (prop_tb._combinationsGrid.getSelectedRowId()){
                    var v_scroll=prop_tb._combinationsImagesGrid.objBox.scrollTop;
                    getCombinationsImages('prop_tb._combinationsImagesGrid.objBox.scrollTop = "'+v_scroll+'";');
                }
            });
        }
    });

    prop_tb._combinationsImagesGrid.attachEvent("onEditCell", function(stage,rId,cInd,nValue,oValue){
        let idxLegend = prop_tb._combinationsImagesGrid.getColIndexById('legend');
        if(Number(cInd) === idxLegend){
            if(nValue !== oValue) {
                $.post("index.php?ajax=1&act=cat_combination_image_update",{'action':"legend",'id_lang':SC_ID_LANG,'ids':rId,'legend':nValue},function(data){
                    var v_scroll=prop_tb._combinationsImagesGrid.objBox.scrollTop;
                    getCombinationsImages('prop_tb._combinationsImagesGrid.objBox.scrollTop = "'+v_scroll+'";');
                });
            }
        }
        return true;
    });

    // UISettings
    prop_tb._combinationsImagesGrid._uisettings_prefix='cat_combi_image';
    prop_tb._combinationsImagesGrid._uisettings_name=prop_tb._combinationsImagesGrid._uisettings_prefix;
    prop_tb._combinationsImagesGrid._first_loading=1;

    // UISettings
    initGridUISettings(prop_tb._combinationsImagesGrid);
    prop_tb._combi_imageFastMobileTabletUpload.setWidth('200');
    prop_tb._combi_imageFastMobileTabletUpload.fixSize(true, false);
    getCombinationsImages();

}

function displayCombiFastUpload()
{
    let products_attr_ids = prop_tb._combinationsGrid.getSelectedRowId();
    prop_tb._combi_imageFastUpload.attachURL("index.php?ajax=1&act=cat_image_upload&product_list="+lastProductSelID+"&is_attr=1&attr_list="+products_attr_ids+"&id_lang="+SC_ID_LANG);
}

function displayCombiFastMobileTabletUpload()
{
    prop_tb._combi_imageFastMobileTabletUpload.attachURL('index.php?ajax=1&act=cat_image_qrcode_get',null,{ids:lastProductSelID});
}


function getCombinationsImages(callback)
{
    if (prop_tb._combinationsGrid.getSelectedRowId() && prop_tb._combinationsGrid.getSelectedRowId().substr(0,3)!='NEW')
    {
        prop_tb._combinationsImagesGrid._lastCombination=prop_tb._combinationsGrid.getSelectedRowId();
        prop_tb._combinationsImagesGrid.load("index.php?ajax=1&act=cat_combination_image_get&id_lang="+SC_ID_LANG+"&id_product="+lastProductSelID+"&selection="+prop_tb._combinationsGrid.getSelectedRowId()+"&"+new Date().getTime(),function(){
            // UISettings
            loadGridUISettings(prop_tb._combinationsImagesGrid);
            prop_tb._combinationsImagesGrid._first_loading=0;



            if (typeof(callback)=='undefined') callback='';
            eval(callback);
        });
    }else if(prop_tb._combinationsGrid.getSelectedRowId()==null){
        prop_tb._combinationsImagesGrid._lastCombination=0;
        prop_tb._combinationsImagesGrid.clearAll();
    }
}



