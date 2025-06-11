<?php
$post_action = Tools::getValue('action');
if (!empty($post_action) && $post_action == 'do_check')
{
    $sql = 'SELECT *
            FROM `'._DB_PREFIX_.'stock_available` sa
            WHERE 
                sa.id_product NOT IN (SELECT p.id_product FROM `'._DB_PREFIX_.'product` p)
                OR
                (
                    sa.id_product_attribute > 0    
                    AND
                    sa.id_product_attribute NOT IN (SELECT pa.id_product_attribute FROM `'._DB_PREFIX_.'product_attribute` pa WHERE pa.id_product = sa.id_product)
                ) LIMIT 1500';
    $res = Db::getInstance()->ExecuteS($sql);

    $content = '';
    $content_js = '';
    $results = 'OK';
    if (!empty($res) && count($res) > 0)
    {
        $results = 'KO';
        ob_start(); ?>
        <script type="text/javascript">
    
            var tbGhostStockAvailable = dhxlSCExtCheck.tabbar.cells("table_CAT_STK_GHOST_STOCK_AVAILABLE").attachToolbar();
            tbGhostStockAvailable.setIconset('awesome');
            tbGhostStockAvailable.addButton("selectall", 0, "", 'fa fa-bolt yellow', 'fa fa-bolt yellow');
            tbGhostStockAvailable.setItemToolTip('selectall','<?php echo _l('Select all'); ?>');
            tbGhostStockAvailable.addButton("delete", 0, "", 'fa fa-minus-circle red', 'fa fa-minus-circle red');
            tbGhostStockAvailable.setItemToolTip('delete','<?php echo _l('Delete ghost stock'); ?>');
            tbGhostStockAvailable.attachEvent("onClick",
                function(id){
                    if (id=='selectall')
                    {
                        gridGhostStockAvailable.selectAll();
                        getGridStat_GhostStockAvailable();
                    }
                    if (id=='delete')
                    {
                        deleteGhostStockAvailable()
                    }
                });
        
            var gridGhostStockAvailable = dhxlSCExtCheck.tabbar.cells("table_CAT_STK_GHOST_STOCK_AVAILABLE").attachGrid();
            gridGhostStockAvailable.setImagePath("lib/js/imgs/");
            gridGhostStockAvailable.enableSmartRendering(true);
            gridGhostStockAvailable.enableMultiselect(true);
    
            gridGhostStockAvailable.setHeader("ID,<?php echo _l('Name'); ?>,<?php echo _l('Combination ID'); ?>,<?php echo _l('Quantity'); ?>");
            gridGhostStockAvailable.setInitWidths("60,110,100,100");
            gridGhostStockAvailable.setColAlign("left,left,left,left");
            gridGhostStockAvailable.setColTypes("ro,ro,ro,ro");
            gridGhostStockAvailable.setColSorting("int,str,int,int");
            gridGhostStockAvailable.attachHeader("#numeric_filter,#text_filter,#numeric_filter,#numeric_filter");
            gridGhostStockAvailable.init();

            var xml = '<rows>';
            <?php foreach ($res as $stock_available)
        {
            $name = '';
            $sql = 'SELECT name FROM `'._DB_PREFIX_."product_lang` WHERE id_product = '".(int) $stock_available['id_product']."' AND id_lang = '".(int) SCI::getConfigurationValue('PS_LANG_DEFAULT')."'";
            $name_temp = Db::getInstance()->getValue($sql);
            if (!empty($name_temp))
            {
                $name = $name_temp;
            } ?>
            xml = xml+'   <row id="<?php echo $stock_available['id_stock_available']; ?>">';
            xml = xml+'      <cell><![CDATA[<?php echo $stock_available['id_product']; ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php echo str_replace("'", "\'", $name); ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php echo $stock_available['id_product_attribute']; ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php echo str_replace("'", "\'", $stock_available['quantity']); ?>]]></cell>';
            xml = xml+'   </row>';
            <?php
        } ?>
            xml = xml+'</rows>';
            gridGhostStockAvailable.parse(xml);

            sbGhostStockAvailable=dhxlSCExtCheck.tabbar.cells("table_CAT_STK_GHOST_STOCK_AVAILABLE").attachStatusBar();
            function getGridStat_GhostStockAvailable(){
                var filteredRows=gridGhostStockAvailable.getRowsNum();
                var selectedRows=(gridGhostStockAvailable.getSelectedRowId()?gridGhostStockAvailable.getSelectedRowId().split(',').length:0);
                sbGhostStockAvailable.setText('<?php echo count($res).' '._l('Errors'); ?>'+" - <?php echo _l('Filter')._l(':'); ?> "+filteredRows+" - <?php echo _l('Selection')._l(':'); ?> "+selectedRows);
            }
            gridGhostStockAvailable.attachEvent("onFilterEnd", function(elements){
                getGridStat_GhostStockAvailable();
            });
            gridGhostStockAvailable.attachEvent("onSelectStateChanged", function(id){
                getGridStat_GhostStockAvailable();
            });
            getGridStat_GhostStockAvailable();

            function deleteGhostStockAvailable()
            {
                var selectedGhostStockAvailables = gridGhostStockAvailable.getSelectedRowId();
                if(selectedGhostStockAvailables==null || selectedGhostStockAvailables=="")
                    selectedGhostStockAvailables = 0;
                if(selectedGhostStockAvailables!="0")
                {
                    $.post("index.php?ajax=1&act=all_win-fixmyprestashop_actions&check=CAT_STK_GHOST_STOCK_AVAILABLE&id_lang="+SC_ID_LANG, { "action": "delete_stockavailable", "ids": selectedGhostStockAvailables}, function(data){
                        dhxlSCExtCheck.tabbar.tabs("table_CAT_STK_GHOST_STOCK_AVAILABLE").close();

                         dhxlSCExtCheck.gridChecks.selectRowById('CAT_STK_GHOST_STOCK_AVAILABLE');
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
            'title' => _l('Ghost Stock'),
            'contentJs' => $content_js,
    ));
}
elseif (!empty($post_action) && $post_action == 'delete_stockavailable')
{
    $cache_shop_by_product = array();
    function getShopsByProduct($id_product)
    {
        global $cache_shop_by_product;
        $cache = isset($cache_shop_by_product[$id_product])?$cache_shop_by_product[$id_product]:false;
        if (empty($cache))
        {
            $list = array();
            $res = Db::getInstance()->executeS('
                SELECT `id_shop`
                FROM `'._DB_PREFIX_.'product_shop`
                WHERE `id_product` = '.(int) $id_product);
            if (!empty($res) && count($res) > 0)
            {
                foreach ($res as $value)
                {
                    $list[] = $value['id_shop'];
                }
            }

            $cache_shop_by_product[$id_product] = $list;
        }
        else
        {
            $list = $cache;
        }

        return $list;
    }

    function qtySumStockAvailable($id_product)
    {
        $shops = getShopsByProduct($id_product);
        foreach ($shops as $shop_id)
        {
            $query = new DbQuery();
            $query->select('SUM(quantity)');
            $query->from('stock_available');
            $query->where('id_product = '.(int) $id_product);
            $query->where('id_product_attribute != 0 ');
            $query = StockAvailable::addSqlShopRestriction($query, $shop_id);

            $new_qty = (int) Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);

            StockAvailable::setQuantity($id_product, 0, $new_qty, $shop_id);
        }
    }

    $post_ids = Tools::getValue('ids');
    if (!empty($post_ids))
    {
        $products = array();
        $ids = explode(',', $post_ids);
        foreach ($ids as $id)
        {
            $sql = 'SELECT id_product,id_product_attribute FROM `'._DB_PREFIX_."stock_available` WHERE id_stock_available = '".(int) $id."'";
            $res =  Db::getInstance()->getRow($sql);
            if(!empty($res)){
                list($id_product, $id_product_attribute) = array_values($res);
            }

            $sql = 'DELETE FROM '._DB_PREFIX_."stock_available WHERE id_stock_available = '".(int) $id."'";
            $res = dbExecuteForeignKeyOff($sql);

            if (!empty($id_product_attribute))
            {
                $products[$id_product] = (int) $id_product;
            }
        }

        foreach ($products as $id_product)
        {
            qtySumStockAvailable((int) $id_product);
        }
    }
}
