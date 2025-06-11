p<?php
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
 * Upgrade to 1.6.0
 *
 * @param Colissimo $module
 * @return bool
 * @throws PrestaShopDatabaseException
 * @throws PrestaShopException
 */
function upgrade_module_1_6_0($module)
{
    $logsEnabled = Configuration::get('COLISSIMO_LOGS');
    Configuration::updateValue('COLISSIMO_LOGS', 1);
    $module->initLogger();
    $module->logger->setChannel('ModuleUpgrade');
    $module->logger->info('Module upgrade. Version 1.6.0');
    Configuration::updateValue('COLISSIMO_DEFAULT_ORIGIN_COUNTRY', 8);
    Configuration::updateValue('COLISSIMO_LABEL_DISPLAY_REFERENCE', 1);
    $colissimoCustomDocumentQuery = "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."colissimo_custom_document` (
            `id_colissimo_custom_document` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
            `id_colissimo_label` INT(10) NOT NULL DEFAULT '0',
            `id_colissimo_order` INT(10) NOT NULL DEFAULT '0',
            `id_document` VARCHAR(50) NOT NULL DEFAULT '0',
            `type` ENUM('CN23','COMMERCIAL_INVOICE','OTHER') NOT NULL DEFAULT 'CN23',
            `date_add` DATETIME NOT NULL,
            PRIMARY KEY (`id_colissimo_custom_document`)
        )";

    $colissimoCustomDocument = Db::getInstance()
                                 ->execute($colissimoCustomDocumentQuery);

    if (!$colissimoCustomDocument) {
        $module->logger->error('Cannot create table colissimo_custom_document.');
        Configuration::updateValue('COLISSIMO_LOGS', (int) $logsEnabled);

        return false;
    }

    $customsDocumentsTab = Tab::getInstanceFromClassName('AdminColissimoCustomsDocuments');
    if (!Validate::isLoadedObject($customsDocumentsTab)) {
        try {
            $module->installMenu(
                array(
                    'visible' => true,
                    'class_name' => 'AdminColissimoCustomsDocuments',
                    'parent_class_name' => 'AdminParentShipping',
                    'ParentClassName' => 'AdminParentShipping',
                    'name' => array(
                        'fr' => 'Colissimo - Documents',
                        'en' => 'Colissimo - Customs documents',
                    ),
                )
            );
        } catch (Exception $e) {
            $module->logger->error('Cannot install Customs Documents menu.');
            Configuration::updateValue('COLISSIMO_LOGS', (int) $logsEnabled);

            return false;
        }
    }

    $languages = Language::getLanguages(false);
    $carrier = array(
        'name' => 'Colissimo ECO Outre Mer',
        'delays' => array(
            'fr' => 'Livraison ECO Outre Mer',
            'en' => 'Delivery ECO overseas',
        ),
        'url' => '',
        'active' => true,
        'shipping_handling' => false,
        'range_behavior' => 0,
        'is_module' => true,
        'is_free' => false,
        'shipping_external' => true,
        'need_range' => true,
        'external_module_name' => $module->name,
        'shipping_method' => Carrier::SHIPPING_METHOD_WEIGHT,
        'service' => 'ECO_OUTRE_MER',
    );
    $idCarrier = Configuration::get('COLISSIMO_CARRIER_'.$carrier['service']);
    $oldCarrier = ColissimoCarrier::getCarrierByReference((int) $idCarrier);
    if ($oldCarrier !== false &&
        Validate::isLoadedObject($oldCarrier) &&
        $oldCarrier->external_module_name == $module->name
    ) {
        $module->logger->info('Carrier '.$carrier['service'].' already exists');
    } else {
        $module->logger->info('Creating carrier '.$carrier['service']);
        $newCarrier = $module->createCarrier($carrier, $languages);
        $context = Context::getContext();
        $newCarrier->setGroups(Group::getGroups($context->language->id));
    }

    $ECOOutreMerId = ColissimoService::getServiceIdByIdCarrierDestinationType(
        Configuration::get('COLISSIMO_CARRIER_ECO_OUTRE_MER'),
        'OM'
    );
    if (!$ECOOutreMerId) {
        $colissimoService = new ColissimoService();
        $colissimoService->id_carrier = (int) Configuration::get('COLISSIMO_CARRIER_ECO_OUTRE_MER');
        $colissimoService->product_code = 'ECO';
        $colissimoService->commercial_name = 'OM - ECO OUTRE MER';
        $colissimoService->destination_type = 'OM';
        $colissimoService->is_signature = 0;
        $colissimoService->is_pickup = 0;
        $colissimoService->is_return = 0;
        $colissimoService->type = 'ECO_OUTRE_MER';
        try {
            $colissimoService->save();
            $module->logger->info('ECO OUTRE MER service added.');
        } catch (Exception $e) {
            $module->logger->error('Cannot add ECO OUTRE MER service.', array('message' => $e->getMessage()));
            Configuration::updateValue('COLISSIMO_LOGS', (int) $logsEnabled);

            return false;
        }
    } else {
        $module->logger->info('ECO OUTRE MER service already exists.');
    }
    $module->logger->info('Module upgraded.');
    Configuration::updateValue('COLISSIMO_LOGS', (int) $logsEnabled);

    return true;
}
