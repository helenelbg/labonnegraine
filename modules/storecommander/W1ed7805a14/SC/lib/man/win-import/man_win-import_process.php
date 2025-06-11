<?php

error_reporting(E_ALL ^ E_NOTICE);
@ini_set('display_errors', 'on');

if (!isset($CRON))
{
    $CRON = 0;
}

$action = Tools::getValue('action');
$id_lang = (int) Tools::getValue('id_lang');
$mapping = Tools::getValue('mapping', '');
$create_categories = (int) Tools::getValue('create_categories', -1);

if (SCAS)
{
    $stock_manager = StockManagerFactory::getManager();
}

if (version_compare(_PS_VERSION_, '1.5.0.0', '<'))
{
    include_once SC_PS_PATH_DIR.'images.inc.php';
}

include_once SC_DIR.'lib/php/parsecsv.lib.php';
require_once SC_DIR.'lib/man/win-import/man_win-import_tools.php';

switch ($action) {
    case 'check_data':
        $files = array_diff(scandir(SC_CSV_IMPORT_DIR.'manufacturers/'), array_merge(array('.', '..', 'index.php', '.htaccess', SC_CSV_IMPORT_CONF)));
        readManImportConfigXML($files);
        $filename = Tools::getValue('filename', 0);
        if ($filename === 0)
        {
            exit(_l('You have to select a file and a mapping.'));
        }
        if (array_key_exists($filename, $importConfig))
        {
            $config = $importConfig[$filename];
        }
        $DATAFILE = file_get_contents(SC_CSV_IMPORT_DIR.'manufacturers/'.$filename);
        $DATA = array_filter(preg_split("/(?:\r\n|\r|\n)/", $DATAFILE));
        if ($importConfig[$filename]['fieldsep'] == 'dcomma')
        {
            $importConfig[$filename]['fieldsep'] = ';';
        }
        if ($importConfig[$filename]['fieldsep'] == 'dcommamac')
        {
            $importConfig[$filename]['fieldsep'] = ';';
        }
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
        $mappingDataArray = explode(';', $mapping);
        $mappingData = array('CSVArray' => array(), 'DBArray' => array(), 'CSV2DB' => array(), 'CSV2DBOptions' => array(), 'CSV2DBOptionsMerged' => array());
        foreach ($mappingDataArray as $val)
        {
            if ($val != '')
            {
                $tmp = explode(',', $val);
                $tmp2 = $tmp[0];
                escapeCharForPS($tmp2);
                $mappingData['DBArray'][] = $tmp[1];
            }
        }
        $required_object_fields_to_check = array('id_manufacturer', 'name');
        $db_field_keys = array_flip($mappingData['DBArray']);
        $key_identificatior = $db_field_keys[$importConfig[$filename]['idby']]; ## name ou id_manufacturer
        $errors = array();
        for ($current_line = $FIRST_CONTENT_LINE; (($current_line <= (count($DATA) - 1)) && $line = parseCSVLine($importConfig[$filename]['fieldsep'], $DATA[$current_line])); ++$current_line)
        {
            $line = array_map('cleanQuotes', $line);
            $manufacturer = new Manufacturer();
            $identificator = $line[$key_identificatior];
            foreach ($line as $key => $value)
            {
                if (array_key_exists($key, $mappingData['DBArray']))
                {
                    $db_field = $mappingData['DBArray'][$key];
                    if (in_array($db_field, $required_object_fields_to_check))
                    {
                        $manufacturer_validation = $manufacturer->validateField($db_field, $value, null, array(), true);
                        if ($manufacturer_validation !== true)
                        {
                            $errors[$identificator][] = $manufacturer_validation;
                        }
                    }
                }
            }
        }
        echo '<div id="outputResult" style="height:100%;overflow:auto;">
            <div style="width: 100%;box-sizing: border-box;height: 100%;line-height: 20px;">';
        if (!empty($errors))
        {
            $error_content = '';
            foreach ($errors as $identificator => $err_list)
            {
                $error_content .= $identificator.';'.implode('-', $err_list)."\n";
            }
            echo '<i style="width:10px;height:10px;background: red;display: block;border-radius: 11px;margin: 5px 5px 0 0;float: left;line-height: 43px;"></i>'._l('There are some errors:').' <button onClick="$(\'#check_data_result\').select();return false;">'._l('Select all').'</button>
                <br>
                <br>
                <textarea id="check_data_result" style="width: 100%;box-sizing: border-box;height: calc(100% - 60px);resize: none;">'.$error_content.'</textarea>
                </div>';
        }
        else
        {
            echo '<i style="width:10px;height:10px;background:#2dd83a;display: block;border-radius: 11px;margin: 5px 5px 0 0;float: left;line-height: 43px;"></i>'._l('No error').'</div>';
        }
        echo '</div>
            </div>';
        break;
    case 'conf_delete':
        $imp_opt_files = Tools::getValue('imp_opt_files', '');
        if ($imp_opt_files == '')
        {
            exit(_l('You should mark at least one file to delete'));
        }
        $imp_opt_file_array = preg_split('/;/', $imp_opt_files);
        foreach ($imp_opt_file_array as $imp_opt_file)
        {
            if ($imp_opt_file != '')
            {
                if (@unlink(SC_CSV_IMPORT_DIR.'manufacturers/'.$imp_opt_file))
                {
                    echo $imp_opt_file.' '._l('deleted')."\n";
                }
                else
                {
                    echo _l('Unable to delete this file, please check write permissions:').' '.$imp_opt_file."\n";
                }
            }
        }
        break;
    case 'mapping_load':
        echo loadMappingMan(Tools::getValue('filename', ''));
        break;
    case 'mapping_delete':
        $filename = str_replace('.map.xml', '', Tools::getValue('filename'));
        @unlink(SC_CSV_IMPORT_DIR.'manufacturers/'.$filename.'.map.xml');
        break;
    case 'mapping_saveas':
        $filename = str_replace('.map.xml', '', Tools::getValue('filename'));
        @unlink(SC_CSV_IMPORT_DIR.'manufacturers/'.$filename.'.map.xml');
        $mapping = preg_split('/;/', $mapping);
        $content = '<mapping><id_lang>'.(int) $sc_agent->id_lang.'</id_lang>';
        foreach ($mapping as $map)
        {
            $val = preg_split('/,/', $map);
            if (count($val) == 3)
            {
                $content .= '<map>';
                $content .= '<csvname><![CDATA['.$val[0].']]></csvname>';
                $content .= '<dbname><![CDATA['.$val[1].']]></dbname>';
                $content .= '<options><![CDATA['.$val[2].']]></options>';
                $content .= '</map>';
            }
        }
        $content .= '</mapping>';
        file_put_contents(SC_CSV_IMPORT_DIR.'manufacturers/'.$filename.'.map.xml', $content);
        echo _l('Data saved!');
        break;
    case 'mapping_process':
        echo '<div id="outputResult" style="height:100%;overflow:auto;">';
        if (SC_BETA)
        {
            $time_start = microtime(true);
        }
        checkDB();
        $scdebug = false;
        global $switchObject; // variable for custom import fields check
        $switchObject = '';
        global $TODO; // actions
        $TODO = array();
        global $id_manufacturer;
        $id_manufacturer = 0;
        $defaultLanguageId = (int) Configuration::get('PS_LANG_DEFAULT');
        $defaultLanguage = new Language($defaultLanguageId);
        $getIDlangByISO = array();
        $id_lang_sc = (int) Tools::getValue('id_lang_sc');
        foreach ($languages as $lang)
        {
            $getIDlangByISO[$lang['iso_code']] = $lang['id_lang'];
        }

        $files = array_diff(scandir(SC_CSV_IMPORT_DIR.'manufacturers/'), array_merge(array('.', '..', 'index.php', '.htaccess', SC_CSV_IMPORT_CONF)));
        readManImportConfigXML($files);
        $filename = Tools::getValue('filename', 0);
        $importlimit = (int) Tools::getValue('importlimit', 0);
        $importlimit = ($importlimit > 0 ? $importlimit : (int) $importConfig[$filename]['importlimit']);
        if ($importConfig[$filename]['firstlinecontent'] != '')
        {
            --$importlimit;
        }
        if ($CRON)
        {
            $mapping = loadMappingMan($importConfig[$filename]['mapping']);
        }
        if ($filename === 0 || $mapping == '')
        {
            exit(_l('You have to select a file and a mapping.'));
        }
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
        // check mapping
        switch ($importConfig[$filename]['idby']) {
            case 'id_manufacturer':
                if (!sc_in_array('id_manufacturer', $mappingData['DBArray'], 'manWinImportProcess_DBArray'))
                {
                    exit(_l('Wrong mapping, mapping should contain the id_manufacturer field'));
                }
                break;
            case 'name':
                if (!sc_in_array('name', $mappingData['DBArray'], 'manWinImportProcess_DBArray'))
                {
                    exit(_l('Wrong mapping, mapping should contain the name field'));
                }
                break;
        }

        // create TODO file
        if (substr($filename, strlen($filename) - 9, 9) == '.TODO.csv' && !file_exists(SC_CSV_IMPORT_DIR.'manufacturers/'.$filename))
        {
            exit(_l('The TODO file has been deleted, please select the original CSV file.'));
        }
        if (substr($filename, strlen($filename) - 9, 9) != '.TODO.csv')
        {
            $TODOfilename = substr($filename, 0, -4).'.TODO.csv';
            if (!file_exists(SC_CSV_IMPORT_DIR.'manufacturers/'.$TODOfilename))
            {
                copy(SC_CSV_IMPORT_DIR.'manufacturers/'.$filename, SC_CSV_IMPORT_DIR.'manufacturers/'.$TODOfilename);
                foreach ($importConfig[$filename] as $k => $v)
                {
                    $importConfig[$TODOfilename][$k] = $v;
                    if ($k == 'name')
                    {
                        $importConfig[$TODOfilename][$k] = $TODOfilename;
                    }
                }
                writeManImportConfigXML();
            }
        }
        else
        {
            $TODOfilename = $filename;
        }
        $needSaveTODO = false;

        // open csv filename
        if ($importConfig[$TODOfilename]['fieldsep'] == 'dcomma')
        {
            $importConfig[$TODOfilename]['fieldsep'] = ';';
        }
        if ($importConfig[$TODOfilename]['fieldsep'] == 'dcommamac')
        {
            $importConfig[$TODOfilename]['fieldsep'] = ';';
        }
        // get first line
        $DATAFILE = file_get_contents(SC_CSV_IMPORT_DIR.'manufacturers/'.$TODOfilename);
        $DATA = preg_split("/(?:\r\n|\r|\n)/", $DATAFILE);
        if ($importConfig[$TODOfilename]['firstlinecontent'] != '')
        {
            $firstLineData = explode($importConfig[$TODOfilename]['fieldsep'], $importConfig[$TODOfilename]['firstlinecontent']);
            $FIRST_CONTENT_LINE = 0;
        }
        else
        {
            $firstLineData = explode($importConfig[$TODOfilename]['fieldsep'], $DATA[0]);
            $FIRST_CONTENT_LINE = 1;
        }
        if (count($firstLineData) != count(array_unique($firstLineData)))
        {
            exit(_l('Error : at least 2 columns have the same name in CSV file. You must use a unique name by column in the first line of your CSV file.'));
        }
        foreach ($firstLineData as $key => $val)
        {
            escapeCharForPS($firstLineData[$key]);
        }
        $firstLineData = array_map('cleanQuotes', $firstLineData);
        if ($importConfig[$TODOfilename]['utf8'])
        {
            utf8_encode_array($firstLineData);
        }

        // CHECK FILE VALIDITY
        if (count($mappingData['CSVArray']) > count($firstLineData))
        {
            exit(_l('Error in mapping: too much field to import').' (CSVArray:'.count($mappingData['CSVArray']).' - firstLineData:'.count($firstLineData).')');
        }
        foreach ($mappingData['CSVArray'] as $val)
        {
            if (!sc_in_array($val, $firstLineData, 'manWinImportProcess_firstLineData'))
            {
                exit(_l('Error in mapping: the fields are not in the CSV file')._l(':').$val);
            }
        }

        if ($err != '')
        {
            exit($err.'<br/><br/>'._l('The process has been stopped before any modification in the database. You need to fix these errors first.'));
        }

        $stats = array('created' => 0, 'modified' => 0, 'skipped' => 0, 'group_created' => 0);
        $CSVDataStr = file_get_contents(SC_CSV_IMPORT_DIR.'manufacturers/'.$TODOfilename);
        $CSVData = preg_split("/(?:\r\n|\r|\n)/", $CSVDataStr);
        $lastIdentifier = '';

        for ($current_line = $FIRST_CONTENT_LINE; ((($current_line <= (count($DATA) - 1)) && $line = parseCSVLine($importConfig[$TODOfilename]['fieldsep'], $DATA[$current_line])) && ($current_line <= $importlimit)); ++$current_line)
        {
            if ($DATA[$current_line] == '')
            {
                continue;
            }
            $line = array_map('cleanQuotes', $line);
            if ($scdebug)
            {
                echo 'line '.$current_line.': ';
            }
            $line[count($line) - 1] = rtrim($line[count($line) - 1]);
            $TODO = array();
            $TODOSHOP = array();
            $imagesListFromDB = array();
            $imageList = array();
            if ($importConfig[$TODOfilename]['utf8'] == 1)
            {
                utf8_encode_array($line);
            }

            switch ($importConfig[$TODOfilename]['idby']) {
                case 'id_manufacturer':
                    $sql = 'SELECT id_manufacturer,date_upd FROM '._DB_PREFIX_.'manufacturer WHERE id_manufacturer='.(int) findCSVLineValue('id_manufacturer');
                    break;
                case 'name':
                    $sql = 'SELECT id_manufacturer,date_upd FROM '._DB_PREFIX_."manufacturer WHERE name='".pSQL(findCSVLineValue('name'))."'";
                    break;
            }
            $res = Db::getInstance()->getRow($sql);
            if (is_array($res) && count($res))
            {
                $id_manufacturer = $res['id_manufacturer'];
            }
            else
            {
                $id_manufacturer = 0;
            }

            if ($scdebug)
            {
                echo findCSVLineValue('id_manufacturer').' : '.$id_manufacturer.'<br/>';
            }
            if ($scdebug)
            {
                echo 'a';
            }

            if ($CRON && isset($CRON_OLDERTHAN) && $CRON_OLDERTHAN > 0)
            {
                $date_upd = strtotime($res['date_upd']);
                $nowres = Db::getInstance()->getRow('SELECT UNIX_TIMESTAMP() AS ut');
                $now = ($nowres ? $nowres['ut'] : 0);
                if (($date_upd > ($now - ((int) $CRON_OLDERTHAN * 60))))
                { // if not a recent updated object...
                    ++$stats['skipped'];
                    ++$importlimit; // on suppose que tous les éléments ont été créés en BDD : le cron ne sert que pour mettre à jour stock et/ou prix
                    continue;
                }
            }

            if (!empty($id_manufacturer))
            {
                if ($importConfig[$TODOfilename]['forfoundmanufacturer'] == 'skip' && $id_manufacturer)
                {
                    ++$stats['skipped'];
                    if (_s('CAT_IMPORT_IGNORED_LINES') == 1)
                    {
                        unset($CSVData[$current_line]);
                        $needSaveTODO = true;
                    }
                    // ne pas augmenter la limite totale car les prochaines lignes n'ont pas été analysées et donc des éléments peuvent manquer.
                    //$importlimit++;
                    continue;
                }
                elseif ($importConfig[$TODOfilename]['forfoundmanufacturer'] == 'update')
                {
                    $skip = false;

                    if (empty($id_manufacturer))
                    {
                        $skip = true;
                    }

                    if ($skip)
                    {
                        ++$stats['skipped'];
                        if (_s('CAT_IMPORT_IGNORED_LINES') == 1)
                        {
                            unset($CSVData[$current_line]);
                            $needSaveTODO = true;
                        }
                        // ne pas augmenter la limite totale car les prochaines lignes n'ont pas été analysées et donc des éléments peuvent manquer.
                        //$importlimit++;
                        continue;
                    }
                    else
                    {
                        $newManufacturer = new Manufacturer($id_manufacturer);
                        ++$stats['modified'];
                    }
                }
                else
                {
                    $name = findCSVLineValue('name');
                    if (empty($name))
                    {
                        exit(_l('Name can\'t be empty to create a manufacturer: line n°').' '.$current_line);
                    }
                    ## create new manufacturer with default values
                    $newManufacturer = new Manufacturer();
                    $newManufacturer->active = 0;
                    $newManufacturer->name = $name;
                    ++$stats['created'];
                }
            }
            else
            {
                ## IGNORE LIGNE
                if ($importConfig[$TODOfilename]['fornewmanufacturer'] == 'skip')
                {
                    ++$stats['skipped'];
                    if (_s('CAT_IMPORT_IGNORED_LINES') == 1)
                    {
                        unset($CSVData[$current_line]);
                        $needSaveTODO = true;
                    }
                    continue;
                }
                ## CREE NOUVEAU fabricant
                elseif ($importConfig[$TODOfilename]['fornewmanufacturer'] == 'create')
                {
                    if (findCSVLineValue('name') == '')
                    {
                        ++$stats['skipped'];
                        if (_s('CAT_IMPORT_IGNORED_LINES') == 1)
                        {
                            unset($CSVData[$current_line]);
                            $needSaveTODO = true;
                        }
                        continue;
                    }
                    ## create new manufacturer with default values
                    $newManufacturer = new Manufacturer();
                    $newManufacturer->active = 0;
                    $newManufacturer->name = $name;
                    ++$stats['created'];
                }
            }

            if ($scdebug)
            {
                echo 'b';
            }
            foreach ($line as $key => $value)
            {
                $value = trim($value);
                $GLOBALS['import_value'] = $value;
                if ($scdebug && !sc_array_key_exists($key, $firstLineData))
                {
                    echo 'ERR'.$key.'x'.$current_line.'x'.join(';', $line).'xxx'.join(';', array_keys($firstLineData)).'<br/>';
                }
                if (sc_array_key_exists($key, $firstLineData) && sc_in_array($firstLineData[$key], $mappingData['CSVArray'], 'manWinImportProcess_CSVArray'))
                {
                    if ($scdebug)
                    {
                        echo 'c';
                    }
                    $switchObject = $mappingData['CSV2DB'][$firstLineData[$key]];
                    switch ($switchObject) {
                        // CUSTOMER
                        case 'name':
                            $newManufacturer->name = $value;
                            break;
                        case 'description':
                            $newManufacturer->description = $value;
                            break;
                        case 'short_description':
                            $newManufacturer->short_description = $value;
                            break;
                        case 'meta_title':
                            $newManufacturer->meta_title = $value;
                            break;
                        case 'meta_keywords':
                            $newManufacturer->meta_keywords = $value;
                            break;
                        case 'meta_description':
                            $newManufacturer->meta_description = $value;
                            break;
                        case 'id_shop_list':
                            $newManufacturer->id_shop_list = explode($importConfig[$filename]['valuesep'], $value);
                            break;
                        case 'imageURL':
                            if (!empty($value))
                            {
                                $imagefilename = findImageFileName($value);
                                $imagefilenameshort = substr($imagefilename, strlen(SC_CSV_IMPORT_DIR.'images/'), strlen($imagefilename));

                                if (!sc_array_key_exists($value, $imageList))
                                {
                                    if ($imagefilename)
                                    {
                                        if (!copyManImg($newManufacturer->id, $imagefilename))
                                        {
                                            echo _l('Impossible to copy image:').' '.$imagefilename.'<br/>';
                                        }
                                    }
                                    else
                                    {
                                        if (!copyManImg($newManufacturer->id, $value))
                                        {
                                            echo _l('Impossible to copy image:').' '.$value.'<br/>';
                                        }
                                    }
                                    if (file_exists(_PS_MANU_IMG_DIR_.$id_manufacturer.'.jpg'))
                                    {
                                        $imageList[$value] = _PS_MANU_IMG_DIR_.$id_manufacturer.'.jpg';
                                    }
                                }
                                else
                                {
                                    if (!copyManImg($id_manufacturer, $imageList[$value]))
                                    {
                                        echo _l('Impossible to copy image:').' '.$value.'<br/>';
                                    }
                                }
                            }
                            break;

                        ## ACTIONS
                        /*
                        case 'ActionDeleteAllCustomers':
                            if (getBoolean($value)) {
                                if (!empty($newManufacturer->id)) {
                                    $orders = Order::getCustomerOrders((int)$newManufacturer->id);
                                    if (empty($orders)) {
                                        $newManufacturer->delete();
                                    } else {
                                        $newManufacturer->deleted = 1;
                                        $newManufacturer->save();
                                    }
                                }
                            }
                            break;
                        */
                        default:
                            ## Todo pas pour le moment. utile ?
                            ## sc_ext::readImportCustomerCSVConfigXML('importProcessCustomer');
                    }
                }
            }

            if ($scdebug)
            {
                echo 'd';
            }
            $newManufacturer->date_upd = date('Y-m-d H:i:s');
            try
            {
                $newManufacturer->save();
            }
            catch (PrestaShopExceptionException $e)
            {
                exit(_l('Error').' - '.$e->getMessage());
            }
            catch (Exception $e)
            {
                exit(_l('Error').' - '.$e->getMessage());
            }
            $lastid_customer = $newManufacturer->id;
            if ($scdebug)
            {
                echo 'e';
            }
            unset($CSVData[$current_line]);
            file_put_contents(SC_CSV_IMPORT_DIR.'manufacturers/'.$TODOfilename, join("\n", $CSVData));
            $needSaveTODO = false;

            ## Todo pas pour le moment. utile ?
            ## sc_ext::readImportCustomerCSVConfigXML('importProcessCustomerAfter');
        }

        ## Todo pas pour le moment. utile ?
        ## sc_ext::readImportCustomerCSVConfigXML('importProcessAfterCreateAll');

        if ($needSaveTODO)
        {
            file_put_contents(SC_CSV_IMPORT_DIR.'manufacturers/'.$TODOfilename, join("\n", $CSVData));
        }
        echo '<b>'._l('Stats:').'</b><br/>';
        $msg = _l('New manufacturers:').' '.$stats['created'].'<br/>';
        $msg .= _l('Modified manufacturers:').' '.$stats['modified'].'<br/>';
        $msg .= _l('Skipped lines:').' '.$stats['skipped'].'<br/>';
        if (!empty($stats['group_created']))
        {
            $msg .= _l('New groups:').' '.$stats['group_created'].'<br/>';
        }
        echo $msg.'<br/>';

        if ((count($CSVData) == 1) || (count($CSVData) == 2 && $CSVData[0] == join('', $CSVData)) || (filesize(SC_CSV_IMPORT_DIR.'manufacturers/'.$TODOfilename) == 0))
        {
            @unlink(SC_CSV_IMPORT_DIR.'manufacturers/'.$TODOfilename);
            echo _l('All manufacturers have been imported. The TODO file is deleted.').'<br/><br/>';
            echo '<b>'._l('End of import process.').'</b><br/><br/>';
            echo '<b>'._l('You need to refresh the page, click here:').' <a target="_top" href="index.php">Go!</a></b><br/>';
            echo '<script type="text/javascript">window.top.displayOptionsMan();window.top.stopAutoImportMan(true);</script>';
            $msg2 = 'All manufacturers have been imported.';
        }
        else
        {
            echo '<b>'._l('There are still manufacturers to be imported in the working file. It can mean errors you need to correct or lines which have been ignored on purpose. Once corrections have been made, click again on the import icon to proceed further.').'</b><br/><br/>';
            echo '<script type="text/javascript">window.top.displayOptionsMan();window.top.prepareNextStepMan('.($stats['created'] + $stats['modified'] + $stats['skipped'] == 0 ? 0 : filesize(SC_CSV_IMPORT_DIR.'manufacturers/'.$TODOfilename)).');</script>';
            $msg2 = 'Need fix and run import again.';
        }
        $msg3 = '';
        if ($CRON)
        {
            $msg3 .= _l('CRON task name')._l(':').' '.$CRON_NAME.'<br/>';
            $msg3 .= (isset($CRON_DELETETODO) && $CRON_DELETETODO ? $TODOfilename.' '._l('deleted').'<br/>' : '');
            $msg3 .= _l('Update manufacturers older than').' '.$CRON_OLDERTHAN;
        }
        addToHistory('customer_import', 'import', '', '', '', '', 'Imported file: '.$TODOfilename.'<br/>'.$msg.$msg2.($msg3 != '' ? '<br/>'.$msg3 : ''), '');
        echo '</div>';
        break;
}
