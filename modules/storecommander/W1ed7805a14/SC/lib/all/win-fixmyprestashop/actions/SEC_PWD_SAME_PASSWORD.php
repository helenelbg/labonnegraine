<?php
$post_action = Tools::getValue('action');
if (!empty($post_action) && $post_action == 'do_check')
{
    $sql = 'SELECT ee.id_employee,ee.email, cc.id_customer,cc.email as email_customer FROM '._DB_PREFIX_.'employee ee JOIN '._DB_PREFIX_.'customer cc ON cc.passwd = ee.passwd LIMIT 1500';
    $res = Db::getInstance()->ExecuteS($sql);

    $content = '';
    $content_js = '';
    $results = 'OK';
    if (!empty($res) && count($res) > 0)
    {
        $results = 'KO';
        ob_start(); ?>
        <script type="text/javascript">

            var tbSamePwd = dhxlSCExtCheck.tabbar.cells("table_SEC_PWD_SAME_PASSWORD").attachToolbar();
            tbSamePwd.setIconset('awesome');

            tbSamePwd.addButton("exportcsv", 0, "", 'fad fa-file-csv green', 'fad fa-file-csv green');
            tbSamePwd.setItemToolTip('exportcsv','<?php echo _l('Export grid to clipboard in CSV format for MSExcel with tab delimiter.'); ?>');
            tbSamePwd.addButton("print", 0, "", 'fad fa-print', 'fad fa-print');
            tbSamePwd.setItemToolTip('print','<?php echo _l('Print grid', 1); ?>');
            tbSamePwd.attachEvent("onClick",
                function(id){
                    if(id=='exportcsv') {
                        displayQuickExportWindow(gridSamePwd,1);
                    }
                    if (id=='print'){
                        gridSamePwd.printView();
                    }
                });

            var gridSamePwd = dhxlSCExtCheck.tabbar.cells("table_SEC_PWD_SAME_PASSWORD").attachGrid();
            gridSamePwd.setImagePath("lib/js/imgs/");
            gridSamePwd.enableSmartRendering(true);
            gridSamePwd.enableMultiselect(true);

            gridSamePwd.setHeader("ID <?php echo _l('employee'); ?>,<?php echo _l('Employee email'); ?>,ID <?php echo _l('customer'); ?>,<?php echo _l('Customer email'); ?>");
            gridSamePwd.setInitWidths("100,*,100,*");
            gridSamePwd.setColAlign("left,left,left,left");
            gridSamePwd.setColTypes("ro,ro,ro,ro");
            gridSamePwd.setColSorting("int,str,int,str");
            gridSamePwd.attachHeader("#numeric_filter,#text_filter,#numeric_filter,#text_filter");
            gridSamePwd.init();

            var xml = '<rows>';
            <?php foreach ($res as $row) { ?>
            xml = xml+'   <row id="<?php echo $row['id_employee'].'_'.$row['id_customer']; ?>">';
            xml = xml+'      <cell><![CDATA[<?php echo $row['id_employee']; ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php echo $row['email']; ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php echo $row['id_customer']; ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php echo $row['email_customer']; ?>]]></cell>';
            xml = xml+'   </row>';
            <?php } ?>
            xml = xml+'</rows>';
            gridSamePwd.parse(xml);

            sbSamePwd=dhxlSCExtCheck.tabbar.cells("table_SEC_PWD_SAME_PASSWORD").attachStatusBar();
            function getGridStat_SamePwd(){
                var filteredRows=gridSamePwd.getRowsNum();
                var selectedRows=(gridSamePwd.getSelectedRowId()?gridSamePwd.getSelectedRowId().split(',').length:0);
                sbSamePwd.setText('<?php echo count($res).' '._l('Errors'); ?>'+" - <?php echo _l('Filter')._l(':'); ?> "+filteredRows+" - <?php echo _l('Selection')._l(':'); ?> "+selectedRows);
            }
            gridSamePwd.attachEvent("onFilterEnd", function(elements){
                getGridStat_SamePwd();
            });
            gridSamePwd.attachEvent("onSelectStateChanged", function(id){
                getGridStat_SamePwd();
            });
            getGridStat_SamePwd();
        </script>
        <?php
        $content_js = ob_get_clean();
    }
    echo json_encode(array(
        'results' => $results,
        'contentType' => 'grid',
        'content' => $content,
        'title' => _l('Same password'),
        'contentJs' => $content_js,
    ));
}
