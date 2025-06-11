<?php
$post_action = Tools::getValue('action');
if (!empty($post_action) && $post_action == 'do_check')
{
    $sql = 'select pl.id_attribute_group, pl.name from '._DB_PREFIX_.'attribute_group_lang pl where pl.id_attribute_group not in (select p.id_attribute_group from '._DB_PREFIX_.'attribute_group p) ORDER BY id_lang ASC LIMIT 1500';
    $res = Db::getInstance()->ExecuteS($sql);

    $content = '';
    $content_js = '';
    $results = 'OK';
    if (!empty($res) && count($res) > 0)
    {
        $results = 'KO';
        ob_start(); ?>
        <script type="text/javascript">
    
            var tbGhostAttributeGroup = dhxlSCExtCheck.tabbar.cells("table_CAT_ATTR_GHOST_GROUP_ATTRIBUTE").attachToolbar();
            tbGhostAttributeGroup.setIconset('awesome');
            tbGhostAttributeGroup.addButton("selectall", 0, "", 'fa fa-bolt yellow', 'fa fa-bolt yellow');
            tbGhostAttributeGroup.setItemToolTip('selectall','<?php echo _l('Select all'); ?>');
            tbGhostAttributeGroup.addButton("delete", 0, "", 'fa fa-minus-circle red', 'fa fa-minus-circle red');
            tbGhostAttributeGroup.setItemToolTip('delete','<?php echo _l('Delete incomplete attribute groups'); ?>');
            tbGhostAttributeGroup.addButton("add", 0, "", 'fa fa-plus-circle green', 'fa fa-plus-circle green');
            tbGhostAttributeGroup.setItemToolTip('add','<?php echo _l('Recover incomplete attributes groups'); ?>');
            tbGhostAttributeGroup.attachEvent("onClick",
                function(id){
                    if (id=='selectall')
                    {
                        gridGhostAttributeGroup.selectAll();
                        getGridStat_GhostAttributeGroup();
                    }
                    if (id=='delete')
                    {
                        deleteGhostAttributeGroup();
                    }
                    if (id=='add')
                    {
                        addGhostAttributeGroup()
                    }
                });
        
            var gridGhostAttributeGroup = dhxlSCExtCheck.tabbar.cells("table_CAT_ATTR_GHOST_GROUP_ATTRIBUTE").attachGrid();
            gridGhostAttributeGroup.setImagePath("lib/js/imgs/");
            gridGhostAttributeGroup.enableSmartRendering(true);
            gridGhostAttributeGroup.enableMultiselect(true);
    
            gridGhostAttributeGroup.setHeader("ID,<?php echo _l('Name'); ?>,<?php echo _l('Attributes ?'); ?>,<?php echo _l('Used?'); ?>");
            gridGhostAttributeGroup.setInitWidths("100, 110,100,50");
            gridGhostAttributeGroup.setColAlign("left,left,center,left");
            gridGhostAttributeGroup.setColTypes("ro,ro,ro,ro");
            gridGhostAttributeGroup.setColSorting("int,str,str,str");
            gridGhostAttributeGroup.attachHeader("#numeric_filter,#text_filter,#select_filter,#select_filter");
            gridGhostAttributeGroup.init();

            var xml = '<rows>';
            <?php foreach ($res as $attribute_group)
        {
            $sql = 'SELECT * FROM `'._DB_PREFIX_."product_attribute_group_combination` WHERE id_attribute_group = '".(int) $attribute_group['id_attribute_group']."'";
            $is_used = Db::getInstance()->ExecuteS($sql);

            $sql = 'select p.* from '._DB_PREFIX_."attribute p where p.id_attribute_group = '".(int) $attribute_group['id_attribute_group']."'";
            $attributs = Db::getInstance()->ExecuteS($sql); ?>
            xml = xml+'   <row id="<?php echo $attribute_group['id_attribute_group']; ?>">';
            xml = xml+'      <cell><![CDATA[<?php echo $attribute_group['id_attribute_group']; ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php echo str_replace("'", "\'", $attribute_group['name']); ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php if (!empty($attributs) && count($attributs) > 0)
            {
                echo _l('Yes');
            }
            else
            {
                echo _l('No');
            } ?>]]></cell>';
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
            gridGhostAttributeGroup.parse(xml);

            sbGhostAttributeGroup=dhxlSCExtCheck.tabbar.cells("table_CAT_ATTR_GHOST_GROUP_ATTRIBUTE").attachStatusBar();
            function getGridStat_GhostAttributeGroup(){
                var filteredRows=gridGhostAttributeGroup.getRowsNum();
                var selectedRows=(gridGhostAttributeGroup.getSelectedRowId()?gridGhostAttributeGroup.getSelectedRowId().split(',').length:0);
                sbGhostAttributeGroup.setText('<?php echo count($res).' '._l('Errors'); ?>'+" - <?php echo _l('Filter')._l(':'); ?> "+filteredRows+" - <?php echo _l('Selection')._l(':'); ?> "+selectedRows);
            }
            gridGhostAttributeGroup.attachEvent("onFilterEnd", function(elements){
                getGridStat_GhostAttributeGroup();
            });
            gridGhostAttributeGroup.attachEvent("onSelectStateChanged", function(id){
                getGridStat_GhostAttributeGroup();
            });
            getGridStat_GhostAttributeGroup();

            function deleteGhostAttributeGroup()
            {
                var selectedGhostAttributeGroups = gridGhostAttributeGroup.getSelectedRowId();
                if(selectedGhostAttributeGroups==null || selectedGhostAttributeGroups=="")
                    selectedGhostAttributeGroups = 0;
                if(selectedGhostAttributeGroups!="0")
                {
                    $.post("index.php?ajax=1&act=all_win-fixmyprestashop_actions&check=CAT_ATTR_GHOST_GROUP_ATTRIBUTE&id_lang="+SC_ID_LANG, { "action": "delete_attribute_groups", "ids": selectedGhostAttributeGroups}, function(data){
                        dhxlSCExtCheck.tabbar.tabs("table_CAT_ATTR_GHOST_GROUP_ATTRIBUTE").close();

                         dhxlSCExtCheck.gridChecks.selectRowById('CAT_ATTR_GHOST_GROUP_ATTRIBUTE');
                         doCheck(false);
                    });
                }
            }

            function addGhostAttributeGroup()
            {
                var selectedGhostAttributeGroups = gridGhostAttributeGroup.getSelectedRowId();
                if(selectedGhostAttributeGroups==null || selectedGhostAttributeGroups=="")
                    selectedGhostAttributeGroups = 0;
                if(selectedGhostAttributeGroups!="0")
                {
                    $.post("index.php?ajax=1&act=all_win-fixmyprestashop_actions&check=CAT_ATTR_GHOST_GROUP_ATTRIBUTE&id_lang="+SC_ID_LANG, { "action": "add_attribute_groups", "ids": selectedGhostAttributeGroups}, function(data){
                        dhxlSCExtCheck.tabbar.tabs("table_CAT_ATTR_GHOST_GROUP_ATTRIBUTE").close();

                         dhxlSCExtCheck.gridChecks.selectRowById('CAT_ATTR_GHOST_GROUP_ATTRIBUTE');
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
            'title' => _l('Ghost group attr.'),
            'contentJs' => $content_js,
    ));
}
elseif (!empty($post_action) && $post_action == 'delete_attribute_groups')
{
    $post_ids = Tools::getValue('ids');
    if (!empty($post_ids))
    {
        $sql = 'DELETE FROM '._DB_PREFIX_.'attribute_group_lang WHERE id_attribute_group IN ('.pInSQL($post_ids).')';
        $res = dbExecuteForeignKeyOff($sql);
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
            $has_color = 0;
            $sql = 'select p.* from '._DB_PREFIX_."attribute p where p.id_attribute_group = '".(int) $id."'";
            $attributs = Db::getInstance()->ExecuteS($sql);
            foreach ($attributs as $attribut)
            {
                if (!empty($attribut['color']))
                {
                    $has_color = 1;
                }
            }

            $sql = 'INSERT INTO '._DB_PREFIX_.'attribute_group (id_attribute_group, is_color_group)
                    VALUES ('.(int) $id.','.psql($has_color).')';
            $res = dbExecuteForeignKeyOff($sql);

            Module::hookExec('afterSaveAttributeGroup', array('id_attribute_group' => $id));
        }
    }
}
