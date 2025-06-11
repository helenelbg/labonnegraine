<?php

$field_to_get = 'description';

$field_to_update = (string) Tools::getValue('content', '');
if (Tools::getValue('act', '') != basename(__FILE__, '.php')
    || $field_to_update !== $field_to_get)
{
    exit;
}

$id_supplier = (int) Tools::getValue('id_supplier');
$id_lang = (int) Tools::getValue('id_lang');

$sql = 'SELECT `'.bqSQL($field_to_update).'` as field_from_db 
        FROM '._DB_PREFIX_.'supplier_lang 
        WHERE id_supplier='.(int) $id_supplier.' 
        AND id_lang='.(int) $id_lang;
echo Db::getInstance()->getValue($sql);
