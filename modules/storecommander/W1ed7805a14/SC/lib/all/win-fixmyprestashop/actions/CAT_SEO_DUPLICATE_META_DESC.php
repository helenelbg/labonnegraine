<?php
$post_action = Tools::getValue('action');
$action_name = 'CAT_SEO_DUPLICATE_META_DESC';
$tab_title = _l('Same meta desc');

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
            WHERE pl1.meta_description = pl2.meta_description
                AND pl1.id_product != pl2.id_product
                AND pl1.meta_description != ""
            GROUP BY pl1.id_product, pl1.id_lang, pl1.id_shop
           ORDER BY pl1.meta_description';
        $res = Db::getInstance()->ExecuteS($sql);
    }
    else
    {
        $sql = 'SELECT pl1.*, p.active, 
        (SELECT p1.id_category_default FROM '._DB_PREFIX_.'product p1 WHERE pl1.id_product=p1.id_product) as id_category_default
            FROM '._DB_PREFIX_.'product_lang pl1
            LEFT JOIN '._DB_PREFIX_.'product p ON (p.id_product = pl1.id_product)
            ,'._DB_PREFIX_.'product_lang pl2
            WHERE pl1.meta_description = pl1.meta_description
                AND pl1.id_product != pl2.id_product
            GROUP BY pl1.id_product, pl1.id_lang
           ORDER BY pl1.meta_description';
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

            var tbProductSameMetaDesc = dhxlSCExtCheck.tabbar.cells("table_<?php echo $action_name; ?>").attachToolbar();
            tbProductSameMetaDesc.setIconset('awesome');
            var idProductSameMetaDesc = '';
            tbProductSameMetaDesc.addButton("gotocatalog", 0, "", 'fad fa-external-link green', 'fad fa-external-link green');
            tbProductSameMetaDesc.setItemToolTip('gotocatalog','<?php echo _l('Go to the product in catalog.'); ?>');
            tbProductSameMetaDesc.addButton("exportcsv", 0, "", 'fad fa-file-csv green', 'fad fa-file-csv green');
            tbProductSameMetaDesc.setItemToolTip('exportcsv','<?php echo _l('Export grid to clipboard in CSV format for MSExcel with tab delimiter.'); ?>');
            tbProductSameMetaDesc.attachEvent("onClick",
                function(id){
                    if (id=='gotocatalog')
                    {
                        if(idProductSameMetaDesc !== '') {
                            var path = gridProductSameMetaDesc.getUserData(idProductSameMetaDesc, "path_pdt");
                            let url = "?page=cat_tree&open_cat_grid="+path;
                            window.open(url,'_blank');
                        }

                    }
                    if(id=='exportcsv') {
                        displayQuickExportWindow(gridProductSameMetaDesc,1);
                    }
                });

            var gridProductSameMetaDesc = dhxlSCExtCheck.tabbar.cells("table_<?php echo $action_name; ?>").attachGrid();
            gridProductSameMetaDesc.setImagePath("lib/js/imgs/");
            gridProductSameMetaDesc.enableSmartRendering(true);
            gridProductSameMetaDesc.enableMultiselect(false);

            gridProductSameMetaDesc.setHeader("ID <?php echo _l('product'); ?>,<?php echo _l('Active'); ?>,<?php echo _l('product'); ?>,<?php echo _l('lang'); ?><?php echo version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? ','._l('shop') : ''; ?>,meta_description");
            gridProductSameMetaDesc.setInitWidths("80,60,100,100<?php echo version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? ',100' : ''; ?>,*");
            gridProductSameMetaDesc.setColAlign("left,left,left,left<?php echo version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? ',left' : ''; ?>,left");
            gridProductSameMetaDesc.setColTypes("ro,ro,ro,ro<?php echo version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? ',ro' : ''; ?>,ro");
            gridProductSameMetaDesc.setColSorting("int,str,str,str<?php echo version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? ',str' : ''; ?>,str");
            gridProductSameMetaDesc.attachHeader("#numeric_filter,#select_filter,#text_filter,#select_filter<?php echo version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? ',#select_filter' : ''; ?>,#text_filter");
            gridProductSameMetaDesc.init();

            gridProductSameMetaDesc.attachEvent('onRowSelect',function(id){
                idProductSameMetaDesc = id;
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
            xml = xml+'      <cell><![CDATA[<?php echo str_replace(array("\r", "\n", "\r\n"), '', trim(addslashes($row['meta_description']))); ?>]]></cell>';
            xml = xml+'   </row>';
            <?php } ?>
            xml = xml+'</rows>';
            gridProductSameMetaDesc.parse(xml);

            sbProductSameMetaDesc=dhxlSCExtCheck.tabbar.cells("table_<?php echo $action_name; ?>").attachStatusBar();
            function getGridStat_ProductSameMetaDesc(){
                var filteredRows=gridProductSameMetaDesc.getRowsNum();
                var selectedRows=(gridProductSameMetaDesc.getSelectedRowId()?gridProductSameMetaDesc.getSelectedRowId().split(',').length:0);
                sbProductSameMetaDesc.setText('<?php echo count($res).' '._l('Errors'); ?>'+" - <?php echo _l('Filter')._l(':'); ?> "+filteredRows+" - <?php echo _l('Selection')._l(':'); ?> "+selectedRows);
            }
            gridProductSameMetaDesc.attachEvent("onFilterEnd", function(elements){
                getGridStat_ProductSameMetaDesc();
            });
            gridProductSameMetaDesc.attachEvent("onSelectStateChanged", function(id){
                getGridStat_ProductSameMetaDesc();
            });
            getGridStat_ProductSameMetaDesc();
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
