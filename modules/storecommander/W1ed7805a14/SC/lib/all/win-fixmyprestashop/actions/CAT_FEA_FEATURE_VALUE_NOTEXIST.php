<?php
$post_action = Tools::getValue('action');
if (!empty($post_action) && $post_action == 'do_check')
{
    $sql = 'SELECT id_feature_value, COUNT( id_product ) as NB
    FROM `'._DB_PREFIX_.'feature_product`
    WHERE `id_feature_value` NOT IN ( SELECT id_feature_value FROM `'._DB_PREFIX_.'feature_value` )
    GROUP BY id_feature_value
    ORDER BY id_feature_value
    LIMIT 1500';
    $res = Db::getInstance()->ExecuteS($sql);

    $content = '';
    $content_js = '';
    $results = 'OK';
    if (!empty($res) && count($res) > 0)
    {
        $results = 'KO';
        ob_start(); ?>
        <script type="text/javascript">

            var tbFeatureValueNotExist = dhxlSCExtCheck.tabbar.cells("table_CAT_FEA_FEATURE_VALUE_NOTEXIST").attachToolbar();
            tbFeatureValueNotExist.setIconset('awesome');
            tbFeatureValueNotExist.addButton("selectall", 0, "", 'fa fa-bolt yellow', 'fa fa-bolt yellow');
            tbFeatureValueNotExist.setItemToolTip('selectall','<?php echo _l('Select all'); ?>');
            tbFeatureValueNotExist.addButton("delete", 0, "", 'fa fa-minus-circle red', 'fa fa-minus-circle red');
            tbFeatureValueNotExist.setItemToolTip('delete','<?php echo _l('Delete id_feature_value not existing'); ?>');
            tbFeatureValueNotExist.attachEvent("onClick",
                function(id){
                    if (id=='selectall')
                    {
                        gridFeatureValueNotExist.selectAll();
                        getGridStat_FeatureValueNotExist();
                    }
                    if (id=='delete')
                    {
                        deleteFeatureValueNotExist();
                    }
                });

            var gridFeatureValueNotExist = dhxlSCExtCheck.tabbar.cells("table_CAT_FEA_FEATURE_VALUE_NOTEXIST").attachGrid();
            gridFeatureValueNotExist.setImagePath("lib/js/imgs/");
            gridFeatureValueNotExist.enableSmartRendering(true);
            gridFeatureValueNotExist.enableMultiselect(true);

            gridFeatureValueNotExist.setHeader("id_feature_value,<?php echo _l('Used'); ?>");
            gridFeatureValueNotExist.setInitWidths("100,100");
            gridFeatureValueNotExist.setColAlign("left,left");
            gridFeatureValueNotExist.setColTypes("ro,ro");
            gridFeatureValueNotExist.setColSorting("int,int");
            gridFeatureValueNotExist.attachHeader("#numeric_filter,#numeric_filter");
            gridFeatureValueNotExist.init();

            var xml = '<rows>';
            <?php foreach ($res as $row)
        {
            ?>
            xml = xml+'   <row id="<?php echo $row['id_feature_value']; ?>">';
            xml = xml+'      <cell><![CDATA[<?php echo $row['id_feature_value']; ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php echo $row['NB']; ?>]]></cell>';
            xml = xml+'   </row>';
            <?php
        } ?>
            xml = xml+'</rows>';
            gridFeatureValueNotExist.parse(xml);

            sbFeatureValueNotExist=dhxlSCExtCheck.tabbar.cells("table_CAT_FEA_FEATURE_VALUE_NOTEXIST").attachStatusBar();
            function getGridStat_FeatureValueNotExist(){
                var filteredRows=gridFeatureValueNotExist.getRowsNum();
                var selectedRows=(gridFeatureValueNotExist.getSelectedRowId()?gridFeatureValueNotExist.getSelectedRowId().split(',').length:0);
                sbFeatureValueNotExist.setText('<?php echo count($res).' '._l('Errors'); ?>'+" - <?php echo _l('Filter')._l(':'); ?> "+filteredRows+" - <?php echo _l('Selection')._l(':'); ?> "+selectedRows);
            }
            gridFeatureValueNotExist.attachEvent("onFilterEnd", function(elements){
                getGridStat_FeatureValueNotExist();
            });
            gridFeatureValueNotExist.attachEvent("onSelectStateChanged", function(id){
                getGridStat_FeatureValueNotExist();
            });
            getGridStat_FeatureValueNotExist();

            function deleteFeatureValueNotExist()
            {
                let selectedFeatureValueNotExists = gridFeatureValueNotExist.getSelectedRowId();
                if(selectedFeatureValueNotExists !==null || selectedFeatureValueNotExists !=="")
                {
                    $.post("index.php?ajax=1&act=all_win-fixmyprestashop_actions&check=CAT_FEA_FEATURE_VALUE_NOTEXIST&id_lang="+SC_ID_LANG, { "action": "delete_feature_value_notexist", "ids": selectedFeatureValueNotExists}, function(data){
                        dhxlSCExtCheck.tabbar.tabs("table_CAT_FEA_FEATURE_VALUE_NOTEXIST").close();

                        dhxlSCExtCheck.gridChecks.selectRowById('CAT_FEA_FEATURE_VALUE_NOTEXIST');
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
        'title' => _l('Feat. Val. not exist'),
        'contentJs' => $content_js,
    ));
}
elseif (!empty($post_action) && $post_action == 'delete_feature_value_notexist')
{
    $post_ids = Tools::getValue('ids');
    if (!empty($post_ids) || $post_ids == 0)
    {
        $ids = explode(',', $post_ids);
        foreach ($ids as $id)
        {
            $sql = 'DELETE FROM '._DB_PREFIX_."feature_product WHERE id_feature_value='".(int) $id."'";
            dbExecuteForeignKeyOff($sql);
        }
    }
}
