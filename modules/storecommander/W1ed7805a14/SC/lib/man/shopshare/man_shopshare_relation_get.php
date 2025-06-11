<?php

    $idlist = Tools::getValue('idlist', '0');
    $id_lang = (int) Tools::getValue('id_lang');
    $cntManufacturers = count(explode(',', $idlist));
    $used = array();

    $multiple = false;
    if (strpos($idlist, ',') !== false)
    {
        $multiple = true;
    }

    $sql = 'SELECT *
                FROM '._DB_PREFIX_."shop
                WHERE deleted != '1'
                ORDER BY id_shop_group ASC, name ASC";
    $res = Db::getInstance()->ExecuteS($sql);

    if (!$multiple)
    {
        $manufacturer = new Manufacturer((int) $idlist);
        foreach ($res as $shop)
        {
            $sql2 = 'SELECT id_manufacturer
                FROM '._DB_PREFIX_.'manufacturer_shop
                WHERE id_manufacturer IN ('.pInSQL($idlist).")
                    AND id_shop = '".$shop['id_shop']."'";
            $res2 = Db::getInstance()->getRow($sql2);
            if (!empty($res2['id_manufacturer']))
            {
                $used[$shop['id_shop']][0] = 1;
            }
        }
    }
    else
    {
        foreach ($res as $shop)
        {
            $used[$shop['id_shop']] = array(0, 0, 0);
            $nb_present = 0;

            $sql2 = 'SELECT id_manufacturer, active
                FROM '._DB_PREFIX_.'manufacturer_shop
                WHERE id_manufacturer IN ('.pInSQL($idlist).")
                    AND id_shop = '".$shop['id_shop']."'";
            $res2 = Db::getInstance()->ExecuteS($sql2);
            foreach ($res2 as $manufacturer)
            {
                if (!empty($manufacturer['id_manufacturer']))
                {
                    ++$nb_present;
                }
            }

            if ($nb_present == $cntManufacturers)
            {
                $used[$shop['id_shop']][0] = 1;
            }
        }
    }

    $i = 0;
    foreach ($used as $id_shop => $values)
    {
        if ($i > 0)
        {
            echo ';';
        }
        echo $id_shop.','.$values[0];
        ++$i;
    }
