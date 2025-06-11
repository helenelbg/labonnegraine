<?php
$post_action = Tools::getValue('action');
if (!empty($post_action) && $post_action == 'do_check')
{
    $sql = 'SELECT pp.id_product, pp.id_shop_default
            FROM '._DB_PREFIX_.'product pp
            WHERE pp.id_product NOT IN (SELECT DISTINCT id_product FROM '._DB_PREFIX_.'product_shop WHERE id_shop = pp.id_shop_default)';
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
        $results = 'KO';
        ob_start(); ?>
        <script type="text/javascript">

            var colsProductAssociationShop = dhxlSCExtCheck.tabbar.cells("table_CAT_PRODUCT_ASSOCIATION_SHOP").attachLayout("2U");

            // PRODUCTS
            var tbLeftProductAssociationShop = colsProductAssociationShop.cells('a').attachToolbar();
            tbLeftProductAssociationShop.setIconset('awesome');
            colsProductAssociationShop.cells('a').setText("<?php echo _l('Products'); ?>");
            tbLeftProductAssociationShop.addButton("selectAll", 0, "", 'fa fa-bolt yellow', 'fa fa-bolt yellow');
            tbLeftProductAssociationShop.setItemToolTip('selectAll','<?php echo _l('Select all'); ?>');
            tbLeftProductAssociationShop.addButton("replaceIdDefaultShop", 0, "", 'fad fa-tools green', 'fad fa-tools green');
            tbLeftProductAssociationShop.setItemToolTip('replaceIdDefaultShop','<?php echo _l('Recreate default shop association', 1); ?>');
            tbLeftProductAssociationShop.attachEvent("onClick",
                function(id){
                    if (id=='selectAll')
                    {
                        gridLeftProductAssociationShop.selectAll();
                        getGridStat_LeftProductAssociationShop();
                    }
                    if (id=='replaceIdDefaultShop')
                    {
                        replaceIdDefaultShop();
                    }
                });

            var gridLeftProductAssociationShop = colsProductAssociationShop.cells('a').attachGrid();
            gridLeftProductAssociationShop.setImagePath("lib/js/imgs/");
            gridLeftProductAssociationShop.enableSmartRendering(true);
            gridLeftProductAssociationShop.enableMultiselect(false);

            gridLeftProductAssociationShop.setHeader("<?php echo _l('ID'); ?>,<?php echo _l('Ref'); ?>,<?php echo _l('Name'); ?>");
            gridLeftProductAssociationShop.setColAlign("left,left,left");
            gridLeftProductAssociationShop.setColTypes("ro,ro,ro");
            gridLeftProductAssociationShop.setInitWidths("40,80,");
            gridLeftProductAssociationShop.setColSorting("int,str,str");
            gridLeftProductAssociationShop.attachHeader("#numeric_filter,#text_filter,#text_filter");
            gridLeftProductAssociationShop.init();

            var xml = '<rows>';
            <?php foreach ($res as $id => $row)
        {
            $product = new Product($row['id_product'], false, $cookie->id_lang); ?>
            xml = xml+'<row id="<?php echo $row['id_product'].'_'.$row['id_shop_default']; ?>">';
            xml = xml+'    <userdata name="ids"><?php echo $row['id_product'].'_'.$row['id_shop_default']; ?></userdata>';
            xml = xml+'    <userdata name="id_shop"><?php echo $row['id_shop_default']; ?></userdata>';
            xml = xml+'    <cell><![CDATA[<?php echo $row['id_product']; ?>]]></cell>';
            xml = xml+'    <cell><![CDATA[<?php echo str_replace("'", "\'", $product->reference); ?>]]></cell>';
            xml = xml+'    <cell><![CDATA[<?php echo str_replace("'", "\'", $product->name); ?>]]></cell>';
            xml = xml+'</row>';
            <?php
        } ?>
            xml = xml+'</rows>';
            gridLeftProductAssociationShop.parse(xml);

            sbLeftProductAssociationShop=dhxlSCExtCheck.tabbar.cells("table_CAT_PRODUCT_ASSOCIATION_SHOP").attachStatusBar();
            function getGridStat_LeftProductAssociationShop(){
                var filteredRows=gridLeftProductAssociationShop.getRowsNum();
                var selectedRows=(gridLeftProductAssociationShop.getSelectedRowId()?gridLeftProductAssociationShop.getSelectedRowId().split(',').length:0);
                sbLeftProductAssociationShop.setText('<?php echo count($res).' '._l('Errors'); ?>'+" - <?php echo _l('Filter')._l(':'); ?> "+filteredRows+" - <?php echo _l('Selection')._l(':'); ?> "+selectedRows);
            }
            gridLeftProductAssociationShop.attachEvent("onFilterEnd", function(elements){
                getGridStat_LeftProductAssociationShop();
            });
            gridLeftProductAssociationShop.attachEvent("onSelectStateChanged", function(id){
                getGridStat_LeftProductAssociationShop();
            });
            getGridStat_LeftProductAssociationShop();

            gridLeftProductAssociationShop.attachEvent("onRowSelect", function(id,ind){
                var ids = gridLeftProductAssociationShop.getUserData(id, "ids");
            });

            // SHOP
            var tbRightProductAssociationShop = colsProductAssociationShop.cells('b').attachToolbar();
            tbRightProductAssociationShop.setIconset('awesome');
            colsProductAssociationShop.cells('b').setText("<?php echo _l('Shop list'); ?>");
            tbRightProductAssociationShop.addButton("replaceIdDefaultShopWithSelected", 0, "", 'fad fa-tools green', 'fad fa-tools green');
            tbRightProductAssociationShop.setItemToolTip('replaceIdDefaultShopWithSelected','<?php echo _l('Update default shop with selected shop', 1); ?>');
            tbRightProductAssociationShop.attachEvent("onClick",
                function(id){
                    if (id=='replaceIdDefaultShopWithSelected')
                    {
                        replaceIdDefaultShopWithSelected();
                    }
                });

            var gridProductAssociationShop = colsProductAssociationShop.cells('b').attachGrid();
            gridProductAssociationShop.setImagePath("lib/js/imgs/");
            gridProductAssociationShop.enableSmartRendering(true);
            gridProductAssociationShop.enableMultiselect(true);

            gridProductAssociationShop.setHeader("<?php echo _l('ID'); ?>,<?php echo _l('Name'); ?>");
            gridProductAssociationShop.setColAlign("left,left");
            gridProductAssociationShop.setColTypes("ro,ro");
            gridProductAssociationShop.setInitWidths("40,");
            gridProductAssociationShop.enableMultiselect(false);
            gridProductAssociationShop.init();

            var xml = '<rows>';
            <?php foreach ($shops as $id => $row)
        {
            ?>
            xml = xml+'<row id="<?php echo $row['id_shop']; ?>">';
            xml = xml+'    <userdata name="ids"><?php echo $row['id_shop']; ?></userdata>';
            xml = xml+'    <cell><![CDATA[<?php echo $row['id_shop']; ?>]]></cell>';
            xml = xml+'    <cell><![CDATA[<?php echo str_replace("'", "\'", $row['name']); ?>]]></cell>';
            xml = xml+'</row>';
            <?php
        } ?>
            xml = xml+'</rows>';
            gridProductAssociationShop.parse(xml);

            function replaceIdDefaultShop()
            {
                var products_id = gridLeftProductAssociationShop.getSelectedRowId();
                if(products_id==null || products_id=="")
                    products_id = 0;
                if(products_id!="0")
                {
                    $.post("index.php?ajax=1&act=all_win-fixmyprestashop_actions&check=CAT_PRODUCT_ASSOCIATION_SHOP&id_lang="+SC_ID_LANG, { "action": "replace_id_default_shop", "ids": products_id}, function(data){
                        dhxlSCExtCheck.tabbar.tabs("table_CAT_PRODUCT_ASSOCIATION_SHOP").close();

                        dhxlSCExtCheck.gridChecks.selectRowById('CAT_PRODUCT_ASSOCIATION_SHOP');
                        doCheck(false);
                    });
                }
            }
            function replaceIdDefaultShopWithSelected()
            {
                var products_id = gridLeftProductAssociationShop.getSelectedRowId();
                var selectedShop = gridProductAssociationShop.getSelectedRowId();
                if(selectedShop==null || selectedShop=="")
                    selectedShop = 0;
                if(products_id==null || products_id=="")
                    products_id = 0;
                if(selectedShop!="0" && products_id!="0")
                {
                    $.post("index.php?ajax=1&act=all_win-fixmyprestashop_actions&check=CAT_PRODUCT_ASSOCIATION_SHOP&id_lang="+SC_ID_LANG, { "action": "replace_id_default_shop_selected", "ids": products_id, "selectedShop":selectedShop}, function(data){
                        dhxlSCExtCheck.tabbar.tabs("table_CAT_PRODUCT_ASSOCIATION_SHOP").close();

                        dhxlSCExtCheck.gridChecks.selectRowById('CAT_PRODUCT_ASSOCIATION_SHOP');
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
        'title' => _l('Pdt. without dft. shop'),
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
            list($id_product, $id_shop_default) = explode('_', $id);

            $id_shop_base = 0;
            if (Configuration::get('PS_SHOP_DEFAULT'))
            {
                $sql = 'SELECT id_shop
                            FROM '._DB_PREFIX_.'product_shop 
                            WHERE id_product = '.(int) $id_product.'
                            AND id_shop = '.(int) Configuration::get('PS_SHOP_DEFAULT');
                if ($res = Db::getInstance()->getRow($sql))
                {
                    $id_shop_base = $res['id_shop'];
                }
            }
            if (empty($id_shop_base))
            {
                $sql = 'SELECT id_shop
                        FROM '._DB_PREFIX_.'product_shop 
                        WHERE id_product = '.(int) $id_product;
                if ($res = Db::getInstance()->getRow($sql))
                {
                    $id_shop_base = $res['id_shop'];
                }
            }
            if (!empty($id_shop_base))
            {
                $product = new Product((int) $id_product, false, $cookie->id_lang, (int) $id_shop_base);
                $product->id_shop_list = array($id_shop_default);
                $res = $product->save();
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
            list($id_product, $id_shop_default) = explode('_', $id);

            $id_shop_base = 0;

            if ($selectedShop)
            {
                $sql = 'SELECT id_shop
                            FROM '._DB_PREFIX_.'product_shop 
                            WHERE id_product = '.(int) $id_product.'
                            AND id_shop = '.(int) $selectedShop;
                if ($res = Db::getInstance()->getRow($sql))
                {
                    $id_shop_base = $selectedShop;
                }
            }

            if (Configuration::get('PS_SHOP_DEFAULT'))
            {
                $sql = 'SELECT id_shop
                            FROM '._DB_PREFIX_.'product_shop 
                            WHERE id_product = '.(int) $id_product.'
                            AND id_shop = '.(int) Configuration::get('PS_SHOP_DEFAULT');
                if ($res = Db::getInstance()->getRow($sql))
                {
                    $id_shop_base = $res['id_shop'];
                }
            }

            if (empty($id_shop_base))
            {
                $sql = 'SELECT id_shop
                        FROM '._DB_PREFIX_.'product_shop 
                        WHERE id_product = '.(int) $id_product;
                if ($res = Db::getInstance()->getRow($sql))
                {
                    $id_shop_base = $res['id_shop'];
                }
            }

            if (!empty($id_shop_base))
            {
                $product = new Product((int) $id_product, false, null, (int) $id_shop_base);
                $product->id_shop_default = $selectedShop;
                $product->id_shop_list = array($selectedShop);
                $res = $product->save();
            }
        }
    }
}
