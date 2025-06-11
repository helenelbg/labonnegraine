<?php
$post_action = Tools::getValue('action');
$action_name = 'CAT_PROD_WITHOUT_PRODUCT_TYPE';
$tab_title = _l('Without prd. type');
if (!empty($post_action) && $post_action == 'do_check')
{
    $sql = 'SELECT p.id_product,pl.name
                FROM '._DB_PREFIX_.'product p
                LEFT JOIN '._DB_PREFIX_.'product_lang pl 
                    ON (pl.id_product = p.id_product '.(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? ' AND pl.id_lang = '.(int) $id_lang.' AND pl.id_shop = p.id_shop_default' : '') .')
                WHERE p.product_type IS NULL 
                    OR p.product_type =""
                LIMIT 1500';
    $res = Db::getInstance()->executeS($sql);

    $content = '';
    $content_js = '';
    $results = 'OK';
    if ($res)
    {
        $results = 'KO';
        ob_start(); ?>
        <script type="text/javascript">
    
            var tbProductWithoutDefaultType = dhxlSCExtCheck.tabbar.cells("table_<?php echo $action_name; ?>").attachToolbar();
            tbProductWithoutDefaultType.setIconset('awesome');
            tbProductWithoutDefaultType.addButton("recovertype", 1000, "", 'fa fa-plus-circle green', 'fa fa-plus-circle green');
            tbProductWithoutDefaultType.setItemToolTip('recovertype','<?php echo _l('Set the default type for all impacted products'); ?>');
            tbProductWithoutDefaultType.attachEvent("onClick",
                function(id){
                    switch (id) {
                        case 'recovertype':
                            gridProductWithoutDefaultType.selectAll();
                            recoverProductType();
                            break;
                    }
                });
        
            var gridProductWithoutDefaultType = dhxlSCExtCheck.tabbar.cells("table_<?php echo $action_name; ?>").attachGrid();
            gridProductWithoutDefaultType.setImagePath("lib/js/imgs/");
            gridProductWithoutDefaultType.enableSmartRendering(true);
            gridProductWithoutDefaultType.enableMultiselect(true);
    
            gridProductWithoutDefaultType.setHeader("ID <?php echo _l('product'); ?>,<?php echo _l('Name'); ?>");
            gridProductWithoutDefaultType.setInitWidths("60,*");
            gridProductWithoutDefaultType.setColAlign("left,left");
            gridProductWithoutDefaultType.setColTypes("ro,ro");
            gridProductWithoutDefaultType.setColSorting("int,str");
            gridProductWithoutDefaultType.attachHeader("#numeric_filter,#text_filter");
            gridProductWithoutDefaultType.init();

            var xml = '<rows>';
            <?php foreach ($res as $product) { ?>
            xml = xml+'   <row id="<?php echo $product['id_product']; ?>">';
            xml = xml+'      <cell><![CDATA[<?php echo $product['id_product']; ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php echo addslashes($product['name']); ?>]]></cell>';
            xml = xml+'   </row>';
            <?php } ?>
            xml = xml+'</rows>';
            gridProductWithoutDefaultType.parse(xml);

            sbProductWithoutDefaultType=dhxlSCExtCheck.tabbar.cells("table_<?php echo $action_name; ?>").attachStatusBar();
             function getGridStat_ProductWithoutDefaultType(){
                 var filteredRows=gridProductWithoutDefaultType.getRowsNum();
                 var selectedRows=(gridProductWithoutDefaultType.getSelectedRowId()?gridProductWithoutDefaultType.getSelectedRowId().split(',').length:0);
                 sbProductWithoutDefaultType.setText('<?php echo count($res).' '._l('Errors'); ?>'+" - <?php echo _l('Filter')._l(':'); ?> "+filteredRows+" - <?php echo _l('Selection')._l(':'); ?> "+selectedRows);
             }
             gridProductWithoutDefaultType.attachEvent("onFilterEnd", function(elements){
                getGridStat_ProductWithoutDefaultType();
             });
             gridProductWithoutDefaultType.attachEvent("onSelectStateChanged", function(id){
                getGridStat_ProductWithoutDefaultType();
             });
             getGridStat_ProductWithoutDefaultType();

             function recoverProductType()
            {
                $.post("index.php?ajax=1&act=all_win-fixmyprestashop_actions&check=<?php echo $action_name; ?>", {
                    action: "recovertype",
                }, function(data){
                    dhxlSCExtCheck.tabbar.tabs("table_<?php echo $action_name; ?>").close();
                    dhxlSCExtCheck.gridChecks.selectRowById('<?php echo $action_name; ?>');
                    doCheck(false);
                });
            }
        </script>
        <?php
        $content_js = ob_get_clean();
    }
    echo json_encode(array(
            'results' => $results,
            'contentType' => 'grid',
            'content' => $content,
            'title' => $tab_title,
            'contentJs' => $content_js,
    ));
}
elseif (!empty($post_action) && $post_action == 'recovertype')
{
    $product_type = PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductType::TYPE_STANDARD;
    Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'product SET product_type = "'.pSQL($product_type).'" WHERE product_type IS NULL OR product_type = ""');
}
