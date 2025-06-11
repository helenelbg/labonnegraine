<?php

$id_lang = (int) Tools::getValue('id_lang');
$id_customer = (int) Tools::getValue('id_customer');
$id_shop = (int) Tools::getValue('id_shop', 1);
$id_cart = (int) Tools::getValue('id_cart');

$action = Tools::getValue('action');

if (!empty($action) && !empty($id_customer) && !empty($id_cart))
{
    switch ($action) {
        case 'convert_price':
            $todo = Tools::getValue('todo', null);
            $data_price = Tools::getValue('data_price', null);
            if (!empty($todo) && !empty($data_price))
            {
                $todo = str_replace(',', '.', $todo);
                foreach ($data_price as $row_id => $price)
                {
                    $new_price = 0;
                    if (strpos($todo, '-') !== false)
                    {
                        $current_todo = str_replace('-', '', $todo);
                        if (strpos($current_todo, '%') !== false)
                        {
                            $curren_todo = str_replace('%', '', $current_todo);
                            $new_price = $price - ($price * $current_todo) / 100;
                        }
                        else
                        {
                            $new_price = $price - $current_todo;
                        }
                    }
                    else
                    {
                        $current_todo = str_replace('+', '', $todo);
                        if (strpos($current_todo, '%') !== false)
                        {
                            $current_todo = str_replace('%', '', $current_todo);
                            $new_price = $price + ($price * $current_todo) / 100;
                        }
                        else
                        {
                            $new_price = $price + $current_todo;
                        }
                    }
                    list($id_product, $id_product_attribute) = explode('_', $row_id);
                    SpecificPrice::deleteByIdCart((int) $id_cart, (int) $id_product, (int) $id_product_attribute);
                    $specific_price = new SpecificPrice();
                    $specific_price->id_cart = (int) $id_cart;
                    $specific_price->id_shop = 0;
                    $specific_price->id_shop_group = 0;
                    $specific_price->id_currency = 0;
                    $specific_price->id_country = 0;
                    $specific_price->id_group = 0;
                    $specific_price->id_customer = (int) $id_customer;
                    $specific_price->id_product = (int) $id_product;
                    $specific_price->id_product_attribute = (int) $id_product_attribute;
                    $specific_price->price = (float) $new_price;
                    $specific_price->from_quantity = 1;
                    $specific_price->reduction = 0;
                    $specific_price->reduction_type = 'amount';
                    $specific_price->from = '0000-00-00 00:00:00';
                    $specific_price->to = '0000-00-00 00:00:00';
                    $specific_price->add();
                }
            }
            break;
        case 'reset_price':
            $row_id = Tools::getValue('selection_id', null);
            if (!empty($row_id))
            {
                $row_id = explode(',', $row_id);
                foreach ($row_id as $row)
                {
                    list($id_product, $id_product_attribute) = explode('_', $row);
                    SpecificPrice::deleteByIdCart($id_cart, $id_product, $id_product_attribute);
                }
            }
            break;
        case 'update_qty':
            $id_product = Tools::getValue('id_product');
            $quantity = (int) Tools::getValue('quantity');

            list($id_product, $id_product_attribute) = explode('_', $id_product);

            if (!empty($id_product))
            {
                if (!empty($quantity) && is_numeric($quantity))
                {
                    $product = new Product($id_product, false, $id_lang, $id_shop);
                    checkQuantity($product, $quantity, $id_product_attribute);

                    $sql = '
                    UPDATE '._DB_PREFIX_.'cart_product SET quantity = "'.(int) $quantity.'"
                    WHERE id_cart = "'.(int) $id_cart.'" AND id_product="'.(int) $id_product.'" AND id_product_attribute="'.(int) $id_product_attribute.'"';
                    Db::getInstance()->Execute($sql);
                }
                elseif (empty($quantity))
                {
                    $sql = '
                    DELETE FROM '._DB_PREFIX_.'cart_product
                    WHERE id_cart = "'.(int) $id_cart.'" AND id_product="'.(int) $id_product.'" AND id_product_attribute="'.(int) $id_product_attribute.'"';
                    Db::getInstance()->Execute($sql);
                }
            }

            break;
        case 'add_product':
            $id_product = Tools::getValue('id_product');
            $quantities = Tools::getValue('quantity', '1');

            if (!empty($id_product))
            {
                $ids = explode(',', $id_product);
                foreach ($ids as $id)
                {
                    list($id_product, $id_product_attribute) = explode('_', $id);
                    if (!empty($id_product))
                    {
                        if (is_array($quantities) && array_key_exists($id, $quantities))
                        {
                            $quantity = (int) $quantities[$id];
                        }
                        else
                        {
                            $quantity = (int) $quantities;
                        }
                        $product = new Product($id_product, false, $id_lang, $id_shop);
                        checkQuantity($product, $quantity, $id_product_attribute);

                        $sql = '
                        SELECT id_cart FROM '._DB_PREFIX_.'cart_product
                        WHERE id_cart = "'.(int) $id_cart.'" AND id_product="'.(int) $id_product.'" AND id_product_attribute="'.(int) $id_product_attribute.'"';
                        $exist = Db::getInstance()->getValue($sql);
                        if (!empty($exist))
                        {
                            $sql = '
                            UPDATE '._DB_PREFIX_.'cart_product SET quantity = quantity+'.(int) $quantity.'
                            WHERE id_cart = "'.(int) $id_cart.'" AND id_product="'.(int) $id_product.'" AND id_product_attribute="'.(int) $id_product_attribute.'"';
                            Db::getInstance()->Execute($sql);
                        }
                        else
                        {
                            $sql = '
                            INSERT INTO '._DB_PREFIX_.'cart_product (id_cart,id_product,id_product_attribute,quantity,date_add'.(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? ',id_shop' : '').')
                            VALUES ("'.(int) $id_cart.'","'.(int) $id_product.'","'.(int) $id_product_attribute.'","'.(int) $quantity.'","'.date('Y-m-d H:i:s').'"'.(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? ',"'.(int) $id_shop.'"' : '').')';
                            Db::getInstance()->Execute($sql);
                        }
                    }
                }
            }

            break;
        case 'remove_product':
            $id_product = Tools::getValue('id_product');

            if (!empty($id_product))
            {
                $ids = explode(',', $id_product);
                foreach ($ids as $id)
                {
                    list($id_product, $id_product_attribute) = explode('_', $id);
                    if (!empty($id_product))
                    {
                        $sql = '
                        DELETE FROM '._DB_PREFIX_.'cart_product
                        WHERE id_cart = "'.(int) $id_cart.'" AND id_product="'.(int) $id_product.'" AND id_product_attribute="'.(int) $id_product_attribute.'"';
                        Db::getInstance()->Execute($sql);
                    }
                }
            }
            break;
        case 'update_address':
            $type = Tools::getValue('type');
            $id_address = (int) Tools::getValue('id_address');

            if (!empty($id_address) && !empty($type))
            {
                $sql = '
                   UPDATE '._DB_PREFIX_.'cart SET id_address_'.pSQL($type).' = "'.(int) $id_address.'"
                   WHERE id_cart = "'.(int) $id_cart.'" ';
                Db::getInstance()->Execute($sql);
            }
            break;
    }
}

function checkQuantity($product, $quantity, $id_product_attribute = null)
{
    global $id_shop;
    $id_product = (int) $product->id;
    if (version_compare(_PS_VERSION_, '1.5.0.5', '>='))
    {
        if (Validate::isLoadedObject($product))
        {
            // By default, the product quantity correspond to the available quantity to sell in the current shop
            $product->quantity = StockAvailable::getQuantityAvailableByProduct($id_product, $id_product_attribute, $id_shop);
            $product->out_of_stock = StockAvailable::outOfStock($id_product, $id_shop);
            $product->depends_on_stock = StockAvailable::dependsOnStock($id_product, $id_shop);
            $id_shop_group = Shop::getGroupFromShop($id_shop);
            $shop_group = new ShopGroup($id_shop_group);
            if ($shop_group->share_stock == 1)
            {
                $product->advanced_stock_management = Db::getInstance()->getValue('SELECT `advanced_stock_management`
                                                                                        FROM '._DB_PREFIX_.'product_shop
                                                                                        WHERE id_product='.(int) $id_product.'
                                                                                        AND id_shop='.(int) $id_shop);
            }
        }
    }
    $product_qty = $product->quantity;
    if (Pack::isPack($id_product))
    {
        $product_qty = Pack::getQuantity($id_product, $id_product_attribute);
    }
    if (!SCI::isAvailableWhenOutOfStockByShop((int) $product->out_of_stock, (int) $id_shop) && $quantity > $product_qty)
    {
        exit(_l('You do not have enough available quantity.').' '._l('Stock available').':'.$product_qty);
    }
    if (!empty($id_product_attribute))
    {
        if (version_compare(_PS_VERSION_, '8.0.0', '>='))
        {
            $minimal_quantity = (int) ProductAttribute::getAttributeMinimalQty($id_product_attribute);
        }
        else
        {
            $minimal_quantity = (int) Attribute::getAttributeMinimalQty($id_product_attribute);
        }
    }
    else
    {
        $minimal_quantity = (int) $product->minimal_quantity;
    }
    if ($quantity < $minimal_quantity && $minimal_quantity > 1)
    {
        exit(_l('You chosen a quantity under minimal quantity for this product.').' '._l('Minimum quantity').':'.$minimal_quantity);
    }
}
