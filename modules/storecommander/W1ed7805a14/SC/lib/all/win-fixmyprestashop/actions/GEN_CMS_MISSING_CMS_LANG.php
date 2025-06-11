<?php
$post_action = Tools::getValue('action');
if (!empty($post_action) && $post_action == 'do_check')
{
    $res = missingLangGet('cms');

    $content = '';
    $content_js = '';
    $results = 'OK';
    if (!empty($res) && count($res) > 0)
    {
        $results = 'KO';
        ob_start(); ?>
        <script type="text/javascript">
    
            var tbMissingCMSLang = dhxlSCExtCheck.tabbar.cells("table_GEN_CMS_MISSING_CMS_LANG").attachToolbar();
            tbMissingCMSLang.setIconset('awesome');
            tbMissingCMSLang.addButton("selectall", 0, "", 'fa fa-bolt yellow', 'fa fa-bolt yellow');
            tbMissingCMSLang.setItemToolTip('selectall','<?php echo _l('Select all'); ?>');
            tbMissingCMSLang.addButton("delete", 0, "", 'fa fa-minus-circle red', 'fa fa-minus-circle red');
            tbMissingCMSLang.setItemToolTip('delete','<?php echo _l('Delete incomplete CMS'); ?>');
            tbMissingCMSLang.attachEvent("onClick",
                function(id){
                    if (id=='selectall')
                    {
                        gridMissingCMSLang.selectAll();
                        getGridStat_MissingCMSLang();
                    }
                    if (id=='delete')
                    {
                        deleteMissingCMSLang();
                    }
                });
        
            var gridMissingCMSLang = dhxlSCExtCheck.tabbar.cells("table_GEN_CMS_MISSING_CMS_LANG").attachGrid();
            gridMissingCMSLang.setImagePath("lib/js/imgs/");
            gridMissingCMSLang.enableSmartRendering(true);
            gridMissingCMSLang.enableMultiselect(true);
    
            gridMissingCMSLang.setHeader("ID");
            gridMissingCMSLang.setInitWidths("*");
            gridMissingCMSLang.setColAlign("left");
            gridMissingCMSLang.setColTypes("ro");
            gridMissingCMSLang.setColSorting("int");
            gridMissingCMSLang.attachHeader("#numeric_filter");
            gridMissingCMSLang.init();
    
            var xml = '<rows>';
            <?php foreach ($res as $cms) { ?>
            xml = xml+'   <row id="<?php echo $cms['id_cms']; ?>">';
            xml = xml+'      <cell><![CDATA[<?php echo $cms['id_cms']; ?>]]></cell>';
            xml = xml+'   </row>';
            <?php } ?>
            xml = xml+'</rows>';
            gridMissingCMSLang.parse(xml);

            sbMissingCMSLang=dhxlSCExtCheck.tabbar.cells("table_GEN_CMS_MISSING_CMS_LANG").attachStatusBar();
            function getGridStat_MissingCMSLang(){
                var filteredRows=gridMissingCMSLang.getRowsNum();
                var selectedRows=(gridMissingCMSLang.getSelectedRowId()?gridMissingCMSLang.getSelectedRowId().split(',').length:0);
                sbMissingCMSLang.setText('<?php echo count($res).' '._l('Errors'); ?>'+" - <?php echo _l('Filter')._l(':'); ?> "+filteredRows+" - <?php echo _l('Selection')._l(':'); ?> "+selectedRows);
            }
            gridMissingCMSLang.attachEvent("onFilterEnd", function(elements){
                getGridStat_MissingCMSLang();
            });
            gridMissingCMSLang.attachEvent("onSelectStateChanged", function(id){
                getGridStat_MissingCMSLang();
            });
            getGridStat_MissingCMSLang();

            function deleteMissingCMSLang()
            {
                var selectedMissingCMSLangs = gridMissingCMSLang.getSelectedRowId();
                if(selectedMissingCMSLangs==null || selectedMissingCMSLangs=="")
                    selectedMissingCMSLangs = 0;
                if(selectedMissingCMSLangs!="0")
                {
                    $.post("index.php?ajax=1&act=all_win-fixmyprestashop_actions&check=GEN_CMS_MISSING_CMS_LANG&id_lang="+SC_ID_LANG, { "action": "delete_cms", "ids": selectedMissingCMSLangs}, function(data){
                        dhxlSCExtCheck.tabbar.tabs("table_GEN_CMS_MISSING_CMS_LANG").close();

                         dhxlSCExtCheck.gridChecks.selectRowById('GEN_CMS_MISSING_CMS_LANG');
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
            'title' => _l('CMS lang'),
            'contentJs' => $content_js,
    ));
}
elseif (!empty($post_action) && $post_action == 'delete_cms')
{
    $post_ids = Tools::getValue('ids');
    if (!empty($post_ids))
    {
        $ids = explode(',', $post_ids);
        foreach ($ids as $id)
        {
            $cms = new CMS($id);
            $cms->delete();
        }
    }
}
