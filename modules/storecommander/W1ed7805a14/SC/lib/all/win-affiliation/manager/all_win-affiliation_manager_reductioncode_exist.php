<?php

$id_lang = (int) Tools::getValue('id_lang', 0);
$id_partner = (int) Tools::getValue('id_partner', 0);
$code = (Tools::getValue('code', ''));

$return = 'KO';

if (!empty($code))
{
    if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
    {
        $sql = 'SELECT id_cart_rule
                FROM '._DB_PREFIX_."cart_rule
                WHERE code='".pSQL($code)."'";
        $res = Db::getInstance()->ExecuteS($sql);
        if (empty($res[0]['id_cart_rule']))
        {
            $return = 'OK';
        }
    }
    else
    {
        $sql = 'SELECT id_discount
                FROM '._DB_PREFIX_."discount
                WHERE name='".pSQL($code)."'";
        $res = Db::getInstance()->ExecuteS($sql);
        if (empty($res[0]['id_discount']))
        {
            $return = 'OK';
        }
    }
}

echo $return;
