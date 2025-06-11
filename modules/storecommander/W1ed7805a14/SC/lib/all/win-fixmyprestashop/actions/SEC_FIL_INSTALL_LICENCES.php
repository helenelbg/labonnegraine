<?php
$post_action = Tools::getValue('action');

$files = array(
    _PS_ROOT_DIR_.'/INSTALL.txt',
    _PS_ROOT_DIR_.'/LICENSES',
    _PS_ROOT_DIR_.'/Install_PrestaShop.html',
    _PS_OVERRIDE_DIR_.'readme_override.txt',
);

if (!empty($post_action) && $post_action == 'do_check')
{
    $content = '';
    $results = 'OK';

    $found = false;
    foreach ($files as $filepath)
    {
        if (file_exists($filepath))
        {
            $found = true;
            break;
        }
    }

    if ($found)
    {
        $results = 'KO';
        ob_start(); ?>
        <div style="padding: 20px;height: 100%;overflow: auto;">
            <img src="./lib/img/security.png" alt="" style="float: left; margin-right: 20px; margin-bottom: 20px;" />
            <?php
            echo '<p><strong>'._l('Files listed below are still located on root of your server. These files can generate security issues and it is recommended to delete them.').'</strong></p>';
        foreach ($files as $filepath)
        {
            if (file_exists($filepath))
            {
                echo '<p>- <span style="color: #777777;"></span>'.$filepath.'</p>';
            }
        } ?>
            <div style="clear: both"></div>
            <div style="margin-top: 30px; text-align: center;">
                <button class="btn_red" onClick="SEC_FIL_INSTALL_LICENCES_delete()"><?php echo _l('Delete these files'); ?></button>
            </div>
            <div style="margin-top: 30px; background: #D7BC3F; border: 1px solid #D7BC3F; padding: 10px;">
                <?php echo _l('We might not have enough permissions on your FTP to fix this, and you therefore need to do this manually on your FTP.'); ?>
            </div>
        </div>
        <script>
           function SEC_FIL_INSTALL_LICENCES_delete()
           {
                $.post("index.php?ajax=1&act=all_win-fixmyprestashop_actions&check=SEC_FIL_INSTALL_LICENCES&id_lang="+SC_ID_LANG, { "action": "delete_files"}, function(data){
                    dhxlSCExtCheck.tabbar.tabs("table_SEC_FIL_INSTALL_LICENCES").close();

                    dhxlSCExtCheck.gridChecks.selectRowById('SEC_FIL_INSTALL_LICENCES');
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
}
elseif (!empty($post_action) && $post_action == 'delete_files')
{
    foreach ($files as $filepath)
    {
        if (file_exists($filepath))
        {
            @unlink($filepath);
        }
    }
}
