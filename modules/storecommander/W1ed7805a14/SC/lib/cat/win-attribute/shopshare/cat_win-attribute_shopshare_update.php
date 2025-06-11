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
        // pour une ou plusieurs group d'attributs passés en params
        case 'present':
            foreach ($ids as $id)
            {
                if ($value)
                {
                    $sql = 'INSERT INTO '._DB_PREFIX_.'attribute_group_shop 
                              VALUES ('.(int) $id.','.(int) $id_shop.')';
                    Db::getInstance()->execute($sql);
                    $ids_attribute = Db::getInstance()->executeS('SELECT id_attribute FROM '._DB_PREFIX_.'attribute WHERE id_attribute_group = '.(int) $id);
                    if (!empty($ids_attribute))
                    {
                        $before_sql2 = 'INSERT IGNORE INTO '._DB_PREFIX_.'attribute_shop VALUES ';
                        $sql2 = array();
                        foreach ($ids_attribute as $row)
                        {
                            $sql2[] = '('.(int) $row['id_attribute'].','.(int) $id_shop.')';
                        }
                        $sql2 = $before_sql2.implode(',', $sql2);
                        Db::getInstance()->execute($sql2);
                    }
                }
                else
                {
                    $sql = 'DELETE
                          FROM '._DB_PREFIX_.'attribute_group_shop 
                          WHERE id_attribute_group = '.(int) $id.' 
                          AND id_shop = '.(int) $id_shop.';'.
                        'DELETE
                          FROM '._DB_PREFIX_.'attribute_shop 
                          WHERE id_attribute IN (SELECT id_attribute FROM '._DB_PREFIX_.'attribute WHERE id_attribute_group = '.(int) $id.')
                          AND id_shop = '.(int) $id_shop;
                    Db::getInstance()->execute($sql);
                }
            }
            break;
        // Modification de present
        // pour un ou plusieurs shops passés en params
        // pour un ou plusieurs group d'attributs passées en params
        case 'mass_present':
            $shops = explode(',', $id_shop);
            foreach ($shops as $id_shop)
            {
                foreach ($ids as $id)
                {
                    if ($value)
                    {
                        $sql = 'INSERT INTO '._DB_PREFIX_.'attribute_group_shop 
                                  VALUES ('.(int) $id.','.(int) $id_shop.')';
                        Db::getInstance()->execute($sql);
                        $ids_attribute = Db::getInstance()->executeS('SELECT id_attribute FROM '._DB_PREFIX_.'attribute WHERE id_attribute_group = '.(int) $id);
                        if (!empty($ids_attribute))
                        {
                            $before_sql2 = 'INSERT IGNORE INTO '._DB_PREFIX_.'attribute_shop VALUES ';
                            $sql2 = array();
                            foreach ($ids_attribute as $row)
                            {
                                $sql2[] = '('.(int) $row['id_attribute'].','.(int) $id_shop.')';
                            }
                            $sql2 = $before_sql2.implode(',', $sql2);
                            Db::getInstance()->execute($sql2);
                        }
                    }
                    else
                    {
                        $sql = 'DELETE
                              FROM '._DB_PREFIX_.'attribute_group_shop 
                              WHERE id_attribute_group = '.(int) $id.' 
                              AND id_shop = '.(int) $id_shop.';'.
                            'DELETE
                              FROM '._DB_PREFIX_.'attribute_shop 
                              WHERE id_attribute IN (SELECT id_attribute FROM '._DB_PREFIX_.'attribute WHERE id_attribute_group = '.(int) $id.')
                              AND id_shop = '.(int) $id_shop;
                        Db::getInstance()->execute($sql);
                    }
                }
            }
            break;
    }
}
