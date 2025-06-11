<?php

    $grids = array(
        'grid_light' => 'id_customer,id_gender,'.(_s('CUS_USE_COMPANY_FIELDS') ? 'company,' : '').'firstname,lastname,email,active,newsletter,optin,cart_lang,date_add,date_connection',
        'grid_large' => 'id_customer,id_gender,'.(_s('CUS_USE_COMPANY_FIELDS') ? 'company,siret,ape,' : '').'firstname,lastname,email,birthday,active,newsletter,optin,cart_lang,date_add,date_connection,id_default_group,groups,valid_orders,last_delivery_address',
        'grid_address' => 'id_customer,id_address,firstname,lastname,email,dni,'.(_s('CUS_USE_COMPANY_FIELDS') ? 'company,' : '').'address1,address2,postcode,city,id_state,id_country,phone,phone_mobile,invoice,delivery',
        'grid_convert' => 'id_customer,id_gender,'.(_s('CUS_USE_COMPANY_FIELDS') ? 'company,' : '').'firstname,lastname,email,id_default_group,groups,valid_orders,total_valid_orders,last_date_order,nb_cart_product,total_cart_product,last_date_cart',
    );

if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
{
    $grids = array(
            'grid_light' => 'id_customer,'.(SCMS ? 'id_shop,shop_name,' : '').'id_gender,'.(_s('CUS_USE_COMPANY_FIELDS') ? 'company,' : '').'firstname,lastname,email,active,newsletter,optin,cart_lang,date_add,date_connection',
            'grid_large' => 'id_customer,'.(SCMS ? 'id_shop,shop_name,' : '').'id_gender,'.(_s('CUS_USE_COMPANY_FIELDS') ? 'company,siret,ape,' : '').'firstname,lastname,email,birthday,active,newsletter,optin,cart_lang,date_add,date_connection,id_default_group,groups,note,valid_orders,last_delivery_address',
            'grid_address' => 'id_customer,id_address,'.(SCMS ? 'id_shop,shop_name,' : '').'firstname,lastname,email,dni,vat_number,'.(_s('CUS_USE_COMPANY_FIELDS') ? 'company,' : '').'address1,address2,postcode,city,id_state,id_country,phone,phone_mobile,invoice,delivery',
            'grid_convert' => 'id_customer,'.(SCMS ? 'id_shop,shop_name,' : '').'id_gender,'.(_s('CUS_USE_COMPANY_FIELDS') ? 'company,' : '').'firstname,lastname,email,id_default_group,groups,valid_orders,total_valid_orders,last_date_order,nb_cart_product,total_cart_product,last_date_cart',
    );
}

if (version_compare(_PS_VERSION_, '1.5.4.0', '>='))
{
    $grids = array(
            'grid_light' => 'id_customer,'.(SCMS ? 'id_shop,shop_name,' : '').'id_gender,'.(_s('CUS_USE_COMPANY_FIELDS') ? 'company,' : '').'firstname,lastname,email,active,newsletter,optin,id_lang,date_add,date_connection',
            'grid_large' => 'id_customer,'.(SCMS ? 'id_shop,shop_name,' : '').'id_gender,'.(_s('CUS_USE_COMPANY_FIELDS') ? 'company,siret,ape,' : '').'firstname,lastname,email,birthday,active,newsletter,optin,id_lang,date_add,date_connection,id_default_group,groups,note,valid_orders,last_delivery_address',
            'grid_address' => 'id_customer,id_address,'.(SCMS ? 'id_shop,shop_name,' : '').'firstname,lastname,email,dni,vat_number,'.(_s('CUS_USE_COMPANY_FIELDS') ? 'company,' : '').'address1,address2,postcode,city,id_state,id_country,phone,phone_mobile,invoice,delivery',
            'grid_convert' => 'id_customer,'.(SCMS ? 'id_shop,shop_name,' : '').'id_gender,'.(_s('CUS_USE_COMPANY_FIELDS') ? 'company,' : '').'firstname,lastname,email,id_default_group,groups,valid_orders,total_valid_orders,last_date_order,nb_cart_product,total_cart_product,last_date_cart',
    );
}
