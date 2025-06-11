<?php
$post_action = Tools::getValue('action');
$current_control_filename = basename(__FILE__, '.php');
$cell_id = 'table_'.$current_control_filename;

if (!empty($post_action) && $post_action == 'do_check')
{
    $updateAvailable = checkSCVersion(false, false);
    $content = '';
    $results = 'OK';
    if (!empty($updateAvailable))
    {
        $results = 'KO';
        ob_start(); ?>
        <div style="padding: 20px;height: 100%;overflow: auto;">
            <img src="./lib/img/information_big.png" alt="" style="float: left; margin-right: 20px; margin-bottom: 20px;"/>
            <p>
                <strong><?php echo _l('Your Store Commander needs to be updated.'); ?></strong>
            </p>
            <div style="clear: both"></div>
            <div style="margin-top: 30px; text-align: center;">
                <button class="btn_red" onClick="<?php echo $current_control_filename; ?>_do()"><?php echo _l('Update Store Commander'); ?></button>
            </div>
        </div>
        <script>
            function <?php echo $current_control_filename; ?>_do(){
                dhxMenu.callEvent('onClick',['version2']);
            }
        </script>
        <?php
        $content = ob_get_clean();
    }

    echo json_encode(array(
        'results' => $results,
        'contentType' => 'content',
        'content' => $content,
        'title' => _l('Sc version'),
    ));
}
