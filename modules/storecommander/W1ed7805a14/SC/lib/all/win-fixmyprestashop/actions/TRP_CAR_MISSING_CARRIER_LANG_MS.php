<?php
$post_action = Tools::getValue('action');
if (!empty($post_action) && $post_action == 'do_check')
{
    $res = missingLangMSGet('carrier');

    $content = '';
    $content_js = '';
    $results = 'OK';
    if (!empty($res) && count($res) > 0)
    {
        $results = 'KO';
        ob_start(); ?>
        <script type="text/javascript">
    
            var tbMissingCarrierLang = dhxlSCExtCheck.tabbar.cells("table_TRP_CAR_MISSING_CARRIER_LANG_MS").attachToolbar();
            tbMissingCarrierLang.setIconset('awesome');
            tbMissingCarrierLang.addButton("selectall", 0, "", 'fa fa-bolt yellow', 'fa fa-bolt yellow');
            tbMissingCarrierLang.setItemToolTip('selectall','<?php echo _l('Select all'); ?>');
            tbMissingCarrierLang.addButton("delete", 0, "", 'fa fa-minus-circle red', 'fa fa-minus-circle red');
            tbMissingCarrierLang.setItemToolTip('delete','<?php echo _l('Remove carriers from shops'); ?>');
            tbMissingCarrierLang.addButton("add", 0, "", 'fa fa-plus-circle green', 'fa fa-plus-circle green');
            tbMissingCarrierLang.setItemToolTip('add','<?php echo _l('Recover incomplete carriers'); ?>');
            tbMissingCarrierLang.attachEvent("onClick",
                function(id){
                    if (id=='selectall')
                    {
                        gridMissingCarrierLang.selectAll();
                        getGridStat_MissingCarrierLang();
                    }
                    if (id=='delete')
                    {
                        deleteMissingCarrierLang();
                    }
                    if (id=='add')
                    {
                        addMissingCarrierLang()
                    }
                });
        
            var gridMissingCarrierLang = dhxlSCExtCheck.tabbar.cells("table_TRP_CAR_MISSING_CARRIER_LANG_MS").attachGrid();
            gridMissingCarrierLang.setImagePath("lib/js/imgs/");
            gridMissingCarrierLang.enableSmartRendering(true);
            gridMissingCarrierLang.enableMultiselect(true);
    
            gridMissingCarrierLang.setHeader("ID,<?php echo _l('Used ?'); ?>,<?php echo _l('Shop'); ?>");
            gridMissingCarrierLang.setInitWidths("100,100,200");
            gridMissingCarrierLang.setColAlign("left,left,left");
            gridMissingCarrierLang.setColTypes("ro,ro,ro");
            gridMissingCarrierLang.setColSorting("int,str,str");
            gridMissingCarrierLang.attachHeader("#numeric_filter,#select_filter,#text_filter");
            gridMissingCarrierLang.init();
    
            var xml = '<rows>';
            <?php foreach ($res as $carrier)
        {
            $sql = 'SELECT * FROM `'._DB_PREFIX_."orders` WHERE id_carrier = '".(int) $carrier['id_carrier']."' LIMIT 1";
            $is_used = Db::getInstance()->ExecuteS($sql); ?>
            xml = xml+'   <row id="<?php echo $carrier['id_carrier'].'_'.$carrier['id_shop']; ?>">';
            xml = xml+'      <cell><![CDATA[<?php echo $carrier['id_carrier']; ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php if (!empty($is_used) && count($is_used) > 0)
            {
                echo _l('Yes');
            }
            else
            {
                echo _l('No');
            } ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php echo str_replace("'", "\'", $carrier['shop_name']).' (#'.$carrier['id_shop'].')'; ?>]]></cell>';
            xml = xml+'   </row>';
            <?php
        } ?>
            xml = xml+'</rows>';
            gridMissingCarrierLang.parse(xml);

            sbMissingCarrierLang=dhxlSCExtCheck.tabbar.cells("table_TRP_CAR_MISSING_CARRIER_LANG_MS").attachStatusBar();
            function getGridStat_MissingCarrierLang(){
                var filteredRows=gridMissingCarrierLang.getRowsNum();
                var selectedRows=(gridMissingCarrierLang.getSelectedRowId()?gridMissingCarrierLang.getSelectedRowId().split(',').length:0);
                sbMissingCarrierLang.setText('<?php echo count($res).' '._l('Errors'); ?>'+" - <?php echo _l('Filter')._l(':'); ?> "+filteredRows+" - <?php echo _l('Selection')._l(':'); ?> "+selectedRows);
            }
            gridMissingCarrierLang.attachEvent("onFilterEnd", function(elements){
                getGridStat_MissingCarrierLang();
            });
            gridMissingCarrierLang.attachEvent("onSelectStateChanged", function(id){
                getGridStat_MissingCarrierLang();
            });
            getGridStat_MissingCarrierLang();

            function deleteMissingCarrierLang()
            {
                var selectedMissingCarrierLangs = gridMissingCarrierLang.getSelectedRowId();
                if(selectedMissingCarrierLangs==null || selectedMissingCarrierLangs=="")
                    selectedMissingCarrierLangs = 0;
                if(selectedMissingCarrierLangs!="0")
                {
                    $.post("index.php?ajax=1&act=all_win-fixmyprestashop_actions&check=TRP_CAR_MISSING_CARRIER_LANG_MS&id_lang="+SC_ID_LANG, { "action": "delete_carriers", "ids": selectedMissingCarrierLangs}, function(data){
                        dhxlSCExtCheck.tabbar.tabs("table_TRP_CAR_MISSING_CARRIER_LANG_MS").close();

                         dhxlSCExtCheck.gridChecks.selectRowById('TRP_CAR_MISSING_CARRIER_LANG_MS');
                         doCheck(false);
                    });
                }
            }

            function addMissingCarrierLang()
            {
                var selectedMissingCarrierLangs = gridMissingCarrierLang.getSelectedRowId();
                if(selectedMissingCarrierLangs==null || selectedMissingCarrierLangs=="")
                    selectedMissingCarrierLangs = 0;
                if(selectedMissingCarrierLangs!="0")
                {
                    $.post("index.php?ajax=1&act=all_win-fixmyprestashop_actions&check=TRP_CAR_MISSING_CARRIER_LANG_MS&id_lang="+SC_ID_LANG, { "action": "add_carriers", "ids": selectedMissingCarrierLangs}, function(data){
                        dhxlSCExtCheck.tabbar.tabs("table_TRP_CAR_MISSING_CARRIER_LANG_MS").close();

                         dhxlSCExtCheck.gridChecks.selectRowById('TRP_CAR_MISSING_CARRIER_LANG_MS');
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
            'title' => _l('Carrier lang'),
            'contentJs' => $content_js,
    ));
}
elseif (!empty($post_action) && $post_action == 'delete_carriers')
{
    $post_ids = Tools::getValue('ids');
    if (!empty($post_ids))
    {
        $ids = explode(',', $post_ids);
        foreach ($ids as $id)
        {
            list($id_carrier, $id_shop) = explode('_', $id);

            $sql = 'DELETE FROM '._DB_PREFIX_.'carrier_shop WHERE id_carrier = '.(int) $id_carrier.' AND id_shop = '.(int) $id_shop;
            dbExecuteForeignKeyOff($sql);

            $sql = 'DELETE FROM '._DB_PREFIX_.'carrier_lang WHERE id_carrier = '.(int) $id_carrier.' AND id_shop = '.(int) $id_shop;
            dbExecuteForeignKeyOff($sql);
        }
    }
}
elseif (!empty($post_action) && $post_action == 'add_carriers')
{
    $post_ids = Tools::getValue('ids');
    if (!empty($post_ids))
    {
        $ids = explode(',', $post_ids);
        foreach ($ids as $id)
        {
            list($id_carrier, $id_shop) = explode('_', $id);

            $sql = 'SELECT  l.*
                    FROM '._DB_PREFIX_.'lang l
                    WHERE
                        l.id_lang not in (SELECT pl.id_lang FROM '._DB_PREFIX_."carrier_lang pl WHERE pl.id_carrier='".(int) $id."' AND pl.id_shop = '".(int) $id_shop."')";
            $languages = Db::getInstance()->ExecuteS($sql);

            foreach ($languages as $language)
            {
                $sql = 'INSERT INTO '._DB_PREFIX_.'carrier_lang (id_carrier, id_lang, id_shop, delay)
                        VALUES ('.(int) $id_carrier.','.(int) $language['id_lang'].','.(int) $id_shop.",'Carrier')";
                dbExecuteForeignKeyOff($sql);
            }
        }
    }
}
