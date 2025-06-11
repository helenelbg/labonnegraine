<?php
$post_action = Tools::getValue('action');
if (!empty($post_action) && $post_action == 'do_check')
{
    $sql = 'SELECT pa.id_product, pa.id_product_attribute, pp.id_shop_default AS id_product_shop_default
            FROM '._DB_PREFIX_.'product_attribute pa
            INNER JOIN '._DB_PREFIX_.'product pp ON (pp.id_product = pa.id_product)
            WHERE pa.id_product_attribute NOT IN (SELECT DISTINCT id_product_attribute FROM '._DB_PREFIX_.'product_attribute_shop WHERE id_shop = pp.id_shop_default)';
    $res = Db::getInstance()->ExecuteS($sql);

    $sql = 'SELECT id_shop, name
            FROM '._DB_PREFIX_.'shop
            WHERE active = 1';
    $shops = Db::getInstance()->ExecuteS($sql);

    $content = '';
    $content_js = '';
    $results = 'OK';
    if (!empty($res) && count($res) > 0)
    {
        $idProductShopDefaults = array_map(function ($e)
        {
            return isset($e['id_product_shop_default'])?$e['id_product_shop_default']:false;
        }, $res);
        $results = 'KO';
        ob_start(); ?>
        <script type="text/javascript">

            var colsProductAssociationShop = dhxlSCExtCheck.tabbar.cells("table_CAT_ATTR_ASSOCIATION_SHOP").attachLayout("2U");

            // PRODUCTS_ATTRIBUTES
            var tbLeftProductAttrAssociationShop = colsProductAssociationShop.cells('a').attachToolbar();
            tbLeftProductAttrAssociationShop.setIconset('awesome');
            colsProductAssociationShop.cells('a').setText("<?php echo _l('Products'); ?>");
            tbLeftProductAttrAssociationShop.addButton("selectall", 0, "", 'fa fa-bolt yellow', 'fa fa-bolt yellow');
            tbLeftProductAttrAssociationShop.setItemToolTip('selectall','<?php echo _l('Select all'); ?>');
            tbLeftProductAttrAssociationShop.addButton("replaceIdDefaultShop", 0, "", 'fad fa-tools green', 'fad fa-tools green');
            tbLeftProductAttrAssociationShop.setItemToolTip('replaceIdDefaultShop','<?php echo _l('Recreate missing row for attribute_shop', 1); ?>');
            tbLeftProductAttrAssociationShop.attachEvent("onClick",
                function(id){
                    if (id=='selectall')
                    {
                        gridLeftProductAttrAssociationShop.selectAll();
                        getGridStat_LeftProductAttrAssociationShop();
                    }
                    if (id=='replaceIdDefaultShop')
                    {
                        replaceIdDefaultShop();
                    }
                    if (id=='replaceIdDefaultShopWithSelected')
                    {
                        replaceIdDefaultShopWithSelected();
                    }
                });

            var gridLeftProductAttrAssociationShop = colsProductAssociationShop.cells('a').attachGrid();
            gridLeftProductAttrAssociationShop.setImagePath("lib/js/imgs/");
            gridLeftProductAttrAssociationShop.enableSmartRendering(true);

            gridLeftProductAttrAssociationShop.setHeader("<?php echo _l('ID product'); ?>,<?php echo _l('Id product attribute'); ?>");
            gridLeftProductAttrAssociationShop.setColAlign("left,left");
            gridLeftProductAttrAssociationShop.setColTypes("ro,ro");
            gridLeftProductAttrAssociationShop.setInitWidths("100,150");
            gridLeftProductAttrAssociationShop.setColSorting("int,int");
            gridLeftProductAttrAssociationShop.attachHeader("#numeric_filter,#numeric_filter");
            gridLeftProductAttrAssociationShop.enableMultiselect(true);
            gridLeftProductAttrAssociationShop.init();

            var xml = '<rows>';
            <?php foreach ($res as $id => $row)
        {
            ?>
            xml = xml+'<row id="<?php echo $row['id_product'].'_'.$row['id_product_attribute'].'_'.$row['id_product_shop_default']; ?>">';
            xml = xml+'    <userdata name="ids"><?php echo $row['id_product_attribute'].'_'.$row['id_product_shop_default']; ?></userdata>';
            xml = xml+'    <cell><![CDATA[<?php echo $row['id_product']; ?>]]></cell>';
            xml = xml+'    <cell><![CDATA[<?php echo $row['id_product_attribute']; ?>]]></cell>';
            xml = xml+'</row>';
            <?php
        } ?>
            xml = xml+'</rows>';
            gridLeftProductAttrAssociationShop.parse(xml);
            
            sbLeftProductAttrAssociationShop=dhxlSCExtCheck.tabbar.cells("table_CAT_ATTR_ASSOCIATION_SHOP").attachStatusBar();
            function getGridStat_LeftProductAttrAssociationShop(){
                var filteredRows=gridLeftProductAttrAssociationShop.getRowsNum();
                var selectedRows=(gridLeftProductAttrAssociationShop.getSelectedRowId()?gridLeftProductAttrAssociationShop.getSelectedRowId().split(',').length:0);
                sbLeftProductAttrAssociationShop.setText('<?php echo count($res).' '._l('Errors'); ?>'+" - <?php echo _l('Filter')._l(':'); ?> "+filteredRows+" - <?php echo _l('Selection')._l(':'); ?> "+selectedRows);
            }
            gridLeftProductAttrAssociationShop.attachEvent("onFilterEnd", function(elements){
                getGridStat_LeftProductAttrAssociationShop();
            });
            gridLeftProductAttrAssociationShop.attachEvent("onSelectStateChanged", function(id){
                getGridStat_LeftProductAttrAssociationShop();
            });
            getGridStat_LeftProductAttrAssociationShop();

            gridLeftProductAttrAssociationShop.attachEvent("onRowSelect", function(id,ind){
                var ids = gridLeftProductAttrAssociationShop.getUserData(id, "ids");
            });

            // SHOP
            var tbRightProductAssociationShop = colsProductAssociationShop.cells('b').attachToolbar();
            tbRightProductAssociationShop.setIconset('awesome');
            colsProductAssociationShop.cells('b').setText("<?php echo _l('Shop list'); ?>");
            tbRightProductAssociationShop.addButton("replaceIdDefaultShopWithSelected", 0, "", 'fad fa-tools green', 'fad fa-tools green');
            tbRightProductAssociationShop.setItemToolTip('replaceIdDefaultShopWithSelected','<?php echo _l('Update default shop with your choice', 1); ?>');
            tbRightProductAssociationShop.attachEvent("onClick",
                function(id){
                    if (id=='replaceIdDefaultShopWithSelected')
                    {
                        replaceIdDefaultShopWithSelected();
                    }
                });

            var gridRightProductAssociationShop = colsProductAssociationShop.cells('b').attachGrid();
            gridRightProductAssociationShop.setImagePath("lib/js/imgs/");
            gridRightProductAssociationShop.enableSmartRendering(true);
            gridRightProductAssociationShop.enableMultiselect(true);

            gridRightProductAssociationShop.setHeader("<?php echo _l('ID'); ?>,<?php echo _l('Name'); ?>");
            gridRightProductAssociationShop.setColAlign("left,left");
            gridRightProductAssociationShop.setColTypes("ro,ro");
            gridRightProductAssociationShop.setInitWidths("40,");
            gridRightProductAssociationShop.enableMultiselect(false);
            gridRightProductAssociationShop.init();

            var xml = '<rows>';
            <?php foreach ($shops as $id => $row)
        {
            $style = (!in_array($row['id_shop'], $idProductShopDefaults)) ? '' : 'background:red;color:white;'; ?>
            xml = xml+'<row id="<?php echo $row['id_shop']; ?>">';
            xml = xml+'    <userdata name="ids"><?php echo $row['id_shop']; ?></userdata>';
            xml = xml+'    <cell style="<?php echo $style; ?>"><![CDATA[<?php echo $row['id_shop']; ?>]]></cell>';
            xml = xml+'    <cell style="<?php echo $style; ?>"><![CDATA[<?php echo str_replace("'", "\'", $row['name']); ?>]]></cell>';
            xml = xml+'</row>';
            <?php
        } ?>
            xml = xml+'</rows>';
            gridRightProductAssociationShop.parse(xml);

            function replaceIdDefaultShop()
            {
                var products_id = gridLeftProductAttrAssociationShop.getSelectedRowId();
                if(products_id==null || products_id=="")
                    products_id = 0;
                if(products_id!="0")
                {
                    $.post("index.php?ajax=1&act=all_win-fixmyprestashop_actions&check=CAT_ATTR_ASSOCIATION_SHOP&id_lang="+SC_ID_LANG, { "action": "replace_id_default_shop", "ids": products_id}, function(data){
                        dhxlSCExtCheck.tabbar.tabs("table_CAT_ATTR_ASSOCIATION_SHOP").close();

                        dhxlSCExtCheck.gridChecks.selectRowById('CAT_ATTR_ASSOCIATION_SHOP');
                        doCheck(false);
                    });
                }
            }

            function replaceIdDefaultShopWithSelected()
            {
                var products_id = gridLeftProductAttrAssociationShop.getSelectedRowId();
                var selectedShop = gridRightProductAssociationShop.getSelectedRowId();
                if(selectedShop==null || selectedShop=="")
                    selectedShop = 0;
                if(products_id==null || products_id=="")
                    products_id = 0;
                if(selectedShop!="0" && products_id!="0")
                {
                    var all_ids = products_id;
                    $.post("index.php?ajax=1&act=all_win-fixmyprestashop_actions&check=CAT_ATTR_ASSOCIATION_SHOP&id_lang="+SC_ID_LANG, { "action": "replace_id_default_shop_selected", "ids": all_ids, "selectedShop" : selectedShop }, function(data){
                        dhxlSCExtCheck.tabbar.tabs("table_CAT_ATTR_ASSOCIATION_SHOP").close();

                        dhxlSCExtCheck.gridChecks.selectRowById('CAT_ATTR_ASSOCIATION_SHOP');
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
        'title' => _l('Combi. without dft shop'),
        'contentJs' => $content_js,
    ));
}
elseif (!empty($post_action) && $post_action == 'replace_id_default_shop')
{
    $post_ids = Tools::getValue('ids');
    if (!empty($post_ids))
    {
        $ids = explode(',', $post_ids);

        foreach ($ids as $id)
        {
            list($id_product, $id_product_attribute, $id_shop_default) = explode('_', $id);

            $id_shop_base = 0;
            if (Configuration::get('PS_SHOP_DEFAULT'))
            {
                $sql = 'SELECT id_shop
                            FROM '._DB_PREFIX_.'product_attribute_shop 
                            WHERE id_product_attribute = '.(int) $id_product_attribute.'
                            AND id_shop = '.(int) Configuration::get('PS_SHOP_DEFAULT');
                if ($res = Db::getInstance()->getRow($sql))
                {
                    $id_shop_base = $res['id_shop'];
                }
            }
            if (empty($id_shop_base))
            {
                $sql = 'SELECT id_shop
                        FROM '._DB_PREFIX_.'product_attribute_shop 
                        WHERE id_product_attribute = '.(int) $id_product_attribute;
                if ($res = Db::getInstance()->getRow($sql))
                {
                    $id_shop_base = $res['id_shop'];
                }
            }
            if (!empty($id_shop_base))
            {
                if (version_compare(_PS_VERSION_, '1.6.0.0', '>='))
                {
                    $combination = new CombinationCore((int) $id_product_attribute, false, $id_shop_base);
                    if (empty($combination->id_product))
                    {
                        $combination->id_product = $id_product;
                    }
                    if (empty($combination->minimal_quantity))
                    {
                        $combination->minimal_quantity = 1;
                    }
                    $combination->id_shop_list = array($id_shop_default);
                    $res = $combination->save();
                }
                else
                {
                    $sql = 'SELECT *
                        FROM '._DB_PREFIX_.'product_attribute_shop
                        WHERE id_product_attribute = '.$id_product_attribute.'
                        AND id_shop = '.(int) $id_shop_base;
                    if ($res = Db::getInstance()->getRow($sql))
                    {
                        $insertSQL = 'INSERT INTO '._DB_PREFIX_.'product_attribute_shop 
                            (id_product_attribute, 
                            id_shop, 
                            wholesale_price, 
                            price, 
                            ecotax, 
                            weight, 
                            unit_price_impact, 
                            default_on, 
                            minimal_quantity, 
                            available_date)
                            VALUES ('.(int) $res['id_product_attribute'].',
                            '.(int) $id_shop_default.',
                            '.(float) $res['wholesale_price'].',
                            '.(float) $res['price'].',
                            '.(float) $res['ecotax'].',
                            '.(float) $res['weight'].',
                            '.(float) $res['unit_price_impact'].',
                            '.pSQL($res['default_on']).',
                            '.(int) $res['minimal_quantity'].',
                            '.pSQL($res['available_date']).')';
                        $res = dbExecuteForeignKeyOff($insertSQL);
                    }
                }
            }
        }
    }
}
elseif (!empty($post_action) && $post_action == 'replace_id_default_shop_selected')
{
    $post_ids = Tools::getValue('ids');
    $selectedShop = (int) Tools::getValue('selectedShop');
    if (!empty($post_ids))
    {
        $ids = explode(',', $post_ids);

        foreach ($ids as $id)
        {
            list($id_product, $id_product_attribute, $id_shop_default) = explode('_', $id);

            $id_shop_base = 0;
            if (Configuration::get('PS_SHOP_DEFAULT'))
            {
                $sql = 'SELECT id_shop
                            FROM '._DB_PREFIX_.'product_attribute_shop 
                            WHERE id_product_attribute = '.(int) $id_product_attribute.'
                            AND id_shop = '.(int) Configuration::get('PS_SHOP_DEFAULT');
                if ($res = Db::getInstance()->getRow($sql))
                {
                    $id_shop_base = $res['id_shop'];
                }
            }
            if (empty($id_shop_base))
            {
                $id_shop_base = $selectedShop;
            }
            if (!empty($id_shop_base))
            {
                if (version_compare(_PS_VERSION_, '1.6.0.0', '>='))
                {
                    $combination = new CombinationCore((int) $id_product_attribute, false, $id_shop_base);
                    if (empty($combination->id_product))
                    {
                        $combination->id_product = $id_product;
                    }
                    if (empty($combination->minimal_quantity))
                    {
                        $combination->minimal_quantity = 1;
                    }
                    $combination->id_shop_list = array($id_shop_default);
                    $res = $combination->save();
                }
                else
                {
                    $sql = 'SELECT *
                        FROM '._DB_PREFIX_.'product_attribute_shop
                        WHERE id_product_attribute = '.$id_product_attribute.'
                        AND id_shop = '.(int) $id_shop_base;
                    if ($res = Db::getInstance()->getRow($sql))
                    {
                        $insertSQL = 'INSERT INTO '._DB_PREFIX_.'product_attribute_shop 
                            (id_product_attribute, 
                            id_shop, 
                            wholesale_price, 
                            price, 
                            ecotax, 
                            weight, 
                            unit_price_impact, 
                            default_on, 
                            minimal_quantity, 
                            available_date)
                            VALUES ('.(int) $res['id_product_attribute'].',
                            '.(int) $id_shop_default.',
                            '.(float) $res['wholesale_price'].',
                            '.(float) $res['price'].',
                            '.(float) $res['ecotax'].',
                            '.(float) $res['weight'].',
                            '.(float) $res['unit_price_impact'].',
                            '.pSQL($res['default_on']).',
                            '.(int) $res['minimal_quantity'].',
                            '.pSQL($res['available_date']).')';
                        $res = dbExecuteForeignKeyOff($insertSQL);
                    }
                }
            }
        }
    }
}
