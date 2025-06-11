<?php
$post_action = Tools::getValue('action');
$action_name = 'CAT_COMBI_DUPLICATE_REFERENCE';
$tab_title = _l('Combi. same ref.');

if (!empty($post_action) && $post_action == 'do_check')
{
    $res = array();
    $sql = 'SELECT reference, count(*) AS c
                FROM '._DB_PREFIX_.'product_attribute
                WHERE reference != ""
                GROUP BY reference
                HAVING c > 1
                ORDER BY c DESC';
    $ref_found = Db::getInstance()->ExecuteS($sql);
    if (!empty($ref_found))
    {
        $refs = array();
        foreach ($ref_found as $row)
        {
            $refs[] = $row['reference'];
        }
        $sql = 'SELECT pa.id_product_attribute,pa.id_product,'.(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? ' ps.id_category_default ' : ' p.id_category_default').',pa.reference,pl.name, p.active '.(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? ' , ps.active ' : '').'
                    FROM '._DB_PREFIX_.'product_attribute pa
                    LEFT JOIN '._DB_PREFIX_.'product p ON p.id_product = pa.id_product
                    LEFT JOIN '._DB_PREFIX_.'product_lang pl 
                        ON (pl.id_product = p.id_product AND pl.id_lang = '.(int) $id_lang.(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? ' AND pl.id_shop = p.id_shop_default' : '').')
                    '.(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? ' LEFT JOIN '._DB_PREFIX_.'product_shop ps ON (ps.id_product = p.id_product AND ps.id_shop = p.id_shop_default ) ' : '').'
                    WHERE pa.reference IN ("'.implode('","', $refs).'") 
                    ORDER BY pa.reference,p.id_product';
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

            var tbCombinationSameRef = dhxlSCExtCheck.tabbar.cells("table_<?php echo $action_name; ?>").attachToolbar();
            tbCombinationSameRef.setIconset('awesome');
            var idCombinationSameRef = '';
            tbCombinationSameRef.addButton("gotocatalog", 0, "", 'fa fa-sitemap', 'fa fa-sitemap');
            tbCombinationSameRef.setItemToolTip('gotocatalog','<?php echo _l('Go to the combination in catalog.'); ?>');
            tbCombinationSameRef.addButton("exportcsv", 0, "", 'fad fa-file-csv green', 'fad fa-file-csv green');
            tbCombinationSameRef.setItemToolTip('exportcsv','<?php echo _l('Export grid to clipboard in CSV format for MSExcel with tab delimiter.'); ?>');
            tbCombinationSameRef.attachEvent("onClick",
                function(id){
                    if (id=='gotocatalog')
                    {
                        if(idCombinationSameRef !== '') {
                            let url = "?page=cat_tree&open_cat_grid="+idCombinationSameRef;
                            window.open(url,'_blank');
                        }

                    }
                    if(id=='exportcsv') {
                        displayQuickExportWindow(gridCombinationSameRef,1);
                    }
                });

            var gridCombinationSameRef = dhxlSCExtCheck.tabbar.cells("table_<?php echo $action_name; ?>").attachGrid();
            gridCombinationSameRef.setImagePath("lib/js/imgs/");
            gridCombinationSameRef.enableSmartRendering(true);
            gridCombinationSameRef.enableMultiselect(false);

            gridCombinationSameRef.setHeader("ID <?php echo _l('product'); ?>,ID <?php echo _l('combination'); ?>,<?php echo _l('Reference'); ?>,<?php echo _l('Active'); ?>,<?php echo _l('Product name'); ?>,<?php echo _l('Combination name'); ?>");
            gridCombinationSameRef.setInitWidths("100,100,100,60,200,200");
            gridCombinationSameRef.setColAlign("left,left,left,left,left,left");
            gridCombinationSameRef.setColTypes("ro,ro,ro,ro,ro,ro");
            gridCombinationSameRef.setColSorting("int,int,str,str,str,str");
            gridCombinationSameRef.attachHeader("#numeric_filter,#numeric_filter,#text_filter,#select_filter,#text_filter,#text_filter");
            gridCombinationSameRef.init();

            gridCombinationSameRef.attachEvent('onRowSelect',function(id){
                idCombinationSameRef = id;
            });

            var xml = '<rows>';
            <?php foreach ($res as $row) { ?>
            xml = xml+'   <row id="<?php echo $row['id_category_default'].'-'.$row['id_product'].'-'.$row['id_product_attribute']; ?>">';
            xml = xml+'      <cell><![CDATA[<?php echo $row['id_product']; ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php echo $row['id_product_attribute']; ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php echo $row['reference']; ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php echo !empty($row['active']) ? _l('Yes') : _l('No'); ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php echo addslashes($row['name']); ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php echo array_key_exists($row['id_product_attribute'], $attr_name) ? addslashes($attr_name[$row['id_product_attribute']]) : ''; ?>]]></cell>';
            xml = xml+'   </row>';
            <?php } ?>
            xml = xml+'</rows>';
            gridCombinationSameRef.parse(xml);

            sbCombinationSameRef=dhxlSCExtCheck.tabbar.cells("table_<?php echo $action_name; ?>").attachStatusBar();
            function getGridStat_CombinationSameRef(){
                var filteredRows=gridCombinationSameRef.getRowsNum();
                var selectedRows=(gridCombinationSameRef.getSelectedRowId()?gridCombinationSameRef.getSelectedRowId().split(',').length:0);
                sbCombinationSameRef.setText('<?php echo count($res).' '._l('Errors'); ?>'+" - <?php echo _l('Filter')._l(':'); ?> "+filteredRows+" - <?php echo _l('Selection')._l(':'); ?> "+selectedRows);
            }
            gridCombinationSameRef.attachEvent("onFilterEnd", function(elements){
                getGridStat_CombinationSameRef();
            });
            gridCombinationSameRef.attachEvent("onSelectStateChanged", function(id){
                getGridStat_CombinationSameRef();
            });
            getGridStat_CombinationSameRef();
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
