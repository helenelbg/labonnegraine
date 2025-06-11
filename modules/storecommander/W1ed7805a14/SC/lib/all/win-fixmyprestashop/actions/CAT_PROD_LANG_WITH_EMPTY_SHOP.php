<?php
$post_action = Tools::getValue('action');
if (!empty($post_action) && $post_action == 'do_check')
{
    $sql = 'SELECT *
        FROM `'._DB_PREFIX_.'product_lang`
        WHERE id_shop = 0 LIMIT 1500';
    $res = Db::getInstance()->ExecuteS($sql);

    $content = '';
    $content_js = '';
    $results = 'OK';
    if (!empty($res) && count($res) > 0)
    {
        $results = 'KO';
        ob_start(); ?>
        <script type="text/javascript">
    
            var tbProductLangEmptyShop = dhxlSCExtCheck.tabbar.cells("table_CAT_PROD_LANG_WITH_EMPTY_SHOP").attachToolbar();
            tbProductLangEmptyShop.setIconset('awesome');
            tbProductLangEmptyShop.addButton("selectall", 0, "", 'fa fa-bolt yellow', 'fa fa-bolt yellow');
            tbProductLangEmptyShop.setItemToolTip('selectall','<?php echo _l('Select all'); ?>');
            tbProductLangEmptyShop.addButton("delete", 0, "", 'fa fa-minus-circle red', 'fa fa-minus-circle red');
            tbProductLangEmptyShop.setItemToolTip('delete','<?php echo _l('Remove mistranslations'); ?>');
            tbProductLangEmptyShop.attachEvent("onClick",
                function(id){
                    if (id=='selectall')
                    {
                        gridProductLangEmptyShop.selectAll();
                        getGridStat_ProductLangEmptyShop();
                    }
                    if (id=='delete')
                    {
                        deleteProductLangEmptyShop();
                    }
                });
        
            var gridProductLangEmptyShop = dhxlSCExtCheck.tabbar.cells("table_CAT_PROD_LANG_WITH_EMPTY_SHOP").attachGrid();
            gridProductLangEmptyShop.setImagePath("lib/js/imgs/");
            gridProductLangEmptyShop.enableSmartRendering(true);
            gridProductLangEmptyShop.enableMultiselect(true);

            gridProductLangEmptyShop.setHeader("ID,<?php echo _l('Name'); ?>,<?php echo _l('Lang'); ?>");
            gridProductLangEmptyShop.setInitWidths("100,300,200");
            gridProductLangEmptyShop.setColAlign("left,left,left");
            gridProductLangEmptyShop.setColTypes("ro,ro,ro");
            gridProductLangEmptyShop.setColSorting("int,str,str");
            gridProductLangEmptyShop.attachHeader("#numeric_filter,#text_filter,#select_filter");
            gridProductLangEmptyShop.init();
    
            var xml = '<rows>';
            <?php foreach ($res as $product)
        {
            $lang = Language::getLanguage($product['id_lang']); ?>
            xml = xml+'   <row id="<?php echo $product['id_product'].'_'.$product['id_lang']; ?>">';
            xml = xml+'      <cell><![CDATA[<?php echo $product['id_product']; ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php echo str_replace("'", "\'", $product['name']); ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php echo str_replace("'", "\'", $lang['name']); ?>]]></cell>';
            xml = xml+'   </row>';
            <?php
        } ?>
            xml = xml+'</rows>';
            gridProductLangEmptyShop.parse(xml);

            sbProductLangEmptyShop=dhxlSCExtCheck.tabbar.cells("table_CAT_PROD_LANG_WITH_EMPTY_SHOP").attachStatusBar();
            function getGridStat_ProductLangEmptyShop(){
                var filteredRows=gridProductLangEmptyShop.getRowsNum();
                var selectedRows=(gridProductLangEmptyShop.getSelectedRowId()?gridProductLangEmptyShop.getSelectedRowId().split(',').length:0);
                sbProductLangEmptyShop.setText('<?php echo count($res).' '._l('Errors'); ?>'+" - <?php echo _l('Filter')._l(':'); ?> "+filteredRows+" - <?php echo _l('Selection')._l(':'); ?> "+selectedRows);
            }
            gridProductLangEmptyShop.attachEvent("onFilterEnd", function(elements){
                getGridStat_ProductLangEmptyShop();
            });
            gridProductLangEmptyShop.attachEvent("onSelectStateChanged", function(id){
                getGridStat_ProductLangEmptyShop();
            });
            getGridStat_ProductLangEmptyShop();

            function deleteProductLangEmptyShop()
            {
                var selectedProductLangEmptyShops = gridProductLangEmptyShop.getSelectedRowId();
                if(selectedProductLangEmptyShops==null || selectedProductLangEmptyShops=="")
                    selectedProductLangEmptyShops = 0;
                if(selectedProductLangEmptyShops!="0")
                {
                    $.post("index.php?ajax=1&act=all_win-fixmyprestashop_actions&check=CAT_PROD_LANG_WITH_EMPTY_SHOP&id_lang="+SC_ID_LANG, { "action": "delete_products", "ids": selectedProductLangEmptyShops}, function(data){
                        dhxlSCExtCheck.tabbar.tabs("table_CAT_PROD_LANG_WITH_EMPTY_SHOP").close();

                         dhxlSCExtCheck.gridChecks.selectRowById('CAT_PROD_LANG_WITH_EMPTY_SHOP');
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
            'title' => _l('Prod_lang empty shop'),
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
            list($id_product, $id_lang) = explode('_', $id);
            if (!empty($id_product) && !empty($id_lang))
            {
                $sql = 'DELETE FROM `'._DB_PREFIX_."product_lang` 
                WHERE id_product = '".(int) $id_product."'
                    AND id_lang = '".(int) $id_lang."'
                    AND id_shop = '0'";
                dbExecuteForeignKeyOff($sql);
            }
        }
    }
}
