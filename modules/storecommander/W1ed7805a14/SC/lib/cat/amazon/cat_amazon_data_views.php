<?php

$grids = 'region,id_product,id_product_attribute,disable,send_to_creation,name,attribute_name,bullet_point1,bullet_point2,bullet_point3,bullet_point4,bullet_point5,text,asin1,asin2,asin3,amazon_price,price_rule,price,price_inc_tax,force,fba,fba_value,latency,gift_wrap,gift_message,shipping,shipping_type,browsenode';
if (!empty($alternate_fields_enable))
{
    $grids .= ',alternative_title,alternative_description';
}
