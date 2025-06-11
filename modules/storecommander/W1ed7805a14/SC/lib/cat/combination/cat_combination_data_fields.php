<?php

$format_ht = (_s('CAT_PROD_PRICEWITHOUTTAX4DEC') ? '0.0000' : '0.00');
$format_ttc = (_s('CAT_PROD_PRICEWITHTAX4DEC') ? '0.0000' : '0.00');
$colSettings['id_product_attribute'] = array('text' => _l('ID'), 'width' => 40, 'align' => 'right', 'type' => 'ro', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter');
$colSettings['reference'] = array('text' => _l('Ref'), 'width' => 90, 'align' => 'left', 'type' => 'edtxt', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
$colSettings['supplier_reference'] = array('text' => _l('Supplier Ref.'), 'width' => 90, 'align' => 'left', 'type' => 'edtxt', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
if (version_compare(_PS_VERSION_, '1.7.7.0', '>='))
{
    $colSettings['mpn'] = array('text' => _l('MPN'), 'width' => 90, 'align' => 'left', 'type' => 'edtxt', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
}
$colSettings['ean13'] = array('text' => _l('EAN13'), 'width' => 90, 'align' => 'left', 'type' => 'edtxt', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
$colSettings['upc'] = array('text' => _l('UPC'), 'width' => 90, 'align' => 'left', 'type' => 'edtxt', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
if (version_compare(_PS_VERSION_, '1.7.0.0', '>='))
{
    $colSettings['isbn'] = array('text' => _l('ISBN'), 'width' => 90, 'align' => 'left', 'type' => 'edtxt', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
}
if (version_compare(_PS_VERSION_, '8.0.0', '<'))
{
    $colSettings['location'] = array('text' => _l('Location').(SCAS ? ' '._l('(old)') : ''), 'width' => 90, 'align' => 'left', 'type' => 'edtxt', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
}
if (version_compare(_PS_VERSION_, '1.7.5.0', '>='))
{
    $colSettings['location_new'] = array('text' => _l('Stock location'), 'width' => 90, 'align' => 'left', 'type' => 'edtxt', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
}
$colSettings['quantity'] = array('text' => _l('Qty'), 'width' => 40, 'align' => 'right', 'type' => 'ed', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter');
$colSettings['minimal_quantity'] = array('text' => _l('Minimum quantity'), 'width' => 40, 'align' => 'right', 'type' => 'ed', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter');
$colSettings['quantityupdate'] = array('text' => _l('Qty +/-'), 'width' => 40, 'align' => 'right', 'type' => 'ed', 'sort' => 'na', 'color' => '#EFFAFF', 'filter' => 'na');
$colSettings['wholesale_price'] = array('text' => _l('Wholesale price'), 'width' => 50, 'align' => 'right', 'type' => 'ed', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter');
$colSettings['pprice'] = array('text' => _l('Prod. price'), 'width' => 50, 'align' => 'right', 'type' => 'ron', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter', 'format' => $format_ttc);
$colSettings['price'] = array('text' => _l('Attr. price'), 'width' => 50, 'align' => 'right', 'type' => 'edn', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter', 'format' => $format_ht);
$colSettings['ppriceextax'] = array('text' => _l('Prod. price excl tax'), 'width' => 50, 'align' => 'right', 'type' => 'ron', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter', 'format' => $format_ht);
$colSettings['priceextax'] = array('text' => _l('Attr. price excl tax'), 'width' => 50, 'align' => 'right', 'type' => 'edn', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter', 'format' => '0.00');
$colSettings['margin'] = array('text' => _l('Margin'), 'width' => 45, 'align' => 'right', 'type' => 'ro', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter', 'format' => '0.00');
$colSettings['pweight'] = array('text' => _l('Prod. weight'), 'width' => 50, 'align' => 'right', 'type' => 'ro', 'sort' => 'int', 'color' => '', 'filter' => '#text_filter');
$colSettings['weight'] = array('text' => _l('Att. weight'), 'width' => 50, 'align' => 'right', 'type' => 'ed', 'sort' => 'int', 'color' => '', 'filter' => '#text_filter');
$colSettings['default_on'] = array('text' => _l('Default'), 'width' => 20, 'align' => 'center', 'type' => 'ra', 'sort' => 'str', 'color' => '', 'filter' => 'na');
$colSettings['ecotax'] = array('text' => _l('Ecotax'), 'width' => 50, 'align' => 'right', 'type' => 'edn', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter', 'format' => '0.00');
$colSettings['unit_price_impact'] = array('text' => _l('Unit price (combination impact)'), 'width' => 50, 'align' => 'right', 'type' => 'ed', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter');
$colSettings['unit_price_impact_inc_tax'] = array('text' => _l('Unit price tax incl. (combination impact)'), 'width' => 50, 'align' => 'right', 'type' => 'ed', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter');
$colSettings['sc_active'] = array('text' => _l('Used'), 'width' => 50, 'align' => 'center', 'type' => 'co', 'sort' => 'str', 'color' => '', 'filter' => '#select_filter', 'options' => array(0 => _l('No'), 1 => _l('Yes')));
if (_s('CAT_PROD_COMBI_METHOD'))
{
    $colSettings['ATTR'] = array('text' => _l('Attributes'), 'width' => 100, 'align' => 'left', 'type' => 'ro', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
}

$colSettings['quantity_physical'] = array('text' => _l('Physical stock'), 'width' => 60, 'align' => 'right', 'type' => 'ro', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter');
$colSettings['quantity_usable'] = array('text' => _l('Available stock'), 'width' => 60, 'align' => 'right', 'type' => 'ro', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter');
$colSettings['quantity_real'] = array('text' => _l('Live stock'), 'width' => 60, 'align' => 'right', 'type' => 'ro', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter');

$colSettings['available_date'] = array('text' => _l('Available date'), 'width' => 80, 'align' => 'right', 'type' => 'dhxCalendarA', 'sort' => 'date', 'color' => '', 'filter' => '#select_filter', 'format' => '%Y-%m-%d');

if (version_compare(_PS_VERSION_, '1.7.3.0', '>='))
{
    $colSettings['low_stock_alert'] = array('text' => _l('Low stock alert'), 'width' => 60, 'align' => 'left', 'type' => 'coro', 'sort' => 'str', 'color' => '', 'filter' => '#select_filter', 'options' => array(0 => _l('No'), 1 => _l('Yes')));
    $colSettings['low_stock_threshold'] = array('text' => _l('Low stock threshold'), 'width' => 60, 'align' => 'left', 'type' => 'edn', 'sort' => 'str', 'color' => '', 'filter' => '#numeric_filter', 'options' => array(0 => _l('No'), 1 => _l('Yes')));
}

if (version_compare(_PS_VERSION_, '1.7.5.0', '>='))
{
    $colSettings['soft_qty_physical'] = array('text' => _l('Physical quantity'), 'width' => 60, 'align' => 'right', 'type' => 'ro', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter');
    $colSettings['soft_qty_reserved'] = array('text' => _l('Reserved quantity'), 'width' => 60, 'align' => 'right', 'type' => 'ro', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter');
}

if (SCI::getConfigurationValue('SC_DELIVERYDATE_INSTALLED') == '1')
{
    $colSettings['available_later'] = array('text' => _l('Msg available later'), 'width' => 100, 'align' => 'left', 'type' => 'co', 'sort' => 'str', 'color' => '', 'filter' => '#select_filter', 'options' => $arrMsgAvailableLater);
}

if (version_compare(_PS_VERSION_, '1.5.0.1', '>='))
{
    $colSettings['position'] = array('text' => _l('Position'), 'width' => 50, 'align' => 'right', 'type' => 'ro', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter');
}
