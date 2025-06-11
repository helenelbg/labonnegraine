<?php
$post_action = Tools::getValue('action');
$current_control_filename = basename(__FILE__, '.php');
$cell_id = 'table_'.$current_control_filename;
$term_name = _l('Shop cleaning and optimization');

if (!empty($post_action) && $post_action == 'do_check')
{
    $count = Db::getInstance()->getValue('SELECT COUNT(*) nb_log FROM '._DB_PREFIX_.'log');
    $content = '';
    $results = 'OK';
    if (!empty($count) && $count > 10000)
    {
        $results = 'KO';
        ob_start(); ?>
        <div style="padding: 20px;height: 100%;overflow: auto;">
            <img src="./lib/img/information_big.png" alt="" style="float: left; margin-right: 20px; margin-bottom: 20px;"/>
            <p>
                <strong><?php echo _l('Your shop contains %s lines in %s table.', false, array($count, _DB_PREFIX_.'log')); ?></strong>
            </p>
            <div style="clear: both"></div>
            <div style="margin-top: 30px; text-align: center;">
                <button class="btn_red" onClick="openTerminatorAndFilter('dellog');"><?php echo _l('Delete data by using %s', false, array($term_name)); ?></button>
            </div>
        </div>
        <?php
        $content = ob_get_clean();
    }

    echo json_encode(array(
        'results' => $results,
        'contentType' => 'content',
        'content' => $content,
        'title' => _l('ps_log'),
    ));
}
