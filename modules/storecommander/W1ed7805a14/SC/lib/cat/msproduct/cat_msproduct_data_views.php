<?php

$grids = 'id_product,reference,supplier_reference,name,id_shop,link_rewrite,active,visibility,on_sale,online_only,show_price,quantity,minimal_quantity,ecotax,wholesale_price,price,id_tax_rules_group,price_inc_tax,margin,unity,unit_price_ratio,additional_shipping_cost,available_for_order,available_date,condition,available_now,available_later';
if (SCAS)
{
    $grids = str_replace(',quantity,', ',advanced_stock_management,quantity,', $grids);
}
if (version_compare(_PS_VERSION_, '1.7.0.0', '>='))
{
    $grids = str_replace(',condition,', ',condition,show_condition,', $grids);
}
