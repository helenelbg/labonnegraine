<?php
$post_action = Tools::getValue('action');
if (!empty($post_action) && $post_action == 'do_check')
{
    $sql = 'SELECT sa.*,(SELECT SUM(s.usable_quantity) FROM `'._DB_PREFIX_.'stock` s WHERE s.id_product=sa.id_product AND s.id_product_attribute=sa.id_product_attribute) as g_qty, sap.id_product_attribute as p_pa, sap.depends_on_stock as p_dos
            FROM `'._DB_PREFIX_.'stock_available` sa,
                `'._DB_PREFIX_.'stock_available` sap
                INNER JOIN `'._DB_PREFIX_.'product_shop` ps ON (sap.id_product=ps.id_product AND sap.id_shop=ps.id_shop)
            WHERE
                            sa.id_product_attribute > 0
                            AND sap.id_product_attribute = 0

                            AND (sap.id_product=sa.id_product AND sap.id_shop=sa.id_shop)

                            AND ps.advanced_stock_management=1
                            AND sap.depends_on_stock=1

                            AND sa.quantity != (SELECT SUM(s.usable_quantity) FROM `'._DB_PREFIX_.'stock` s WHERE s.id_product=sa.id_product AND s.id_product_attribute=sa.id_product_attribute)
            ORDER BY sa.id_product ASC
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

            var tbGhostCombiStockAvailable = dhxlSCExtCheck.tabbar.cells("table_CAT_STK_TOO_MUCH_COMBI_STOCK_AVAILABLE").attachToolbar();
            tbGhostCombiStockAvailable.setIconset('awesome');
            tbGhostCombiStockAvailable.addButton("selectall", 0, "", 'fa fa-bolt yellow', 'fa fa-bolt yellow');
            tbGhostCombiStockAvailable.setItemToolTip('selectall','<?php echo _l('Select all'); ?>');
            tbGhostCombiStockAvailable.addButton("delete", 0, "", 'fad fa-edit yellow', 'fad fa-edit yellow');
            tbGhostCombiStockAvailable.setItemToolTip('delete','<?php echo _l('Fix stock'); ?>');
            tbGhostCombiStockAvailable.attachEvent("onClick",
                function(id){
                    if (id=='selectall')
                    {
                        gridGhostCombiStockAvailable.selectAll();
                        getGridStat_GhostCombiStockAvailable();
                    }
                    if (id=='delete')
                    {
                        deleteGhostCombiStockAvailable()
                    }
                });

            var gridGhostCombiStockAvailable = dhxlSCExtCheck.tabbar.cells("table_CAT_STK_TOO_MUCH_COMBI_STOCK_AVAILABLE").attachGrid();
            gridGhostCombiStockAvailable.setImagePath("lib/js/imgs/");
            gridGhostCombiStockAvailable.enableSmartRendering(true);
            gridGhostCombiStockAvailable.enableMultiselect(true);

            gridGhostCombiStockAvailable.setHeader("ID,<?php echo _l('Name'); ?>,<?php echo _l('Combination ID'); ?>,ID shop,<?php echo _l('Good qty'); ?>,<?php echo _l('Wrong qty'); ?>");
            gridGhostCombiStockAvailable.setInitWidths("60,250,60,60,60,60");
            gridGhostCombiStockAvailable.setColSorting("int,str,int,int,int,int");
            gridGhostCombiStockAvailable.setColAlign("left,left,left,left,right,right");
            gridGhostCombiStockAvailable.setColTypes("ro,ro,ro,ro,ro,ro");
            gridGhostCombiStockAvailable.attachHeader("#numeric_filter,#text_filter,#numeric_filter,#numeric_filter,#numeric_filter,#numeric_filter");
            gridGhostCombiStockAvailable.init();

            var xml = '<rows>';
            <?php foreach ($res as $stock_available)
        {
            $name = '';
            $sql = 'SELECT name FROM `'._DB_PREFIX_."product_lang` WHERE id_product = '".(int) $stock_available['id_product']."' AND id_lang = ".(int) SCI::getConfigurationValue('PS_LANG_DEFAULT');
            $name_temp = Db::getInstance()->getValue($sql);
            if (!empty($name_temp))
            {
                $name = $name_temp;
            } ?>
            xml = xml+'   <row id="<?php echo $stock_available['id_stock_available'].'_'.$stock_available['g_qty']; ?>">';
            xml = xml+'      <cell><![CDATA[<?php echo $stock_available['id_product']; ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php echo str_replace("'", "\'", str_replace("\'", "'", $name)); ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php echo $stock_available['id_product_attribute']; ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php echo $stock_available['id_shop']; ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php echo $stock_available['g_qty']; ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php echo $stock_available['quantity']; ?>]]></cell>';
            xml = xml+'   </row>';
            <?php
        } ?>
            xml = xml+'</rows>';
            gridGhostCombiStockAvailable.parse(xml);

            sbGhostCombiStockAvailable=dhxlSCExtCheck.tabbar.cells("table_CAT_STK_TOO_MUCH_COMBI_STOCK_AVAILABLE").attachStatusBar();
            function getGridStat_GhostCombiStockAvailable(){
                var filteredRows=gridGhostCombiStockAvailable.getRowsNum();
                var selectedRows=(gridGhostCombiStockAvailable.getSelectedRowId()?gridGhostCombiStockAvailable.getSelectedRowId().split(',').length:0);
                sbGhostCombiStockAvailable.setText('<?php echo count($res).' '._l('Errors'); ?>'+" - <?php echo _l('Filter')._l(':'); ?> "+filteredRows+" - <?php echo _l('Selection')._l(':'); ?> "+selectedRows);
            }
            gridGhostCombiStockAvailable.attachEvent("onFilterEnd", function(elements){
                getGridStat_GhostCombiStockAvailable();
            });
            gridGhostCombiStockAvailable.attachEvent("onSelectStateChanged", function(id){
                getGridStat_GhostCombiStockAvailable();
            });
            getGridStat_GhostCombiStockAvailable();

            function deleteGhostCombiStockAvailable()
            {
                var selectedGhostCombiStockAvailables = gridGhostCombiStockAvailable.getSelectedRowId();
                if(selectedGhostCombiStockAvailables==null || selectedGhostCombiStockAvailables=="")
                    selectedGhostCombiStockAvailables = 0;
                if(selectedGhostCombiStockAvailables!="0")
                {
                    $.post("index.php?ajax=1&act=all_win-fixmyprestashop_actions&check=CAT_STK_TOO_MUCH_COMBI_STOCK_AVAILABLE&id_lang="+SC_ID_LANG, { "action": "delete_stockavailable", "ids": selectedGhostCombiStockAvailables}, function(data){
                        dhxlSCExtCheck.tabbar.tabs("table_CAT_STK_TOO_MUCH_COMBI_STOCK_AVAILABLE").close();

                        dhxlSCExtCheck.gridChecks.selectRowById('CAT_STK_TOO_MUCH_COMBI_STOCK_AVAILABLE');
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
        'title' => _l('Wrong Combi Stock'),
        'contentJs' => $content_js,
    ));
}
elseif (!empty($post_action) && $post_action == 'delete_stockavailable')
{
    $post_ids = Tools::getValue('ids');
    if (!empty($post_ids))
    {
        $ids = explode(',', $post_ids);
        foreach ($ids as $id)
        {
            list($id, $qty) = explode('_', $id);
            $sql = 'UPDATE '._DB_PREFIX_."stock_available SET quantity='".(int) $qty."' WHERE id_stock_available = '".(int) $id."'";
            $res = dbExecuteForeignKeyOff($sql);
        }
    }
}
