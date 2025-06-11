<?php

@error_reporting(E_ERROR | E_PARSE);
@ini_set('display_errors', 'ON');

$id_lang = Tools::getValue('id_lang', '0');
$action = Tools::getValue('action', '');
$languages = Language::getLanguages(true);

$return = 'ERROR: Try again later';

// FUNCTIONS
$updated_products = array();

// Récupération de toutes les modifications à effectuer
if (Tools::getValue('rows') || $action == 'insert')
{
    if ($action != 'insert')
    {

        if(_PS_MAGIC_QUOTES_GPC_)
            $_POST["rows"] = Tools::getValue('rows');
        $rows = json_decode($_POST["rows"]);
    }
    else
    {
        $rows = array();
        $rows[0] = new stdClass();
        $rows[0]->name = Tools::getValue('act', '');
        $rows[0]->action = Tools::getValue('action', '');
        $rows[0]->row = Tools::getValue('gr_id', '');
        $rows[0]->callback = Tools::getValue('callback', '');
        $rows[0]->params = $_POST;
    }

    if (is_array($rows) && count($rows) > 0)
    {
        $callbacks = '';

        // Première boucle pour remplir la table sc_queue_log
        // avec toutes ces modifications
        $log_ids = array();
        $date = date('Y-m-d H:i:s');
        foreach ($rows as $num => $row)
        {
            $id = QueueLog::add($row->name, $row->row, $row->action, (!empty($row->params) ? $row->params : array()), (!empty($row->callback) ? $row->callback : null), $date);
            $log_ids[$num] = $id;
        }

        // Deuxième boucle pour effectuer les
        // actions les une après les autres
        foreach ($rows as $num => $row)
        {
            if (!empty($log_ids[$num]))
            {
                $gr_id = (int) $row->row;
                $action = $row->action;

                if (!empty($row->callback))
                {
                    $callbacks .= $row->callback.';';
                }

                if ($action != 'insert')
                {
                    $_POST = array();
                    $_POST = (array) json_decode($row->params);
                }

                if (!empty($action) && $action == 'insert')
                {
                    $group = new Group();
                    foreach ($languages as $lang)
                    {
                        $group->name[$lang['id_lang']] = 'new';
                    }
                    $group->price_display_method = 0;
                    $group->save();
                    $newId = $group->id;
                    if (!empty($newId))
                    {
                        $callbacks = str_replace('{newid}', $newId, $callbacks);
                    }
                }
                elseif (!empty($action) && $action == 'delete' && !empty($gr_id))
                {
                    $group = new Group($gr_id);
                    $group->delete();
                }
                elseif (!empty($action) && $action == 'update' && !empty($gr_id))
                {
                    $fields_required = array('reduction', 'price_display_method', 'show_prices');
                    $fields_required_lang = array('name');
                    $todo = array();
                    foreach ($fields_required as $field)
                    {
                        if (isset($_POST[$field]))
                        {
                            $todo[] = ' '.$field.'='.(float) Tools::getValue($field);
                        }
                    }
                    if (count($todo) > 0)
                    {
                        $todo = implode(',', $todo);
                        $sql = 'UPDATE `'._DB_PREFIX_.'group` SET'.pSQL($todo).", date_upd='".date('Y-m-d H:i:s')."' WHERE id_group=".(int) Tools::getValue('id_group');
                        Db::getInstance()->Execute($sql);
                    }

                    $todo = array();
                    foreach ($fields_required_lang as $field)
                    {
                        if (isset($_POST[$field]))
                        {
                            $todo[] = ' '.$field.'="'.pSQL(Tools::getValue($field)).'"';
                        }
                    }
                    if (count($todo) > 0)
                    {
                        $todo = implode(',', $todo);
                        $sql = 'UPDATE '._DB_PREFIX_.'group_lang SET'.$todo.' WHERE id_group='.(int) Tools::getValue('id_group').' AND id_lang = '.(int) Tools::getValue('id_lang');
                        Db::getInstance()->Execute($sql);
                    }
                }

                QueueLog::delete(($log_ids[$num]));
            }
        }

        // RETURN
        $return = json_encode(array('callback' => $callbacks));
    }
}
echo $return;
