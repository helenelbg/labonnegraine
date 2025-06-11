<?php

    $sql = 'SELECT DISTINCT i.id_product,i.id_image FROM '._DB_PREFIX_.'image i
                WHERE NOT EXISTS (SELECT * FROM '._DB_PREFIX_.'image ii WHERE i.id_product=ii.id_product AND ii.cover=1)
                AND i.position=1
                GROUP BY i.id_product';
    $res = Db::getInstance()->ExecuteS($sql);
    $updated_products = array();
    if (count($res))
    {
        foreach ($res as $i)
        {
            $sql = 'UPDATE '._DB_PREFIX_.'image SET cover=1 WHERE id_product='.(int) $i['id_product'].' AND id_image='.(int) $i['id_image'];
            Db::getInstance()->Execute($sql);
            $updated_products[$i['id_product']] = $i['id_product'];
        }
    }
    if (!empty($updated_products))
    {
        ExtensionPMCM::clearFromIdsProduct($updated_products);
    }
    echo 'Ok';
