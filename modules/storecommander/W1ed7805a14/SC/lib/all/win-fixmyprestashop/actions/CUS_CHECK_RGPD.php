<?php
$post_action = Tools::getValue('action');
if (!empty($post_action) && $post_action == 'do_check')
{
    $wanted_month = (int) _s('APP_FIX_CHECK_RGPD_MONTH');
    if ($wanted_month==0)
    {
        $wanted_month = 12;
    }

    $wanted_month_last_conn = (int) _s('APP_FIX_CHECK_RGPD_LASTCONN_MONTH');
    if ($wanted_month_last_conn==0)
    {
        $wanted_month_last_conn = 12;
    }

    $date = new DateTime(date('Y-m-d'));
    $date->modify('-'.(int) $wanted_month.' month');
    $cus_date_add = $date->format('Y-m-d');

    $date = new DateTime(date('Y-m-d'));
    $date->modify('-'.(int) $wanted_month_last_conn.' month');
    $cus_last_conn = $date->format('Y-m-d');

    $sql = 'SELECT c.*
             FROM `'._DB_PREFIX_.'customer` c
             WHERE c.id_customer NOT IN (SELECT o.id_customer FROM `'._DB_PREFIX_.'orders` o GROUP BY o.id_customer)
             AND c.id_customer NOT IN (SELECT guest.id_customer FROM `'._DB_PREFIX_.'guest` guest LEFT JOIN `'._DB_PREFIX_.'connections` conn ON (guest.id_guest = conn.id_guest) WHERE conn.date_add >= "'.pSQL($cus_last_conn).' 00:00:00")
             AND c.date_add <="'.pSQL($cus_date_add).' 00:00:00"
             ORDER BY c.id_customer ASC';
    $res = Db::getInstance()->ExecuteS($sql);

    $content = '';
    $content_js = '';
    $results = 'OK';
    if (!empty($res) && count($res) > 0)
    {
        $results = 'KO';
        ob_start(); ?>
        <script type="text/javascript">

            var tbCheckRGPD = dhxlSCExtCheck.tabbar.cells("table_CUS_CHECK_RGPD").attachToolbar();
            tbCheckRGPD.setIconset('awesome');
            tbCheckRGPD.addButton("selectall", 0, "", 'fa fa-bolt yellow', 'fa fa-bolt yellow');
            tbCheckRGPD.setItemToolTip('selectall','<?php echo _l('Select all'); ?>');
            tbCheckRGPD.addButton("exportcsv", 0, "", 'fad fa-file-csv green', 'fad fa-file-csv green');
            tbCheckRGPD.setItemToolTip('exportcsv','<?php echo _l('Export grid to clipboard in CSV format for MSExcel with tab delimiter.'); ?>');
            tbCheckRGPD.addButton("print", 0, "", 'fad fa-print', 'fad fa-print');
            tbCheckRGPD.setItemToolTip('print','<?php echo _l('Print grid', 1); ?>');
            tbCheckRGPD.attachEvent("onClick",
                function(id){
                    if (id=='selectall')
                    {
                        gridCheckRGPD.selectAll();
                        getGridStat_CheckRGPD();
                    }
                    if(id=='exportcsv') {
                        displayQuickExportWindow(gridCheckRGPD,1);
                    }
                    if (id=='print'){
                        gridCheckRGPD.printView();
                    }
                });

            var gridCheckRGPD = dhxlSCExtCheck.tabbar.cells("table_CUS_CHECK_RGPD").attachGrid();
            gridCheckRGPD.setImagePath("lib/js/imgs/");
            gridCheckRGPD.enableSmartRendering(true);
            gridCheckRGPD.enableMultiselect(true);

            gridCheckRGPD.setHeader("ID,<?php echo _l('Name'); ?>,<?php echo _l('Email'); ?>");
            gridCheckRGPD.setInitWidths("100,200,200");
            gridCheckRGPD.setColAlign("left,left,left");
            gridCheckRGPD.setColTypes("ro,ro,ro");
            gridCheckRGPD.setColSorting("int,str,str");
            gridCheckRGPD.attachHeader("#numeric_filter,#text_filter,#text_filter");
            gridCheckRGPD.init();

            var xml = '<rows>';
            <?php foreach ($res as $customer)
            { ?>
            xml = xml+'   <row id="<?php echo $customer['id_customer']; ?>">';
            xml = xml+'      <cell><![CDATA[<?php echo $customer['id_customer']; ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php echo addslashes($customer['firstname'].' '.$customer['lastname']); ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php echo addslashes($customer['email']); ?>]]></cell>';
            xml = xml+'   </row>';
            <?php
            } ?>
            xml = xml+'</rows>';
            gridCheckRGPD.parse(xml);

            sbCheckRGPD=dhxlSCExtCheck.tabbar.cells("table_CUS_CHECK_RGPD").attachStatusBar();
            function getGridStat_CheckRGPD(){
                var filteredRows=gridCheckRGPD.getRowsNum();
                var selectedRows=(gridCheckRGPD.getSelectedRowId()?gridCheckRGPD.getSelectedRowId().split(',').length:0);
                sbCheckRGPD.setText('<?php echo count($res).' '._l('Errors'); ?>'+" - <?php echo _l('Filter')._l(':'); ?> "+filteredRows+" - <?php echo _l('Selection')._l(':'); ?> "+selectedRows);
            }
            gridCheckRGPD.attachEvent("onFilterEnd", function(elements){
                getGridStat_CheckRGPD();
            });
            gridCheckRGPD.attachEvent("onSelectStateChanged", function(id){
                getGridStat_CheckRGPD();
            });
            getGridStat_CheckRGPD();

            custom_param = "";
        </script>
        <?php
            $content_js = ob_get_clean();
    }
    echo json_encode(array(
        'results' => $results,
        'contentType' => 'grid',
        'content' => $content,
        'title' => _l('Cus. GDPR'),
        'contentJs' => $content_js,
    ));
}
