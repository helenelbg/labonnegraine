<?php
$post_action = Tools::getValue('action');
if (!empty($post_action) && $post_action == 'do_check')
{
    $sql = 'SELECT p.id_product, pl.name, p.id_category_default '.(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? ' ,ps.id_category_default ' : '').',
                fp.id_feature, fl.name AS feature_name, fvl.id_lang
            FROM '._DB_PREFIX_.'product p
                INNER JOIN '._DB_PREFIX_."product_lang pl ON (pl.id_product = p.id_product AND pl.id_lang='".(int) $id_lang."')
                ".(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? ' INNER JOIN '._DB_PREFIX_.'product_shop ps ON (ps.id_product = p.id_product AND ps.id_shop=p.id_shop_default) ' : '').'
                INNER JOIN '._DB_PREFIX_.'feature_product fp ON (fp.id_product = p.id_product)
                    INNER JOIN '._DB_PREFIX_."feature_lang fl ON (fp.id_feature = fl.id_feature AND fl.id_lang='".(int) $id_lang."')
                    INNER JOIN "._DB_PREFIX_.'feature_value fv ON (fp.id_feature_value = fv.id_feature_value AND fv.custom=1)
                        INNER JOIN '._DB_PREFIX_."feature_value_lang fvl ON (fvl.id_feature_value = fv.id_feature_value)
            WHERE fvl.value='' OR fvl.value IS NULL
            ORDER BY p.id_product ASC, fl.name ASC, fvl.id_lang";
    $res = Db::getInstance()->ExecuteS($sql);

    $langs = array();
    $sql = 'SELECT * FROM '._DB_PREFIX_.'lang';
    $tmps = Db::getInstance()->ExecuteS($sql);
    foreach ($tmps as $tmp)
    {
        $langs[$tmp['id_lang']] = $tmp['name'];
    }

    $content = '';
    $content_js = '';
    $results = 'OK';
    if (!empty($res) && count($res) > 0)
    {
        $results = 'KO';
        ob_start(); ?>
        <script type="text/javascript">

            var tbEmptyCustomFeature = dhxlSCExtCheck.tabbar.cells("table_CAT_PROD_EMPTY_CUSTOM_FEATURE").attachToolbar();
            tbEmptyCustomFeature.setIconset('awesome');
            tbEmptyCustomFeature.addButton("gotocatalog", 0, "", 'fad fa-external-link green', 'fad fa-external-link green');
            tbEmptyCustomFeature.setItemToolTip('gotocatalog','<?php echo _l('Go to the product in catalog.'); ?>');
            tbEmptyCustomFeature.attachEvent("onClick",
                function(id){
                    if (id=='gotocatalog')
                    {
                        if(idEmptyCustomFeature !== '') {
                            var path = gridEmptyCustomFeature.getUserData(idEmptyCustomFeature, "path_pdt");
                            let url = "?page=cat_tree&open_cat_grid="+path;
                            window.open(url,'_blank');
                        }
                    }
                });

            var gridEmptyCustomFeature = dhxlSCExtCheck.tabbar.cells("table_CAT_PROD_EMPTY_CUSTOM_FEATURE").attachGrid();
            gridEmptyCustomFeature.setImagePath("lib/js/imgs/");
            gridEmptyCustomFeature.enableSmartRendering(true);
            gridEmptyCustomFeature.enableMultiselect(false);

            gridEmptyCustomFeature.setHeader("ID <?php echo _l('product'); ?>,<?php echo _l('Name'); ?>,<?php echo _l('Feature'); ?>,<?php echo _l('Lang'); ?>");
            gridEmptyCustomFeature.setInitWidths("100,*,200,120");
            gridEmptyCustomFeature.setColAlign("left,left,left,left");
            gridEmptyCustomFeature.setColTypes("ro,ro,ro,ro");
            gridEmptyCustomFeature.setColSorting("int,str,str,str");
            gridEmptyCustomFeature.attachHeader("#numeric_filter,#text_filter,#text_filter,#select_filter");
            gridEmptyCustomFeature.init();

            var idEmptyCustomFeature = '';
            gridEmptyCustomFeature.attachEvent('onRowSelect',function(id){
                idEmptyCustomFeature = id;
            });

            var xml = '<rows>';
            <?php foreach ($res as $product)
        {
            ?>
            xml = xml+'   <row id="<?php echo $product['id_product'].'_'.$product['id_feature'].'_'.$product['id_lang']; ?>">';
            xml = xml+'      <userdata name="path_pdt"><?php echo $product['id_category_default'].'-'.$product['id_product']; ?></userdata>';
            xml = xml+'      <cell><![CDATA[<?php echo str_replace("'", "\'", $product['id_product']); ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php echo str_replace("'", "\'", $product['name']); ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php echo str_replace("'", "\'", $product['feature_name']); ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php echo str_replace("'", "\'", $langs[$product['id_lang']]); ?>]]></cell>';
            xml = xml+'   </row>';
            <?php
        } ?>
            xml = xml+'</rows>';
            gridEmptyCustomFeature.parse(xml);

            sbEmptyCustomFeature=dhxlSCExtCheck.tabbar.cells("table_CAT_PROD_EMPTY_CUSTOM_FEATURE").attachStatusBar();
            function getGridStat_EmptyCustomFeature(){
                var filteredRows=gridEmptyCustomFeature.getRowsNum();
                var selectedRows=(gridEmptyCustomFeature.getSelectedRowId()?gridEmptyCustomFeature.getSelectedRowId().split(',').length:0);
                sbEmptyCustomFeature.setText('<?php echo count($res).' '._l('Errors'); ?>'+" - <?php echo _l('Filter')._l(':'); ?> "+filteredRows+" - <?php echo _l('Selection')._l(':'); ?> "+selectedRows);
            }
            gridEmptyCustomFeature.attachEvent("onFilterEnd", function(elements){
                getGridStat_EmptyCustomFeature();
            });
            gridEmptyCustomFeature.attachEvent("onSelectStateChanged", function(id){
                getGridStat_EmptyCustomFeature();
            });
            getGridStat_EmptyCustomFeature();
        </script>
        <?php
        $content_js = ob_get_clean();
    }
    echo json_encode(array(
        'results' => $results,
        'contentType' => 'grid',
        'content' => $content,
        'title' => _l('Empty custom feat'),
        'contentJs' => $content_js,
    ));
}
