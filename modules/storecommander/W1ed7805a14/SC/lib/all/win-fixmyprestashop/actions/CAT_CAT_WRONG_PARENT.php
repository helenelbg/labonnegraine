<?php
$post_action = Tools::getValue('action');
if (!empty($post_action) && $post_action == 'do_check')
{
    $sql = 'SELECT cat.id_category, cat.`level_depth`, parent.id_category as id_category_parent, parent.`level_depth` as level_depth_parent
        FROM `'._DB_PREFIX_.'category` cat
        INNER JOIN `'._DB_PREFIX_.'category` parent ON (cat.id_parent=parent.id_category)
        WHERE 
        (cat.id_category=parent.id_category)
        OR
        (cat.`level_depth`<=parent.`level_depth`) LIMIT 1500';
    $res = Db::getInstance()->ExecuteS($sql);

    $content = '';
    $content_js = '';
    $results = 'OK';
    if (!empty($res) && count($res) > 0)
    {
        $results = 'KO';
        ob_start(); ?>
        <script type="text/javascript">
    
            var tbWrongTreeCategory = dhxlSCExtCheck.tabbar.cells("table_CAT_CAT_WRONG_PARENT").attachToolbar();
            tbWrongTreeCategory.setIconset('awesome');
            tbWrongTreeCategory.addButton("selectall", 0, "", 'fa fa-bolt yellow', 'fa fa-bolt yellow');
            tbWrongTreeCategory.setItemToolTip('selectall','<?php echo _l('Select all'); ?>');
            tbWrongTreeCategory.addButton("change", 0, "", 'fa fa-external-link green', 'fa fa-external-link green');
            tbWrongTreeCategory.setItemToolTip('change','<?php echo _l('Move in home category', 1); ?>');
            tbWrongTreeCategory.attachEvent("onClick",
                function(id){
                    if (id=='selectall')
                    {
                        gridWrongTreeCategory.selectAll();
                        getGridStat_WrongTreeCategory();
                    }
                    if (id=='change')
                    {
                        replaceWrongTreeCategory()
                    }
                });
        
            var gridWrongTreeCategory = dhxlSCExtCheck.tabbar.cells("table_CAT_CAT_WRONG_PARENT").attachGrid();
            gridWrongTreeCategory.setImagePath("lib/js/imgs/");
            gridWrongTreeCategory.enableSmartRendering(true);
            gridWrongTreeCategory.enableMultiselect(true);
    
            gridWrongTreeCategory.setHeader("ID,<?php echo _l('Name'); ?>,<?php echo _l('Used?'); ?>");
            gridWrongTreeCategory.setInitWidths("100, 110,50");
            gridWrongTreeCategory.setColAlign("left,left,left");
            gridWrongTreeCategory.setColTypes("ro,ro,ro");
            gridWrongTreeCategory.setColSorting("int,str,str");
            gridWrongTreeCategory.attachHeader("#numeric_filter,#text_filter,#select_filter");
            gridWrongTreeCategory.init();

            var xml = '<rows>';
            <?php foreach ($res as $category)
        {
            $cat = new Category((int) $category['id_category'], SCI::getConfigurationValue('PS_LANG_DEFAULT'));
            $sql = 'SELECT * FROM `'._DB_PREFIX_."category_product` WHERE id_category = '".(int) $category['id_category']."' LIMIT 1";
            $is_used = Db::getInstance()->ExecuteS($sql); ?>
            xml = xml+'   <row id="<?php echo $category['id_category']; ?>">';
            xml = xml+'      <cell><![CDATA[<?php echo $category['id_category']; ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php echo str_replace("'", "\'", $cat->name); ?>]]></cell>';
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
            gridWrongTreeCategory.parse(xml);

            sbWrongTreeCategory=dhxlSCExtCheck.tabbar.cells("table_CAT_CAT_WRONG_PARENT").attachStatusBar();
            function getGridStat_WrongTreeCategory(){
                var filteredRows=gridWrongTreeCategory.getRowsNum();
                var selectedRows=(gridWrongTreeCategory.getSelectedRowId()?gridWrongTreeCategory.getSelectedRowId().split(',').length:0);
                sbWrongTreeCategory.setText('<?php echo count($res).' '._l('Errors'); ?>'+" - <?php echo _l('Filter')._l(':'); ?> "+filteredRows+" - <?php echo _l('Selection')._l(':'); ?> "+selectedRows);
            }
            gridWrongTreeCategory.attachEvent("onFilterEnd", function(elements){
                getGridStat_WrongTreeCategory();
            });
            gridWrongTreeCategory.attachEvent("onSelectStateChanged", function(id){
                getGridStat_WrongTreeCategory();
            });
            getGridStat_WrongTreeCategory();

            function replaceWrongTreeCategory()
            {
                var selectedWrongTreeCategorys = gridWrongTreeCategory.getSelectedRowId();
                if(selectedWrongTreeCategorys==null || selectedWrongTreeCategorys=="")
                    selectedWrongTreeCategorys = 0;
                if(selectedWrongTreeCategorys!="0")
                {
                    $.post("index.php?ajax=1&act=all_win-fixmyprestashop_actions&check=CAT_CAT_WRONG_PARENT&id_lang="+SC_ID_LANG, { "action": "replace_categories", "ids": selectedWrongTreeCategorys}, function(data){
                        dhxlSCExtCheck.tabbar.tabs("table_CAT_CAT_WRONG_PARENT").close();

                         dhxlSCExtCheck.gridChecks.selectRowById('CAT_CAT_WRONG_PARENT');
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
            'title' => _l('Cat. wrong tree'),
            'contentJs' => $content_js,
    ));
}
elseif (!empty($post_action) && $post_action == 'replace_categories')
{
    $post_ids = Tools::getValue('ids');
    if (!empty($post_ids))
    {
        $ids = explode(',', $post_ids);
        foreach ($ids as $id)
        {
            $category_id = 1;
            if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
            {
                $sql = 'SELECT id_shop_default
                FROM `'._DB_PREFIX_."category`
                WHERE id_category = '".(int) $id."'";
                $shop_id = Db::getInstance()->getValue($sql);
                if (!empty($shop_id))
                {
                    $sql = 'SELECT id_category
                    FROM `'._DB_PREFIX_."shop`
                    WHERE id_shop = '".(int) $shop_id."'";
                    $category_id = Db::getInstance()->getValue($sql);
                }
            }
            if (empty($category_id))
            {
                $category_id = 1;
            }

            $sql = 'UPDATE `'._DB_PREFIX_."category` SET id_parent = '".(int) $category_id."' WHERE id_category = '".(int) $id."'";
            dbExecuteForeignKeyOff($sql);
        }
    }
}
