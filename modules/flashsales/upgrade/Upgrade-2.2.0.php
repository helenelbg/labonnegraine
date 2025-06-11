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

function upgrade_module_2_2_0($object)
{
    // Add new hook
    if (!$object->registerHook('actionObjectUpdateAfter')) {
        return false;
    }

    // Create new tables
    $sql = [];
    include _PS_MODULE_DIR_ . $object->name . '/sql/install.php';
    foreach ($sql as $s) {
        if (!Db::getInstance()->execute($s)) {
            return false;
        }
    }

    // Add new columns
    $tables = ['flash_sale_custom_specific_prices', 'flash_sale_custom_specific_prices_group'];
    foreach ($tables as $table) {
        $alter_sql = '
            SELECT *
            FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_NAME = "' . _DB_PREFIX_ . $table . '"
            AND TABLE_SCHEMA = "' . _DB_NAME_ . '"
            AND COLUMN_NAME = "from"
        ';

        if (!Db::getInstance()->executeS($alter_sql)) {
            Db::getInstance()->execute('
                ALTER TABLE `' . _DB_PREFIX_ . $table . '`
                ADD COLUMN `from` datetime NOT NULL AFTER `reduction_type`,
                ADD COLUMN `to` datetime NOT NULL AFTER `from`
            ');
        }
    }

    // Fill tables with old data
    $flash_sales = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT fs.*
		FROM `' . _DB_PREFIX_ . 'flash_sale` fs
	');

    foreach ($flash_sales as $flash_sale) {
        $flash_sale_object = new FlashSale((int) $flash_sale['id_flash_sale']);
        if (!Validate::isLoadedObject($flash_sale_object)) {
            continue;
        }

        $restrictions = $flash_sale_object->restrictions;
        foreach ($restrictions as $key => $restriction) {
            if (isset($flash_sale[$restriction['identifier']]) && $flash_sale[$restriction['identifier']]) {
                $flash_sale_object->setRestrictionsByKey($key, [$flash_sale[$restriction['identifier']]]);
            }
        }

        foreach ($tables as $table) {
            $inserts = [];
            $rows = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
        		SELECT *
        		FROM `' . _DB_PREFIX_ . $table . '`
                WHERE `id_flash_sale` = ' . (int) $flash_sale['id_flash_sale'] . '
        	');

            foreach ($rows as $row) {
                $inserts[] = '(' . (int) $row['id_' . $table] . ', "' . pSQL($flash_sale['from']) . '", "' . pSQL($flash_sale['to']) . '")';
            }

            if (count($inserts)) {
                Db::getInstance()->execute('
                INSERT INTO `' . _DB_PREFIX_ . $table . '` (`id_' . $table . '`, `from`, `to`)
        		VALUES ' . implode(',', $inserts) . '
                ON DUPLICATE KEY UPDATE `from`="' . pSQL($flash_sale['from']) . '", `to`="' . pSQL($flash_sale['to']) . '"');
            }
        }
    }

    // Drop deprecated columns
    $alter_sql = '
        SELECT *
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_NAME = "' . _DB_PREFIX_ . 'flash_sale"
        AND TABLE_SCHEMA = "' . _DB_NAME_ . '"
        AND COLUMN_NAME = "id_shop"
    ';

    if (Db::getInstance()->executeS($alter_sql)) {
        Db::getInstance()->execute('
            ALTER TABLE `' . _DB_PREFIX_ . 'flash_sale`
            DROP COLUMN `id_shop`,
            DROP COLUMN `id_group`,
            DROP COLUMN `id_currency`,
            DROP COLUMN `id_country`
        ');
    }

    return true;
}
