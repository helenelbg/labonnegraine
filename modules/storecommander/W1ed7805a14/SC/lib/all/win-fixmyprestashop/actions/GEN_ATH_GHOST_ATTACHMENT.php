<?php
$post_action = Tools::getValue('action');
if (!empty($post_action) && $post_action == 'do_check')
{
    $sql = 'SELECT pl.id_attachment, pl.name FROM '._DB_PREFIX_.'attachment_lang pl WHERE pl.id_attachment NOT IN (SELECT p.id_attachment FROM '._DB_PREFIX_.'attachment p) ORDER BY id_lang ASC LIMIT 1500';
    $res = Db::getInstance()->ExecuteS($sql);

    $content = '';
    $content_js = '';
    $results = 'OK';
    if (!empty($res) && count($res) > 0)
    {
        $results = 'KO';
        ob_start(); ?>
        <script type="text/javascript">
    
            var tbGhostAttachment = dhxlSCExtCheck.tabbar.cells("table_GEN_ATH_GHOST_ATTACHMENT").attachToolbar();
            tbGhostAttachment.setIconset('awesome');
            tbGhostAttachment.addButton("selectall", 0, "", 'fa fa-bolt yellow', 'fa fa-bolt yellow');
            tbGhostAttachment.setItemToolTip('selectall','<?php echo _l('Select all'); ?>');
            tbGhostAttachment.addButton("delete", 0, "", 'fa fa-minus-circle red', 'fa fa-minus-circle red');
            tbGhostAttachment.setItemToolTip('delete','<?php echo _l('Delete incomplete attachments'); ?>');
            tbGhostAttachment.attachEvent("onClick",
                function(id){
                    if (id=='selectall')
                    {
                        gridGhostAttachment.selectAll();
                        getGridStat_GhostAttachment();
                    }
                    if (id=='delete')
                    {
                        deleteGhostAttachment();
                    }
                });
        
            var gridGhostAttachment = dhxlSCExtCheck.tabbar.cells("table_GEN_ATH_GHOST_ATTACHMENT").attachGrid();
            gridGhostAttachment.setImagePath("lib/js/imgs/");
            gridGhostAttachment.enableSmartRendering(true);
            gridGhostAttachment.enableMultiselect(true);
    
            gridGhostAttachment.setHeader("ID,<?php echo _l('Name'); ?>,<?php echo _l('Used ?'); ?>");
            gridGhostAttachment.setInitWidths("100, 110,50");
            gridGhostAttachment.setColAlign("left,left,left");
            gridGhostAttachment.setColTypes("ro,ro,ro");
            gridGhostAttachment.setColSorting("int,str,str");
            gridGhostAttachment.attachHeader("#numeric_filter,#text_filter,#select_filter");
            gridGhostAttachment.init();

            var xml = '<rows>';
            <?php foreach ($res as $attachment)
        {
            $sql = 'SELECT * FROM `'._DB_PREFIX_."orders` WHERE id_attachment = '".(int) $attachment['id_attachment']."' LIMIT 1";
            $is_used = Db::getInstance()->ExecuteS($sql); ?>
            xml = xml+'   <row id="<?php echo $attachment['id_attachment']; ?>">';
            xml = xml+'      <cell><![CDATA[<?php echo $attachment['id_attachment']; ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php echo str_replace("'", "\'", $attachment['name']); ?>]]></cell>';
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
            gridGhostAttachment.parse(xml);

            sbGhostAttachment=dhxlSCExtCheck.tabbar.cells("table_GEN_ATH_GHOST_ATTACHMENT").attachStatusBar();
            function getGridStat_GhostAttachment(){
                var filteredRows=gridGhostAttachment.getRowsNum();
                var selectedRows=(gridGhostAttachment.getSelectedRowId()?gridGhostAttachment.getSelectedRowId().split(',').length:0);
                sbGhostAttachment.setText('<?php echo count($res).' '._l('Errors'); ?>'+" - <?php echo _l('Filter')._l(':'); ?> "+filteredRows+" - <?php echo _l('Selection')._l(':'); ?> "+selectedRows);
            }
            gridGhostAttachment.attachEvent("onFilterEnd", function(elements){
                getGridStat_GhostAttachment();
            });
            gridGhostAttachment.attachEvent("onSelectStateChanged", function(id){
                getGridStat_GhostAttachment();
            });
            getGridStat_GhostAttachment();

            function deleteGhostAttachment()
            {
                var selectedGhostAttachments = gridGhostAttachment.getSelectedRowId();
                if(selectedGhostAttachments==null || selectedGhostAttachments=="")
                    selectedGhostAttachments = 0;
                if(selectedGhostAttachments!="0")
                {
                    $.post("index.php?ajax=1&act=all_win-fixmyprestashop_actions&check=GEN_ATH_GHOST_ATTACHMENT&id_lang="+SC_ID_LANG, { "action": "delete_attachments", "ids": selectedGhostAttachments}, function(data){
                        dhxlSCExtCheck.tabbar.tabs("table_GEN_ATH_GHOST_ATTACHMENT").close();

                         dhxlSCExtCheck.gridChecks.selectRowById('GEN_ATH_GHOST_ATTACHMENT');
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
            'title' => _l('Ghost attach.'),
            'contentJs' => $content_js,
    ));
}
elseif (!empty($post_action) && $post_action == 'delete_attachments')
{
    $post_ids = Tools::getValue('ids');
    if (!empty($post_ids))
    {
        $sql = 'DELETE FROM '._DB_PREFIX_.'attachment_lang WHERE id_attachment IN ('.pInSQL($post_ids).')';
        $res = dbExecuteForeignKeyOff($sql);
    }
}
