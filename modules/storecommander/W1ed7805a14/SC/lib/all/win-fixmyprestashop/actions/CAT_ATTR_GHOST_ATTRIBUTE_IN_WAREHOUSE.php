<?php
$post_action = Tools::getValue('action');
if (!empty($post_action) && $post_action == 'do_check')
{
    $sql = 'SELECT pl.*
            FROM '._DB_PREFIX_.'warehouse_product_location pl
            WHERE pl.id_product_attribute not in (SELECT p.id_product_attribute FROM '._DB_PREFIX_.'product_attribute p)
                AND id_product_attribute!=0
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
    
            var tbGhostAttributWarehouse = dhxlSCExtCheck.tabbar.cells("table_CAT_ATTR_GHOST_ATTRIBUTE_IN_WAREHOUSE").attachToolbar();
            tbGhostAttributWarehouse.setIconset('awesome');
            tbGhostAttributWarehouse.addButton("selectall", 0, "", 'fa fa-bolt yellow', 'fa fa-bolt yellow');
            tbGhostAttributWarehouse.setItemToolTip('selectall','<?php echo _l('Select all'); ?>');
            tbGhostAttributWarehouse.addButton("delete", 0, "", 'fa fa-minus-circle red', 'fa fa-minus-circle red');
            tbGhostAttributWarehouse.setItemToolTip('delete','<?php echo _l('Delete incomplete combinations'); ?>');
            tbGhostAttributWarehouse.attachEvent("onClick",
                function(id){
                    if (id=='selectall')
                    {
                        gridGhostAttributWarehouse.selectAll();
                        getGridStat_GhostAttributWarehouse();
                    }
                    if (id=='delete')
                    {
                        deleteGhostAttributWarehouse();
                    }
                });
        
            var gridGhostAttributWarehouse = dhxlSCExtCheck.tabbar.cells("table_CAT_ATTR_GHOST_ATTRIBUTE_IN_WAREHOUSE").attachGrid();
            gridGhostAttributWarehouse.setImagePath("lib/js/imgs/");
            gridGhostAttributWarehouse.enableSmartRendering(true);
            gridGhostAttributWarehouse.enableMultiselect(true);
    
            gridGhostAttributWarehouse.setHeader("ID,<?php echo _l('Name'); ?>,<?php echo _l('Combination ID'); ?>,<?php echo _l('Warehouse'); ?>");
            gridGhostAttributWarehouse.setInitWidths("40, 100, 40,200");
            gridGhostAttributWarehouse.setColAlign("left,left,left,left");
            gridGhostAttributWarehouse.setColTypes("ro,ro,ro,ro");
            gridGhostAttributWarehouse.setColSorting("int,str,int,str");
            gridGhostAttributWarehouse.attachHeader("#numeric_filter,#text_filter,#numeric_filter,#text_filter");
            gridGhostAttributWarehouse.init();

            var xml = '<rows>';
            <?php foreach ($res as $row)
        {
            $warehouse = new Warehouse($row['id_warehouse']);
            $product = new Product($row['id_product'], false, $id_lang); ?>
            xml = xml+'   <row id="<?php echo $row['id_product_attribute'].'_'.$row['id_warehouse']; ?>">';
            xml = xml+'      <cell><![CDATA[<?php echo $row['id_product']; ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php echo str_replace("'", "\'", $product->name); ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php echo $row['id_product_attribute']; ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php echo str_replace("'", "\'", $warehouse->name); ?>]]></cell>';
            xml = xml+'   </row>';
            <?php
        } ?>
            xml = xml+'</rows>';
            gridGhostAttributWarehouse.parse(xml);

            sbGhostAttributWarehouse=dhxlSCExtCheck.tabbar.cells("table_CAT_ATTR_GHOST_ATTRIBUTE_IN_WAREHOUSE").attachStatusBar();
            function getGridStat_GhostAttributWarehouse(){
                var filteredRows=gridGhostAttributWarehouse.getRowsNum();
                var selectedRows=(gridGhostAttributWarehouse.getSelectedRowId()?gridGhostAttributWarehouse.getSelectedRowId().split(',').length:0);
                sbGhostAttributWarehouse.setText('<?php echo count($res).' '._l('Errors'); ?>'+" - <?php echo _l('Filter')._l(':'); ?> "+filteredRows+" - <?php echo _l('Selection')._l(':'); ?> "+selectedRows);
            }
            gridGhostAttributWarehouse.attachEvent("onFilterEnd", function(elements){
                getGridStat_GhostAttributWarehouse();
            });
            gridGhostAttributWarehouse.attachEvent("onSelectStateChanged", function(id){
                getGridStat_GhostAttributWarehouse();
            });
            getGridStat_GhostAttributWarehouse();

            function deleteGhostAttributWarehouse()
            {
                var selectedGhostAttributWarehouses = gridGhostAttributWarehouse.getSelectedRowId();
                if(selectedGhostAttributWarehouses==null || selectedGhostAttributWarehouses=="")
                    selectedGhostAttributWarehouses = 0;
                if(selectedGhostAttributWarehouses!="0")
                {
                    $.post("index.php?ajax=1&act=all_win-fixmyprestashop_actions&check=CAT_ATTR_GHOST_ATTRIBUTE_IN_WAREHOUSE&id_lang="+SC_ID_LANG, { "action": "delete_attributes", "ids": selectedGhostAttributWarehouses}, function(data){
                        dhxlSCExtCheck.tabbar.tabs("table_CAT_ATTR_GHOST_ATTRIBUTE_IN_WAREHOUSE").close();

                         dhxlSCExtCheck.gridChecks.selectRowById('CAT_ATTR_GHOST_ATTRIBUTE_IN_WAREHOUSE');
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
            'title' => _l('Ghost attr.'),
            'contentJs' => $content_js,
    ));
}
elseif (!empty($post_action) && $post_action == 'delete_attributes')
{
    $post_ids = Tools::getValue('ids');
    if (!empty($post_ids))
    {
        $ids = explode(',', $post_ids);
        foreach ($ids as $id)
        {
            list($id_product_attribute, $id_warehouse) = explode('_', $id);

            $sql = 'DELETE FROM '._DB_PREFIX_.'warehouse_product_location WHERE id_product_attribute = '.(int) $id_product_attribute.' AND id_warehouse = '.(int) $id_warehouse;
            dbExecuteForeignKeyOff($sql);
        }
    }
}
