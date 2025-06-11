<?php
$post_action = Tools::getValue('action');
if (!empty($post_action) && $post_action == 'do_check')
{
    $res = missingLangGet('attribute');

    $content = '';
    $content_js = '';
    $results = 'OK';
    if (!empty($res) && count($res) > 0)
    {
        $results = 'KO';
        ob_start(); ?>
        <script type="text/javascript">

            var tbMissingAttributeLang = dhxlSCExtCheck.tabbar.cells("table_CAT_ATTR_MISSING_ATTRIBUTE_LANG").attachToolbar();
            tbMissingAttributeLang.setIconset('awesome');
            tbMissingAttributeLang.addButton("selectall", 0, "", 'fa fa-bolt yellow', 'fa fa-bolt yellow');
            tbMissingAttributeLang.setItemToolTip('selectall','<?php echo _l('Select all'); ?>');
            tbMissingAttributeLang.addButton("delete", 0, "", 'fa fa-minus-circle red', 'fa fa-minus-circle red');
            tbMissingAttributeLang.setItemToolTip('delete','<?php echo _l('Delete incomplete attributes'); ?>');
            tbMissingAttributeLang.attachEvent("onClick",
                function(id){
                    if (id=='selectall')
                    {
                        gridMissingAttributeLang.selectAll();
                        getGridStat_MissingAttributeLang();
                    }
                    if (id=='delete')
                    {
                        deleteMissingAttributeLang();
                    }
                });

            var gridMissingAttributeLang = dhxlSCExtCheck.tabbar.cells("table_CAT_ATTR_MISSING_ATTRIBUTE_LANG").attachGrid();
            gridMissingAttributeLang.setImagePath("lib/js/imgs/");
            gridMissingAttributeLang.enableSmartRendering(true);
            gridMissingAttributeLang.enableMultiselect(true);

            gridMissingAttributeLang.setHeader("ID,<?php echo _l('Group ID'); ?>,<?php echo _l('Used?'); ?>");
            gridMissingAttributeLang.setInitWidths("100,100,50");
            gridMissingAttributeLang.setColAlign("left,left,left");
            gridMissingAttributeLang.setColTypes("ro,ro,ro");
            gridMissingAttributeLang.setColSorting("int,int,str");
            gridMissingAttributeLang.attachHeader("#numeric_filter,#numeric_filter,#select_filter");
            gridMissingAttributeLang.init();

            var xml = '<rows>';
            <?php foreach ($res as $attribute)
        {
            $sql = 'SELECT * FROM `'._DB_PREFIX_."product_attribute_combination` WHERE id_attribute = '".(int) $attribute['id_attribute']."' LIMIT 1500";
            $is_used = Db::getInstance()->ExecuteS($sql); ?>
            xml = xml+'   <row id="<?php echo $attribute['id_attribute']; ?>">';
            xml = xml+'      <cell><![CDATA[<?php echo $attribute['id_attribute']; ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php echo $attribute['id_attribute_group']; ?>]]></cell>';
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
            gridMissingAttributeLang.parse(xml);

            sbMissingAttributeLang=dhxlSCExtCheck.tabbar.cells("table_CAT_ATTR_MISSING_ATTRIBUTE_LANG").attachStatusBar();
            function getGridStat_MissingAttributeLang(){
                var filteredRows=gridMissingAttributeLang.getRowsNum();
                var selectedRows=(gridMissingAttributeLang.getSelectedRowId()?gridMissingAttributeLang.getSelectedRowId().split(',').length:0);
                sbMissingAttributeLang.setText('<?php echo count($res).' '._l('Errors'); ?>'+" - <?php echo _l('Filter')._l(':'); ?> "+filteredRows+" - <?php echo _l('Selection')._l(':'); ?> "+selectedRows);
            }
            gridMissingAttributeLang.attachEvent("onFilterEnd", function(elements){
                getGridStat_MissingAttributeLang();
            });
            gridMissingAttributeLang.attachEvent("onSelectStateChanged", function(id){
                getGridStat_MissingAttributeLang();
            });
            getGridStat_MissingAttributeLang();

            function deleteMissingAttributeLang()
            {
                var selectedMissingAttributeLangs = gridMissingAttributeLang.getSelectedRowId();
                if(selectedMissingAttributeLangs==null || selectedMissingAttributeLangs=="")
                    selectedMissingAttributeLangs = 0;
                if(selectedMissingAttributeLangs!="0")
                {
                    $.post("index.php?ajax=1&act=all_win-fixmyprestashop_actions&check=CAT_ATTR_MISSING_ATTRIBUTE_LANG&id_lang="+SC_ID_LANG, { "action": "delete_attributes", "ids": selectedMissingAttributeLangs}, function(data){
                        dhxlSCExtCheck.tabbar.tabs("table_CAT_ATTR_MISSING_ATTRIBUTE_LANG").close();

                         dhxlSCExtCheck.gridChecks.selectRowById('CAT_ATTR_MISSING_ATTRIBUTE_LANG');
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
            'title' => _l('Attr. lang'),
            'contentJs' => $content_js,
    ));
}
elseif (!empty($post_action) && $post_action == 'delete_attributes')
{
    $post_ids = Tools::getValue('ids');
    if (!empty($post_ids))
    {
        $ids = explode(',', $post_ids);
        foreach ($ids as $id)
        {
            if (version_compare(_PS_VERSION_, '8.0.0', '>='))
            {
                $attribute = new ProductAttribute($id);
            }
            else
            {
                $attribute = new Attribute($id);
            }
            $attribute->delete();
            if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
            {
                $sql = 'DELETE FROM '._DB_PREFIX_.'attribute WHERE id_attribute = '.(int) $id.';';
                dbExecuteForeignKeyOff($sql);
            }
        }
    }
}
