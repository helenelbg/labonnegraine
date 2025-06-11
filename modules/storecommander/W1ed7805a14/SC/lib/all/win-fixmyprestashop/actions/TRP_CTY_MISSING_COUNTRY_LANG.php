<?php
$post_action = Tools::getValue('action');
if (!empty($post_action) && $post_action == 'do_check')
{
    $res = missingLangGet('country');

    $content = '';
    $content_js = '';
    $results = 'OK';
    if (!empty($res) && count($res) > 0)
    {
        $results = 'KO';
        ob_start(); ?>
        <script type="text/javascript">
    
            var tbMissingCountryLang = dhxlSCExtCheck.tabbar.cells("table_TRP_CTY_MISSING_COUNTRY_LANG").attachToolbar();
            tbMissingCountryLang.setIconset('awesome');
            tbMissingCountryLang.addButton("selectall", 0, "", 'fa fa-bolt yellow', 'fa fa-bolt yellow');
            tbMissingCountryLang.setItemToolTip('selectall','<?php echo _l('Select all'); ?>');


            tbMissingCountryLang.addButton("add", 0, "", 'fa fa-plus-circle green', 'fa fa-plus-circle green');
            tbMissingCountryLang.setItemToolTip('add','<?php echo _l('Recover incomplete countries'); ?>');
            tbMissingCountryLang.attachEvent("onClick",
                function(id){
                    if (id=='selectall')
                    {
                        gridMissingCountryLang.selectAll();
                        getGridStat_MissingCountryLang();
                    }
                    if (id=='delete')
                    {
                        deleteMissingCountryLang();
                    }
                    if (id=='add')
                    {
                        addMissingCountryLang()
                    }
                });
        
            var gridMissingCountryLang = dhxlSCExtCheck.tabbar.cells("table_TRP_CTY_MISSING_COUNTRY_LANG").attachGrid();
            gridMissingCountryLang.setImagePath("lib/js/imgs/");
            gridMissingCountryLang.enableSmartRendering(true);
            gridMissingCountryLang.enableMultiselect(true);
    
            gridMissingCountryLang.setHeader("ID,<?php echo _l('Used ?'); ?>");
            gridMissingCountryLang.setInitWidths("100,50");
            gridMissingCountryLang.setColAlign("left,left");
            gridMissingCountryLang.setColTypes("ro,ro");
            gridMissingCountryLang.setColSorting("int,str");
            gridMissingCountryLang.attachHeader("#numeric_filter,#select_filter");
            gridMissingCountryLang.init();
    
            var xml = '<rows>';
            <?php foreach ($res as $country)
        {
            $sql = 'SELECT * FROM `'._DB_PREFIX_."address` WHERE id_country = ".(int) $country['id_country']." LIMIT 1";
            $is_used = Db::getInstance()->ExecuteS($sql); ?>
            xml = xml+'   <row id="<?php echo $country['id_country']; ?>">';
            xml = xml+'      <cell><![CDATA[<?php echo $country['id_country']; ?>]]></cell>';
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
            gridMissingCountryLang.parse(xml);

            sbMissingCountryLang=dhxlSCExtCheck.tabbar.cells("table_TRP_CTY_MISSING_COUNTRY_LANG").attachStatusBar();
            function getGridStat_MissingCountryLang(){
                var filteredRows=gridMissingCountryLang.getRowsNum();
                var selectedRows=(gridMissingCountryLang.getSelectedRowId()?gridMissingCountryLang.getSelectedRowId().split(',').length:0);
                sbMissingCountryLang.setText('<?php echo count($res).' '._l('Errors'); ?>'+" - <?php echo _l('Filter')._l(':'); ?> "+filteredRows+" - <?php echo _l('Selection')._l(':'); ?> "+selectedRows);
            }
            gridMissingCountryLang.attachEvent("onFilterEnd", function(elements){
                getGridStat_MissingCountryLang();
            });
            gridMissingCountryLang.attachEvent("onSelectStateChanged", function(id){
                getGridStat_MissingCountryLang();
            });
            getGridStat_MissingCountryLang();

            function deleteMissingCountryLang()
            {
                var selectedMissingCountryLangs = gridMissingCountryLang.getSelectedRowId();
                if(selectedMissingCountryLangs==null || selectedMissingCountryLangs=="")
                    selectedMissingCountryLangs = 0;
                if(selectedMissingCountryLangs!="0")
                {
                    $.post("index.php?ajax=1&act=all_win-fixmyprestashop_actions&check=TRP_CTY_MISSING_COUNTRY_LANG&id_lang="+SC_ID_LANG, { "action": "delete_countries", "ids": selectedMissingCountryLangs}, function(data){
                        dhxlSCExtCheck.tabbar.tabs("table_TRP_CTY_MISSING_COUNTRY_LANG").close();

                         dhxlSCExtCheck.gridChecks.selectRowById('TRP_CTY_MISSING_COUNTRY_LANG');
                         doCheck(false);
                    });
                }
            }

            function addMissingCountryLang()
            {
                var selectedMissingCountryLangs = gridMissingCountryLang.getSelectedRowId();
                if(selectedMissingCountryLangs==null || selectedMissingCountryLangs=="")
                    selectedMissingCountryLangs = 0;
                if(selectedMissingCountryLangs!="0")
                {
                    $.post("index.php?ajax=1&act=all_win-fixmyprestashop_actions&check=TRP_CTY_MISSING_COUNTRY_LANG&id_lang="+SC_ID_LANG, { "action": "add_countries", "ids": selectedMissingCountryLangs}, function(data){
                        dhxlSCExtCheck.tabbar.tabs("table_TRP_CTY_MISSING_COUNTRY_LANG").close();

                         dhxlSCExtCheck.gridChecks.selectRowById('TRP_CTY_MISSING_COUNTRY_LANG');
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
            'title' => _l('Country lang'),
            'contentJs' => $content_js,
    ));
}
elseif (!empty($post_action) && $post_action == 'delete_countries')
{
    $post_ids = Tools::getValue('ids');
    if (!empty($post_ids))
    {
        $ids = explode(',', $post_ids);
        foreach ($ids as $id)
        {
            $country = new Country($id);
            $country->delete();
        }
    }
}
elseif (!empty($post_action) && $post_action == 'add_countries')
{
    $post_ids = Tools::getValue('ids');
    if (!empty($post_ids))
    {
        $ids = explode(',', $post_ids);
        foreach ($ids as $id)
        {
            $sql = 'SELECT  l.*
                    FROM '._DB_PREFIX_.'lang l
                    WHERE l.id_lang not in (SELECT pl.id_lang FROM '._DB_PREFIX_."country_lang pl WHERE pl.id_country='".$id."')";
            $languages = Db::getInstance()->ExecuteS($sql);

            foreach ($languages as $language)
            {
                $sql = 'INSERT INTO '._DB_PREFIX_.'country_lang (id_country, id_lang, name)
                        VALUES ('.$id.','.$language['id_lang'].",'Country')";
                $res = dbExecuteForeignKeyOff($sql);
            }
        }
    }
}
