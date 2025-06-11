<?php

    $product_list = Tools::getValue('product_list');
    $id_lang = (int) Tools::getValue('id_lang');
    $used = array();

    if (!empty($product_list))
    {
        $sql = 'SELECT DISTINCT id_tag
            FROM '._DB_PREFIX_.'product_tag
            WHERE id_product IN ('.pInSQL($product_list).')';
        $res = Db::getInstance()->ExecuteS($sql);
        foreach ($res as $row)
        {
            $used[] = $row['id_tag'];
        }
    }
    echo join(',', $used);
