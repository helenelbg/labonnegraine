<?php
$post_action = Tools::getValue('action');
$action_name = 'CAT_PRODUCT_DUPLICATE_NAME';
$tab_title = _l('Products same name');

if (!empty($post_action) && $post_action == 'do_check')
{
    $name_found = array();
    $res = array();
    $shops = array();
    $langs = array();
    $tmpl = Language::getLanguages(false);
    $langs = array_column($tmpl,'iso_code','id_lang');

    if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
    {
        $tmps = SCI::getAllShops();
        $shops = array_column($tmps,'name','id_shop');

        $sql = 'SELECT pl.id_product, pl.name, pl.id_lang, pl.id_shop,  
                CONCAT(pl.name, " " ,pl.id_lang, " " ,pl.id_shop) AS concat
            FROM `'._DB_PREFIX_.'product` p
            INNER JOIN `'._DB_PREFIX_.'product_lang` pl
            ON (p.id_product = pl.id_product)
            WHERE name != ""
            GROUP BY concat
            HAVING count(*) > 1
            ORDER BY concat DESC';
    }
    else
    {
        $sql = 'SELECT pl.id_product, pl.name, pl.id_lang, 
                CONCAT(pl.name, " " ,pl.id_lang) AS concat
            FROM `'._DB_PREFIX_.'product` p
            INNER JOIN `'._DB_PREFIX_.'product_lang` pl
            ON (p.id_product = pl.id_product)
            WHERE name != ""
            AND pl.id_lang= '.(int) $lang['id_lang'].'
            GROUP BY concat
            HAVING count(*) > 1
            ORDER BY c DESC';
    }
    $name_found = Db::getInstance()->ExecuteS($sql);

    // Get all products rows for each name duplicates
    if (!empty($name_found))
    {
        $name = array();
        foreach ($name_found as $row)
        {
            if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
            {
                $sql = 'SELECT p.id_product,pl.name, pl.id_lang, pl.id_shop, ps.active, ps.id_category_default
                    FROM `'._DB_PREFIX_.'product` p
                    LEFT JOIN `'._DB_PREFIX_.'product_lang` pl
                    ON (pl.id_product = p.id_product AND pl.id_lang = '.(int) $row['id_lang'].' AND pl.id_shop = '.(int) $row['id_shop'].')
                    INNER JOIN `'._DB_PREFIX_.'product_shop` ps
                    ON (ps.id_product = p.id_product AND ps.id_shop = '.(int) $row['id_shop'].')
                    WHERE pl.name = "'.$row['name'].'"
                    ORDER BY pl.name
                    LIMIT 1500';
            }
            else
            {
                $sql = 'SELECT p.id_product,pl.name, pl.id_lang, p.active, p.id_category_default
                    FROM `'._DB_PREFIX_.'product` p
                    LEFT JOIN `'._DB_PREFIX_.'product_lang` pl
                    ON (pl.id_product = p.id_product AND pl.id_lang = '.(int) $row['id_lang'].')
                    WHERE pl.name = "'.$row['name'].'"
                    ORDER BY pl.name
                    LIMIT 1500';
            }
            $res = array_merge($res, Db::getInstance()->ExecuteS($sql));
        }
    }

    $content = '';
    $content_js = '';
    $results = 'OK';
    if (!empty($name_found) && count($res) > 0)
    {
        $results = 'KO';
        ob_start(); ?>
        <script type="text/javascript">

            var tbProductSameName = dhxlSCExtCheck.tabbar.cells("table_<?php echo $action_name; ?>").attachToolbar();
            tbProductSameName.setIconset('awesome');
            var idShopProductSameName = 0;
            var idProductSameName = '';
            tbProductSameName.addButton("gotocatalog", 0, "", 'fad fa-external-link green', 'fad fa-external-link green');
            tbProductSameName.setItemToolTip('gotocatalog','<?php echo _l('Go to the product in catalog.'); ?>');
            tbProductSameName.addButton("exportcsv", 0, "", 'fad fa-file-csv green', 'fad fa-file-csv green');
            tbProductSameName.setItemToolTip('exportcsv','<?php echo _l('Export grid to clipboard in CSV format for MSExcel with tab delimiter.'); ?>');
            tbProductSameName.attachEvent("onClick",
                function(id){
                    if (id=='gotocatalog')
                    {
                        if(idProductSameName !== '') {
                            let url = "?page=cat_tree&open_cat_grid="+idProductSameName+"&only_shop="+idShopProductSameName;
                            window.open(url,'_blank');
                        }

                    }
                    if(id=='exportcsv') {
                        displayQuickExportWindow(gridProductSameName,1);
                    }
                });

            var gridProductSameName = dhxlSCExtCheck.tabbar.cells("table_<?php echo $action_name; ?>").attachGrid();
            gridProductSameName.setImagePath("lib/js/imgs/");
            gridProductSameName.enableSmartRendering(true);
            gridProductSameName.enableMultiselect(false);

            gridProductSameName.setHeader("ID <?php echo _l('product'); ?>,<?php echo _l('Name'); ?><?php if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) echo ","._l('Shop'); ?>,<?php echo _l('lang'); ?>,<?php echo _l('Active'); ?>");
            gridProductSameName.setInitWidths("100,100<?php echo version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? ',60' : ''; ?>,60,60");
            gridProductSameName.setColAlign("left,left<?php echo version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? ',left' : ''; ?>,left,left");
            gridProductSameName.setColTypes("ro,ro<?php echo version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? ',ro' : ''; ?>,ro,ro");
            gridProductSameName.setColSorting("int,str<?php echo version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? ',int' : ''; ?>,int,int");
            gridProductSameName.attachHeader("#numeric_filter,#text_filter<?php echo version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? ',#select_filter' : ''; ?>,#select_filter,#select_filter");
            gridProductSameName.init();

            gridProductSameName.attachEvent('onRowSelect',function(id){
                let array_ids=id.split('-');
                idProductSameName = array_ids[0]+'-'+array_ids[1];
                idShopProductSameName =  array_ids[3];
            });

            var xml = '<rows>';
            <?php foreach ($res as $row)
            { ?>
            xml = xml+'   <row id="<?php echo $row['id_category_default'].'-'.$row['id_product'].'-'.$row['id_lang'].'-'.$row['id_shop']; ?>">';
            xml = xml+'      <cell><![CDATA[<?php echo $row['id_product']; ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php echo addslashes($row['name']); ?>]]></cell>';
            <?php if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) { ?>
            xml = xml+'      <cell><![CDATA[<?php echo addslashes($shops[$row['id_shop']]); ?>]]></cell>';
            <?php } ?>
            xml = xml+'      <cell><![CDATA[<?php echo addslashes($langs[$row['id_lang']]); ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php echo !empty($row['active']) ? _l('Yes') : _l('No'); ?>]]></cell>';
            xml = xml+'   </row>';
            <?php } ?>
            xml = xml+'</rows>';
            gridProductSameName.parse(xml);

            sbProductSameName=dhxlSCExtCheck.tabbar.cells("table_<?php echo $action_name; ?>").attachStatusBar();
            function getGridStat_ProductSameName(){
                var filteredRows=gridProductSameName.getRowsNum();
                var selectedRows=(gridProductSameName.getSelectedRowId()?gridProductSameName.getSelectedRowId().split(',').length:0);
                sbProductSameName.setText('<?php echo count($res).' '._l('Errors'); ?>'+" - <?php echo _l('Filter')._l(':'); ?> "+filteredRows+" - <?php echo _l('Selection')._l(':'); ?> "+selectedRows);
            }
            gridProductSameName.attachEvent("onFilterEnd", function(elements){
                getGridStat_ProductSameName();
            });
            gridProductSameName.attachEvent("onSelectStateChanged", function(id){
                getGridStat_ProductSameName();
            });
            getGridStat_ProductSameName();
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
