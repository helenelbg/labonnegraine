<?php

if (Tools::getValue('url'))
{
    include_once SC_DIR.'lib/php/parsecsv.lib.php';
    require_once SC_DIR.'lib/cus/win-import/cus_win-import_tools.php';

    $file = Tools::getValue('url');
    $fieldSep = Tools::getValue('fieldsep');
    $type = Tools::getValue('type');
    $forceUTF8 = Tools::getValue('utf8');
    $nbRowStart = Tools::getValue('nbrowstart', 0);
    $nbRowEnd = Tools::getValue('nbrowend', 20);
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
    }

    if (!file_exists($file))
    {
        exit(_l('File not found'));
    }

    /*
    * Initialisation parse fichier
    */
    $csv = new parseCSV();
    $csv->auto($file, true, $nbRowEnd);
    $firstRow = $csv->titles;
    $rows = $csv->data;
    $rows = array_slice($rows, 0, $nbRowEnd);
    if ($forceUTF8 == 1)
    {
        utf8_encode_array($firstRow);
        utf8_encode_array($rows);
    }

    // Si deux colonne de même nom
    if (count($firstRow) != count(array_unique($firstRow)))
    {
        exit(_l('Error : at least 2 columns have the same name in CSV file. You must use a unique name by column in the first line of your CSV file.').' <a href="'.getScExternalLink('support_error_import_csv_similar_column').'" target="_blank">'._l('See corresponding article').'</a>');
    }

    /*
    * Traitement pour affichage brut et en grille
    */
    if (!empty($type) && $type == 'raw')
    {
        $raw = $csv->unparse($rows);
        echo $forceUTF8 == 1 ? utf8_encode($raw) : $raw;
    }
    else
    {
        $xml = '';
        $i = 1;
        foreach ($rows as $row)
        {
            $row = array_map('cleanQuotes', $row);
            $xml .= '<row id="row_'.$i."\">\n";
            foreach ($row as $field)
            {
                $xml .= '    <cell>'.(!is_string($field) ? $field : '<![CDATA['.$field.']]>')."</cell>\n";
            }
            $xml .= "</row>\n";
            ++$i;
        }

        $columns = array();
        $firstLine = '';
        foreach ($firstRow as $field)
        {
            $columns[] = '<column id="'.$field.'" width="120" type="edtxt" align="left" sort="na"><![CDATA['.$field.']]></column>';
        }

        if (stristr($_SERVER['HTTP_ACCEPT'], 'application/xhtml+xml'))
        {
            header('Content-type: application/xhtml+xml');
        }
        else
        {
            header('Content-type: text/xml');
        }

        echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n"; ?>
        <rows>
            <head>
                <?php
                    foreach ($columns as $col)
                    {
                        echo $col;
                    } ?>

                <afterInit>
                    <call command="enableHeaderMenu"></call>
                </afterInit>
            </head>
            <?php
                echo $xml; ?>
        </rows>

<?php
    }
}

?>
