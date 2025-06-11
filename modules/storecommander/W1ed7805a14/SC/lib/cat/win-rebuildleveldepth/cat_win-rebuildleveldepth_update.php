<?php

    $id_shop = (int) Tools::getValue('id_shop', SCI::getSelectedShop());
    // Fix level_depth
    if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
    {
        Context::getContext()->shop->id = $id_shop;
    }
    fixLevelDepth();

    // Fix categories which are more than 1 time in the group
    $sql = 'SELECT count(*) as nb,id_category,id_group FROM `'._DB_PREFIX_.'category_group` GROUP BY id_category ORDER BY nb DESC';
    $res = Db::getInstance()->ExecuteS($sql);
    foreach ($res as $line)
    {
        if ($line['nb'] > 1)
        {
            Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'category_group` WHERE id_category='.(int) $line['id_category'].' AND id_group='.(int) $line['id_group']);
            Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'category_group` (id_category,id_group) VALUES ('.(int) $line['id_category'].','.(int) $line['id_group'].')');
        }
    }

    // Fix products which are several times in one category
    $sql = 'SELECT count(*) as nb,id_category,id_product FROM `'._DB_PREFIX_.'category_product` GROUP BY id_category,id_product ORDER BY nb DESC';
    $res = Db::getInstance()->ExecuteS($sql);
    foreach ($res as $line)
    {
        if ($line['nb'] > 1)
        {
            $sql2 = 'SELECT position FROM `'._DB_PREFIX_.'category_product` where id_category='.(int) $line['id_category'].' AND id_product='.(int) $line['id_product'].' ORDER BY position ASC LIMIT 1';
            $res2 = Db::getInstance()->ExecuteS($sql2);
            Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'category_product` WHERE id_category='.(int) $line['id_category'].' AND id_product='.(int) $line['id_product']);
            Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'category_product` (id_category,id_product,position) VALUES ('.(int) $line['id_category'].','.(int) $line['id_product'].','.(int) $res2[0]['position'].')');
        }
    }

    // Fix categories where id_parent doesn't exist
    if (SCMS)
    {
        $sql = 'SELECT c.id_category FROM `'._DB_PREFIX_.'category` c WHERE c.id_parent!=0 AND c.id_parent NOT IN (SELECT cc.id_category FROM `'._DB_PREFIX_.'category` cc)';
        $res = Db::getInstance()->ExecuteS($sql);
        foreach ($res as $line)
        {
            $sql2 = 'UPDATE `'._DB_PREFIX_.'category` SET id_parent = '.(int) Category::getRootCategory().' WHERE id_category='.(int) $line['id_category'];
            Db::getInstance()->Execute($sql2);
        }
    }

    // Fix Ntree
    Category::regenerateEntireNtree();
    exit($id_shop.'|ok');
