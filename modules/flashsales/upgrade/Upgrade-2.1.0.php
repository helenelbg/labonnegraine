<?php
/**
* 2022 - Keyrnel
*
* NOTICE OF LICENSE
*
* The source code of this module is under a commercial license.
* Each license is unique and can be installed and used on only one shop.
* Any reproduction or representation total or partial of the module, one or more of its components,
* by any means whatsoever, without express permission from us is prohibited.
* If you have not received this module from us, thank you for contacting us.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future.
*
* @author    Keyrnel
* @copyright 2022 - Keyrnel
* @license   commercial
* International Registered Trademark & Property of Keyrnel
*/
if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_2_1_0($object)
{
    // Add new hook
    if (!$object->registerHook('displayProductAdditionalInfo')) {
        return false;
    }

    // Install  new conf
    $languages = Language::getLanguages();
    foreach ($object->getConfigs() as $config) {
        $name = $config['name'];
        $value = $config['value'];

        if ($name == 'FLASHSALE_DEL_SPECIFICPRICE' || $name == 'FLASHSALE_DISPLAY_TOPMENU') {
            continue;
        }

        if (isset($config['lang']) && $config['lang']) {
            $value = [];

            if ($name == 'FLASHSALE_TITLE' || $name == 'FLASHSALE_COUNTDOWN_STRING') {
                $old_conf = '';
                if ($name == 'FLASHSALE_TITLE') {
                    $old_conf = 'FLASHSALE_PAGE_TITLE';
                } elseif ($name == 'FLASHSALE_COUNTDOWN_STRING') {
                    $old_conf = 'FLASHSALE_COUNTDOWN_STRING';
                }

                foreach ($languages as $language) {
                    $value[$language['id_lang']] = Configuration::get($old_conf, $language['id_lang']);
                }
            } else {
                foreach ($languages as $language) {
                    $value[$language['id_lang']] = isset($config['value'][$language['iso_code']]) ? $config['value'][$language['iso_code']] : $config['value'][$language['en']];
                }
            }
        }

        if (isset($config['layouts']) && count($config['layouts'])) {
            foreach ($config['layouts'] as $layout) {
                $name = $config['name'] . '_' . Tools::strtoupper($layout);
                if ($name == 'FLASHSALE_PRODUCTS_NB_HOME_PAGE') {
                    $value = Configuration::get('FLASHSALE_PRODUCTS_NB');
                }

                if (!Configuration::updateValue($name, $value)) {
                    return false;
                }
            }
        } else {
            if (!Configuration::updateValue($name, $value)) {
                return false;
            }
        }
    }

    // Add new columns to existing database
    $alter_combination_sql = '
        SELECT *
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE table_name = "' . _DB_PREFIX_ . 'flash_sale_custom_specific_prices"
        AND table_schema = "' . _DB_NAME_ . '"
        AND column_name = "id_product_attribute"
    ';

    if (!Db::getInstance()->executeS($alter_combination_sql)) {
        Db::getInstance()->execute('
        ALTER TABLE `' . _DB_PREFIX_ . 'flash_sale_custom_specific_prices`
        ADD `id_product_attribute` int(10) unsigned NOT NULL AFTER `id_product`
        ');

        Db::getInstance()->execute('
        UPDATE `' . _DB_PREFIX_ . 'flash_sale_custom_specific_prices`
        SET `id_product_attribute` = 0
        ');
    }

    $alter_reduction_sql = '
        SELECT *
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE table_name = "' . _DB_PREFIX_ . 'flash_sale_custom_specific_prices"
        AND table_schema = "' . _DB_NAME_ . '"
        AND column_name = "custom_reduction"
    ';

    if (!Db::getInstance()->executeS($alter_reduction_sql)) {
        Db::getInstance()->execute('
        ALTER TABLE `' . _DB_PREFIX_ . 'flash_sale_custom_specific_prices`
        ADD `custom_reduction` tinyint(1) NOT NULL AFTER `reduction_type`
        ');

        Db::getInstance()->execute('
        UPDATE `' . _DB_PREFIX_ . 'flash_sale_custom_specific_prices`
        SET `custom_reduction` = 1
        ');
    }

    // Install new table
    $object->installDb();

    // Update countdown css
    $file = 'countdown.css';
    $path = _PS_MODULE_DIR_ . $object->name . '/views/css/front/';
    $dest = version_compare(_PS_VERSION_, '1.7', '>=') ? _PS_THEME_DIR_ . 'modules/' . $object->name . '/views/css/front/' : _PS_THEME_DIR_ . 'css/modules/' . $object->name . '/views/css/front/';
    if (!file_exists($dest)) {
        mkdir($dest, 0777, true);
    }
    if (!file_exists($dest . $file)) {
        copy($path . $file, $dest . $file);
    }
    $css_file = Tools::file_get_contents($dest . $file);

    $old_file = 'design.css';
    $override_dir = version_compare(_PS_VERSION_, '1.7', '>=') ? _PS_THEME_DIR_ . 'modules/' . $object->name . '/views/css/' : _PS_THEME_DIR_ . 'css/modules/' . $object->name . '/views/css/';
    if (file_exists($override_dir . $old_file)) {
        $old_file_path = $override_dir . $old_file;
    } else {
        $old_file_path = _PS_MODULE_DIR_ . $object->name . '/views/css/' . $old_file;
    }
    $old_css_file = Tools::file_get_contents($old_file_path);

    foreach ($object->getLayouts() as $layout) {
        foreach ($object->getCssFields() as $css_field) {
            if (isset($css_field['layouts']) && !in_array($layout, $css_field['layouts'])) {
                continue;
            }

            $old_pos = stripos($old_css_file, '.flashsale-countdown-box');
            if (isset($css_field['class'])) {
                $old_pos = stripos($old_css_file, $css_field['class'], $old_pos);
            }

            $old_css_property = $css_field['property'];
            if ($old_css_property == 'padding-top'
                || $old_css_property == 'padding-bottom'
                || $old_css_property == 'padding-left'
                || $old_css_property == 'padding-right') {
                $old_css_property = 'padding';
            } elseif ($old_css_property == 'margin-top'
                || $old_css_property == 'margin-bottom') {
                $old_css_property = 'margin';
            }

            if (preg_match('#' . $old_css_property . '[^-]#', $old_css_file, $matches, PREG_OFFSET_CAPTURE, $old_pos)) {
                if ($css_field['property'] == 'margin-top'
                    || $css_field['property'] == 'margin-bottom') {
                }
                $old_pos = stripos($old_css_file, ':', $matches[0][1]);
                $start = $old_pos + 1;
                $end = stripos($old_css_file, ';', $start);
                $output = Tools::substr($old_css_file, $start, $end - $start);
                $output = trim($output);

                if ($old_css_property == 'padding'
                    || $old_css_property == 'margin') {
                    $output = explode(' ', $output);

                    if ($css_field['property'] == 'padding-top'
                        || $css_field['property'] == 'padding-bottom') {
                        $output = $output[0];
                    } elseif ($css_field['property'] == 'padding-left'
                        || $css_field['property'] == 'padding-right') {
                        $output = $output[1];
                    } elseif ($css_field['property'] == 'margin-top'
                        || $css_field['property'] == 'margin-bottom') {
                        $output = $output[0];
                    }
                }

                $pos = stripos($css_file, '.flashsale-countdown-box.' . $layout);
                if (isset($css_field['class'])) {
                    $pos = stripos($css_file, $css_field['class'], $pos);
                }
                $pos = stripos($css_file, $css_field['property'], $pos);
                $pos = stripos($css_file, ':', $pos);
                $start = $pos + 1;
                $end = stripos($css_file, ';', $start);
                $css_file = substr_replace($css_file, ' ' . $output, $start, $end - $start);
            }
        }
    }

    file_put_contents($dest . $file, $css_file);

    return true;
}
