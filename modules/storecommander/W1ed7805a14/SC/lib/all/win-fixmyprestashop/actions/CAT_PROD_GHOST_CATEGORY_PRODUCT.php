<?php
$post_action = Tools::getValue('action');
if (!empty($post_action) && $post_action == 'do_check')
{
    $sql = 'select cp.id_product from '._DB_PREFIX_.'category_product cp where cp.id_product not in (select p.id_product from '._DB_PREFIX_.'product p) GROUP BY cp.id_product LIMIT 1500';
    $res = Db::getInstance()->ExecuteS($sql);

    $content = '';
    $content_js = '';
    $results = 'OK';
    if (!empty($res) && count($res) > 0)
    {
        $results = 'KO';
        ob_start(); ?>
        <script type="text/javascript">
    
            var tbGhostCategoryProduct = dhxlSCExtCheck.tabbar.cells("table_CAT_PROD_GHOST_CATEGORY_PRODUCT").attachToolbar();
            tbGhostCategoryProduct.setIconset('awesome');
            tbGhostCategoryProduct.addButton("selectall", 0, "", 'fa fa-bolt yellow', 'fa fa-bolt yellow');
            tbGhostCategoryProduct.setItemToolTip('selectall','<?php echo _l('Select all'); ?>');
            tbGhostCategoryProduct.addButton("delete", 0, "", 'fa fa-minus-circle red', 'fa fa-minus-circle red');
            tbGhostCategoryProduct.setItemToolTip('delete','<?php echo _l('Delete incomplete products'); ?>');
            tbGhostCategoryProduct.attachEvent("onClick",
                function(id){
                    if (id=='selectall')
                    {
                        gridGhostCategoryProduct.selectAll();
                        getGridStat_GhostCategoryProduct();
                    }
                    if (id=='delete')
                    {
                        deleteGhostCategoryProduct();
                    }
                });
        
            var gridGhostCategoryProduct = dhxlSCExtCheck.tabbar.cells("table_CAT_PROD_GHOST_CATEGORY_PRODUCT").attachGrid();
            gridGhostCategoryProduct.setImagePath("lib/js/imgs/");
            gridGhostCategoryProduct.enableSmartRendering(true);
            gridGhostCategoryProduct.enableMultiselect(true);
    
            gridGhostCategoryProduct.setHeader("<?php echo _l('Deleted products ID'); ?>");
            gridGhostCategoryProduct.setInitWidths("*");
            gridGhostCategoryProduct.setColAlign("left");
            gridGhostCategoryProduct.setColTypes("ro");
            gridGhostCategoryProduct.setColSorting("int");
            gridGhostCategoryProduct.attachHeader("#numeric_filter");
            gridGhostCategoryProduct.init();
    
            var xml = '<rows>';
            <?php foreach ($res as $product) { ?>
            xml = xml+'   <row id="<?php echo $product['id_product']; ?>">';
            xml = xml+'      <cell><![CDATA[<?php echo $product['id_product']; ?>]]></cell>';
            xml = xml+'   </row>';
            <?php } ?>
            xml = xml+'</rows>';
            gridGhostCategoryProduct.parse(xml);

            sbGhostCategoryProduct=dhxlSCExtCheck.tabbar.cells("table_CAT_PROD_GHOST_CATEGORY_PRODUCT").attachStatusBar();
            function getGridStat_GhostCategoryProduct(){
                var filteredRows=gridGhostCategoryProduct.getRowsNum();
                var selectedRows=(gridGhostCategoryProduct.getSelectedRowId()?gridGhostCategoryProduct.getSelectedRowId().split(',').length:0);
                sbGhostCategoryProduct.setText('<?php echo count($res).' '._l('Errors'); ?>'+" - <?php echo _l('Filter')._l(':'); ?> "+filteredRows+" - <?php echo _l('Selection')._l(':'); ?> "+selectedRows);
            }
            gridGhostCategoryProduct.attachEvent("onFilterEnd", function(elements){
                getGridStat_GhostCategoryProduct();
            });
            gridGhostCategoryProduct.attachEvent("onSelectStateChanged", function(id){
                getGridStat_GhostCategoryProduct();
            });
            getGridStat_GhostCategoryProduct();

            function deleteGhostCategoryProduct()
            {
                var selectedGhostCategoryProducts = gridGhostCategoryProduct.getSelectedRowId();
                if(selectedGhostCategoryProducts==null || selectedGhostCategoryProducts=="")
                    selectedGhostCategoryProducts = 0;
                if(selectedGhostCategoryProducts!="0")
                {
                    $.post("index.php?ajax=1&act=all_win-fixmyprestashop_actions&check=CAT_PROD_GHOST_CATEGORY_PRODUCT&id_lang="+SC_ID_LANG, { "action": "delete_category_product", "ids": selectedGhostCategoryProducts}, function(data){
                        dhxlSCExtCheck.tabbar.tabs("table_CAT_PROD_GHOST_CATEGORY_PRODUCT").close();

                         dhxlSCExtCheck.gridChecks.selectRowById('CAT_PROD_GHOST_CATEGORY_PRODUCT');
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
            'title' => _l('Ghost product'),
            'contentJs' => $content_js,
    ));
}
elseif (!empty($post_action) && $post_action == 'delete_category_product')
{
    $post_ids = Tools::getValue('ids');
    if (!empty($post_ids))
    {
        $sql = 'DELETE FROM '._DB_PREFIX_.'category_product WHERE id_product IN ('.pInSQL($post_ids).')';
        $res = dbExecuteForeignKeyOff($sql);
    }
}
