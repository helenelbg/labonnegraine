<?php
$post_action = Tools::getValue('action');
$action_name = 'SEC_CUSTOMER_DEFAULT_INSTALL';

if (!empty($post_action) && $post_action == 'do_check')
{
    $content = '';
    $results = 'OK';
    $found = Db::getInstance()->getValue('SELECT id_customer FROM '._DB_PREFIX_.'customer WHERE email = "pub@prestashop.com"');

    if ($found)
    {
        $results = 'KO';
        ob_start(); ?>
        <div style="padding: 20px;height: 100%;overflow: auto;">
            <img src="./lib/img/security.png" alt="" style="float: left; margin-right: 20px; margin-bottom: 20px;"/>
            <?php echo '<p><strong>'._l('The %s account is still present. Some data from your shop could be reached by using this unused account', false, array('pub@prestashop.com')).'</strong></p>'; ?>
            <div style="clear: both"></div>
            <div style="margin-top: 30px; text-align: center;">
                <button class="btn_red"
                        onClick="<?php echo $action_name; ?>_delete()"><?php echo _l('Delete this customer account'); ?></button>
            </div>
        </div>
        <script>
            function <?php echo $action_name; ?>_delete() {
                $('.loader_img').show();
                $.post("index.php?ajax=1&act=all_win-fixmyprestashop_actions&check=<?php echo $action_name; ?>",
                    {
                        action: 'remove'
                    }, function (data) {
                        dhxlSCExtCheck.tabbar.tabs("table_<?php echo $action_name; ?>").close();
                        dhxlSCExtCheck.gridChecks.selectRowById('<?php echo $action_name; ?>');
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
        'title' => _l('Default cus. found'),
    ));
}
elseif (!empty($post_action) && $post_action == 'remove')
{
    $id_customer = Db::getInstance()->getValue('SELECT id_customer FROM '._DB_PREFIX_.'customer WHERE email = "pub@prestashop.com"');
    if($id_customer)
    {
        $toRemove = new Customer((int)$id_customer);
        $toRemove->delete();
    }
}
