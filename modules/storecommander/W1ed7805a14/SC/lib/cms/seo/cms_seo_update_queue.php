<?php

@error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
@ini_set('display_errors', 'ON');

$id_lang = Tools::getValue('id_lang', '0');
$action = Tools::getValue('action', '');

$return = 'ERROR: Try again later';

// FUNCTIONS
$updated_cms = array();

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
                $gr_id = $row->row;
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

                if (!empty($action) && $action == 'update' && !empty($gr_id))
                {
                    if (version_compare(_PS_VERSION_, '1.6.0.0', '>=') && SCMS)
                    {
                        list($id_cms, $id_lang, $id_shop) = explode('_', $gr_id);
                    }
                    else
                    {
                        list($id_cms, $id_lang) = explode('_', $gr_id);
                    }
                    $updated_cms[$id_cms] = $id_cms;

                    $fields = array('link_rewrite', 'meta_title', 'meta_description', 'meta_keywords');
                    if (version_compare(_PS_VERSION_, '1.7.5.0', '>='))
                    {
                        $fields[] = 'head_seo_title';
                    }

                    // LANG
                    $todo = array();
                    foreach ($fields as $field)
                    {
                        if (isset($_POST[$field]))
                        {
                            $val = Tools::getValue($field);
                            $todo[] = '`'.bqSQL($field)."`='".psql(html_entity_decode($val))."'";
                        }
                    }

                    if (count($todo))
                    {
                        $sql = 'UPDATE '._DB_PREFIX_.'cms_lang SET '.join(' , ', $todo)." 
                        WHERE id_cms='".(int) $id_cms."' AND id_lang='".(int) $id_lang."'";
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
