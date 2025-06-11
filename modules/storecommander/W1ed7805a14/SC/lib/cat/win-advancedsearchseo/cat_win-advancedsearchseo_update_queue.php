<?php

$action = Tools::getValue('action', '');
$return = 'ERROR: Try again later';
$as_version = SCI::getModuleVersion('pm_advancedsearch4');
if (version_compare($as_version, '5.0.0', '>='))
{
    $advanced_search_definition_fields = \AdvancedSearch\Models\Seo::$definition['fields'];
}
else
{
    $advanced_search_definition_fields = array(
        'meta_title' => 128,
        'meta_description' => 255,
        'title' => 128,
        'seo_url' => 128,
        'meta_keywords' => 255,
    );
}

// FUNCTIONS
$updated_products = array();

// Récupération de toutes les modifications à effectuer
if (Tools::getValue('rows') || $action == 'insert')
{
    if(_PS_MAGIC_QUOTES_GPC_)
            $_POST["rows"] = Tools::getValue('rows');
        $rows = json_decode($_POST["rows"]);

    if (is_array($rows) && count($rows) > 0)
    {
        $callbacks = array();

        // Première boucle pour remplir la table sc_queue_log
        // avec toutes ces modifications
        $log_ids = array();
        $date = date('Y-m-d H:i:s');
        foreach ($rows as $num => $row)
        {
            $id = QueueLog::add($row->name, $row->row, $row->action, (!empty($row->params) ? $row->params : array()), (!empty($row->callback) ? $row->callback : null), $date);
            $log_ids[$num] = $id;
        }

        $sql_todo = array();

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
                    $callbacks[$num] = $row->callback;
                }

                $_POST = array();
                $_POST = (array) json_decode($row->params);

                if (!empty($action) && $action == 'update' && !empty($gr_id))
                {
                    $allowed_fields = array(
                        'seo_url',
                        'title',
                        'description',
                        'footer_description',
                        'meta_title',
                        'meta_description',
                        'meta_keywords',
                    );
                    $data_for_sql = $jsonData = array();
                    list($id_seo, $id_lang) = explode('_', $gr_id);
                    foreach ($allowed_fields as $field)
                    {
                        if (isset($_POST[$field]))
                        {
                            $data = Tools::getValue($field);
                            if (array_key_exists($field, $advanced_search_definition_fields)
                                && array_key_exists('size', $advanced_search_definition_fields[$field]))
                            {
                                ## limitation du nombre de caractères autorisés
                                $data = substr($data, 0, (int) $advanced_search_definition_fields[$field]['size']);
                            }

                            $jsonData[$field] = $data;

                            if (in_array($field, array('title', 'meta_title')))
                            {
                                $jsonData['seo_url'] = link_rewrite($data, Language::getIsoById($id_lang));
                            }
                        }
                    }

                    if (!empty($jsonData))
                    {
                        $html = false;
                        foreach ($jsonData as $field => $data)
                        {
                            if (in_array($field, array('description', 'footer_description')))
                            {
                                $html = true;
                            }
                            $data_for_sql[] = '`'.bqSQL($field).'` = "'.bqSQL($data, $html).'"';
                        }
                    }

                    if (!empty($data_for_sql))
                    {
                        $sql = 'UPDATE '._DB_PREFIX_.'pm_advancedsearch_seo_lang
                                SET '.implode(', ', $data_for_sql).'
                                WHERE id_seo = '.(int) $id_seo.'
                                AND id_lang ='.(int) $id_lang;
                        $sql_todo[] = $sql;
                    }

                    if (array_key_exists($num, $callbacks))
                    {
                        $callbacks[$num] = str_replace('{jsonData}', json_encode($jsonData), $callbacks[$num]);
                    }
                }

                QueueLog::delete(($log_ids[$num]));
            }
        }

        if (!empty($sql_todo))
        {
            foreach($sql_todo as $rowSql)
            {
                Db::getInstance()->execute($rowSql);
            }
        }

        // RETURN
        $return = json_encode(array('callback' => implode('', $callbacks)));
    }
}
echo $return;
