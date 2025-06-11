<?php

$contentType = Tools::getValue('content', '');
if (Tools::getValue('act', '') == 'man_description_get' && sc_in_array($contentType, array('short_description', 'description'), 'catDescGet_fields'))
{
    $id_manufacturer = Tools::getValue('id_manufacturer', '0');
    $id_lang = Tools::getValue('id_lang', '0');

    $sql = 'SELECT '.psql($contentType).' FROM '._DB_PREFIX_."manufacturer_lang WHERE id_manufacturer=".(int) $id_manufacturer." AND id_lang=".(int) $id_lang;
    $row = Db::getInstance()->getRow($sql);
    echo $row[$contentType];
}
