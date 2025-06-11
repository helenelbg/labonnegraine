<?php

class ExportConvert
{
    public static function convert($name, $datas)
    {
        $new_datas = array();
        $mapping = $datas->field;
        $i = 0;
        foreach ($mapping as $map)
        {
            $new_datas[(int) $i]['id'] = $map->id;
            $new_datas[(int) $i]['used'] = $map->used;
            $new_datas[(int) $i]['name'] = $map->name;
            $new_datas[(int) $i]['lang'] = $map->lang;
            $new_datas[(int) $i]['options'] = $map->options;
            //$new_datas[(int)$i]["filters"]=$map->filters;
            $new_datas[(int) $i]['modifications'] = $map->modifications;
            $new_datas[(int) $i]['column_name'] = $map->column_name;
            ++$i;
        }

        $attributes = $datas->attributes();

        $file_version = 1;
        $actual_version = 1;

        if (!empty($attributes->version))
        {
            $file_version = (int) $attributes->version;
        }

        if (defined('SC_EXPORT_VERSION') && SC_EXPORT_VERSION > 0)
        {
            $actual_version = (int) SC_EXPORT_VERSION;
        }

        if ($file_version != $actual_version)
        {
            $start = $file_version + 1;
            for ($i = $start; $i <= $actual_version; ++$i)
            {
                $new_datas = call_user_func(array('ExportConvert', '_convert_from_'.$file_version.'_to_'.$i), $new_datas);
                ++$file_version;
            }

            self::saveXML($name, $actual_version, $new_datas);
            $new_datas = @simplexml_load_file(SC_TOOLS_DIR.'cat_export/'.$name);
        }
        else
        {
            $new_datas = $datas;
        }

        return $new_datas;
    }

    public static function saveXML($name, $version, $datas)
    {
        $content = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n".'<mapping version="'.(int) $version.'">'."\n";
        $contentArray = array();
        foreach ($datas as $row_id => $map)
        {
            $contentArray[$row_id][] = '<field>';
            foreach ($map as $field => $value)
            {
                $contentArray[$row_id][] = '<'.$field.'><![CDATA['.$value.']]></'.$field.'>';
            }
            $contentArray[$row_id][] = '</field>'."\n";
            $contentArray[$row_id] = implode('', $contentArray[$row_id]);
        }
        $content .= implode('', $contentArray).'</mapping>';
        file_put_contents(SC_TOOLS_DIR.'cat_export/'.$name, $content);
    }

    public static function _convert_from_1_to_2($datas)
    {
        global $sc_agent;
        $new_datas = $datas;

        $defaultLanguageId = (int) Configuration::get('PS_LANG_DEFAULT');
        if (!empty($sc_agent->id_lang))
        {
            $defaultLanguageId = (int) $sc_agent->id_lang;
        }

        $defaultLanguage = Language::getIsoById($defaultLanguageId);

        foreach ($new_datas as $i => $map)
        {
            $new_datas[$i]['column_name'] = '';
            $new_datas[$i]['lang'] = '';
            if ($map['name'] == 'attribute' || $map['name'] == 'feature')
            {
                $new_datas[$i]['lang'] = $defaultLanguage;
            }
            else
            {
                $new_datas[$i]['lang'] = $map['options'];
                $new_datas[$i]['options'] = '';
            }
        }

        return $new_datas;
    }

    public static function _convert_from_2_to_3($datas)
    {
        global $sc_agent;
        $new_datas = $datas;

        foreach ($new_datas as $i => $map)
        {
            if (!empty($map['modifications']))
            {
                $new_datas[$i]['modifications'] = str_replace(' ', '&&&', $map['modifications']);
            }
        }

        return $new_datas;
    }

    public static function _convert_from_3_to_4($datas)
    {
        global $sc_agent;
        $new_datas = $datas;

        foreach ($new_datas as $i => $map)
        {
            $field_to_overwritte = (string) $map['name'];
            $replace_fields = array('unit_price' => 'unit_price_tax_excl_with_reduc');
            if (array_key_exists($field_to_overwritte, $replace_fields))
            {
                $new_datas[$i]['name'] = (string) $replace_fields[$field_to_overwritte];
                break;
            }
        }

        return $new_datas;
    }

    public static function _convert_from_4_to_5($datas)
    {
        $new_datas = $datas;
        $common_fields = getCommonProductCombinationsExportFields();

        foreach ($new_datas as $i => &$map)
        {
            $current_field = (string) $map['name'];
            $tmp = array(
                'used' => $map['used'],
                'name' => $map['name'],
                'lang' => $map['lang'],
                'options' => $map['options'],
                'options_two' => (in_array($current_field, $common_fields) ? 'default' : ''),
                'modifications' => $map['modifications'],
                'column_name' => $map['column_name'],
            );
            $map = $tmp;
            $value_for_replace_export_pref = 'prod_value_if_combi_empty';
            $bdd_settings = json_decode(SCI::getConfigurationValue('SC_SETTINGS', 0), true);
            switch ($current_field) {
                case 'ean13':
                    if (array_key_exists('CAT_EXPORT_EAN13_COMBI', $bdd_settings) && $bdd_settings['CAT_EXPORT_EAN13_COMBI']['value'] == 1)
                    {
                        $map['options_two'] = $value_for_replace_export_pref;
                    }
                    break;
                case 'reference':
                    if (array_key_exists('CAT_EXPORT_REF_COMBI', $bdd_settings) && $bdd_settings['CAT_EXPORT_REF_COMBI']['value'] == 1)
                    {
                        $map['options_two'] = $value_for_replace_export_pref;
                    }
                    break;
                case 'upc':
                    if (array_key_exists('CAT_EXPORT_UPC_COMBI', $bdd_settings) && $bdd_settings['CAT_EXPORT_UPC_COMBI']['value'] == 1)
                    {
                        $map['options_two'] = $value_for_replace_export_pref;
                    }
                    break;
                case 'isbn':
                    if (array_key_exists('CAT_EXPORT_ISBN_COMBI', $bdd_settings) && $bdd_settings['CAT_EXPORT_ISBN_COMBI']['value'] == 1)
                    {
                        $map['options_two'] = $value_for_replace_export_pref;
                    }
                    break;
            }
        }

        return $new_datas;
    }
}
