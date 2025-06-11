<?php
/**
 * 2007-2021 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author     PrestaShop SA <contact@prestashop.com>
 * @copyright  2007-2021 PrestaShop SA
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Upgrade to 1.6.1
 *
 * @param Colissimo $module
 * @return bool
 * @throws PrestaShopDatabaseException
 * @throws PrestaShopException
 */
function upgrade_module_1_6_1($module)
{
    $logsEnabled = Configuration::get('COLISSIMO_LOGS');
    Configuration::updateValue('COLISSIMO_LOGS', 1);
    $module->initLogger();
    $module->logger->setChannel('ModuleUpgrade');
    $module->logger->info('Module upgrade. Version 1.6.1');
    $shops = Shop::getShops();
    foreach ($shops as $shop) {
        Configuration::updateValue('COLISSIMO_WIDGET_REMOTE', 1, false, null, $shop['id_shop']);
    }

    $module->registerHook('displayAdminOrdersListAfter');
    $colissimoAddressQuery = "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "colissimo_address` (
            `id_colissimo_address` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
            `id_cart` INT(11) NOT NULL DEFAULT '0',
            `code_porte1` VARCHAR(8) NOT NULL DEFAULT '0',
            `code_porte2` VARCHAR(8) NULL DEFAULT '0', 
            PRIMARY KEY (`id_colissimo_address`),
            INDEX `id_cart` (`id_cart`)
        )";

    $colissimoAddress = Db::getInstance()
        ->execute($colissimoAddressQuery);
    if (!$colissimoAddress) {
        $module->logger->error('Cannot create table colissimo_address.');
        Configuration::updateValue('COLISSIMO_LOGS', (int) $logsEnabled);

        return false;
    }
    Configuration::updateValue('COLISSIMO_POSTAL_PARTNER', 0);
    Configuration::updateValue('COLISSIMO_ENABLE_DDP', 0);
    Configuration::updateValue('COLISSIMO_DDP_COST', 18);
    Configuration::updateValue('COLISSIMO_DDP_GB_COST', 10);
    if (version_compare(_PS_VERSION_, '1.7', '>=')) {
        $module->registerHook('displayShoppingCart');
        $module->registerHook('displayCheckoutSummaryTop');
        if (version_compare(_PS_VERSION_, '1.7.8.0', '>=')) {
            $module->registerHook('displayCartModalContent');
        }
    } else {
        $module->registerHook('displayBeforeShoppingCartBlock');
    }

    // Add new column in colissimo_order table if needed
    try {
        $columnExists = Db::getInstance()
            ->executeS('SHOW COLUMNS FROM `' . _DB_PREFIX_ . 'colissimo_order` LIKE "ddp"');
    } catch (Exception $e) {
        $module->logger->error($e->getMessage());
        Configuration::updateValue('COLISSIMO_LOGS', (int) $logsEnabled);

        return false;
    }
    if (empty($columnExists)) {
        $result = Db::getInstance()
            ->execute(
                'ALTER TABLE `' . _DB_PREFIX_ . 'colissimo_order` ADD COLUMN `ddp` TINYINT(3) UNSIGNED NULL DEFAULT \'0\' AFTER `migration`'
            );
        if (!$result) {
            $module->logger->error('Cannot add column in colissimo_order table.');
            Configuration::updateValue('COLISSIMO_LOGS', (int) $logsEnabled);

            return false;
        }
        $module->logger->info('New column in colissimo_order created.');
    } else {
        $module->logger->info('Column ddp already exists.');
    }
    
    try {
        $columnExists = Db::getInstance()
            ->executeS('SHOW COLUMNS FROM `' . _DB_PREFIX_ . 'colissimo_order` LIKE "ddp_cost"');
    } catch (Exception $e) {
        $module->logger->error($e->getMessage());
        Configuration::updateValue('COLISSIMO_LOGS', (int) $logsEnabled);

        return false;
    }
    if (empty($columnExists)) {
        $result = Db::getInstance()
            ->execute(
                'ALTER TABLE `' . _DB_PREFIX_ . 'colissimo_order` ADD COLUMN `ddp_cost` FLOAT NOT NULL AFTER `ddp`'
            );
        if (!$result) {
            $module->logger->error('Cannot add column in colissimo_order table.');
            Configuration::updateValue('COLISSIMO_LOGS', (int) $logsEnabled);

            return false;
        }
        $module->logger->info('New column in colissimo_order created.');
    } else {
        $module->logger->info('Column ddp already exists.');
    }
    $module->logger->info('Module upgraded.');
    Configuration::updateValue('COLISSIMO_LOGS', (int) $logsEnabled);

    return true;
}
