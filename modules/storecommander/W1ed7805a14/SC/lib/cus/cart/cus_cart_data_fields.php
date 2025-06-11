<?php

$colSettings = array();
#cdiscount options
$colSettings['id_cart'] = array('text' => _l('ID cart'), 'width' => 60, 'align' => 'right', 'type' => 'ro', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter');
$colSettings['id_customer'] = array('text' => _l('id customer'), 'width' => 60, 'align' => 'right', 'type' => 'ro', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter');
$colSettings['email'] = array('text' => _l('Email'), 'width' => 120, 'align' => 'left', 'type' => 'ed', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
$colSettings['product_date_add'] = array('text' => _l('Product date added'), 'width' => 140, 'align' => 'left', 'type' => 'ro', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
$colSettings['id_product'] = array('text' => _l('ID product'), 'width' => 60, 'align' => 'right', 'type' => 'ro', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter');
$colSettings['id_product_attribute'] = array('text' => _l('ID product attribute'), 'width' => 60, 'align' => 'right', 'type' => 'ro', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter');
$colSettings['stock_available'] = array('text' => _l('Stock available'), 'width' => 60, 'align' => 'right', 'type' => 'ro', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter', 'footer' => '#stat_total');
$colSettings['quantity'] = array('text' => _l('Quantity'), 'width' => 60, 'align' => 'right', 'type' => 'ro', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter', 'footer' => '#stat_total');
$colSettings['product_name'] = array('text' => _l('Product name'), 'width' => 250, 'align' => 'left', 'type' => 'ro', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
