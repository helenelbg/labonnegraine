<?php

global $cat_product_data_fields_condition;
$format_ht = (_s('CAT_PROD_PRICEWITHOUTTAX4DEC') ? '0.0000' : '0.00');
$format_ttc = (_s('CAT_PROD_PRICEWITHTAX4DEC') ? '0.0000' : '0.00');
$colSettings['id'] = array('text' => _l('ID'), 'width' => 40, 'align' => 'left', 'type' => 'ro', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter');
$colSettings['name'] = array('text' => _l('Name'), 'width' => 200, 'align' => 'left', 'type' => 'edtxt', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
$colSettings['position'] = array('text' => _l('Pos.'), 'width' => 35, 'align' => 'right', 'type' => 'ro', 'sort' => 'int', 'color' => '', 'filter' => '#text_filter');
$colSettings['reference'] = array('text' => _l('Ref'), 'width' => 80, 'align' => 'left', 'type' => 'edtxt', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
$colSettings['quantity'] = array('text' => _l('Stock available'), 'width' => 60, 'align' => 'right', 'type' => 'ed', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter');
if (version_compare(_PS_VERSION_, '1.7.5.0', '>='))
{
    $colSettings['soft_qty_physical'] = array('text' => _l('Physical quantity'), 'width' => 60, 'align' => 'right', 'type' => 'ro', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter');
    $colSettings['soft_qty_reserved'] = array('text' => _l('Reserved quantity'), 'width' => 60, 'align' => 'right', 'type' => 'ro', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter');
}
$colSettings['minimal_quantity'] = array('text' => _l('Minimum quantity'), 'width' => 60, 'align' => 'right', 'type' => 'ed', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter');
$colSettings['quantityupdate'] = array('text' => _l('Stock available +/-'), 'width' => 60, 'align' => 'right', 'type' => 'ed', 'sort' => 'na', 'color' => '#EFFAFF', 'filter' => '#numeric_filter');
$colSettings['wholesale_price'] = array('text' => _l('Wholesale price'), 'width' => 55, 'align' => 'right', 'type' => 'ed', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter');
$colSettings['price'] = array('text' => _l('Price excl. Tax'), 'width' => 65, 'align' => 'right', 'type' => 'edn', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter', 'format' => $format_ht);
$colSettings['additional_shipping_cost'] = array('text' => _l('Add. shipping cost'), 'width' => 65, 'align' => 'right', 'type' => 'ed', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter');
$colSettings['price_inc_tax'] = array('text' => _l('Price incl. Tax'), 'width' => 65, 'align' => 'right', 'type' => 'edn', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter', 'format' => $format_ttc);
$colSettings['unity'] = array('text' => _l('Unit'), 'width' => 50, 'align' => 'right', 'type' => 'ed', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
$colSettings['unit_price_ratio'] = array('text' => _l('Unit price'), 'width' => 65, 'align' => 'right', 'type' => 'edn', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter', 'format' => '0.00');
$colSettings['unit_price_inc_tax'] = array('text' => _l('Unit price Tax incl'), 'width' => 65, 'align' => 'right', 'type' => 'edn', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter', 'format' => '0.00');
$colSettings['discountprice'] = array('text' => _l('Discount price'), 'width' => 150, 'align' => 'left', 'type' => 'edtxt', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
$colSettings['ecotax'] = array('text' => _l('EcoTax'), 'width' => 50, 'align' => 'right', 'type' => 'edn', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter', 'format' => '0.00');
$colSettings['weight'] = array('text' => _l('Weight'), 'width' => 65, 'align' => 'right', 'type' => 'ed', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter');
$colSettings['width'] = array('text' => _l('Width'), 'width' => 65, 'align' => 'right', 'type' => 'ed', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter');
$colSettings['height'] = array('text' => _l('Height'), 'width' => 65, 'align' => 'right', 'type' => 'ed', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter');
$colSettings['depth'] = array('text' => _l('Depth'), 'width' => 65, 'align' => 'right', 'type' => 'ed', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter');
$colSettings['supplier_reference'] = array('text' => _l('Supplier Ref.'), 'width' => 80, 'align' => 'left', 'type' => 'edtxt', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
$colSettings['id_manufacturer'] = array('text' => _l('Manufacturer'), 'width' => 100, 'align' => 'left', 'type' => 'coro', 'sort' => 'str', 'color' => '', 'filter' => '#select_filter_strict', 'options' => $arrManufacturers);
$colSettings['id_supplier'] = array('text' => _l('Supplier'), 'width' => 100, 'align' => 'left', 'type' => 'coro', 'sort' => 'str', 'color' => '', 'filter' => '#select_filter_strict', 'options' => $arrSuppliers);
$colSettings['id_tax_rules_group'] = array('text' => _l('Tax rule'), 'width' => 65, 'align' => 'left', 'type' => 'coro', 'sort' => 'str', 'color' => '', 'filter' => '#select_filter', 'options' => $arrTax);
if (version_compare(_PS_VERSION_, '1.7.7.0', '>='))
{
    $colSettings['mpn'] = array('text' => _l('MPN'), 'width' => 100, 'align' => 'left', 'type' => 'edtxt', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
}
$colSettings['ean13'] = array('text' => _l('EAN13'), 'width' => 100, 'align' => 'left', 'type' => 'edtxt', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
$colSettings['upc'] = array('text' => _l('UPC'), 'width' => 100, 'align' => 'left', 'type' => 'edtxt', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
if (version_compare(_PS_VERSION_, '1.7.0.0', '>='))
{
    $colSettings['isbn'] = array('text' => _l('ISBN'), 'width' => 100, 'align' => 'left', 'type' => 'edtxt', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
}
$colSettings['location'] = array('text' => _l('Location').(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? ' '._l('(old)') : ''), 'width' => 100, 'align' => 'left', 'type' => 'edtxt', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
if (version_compare(_PS_VERSION_, '1.7.5.0', '>='))
{
    $colSettings['location_new'] = array('text' => _l('Stock location'), 'width' => 100, 'align' => 'left', 'type' => 'edtxt', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
}
$colSettings['out_of_stock'] = array('text' => _l('If out of stock'), 'width' => 100, 'align' => 'left', 'type' => 'coro', 'sort' => 'str', 'color' => '', 'filter' => '#select_filter', 'options' => array(0 => _l('Deny orders'), 1 => _l('Allow orders'), 2 => _l('Default(Pref)')));
$colSettings['available_now'] = array('text' => _l('Msg available now'), 'width' => 100, 'align' => 'left', 'type' => 'co', 'sort' => 'str', 'color' => '', 'filter' => '#select_filter', 'options' => $arrMsgAvailableNow);
$colSettings['available_later'] = array('text' => _l('Msg available later'), 'width' => 100, 'align' => 'left', 'type' => 'co', 'sort' => 'str', 'color' => '', 'filter' => '#select_filter', 'options' => $arrMsgAvailableLater);
$colSettings['reduction_price'] = array('text' => _l('Reduction amount'), 'width' => 60, 'align' => 'right', 'type' => 'co', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter', 'options' => $arrReductionPrice);
$colSettings['price_with_reduction'] = array('text' => _l('Price incl reduction'), 'width' => 60, 'align' => 'right', 'type' => 'ro', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter');
$colSettings['reduction_percent'] = array('text' => _l('Reduction %'), 'width' => 60, 'align' => 'right', 'type' => 'co', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter', 'options' => $arrReductionPercent);
$colSettings['price_with_reduction_percent'] = array('text' => _l('Price incl % reduction'), 'width' => 60, 'align' => 'right', 'type' => 'ro', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter');
$colSettings['reduction_from'] = array('text' => _l('Discount starts'), 'width' => 140, 'align' => 'right', 'type' => 'dhxCalendarA', 'sort' => 'date', 'color' => '', 'filter' => '#select_filter');
$colSettings['reduction_to'] = array('text' => _l('Discount ends'), 'width' => 140, 'align' => 'right', 'type' => 'dhxCalendarA', 'sort' => 'date', 'color' => '', 'filter' => '#select_filter');

$colSettings['margin_wt_amount_after_reduction'] = array('text' => _l('Margin amount after reduction'), 'width' => 60, 'align' => 'right', 'type' => 'ro', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter');
$colSettings['margin_wt_percent_after_reduction'] = array('text' => _l('Margin % after reduction'), 'width' => 60, 'align' => 'right', 'type' => 'ro', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter');
$colSettings['margin_after_reduction'] = array('text' => _l('Margin after reduction'), 'width' => 60, 'align' => 'right', 'type' => 'ro', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter');
$colSettings['price_wt_with_reduction'] = array('text' => _l('Price tax excl after reduction'), 'width' => 60, 'align' => 'right', 'type' => 'ro', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter');
$colSettings['price_it_with_reduction'] = array('text' => _l('Price tax incl after reduction'), 'width' => 60, 'align' => 'right', 'type' => 'ro', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter');
if (version_compare(_PS_VERSION_, '1.6.0.11', '>='))
{
    $colSettings['reduction_tax'] = array('text' => _l('Reduction tax'), 'width' => 50, 'align' => 'right', 'type' => 'coro', 'sort' => 'str', 'color' => '', 'filter' => '#select_filter', 'options' => array(0 => _l('Excl. tax'), 1 => _l('Incl. tax')));
}

$colSettings['on_sale'] = array('text' => _l('On sale'), 'width' => 45, 'align' => 'center', 'type' => 'coro', 'sort' => 'str', 'color' => '', 'filter' => '#select_filter', 'options' => array(0 => _l('No'), 1 => _l('Yes')));

if (_r('ACT_CAT_ENABLE_PRODUCTS'))
{
    $colSettings['active'] = array('text' => _l('Active'), 'width' => 45, 'align' => 'center', 'type' => 'coro', 'sort' => 'str', 'color' => '', 'filter' => '#select_filter', 'options' => array(0 => _l('No'), 1 => _l('Yes')));
}
else
{
    $colSettings['active'] = array('text' => _l('Active'), 'width' => 45, 'align' => 'center', 'type' => 'ro', 'sort' => 'str', 'color' => '', 'filter' => '#select_filter', 'options' => array(0 => _l('No'), 1 => _l('Yes')));
}

$colSettings['available_for_order'] = array('text' => _l('Available for order'), 'width' => 45, 'align' => 'center', 'type' => 'coro', 'sort' => 'str', 'color' => '', 'filter' => '#select_filter', 'options' => array(0 => _l('No'), 1 => _l('Yes')));
$colSettings['show_price'] = array('text' => _l('Show price'), 'width' => 45, 'align' => 'center', 'type' => 'coro', 'sort' => 'str', 'color' => '', 'filter' => '#select_filter', 'options' => array(0 => _l('No'), 1 => _l('Yes')));
$colSettings['online_only'] = array('text' => _l('Online only'), 'width' => 45, 'align' => 'center', 'type' => 'coro', 'sort' => 'str', 'color' => '', 'filter' => '#select_filter', 'options' => array(0 => _l('No'), 1 => _l('Yes')));
$colSettings['condition'] = array('text' => _l('Condition'), 'width' => 65, 'align' => 'center', 'type' => 'coro', 'sort' => 'str', 'color' => '', 'filter' => '#select_filter', 'options' => (isset($cat_product_data_fields_condition) ? $cat_product_data_fields_condition : array('new' => _l('New'), 'used' => _l('Used'), 'refurbished' => _l('Refurbished'))));
if (version_compare(_PS_VERSION_, '1.7.0.0', '>='))
{
    $colSettings['show_condition'] = array('text' => _l('Show condition'), 'width' => 45, 'align' => 'center', 'type' => 'coro', 'sort' => 'str', 'color' => '', 'filter' => '#select_filter', 'options' => array(0 => _l('No'), 1 => _l('Yes')));
}
$colSettings['link_rewrite'] = array('text' => _l('link_rewrite'), 'width' => 200, 'align' => 'left', 'type' => 'edtxt', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
$colSettings['meta_title'] = array('text' => _l('meta_title'), 'width' => 200, 'align' => 'left', 'type' => 'edtxt', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
$colSettings['meta_description'] = array('text' => _l('meta_description'), 'width' => 200, 'align' => 'left', 'type' => 'txttxt', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
$colSettings['meta_keywords'] = array('text' => _l('meta_keywords'), 'width' => 200, 'align' => 'left', 'type' => 'edtxt', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
$colSettings['date_add'] = array('text' => _l('Creation date'), 'width' => 140, 'align' => 'left', 'type' => 'dhxCalendarA', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
$colSettings['date_upd'] = array('text' => _l('Modified date'), 'width' => 140, 'align' => 'right', 'type' => 'dhxCalendarA', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
if (version_compare(_PS_VERSION_, '1.5.0.0', '<'))
{
    $colSettings['id_color_default'] = array('text' => _l('Default color group'), 'width' => 100, 'align' => 'left', 'type' => 'coro', 'sort' => 'str', 'color' => '', 'filter' => '#select_filter', 'options' => $arrColorGroups);
}
$colSettings['margin'] = array('text' => _l('Margin/Coef'), 'width' => 45, 'align' => 'right', 'type' => 'ron', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter', 'format' => '0.00');
$colSettings['description_short'] = array('text' => _l('Short description'), 'width' => 300, 'align' => 'left', 'type' => 'wysiwyg', 'sort' => 'na', 'color' => '', 'filter' => '#text_filter');
$colSettings['description'] = array('text' => _l('Description'), 'width' => 300, 'align' => 'left', 'type' => 'wysiwyg', 'sort' => 'na', 'color' => '', 'filter' => '#text_filter');
$colSettings['image'] = array('text' => _l('Image'), 'width' => 60, 'align' => 'center', 'type' => 'ro', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
$colSettings['combinations'] = array('text' => _l('Combinations (copy/paste)'), 'width' => 100, 'align' => 'left', 'type' => 'coro', 'sort' => 'na', 'color' => '', 'filter' => '#text_filter');
$colSettings['features'] = array('text' => _l('Features (copy/paste)'), 'width' => 100, 'align' => 'left', 'type' => 'coro', 'sort' => 'na', 'color' => '', 'filter' => '#text_filter');
$colSettings['categories'] = array('text' => _l('Categories (copy/paste)'), 'width' => 100, 'align' => 'left', 'type' => 'coro', 'sort' => 'na', 'color' => '', 'filter' => '#text_filter');
$colSettings['available_date'] = array('text' => _l('Available date'), 'width' => 80, 'align' => 'right', 'type' => 'dhxCalendarA', 'sort' => 'date', 'color' => '', 'filter' => '#select_filter', 'format' => '%Y-%m-%d');
$colSettings['visibility'] = array('text' => _l('Visibility'), 'width' => 65, 'align' => 'center', 'type' => 'coro', 'sort' => 'str', 'color' => '', 'filter' => '#select_filter', 'options' => array('both' => _l('Both'), 'catalog' => _l('Catalog'), 'search' => _l('Search'), 'none' => _l('None')));

// PACK
if (version_compare(_PS_VERSION_, '1.6.1.14', '>='))
{
    $colSettings['cache_is_pack'] = array('text' => _l('Is pack'), 'width' => 45, 'align' => 'center', 'type' => 'coro', 'sort' => 'str', 'color' => '', 'filter' => '#select_filter', 'options' => array(0 => _l('No'), 1 => _l('Yes')));
    $colSettings['pack_stock_type'] = array('text' => _l('Pack quantity'), 'width' => 120, 'align' => 'left', 'type' => 'coro', 'sort' => 'str', 'color' => '', 'filter' => '#select_filter', 'options' => array(0 => _l('Decrement pack only'), 1 => _l('Decrement products in pack only'), 2 => _l('Decrement both'), 3 => _l('Default behavior (see Prestashop Back Office)')));
}

if (version_compare(_PS_VERSION_, '8.0.0', '>='))
{
    $colSettings['redirect_type'] = array('text' => _l('Redirection type'), 'width' => 230, 'align' => 'left', 'type' => 'coro', 'sort' => 'str', 'color' => '', 'filter' => '#select_filter', 'options' => array('-' => '-', 404 => _l('No redirection (404)'), 410 => _l('No redirection (%s)', null, array(410)), '301-product' => _l('Permanent redirection to a product (301)'), '302-product' => _l('Temporary redirection to a product (302)'), '301-category' => _l('Permanent redirection to a category (301)'), '302-category' => _l('Temporary redirection to a category (302)')));
}
elseif (version_compare(_PS_VERSION_, '1.7.1.0', '>='))
{
    $colSettings['id_type_redirected'] = array('text' => _l('id_product_redirected / id_category_redirected'), 'width' => 150, 'align' => 'center', 'type' => 'edtxt', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter');
    $colSettings['redirect_type'] = array('text' => _l('Redirection type'), 'width' => 230, 'align' => 'left', 'type' => 'coro', 'sort' => 'str', 'color' => '', 'filter' => '#select_filter', 'options' => array('-' => '-', 404 => _l('No redirection (404)'), '301-product' => _l('Permanent redirection to a product (301)'), '302-product' => _l('Temporary redirection to a product (302)'), '301-category' => _l('Permanent redirection to a category (301)'), '302-category' => _l('Temporary redirection to a category (302)')));
}
else
{
    $colSettings['id_product_redirected'] = array('text' => _l('id_product_redirected'), 'width' => 65, 'align' => 'center', 'type' => 'edtxt', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter');
    $colSettings['redirect_type'] = array('text' => _l('Redirection type'), 'width' => 120, 'align' => 'left', 'type' => 'coro', 'sort' => 'str', 'color' => '', 'filter' => '#select_filter', 'options' => array('-' => '-', 404 => _l('No redirection (404)'), 301 => _l('Permanent redirection (301)'), 302 => _l('Temporary redirection (302)')));
}

$colSettings['advanced_stock_management'] = array('text' => _l('Advanced Stock Mgmt.'), 'width' => 100, 'align' => 'left', 'type' => 'coro', 'sort' => 'str', 'color' => '', 'filter' => '#select_filter', 'options' => array(1 => _l('Disabled'), 2 => _l('Enabled'), 3 => _l('Enabled + Manual Mgmt')));
$colSettings['quantity_physical'] = array('text' => _l('Physical stock'), 'width' => 60, 'align' => 'right', 'type' => 'ro', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter');
$colSettings['quantity_usable'] = array('text' => _l('Available stock'), 'width' => 60, 'align' => 'right', 'type' => 'ro', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter');
$colSettings['quantity_real'] = array('text' => _l('Live stock'), 'width' => 60, 'align' => 'right', 'type' => 'ro', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter');

$colSettings['last_order'] = array('text' => _l('Last order'), 'width' => 140, 'align' => 'left', 'type' => 'ro', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');

$colSettings['is_virtual'] = array('text' => _l('Is virtual'), 'width' => 45, 'align' => 'center', 'type' => 'coro', 'sort' => 'str', 'color' => '', 'filter' => '#select_filter', 'options' => array(0 => _l('No'), 1 => _l('Yes')));

if (version_compare(_PS_VERSION_, '1.7.3.0', '>='))
{
    $colSettings['additional_delivery_times'] = array('text' => _l('Additional delivery times'), 'width' => 100, 'align' => 'left', 'type' => 'coro', 'sort' => 'str', 'color' => '', 'filter' => '#select_filter', 'options' => array(0 => _l('None'), 1 => _l('Default delivery time'), 2 => _l('Specific delivery time')));
    $colSettings['delivery_in_stock'] = array('text' => _l('Delivery time for products in stock'), 'width' => 140, 'align' => 'left', 'type' => 'ed', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
    $colSettings['delivery_out_stock'] = array('text' => _l('Delivery time for out of stock products'), 'width' => 140, 'align' => 'left', 'type' => 'ed', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');

    $colSettings['low_stock_alert'] = array('text' => _l('Low stock alert'), 'width' => 60, 'align' => 'left', 'type' => 'coro', 'sort' => 'str', 'color' => '', 'filter' => '#select_filter', 'options' => array(0 => _l('No'), 1 => _l('Yes')));
    $colSettings['low_stock_threshold'] = array('text' => _l('Low stock threshold'), 'width' => 60, 'align' => 'left', 'type' => 'edn', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter');
}

$w_name = '';
if (SCAS)
{
    $id_w = SCI::getSelectedWarehouse();
    if (!empty($id_w))
    {
        $w = new Warehouse((int) $id_w);
        $w_name = ' '._l('('.$w->name.')');
    }
}
if (empty($w_name))
{
    $w_name = ' (warehouse)';
}
$colSettings['location_warehouse'] = array('text' => _l('Location').$w_name, 'width' => 100, 'align' => 'left', 'type' => 'edtxt', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');

if (defined('SC_UkooProductCompat_ACTIVE') && SC_UkooProductCompat_ACTIVE == 1 && SCI::moduleIsInstalled('ukoocompat'))
{
    $colSettings['nb_compatibilities'] = array('text' => _l('Nb. compat. associated'), 'width' => 40, 'align' => 'left', 'type' => 'ro', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter');
    $colSettings['compatibilities'] = array('text' => _l('Compatibilities (copy/paste)'), 'width' => 100, 'align' => 'left', 'type' => 'coro', 'sort' => 'na', 'color' => '', 'filter' => '#text_filter');
}
if (defined('SC_FeedBiz_ACTIVE') && SC_FeedBiz_ACTIVE == 1 && SCI::moduleIsInstalled('feedbiz'))
{
    global $id_lang;

    $market_place_allowed = SCI::getFeedBizAllowedMarketPlace();

    $iso = Language::getIsoById($id_lang);
    $shippingTemplates = unserialize(Configuration::get('FEEDBIZ_AMAZON_SHIPPING_GROUP'));
    $shippingTemplate = isset($shippingTemplates[$iso]) ? $shippingTemplates[$iso] : null;
    #product options
    $colSettings['feedbiz'] = array('text' => _l('Feedbiz'), 'width' => 70, 'align' => 'center', 'type' => 'ro', 'sort' => 'str', 'color' => '', 'filter' => '#select_filter');
    $colSettings['fpo_enable_on_product'] = array('text' => _l('Product option exist'), 'width' => 120, 'align' => 'center', 'type' => 'ro', 'sort' => 'int', 'color' => '', 'filter' => '#text_filter');
    $colSettings['fpo_enable_on_attribute'] = array('text' => _l('Product attribute option exist'), 'width' => 120, 'align' => 'left', 'type' => 'ro', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter');
    $colSettings['fpo_id_product_attribute'] = array('text' => _l('ID product attribute'), 'width' => 80, 'align' => 'left', 'type' => 'ro', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter');
    $colSettings['fpo_disable'] = array('text' => _l('Disable'), 'width' => 60, 'align' => 'center', 'type' => 'coro', 'sort' => 'int', 'color' => '', 'filter' => '#select_filter', 'options' => array(0 => _l('No'), 1 => _l('Yes')));
    $colSettings['fpo_force'] = array('text' => _l('Force stock'), 'width' => 60, 'align' => 'center', 'type' => 'edn', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter');
    $colSettings['fpo_price'] = array('text' => _l('Force price'), 'width' => 60, 'align' => 'center', 'type' => 'edn', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter', 'format' => '0.00');
    $colSettings['fpo_shipping'] = array('text' => _l('Force delivery'), 'width' => 70, 'align' => 'center', 'type' => 'edn', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter', 'format' => '0.00');
    $colSettings['fpo_text'] = array('text' => _l('Text'), 'width' => 150, 'align' => 'left', 'type' => 'txt', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
    #amazon options
    if ($market_place_allowed['amazon'])
    {
        $colSettings['feedbiz_amazon'] = array('text' => _l('Feedbiz Amazon'), 'width' => 70, 'align' => 'center', 'type' => 'ro', 'sort' => 'str', 'color' => '', 'filter' => '#select_filter');
        $colSettings['fpao_enable_on_product'] = array('text' => _l('Product option exist').' Amazon', 'width' => 120, 'align' => 'center', 'type' => 'ro', 'sort' => 'int', 'color' => '', 'filter' => '#text_filter');
        $colSettings['fpao_enable_on_attribute'] = array('text' => _l('Product attribute option exist').' Amazon', 'width' => 120, 'align' => 'left', 'type' => 'ro', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter');
        $colSettings['fpao_id_product_attribute'] = array('text' => _l('ID product attribute'), 'width' => 80, 'align' => 'left', 'type' => 'ro', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter');
        $colSettings['fpao_disable'] = array('text' => _l('Disable'), 'width' => 60, 'align' => 'center', 'type' => 'coro', 'sort' => 'int', 'color' => '', 'filter' => '#select_filter', 'options' => array(0 => _l('No'), 1 => _l('Yes')));
        $colSettings['fpao_force'] = array('text' => _l('Force stock'), 'width' => 60, 'align' => 'center', 'type' => 'edn', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter');
        $colSettings['fpao_price'] = array('text' => _l('Force price'), 'width' => 60, 'align' => 'center', 'type' => 'edn', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter', 'format' => '0.00');
        $colSettings['fpao_shipping'] = array('text' => _l('Force delivery'), 'width' => 70, 'align' => 'center', 'type' => 'edn', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter', 'format' => '0.00');
        $colSettings['fpao_text'] = array('text' => _l('Text'), 'width' => 150, 'align' => 'left', 'type' => 'txt', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
        $colSettings['fpao_nopexport'] = array('text' => _l('Don\'t synchronize price'), 'width' => 60, 'align' => 'center', 'type' => 'coro', 'sort' => 'int', 'color' => '', 'filter' => '#select_filter', 'options' => array(0 => _l('No'), 1 => _l('Yes')));
        $colSettings['fpao_noqexport'] = array('text' => _l('Don\'t synchronize quantity'), 'width' => 60, 'align' => 'center', 'type' => 'coro', 'sort' => 'int', 'color' => '', 'filter' => '#select_filter', 'options' => array(0 => _l('No'), 1 => _l('Yes')));
        $colSettings['fpao_fba'] = array('text' => _l('Amazon expedition (FBA)'), 'width' => 60, 'align' => 'center', 'type' => 'coro', 'sort' => 'int', 'color' => '', 'filter' => '#select_filter', 'options' => array(0 => _l('No'), 1 => _l('Yes')));
        $colSettings['fpao_fba_value'] = array('text' => _l('FBA added value'), 'width' => 60, 'align' => 'center', 'type' => 'edn', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter', 'format' => '0');
        $colSettings['fpao_asin1'] = array('text' => _l('ASIN1'), 'width' => 60, 'align' => 'left', 'type' => 'txt', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
        $colSettings['fpao_asin2'] = array('text' => _l('ASIN2'), 'width' => 60, 'align' => 'left', 'type' => 'txt', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
        $colSettings['fpao_asin3'] = array('text' => _l('ASIN3'), 'width' => 60, 'align' => 'left', 'type' => 'txt', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
        $colSettings['fpao_bullet_point1'] = array('text' => _l('Bullet point 1'), 'width' => 150, 'align' => 'left', 'type' => 'txt', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
        $colSettings['fpao_bullet_point2'] = array('text' => _l('Bullet point 2'), 'width' => 150, 'align' => 'left', 'type' => 'txt', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
        $colSettings['fpao_bullet_point3'] = array('text' => _l('Bullet point 3'), 'width' => 150, 'align' => 'left', 'type' => 'txt', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
        $colSettings['fpao_bullet_point4'] = array('text' => _l('Bullet point 4'), 'width' => 150, 'align' => 'left', 'type' => 'txt', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
        $colSettings['fpao_bullet_point5'] = array('text' => _l('Bullet point 5'), 'width' => 150, 'align' => 'left', 'type' => 'txt', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
        $colSettings['fpao_shipping_type'] = array('text' => _l('Bullet point 5'), 'width' => 60, 'align' => 'center', 'type' => 'coro', 'sort' => 'int', 'color' => '', 'filter' => '#select_filter', 'options' => array(0 => _l('-'), 1 => _l('Standard'), 2 => _l('Express')));
        $colSettings['fpao_gift_wrap'] = array('text' => _l('Gift wrap'), 'width' => 60, 'align' => 'center', 'type' => 'coro', 'sort' => 'int', 'color' => '', 'filter' => '#select_filter', 'options' => array(0 => _l('No'), 1 => _l('Yes')));
        $colSettings['fpao_gift_message'] = array('text' => _l('Gift message'), 'width' => 60, 'align' => 'center', 'type' => 'coro', 'sort' => 'int', 'color' => '', 'filter' => '#select_filter', 'options' => array(0 => _l('No'), 1 => _l('Yes')));
        $colSettings['fpao_browsenode'] = array('text' => _l('Browse node'), 'width' => 60, 'align' => 'center', 'type' => 'edn', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter', 'format' => '0');
        $colSettings['fpao_repricing_min'] = array('text' => _l('Repricing min'), 'width' => 70, 'align' => 'center', 'type' => 'edn', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter', 'format' => '0.00');
        $colSettings['fpao_repricing_max'] = array('text' => _l('Repricing max'), 'width' => 70, 'align' => 'center', 'type' => 'edn', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter', 'format' => '0.00');
        $colSettings['fpao_repricing_gap'] = array('text' => _l('Repricing gap'), 'width' => 70, 'align' => 'center', 'type' => 'edn', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter', 'format' => '0.00');
        $colSettings['fpao_shipping_group'] = array('text' => _l('Expedition template'), 'width' => 60, 'align' => 'center', 'type' => 'coro', 'sort' => 'int', 'color' => '', 'filter' => '#select_filter', 'options' => $shippingTemplate);
    }
    if ($market_place_allowed['cdiscount'])
    {
        $colSettings['feedbiz_cdiscount'] = array('text' => _l('Feedbiz Cdiscount'), 'width' => 70, 'align' => 'center', 'type' => 'ro', 'sort' => 'str', 'color' => '', 'filter' => '#select_filter');
        $colSettings['fpco_enable_on_product'] = array('text' => _l('Product option exist').' Cdiscount', 'width' => 120, 'align' => 'center', 'type' => 'ro', 'sort' => 'int', 'color' => '', 'filter' => '#text_filter');
        $colSettings['fpco_enable_on_attribute'] = array('text' => _l('Product attribute option exist').' Cdiscount', 'width' => 120, 'align' => 'left', 'type' => 'ro', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter');
        $colSettings['fpco_id_product_attribute'] = array('text' => _l('ID product attribute'), 'width' => 80, 'align' => 'left', 'type' => 'ro', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter');
        $colSettings['fpco_disable'] = array('text' => _l('Disable'), 'width' => 60, 'align' => 'center', 'type' => 'coro', 'sort' => 'int', 'color' => '', 'filter' => '#select_filter', 'options' => array(0 => _l('No'), 1 => _l('Yes')));
        $colSettings['fpco_force'] = array('text' => _l('Force stock'), 'width' => 60, 'align' => 'center', 'type' => 'edn', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter');
        $colSettings['fpco_price'] = array('text' => _l('Force price'), 'width' => 60, 'align' => 'center', 'type' => 'edn', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter', 'format' => '0.00');
        $colSettings['fpco_price_up'] = array('text' => _l('Force price up'), 'width' => 60, 'align' => 'center', 'type' => 'edn', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter', 'format' => '0.00');
        $colSettings['fpco_price_down'] = array('text' => _l('Force price down'), 'width' => 60, 'align' => 'center', 'type' => 'edn', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter', 'format' => '0.00');
        $colSettings['fpco_shipping'] = array('text' => _l('Shipping'), 'width' => 70, 'align' => 'center', 'type' => 'edn', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter', 'format' => '0.00');
        $colSettings['fpco_shipping_delay'] = array('text' => _l('Shipping delay'), 'width' => 70, 'align' => 'center', 'type' => 'edn', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter', 'format' => '0.00');
        $colSettings['fpco_clogistique'] = array('text' => _l('Clogistique'), 'width' => 70, 'align' => 'center', 'type' => 'coro', 'sort' => 'int', 'color' => '', 'filter' => '#select_filter', 'options' => array(0 => _l('No'), 1 => _l('Yes')));
        $colSettings['fpco_valueadded'] = array('text' => _l('Added value'), 'width' => 70, 'align' => 'center', 'type' => 'edn', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter', 'format' => '0.00');
        $colSettings['fpco_text'] = array('text' => _l('Text'), 'width' => 150, 'align' => 'left', 'type' => 'txt', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
    }
}
if (defined('SC_Amazon_ACTIVE') && SC_Amazon_ACTIVE == 1 && SCI::moduleIsInstalled('amazon'))
{
    $colSettings['amazon'] = array('text' => _l('Amazon'), 'width' => 70, 'align' => 'center', 'type' => 'ro', 'sort' => 'str', 'color' => '', 'filter' => '#select_filter');
}
if (defined('SC_Cdiscount_ACTIVE') && SC_Cdiscount_ACTIVE == 1 && SCI::moduleIsInstalled('cdiscount'))
{
    $colSettings['cdiscount'] = array('text' => _l('Cdiscount'), 'width' => 70, 'align' => 'center', 'type' => 'ro', 'sort' => 'str', 'color' => '', 'filter' => '#select_filter');
}
if (version_compare(_PS_VERSION_, '1.7.8.0', '>='))
{
    $colSettings['product_type'] = array('text' => _l('Type'), 'width' => 120, 'align' => 'left', 'type' => 'ro', 'sort' => 'str', 'color' => '', 'filter' => '#select_filter');
}
