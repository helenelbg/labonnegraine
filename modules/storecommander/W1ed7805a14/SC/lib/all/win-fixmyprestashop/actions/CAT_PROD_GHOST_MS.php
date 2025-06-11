<?php
$post_action = Tools::getValue('action');
if (!empty($post_action) && $post_action == 'do_check')
{
    $sql = 'SELECT p.id_product FROM '._DB_PREFIX_.'product p where p.id_product NOT IN (SELECT ps.id_product FROM '._DB_PREFIX_.'product_shop ps) ORDER BY id_product ASC LIMIT 1500';
    $res = Db::getInstance()->ExecuteS($sql);

    $content = '';
    $content_js = '';
    $results = 'OK';
    if (!empty($res) && count($res) > 0)
    {
        $results = 'KO';
        ob_start(); ?>
        <script type="text/javascript">

            var tbGhostProduct = dhxlSCExtCheck.tabbar.cells("table_CAT_PROD_GHOST_MS").attachToolbar();
            tbGhostProduct.setIconset('awesome');
            tbGhostProduct.addButton("selectall", 0, "", 'fa fa-bolt yellow', 'fa fa-bolt yellow');
            tbGhostProduct.setItemToolTip('selectall','<?php echo _l('Select all'); ?>');
            tbGhostProduct.addButton("delete", 0, "", 'fa fa-minus-circle red', 'fa fa-minus-circle red');
            tbGhostProduct.setItemToolTip('delete','<?php echo _l('Delete incomplete products'); ?>');
            tbGhostProduct.attachEvent("onClick",
                function(id){
                    if (id=='selectall')
                    {
                        gridGhostProduct.selectAll();
                        getGridStat_GhostProduct();
                    }
                    if (id=='delete')
                    {
                        deleteGhostProduct();
                    }
                });

            var gridGhostProduct = dhxlSCExtCheck.tabbar.cells("table_CAT_PROD_GHOST_MS").attachGrid();
            gridGhostProduct.setImagePath("lib/js/imgs/");
            gridGhostProduct.enableSmartRendering(true);
            gridGhostProduct.enableMultiselect(true);

            gridGhostProduct.setHeader("ID <?php echo _l('product'); ?>");
            gridGhostProduct.setInitWidths("100");
            gridGhostProduct.setColAlign("left");
            gridGhostProduct.setColTypes("ro");
            gridGhostProduct.setColSorting("int");
            gridGhostProduct.attachHeader("#numeric_filter");
            gridGhostProduct.init();

            var xml = '<rows>';
            <?php foreach ($res as $attribute) { ?>
            xml = xml+'   <row id="<?php echo $attribute['id_product']; ?>">';
            xml = xml+'      <cell><![CDATA[<?php echo $attribute['id_product']; ?>]]></cell>';
            xml = xml+'   </row>';
            <?php } ?>
            xml = xml+'</rows>';
            gridGhostProduct.parse(xml);

            sbGhostProduct=dhxlSCExtCheck.tabbar.cells("table_CAT_PROD_GHOST_MS").attachStatusBar();
            function getGridStat_GhostProduct(){
                var filteredRows=gridGhostProduct.getRowsNum();
                var selectedRows=(gridGhostProduct.getSelectedRowId()?gridGhostProduct.getSelectedRowId().split(',').length:0);
                sbGhostProduct.setText('<?php echo count($res).' '._l('Errors'); ?>'+" - <?php echo _l('Filter')._l(':'); ?> "+filteredRows+" - <?php echo _l('Selection')._l(':'); ?> "+selectedRows);
            }
            gridGhostProduct.attachEvent("onFilterEnd", function(elements){
                getGridStat_GhostProduct();
            });
            gridGhostProduct.attachEvent("onSelectStateChanged", function(id){
                getGridStat_GhostProduct();
            });
            getGridStat_GhostProduct();

            function deleteGhostProduct()
            {
                var selectedGhostProducts = gridGhostProduct.getSelectedRowId();
                if(selectedGhostProducts==null || selectedGhostProducts=="")
                    selectedGhostProducts = 0;
                if(selectedGhostProducts!="0")
                {
                    $.post("index.php?ajax=1&act=all_win-fixmyprestashop_actions&check=CAT_PROD_GHOST_MS&id_lang="+SC_ID_LANG, { "action": "delete_products", "ids": selectedGhostProducts}, function(data){
                        dhxlSCExtCheck.tabbar.tabs("table_CAT_PROD_GHOST_MS").close();

                        dhxlSCExtCheck.gridChecks.selectRowById('CAT_PROD_GHOST_MS');
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
        'title' => _l('Ghost pdt.'),
        'contentJs' => $content_js,
    ));
}
elseif (!empty($post_action) && $post_action == 'delete_products')
{
    $post_ids = explode(',', Tools::getValue('ids'));
    if (!empty($post_ids))
    {
        $shops = Shop::getShops(false, null, true);

        foreach ($post_ids as $id_product)
        {
            if (is_numeric($id_product) && $id_product)
            {
                $sql = 'DELETE FROM '._DB_PREFIX_."product WHERE id_product=" .(int) $id_product . "";
                dbExecuteForeignKeyOff($sql);
            }
        }
    }
}
