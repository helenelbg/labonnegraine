<?php
$post_action = Tools::getValue('action');
if (!empty($post_action) && $post_action == 'do_check')
{
    $sql = 'SELECT p.id_product, pl.name, '.(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? ' ps.id_category_default ' : ' p.id_category_default').', cl.name AS category_name
        FROM '._DB_PREFIX_.'product p
         LEFT JOIN '._DB_PREFIX_.'product_lang pl 
                    ON (pl.id_product = p.id_product AND pl.id_lang = '.(int) $id_lang.(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? ' AND pl.id_shop = p.id_shop_default' : '').')                    
         LEFT JOIN '._DB_PREFIX_.'category_lang cl 
                    ON (cl.id_category = p.id_category_default AND cl.id_lang = '.(int) $id_lang.(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? ' AND cl.id_shop = p.id_shop_default' : '').')
        '.(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? ' LEFT JOIN '._DB_PREFIX_.'product_shop ps ON (ps.id_product = p.id_product AND ps.id_shop = p.id_shop_default ) ' : '').'                    
        WHERE p.id_product NOT IN (SELECT cp.id_product FROM '._DB_PREFIX_.'category_product cp) 
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
    
            var tbProductWithoutCategory = dhxlSCExtCheck.tabbar.cells("table_CAT_PROD_WITHOUT_CATEGORY").attachToolbar();
            tbProductWithoutCategory.setIconset('awesome');
            tbProductWithoutCategory.addButton("selectall", 0, "", 'fa fa-bolt yellow', 'fa fa-bolt yellow');
            tbProductWithoutCategory.setItemToolTip('selectall','<?php echo _l('Select all'); ?>');
            tbProductWithoutCategory.addButton("put_default", 0, "", 'fad fa-bags-shopping blue', 'fad fa-bags-shopping blue');
            tbProductWithoutCategory.setItemToolTip('put_default','<?php echo _l('Put product in his default category'); ?>');
            tbProductWithoutCategory.addButton("exportcsv", 0, "", 'fad fa-file-csv green', 'fad fa-file-csv green');
            tbProductWithoutCategory.setItemToolTip('exportcsv','<?php echo _l('Export grid to clipboard in CSV format for MSExcel with tab delimiter.'); ?>');
            tbProductWithoutCategory.attachEvent("onClick",
                function(id){
                    switch (id) {
                        case 'selectall':
                            gridProductWithoutCategory.selectAll();
                            getGridStat_ProductWithoutCategory();
                            break;
                        case 'put_default':
                            putdefaultProductWithoutCategory();
                            break;
                        case 'exportcsv':
                            displayQuickExportWindow(gridProductWithoutCategory, 1);
                            break;
                    }
                });
        
            var gridProductWithoutCategory = dhxlSCExtCheck.tabbar.cells("table_CAT_PROD_WITHOUT_CATEGORY").attachGrid();
            gridProductWithoutCategory.setImagePath("lib/js/imgs/");
            gridProductWithoutCategory.enableSmartRendering(true);
            gridProductWithoutCategory.enableMultiselect(true);
    
            gridProductWithoutCategory.setHeader("ID <?php echo _l('product'); ?>,<?php echo _l('Name'); ?>,<?php echo _l('Default category'); ?>");
            gridProductWithoutCategory.setInitWidths("100,*,*");
            gridProductWithoutCategory.setColAlign("left,left,left");
            gridProductWithoutCategory.setColTypes("ro,ro,ro");
            gridProductWithoutCategory.setColSorting("int,str,str");
            gridProductWithoutCategory.attachHeader("#numeric_filter,#text_filter,#text_filter");
            gridProductWithoutCategory.init();

            var xml = '<rows>';
            <?php foreach ($res as $product) { ?>
            xml = xml+'   <row id="<?php echo $product['id_product']; ?>">';
            xml = xml+'      <cell><![CDATA[<?php echo $product['id_product']; ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php echo addslashes($product['name']); ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php echo '#'.$product['id_category_default'].' '.addslashes($product['category_name']); ?>]]></cell>';
            xml = xml+'   </row>';
            <?php } ?>
            xml = xml+'</rows>';
            gridProductWithoutCategory.parse(xml);

            sbProductWithoutCategory=dhxlSCExtCheck.tabbar.cells("table_CAT_PROD_WITHOUT_CATEGORY").attachStatusBar();
             function getGridStat_ProductWithoutCategory(){
                 var filteredRows=gridProductWithoutCategory.getRowsNum();
                 var selectedRows=(gridProductWithoutCategory.getSelectedRowId()?gridProductWithoutCategory.getSelectedRowId().split(',').length:0);
                 sbProductWithoutCategory.setText('<?php echo count($res).' '._l('Errors'); ?>'+" - <?php echo _l('Filter')._l(':'); ?> "+filteredRows+" - <?php echo _l('Selection')._l(':'); ?> "+selectedRows);
             }
             gridProductWithoutCategory.attachEvent("onFilterEnd", function(elements){
                getGridStat_ProductWithoutCategory();
             });
             gridProductWithoutCategory.attachEvent("onSelectStateChanged", function(id){
                getGridStat_ProductWithoutCategory();
             });
             getGridStat_ProductWithoutCategory();

            function putdefaultProductWithoutCategory()
            {
                var selectedProductWithoutCategorys = gridProductWithoutCategory.getSelectedRowId();
                if(selectedProductWithoutCategorys==null || selectedProductWithoutCategorys=="")
                    selectedProductWithoutCategorys = 0;
                if(selectedProductWithoutCategorys!="0")
                {
                    $.post("index.php?ajax=1&act=all_win-fixmyprestashop_actions&check=CAT_PROD_WITHOUT_CATEGORY&id_lang="+SC_ID_LANG, { "action": "put_default", "ids": selectedProductWithoutCategorys}, function(data){
                        dhxlSCExtCheck.tabbar.tabs("table_CAT_PROD_WITHOUT_CATEGORY").close();

                         dhxlSCExtCheck.gridChecks.selectRowById('CAT_PROD_WITHOUT_CATEGORY');
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
            'title' => _l('Without cat.'),
            'contentJs' => $content_js,
    ));
}
elseif (!empty($post_action) && $post_action == 'put_default')
{
    $post_ids = Tools::getValue('ids');
    if (!empty($post_ids))
    {
        $sql = 'SELECT *
        FROM '._DB_PREFIX_.'product
        WHERE id_product IN ('.pInSQL($post_ids).')';
        $res = Db::getInstance()->ExecuteS($sql);

        foreach ($res as $pdt)
        {
            if (!empty($pdt['id_category_default']))
            {
                $sql = 'INSERT INTO '._DB_PREFIX_."category_product (id_category,id_product,`position`)
                    VALUES ('".(int) $pdt['id_category_default']."','".(int) $pdt['id_product']."','0')";
                $res = dbExecuteForeignKeyOff($sql);
            }
        }
    }
}
