<?php
$post_action = Tools::getValue('action');
if (!empty($post_action) && $post_action == 'do_check')
{
    $sql = 'SELECT pl.*, l.name as lang
            FROM '._DB_PREFIX_.'product_lang pl
                INNER JOIN '._DB_PREFIX_."lang l ON l.id_lang = pl.id_lang
            WHERE 
                pl.link_rewrite IS NULL OR pl.link_rewrite = '' 
            ORDER BY pl.id_product, pl.id_lang LIMIT 1500 ";
    $res = Db::getInstance()->ExecuteS($sql);

    $content = '';
    $content_js = '';
    $results = 'OK';
    if (!empty($res) && count($res) > 0)
    {
        $results = 'KO';
        ob_start(); ?>
        <script type="text/javascript">
    
            var tbProductWithoutLinkRewrite = dhxlSCExtCheck.tabbar.cells("table_CAT_PROD_PRODUCT_WITHOUT_LINK_REWRITE").attachToolbar();
            tbProductWithoutLinkRewrite.setIconset('awesome');
            tbProductWithoutLinkRewrite.addButton("selectall", 0, "", 'fa fa-bolt yellow', 'fa fa-bolt yellow');
            tbProductWithoutLinkRewrite.setItemToolTip('selectall','<?php echo _l('Select all'); ?>');
            tbProductWithoutLinkRewrite.addButton("generate", 0, "", 'fad fa-tools green', 'fad fa-tools green');
            tbProductWithoutLinkRewrite.setItemToolTip('generate','<?php echo _l('Generate links'); ?>');
            tbProductWithoutLinkRewrite.attachEvent("onClick",
                function(id){
                    if (id=='selectall')
                    {
                        gridProductWithoutLinkRewrite.selectAll();
                        getGridStat_ProductWithoutLinkRewrite();
                    }
                    if (id=='generate')
                    {
                        generateLinksProductWithoutLinkRewrite();
                    }
                });
        
            var gridProductWithoutLinkRewrite = dhxlSCExtCheck.tabbar.cells("table_CAT_PROD_PRODUCT_WITHOUT_LINK_REWRITE").attachGrid();
            gridProductWithoutLinkRewrite.setImagePath("lib/js/imgs/");
            gridProductWithoutLinkRewrite.enableSmartRendering(true);
            gridProductWithoutLinkRewrite.enableMultiselect(true);
    
            gridProductWithoutLinkRewrite.setHeader("<?php echo _l('Product ID'); ?>,<?php echo _l('Name'); ?>,<?php echo _l('Lang'); ?>");
            gridProductWithoutLinkRewrite.setInitWidths("80,200,100");
            gridProductWithoutLinkRewrite.setColAlign("right,left,left");
            gridProductWithoutLinkRewrite.setColTypes("ro,ro,ro");
            gridProductWithoutLinkRewrite.setColSorting("int,str,str");
            gridProductWithoutLinkRewrite.attachHeader("#numeric_filter,#text_filter,#select_filter");
            gridProductWithoutLinkRewrite.init();
    
            var xml = '<rows>';
            <?php foreach ($res as $product) { ?>
            xml = xml+'   <row id="<?php echo $product['id_product']; ?>_<?php echo $product['id_lang']; ?>">';
            xml = xml+'      <cell><![CDATA[<?php echo $product['id_product']; ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php echo str_replace("'", "\'", $product['name']); ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php echo str_replace("'", "\'", $product['lang']); ?>]]></cell>';
            xml = xml+'   </row>';
            <?php } ?>
            xml = xml+'</rows>';
            gridProductWithoutLinkRewrite.parse(xml);

            sbProductWithoutLinkRewrite=dhxlSCExtCheck.tabbar.cells("table_CAT_PROD_PRODUCT_WITHOUT_LINK_REWRITE").attachStatusBar();
            function getGridStat_ProductWithoutLinkRewrite(){
                var filteredRows=gridProductWithoutLinkRewrite.getRowsNum();
                var selectedRows=(gridProductWithoutLinkRewrite.getSelectedRowId()?gridProductWithoutLinkRewrite.getSelectedRowId().split(',').length:0);
                sbProductWithoutLinkRewrite.setText('<?php echo count($res).' '._l('Errors'); ?>'+" - <?php echo _l('Filter')._l(':'); ?> "+filteredRows+" - <?php echo _l('Selection')._l(':'); ?> "+selectedRows);
            }
            gridProductWithoutLinkRewrite.attachEvent("onFilterEnd", function(elements){
                getGridStat_ProductWithoutLinkRewrite();
            });
            gridProductWithoutLinkRewrite.attachEvent("onSelectStateChanged", function(id){
                getGridStat_ProductWithoutLinkRewrite();
            });
            getGridStat_ProductWithoutLinkRewrite();
            
            <?php foreach ($res as $product)
        {
            if (empty($product['name'])) {?>
                gridProductWithoutLinkRewrite.cells("<?php echo $product['id_product']; ?>_<?php echo $product['id_lang']; ?>",1).setBgColor('red');
            <?php }
        } ?>

            function generateLinksProductWithoutLinkRewrite()
            {
                var selectedProductWithoutLinkRewrites = gridProductWithoutLinkRewrite.getSelectedRowId();
                if(selectedProductWithoutLinkRewrites==null || selectedProductWithoutLinkRewrites=="")
                    selectedProductWithoutLinkRewrites = 0;
                if(selectedProductWithoutLinkRewrites!="0")
                {
                    $.post("index.php?ajax=1&act=all_win-fixmyprestashop_actions&check=CAT_PROD_PRODUCT_WITHOUT_LINK_REWRITE&id_lang="+SC_ID_LANG, { "action": "generate_links", "ids": selectedProductWithoutLinkRewrites}, function(data){
                        dhxlSCExtCheck.tabbar.tabs("table_CAT_PROD_PRODUCT_WITHOUT_LINK_REWRITE").close();

                         dhxlSCExtCheck.gridChecks.selectRowById('CAT_PROD_PRODUCT_WITHOUT_LINK_REWRITE');
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
            'title' => _l('Miss. link rewrite'),
            'contentJs' => $content_js,
    ));
}
elseif (!empty($post_action) && $post_action == 'generate_links')
{
    $post_ids = Tools::getValue('ids');
    if (!empty($post_ids))
    {
        $ids = explode(',', $post_ids);
        foreach ($ids as $id)
        {
            $name = '';

            list($id_product, $id_lang) = explode('_', $id);

            $sql = 'SELECT name FROM '._DB_PREFIX_."product_lang  WHERE id_product = '".(int) $id_product."' AND id_lang = '".(int) $id_lang."' ";
            $name_in_lang = Db::getInstance()->getValue($sql);
            if (!empty($name_in_lang))
            {
                $name = $name_in_lang;
            }
            else
            {
                $sql = 'SELECT name FROM '._DB_PREFIX_."product_lang  WHERE id_product = '".(int) $id_product."' AND id_lang = '1' ";
                $name_en = Db::getInstance()->getValue($sql);
                if (!empty($name_en))
                {
                    $name = $name_en;
                }
                else
                {
                    $name = 'product';
                }
            }

            if (!empty($name))
            {
                $sql = 'UPDATE '._DB_PREFIX_."product_lang 
                        SET `link_rewrite`='".pSQL(Tools::link_rewrite($name))."', name='".pSQL($name)."'
                        WHERE id_product = '".(int) $id_product."' AND id_lang = '".(int) $id_lang."' ";
                $res = dbExecuteForeignKeyOff($sql);
            }
        }
    }
}
