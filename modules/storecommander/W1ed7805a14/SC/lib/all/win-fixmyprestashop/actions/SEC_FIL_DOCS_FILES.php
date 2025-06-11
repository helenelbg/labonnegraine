<?php
$post_action = Tools::getValue('action');

$path = _PS_ROOT_DIR_.'/docs/';

if (!empty($post_action) && $post_action == 'do_check')
{
    $content = '';
    $results = 'OK';

    $found = array();
    if (file_exists($path.'licences') && is_dir($path.'licences'))
    {
        $found[$path.'licences'] = 1;
    }
    if (file_exists($path.'CHANGELOG.txt'))
    {
        $found[$path.'CHANGELOG.txt'] = 1;
    }

    $files = scandir($path, 1);
    foreach ($files as $file)
    {
        if (substr($file, 0, 7) == 'readme_')
        {
            $found[$path.$file] = 1;
        }
    }

    if (!empty($found))
    {
        $results = 'KO';
        ob_start(); ?>
        <div style="padding: 20px;height: 100%;overflow: auto;">
            <img src="./lib/img/security.png" alt="" style="float: left; margin-right: 20px; margin-bottom: 20px;" />
            <?php
            echo '<p><strong>'._l('Files listed below are still located on root of your server. These files can generate security issues and it is recommended to delete them.').'</strong></p>';
        foreach ($found as $file => $v)
        {
            $name = str_replace($path, '', $file);
            $p = str_replace($name, '', $path);

            if ($file == $path.'licences')
            {
                $name .= ' '._l('(Dossier)');
            }
            echo "<p>- <span style='color: #777777;'>".$p.'</span>'.$name.'</p>';
        } ?>
            <div style="clear: both"></div>
            <div style="margin-top: 30px; text-align: center;">
                <button class="btn_red" onClick="SEC_FIL_DOCS_FILES_delete()"><?php echo _l('Delete these files'); ?></button>
            </div>
            <div style="margin-top: 30px; background: #D7BC3F; border: 1px solid #D7BC3F; padding: 10px;">
                <?php echo _l('We might not have enough permissions on your FTP to fix this, and you therefore need to do this manually on your FTP.'); ?>
            </div>
        </div>
        <script>
            function SEC_FIL_DOCS_FILES_delete()
            {
                $.post("index.php?ajax=1&act=all_win-fixmyprestashop_actions&check=SEC_FIL_DOCS_FILES&id_lang="+SC_ID_LANG, { "action": "delete_files"}, function(data){
                    dhxlSCExtCheck.tabbar.tabs("table_SEC_FIL_DOCS_FILES").close();

                    dhxlSCExtCheck.gridChecks.selectRowById('SEC_FIL_DOCS_FILES');
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
    $found = array();
    if (file_exists($path.'licences') && is_dir($path.'licences'))
    {
        $found[$path.'licences'] = 1;
    }
    if (file_exists($path.'CHANGELOG.txt'))
    {
        $found[$path.'CHANGELOG.txt'] = 1;
    }

    $files = scandir($path, 1);
    foreach ($files as $file)
    {
        if (substr($file, 0, 7) == 'readme_')
        {
            $found[$path.$file] = 1;
        }
    }

    if (!empty($found))
    {
        foreach ($found as $file => $v)
        {
            if (!empty($file))
            {
                if (is_dir($file))
                {
                    $files = scandir($file, 1);
                    foreach ($files as $file2)
                    {
                        @unlink($path.'licences/'.$file2);
                    }
                    @rmdir($file);
                }
                else
                {
                    @unlink($file);
                }
            }
        }
    }
}
