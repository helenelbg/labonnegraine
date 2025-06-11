<?php

function readManImportConfigXML($files)
{
    global $importConfig;
    $importConfig = array();
    ## read config
    if ($feed = @simplexml_load_file(SC_CSV_IMPORT_DIR.'manufacturers/'.SC_CSV_IMPORT_CONF))
    {
        foreach ($feed->csvfile as $file)
        {
            if (strpos((string) $file->name, '&') === false)
            {
                $importConfig[(string) $file->name] = array(
                    'name' => (string) $file->name,
                    'supplier' => (string) $file->supplier,
                    'mapping' => (string) $file->mapping,
                    'fieldsep' => (string) $file->fieldsep,
                    'valuesep' => (string) $file->valuesep,
                    'utf8' => (string) $file->utf8,
                    'idby' => (string) $file->idby,
                    'forfoundmanufacturer' => (string) $file->forfoundmanufacturer, ## garder cette ligne pour convertir les anciens fichiers XML des clients
                    'fornewmanufacturer' => (string) $file->fornewmanufacturer,
                    'firstlinecontent' => (string) $file->firstlinecontent,
                    'importlimit' => (string) $file->importlimit,
                );
            }
        }
    }
    ## config by default
    foreach ($files as $file)
    {
        if ($file != '' && !sc_in_array($file, array_keys($importConfig), 'manWinImportProcess_arraykeysimportConfig')
            && strpos($file, '&') === false)
        {
            $importConfig[$file] = array(
                'name' => $file,
                'mapping' => '',
                'fieldsep' => 'dcomma',
                'valuesep' => ',',
                'utf8' => '1',
                'idby' => 'id_manufacturer',
                'forfoundmanufacturer' => 'skip',
                'fornewmanufacturer' => 'create',
                'firstlinecontent' => '',
                'importlimit' => '500',
            );
        }
    }
}

function writeManImportConfigXML()
{
    global $importConfig;
    $content = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
    $content .= '<csvfiles>'."\n";
    foreach ($importConfig as $conf)
    {
        if (file_exists(SC_CSV_IMPORT_DIR.'manufacturers/'.$conf['name']))
        {
            $content .= '<csvfile>'."\n";
            $content .= '<name><![CDATA['.$conf['name'].']]></name>';
            $content .= '<mapping><![CDATA['.$conf['mapping'].']]></mapping>';
            $content .= '<fieldsep><![CDATA['.$conf['fieldsep'].']]></fieldsep>';
            $content .= '<valuesep><![CDATA['.$conf['valuesep'].']]></valuesep>';
            $content .= '<utf8><![CDATA['.$conf['utf8'].']]></utf8>';
            $content .= '<idby><![CDATA['.$conf['idby'].']]></idby>';
            $content .= '<forfoundmanufacturer><![CDATA['.$conf['forfoundmanufacturer'].']]></forfoundmanufacturer>';
            $content .= '<fornewmanufacturer><![CDATA['.$conf['fornewmanufacturer'].']]></fornewmanufacturer>';
            $content .= '<firstlinecontent><![CDATA['.$conf['firstlinecontent'].']]></firstlinecontent>';
            $content .= '<importlimit><![CDATA['.$conf['importlimit'].']]></importlimit>';
            $content .= '</csvfile>'."\n";
        }
    }
    $content .= '</csvfiles>';

    return file_put_contents(SC_CSV_IMPORT_DIR.'manufacturers/'.SC_CSV_IMPORT_CONF, $content);
}

function parseCSVLine($fieldsep, $strline)
{
    global $firstLineData;
    $strline = join($fieldsep, $firstLineData)."\r\n".$strline."\r\n";
    $csv = new parseCSV();
    $csv->delimiter = $fieldsep;
    $csv->parse($strline);
    if (count($csv->data))
    {
        $result = array_values($csv->data[0]);
    }
    else
    {
        $result = array();
    }
    if (count($result) == count($firstLineData) - 1)
    {
        $result[] = '';
    }

    return $result;
}

function getBoolean($value)
{
    if (sc_in_array(Tools::strtoupper($value), array('1', 'YES', 'TRUE', 'VRAI', 'OUI', 'ON'), 'manWinImportProcess_getboolean'))
    {
        return true;
    }

    return false;
}

function fieldInMapping($field)
{
    global $line, $firstLineData, $mappingData;
    $return = false;
    foreach ($line as $k => $v)
    {
        if (sc_in_array($firstLineData[$k], $mappingData['CSVArray'], 'manWinImportProcess_CSVArray') && $mappingData['CSV2DB'][$firstLineData[$k]] == $field)
        {
            $return = true;
        }
    }

    return $return;
}

function findCSVLineValue($valueToFind)
{
    global $line, $firstLineData, $mappingData, $arrayFlipCache;
    foreach ($line as $k => $v)
    {
        if (!sc_array_key_exists($k, $firstLineData))
        {
            return '';
        }
        if (sc_in_array($firstLineData[$k], $mappingData['CSVArray'], 'manWinImportProcess_CSVArray') && $mappingData['CSV2DB'][$firstLineData[$k]] == $valueToFind)
        {
            return $v;
        }
    }

    return '';
}

function findCSVLineValueByLang($valueToFind, $id_lang)
{
    global $line, $firstLineData, $mappingData, $getIDlangByISO;
    foreach ($line as $k => $v)
    {
        if (sc_in_array($firstLineData[$k], $mappingData['CSVArray'], 'manWinImportProcess_CSVArray') && $mappingData['CSV2DB'][$firstLineData[$k]] == $valueToFind && (int) $getIDlangByISO[$mappingData['CSV2DBOptions'][$firstLineData[$k]]] == $id_lang)
        {
            return $v;
        }
    }

    return '';
}

function findAllCSVLineValue($valueToFind, &$arrayToFill, $optionToGet = null, $fromObject = null)
{
    global $line, $firstLineData, $mappingData, $importConfig, $TODOfilename;

    foreach ($line as $k => $v)
    {
        if (sc_in_array($firstLineData[$k], $mappingData['CSVArray'], 'manWinImportProcess_CSVArray') && sc_array_key_exists($firstLineData[$k], $mappingData['CSV2DB']) && $mappingData['CSV2DB'][$firstLineData[$k]] == $valueToFind)
        {
            if ($valueToFind == 'attribute_multiple')
            {
                $vArray = explode($importConfig[$TODOfilename]['valuesep'], $v);
                foreach ($vArray as $val)
                {
                    @$arrayToFill[] = array('object' => $firstLineData[$k],
                        'value' => trim($val),
                        $optionToGet => $fromObject[$mappingData['CSV2DBOptions'][$firstLineData[$k]]],
                        'option' => $mappingData['CSV2DBOptions'][$firstLineData[$k]],
                        'color_attr_options' => '',
                    );
                }
            }
            elseif ($valueToFind == 'attribute')
            {
                $attr_color = findCSVLineValue('attribute_color');
                $attr_texture = findCSVLineValue('attribute_texture');
                @$arrayToFill[] = array('object' => $firstLineData[$k],
                    'value' => trim($v),
                    $optionToGet => $fromObject[$mappingData['CSV2DBOptions'][$firstLineData[$k]]],
                    'option' => $mappingData['CSV2DBOptions'][$firstLineData[$k]],
                    'color_attr_options' => ($attr_color ? $attr_color : '').'_|_'.($attr_texture ? $attr_texture : ''),
                );
            }
            else
            {
                if (($valueToFind != 'feature' && $valueToFind != 'feature_custom') || (($valueToFind == 'feature' || $valueToFind == 'feature_custom') && trim($v) != '-'))
                {
                    if (empty($fromObject) || empty($optionToGet))
                    {
                        @$arrayToFill[] = array('object' => $firstLineData[$k],
                            'value' => trim($v),
                            'option' => $mappingData['CSV2DBOptions'][$firstLineData[$k]],
                            'color_attr_options' => '',
                        );
                    }
                    else
                    {
                        @$arrayToFill[] = array('object' => $firstLineData[$k],
                            'value' => trim($v),
                            $optionToGet => $fromObject[$mappingData['CSV2DBOptions'][$firstLineData[$k]]],
                            'option' => $mappingData['CSV2DBOptions'][$firstLineData[$k]],
                            'color_attr_options' => '',
                        );
                    }
                }
            }
        }
    }
}

function getIDAttributeGroupByCSVColumnName($name)
{
    global $dataArray_attributegroup;
    foreach ($dataArray_attributegroup as $item)
    {
        if ($item['object'] == $name)
        {
            return $item['id_attribute_group'];
        }
    }

    return 0;
}

function copyManImg($id_manufacturer, $url)
{
    $tmpfile = _PS_MANU_IMG_DIR_.$id_manufacturer.'.jpg';
    $copy = copy(urlencode($url), $tmpfile);
    $images_types = ImageType::getImagesTypes('manufacturers');
    $generate_hight_dpi_images = (bool) Configuration::get('PS_HIGHT_DPI');
    if ($copy)
    {
        foreach ($images_types as $image_type)
        {
            ImageManager::resize(
                _PS_MANU_IMG_DIR_.$id_manufacturer.'.jpg',
                _PS_MANU_IMG_DIR_.$id_manufacturer.'-'.stripslashes($image_type['name']).'.jpg',
                (int) $image_type['width'],
                (int) $image_type['height']
            );

            if ($generate_hight_dpi_images)
            {
                ImageManager::resize(
                    _PS_MANU_IMG_DIR_.$id_manufacturer.'.jpg',
                    _PS_MANU_IMG_DIR_.$id_manufacturer.'-'.stripslashes($image_type['name']).'2x.jpg',
                    (int) $image_type['width'] * 2,
                    (int) $image_type['height'] * 2
                );
            }
        }
    }
    else
    {
        $data = sc_file_get_contents($url);
        $handle = fopen($tmpfile, 'w');
        fwrite($handle, $data);
        fclose($handle);
        if (!file_exists($tmpfile))
        {
            @unlink($tmpfile);

            return false;
        }
        else
        {
            foreach ($images_types as $image_type)
            {
                ImageManager::resize(
                    _PS_MANU_IMG_DIR_.$id_manufacturer.'.jpg',
                    _PS_MANU_IMG_DIR_.$id_manufacturer.'-'.stripslashes($image_type['name']).'.jpg',
                    (int) $image_type['width'],
                    (int) $image_type['height']
                );

                if ($generate_hight_dpi_images)
                {
                    ImageManager::resize(
                        _PS_MANU_IMG_DIR_.$id_manufacturer.'.jpg',
                        _PS_MANU_IMG_DIR_.$id_manufacturer.'-'.stripslashes($image_type['name']).'2x.jpg',
                        (int) $image_type['width'] * 2,
                        (int) $image_type['height'] * 2
                    );
                }
            }
        }
    }

    return true;
}

function findImageFileName($filename)
{
    if (strpos($filename, 'http://') !== false)
    {
        return false;
    }
    $basefile = SC_CSV_IMPORT_DIR.'images/'.$filename;
    $files = array(
        $basefile,
        $basefile.'.jpg',
        $basefile.'.png',
        $basefile.'.gif',
        $basefile.'.JPG', $basefile.'.PNG', $basefile.'.GIF',
        $basefile.'.Jpg', $basefile.'.Png', $basefile.'.Gif',
    );
    foreach ($files as $file)
    {
        if (file_exists($file))
        {
            return $file;
        }
    }

    return false;
}

function loadMappingMan($filename)
{
    global $sc_agent;
    if ($filename == '')
    {
        return '';
    }
    if (strpos($filename, '.map.xml') === false)
    {
        $filename = $filename.'.map.xml';
    }
    $content = '';
    if (file_exists(SC_CSV_IMPORT_DIR.'manufacturers/'.$filename)
        && $feed = simplexml_load_file(SC_CSV_IMPORT_DIR.'manufacturers/'.$filename))
    {
        $id_lang = (int) $feed->id_lang;
        if (!$id_lang)
        {
            $id_lang = (int) $sc_agent->id_lang;
        }
        $groups = Db::getInstance()->executeS('SELECT DISTINCT agl.`name`, ag.*, agl.*
                                            FROM `'._DB_PREFIX_.'attribute_group` ag
                                            LEFT JOIN `'._DB_PREFIX_.'attribute_group_lang` agl
                                                ON (ag.`id_attribute_group` = agl.`id_attribute_group` 
                                                AND `id_lang` = '.(int) $id_lang.')
                                            ORDER BY `name` ASC');
        $groupsName = array();
        foreach ($groups as $g)
        {
            $groupsName[] = $g['name'];
        }
        foreach ($feed->map as $map)
        {
            if (!sc_in_array(trim((string) $map->dbname), array('attribute', 'attribute_multiple'), 'manWinImportProcess_attrfields')
                || (sc_in_array(trim((string) $map->dbname), array('attribute', 'attribute_multiple'), 'manWinImportProcess_attrfields') && sc_in_array(trim((string) $map->options), $groupsName, 'manWinImportProcess_groupsName'))
                || (SCAS && ($map->dbname == 'quantity' || $map->dbname == 'location')))
            {
                $content .= trim((string) $map->csvname).','.trim((string) $map->dbname).','.trim((string) $map->options).';';
            }
            else
            { ## we skip attribute group value if not available
                $content .= trim((string) $map->csvname).','.trim((string) $map->dbname).',;';
            }
        }
    }

    return $content;
}

function getReadableError()
{
    $error = error_get_last();
    $return = '';
    if ($error['type'] === E_ERROR)
    {
        $message = $error['message'];
        $return .= _l('Your CSV import process stopped due to an error').'<br/><br/>';

        if (strpos($message, 'Product->name is not valid') !== false)
        {
            $return .= '<b>'._l('Product:name contains one or more invalid characters such as ^<>;=#{}').'</b>';
        }
        elseif (strpos($message, 'Product->name length') !== false)
        {
            $return .= '<b>'._l('Product:name length > 128 characters').'</b>';
        }
        elseif (strpos($message, 'Product->name is empty') !== false)
        {
            $return .= '<b>'._l('Product:name is empty').'</b>';
        }
        elseif (strpos($message, 'Product->link_rewrite is not valid') !== false)
        {
            $return .= '<b>'._l('Product:link rewrite need only contains letters, numbers and following characters: _-').'</b>';
        }
        elseif (strpos($message, 'Product->link_rewrite is empty') !== false)
        {
            $return .= '<b>'._l('Product:link rewrite is empty').'</b>';
        }
        elseif (strpos($message, 'Product->link_rewrite length') !== false)
        {
            $return .= '<b>'._l('Product:link rewrite length > 128 characters').'</b>';
        }
        elseif (strpos($message, 'Product->ean13 is not valid') !== false)
        {
            $return .= '<b>'._l('Product:EAN13 is not valid').'</b><br/>';
        }
        elseif (strpos($message, 'Product->date_add is not valid') !== false)
        {
            $return .= '<b>'._l('Product:date add needs to be YYYY-MM-DD hh:mm:ss').'</b>';
        }
        elseif (strpos($message, 'Product->meta_description is not valid') !== false)
        {
            $return .= '<b>'._l('Product:meta description contains one or more invalid characters such as ^<>;=#{}').'</b>';
        }
        elseif (strpos($message, 'Product->meta_title is not valid') !== false)
        {
            $return .= '<b>'._l('Product:meta title contains one or more invalid characters such as ^<>;=#{}').'</b>';
        }
        elseif (strpos($message, 'Product->description is not valid') !== false)
        {
            $return .= '<b>'._l('Product:description contains iframes (video links) and when the corresponding option is disabled in your PrestaShop backoffice').'</b>';
        }
        elseif (strpos($message, 'Product->price is empty') !== false)
        {
            $return .= '<b>'._l('Product:price is empty').'</b><br/>';
            $return .= '<b>'._l('This error is related to Multistore management, and there are two possible reasons for this error:').'</b><br/>';
            $return .= '<b>-'._l('id_shop_list is not present in your CSV file (to specify in which store(s) to apply the modifications)').'</b><br/>';
            $return .= '<b>-'._l('the product (failing to be modified during the import process) does not exist in one or more shop ID specified in your CSV file').'</b>';
        }
        elseif (strpos($message, 'Combination->reference length') !== false)
        {
            $return .= '<b>'._l('Combination:reference length > 32 characters').'</b>';
        }
        elseif (strpos($message, 'FeatureValue->value is not valid') !== false)
        {
            $return .= '<b>'._l('Feature value:value contains one or more invalid characters such as ^<>;=#{}').'</b>';
        }
        elseif (strpos($message, 'Tag->name length') !== false)
        {
            $return .= '<b>'._l('Tag:name length > 32 characters').'</b>';
        }

        $return .= '<br/><br/>'._l('Download the corresponding TODO file to check and fix.<br/>You will then be able to start your import again.');
        $return .= '<br/><br/>Detail:<pre>'.$message.'</pre>';
    }
    echo $return;
}

function remove_utf8_bom($text)
{
    $bom = pack('H*', 'EFBBBF');
    $text = preg_replace("/^$bom/", '', $text);

    return $text;
}
