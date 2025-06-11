<?php

$colSettings['id'] = array('text' => _l('ID'), 'width' => 50, 'align' => 'left', 'type' => 'ro', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
$colSettings['name'] = array('text' => _l('Supplier'), 'width' => 200, 'align' => 'left', 'type' => 'ro', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
$colSettings['present'] = array('text' => _l('Present'), 'width' => 80, 'align' => 'center', 'type' => 'ch', 'sort' => 'int', 'color' => '', 'filter' => '#select_filter');
$colSettings['default'] = array('text' => _l('Default'), 'width' => 50, 'align' => 'center', 'type' => 'ra', 'sort' => 'str', 'color' => '', 'filter' => '#select_filter');

$colSettings['product_supplier_reference'] = array('text' => _l('Supplier reference'), 'width' => 100, 'align' => 'left', 'type' => 'ed', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
$colSettings['product_supplier_price_te'] = array('text' => _l('Wholesale price'), 'width' => 100, 'align' => 'right', 'type' => 'ed', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
$colSettings['id_currency'] = array('text' => _l('Currency'), 'width' => 80, 'align' => 'right', 'type' => 'coro', 'sort' => 'int', 'color' => '', 'filter' => '#select_filter', 'options' => $currencies);
