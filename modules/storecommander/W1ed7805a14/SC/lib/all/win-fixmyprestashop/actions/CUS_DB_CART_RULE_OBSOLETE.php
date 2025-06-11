<?php
$post_action = Tools::getValue('action');
$current_control_filename = basename(__FILE__, '.php');
$cell_id = 'table_'.$current_control_filename;
$term_name = _l('Shop cleaning and optimization');

if (!empty($post_action) && $post_action == 'do_check')
{
    $count = Db::getInstance()->getValue('SELECT COUNT(*) as nb
                                                FROM `'._DB_PREFIX_.'cart_rule`
                                                WHERE date_to < (NOW() - INTERVAL 1 YEAR) AND id_cart_rule NOT IN(
                                                SELECT DISTINCT `id_cart_rule`
                                                FROM `'._DB_PREFIX_.'cart_cart_rule`)');
    $content = '';
    $results = 'OK';
    if (!empty($count))
    {
        $results = 'KO';
        ob_start(); ?>
        <div style="padding: 20px;height: 100%;overflow: auto;">
            <img src="./lib/img/information_big.png" alt="" style="float: left; margin-right: 20px; margin-bottom: 20px;"/>
            <p>
                <strong><?php echo _l('Your shop contains %s lines of obsolete cart rules.', false, array($count)); ?></strong>
            </p>
            <p><?php echo _l('You can set the time value directly in %s', false, array($term_name)); ?></p>
            <div style="clear: both"></div>
            <div style="margin-top: 30px; text-align: center;">
                <button class="btn_red" onClick="openTerminatorAndFilter('deldiscountdate');"><?php echo _l('Delete data by using %s', false, array($term_name)); ?></button>
            </div>
        </div>
        <?php
        $content = ob_get_clean();
    }

    echo json_encode(array(
        'results' => $results,
        'contentType' => 'content',
        'content' => $content,
        'title' => _l('Obs. cart rules'),
    ));
}
