<?php
if (_r('GRI_MAN_PROPERTIES_GRID_DESC')) { ?>
    prop_tb.addListOption('panel', 'descriptions', 2, "button", '<?php echo _l('Descriptions', 1); ?>', "fad fa-align-left");
    allowed_properties_panel[allowed_properties_panel.length] = "descriptions";
    prop_tb.addButton("description_refresh",1000, "", "fa fa-sync green", "fa fa-sync green");
    prop_tb.setItemToolTip('description_refresh','<?php echo _l('Refresh', 1); ?>');
    prop_tb.addButton('desc_save',1000,'','fa fa-save blue','fa fa-save blue');
    prop_tb.setItemToolTip('desc_save','<?php echo _l('Save descriptions', 1); ?>');
    prop_tb.addText('txt_descriptionsize', 1000, '<?php echo _l('Short description charset')._l(':').' '.'0/'._s('man_SHORT_DESC_SIZE'); ?>');
    prop_tb.addButtonTwoState('desc_twodesc', 1000, "", "fad fa-text-height blue", "fad fa-text-height blue");
    prop_tb.setItemToolTip('desc_twodesc','<?php echo _l('Display all descriptions', 1); ?>');
    <?php if (SC_CREATIVE_ELEMENTS_ACTIVE){ ?>
    prop_tb.addButton('edit_description_with_ce',1000, "", "ce_editor_link", "ce_editor_link");
    prop_tb.setItemToolTip('edit_description_with_ce','<?php echo _l('Edit with CreativeElements', 1); ?>');
    <?php } ?>

    needInitDescriptions = 1;
    function initDescriptions(){
    if (needInitDescriptions)
    {
    prop_tb._descriptionsLayout = dhxLayout.cells('b').attachLayout('1C');
    prop_tb._descriptionsLayout.cells('a').hideHeader();
    var URLOptions='&id_manufacturer=0&id_lang='+SC_ID_LANG;
    <?php if (_s('APP_RICH_EDITOR') == 1) { ?>
        prop_tb._descriptionsLayout.cells('a').attachURL('index.php?ajax=1&act=man_description_tinymce'+URLOptions);
    <?php }
else
{ ?>
        prop_tb._descriptionsLayout.cells('a').attachURL('index.php?ajax=1&act=man_description_ckeditor'+URLOptions);
    <?php } ?>
    dhxLayout.cells('b').showHeader();
    needInitDescriptions=0;
    }
    }



    function setPropertiesPanel_descriptions(id){
    // ask to save description if modified
    if (propertiesPanel=='descriptions' && id!='desc_save' && typeof prop_tb._descriptionsLayout!='undefined')
    prop_tb._descriptionsLayout.cells('a').getFrame().contentWindow.checkChange();

    if (id=='descriptions')
    {
    hidePropTBButtons();
    prop_tb.showItem('description_refresh');
    prop_tb.showItem('desc_save');
    prop_tb.showItem('desc_twodesc');
    <?php if (SC_CREATIVE_ELEMENTS_ACTIVE){ ?>
    prop_tb.showItem('edit_description_with_ce');
    <?php } ?>
    prop_tb.showItem('txt_descriptionsize');
    prop_tb.setItemState("desc_twodesc", 0);
    prop_tb.setItemText('panel', '<?php echo _l('Descriptions', 1); ?>');
    prop_tb.setItemImage('panel', 'fad fa-align-left');

    needInitDescriptions = 1;
    initDescriptions();
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
        openCreativeElements(man_grid.getSelectedRowId(), '<?php echo CE\UId::MANUFACTURER; ?>');
    }
    <?php } ?>
    if (id=='description_refresh')
    {
    URLOptions='&id_manufacturer='+last_manufacturerID+'&id_lang='+SC_ID_LANG;
    <?php if (_s('APP_RICH_EDITOR') == 1) { ?>
        prop_tb._descriptionsLayout.cells('a').attachURL('index.php?ajax=1&act=man_description_tinymce'+URLOptions);
    <?php }
else
{ ?>
        prop_tb._descriptionsLayout.cells('a').attachURL('index.php?ajax=1&act=man_description_ckeditor'+URLOptions);
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
    });


    man_grid_tb.attachEvent("onClick",function(id){
    <?php
    $tmp = array();
    $clang = _l('Language');
    foreach ($languages as $lang)
    {
        echo '
            if (id==\'man_lang_'.$lang['iso_code'].'\')
            {
                if (propertiesPanel==\'descriptions\' && typeof prop_tb._descriptionsLayout!=\'undefined\')
                    prop_tb._descriptionsLayout.cells(\'a\').getFrame().contentWindow.checkChange();
            }
';
    }
    ?>
    });

    let man_descriptions_current_id = 0;
    man_grid.attachEvent("onRowSelect",function (idproduct){
        last_manufacturerID=idproduct;
        idxProductName=man_grid.getColIndexById('name');
        if (propertiesPanel=='descriptions' && (man_grid.getSelectedRowId()!==null && man_descriptions_current_id!=idproduct))
        {
            if (prop_tb._descriptionsLayout.cells('a').getFrame().contentWindow.checkSize())
            {
                prop_tb._descriptionsLayout.cells('a').getFrame().contentWindow.checkChange();
                dhxLayout.cells('b').setText('<?php echo _l('Properties', 1).' '._l('of', 1); ?> '+man_grid.cells(last_manufacturerID,idxProductName).getValue());
                <?php if (_s('APP_RICH_EDITOR') != 1) { ?>
                prop_tb._descriptionsLayout.cells('a').progressOn();
                <?php } ?>
                prop_tb._descriptionsLayout.cells('a').getFrame().contentWindow.ajaxLoad('&id_manufacturer='+last_manufacturerID+'&id_lang='+SC_ID_LANG,last_manufacturerID,SC_ID_LANG);
            }else{
                dhtmlx.message({text:'<?php echo _l('Short description charset must be < ')._s('man_SHORT_DESC_SIZE').' '._l('chars', 1); ?>',type:'error'});
            }
            man_descriptions_current_id=idproduct;
        }
    });

<?php } ?>
