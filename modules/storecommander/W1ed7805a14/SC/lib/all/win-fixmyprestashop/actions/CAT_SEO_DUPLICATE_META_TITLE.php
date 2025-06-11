<?php
$post_action = Tools::getValue('action');
$action_name = 'CAT_SEO_DUPLICATE_META_TITLE';
$tab_title = _l('Same meta title');

if (!empty($post_action) && $post_action == 'do_check')
{
    $res = array();

    $langs = array();
    $sql = 'SELECT * FROM '._DB_PREFIX_.'lang';
    $tmps = Db::getInstance()->ExecuteS($sql);
    foreach ($tmps as $tmp)
    {
        $langs[$tmp['id_lang']] = $tmp['name'];
    }

    if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
    {
        $shops = array();
        $sql = 'SELECT * FROM '._DB_PREFIX_.'shop';
        $tmps = Db::getInstance()->ExecuteS($sql);
        foreach ($tmps as $tmp)
        {
            $shops[$tmp['id_shop']] = $tmp['name'];
        }

        $sql = 'SELECT pl1.*, ps.active, 
        (SELECT ps1.id_category_default FROM '._DB_PREFIX_.'product_shop ps1 WHERE pl1.id_product=ps1.id_product AND pl1.id_shop=ps1.id_shop) as id_category_default
            FROM '._DB_PREFIX_.'product_lang pl1
            '.(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? ' LEFT JOIN '._DB_PREFIX_.'product_shop ps ON (ps.id_product = pl1.id_product) ' : '')
            .' ,'._DB_PREFIX_.'product_lang pl2
            WHERE pl1.meta_title = pl2.meta_title
                AND pl1.id_product != pl2.id_product
                AND pl1.meta_title != ""
            GROUP BY pl1.id_product, pl1.id_lang, pl1.id_shop
           ORDER BY pl1.meta_title';
        $res = Db::getInstance()->ExecuteS($sql);
    }
    else
    {
        $sql = 'SELECT pl1.*, p.active, 
        (SELECT p1.id_category_default FROM '._DB_PREFIX_.'product p1 WHERE pl1.id_product=p1.id_product) as id_category_default
            FROM '._DB_PREFIX_.'product_lang pl1
            LEFT JOIN '._DB_PREFIX_.'product p ON (p.id_product = pl1.id_product)
            ,'._DB_PREFIX_.'product_lang pl2
            WHERE pl1.meta_title = pl1.meta_title
                AND pl1.id_product != pl2.id_product
            GROUP BY pl1.id_product, pl1.id_lang
           ORDER BY pl1.meta_title';
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

            var tbProductSameMetaTitle = dhxlSCExtCheck.tabbar.cells("table_<?php echo $action_name; ?>").attachToolbar();
            tbProductSameMetaTitle.setIconset('awesome');
            var idProductSameMetaTitle = '';
            tbProductSameMetaTitle.addButton("gotocatalog", 0, "", 'fad fa-external-link green', 'fad fa-external-link green');
            tbProductSameMetaTitle.setItemToolTip('gotocatalog','<?php echo _l('Go to the product in catalog.'); ?>');
            tbProductSameMetaTitle.addButton("exportcsv", 0, "", 'fad fa-file-csv green', 'fad fa-file-csv green');
            tbProductSameMetaTitle.setItemToolTip('exportcsv','<?php echo _l('Export grid to clipboard in CSV format for MSExcel with tab delimiter.'); ?>');
            tbProductSameMetaTitle.attachEvent("onClick",
                function(id){
                    if (id=='gotocatalog')
                    {
                        if(idProductSameMetaTitle !== '') {
                            var path = gridProductSameMetaTitle.getUserData(idProductSameMetaTitle, "path_pdt");
                            let url = "?page=cat_tree&open_cat_grid="+path;
                            window.open(url,'_blank');
                        }

                    }
                    if(id=='exportcsv') {
                        displayQuickExportWindow(gridProductSameMetaTitle,1);
                    }
                });

            var gridProductSameMetaTitle = dhxlSCExtCheck.tabbar.cells("table_<?php echo $action_name; ?>").attachGrid();
            gridProductSameMetaTitle.setImagePath("lib/js/imgs/");
            gridProductSameMetaTitle.enableSmartRendering(true);
            gridProductSameMetaTitle.enableMultiselect(false);

            gridProductSameMetaTitle.setHeader("ID <?php echo _l('product'); ?>,<?php echo _l('Active'); ?>,<?php echo _l('product'); ?>,<?php echo _l('lang'); ?><?php echo version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? ','._l('shop') : ''; ?>,meta_title");
            gridProductSameMetaTitle.setInitWidths("80,60,100,100<?php echo version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? ',100' : ''; ?>,*");
            gridProductSameMetaTitle.setColAlign("left,left,left,left<?php echo version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? ',left' : ''; ?>,left");
            gridProductSameMetaTitle.setColTypes("ro,ro,ro,ro<?php echo version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? ',ro' : ''; ?>,ro");
            gridProductSameMetaTitle.setColSorting("int,str,str,str<?php echo version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? ',str' : ''; ?>,str");
            gridProductSameMetaTitle.attachHeader("#numeric_filter,#select_filter,#text_filter,#select_filter<?php echo version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? ',#select_filter' : ''; ?>,#text_filter");
            gridProductSameMetaTitle.init();

            gridProductSameMetaTitle.attachEvent('onRowSelect',function(id){
                idProductSameMetaTitle = id;
            });

            var xml = '<rows>';
            <?php foreach ($res as $row)
            { ?>
            xml = xml+'   <row id="<?php echo $row['id_product'].'_'.$row['id_lang'].(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? '_'.$row['id_shop'] : ''); ?>">';
            xml = xml+'      <userdata name="path_pdt"><?php echo $row['id_category_default'].'-'.$row['id_product']; ?></userdata>';
            xml = xml+'      <cell><![CDATA[<?php echo $row['id_product']; ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php echo !empty($row['active']) ? _l('Yes') : _l('No'); ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php echo addslashes($row['name']); ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php echo addslashes($langs[$row['id_lang']]); ?>]]></cell>';
            <?php if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
            { ?>
            xml = xml+'      <cell><![CDATA[<?php echo addslashes($shops[$row['id_shop']]); ?>]]></cell>';
            <?php } ?>
            xml = xml+'      <cell><![CDATA[<?php echo addslashes($row['meta_title']); ?>]]></cell>';
            xml = xml+'   </row>';
            <?php } ?>
            xml = xml+'</rows>';
            gridProductSameMetaTitle.parse(xml);

            sbProductSameMetaTitle=dhxlSCExtCheck.tabbar.cells("table_<?php echo $action_name; ?>").attachStatusBar();
            function getGridStat_ProductSameMetaTitle(){
                var filteredRows=gridProductSameMetaTitle.getRowsNum();
                var selectedRows=(gridProductSameMetaTitle.getSelectedRowId()?gridProductSameMetaTitle.getSelectedRowId().split(',').length:0);
                sbProductSameMetaTitle.setText('<?php echo count($res).' '._l('Errors'); ?>'+" - <?php echo _l('Filter')._l(':'); ?> "+filteredRows+" - <?php echo _l('Selection')._l(':'); ?> "+selectedRows);
            }
            gridProductSameMetaTitle.attachEvent("onFilterEnd", function(elements){
                getGridStat_ProductSameMetaTitle();
            });
            gridProductSameMetaTitle.attachEvent("onSelectStateChanged", function(id){
                getGridStat_ProductSameMetaTitle();
            });
            getGridStat_ProductSameMetaTitle();
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
