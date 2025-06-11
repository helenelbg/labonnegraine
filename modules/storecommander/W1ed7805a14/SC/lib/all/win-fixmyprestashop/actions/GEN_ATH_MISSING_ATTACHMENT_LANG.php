<?php
$post_action = Tools::getValue('action');
if (!empty($post_action) && $post_action == 'do_check')
{
    $res = missingLangGet('attachment');

    $content = '';
    $content_js = '';
    $results = 'OK';
    if (!empty($res) && count($res) > 0)
    {
        $results = 'KO';
        ob_start(); ?>
        <script type="text/javascript">
    
            var tbMissingAttachmentLang = dhxlSCExtCheck.tabbar.cells("table_GEN_ATH_MISSING_ATTACHMENT_LANG").attachToolbar();
            tbMissingAttachmentLang.setIconset('awesome');
            tbMissingAttachmentLang.addButton("selectall", 0, "", 'fa fa-bolt yellow', 'fa fa-bolt yellow');
            tbMissingAttachmentLang.setItemToolTip('selectall','<?php echo _l('Select all'); ?>');
            tbMissingAttachmentLang.addButton("delete", 0, "", 'fa fa-minus-circle red', 'fa fa-minus-circle red');
            tbMissingAttachmentLang.setItemToolTip('delete','<?php echo _l('Delete incomplete attachments'); ?>');
            tbMissingAttachmentLang.addButton("add", 0, "", 'fa fa-plus-circle green', 'fa fa-plus-circle green');
            tbMissingAttachmentLang.setItemToolTip('add','<?php echo _l('Recover incomplete attachments'); ?>');
            tbMissingAttachmentLang.attachEvent("onClick",
                function(id){
                    if (id=='selectall')
                    {
                        gridMissingAttachmentLang.selectAll();
                        getGridStat_MissingAttachmentLang();
                    }
                    if (id=='delete')
                    {
                        deleteMissingAttachmentLang();
                    }
                    if (id=='add')
                    {
                        addMissingAttachmentLang()
                    }
                });
        
            var gridMissingAttachmentLang = dhxlSCExtCheck.tabbar.cells("table_GEN_ATH_MISSING_ATTACHMENT_LANG").attachGrid();
            gridMissingAttachmentLang.setImagePath("lib/js/imgs/");
            gridMissingAttachmentLang.enableSmartRendering(true);
            gridMissingAttachmentLang.enableMultiselect(true);
    
            gridMissingAttachmentLang.setHeader("ID,<?php echo _l('Used ?'); ?>");
            gridMissingAttachmentLang.setInitWidths("100,50");
            gridMissingAttachmentLang.setColAlign("left,left");
            gridMissingAttachmentLang.setColTypes("ro,ro");
            gridMissingAttachmentLang.setColSorting("int,str");
            gridMissingAttachmentLang.attachHeader("#numeric_filter,#select_filter");
            gridMissingAttachmentLang.init();
    
            var xml = '<rows>';
            <?php foreach ($res as $attachment)
        {
            $sql = 'SELECT * FROM `'._DB_PREFIX_."orders` WHERE id_attachment = '".(int) $attachment['id_attachment']."'";
            $is_used = Db::getInstance()->ExecuteS($sql); ?>
            xml = xml+'   <row id="<?php echo $attachment['id_attachment']; ?>">';
            xml = xml+'      <cell><![CDATA[<?php echo $attachment['id_attachment']; ?>]]></cell>';
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
            gridMissingAttachmentLang.parse(xml);

            sbMissingAttachmentLang=dhxlSCExtCheck.tabbar.cells("table_GEN_ATH_MISSING_ATTACHMENT_LANG").attachStatusBar();
            function getGridStat_MissingAttachmentLang(){
                var filteredRows=gridMissingAttachmentLang.getRowsNum();
                var selectedRows=(gridMissingAttachmentLang.getSelectedRowId()?gridMissingAttachmentLang.getSelectedRowId().split(',').length:0);
                sbMissingAttachmentLang.setText('<?php echo count($res).' '._l('Errors'); ?>'+" - <?php echo _l('Filter')._l(':'); ?> "+filteredRows+" - <?php echo _l('Selection')._l(':'); ?> "+selectedRows);
            }
            gridMissingAttachmentLang.attachEvent("onFilterEnd", function(elements){
                getGridStat_MissingAttachmentLang();
            });
            gridMissingAttachmentLang.attachEvent("onSelectStateChanged", function(id){
                getGridStat_MissingAttachmentLang();
            });
            getGridStat_MissingAttachmentLang();

            function deleteMissingAttachmentLang()
            {
                var selectedMissingAttachmentLangs = gridMissingAttachmentLang.getSelectedRowId();
                if(selectedMissingAttachmentLangs==null || selectedMissingAttachmentLangs=="")
                    selectedMissingAttachmentLangs = 0;
                if(selectedMissingAttachmentLangs!="0")
                {
                    $.post("index.php?ajax=1&act=all_win-fixmyprestashop_actions&check=GEN_ATH_MISSING_ATTACHMENT_LANG&id_lang="+SC_ID_LANG, { "action": "delete_attachments", "ids": selectedMissingAttachmentLangs}, function(data){
                        dhxlSCExtCheck.tabbar.tabs("table_GEN_ATH_MISSING_ATTACHMENT_LANG").close();

                         dhxlSCExtCheck.gridChecks.selectRowById('GEN_ATH_MISSING_ATTACHMENT_LANG');
                         doCheck(false);
                    });
                }
            }

            function addMissingAttachmentLang()
            {
                var selectedMissingAttachmentLangs = gridMissingAttachmentLang.getSelectedRowId();
                if(selectedMissingAttachmentLangs==null || selectedMissingAttachmentLangs=="")
                    selectedMissingAttachmentLangs = 0;
                if(selectedMissingAttachmentLangs!="0")
                {
                    $.post("index.php?ajax=1&act=all_win-fixmyprestashop_actions&check=GEN_ATH_MISSING_ATTACHMENT_LANG&id_lang="+SC_ID_LANG, { "action": "add_attachments", "ids": selectedMissingAttachmentLangs}, function(data){
                        dhxlSCExtCheck.tabbar.tabs("table_GEN_ATH_MISSING_ATTACHMENT_LANG").close();

                         dhxlSCExtCheck.gridChecks.selectRowById('GEN_ATH_MISSING_ATTACHMENT_LANG');
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
            'title' => _l('Attach. lang'),
            'contentJs' => $content_js,
    ));
}
elseif (!empty($post_action) && $post_action == 'delete_attachments')
{
    $post_ids = Tools::getValue('ids');
    if (!empty($post_ids))
    {
        $ids = explode(',', $post_ids);
        foreach ($ids as $id)
        {
            $attachment = new Attachment($id);
            $attachment->delete();
        }
    }
}
elseif (!empty($post_action) && $post_action == 'add_attachments')
{
    $post_ids = Tools::getValue('ids');
    if (!empty($post_ids))
    {
        $ids = explode(',', $post_ids);
        foreach ($ids as $id)
        {
            $sql = 'SELECT  l.*
                    FROM '._DB_PREFIX_.'lang l
                    WHERE l.id_lang not in (SELECT pl.id_lang FROM '._DB_PREFIX_."attachment_lang pl WHERE pl.id_attachment='".(int) $id."')";
            $languages = Db::getInstance()->ExecuteS($sql);

            foreach ($languages as $language)
            {
                $sql = 'INSERT INTO '._DB_PREFIX_.'attachment_lang (id_attachment, id_lang, name)
                        VALUES ('.(int) $id.','.(int) $language['id_lang'].",'Attachment')";
                $res = dbExecuteForeignKeyOff($sql);
            }
        }
    }
}
