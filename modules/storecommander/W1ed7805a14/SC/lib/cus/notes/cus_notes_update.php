<?php

$id_customer = (int) Tools::getValue('id_customer', 0);
$content = Tools::getValue('content', null);

if ($id_customer > 0)
{
    $sql = 'UPDATE '._DB_PREFIX_.'customer 
            SET note="'.pSQL($content).'" 
            WHERE id_customer = '.(int) $id_customer;
    if (!Db::getInstance()->execute($sql))
    {
        exit('KO');
    }
}
exit('OK');
