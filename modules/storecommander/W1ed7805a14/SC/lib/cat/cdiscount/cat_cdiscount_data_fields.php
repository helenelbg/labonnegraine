<?php

$colSettings = array();
#cdiscount options
$colSettings['id_product'] = array('text' => _l('ID product'), 'width' => 80, 'align' => 'left', 'type' => 'ro', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter');
$colSettings['name'] = array('text' => _l('Name'), 'width' => 200, 'align' => 'left', 'type' => 'ro', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
$colSettings['force'] = array('text' => _l('Force stock'), 'width' => 70, 'align' => 'center', 'type' => 'coro', 'sort' => 'int', 'color' => '', 'filter' => '#select_filter', 'options' => array(0 => _l('No'), 1 => _l('Yes')));
$colSettings['price_inc_tax'] = array('text' => _l('Price incl. Tax'), 'width' => 60, 'align' => 'center', 'type' => 'ro', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter', 'format' => '0.00');
$colSettings['disable'] = array('text' => _l('Active'), 'width' => 70, 'align' => 'center', 'type' => 'coro', 'sort' => 'int', 'color' => '', 'filter' => '#select_filter', 'options' => array(1 => _l('No'), 0 => _l('Yes')));
$colSettings['price'] = array('text' => _l('Replace price'), 'width' => 70, 'align' => 'center', 'type' => 'edn', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter', 'format' => '0.00');
$colSettings['shipping'] = array('text' => _l('Shipping fee'), 'width' => 70, 'align' => 'center', 'type' => 'edn', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter', 'format' => '0.00');
$colSettings['shipping_delay'] = array('text' => _l('Shipping delay'), 'width' => 70, 'align' => 'center', 'type' => 'edn', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter', 'format' => '0');
$colSettings['clogistique'] = array('text' => _l('Clogistique'), 'width' => 70, 'align' => 'center', 'type' => 'coro', 'sort' => 'int', 'color' => '', 'filter' => '#select_filter', 'options' => array(0 => _l('No'), 1 => _l('Yes')));
$colSettings['valueadded'] = array('text' => _l('Added value'), 'width' => 70, 'align' => 'center', 'type' => 'edn', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter', 'format' => '0.00');
$colSettings['text'] = array('text' => _l('Text'), 'width' => 150, 'align' => 'left', 'type' => 'txt', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
