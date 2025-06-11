<?php
$post_action = Tools::getValue('action');
if (!empty($post_action) && $post_action == 'do_check')
{
    $products = array();
    if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
    {
        $sql = 'SELECT pl.id_product, pl.name, s.name AS name_shop, pl.id_shop
                FROM '._DB_PREFIX_.'product_lang pl
                LEFT JOIN '._DB_PREFIX_.'shop s ON (s.id_shop = pl.id_shop)
                WHERE pl.id_lang = '.(int) SCI::getConfigurationValue('PS_LANG_DEFAULT').' 
                AND CONCAT(pl.id_product,"_",pl.id_shop) IN (SELECT CONCAT(ims.id_product,"_",ims.id_shop) AS product_shop
                                                                FROM '._DB_PREFIX_.'image_shop ims
                                                                WHERE ims.id_product > 0
                                                                GROUP BY ims.id_product,ims.id_shop
                                                                HAVING COALESCE(SUM(ims.cover), 0) < COUNT(DISTINCT(ims.id_shop)))
                LIMIT 1500';
        $products = Db::getInstance()->ExecuteS($sql);
    }
    else
    {
        $sql = 'SELECT i.id_product, pl.name, 
                    (SELECT COUNT(*) 
                        FROM '._DB_PREFIX_.'image i2 
                        WHERE i2.id_product = i.id_product AND cover = 1) AS nb_cover, 
                    (SELECT COUNT(*) 
                        FROM '._DB_PREFIX_.'image i2 
                        WHERE i2.id_product = i.id_product) AS nb
                FROM '._DB_PREFIX_.'image i
                INNER JOIN '._DB_PREFIX_."product_lang pl ON (pl.id_product = i.id_product AND pl.id_lang = '".(int) SCI::getConfigurationValue('PS_LANG_DEFAULT')."')
                GROUP BY i.id_product LIMIT 1500";
        $res = Db::getInstance()->ExecuteS($sql);
        if (!empty($res) && count($res) > 0)
        {
            foreach ($res as $p)
            {
                if (empty($p['nb_cover']) && !empty($p['nb']))
                {
                    $products[] = $p;
                }
            }
        }
    }

    $content = '';
    $content_js = '';
    $results = 'OK';
    if (!empty($products) && count($products) > 0)
    {
        $results = 'KO';
        ob_start(); ?>
        <script type="text/javascript">
    
            var tbMissingCoverImage = dhxlSCExtCheck.tabbar.cells("table_CAT_PROD_MISSING_COVER_IMAGE").attachToolbar();
            tbMissingCoverImage.setIconset('awesome');
            tbMissingCoverImage.addButton("selectall", 0, "", 'fa fa-bolt yellow', 'fa fa-bolt yellow');
            tbMissingCoverImage.setItemToolTip('selectall','<?php echo _l('Select all'); ?>');
            tbMissingCoverImage.addButton("put_cover", 0, "", 'fa fa-plus-circle green', 'fa fa-plus-circle green');
            tbMissingCoverImage.setItemToolTip('put_cover','<?php echo _l('Put first image on cover'); ?>');
            tbMissingCoverImage.attachEvent("onClick",
                function(id){
                    if (id=='selectall')
                    {
                        gridMissingCoverImage.selectAll();
                        getGridStat_MissingCoverImage();
                    }
                    if (id=='put_cover')
                    {
                        <?php if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) { ?>
                            addMissingCoverImageMS()
                        <?php }
        else
        { ?>
                            addMissingCoverImage()
                        <?php } ?>
                    }
                });
        
            var gridMissingCoverImage = dhxlSCExtCheck.tabbar.cells("table_CAT_PROD_MISSING_COVER_IMAGE").attachGrid();
            gridMissingCoverImage.setImagePath("lib/js/imgs/");
            gridMissingCoverImage.enableSmartRendering(true);
            gridMissingCoverImage.enableMultiselect(true);
    
            gridMissingCoverImage.setHeader("ID,<?php echo _l('Name'); ?><?php if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
        {
            echo ','._l('Shop');
        } ?>");
            gridMissingCoverImage.setInitWidths("100,<?php if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
        {
            echo '100,';
        } ?>*");
            gridMissingCoverImage.setColAlign("left,left<?php if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
        {
            echo ',left';
        } ?>");
            gridMissingCoverImage.setColTypes("ro,ro,ro");
            gridMissingCoverImage.setColSorting("int,str,str");
            gridMissingCoverImage.attachHeader("#numeric_filter,#text_filter,#text_filter");
            gridMissingCoverImage.init();
    
            var xml = '<rows>';
            <?php foreach ($products as $product) { ?>
            <?php if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) { ?>
            xml = xml+'   <row id="<?php echo $product['id_product'].'_'.$product['id_shop']; ?>">';
            <?php }
        else
        { ?>
            xml = xml+'   <row id="<?php echo $product['id_product']; ?>">';
            <?php } ?>
            xml = xml+'      <cell><![CDATA[<?php echo $product['id_product']; ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php echo str_replace("'", "\'", str_replace("\'", "'", $product['name'])); ?>]]></cell>';
            <?php if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) { ?>
            xml = xml+'      <cell><![CDATA[<?php echo str_replace("'", "\'", str_replace("\'", "'", $product['name_shop'])); ?>]]></cell>';
            <?php } ?>
            xml = xml+'   </row>';
            <?php } ?>
            xml = xml+'</rows>';
            gridMissingCoverImage.parse(xml);

            sbMissingCoverImage=dhxlSCExtCheck.tabbar.cells("table_CAT_PROD_MISSING_COVER_IMAGE").attachStatusBar();
            function getGridStat_MissingCoverImage(){
                var filteredRows=gridMissingCoverImage.getRowsNum();
                var selectedRows=(gridMissingCoverImage.getSelectedRowId()?gridMissingCoverImage.getSelectedRowId().split(',').length:0);
                sbMissingCoverImage.setText('<?php echo count($products).' '._l('Errors'); ?>'+" - <?php echo _l('Filter')._l(':'); ?> "+filteredRows+" - <?php echo _l('Selection')._l(':'); ?> "+selectedRows);
            }
            gridMissingCoverImage.attachEvent("onFilterEnd", function(elements){
                getGridStat_MissingCoverImage();
            });
            gridMissingCoverImage.attachEvent("onSelectStateChanged", function(id){
                getGridStat_MissingCoverImage();
            });
            getGridStat_MissingCoverImage();

            function addMissingCoverImage()
            {
                var selectedMissingCoverImages = gridMissingCoverImage.getSelectedRowId();
                if(selectedMissingCoverImages==null || selectedMissingCoverImages=="")
                    selectedMissingCoverImages = 0;
                if(selectedMissingCoverImages!="0")
                {
                    $.post("index.php?ajax=1&act=all_win-fixmyprestashop_actions&check=CAT_PROD_MISSING_COVER_IMAGE&id_lang="+SC_ID_LANG, { "action": "image_cover", "ids": selectedMissingCoverImages}, function(data){
                        dhxlSCExtCheck.tabbar.tabs("table_CAT_PROD_MISSING_COVER_IMAGE").close();

                         dhxlSCExtCheck.gridChecks.selectRowById('CAT_PROD_MISSING_COVER_IMAGE');
                         doCheck(false);
                    });
                }
            }

            function addMissingCoverImageMS()
            {
                var selectedMissingCoverImages = gridMissingCoverImage.getSelectedRowId();
                if(selectedMissingCoverImages==null || selectedMissingCoverImages=="")
                    selectedMissingCoverImages = 0;
                if(selectedMissingCoverImages!="0")
                {
                    $.post("index.php?ajax=1&act=all_win-fixmyprestashop_actions&check=CAT_PROD_MISSING_COVER_IMAGE&id_lang="+SC_ID_LANG, { "action": "image_cover_ms", "ids": selectedMissingCoverImages}, function(data){
                        dhxlSCExtCheck.tabbar.tabs("table_CAT_PROD_MISSING_COVER_IMAGE").close();

                         dhxlSCExtCheck.gridChecks.selectRowById('CAT_PROD_MISSING_COVER_IMAGE');
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
            'title' => _l('Image cover'),
            'contentJs' => $content_js,
    ));
}
elseif (!empty($post_action) && $post_action == 'image_cover')
{
    $post_ids = Tools::getValue('ids');
    if (!empty($post_ids))
    {
        $ids = explode(',', $post_ids);
        foreach ($ids as $id)
        {
            $sql = 'SELECT id_image
                    FROM '._DB_PREFIX_."image
                    WHERE id_product = '".(int) $id."'
                    ORDER BY position ASC
                    LIMIT 1";
            $image_first = Db::getInstance()->executeS($sql);
            if (!empty($image_first[0]['id_image']))
            {
                $sql = 'UPDATE '._DB_PREFIX_."image SET cover = '1' WHERE id_image = '".(int) $image_first[0]['id_image']."'";
                $res = dbExecuteForeignKeyOff($sql);
            }
        }
    }
}
elseif (!empty($post_action) && $post_action == 'image_cover_ms')
{
    $post_ids = Tools::getValue('ids');
    if (!empty($post_ids))
    {
        $ids = explode(',', $post_ids);
        foreach ($ids as $id)
        {
            list($id_product, $id_shop) = explode('_', $id);

            $sql = 'SELECT ims.id_image, ims.id_shop
                    FROM '._DB_PREFIX_.'image_shop ims
                        INNER JOIN '._DB_PREFIX_."image i ON (i.id_image = ims.id_image)
                    WHERE i.id_product = '".(int) $id_product."'
                        AND ims.id_shop = '".(int) $id_shop."'
                    ORDER BY i.position ASC
                    LIMIT 1";
            $image_first = Db::getInstance()->executeS($sql);
            if (!empty($image_first[0]['id_image']) && !empty($image_first[0]['id_shop']))
            {
                $sql = 'UPDATE '._DB_PREFIX_."image_shop SET cover = '1' WHERE id_image = '".(int) $image_first[0]['id_image']."' AND id_shop = '".(int) $image_first[0]['id_shop']."'";
                $res = dbExecuteForeignKeyOff($sql);
            }
        }
    }
}
