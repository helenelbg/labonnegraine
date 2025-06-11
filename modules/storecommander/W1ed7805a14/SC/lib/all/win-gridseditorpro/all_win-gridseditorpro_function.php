<?php

function getConfXmlName($type)
{
    if (!empty($type))
    {
        $xml = SC_TOOLS_DIR.'grids_'.$type.'_conf.xml';
        if ($type == 'productimport')
        {
            $xml = SC_TOOLS_DIR.'import_csv_conf.xml';
        }
        if ($type == 'customersimport')
        {
            $xml = SC_TOOLS_DIR.'import_customer_csv_conf.xml';
        }
        elseif ($type == 'productexport')
        {
            $xml = SC_TOOLS_DIR.'export_csv_conf.xml';
        }

        return $xml;
    }

    return false;
}
