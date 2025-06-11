<?php
$post_action = Tools::getValue('action');
$action_name = 'CUS_DB_DUPLICATE_EMAIL';
$tab_title = _l('Cus. same email');

if (!empty($post_action) && $post_action == 'do_check')
{
    $res = array();
    $sql = 'SELECT *
            FROM '._DB_PREFIX_.'customer
            WHERE email IN (SELECT email
                            FROM '._DB_PREFIX_.'customer
                            GROUP BY email
                            HAVING COUNT(email) > 1)';
    $res = Db::getInstance()->ExecuteS($sql);

    if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
    {
        $shops = array();
        $sql = 'SELECT * FROM '._DB_PREFIX_.'shop';
        $tmps = Db::getInstance()->ExecuteS($sql);
        foreach ($tmps as $tmp)
        {
            $shops[$tmp['id_shop']] = $tmp['name'];
        }
    }

    $content = '';
    $content_js = '';
    $results = 'OK';
    if (!empty($res) && count($res) > 0)
    {
        $tmp_ids = array();
        foreach ($res as $row)
        {
            $tmp_ids[] = $row['id_customer'];
        }
        $sql = 'SELECT id_customer, COUNT(id_order) as count_order
                FROM '._DB_PREFIX_.'orders 
                WHERE id_customer IN ('.pSQL(implode(',', $tmp_ids)).') 
                GROUP BY id_customer';
        $ord_res = Db::getInstance()->ExecuteS($sql);
        $cache_nb_order_by_customer = array();
        if (!empty($ord_res))
        {
            foreach ($ord_res as $row)
            {
                $cache_nb_order_by_customer[$row['id_customer']] = (int) $row['count_order'];
            }
        }

        $results = 'KO';
        ob_start(); ?>
        <script type="text/javascript">

            var tbCustomerSameEmail = dhxlSCExtCheck.tabbar.cells("table_<?php echo $action_name; ?>").attachToolbar();
            tbCustomerSameEmail.setIconset('awesome');
            var idCustomerSameEmail = '';
            tbCustomerSameEmail.addButton("exportcsv", 0, "", 'fad fa-file-csv green', 'fad fa-file-csv green');
            tbCustomerSameEmail.setItemToolTip('exportcsv','<?php echo _l('Export grid to clipboard in CSV format for MSExcel with tab delimiter.'); ?>');
            tbCustomerSameEmail.attachEvent("onClick",
                function(id){
                    if(id=='exportcsv') {
                        displayQuickExportWindow(gridCustomerSameEmail,1);
                    }
                });

            var gridCustomerSameEmail = dhxlSCExtCheck.tabbar.cells("table_<?php echo $action_name; ?>").attachGrid();
            gridCustomerSameEmail.setImagePath("lib/js/imgs/");
            gridCustomerSameEmail.enableSmartRendering(true);
            gridCustomerSameEmail.enableMultiselect(false);

            gridCustomerSameEmail.setHeader("ID <?php echo _l('customer'); ?>,<?php echo _l('email'); ?>,<?php echo _l('deleted'); ?>,<?php echo _l('Number of orders'); ?><?php echo version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? ','._l('Shop') : ''; ?>");
            gridCustomerSameEmail.setInitWidths("80,200,80,80<?php echo version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? ',120' : ''; ?>");
            gridCustomerSameEmail.setColAlign("left,left,left,left<?php echo version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? ',left' : ''; ?>");
            gridCustomerSameEmail.setColSorting("int,str,str,int<?php echo version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? ',str' : ''; ?>");
            gridCustomerSameEmail.setColTypes("ed,ed,ro,ro<?php echo version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? ',ro' : ''; ?>");
            gridCustomerSameEmail.attachHeader("#numeric_filter,#text_filter,#select_filter,#numeric_filter<?php echo version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? ',#select_filter' : ''; ?>");
            gridCustomerSameEmail.init();

            gridCustomerSameEmail.attachEvent('onRowSelect',function(id){
                idCustomerSameEmail = id;
            });

            var xml = '<rows>';
            <?php foreach ($res as $row) { ?>
            xml = xml+'   <row id="<?php echo $row['id_customer']; ?>">';
            xml = xml+'      <cell><![CDATA[<?php echo $row['id_customer']; ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php echo $row['email']; ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php echo !empty($row['deleted']) ? _l('Yes') : _l('No'); ?>]]></cell>';
            xml = xml+'      <cell><?php echo array_key_exists($row['id_customer'], $cache_nb_order_by_customer) ? $cache_nb_order_by_customer[$row['id_customer']] : 0; ?></cell>';
            <?php if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) { ?>
            xml = xml+'      <cell><![CDATA[<?php echo addslashes($shops[$row['id_shop']]); ?>]]></cell>';
            <?php } ?>
            xml = xml+'   </row>';
            <?php } ?>
            xml = xml+'</rows>';
            gridCustomerSameEmail.parse(xml);

            sbCustomerSameEmail=dhxlSCExtCheck.tabbar.cells("table_<?php echo $action_name; ?>").attachStatusBar();
            function getGridStat_CustomerSameEmail(){
                var filteredRows=gridCustomerSameEmail.getRowsNum();
                var selectedRows=(gridCustomerSameEmail.getSelectedRowId()?gridCustomerSameEmail.getSelectedRowId().split(',').length:0);
                sbCustomerSameEmail.setText('<?php echo count($res).' '._l('Errors'); ?>'+" - <?php echo _l('Filter')._l(':'); ?> "+filteredRows+" - <?php echo _l('Selection')._l(':'); ?> "+selectedRows);
            }
            gridCustomerSameEmail.attachEvent("onFilterEnd", function(elements){
                getGridStat_CustomerSameEmail();
            });
            gridCustomerSameEmail.attachEvent("onSelectStateChanged", function(id){
                getGridStat_CustomerSameEmail();
            });
            getGridStat_CustomerSameEmail();
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
