<?php

    $product_list = Tools::getValue('product_list');
    $id_feature = (int) Tools::getValue('id_feature');
    $id_lang = (int) Tools::getValue('id_lang');
    $used = array();

    $multiple = false;
    if (strpos($product_list, ',') !== false)
    {
        $multiple = true;
    }

    if (!$multiple)
    {
        $sql = '    SELECT DISTINCT id_feature_value
                FROM '._DB_PREFIX_.'feature_product
                WHERE id_product IN ('.pInSQL($product_list).')';
        $res = Db::getInstance()->ExecuteS($sql);
        foreach ($res as $row)
        {
            $used[] = $row['id_feature_value'].'_';
        }
        echo join(',', $used);
    }
    else
    {
        $cntProducts = 0;
        if (!empty($idlist))
        {
            $cntProducts = count(explode(',', $product_list));
        }

        $sql = '
        SELECT *
        FROM `'._DB_PREFIX_.'feature_value` v
        LEFT JOIN `'._DB_PREFIX_.'feature_value_lang` vl ON (v.`id_feature_value` = vl.`id_feature_value` AND vl.`id_lang` = '.(int) $id_lang.')
        WHERE v.`id_feature` = '.(int) $id_feature.' AND (v.`custom` IS NULL OR v.`custom` = 0)
        ORDER BY vl.`value` ASC';
        $feature_values = Db::getInstance()->ExecuteS($sql);
        foreach ($feature_values as $feature_value)
        {
            $color = 'DDDDDD';
            $value = 0;
            $nb_present = 0;

            $sql = '    SELECT id_feature_value
                FROM '._DB_PREFIX_.'feature_product
                WHERE id_product IN ('.pInSQL($product_list).")
                    AND id_feature_value = '".(int) $feature_value['id_feature_value']."'";
            $res = Db::getInstance()->ExecuteS($sql);
            foreach ($res as $row)
            {
                if (!empty($product['id_product']))
                {
                    ++$nb_present;
                }
            }

            if ($nb_present == $cntProducts)
            {
                $value = 1;
                $color = '7777AA';
            }
            elseif ($nb_present < $cntProducts && $nb_present > 0)
            {
                $value = 1;
                $color = '777777';
            }

            if ($value)
            {
                $used[] = $row['id_feature_value'].'_'.$color;
            }
        }
        echo join(',', $used);
    }
