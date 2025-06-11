<?php

    if (Tools::getValue('act', '') == 'cat_win-catimport_config_update')
    {
        $csvFile = html_entity_decode(Tools::getValue('gr_id'));
        $files = array_diff(scandir(SC_CSV_IMPORT_DIR.'category/'), array_merge(array('.', '..', 'index.php', '.htaccess', SC_CSV_IMPORT_CONF)));
        readCatImportConfigXML($files);
        $fields = array('fieldsep', 'valuesep', 'utf8', 'idby', 'iffoundindb', 'fornewcat', 'forfoundcat', 'mapping', 'firstlinecontent', 'importlimit');
        foreach ($fields as $field)
        {
            if (isset($_POST[$field]))
            {
                $importConfig[$csvFile][$field] = psql(html_entity_decode(Tools::getValue($field)));
            }
        }
        writeCatImportConfigXML();
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
