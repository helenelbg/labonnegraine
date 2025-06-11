<?php
$post_action = Tools::getValue('action');
$caracs = array(
    'ï»¿',
    '',
);
if (!empty($post_action) && $post_action == 'do_check')
{
    if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
    {
        $sql = 'SELECT pl.id_product, pl.id_lang, pl.id_shop, pl.name, s.name as name_shop, p.reference
            FROM `'._DB_PREFIX_.'product_lang` pl
                INNER JOIN `'._DB_PREFIX_.'shop` s ON (pl.id_shop = s.id_shop)
                INNER JOIN `'._DB_PREFIX_.'product` p ON (pl.id_product = p.id_product)
            WHERE
                    ';
    }
    else
    {
        $sql = 'SELECT pl.id_product, pl.id_lang, pl.name, p.reference
            FROM `'._DB_PREFIX_.'product_lang` pl
                INNER JOIN `'._DB_PREFIX_.'product` p ON (pl.id_product = p.id_product)
            WHERE
                    ';
    }
    foreach ($caracs as $key => $carac)
    {
        if ($key > 0)
        {
            $sql .= ' OR ';
        }
        $sql .= " (pl.description LIKE '%".pSQL($carac)."%' OR pl.description_short LIKE '%".pSQL($carac)."%') ";
    }
    $res = Db::getInstance()->ExecuteS($sql);

    $content = '';
    $content_js = '';
    $results = 'OK';
    if (!empty($res) && count($res) > 0)
    {
        $results = 'KO';
        ob_start(); ?>
        <script type="text/javascript">

        
            var tbHiddenCarac = dhxlSCExtCheck.tabbar.cells("table_CAT_PROD_DESC_HIDDEN_CARAC").attachToolbar();
            tbHiddenCarac.setIconset('awesome');
            tbHiddenCarac.addButton("selectall", 0, "", 'fa fa-bolt yellow', 'fa fa-bolt yellow');
            tbHiddenCarac.setItemToolTip('selectall','<?php echo _l('Select all'); ?>');
            tbHiddenCarac.addButton("replace", 0, "", 'fad fa-sync green', 'fad fa-sync green');
            tbHiddenCarac.setItemToolTip('replace','<?php echo _l('Delete hidden characters'); ?>');
            tbHiddenCarac.attachEvent("onClick",
                function(id){
                    if (id=='selectall')
                    {
                        gridHiddenCarac.selectAll();
                        getGridStat_HiddenCarac();
                    }
                    if (id=='replace')
                    {
                        replaceHiddenCarac();
                    }
                });
        
            var gridHiddenCarac = dhxlSCExtCheck.tabbar.cells("table_CAT_PROD_DESC_HIDDEN_CARAC").attachGrid();
            gridHiddenCarac.setImagePath("lib/js/imgs/");
            gridHiddenCarac.enableSmartRendering(true);
            gridHiddenCarac.enableMultiselect(true);
    
            gridHiddenCarac.setHeader("ID,<?php echo _l('Reference'); ?>,<?php echo _l('Name'); ?>,<?php echo _l('Lang'); ?><?php if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
        {
            echo ','._l('Shop');
        } ?>");
            gridHiddenCarac.setInitWidths("100, 80, 110,100<?php if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
        {
            echo ', 100';
        } ?>");
            gridHiddenCarac.setColAlign("left,left,left,left<?php if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
        {
            echo ',left';
        } ?>");
            gridHiddenCarac.setColTypes("ro,ro,ro,ro<?php if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
        {
            echo ',ro';
        } ?>");
            gridHiddenCarac.setColSorting("int,str,str<?php if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
        {
            echo ',str';
        } ?>");
            gridHiddenCarac.attachHeader("#numeric_filter,#text_filter,#select_filter<?php if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
        {
            echo ',text_filter';
        } ?>");
            gridHiddenCarac.init();

            var xml = '<rows>';
            <?php foreach ($res as $product)
        {
            $lang = new Language((int) $product['id_lang'], (int) SCI::getConfigurationValue('PS_LANG_DEFAULT'));
            if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
            {
                $id = $product['id_product'].'_'.$product['id_lang'].'_'.$product['id_shop'];
            }
            else
            {
                $id = $product['id_product'].'_'.$product['id_lang'];
            } ?>
            xml = xml+'   <row id="<?php echo $id; ?>">';
            xml = xml+'      <cell><![CDATA[<?php echo $product['id_product']; ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php echo str_replace("'", "\'", $product['reference']); ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php echo str_replace("'", "\'", $product['name']); ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php echo str_replace("'", "\'", $lang->name); ?>]]></cell>';
            <?php if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) { ?>
            xml = xml+'      <cell><![CDATA[<?php echo str_replace("'", "\'", $product['name_shop']); ?>]]></cell>';
            <?php } ?>
            xml = xml+'   </row>';
            <?php
        } ?>
            xml = xml+'</rows>';
            gridHiddenCarac.parse(xml);

            sbHiddenCarac=dhxlSCExtCheck.tabbar.cells("table_CAT_PROD_DESC_HIDDEN_CARAC").attachStatusBar();
            function getGridStat_HiddenCarac(){
                var filteredRows=gridHiddenCarac.getRowsNum();
                var selectedRows=(gridHiddenCarac.getSelectedRowId()?gridHiddenCarac.getSelectedRowId().split(',').length:0);
                sbHiddenCarac.setText('<?php echo count($res).' '._l('Errors'); ?>'+" - <?php echo _l('Filter')._l(':'); ?> "+filteredRows+" - <?php echo _l('Selection')._l(':'); ?> "+selectedRows);
            }
            gridHiddenCarac.attachEvent("onFilterEnd", function(elements){
                getGridStat_HiddenCarac();
            });
            gridHiddenCarac.attachEvent("onSelectStateChanged", function(id){
                getGridStat_HiddenCarac();
            });
            getGridStat_HiddenCarac();

            function replaceHiddenCarac()
            {
                var selectedHiddenCaracs = gridHiddenCarac.getSelectedRowId();
                if(selectedHiddenCaracs==null || selectedHiddenCaracs=="")
                    selectedHiddenCaracs = 0;
                if(selectedHiddenCaracs!="0")
                {
                    $.post("index.php?ajax=1&act=all_win-fixmyprestashop_actions&check=CAT_PROD_DESC_HIDDEN_CARAC&id_lang="+SC_ID_LANG, { "action": "replace", "ids": selectedHiddenCaracs}, function(data){
                        dhxlSCExtCheck.tabbar.tabs("table_CAT_PROD_DESC_HIDDEN_CARAC").close();

                         dhxlSCExtCheck.gridChecks.selectRowById('CAT_PROD_DESC_HIDDEN_CARAC');
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
            'title' => _l('Hidden Charac.'),
            'contentJs' => $content_js,
    ));
}
elseif (!empty($post_action) && $post_action == 'replace')
{
    $post_ids = Tools::getValue('ids');
    if (!empty($post_ids))
    {
        $ids = explode(',', $post_ids);
        foreach ($ids as $id)
        {
            $descs = array();
            if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
            {
                list($id_product, $id_lang, $id_shop) = explode('_', $id);

                $sql = 'SELECT description, description_short FROM `'._DB_PREFIX_."product_lang` WHERE id_product='".(int) $id_product."' AND  id_lang='".(int) $id_lang."' AND  id_shop='".(int) $id_shop."'";
                $descs = Db::getInstance()->getRow($sql);
            }
            else
            {
                list($id_product, $id_lang) = explode('_', $id);

                $sql = 'SELECT description, description_short FROM `'._DB_PREFIX_."product_lang` WHERE id_product='".(int) $id_product."' AND  id_lang='".(int) $id_lang."'";
                $descs = Db::getInstance()->getRow($sql);
            }

            if (!empty($descs))
            {
                foreach ($caracs as $carac)
                {
                    $descs['description'] = utf8_decode(str_replace($carac, '', utf8_encode($descs['description'])));
                    $descs['description'] = str_replace($carac, '', $descs['description']);

                    $descs['description_short'] = utf8_decode(str_replace($carac, '', utf8_encode($descs['description_short'])));
                    $descs['description_short'] = str_replace($carac, '', $descs['description_short']);
                }

                $sql = 'UPDATE `'._DB_PREFIX_."product_lang` SET description = '".pSQL($descs['description'])."', description_short = '".pSQL($descs['description_short'])."' WHERE id_product='".(int) $id_product."' AND  id_lang='".(int) $id_lang."'";
                if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                {
                    $sql .= "  AND  id_shop='".(int) $id_shop."' ";
                }
                dbExecuteForeignKeyOff($sql);
            }
        }
    }
}
