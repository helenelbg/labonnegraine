<?php
$post_action = Tools::getValue('action');
if (!empty($post_action) && $post_action == 'do_check')
{
    $res = missingLangMSGet('product', array('id_product', 'reference'));
    $content = '';
    $content_js = '';
    $results = 'OK';
    if (!empty($res) && count($res) > 0)
    {
        $results = 'KO';
        ob_start(); ?>
        <script type="text/javascript">
    
            var tbMissingProductLang = dhxlSCExtCheck.tabbar.cells("table_CAT_PROD_MISSING_PRODUCT_LANG_MS").attachToolbar();
            tbMissingProductLang.setIconset('awesome');
            tbMissingProductLang.addButton("selectall", 0, "", 'fa fa-bolt yellow', 'fa fa-bolt yellow');
            tbMissingProductLang.setItemToolTip('selectall','<?php echo _l('Select all'); ?>');
            tbMissingProductLang.addButton("delete", 0, "", 'fa fa-minus-circle red', 'fa fa-minus-circle red');
            tbMissingProductLang.setItemToolTip('delete','<?php echo _l('Remove products from shops'); ?>');
            tbMissingProductLang.addButton("add", 0, "", 'fa fa-plus-circle green', 'fa fa-plus-circle green');
            tbMissingProductLang.setItemToolTip('add','<?php echo _l('Recover incomplete products'); ?>');
            tbMissingProductLang.attachEvent("onClick",
                function(id){
                    if (id=='selectall')
                    {
                        gridMissingProductLang.selectAll();
                        getGridStat_MissingProductLang();
                    }
                    if (id=='delete')
                    {
                        deleteMissingProductLang();
                    }
                    if (id=='add')
                    {
                        addMissingProductLang();
                    }
                });
        
            var gridMissingProductLang = dhxlSCExtCheck.tabbar.cells("table_CAT_PROD_MISSING_PRODUCT_LANG_MS").attachGrid();
            gridMissingProductLang.setImagePath("lib/js/imgs/");
            gridMissingProductLang.enableSmartRendering(true);
            gridMissingProductLang.enableMultiselect(true);
    
            gridMissingProductLang.setHeader("ID,<?php echo _l('Reference'); ?>,<?php echo _l('Shop'); ?>");
            gridMissingProductLang.setInitWidths("100,100,200");
            gridMissingProductLang.setColAlign("left,left,left");
            gridMissingProductLang.setColTypes("ro,ro,ro");
            gridMissingProductLang.setColSorting("int,str,str");
            gridMissingProductLang.attachHeader("#numeric_filter,#text_filter,#text_filter");
            gridMissingProductLang.init();
    
            var xml = '<rows>';
            <?php foreach ($res as $product)
            { ?>
            xml = xml+'   <row id="<?php echo $product['id_product'].'_'.$product['id_shop']; ?>">';
            xml = xml+'      <cell><![CDATA[<?php echo $product['id_product']; ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php echo str_replace("'", "\'", $product['reference']); ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php echo str_replace("'", "\'", $product['shop_name']).' (#'.$product['id_shop'].')'; ?>]]></cell>';
            xml = xml+'   </row>';
            <?php } ?>
            xml = xml+'</rows>';
            gridMissingProductLang.parse(xml);

            sbMissingProductLang=dhxlSCExtCheck.tabbar.cells("table_CAT_PROD_MISSING_PRODUCT_LANG_MS").attachStatusBar();
            function getGridStat_MissingProductLang(){
                var filteredRows=gridMissingProductLang.getRowsNum();
                var selectedRows=(gridMissingProductLang.getSelectedRowId()?gridMissingProductLang.getSelectedRowId().split(',').length:0);
                sbMissingProductLang.setText('<?php echo count($res).' '._l('Errors'); ?>'+" - <?php echo _l('Filter')._l(':'); ?> "+filteredRows+" - <?php echo _l('Selection')._l(':'); ?> "+selectedRows);
            }
            gridMissingProductLang.attachEvent("onFilterEnd", function(elements){
                getGridStat_MissingProductLang();
            });
            gridMissingProductLang.attachEvent("onSelectStateChanged", function(id){
                getGridStat_MissingProductLang();
            });
            getGridStat_MissingProductLang();

            function deleteMissingProductLang()
            {
                var selectedMissingProductLangs = gridMissingProductLang.getSelectedRowId();
                if(selectedMissingProductLangs==null || selectedMissingProductLangs=="")
                    selectedMissingProductLangs = 0;
                if(selectedMissingProductLangs!="0")
                {
                    $.post("index.php?ajax=1&act=all_win-fixmyprestashop_actions&check=CAT_PROD_MISSING_PRODUCT_LANG_MS&id_lang="+SC_ID_LANG, { "action": "delete_products", "ids": selectedMissingProductLangs}, function(data){
                        dhxlSCExtCheck.tabbar.tabs("table_CAT_PROD_MISSING_PRODUCT_LANG_MS").close();

                         dhxlSCExtCheck.gridChecks.selectRowById('CAT_PROD_MISSING_PRODUCT_LANG_MS');
                         doCheck(false);
                    });
                }
            }

            function addMissingProductLang()
            {
                var selectedMissingProductLangs = gridMissingProductLang.getSelectedRowId();
                if(selectedMissingProductLangs==null || selectedMissingProductLangs=="")
                    selectedMissingProductLangs = 0;
                if(selectedMissingProductLangs!="0")
                {
                    $.post("index.php?ajax=1&act=all_win-fixmyprestashop_actions&check=CAT_PROD_MISSING_PRODUCT_LANG_MS&id_lang="+SC_ID_LANG, { "action": "add_products", "ids": selectedMissingProductLangs}, function(data){
                        dhxlSCExtCheck.tabbar.tabs("table_CAT_PROD_MISSING_PRODUCT_LANG_MS").close();

                         dhxlSCExtCheck.gridChecks.selectRowById('CAT_PROD_MISSING_PRODUCT_LANG_MS');
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
            'title' => _l('Product lang'),
            'contentJs' => $content_js,
    ));
}
elseif (!empty($post_action) && $post_action == 'delete_products')
{
    $post_ids = Tools::getValue('ids');
    if (!empty($post_ids))
    {
        $ids = explode(',', $post_ids);
        foreach ($ids as $id)
        {
            list($id_product, $id_shop) = explode('_', $id);

            $sql = 'DELETE FROM '._DB_PREFIX_.'product_shop WHERE id_product = '.(int) $id_product.' AND id_shop = '.(int) $id_shop;
            dbExecuteForeignKeyOff($sql);

            $sql = 'DELETE FROM '._DB_PREFIX_.'product_lang WHERE id_product = '.(int) $id_product.' AND id_shop = '.(int) $id_shop;
            dbExecuteForeignKeyOff($sql);

            $sql = 'SELECT id_shop_default FROM '._DB_PREFIX_.'product WHERE id_product = '.(int) $id_product;
            $shop_default = Db::getInstance()->getValue($sql);
            if (!empty($shop_default) && $shop_default == $id_shop)
            {
                $sql = 'SELECT id_shop FROM '._DB_PREFIX_.'product_shop WHERE id_product = '.(int) $id_product.' AND id_shop!='.(int) $id_shop.' LIMIT 1';
                $shop_id = Db::getInstance()->executeS($sql);
                if (!empty($shop_id[0]['id_shop']))
                {
                    $shop_id = $shop_id[0]['id_shop'];
                    $sql = 'UPDATE '._DB_PREFIX_.'product SET id_shop_default = '.(int) $shop_id.' WHERE id_product = '.(int) $id_product;
                    dbExecuteForeignKeyOff($sql);
                }
            }
        }
    }
}
elseif (!empty($post_action) && $post_action == 'add_products')
{
    $post_ids = Tools::getValue('ids');
    if (!empty($post_ids))
    {
        $ids = explode(',', $post_ids);
        foreach ($ids as $id)
        {
            list($id_product, $id_shop) = explode('_', $id);

            $langs_in_shop = Language::getLanguages(false, $id_shop);

            foreach ($langs_in_shop as $lang)
            {
                $sql = 'SELECT id_shop 
                    FROM '._DB_PREFIX_.'product_lang 
                    WHERE id_product = '.(int) $id_product.' 
                        AND id_shop='.(int) $id_shop.' 
                        AND id_lang='.(int) $lang['id_lang'].' 
                    LIMIT 1';
                $exist = Db::getInstance()->executeS($sql);
                if (empty($exist[0]['id_shop']))
                { // S'il n'y a pas de langue pour ce produit / shop
                    // On va regarder s'il existe la même langue pour une autre boutique
                    $sql = 'SELECT *
                    FROM '._DB_PREFIX_.'product_lang
                    WHERE id_product = '.(int) $id_product.'
                        AND id_lang='.(int) $lang['id_lang'].'
                    LIMIT 1';
                    $in_other_lang = Db::getInstance()->executeS($sql);
                    if (!empty($in_other_lang[0]['id_shop']))
                    {
                        $in_other_lang = $in_other_lang[0];
                        $sql = 'INSERT INTO '._DB_PREFIX_."product_lang (id_product,id_shop,id_lang,description,description_short,link_rewrite,meta_description,meta_keywords,meta_title,name,available_now,available_later)
                        VALUES ('".(int) $id_product."','".(int) $id_shop."','".(int) $lang['id_lang']."','".pSQL($in_other_lang['description'])."','".pSQL($in_other_lang['description_short'])."','".pSQL($in_other_lang['link_rewrite'])."','".pSQL($in_other_lang['meta_description'])."','".pSQL($in_other_lang['meta_keywords'])."','".pSQL($in_other_lang['meta_title'])."','".pSQL($in_other_lang['name'])."','".pSQL($in_other_lang['available_now'])."','".pSQL($in_other_lang['available_later'])."')";
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
                            FROM '._DB_PREFIX_.'product_lang 
                            WHERE id_product = '.(int) $id_product.' 
                                AND id_shop='.(int) $id_shop.' 
                                AND id_lang='.(int) $default_lang.' 
                            LIMIT 1';
                            $in_default_lang = Db::getInstance()->executeS($sql);
                            if (!empty($in_default_lang[0]['id_shop']))
                            {
                                $in_default_lang = $in_default_lang[0];
                                $sql = 'INSERT INTO '._DB_PREFIX_."product_lang (id_product,id_shop,id_lang,description,description_short,link_rewrite,meta_description,meta_keywords,meta_title,name,available_now,available_later)
                                VALUES ('".(int) $id_product."','".(int) $id_shop."','".(int) $lang['id_lang']."','".pSQL($in_default_lang['description'])."','".pSQL($in_default_lang['description_short'])."','".pSQL($in_default_lang['link_rewrite'])."','".pSQL($in_default_lang['meta_description'])."','".pSQL($in_default_lang['meta_keywords'])."','".pSQL($in_default_lang['meta_title'])."','".pSQL($in_default_lang['name'])."','".pSQL($in_default_lang['available_now'])."','".pSQL($in_default_lang['available_later'])."')";
                                dbExecuteForeignKeyOff($sql);
                                $created = true;
                            }
                            else
                            {
                                // On va regarder s'il existe la langue par défaut pour une autre boutique
                                $sql = 'SELECT *
                                FROM '._DB_PREFIX_.'product_lang
                                WHERE id_product = '.(int) $id_product.'
                                    AND id_lang='.(int) $default_lang.'
                                LIMIT 1';
                                $in_default_lang = Db::getInstance()->executeS($sql);
                                if (!empty($in_default_lang[0]['id_shop']))
                                {
                                    $in_default_lang = $in_default_lang[0];
                                    $sql = 'INSERT INTO '._DB_PREFIX_."product_lang (id_product,id_shop,id_lang,description,description_short,link_rewrite,meta_description,meta_keywords,meta_title,name,available_now,available_later)
                                    VALUES ('".(int) $id_product."','".(int) $id_shop."','".(int) $lang['id_lang']."','".pSQL($in_default_lang['description'])."','".pSQL($in_default_lang['description_short'])."','".pSQL($in_default_lang['link_rewrite'])."','".pSQL($in_default_lang['meta_description'])."','".pSQL($in_default_lang['meta_keywords'])."','".pSQL($in_default_lang['meta_title'])."','".pSQL($in_default_lang['name'])."','".pSQL($in_default_lang['available_now'])."','".pSQL($in_default_lang['available_later'])."')";
                                    dbExecuteForeignKeyOff($sql);
                                    $created = true;
                                }
                            }
                        }

                        // On va créé une ligne basique
                        if (!$created)
                        {
                            $sql = 'INSERT INTO '._DB_PREFIX_."product_lang (id_product,id_shop,id_lang,description,description_short,link_rewrite,meta_description,meta_keywords,meta_title,name,available_now,available_later)
                                    VALUES ('".(int) $id_product."','".(int) $id_shop."','".(int) $lang['id_lang']."','','','product_".$id_product."','','','','Product ".$id_product."','','')";
                            dbExecuteForeignKeyOff($sql);
                        }
                    }
                }
            }
        }
    }
}
