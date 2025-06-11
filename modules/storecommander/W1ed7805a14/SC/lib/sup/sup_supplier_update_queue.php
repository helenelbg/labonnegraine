<?php

$id_lang = Tools::getValue('id_lang', '0');
$action = Tools::getValue('action', '');

$return = 'ERROR: Try again later';

// FUNCTIONS
$debug = false;
$extraVars = '';
$updated_cms = array();
$return_datas = array();

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
                $id_supplier = $row->row;
                $updated_cms[$id_supplier] = $id_supplier;
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
                    $newSupplier = new Supplier();
                    $newSupplier->name = Tools::getValue('name');
                    if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                    {
                        $newSupplier->id_shop_list = SCI::getSelectedShopActionList();
                    }
                    $newSupplier->active = _s('SUP_SUPPLIER_CREA_ACTIVE');

                    foreach ($languages as $lang)
                    {
                        $newSupplier->link_rewrite[$lang['id_lang']] = 'new-supplier';
                    }
                    $newSupplier->add();
                    $newId = $newSupplier->id;

                    if (!empty($newId))
                    {
                        $new_address = new Address();
                        $new_address->alias = 'supplier #'.$newId;
                        $new_address->firstname = 'supplier';
                        $new_address->lastname = 'supplier';
                        $new_address->address1 = '-';
                        $new_address->city = '-';
                        $new_address->id_country = Sci::getConfigurationValue('PS_COUNTRY_DEFAULT');
                        $new_address->id_supplier = (int) $newId;
                        $new_address->add();

                        $callbacks = str_replace('{newid}', $newId, $callbacks);
                    }
                }
                elseif (!empty($action) && $action == 'delete' && !empty($gr_id))
                {
                    $supplier = new Supplier((int) $gr_id);

                    $sql = 'DELETE FROM `'._DB_PREFIX_.'address` WHERE id_supplier='.(int) $supplier->id;
                    Db::getInstance()->Execute($sql);
                    if (SCMS)
                    {
                        $sql = 'SELECT id_shop FROM '._DB_PREFIX_.'supplier_shop WHERE id_supplier='.(int) $supplier->id;
                        $id_shop_list_array = Db::getInstance()->ExecuteS($sql);
                        $id_shop_list = array();
                        foreach ($id_shop_list_array as $array_shop)
                        {
                            $id_shop_list[] = $array_shop['id_shop'];
                        }
                        $supplier->id_shop_list = $id_shop_list;
                    }
                    $supplier->delete();
                    addToHistory('sup_tree', 'delete', 'supplier', (int) $supplier->id, null, _DB_PREFIX_.'supplier', null, null);
                }
                elseif (!empty($action) && $action == 'update' && !empty($gr_id))
                {
                    $id_lang = (int) Tools::getValue('id_lang');
                    $id_supplier = $id_supplier; // for compatibility with old extensions - DO NOT REMOVE
                    $fields = array('id_supplier', 'name', 'active');
                    $fields_lang = array('meta_title', 'meta_description', 'meta_keywords');
                    $fields_address = array('phone', 'phone_mobile', 'alias', 'firstname', 'lastname', 'company', 'address1', 'address2', 'postcode', 'city', 'id_state', 'id_country', 'dni', 'other');
                    $fieldsWithHTML = array();
                    $todo = array();
                    $todoshop = array();
                    $todo_lang = array();
                    $versSuffix = '';
                    $updated_field = $row->updated_field;

                    foreach ($fields as $field)
                    {
                        if (isset($_POST[$field]))
                        {
                            switch ($field) {
                                case 'active':
                                    if (_r('ACT_SUP_ENABLE_SUPPLIER'))
                                    {
                                        $todo[] = "`active`='".psql(Tools::getValue($field))."'";
                                    }
                                    break;
                                default:
                                    $value = psql(Tools::getValue($field), (sc_in_array($field, $fieldsWithHTML, 'supplierUpdateQueue_fieldsWithHTML') ? true : false));
                                    $todo[] = '`'.bqSQL($field)."`='".$value."'";
                                    break;
                            }
                        }
                    }

                    foreach ($fields_lang as $field)
                    {
                        if (isset($_POST[$field]))
                        {
                            $value = psql(Tools::getValue($field), (sc_in_array($field, $fieldsWithHTML, 'supplierUpdateQueue_fieldsWithHTML') ? true : false));
                            $todo_lang[] = '`'.bqSQL($field)."`='".$value."'";
                            addToHistory('sup_tree', 'modification', $field, (int) $id_supplier, $id_lang, _DB_PREFIX_.'supplier_lang', $value);
                        }
                    }

                    if (in_array($updated_field, $fields_address))
                    {
                        if (isset($_POST[$updated_field]))
                        {
                            $id_address = Db::getInstance()->getValue('SELECT id_address FROM '._DB_PREFIX_.'address WHERE id_supplier = '.(int) $id_supplier);
                            $address = new Address($id_address);
                            $address->id_supplier = (int) $id_supplier;
                            if (empty($id_address))
                            {
                                $address->id_country = (int) SCI::getConfigurationValue('PS_COUNTRY_DEFAULT');
                                $address->alias = 'supplier #'.$id_supplier;
                                $address->lastname = 'supplier';
                                $address->firstname = 'supplier';
                                $address->address1 = '-';
                                $address->city = '-';
                            }
                            $address->$updated_field = Tools::getValue($updated_field);
                            $address->save();
                        }
                    }

                    $todo[] = '`date_upd`= NOW()';

                    if (count($todo))
                    {
                        $sql = 'UPDATE '._DB_PREFIX_.'supplier SET '.implode(' , ', $todo).' WHERE id_supplier='.(int) $id_supplier;
                        Db::getInstance()->Execute($sql);
                    }
                    if (count($todo_lang))
                    {
                        $sql = 'UPDATE '._DB_PREFIX_.'supplier_lang SET '.implode(' , ', $todo_lang).' WHERE id_supplier='.(int) $id_supplier.' AND id_lang='.(int) $id_lang;
                        if ($debug)
                        {
                            $dd .= $sql2."\n";
                        }
                        Db::getInstance()->Execute($sql);
                    }
                }

                $return_callback = '';
                foreach ($return_datas as $key => $val)
                {
                    if (!empty($key))
                    {
                        if (!empty($return_callback))
                        {
                            $return_callback .= ',';
                        }
                        $return_callback .= $key.":'".str_replace("'", "\'", $val)."'";
                    }
                }
                if (!empty($extraVars))
                {
                    if (!empty($return_callback))
                    {
                        $return_callback .= ',';
                    }
                    $return_callback .= $extraVars;
                }
                $return_callback = '{'.$return_callback.'}';
                $callbacks = str_replace('{data}', $return_callback, $callbacks);

                QueueLog::delete(($log_ids[$num]));
            }
        }

        // RETURN
        $return = json_encode(array('callback' => $callbacks));
    }
}

echo $return;
