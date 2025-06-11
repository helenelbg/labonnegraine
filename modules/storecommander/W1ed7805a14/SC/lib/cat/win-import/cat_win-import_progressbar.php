<?php

$file = Tools::getValue('file', null);
if (!empty($file))
{
    if (strpos($file, '.TODO.') === false)
    {
        $base_file = $file;
        $TODO_file = str_replace('.csv', '.TODO.csv', $file);
    }
    else
    {
        $base_file = str_replace('.TODO.csv', '.csv', $file);
        $TODO_file = $file;
    }
    $base_file_size = round(filesize(SC_CSV_IMPORT_DIR.$base_file));
    if (file_exists(SC_CSV_IMPORT_DIR.$TODO_file))
    {
        $TODO_file_size = round(filesize(SC_CSV_IMPORT_DIR.$TODO_file));
        $total = round(getPercent($TODO_file_size, $base_file_size));
        exit($total);
    }
    else
    {
        exit('100');
    }
}

function getPercent($a, $b)
{
    return 100 - (($a * 100) / $b);
}
