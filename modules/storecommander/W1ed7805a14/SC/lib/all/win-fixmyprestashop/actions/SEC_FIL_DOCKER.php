<?php
$post_action = Tools::getValue('action');

$path = _PS_ROOT_DIR_.'/';
$current_control_filename = basename(__FILE__, '.php');
$cell_id = 'table_'.$current_control_filename;
$filepath = $path.'.docker';

if (!empty($post_action) && $post_action == 'do_check')
{
    $content = '';
    $results = 'OK';

    if (file_exists($filepath))
    {
        $results = 'KO';
        ob_start(); ?>
        <div style="padding: 20px;height: 100%;overflow: auto;">
            <img src="./lib/img/security.png" alt="" style="float: left; margin-right: 20px; margin-bottom: 20px;" />
            <p><strong><?php echo _l('Files listed below are still located on root of your server. These files can generate security issues and it is recommended to delete them.'); ?></strong></p>
            <p>- <span style='color: #777777;'><?php echo $path; ?></span>.docker</p>
            <div style="clear: both"></div>
            <div style="margin-top: 30px; text-align: center;">
                <button class="btn_red" onClick="<?php echo $current_control_filename; ?>_delete()"><?php echo _l('Delete these files'); ?></button>
            </div>
            <div style="margin-top: 30px; background: #D7BC3F; border: 1px solid #D7BC3F; padding: 10px;">
                <?php echo _l('We might not have enough permissions on your FTP to fix this, and you therefore need to do this manually on your FTP.'); ?>
            </div>
        </div>
        <script>
           function <?php echo $current_control_filename; ?>_delete()
           {
                $.post("index.php?ajax=1&act=all_win-fixmyprestashop_actions&check=<?php echo $current_control_filename; ?>", {action: "delete"}, function(){
                    dhxlSCExtCheck.tabbar.tabs("<?php echo $cell_id; ?>").close();
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
        'title' => _l('.docker folder'),
    ));
}
elseif (!empty($post_action) && $post_action == 'delete')
{
    if (file_exists($filepath))
    {
        unlink($filepath);
    }
}
