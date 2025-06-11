<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

$action = Tools::getValue('action', '');

## Update from cat_feedbiz_grid
if (Tools::getValue('rows') || $action == 'insert')
{
    $return = 'ERROR: Try again later';

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

                if (!empty($action) && $action == 'update')
                {
                    $id_product = $row->row;
                    $fields = explode(',', 'force,disable,price,shipping,shipping_delay,clogistique,valueadded,text');
                    $insert_field = array();
                    $insert_value = array();
                    $update_combo = array();
                    $id_lang = (int) Language::getIdByIso('fr');
                    $insert_field[] = '`id_lang`';
                    $insert_value[] = $id_lang;
                    foreach ($fields as $field)
                    {
                        if (isset($_POST[$field]))
                        {
                            $insert_field[] = '`'.bqSQL($field).'`';
                            $insert_value[] = '"'.pSQL(Tools::getValue($field)).'"';
                            $update_combo[] = '`'.bqSQL($field).'` = '.(!Tools::getValue($field) ? 'NULL' : '"'.pSQL(Tools::getValue($field)).'"');
                        }
                    }
                    $find = Db::getInstance()->getValue('SELECT COUNT(*) FROM '._DB_PREFIX_.'cdiscount_product_option WHERE id_product='.(int) $id_product);
                    if (!empty($find) && !empty($update_combo))
                    {
                        $sql = 'UPDATE '._DB_PREFIX_.'cdiscount_product_option SET '.implode(', ', $update_combo).' WHERE id_product='.(int) $id_product;
                        Db::getInstance()->Execute($sql);
                    }
                    elseif (!empty($insert_field) && !empty($insert_value))
                    {
                        $sql = 'INSERT INTO '._DB_PREFIX_.'cdiscount_product_option (`id_product`, '.implode(',', $insert_field).') 
                        VALUES ('.(int) $id_product.', '.implode(', ', $insert_value).')';
                        Db::getInstance()->Execute($sql);
                    }
                }

                QueueLog::delete(($log_ids[$num]));
            }
        }

        // RETURN
        $return = json_encode(array('callback' => $callbacks));
    }
    exit($return);
}
