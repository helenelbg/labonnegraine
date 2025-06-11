<?php

$id_customer = (int) Tools::getValue('id_customer', 0);

if ($id_customer > 0)
{
    $sql = 'SELECT note 
            FROM '._DB_PREFIX_.'customer 
            WHERE id_customer = '.(int) $id_customer;
    exit(Db::getInstance()->getValue($sql));
}
