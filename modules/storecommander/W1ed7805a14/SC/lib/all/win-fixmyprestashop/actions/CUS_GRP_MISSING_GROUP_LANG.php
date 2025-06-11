<?php
$post_action = Tools::getValue('action');
if (!empty($post_action) && $post_action == 'do_check')
{
    $res = missingLangGet('group');

    $content = '';
    $content_js = '';
    $results = 'OK';
    if (!empty($res) && count($res) > 0)
    {
        $results = 'KO';
        ob_start(); ?>
        <script type="text/javascript">
    
            var tbMissingGroupLang = dhxlSCExtCheck.tabbar.cells("table_CUS_GRP_MISSING_GROUP_LANG").attachToolbar();
            tbMissingGroupLang.setIconset('awesome');
            tbMissingGroupLang.addButton("selectall", 0, "", 'fa fa-bolt yellow', 'fa fa-bolt yellow');
            tbMissingGroupLang.setItemToolTip('selectall','<?php echo _l('Select all'); ?>');
            tbMissingGroupLang.addButton("delete", 0, "", 'fa fa-minus-circle red', 'fa fa-minus-circle red');
            tbMissingGroupLang.setItemToolTip('delete','<?php echo _l('Delete incomplete groups'); ?>');
            tbMissingGroupLang.addButton("add", 0, "", 'fa fa-plus-circle green', 'fa fa-plus-circle green');
            tbMissingGroupLang.setItemToolTip('add','<?php echo _l('Recover incomplete groups'); ?>');
            tbMissingGroupLang.attachEvent("onClick",
                function(id){
                    if (id=='selectall')
                    {
                        gridMissingGroupLang.selectAll();
                    }
                    if (id=='delete')
                    {
                        deleteMissingGroupLang();
                    }
                    if (id=='add')
                    {
                        addMissingGroupLang()
                    }
                });
        
            var gridMissingGroupLang = dhxlSCExtCheck.tabbar.cells("table_CUS_GRP_MISSING_GROUP_LANG").attachGrid();
            gridMissingGroupLang.setImagePath("lib/js/imgs/");
            gridMissingGroupLang.enableSmartRendering(true);
            gridMissingGroupLang.enableMultiselect(true);
    
            gridMissingGroupLang.setHeader("ID,<?php echo _l('Used ?'); ?>");
            gridMissingGroupLang.setInitWidths("100,50");
            gridMissingGroupLang.setColAlign("left,left");
            gridMissingGroupLang.setColTypes("ro,ro");
            gridMissingGroupLang.setColSorting("int,str");
            gridMissingGroupLang.attachHeader("#numeric_filter,#select_filter");
            gridMissingGroupLang.init();
    
            var xml = '<rows>';
            <?php foreach ($res as $group)
        {
            $sql = 'SELECT * FROM `'._DB_PREFIX_."customer` WHERE id_default_group = '".(int) $group['id_group']."' LIMIT 1";
            $is_default_used = Db::getInstance()->ExecuteS($sql);
            $sql = 'SELECT * FROM `'._DB_PREFIX_."customer_group` WHERE id_group = '".(int) $group['id_group']."' LIMIT 1";
            $is_used = Db::getInstance()->ExecuteS($sql); ?>
            xml = xml+'   <row id="<?php echo $group['id_group']; ?>">';
            xml = xml+'      <cell><![CDATA[<?php echo $group['id_group']; ?>]]></cell>';
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
            gridMissingGroupLang.parse(xml);

            sbMissingGroupLang=dhxlSCExtCheck.tabbar.cells("table_CUS_GRP_MISSING_GROUP_LANG").attachStatusBar();
            function getGridStat_MissingGroupLang(){
                var filteredRows=gridMissingGroupLang.getRowsNum();
                var selectedRows=(gridMissingGroupLang.getSelectedRowId()?gridMissingGroupLang.getSelectedRowId().split(',').length:0);
                sbMissingGroupLang.setText('<?php echo count($res).' '._l('Errors'); ?>'+" - <?php echo _l('Filter')._l(':'); ?> "+filteredRows+" - <?php echo _l('Selection')._l(':'); ?> "+selectedRows);
            }
            gridMissingGroupLang.attachEvent("onFilterEnd", function(elements){
                getGridStat_MissingGroupLang();
            });
            gridMissingGroupLang.attachEvent("onSelectStateChanged", function(id){
                getGridStat_MissingGroupLang();
            });
            getGridStat_MissingGroupLang();

            function deleteMissingGroupLang()
            {
                var selectedMissingGroupLangs = gridMissingGroupLang.getSelectedRowId();
                if(selectedMissingGroupLangs==null || selectedMissingGroupLangs=="")
                    selectedMissingGroupLangs = 0;
                if(selectedMissingGroupLangs!="0")
                {
                    $.post("index.php?ajax=1&act=all_win-fixmyprestashop_actions&check=CUS_GRP_MISSING_GROUP_LANG&id_lang="+SC_ID_LANG, { "action": "delete_groups", "ids": selectedMissingGroupLangs}, function(data){
                        dhxlSCExtCheck.tabbar.tabs("table_CUS_GRP_MISSING_GROUP_LANG").close();

                         dhxlSCExtCheck.gridChecks.selectRowById('CUS_GRP_MISSING_GROUP_LANG');
                         doCheck(false);
                    });
                }
            }

            function addMissingGroupLang()
            {
                var selectedMissingGroupLangs = gridMissingGroupLang.getSelectedRowId();
                if(selectedMissingGroupLangs==null || selectedMissingGroupLangs=="")
                    selectedMissingGroupLangs = 0;
                if(selectedMissingGroupLangs!="0")
                {
                    $.post("index.php?ajax=1&act=all_win-fixmyprestashop_actions&check=CUS_GRP_MISSING_GROUP_LANG&id_lang="+SC_ID_LANG, { "action": "add_groups", "ids": selectedMissingGroupLangs}, function(data){
                        dhxlSCExtCheck.tabbar.tabs("table_CUS_GRP_MISSING_GROUP_LANG").close();

                         dhxlSCExtCheck.gridChecks.selectRowById('CUS_GRP_MISSING_GROUP_LANG');
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
            'title' => _l('Group lang'),
            'contentJs' => $content_js,
    ));
}
elseif (!empty($post_action) && $post_action == 'delete_groups')
{
    $post_ids = Tools::getValue('ids');
    if (!empty($post_ids))
    {
        $ids = explode(',', $post_ids);
        foreach ($ids as $id)
        {
            $group = new Group($id);
            $group->delete();
        }
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
            $sql = 'SELECT  l.*
                    FROM '._DB_PREFIX_.'lang l
                    WHERE l.id_lang not in (SELECT pl.id_lang FROM '._DB_PREFIX_."group_lang pl WHERE pl.id_group='".(int) $id."')";
            $languages = Db::getInstance()->ExecuteS($sql);

            foreach ($languages as $language)
            {
                $sql = 'INSERT INTO '._DB_PREFIX_.'group_lang (id_group, id_lang, name)
                        VALUES ('.(int) $id.','.(int) $language['id_lang'].",'Group')";
                $res = dbExecuteForeignKeyOff($sql);
            }
        }
    }
}
