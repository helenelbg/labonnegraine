<?php
$post_action = Tools::getValue('action');

if (!empty($post_action) && $post_action == 'do_check')
{
    $content = '';
    $results = 'OK';

    $found = false;
    $override_found = false;
    $path_to_check = array(
        _PS_CLASS_DIR_.'form/CustomerForm.php',
        _PS_OVERRIDE_DIR_.'classes/form/CustomerForm.php',
    );
    $needle = <<<'EOT'
$customer = new Customer($this->getValue('id_customer'));
EOT;
    if (file_exists(_PS_CLASS_DIR_.'form/CustomerForm.php')
        && !file_exists(_PS_OVERRIDE_DIR_.'classes/form/CustomerForm.php'))
    {
        $content = file_get_contents(_PS_CLASS_DIR_.'form/CustomerForm.php');
        if (strpos($content, $needle) !== false)
        {
            $found = true;
        }
    }
    elseif (file_exists(_PS_OVERRIDE_DIR_.'classes/form/CustomerForm.php'))
    {
        $content = file_get_contents(_PS_OVERRIDE_DIR_.'classes/form/CustomerForm.php');
        if (strpos($content, $needle) !== false)
        {
            $found = $override_found = true;
        }
    }

    if (!empty($found))
    {
        $results = 'KO';
        ob_start(); ?>
        <div style="padding: 20px;height: 100%;overflow: auto;">
            <img src="./lib/img/security.png" alt="" style="float: left; margin-right: 20px; margin-bottom: 20px;"/>
            <?php
            echo '<p><strong>'._l('Verify your shop vulnerability to CVE-2020-5250').'</strong></p>'; ?>
            <div style="clear: both">
                <?php echo '<ul><li>'._l('The first solution is to upgrade your PrestaShop version to 1.7.6.4 or above. Please contact your web agency.').'</li>
                    <li>'._l('The second solution is to contact your web agency to patch your shop.').'</li>';
        if (!$override_found)
        {
            echo '<li>'._l('The third solution is to let Store Commander modify your PrestaShop installation. <strong><u>DO NOT CLOSE THIS WINDOW UNTIL YOU HAVE COMPLETED THE FULL PROCEDURE</u></strong>.').'<ol>
                            <li><button class="btn_red" onClick="SEC_FRONT_CUSTOMER_FORM_BREACH_fix_file(this)">'._l('Click here to modify your PrestaShop files').'<img style="display:none;" class="loader_img" src="../SC/lib/img/ajax-loader16.gif"/><img style="display:none;" class="valid_img" src="../SC/lib/img/tick.png"/></button></li>
                            <li>'._l('Once replaced, test 2 pages on the customer account: address modification and personnal information.').'<br/>
                            '._l('If these pages work properly after the patch, your are safe.').'<br/>
                            '._l('If there is a problem, please %s and contact your web agency as soon as possible.', null, array('<button class="btn_red" onClick="SEC_FRONT_CUSTOMER_FORM_BREACH_recover_file(this)">'._l('click here to restore original files').'<img style="display:none;" class="valid_img" src="../SC/lib/img/ajax-loader16.gif"/><img style="display:none;" class="loader_img" src="../SC/lib/img/tick.png"/></button>')).'</li>
                        </ol></li>';
        }
        echo '</ul>'; ?>
            </div>
            <div style="clear: both">
                <?php
                echo _l('Technical information about this security issue:');
        echo '<ul>
                    <li><a href="https://github.com/PrestaShop/PrestaShop/security/advisories/GHSA-mhfc-6rhg-fxp3" target="_blank">'._l('Link to the official description').'</a></li>
                    <li><a href="https://github.com/PrestaShop/PrestaShop/commit/a4a609b5064661f0b47ab5bc538e1a9cd3dd1069" target="_blank">'._l('Link to technical information to patch your shop').'</a></li>
                    <li><a href="https://cve.mitre.org/cgi-bin/cvekey.cgi?keyword=CVE-2020-5250" target="_blank">'._l('Link to CVE details').'</a></li>
                </ul>'; ?>
            </div>
            <div id="SEC_FRONT_CUSTOMER_FORM_BREACH_ERROR"
                 style="display:none;margin-top: 30px; background: #D7BC3F; border: 1px solid #D7BC3F; padding: 10px;word-break: break-word;">
            </div>
        </div>
        <script>
            function SEC_FRONT_CUSTOMER_FORM_BREACH_fix_file(btn) {
                $(btn).find('.loader_img').show();
                $.post("index.php?ajax=1&act=all_win-fixmyprestashop_actions&check=SEC_FRONT_CUSTOMER_FORM_BREACH",
                    {
                        "action": "fix_files"
                    }, function (error) {
                        $(btn).find('.loader_img').hide();
                        if (error) {
                            $('#SEC_FRONT_CUSTOMER_FORM_BREACH_ERROR').show().html(error);
                        } else {
                            $(btn).find('.valid_img').show();
                            let idxResults=dhxlSCExtCheck.gridChecks.getColIndexById('results');
                            dhxlSCExtCheck.gridChecks.cells('SEC_FRONT_CUSTOMER_FORM_BREACH',idxResults).setBgColor('green');
                            dhxlSCExtCheck.gridChecks.selectRowById('SEC_FRONT_CUSTOMER_FORM_BREACH');
                        }
                    });
            }
            function SEC_FRONT_CUSTOMER_FORM_BREACH_recover_file(btn) {
                $(btn).find('.loader_img').show();
                $.post("index.php?ajax=1&act=all_win-fixmyprestashop_actions&check=SEC_FRONT_CUSTOMER_FORM_BREACH",
                    {
                        "action": "recover_files"
                    }, function (error) {
                        $(btn).find('.loader_img').hide();
                        if (error) {
                            $('#SEC_FRONT_CUSTOMER_FORM_BREACH_ERROR').show().html(error);
                        } else {
                            $(btn).find('.valid_img').show();
                            dhxlSCExtCheck.tabbar.tabs("table_SEC_FRONT_CUSTOMER_FORM_BREACH").close();
                            dhxlSCExtCheck.gridChecks.selectRowById('SEC_FRONT_CUSTOMER_FORM_BREACH');
                            doCheck(false);
                        }
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
}
elseif (!empty($post_action) && $post_action == 'fix_files')
{
    $version_folder = 0;
    if (version_compare(_PS_VERSION_, '1.7.0.0', '>=')
        && version_compare(_PS_VERSION_, '1.7.0.6', '<='))
    {
        $version_folder = 1706;
    }
    elseif (version_compare(_PS_VERSION_, '1.7.0.6', '>')
        && version_compare(_PS_VERSION_, '1.7.1.2', '<='))
    {
        $version_folder = 1712;
    }
    elseif (version_compare(_PS_VERSION_, '1.7.1.2', '>')
        && version_compare(_PS_VERSION_, '1.7.2.5', '<='))
    {
        $version_folder = 1725;
    }
    elseif (version_compare(_PS_VERSION_, '1.7.2.5', '>')
        && version_compare(_PS_VERSION_, '1.7.3.0', '<='))
    {
        $version_folder = 1730;
    }
    elseif (version_compare(_PS_VERSION_, '1.7.3.0', '>')
        && version_compare(_PS_VERSION_, '1.7.3.4', '<='))
    {
        $version_folder = 1734;
    }
    elseif (version_compare(_PS_VERSION_, '1.7.3.4', '>')
        && version_compare(_PS_VERSION_, '1.7.4.4', '<='))
    {
        $version_folder = 1744;
    }
    elseif (version_compare(_PS_VERSION_, '1.7.4.4', '>')
        && version_compare(_PS_VERSION_, '1.7.5.2', '<='))
    {
        $version_folder = 1752;
    }
    elseif (version_compare(_PS_VERSION_, '1.7.5.2', '>')
        && version_compare(_PS_VERSION_, '1.7.6.3', '<='))
    {
        $version_folder = 1763;
    }

    if ($version_folder > 0)
    {
        $path_destination = _PS_CLASS_DIR_.'form/';
        $path_backup_files = SC_DIR.'lib/all/win-fixmyprestashop/action_files/SEC_FRONT_CUSTOMER_FORM_BREACH/_backup/';
        $path_file_source = array(
            'CustomerAddressFormatter.php' => SC_DIR.'lib/all/win-fixmyprestashop/action_files/SEC_FRONT_CUSTOMER_FORM_BREACH/'.$version_folder.'/',
            'CustomerAddressForm.php' => SC_DIR.'lib/all/win-fixmyprestashop/action_files/SEC_FRONT_CUSTOMER_FORM_BREACH/'.$version_folder.'/',
            'CustomerFormatter.php' => SC_DIR.'lib/all/win-fixmyprestashop/action_files/SEC_FRONT_CUSTOMER_FORM_BREACH/'.$version_folder.'/',
            'CustomerForm.php' => SC_DIR.'lib/all/win-fixmyprestashop/action_files/SEC_FRONT_CUSTOMER_FORM_BREACH/'.$version_folder.'/',
        );
        $error = array();
        ##backup
        foreach ($path_file_source as $file_name => $source_override_path)
        {
            if (!copy($path_destination.$file_name, $path_backup_files.$file_name))
            {
                $error[] = '<p>'._l('Unable to backup the current file <strong>%s</strong> in <strong>%s</strong>.', null, array($file_name, $path_backup_files)).'</p>';
            }
            else
            {
                if (!copy($source_override_path.$file_name, $path_destination.$file_name))
                {
                    $error[] = '<p>'._l('Unable to copy the patched file <strong>%s</strong> to folder <strong>%s</strong>.', null, array($file_name, $path_destination)).'</p>';
                }
            }
        }
        if (!empty($error))
        {
            echo implode('<br/>', $error);
        }
    }
}
elseif (!empty($post_action) && $post_action == 'recover_files')
{
    $path_destination = _PS_CLASS_DIR_.'form/';
    $path_backup = SC_DIR.'lib/all/win-fixmyprestashop/action_files/SEC_FRONT_CUSTOMER_FORM_BREACH/_backup/';
    $path_backup_files = array(
        'CustomerAddressFormatter.php',
        'CustomerAddressForm.php',
        'CustomerFormatter.php',
        'CustomerForm.php',
    );
    foreach ($path_backup_files as $file_name)
    {
        if (!copy($path_backup.$file_name, $path_destination.$file_name))
        {
            $error[] = '<p>'._l('Unable to recover backup <strong>%s</strong> to the current file <strong>%s</strong>.', null, array($file_name, $path_destination)).'</p>';
        }
    }
}
