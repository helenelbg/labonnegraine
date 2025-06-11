<?php
$post_action = Tools::getValue('action');
if (!empty($post_action) && $post_action == 'do_check')
{
    $sql = 'select pl.id_cms_block, pl.name from '._DB_PREFIX_.'cms_block_lang pl where pl.id_cms_block not in (select p.id_cms_block from '._DB_PREFIX_.'cms_block p) ORDER BY id_lang ASC LIMIT 1500';
    $res = Db::getInstance()->ExecuteS($sql);

    $content = '';
    $content_js = '';
    $results = 'OK';
    if (!empty($res) && count($res) > 0)
    {
        $results = 'KO';
        ob_start(); ?>
        <script type="text/javascript">
    
            var tbGhostCmsBlock = dhxlSCExtCheck.tabbar.cells("table_GEN_CMS_GHOST_CMS_BLOCK").attachToolbar();
            tbGhostCmsBlock.setIconset('awesome');
            tbGhostCmsBlock.addButton("selectall", 0, "", 'fa fa-bolt yellow', 'fa fa-bolt yellow');
            tbGhostCmsBlock.setItemToolTip('selectall','<?php echo _l('Select all'); ?>');
            tbGhostCmsBlock.addButton("delete", 0, "", 'fa fa-minus-circle red', 'fa fa-minus-circle red');
            tbGhostCmsBlock.setItemToolTip('delete','<?php echo _l('Delete incomplete CMS blocks'); ?>');
            tbGhostCmsBlock.addButton("add", 0, "", 'fa fa-plus-circle green', 'fa fa-plus-circle green');
            tbGhostCmsBlock.setItemToolTip('add','<?php echo _l('Recover incomplete CMS blocks'); ?>');
            tbGhostCmsBlock.attachEvent("onClick",
                function(id){
                    if (id=='selectall')
                    {
                        gridGhostCmsBlock.selectAll();
                        getGridStat_GhostCmsBlock();
                    }
                    if (id=='delete')
                    {
                        deleteGhostCmsBlock();
                    }
                    if (id=='add')
                    {
                        addGhostCmsBlock()
                    }
                });
        
            var gridGhostCmsBlock = dhxlSCExtCheck.tabbar.cells("table_GEN_CMS_GHOST_CMS_BLOCK").attachGrid();
            gridGhostCmsBlock.setImagePath("lib/js/imgs/");
            gridGhostCmsBlock.enableSmartRendering(true);
            gridGhostCmsBlock.enableMultiselect(true);
    
            gridGhostCmsBlock.setHeader("ID,<?php echo _l('Name'); ?>");
            gridGhostCmsBlock.setInitWidths("100, *");
            gridGhostCmsBlock.setColAlign("left,left");
            gridGhostCmsBlock.setColTypes("ro,ro");
            gridGhostCmsBlock.setColSorting("int,str");
            gridGhostCmsBlock.attachHeader("#numeric_filter,#text_filter");
            gridGhostCmsBlock.init();

            var xml = '<rows>';
            <?php foreach ($res as $cms_block) { ?>
            xml = xml+'   <row id="<?php echo $cms_block['id_cms_block']; ?>">';
            xml = xml+'      <cell><![CDATA[<?php echo $cms_block['id_cms_block']; ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php echo str_replace("'", "\'", $cms_block['name']); ?>]]></cell>';
            xml = xml+'   </row>';
            <?php } ?>
            xml = xml+'</rows>';
            gridGhostCmsBlock.parse(xml);

            sbGhostCmsBlock=dhxlSCExtCheck.tabbar.cells("table_GEN_CMS_GHOST_CMS_BLOCK").attachStatusBar();
            function getGridStat_GhostCmsBlock(){
                var filteredRows=gridGhostCmsBlock.getRowsNum();
                var selectedRows=(gridGhostCmsBlock.getSelectedRowId()?gridGhostCmsBlock.getSelectedRowId().split(',').length:0);
                sbGhostCmsBlock.setText('<?php echo count($res).' '._l('Errors'); ?>'+" - <?php echo _l('Filter')._l(':'); ?> "+filteredRows+" - <?php echo _l('Selection')._l(':'); ?> "+selectedRows);
            }
            gridGhostCmsBlock.attachEvent("onFilterEnd", function(elements){
                getGridStat_GhostCmsBlock();
            });
            gridGhostCmsBlock.attachEvent("onSelectStateChanged", function(id){
                getGridStat_GhostCmsBlock();
            });
            getGridStat_GhostCmsBlock();

            function deleteGhostCmsBlock()
            {
                var selectedGhostCmsBlocks = gridGhostCmsBlock.getSelectedRowId();
                if(selectedGhostCmsBlocks==null || selectedGhostCmsBlocks=="")
                    selectedGhostCmsBlocks = 0;
                if(selectedGhostCmsBlocks!="0")
                {
                    $.post("index.php?ajax=1&act=all_win-fixmyprestashop_actions&check=GEN_CMS_GHOST_CMS_BLOCK&id_lang="+SC_ID_LANG, { "action": "delete_cms_blocks", "ids": selectedGhostCmsBlocks}, function(data){
                        dhxlSCExtCheck.tabbar.tabs("table_GEN_CMS_GHOST_CMS_BLOCK").close();

                         dhxlSCExtCheck.gridChecks.selectRowById('GEN_CMS_GHOST_CMS_BLOCK');
                         doCheck(false);
                    });
                }
            }

            function addGhostCmsBlock()
            {
                var selectedGhostCmsBlocks = gridGhostCmsBlock.getSelectedRowId();
                if(selectedGhostCmsBlocks==null || selectedGhostCmsBlocks=="")
                    selectedGhostCmsBlocks = 0;
                if(selectedGhostCmsBlocks!="0")
                {
                    $.post("index.php?ajax=1&act=all_win-fixmyprestashop_actions&check=GEN_CMS_GHOST_CMS_BLOCK&id_lang="+SC_ID_LANG, { "action": "add_cms_blocks", "ids": selectedGhostCmsBlocks}, function(data){
                        dhxlSCExtCheck.tabbar.tabs("table_GEN_CMS_GHOST_CMS_BLOCK").close();

                         dhxlSCExtCheck.gridChecks.selectRowById('GEN_CMS_GHOST_CMS_BLOCK');
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
            'title' => _l('Ghost CMS block'),
            'contentJs' => $content_js,
    ));
}
elseif (!empty($post_action) && $post_action == 'delete_cms_blocks')
{
    $post_ids = Tools::getValue('ids');
    if (!empty($post_ids))
    {
        $sql = 'DELETE FROM '._DB_PREFIX_.'cms_block_lang WHERE id_cms_block IN ('.pInSQL($post_ids).')';
        $res = dbExecuteForeignKeyOff($sql);
    }
}
elseif (!empty($post_action) && $post_action == 'add_cms_blocks')
{
    $post_ids = Tools::getValue('ids');
    if (!empty($post_ids))
    {
        $sql = 'SELECT id_cms_category FROM '._DB_PREFIX_.'cms_category WHERE id_parent = 0 ORDER BY position LIMIT 1';
        $res = Db::getInstance()->ExecuteS($sql);
        if (!empty($res[0]['id_cms_category']))
        {
            $id_cms_category = $res[0]['id_cms_category'];

            $ids = explode(',', $post_ids);
            foreach ($ids as $id)
            {
                $sql = 'INSERT INTO '._DB_PREFIX_.'cms_block (id_cms_block,id_cms_category,location,position)
                        VALUES ('.(int) $id.','.(int) $id_cms_category.',0,0)';
                $res = dbExecuteForeignKeyOff($sql);
            }
        }
    }
}
