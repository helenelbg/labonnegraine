<?php
if (_r('GRI_CAT_PROPERTIES_GRID_DESC')) { ?>
        prop_tb.addListOption('panel', 'descriptions', 2, "button", '<?php echo _l('Descriptions', 1); ?>', "fad fa-align-left");
        allowed_properties_panel[allowed_properties_panel.length] = "descriptions";
    prop_tb.addButton("description_refresh",1000, "", "fa fa-sync green", "fa fa-sync green");
    prop_tb.setItemToolTip('description_refresh','<?php echo _l('Refresh', 1); ?>');
    prop_tb.addButton('desc_save',1000,'','fa fa-save blue','fa fa-save blue');
    prop_tb.setItemToolTip('desc_save','<?php echo _l('Save descriptions', 1); ?>');
    prop_tb.addText('txt_descriptionsize', 1000, '<?php echo _l('Short description charset')._l(':').' '.'0/'._s('CAT_SHORT_DESC_SIZE'); ?>');
    <?php if (_r('INT_CAT_PROPERTIES_DESC_SHOW_DESC_SHORT')) { ?>
    prop_tb.addButtonTwoState('desc_twodesc', 1000, "", "fad fa-text-height blue", "fad fa-text-height blue");
    prop_tb.setItemToolTip('desc_twodesc','<?php echo _l('Display all descriptions', 1); ?>');
    <?php } ?>
    prop_tb.addButtonTwoState('desc_3cols', 1000, "", "fa fa-bars fa-rotate-90 green", "fa fa-bars fa-rotate-90 green");
    prop_tb.setItemToolTip('desc_3cols','<?php echo _l('Display on 3 columns', 1); ?>');
    <?php if (SC_CREATIVE_ELEMENTS_ACTIVE){ ?>
    prop_tb.addButton('edit_description_with_ce',1000, "", "ce_editor_link", "ce_editor_link");
    prop_tb.setItemToolTip('edit_description_with_ce','<?php echo _l('Edit with CreativeElements', 1); ?>');
    <?php } ?>

    not_save = 0;

    needInitDescriptions = 1;
    function initDescriptions(){
        if (needInitDescriptions)
        {
            <?php
            if (count($languages) > 1) {?>
                prop_tb._descriptionsLayout = dhxLayout.cells('b').attachLayout('2U');
            <?php }
            else
            { ?>
                prop_tb._descriptionsLayout = dhxLayout.cells('b').attachLayout('1C');
            <?php } ?>
            prop_tb._descriptionsLayout.cells('a').hideHeader();
            <?php if (_s('APP_RICH_EDITOR') == 1) { ?>
            prop_tb._descriptionsLayout.cells('a').attachURL('index.php?ajax=1&act=cat_description_tinymce'+URLOptions);
            <?php }
            else
            { ?>
            prop_tb._descriptionsLayout.cells('a').attachURL('index.php?ajax=1&act=cat_description_ckeditor'+URLOptions);
            <?php } ?>
            dhxLayout.cells('b').showHeader();

            // subproperties
            <?php
            if (count($languages) > 1) {?>
            if(getParamUISettings('start_cat_description') == 1) {
                prop_tb._descriptionsLayout.cells('b').collapse();
            }
            actual_subproperties = "desc_translation";
            prop_tb._descriptionsLayout.cells('b').setWidth(200);
                prop_4column_layout = prop_tb._descriptionsLayout;
            prop_tb._descriptionsLayout.cells('b').setText("<?php echo _l('Description'); ?>");
            prop_tb.desc_subproperties_tb=prop_tb._descriptionsLayout.cells('b').attachToolbar();
            prop_tb.desc_subproperties_tb.setIconset('awesome');
            var opts = new Array();
            prop_tb.desc_subproperties_tb.addButtonSelect("descSubProperties", 0, "<?php echo _l('Translation'); ?>", opts, "fad fa-align-left", "fad fa-align-left",false,true);
            hideDescSubpropertiesItems();
            <?php } ?>

            prop_tb._descriptionsLayout.attachEvent("onCollapse", function(name){
                saveParamUISettings('start_cat_description', 1);
            });
            prop_tb._descriptionsLayout.attachEvent("onExpand", function(name){
                saveParamUISettings('start_cat_description', 0);
            });

            needInitDescriptions=0;
        }
    }

    function hideDescSubpropertiesItems()
    {
        prop_tb.desc_subproperties_tb.forEachItem(function(itemId){
            if(itemId!="descSubProperties") {
                prop_tb.desc_subproperties_tb.hideItem(itemId);
            }
        });
    }

    function setPropertiesPanel_descriptions(id){
        // ask to save description if modified
        if (propertiesPanel=='descriptions' && id!='desc_save' && id!='desc_twodesc' && typeof prop_tb._descriptionsLayout!='undefined')
            checkDescriptionsBeforeChangeRow();

        if (id=='descriptions')
        {
            hidePropTBButtons();
            prop_tb.showItem('description_refresh');
            prop_tb.showItem('desc_save');
            <?php if (_r('INT_CAT_PROPERTIES_DESC_SHOW_DESC_SHORT')) { ?>
            prop_tb.showItem('desc_twodesc');
            <?php } ?>
            <?php if (SC_CREATIVE_ELEMENTS_ACTIVE){ ?>
            prop_tb.showItem('edit_description_with_ce');
            <?php } ?>
            prop_tb.showItem('desc_3cols');
            prop_tb.showItem('txt_descriptionsize');
            <?php if (_r('INT_CAT_PROPERTIES_DESC_SHOW_DESC_SHORT')) { ?>
            prop_tb.setItemState("desc_twodesc", 0);
            <?php } ?>
            prop_tb.setItemText('panel', '<?php echo _l('Descriptions', 1); ?>');
            prop_tb.setItemImage('panel', 'fad fa-align-left');
            URLOptions='';
            if (lastProductSelID!=0) URLOptions='&id_product='+lastProductSelID+'&id_lang='+SC_ID_LANG;
            needInitDescriptions = 1;
            is_prop_4columns = true;
            initDescriptions();
            <?php if (count($languages) > 1) {?>
            needInitDescriptionTranslation=1;
            initDescriptionTranslation();
            <?php } ?>
            propertiesPanel='descriptions';
            <?php if (_s('CAT_PROPERTIES_DESCRIPTION_AUTO_SIZING') == 1) { ?>
                dhxLayout.cells('b').setWidth(680);
            <?php } ?>
        }

        <?php if (SC_CREATIVE_ELEMENTS_ACTIVE){ ?>
        if(id=='edit_description_with_ce'){
            openCreativeElements(lastProductSelID, '<?php echo CE\UId::PRODUCT; ?>');
        }
        <?php } ?>
        if (id=='desc_save')
        {
            not_save = 0;
            <?php if (_s('APP_RICH_EDITOR') != 1) { ?>
            prop_tb._descriptionsLayout.cells('a').progressOn();
            <?php } ?>
            prop_tb._descriptionsLayout.cells('a').getFrame().contentWindow.ajaxSave();
        }
        if (id=='description_refresh')
        {
            if (lastProductSelID!=0) URLOptions='&id_product='+lastProductSelID+'&id_lang='+SC_ID_LANG;
            <?php if (_s('APP_RICH_EDITOR') == 1) { ?>
                prop_tb._descriptionsLayout.cells('a').attachURL('index.php?ajax=1&act=cat_description_tinymce'+URLOptions);
            <?php }
            else
            { ?>
                prop_tb._descriptionsLayout.cells('a').attachURL('index.php?ajax=1&act=cat_description_ckeditor'+URLOptions);
            <?php } ?>
        }
    }
    prop_tb.attachEvent("onClick", setPropertiesPanel_descriptions);

    prop_tb.attachEvent("onStateChange",function(id,state){
        if (id=='desc_twodesc')
        {
            if (state)
            {
                prop_tb._descriptionsLayout.cells('a').getFrame().contentWindow.hideShortDesc();
            }else{
                prop_tb._descriptionsLayout.cells('a').getFrame().contentWindow.showShortDesc();
            }
        }
        if (id=='desc_3cols')
        {
            if (state)
            {
                cat.cells("a").collapse();
                var tmp_size = ($(window).width()*1)/3;
                dhxLayout.cells('b').setWidth(tmp_size*2);
                prop_tb._descriptionsLayout.cells('b').expand();
                prop_tb._descriptionsLayout.cells('b').setWidth(tmp_size);
            }else{
                cat.cells("a").expand();
                var start_cat_size_prop = getParamUISettings('start_cat_size_prop');
                dhxLayout.cells('b').setWidth(start_cat_size_prop);
                prop_tb._descriptionsLayout.cells('b').collapse();
            }
        }
    });


    cat_grid_tb.attachEvent("onClick",function(id){
<?php
    $tmp = array();
    $clang = _l('Language');
    foreach ($languages as $lang)
    {
        echo '
            if (id==\'cat_lang_'.$lang['iso_code'].'\')
            {
                if (propertiesPanel==\'descriptions\' && typeof prop_tb._descriptionsLayout!=\'undefined\')
                    checkDescriptionsBeforeChangeRow();
            }
';
    }
?>
    });

    cat_grid.attachEvent("onBeforeSelect", function(newP,oldP,newI){
        checkDescriptionsBeforeChangeRow();
        return true;
    });

    let descriptions_current_id = 0;
    cat_grid.attachEvent("onRowSelect",function (idproduct){
        lastProductSelID=idproduct;
        if (propertiesPanel=='descriptions' && (cat_grid.getSelectedRowId()!==null && descriptions_current_id!=idproduct))
        {
            if (prop_tb._descriptionsLayout.cells('a').getFrame().contentWindow.checkSize())
            {
                dhxLayout.cells('b').setText('<?php echo _l('Properties', 1).' '._l('of', 1); ?> '+getSelectedItemValueOrID(cat_grid,lastProductSelID,'name'));
                <?php if (_s('APP_RICH_EDITOR') != 1) { ?>
                prop_tb._descriptionsLayout.cells('a').progressOn();
                <?php } ?>
                prop_tb._descriptionsLayout.cells('a').getFrame().contentWindow.ajaxLoad('&id_product='+lastProductSelID+'&id_lang='+SC_ID_LANG,lastProductSelID,SC_ID_LANG);
            }else{
                dhtmlx.message({text:'<?php echo _l('Short description charset must be < ')._s('CAT_SHORT_DESC_SIZE').' '._l('chars', 1); ?>',type:'error'});
            }
            descriptions_current_id=idproduct;
        }
    });

        function checkDescriptionsBeforeChangeRow(currentproduct)
        {
            if (propertiesPanel=='descriptions')
            {
                if(not_save !== 0) {
                    prop_tb._descriptionsLayout.cells('a').getFrame().contentWindow.checkChange();
                }
            }
        }

<?php } ?>