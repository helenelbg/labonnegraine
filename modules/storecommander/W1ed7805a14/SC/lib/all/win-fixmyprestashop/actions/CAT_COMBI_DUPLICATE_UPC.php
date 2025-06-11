<?php
$post_action = Tools::getValue('action');
$action_name = 'CAT_COMBI_DUPLICATE_UPC';
$tab_title = _l('Combi. same UPC');

if (!empty($post_action) && $post_action == 'do_check')
{
    $res = array();
    $sql = 'SELECT upc, count(*) AS c
                FROM '._DB_PREFIX_.'product_attribute
                WHERE upc != ""
                GROUP BY upc
                HAVING c > 1
                ORDER BY c DESC';
    $ref_found = Db::getInstance()->ExecuteS($sql);
    if (!empty($ref_found))
    {
        $refs = array();
        foreach ($ref_found as $row)
        {
            $refs[] = $row['upc'];
        }
        $sql = 'SELECT pa.id_product_attribute,pa.id_product,'.(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? ' ps.id_category_default ' : ' p.id_category_default').',pa.upc,pl.name, p.active '.(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? ' , ps.active ' : '').'
                    FROM '._DB_PREFIX_.'product_attribute pa
                    LEFT JOIN '._DB_PREFIX_.'product p ON p.id_product = pa.id_product
                    LEFT JOIN '._DB_PREFIX_.'product_lang pl
                        ON (pl.id_product = p.id_product AND pl.id_lang = '.(int) $id_lang.(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? ' AND pl.id_shop = p.id_shop_default' : '').')
                    '.(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? ' LEFT JOIN '._DB_PREFIX_.'product_shop ps ON (ps.id_product = p.id_product AND ps.id_shop = p.id_shop_default ) ' : '').'
                    WHERE pa.upc IN ("'.implode('","', $refs).'") 
                    ORDER BY pa.upc,p.id_product';
        $res = Db::getInstance()->ExecuteS($sql);
    }

    $content = '';
    $content_js = '';
    $results = 'OK';
    if (!empty($res) && count($res) > 0)
    {
        $tmp = array();
        foreach ($res as $row)
        {
            $tmp[] = $row['id_product_attribute'];
        }
        $attr_name = SCI::cachingAttributeName($id_lang, $tmp);
        $results = 'KO';
        ob_start(); ?>
        <script type="text/javascript">

            var tbCombinationSameUPC = dhxlSCExtCheck.tabbar.cells("table_<?php echo $action_name; ?>").attachToolbar();
            tbCombinationSameUPC.setIconset('awesome');
            var idCombinationSameUPC = '';
            tbCombinationSameUPC.addButton("gotocatalog", 0, "", 'fa fa-sitemap', 'fa fa-sitemap');
            tbCombinationSameUPC.setItemToolTip('gotocatalog','<?php echo _l('Go to the combination in catalog.'); ?>');
            tbCombinationSameUPC.addButton("exportcsv", 0, "", 'fad fa-file-csv green', 'fad fa-file-csv green');
            tbCombinationSameUPC.setItemToolTip('exportcsv','<?php echo _l('Export grid to clipboard in CSV format for MSExcel with tab delimiter.'); ?>');
            tbCombinationSameUPC.attachEvent("onClick",
                function(id){
                    if (id=='gotocatalog')
                    {
                        if(idCombinationSameUPC !== '') {
                            let url = "?page=cat_tree&open_cat_grid="+idCombinationSameUPC;
                            window.open(url,'_blank');
                        }

                    }
                    if(id=='exportcsv') {
                        displayQuickExportWindow(gridCombinationSameUPC,1);
                    }
                });

            var gridCombinationSameUPC = dhxlSCExtCheck.tabbar.cells("table_<?php echo $action_name; ?>").attachGrid();
            gridCombinationSameUPC.setImagePath("lib/js/imgs/");
            gridCombinationSameUPC.enableSmartRendering(true);
            gridCombinationSameUPC.enableMultiselect(false);

            gridCombinationSameUPC.setHeader("ID <?php echo _l('product'); ?>,ID <?php echo _l('combination'); ?>,<?php echo _l('UPC'); ?>,<?php echo _l('Active'); ?>,<?php echo _l('Product name'); ?>,<?php echo _l('Combination name'); ?>");
            gridCombinationSameUPC.setInitWidths("100,100,100,60,200,200");
            gridCombinationSameUPC.setColAlign("left,left,left,left,left,left");
            gridCombinationSameUPC.setColTypes("ro,ro,ro,ro,ro,ro");
            gridCombinationSameUPC.setColSorting("int,int,str,str,str,str");
            gridCombinationSameUPC.attachHeader("#numeric_filter,#numeric_filter,#text_filter,#select_filter,#text_filter,#text_filter");
            gridCombinationSameUPC.init();

            gridCombinationSameUPC.attachEvent('onRowSelect',function(id){
                idCombinationSameUPC = id;
            });

            var xml = '<rows>';
            <?php foreach ($res as $row) { ?>
            xml = xml+'   <row id="<?php echo $row['id_category_default'].'-'.$row['id_product'].'-'.$row['id_product_attribute']; ?>">';
            xml = xml+'      <cell><![CDATA[<?php echo $row['id_product']; ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php echo $row['id_product_attribute']; ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php echo $row['upc']; ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php echo !empty($row['active']) ? _l('Yes') : _l('No'); ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php echo addslashes($row['name']); ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php echo array_key_exists($row['id_product_attribute'], $attr_name) ? addslashes($attr_name[$row['id_product_attribute']]) : ''; ?>]]></cell>';
            xml = xml+'   </row>';
            <?php } ?>
            xml = xml+'</rows>';
            gridCombinationSameUPC.parse(xml);

            sbCombinationSameUPC=dhxlSCExtCheck.tabbar.cells("table_<?php echo $action_name; ?>").attachStatusBar();
            function getGridStat_CombinationSameUPC(){
                var filteredRows=gridCombinationSameUPC.getRowsNum();
                var selectedRows=(gridCombinationSameUPC.getSelectedRowId()?gridCombinationSameUPC.getSelectedRowId().split(',').length:0);
                sbCombinationSameUPC.setText('<?php echo count($res).' '._l('Errors'); ?>'+" - <?php echo _l('Filter')._l(':'); ?> "+filteredRows+" - <?php echo _l('Selection')._l(':'); ?> "+selectedRows);
            }
            gridCombinationSameUPC.attachEvent("onFilterEnd", function(elements){
                getGridStat_CombinationSameUPC();
            });
            gridCombinationSameUPC.attachEvent("onSelectStateChanged", function(id){
                getGridStat_CombinationSameUPC();
            });
            getGridStat_CombinationSameUPC();
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
