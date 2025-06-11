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

function upgrade_module_2_0_0($object)
{
    $languages = Language::getLanguages();

    // Install  new conf
    if (!Configuration::updateValue('FLASHSALE_PRODUCTS_NB', Configuration::get('PS_FLASHSALES_NBR'))
        || !Configuration::updateValue('FLASHSALE_DEL_SPECIFICPRICE', '1')
        || !Configuration::updateValue('FLASHSALE_DISPLAY_TOPMENU', '0')) {
        return false;
    }

    $values = [];
    foreach ($languages as $language) {
        $values['FLASHSALE_PAGE_TITLE'] = [$language['id_lang'] => Configuration::get('PS_FS_NAME', $language['id_lang'])];
        $values['FLASHSALE_COUNTDOWN_STRING'] = [$language['id_lang'] => Configuration::get('COUNTDOWN_TEXT', $language['id_lang'])];

        foreach ($values as $key => $value) {
            if (!Configuration::updateValue($key, $value)) {
                return false;
            }
        }
    }

    // Delete deprecated conf
    Configuration::deleteByName('PS_FLASHSALES_NBR');
    Configuration::deleteByName('PS_FS_NAME');
    Configuration::deleteByName('COUNTDOWN_TEXT');
    Configuration::deleteByName('FS_DESC');
    Configuration::deleteByName('PS_COUNTDOWN_COLOR');
    Configuration::deleteByName('PS_COUNTDOWN_BACKGROUND_COLOR');
    Configuration::deleteByName('PS_COUNTDOWN_BORDER');
    Configuration::deleteByName('PS_COUNTDOWN_BORDER_COLOR');
    Configuration::deleteByName('PS_COUNTDOWN_CHRONO_COLOR');
    Configuration::deleteByName('PS_COUNTDOWN_WIDTH');
    Configuration::deleteByName('PS_FS_NAME_COLOR');
    Configuration::deleteByName('PS_FS_IMG');
    Configuration::deleteByName('PS_DISPLAY_FLASHSALES');
    Configuration::deleteByName('PS_DISPLAY_HOME_FLASHSALES');
    Configuration::deleteByName('PS_DISPLAY_BLOCK_FLASHSALES');

    // Get old flash Sales
    $flash_sales = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT fs.*
		FROM ' . _DB_PREFIX_ . 'flash_sale fs
	');
    foreach ($flash_sales as $key => $flash_sale) {
        $flash_sales[$key]['products'] = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT fsp.*
			FROM ' . _DB_PREFIX_ . 'flash_sale_products fsp
			WHERE fsp.`id_flash_sale` = ' . (int) $flash_sale['id_flash_sale'] . '
		');
    }

    // Delete old flash sales table
    Db::getInstance()->execute('DROP TABLE IF EXISTS`' . _DB_PREFIX_ . 'flash_sale`');
    Db::getInstance()->execute('DROP TABLE IF EXISTS`' . _DB_PREFIX_ . 'flash_sale_products`');

    // Create new flash sales table
    $sql = [];
    include _PS_MODULE_DIR_ . $object->name . '/sql/install.php';
    foreach ($sql as $s) {
        if (!Db::getInstance()->execute($s)) {
            return false;
        }
    }

    // fill tables with old data
    foreach ($flash_sales as $key => $flash_sale) {
        $specific_price_rule = new SpecificPriceRule((int) $flash_sales[$key]['id_specific_price_rule']);
        if (!Validate::isLoadedObject($specific_price_rule)) {
            continue;
        }

        // create flash sale object
        $obj = new FlashSale();
        $obj->id_shop = (int) $specific_price_rule->id_shop;
        $obj->id_currency = (int) $specific_price_rule->id_currency;
        $obj->id_country = (int) $specific_price_rule->id_country;
        $obj->id_group = (int) $specific_price_rule->id_group;
        $obj->id_customer = (int) Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
			SELECT sp.`id_customer`
			FROM ' . _DB_PREFIX_ . 'specific_price sp
			WHERE sp.id_specific_price_rule = ' . (int) $specific_price_rule->id . '
			GROUP BY sp.`id_customer`
		');
        $obj->reduction = $specific_price_rule->reduction;
        if (version_compare(_PS_VERSION_, '1.6.0.11', '>=')) {
            $obj->reduction_tax = $specific_price_rule->reduction_tax;
        }
        $obj->reduction_type = $specific_price_rule->reduction_type;
        $obj->from = $specific_price_rule->from;
        $obj->to = $specific_price_rule->to;
        $obj->active = (int) $flash_sale['active'];
        foreach ($languages as $language) {
            $obj->name[(int) $language['id_lang']] = $language['iso_code'] == 'fr' ? 'Ventes Flash' : 'Flash Sales';
        }
        $obj->add();
        $specific_price_rule->delete();

        // add related Products
        foreach ($flash_sale['products'] as $product) {
            Db::getInstance()->execute('INSERT INTO `' . _DB_PREFIX_ . 'flash_sale_products` (`id_flash_sale`, `id_product`)
			VALUES (' . (int) $obj->id . ', ' . (int) $product['id_product'] . ')');
            Db::getInstance()->Insert_ID();

            if (!$obj->active) {
                continue;
            }

            // create specific prices
            if ($specific_price = SpecificPrice::exists(
                (int) $product['id_product'],
                0,
                (int) $obj->id_shop,
                (int) $obj->id_group,
                (int) $obj->id_country,
                (int) $obj->id_currency,
                (int) $obj->id_customer,
                1,
                $obj->from,
                $obj->to
            )) {
                $sp_obj = new SpecificPrice((int) $specific_price);
                $sp_obj->delete();
            }
            $obj->addSpecificPrice((int) $product['id_product']);
        }
    }

    // delete old tabs
    $tabs = Tab::getCollectionFromModule($object->name);
    foreach ($tabs as $tab) {
        if (!$tab->delete()) {
            return false;
        }
    }

    // Install new tabs
    $tab = new Tab();
    foreach ($languages as $language) {
        $tab->name[$language['id_lang']] = $language['iso_code'] == 'fr' ? 'Ventes Flash' : 'Flash sales';
    }
    $tab->class_name = 'AdminFlashSales';
    $tab->id_parent = (int) Tab::getIdFromClassName('AdminPriceRule');
    $tab->module = $object->name;
    if (!$tab->add()) {
        return false;
    }

    // delete old meta
    if ($metas = Meta::getMetaByPage('module-' . $object->name . '-page', (int) Context::getContext()->language->id)) {
        $obj = new Meta((int) $metas['id_meta']);
        if (!$obj->delete()) {
            return false;
        }
    }

    // Install Meta
    $meta = new Meta();
    $meta->page = 'module-' . $object->name . '-page';
    $meta->configurable = 1;
    foreach ($languages as $language) {
        if ($language['iso_code'] == 'fr') {
            $meta->title[(int) $language['id_lang']] = 'Ventes flash';
            $meta->description[(int) $language['id_lang']] = '';
            $meta->url_rewrite[(int) $language['id_lang']] = 'ventes-flash';
        } else {
            $meta->title[(int) $language['id_lang']] = 'Flash sales';
            $meta->description[(int) $language['id_lang']] = '';
            $meta->url_rewrite[(int) $language['id_lang']] = 'flash-sales';
        }
    }
    $meta->add();

    if (version_compare(_PS_VERSION_, '1.6', '>=')) {
        $themes = Theme::getThemes();
        $theme_meta_value = [];
        foreach ($themes as $theme) {
            $theme_meta_value[] = [
                'id_theme' => $theme->id,
                'id_meta' => (int) $meta->id,
                'left_column' => (int) $theme->default_left_column,
                'right_column' => (int) $theme->default_right_column,
            ];
        }
        if (count($theme_meta_value) > 0) {
            Db::getInstance()->insert('theme_meta', (array) $theme_meta_value, false, true, DB::INSERT_IGNORE);
        }
    }

    // delete deprecated files
    $files = [
        _PS_MODULE_DIR_ . $object->name . '/views/css/1.5',
        _PS_MODULE_DIR_ . $object->name . '/views/css/1.6',
        _PS_MODULE_DIR_ . $object->name . '/views/img/cat_banner.jpg',
        _PS_MODULE_DIR_ . $object->name . '/views/img/chrono_black.png',
        _PS_MODULE_DIR_ . $object->name . '/views/img/chrono_red.png',
        _PS_MODULE_DIR_ . $object->name . '/views/img/chrono_orange.png',
        _PS_MODULE_DIR_ . $object->name . '/views/img/chrono_white.png',
        _PS_MODULE_DIR_ . $object->name . '/views/templates/admin/flash_sales_setting',
        _PS_MODULE_DIR_ . $object->name . '/views/templates/admin/flashsales',
        _PS_MODULE_DIR_ . $object->name . '/views/templates/front/1.5',
        _PS_MODULE_DIR_ . $object->name . '/views/templates/front/1.6',
        _PS_MODULE_DIR_ . $object->name . '/views/templates/hook/1.5',
        _PS_MODULE_DIR_ . $object->name . '/views/templates/hook/1.6',
        _PS_MODULE_DIR_ . $object->name . '/controllers/admin/AdminFlashSalesSettingController.php',
    ];

    foreach ($files as $file) {
        if (file_exists($file)) {
            deleteFile($file);
        }
    }

    return true;
}

function deleteFile($path)
{
    if (is_dir($path) === true) {
        $files = array_diff(scandir($path), ['.', '..']);
        foreach ($files as $file) {
            deleteFile($path . DIRECTORY_SEPARATOR . $file);
        }

        return rmdir($path);
    } elseif (is_file($path) === true) {
        return unlink($path);
    } else {
        return false;
    }
}
