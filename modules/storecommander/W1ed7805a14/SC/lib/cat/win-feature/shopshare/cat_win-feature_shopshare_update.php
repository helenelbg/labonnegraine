<?php

$idlist = Tools::getValue('idlist', '');
$action = Tools::getValue('action', '');
$id_lang = Tools::getValue('id_lang', '0');
$id_shop = Tools::getValue('id_shop', '0');
$value = Tools::getValue('value', '0');

if ($value == 'true')
{
    $value = 1;
}
else
{
    $value = 0;
}

$ids = explode(',', $idlist);
$multiple = false;
if (count($ids) > 1)
{
    $multiple = true;
}

if ($action != '' && !empty($id_shop) && !empty($idlist))
{
    switch ($action) {
        // Modification de present pour le shop passé en params
        // pour une ou plusieurs caractéristiques passés en params
        case 'present':
            foreach ($ids as $id)
            {
                if ($value)
                {
                    $sql = 'INSERT INTO '._DB_PREFIX_.'feature_shop 
                              VALUES ('.(int) $idlist.','.(int) $id_shop.')';
                    Db::getInstance()->execute($sql);
                }
                else
                {
                    $sql = 'DELETE
                          FROM '._DB_PREFIX_.'feature_shop 
                          WHERE id_feature = '.(int) $idlist.' 
                          AND id_shop = '.(int) $id_shop;
                    Db::getInstance()->execute($sql);
                }
            }
            break;
        // Modification de present
        // pour un ou plusieurs shops passés en params
        // pour un ou plusieurs feature passées en params
        case 'mass_present':
            $shops = explode(',', $id_shop);
            foreach ($shops as $shop)
            {
                foreach ($ids as $id)
                {
                    if ($value)
                    {
                        $sql = 'INSERT INTO '._DB_PREFIX_.'feature_shop 
                              VALUES ('.(int) $id.','.(int) $shop.')';
                        Db::getInstance()->execute($sql);
                    }
                    else
                    {
                        $sql = 'DELETE
                              FROM '._DB_PREFIX_.'feature_shop 
                              WHERE id_feature = '.(int) $id.' 
                              AND id_shop = '.(int) $shop;
                        Db::getInstance()->execute($sql);
                    }
                }
            }
            break;
    }
}
