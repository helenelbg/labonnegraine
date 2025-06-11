<?php
$post_action = Tools::getValue('action');
if (!empty($post_action) && $post_action == 'do_check')
{
    $sql = 'SELECT  ps_1.id_product
            FROM '._DB_PREFIX_.'product_shop ps_1
                LEFT JOIN '._DB_PREFIX_."stock_available sa_1 ON ( ps_1.id_product = sa_1.id_product AND sa_1.id_product_attribute = '0' ),
                "._DB_PREFIX_.'product_shop ps_2
                LEFT JOIN '._DB_PREFIX_."stock_available sa_2 ON ( ps_2.id_product = sa_2.id_product AND sa_2.id_product_attribute = '0' )
            WHERE
                ps_1.id_product = ps_2.id_product
                AND ps_1.id_shop != ps_2.id_shop
                AND (
                    ps_1.advanced_stock_management != ps_2.advanced_stock_management
                    ||
                    (
                        (sa_1.depends_on_stock=1 && sa_2.depends_on_stock!=1)
                        ||
                        (sa_2.depends_on_stock=1 && sa_1.depends_on_stock!=1)
                    )
                )
            GROUP BY ps_1.id_product
            ORDER BY ps_1.id_product ASC LIMIT 1500";
    $res = Db::getInstance()->ExecuteS($sql);

    $content = '';
    $content_js = '';
    $results = 'OK';
    if (!empty($res) && count($res) > 0)
    {
        $results = 'KO';
        ob_start();
        $id_lang = (int) Tools::getValue('id_lang', SCI::getConfigurationValue('PS_LANG_DEFAULT')); ?>
        <script type="text/javascript">
    
            var tbDiffAdvancedstocksMode = dhxlSCExtCheck.tabbar.cells("table_CAT_PROD_DIFF_ADVANCEDSTOCKS_MODE").attachToolbar();
            tbDiffAdvancedstocksMode.setIconset('awesome');
            tbDiffAdvancedstocksMode.addButton("goto", 0, "", 'fad fa-search', 'fad fa-search');
            tbDiffAdvancedstocksMode.setItemToolTip('goto','<?php echo _l('See product'); ?>');
            tbDiffAdvancedstocksMode.attachEvent("onClick",
                function(id){
                    if (id=='selectall')
                    {
                        gridDiffAdvancedstocksMode.selectAll();
                        getGridStat_DiffAdvancedstocksMode();
                    }
                    if (id=='goto')
                    {
                        goToDiffAdvancedstocksMode()
                    }
                });
        
            var gridDiffAdvancedstocksMode = dhxlSCExtCheck.tabbar.cells("table_CAT_PROD_DIFF_ADVANCEDSTOCKS_MODE").attachGrid();
            gridDiffAdvancedstocksMode.setImagePath("lib/js/imgs/");
            gridDiffAdvancedstocksMode.enableSmartRendering(true);
            gridDiffAdvancedstocksMode.enableMultiselect(false);
    
            gridDiffAdvancedstocksMode.setHeader("ID,<?php echo _l('Name'); ?>,<?php echo _l('Shop'); ?>,<?php echo _l('Advanced Stock Mgmt.'); ?>");
            gridDiffAdvancedstocksMode.attachHeader("#select_filter,#select_filter,#select_filter,#select_filter");
            gridDiffAdvancedstocksMode.setInitWidths("100,150,100,200");
            gridDiffAdvancedstocksMode.setColAlign("left,left,left,left");
            gridDiffAdvancedstocksMode.setColTypes("ro,ro,ro,ro");
            gridDiffAdvancedstocksMode.setColSorting("int,str,str,str");
            gridDiffAdvancedstocksMode.attachHeader("#numeric_filter,#text_filter,#text_filter,#select_filter");
            gridDiffAdvancedstocksMode.init();
    
            var xml = '<rows>';
            <?php
            $types = array(1 => _l('Disabled'), 2 => _l('Enabled'), 3 => _l('Enabled + Manual Mgmt'));
        foreach ($res as $product)
        {
            $product_shops = Db::getInstance()->executeS('
                SELECT ps.`id_shop`, s.name
                FROM `'._DB_PREFIX_.'product_shop` ps
                    INNER JOIN '._DB_PREFIX_.'shop s ON ( ps.id_shop = s.id_shop )
                WHERE ps.`id_product` = '.(int) $product['id_product']);

            foreach ($product_shops as $product_shop)
            {
                $type_advanced_stock_management = 1;
                $product_inst = new Product($product['id_product'], false, $id_lang, $product_shop['id_shop']);

                if ($product_inst->advanced_stock_management == 1)
                {
                    $type_advanced_stock_management = 2;
                    if (!StockAvailable::dependsOnStock((int) $product['id_product'], (int) $product_shop['id_shop']))
                    {
                        $type_advanced_stock_management = 3;
                    }
                }
                $id_category = 0;
                $cats = Db::getInstance()->executeS('
                        SELECT cp.`id_category`
                        FROM `'._DB_PREFIX_.'category_product` cp
                            INNER JOIN `'._DB_PREFIX_.'category_shop` cs ON (cs.id_category = cp.id_category AND cs.id_shop="'.(int) $product_shop['id_shop'].'")
                        WHERE cp.`id_product` = '.(int) $product['id_product']
                    );

                if (!empty($cats[0]['id_category']))
                {
                    $id_category = $cats[0]['id_category'];
                } ?>
                xml = xml+'   <row id="<?php echo $product_shop['id_shop'].'_'.$id_category.'_'.$product['id_product']; ?>">';
                xml = xml+'      <cell><![CDATA[<?php echo $product['id_product']; ?>]]></cell>';
                xml = xml+'      <cell><![CDATA[<?php echo str_replace("'", "\'", $product_inst->name); ?>]]></cell>';
                xml = xml+'      <cell><![CDATA[<?php echo str_replace("'", "\'", $product_shop['name']).' (#'.$product_shop['id_shop'].')'; ?>]]></cell>';
                xml = xml+'      <cell><![CDATA[<?php echo str_replace("'", "\'", $types[$type_advanced_stock_management]); ?>]]></cell>';
                xml = xml+'   </row>';
                <?php
            }
        } ?>
            xml = xml+'</rows>';
            gridDiffAdvancedstocksMode.parse(xml);

            sbDiffAdvancedstocksMode=dhxlSCExtCheck.tabbar.cells("table_CAT_PROD_DIFF_ADVANCEDSTOCKS_MODE").attachStatusBar();
            function getGridStat_DiffAdvancedstocksMode(){
                var filteredRows=gridDiffAdvancedstocksMode.getRowsNum();
                var selectedRows=(gridDiffAdvancedstocksMode.getSelectedRowId()?gridDiffAdvancedstocksMode.getSelectedRowId().split(',').length:0);
                sbDiffAdvancedstocksMode.setText('<?php echo count($res).' '._l('Errors'); ?>'+" - <?php echo _l('Filter')._l(':'); ?> "+filteredRows+" - <?php echo _l('Selection')._l(':'); ?> "+selectedRows);
            }
            gridDiffAdvancedstocksMode.attachEvent("onFilterEnd", function(elements){
                getGridStat_DiffAdvancedstocksMode();
            });
            gridDiffAdvancedstocksMode.attachEvent("onSelectStateChanged", function(id){
                getGridStat_DiffAdvancedstocksMode();
            });
            getGridStat_DiffAdvancedstocksMode();

            function goToDiffAdvancedstocksMode()
            {
                var selectedDiffAdvancedstocksModes = gridDiffAdvancedstocksMode.getSelectedRowId();
                if(selectedDiffAdvancedstocksModes==null || selectedDiffAdvancedstocksModes=="")
                    selectedDiffAdvancedstocksModes = 0;
                if(selectedDiffAdvancedstocksModes!="")
                {
                    var shop_id = 0;
                    var category_id = 0;
                    var product_id = 0;
                    var tmp = selectedDiffAdvancedstocksModes.split("_");
                    if(tmp[0]!=undefined && tmp[0]!=null && tmp[0]!="")
                        shop_id = tmp[0];
                    if(tmp[1]!=undefined && tmp[1]!=null && tmp[1]!="")
                        category_id = tmp[1];
                    if(tmp[2]!=undefined && tmp[2]!=null && tmp[2]!="")
                        product_id = tmp[2];

                    if(shop_id!=0 && category_id!=0 && product_id!=0)
                    {
                        var action_after = "cat_tree.openItem("+category_id+");cat_tree.selectItem("+category_id+",false);catselection="+category_id+";"+
                        "displayProducts('cat_grid.selectRowById("+product_id+",false,true,true);doOnRowSelected("+product_id+");lastProductSelID="+product_id+";');";

                        cat_shoptree.selectItem("all",false);
                        cat_shoptree.openItem(shop_id);
                        cat_shoptree.selectItem(shop_id,false);
                        onClickShopTree(shop_id, null,action_after);
                    }
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
            'title' => _l('Diff. ASM.'),
            'contentJs' => $content_js,
    ));
}
