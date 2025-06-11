<?php

$sc_active = SCI::getConfigurationValue('SC_PLUG_DISABLECOMBINATIONS', 0);

$grids = 'id_product_attribute,'.($sc_active ? 'sc_active,' : '').'reference,supplier_reference,ean13,upc,location,ATTR,quantity,quantityupdate,minimal_quantity,wholesale_price,pprice,price,ppriceextax,priceextax,margin,ecotax,pweight,weight,unit_price_impact,unit_price_impact_inc_tax,default_on';
if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
{
    $grids = str_replace(',default_on', ',available_date,default_on', $grids);
}
if (SCAS)
{
    $grids = 'id_product_attribute,'.($sc_active ? 'sc_active,' : '').'reference,supplier_reference,ean13,location,upc,ATTR,quantity,quantity_physical,quantity_usable,quantity_real,quantityupdate,minimal_quantity,wholesale_price,pprice,price,ppriceextax,priceextax,margin,ecotax,pweight,weight,unit_price_impact,unit_price_impact_inc_tax,available_date,default_on';
}

if (version_compare(_PS_VERSION_, '1.5.0.0', '>=') && (int) SCI::getConfigurationValue('PS_USE_ECOTAX', null, 0, SCI::getSelectedShop()) == 0)
{
    $grids = str_replace(',ecotax', '', $grids);
}
elseif (version_compare(_PS_VERSION_, '1.5.0.0', '<') && (int) SCI::getConfigurationValue('PS_USE_ECOTAX') == 0)
{
    $grids = str_replace(',ecotax', '', $grids);
}
if (version_compare(_PS_VERSION_, '1.7.0.0', '>='))
{
    $grids = str_replace(',upc', ',upc,isbn', $grids);
}
if (version_compare(_PS_VERSION_, '1.7.3.0', '>='))
{
    $grids = str_replace(',available_date', ',available_date,low_stock_alert,low_stock_threshold', $grids);
}
if (version_compare(_PS_VERSION_, '1.7.5.0', '>='))
{
    $grids = str_replace(',location', ',location,location_new', $grids);
}
if (version_compare(_PS_VERSION_, '1.7.7.0', '>='))
{
    $grids = str_replace(',ean13', ',mpn,ean13', $grids);
}

if (SCI::getConfigurationValue('SC_DELIVERYDATE_INSTALLED') == '1')
{
    $grids = str_replace(',default_on', ',available_later,default_on', $grids);
}

if (version_compare(_PS_VERSION_, '1.5.0.1', '>='))
{
    $grids = str_replace(',default_on', ',position,default_on', $grids);
}

if (version_compare(_PS_VERSION_, '8.0.0', '>='))
{
    $grids = str_replace(',location,', ',', $grids);
}
