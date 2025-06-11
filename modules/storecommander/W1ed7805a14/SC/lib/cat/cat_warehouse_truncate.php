<?php

$id_lang = (int) Tools::getValue('id_lang');
$id_warehouse = (int) Tools::getValue('id_warehouse');
$history = (int) Tools::getValue('history', 0);

$return = array(
    'type' => 'error',
    'message' => '',
    'debug' => '',
);

if (!empty($id_warehouse))
{
    $sql = 'SELECT id_product
                    FROM `'._DB_PREFIX_."stock`
                    WHERE id_warehouse='".(int) $id_warehouse."'";
    $id_products = Db::getInstance()->executeS($sql);

    if (empty($history))
    {
        Db::getInstance()->execute('
                DELETE FROM `'._DB_PREFIX_.'stock_mvt`
                WHERE `id_stock` IN (SELECT id_stock FROM `'._DB_PREFIX_.'stock` WHERE id_warehouse="'.(int) $id_warehouse.'")');
        Db::getInstance()->execute('
                DELETE FROM `'._DB_PREFIX_.'stock` WHERE id_warehouse="'.(int) $id_warehouse.'"');
    }
    else
    {
        Db::getInstance()->execute('
                UPDATE `'._DB_PREFIX_.'stock` SET  physical_quantity=0, usable_quantity=0 WHERE id_warehouse="'.(int) $id_warehouse.'"');
    }

    foreach ($id_products as $product)
    {
        StockAvailable::synchronize((int) $product['id_product']);
    }

    addToHistory('warehouse', 'truncate', 'warehouse', (int) $id_warehouse, $id_lang, _DB_PREFIX_.'stock', (int) $id_warehouse, false);

    $return = array(
            'type' => 'success',
            'message' => '',
            'debug' => '',
    );
}

echo json_encode($return);
