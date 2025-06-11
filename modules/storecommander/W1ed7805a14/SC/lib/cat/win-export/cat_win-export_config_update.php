<?php

    if (Tools::getValue('act', '') == 'cat_win-export_config_update')
    {
        $scriptname = html_entity_decode(Tools::getValue('gr_id'));
        readExportConfigXML($scriptname);
        $fields = array('mapping', 'shops', 'categoriessel', 'exportfilename', 'supplier', 'exportdisabledproducts', 'exportcombinations', 'exportoutofstock', 'exportbydefaultcategory', 'shippingfee', 'shippingfeefreefrom', 'fieldsep', 'valuesep', 'categorysep', 'enclosedby', 'iso', 'firstlinecontent', 'lastexportdate');
        foreach ($fields as $field)
        {
            if (isset($_GET[$field]) || isset($_POST[$field]))
            {
                $exportConfig[$field] = psql(html_entity_decode(Tools::getValue($field)));
            }
        }
        writeExportConfigXML($scriptname);
        if (stristr($_SERVER['HTTP_ACCEPT'], 'application/xhtml+xml'))
        {
            header('Content-type: application/xhtml+xml');
        }
        else
        {
            header('Content-type: text/xml');
        }
        echo '<?xml version="1.0" encoding="UTF-8"?><data><action type=\'update\' sid=\''.$scriptname.'\' tid=\''.$scriptname.'\' /></data>';
    }
