<?php
$post_action = Tools::getValue('action');
if (!empty($post_action) && $post_action == 'do_check')
{
    $sql = 'SELECT pl.id_group, pl.name FROM `'._DB_PREFIX_.'group_lang` pl WHERE pl.id_group NOT IN (SELECT p.id_group FROM `'._DB_PREFIX_.'group` p) ORDER BY id_lang ASC LIMIT 1500';
    $res = Db::getInstance()->ExecuteS($sql);

    $content = '';
    $content_js = '';
    $results = 'OK';
    if (!empty($res) && count($res) > 0)
    {
        $results = 'KO';
        ob_start(); ?>
        <script type="text/javascript">
    
            var tbGhostGroup = dhxlSCExtCheck.tabbar.cells("table_CUS_GRP_GHOST_GROUP").attachToolbar();
            tbGhostGroup.setIconset('awesome');
            tbGhostGroup.addButton("selectall", 0, "", 'fa fa-bolt yellow', 'fa fa-bolt yellow');
            tbGhostGroup.setItemToolTip('selectall','<?php echo _l('Select all'); ?>');
            tbGhostGroup.addButton("delete", 0, "", 'fa fa-minus-circle red', 'fa fa-minus-circle red');
            tbGhostGroup.setItemToolTip('delete','<?php echo _l('Delete incomplete groups'); ?>');
            tbGhostGroup.addButton("add", 0, "", 'fa fa-plus-circle green', 'fa fa-plus-circle green');
            tbGhostGroup.setItemToolTip('add','<?php echo _l('Recover incomplete groups'); ?>');
            tbGhostGroup.attachEvent("onClick",
                function(id){
                    if (id=='selectall')
                    {
                        gridGhostGroup.selectAll();
                        getGridStat_GhostGroup();
                    }
                    if (id=='delete')
                    {
                        deleteGhostGroup();
                    }
                    if (id=='add')
                    {
                        addGhostGroup()
                    }
                });
        
            var gridGhostGroup = dhxlSCExtCheck.tabbar.cells("table_CUS_GRP_GHOST_GROUP").attachGrid();
            gridGhostGroup.setImagePath("lib/js/imgs/");
            gridGhostGroup.enableSmartRendering(true);
            gridGhostGroup.enableMultiselect(true);
    
            gridGhostGroup.setHeader("ID,<?php echo _l('Name'); ?>,<?php echo _l('Used ?'); ?>");
            gridGhostGroup.setInitWidths("100, 110,50");
            gridGhostGroup.setColAlign("left,left,left");
            gridGhostGroup.setColTypes("ro,ro,ro");
            gridGhostGroup.setColSorting("int,str,str");
            gridGhostGroup.attachHeader("#numeric_filter,#text_filter,#select_filter");
            gridGhostGroup.init();

            var xml = '<rows>';
            <?php foreach ($res as $group)
        {
            $sql = 'SELECT * FROM `'._DB_PREFIX_."customer` WHERE id_default_group = '".(int) $group['id_group']."' LIMIT 1";
            $is_default_used = Db::getInstance()->ExecuteS($sql);
            $sql = 'SELECT * FROM `'._DB_PREFIX_."customer_group` WHERE id_group = '".(int) $group['id_group']."' LIMIT 1";
            $is_used = Db::getInstance()->ExecuteS($sql); ?>
            xml = xml+'   <row id="<?php echo $group['id_group']; ?>">';
            xml = xml+'      <cell><![CDATA[<?php echo $group['id_group']; ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php echo str_replace("'", "\'", $group['name']); ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php if ((!empty($is_used) && count($is_used) > 0) || (!empty($is_default_used) && count($is_default_used) > 0))
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
            gridGhostGroup.parse(xml);

            sbGhostGroup=dhxlSCExtCheck.tabbar.cells("table_CUS_GRP_GHOST_GROUP").attachStatusBar();
            function getGridStat_GhostGroup(){
                var filteredRows=gridGhostGroup.getRowsNum();
                var selectedRows=(gridGhostGroup.getSelectedRowId()?gridGhostGroup.getSelectedRowId().split(',').length:0);
                sbGhostGroup.setText('<?php echo count($res).' '._l('Errors'); ?>'+" - <?php echo _l('Filter')._l(':'); ?> "+filteredRows+" - <?php echo _l('Selection')._l(':'); ?> "+selectedRows);
            }
            gridGhostGroup.attachEvent("onFilterEnd", function(elements){
                getGridStat_GhostGroup();
            });
            gridGhostGroup.attachEvent("onSelectStateChanged", function(id){
                getGridStat_GhostGroup();
            });
            getGridStat_GhostGroup();

            function deleteGhostGroup()
            {
                var selectedGhostGroups = gridGhostGroup.getSelectedRowId();
                if(selectedGhostGroups==null || selectedGhostGroups=="")
                    selectedGhostGroups = 0;
                if(selectedGhostGroups!="0")
                {
                    $.post("index.php?ajax=1&act=all_win-fixmyprestashop_actions&check=CUS_GRP_GHOST_GROUP&id_lang="+SC_ID_LANG, { "action": "delete_groups", "ids": selectedGhostGroups}, function(data){
                        dhxlSCExtCheck.tabbar.tabs("table_CUS_GRP_GHOST_GROUP").close();

                         dhxlSCExtCheck.gridChecks.selectRowById('CUS_GRP_GHOST_GROUP');
                         doCheck(false);
                    });
                }
            }

            function addGhostGroup()
            {
                var selectedGhostGroups = gridGhostGroup.getSelectedRowId();
                if(selectedGhostGroups==null || selectedGhostGroups=="")
                    selectedGhostGroups = 0;
                if(selectedGhostGroups!="0")
                {
                    $.post("index.php?ajax=1&act=all_win-fixmyprestashop_actions&check=CUS_GRP_GHOST_GROUP&id_lang="+SC_ID_LANG, { "action": "add_groups", "ids": selectedGhostGroups}, function(data){
                        dhxlSCExtCheck.tabbar.tabs("table_CUS_GRP_GHOST_GROUP").close();

                         dhxlSCExtCheck.gridChecks.selectRowById('CUS_GRP_GHOST_GROUP');
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
            'title' => _l('Ghost group'),
            'contentJs' => $content_js,
    ));
}
elseif (!empty($post_action) && $post_action == 'delete_groups')
{
    $post_ids = Tools::getValue('ids');
    if (!empty($post_ids))
    {
        $sql = 'DELETE FROM '._DB_PREFIX_.'group_lang WHERE id_group IN ('.pInSQL($post_ids).')';
        $res = dbExecuteForeignKeyOff($sql);
    }
}
elseif (!empty($post_action) && $post_action == 'add_groups')
{
    $post_ids = Tools::getValue('ids');
    if (!empty($post_ids))
    {
        $ids = explode(',', $post_ids);
        foreach ($ids as $id)
        {
            $sql = 'INSERT INTO `'._DB_PREFIX_.'group` (id_group, date_add, date_upd)
                    VALUES ('.(int) $id.",'".date('Y-m-d H:i:s')."','".date('Y-m-d H:i:s')."')";
            $res = dbExecuteForeignKeyOff($sql);
        }
    }
}
