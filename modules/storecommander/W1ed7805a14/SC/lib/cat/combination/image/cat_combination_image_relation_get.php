<?php

    $id_image = (int) Tools::getValue('id_image');

    $sql = '
    SELECT id_product_attribute
    FROM `'._DB_PREFIX_.'product_attribute_image` pai
    WHERE pai.`id_image` = '.(int) $id_image;
    $res = Db::getInstance()->ExecuteS($sql);

    foreach ($res as $val)
    {
        echo $val['id_product_attribute'].',';
    }
