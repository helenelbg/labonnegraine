<?php
$post_action = Tools::getValue('action');
if (!empty($post_action) && $post_action == 'do_check')
{
    $res = missingLangGet('feature_value');

    $content = '';
    $content_js = '';
    $results = 'OK';
    if (!empty($res) && count($res) > 0)
    {
        $results = 'KO';
        ob_start(); ?>
        <script type="text/javascript">
    
            var tbMissingFeatureValueLang = dhxlSCExtCheck.tabbar.cells("table_CAT_FEA_MISSING_FEATURE_VALUE_LANG").attachToolbar();
            tbMissingFeatureValueLang.setIconset('awesome');
            tbMissingFeatureValueLang.addButton("selectall", 0, "", 'fa fa-bolt yellow', 'fa fa-bolt yellow');
            tbMissingFeatureValueLang.setItemToolTip('selectall','<?php echo _l('Select all'); ?>');
            tbMissingFeatureValueLang.addButton("delete", 0, "", 'fa fa-minus-circle red', 'fa fa-minus-circle red');
            tbMissingFeatureValueLang.setItemToolTip('delete','<?php echo _l('Delete incomplete feature values'); ?>');
            tbMissingFeatureValueLang.addButton("add", 0, "", 'fa fa-plus-circle green', 'fa fa-plus-circle green');
            tbMissingFeatureValueLang.setItemToolTip('add','<?php echo _l('Recover incomplete feature values'); ?>');
            tbMissingFeatureValueLang.attachEvent("onClick",
                function(id){
                    if (id=='selectall')
                    {
                        gridMissingFeatureValueLang.selectAll();
                        getGridStat_MissingFeatureValueLang();
                    }
                    if (id=='delete')
                    {
                        deleteMissingFeatureValueLang();
                    }
                    if (id=='add')
                    {
                        addMissingFeatureValueLang()
                    }
                });
        
            var gridMissingFeatureValueLang = dhxlSCExtCheck.tabbar.cells("table_CAT_FEA_MISSING_FEATURE_VALUE_LANG").attachGrid();
            gridMissingFeatureValueLang.setImagePath("lib/js/imgs/");
            gridMissingFeatureValueLang.enableSmartRendering(true);
            gridMissingFeatureValueLang.enableMultiselect(true);
    
            gridMissingFeatureValueLang.setHeader("ID,<?php echo _l('Used?'); ?>");
            gridMissingFeatureValueLang.setInitWidths("100,50");
            gridMissingFeatureValueLang.setColAlign("left,left");
            gridMissingFeatureValueLang.setColTypes("ro,ro");
            gridMissingFeatureValueLang.setColSorting("int,str");
            gridMissingFeatureValueLang.attachHeader("#numeric_filter,#select_filter");
            gridMissingFeatureValueLang.init();
    
            var xml = '<rows>';
            <?php foreach ($res as $feature_value)
        {
            $sql = 'SELECT * FROM `'._DB_PREFIX_."feature_product` WHERE id_feature_value = '".(int) $feature_value['id_feature_value']."' LIMIT 1";
            $is_used = Db::getInstance()->ExecuteS($sql); ?>
            xml = xml+'   <row id="<?php echo $feature_value['id_feature_value']; ?>">';
            xml = xml+'      <cell><![CDATA[<?php echo $feature_value['id_feature_value']; ?>]]></cell>';
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
            gridMissingFeatureValueLang.parse(xml);

            sbMissingFeatureValueLang=dhxlSCExtCheck.tabbar.cells("table_CAT_FEA_MISSING_FEATURE_VALUE_LANG").attachStatusBar();
            function getGridStat_MissingFeatureValueLang(){
                var filteredRows=gridMissingFeatureValueLang.getRowsNum();
                var selectedRows=(gridMissingFeatureValueLang.getSelectedRowId()?gridMissingFeatureValueLang.getSelectedRowId().split(',').length:0);
                sbMissingFeatureValueLang.setText('<?php echo count($res).' '._l('Errors'); ?>'+" - <?php echo _l('Filter')._l(':'); ?> "+filteredRows+" - <?php echo _l('Selection')._l(':'); ?> "+selectedRows);
            }
            gridMissingFeatureValueLang.attachEvent("onFilterEnd", function(elements){
                getGridStat_MissingFeatureValueLang();
            });
            gridMissingFeatureValueLang.attachEvent("onSelectStateChanged", function(id){
                getGridStat_MissingFeatureValueLang();
            });
            getGridStat_MissingFeatureValueLang();

            function deleteMissingFeatureValueLang()
            {
                var selectedMissingFeatureValueLangs = gridMissingFeatureValueLang.getSelectedRowId();
                if(selectedMissingFeatureValueLangs==null || selectedMissingFeatureValueLangs=="")
                    selectedMissingFeatureValueLangs = 0;
                if(selectedMissingFeatureValueLangs!="0")
                {
                    $.post("index.php?ajax=1&act=all_win-fixmyprestashop_actions&check=CAT_FEA_MISSING_FEATURE_VALUE_LANG&id_lang="+SC_ID_LANG, { "action": "delete_feature_values", "ids": selectedMissingFeatureValueLangs}, function(data){
                        dhxlSCExtCheck.tabbar.tabs("table_CAT_FEA_MISSING_FEATURE_VALUE_LANG").close();

                         dhxlSCExtCheck.gridChecks.selectRowById('CAT_FEA_MISSING_FEATURE_VALUE_LANG');
                         doCheck(false);
                    });
                }
            }

            function addMissingFeatureValueLang()
            {
                var selectedMissingFeatureValueLangs = gridMissingFeatureValueLang.getSelectedRowId();
                if(selectedMissingFeatureValueLangs==null || selectedMissingFeatureValueLangs=="")
                    selectedMissingFeatureValueLangs = 0;
                if(selectedMissingFeatureValueLangs!="0")
                {
                    $.post("index.php?ajax=1&act=all_win-fixmyprestashop_actions&check=CAT_FEA_MISSING_FEATURE_VALUE_LANG&id_lang="+SC_ID_LANG, { "action": "add_feature_values", "ids": selectedMissingFeatureValueLangs}, function(data){
                        dhxlSCExtCheck.tabbar.tabs("table_CAT_FEA_MISSING_FEATURE_VALUE_LANG").close();

                         dhxlSCExtCheck.gridChecks.selectRowById('CAT_FEA_MISSING_FEATURE_VALUE_LANG');
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
            'title' => _l('Feat. val. lang'),
            'contentJs' => $content_js,
    ));
}
elseif (!empty($post_action) && $post_action == 'delete_feature_values')
{
    $post_ids = Tools::getValue('ids');
    if (!empty($post_ids))
    {
        $ids = explode(',', $post_ids);
        foreach ($ids as $id)
        {
            $feature_value = new FeatureValue($id);
            $feature_value->delete();
            if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
            {
                $sql = 'DELETE FROM '._DB_PREFIX_.'feature_value WHERE id_feature_value = '.(int) $id;
                dbExecuteForeignKeyOff($sql);
            }
        }
    }
}
elseif (!empty($post_action) && $post_action == 'add_feature_values')
{
    $post_ids = Tools::getValue('ids');
    if (!empty($post_ids))
    {
        $ids = explode(',', $post_ids);
        foreach ($ids as $id)
        {
            $sql = 'SELECT  l.*
                    FROM '._DB_PREFIX_.'lang l
                    WHERE l.id_lang not in (SELECT pl.id_lang FROM '._DB_PREFIX_."feature_value_lang pl WHERE pl.id_feature_value='".(int) $id."')";
            $languages = Db::getInstance()->ExecuteS($sql);

            foreach ($languages as $language)
            {
                $sql = 'INSERT INTO '._DB_PREFIX_.'feature_value_lang (id_feature_value, id_lang, value)
                        VALUES ('.(int) $id.','.(int) $language['id_lang'].",'Feature value')";
                $res = dbExecuteForeignKeyOff($sql);
            }
        }
    }
}
