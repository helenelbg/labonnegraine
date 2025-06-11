<?php

    $debug = false;

    error_reporting(E_ALL ^ E_NOTICE);
    @ini_set('display_errors', 'on');

    if (!isset($CRON))
    {
        $CRON = 0;
    }
    if (!isset($CRONVERSION))
    {
        $CRONVERSION = 1;
    }

    if (!empty($CRON))
    {
        if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
        {
            Context::getContext()->employee = new Employee(Tools::getValue('id_employee'));
        }
        $sc_agent = new SC_Agent();
    }

    require_once SC_DIR.'lib/cat/win-export/cat_win-export_tools.php';

    $action = Tools::getValue('action');
    $id_lang = (int) Tools::getValue('id_lang');
    $mapping = Tools::getValue('mapping');
    $cache = array();

    $cacheCategory = array();
    $cacheCategoryPath = array();
    $categoriesProperties = array();
    $categoryNameByID = array();
    $cacheCarriers = array();

    switch ($action){
        case 'conf_delete':
            $exp_opt_files = Tools::getValue('exp_opt_files', '');
            if ($exp_opt_files == '')
            {
                exit(_l('You should mark at least one file to delete'));
            }
            $exp_opt_files_array = preg_split('/;/', $exp_opt_files);
            foreach ($exp_opt_files_array as $exp_opt_file)
            {
                if ($exp_opt_file != '')
                {
                    if (@unlink(SC_TOOLS_DIR.'cat_export/'.$exp_opt_file.'.script.xml'))
                    {
                        echo $exp_opt_file.' '._l('deleted')."\n";
                    }
                    else
                    {
                        echo _l('Unable to delete this file, please check write permissions:').' '.$exp_opt_file."\n";
                    }
                }
            }
            break;
        case 'conf_add':
            $scriptname = Tools::getValue('scriptname', '');
            readExportConfigXML('');
            writeExportConfigXML($scriptname.'.script.xml');
            break;
        case 'reset_export':
            $return = false;
            $export_id = (int) Tools::getValue('export_id', 0);
            if (!empty($export_id))
            {
                $sql = 'UPDATE '._DB_PREFIX_.'sc_export SET exporting = 0, id_next = 0, id_combination_next = 0 WHERE id_sc_export = '.(int) $export_id;
                Db::getInstance()->Execute($sql);
                $return = true;
            }

            if (!$return)
            {
                echo '<strong style="color: #831f1f;">'._l('An error occured during reset. Please try again.').'</span>';
            }
            else
            {
                echo '<strong style="color: #266e00;">'._l('The export was successfully reset.').'</span>';
            }

            break;
        case 'categselection_load':
            $filename = str_replace('.sel.xml', '', Tools::getValue('filename')).'.sel.xml';
            $cat_selection = array();
            if (file_exists(SC_TOOLS_DIR.'cat_categories_sel/'.$filename) && $feed = simplexml_load_file(SC_TOOLS_DIR.'cat_categories_sel/'.$filename))
            {
                foreach ($feed->category as $category)
                {
                    $cat_selection[] = (int) $category->id;
                }
            }
            echo implode(';', $cat_selection);
            break;
        case 'categselection_saveas':
            $filename = Tools::getValue('filename');
            $categselection = Tools::getValue('categselection');
            @unlink(SC_TOOLS_DIR.'cat_categories_sel/'.$filename.'.sel.xml');
            $categselection = preg_split('/,/', $categselection);
            $content = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n".'<categselection>';
            $contentArray = array();
            foreach ($categselection as $catsel)
            {
                $val = preg_split('/,/', $catsel);
                if (count($val) == 1 && $val[0] != '')
                {
                    $contentArray[(int) $val[0]] = '<category>';
                    $contentArray[(int) $val[0]] .= '<id><![CDATA['.$val[0].']]></id>';
                    $contentArray[(int) $val[0]] .= '</category>'."\n";
                }
            }
            ksort($contentArray);
            $content .= join('', $contentArray).'</categselection>';
            file_put_contents(SC_TOOLS_DIR.'cat_categories_sel/'.$filename.'.sel.xml', $content);
            echo _l('Data saved!');
            break;
        case 'categselection_delete':
            $filename = Tools::getValue('filename');
            @unlink(SC_TOOLS_DIR.'cat_categories_sel/'.$filename.'.sel.xml');
            break;
        case 'export_process':
            global $switchObject,$f,$switchObjectOption,$switchObjectLang,$p,$getIDlangByISO,$getCarrierByName,$field,$id_product,$id_product_attribute; // variable for custom import fields check, used in extensions, do not remove
            // INIT VARS
            $AUTO_EXPORT = (int) Tools::getValue('auto_export', 0);
            $export_limit = (int) Tools::getValue('export_limit', 500);
            $first_interval = (int) Tools::getValue('first_interval', 0);

            $time_start = microtime(true);

            $ALREADY_EXPORTING = false;
            $STOP_SCRIPT = false;

            $switchObject = '';
            $switchObjectOption = '';
            $switchObjectOptionTwo = '';
            $switchObjectLang = '';
            $link = new Link();

                $protocol = Tools::getProtocol(SCI::getConfigurationValue('PS_SSL_ENABLED'));
                $link = new Link($protocol, $protocol);

            $id_product = 0;
            $id_product_attribute = 0;
            $defaultLanguageId = (int) Configuration::get('PS_LANG_DEFAULT');
            $defaultLanguage = new Language($defaultLanguageId);
            $getIDlangByISO = array();
            foreach ($languages as $lang)
            {
                $getIDlangByISO[$lang['iso_code']] = $lang['id_lang'];
            }
            $getCarrierByName = array();
            foreach (Carrier::getCarriers($defaultLanguageId, true, false, false, null, Carrier::ALL_CARRIERS) as $carrier)
            {
                $getCarrierByName[$carrier['name']] = $carrier['id_carrier'];
            }
            $filename = Tools::getValue('filename', 0);
            $sc_active = SCI::getConfigurationValue('SC_PLUG_DISABLECOMBINATIONS', 0);
            $auto_filename = $filename;
            if ($filename === 0)
            {
                if (!$AUTO_EXPORT && !$CRON)
                {
                    exit(_l('You have to select a file.'));
                }
                elseif ($CRON)
                {
                    echo _l('You have to select a file.');
                    $STOP_SCRIPT = true;
                }
                else
                {
                    echo json_encode(array(
                        'type' => 'error',
                        'stop' => 1,
                        'content' => '<strong style="color: #831f1f;">'._l('You have to select a file.', 1).'</span>',
                        'filename' => $auto_filename,
                        'first_interval' => $first_interval,
                    ));
                    exit();
                }
            }
            if ($STOP_SCRIPT)
            {
                break;
            }

            $arrIdAvailableLater = array();
            if (SCI::getConfigurationValue('SC_DELIVERYDATE_INSTALLED') == '1')
            {
                $sql = 'SELECT * FROM '._DB_PREFIX_.'sc_available_later';
                $res = Db::getInstance()->ExecuteS($sql);
                foreach ($res as $row)
                {
                    $arrIdAvailableLater[$row['id_sc_available_later']][$row['id_lang']] = $row['available_later'];
                }
            }

            // fields to search in ps_product_lang
            $fields_lang = array('name', 'available_now', 'link_rewrite', 'meta_title', 'meta_description', 'meta_keywords', 'description_short', 'description', 'name_with_attributes');
            // fields specific to combination, should not be read in Product object
            $combination_fields = array('name_with_attributes');

            $not_auto_combination_fields = array();
            if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
            {
                $not_auto_combination_fields[] = 'supplier_reference';
            }

            $marginMatrix_forms = array(
                    0 => '{price}-{wholesale_price}',
                    1 => '({wholesale_price} > 0 ? ({price}-{wholesale_price})*100/{wholesale_price} : 0)',
                    2 => '({wholesale_price} > 0 ? {price}/{wholesale_price} : 0)',
                    3 => '({wholesale_price} > 0 ? {price_inc_tax}/{wholesale_price} : 0)',
                    4 => '({wholesale_price} > 0 ? ({price_inc_tax}-{wholesale_price})*100/{wholesale_price} : 0)',
                    5 => '({price} > 0 ? ({price}-{wholesale_price})*100/{price} : 0)',
            );
            $marginMatrix_form = $marginMatrix_forms[_s('CAT_PROD_GRID_MARGIN_OPERATION')];

            if (!$CRON && !$AUTO_EXPORT)
            {
                showHeaders();
            }

            if ($AUTO_EXPORT || ($CRON && $CRONVERSION >= 2))
            {
                $sql = 'SELECT * FROM '._DB_PREFIX_."sc_export WHERE name='".pSQL($auto_filename)."'";
                $sc_export = Db::getInstance()->ExecuteS($sql);
                if (!empty($sc_export[0]['id_sc_export']))
                {
                    $sc_export = $sc_export[0];
                }
                else
                {
                    $sql = 'INSERT INTO '._DB_PREFIX_."sc_export (name, last_export, exporting, id_next, id_combination_next, total_lines)
                            VALUES ('".pSQL($auto_filename)."',NULL,0,0,0,0)";
                    Db::getInstance()->Execute($sql);
                    $temp_id = Db::getInstance()->Insert_ID();
                    if (!empty($temp_id))
                    {
                        $sc_export = array('id_sc_export' => $temp_id, 'name' => $auto_filename, 'last_export' => null, 'exporting' => 0, 'id_next' => 0, 'id_combination_next' => 0, 'total_lines' => 0);
                    }
                    else
                    {
                        if ($AUTO_EXPORT)
                        {
                            echo json_encode(array(
                                'type' => 'error',
                                'stop' => 1,
                                'content' => '<strong style="color: #831f1f;">'._l('Error during sc_export creation.', 1).'</span>',
                                'debug' => $sql,
                                'filename' => $auto_filename,
                                'first_interval' => $first_interval,
                            ));
                            exit();
                        }
                        else
                        {
                            echo _l('Error during sc_export creation.');
                            $STOP_SCRIPT = true;
                        }
                    }
                }
            }
            if ($STOP_SCRIPT)
            {
                break;
            }

            // GET MAPPING
            readExportConfigXML($filename);
            if ($exportConfig['fieldsep'] == 'dcomma')
            {
                $exportConfig['fieldsep'] = ';';
            }
            if ($exportConfig['fieldsep'] == 'dcommamac')
            {
                $exportConfig['fieldsep'] = ';';
            }
            if ($exportConfig['fieldsep'] == 'tab')
            {
                $exportConfig['fieldsep'] = '    ';
            }
            if ($exportConfig['enclosedby'] == 'quote')
            {
                $exportConfig['enclosedby'] = '"';
            }
            $selected_shops_id = (int) $exportConfig['shops'];
            if (empty($selected_shops_id))
            {
                $selected_shops_id = SCI::getSelectedShop();
            }
            if (empty($selected_shops_id))
            {
                $selected_shops_id = (int) Configuration::get('PS_SHOP_DEFAULT');
            }

            /* Server Params */
            $server_host = getHttpHost(false, true);
            $protocol = 'http://';
            $protocol_ssl = 'https://';
            $protocol_link = (Configuration::get('PS_SSL_ENABLED')) ? $protocol_ssl : $protocol;
            if (SCMS)
            {
                $shop = new Shop((int) $selected_shops_id);
                $_PS_BASE_URL_ = $protocol_link.$shop->domain.$shop->getBaseURI().'img/p/';
            }
            elseif (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
            {
                $shop = new Shop((int) Configuration::get('PS_SHOP_DEFAULT'));
                $_PS_BASE_URL_ = $protocol_link.$shop->domain.$shop->getBaseURI().'img/p/';
            }
            else
            {
                $_PS_BASE_URL_ = $protocol_link.$server_host._THEME_PROD_DIR_;
            }

            if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
            {
                Context::getContext()->shop = new Shop((int) $selected_shops_id);
                Shop::setContext(Shop::CONTEXT_SHOP, (int) $selected_shops_id);
            }

            // READ MAPPING
            $fieldList = array();
            $mapping = array();
            if ($exportConfig['categoriessel'] == '')
            {
                if (!$AUTO_EXPORT && !$CRON)
                {
                    exit(_l('You have to set the category selection for the script')._l(':').' '.substr($filename, 0, -11));
                }
                elseif ($CRON)
                {
                    echo _l('You have to set the category selection for the script')._l(':').' '.substr($filename, 0, -11);
                    $STOP_SCRIPT = true;
                }
                else
                {
                    echo json_encode(array(
                        'type' => 'error',
                        'stop' => 1,
                        'content' => '<strong style="color: #831f1f;">'._l('You have to set the category selection for the script')._l(':').' '.substr($filename, 0, -11).'</span>',
                        'filename' => $auto_filename,
                        'first_interval' => $first_interval,
                    ));
                    exit();
                }
            }
            if ($exportConfig['mapping'] == '')
            {
                if (!$AUTO_EXPORT && !$CRON)
                {
                    exit(_l('You have to set the mapping for the script.')._l(':').' '.substr($filename, 0, -11));
                }
                elseif ($CRON)
                {
                    echo _l('You have to set the mapping for the script.')._l(':').' '.substr($filename, 0, -11);
                    $STOP_SCRIPT = true;
                }
                else
                {
                    echo json_encode(array(
                        'type' => 'error',
                        'stop' => 1,
                        'content' => '<strong style="color: #831f1f;">'._l('You have to set the mapping for the script.')._l(':').' '.substr($filename, 0, -11).'</span>',
                        'filename' => $auto_filename,
                        'first_interval' => $first_interval,
                    ));
                    exit();
                }
            }
            if ($exportConfig['mapping'] != '' && $feed = @simplexml_load_file(SC_TOOLS_DIR.'cat_export/'.$exportConfig['mapping']))
            {
                foreach ($feed->field as $mfield)
                {
                    if ((int) $mfield->used)
                    {
                        $mapping[] = array('name' => (string) $mfield->name,
                                                            'lang' => (string) $mfield->lang,
                                                            'options' => (string) $mfield->options,
                                                            'options_two' => (string) $mfield->options_two,
                                                            'filters' => (string) $mfield->filters,
                                                            'modifications' => (string) $mfield->modifications,
                                                            'column_name' => (string) $mfield->column_name, );
                        $fieldList[] = (string) $mfield->name;
                    }
                }
            }

            if ($exportConfig['exportfilename'] == '')
            {
                if (!$AUTO_EXPORT && !$CRON)
                {
                    exit(_l('You have to define a filename for the export.')._l(':').' '.substr($filename, 0, -11));
                }
                elseif ($CRON)
                {
                    echo _l('You have to define a filename for the export.')._l(':').' '.substr($filename, 0, -11);
                    $STOP_SCRIPT = true;
                }
                else
                {
                    echo json_encode(array(
                        'type' => 'error',
                        'stop' => 1,
                        'content' => '<strong style="color: #831f1f;">'._l('You have to define a filename for the export.', 1)._l(':', 1).' '.substr($filename, 0, -11).'</span>',
                        'filename' => $auto_filename,
                        'first_interval' => $first_interval,
                    ));
                    exit();
                }
            }
            if ($STOP_SCRIPT)
            {
                break;
            }

            if (!$exportConfig['exportoutofstock'] && SCAS)
            {
                if (!(sc_in_array('quantity', $fieldList, 'catWinExportProcess_fieldList') || sc_in_array('quantity_physical', $fieldList, 'catWinExportProcess_fieldList')))
                {
                    exit(_l('When the option \'Export out of stock products\' is disabled, your mapping must include \'Quantity\' or \'Physical quantity\' field.'));
                }
            }

            // CRON CHECK
            if ($CRON && $CRONVERSION >= 2)
            {
                if ($sc_export['exporting'] == 1)
                {
                    $ALREADY_EXPORTING = true;
                }
                else
                {
                    $sql = 'UPDATE '._DB_PREFIX_."sc_export SET exporting = 1 WHERE name = '".pSQL($auto_filename)."'";
                    Db::getInstance()->Execute($sql);
                }
            }

            // AUTO EXPORT & FIRST INTERVAL
            if (($AUTO_EXPORT || ($CRON && $CRONVERSION >= 2)) && $first_interval)
            {
                if ($sc_export['exporting'] == 0)
                {
                    $sql = 'UPDATE '._DB_PREFIX_."sc_export SET exporting = 1, id_next=0, id_combination_next=0, last_export='".date('Y-m-d H:i:s')."' WHERE name = '".pSQL($auto_filename)."'";
                    Db::getInstance()->Execute($sql);

                    // INITIALISATION
                    $sql = 'DELETE FROM '._DB_PREFIX_."sc_export_product WHERE id_sc_export = '".(int) $sc_export['id_sc_export']."'";
                    Db::getInstance()->Execute($sql);

                    file_put_contents(SC_CSV_EXPORT_DIR.$exportConfig['exportfilename'], '');

                    $sc_export['exporting'] = 0;
                    $sc_export['id_next'] = 0;
                    $sc_export['id_combination_next'] = 0;
                }
                else
                {
                    $ALREADY_EXPORTING = true;
                }
            }

            $filter_supplier_id = (int) $exportConfig['supplier'];

            // MAIN QUERIES
            if ($exportConfig['exportbydefaultcategory'])
            { // by default category
                $sql = ' SELECT p.id_product,c.id_category';
                $sql .= ' FROM '._DB_PREFIX_.'product p';
                if (SCMS && $selected_shops_id > 0)
                {
                    $sql .= '    INNER JOIN `'._DB_PREFIX_.'product_shop` ps ON (p.id_product = ps.id_product AND ps.id_shop = "'.(int) $selected_shops_id.'")';
                }
                elseif (!SCMS && version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                {
                    $sql .= '    INNER JOIN `'._DB_PREFIX_.'product_shop` ps ON (p.id_product = ps.id_product AND ps.id_shop = "'.SCI::getSelectedShop().'")';
                }
                $sql .= ' LEFT JOIN '._DB_PREFIX_.'category_product pdc ON (pdc.id_category='.((SCMS && $selected_shops_id > 0) || (!SCMS && version_compare(_PS_VERSION_, '1.5.0.0', '>=')) ? 'ps' : 'p').'.id_category_default AND pdc.id_product=p.id_product)';
                $sql .= ' LEFT JOIN '._DB_PREFIX_.'category c ON (c.id_category=pdc.id_category)';
                $sql .= ' WHERE 1';
                if (!empty($filter_supplier_id))
                {
                    $sql .= ' AND  (
                        p.id_supplier='.(int) $filter_supplier_id;
                    if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                    {
                        $sql .= ' OR '.(int) $filter_supplier_id.' IN (SELECT id_supplier FROM '._DB_PREFIX_.'product_supplier psupp WHERE psupp.id_product = p.id_product) ';
                    }
                    $sql .= ' ) ';
                }
                switch ($exportConfig['categoriessel']){
                    case 'all':
                        break;
                    case 'all_enabled':
                        $sql .= ' AND c.active=1'."\n";
                        break;
                    case 'all_disabled':
                        $sql .= ' AND c.active=0'."\n";
                        break;
                    default:
                        $filenameCatSel = $exportConfig['categoriessel'];
                        $categories = array();
                        if ($feed = simplexml_load_file(SC_TOOLS_DIR.'cat_categories_sel/'.$filenameCatSel))
                        {
                            foreach ($feed->category as $category)
                            {
                                $categories[] = (string) $category->id;
                            }
                            $sql .= ' AND pdc.id_category IN ('.join(',', $categories).')';
                        }
                        break;
                }
                if ((SCMS && $selected_shops_id > 0) || (!SCMS && version_compare(_PS_VERSION_, '1.5.0.0', '>=')))
                {
                    $sql .= ((int) ($exportConfig['exportdisabledproducts']) ? '' : ' AND ps.active=1');
                }
                else
                {
                    $sql .= ((int) ($exportConfig['exportdisabledproducts']) ? '' : ' AND p.active=1');
                }
                if ($AUTO_EXPORT || ($CRON && $CRONVERSION >= 2))
                {
                    $tmpSql = 'SELECT id_product FROM '._DB_PREFIX_.'sc_export_product WHERE id_sc_export = "'.(int) $sc_export['id_sc_export'].'" AND handled=1';
                    $alreadyExported_ID = Db::getInstance()->ExecuteS($tmpSql);
                    $tmpIDS = array();
                    foreach ($alreadyExported_ID as $ids_product)
                    {
                        $tmpIDS[] = (int) $ids_product['id_product'];
                    }

                    if (count($tmpIDS) > 0)
                    {
                        $sql .= ' AND  (
                                    p.id_product NOT IN ('.join(',', $tmpIDS).') ';
                        if (!empty($sc_export['id_next']) && $exportConfig['exportcombinations'])
                        {
                            $sql .= ' OR (p.id_product = "'.$sc_export['id_next'].'") ';
                        }
                        $sql .= ' ) ';
                    }
                }
                if ($CRON && isset($CRON_NEWERTHAN) && $CRON_NEWERTHAN > 0)
                {
                    $sql .= 'AND (p.date_upd >= (SELECT DATE_ADD("'.date('Y-m-d H:i:s').'", INTERVAL -'.(int) $CRON_NEWERTHAN.' SECOND)))';
                }
                $sql .= ' GROUP BY p.id_product,c.id_category';
                if ($AUTO_EXPORT || ($CRON && $CRONVERSION >= 2))
                {
                    if (!empty($sc_export['id_next']) && $exportConfig['exportcombinations'])
                    {
                        $sql .= ' ORDER BY p.id_product ASC
                                LIMIT '.(int) ($export_limit + 1);
                    }
                    else
                    {
                        $sql .= ' ORDER BY p.id_product ASC
                                LIMIT '.(int) ($export_limit);
                    }
                }
                else
                {
                    $sql .= ' ORDER BY p.id_category_default, pdc.position';
                }
            }
            else
            { // for all categories
                $sql = ' SELECT DISTINCT(cp.id_product),cp.id_category';
                $sql .= ' FROM '._DB_PREFIX_.'category_product cp';
                $sql .= ' LEFT JOIN '._DB_PREFIX_.'category c ON (c.id_category=cp.id_category)';
                $sql .= ' LEFT JOIN '._DB_PREFIX_.'product p ON (p.id_product=cp.id_product)';
                if (SCMS && $selected_shops_id > 0)
                {
                    $sql .= '    INNER JOIN `'._DB_PREFIX_.'product_shop` ps ON (p.id_product = ps.id_product AND ps.id_shop = "'.(int) $selected_shops_id.'")';
                }
                elseif (!SCMS && version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                {
                    $sql .= '    INNER JOIN `'._DB_PREFIX_.'product_shop` ps ON (p.id_product = ps.id_product AND ps.id_shop = "'.SCI::getSelectedShop().'")';
                }
                $sql .= ' WHERE 1';
                if (!empty($filter_supplier_id))
                {
                    $sql .= ' AND  (
                        p.id_supplier='.(int) $filter_supplier_id;
                    if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                    {
                        $sql .= ' OR '.(int) $filter_supplier_id.' IN (SELECT id_supplier FROM '._DB_PREFIX_.'product_supplier psupp WHERE psupp.id_product = p.id_product) ';
                    }
                    $sql .= ' ) ';
                }
                switch ($exportConfig['categoriessel']){
                    case 'all':
                        break;
                    case 'all_enabled':
                        $sql .= ' AND c.active=1';
                        break;
                    case 'all_disabled':
                        $sql .= ' AND c.active=0';
                        break;
                    default:
                        $filenameCatSel = $exportConfig['categoriessel'];
                        $categories = array();
                        if ($feed = simplexml_load_file(SC_TOOLS_DIR.'cat_categories_sel/'.$filenameCatSel))
                        {
                            foreach ($feed->category as $category)
                            {
                                $categories[] = (string) $category->id;
                            }
                            $sql .= ' AND cp.id_category IN ('.join(',', $categories).')';
                        }
                        break;
                }
                if ((SCMS && $selected_shops_id > 0) || (!SCMS && version_compare(_PS_VERSION_, '1.5.0.0', '>=')))
                {
                    $sql .= ((int) ($exportConfig['exportdisabledproducts']) ? '' : ' AND ps.active=1');
                }
                else
                {
                    $sql .= ((int) ($exportConfig['exportdisabledproducts']) ? '' : ' AND p.active=1');
                }
                if ($AUTO_EXPORT || ($CRON && $CRONVERSION >= 2))
                {
                    $sql .= ' AND  (
                                cp.id_product NOT IN (SELECT id_product FROM '._DB_PREFIX_.'sc_export_product WHERE id_sc_export = "'.(int) $sc_export['id_sc_export'].'") ';
                    if (!empty($sc_export['id_next']) && $exportConfig['exportcombinations'])
                    {
                        $sql .= ' OR (cp.id_product = "'.$sc_export['id_next'].'") ';
                    }
                    $sql .= ' ) ';
                }
                if ($CRON && isset($CRON_NEWERTHAN) && $CRON_NEWERTHAN > 0)
                {
                    $sql .= 'AND (p.date_upd >= (SELECT DATE_ADD("'.date('Y-m-d H:i:s').'", INTERVAL -'.(int) $CRON_NEWERTHAN.' SECOND)))';
                }
                $sql .= ' GROUP BY cp.id_product';
                if ($AUTO_EXPORT || ($CRON && $CRONVERSION >= 2))
                {
                    if (!empty($sc_export['id_next']) && $exportConfig['exportcombinations'])
                    {
                        $sql .= ' ORDER BY cp.id_product ASC
                                LIMIT '.(int) ($export_limit + 1);
                    }
                    else
                    {
                        $sql .= ' ORDER BY cp.id_product ASC
                                LIMIT '.(int) $export_limit;
                    }
                }
                else
                {
                    $sql .= ' ORDER BY cp.id_category, cp.position';
                }
            }
            $products = Db::getInstance()->ExecuteS($sql);
            if (!is_array($products))
            {
                if (!$CRON)
                {
                    echo json_encode(array(
                            'type' => 'error',
                            'stop' => 1,
                            'content' => '<strong style="color: #831f1f;">MySQL error: '.Db::getInstance()->getMsgError().'</span>',
                            'filename' => $auto_filename,
                            'debug' => $debug,
                            'first_interval' => $first_interval,
                    ));
                    exit;
                }
            }

            ## création liste ids product array && string
            $product_ids_list_array = array();
            foreach ($products as $prd)
            {
                $product_ids_list_array[] = (int) $prd['id_product'];
            }
            $product_ids_list_string = (string) implode(',', $product_ids_list_array);

            $linecount = 0;
            $linecountreal = 0;

            // ALREADY EXPORTING
            if (($AUTO_EXPORT || ($CRON && $CRONVERSION >= 2)) && $ALREADY_EXPORTING && !$exportConfig['exportcombinations'])
            {
                if (!empty($products[0]['id_product']))
                {
                    $sql = 'UPDATE '._DB_PREFIX_."sc_export SET exporting = 0 WHERE name = '".pSQL($auto_filename)."'";
                    Db::getInstance()->Execute($sql);

                    if ($AUTO_EXPORT)
                    {
                        echo json_encode(array(
                                    'type' => 'error',
                                    'stop' => 1,
                                    'content' => '<strong style="color: #831f1f;">'._l('This export is already in progress.', 1).' - <a href="javascript: void(0);" class="reset_export" id="export_'.(int) $sc_export['id_sc_export'].'">'._l('Reset', 1).'</a></span>',
                                    'filename' => $auto_filename,
                                    'first_interval' => $first_interval,
                            ));
                        exit();
                    }
                    else
                    {
                        $STOP_SCRIPT = true;
                        echo _l('This export is already in progress.');
                    }
                }
                else
                {
                    $sql = 'UPDATE '._DB_PREFIX_."sc_export SET exporting = 0 WHERE name = '".pSQL($auto_filename)."'";
                    Db::getInstance()->Execute($sql);

                    if ($AUTO_EXPORT)
                    {
                        echo json_encode(array(
                         'type' => 'error',
                                'stop' => 1,
                                'content' => '<strong style="color: #831f1f;">'._l('An error occured during export. Please try again.', 1).'</span>',
                                'filename' => $auto_filename,
                                'first_interval' => $first_interval,
                        ));
                        exit();
                    }
                    else
                    {
                        $STOP_SCRIPT = true;
                        echo _l('An error occured during export. Please try again.');
                    }
                }
            }
            if ($STOP_SCRIPT)
            {
                break;
            }

            // WRITE FILE
            if (($AUTO_EXPORT || ($CRON && $CRONVERSION >= 2)) && $ALREADY_EXPORTING)
            {
                $fp = null;
            }
            elseif (($AUTO_EXPORT || ($CRON && $CRONVERSION >= 2)) && !$ALREADY_EXPORTING)
            {
                $fp = fopen(SC_CSV_EXPORT_DIR.$exportConfig['exportfilename'], 'a');
            }
            else
            {
                $fp = fopen(SC_CSV_EXPORT_DIR.$exportConfig['exportfilename'], 'w');
            }

            ## utf-8 bom
            if (!(int) $exportConfig['iso'])
            {
                fwrite($fp, "\xEF\xBB\xBF");
            }
            // First line
            if ((!$AUTO_EXPORT && !($CRON && $CRONVERSION >= 2)) || (($AUTO_EXPORT || ($CRON && $CRONVERSION >= 2)) && $first_interval && !$ALREADY_EXPORTING))
            {
                if ($exportConfig['firstlinecontent'] != '')
                {
                    if (trim($exportConfig['firstlinecontent']) != '-')
                    {
                        fwrite($fp, ((int) $exportConfig['iso'] ? utf8_decode($exportConfig['firstlinecontent']) : $exportConfig['firstlinecontent'])."\n");
                    }
                }
                else
                {
                    $fname = getExportCSVFields();
                    $fname = array_flip($fname);
                    $names = array();
                    $image_link_num = 1;
                    $image_id_num = 1;
                    $image_legend_num = 1;
                    $option_two_label = array(
                        'product_value' => _l('Product value'),
                        'combination_value' => _l('Combination value'),
                        'prod_value_if_combi_empty' => _l('Product value if combination value is empty'),
                    );
                    foreach ($fieldList as $k => $mfield)
                    {
                        if (!empty($mapping[$k]['column_name']))
                        {
                            $names[$k] = stripslashes($mapping[$k]['column_name']);
                        }
                        else
                        {
                            if (SCAS && ($mfield == 'quantity' || $mfield == 'location' || $mfield == 'quantity_physical' || $mfield == 'quantity_usable' || $mfield == 'quantity_real'))
                            {
                                $temp_name = $fname[$mfield];
                                if (empty($mapping[$k]['options']) || $mapping[$k]['options'] == 'warehouse_none')
                                {
                                    $temp_name .= ' '._l('No warehouse');
                                }
                                else
                                {
                                    $temp = (int) str_replace('warehouse_', '', $mapping[$k]['options']);
                                    if (!empty($temp))
                                    {
                                        $warehouse = new Warehouse((int) $temp, (int) $id_lang);
                                        if (!empty($warehouse->name))
                                        {
                                            $temp_name .= ' '._l('Warehouse').' '.$warehouse->reference.' - '.$warehouse->name;
                                        }
                                    }
                                }
                                $names[$k] = stripslashes($temp_name);
                            }
                            elseif ($mfield == 'supplier_reference' || $mfield == 'wholesale_price')
                            {
                                $temp_name = $fname[$mfield];
                                if (empty($mapping[$k]['options']) || $mapping[$k]['options'] == 'supplier_none')
                                {
                                    $temp_name .= ' '._l('Default values display products/combinations grids');
                                }
                                else
                                {
                                    $temp = ($mapping[$k]['options']);
                                    if (!empty($temp))
                                    {
                                        $temp_name .= ' '._l('Supplier').' '.$temp;
                                    }
                                }
                                $names[$k] = stripslashes($temp_name);
                            }
                            elseif ($mfield == 'links_to_all_images')
                            {
                                if (!in_array(stripslashes($fname[$mfield]), $names))
                                {
                                    $names[$k] = stripslashes($fname[$mfield]);
                                }
                            }
                            elseif ($mfield == 'image_link' || $mfield == 'image_id' || $mfield == 'image_legend')
                            {
                                $num = 1;
                                if ($mfield == 'image_link')
                                {
                                    $num = $image_link_num;
                                    ++$image_link_num;
                                }
                                if ($mfield == 'image_id')
                                {
                                    $num = $image_id_num;
                                    ++$image_id_num;
                                }
                                if ($mfield == 'image_legend')
                                {
                                    $num = $image_legend_num;
                                    ++$image_legend_num;
                                }
                                $names[$k] = stripslashes($fname[$mfield].($mapping[$k]['options'] != '' && $mapping[$k]['options'] != 'supplier_none' ? ' '.$mapping[$k]['options'] : '').($mapping[$k]['lang'] != '' ? ' '.strtoupper($mapping[$k]['lang']) : '').' '.$num);
                            }
                            else
                            {
                                $names[$k] = stripslashes($fname[$mfield].($mapping[$k]['options'] != '' && $mapping[$k]['options'] != 'supplier_none' ? ' '.$mapping[$k]['options'] : '').($mapping[$k]['lang'] != '' ? ' '.strtoupper($mapping[$k]['lang']) : ''));
                            }
                        }

                        if (empty($mapping[$k]['column_name']) && !empty($mapping[$k]['options_two']) && array_key_exists($mapping[$k]['options_two'], $option_two_label))
                        {
                            $names[$k] .= ' '.stripslashes($option_two_label[$mapping[$k]['options_two']]);
                        }
                    }
                    fwrite($fp, ((int) $exportConfig['iso'] ? utf8_decode(join($exportConfig['fieldsep'], $names)) : join($exportConfig['fieldsep'], $names))."\n");
                }
            }

            // supplier
            if (sc_in_array('supplier_reference', $fieldList, 'catWinExportProcess_fieldList') || sc_in_array('wholesale_price', $fieldList, 'catWinExportProcess_fieldList'))
            {
                $suppliersListByLang = array();

                // DEFAULT LANG
                $sql = 'SELECT s.name,s.`id_supplier`
                            FROM `'._DB_PREFIX_.'supplier` s
                            '.((SCMS && $selected_shops_id > 0) ? ' INNER JOIN `'._DB_PREFIX_.'supplier_shop` ss ON (s.`id_supplier` = ss.`id_supplier` AND ss.id_shop = "'.(int) $selected_shops_id.'") ' : '');
                $suppliers = Db::getInstance()->ExecuteS($sql);
                foreach ($suppliers as $supplier)
                {
                    $suppliersListByLang[$supplier['name']] = $supplier['id_supplier'];
                }
            }

            ## Attachments cache
            if (!empty($product_ids_list_string) && (sc_in_array('attachments', $fieldList, 'catWinExportProcess_fieldList')
                || sc_in_array('attachments_url', $fieldList, 'catWinExportProcess_fieldList')))
            {
                $attachment_res = Db::getInstance()->executeS('SELECT pa.id_product, a.*
                                                                    FROM '._DB_PREFIX_.'product_attachment pa
                                                                    LEFT JOIN '._DB_PREFIX_.'attachment a ON (a.id_attachment=pa.id_attachment)
                                                                    WHERE pa.id_product IN ('.pInSQL($product_ids_list_string).')');
                $attachments_array = array();
                if (!empty($attachment_res))
                {
                    foreach ($attachment_res as $res)
                    {
                        $attachments_array[$res['id_product']][$res['id_attachment']] = $res;
                    }
                }
            }

            ## Out_of_stock cache
            $oos_array = array();
            if (!empty($product_ids_list_string) && version_compare(_PS_VERSION_, '1.5.0.2', '>=') && sc_in_array('out_of_stock', $fieldList, 'catWinExportProcess_fieldList'))
            {
                $oos_res = Db::getInstance()->executeS('SELECT id_shop,id_product,id_product_attribute,out_of_stock
                                                               FROM '._DB_PREFIX_.'stock_available
                                                               WHERE id_shop = '.(int) $selected_shops_id.'
                                                               AND id_product IN ('.pInSQL($product_ids_list_string).')');
                if (!empty($oos_res))
                {
                    foreach ($oos_res as $res)
                    {
                        $oos_array[$res['id_product']][$res['id_product_attribute']] = (int) $res['out_of_stock'];
                    }
                }
            }

            ## Pack cache
            if (!empty($product_ids_list_string) && sc_in_array('product_pack', $fieldList, 'catWinExportProcess_fieldList'))
            {
                $pack_res = Db::getInstance()->executeS("SELECT id_product_pack, IF(id_product_attribute_item>0,CONCAT(id_product_item,'_',id_product_attribute_item,'#',quantity),CONCAT(id_product_item,'#',quantity)) AS pack_row
                                                            FROM "._DB_PREFIX_.'pack
                                                            WHERE id_product_pack IN ('.pInSQL($product_ids_list_string).')');
                $pack_array = array();
                if (!empty($pack_res))
                {
                    foreach ($pack_res as $res)
                    {
                        $pack_array[$res['id_product_pack']][] = (string) $res['pack_row'];
                    }
                }
            }

            // PRODUCTS TREATMENT
            $multipleFeatureEnabled = false;
            if (version_compare(_PS_VERSION_, '1.7.3.0', '>=') || (
                    (version_compare(_PS_VERSION_, '1.5.0.0', '<') || Feature::isFeatureActive())
                    && (defined('SC_MultiplesFeatures_ACTIVE') && SC_MultiplesFeatures_ACTIVE == '1')
                    && (SCI::moduleIsInstalled('pm_multiplefeatures')))
            ) {
                if (SCI::moduleIsInstalled('pm_multiplefeatures') && $separator = Configuration::get('PM_MF_CONF'))
                {
                    $featureSseparator = json_decode($separator, true);
                    $featureSseparator = $featureSseparator['featureSeparator'];
                }
                else
                {
                    $featureSseparator = $exportConfig['valuesep'];
                }
                $multipleFeatureEnabled = true;
            }

            $fullProduct = count($fieldList) != count(array_diff($fieldList, array('supplier', 'manufacturer', 'vat', 'link_to_product', 'link_to_combination', 'unity', 'tags')));

            foreach ($products as $idp)
            {
                $id_product_attribute = 0;
                $sql = 'UPDATE '._DB_PREFIX_."sc_export SET id_next = '".(int) $idp['id_product']."' WHERE name = '".pSQL($auto_filename)."'";
                Db::getInstance()->Execute($sql);

                // array_diff checks if we need to load product details (more queries to handle)
                if (SCMS && $selected_shops_id > 0)
                {
                    $p = new Product($idp['id_product'], $fullProduct, null, (int) $selected_shops_id);
                }
                else
                {
                    $p = new Product($idp['id_product'], $fullProduct);
                }

                $id_product = $idp['id_product'];

                // features
                if (sc_in_array('feature', $fieldList, 'catWinExportProcess_fieldList')
                    || sc_in_array('id_feature_value', $fieldList, 'catWinExportProcess_fieldList'))
                {
                    $featuresListByLang = array();

                    // DEFAULT LANG
                    $featuresListNameDefault = array();
                    $sql = 'SELECT fl.name,fp.`id_feature`
                            FROM `'._DB_PREFIX_.'feature_product` fp
                            LEFT JOIN `'._DB_PREFIX_.'feature_lang` fl ON fp.`id_feature` = fl.`id_feature`
                            '.((SCMS && $selected_shops_id > 0) ? ' INNER JOIN `'._DB_PREFIX_.'feature_shop` fs ON (fp.`id_feature` = fs.`id_feature` AND fs.id_shop = "'.(int) $selected_shops_id.'") ' : '').'
                            WHERE fp.`id_product` = '.(int) $idp['id_product'].'
                            AND fl.`id_lang` = '.(int) $sc_agent->id_lang;
                    $features = Db::getInstance()->ExecuteS($sql);
                    foreach ($features as $feature)
                    {
                        $featuresListNameDefault[$feature['id_feature']] = $feature['name'];
                    }

                    // OTHER LANGS
                    $langs = Language::getLanguages(false);
                    foreach ($langs as $lang)
                    {
                        $sql = 'SELECT fl.name,fvl.value,fp.`id_feature`,fp.`id_feature_value`
                                    FROM `'._DB_PREFIX_.'feature_product` fp
                                    LEFT JOIN `'._DB_PREFIX_.'feature_lang` fl ON fp.`id_feature` = fl.`id_feature`
                                    LEFT JOIN `'._DB_PREFIX_.'feature_value_lang` fvl ON fp.`id_feature_value` = fvl.`id_feature_value`
                                    '.((SCMS && $selected_shops_id > 0) ? ' INNER JOIN `'._DB_PREFIX_.'feature_shop` fs ON (fp.`id_feature` = fs.`id_feature` AND fs.id_shop = "'.(int) $selected_shops_id.'") ' : '').'
                                    WHERE fp.`id_product` = '.(int) $idp['id_product'].'
                                    AND fl.`id_lang` = '.(int) $lang['id_lang'].'
                                    AND fvl.`id_lang` = '.(int) $lang['id_lang'];
                        $features = Db::getInstance()->ExecuteS($sql);
                        $featuresListByLang[$lang['id_lang']] = array();
                        foreach ($features as $feature)
                        {
                            $featuresListByLang[$lang['id_lang']][$featuresListNameDefault[$feature['id_feature']]]['id_feature_value'] = $feature['id_feature_value'];
                            if ($multipleFeatureEnabled && !empty($featuresListByLang[$lang['id_lang']][$featuresListNameDefault[$feature['id_feature']]]))
                            {
                                if (!empty($featuresListByLang[$lang['id_lang']][$featuresListNameDefault[$feature['id_feature']]]['value']))
                                {
                                    $featuresListByLang[$lang['id_lang']][$featuresListNameDefault[$feature['id_feature']]]['value'] .= $featureSseparator.$feature['value'];
                                }
                                else
                                {
                                    $featuresListByLang[$lang['id_lang']][$featuresListNameDefault[$feature['id_feature']]]['value'] = $feature['value'];
                                }
                            }
                            else
                            {
                                $featuresListByLang[$lang['id_lang']][$featuresListNameDefault[$feature['id_feature']]]['value'] = $feature['value'];
                            }
                        }
                    }
                }

                // combinations
                if (
                    (!$AUTO_EXPORT && !($CRON && $CRONVERSION >= 2))
                    || (($AUTO_EXPORT || ($CRON && $CRONVERSION >= 2)) && $exportConfig['exportcombinations'] && $idp['id_product'] != $sc_export['id_next'])
                    || (($AUTO_EXPORT || ($CRON && $CRONVERSION >= 2)) && !$exportConfig['exportcombinations'])
                ) {
                    $combArray = array(0 => array());
                } // 0: initialize array for products without combinations
                else
                {
                    $combArray = array();
                }

                if ($exportConfig['exportcombinations'])
                {
                    // IN DEFAULT LANG
                    $attributesByDefaultLang = array();
                    $sql = 'SELECT agl.`name` AS group_name, ag.`id_attribute_group`
                                FROM `'._DB_PREFIX_.'attribute_group` ag
                                LEFT JOIN `'._DB_PREFIX_.'attribute_group_lang` agl ON (ag.`id_attribute_group` = agl.`id_attribute_group`)
                                WHERE agl.`id_lang` = '.(int) $sc_agent->id_lang.'
                                ORDER BY ag.`id_attribute_group`';
                    $temp_attributes = Db::getInstance()->ExecuteS($sql);
                    foreach ($temp_attributes as $temp_attribute)
                    {
                        $attributesByDefaultLang[$temp_attribute['id_attribute_group']] = $temp_attribute['group_name'];
                    }

                    /* Build attributes combinaisons */
                    $standardFields = array('price', 'wholesale_price', 'weight', 'reference', 'upc', 'unit_price_impact', 'supplier_reference', 'ean13', 'default_on', 'ecotax');
                    if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                    {
                        $standardFields[] = 'available_date';
                    }
                    if (version_compare(_PS_VERSION_, '1.7.0.0', '>='))
                    {
                        $standardFields[] = 'isbn';
                    }
                    if (version_compare(_PS_VERSION_, '1.7.3.0', '>='))
                    {
                        $standardFields[] = 'low_stock_alert';
                        $standardFields[] = 'low_stock_threshold';
                    }
                    if (version_compare(_PS_VERSION_, '1.7.7.0', '>='))
                    {
                        $standardFields[] = 'mpn';
                    }
                    if (!SCAS)
                    {
                        $standardFields[] = 'location';
                    }
                    sc_ext::readExportCSVConfigXML('addInCombiFields');
                    $sql = 'SELECT pa.*,ag.`id_attribute_group`, ag.`is_color_group`,'.(version_compare(_PS_VERSION_, '1.5.0.1', '>=') ? 'ag.`position` AS group_position,' : '').' agl.`name` AS group_name, al.`name` AS attribute_name, a.`id_attribute`, a.`color`,al.id_lang '.((version_compare(_PS_VERSION_, '1.5.0.0', '>=') && $selected_shops_id > 0) ? ',pas.*' : '').'
                                FROM `'._DB_PREFIX_.'product_attribute` pa
                                LEFT JOIN `'._DB_PREFIX_.'product_attribute_combination` pac ON pac.`id_product_attribute` = pa.`id_product_attribute`
                                LEFT JOIN `'._DB_PREFIX_.'attribute` a ON a.`id_attribute` = pac.`id_attribute`
                                LEFT JOIN `'._DB_PREFIX_.'attribute_group` ag ON ag.`id_attribute_group` = a.`id_attribute_group`
                                LEFT JOIN `'._DB_PREFIX_.'attribute_lang` al ON (a.`id_attribute` = al.`id_attribute`)
                                LEFT JOIN `'._DB_PREFIX_.'attribute_group_lang` agl ON (ag.`id_attribute_group` = agl.`id_attribute_group` AND al.`id_lang` = agl.`id_lang`)
                                '.((version_compare(_PS_VERSION_, '1.5.0.0', '>=') && $selected_shops_id > 0) ? ' INNER JOIN `'._DB_PREFIX_.'product_attribute_shop` pas ON (pa.`id_product_attribute` = pas.`id_product_attribute` AND pas.id_shop="'.(int) $selected_shops_id.'")' : '').'
                                WHERE pa.`id_product` = '.(int) $p->id.' ';
                    if ($AUTO_EXPORT || ($CRON && $CRONVERSION >= 2))
                    {
                        $sql .= ' AND  (
                                    pa.id_product_attribute NOT IN (SELECT id_product_attribute FROM '._DB_PREFIX_.'sc_export_product WHERE id_sc_export = "'.(int) $sc_export['id_sc_export'].'" AND id_product='.(int) $p->id.') ';
                        $sql .= ' ) ';
                    }
                    $sql .= ' ORDER BY pa.`id_product_attribute`';

                    $combinaisons = Db::getInstance()->ExecuteS($sql);
                    $groups = array();
                    if (is_array($combinaisons))
                    {
                        $combinationImages = getCombinationImages((int) $p->id);
                        foreach ($combinaisons as $k => $combinaison)
                        {
                            $id_product_attribute = $combinaison['id_product_attribute'];
                            if (!$exportConfig['exportoutofstock'])
                            {
                                if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                                {
                                    $qty = StockAvailable::getQuantityAvailableByProduct($p->id, ($id_product_attribute == 0 ? null : $id_product_attribute), (version_compare(_PS_VERSION_, '1.5.0.0', '>=') && $selected_shops_id > 0 ? (int) $selected_shops_id : null));
                                }
                                else
                                {
                                    $qty = (int) $combinaison['quantity'];
                                }
                                if ($qty <= 0)
                                {
                                    continue;
                                }
                            }
                            $combArray[$combinaison['id_product_attribute']]['price_impact'] = $combinaison['price'];
                            if (version_compare(_PS_VERSION_, '8.0.0', '>='))
                            {
                                $combArray[$combinaison['id_product_attribute']]['pa_location'] = StockAvailable::getLocation($p->id, ($id_product_attribute == 0 ? null : $id_product_attribute), $selected_shops_id);
                            }
                            else
                            {
                                $combArray[$combinaison['id_product_attribute']]['pa_location'] = $combinaison['location'];
                            }
                            $combArray[$combinaison['id_product_attribute']]['weight_impact'] = $combinaison['weight'];
                            if (isset($combinaison['minimal_quantity']))
                            {
                                $combArray[$combinaison['id_product_attribute']]['minimal_quantity'] = $combinaison['minimal_quantity'];
                            }
                            if (!empty($sc_active))
                            {
                                $combArray[$combinaison['id_product_attribute']]['sc_active'] = $combinaison['sc_active'];
                            }
                            if (SCI::getConfigurationValue('SC_DELIVERYDATE_INSTALLED') == '1')
                            {
                                $combArray[$combinaison['id_product_attribute']]['id_sc_available_later'] = $combinaison['id_sc_available_later'];
                            }
                            $combArray[$combinaison['id_product_attribute']]['id_image'] = sc_array_key_exists($combinaison['id_product_attribute'], $combinationImages) ? $combinationImages[$combinaison['id_product_attribute']] : 0;
                            $combArray[$combinaison['id_product_attribute']]['attributes'][$combinaison['id_lang']][] = array($combinaison['group_name'], $combinaison['attribute_name'], $combinaison['id_attribute'], $combinaison['id_attribute_group'], $combinaison['is_color_group'], $combinaison['color'], (version_compare(_PS_VERSION_, '1.5.0.1', '>=') ? $combinaison['group_position'] : 0));
                            foreach ($standardFields as $sfield)
                            {
                                if (sc_array_key_exists($sfield, $combinaison))
                                {
                                    $combArray[$combinaison['id_product_attribute']][$sfield] = $combinaison[$sfield];
                                }
                            }
                            if ($combinaison['is_color_group'])
                            {
                                $groups[$combinaison['id_attribute_group']] = $combinaison['group_name'];
                            }
                        }
                        if (count($combArray) > 1)
                        { // unset products without combinations to skip next foreach
                            unset($combArray[0]);
                        }
                        foreach ($combArray as $id_product_attribute => $attributes)
                        {
                            if ($id_product_attribute != 0)
                            {
                                foreach ($attributes['attributes'] as $id_lang => $values)
                                {
                                    foreach ($values as $v)
                                    {
                                        if (!isset($combArray[$id_product_attribute]['name_with_attributes'][$id_lang]))
                                        {
                                            $combArray[$id_product_attribute]['name_with_attributes'][$id_lang] = $p->name[(int) $id_lang].' ';
                                        }
                                        $combArray[$id_product_attribute]['name_with_attributes'][$id_lang] .= $v[0].':'.$v[1].' - ';
                                        if (!isset($combArray[$id_product_attribute]['attributeByGroup'][$id_lang]))
                                        {
                                            $combArray[$id_product_attribute]['attributeByGroup'][$id_lang] = array();
                                        }
                                        $combArray[$id_product_attribute]['attributeByGroup'][$id_lang][$attributesByDefaultLang[$v[3]]] = $v[1];
                                        if (!empty($v[4]))
                                        {
                                            if (!empty($v[5]) && !isset($combArray[$id_product_attribute]['attribute_color']))
                                            {
                                                $combArray[$id_product_attribute]['attribute_color'] = '';
                                            }
                                            if (!empty($v[5]))
                                            {
                                                if (!empty($combArray[$id_product_attribute]['attribute_color']))
                                                {
                                                    if (strpos($combArray[$id_product_attribute]['attribute_color'], $v[5]) === false)
                                                    {
                                                        $combArray[$id_product_attribute]['attribute_color'] .= $exportConfig['valuesep'].$v[5];
                                                    }
                                                }
                                                else
                                                {
                                                    $combArray[$id_product_attribute]['attribute_color'] .= $v[5];
                                                }
                                            }

                                            $combArray[$id_product_attribute]['attribute_texture'] = '';
                                            if (!empty($v[2]))
                                            {
                                                $ext = checkAndGetImgExtension(_PS_COL_IMG_DIR_.$v[2]);
                                                if (!empty($ext))
                                                {
                                                    $attribute_texture = $_PS_BASE_URL_.'../co/'.$v[2].'.'.$ext;
                                                    if (!empty($combArray[$id_product_attribute]['attribute_texture']))
                                                    {
                                                        if (strpos($combArray[$id_product_attribute]['attribute_texture'], $attribute_texture) === false)
                                                        {
                                                            $combArray[$id_product_attribute]['attribute_texture'] .= ','.$attribute_texture;
                                                        }
                                                    }
                                                    else
                                                    {
                                                        $combArray[$id_product_attribute]['attribute_texture'] .= $attribute_texture;
                                                    }
                                                }
                                            }
                                        }
                                        // $group[0] : group name
                                        // $group[1] : attribute name
                                        // $group[2] : attribute id
                                        // $group[3] : id_attribute_group
                                        // $group[4] : is_color_group
                                        // $group[4] : color
                                    }
                                }
                            }
                        }
                    }
                }

                // ALREADY EXPORTING
                if (($AUTO_EXPORT || ($CRON && $CRONVERSION >= 2)) && $ALREADY_EXPORTING && $exportConfig['exportcombinations'])
                {
                    $id_product_attribute_first = 0;
                    ksort($combArray);
                    foreach ($combArray as $id_product_attribute => $product_attribute)
                    {
                        if (!empty($id_product_attribute) && $idp['id_product'] == $sc_export['id_next'])
                        {
                            $id_product_attribute_first = $id_product_attribute;
                            break;
                        }
                    }

                    if (!empty($id_product_attribute_first))
                    {
                        if ($id_product_attribute_first == $sc_export['id_combination_next'])
                        {
                            $sql = 'UPDATE '._DB_PREFIX_."sc_export SET exporting = 0 WHERE name = '".pSQL($auto_filename)."'";
                            Db::getInstance()->Execute($sql);

                            if ($AUTO_EXPORT)
                            {
                                echo json_encode(array(
                                        'type' => 'error',
                                        'stop' => 1,
                                        'content' => '<strong style="color: #831f1f;">'._l('An error occured during last export with combination').' #'.$id_product_attribute_first.' '._l('from product').' #'.$idp['id_product'].'<br/>'._l('Check this combination before to try again.').'</span>',
                                        'filename' => $auto_filename,
                                        'first_interval' => $first_interval,
                                ));
                                exit();
                            }
                            else
                            {
                                $STOP_SCRIPT = true;
                                echo _l('An error occured during last export with combination').' #'.$id_product_attribute_first.' '._l('from product').' #'.$idp['id_product'].'<br/>'._l('Check this combination before to try again.');
                            }
                        }
                        else
                        {
                            if ($AUTO_EXPORT)
                            {
                                echo json_encode(array(
                                        'type' => 'error',
                                        'stop' => 1,
                                        'content' => '<strong style="color: #831f1f;">'._l('This export is already in progress.', 1).' - <a href="javascript: void(0);" class="reset_export" id="export_'.(int) $sc_export['id_sc_export'].'">'._l('Reset', 1).'</a></span>',
                                        'filename' => $auto_filename,
                                        'first_interval' => $first_interval,
                                ));
                                exit();
                            }
                            else
                            {
                                $STOP_SCRIPT = true;
                                echo _l('This export is already in progress.');
                            }
                        }
                    }
                    else
                    {
                        $sql = 'UPDATE '._DB_PREFIX_."sc_export SET exporting = 0 WHERE name = '".pSQL($auto_filename)."'";
                        Db::getInstance()->Execute($sql);

                        if ($AUTO_EXPORT)
                        {
                            echo json_encode(array(
                                'type' => 'error',
                                'stop' => 1,
                                'content' => '<strong style="color: #831f1f;">'._l('This export is already in progress.', 1).' - <a href="javascript: void(0);" class="reset_export" id="export_'.(int) $sc_export['id_sc_export'].'">'._l('Reset', 1).'</a></span>',
                                'filename' => $auto_filename,
                                'first_interval' => $first_interval,
                            ));
                            exit();
                        }
                        else
                        {
                            $STOP_SCRIPT = true;
                            echo _l('This export is already in progress.');
                        }
                    }
                }
                if ($STOP_SCRIPT)
                {
                    break;
                }

                $extension_vars = array();

                // COMBINATIONS TREATMENT
                foreach ($combArray as $id_product_attribute => $product_attribute)
                {
                    if (
                        !(
                            ($AUTO_EXPORT || ($CRON && $CRONVERSION >= 2))
                            && $exportConfig['exportcombinations']
                            && $p->id == $sc_export['id_next']
                            && !empty($id_product_attribute)
                            && $id_product_attribute <= $sc_export['id_combination_next']
                        )
                    ) {
                        if (!empty($id_product_attribute))
                        {
                            $sql = 'UPDATE '._DB_PREFIX_."sc_export SET id_combination_next = '".(int) $id_product_attribute."' WHERE name = '".pSQL($auto_filename)."'";
                            Db::getInstance()->Execute($sql);
                        }

                        ++$linecount;
                        if (!$exportConfig['exportoutofstock'] && !SCAS)
                        {
                            if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                            {
                                $qty = StockAvailable::getQuantityAvailableByProduct($p->id, ($id_product_attribute == 0 ? null : $id_product_attribute), (version_compare(_PS_VERSION_, '1.5.0.0', '>=') && $selected_shops_id > 0 ? (int) $selected_shops_id : null));
                            }
                            else
                            {
                                $qty = $p->getQuantity($p->id, ($id_product_attribute == 0 ? null : $id_product_attribute));
                            }
                            if ($qty <= 0)
                            {
                                if (!empty($sc_export['id_sc_export']))
                                {
                                    $sql = '
                                    INSERT INTO '._DB_PREFIX_."sc_export_product (id_sc_export, id_product, id_product_attribute,exported)
                                    VALUES ('".(int) $sc_export['id_sc_export']."','".(int) $idp['id_product']."','".(int) $id_product_attribute."','0')";
                                    Db::getInstance()->Execute($sql);
                                }
                                continue;
                            }
                        }
                        if (!empty($sc_export['id_sc_export']))
                        {
                            $sql = '
                                INSERT INTO '._DB_PREFIX_."sc_export_product (id_sc_export, id_product, id_product_attribute,exported)
                                VALUES ('".(int) $sc_export['id_sc_export']."','".(int) $idp['id_product']."','".(int) $id_product_attribute."','1')";
                            Db::getInstance()->Execute($sql);
                        }

                        $linecontent = '';
                        $cacheProductImages = array();
                        $cacheQueries = array();
                        $num_img_link = 1;
                        $num_img_url = 1;
                        $num_img_legend = 1;
                        $num_img_id = 1;
                        $legends = array();
                        if (!$exportConfig['exportoutofstock'] && SCAS)
                        {
                            $quantity_SCAS = 0;
                        }
                        sc_ext::readExportCSVConfigXML('exportProcessInitRowVars');

                        // foreach field in mapping
                        foreach ($mapping as $n => $f)
                        {
                            $field = '';
                            // extensions
                            $switchObject = $f['name'];
                            $switchObjectOption = $f['options'];
                            $switchObjectOptionTwo = $f['options_two'];
                            $switchObjectLang = $f['lang'];

                            sc_ext::readExportCSVConfigXML('exportProcessProduct');
                            if (sc_in_array($f['name'], $fields_lang, 'catWinExportProcess_fields_lang') && ($f['lang'] == '' || !sc_array_key_exists($f['lang'], $getIDlangByISO)))
                            {
                                exit(_l('You have to set the language in the mapping for the field:').' '._l($f['name']));
                            }
                            if ($f['name'] == 'id_product')
                            {
                                $f['name'] = 'id';
                            }
                            if ($f['name'] == 'supplier')
                            {
                                $f['name'] = 'supplier_name';
                            }
                            if ($f['name'] == 'manufacturer')
                            {
                                $f['name'] = 'manufacturer_name';
                            }
                            if ($f['name'] == 'vat')
                            {
                                $f['name'] = 'tax_rate';
                            }
                            // combinations
                            if ($field == '' && sc_array_key_exists($f['name'], $product_attribute) && $id_product_attribute != 0)
                            {
                                if (sc_in_array($f['name'], $fields_lang, 'catWinExportProcess_fields_lang'))
                                {
                                    $field = $product_attribute[$f['name']][$getIDlangByISO[$f['lang']]]; // lang
                                    if ($f['name'] == 'name_with_attributes')
                                    {
                                        $field = trim($field, ' - ');
                                    }
                                }
                                elseif (!sc_in_array($f['name'], $not_auto_combination_fields, 'catWinExportProcess_not_auto_combination_fields'))
                                {
                                    $field = $product_attribute[$f['name']];
                                    if ($f['name'] == 'price' && $exportConfig['shippingfee'] > 0 && $field <= $exportConfig['shippingfeefreefrom'])
                                    {
                                        $field += $exportConfig['shippingfee'];
                                    }
                                    if ($f['name'] == 'ecotax' && (float) $field == 0)
                                    {
                                        $field = $p->ecotax;
                                    }

                                    ## $field = '' here => go to Product loop
                                    switch ($f['name']) {
                                        case 'ean13':
                                        case 'mpn':
                                        case 'upc':
                                        case 'isbn':
                                        case 'reference':
                                            switch ($switchObjectOptionTwo){
                                                case 'product_value':
                                                    $field = '';
                                                    break;
                                                case 'prod_value_if_combi_empty':
                                                    if (empty($product_attribute[$f['name']]))
                                                    {
                                                        $field = '';
                                                    }
                                                    break;
                                                default:
                                                    $field = $product_attribute[$f['name']];
                                            }
                                            break;
                                        case 'weight':
                                            if ($switchObjectOptionTwo == 'product_value')
                                            {
                                                $field = '';
                                            }
                                            else
                                            {
                                                $field += $p->weight;
                                            }
                                            break;
                                        case 'minimal_quantity':
                                            if ($switchObjectOptionTwo == 'product_value')
                                            {
                                                $field = '';
                                            }
                                            break;
                                        case 'wholesale_price':
                                            if ($f['options'] == 'supplier_none')
                                            {
                                                $f['options'] = '';
                                            }
                                            if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                                            {
                                                if (!empty($f['options']) && !empty($suppliersListByLang[$f['options']]))
                                                {
                                                    $id_supplier = (int) $suppliersListByLang[$f['options']];
                                                    $field = ProductSupplier::getProductSupplierPrice((int) $p->id, ($switchObjectOptionTwo == 'product_value' ? 0 : (int) $id_product_attribute), $id_supplier);
                                                }
                                                elseif (empty($f['options']))
                                                {
                                                    if (!empty($id_product_attribute) && $switchObjectOptionTwo != 'product_value')
                                                    {
                                                        $field = $product_attribute['wholesale_price'];
                                                    }
                                                    else
                                                    {
                                                        $field = $p->wholesale_price;
                                                    }
                                                }
                                                else
                                                {
                                                    $field = '0';
                                                }
                                            }
                                            else
                                            {
                                                if (!empty($id_product_attribute) && $switchObjectOptionTwo != 'product_value')
                                                {
                                                    $field = $product_attribute['wholesale_price'];
                                                }
                                                else
                                                {
                                                    $field = $p->wholesale_price;
                                                }
                                            }
                                            break;
                                    }
                                }
                            }
                            if ($field == '')
                            {
                                if ($f['name'] == 'name_with_attributes')
                                {
                                    $f['name'] = 'name';
                                }
                                if (sc_in_array($f['name'], $fields_lang, 'catWinExportProcess_fields_lang'))
                                {
                                    $field = trim($p->{$f['name']}[$getIDlangByISO[$f['lang']]]); // lang
                                }
                                else
                                {
                                    $type_advanced_stock_management = 1; // Not Advanced Stock Management
                                    if (SCAS)
                                    {
                                        // Produit utilise la gestion avancée
                                        if ($p->advanced_stock_management == 1)
                                        {
                                            $type_advanced_stock_management = 2; // With Advanced Stock Management

                                            if (!StockAvailable::dependsOnStock((int) $p->id, (int) $selected_shops_id))
                                            {
                                                $type_advanced_stock_management = 3;
                                            }// With Advanced Stock Management + Manual management
                                        }
                                    }

                                    switch ($f['name']) {
                                        case 'category_default_full_path':
                                            createCategoryCache($getIDlangByISO[$f['lang']]);
                                            $field = getCategoryPath((int) $p->id_category_default, '', (int) $p->id_category_default, $getIDlangByISO[$f['lang']]);
                                            break;
                                        case 'category_full_path':
                                            createCategoryCache($getIDlangByISO[$f['lang']]);
                                            $field = '';
                                            $row = createQueriesCache('SELECT `id_category` FROM `'._DB_PREFIX_.'category_product`
                                                    WHERE `id_product` = '.(int) $p->id.'
                                                        AND id_category!=1 '.
                                                (SCMS && $selected_shops_id > 0 ? ' AND id_category IN (SELECT cs.id_category FROM `'._DB_PREFIX_.'category_shop` cs WHERE cs.id_shop = '.(int) $selected_shops_id.')' : ''));
                                            if ($row)
                                            {
                                                foreach ($row as $val)
                                                {
                                                    $field .= getCategoryPath($val['id_category'], '', $val['id_category'], $getIDlangByISO[$f['lang']]).$exportConfig['categorysep'];
                                                }
                                            }
                                            $field = trim($field, $exportConfig['categorysep'].' ');
                                            break;
                                        case 'id_category(s)':
                                            $field = Db::getInstance()->getValue('SELECT GROUP_CONCAT(`id_category` SEPARATOR "'.$exportConfig['valuesep'].'") FROM `'._DB_PREFIX_.'category_product` WHERE `id_product` = '.(int) $p->id);
                                            break;
                                        case 'id_category_default':
                                            $field = (int) $p->id_category_default;
                                            break;
                                        case 'available_date':
                                            $field = ($p->available_date);
                                            break;
                                        case 'additional_delivery_times':
                                            $field = (int) $p->additional_delivery_times;
                                            break;
                                        case 'delivery_in_stock':
                                            $field = ($p->delivery_in_stock[$getIDlangByISO[$f['lang']]]);
                                            break;
                                        case 'delivery_out_stock':
                                            $field = ($p->delivery_out_stock[$getIDlangByISO[$f['lang']]]);
                                            break;
                                        case 'low_stock_alert':
                                            $field = (int) $p->low_stock_alert;
                                            break;
                                        case 'low_stock_threshold':
                                            $field = (int) $p->low_stock_threshold;
                                            break;
                                        case 'id_manufacturer':
                                            $field = (int) $p->id_manufacturer;
                                            break;
                                        case 'accessories':
                                            $field = '';
                                            $rows = createQueriesCache('
                                                    SELECT DISTINCT pr.reference AS reference
                                                    FROM `'._DB_PREFIX_.'accessory` AS ac
                                                    INNER JOIN `'._DB_PREFIX_.'product` AS pr ON (pr.id_product = ac.id_product_2)
                                                    WHERE ac.id_product_1 = '.(int) $p->id.' ');
                                            if (!empty($rows))
                                            {
                                                foreach ($rows as $val)
                                                {
                                                    if (!empty($val['reference']))
                                                    {
                                                        $field .= $val['reference'].$exportConfig['valuesep'];
                                                    }
                                                }
                                            }
                                            $field = trim($field, $exportConfig['valuesep']);
                                            break;
                                        case 'advanced_stock_management':
                                            $field = (int) $type_advanced_stock_management;
                                            break;
                                        case 'id_supplier':
                                            $field = (int) $p->id_supplier;
                                            break;
                                        case 'visibility':
                                            if ($p->visibility == 'both')
                                            {
                                                $field = _l('Both');
                                            }
                                            elseif ($p->visibility == 'catalog')
                                            {
                                                $field = _l('Catalog');
                                            }
                                            elseif ($p->visibility == 'search')
                                            {
                                                $field = _l('Search');
                                            }
                                            elseif ($p->visibility == 'none')
                                            {
                                                $field = _l('None');
                                            }
                                            break;
                                        case 'supplier_reference':
                                            if ($f['options'] == 'supplier_none')
                                            {
                                                $f['options'] = '';
                                            }
                                            if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                                            {
                                                if (!empty($f['options']) && !empty($suppliersListByLang[$f['options']]))
                                                {
                                                    $id_supplier = (int) $suppliersListByLang[$f['options']];
                                                    $field = psql(ProductSupplier::getProductSupplierReference((int) $p->id, ($switchObjectOptionTwo == 'product_value' ? 0 : (int) $id_product_attribute), $id_supplier));
                                                }
                                                elseif (empty($f['options']))
                                                {
                                                    $field = psql(ProductSupplier::getProductSupplierReference((int) $p->id, ($switchObjectOptionTwo == 'product_value' ? 0 : (int) $id_product_attribute), (int) $p->id_supplier));

                                                    // cas particulier de dissociation fournisseur reassociation : entrée vide dans table product_supplier
                                                    if(trim($field, ' ') == ''){
                                                        if($switchObjectOptionTwo == 'product_value'){
                                                            $supplierReference = createQueriesCache('SELECT `supplier_reference` FROM `'._DB_PREFIX_.'product`
                                                    WHERE `id_product` = '.(int) $p->id);
                                                        } else {
                                                            $supplierReference = createQueriesCache('SELECT `supplier_reference` FROM `'._DB_PREFIX_.'product_attribute`
                                                    WHERE `id_product` = '.(int) $p->id.' AND `id_product_attribute`='.(int)$id_product_attribute);
                                                        }
                                                        if(!empty($supplierReference)){
                                                            $field = $supplierReference[0]['supplier_reference'];
                                                        }
                                                    }
                                                }
                                                else
                                                {
                                                    $field = '';
                                                }
                                            }
                                            else
                                            {
                                                $field = psql($p->supplier_reference);
                                            }
                                            break;
                                        case 'wholesale_price':
                                            if ($f['options'] == 'supplier_none')
                                            {
                                                $f['options'] = '';
                                            }
                                            if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                                            {
                                                if (!empty($f['options']) && !empty($suppliersListByLang[$f['options']]))
                                                {
                                                    $id_supplier = (int) $suppliersListByLang[$f['options']];
                                                    $field = (ProductSupplier::getProductSupplierPrice((int) $p->id, ($switchObjectOptionTwo == 'product_value' ? 0 : (int) $id_product_attribute), $id_supplier));
                                                }
                                                elseif (empty($f['options']))
                                                {
                                                    if (!empty($id_product_attribute) && $switchObjectOptionTwo != 'product_value')
                                                    {
                                                        $field = ($product_attribute['wholesale_price']);
                                                    }
                                                    else
                                                    {
                                                        $field = ($p->wholesale_price);
                                                    }
                                                }
                                                else
                                                {
                                                    $field = '0';
                                                }
                                            }
                                            else
                                            {
                                                if (!empty($id_product_attribute) && $switchObjectOptionTwo != 'product_value')
                                                {
                                                    $field = ($product_attribute['wholesale_price']);
                                                }
                                                else
                                                {
                                                    $field = ($p->wholesale_price);
                                                }
                                            }
                                            break;
                                        case 'category_default':
                                            createCategoryCache($getIDlangByISO[$f['lang']]);
                                            $field = $categoryNameByID[$getIDlangByISO[$f['lang']]][$p->id_category_default];
                                            break;
                                        case 'categories':
                                            createCategoryCache($getIDlangByISO[$f['lang']]);
                                            $field = '';
                                            if ($row = createQueriesCache('SELECT `id_category` FROM `'._DB_PREFIX_.'category_product`
                                                    WHERE `id_product` = '.(int) $p->id.'
                                                        AND id_category!=1 '.
                                                        (SCMS && $selected_shops_id > 0 ? ' AND id_category IN (SELECT cs.id_category FROM `'._DB_PREFIX_.'category_shop` cs WHERE cs.id_shop = '.(int) $selected_shops_id.')' : ''))
                                                    ) {
                                                foreach ($row as $val)
                                                {
                                                    $field .= $categoryNameByID[$getIDlangByISO[$f['lang']]][$val['id_category']].$exportConfig['valuesep'];
                                                }
                                            }
                                            $field = trim($field, $exportConfig['valuesep'].' ');
                                            break;
                                        case 'carriers':
                                            createCarriersCache();
                                            $field = '';
                                            if ($row = createQueriesCache('
                                                    SELECT `id_carrier_reference` FROM `'._DB_PREFIX_.'product_carrier`
                                                    WHERE `id_product` = '.(int) $p->id.'
                                                        '.(SCMS && $selected_shops_id > 0 ? ' AND id_shop = '.(int) $selected_shops_id.' ' : ''))
                                            ) {
                                                foreach ($row as $val)
                                                {
                                                    $field .= $cacheCarriers[$val['id_carrier_reference']].$exportConfig['valuesep'];
                                                }
                                            }
                                            $field = trim($field, $exportConfig['valuesep'].' ');
                                            break;
                                        case 'suppliers':
                                            createSuppliersCache();
                                            $field = '';
                                            $rows = Db::getInstance()->ExecuteS('
                                                    SELECT ps.`id_supplier` FROM `'._DB_PREFIX_.'product_supplier` ps
                                                        '.(SCMS && $selected_shops_id > 0 ? ' INNER JOIN `'._DB_PREFIX_.'supplier_shop` ss ON (ss.id_supplier=ps.id_supplier AND ss.id_shop = '.(int) $selected_shops_id.') ' : '').'
                                                    WHERE ps.`id_product` = '.(int) $p->id.'
                                                        '.((!empty($id_product_attribute)) ? ' AND ps.id_product_attribute="'.(int) $id_product_attribute.'"' : '').'
                                                        GROUP BY ps.`id_supplier`');
                                            if (!empty($rows))
                                            {
                                                foreach ($rows as $val)
                                                {
                                                    $field .= $cacheSuppliers[$val['id_supplier']].$exportConfig['valuesep'];
                                                }
                                            }
                                            $field = trim($field, $exportConfig['valuesep'].' ');
                                            break;
                                        case 'last_order':
                                            $field = '';
                                            $sql = 'SELECT o.date_add
                                                    FROM '._DB_PREFIX_.'orders o
                                                    LEFT JOIN '._DB_PREFIX_.'order_detail od ON (o.id_order=od.id_order)
                                                    WHERE o.valid=1
                                                        AND od.product_id="'.(int) $p->id.'"
                                                        '.((!empty($id_product_attribute)) ? ' AND od.product_attribute_id="'.(int) $id_product_attribute.'"' : '').'
                                                    ORDER BY date_add DESC
                                                    LIMIT 1';
                                            $row = Db::getInstance()->ExecuteS($sql);
                                            if (!empty($row[0]['date_add']))
                                            {
                                                $field = $row[0]['date_add'];
                                            }
                                            break;
                                        case '_fixed_value':
                                            $field = $f['modifications'];
                                            break;
                                        case 'location':
                                            $field = '';
                                            if (SCAS)
                                            {
                                                $id_warehouse = (int) str_replace('warehouse_', '', $f['options']);

                                                if (($type_advanced_stock_management == 2 || $type_advanced_stock_management == 3) && !empty($id_warehouse))
                                                {
                                                    if (!empty($id_warehouse) && is_numeric($id_warehouse) && $id_warehouse > 0)
                                                    {
                                                        $hasCombi = $p->hasAttributes();
                                                        $query_exist = new DbQuery();
                                                        $query_exist->select('wpl.id_warehouse_product_location');
                                                        $query_exist->from('warehouse_product_location', 'wpl');
                                                        $query_exist->where('wpl.id_product = '.(int) $p->id.'
                                                                AND wpl.id_warehouse = '.(int) $id_warehouse
                                                        );
                                                        if (!empty($id_product_attribute))
                                                        {
                                                            $query_exist->where('wpl.id_product_attribute = '.(int) $id_product_attribute);
                                                        }
                                                        elseif ($hasCombi)
                                                        {
                                                            $query_exist->where('wpl.id_product_attribute != 0');
                                                        }
                                                        else
                                                        {
                                                            $query_exist->where('wpl.id_product_attribute = 0');
                                                        }
                                                        $in_warehouse = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query_exist);
                                                        if (!empty($in_warehouse))
                                                        {
                                                            $new = new WarehouseProductLocation($in_warehouse);
                                                            $field = $new->location;
                                                        }
                                                    }
                                                }
                                                elseif (empty($id_warehouse))
                                                {
                                                    if (!empty($id_product_attribute))
                                                    {
                                                        $field = $product_attribute['pa_location'];
                                                    }
                                                    else
                                                    {
                                                        $field = $p->location;
                                                    }
                                                }
                                            }
                                            else
                                            {
                                                if (!empty($id_product_attribute))
                                                {
                                                    $field = $product_attribute['pa_location'];
                                                }
                                                elseif ($fullProduct && version_compare(_PS_VERSION_, '1.7.7.8', '>='))
                                                {
                                                    $field = Db::getInstance()->getValue('SELECT location FROM '._DB_PREFIX_.'product WHERE id_product = '.(int) $p->id);
                                                }
                                                else
                                                {
                                                    $field = $p->location;
                                                }
                                            }
                                            break;
                                        case 'quantity':
                                            if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                                            {
                                                $qty = '';
                                                $no_scas = true;
                                                if (SCAS)
                                                {
                                                    $id_warehouse = (int) str_replace('warehouse_', '', $f['options']);

                                                    if ($type_advanced_stock_management == 2)
                                                    {
                                                        $qty = 0;
                                                        $no_scas = false;
                                                        if (!empty($id_warehouse) && is_numeric($id_warehouse) && $id_warehouse > 0)
                                                        {
                                                            $hasCombi = $p->hasAttributes();
                                                            $query_exist = new DbQuery();
                                                            $query_exist->select('wpl.id_warehouse_product_location');
                                                            $query_exist->from('warehouse_product_location', 'wpl');
                                                            $query_exist->where('wpl.id_product = '.(int) $p->id.'
                                                                AND wpl.id_warehouse = '.(int) $id_warehouse
                                                            );
                                                            if (!empty($id_product_attribute) && $switchObjectOptionTwo != 'product_value')
                                                            {
                                                                $query_exist->where('wpl.id_product_attribute = '.(int) $id_product_attribute);
                                                            }
                                                            elseif ($hasCombi && $switchObjectOptionTwo != 'product_value')
                                                            {
                                                                $query_exist->where('wpl.id_product_attribute != 0');
                                                            }
                                                            else
                                                            {
                                                                $query_exist->where('wpl.id_product_attribute = 0');
                                                            }
                                                            $in_warehouse = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query_exist);
                                                            if (!empty($in_warehouse))
                                                            {
                                                                $query_quantity = new DbQuery();
                                                                $query_quantity->select('SUM(st.physical_quantity) as physical_quantity');
                                                                $query_quantity->from('stock', 'st');
                                                                $query_quantity->where('st.id_product = '.(int) $p->id);
                                                                $query_quantity->where('st.id_warehouse = '.(int) $id_warehouse);

                                                                if (!empty($id_product_attribute) && $switchObjectOptionTwo != 'product_value')
                                                                {
                                                                    $query_quantity->where('st.id_product_attribute = '.(int) $id_product_attribute);
                                                                }
                                                                elseif ($hasCombi && $switchObjectOptionTwo != 'product_value')
                                                                {
                                                                    $query_quantity->where('st.id_product_attribute != 0');
                                                                }
                                                                else
                                                                {
                                                                    $query_quantity->where('st.id_product_attribute = 0');
                                                                }

                                                                $avanced_quantities = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($query_quantity);

                                                                if (!empty($avanced_quantities['physical_quantity']))
                                                                {
                                                                    $qty = (int) $avanced_quantities['physical_quantity'];
                                                                }
                                                            }
                                                        }
                                                    }
                                                    else
                                                    {
                                                        if (!empty($id_warehouse) && is_numeric($id_warehouse) && $id_warehouse > 0)
                                                        {
                                                            $no_scas = false;
                                                        }
                                                    }
                                                }

                                                if ($no_scas)
                                                {
                                                    switch ($switchObjectOptionTwo){
                                                        case 'product_value':
                                                            $qty = StockAvailable::getQuantityAvailableByProduct($p->id, 0, (SCMS && $selected_shops_id > 0 ? (int) $selected_shops_id : null));
                                                            break;
                                                        case 'combination_value':
                                                            $qty = 0;
                                                            if (!empty($id_product_attribute))
                                                            {
                                                                $qty = StockAvailable::getQuantityAvailableByProduct($p->id, (int) $id_product_attribute, (SCMS && $selected_shops_id > 0 ? (int) $selected_shops_id : null));
                                                            }
                                                            break;
                                                        default:
                                                            $qty = StockAvailable::getQuantityAvailableByProduct($p->id, (int) $id_product_attribute, (SCMS && $selected_shops_id > 0 ? (int) $selected_shops_id : null));
                                                    }
                                                }

                                                if (!$exportConfig['exportoutofstock'] && SCAS)
                                                {
                                                    $quantity_SCAS += (int) $qty;
                                                }
                                            }
                                            else
                                            {
                                                $qty = $p->getQuantity($p->id, ($id_product_attribute == 0 || $switchObjectOptionTwo == 'product_value' ? null : $id_product_attribute));
                                            }
                                            $field = $qty;
                                            break;
                                        case 'physical_quantity':
                                        case 'reserved_quantity':
                                            $sql = 'SELECT '.$f['name'].' FROM '._DB_PREFIX_.'stock_available WHERE id_product='.(int) $p->id.' AND id_product_attribute='.(int) ($id_product_attribute > 0 && $switchObjectOptionTwo != 'product_value' ? $id_product_attribute : 0).(SCMS && $selected_shops_id > 0 ? ' AND id_shop='.(int) $selected_shops_id : '');
                                            $field = (int) Db::getInstance()->getValue($sql);
                                            break;
                                        case 'location_new':
                                            $sql = 'SELECT location FROM '._DB_PREFIX_.'stock_available WHERE id_product='.(int) $p->id.' AND id_product_attribute='.(int) ($id_product_attribute > 0 ? $id_product_attribute : 0).(SCMS && $selected_shops_id > 0 ? ' AND id_shop='.(int) $selected_shops_id : '');
                                            $field = Db::getInstance()->getValue($sql);
                                            break;
                                        case 'quantity_physical':
                                            $qty = '';
                                            if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                                            {
                                                $no_scas = true;
                                                if (SCAS)
                                                {
                                                    $id_warehouse = (int) str_replace('warehouse_', '', $f['options']);

                                                    if ($type_advanced_stock_management == 2)
                                                    {
                                                        $qty = 0;
                                                        $no_scas = false;
                                                        if (!empty($id_warehouse) && is_numeric($id_warehouse) && $id_warehouse > 0)
                                                        {
                                                            $hasCombi = $p->hasAttributes();
                                                            $query_exist = new DbQuery();
                                                            $query_exist->select('wpl.id_warehouse_product_location');
                                                            $query_exist->from('warehouse_product_location', 'wpl');
                                                            $query_exist->where('wpl.id_product = '.(int) $p->id.'
                                                                AND wpl.id_warehouse = '.(int) $id_warehouse
                                                            );
                                                            if (!empty($id_product_attribute) && $switchObjectOptionTwo != 'product_value')
                                                            {
                                                                $query_exist->where('wpl.id_product_attribute = '.(int) $id_product_attribute);
                                                            }
                                                            elseif ($hasCombi && $switchObjectOptionTwo != 'product_value')
                                                            {
                                                                $query_exist->where('wpl.id_product_attribute != 0');
                                                            }
                                                            else
                                                            {
                                                                $query_exist->where('wpl.id_product_attribute = 0');
                                                            }
                                                            $in_warehouse = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query_exist);
                                                            if (!empty($in_warehouse))
                                                            {
                                                                $query_quantity = new DbQuery();
                                                                $query_quantity->select('SUM(st.physical_quantity) as physical_quantity');
                                                                $query_quantity->from('stock', 'st');
                                                                $query_quantity->where('st.id_product = '.(int) $p->id);
                                                                $query_quantity->where('st.id_warehouse = '.(int) $id_warehouse);

                                                                if (!empty($id_product_attribute) && $switchObjectOptionTwo != 'product_value')
                                                                {
                                                                    $query_quantity->where('st.id_product_attribute = '.(int) $id_product_attribute);
                                                                }
                                                                elseif ($hasCombi && $switchObjectOptionTwo != 'product_value')
                                                                {
                                                                    $query_quantity->where('st.id_product_attribute != 0');
                                                                }
                                                                else
                                                                {
                                                                    $query_quantity->where('st.id_product_attribute = 0');
                                                                }

                                                                $avanced_quantities = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($query_quantity);

                                                                if (!empty($avanced_quantities['physical_quantity']))
                                                                {
                                                                    $qty = (int) $avanced_quantities['physical_quantity'];
                                                                }
                                                            }
                                                        }
                                                    }

                                                    if (!$exportConfig['exportoutofstock'] && SCAS)
                                                    {
                                                        $quantity_SCAS += (int) $qty;
                                                    }
                                                }
                                            }
                                            $field = $qty;
                                            break;
                                        case 'quantity_usable':
                                            $qty = '';
                                            if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                                            {
                                                $no_scas = true;
                                                if (SCAS)
                                                {
                                                    $id_warehouse = (int) str_replace('warehouse_', '', $f['options']);

                                                    if ($type_advanced_stock_management == 2)
                                                    {
                                                        $qty = 0;
                                                        $no_scas = false;
                                                        if (!empty($id_warehouse) && is_numeric($id_warehouse) && $id_warehouse > 0)
                                                        {
                                                            $hasCombi = $p->hasAttributes();
                                                            $query_exist = new DbQuery();
                                                            $query_exist->select('wpl.id_warehouse_product_location');
                                                            $query_exist->from('warehouse_product_location', 'wpl');
                                                            $query_exist->where('wpl.id_product = '.(int) $p->id.'
                                                                AND wpl.id_warehouse = '.(int) $id_warehouse
                                                            );
                                                            if (!empty($id_product_attribute) && $switchObjectOptionTwo != 'product_value')
                                                            {
                                                                $query_exist->where('wpl.id_product_attribute = '.(int) $id_product_attribute);
                                                            }
                                                            elseif ($hasCombi && $switchObjectOptionTwo != 'product_value')
                                                            {
                                                                $query_exist->where('wpl.id_product_attribute != 0');
                                                            }
                                                            else
                                                            {
                                                                $query_exist->where('wpl.id_product_attribute = 0');
                                                            }
                                                            $in_warehouse = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query_exist);
                                                            if (!empty($in_warehouse))
                                                            {
                                                                $query_quantity = new DbQuery();
                                                                $query_quantity->select('SUM(st.usable_quantity) as usable_quantity');
                                                                $query_quantity->from('stock', 'st');
                                                                $query_quantity->where('st.id_product = '.(int) $p->id);
                                                                $query_quantity->where('st.id_warehouse = '.(int) $id_warehouse);

                                                                if (!empty($id_product_attribute) && $switchObjectOptionTwo != 'product_value')
                                                                {
                                                                    $query_quantity->where('st.id_product_attribute = '.(int) $id_product_attribute);
                                                                }
                                                                elseif ($hasCombi && $switchObjectOptionTwo != 'product_value')
                                                                {
                                                                    $query_quantity->where('st.id_product_attribute != 0');
                                                                }
                                                                else
                                                                {
                                                                    $query_quantity->where('st.id_product_attribute = 0');
                                                                }

                                                                $avanced_quantities = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($query_quantity);

                                                                if (!empty($avanced_quantities['usable_quantity']))
                                                                {
                                                                    $qty = (int) $avanced_quantities['usable_quantity'];
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                            $field = $qty;
                                            break;
                                        case 'quantity_real':
                                            $qty = '';
                                            if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                                            {
                                                $no_scas = true;
                                                if (SCAS)
                                                {
                                                    $id_warehouse = (int) str_replace('warehouse_', '', $f['options']);

                                                    if ($type_advanced_stock_management == 2)
                                                    {
                                                        $qty = 0;
                                                        $no_scas = false;
                                                        if (!empty($id_warehouse) && is_numeric($id_warehouse) && $id_warehouse > 0)
                                                        {
                                                            $hasCombi = $p->hasAttributes();
                                                            $query_exist = new DbQuery();
                                                            $query_exist->select('wpl.id_warehouse_product_location');
                                                            $query_exist->from('warehouse_product_location', 'wpl');
                                                            $query_exist->where('wpl.id_product = '.(int) $p->id.'
                                                                AND wpl.id_warehouse = '.(int) $id_warehouse
                                                            );
                                                            if (!empty($id_product_attribute) && $switchObjectOptionTwo != 'product_value')
                                                            {
                                                                $query_exist->where('wpl.id_product_attribute = '.(int) $id_product_attribute);
                                                            }
                                                            elseif ($hasCombi && $switchObjectOptionTwo != 'product_value')
                                                            {
                                                                $query_exist->where('wpl.id_product_attribute != 0');
                                                            }
                                                            else
                                                            {
                                                                $query_exist->where('wpl.id_product_attribute = 0');
                                                            }
                                                            $in_warehouse = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query_exist);
                                                            if (!empty($in_warehouse))
                                                            {
                                                                $value = SCI::getProductRealQuantities((int) $p->id,
                                                                    ($switchObjectOptionTwo == 'product_value' ? 0 : (int) $id_product_attribute),
                                                                    (int) $id_warehouse,
                                                                    true,
                                                                    $hasCombi);
                                                                if (!empty($value))
                                                                {
                                                                    $qty = (int) $value;
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                            $field = $qty;
                                            break;
                                        case 'quantity_total_physical':
                                            $qty = '';
                                            if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                                            {
                                                $no_scas = true;
                                                if (SCAS)
                                                {
                                                    if ($type_advanced_stock_management == 2)
                                                    {
                                                        $qty = 0;
                                                        $no_scas = false;
                                                        $hasCombi = $p->hasAttributes();
                                                        $query_exist = new DbQuery();
                                                        $query_exist->select('wpl.id_warehouse_product_location');
                                                        $query_exist->from('warehouse_product_location', 'wpl');
                                                        $query_exist->where('wpl.id_product = '.(int) $p->id);
                                                        if (!empty($id_product_attribute) && $switchObjectOptionTwo != 'product_value')
                                                        {
                                                            $query_exist->where('wpl.id_product_attribute = '.(int) $id_product_attribute);
                                                        }
                                                        elseif ($hasCombi && $switchObjectOptionTwo != 'product_value')
                                                        {
                                                            $query_exist->where('wpl.id_product_attribute != 0');
                                                        }
                                                        else
                                                        {
                                                            $query_exist->where('wpl.id_product_attribute = 0');
                                                        }
                                                        $in_warehouse = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query_exist);
                                                        if (!empty($in_warehouse))
                                                        {
                                                            $query_quantity = new DbQuery();
                                                            $query_quantity->select('SUM(st.physical_quantity) as physical_quantity');
                                                            $query_quantity->from('stock', 'st');
                                                            $query_quantity->where('st.id_product = '.(int) $p->id);

                                                            if (!empty($id_product_attribute) && $switchObjectOptionTwo != 'product_value')
                                                            {
                                                                $query_quantity->where('st.id_product_attribute = '.(int) $id_product_attribute);
                                                            }
                                                            elseif ($hasCombi && $switchObjectOptionTwo != 'product_value')
                                                            {
                                                                $query_quantity->where('st.id_product_attribute != 0');
                                                            }
                                                            else
                                                            {
                                                                $query_quantity->where('st.id_product_attribute = 0');
                                                            }

                                                            $avanced_quantities = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($query_quantity);

                                                            if (!empty($avanced_quantities['physical_quantity']))
                                                            {
                                                                $qty = (int) $avanced_quantities['physical_quantity'];
                                                            }
                                                        }
                                                    }

                                                    if (!$exportConfig['exportoutofstock'] && SCAS)
                                                    {
                                                        $quantity_SCAS += (int) $qty;
                                                    }
                                                }
                                            }
                                            $field = $qty;
                                            break;
                                        case 'quantity_total_usable':
                                            $qty = '';
                                            if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                                            {
                                                $no_scas = true;
                                                if (SCAS)
                                                {
                                                    if ($type_advanced_stock_management == 2)
                                                    {
                                                        $qty = 0;
                                                        $no_scas = false;
                                                        $hasCombi = $p->hasAttributes();
                                                        $query_exist = new DbQuery();
                                                        $query_exist->select('wpl.id_warehouse_product_location');
                                                        $query_exist->from('warehouse_product_location', 'wpl');
                                                        $query_exist->where('wpl.id_product = '.(int) $p->id);
                                                        if (!empty($id_product_attribute) && $switchObjectOptionTwo != 'product_value')
                                                        {
                                                            $query_exist->where('wpl.id_product_attribute = '.(int) $id_product_attribute);
                                                        }
                                                        elseif ($hasCombi && $switchObjectOptionTwo != 'product_value')
                                                        {
                                                            $query_exist->where('wpl.id_product_attribute != 0');
                                                        }
                                                        else
                                                        {
                                                            $query_exist->where('wpl.id_product_attribute = 0');
                                                        }
                                                        $in_warehouse = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query_exist);
                                                        if (!empty($in_warehouse))
                                                        {
                                                            $query_quantity = new DbQuery();
                                                            $query_quantity->select('SUM(st.usable_quantity) as usable_quantity');
                                                            $query_quantity->from('stock', 'st');
                                                            $query_quantity->where('st.id_product = '.(int) $p->id);

                                                            if (!empty($id_product_attribute) && $switchObjectOptionTwo != 'product_value')
                                                            {
                                                                $query_quantity->where('st.id_product_attribute = '.(int) $id_product_attribute);
                                                            }
                                                            elseif ($hasCombi && $switchObjectOptionTwo != 'product_value')
                                                            {
                                                                $query_quantity->where('st.id_product_attribute != 0');
                                                            }
                                                            else
                                                            {
                                                                $query_quantity->where('st.id_product_attribute = 0');
                                                            }

                                                            $avanced_quantities = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($query_quantity);

                                                            if (!empty($avanced_quantities['usable_quantity']))
                                                            {
                                                                $qty = (int) $avanced_quantities['usable_quantity'];
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                            $field = $qty;
                                            break;
                                        case 'vat':
                                            $field = $p->tax_rate;
                                            break;
                                        case 'vat_name':
                                            $field = $p->tax_rate;
                                            $sql = 'SELECT trg.name
                                            FROM `'._DB_PREFIX_.'tax_rules_group` trg
                                            WHERE id_tax_rules_group="'.(int) $p->id_tax_rules_group.'"';
                                            $res = Db::getInstance()->getValue($sql);
                                            if (!empty($res))
                                            {
                                                $field = $res;
                                            }
                                            break;
                                        case 'unit_price_tax_incl':
                                            $field = 0;
                                            if ($p->unit_price_ratio > 0)
                                            {
                                                $field = ps_round($p->getPrice(true, ($id_product_attribute == 0 ? null : $id_product_attribute), 6, null, null, false) / $p->unit_price_ratio, (int) _s('CAT_EXPORT_PRICE_DECIMAL'));
                                            }
                                            break;
                                        case 'unit_price_tax_incl_with_reduc':
                                            $field = 0;
                                            if ($p->unit_price_ratio > 0)
                                            {
                                                $field = ps_round($p->getPrice(true, ($id_product_attribute == 0 ? null : $id_product_attribute)) / $p->unit_price_ratio, (int) _s('CAT_EXPORT_PRICE_DECIMAL'));
                                            }
                                            break;
                                        case 'unit_price_tax_excl':
                                            $field = 0;
                                            if ($p->unit_price_ratio > 0)
                                            {
                                                $field = ps_round($p->getPrice(false, ($id_product_attribute == 0 ? null : $id_product_attribute), 6, null, null, false) / $p->unit_price_ratio, (int) _s('CAT_EXPORT_PRICE_DECIMAL'));
                                            }
                                            break;
                                        case 'unit_price_tax_excl_with_reduc':
                                            $field = 0;
                                            if ($p->unit_price_ratio > 0)
                                            {
                                                $field = ps_round($p->getPrice(false, ($id_product_attribute == 0 ? null : $id_product_attribute)) / $p->unit_price_ratio, (int) _s('CAT_EXPORT_PRICE_DECIMAL'));
                                            }
                                            break;
                                        case 'ecotax_taxincl':
                                            $ecotax = $p->ecotax;
                                            if (!empty($product_attribute['ecotax']) && ($product_attribute['ecotax'] > 0 || $product_attribute['ecotax'] < 0))
                                            {
                                                $ecotax = $product_attribute['ecotax'];
                                            }
                                            $field = $ecotax * SCI::getEcotaxTaxRate();
                                            break;
                                        case 'priceexctax':
                                            if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                                            {
                                                $specific_price = array();
                                                $field = $p->priceCalculation((int) $selected_shops_id, $p->id, ($switchObjectOptionTwo == 'product_value' ? false : (int) $id_product_attribute), (int) SCI::getDefaultCountryId() /*$id_country*/ , 0 /*$id_state*/ , 0 /*$zipcode*/ , (int) Configuration::get('PS_CURRENCY_DEFAULT') /*$id_currency*/ ,
                                                        1 /*$id_group*/ , 1 /*$quantity*/ , 0 /* use tax */ , 6 /*$decimals*/ , 0 /*$only_reduc*/ , 1 /*$use_reduc*/ , 0 /*$with_ecotax*/ , $specific_price, 0 /*$use_group_reduction*/ ,
                                                        0 /*$id_customer*/ , 0 /*$use_customer_price*/ , 0 /*$id_cart*/ , 1 /*$real_quantity*/);
                                            }
                                            else
                                            {
                                                if ($switchObjectOptionTwo == 'product_value')
                                                {
                                                    $field = $p->getPrice(false, false, 6);
                                                }
                                                else
                                                {
                                                    $field = $p->getPrice(false, ($id_product_attribute == 0 ? null : $id_product_attribute), 6);
                                                }
                                            }
                                            if ($exportConfig['shippingfee'] > 0 && $field <= $exportConfig['shippingfeefreefrom'])
                                            {
                                                $field += $exportConfig['shippingfee'];
                                            }
                                            $field = ps_round($field, (int) _s('CAT_EXPORT_PRICE_DECIMAL'));
                                            break;
                                        case 'priceinctax':
                                            if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                                            {
                                                $specific_price = array();
                                                $field = $p->priceCalculation((int) $selected_shops_id, $p->id, ($switchObjectOptionTwo == 'product_value' ? false : (int) $id_product_attribute), (int) SCI::getDefaultCountryId() /*$id_country*/ , 0 /*$id_state*/ , 0 /*$zipcode*/ , (int) Configuration::get('PS_CURRENCY_DEFAULT') /*$id_currency*/ ,
                                                        1 /*$id_group*/ , 1 /*$quantity*/ , 1 /* use tax */ , _s('CAT_EXPORT_PRICE_DECIMAL') /*$decimals*/ , 0 /*$only_reduc*/ , 1 /*$use_reduc*/ , 1 /*$with_ecotax*/ , $specific_price, 0 /*$use_group_reduction*/ ,
                                                        0 /*$id_customer*/ , 0 /*$use_customer_price*/ , 0 /*$id_cart*/ , 1 /*$real_quantity*/);
                                            }
                                            else
                                            {
                                                if ($switchObjectOptionTwo == 'product_value')
                                                {
                                                    $field = ps_round($p->getPrice(true, false), _s('CAT_EXPORT_PRICE_DECIMAL'));
                                                }
                                                else
                                                {
                                                    $field = ps_round($p->getPrice(true, ($id_product_attribute == 0 ? null : $id_product_attribute)), _s('CAT_EXPORT_PRICE_DECIMAL'));
                                                }
                                            }
                                            if ($exportConfig['shippingfee'] > 0 && $field <= $exportConfig['shippingfeefreefrom'])
                                            {
                                                $field += $exportConfig['shippingfee'];
                                            }
                                            $field = ps_round($field, (int) _s('CAT_EXPORT_PRICE_DECIMAL'));
                                            break;
                                        case 'price_inctax_without_reduction':
                                            if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                                            {
                                                $specific_price = array();
                                                $field = $p->priceCalculation((int) $selected_shops_id, $p->id, ($switchObjectOptionTwo == 'product_value' ? false : (int) $id_product_attribute), (int) SCI::getDefaultCountryId() /*$id_country*/ , 0 /*$id_state*/ , 0 /*$zipcode*/ , (int) Configuration::get('PS_CURRENCY_DEFAULT') /*$id_currency*/ ,
                                                        1 /*$id_group*/ , 1 /*$quantity*/ , 1 /* use tax */ , 6 /*$decimals*/ , 0 /*$only_reduc*/ , 0 /*$use_reduc*/ , 1 /*$with_ecotax*/ , $specific_price, 0 /*$use_group_reduction*/ ,
                                                        0 /*$id_customer*/ , 0 /*$use_customer_price*/ , 0 /*$id_cart*/ , 1 /*$real_quantity*/);
                                            }
                                            else
                                            {
                                                $specific_price = array();
                                                $field = $p->priceCalculation((int) $selected_shops_id, $p->id, ($switchObjectOptionTwo == 'product_value' ? false : (int) $id_product_attribute), (int) SCI::getDefaultCountryId() /*$id_country*/ , 0 /*$id_state*/ , 0 /*$zipcode*/ , (int) Configuration::get('PS_CURRENCY_DEFAULT') /*$id_currency*/ ,
                                                        1 /*$id_group*/ , 1 /*$quantity*/ , 1 /* use tax */ , 6 /*$decimals*/ , 0 /*$only_reduc*/ , 0 /*$use_reduc*/ , 1 /*$with_ecotax*/ , $specific_price, 0 /*$use_group_reduction*/);
                                            }

                                            if ($exportConfig['shippingfee'] > 0 && $field <= $exportConfig['shippingfeefreefrom'])
                                            {
                                                $field += $exportConfig['shippingfee'];
                                            }
                                            $field = ps_round($field, (int) _s('CAT_EXPORT_PRICE_DECIMAL'));
                                            break;
                                        case 'price_exctax_without_reduction':
                                            if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                                            {
                                                $specific_price = array();
                                                $field = $p->priceCalculation((int) $selected_shops_id, $p->id, ($switchObjectOptionTwo == 'product_value' ? false : (int) $id_product_attribute), (int) SCI::getDefaultCountryId() /*$id_country*/ , 0 /*$id_state*/ , 0 /*$zipcode*/ , (int) Configuration::get('PS_CURRENCY_DEFAULT') /*$id_currency*/ ,
                                                        1 /*$id_group*/ , 1 /*$quantity*/ , 0 /* use tax */ , 6 /*$decimals*/ , 0 /*$only_reduc*/ , 0 /*$use_reduc*/ , 1 /*$with_ecotax*/ , $specific_price, 0 /*$use_group_reduction*/ ,
                                                        0 /*$id_customer*/ , 0 /*$use_customer_price*/ , 0 /*$id_cart*/ , 1 /*$real_quantity*/);
                                            }
                                            else
                                            {
                                                $specific_price = array();
                                                $field = $p->priceCalculation((int) $selected_shops_id, $p->id, ($switchObjectOptionTwo == 'product_value' ? false : (int) $id_product_attribute), (int) SCI::getDefaultCountryId() /*$id_country*/ , 0 /*$id_state*/ , 0 /*$zipcode*/ , (int) Configuration::get('PS_CURRENCY_DEFAULT') /*$id_currency*/ ,
                                                    1 /*$id_group*/ , 1 /*$quantity*/ , 0 /* use tax */ , 6 /*$decimals*/ , 0 /*$only_reduc*/ , 0 /*$use_reduc*/ , 1 /*$with_ecotax*/ , $specific_price, 0 /*$use_group_reduction*/);
                                            }

                                            if ($exportConfig['shippingfee'] > 0 && $field <= $exportConfig['shippingfeefreefrom'])
                                            {
                                                $field += $exportConfig['shippingfee'];
                                            }
                                            $field = ps_round($field, (int) _s('CAT_EXPORT_PRICE_DECIMAL'));
                                            break;
                                        case 'priceinctaxwithshipping':
                                            if (SCMS)
                                            {
                                                $specific_price = array();
                                                $price = $p->priceCalculation((int) $selected_shops_id, $p->id, (int) $id_product_attribute, (int) SCI::getDefaultCountryId() /*$id_country*/ , 0 /*$id_state*/ , 0 /*$zipcode*/ , (int) Configuration::get('PS_CURRENCY_DEFAULT') /*$id_currency*/ ,
                                                        1 /*$id_group*/ , 1 /*$quantity*/ , 1 /* use tax */ , 6 /*$decimals*/ , 0 /*$only_reduc*/ , 1 /*$use_reduc*/ , 1 /*$with_ecotax*/ , $specific_price, 0 /*$use_group_reduction*/ ,
                                                        0 /*$id_customer*/ , 0 /*$use_customer_price*/ , 0 /*$id_cart*/ , 1 /*$real_quantity*/);
                                            }
                                            else
                                            {
                                                $price = ps_round($p->getPrice(true, ($id_product_attribute == 0 ? null : $id_product_attribute)), 6);
                                            }
                                            $weight = $p->weight;
                                            $id_carrier = null;
                                            if (sc_array_key_exists($f['options'], $getCarrierByName))
                                            {
                                                $id_carrier = (int) $getCarrierByName[$f['options']];
                                            }
                                            $field = ps_round(getOrderShippingCost($id_carrier, true, 0, $weight, $price) + $price, 6);
                                            if ($exportConfig['shippingfee'] > 0 && $field <= $exportConfig['shippingfeefreefrom'])
                                            {
                                                $field += $exportConfig['shippingfee'];
                                            }
                                            $field = ps_round($field, (int) _s('CAT_EXPORT_PRICE_DECIMAL'));
                                            break;
                                        case 'productshippingcost':
                                            if (SCMS)
                                            {
                                                $specific_price = array();
                                                $price = $p->priceCalculation((int) $selected_shops_id, $p->id, (int) $id_product_attribute, (int) SCI::getDefaultCountryId() /*$id_country*/ , 0 /*$id_state*/ , 0 /*$zipcode*/ , (int) Configuration::get('PS_CURRENCY_DEFAULT') /*$id_currency*/ ,
                                                        1 /*$id_group*/ , 1 /*$quantity*/ , 1 /* use tax */ , 6 /*$decimals*/ , 0 /*$only_reduc*/ , 1 /*$use_reduc*/ , 0 /*$with_ecotax*/ , $specific_price, 0 /*$use_group_reduction*/ ,
                                                        0 /*$id_customer*/ , 0 /*$use_customer_price*/ , 0 /*$id_cart*/ , 1 /*$real_quantity*/);
                                            }
                                            else
                                            {
                                                $price = $p->getPrice(true, ($id_product_attribute == 0 ? null : $id_product_attribute), 6);
                                            }
                                            $price = $p->getPrice(true, ($id_product_attribute == 0 ? null : $id_product_attribute), 6);
                                            $weight = $p->weight;
                                            $field = ps_round(getOrderShippingCost(null, true, 0, $weight, $price), 6);
                                            if ($exportConfig['shippingfee'] > 0 && $field <= $exportConfig['shippingfeefreefrom'])
                                            {
                                                $field += $exportConfig['shippingfee'];
                                            }
                                            $field = ps_round($field, (int) _s('CAT_EXPORT_PRICE_DECIMAL'));
                                            break;
                                        case 'link_to_cover_image':
                                            if ((int) $getIDlangByISO[$f['lang']] < 1)
                                            {
                                                exit(_l('You have to set the language in the mapping for the field:').' '._l('link_to_cover_image'));
                                            }
                                            $tmp = (!$exportConfig['exportcombinations'] || $id_product_attribute == 0 || $product_attribute['id_image'] == 0 ? $p->getCover($p->id) : $product_attribute);
                                            if ($tmp['id_image'] != '')
                                            {
                                                $field = $_PS_BASE_URL_.getImgPath($p->id, $tmp['id_image'], _s('CAT_EXPORT_IMAGE_FORMAT'), 'jpg');
                                            }
                                            break;
                                        case 'link_to_image01':case 'link_to_image02':case 'link_to_image03':case 'link_to_image04':case 'link_to_image05':
                                        case 'link_to_image06':case 'link_to_image07':case 'link_to_image08':case 'link_to_image09':case 'link_to_image10':
                                        case 'image_link':
                                            if ((int) $getIDlangByISO[$f['lang']] < 1)
                                            {
                                                exit(_l('You have to set the language in the mapping for the field:').' '._l('image_link'));
                                            }
                                            if (!sc_array_key_exists($p->id.'_'.$id_product_attribute, $cacheProductImages))
                                            {
                                                $cacheProductImages[$p->id.'_'.$id_product_attribute] = array();
                                                $res = Db::getInstance()->ExecuteS('
                                                        SELECT i.`cover`, i.`id_image`, il.`legend`, i.`position`
                                                        FROM `'._DB_PREFIX_.'image` i
                                                        LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (i.`id_image` = il.`id_image` AND il.`id_lang` = '.(int) $getIDlangByISO[$f['lang']].')
                                                        '.(!empty($id_product_attribute) ? ' INNER JOIN `'._DB_PREFIX_.'product_attribute_image` pai ON (i.`id_image` = pai.`id_image` AND pai.`id_product_attribute` = '.(int) $id_product_attribute.') ' : '').'
                                                        WHERE i.`id_product` = '.(int) $p->id.'
                                                        ORDER BY `position`');
                                                $count = 0;
                                                foreach ($res as $k => $v)
                                                {
                                                    $position_image = count($cacheProductImages[$p->id.'_'.$id_product_attribute]) + 1;
                                                    $cacheProductImages[$p->id.'_'.$id_product_attribute][$position_image] = $v['id_image'];
                                                    $legends[$p->id.'_'.$id_product_attribute][(int) $getIDlangByISO[$f['lang']]][$v['id_image']] = $v['legend'];
                                                    ++$count;
                                                }
                                            }

                                            $num = $num_img_link;
                                            if ($num_img_link <= $count)
                                            {
                                                if (is_array($cacheProductImages[$p->id.'_'.$id_product_attribute]) && sc_array_key_exists((int) $num, $cacheProductImages[$p->id.'_'.$id_product_attribute]) && (int) $cacheProductImages[$p->id.'_'.$id_product_attribute][$num])
                                                {
                                                    $field = $_PS_BASE_URL_.getImgPath($p->id, $cacheProductImages[$p->id.'_'.$id_product_attribute][$num], _s('CAT_EXPORT_IMAGE_FORMAT'), 'jpg');
                                                    ++$num_img_link;
                                                }
                                            }
                                            break;
                                        case 'image_url':
                                            if ((int) $getIDlangByISO[$f['lang']] < 1)
                                            {
                                                exit(_l('You have to set the language in the mapping for the field:').' '._l('image_url'));
                                            }
                                            if (!sc_array_key_exists($p->id.'_'.$id_product_attribute, $cacheProductImages))
                                            {
                                                $cacheProductImages[$p->id.'_'.$id_product_attribute] = array();
                                                $res = Db::getInstance()->ExecuteS('
                                                        SELECT i.`cover`, i.`id_image`, il.`legend`, i.`position`
                                                        FROM `'._DB_PREFIX_.'image` i
                                                        LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (i.`id_image` = il.`id_image` AND il.`id_lang` = '.(int) $getIDlangByISO[$f['lang']].')
                                                        '.(!empty($id_product_attribute) ? ' INNER JOIN `'._DB_PREFIX_.'product_attribute_image` pai ON (i.`id_image` = pai.`id_image` AND pai.`id_product_attribute` = '.(int) $id_product_attribute.') ' : '').'
                                                        WHERE i.`id_product` = '.(int) $p->id.'
                                                        ORDER BY `position`');
                                                $count = 0;
                                                foreach ($res as $k => $v)
                                                {
                                                    $position_image = count($cacheProductImages[$p->id.'_'.$id_product_attribute]) + 1;
                                                    $cacheProductImages[$p->id.'_'.$id_product_attribute][$position_image] = $v['id_image'];
                                                    $legends[$p->id.'_'.$id_product_attribute][(int) $getIDlangByISO[$f['lang']]][$v['id_image']] = $v['legend'];
                                                    ++$count;
                                                }
                                            }

                                            $num = $num_img_url;

                                            if ($num_img_url <= $count)
                                            {
                                                if (is_array($cacheProductImages[$p->id.'_'.$id_product_attribute]) && sc_array_key_exists((int) $num, $cacheProductImages[$p->id.'_'.$id_product_attribute]) && (int) $cacheProductImages[$p->id.'_'.$id_product_attribute][$num])
                                                {
                                                    $temp_id_image = $cacheProductImages[$p->id.'_'.$id_product_attribute][$num];
                                                    $field = $link->getImageLink($p->link_rewrite[(int) $getIDlangByISO[$f['lang']]], $temp_id_image);

                                                    ++$num_img_url;
                                                }
                                            }
                                            break;
                                        case 'image_legend':
                                            if ((int) $getIDlangByISO[$f['lang']] < 1)
                                            {
                                                exit(_l('You have to set the language in the mapping for the field:').' '._l('image_legend'));
                                            }
                                            if (!sc_array_key_exists($p->id.'_'.$id_product_attribute, $cacheProductImages) || empty($legends[$p->id.'_'.$id_product_attribute][(int) $getIDlangByISO[$f['lang']]]))
                                            {
                                                $cacheProductImages[$p->id.'_'.$id_product_attribute] = array();
                                                $res = Db::getInstance()->ExecuteS('
                                                        SELECT i.`cover`, i.`id_image`, il.`legend`, i.`position`
                                                        FROM `'._DB_PREFIX_.'image` i
                                                        LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (i.`id_image` = il.`id_image` AND il.`id_lang` = '.(int) $getIDlangByISO[$f['lang']].')
                                                        '.(!empty($id_product_attribute) ? ' INNER JOIN `'._DB_PREFIX_.'product_attribute_image` pai ON (i.`id_image` = pai.`id_image` AND pai.`id_product_attribute` = '.(int) $id_product_attribute.' AND pai.`id_image`!=0) ' : '').'
                                                        WHERE i.`id_product` = '.(int) $p->id.'
                                                        ORDER BY `position`');
                                                $count = 0;
                                                foreach ($res as $k => $v)
                                                {
                                                    $position_image = count($cacheProductImages[$p->id.'_'.$id_product_attribute]) + 1;
                                                    $cacheProductImages[$p->id.'_'.$id_product_attribute][$position_image] = $v['id_image'];
                                                    $legends[$p->id.'_'.$id_product_attribute][(int) $getIDlangByISO[$f['lang']]][$v['id_image']] = $v['legend'];
                                                    ++$count;
                                                }
                                            }
                                            $num = $num_img_legend;

                                            if ($num_img_legend <= $count)
                                            {
                                                if (is_array($cacheProductImages[$p->id.'_'.$id_product_attribute]) && sc_array_key_exists((int) $num, $cacheProductImages[$p->id.'_'.$id_product_attribute]) && !empty($legends[$p->id.'_'.$id_product_attribute][(int) $getIDlangByISO[$f['lang']]][$cacheProductImages[$p->id.'_'.$id_product_attribute][$num]]))
                                                {
                                                    $field = $legends[$p->id.'_'.$id_product_attribute][(int) $getIDlangByISO[$f['lang']]][$cacheProductImages[$p->id.'_'.$id_product_attribute][$num]];
                                                    ++$num_img_legend;
                                                }
                                            }
                                            break;
                                        case 'image_id':
                                            if (!sc_array_key_exists($p->id.'_'.$id_product_attribute, $cacheProductImages))
                                            {
                                                $img_id_lang = (int) (isset($f['lang']) && !empty($f['lang']) ? $getIDlangByISO[$f['lang']] : $id_lang);
                                                $cacheProductImages[$p->id.'_'.$id_product_attribute] = array();
                                                $res = Db::getInstance()->ExecuteS('
                                                        SELECT i.`cover`, i.`id_image`, il.`legend`, i.`position`
                                                        FROM `'._DB_PREFIX_.'image` i
                                                        LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (i.`id_image` = il.`id_image` AND il.`id_lang` = '.$img_id_lang.')
                                                        '.(!empty($id_product_attribute) ? ' INNER JOIN `'._DB_PREFIX_.'product_attribute_image` pai ON (i.`id_image` = pai.`id_image` AND pai.`id_product_attribute` = '.(int) $id_product_attribute.' AND pai.`id_image`!=0) ' : '').'
                                                        WHERE i.`id_product` = '.(int) $p->id.'
                                                        ORDER BY `position`');
                                                $count = 0;
                                                foreach ($res as $k => $v)
                                                {
                                                    if (!empty($cacheProductImages[$p->id.'_'.$id_product_attribute]))
                                                    {
                                                        $position_image = count($cacheProductImages[$p->id.'_'.$id_product_attribute]) + 1;
                                                    }
                                                    else
                                                    {
                                                        $position_image = 1;
                                                    }
                                                    $cacheProductImages[$p->id.'_'.$id_product_attribute][$position_image] = $v['id_image'];
                                                    $legends[$p->id.'_'.$id_product_attribute][$img_id_lang][$v['id_image']] = $v['legend'];
                                                    ++$count;
                                                }
                                            }

                                            $num = $num_img_id;

                                            if ($num_img_id <= $count)
                                            {
                                                if (is_array($cacheProductImages[$p->id.'_'.$id_product_attribute]) && sc_array_key_exists((int) $num, $cacheProductImages[$p->id.'_'.$id_product_attribute]) && !empty($cacheProductImages[$p->id.'_'.$id_product_attribute][$num]))
                                                {
                                                    $field = $cacheProductImages[$p->id.'_'.$id_product_attribute][$num];
                                                    ++$num_img_id;
                                                }
                                            }
                                            break;
                                        case 'image_id_all':
                                            $field = '';
                                            $res = createQueriesCache('
                                                        SELECT i.`id_image`
                                                        FROM `'._DB_PREFIX_.'image` i
                                                        '.(!empty($id_product_attribute) ? ' INNER JOIN `'._DB_PREFIX_.'product_attribute_image` pai ON (i.`id_image` = pai.`id_image` AND pai.`id_product_attribute` = '.(int) $id_product_attribute.' AND pai.`id_image`!=0) ' : '').'
                                                        WHERE i.`id_product` = '.(int) $p->id.'
                                                        ORDER BY `position`');
                                            foreach ($res as $k => $v)
                                            {
                                                if (!empty($v))
                                                {
                                                    if (!empty($field))
                                                    {
                                                        $field .= $exportConfig['valuesep'];
                                                    }
                                                    $field .= $v['id_image'];
                                                }
                                            }
                                            break;
                                        case 'image_default_id':
                                            $field = '';
                                            if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                                            {
                                                $res = createQueriesCache('
                                                        SELECT i.`id_image`
                                                        FROM `'._DB_PREFIX_.'image` i
                                                            INNER JOIN  `'._DB_PREFIX_.'image_shop` ish ON (i.id_image = ish.id_image AND ish.id_shop = '.(int) $selected_shops_id.')
                                                        WHERE i.`id_product` = '.(int) $p->id.'
                                                            AND ish.cover = 1
                                                        ORDER BY `position` ASC');
                                            }
                                            else
                                            {
                                                $res = createQueriesCache('
                                                        SELECT i.`id_image`
                                                        FROM `'._DB_PREFIX_.'image` i
                                                        WHERE i.`id_product` = '.(int) $p->id.'
                                                            AND i.cover = 1
                                                        ORDER BY `position` ASC');
                                            }
                                            if (!empty($res[0]['id_image']))
                                            {
                                                $field = $res[0]['id_image'];
                                            }
                                            break;
                                        case 'links_to_all_images':
                                            if ((int) $getIDlangByISO[$f['lang']] < 1)
                                            {
                                                exit(_l('You have to set the language in the mapping for the field:').' '._l('links_to_all_images'));
                                            }
                                            $res = createQueriesCache('SELECT i.`cover`, i.`id_image`, il.`legend`, i.`position`
                                                    FROM `'._DB_PREFIX_.'image` i
                                                    LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (i.`id_image` = il.`id_image` AND il.`id_lang` = '.(int) $getIDlangByISO[$f['lang']].')
                                                    '.(!empty($id_product_attribute) ? ' INNER JOIN `'._DB_PREFIX_.'product_attribute_image` pai ON (i.`id_image` = pai.`id_image` AND pai.`id_product_attribute` = '.(int) $id_product_attribute.') ' : '').'
                                                    WHERE i.`id_product` = '.(int) $p->id.'
                                                    ORDER BY `position`');
                                            $imgs = $cacheProductImages[$p->id.'_'.$id_product_attribute] = array();
                                            foreach ($res as $k => $v)
                                            {
                                                $position_image = count($cacheProductImages[$p->id.'_'.$id_product_attribute]) + 1;
                                                $cacheProductImages[$p->id.'_'.$id_product_attribute][$position_image] = $v['id_image'];
                                                $legends[$p->id.'_'.$id_product_attribute][(int) $getIDlangByISO[$f['lang']]][$v['id_image']] = $v['legend'];
                                                $imgs[] = $_PS_BASE_URL_.getImgPath($p->id, $v['id_image'], _s('CAT_EXPORT_IMAGE_FORMAT'), 'jpg');
                                            }
                                            $field = join($exportConfig['valuesep'], $imgs);
                                            break;
                                        case 'urls_to_all_images':
                                            if ((int) $getIDlangByISO[$f['lang']] < 1)
                                            {
                                                exit(_l('You have to set the language in the mapping for the field:').' '._l('urls_to_all_images'));
                                            }
                                            $res = createQueriesCache('SELECT i.`cover`, i.`id_image`, il.`legend`, i.`position`
                                                    FROM `'._DB_PREFIX_.'image` i
                                                    LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (i.`id_image` = il.`id_image` AND il.`id_lang` = '.(int) $getIDlangByISO[$f['lang']].')
                                                    '.(!empty($id_product_attribute) ? ' INNER JOIN `'._DB_PREFIX_.'product_attribute_image` pai ON (i.`id_image` = pai.`id_image` AND pai.`id_product_attribute` = '.(int) $id_product_attribute.') ' : '').'
                                                    WHERE i.`id_product` = '.(int) $p->id.'
                                                    ORDER BY `position`');
                                            $imgs = $cacheProductImages[$p->id.'_'.$id_product_attribute] = array();
                                            foreach ($res as $k => $v)
                                            {
                                                $position_image = count($cacheProductImages[$p->id.'_'.$id_product_attribute]) + 1;
                                                $cacheProductImages[$p->id.'_'.$id_product_attribute][$position_image] = $v['id_image'];
                                                $legends[$p->id.'_'.$id_product_attribute][(int) $getIDlangByISO[$f['lang']]][$v['id_image']] = $v['legend'];

                                                $temp_url = $link->getImageLink($p->link_rewrite[(int) $getIDlangByISO[$f['lang']]], $v['id_image']);
                                                $imgs[] = $temp_url;
                                            }
                                            $field = join($exportConfig['valuesep'], $imgs);
                                            break;
                                        case 'image_id_all_for_product':
                                            $field = '';
                                            $res = createQueriesCache('SELECT i.`cover`, i.`id_image`, i.`position`
                                                    FROM `'._DB_PREFIX_.'image` i
                                                    WHERE i.`id_product` = '.(int) $p->id.'
                                                    ORDER BY `position`');
                                            foreach ($res as $k => $v)
                                            {
                                                if (!empty($v))
                                                {
                                                    if (!empty($field))
                                                    {
                                                        $field .= $exportConfig['valuesep'];
                                                    }
                                                    $field .= $v['id_image'];
                                                }
                                            }
                                            break;
                                        case 'links_to_all_images_for_product':
                                            if ((int) $getIDlangByISO[$f['lang']] < 1)
                                            {
                                                exit(_l('You have to set the language in the mapping for the field:').' '._l('links_to_all_images'));
                                            }
                                            $res = createQueriesCache('SELECT i.`cover`, i.`id_image`, il.`legend`, i.`position`
                                                    FROM `'._DB_PREFIX_.'image` i
                                                    LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (i.`id_image` = il.`id_image` AND il.`id_lang` = '.(int) $getIDlangByISO[$f['lang']].')
                                                    WHERE i.`id_product` = '.(int) $p->id.'
                                                    ORDER BY `position`');
                                            $imgs = $cacheProductImages[$p->id.'_'.$id_product_attribute] = array();
                                            foreach ($res as $k => $v)
                                            {
                                                $position_image = count($cacheProductImages[$p->id.'_'.$id_product_attribute]) + 1;
                                                $cacheProductImages[$p->id.'_'.$id_product_attribute][$position_image] = $v['id_image'];
                                                $legends[$p->id.'_'.$id_product_attribute][(int) $getIDlangByISO[$f['lang']]][$v['id_image']] = $v['legend'];
                                                $imgs[] = $_PS_BASE_URL_.getImgPath($p->id, $v['id_image'], _s('CAT_EXPORT_IMAGE_FORMAT'), 'jpg');
                                            }
                                            $field = join($exportConfig['valuesep'], $imgs);
                                            break;
                                        case 'urls_to_all_images_for_product':
                                            if ((int) $getIDlangByISO[$f['lang']] < 1)
                                            {
                                                exit(_l('You have to set the language in the mapping for the field:').' '._l('urls_to_all_images'));
                                            }
                                            $res = createQueriesCache('SELECT i.`cover`, i.`id_image`, il.`legend`, i.`position`
                                                    FROM `'._DB_PREFIX_.'image` i
                                                    LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (i.`id_image` = il.`id_image` AND il.`id_lang` = '.(int) $getIDlangByISO[$f['lang']].')
                                                    WHERE i.`id_product` = '.(int) $p->id.'
                                                    ORDER BY `position`');
                                            $imgs = $cacheProductImages[$p->id.'_'.$id_product_attribute] = array();
                                            foreach ($res as $k => $v)
                                            {
                                                $position_image = count($cacheProductImages[$p->id.'_'.$id_product_attribute]) + 1;
                                                $cacheProductImages[$p->id.'_'.$id_product_attribute][$position_image] = $v['id_image'];
                                                $legends[$p->id.'_'.$id_product_attribute][(int) $getIDlangByISO[$f['lang']]][$v['id_image']] = $v['legend'];

                                                $temp_url = $link->getImageLink($p->link_rewrite[(int) $getIDlangByISO[$f['lang']]], $v['id_image']);
                                                $imgs[] = $temp_url;
                                            }
                                            $field = join($exportConfig['valuesep'], $imgs);
                                            break;
                                        case 'link_to_product':
                                            $alias = $p->link_rewrite[$getIDlangByISO[$f['lang']]];
                                            $category = '';
                                            if ($p->id_category_default)
                                            {
                                                $category = Category::getLinkRewrite((int) $p->id_category_default, (int) $getIDlangByISO[$f['lang']]);
                                            }
                                            $force = (bool) Configuration::get('PS_REWRITING_SETTINGS');
                                            if (SCMS)
                                            {
                                                $field = $link->getProductLink($p->id, $alias, $category, $p->ean13, $getIDlangByISO[$f['lang']], (int) $selected_shops_id, 0, $force);
                                            }
                                            else
                                            {
                                                if (!defined('_PS_BASE_URL_'))
                                                {
                                                    define('_PS_BASE_URL_', Tools::getShopDomain(true));
                                                }
                                                if (version_compare(_PS_VERSION_, '1.6.0.0', '>='))
                                                {
                                                    $field = $link->getProductLink($p->id, $alias, $category, $p->ean13, $getIDlangByISO[$f['lang']], SCI::getConfigurationValue('PS_SHOP_DEFAULT'), 0, $force);
                                                }
                                                else
                                                {
                                                    $field = $link->getProductLink($p->id, $alias, $category, $p->ean13, $getIDlangByISO[$f['lang']], 0, 0, $force);
                                                }
                                            }
                                            break;
                                        case 'tags':
                                            $field = str_replace(',', $exportConfig['valuesep'], $p->getTags($getIDlangByISO[$f['lang']]));
                                            break;
                                        case 'id_product-id_attribute':
                                            $field = $p->id.($id_product_attribute > 0 ? '_'.$id_product_attribute : '');
                                            break;
                                        case 'id_product_attribute':
                                            $field = ($id_product_attribute > 0 ? $id_product_attribute : '');
                                            break;
                                        case 'stock_value':
                                            if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                                            {
                                                $qty = '';
                                                $no_scas = true;
                                                $hasCombi = $p->hasAttributes();
                                                if (SCAS)
                                                {
                                                    $type_advanced_stock_management = 1;
                                                    if ($p->advanced_stock_management == 1)
                                                    {
                                                        $type_advanced_stock_management = 2;
                                                        if (!StockAvailable::dependsOnStock((int) $p->id, (int) $selected_shops_id))
                                                        {
                                                            $type_advanced_stock_management = 3;
                                                        }
                                                    }

                                                    if ($type_advanced_stock_management == 2)
                                                    {
                                                        $qty = 0;
                                                        $no_scas = false;

                                                        $query_quantity = new DbQuery();
                                                        if ($hasCombi)
                                                        {
                                                            $query_quantity->select('st.id_product_attribute, st.physical_quantity');
                                                        }
                                                        else
                                                        {
                                                            $query_quantity->select('SUM(st.physical_quantity) as physical_quantity');
                                                        }
                                                        $query_quantity->from('stock', 'st');
                                                        $query_quantity->innerJoin('warehouse_shop', 'ws', "(ws.id_warehouse=st.id_warehouse AND ws.id_shop='".(int) $selected_shops_id."')");
                                                        $query_quantity->where('st.id_product = '.(int) $p->id);

                                                        if (!empty($id_product_attribute))
                                                        {
                                                            $query_quantity->where('st.id_product_attribute = '.(int) $id_product_attribute);
                                                        }
                                                        elseif ($hasCombi)
                                                        {
                                                            $query_quantity->where('st.id_product_attribute != 0');
                                                        }
                                                        else
                                                        {
                                                            $query_quantity->where('st.id_product_attribute = 0');
                                                        }

                                                        if (!empty($id_product_attribute))
                                                        {
                                                            $avanced_quantities = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($query_quantity);
                                                            $qty = $avanced_quantities['physical_quantity'];
                                                            $field = $qty * $p->getPrice(false, $id_product_attribute, 6, null, false, false);
                                                        }
                                                        elseif ($hasCombi)
                                                        {
                                                            $avanced_quantities = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($query_quantity);
                                                            foreach ($avanced_quantities as $avanced_quantity)
                                                            {
                                                                $qty = $avanced_quantity['physical_quantity'];
                                                                $id_pa = $avanced_quantity['id_product_attribute'];

                                                                $field += $qty * $p->getPrice(false, $id_pa, 6, null, false, false);
                                                            }
                                                        }
                                                        else
                                                        {
                                                            $avanced_quantities = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($query_quantity);
                                                            $qty = (int) $avanced_quantities['physical_quantity'];
                                                            $field = $qty * $p->getPrice(false, null, 6, null, false, false);
                                                        }
                                                    }
                                                    else
                                                    {
                                                        if (!empty($id_warehouse) && is_numeric($id_warehouse) && $id_warehouse > 0)
                                                        {
                                                            $no_scas = false;
                                                        }
                                                    }
                                                }

                                                if ($no_scas)
                                                {
                                                    if (!empty($id_product_attribute) || !$hasCombi)
                                                    {
                                                        $qty = (int) StockAvailable::getQuantityAvailableByProduct($p->id, ($id_product_attribute == 0 ? null : $id_product_attribute), (SCMS && $selected_shops_id > 0 ? (int) $selected_shops_id : null));
                                                        $field = $qty * $p->getPrice(false, ($id_product_attribute == 0 ? null : $id_product_attribute), 6, null, false, false);
                                                    }
                                                    else
                                                    {
                                                        $attrs = SCI::getAttributeCombinations($p, (int) $id_lang);
                                                        foreach ($attrs as $attr)
                                                        {
                                                            $id_pa = $attr['id_product_attribute'];
                                                            $qty = (int) StockAvailable::getQuantityAvailableByProduct($p->id, $id_pa, (SCMS && $selected_shops_id > 0 ? (int) $selected_shops_id : null));
                                                            $field += $qty * $p->getPrice(false, $id_pa, 6, null, false, false);
                                                        }
                                                    }
                                                }
                                            }
                                            else
                                            {
                                                if (!empty($id_product_attribute) || !$hasCombi)
                                                {
                                                    $qty = (int) $p->getQuantity($p->id, ($id_product_attribute == 0 ? null : $id_product_attribute));
                                                    $field = $qty * $p->getPrice(false, ($id_product_attribute == 0 ? null : $id_product_attribute), 6, null, false, false);
                                                }
                                                else
                                                {
                                                    $attrs = SCI::getAttributeCombinations($p, (int) $id_lang);
                                                    foreach ($attrs as $attr)
                                                    {
                                                        $id_pa = $attr['id_product_attribute'];
                                                        $qty = (int) $p->getQuantity($p->id, $id_pa);
                                                        $field += $qty * $p->getPrice(false, $id_pa, 6, null, false, false);
                                                    }
                                                }
                                            }
                                            break;
                                        case 'stock_value_with_reduction':
                                            if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                                            {
                                                $qty = '';
                                                $no_scas = true;
                                                if (SCAS)
                                                {
                                                    $type_advanced_stock_management = 1;
                                                    if ($p->advanced_stock_management == 1)
                                                    {
                                                        $type_advanced_stock_management = 2;
                                                        if (!StockAvailable::dependsOnStock((int) $p->id, (int) $selected_shops_id))
                                                        {
                                                            $type_advanced_stock_management = 3;
                                                        }
                                                    }

                                                    if ($type_advanced_stock_management == 2)
                                                    {
                                                        $qty = 0;
                                                        $no_scas = false;

                                                        $hasCombi = $p->hasAttributes();

                                                        $query_quantity = new DbQuery();
                                                        $query_quantity->select('SUM(st.physical_quantity) as physical_quantity');
                                                        $query_quantity->from('stock', 'st');
                                                        $query_quantity->innerJoin('warehouse_shop', 'ws', "(ws.id_warehouse=st.id_warehouse AND ws.id_shop='".(int) $selected_shops_id."')");
                                                        $query_quantity->where('st.id_product = '.(int) $p->id);

                                                        if (!empty($id_product_attribute))
                                                        {
                                                            $query_quantity->where('st.id_product_attribute = '.(int) $id_product_attribute);
                                                        }
                                                        elseif ($hasCombi)
                                                        {
                                                            $query_quantity->where('st.id_product_attribute != 0');
                                                        }
                                                        else
                                                        {
                                                            $query_quantity->where('st.id_product_attribute = 0');
                                                        }

                                                        $avanced_quantities = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($query_quantity);

                                                        if (!empty($avanced_quantities['physical_quantity']))
                                                        {
                                                            $qty = $avanced_quantities['physical_quantity'];
                                                        }
                                                    }
                                                    else
                                                    {
                                                        if (!empty($id_warehouse) && is_numeric($id_warehouse) && $id_warehouse > 0)
                                                        {
                                                            $no_scas = false;
                                                        }
                                                    }
                                                }

                                                if ($no_scas)
                                                {
                                                    $qty = (int) StockAvailable::getQuantityAvailableByProduct($p->id, ($id_product_attribute == 0 ? null : $id_product_attribute), (SCMS && $selected_shops_id > 0 ? (int) $selected_shops_id : null));
                                                }
                                            }
                                            else
                                            {
                                                $qty = (int) $p->getQuantity($p->id, ($id_product_attribute == 0 ? null : $id_product_attribute));
                                            }

                                            if ($qty != '')
                                            {
                                                $field = $qty * $p->getPrice(false, ($id_product_attribute == 0 ? null : $id_product_attribute));
                                            }
                                            else
                                            {
                                                $field = '';
                                            }
                                            break;
                                        case 'stock_value_wholesale':
                                            if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                                            {
                                                $qty = '';
                                                $no_scas = true;
                                                if (SCAS)
                                                {
                                                    $type_advanced_stock_management = 1;
                                                    if ($p->advanced_stock_management == 1)
                                                    {
                                                        $type_advanced_stock_management = 2;
                                                        if (!StockAvailable::dependsOnStock((int) $p->id, (int) $selected_shops_id))
                                                        {
                                                            $type_advanced_stock_management = 3;
                                                        }
                                                    }

                                                    if ($type_advanced_stock_management == 2)
                                                    {
                                                        $qty = 0;
                                                        $no_scas = false;

                                                        $hasCombi = $p->hasAttributes();

                                                        $query_quantity = new DbQuery();
                                                        $query_quantity->select('SUM(st.physical_quantity) as physical_quantity');
                                                        $query_quantity->from('stock', 'st');
                                                        $query_quantity->innerJoin('warehouse_shop', 'ws', "(ws.id_warehouse=st.id_warehouse AND ws.id_shop='".(int) $selected_shops_id."')");
                                                        $query_quantity->where('st.id_product = '.(int) $p->id);

                                                        if (!empty($id_product_attribute))
                                                        {
                                                            $query_quantity->where('st.id_product_attribute = '.(int) $id_product_attribute);
                                                        }
                                                        elseif ($hasCombi)
                                                        {
                                                            $query_quantity->where('st.id_product_attribute != 0');
                                                        }
                                                        else
                                                        {
                                                            $query_quantity->where('st.id_product_attribute = 0');
                                                        }

                                                        $avanced_quantities = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($query_quantity);

                                                        if (!empty($avanced_quantities['physical_quantity']))
                                                        {
                                                            $qty = (int) $avanced_quantities['physical_quantity'];
                                                        }
                                                    }
                                                    else
                                                    {
                                                        if (!empty($id_warehouse) && is_numeric($id_warehouse) && $id_warehouse > 0)
                                                        {
                                                            $no_scas = false;
                                                        }
                                                    }
                                                }

                                                if ($no_scas)
                                                {
                                                    $qty = (int) StockAvailable::getQuantityAvailableByProduct($p->id, ($id_product_attribute == 0 ? null : $id_product_attribute), (SCMS && $selected_shops_id > 0 ? (int) $selected_shops_id : null));
                                                }
                                            }
                                            else
                                            {
                                                $qty = (int) $p->getQuantity($p->id, ($id_product_attribute == 0 ? null : $id_product_attribute));
                                            }

                                            if ($qty != '')
                                            {
                                                $field = $qty * ($id_product_attribute == 0 ? (float) $p->wholesale_price : (float) $product_attribute['wholesale_price']);
                                            }
                                            else
                                            {
                                                $field = '';
                                            }
                                            break;
                                        case 'margin':
                                            $margin = 0;
                                            // WHOLESALE
                                            $wholesale_price = ($id_product_attribute == 0 ? $p->wholesale_price : $product_attribute['wholesale_price']);
                                            // PRICE
                                            $price = 0;
                                            if (SCMS)
                                            {
                                                $specific_price = array();
                                                $price = $p->priceCalculation((int) $selected_shops_id, $p->id, (int) $id_product_attribute, (int) SCI::getDefaultCountryId() /*$id_country*/ , 0 /*$id_state*/ , 0 /*$zipcode*/ , (int) Configuration::get('PS_CURRENCY_DEFAULT') /*$id_currency*/ ,
                                                        1 /*$id_group*/ , 1 /*$quantity*/ , 0 /* use tax */ , 6 /*$decimals*/ , 0 /*$only_reduc*/ , 1 /*$use_reduc*/ , 0 /*$with_ecotax*/ , $specific_price, 0 /*$use_group_reduction*/ ,
                                                        0 /*$id_customer*/ , 0 /*$use_customer_price*/ , 0 /*$id_cart*/ , 1 /*$real_quantity*/);
                                            }
                                            else
                                            {
                                                $price = $p->getPrice(false, ($id_product_attribute == 0 ? null : $id_product_attribute));
                                            }
                                            if ($exportConfig['shippingfee'] > 0 && $field <= $exportConfig['shippingfeefreefrom'])
                                            {
                                                $price += $exportConfig['shippingfee'];
                                            }
                                            // PRICE INC TAX
                                            $price_inc_tax = 0;
                                            if (SCMS)
                                            {
                                                $specific_price = array();
                                                $price_inc_tax = $p->priceCalculation((int) $selected_shops_id, $p->id, (int) $id_product_attribute, (int) SCI::getDefaultCountryId() /*$id_country*/ , 0 /*$id_state*/ , 0 /*$zipcode*/ , (int) Configuration::get('PS_CURRENCY_DEFAULT') /*$id_currency*/ ,
                                                        1 /*$id_group*/ , 1 /*$quantity*/ , 1 /* use tax */ , 6 /*$decimals*/ , 0 /*$only_reduc*/ , 1 /*$use_reduc*/ , 1 /*$with_ecotax*/ , $specific_price, 0 /*$use_group_reduction*/ ,
                                                        0 /*$id_customer*/ , 0 /*$use_customer_price*/ , 0 /*$id_cart*/ , 1 /*$real_quantity*/);
                                            }
                                            else
                                            {
                                                $price_inc_tax = ps_round($p->getPrice(true, ($id_product_attribute == 0 ? null : $id_product_attribute)), 6);
                                            }
                                            if ($exportConfig['shippingfee'] > 0 && $field <= $exportConfig['shippingfeefreefrom'])
                                            {
                                                $price_inc_tax += $exportConfig['shippingfee'];
                                            }

                                            // CALCUL
                                            if (!empty($marginMatrix_form))
                                            {
                                                $temp_form = $marginMatrix_form;
                                                $temp_form = str_replace('{price}', $price, $temp_form);
                                                $temp_form = str_replace('{wholesale_price}', $wholesale_price, $temp_form);
                                                $temp_form = str_replace('{price_inc_tax}', $price_inc_tax, $temp_form);

                                                $temp_form = '$margin='.$temp_form.';';
                                                @eval($temp_form); //  @ to avoid "/0" error message
                                            }
                                            $field = number_format($margin, (int) _s('CAT_EXPORT_PRICE_DECIMAL'), '.', '');
                                            break;
                                        case 'feature':
                                            $field = '';
                                            if (!empty($featuresListByLang[$getIDlangByISO[$f['lang']]]) && sc_array_key_exists($f['options'], $featuresListByLang[$getIDlangByISO[$f['lang']]]))
                                            {// has feature
                                                $field = $featuresListByLang[$getIDlangByISO[$f['lang']]][$f['options']]['value'];
                                            }
                                            break;
                                        case 'id_feature_value':
                                            $field = '';
                                            if (!empty($featuresListByLang[$getIDlangByISO[$f['lang']]]) && sc_array_key_exists($f['options'], $featuresListByLang[$getIDlangByISO[$f['lang']]]))
                                            {// has feature
                                                $field = $featuresListByLang[$getIDlangByISO[$f['lang']]][$f['options']]['id_feature_value'];
                                            }
                                            break;
                                        case 'attribute':
                                            if (!isset($product_attribute['attributeByGroup']))
                                            {
                                                break;
                                            }
                                            $field = '';
                                            if (isset($product_attribute['attributeByGroup'][$getIDlangByISO[$f['lang']]][$f['options']]))
                                            {// has attribute
                                                $field = $product_attribute['attributeByGroup'][$getIDlangByISO[$f['lang']]][$f['options']];
                                            }
                                            break;
                                        case 'attribute_color':
                                            if (!isset($product_attribute['attribute_color']))
                                            {
                                                break;
                                            }
                                            $field = '';
                                            if (isset($product_attribute['attribute_color']))
                                            {
                                                $field = Tools::strtoupper(trim($product_attribute['attribute_color'], ','));
                                            }
                                            break;
                                        case 'attribute_texture':
                                            $field = '';
                                            if (!empty($row['attribute_texture']))
                                            {
                                                $field = $row['attribute_texture'];
                                            }
                                            break;
                                        case 'name_with_attributes':
                                            $field = $p->name[$getIDlangByISO[$f['lang']]];
                                            break;
                                        case 'availability_message':
                                            if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                                            {
                                                $qty = StockAvailable::getQuantityAvailableByProduct($p->id, ($id_product_attribute == 0 ? null : $id_product_attribute), (SCMS && $selected_shops_id > 0 ? (int) $selected_shops_id : null));
                                            }
                                            else
                                            {
                                                $qty = $p->getQuantity($p->id, ($id_product_attribute == 0 ? null : $id_product_attribute));
                                            }
                                            if ($qty > 0)
                                            {
                                                $field = $p->available_now[$getIDlangByISO[$f['lang']]];
                                            }
                                            else
                                            {
                                                if (!empty($id_product_attribute) && SCI::getConfigurationValue('SC_DELIVERYDATE_INSTALLED') == '1')
                                                {
                                                    if (!empty($arrIdAvailableLater[$product_attribute['id_sc_available_later']][$getIDlangByISO[$f['lang']]]))
                                                    {
                                                        $field = $arrIdAvailableLater[$product_attribute['id_sc_available_later']][$getIDlangByISO[$f['lang']]];
                                                    }
                                                }
                                                else
                                                {
                                                    $field = $p->available_later[$getIDlangByISO[$f['lang']]];
                                                }
                                            }
                                            break;
                                        case 'available_later':
                                            if (!empty($id_product_attribute) && SCI::getConfigurationValue('SC_DELIVERYDATE_INSTALLED') == '1')
                                            {
                                                if (!empty($arrIdAvailableLater[$product_attribute['id_sc_available_later']][$getIDlangByISO[$f['lang']]]))
                                                {
                                                    $field = $arrIdAvailableLater[$product_attribute['id_sc_available_later']][$getIDlangByISO[$f['lang']]];
                                                }
                                            }
                                            else
                                            {
                                                $field = $p->available_later[$getIDlangByISO[$f['lang']]];
                                            }
                                            break;
                                        case 'reduction_price':case 'reduction_percent':case 'reduction_from':case 'reduction_to':case 'reduction_tax':
                                            $sql_specific_price = 'SELECT *
                                                                    FROM `'._DB_PREFIX_.'specific_price`
                                                                    WHERE id_product = '.(int) $p->id."
                                                                         AND (`from` <= '".date('Y-m-d H:i:s')."' OR `from`='0000-00-00 00:00:00')
                                                                         AND (`to` >= '".date('Y-m-d H:i:s')."' OR `to`='0000-00-00 00:00:00')
                                                                         ".(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? ' AND ( id_shop = '.(int) $selected_shops_id.' OR id_shop = 0 ) ' : '').'
                                                                         '.(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? 'AND id_product_attribute = 0 ' : '').'
                                                                         '.(version_compare(_PS_VERSION_, '1.5.0.2', '>=') ? 'AND id_cart = 0 ' : '').'
                                                                     ORDER BY `id_shop` DESC,`to` DESC, id_specific_price ASC';
                                            $res = Db::getInstance()->getRow($sql_specific_price);
                                            if (!empty($res))
                                            {
                                                if ($f['name'] == 'reduction_from')
                                                {
                                                    if ($res['from'] != '0000-00-00 00:00:00')
                                                    {
                                                        $field = $res['from'];
                                                    }
                                                    else
                                                    {
                                                        $field = '1989-01-01 00:00:00';
                                                    }
                                                }
                                                if ($f['name'] == 'reduction_to')
                                                {
                                                    if ($res['to'] != '0000-00-00 00:00:00')
                                                    {
                                                        $field = $res['to'];
                                                    }
                                                    else
                                                    {
                                                        $field = '2077-12-31 23:59:59';
                                                    }
                                                }
                                                if ($f['name'] == 'reduction_price' && $res['reduction_type'] == 'amount')
                                                {
                                                    $field = number_format($res['reduction'], (int) _s('CAT_EXPORT_PRICE_DECIMAL'), '.', '');
                                                }
                                                if ($f['name'] == 'reduction_percent' && $res['reduction_type'] == 'percentage')
                                                {
                                                    $field = number_format($res['reduction'] * 100, (int) _s('CAT_EXPORT_PRICE_DECIMAL'), '.', '');
                                                }
                                                if ($f['name'] == 'reduction_tax')
                                                {
                                                    $field = (int) $res['reduction_tax'];
                                                }
                                            }
                                            $field = $field;
                                            break;
                                        case 'id_shop_list':
                                            $tmp_shop_list = Product::getShopsByProduct($p->id);
                                            $tmp_shop_list = array_column($tmp_shop_list, 'id_shop');
                                            $field = implode($exportConfig['valuesep'], $tmp_shop_list);
                                            break;
                                        case 'attachments':
                                            if (isset($attachments_array)
                                                && !empty($attachments_array)
                                                && array_key_exists($p->id, $attachments_array))
                                            {
                                                $field = array();
                                                foreach ($attachments_array[$p->id] as $attachment)
                                                {
                                                    $field[] = (string) $attachment['file'];
                                                }
                                                $field = implode($exportConfig['valuesep'], $field);
                                            }
                                            break;
                                        case 'attachments_url':
                                            if (isset($attachments_array)
                                                && !empty($attachments_array)
                                                && array_key_exists($p->id, $attachments_array))
                                            {
                                                $field = array();
                                                foreach ($attachments_array[$p->id] as $attachment)
                                                {
                                                    $field[] = (string) $link->getPageLink('attachment', true, null, 'id_attachment='.(int) $attachment['id_attachment']);
                                                }
                                                $field = implode($exportConfig['valuesep'], $field);
                                            }
                                            break;
                                        case 'margin':
                                            $field = 'test margin';
                                            break;
                                        case 'link_to_combination':
                                            if (!empty($id_product_attribute))
                                            {
                                                $alias = $p->link_rewrite[$getIDlangByISO[$f['lang']]];
                                                $link_product_base = null;
                                                $category = '';
                                                if ($p->id_category_default)
                                                {
                                                    $category = Category::getLinkRewrite((int) $p->id_category_default, (int) $getIDlangByISO[$f['lang']]);
                                                }
                                                $force = (bool) Configuration::get('PS_REWRITING_SETTINGS');
                                                if (SCMS)
                                                {
                                                    $link_product_base = $link->getProductLink($p->id, $alias, $category, $p->ean13, $getIDlangByISO[$f['lang']], (int) $selected_shops_id, (version_compare(_PS_VERSION_, '1.7.0.0', '>=') ? (int) $id_product_attribute : 0), $force);
                                                }
                                                else
                                                {
                                                    if (!defined('_PS_BASE_URL_'))
                                                    {
                                                        define('_PS_BASE_URL_', Tools::getShopDomain(true));
                                                    }
                                                    if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                                                    {
                                                        $link_product_base = $link->getProductLink($p->id, $alias, $category, $p->ean13, $getIDlangByISO[$f['lang']], SCI::getConfigurationValue('PS_SHOP_DEFAULT'), (version_compare(_PS_VERSION_, '1.7.0.0', '>=') ? (int) $id_product_attribute : 0), $force);
                                                    }
                                                    else
                                                    {
                                                        $link_product_base = $link->getProductLink($p->id, $alias, $category, $p->ean13, $getIDlangByISO[$f['lang']], 0, (version_compare(_PS_VERSION_, '1.7.0.0', '>=') ? (int) $id_product_attribute : 0), $force);
                                                    }
                                                }
                                                if (version_compare(_PS_VERSION_, '1.7.0.0', '>='))
                                                {
                                                    $field = $link_product_base;
                                                }
                                                else
                                                {
                                                    if (!empty($link_product_base) && array_key_exists((int) $getIDlangByISO[$f['lang']], $product_attribute['attributes']))
                                                    {
                                                        $combination_link = array();
                                                        $combination_detail = $product_attribute['attributes'][(int) $getIDlangByISO[$f['lang']]];
                                                        foreach ($combination_detail as $combination)
                                                        {
                                                            $tmp = array(
                                                                $combination[2],
                                                                $combination[0],
                                                                $combination[1],
                                                            );
                                                            $combination_link[$combination[6]] = Tools::link_rewrite(implode('-', $tmp));
                                                        }
                                                        ksort($combination_link);
                                                        $field = $link_product_base.'#/'.implode('/', $combination_link);
                                                    }
                                                }
                                            }
                                            break;
                                        case 'out_of_stock':
                                            if (version_compare(_PS_VERSION_, '1.5.0.2', '>=') && array_key_exists($p->id, $oos_array))
                                            {
                                                $field = $oos_array[$p->id][(int) $id_product_attribute];
                                            }
                                            else
                                            {
                                                $field = $p->{$f['name']};
                                            }
                                            break;
                                        case 'product_pack':
                                            if (isset($pack_array)
                                                && !empty($pack_array)
                                                && array_key_exists($p->id, $pack_array))
                                            {
                                                $field = (string) implode($exportConfig['valuesep'], $pack_array[$p->id]);
                                            }
                                            break;
                                        default:
                                            if (property_exists('Product', $f['name']))
                                            {
                                                $field = $p->{$f['name']};
                                            }
                                            break;
                                    }// END SWITCH
                                }
                            }

                            if ($f['name'] == 'wholesale_price'
                                    || $f['name'] == 'ecotax'
                                    || $f['name'] == 'priceinctax'
                                    || $f['name'] == 'priceexctax'
                                    || $f['name'] == 'price_inctax_without_reduction'
                                    || $f['name'] == 'price_exctax_without_reduction'
                                    || $f['name'] == 'impact'
                                    || $f['name'] == 'priceinctaxwithshipping'
                                    || $f['name'] == 'productshippingcost'
                                    || $f['name'] == 'additional_shipping_cost'
                                    || $f['name'] == 'unit'
                                    || $f['name'] == 'margin'
                            ) {
                                $field = number_format($field, (int) _s('CAT_EXPORT_PRICE_DECIMAL'), '.', '');
                            }

                            if ($f['name'] == 'description' || $f['name'] == 'description_short')
                            {
                                $field = str_replace("\r\n", '', $field);
                                $field = str_replace("\n", '', $field);
                            }

                            if ($f['modifications'] != '')
                            {
                                $tasks = explode('&&&', $f['modifications']);
                                if (in_array('nohtml', $tasks) || in_array('NOHTML', $tasks))
                                {
                                    $tasks[] = 'strip_tags';
                                    $tasks[] = 'html_entity_decode';
                                }
                                foreach ($tasks as $t)
                                {
                                    if ($t != '' && (sc_in_array($t, array('strip_tags', 'strtolower', 'strtoupper', 'html_entity_decode'), 'catWinExportProcess_tasksfunctions') || substr($t, 0, 1) == '='))
                                    {
                                        if ($t == 'html_entity_decode')
                                        {
                                            $field = html_entity_decode($field, ENT_QUOTES, 'UTF-8');
                                        }
                                        elseif (substr($t, 0, 1) == '=')
                                        {
                                            if (is_numeric($field))
                                            {
                                                if (strpos($t, '(x)'))
                                                {
                                                    $t = str_replace(array("'(x)'", '"(x)"'), (is_int($field) ? '(int)$field' : '(float)$field'), $t);
                                                }
                                                else
                                                {
                                                    $t = str_replace('x', (is_int($field) ? (int) $field : (float) $field), $t);
                                                }
                                                eval('$field='.substr($t, 1, 10000000).';');
                                            }
                                            else
                                            {
                                                if (strpos($t, '(x)'))
                                                {
                                                    $t = str_replace("'(x)'", '$field', $t);
                                                    $t = str_replace('"(x)"', '$field', $t);
                                                }
                                                eval('$field='.substr($t, 1, 10000000).';');
                                            }
                                        }
                                        else
                                        {
                                            eval('$field='.$t.'($field);');
                                        }
                                    }
                                }
                            }

                            if ($exportConfig['enclosedby'] != '')
                            {
                                $field = str_replace('"', '""', $field);
                            }

                            $linecontent .= $exportConfig['enclosedby'].$field.$exportConfig['enclosedby'].$exportConfig['fieldsep'];
                        } // END FOR EACH MAPPING

                        $linecontent = substr($linecontent, 0, -1 * strlen($exportConfig['fieldsep']));

                        $authorized = true;
                        if (!$exportConfig['exportoutofstock'] && SCAS)
                        {
                            if ($quantity_SCAS <= 0)
                            {
                                $authorized = false;
                            }
                        }
                        if (strpos($linecontent, '_DONOTEXPORT_') !== false)
                        {
                            $authorized = false;
                        }
                        if ($authorized)
                        {
                            if ((int) $exportConfig['iso'])
                            {
                                $linecontent = utf8_decode($linecontent);
                            }
                            fwrite($fp, $linecontent."\n");
                        }
                        else
                        {
                            if (!empty($sc_export['id_sc_export']))
                            {
                                $sql = 'SELECT id_sc_export
                                            FROM '._DB_PREFIX_."sc_export_product
                                             WHERE
                                                 id_sc_export='".(int) $sc_export['id_sc_export']."'
                                                 AND id_product='".(int) $idp['id_product']."'
                                                 AND id_product_attribute='".(int) $id_product_attribute."'";
                                $exist = Db::getInstance()->ExecuteS($sql);
                                if (!empty($exist[0]['id_sc_export']))
                                {
                                    $sql = '
                                    UPDATE '._DB_PREFIX_."sc_export_product SET exported='0'
                                    WHERE
                                                 id_sc_export='".(int) $sc_export['id_sc_export']."'
                                                 AND id_product='".(int) $idp['id_product']."'
                                                 AND id_product_attribute='".(int) $id_product_attribute."'";
                                    Db::getInstance()->Execute($sql);
                                }
                                else
                                {
                                    $sql = '
                                    INSERT INTO '._DB_PREFIX_."sc_export_product (id_sc_export, id_product, id_product_attribute,exported)
                                    VALUES ('".(int) $sc_export['id_sc_export']."','".(int) $idp['id_product']."','".(int) $id_product_attribute."','0')";
                                    Db::getInstance()->Execute($sql);
                                }
                            }
                            continue;
                        }

                        if (!empty($sc_export['id_sc_export']))
                        {
                            $sql = 'UPDATE '._DB_PREFIX_."sc_export_product SET handled=1 WHERE id_sc_export='".(int) $sc_export['id_sc_export']."'
                                    AND id_product='".(int) $idp['id_product']."'
                                    AND id_product_attribute='".(int) $id_product_attribute."'";
                            Db::getInstance()->Execute($sql);
                        }
                        ++$linecountreal;
                        if (($AUTO_EXPORT || ($CRON && $CRONVERSION >= 2)) && $linecount == $export_limit)
                        {
                            break;
                        }
                    }
                } // END FOR EACH COMBINATION
                if (($AUTO_EXPORT || ($CRON && $CRONVERSION >= 2)) && $linecount == $export_limit)
                {
                    break;
                }
            } // END FOR EACH PRODUCT
            if ($STOP_SCRIPT)
            {
                break;
            }

            if (!(($AUTO_EXPORT || ($CRON && $CRONVERSION >= 2)) && $ALREADY_EXPORTING))
            {
                fclose($fp);
            }
            $exportConfig['lastexportdate'] = date('Y-m-d H:i:s');
            writeExportConfigXML($filename);
            if (file_exists(SC_CSV_EXPORT_DIR.$exportConfig['exportfilename']))
            {
                $message_return = '';
                $nb_lines = $linecountreal;
                if ($AUTO_EXPORT || ($CRON && $CRONVERSION >= 2))
                {
                    $sql = "SELECT DISTINCT(CONCAT(id_product,'_', id_product_attribute)) FROM "._DB_PREFIX_."sc_export_product WHERE id_sc_export = '".(int) $sc_export['id_sc_export']."' AND exported=1 AND handled=1";
                    $temp_nb = Db::getInstance()->ExecuteS($sql);
                    $nb_lines = count($temp_nb);
                }

                if (SC_INSTALL_MODE == 0)
                {
                    $message_return = _l('Export:').' <a href="'.(isset($websiteURL) ? $websiteURL : '').__PS_BASE_URI__.'export/'.$exportConfig['exportfilename'].'?'.time().'" target="_blank">'.(isset($websiteURL) ? $websiteURL : '').'/export/'.$exportConfig['exportfilename'].'</a> - '.$nb_lines.' '._l('lines').' - '.date('Y-m-d H:i:s');
                }
                else
                {
                    $message_return = _l('Export:').' <a href="'.(isset($websiteURL) ? $websiteURL : '').__PS_BASE_URI__.'modules/'.SC_MODULE_FOLDER_NAME.'/export/'.$exportConfig['exportfilename'].'?'.time().'" target="_blank">/modules/'.SC_MODULE_FOLDER_NAME.'/export/'.$exportConfig['exportfilename'].'</a> - '.$nb_lines.' '._l('lines').' - '.date('Y-m-d H:i:s');
                }

                if ($AUTO_EXPORT)
                {
                    if ($linecount < $export_limit)
                    {
                        $sql = 'UPDATE '._DB_PREFIX_."sc_export SET exporting = 0 WHERE name = '".pSQL($auto_filename)."'";
                        Db::getInstance()->Execute($sql);
                        echo json_encode(array(
                                'type' => 'success',
                                'stop' => 1,
                                'content' => '<strong style="color: #266e00;">'._l('Export finished.').'</strong><br/>'.$message_return,
                                'filename' => $auto_filename,
                                'debug' => $debug,
                                'first_interval' => $first_interval,
                        ));
                    }
                    else
                    {
                        echo json_encode(array(
                                'type' => 'success',
                                'content' => $message_return,
                                'filename' => $auto_filename,
                                'debug' => $debug,
                                'first_interval' => $first_interval,
                        ));
                    }
                }
                else
                {
                    if (($CRON && $CRONVERSION >= 2))
                    {
                        $sql = 'UPDATE '._DB_PREFIX_."sc_export SET exporting = 0 WHERE name = '".pSQL($auto_filename)."'";
                        Db::getInstance()->Execute($sql);
                        if ($linecount < $export_limit)
                        {
                            echo _l('Export finished.').'<br/>';
                        }
                    }
                    echo $message_return;
                }

                if (!$CRON && !$AUTO_EXPORT)
                {
                    ?>
                    <script type="text/javascript">
                        dhtmlx.message({text:'<?php echo _l('File created')._l(':').' '.$exportConfig['exportfilename']; ?>',type:'info'});
                    </script>
                    <?php
                }
            }
            else
            {
                if ($AUTO_EXPORT)
                {
                    echo json_encode(array(
                            'type' => 'error',
                            'stop' => 1,
                            'content' => '<strong style="color: #831f1f;">'._l('File NOT created', 1).'</span>',
                            'filename' => $auto_filename,
                            'debug' => $debug,
                            'first_interval' => $first_interval,
                    ));
                }
                else
                {
                    echo _l('File NOT created');
                }
            }
            addToHistory('catalog_export', 'export', '', '', '', '', 'Script: '.$filename.'<br/>Exported file: '.$exportConfig['exportfilename'].'<br/>'.$linecount.' '._l('lines'), '');
            break;
        }
