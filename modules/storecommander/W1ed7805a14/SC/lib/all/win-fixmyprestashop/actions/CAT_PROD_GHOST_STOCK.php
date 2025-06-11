<?php
$post_action = Tools::getValue('action');
if (!empty($post_action) && $post_action == 'do_check')
{
    $sql = 'SELECT pl.id_product
        FROM '._DB_PREFIX_.'stock pl 
        WHERE pl.id_product NOT IN (SELECT p.id_product FROM '._DB_PREFIX_.'product p) 
            AND pl.id_product_attribute = 0
        LIMIT 1500';
    $res = Db::getInstance()->ExecuteS($sql);

    $content = '';
    $content_js = '';
    $results = 'OK';
    if (!empty($res) && count($res) > 0)
    {
        $results = 'KO';
        ob_start(); ?>
        <script type="text/javascript">
    
            var tbGhostStock = dhxlSCExtCheck.tabbar.cells("table_CAT_PROD_GHOST_STOCK").attachToolbar();
            tbGhostStock.setIconset('awesome');
            tbGhostStock.addButton("selectall", 0, "", 'fa fa-bolt yellow', 'fa fa-bolt yellow');
            tbGhostStock.setItemToolTip('selectall','<?php echo _l('Select all'); ?>');
            tbGhostStock.addButton("delete", 0, "", 'fa fa-minus-circle red', 'fa fa-minus-circle red');
            tbGhostStock.setItemToolTip('delete','<?php echo _l('Delete incomplete stock'); ?>');
            tbGhostStock.attachEvent("onClick",
                function(id){
                    if (id=='selectall')
                    {
                        gridGhostStock.selectAll();
                        getGridStat_GhostStock();
                    }
                    if (id=='delete')
                    {
                        deleteGhostStock();
                    }
                });
        
            var gridGhostStock = dhxlSCExtCheck.tabbar.cells("table_CAT_PROD_GHOST_STOCK").attachGrid();
            gridGhostStock.setImagePath("lib/js/imgs/");
            gridGhostStock.enableSmartRendering(true);
            gridGhostStock.enableMultiselect(true);
    
            gridGhostStock.setHeader("<?php echo _l('Deleted products ID'); ?>");
            gridGhostStock.setInitWidths("*");
            gridGhostStock.setColAlign("left");
            gridGhostStock.setColTypes("ro");
            gridGhostStock.setColSorting("int");
            gridGhostStock.attachHeader("#numeric_filter");
            gridGhostStock.init();
    
            var xml = '<rows>';
            <?php foreach ($res as $product) { ?>
            xml = xml+'   <row id="<?php echo $product['id_product']; ?>">';
            xml = xml+'      <cell><![CDATA[<?php echo $product['id_product']; ?>]]></cell>';
            xml = xml+'   </row>';
            <?php } ?>
            xml = xml+'</rows>';
            gridGhostStock.parse(xml);

            sbGhostStock=dhxlSCExtCheck.tabbar.cells("table_CAT_PROD_GHOST_STOCK").attachStatusBar();
            function getGridStat_GhostStock(){
                var filteredRows=gridGhostStock.getRowsNum();
                var selectedRows=(gridGhostStock.getSelectedRowId()?gridGhostStock.getSelectedRowId().split(',').length:0);
                sbGhostStock.setText('<?php echo count($res).' '._l('Errors'); ?>'+" - <?php echo _l('Filter')._l(':'); ?> "+filteredRows+" - <?php echo _l('Selection')._l(':'); ?> "+selectedRows);
            }
            gridGhostStock.attachEvent("onFilterEnd", function(elements){
                getGridStat_GhostStock();
            });
            gridGhostStock.attachEvent("onSelectStateChanged", function(id){
                getGridStat_GhostStock();
            });
            getGridStat_GhostStock();

            function deleteGhostStock()
            {
                var selectedGhostStocks = gridGhostStock.getSelectedRowId();
                if(selectedGhostStocks==null || selectedGhostStocks=="")
                    selectedGhostStocks = 0;
                if(selectedGhostStocks!="0")
                {
                    $.post("index.php?ajax=1&act=all_win-fixmyprestashop_actions&check=CAT_PROD_GHOST_STOCK&id_lang="+SC_ID_LANG, { "action": "delete_stocks", "ids": selectedGhostStocks}, function(data){
                        dhxlSCExtCheck.tabbar.tabs("table_CAT_PROD_GHOST_STOCK").close();

                         dhxlSCExtCheck.gridChecks.selectRowById('CAT_PROD_GHOST_STOCK');
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
            'title' => _l('Ghost stock'),
            'contentJs' => $content_js,
    ));
}
elseif (!empty($post_action) && $post_action == 'delete_stocks')
{
    $post_ids = Tools::getValue('ids');
    if (!empty($post_ids))
    {
        $sql = 'DELETE FROM '._DB_PREFIX_.'stock WHERE id_product IN ('.pInSQL($post_ids).') AND id_product_attribute = 0';
        $res = dbExecuteForeignKeyOff($sql);
    }
}
