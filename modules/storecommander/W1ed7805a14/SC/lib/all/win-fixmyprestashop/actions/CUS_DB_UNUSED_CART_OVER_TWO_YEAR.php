<?php
$post_action = Tools::getValue('action');
$current_control_filename = basename(__FILE__, '.php');
$cell_id = 'table_'.$current_control_filename;
$term_name = _l('Shop cleaning and optimization');

if (!empty($post_action) && $post_action == 'do_check')
{
    $count = Db::getInstance()->getValue('SELECT COUNT(*) as nb_cart
                                                FROM `'._DB_PREFIX_.'cart` 
                                                WHERE date_upd < DATE_SUB(NOW(),INTERVAL 2 YEAR)
                                                AND `id_cart` NOT IN(SELECT DISTINCT `id_cart` 
                                                                        FROM `'._DB_PREFIX_.'orders`)');
    $content = '';
    $results = 'OK';
    if (!empty($count))
    {
        $results = 'KO';
        ob_start(); ?>
        <div style="padding: 20px;height: 100%;overflow: auto;">
            <img src="./lib/img/information_big.png" alt="" style="float: left; margin-right: 20px; margin-bottom: 20px;"/>
            <p>
                <strong><?php echo _l('Your shop contains %s lines of abandoned carts.', false, array($count)); ?></strong>
            </p>
            <div style="clear: both"></div>
            <div style="margin-top: 30px; text-align: center;">
                <button class="btn_red" onClick="openTerminatorAndFilter('deloldcart');"><?php echo _l('Delete data by using %s', false, array($term_name)); ?></button>
            </div>
        </div>
        <?php
        $content = ob_get_clean();
    }

    echo json_encode(array(
        'results' => $results,
        'contentType' => 'content',
        'content' => $content,
        'title' => _l('Abandoned carts'),
    ));
}
