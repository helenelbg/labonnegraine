<?php
$post_action = Tools::getValue('action');
if (!empty($post_action) && $post_action == 'do_check')
{
    $sql = 'SELECT wpl.id_product, wpl.id_product_attribute, wpl.id_warehouse
        FROM '._DB_PREFIX_.'warehouse_product_location wpl 
            INNER JOIN '._DB_PREFIX_.'product p ON (p.id_product=wpl.id_product)
                INNER JOIN '._DB_PREFIX_.'product_shop ps ON (ps.id_product=p.id_product AND ps.id_shop=p.id_shop_default)
                INNER JOIN '._DB_PREFIX_.'stock_available sa ON (sa.id_product=p.id_product AND sa.id_shop=p.id_shop_default AND sa.id_product_attribute=0)
        WHERE wpl.id_product_attribute NOT IN (SELECT s.id_product_attribute FROM '._DB_PREFIX_."stock s WHERE s.id_product=wpl.id_product AND s.id_product_attribute=wpl.id_product_attribute AND s.id_warehouse=wpl.id_warehouse)
            AND ps.advanced_stock_management = '1'
            AND sa.depends_on_stock=1 
        LIMIT 1500";
    $res = Db::getInstance()->ExecuteS($sql);

    $content = '';
    $content_js = '';
    $results = 'OK';
    if (!empty($res) && count($res) > 0)
    {
        $results = 'KO';
        ob_start(); ?>
        <script type="text/javascript">
    
            var tbCombiWithoutStockRow = dhxlSCExtCheck.tabbar.cells("table_CAT_COMBI_WITHOUT_STOCK_ROW").attachToolbar();
            tbCombiWithoutStockRow.setIconset('awesome');
            tbCombiWithoutStockRow.addButton("selectall", 0, "", 'fa fa-bolt yellow', 'fa fa-bolt yellow');
            tbCombiWithoutStockRow.setItemToolTip('selectall','<?php echo _l('Select all'); ?>');
            tbCombiWithoutStockRow.addButton("delete", 0, "", 'fa fa-minus-circle red', 'fa fa-minus-circle red');
            tbCombiWithoutStockRow.setItemToolTip('delete','<?php echo _l('Delete associations'); ?>');
            tbCombiWithoutStockRow.addButton("add", 0, "", 'fa fa-plus-circle green', 'fa fa-plus-circle green');
            tbCombiWithoutStockRow.setItemToolTip('add','<?php echo _l('Create row in stock'); ?>');
            tbCombiWithoutStockRow.attachEvent("onClick",
                function(id){
                    if (id=='selectall')
                    {
                        gridCombiWithoutStockRow.selectAll();
                        getGridStat_CombiWithoutStockRow();
                    }
                    if (id=='delete')
                    {
                        deleteCombiWithoutStockRow()
                    }
                    if (id=='add')
                    {
                        addCombiWithoutStockRow()
                    }
                });
        
            var gridCombiWithoutStockRow = dhxlSCExtCheck.tabbar.cells("table_CAT_COMBI_WITHOUT_STOCK_ROW").attachGrid();
            gridCombiWithoutStockRow.setImagePath("lib/js/imgs/");
            gridCombiWithoutStockRow.enableSmartRendering(false);
            gridCombiWithoutStockRow.enableMultiselect(true);
    
            gridCombiWithoutStockRow.setHeader("ID <?php echo _l('Product'); ?>,<?php echo _l('Product'); ?>,ID <?php echo _l('combination'); ?>,<?php echo _l('Warehouse'); ?>");
            gridCombiWithoutStockRow.setInitWidths("60,250,60,150");
            gridCombiWithoutStockRow.setColAlign("left,right,right,right");
            gridCombiWithoutStockRow.setColTypes("ro,ro,ro,ro");
            gridCombiWithoutStockRow.setColSorting("int,str,int,str");
            gridCombiWithoutStockRow.attachHeader("#numeric_filter,#text_filter,#numeric_filter,#text_filter");
            gridCombiWithoutStockRow.init();
    
            var xml = '<rows>';
            <?php foreach ($res as $row)
        {
            $product = new Product($row['id_product'], false, $id_lang);
            $attr = new Combination($row['id_product_attribute'], $id_lang, $product->id_shop_default);
            $warehouse = new Warehouse($row['id_warehouse']); ?>
            xml = xml+'   <row id="<?php echo $row['id_product'].'_'.$row['id_product_attribute'].'_'.$row['id_warehouse']; ?>">';
            xml = xml+'      <cell><![CDATA[<?php echo $row['id_product']; ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php echo str_replace("'", "\'", str_replace("\'", "'", $product->name)); ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php echo $row['id_product_attribute']; ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php echo str_replace("'", "\'", str_replace("\'", "'", $warehouse->name)); ?>]]></cell>';
            xml = xml+'   </row>';
            <?php
        } ?>
            xml = xml+'</rows>';
            gridCombiWithoutStockRow.parse(xml);

            sbCombiWithoutStockRow=dhxlSCExtCheck.tabbar.cells("table_CAT_COMBI_WITHOUT_STOCK_ROW").attachStatusBar();
            function getGridStat_CombiWithoutStockRow(){
                var filteredRows=gridCombiWithoutStockRow.getRowsNum();
                var selectedRows=(gridCombiWithoutStockRow.getSelectedRowId()?gridCombiWithoutStockRow.getSelectedRowId().split(',').length:0);
                sbCombiWithoutStockRow.setText('<?php echo count($res).' '._l('Errors'); ?>'+" - <?php echo _l('Filter')._l(':'); ?> "+filteredRows+" - <?php echo _l('Selection')._l(':'); ?> "+selectedRows);
            }
            gridCombiWithoutStockRow.attachEvent("onFilterEnd", function(elements){
                getGridStat_CombiWithoutStockRow();
            });
            gridCombiWithoutStockRow.attachEvent("onSelectStateChanged", function(id){
                getGridStat_CombiWithoutStockRow();
            });
            getGridStat_CombiWithoutStockRow();

            function deleteCombiWithoutStockRow()
            {
                var selectedCombiWithoutStockRows = gridCombiWithoutStockRow.getSelectedRowId();
                if(selectedCombiWithoutStockRows==null || selectedCombiWithoutStockRows=="")
                    selectedCombiWithoutStockRows = 0;
                if(selectedCombiWithoutStockRows!="0")
                {
                    $.post("index.php?ajax=1&act=all_win-fixmyprestashop_actions&check=CAT_COMBI_WITHOUT_STOCK_ROW&id_lang="+SC_ID_LANG, { "action": "delete_association", "ids": selectedCombiWithoutStockRows}, function(data){
                        dhxlSCExtCheck.tabbar.tabs("table_CAT_COMBI_WITHOUT_STOCK_ROW").close();

                         dhxlSCExtCheck.gridChecks.selectRowById('CAT_COMBI_WITHOUT_STOCK_ROW');
                         doCheck(false);
                    });
                }
            }
            function addCombiWithoutStockRow()
            {
                var selectedCombiWithoutStockRows = gridCombiWithoutStockRow.getSelectedRowId();
                if(selectedCombiWithoutStockRows==null || selectedCombiWithoutStockRows=="")
                    selectedCombiWithoutStockRows = 0;
                if(selectedCombiWithoutStockRows!="0")
                {
                    $.post("index.php?ajax=1&act=all_win-fixmyprestashop_actions&check=CAT_COMBI_WITHOUT_STOCK_ROW&id_lang="+SC_ID_LANG, { "action": "add_association", "ids": selectedCombiWithoutStockRows}, function(data){
                        dhxlSCExtCheck.tabbar.tabs("table_CAT_COMBI_WITHOUT_STOCK_ROW").close();

                         dhxlSCExtCheck.gridChecks.selectRowById('CAT_COMBI_WITHOUT_STOCK_ROW');
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
            'title' => _l('Not sharing shop'),
            'contentJs' => $content_js,
    ));
}
elseif (!empty($post_action) && $post_action == 'delete_association')
{
    $post_ids = Tools::getValue('ids');
    if (!empty($post_ids))
    {
        $ids = explode(',', $post_ids);
        foreach ($ids as $id)
        {
            list($id_product, $id_product_attribute, $id_warehouse) = explode('_', $id);

            $sql = 'DELETE FROM '._DB_PREFIX_."warehouse_product_location 
                WHERE id_product = '".(int) $id_product."' 
                    AND id_product_attribute = '".(int) $id_product_attribute."' 
                    AND id_warehouse = '".(int) $id_warehouse."'";
            $res = dbExecuteForeignKeyOff($sql);
        }
    }
}
elseif (!empty($post_action) && $post_action == 'add_association')
{
    $post_ids = Tools::getValue('ids');
    if (!empty($post_ids))
    {
        $ids = explode(',', $post_ids);
        foreach ($ids as $id)
        {
            list($id_product, $id_product_attribute, $id_warehouse) = explode('_', $id);

            $attr = new Combination((int) $id_product_attribute, $id_lang);

            $sql = 'INSERT INTO '._DB_PREFIX_."stock (id_warehouse,id_product,id_product_attribute,reference,ean13,upc,price_te)
                VALUES ('".(int) $id_warehouse."','".(int) $id_product."','".(int) $id_product_attribute."','".psql($attr->reference)."','".psql($attr->ean13)."','".psql($attr->upc)."','".psql($attr->wholesale_price)."')";
            $res = dbExecuteForeignKeyOff($sql);
        }
    }
}
