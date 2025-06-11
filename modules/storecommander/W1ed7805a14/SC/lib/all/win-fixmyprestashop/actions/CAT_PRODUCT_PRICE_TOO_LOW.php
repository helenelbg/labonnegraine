<?php
if (!defined('STORE_COMMANDER')) { exit; }

$post_action = Tools::getValue('action');
$action_name = 'CAT_PRODUCT_PRICE_TOO_LOW';
$tab_title = _l('Products with price too low');

if (!empty($post_action) && $post_action == 'do_check')
{
    $langs = array();
    $tmpl = Language::getLanguages(false);
    $langs = array_column($tmpl,'iso_code','id_lang');

    $tmps = SCI::getAllShops();
    $shops = array_column($tmps,'name','id_shop');

    $sql = 'SELECT DISTINCT ps.`id_category_default`, p.`id_product`, p.`reference`, ps.`price` AS pv, ps.`wholesale_price` AS pa,
            (ps.`wholesale_price` - ps.`price`) / (ps.`price` / 100) AS negative_margin
            FROM `' . _DB_PREFIX_ . 'product` p INNER JOIN `' . _DB_PREFIX_ . 'product_shop` ps ON (p.`id_product` = ps.`id_product` AND ps.`id_shop` = p.`id_shop_default` ) 
            WHERE ps.`price` < ps.`wholesale_price` LIMIT 1500';
    $res = Db::getInstance()->ExecuteS($sql);

    $content = '';
    $content_js = '';
    $results = 'OK';
    if (!empty($res) && count($res) > 0)
    {
        $results = 'KO';
        ob_start(); ?>
        <script>

            var tbProductWithPriceTooLow = dhxlSCExtCheck.tabbar.cells("table_<?php echo $action_name; ?>").attachToolbar();
            tbProductWithPriceTooLow.setIconset('awesome');
            var idProductWithPriceTooLow = '';
            tbProductWithPriceTooLow.addButton("exportcsv", 0, "", 'fad fa-file-csv green', 'fad fa-file-csv green');
            tbProductWithPriceTooLow.setItemToolTip("exportcsv","<?php echo _l('Export grid to clipboard in CSV format for MSExcel with tab delimiter.'); ?>");
            tbProductWithPriceTooLow.addButton("gotocatalog", 0, "", 'fad fa-external-link green', 'fad fa-external-link green');
            tbProductWithPriceTooLow.setItemToolTip('gotocatalog','<?php echo _l('Go to the product in catalog.'); ?>');
            tbProductWithPriceTooLow.attachEvent("onClick",
                function(id){
                    if (id=='gotocatalog')
                    {
                        if(idProductWithPriceTooLow !== '') {
                            let url = "?page=cat_tree&open_cat_grid="+idProductWithPriceTooLow;
                            window.open(url,'_blank');
                        }
                    }
                    if (id=='exportcsv')
                    {
                        displayQuickExportWindow(gridProductWithPriceTooLow, 1);
                    }
                });

            var gridProductWithPriceTooLow = dhxlSCExtCheck.tabbar.cells("table_<?php echo $action_name; ?>").attachGrid();
            gridProductWithPriceTooLow.enableSmartRendering(false);
            gridProductWithPriceTooLow.enableMultiselect(true);

            gridProductWithPriceTooLow.setHeader("ID <?php echo _l('Product'); ?>,<?php echo _l('Reference'); ?>,<?php echo _l('Wholesale price'); ?>,<?php echo _l('Prod. price ex. tax'); ?>,<?php echo _l('Negative margin'); ?> %");
            gridProductWithPriceTooLow.setInitWidths("60,180,100,100,120");
            gridProductWithPriceTooLow.setColAlign("right,right,right,right,right");
            gridProductWithPriceTooLow.setColTypes("ro,ro,ro,ro,ro");
            gridProductWithPriceTooLow.setColSorting("int,str,int,int,int");
            gridProductWithPriceTooLow.attachHeader("#numeric_filter,#text_filter,#numeric_filter,#numeric_filter,#numeric_filter");
            gridProductWithPriceTooLow.init();
            
            gridProductWithPriceTooLow.attachEvent('onRowSelect',function(id){
                idProductWithPriceTooLow = id;
            });

            var xml = '<rows>';
            <?php foreach ($res as $row)
            {
            ?>
            xml = xml+'   <row id="<?php echo $row['id_category_default'].'-'.$row['id_product'] ?>">';
            xml = xml+'      <cell><![CDATA[<?php echo $row['id_product']; ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php echo addslashes($row['reference']); ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php echo number_format($row['pa'],2); ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php echo number_format($row['pv'],2); ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php echo number_format($row['negative_margin'],2); ?>]]></cell>';
            xml = xml+'   </row>';
            <?php
            } ?>
            xml = xml+'</rows>';
            gridProductWithPriceTooLow.parse(xml);

            sbProductWithPriceTooLow=dhxlSCExtCheck.tabbar.cells("table_<?php echo $action_name; ?>").attachStatusBar();
            function getGridStat_ProductWithPriceTooLow(){
                var filteredRows=gridProductWithPriceTooLow.getRowsNum();
                var selectedRows=(gridProductWithPriceTooLow.getSelectedRowId()?gridProductWithPriceTooLow.getSelectedRowId().split(',').length:0);
                sbProductWithPriceTooLow.setText('<?php echo count($res).' '._l('Errors'); ?>'+" - <?php echo _l('Filter')._l(':'); ?> "+filteredRows+" - <?php echo _l('Selection')._l(':'); ?> "+selectedRows);
            }
            gridProductWithPriceTooLow.attachEvent("onFilterEnd", function(elements){
                getGridStat_ProductWithPriceTooLow();
            });
            gridProductWithPriceTooLow.attachEvent("onSelectStateChanged", function(id){
                getGridStat_ProductWithPriceTooLow();
            });
            getGridStat_ProductWithPriceTooLow();

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
