<?php

$tab = Tools::getValue('tab');
$tab = str_replace('_import', '', $tab);
$id_lang = (int) Tools::getValue('id_lang');
$content = Tools::getValue('content');
$languages = Language::getLanguages(!_s('CAT_PROD_LANGUAGE_ALL'));
$return = '';
$id_item = 0;
$error = false;

$content = trim($content, '"');

if (!empty($content))
{
    $data_languages = array();
    $i = 1;
    foreach ($languages as $lang)
    {
        $data_languages[$i] = (int) $lang['id_lang'];
        ++$i;
    }
    $data_languages_count = count($data_languages);

    $content = explode('\n', $content);

    ## si la dernière entrée est vide,
    ## on supprime la ligne pour éviter tout problème
    foreach ($content as $k => $row)
    {
        if (empty($row))
        {
            unset($content[$k]);
        }
    }

    $data_to_import = array();
    foreach ($content as $key => $row)
    {
        if ($key > 0)
        {
            $first_row_count_col = substr_count($content[0], '\t');
            $actual_row_count_col = substr_count($row, '\t');

            if ($actual_row_count_col !== $first_row_count_col)
            {
                $error = true;
                $return = _l('The number of fields does not match the export', 1);
            }
        }

        if (!empty($row))
        {
            $row = explode('\t', $row);
            $count_data_row = count($row) - 1;
            if ($tab == 'group_attribute')
            {
                $count_data_row = $count_data_row / 2;
            }
            if ($count_data_row !== $data_languages_count)
            {
                $error = true;
                $return = _l('The number of fields does not match the number of languages', 1);
            }
            $data_to_import[] = $row;
        }
    }

    if (!$error)
    {
        unset($data_to_import[0]);

        switch ($tab) {
            case 'group_feature':
                $actionDone = 0;
                $increment = 0;
                foreach ($data_to_import as $row)
                {
                    foreach ($data_languages as $key => $id_lang)
                    {
                        if (!empty($row[0]))
                        {
                            $increment++;
                            $actionDone += (int)Db::getInstance()->execute('UPDATE '._DB_PREFIX_."feature_lang
                                SET name='".pSQL($row[$key])."'
                                WHERE id_feature = ".(int) $row[0].'
                                AND id_lang = '.(int) $id_lang);
                        }
                    }
                }
                if ($actionDone === $increment)
                {
                    $return = _l('Translation for feature groups updated', 1);
                    $id_item = 0;
                }
            break;
            case 'feature_value':
                Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'temp_translate_feature_value');
                $temp_table_creaded = Db::getInstance()->Execute('CREATE TABLE '._DB_PREFIX_.'temp_translate_feature_value (`id_feature_value` INT(11),`id_lang` INT(11),`value` VARCHAR(255) CHARACTER SET utf8,INDEX(`id_feature_value`,`id_lang`))');
                if ($temp_table_creaded)
                {
                    $sql = array();
                    $init_sql = 'INSERT INTO '._DB_PREFIX_.'temp_translate_feature_value (`id_feature_value`,`id_lang`,`value`) VALUES ';
                    foreach ($data_to_import as $row)
                    {
                        foreach ($data_languages as $key => $id_lang)
                        {
                            if (!empty($row[0]))
                            {
                                $sql[] = '('.(int) $row[0].','.(int) $id_lang.",'".pSQL($row[$key])."')";
                                if (count($sql) == 500)
                                {
                                    $sql = $init_sql.implode(',', $sql);
                                    $res = Db::getInstance()->execute($sql);
                                    $sql = array();
                                }
                            }
                        }
                    }
                    if (!empty($sql))
                    {
                        $sql = $init_sql.implode(',', $sql);
                        $res = Db::getInstance()->execute($sql);
                    }
                    $final_sql = 'UPDATE '._DB_PREFIX_.'feature_value_lang fvl, '._DB_PREFIX_.'temp_translate_feature_value tmp
                    SET fvl.`value` = tmp.`value`
                    WHERE fvl.id_feature_value = tmp.id_feature_value AND fvl.id_lang = tmp.id_lang';
                    $final_update = Db::getInstance()->execute($final_sql);
                    if ($final_update)
                    {
                        Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'temp_translate_feature_value');
                        $return = _l('Translation for feature values updated', 1);
                        $id_item = 1;
                    }
                }
                else
                {
                    $sql = array();
                    foreach ($data_to_import as $row)
                    {
                        foreach ($data_languages as $key => $id_lang)
                        {
                            if (!empty($row[0]))
                            {
                                $sql[] = 'UPDATE '._DB_PREFIX_."feature_value_lang SET value='".pSQL($row[$key])."' WHERE id_feature_value = ".(int) $row[0].' AND id_lang = '.(int) $id_lang;
                            }
                        }
                    }
                    if (!empty($sql))
                    {
                        $chuncked = array_chunk($sql, 500);
                        foreach ($chuncked as $arr_sql)
                        {
                            foreach($arr_sql as $rowSql)
                            {
                                Db::getInstance()->execute($rowSql);
                            }
                        }
                        $return = _l('Translation for feature values updated', 1);
                        $id_item = 1;
                    }
                }
                break;
            case 'group_attribute':
                $actionDone = 0;
                foreach ($data_to_import as $row)
                {
                    foreach ($data_languages as $key => $id_lang)
                    {
                        if (!empty($row[0]))
                        {
                            $newKey = $key + ($key - 1);
                            $actionDone += (int)Db::getInstance()->execute('UPDATE '._DB_PREFIX_."attribute_group_lang
                                SET name='".pSQL($row[$newKey])."', public_name='".pSQL($row[$newKey + 1])."'
                                WHERE id_attribute_group = ".(int) $row[0].'
                                AND id_lang = '.(int) $id_lang);
                        }
                    }
                }
                if ($actionDone)
                {
                    $return = _l('Translation for combination groups updated', 1);
                    $id_item = 2;
                }
            break;
            case 'attribute_value':
                Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'temp_translate_attribute_value');
                $temp_table_creaded = Db::getInstance()->Execute('CREATE TABLE '._DB_PREFIX_.'temp_translate_attribute_value (`id_attribute` INT(11),`id_lang` INT(11),`name` VARCHAR(255) CHARACTER SET utf8,INDEX(`id_attribute`,`id_lang`))');
                if ($temp_table_creaded)
                {
                    $sql = array();
                    $init_sql = 'INSERT INTO '._DB_PREFIX_.'temp_translate_attribute_value (`id_attribute`,`id_lang`,`name`) VALUES ';
                    foreach ($data_to_import as $row)
                    {
                        foreach ($data_languages as $key => $id_lang)
                        {
                            if (!empty($row[0]))
                            {
                                $sql[] = '('.(int) $row[0].','.(int) $id_lang.",'".pSQL($row[$key])."')";
                                if (count($sql) == 500)
                                {
                                    $sql = $init_sql.implode(',', $sql);
                                    $res = Db::getInstance()->execute($sql);
                                    $sql = array();
                                }
                            }
                        }
                    }
                    if (!empty($sql))
                    {
                        $sql = $init_sql.implode(',', $sql);
                        $res = Db::getInstance()->execute($sql);
                    }
                    $final_sql = 'UPDATE '._DB_PREFIX_.'attribute_lang fvl, '._DB_PREFIX_.'temp_translate_attribute_value tmp
                    SET fvl.`name` = tmp.`name`
                    WHERE fvl.id_attribute = tmp.id_attribute AND fvl.id_lang = tmp.id_lang';
                    $final_update = Db::getInstance()->execute($final_sql);
                    if ($final_update)
                    {
                        Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'temp_translate_attribute_value');
                        $return = _l('Translation for combination attributes updated', 1);
                        $id_item = 3;
                    }
                }
                else
                {
                    $sql = array();
                    foreach ($data_to_import as $row)
                    {
                        foreach ($data_languages as $key => $id_lang)
                        {
                            if (!empty($row[0]))
                            {
                                $sql[] = 'UPDATE '._DB_PREFIX_."attribute_lang SET name='".pSQL($row[$key])."' WHERE id_attribute = ".(int) $row[0].' AND id_lang = '.(int) $id_lang;
                            }
                        }
                    }
                    if (!empty($sql))
                    {
                        $chuncked = array_chunk($sql, 500);
                        foreach ($chuncked as $arr_sql)
                        {
                            foreach($arr_sql as $rowSql)
                            {
                                Db::getInstance()->execute($rowSql);
                            }
                        }
                        $return = _l('Translation for combination attributes updated', 1);
                        $id_item = 3;
                    }
                }
            break;
        }
    }
}
else
{
    $error = true;
    $return = _l('Empty data', 1);
}

exit(json_encode(array(
    'error' => $error,
    'id_item' => $id_item,
    'message' => $return,
)));
