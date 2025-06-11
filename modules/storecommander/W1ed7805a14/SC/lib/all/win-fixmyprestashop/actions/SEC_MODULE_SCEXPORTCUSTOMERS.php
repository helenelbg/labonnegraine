<?php
$post_action = Tools::getValue('action');
$current_control_filename = basename(__FILE__, '.php');
$cell_id = 'table_'.$current_control_filename;
$moduleName = 'scexportcustomers';
$moduleNameLiteral = _l('Customers export');
$now = date('YmdHi');
$modulePath = _PS_MODULE_DIR_.$moduleName.'/';
$zipFile = '_'.$moduleName.'-backup-{version}.zip';

switch ($post_action) {
    case 'do_check':
        $content = '';
        $results = 'OK';

        ## remove old module backup zip older than ...
        $checkModuleNow = new DateTime();
        $maxBackupDateTime = $checkModuleNow->modify('-4 days')->format('Y-m-d');
        foreach (glob(_PS_MODULE_DIR_.'_'.$moduleName.'-backup*.zip') as $pathFile)
        {
            $moduleCreationDate = filectime($pathFile);
            if($moduleCreationDate)
            {
                $moduleCreationDateFormatted = date('Y-m-d', $moduleCreationDate);
                if ($moduleCreationDateFormatted < $maxBackupDateTime)
                {
                    @unlink($pathFile);
                }
            }
        }

        if (file_exists($modulePath.$moduleName.'.php'))
        {
            $extension = getExtensionDetail($moduleName);

            $version = Db::getInstance()->getValue('SELECT version
                                                        FROM `'._DB_PREFIX_.'module`
                                                        WHERE `name` ="'.$moduleName.'"');
            if (!empty($version) && version_compare($version, $extension['module_version'], '<'))
            {
                $zipFile = str_replace('{version}', $version, $zipFile);
                $results = 'KO';
                $backup_exists = file_exists(_PS_MODULE_DIR_.$zipFile);
                ob_start(); ?>
                <style>
                    .current_panel {
                        padding: 20px;
                        height: 100%;
                        overflow: auto;
                        font-family: Arial,sans-serif;
                        font-size: 0.9em;
                    }
                    .main_list li {
                        margin-bottom: 10px;
                    }
                    .main_list li ul {
                        margin-top: 10px;
                    }

                    .main_list li ul li{
                        list-style: none;
                        text-align: center;
                    }
                </style>
                <div class="current_panel" style="padding: 20px;height: 100%;overflow: auto;">
                    <img src="./lib/img/security.png" alt="" style="float: left; margin-right: 20px; margin-bottom: 20px;"/>
                    <?php
                    echo '<p>'._l('You need to update your module %s for security fixes and new features.', null, array('<b>'.$moduleNameLiteral.'</b>')).'</p>'; ?>
                    <div class="main_list" style="clear: both">
                    <?php echo '<ol>
                        <li>'._l('If you ordered a module modification please contact us at <br>%s or by %s<br>before updating the module', null, array('<a href="mailto:support@storecommander.com">support@storecommander.com</a>', '<a href="javascript:void(0);" onclick="Intercom(\'showNewMessage\');">'._l('tchat').'</a>')).'</li>
                        <li>
                            <button class="btn_red" onclick="'.$current_control_filename.'_backup(this)">'._l('Click here to download a backup of your current module').' </button> <i class="far fa-spinner fa-spin" style="display:none"></i>
                            <br>
                            <p class="backup_available" '.($backup_exists ? '' : 'style="display: none;"').'>'._l('Backup available here:%s', null, array('<a href="/modules/'.$zipFile.'" target="_blank">/modules/'.$zipFile.'</a>')).'</p>
                        </li>
                        <li><button class="btn_red" onclick="'.$current_control_filename.'_update(this)">'._l('Click here to update the module').'</button> <i class="far fa-spinner fa-spin" style="display:none"></i></li>

                        </ol>'; ?>
                    </div>
                </div>
                <script>
                   function <?php echo $current_control_filename; ?>_backup(thisObject)
                   {
                       $(thisObject).parent('li').find('i.fa-spinner').show();
                        $.post("index.php?ajax=1&act=all_win-fixmyprestashop_actions&check=<?php echo $current_control_filename; ?>", {action: "backup"}, function(response){
                            if(response === 'ok')
                            {
                                dhtmlx.message({
                                    text:'<?php echo _l('Backup done'); ?>',
                                    type:'success',
                                    expire: 10000
                                });
                                $(thisObject).parent('li').find('.backup_available').show();
                            } else {
                                dhtmlx.message({
                                    text: "<?php echo _l('Unable to make a backup of the module %s', true, array('<b>'.$moduleNameLiteral.'</b>')); ?>",
                                    css: "alert-error",
                                    expire: 10000
                                });
                            }
                            $(thisObject).parent('li').find('i.fa-spinner').hide();
                        });
                    }
                   function <?php echo $current_control_filename; ?>_update(thisObject)
                   {
                       $(thisObject).parent('li').find('i.fa-spinner').show();
                        $.post("index.php?ajax=1&act=all_win-fixmyprestashop_actions&check=<?php echo $current_control_filename; ?>", {action: "update"}, function(response){
                            if(response === 'ok') {
                                dhxlSCExtCheck.tabbar.tabs("<?php echo $cell_id; ?>").close();
                                dhxlSCExtCheck.gridChecks.selectRowById('<?php echo $current_control_filename; ?>');
                                doCheck(false);
                            } else {
                                dhtmlx.message({
                                    text: "<?php echo _l('Unable to update the module %s', true, array('<b>'.$moduleNameLiteral.'</b>')); ?>",
                                    css: "alert-error",
                                    expire: 30000
                                });
                            }
                            $(thisObject).parent('li').find('i.fa-spinner').hide();
                        });
                    }
                </script>
                <?php
                $content = ob_get_clean();
            }
        }

        echo json_encode(array(
            'results' => $results,
            'contentType' => 'content',
            'content' => $content,
            'title' => $moduleNameLiteral,
        ));

        break;
    case 'backup':
        $version = Db::getInstance()->getValue('SELECT version
                                                        FROM `'._DB_PREFIX_.'module`
                                                        WHERE `name` ="'.$moduleName.'"');
        $zipFile = str_replace('{version}', $version, $zipFile);
        $command = 'zip -rq '._PS_MODULE_DIR_.$zipFile.' '.$modulePath;
        exec($command, $out, $commandResult);
        if ($commandResult)
        {
            exit('nok');
        }
        exit('ok');
    case 'update':
        $extract_done = (bool) getScExtensionAndExtract($moduleName);
        if ($extract_done)
        {
            $extension = getExtensionDetail($moduleName);
            Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'module SET version="'.$extension['module_version'].'" WHERE name = "'.$moduleName.'"');
            exit('ok');
        }
        exit('nok');
}
