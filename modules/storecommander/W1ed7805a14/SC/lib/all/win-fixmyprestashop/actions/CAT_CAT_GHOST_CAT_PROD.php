<?php
$post_action = Tools::getValue('action');
if (!empty($post_action) && $post_action == 'do_check')
{
    $sql = 'select pl.id_category, pl.id_product from '._DB_PREFIX_.'category_product pl where pl.id_category not in (select p.id_category from '._DB_PREFIX_.'category p) limit 1500';
    $res = Db::getInstance()->ExecuteS($sql);

    $content = '';
    $content_js = '';
    $results = 'OK';
    if (!empty($res) && count($res) > 0)
    {
        $results = 'KO';
        ob_start(); ?>
        <script type="text/javascript">
    
            var tbGhostCategoryProduct = dhxlSCExtCheck.tabbar.cells("table_CAT_CAT_GHOST_CAT_PROD").attachToolbar();
            tbGhostCategoryProduct.setIconset('awesome');
            tbGhostCategoryProduct.addButton("exportcsv", 0, "", 'fad fa-file-csv green', 'fad fa-file-csv green');
            tbGhostCategoryProduct.setItemToolTip('exportcsv','<?php echo _l('Export grid to clipboard in CSV format for MSExcel with tab delimiter.'); ?>');
            tbGhostCategoryProduct.addButton("delete", 0, "", 'fa fa-minus-circle red', 'fa fa-minus-circle red');
            tbGhostCategoryProduct.setItemToolTip('delete','<?php echo _l('Delete incomplete categories'); ?>');
            tbGhostCategoryProduct.addButton("selectall", 0, "", 'fa fa-bolt yellow', 'fa fa-bolt yellow');
            tbGhostCategoryProduct.setItemToolTip('selectall','<?php echo _l('Select all'); ?>');
            tbGhostCategoryProduct.attachEvent("onClick", function(id){
                switch(id)
                {
                    case 'exportcsv':
                        displayQuickExportWindow(gridGhostCategoryProduct,1);
                        break
                    case 'selectall':
                        gridGhostCategoryProduct.selectAll();
                        getGridStat_GhostCategoryProduct();
                        break;
                    case 'delete':
                        deleteGhostCategoryProduct();
                        break;
                }
            });
        
            var gridGhostCategoryProduct = dhxlSCExtCheck.tabbar.cells("table_CAT_CAT_GHOST_CAT_PROD").attachGrid();
            gridGhostCategoryProduct.setImagePath("lib/js/imgs/");
            gridGhostCategoryProduct.enableSmartRendering(true);
            gridGhostCategoryProduct.enableMultiselect(true);
    
            gridGhostCategoryProduct.setHeader("<?php echo _l('ID deleted categories'); ?>,<?php echo _l('Associated products ID'); ?>");
            gridGhostCategoryProduct.setInitWidths("*");
            gridGhostCategoryProduct.setColAlign("left","left");
            gridGhostCategoryProduct.setColTypes("ro,ro");
            gridGhostCategoryProduct.setColSorting("int","int");
            gridGhostCategoryProduct.attachHeader("#numeric_filter","#numeric_filter");
            gridGhostCategoryProduct.init();
    
            var xml = '<rows>';
            <?php foreach ($res as $row)
            { ?>
            xml = xml+'   <row id="<?php echo $row['id_category']; ?>_<?php echo $row['id_product']; ?>">';
            xml = xml+'      <cell><![CDATA[<?php echo $row['id_category']; ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php echo $row['id_product']; ?>]]></cell>';
            xml = xml+'   </row>';
            <?php } ?>
            xml = xml+'</rows>';
            gridGhostCategoryProduct.parse(xml);

            sbGhostCategoryProduct=dhxlSCExtCheck.tabbar.cells("table_CAT_CAT_GHOST_CAT_PROD").attachStatusBar();
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


                    $.post("index.php?ajax=1&act=all_win-fixmyprestashop_actions&check=CAT_CAT_GHOST_CAT_PROD&id_lang="+SC_ID_LANG, { "action": "delete_categories", "ids": selectedGhostCategoryProducts}, function(data){
                        dhxlSCExtCheck.tabbar.tabs("table_CAT_CAT_GHOST_CAT_PROD").close();

                         dhxlSCExtCheck.gridChecks.selectRowById('CAT_CAT_GHOST_CAT_PROD');
                         doCheck(false);
                    });

            }
        </script>
        <?php
        $content_js = ob_get_clean();
    }
    echo json_encode(array(
            'results' => $results,
            'contentType' => 'grid',
            'content' => $content,
            'title' => _l('Ghost cat_prod'),
            'contentJs' => $content_js,
    ));
}
elseif (!empty($post_action) && $post_action == 'delete_categories')
{
    $post_ids = Tools::getValue('ids');
    if (isset($post_ids) && !empty($post_ids))
    {
        foreach (explode(',', $post_ids) as $id)
        {
            $categ_id = explode('_', $id)[0];
            $prod_id = explode('_', $id)[1];
            $sql = 'DELETE FROM '._DB_PREFIX_.'category_product 
                WHERE id_category = '.(int) $categ_id.'
                AND id_product ='.(int) $prod_id;
            $res = dbExecuteForeignKeyOff($sql);
        }
    }
}
