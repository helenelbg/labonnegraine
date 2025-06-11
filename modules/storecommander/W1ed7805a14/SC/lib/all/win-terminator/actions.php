<?php

$actions = array();

/*
$actions[""] = array(
    "name" => _l(''),
    "type" => "",
    "currently" => '',
    "param" => 1,
    "default_value" => "",
    "info" => ""
);
*/

$actions['deloldcart'] = array(
    'name' => _l('Delete all carts if not used by orders and if older than Now -XX days'),
    'type' => 'maintenance',
    'currently' => '',
    'param' => 1,
    'default_value' => '',
    'info' => _l('Delete all carts if not used by orders and if older than Now -XX days: allow clients to keep products in their carts from the last XX days').'<br/><br/>
            '._l('Affected tables:').' cart ; cart_product ; orders ; message ; message_readed ; '.
        'specific_price ; customization ; customized_data',
);
if (version_compare(_PS_VERSION_, '1.5.0.0', '<'))
{
    $actions['deloldupdatedproducts'] = array(
        'name' => _l('Delete all products where date of update is less than (yyyy-mm-dd)'),
        'type' => 'maintenance',
        'currently' => '',
        'param' => 1,
        'default_value' => '',
        'info' => _l('Delete all products and all elements (images, attachements, etc.) where date of update is less than the entered date'),
    );
}
$actions['deldiscountdate'] = array(
    'name' => _l('Delete all discounts older than Now -XX days'),
    'type' => 'maintenance',
    'currently' => '',
    'param' => 1,
    'default_value' => '',
    'info' => _l('Delete all discounts where older than Now -XX days').'<br/><br/>
                    '._l('Affected tables:').' specific_price,cart_rule,cart_rule_carrier,cart_rule_country,cart_rule_group,cart_rule_lang,cart_rule_shop,specific_price_rule,specific_price_rule_condition_group,specific_price_rule_condition',
);
$actions['delupload'] = array(
    'name' => _l('Delete product customization where created cart date less than XX days'),
    'type' => 'maintenance',
    'currently' => '',
    'param' => 1,
    'default_value' => '',
    'info' => _l('Delete customization files depending on created cart date').'<br/><br/>
                    '._l('Deleted files:').' '._PS_UPLOAD_DIR_.'*.*',
);
$actions['delsearch'] = array(
    'name' => _l('Delete all search details'),
    'type' => 'maintenance',
    'currently' => '$terminatorTools->g(\'search_index\')',
    'param' => 0,
    'default_value' => '',
    'info' => _l('Delete all search details').'<br/><br/>
                    '._l('Affected tables:').' search_index ; search_word ; sekeyword ; statssearch',
);
$actions['delpagenotfound'] = array(
    'name' => _l('Delete all pages not found'),
    'type' => 'maintenance',
    'currently' => '$terminatorTools->g(\'pagenotfound\')',
    'param' => 0,
    'default_value' => '',
    'info' => _l('Delete all page not found stats').'<br/><br/>
                    '._l('Affected tables:').' pagenotfound',
);
$actions['dellog'] = array(
    'name' => _l('Delete log'),
    'type' => 'maintenance',
    'currently' => '$terminatorTools->g(\'log\')',
    'param' => 1,
    'default_value' => '500',
    'info' => _l('Delete log (errors and system alerts), parameter is used to keep the specified number of logs in the table').'<br/><br/>
                    '._l('Affected tables:').' log',
);
$actions['delpageviewed'] = array(
    'name' => _l('Delete all viewed pages'),
    'type' => 'maintenance',
    'currently' => '$terminatorTools->g(\'page_viewed\')',
    'param' => 0,
    'default_value' => '',
    'info' => _l('Delete all page viewed stats').'<br/><br/>
                    '._l('Affected tables:').' page_viewed ; date_range',
);
$actions['delconnections'] = array(
    'name' => _l('Delete all connection stats older than Now -XX days (with a legal number of 365 days minimum)'),
    'type' => 'maintenance',
    'currently' => '$terminatorTools->g(\'connections\')',
    'param' => 1,
    'default_value' => '365',
    'info' => _l('Delete all connection stats older than Now -XX days (with a legal number of 365 days minimum)').'<br/><br/>
                    '._l('Affected tables:').'  cart ; connections ; connections_page ; connections_source ; guest',
);
if (version_compare(_PS_VERSION_, '8.0.0', '<'))
{
    $actions['delreferrer'] = array(
        'name' => _l('Delete referrers'),
        'type' => 'maintenance',
        'currently' => '$terminatorTools->g(\'referrer\')',
        'param' => 0,
        'default_value' => '',
        'info' => _l('Delete referrer and affiliate stats').'<br/><br/>
                    '._l('Affected tables:').' referrer ; referrer_cache ; referrer_shop',
    );
}
$actions['optimize'] = array(
    'name' => _l('Optimize tables'),
    'type' => 'maintenance',
    'currently' => '',
    'param' => 0,
    'default_value' => '',
    'info' => _l('This option should be used if you have deleted a large part of a table. Deleted rows are maintained in a linked list and subsequent INSERT operations reuse old row positions.').'
                    '._l('It will reclaim the unused space and to defragment the data file. See').' <a href="http://dev.mysql.com/doc/refman/5.1/en/optimize-table.html" target="_blank">the MySQL manual</a> '._l('for more information'),
);

$actions['delcart'] = array(
    'name' => _l('Delete all carts'),
    'type' => 'db',
    'currently' => '$terminatorTools->g(\'cart\')',
    'param' => 0,
    'default_value' => '',
    'info' => _l('Delete all carts').'<br/><br/>
            '._l('Affected tables:').' cart ; cart_product ; cart_cart_rule ; message ; message_readed ; orders ; specific_price ; customization ; customized_data',
);
$actions['delcategoryandproduct'] = array(
    'name' => _l('Delete all categories and products'),
    'type' => 'db',
    'currently' => '$terminatorTools->g(\'category\').\' / \'.$terminatorTools->g(\'product\')',
    'param' => 0,
    'default_value' => '',
    'info' => _l('Delete all categories and products. Insert the default Home category.').'<br/><br/>
                    '._l('Affected tables:').' category ; category_lang ; category_group ; category_shop ; group_reduction ; scene_category ; category_product ; '.$terminatorTools->mainFunctions('deleteProducts_info').' ; '.$terminatorTools->mainFunctions('deleteCarts_info').' ; '.$terminatorTools->mainFunctions('deleteProductComments_info').'<br/><br/>
                    '._l('Deleted files:').' '._PS_CAT_IMG_DIR_.'*.* ; '._PS_PROD_IMG_DIR_.'*.* ; '._PS_TMP_IMG_DIR_.'*product*.* ; '._PS_TMP_IMG_DIR_.'*category*.* ; '._PS_DOWNLOAD_DIR_.'*.*',
);
$actions['delorder'] = array(
    'name' => _l('Delete all orders'),
    'type' => 'db',
    'currently' => '$terminatorTools->g(\'orders\')',
    'param' => 0,
    'default_value' => '',
    'info' => _l('Delete all orders, messages, discounts, returns, customizations, slips').'<br/><br/>
                    '._l('Affected tables:').' '.$terminatorTools->mainFunctions('deleteOrders_info'),
);
$actions['delslip'] = array(
    'name' => _l('Delete all slips'),
    'type' => 'db',
    'currently' => '$terminatorTools->g(\'order_slip\')',
    'param' => 0,
    'default_value' => '',
    'info' => _l('Delete all slips').'<br/><br/>
                    '._l('Affected tables:').' order_slip ; order_slip_detail',
);
$actions['delcustomer'] = array(
    'name' => _l('Delete all customers and addresses'),
    'type' => 'db',
    'currently' => '$terminatorTools->g(\'customer\')',
    'param' => 0,
    'default_value' => '',
    'info' => _l('Delete all customers and all orders').'<br/><br/>
                    '._l('Affected tables:').' customer,address,customer_group,customer_thread,customer_message,customer_message_sync_imap,employee,'.$terminatorTools->mainFunctions('deleteOrders_info').','.$terminatorTools->mainFunctions('deleteCarts_info'),
);
$actions['resetgroup'] = array(
    'name' => _l('Delete groups'),
    'type' => 'db',
    'currently' => '$terminatorTools->g(\'group\')',
    'param' => 0,
    'default_value' => '',
    'info' => _l('Delete groups but keep default group').'<br/><br/>
                    '._l('Affected tables:').' group ; group_lang ; carrier_group ; group_reduction ; customer ; customer_group ; category_group ; module_group ; specific_price ; specific_price_rule ; cart_rule_group ; product_group_reduction_cache',
);
$actions['delmessage'] = array(
    'name' => _l('Delete all stored messages'),
    'type' => 'db',
    'currently' => '$terminatorTools->g(\'order_message\')',
    'param' => 0,
    'default_value' => '',
    'info' => _l('Delete all stored messages which are used as model for your communication in the order details page').'<br/><br/>
                    '._l('Affected tables:').' order_message ; order_message_lang',
);
$actions['delmanufacturer'] = array(
    'name' => _l('Delete all manufacturers'),
    'type' => 'db',
    'currently' => '$terminatorTools->g(\'manufacturer\')',
    'param' => 0,
    'default_value' => '',
    'info' => _l('Delete all manufacturers').'<br/><br/>
                    '._l('Affected tables:').' manufacturer ; manufacturer_lang ; manufacturer_shop ; address ; product<br/><br/>
                    '._l('Deleted files:').' '._PS_MANU_IMG_DIR_.'*.*',
);
$actions['delsupplier'] = array(
    'name' => _l('Delete all suppliers'),
    'type' => 'db',
    'currently' => '$terminatorTools->g(\'supplier\')',
    'param' => 0,
    'default_value' => '',
    'info' => _l('Delete all suppliers').'<br/><br/>
                    '._l('Affected tables:').' supplier ; product_supplier ; supplier_lang ; supplier_shop ; supply_order ; supply_order_detail ; supply_order_history ; supply_order_receipt_history ; address ; product<br/><br/>
                    '._l('Deleted files:').' '._PS_SUPP_IMG_DIR_.'*.*',
);
$actions['delcarrier'] = array(
    'name' => _l('Delete all carriers'),
    'type' => 'db',
    'currently' => '$terminatorTools->g(\'carrier\')',
    'param' => 0,
    'default_value' => '',
    'info' => _l('Delete all stored carriers and add the default carrier (you). Reset carts and orders to unselected carrier.').'<br/><br/>
                    '._l('Affected tables:').' carrier ; carrier_lang ; carrier_group ; carrier_shop ; carrier_tax_rules_group_shop ; carrier_zone ; delivery ; range_price ; range_weight ; cart_rule_carrier ; product_carrier ; warehouse_carrier ; cart ; orders ; order_carrier'.(version_compare(_PS_VERSION_, '1.7.0.0', '>=') ? ' ; module_carrier' : ''),
);
$actions['delproduct'] = array(
    'name' => _l('Delete all products with image files'),
    'type' => 'db',
    'currently' => '$terminatorTools->g(\'product\')',
    'param' => 0,
    'default_value' => '',
    'info' => _l('Delete all products in database and delete all image/document files').'<br/><br/>
                    '._l('Affected tables:').' '.$terminatorTools->mainFunctions('deleteProducts_info').' ; '.$terminatorTools->mainFunctions('deleteProductComments_info').' ; '.$terminatorTools->mainFunctions('deleteCarts_info').'<br/><br/>
                    '._l('Deleted files:').' '._PS_PROD_IMG_DIR_.'*.* ; '._PS_TMP_IMG_DIR_.'*product*.* ;  '._PS_DOWNLOAD_DIR_.'*.*',
);
$actions['delcustomization'] = array(
    'name' => _l('Delete all product customization data'),
    'type' => 'db',
    'currently' => '$terminatorTools->g(\'customization\')',
    'param' => 0,
    'default_value' => '',
    'info' => _l('Delete all customizations').'<br/><br/>
                    '._l('Affected tables:').' customization ; customized_data ; customization_field ; customization_field_lang ; product
                    '._l('Deleted files:').' '._PS_UPLOAD_DIR_.'*.*',
);
$actions['delfeature'] = array(
    'name' => _l('Delete all product features'),
    'type' => 'db',
    'currently' => '$terminatorTools->g(\'feature\')',
    'param' => 0,
    'default_value' => '',
    'info' => _l('Delete all features and feature values').'<br/><br/>
                    '._l('Affected tables:').' feature ; feature_lang ; feature_product ; feature_shop ; feature_value ; feature_value_lang',
);
$actions['deldisabledproducts'] = array(
    'name' => _l('Delete all product inactive'),
    'type' => 'db',
    'currently' => '$terminatorTools->g(\'product\', \'active=0\')',
    'param' => 0,
    'default_value' => '',
    'info' => _l('Delete all inactive products and all elements (images, attachments, etc.)'),
);
$actions['delstockmvt'] = array(
    'name' => _l('Delete all movements of stock products'),
    'type' => 'db',
    'currently' => '$terminatorTools->g(\'stock_mvt\')',
    'param' => 0,
    'default_value' => '',
    'info' => _l('Delete all movements of stock products').'<br/><br/>
                    '._l('Affected tables:').' stock_mvt',
);
$actions['delattributes'] = array(
    'name' => _l('Delete all product attributes and groups of attributes'),
    'type' => 'db',
    'currently' => '$terminatorTools->g(\'attribute\')',
    'param' => 0,
    'default_value' => '',
    'info' => _l('Delete all attributes and attribute groups').'<br/><br/>
                    '._l('Affected tables:').' attribute ; attribute_lang ; attribute_group ; attribute_group_lang ; attribute_group_shop ; attribute_shop ; '.(version_compare(_PS_VERSION_, '8.0.0', '<') ? 'attribute_impact ; ' : '').'product_attribute ; product_attribute_shop ; product_attribute_combination ; product_attribute_image',
);
$actions['deltag'] = array(
    'name' => _l('Delete all tags'),
    'type' => 'db',
    'currently' => '$terminatorTools->g(\'tag\')',
    'param' => 0,
    'default_value' => '',
    'info' => _l('Delete all tags').'<br/><br/>
                    '._l('Affected tables:').' tag ; product_tag',
);
$actions['deltextalias'] = array(
    'name' => _l('Delete all text aliases'),
    'type' => 'db',
    'currently' => '$terminatorTools->g(\'alias\')',
    'param' => 0,
    'default_value' => '',
    'info' => _l('Delete all alias').'<br/><br/>
                    '._l('Affected tables:').' alias',
);
if (version_compare(_PS_VERSION_, '1.7.0.0', '<'))
{
    $actions['delscene'] = array(
    'name' => _l('Delete all scenes'),
    'type' => 'db',
    'currently' => '$terminatorTools->g(\'scene\')',
    'param' => 0,
    'default_value' => '',
    'info' => _l('Delete all scenes').' <br /><br />
                        '._l('Affected tables:').' scene; scene_category; scene_lang; scene_products; scene_shop <br /><br />
                        '._l('Deleted files:').' '._PS_SCENE_IMG_DIR_.' *.* ; '._PS_SCENE_THUMB_IMG_DIR_.' *.*',
);
}
$actions['deldiscount'] = array(
    'name' => _l('Delete all special offers (discounts)'),
    'type' => 'db',
    'currently' => '$terminatorTools->g(\'discount\')',
    'param' => 0,
    'default_value' => '',
    'info' => _l('Delete all discounts').'<br/><br/>
                    '._l('Affected tables:').' specific_price',
);
$actions['resetrangeprice'] = array(
    'name' => _l('Reset price ranges'),
    'type' => 'db',
    'currently' => '',
    'param' => 0,
    'default_value' => '',
    'info' => _l('Delete data and insert the default price range').'<br/><br/>
                    '._l('Affected tables:').' range_price',
);
$actions['resetrangeweight'] = array(
    'name' => _l('Reset weight ranges'),
    'type' => 'db',
    'currently' => '',
    'param' => 0,
    'default_value' => '',
    'info' => _l('Delete data and insert the default weight range').'<br/><br/>
                    '._l('Affected tables:').' range_weight',
);
$actions['resetstore'] = array(
    'name' => _l('Reset stores'),
    'type' => 'db',
    'currently' => '$terminatorTools->g(\'store\')',
    'param' => 0,
    'default_value' => '',
    'info' => _l('Delete all stores').'<br/><br/>
                    '._l('Affected tables:').' store ; store_shop '.(version_compare(_PS_VERSION_, '1.7.3.0', '>=') ? '; store_lang' : '').'<br/><br/>
                    '._l('Deleted files:').' '._PS_STORE_IMG_DIR_.'*.*',
);

$actions['delemailsubscription'] = array(
    'name' => _l('Delete all emails registred in emailsubscription'),
    'type' => 'module',
    'currently' => (version_compare(_PS_VERSION_, '1.7.0.0', '>=') ? '$terminatorTools->g(\'emailsubscription\')' : '$terminatorTools->g(\'newsletter\')'),
    'param' => 0,
    'default_value' => '',
    'info' => _l('Check if you use the emailsubscription module and delete all mails').'<br/><br/>
                            '._l('Affected tables:').(version_compare(_PS_VERSION_, '1.7.0.0', '>=') ? ' emailsubscription' : ' newsletter'),
);
if (version_compare(_PS_VERSION_, '1.7.0.0', '<'))
{
    $actions['delcomment'] = array(
    'name' => _l('Delete all product comments'),
    'type' => 'module',
    'currently' => '$terminatorTools->g(\'product_comment\')',
    'param' => 0,
    'default_value' => '',
    'info' => _l('Delete all product comments').'<br/><br/>
                        '._l('Affected tables:').' '.$terminatorTools->mainFunctions('deleteProductComments_info'),
);
    $actions['delwishlist'] = array(
    'name' => _l('Delete all wishlists'),
    'type' => 'module',
    'currently' => '$terminatorTools->g(\'wishlist\')',
    'param' => 0,
    'default_value' => '',
    'info' => _l('Check if you use the wishlist module and delete all wishlists').'<br/><br/>
                        '._l('Affected tables:').' wishlist ; wishlist_email ; wishlist_product ; wishlist_product_cart',
);
    $actions['delloyalty'] = array(
    'name' => _l('Delete all reward points (loyalties)'),
    'type' => 'module',
    'currently' => '$terminatorTools->g(\'loyalty\')',
    'param' => 0,
    'default_value' => '',
    'info' => _l('Check if you use the loyalties module and delete all loyalties').'<br/><br/>
                        '._l('Affected tables:').' loyalty ; loyalty_history',
);
}
$actions['delbestsellers'] = array(
    'name' => _l('Delete all bestseller stats'),
    'type' => 'module',
    'currently' => '$terminatorTools->g(\'product_sale\')',
    'param' => 0,
    'default_value' => '',
    'info' => _l('Delete bestseller statistics used in the bestsellers block.').'<br/><br/>
                    '._l('Affected tables:').' product_sale',
);

$actions['invoicenum'] = array(
    'name' => _l('Next invoice number'),
    'type' => 'other',
    'currently' => '',
    'param' => 1,
    'default_value' => (Order::getLastInvoiceNumber() + 1),
    'info' => _l('If you delete orders you may need to reset the invoice counter. You can start from 0 or 1 or from any other values.'),
);
$actions['ordernum'] = array(
    'name' => _l('Next order number'),
    'type' => 'other',
    'currently' => '',
    'param' => 1,
    'default_value' => $terminatorTools->getAutoIncrement('orders'),
    'info' => _l('Set the next order number (autoincrement of table) if you wish to begin order from 501 for example.').'<br/>
                    '._l('The number will be visible on the next created order.'),
);
$actions['clientnum'] = array(
    'name' => _l('Next client account number'),
    'type' => 'other',
    'currently' => '',
    'param' => 1,
    'default_value' => $terminatorTools->getAutoIncrement('customer'),
    'info' => _l('Set the next client account number (autoincrement of table) if you wish to begin account number from 501 for example.').'<br/>
                    '._l('The number will be visible on the next created customer.'),
);

$actions['delsmartycache'] = array(
    'name' => _l('Delete smarty cache'),
    'type' => 'files',
    'currently' => '$terminatorTools->sizeFormat($terminatorTools->getDirectorySize(_PS_ROOT_DIR_ . \'/cache/smarty/compile/\', \'size\')'.
        ' + $terminatorTools->getDirectorySize(_PS_ROOT_DIR_ . \'/cache/smarty/cache/\', \'size\')'.
        (version_compare(_PS_VERSION_, '1.7.0.0', '>=') ? ' + $terminatorTools->getDirectorySize(_PS_ROOT_DIR_ . \'/var/cache/\', \'size\')' : '').')',
    'param' => 0,
    'default_value' => '',
    'info' => _l('Delete all files in /cache/smarty/cache/*.* and /cache/smarty/compile/*.* (but not index.php)'),
);
$actions['deltmpimg'] = array(
    'name' => _l('Delete temporary image files'),
    'type' => 'files',
    'currently' => '$terminatorTools->sizeFormat($terminatorTools->getDirectorySize(_PS_TMP_IMG_DIR_, \'size\'))',
    'param' => 0,
    'default_value' => '',
    'info' => _l('Delete all image files in /img/tmp/*.* (but not index.php). The files are created by the back office for thumbnails.'),
);
$actions['delcachefs'] = array(
    'name' => _l('Delete cacheFS'),
    'type' => 'files',
    'currently' => '$terminatorTools->sizeFormat($terminatorTools->getDirectorySize(_PS_ROOT_DIR_ . "/cache/cachefs/", \'size\'))',
    'param' => 0,
    'default_value' => '',
    'info' => _l('Delete all image files in /cache/cachefs/*.* (but not index.php)'),
);
$actions['delcachetcpdf'] = array(
    'name' => _l('Delete TCPDF cache'),
    'type' => 'files',
    'currently' => '$terminatorTools->sizeFormat($terminatorTools->getDirectorySize(_PS_ROOT_DIR_ . "/cache/tcpdf/", \'size\'))',
    'param' => 0,
    'default_value' => '',
    'info' => _l('Delete all image files in /cache/tcpdf/*.* (but not index.php)'),
);
$actions['emptypsimportfolder'] = array(
    'name' => _l("Empty Prestashop's import folder"),
    'type' => 'files',
    'currently' => '$terminatorTools->sizeFormat($terminatorTools->getDirectorySize(SC_PS_PATH_ADMIN_DIR . "import/", \'size\'))',
    'param' => 0,
    'default_value' => '',
    'info' => _l('Delete all files in /admin_folder/import/*.* (but not index.php and .htaccess)'),
);
$actions['resetcustomerspwd'] = array(
    'name' => _l('Reset all customers password'),
    'type' => 'db',
    'currently' => '',
    'param' => 0,
    'default_value' => '',
    'info' => _l('Reset password for all customers'),
);
$actions['resetemployeespwd'] = array(
    'name' => _l('Reset all employees password'),
    'type' => 'db',
    'currently' => '',
    'param' => 0,
    'default_value' => '',
    'info' => _l('Reset password for all employees'),
);
