<?php

$colSettings['id_order_detail'] = array('text' => _l('id order detail'), 'width' => 45, 'align' => 'right', 'type' => 'ro', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter');
$colSettings['id_order'] = array('text' => _l('id order'), 'width' => 45, 'align' => 'right', 'type' => 'ro', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter');
$colSettings['product_id'] = array('text' => _l('Product ID'), 'width' => 45, 'align' => 'right', 'type' => 'ro', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
$colSettings['product_attribute_id'] = array('text' => _l('id product attribute'), 'width' => 45, 'align' => 'right', 'type' => 'ro', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
$colSettings['image'] = array('text' => _l('Image'), 'width' => 60, 'align' => 'center', 'type' => 'ro', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
$colSettings['product_name'] = array('text' => _l('Name'), 'width' => 200, 'align' => 'left', 'type' => 'edtxt', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
$colSettings['product_quantity'] = array('text' => _l('Quantity orded'), 'width' => 45, 'align' => 'right', 'type' => 'edtxt', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter');
$colSettings['actual_quantity_in_stock'] = array('text' => _l('Current qty in stock'), 'width' => 45, 'align' => 'right', 'type' => 'ron', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter');
$colSettings['product_quantity_in_stock'] = array('text' => _l('Quantity in stock at the time of the order'), 'width' => 50, 'align' => 'right', 'type' => 'ro', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
$colSettings['in_stock'] = array('text' => _l('In stock'), 'width' => 45, 'align' => 'center', 'type' => 'ro', 'sort' => 'int', 'color' => '', 'filter' => '#select_filter');
$colSettings['product_quantity_refunded'] = array('text' => _l('Qty refunded'), 'width' => 45, 'align' => 'right', 'type' => 'ron', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter');
$colSettings['product_quantity_return'] = array('text' => _l('Qty returned'), 'width' => 45, 'align' => 'right', 'type' => 'ro', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter');
$colSettings['product_price'] = array('text' => _l('Price excl. Tax'), 'width' => 70, 'align' => 'right', 'type' => 'edn', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter', 'format' => '0.00');
$colSettings['product_ean13'] = array('text' => _l('EAN13'), 'width' => 70, 'align' => 'left', 'type' => 'edtxt', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
$colSettings['product_upc'] = array('text' => _l('UPC'), 'width' => 70, 'align' => 'left', 'type' => 'edtxt', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
$colSettings['product_reference'] = array('text' => _l('Reference'), 'width' => 70, 'align' => 'left', 'type' => 'edtxt', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
$colSettings['product_supplier_reference'] = array('text' => _l('Supplier reference'), 'width' => 70, 'align' => 'left', 'type' => 'edtxt', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
$colSettings['product_weight'] = array('text' => _l('Weight'), 'width' => 50, 'align' => 'right', 'type' => 'edn', 'sort' => 'int', 'color' => '', 'filter' => '#text_filter');

if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
{
    $colSettings['original_product_price'] = array('text' => _l('Original price excl. Tax'), 'width' => 70, 'align' => 'right', 'type' => 'ro', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter', 'format' => '0.00');
}

if (version_compare(_PS_VERSION_, '1.7.0.0', '>='))
{
    $colSettings['product_mpn'] = array('text' => _l('MPN'), 'width' => 70, 'align' => 'left', 'type' => 'edtxt', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
    $colSettings['product_isbn'] = array('text' => _l('ISBN'), 'width' => 70, 'align' => 'left', 'type' => 'edtxt', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
}
