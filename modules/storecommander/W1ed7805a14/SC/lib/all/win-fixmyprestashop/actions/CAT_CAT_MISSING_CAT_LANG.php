<?php
$post_action = Tools::getValue('action');
if (!empty($post_action) && $post_action == 'do_check')
{
    $res = missingLangGet('category');

    $content = '';
    $content_js = '';
    $results = 'OK';
    if (!empty($res) && count($res) > 0)
    {
        $results = 'KO';
        ob_start(); ?>
        <script type="text/javascript">
    
            var tbMissingCategoryLang = dhxlSCExtCheck.tabbar.cells("table_CAT_CAT_MISSING_CAT_LANG").attachToolbar();
            tbMissingCategoryLang.setIconset('awesome');
            tbMissingCategoryLang.addButton("selectall", 0, "", 'fa fa-bolt yellow', 'fa fa-bolt yellow');
            tbMissingCategoryLang.setItemToolTip('selectall','<?php echo _l('Select all'); ?>');
            tbMissingCategoryLang.addButton("delete", 0, "", 'fa fa-minus-circle red', 'fa fa-minus-circle red');
            tbMissingCategoryLang.setItemToolTip('delete','<?php echo _l('Delete incomplete categories'); ?>');
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
        
            var gridMissingCategoryLang = dhxlSCExtCheck.tabbar.cells("table_CAT_CAT_MISSING_CAT_LANG").attachGrid();
            gridMissingCategoryLang.setImagePath("lib/js/imgs/");
            gridMissingCategoryLang.enableSmartRendering(true);
            gridMissingCategoryLang.enableMultiselect(true);
    
            gridMissingCategoryLang.setHeader("ID,<?php echo _l('Used?'); ?>");
            gridMissingCategoryLang.setInitWidths("100,50");
            gridMissingCategoryLang.setColAlign("left,left");
            gridMissingCategoryLang.setColTypes("ro,ro");
            gridMissingCategoryLang.setColSorting("int,str");
            gridMissingCategoryLang.attachHeader("#numeric_filter,#select_filter");
            gridMissingCategoryLang.init();
    
            var xml = '<rows>';
            <?php foreach ($res as $category)
        {
            $sql = 'SELECT * FROM `'._DB_PREFIX_."category_product` WHERE id_category = '".(int) $category['id_category']."' LIMIT 1";
            $is_used = Db::getInstance()->ExecuteS($sql); ?>
            xml = xml+'   <row id="<?php echo $category['id_category']; ?>">';
            xml = xml+'      <cell><![CDATA[<?php echo $category['id_category']; ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php if (!empty($is_used) && count($is_used) > 0)
            {
                echo _l('Yes');
            }
            else
            {
                echo _l('No');
            } ?>]]></cell>';
            xml = xml+'   </row>';
            <?php
        } ?>
            xml = xml+'</rows>';
            gridMissingCategoryLang.parse(xml);

            sbMissingCategoryLang=dhxlSCExtCheck.tabbar.cells("table_CAT_CAT_MISSING_CAT_LANG").attachStatusBar();
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
                    $.post("index.php?ajax=1&act=all_win-fixmyprestashop_actions&check=CAT_CAT_MISSING_CAT_LANG&id_lang="+SC_ID_LANG, { "action": "delete_categories", "ids": selectedMissingCategoryLangs}, function(data){
                        dhxlSCExtCheck.tabbar.tabs("table_CAT_CAT_MISSING_CAT_LANG").close();

                         dhxlSCExtCheck.gridChecks.selectRowById('CAT_CAT_MISSING_CAT_LANG');
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
                    $.post("index.php?ajax=1&act=all_win-fixmyprestashop_actions&check=CAT_CAT_MISSING_CAT_LANG&id_lang="+SC_ID_LANG, { "action": "add_categorys", "ids": selectedMissingCategoryLangs}, function(data){
                        dhxlSCExtCheck.tabbar.tabs("table_CAT_CAT_MISSING_CAT_LANG").close();

                         dhxlSCExtCheck.gridChecks.selectRowById('CAT_CAT_MISSING_CAT_LANG');
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
            $category = new Category($id);
            $category->delete();
            if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
            {
                $sql = 'DELETE FROM '._DB_PREFIX_.'category WHERE id_category = '.(int) $id;
                dbExecuteForeignKeyOff($sql);
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
            $sql = 'SELECT  l.*
                    FROM '._DB_PREFIX_.'lang l
                    WHERE l.id_lang not in (SELECT pl.id_lang FROM '._DB_PREFIX_."category_lang pl WHERE pl.id_category='".(int) $id."')";
            $languages = Db::getInstance()->ExecuteS($sql);

            foreach ($languages as $language)
            {
                $sql = 'INSERT INTO '._DB_PREFIX_.'category_lang (id_category, id_lang, name, link_rewrite)
                        VALUES ('.(int) $id.','.(int) $language['id_lang'].",'Category','category')";
                $res = dbExecuteForeignKeyOff($sql);
            }
        }
    }
}
