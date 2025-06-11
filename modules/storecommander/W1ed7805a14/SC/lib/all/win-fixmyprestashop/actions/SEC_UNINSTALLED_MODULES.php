<?php

$post_action = Tools::getValue('action');
$current_control_filename = basename(__FILE__, '.php');
$cell_id = 'table_'.$current_control_filename;
$scSalt = SCI::getConfigurationValue('SC_SALT');

if (!empty($post_action) && $post_action == 'do_check')
{
    $modulesDisk = new DirectoryIterator(_PS_MODULE_DIR_);
    $moduleDatabase = Db::getInstance()->executeS('SELECT name FROM '._DB_PREFIX_.'module');
    $moduleDatabase = array_column($moduleDatabase, 'name');
    $uninstalledModuleToDelete = array();
    foreach ($modulesDisk as $directoryDisk)
    {
        if ($directoryDisk->isDir() && !$directoryDisk->isDot() && !in_array($directoryDisk, $moduleDatabase))
        {
            $baseName = $directoryDisk->getBasename();
            $uninstalledModuleToDelete[md5($baseName.$scSalt)] = $baseName;
        }
    }

    $content = '';
    $content_js = '';
    $results = 'OK';
    if (!empty($uninstalledModuleToDelete))
    {
        $results = 'KO';
        ob_start(); ?>
        <script type="text/javascript">

            var tbUninstalledModules = dhxlSCExtCheck.tabbar.cells("<?php echo $cell_id; ?>").attachToolbar();
            tbUninstalledModules.setIconset('awesome');
            tbUninstalledModules.addButton("selectall", 0, "", 'fa fa-bolt yellow', 'fa fa-bolt yellow');
            tbUninstalledModules.setItemToolTip('selectall','<?php echo _l('Select all'); ?>');
            tbUninstalledModules.addButton("delete", 0, "", 'fa fa-minus-circle red', 'fa fa-minus-circle red');
            tbUninstalledModules.setItemToolTip('delete','<?php echo _l('Delete modules'); ?>');
            tbUninstalledModules.attachEvent("onClick",
                function(id){
                    switch(id) {
                        case 'selectall':
                            gridUninstalledModules.selectAll();
                            break;
                        case 'delete':
                            if(confirm('<?php echo _l('Are you sure you want to delete the selected items?', 1); ?>')) {
                                deleteUninstalledModules();
                            }
                            break;
                    }
                });

            var gridUninstalledModules = dhxlSCExtCheck.tabbar.cells("<?php echo $cell_id; ?>").attachGrid();
            gridUninstalledModules.setImagePath("lib/js/imgs/");
            gridUninstalledModules.enableSmartRendering(true);
            gridUninstalledModules.enableMultiselect(true);

            gridUninstalledModules.setHeader("<?php echo _l('Module'); ?>");
            gridUninstalledModules.setInitWidths("*");
            gridUninstalledModules.setColAlign("left");
            gridUninstalledModules.setColTypes("ro");
            gridUninstalledModules.setColSorting("str");
            gridUninstalledModules.attachHeader("#text_filter");
            gridUninstalledModules.init();

            let xml = '<rows>';
            <?php foreach ($uninstalledModuleToDelete as $uniqueId => $moduleName)
        {
            ?>
                xml = xml+'    <row id="<?php echo $uniqueId; ?>">';
                xml = xml+'        <cell><![CDATA[<?php echo $moduleName; ?>]]></cell>';
                xml = xml+'    </row>';
                <?php
        } ?>
            xml = xml+'</rows>';
            gridUninstalledModules.parse(xml);

            function deleteUninstalledModules()
            {
                let selectedModules = gridUninstalledModules.getSelectedRowId();
                if(selectedModules)
                {
                    $.post("index.php?ajax=1&act=all_win-fixmyprestashop_actions&check=<?php echo $current_control_filename; ?>&id_lang="+SC_ID_LANG, {
                        "action": 'deleteModuleList',
                        "mlist": selectedModules
                    }, function(){
                        dhxlSCExtCheck.tabbar.tabs("<?php echo $cell_id; ?>").close();
                        dhxlSCExtCheck.gridChecks.selectRowById('<?php echo $current_control_filename; ?>');
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
        'title' => _l('Uninstalled modules'),
        'contentJs' => $content_js,
    ));
}
elseif (!empty($post_action) && $post_action == 'deleteModuleList')
{
    $mList = Tools::getValue('mlist');
    if (!empty($mList))
    {
        $mListFromPayload = explode(',', $mList);
        $modulesDisk = new DirectoryIterator(_PS_MODULE_DIR_);
        $uninstalledModuleToDelete = array();
        foreach ($modulesDisk as $directoryDisk)
        {
            if ($directoryDisk->isDir() && !$directoryDisk->isDot() && in_array(md5($directoryDisk->getBasename().$scSalt), $mListFromPayload) && !empty($directoryDisk->getBasename()))
            {
                dirRemove(_PS_MODULE_DIR_.$directoryDisk->getBasename());
            }
        }
    }
}
