<?php

$colSettings['id_customer'] = array('text' => _l('id customer'), 'width' => 45, 'align' => 'right', 'type' => 'ron', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter');
$colSettings['id_shop'] = array('text' => _l('id shop'), 'width' => 45, 'align' => 'right', 'type' => 'ron', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter');
$colSettings['shop_name'] = array('text' => _l('Shop'), 'width' => 100, 'align' => 'left', 'type' => 'ro', 'sort' => 'str', 'color' => '', 'filter' => '#select_filter');
$colSettings['id_gender'] = array('text' => _l('Title'), 'width' => 45, 'align' => 'right', 'type' => 'coro', 'sort' => 'str', 'color' => '', 'filter' => '#select_filter', 'options' => $arrGenders);
$colSettings['company'] = array('text' => _l('Company'), 'width' => 70, 'align' => 'left', 'type' => 'ed', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
$colSettings['siret'] = array('text' => _l('Siret'), 'width' => 70, 'align' => 'left', 'type' => 'ed', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
$colSettings['ape'] = array('text' => _l('APE'), 'width' => 70, 'align' => 'left', 'type' => 'ed', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
$colSettings['firstname'] = array('text' => _l('Firstname'), 'width' => 100, 'align' => 'left', 'type' => 'ed', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
$colSettings['lastname'] = array('text' => _l('Lastname'), 'width' => 100, 'align' => 'left', 'type' => 'ed', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
$colSettings['email'] = array('text' => _l('Email'), 'width' => 100, 'align' => 'left', 'type' => 'ed', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
$colSettings['active'] = array('text' => _l('Active'), 'width' => 45, 'align' => 'center', 'type' => 'coro', 'sort' => 'str', 'color' => '', 'filter' => '#select_filter', 'options' => array(0 => _l('No'), 1 => _l('Yes')));
$colSettings['newsletter'] = array('text' => _l('Newsletter'), 'width' => 45, 'align' => 'center', 'type' => 'coro', 'sort' => 'str', 'color' => '', 'filter' => '#select_filter', 'options' => array(0 => _l('No'), 1 => _l('Yes')));
$colSettings['optin'] = array('text' => _l('Optin'), 'width' => 45, 'align' => 'center', 'type' => 'coro', 'sort' => 'str', 'color' => '', 'filter' => '#select_filter', 'options' => array(0 => _l('No'), 1 => _l('Yes')));
$colSettings['date_add'] = array('text' => _l('Creation date'), 'width' => 140, 'align' => 'left', 'type' => 'ro', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
$colSettings['date_connection'] = array('text' => _l('Last connection'), 'width' => 140, 'align' => 'left', 'type' => 'ro', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
$colSettings['msg'] = array('text' => _l('Message'), 'width' => 45, 'align' => 'right', 'type' => 'ro', 'sort' => 'str', 'color' => '', 'filter' => ' ');
$colSettings['cart_lang'] = array('text' => _l('Cart language'), 'width' => 70, 'align' => 'left', 'type' => 'ro', 'sort' => 'str', 'color' => '', 'filter' => '#select_filter');
$colSettings['id_lang'] = array('text' => _l('Customer language'), 'width' => 70, 'align' => 'left', 'type' => 'coro', 'sort' => 'str', 'color' => '', 'filter' => '#select_filter', 'options' => $language_arr);
$colSettings['birthday'] = array('text' => _l('DOB'), 'width' => 80, 'align' => 'right', 'type' => 'ed', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
$colSettings['id_default_group'] = array('text' => _l('Default group'), 'width' => 80, 'align' => 'left', 'type' => 'coro', 'sort' => 'str', 'color' => '', 'filter' => '#select_filter', 'options' => $arrGroupes);
$colSettings['groups'] = array('text' => _l('Groups'), 'width' => 100, 'align' => 'left', 'type' => 'ro', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
$colSettings['note'] = array('text' => _l('Private note'), 'width' => 150, 'align' => 'left', 'type' => 'txt', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
$colSettings['valid_orders'] = array('text' => _l('Confirmed orders'), 'width' => 50, 'align' => 'center', 'type' => 'ron', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter');
$colSettings['total_valid_orders'] = array('text' => _l('Total confirmed orders'), 'width' => 50, 'align' => 'center', 'type' => 'ron', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter');
$colSettings['last_delivery_address'] = array('text' => _l('Last delivery address'), 'width' => 150, 'align' => 'left', 'type' => 'ron', 'sort' => 'int', 'color' => '', 'filter' => '#text_filter');
$colSettings['last_invoice_address'] = array('text' => _l('Last invoice address'), 'width' => 150, 'align' => 'left', 'type' => 'ron', 'sort' => 'int', 'color' => '', 'filter' => '#text_filter');
$colSettings['last_date_order'] = array('text' => _l('Last order date'), 'width' => 140, 'align' => 'left', 'type' => 'ro', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
$colSettings['last_date_cart'] = array('text' => _l('Last cart date'), 'width' => 140, 'align' => 'center', 'type' => 'ro', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
$colSettings['nb_cart_product'] = array('text' => _l('Nb products in cart'), 'width' => 50, 'align' => 'center', 'type' => 'ron', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter');
$colSettings['total_cart_product'] = array('text' => _l('Total cart'), 'width' => 50, 'align' => 'right', 'type' => 'ron', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter');
$colSettings['discount_codes'] = array('text' => _l('Discount codes'), 'width' => 100, 'align' => 'left', 'type' => 'ro', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
{
    $colSettings['website'] = array('text' => _l('Website'), 'width' => 100, 'align' => 'left', 'type' => 'ed', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
}
// ADDRESS
$colSettings['id_address'] = array('text' => _l('id address'), 'width' => 45, 'align' => 'right', 'type' => 'ron', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter');
$colSettings['alias'] = array('text' => _l('Address alias'), 'width' => 100, 'align' => 'left', 'type' => 'ed', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter', 'onlyforgrids' => array('grid_address'));
$colSettings['address1'] = array('text' => _l('Address'), 'width' => 100, 'align' => 'left', 'type' => 'ed', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter', 'onlyforgrids' => array('grid_address'));
$colSettings['address2'] = array('text' => _l('Address').' 2', 'width' => 70, 'align' => 'left', 'type' => 'ed', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter', 'onlyforgrids' => array('grid_address'));
$colSettings['postcode'] = array('text' => _l('Postcode'), 'width' => 70, 'align' => 'left', 'type' => 'edn', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter', 'onlyforgrids' => array('grid_address'));
$colSettings['city'] = array('text' => _l('City'), 'width' => 70, 'align' => 'left', 'type' => 'ed', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter', 'onlyforgrids' => array('grid_address'));
$colSettings['id_state'] = array('text' => _l('State'), 'width' => 70, 'align' => 'left', 'type' => 'coro', 'sort' => 'str', 'color' => '', 'filter' => '#select_filter_strict', 'options' => $arrStates, 'onlyforgrids' => array('grid_address'));
$colSettings['id_country'] = array('text' => _l('Country'), 'width' => 70, 'align' => 'left', 'type' => 'coro', 'sort' => 'str', 'color' => '', 'filter' => '#select_filter_strict', 'options' => $arrCountrys, 'onlyforgrids' => array('grid_address'));
$colSettings['other'] = array('text' => _l('Address').' - '._l('Other'), 'width' => 100, 'align' => 'left', 'type' => 'txttxt', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter', 'onlyforgrids' => array('grid_address'));
$colSettings['invoice'] = array('text' => _l('Invoice?'), 'width' => 80, 'align' => 'center', 'type' => 'ron', 'sort' => 'int', 'color' => '', 'filter' => '#select_filter', 'onlyforgrids' => array('grid_address'));
$colSettings['delivery'] = array('text' => _l('Delivery?'), 'width' => 70, 'align' => 'center', 'type' => 'ron', 'sort' => 'int', 'color' => '', 'filter' => '#select_filter', 'onlyforgrids' => array('grid_address'));
$colSettings['phone'] = array('text' => _l('Phone'), 'width' => 100, 'align' => 'left', 'type' => 'ed', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter', 'onlyforgrids' => array('grid_address'));
$colSettings['phone_mobile'] = array('text' => _l('Phone mobile'), 'width' => 100, 'align' => 'left', 'type' => 'ed', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter', 'onlyforgrids' => array('grid_address'));
$colSettings['dni'] = array('text' => _l('DNI'), 'width' => 100, 'align' => 'left', 'type' => 'ed', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter', 'onlyforgrids' => array('grid_address'));
$colSettings['vat_number'] = array('text' => _l('VAT Number'), 'width' => 100, 'align' => 'left', 'type' => 'ed', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter', 'onlyforgrids' => array('grid_address'));
