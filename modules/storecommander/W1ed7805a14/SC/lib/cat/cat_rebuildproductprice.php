<?php

    $rate = 1;

    $product_ids = Tools::getValue('product_ids', null);

    if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
    {
        $selected_shop_id = SCI::getSelectedShop();
        $res = Db::getInstance()->ExecuteS(
            'SELECT pas.`id_product_attribute`, pa.`id_product`, pas.`price`
                FROM `'._DB_PREFIX_.'product_attribute_shop` pas
                    LEFT JOIN '._DB_PREFIX_.'product_attribute pa ON (pa.id_product_attribute=pas.id_product_attribute)
                    '.(SCI::getSelectedShop() > 0 ? 'LEFT JOIN '._DB_PREFIX_.'product p ON (pa.id_product=p.id_product)' : '').'
                WHERE '.(!empty($product_ids) ? 'pas.id_product IN ('.pInSQL($product_ids).') AND ' : '').'
                pas.`price` != 0
                    AND pas.id_shop = '.(SCI::getSelectedShop() > 0 ? (int) SCI::getSelectedShop() : 'p.id_shop_default').'
                    AND pas.`default_on` = 1');
    }
    else
    {
        $res = Db::getInstance()->ExecuteS(
            'SELECT pa.`id_product_attribute`, pa.`id_product`, pa.`price` FROM `'._DB_PREFIX_.'product_attribute` pa
                LEFT JOIN '._DB_PREFIX_.'product p ON (p.id_product=pa.id_product)
                WHERE '.(!empty($product_ids) ? 'pa.id_product IN ('.pInSQL($product_ids).') AND ' : '').'
                pa.`price` != 0
                    AND pa.`default_on` = 1');
    }

    $updated_products = array();
    foreach ($res as $DefAttrib)
    {
        Db::getInstance()->Execute(
            'UPDATE `'._DB_PREFIX_.'product`
                SET `price`=(`price` + '.((float) $DefAttrib['price'] / $rate).')
                WHERE `id_product`='.(int) $DefAttrib['id_product']);
        Db::getInstance()->Execute(
            'UPDATE `'._DB_PREFIX_.'product_attribute`
                SET `price`=(`price` - '.((float) $DefAttrib['price']).')
                WHERE `id_product`='.(int) $DefAttrib['id_product']);

        $updated_products[$DefAttrib['id_product']] = $DefAttrib['id_product'];

        if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
        {
            $shops = SCI::getSelectedShopActionList(false, $DefAttrib['id_product']);
            foreach ($shops as $shop_id)
            {
                Db::getInstance()->Execute(
                'UPDATE `'._DB_PREFIX_.'product_shop`
                SET `price`=(`price` + '.((float) $DefAttrib['price'] / $rate).')
                WHERE `id_product`='.(int) $DefAttrib['id_product'].'
                    AND `id_shop` = "'.(int) $shop_id.'"');

                $combinations_list = '';
                $combinations = Db::getInstance()->ExecuteS(
                        'SELECT `id_product_attribute`
                        FROM `'._DB_PREFIX_.'product_attribute`
                        WHERE id_product = "'.(int) $DefAttrib['id_product'].'"');
                foreach ($combinations as $combination)
                {
                    if (!empty($combinations_list))
                    {
                        $combinations_list .= ',';
                    }
                    $combinations_list .= $combination['id_product_attribute'];
                }

                if (!empty($combinations_list))
                {
                    Db::getInstance()->Execute(
                    'UPDATE `'._DB_PREFIX_.'product_attribute_shop`
                    SET `price`=(`price` - '.((float) $DefAttrib['price']).')
                    WHERE `id_product_attribute` IN ('.pSQL($combinations_list).')
                        AND `id_shop` = "'.(int) $shop_id.'"');
                }
            }
        }
    }
    if (!empty($updated_products))
    {
        ExtensionPMCM::clearFromIdsProduct($updated_products);
    }
    echo 'Ok';
