<?php
$post_action = Tools::getValue('action');
if (!empty($post_action) && $post_action == 'do_check')
{
    $sql = 'SELECT cl.id_country, cl.name FROM '._DB_PREFIX_.'country_lang cl WHERE cl.id_country NOT IN (SELECT c.id_country FROM '._DB_PREFIX_.'country c) ORDER BY cl.id_lang ASC LIMIT 1500';
    $res = Db::getInstance()->ExecuteS($sql);

    $content = '';
    $content_js = '';
    $results = 'OK';
    if (!empty($res) && count($res) > 0)
    {
        $results = 'KO';
        ob_start(); ?>
        <script type="text/javascript">
    
            var tbGhostCountry = dhxlSCExtCheck.tabbar.cells("table_TRP_CTY_GHOST_COUNTRY").attachToolbar();
            tbGhostCountry.setIconset('awesome');
            tbGhostCountry.addButton("selectall", 0, "", 'fa fa-bolt yellow', 'fa fa-bolt yellow');
            tbGhostCountry.setItemToolTip('selectall','<?php echo _l('Select all'); ?>');
            tbGhostCountry.addButton("delete", 0, "", 'fa fa-minus-circle red', 'fa fa-minus-circle red');
            tbGhostCountry.setItemToolTip('delete','<?php echo _l('Delete incomplete countries'); ?>');
            tbGhostCountry.attachEvent("onClick",
                function(id){
                    if (id=='selectall')
                    {
                        gridGhostCountry.selectAll();
                        getGridStat_GhostCountry();
                    }
                    if (id=='delete')
                    {
                        deleteGhostCountry();
                    }
                });
        
            var gridGhostCountry = dhxlSCExtCheck.tabbar.cells("table_TRP_CTY_GHOST_COUNTRY").attachGrid();
            gridGhostCountry.setImagePath("lib/js/imgs/");
            gridGhostCountry.enableSmartRendering(true);
            gridGhostCountry.enableMultiselect(true);
    
            gridGhostCountry.setHeader("ID,<?php echo _l('Name'); ?>,<?php echo _l('Used ?'); ?>");
            gridGhostCountry.setInitWidths("100, 110,50");
            gridGhostCountry.setColAlign("left,left,left");
            gridGhostCountry.setColTypes("ro,ro,ro");
            gridGhostCountry.setColSorting("int,str,str");
            gridGhostCountry.attachHeader("#numeric_filter,#text_filter,#select_filter");
            gridGhostCountry.init();

            var xml = '<rows>';
            <?php foreach ($res as $country)
        {
            $sql = 'SELECT * FROM `'._DB_PREFIX_."address` WHERE id_country = ".(int) $country['id_country']." LIMIT 1";
            $is_used = Db::getInstance()->ExecuteS($sql); ?>
            xml = xml+'   <row id="<?php echo $country['id_country']; ?>">';
            xml = xml+'      <cell><![CDATA[<?php echo $country['id_country']; ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php echo str_replace("'", "\'", $country['name']); ?>]]></cell>';
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
            gridGhostCountry.parse(xml);

            sbGhostCountry=dhxlSCExtCheck.tabbar.cells("table_TRP_CTY_GHOST_COUNTRY").attachStatusBar();
            function getGridStat_GhostCountry(){
                var filteredRows=gridGhostCountry.getRowsNum();
                var selectedRows=(gridGhostCountry.getSelectedRowId()?gridGhostCountry.getSelectedRowId().split(',').length:0);
                sbGhostCountry.setText('<?php echo count($res).' '._l('Errors'); ?>'+" - <?php echo _l('Filter')._l(':'); ?> "+filteredRows+" - <?php echo _l('Selection')._l(':'); ?> "+selectedRows);
            }
            gridGhostCountry.attachEvent("onFilterEnd", function(elements){
                getGridStat_GhostCountry();
            });
            gridGhostCountry.attachEvent("onSelectStateChanged", function(id){
                getGridStat_GhostCountry();
            });
            getGridStat_GhostCountry();

            function deleteGhostCountry()
            {
                var selectedGhostCountries = gridGhostCountry.getSelectedRowId();
                if(selectedGhostCountries==null || selectedGhostCountries=="")
                    selectedGhostCountries = 0;
                if(selectedGhostCountries!="0")
                {
                    $.post("index.php?ajax=1&act=all_win-fixmyprestashop_actions&check=TRP_CTY_GHOST_COUNTRY&id_lang="+SC_ID_LANG, { "action": "delete_countries", "ids": selectedGhostCountries}, function(data){
                        dhxlSCExtCheck.tabbar.tabs("table_TRP_CTY_GHOST_COUNTRY").close();

                         dhxlSCExtCheck.gridChecks.selectRowById('TRP_CTY_GHOST_COUNTRY');
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
            'title' => _l('Ghost country'),
            'contentJs' => $content_js,
    ));
}
elseif (!empty($post_action) && $post_action == 'delete_countries')
{
    $post_ids = Tools::getValue('ids');
    if (!empty($post_ids))
    {
        $sql = 'DELETE FROM '._DB_PREFIX_.'country_lang WHERE id_country IN ('.pInSQL($post_ids).')';
        $res = dbExecuteForeignKeyOff($sql);
    }
}
