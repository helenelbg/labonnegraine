<?php

$colSettings['id_specific_price'] = array('text' => _l('ID'), 'width' => 40, 'align' => 'right', 'type' => 'ro', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter');
$colSettings['id_product'] = array('text' => _l('id_product'), 'width' => 40, 'align' => 'right', 'type' => 'ro', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter');
$colSettings['id_product_attribute'] = array('text' => _l('id_product_attribute'), 'width' => 40, 'align' => 'right', 'type' => 'ro', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter');
$colSettings['reference'] = array('text' => _l('Ref'), 'width' => 80, 'align' => 'left', 'type' => 'ro', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
$colSettings['name'] = array('text' => _l('Name'), 'width' => 120, 'align' => 'left', 'type' => 'ro', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
$colSettings['manufacturer'] = array('text' => _l('Manufacturer'), 'width' => 100, 'align' => 'left', 'type' => 'ro', 'sort' => 'str', 'color' => '', 'filter' => '#select_filter');
$colSettings['supplier'] = array('text' => _l('Supplier'), 'width' => 100, 'align' => 'left', 'type' => 'ro', 'sort' => 'str', 'color' => '', 'filter' => '#select_filter');
$colSettings['shop_id'] = array('text' => _l('ID Shop'), 'width' => 50, 'align' => 'center', 'type' => 'ro', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter');
$colSettings['id_shop'] = array('text' => _l('Shop'), 'width' => 50, 'align' => 'left', 'type' => 'coro', 'sort' => 'str', 'color' => '', 'filter' => '#select_filter', 'options' => $shops);
$colSettings['id_shop_group'] = array('text' => _l('Shop group'), 'width' => 50, 'align' => 'left', 'type' => 'coro', 'sort' => 'str', 'color' => '', 'filter' => '#select_filter', 'options' => $group_shops);
$colSettings['id_group'] = array('text' => _l('Customer group'), 'width' => 50, 'align' => 'left', 'type' => 'coro', 'sort' => 'str', 'color' => '', 'filter' => '#select_filter', 'options' => $groups);
$colSettings['from_quantity'] = array('text' => _l('Minimum quantity'), 'width' => 50, 'align' => 'right', 'type' => 'edn', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter');
if (_s('APP_COMPAT_MODULE_PPE'))
{
    $colSettings['from_quantity'] = array('text' => _l('Minimum quantity'), 'width' => 50, 'align' => 'right', 'type' => 'edn', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter', 'format' => '0.000000');
}
$colSettings['price'] = array('text' => _l('Fixed price excl. Tax'), 'width' => 50, 'align' => 'right', 'type' => 'edn', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter');
$colSettings['reduction_tax'] = array('text' => _l('Tax'), 'width' => 50, 'align' => 'right', 'type' => 'coro', 'sort' => 'str', 'color' => '', 'filter' => '#select_filter', 'options' => array(0 => _l('Excl. tax'), 1 => _l('Incl. tax')));
$colSettings['reduction_price'] = array('text' => _l('Reduction amount'), 'width' => 60, 'align' => 'right', 'type' => 'co', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter');
$colSettings['reduction_percent'] = array('text' => _l('Reduction %'), 'width' => 60, 'align' => 'right', 'type' => 'co', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter');
$colSettings['margin_wt_amount_after_reduction'] = array('text' => _l('Margin amount after reduction'), 'width' => 60, 'align' => 'right', 'type' => 'ro', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter');
$colSettings['margin_wt_percent_after_reduction'] = array('text' => _l('Margin % after reduction'), 'width' => 60, 'align' => 'right', 'type' => 'ro', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter');
$colSettings['margin_after_reduction'] = array('text' => _l('Margin after reduction'), 'width' => 60, 'align' => 'right', 'type' => 'ro', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter');
$colSettings['price_with_reduction_tax_excl'] = array('text' => _l('Price tax excl after reduction'), 'width' => 60, 'align' => 'right', 'type' => 'edn', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter');
$colSettings['price_with_reduction_tax_incl'] = array('text' => _l('Price tax incl after reduction'), 'width' => 60, 'align' => 'right', 'type' => 'edn', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter');
$colSettings['from'] = array('text' => _l('Reduction from'), 'width' => 140, 'align' => 'left', 'type' => 'dhxCalendarA', 'sort' => 'date', 'color' => '', 'filter' => '#select_filter');
$colSettings['to'] = array('text' => _l('Reduction to'), 'width' => 140, 'align' => 'left', 'type' => 'dhxCalendarA', 'sort' => 'date', 'color' => '', 'filter' => '#select_filter');
$colSettings['from_num'] = array('text' => _l('Reduction from').' '._l('(num)'), 'width' => 70, 'align' => 'right', 'type' => 'ro', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
$colSettings['to_num'] = array('text' => _l('Reduction to').' '._l('(num)'), 'width' => 70, 'align' => 'right', 'type' => 'ro', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
$colSettings['id_country'] = array('text' => _l('Country'), 'width' => 50, 'align' => 'left', 'type' => 'coro', 'sort' => 'str', 'color' => '', 'filter' => '#select_filter', 'options' => $countries);
$colSettings['id_currency'] = array('text' => _l('Currency'), 'width' => 50, 'align' => 'left', 'type' => 'coro', 'sort' => 'str', 'color' => '', 'filter' => '#select_filter', 'options' => $currencies);
$colSettings['on_sale'] = array('text' => _l('On sale'), 'width' => 60, 'align' => 'left', 'type' => 'coro', 'sort' => 'str', 'color' => '', 'filter' => '#select_filter', 'options' => array(0 => _l('No'), 1 => _l('Yes')));

// GE
$colSettings['image'] = array('text' => _l('Image'), 'width' => 60, 'align' => 'center', 'type' => 'ro', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
$colSettings['supplier_reference'] = array('text' => _l('Supplier Ref.'), 'width' => 80, 'align' => 'left', 'type' => 'ro', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
$colSettings['ean13'] = array('text' => _l('EAN13'), 'width' => 100, 'align' => 'left', 'type' => 'ro', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
$colSettings['upc'] = array('text' => _l('UPC'), 'width' => 100, 'align' => 'left', 'type' => 'ro', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
$colSettings['active'] = array('text' => _l('Active'), 'width' => 45, 'align' => 'center', 'type' => 'ro', 'sort' => 'str', 'color' => '', 'filter' => '#select_filter');
$colSettings['id_customer'] = array('text' => _l('Customer'), 'width' => 45, 'align' => 'center', 'type' => 'ro', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
