<?php

$colSettings['id_specific_price'] = array('text' => _l('ID'), 'width' => 40, 'align' => 'right', 'type' => 'ro', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter');
$colSettings['id_product'] = array('text' => _l('id_product'), 'width' => 40, 'align' => 'right', 'type' => 'ro', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter');
$colSettings['id_product_attribute'] = array('text' => _l('id_product_attribute'), 'width' => 40, 'align' => 'right', 'type' => 'ro', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter');
$colSettings['id_customer'] = array('text' => _l('Customer'), 'width' => 100, 'align' => 'left', 'type' => 'combo', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
$colSettings['reference'] = array('text' => _l('Ref'), 'width' => 80, 'align' => 'left', 'type' => 'ro', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
$colSettings['name'] = array('text' => _l('Name'), 'width' => 120, 'align' => 'left', 'type' => 'ro', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
$colSettings['id_shop'] = array('text' => _l('Shop'), 'width' => 50, 'align' => 'left', 'type' => 'coro', 'sort' => 'str', 'color' => '', 'filter' => '#select_filter', 'options' => $shops);
$colSettings['id_shop_group'] = array('text' => _l('Shop group'), 'width' => 50, 'align' => 'left', 'type' => 'coro', 'sort' => 'str', 'color' => '', 'filter' => '#select_filter', 'options' => $group_shops);
$colSettings['id_group'] = array('text' => _l('Customer group'), 'width' => 50, 'align' => 'left', 'type' => 'coro', 'sort' => 'str', 'color' => '', 'filter' => '#select_filter', 'options' => $groups);
$colSettings['from_quantity'] = array('text' => _l('Minimum quantity'), 'width' => 50, 'align' => 'right', 'type' => 'edn', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter');
if (_s('APP_COMPAT_MODULE_PPE'))
{
    $colSettings['from_quantity'] = array('text' => _l('Minimum quantity'), 'width' => 50, 'align' => 'right', 'type' => 'edn', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter', 'format' => '0.000000');
}
$colSettings['price'] = array('text' => _l('Fixed price excl. Tax'), 'width' => 70, 'align' => 'right', 'type' => 'edn', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter');
$colSettings['reduction'] = array('text' => _l('Reduction'), 'width' => 65, 'align' => 'right', 'type' => 'ed', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
if (version_compare(_PS_VERSION_, '1.6.0.11', '>='))
{
    $colSettings['reduction_tax'] = array('text' => _l('Reduction tax'), 'width' => 70, 'align' => 'right', 'type' => 'coro', 'sort' => 'str', 'color' => '', 'filter' => '#select_filter', 'options' => array(0 => _l('Excl. tax'), 1 => _l('Incl. tax')));
}
else
{
    $colSettings['reduction_tax'] = array('text' => _l('Reduction tax'), 'width' => 70, 'align' => 'right', 'type' => 'ro', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
}
$colSettings['from'] = array('text' => _l('Reduction from'), 'width' => 140, 'align' => 'left', 'type' => 'dhxCalendarA', 'sort' => 'date', 'color' => '', 'filter' => '#select_filter');
$colSettings['to'] = array('text' => _l('Reduction to'), 'width' => 140, 'align' => 'left', 'type' => 'dhxCalendarA', 'sort' => 'date', 'color' => '', 'filter' => '#select_filter');
$colSettings['from_num'] = array('text' => _l('Reduction from').' '._l('(num)'), 'width' => 70, 'align' => 'right', 'type' => 'ro', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
$colSettings['to_num'] = array('text' => _l('Reduction to').' '._l('(num)'), 'width' => 70, 'align' => 'right', 'type' => 'ro', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
$colSettings['id_country'] = array('text' => _l('Country'), 'width' => 50, 'align' => 'left', 'type' => 'coro', 'sort' => 'str', 'color' => '', 'filter' => '#select_filter', 'options' => $countries);
$colSettings['id_currency'] = array('text' => _l('Currency'), 'width' => 50, 'align' => 'left', 'type' => 'coro', 'sort' => 'str', 'color' => '', 'filter' => '#select_filter', 'options' => $currencies);
$colSettings['price_with_reduction_tax_excl'] = array('text' => _l('Price tax excl after reduction'), 'width' => 65, 'align' => 'left', 'type' => 'edn', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter', 'format' => '0.000000');
$colSettings['price_with_reduction_tax_incl'] = array('text' => _l('Price tax incl after reduction'), 'width' => 65, 'align' => 'left', 'type' => 'edn', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter', 'format' => '0.000000');

// GE
$colSettings['image'] = array('text' => _l('Image'), 'width' => 60, 'align' => 'center', 'type' => 'ro', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
$colSettings['supplier_reference'] = array('text' => _l('Supplier Ref.'), 'width' => 80, 'align' => 'left', 'type' => 'ro', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
$colSettings['ean13'] = array('text' => _l('EAN13'), 'width' => 100, 'align' => 'left', 'type' => 'ro', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
$colSettings['upc'] = array('text' => _l('UPC'), 'width' => 100, 'align' => 'left', 'type' => 'ro', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
$colSettings['active'] = array('text' => _l('Active'), 'width' => 45, 'align' => 'center', 'type' => 'ro', 'sort' => 'str', 'color' => '', 'filter' => '#select_filter', 'options' => array(0 => _l('No'), 1 => _l('Yes')));
$colSettings['price_exl_tax'] = array('text' => _l('Price excl. Tax'), 'width' => 65, 'align' => 'right', 'type' => 'ro', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter', 'format' => '0.00');
$colSettings['price_inc_tax'] = array('text' => _l('Price incl. Tax'), 'width' => 65, 'align' => 'right', 'type' => 'ro', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter', 'format' => '0.00');
$colSettings['id_manufacturer'] = array('text' => _l('Manufacturer'), 'width' => 100, 'align' => 'left', 'type' => 'ro', 'sort' => 'str', 'color' => '', 'filter' => '#select_filter');
$colSettings['id_supplier'] = array('text' => _l('Supplier'), 'width' => 100, 'align' => 'left', 'type' => 'ro', 'sort' => 'str', 'color' => '', 'filter' => '#select_filter');
$colSettings['id_specific_price_rule'] = array('text' => _l('Type'), 'width' => 100, 'align' => 'left', 'type' => 'ro', 'sort' => 'int', 'color' => '', 'filter' => '#select_filter');
