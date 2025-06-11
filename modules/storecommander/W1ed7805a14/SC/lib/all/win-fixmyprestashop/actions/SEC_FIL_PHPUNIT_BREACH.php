<?php
$post_action = Tools::getValue('action');
$coreDir = (version_compare(_PS_VERSION_, '1.6.0.5', '>=') ? _PS_CORE_DIR_ : _PS_ROOT_DIR_);

if (!empty($post_action) && $post_action == 'do_check')
{
    $content = '';
    $results = 'OK';

    $found = array();
    if (function_exists('exec'))
    {
        $command = 'find '.$coreDir.' -type d -name "phpunit"';
        exec($command, $out);
        if (!empty($out))
        {
            foreach ($out as $k => $r)
            {
                $exp = explode('vendor/phpunit', $r);
                if (empty($exp[1]))
                {
                    $found[] = $r;
                }
            }
        }
    }
    else
    {
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($coreDir));
        foreach ($files as $file)
        {
            if ($file->isDir() && strpos($file->getPathname(), 'vendor/phpunit') !== false)
            {
                $tmp_name = trim($file->getPathname(), './');
                $exp = explode('vendor/phpunit', $tmp_name);
                if (empty($exp[1]))
                {
                    $found[] = $exp[0].'vendor/phpunit';
                }
            }
        }
    }

    $found = array_unique($found);

    if (!empty($found))
    {
        $results = 'KO';
        ob_start(); ?>
        <div style="padding: 20px;height: 100%;overflow: auto;">
            <img src="./lib/img/security.png" alt="" style="float: left; margin-right: 20px; margin-bottom: 20px;"/>
            <?php
            echo '<p><strong>'._l('Folders listed below are still located on your server. These files can generate security issues and we recommend to delete them.').'</strong></p>';
        foreach ($found as $file)
        {
            echo "<p>- <span style='color: #777777;'>".$file.'</span></p>';
        } ?>
            <div style="clear: both"></div>
            <div style="margin-top: 30px; text-align: center;">
                <button class="btn_red"
                        onClick="SEC_FIL_PHPUNIT_BREACH_delete(this)"><?php echo _l('Delete these files'); ?><img
                            style="display:none;" class="loader_img" src="../SC/lib/img/ajax-loader16.gif"/></button>
            </div>
            <div style="margin-top: 30px; background: #D7BC3F; border: 1px solid #D7BC3F; padding: 10px;">
                <?php echo _l('We might not have enough permissions on your FTP to fix this, and you therefore need to do this manually on your FTP.'); ?>
            </div>
        </div>
        <script>
            function SEC_FIL_PHPUNIT_BREACH_delete(btn) {
                $('.loader_img').show();
                $.post("index.php?ajax=1&act=all_win-fixmyprestashop_actions&check=SEC_FIL_PHPUNIT_BREACH",
                    {
                        "action": "delete_files"
                    }, function (data) {
                        dhxlSCExtCheck.tabbar.tabs("table_SEC_FIL_PHPUNIT_BREACH").close();
                        dhxlSCExtCheck.gridChecks.selectRowById('SEC_FIL_PHPUNIT_BREACH');
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
    if (function_exists('exec'))
    {
        $command = 'find '.$coreDir.' -type d -name "phpunit"';
        exec($command, $out);
        if (!empty($out))
        {
            foreach ($out as $k => $r)
            {
                $exp = explode('vendor/phpunit', $r);
                if (empty($exp[1]))
                {
                    $found[] = $r;
                }
            }
        }
        if (!empty($found))
        {
            foreach ($found as $folder)
            {
                $command = 'rm -r '.$folder;
                exec($command);
            }
        }
    }
    else
    {
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($coreDir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($files as $file)
        {
            if (strpos($file->getPathname(), 'vendor/phpunit') !== false)
            {
                if ($file->isDir())
                {
                    @rmdir($file->getRealPath());
                }
                else
                {
                    $fname = $file->getRealPath();
                    @unlink($fname);
                }
            }
        }
    }
}
