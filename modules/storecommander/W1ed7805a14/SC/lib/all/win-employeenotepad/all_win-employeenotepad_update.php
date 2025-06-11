<?php

$action = Tools::getValue('action', null);
$error = array();
$return = array();

if (!empty($action) && !empty($sc_agent->id_employee))
{
    switch ($action) {
        case 'save':
            $note = Tools::getValue('note', null);
            $res = Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'employee 
                                                    SET sc_note = "'.pSQL($note, 1).'" 
                                                    WHERE id_employee ='.(int) $sc_agent->id_employee);
            if (!$res)
            {
                $error[] = _l('Error during saving note');
            }
            break;
        case 'delete':
            $res = Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'employee 
                                                    SET sc_note = NULL 
                                                    WHERE id_employee ='.(int) $sc_agent->id_employee);
            if (!$res)
            {
                $error[] = _l('Error during erasing note');
            }
            break;
    }
}

$return = array(
    'valid' => (empty($error) ? 1 : 0),
    'error' => (empty($error) ? 0 : 1),
    'detail' => implode('<br/>', $error),
);
exit(json_encode($return));
