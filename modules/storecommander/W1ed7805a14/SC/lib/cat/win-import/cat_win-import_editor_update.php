<?php

if (Tools::getValue('save') && Tools::getValue('save') == 1)
{
    include_once SC_DIR.'lib/php/parsecsv.lib.php';
    require_once SC_DIR.'lib/cat/win-import/cat_win-import_tools.php';

    $file = Tools::getValue('url');
    $fieldSep = Tools::getValue('fieldsep');
    $dataFromUrl = Tools::getValue('data');
    $type = Tools::getValue('type');
    $forceUTF8 = Tools::getValue('utf8');
    $nbRowStart = Tools::getValue('nbrowstart');
    $nbRowEnd = Tools::getValue('nbrowend', 20);
    $errors = array();
    $delimiter = array(
        'dcomma' => ';',
        'dcommamac' => ';',
        ',' => ',',
        '|' => '|',
        'tab' => "\t",
    );
    if (array_key_exists($fieldSep, $delimiter))
    {
        $delimiter = $delimiter[$fieldSep];
        if ($fieldSep === 'tab')
        {
            $dataFromUrl = str_replace('{tab}', $delimiter, $dataFromUrl);
        }
        else
        {
            $dataFromUrl = str_replace($fieldSep, $delimiter, $dataFromUrl);
        }
    }

    if (!file_exists($file))
    {
        exit(_l('File not found', 1));
    }

    $fileToRead = new parseCSV();
    $fileToRead->auto($file, true);

    $fileToWrite = new parseCSV();
    $fileToWrite->delimiter = $delimiter;
    // Si on force utf-8 alors il faut enregistrer les valeurs en ISO avant que le traitement ne reconvertisse le fichier en UTF-8
    $fileToWrite->parse(($forceUTF8 == 1 ? utf8_decode($dataFromUrl) : $dataFromUrl));
    $firstRow = (!empty($type) && $type == 'grid' ? $fileToRead->titles : $fileToWrite->titles);

    for ($i = $nbRowStart; $i <= ($nbRowEnd - 1); ++$i)
    {
        if (!empty($fileToWrite->data[$i]))
        {
            $fileToRead->data[$i] = $fileToWrite->data[$i];
        }
        else
        {
            unset($fileToRead->data[$i]);
        }
    }

    // Si deux colonne de même nom
    if (count($firstRow) != count(array_unique($firstRow)))
    {
        exit(_l('Error : at least 2 columns have the same name in CSV file. You must use a unique name by column in the first line of your CSV file.').' <a href="'.getScExternalLink('support_error_import_csv_similar_column').'" target="_blank">'._l('See corresponding article').'</a>');
    }
    else
    {
        try
        {
            $fileSaved = new parseCSV($file);
            $fileSaved->delimiter = $delimiter;
            $fileSaved->linefeed = "\n";
            $fileSaved->titles = $firstRow;
            $fileSaved->data = $fileToRead->data;
            $fileSaved->save();

            echo _l('Updated file', 1);
        }
        catch (Exception $e)
        {
            echo $e->getMessage(), "\n";
        }
    }
}
exit();
