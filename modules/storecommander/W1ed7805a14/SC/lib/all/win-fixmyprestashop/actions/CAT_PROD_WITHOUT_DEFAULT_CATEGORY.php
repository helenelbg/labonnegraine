<?php
$post_action = Tools::getValue('action');
$action_name = 'CAT_PROD_WITHOUT_DEFAULT_CATEGORY';
$tab_title = _l('Without default cat.');
if (!empty($post_action) && $post_action == 'do_check')
{
    $sql = 'SELECT p.id_product, pl.name'
    .(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? ', ps.id_category_default' : ', p.id_category_default')
    .(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? ', ps.active ' : ', p.active').'
            FROM `'._DB_PREFIX_.'product` p
             LEFT JOIN `'._DB_PREFIX_.'product_lang` pl 
                    ON (pl.id_product = p.id_product AND pl.id_lang = '.(int) $id_lang.(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? ' AND pl.id_shop = p.id_shop_default' : '').')
            '.(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? ' LEFT JOIN '._DB_PREFIX_.'product_shop ps ON (ps.id_product = p.id_product AND ps.id_shop = p.id_shop_default ) ' : '').'
            LEFT JOIN `'._DB_PREFIX_.'category` c ON (c.id_category = ps.id_category_default)
            WHERE '.(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? 'ps.id_category_default ' : 'p.id_category_default').' = 0 
            OR '.(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? 'ps.id_category_default ' : 'p.id_category_default').' IS NULL 
            OR c.id_category  IS NULL
            LIMIT 1500';

    $res = Db::getInstance()->ExecuteS($sql);

    $content = '';
    $content_js = '';
    $results = 'OK';
    if (!empty($res))
    {
        $results = 'KO';
        ob_start(); ?>
        <script type="text/javascript">
    
            var tbProductWithoutDefaultCategory = dhxlSCExtCheck.tabbar.cells("table_<?php echo $action_name; ?>").attachToolbar();
            tbProductWithoutDefaultCategory.setIconset('awesome');
            tbProductWithoutDefaultCategory.addButton("selectall", 0, "", 'fa fa-bolt yellow', 'fa fa-bolt yellow');
            tbProductWithoutDefaultCategory.setItemToolTip('selectall','<?php echo _l('Select all'); ?>');
            tbProductWithoutDefaultCategory.addButton("exportcsv", 0, "", 'fad fa-file-csv green', 'fad fa-file-csv green');
            tbProductWithoutDefaultCategory.setItemToolTip('exportcsv','<?php echo _l('Export grid to clipboard in CSV format for MSExcel with tab delimiter.'); ?>');
            tbProductWithoutDefaultCategory.attachEvent("onClick",
                function(id){
                    switch (id) {
                        case 'selectall':
                            gridProductWithoutDefaultCategory.selectAll();
                            getGridStat_ProductWithoutDefaultCategory();
                            break;
                        case 'exportcsv':
                            displayQuickExportWindow(gridProductWithoutDefaultCategory, 1);
                            break;
                    }
                });
        
            var gridProductWithoutDefaultCategory = dhxlSCExtCheck.tabbar.cells("table_<?php echo $action_name; ?>").attachGrid();
            gridProductWithoutDefaultCategory.setImagePath("lib/js/imgs/");
            gridProductWithoutDefaultCategory.enableSmartRendering(true);
            gridProductWithoutDefaultCategory.enableMultiselect(true);
    
            gridProductWithoutDefaultCategory.setHeader("ID <?php echo _l('product'); ?>,<?php echo _l('Name'); ?>,<?php echo _l('Default category'); ?>,<?php echo _l('Active'); ?>");
            gridProductWithoutDefaultCategory.setInitWidths("60,200,60,60");
            gridProductWithoutDefaultCategory.setColAlign("left,left,left,left");
            gridProductWithoutDefaultCategory.setColTypes("ro,ro,ro,ro");
            gridProductWithoutDefaultCategory.setColSorting("int,str,str,str");
            gridProductWithoutDefaultCategory.attachHeader("#numeric_filter,#text_filter,#text_filter,#select_filter");
            gridProductWithoutDefaultCategory.init();

            var xml = '<rows>';
            <?php foreach ($res as $product) { ?>
            xml = xml+'   <row id="<?php echo $product['id_product']; ?>">';
            xml = xml+'      <cell><![CDATA[<?php echo $product['id_product']; ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php echo addslashes($product['name']); ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php echo (is_null($product['id_category_default'])) ? 'NULL' : $product['id_category_default']; ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php echo (!empty($product['active'])) ? _l('Yes') : _l('No'); ?>]]></cell>';
            xml = xml+'   </row>';
            <?php } ?>
            xml = xml+'</rows>';
            gridProductWithoutDefaultCategory.parse(xml);

            sbProductWithoutDefaultCategory=dhxlSCExtCheck.tabbar.cells("table_<?php echo $action_name; ?>").attachStatusBar();
             function getGridStat_ProductWithoutDefaultCategory(){
                 var filteredRows=gridProductWithoutDefaultCategory.getRowsNum();
                 var selectedRows=(gridProductWithoutDefaultCategory.getSelectedRowId()?gridProductWithoutDefaultCategory.getSelectedRowId().split(',').length:0);
                 sbProductWithoutDefaultCategory.setText('<?php echo count($res).' '._l('Errors'); ?>'+" - <?php echo _l('Filter')._l(':'); ?> "+filteredRows+" - <?php echo _l('Selection')._l(':'); ?> "+selectedRows);
             }
             gridProductWithoutDefaultCategory.attachEvent("onFilterEnd", function(elements){
                getGridStat_ProductWithoutDefaultCategory();
             });
             gridProductWithoutDefaultCategory.attachEvent("onSelectStateChanged", function(id){
                getGridStat_ProductWithoutDefaultCategory();
             });
             getGridStat_ProductWithoutDefaultCategory();
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