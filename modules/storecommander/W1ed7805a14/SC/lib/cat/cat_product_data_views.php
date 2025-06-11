<?php

$grids = array(
        'grid_light' => 'id,image,active,position,reference,name,quantity,quantityupdate,minimal_quantity,price,id_tax_rules_group,price_inc_tax,ecotax',
        'grid_large' => 'id,image,reference,name,quantity,quantityupdate,minimal_quantity,wholesale_price,price,id_tax_rules_group,price_inc_tax,ecotax,weight,supplier_reference,id_manufacturer,id_supplier,ean13,upc,location,date_add,reduction_price,price_with_reduction,reduction_percent,price_with_reduction_percent,reduction_from,reduction_to,out_of_stock,available_now,available_later,on_sale,available_for_order,show_price,online_only,condition,position,active',
        'grid_delivery' => 'id,image,reference,name,quantity,quantityupdate,minimal_quantity,additional_shipping_cost,weight,width,height,depth,out_of_stock,available_now,available_later,id_manufacturer,id_supplier,active',
        'grid_price' => 'id,image,reference,name,wholesale_price,price,margin,id_tax_rules_group,price_inc_tax,unit_price_ratio,unit_price_inc_tax,unity,reduction_price,ecotax,discountprice,id_manufacturer,id_supplier,active',
        'grid_discount' => 'id,image,reference,name,wholesale_price,price,id_tax_rules_group,price_inc_tax,ecotax,out_of_stock,on_sale,reduction_price,price_with_reduction,reduction_percent,price_with_reduction_percent,reduction_from,reduction_to,quantity,date_add,id_manufacturer,id_supplier,position,active',
        'grid_seo' => 'id,image,reference,supplier_reference,name,meta_title,meta_description,meta_keywords,link_rewrite,active',
        'grid_reference' => 'id,image,reference,supplier_reference,name,available_for_order,show_price,online_only,condition,date_upd,date_add,id_manufacturer,id_supplier,ean13,upc,location,id_color_default,combinations,features,categories,active',
        'grid_description' => 'id,image,reference,name,description_short,description,active',
        'grid_combination_price' => 'id,reference,name,wholesale_price,price,id_tax_rules_group,price_inc_tax,ecotax,id_manufacturer,id_supplier,active',
        'grid_discount_2' => 'id,image,reference,name,wholesale_price,price,id_tax_rules_group,price_inc_tax,ecotax,out_of_stock,on_sale,reduction_price,reduction_percent,margin_wt_amount_after_reduction,margin_wt_percent_after_reduction,margin_after_reduction,price_wt_with_reduction,price_it_with_reduction,reduction_from,reduction_to,quantity,last_order,date_add,id_manufacturer,id_supplier,position,active',
);
if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
{
    $grids = array(
            'grid_light' => 'id,image,active,position,reference,name,quantity,quantityupdate,minimal_quantity,price,id_tax_rules_group,price_inc_tax,ecotax',
            'grid_large' => 'id,image,reference,name,quantity,quantityupdate,minimal_quantity,wholesale_price,price,id_tax_rules_group,price_inc_tax,ecotax,weight,supplier_reference,id_manufacturer,id_supplier,ean13,upc,location,date_add,reduction_price,price_with_reduction,reduction_percent,price_with_reduction_percent,reduction_from,reduction_to,out_of_stock,available_now,available_later,on_sale,available_for_order,available_date,visibility,show_price,online_only,condition,position,active',
            'grid_delivery' => 'id,image,reference,name,quantity,quantityupdate,minimal_quantity,additional_shipping_cost,weight,width,height,depth,out_of_stock,available_now,available_later,id_manufacturer,id_supplier,active',
            'grid_price' => 'id,image,reference,name,wholesale_price,price,margin,id_tax_rules_group,price_inc_tax,unit_price_ratio,unit_price_inc_tax,unity,reduction_price,ecotax,discountprice,id_manufacturer,id_supplier,active',
            'grid_discount' => 'id,image,reference,name,wholesale_price,price,id_tax_rules_group,price_inc_tax,ecotax,out_of_stock,on_sale,reduction_price,price_with_reduction,reduction_percent,price_with_reduction_percent,reduction_from,reduction_to,quantity,date_add,id_manufacturer,id_supplier,position,active',
            'grid_seo' => 'id,image,reference,supplier_reference,name,meta_title,meta_description,meta_keywords,link_rewrite,redirect_type,id_product_redirected,active',
            'grid_reference' => 'id,image,reference,supplier_reference,name,available_for_order,available_date,visibility,show_price,online_only,condition,date_upd,date_add,id_manufacturer,id_supplier,ean13,upc,location,combinations,features,categories,active',
            'grid_description' => 'id,image,reference,name,description_short,description,active',
            'grid_combination_price' => 'id,reference,name,wholesale_price,price,id_tax_rules_group,price_inc_tax,ecotax,id_manufacturer,id_supplier,active',
            'grid_discount_2' => 'id,image,reference,name,wholesale_price,price,id_tax_rules_group,price_inc_tax,ecotax,out_of_stock,on_sale,reduction_price,reduction_percent,margin_wt_amount_after_reduction,margin_wt_percent_after_reduction,margin_after_reduction,price_wt_with_reduction,price_it_with_reduction,reduction_from,reduction_to,quantity,last_order,date_add,id_manufacturer,id_supplier,position,active',
    );
}
if (SCAS)
{
    $grids = array(
            'grid_light' => 'id,image,active,position,reference,name,quantity,advanced_stock_management,quantity_physical,quantity_usable,quantity_real,quantityupdate,minimal_quantity,price,id_tax_rules_group,price_inc_tax,ecotax',
            'grid_large' => 'id,image,reference,name,quantity,advanced_stock_management,quantity_physical,quantity_usable,quantity_real,quantityupdate,minimal_quantity,wholesale_price,price,id_tax_rules_group,price_inc_tax,ecotax,weight,supplier_reference,id_manufacturer,id_supplier,ean13,upc,location,date_add,reduction_price,price_with_reduction,reduction_percent,price_with_reduction_percent,reduction_from,reduction_to,out_of_stock,available_now,available_later,on_sale,available_for_order,available_date,visibility,show_price,online_only,condition,position,active',
            'grid_delivery' => 'id,image,reference,name,quantity,advanced_stock_management,quantity_physical,quantity_usable,quantity_real,quantityupdate,minimal_quantity,additional_shipping_cost,weight,width,height,depth,out_of_stock,available_now,available_later,id_manufacturer,id_supplier,active',
            'grid_price' => 'id,image,reference,name,wholesale_price,price,margin,id_tax_rules_group,price_inc_tax,unit_price_ratio,unit_price_inc_tax,unity,reduction_price,ecotax,discountprice,id_manufacturer,id_supplier,active',
            'grid_discount' => 'id,image,reference,name,wholesale_price,price,id_tax_rules_group,price_inc_tax,ecotax,out_of_stock,on_sale,reduction_price,price_with_reduction,reduction_percent,price_with_reduction_percent,reduction_from,reduction_to,quantity,advanced_stock_management,quantity_physical,quantity_usable,quantity_real,quantityupdate,date_add,id_manufacturer,id_supplier,position,active',
            'grid_seo' => 'id,image,reference,supplier_reference,name,meta_title,meta_description,meta_keywords,link_rewrite,redirect_type,id_product_redirected,active',
            'grid_reference' => 'id,image,reference,supplier_reference,name,available_for_order,available_date,visibility,show_price,online_only,condition,date_upd,date_add,id_manufacturer,id_supplier,ean13,upc,location,combinations,features,categories,active',
            'grid_description' => 'id,image,reference,name,description_short,description,active',
            'grid_combination_price' => 'id,reference,name,wholesale_price,price,id_tax_rules_group,price_inc_tax,ecotax,id_manufacturer,id_supplier,active',
            'grid_discount_2' => 'id,image,reference,name,wholesale_price,price,id_tax_rules_group,price_inc_tax,ecotax,out_of_stock,on_sale,reduction_price,reduction_percent,margin_wt_amount_after_reduction,margin_wt_percent_after_reduction,margin_after_reduction,price_wt_with_reduction,price_it_with_reduction,reduction_from,reduction_to,quantity,advanced_stock_management,quantity_physical,quantity_usable,quantity_real,quantityupdate,last_order,date_add,id_manufacturer,id_supplier,position,active',
        );
}

if (version_compare(_PS_VERSION_, '1.7.1.0', '>='))
{
    $grids = array(
        'grid_light' => 'id,image,active,position,reference,name,quantity,quantityupdate,minimal_quantity,price,id_tax_rules_group,price_inc_tax,ecotax',
        'grid_large' => 'id,image,reference,name,quantity,quantityupdate,minimal_quantity,wholesale_price,price,id_tax_rules_group,price_inc_tax,ecotax,weight,supplier_reference,id_manufacturer,id_supplier,ean13,upc,isbn,location,date_add,reduction_price,price_with_reduction,reduction_percent,price_with_reduction_percent,reduction_from,reduction_to,out_of_stock,available_now,available_later,on_sale,available_for_order,available_date,visibility,show_price,online_only,condition,show_condition,position,active',
        'grid_delivery' => 'id,image,reference,name,quantity,quantityupdate,minimal_quantity,additional_shipping_cost,weight,width,height,depth,out_of_stock,available_now,available_later,id_manufacturer,id_supplier,active',
        'grid_price' => 'id,image,reference,name,wholesale_price,price,margin,id_tax_rules_group,price_inc_tax,unit_price_ratio,unit_price_inc_tax,unity,reduction_price,ecotax,discountprice,id_manufacturer,id_supplier,active',
        'grid_discount' => 'id,image,reference,name,wholesale_price,price,id_tax_rules_group,price_inc_tax,ecotax,out_of_stock,on_sale,reduction_price,price_with_reduction,reduction_percent,price_with_reduction_percent,reduction_from,reduction_to,quantity,date_add,id_manufacturer,id_supplier,position,active',
        'grid_seo' => 'id,image,reference,supplier_reference,name,meta_title,meta_description,meta_keywords,link_rewrite,redirect_type,id_type_redirected,active',
        'grid_reference' => 'id,image,reference,supplier_reference,name,available_for_order,available_date,visibility,show_price,online_only,condition,show_condition,date_upd,date_add,id_manufacturer,id_supplier,ean13,upc,isbn,location,combinations,features,categories,active',
        'grid_description' => 'id,image,reference,name,description_short,description,active',
        'grid_combination_price' => 'id,reference,name,wholesale_price,price,id_tax_rules_group,price_inc_tax,ecotax,id_manufacturer,id_supplier,active',
        'grid_discount_2' => 'id,image,reference,name,wholesale_price,price,id_tax_rules_group,price_inc_tax,ecotax,out_of_stock,on_sale,reduction_price,reduction_percent,margin_wt_amount_after_reduction,margin_wt_percent_after_reduction,margin_after_reduction,price_wt_with_reduction,price_it_with_reduction,reduction_from,reduction_to,quantity,last_order,date_add,id_manufacturer,id_supplier,position,active',
    );
}

if (version_compare(_PS_VERSION_, '1.7.3.0', '>='))
{
    $grids['grid_delivery'] = 'id,image,reference,name,quantity,quantityupdate,minimal_quantity,additional_shipping_cost,weight,width,height,depth,out_of_stock,low_stock_alert,low_stock_threshold,additional_delivery_times,delivery_in_stock,delivery_out_stock,available_now,available_later,id_manufacturer,id_supplier,active';

    if (SCAS)
    {
        $grids['grid_delivery'] = 'id,image,reference,name,quantity,advanced_stock_management,quantity_physical,quantity_usable,quantity_real,quantityupdate,minimal_quantity,additional_shipping_cost,weight,width,height,depth,out_of_stock,low_stock_alert,low_stock_threshold,additional_delivery_times,delivery_in_stock,delivery_out_stock,available_now,available_later,id_manufacturer,id_supplier,active';
    }
}
if (version_compare(_PS_VERSION_, '1.7.5.0', '>='))
{
    $grids['grid_light'] = str_replace(',quantity,', ',soft_qty_physical,soft_qty_reserved,quantity,', $grids['grid_light']);
    $grids['grid_large'] = str_replace(',quantity,', ',soft_qty_physical,soft_qty_reserved,quantity,', $grids['grid_large']);
    $grids['grid_large'] = str_replace(',location,', ',location,location_new,', $grids['grid_large']);
    $grids['grid_delivery'] = str_replace(',quantity,', ',soft_qty_physical,soft_qty_reserved,quantity,', $grids['grid_delivery']);
    $grids['grid_reference'] = str_replace(',location,', ',location,location_new,', $grids['grid_reference']);
}

if (defined('SC_UkooProductCompat_ACTIVE') && SC_UkooProductCompat_ACTIVE == 1 && SCI::moduleIsInstalled('ukoocompat'))
{
    $grids['grid_reference'] .= ',nb_compatibilities,compatibilities';
}
if (defined('SC_FeedBiz_ACTIVE') && SC_FeedBiz_ACTIVE == 1 && SCI::moduleIsInstalled('feedbiz'))
{
    $market_place_allowed = SCI::getFeedBizAllowedMarketPlace();
    $add_to_grid = array();
    foreach ($market_place_allowed as $marketplace => $value)
    {
        if (!empty($value))
        {
            $add_to_grid[] = 'feedbiz_'.$marketplace;
        }
    }
    $to_the_grid = ',feedbiz'.(!empty($add_to_grid) ? ','.implode(',', $add_to_grid) : '');
    $grids['grid_light'] .= $to_the_grid;
    $grids['grid_large'] .= $to_the_grid;
    $grids['grid_reference'] .= $to_the_grid;
    $grids['grid_feedbiz_option'] = 'id,name,fpo_enable_on_product,fpo_enable_on_attribute,fpo_force,fpo_disable,fpo_price,fpo_shipping,fpo_text';
    if ($market_place_allowed['amazon'])
    {
        $grids['grid_feedbiz_amazon_option'] = 'id,name,fpao_enable_on_product,fpao_enable_on_attribute,fpao_force,fpao_disable,fpao_price,fpao_shipping,fpao_text,fpao_nopexport,fpao_noqexport,fpao_fba,fpao_fba_value,fpao_asin1,fpao_asin2,fpao_asin3,fpao_bullet_point1,fpao_bullet_point2,fpao_bullet_point3,fpao_bullet_point4,fpao_bullet_point5,fpao_shipping_type,fpao_gift_wrap,fpao_gift_message,fpao_browsenode,fpao_repricing_min,fpao_repricing_max,fpao_repricing_gap,fpao_shipping_group';
    }
    if ($market_place_allowed['cdiscount'])
    {
        $grids['grid_feedbiz_cdiscount_option'] = 'id,name,fpco_enable_on_product,fpco_enable_on_attribute,fpco_force,fpco_disable,fpco_price,fpco_price_up,fpco_price_down,fpco_shipping,fpco_shipping_delay,fpco_clogistique,fpco_valueadded,fpco_text';
    }
}
if (defined('SC_Amazon_ACTIVE') && SC_Amazon_ACTIVE == 1 && SCI::moduleIsInstalled('amazon'))
{
    $grids['grid_light'] .= ',amazon';
    $grids['grid_large'] .= ',amazon';
    $grids['grid_reference'] .= ',amazon';
}
if (defined('SC_Cdiscount_ACTIVE') && SC_Cdiscount_ACTIVE == 1 && SCI::moduleIsInstalled('cdiscount'))
{
    $grids['grid_light'] .= ',cdiscount';
    $grids['grid_large'] .= ',cdiscount';
    $grids['grid_reference'] .= ',cdiscount';
}

// PACK
if (version_compare(_PS_VERSION_, '1.6.1.14', '>='))
{
    $grids['grid_large'] = str_replace(',reference,', ',cache_is_pack,reference,', $grids['grid_large']);

    $grids['grid_pack'] = $grids['grid_light'];
    $grids['grid_pack'] = str_replace(',name,', ',name,cache_is_pack,pack_stock_type,', $grids['grid_pack']);
}

## MPN
if (version_compare(_PS_VERSION_, '1.7.7.0', '>='))
{
    $grids['grid_large'] = str_replace(',ean13,', ',mpn,ean13,', $grids['grid_large']);
    $grids['grid_reference'] = str_replace(',ean13,', ',mpn,ean13,', $grids['grid_reference']);
}

## Product Type
if (version_compare(_PS_VERSION_, '1.7.8.0', '>='))
{
    $grids['grid_light'] .= ',product_type';
    $grids['grid_large'] .= ',product_type';
}
