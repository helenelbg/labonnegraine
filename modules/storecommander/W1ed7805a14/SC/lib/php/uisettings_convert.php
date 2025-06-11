<?php

class UISettingsConvert
{
    public static function convert($datas)
    {
        $new_datas = $datas;

        $file_version = 1;
        $actual_version = 1;
        if (!empty($datas['version']) && is_numeric($datas['version']))
        {
            $file_version = (int) $datas['version'];
        }
        $new_datas['version'] = $file_version;

        if (defined('SC_UISETTINGS_VERSION') && SC_UISETTINGS_VERSION > 0)
        {
            $actual_version = (int) SC_UISETTINGS_VERSION;
        }

        if ($file_version != $actual_version)
        {
            $start = $file_version + 1;
            for ($i = $start; $i <= $actual_version; ++$i)
            {
                $new_datas = call_user_func(array('UISettingsConvert', '_convert_from_'.$file_version.'_to_'.$i), $new_datas);
                ++$file_version;
            }
            $new_datas['version'] = $actual_version;
            UISettings::write_ini_file($new_datas, false);
        }

        return $new_datas;
    }

    public static function _convert_from_1_to_2($datas)
    {
        $new_datas = $datas;

        $to_delete = array(
            'cat_win-attribute_group',
            'cat_attachment',
            'cat_customization',
            'cat_image',
            'cat_win-feature',
            'cat_win-feature_value',
        );

        foreach ($to_delete as $grid)
        {
            if (!empty($new_datas[$grid]))
            {
                $new_datas[$grid] = '';
                unset($new_datas[$grid]);
            }
        }

        return $new_datas;
    }

    public static function _convert_from_2_to_3($datas)
    {
        $new_datas = $datas;

        foreach ($datas as $name)
        {
            if (substr($name, 0, 15) == 'cat_combination')
            {
                $new_datas[$name] = '';
                unset($new_datas[$name]);
            }
        }

        return $new_datas;
    }

    public static function _convert_from_3_to_4($datas)
    {
        $new_datas = $datas;

        foreach ($datas as $name => $values)
        {
            if (substr($name, 0, 15) == 'cat_combination')
            {
                $new_datas[$name] = '';
                unset($new_datas[$name]);
            }
        }

        return $new_datas;
    }

    public static function _convert_from_4_to_5($datas)
    {
        $new_datas = $datas;

        $prefix_toupdate = array(
            'cat_grid_',
            'cms_grid_',
            'ord_grid_',
            'cus_grid_',
            'cat_combination_separate',
            'cat_combination',
            'cat_combinationmultiproduct',
        );
        $name_toupdate = array(
            'cat_image',
            'gmapartner',
            'specificprice_grid',
            'cat_specificprice',
            'cat_supplier',
            'cat_msproduct',
            'cat_mscombination',
            'cat_productsort',
        );

        foreach ($datas as $name => $values)
        {
            $to_update = false;
            if (in_array($name, $name_toupdate))
            {
                $to_update = true;
            }
            foreach ($prefix_toupdate as $prefix)
            {
                if (!(strpos($name, $prefix) !== 0))
                {
                    $to_update = true;
                    break;
                }
            }
            if ($to_update)
            {
                $values_exp = explode('|', $values);

                $nvalue = '||';
                if (!empty($values_exp[2]))
                {
                    $nvalue .= $values_exp[2];
                }
                $nvalue .= '|';
                if (!empty($values_exp[3]))
                {
                    $nvalue .= $values_exp[3];
                }

                $new_datas[$name] = $nvalue;
            }
        }

        return $new_datas;
    }
}
