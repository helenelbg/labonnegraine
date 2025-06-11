<?php
ini_set('memory_limit', '512M');
ini_set('max_execution_time', '10000');
    require(dirname(__FILE__).'/../config/config.inc.php');

    /*******************************************************************************
    ********** Supression des Stats appelé par Cron *******************************
    *******************************************************************************/

    // nbr de jours à conserver
    $days=30; 	//connections etc ...
    $days_panier=180; 	//connections etc ...
    $day_page=30; 	//page viewed

    // nbr de guest à conserver
    $nbGuest=50000;
    $interval=$days.' DAY';
    $interval_panier=$days_panier.' DAY';

    //-1- ps_cart delete
    $sql = 'SELECT * FROM `'._DB_PREFIX_.'cart`
    WHERE date_upd < DATE_SUB(NOW(), INTERVAL '.$interval_panier.') AND id_cart NOT IN (SELECT id_cart FROM `'._DB_PREFIX_.'orders`) ORDER BY date_upd DESC';
    $res=Db::getInstance()->ExecuteS($sql);
    /*if(!$res)
      die('error select ps_cart for '.$days_panier.' day(s)');*/

    foreach($res as $cartselect)
    {
      $cartd = new Cart($cartselect['id_cart']);
      $delete = $cartd->delete();
    }

    //0- ps_cart_rule delete
    $sql = 'SELECT * FROM `'._DB_PREFIX_.'cart_rule`
    WHERE date_to < DATE_SUB(NOW(), INTERVAL '.$interval.') ORDER BY date_to DESC';
    $res=Db::getInstance()->ExecuteS($sql);
    if(!$res)
      die('error select ps_cart_rule for '.$days.' day(s)');

    foreach($res as $rule)
    {
      $cartRule = new CartRule($rule['id_cart_rule']);
      $delete = $cartRule->delete();
    }

    //1- ps_connections_pages delete
    $res=Db::getInstance()->Execute('
    DELETE FROM `'._DB_PREFIX_.'connections_page`
    WHERE time_start < DATE_SUB(NOW(), INTERVAL '.$interval.')');
    if(!$res)
    	die('error delete ps_connections_pages for '.$days.' day(s)');


      //2- ps_connections delete
      $res=Db::getInstance()->Execute('
      DELETE FROM `'._DB_PREFIX_.'connections`
      WHERE date_add < DATE_SUB(NOW(), INTERVAL '.$interval.')');
      if(!$res)
      	die('error delete ps_connections for '.$days.' day(s)');

        //2.5- ps_pagenotfound delete
        $res=Db::getInstance()->Execute('
        DELETE FROM `'._DB_PREFIX_.'pagenotfound`
        WHERE date_add < DATE_SUB(NOW(), INTERVAL '.$interval.')');
        if(!$res)
          die('error delete ps_pagenotfound for '.$days.' day(s)');

    //3- ps_connections_source delete
    $res=Db::getInstance()->Execute('
    DELETE FROM `'._DB_PREFIX_.'connections_source`
    WHERE date_add < DATE_SUB(NOW(), INTERVAL '.$interval.')');
    if(!$res)
    	die('error delete ps_connections_source for '.$days.' day(s)');

    //4- ps_page_viewed delete
    $currentRange=DateRange::getCurrentRange();
    $endRange = $currentRange-$day_page;
    $res=Db::getInstance()->Execute('
    DELETE FROM `'._DB_PREFIX_.'page_viewed`
    WHERE id_date_range < '.$endRange);
    if(!$res)
    	die('error delete ps_page_viewed for '.$endRange.' date_range');

    //5- ps_guest delete
    $sql="SELECT `id_guest` FROM `"._DB_PREFIX_."guest` ORDER BY `id_guest` DESC LIMIT 0, 1";
    $res = Db::getInstance()->ExecuteS($sql);
    if(!$res)
    	die('error select highest '._DB_PREFIX_.'guest');
    $currentGuest=$res[0]['id_guest'];
    $endGuest = $currentGuest-$nbGuest;
    $res=Db::getInstance()->Execute('
    DELETE FROM `'._DB_PREFIX_.'guest`
    WHERE id_guest < '.$endGuest);
    if(!$res)
    	die('error delete ps_guest for '.$endGuest.' endGuest');

    $sql='OPTIMIZE TABLE  `'._DB_PREFIX_.'pagenotfound`, `'._DB_PREFIX_.'cart_rule_combination`, `'._DB_PREFIX_.'connections_page`, `'._DB_PREFIX_.'connections`, `'._DB_PREFIX_.'cart_product`, `'._DB_PREFIX_.'connections_source`, `'._DB_PREFIX_.'order_detail`, `'._DB_PREFIX_.'order_detail_tax`, `'._DB_PREFIX_.'statssearch`, `'._DB_PREFIX_.'order_history`, `'._DB_PREFIX_.'search_index`, `'._DB_PREFIX_.'cart`, `'._DB_PREFIX_.'page_viewed`, `'._DB_PREFIX_.'newsletter_stats`, `'._DB_PREFIX_.'sekeyword`, `'._DB_PREFIX_.'address`, `'._DB_PREFIX_.'guest`, `'._DB_PREFIX_.'discount_category`, `'._DB_PREFIX_.'orders`, `'._DB_PREFIX_.'order_carrier`, `'._DB_PREFIX_.'message`, `'._DB_PREFIX_.'order_invoice`, `'._DB_PREFIX_.'order_payment`, `'._DB_PREFIX_.'order_invoice_payment`, `'._DB_PREFIX_.'cart_rule_product_rule_value`, `'._DB_PREFIX_.'product_carrier`, `'._DB_PREFIX_.'order_invoice_tax`, `'._DB_PREFIX_.'inventaire`, `'._DB_PREFIX_.'customer_group`, `'._DB_PREFIX_.'customer`, `'._DB_PREFIX_.'socolissimo_delivery_info`, `'._DB_PREFIX_.'sonice_etq_label`, `'._DB_PREFIX_.'customer_05032018`, `'._DB_PREFIX_.'sonice_etq_session_detail`, `'._DB_PREFIX_.'soflexibilite_carrier_fee_check`, `'._DB_PREFIX_.'paypal_order`, `'._DB_PREFIX_.'payline_token`, `'._DB_PREFIX_.'log`, `'._DB_PREFIX_.'feature_product`, `'._DB_PREFIX_.'payline_order`, `'._DB_PREFIX_.'cart_cart_rule`, `'._DB_PREFIX_.'order_cart_rule`, `'._DB_PREFIX_.'search_word`, `'._DB_PREFIX_.'delivery`, `'._DB_PREFIX_.'accessory`, `'._DB_PREFIX_.'paypal_customer`, `'._DB_PREFIX_.'product_attribute_combination`, `'._DB_PREFIX_.'stock_available`, `'._DB_PREFIX_.'dpdfrance_shipping`, `'._DB_PREFIX_.'product_attribute_combinationsauve1310`, `'._DB_PREFIX_.'product_attribute_combination_sauve251017`, `'._DB_PREFIX_.'product_tag`, `'._DB_PREFIX_.'category_product`, `'._DB_PREFIX_.'cart_rule_lang`, `'._DB_PREFIX_.'layered_product_attribute`, `'._DB_PREFIX_.'stock_available_sauve251017`, `'._DB_PREFIX_.'stock_availablesauve161017`, `'._DB_PREFIX_.'order_cart_rule.save`, `'._DB_PREFIX_.'product_attribute_shop`, `'._DB_PREFIX_.'feature_value_lang`, `'._DB_PREFIX_.'product_attribute`, `'._DB_PREFIX_.'date_range`, `'._DB_PREFIX_.'product_attribute_shop_sauve251017`, `'._DB_PREFIX_.'image_lang`, `'._DB_PREFIX_.'product_attributesauve1310`, `'._DB_PREFIX_.'product_attribute_sauve251017`, `'._DB_PREFIX_.'sonice_suivicolis`, `'._DB_PREFIX_.'specific_price13112018`, `'._DB_PREFIX_.'page`, `'._DB_PREFIX_.'customized_data`, `'._DB_PREFIX_.'pm_advancedpack_cart_products`, `'._DB_PREFIX_.'cart_rule`, `'._DB_PREFIX_.'product_lang`, `'._DB_PREFIX_.'specific_price`, `'._DB_PREFIX_.'range_weight`, `'._DB_PREFIX_.'feature_value`, `'._DB_PREFIX_.'reassort`, `'._DB_PREFIX_.'mailalert_customer_oos`, `'._DB_PREFIX_.'image`, `'._DB_PREFIX_.'image_shop`, `'._DB_PREFIX_.'customer_message`, `'._DB_PREFIX_.'attribute_lang`, `'._DB_PREFIX_.'inventaire_lots`, `'._DB_PREFIX_.'layered_indexable_attribute_lang_value`, `AW_test_lots`, `'._DB_PREFIX_.'specific_price_251017`, `'._DB_PREFIX_.'customer_thread`, `'._DB_PREFIX_.'specific_pricesauve1310`, `'._DB_PREFIX_.'tag`, `'._DB_PREFIX_.'product_shop`, `'._DB_PREFIX_.'product`, `'._DB_PREFIX_.'product_sale`, `'._DB_PREFIX_.'layered_price_index`, `'._DB_PREFIX_.'referralprogram`, `'._DB_PREFIX_.'specific_price_priority`, `'._DB_PREFIX_.'attribute_impact`, `'._DB_PREFIX_.'aff_product_rates`, `'._DB_PREFIX_.'attribute`, `'._DB_PREFIX_.'attribute_shop`, `'._DB_PREFIX_.'customized_data_sauve_100118`, `'._DB_PREFIX_.'tax_lang`, `'._DB_PREFIX_.'discount_lang`, `'._DB_PREFIX_.'layered_category`, `'._DB_PREFIX_.'layered_friendly_url`, `'._DB_PREFIX_.'configuration`, `'._DB_PREFIX_.'carrier_zone`, `'._DB_PREFIX_.'flashsalespro_items`, `'._DB_PREFIX_.'cart_rule_shop`, `'._DB_PREFIX_.'carrier_group`, `'._DB_PREFIX_.'sonice_etq_session`, `'._DB_PREFIX_.'theme_meta`, `'._DB_PREFIX_.'carrier_lang`, `'._DB_PREFIX_.'alerte`, `'._DB_PREFIX_.'order_slip_detail`, `'._DB_PREFIX_.'tax`, `'._DB_PREFIX_.'buyers_group_order_cart`, `'._DB_PREFIX_.'timezone`, `'._DB_PREFIX_.'discount`, `'._DB_PREFIX_.'module_group`, `'._DB_PREFIX_.'customization`, `flags_saisons_box`, `'._DB_PREFIX_.'aff_tracking`, `'._DB_PREFIX_.'category_group`, `'._DB_PREFIX_.'category_lang_sauve11062018`, `'._DB_PREFIX_.'hook_module`, `'._DB_PREFIX_.'cart_rule_product_rule_group`, `'._DB_PREFIX_.'cart_rule_product_rule`, `'._DB_PREFIX_.'carrier`, `'._DB_PREFIX_.'carrier_shop`, `'._DB_PREFIX_.'carrier_tax_rules_group_shop`, `'._DB_PREFIX_.'fichiers_categories_produits`, `'._DB_PREFIX_.'category_lang`, `'._DB_PREFIX_.'module_access`, `'._DB_PREFIX_.'access`, `'._DB_PREFIX_.'tab_lang`, `'._DB_PREFIX_.'hook_module_exceptions`, `'._DB_PREFIX_.'redirect_newsletter`, `'._DB_PREFIX_.'sonice_etq_hscode`, `'._DB_PREFIX_.'aff_category_rates`, `'._DB_PREFIX_.'hook`, `'._DB_PREFIX_.'newsletter`, `'._DB_PREFIX_.'pm_advancedtopmenu_elements_lang`, `'._DB_PREFIX_.'category`, `'._DB_PREFIX_.'category_shop`, `'._DB_PREFIX_.'module`, `'._DB_PREFIX_.'tab`, `'._DB_PREFIX_.'module_country`, `'._DB_PREFIX_.'range_price`, `'._DB_PREFIX_.'attachment_lang`, `'._DB_PREFIX_.'module_shop`, `'._DB_PREFIX_.'order_slip`, `'._DB_PREFIX_.'meta_lang`, `'._DB_PREFIX_.'page_type`, `'._DB_PREFIX_.'hook_alias`, `'._DB_PREFIX_.'giftcardtemplate_shop`, `'._DB_PREFIX_.'buyers_group_customers`, `'._DB_PREFIX_.'pm_advancedtopmenu_elements`, `'._DB_PREFIX_.'customization_field_lang`, `'._DB_PREFIX_.'cms_lang`, `'._DB_PREFIX_.'pm_advancedpack_products`, `'._DB_PREFIX_.'configuration_kpi`, `'._DB_PREFIX_.'configuration_lang`, `'._DB_PREFIX_.'order_state_lang`, `'._DB_PREFIX_.'attachment`, `'._DB_PREFIX_.'product_attachment`, `'._DB_PREFIX_.'layered_indexable_feature_value_lang_value`, `'._DB_PREFIX_.'country_lang`, `'._DB_PREFIX_.'tax_rule`, `'._DB_PREFIX_.'customization_field`, `'._DB_PREFIX_.'state`, `'._DB_PREFIX_.'buyers_group`, `'._DB_PREFIX_.'giftcardorder`, `'._DB_PREFIX_.'address_format`, `'._DB_PREFIX_.'meta`, `'._DB_PREFIX_.'feature_lang`, `'._DB_PREFIX_.'homeslider_slides_lang`, `'._DB_PREFIX_.'gift_voucher`, `'._DB_PREFIX_.'pm_advancedtopmenu_columns_lang`, `'._DB_PREFIX_.'cms`, `'._DB_PREFIX_.'cms_shop`, `'._DB_PREFIX_.'pm_advancedtopmenu_columns_wrap_lang`, `'._DB_PREFIX_.'location_coords`, `'._DB_PREFIX_.'order_state`, `'._DB_PREFIX_.'aff_configuration`, `'._DB_PREFIX_.'order_return_detail`, `'._DB_PREFIX_.'country`, `'._DB_PREFIX_.'country_shop`, `'._DB_PREFIX_.'aff_cart`, `'._DB_PREFIX_.'zone`, `'._DB_PREFIX_.'zone_shop`, `'._DB_PREFIX_.'buyers_group_orders`, `'._DB_PREFIX_.'feature`, `'._DB_PREFIX_.'feature_shop`, `'._DB_PREFIX_.'homeslider`, `'._DB_PREFIX_.'homeslider_slides`, `'._DB_PREFIX_.'pm_advancedpack`, `'._DB_PREFIX_.'pm_advancedtopmenu_columns`, `'._DB_PREFIX_.'quick_access_lang`, `'._DB_PREFIX_.'message_readed`, `'._DB_PREFIX_.'pm_advancedtopmenu_columns_wrap`, `'._DB_PREFIX_.'pm_advancedtopmenu_lang`, `'._DB_PREFIX_.'gender_lang`, `'._DB_PREFIX_.'image_type`, `'._DB_PREFIX_.'stock_mvt_reason_lang`, `'._DB_PREFIX_.'delivery_date`, `'._DB_PREFIX_.'delivery_date_lang`, `'._DB_PREFIX_.'search_engine`, `'._DB_PREFIX_.'fichiers_infos`, `'._DB_PREFIX_.'giftcardproduct`, `'._DB_PREFIX_.'aff_commission`, `'._DB_PREFIX_.'attribute_group_lang`, `'._DB_PREFIX_.'blocklink_lang`, `'._DB_PREFIX_.'cms_block_page`, `'._DB_PREFIX_.'layered_indexable_attribute_group_lang_value`, `'._DB_PREFIX_.'oleapromo_lang`, `'._DB_PREFIX_.'giftcardtemplate_lang`, `'._DB_PREFIX_.'layered_indexable_feature`, `'._DB_PREFIX_.'loyalty_state_lang`, `'._DB_PREFIX_.'order_return`, `'._DB_PREFIX_.'order_return_state_lang`, `'._DB_PREFIX_.'quick_access`, `'._DB_PREFIX_.'reinsurance_lang`, `'._DB_PREFIX_.'so_return_label`, `'._DB_PREFIX_.'aff_sales`, `'._DB_PREFIX_.'block_cms`, `'._DB_PREFIX_.'group_lang`, `'._DB_PREFIX_.'pm_advancedtopmenu`, `'._DB_PREFIX_.'pm_advancedtopmenu_shop`, `'._DB_PREFIX_.'web_browser`, `'._DB_PREFIX_.'configuration_kpi_lang`, `'._DB_PREFIX_.'dateofdelivery_carrier_rule`, `'._DB_PREFIX_.'module_currency`, `'._DB_PREFIX_.'pm_adsandslideshow_auto_conf_lang`, `'._DB_PREFIX_.'attribute_group`, `'._DB_PREFIX_.'attribute_group_shop`, `'._DB_PREFIX_.'cms_block_lang`, `'._DB_PREFIX_.'discount_type_lang`, `'._DB_PREFIX_.'employee`, `'._DB_PREFIX_.'employee_shop`, `'._DB_PREFIX_.'layered_indexable_attribute_group`, `'._DB_PREFIX_.'oleapromo`, `'._DB_PREFIX_.'oleapromo_shop`, `'._DB_PREFIX_.'request_sql`, `'._DB_PREFIX_.'blocklink`, `'._DB_PREFIX_.'blocklink_shop`, `'._DB_PREFIX_.'cms_category_lang`, `'._DB_PREFIX_.'giftcardtemplate`, `'._DB_PREFIX_.'group`, `'._DB_PREFIX_.'layered_filter`, `'._DB_PREFIX_.'loyalty_state`, `'._DB_PREFIX_.'order_return_state`, `'._DB_PREFIX_.'reinsurance`, `'._DB_PREFIX_.'stock_mvt_reason`, `'._DB_PREFIX_.'themeconfigurator`, `'._DB_PREFIX_.'buyers_group_reduction`, `'._DB_PREFIX_.'flashsalespro`, `'._DB_PREFIX_.'flashsalespro_names`, `'._DB_PREFIX_.'gender`, `'._DB_PREFIX_.'layered_indexable_feature_lang_value`, `'._DB_PREFIX_.'operating_system`, `'._DB_PREFIX_.'order_message_lang`, `'._DB_PREFIX_.'profile_lang`, `'._DB_PREFIX_.'tnt_carrier_option`, `'._DB_PREFIX_.'currency`, `'._DB_PREFIX_.'currency_shop`, `'._DB_PREFIX_.'discount_type`, `'._DB_PREFIX_.'group_shop`, `'._DB_PREFIX_.'product_download`, `'._DB_PREFIX_.'tax_rules_group`, `'._DB_PREFIX_.'tax_rules_group_shop`, `'._DB_PREFIX_.'aff_affiliates`, `'._DB_PREFIX_.'aff_campaigns`, `'._DB_PREFIX_.'aff_configuration_lang`, `'._DB_PREFIX_.'cms_category`, `'._DB_PREFIX_.'cms_category_shop`, `'._DB_PREFIX_.'code_blocage_newsletter`, `'._DB_PREFIX_.'contact_lang`, `'._DB_PREFIX_.'editorial_lang`, `'._DB_PREFIX_.'info_lang`, `'._DB_PREFIX_.'lang`, `'._DB_PREFIX_.'lang_shop`, `'._DB_PREFIX_.'manufacturer`, `'._DB_PREFIX_.'manufacturer_shop`, `'._DB_PREFIX_.'module_preference`, `'._DB_PREFIX_.'order_message`, `'._DB_PREFIX_.'payline_lang_2`, `'._DB_PREFIX_.'pm_adsandslideshow_auto_conf`, `'._DB_PREFIX_.'pm_advancedpack_products_attributes`, `'._DB_PREFIX_.'product_attribute_image`, `'._DB_PREFIX_.'product_comment`, `'._DB_PREFIX_.'profile`, `'._DB_PREFIX_.'supplier`, `'._DB_PREFIX_.'supplier_shop`, `'._DB_PREFIX_.'cms_block`, `'._DB_PREFIX_.'cms_block_shop`, `'._DB_PREFIX_.'contact`, `'._DB_PREFIX_.'contact_shop`, `'._DB_PREFIX_.'editorial`, `'._DB_PREFIX_.'info`, `'._DB_PREFIX_.'payline_2`, `'._DB_PREFIX_.'payline_card`, `'._DB_PREFIX_.'phytosanitaire`, `'._DB_PREFIX_.'pm_adsandslideshow`, `'._DB_PREFIX_.'pm_adsandslideshow_element`, `'._DB_PREFIX_.'pm_adsandslideshow_element_lang`, `'._DB_PREFIX_.'pm_adsandslideshow_lang`, `'._DB_PREFIX_.'shop`, `'._DB_PREFIX_.'shop_group`, `'._DB_PREFIX_.'shop_url`, `'._DB_PREFIX_.'theme`, `'._DB_PREFIX_.'aff_affiliates_meta`, `'._DB_PREFIX_.'aff_banners`, `'._DB_PREFIX_.'aff_custom_fields`, `'._DB_PREFIX_.'aff_custom_fields_lang`, `'._DB_PREFIX_.'aff_customers`, `'._DB_PREFIX_.'aff_payment_methods`, `'._DB_PREFIX_.'aff_payment_methods_fields`, `'._DB_PREFIX_.'aff_payments`, `'._DB_PREFIX_.'aff_texts`, `'._DB_PREFIX_.'alias`, `'._DB_PREFIX_.'cart_rule_carrier`, `'._DB_PREFIX_.'cart_rule_country`, `'._DB_PREFIX_.'cart_rule_group`, `'._DB_PREFIX_.'cat_restriction`, `'._DB_PREFIX_.'cms_role`, `'._DB_PREFIX_.'cms_role_lang`, `'._DB_PREFIX_.'compare`, `'._DB_PREFIX_.'compare_product`, `'._DB_PREFIX_.'customer_message_sync_imap`, `'._DB_PREFIX_.'flashsalespro_temp`, `'._DB_PREFIX_.'giftcardtag`, `'._DB_PREFIX_.'giftcardtemplate_tag`, `'._DB_PREFIX_.'group_reduction`, `'._DB_PREFIX_.'import_match`, `'._DB_PREFIX_.'layered_filter_shop`, `'._DB_PREFIX_.'linksmenutop`, `'._DB_PREFIX_.'linksmenutop_lang`, `'._DB_PREFIX_.'loyalty`, `'._DB_PREFIX_.'loyalty_history`, `'._DB_PREFIX_.'manufacturer_lang`, `'._DB_PREFIX_.'memcached_servers`, `'._DB_PREFIX_.'order_slip_detail_tax`, `'._DB_PREFIX_.'orlique_invoice`, `'._DB_PREFIX_.'orlique_invoice_lang`, `'._DB_PREFIX_.'pack`, `'._DB_PREFIX_.'payline_dirdebit`, `'._DB_PREFIX_.'payline_subscribe`, `'._DB_PREFIX_.'payline_subscribe_order`, `'._DB_PREFIX_.'payline_subscribe_state`, `'._DB_PREFIX_.'payline_wallet`, `'._DB_PREFIX_.'paypal_capture`, `'._DB_PREFIX_.'paypal_login_user`, `'._DB_PREFIX_.'pm_adsandslideshow_category`, `'._DB_PREFIX_.'pm_adsandslideshow_product`, `'._DB_PREFIX_.'pm_advancedtopmenu_prod_column`, `'._DB_PREFIX_.'product_comment_criterion`, `'._DB_PREFIX_.'product_comment_criterion_category`, `'._DB_PREFIX_.'product_comment_criterion_lang`, `'._DB_PREFIX_.'product_comment_criterion_product`, `'._DB_PREFIX_.'product_comment_grade`, `'._DB_PREFIX_.'product_country_tax`, `'._DB_PREFIX_.'product_group_reduction_cache`, `'._DB_PREFIX_.'product_supplier`, `'._DB_PREFIX_.'referrer`, `'._DB_PREFIX_.'referrer_cache`, `'._DB_PREFIX_.'referrer_shop`, `'._DB_PREFIX_.'required_field`, `'._DB_PREFIX_.'risk`, `'._DB_PREFIX_.'risk_lang`, `'._DB_PREFIX_.'scene`, `'._DB_PREFIX_.'scene_category`, `'._DB_PREFIX_.'scene_lang`, `'._DB_PREFIX_.'scene_products`, `'._DB_PREFIX_.'scene_shop`, `'._DB_PREFIX_.'so_delivery`, `'._DB_PREFIX_.'specific_price_rule`, `'._DB_PREFIX_.'specific_price_rule_condition`, `'._DB_PREFIX_.'specific_price_rule_condition_group`, `'._DB_PREFIX_.'stock`, `'._DB_PREFIX_.'stock_mvt`, `'._DB_PREFIX_.'store`, `'._DB_PREFIX_.'store_shop`, `'._DB_PREFIX_.'supplier_lang`, `'._DB_PREFIX_.'supply_order`, `'._DB_PREFIX_.'supply_order_detail`, `'._DB_PREFIX_.'supply_order_history`, `'._DB_PREFIX_.'supply_order_receipt_history`, `'._DB_PREFIX_.'supply_order_state`, `'._DB_PREFIX_.'supply_order_state_lang`, `'._DB_PREFIX_.'tab_module_preference`, `'._DB_PREFIX_.'theme_specific`, `'._DB_PREFIX_.'tnt_carrier_cache_service`, `'._DB_PREFIX_.'tnt_carrier_drop_off`, `'._DB_PREFIX_.'tnt_carrier_shipping_number`, `'._DB_PREFIX_.'tnt_carrier_weight`, `'._DB_PREFIX_.'tnt_package_history`, `'._DB_PREFIX_.'warehouse`, `'._DB_PREFIX_.'warehouse_carrier`, `'._DB_PREFIX_.'warehouse_product_location`, `'._DB_PREFIX_.'warehouse_shop`, `'._DB_PREFIX_.'webservice_account`, `'._DB_PREFIX_.'webservice_account_shop`, `'._DB_PREFIX_.'webservice_permission`, `'._DB_PREFIX_.'wishlist`, `'._DB_PREFIX_.'wishlist_email`, `'._DB_PREFIX_.'wishlist_product`, `'._DB_PREFIX_.'wishlist_product_cart`;';
    //echo $sql;
    /*$res = Db::getInstance()->ExecuteS($sql);
    if(!$res)
      die('error OPTIMIZE tables');
*/