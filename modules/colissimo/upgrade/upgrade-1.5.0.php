p<?php
/**
 * 2007-2020 PrestaShop
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
 * @copyright  2007-2020 PrestaShop SA
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Upgrade to 1.5.0
 *
 * @param $module Colissimo
 * @return bool
 */
function upgrade_module_1_5_0($module)
{
    $module->registerHook('actionAdminOrdersListingFieldsModifier');
    $module->registerHook('actionOrderGridQueryBuilderModifier');
    $module->registerHook('actionOrderGridDefinitionModifier');
    
    Configuration::updateValue('COLISSIMO_USE_WEIGHT_TARE', 0);
    Configuration::updateValue('COLISSIMO_VIEW_TRACKING_NUMBER', 0);
    
    $logsEnabled = Configuration::get('COLISSIMO_LOGS');
    Configuration::updateValue('COLISSIMO_LOGS', 1);
    $module->initLogger();
    $module->logger->setChannel('ModuleUpgrade');
    $module->logger->info('Module upgrade. Version 1.5.0');
    $colissimoLabelProductQuery = "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."colissimo_label_product`(
        `id_colissimo_label_product` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
        `id_colissimo_label` INT(10) UNSIGNED NOT NULL,
        `id_product` INT(10) UNSIGNED NOT NULL,
        `id_product_attribute` INT(10) UNSIGNED NOT NULL DEFAULT '0',
        `quantity` INT(10) UNSIGNED NOT NULL DEFAULT '0',
        `date_add` DATETIME NOT NULL,
        PRIMARY KEY (`id_colissimo_label_product`),
        INDEX `id_colissimo_label` (`id_colissimo_label`)
    )";
    
    $colissimoLabelProduct = Db::getInstance()
                                ->execute($colissimoLabelProductQuery);
    
    if (!$colissimoLabelProduct) {
        $module->logger->error('Cannot create table colissimo_label_product.');
        Configuration::updateValue('COLISSIMO_LOGS', (int) $logsEnabled);

        return false;
    }
    $module->logger->info('Module upgraded.');
    Configuration::updateValue('COLISSIMO_LOGS', (int) $logsEnabled);

    return true;
}
