<?php
if (!defined('STORE_COMMANDER')) { exit; }

$post_action = Tools::getValue('action');

$path = _PS_ROOT_DIR_.'/';

if (!empty($post_action) && $post_action == 'do_check')
{
    $content = '';
    $results = 'OK';

    if (!file_exists($path.'robots.txt'))
    {
        $results = 'KO';
        ob_start(); ?>
        <div style="padding: 20px;height: 100%;overflow: auto;">
            <img src="./lib/img/information_big.png" alt="" style="float: left; margin-right: 20px; margin-bottom: 20px;" />
            <p><?php echo _l('If this file is located on root of your server, it can improve your SEO.'); ?></p>
            <div style="clear: both"></div>
            <div style="margin-top: 30px; background: #D7BC3F; border: 1px solid #D7BC3F; padding: 10px;">
                <?php echo _l('You can create or regenerate this file from your PrestaShop backoffice: SEO & URLs'); ?>
            </div>
        </div>
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
