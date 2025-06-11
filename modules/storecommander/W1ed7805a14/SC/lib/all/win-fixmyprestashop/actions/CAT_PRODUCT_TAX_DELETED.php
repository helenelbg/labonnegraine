<?php
$post_action = Tools::getValue('action');
if (!empty($post_action) && $post_action == 'do_check')
{
    if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
    {
        $tmps = SCI::getAllShops();
        foreach ($tmps as $tmp)
        {
            $shops[$tmp['id_shop']] = $tmp['name'];
        }
    }

    $sql = 'SELECT p.id_product, p.reference, '.(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? 'ps.id_tax_rules_group, ps.id_shop' : 'p.id_tax_rules_group').', pl.name
            FROM '._DB_PREFIX_.'product p
            LEFT JOIN '._DB_PREFIX_.'product_lang pl ON (pl.id_product = p.id_product AND pl.id_lang  = '.(int) $cookie->id_lang.')
            LEFT JOIN '._DB_PREFIX_.'tax_rules_group trg ON (p.id_tax_rules_group = trg.id_tax_rules_group)
            '.(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? ' LEFT JOIN '._DB_PREFIX_.'product_shop ps ON (ps.id_product = p.id_product) ' : '').'
            WHERE trg.deleted = 1
            GROUP BY p.id_product, p.reference, pl.name '.(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? ',ps.id_shop' : '');
    $res = Db::getInstance()->ExecuteS($sql);

    $sql = 'SELECT id_tax_rules_group, name
        FROM '._DB_PREFIX_.'tax_rules_group
        WHERE deleted = 0';
    $taxes = Db::getInstance()->ExecuteS($sql);

    $content = $content_js = '';
    $results = 'OK';
    if (!empty($res) && count($res) > 0)
    {
        $name_key = 'CAT_PRODUCT_TAX_DELETED';
        $results = 'KO';
        ob_start(); ?>
        <script type="text/javascript">

            var colsProductDeletedTax = dhxlSCExtCheck.tabbar.cells("table_CAT_PRODUCT_TAX_DELETED").attachLayout("2U");

            // LISTE PRODUCTS
            var tbLeftProductDeletedTax = colsProductDeletedTax.cells('a').attachToolbar();
            tbLeftProductDeletedTax.setIconset('awesome');
            colsProductDeletedTax.cells('a').setText("<?php echo _l('Products'); ?>");
            tbLeftProductDeletedTax.addButton("selectall", 0, "", 'fa fa-bolt yellow', 'fa fa-bolt yellow');
            tbLeftProductDeletedTax.setItemToolTip('selectall','<?php echo _l('Select all'); ?>');
            tbLeftProductDeletedTax.attachEvent("onClick",
                function(id){
                    if (id=='selectall')
                    {
                        gridLeftProductDeletedTax.selectAll();
                        getGridStat_LeftProductDeletedTax();
                    }
                });

            var gridLeftProductDeletedTax = colsProductDeletedTax.cells('a').attachGrid();
            gridLeftProductDeletedTax.setImagePath("lib/js/imgs/");
            gridLeftProductDeletedTax.enableSmartRendering(true);
            gridLeftProductDeletedTax.enableMultiselect(true);

            gridLeftProductDeletedTax.setHeader("<?php echo _l('ID'); ?>,<?php echo _l('Ref'); ?>,<?php echo _l('Name'); ?><?php echo version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? ','._l('shop') : ''; ?>");
            gridLeftProductDeletedTax.setColAlign("left,left,left<?php echo version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? ',left' : ''; ?>");
            gridLeftProductDeletedTax.setColTypes("ro,ro,ro<?php echo version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? ',ro' : ''; ?>");
            gridLeftProductDeletedTax.setInitWidths("40,80,,<?php echo version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? ',*' : ''; ?>");
            gridLeftProductDeletedTax.setColSorting("int,str,str<?php echo version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? ',str' : ''; ?>");
            gridLeftProductDeletedTax.attachHeader("#numeric_filter,#text_filter,#text_filter<?php echo version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? ',#select_filter' : ''; ?>");
            gridLeftProductDeletedTax.init();

            var xml = '<rows>';
            <?php foreach ($res as $id => $row)
            { ?>
            xml = xml+'<row id="<?php echo $row['id_product'].(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? '_'.$row['id_shop'] : ''); ?>">';
            xml = xml+'    <userdata name="ids"><?php echo $row['id_product']; ?></userdata>';
            xml = xml+'    <cell><![CDATA[<?php echo $row['id_product']; ?>]]></cell>';
            xml = xml+'    <cell><![CDATA[<?php echo str_replace("'", "\'", $row['reference']); ?>]]></cell>';
            xml = xml+'    <cell><![CDATA[<?php echo str_replace("'", "\'", $row['name']); ?>]]></cell>';
            <?php if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
            { ?>
            xml = xml+'    <cell><![CDATA[<?php echo addslashes($shops[$row['id_shop']]); ?>]]></cell>';
            <?php } ?>
            xml = xml+'</row>';
            <?php
            } ?>
            xml = xml+'</rows>';
            gridLeftProductDeletedTax.parse(xml);

            sbLeftProductDeletedTax=dhxlSCExtCheck.tabbar.cells("table_CAT_PRODUCT_TAX_DELETED").attachStatusBar();
            function getGridStat_LeftProductDeletedTax(){
                var filteredRows=gridLeftProductDeletedTax.getRowsNum();
                var selectedRows=(gridLeftProductDeletedTax.getSelectedRowId()?gridLeftProductDeletedTax.getSelectedRowId().split(',').length:0);
                sbLeftProductDeletedTax.setText('<?php echo count($res).' '._l('Errors'); ?>'+" - <?php echo _l('Filter')._l(':'); ?> "+filteredRows+" - <?php echo _l('Selection')._l(':'); ?> "+selectedRows);
            }
            gridLeftProductDeletedTax.attachEvent("onFilterEnd", function(elements){
                getGridStat_LeftProductDeletedTax();
            });
            gridLeftProductDeletedTax.attachEvent("onSelectStateChanged", function(id){
                getGridStat_LeftProductDeletedTax();
            });
            getGridStat_LeftProductDeletedTax();

            gridLeftProductDeletedTax.attachEvent("onRowSelect", function(id,ind){
                var ids = gridLeftProductDeletedTax.getUserData(id, "ids");
            });

            // LISTE TAXE RULES GROUP
            var tbRightProductDeletedTax = colsProductDeletedTax.cells('b').attachToolbar();
            tbRightProductDeletedTax.setIconset('awesome');
            colsProductDeletedTax.cells('b').setText("<?php echo _l('Tax rules groups'); ?>");
            tbRightProductDeletedTax.addButton("updateTax", 0, "", 'fa fa-save blue', 'fa fa-save blue');
            tbRightProductDeletedTax.setItemToolTip('updateTax','<?php echo _l('Apply selected tax'); ?>');
            tbRightProductDeletedTax.attachEvent("onClick",
                function(id){
                    if (id=='updateTax')
                    {
                        updateTax();
                    }
                });

            var gridRightProductDeletedTax = colsProductDeletedTax.cells('b').attachGrid();
            gridRightProductDeletedTax.setImagePath("lib/js/imgs/");
            gridRightProductDeletedTax.enableSmartRendering(true);
            gridRightProductDeletedTax.enableMultiselect(false);

            gridRightProductDeletedTax.setHeader("<?php echo _l('ID'); ?>,<?php echo _l('Name'); ?>");
            gridRightProductDeletedTax.setColAlign("left,left");
            gridRightProductDeletedTax.setColTypes("ro,ro");
            gridRightProductDeletedTax.setInitWidths("40,");
            gridRightProductDeletedTax.init();

            var xml = '<rows>';
            <?php foreach ($taxes as $id => $row)
            {
                ?>
            xml = xml+'<row id="<?php echo $row['id_tax_rules_group']; ?>">';
            xml = xml+'    <cell><![CDATA[<?php echo str_replace("'", "\'", $row['id_tax_rules_group']); ?>]]></cell>';
            xml = xml+'    <cell><![CDATA[<?php echo str_replace("'", "\'", $row['name']); ?>]]></cell>';
            xml = xml+'</row>';
            <?php
            } ?>
            xml = xml+'</rows>';

            gridRightProductDeletedTax.parse(xml);

            function updateTax()
            {
                var ids = gridLeftProductDeletedTax.getSelectedRowId();
                gridLeftProductDeletedTax.selectAll();
                var selectedTax = gridRightProductDeletedTax.getSelectedRowId();
                if(selectedTax==null || selectedTax=="")
                    selectedTax = 0;
                if(ids==null || ids=="")
                    ids = 0;
                if(selectedTax!="0" && ids!="0") {
                    $.post("index.php?ajax=1&act=all_win-fixmyprestashop_actions&check=CAT_PRODUCT_TAX_DELETED&id_lang="+SC_ID_LANG, { "action": "update_id_tax_rules_group", "ids": ids, "selectedTax" : selectedTax}, function(data){
                        dhxlSCExtCheck.tabbar.tabs("table_CAT_PRODUCT_TAX_DELETED").close();

                        dhxlSCExtCheck.gridChecks.selectRowById('CAT_PRODUCT_TAX_DELETED');
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
        'title' => _l('Product taxes deleted'),
        'contentJs' => $content_js,
    ));
}
elseif (!empty($post_action) && $post_action == 'update_id_tax_rules_group')
{
    $post_ids = Tools::getValue('ids');
    $selectedTax = Tools::getValue('selectedTax');
    if (isset($post_ids))
    {
        $ids = explode(',', $post_ids);
        foreach ($ids as $id)
        {
            if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
            {
                list($id_prd, $id_shp) = explode('_', $id);
                $sql = 'UPDATE '._DB_PREFIX_.'product_shop
                    SET id_tax_rules_group = '.(int) $selectedTax.'
                    WHERE id_product = '.(int) $id_prd.'
                    AND id_shop ='.(int) $id_shp;
                $res = dbExecuteForeignKeyOff($sql);

                $sql = 'UPDATE ' . _DB_PREFIX_ . 'product
                    SET id_tax_rules_group = ' . (int)$selectedTax . '
                    WHERE id_product = ' . (int) $id_prd;
                $res = dbExecuteForeignKeyOff($sql);
            }
            else
            {
                $sql = 'UPDATE ' . _DB_PREFIX_ . 'product
                    SET id_tax_rules_group = ' . (int)$selectedTax . '
                    WHERE id_product = ' . (int) $id;
                $res = dbExecuteForeignKeyOff($sql);
            }
        }
    }
}
