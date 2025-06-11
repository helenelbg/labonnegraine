<?php

$colSettings = array();
#amazon options
$colSettings['region'] = array('text' => _l('Region'), 'width' => 60, 'align' => 'center', 'type' => 'ro', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
$colSettings['id_product'] = array('text' => _l('id_product'), 'width' => 80, 'align' => 'left', 'type' => 'ro', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter');
$colSettings['name'] = array('text' => _l('Name'), 'width' => 200, 'align' => 'left', 'type' => 'ro', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
$colSettings['attribute_name'] = array('text' => _l('Attribute name'), 'width' => 200, 'align' => 'left', 'type' => 'ro', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
$colSettings['id_product_attribute'] = array('text' => _l('id_product_attribute'), 'width' => 80, 'align' => 'left', 'type' => 'ro', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter');
$colSettings['latency'] = array('text' => _l('Latency'), 'width' => 80, 'align' => 'center', 'type' => 'edn', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter');
$colSettings['disable'] = array('text' => _l('Active'), 'width' => 50, 'align' => 'center', 'type' => 'coro', 'sort' => 'int', 'color' => '', 'filter' => '#select_filter', 'options' => array(-1 => _l('No'), 0 => _l('Yes'), 1 => _l('Force disable')));
$colSettings['force'] = array('text' => _l('Force stock'), 'width' => 60, 'align' => 'center', 'type' => 'coro', 'sort' => 'int', 'color' => '', 'filter' => '#select_filter', 'options' => array(0 => _l('No'), 1 => _l('Yes')));
$colSettings['send_to_creation'] = array('text' => _l('Send to creation'), 'width' => 60, 'align' => 'center', 'type' => 'ro', 'sort' => 'int', 'color' => '', 'filter' => '#select_filter');
$colSettings['price'] = array('text' => _l('Force price'), 'width' => 60, 'align' => 'center', 'type' => 'edn', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter', 'format' => '0.00');
$colSettings['price_inc_tax'] = array('text' => _l('Price incl. Tax'), 'width' => 60, 'align' => 'center', 'type' => 'ro', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter', 'format' => '0.00');
$colSettings['shipping'] = array('text' => _l('Force shipping cost'), 'width' => 70, 'align' => 'center', 'type' => 'edn', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter', 'format' => '0.00');
$colSettings['text'] = array('text' => _l('State Text'), 'width' => 150, 'align' => 'left', 'type' => 'txt', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
$colSettings['nopexport'] = array('text' => _l('Don\'t synchronize price'), 'width' => 100, 'align' => 'center', 'type' => 'coro', 'sort' => 'int', 'color' => '', 'filter' => '#select_filter', 'options' => array(0 => _l('Synchronize'), 1 => _l('Don\'t synchronize')));
$colSettings['noqexport'] = array('text' => _l('Don\'t synchronize quantity'), 'width' => 100, 'align' => 'center', 'type' => 'coro', 'sort' => 'int', 'color' => '', 'filter' => '#select_filter', 'options' => array(0 => _l('Synchronize'), 1 => _l('Don\'t synchronize')));
$colSettings['fba'] = array('text' => _l('Shipped by Amazon (FBA)'), 'width' => 60, 'align' => 'center', 'type' => 'coro', 'sort' => 'int', 'color' => '', 'filter' => '#select_filter', 'options' => array(0 => _l('No'), 1 => _l('Yes')));
$colSettings['fba_value'] = array('text' => _l('FBA added value'), 'width' => 60, 'align' => 'center', 'type' => 'edn', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter', 'format' => '0');
$colSettings['asin1'] = array('text' => _l('ASIN1'), 'width' => 60, 'align' => 'left', 'type' => 'edtxt', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
$colSettings['asin2'] = array('text' => _l('ASIN2'), 'width' => 60, 'align' => 'left', 'type' => 'edtxt', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
$colSettings['asin3'] = array('text' => _l('ASIN3'), 'width' => 60, 'align' => 'left', 'type' => 'edtxt', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
$colSettings['bullet_point1'] = array('text' => _l('Bullet point 1'), 'width' => 150, 'align' => 'left', 'type' => 'txt', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
$colSettings['bullet_point2'] = array('text' => _l('Bullet point 2'), 'width' => 150, 'align' => 'left', 'type' => 'txt', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
$colSettings['bullet_point3'] = array('text' => _l('Bullet point 3'), 'width' => 150, 'align' => 'left', 'type' => 'txt', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
$colSettings['bullet_point4'] = array('text' => _l('Bullet point 4'), 'width' => 150, 'align' => 'left', 'type' => 'txt', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
$colSettings['bullet_point5'] = array('text' => _l('Bullet point 5'), 'width' => 150, 'align' => 'left', 'type' => 'txt', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
$colSettings['shipping_type'] = array('text' => _l('Shipping type'), 'width' => 80, 'align' => 'center', 'type' => 'coro', 'sort' => 'int', 'color' => '', 'filter' => '#select_filter', 'options' => array(0 => _l('-'), 1 => _l('Standard'), 2 => _l('Express')));
$colSettings['gift_wrap'] = array('text' => _l('Gift wrap'), 'width' => 60, 'align' => 'center', 'type' => 'coro', 'sort' => 'int', 'color' => '', 'filter' => '#select_filter', 'options' => array(0 => _l('No'), 1 => _l('Yes')));
$colSettings['gift_message'] = array('text' => _l('Gift message'), 'width' => 60, 'align' => 'center', 'type' => 'coro', 'sort' => 'int', 'color' => '', 'filter' => '#select_filter', 'options' => array(0 => _l('No'), 1 => _l('Yes')));
$colSettings['browsenode'] = array('text' => _l('Browse node'), 'width' => 80, 'align' => 'center', 'type' => 'edn', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter', 'format' => '0');
$colSettings['repricing_min'] = array('text' => _l('Repricing min'), 'width' => 70, 'align' => 'center', 'type' => 'edn', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter', 'format' => '0.00');
$colSettings['repricing_max'] = array('text' => _l('Repricing max'), 'width' => 70, 'align' => 'center', 'type' => 'edn', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter', 'format' => '0.00');
$colSettings['repricing_gap'] = array('text' => _l('Repricing gap'), 'width' => 70, 'align' => 'center', 'type' => 'edn', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter', 'format' => '0.00');
$colSettings['amazon_price'] = array('text' => _l('Amazon price'), 'width' => 70, 'align' => 'left', 'type' => 'ro', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter', 'format' => '0.00');
$colSettings['price_rule'] = array('text' => _l('Price rule applied'), 'width' => 60, 'align' => 'left', 'type' => 'ro', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
if (!empty($alternate_fields_enable))
{
    $colSettings['alternative_title'] = array('text' => _l('Alternate title'), 'width' => 150, 'align' => 'left', 'type' => 'edtxt', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
    $colSettings['alternative_description'] = array('text' => _l('Alternate description'), 'width' => 150, 'align' => 'left', 'type' => 'txttxt', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
}
