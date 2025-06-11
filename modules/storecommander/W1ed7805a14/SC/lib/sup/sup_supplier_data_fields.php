<?php

$colSettings['id_supplier'] = array('text' => _l('ID'), 'width' => 70, 'align' => 'left', 'type' => 'ro', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter');
$colSettings['image'] = array('text' => _l('Logo'), 'width' => 70, 'align' => 'center', 'type' => 'ro', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
$colSettings['name'] = array('text' => _l('Name'), 'width' => 200, 'align' => 'left', 'type' => 'edtxt', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
$colSettings['date_add'] = array('text' => _l('Creation date'), 'width' => 140, 'align' => 'left', 'type' => 'ro', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
$colSettings['date_upd'] = array('text' => _l('Modified date'), 'width' => 140, 'align' => 'right', 'type' => 'ro', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
$colSettings['description'] = array('text' => _l('Description'), 'width' => 300, 'align' => 'left', 'type' => 'wysiwyg', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
$colSettings['meta_title'] = array('text' => _l('meta_title'), 'width' => 200, 'align' => 'left', 'type' => 'edtxt', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
$colSettings['meta_description'] = array('text' => _l('meta_description'), 'width' => 200, 'align' => 'left', 'type' => 'edtxt', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
$colSettings['meta_keywords'] = array('text' => _l('meta_keywords'), 'width' => 200, 'align' => 'left', 'type' => 'edtxt', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
$colSettings['active'] = array('text' => _l('active'), 'width' => 70, 'align' => 'left', 'type' => 'coro', 'sort' => 'str', 'color' => '', 'filter' => '#select_filter', 'options' => array(0 => _l('No'), 1 => _l('Yes')));
$colSettings['nb_products'] = array('text' => _l('Nb. associated products'), 'width' => 70, 'align' => 'left', 'type' => 'ro', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter');
$colSettings['id_address'] = array('text' => _l('id address'), 'width' => 45, 'align' => 'right', 'type' => 'ron', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter', 'onlyforgrids' => array('grid_address'));
$colSettings['alias'] = array('text' => _l('Alias'), 'width' => 70, 'align' => 'left', 'type' => 'ed', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter', 'onlyforgrids' => array('grid_address'));
$colSettings['firstname'] = array('text' => _l('Firstname'), 'width' => 70, 'align' => 'left', 'type' => 'ed', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter', 'onlyforgrids' => array('grid_address'));
$colSettings['lastname'] = array('text' => _l('Lastname'), 'width' => 70, 'align' => 'left', 'type' => 'ed', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter', 'onlyforgrids' => array('grid_address'));
$colSettings['company'] = array('text' => _l('Company'), 'width' => 70, 'align' => 'left', 'type' => 'ed', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter', 'onlyforgrids' => array('grid_address'));
$colSettings['phone'] = array('text' => _l('Phone'), 'width' => 100, 'align' => 'left', 'type' => 'ed', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter', 'onlyforgrids' => array('grid_address'));
$colSettings['phone_mobile'] = array('text' => _l('Phone mobile'), 'width' => 100, 'align' => 'left', 'type' => 'ed', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter', 'onlyforgrids' => array('grid_address'));
$colSettings['address1'] = array('text' => _l('Address'), 'width' => 100, 'align' => 'left', 'type' => 'ed', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter', 'onlyforgrids' => array('grid_address'));
$colSettings['address2'] = array('text' => _l('Address Line 2'), 'width' => 70, 'align' => 'left', 'type' => 'ed', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter', 'onlyforgrids' => array('grid_address'));
$colSettings['postcode'] = array('text' => _l('Postcode'), 'width' => 70, 'align' => 'left', 'type' => 'ed', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter', 'onlyforgrids' => array('grid_address'));
$colSettings['city'] = array('text' => _l('City'), 'width' => 70, 'align' => 'left', 'type' => 'ed', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter', 'onlyforgrids' => array('grid_address'));
$colSettings['id_state'] = array('text' => _l('State'), 'width' => 70, 'align' => 'left', 'type' => 'coro', 'sort' => 'str', 'color' => '', 'filter' => '#select_filter_strict', 'options' => array(), 'onlyforgrids' => array('grid_address'));
$colSettings['id_country'] = array('text' => _l('Country'), 'width' => 70, 'align' => 'left', 'type' => 'coro', 'sort' => 'str', 'color' => '', 'filter' => '#select_filter_strict', 'options' => array(), 'onlyforgrids' => array('grid_address'));
$colSettings['dni'] = array('text' => _l('dni'), 'width' => 70, 'align' => 'left', 'type' => 'ed', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter', 'onlyforgrids' => array('grid_address'));
$colSettings['other'] = array('text' => _l('Other'), 'width' => 100, 'align' => 'left', 'type' => 'ed', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter', 'onlyforgrids' => array('grid_address'));
