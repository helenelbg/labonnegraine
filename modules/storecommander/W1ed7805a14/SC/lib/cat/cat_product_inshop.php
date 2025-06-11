<?php

$id_product = (int) Tools::getValue('id_product', '0');

$id_shop_default = SCI::getConfigurationValue('PS_SHOP_DEFAULT');

$present = false;

if (!empty($id_product) && !empty($id_shop_default))
{
    $sql2 = 'SELECT id_product
                FROM '._DB_PREFIX_."product_shop
                WHERE id_product = " .(int) $id_product . "
                    AND id_shop = " .(int) $id_shop_default;
    $res2 = Db::getInstance()->ExecuteS($sql2);
    foreach ($res2 as $product)
    {
        if (!empty($product['id_product']))
        {
            $present = $product['id_product'];
        }
    }
}

echo (int) $present;
