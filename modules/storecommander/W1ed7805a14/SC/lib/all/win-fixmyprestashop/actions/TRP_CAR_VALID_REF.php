<?php
$post_action = Tools::getValue('action');
if (!empty($post_action) && $post_action == 'do_check')
{
    $sql = 'SELECT cc.id_carrier, cc.id_reference, cc.name 
            FROM '._DB_PREFIX_.'carrier cc
            WHERE cc.id_reference = 0
            OR cc.id_reference NOT IN (SELECT id_carrier FROM '._DB_PREFIX_.'carrier WHERE id_carrier = cc.id_reference)';
    $res = Db::getInstance()->ExecuteS($sql);

    $content = '';
    $content_js = '';
    $results = 'OK';
    if (!empty($res) && count($res) > 0)
    {
        $results = 'KO';
        ob_start(); ?>
        <script type="text/javascript">

            var tbRefCarrier = dhxlSCExtCheck.tabbar.cells("table_TRP_CAR_VALID_REF").attachToolbar();
            tbRefCarrier.setIconset('awesome');
            tbRefCarrier.addButton("selectall", 0, "", 'fa fa-bolt yellow', 'fa fa-bolt yellow');
            tbRefCarrier.setItemToolTip('selectall','<?php echo _l('Select all'); ?>');
            tbRefCarrier.addButton("addIdReference", 0, "", 'fa fa-plus-circle green', 'fa fa-plus-circle green');
            tbRefCarrier.setItemToolTip('addIdReference','<?php echo _l('Recover a valid id_reference'); ?>');
            tbRefCarrier.attachEvent("onClick",
                function(id){
                    if (id=='selectall')
                    {
                        gridRefCarrier.selectAll();
                        getGridStat_RefCarrier();
                    }

                    if (id=='addIdReference')
                    {
                        addIdReference()
                    }
                });

            var gridRefCarrier = dhxlSCExtCheck.tabbar.cells("table_TRP_CAR_VALID_REF").attachGrid();
            gridRefCarrier.setImagePath("lib/js/imgs/");
            gridRefCarrier.enableSmartRendering(true);
            gridRefCarrier.enableMultiselect(true);

            gridRefCarrier.setHeader("ID,<?php echo _l('Name'); ?>,<?php echo _l('Used ?'); ?>");
            gridRefCarrier.setInitWidths("100, 110,70");
            gridRefCarrier.setColAlign("left,left,left");
            gridRefCarrier.setColTypes("ro,ro,ro");
            gridRefCarrier.setColSorting("int,str,str");
            gridRefCarrier.attachHeader("#numeric_filter,#text_filter,#select_filter");
            gridRefCarrier.init();

            var xml = '<rows>';
            <?php foreach ($res as $carrier)
        {
            $sql = 'SELECT * FROM `'._DB_PREFIX_."orders` WHERE id_carrier = '".(int) $carrier['id_carrier']."' LIMIT 1";
            $is_used = Db::getInstance()->ExecuteS($sql); ?>
            xml = xml+'   <row id="<?php echo $carrier['id_carrier']; ?>">';
            xml = xml+'      <cell><![CDATA[<?php echo $carrier['id_carrier']; ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php echo str_replace("'", "\'", $carrier['name']); ?>]]></cell>';
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
            gridRefCarrier.parse(xml);

            sbRefCarrier=dhxlSCExtCheck.tabbar.cells("table_TRP_CAR_VALID_REF").attachStatusBar();
            function getGridStat_RefCarrier(){
                var filteredRows=gridRefCarrier.getRowsNum();
                var selectedRows=(gridRefCarrier.getSelectedRowId()?gridRefCarrier.getSelectedRowId().split(',').length:0);
                sbRefCarrier.setText('<?php echo count($res).' '._l('Errors'); ?>'+" - <?php echo _l('Filter')._l(':'); ?> "+filteredRows+" - <?php echo _l('Selection')._l(':'); ?> "+selectedRows);
            }
            gridRefCarrier.attachEvent("onFilterEnd", function(elements){
                getGridStat_RefCarrier();
            });
            gridRefCarrier.attachEvent("onSelectStateChanged", function(id){
                getGridStat_RefCarrier();
            });
            getGridStat_RefCarrier();

            function addIdReference()
            {
                var selectedRefCarriers = gridRefCarrier.getSelectedRowId();
                if(selectedRefCarriers==null || selectedRefCarriers=="")
                    selectedRefCarriers = 0;
                if(selectedRefCarriers!="0")
                {
                    $.post("index.php?ajax=1&act=all_win-fixmyprestashop_actions&check=TRP_CAR_VALID_REF&id_lang="+SC_ID_LANG, { "action": "add_id_reference", "ids": selectedRefCarriers}, function(data){
                        dhxlSCExtCheck.tabbar.tabs("table_TRP_CAR_VALID_REF").close();

                        dhxlSCExtCheck.gridChecks.selectRowById('TRP_CAR_VALID_REF');
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
        'title' => _l('Carrier ref.'),
        'contentJs' => $content_js,
    ));
}
elseif (!empty($post_action) && $post_action == 'add_id_reference')
{
    $post_ids = Tools::getValue('ids');
    if (!empty($post_ids))
    {
        $ids = explode(',', $post_ids);
        foreach ($ids as $id)
        {
            $sql = 'UPDATE '._DB_PREFIX_.'carrier
                    SET id_reference = '.(int) $id.'
                    WHERE id_carrier = '.(int) $id;
            $res = dbExecuteForeignKeyOff($sql);
        }
    }
}
