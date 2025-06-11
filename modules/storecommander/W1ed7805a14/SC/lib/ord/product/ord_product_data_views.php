<?php

$grids = 'id_order_detail,id_order,product_id,product_attribute_id,image,product_name,product_quantity,actual_quantity_in_stock,product_quantity_in_stock,in_stock,product_quantity_refunded,product_quantity_return,product_price,product_ean13,product_upc,product_reference,product_supplier_reference,product_weight';

if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
{
    $grids = str_replace(',product_quantity_return', ',product_quantity_return,original_product_price', $grids);
}

if (version_compare(_PS_VERSION_, '1.7.7.0', '>='))
{
    $grids = str_replace(',product_price', ',product_price,product_mpn', $grids);
    $grids = str_replace(',product_upc', ',product_upc,product_isbn', $grids);
}
