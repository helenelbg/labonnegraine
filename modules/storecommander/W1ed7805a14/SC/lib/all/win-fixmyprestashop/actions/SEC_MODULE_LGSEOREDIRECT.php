<?php
$post_action = Tools::getValue('action');
$current_control_filename = basename(__FILE__, '.php');
$cell_id = 'table_'.$current_control_filename;

if (!empty($post_action) && $post_action == 'do_check')
{
    $content = '';
    $results = 'OK';
    $moduleName = 'lgseoredirect';
    $version = Db::getInstance()->getValue('SELECT version
                                                FROM `'._DB_PREFIX_.'module`
                                                WHERE `name` ="'.$moduleName.'"');
    if (version_compare($version, '1.2.5', '<='))
    {
        $results = 'KO';
        ob_start(); ?>
        <div style="padding: 20px;height: 100%;overflow: auto;">
            <img src="./lib/img/security.png" alt="" style="float: left; margin-right: 20px; margin-bottom: 20px;"/>
            <?php
            echo '<p><strong>'._l('This version (%s) of module %s contains a critical security breach', null, array($version, $moduleName)).'</strong></p>'; ?>
            <div style="clear: both">
                <?php echo '<ul><li>'._l('Please upgrade the module to the latest version.').'</li>
                    <li>'._l('Check if override<br><b>%s</b><br>is still valid with the latest version of module.', null, array('modules/lgseoredirect/override/classes/controller/FrontController.php')).'<br><br>
                    '._l('If you don\'t understand the point, please contact your web agency.').'</li>';
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
