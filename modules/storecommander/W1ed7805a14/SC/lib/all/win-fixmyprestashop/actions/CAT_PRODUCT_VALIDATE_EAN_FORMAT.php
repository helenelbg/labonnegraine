<?php
$post_action = Tools::getValue('action');
$action_name = 'CAT_PRODUCT_VALIDATE_EAN_FORMAT';
$tab_title = _l('EAN not valid');

if (!empty($post_action) && $post_action == 'do_check')
{
    function regenerate_check_digit($EAN_12) {
        $sum=0;
        $digit_array = str_split($EAN_12,1);
        foreach ($digit_array as $k => $v)
        {
            $multiplier = ($k % 2) ? 3 : 1;
            $sum += $v * $multiplier;
        }
        $next_ten=ceil($sum/10)*10 ;
        $check_digit = $next_ten - $sum;

        return (int) $check_digit;
    }

    $res_bad_format = array();
    $sql_bad = 'SELECT DISTINCT p.id_product, pl.name, p.ean13, "BAD FORMAT (NOT 13 NUMERIC DIGIT)" as problem,'.(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? ' ps.id_category_default ' : ' p.id_category_default').', '.(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? ' ps.active ' : ' p.active').'
                FROM `'._DB_PREFIX_.'product` p
                INNER JOIN '._DB_PREFIX_.'product_lang pl ON (pl.id_product = p.id_product '.(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? ' AND pl.id_lang = '.(int) $id_lang : '') .')
                '.(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? ' INNER JOIN '._DB_PREFIX_.'product_shop ps ON (ps.id_product = p.id_product AND ps.id_shop = p.id_shop_default ) ' : '').'
                WHERE ean13 != "" AND ean13 NOT REGEXP "^[0-9]{13}$"
                ORDER BY p.id_product ASC';
    $res_bad_format = Db::getInstance()->ExecuteS($sql_bad);

    $res_good_format = array();
    $sql_good = 'SELECT DISTINCT p.id_product, pl.name, p.ean13, "WRONG CHECK DIGIT" as problem,'.(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? ' ps.id_category_default ' : ' p.id_category_default').', '.(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? ' ps.active ' : ' p.active').'
                FROM `'._DB_PREFIX_.'product` p
                INNER JOIN '._DB_PREFIX_.'product_lang pl ON (pl.id_product = p.id_product '.(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? ' AND pl.id_lang = '.(int) $id_lang : '') .')
                '.(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? ' INNER JOIN '._DB_PREFIX_.'product_shop ps ON (ps.id_product = p.id_product AND ps.id_shop = p.id_shop_default ) ' : '').'
                WHERE ean13 != "" AND ean13 REGEXP "^[0-9]{13}$"
                ORDER BY p.id_product ASC';
    $res_good_format = Db::getInstance()->ExecuteS($sql_good);

    $res_bad_check_digit = array();
    foreach ($res_good_format as $row) {
        $ean13 = $row['ean13'];
        $ean12 = substr($ean13, 0, 12);
        $ean13_last_digit = (int) substr($ean13, 12);

        if ($ean13_last_digit != regenerate_check_digit($ean12)) {
            $res_bad_check_digit[] = $row;
        }
    }

    $res = array_merge($res_bad_format, $res_bad_check_digit);

    $content = '';
    $content_js = '';
    $results = 'OK';
    if (!empty($res) && count($res) > 0)
    {
        $results = 'KO';
        ob_start(); ?>
        <script type="text/javascript">

            var tbProductBadFormatEAN = dhxlSCExtCheck.tabbar.cells("table_<?php echo $action_name; ?>").attachToolbar();
            tbProductBadFormatEAN.setIconset('awesome');
            var idProductBadFormatEAN = '';
            tbProductBadFormatEAN.addButton("gotocatalog", 0, "", 'fad fa-external-link green', 'fad fa-external-link green');
            tbProductBadFormatEAN.setItemToolTip('gotocatalog','<?php echo _l('Go to the product in catalog.'); ?>');
            tbProductBadFormatEAN.addButton("exportcsv", 0, "", 'fad fa-file-csv green', 'fad fa-file-csv green');
            tbProductBadFormatEAN.setItemToolTip('exportcsv','<?php echo _l('Export grid to clipboard in CSV format for MSExcel with tab delimiter.'); ?>');
            tbProductBadFormatEAN.attachEvent("onClick",
                function(id){
                    if (id=='gotocatalog')
                    {
                        if(idProductBadFormatEAN !== '') {
                            let url = "?page=cat_tree&open_cat_grid="+idProductBadFormatEAN;
                            window.open(url,'_blank');
                        }

                    }
                    if(id=='exportcsv') {
                        displayQuickExportWindow(gridProductBadFormatEAN,1);
                    }
                });

            var gridProductBadFormatEAN = dhxlSCExtCheck.tabbar.cells("table_<?php echo $action_name; ?>").attachGrid();
            gridProductBadFormatEAN.setImagePath("lib/js/imgs/");
            gridProductBadFormatEAN.enableSmartRendering(true);
            gridProductBadFormatEAN.enableMultiselect(false);

            gridProductBadFormatEAN.setHeader("ID <?php echo _l('product'); ?>,<?php echo _l('EAN'); ?>,<?php echo _l('Name'); ?>,<?php echo _l('Error'); ?>,<?php echo _l('Active'); ?>");
            gridProductBadFormatEAN.setInitWidths("60,100,200,150,*");
            gridProductBadFormatEAN.setColAlign("left,left,left,left,left");
            gridProductBadFormatEAN.setColTypes("ro,ro,ro,ro,ro");
            gridProductBadFormatEAN.setColSorting("int,str,str,str,str");
            gridProductBadFormatEAN.attachHeader("#numeric_filter,#text_filter,#text_filter,#select_filter,#select_filter");
            gridProductBadFormatEAN.init();

            gridProductBadFormatEAN.attachEvent('onRowSelect',function(id){
                idProductBadFormatEAN = id;
            });

            var xml = '<rows>';
            <?php foreach ($res as $row) { ?>
            xml = xml+'   <row id="<?php echo $row['id_category_default'].'-'.$row['id_product']; ?>">';
            xml = xml+'      <cell><![CDATA[<?php echo $row['id_product']; ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php echo $row['ean13']; ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php echo addslashes($row['name']); ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php echo _l($row['problem']); ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php echo !empty($row['active']) ? _l('Yes') : _l('No'); ?>]]></cell>';
            xml = xml+'   </row>';
            <?php } ?>
            xml = xml+'</rows>';
            gridProductBadFormatEAN.parse(xml);

            sbProductBadFormatEAN=dhxlSCExtCheck.tabbar.cells("table_<?php echo $action_name; ?>").attachStatusBar();
            function getGridStat_ProductBadFormatEAN(){
                var filteredRows=gridProductBadFormatEAN.getRowsNum();
                var selectedRows=(gridProductBadFormatEAN.getSelectedRowId()?gridProductBadFormatEAN.getSelectedRowId().split(',').length:0);
                sbProductBadFormatEAN.setText('<?php echo count($res).' '._l('Errors'); ?>'+" - <?php echo _l('Filter')._l(':'); ?> "+filteredRows+" - <?php echo _l('Selection')._l(':'); ?> "+selectedRows);
            }
            gridProductBadFormatEAN.attachEvent("onFilterEnd", function(elements){
                getGridStat_ProductBadFormatEAN();
            });
            gridProductBadFormatEAN.attachEvent("onSelectStateChanged", function(id){
                getGridStat_ProductBadFormatEAN();
            });
            getGridStat_ProductBadFormatEAN();
        </script>
        <?php
        $content_js = ob_get_clean();
    }
    echo json_encode(array(
        'results' => $results,
        'contentType' => 'grid',
        'content' => $content,
        'title' => $tab_title,
        'contentJs' => $content_js,
    ));
}