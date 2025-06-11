<?php
$post_action = Tools::getValue('action');
$action_name = 'SEC_FIL_GIT_FILES';
$tab_title = _l('Security Files');

if (!empty($post_action) && $post_action == 'do_check')
{
    $git_folder_in_classes = _PS_ROOT_DIR_.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'.git';
    $git_folder_in_override = _PS_ROOT_DIR_.DIRECTORY_SEPARATOR.'override'.DIRECTORY_SEPARATOR.'.git';

    $content = '';
    $results = 'OK';
    $found = array();

    if (is_dir($git_folder_in_classes))
    {
        $found[] = $git_folder_in_classes;
    }
    if (is_dir($git_folder_in_override))
    {
        $found[] = $git_folder_in_override;
    }

    if (!empty($found))
    {
        $results = 'KO';
        ob_start(); ?>
        <div style="padding: 20px;height: 100%;overflow: auto;">
            <img src="./lib/img/security.png" alt="" style="float: left; margin-right: 20px; margin-bottom: 20px;" />
            <?php
            echo '<p><strong>'._l('Files listed below are still located on root of your server. These files can generate security issues and it is recommended to delete them.').'</strong></p>';
        foreach ($found as $v)
        {
            echo '<p>-&nbsp;'.$v.'</p>';
        } ?>
            <div style="clear: both"></div>
        <?php
        $content = ob_get_clean();
    }

    echo json_encode(array(
        'results' => $results,
        'contentType' => 'content',
        'content' => $content,
        'title' => $tab_title,
    ));
}
