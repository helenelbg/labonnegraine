<?php
$post_action = Tools::getValue('action');
$action_name = 'CAT_PROD_DUPLICATE_COMBINATION';
$tab_title = _l('P/Combi. duplicate');

if (!empty($post_action) && $post_action == 'do_check')
{
    $res = array();
    $sql = 'SELECT COUNT(*) AS dbl, '.(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? ' ps.id_category_default ' : ' p.id_category_default').', pa.id_product_attribute, 
                    pa.id_product, '.(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? 'ps.active ' : 'p.active').', (
                        SELECT GROUP_CONCAT(pal.name ORDER BY ag.position ASC,a.position ASC SEPARATOR "   ")
                        FROM '._DB_PREFIX_.'product_attribute_combination pac
                            LEFT JOIN '._DB_PREFIX_.'attribute a ON a.id_attribute = pac.id_attribute
                            LEFT JOIN '._DB_PREFIX_.'attribute_group ag ON ag.id_attribute_group = a.id_attribute_group
                            LEFT JOIN '._DB_PREFIX_.'attribute_lang pal ON pal.id_attribute = pac.id_attribute AND pal.id_lang = '.(int) $id_lang.'
                            WHERE pac.id_product_attribute = pa.id_product_attribute
                            GROUP BY pac.id_product_attribute
                        ) AS attr_list
            FROM '._DB_PREFIX_.'product_attribute pa
            LEFT JOIN '._DB_PREFIX_.'product p ON pa.id_product = p.id_product
            '.(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? ' LEFT JOIN '._DB_PREFIX_.'product_shop ps ON (ps.id_product = p.id_product AND ps.id_shop = p.id_shop_default )' : '').'
            GROUP BY id_product, attr_list
            HAVING dbl > 1
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

            let tbProdDuplicateCombination = dhxlSCExtCheck.tabbar.cells("table_<?php echo $action_name; ?>").attachToolbar();
            tbProdDuplicateCombination.setIconset('awesome');
            let idProdDuplicateCombination = '';
            tbProdDuplicateCombination.addButton("selectall", 0, "", 'fa fa-bolt yellow', 'fa fa-bolt yellow');
            tbProdDuplicateCombination.setItemToolTip('selectall','<?php echo _l('Select all'); ?>');
            tbProdDuplicateCombination.addButton("gotoCombicatalog", 0, "", 'fa fa-sitemap', 'fa fa-sitemap');
            tbProdDuplicateCombination.setItemToolTip('gotoCombicatalog','<?php echo _l('Go to the combination in catalog.'); ?>');
            tbProdDuplicateCombination.addButton("gotocatalog", 0, "", 'fad fa-external-link green', 'fad fa-external-link green');
            tbProdDuplicateCombination.setItemToolTip('gotocatalog','<?php echo _l('Go to the product in catalog.'); ?>');
            tbProdDuplicateCombination.addButton("exportcsv", 0, "", 'fad fa-file-csv green', 'fad fa-file-csv green');
            tbProdDuplicateCombination.setItemToolTip('exportcsv','<?php echo _l('Export grid to clipboard in CSV format for MSExcel with tab delimiter.'); ?>');

            tbProdDuplicateCombination.attachEvent("onClick", function(id){
                switch(id){
                    case 'gotocatalog':
                        if(idProdDuplicateCombination !== '') {
                            let path = gridProdDuplicateCombination.getUserData(idProdDuplicateCombination, "path_pdt");
                            let url = "?page=cat_tree&open_cat_grid="+path;
                            window.open(url,'_blank');
                        }
                        break;
                    case 'gotoCombicatalog':
                        if(idProdDuplicateCombination !== '') {
                            let path = gridProdDuplicateCombination.getUserData(idProdDuplicateCombination, "path_combi");
                            let url = "?page=cat_tree&open_cat_grid="+path;
                            window.open(url,'_blank');
                        }
                        break;
                    case 'exportcsv':
                        displayQuickExportWindow(gridProdDuplicateCombination,1);
                        break;
                    case 'selectall':
                        gridProdDuplicateCombination.selectAll();
                        break;
                }
            });

            let gridProdDuplicateCombination = dhxlSCExtCheck.tabbar.cells("table_<?php echo $action_name; ?>").attachGrid();
            gridProdDuplicateCombination.setImagePath("lib/js/imgs/");
            gridProdDuplicateCombination.enableSmartRendering(true);
            gridProdDuplicateCombination.enableMultiselect(true);

            gridProdDuplicateCombination.setHeader("ID <?php echo _l('product'); ?>,<?php echo _l('Combinations detail'); ?>,<?php echo _l('Active'); ?>");
            gridProdDuplicateCombination.setInitWidths("80,*,60");
            gridProdDuplicateCombination.setColAlign("left,left,left");
            gridProdDuplicateCombination.setColTypes("ro,ro,ro");
            gridProdDuplicateCombination.setColSorting("int,str,str");
            gridProdDuplicateCombination.attachHeader("#numeric_filter,#text_filter,#select_filter");
            gridProdDuplicateCombination.init();

            gridProdDuplicateCombination.attachEvent('onRowSelect',function(id){
                idProdDuplicateCombination = id;
            });

            let xml = '<rows>';
            <?php foreach ($res as $row)
            { ?>
            xml = xml+'    <row id="<?php echo $row['id_product'].'_'.$row['id_product_attribute']; ?>">';
            xml = xml+'        <userdata name="path_pdt"><?php echo $row['id_category_default'].'-'.$row['id_product']; ?></userdata>';
            xml = xml+'        <userdata name="path_combi"><?php echo $row['id_category_default'].'-'.$row['id_product'].'-'.$row['id_product_attribute']; ?></userdata>';
            xml = xml+'        <cell><![CDATA[<?php echo $row['id_product']; ?>]]></cell>';
            xml = xml+'        <cell><![CDATA[<?php echo addslashes($row['attr_list']); ?>]]></cell>';
            xml = xml+'        <cell><![CDATA[<?php echo !empty($row['active']) ? _l('Yes') : _l('No'); ?>]]></cell>';
            xml = xml+'    </row>';
            <?php } ?>
            xml = xml+'</rows>';
            gridProdDuplicateCombination.parse(xml);

            sbProdDuplicateCombination=dhxlSCExtCheck.tabbar.cells("table_<?php echo $action_name; ?>").attachStatusBar();
            function getGridStat_ProdDuplicateCombination(){
                let filteredRows=gridProdDuplicateCombination.getRowsNum();
                let selectedRows=(gridProdDuplicateCombination.getSelectedRowId()?gridProdDuplicateCombination.getSelectedRowId().split(',').length:0);
                sbProdDuplicateCombination.setText('<?php echo count($res).' '._l('Errors'); ?>'+" - <?php echo _l('Filter')._l(':'); ?> "+filteredRows+" - <?php echo _l('Selection')._l(':'); ?> "+selectedRows);
            }
            gridProdDuplicateCombination.attachEvent("onFilterEnd", function(elements){
                getGridStat_ProdDuplicateCombination();
            });
            gridProdDuplicateCombination.attachEvent("onSelectStateChanged", function(id){
                getGridStat_ProdDuplicateCombination();
            });
            getGridStat_ProdDuplicateCombination();
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
