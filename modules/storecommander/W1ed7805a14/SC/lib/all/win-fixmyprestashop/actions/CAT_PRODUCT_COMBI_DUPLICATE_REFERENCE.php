<?php
$post_action = Tools::getValue('action');
$action_name = 'CAT_PRODUCT_COMBI_DUPLICATE_REFERENCE';
$tab_title = _l('P/Combi. same Ref.');
$itemTitle = _l('Reference');
$itemToFind = 'reference';

if (!empty($post_action) && $post_action == 'do_check')
{
    $sql = 'SELECT `'.bqSQL($itemToFind).'`, COUNT(*) as occurences
                FROM (
                    SELECT `'.bqSQL($itemToFind).'` 
                    FROM '._DB_PREFIX_.'product 
                    WHERE `'.bqSQL($itemToFind).'` IS NOT NULL 
                        AND `'.bqSQL($itemToFind).'` <> "" 
                    UNION ALL
                    SELECT `'.bqSQL($itemToFind).'` 
                    FROM '._DB_PREFIX_.'product_attribute 
                    WHERE `'.bqSQL($itemToFind).'` IS NOT NULL 
                        AND `'.bqSQL($itemToFind).'` <> ""
                ) as temp
                GROUP BY `'.bqSQL($itemToFind).'`
                HAVING occurences > 1
                ORDER BY occurences DESC
                LIMIT 1500';
    $itemFound = Db::getInstance()->executes($sql);

    if($itemFound)
    {
        $itemFoundList = array_column($itemFound,$itemToFind);
        $itemFoundListOccur = array_column($itemFound,'occurences',$itemToFind);
        $productsItem = Db::getInstance()->executeS('SELECT `'.bqSQL($itemToFind).'` 
                                                                FROM '._DB_PREFIX_.'product 
                                                                WHERE `'.bqSQL($itemToFind).'` IN ("'.implode('","',$itemFoundList).'")');
        $productAttributesItem = Db::getInstance()->executeS('SELECT `'.bqSQL($itemToFind).'`
                                                                    FROM ' . _DB_PREFIX_ . 'product_attribute 
                                                                    WHERE `'.bqSQL($itemToFind).'` IN ("' . implode('","', $itemFoundList) . '")');
        ## bloc nécessaire pour n'avoir que les doublons ENTRE les deux tables
        if($productsItem && $productAttributesItem) {
            $productArrayCol = array_column($productsItem,null,$itemToFind);
            $productAttributeArrayCol = array_column($productAttributesItem,null,$itemToFind);
            $finalList = array_keys(array_intersect_key($productAttributeArrayCol, $productArrayCol));
            $finalListOccurences = array();
            foreach($finalList as $item)
            {
                $finalListOccurences[$item] = $itemFoundListOccur[$item];
            }
            arsort($finalListOccurences);
            $itemFound=$finalListOccurences;
        } else {
            $itemFound = false;
        }
    }

    $content = '';
    $content_js = '';
    $results = 'OK';
    if (!empty($itemFound))
    {
        $results = 'KO';
        ob_start(); ?>
        <script type="text/javascript">
            let rowsProductCombiSameREFERENCE = dhxlSCExtCheck.tabbar.cells("table_<?php echo $action_name; ?>").attachLayout("2E");

            let occurencesProductCombiSameREFERENCE_layout = rowsProductCombiSameREFERENCE.cells('a');
            occurencesProductCombiSameREFERENCE_layout.setText('<?php echo _l('Results'); ?>');

            let detailProductCombiSameREFERENCE_layout = rowsProductCombiSameREFERENCE.cells('b');
            detailProductCombiSameREFERENCE_layout.setText('<?php echo _l('Details of the selected rows'); ?>');

            // results
            let tbProductCombiSameREFERENCE = occurencesProductCombiSameREFERENCE_layout.attachToolbar();
            tbProductCombiSameREFERENCE.setIconset('awesome');
            tbProductCombiSameREFERENCE.addButton("exportcsv", 0, "", 'fad fa-file-csv green', 'fad fa-file-csv green');
            tbProductCombiSameREFERENCE.setItemToolTip('exportcsv','<?php echo _l('Export grid to clipboard in CSV format for MSExcel with tab delimiter.'); ?>');
            tbProductCombiSameREFERENCE.addButton("selectAll", 0, "", 'fa fa-bolt yellow', 'fa fa-bolt yellow');
            tbProductCombiSameREFERENCE.setItemToolTip('selectAll','<?php echo _l('Export grid to clipboard in CSV format for MSExcel with tab delimiter.'); ?>');
            tbProductCombiSameREFERENCE.attachEvent("onClick", function(id){
                switch (id) {
                    case 'exportcsv':
                        displayQuickExportWindow(gridDetailsProductCombiSameREFERENCE, 1);
                        break;
                    case 'selectAll':
                        gridProductCombiSameREFERENCE.selectAll();
                        getSbProductCombiSameREFERENCE();
                        displayGridDetailsProductCombiSameREFERENCE()
                        break;
                }
            });

            let gridProductCombiSameREFERENCE = occurencesProductCombiSameREFERENCE_layout.attachGrid();
            gridProductCombiSameREFERENCE.setImagePath("lib/js/imgs/");
            gridProductCombiSameREFERENCE.enableSmartRendering(true);
            gridProductCombiSameREFERENCE.enableMultiselect(true);

            gridProductCombiSameREFERENCE.setHeader("<?php echo $itemTitle ?>,<?php echo _l('Occurences found'); ?>");
            gridProductCombiSameREFERENCE.setInitWidths("250,*");
            gridProductCombiSameREFERENCE.setColAlign("left,left");
            gridProductCombiSameREFERENCE.setColTypes("ro,ro");
            gridProductCombiSameREFERENCE.setColSorting("str,int");
            gridProductCombiSameREFERENCE.attachHeader("#text_filter,#numeric_filter");
            gridProductCombiSameREFERENCE.init();

            gridProductCombiSameREFERENCE.attachEvent("onRowSelect", function(){
                displayGridDetailsProductCombiSameREFERENCE();
            });

            let referenceXml = '<rows>';
            <?php foreach ($itemFound as $item => $occurences) { ?>
            referenceXml = referenceXml+'   <row id="<?php echo $item; ?>">';
            referenceXml = referenceXml+'      <cell><![CDATA[<?php echo $item; ?>]]></cell>';
            referenceXml = referenceXml+'      <cell><![CDATA[<?php echo (int) $occurences; ?>]]></cell>';
            referenceXml = referenceXml+'   </row>';
            <?php } ?>
            referenceXml = referenceXml+'</rows>';
            gridProductCombiSameREFERENCE.parse(referenceXml);

            sbProductCombiSameREFERENCE=occurencesProductCombiSameREFERENCE_layout.attachStatusBar();
            function getSbProductCombiSameREFERENCE(){
                let filteredRows=gridProductCombiSameREFERENCE.getRowsNum();
                let selectedRows=(gridProductCombiSameREFERENCE.getSelectedRowId()?gridProductCombiSameREFERENCE.getSelectedRowId().split(',').length:0);
                sbProductCombiSameREFERENCE.setText('<?php echo count($itemFound).' '._l('Errors'); ?>'+" - <?php echo _l('Filter')._l(':'); ?> "+filteredRows+" - <?php echo _l('Selection')._l(':'); ?> "+selectedRows);
            }
            gridProductCombiSameREFERENCE.attachEvent("onFilterEnd", function(elements){
                getSbProductCombiSameREFERENCE();
            });
            gridProductCombiSameREFERENCE.attachEvent("onSelectStateChanged", function(id){
                getSbProductCombiSameREFERENCE();
            });
            getSbProductCombiSameREFERENCE();

            ////

            // details
            let tbDetailProductCombiSameREFERENCE = detailProductCombiSameREFERENCE_layout.attachToolbar();
            tbDetailProductCombiSameREFERENCE.setIconset('awesome');
            var lastIdProductCombiSameREFERENCE = '';
            tbDetailProductCombiSameREFERENCE.addButton("gotoCombicatalog", 0, "", 'fa fa-sitemap', 'fa fa-sitemap');
            tbDetailProductCombiSameREFERENCE.setItemToolTip('gotoCombicatalog','<?php echo _l('Go to the combination in catalog.'); ?>');
            tbDetailProductCombiSameREFERENCE.addButton("gotocatalog", 0, "", 'fad fa-external-link green', 'fad fa-external-link green');
            tbDetailProductCombiSameREFERENCE.setItemToolTip('gotocatalog','<?php echo _l('Go to the product in catalog.'); ?>');
            tbDetailProductCombiSameREFERENCE.addButton("exportcsv", 0, "", 'fad fa-file-csv green', 'fad fa-file-csv green');
            tbDetailProductCombiSameREFERENCE.setItemToolTip('exportcsv','<?php echo _l('Export grid to clipboard in CSV format for MSExcel with tab delimiter.'); ?>');
            tbDetailProductCombiSameREFERENCE.attachEvent("onClick", function(id){
                switch (id) {
                    case 'gotocatalog':
                        if (lastIdProductCombiSameREFERENCE !== '') {
                            let path = gridDetailsProductCombiSameREFERENCE.getUserData(lastIdProductCombiSameREFERENCE, "path_pdt");
                            let url = "?page=cat_tree&open_cat_grid=" + path;
                            window.open(url, '_blank');
                        }
                        break;
                    case 'gotoCombicatalog':
                        if (lastIdProductCombiSameREFERENCE !== '') {
                            let path = gridDetailsProductCombiSameREFERENCE.getUserData(lastIdProductCombiSameREFERENCE, "path_combi");
                            let url = "?page=cat_tree&open_cat_grid=" + path;
                            window.open(url, '_blank');
                        }
                        break;
                    case 'exportcsv':
                        displayQuickExportWindow(gridDetailsProductCombiSameREFERENCE, 1);
                        break;
                }
            });


            let gridDetailsProductCombiSameREFERENCE = detailProductCombiSameREFERENCE_layout.attachGrid();
            gridDetailsProductCombiSameREFERENCE.setImagePath("lib/js/imgs/");
            gridDetailsProductCombiSameREFERENCE.enableSmartRendering(true);
            gridDetailsProductCombiSameREFERENCE.enableMultiselect(false);

            gridDetailsProductCombiSameREFERENCE.setHeader("ID <?php echo _l('product'); ?>,<?php echo _l('Active'); ?>,<?php echo _l('Product name'); ?>,<?php echo $itemTitle ?>,ID <?php echo _l('Combination'); ?>,<?php echo _l('Active'); ?>,<?php echo _l('Product name'); ?>,<?php echo _l('Combination name'); ?>");
            gridDetailsProductCombiSameREFERENCE.setInitWidths("100,60,200,100,100,60,100,200");
            gridDetailsProductCombiSameREFERENCE.setColAlign("left,left,left,left,left,left,left,left");
            gridDetailsProductCombiSameREFERENCE.setColTypes("ro,ro,ro,ro,ro,ro,ro,ro");
            gridDetailsProductCombiSameREFERENCE.setColSorting("int,str,str,str,int,str,str,str");
            gridDetailsProductCombiSameREFERENCE.attachHeader("#numeric_filter,#select_filter,#text_filter,#text_filter,#numeric_filter,#select_filter,#text_filter,#text_filter");
            gridDetailsProductCombiSameREFERENCE.init();

            function displayGridDetailsProductCombiSameREFERENCE()
            {
                let selection = gridProductCombiSameREFERENCE.getSelectedRowId();
                if(!selection)
                {
                    return;
                }
                gridDetailsProductCombiSameREFERENCE.clearAll();
                $.post("index.php?ajax=1&act=all_win-fixmyprestashop_actions&check=<?php echo $action_name; ?>", {
                    action: "detail_get",
                    id_lang:SC_ID_LANG,
                    list: selection,
                }, function(data){
                    if(data !== '')
                    {
                        gridDetailsProductCombiSameREFERENCE.parse(data);
                    }
                });
            }

            gridDetailsProductCombiSameREFERENCE.attachEvent('onRowSelect',function(id){
                lastIdProductCombiSameREFERENCE = id;
            });

            displayGridDetailsProductCombiSameREFERENCE();


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
elseif (!empty($post_action) && $post_action == 'detail_get')
{
    $itemLis = Tools::getValue('list');
    $explodedList = explode(',',$itemLis);
    $id_lang = (int)Tools::getValue('id_lang');
    $return = '';
    $attr_name = $finalArray = array();
    if (isset($itemLis))
    {
        $productSql = 'SELECT p.`'.bqSQL($itemToFind).'`, p.id_product, pl.name, 
                                '.(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? "ps" : 'p').'.active, 
                                '.(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? 'ps' : 'p').'.id_category_default
                        FROM ' . _DB_PREFIX_ . 'product p 
                        '.(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? 'LEFT JOIN ' . _DB_PREFIX_ . 'product_shop ps ON (ps.id_product = p.id_product AND ps.id_shop = p.id_shop_default)' : '').'
                        LEFT JOIN ' . _DB_PREFIX_ . 'product_lang pl ON (pl.id_product = p.id_product AND pl.id_lang = '.(int)$id_lang. (version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? ' AND pl.id_shop = p.id_shop_default' : '') . ')
                        WHERE p.`'.bqSQL($itemToFind).'` IN ("'.implode('","',explode(',',pSQL($itemLis))).'")';
        $products = Db::getInstance()->executeS($productSql);
        if(empty($products))
        {
            die($return);
        }
        $productAttributeSql = 'SELECT pa.`'.bqSQL($itemToFind).'`, pa.id_product, pa.id_product_attribute, pl.name, 
                                    '.(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? "ps" : 'p').'.active, 
                                    '.(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? 'ps' : 'p').'.id_category_default
                                FROM ' . _DB_PREFIX_ . 'product_attribute pa
                                LEFT JOIN ' . _DB_PREFIX_ . 'product p ON (p.id_product = pa.id_product)
                                '.(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? 'LEFT JOIN ' . _DB_PREFIX_ . 'product_shop ps ON (ps.id_product = p.id_product AND ps.id_shop = p.id_shop_default)' : '').'
                                LEFT JOIN ' . _DB_PREFIX_ . 'product_lang pl ON (pl.id_product = p.id_product AND pl.id_lang = '.(int)$id_lang . (version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? ' AND pl.id_shop = p.id_shop_default' : '') . ')
                                WHERE pa.`'.bqSQL($itemToFind).'` IN ("'.implode('","',explode(',',pSQL($itemLis))).'")';
        $productAttributes = Db::getInstance()->executeS($productAttributeSql);
        if($productAttributes)
        {
            $attr_name = SCI::cachingAttributeName($id_lang, array_column($productAttributes,'id_product_attribute'));
        } else
        {
            $productAttributes = array();
        }

        $return .= '<rows>';
        foreach ($products as $pRow)
        {
            foreach($productAttributes as $paRow)
            {
                if (!isset($pRow[$itemToFind]) || !isset($paRow[$itemToFind])) {
                    continue;
                }

                $return .= '   <row id="' . $pRow['id_product'] . '_' . implode('-', array($paRow['id_product'], $paRow['id_product_attribute'])) . '">';
                $return .= '      <userdata name="path_pdt">' . $pRow['id_category_default'] . '-' . $pRow['id_product'] . '</userdata>';
                $return .= '      <userdata name="path_combi">' . $paRow['id_category_default'] . '-' . $paRow['id_product'] . '-' . $paRow['id_product_attribute'] . '</userdata>';
                $return .= '      <cell><![CDATA[' . $pRow['id_product'] . ']]></cell>';
                $return .= '      <cell><![CDATA[' . (!empty($pRow['active']) ? _l('Yes') : _l('No')) . ']]></cell>';
                $return .= '      <cell><![CDATA[' . addslashes($pRow['name']) . ']]></cell>';
                $return .= '      <cell><![CDATA[' . (!empty($pRow[$itemToFind]) ? $pRow[$itemToFind] : 0) . ']]></cell>';
                $return .= '      <cell><![CDATA[' . $paRow['id_product'] . '-' . $paRow['id_product_attribute'] . ']]></cell>';
                $return .= '      <cell><![CDATA[' . (!empty($paRow['active']) ? _l('Yes') : _l('No')) . ']]></cell>';
                $return .= '      <cell><![CDATA[' . addslashes($paRow['name']) . ']]></cell>';
                $return .= '      <cell><![CDATA[' . (array_key_exists($paRow['id_product_attribute'], $attr_name) ? addslashes($attr_name[$paRow['id_product_attribute']]) : '') . ']]></cell>';
                $return .= '   </row>';
            }
        }
        $return .= '</rows>';
    }
    echo $return;
}
