<?php

    $id_image = (int) Tools::getValue('id_image');

    $sql = '
    SELECT pa.id_product,pai.id_product_attribute
    FROM `'._DB_PREFIX_.'product_attribute_image` pai
    LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa ON pa.id_product_attribute = pai.id_product_attribute
    WHERE pai.`id_image` = '.(int) $id_image;
    $res = Db::getInstance()->ExecuteS($sql);

    $return = array();

    foreach ($res as $val)
    {
        $return[] = (int) $val['id_product'].'_'.$val['id_product_attribute'];
    }

    echo implode(',', $return);
