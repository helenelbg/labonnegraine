<?php
$post_action = Tools::getValue('action');
if (!empty($post_action) && $post_action == 'do_check')
{
    $sql = 'select pl.id_feature_value from '._DB_PREFIX_.'feature_value_lang pl where pl.id_feature_value not in (select p.id_feature_value from '._DB_PREFIX_.'feature_value p) ORDER BY id_lang ASC LIMIT 1500';
    $res = Db::getInstance()->ExecuteS($sql);

    $content = '';
    $content_js = '';
    $results = 'OK';
    if (!empty($res) && count($res) > 0)
    {
        $results = 'KO';
        ob_start(); ?>
        <script type="text/javascript">
    
            var tbGhostFeatureValue = dhxlSCExtCheck.tabbar.cells("table_CAT_FEA_GHOST_FEATURE_VALUE").attachToolbar();
            tbGhostFeatureValue.setIconset('awesome');
            tbGhostFeatureValue.addButton("selectall", 0, "", 'fa fa-bolt yellow', 'fa fa-bolt yellow');
            tbGhostFeatureValue.setItemToolTip('selectall','<?php echo _l('Select all'); ?>');
            tbGhostFeatureValue.addButton("delete", 0, "", 'fa fa-minus-circle red', 'fa fa-minus-circle red');
            tbGhostFeatureValue.setItemToolTip('delete','<?php echo _l('Delete incomplete feature values'); ?>');
            tbGhostFeatureValue.attachEvent("onClick",
                function(id){
                    if (id=='selectall')
                    {
                        gridGhostFeatureValue.selectAll();
                        getGridStat_GhostFeatureValue();
                    }
                    if (id=='delete')
                    {
                        deleteGhostFeatureValue();
                    }
                });
        
            var gridGhostFeatureValue = dhxlSCExtCheck.tabbar.cells("table_CAT_FEA_GHOST_FEATURE_VALUE").attachGrid();
            gridGhostFeatureValue.setImagePath("lib/js/imgs/");
            gridGhostFeatureValue.enableSmartRendering(true);
            gridGhostFeatureValue.enableMultiselect(true);
    
            gridGhostFeatureValue.setHeader("ID,<?php echo _l('Used?'); ?>");
            gridGhostFeatureValue.setInitWidths("100,50");
            gridGhostFeatureValue.setColAlign("left,left");
            gridGhostFeatureValue.setColTypes("ro,ro");
            gridGhostFeatureValue.setColSorting("int,str");
            gridGhostFeatureValue.attachHeader("#numeric_filter,#select_filter");
            gridGhostFeatureValue.init();

            var xml = '<rows>';
            <?php foreach ($res as $feature_value)
        {
            $sql = 'SELECT * FROM `'._DB_PREFIX_."feature_product` WHERE id_feature_value = '".(int) $feature_value['id_feature_value']."' LIMIT 1";
            $is_used = Db::getInstance()->ExecuteS($sql); ?>
            xml = xml+'   <row id="<?php echo $feature_value['id_feature_value']; ?>">';
            xml = xml+'      <cell><![CDATA[<?php echo $feature_value['id_feature_value']; ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php if (!empty($is_used) && count($is_used) > 0)
            {
                echo _l('Yes');
            }
            else
            {
                echo _l('No');
            } ?>]]></cell>';
            xml = xml+'   </row>';
            <?php
        } ?>
            xml = xml+'</rows>';
            gridGhostFeatureValue.parse(xml);

            sbGhostFeatureValue=dhxlSCExtCheck.tabbar.cells("table_CAT_FEA_GHOST_FEATURE_VALUE").attachStatusBar();
            function getGridStat_GhostFeatureValue(){
                var filteredRows=gridGhostFeatureValue.getRowsNum();
                var selectedRows=(gridGhostFeatureValue.getSelectedRowId()?gridGhostFeatureValue.getSelectedRowId().split(',').length:0);
                sbGhostFeatureValue.setText('<?php echo count($res).' '._l('Errors'); ?>'+" - <?php echo _l('Filter')._l(':'); ?> "+filteredRows+" - <?php echo _l('Selection')._l(':'); ?> "+selectedRows);
            }
            gridGhostFeatureValue.attachEvent("onFilterEnd", function(elements){
                getGridStat_GhostFeatureValue();
            });
            gridGhostFeatureValue.attachEvent("onSelectStateChanged", function(id){
                getGridStat_GhostFeatureValue();
            });
            getGridStat_GhostFeatureValue();

            function deleteGhostFeatureValue()
            {
                var selectedGhostFeatureValues = gridGhostFeatureValue.getSelectedRowId();
                if(selectedGhostFeatureValues==null || selectedGhostFeatureValues=="")
                    selectedGhostFeatureValues = 0;
                if(selectedGhostFeatureValues!="0")
                {
                    $.post("index.php?ajax=1&act=all_win-fixmyprestashop_actions&check=CAT_FEA_GHOST_FEATURE_VALUE&id_lang="+SC_ID_LANG, { "action": "delete_feature_values", "ids": selectedGhostFeatureValues}, function(data){
                        dhxlSCExtCheck.tabbar.tabs("table_CAT_FEA_GHOST_FEATURE_VALUE").close();

                         dhxlSCExtCheck.gridChecks.selectRowById('CAT_FEA_GHOST_FEATURE_VALUE');
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
            'title' => _l('Ghost feat. val.'),
            'contentJs' => $content_js,
    ));
}
elseif (!empty($post_action) && $post_action == 'delete_feature_values')
{
    $post_ids = Tools::getValue('ids');
    if (!empty($post_ids))
    {
        $sql = 'DELETE FROM '._DB_PREFIX_.'feature_value_lang WHERE id_feature_value IN ('.pInSQL($post_ids).')';
        $res = dbExecuteForeignKeyOff($sql);
    }
}
