<?php
$post_action = Tools::getValue('action');
if (!empty($post_action) && $post_action == 'do_check')
{
    $sql = 'select pl.id_order_state, pl.name from '._DB_PREFIX_.'order_state_lang pl where pl.id_order_state not in (select p.id_order_state from '._DB_PREFIX_.'order_state p) ORDER BY id_lang ASC LIMIT 1500';
    $res = Db::getInstance()->ExecuteS($sql);

    $content = '';
    $content_js = '';
    $results = 'OK';
    if (!empty($res) && count($res) > 0)
    {
        $results = 'KO';
        ob_start(); ?>
        <script type="text/javascript">
    
            var tbGhostOrderState = dhxlSCExtCheck.tabbar.cells("table_CMD_STA_GHOST_ORDER_STATE").attachToolbar();
            tbGhostOrderState.setIconset('awesome');
            tbGhostOrderState.addButton("selectall", 0, "", 'fa fa-bolt yellow', 'fa fa-bolt yellow');
            tbGhostOrderState.setItemToolTip('selectall','<?php echo _l('Select all'); ?>');
            tbGhostOrderState.addButton("delete", 0, "", 'fa fa-minus-circle red', 'fa fa-minus-circle red');
            tbGhostOrderState.setItemToolTip('delete','<?php echo _l('Delete incomplete order states'); ?>');
            tbGhostOrderState.addButton("add", 0, "", 'fa fa-plus-circle green', 'fa fa-plus-circle green');
            tbGhostOrderState.setItemToolTip('add','<?php echo _l('Recover incomplete order states'); ?>');
            tbGhostOrderState.attachEvent("onClick",
                function(id){
                    if (id=='selectall')
                    {
                        gridGhostOrderState.selectAll();
                        getGridStat_GhostOrderState();
                    }
                    if (id=='delete')
                    {
                        deleteGhostOrderState();
                    }
                    if (id=='add')
                    {
                        addGhostOrderState()
                    }
                });
        
            var gridGhostOrderState = dhxlSCExtCheck.tabbar.cells("table_CMD_STA_GHOST_ORDER_STATE").attachGrid();
            gridGhostOrderState.setImagePath("lib/js/imgs/");
            gridGhostOrderState.enableSmartRendering(true);
            gridGhostOrderState.enableMultiselect(true);
    
            gridGhostOrderState.setHeader("ID,<?php echo _l('Name'); ?>,<?php echo _l('Used?'); ?>");
            gridGhostOrderState.setInitWidths("100, 110,50");
            gridGhostOrderState.setColAlign("left,left,left");
            gridGhostOrderState.setColTypes("ro,ro,ro");
            gridGhostOrderState.setColSorting("int,str,str");
            gridGhostOrderState.attachHeader("#numeric_filter,#text_filter,#select_filter");
            gridGhostOrderState.init();

            var xml = '<rows>';
            <?php foreach ($res as $order_state)
        {
            $sql = 'SELECT * FROM `'._DB_PREFIX_."order_history` WHERE id_order_state = '".(int) $order_state['id_order_state']."' LIMIT 1";
            $is_used = Db::getInstance()->ExecuteS($sql); ?>
            xml = xml+'   <row id="<?php echo $order_state['id_order_state']; ?>">';
            xml = xml+'      <cell><![CDATA[<?php echo $order_state['id_order_state']; ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php echo str_replace("'", "\'", $order_state['name']); ?>]]></cell>';
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
            gridGhostOrderState.parse(xml);

            sbGhostOrderState=dhxlSCExtCheck.tabbar.cells("table_CMD_STA_GHOST_ORDER_STATE").attachStatusBar();
            function getGridStat_GhostOrderState(){
                var filteredRows=gridGhostOrderState.getRowsNum();
                var selectedRows=(gridGhostOrderState.getSelectedRowId()?gridGhostOrderState.getSelectedRowId().split(',').length:0);
                sbGhostOrderState.setText('<?php echo count($res).' '._l('Errors'); ?>'+" - <?php echo _l('Filter')._l(':'); ?> "+filteredRows+" - <?php echo _l('Selection')._l(':'); ?> "+selectedRows);
            }
            gridGhostOrderState.attachEvent("onFilterEnd", function(elements){
                getGridStat_GhostOrderState();
            });
            gridGhostOrderState.attachEvent("onSelectStateChanged", function(id){
                getGridStat_GhostOrderState();
            });
            getGridStat_GhostOrderState();

            function deleteGhostOrderState()
            {
                var selectedGhostOrderStates = gridGhostOrderState.getSelectedRowId();
                if(selectedGhostOrderStates==null || selectedGhostOrderStates=="")
                    selectedGhostOrderStates = 0;
                if(selectedGhostOrderStates!="0")
                {
                    $.post("index.php?ajax=1&act=all_win-fixmyprestashop_actions&check=CMD_STA_GHOST_ORDER_STATE&id_lang="+SC_ID_LANG, { "action": "delete_order_states", "ids": selectedGhostOrderStates}, function(data){
                        dhxlSCExtCheck.tabbar.tabs("table_CMD_STA_GHOST_ORDER_STATE").close();

                         dhxlSCExtCheck.gridChecks.selectRowById('CMD_STA_GHOST_ORDER_STATE');
                         doCheck(false);
                    });
                }
            }

            function addGhostOrderState()
            {
                var selectedGhostOrderStates = gridGhostOrderState.getSelectedRowId();
                if(selectedGhostOrderStates==null || selectedGhostOrderStates=="")
                    selectedGhostOrderStates = 0;
                if(selectedGhostOrderStates!="0")
                {
                    $.post("index.php?ajax=1&act=all_win-fixmyprestashop_actions&check=CMD_STA_GHOST_ORDER_STATE&id_lang="+SC_ID_LANG, { "action": "add_order_states", "ids": selectedGhostOrderStates}, function(data){
                        dhxlSCExtCheck.tabbar.tabs("table_CMD_STA_GHOST_ORDER_STATE").close();

                         dhxlSCExtCheck.gridChecks.selectRowById('CMD_STA_GHOST_ORDER_STATE');
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
            'title' => _l('Ghost ord. stat.'),
            'contentJs' => $content_js,
    ));
}
elseif (!empty($post_action) && $post_action == 'delete_order_states')
{
    $post_ids = Tools::getValue('ids');
    if (!empty($post_ids))
    {
        $sql = 'DELETE FROM '._DB_PREFIX_.'order_state_lang WHERE id_order_state IN ('.pInSQL($post_ids).')';
        $res = dbExecuteForeignKeyOff($sql);
    }
}
elseif (!empty($post_action) && $post_action == 'add_order_states')
{
    $post_ids = Tools::getValue('ids');
    if (!empty($post_ids))
    {
        $ids = explode(',', $post_ids);
        foreach ($ids as $id)
        {
            $sql = 'INSERT INTO '._DB_PREFIX_.'order_state (id_order_state, color)
                    VALUES ('.(int) $id.",'lightblue')";
            $res = dbExecuteForeignKeyOff($sql);
        }
    }
}
