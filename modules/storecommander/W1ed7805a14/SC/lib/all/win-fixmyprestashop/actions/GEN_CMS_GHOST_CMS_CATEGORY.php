<?php
$post_action = Tools::getValue('action');
if (!empty($post_action) && $post_action == 'do_check')
{
    $sql = 'SELECT pl.id_cms_category, pl.name FROM '._DB_PREFIX_.'cms_category_lang pl WHERE pl.id_cms_category NOT IN (SELECT p.id_cms_category FROM '._DB_PREFIX_.'cms_category p) ORDER BY id_lang ASC LIMIT 1500';
    $res = Db::getInstance()->ExecuteS($sql);

    $content = '';
    $content_js = '';
    $results = 'OK';
    if (!empty($res) && count($res) > 0)
    {
        $results = 'KO';
        ob_start(); ?>
        <script type="text/javascript">
    
            var tbGhostCmsBlock = dhxlSCExtCheck.tabbar.cells("table_GEN_CMS_GHOST_CMS_CATEGORY").attachToolbar();
            tbGhostCmsBlock.setIconset('awesome');
            tbGhostCmsBlock.addButton("selectall", 0, "", 'fa fa-bolt yellow', 'fa fa-bolt yellow');
            tbGhostCmsBlock.setItemToolTip('selectall','<?php echo _l('Select all'); ?>');
            tbGhostCmsBlock.addButton("delete", 0, "", 'fa fa-minus-circle red', 'fa fa-minus-circle red');
            tbGhostCmsBlock.setItemToolTip('delete','<?php echo _l('Delete incomplete CMS categories'); ?>');
            tbGhostCmsBlock.addButton("add", 0, "", 'fa fa-plus-circle green', 'fa fa-plus-circle green');
            tbGhostCmsBlock.setItemToolTip('add','<?php echo _l('Recover incomplete CMS categories'); ?>');
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
        
            var gridGhostCmsBlock = dhxlSCExtCheck.tabbar.cells("table_GEN_CMS_GHOST_CMS_CATEGORY").attachGrid();
            gridGhostCmsBlock.setImagePath("lib/js/imgs/");
            gridGhostCmsBlock.enableSmartRendering(true);
            gridGhostCmsBlock.enableMultiselect(true);
    
            gridGhostCmsBlock.setHeader("ID,<?php echo _l('Name'); ?>,<?php echo _l('Used ?'); ?>");
            gridGhostCmsBlock.setInitWidths("100,110,50");
            gridGhostCmsBlock.setColAlign("left,left,left");
            gridGhostCmsBlock.setColTypes("ro,ro,ro");
            gridGhostCmsBlock.setColSorting("int,str,str");
            gridGhostCmsBlock.attachHeader("#numeric_filter,#text_filter,#select_filter");
            gridGhostCmsBlock.init();

            var xml = '<rows>';
            <?php foreach ($res as $cms_category)
        {
            $sql = 'SELECT * FROM `'._DB_PREFIX_."cms` WHERE id_cms_category = '".(int) $cms_category['id_cms_category']."'";
            $is_used = Db::getInstance()->ExecuteS($sql); ?>
            xml = xml+'   <row id="<?php echo $cms_category['id_cms_category']; ?>">';
            xml = xml+'      <cell><![CDATA[<?php echo $cms_category['id_cms_category']; ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php echo str_replace("'", "\'", $cms_category['name']); ?>]]></cell>';
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
            gridGhostCmsBlock.parse(xml);

            sbGhostCmsBlock=dhxlSCExtCheck.tabbar.cells("table_GEN_CMS_GHOST_CMS_CATEGORY").attachStatusBar();
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
                    $.post("index.php?ajax=1&act=all_win-fixmyprestashop_actions&check=GEN_CMS_GHOST_CMS_CATEGORY&id_lang="+SC_ID_LANG, { "action": "delete_cms_categories", "ids": selectedGhostCmsBlocks}, function(data){
                        dhxlSCExtCheck.tabbar.tabs("table_GEN_CMS_GHOST_CMS_CATEGORY").close();

                         dhxlSCExtCheck.gridChecks.selectRowById('GEN_CMS_GHOST_CMS_CATEGORY');
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
                    $.post("index.php?ajax=1&act=all_win-fixmyprestashop_actions&check=GEN_CMS_GHOST_CMS_CATEGORY&id_lang="+SC_ID_LANG, { "action": "add_cms_categories", "ids": selectedGhostCmsBlocks}, function(data){
                        dhxlSCExtCheck.tabbar.tabs("table_GEN_CMS_GHOST_CMS_CATEGORY").close();

                         dhxlSCExtCheck.gridChecks.selectRowById('GEN_CMS_GHOST_CMS_CATEGORY');
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
            'title' => _l('Ghost CMS cat.'),
            'contentJs' => $content_js,
    ));
}
elseif (!empty($post_action) && $post_action == 'delete_cms_categories')
{
    $post_ids = Tools::getValue('ids');
    if (!empty($post_ids))
    {
        $sql = 'DELETE FROM '._DB_PREFIX_.'cms_category_lang WHERE id_cms_category IN ('.pInSQL($post_ids).')';
        $res = dbExecuteForeignKeyOff($sql);
    }
}
elseif (!empty($post_action) && $post_action == 'add_cms_categories')
{
    $post_ids = Tools::getValue('ids');
    if (!empty($post_ids))
    {
        $ids = explode(',', $post_ids);
        foreach ($ids as $id)
        {
            $sql = 'INSERT INTO '._DB_PREFIX_.'cms_category (id_cms_category, active, date_add, date_upd)
                    VALUES ('.(int) $id.",0, '".date('Y-m-d H:i:s')."', '".date('Y-m-d H:i:s')."')";
            $res = dbExecuteForeignKeyOff($sql);
        }
    }
}
