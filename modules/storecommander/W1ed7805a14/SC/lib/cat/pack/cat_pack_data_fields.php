<?php


$colSettings['id'] = array('text' => _l('ID'), 'width' => 40, 'align' => 'right', 'type' => 'ro', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter');

$colSettings['active'] = array('text' => _l('Active'), 'width' => 45, 'align' => 'center', 'type' => 'ro', 'sort' => 'int', 'color' => '', 'filter' => '#select_filter', 'options' => array(0 => _l('No'), 1 => _l('Yes')), 'onlyforgrids' => array('grid_proppackproduct'));
$colSettings['id_image'] = array('text' => _l('Image'), 'width' => 60, 'align' => 'center', 'type' => 'ro', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter', 'onlyforgrids' => array('grid_proppackproduct'));
$colSettings['reference'] = array('text' => _l('Reference'), 'width' => 80, 'align' => 'left', 'type' => 'ro', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
$colSettings['name'] = array('text' => _l('Name'), 'width' => 120, 'align' => 'left', 'type' => 'ro', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');

$colSettings['quantity'] = array('text' => _l('Quantity in pack'), 'width' => 80, 'align' => 'left', 'type' => 'edn', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter', 'footer' => '#stat_total');
$colSettings['stock_available'] = array('text' => _l('Stock available'), 'width' => 80, 'align' => 'left', 'type' => 'ro', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter');


$colSettings['ean13'] = array('text' => _l('EAN13'), 'width' => 100, 'align' => 'left', 'type' => 'ro', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
$colSettings['upc'] = array('text' => _l('UPC'), 'width' => 100, 'align' => 'left', 'type' => 'ro', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
$colSettings['mpn'] = array('text' => _l('MPN'), 'width' => 100, 'align' => 'left', 'type' => 'ro', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
$colSettings['isbn'] = array('text' => _l('ISBN'), 'width' => 100, 'align' => 'left', 'type' => 'ro', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');

$colSettings['present'] = array('text' => _l('Present'), 'width' => 100, 'align' => 'center', 'type' => 'ch', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter', 'onlyforgrids' => array('grid_proppackcombi'));


