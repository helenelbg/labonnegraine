<?php
if (!defined('STORE_COMMANDER')) { exit; }

$post_action = Tools::getValue('action');
$current_control_filename = basename(__FILE__, '.php');
$cell_id = 'table_'.$current_control_filename;
$moduleName = 'scfixmyprestashop';
$moduleNameLiteral = _l('Fix My Prestashop');
$modulePath = _PS_MODULE_DIR_.$moduleName.'/';

switch ($post_action) {
    case 'do_check':
        $content = '';
        $results = 'OK';

        if (file_exists($modulePath.$moduleName.'.php'))
        {
            $results = 'KO';
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
                echo '<p>'._l('This is an obsolete module. It is already integrated into %s via the "%s" menu.', null, array('Store Commander', _l('Tools').' > '._l('FixMyPrestashop'))).'</p>'; ?>
                <div class="main_list" style="clear: both">
                <?php echo '<ul>
                    <li><button class="btn_red" onclick="'.$current_control_filename.'_delete(this)">'._l('Click to delete the module').'</button> <i class="far fa-spinner fa-spin" style="display:none"></i></li>
                    </ul>'; ?>
                </div>
            </div>
            <script>
               function <?php echo $current_control_filename; ?>_delete(thisObject)
               {
                   $(thisObject).parent('li').find('i.fa-spinner').show();
                    $.post("index.php?ajax=1&act=all_win-fixmyprestashop_actions&check=<?php echo $current_control_filename; ?>", {
                        token: "<?php echo generateToken(date("YmdH"),42); ?>",
                        action: "delete"
                    }, function(response){
                        if(response === 'ok') {
                            dhxlSCExtCheck.tabbar.tabs("<?php echo $cell_id; ?>").close();
                            dhxlSCExtCheck.gridChecks.deleteRow('<?php echo $current_control_filename; ?>');
                        } else {
                            let splitted = response.split(' ');
                            if(splitted.length > 1 && splitted[0] === 'nok')
                            {
                                dhxlSCExtCheck.gridChecks.selectRowById('<?php echo $current_control_filename; ?>');
                                dhtmlx.message({
                                    text: "<?php echo _l('Unable to delete the module %s', true, array('<b>' . $moduleNameLiteral . '</b>')); ?>. " + response,
                                    css: "alert-error",
                                    expire: 30000
                                });
                            }
                        }
                        $(thisObject).parent('li').find('i.fa-spinner').hide();
                    });
                }
            </script>
            <?php
            $content = ob_get_clean();
        }

        echo json_encode(array(
            'results' => $results,
            'contentType' => 'content',
            'content' => $content,
            'title' => $moduleNameLiteral,
        ));

        break;
    case 'delete':
        $hash = generateToken(date("YmdH"),42);
        if(!Tools::isSubmit('token')
            || Tools::getValue('token') !== $hash) {
            exit('nok');
        }
        if (version_compare(_PS_VERSION_, '1.7', '>='))
        {
            $legacyLogger = new PrestaShop\PrestaShop\Adapter\LegacyLogger();
            $moduleDataProvider = new PrestaShop\PrestaShop\Adapter\Module\ModuleDataProvider($legacyLogger, Context::getContext()->getTranslator());
            $isInstalled = $moduleDataProvider->isInstalled($moduleName);
        }
        else
        {
            $isInstalled = Module::isInstalled($moduleName);
        }
        if($isInstalled)
        {
            $module = Module::getInstanceByName($moduleName);
            $uninstalled = (bool)$module->uninstall();
            if (!$uninstalled) {
                exit('nok uninstall');
            }
        }
        dirRemove($modulePath);
        if(file_exists($modulePath))
        {
            exit('nok folder deletion');
        }
        exit('ok');
}
