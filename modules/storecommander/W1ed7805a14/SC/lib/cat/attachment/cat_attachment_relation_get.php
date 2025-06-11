<?php

    $product_list = Tools::getValue('product_list', 'null');
    $id_lang = (int) Tools::getValue('id_lang');
    $used = array();

    if ($product_list != 'null')
    {
        $sql = '    SELECT DISTINCT id_attachment
                FROM '._DB_PREFIX_.'product_attachment
                WHERE id_product IN ('.pInSQL($product_list).')';
        $res = Db::getInstance()->ExecuteS($sql);

        foreach ($res as $row)
        {
            $used[] = $row['id_attachment'];
        }
        echo join(',', $used);
    }
