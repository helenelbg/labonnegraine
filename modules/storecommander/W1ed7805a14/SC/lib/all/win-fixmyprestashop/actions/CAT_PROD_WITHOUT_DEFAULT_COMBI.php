<?php
$post_action = Tools::getValue('action');
if (!empty($post_action) && $post_action == 'do_check')
{
    $res = array();
    $shops = Shop::getShops(false);
    foreach ($shops as $id_shop => $shop)
    {
        $sql = '
        SELECT psp.id_product,psp.id_shop
        FROM '._DB_PREFIX_."product_shop psp
        WHERE 
            psp.id_shop = '".(int) $id_shop."'
            AND psp.id_product IN (
                SELECT pa.id_product
                FROM "._DB_PREFIX_.'product_attribute pa
                LEFT JOIN '._DB_PREFIX_."product_attribute_shop pas ON (pas.id_product_attribute=pa.id_product_attribute)
                WHERE pas.id_shop = '".(int) $id_shop."'
            )
            AND psp.id_product NOT IN (
                SELECT pa.id_product
                FROM "._DB_PREFIX_.'product_attribute pa
                LEFT JOIN '._DB_PREFIX_."product_attribute_shop pas ON (pas.id_product_attribute=pa.id_product_attribute)
                WHERE pas.default_on='1'
                    AND pas.id_shop = '".(int) $id_shop."'
            )
        GROUP BY psp.id_product
         LIMIT 1500";
        $res2 = Db::getInstance()->ExecuteS($sql);
        if (!empty($res2) && count($res2) > 0)
        {
            foreach ($res2 as $product)
            {
                $product_inst = new Product((int) $product['id_product'], false, (int) $id_lang, (int) $id_shop);

                $res[] = array(
                    'id_product' => $product['id_product'],
                    'id_shop' => $product['id_shop'],
                    'shop_name' => $shop['name'],
                    'name' => $product_inst->name,
                );
            }
        }
    }

    $content = '';
    $content_js = '';
    $results = 'OK';
    if (!empty($res) && count($res) > 0)
    {
        $results = 'KO';
        ob_start(); ?>
        <script type="text/javascript">
    
            var tbNoDefaultCombi = dhxlSCExtCheck.tabbar.cells("table_CAT_PROD_WITHOUT_DEFAULT_COMBI").attachToolbar();
            tbNoDefaultCombi.setIconset('awesome');
            tbNoDefaultCombi.addButton("selectall", 0, "", 'fa fa-bolt yellow', 'fa fa-bolt yellow');
            tbNoDefaultCombi.setItemToolTip('selectall','<?php echo _l('Select all'); ?>');
            tbNoDefaultCombi.addButton("exportcsv", 0, "", 'fad fa-file-csv green', 'fad fa-file-csv green');
            tbNoDefaultCombi.setItemToolTip('exportcsv','<?php echo _l('Export grid to clipboard in CSV format for MSExcel with tab delimiter.'); ?>');
            tbNoDefaultCombi.addButton("add", 0, "", 'fa fa-plus-circle green', 'fa fa-plus-circle green');
            tbNoDefaultCombi.setItemToolTip('add','<?php echo _l('Recover incomplete products'); ?>');
            tbNoDefaultCombi.attachEvent("onClick",
                function(id){
                    if (id=='selectall')
                    {
                        gridNoDefaultCombi.selectAll();
                        getGridStat_NoDefaultCombi();
                    }
                    if(id=='exportcsv') {
                        displayQuickExportWindow(gridNoDefaultCombi,1);
                    }
                    if (id=='add')
                    {
                        addNoDefaultCombi()
                    }
                });
        
            var gridNoDefaultCombi = dhxlSCExtCheck.tabbar.cells("table_CAT_PROD_WITHOUT_DEFAULT_COMBI").attachGrid();
            gridNoDefaultCombi.setImagePath("lib/js/imgs/");
            gridNoDefaultCombi.enableSmartRendering(true);
            gridNoDefaultCombi.enableMultiselect(true);
            gridNoDefaultCombi.setHeader("ID,<?php echo _l('Name'); ?>,<?php echo _l('Shop'); ?>");
            gridNoDefaultCombi.setInitWidths("100,100,200");
            gridNoDefaultCombi.setColAlign("left,left,left");
            gridNoDefaultCombi.setColTypes("ro,ro,ro");
            gridNoDefaultCombi.setColSorting("int,str,str");
            gridNoDefaultCombi.attachHeader("#numeric_filter,#text_filter,#text_filter");
            gridNoDefaultCombi.init();
    
            var xml = '<rows>';
            <?php foreach ($res as $product) { ?>
            xml = xml+'   <row id="<?php echo $product['id_product'].'_'.$product['id_shop']; ?>">';
            xml = xml+'      <cell><![CDATA[<?php echo $product['id_product']; ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php echo str_replace("'", "\'", ($product['name'])); ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php echo str_replace("'", "\'", ($product['shop_name'])).' (#'.$product['id_shop'].')'; ?>]]></cell>';
            xml = xml+'   </row>';
            <?php } ?>
            xml = xml+'</rows>';
            gridNoDefaultCombi.parse(xml);

            sbNoDefaultCombi=dhxlSCExtCheck.tabbar.cells("table_CAT_PROD_WITHOUT_DEFAULT_COMBI").attachStatusBar();
            function getGridStat_NoDefaultCombi(){
                var filteredRows=gridNoDefaultCombi.getRowsNum();
                var selectedRows=(gridNoDefaultCombi.getSelectedRowId()?gridNoDefaultCombi.getSelectedRowId().split(',').length:0);
                sbNoDefaultCombi.setText('<?php echo count($res).' '._l('Errors'); ?>'+" - <?php echo _l('Filter')._l(':'); ?> "+filteredRows+" - <?php echo _l('Selection')._l(':'); ?> "+selectedRows);
            }
            gridNoDefaultCombi.attachEvent("onFilterEnd", function(elements){
                getGridStat_NoDefaultCombi();
            });
            gridNoDefaultCombi.attachEvent("onSelectStateChanged", function(id){
                getGridStat_NoDefaultCombi();
            });
            getGridStat_NoDefaultCombi();

            function addNoDefaultCombi()
            {
                var selectedNoDefaultCombi = gridNoDefaultCombi.getSelectedRowId();
                if(selectedNoDefaultCombi==null || selectedNoDefaultCombi=="")
                    selectedNoDefaultCombi = 0;
                if(selectedNoDefaultCombi!="0")
                {
                    $.post("index.php?ajax=1&act=all_win-fixmyprestashop_actions&check=CAT_PROD_WITHOUT_DEFAULT_COMBI&id_lang="+SC_ID_LANG, { "action": "add_products", "ids": selectedNoDefaultCombi}, function(data){
                        dhxlSCExtCheck.tabbar.tabs("table_CAT_PROD_WITHOUT_DEFAULT_COMBI").close();

                         dhxlSCExtCheck.gridChecks.selectRowById('CAT_PROD_WITHOUT_DEFAULT_COMBI');
                         doCheck(false);
                    });
                }
            }
        </script>
        <?php $content_js = ob_get_clean();
    }
    echo json_encode(array(
            'results' => $results,
            'contentType' => 'grid',
            'content' => $content,
            'title' => _l('No default combi'),
            'contentJs' => $content_js,
    ));
}
elseif (!empty($post_action) && $post_action == 'add_products')
{
    $post_ids = Tools::getValue('ids');
    if (!empty($post_ids))
    {
        $ids = explode(',', $post_ids);
        foreach ($ids as $id)
        {
            list($id_product, $id_shop) = explode('_', $id);

            $ida = Db::getInstance()->ExecuteS('SELECT pas2.id_product_attribute 
            FROM '._DB_PREFIX_.'product_attribute_shop pas2 
            WHERE pas2.id_product_attribute IN (
                SELECT pa.id_product_attribute 
                FROM '._DB_PREFIX_.'product_attribute pa 
                WHERE pa.id_product = '.(int) $id_product.'
            ) 
                AND pas2.id_shop = "'.(int) $id_shop.'"
            ORDER BY pas2.price ASC
            LIMIT 1');
            if (!empty($ida[0]['id_product_attribute']))
            {
                dbExecuteForeignKeyOff('UPDATE '._DB_PREFIX_.'product_attribute_shop SET default_on=1 WHERE id_product_attribute = '.(int) $ida[0]['id_product_attribute'].' AND id_shop = "'.(int) $id_shop.'"');
            }
        }
    }
}
