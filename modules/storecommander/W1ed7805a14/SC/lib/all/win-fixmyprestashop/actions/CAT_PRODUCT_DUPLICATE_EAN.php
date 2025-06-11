<?php
$post_action = Tools::getValue('action');
$id_lang = (int) Tools::getValue('id_lang');
$action_name = 'CAT_PRODUCT_DUPLICATE_EAN';
$tab_title = _l('Products same EAN13');

if (!empty($post_action) && $post_action == 'do_check')
{
    $res = array();
    $sql = 'SELECT ean13, count(*) AS c
                FROM '._DB_PREFIX_.'product
                WHERE ean13 != ""
                GROUP BY ean13
                HAVING c > 1
                ORDER BY c DESC';
    $ref_found = Db::getInstance()->ExecuteS($sql);
    if (!empty($ref_found))
    {
        $refs = array();
        foreach ($ref_found as $row)
        {
            $refs[] = $row['ean13'];
        }
        $sql = 'SELECT p.id_product, '.(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? ' ps.id_category_default ' : ' p.id_category_default').',p.ean13,pl.name, p.active '.(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? ' , ps.active ' : '').'
                    FROM '._DB_PREFIX_.'product p
                    LEFT JOIN '._DB_PREFIX_.'product_lang pl ON (pl.id_product = p.id_product AND pl.id_lang = '.(int) $id_lang.(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? ' AND pl.id_shop = p.id_shop_default' : '').')
                    '.(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? ' LEFT JOIN '._DB_PREFIX_.'product_shop ps ON (ps.id_product = p.id_product AND ps.id_shop = p.id_shop_default ) ' : '').'
                    WHERE p.ean13 IN ("'.implode('","', $refs).'") 
                    ORDER BY p.ean13';
        $res = Db::getInstance()->ExecuteS($sql);
    }

    $content = '';
    $content_js = '';
    $results = 'OK';
    if (!empty($res) && count($res) > 0)
    {
        $results = 'KO';
        ob_start(); ?>
        <script type="text/javascript">

            var tbProductSameEAN = dhxlSCExtCheck.tabbar.cells("table_<?php echo $action_name; ?>").attachToolbar();
            tbProductSameEAN.setIconset('awesome');
            var idProductSameEAN = '';
            tbProductSameEAN.addButton("gotocatalog", 0, "", 'fad fa-external-link green', 'fad fa-external-link green');
            tbProductSameEAN.setItemToolTip('gotocatalog','<?php echo _l('Go to the product in catalog.'); ?>');
            tbProductSameEAN.addButton("exportcsv", 0, "", 'fad fa-file-csv green', 'fad fa-file-csv green');
            tbProductSameEAN.setItemToolTip('exportcsv','<?php echo _l('Export grid to clipboard in CSV format for MSExcel with tab delimiter.'); ?>');
            tbProductSameEAN.attachEvent("onClick",
                function(id){
                    if (id=='gotocatalog')
                    {
                        if(idProductSameEAN !== '') {
                            let url = "?page=cat_tree&open_cat_grid="+idProductSameEAN;
                            window.open(url,'_blank');
                        }

                    }
                    if(id=='exportcsv') {
                        displayQuickExportWindow(gridProductSameEAN,1);
                    }
                });

            var gridProductSameEAN = dhxlSCExtCheck.tabbar.cells("table_<?php echo $action_name; ?>").attachGrid();
            gridProductSameEAN.setImagePath("lib/js/imgs/");
            gridProductSameEAN.enableSmartRendering(true);
            gridProductSameEAN.enableMultiselect(false);

            gridProductSameEAN.setHeader("ID <?php echo _l('product'); ?>,<?php echo _l('EAN'); ?>,<?php echo _l('Active'); ?>,<?php echo _l('Name'); ?>");
            gridProductSameEAN.setInitWidths("100,100,60,*");
            gridProductSameEAN.setColAlign("left,left,left,left");
            gridProductSameEAN.setColTypes("ro,ro,ro,ro");
            gridProductSameEAN.setColSorting("int,str,str,str");
            gridProductSameEAN.attachHeader("#numeric_filter,#text_filter,#select_filter,#text_filter");
            gridProductSameEAN.init();

            gridProductSameEAN.attachEvent('onRowSelect',function(id){
                idProductSameEAN = id;
            });

            var xml = '<rows>';
            <?php foreach ($res as $row) { ?>
            xml = xml+'   <row id="<?php echo $row['id_category_default'].'-'.$row['id_product']; ?>">';
            xml = xml+'      <cell><![CDATA[<?php echo $row['id_product']; ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php echo !empty($row['ean13']) ? $row['ean13'] : 0; ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php echo !empty($row['active']) ? _l('Yes') : _l('No'); ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php echo addslashes($row['name']); ?>]]></cell>';
            xml = xml+'   </row>';
            <?php } ?>
            xml = xml+'</rows>';
            gridProductSameEAN.parse(xml);

            sbProductSameEAN=dhxlSCExtCheck.tabbar.cells("table_<?php echo $action_name; ?>").attachStatusBar();
            function getGridStat_ProductSameEAN(){
                var filteredRows=gridProductSameEAN.getRowsNum();
                var selectedRows=(gridProductSameEAN.getSelectedRowId()?gridProductSameEAN.getSelectedRowId().split(',').length:0);
                sbProductSameEAN.setText('<?php echo count($res).' '._l('Errors'); ?>'+" - <?php echo _l('Filter')._l(':'); ?> "+filteredRows+" - <?php echo _l('Selection')._l(':'); ?> "+selectedRows);
            }
            gridProductSameEAN.attachEvent("onFilterEnd", function(elements){
                getGridStat_ProductSameEAN();
            });
            gridProductSameEAN.attachEvent("onSelectStateChanged", function(id){
                getGridStat_ProductSameEAN();
            });
            getGridStat_ProductSameEAN();
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
