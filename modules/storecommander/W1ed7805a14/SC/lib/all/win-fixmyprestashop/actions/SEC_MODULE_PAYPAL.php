<?php
$post_action = Tools::getValue('action');
$current_control_filename = basename(__FILE__, '.php');
$cell_id = 'table_'.$current_control_filename;
$vulnerableFilePath = _PS_MODULE_DIR_. 'paypal/ipn.php';

switch($post_action)
{
    case 'do_check':
        $content = '';
        $results = 'OK';
        $moduleName = 'paypal';
        $versionFound = Db::getInstance()->getValue('SELECT version
                                                            FROM `'._DB_PREFIX_.'module`
                                                            WHERE `name` ="'.pSQL($moduleName).'" 
                                                            AND version BETWEEN "3.12.0" AND "3.16.3"');
        if($versionFound
            && file_exists($vulnerableFilePath))
        {
            $vulnerableContent = file_get_contents($vulnerableFilePath);
            $lines = explode("\n", $vulnerableContent);

            foreach ($lines as $k => $line)
            {
                if (strpos($line, 'paypal_hss_email_error')
                    && strpos($line, "'email' => Tools::getValue('receiver_email')")
                ) {
                    $results = 'KO';
                    break;
                }
            }
        }
        if($results == 'KO')
        {
            ob_start(); ?>
            <div style="padding: 20px;height: 100%;overflow: auto;">
                <img src="./lib/img/security.png" alt="" style="float: left; margin-right: 20px; margin-bottom: 20px;"/>
                <?php
                echo '<p><strong>'._l('This version (%s) of module %s contains a critical security breach', null, array($versionFound, _l($moduleName))).'</strong></p>'; ?>
                <div style="clear: both">
                    <?php echo '<ul><li><strong>'._l('Please upgrade the module to the latest version.').'</strong><br>'._l('or').'<br/></li>
                        <li><button class="btn_red" onclick="'.$current_control_filename.'_applypatch(this)">'._l('Click here to apply a patch').'</button> <i class="far fa-spinner fa-spin" style="display:none"></i></li>';
            echo '</ul>'; ?>
                </div>
            </div>
            <script>
                   function <?php echo $current_control_filename; ?>_applypatch(thisObject)
                   {
                       $(thisObject).parent('li').find('i.fa-spinner').show();
                        $.post("index.php?ajax=1&act=all_win-fixmyprestashop_actions&check=<?php echo $current_control_filename; ?>", {
                            action: "apply_patch"
                        }, function(response){
                            let jsonResponse = JSON.parse(response);
                            dhtmlx.message({
                                text:jsonResponse.text,
                                type:jsonResponse.state,
                                expire: 5000
                            });
                            $(thisObject).parent('li').find('i.fa-spinner').hide();
                            dhxlSCExtCheck.tabbar.tabs("table_<?php echo $current_control_filename; ?>").close();
                            dhxlSCExtCheck.gridChecks.selectRowById('<?php echo $current_control_filename; ?>');
                            doCheck(false);
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
            'title' => _l('Security Files'),
        ));
        break;
    case 'apply_patch':
        $moduleName = 'paypal';
        $versionFound = Db::getInstance()->getValue('SELECT version
                                                            FROM `'._DB_PREFIX_.'module`
                                                            WHERE `name` ="'.pSQL($moduleName).'" 
                                                            AND version BETWEEN "3.12.0" AND "3.16.3"');
        $response = array(
            'text' => _l('Error during apply patch'),
            'state' => 'error'
        );
        if($versionFound
            && file_exists($vulnerableFilePath))
        {
            $vulnerableContent = file_get_contents($vulnerableFilePath);
            $lines = explode("\n", $vulnerableContent);

            foreach ($lines as $k => $line)
            {
                if (strpos($line, 'paypal_hss_email_error')
                    && strpos($line, "'email' => Tools::getValue('receiver_email')")
                ) {
                    $lines[$k] = "    Db::getInstance()->insert('paypal_hss_email_error', ['id_cart' => (int) \$custom['id_cart'], 'email' => pSQL(Tools::getValue('receiver_email', ''))]);";
                }
            }
            $contentFixed = implode("\n", $lines);
            if(file_put_contents($vulnerableFilePath, $contentFixed) !== false)
            {
                $response = array(
                    'text' => _l('Patched'),
                    'state' => 'success'
                );
            }
        }
        die(json_encode($response));
}
