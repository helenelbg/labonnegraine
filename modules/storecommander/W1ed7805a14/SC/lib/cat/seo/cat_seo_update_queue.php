<?php

@error_reporting(E_ERROR | E_PARSE);
@ini_set('display_errors', 'ON');

$id_lang = Tools::getValue('id_lang', '0');
$action = Tools::getValue('action', '');

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
                    if (SCMS)
                    {
                        list($id_product, $id_lang, $id_shop) = explode('_', $gr_id);
                    }
                    else
                    {
                        list($id_product, $id_lang) = explode('_', $gr_id);
                    }
                    $updated_products[$id_product] = $id_product;

                    $list_lang_fields = 'name,link_rewrite,meta_title,meta_description,meta_keywords';

                    // LANG
                    $fields = explode(',', $list_lang_fields);
                    $todo = array();
                    $history_array = array();
                    $link_rewrite = '';
                    foreach ($fields as $field)
                    {
                        if (isset($_POST[$field]))
                        {
                            $val = Tools::getValue($field);
                            $todo[] = '`'.bqSQL($field)."`='".psql(html_entity_decode($val))."'";
                            $history_array[] = array(
                                'field' => $field,
                                'value' => psql(html_entity_decode($val)),
                            );

                            if ($field == 'name' && _s('CAT_SEO_NAME_TO_URL'))
                            {
                                $iso = Language::getIsoById($id_lang);
                                $link_rewrite = "`link_rewrite`='".link_rewrite($val, $iso)."'";
                                $history_array[] = array(
                                    'field' => 'link_rewrite',
                                    'value' => link_rewrite($val, $iso),
                                );
                            }
                        }
                    }

                    if (!empty($link_rewrite))
                    {
                        $todo[] = $link_rewrite;
                    }

                    if (count($todo))
                    {
                        ##history
                        $current_product = Db::getInstance()->getRow('SELECT '.$list_lang_fields.'
                                                                            FROM '._DB_PREFIX_.'product_lang
                                                                            WHERE id_product = '.(int) $id_product.'
                                                                            AND id_lang='.(int) $id_lang.
                                                                            (version_compare(_PS_VERSION_, '1.5.0', '>=') ? ' AND id_shop = '.(int) $id_shop : ''));
                        foreach ($history_array as $row)
                        {
                            addToHistory('cat_prop_seo', 'modification', $row['field'], $id_product, $id_lang, 'product_lang', $row['value'], $current_product[$row['field']], (version_compare(_PS_VERSION_, '1.5.0', '>=') ? (int) $id_shop : null));
                        }

                        if (SCMS)
                        {
                            $sql = 'UPDATE '._DB_PREFIX_.'product_lang SET '.join(' , ', $todo)." WHERE id_product=" .(int) $id_product . " AND id_shop=" .(int) $id_shop . " AND id_lang=" .(int) $id_lang;
                        }
                        else
                        {
                            $sql = 'UPDATE '._DB_PREFIX_.'product_lang SET '.join(' , ', $todo)." WHERE id_product=" .(int) $id_product . " AND id_lang=" .(int) $id_lang;
                        }
                        Db::getInstance()->Execute($sql);
                    }

                    //update date_upd
                    Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'product SET date_upd = NOW() WHERE id_product='.(int) $id_product);
                    if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                    {
                        Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'product_shop SET date_upd = NOW() WHERE id_product='.(int) $id_product.' AND id_shop = '.(int) $id_shop);
                    }
                }

                QueueLog::delete(($log_ids[$num]));
            }
        }

        // PM Cache
        if (!empty($updated_products))
        {
            ExtensionPMCM::clearFromIdsProduct($updated_products);
        }

        // RETURN
        $return = json_encode(array('callback' => $callbacks));
    }
}
echo $return;
