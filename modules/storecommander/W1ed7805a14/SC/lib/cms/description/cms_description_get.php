<?php

$id_cms = Tools::getValue('id_cms', '0');
$id_lang = Tools::getValue('id_lang', '0');
$id_shop = Tools::getValue('id_shop', 0);

if (Tools::getValue('act', '') == 'cms_description_get')
{
    $sql = 'SELECT `content` FROM '._DB_PREFIX_.'cms_lang 
                WHERE id_cms='.(int) $id_cms.'
                AND id_lang='.(int) $id_lang;
    if (version_compare(_PS_VERSION_, '1.6.0.12', '>=') && !empty($id_shop))
    {
        $sql .= ' AND id_shop='.(int) $id_shop;
    }
    $row = Db::getInstance()->getValue($sql);
    echo $row;
}
