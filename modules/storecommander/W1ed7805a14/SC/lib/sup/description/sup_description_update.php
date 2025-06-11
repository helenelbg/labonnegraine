<?php

$field_to_update = 'description';

if (Tools::getValue('act', '') != basename(__FILE__, '.php')
    || !Tools::getValue($field_to_update, null))
{
    exit;
}
$id_supplier = (int) Tools::getValue('id_supplier');
$id_lang = (int) Tools::getValue('id_lang');
$field_to_update_value = (string) Tools::getValue($field_to_update, '');

if (version_compare(_PS_VERSION_, '1.6.0.0', '>='))
{
    if (!Validate::isCleanHtml($field_to_update_value, (int) Configuration::get('PS_ALLOW_HTML_IFRAME')))
    {
        if (!Configuration::get('PS_ALLOW_HTML_IFRAME'))
        {
            exit('ERR|'.$field_to_update.'_with_iframe');
        }
        else
        {
            exit('ERR|'.$field_to_update.'_invalid');
        }
    }
}
elseif (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
{
    if (!Validate::isString($field_to_update_value))
    {
        exit('ERR|'.$field_to_update.'_invalid');
    }
}

$sql = 'UPDATE '._DB_PREFIX_.'supplier_lang 
        SET `'.bqSQL($field_to_update)."`='".psql($field_to_update_value, true)."' 
        WHERE id_supplier=".(int) $id_supplier.' 
        AND id_lang='.(int) $id_lang;
Db::getInstance()->Execute($sql);
exit('OK');
