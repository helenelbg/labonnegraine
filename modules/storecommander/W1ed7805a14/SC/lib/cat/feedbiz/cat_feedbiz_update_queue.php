<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

$id_lang = Tools::getValue('id_lang', '0');
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
                    switch (Tools::getValue('feedb_view')) {
                        case 'grid_feedbiz_option':
                            list($id_product, $id_product_attribute, $id_lang) = explode('_', $row->row);
                            $fields = explode(',', 'force,disable,price,shipping,text');
                            $insert_field = array();
                            $insert_value = array();
                            $update_combo = array();
                            foreach ($fields as $field)
                            {
                                if (isset($_POST[$field]))
                                {
                                    $insert_field[] = '`'.bqSQL($field).'`';
                                    $insert_value[] = '"'.pSQL(Tools::getValue($field)).'"';
                                    $update_combo[] = '`'.bqSQL($field).'` = '.(!Tools::getValue($field) ? 'NULL' : '"'.pSQL(Tools::getValue($field)).'"');
                                }
                            }
                            $find = Db::getInstance()->getValue('SELECT COUNT(*) FROM '._DB_PREFIX_.'feedbiz_product_option WHERE id_product='.(int) $id_product.' AND id_product_attribute = '.(int) $id_product_attribute.' AND id_lang='.(int) $id_lang);
                            if (!empty($find) && !empty($update_combo))
                            {
                                $sql = 'UPDATE '._DB_PREFIX_.'feedbiz_product_option SET '.implode(', ', $update_combo).' WHERE id_product='.(int) $id_product.' AND id_product_attribute ='.(int) $id_product_attribute.' AND id_lang='.(int) $id_lang;
                                Db::getInstance()->Execute($sql);
                            }
                            elseif (!empty($insert_field) && !empty($insert_value))
                            {
                                $sql = 'INSERT INTO '._DB_PREFIX_.'feedbiz_product_option (`id_product`, `id_product_attribute`, `id_lang`, '.implode(',', $insert_field).') 
                                VALUES ('.(int) $id_product.', '.(int) $id_product_attribute.', '.(int) $id_lang.', '.implode(', ', $insert_value).')';
                                Db::getInstance()->Execute($sql);
                            }
                            break;
                        case 'grid_feedbiz_amazon_option':
                            list($id_product, $id_product_attribute, $region) = explode('_', $row->row);
                            $fields = explode(',', 'force,disable,price,shipping,text,nopexport,noqexport,fba,fba_value,latency,asin1,asin2,asin3,bullet_point1,bullet_point2,bullet_point3,bullet_point4,bullet_point5,shipping_type,gift_wrap,gift_message,browsenode,repricing_min,repricing_max,repricing_gap,shipping_group');
                            $insert_field = array();
                            $insert_value = array();
                            $update_combo = array();
                            foreach ($fields as $field)
                            {
                                if (isset($_POST[$field]))
                                {
                                    $insert_field[] = '`'.bqSQL($field).'`';
                                    $insert_value[] = '"'.pSQL(Tools::getValue($field)).'"';
                                    $update_combo[] = '`'.bqSQL($field).'` = '.(!Tools::getValue($field) ? 'NULL' : '"'.pSQL(Tools::getValue($field)).'"');
                                }
                            }
                            $find = Db::getInstance()->getValue('SELECT COUNT(*) FROM '._DB_PREFIX_.'feedbiz_amazon_options WHERE id_product='.(int) $id_product.' AND id_product_attribute = '.(int) $id_product_attribute." AND region='".pSQL($region)."'");
                            if (!empty($find))
                            {
                                $sql = 'UPDATE '._DB_PREFIX_.'feedbiz_amazon_options SET '.implode(', ', $update_combo).' WHERE id_product='.(int) $id_product.' AND id_product_attribute = '.(int) $id_product_attribute." AND region='".pSQL($region)."'";
                                Db::getInstance()->Execute($sql);
                            }
                            elseif (!empty($insert_field) && !empty($insert_value))
                            {
                                $sql = 'INSERT INTO '._DB_PREFIX_.'feedbiz_amazon_options (`id_product`, `id_product_attribute`, `region`, '.implode(',', $insert_field).') 
                                VALUES ('.(int) $id_product.', '.(int) $id_product_attribute.", '".pSQL($region)."', ".implode(', ', $insert_value).')';
                                Db::getInstance()->Execute($sql);
                            }
                            break;
                        case 'grid_feedbiz_cdiscount_option':
                            list($id_product, $id_product_attribute, $region) = explode('_', $row->row);
                            $fields = explode(',', 'force,disable,price,price_up,price_down,shipping,shipping_delay,clogistique,valueadded,text');
                            $insert_field = array();
                            $insert_value = array();
                            $update_combo = array();
                            foreach ($fields as $field)
                            {
                                if (isset($_POST[$field]))
                                {
                                    $insert_field[] = '`'.bqSQL($field).'`';
                                    $insert_value[] = '"'.pSQL(Tools::getValue($field)).'"';
                                    $update_combo[] = '`'.bqSQL($field).'` = '.(!Tools::getValue($field) ? 'NULL' : '"'.pSQL(Tools::getValue($field)).'"');
                                }
                            }
                            $find = Db::getInstance()->getValue('SELECT COUNT(*) FROM '._DB_PREFIX_.'feedbiz_cdiscount_options WHERE id_product='.(int) $id_product.' AND id_product_attribute = '.(int) $id_product_attribute." AND region='".pSQL($region)."'");
                            if (!empty($find))
                            {
                                $sql = 'UPDATE '._DB_PREFIX_.'feedbiz_cdiscount_options SET '.implode(', ', $update_combo).' WHERE id_product='.(int) $id_product.' AND id_product_attribute = '.(int) $id_product_attribute." AND region='".pSQL($region)."'";
                                Db::getInstance()->Execute($sql);
                            }
                            elseif (!empty($insert_field) && !empty($insert_value))
                            {
                                $sql = 'INSERT INTO '._DB_PREFIX_.'feedbiz_cdiscount_options (`id_product`, `id_product_attribute`, `region`, '.implode(',', $insert_field).') 
                                VALUES ('.(int) $id_product.', '.(int) $id_product_attribute.", '".pSQL($region)."', ".implode(', ', $insert_value).')';
                                Db::getInstance()->Execute($sql);
                            }
                            break;
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
