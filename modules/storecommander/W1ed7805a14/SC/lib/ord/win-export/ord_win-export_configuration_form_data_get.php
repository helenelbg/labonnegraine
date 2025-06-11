<?php
if (!defined('STORE_COMMANDER')) { exit; }

if (!defined('SC_ExportOrders_ACTIVE') || (int)SC_ExportOrders_ACTIVE !== 1)
{
    exit;
}

$margin = SCI::getConfigurationValue("SC_QUICKACCOUNTING_MARGIN");
$excludedTaxRate = SCI::getConfigurationValue("SC_QUICKACCOUNTING_EXCLUDE_RATE");
$excludedTaxRate = explode("\n",$excludedTaxRate);
foreach($excludedTaxRate as &$rate)
{
    $rate = trim($rate);
}
$sql = 'SELECT DISTINCT(rate) as rate 
        FROM '._DB_PREFIX_.'tax 
        ORDER BY rate';
$rates = Db::getInstance()->executeS($sql);
$taxes = array();
foreach($rates as $k => $rate)
{
    $taxe_rate = number_format($rate["rate"], 1, ".", "");
    $taxes[]=array(
        'text'=> $taxe_rate,
        'value'=> $taxe_rate,
        'selected' => (in_array($taxe_rate,$excludedTaxRate))
    );
}

$response = array(
    'margin' => $margin,
    'taxe_rate_list' => $taxes
);

exit(json_encode($response));