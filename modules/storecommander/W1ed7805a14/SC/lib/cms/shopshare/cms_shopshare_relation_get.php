<?php

    $idList = Tools::getValue('idlist', '0');
    $idLang = (int) Tools::getValue('id_lang');
    $cntCMS = count(explode(',', $idList));
    $used = array();

    $multiple = false;
    if (strpos($idList, ',') !== false)
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
        $cms = new CMS((int) $idList);
        foreach ($res as $shop)
        {
            $is_default = 0;

            $used[$shop['id_shop']] = array(0, 0, 'DDDDDD');
            $sql2 = 'SELECT cs.id_cms, c.active
                FROM '._DB_PREFIX_.'cms c
                LEFT JOIN '._DB_PREFIX_.'cms_shop cs ON (cs.id_cms = c.id_cms)
                WHERE c.id_cms IN ('.pInSQL($idList).")
                    AND cs.id_shop = '".(int) $shop['id_shop']."'";
            $res2 = Db::getInstance()->getRow($sql2);
            if (!empty($res2['id_cms']))
            {
                $used[$shop['id_shop']][0] = 1;
            }
        }
    }
    else
    {
        $sql3 = 'SELECT id_shop
                FROM '._DB_PREFIX_.'cms_shop
                WHERE id_cms IN ('.pInSQL($idList).')';
        $res3 = Db::getInstance()->executeS($sql3);

        foreach ($res as $shop)
        {
            $used[$shop['id_shop']] = array(0, 0, 0);
            $nb_present = 0;

            $sql2 = 'SELECT cs.id_cms, c.active
                FROM '._DB_PREFIX_.'cms c
                LEFT JOIN '._DB_PREFIX_.'cms_shop cs ON (cs.id_cms = c.id_cms)
                WHERE c.id_cms IN ('.pInSQL($idList).")
                    AND cs.id_shop = '".(int) $shop['id_shop']."'";
            $res2 = Db::getInstance()->ExecuteS($sql2);
            foreach ($res2 as $cms)
            {
                if (!empty($cms['id_cms']))
                {
                    ++$nb_present;
                }
            }

            if ($nb_present == $cntCMS)
            {
                $used[$shop['id_shop']][0] = 1;
            }
        }
    }
