<?php
if (_r('GRI_CMS_PROPERTIES_GRID_DESC')) { ?>
    prop_tb.addListOption('panel', 'descriptions', 2, "button", '<?php echo _l('Descriptions', 1); ?>', "fad fa-align-left");
    allowed_properties_panel[allowed_properties_panel.length] = "descriptions";
    prop_tb.addButton("description_refresh",1000, "", "fa fa-sync green", "fa fa-sync green");
    prop_tb.setItemToolTip('description_refresh','<?php echo _l('Refresh', 1); ?>');
    prop_tb.addButton('desc_save',1000,'','fa fa-save blue','fa fa-save blue');
    prop_tb.setItemToolTip('desc_save','<?php echo _l('Save descriptions', 1); ?>');

    prop_tb.addButtonTwoState('desc_3cols', 1000, "", "fa fa-bars fa-rotate-90 green", "fa fa-bars fa-rotate-90 green");
    prop_tb.setItemToolTip('desc_3cols','<?php echo _l('Display on 3 columns', 1); ?>');
    
    <?php if (SC_CREATIVE_ELEMENTS_ACTIVE){ ?>
    prop_tb.addButton('edit_description_with_ce',1000, "", "ce_editor_link", "ce_editor_link");
    prop_tb.setItemToolTip('edit_description_with_ce','<?php echo _l('Edit with CreativeElements', 1); ?>');
    <?php } ?>
    needInitDescriptions = 1;
    function initDescriptions(){
        if (needInitDescriptions)
        {
            <?php if (count($languages) > 1) {?>
                prop_tb._descriptionsLayout = dhxLayout.cells('b').attachLayout('2U');
            <?php }
else
{ ?>
                prop_tb._descriptionsLayout = dhxLayout.cells('b').attachLayout('1C');
            <?php } ?>
            let URLOptions='&id_cms='+lastcms_pageID+'&id_lang='+SC_ID_LANG+'&id_shop='+shopselection;
            prop_tb._descriptionsLayout.cells('a').hideHeader();
            <?php if (_s('APP_RICH_EDITOR') == 1) { ?>
                prop_tb._descriptionsLayout.cells('a').attachURL('index.php?ajax=1&act=cms_description_tinymce'+URLOptions);
            <?php }
else
{ ?>
                prop_tb._descriptionsLayout.cells('a').attachURL('index.php?ajax=1&act=cms_description_ckeditor'+URLOptions);
            <?php } ?>
            dhxLayout.cells('b').showHeader();

            // subproperties
            <?php
            if (count($languages) > 1) {?>
                if(getParamUISettings('start_cms_description') == 1) {
                    prop_tb._descriptionsLayout.cells('b').collapse();
                }
                actual_subproperties = "desc_translation";
                prop_tb._descriptionsLayout.cells('b').setWidth(200);
                prop_tb._descriptionsLayout.cells('b').setText("<?php echo _l('Description'); ?>");
                prop_tb.desc_subproperties_tb=prop_tb._descriptionsLayout.cells('b').attachToolbar();
                prop_tb.desc_subproperties_tb.setIconset('awesome');
                var opts = new Array();
                prop_tb.desc_subproperties_tb.addButtonSelect("descSubProperties", 0, "<?php echo _l('Translation'); ?>", opts, "fad fa-align-left", "fad fa-align-left",false,true);
                hideDescSubpropertiesItems();
                <?php } ?>

            prop_tb._descriptionsLayout.attachEvent("onCollapse", function(name){
                saveParamUISettings('start_cms_description', 1);
            });
            prop_tb._descriptionsLayout.attachEvent("onExpand", function(name){
                saveParamUISettings('start_cms_description', 0);
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


    function setPropertiesPanel_descriptions(id) {
        // ask to save description if modified
        if (propertiesPanel=='descriptions' && id!='desc_save' && typeof prop_tb._descriptionsLayout!='undefined')
            prop_tb._descriptionsLayout.cells('a').getFrame().contentWindow.checkChange();

        if (id=='descriptions')
        {
            hidePropTBButtons();
            prop_tb.showItem('description_refresh');
            prop_tb.showItem('desc_save');
            prop_tb.showItem('desc_3cols');
            prop_tb.setItemState('desc_3cols', 0);
            <?php if (SC_CREATIVE_ELEMENTS_ACTIVE){ ?>
            prop_tb.showItem('edit_description_with_ce');
            <?php } ?>
            prop_tb.setItemText('panel', '<?php echo _l('Descriptions', 1); ?>');
            prop_tb.setItemImage('panel', 'fad fa-align-left');
            URLOptions='';
            if (lastcms_pageID!=0) URLOptions='&id_cms='+lastcms_pageID+'&id_lang='+SC_ID_LANG+'&id_shop='+shopselection;
            needInitDescriptions = 1;
            initDescriptions();
        <?php if (count($languages) > 1) {?>
            needInitDescriptionTranslation=1;
            initDescriptionTranslation();
        <?php } ?>
            propertiesPanel='descriptions';
            dhxLayout.cells('b').setWidth(680);//605
        }

        if (id=='desc_save')
        {
            <?php if (_s('APP_RICH_EDITOR') != 1) { ?>
                prop_tb._descriptionsLayout.cells('a').progressOn();
            <?php } ?>
            prop_tb._descriptionsLayout.cells('a').getFrame().contentWindow.ajaxSave();
        }

        <?php if (SC_CREATIVE_ELEMENTS_ACTIVE){ ?>
        if(id=='edit_description_with_ce'){
            openCreativeElements(cms_grid.getSelectedRowId(), '<?php echo CE\UId::CMS; ?>');
        }
        <?php } ?>

        if (id=='description_refresh')
        {
            <?php if (_s('APP_RICH_EDITOR') != 1) { ?>
                prop_tb._descriptionsLayout.cells('a').progressOn();
            <?php } ?>
            prop_tb._descriptionsLayout.cells('a').getFrame().contentWindow.ajaxLoad('&id_cms='+lastcms_pageID+'&id_lang='+SC_ID_LANG,lastcms_pageID,SC_ID_LANG,shopselection);
        }

    }
    prop_tb.attachEvent("onClick", setPropertiesPanel_descriptions);

prop_tb.attachEvent("onStateChange",function(id,state){
    if (id=='desc_3cols')
    {
        if (state)
        {
            cms.cells("a").collapse();
            var tmp_size = ($(window).width()*1)/3;
            dhxLayout.cells('b').setWidth(tmp_size*2);
            prop_tb._descriptionsLayout.cells('b').expand();
            prop_tb._descriptionsLayout.cells('b').setWidth(tmp_size);
        }else{
            cms.cells("a").expand();
            var start_cms_size_prop = getParamUISettings('start_cms_size_prop');
            dhxLayout.cells('b').setWidth(start_cms_size_prop);
            prop_tb._descriptionsLayout.cells('b').collapse();
        }
    }
});

    <?php if (SCMS) { ?>
        cms_shoptree.attachEvent("onClick",function(){
            if (lastcms_pageID!=0) {
                <?php if (_s('APP_RICH_EDITOR') != 1) { ?>
                    prop_tb._descriptionsLayout.cells('a').progressOn();
                <?php } ?>
                prop_tb._descriptionsLayout.cells('a').getFrame().contentWindow.ajaxLoad('&id_cms='+lastcms_pageID+'&id_lang='+SC_ID_LANG,lastcms_pageID,SC_ID_LANG,shopselection);
            }
        });
    <?php } ?>

    cms_grid_tb.attachEvent("onClick",function(id){
        <?php
        $tmp = array();
        $clang = _l('Language');
        foreach ($languages as $lang)
        {
            if (_s('APP_RICH_EDITOR') == 1)
            {
                $type = 'tinymce';
            }
            else
            {
                $type = 'ckeditor';
            }

            echo "
                if (id=='cms_lang_".$lang['iso_code']."')
                {
                    if (lastcms_pageID!=0){
                        URLOptions='&id_cms='+lastcms_pageID+'&id_lang='+SC_ID_LANG+'&id_shop='+shopselection;
                        prop_tb._descriptionsLayout.cells('a').attachURL('index.php?ajax=1&act=cms_description_".$type."'+URLOptions);
                    }
                        
                    if (propertiesPanel=='descriptions' && typeof prop_tb._descriptionsLayout!='undefined')
                        prop_tb._descriptionsLayout.cells('a').getFrame().contentWindow.checkChange();
                }";
        }
        ?>
    });

    let cms_descriptions_current_id = 0;
    cms_grid.attachEvent("onRowSelect",function (idcms){
        lastcms_pageID=idcms;
        idxCmsName=cms_grid.getColIndexById('meta_title');
        if (propertiesPanel=='descriptions' && (cms_grid.getSelectedRowId()!==null && cms_descriptions_current_id!=idcms))
        {
            prop_tb._descriptionsLayout.cells('a').getFrame().contentWindow.checkChange();
            dhxLayout.cells('b').setText('<?php echo _l('Properties', 1).' '._l('of', 1); ?> '+cms_grid.cells(lastcms_pageID,idxCmsName).getValue());
            <?php if (_s('APP_RICH_EDITOR') != 1) { ?>
                prop_tb._descriptionsLayout.cells('a').progressOn();
            <?php } ?>
            prop_tb._descriptionsLayout.cells('a').getFrame().contentWindow.ajaxLoad('&id_cms='+lastcms_pageID+'&id_lang='+SC_ID_LANG,lastcms_pageID,SC_ID_LANG,shopselection);
            cms_descriptions_current_id=idcms;
        }
    });

<?php } ?>
