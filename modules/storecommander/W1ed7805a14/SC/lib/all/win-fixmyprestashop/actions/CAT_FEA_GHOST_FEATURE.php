<?php
$post_action = Tools::getValue('action');
if (!empty($post_action) && $post_action == 'do_check')
{
    $sql = 'select pl.id_feature, pl.name from '._DB_PREFIX_.'feature_lang pl where pl.id_feature not in (select p.id_feature from '._DB_PREFIX_.'feature p) ORDER BY id_lang ASC LIMIT 1500';
    $res = Db::getInstance()->ExecuteS($sql);

    $content = '';
    $content_js = '';
    $results = 'OK';
    if (!empty($res) && count($res) > 0)
    {
        $results = 'KO';
        ob_start(); ?>
        <script type="text/javascript">
    
            var tbGhostFeature = dhxlSCExtCheck.tabbar.cells("table_CAT_FEA_GHOST_FEATURE").attachToolbar();
            tbGhostFeature.setIconset('awesome');
            tbGhostFeature.addButton("selectall", 0, "", 'fa fa-bolt yellow', 'fa fa-bolt yellow');
            tbGhostFeature.setItemToolTip('selectall','<?php echo _l('Select all'); ?>');
            tbGhostFeature.addButton("delete", 0, "", 'fa fa-minus-circle red', 'fa fa-minus-circle red');
            tbGhostFeature.setItemToolTip('delete','<?php echo _l('Delete incomplete features'); ?>');
            tbGhostFeature.addButton("add", 0, "", 'fa fa-plus-circle green', 'fa fa-plus-circle green');
            tbGhostFeature.setItemToolTip('add','<?php echo _l('Recover incomplete features'); ?>');
            tbGhostFeature.attachEvent("onClick",
                function(id){
                    if (id=='selectall')
                    {
                        gridGhostFeature.selectAll();
                        getGridStat_GhostFeature();
                    }
                    if (id=='delete')
                    {
                        deleteGhostFeature();
                    }
                    if (id=='add')
                    {
                        addGhostFeature()
                    }
                });
        
            var gridGhostFeature = dhxlSCExtCheck.tabbar.cells("table_CAT_FEA_GHOST_FEATURE").attachGrid();
            gridGhostFeature.setImagePath("lib/js/imgs/");
            gridGhostFeature.enableSmartRendering(true);
            gridGhostFeature.enableMultiselect(true);
    
            gridGhostFeature.setHeader("ID,<?php echo _l('Name'); ?>,<?php echo _l('Used?'); ?>");
            gridGhostFeature.setInitWidths("100, 110,50");
            gridGhostFeature.setColAlign("left,left,left");
            gridGhostFeature.setColTypes("ro,ro,ro");
            gridGhostFeature.setColSorting("int,str,str");
            gridGhostFeature.attachHeader("#numeric_filter,#text_filter,#select_filter");
            gridGhostFeature.init();

            var xml = '<rows>';
            <?php foreach ($res as $feature)
        {
            $sql = 'SELECT * FROM `'._DB_PREFIX_."feature_product` WHERE id_feature = '".(int) $feature['id_feature']."' LIMIT 1";
            $is_used = Db::getInstance()->ExecuteS($sql); ?>
            xml = xml+'   <row id="<?php echo $feature['id_feature']; ?>">';
            xml = xml+'      <cell><![CDATA[<?php echo $feature['id_feature']; ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php echo str_replace("'", "\'", $feature['name']); ?>]]></cell>';
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
            gridGhostFeature.parse(xml);

            sbGhostFeature=dhxlSCExtCheck.tabbar.cells("table_CAT_FEA_GHOST_FEATURE").attachStatusBar();
            function getGridStat_GhostFeature(){
                var filteredRows=gridGhostFeature.getRowsNum();
                var selectedRows=(gridGhostFeature.getSelectedRowId()?gridGhostFeature.getSelectedRowId().split(',').length:0);
                sbGhostFeature.setText('<?php echo count($res).' '._l('Errors'); ?>'+" - <?php echo _l('Filter')._l(':'); ?> "+filteredRows+" - <?php echo _l('Selection')._l(':'); ?> "+selectedRows);
            }
            gridGhostFeature.attachEvent("onFilterEnd", function(elements){
                getGridStat_GhostFeature();
            });
            gridGhostFeature.attachEvent("onSelectStateChanged", function(id){
                getGridStat_GhostFeature();
            });
            getGridStat_GhostFeature();

            function deleteGhostFeature()
            {
                var selectedGhostFeatures = gridGhostFeature.getSelectedRowId();
                if(selectedGhostFeatures==null || selectedGhostFeatures=="")
                    selectedGhostFeatures = 0;
                if(selectedGhostFeatures!="0")
                {
                    $.post("index.php?ajax=1&act=all_win-fixmyprestashop_actions&check=CAT_FEA_GHOST_FEATURE&id_lang="+SC_ID_LANG, { "action": "delete_features", "ids": selectedGhostFeatures}, function(data){
                        dhxlSCExtCheck.tabbar.tabs("table_CAT_FEA_GHOST_FEATURE").close();

                         dhxlSCExtCheck.gridChecks.selectRowById('CAT_FEA_GHOST_FEATURE');
                         doCheck(false);
                    });
                }
            }

            function addGhostFeature()
            {
                var selectedGhostFeatures = gridGhostFeature.getSelectedRowId();
                if(selectedGhostFeatures==null || selectedGhostFeatures=="")
                    selectedGhostFeatures = 0;
                if(selectedGhostFeatures!="0")
                {
                    $.post("index.php?ajax=1&act=all_win-fixmyprestashop_actions&check=CAT_FEA_GHOST_FEATURE&id_lang="+SC_ID_LANG, { "action": "add_features", "ids": selectedGhostFeatures}, function(data){
                        dhxlSCExtCheck.tabbar.tabs("table_CAT_FEA_GHOST_FEATURE").close();

                         dhxlSCExtCheck.gridChecks.selectRowById('CAT_FEA_GHOST_FEATURE');
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
            'title' => _l('Ghost feature'),
            'contentJs' => $content_js,
    ));
}
elseif (!empty($post_action) && $post_action == 'delete_features')
{
    $post_ids = Tools::getValue('ids');
    if (!empty($post_ids))
    {
        $sql = 'DELETE FROM '._DB_PREFIX_.'feature_lang WHERE id_feature IN ('.pInSQL($post_ids).')';
        $res = dbExecuteForeignKeyOff($sql);
    }
}
elseif (!empty($post_action) && $post_action == 'add_features')
{
    $post_ids = Tools::getValue('ids');
    if (!empty($post_ids))
    {
        $ids = explode(',', $post_ids);
        foreach ($ids as $id)
        {
            $sql = 'INSERT INTO '._DB_PREFIX_.'feature (id_feature)
                    VALUES ('.(int) $id.')';
            $res = dbExecuteForeignKeyOff($sql);
        }
    }
}
