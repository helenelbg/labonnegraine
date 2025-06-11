<?php

$grids = 'id,name,present,default';

if (!$multiple && !$has_combi)
{
    $grids = 'id,name,present,product_supplier_reference,product_supplier_price_te,id_currency,default';
}
