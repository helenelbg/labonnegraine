<?php

$post_action = Tools::getValue('action');
if (!empty($post_action) && $post_action == 'do_check')
{
    $res = missingLangGet('product');

    $content = '';
    $content_js = '';
    $results = 'OK';
    if (!empty($res) && count($res) > 0)
    {
        $results = 'KO';
        ob_start(); ?>
        <script type="text/javascript">

            var tbMissingProductLang = dhxlSCExtCheck.tabbar.cells("table_CAT_PROD_MISSING_PRODUCT_LANG").attachToolbar();
            tbMissingProductLang.setIconset('awesome');
            tbMissingProductLang.addButton("selectall", 0, "", 'fa fa-bolt yellow', 'fa fa-bolt yellow');
            tbMissingProductLang.setItemToolTip('selectall', '<?php echo _l('Select all'); ?>');
            tbMissingProductLang.addButton("delete", 0, "", 'fa fa-minus-circle red', 'fa fa-minus-circle red');
            tbMissingProductLang.setItemToolTip('delete', '<?php echo _l('Delete incomplete products'); ?>');
            tbMissingProductLang.addButton("add", 0, "", 'fa fa-plus-circle green', 'fa fa-plus-circle green');
            tbMissingProductLang.setItemToolTip('add', '<?php echo _l('Recover incomplete products'); ?>');
            tbMissingProductLang.attachEvent("onClick",
                function (id) {
                    if (id == 'selectall') {
                        gridMissingProductLang.selectAll();
                        getGridStat_MissingProductLang();
                    }
                    if (id == 'delete') {
                        deleteMissingProductLang();
                    }
                    if (id == 'add') {
                        addMissingProductLang();
                    }
                }
            );

            var gridMissingProductLang = dhxlSCExtCheck.tabbar.cells("table_CAT_PROD_MISSING_PRODUCT_LANG").attachGrid();
            gridMissingProductLang.setImagePath("lib/js/imgs/");
            gridMissingProductLang.enableSmartRendering(true);
            gridMissingProductLang.enableMultiselect(true);

            gridMissingProductLang.setHeader("ID,<?php echo _l('Reference'); ?>");
            gridMissingProductLang.setInitWidths("100,100");
            gridMissingProductLang.setColAlign("left,left");
            gridMissingProductLang.setColTypes("ro,ro");
            gridMissingProductLang.setColSorting("int,str");
            gridMissingProductLang.attachHeader("#numeric_filter,#text_filter");
            gridMissingProductLang.init();

            var xml = '<rows>';
            <?php foreach ($res as $product)
            { ?>
            xml = xml + '   <row id="<?php echo $product['id_product']; ?>">';
            xml = xml + '      <cell><![CDATA[<?php echo $product['id_product']; ?>]]></cell>';
            xml = xml + '      <cell><![CDATA[<?php echo str_replace("'", "\'", $product['reference']); ?>]]></cell>';
            xml = xml + '   </row>';
            <?php } ?>
            xml = xml + '</rows>';
            gridMissingProductLang.parse(xml);

            sbMissingProductLang=dhxlSCExtCheck.tabbar.cells("table_CAT_PROD_MISSING_PRODUCT_LANG").attachStatusBar();
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

            function deleteMissingProductLang() {
                var selectedMissingProductLangs = gridMissingProductLang.getSelectedRowId();
                if (selectedMissingProductLangs == null || selectedMissingProductLangs == "")
                    selectedMissingProductLangs = 0;
                if (selectedMissingProductLangs != "0") {

                    $.post("index.php?ajax=1&act=all_win-fixmyprestashop_actions&check=CAT_PROD_MISSING_PRODUCT_LANG&id_lang=" + SC_ID_LANG, {
                        "action": "delete_products",
                        "ids": selectedMissingProductLangs
                    }, function (data) {
                        dhxlSCExtCheck.tabbar.tabs("table_CAT_PROD_MISSING_PRODUCT_LANG").close();

                        dhxlSCExtCheck.gridChecks.selectRowById('CAT_PROD_MISSING_PRODUCT_LANG');
                        doCheck(false);
                    });
                }
            }
            
            function addMissingProductLang() {
                var selectedMissingProductLangs = gridMissingProductLang.getSelectedRowId();
                if (selectedMissingProductLangs == null || selectedMissingProductLangs == "")
                    selectedMissingProductLangs = 0;
                if (selectedMissingProductLangs != "0") {

                    $.post("index.php?ajax=1&act=all_win-fixmyprestashop_actions&check=CAT_PROD_MISSING_PRODUCT_LANG&id_lang=" + SC_ID_LANG, {
                        "action": "add_products",
                        "ids": selectedMissingProductLangs
                    }, function (data) {
                        dhxlSCExtCheck.tabbar.tabs("table_CAT_PROD_MISSING_PRODUCT_LANG").close();

                        dhxlSCExtCheck.gridChecks.selectRowById('CAT_PROD_MISSING_PRODUCT_LANG');
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
            $product = new Product($id);
            $product->delete();
            if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
            {
                $sql = 'DELETE FROM '._DB_PREFIX_.'product WHERE id_product = '.(int) $id;
                dbExecuteForeignKeyOff($sql);
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
        foreach ($ids as $id_product)
        {
            $langs = Language::getLanguages(false);

            foreach ($langs as $lang)
            {
                $sql = 'SELECT id_lang 
                    FROM '._DB_PREFIX_.'product_lang
                    WHERE id_product = '.(int) $id_product.'
                        AND id_lang='.(int) $lang['id_lang'].'
                    LIMIT 1';

                $exist = Db::getInstance()->executeS($sql);
                if (empty($exist[0]['id_lang']))
                { // S'il n'y a pas de langue pour ce produit
                    $created = false;
                    $default_lang = Configuration::get('PS_LANG_DEFAULT');
                    if (!empty($default_lang))
                    {
                        // On va regarder s'il existe la langue par défaut
                        $sql = 'SELECT * 
                        FROM '._DB_PREFIX_.'product_lang
                        WHERE id_product = '.(int) $id_product.'
                            AND id_lang='.(int) $default_lang.'
                        LIMIT 1';

                        $in_default_lang = Db::getInstance()->executeS($sql);
                        if (!empty($in_default_lang[0]['id_lang']))
                        {
                            $in_default_lang = $in_default_lang[0];
                            $sql = 'INSERT INTO '._DB_PREFIX_."product_lang (id_product,id_lang,description,description_short,link_rewrite,meta_description,meta_keywords,meta_title,name,available_now,available_later)
                            VALUES ('".(int) $id_product."','".(int) $lang['id_lang']."','".pSQL($in_default_lang['description'])."','".pSQL($in_default_lang['description_short'])."','".pSQL($in_default_lang['link_rewrite'])."','".pSQL($in_default_lang['meta_description'])."','".pSQL($in_default_lang['meta_keywords'])."','".pSQL($in_default_lang['meta_title'])."','".pSQL($in_default_lang['name'])."','".pSQL($in_default_lang['available_now'])."','".pSQL($in_default_lang['available_later'])."')";
                            dbExecuteForeignKeyOff($sql);
                            $created = true;
                        }
                    }

                    // On va créé une ligne basique
                    if (!$created)
                    {
                        $sql = 'INSERT INTO '._DB_PREFIX_."product_lang (id_product,id_lang,description,description_short,link_rewrite,meta_description,meta_keywords,meta_title,name,available_now,available_later)
                                VALUES ('".(int) $id_product."','".(int) $lang['id_lang']."','','','product_".$id_product."','','','','Product ".$id_product."','','')";
                        dbExecuteForeignKeyOff($sql);
                    }
                }
            }
        }
    }
}
