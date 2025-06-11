<?php

error_reporting(E_ALL ^ E_NOTICE);
@ini_set('display_errors', 'on');

$id_lang = (int) Tools::getValue('id_lang');
$mapping = Tools::getValue('mapping', '');
$filename = Tools::getValue('filename', '');
$importlimit = Tools::getValue('importlimit', '');
$mapppinggridlength = Tools::getValue('mapppinggridlength', 0);
$mappingname = Tools::getValue('mappingname', '');
$create_categories = (int) Tools::getValue('create_categories', -1);

include_once SC_DIR.'lib/php/parsecsv.lib.php';
require_once SC_DIR.'lib/cat/win-import/cat_win-import_tools.php';

$return = '';

if (!empty($filename))
{
    // INIT
    $files = array_diff(scandir(SC_CSV_IMPORT_DIR), array_merge(array('.', '..', 'index.php', '.htaccess', SC_CSV_IMPORT_CONF)));
    readImportConfigXML($files);

    $mapping = loadMapping($importConfig[$filename]['mapping']);
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

    // ACTION NEW PRODUCT
    $fornewproduct = $importConfig[$filename]['fornewproduct'];
    if ($fornewproduct == 'skip')
    {
        $fornewproduct = _l('Skip');
    }
    if ($fornewproduct == 'create')
    {
        $fornewproduct = _l('Create new product');
    }
    $fornewproduct = strtolower($fornewproduct);

    // ACTION FOUND PRODUCT
    $idby = $importConfig[$filename]['idby'];
    if ($idby == 'prodname')
    {
        $idby = _l('Product name');
    }
    if ($idby == 'prodref')
    {
        $idby = _l('Product reference');
    }
    if ($idby == 'prodrefthenprodname')
    {
        $idby = _l('Prod. ref THEN prod. name');
    }
    if ($idby == 'supref')
    {
        $idby = _l('Supplier reference');
    }
    if ($idby == 'suprefthenprodname')
    {
        $idby = _l('Sup. ref THEN prod. name');
    }
    if ($idby == 'prodrefandsupref')
    {
        $idby = _l('Product and supplier reference');
    }
    if ($idby == 'prodnameandsupref')
    {
        $idby = _l('Product and supplier name');
    }
    if ($idby == 'idproduct')
    {
        $idby = _l('id_product');
    }
    if ($idby == 'idproductattribute')
    {
        $idby = _l('id_product_attribute');
    }
    if ($idby == 'ean13')
    {
        $idby = _l('EAN');
    }
    if ($idby == 'upc')
    {
        $idby = _l('UPC');
    }
    if ($idby == 'mpn')
    {
        $idby = _l('MPN');
    }
    if ($idby == 'isbn')
    {
        $idby = _l('ISBN');
    }
    if ($idby == 'specialIdentifier')
    {
        $idby = _l('specialIdentifier');
    }
    $idby = strtolower($idby);

    $forfoundproduct = $importConfig[$filename]['forfoundproduct'];
    if ($forfoundproduct == 'skip')
    {
        $forfoundproduct = _l('Skip');
    }
    if ($forfoundproduct == 'update')
    {
        $forfoundproduct = _l('Modify product');
    }
    if ($forfoundproduct == 'create')
    {
        $forfoundproduct = _l('Created as duplication');
    }
    $forfoundproduct = strtolower($forfoundproduct);
    $return .= _l('Products will be identified by <strong>%s</strong>.', false, array($idby)).'<br/><br/>';
    $return .= _l('Action for new products: <strong>%s</strong>.', false, array($fornewproduct)).'<br/><br/>';
    $return .= _l('Action for existing products: <strong>%s</strong>.', false, array($forfoundproduct)).'<br/><br/>';


    if ($mapppinggridlength == 1)
    {
        $return .= '<strong>'._l('!!! WARNING !!!').'</strong> '._l('Field/Value separators selected in your configuation do not seem to match your CSV file. Check your settings.').'<br/><br/>';
    }

    if ($fornewproduct == 'skip' && $forfoundproduct == 'skip')
    {
        $return .= '<strong>'._l('!!! WARNING !!!').'</strong> '._l('An action needs to be selected before importing.').'<br/><br/>';
    }

    // ALERT FOR COMBINATIONS IMPORT
    if ($importConfig[$filename]['forfoundproduct'] == 'skip' && sc_in_array('attribute', $mappingData['DBArray'], 'catWinImportCheck_DBArray'))
    {
        $return .= '<strong>'._l('!!! WARNING !!!').'</strong> '._l('If this import will create combinations, only the first combination of each product will be created. To avoid this, select "modify product" in "Action for existing products".').'<br/><br/>';
    }

    if ($importConfig[$filename]['fornewproduct'] == 'create' && $importConfig[$filename]['forfoundproduct'] == 'create' && sc_in_array('attribute', $mappingData['DBArray'], 'catWinImportCheck_DBArray'))
    {
        $return .= '<strong>'._l('!!! WARNING !!!').'</strong> '._l('If this import will add new combinations, a product will be created for each line corresponding to a combination. To avoid this select "modify product" in "Action for existing products".').'<br/><br/>';
    }

    // SUPPLIER FILTER
    if (!empty($importConfig[$filename]['supplier']))
    {
        $supplier = new Supplier((int) $importConfig[$filename]['supplier'], $id_lang);
        if (!empty($supplier->name))
        {
            $return .= _l('Only products associated to supplier <strong>%s</strong> will be updated.', false, array($supplier->name)).'<br/><br/>';
        }
    }

    // VAT check : if HT & TTC without VAT  or HT & VAT & TTC ==> need to use HT & VAT only
    if ((sc_in_array('priceinctax', $mappingData['DBArray'], 'catWinImportCheckVAT_DBArray') || sc_in_array('priceinctaxincecotax', $mappingData['DBArray'], 'catWinImportCheckVAT_DBArray'))
        && !sc_in_array('VAT', $mappingData['DBArray'], 'catWinImportCheckVAT_DBArray'))
    {
        $return .= '<strong>'._l('!!! WARNING !!!').'</strong> '._l('Error in mapping: price including VAT found in CSV columns but no VAT column found. You need to indicate the VAT or use only price excluding VAT.').'<br/><br/>';
    }
    if ((sc_in_array('priceinctax', $mappingData['DBArray'], 'catWinImportCheckVAT_DBArray') || sc_in_array('priceinctaxincecotax', $mappingData['DBArray'], 'catWinImportCheckVAT_DBArray'))
        && sc_in_array('priceexctax', $mappingData['DBArray'], 'catWinImportCheckVAT_DBArray')
        && sc_in_array('VAT', $mappingData['DBArray'], 'catWinImportCheckVAT_DBArray'))
    {
        $return .= '<strong>'._l('!!! WARNING !!!').'</strong> '._l('Price excluding VAT, price including VAT and VAT found in CSV columns. You must use only price excluding VAT with VAT.').'<br/><br/>';
    }

    // Check Column unit_price_ratio HT && unit_price_ratio TTC in same file
    if (sc_in_array('unit_price_ratio', $mappingData['DBArray'], 'catWinImportCheck_DBArray') && sc_in_array('unit_price_ratio_ttc', $mappingData['DBArray'], 'catWinImportCheck_DBArray'))
    {
        $return .= '<strong>'._l('!!! WARNING !!!').'</strong> '._l('Error in mapping: column unit price tax excl and unit price tax incl found. You need to choose only one of them.').'<br/><br/>';
    }
    // Check Column unit_price_ratio ttc but without tva col
    if (sc_in_array('unit_price_ratio_ttc', $mappingData['DBArray'], 'catWinImportCheck_DBArray') && !sc_in_array('VAT', $mappingData['DBArray'], 'catWinImportCheckVAT_DBArray'))
    {
        $return .= '<strong>'._l('!!! WARNING !!!').'</strong> '._l('Error in mapping: column unit price tax incl found. You need to indicate the VAT or use only unit price excluding VAT.').'<br/><br/>';
    }
    // Check Column unit_price_impact HT && unit_price_impact TTC in same file (combinations)
    if (sc_in_array('unit_price_impact', $mappingData['DBArray'], 'catWinImportCheck_DBArray') && sc_in_array('unit_price_impact_ttc', $mappingData['DBArray'], 'catWinImportCheck_DBArray'))
    {
        $return .= '<strong>'._l('!!! WARNING !!!').'</strong> '._l('Error in mapping: column unit price tax excl (combination impact) and unit price tax incl (combination impact) found. You need to choose only one of them.').'<br/><br/>';
    }
    // Check Column unit_price_impact ttc but without tva col (combinations)
    if (sc_in_array('unit_price_impact_ttc', $mappingData['DBArray'], 'catWinImportCheck_DBArray') && !sc_in_array('VAT', $mappingData['DBArray'], 'catWinImportCheckVAT_DBArray'))
    {
        $return .= '<strong>'._l('!!! WARNING !!!').'</strong> '._l('Error in mapping: column unit price tax incl (combination impact) found. You need to indicate the VAT or use only unit price excluding VAT.').'<br/><br/>';
    }

    // CREATE CATEGORIES AND ELEMENTS
    if ($create_categories <= 0)
    {
        $create_categories = (int) $importConfig[$filename]['createcategories'];
    }
    if ($create_categories > 0)
    {
        $return .= _l('The categories will be created automatically and products associated to them.').'<br/><br/>';
    }

    if ($importConfig[$filename]['createelements'] == 1)
    {
        $return .= _l('Elements found in the CSV file will be created automatically: features, combination attributes, manufacturers, suppliers, tags.').'<br/><br/>';
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
    $DATAFILE = remove_utf8_bom(file_get_contents(SC_CSV_IMPORT_DIR.$filename));
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

    $return .= '<i class="fa fa-check-circle" style="color:green"></i> <a href="'.getScExternalLink('support_csv_import_checklist').'" target="_blank"><b>'._l('Is your import ready? See the Checklist!').'</b></a>';
}

if (!empty($return))
{
    $return = '<div style="font-family:Arial,sans-serif;font-size: 13px !important; height: 100%; overflow: auto;"><div style="padding: 10px;">'.$return.'</div></div>';
    echo $return;
}
