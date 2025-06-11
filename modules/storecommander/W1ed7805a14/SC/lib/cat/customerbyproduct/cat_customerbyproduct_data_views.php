<?php
$grids = 'id_order,id_customer,firstname,lastname,email,product_id,product_attribute_id,product_name,product_quantity,id_order_state,payment,group_name,date_add';

if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) {
    $grids = str_replace(',id_customer,', ',id_customer,cus_lang,', $grids);
    $grids = str_replace(',email,', ',email,company,newsletter,', $grids);
}

if (SCMS)
{
    $grids = str_replace(',id_customer,', ',id_customer,id_shop,shop_name,', $grids);
}