<?php
$post_action = Tools::getValue('action');
if (!empty($post_action) && $post_action == 'do_check')
{
    $res = missingLangGet('image');

    $content = '';
    $content_js = '';
    $results = 'OK';
    if (!empty($res) && count($res) > 0)
    {
        $results = 'KO';
        ob_start(); ?>
        <script type="text/javascript">
    
            var tbMissingImageLang = dhxlSCExtCheck.tabbar.cells("table_CAT_PROD_MISSING_IMAGE_LANG").attachToolbar();
            tbMissingImageLang.setIconset('awesome');
            tbMissingImageLang.addButton("selectall", 0, "", 'fa fa-bolt yellow', 'fa fa-bolt yellow');
            tbMissingImageLang.setItemToolTip('selectall','<?php echo _l('Select all'); ?>');
            tbMissingImageLang.addButton("delete", 0, "", 'fa fa-minus-circle red', 'fa fa-minus-circle red');
            tbMissingImageLang.setItemToolTip('delete','<?php echo _l('Delete incomplete images'); ?>');
            tbMissingImageLang.addButton("add", 0, "", 'fa fa-plus-circle green', 'fa fa-plus-circle green');
            tbMissingImageLang.setItemToolTip('add','<?php echo _l('Recover incomplete images'); ?>');
            tbMissingImageLang.attachEvent("onClick",
                function(id){
                    if (id=='selectall')
                    {
                        gridMissingImageLang.selectAll();
                        getGridStat_MissingImageLang();
                    }
                    if (id=='delete')
                    {
                        deleteMissingImageLang();
                    }
                    if (id=='add')
                    {
                        addMissingImageLang();
                    }
                });
        
            var gridMissingImageLang = dhxlSCExtCheck.tabbar.cells("table_CAT_PROD_MISSING_IMAGE_LANG").attachGrid();
            gridMissingImageLang.setImagePath("lib/js/imgs/");
            gridMissingImageLang.enableSmartRendering(true);
            gridMissingImageLang.enableMultiselect(true);
    
            gridMissingImageLang.setHeader("ID image,ID product");
            gridMissingImageLang.setInitWidths("100,100");
            gridMissingImageLang.setColAlign("left,left");
            gridMissingImageLang.setColTypes("ro,ro");
            gridMissingImageLang.setColSorting("int,int");
            gridMissingImageLang.attachHeader("#numeric_filter,#numeric_filter");
            gridMissingImageLang.init();
    
            var xml = '<rows>';
            <?php foreach ($res as $image) { ?>
            xml = xml+'   <row id="<?php echo $image['id_image']; ?>">';
            xml = xml+'      <cell><![CDATA[<?php echo $image['id_image']; ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php echo $image['id_product']; ?>]]></cell>';
            xml = xml+'   </row>';
            <?php } ?>
            xml = xml+'</rows>';
            gridMissingImageLang.parse(xml);

            sbMissingImageLang=dhxlSCExtCheck.tabbar.cells("table_CAT_PROD_MISSING_IMAGE_LANG").attachStatusBar();
            function getGridStat_MissingImageLang(){
                var filteredRows=gridMissingImageLang.getRowsNum();
                var selectedRows=(gridMissingImageLang.getSelectedRowId()?gridMissingImageLang.getSelectedRowId().split(',').length:0);
                sbMissingImageLang.setText('<?php echo count($res).' '._l('Errors'); ?>'+" - <?php echo _l('Filter')._l(':'); ?> "+filteredRows+" - <?php echo _l('Selection')._l(':'); ?> "+selectedRows);
            }
            gridMissingImageLang.attachEvent("onFilterEnd", function(elements){
                getGridStat_MissingImageLang();
            });
            gridMissingImageLang.attachEvent("onSelectStateChanged", function(id){
                getGridStat_MissingImageLang();
            });
            getGridStat_MissingImageLang();

            function deleteMissingImageLang()
            {
                var selectedMissingImageLangs = gridMissingImageLang.getSelectedRowId();
                if(selectedMissingImageLangs==null || selectedMissingImageLangs=="")
                    selectedMissingImageLangs = 0;
                if(selectedMissingImageLangs!="0")
                {
                    $.post("index.php?ajax=1&act=all_win-fixmyprestashop_actions&check=CAT_PROD_MISSING_IMAGE_LANG&id_lang="+SC_ID_LANG, { "action": "delete_images", "ids": selectedMissingImageLangs}, function(data){
                        dhxlSCExtCheck.tabbar.tabs("table_CAT_PROD_MISSING_IMAGE_LANG").close();

                         dhxlSCExtCheck.gridChecks.selectRowById('CAT_PROD_MISSING_IMAGE_LANG');
                         doCheck(false);
                    });
                }
            }

            function addMissingImageLang()
            {
                var selectedMissingImageLangs = gridMissingImageLang.getSelectedRowId();
                if(selectedMissingImageLangs==null || selectedMissingImageLangs=="")
                    selectedMissingImageLangs = 0;
                if(selectedMissingImageLangs!="0")
                {
                    $.post("index.php?ajax=1&act=all_win-fixmyprestashop_actions&check=CAT_PROD_MISSING_IMAGE_LANG&id_lang="+SC_ID_LANG, { "action": "add_images", "ids": selectedMissingImageLangs}, function(data){
                        dhxlSCExtCheck.tabbar.tabs("table_CAT_PROD_MISSING_IMAGE_LANG").close();

                         dhxlSCExtCheck.gridChecks.selectRowById('CAT_PROD_MISSING_IMAGE_LANG');
                         doCheck(false);
                    });
                }
            }
        </script>
        <?php
        $content_js = ob_get_clean();
    }
    echo json_encode(array(
            'results' => $results,
            'contentType' => 'grid',
            'content' => $content,
            'title' => _l('Image lang'),
            'contentJs' => $content_js,
    ));
}
elseif (!empty($post_action) && $post_action == 'delete_images')
{
    $post_ids = Tools::getValue('ids');
    if (!empty($post_ids))
    {
        $ids = explode(',', $post_ids);
        foreach ($ids as $id)
        {
            $sql = 'DELETE FROM '._DB_PREFIX_.'image WHERE id_product = '.(int) $id;
            dbExecuteForeignKeyOff($sql);
        }
    }
}
elseif (!empty($post_action) && $post_action == 'add_images')
{
    $post_ids = Tools::getValue('ids');
    if (!empty($post_ids))
    {
        $langs = Language::getLanguages(false);
        $default_lang = Configuration::get('PS_LANG_DEFAULT');

        $ids = explode(',', $post_ids);
        foreach ($ids as $id_image)
        {
            foreach ($langs as $lang)
            {
                $sql = 'SELECT id_lang
                    FROM '._DB_PREFIX_.'image_lang
                    WHERE id_image = '.(int) $id_image.'
                        AND id_lang='.(int) $lang['id_lang'].'
                    LIMIT 1';
                $exist = Db::getInstance()->executeS($sql);
                if (empty($exist[0]['id_lang']))
                { // S'il n'y a pas de langue pour cette image
                    $created = false;
                    if (!empty($default_lang))
                    {
                        // On va regarder s'il existe la langue par défaut
                        $sql = 'SELECT *
                        FROM '._DB_PREFIX_.'image_lang
                        WHERE id_image = '.(int) $id_image.'
                            AND id_lang='.(int) $default_lang.'
                        LIMIT 1';
                        $in_default_lang = Db::getInstance()->executeS($sql);
                        if (!empty($in_default_lang[0]['id_lang']))
                        {
                            $in_default_lang = $in_default_lang[0];
                            $sql = 'INSERT INTO '._DB_PREFIX_."image_lang (id_image,id_lang,legend)
                            VALUES ('".(int) $id_image."','".(int) $lang['id_lang']."','".pSQL($in_default_lang['legend'])."')";
                            dbExecuteForeignKeyOff($sql);
                            $created = true;
                        }
                    }

                    // On va créé une ligne basique
                    if (!$created)
                    {
                        $sql = 'INSERT INTO '._DB_PREFIX_."image_lang (id_image,id_lang)
                                VALUES ('".(int) $id_image."','".(int) $lang['id_lang']."')";
                        dbExecuteForeignKeyOff($sql);
                    }
                }
            }
        }
    }
}
