<?php

$grids = 'id_product,id_product_attribute,sc_active,product_name,combination_name,reference,supplier_reference,ean13,upc,quantity,available_date,minimal_quantity,wholesale_price,pprice,price,taxrate,ppriceextax,priceextax,margin,ecotax,weight';
if (version_compare(_PS_VERSION_, '1.7.7.0', '>='))
{
    $grids = str_replace(',ean13', ',mpn,ean13', $grids);
}
