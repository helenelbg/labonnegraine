<?php
 if (_r('GRI_CAT_PROPERTIES_GRID_IMG')) { ?>
        prop_tb.addListOption('panel', 'images', 3, "button", '<?php echo _l('Images', 1); ?>', "fad fa-image");
        allowed_properties_panel[allowed_properties_panel.length] = "images";
<?php } ?>

    prop_tb.addButton("image_refresh",1000, "", "fa fa-sync green", "fa fa-sync green");
    prop_tb.setItemToolTip('image_refresh','<?php echo _l('Refresh grid', 1); ?>');
    prop_tb.addButton("image_add",1000, "", "fa fa-plus-circle green", "fa fa-plus-circle green");
    prop_tb.setItemToolTip('image_add','<?php echo _l('Upload new images', 1); ?>');
<?php
if (version_compare(_PS_VERSION_, '1.5.0.0', '<') || version_compare(_PS_VERSION_, '1.5.6.1', '>='))
 {
     $sql = 'SELECT COUNT(*) AS cnt FROM '._DB_PREFIX_.'lang';
     $res = Db::getInstance()->getValue($sql);
     if ($res > 1)
     {
         ?>
    var opts = [['lang_select', 'obj', '<?php echo _l('Assign product name to selected image(s) legend', 1); ?>', ''],
        ['separator1', 'sep', '', ''],
        ['image_fill_legend_current_lang', 'obj', '<?php echo _l('For the current language'); ?>', ''],
        ['image_fill_legend_all_lang', 'obj', '<?php echo _l('For all languages'); ?>', '']
        ];
    prop_tb.addButtonSelect("image_fill_legend", 1000, "",opts, "fad fa-key yellow", "fad fa-key yellow",false,true);
    prop_tb.setItemToolTip("image_fill_legend","<?php echo _l('Assign product name to selected image(s) legend', 1); ?>");

<?php
     }
     else
     {
         ?>
    prop_tb.addButton("image_fill_legend",1000, "", "fad fa-key yellow", "fad fa-key yellow");
    prop_tb.setItemToolTip('image_fill_legend','<?php echo _l('Assign product name to selected image(s) legend', 1); ?>');
<?php
     }
 }
?>
    prop_tb.addButton("image_setposition",1000, "", "fa fa-list-ol green", "fa fa-list-ol grey");
    prop_tb.setItemToolTip('image_setposition','<?php echo _l('Save image positions', 1); ?>');
    <?php if (_r('ACT_CAT_DELETE_IMAGE')) { ?>
        prop_tb.addButton('image_del',1000,'','fa fa-minus-circle red','fa fa-minus-circle red');
        prop_tb.setItemToolTip('image_del','<?php echo _l('Delete selected images', 1); ?>');
    <?php } ?>
    prop_tb.addButton("image_selectall",1000, "", "fa fa-bolt yellow", "fad fa-bolt grey");
    prop_tb.setItemToolTip('image_selectall','<?php echo _l('Select all images', 1); ?>');

    <?php if (KAI9DF4 != 1) { ?>
    var opts = [
    ['image_eservices_cutout', 'obj', '<?php echo _l('Cut the selected image out', 1); ?>', 'fad fa-game-board-alt fa-rotate-90'],
    ['image_eservices_cutout_addproject', 'obj', '<?php echo _l('Add selected images in projet to cut them out', 1); ?>', 'fad fa-game-board-alt fa-rotate-90'],
    ['image_eservices_cutout_seeproject', 'obj', '<?php echo _l('See cut out project', 1); ?>', 'fad fa-game-board-alt fa-rotate-90']
    ];
    prop_tb.addButtonSelect("image_eservices_list", 1000, "",opts, "fa fa-gem red", "fa fa-gem red",false,true);
    prop_tb.setItemToolTip("image_eservices_list","<?php echo _l('e-Services', 1); ?>");
    <?php } ?>


    <?php if (version_compare(_PS_VERSION_, '1.5.0.5', '>=')) {?>
    prop_tb.addButton("thumbnail_regeneration",1000, "", "fad fa-tachometer-alt-fastest", "fad fa-tachometer-alt-fastest");
    prop_tb.setItemToolTip('thumbnail_regeneration','<?php echo _l('Regenerate thumbnails from image selection', 1); ?>');
    <?php } ?>

    prop_tb.addButtonTwoState('image_detail', 1000, "", "fad fa-ruler-combined", "fad fa-ruler-combined");
    prop_tb.setItemToolTip('image_detail','<?php echo _l('Show image details', 1); ?>');

    getTbSettingsButton(prop_tb, {'grideditor':1,'settings':1}, 'prop_image_',1000);

    prop_tb._imagesUploadWindow=new Array();

    var product_shop_default = 0;

    clipboardType_Images = null;
    needInitImages = 1;
    function initImages(){
        if (needInitImages)
        {
            prop_tb._imagesLayout = dhxLayout.cells('b').attachLayout('3T');
            prop_tb._imagesLayout.cells('a').hideHeader();
            dhxLayout.cells('b').showHeader();
            prop_tb._imagesGrid = prop_tb._imagesLayout.cells('a').attachGrid();
            prop_tb._imagesGrid.setImagePath("lib/js/imgs/");
            prop_tb._imagesGrid.enableDragAndDrop(true);
            prop_tb._imagesGrid.setDragBehavior('child');
            prop_tb._imagesGrid.enableMultiselect(true);
            

            /* Add Image fast */
            prop_tb._imagesLayout.cells('a').setHeight(Number(dhxLayout.cells('b').conf.size.h-320));
            prop_tb._imageFastUpload = prop_tb._imagesLayout.cells('b');
            prop_tb._imageFastMobileTabletUpload = prop_tb._imagesLayout.cells('c');
            prop_tb._imageFastUpload.hideHeader();
            prop_tb._imageFastMobileTabletUpload.hideHeader();

            // UISettings
            prop_tb._imagesGrid._uisettings_prefix='cat_image';
            prop_tb._imagesGrid._uisettings_name=prop_tb._imagesGrid._uisettings_prefix;
            prop_tb._imagesGrid._uisettings_limited=true;
               prop_tb._imagesGrid._first_loading=1;
               
            // UISettings
            initGridUISettings(prop_tb._imagesGrid);
            
            function onEditCellImage(stage,rId,cInd,nValue,oValue){
                    idxLegend=prop_tb._imagesGrid.getColIndexById('legend');
                    idxCover=prop_tb._imagesGrid.getColIndexById('cover');
                    <?php sc_ext::readCustomImageGridConfigXML('onEditCell'); ?>
                    if (cInd == idxLegend){
                        col='legend';
                        if(stage==2)
                        {
                            prop_tb._imagesGrid.setRowTextBold(rId);
                            <?php sc_ext::readCustomImageGridConfigXML('onBeforeUpdate'); ?>
                            $.post("index.php?ajax=1&act=cat_image_update&action=update&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{'id_product': lastProductSelID, col: col, val: nValue.replace(/#/g,''), 'list_id_image':rId},function(data){
                                <?php sc_ext::readCustomImageGridConfigXML('onAfterUpdate'); ?>
                                prop_tb._imagesGrid.setRowTextNormal(rId);
                            });
                        }
                    }
<?php if (SCMS) { ?>
                    if (cInd == idxCover){
                        if (prop_tb._imagesGrid.cells(rId,prop_tb._imagesGrid.getColIndexById("shop_"+shopselection)).getValue()=='0')
                            return false;
                    }
<?php } ?>
                    return true;
            }
            prop_tb._imagesGrid.attachEvent("onEditCell",onEditCellImage);
            function onCheckImage(rId,cInd,state)
            {
                var cId=prop_tb._imagesGrid.getColumnId(cInd);
                if (cId!='cover')
                {
                    var shop = cId.replace("shop_", "");
                    $.post("index.php?ajax=1&act=cat_image_update&action=shop&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{'id_product': lastProductSelID, 'list_id_image':rId,shop:shop,val:Number(state), is_cover: prop_tb._imagesGrid.getUserData(rId,'cover')},function(data){});
                }else{
                    $.post("index.php?ajax=1&act=cat_image_update&action=update&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{'id_product': lastProductSelID, 'list_id_image':rId,col:'cover',val:Number(state)},function(data){});
                }
            }
            prop_tb._imagesGrid.attachEvent("onCheck", onCheckImage);
            needInitImages=0;
            
            
            // Context menu for grid
            images_cmenu=new dhtmlXMenuObject();
            images_cmenu.renderAsContextMenu();
            function onGridImagesContextButtonClick(itemId) {
                tabId = prop_tb._imagesGrid.contextID.split('_');
                tabId = tabId[0];
                switch(itemId) {
                    case "copy":
                        if (lastColumnRightClicked_Images != 0) {
                            clipboardValue_Images = prop_tb._imagesGrid.cells(tabId, lastColumnRightClicked_Images).getValue();
                            images_cmenu.setItemText('paste', '<?php echo _l('Paste'); ?> ' + clipboardValue_Images);
                            clipboardType_Images = lastColumnRightClicked_Images;
                        }
                        break;
                    case "paste":
                        if (lastColumnRightClicked_Images != 0 && clipboardValue_Images != null && clipboardType_Images == lastColumnRightClicked_Images) {
                            selection = prop_tb._imagesGrid.getSelectedRowId();
                            idxLegend = prop_tb._imagesGrid.getColIndexById('legend');
                            if (selection != '' && selection != null) {
                                for(const row_id of selection.split(',')){
                                    prop_tb._imagesGrid.cells(row_id, lastColumnRightClicked_Images).setValue(clipboardValue_Images);
                                    if (lastColumnRightClicked_Images == idxLegend) {
                                        onEditCellImage(2, row_id, lastColumnRightClicked_Images, clipboardValue_Images, null);
                                    }else {
                                        onCheckImage(row_id, lastColumnRightClicked_Images, clipboardValue_Images);
                                    }
                                }
                            }
                        }
                        break;
                }
            }
            images_cmenu.attachEvent("onClick", onGridImagesContextButtonClick);
            var contextMenuXML='<menu absolutePosition="auto" mode="popup" maxItems="8"  globalCss="contextMenu" globalSecondCss="contextMenu" globalTextCss="contextMenuItem">'+
                    '<item text="Object" id="object" enabled="false"/>'+
                    '<item text="<?php echo _l('Copy'); ?>" id="copy"/>'+
                    '<item text="<?php echo _l('Paste'); ?>" id="paste"/>'+
                '</menu>';
            images_cmenu.loadStruct(contextMenuXML);
            prop_tb._imagesGrid.enableContextMenu(images_cmenu);

            prop_tb._imagesGrid.attachEvent("onBeforeContextMenu", function(rowid,colidx,grid){
                var disableOnCols=new Array(
                        prop_tb._imagesGrid.getColIndexById('id_product'),
                        prop_tb._imagesGrid.getColIndexById('id_image'),
                        prop_tb._imagesGrid.getColIndexById('image'),
                        prop_tb._imagesGrid.getColIndexById('reference'),
                        prop_tb._imagesGrid.getColIndexById('name'),
                        prop_tb._imagesGrid.getColIndexById('position'),
                        prop_tb._imagesGrid.getColIndexById('cover')
                        );
                if (in_array(colidx,disableOnCols))
                {
                    return false;
                }
                lastColumnRightClicked_Images=colidx;
                images_cmenu.setItemText('object', '<?php echo _l('Image:'); ?> '+prop_tb._imagesGrid.cells(rowid,prop_tb._imagesGrid.getColIndexById('id_image')).getTitle());
                if (lastColumnRightClicked_Images==clipboardType_Images)
                {
                    images_cmenu.setItemEnabled('paste');
                }else{
                    images_cmenu.setItemDisabled('paste');
                }
                return true;
            });
        }
    }



    function setPropertiesPanel_images(id){
        if (id=='images')
        {
            if(lastProductSelID!=undefined && lastProductSelID!="")
            {
                dhxLayout.cells('b').setText('<?php echo _l('Properties', 1).' '._l('of', 1); ?> '+getSelectedItemValueOrID(cat_grid,lastProductSelID,'name'));
            }
            hidePropTBButtons();
            prop_tb.showItem('image_del');
            prop_tb.showItem('image_selectall');

            prop_tb.showItem('image_setposition');
            prop_tb.showItem('image_add');
            prop_tb.showItem('image_refresh');
            prop_tb.showItem('image_eservices_list');
            prop_tb.showItem('thumbnail_regeneration');
            prop_tb.showItem('image_detail');
            prop_tb.showItem('prop_image_settings_menu');

<?php
if (version_compare(_PS_VERSION_, '1.5.0.0', '<') || version_compare(_PS_VERSION_, '1.5.6.1', '>='))
{
    ?>
            prop_tb.showItem('image_fill_legend');
<?php
}
?>
            prop_tb.setItemText('panel', '<?php echo _l('Images', 1); ?>');
            prop_tb.setItemImage('panel', 'fad fa-image');
            needInitImages=1;
            initImages();
            propertiesPanel='images';
            if (lastProductSelID!=0)
            {
                displayImages();
                displayFastUpload();
                displayFastMobileTabletUpload();
            }
        }
        if (id=='image_refresh'){
            displayImages();
        }
        if (id=='image_selectall'){
            prop_tb._imagesGrid.selectAll();
        }
        if (id=='prop_image_grideditor'){
            openWinGridEditor('type_image');
        }
        if (id=='prop_image_settings'){
            openSettingsWindow('Catalog','Image');
        }

        if (id=='image_add'){
            var products_ids = cat_grid.getSelectedRowId();
            if (products_ids != undefined && products_ids!=0)
            {
                if (!dhxWins.isWindow("wProductImages"+products_ids))
                {
                    prop_tb._imagesUploadWindow[products_ids] = dhxWins.createWindow("prop_tb._imagesUploadWindow[products_ids]", 50, 50, 600, 450);
                    if(products_ids.search(",")<0)
                            prop_tb._imagesUploadWindow[products_ids].setText('<?php echo _l('Upload images', 1); ?>: '+getSelectedItemValueOrID(cat_grid,products_ids,'name'));
                        else
                            prop_tb._imagesUploadWindow[products_ids].setText('<?php echo _l('Upload images', 1); ?>');
                    ll = new dhtmlXLayoutObject(prop_tb._imagesUploadWindow[products_ids], "1C");
                    ll.cells('a').hideHeader();
                    
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
    
                    ll.cells('a').attachURL("index.php?ajax=1&act=cat_image_upload&"+new Date().getTime(),null,{'product_list':products_ids,'id_lang':SC_ID_LANG});
                    prop_tb._imagesUploadWindow[products_ids].attachEvent("onClose", function(win){
                            win.hide();
                            displayImages();
                            return false;
                        });
                }else{
                    prop_tb._imagesUploadWindow[products_ids].show();
                }
            }else{
                alert('<?php echo _l('Please select a product', 1); ?>');
            }
        }
        if (id=='image_setposition'){
            if (prop_tb._imagesGrid.getRowsNum()>0 && lastProductSelID!=0)
            {
                var positions='';
                var idx=0;
                var i = 1 ;
                prop_tb._imagesGrid.forEachRow(function(id){
                        positions+=id+','+prop_tb._imagesGrid.getRowIndex(id)+';';
                        idx++;
                    });
                $.post("index.php?ajax=1&act=cat_image_update&action=position&"+new Date().getTime(),{ id_product: lastProductSelID, positions: positions },function(){
                        idxPosition=prop_tb._imagesGrid.getColIndexById('position');
                        displayImages('prop_tb._imagesGrid.sortRows('+idxPosition+', "int", "asc");');
                    });
            }
        }
        if (id=='image_del')
        {
            let imgSelection = prop_tb._imagesGrid.getSelectedRowId();
            if (imgSelection==null)
            {
                alert('<?php echo _l('Please select an image', 1); ?>');
                return false;
            }
            let lastProductSelected = lastProductSelID;
            if(lastProductSelected < 1 && cat_grid.getSelectedRowId() == null)
            {
                alert('<?php echo _l('Please select a product', 1); ?>');
                return false;
            }

            if(lastProductSelected < 1){
                lastProductSelected = cat_grid.getSelectedRowId();
                lastProductSelected = lastProductSelected.split(',');
                lastProductSelected = lastProductSelected[lastProductSelected.length - 1];
            }

            if (confirm('<?php echo _l('Are you sure you want to delete the selected items?', 1); ?>'))
            {
                $.post("index.php?ajax=1&act=cat_image_update&action=delete",{
                    id_product: lastProductSelected,
                    list_id_image: imgSelection
                },function(){
                    displayImages();
                });
            }
        }
        if (id=='image_fill_legend_current_lang' || id=='image_fill_legend')
        {
            if (prop_tb._imagesGrid.getSelectedRowId()==null)
            {
                alert('<?php echo _l('Please select an image', 1); ?>');
            }else{
                if (lastProductSelID!=0)
                {
                    if (confirm('<?php echo _l('Are you sure you want to change the legend of the selected image(s) with the product name for the current language?', 1); ?>'))
                    {
                        list=prop_tb._imagesGrid.getSelectedRowId().split(',');
                        idxLegend=prop_tb._imagesGrid.getColIndexById('legend');
                        idxProductName=cat_grid.getColIndexById('name');
                        $.post("index.php?ajax=1&act=cat_image_update&action=image_fill_legend_current_lang&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{'id_products': cat_grid.getSelectedRowId(), 'list_id_image':prop_tb._imagesGrid.getSelectedRowId()},function(){

                        });
                        for(i=0 ; i < list.length ; i++)
                        {
                            prop_tb._imagesGrid.cells(list[i],idxLegend).setValue(cat_grid.cells(lastProductSelID,idxProductName).getValue().replace(/#/g,''));
                        }
                    }
                }else{
                    alert('<?php echo _l('Please select a product', 1); ?>');
                }
            }
        }
        if (id=='image_fill_legend_all_lang')
        {
            if (prop_tb._imagesGrid.getSelectedRowId()==null)
            {
                alert('<?php echo _l('Please select an image', 1); ?>');
            }else{
                if (lastProductSelID!=0)
                {
                    if (confirm('<?php echo _l('Are you sure you want to change the legend of the selected image(s) with the product name for all languages?', 1); ?>'))
                    {
                        list=prop_tb._imagesGrid.getSelectedRowId().split(',');
                        idxLegend=prop_tb._imagesGrid.getColIndexById('legend');
                        idxProductName=cat_grid.getColIndexById('name');
                        $.post("index.php?ajax=1&act=cat_image_update&action=image_fill_legend_all_lang&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{'id_products': cat_grid.getSelectedRowId(), 'list_id_image':prop_tb._imagesGrid.getSelectedRowId()},function(){

                        });
                        for(i=0 ; i < list.length ; i++)
                        {
                            prop_tb._imagesGrid.cells(list[i],idxLegend).setValue(cat_grid.cells(lastProductSelID,idxProductName).getValue().replace(/#/g,''));
                        }
                    }
                }else{
                    alert('<?php echo _l('Please select a product', 1); ?>');
                }
            }
        }
        if(id=='image_eservices_cutout_addproject')
        {
            list=prop_tb._imagesGrid.getSelectedRowId();
            if(list!="")
            {
                $.post("index.php?ajax=1&act=cat_image_cutout_addproject&id_image="+list+"&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),function(data){
                    if(data!=undefined && data=="OK")
                        dhtmlx.message({text:'<?php echo _l('Images added in project.', 1); ?>',type:'success',expire:10000});
                    else
                        dhtmlx.message({text:'<?php echo _l('An error occured. Please contact our support.', 1); ?>',type:'error',expire:15000});
                });
            }
        }
        if(id=='image_eservices_cutout_seeproject')
        {
            loadWindoweServicesProject("cutout");
        }
        if(id=='image_eservices_cutout')
        {
            list=prop_tb._imagesGrid.getSelectedRowId().split(',');
            if(list.length==1)
            {
                dhxLayout.cells('b').progressOn();
                $.post("index.php?ajax=1&act=cat_image_cutout_upload&id_image="+list[0]+"&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),function(data){
                    if(data!=undefined && data!=null && data!="" && data!=0)
                    {
                        dhxLayout.cells('b').progressOff();
                        if(data.type=="error")
                            dhtmlx.message({text:data.message,type:'error',expire:15000});
                        else
                        {
                            ClippingMagic.edit({
                                "image" : {
                                    "id" : data.id,
                                    "secret" : data.secret
                                },
                                "locale" : "<?php echo $user_lang_iso == 'fr' ? 'fr-FR' : 'en-US'; ?>"
                            }, callbackCutOut);
                        }
                    }
                    else
                        dhtmlx.message({text:'<?php echo _l('An error occured. Please contact our support.', 1); ?>',type:'error',expire:15000});
                },'JSON');
            }
            else if(list.length>1)
            {
                dhtmlx.message({text:'<?php echo _l('You must select only one image', 1); ?>',type:'error',expire:15000});
            }
            else if(list.length==0)
            {
                dhtmlx.message({text:'<?php echo _l('You must select one image', 1); ?>',type:'error',expire:15000});
            }
        }
        if (id == 'thumbnail_regeneration') {
            let imagelist = prop_tb._imagesGrid.getSelectedRowId();
            if (imagelist !== null) {
                dhxLayout.cells('b').progressOn();
                $.post("index.php?ajax=1&act=cat_image_update", {
                    'action': id,
                    'list_id_image': imagelist
                }, function (response) {
                    dhxLayout.cells('b').progressOff();
                    let parsed_response = JSON.parse(response);
                    for( const [id_image, img_response] of Object.entries(parsed_response)) {
                        if (img_response.success === 'OK') {
                            dhtmlx.message({
                                text: '<?php echo ucfirst(_l('image', 1)); ?>#'+id_image+' <?php echo _l('Thumbnails successfully regenerated.', 1); ?>',
                                type: 'success',
                                expire: 5000
                            });
                            displayImages();
                        } else {
                            dhtmlx.message({text:'<?php echo ucfirst(_l('image', 1)); ?>#'+id_image+' '+img_response.error, type: 'error', expire: 10000});
                        }
                    }
                });
            }
        }
    }
    prop_tb.attachEvent("onClick", setPropertiesPanel_images);
    prop_tb.attachEvent("onStateChange", function(id,state){
        if (id == 'image_detail') {
            displayImages();
        }
    });
    

function displayImages(callback)
{
    prop_tb._imagesGrid.clearAll(true);
    $.post("index.php?ajax=1&act=cat_image_get",{
        id_product:cat_grid.getSelectedRowId(),
        id_lang:SC_ID_LANG,
        showImageDetail:prop_tb.getItemState('image_detail')
    },function(data)
    {
        prop_tb._imagesGrid.parse(data);
        nb=prop_tb._imagesGrid.getRowsNum();
        prop_tb._sb.setText(nb+(nb>1?" <?php echo _l('images'); ?>":" <?php echo _l('image'); ?>"));

           // UISettings
        loadGridUISettings(prop_tb._imagesGrid);
        prop_tb._imagesGrid._first_loading=0;

        <?php sc_ext::readCustomImageGridConfigXML('afterGetRows'); ?>

        if (callback!='') eval(callback);
    });
}

function displayFastUpload()
{
    prop_tb._imageFastUpload.attachURL("index.php?ajax=1&act=cat_image_upload&product_list="+cat_grid.getSelectedRowId()+"&id_lang="+SC_ID_LANG);
}

function displayFastMobileTabletUpload()
{
    prop_tb._imageFastMobileTabletUpload.attachURL('index.php?ajax=1&act=cat_image_qrcode_get',null,{ids:cat_grid.getSelectedRowId()});
}

function callbackCutOut(opts)
{
    if(opts!=undefined && opts!=null && opts!="")
    {
        if(opts.event=="result-generated")
        {
            dhxLayout.cells('b').progressOn();
            $.post("index.php?ajax=1&act=cat_image_cutout_payment&id_image_cutout="+opts.image.id+"&id_image="+prop_tb._imagesGrid.getSelectedRowId()+"&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),function(data){
                dhxLayout.cells('b').progressOff();
                displayImages();
                if(data==undefined || (data!=undefined && data!="OK"))
                {
                    dhtmlx.message({text:'<?php echo _l('An error occured. Please contact our support.', 1); ?>',type:'error',expire:15000});
                }
            });
        }
    }
}

    let images_current_id = 0;
    cat_grid.attachEvent("onRowSelect",function (idproduct){
        if (propertiesPanel=='images' && (cat_grid.getSelectedRowId()!==null && images_current_id!=idproduct)){
            initImages();
            displayImages();
            displayFastUpload();
            displayFastMobileTabletUpload();
            images_current_id=idproduct;
            if (dhxWins.isWindow("wQrCodeImageImporter")) {
                // refresh qrcode
                $.get("index.php?ajax=1&act=cat_image_qrcode_init&id_lang="+SC_ID_LANG,function(data){
                    $('#jsExecute').html(data);
                });
            }
        }
    });
