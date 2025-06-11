<?php

$id_lang = (int) Tools::getValue('id_lang');
$id_warehouse = (int) Tools::getValue('id_warehouse');

$return = array(
    'type' => 'error',
    'message' => '',
    'debug' => '',
);

if (!empty($id_warehouse))
{
    $res = Db::getInstance()->executeS('SELECT w.id_product FROM '._DB_PREFIX_.'warehouse_product_location w WHERE w.id_warehouse = '.(int) $id_warehouse);

    $ids = array();
    foreach ($res as $row)
    {
        $ids[] = $row['id_product'];
    }

    SCI::synchronizeArrayOfProducts($ids);

    $return = array(
            'type' => 'success',
            'message' => '',
            'debug' => '',
    );
}

echo json_encode($return);
