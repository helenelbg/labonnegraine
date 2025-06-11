<?php

$grids = 'id_specific_price,id_product,id_product_attribute,reference,name,id_group,from_quantity,,price,price_exl_tax,price_inc_tax,reduction,price_with_reduction_tax_excl,price_with_reduction_tax_incl,from,to,id_country,id_currency';

if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
{
    $grids = 'id_specific_price,id_product,id_product_attribute,reference,name,id_group,from_quantity,price,price_exl_tax,price_inc_tax,reduction,reduction_tax,price_with_reduction_tax_excl,price_with_reduction_tax_incl,from,to,id_country,id_currency,id_specific_price_rule,id_customer';
}

if (SCMS)
{
    $grids = str_replace(',id_group,', ',id_shop,id_shop_group,id_group,', $grids);
}
