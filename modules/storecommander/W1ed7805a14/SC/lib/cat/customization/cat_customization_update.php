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
                $current_row_id = (int) $row->row;
                $action = $row->action;

                if (!empty($row->callback))
                {
                    $callbacks .= $row->callback.';';
                }

                if ($action != 'insert')
                {
                    $_POST = json_decode($row->params, true);
                }

                if (!empty($action) && $action == 'insert')
                {
                    $id_products = explode(',', $row->params['id_product']);
                    $newId = '';
                    foreach ($id_products as $id_product)
                    {
                        $sql = 'INSERT INTO `'._DB_PREFIX_.'customization_field` (id_product,type,required) VALUES ('.(int) $id_product.',0,0)';
                        Db::getInstance()->Execute($sql);
                        $id_customization_field = Db::getInstance()->Insert_ID();
                        if (!empty($newId))
                        {
                            $newId .= ',';
                        }
                        $newId .= $id_customization_field;
                        foreach ($languages as $lang)
                        {
                            if (version_compare(_PS_VERSION_, '1.6.0.12', '>='))
                            {
                                $shops = SCI::getSelectedShopActionList();
                                foreach ($shops as $shop)
                                {
                                    Db::getInstance()->execute('INSERT INTO '._DB_PREFIX_.'customization_field_lang (`id_customization_field`,`id_lang`,name,`id_shop`) VALUES ('.(int) $id_customization_field.','.(int) $lang['id_lang'].",'',".(int) $shop.')');
                                }
                            }
                            else
                            {
                                $sql = 'INSERT INTO `'._DB_PREFIX_.'customization_field_lang` (id_customization_field,id_lang,name) VALUES ('.(int) $id_customization_field.','.(int) $lang['id_lang'].",'')";
                                Db::getInstance()->Execute($sql);
                            }
                        }
                    }
                    if (!empty($newId))
                    {
                        $callbacks = str_replace('{newid}', $newId, $callbacks);
                    }
                }
                elseif (!empty($action) && $action == 'delete' && !empty($gr_id))
                {
                    if (version_compare(_PS_VERSION_, '1.7.3.0', '>='))
                    {
                        $sql = 'SELECT cf.*
                                FROM '._DB_PREFIX_.'customization c
                                INNER JOIN '._DB_PREFIX_.'customized_data cd
                                    ON (c.id_customization=cd.id_customization)
                                INNER JOIN '._DB_PREFIX_.'customization_field cf
                                    ON (cf.id_customization_field=cd.index AND cf.type=cd.type AND cf.id_customization_field = '.(int) $current_row_id.')
                                WHERE 1=1';
                        $res = Db::getInstance()->getRow($sql);
                        if (!empty($res['id_customization_field']))
                        {
                            $sql2 = 'UPDATE '._DB_PREFIX_.'customization_field SET is_deleted=1 WHERE id_customization_field='.(int) $current_row_id;
                            Db::getInstance()->Execute($sql2);
                        }
                        else
                        {
                            Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'customization_field` WHERE `id_customization_field` = '.(int) $current_row_id);
                            Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'customization_field_lang` WHERE `id_customization_field` = '.(int) $current_row_id);
                        }
                    }
                    else
                    {
                        Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'customization_field` WHERE `id_customization_field` = '.(int) $current_row_id);
                        Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'customization_field_lang` WHERE `id_customization_field` = '.(int) $current_row_id);
                    }
                }
                elseif (!empty($action) && $action == 'update' && !empty($gr_id))
                {
                    $list_id = explode(',', $gr_id);
                    foreach ($_POST as $field => $value)
                    {
                        switch ($field) {
                            case 'required':case 'type':
                            foreach ($list_id as $id)
                            {
                                addToHistory('customization_field', 'modification', $field, (int) $id, 0, _DB_PREFIX_.'customization_field', psql($value));
                            }
                            $sql = 'UPDATE '._DB_PREFIX_.'customization_field
                                        SET `'.bqSQL($field).'`='.(int) $value.'
                                        WHERE id_customization_field IN ('.pInSQL($gr_id).')';
                                Db::getInstance()->Execute($sql);
                                break;
                            default:
                                $final_sql = $fields_lang = $todo_lang = array();
                                foreach ($languages as $lang)
                                {
                                    $key_iso = 'name¤'.$lang['iso_code'];
                                    if (array_key_exists($key_iso, $_POST))
                                    {
                                        $id_lang = (int) $lang['id_lang'];
                                        $the_value = Tools::getValue($key_iso);
                                        foreach ($list_id as $id_customization_field)
                                        {
                                            $shops = SCI::getSelectedShopActionList();
                                            foreach ($shops as $shop)
                                            {
                                                if (version_compare(_PS_VERSION_, '1.6.0.12', '>='))
                                                {
                                                    $sql = 'SELECT COUNT(*) FROM '._DB_PREFIX_.'customization_field_lang WHERE id_customization_field='.(int) $id_customization_field.' AND id_lang='.(int) $id_lang.' AND id_shop = '.(int) $shop;
                                                    $test = Db::getInstance()->getValue($sql);
                                                    if ($test == 0)
                                                    {
                                                        $final_sql[] = 'INSERT IGNORE INTO '._DB_PREFIX_.'customization_field_lang (`id_customization_field`,`id_lang`,`name`,`id_shop`) VALUES ('.(int) $id_customization_field.','.(int) $id_lang.",'".psql($the_value)."',".(int) $shop.')';
                                                    }
                                                    else
                                                    {
                                                        $final_sql[] = 'UPDATE '._DB_PREFIX_."customization_field_lang SET `name` = '".pSQL($the_value)."' WHERE id_customization_field=".(int) $id_customization_field.' AND id_lang='.(int) $id_lang.' AND id_shop = '.(int) $shop;
                                                    }
                                                }
                                                else
                                                {
                                                    $sql = 'SELECT COUNT(*) FROM '._DB_PREFIX_.'customization_field_lang WHERE id_customization_field='.(int) $id_customization_field.' AND id_lang='.(int) $id_lang;
                                                    $test = Db::getInstance()->getValue($sql);
                                                    if ($test == 0)
                                                    {
                                                        $final_sql[] = 'INSERT IGNORE INTO '._DB_PREFIX_.'customization_field_lang (`id_customization_field`,`id_lang`,`name`) VALUES ('.(int) $id_customization_field.','.(int) $id_lang.",'".psql($the_value)."')";
                                                    }
                                                    else
                                                    {
                                                        $final_sql[] = 'UPDATE '._DB_PREFIX_."customization_field_lang SET `name` = '".pSQL($the_value)."' WHERE id_customization_field=".(int) $id_customization_field.' AND id_lang='.(int) $id_lang;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                                foreach($final_sql as $rowSql)
                                {
                                    Db::getInstance()->execute($rowSql);
                                }
                        }
                    }
                }
                /**
                 * Recalcule du champ customizable
                 * Pour tous les produits.
                 */
                Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'product` p
                        SET customizable=(SELECT count(id_customization_field) FROM `'._DB_PREFIX_.'customization_field` cf WHERE cf.id_product=p.id_product '.(version_compare(_PS_VERSION_, '1.7.3.0', '>=') ? " AND cf.is_deleted='0' " : '').'),
                        text_fields=(SELECT count(id_customization_field) FROM `'._DB_PREFIX_.'customization_field` cf WHERE cf.id_product=p.id_product AND cf.type=1 '.(version_compare(_PS_VERSION_, '1.7.3.0', '>=') ? " AND cf.is_deleted='0' " : '').'),
                        uploadable_files=(SELECT count(id_customization_field) FROM `'._DB_PREFIX_.'customization_field` cf WHERE cf.id_product=p.id_product AND cf.type=0 '.(version_compare(_PS_VERSION_, '1.7.3.0', '>=') ? " AND cf.is_deleted='0' " : '').')');
                if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                {
                    Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'product_shop` p
                            SET customizable=(SELECT count(id_customization_field) FROM `'._DB_PREFIX_.'customization_field` cf WHERE cf.id_product=p.id_product '.(version_compare(_PS_VERSION_, '1.7.3.0', '>=') ? " AND cf.is_deleted='0' " : '').'),
                            text_fields=(SELECT count(id_customization_field) FROM `'._DB_PREFIX_.'customization_field` cf WHERE cf.id_product=p.id_product AND cf.type=1 '.(version_compare(_PS_VERSION_, '1.7.3.0', '>=') ? " AND cf.is_deleted='0' " : '').'),
                            uploadable_files=(SELECT count(id_customization_field) FROM `'._DB_PREFIX_.'customization_field` cf WHERE cf.id_product=p.id_product AND cf.type=0 '.(version_compare(_PS_VERSION_, '1.7.3.0', '>=') ? " AND cf.is_deleted='0' " : '').')');
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
