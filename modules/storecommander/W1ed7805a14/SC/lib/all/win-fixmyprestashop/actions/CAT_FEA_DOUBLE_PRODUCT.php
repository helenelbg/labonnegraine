<?php
$post_action = Tools::getValue('action');
if (!empty($post_action) && $post_action == 'do_check')
{
    $sql = 'SELECT active
    FROM '._DB_PREFIX_."module
    WHERE name = 'pm_multiplefeatures'";
    $multiple = Db::getInstance()->ExecuteS($sql);
    if (empty($multiple[0]['active']))
    {
        $sql = "SELECT
          CONCAT(`id_feature`,'_',`id_product`) AS ids,
          COUNT(*) AS nb
        FROM "._DB_PREFIX_.'feature_product
        GROUP BY ids
        HAVING COUNT(*)>1
        ORDER BY ids LIMIT 1500';
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

            var tbDoubleFeatureProduct = dhxlSCExtCheck.tabbar.cells("table_CAT_FEA_DOUBLE_PRODUCT").attachToolbar();
            tbDoubleFeatureProduct.setIconset('awesome');
            tbDoubleFeatureProduct.addButton("selectall", 0, "", 'fa fa-bolt yellow', 'fa fa-bolt yellow');
            tbDoubleFeatureProduct.setItemToolTip('selectall','<?php echo _l('Select all'); ?>');
            tbDoubleFeatureProduct.addButton("delete", 0, "", 'fa fa-minus-circle red', 'fa fa-minus-circle red');
            tbDoubleFeatureProduct.setItemToolTip('delete','<?php echo _l('Delete doubles'); ?>');
            tbDoubleFeatureProduct.attachEvent("onClick",
                function(id){
                    if (id=='selectall')
                    {
                        gridDoubleFeatureProduct.selectAll();
                        getGridStat_DoubleFeatureProduct();
                    }
                    if (id=='delete')
                    {
                        deleteDoubleFeatureProduct();
                    }
                });

            var gridDoubleFeatureProduct = dhxlSCExtCheck.tabbar.cells("table_CAT_FEA_DOUBLE_PRODUCT").attachGrid();
            gridDoubleFeatureProduct.setImagePath("lib/js/imgs/");
            gridDoubleFeatureProduct.enableSmartRendering(true);
            gridDoubleFeatureProduct.enableMultiselect(true);

            gridDoubleFeatureProduct.setHeader("id_feature,id_product");
            gridDoubleFeatureProduct.setInitWidths("100,100");
            gridDoubleFeatureProduct.setColAlign("left,left");
            gridDoubleFeatureProduct.setColTypes("ro,ro");
            gridDoubleFeatureProduct.setColSorting("int,int");
            gridDoubleFeatureProduct.attachHeader("#numeric_filter,#numeric_filter");
            gridDoubleFeatureProduct.init();

            var xml = '<rows>';
            <?php foreach ($res as $row)
        {
            list($id_feature, $id_product) = explode('_', $row['ids']); ?>
            xml = xml+'   <row id="<?php echo $row['ids']; ?>">';
            xml = xml+'      <cell><![CDATA[<?php echo $id_feature; ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php echo $id_product; ?>]]></cell>';
            xml = xml+'   </row>';
            <?php
        } ?>
            xml = xml+'</rows>';
            gridDoubleFeatureProduct.parse(xml);

            sbDoubleFeatureProduct=dhxlSCExtCheck.tabbar.cells("table_CAT_FEA_DOUBLE_PRODUCT").attachStatusBar();
            function getGridStat_DoubleFeatureProduct(){
                var filteredRows=gridDoubleFeatureProduct.getRowsNum();
                var selectedRows=(gridDoubleFeatureProduct.getSelectedRowId()?gridDoubleFeatureProduct.getSelectedRowId().split(',').length:0);
                sbDoubleFeatureProduct.setText('<?php echo count($res).' '._l('Errors'); ?>'+" - <?php echo _l('Filter')._l(':'); ?> "+filteredRows+" - <?php echo _l('Selection')._l(':'); ?> "+selectedRows);
            }
            gridDoubleFeatureProduct.attachEvent("onFilterEnd", function(elements){
                getGridStat_DoubleFeatureProduct();
            });
            gridDoubleFeatureProduct.attachEvent("onSelectStateChanged", function(id){
                getGridStat_DoubleFeatureProduct();
            });
            getGridStat_DoubleFeatureProduct();

            function deleteDoubleFeatureProduct()
            {
                var selectedDoubleFeatureProducts = gridDoubleFeatureProduct.getSelectedRowId();
                if(selectedDoubleFeatureProducts==null || selectedDoubleFeatureProducts=="")
                    selectedDoubleFeatureProducts = 0;
                if(selectedDoubleFeatureProducts!="0")
                {
                    $.post("index.php?ajax=1&act=all_win-fixmyprestashop_actions&check=CAT_FEA_DOUBLE_PRODUCT&id_lang="+SC_ID_LANG, { "action": "delete_doubles", "ids": selectedDoubleFeatureProducts}, function(data){
                        dhxlSCExtCheck.tabbar.tabs("table_CAT_FEA_DOUBLE_PRODUCT").close();

                        dhxlSCExtCheck.gridChecks.selectRowById('CAT_FEA_DOUBLE_PRODUCT');
                        doCheck(false);
                    });
                }
            }
        </script>
        <?php
        $content_js = ob_get_clean();
    }
    echo json_encode(array(
        'results' => $results,
        'contentType' => 'grid',
        'content' => $content,
        'title' => _l('Double feat. pdt.'),
        'contentJs' => $content_js,
    ));
}
elseif (!empty($post_action) && $post_action == 'delete_doubles')
{
    $post_ids = Tools::getValue('ids');
    if (!empty($post_ids))
    {
        $ids = explode(',', $post_ids);
        foreach ($ids as $row)
        {
            list($id_feature, $id_product) = explode('_', $row);

            $sql = 'SELECT * FROM '._DB_PREFIX_."feature_product WHERE id_feature='".(int) $id_feature."' AND id_product='".(int) $id_product."'";
            $res = Db::getInstance()->executeS($sql);

            foreach ($res as $num => $double)
            {
                if ($num > 0)
                {
                    $sql = 'DELETE FROM '._DB_PREFIX_."feature_product WHERE id_feature='".(int) $id_feature."' AND id_product='".(int) $id_product."' LIMIT 1";
                    dbExecuteForeignKeyOff($sql);
                }
            }
        }
    }
}
