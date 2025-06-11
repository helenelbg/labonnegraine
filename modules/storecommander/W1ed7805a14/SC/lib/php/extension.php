<?php

if (isset($_GET['setextension']))
{
    $local_settings['CORE_USE_EXTENSIONS']['value'] = (int) Tools::getValue('setextension');
    saveSettings();
}

require_once SC_DIR.'lib/php/extension/extension_pmcachemanager.php';

class SC_Ext
{
    public static function readCustomGridsConfigXML($config, $extData = false)
    {
        global $grids_products_conf;
        if (!SC_TOOLS || !file_exists(SC_TOOLS_DIR.'grids_products_conf.xml') || !_s('CORE_USE_EXTENSIONS'))
        {
            return false;
        }

        if (!$grids_products_conf)
        {
            $extConvert = new ExtensionConvert();
            $extConvert->convert('products');
            if (!$grids_products_conf = simplexml_load_file(SC_TOOLS_DIR.'grids_products_conf.xml'))
            {
                return false;
            }
        }

        switch ($config){
            case 'permissions':
                global $permissions_list;
                $commun_list = array('grid_light', 'grid_large', 'grid_delivery', 'grid_price', 'grid_discount', 'grid_discount_2', 'grid_seo', 'grid_reference', 'grid_description');

                foreach ($grids_products_conf->grids->grid as $grid)
                {
                    if (!sc_in_array((string) $grid->name, $commun_list, 'extension_communlist'))
                    {
                        $permissions_list['GRI_CAT_VIEW_GRID_EXT_'.$grid->name] = array('id' => 'GRI_CAT_VIEW_GRID_EXT_'.$grid->name, 'section1' => 'Grid', 'section2' => 'Catalog', 'name' => 'Product grid:'.' '.(string) $grid->text->fr, 'description' => '', 'default_admin' => 1, 'default_value' => 1);
                    }
                }
                break;
            case 'gridnames':
                foreach ($grids_products_conf->grids->grid as $grid)
                {
                    $add = true;

                    if ($grid->name == 'grid_light' && !_r('GRI_CAT_VIEW_GRID_LIGHT'))
                    {
                        $add = false;
                    }
                    elseif ($grid->name == 'grid_large' && !_r('GRI_CAT_VIEW_GRID_LARGE'))
                    {
                        $add = false;
                    }
                    elseif ($grid->name == 'grid_pack' && !_r('GRI_CAT_VIEW_GRID_PACK'))
                    {
                        $add = false;
                    }
                    elseif ($grid->name == 'grid_delivery' && !_r('GRI_CAT_VIEW_GRID_DELIVERY'))
                    {
                        $add = false;
                    }
                    elseif ($grid->name == 'grid_price' && !_r('GRI_CAT_VIEW_GRID_PRICE'))
                    {
                        $add = false;
                    }
                    elseif ($grid->name == 'grid_discount' && !_r('GRI_CAT_VIEW_GRID_DISCOUNT'))
                    {
                        $add = false;
                    }
                    elseif ($grid->name == 'grid_discount_2' && !_r('GRI_CAT_VIEW_GRID_DISCOUNT'))
                    {
                        $add = false;
                    }
                    elseif ($grid->name == 'grid_seo' && !_r('GRI_CAT_VIEW_GRID_SEO'))
                    {
                        $add = false;
                    }
                    elseif ($grid->name == 'grid_reference' && !_r('GRI_CAT_VIEW_GRID_REFERENCE'))
                    {
                        $add = false;
                    }
                    elseif ($grid->name == 'grid_description' && !_r('GRI_CAT_VIEW_GRID_DESCRIPTION'))
                    {
                        $add = false;
                    }
                    elseif (!_r('GRI_CAT_VIEW_GRID_EXT_'.$grid->name))
                    {
                        $add = false;
                    }

                    if ($add == true)
                    {
                        echo "gridnames['".((string) $grid->name)."']='".str_replace("'", "\'", (string) $grid->text->fr)."';";
                    }
                }
                break;
            case 'toolbar':
                $lines = array();
                foreach ($grids_products_conf->grids->grid as $grid)
                {
                    $lines[] = "['".((string) $grid->name)."', 'obj', gridnames['".((string) $grid->name)."'], '']";
                }
                echo(count($lines) ? ',' : '').join(',', $lines);
                break;
            case 'gridConfig':
                global $grids;
                foreach ($grids_products_conf->grids->grid as $grid)
                {
                    $grids[((string) $grid->name)] = ((string) $grid->value);
                }
                break;
            case 'colSettings':
                global $colSettings,$arrManufacturers,$arrSuppliers;
                foreach ($grids_products_conf->fields->field as $field)
                {
                    if ((string) $field->filter == 'na')
                    {
                        $field->filter = '';
                    }
                    $footer = '';
                    $options = '';
                    if (!empty($colSettings[((string) $field->name)]))
                    {
                        $footer = $colSettings[((string) $field->name)]['footer'];
                        $options = $colSettings[((string) $field->name)]['options'];
                    }
                    if (trim((string) $field->footer) != '')
                    {
                        $footer = (string) $field->footer;
                    }
                    if (trim((string) $field->options) != '')
                    {
                        $options = eval($field->options);
                    }
                    else
                    {
                        $answertype = ((string) $field->answertype);
                        if ($answertype != '')
                        {
                            if ($answertype == 'YESNO')
                            {
                                $options = array(0 => _l('No'), 1 => _l('Yes'));
                            }
                        }
                    }
                    $format = '';
                    if (!empty($field->format))
                    {
                        $format = $field->format;
                    }
                    elseif (!empty($colSettings[(string) $field->name]['format']))
                    {
                        $format = $colSettings[(string) $field->name]['format'];
                    }
                    $colSettings[((string) $field->name)] = array('text' => str_replace("'", "\'", (string) $field->text->fr),
                                                                                                            'width' => ((string) $field->width),
                                                                                                            'align' => ((string) $field->align),
                                                                                                            'type' => ((string) $field->celltype),
                                                                                                            'sort' => ((string) $field->sort),
                                                                                                            'color' => ((string) $field->color),
                                                                                                            'format' => (string) $format, //((string) $field->format),
                                                                                                            'filter' => ((string) $field->filter),
                                                                                                            'footer' => $footer,
                                                                                                            'options' => $options, );
                    $buildDefaultValue = ((string) $field->buildDefaultValue);
                    if ($buildDefaultValue != '')
                    {
                        $colSettings[((string) $field->name)]['buildDefaultValue'] = $buildDefaultValue;
                    }
                }
                break;
            case 'updateSettings':
                global $fields,$fields_lang,$forceUpdateCombinations,$idproduct,$id_lang,$multiStoresFields,$cols;

                foreach ($grids_products_conf->fields->field as $field)
                {
                    if ((isset($cols) && sc_in_array((string) $field->name, $cols, 'cols')) || !isset($cols))
                    {
                        if (((string) $field->table) == 'product')
                        {
                            $fields[] = ((string) $field->name);
                        }
                        elseif (((string) $field->table) == 'product_lang')
                        {
                            $fields_lang[] = ((string) $field->name);
                        }
                        elseif (((string) $field->table) == 'product_shop')
                        {
                            if (isset($multiStoresFields))
                            {
                                $multiStoresFields[] = ((string) $field->name);
                            }
                        }
                        elseif (((string) $field->table) == 'special' && (trim((string) $field->onUpdate) != ''))
                        {
                            // special actions
                            eval((string) $field->onUpdate);
                        }
                        if ((int) $field->forceUpdateCombinationsGrid == 1)
                        {
                            $forceUpdateCombinations[] = ((string) $field->name);
                        }
                    }
                }
                break;
            case 'rowData':
                global $fields,$fields_lang,$col,$cols,$idproduct,$id_lang,$prodrow,$has_combination,$prodWithAttributes;
                foreach ($grids_products_conf->fields->field as $field)
                {
                    if (sc_in_array((string) $field->name, $cols, 'cols') && (trim((string) $field->rowData) != ''))
                    {
                        eval((string) $field->rowData);
                    }
                }
                    break;
            case 'SQLSelectDataSelect':
                global $sql,$view,$cols,$id_lang;
                foreach ($grids_products_conf->fields->field as $field)
                {
                    if (sc_in_array((string) $field->name, $cols, 'cols') && (trim((string) $field->SQLSelectDataSelect) != ''))
                    {
                        $sql .= eval((string) $field->SQLSelectDataSelect);
                    }
                }
                    break;
            case 'SQLSelectDataLeftJoin':
                global $sql,$view,$cols,$id_lang;
                foreach ($grids_products_conf->fields->field as $field)
                {
                    if (sc_in_array((string) $field->name, $cols, 'cols') && (trim((string) $field->SQLSelectDataLeftJoin) != ''))
                    {
                        $sql .= eval((string) $field->SQLSelectDataLeftJoin);
                    }
                }
                    break;
            case 'SQLSelectDataWhere':
                global $sql,$view,$cols,$id_lang;
                foreach ($grids_products_conf->fields->field as $field)
                {
                    if (sc_in_array((string) $field->name, $cols, 'cols') && (trim((string) $field->SQLSelectDataWhere) != ''))
                    {
                        $sql .= eval((string) $field->SQLSelectDataWhere);
                    }
                }
                    break;
            case 'onEditCell':
                foreach ($grids_products_conf->grids->grid as $grid)
                {
                    echo (string) $grid->onEditCell;
                }
                foreach ($grids_products_conf->fields->field as $field)
                {
                    echo (string) $field->onEditCell;
                }
                break;
            case 'onAfterUpdate':
                foreach ($grids_products_conf->grids->grid as $grid)
                {
                    echo (string) $grid->onAfterUpdate;
                }
                foreach ($grids_products_conf->fields->field as $field)
                {
                    echo (string) $field->onAfterUpdate;
                }
                break;
            case 'onAfterUpdateSQL':
                global $idproduct, $id_product, $id_lang;
                $idproduct = $id_product; // for compatibility with old versions
                foreach ($grids_products_conf->fields->field as $field)
                {
                    if ((trim((string) $field->onAfterUpdateSQL) != ''))
                    {
                        eval((string) $field->onAfterUpdateSQL);
                    }
                }
                break;
            case 'onBeforeUpdateSQL':
                global $idproduct,$id_product,$id_product_attribute,$id_lang,$fields,$fields_lang,$fieldsWithHTML;
                $idproduct = $id_product; // for compatibility with old versions
                foreach ($grids_products_conf->fields->field as $field)
                {
                    if ((trim((string) $field->onBeforeUpdateSQL) != ''))
                    {
                        eval((string) $field->onBeforeUpdateSQL);
                    }
                }
                break;
            case 'onBeforeUpdate':
                foreach ($grids_products_conf->grids->grid as $grid)
                {
                    echo (string) $grid->onBeforeUpdate;
                }
                foreach ($grids_products_conf->fields->field as $field)
                {
                    echo (string) $field->onBeforeUpdate;
                }
                break;
            case 'gridUserData':
                foreach ($grids_products_conf->grids->grid as $grid)
                {
                    if ((trim((string) $grid->gridUserData) != ''))
                    {
                        eval((string) $grid->gridUserData);
                    }
                }
                break;
            case 'rowUserData':
                global $cols;
                foreach ($grids_products_conf->fields->field as $field)
                {
                    if (is_array($cols) && sc_in_array((string) $field->name, $cols, 'cols'))
                    {
                        if ((trim((string) $field->rowUserData) != ''))
                        {
                            eval((string) $field->rowUserData);
                        }
                    }
                }
                break;
            case 'extraVars':
                global $action,$extraVars,$cols;
                foreach ($grids_products_conf->grids->grid as $grid)
                {
                    if ((trim((string) $grid->extraVars) != ''))
                    {
                        eval((string) $grid->extraVars);
                    }
                }
                foreach ($grids_products_conf->fields->field as $field)
                {
                    if ((trim((string) $field->extraVars) != ''))
                    {
                        eval((string) $field->extraVars);
                    }
                }
                break;
            case 'afterGetRows':
                foreach ($grids_products_conf->fields->field as $field)
                {
                    if (!empty($field->afterGetRows))
                    {
                        eval((string) $field->afterGetRows);
                    }
                }
                break;
        }
    }

    public static function readCustomCombinationsGridConfigXML($config, $extData = false)
    {
        global $grids_combinations_conf;
        $setextension = Tools::getValue('setextension', 1);
        if (!SC_TOOLS || !file_exists(SC_TOOLS_DIR.'grids_combinations_conf.xml') || !_s('CORE_USE_EXTENSIONS'))
        {
            return false;
        }
        if (!$grids_combinations_conf)
        {
            $ext = new ExtensionConvert();
            $ext->convert('combinations');
            if (!$grids_combinations_conf = simplexml_load_file(SC_TOOLS_DIR.'grids_combinations_conf.xml'))
            {
                return false;
            }
        }

        switch ($config){
            case 'gridConfig':
                global $sourceGridFormat;
                if (!empty($grids_combinations_conf->grids->grid->value))
                {
                    $sourceGridFormat = (string) $grids_combinations_conf->grids->grid->value;
                }
                break;
            case 'definition':
                global $combArray,$combinaison,$cols,$all_cols;
                foreach ($grids_combinations_conf->fields->field as $field)
                {
                    $definition = (string) $field->definition;
                    if (empty($definition) && in_array(((string) $field->table), array('product_attribute', 'product_attribute_shop', 'product', 'product_shop', 'product_supplier')))
                    {
                        $field->definition = ' return $combArray[$combinaison["id_product_attribute"]]["'.(string) $field->name.'"] = $combinaison["'.(string) $field->name.'"];';
                    }
                    if (sc_in_array((string) $field->name, $all_cols, 'extension_allcols'))
                    {
                        eval((string) $field->definition);
                    }
                }
                break;
            case 'colSettings':
                global $colSettings;
                foreach ($grids_combinations_conf->fields->field as $field)
                {
                    if ((string) $field->filter == 'na')
                    {
                        $field->filter = '';
                    }
                    $footer = '';
                    $options = '';
                    if (!empty($colSettings[((string) $field->name)]))
                    {
                        $footer = $colSettings[((string) $field->name)]['footer'];
                        $options = $colSettings[((string) $field->name)]['options'];
                    }
                    if (trim((string) $field->footer) != '')
                    {
                        $footer = (string) $field->footer;
                    }
                    if (trim((string) $field->options) != '')
                    {
                        $options = eval((string) $field->options);
                    }
                    else
                    {
                        $answertype = ((string) $field->answertype);
                        if ($answertype != '')
                        {
                            if ($answertype == 'YESNO')
                            {
                                $options = array(0 => _l('No'), 1 => _l('Yes'));
                            }
                        }
                    }
                    $colSettings[((string) $field->name)] = array('text' => str_replace("'", "\'", (string) $field->text->fr),
                            'width' => ((string) $field->width),
                            'align' => ((string) $field->align),
                            'type' => ((string) $field->celltype),
                            'sort' => ((string) $field->sort),
                            'color' => ((string) $field->color),
                            'format' => $colSettings[((string) $field->name)]['format'], //((string) $field->format),
                            'filter' => ((string) $field->filter),
                            'footer' => $footer,
                            'options' => $options, );
                    $buildDefaultValue = ((string) $field->buildDefaultValue);
                    if ($buildDefaultValue != '')
                    {
                        $colSettings[((string) $field->name)]['buildDefaultValue'] = $buildDefaultValue;
                    }
                }
                break;
            case 'updateSettings':
                global $fields,$shopfields;
                foreach ($grids_combinations_conf->fields->field as $field)
                {
                    if (((string) $field->table) == 'product_attribute')
                    {
                        if (isset($fields))
                        {
                            $fields[] = ((string) $field->name);
                        }
                    }
                    elseif (((string) $field->table) == 'product_attribute_shop')
                    {
                        if (isset($shopfields))
                        {
                            $shopfields[] = ((string) $field->name);
                        }
                    }
                }
                break;
            case 'onEditCell':
                echo (string) $grids_combinations_conf->grids->grid->onEditCell;
                foreach ($grids_combinations_conf->fields->field as $field)
                {
                    echo (string) $field->onEditCell;
                }
                break;
            case 'onAfterUpdate':
                echo (string) $grids_combinations_conf->grids->grid->onAfterUpdate;
                foreach ($grids_combinations_conf->fields->field as $field)
                {
                    echo (string) $field->onAfterUpdate;
                }
                break;
            case 'onAfterUpdateSQL':
                global $idproduct,$id_product,$id_product_attribute, $id_lang;
                foreach ($grids_combinations_conf->fields->field as $field)
                {
                    eval((string) $field->onAfterUpdateSQL);
                }
                break;
            case 'onBeforeUpdate':
                echo (string) $grids_combinations_conf->grids->grid->onBeforeUpdate;
                foreach ($grids_combinations_conf->fields->field as $field)
                {
                    echo (string) $field->onBeforeUpdate;
                }
                break;
            case 'gridUserData':
                eval((string) $grids_combinations_conf->grids->grid->gridUserData);
                break;
            case 'rowUserData':
                global $cols,$all_cols;
                foreach ($grids_combinations_conf->fields->field as $field)
                {
                    if (sc_in_array((string) $field->name, $all_cols, 'extension_allcols'))
                    {
                        eval((string) $field->rowUserData);
                    }
                }
                break;
            case 'extraVars':
                global $action,$extraVars,$cols,$all_cols;
                eval((string) $grids_combinations_conf->grids->grid->extraVars);
                foreach ($grids_combinations_conf->fields->field as $field)
                {
                    eval((string) $field->extraVars);
                }
                break;
            case 'SQLSelectDataSelect':
                global $sql,$view,$cols,$all_cols, $id_lang;
                foreach ($grids_combinations_conf->fields->field as $field)
                {
                    if (sc_in_array((string) $field->name, $all_cols, 'extension_allcols'))
                    {
                        $SQLSelectDataSelect = (string) $field->SQLSelectDataSelect;
                        if (!empty($SQLSelectDataSelect))
                        {
                            $sql .= eval($SQLSelectDataSelect);
                        }
                        elseif (((string) $field->table) == 'product_attribute')
                        {
                            $sql .= ',pa.`'.bqSQL($field->name).'`';
                        }
                        elseif (((string) $field->table) == 'product_attribute_shop')
                        {
                            $sql .= ',pa_shop.`'.bqSQL($field->name).'`';
                        }
                        elseif (((string) $field->table) == 'product')
                        {
                            $sql .= ',p.`'.bqSQL($field->name).'`';
                        }
                        elseif (((string) $field->table) == 'product_shop')
                        {
                            $sql .= ',p_shop.`'.bqSQL($field->name).'`';
                        }
                        elseif (((string) $field->table) == 'product_supplier')
                        {
                            $sql .= ',ps.`'.bqSQL($field->name).'`';
                        }
                    }
                }
                break;
            case 'SQLSelectDataLeftJoin':
                global $sql,$view,$cols,$all_cols, $id_lang;
                foreach ($grids_combinations_conf->fields->field as $field)
                {
                    if (sc_in_array((string) $field->name, $all_cols, 'extension_allcols'))
                    {
                        $sql .= eval((string) $field->SQLSelectDataLeftJoin);
                    }
                }
                break;
            case 'afterGetRows':
                foreach ($grids_combinations_conf->fields->field as $field)
                {
                    if (!empty($field->afterGetRows))
                    {
                        eval((string) $field->afterGetRows);
                    }
                }
                break;
        }
    }

    public static function readExportCSVConfigXML($config)
    {
        global $scExtensions_toDisabledInScTools;
        //global $export_csv_conf;
        $setextension = Tools::getValue('setextension', 1);
        if (!SC_TOOLS || !_s('CORE_USE_EXTENSIONS'))
        {
            return false;
        }
        $files = array();

        $all_files = scandir(SC_TOOLS_DIR);
        foreach ($all_files as $dir)
        {
            if (is_dir(SC_TOOLS_DIR.$dir) && $dir != '.' && $dir != '..')
            {
                if (in_array($dir, $scExtensions_toDisabledInScTools))
                {
                    continue;
                }
                if (file_exists(SC_TOOLS_DIR.$dir.'/export_csv_conf.xml'))
                {
                    $files[] = SC_TOOLS_DIR.$dir.'/export_csv_conf.xml';
                }
            }
        }
        if (file_exists(SC_TOOLS_DIR.'export_csv_conf.xml'))
        {
            $files[] = SC_TOOLS_DIR.'export_csv_conf.xml';
        }

        $return = '';

        foreach ($files as $file)
        {
            if (file_exists($file))
            {
                $exec = true;
                if (!$export_csv_conf = simplexml_load_file($file))
                {
                    $exec = false;
                }
                if ($exec)
                {
                    switch ($config){
                        case 'definition':// non
                            global $array,$fields_lang;
                            $tmp = explode(',', (string) $export_csv_conf->definitionLang);
                            foreach ($tmp as $t)
                            {
                                $fields_lang[] = $t;
                            }
                            eval((string) $export_csv_conf->definition);
                            break;
                        case 'exportMappingPrepareGrid': // avancée
                            global $sc_agent;
                            eval((string) $export_csv_conf->exportMappingPrepareGrid);
                            break;
                        case 'exportMappingCheckGrid': // avancée
                            global $sc_agent;
                            eval((string) $export_csv_conf->exportMappingCheckGrid);
                            break;
                        case 'exportMappingFillCombo': // avancée
                            global $sc_agent;
                            eval((string) $export_csv_conf->exportMappingFillCombo);
                            break;
                        case 'definitionLang': // permettre la sélection d'une langue dans le mapping
                            $tmp = join("','", explode(',', (string) $export_csv_conf->definitionLang));
                            $return .= ($tmp == '' ? '' : ",'".$tmp."'");
                            break;
                        case 'exportProcessInitRowVars': // avancée
                            global $id_product,$id_product_attribute,$p,$extension_vars;
                            eval((string) $export_csv_conf->exportProcessInitRowVars);
                            break;
                        case 'exportProcessProduct':
                            global $exportConfig,$switchObject,$switchObjectOption,$switchObjectLang,$getIDlangByISO,$id_product,$id_product_attribute,$field,$fields_lang,$f,$p,$product_attribute,$extension_vars,$selected_shops_id,$featuresListByLang,$featuresListNameDefault,$suppliersListByLang;
                            eval((string) $export_csv_conf->exportProcessProduct);
                            break;
                        case 'addInCombiFields': // avancée
                            global $standardFields;
                            eval((string) $export_csv_conf->addInCombiFields);
                            break;
                        case 'definitionForOptionTwoField':
                            if (!empty($export_csv_conf->definitionForOptionTwoField))
                            {
                                $return .= (string) $export_csv_conf->definitionForOptionTwoField;
                            }
                            break;
                    }
                }
            }
        }

        if (!empty($return))
        {
            return $return;
        }
    }

    public static function readImportCSVConfigXML($config)
    {
        global $scExtensions_toDisabledInScTools;
        //global $import_csv_conf;
        $setextension = Tools::getValue('setextension', 1);
        if (!SC_TOOLS || !_s('CORE_USE_EXTENSIONS'))
        {
            return false;
        }

        $files = array();

        $all_files = scandir(SC_TOOLS_DIR);
        foreach ($all_files as $dir)
        {
            if (is_dir(SC_TOOLS_DIR.$dir) && $dir != '.' && $dir != '..')
            {
                if (in_array($dir, $scExtensions_toDisabledInScTools))
                {
                    continue;
                }
                if (file_exists(SC_TOOLS_DIR.$dir.'/import_csv_conf.xml'))
                {
                    $files[] = SC_TOOLS_DIR.$dir.'/import_csv_conf.xml';
                }
            }
        }
        if (file_exists(SC_TOOLS_DIR.'import_csv_conf.xml'))
        {
            $files[] = SC_TOOLS_DIR.'import_csv_conf.xml';
        }

        $return = '';

        foreach ($files as $file)
        {
            if (file_exists($file))
            {
                $exec = true;
                if (!$import_csv_conf = simplexml_load_file($file))
                {
                    $exec = false;
                }
                if ($exec)
                {
                    switch ($config){
                        case 'definition':
                            global $array,$sc_agent;
                            eval((string) $import_csv_conf->definition);
                            break;
                        case 'importMappingLoadMappingOption': // avancée
                            global $sc_agent,$defaultLanguageId;
                            eval((string) $import_csv_conf->importMappingLoadMappingOption);
                            break;
                        case 'importMappingPrepareGrid': // avancée
                            global $sc_agent;
                            eval((string) $import_csv_conf->importMappingPrepareGrid);
                            break;
                        case 'importMappingCheckGrid': // avancée
                            global $sc_agent;
                            eval((string) $import_csv_conf->importMappingCheckGrid);
                            break;
                        case 'importMappingFillCombo': // avancée
                            global $sc_agent,$defaultLanguageId;
                            eval((string) $import_csv_conf->importMappingFillCombo);
                            break;
                        case 'importProcessIdentifier': // avancée
                            global $importConfig,$TODOfilename,$defaultLanguage,$sql,$sc_agent;
                            eval((string) $import_csv_conf->importProcessIdentifier);
                            break;
                        case 'importProcessProduct':
                            global $switchObject,$TODO,$id_product,$id_product_attribute,$newprod,$id_lang,$importConfig,$TODOfilename,$mappingData,$firstLineData,$key,$sc_agent,$features,$featureValues,$extension_vars,$id_shop,$getIDlangByISO;
                            eval((string) $import_csv_conf->importProcessProduct);
                            break;
                        case 'importProcessCombination':
                            global $switchObject,$TODO,$id_product,$id_product_attribute,$combinationValues,$newprod,$importConfig,$TODOfilename,$mappingData,$firstLineData,$key,$sc_agent,$features,$featureValues,$extension_vars,$id_lang,$id_shop,$getIDlangByISO;
                            eval((string) $import_csv_conf->importProcessCombination);
                            break;
                        case 'importProcessAfterCreateAll':
                            global $switchObject,$TODO,$id_product,$id_product_attribute,$combinationValues,$newprod,$importConfig,$TODOfilename,$mappingData,$firstLineData,$key,$sc_agent,$features,$featureValues,$extension_vars,$id_lang,$id_shop,$getIDlangByISO,$value;
                            eval((string) $import_csv_conf->importProcessAfterCreateAll);
                            break;
                        case 'importProcessInitRowVars': // avancée
                            global $switchObject,$id_product,$id_product_attribute,$newprod,$mappingData,$sc_agent,$extension_vars,$id_shop;
                            eval((string) $import_csv_conf->importProcessInitRowVars);
                            break;
                        case 'importProcessImageUpdate': // avancée
                            global $switchObject,$id_product,$id_product_attribute,$newprod,$mappingData,$getIDlangByISO,$sc_agent,$actual_num,$actual_id,$image_id,$TODO_image,$extension_vars,$id_shop;
                            eval((string) $import_csv_conf->importProcessImageUpdate);
                            break;
                        case 'definitionForLangField':
                            if (!empty($import_csv_conf->definitionForLangField))
                            {
                                $tmp = join("','", explode(',', (string) $import_csv_conf->definitionForLangField));
                                $return .= ($tmp == '' ? '' : ",'".$tmp."'");
                            }
                            elseif (!empty($import_csv_conf->definitionLang))
                            {
                                $tmp = join("','", explode(',', (string) $import_csv_conf->definitionLang));
                                $return .= ($tmp == '' ? '' : ",'".$tmp."'");
                            }
                            break;
                        case 'definitionForOptionField':
                            if (!empty($import_csv_conf->definitionForOptionField))
                            {
                                $tmp = join("','", explode(',', (string) $import_csv_conf->definitionForOptionField));
                                $return .= ($tmp == '' ? '' : ",'".$tmp."'");
                            }
                            break;
                    }
                }
            }
        }

        if (!empty($return))
        {
            return $return;
        }
    }

    public static function readCustomOrdersGridsConfigXML($config, $extData = false)
    {
        global $grids_orders_conf;
        $setextension = Tools::getValue('setextension', 1);
        if (!SC_TOOLS || !file_exists(SC_TOOLS_DIR.'grids_orders_conf.xml') || !_s('CORE_USE_EXTENSIONS'))
        {
            return false;
        }

        if (!$grids_orders_conf)
        {
            $extConvert = new ExtensionConvert();
            $extConvert->convert('orders');
            if (!$grids_orders_conf = simplexml_load_file(SC_TOOLS_DIR.'grids_orders_conf.xml'))
            {
                return false;
            }
        }
        switch ($config){
            case 'permissions':
                global $permissions_list;
                $commun_list = array('grid_light', 'grid_large', 'grid_delivery', 'grid_picking');
                foreach ($grids_orders_conf->grids->grid as $grid)
                {
                    if (!sc_in_array((string) $grid->name, $commun_list, 'extension_communlist'))
                    {
                        $permissions_list['GRI_ORD_VIEW_GRID_EXT_'.$grid->name] = array('id' => 'GRI_ORD_VIEW_GRID_EXT_'.$grid->name, 'section1' => 'Grid', 'section2' => 'Orders', 'name' => 'Order grid:'.' '.(string) $grid->text->fr, 'description' => '', 'default_admin' => 1, 'default_value' => 1);
                    }
                }
                break;
            case 'gridnames':
                foreach ($grids_orders_conf->grids->grid as $grid)
                {
                    $add = true;
                    if ($grid->name == 'grid_light' && !_r('GRI_ORD_VIEW_GRID_LIGHT'))
                    {
                        $add = false;
                    }
                    elseif ($grid->name == 'grid_large' && !_r('GRI_ORD_VIEW_GRID_LARGE'))
                    {
                        $add = false;
                    }
                    elseif ($grid->name == 'grid_delivery' && !_r('GRI_ORD_VIEW_GRID_DELIVERY'))
                    {
                        $add = false;
                    }
                    elseif ($grid->name == 'grid_picking' && !_r('GRI_ORD_VIEW_GRID_PICKING'))
                    {
                        $add = false;
                    }
                    elseif (!_r('GRI_ORD_VIEW_GRID_EXT_'.$grid->name))
                    {
                        $add = false;
                    }

                    if ($add == true)
                    {
                        echo "gridnames['".((string) $grid->name)."']='".str_replace("'", "\'", (string) $grid->text->fr)."';";
                    }
                }
                break;
            case 'toolbar':
                $lines = array();
                foreach ($grids_orders_conf->grids->grid as $grid)
                {
                    $lines[] = "['".((string) $grid->name)."', 'obj', gridnames['".((string) $grid->name)."'], '']";
                }
                echo(count($lines) ? ',' : '').join(',', $lines);
                break;
            case 'gridConfig':
                global $grids;
                foreach ($grids_orders_conf->grids->grid as $grid)
                {
                    $grids[((string) $grid->name)] = ((string) $grid->value);
                }
                break;
            case 'colSettings':
                global $colSettings,$arrPayments,$arrStatus;
                foreach ($grids_orders_conf->fields->field as $field)
                {
                    if ((string) $field->filter == 'na')
                    {
                        $field->filter = '';
                    }
                    $footer = '';
                    $options = '';
                    if (!empty($colSettings[((string) $field->name)]))
                    {
                        $footer = $colSettings[((string) $field->name)]['footer'];
                        $options = $colSettings[((string) $field->name)]['options'];
                    }
                    if (trim((string) $field->footer) != '')
                    {
                        $footer = (string) $field->footer;
                    }
                    if (trim((string) $field->options) != '')
                    {
                        $options = eval((string) $field->options);
                    }
                    else
                    {
                        $answertype = ((string) $field->answertype);
                        if ($answertype != '')
                        {
                            if ($answertype == 'YESNO')
                            {
                                $options = array(0 => _l('No'), 1 => _l('Yes'));
                            }
                        }
                    }
                    $colSettings[((string) $field->name)] = array('text' => str_replace("'", "\'", (string) $field->text->fr),
                                                                                                            'width' => ((string) $field->width),
                                                                                                            'align' => ((string) $field->align),
                                                                                                            'type' => ((string) $field->celltype),
                                                                                                            'sort' => ((string) $field->sort),
                                                                                                            'color' => ((string) $field->color),
                                                                                                            'format' => $colSettings[((string) $field->name)]['format'], //((string) $field->format),
                                                                                                            'filter' => ((string) $field->filter),
                                                                                                            'footer' => $footer,
                                                                                                            'options' => $options, );
                    $buildDefaultValue = ((string) $field->buildDefaultValue);
                    if ($buildDefaultValue != '')
                    {
                        $colSettings[((string) $field->name)]['buildDefaultValue'] = $buildDefaultValue;
                    }
                }
                break;
            case 'updateSettings':
                global $cols,$fields_order,$fields_customer,$fields_other,$idorder,$id_lang,$fields,$fields_address_invoice,$fields_address_delivery;
                foreach ($grids_orders_conf->fields->field as $field)
                {
                    if ((isset($cols) && sc_in_array((string) $field->name, $cols, 'extension_cols')) || !isset($cols))
                    {
                        if (((string) $field->table) == 'orders')
                        {
                            if (!empty($fields_order))
                            {
                                $fields_order[] = ((string) $field->name);
                            }
                            if (!empty($fields))
                            {
                                $fields[] = ((string) $field->name);
                            }
                        }
                        elseif (((string) $field->table) == 'customer')
                        {
                            if (!empty($fields_customer))
                            {
                                $fields_customer[] = ((string) $field->name);
                            }
                        }
                        elseif (((string) $field->table) == 'address_delivery')
                        {
                            if (isset($fields_other))
                            {
                                $fields_other[] = ',ad.`'.bqSQL($field->name).'`';
                            }
                        }
                        elseif (((string) $field->table) == 'address_invoice')
                        {
                            if (isset($fields_other))
                            {
                                $fields_other[] = ',adi.`'.bqSQL($field->name).'`';
                            }
                        }
                        elseif (((string) $field->table) == 'order_detail')
                        {
                            if (isset($fields_other))
                            {
                                $fields_other[] = ',od.`'.bqSQL($field->name).'`';
                            }
                        }
                        elseif (((string) $field->table) == 'product')
                        {
                            if (isset($fields_other))
                            {
                                $fields_other[] = ',p.`'.bqSQL($field->name).'`';
                            }
                        }
                        elseif (((string) $field->table) == 'product_shop')
                        {
                            if (isset($fields_other))
                            {
                                $fields_other[] = ',ps.`'.bqSQL($field->name).'`';
                            }
                        }
                        elseif (((string) $field->table) == 'warehouse')
                        {
                            if (isset($fields_other))
                            {
                                $fields_other[] = ',w.`'.bqSQL($field->name).'`';
                            }
                        }
                        elseif (((string) $field->table) == 'product_lang')
                        {
                            if (isset($fields_other))
                            {
                                $fields_other[] = ',pl.`'.bqSQL($field->name).'`';
                            }
                        }
                        elseif (((string) $field->table) == 'category_lang')
                        {
                            if (isset($fields_other))
                            {
                                $fields_other[] = ',cl.`'.bqSQL($field->name).'`';
                            }
                        }
                        elseif (((string) $field->table) == 'special')
                        {
                            // special actions
                            eval((string) $field->onUpdate);
                        }
                    }
                }
                break;
            case 'rowData':
                global $fields_order,$fields_customer,$col,$cols,$idorder,$id_lang,$orderrow;
                foreach ($grids_orders_conf->fields->field as $field)
                {
                    if (sc_in_array((string) $field->name, $cols, 'extension_cols'))
                    {
                        eval((string) $field->rowData);
                    }
                }
                    break;
            case 'SQLSelectDataSelect':
                global $sql,$view,$cols,$id_lang;
                foreach ($grids_orders_conf->fields->field as $field)
                {
                    if (sc_in_array((string) $field->name, $cols, 'extension_cols'))
                    {
                        $sql .= eval((string) $field->SQLSelectDataSelect);
                    }
                }
                    break;
            case 'SQLSelectDataLeftJoin':
                global $sql,$view,$cols;
                foreach ($grids_orders_conf->fields->field as $field)
                {
                    if (sc_in_array((string) $field->name, $cols, 'extension_cols'))
                    {
                        $sql .= eval((string) $field->SQLSelectDataLeftJoin);
                    }
                }
                    break;
            case 'onEditCell':
                foreach ($grids_orders_conf->grids->grid as $grid)
                {
                    echo (string) $grid->onEditCell;
                }
                foreach ($grids_orders_conf->fields->field as $field)
                {
                    echo (string) $field->onEditCell;
                }
                break;
            case 'onAfterUpdate':
                foreach ($grids_orders_conf->grids->grid as $grid)
                {
                    echo (string) $grid->onAfterUpdate;
                }
                foreach ($grids_orders_conf->fields->field as $field)
                {
                    echo (string) $field->onAfterUpdate;
                }
                break;
            case 'onAfterUpdateSQL':
                global $id_order,$id_order_detail;
                foreach ($grids_orders_conf->fields->field as $field)
                {
                    eval((string) $field->onAfterUpdateSQL);
                }
                break;
            case 'onBeforeUpdate':
                foreach ($grids_orders_conf->grids->grid as $grid)
                {
                    echo (string) $grid->onBeforeUpdate;
                }
                foreach ($grids_orders_conf->fields->field as $field)
                {
                    echo (string) $field->onBeforeUpdate;
                }
                break;
            case 'gridUserData':
                foreach ($grids_orders_conf->grid as $grid)
                {
                    eval((string) $grid->gridUserData);
                }
                break;
            case 'rowUserData':
                global $cols;
                foreach ($grids_orders_conf->fields->field as $field)
                {
                    if (sc_in_array((string) $field->name, $cols, 'extension_cols'))
                    {
                        eval((string) $field->rowUserData);
                    }
                }
                break;
            case 'extraVars':
                global $action,$extraVars,$cols;
                foreach ($grids_orders_conf->grids->grid as $grid)
                {
                    eval((string) $grid->extraVars);
                }
                foreach ($grids_orders_conf->fields->field as $field)
                {
                    eval((string) $field->extraVars);
                }
                break;
            case 'afterGetRows':
                foreach ($grids_orders_conf->fields->field as $field)
                {
                    if (!empty($field->afterGetRows))
                    {
                        eval((string) $field->afterGetRows);
                    }
                }
                break;
        }
    }

    public static function readCustomOrderPropProductsGridsConfigXML($config, $extData = false)
    {
        global $grids_order_product_conf;
        $setextension = Tools::getValue('setextension', 1);
        if (!SC_TOOLS || !file_exists(SC_TOOLS_DIR.'grids_order_product_conf.xml') || !_s('CORE_USE_EXTENSIONS'))
        {
            return false;
        }

        if (!$grids_order_product_conf)
        {
            $extConvert = new ExtensionConvert();
            $extConvert->convert('order_product');
            if (!$grids_order_product_conf = simplexml_load_file(SC_TOOLS_DIR.'grids_order_product_conf.xml'))
            {
                return false;
            }
        }

        switch ($config){
            case 'gridConfig':
                global $sourceGridFormat;
                if (!empty($grids_order_product_conf->grids->grid->value))
                {
                    $sourceGridFormat = (string) $grids_order_product_conf->grids->grid->value;
                }
                break;
            case 'colSettings':
                global $colSettings,$arrPayments,$arrStatus;
                foreach ($grids_order_product_conf->fields->field as $field)
                {
                    if ((string) $field->filter == 'na')
                    {
                        $field->filter = '';
                    }
                    $footer = '';
                    $options = '';
                    if (!empty($colSettings[((string) $field->name)]))
                    {
                        $footer = $colSettings[((string) $field->name)]['footer'];
                        $options = $colSettings[((string) $field->name)]['options'];
                    }
                    if (trim((string) $field->footer) != '')
                    {
                        $footer = (string) $field->footer;
                    }
                    if (trim((string) $field->options) != '')
                    {
                        $options = eval((string) $field->options);
                    }
                    else
                    {
                        $answertype = ((string) $field->answertype);
                        if ($answertype != '')
                        {
                            if ($answertype == 'YESNO')
                            {
                                $options = array(0 => _l('No'), 1 => _l('Yes'));
                            }
                        }
                    }
                    $colSettings[((string) $field->name)] = array('text' => str_replace("'", "\'", (string) $field->text->fr),
                        'width' => ((string) $field->width),
                        'align' => ((string) $field->align),
                        'type' => ((string) $field->celltype),
                        'sort' => ((string) $field->sort),
                        'color' => ((string) $field->color),
                        'format' => $colSettings[((string) $field->name)]['format'], //((string) $field->format),
                        'filter' => ((string) $field->filter),
                        'footer' => $footer,
                        'options' => $options, );
                    $buildDefaultValue = ((string) $field->buildDefaultValue);
                    if ($buildDefaultValue != '')
                    {
                        $colSettings[((string) $field->name)]['buildDefaultValue'] = $buildDefaultValue;
                    }
                }
                break;
            case 'rowData':
                global $col,$cols,$id_lang;
                foreach ($grids_order_product_conf->fields->field as $field)
                {
                    if (sc_in_array((string) $field->name, $cols, 'extension_cols'))
                    {
                        eval((string) $field->rowData);
                    }
                }
                break;
            case 'SQLSelectDataSelect':
                global $sql,$view,$cols,$id_lang;
                foreach ($grids_order_product_conf->fields->field as $field)
                {
                    if (sc_in_array((string) $field->name, $cols, 'extension_cols'))
                    {
                        $sql .= eval((string) $field->SQLSelectDataSelect);
                    }
                }
                break;
            case 'SQLSelectDataLeftJoin':
                global $sql,$view,$cols;
                foreach ($grids_order_product_conf->fields->field as $field)
                {
                    if (sc_in_array((string) $field->name, $cols, 'extension_cols'))
                    {
                        $sql .= eval((string) $field->SQLSelectDataLeftJoin);
                    }
                }
                break;
            case 'onEditCell':
                foreach ($grids_order_product_conf->grids->grid as $grid)
                {
                    echo (string) $grid->onEditCell;
                }
                foreach ($grids_order_product_conf->fields->field as $field)
                {
                    echo (string) $field->onEditCell;
                }
                break;
            case 'onAfterUpdate':
                foreach ($grids_order_product_conf->grids->grid as $grid)
                {
                    echo (string) $grid->onAfterUpdate;
                }
                foreach ($grids_order_product_conf->fields->field as $field)
                {
                    echo (string) $field->onAfterUpdate;
                }
                break;
            case 'onAfterUpdateSQL':
                global $id_order,$id_order_detail;
                foreach ($grids_order_product_conf->fields->field as $field)
                {
                    eval((string) $field->onAfterUpdateSQL);
                }
                break;
            case 'onBeforeUpdate':
                foreach ($grids_order_product_conf->grids->grid as $grid)
                {
                    echo (string) $grid->onBeforeUpdate;
                }
                foreach ($grids_order_product_conf->fields->field as $field)
                {
                    echo (string) $field->onBeforeUpdate;
                }
                break;
            case 'gridUserData':
                foreach ($grids_order_product_conf->grid as $grid)
                {
                    eval((string) $grid->gridUserData);
                }
                break;
            case 'rowUserData':
                global $cols;
                foreach ($grids_order_product_conf->fields->field as $field)
                {
                    if (sc_in_array((string) $field->name, $cols, 'extension_cols'))
                    {
                        eval((string) $field->rowUserData);
                    }
                }
                break;
            case 'extraVars':
                global $action,$extraVars,$cols;
                foreach ($grids_order_product_conf->grids->grid as $grid)
                {
                    eval((string) $grid->extraVars);
                }
                foreach ($grids_order_product_conf->fields->field as $field)
                {
                    eval((string) $field->extraVars);
                }
                break;
            case 'afterGetRows':
                foreach ($grids_order_product_conf->fields->field as $field)
                {
                    if (!empty($field->afterGetRows))
                    {
                        eval((string) $field->afterGetRows);
                    }
                }
                break;
        }
    }

    public static function readCustomCustomersGridsConfigXML($config, $extData = false)
    {
        global $grids_customers_conf;
        $setextension = Tools::getValue('setextension', 1);
        if (!SC_TOOLS || !file_exists(SC_TOOLS_DIR.'grids_customers_conf.xml') || !_s('CORE_USE_EXTENSIONS'))
        {
            return false;
        }

        if (!$grids_customers_conf)
        {
            $extConvert = new ExtensionConvert();
            $extConvert->convert('customers');
            if (!$grids_customers_conf = simplexml_load_file(SC_TOOLS_DIR.'grids_customers_conf.xml'))
            {
                return false;
            }
        }
        switch ($config){
            case 'permissions':
                global $permissions_list;
                $commun_list = array('grid_light', 'grid_large', 'grid_address', 'grid_convert');

                foreach ($grids_customers_conf->grids->grid as $grid)
                {
                    if (!sc_in_array((string) $grid->name, $commun_list, 'extension_commun_list'))
                    {
                        $permissions_list['GRI_CUS_VIEW_GRID_EXT_'.$grid->name] = array('id' => 'GRI_CUS_VIEW_GRID_EXT_'.$grid->name, 'section1' => 'Grid', 'section2' => 'Customers', 'name' => 'Customer grid:'.' '.(string) $grid->text->fr, 'description' => '', 'default_admin' => 1, 'default_value' => 1);
                    }
                }
                break;
            case 'gridnames':
                foreach ($grids_customers_conf->grids->grid as $grid)
                {
                    $add = true;
                    if ($grid->name == 'grid_light' && !_r('GRI_CUS_VIEW_GRID_LIGHT'))
                    {
                        $add = false;
                    }
                    elseif ($grid->name == 'grid_large' && !_r('GRI_CUS_VIEW_GRID_LARGE'))
                    {
                        $add = false;
                    }
                    elseif ($grid->name == 'grid_address' && !_r('GRI_CUS_VIEW_GRID_ADDRESS'))
                    {
                        $add = false;
                    }
                    elseif ($grid->name == 'grid_convert' && !_r('GRI_CUS_VIEW_GRID_CONVERT'))
                    {
                        $add = false;
                    }
                    elseif (!_r('GRI_CUS_VIEW_GRID_EXT_'.$grid->name))
                    {
                        $add = false;
                    }

                    if ($add == true)
                    {
                        echo "gridnames['".((string) $grid->name)."']='".str_replace("'", "\'", (string) $grid->text->fr)."';";
                    }
                }
                break;
            case 'toolbar':
                $lines = array();
                foreach ($grids_customers_conf->grids->grid as $grid)
                {
                    $lines[] = "['".((string) $grid->name)."', 'obj', gridnames['".((string) $grid->name)."'], '']";
                }
                echo(count($lines) ? ',' : '').join(',', $lines);
                break;
            case 'gridConfig':
                global $grids;
                foreach ($grids_customers_conf->grids->grid as $grid)
                {
                    $grids[((string) $grid->name)] = ((string) $grid->value);
                }
                break;
            case 'colSettings':
                global $colSettings,$arrGenders,$arrGroupes,$arrStates,$arrCountrys;
                foreach ($grids_customers_conf->fields->field as $field)
                {
                    if ((string) $field->filter == 'na')
                    {
                        $field->filter = '';
                    }
                    $footer = '';
                    $options = '';
                    if (!empty($colSettings[((string) $field->name)]))
                    {
                        $footer = $colSettings[((string) $field->name)]['footer'];
                        $options = $colSettings[((string) $field->name)]['options'];
                    }
                    if (trim((string) $field->footer) != '')
                    {
                        $footer = (string) $field->footer;
                    }
                    if (trim((string) $field->options) != '')
                    {
                        $options = eval((string) $field->options);
                    }
                    else
                    {
                        $answertype = ((string) $field->answertype);
                        if ($answertype != '')
                        {
                            if ($answertype == 'YESNO')
                            {
                                $options = array(0 => _l('No'), 1 => _l('Yes'));
                            }
                        }
                    }
                    $colSettings[((string) $field->name)] = array('text' => str_replace("'", "\'", (string) $field->text->fr),
                                                                                                            'width' => ((string) $field->width),
                                                                                                            'align' => ((string) $field->align),
                                                                                                            'type' => ((string) $field->celltype),
                                                                                                            'sort' => ((string) $field->sort),
                                                                                                            'color' => ((string) $field->color),
                                                                                                            'format' => $colSettings[((string) $field->name)]['format'], //((string) $field->format),
                                                                                                            'filter' => ((string) $field->filter),
                                                                                                            'footer' => $footer,
                                                                                                            'options' => $options, );
                    $buildDefaultValue = ((string) $field->buildDefaultValue);
                    if ($buildDefaultValue != '')
                    {
                        $colSettings[((string) $field->name)]['buildDefaultValue'] = $buildDefaultValue;
                    }
                }
                break;
            case 'updateSettings':
                global $fields_customer,$fields,$fields_address,$idcustomer,$id_lang;
                foreach ($grids_customers_conf->fields->field as $field)
                {
                    if (((string) $field->table) == 'customer')
                    {
                        if (!empty($fields_customer))
                        {
                            $fields_customer[] = ((string) $field->name);
                        }
                        if (!empty($fields))
                        {
                            $fields[] = ((string) $field->name);
                        }
                    }
                    elseif (((string) $field->table) == 'address')
                    {
                        if (!empty($fields_address))
                        {
                            $fields_address[] = ((string) $field->name);
                        }
                    }
                    elseif (((string) $field->table) == 'special')
                    {
                        // special actions
                        eval((string) $field->onUpdate);
                    }
                }
                break;
            case 'rowData':
                global $fields_customer,$fields,$fields_address,$col,$cols,$idcustomer,$id_lang,$gridrow;
                foreach ($grids_customers_conf->fields->field as $field)
                {
                    if (sc_in_array((string) $field->name, $cols, 'extension_cols'))
                    {
                        eval((string) $field->rowData);
                    }
                }
                    break;
            case 'SQLSelectDataSelect':
                global $sql,$view,$cols;
                foreach ($grids_customers_conf->fields->field as $field)
                {
                    if (sc_in_array((string) $field->name, $cols, 'extension_cols'))
                    {
                        $sql .= eval((string) $field->SQLSelectDataSelect);
                    }
                }
                    break;
            case 'SQLSelectDataLeftJoin':
                global $sql,$view,$cols;
                foreach ($grids_customers_conf->fields->field as $field)
                {
                    if (sc_in_array((string) $field->name, $cols, 'extension_cols'))
                    {
                        $sql .= eval((string) $field->SQLSelectDataLeftJoin);
                    }
                }
                    break;
            case 'onEditCell':
                foreach ($grids_customers_conf->grids->grid as $grid)
                {
                    echo (string) $grid->onEditCell;
                }
                foreach ($grids_customers_conf->fields->field as $field)
                {
                    echo (string) $field->onEditCell;
                }
                break;
            case 'onAfterUpdate':
                foreach ($grids_customers_conf->grids->grid as $grid)
                {
                    echo (string) $grid->onAfterUpdate;
                }
                foreach ($grids_customers_conf->fields->field as $field)
                {
                    echo (string) $field->onAfterUpdate;
                }
                break;
            case 'onAfterUpdateSQL':
                global $id_customer,$gr_id,$for_address,$_POST;
                foreach ($grids_customers_conf->fields->field as $field)
                {
                    eval((string) $field->onAfterUpdateSQL);
                }
                break;
            case 'onBeforeUpdate':
                foreach ($grids_customers_conf->grids->grid as $grid)
                {
                    echo (string) $grid->onBeforeUpdate;
                }
                foreach ($grids_customers_conf->fields->field as $field)
                {
                    echo (string) $field->onBeforeUpdate;
                }
                break;
            case 'gridUserData':
                foreach ($grids_customers_conf->grids->grid as $grid)
                {
                    eval((string) $grid->gridUserData);
                }
                break;
            case 'rowUserData':
                global $cols;
                foreach ($grids_customers_conf->fields->field as $field)
                {
                    if (sc_in_array((string) $field->name, $cols, 'extension_cols'))
                    {
                        eval((string) $field->rowUserData);
                    }
                }
                break;
            case 'extraVars':
                global $action,$extraVars,$cols;
                foreach ($grids_customers_conf->grids->grid as $grid)
                {
                    eval((string) $grid->extraVars);
                }
                foreach ($grids_customers_conf->fields->field as $field)
                {
                    eval((string) $field->extraVars);
                }
                break;
            case 'afterGetRows':
                foreach ($grids_customers_conf->fields->field as $field)
                {
                    if (!empty($field->afterGetRows))
                    {
                        eval((string) $field->afterGetRows);
                    }
                }
                break;
        }
    }

    public static function readImportCustomerCSVConfigXML($config)
    {
        //global $import_csv_conf;
        $setextension = Tools::getValue('setextension', 1);
        if (!SC_TOOLS || !_s('CORE_USE_EXTENSIONS'))
        {
            return false;
        }

        $files = array();

        $all_files = scandir(SC_TOOLS_DIR);
        foreach ($all_files as $dir)
        {
            if (is_dir(SC_TOOLS_DIR.$dir) && $dir != '.' && $dir != '..')
            {
                if (file_exists(SC_TOOLS_DIR.$dir.'/import_customer_csv_conf.xml'))
                {
                    $files[] = SC_TOOLS_DIR.$dir.'/import_customer_csv_conf.xml';
                }
            }
        }
        if (file_exists(SC_TOOLS_DIR.'import_customer_csv_conf.xml'))
        {
            $files[] = SC_TOOLS_DIR.'import_customer_csv_conf.xml';
        }

        $return = '';

        foreach ($files as $file)
        {
            if (file_exists($file))
            {
                $exec = true;
                if (!$import_csv_conf = simplexml_load_file($file))
                {
                    $exec = false;
                }
                if ($exec)
                {
                    switch ($config){
                        case 'definition':
                            global $array,$sc_agent;
                            eval((string) $import_csv_conf->definition);
                            break;
                        case 'importMappingPrepareGrid': // avancée
                            global $sc_agent;
                            eval((string) $import_csv_conf->importMappingPrepareGrid);
                            break;
                        case 'importMappingCheckGrid': // avancée
                            global $sc_agent;
                            eval((string) $import_csv_conf->importMappingCheckGrid);
                            break;
                        case 'importMappingFillCombo': // avancée
                            global $sc_agent;
                            eval((string) $import_csv_conf->importMappingFillCombo);
                            break;
                        case 'importProcessCustomer':
                            global $switchObject,$TODO,$newcustomer,$newaddress,$id_customer,$id_lang,$importConfig,$TODOfilename,$mappingData,$firstLineData,$key,$sc_agent,$id_shop,$getIDlangByISO;
                            eval((string) $import_csv_conf->importProcessCustomer);
                            break;
                        case 'importProcessCustomerAfter':
                            global $newcustomer,$newaddress,$id_customer,$id_lang,$importConfig,$TODOfilename,$mappingData,$firstLineData,$sc_agent,$id_shop,$getIDlangByISO;
                            eval((string) $import_csv_conf->importProcessCustomerAfter);
                            break;
                        case 'importProcessAfterCreateAll':
                            global $importConfig,$TODOfilename,$mappingData,$firstLineData,$sc_agent,$id_lang,$id_shop,$getIDlangByISO;
                            eval((string) $import_csv_conf->importProcessAfterCreateAll);
                            break;
                    }
                }
            }
        }

        if (!empty($return))
        {
            return $return;
        }
    }

    public static function readCustomImageGridConfigXML($config)
    {
        global $grid_image_conf;
        $setextension = Tools::getValue('setextension', 1);
        if (!SC_TOOLS || !file_exists(SC_TOOLS_DIR.'grids_image_conf.xml') || !_s('CORE_USE_EXTENSIONS'))
        {
            return false;
        }
        if (empty($grid_image_conf))
        {
            if (!$grid_image_conf = simplexml_load_file(SC_TOOLS_DIR.'grids_image_conf.xml'))
            {
                return false;
            }
        }
        switch ($config){
            case 'gridConfig':
                global $sourceGridFormat;
                if (!empty($grid_image_conf->grids->grid->value))
                {
                    $sourceGridFormat = (string) $grid_image_conf->grids->grid->value;
                }
                break;
            case 'definition':
                global $combArray,$combinaison,$cols,$all_cols;
                foreach ($grid_image_conf->fields->field as $field)
                {
                    if (sc_in_array((string) $field->name, $all_cols, 'extension_allcols'))
                    {
                        eval((string) $field->definition);
                    }
                }
                break;
            case 'colSettings':
                global $colSettings;
                foreach ($grid_image_conf->fields->field as $field)
                {
                    if ((string) $field->filter == 'na')
                    {
                        $field->filter = '';
                    }
                    $footer = '';
                    $options = '';
                    if (!empty($colSettings[((string) $field->name)]))
                    {
                        $footer = $colSettings[((string) $field->name)]['footer'];
                        $options = $colSettings[((string) $field->name)]['options'];
                    }
                    if (trim((string) $field->footer) != '')
                    {
                        $footer = (string) $field->footer;
                    }
                    if (trim((string) $field->options) != '')
                    {
                        $options = eval((string) $field->options);
                    }
                    else
                    {
                        $answertype = ((string) $field->answertype);
                        if ($answertype != '')
                        {
                            if ($answertype == 'YESNO')
                            {
                                $options = array(0 => _l('No'), 1 => _l('Yes'));
                            }
                        }
                    }
                    $colSettings[((string) $field->name)] = array('text' => str_replace("'", "\'", (string) $field->text->fr),
                            'width' => ((string) $field->width),
                            'align' => ((string) $field->align),
                            'type' => ((string) $field->celltype),
                            'sort' => ((string) $field->sort),
                            'color' => ((string) $field->color),
                            'format' => $colSettings[((string) $field->name)]['format'], //((string) $field->format),
                            'filter' => ((string) $field->filter),
                            'footer' => $footer,
                            'options' => $options, );
                    $buildDefaultValue = ((string) $field->buildDefaultValue);
                    if ($buildDefaultValue != '')
                    {
                        $colSettings[((string) $field->name)]['buildDefaultValue'] = $buildDefaultValue;
                    }
                }
                break;
        case 'updateSettings':
            global $fields_other,$fields_lang,$fields,$cols;
            foreach ($grid_image_conf->fields->field as $field)
            {
                if (((string) $field->table) == 'image')
                {
                    if (isset($fields_other) && sc_in_array((string) $field->name, $cols, 'extension_allcols'))
                    {
                        $fields_other[] = 'i.`'.bqSQL($field->name).'`';
                    }
                    if (!empty($fields))
                    {
                        $fields[] = (bqSQL($field->name));
                    }
                }
                elseif (((string) $field->table) == 'image_lang')
                {
                    if (isset($fields_other) && sc_in_array((string) $field->name, $cols, 'extension_allcols'))
                    {
                        $fields_other[] = 'il.`'.bqSQL($field->name).'`';
                    }
                    if (!empty($fields_lang))
                    {
                        $fields_lang[] = ((string) $field->name);
                    }
                }
                elseif (((string) $field->table) == 'product')
                {
                    if (isset($fields_other) && sc_in_array((string) $field->name, $cols, 'extension_allcols'))
                    {
                        $fields_other[] = 'p.`'.bqSQL($field->name).'`';
                    }
                }
                elseif (((string) $field->table) == 'image_shop')
                {
                    if (isset($fields_other) && sc_in_array((string) $field->name, $cols, 'extension_allcols'))
                    {
                        $fields_other[] = 'ims.`'.bqSQL($field->name).'`';
                    }
                }
                elseif (((string) $field->table) == 'product_lang')
                {
                    if (isset($fields_other) && sc_in_array((string) $field->name, $cols, 'extension_allcols'))
                    {
                        $fields_other[] = 'pl.`'.bqSQL($field->name).'`';
                    }
                }
            }
            break;
            case 'gridUserData':
                eval((string) $grid_image_conf->grids->grid->gridUserData);
                break;
            case 'rowUserData':
                global $cols,$all_cols;
                foreach ($grid_image_conf->fields->field as $field)
                {
                    if (sc_in_array((string) $field->name, $all_cols, 'extension_allcols'))
                    {
                        eval((string) $field->rowUserData);
                    }
                }
                break;
            case 'SQLSelectDataSelect':
                global $sql,$view,$cols,$all_cols;
                foreach ($grid_image_conf->fields->field as $field)
                {
                    if (sc_in_array((string) $field->name, $all_cols, 'extension_allcols'))
                    {
                        $sql .= eval((string) $field->SQLSelectDataSelect);
                    }
                }
                break;
            case 'SQLSelectDataLeftJoin':
                global $sql,$view,$cols,$all_cols;
                foreach ($grid_image_conf->fields->field as $field)
                {
                    if (sc_in_array((string) $field->name, $all_cols, 'extension_allcols'))
                    {
                        $sql .= eval((string) $field->SQLSelectDataLeftJoin);
                    }
                }
                break;
            case 'onEditCell':
                echo (string) $grid_image_conf->grids->grid->onEditCell;
                foreach ($grid_image_conf->fields->field as $field)
                {
                    if (!empty($field->onEditCell))
                    {
                        echo (string) $field->onEditCell;
                    }
                    elseif (((string) $field->table) == 'image' || ((string) $field->table) == 'image_lang')
                    {
                        echo '
                        idx'.(string) $field->name.'=prop_tb._imagesGrid.getColIndexById("'.(string) $field->name.'");
                        if (cInd == idx'.(string) $field->name.' && stage==2){
                            $.post("index.php?ajax=1&act=cat_image_update&action=update&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{"id_product": lastProductSelID, col: "'.(string) $field->name.'", val: nValue, "list_id_image":prop_tb._imagesGrid.getSelectedRowId()},function(data){});
                        }
                        ';
                    }
                }
                break;
            case 'onBeforeUpdate':
                echo (string) $grid_image_conf->grids->grid->onBeforeUpdate;
                foreach ($grid_image_conf->fields->field as $field)
                {
                    echo (string) $field->onBeforeUpdate;
                }
                break;
            case 'onAfterUpdate':
                echo (string) $grid_image_conf->grids->grid->onAfterUpdate;
                foreach ($grid_image_conf->fields->field as $field)
                {
                    echo (string) $field->onAfterUpdate;
                }
                break;
            case 'onAfterUpdateSQL':
                global $id_image,$id_product,$id_lang,$col,$val;
                foreach ($grid_image_conf->fields->field as $field)
                {
                    eval((string) $field->onAfterUpdateSQL);
                }
                break;
            case 'afterGetRows':
                foreach ($grid_image_conf->fields->field as $field)
                {
                    if (!empty($field->afterGetRows))
                    {
                        eval((string) $field->afterGetRows);
                    }
                }
                break;
        }
    }

    public static function readCustomCategoriesGridConfigXML($config, $extData = '')
    {
        global $grids_categories_conf;
        $setextension = Tools::getValue('setextension', 1);
        if (!SC_TOOLS || !file_exists(SC_TOOLS_DIR.'grids_categories_conf.xml'))
        {
            return false;
        }
        if (!$grids_categories_conf)
        {
            $ext = new ExtensionConvert();
            $ext->convert('categories');
            if (!$grids_categories_conf = simplexml_load_file(SC_TOOLS_DIR.'grids_categories_conf.xml'))
            {
                return false;
            }
        }

        switch ($config){
            case 'addHeaderInGet':
                $return = '';
                foreach ($grids_categories_conf->fields->field as $field)
                {
                    $html_options = '';
                    if (trim((string) $field->options) != '')
                    {
                        $field->align = 'center';
                        $field->sort = 'str';
                        $options = eval((string) $field->options);
                        foreach ($options as $value => $name)
                        {
                            $html_options .= '<option value="'.$value.'">'.$name.'</option>'."\n";
                        }
                    }
                    $return .= '<column id="'.(string) $field->name.'" width="'.(string) $field->width.'" type="'.(string) $field->celltype.'" align="'.(string) $field->align.'" sort="'.(string) $field->sort.'">'.str_replace("'", "\'", (string) $field->text->fr).$html_options.'</column>'."\n";
                }

                return $return;
                break;
            case 'addFilterInGet':
                $return = '';
                foreach ($grids_categories_conf->fields->field as $field)
                {
                    $val = '';
                    if (!empty($field->filter))
                    {
                        $val = (string) $field->filter;
                    }
                    $return .= ','.$val;
                }

                return $return;
                break;
            case 'addRowValueInGet':
                global $row;
                $return = '';
                foreach ($grids_categories_conf->fields->field as $field)
                {
                    $val = '';
                    if (!empty($extData[(string) $field->name]))
                    {
                        $val = $extData[(string) $field->name];
                    }
                    if (trim((string) $field->options) != '' && (empty($val) || is_numeric($val)))
                    {
                        $val = (int) $val;
                    }
                    $return .= '<cell><![CDATA['.$val.']]></cell>';
                    $return .= '<userdata name="enableClick_'.(string) $field->name.'">1</userdata>'."\n";
                }

                return $return;
                break;
            case 'onEditCell':
                foreach ($grids_categories_conf->fields->field as $field)
                {
                    eval((string) $field->onEditCell);
                }
                break;
            case 'onAfterUpdateSQL':
                global $field,$value,$id_category,$id_lang;
                foreach ($grids_categories_conf->fields->field as $fieldnode)
                {
                    eval((string) $fieldnode->onAfterUpdateSQL);
                }
                break;
        }
    }

    public static function readCustomProductsortGridConfigXML($config, $extData = false)
    {
        global $grids_productsort_conf;
        $setextension = Tools::getValue('setextension', 1);
        if (!SC_TOOLS || !file_exists(SC_TOOLS_DIR.'grids_productsort_conf.xml') || !_s('CORE_USE_EXTENSIONS'))
        {
            return false;
        }
        if (!$grids_productsort_conf)
        {
            $ext = new ExtensionConvert();
            $ext->convert('productsort');
            if (!$grids_productsort_conf = simplexml_load_file(SC_TOOLS_DIR.'grids_productsort_conf.xml'))
            {
                return false;
            }
        }

        switch ($config){
            case 'gridConfig':
                global $sourceGridFormat;
                if (!empty($grids_productsort_conf->grids->grid->value))
                {
                    $sourceGridFormat = (string) $grids_productsort_conf->grids->grid->value;
                }
                break;
            case 'definition':
                global $combArray,$combinaison,$cols,$all_cols;
                foreach ($grids_productsort_conf->fields->field as $field)
                {
                    if (sc_in_array((string) $field->name, $all_cols, 'extension_allcols'))
                    {
                        eval((string) $field->definition);
                    }
                }
                break;
            case 'colSettings':
                global $colSettings;
                foreach ($grids_productsort_conf->fields->field as $field)
                {
                    if ((string) $field->filter == 'na')
                    {
                        $field->filter = '';
                    }
                    $footer = '';
                    $options = '';
                    if (!empty($colSettings[((string) $field->name)]))
                    {
                        $footer = $colSettings[((string) $field->name)]['footer'];
                        $options = $colSettings[((string) $field->name)]['options'];
                    }
                    if (trim((string) $field->footer) != '')
                    {
                        $footer = (string) $field->footer;
                    }
                    if (trim((string) $field->options) != '')
                    {
                        $options = eval((string) $field->options);
                    }
                    else
                    {
                        $answertype = ((string) $field->answertype);
                        if ($answertype != '')
                        {
                            if ($answertype == 'YESNO')
                            {
                                $options = array(0 => _l('No'), 1 => _l('Yes'));
                            }
                        }
                    }
                    $colSettings[((string) $field->name)] = array('text' => str_replace("'", "\'", (string) $field->text->fr),
                            'width' => ((string) $field->width),
                            'align' => ((string) $field->align),
                            'type' => ((string) $field->celltype),
                            'sort' => ((string) $field->sort),
                            'color' => ((string) $field->color),
                            'format' => $colSettings[((string) $field->name)]['format'], //((string) $field->format),
                            'filter' => ((string) $field->filter),
                            'footer' => $footer,
                            'options' => $options, );
                    $buildDefaultValue = ((string) $field->buildDefaultValue);
                    if ($buildDefaultValue != '')
                    {
                        $colSettings[((string) $field->name)]['buildDefaultValue'] = $buildDefaultValue;
                    }
                }
                break;
            case 'gridUserData':
                eval((string) $grids_productsort_conf->grids->grid->gridUserData);
                break;
            case 'SQLSelectDataSelect':
                global $sql,$view,$cols,$all_cols;
                foreach ($grids_productsort_conf->fields->field as $field)
                {
                    if (sc_in_array((string) $field->name, $all_cols, 'extension_allcols'))
                    {
                        if (!empty($field->SQLSelectDataSelect))
                        {
                            $sql .= eval((string) $field->SQLSelectDataSelect);
                        }
                        elseif (((string) $field->table) == 'product')
                        {
                            $sql .= ',p.`'.bqSQL($field->name).'`';
                        }
                        elseif (((string) $field->table) == 'product_shop')
                        {
                            $sql .= ',prs.`'.bqSQL($field->name).'`';
                        }
                        elseif (((string) $field->table) == 'product_lang')
                        {
                            $sql .= ',pl.`'.bqSQL($field->name).'`';
                        }
                    }
                }
                break;
            case 'SQLSelectDataLeftJoin':
                global $sql,$view,$cols,$all_cols;
                foreach ($grids_productsort_conf->fields->field as $field)
                {
                    if (sc_in_array((string) $field->name, $all_cols, 'extension_allcols'))
                    {
                        $sql .= eval((string) $field->SQLSelectDataLeftJoin);
                    }
                }
                break;
            case 'afterGetRows':
                foreach ($grids_productsort_conf->fields->field as $field)
                {
                    if (!empty($field->afterGetRows))
                    {
                        eval((string) $field->afterGetRows);
                    }
                }
                break;
            case 'rowData':
                global $row,$col;
                foreach ($grids_productsort_conf->fields->field as $field)
                {
                    if ((trim((string) $field->rowData) != ''))
                    {
                        eval((string) $field->rowData);
                    }
                }
                break;
        }
    }

    public static function readCustomMsProductGridConfigXML($config, $extData = false)
    {
        global $grids_msproduct_conf;
        $setextension = Tools::getValue('setextension', 1);
        if (!SC_TOOLS || !file_exists(SC_TOOLS_DIR.'grids_msproduct_conf.xml') || !_s('CORE_USE_EXTENSIONS'))
        {
            return false;
        }
        if (!$grids_msproduct_conf)
        {
            $ext = new ExtensionConvert();
            $ext->convert('msproduct');
            if (!$grids_msproduct_conf = simplexml_load_file(SC_TOOLS_DIR.'grids_msproduct_conf.xml'))
            {
                return false;
            }
        }

        switch ($config){
            case 'gridConfig':
                global $sourceGridFormat;
                if (!empty($grids_msproduct_conf->grids->grid->value))
                {
                    $sourceGridFormat = (string) $grids_msproduct_conf->grids->grid->value;
                }
                break;
            case 'definition':
                global $combArray,$combinaison,$cols,$all_cols;
                foreach ($grids_msproduct_conf->fields->field as $field)
                {
                    if (sc_in_array((string) $field->name, $all_cols, 'extension_allcols'))
                    {
                        eval((string) $field->definition);
                    }
                }
                break;
            case 'colSettings':
                global $colSettings;
                foreach ($grids_msproduct_conf->fields->field as $field)
                {
                    if ((string) $field->filter == 'na')
                    {
                        $field->filter = '';
                    }
                    $footer = '';
                    $options = '';
                    if (!empty($colSettings[((string) $field->name)]))
                    {
                        $footer = $colSettings[((string) $field->name)]['footer'];
                        $options = $colSettings[((string) $field->name)]['options'];
                    }
                    if (trim((string) $field->footer) != '')
                    {
                        $footer = (string) $field->footer;
                    }
                    if (trim((string) $field->options) != '')
                    {
                        $options = eval((string) $field->options);
                    }
                    else
                    {
                        $answertype = ((string) $field->answertype);
                        if ($answertype != '')
                        {
                            if ($answertype == 'YESNO')
                            {
                                $options = array(0 => _l('No'), 1 => _l('Yes'));
                            }
                        }
                    }
                    $colSettings[((string) $field->name)] = array('text' => str_replace("'", "\'", (string) $field->text->fr),
                            'width' => ((string) $field->width),
                            'align' => ((string) $field->align),
                            'type' => ((string) $field->celltype),
                            'sort' => ((string) $field->sort),
                            'color' => ((string) $field->color),
                            'format' => $colSettings[((string) $field->name)]['format'], //((string) $field->format),
                            'filter' => ((string) $field->filter),
                            'footer' => $footer,
                            'options' => $options, );
                    $buildDefaultValue = ((string) $field->buildDefaultValue);
                    if ($buildDefaultValue != '')
                    {
                        $colSettings[((string) $field->name)]['buildDefaultValue'] = $buildDefaultValue;
                    }
                }
                break;
            case 'gridUserData':
                eval((string) $grids_msproduct_conf->grids->grid->gridUserData);
                break;
            case 'rowUserData':
                global $cols,$all_cols;
                foreach ($grids_msproduct_conf->fields->field as $field)
                {
                    if (sc_in_array((string) $field->name, $all_cols, 'extension_allcols'))
                    {
                        eval((string) $field->rowUserData);
                    }
                }
                break;
            case 'SQLSelectDataSelect':
                global $sql,$view,$cols,$all_cols;
                foreach ($grids_msproduct_conf->fields->field as $field)
                {
                    if (sc_in_array((string) $field->name, $all_cols, 'extension_allcols'))
                    {
                        $sql .= eval((string) $field->SQLSelectDataSelect);
                    }
                }
                break;
            case 'SQLSelectDataLeftJoin':
                global $sql,$view,$cols,$all_cols;
                foreach ($grids_msproduct_conf->fields->field as $field)
                {
                    if (sc_in_array((string) $field->name, $all_cols, 'extension_allcols'))
                    {
                        $sql .= eval((string) $field->SQLSelectDataLeftJoin);
                    }
                }
                break;
            case 'onEditCell':
                echo (string) $grids_msproduct_conf->grids->grid->onEditCell;
                foreach ($grids_msproduct_conf->fields->field as $field)
                {
                    echo (string) $field->onEditCell;
                }
                break;
            case 'onBeforeUpdate':
                echo (string) $grids_msproduct_conf->grids->grid->onBeforeUpdate;
                foreach ($grids_msproduct_conf->fields->field as $field)
                {
                    echo (string) $field->onBeforeUpdate;
                }
                break;
            case 'onAfterUpdate':
                echo (string) $grids_msproduct_conf->grids->grid->onAfterUpdate;
                foreach ($grids_msproduct_conf->fields->field as $field)
                {
                    echo (string) $field->onAfterUpdate;
                }
                break;
            case 'onAfterUpdateSQL':
                global $idproduct,$id_product,$id_product_attribute,$id_shop;
                foreach ($grids_msproduct_conf->fields->field as $field)
                {
                    eval((string) $field->onAfterUpdateSQL);
                }
                break;
            case 'afterGetRows':
                foreach ($grids_msproduct_conf->fields->field as $field)
                {
                    if (!empty($field->afterGetRows))
                    {
                        eval((string) $field->afterGetRows);
                    }
                }
                break;
        }
    }

    public static function readCustomMsCombinationGridConfigXML($config, $extData = false)
    {
        global $grids_mscombination_conf;
        $setextension = Tools::getValue('setextension', 1);
        if (!SC_TOOLS || !file_exists(SC_TOOLS_DIR.'grids_mscombination_conf.xml') || !_s('CORE_USE_EXTENSIONS'))
        {
            return false;
        }
        if (!$grids_mscombination_conf)
        {
            $ext = new ExtensionConvert();
            $ext->convert('mscombination');
            if (!$grids_mscombination_conf = simplexml_load_file(SC_TOOLS_DIR.'grids_mscombination_conf.xml'))
            {
                return false;
            }
        }

        switch ($config){
            case 'gridConfig':
                global $sourceGridFormat;
                if (!empty($grids_mscombination_conf->grids->grid->value))
                {
                    $sourceGridFormat = (string) $grids_mscombination_conf->grids->grid->value;
                }
                break;
            case 'definition':
                global $product_attr_by_shop,$product_attr,$product,$cols,$all_cols;
                foreach ($grids_mscombination_conf->fields->field as $field)
                {
                    if (sc_in_array((string) $field->name, $all_cols, 'extension_allcols'))
                    {
                        eval((string) $field->definition);
                    }
                }
                break;
            case 'colSettings':
                global $colSettings;
                foreach ($grids_mscombination_conf->fields->field as $field)
                {
                    if ((string) $field->filter == 'na')
                    {
                        $field->filter = '';
                    }
                    $footer = '';
                    $options = '';
                    if (!empty($colSettings[((string) $field->name)]))
                    {
                        $footer = $colSettings[((string) $field->name)]['footer'];
                        $options = $colSettings[((string) $field->name)]['options'];
                    }
                    if (trim((string) $field->footer) != '')
                    {
                        $footer = (string) $field->footer;
                    }
                    if (trim((string) $field->options) != '')
                    {
                        $options = eval((string) $field->options);
                    }
                    else
                    {
                        $answertype = ((string) $field->answertype);
                        if ($answertype != '')
                        {
                            if ($answertype == 'YESNO')
                            {
                                $options = array(0 => _l('No'), 1 => _l('Yes'));
                            }
                        }
                    }
                    $colSettings[((string) $field->name)] = array('text' => str_replace("'", "\'", (string) $field->text->fr),
                            'width' => ((string) $field->width),
                            'align' => ((string) $field->align),
                            'type' => ((string) $field->celltype),
                            'sort' => ((string) $field->sort),
                            'color' => ((string) $field->color),
                            'format' => $colSettings[((string) $field->name)]['format'], //((string) $field->format),
                            'filter' => ((string) $field->filter),
                            'footer' => $footer,
                            'options' => $options, );
                    $buildDefaultValue = ((string) $field->buildDefaultValue);
                    if ($buildDefaultValue != '')
                    {
                        $colSettings[((string) $field->name)]['buildDefaultValue'] = $buildDefaultValue;
                    }
                }
                break;
            case 'gridUserData':
                eval((string) $grids_mscombination_conf->grids->grid->gridUserData);
                break;
            case 'rowUserData':
                global $cols,$all_cols;
                foreach ($grids_mscombination_conf->fields->field as $field)
                {
                    if (sc_in_array((string) $field->name, $all_cols, 'extension_allcols'))
                    {
                        eval((string) $field->rowUserData);
                    }
                }
                break;
            case 'SQLSelectDataSelect':
                global $sql,$view,$cols,$all_cols;
                foreach ($grids_mscombination_conf->fields->field as $field)
                {
                    if (sc_in_array((string) $field->name, $all_cols, 'extension_allcols'))
                    {
                        $sql .= eval((string) $field->SQLSelectDataSelect);
                    }
                }
                break;
            case 'SQLSelectDataLeftJoin':
                global $sql,$view,$cols,$all_cols;
                foreach ($grids_mscombination_conf->fields->field as $field)
                {
                    if (sc_in_array((string) $field->name, $all_cols, 'extension_allcols'))
                    {
                        $sql .= eval((string) $field->SQLSelectDataLeftJoin);
                    }
                }
                break;
            case 'onEditCell':
                echo (string) $grids_mscombination_conf->grids->grid->onEditCell;
                foreach ($grids_mscombination_conf->fields->field as $field)
                {
                    echo (string) $field->onEditCell;
                }
                break;
            case 'onBeforeUpdate':
                echo (string) $grids_mscombination_conf->grids->grid->onBeforeUpdate;
                foreach ($grids_mscombination_conf->fields->field as $field)
                {
                    echo (string) $field->onBeforeUpdate;
                }
                break;
            case 'onAfterUpdate':
                echo (string) $grids_mscombination_conf->grids->grid->onAfterUpdate;
                foreach ($grids_mscombination_conf->fields->field as $field)
                {
                    echo (string) $field->onAfterUpdate;
                }
                break;
            case 'onAfterUpdateSQL':
                global $idproduct,$id_product,$id_product_attribute,$id_shop;
                foreach ($grids_mscombination_conf->fields->field as $field)
                {
                    eval((string) $field->onAfterUpdateSQL);
                }
                break;
            case 'afterGetRows':
                foreach ($grids_mscombination_conf->fields->field as $field)
                {
                    if (!empty($field->afterGetRows))
                    {
                        eval((string) $field->afterGetRows);
                    }
                }
                break;
        }
    }

    public static function readCustomCombinationMultiProductGridConfigXML($config, $extData = false)
    {
        global $grids_combinationmultiproduct_conf;
        $setextension = Tools::getValue('setextension', 1);
        if (!SC_TOOLS || !file_exists(SC_TOOLS_DIR.'grids_combinationmultiproduct_conf.xml') || !_s('CORE_USE_EXTENSIONS'))
        {
            return false;
        }
        if (!$grids_combinationmultiproduct_conf)
        {
            $ext = new ExtensionConvert();
            $ext->convert('combinationmultiproduct');
            if (!$grids_combinationmultiproduct_conf = simplexml_load_file(SC_TOOLS_DIR.'grids_combinationmultiproduct_conf.xml'))
            {
                return false;
            }
        }

        switch ($config){
            case 'gridConfig':
                global $sourceGridFormat;
                if (!empty($grids_combinationmultiproduct_conf->grids->grid->value))
                {
                    $sourceGridFormat = (string) $grids_combinationmultiproduct_conf->grids->grid->value;
                }
                break;
            case 'definition':
                global $combArray,$combination,$cols,$all_cols;
                foreach ($grids_combinationmultiproduct_conf->fields->field as $field)
                {
                    if (sc_in_array((string) $field->name, $all_cols, 'extension_allcols'))
                    {
                        eval((string) $field->definition);
                    }
                }
                break;
            case 'rowData':
                global $combArray,$combination,$cols,$all_cols,$product_attribute;
                foreach ($grids_combinationmultiproduct_conf->fields->field as $field)
                {
                    if (sc_in_array((string) $field->name, $cols, 'cols') && (trim((string) $field->rowData) != ''))
                    {
                        eval((string) $field->rowData);
                    }
                }
                break;
            case 'colSettings':
                global $colSettings;
                foreach ($grids_combinationmultiproduct_conf->fields->field as $field)
                {
                    if ((string) $field->filter == 'na')
                    {
                        $field->filter = '';
                    }
                    $footer = '';
                    $options = '';
                    if (!empty($colSettings[((string) $field->name)]))
                    {
                        $footer = $colSettings[((string) $field->name)]['footer'];
                        $options = $colSettings[((string) $field->name)]['options'];
                    }
                    if (trim((string) $field->footer) != '')
                    {
                        $footer = (string) $field->footer;
                    }
                    if (trim((string) $field->options) != '')
                    {
                        $options = eval((string) $field->options);
                    }
                    else
                    {
                        $answertype = ((string) $field->answertype);
                        if ($answertype != '')
                        {
                            if ($answertype == 'YESNO')
                            {
                                $options = array(0 => _l('No'), 1 => _l('Yes'));
                            }
                        }
                    }
                    $colSettings[((string) $field->name)] = array('text' => str_replace("'", "\'", (string) $field->text->fr),
                            'width' => ((string) $field->width),
                            'align' => ((string) $field->align),
                            'type' => ((string) $field->celltype),
                            'sort' => ((string) $field->sort),
                            'color' => ((string) $field->color),
                            'format' => $colSettings[((string) $field->name)]['format'], //((string) $field->format),
                            'filter' => ((string) $field->filter),
                            'footer' => $footer,
                            'options' => $options, );
                    $buildDefaultValue = ((string) $field->buildDefaultValue);
                    if ($buildDefaultValue != '')
                    {
                        $colSettings[((string) $field->name)]['buildDefaultValue'] = $buildDefaultValue;
                    }
                }
                break;
            case 'gridUserData':
                eval((string) $grids_combinationmultiproduct_conf->grids->grid->gridUserData);
                break;
            case 'rowUserData':
                global $cols,$all_cols;
                foreach ($grids_combinationmultiproduct_conf->fields->field as $field)
                {
                    if (sc_in_array((string) $field->name, $all_cols, 'extension_allcols'))
                    {
                        eval((string) $field->rowUserData);
                    }
                }
                break;
            case 'SQLSelectDataSelect':
                global $sql,$view,$cols,$all_cols,$id_lang;
                foreach ($grids_combinationmultiproduct_conf->fields->field as $field)
                {
                    if (sc_in_array((string) $field->name, $all_cols, 'extension_allcols'))
                    {
                        if (!empty($field->SQLSelectDataSelect) && strlen($field->SQLSelectDataSelect) > 0)
                        {
                            $sql .= eval((string) $field->SQLSelectDataSelect);
                        }
                        elseif (((string) $field->table) == 'product_attribute')
                        {
                            $sql .= ',pa.`'.bqSQL($field->name).'`';
                        }
                        elseif (((string) $field->table) == 'product_attribute_shop')
                        {
                            $sql .= ',pas.`'.bqSQL($field->name).'`';
                        }
                    }
                }
                break;
            case 'SQLSelectDataLeftJoin':
                global $sql,$view,$cols,$all_cols,$id_lang;
                foreach ($grids_combinationmultiproduct_conf->fields->field as $field)
                {
                    if (sc_in_array((string) $field->name, $all_cols, 'extension_allcols'))
                    {
                        $sql .= eval((string) $field->SQLSelectDataLeftJoin);
                    }
                }
                break;
            case 'onEditCell':
                echo (string) $grids_combinationmultiproduct_conf->grids->grid->onEditCell;
                foreach ($grids_combinationmultiproduct_conf->fields->field as $field)
                {
                    echo (string) $field->onEditCell;
                }
                break;
            case 'onBeforeUpdate':
                echo (string) $grids_combinationmultiproduct_conf->grids->grid->onBeforeUpdate;
                foreach ($grids_combinationmultiproduct_conf->fields->field as $field)
                {
                    echo (string) $field->onBeforeUpdate;
                }
                break;
            case 'onAfterUpdate':
                echo (string) $grids_combinationmultiproduct_conf->grids->grid->onAfterUpdate;
                foreach ($grids_combinationmultiproduct_conf->fields->field as $field)
                {
                    echo (string) $field->onAfterUpdate;
                }
                break;
            case 'onAfterUpdateSQL':
                global $idproduct,$id_product,$id_product_attribute,$id_lang;
                foreach ($grids_combinationmultiproduct_conf->fields->field as $field)
                {
                    eval((string) $field->onAfterUpdateSQL);
                }
                break;
            case 'onBeforeUpdateSQL':
                global $idproduct,$id_product,$id_product_attribute,$list_shop_fields,$todo_pa,$id_lang;
                foreach ($grids_combinationmultiproduct_conf->fields->field as $field)
                {
                    if (!empty($field->onBeforeUpdateSQL))
                    {
                        eval((string) $field->onBeforeUpdateSQL);
                    }
                    elseif (((string) $field->table) == 'product_attribute')
                    {
                        if (isset($_POST[(string) $field->name]))
                        {
                            $todo_pa[] = '`'.bqSQL($field->name)."`='".pSQL(Tools::getValue((string) $field->name))."'";
                        }
                    }
                    elseif (((string) $field->table) == 'product_attribute_shop')
                    {
                        $list_shop_fields .= ','.(string) $field->name;
                    }
                }
                break;
            case 'afterGetRows':
                foreach ($grids_combinationmultiproduct_conf->fields->field as $field)
                {
                    if (!empty($field->afterGetRows))
                    {
                        eval((string) $field->afterGetRows);
                    }
                }
                break;
        }
    }

    public static function readCustomPropSpePriceGridConfigXML($config, $extData = false)
    {
        global $grids_propspeprice_conf;
        $setextension = Tools::getValue('setextension', 1);
        if (!SC_TOOLS || !file_exists(SC_TOOLS_DIR.'grids_propspeprice_conf.xml') || !_s('CORE_USE_EXTENSIONS'))
        {
            return false;
        }
        if (!$grids_propspeprice_conf)
        {
            $ext = new ExtensionConvert();
            $ext->convert('propspeprice');
            if (!$grids_propspeprice_conf = simplexml_load_file(SC_TOOLS_DIR.'grids_propspeprice_conf.xml'))
            {
                return false;
            }
        }

        switch ($config){
            case 'gridConfig':
                global $sourceGridFormat;
                if (!empty($grids_propspeprice_conf->grids->grid->value))
                {
                    $sourceGridFormat = (string) $grids_propspeprice_conf->grids->grid->value;
                }
                break;
            case 'definition':
                global $combArray,$combinaison,$cols,$all_cols;
                foreach ($grids_propspeprice_conf->fields->field as $field)
                {
                    if (sc_in_array((string) $field->name, $all_cols, 'extension_allcols'))
                    {
                        eval((string) $field->definition);
                    }
                }
                break;
            case 'colSettings':
                global $colSettings;
                foreach ($grids_propspeprice_conf->fields->field as $field)
                {
                    if ((string) $field->filter == 'na')
                    {
                        $field->filter = '';
                    }
                    $footer = '';
                    $options = '';
                    if (!empty($colSettings[((string) $field->name)]))
                    {
                        $footer = $colSettings[((string) $field->name)]['footer'];
                        $options = $colSettings[((string) $field->name)]['options'];
                    }
                    if (trim((string) $field->footer) != '')
                    {
                        $footer = (string) $field->footer;
                    }
                    if (trim((string) $field->options) != '')
                    {
                        $options = eval((string) $field->options);
                    }
                    else
                    {
                        $answertype = ((string) $field->answertype);
                        if ($answertype != '')
                        {
                            if ($answertype == 'YESNO')
                            {
                                $options = array(0 => _l('No'), 1 => _l('Yes'));
                            }
                        }
                    }
                    $colSettings[((string) $field->name)] = array('text' => str_replace("'", "\'", (string) $field->text->fr),
                            'width' => ((string) $field->width),
                            'align' => ((string) $field->align),
                            'type' => ((string) $field->celltype),
                            'sort' => ((string) $field->sort),
                            'color' => ((string) $field->color),
                            'format' => $colSettings[((string) $field->name)]['format'], //((string) $field->format),
                            'filter' => ((string) $field->filter),
                            'footer' => $footer,
                            'options' => $options, );
                    $buildDefaultValue = ((string) $field->buildDefaultValue);
                    if ($buildDefaultValue != '')
                    {
                        $colSettings[((string) $field->name)]['buildDefaultValue'] = $buildDefaultValue;
                    }
                }
                break;
            case 'gridUserData':
                eval((string) $grids_propspeprice_conf->grids->grid->gridUserData);
                break;
            case 'rowUserData':
                global $cols,$all_cols;
                foreach ($grids_propspeprice_conf->fields->field as $field)
                {
                    if (sc_in_array((string) $field->name, $all_cols, 'extension_allcols'))
                    {
                        eval((string) $field->rowUserData);
                    }
                }
                break;
            case 'SQLSelectDataSelect':
                global $sql,$view,$cols,$all_cols;
                foreach ($grids_propspeprice_conf->fields->field as $field)
                {
                    if (sc_in_array((string) $field->name, $all_cols, 'extension_allcols'))
                    {
                        $sql .= eval((string) $field->SQLSelectDataSelect);
                    }
                }
                break;
            case 'SQLSelectDataLeftJoin':
                global $sql,$view,$cols,$all_cols;
                foreach ($grids_propspeprice_conf->fields->field as $field)
                {
                    if (sc_in_array((string) $field->name, $all_cols, 'extension_allcols'))
                    {
                        $sql .= eval((string) $field->SQLSelectDataLeftJoin);
                    }
                }
                break;
            case 'onEditCell':
                echo (string) $grids_propspeprice_conf->grids->grid->onEditCell;
                foreach ($grids_propspeprice_conf->fields->field as $field)
                {
                    echo (string) $field->onEditCell;
                }
                break;
            case 'onBeforeUpdate':
                echo (string) $grids_propspeprice_conf->grids->grid->onBeforeUpdate;
                foreach ($grids_propspeprice_conf->fields->field as $field)
                {
                    echo (string) $field->onBeforeUpdate;
                }
                break;
            case 'onAfterUpdate':
                echo (string) $grids_propspeprice_conf->grids->grid->onAfterUpdate;
                foreach ($grids_propspeprice_conf->fields->field as $field)
                {
                    echo (string) $field->onAfterUpdate;
                }
                break;
            case 'onAfterUpdateSQL':
                global $idproduct,$id_product,$id_product_attribute;
                foreach ($grids_propspeprice_conf->fields->field as $field)
                {
                    eval((string) $field->onAfterUpdateSQL);
                }
                break;
            case 'afterGetRows':
                foreach ($grids_propspeprice_conf->fields->field as $field)
                {
                    if (!empty($field->afterGetRows))
                    {
                        eval((string) $field->afterGetRows);
                    }
                }
                break;
        }
    }

    public static function readCustomPropCustomersGridConfigXML($config, $extData = false)
    {
        global $grids_propcustomers_conf;
        $setextension = Tools::getValue('setextension', 1);
        if (!SC_TOOLS || !file_exists(SC_TOOLS_DIR.'grids_propcustomers_conf.xml') || !_s('CORE_USE_EXTENSIONS'))
        {
            return false;
        }
        if (!$grids_propcustomers_conf)
        {
            $ext = new ExtensionConvert();
            $ext->convert('propcustomers');
            if (!$grids_propcustomers_conf = simplexml_load_file(SC_TOOLS_DIR.'grids_propcustomers_conf.xml'))
            {
                return false;
            }
        }

        switch ($config){
            case 'gridConfig':
                global $sourceGridFormat;
                if (!empty($grids_propcustomers_conf->grids->grid->value))
                {
                    $sourceGridFormat = (string) $grids_propcustomers_conf->grids->grid->value;
                }


                break;
            case 'definition':
                global $combArray,$combinaison,$cols,$all_cols;
                foreach ($grids_propcustomers_conf->fields->field as $field)
                {
                    if (sc_in_array((string) $field->name, $all_cols, 'extension_allcols'))
                    {
                        eval((string) $field->definition);
                    }
                }
                break;
            case 'colSettings':
                global $colSettings;

                foreach ($grids_propcustomers_conf->fields->field as $field)
                {

                    if ((string) $field->filter == 'na')
                    {
                        $field->filter = '';
                    }
                    $footer = '';
                    $options = '';
                    if (!empty($colSettings[((string) $field->name)]))
                    {
                        $footer = $colSettings[((string) $field->name)]['footer'];
                        $options = $colSettings[((string) $field->name)]['options'];
                    }
                    if (trim((string) $field->footer) != '')
                    {
                        $footer = (string) $field->footer;
                    }
                    if (trim((string) $field->options) != '')
                    {
                        $options = eval((string) $field->options);
                    }
                    else
                    {
                        $answertype = ((string) $field->answertype);
                        if ($answertype != '')
                        {
                            if ($answertype == 'YESNO')
                            {
                                $options = array(0 => _l('No'), 1 => _l('Yes'));
                            }
                        }
                    }
                    $colSettings[((string) $field->name)] = array('text' => str_replace("'", "\'", (string) $field->text->fr),
                        'width' => ((string) $field->width),
                        'align' => ((string) $field->align),
                        'type' => ((string) $field->celltype),
                        'sort' => ((string) $field->sort),
                        'color' => ((string) $field->color),
                        'format' => $colSettings[((string) $field->name)]['format'], //((string) $field->format),
                        'filter' => ((string) $field->filter),
                        'footer' => $footer,
                        'options' => $options, );
                    $buildDefaultValue = ((string) $field->buildDefaultValue);
                    if ($buildDefaultValue != '')
                    {
                        $colSettings[((string) $field->name)]['buildDefaultValue'] = $buildDefaultValue;
                    }
                }
                break;
            case 'gridUserData':
                eval((string) $grids_propcustomers_conf->grids->grid->gridUserData);
                break;
            case 'rowUserData':
                global $cols,$all_cols;
                foreach ($grids_propcustomers_conf->fields->field as $field)
                {
                    if (sc_in_array((string) $field->name, $all_cols, 'extension_allcols'))
                    {
                        eval((string) $field->rowUserData);
                    }
                }
                break;
            case 'SQLSelectDataSelect':
                global $sql,$view,$cols,$all_cols;
                foreach ($grids_propcustomers_conf->fields->field as $field)
                {
                    if (sc_in_array((string) $field->name, $all_cols, 'extension_allcols'))
                    {
                        $sql .= eval((string) $field->SQLSelectDataSelect);
                    }
                }
                break;
            case 'SQLSelectDataLeftJoin':
                global $sql,$view,$cols,$all_cols;
                foreach ($grids_propcustomers_conf->fields->field as $field)
                {
                    if (sc_in_array((string) $field->name, $all_cols, 'extension_allcols'))
                    {
                        $sql .= eval((string) $field->SQLSelectDataLeftJoin);
                    }
                }
                break;
            case 'onEditCell':
                echo (string) $grids_propcustomers_conf->grids->grid->onEditCell;
                foreach ($grids_propcustomers_conf->fields->field as $field)
                {
                    echo (string) $field->onEditCell;
                }
                break;
            case 'onBeforeUpdate':
                echo (string) $grids_propcustomers_conf->grids->grid->onBeforeUpdate;
                foreach ($grids_propcustomers_conf->fields->field as $field)
                {
                    echo (string) $field->onBeforeUpdate;
                }
                break;
            case 'onAfterUpdate':
                echo (string) $grids_propcustomers_conf->grids->grid->onAfterUpdate;
                foreach ($grids_propcustomers_conf->fields->field as $field)
                {
                    echo (string) $field->onAfterUpdate;
                }
                break;
            case 'onAfterUpdateSQL':
                global $idproduct,$id_product,$id_product_attribute;
                foreach ($grids_propcustomers_conf->fields->field as $field)
                {
                    eval((string) $field->onAfterUpdateSQL);
                }
                break;
            case 'afterGetRows':
                foreach ($grids_propcustomers_conf->fields->field as $field)
                {
                    if (!empty($field->afterGetRows))
                    {
                        eval((string) $field->afterGetRows);
                    }
                }
                break;
        }
    }

    public static function readCustomWinSpePriceGridConfigXML($config, $extData = false)
    {
        global $grids_winspeprice_conf;
        $setextension = Tools::getValue('setextension', 1);
        if (!SC_TOOLS || !file_exists(SC_TOOLS_DIR.'grids_winspeprice_conf.xml') || !_s('CORE_USE_EXTENSIONS'))
        {
            return false;
        }
        if (!$grids_winspeprice_conf)
        {
            $ext = new ExtensionConvert();
            $ext->convert('winspeprice');
            if (!$grids_winspeprice_conf = simplexml_load_file(SC_TOOLS_DIR.'grids_winspeprice_conf.xml'))
            {
                return false;
            }
        }

        switch ($config){
            case 'gridConfig':
                global $sourceGridFormat;
                if (!empty($grids_winspeprice_conf->grids->grid->value))
                {
                    $sourceGridFormat = (string) $grids_winspeprice_conf->grids->grid->value;
                }
                break;
            case 'definition':
                global $combArray,$combinaison,$cols,$all_cols;
                foreach ($grids_winspeprice_conf->fields->field as $field)
                {
                    if (sc_in_array((string) $field->name, $all_cols, 'extension_allcols'))
                    {
                        eval((string) $field->definition);
                    }
                }
                break;
            case 'colSettings':
                global $colSettings;
                foreach ($grids_winspeprice_conf->fields->field as $field)
                {
                    if ((string) $field->filter == 'na')
                    {
                        $field->filter = '';
                    }
                    $footer = '';
                    $options = '';
                    if (!empty($colSettings[((string) $field->name)]))
                    {
                        $footer = $colSettings[((string) $field->name)]['footer'];
                        $options = $colSettings[((string) $field->name)]['options'];
                    }
                    if (trim((string) $field->footer) != '')
                    {
                        $footer = (string) $field->footer;
                    }
                    if (trim((string) $field->options) != '')
                    {
                        $options = eval((string) $field->options);
                    }
                    else
                    {
                        $answertype = ((string) $field->answertype);
                        if ($answertype != '')
                        {
                            if ($answertype == 'YESNO')
                            {
                                $options = array(0 => _l('No'), 1 => _l('Yes'));
                            }
                        }
                    }
                    $colSettings[((string) $field->name)] = array('text' => str_replace("'", "\'", (string) $field->text->fr),
                            'width' => ((string) $field->width),
                            'align' => ((string) $field->align),
                            'type' => ((string) $field->celltype),
                            'sort' => ((string) $field->sort),
                            'color' => ((string) $field->color),
                            'format' => $colSettings[((string) $field->name)]['format'], //((string) $field->format),
                            'filter' => ((string) $field->filter),
                            'footer' => $footer,
                            'options' => $options, );
                    $buildDefaultValue = ((string) $field->buildDefaultValue);
                    if ($buildDefaultValue != '')
                    {
                        $colSettings[((string) $field->name)]['buildDefaultValue'] = $buildDefaultValue;
                    }
                }
                break;
            case 'gridUserData':
                eval((string) $grids_winspeprice_conf->grids->grid->gridUserData);
                break;
            case 'rowUserData':
                global $cols,$all_cols;
                foreach ($grids_winspeprice_conf->fields->field as $field)
                {
                    if (sc_in_array((string) $field->name, $all_cols, 'extension_allcols'))
                    {
                        eval((string) $field->rowUserData);
                    }
                }
                break;
            case 'SQLSelectDataSelect':
                global $sql,$view,$cols,$all_cols;
                foreach ($grids_winspeprice_conf->fields->field as $field)
                {
                    if (sc_in_array((string) $field->name, $all_cols, 'extension_allcols'))
                    {
                        $sql .= eval((string) $field->SQLSelectDataSelect);
                    }
                }
                break;
            case 'SQLSelectDataLeftJoin':
                global $sql,$view,$cols,$all_cols;
                foreach ($grids_winspeprice_conf->fields->field as $field)
                {
                    if (sc_in_array((string) $field->name, $all_cols, 'extension_allcols'))
                    {
                        $sql .= eval((string) $field->SQLSelectDataLeftJoin);
                    }
                }
                break;
            case 'onEditCell':
                echo (string) $grids_winspeprice_conf->grids->grid->onEditCell;
                foreach ($grids_winspeprice_conf->fields->field as $field)
                {
                    echo (string) $field->onEditCell;
                }
                break;
            case 'onBeforeUpdate':
                echo (string) $grids_winspeprice_conf->grids->grid->onBeforeUpdate;
                foreach ($grids_winspeprice_conf->fields->field as $field)
                {
                    echo (string) $field->onBeforeUpdate;
                }
                break;
            case 'onAfterUpdate':
                echo (string) $grids_winspeprice_conf->grids->grid->onAfterUpdate;
                foreach ($grids_winspeprice_conf->fields->field as $field)
                {
                    echo (string) $field->onAfterUpdate;
                }
                break;
            case 'onAfterUpdateSQL':
                global $id_product,$id_specific_price;
                foreach ($grids_winspeprice_conf->fields->field as $field)
                {
                    eval((string) $field->onAfterUpdateSQL);
                }
                break;
            case 'afterGetRows':
                foreach ($grids_winspeprice_conf->fields->field as $field)
                {
                    if (!empty($field->afterGetRows))
                    {
                        eval((string) $field->afterGetRows);
                    }
                }
                break;
        }
    }

    public static function readCustomPropSupplierGridConfigXML($config, $extData = false)
    {
        global $grids_propsupplier_conf;
        $setextension = Tools::getValue('setextension', 1);
        if (!SC_TOOLS || !file_exists(SC_TOOLS_DIR.'grids_propsupplier_conf.xml') || !_s('CORE_USE_EXTENSIONS'))
        {
            return false;
        }
        if (!$grids_propsupplier_conf)
        {
            $ext = new ExtensionConvert();
            $ext->convert('propsupplier');
            if (!$grids_propsupplier_conf = simplexml_load_file(SC_TOOLS_DIR.'grids_propsupplier_conf.xml'))
            {
                return false;
            }
        }

        switch ($config){
            case 'gridConfig':
                global $sourceGridFormat;
                if (!empty($grids_propsupplier_conf->grids->grid->value))
                {
                    $sourceGridFormat = (string) $grids_propsupplier_conf->grids->grid->value;
                }
                break;
            case 'definition':
                global $combArray,$combinaison,$cols,$all_cols;
                foreach ($grids_propsupplier_conf->fields->field as $field)
                {
                    if (sc_in_array((string) $field->name, $all_cols, 'extension_allcols'))
                    {
                        eval((string) $field->definition);
                    }
                }
                break;
            case 'colSettings':
                global $colSettings;
                foreach ($grids_propsupplier_conf->fields->field as $field)
                {
                    if ((string) $field->filter == 'na')
                    {
                        $field->filter = '';
                    }
                    $footer = '';
                    $options = '';
                    if (!empty($colSettings[((string) $field->name)]))
                    {
                        $footer = $colSettings[((string) $field->name)]['footer'];
                        $options = $colSettings[((string) $field->name)]['options'];
                    }
                    if (trim((string) $field->footer) != '')
                    {
                        $footer = (string) $field->footer;
                    }
                    if (trim((string) $field->options) != '')
                    {
                        $options = eval((string) $field->options);
                    }
                    else
                    {
                        $answertype = ((string) $field->answertype);
                        if ($answertype != '')
                        {
                            if ($answertype == 'YESNO')
                            {
                                $options = array(0 => _l('No'), 1 => _l('Yes'));
                            }
                        }
                    }
                    $colSettings[((string) $field->name)] = array('text' => str_replace("'", "\'", (string) $field->text->fr),
                        'width' => ((string) $field->width),
                        'align' => ((string) $field->align),
                        'type' => ((string) $field->celltype),
                        'sort' => ((string) $field->sort),
                        'color' => ((string) $field->color),
                        'format' => $colSettings[((string) $field->name)]['format'], //((string) $field->format),
                        'filter' => ((string) $field->filter),
                        'footer' => $footer,
                        'options' => $options, );
                    $buildDefaultValue = ((string) $field->buildDefaultValue);
                    if ($buildDefaultValue != '')
                    {
                        $colSettings[((string) $field->name)]['buildDefaultValue'] = $buildDefaultValue;
                    }
                }
                break;
            case 'gridUserData':
                eval((string) $grids_propsupplier_conf->grids->grid->gridUserData);
                break;
            case 'rowUserData':
                global $cols,$all_cols;
                foreach ($grids_propsupplier_conf->fields->field as $field)
                {
                    if (sc_in_array((string) $field->name, $all_cols, 'extension_allcols'))
                    {
                        eval((string) $field->rowUserData);
                    }
                }
                break;
            case 'rowData':
                global $cols,$id_lang,$row,$id_product_list;
                foreach ($grids_propsupplier_conf->fields->field as $field)
                {
                    if (sc_in_array((string) $field->name, $cols, 'extension_cols'))
                    {
                        eval((string) $field->rowData);
                    }
                }
                break;
            case 'SQLSelectDataSelect':
                global $sql,$view,$cols,$all_cols;
                foreach ($grids_propsupplier_conf->fields->field as $field)
                {
                    if (sc_in_array((string) $field->name, $all_cols, 'extension_allcols'))
                    {
                        $sql .= eval((string) $field->SQLSelectDataSelect);
                    }
                }
                break;
            case 'SQLSelectDataLeftJoin':
                global $sql,$view,$cols,$all_cols;
                foreach ($grids_propsupplier_conf->fields->field as $field)
                {
                    if (sc_in_array((string) $field->name, $all_cols, 'extension_allcols'))
                    {
                        $sql .= eval((string) $field->SQLSelectDataLeftJoin);
                    }
                }
                break;
            case 'onEditCell':
                echo (string) $grids_propsupplier_conf->grids->grid->onEditCell;
                foreach ($grids_propsupplier_conf->fields->field as $field)
                {
                    echo (string) $field->onEditCell;
                }
                break;
            case 'onBeforeUpdate':
                echo (string) $grids_propsupplier_conf->grids->grid->onBeforeUpdate;
                foreach ($grids_propsupplier_conf->fields->field as $field)
                {
                    echo (string) $field->onBeforeUpdate;
                }
                break;
            case 'onAfterUpdate':
                echo (string) $grids_propsupplier_conf->grids->grid->onAfterUpdate;
                foreach ($grids_propsupplier_conf->fields->field as $field)
                {
                    echo (string) $field->onAfterUpdate;
                }
                break;
            case 'onAfterUpdateSQL':
                global $field_from_payload,$id_product,$id_supplier;
                foreach ($grids_propsupplier_conf->fields->field as $field)
                {
                    eval((string) $field->onAfterUpdateSQL);
                }
                break;
            case 'afterGetRows':
                foreach ($grids_propsupplier_conf->fields->field as $field)
                {
                    if (!empty($field->afterGetRows))
                    {
                        eval((string) $field->afterGetRows);
                    }
                }
                break;
        }
    }

    public static function readCustomproppackproductGridConfigXML($config, $extData = false)
    {
        global $grids_proppackproduct_conf;
        $setextension = Tools::getValue('setextension', 1);
        if (!SC_TOOLS || !file_exists(SC_TOOLS_DIR.'grids_proppackproduct_conf.xml') || !_s('CORE_USE_EXTENSIONS'))
        {
            return false;
        }
        if (!$grids_proppackproduct_conf)
        {
            $ext = new ExtensionConvert();
            $ext->convert('proppackproduct');
            if (!$grids_proppackproduct_conf = simplexml_load_file(SC_TOOLS_DIR.'grids_proppackproduct_conf.xml'))
            {
                return false;
            }
        }

        switch ($config){
            case 'gridConfig':
                global $sourceGridFormat;
                foreach ($grids_proppackproduct_conf->grids->grid as $grid)
                {
                    $sourceGridFormat[((string) $grid->name)] = ((string) $grid->value);
                }
                break;
            case 'definition':
                global $combArray,$combinaison,$cols,$all_cols;
                foreach ($grids_proppackproduct_conf->fields->field as $field)
                {
                    if (sc_in_array((string) $field->name, $all_cols, 'extension_allcols'))
                    {
                        eval((string) $field->definition);
                    }
                }
                break;
            case 'colSettings':
                global $colSettings;
                foreach ($grids_proppackproduct_conf->fields->field as $field)
                {
                    if ((string) $field->filter == 'na')
                    {
                        $field->filter = '';
                    }
                    $footer = '';
                    $options = '';
                    if (!empty($colSettings[((string) $field->name)]))
                    {
                        $footer = $colSettings[((string) $field->name)]['footer'];
                        $options = $colSettings[((string) $field->name)]['options'];
                    }
                    if (trim((string) $field->footer) != '')
                    {
                        $footer = (string) $field->footer;
                    }
                    if (trim((string) $field->options) != '')
                    {
                        $options = eval((string) $field->options);
                    }
                    else
                    {
                        $answertype = ((string) $field->answertype);
                        if ($answertype != '')
                        {
                            if ($answertype == 'YESNO')
                            {
                                $options = array(0 => _l('No'), 1 => _l('Yes'));
                            }
                        }
                    }
                    $colSettings[((string) $field->name)] = array('text' => str_replace("'", "\'", (string) $field->text->fr),
                        'width' => ((string) $field->width),
                        'align' => ((string) $field->align),
                        'type' => ((string) $field->celltype),
                        'sort' => ((string) $field->sort),
                        'color' => ((string) $field->color),
                        'format' => $colSettings[((string) $field->name)]['format'], //((string) $field->format),
                        'filter' => ((string) $field->filter),
                        'footer' => $footer,
                        'options' => $options, );
                    $buildDefaultValue = ((string) $field->buildDefaultValue);
                    if ($buildDefaultValue != '')
                    {
                        $colSettings[((string) $field->name)]['buildDefaultValue'] = $buildDefaultValue;
                    }
                }
                break;
            case 'gridUserData':
                eval((string) $grids_proppackproduct_conf->grids->grid->gridUserData);
                break;
            case 'rowUserData':
                global $cols,$all_cols;
                foreach ($grids_proppackproduct_conf->fields->field as $field)
                {
                    if (sc_in_array((string) $field->name, $all_cols, 'extension_allcols'))
                    {
                        eval((string) $field->rowUserData);
                    }
                }
                break;
            case 'rowData':
                global $cols,$id_lang,$row,$id_product_list;
                foreach ($grids_proppackproduct_conf->fields->field as $field)
                {
                    if (sc_in_array((string) $field->name, $cols, 'extension_cols'))
                    {
                        eval((string) $field->rowData);
                    }
                }
                break;
            case 'SQLSelectDataSelect':
                global $sql,$view,$cols,$all_cols;
                foreach ($grids_proppackproduct_conf->fields->field as $field)
                {
                    if (sc_in_array((string) $field->name, $all_cols, 'extension_allcols'))
                    {
                        $sql .= eval((string) $field->SQLSelectDataSelect);
                    }
                }
                break;
            case 'SQLSelectDataLeftJoin':
                global $sql,$view,$cols,$all_cols;
                foreach ($grids_proppackproduct_conf->fields->field as $field)
                {
                    if (sc_in_array((string) $field->name, $all_cols, 'extension_allcols'))
                    {
                        $sql .= eval((string) $field->SQLSelectDataLeftJoin);
                    }
                }
                break;
            case 'onEditCell':
                echo (string) $grids_proppackproduct_conf->grids->grid->onEditCell;
                foreach ($grids_proppackproduct_conf->fields->field as $field)
                {
                    echo (string) $field->onEditCell;
                }
                break;
            case 'onBeforeUpdate':
                echo (string) $grids_proppackproduct_conf->grids->grid->onBeforeUpdate;
                foreach ($grids_proppackproduct_conf->fields->field as $field)
                {
                    echo (string) $field->onBeforeUpdate;
                }
                break;
            case 'onAfterUpdate':
                echo (string) $grids_proppackproduct_conf->grids->grid->onAfterUpdate;
                foreach ($grids_proppackproduct_conf->fields->field as $field)
                {
                    echo (string) $field->onAfterUpdate;
                }
                break;
            case 'onAfterUpdateSQL':
                global $field_from_payload,$id_product,$id_supplier;
                foreach ($grids_proppackproduct_conf->fields->field as $field)
                {
                    eval((string) $field->onAfterUpdateSQL);
                }
                break;
            case 'afterGetRows':
                foreach ($grids_proppackproduct_conf->fields->field as $field)
                {
                    if (!empty($field->afterGetRows))
                    {
                        eval((string) $field->afterGetRows);
                    }
                }
                break;
        }
    }

    public static function readCustomGMAPartnerGridConfigXML($config, $extData = false)
    {
        global $grids_gmapartner_conf;
        $setextension = Tools::getValue('setextension', 1);
        if (!SC_TOOLS || !file_exists(SC_TOOLS_DIR.'grids_gmapartner_conf.xml') || !_s('CORE_USE_EXTENSIONS'))
        {
            return false;
        }
        if (!$grids_gmapartner_conf)
        {
            $ext = new ExtensionConvert();
            $ext->convert('gmapartner');
            if (!$grids_gmapartner_conf = simplexml_load_file(SC_TOOLS_DIR.'grids_gmapartner_conf.xml'))
            {
                return false;
            }
        }

        switch ($config){
            case 'gridConfig':
                global $sourceGridFormat;
                if (!empty($grids_gmapartner_conf->grids->grid->value))
                {
                    $sourceGridFormat = (string) $grids_gmapartner_conf->grids->grid->value;
                }
                break;
            case 'definition':
                global $cols,$all_cols;
                foreach ($grids_gmapartner_conf->fields->field as $field)
                {
                    if (sc_in_array((string) $field->name, $all_cols, 'extension_allcols'))
                    {
                        eval((string) $field->definition);
                    }
                }
                break;
            case 'colSettings':
                global $colSettings;
                foreach ($grids_gmapartner_conf->fields->field as $field)
                {
                    if ((string) $field->filter == 'na')
                    {
                        $field->filter = '';
                    }
                    $footer = '';
                    $options = '';
                    if (!empty($colSettings[((string) $field->name)]))
                    {
                        $footer = $colSettings[((string) $field->name)]['footer'];
                        $options = $colSettings[((string) $field->name)]['options'];
                    }
                    if (trim((string) $field->footer) != '')
                    {
                        $footer = (string) $field->footer;
                    }
                    if (trim((string) $field->options) != '')
                    {
                        $options = eval((string) $field->options);
                    }
                    else
                    {
                        $answertype = ((string) $field->answertype);
                        if ($answertype != '')
                        {
                            if ($answertype == 'YESNO')
                            {
                                $options = array(0 => _l('No'), 1 => _l('Yes'));
                            }
                        }
                    }
                    $colSettings[((string) $field->name)] = array('text' => str_replace("'", "\'", (string) $field->text->fr),
                        'width' => ((string) $field->width),
                        'align' => ((string) $field->align),
                        'type' => ((string) $field->celltype),
                        'sort' => ((string) $field->sort),
                        'color' => ((string) $field->color),
                        'format' => $colSettings[((string) $field->name)]['format'], //((string) $field->format),
                        'filter' => ((string) $field->filter),
                        'footer' => $footer,
                        'options' => $options, );
                    $buildDefaultValue = ((string) $field->buildDefaultValue);
                    if ($buildDefaultValue != '')
                    {
                        $colSettings[((string) $field->name)]['buildDefaultValue'] = $buildDefaultValue;
                    }
                }
                break;
            case 'rowData':
                global $col, $row, $partnerCustomer, $color_coupon, $invoice, $total;
                foreach ($grids_gmapartner_conf->fields->field as $field)
                {
                    if ((trim((string) $field->rowData) != ''))
                    {
                        eval((string) $field->rowData);
                    }
                }
                break;
            case 'gridUserData':
                eval((string) $grids_gmapartner_conf->grids->grid->gridUserData);
                break;
            case 'rowUserData':
                global $cols,$all_cols;
                foreach ($grids_gmapartner_conf->fields->field as $field)
                {
                    if (sc_in_array((string) $field->name, $all_cols, 'extension_allcols'))
                    {
                        eval((string) $field->rowUserData);
                    }
                }
                break;
            case 'SQLSelectDataSelect':
                global $sql,$view,$cols,$all_cols;
                foreach ($grids_gmapartner_conf->fields->field as $field)
                {
                    if (sc_in_array((string) $field->name, $all_cols, 'extension_allcols'))
                    {
                        $sql .= eval((string) $field->SQLSelectDataSelect);
                    }
                }
                break;
            case 'SQLSelectDataLeftJoin':
                global $sql,$view,$cols,$all_cols;
                foreach ($grids_gmapartner_conf->fields->field as $field)
                {
                    if (sc_in_array((string) $field->name, $all_cols, 'extension_allcols'))
                    {
                        $sql .= eval((string) $field->SQLSelectDataLeftJoin);
                    }
                }
                break;
            case 'onEditCell':
                echo (string) $grids_gmapartner_conf->grids->grid->onEditCell;
                foreach ($grids_gmapartner_conf->fields->field as $field)
                {
                    echo (string) $field->onEditCell;
                }
                break;
            case 'onBeforeUpdate':
                echo (string) $grids_gmapartner_conf->grids->grid->onBeforeUpdate;
                foreach ($grids_gmapartner_conf->fields->field as $field)
                {
                    echo (string) $field->onBeforeUpdate;
                }
                break;
            case 'onAfterUpdate':
                echo (string) $grids_gmapartner_conf->grids->grid->onAfterUpdate;
                foreach ($grids_gmapartner_conf->fields->field as $field)
                {
                    echo (string) $field->onAfterUpdate;
                }
                break;
            case 'onAfterUpdateSQL':
                global $id_partner;
                foreach ($grids_gmapartner_conf->fields->field as $field)
                {
                    eval((string) $field->onAfterUpdateSQL);
                }
                break;
            case 'afterGetRows':
                foreach ($grids_gmapartner_conf->fields->field as $field)
                {
                    if (!empty($field->afterGetRows))
                    {
                        eval((string) $field->afterGetRows);
                    }
                }
                break;
        }
    }

    public static function readCustomCMSGridsConfigXML($config, $extData = false)
    {
        global $grids_cms_conf;
        $setextension = Tools::getValue('setextension', 1);
        if (!SC_TOOLS || !file_exists(SC_TOOLS_DIR.'grids_cms_conf.xml') || !_s('CORE_USE_EXTENSIONS'))
        {
            return false;
        }

        if (!$grids_cms_conf)
        {
            $extConvert = new ExtensionConvert();
            $extConvert->convert('cms');
            if (!$grids_cms_conf = simplexml_load_file(SC_TOOLS_DIR.'grids_cms_conf.xml'))
            {
                return false;
            }
        }
        switch ($config){
            case 'permissions':
                global $permissions_list;
                $commun_list = array('grid_light', 'grid_large', 'grid_seo');

                foreach ($grids_cms_conf->grids->grid as $grid)
                {
                    if (!sc_in_array((string) $grid->name, $commun_list, 'extension_commun_list_cms'))
                    {
                        $permissions_list['GRI_CMS_VIEW_GRID_EXT_'.$grid->name] = array('id' => 'GRI_CMS_VIEW_GRID_EXT_'.$grid->name, 'section1' => 'Grid', 'section2' => 'CMS', 'name' => 'CMS grid:'.' '.(string) $grid->text->fr, 'description' => '', 'default_admin' => 1, 'default_value' => 1);
                    }
                }
                break;
            case 'gridnames':
                foreach ($grids_cms_conf->grids->grid as $grid)
                {
                    $add = true;
                    if ($grid->name == 'grid_light' && !_r('GRI_CAT_VIEW_GRID_LIGHT'))
                    {
                        $add = false;
                    }
                    elseif ($grid->name == 'grid_large' && !_r('GRI_CMS_VIEW_GRID_LARGE'))
                    {
                        $add = false;
                    }
                    elseif ($grid->name == 'grid_seo' && !_r('GRI_CMS_VIEW_GRID_SEO'))
                    {
                        $add = false;
                    }
                    elseif (!_r('GRI_CUS_VIEW_GRID_EXT_'.$grid->name))
                    {
                        $add = false;
                    }

                    if ($add == true)
                    {
                        echo "gridnames['".((string) $grid->name)."']='".str_replace("'", "\'", (string) $grid->text->fr)."';";
                    }
                }
                break;
            case 'toolbar':
                $lines = array();
                foreach ($grids_cms_conf->grids->grid as $grid)
                {
                    $lines[] = "['".((string) $grid->name)."', 'obj', gridnames['".((string) $grid->name)."'], '']";
                }
                echo(count($lines) ? ',' : '').join(',', $lines);
                break;
            case 'gridConfig':
                global $grids;
                foreach ($grids_cms_conf->grids->grid as $grid)
                {
                    $grids[((string) $grid->name)] = ((string) $grid->value);
                }
                break;
            case 'colSettings':
                global $colSettings;
                foreach ($grids_cms_conf->fields->field as $field)
                {
                    if ((string) $field->filter == 'na')
                    {
                        $field->filter = '';
                    }
                    $footer = '';
                    $options = '';
                    if (!empty($colSettings[((string) $field->name)]))
                    {
                        $footer = $colSettings[((string) $field->name)]['footer'];
                        $options = $colSettings[((string) $field->name)]['options'];
                    }
                    if (trim((string) $field->footer) != '')
                    {
                        $footer = (string) $field->footer;
                    }
                    if (trim((string) $field->options) != '')
                    {
                        $options = eval((string) $field->options);
                    }
                    else
                    {
                        $answertype = ((string) $field->answertype);
                        if ($answertype != '')
                        {
                            if ($answertype == 'YESNO')
                            {
                                $options = array(0 => _l('No'), 1 => _l('Yes'));
                            }
                        }
                    }
                    $colSettings[((string) $field->name)] = array('text' => str_replace("'", "\'", (string) $field->text->fr),
                        'width' => ((string) $field->width),
                        'align' => ((string) $field->align),
                        'type' => ((string) $field->celltype),
                        'sort' => ((string) $field->sort),
                        'color' => ((string) $field->color),
                        'format' => $colSettings[((string) $field->name)]['format'], //((string) $field->format),
                        'filter' => ((string) $field->filter),
                        'footer' => $footer,
                        'options' => $options, );
                    $buildDefaultValue = ((string) $field->buildDefaultValue);
                    if ($buildDefaultValue != '')
                    {
                        $colSettings[((string) $field->name)]['buildDefaultValue'] = $buildDefaultValue;
                    }
                }
                break;
            case 'rowData':
                global $fields_customer,$fields,$col,$cols,$id_lang,$gridrow;
                foreach ($grids_cms_conf->fields->field as $field)
                {
                    if (sc_in_array((string) $field->name, $cols, 'extension_cols'))
                    {
                        eval((string) $field->rowData);
                    }
                }
                break;
            case 'SQLSelectDataSelect':
                global $sql,$view,$cols;
                foreach ($grids_cms_conf->fields->field as $field)
                {
                    if (sc_in_array((string) $field->name, $cols, 'extension_cols'))
                    {
                        $sql .= eval((string) $field->SQLSelectDataSelect);
                    }
                }
                break;
            case 'SQLSelectDataLeftJoin':
                global $sql,$view,$cols;
                foreach ($grids_cms_conf->fields->field as $field)
                {
                    if (sc_in_array((string) $field->name, $cols, 'extension_cols'))
                    {
                        $sql .= eval((string) $field->SQLSelectDataLeftJoin);
                    }
                }
                break;
            case 'onEditCell':
                foreach ($grids_cms_conf->grids->grid as $grid)
                {
                    echo (string) $grid->onEditCell;
                }
                foreach ($grids_cms_conf->fields->field as $field)
                {
                    echo (string) $field->onEditCell;
                }
                break;
            case 'onAfterUpdateSQL':
                global $id_cms;
                foreach ($grids_cms_conf->fields->field as $field)
                {
                    eval((string) $field->onAfterUpdateSQL);
                }
                break;
            case 'onBeforeUpdate':
                foreach ($grids_cms_conf->grids->grid as $grid)
                {
                    echo (string) $grid->onBeforeUpdate;
                }
                foreach ($grids_cms_conf->fields->field as $field)
                {
                    echo (string) $field->onBeforeUpdate;
                }
                break;
            case 'gridUserData':
                foreach ($grids_cms_conf->grids->grid as $grid)
                {
                    eval((string) $grid->gridUserData);
                }
                break;
            case 'rowUserData':
                global $cols;
                foreach ($grids_cms_conf->fields->field as $field)
                {
                    if (sc_in_array((string) $field->name, $cols, 'extension_cols'))
                    {
                        eval((string) $field->rowUserData);
                    }
                }
                break;
            case 'extraVars':
                global $action,$extraVars,$cols;
                foreach ($grids_cms_conf->grids->grid as $grid)
                {
                    eval((string) $grid->extraVars);
                }
                foreach ($grids_cms_conf->fields->field as $field)
                {
                    eval((string) $field->extraVars);
                }
                break;
            case 'afterGetRows':
                foreach ($grids_cms_conf->fields->field as $field)
                {
                    if (!empty($field->afterGetRows))
                    {
                        eval((string) $field->afterGetRows);
                    }
                }
                break;
        }
    }

    public static function readCatImportCSVConfigXML($config)
    {
    }

    public static function addNewField($type, $field, $view = '')
    {
        if (!empty($type) && !empty($field))
        {
            $file = SC_TOOLS_DIR.'grids_'.$type.'_conf.xml';

            // UPDATE FIELD
            $dom = new DOMDocument();
            $dom->preserveWhiteSpace = false;
            $dom->formatOutput = true;
            $dom->load($file);

            $alone_grids = array('combinations', 'productsort', 'msproduct', 'mscombination', 'image');
            if (sc_in_array($type, $alone_grids, 'extension_alone_grids'))
            {
                $nodeGridList = $dom->getElementsByTagname('grid');
                foreach ($nodeGridList as $nodeGrid)
                {
                    $nodeText = $nodeGrid->getElementsByTagname('value')->item(0);
                    $value = $nodeText->nodeValue.','.$field;
                    $nodeText->nodeValue = '';
                    $v = $nodeText->ownerDocument->createCDATASection($value);
                    $nodeText->appendChild($v);
                }
                $dom->save($file);
            }

            $content = file_get_contents($file);
            $content = str_replace('<grids/>', '<grids></grids>', $content);
            $content = str_replace('<fields/>', '<fields></fields>', $content);
            file_put_contents($file, $content);
        }
    }
}
