<?php
$post_action = Tools::getValue('action');
if (!empty($post_action) && $post_action == 'do_check')
{
    $res = missingLangMSGet('category');

    $content = '';
    $content_js = '';
    $results = 'OK';
    if (!empty($res) && count($res) > 0)
    {
        $results = 'KO';
        ob_start(); ?>
        <script type="text/javascript">
    
            var tbMissingCategoryLang = dhxlSCExtCheck.tabbar.cells("table_CAT_CAT_MISSING_CAT_LANG_MS").attachToolbar();
            tbMissingCategoryLang.setIconset('awesome');
            tbMissingCategoryLang.addButton("selectall", 0, "", 'fa fa-bolt yellow', 'fa fa-bolt yellow');
            tbMissingCategoryLang.setItemToolTip('selectall','<?php echo _l('Select all'); ?>');
            tbMissingCategoryLang.addButton("delete", 0, "", 'fa fa-minus-circle red', 'fa fa-minus-circle red');
            tbMissingCategoryLang.setItemToolTip('delete','<?php echo _l('Remove categories from shops'); ?>');
            tbMissingCategoryLang.addButton("add", 0, "", 'fa fa-plus-circle green', 'fa fa-plus-circle green');
            tbMissingCategoryLang.setItemToolTip('add','<?php echo _l('Recover incomplete categories'); ?>');
            tbMissingCategoryLang.attachEvent("onClick",
                function(id){
                    if (id=='selectall')
                    {
                        gridMissingCategoryLang.selectAll();
                        getGridStat_MissingCategoryLang();
                    }
                    if (id=='delete')
                    {
                        deleteMissingCategoryLang();
                    }
                    if (id=='add')
                    {
                        addMissingCategoryLang()
                    }
                });
        
            var gridMissingCategoryLang = dhxlSCExtCheck.tabbar.cells("table_CAT_CAT_MISSING_CAT_LANG_MS").attachGrid();
            gridMissingCategoryLang.setImagePath("lib/js/imgs/");
            gridMissingCategoryLang.enableSmartRendering(true);
            gridMissingCategoryLang.enableMultiselect(true);
    
            gridMissingCategoryLang.setHeader("ID,<?php echo _l('Used?'); ?>,<?php echo _l('Shop'); ?>");
            gridMissingCategoryLang.setInitWidths("100,100,200");
            gridMissingCategoryLang.setColAlign("left,left,left");
            gridMissingCategoryLang.setColTypes("ro,ro,ro");
            gridMissingCategoryLang.setColSorting("int,str,str");
            gridMissingCategoryLang.attachHeader("#numeric_filter,#select_filter,#text_filter");
            gridMissingCategoryLang.init();
    
            var xml = '<rows>';
            <?php foreach ($res as $category)
        {
            $sql = 'SELECT * FROM `'._DB_PREFIX_."category_product` WHERE id_category = '".(int) $category['id_category']."' LIMIT 1";
            $is_used = Db::getInstance()->ExecuteS($sql); ?>
            xml = xml+'   <row id="<?php echo $category['id_category'].'_'.$category['id_shop']; ?>">';
            xml = xml+'      <cell><![CDATA[<?php echo $category['id_category']; ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php if (!empty($is_used) && count($is_used) > 0)
            {
                echo _l('Yes');
            }
            else
            {
                echo _l('No');
            } ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php echo str_replace("'", "\'", $category['shop_name']).' (#'.$category['id_shop'].')'; ?>]]></cell>';
            xml = xml+'   </row>';
            <?php
        } ?>
            xml = xml+'</rows>';
            gridMissingCategoryLang.parse(xml);

            sbMissingCategoryLang=dhxlSCExtCheck.tabbar.cells("table_CAT_CAT_MISSING_CAT_LANG_MS").attachStatusBar();
            function getGridStat_MissingCategoryLang(){
                var filteredRows=gridMissingCategoryLang.getRowsNum();
                var selectedRows=(gridMissingCategoryLang.getSelectedRowId()?gridMissingCategoryLang.getSelectedRowId().split(',').length:0);
                sbMissingCategoryLang.setText('<?php echo count($res).' '._l('Errors'); ?>'+" - <?php echo _l('Filter')._l(':'); ?> "+filteredRows+" - <?php echo _l('Selection')._l(':'); ?> "+selectedRows);
            }
            gridMissingCategoryLang.attachEvent("onFilterEnd", function(elements){
                getGridStat_MissingCategoryLang();
            });
            gridMissingCategoryLang.attachEvent("onSelectStateChanged", function(id){
                getGridStat_MissingCategoryLang();
            });
            getGridStat_MissingCategoryLang();

            function deleteMissingCategoryLang()
            {
                var selectedMissingCategoryLangs = gridMissingCategoryLang.getSelectedRowId();
                if(selectedMissingCategoryLangs==null || selectedMissingCategoryLangs=="")
                    selectedMissingCategoryLangs = 0;
                if(selectedMissingCategoryLangs!="0")
                {
                    $.post("index.php?ajax=1&act=all_win-fixmyprestashop_actions&check=CAT_CAT_MISSING_CAT_LANG_MS&id_lang="+SC_ID_LANG, { "action": "delete_categories", "ids": selectedMissingCategoryLangs}, function(data){
                        dhxlSCExtCheck.tabbar.tabs("table_CAT_CAT_MISSING_CAT_LANG_MS").close();

                         dhxlSCExtCheck.gridChecks.selectRowById('CAT_CAT_MISSING_CAT_LANG_MS');
                         doCheck(false);
                    });
                }
            }

            function addMissingCategoryLang()
            {
                var selectedMissingCategoryLangs = gridMissingCategoryLang.getSelectedRowId();
                if(selectedMissingCategoryLangs==null || selectedMissingCategoryLangs=="")
                    selectedMissingCategoryLangs = 0;
                if(selectedMissingCategoryLangs!="0")
                {
                    $.post("index.php?ajax=1&act=all_win-fixmyprestashop_actions&check=CAT_CAT_MISSING_CAT_LANG_MS&id_lang="+SC_ID_LANG, { "action": "add_categorys", "ids": selectedMissingCategoryLangs}, function(data){
                        dhxlSCExtCheck.tabbar.tabs("table_CAT_CAT_MISSING_CAT_LANG_MS").close();

                         dhxlSCExtCheck.gridChecks.selectRowById('CAT_CAT_MISSING_CAT_LANG_MS');
                         doCheck(false);
                    });
                }
            }
        </script>
        <?php $content_js = ob_get_clean();
    }
    echo json_encode(array(
            'results' => $results,
            'contentType' => 'grid',
            'content' => $content,
            'title' => _l('Category lang'),
            'contentJs' => $content_js,
    ));
}
elseif (!empty($post_action) && $post_action == 'delete_categories')
{
    $post_ids = Tools::getValue('ids');
    if (!empty($post_ids))
    {
        $ids = explode(',', $post_ids);
        foreach ($ids as $id)
        {
            list($id_category, $id_shop) = explode('_', $id);

            $sql = 'DELETE FROM '._DB_PREFIX_.'category_shop WHERE id_category = '.(int) $id_category.' AND id_shop = '.(int) $id_shop;
            dbExecuteForeignKeyOff($sql);

            $sql = 'DELETE FROM '._DB_PREFIX_.'category_lang WHERE id_category = '.(int) $id_category.' AND id_shop = '.(int) $id_shop;
            dbExecuteForeignKeyOff($sql);

            $sql = 'SELECT id_shop_default FROM '._DB_PREFIX_.'category WHERE id_category = '.(int) $id_category;
            $shop_default = Db::getInstance()->getValue($sql);
            if (!empty($shop_default) && $shop_default == $id_shop)
            {
                $sql = 'SELECT id_shop FROM '._DB_PREFIX_.'category_shop WHERE id_category = '.(int) $id_category.' AND id_shop!='.(int) $id_shop.' LIMIT 1';
                $shop_id = Db::getInstance()->executeS($sql);
                if (!empty($shop_id[0]['id_shop']))
                {
                    $shop_id = $shop_id[0]['id_shop'];
                    $sql = 'UPDATE '._DB_PREFIX_.'category SET id_shop_default = '.(int) $shop_id.' WHERE id_category = '.(int) $id_category;
                    dbExecuteForeignKeyOff($sql);
                }
            }
        }
    }
}
elseif (!empty($post_action) && $post_action == 'add_categorys')
{
    $post_ids = Tools::getValue('ids');
    if (!empty($post_ids))
    {
        $ids = explode(',', $post_ids);
        foreach ($ids as $id)
        {
            list($id_category, $id_shop) = explode('_', $id);

            $langs_in_shop = Language::getLanguages(false, $id_shop);

            foreach ($langs_in_shop as $lang)
            {
                $sql = 'SELECT id_shop
                    FROM '._DB_PREFIX_.'category_lang
                    WHERE id_category = '.(int) $id_category.'
                        AND id_shop='.(int) $id_shop.'
                        AND id_lang='.(int) $lang['id_lang'].'
                    LIMIT 1';
                $exist = Db::getInstance()->executeS($sql);
                if (empty($exist[0]['id_shop']))
                { // S'il n'y a pas de langue pour ce produit / shop
                    // On va regarder s'il existe la même langue pour une autre boutique
                    $sql = 'SELECT *
                    FROM '._DB_PREFIX_.'category_lang
                    WHERE id_category = '.(int) $id_category.'
                        AND id_lang='.(int) $lang['id_lang'].'
                    LIMIT 1';
                    $in_other_lang = Db::getInstance()->executeS($sql);
                    if (!empty($in_other_lang[0]['id_shop']))
                    {
                        $in_other_lang = $in_other_lang[0];
                        $sql = 'INSERT INTO '._DB_PREFIX_."category_lang (id_category,id_shop,id_lang,name,description,link_rewrite,meta_title,meta_keywords,meta_description )
                        VALUES ('".(int) $id_category."','".(int) $id_shop."','".(int) $lang['id_lang']."','".pSQL($in_other_lang['name'])."','".pSQL($in_other_lang['description'])."','".pSQL($in_other_lang['link_rewrite'])."','".pSQL($in_other_lang['meta_title'])."','".pSQL($in_other_lang['meta_keywords'])."','".pSQL($in_other_lang['meta_description'])."')";
                        dbExecuteForeignKeyOff($sql);
                    }
                    else
                    {
                        $created = false;
                        $default_lang = Configuration::get('PS_LANG_DEFAULT');
                        if (!empty($default_lang))
                        {
                            // On va regarder s'il existe la langue par défaut pour la boutique
                            $sql = 'SELECT *
                            FROM '._DB_PREFIX_.'category_lang
                            WHERE id_category = '.(int) $id_category.'
                                AND id_shop='.(int) $id_shop.'
                                AND id_lang='.(int) $default_lang.'
                            LIMIT 1';
                            $in_default_lang = Db::getInstance()->executeS($sql);
                            if (!empty($in_default_lang[0]['id_shop']))
                            {
                                $in_default_lang = $in_default_lang[0];
                                $sql = 'INSERT INTO '._DB_PREFIX_."category_lang (id_category,id_shop,id_lang,name,description,link_rewrite,meta_title,meta_keywords,meta_description )
                                VALUES ('".(int) $id_category."','".(int) $id_shop."','".(int) $lang['id_lang']."','".pSQL($in_default_lang['name'])."','".pSQL($in_default_lang['description'])."','".pSQL($in_default_lang['link_rewrite'])."','".pSQL($in_default_lang['meta_title'])."','".pSQL($in_default_lang['meta_keywords'])."','".pSQL($in_default_lang['meta_description'])."')";
                                dbExecuteForeignKeyOff($sql);
                                $created = true;
                            }
                            else
                            {
                                // On va regarder s'il existe la langue par défaut pour une autre boutique
                                $sql = 'SELECT *
                                FROM '._DB_PREFIX_.'category_lang
                                WHERE id_category = '.(int) $id_category.'
                                    AND id_lang='.(int) $default_lang.'
                                LIMIT 1';
                                $in_default_lang = Db::getInstance()->executeS($sql);
                                if (!empty($in_default_lang[0]['id_shop']))
                                {
                                    $in_default_lang = $in_default_lang[0];
                                    $sql = 'INSERT INTO '._DB_PREFIX_."category_lang (id_category,id_shop,id_lang,name,description,link_rewrite,meta_title,meta_keywords,meta_description )
                                    VALUES ('".(int) $id_category."','".(int) $id_shop."','".(int) $lang['id_lang']."','".pSQL($in_default_lang['name'])."','".pSQL($in_default_lang['description'])."','".pSQL($in_default_lang['link_rewrite'])."','".pSQL($in_default_lang['meta_title'])."','".pSQL($in_default_lang['meta_keywords'])."','".pSQL($in_default_lang['meta_description'])."')";
                                    dbExecuteForeignKeyOff($sql);
                                    $created = true;
                                }
                            }
                        }

                        // On va créé une ligne basique
                        if (!$created)
                        {
                            $sql = 'INSERT INTO '._DB_PREFIX_."category_lang (id_category,id_shop,id_lang,name,description,link_rewrite,meta_title,meta_keywords,meta_description )
                                    VALUES ('".(int) $id_category."','".(int) $id_shop."','".(int) $lang['id_lang']."','Category ".$id_category."','','category_".$id_category."','','','')";
                            dbExecuteForeignKeyOff($sql);
                        }
                    }
                }
            }
        }
    }
}
