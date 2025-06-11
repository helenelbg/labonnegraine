<?php

$colSettings['id_partner'] = array('text' => _l('Partner ID'), 'width' => 40, 'align' => 'right', 'type' => 'ro', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter', 'footer' => '#stat_count');
$colSettings['id_shop'] = array('text' => _l('Shop'), 'width' => 80, 'align' => 'left', 'type' => 'ro', 'sort' => 'str', 'color' => '', 'filter' => '#select_filter');
$colSettings['active'] = array('text' => _l('Active'), 'width' => 80, 'align' => 'right', 'type' => 'coro', 'sort' => 'str', 'color' => '', 'filter' => '#select_filter', 'options' => array('0' => _l('No'), '1' => _l('Yes')));
$colSettings['customer_id'] = array('text' => _l('Customer ID'), 'width' => 50, 'align' => 'right', 'type' => 'edtxt', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter');
$colSettings['name'] = array('text' => _l('Name'), 'width' => 140, 'align' => 'left', 'type' => 'ro', 'sort' => 'str_custom', 'color' => '', 'filter' => '#text_filter');
$colSettings['code'] = array('text' => _l('Partner Code'), 'width' => 80, 'align' => 'left', 'type' => 'edtxt', 'sort' => 'str_custom', 'color' => '', 'filter' => '#text_filter');
$colSettings['percent_comm'] = array('text' => _l('% Comm. for partner code'), 'width' => 80, 'type' => 'edtxt', 'align' => 'right', 'sort' => 'float', 'color' => '', 'filter' => '#numeric_filter');
$colSettings['coupon_code'] = array('text' => _l('Discount coupon Code'), 'width' => 80, 'type' => 'edtxt', 'align' => 'left', 'sort' => 'str_custom', 'color' => '', 'filter' => '#text_filter');
$colSettings['coupon_percent_comm'] = array('text' => _l('% Comm. for discount coupon'), 'width' => 80, 'type' => 'edtxt', 'align' => 'right', 'sort' => 'float', 'color' => '', 'filter' => '#numeric_filter');
$colSettings['mode'] = array('text' => _l('Mode'), 'width' => 80, 'type' => 'coro', 'align' => 'right', 'sort' => 'str', 'color' => '', 'filter' => '#select_filter', 'options' => array('unlimited' => _l('Unlimited'), 'limited' => _l('Limited'), 'firstorder' => _l('First order')));
$colSettings['duration'] = array('text' => _l('Duration'), 'width' => 80, 'type' => 'edtxt', 'align' => 'right', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter');
$colSettings['quantity'] = array('text' => _l('Nb. aff.'), 'width' => 80, 'type' => 'ro', 'align' => 'right', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter', 'footer' => '#stat_total');
$colSettings['total_payments'] = array('text' => _l('Total paid'), 'width' => 80, 'type' => 'ron', 'align' => 'right', 'sort' => 'float', 'color' => '', 'filter' => '#numeric_filter', 'footer' => '#stat_total', 'format' => '0.00');
$colSettings['total_to_pay'] = array('text' => _l('Next payment amount'), 'width' => 80, 'type' => 'ron', 'align' => 'right', 'sort' => 'float', 'color' => '', 'filter' => '#numeric_filter', 'footer' => '#stat_total', 'format' => '0.00');
$colSettings['total_invoiced'] = array('text' => _l('Invoiced amount to pay'), 'width' => 80, 'type' => 'ron', 'align' => 'right', 'sort' => 'float', 'color' => '', 'filter' => '#numeric_filter', 'footer' => '#stat_total', 'format' => '0.00');
$colSettings['total_gained'] = array('text' => _l('Total commissions'), 'width' => 80, 'type' => 'ron', 'align' => 'right', 'sort' => 'float', 'color' => '', 'filter' => '#numeric_filter', 'footer' => '#stat_total', 'format' => '0.00');
$colSettings['note'] = array('text' => _l('Note'), 'width' => 80, 'type' => 'txt', 'align' => 'left', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
$colSettings['ppa'] = array('text' => _l('Membership Terms and Conditions'), 'width' => 80, 'type' => 'coro', 'align' => 'right', 'sort' => '', 'color' => '', 'filter' => '#select_filter', 'options' => array('0' => _l('No'), '1' => _l('Yes')));
$colSettings['ppa_date'] = array('text' => _l('Date of acceptance of MTC'), 'width' => 80, 'type' => 'ro', 'align' => 'right', 'sort' => 'date', 'color' => '', 'filter' => '#text_filter');
$colSettings['date_add'] = array('text' => _l('Date added'), 'width' => 110, 'type' => 'dhxCalendarA', 'align' => 'right', 'sort' => 'date', 'color' => '', 'filter' => '#text_filter');

// CUSTOMER
$colSettings['email'] = array('text' => _l('Email'), 'width' => 120, 'align' => 'left', 'type' => 'ed', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
$colSettings['company'] = array('text' => _l('Company'), 'width' => 140, 'align' => 'left', 'type' => 'ro', 'sort' => 'str_custom', 'color' => '', 'filter' => '#text_filter');
$colSettings['id_lang'] = array('text' => _l('Language'), 'width' => 30, 'align' => 'center', 'type' => 'coro', 'sort' => 'str', 'color' => '', 'filter' => '#select_filter', 'options' => $langOption);
$colSettings['siret'] = array('text' => _l('Customer').' - '._l('Siret'), 'width' => 80, 'align' => 'left', 'type' => 'ro', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
$colSettings['ape'] = array('text' => _l('Customer').' - '._l('APE'), 'width' => 80, 'align' => 'left', 'type' => 'ro', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
$colSettings['website'] = array('text' => _l('Customer').' - '._l('Website'), 'width' => 80, 'align' => 'left', 'type' => 'ro', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
