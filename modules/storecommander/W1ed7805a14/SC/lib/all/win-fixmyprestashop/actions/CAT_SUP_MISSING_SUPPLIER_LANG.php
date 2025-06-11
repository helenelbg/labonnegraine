<?php
$post_action = Tools::getValue('action');
if (!empty($post_action) && $post_action == 'do_check')
{
    $res = missingLangGet('supplier');

    $content = '';
    $content_js = '';
    $results = 'OK';
    if (!empty($res) && count($res) > 0)
    {
        $results = 'KO';
        ob_start(); ?>
        <script type="text/javascript">
    
            var tbMissingSupplierLang = dhxlSCExtCheck.tabbar.cells("table_CAT_SUP_MISSING_SUPPLIER_LANG").attachToolbar();
            tbMissingSupplierLang.setIconset('awesome');
            tbMissingSupplierLang.addButton("selectall", 0, "", 'fa fa-bolt yellow', 'fa fa-bolt yellow');
            tbMissingSupplierLang.setItemToolTip('selectall','<?php echo _l('Select all'); ?>');
            tbMissingSupplierLang.addButton("delete", 0, "", 'fa fa-minus-circle red', 'fa fa-minus-circle red');
            tbMissingSupplierLang.setItemToolTip('delete','<?php echo _l('Delete incomplete suppliers'); ?>');
            tbMissingSupplierLang.addButton("add", 0, "", 'fa fa-plus-circle green', 'fa fa-plus-circle green');
            tbMissingSupplierLang.setItemToolTip('add','<?php echo _l('Recover incomplete suppliers'); ?>');
            tbMissingSupplierLang.attachEvent("onClick",
                function(id){
                    if (id=='selectall')
                    {
                        gridMissingSupplierLang.selectAll();
                        getGridStat_MissingSupplierLang();
                    }
                    if (id=='delete')
                    {
                        deleteMissingSupplierLang();
                    }
                    if (id=='add')
                    {
                        addMissingSupplierLang()
                    }
                });
        
            var gridMissingSupplierLang = dhxlSCExtCheck.tabbar.cells("table_CAT_SUP_MISSING_SUPPLIER_LANG").attachGrid();
            gridMissingSupplierLang.setImagePath("lib/js/imgs/");
            gridMissingSupplierLang.enableSmartRendering(true);
            gridMissingSupplierLang.enableMultiselect(true);
    
            gridMissingSupplierLang.setHeader("ID,<?php echo _l('Name'); ?>,<?php echo _l('Used?'); ?>");
            gridMissingSupplierLang.setInitWidths("100,110,50");
            gridMissingSupplierLang.setColAlign("left,left,left");
            gridMissingSupplierLang.setColTypes("ro,ro,ro");
            gridMissingSupplierLang.setColSorting("int,str,str");
            gridMissingSupplierLang.attachHeader("#numeric_filter,#text_filter,#select_filter");
            gridMissingSupplierLang.init();
    
            var xml = '<rows>';
            <?php foreach ($res as $supplier)
        {
            $sql = 'SELECT * FROM `'._DB_PREFIX_."product` WHERE id_supplier = '".(int) $supplier['id_supplier']."' LIMIT 1500";
            $is_used = Db::getInstance()->ExecuteS($sql); ?>
            xml = xml+'   <row id="<?php echo $supplier['id_supplier']; ?>">';
            xml = xml+'      <cell><![CDATA[<?php echo $supplier['id_supplier']; ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php echo str_replace("'", "\'", $supplier['name']); ?>]]></cell>';
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
            gridMissingSupplierLang.parse(xml);

            sbMissingSupplierLang=dhxlSCExtCheck.tabbar.cells("table_CAT_SUP_MISSING_SUPPLIER_LANG").attachStatusBar();
            function getGridStat_MissingSupplierLang(){
                var filteredRows=gridMissingSupplierLang.getRowsNum();
                var selectedRows=(gridMissingSupplierLang.getSelectedRowId()?gridMissingSupplierLang.getSelectedRowId().split(',').length:0);
                sbMissingSupplierLang.setText('<?php echo count($res).' '._l('Errors'); ?>'+" - <?php echo _l('Filter')._l(':'); ?> "+filteredRows+" - <?php echo _l('Selection')._l(':'); ?> "+selectedRows);
            }
            gridMissingSupplierLang.attachEvent("onFilterEnd", function(elements){
                getGridStat_MissingSupplierLang();
            });
            gridMissingSupplierLang.attachEvent("onSelectStateChanged", function(id){
                getGridStat_MissingSupplierLang();
            });
            getGridStat_MissingSupplierLang();

            function deleteMissingSupplierLang()
            {
                var selectedMissingSupplierLangs = gridMissingSupplierLang.getSelectedRowId();
                if(selectedMissingSupplierLangs==null || selectedMissingSupplierLangs=="")
                    selectedMissingSupplierLangs = 0;
                if(selectedMissingSupplierLangs!="0")
                {
                    $.post("index.php?ajax=1&act=all_win-fixmyprestashop_actions&check=CAT_SUP_MISSING_SUPPLIER_LANG&id_lang="+SC_ID_LANG, { "action": "delete_suppliers", "ids": selectedMissingSupplierLangs}, function(data){
                        dhxlSCExtCheck.tabbar.tabs("table_CAT_SUP_MISSING_SUPPLIER_LANG").close();

                         dhxlSCExtCheck.gridChecks.selectRowById('CAT_SUP_MISSING_SUPPLIER_LANG');
                         doCheck(false);
                    });
                }
            }

            function addMissingSupplierLang()
            {
                var selectedMissingSupplierLangs = gridMissingSupplierLang.getSelectedRowId();
                if(selectedMissingSupplierLangs==null || selectedMissingSupplierLangs=="")
                    selectedMissingSupplierLangs = 0;
                if(selectedMissingSupplierLangs!="0")
                {
                    $.post("index.php?ajax=1&act=all_win-fixmyprestashop_actions&check=CAT_SUP_MISSING_SUPPLIER_LANG&id_lang="+SC_ID_LANG, { "action": "add_suppliers", "ids": selectedMissingSupplierLangs}, function(data){
                        dhxlSCExtCheck.tabbar.tabs("table_CAT_SUP_MISSING_SUPPLIER_LANG").close();

                         dhxlSCExtCheck.gridChecks.selectRowById('CAT_SUP_MISSING_SUPPLIER_LANG');
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
            'title' => _l('Supplier lang'),
            'contentJs' => $content_js,
    ));
}
elseif (!empty($post_action) && $post_action == 'delete_suppliers')
{
    $post_ids = Tools::getValue('ids');
    if (!empty($post_ids))
    {
        $ids = explode(',', $post_ids);
        foreach ($ids as $id)
        {
            $supplier = new Supplier($id);
            $supplier->delete();
        }
    }
}
elseif (!empty($post_action) && $post_action == 'add_suppliers')
{
    $post_ids = Tools::getValue('ids');
    if (!empty($post_ids))
    {
        $ids = explode(',', $post_ids);
        foreach ($ids as $id)
        {
            $sql = 'SELECT  l.*
                    FROM '._DB_PREFIX_.'lang l
                    WHERE l.id_lang not in (SELECT pl.id_lang FROM '._DB_PREFIX_."supplier_lang pl WHERE pl.id_supplier='".(int) $id."')";
            $languages = Db::getInstance()->ExecuteS($sql);

            foreach ($languages as $language)
            {
                $sql = 'INSERT INTO '._DB_PREFIX_.'supplier_lang (id_supplier, id_lang)
                        VALUES ('.(int) $id.','.(int) $language['id_lang'].')';
                $res = dbExecuteForeignKeyOff($sql);
            }
        }
    }
}
