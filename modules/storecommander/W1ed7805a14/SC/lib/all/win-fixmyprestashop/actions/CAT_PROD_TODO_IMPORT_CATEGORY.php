<?php
$post_action = Tools::getValue('action');
$action_name = 'CAT_PROD_TODO_IMPORT_CATEGORY';
$tab_title = _l('Categ. TODO');

if (!empty($post_action) && $post_action == 'do_check')
{
    $res = array();

    $sql = 'SELECT DISTINCT c.id_category, cl.name, c.date_add, COUNT(cp.id_product) as count FROM `' . _DB_PREFIX_ . 'category` c
        LEFT JOIN `' . _DB_PREFIX_ . 'category_lang` cl ON (c.id_category = cl.id_category AND cl.id_lang = '.(int) $id_lang .(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? ' AND cl.id_shop=c.id_shop_default' : '').' )
        LEFT JOIN `' . _DB_PREFIX_ . 'category_product` cp ON (c.id_category = cp.id_category)
    WHERE cl.name LIKE "%.TODO.csv"
    GROUP BY cp.id_category LIMIT 1500';

    $res = Db::getInstance()->ExecuteS($sql);

    $content = '';
    $content_js = '';
    $results = 'OK';
    if (!empty($res) && count($res) > 0)
    {
        $results = 'KO';
        ob_start(); ?>
        <script type="text/javascript">
    
            var tbTodoImportCategory = dhxlSCExtCheck.tabbar.cells("table_<?php echo $action_name; ?>").attachToolbar();
            tbTodoImportCategory.setIconset('awesome');
            var idTodoImportCategory = '';
            tbTodoImportCategory.addButton("gotocatalog", 0, "", 'fad fa-external-link green', 'fad fa-external-link green');
            tbTodoImportCategory.setItemToolTip('gotocatalog','<?php echo _l('Go to the category in catalog.'); ?>');
            tbTodoImportCategory.addButton("selectall", 0, "", 'fa fa-bolt yellow', 'fa fa-bolt yellow');
            tbTodoImportCategory.setItemToolTip('selectall','<?php echo _l('Select all'); ?>');
            tbTodoImportCategory.addButton("delete", 0, "", 'fa fa-minus-circle red', 'fa fa-minus-circle red');
            tbTodoImportCategory.setItemToolTip('delete','<?php echo _l('Delete empty categories'); ?>');
            tbTodoImportCategory.attachEvent("onClick",
                function(id){
                    if (id=='gotocatalog')
                    {
                        if(idTodoImportCategory !== '') {
                            var path = idTodoImportCategory+"-0";
                            let url = "?page=cat_tree&open_cat_grid="+path;
                            window.open(url,'_blank');
                        }
                    }
                    if (id=='selectall')
                    {
                        gridTodoImportCategory.selectAll();
                        getGridStat_TodoImportCategory();
                    }
                    if (id=='delete')
                    {
                        deleteEmptyTodoCateg();
                    }
                });
        
            var gridTodoImportCategory = dhxlSCExtCheck.tabbar.cells("table_<?php echo $action_name; ?>").attachGrid();
            gridTodoImportCategory.setImagePath("lib/js/imgs/");
            gridTodoImportCategory.enableSmartRendering(true);
            gridTodoImportCategory.enableMultiselect(true);
    
            gridTodoImportCategory.setHeader("<?php echo _l('Category ID'); ?>,<?php echo _l('Name'); ?>,<?php echo _l('Number of products'); ?>,<?php echo _l('Creation date'); ?>");
            gridTodoImportCategory.setInitWidths("100,100,100,100");
            gridTodoImportCategory.setColAlign("left,left,left,left");
            gridTodoImportCategory.setColTypes("ro,ro,ro,ro");
            gridTodoImportCategory.setColSorting("int,str,int,str");
            gridTodoImportCategory.attachHeader("#numeric_filter,#text_filter,#text_filter,#numeric_filter");
            gridTodoImportCategory.init();

            gridTodoImportCategory.attachEvent('onRowSelect',function(id){
                idTodoImportCategory = id;
            });
    
            var xml = '<rows>';
            <?php foreach ($res as $row) { ?>
            xml = xml+'   <row id="<?php echo $row['id_category'] ?>">';
            xml = xml+'      <cell><![CDATA[<?php echo $row['id_category']; ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php echo  addslashes($row['name']); ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php echo  $row['count']; ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php echo  $row['date_add']; ?>]]></cell>';
            xml = xml+'   </row>';
            <?php } ?>
            xml = xml+'</rows>';
            gridTodoImportCategory.parse(xml);

            sbTodoImportCategory=dhxlSCExtCheck.tabbar.cells("table_<?php echo $action_name; ?>").attachStatusBar();
            function getGridStat_TodoImportCategory(){
                var filteredRows=gridTodoImportCategory.getRowsNum();
                var selectedRows=(gridTodoImportCategory.getSelectedRowId()?gridTodoImportCategory.getSelectedRowId().split(',').length:0);
                sbTodoImportCategory.setText('<?php echo count($res).' '._l('Errors'); ?>'+" - <?php echo _l('Filter')._l(':'); ?> "+filteredRows+" - <?php echo _l('Selection')._l(':'); ?> "+selectedRows);
            }
            gridTodoImportCategory.attachEvent("onFilterEnd", function(elements){
                getGridStat_TodoImportCategory();
            });
            gridTodoImportCategory.attachEvent("onSelectStateChanged", function(id){
                getGridStat_TodoImportCategory();
            });
            getGridStat_TodoImportCategory();

            function deleteEmptyTodoCateg()
            {
                var selectedTodoImportCategory = gridTodoImportCategory.getSelectedRowId();
                if(selectedTodoImportCategory==null || selectedTodoImportCategory=="")
                    selectedTodoImportCategory = 0;
                if(selectedTodoImportCategory!="0")
                {
                    $.post("index.php?ajax=1&act=all_win-fixmyprestashop_actions&check=CAT_PROD_TODO_IMPORT_CATEGORY&id_lang="+SC_ID_LANG, { "action": "delete_empty_category", "ids": selectedTodoImportCategory}, function(data){
                        dhxlSCExtCheck.tabbar.tabs("table_<?php echo $action_name; ?>").close();

                         dhxlSCExtCheck.gridChecks.selectRowById('<?php echo $action_name; ?>');
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
            'title' => _l($tab_title),
            'contentJs' => $content_js,
    ));
}
elseif (!empty($post_action) && $post_action == 'delete_empty_category')
{
    $post_ids = Tools::getValue('ids');
    if (!empty($post_ids))
    {
        $ids = explode(',', $post_ids);
        foreach ($ids as $idc)
        {
            $sql = 'SELECT * FROM `'._DB_PREFIX_.'category_product` cp
            WHERE cp. id_category = '. (int) $idc;
            $res = Db::getInstance()->ExecuteS($sql);

            if (empty($res))
            {
                if ($idc > 1)
                {
                    if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                    {
                        $category = new Category($idc);
                        $category->id_shop_list = SCI::getShopsByCategory($idc);
                        if (Validate::isLoadedObject($category) && !$category->isRootCategoryForAShop())
                        {
                            $category->delete();
                        }
                    }
                    else
                    {  // versions < 1.5
                        $category = new Category($idc);
                        if (Validate::isLoadedObject($category))
                        {
                            $category->delete();
                        }
                    }
                }
            }
        }
    }
}
