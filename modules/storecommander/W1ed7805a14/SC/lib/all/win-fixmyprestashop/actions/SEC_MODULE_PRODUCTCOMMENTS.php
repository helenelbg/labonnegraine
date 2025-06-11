<?php
$post_action = Tools::getValue('action');
$current_control_filename = basename(__FILE__, '.php');
$cell_id = 'table_'.$current_control_filename;

if (!empty($post_action) && $post_action == 'do_check')
{
    $content = '';
    $results = 'OK';
    $moduleName = 'productcomments';
    $version = Db::getInstance()->getValue('SELECT version
                                                FROM `'._DB_PREFIX_.'module`
                                                WHERE `name` ="'.$moduleName.'"');
    if (version_compare($version, '5.0.1', '<='))
    {
        $results = 'KO';
        ob_start(); ?>
        <div style="padding: 20px;height: 100%;overflow: auto;">
            <img src="./lib/img/security.png" alt="" style="float: left; margin-right: 20px; margin-bottom: 20px;"/>
            <?php
            echo '<p><strong>'._l('This version (%s) of module %s contains a critical security breach', null, array($version, _l($moduleName))).'</strong></p>'; ?>
            <div style="clear: both">
                <?php echo '<ul><strong><li>'._l('Please upgrade the module to the latest version.').'</li></strong>
                    <li><a href="https://github.com/PrestaShop/productcomments" target="_blank">https://github.com/PrestaShop/productcomments</a></li><br>
                    <li>'._l('If you don\'t understand the point, please contact your web agency.').'</li>';
        echo '</ul>'; ?>
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
