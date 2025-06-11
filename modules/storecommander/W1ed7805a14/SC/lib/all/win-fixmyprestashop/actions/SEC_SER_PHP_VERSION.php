<?php
$post_action = Tools::getValue('action');

if (!empty($post_action) && $post_action == 'do_check')
{
    $content = '';
    $results = 'OK';

    if (version_compare(phpversion(), '5.6', '<'))
    {
        $results = 'KO';
        ob_start(); ?>
        <div style="padding: 20px;">
            <img src="./lib/img/security.png" alt="" style="float: left; margin-right: 20px; margin-bottom: 20px;" />
            <p><?php echo _l('PHP version on your server is inferior to 5.6 and we strongly advise against it because this version is not supported anymore.'); ?></p>
            <div style="clear: both"></div>
        </div>
        <?php
        $content = ob_get_clean();
    }

    echo json_encode(array(
        'results' => $results,
        'contentType' => 'content',
        'content' => $content,
        'title' => _l('PHP Version'),
    ));
}
