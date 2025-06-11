<?php
$post_action = Tools::getValue('action');
if (!empty($post_action) && $post_action == 'do_check')
{
    $sql = 'SELECT o.id_order, o.current_state
            FROM '._DB_PREFIX_.'orders o 
            WHERE o.current_state != (SELECT oh.id_order_state FROM '._DB_PREFIX_.'order_history oh WHERE oh.id_order = o.id_order ORDER BY oh.date_add DESC LIMIT 1) 
            ORDER BY id_lang ASC LIMIT 1500';
    $res = Db::getInstance()->ExecuteS($sql);

    $content = '';
    $content_js = '';
    $results = 'OK';
    if (!empty($res) && count($res) > 0)
    {
        $results = 'KO';
        ob_start(); ?>
        <script type="text/javascript">
    
            var tbCurrentState = dhxlSCExtCheck.tabbar.cells("table_CMD_STA_CURRENT_STATE").attachToolbar();
            tbCurrentState.setIconset('awesome');
            tbCurrentState.addButton("selectall", 0, "", 'fa fa-bolt yellow', 'fa fa-bolt yellow');
            tbCurrentState.setItemToolTip('selectall','<?php echo _l('Select all'); ?>');
            tbCurrentState.addButton("replace", 0, "", 'fad fa-sliders-v-square yellow', 'fad fa-sliders-v-square yellow');
            tbCurrentState.setItemToolTip('replace','<?php echo _l('Update order with good state'); ?>');
            tbCurrentState.attachEvent("onClick",
                function(id){
                    if (id=='selectall')
                    {
                        gridCurrentState.selectAll();
                        getGridStat_CurrentState();
                    }
                    if (id=='replace')
                    {
                        replaceCurrentState();
                    }
                });
        
            var gridCurrentState = dhxlSCExtCheck.tabbar.cells("table_CMD_STA_CURRENT_STATE").attachGrid();
            gridCurrentState.setImagePath("lib/js/imgs/");
            gridCurrentState.enableSmartRendering(true);
            gridCurrentState.enableMultiselect(true);
    
            gridCurrentState.setHeader("<?php echo _l('Order'); ?>,<?php echo _l('Wrong state'); ?>,<?php echo _l('Actual state'); ?>");
            gridCurrentState.setInitWidths("60, 120,150");
            gridCurrentState.setColAlign("left,left,left");
            gridCurrentState.setColTypes("ro,ro,ro");
            gridCurrentState.setColSorting("int,str,str");
            gridCurrentState.attachHeader("#numeric_filter,#text_filter,#text_filter");
            gridCurrentState.init();

            var xml = '<rows>';
            <?php foreach ($res as $order)
        {
            $good_state = '';
            $good_state_id = '';
            $sql = 'SELECT oh.id_order_state FROM '._DB_PREFIX_."order_history oh WHERE oh.id_order = '".(int) $order['id_order']."' ORDER BY oh.date_add DESC LIMIT 1";
            $temp = Db::getInstance()->ExecuteS($sql);
            if (!empty($temp[0]['id_order_state']))
            {
                $good_state_id = $temp[0]['id_order_state'];
                $temp = new OrderState((int) $temp[0]['id_order_state'], (int) $id_lang);
                if (!empty($temp->name))
                {
                    $good_state = $temp->name;
                }
            }

            $wrong_state = '';
            $temp = new OrderState((int) $order['current_state'], (int) $id_lang);
            if (!empty($temp->name))
            {
                $wrong_state = $temp->name;
            } ?>
            xml = xml+'   <row id="<?php echo $order['id_order'].'_'.$good_state_id; ?>">';
            xml = xml+'      <cell><![CDATA[<?php echo $order['id_order']; ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php echo str_replace("'", "\'", $wrong_state); ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php echo str_replace("'", "\'", $good_state); ?>]]></cell>';
            xml = xml+'   </row>';
            <?php
        } ?>
            xml = xml+'</rows>';
            gridCurrentState.parse(xml);

            sbCurrentState=dhxlSCExtCheck.tabbar.cells("table_CMD_STA_CURRENT_STATE").attachStatusBar();
            function getGridStat_CurrentState(){
                var filteredRows=gridCurrentState.getRowsNum();
                var selectedRows=(gridCurrentState.getSelectedRowId()?gridCurrentState.getSelectedRowId().split(',').length:0);
                sbCurrentState.setText('<?php echo count($res).' '._l('Errors'); ?>'+" - <?php echo _l('Filter')._l(':'); ?> "+filteredRows+" - <?php echo _l('Selection')._l(':'); ?> "+selectedRows);
            }
            gridCurrentState.attachEvent("onFilterEnd", function(elements){
                getGridStat_CurrentState();
            });
            gridCurrentState.attachEvent("onSelectStateChanged", function(id){
                getGridStat_CurrentState();
            });
            getGridStat_CurrentState();

            function replaceCurrentState()
            {
                var selectedCurrentStates = gridCurrentState.getSelectedRowId();
                if(selectedCurrentStates==null || selectedCurrentStates=="")
                    selectedCurrentStates = 0;
                if(selectedCurrentStates!="0")
                {
                    $.post("index.php?ajax=1&act=all_win-fixmyprestashop_actions&check=CMD_STA_CURRENT_STATE&id_lang="+SC_ID_LANG, { "action": "replace", "ids": selectedCurrentStates}, function(data){
                        dhxlSCExtCheck.tabbar.tabs("table_CMD_STA_CURRENT_STATE").close();

                         dhxlSCExtCheck.gridChecks.selectRowById('CMD_STA_CURRENT_STATE');
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
            'title' => _l('Current state'),
            'contentJs' => $content_js,
    ));
}
elseif (!empty($post_action) && $post_action == 'replace')
{
    $post_ids = Tools::getValue('ids');
    if (!empty($post_ids))
    {
        $ids = explode(',', $post_ids);
        foreach ($ids as $id)
        {
            list($id_order, $id_state) = explode('_', $id);

            if (!empty($id_state))
            {
                $sql = 'UPDATE '._DB_PREFIX_."orders SET current_state = '".(int) $id_state."' WHERE id_order = ".(int) $id_order;
                dbExecuteForeignKeyOff($sql);
            }
        }
    }
}
