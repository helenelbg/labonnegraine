<?php
$post_action = Tools::getValue('action');
if (!empty($post_action) && $post_action == 'do_check')
{
    $sql = 'SELECT pa.id_product_attribute, pa.id_product
          FROM '._DB_PREFIX_.'product_attribute pa
           WHERE pa.id_product_attribute NOT IN (SELECT pas.id_product_attribute FROM '._DB_PREFIX_.'product_attribute_shop pas)
           ORDER BY id_product_attribute ASC
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

            var tbGhostCombi = dhxlSCExtCheck.tabbar.cells("table_CAT_COMBI_GHOST_MS").attachToolbar();
            tbGhostCombi.setIconset('awesome');
            tbGhostCombi.addButton("selectall", 0, "", 'fa fa-bolt yellow', 'fa fa-bolt yellow');
            tbGhostCombi.setItemToolTip('selectall','<?php echo _l('Select all'); ?>');
            tbGhostCombi.addButton("delete", 0, "", 'fa fa-minus-circle red', 'fa fa-minus-circle red');
            tbGhostCombi.setItemToolTip('delete','<?php echo _l('Delete incomplete combinations'); ?>');
            tbGhostCombi.attachEvent("onClick",
                function(id){
                    if (id=='selectall')
                    {
                        gridGhostCombi.selectAll();
                        getGridStat_GhostCombi();
                    }
                    if (id=='delete')
                    {
                        deleteGhostCombi();
                    }
                });

            var gridGhostCombi = dhxlSCExtCheck.tabbar.cells("table_CAT_COMBI_GHOST_MS").attachGrid();
            gridGhostCombi.setImagePath("lib/js/imgs/");
            gridGhostCombi.enableSmartRendering(true);
            gridGhostCombi.enableMultiselect(true);

            gridGhostCombi.setHeader("ID <?php echo _l('product'); ?>, ID <?php echo _l('combination'); ?>");
            gridGhostCombi.setInitWidths("100, 100");
            gridGhostCombi.setColAlign("left,left");
            gridGhostCombi.setColTypes("ro,ro");
            gridGhostCombi.setColSorting("int,int");
            gridGhostCombi.attachHeader("#numeric_filter,#numeric_filter");
            gridGhostCombi.init();

            var xml = '<rows>';
            <?php foreach ($res as $attribute) { ?>
            xml = xml+'   <row id="<?php echo $attribute['id_product_attribute']; ?>">';
            xml = xml+'      <cell><![CDATA[<?php echo $attribute['id_product']; ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php echo $attribute['id_product_attribute']; ?>]]></cell>';
            xml = xml+'   </row>';
            <?php } ?>
            xml = xml+'</rows>';
            gridGhostCombi.parse(xml);

            sbGhostCombi=dhxlSCExtCheck.tabbar.cells("table_CAT_COMBI_GHOST_MS").attachStatusBar();
            function getGridStat_GhostCombi(){
                var filteredRows=gridGhostCombi.getRowsNum();
                var selectedRows=(gridGhostCombi.getSelectedRowId()?gridGhostCombi.getSelectedRowId().split(',').length:0);
                sbGhostCombi.setText('<?php echo count($res).' '._l('Errors'); ?>'+" - <?php echo _l('Filter')._l(':'); ?> "+filteredRows+" - <?php echo _l('Selection')._l(':'); ?> "+selectedRows);
            }
            gridGhostCombi.attachEvent("onFilterEnd", function(elements){
                getGridStat_GhostCombi();
            });
            gridGhostCombi.attachEvent("onSelectStateChanged", function(id){
                getGridStat_GhostCombi();
            });
            getGridStat_GhostCombi();

            function deleteGhostCombi()
            {
                var selectedGhostCombis = gridGhostCombi.getSelectedRowId();
                if(selectedGhostCombis==null || selectedGhostCombis=="")
                    selectedGhostCombis = 0;
                if(selectedGhostCombis!="0")
                {
                    $.post("index.php?ajax=1&act=all_win-fixmyprestashop_actions&check=CAT_COMBI_GHOST_MS&id_lang="+SC_ID_LANG, { "action": "delete_combis", "ids": selectedGhostCombis}, function(data){
                        dhxlSCExtCheck.tabbar.tabs("table_CAT_COMBI_GHOST_MS").close();

                         dhxlSCExtCheck.gridChecks.selectRowById('CAT_COMBI_GHOST_MS');
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
            'title' => _l('Ghost combi.'),
            'contentJs' => $content_js,
    ));
}
elseif (!empty($post_action) && $post_action == 'delete_combis')
{
    function checkDefaultAttributes($id_product)
    {
        $row = Db::getInstance()->getRow('
        SELECT id_product, id_product_attribute
        FROM `'._DB_PREFIX_.'product_attribute`
        WHERE `default_on` = 1 AND `id_product` = '.(int) ($id_product));
        if ($row)
        {
            return (int) ($row['id_product_attribute']);
        }

        $mini = Db::getInstance()->getRow('
        SELECT MIN(pa.id_product_attribute) as `id_attr`
        FROM `'._DB_PREFIX_.'product_attribute` pa
        WHERE `id_product` = '.(int) ($id_product));
        if (!$mini)
        {
            return 0;
        }

        if (!dbExecuteForeignKeyOff('
            UPDATE `'._DB_PREFIX_.'product_attribute`
            SET `default_on` = 1
            WHERE `id_product_attribute` = '.(int) ($mini['id_attr'])))
        {
            return 0;
        }

        return (int) ($mini['id_attr']);
    }

    $post_ids = explode(',', Tools::getValue('ids'));
    if (!empty($post_ids))
    {
        if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
        {
            $shops = Shop::getShops(false, null, true);

            foreach ($post_ids as $id_product_attribute)
            {
                if (is_numeric($id_product_attribute) && $id_product_attribute)
                {
                    $c = new Combination($id_product_attribute);
                    $id_product = (int) $c->id_product;
                    $c->id_shop_list = Shop::getShops(false, null, true);
                    $c->delete();
                    foreach ($shops as $shop)
                    {
                        StockAvailable::removeProductFromStockAvailable((int) $id_product, (int) $id_product_attribute, $shop);
                    }

                    $p = new Product($id_product);
                    $p->checkDefaultAttributes();
                    if (!$p->hasAttributes())
                    {
                        $sql = 'UPDATE '._DB_PREFIX_."product SET cache_default_attribute='0' WHERE id_product=" .(int) $id_product;
                        dbExecuteForeignKeyOff($sql);
                        if ($post_for_PS == 1)
                        {
                            $shopID = Context::getContext()->shop->id;
                        }
                        else
                        {
                            $shopID = SCI::getSelectedShopActionList();
                        }
                        $sql = 'UPDATE '._DB_PREFIX_."product_shop SET cache_default_attribute='0' WHERE id_product='".(int)$id_product."' AND id_shop IN (".pInSQL($shopID).') ';
                        dbExecuteForeignKeyOff($sql);
                    }
                    else
                    {
                        Product::updateDefaultAttribute((int) $id_product);
                    }

                    if ($post_for_PS == 1)
                    {
                        $cache_shop_by_product = array();
                        $cache = array();
                        if (array_key_exists($id_product, $cache_shop_by_product))
                        {
                            $cache = $cache_shop_by_product[$id_product];
                        }
                        if (empty($cache))
                        {
                            $shop_list = array();
                            $res = Db::getInstance()->executeS('SELECT `id_shop`
                                                                FROM `'._DB_PREFIX_.'product_shop`
                                                                WHERE `id_product` = '.(int) $id_product);
                            if (!empty($res) && count($res) > 0)
                            {
                                foreach ($res as $value)
                                {
                                    $shop_list[] = $value['id_shop'];
                                }
                            }

                            $cache_shop_by_product[$id_product] = $shop_list;
                        }
                        else
                        {
                            $shop_list = $cache;
                        }

                        foreach ($shop_list as $shop_id)
                        {
                            $query = new DbQuery();
                            $query->select('SUM(quantity)');
                            $query->from('stock_available');
                            $query->where('id_product = '.(int) $id_product);
                            $query->where('id_product_attribute != 0 ');
                            $query = StockAvailable::addSqlShopRestriction($query, $shop_id);

                            $new_qty = (int) Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);

                            if (!Validate::isUnsignedId($id_product))
                            {
                                return false;
                            }

                            $context = Context::getContext();

                            // if there is no $id_shop, gets the context one
                            if ($id_shop === null && Shop::getContext() != Shop::CONTEXT_GROUP)
                            {
                                $id_shop = (int) $context->shop->id;
                            }

                            $depends_on_stock = StockAvailable::dependsOnStock($id_product, (int) $id_shop);

                            //Try to set available quantity if product does not depend on physical stock
                            if (!$depends_on_stock)
                            {
                                $id_stock_available = (int) StockAvailable::getStockAvailableIdByProductId($id_product,
                                    $id_product_attribute, $id_shop);
                                if ($id_stock_available)
                                {
                                    $stock_available = new StockAvailable($id_stock_available);
                                    $stock_available->quantity = (int) $new_qty;
                                    $stock_available->update();
                                }
                                else
                                {
                                    $out_of_stock = StockAvailable::outOfStock($id_product, $id_shop);
                                    $stock_available = new StockAvailable();
                                    $stock_available->out_of_stock = (int) $out_of_stock;
                                    $stock_available->id_product = (int) $id_product;
                                    $stock_available->id_product_attribute = (int) $id_product_attribute;
                                    $stock_available->quantity = (int) $new_qty;

                                    if ($id_shop === null)
                                    {
                                        $shop_group = Shop::getContextShopGroup();
                                    }
                                    else
                                    {
                                        $shop_group = new ShopGroup((int) Shop::getGroupFromShop((int) $id_shop));
                                    }

                                    // if quantities are shared between shops of the group
                                    if ($shop_group->share_stock)
                                    {
                                        $stock_available->id_shop = 0;
                                        $stock_available->id_shop_group = (int) $shop_group->id;
                                    }
                                    else
                                    {
                                        $stock_available->id_shop = (int) $id_shop;
                                        $stock_available->id_shop_group = 0;
                                    }
                                    $stock_available->add();
                                }

                                Hook::exec('actionUpdateQuantity',
                                    array(
                                        'id_product' => $id_product,
                                        'id_product_attribute' => $id_product_attribute,
                                        'quantity' => $stock_available->quantity,
                                    )
                                );
                            }
                        }
                    }
                    else
                    {
                        SCI::qtySumStockAvailable($id_product);
                    }
                }
            }
        }
        else
        {
            foreach ($post_ids as $id_product_attribute)
            {
                if (is_numeric($id_product_attribute))
                {
                    $c = new Combination($id_product_attribute);
                    $id_product = (int) $c->id_product;

                    $sql = 'DELETE FROM '._DB_PREFIX_."product_attribute WHERE id_product_attribute=" .(int) $id_product_attribute . "";
                    dbExecuteForeignKeyOff($sql);
                    $sql = 'DELETE FROM '._DB_PREFIX_."product_attribute_combination WHERE id_product_attribute=" .(int) $id_product_attribute . "";
                    dbExecuteForeignKeyOff($sql);
                    $sql = 'DELETE FROM `'._DB_PREFIX_.'cart_product` WHERE `id_product_attribute` = '.(int) $id_product_attribute;
                    dbExecuteForeignKeyOff($sql);
                    $sql = 'DELETE FROM '._DB_PREFIX_."product_attribute_image WHERE id_product_attribute=" .(int) $id_product_attribute . "";
                    dbExecuteForeignKeyOff($sql);

                    if (_s('APP_COMPAT_HOOK') && !_s('APP_COMPAT_EBAY'))
                    {
                        SCI::hookExec('deleteProductAttribute', array('id_product_attribute' => (int) $id_product_attribute, 'id_product' => (int) $id_product, 'deleteAllAttributes' => false));
                    }
                    elseif (_s('APP_COMPAT_EBAY'))
                    {
                        Configuration::updateValue('EBAY_SYNC_LAST_PRODUCT', min(Configuration::get('EBAY_SYNC_LAST_PRODUCT'), (int) $id_product));
                    }

                    $default_id = checkDefaultAttributes((int) $id_product);

                    dbExecuteForeignKeyOff('
                        UPDATE `'._DB_PREFIX_.'product`
                        SET `cache_default_attribute` ='.(int) $default_id.'
                        WHERE `id_product` = '.(int) $id_product);

                    dbExecuteForeignKeyOff('
                        UPDATE `'._DB_PREFIX_.'product`
                        SET `quantity` =
                            (
                            SELECT SUM(`quantity`)
                            FROM `'._DB_PREFIX_.'product_attribute`
                            WHERE `id_product` = '.(int) $id_product.'
                            )
                        WHERE `id_product` = '.(int) $id_product);
                }
            }
        }
    }
}
