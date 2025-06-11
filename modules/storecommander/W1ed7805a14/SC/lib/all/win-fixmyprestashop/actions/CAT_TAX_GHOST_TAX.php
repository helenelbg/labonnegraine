<?php
$post_action = Tools::getValue('action');
if (!empty($post_action) && $post_action == 'do_check')
{
    $sql = 'select pl.id_tax, pl.name from '._DB_PREFIX_.'tax_lang pl where pl.id_tax not in (select p.id_tax from '._DB_PREFIX_.'tax p) ORDER BY id_lang ASC LIMIT 1500';
    $res = Db::getInstance()->ExecuteS($sql);

    $content = '';
    $content_js = '';
    $results = 'OK';
    if (!empty($res) && count($res) > 0)
    {
        $results = 'KO';
        ob_start(); ?>
        <script type="text/javascript">
    
            var tbGhostTax = dhxlSCExtCheck.tabbar.cells("table_CAT_TAX_GHOST_TAX").attachToolbar();
            tbGhostTax.setIconset('awesome');
            tbGhostTax.addButton("selectall", 0, "", 'fa fa-bolt yellow', 'fa fa-bolt yellow');
            tbGhostTax.setItemToolTip('selectall','<?php echo _l('Select all'); ?>');
            tbGhostTax.addButton("delete", 0, "", 'fa fa-minus-circle red', 'fa fa-minus-circle red');
            tbGhostTax.setItemToolTip('delete','<?php echo _l('Delete incomplete taxes'); ?>');
            tbGhostTax.addButton("add", 0, "", 'fa fa-plus-circle green', 'fa fa-plus-circle green');
            tbGhostTax.setItemToolTip('add','<?php echo _l('Recover incomplete taxes'); ?>');
            tbGhostTax.attachEvent("onClick",
                function(id){
                    if (id=='selectall')
                    {
                        gridGhostTax.selectAll();
                        getGridStat_GhostTax();
                    }
                    if (id=='delete')
                    {
                        deleteGhostTax();
                    }
                    if (id=='add')
                    {
                        addGhostTax()
                    }
                });
        
            var gridGhostTax = dhxlSCExtCheck.tabbar.cells("table_CAT_TAX_GHOST_TAX").attachGrid();
            gridGhostTax.setImagePath("lib/js/imgs/");
            gridGhostTax.enableSmartRendering(true);
            gridGhostTax.enableMultiselect(true);
    
            gridGhostTax.setHeader("ID,<?php echo _l('Name'); ?>,<?php echo _l('Used?'); ?>");
            gridGhostTax.setInitWidths("100, 110,50");
            gridGhostTax.setColAlign("left,left,left");
            gridGhostTax.setColTypes("ro,ro,ro");
            gridGhostTax.setColSorting("int,str,str");
            gridGhostTax.attachHeader("#numeric_filter,#text_filter,#select_filter");
            gridGhostTax.init();

            var xml = '<rows>';
            <?php foreach ($res as $tax)
        {
            $sql = 'SELECT * FROM `'._DB_PREFIX_."tax_rule` WHERE id_tax = '".(int) $tax['id_tax']."' LIMIT 1";
            $is_used = Db::getInstance()->ExecuteS($sql); ?>
            xml = xml+'   <row id="<?php echo $tax['id_tax']; ?>">';
            xml = xml+'      <cell><![CDATA[<?php echo $tax['id_tax']; ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php echo str_replace("'", "\'", $tax['name']); ?>]]></cell>';
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
            gridGhostTax.parse(xml);

            sbGhostTax=dhxlSCExtCheck.tabbar.cells("table_CAT_TAX_GHOST_TAX").attachStatusBar();
            function getGridStat_GhostTax(){
                var filteredRows=gridGhostTax.getRowsNum();
                var selectedRows=(gridGhostTax.getSelectedRowId()?gridGhostTax.getSelectedRowId().split(',').length:0);
                sbGhostTax.setText('<?php echo count($res).' '._l('Errors'); ?>'+" - <?php echo _l('Filter')._l(':'); ?> "+filteredRows+" - <?php echo _l('Selection')._l(':'); ?> "+selectedRows);
            }
            gridGhostTax.attachEvent("onFilterEnd", function(elements){
                getGridStat_GhostTax();
            });
            gridGhostTax.attachEvent("onSelectStateChanged", function(id){
                getGridStat_GhostTax();
            });
            getGridStat_GhostTax();

            function deleteGhostTax()
            {
                var selectedGhostTaxs = gridGhostTax.getSelectedRowId();
                if(selectedGhostTaxs!=null && selectedGhostTaxs!="")
                {
                    $.post("index.php?ajax=1&act=all_win-fixmyprestashop_actions&check=CAT_TAX_GHOST_TAX&id_lang="+SC_ID_LANG, { "action": "delete_taxs", "ids": selectedGhostTaxs}, function(data){
                        dhxlSCExtCheck.tabbar.tabs("table_CAT_TAX_GHOST_TAX").close();

                         dhxlSCExtCheck.gridChecks.selectRowById('CAT_TAX_GHOST_TAX');
                         doCheck(false);
                    });
                }
            }

            function addGhostTax()
            {
                var selectedGhostTaxs = gridGhostTax.getSelectedRowId();
                if(selectedGhostTaxs==null || selectedGhostTaxs=="")
                    selectedGhostTaxs = 0;
                if(selectedGhostTaxs!="0")
                {
                    $.post("index.php?ajax=1&act=all_win-fixmyprestashop_actions&check=CAT_TAX_GHOST_TAX&id_lang="+SC_ID_LANG, { "action": "add_taxs", "ids": selectedGhostTaxs}, function(data){
                        dhxlSCExtCheck.tabbar.tabs("table_CAT_TAX_GHOST_TAX").close();

                         dhxlSCExtCheck.gridChecks.selectRowById('CAT_TAX_GHOST_TAX');
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
            'title' => _l('Ghost tax'),
            'contentJs' => $content_js,
    ));
}
elseif (!empty($post_action) && $post_action == 'delete_taxs')
{
    $post_ids = Tools::getValue('ids');
    if (!empty($post_ids) || $post_ids == 0)
    {
        $sql = 'DELETE FROM '._DB_PREFIX_.'tax_lang WHERE id_tax IN ('.pInSQL($post_ids).')';
        $res = dbExecuteForeignKeyOff($sql);
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
            $sql = 'INSERT INTO '._DB_PREFIX_.'tax (id_tax, rate, active)
                    VALUES ('.(int) $id.',0, 0)';
            $res = dbExecuteForeignKeyOff($sql);
        }
    }
}
