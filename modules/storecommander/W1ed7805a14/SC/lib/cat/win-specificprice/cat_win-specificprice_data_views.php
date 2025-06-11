<?php

$grids = 'id_specific_price,id_product,id_product_attribute,reference,name,manufacturer,supplier,id_group,from_quantity,price,reduction_price,reduction_percent,margin_wt_amount_after_reduction,margin_wt_percent_after_reduction,margin_after_reduction,price_with_reduction_tax_excl,price_with_reduction_tax_incl,from,to,from_num,to_num,id_country,id_currency,on_sale';

if (SCMS)
{
    $grids = 'id_specific_price,id_product,id_product_attribute,reference,name,manufacturer,supplier,shop_id,id_shop,id_shop_group,id_group,from_quantity,price,reduction_price,reduction_percent,margin_wt_amount_after_reduction,margin_wt_percent_after_reduction,margin_after_reduction,price_with_reduction_tax_excl,price_with_reduction_tax_incl,from,to,from_num,to_num,id_country,id_currency,on_sale';
}

if (SCMS && version_compare(_PS_VERSION_, '1.6.0.11', '>='))
{
    $grids = 'id_specific_price,id_product,id_product_attribute,reference,name,manufacturer,supplier,shop_id,id_shop,id_shop_group,id_group,from_quantity,price,reduction_tax,reduction_price,reduction_percent,margin_wt_amount_after_reduction,margin_wt_percent_after_reduction,margin_after_reduction,price_with_reduction_tax_excl,price_with_reduction_tax_incl,from,to,from_num,to_num,id_country,id_currency,on_sale,id_customer';
}
elseif (version_compare(_PS_VERSION_, '1.6.0.11', '>='))
{
    $grids = 'id_specific_price,id_product,id_product_attribute,reference,name,manufacturer,supplier,id_group,from_quantity,price,reduction_tax,reduction_price,reduction_percent,margin_wt_amount_after_reduction,margin_wt_percent_after_reduction,margin_after_reduction,price_with_reduction_tax_excl,price_with_reduction_tax_incl,from,to,from_num,to_num,id_country,id_currency,on_sale,id_customer';
}
elseif (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
{
    $grids = 'id_specific_price,id_product,id_product_attribute,reference,name,manufacturer,supplier,id_group,from_quantity,price,reduction_tax,reduction_price,reduction_percent,margin_wt_amount_after_reduction,margin_wt_percent_after_reduction,margin_after_reduction,price_with_reduction_tax_excl,price_with_reduction_tax_incl,from,to,from_num,to_num,id_country,id_currency,on_sale,id_customer';
}
