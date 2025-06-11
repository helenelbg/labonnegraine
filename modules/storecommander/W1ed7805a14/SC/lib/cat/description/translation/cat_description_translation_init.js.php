<?php
if (count($languages) > 1)
{
    $title = _l('Translation', 1);
    $icon = 'fad fa-align-left';
    $property = 'descSubProperties';
    $sub_prop = 'desc_translation'; ?>
    var selected_lang = '';
    var obj_lang_iso = {};
    prop_tb.attachEvent("onClick", function setPropertiesPanel_descriptions(id){
        if(id == 'descriptions'){
            prop_tb.desc_subproperties_tb.addListOption('<?php echo $property; ?>', '<?php echo $sub_prop; ?>', 9, "button", '<?php echo $title; ?>', "<?php echo $icon; ?>");

            prop_tb.desc_subproperties_tb.attachEvent("onClick", function(id){
                if(id=="<?php echo $sub_prop; ?>")
                {
                    hideDescSubpropertiesItems();
                    prop_tb.desc_subproperties_tb.setItemText('<?php echo $property; ?>', '<?php echo $title; ?>');
                    prop_tb.desc_subproperties_tb.setItemImage('<?php echo $property; ?>', '<?php echo $icon; ?>');
                    actual_subproperties = "<?php echo $sub_prop; ?>";
                    initDescriptionTranslation();
                }
            });
        }
    });

    let descriptions_translation_current_id = 0;
    cat_grid.attachEvent("onRowSelect", function(idproduct){
        if (propertiesPanel=='descriptions' && actual_subproperties == "<?php echo $sub_prop; ?>" && (cat_grid.getSelectedRowId()!==null && descriptions_translation_current_id!=idproduct))
        {
            lastProductSelID = idproduct;
            getDescriptionTranslation();
            descriptions_translation_current_id=idproduct;
        }
    });

    function initDescriptionTranslation()
    {
        if(needInitDescriptionTranslation==1)
        {
    <?php
        $tmp = array();
    $optlang = '';
    $clang = _l('Language', 1);
    foreach ($languages as $lang)
    {
        if ((int) _s('CAT_PROD_LANGUAGE_ALL') == 0 && $lang['active'] != 1)
        {
            continue;
        }
        echo 'obj_lang_iso['.$lang['id_lang'].'] = "'.$lang['iso_code'].'";'."\n";
        if ($lang['id_lang'] != $sc_agent->id_lang)
        {
            $clang = $lang['iso_code'];
            echo 'selected_lang = "'.$lang['id_lang'].'";'."\n";
            $optlang = 'desc_translang_'.$lang['id_lang'];
        }
        $tmp[] = "['desc_translang_".$lang['id_lang']."', 'obj', '".$lang['name']."', '']";
    }
    if (count($tmp) > 1)
    {
        echo 'var opts = ['.implode(',', $tmp).'];';
    } ?>
            prop_tb.desc_subproperties_tb.addButtonSelect('desc_translang',100,'<?php echo $clang; ?>',opts,'fad fa-flag blue','fad fa-flag blue',false,true);
            prop_tb.desc_subproperties_tb.setItemToolTip('desc_translang','<?php echo _l('Select catalog language'); ?>');
            prop_tb.desc_subproperties_tb.setListOptionSelected('desc_translang', '<?php echo $optlang; ?>');
            prop_tb.desc_subproperties_tb.addButton("desc_transl_refresh", 100, "", "fa fa-sync green", "fa fa-sync green");
            prop_tb.desc_subproperties_tb.setItemToolTip('desc_transl_refresh','<?php echo _l('Refresh', 1); ?>');
            prop_tb.desc_subproperties_tb.addButton("desc_transl_save", 100, "", "fa fa-save blue", "fa fa-save blue");
            prop_tb.desc_subproperties_tb.setItemToolTip('desc_transl_save','<?php echo _l('Save translation', 1); ?>');
            prop_tb.desc_subproperties_tb.addText('desc_txt_descriptionsize', 100, '<?php echo _l('Short description charset')._l(':').' '.'0/'._s('CAT_SHORT_DESC_SIZE'); ?>');
            prop_tb.desc_subproperties_tb.addButtonTwoState('desc_transl_twodesc', 100, "", "fad fa-text-height blue", "fad fa-text-height blue");
            prop_tb.desc_subproperties_tb.setItemToolTip('desc_transl_twodesc','<?php echo _l('Display all descriptions', 1); ?>');
            hideDescSubpropertiesItems();

            prop_tb.desc_subproperties_tb.showItem('desc_transl_refresh');
            prop_tb.desc_subproperties_tb.showItem('desc_transl_twodesc');
            prop_tb.desc_subproperties_tb.showItem('desc_transl_save');
    <?php if (count($tmp) > 1) { ?>
            prop_tb.desc_subproperties_tb.showItem('desc_translang');
    <?php } ?>
            prop_tb.desc_subproperties_tb.showItem('desc_txt_descriptionsize');

            getDescriptionTranslation();

            prop_tb.desc_subproperties_tb.attachEvent("onClick", function(id){
                if(id=='desc_transl_refresh') {
                    getDescriptionTranslation();
                }
                if(id=='desc_transl_save') {
                    not_save = 0;
                    <?php if (_s('APP_RICH_EDITOR') != 1) { ?>
                    prop_tb._descriptionsLayout.cells('b').progressOn();
                    <?php } ?>
                    prop_tb._descriptionsLayout.cells('b').getFrame().contentWindow.ajaxSave();
                }
                if (id.substr(0,15)=='desc_translang_'){
                    selected_lang = id.replace('desc_translang_', '');
                    prop_tb.desc_subproperties_tb.setItemText('desc_translang',obj_lang_iso[selected_lang]);
                    checkDescriptionsLangBeforeChangeRow();
                    getDescriptionTranslation();
                }
            });
            prop_tb.desc_subproperties_tb.attachEvent("onStateChange",function(id,state){
                if (id=='desc_transl_twodesc')
                {
                    if (state)
                    {
                        prop_tb._descriptionsLayout.cells('b').getFrame().contentWindow.hideShortDesc();
                    }else{
                        prop_tb._descriptionsLayout.cells('b').getFrame().contentWindow.showShortDesc();
                    }
                }
            });
        }
    }

    function getDescriptionTranslation()
    {
        prop_tb._descriptionsLayout.cells('b').attachURL('index.php?ajax=1&act=cat_description_ckeditor', null, {id_product: lastProductSelID, id_lang: selected_lang, subprop:1});
    }

    function checkDescriptionsLangBeforeChangeRow()
    {
        if (propertiesPanel=='descriptions')
        {
            if(not_save !== 0) {
                prop_tb._descriptionsLayout.cells('b').getFrame().contentWindow.checkChange();
            }
        }
    }

<?php
}
?>