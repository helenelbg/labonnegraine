<?php

@error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
@ini_set('display_errors', 'ON');

$id_lang = Tools::getValue('sc_id_lang', '0');
$action = Tools::getValue('action', '');

$return = 'ERROR: Try again later';

// FUNCTIONS
$debug = false;
$extraVars = '';
$return_datas = array();

// Récupération de toutes les modifications à effectuer
if (Tools::getValue('rows') || $action == 'insert')
{
    if ($action == 'format')
    {
        $type = Tools::getValue('type', '');
        switch ($type){
            case 'capitalize':
                $customers = Db::getInstance()->executeS('SELECT id_customer,LOWER(lastname) as lastname,LOWER(firstname) as firstname FROM '._DB_PREFIX_.'customer');
                if (!empty($customers))
                {
                    foreach ($customers as $customer)
                    {
                        $lastname = ucwords($customer['lastname'], "'- ");
                        $firstname = ucwords($customer['firstname'], "'- ");
                        Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'customer
                                    SET firstname="'.pSQL($firstname).'",lastname="'.pSQL($lastname).'"
                                    WHERE id_customer = '.(int) $customer['id_customer']);
                    }
                }
                exit('OK');
                break;
            case 'uppercase':
                $customers = Db::getInstance()->executeS('SELECT id_customer,UPPER(lastname) as lastname,LOWER(firstname) as firstname FROM '._DB_PREFIX_.'customer');
                if (!empty($customers))
                {
                    foreach ($customers as $customer)
                    {
                        $firstname = ucwords($customer['firstname'], "'- ");
                        Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'customer
                                    SET firstname="'.pSQL($firstname).'",lastname="'.pSQL($customer['lastname']).'"
                                    WHERE id_customer = '.(int) $customer['id_customer']);
                    }
                }
                exit('OK');
                break;
        }
        exit;
    }
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
                $_POST['gr_id'] = $gr_id;

                if (!empty($action) && $action == 'insert')
                {// Todo Insert
                    /*if(!empty($newId))
                    {
                        $callbacks = str_replace("{newid}", $newId, $callbacks) ;
                    }*/
                }
                elseif (!empty($action) && $action == 'delete' && !empty($gr_id))
                {
                    if (array_key_exists('id_customer', $_POST))
                    {
                        $id_customer = (int) Tools::getValue('id_customer');
                        $full_delete = (bool) Tools::getValue('full_delete');
                        if ($full_delete)
                        {
                            $customer = new Customer((int) $id_customer);
                            $customer->delete();
                        }
                        else
                        {
                            $sql = 'UPDATE '._DB_PREFIX_.'customer
                                    SET deleted = 1, date_upd=NOW()
                                    WHERE id_customer = '.(int) $id_customer;
                            Db::getInstance()->execute($sql);
                        }
                        addToHistory('customer', 'delete', 'customer', (int) $id_customer, null, _DB_PREFIX_.'customer', null, null);
                    }
                }
                elseif (!empty($action) && $action == 'update' && !empty($gr_id))
                {
                    $fields = array('id_gender', 'siret', 'ape', 'firstname', 'lastname', 'email', 'active', 'newsletter', 'optin', 'birthday', 'id_default_group', 'note', 'id_lang');
                    $fields_address = array('firstname', 'lastname', 'address1', 'address2', 'postcode', 'city', 'id_state', 'id_country', 'other', 'phone', 'phone_mobile', 'vat_number');
                    if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                    {
                        $fields[] = 'website';
                    }
                    $for_address = array_key_exists('id_address', $_POST);
                    if ($for_address)
                    {
                        $fields_address[] = 'company';
                        $id_address = (int) Tools::getValue('id_address');
                    }
                    else
                    {
                        $fields[] = 'company';
                    }
                    $id_customer = (int) ($for_address ? Tools::getValue('id_customer') : $gr_id);
                    $todo = array();
                    $todo_address = array();
                    sc_ext::readCustomCustomersGridsConfigXML('updateSettings');
                    sc_ext::readCustomCustomersGridsConfigXML('onBeforeUpdateSQL');

                    foreach ($fields as $field)
                    {
                        if (array_key_exists($field, $_POST))
                        {
                            $value = Tools::getValue($field);
                            $todo[] = $field."='".pSQL($value)."'";
                            addToHistory('customer', 'modification', $field, (int) $id_customer, 0, _DB_PREFIX_.'customer', pSQL($value));
                        }
                    }

                    if ($for_address)
                    {
                        foreach ($fields_address as $field)
                        {
                            if (array_key_exists($field, $_POST))
                            {
                                $value = Tools::getValue($field);
                                $todo_address[] = $field."='".pSQL($value)."'";
                                addToHistory('address', 'modification', $field, (int) $id_address, 0, _DB_PREFIX_.'address', pSQL($value));
                            }
                        }
                    }

                    if (!empty($todo))
                    {
                        $sql = 'UPDATE '._DB_PREFIX_.'customer
                                SET '.implode(' , ', $todo).', date_upd = NOW()
                                WHERE id_customer='.(int) $id_customer;
                        Db::getInstance()->Execute($sql);
                    }

                    if (!empty($todo_address))
                    {
                        $sql = 'UPDATE '._DB_PREFIX_.'address
                                SET '.implode(', ', $todo_address).', date_upd = NOW()
                                WHERE id_address='.(int) $id_address;
                        Db::getInstance()->Execute($sql);
                    }
                    sc_ext::readCustomCustomersGridsConfigXML('onAfterUpdateSQL');
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
