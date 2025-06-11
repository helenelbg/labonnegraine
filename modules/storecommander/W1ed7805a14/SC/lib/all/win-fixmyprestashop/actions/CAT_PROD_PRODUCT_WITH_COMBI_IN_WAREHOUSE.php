<?php
$post_action = Tools::getValue('action');
if (!empty($post_action) && $post_action == 'do_check')
{
    $sql = 'SELECT pl.*
            FROM '._DB_PREFIX_.'warehouse_product_location pl 
            WHERE pl.id_product IN (SELECT p.id_product FROM '._DB_PREFIX_.'warehouse_product_location p WHERE p.id_product_attribute!=0 AND p.id_product = pl.id_product)
                AND id_product_attribute=0 
            ORDER BY id_product ASC LIMIT 1500';
    $res = Db::getInstance()->ExecuteS($sql);

    $content = '';
    $content_js = '';
    $results = 'OK';
    if (!empty($res) && count($res) > 0)
    {
        $results = 'KO';
        ob_start(); ?>
        <script type="text/javascript">
    
            var tbProductWithCombiInWarehouse = dhxlSCExtCheck.tabbar.cells("table_CAT_PROD_PRODUCT_WITH_COMBI_IN_WAREHOUSE").attachToolbar();
            tbProductWithCombiInWarehouse.setIconset('awesome');
            tbProductWithCombiInWarehouse.addButton("selectall", 0, "", 'fa fa-bolt yellow', 'fa fa-bolt yellow');
            tbProductWithCombiInWarehouse.setItemToolTip('selectall','<?php echo _l('Select all'); ?>');
            tbProductWithCombiInWarehouse.addButton("delete", 0, "", 'fa fa-minus-circle red', 'fa fa-minus-circle red');
            tbProductWithCombiInWarehouse.setItemToolTip('delete','<?php echo _l('Delete invalid rows'); ?>');
            tbProductWithCombiInWarehouse.attachEvent("onClick",
                function(id){
                    if (id=='selectall')
                    {
                        gridProductWithCombiInWarehouse.selectAll();
                        getGridStat_ProductWithCombiInWarehouse();
                    }
                    if (id=='delete')
                    {
                        deleteProductWithCombiInWarehouse();
                    }
                });
        
            var gridProductWithCombiInWarehouse = dhxlSCExtCheck.tabbar.cells("table_CAT_PROD_PRODUCT_WITH_COMBI_IN_WAREHOUSE").attachGrid();
            gridProductWithCombiInWarehouse.setImagePath("lib/js/imgs/");
            gridProductWithCombiInWarehouse.enableSmartRendering(true);
            gridProductWithCombiInWarehouse.enableMultiselect(true);
    
            gridProductWithCombiInWarehouse.setHeader("ID,<?php echo _l('Name'); ?>,<?php echo _l('Warehouse'); ?>");
            gridProductWithCombiInWarehouse.setInitWidths("40, 100, *");
            gridProductWithCombiInWarehouse.setColAlign("left,left,left");
            gridProductWithCombiInWarehouse.setColTypes("ro,ro,ro");
            gridProductWithCombiInWarehouse.setColSorting("int,str,str");
            gridProductWithCombiInWarehouse.attachHeader("#numeric_filter,#text_filter,#text_filter");
            gridProductWithCombiInWarehouse.init();

            var xml = '<rows>';
            <?php foreach ($res as $row)
        {
            $warehouse = new Warehouse($row['id_warehouse']);
            $product = new Product($row['id_product'], false, $id_lang); ?>
            xml = xml+'   <row id="<?php echo $row['id_product'].'_'.$row['id_warehouse']; ?>">';
            xml = xml+'      <cell><![CDATA[<?php echo $row['id_product']; ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php echo str_replace("'", "\'", $product->name); ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php echo str_replace("'", "\'", $warehouse->name); ?>]]></cell>';
            xml = xml+'   </row>';
            <?php
        } ?>
            xml = xml+'</rows>';
            gridProductWithCombiInWarehouse.parse(xml);

            sbProductWithCombiInWarehouse=dhxlSCExtCheck.tabbar.cells("table_CAT_PROD_PRODUCT_WITH_COMBI_IN_WAREHOUSE").attachStatusBar();
            function getGridStat_ProductWithCombiInWarehouse(){
                var filteredRows=gridProductWithCombiInWarehouse.getRowsNum();
                var selectedRows=(gridProductWithCombiInWarehouse.getSelectedRowId()?gridProductWithCombiInWarehouse.getSelectedRowId().split(',').length:0);
                sbProductWithCombiInWarehouse.setText('<?php echo count($res).' '._l('Errors'); ?>'+" - <?php echo _l('Filter')._l(':'); ?> "+filteredRows+" - <?php echo _l('Selection')._l(':'); ?> "+selectedRows);
            }
            gridProductWithCombiInWarehouse.attachEvent("onFilterEnd", function(elements){
                getGridStat_ProductWithCombiInWarehouse();
            });
            gridProductWithCombiInWarehouse.attachEvent("onSelectStateChanged", function(id){
                getGridStat_ProductWithCombiInWarehouse();
            });
            getGridStat_ProductWithCombiInWarehouse();

            function deleteProductWithCombiInWarehouse()
            {
                var selectedProductWithCombiInWarehouses = gridProductWithCombiInWarehouse.getSelectedRowId();
                if(selectedProductWithCombiInWarehouses==null || selectedProductWithCombiInWarehouses=="")
                    selectedProductWithCombiInWarehouses = 0;
                if(selectedProductWithCombiInWarehouses!="0")
                {
                    $.post("index.php?ajax=1&act=all_win-fixmyprestashop_actions&check=CAT_PROD_PRODUCT_WITH_COMBI_IN_WAREHOUSE&id_lang="+SC_ID_LANG, { "action": "delete_products", "ids": selectedProductWithCombiInWarehouses}, function(data){
                        dhxlSCExtCheck.tabbar.tabs("table_CAT_PROD_PRODUCT_WITH_COMBI_IN_WAREHOUSE").close();

                         dhxlSCExtCheck.gridChecks.selectRowById('CAT_PROD_PRODUCT_WITH_COMBI_IN_WAREHOUSE');
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
            'title' => _l('Alone Pdt. in Warehouse'),
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
            list($id_product, $id_warehouse) = explode('_', $id);

            $sql = 'DELETE FROM '._DB_PREFIX_.'warehouse_product_location WHERE id_product_attribute = 0 AND id_product = '.(int) $id_product.' AND id_warehouse = '.(int) $id_warehouse;
            dbExecuteForeignKeyOff($sql);
        }
    }
}
