<?php

error_reporting(E_ALL ^ E_NOTICE);
@ini_set('display_errors', 'on');

$id_lang = (int) Tools::getValue('id_lang');
$mapping = Tools::getValue('mapping', '');
$filename = Tools::getValue('filename', '');
$importlimit = Tools::getValue('importlimit', '');
$mapppinggridlength = Tools::getValue('mapppinggridlength', 0);
$mappingname = Tools::getValue('mappingname', '');

include_once SC_DIR.'lib/php/parsecsv.lib.php';
require_once SC_DIR.'lib/man/win-import/man_win-import_tools.php';

$return = '';

if (!empty($filename))
{
    // INIT
    $files = array_diff(scandir(SC_CSV_IMPORT_DIR.'manufacturers/'), array_merge(array('.', '..', 'index.php', '.htaccess', SC_CSV_IMPORT_CONF)));
    readManImportConfigXML($files);
    $mapping = loadMappingMan($importConfig[$filename]['mapping']);
    $mappingDataArray = explode(';', $mapping);
    $mappingData = array('CSVArray' => array(), 'DBArray' => array(), 'CSV2DB' => array(), 'CSV2DBOptions' => array(), 'CSV2DBOptionsMerged' => array());
    foreach ($mappingDataArray as $val)
    {
        if ($val != '')
        {
            $tmp = explode(',', $val);
            $tmp2 = $tmp[0];
            escapeCharForPS($tmp2);
            $mappingData['CSVArray'][] = $tmp2;
            $mappingData['DBArray'][] = $tmp[1];
            $mappingData['CSV2DB'][$tmp[0]] = $tmp[1];
            $mappingData['CSV2DBOptions'][$tmp[0]] = $tmp[2];
            $mappingData['CSV2DBOptionsMerged'][$tmp[0]] = $tmp[1].'_'.$tmp[2];
        }
    }

    // LINE LIMIT AND FILE NAME
    $importlimit = ($importlimit > 0 ? $importlimit : (int) $importConfig[$filename]['importlimit']);

    $return .= _l('<strong>%s</strong> lines of file <strong>"%s"</strong> will be imported.', false, array($importlimit, $filename)).'<br/><br/>';
    $return .= _l('The mapping <strong>"%s"</strong> will be used.', false, array(($mappingname != '' ? $mappingname : $importConfig[$filename]['mapping']))).'<br/><br/>';

    // ACTION FOUND PRODUCT
    $idby = $importConfig[$filename]['idby'];
    if ($idby == 'name')
    {
        $idby = _l('Manufacturer name');
    }
    if ($idby == 'id_manufacturer')
    {
        $idby = _l('id_manufacturer');
    }
    $idby = strtolower($idby);

    $forfoundmanufacturer = $importConfig[$filename]['forfoundmanufacturer'];
    if ($forfoundmanufacturer == 'skip')
    {
        $forfoundmanufacturer = _l('Skip');
    }
    if ($forfoundmanufacturer == 'update')
    {
        $forfoundmanufacturer = _l('modify manufacturer');
    }
    $forfoundmanufacturer = strtolower($forfoundmanufacturer);
    $return .= _l('Manufacturers will be identified by <strong>%s</strong>.', false, array($idby)).'<br/><br/>';
    $return .= _l('Action for existing manufacturers: <strong>%s</strong>.', false, array($forfoundmanufacturer)).'<br/><br/>';

    if (SCMS)
    {
        if (!sc_in_array('id_shop_list', $mappingData['DBArray'], 'manWinImportCheck_idShopList_in_DBArray'))
        {
            $return .= '<strong>'._l('!!! WARNING !!!').'</strong> '._l('id_shop_list is required in multistore mode.').'<br/><br/>';
        }
    }

    if ($mapppinggridlength == 1)
    {
        $return .= '<strong>'._l('!!! WARNING !!!').'</strong> '._l('Field/Value separators selected in your configuation do not seem to match your CSV file. Check your settings.').'<br/><br/>';
    }

    // CHECK MULTILINES
    if ($importConfig[$filename]['fieldsep'] == 'dcomma')
    {
        $importConfig[$filename]['fieldsep'] = ';';
    }
    if ($importConfig[$filename]['fieldsep'] == 'dcommamac')
    {
        $importConfig[$filename]['fieldsep'] = ';';
    }
    if ($importConfig[$filename]['fieldsep'] == 'tab')
    {
        $importConfig[$filename]['fieldsep'] = "\t";
    }
    $DATAFILE = remove_utf8_bom(file_get_contents(SC_CSV_IMPORT_DIR.'manufacturers/'.$filename));
    $DATA = preg_split("/(?:\r\n|\r|\n)/", $DATAFILE);
    if ($importConfig[$filename]['firstlinecontent'] != '')
    {
        $firstLineData = explode($importConfig[$filename]['fieldsep'], $importConfig[$filename]['firstlinecontent']);
        $FIRST_CONTENT_LINE = 0;
    }
    else
    {
        $firstLineData = explode($importConfig[$filename]['fieldsep'], $DATA[0]);
        $FIRST_CONTENT_LINE = 1;
    }
    $nb_element_by_line = count($firstLineData);
    for ($current_line = $FIRST_CONTENT_LINE; ((($current_line <= (count($DATA) - 1)) && $line = parseCSVLine($importConfig[$filename]['fieldsep'], $DATA[$current_line]))); ++$current_line)
    {
        if ($DATA[$current_line] == '')
        {
            continue;
        }
        if (count($line) < $nb_element_by_line)
        {
            $return .= _l('Veuillez vérifier votre fichier car il semblerait que toutes les lignes ne possèdent pas le bon nombre de colonnes. Cela peut également venir d\'une description sur plusieurs lignes.').'<br/><br/>';
            $return .= _l('Lines of your CSV file do not use the correct number of columns, please check your file. Alternatively, this can be caused by descriptions spread on multiple lines.').'<br/><br/>';
            break;
        }
    }

    $return .= '<img src="lib/img/accept.png" alt="" style="margin-bottom: -4px;" /> <a href="'.getScExternalLink('support_csv_import_checklist').'" target="_blank"><b>'._l('Is your import ready? See the Checklist!').'</b></a>';
}

if (!empty($return))
{
    $return = '<div style="font-family:Arial,sans-serif;font-size: 13px !important; height: 100%; overflow: auto;"><div style="padding: 10px;">'.$return.'</div></div>';
    echo $return;
}
