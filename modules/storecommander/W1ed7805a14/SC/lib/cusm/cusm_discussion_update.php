<?php

    $id_objet = (int) Tools::getValue('gr_id', 0);

    if (isset($_POST['!nativeeditor_status']) && trim($_POST['!nativeeditor_status']) == 'updated')
    {
        $fields = array('email', 'status', 'id_employee', 'id_contact');
        $todo = array();
        $todo_message = array();
        foreach ($fields as $field)
        {
            if (isset($_POST[$field]) && $field != 'id_employee')
            {
                $todo[] = $field."='".psql(Tools::getValue($field))."'";
            }
            elseif (isset($_POST[$field]) && $field == 'id_employee')
            {
                $todo_message[] = $field."='".psql(Tools::getValue($field))."'";
            }
        }
        if (count($todo))
        {
            $sql = 'UPDATE '._DB_PREFIX_.'customer_thread SET '.join(' , ', $todo).", date_upd = '".date('Y-m-d H:i:s')."' WHERE id_customer_thread=".(int) $id_objet;
            Db::getInstance()->Execute($sql);
        }
        if (count($todo_message))
        {
            $message_id = 0;
            $sql = 'SELECT cm2.`id_customer_message`
                FROM `'._DB_PREFIX_.'customer_message` cm2
                WHERE cm2.id_employee > 0
                    AND cm2.`id_customer_thread` = '.(int) $id_objet.'
                ORDER BY cm2.`date_add` DESC LIMIT 1';
            $tmp = Db::getInstance()->ExecuteS($sql);
            if (!empty($tmp[0]['id_customer_message']))
            {
                $message_id = $tmp[0]['id_customer_message'];
            }
            if (!empty($message_id))
            {
                $sql = 'UPDATE '._DB_PREFIX_.'customer_message SET '.join(' , ', $todo).' WHERE id_customer_message='.(int) $message_id;
                Db::getInstance()->Execute($sql);
                $sql = 'UPDATE '._DB_PREFIX_."customer_thread SET date_upd = '".date('Y-m-d H:i:s')."' WHERE id_customer_thread=".(int) $id_objet;
                Db::getInstance()->Execute($sql);
            }
        }
        $newId = Tools::getValue('gr_id');
        $action = 'update';
    }
    elseif (isset($_POST['!nativeeditor_status']) && trim($_POST['!nativeeditor_status']) == 'deleted')
    {
        $discussion = new CustomerThread((int) ($id_objet));
        $discussion->delete();
        $newId = Tools::getValue('gr_id');
        $action = 'delete';
    }

    if (stristr($_SERVER['HTTP_ACCEPT'], 'application/xhtml+xml'))
    {
        header('Content-type: application/xhtml+xml');
    }
    else
    {
        header('Content-type: text/xml');
    }
    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
    echo '<data>';
    echo "<action type='".$action."' sid='".Tools::getValue('gr_id')."' tid='".$newId."'/>";
    echo '</data>';
