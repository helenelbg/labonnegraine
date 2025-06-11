<?php

    $idlist = Tools::getValue('idlist', '0');
    $id_lang = (int) Tools::getValue('id_lang');
    $cntProducts = count(explode(',', $idlist));
    $used = array();

    $sql = 'SELECT DISTINCT a.id_product_2 
            FROM '._DB_PREFIX_.'accessory a
            WHERE a.id_product_1 IN ('.pInSQL($idlist).')';
    $res = Db::getInstance()->ExecuteS($sql);

    foreach ($res as $row)
    {
        $used[] = $row['id_product_2'];
    }
    echo join(',', $used);
