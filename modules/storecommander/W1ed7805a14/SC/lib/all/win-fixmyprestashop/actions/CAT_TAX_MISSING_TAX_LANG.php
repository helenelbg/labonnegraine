<?php
$post_action = Tools::getValue('action');
if (!empty($post_action) && $post_action == 'do_check')
{
    $res = missingLangGet('tax');

    $content = '';
    $content_js = '';
    $results = 'OK';
    if (!empty($res) && count($res) > 0)
    {
        $results = 'KO';
        ob_start(); ?>
        <script type="text/javascript">
    
            var tbMissingTaxLang = dhxlSCExtCheck.tabbar.cells("table_CAT_TAX_MISSING_TAX_LANG").attachToolbar();
            tbMissingTaxLang.setIconset('awesome');
            tbMissingTaxLang.addButton("selectall", 0, "", 'fa fa-bolt yellow', 'fa fa-bolt yellow');
            tbMissingTaxLang.setItemToolTip('selectall','<?php echo _l('Select all'); ?>');
            tbMissingTaxLang.addButton("delete", 0, "", 'fa fa-minus-circle red', 'fa fa-minus-circle red');
            tbMissingTaxLang.setItemToolTip('delete','<?php echo _l('Delete incomplete taxes'); ?>');
            tbMissingTaxLang.addButton("add", 0, "", 'fa fa-plus-circle green', 'fa fa-plus-circle green');
            tbMissingTaxLang.setItemToolTip('add','<?php echo _l('Recover incomplete taxes'); ?>');
            tbMissingTaxLang.attachEvent("onClick",
                function(id){
                    if (id=='selectall')
                    {
                        gridMissingTaxLang.selectAll();
                        getGridStat_MissingTaxLang();
                    }
                    if (id=='delete')
                    {
                        deleteMissingTaxLang();
                    }
                    if (id=='add')
                    {
                        addMissingTaxLang()
                    }
                });
        
            var gridMissingTaxLang = dhxlSCExtCheck.tabbar.cells("table_CAT_TAX_MISSING_TAX_LANG").attachGrid();
            gridMissingTaxLang.setImagePath("lib/js/imgs/");
            gridMissingTaxLang.enableSmartRendering(true);
            gridMissingTaxLang.enableMultiselect(true);
    
            gridMissingTaxLang.setHeader("ID,<?php echo _l('Used?'); ?>");
            gridMissingTaxLang.setInitWidths("100,50");
            gridMissingTaxLang.setColAlign("left,left");
            gridMissingTaxLang.setColTypes("ro,ro");
            gridMissingTaxLang.setColSorting("int,str");
            gridMissingTaxLang.attachHeader("#numeric_filter,#select_filter");
            gridMissingTaxLang.init();
    
            var xml = '<rows>';
            <?php foreach ($res as $tax)
        {
            $sql = 'SELECT * FROM `'._DB_PREFIX_."tax_rule` WHERE id_tax = '".(int) $tax['id_tax']."' LIMIT 1";
            $is_used = Db::getInstance()->ExecuteS($sql); ?>
            xml = xml+'   <row id="<?php echo $tax['id_tax']; ?>">';
            xml = xml+'      <cell><![CDATA[<?php echo $tax['id_tax']; ?>]]></cell>';
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
            gridMissingTaxLang.parse(xml);

            sbMissingTaxLang=dhxlSCExtCheck.tabbar.cells("table_CAT_TAX_MISSING_TAX_LANG").attachStatusBar();
            function getGridStat_MissingTaxLang(){
                var filteredRows=gridMissingTaxLang.getRowsNum();
                var selectedRows=(gridMissingTaxLang.getSelectedRowId()?gridMissingTaxLang.getSelectedRowId().split(',').length:0);
                sbMissingTaxLang.setText('<?php echo count($res).' '._l('Errors'); ?>'+" - <?php echo _l('Filter')._l(':'); ?> "+filteredRows+" - <?php echo _l('Selection')._l(':'); ?> "+selectedRows);
            }
            gridMissingTaxLang.attachEvent("onFilterEnd", function(elements){
                getGridStat_MissingTaxLang();
            });
            gridMissingTaxLang.attachEvent("onSelectStateChanged", function(id){
                getGridStat_MissingTaxLang();
            });
            getGridStat_MissingTaxLang();

            function deleteMissingTaxLang()
            {
                var selectedMissingTaxLangs = gridMissingTaxLang.getSelectedRowId();
                if(selectedMissingTaxLangs==null || selectedMissingTaxLangs=="")
                    selectedMissingTaxLangs = 0;
                if(selectedMissingTaxLangs!="0")
                {
                    $.post("index.php?ajax=1&act=all_win-fixmyprestashop_actions&check=CAT_TAX_MISSING_TAX_LANG&id_lang="+SC_ID_LANG, { "action": "delete_taxs", "ids": selectedMissingTaxLangs}, function(data){
                        dhxlSCExtCheck.tabbar.tabs("table_CAT_TAX_MISSING_TAX_LANG").close();

                         dhxlSCExtCheck.gridChecks.selectRowById('CAT_TAX_MISSING_TAX_LANG');
                         doCheck(false);
                    });
                }
            }

            function addMissingTaxLang()
            {
                var selectedMissingTaxLangs = gridMissingTaxLang.getSelectedRowId();
                if(selectedMissingTaxLangs==null || selectedMissingTaxLangs=="")
                    selectedMissingTaxLangs = 0;
                if(selectedMissingTaxLangs!="0")
                {
                    $.post("index.php?ajax=1&act=all_win-fixmyprestashop_actions&check=CAT_TAX_MISSING_TAX_LANG&id_lang="+SC_ID_LANG, { "action": "add_taxs", "ids": selectedMissingTaxLangs}, function(data){
                        dhxlSCExtCheck.tabbar.tabs("table_CAT_TAX_MISSING_TAX_LANG").close();

                         dhxlSCExtCheck.gridChecks.selectRowById('CAT_TAX_MISSING_TAX_LANG');
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
            'title' => _l('Tax lang'),
            'contentJs' => $content_js,
    ));
}
elseif (!empty($post_action) && $post_action == 'delete_taxs')
{
    $post_ids = Tools::getValue('ids');
    if (!empty($post_ids))
    {
        $ids = explode(',', $post_ids);
        foreach ($ids as $id)
        {
            $tax = new Tax((int) $id);
            $tax->delete();
        }
    }
}
elseif (!empty($post_action) && $post_action == 'add_taxs')
{
    $post_ids = Tools::getValue('ids');
    if (!empty($post_ids))
    {
        $ids = explode(',', $post_ids);
        foreach ($ids as $id)
        {
            $sql = 'SELECT  l.*
                    FROM '._DB_PREFIX_.'lang l
                    WHERE l.id_lang not in (SELECT pl.id_lang FROM '._DB_PREFIX_."tax_lang pl WHERE pl.id_tax='".(int) $id."')";
            $languages = Db::getInstance()->ExecuteS($sql);

            foreach ($languages as $language)
            {
                $sql = 'INSERT INTO '._DB_PREFIX_.'tax_lang (id_tax, id_lang, name)
                        VALUES ('.(int) $id.','.(int) $language['id_lang'].",'Tax')";
                $res = dbExecuteForeignKeyOff($sql);
            }
        }
    }
}
