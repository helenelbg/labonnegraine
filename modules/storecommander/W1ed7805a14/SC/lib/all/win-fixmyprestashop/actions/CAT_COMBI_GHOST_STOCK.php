<?php
$post_action = Tools::getValue('action');
if (!empty($post_action) && $post_action == 'do_check')
{
    $sql = 'SELECT pl.id_product, pl.id_product_attribute
        FROM '._DB_PREFIX_.'stock pl 
        WHERE pl.id_product_attribute NOT IN (SELECT p.id_product_attribute FROM '._DB_PREFIX_.'product_attribute p)
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
    
            var tbGhostCombiStock = dhxlSCExtCheck.tabbar.cells("table_CAT_COMBI_GHOST_STOCK").attachToolbar();
            tbGhostCombiStock.setIconset('awesome');
            tbGhostCombiStock.addButton("selectall", 0, "", 'fa fa-bolt yellow', 'fa fa-bolt yellow');
            tbGhostCombiStock.setItemToolTip('selectall','<?php echo _l('Select all'); ?>');
            tbGhostCombiStock.addButton("delete", 0, "", 'fa fa-minus-circle red', 'fa fa-minus-circle red');
            tbGhostCombiStock.setItemToolTip('delete','<?php echo _l('Delete incomplete stock'); ?>');
            tbGhostCombiStock.attachEvent("onClick",
                function(id){
                    if (id=='selectall')
                    {
                        gridGhostCombiStock.selectAll();
                        getGridStat_GhostCombiStock();
                    }
                    if (id=='delete')
                    {
                        deleteGhostCombiStock();
                    }
                });
        
            var gridGhostCombiStock = dhxlSCExtCheck.tabbar.cells("table_CAT_COMBI_GHOST_STOCK").attachGrid();
            gridGhostCombiStock.setImagePath("lib/js/imgs/");
            gridGhostCombiStock.enableSmartRendering(true);
            gridGhostCombiStock.enableMultiselect(true);
    
            gridGhostCombiStock.setHeader("ID <?php echo _l('Product'); ?>,<?php echo _l('Deleted combinations ID'); ?>");
            gridGhostCombiStock.setInitWidths("100,*");
            gridGhostCombiStock.setColAlign("left,left");
            gridGhostCombiStock.setColTypes("ro,ro");
            gridGhostCombiStock.setColSorting("int,int");
            gridGhostCombiStock.attachHeader("#numeric_filter,#numeric_filter");
            gridGhostCombiStock.init();
    
            var xml = '<rows>';
            <?php foreach ($res as $product) { ?>
            xml = xml+'   <row id="<?php echo $product['id_product_attribute']; ?>">';
            xml = xml+'      <cell><![CDATA[<?php echo $product['id_product']; ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php echo $product['id_product_attribute']; ?>]]></cell>';
            xml = xml+'   </row>';
            <?php } ?>
            xml = xml+'</rows>';
            gridGhostCombiStock.parse(xml);

            sbGhostCombiStock=dhxlSCExtCheck.tabbar.cells("table_CAT_COMBI_GHOST_STOCK").attachStatusBar();
            function getGridStat_GhostCombiStock(){
                var filteredRows=gridGhostCombiStock.getRowsNum();
                var selectedRows=(gridGhostCombiStock.getSelectedRowId()?gridGhostCombiStock.getSelectedRowId().split(',').length:0);
                sbGhostCombiStock.setText('<?php echo count($res).' '._l('Errors'); ?>'+" - <?php echo _l('Filter')._l(':'); ?> "+filteredRows+" - <?php echo _l('Selection')._l(':'); ?> "+selectedRows);
            }
            gridGhostCombiStock.attachEvent("onFilterEnd", function(elements){
                getGridStat_GhostCombiStock();
            });
            gridGhostCombiStock.attachEvent("onSelectStateChanged", function(id){
                getGridStat_GhostCombiStock();
            });
            getGridStat_GhostCombiStock();

            function deleteGhostCombiStock()
            {
                var selectedGhostCombiStocks = gridGhostCombiStock.getSelectedRowId();
                if(selectedGhostCombiStocks==null || selectedGhostCombiStocks=="")
                    selectedGhostCombiStocks = 0;
                if(selectedGhostCombiStocks!="0")
                {
                    $.post("index.php?ajax=1&act=all_win-fixmyprestashop_actions&check=CAT_COMBI_GHOST_STOCK&id_lang="+SC_ID_LANG, { "action": "delete_stocks", "ids": selectedGhostCombiStocks}, function(data){
                        dhxlSCExtCheck.tabbar.tabs("table_CAT_COMBI_GHOST_STOCK").close();

                         dhxlSCExtCheck.gridChecks.selectRowById('CAT_COMBI_GHOST_STOCK');
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
            'title' => _l('Ghost combi stock'),
            'contentJs' => $content_js,
    ));
}
elseif (!empty($post_action) && $post_action == 'delete_stocks')
{
    $post_ids = Tools::getValue('ids');
    if (!empty($post_ids))
    {
        $sql = 'DELETE FROM '._DB_PREFIX_.'stock WHERE id_product_attribute IN ('.pInSQL($post_ids).')';
        $res = dbExecuteForeignKeyOff($sql);
    }
}
