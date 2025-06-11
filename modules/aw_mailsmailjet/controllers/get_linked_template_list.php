<?php
    if($_POST['token'] == '5g49e84g68r4gegujherge564er8gujhghgiu' && !empty($_POST['lang_id']))
    {
        include '../../../config/config.inc.php';
        
        $sql = 'SELECT * FROM aw_mails_mailjet WHERE aw_mails_mailjet_lang_id = "'.$_POST['lang_id'].'"';
        $linked_mails_template_list = Db::getInstance($sql)->executeS($sql);

        echo json_encode($linked_mails_template_list);
    }
?>