<?php

    function readImportConfigXML($files)
    {
        global $importConfig;
        $importConfig = array();
        // read config
        if ($feed = @simplexml_load_file(SC_CSV_IMPORT_DIR.SC_CSV_IMPORT_CONF))
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
                                                        'categorysep' => (string) $file->categorysep,
                                                        'utf8' => (string) $file->utf8,
                                                        'idby' => (string) $file->idby,
                                                        'iffoundindb' => (string) $file->iffoundindb, // garder cette ligne pour convertir les anciens fichiers XML des clients
                                                        'fornewproduct' => (string) $file->fornewproduct,
                                                        'forfoundproduct' => (string) $file->forfoundproduct,
                                                        'firstlinecontent' => (string) $file->firstlinecontent,
                                                        'createcategories' => (string) $file->createcategories,
                                                        'importlimit' => (string) $file->importlimit,
                                                        'createelements' => (string) $file->createelements,
                                                    );
                }
            }
        }
        // config by default
        foreach ($files as $file)
        {
            if ($file != '' && !sc_in_array($file, array_keys($importConfig), 'catWinImportProcess_arraykeysimportConfig') && strpos($file, '&') === false)
            {
                $importConfig[$file] = array(
                                                    'name' => $file,
                                                    'supplier' => '',
                                                    'mapping' => '',
                                                    'fieldsep' => 'dcomma',
                                                    'valuesep' => ',',
                                                    'categorysep' => ',',
                                                    'utf8' => '1',
                                                    'idby' => 'prodname',
                                                    'fornewproduct' => 'skip',
                                                    'forfoundproduct' => 'skip',
                                                    'firstlinecontent' => '',
                                                    'createcategories' => '0',
                                                    'importlimit' => '500',
                                                    'createelements' => '0',
                                                    );
            }
        }
    }

    function writeImportConfigXML()
    {
        global $importConfig;
        $content = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        $content .= '<csvfiles>'."\n";
        foreach ($importConfig as $conf)
        {
            if (file_exists(SC_CSV_IMPORT_DIR.$conf['name']))
            {
                $content .= '<csvfile>'."\n";
                $content .= '<name><![CDATA['.$conf['name'].']]></name>';
                $content .= '<supplier><![CDATA['.$conf['supplier'].']]></supplier>';
                $content .= '<mapping><![CDATA['.$conf['mapping'].']]></mapping>';
                $content .= '<fieldsep><![CDATA['.$conf['fieldsep'].']]></fieldsep>';
                $content .= '<valuesep><![CDATA['.$conf['valuesep'].']]></valuesep>';
                $content .= '<categorysep><![CDATA['.$conf['categorysep'].']]></categorysep>';
                $content .= '<utf8><![CDATA['.$conf['utf8'].']]></utf8>';
                $content .= '<idby><![CDATA['.$conf['idby'].']]></idby>';
                $content .= '<fornewproduct><![CDATA['.$conf['fornewproduct'].']]></fornewproduct>';
                $content .= '<forfoundproduct><![CDATA['.$conf['forfoundproduct'].']]></forfoundproduct>';
                $content .= '<firstlinecontent><![CDATA['.$conf['firstlinecontent'].']]></firstlinecontent>';
                $content .= '<createcategories><![CDATA['.$conf['createcategories'].']]></createcategories>';
                $content .= '<importlimit><![CDATA['.$conf['importlimit'].']]></importlimit>';
                $content .= '<createelements><![CDATA['.$conf['createelements'].']]></createelements>';
                $content .= '</csvfile>'."\n";
            }
        }
        $content .= '</csvfiles>';

        return file_put_contents(SC_CSV_IMPORT_DIR.SC_CSV_IMPORT_CONF, $content);
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
        if (sc_in_array(Tools::strtoupper($value), array('1', 'YES', 'TRUE', 'VRAI', 'OUI', 'ON'), 'catWinImportProcess_getboolean'))
        {
            return true;
        }

        return false;
    }

    $id_cat_root = Configuration::get('PS_ROOT_CATEGORY');
    function getCategoryPath($id_category, $path = '')
    {
        global $categoryNameByID,$categoriesProperties,$id_cat_root;
        if (!empty($id_category) && $id_category > 0 && $id_category != $id_cat_root)
        {
            if (!sc_array_key_exists($id_category, $categoriesProperties))
            {
                exit(_l('You should use the tool "check and fix the level_depth field" from the Catalog > Tools menu to fix your categories.').' (id_category:'.$id_category.')');
            }

            return getCategoryPath($categoriesProperties[$id_category]['id_parent'], ' > '.$categoryNameByID[$id_category].$path);
        }
        else
        {
            return trim($path, ' > ');
        }
    }
    function forceCategoryPathFormat($path)
    {
        $tmp = explode('>', $path);
        $tmp = array_map('trim', $tmp);

        return join(' > ', $tmp);
    }

    function checkAndCreateCategory($categList, $id_parent = 1)
    {
        global $languages,$create_categories,$categoriesFirstLevel,$categoryIDByPath,$categories,$categoryNameByID,$categoriesProperties;
        if ($create_categories >= 1)
        {
            if (is_array($categList))
            {
                foreach ($categList as $categ)
                {
                    checkAndCreateCategory(trim($categ));
                }
            }
            else
            {
                if (strpos($categList, '>') != false)
                {
                    $categ = explode('>', $categList);
                    $categ = array_map('trim', $categ);
                    $levdep = 1;
                    foreach ($categ as $k => $c)
                    {
                        $pathSliced = join(' > ', array_slice($categ, 0, $k + 1));
                        if (!sc_array_key_exists(forceCategoryPathFormat($pathSliced), $categoryIDByPath))
                        {
                            $newCateg = new Category();
                            $newCateg->id_parent = $id_parent;
                            foreach ($languages as $lang)
                            {
                                $newCateg->name[$lang['id_lang']] = trim($c);
                                $newCateg->link_rewrite[$lang['id_lang']] = link_rewrite($c, $lang['iso_code']);
                            }
                            $newCateg->level_depth = $levdep;
                            ++$levdep;
                            $newCateg->active = (int) _s('CAT_IMPORT_CATEGCREA_ACTIVE');
                            if (version_compare(_PS_VERSION_, '1.5.0.0', '<'))
                            {
                                $newCateg->position = SCI::getLastPositionFromCategory(1);
                            }
                            $newCateg->save();
                            $groups = $newCateg->getGroups();
                            if (!sc_in_array(1, $groups, 'catWinImportProcess_categorygroups_'.$newCateg->id))
                            {
                                $newCateg->addGroups(array(1));
                            }
                            if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                            {
                                $shops = Category::getShopsByCategory((int) $id_parent);
                                foreach ($shops as $shop)
                                {
                                    $position = SCI::getLastPositionFromCategory((int) $id_parent, (int) $shop['id_shop']);
                                    if (!$position)
                                    {
                                        $position = 1;
                                    }
                                    $newCateg->addPosition($position, $shop['id_shop']);
                                }
                            }
                            $categories[trim($c)] = array('id_category' => $newCateg->id, 'id_parent' => $id_parent);
                            $categoryNameByID[$newCateg->id] = $c;
                            $categoriesProperties[$newCateg->id] = array('id_category' => $newCateg->id, 'id_parent' => $id_parent);
                            $categoryIDByPath[getCategoryPath($newCateg->id)] = $newCateg->id;
                        }
                        $id_parent = $categoryIDByPath[$pathSliced];
                    }
                }
                else
                {
                    // create categ when no path '>' is set, to categoriesFirstLevel
                    if (!sc_in_array($categList, $categoriesFirstLevel, 'catWinImportProcess_categoriesFirstLevel'))
                    {
                        $newCateg = new Category();
                        $newCateg->id_parent = 1;
                        foreach ($languages as $lang)
                        {
                            $newCateg->name[$lang['id_lang']] = trim($categList);
                            $newCateg->link_rewrite[$lang['id_lang']] = link_rewrite($categList, $lang['iso_code']);
                        }
                        $newCateg->level_depth = 1;
                        $newCateg->active = (int) _s('CAT_IMPORT_CATEGCREA_ACTIVE');
                        if (version_compare(_PS_VERSION_, '1.5.0.0', '<'))
                        {
                            $newCateg->position = SCI::getLastPositionFromCategory(1);
                        }
                        $newCateg->save();
                        $newCateg->addGroups(array(1));
                        if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                        {
                            $shops = Category::getShopsByCategory((int) $id_parent);
                            foreach ($shops as $shop)
                            {
                                $position = SCI::getLastPositionFromCategory((int) $id_parent, $shop['id_shop']);
                                if (!$position)
                                {
                                    $position = 1;
                                }
                                $newCateg->addPosition($position, $shop['id_shop']);
                            }
                        }
                        $categories[$categList] = array('id_category' => $newCateg->id, 'id_parent' => 1);
                        $categoryNameByID[$newCateg->id] = $categList;
                        $categoriesProperties[$newCateg->id] = array('id_category' => $newCateg->id, 'id_parent' => 1);
                        $categoryIDByPath[getCategoryPath($newCateg->id)] = $newCateg->id;
                        $categoriesFirstLevel[] = $categList;
                    }
                }
            }
        }
    }

    function fieldInMapping($field)
    {
        global $line,$firstLineData,$mappingData;
        $return = false;
        foreach ($line as $k => $v)
        {
            if (sc_in_array($firstLineData[$k], $mappingData['CSVArray'], 'catWinImportProcess_CSVArray') && $mappingData['CSV2DB'][$firstLineData[$k]] == $field)
            {
                $return = true;
            }
        }

        return $return;
    }

    function findCSVLineValue($valueToFind)
    {
        global $line,$firstLineData,$mappingData,$arrayFlipCache;
        foreach ($line as $k => $v)
        {
            if (!sc_array_key_exists($k, $firstLineData))
            {
                return '';
            }
            if (sc_in_array($firstLineData[$k], $mappingData['CSVArray'], 'catWinImportProcess_CSVArray') && $mappingData['CSV2DB'][$firstLineData[$k]] == $valueToFind)
            {
                return $v;
            }
        }

        return '';
    }

    function findCSVLineValueByLang($valueToFind, $id_lang)
    {
        global $line,$firstLineData,$mappingData,$getIDlangByISO;
        foreach ($line as $k => $v)
        {
            if (sc_in_array($firstLineData[$k], $mappingData['CSVArray'], 'catWinImportProcess_CSVArray') && $mappingData['CSV2DB'][$firstLineData[$k]] == $valueToFind && (int) $getIDlangByISO[$mappingData['CSV2DBOptions'][$firstLineData[$k]]] == $id_lang)
            {
                return $v;
            }
        }

        return '';
    }

    function findAllCSVLineValue($valueToFind, &$arrayToFill, $optionToGet = null, $fromObject = null)
    {
        global $line,$firstLineData,$mappingData,$importConfig,$TODOfilename;

        foreach ($line as $k => $v)
        {
            if (sc_in_array($firstLineData[$k], $mappingData['CSVArray'], 'catWinImportProcess_CSVArray') && sc_array_key_exists($firstLineData[$k], $mappingData['CSV2DB']) && $mappingData['CSV2DB'][$firstLineData[$k]] == $valueToFind)
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

    function createMultiLangField($field)
    {
        $languages = Language::getLanguages();
        $res = array();
        foreach ($languages as $lang)
        {
            $res[$lang['id_lang']] = $field;
        }

        return $res;
    }

    function copyImg($id_entity, $id_image, $url, $entity = 'products')
    {
        $parsed_url = parse_url($url);
        if (array_key_exists('scheme', $parsed_url) && in_array($parsed_url['scheme'], array('http', 'https')))
        {
            $headers = get_headers(urlencode($url));
            $code_header = (int) substr($headers[0], 9, 3);
            if ($headers != false && !in_array($code_header, array(200, 301, 302)))
            {
                return false;
            }
        }
        $tmpfile = tempnam(_PS_TMP_IMG_DIR_, 'ps_import');
        switch ($entity) {
            default:
            case 'products':
                $path = _PS_PROD_IMG_DIR_.getImgPath((int) $id_entity, (int) $id_image, '', '');
                break;
            case 'categories':
                $path = _PS_CAT_IMG_DIR_.(int) $id_entity;
                break;
        }
        $copy = copy(urlencode($url), $tmpfile);
        $generate_hight_dpi_images = (bool) SCI::getConfigurationValue('PS_HIGHT_DPI');
        if ($copy)
        {
            SCI::imageResize($tmpfile, _PS_PROD_IMG_DIR_.getImgPath((int) $id_entity, (int) $id_image));
            $imagesTypes = ImageType::getImagesTypes($entity);
            foreach ($imagesTypes as $k => $imageType)
            {
                $img_name = _PS_PROD_IMG_DIR_.getImgPath((int) $id_entity, (int) $id_image, stripslashes($imageType['name']));
                SCI::imageResize($tmpfile, $img_name, $imageType['width'], $imageType['height']);
                if ($generate_hight_dpi_images)
                {
                    $img_name = str_replace('.jpg', '2x.jpg', $img_name);
                    SCI::imageResize($tmpfile, $img_name, $imageType['width'] * 2, $imageType['height'] * 2);
                }
            }
            // Hook watermark optimization
            if (file_exists(_PS_PROD_IMG_DIR_.getImgPath((int) $id_entity, (int) $id_image)))
            {
                SCI::hookExec('watermark', array('id_image' => $id_image, 'id_product' => $id_entity));
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
                SCI::imageResize($tmpfile, _PS_PROD_IMG_DIR_.getImgPath((int) $id_entity, (int) $id_image));
                $imagesTypes = ImageType::getImagesTypes($entity);
                foreach ($imagesTypes as $k => $imageType)
                {
                    $img_name = _PS_PROD_IMG_DIR_.getImgPath((int) $id_entity, (int) $id_image, stripslashes($imageType['name']));
                    SCI::imageResize($tmpfile, $img_name, $imageType['width'], $imageType['height']);
                    if ($generate_hight_dpi_images)
                    {
                        $img_name = str_replace('.jpg', '2x.jpg', $img_name);
                        SCI::imageResize($tmpfile, $img_name, $imageType['width'] * 2, $imageType['height'] * 2);
                    }
                }
                // Hook watermark optimization
                if (file_exists(_PS_PROD_IMG_DIR_.getImgPath((int) $id_entity, (int) $id_image)))
                {
                    SCI::hookExec('watermark', array('id_image' => $id_image, 'id_product' => $id_entity));
                }
            }
        }

        return true;
    }

    function createCombinations($list)
    {
        if (sizeof($list) <= 1)
        {
            return sizeof($list) ? array_map(function ($v)
            {
                return array($v);
            }, $list[0]) : $list;
        }
        $res = array();
        $first = array_pop($list);
        foreach ($first as $attribute)
        {
            $tab = createCombinations($list);
            foreach ($tab as $toAdd)
            {
                $res[] = is_array($toAdd) ? array_merge($toAdd, array($attribute)) : array($toAdd, $attribute);
            }
        }

        return $res;
    }

    function addAttribute()
    {
        global $combinationValues;

        return $combinationValues;
    }

    function addSupplier($data)
    {
        global $dataDB_supplier,$dataDB_supplierByName;
        if (!is_array($data))
        {
            $data = explode('_|_', $data);
        }
        $newSupplierIDs = array();
        foreach ($data as $supplier)
        {
            if ($supplier != '')
            {
                $newSupplier = new Supplier();
                $newSupplier->name = Tools::substr($supplier, 0, 64);
                $newSupplier->description[(int) Configuration::get('PS_LANG_DEFAULT')] = $supplier;
                $newSupplier->active = 1;
                $newSupplier->save();
                $newSupplierIDs[] = $newSupplier->id;
                if (is_array($dataDB_supplier) && is_array($dataDB_supplierByName))
                {
                    $dataDB_supplier[$newSupplier->id] = $newSupplier->name;
                    $dataDB_supplierByName[$newSupplier->name] = $newSupplier->id;
                }
            }
        }
        SCI::addToShops('supplier', $newSupplierIDs);
    }

    function addManufacturer($data)
    {
        global $dataDB_manufacturer,$dataDB_manufacturerByName;
        if (!is_array($data))
        {
            $data = explode('_|_', $data);
        }
        $newManufacturerIDs = array();
        foreach ($data as $manufacturer)
        {
            if ($manufacturer != '')
            {
                $newManufacturer = new Manufacturer();
                $newManufacturer->name = Tools::substr($manufacturer, 0, 64);
                $newManufacturer->active = 1;
                $newManufacturer->save();
                $newManufacturerIDs[] = $newManufacturer->id;
                if (is_array($dataDB_manufacturer) && is_array($dataDB_manufacturerByName))
                {
                    $dataDB_manufacturer[$newManufacturer->id] = $newManufacturer->name;
                    $dataDB_manufacturerByName[$newManufacturer->name] = $newManufacturer->id;
                }
            }
        }
        SCI::addToShops('manufacturer', $newManufacturerIDs);
    }

    /*
    ** Cree l'attribut et gere les couleurs / textures
    */
    function addAttributeValue($data)
    {
        $attributeValues = explode('y|y', $data);
        $attributeValues = array_unique($attributeValues);
        $attributeValuesIDs = array();
        $attribute_sorting = (bool) _s('CAT_IMPORT_KEEP_SORTING_ATTRIBUTE_FROM_FILE');
        if (empty($attribute_sorting))
        {
            sort($attributeValues);
        }
        foreach ($attributeValues as $attributeValue)
        {
            if ($attributeValue != '')
            {
                $av = explode('x|x', $attributeValue);
                if ($av[0] != '' && $av[1] != '')
                {
                    $options = explode('_|_', $av[1]);
                    $av[1] = (count($options) > 1 ? $options[1] : '');
                    if ($av[1] != '')
                    {
                        if (version_compare(_PS_VERSION_, '8.0.0', '>='))
                        {
                            $newAttributeValue = new ProductAttribute();
                        }
                        else
                        {
                            $newAttributeValue = new Attribute();
                        }
                        $newAttributeValue->id_attribute_group = (int) $av[0];
                        if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                        {
                            $languages = Language::getLanguages(true, false);
                        }
                        else
                        {
                            $languages = Language::getLanguages(true);
                        }
                        foreach ($languages as $language)
                        {
                            $newAttributeValue->name[(int) $language['id_lang']] = Tools::substr($av[1], 0, 255);
                        }
                        if (isset($options[2]) && $options[2] != '')
                        {
                            $newAttributeValue->color = $options[2];
                        }
                        $newAttributeValue->save();
                        $attributeValuesIDs[] = $newAttributeValue->id;

                        // textures
                        if (isset($options[3]) && $options[3] != '')
                        {
                            @copy(SC_CSV_IMPORT_DIR.'images/'.$options[3], _PS_COL_IMG_DIR_.$newAttributeValue->id.'.jpg');
                        }
                    }
                }
            }
        }
        SCI::addToShops('attribute', $attributeValuesIDs);
    }

    function addAttributeGroup($data)
    {
        global $dataArray_attributegroup,$languages;
        $groups = explode('y|y', $data);
        $attributeGroupsIDs = array();
        sort($groups);
        foreach ($groups as $group)
        {
            if ($group != '')
            {
                $newGroup = new AttributeGroup();
                if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                {
                    foreach ($languages as $lang)
                    {
                        $newGroup->name[$lang['id_lang']] = Tools::substr($group, 0, 64);
                        $newGroup->public_name[$lang['id_lang']] = Tools::substr($group, 0, 64);
                    }
                    $newGroup->group_type = 'select';
                }
                $newGroup->save();
                $attributeGroupsIDs[] = $newGroup->id;
                foreach ($dataArray_attributegroup as $key => $v)
                {
                    if (array_key_exists('id_attribute_group', $v) && $v['option'] == $group)
                    {
                        $dataArray_attributegroup[$key]['id_attribute_group'] = $newGroup->id;
                    }
                }
            }
        }
        SCI::addToShops('attribute_group', $attributeGroupsIDs);
    }

    function addFeatureValue($data)
    {
        $featureValues = explode('y|y', $data);
        $featureValues = array_unique($featureValues);
        sort($featureValues);
        foreach ($featureValues as $featureValue)
        {
            if ($featureValue != '')
            {
                $fv = explode('x|x', $featureValue);
                if ($fv[0] != '' && $fv[1] != '')
                {
                    $tmp = explode('_|_', $fv[1]);
                    $fv[1] = (count($tmp) > 1 ? $tmp[1] : '');
                    if ($fv[1] != '')
                    {
                        $newFeatureValue = new FeatureValue();
                        $newFeatureValue->id_feature = (int) $fv[0];
                        $size = (version_compare(_PS_VERSION_, '1.5.0.0', '>=') && array_key_exists('size', FeatureValue::$definition['fields']['value']) ? (int) FeatureValue::$definition['fields']['value']['size'] : 255);
                        $newFeatureValue->value[(int) Configuration::get('PS_LANG_DEFAULT')] = trim(cleanQuotes(Tools::substr($fv[1], 0, $size)));
                        $newFeatureValue->save();
                    }
                }
            }
        }
    }

    function addFeature($data)
    {
        global $dataArray_feature;
        $features = explode('y|y', $data);
        sort($features);
        if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
        {
            $shops = Shop::getShops(false, null, true);
        }
        foreach ($features as $feature)
        {
            if ($feature != '')
            {
                $newFeature = new Feature();
                $size = (version_compare(_PS_VERSION_, '1.5.0.0', '>=') && array_key_exists('size', Feature::$definition['fields']['name']) ? (int) Feature::$definition['fields']['name']['size'] : 128);
                $newFeature->name[(int) Configuration::get('PS_LANG_DEFAULT')] = trim(cleanQuotes(Tools::substr($feature, 0, $size)));
                if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                {
                    $newFeature->id_shop_list = $shops;
                }
                $newFeature->save();
                foreach ($dataArray_feature as $key => $v)
                {
                    if (array_key_exists('id_feature', $v) && $v['option'] == $feature)
                    {
                        $dataArray_feature[$key]['id_feature'] = $newFeature->id;
                    }
                }
            }
        }
    }

    function isCombination($lineToCheck = null)
    {
        global $mappingData,$id_product_attribute,$firstLineData;
        if ($id_product_attribute == 0 && (sc_in_array('attribute', $mappingData['DBArray'], 'catWinImportProcess_DBArray') || sc_in_array('attribute_multiple', $mappingData['DBArray'], 'catWinImportProcess_DBArray')))
        {
            // check only if the attribute filed type is used
            if ($lineToCheck == null)
            {
                return true;
            }
            else
            {
                // if we need to check field presence and data
                $lineToCheck = array_map('cleanQuotes', $lineToCheck);
                foreach ($lineToCheck as $key => $val)
                {
                    if (sc_array_key_exists($firstLineData[$key], $mappingData['CSV2DB']) && sc_in_array($mappingData['CSV2DB'][$firstLineData[$key]], array('attribute', 'attribute_multiple'), 'catWinImportProcess_attrfields') && $val != '')
                    {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    function isCombinationWithID()
    {
        global $mappingData,$id_product_attribute;
        if (sc_in_array('id_product_attribute', $mappingData['DBArray'], 'catWinImportProcess_DBArray') || $id_product_attribute > 0)
        {
            return true;
        }

        return false;
    }

    function findImageFileName($filename)
    {
        $is_url = isUrl($filename);
        if ($is_url)
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

    function loadMapping($filename)
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
        if (file_exists(SC_CSV_IMPORT_DIR.$filename) && $feed = simplexml_load_file(SC_CSV_IMPORT_DIR.$filename))
        {
            $id_lang = (int) $feed->id_lang;
            if (!$id_lang)
            {
                $id_lang = (int) $sc_agent->id_lang;
            }
            $groups = Db::getInstance()->executeS('
                SELECT DISTINCT agl.`name`, ag.*, agl.*
                FROM `'._DB_PREFIX_.'attribute_group` ag
                LEFT JOIN `'._DB_PREFIX_.'attribute_group_lang` agl
                    ON (ag.`id_attribute_group` = agl.`id_attribute_group` AND `id_lang` = '.(int) $id_lang.')
                ORDER BY `name` ASC');
            $groupsName = array();
            foreach ($groups as $g)
            {
                $groupsName[] = $g['name'];
            }
            foreach ($feed->map as $map)
            {
                if (!sc_in_array(trim((string) $map->dbname), array('attribute', 'attribute_multiple'), 'catWinImportProcess_attrfields')
                        || (sc_in_array(trim((string) $map->dbname), array('attribute', 'attribute_multiple'), 'catWinImportProcess_attrfields') && sc_in_array(trim((string) $map->options), $groupsName, 'catWinImportProcess_groupsName'))
                        || (SCAS && ($map->dbname == 'quantity' || $map->dbname == 'location')))
                {
                    $content .= trim((string) $map->csvname).','.trim((string) $map->dbname).','.trim((string) $map->options).';';
                }
                else
                { // we skip attribute group value if not available
                    $content .= trim((string) $map->csvname).','.trim((string) $map->dbname).',;';
                }
            }
        }

        return $content;
    }

    function refreshCacheAttribute()
    {
        global $defaultLanguage,$attributeGroups,$attributeValues,$attributeValuesNames;
        $sql = 'SELECT agl.id_attribute_group,agl.name
                    FROM '._DB_PREFIX_.'attribute_group_lang agl
                    WHERE agl.id_lang='.(int) $defaultLanguage->id;
        $res = Db::getInstance()->ExecuteS($sql);
        $attributeGroups = array();
        foreach ($res as $fv)
        {
            $attributeGroups[$fv['name']] = $fv['id_attribute_group'];
        }
        $sql = 'SELECT a.id_attribute_group,al.id_attribute,al.name
                    FROM '._DB_PREFIX_.'attribute_lang al
                    LEFT JOIN '._DB_PREFIX_.'attribute a ON (al.id_attribute=a.id_attribute)
                    WHERE al.id_lang='.(int) $defaultLanguage->id;
        $res = Db::getInstance()->ExecuteS($sql);
        $attributeValues = array();
        $attributeValuesNames = array();
        foreach ($res as $fv)
        {
            $attributeValues[$fv['id_attribute_group'].'_|_'.trim($fv['name'])] = $fv['id_attribute'];
            $attributeValuesNames[$fv['id_attribute']] = trim($fv['name']);
        }
    }

    function refreshCacheFeature()
    {
        global $defaultLanguage,$features,$featureValues;
        $sql = 'SELECT fl.id_feature,fl.name
                    FROM '._DB_PREFIX_.'feature_lang fl
                    WHERE fl.id_lang='.(int) $defaultLanguage->id;
        $res = Db::getInstance()->ExecuteS($sql);
        $features = array();
        if (!empty($res))
        {
            foreach ($res as $fv)
            {
                $features[$fv['name']] = $fv['id_feature'];
            }
        }
        ## pre-defined features
        $sql = 'SELECT fvl.id_feature_value,fvl.value,fv.id_feature
                    FROM '._DB_PREFIX_.'feature_value_lang fvl
                    LEFT JOIN '._DB_PREFIX_.'feature_value fv ON (fv.id_feature_value=fvl.id_feature_value)
                    WHERE (fv.custom=0 OR fv.custom IS NULL) AND fvl.id_lang='.(int) $defaultLanguage->id;
        $res = Db::getInstance()->ExecuteS($sql);
        $featureValues = array();
        if (!empty($res))
        {
            foreach ($res as $fv)
            {
                $featureValues[$fv['id_feature'].'_|_'.trim($fv['value'])] = $fv['id_feature_value'];
            }
        }
        ## custom features
        $sql = 'SELECT fvl.id_feature_value,fvl.value,fv.id_feature
                    FROM '._DB_PREFIX_.'feature_value_lang fvl
                    LEFT JOIN '._DB_PREFIX_.'feature_value fv ON (fv.id_feature_value=fvl.id_feature_value)
                    WHERE fv.custom=1 AND fvl.id_lang='.(int) $defaultLanguage->id;
        $res = Db::getInstance()->ExecuteS($sql);
        if (!empty($res))
        {
            foreach ($res as $fv)
            {
                $featureValues['custom'][$fv['id_feature'].'_|_'.trim($fv['value'])] = $fv['id_feature_value'];
            }
        }
    }

    function refreshCacheCategory()
    {
        global $defaultLanguage,$categories,$categoriesProperties,$categoryNameByID,$categoryIDByPath,$categoriesFirstLevel;
        $sql = 'SELECT c.id_category,c.id_parent,cl.name,c.level_depth
                            FROM '._DB_PREFIX_.'category c
                            LEFT JOIN '._DB_PREFIX_.'category_lang cl ON (cl.id_category=c.id_category AND cl.id_lang='.(int) $defaultLanguage->id.')
                            GROUP BY c.id_category
                            ORDER BY c.level_depth ASC';
        $res = Db::getInstance()->ExecuteS($sql);

        foreach ($res as $categ)
        {
            if ($categ['id_category'] == $categ['id_parent'])
            {
                exit(_l('A category cannot be parent of itself, you must fix this error for category ID').' '.$categ['id_category'].' - '.trim($categ['name']));
            }
            $categories[trim($categ['name'])] = array('id_category' => $categ['id_category'], 'id_parent' => $categ['id_parent']);
            $categoryNameByID[$categ['id_category']] = $categ['name'];
            $categoriesProperties[$categ['id_category']] = array('id_category' => $categ['id_category'], 'id_parent' => $categ['id_parent']);
            $categoryIDByPath[getCategoryPath($categ['id_category'])] = $categ['id_category'];
            if ($categ['level_depth'] == 1)
            {
                $categoriesFirstLevel[] = $categ['name'];
            }
        }
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

    /**
     * @param $data
     * @return int
     */
    function getCustomizationTypeInt($data)
    {
        if(is_numeric($data))
        {
            return (int) $data;
        }
        return ($data == 'text' ? 1 : 0);
    }

function remove_utf8_bom($text)
{
    $bom = pack('H*', 'EFBBBF');
    $text = preg_replace("/^$bom/", '', $text);

    return $text;
}
