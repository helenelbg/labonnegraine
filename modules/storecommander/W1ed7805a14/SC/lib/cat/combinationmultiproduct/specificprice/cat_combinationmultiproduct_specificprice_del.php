<?php

$rowslist = Tools::getValue('rowslist', '');
if ($rowslist != '')
{
    $rowslistarray = explode(',', $rowslist);
    foreach ($rowslistarray as $id_specific_price)
    {
        if (!empty($id_specific_price))
        {
            $specificPrice = new SpecificPrice((int) ($id_specific_price));
            $specificPrice->delete();
        }
    }
}
