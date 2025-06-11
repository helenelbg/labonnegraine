<?php

$grids = 'id_partner,active,customer_id,id_lang,email,name,company,code,percent_comm,coupon_code,coupon_percent_comm,mode,duration,quantity,total_gained,total_payments,total_to_pay,total_invoiced,note,ppa,ppa_date,date_add';

if (SCMS)
{
    $grids = 'id_partner,id_shop,active,customer_id,id_lang,email,name,company,code,percent_comm,coupon_code,coupon_percent_comm,mode,duration,quantity,total_gained,total_payments,total_to_pay,total_invoiced,note,ppa,ppa_date,date_add';
}
