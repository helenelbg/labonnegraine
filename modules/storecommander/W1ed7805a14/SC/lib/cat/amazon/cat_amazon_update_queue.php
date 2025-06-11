<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

$id_lang = Tools::getValue('id_lang', '0');
$action = Tools::getValue('action', '');

## Update from cat_feedbiz_grid
if (Tools::getValue('rows') || $action == 'insert')
{
    $return = 'ERROR: Try again later';

    $current_id_shop = (int) SCI::getSelectedShop();
    Context::getContext()->shop = new Shop($current_id_shop);
    Shop::setContext(Shop::CONTEXT_SHOP, $current_id_shop);

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
        $date = date('Y-m-d H:i:s');

        if ($action != 'create')
        {
            require_once _PS_MODULE_DIR_.'amazon/amazon.php';
        }

        foreach ($rows as $num => $row)
        {
            $gr_id = (int) $row->row;
            $action = $row->action;

            if (!empty($row->callback))
            {
                $callbacks .= $row->callback.';';
            }

            if ($action != 'insert' && $action != 'create')
            {
                $_POST = array();
                $_POST = (array) json_decode($row->params);
            }

            switch ($action) {
                case 'create':
                    $row_list = explode(',', $row->row);
                    foreach ($row_list as $unique_row)
                    {
                        list($id_product, $id_product_attribute, $id_lang) = explode('_', $unique_row);
                        AmazonProduct::marketplaceActionSet(Amazon::ADD, $id_product, null, null, $id_lang);
                    }
                    break;
                case 'update':
                    list($id_product, $id_product_attribute, $region) = explode('_', $row->row);
                    $fields = explode(',', 'force,disable,price,shipping,text,nopexport,noqexport,fba,fba_value,latency,asin1,asin2,asin3,bullet_point1,bullet_point2,bullet_point3,bullet_point4,bullet_point5,shipping_type,gift_wrap,gift_message,browsenode,repricing_min,repricing_max,repricing_gap,alternative_title,alternative_description');
                    $insert_field = array();
                    $insert_value = array();
                    $update_combo = array();
                    foreach ($fields as $field)
                    {
                        if (isset($_POST[$field]))
                        {
                            $up_field = '`'.bqSQL($field).'`';
                            if ($field == 'alternative_description')
                            {
                                $value = '"'.pSQL(Tools::getValue($field), true).'"';
                            }
                            else
                            {
                                $value = '"'.pSQL(Tools::getValue($field)).'"';
                            }
                            $insert_field[] = $up_field;
                            switch ($field) {
                                case 'disable':
                                    $value_field = (int) Tools::getValue($field);
                                    $insert_value[] = ((empty($value_field) && $value_field != 0) || $value_field == -1 ? 'NULL' : $value);
                                    $update_combo[] = $up_field.' = '.((empty($value_field) && $value_field != 0) || $value_field == -1 ? 'NULL' : $value);
                                    break;
                                default:
                                    $insert_value[] = $value;
                                    $update_combo[] = $up_field.' = '.(!Tools::getValue($field) ? 'NULL' : $value);
                            }
                        }
                    }
                    $find = Db::getInstance()->getValue('SELECT COUNT(*) FROM '._DB_PREFIX_.'marketplace_product_option WHERE id_product='.(int) $id_product.' AND id_product_attribute = '.(int) $id_product_attribute.' AND id_lang = '.(int) $region.' AND id_shop='.$current_id_shop);
                    if (!empty($find) && !empty($update_combo))
                    {
                        $sql = 'UPDATE '._DB_PREFIX_.'marketplace_product_option SET '.implode(', ', $update_combo).' WHERE id_product='.(int) $id_product.' AND id_product_attribute = '.(int) $id_product_attribute.' AND id_lang = '.(int) $region.' AND id_shop='.$current_id_shop;
                        Db::getInstance()->Execute($sql);
                    }
                    elseif (!empty($insert_field) && !empty($insert_value))
                    {
                        $sql = 'INSERT INTO '._DB_PREFIX_.'marketplace_product_option (`id_product`, `id_product_attribute`, `id_lang`, '.implode(',', $insert_field).',`id_shop`)
                        VALUES ('.(int) $id_product.', '.(int) $id_product_attribute.', '.(int) $region.', '.implode(', ', $insert_value).','.$current_id_shop.')';
                        Db::getInstance()->Execute($sql);
                    }

                    ## change product action to => u update
                    Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'marketplace_product_action
                                                        WHERE id_product = '.(int) $id_product.'
                                                        AND id_lang = '.(int) $region.'
                                                        AND marketplace = "amazon"
                                                        AND id_shop = '.$current_id_shop);
                    $sql = 'INSERT INTO '._DB_PREFIX_.'marketplace_product_action (`id_product`, `id_lang`, `marketplace`, `action`, `date_add`,`id_shop`)
                    VALUES ('.(int) $id_product.', '.(int) $region.", 'amazon', 'u', '".date('Y-m-d H:i:')."','.$current_id_shop.')";
                    Db::getInstance()->Execute($sql);
                    break;
            }
        }

        // RETURN
        $return = json_encode(array('callback' => $callbacks));
    }
    exit($return);
}
