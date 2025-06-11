<?php
$post_action = Tools::getValue('action');
if (!empty($post_action) && $post_action == 'do_check')
{
    $res = missingLangGet('attribute_group');

    $content = '';
    $content_js = '';
    $results = 'OK';
    if (!empty($res) && count($res) > 0)
    {
        $results = 'KO';
        ob_start(); ?>
        <script type="text/javascript">
    
            var tbMissingAttributeGroupLang = dhxlSCExtCheck.tabbar.cells("table_CAT_ATTR_MISSING_GROUP_ATTRIBUTE_LANG").attachToolbar();
            tbMissingAttributeGroupLang.setIconset('awesome');
            tbMissingAttributeGroupLang.addButton("selectall", 0, "", 'fa fa-bolt yellow', 'fa fa-bolt yellow');
            tbMissingAttributeGroupLang.setItemToolTip('selectall','<?php echo _l('Select all'); ?>');
            tbMissingAttributeGroupLang.addButton("delete", 0, "", 'fa fa-minus-circle red', 'fa fa-minus-circle red');
            tbMissingAttributeGroupLang.setItemToolTip('delete','<?php echo _l('Delete incomplete attribute_groups'); ?>');
            tbMissingAttributeGroupLang.addButton("add", 0, "", 'fa fa-plus-circle green', 'fa fa-plus-circle green');
            tbMissingAttributeGroupLang.setItemToolTip('add','<?php echo _l('Recover incomplete attributes groups'); ?>');
            tbMissingAttributeGroupLang.attachEvent("onClick",
                function(id){
                    if (id=='selectall')
                    {
                        gridMissingAttributeGroupLang.selectAll();
                        getGridStat_MissingAttributeGroupLang();
                    }
                    if (id=='delete')
                    {
                        deleteMissingAttributeGroupLang();
                    }
                    if (id=='add')
                    {
                        addMissingAttributeGroupLang()
                    }
                });
        
            var gridMissingAttributeGroupLang = dhxlSCExtCheck.tabbar.cells("table_CAT_ATTR_MISSING_GROUP_ATTRIBUTE_LANG").attachGrid();
            gridMissingAttributeGroupLang.setImagePath("lib/js/imgs/");
            gridMissingAttributeGroupLang.enableSmartRendering(true);
            gridMissingAttributeGroupLang.enableMultiselect(true);
    
            gridMissingAttributeGroupLang.setHeader("ID,<?php echo _l('Used?'); ?>");
            gridMissingAttributeGroupLang.setInitWidths("100,50");
            gridMissingAttributeGroupLang.setColAlign("left,left");
            gridMissingAttributeGroupLang.setColTypes("ro,ro");
            gridMissingAttributeGroupLang.setColSorting("int,str");
            gridMissingAttributeGroupLang.attachHeader("#numeric_filter,#select_filter");
            gridMissingAttributeGroupLang.init();
    
            var xml = '<rows>';
            <?php foreach ($res as $attribute_group)
        {
            $sql = 'SELECT pac.* 
                FROM `'._DB_PREFIX_.'product_attribute_combination` pac 
                    INNER JOIN '._DB_PREFIX_."attribute a ON a.id_attribute = pac.id_attribute
                WHERE a.id_attribute_group = '".(int) $attribute_group['id_attribute_group']."' LIMIT 1500";
            $is_used = Db::getInstance()->ExecuteS($sql); ?>
            xml = xml+'   <row id="<?php echo $attribute_group['id_attribute_group']; ?>">';
            xml = xml+'      <cell><![CDATA[<?php echo $attribute_group['id_attribute_group']; ?>]]></cell>';
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
            gridMissingAttributeGroupLang.parse(xml);

            sbMissingAttributeGroupLang=dhxlSCExtCheck.tabbar.cells("table_CAT_ATTR_MISSING_GROUP_ATTRIBUTE_LANG").attachStatusBar();
            function getGridStat_MissingAttributeGroupLang(){
                var filteredRows=gridMissingAttributeGroupLang.getRowsNum();
                var selectedRows=(gridMissingAttributeGroupLang.getSelectedRowId()?gridMissingAttributeGroupLang.getSelectedRowId().split(',').length:0);
                sbMissingAttributeGroupLang.setText('<?php echo count($res).' '._l('Errors'); ?>'+" - <?php echo _l('Filter')._l(':'); ?> "+filteredRows+" - <?php echo _l('Selection')._l(':'); ?> "+selectedRows);
            }
            gridMissingAttributeGroupLang.attachEvent("onFilterEnd", function(elements){
                getGridStat_MissingAttributeGroupLang();
            });
            gridMissingAttributeGroupLang.attachEvent("onSelectStateChanged", function(id){
                getGridStat_MissingAttributeGroupLang();
            });
            getGridStat_MissingAttributeGroupLang();

            function deleteMissingAttributeGroupLang()
            {
                var selectedMissingAttributeGroupLangs = gridMissingAttributeGroupLang.getSelectedRowId();
                if(selectedMissingAttributeGroupLangs==null || selectedMissingAttributeGroupLangs=="")
                    selectedMissingAttributeGroupLangs = 0;
                if(selectedMissingAttributeGroupLangs!="0")
                {
                    $.post("index.php?ajax=1&act=all_win-fixmyprestashop_actions&check=CAT_ATTR_MISSING_GROUP_ATTRIBUTE_LANG&id_lang="+SC_ID_LANG, { "action": "delete_attribute_groups", "ids": selectedMissingAttributeGroupLangs}, function(data){
                        dhxlSCExtCheck.tabbar.tabs("table_CAT_ATTR_MISSING_GROUP_ATTRIBUTE_LANG").close();

                         dhxlSCExtCheck.gridChecks.selectRowById('CAT_ATTR_MISSING_GROUP_ATTRIBUTE_LANG');
                         doCheck(false);
                    });
                }
            }

            function addMissingAttributeGroupLang()
            {
                var selectedMissingAttributeGroupLangs = gridMissingAttributeGroupLang.getSelectedRowId();
                if(selectedMissingAttributeGroupLangs==null || selectedMissingAttributeGroupLangs=="")
                    selectedMissingAttributeGroupLangs = 0;
                if(selectedMissingAttributeGroupLangs!="0")
                {
                    $.post("index.php?ajax=1&act=all_win-fixmyprestashop_actions&check=CAT_ATTR_MISSING_GROUP_ATTRIBUTE_LANG&id_lang="+SC_ID_LANG, { "action": "add_attribute_groups", "ids": selectedMissingAttributeGroupLangs}, function(data){
                        dhxlSCExtCheck.tabbar.tabs("table_CAT_ATTR_MISSING_GROUP_ATTRIBUTE_LANG").close();

                         dhxlSCExtCheck.gridChecks.selectRowById('CAT_ATTR_MISSING_GROUP_ATTRIBUTE_LANG');
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
            'title' => _l('Attr. Group lg.'),
            'contentJs' => $content_js,
    ));
}
elseif (!empty($post_action) && $post_action == 'delete_attribute_groups')
{
    $post_ids = Tools::getValue('ids');
    if (!empty($post_ids))
    {
        $ids = explode(',', $post_ids);
        foreach ($ids as $id)
        {
            $attribute_group = new AttributeGroup($id);
            $attribute_group->delete();
            if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
            {
                $sql = 'DELETE FROM '._DB_PREFIX_.'attribute_group WHERE id_attribute_group = '.(int) $id;
                dbExecuteForeignKeyOff($sql);
            }
        }
    }
}
elseif (!empty($post_action) && $post_action == 'add_attribute_groups')
{
    $post_ids = Tools::getValue('ids');
    if (!empty($post_ids))
    {
        $ids = explode(',', $post_ids);
        foreach ($ids as $id)
        {
            $sql = 'SELECT  l.*
                    FROM '._DB_PREFIX_.'lang l
                    WHERE l.id_lang not in (SELECT pl.id_lang FROM '._DB_PREFIX_."attribute_group_lang pl WHERE pl.id_attribute_group='".(int) $id."')";
            $languages = Db::getInstance()->ExecuteS($sql);

            foreach ($languages as $language)
            {
                $sql = 'INSERT INTO '._DB_PREFIX_.'attribute_group_lang (id_attribute_group, id_lang, name, public_name)
                        VALUES ('.(int) $id.','.(int) $language['id_lang'].",'Attribute group','Attribute group')";
                $res = dbExecuteForeignKeyOff($sql);
            }
        }
    }
}
