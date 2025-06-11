<?php

$action = Tools::getValue('action', null);
$id_customer = (int) Tools::getValue('id_customer', null);
if (!empty($action))
{
    switch ($action){
        case 'add':
            $new_address = new Address();
            $data_form = $_POST;
            foreach ($data_form as $prop => $value)
            {
                $new_address->$prop = trim($value);
            }
            $new_address->id_customer = (int) $id_customer;

            try
            {
                $new_address->add();
                exit($new_address->id);
            }
            catch (PrestaShopException $e)
            {
                exit('ERR:'.$e->getMessage());
            }
            catch (Exception $e)
            {
                exit('ERR:'.$e->getMessage());
            }
            break;
        case 'update':
            $id_address = (int) Tools::getValue('id_address', null);
            $id_lang = (int) Tools::getValue('id_lang', null);
            $item = Tools::getValue('item', null);
            $value = Tools::getValue('value', null);
            if (!empty($id_address) && !empty($item))
            {
                $address = new Address($id_address);
                if ($address->id_customer != $id_customer)
                {
                    exit(_l('This address is not linked to current customer'));
                }
                switch ($item) {
                    case 'id_state':
                        if ($value == '-')
                        {
                            $value = 0;
                        }
                        break;
                }
                $address->$item = $value;
                try
                {
                    $address->update();
                    exit('OK');
                }
                catch (PrestaShopException $e)
                {
                    exit($e->getMessage());
                }
                catch (Exception $e)
                {
                    exit($e->getMessage());
                }
            }
            else
            {
                exit(_l('Empty data'));
            }
            break;
    }
}
