<?php
$post_action = Tools::getValue('action');
$action_name = 'CAT_PROD_GHOST_PRODUCT_ATTRIBUTE';
$tab_title = _l('Ghost product');

if (!empty($post_action) && $post_action == 'do_check')
{
    $sql = 'select pa.id_product from '._DB_PREFIX_.'product_attribute pa where pa.id_product not in (select p.id_product from '._DB_PREFIX_.'product p) GROUP BY pa.id_product LIMIT 1500';
    $res = Db::getInstance()->ExecuteS($sql);

    $content = '';
    $content_js = '';
    $results = 'OK';
    if (!empty($res) && count($res) > 0)
    {
        $results = 'KO';
        ob_start(); ?>
        <script type="text/javascript">

            var tbGhostProductAttribute = dhxlSCExtCheck.tabbar.cells("table_<?php echo $action_name; ?>").attachToolbar();
            tbGhostProductAttribute.setIconset('awesome');
            tbGhostProductAttribute.addButton("selectall", 0, "", 'fa fa-bolt yellow', 'fa fa-bolt yellow');
            tbGhostProductAttribute.setItemToolTip('selectall','<?php echo _l('Select all'); ?>');
            tbGhostProductAttribute.addButton("delete", 0, "", 'fa fa-minus-circle red', 'fa fa-minus-circle red');
            tbGhostProductAttribute.setItemToolTip('delete','<?php echo _l('Delete incomplete products'); ?>');
            tbGhostProductAttribute.attachEvent("onClick",
                function(id){
                    if (id=='selectall')
                    {
                        gridGhostProductAttribute.selectAll();
                        getGridStat_GhostProductAttribute();
                    }
                    if (id=='delete')
                    {
                        deleteGhostProductAttribute();
                    }
                });

            var gridGhostProductAttribute = dhxlSCExtCheck.tabbar.cells("table_<?php echo $action_name; ?>").attachGrid();
            gridGhostProductAttribute.setImagePath("lib/js/imgs/");
            gridGhostProductAttribute.enableSmartRendering(true);
            gridGhostProductAttribute.enableMultiselect(true);

            gridGhostProductAttribute.setHeader("<?php echo _l('Deleted products ID'); ?>");
            gridGhostProductAttribute.setInitWidths("*");
            gridGhostProductAttribute.setColAlign("left");
            gridGhostProductAttribute.setColTypes("ro");
            gridGhostProductAttribute.setColSorting("int");
            gridGhostProductAttribute.attachHeader("#numeric_filter");
            gridGhostProductAttribute.init();

            var xml = '<rows>';
            <?php foreach ($res as $product) { ?>
            xml = xml+'   <row id="<?php echo $product['id_product']; ?>">';
            xml = xml+'      <cell><![CDATA[<?php echo $product['id_product']; ?>]]></cell>';
            xml = xml+'   </row>';
            <?php } ?>
            xml = xml+'</rows>';
            gridGhostProductAttribute.parse(xml);

            sbGhostProductAttribute=dhxlSCExtCheck.tabbar.cells("table_<?php echo $action_name; ?>").attachStatusBar();
            function getGridStat_GhostProductAttribute(){
                var filteredRows=gridGhostProductAttribute.getRowsNum();
                var selectedRows=(gridGhostProductAttribute.getSelectedRowId()?gridGhostProductAttribute.getSelectedRowId().split(',').length:0);
                sbGhostProductAttribute.setText('<?php echo count($res).' '._l('Errors'); ?>'+" - <?php echo _l('Filter')._l(':'); ?> "+filteredRows+" - <?php echo _l('Selection')._l(':'); ?> "+selectedRows);
            }
            gridGhostProductAttribute.attachEvent("onFilterEnd", function(elements){
                getGridStat_GhostProductAttribute();
            });
            gridGhostProductAttribute.attachEvent("onSelectStateChanged", function(id){
                getGridStat_GhostProductAttribute();
            });
            getGridStat_GhostProductAttribute();

            function deleteGhostProductAttribute()
            {
                var selectedGhostProductAttribute = gridGhostProductAttribute.getSelectedRowId();
                if(selectedGhostProductAttribute==null || selectedGhostProductAttribute=="")
                    selectedGhostProductAttribute = 0;
                if(selectedGhostProductAttribute!="0")
                {
                    $.post("index.php?ajax=1&act=all_win-fixmyprestashop_actions&check=CAT_PROD_GHOST_PRODUCT_ATTRIBUTE&id_lang="+SC_ID_LANG, { "action": "delete_product_attribute", "ids": selectedGhostProductAttribute}, function(data){
                        dhxlSCExtCheck.tabbar.tabs("table_<?php echo $action_name; ?>").close();

                        dhxlSCExtCheck.gridChecks.selectRowById('<?php echo $action_name; ?>');
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
        'title' => $tab_title,
        'contentJs' => $content_js,
    ));
}
elseif (!empty($post_action) && $post_action == 'delete_product_attribute')
{
    $post_ids = Tools::getValue('ids');
    if (!empty($post_ids))
    {
        $sql = 'DELETE FROM '._DB_PREFIX_.'product_attribute WHERE id_product IN ('.pInSQL($post_ids).')';
        $res = dbExecuteForeignKeyOff($sql);
    }
}

