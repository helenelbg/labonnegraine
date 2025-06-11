<?php
$post_action = Tools::getValue('action');
if (!empty($post_action) && $post_action == 'do_check')
{
    $period_in_months = 3;
    $date = new DateTime(date('Y-m-d'));
    $date->modify('-'.(int) $period_in_months.' month');
    $date_check_employee_last_logon = $date->format('Y-m-d');

    $sql = 'SELECT ee.*
             FROM `'._DB_PREFIX_.'employee` ee 
             WHERE ee.last_connection_date <="'.pSQL($date_check_employee_last_logon).' 00:00:00" 
             AND ee.active=1
             ORDER BY ee.id_employee ASC';
    $res = Db::getInstance()->ExecuteS($sql);

    $content = '';
    $content_js = '';
    $results = 'OK';
    if (!empty($res) && count($res) > 0)
    {
        $results = 'KO';
        ob_start(); ?>
        <script type="text/javascript">

            var tbCheckEmployeeNoLogon = dhxlSCExtCheck.tabbar.cells("table_SEC_DB_EMPLOYEE_NO_LOGON_3MONTHS").attachToolbar();
            tbCheckEmployeeNoLogon.setIconset('awesome');
            tbCheckEmployeeNoLogon.addButton("selectall", 0, "", 'fa fa-bolt yellow', 'fa fa-bolt yellow');
            tbCheckEmployeeNoLogon.setItemToolTip('selectall','<?php echo _l('Select all'); ?>');
            tbCheckEmployeeNoLogon.addButton("exportcsv", 0, "", 'fad fa-file-csv green', 'fad fa-file-csv green');
            tbCheckEmployeeNoLogon.setItemToolTip('exportcsv','<?php echo _l('Export grid to clipboard in CSV format for MSExcel with tab delimiter.'); ?>');
            tbCheckEmployeeNoLogon.addButton("print", 0, "", 'fad fa-print', 'fad fa-print');
            tbCheckEmployeeNoLogon.setItemToolTip('print','<?php echo _l('Print grid', 1); ?>');
            tbCheckEmployeeNoLogon.addButton("disableaccount", 0, "", 'fa fa-minus-circle green', 'fa fa-minus-circle green');
            tbCheckEmployeeNoLogon.setItemToolTip('disableaccount','<?php echo _l('Disable employees'); ?>');
            tbCheckEmployeeNoLogon.attachEvent("onClick",
                function(id){
                    if (id=='selectall')
                    {
                        gridCheckEmployeeNoLogon.selectAll();
                        getGridStat_CheckEmployeeNoLogon();
                    }
                    if(id=='exportcsv') {
                        displayQuickExportWindow(gridCheckEmployeeNoLogon,1);
                    }
                    if(id=='disableaccount') {
                        disableEmployeeAccounts();;
                    }
                    if (id=='print'){
                        gridCheckEmployeeNoLogon.printView();
                    }
                });

            var gridCheckEmployeeNoLogon = dhxlSCExtCheck.tabbar.cells("table_SEC_DB_EMPLOYEE_NO_LOGON_3MONTHS").attachGrid();
            gridCheckEmployeeNoLogon.setImagePath("lib/js/imgs/");
            gridCheckEmployeeNoLogon.enableSmartRendering(true);
            gridCheckEmployeeNoLogon.enableMultiselect(true);

            gridCheckEmployeeNoLogon.setHeader("ID,<?php echo _l('Email'); ?>,<?php echo _l('Last logon date'); ?>");
            gridCheckEmployeeNoLogon.setInitWidths("100,200,200");
            gridCheckEmployeeNoLogon.setColAlign("left,left,left");
            gridCheckEmployeeNoLogon.setColSorting("int,str,str");
            gridCheckEmployeeNoLogon.attachHeader("#numeric_filter,#text_filter,#text_filter");
            gridCheckEmployeeNoLogon.init();

            var xml = '<rows>';
            <?php foreach ($res as $customer)
            {
            ?>
            xml = xml+'   <row id="<?php echo $customer['id_employee']; ?>">';
            xml = xml+'      <cell><![CDATA[<?php echo $customer['id_employee']; ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php echo addslashes($customer['email']); ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php echo addslashes($customer['last_connection_date']); ?>]]></cell>';
            xml = xml+'   </row>';
            <?php
            } ?>
            xml = xml+'</rows>';
            gridCheckEmployeeNoLogon.parse(xml);

            sbCheckEmployeeNoLogon=dhxlSCExtCheck.tabbar.cells("table_SEC_DB_EMPLOYEE_NO_LOGON_3MONTHS").attachStatusBar();
            function getGridStat_CheckEmployeeNoLogon(){
                var filteredRows=gridCheckEmployeeNoLogon.getRowsNum();
                var selectedRows=(gridCheckEmployeeNoLogon.getSelectedRowId()?gridCheckEmployeeNoLogon.getSelectedRowId().split(',').length:0);
                sbCheckEmployeeNoLogon.setText('<?php echo count($res).' '._l('Errors'); ?>'+" - <?php echo _l('Filter')._l(':'); ?> "+filteredRows+" - <?php echo _l('Selection')._l(':'); ?> "+selectedRows);
            }

            function disableEmployeeAccounts()
            {
                var selectedEmployeeNoLogon = gridCheckEmployeeNoLogon.getSelectedRowId();
                if(selectedEmployeeNoLogon==null || selectedEmployeeNoLogon=="")
                    selectedEmployeeNoLogon = 0;
                if(selectedEmployeeNoLogon!="0")
                {
                    $.post("index.php?ajax=1&act=all_win-fixmyprestashop_actions&check=SEC_DB_EMPLOYEE_NO_LOGON_3MONTHS&id_lang="+SC_ID_LANG, { "action": "disable_employee_account", "ids": selectedEmployeeNoLogon}, function(data){
                        dhxlSCExtCheck.tabbar.tabs("table_SEC_DB_EMPLOYEE_NO_LOGON_3MONTHS").close();

                        dhxlSCExtCheck.gridChecks.selectRowById('SEC_DB_EMPLOYEE_NO_LOGON_3MONTHS');
                        doCheck(false);
                    });
                }
            }

            gridCheckEmployeeNoLogon.attachEvent("onFilterEnd", function(elements){
                getGridStat_CheckEmployeeNoLogon();
            });
            gridCheckEmployeeNoLogon.attachEvent("onSelectStateChanged", function(id){
                getGridStat_CheckEmployeeNoLogon();
            });
            getGridStat_CheckEmployeeNoLogon();

            custom_param = "";
        </script>
        <?php
        $content_js = ob_get_clean();
    }
    echo json_encode(array(
        'results' => $results,
        'contentType' => 'grid',
        'content' => $content,
        'title' => _l('Emp. not logged in'),
        'contentJs' => $content_js,
    ));
}
elseif (!empty($post_action) && $post_action == 'disable_employee_account')
{
    $post_ids = Tools::getValue('ids');
    if (!empty($post_ids))
    {
        $ids = explode(',', $post_ids);
        foreach ($ids as $id)
        {
            $sql = 'UPDATE `'._DB_PREFIX_.'employee` SET active=0
                    WHERE id_employee = '.(int) $id;
            $res = Db::getInstance()->ExecuteS($sql);
        }
    }
}
