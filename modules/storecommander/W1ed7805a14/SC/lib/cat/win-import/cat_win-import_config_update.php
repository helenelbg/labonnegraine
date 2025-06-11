<?php

    if (Tools::getValue('act', '') == 'cat_win-import_config_update')
    {
        $csvFile = html_entity_decode(Tools::getValue('gr_id'));
        $files = array_diff(scandir(SC_CSV_IMPORT_DIR), array_merge(array('.', '..', 'index.php', '.htaccess', SC_CSV_IMPORT_CONF)));
        readImportConfigXML($files);
        $fields = array('supplier', 'fieldsep', 'valuesep', 'categorysep', 'utf8', 'idby', 'iffoundindb', 'fornewproduct', 'forfoundproduct', 'mapping', 'firstlinecontent', 'createcategories', 'importlimit', 'createelements');
        foreach ($fields as $field)
        {
            if (isset($_POST[$field]))
            {
                $importConfig[$csvFile][$field] = psql(html_entity_decode(Tools::getValue($field)));
            }
        }
        writeImportConfigXML();
        if (stristr($_SERVER['HTTP_ACCEPT'], 'application/xhtml+xml'))
        {
            header('Content-type: application/xhtml+xml');
        }
        else
        {
            header('Content-type: text/xml');
        }
        echo '<?xml version="1.0" encoding="UTF-8"?><data><action type=\'update\' sid=\''.$csvFile.'\' tid=\''.$csvFile.'\' /></data>';
    }
