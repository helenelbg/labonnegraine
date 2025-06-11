<?php
/**
 * 2007-2020 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

use PrestaShop\PrestaShop\Core\Grid\Definition\GridDefinitionInterface;
use PrestaShop\PrestaShop\Core\Grid\Filter\Filter;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\DataColumn;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Class Colissimo
 */
class Colissimo extends CarrierModule
{
    const ID_PRODUCT_ADDONS = 42138;

    const COLIVIEW_URL = 'https://www.colissimo.entreprise.laposte.fr/fr';

    const COLISHIP_URL = 'https://www.colissimo.fr/entreprise/coliship/login?lang=fr';

    /** @var ColissimoLogger $logger */
    public $logger;

    /** @var ColissimoLabelGenerator $labelGenerator */
    public $labelGenerator;

    /** @var int $id_carrier */
    public $id_carrier;

    /** @var array $controllers */
    public $controllers = array(
        'widget',
        'tracking',
        'return',
    );

    /** @var array $PNAMailObject */
    public $PNAMailObject = array(
        'fr' => 'Envoi de la commande %s en cours',
        'en' => 'Shipment of order %s in progress',
    );

    /** @var array $returnLabelMailObject */
    public $returnLabelMailObject = array(
        'fr' => 'Etiquette retour pour votre commande %s',
        'en' => 'Return label for order %s',
    );
    
    /**
     * @var array $controllersBO
     */
    public $controllersBO = array(
        'AdminColissimoAffranchissement',
        'AdminColissimoDepositSlip',
        'AdminColissimoDashboard',
        'AdminColissimoColiship',
        'AdminColissimoCustomsDocuments',
    );

    /** @var ColissimoModuleConfiguration $moduleConfiguration */
    private $moduleConfiguration;

    /** @var string $boTheme */
    public $boTheme;

    /**
     * Colissimo constructor.
     */
    public function __construct()
    {
        require_once(dirname(__FILE__).'/classes/module.classes.php');
        require_once(dirname(__FILE__).'/lib/loader.php');

        $this->name = 'colissimo';
        $this->tab = 'shipping_logistics';
        $this->version = '2.0.2';
        $this->module_key = 'cce3f48c72001910b4bbda7b7492b5ba';
        $this->author = 'Colissimo';
        $this->need_instance = 0;
        $this->bootstrap = true;
        parent::__construct();
        $this->displayName = $this->l('Colissimo');
        $this->description = $this->l('Colissimo module for PrestaShop');
        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
        $this->initLogger();
        $this->logger->setChannel('Main');
        $this->labelGenerator = new ColissimoLabelGenerator($this->logger);
        $this->boTheme = Tools::version_compare(_PS_VERSION_, '1.7.7.0', '>=') ? 'new_theme' : 'legacy';
    }

    /**
     * @return bool
     */
    public function install()
    {
        $enableLogs = Configuration::get('COLISSIMO_LOGS');
        Configuration::updateValue('COLISSIMO_LOGS', 1);
        $this->initLogger();
        $this->logger->setChannel('Install');
        try {
            $this->testTechnicalRequirements(false);
            if (!parent::install()) {
                return false;
            }
            $this->createModuleTables();
            $this->createModuleCarriersAndServices();
            $this->createTrackingCodes(ColissimoTools::getColissimoTrackingCodesSource());
            $this->setDefaultConfiguration();
            $this->installMenus();
            $this->installOrderStates();
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
            $this->_errors[] = $e->getMessage();

            return false;
        }
        if (Tools::version_compare(_PS_VERSION_, '1.7.7.0', '>=')) {
            $this->registerHook('displayAdminOrderMainBottom');
            if (Tools::version_compare(_PS_VERSION_, '1.7.8.0', '>=')) {
                $this->registerHook('displayCartModalContent');
            }
        } else {
            $this->registerHook('displayAdminOrder');
        }
        Configuration::updateValue('COLISSIMO_LOGS', $enableLogs);

        return $this->registerHook('actionAdminControllerSetMedia') &&
               $this->registerHook('header') &&
               $this->registerHook('newOrder') &&
               $this->registerHook('moduleRoutes') &&
               $this->registerHook('displayCarrierExtraContent') &&
               $this->registerHook('extraCarrier') &&
               $this->registerHook('displayShoppingCart') &&
               $this->registerHook('displayCheckoutSummaryTop') &&
               $this->registerHook('displayAdminColissimoAffranchissementListAfter') &&
               $this->registerHook('displayAdminColissimoDashboardListAfter') &&
               $this->registerHook('actionValidateStepComplete') &&
               $this->registerHook('actionObjectColissimoDepositSlipDeleteAfter') &&
               $this->registerHook('actionObjectOrderAddBefore') &&
               $this->registerHook('displayCustomerAccount') &&
               $this->registerHook('displayOrderDetail') &&
               $this->registerHook('actionAdminOrdersTrackingNumberUpdate') &&
               $this->registerHook('displayAdminProductsExtra') &&
               $this->registerHook('displayAdminOrdersListAfter') &&
               $this->registerHook('actionProductUpdate') &&
               $this->registerHook('actionAdminCategoriesControllerSaveAfter') &&
               $this->registerHook('actionCategoryFormBuilderModifier') &&
               $this->registerHook('actionAfterCreateCategoryFormHandler') &&
               $this->registerHook('actionAfterUpdateCategoryFormHandler') &&
               $this->registerHook('actionAdminCategoriesFormModifier') &&
               $this->registerHook('addWebserviceResources') &&
               $this->registerHook('actionAdminOrdersListingFieldsModifier') &&
               $this->registerHook('actionOrderGridQueryBuilderModifier') &&
               $this->registerHook('actionOrderGridDefinitionModifier');
    }

    /**
     * @return bool
     * @throws PrestaShopException
     */
    public function uninstall()
    {
        if (Tools::version_compare(_PS_VERSION_, '1.7', '<')) {
            $moduleTabs = Tab::getCollectionFromModule($this->name);
            foreach ($moduleTabs as $moduleTab) {
                $moduleTab->delete();
            }
        }

        return parent::uninstall();
    }

    /**
     *
     */
    public function initLogger()
    {
        $logFile = ColissimoTools::getCurrentLogFilePath();
        if (Configuration::get('COLISSIMO_LOGS')) {
            $handler = new ColissimoFileHandler($logFile);
        } else {
            $handler = new ColissimoNullHandler();
        }
        $this->logger = new ColissimoLogger($handler, $this->version);
    }

    /**
     * @param bool $testCredentials
     * @throws Exception
     */
    public function testTechnicalRequirements($testCredentials)
    {
        if (extension_loaded('curl') == false) {
            throw new Exception($this->l('You need to enable the cURL extension to use this module.'));
        }
        if (extension_loaded('zip') == false) {
            throw new Exception($this->l('You need to enable the zip PHP extension to use this module.'));
        }
        if (extension_loaded('soap') == false) {
            throw new Exception($this->l('You need to enable the SOAP extension to use this module.'));
        }
        if ($testCredentials) {
            if (!Configuration::get('COLISSIMO_ACCOUNT_LOGIN') || !Configuration::get('COLISSIMO_ACCOUNT_PASSWORD')) {
                throw new Exception($this->l('Please configure your contract number & password to use this module.'));
            }
        }
    }

    /**
     * @throws Exception
     */
    public function createModuleTables()
    {
        $colissimoServices = "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."colissimo_service` (
            `id_colissimo_service` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
            `id_carrier` INT(10) UNSIGNED NULL DEFAULT '0',
            `product_code` VARCHAR(5) NOT NULL DEFAULT '0',
            `commercial_name` VARCHAR(50) NOT NULL DEFAULT '0',
            `destination_type` ENUM('FRANCE','OM','EUROPE','WORLDWIDE', 'INTRA_DOM') NOT NULL DEFAULT 'FRANCE',
            `is_signature` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
            `is_pickup` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
            `is_return` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
            `type` VARCHAR(50) NOT NULL DEFAULT '0',
            PRIMARY KEY (`id_colissimo_service`),
            INDEX `product_code` (`product_code`),
            INDEX `id_carrier` (`id_carrier`)
        )";
        $colissimoOrder = "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."colissimo_order` (
            `id_colissimo_order` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
            `id_order` INT(10) UNSIGNED NOT NULL,
            `id_colissimo_service` INT(10) UNSIGNED NOT NULL DEFAULT '0',
            `id_colissimo_pickup_point` INT(10) UNSIGNED NOT NULL DEFAULT '0',
            `migration` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
            `ddp` TINYINT(3) UNSIGNED NULL DEFAULT '0',
            `ddp_cost` FLOAT NOT NULL,
            `hidden` TINYINT(3) UNSIGNED NULL DEFAULT '0',
            PRIMARY KEY (`id_colissimo_order`),
            INDEX `id_order` (`id_order`),
            INDEX `id_colissimo_service` (`id_colissimo_service`),
            INDEX `id_colissimo_pickup_point` (`id_colissimo_pickup_point`)
        )";
        $colissimoLabel = "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."colissimo_label` (
            `id_colissimo_label` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
            `id_colissimo_order` INT(10) UNSIGNED NOT NULL,
            `id_colissimo_deposit_slip` INT(10) UNSIGNED NULL DEFAULT '0',
            `shipping_number` VARCHAR(45) NOT NULL,
            `label_format` VARCHAR(3) NOT NULL,
            `return_label` INT(10) UNSIGNED NOT NULL DEFAULT '0',
            `cn23` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
            `coliship` TINYINT(1) NOT NULL DEFAULT '0',
            `migration` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
            `insurance` TINYINT(3) UNSIGNED NULL DEFAULT NULL,
            `file_deleted` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
            `date_add` DATETIME NOT NULL,
            PRIMARY KEY (`id_colissimo_label`),
            INDEX `id_colissimo_order` (`id_colissimo_order`),
            INDEX `id_colissimo_deposit_slip` (`id_colissimo_deposit_slip`)
        )";
        $colissimoLabelProduct = "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."colissimo_label_product`(
            `id_colissimo_label_product` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
            `id_colissimo_label` INT(10) UNSIGNED NOT NULL,
            `id_product` INT(10) UNSIGNED NOT NULL,
            `id_product_attribute` INT(10) UNSIGNED NOT NULL DEFAULT '0',
            `quantity` INT(10) UNSIGNED NOT NULL DEFAULT '0',
            `date_add` DATETIME NOT NULL,
            PRIMARY KEY (`id_colissimo_label_product`),
            INDEX `id_colissimo_label` (`id_colissimo_label`)
        )";
        $colissimoDepositSlip = "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."colissimo_deposit_slip` (
            `id_colissimo_deposit_slip` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
            `filename` VARCHAR(45) NOT NULL,
            `number` INT(11) UNSIGNED NOT NULL,
            `nb_parcel` INT(11) UNSIGNED NOT NULL,
            `file_deleted` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
            `date_add` DATETIME NOT NULL,
            PRIMARY KEY (`id_colissimo_deposit_slip`),
            INDEX `number` (`number`)
        )";
        $colissimoPickupPoint = "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."colissimo_pickup_point` (
            `id_colissimo_pickup_point` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
            `colissimo_id` VARCHAR(8) NOT NULL,
            `company_name` VARCHAR(64) NOT NULL,
            `address1` VARCHAR(120) NOT NULL,
            `address2` VARCHAR(120) NULL DEFAULT NULL,
            `address3` VARCHAR(120) NULL DEFAULT NULL,
            `city` VARCHAR(80) NOT NULL,
            `zipcode` VARCHAR(10) NOT NULL,
            `country` VARCHAR(64) NOT NULL,
            `iso_country` VARCHAR(2) NOT NULL,
            `product_code` VARCHAR(3) NOT NULL,
            `network` VARCHAR(10) NULL DEFAULT NULL,
            `date_add` DATETIME NOT NULL,
            `date_upd` DATETIME NOT NULL,
            PRIMARY KEY (`id_colissimo_pickup_point`),
            INDEX `colissimo_id` (`colissimo_id`),
            INDEX `iso_country` (`iso_country`),
            INDEX `product_code` (`product_code`)
        )";
        $colissimoCartPickupPoint = "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."colissimo_cart_pickup_point` (
            `id_cart` INT(11) NOT NULL DEFAULT '0',
            `id_colissimo_pickup_point` INT(11) NOT NULL DEFAULT '0',
        	`mobile_phone` VARCHAR(50) NULL DEFAULT NULL,
            PRIMARY KEY (`id_cart`, `id_colissimo_pickup_point`),
            UNIQUE INDEX `id_cart` (`id_cart`)
        )";
        $colissimoShipmentTracking = "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."colissimo_shipment_tracking` (
            `id_colissimo_label` INT(11) UNSIGNED NOT NULL DEFAULT '0',
            `status_text` VARCHAR(255) NULL DEFAULT NULL,
            `typology` VARCHAR(10) NULL DEFAULT NULL,
            `date_upd` DATETIME NOT NULL,
            PRIMARY KEY (`id_colissimo_label`)
        )";
        $colissimoTrackingCode = "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."colissimo_tracking_code` (
            `id_colissimo_tracking_code` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
            `clp_code` VARCHAR(10) NOT NULL DEFAULT '0',
            `inovert_code` VARCHAR(10) NULL DEFAULT '0',
            `typology` VARCHAR(10) NULL DEFAULT NULL,
        	`internal_text` VARCHAR(255) NULL DEFAULT NULL,
            PRIMARY KEY (`id_colissimo_tracking_code`),
            INDEX `clp_code` (`clp_code`),
            INDEX `inovert_code` (`inovert_code`),
            INDEX `typology` (`typology`)
        )";
        $colissimoMailboxReturn = "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."colissimo_mailbox_return` (
            `id_colissimo_label` INT(10) UNSIGNED NOT NULL,
            `pickup_date` VARCHAR(50) NULL DEFAULT NULL,
            `pickup_before` VARCHAR(5) NULL DEFAULT NULL,
            PRIMARY KEY (`id_colissimo_label`),
            INDEX `id_colissimo_label` (`id_colissimo_label`)
        )";
        $colissimoCustomCategory = "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."colissimo_custom_category` (
            `id_colissimo_custom_category` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
            `id_category` INT(10) NULL DEFAULT 0,
            `short_desc` VARCHAR(64) NULL DEFAULT NULL,
            `id_country_origin` INT(10) NULL DEFAULT 0,
            `hs_code` VARCHAR(50) NULL DEFAULT NULL,
            PRIMARY KEY (`id_colissimo_custom_category`)
        )";
        $colissimoCustomProduct = "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."colissimo_custom_product` (
            `id_colissimo_custom_product` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
            `id_product` INT(10) NULL DEFAULT 0,
            `short_desc` VARCHAR(64) NULL DEFAULT NULL,
            `id_country_origin` INT(10) NULL DEFAULT 0,
            `hs_code` VARCHAR(50) NULL DEFAULT NULL,
            PRIMARY KEY (`id_colissimo_custom_product`)
        )";
        $colissimoCustomDocument = "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."colissimo_custom_document` (
            `id_colissimo_custom_document` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
            `id_colissimo_label` INT(10) NOT NULL DEFAULT '0',
            `id_colissimo_order` INT(10) NOT NULL DEFAULT '0',
            `id_document` VARCHAR(50) NOT NULL DEFAULT '0',
            `type` ENUM('CN23','COMMERCIAL_INVOICE','OTHER') NOT NULL DEFAULT 'CN23',
            `date_add` DATETIME NOT NULL,
            PRIMARY KEY (`id_colissimo_custom_document`)
        )";
        $colissimoAddress = "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."colissimo_address` (
            `id_colissimo_address` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
            `id_cart` INT(11) NOT NULL DEFAULT '0',
            `code_porte1` VARCHAR(8) NOT NULL DEFAULT '0',
            `code_porte2` VARCHAR(8) NULL DEFAULT '0', 
            PRIMARY KEY (`id_colissimo_address`),
            INDEX `id_cart` (`id_cart`)
        )";

        $createTablesQueries = array(
            'services'          => $colissimoServices,
            'order'             => $colissimoOrder,
            'label'             => $colissimoLabel,
            'label_product'     => $colissimoLabelProduct,
            'deposit_slip'      => $colissimoDepositSlip,
            'pickup_point'      => $colissimoPickupPoint,
            'cart_pickup_point' => $colissimoCartPickupPoint,
            'shipment_tracking' => $colissimoShipmentTracking,
            'tracking_code'     => $colissimoTrackingCode,
            'mailbox_return'    => $colissimoMailboxReturn,
            'custom_category'   => $colissimoCustomCategory,
            'custom_product'    => $colissimoCustomProduct,
            'custom_document'   => $colissimoCustomDocument,
            'address'           => $colissimoAddress,
        );

        try {
            foreach ($createTablesQueries as $name => $createTablesQuery) {
                Db::getInstance()
                  ->execute($createTablesQuery);
                $this->logger->info('Table '.$name.' created.');
            }
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
            throw new Exception($this->l('Cannot create tables.'));
        }

        $columnExists = Db::getInstance()->executeS('SHOW COLUMNS FROM `'._DB_PREFIX_.'colissimo_order` LIKE "hidden"');
        if (empty($columnExists)) {
            $result = Db::getInstance()
                        ->execute('ALTER TABLE `'._DB_PREFIX_.'colissimo_order` ADD COLUMN `hidden` TINYINT(3) UNSIGNED NULL DEFAULT \'0\' AFTER `migration`;');
            if (!$result) {
                $this->logger->error('Cannot add column in colissimo_order table.');
            }
            $this->logger->info('New column hidden in colissimo_order created.');
        }

        $columnExists = Db::getInstance()
                          ->executeS('SHOW COLUMNS FROM `'._DB_PREFIX_.'colissimo_label` LIKE "insurance"');
        if (empty($columnExists)) {
            $result = Db::getInstance()
                        ->execute(
                            'ALTER TABLE `'._DB_PREFIX_.'colissimo_label` ADD COLUMN `insurance` TINYINT(3) UNSIGNED NULL DEFAULT NULL AFTER `migration`'
                        );
            if (!$result) {
                $this->logger->error('Cannot add column insurance in colissimo_label table.');
            }
            $this->logger->info('New column insurance in colissimo_label created.');
        }
    }

    /**
     * @throws Exception
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function createModuleCarriersAndServices()
    {
        $existingColissimoCarriers = Configuration::getMultiple(
            array(
                'COLISSIMO_CARRIER_AVEC_SIGNATURE',
                'COLISSIMO_CARRIER_SANS_SIGNATURE',
                'COLISSIMO_CARRIER_RELAIS',
                'COLISSIMO_CARRIER_ECO_OUTRE_MER',
            )
        );
        $colissimoCarriers = array(
            array(
                'name'                 => 'Colissimo Domicile sans signature',
                'delays'               => array(
                    'fr' => 'Livraison à domicile',
                    'en' => 'Home delivery in mailbox',
                ),
                'url'                  => '',
                'active'               => true,
                'shipping_handling'    => false,
                'range_behavior'       => 0,
                'is_module'            => true,
                'is_free'              => false,
                'shipping_external'    => true,
                'need_range'           => true,
                'external_module_name' => $this->name,
                'shipping_method'      => Carrier::SHIPPING_METHOD_WEIGHT,
                'service'              => 'SANS_SIGNATURE',
            ),
            array(
                'name'                 => 'Colissimo Domicile avec signature',
                'delays'               => array(
                    'fr' => 'Livraison à domicile contre signature',
                    'en' => 'Home delivery with proof of delivery',
                ),
                'url'                  => '',
                'active'               => true,
                'shipping_handling'    => false,
                'range_behavior'       => 0,
                'is_module'            => true,
                'is_free'              => false,
                'shipping_external'    => true,
                'need_range'           => true,
                'external_module_name' => $this->name,
                'shipping_method'      => Carrier::SHIPPING_METHOD_WEIGHT,
                'service'              => 'AVEC_SIGNATURE',
            ),
            array(
                'name'                 => 'Colissimo Points de retrait',
                'delays'               => array(
                    'fr' => 'Livraison à la poste, en relais Pickup & consignes Pickup Station',
                    'en' => 'Delivery at post office, Pickup points & lockers',
                ),
                'url'                  => '',
                'active'               => true,
                'shipping_handling'    => false,
                'range_behavior'       => 0,
                'is_module'            => true,
                'is_free'              => false,
                'shipping_external'    => true,
                'need_range'           => true,
                'external_module_name' => $this->name,
                'shipping_method'      => Carrier::SHIPPING_METHOD_WEIGHT,
                'service'              => 'RELAIS',
            ),
            array(
                'name'                 => 'Colissimo ECO Outre Mer',
                'delays'               => array(
                    'fr' => 'Livraison ECO Outre Mer',
                    'en' => 'Delivery ECO overseas',
                ),
                'url'                  => '',
                'active'               => true,
                'shipping_handling'    => false,
                'range_behavior'       => 0,
                'is_module'            => true,
                'is_free'              => false,
                'shipping_external'    => true,
                'need_range'           => true,
                'external_module_name' => $this->name,
                'shipping_method'      => Carrier::SHIPPING_METHOD_WEIGHT,
                'service'              => 'ECO_OUTRE_MER',
            ),
        );
        $languages = Language::getLanguages(false);
        foreach ($colissimoCarriers as $colissimoCarrier) {
            $idCarrier = $existingColissimoCarriers['COLISSIMO_CARRIER_'.$colissimoCarrier['service']];
            $oldCarrier = ColissimoCarrier::getCarrierByReference((int) $idCarrier);
            if ($oldCarrier !== false &&
                Validate::isLoadedObject($oldCarrier) &&
                $oldCarrier->external_module_name == $this->name
            ) {
                $this->logger->info('Carrier already exists');
                continue;
            }
            $this->logger->info('Creating carrier '.$colissimoCarrier['service']);
            $newCarrier = $this->createCarrier($colissimoCarrier, $languages);
            $newCarrier->setGroups(Group::getGroups($this->context->language->id));
        }
        $carriersToKeep = Configuration::getMultiple(
            array(
                'COLISSIMO_CARRIER_AVEC_SIGNATURE',
                'COLISSIMO_CARRIER_SANS_SIGNATURE',
                'COLISSIMO_CARRIER_RELAIS',
                'COLISSIMO_CARRIER_ECO_OUTRE_MER',
            )
        );
        //@formatter:off
        Db::getInstance()->update(
            'carrier',
            array('deleted' => 1),
            'external_module_name = "'.pSQL($this->name).'" AND id_reference NOT IN('.implode(',', array_map('intval', $carriersToKeep)).')'
        );
        //@formatter:off
        $colissimoServices = ColissimoTools::getColissimoServicesSource();
        $this->logger->info('Services from CSV', array('csv' => $colissimoServices));
        try {
            $this->createServices($colissimoServices);
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
            throw new Exception($this->l('Cannot create Colissimo services.'));
        }
    }

    /**
     * @param array|bool $trackingCodes
     * @throws Exception
     */
    public function createTrackingCodes($trackingCodes)
    {
        if ($trackingCodes === false) {
            throw new Exception(
                $this->l('Cannot process tracking codes csv files. Please check permissions on the module directory')
            );
        }
        $exceptionsCount = 0;
        foreach ($trackingCodes as $trackingCode) {
            $colissimoTrackingCode = ColissimoTrackingCode::getByClpCode($trackingCode['clp_code']);
            $colissimoTrackingCode->clp_code = pSQL($trackingCode['clp_code']);
            $colissimoTrackingCode->inovert_code = pSQL($trackingCode['inovert_code']);
            $colissimoTrackingCode->typology = pSQL($trackingCode['typology']);
            $colissimoTrackingCode->internal_text = pSQL($trackingCode['internal_text']);
            try {
                $colissimoTrackingCode->save();
            } catch (Exception $e) {
                $this->logger->error($e->getMessage());
                $exceptionsCount++;
                continue;
            }
        }
        $this->logger->info('Tracking codes created. '.$exceptionsCount.' errors thrown.');
    }

    /**
     * @param array $carrierArray
     * @param array $languages
     * @return ColissimoCarrier
     * @throws Exception
     * @throws PrestaShopException
     */
    public function createCarrier($carrierArray, $languages)
    {
        $carrier = new ColissimoCarrier();
        $carrier->hydrate($carrierArray);
        foreach ($languages as $language) {
            $carrier->delay[(int) $language['id_lang']] =
                isset($carrierArray['delays'][$language['iso_code']]) ? $carrierArray['delays'][$language['iso_code']] :
                    $carrierArray['delays']['en'];
        }

        if (!$carrier->save()) {
            throw new Exception($this->l('Cannot create carriers.'));
        }

        $logoPath = _PS_MODULE_DIR_.$this->name.'/views/img/colissimo_carrier_17.png';
        $carrier->setLogo($logoPath, $this->context->language->id);
        Configuration::updateGlobalValue('COLISSIMO_CARRIER_'.$carrierArray['service'], $carrier->id);

        return $carrier;
    }

    /**
     * @param array $colissimoServices
     * @throws Exception
     * @throws PrestaShopException
     */
    public function createServices($colissimoServices)
    {
        foreach ($colissimoServices as $serviceKey => $services) {
            foreach ($services as $service) {
                $id = ColissimoService::getServiceIdByProductCodeDestinationType(
                    $service['product_code'],
                    $service['destination_type']
                );
                $colissimoService = new ColissimoService((int) $id);
                $colissimoService->hydrate($service);
                $colissimoService->id_carrier = (int) Configuration::get('COLISSIMO_CARRIER_'.$serviceKey);
                $colissimoService->type = pSQL($serviceKey);
                $colissimoService->save();
                $this->logger->info('Saved service '.$serviceKey.': '.(int) $colissimoService->id);
            }
        }
    }

    /**
     *
     */
    public function setDefaultConfiguration()
    {
        //@formatter:off
        Configuration::updateValue('COLISSIMO_ACCOUNT_LOGIN', '');
        Configuration::updateValue('COLISSIMO_ACCOUNT_PASSWORD', '');
        Configuration::updateValue('COLISSIMO_ACCOUNT_TYPE', '{"FRANCE":1}');
        Configuration::updateValue('COLISSIMO_SENDER_ADDRESS', '');
        Configuration::updateValue('COLISSIMO_RETURN_ADDRESS', '');
        Configuration::updateValue('COLISSIMO_USE_RETURN_ADDRESS', 0);
        Configuration::updateValue('COLISSIMO_WIDGET_ENDPOINT', 'https://ws.colissimo.fr/widget-point-retrait/rest/authenticate.rest');
        Configuration::updateValue('COLISSIMO_WIDGET_REMOTE', 1);
        Configuration::updateValue('COLISSIMO_WIDGET_COLOR_1', '#333333');
        Configuration::updateValue('COLISSIMO_WIDGET_COLOR_2', '#EA690A');
        Configuration::updateValue('COLISSIMO_WIDGET_FONT', 'Arial');
        $generateLabelStatuses = array_fill_keys(array((int) Configuration::get('PS_OS_PREPARATION')), 1);
        Configuration::updateValue('COLISSIMO_ORDER_PREPARATION_TIME', 1);
        Configuration::updateValue('COLISSIMO_GENERATE_LABEL_STATUSES', json_encode($generateLabelStatuses));
        Configuration::updateValue('COLISSIMO_USE_SHIPPING_IN_PROGRESS', 1);
        Configuration::updateValue('COLISSIMO_USE_HANDLED_BY_CARRIER', 0);
        Configuration::updateValue('COLISSIMO_DISPLAY_TRACKING_NUMBER', 0);
        Configuration::updateValue('COLISSIMO_GENERATE_LABEL_PRESTASHOP', 1);
        Configuration::updateValue('COLISSIMO_POSTAGE_MODE_MANUAL', 1);
        Configuration::updateValue('COLISSIMO_LABEL_FORMAT', 'PDF_A4_300dpi');
        Configuration::updateValue('COLISSIMO_DEFAULT_ORIGIN_COUNTRY', 8);
        Configuration::updateValue('COLISSIMO_LABEL_DISPLAY_REFERENCE', 1);
        Configuration::updateValue('COLISSIMO_DEFAULT_HS_CODE', '');
        Configuration::updateValue('COLISSIMO_USE_WEIGHT_TARE', 0);
        Configuration::updateValue('COLISSIMO_INSURE_SHIPMENTS', 0);
        Configuration::updateValue('COLISSIMO_POSTAL_PARTNER', 0);
        Configuration::updateValue('COLISSIMO_ENABLE_RETURN', 0);
        Configuration::updateValue('COLISSIMO_AUTO_PRINT_RETURN_LABEL', 0);
        Configuration::updateValue('COLISSIMO_DISPLAY_RETURN_LABEL_CUSTOMER', 0);
        Configuration::updateValue('COLISSIMO_ENABLE_MAILBOX_RETURN', 0);
        Configuration::updateValue('COLISSIMO_ENABLE_DDP', 0);
        Configuration::updateValue('COLISSIMO_DDP_COST', 18);
        Configuration::updateValue('COLISSIMO_DDP_GB_COST', 10);
        Configuration::updateValue('COLISSIMO_ENABLE_DOOR_CODES', 0);
        Configuration::updateValue('COLISSIMO_WEIGHTING_PRICES', 0);
        Configuration::updateValue('COLISSIMO_WEIGHTING_TYPE', 'amount');
        Configuration::updateValue('COLISSIMO_WEIGHTING_VALUE_PERCENT', '0');
        Configuration::updateValue('COLISSIMO_WEIGHTING_VALUE_AMOUNT', '0');
        Configuration::updateValue('COLISSIMO_WIDGET_PHONE', '0');
        Configuration::updateGlobalValue('COLISSIMO_FILES_LIMIT', 3000);
        Configuration::updateGlobalValue('COLISSIMO_FILES_LIFETIME', 14);
        //@formatter:on
    }

    /**
     * @return array
     */
    public function getTabs()
    {
        $languages = Language::getLanguages(false);
        $tabNames = array();
        foreach ($languages as $language) {
            if ($language['iso_code'] == 'fr') {
                $tabNames['dashboard'][$language['locale']] = 'Colissimo - Tableau de bord';
                $tabNames['dashboard'][$language['iso_code']] = 'Colissimo - Tableau de bord';
                $tabNames['postage'][$language['locale']] = 'Colissimo - Affranchissement';
                $tabNames['postage'][$language['iso_code']] = 'Colissimo - Affranchissement';
                $tabNames['deposit'][$language['locale']] = 'Colissimo - Bordereaux';
                $tabNames['deposit'][$language['iso_code']] = 'Colissimo - Bordereaux';
                $tabNames['coliship'][$language['locale']] = 'Colissimo - Coliship';
                $tabNames['coliship'][$language['iso_code']] = 'Colissimo - Coliship';
                $tabNames['customs'][$language['locale']] = 'Colissimo - Documents';
                $tabNames['customs'][$language['iso_code']] = 'Colissimo - Documents';
            } else {
                $tabNames['dashboard'][$language['locale']] = 'Colissimo - Dashboard';
                $tabNames['dashboard'][$language['iso_code']] = 'Colissimo - Dashboard';
                $tabNames['postage'][$language['locale']] = 'Colissimo - Postage';
                $tabNames['postage'][$language['iso_code']] = 'Colissimo - Postage';
                $tabNames['deposit'][$language['locale']] = 'Colissimo - Deposit slip';
                $tabNames['deposit'][$language['iso_code']] = 'Colissimo - Deposit slip';
                $tabNames['coliship'][$language['locale']] = 'Colissimo - Coliship';
                $tabNames['coliship'][$language['iso_code']] = 'Colissimo - Coliship';
                $tabNames['customs'][$language['locale']] = 'Colissimo - Customs';
                $tabNames['customs'][$language['iso_code']] = 'Colissimo - Customs';
            }
        }

        $tabs = array(
            array(
                'visible'    => false,
                'class_name' => 'AdminColissimoTestCredentials',
            ),
            array(
                'visible'    => false,
                'class_name' => 'AdminColissimoLabel',
            ),
            array(
                'visible'    => false,
                'class_name' => 'AdminColissimoMigration',
            ),
            array(
                'visible'    => false,
                'class_name' => 'AdminColissimoLogs',
            ),
            array(
                'visible'           => true,
                'class_name'        => 'AdminColissimoDashboard',
                'parent_class_name' => 'AdminParentShipping',
                'ParentClassName'   => 'AdminParentShipping',
                'name'              => $tabNames['dashboard'],
            ),
            array(
                'visible'           => true,
                'class_name'        => 'AdminColissimoAffranchissement',
                'parent_class_name' => 'AdminParentShipping',
                'ParentClassName'   => 'AdminParentShipping',
                'name'              => $tabNames['postage'],
            ),
            array(
                'visible'           => true,
                'class_name'        => 'AdminColissimoDepositSlip',
                'parent_class_name' => 'AdminParentShipping',
                'ParentClassName'   => 'AdminParentShipping',
                'name'              => $tabNames['deposit'],
            ),
            array(
                'visible'           => true,
                'class_name'        => 'AdminColissimoColiship',
                'parent_class_name' => 'AdminParentShipping',
                'ParentClassName'   => 'AdminParentShipping',
                'name'              => $tabNames['coliship'],
            ),
            array(
                'visible'           => true,
                'class_name'        => 'AdminColissimoCustomsDocuments',
                'parent_class_name' => 'AdminParentShipping',
                'ParentClassName'   => 'AdminParentShipping',
                'name'              => $tabNames['customs'],
            ),
        );

        return $tabs;
    }

    /**
     * @throws Exception
     */
    public function installMenus()
    {
        if (Tools::version_compare(_PS_VERSION_, '1.7.1', '<')) {
            $tabs = $this->getTabs();
            foreach ($tabs as $tab) {
                $this->installMenu($tab);
            }
        }
    }

    /**
     * @param array $menu
     * @throws Exception
     */
    public function installMenu($menu)
    {
        $tab = new Tab();
        $tab->active = (bool) $menu['visible'];
        $tab->name = array();
        $tab->class_name = pSQL($menu['class_name']);
        $names = isset($menu['name']) ? $menu['name'] : array('en' => $menu['class_name']);
        $langs = Language::getLanguages(true);
        foreach ($langs as $lang) {
            $tab->name[$lang['id_lang']] =
                isset($names[$lang['iso_code']]) ? pSQL($names[$lang['iso_code']]) : pSQL($names['en']);
        }
        if (isset($menu['parent_class_name'])) {
            $tab->id_parent = (int) Tab::getIdFromClassName($menu['parent_class_name']);
        }
        $tab->module = pSQL($this->name);
        if (!$tab->add()) {
            throw new Exception($this->l('Cannot create Colissimo menu.'));
        }
    }

    /**
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function installOrderStates()
    {
        $shippingInProgressLang = array(
            'fr' => 'En cours d’expédition',
            'en' => 'Shipping in progress',
        );
        $shippingInProgressState = new OrderState(
            (int) Configuration::getGlobalValue('COLISSIMO_OS_SHIPPING_IN_PROGRESS')
        );
        if (!Validate::isLoadedObject($shippingInProgressState)) {
            $shippingInProgressState->name = array();
            $shippingInProgressState->module_name = pSQL($this->name);
            $shippingInProgressState->color = '#e6600c';
            $shippingInProgressState->send_email = false;
            $shippingInProgressState->hidden = false;
            $shippingInProgressState->delivery = false;
            $shippingInProgressState->logable = false;
            $shippingInProgressState->invoice = false;
            $languages = Language::getLanguages(false);
            foreach ($languages as $language) {
                $name = isset($shippingInProgressLang[$language['iso_code']]) ? $shippingInProgressLang[$language['iso_code']] : $shippingInProgressLang['en'];
                $shippingInProgressState->name[(int) $language['id_lang']] = pSQL($name);
            }
            if ($shippingInProgressState->save()) {
                $source = _PS_MODULE_DIR_.$this->name.'/views/img/os_colissimo.gif';
                $destination = _PS_ROOT_DIR_.'/img/os/'.(int) $shippingInProgressState->id.'.gif';
                @copy($source, $destination);
                Configuration::updateGlobalValue(
                    'COLISSIMO_OS_SHIPPING_IN_PROGRESS',
                    (int) $shippingInProgressState->id
                );
            }
        }
        $handledByCarrierLang = array(
            'fr' => 'Remis au transporteur',
            'en' => 'Handled by carrier',
        );
        $handledByCarrierState = new OrderState(
            (int) Configuration::getGlobalValue('COLISSIMO_OS_HANDLED_BY_CARRIER')
        );
        if (!Validate::isLoadedObject($handledByCarrierState)) {
            $handledByCarrierState->name = array();
            $handledByCarrierState->module_name = pSQL($this->name);
            $handledByCarrierState->color = '#e6600c';
            $handledByCarrierState->send_email = false;
            $handledByCarrierState->hidden = false;
            $handledByCarrierState->delivery = false;
            $handledByCarrierState->logable = false;
            $handledByCarrierState->invoice = false;
            $languages = Language::getLanguages(false);
            foreach ($languages as $language) {
                $name = isset($handledByCarrierLang[$language['iso_code']]) ? $handledByCarrierLang[$language['iso_code']] : $handledByCarrierLang['en'];
                $handledByCarrierState->name[(int) $language['id_lang']] = pSQL($name);
            }
            if ($handledByCarrierState->save()) {
                $source = _PS_MODULE_DIR_.$this->name.'/views/img/os_colissimo.gif';
                $destination = _PS_ROOT_DIR_.'/img/os/'.(int) $handledByCarrierState->id.'.gif';
                @copy($source, $destination);
                Configuration::updateGlobalValue(
                    'COLISSIMO_OS_HANDLED_BY_CARRIER',
                    (int) $handledByCarrierState->id
                );
            }
        }
    }

    /**
     * @return array
     * @throws SmartyException
     */
    public function getWhatsNewModal()
    {
        return array(
            'modal_id' => 'colissimo-modal-whatsnew',
            'modal_class' => 'modal-lg',
            'modal_title' => '<i class="icon icon-bullhorn"></i> '.$this->l('What\'s new?'),
            'modal_content' => $this->context->smarty->fetch($this->local_path.'views/templates/admin/whatsnew/whatsnew.tpl'),
        );
    }

    /**
     * @return string
     * @throws Exception
     * @throws SmartyException
     */
    public function getContent()
    {
        $this->context->controller->modals[] = $this->getWhatsNewModal();
        $this->setColissimoHeader();
        $this->moduleConfiguration =
            new ColissimoModuleConfiguration($this->context, $this->local_path, $this->getPathUri(), $this->version);
        $this->postProcessConfiguration();
        $output = $this->moduleConfiguration->getContent();
        if (empty($this->moduleConfiguration->modulesToMigrate)) {
            $senderAddress = new ColissimoMerchantAddress('sender');
            $errors = $this->validateMerchantAddress($senderAddress);
            if (!empty($errors)) {
                //@formatter:off
                $this->context->controller->warnings[] = $this->l('Please fill you sender address in the "My Colissimo account" tab to take full advantage of the module\'s features.');
                //@formatter:on
                foreach ($errors as $error) {
                    $this->context->controller->warnings[] = $error;
                }
            }
            if (Configuration::get('COLISSIMO_USE_RETURN_ADDRESS')) {
                $returnAddress = new ColissimoMerchantAddress('return');
                $errors = $this->validateMerchantAddress($returnAddress);
                if (!empty($errors)) {
                    //@formatter:off
                    $this->context->controller->warnings[] = $this->l('Please fill a valid return address.');
                    //@formatter:on
                    foreach ($errors as $error) {
                        $this->context->controller->warnings[] = $error;
                    }
                }
            }
        }

        return $output;
    }

    /**
     *
     */
    public function postProcessConfiguration()
    {
        $this->context->smarty->assign('active', 'intro');
        if (Tools::isSubmit('submitColissimoSenderAddressConfigForm')) {
            $this->context->smarty->assign('active', 'account');
            $this->postProcessSenderAddress();
        } elseif (Tools::isSubmit('submitColissimoReturnAddressConfigForm')) {
            $this->context->smarty->assign('active', 'account');
            $this->postProcessReturnAddress();
        } elseif (Tools::isSubmit('submitColissimoAccountConfigForm')) {
            $this->context->smarty->assign('active', 'account');
            $this->postProcessAccountConfig();
        } elseif (Tools::isSubmit('submitColissimoWidgetConfigForm')) {
            $this->context->smarty->assign('active', 'fo');
            $this->postProcessWidgetConfig();
        } elseif (Tools::isSubmit('submitColissimoFrontConfiguration')) {
            $this->context->smarty->assign('active', 'fo');
            $this->postProcessColissimoFrontConfiguration();
        } elseif (Tools::isSubmit('submitColissimoWidgetPhone')) {
            $this->context->smarty->assign('active', 'fo');
            $this->postProcessColissimoWidgetPhone();
        } elseif (Tools::isSubmit('submitColissimoBackConfigOrdersForm')) {
            $this->context->smarty->assign('active', 'bo');
            $this->postProcessBackOrdersConfig();
        }  elseif (Tools::isSubmit('submitColissimoPrintConfigForm')) {
            $this->context->smarty->assign('active', 'bo');
            $this->postProcessBackPrintConfig();
        } elseif (Tools::isSubmit('submitColissimoBackShippingForm')) {
            $this->context->smarty->assign('active', 'bo');
            $this->postProcessBackShippingConfig();
        } elseif (Tools::isSubmit('submitColissimoBackPostageConfigForm')) {
            $this->context->smarty->assign('active', 'bo');
            $this->postProcessBackPostageConfig();
        } elseif (Tools::isSubmit('submitDownloadKit')) {
            $this->context->smarty->assign('active', 'bo');
            $this->postProcessDownloadKit();
        } elseif (Tools::isSubmit('submitColissimoShipmentsConfigForm')) {
            $this->context->smarty->assign('active', 'bo');
            $this->postProcessShipmentsConfig();
        } elseif (Tools::isSubmit('submitColissimoFilesConfigForm')) {
            $this->context->smarty->assign('active', 'files');
            $this->postProcessFilesConfig();
        }
    }

    /**
     * @param ColissimoMerchantAddress $merchandAddress
     * @return array
     */
    public function validateMerchantAddress($merchandAddress)
    {
        $errors = array();
        if ($merchandAddress->lastName && !Validate::isName($merchandAddress->lastName)) {
            $errors[] = $this->l('Please fill a valid lastname.');
        }
        if ($merchandAddress->firstName && !Validate::isName($merchandAddress->firstName)) {
            $errors[] = $this->l('Please fill a valid fistname.');
        }
        if (!Validate::isAddress($merchandAddress->line0) ||
            !Validate::isAddress($merchandAddress->line1) ||
            !Validate::isAddress($merchandAddress->line2) ||
            !Validate::isAddress($merchandAddress->line3)
        ) {
            $errors[] = $this->l('Please fill a valid address.');
        }
        if ($merchandAddress->city && !Validate::isCityName($merchandAddress->city)) {
            $errors[] = $this->l('Please fill a valid city.');
        }
        if ($merchandAddress->countryCode) {
            try {
                $country = new Country((int) Country::getByIso($merchandAddress->countryCode));
                if (!Validate::isPostCode($merchandAddress->zipCode) ||
                    !$country->checkZipCode($merchandAddress->zipCode)
                ) {
                    $errors[] = $this->l('Please fill a valid zipcode.');
                }
            } catch (Exception $e) {
                $errors[] = $this->l('Please fill a valid country.');
            }
        }
        if (Tools::isSubmit('colissimo_is_mobile_valid')) {
            if (Tools::getValue('colissimo_is_mobile_valid') == 0) {
                $errors[] = $this->l('Please fill a valid phone number.');
            }
        }
        if (!Validate::isEmail($merchandAddress->email)) {
            $errors[] = $this->l('Please fill a valid email address.');
        }

        return $errors;
    }

    public function validateSenderAddress($senderAddress)
    {
        $errors = array();
        if (!Validate::isName($senderAddress['sender_lastname'])) {
            $errors[] = $this->l('Please fill a valid lastname.');
        }
        if (!Validate::isName($senderAddress['sender_firstname'])) {
            $errors[] = $this->l('Please fill a valid fistname.');
        }
        if (!Validate::isAddress($senderAddress['sender_address1']) ||
            !Validate::isAddress($senderAddress['sender_address2']) ||
            !Validate::isAddress($senderAddress['sender_address3']) ||
            !Validate::isAddress($senderAddress['sender_address4'])
        ) {
            $errors[] = $this->l('Please fill a valid address.');
        }
        if (!Validate::isCityName($senderAddress['sender_city'])) {
            $errors[] = $this->l('Please fill a valid city.');
        }
        try {
            $country = new Country((int) Country::getByIso($senderAddress['sender_country']));
            if (!Validate::isPostCode($senderAddress['sender_zipcode']) ||
                !$country->checkZipCode($senderAddress['sender_zipcode'])
            ) {
                $errors[] = $this->l('Please fill a valid zipcode.');
            }
        } catch (Exception $e) {
            $errors[] = $this->l('Please fill a valid country.');
        }
        if (!Validate::isPostCode($senderAddress['sender_zipcode']) ||
            !$country->checkZipCode($senderAddress['sender_zipcode'])
        ) {
            $errors[] = $this->l('Please fill a valid zipcode.');
        }
        if ($senderAddress['colissimo_is_mobile_valid'] == 0) {
            $errors[] = $this->l('Please fill a valid phone.');
        }
        if (!Validate::isEmail($senderAddress['sender_email'])) {
            $errors[] = $this->l('Please fill a valid email address.');
        }

        return $errors;
    }

    /**
     * @param array|mixed $returnAddress
     * @return array
     */
    public function validateReturnAddress($returnAddress)
    {
        $errors = array();
        if (!Validate::isName($returnAddress['return_lastname'])) {
            $errors[] = $this->l('Please fill a valid return lastname.');
        }
        if (!Validate::isName($returnAddress['return_firstname'])) {
            $errors[] = $this->l('Please fill a valid return fistname.');
        }
        if (!Validate::isAddress($returnAddress['return_address1']) ||
            !Validate::isAddress($returnAddress['return_address2']) ||
            !Validate::isAddress($returnAddress['return_address3']) ||
            !Validate::isAddress($returnAddress['return_address4'])
        ) {
            $errors[] = $this->l('Please fill valid return addresses values.');
        }
        if (!Validate::isCityName($returnAddress['return_city'])) {
            $errors[] = $this->l('Please fill a valid return city.');
        }
        try {
            $country = new Country((int) Country::getByIso($returnAddress['return_country']));
            if (!Validate::isPostCode($returnAddress['return_zipcode']) ||
                !$country->checkZipCode($returnAddress['return_zipcode'])
            ) {
                $errors[] = $this->l('Please fill a valid return zipcode.');
            }
        } catch (Exception $e) {
            $errors[] = $this->l('Please fill a valid return country.');
        }
        if ($returnAddress['colissimo_is_mobile_valid'] == 0) {
            $errors[] = $this->l('Please fill a valid return phone.');
        }
        if (!Validate::isEmail($returnAddress['return_email'])) {
            $errors[] = $this->l('Please fill a valid return email address.');
        }

        return $errors;
    }

    /**
     *
     */
    public function postProcessSenderAddress()
    {
        $senderAddressFromPost = ColissimoTools::getValueMultiple($this->moduleConfiguration->senderAddressFields);
        $senderAddressFromPost['sender_phone'] = $senderAddressFromPost['sender_phone']['full'];
        $senderAddress = new ColissimoMerchantAddress('sender', $senderAddressFromPost);
        $errors = $this->validateMerchantAddress($senderAddress);
        if (!empty($errors)) {
            $this->context->controller->errors = $errors;
        } else {
            Configuration::updateValue('COLISSIMO_SENDER_ADDRESS', $senderAddress->toJSON());
            $this->context->controller->confirmations = $this->l('Sender address saved successfully.');
        }
    }

    /**
     *
     */
    public function postProcessReturnAddress()
    {
        $returnAddressFromPost = ColissimoTools::getValueMultiple($this->moduleConfiguration->returnAddressFields);
        $returnAddressFromPost['return_phone'] = $returnAddressFromPost['return_phone']['full'];
        $returnAddress = new ColissimoMerchantAddress('return', $returnAddressFromPost);
        $errors = $this->validateMerchantAddress($returnAddress);
        if (!empty($errors)) {
            $this->context->controller->errors = $errors;
        } else {
            Configuration::updateValue('COLISSIMO_RETURN_ADDRESS', $returnAddress->toJSON());
            $this->context->controller->confirmations = $this->l('Return address saved successfully.');
        }
    }

    /**
     *
     */
    public function postProcessAccountConfig()
    {
        $keys = $this->moduleConfiguration->accountFields;
        foreach ($keys as $key) {
            Configuration::updateValue($key, trim(Tools::getValue($key)));
        }
        $accountTypeChoices = Tools::getValue('COLISSIMO_ACCOUNT_TYPE');
        if ($accountTypeChoices) {
            $accountType = array();
            foreach ($accountTypeChoices as $accountTypeChoice) {
                $accountType[$accountTypeChoice[0]] = 1;
            }
            Configuration::updateValue('COLISSIMO_ACCOUNT_TYPE', json_encode($accountType));
            $this->context->controller->confirmations = $this->l('Account details saved successfully.');
        } else {
            $this->context->controller->errors = $this->l('Please select at least one account type.');
        }
    }
    
    /**
     *
     */
    public function postProcessColissimoFrontConfiguration()
    {
        $keys = $this->moduleConfiguration->frontConfigFields;
        foreach ($keys as $key) {
            $value = Tools::getValue($key);
            if ($key === 'COLISSIMO_WEIGHTING_VALUE_PERCENT' || $key === 'COLISSIMO_WEIGHTING_VALUE_AMOUNT') {
                if ($value && !Validate::isFloat($value)) {
                    return $this->displayError($this->l('Please enter valid weighting values.'));
                }
                Configuration::updateValue($key, (float)Tools::getValue($key));
            } else {
                Configuration::updateValue($key, Tools::getValue($key));
            }
        }
        $this->context->controller->confirmations = $this->l('Configuration saved successfully.');
    }
    
    /**
     *
     */
    public function postProcessColissimoWidgetPhone()
    {
        Configuration::updateValue('COLISSIMO_WIDGET_PHONE', Tools::getValue('COLISSIMO_WIDGET_PHONE'));
        $this->context->controller->confirmations = $this->l('Configuration saved successfully.');
    }
    
    /**
     *
     */
    public function postProcessWidgetConfig()
    {
        $keys = $this->moduleConfiguration->widgetFields;
        foreach ($keys as $key) {
            Configuration::updateValue($key, Tools::getValue($key));
        }
        $this->context->controller->confirmations = $this->l('Widget configuration saved successfully.');
    }
    
    /**
     *
     */
    public function postProcessBackOrdersConfig()
    {
        $keys = $this->moduleConfiguration->backOrdersFields;
        if (!ctype_digit(Tools::getValue('COLISSIMO_ORDER_PREPARATION_TIME'))) {
            $this->context->controller->errors = $this->l('Please fill a valid preparation time value (integer only).');

            return;
        }
        foreach ($keys as $key) {
            Configuration::updateValue($key, Tools::getValue($key));
        }
        if (Tools::getValue('COLISSIMO_GENERATE_LABEL_STATUSES')) {
            $statuses = array_fill_keys(Tools::getValue('COLISSIMO_GENERATE_LABEL_STATUSES'), 1);
        } else {
            $statuses = array();
        }
        Configuration::updateValue('COLISSIMO_GENERATE_LABEL_STATUSES', json_encode($statuses));
        $this->context->controller->confirmations = $this->l('Back-office configuration saved successfully.');
    }
    
    /**
     *
     */
    public function postProcessBackPrintConfig()
    {
        $keys = $this->moduleConfiguration->backPrintFields;
        $useThermalPrinter = Tools::getValue('COLISSIMO_USE_THERMAL_PRINTER');
        $useEthernet = Tools::getValue('COLISSIMO_USE_ETHERNET');
        if ($useThermalPrinter &&
            $useEthernet &&
            !filter_var(Tools::getValue('COLISSIMO_PRINTER_IP_ADDR', FILTER_VALIDATE_IP))
        ) {
            $this->context->controller->errors = $this->l('Please fill a valid IP address.');

            return;
        }
        foreach ($keys as $key) {
            Configuration::updateValue($key, Tools::getValue($key));
        }
        $this->context->controller->confirmations = $this->l('Back-office configuration saved successfully.');
    }
    
    /**
     *
     */
    public function postProcessBackShippingConfig()
    {
        $keys = $this->moduleConfiguration->backShippingFields;
        $hsCode = Tools::getValue('COLISSIMO_DEFAULT_HS_CODE');
        if ($hsCode && !ColissimoTools::isValidHsCode($hsCode)) {
            //@formatter:off
            $this->context->controller->errors = $this->l('Please fill a valid HS Code value. Expected formats: 6, 8 or 10 digits only.');
            //@formatter:on

            return;
        }
        foreach ($keys as $key) {
            Configuration::updateValue($key, Tools::getValue($key));
        }
        $this->context->controller->confirmations = $this->l('Back-office configuration saved successfully.');
    }

    /**
     *
     */
    public function postProcessBackPostageConfig()
    {
        $keys = $this->moduleConfiguration->backPostageFields;
        foreach ($keys as $key) {
            Configuration::updateValue($key, Tools::getValue($key));
        }
        $this->context->controller->confirmations = $this->l('Back-office configuration saved successfully.');
    }
    
    /**
     *
     */
    public function postProcessShipmentsConfig()
    {
        $keys = $this->moduleConfiguration->defaultShipmentsFields;
        foreach ($keys as $key) {
            $value = Tools::getValue($key);
            if ($key === 'COLISSIMO_DEFAULT_WEIGHT_TARE') {
                if ($value && !Validate::isFloat($value)) {
                    $this->context->controller->errors = $this->l('Please enter valid weigh tare value.');
                    return;
                }
            }
            Configuration::updateValue($key, Tools::getValue($key));
        }
        $this->context->controller->confirmations = $this->l('Default shipments configuration saved successfully.');
    }

    /**
     *
     */
    public function postProcessFilesConfig()
    {
        $keys = $this->moduleConfiguration->filesFields;
        if (!ctype_digit(Tools::getValue('COLISSIMO_FILES_LIMIT')) ||
            !ctype_digit(Tools::getValue('COLISSIMO_FILES_LIFETIME'))
        ) {
            //@formatter:off
            $this->context->controller->errors = $this->l('Please fill a valid files limit value and files lifetime value (integers only).');
            //@formatter:on

            return;
        }
        foreach ($keys as $key) {
            Configuration::updateGlobalValue($key, Tools::getValue($key));
        }
        $this->context->controller->confirmations = $this->l('Files management configuration saved successfully.');
    }

    /**
     *
     */
    public function ajaxProcessUseReturnAddress()
    {
        $return_adr = Tools::getValue('returnAddress');
        Configuration::updateValue('COLISSIMO_USE_RETURN_ADDRESS', (int) $return_adr);
        $return = array(
            'error'   => false,
            'message' => $this->l('Parameter saved successfully'),
        );
        die(json_encode($return));
    }
    
    /**
     *
     */
    public function ajaxProcessOrdersMassAssignment()
    {
        $orderId = (int) Tools::getValue('id_order');
        $isSignature = Tools::getValue('service');
        $this->logger->setChannel('OrdersMassAssignment');
        $order = new Order($orderId);
        if ($isSignature == 1) {
            $serviceType = ColissimoService::TYPE_SIGN;
        } else {
            $serviceType = ColissimoService::TYPE_NOSIGN;
        }
        if (ColissimoOrder::exists($orderId) == 0) {
            $eligibleServices = $this->getEligibleServiceByOrder($order);
            foreach ($eligibleServices as $id => $service) {
                if (ColissimoService::getServiceTypeById($id) == $serviceType) {
                    $colissimoOrder = new ColissimoOrder();
                    $colissimoOrder->id_order = (int) $orderId;
                    $colissimoOrder->id_colissimo_service = (int) $id;
                    $colissimoOrder->id_colissimo_pickup_point = 0;
                    $colissimoOrder->migration = 0;
                    $colissimoOrder->hidden = 0;
                    $colissimoOrder->save();
                    $this->logger->info(sprintf($this->l('The Order %s has been successfully associated to Colissimo %s'), $order->reference, $serviceType));
                    
                    $return = array(
                        'error'   => false,
                        'message' => sprintf($this->l('Order %s successfully associated to colissimo %s'), $order->reference, $serviceType),
                    );
                   die(json_encode($return));
                }
            }
        }
        $this->logger->error(sprintf($this->l('Cannot associate the order %s to Colissimo %s'), $order->reference, $serviceType));
            
        $return = array(
            'error'   => true,
            'message' => sprintf($this->l('Cannot associate the order %s to Colissimo %s'), $order->reference, $serviceType),
        );
        die(json_encode($return));
    }
    
    /**
     *
     */
    public function postProcessDownloadKit()
    {
       ColissimoTools::downloadPrintingKit();
    }

    /**
     *
     */
    public function setColissimoHeader()
    {
        $data = array(
            'img_path'          => $this->getPathUri().'views/img/',
            'module_path'       => $this->getPathUri(),
            'module_version'    => $this->version,
            'id_product_addons' => self::ID_PRODUCT_ADDONS,
            'coliview_url'      => self::COLIVIEW_URL,
        );

        $this->context->smarty->assign(array('data' => $data));
    }

    /**
     * @return string
     * @throws Exception
     * @throws SmartyException
     */
    public function setColissimoControllerHeader()
    {
        $this->context->smarty->assign('link', $this->context->link);
        $this->setColissimoHeader();
        $header = $this->context->smarty->fetch($this->local_path.'views/templates/admin/_partials/header.back.tpl');
        $this->context->smarty->assign(
            'coliship_enabled',
            (int) !Configuration::get('COLISSIMO_GENERATE_LABEL_PRESTASHOP')
        );
        $quickAccess =
            $this->context->smarty->fetch($this->local_path.'views/templates/admin/_partials/quick-access.tpl');

        return $header.$quickAccess;
    }

    /**
     * @param Cart       $cart
     * @param float      $shippingCost
     * @param array|null $products
     * @return float|bool
     */
    public function getPackageShippingCost($cart, $shippingCost, $products)
    {
        $accountType = json_decode(Configuration::get('COLISSIMO_ACCOUNT_TYPE'), true);
        $deliveryAddr = new Address((int) $cart->id_address_delivery);
        if (Validate::isLoadedObject($deliveryAddr)) {
            $isoCountryCustomer = Country::getIsoById((int) $deliveryAddr->id_country);
            $destinationType = ColissimoTools::getDestinationTypeByIdCountry($deliveryAddr->id_country);
        } else {
            $idDefaultCountry = Configuration::get('PS_COUNTRY_DEFAULT');
            $isoCountryCustomer = Country::getIsoById((int) $idDefaultCountry);
            $destinationType = ColissimoTools::getDestinationTypeByIdCountry((int) $idDefaultCountry);
        }
        $carrier = new Carrier((int) $this->id_carrier);
        $idCarrierReference = $carrier->id_reference;
        if ( $_SERVER['REMOTE_ADDR'] == '176.134.93.165' || $_SERVER['REMOTE_ADDR'] == '80.15.118.113' )
            {
                error_log('idCarrierReference : '.$idCarrierReference);
            }
        $idService = ColissimoService::getServiceIdByIdCarrierDestinationType($idCarrierReference, $destinationType);
        $colissimoService = new ColissimoService((int) $idService);
        if ($idService) {
            if (!$colissimoService->isEligibleToAccount($isoCountryCustomer, $accountType)) {
                
            if ( $_SERVER['REMOTE_ADDR'] == '176.134.93.165' || $_SERVER['REMOTE_ADDR'] == '80.15.118.113' )
            {
                error_log('colissomo false 1 : '.$idService.' : '.$isoCountryCustomer . ' // '.print_r($accountType, true));
            }
                return false;
            }
            if ($this->isPassDelivery()) {
                return 0;
            }
            // Add customs fees for the ddp offer
            if (Configuration::get('COLISSIMO_ENABLE_DDP') && in_array($isoCountryCustomer, ColissimoTools::$isoDDP)) {
                if ($isoCountryCustomer == "GB") {
                    $shippingCost += Configuration::get("COLISSIMO_DDP_GB_COST");
                } else {
                    if ($isoCountryCustomer == "CH") {
                        $senderAddr = new ColissimoMerchantAddress('sender');
                        $isoFrom = $senderAddr->countryCode;
                        if (in_array($isoFrom, ColissimoTools::$isoFR) && isset($accountType['EUROPE'])) {
                            $serviceType = ColissimoService::getServiceTypeByIdCarrier((int) $idCarrierReference);
                            if ($serviceType == ColissimoService::TYPE_NOSIGN) {
                                return $shippingCost;
                            }
                        }
                    }
                    $shippingCost += Configuration::get("COLISSIMO_DDP_COST");
                }
            }
            // calculate the increase if price weighting is enabled 
            if (Configuration::get('COLISSIMO_WEIGHTING_PRICES')) {
                if (Configuration::get('COLISSIMO_WEIGHTING_TYPE') == 'percent') {
                    $percent = (float)Configuration::get('COLISSIMO_WEIGHTING_VALUE_PERCENT');
                    if ($percent) {
                        $percent = 1 + ($percent / 100);
                        $shippingCost *= $percent;
                    }
                } else {
                    $shippingCost += (float)Configuration::get('COLISSIMO_WEIGHTING_VALUE_AMOUNT');
                }
            }
            
            return $shippingCost;
        } else {
            if ( $_SERVER['REMOTE_ADDR'] == '176.134.93.165' || $_SERVER['REMOTE_ADDR'] == '80.15.118.113' )
                {
                    error_log('colissomo false');
                }
            return false;
        }
    }

    /**
     * @param Cart  $cart
     * @param float $shippingCost
     * @return bool|float
     */
    public function getOrderShippingCost($cart, $shippingCost)
    {
        return $this->getPackageShippingCost($cart, $shippingCost, null);
    }

    /**
     * @return bool
     */
    public function isPassDelivery()
    {
        if (Module::isEnabled('colissimopass')) {
            require_once(_PS_MODULE_DIR_.'colissimopass/classes/ColissimoPassUser.php');
            if (ColissimoPassUser::isActive()) {
                return true;
            }
            // is product pass in cart ?
            $cart = $this->context->cart;
            $products = $cart->getProducts();
            if (is_array($products)) {
                foreach ($products as $product) {
                    if ($product['id_product'] == (int) Configuration::get('ID_COLISSIMO_PASS_PDT')) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * @param array $params
     * @return bool
     */
    public function getOrderShippingCostExternal($params)
    {
        return false;
    }

    /**
     * @return ColissimoLogger
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * @param int $idOrder
     * @throws Exception
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function postProcessColissimoValidateService($idOrder)
    {
        $order = new Order($idOrder);
        $idColissimoOrder = ColissimoOrder::exists($idOrder);
        $colissimoOrder = new ColissimoOrder((int) $idColissimoOrder);
        $pickupPoint = ColissimoPickupPoint::getPickupPointByIdColissimo(Tools::getValue('id_colissimo_pickup_point'));
        $previousIdColissimoService = $colissimoOrder->id_colissimo_service;
        $previousService = ColissimoService::getServiceTypeById($previousIdColissimoService);
        $previousAddress = new Address((int) $order->id_address_delivery);
        $colissimoOrder->id_order = (int) $idOrder;
        $idServiceToAssociate = (int) Tools::getValue('colissimo_service_to_associate');
        if (!$idServiceToAssociate && !$pickupPoint->id) {
            throw new Exception($this->l('Please choose a pickup point or select another service.'));
        }
        if (!$idServiceToAssociate) {
            $destinationType = ColissimoTools::getDestinationTypeByIsoCountry($pickupPoint->iso_country);
            $idServiceToAssociate = ColissimoService::getServiceIdByProductCodeDestinationType(
                $pickupPoint->product_code,
                $destinationType
            );
        }
        $colissimoOrder->id_colissimo_service = (int) $idServiceToAssociate;
        $colissimoOrder->id_colissimo_pickup_point = (int) $pickupPoint->id;
        $colissimoOrder->migration = 0;
        $colissimoOrder->hidden = 0;
        $colissimoOrder->save();
        $newService = ColissimoService::getServiceTypeById($colissimoOrder->id_colissimo_service);

        if ($previousService === ColissimoService::TYPE_RELAIS && $newService !== ColissimoService::TYPE_RELAIS) {
            $order->id_address_delivery = $order->id_address_invoice;
            $order->update();
        } elseif ($newService === ColissimoService::TYPE_RELAIS && $colissimoOrder->id_colissimo_pickup_point) {
            $idNewAddress = ColissimoTools::createAddressFromPickupPoint(
                $pickupPoint,
                $order,
                $previousAddress->phone
            );
            $order->id_address_delivery = (int) $idNewAddress;
            $order->update();
        }
    }

    /**
     * @param string $typology
     * @param Order  $order
     * @param int    $labelId
     * @param string $statusText
     * @throws PrestaShopDatabaseException
     * @throws Exception
     */
    public function updateTrackingByTypology($typology, $order, $labelId, $statusText = '')
    {
        $idStatusShipped = Configuration::get('PS_OS_SHIPPING');
        $idStatusDelivered = Configuration::get('PS_OS_DELIVERED');
        if ($typology == ColissimoTrackingCode::TYPO_DELIVERED) {
            if (!$order->getHistory($this->context->language->id, $idStatusDelivered)) {
                $this->logger->info('Update order status to "Delivered"');
                $history = new OrderHistory();
                $history->id_order = (int) $order->id;
                $history->changeIdOrderState((int) $idStatusDelivered, (int) $order->id);
                $history->addWithemail();
            }
        } elseif ($typology == ColissimoTrackingCode::TYPO_SHIPPED) {
            if (!$order->getHistory($this->context->language->id, $idStatusShipped) &&
                !$order->getHistory($this->context->language->id, $idStatusDelivered)
            ) {
                $this->logger->info('Update order status to "Shipped"');
                $history = new OrderHistory();
                $history->id_order = (int) $order->id;
                $history->changeIdOrderState((int) $idStatusShipped, (int) $order->id);
                $history->addWithemail();
            }
        }
        if ($statusText) {
            Db::getInstance()
              ->insert(
                  'colissimo_shipment_tracking',
                  array(
                      'id_colissimo_label' => (int) $labelId,
                      'status_text'        => pSQL($statusText),
                      'typology'           => pSQL($typology),
                      'date_upd'           => date('Y-m-d H:i:s'),
                  ),
                  false,
                  true,
                  Db::REPLACE
              );
        }
    }
    
    /**
     * @return array
     * @throws Exception
     * @throws PrestaShopDatabaseException
     */
    public function getOrdersToTrack()
    {
        $dateAdd = new DateTime('now');
        $dateAdd->sub(new DateInterval('P30D'));
        $filteredStatuses = Configuration::getMultiple(array('PS_OS_DELIVERED', 'PS_OS_ERROR', 'PS_OS_CANCELED'));
        $dbQuery = new DbQuery();
        $dbQuery->select('cola.id_colissimo_label')
                ->from('colissimo_label', 'cola')
                ->leftJoin('colissimo_order', 'co', 'co.id_colissimo_order = cola.id_colissimo_order')
                ->leftJoin('orders', 'o', 'o.id_order = co.id_order')
                ->where('o.current_state NOT IN('.implode(',', array_map('intval', $filteredStatuses)).')')
                ->where('cola.date_add > "'.pSQL($dateAdd->format('Y-m-d H:i:s')).'"')
                ->where('cola.return_label = 0'.Shop::addSqlRestriction(false, 'o'));

        $labelIds = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($dbQuery);
        $ordersToUpdate = array_map(
            function ($element) {
                return $element['id_colissimo_label'];
            },
            $labelIds
        );
            
        return $ordersToUpdate;
    }

    /**
     * @param ColissimoLabel $colissimoLabel
     * @return string
     * @throws Exception
     * @throws PrestaShopDatabaseException
     */
    public function updateOrderTracking(ColissimoLabel $colissimoLabel)
    {
        $colissimoOrder = new ColissimoOrder((int) $colissimoLabel->id_colissimo_order);
        $order = new Order((int) $colissimoOrder->id_order);
        $request = new ColissimoTrackingSimpleRequest(ColissimoTools::getCredentials($order->id_shop));
        $request->setSkybillNumber($colissimoLabel->shipping_number);
        $this->logger->infoXml('Log XML request', $request->getRequest(true));
        $client = new ColissimoClient();
        $client->setRequest($request);
        try {
            /** @var ColissimoTrackingSimpleResponse $response */
            $response = $client->request();
        } catch (Exception $e) {
            $this->logger->error('Exception thrown: '.$e->getMessage());
            throw new Exception($this->l('Label #').$colissimoLabel->shipping_number.'<br />'.$e->getMessage());
        }
        if ($response->errorCode) {
            $this->logger->error(sprintf('Error found: (%s) %s', $response->errorCode, $response->errorMessage));
            throw new Exception($this->l('Label #').$colissimoLabel->shipping_number.'<br />'.$response->errorMessage);
        }
        $inovertCode = $response->eventCode;
        $typology = ColissimoTrackingCode::getTypologyByInovertCode($inovertCode);
        if (!$typology && $inovertCode != ColissimoTrackingCode::EVENT_WAITING_SHIPMENT_HANDLING) {
            $typology = ColissimoTrackingCode::TYPO_SHIPPED;
        }
        $this->logger->info('Update tracking.');
        $this->updateTrackingByTypology(
            $typology,
            new Order((int) $colissimoOrder->id_order),
            $colissimoLabel->id,
            $response->eventLibelle
        );

        return sprintf($this->l('Label #%s updated'), $colissimoLabel->shipping_number);
    }

    /**
     * @return bool|string
     * @throws Exception
     */
    public function getWidgetToken()
    {
        $this->logger->setChannel('FrontWidgetToken');
        $credentials = array_merge(
            ColissimoTools::getCredentials(),
            array('force_endpoint' => Configuration::get('COLISSIMO_WIDGET_ENDPOINT'))
        );
        $tokenRequest = new ColissimoWidgetAuthenticationRequest($credentials);
        $client = new ColissimoClient();
        $client->setRequest($tokenRequest);
        try {
            /** @var ColissimoWidgetAuthenticationResponse $response */
            $response = $client->request();
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());

            return false;
        }

        return $response->token;
    }

    /**
     * @param Order $order
     * @return array
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function getEligibleServiceByOrder($order)
    {
        $accountType = json_decode(Configuration::get('COLISSIMO_ACCOUNT_TYPE'), true);
        $deliveryAddr = new Address((int) $order->id_address_delivery);
        if (Validate::isLoadedObject($deliveryAddr)) {
            $isoCountryCustomer = Country::getIsoById((int) $deliveryAddr->id_country);
            $destinationType = ColissimoTools::getDestinationTypeByIdCountry($deliveryAddr->id_country);
        } else {
            $idDefaultCountry = Configuration::get('PS_COUNTRY_DEFAULT');
            $isoCountryCustomer = Country::getIsoById((int) $idDefaultCountry);
            $destinationType = ColissimoTools::getDestinationTypeByIdCountry((int) $idDefaultCountry);
        }
        $eligibleServices = array();
        $serviceIds = ColissimoService::getServiceIdsByDestinationType($destinationType);
        foreach ($serviceIds as $serviceId) {
            $colissimoService = new ColissimoService((int) $serviceId);
            if ($colissimoService->isEligibleToAccount($isoCountryCustomer, $accountType)) {
                if (!$colissimoService->is_pickup) {
                    $eligibleServices[(int) $serviceId] = $colissimoService->commercial_name;
                } else {
                    $eligibleServices[0] = $this->l('Pickup point');
                }
            }
        }
        krsort($eligibleServices);

        return $eligibleServices;
    }

    /**
     * @param int    $idColissimoOrder
     * @param string $channel
     * @throws Exception
     */
    public function assignColissimoOrderVariables($idColissimoOrder, $channel = '')
    {
        $colissimoOrder = new ColissimoOrder((int) $idColissimoOrder);
        $order = new Order((int) $colissimoOrder->id_order);
        $orderDetails = $order->getOrderDetailList();
        $weightUnit = Configuration::get('PS_WEIGHT_UNIT');
        $weight = $order->getTotalWeight();
        if ((int) Configuration::get('COLISSIMO_USE_WEIGHT_TARE') == 1) {
            $weight = $weight + Configuration::get('COLISSIMO_DEFAULT_WEIGHT_TARE');
        }
        $orderTotals = array(
            'amount'      => $order->total_paid_tax_incl,
            'shipping'    => $order->total_shipping_tax_incl,
            'weight'      => $weight,
            'id_currency' => $order->id_currency,
            'weight_unit' => $weightUnit,
        );
        $colissimoPickupPoint = new ColissimoPickupPoint((int) $colissimoOrder->id_colissimo_pickup_point);
        if (Validate::isLoadedObject($colissimoPickupPoint)) {
            $pickupPointId = $colissimoPickupPoint->colissimo_id;
        } else {
            $pickupPointId = false;
        }
        $shipments = $colissimoOrder->getShipments($this->context->language->id);
        $colissimoService = new ColissimoService((int) $colissimoOrder->id_colissimo_service);
        $id_address =  (int) ColissimoAddress::getAddressByCartId((int) $order->id_cart);
        $colissimoAddress = new ColissimoAddress($id_address);
        if ($colissimoOrder->ddp == 1) {
            $this->context->smarty->assign(array('delivery_duty_paid' => (float) $colissimoOrder->ddp_cost));
        }
        $this->context->smarty->assign(
            array(
                'id_colissimo_order' => $idColissimoOrder,
                'id_order'           => $order->id,
                'delivery_addr'      => new Address((int) $order->id_address_delivery),
                'customer'           => new Customer((int) $order->id_customer),
                'order_details'      => $orderDetails,
                'order_totals'       => $orderTotals,
                'shipments'          => $shipments,
                'show_visibility_btn' => !$shipments && $colissimoOrder->hidden == 1,
                'colissimo_channel'  => $channel,
                'colissimo_service'  => $colissimoService->commercial_name,
                'coliship_enabled'   => !Configuration::get('COLISSIMO_GENERATE_LABEL_PRESTASHOP'),
                'pickup_point_id'    => $pickupPointId,
                'use_weight_tare'    => (int) Configuration::get('COLISSIMO_USE_WEIGHT_TARE'),
                'colissimo_address' => $colissimoAddress,
            )
        );
    }

    /**
     * @param int $idOrder
     * @return string|false
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     * @throws SmartyException
     */
    public function getColissimoOrderToAssignHtml($idOrder)
    {
        $order = new Order((int) $idOrder);
        if (!Validate::isLoadedObject($order)) {
            return false;
        }
        $eligibleServices = $this->getEligibleServiceByOrder($order);
        if (empty($eligibleServices)) {
            return false;
        }
        $this->context->smarty->assign(array('id_order' => (int) $idOrder));

        return $this->context->smarty->fetch(sprintf(
            'extends:%s|%s',
            $this->local_path.'views/templates/admin/admin_order/'.$this->boTheme.'/layout-block.tpl',
            $this->local_path.'views/templates/admin/admin_order/'.$this->boTheme.'/order-association.tpl'
        ));
    }

    /**
     * @param string $id
     * @param string $url
     * @param array  $params
     */
    public function registerExternalJs($id, $url, $params = array())
    {
        $this->context->controller->registerJavascript($id, $url, $params);
    }

    /**
     * @param string $id
     * @param string $filename
     * @param array  $params
     */
    public function registerJs($id, $filename, $params = array())
    {
        $this->context->controller->registerJavascript($id, 'modules/'.$this->name.'/views/js/'.$filename, $params);
    }

    /**
     * @param string $id
     * @param string $filename
     * @param array  $params
     */
    public function registerCSS($id, $filename, $params = array())
    {
        $this->context->controller->registerStylesheet(
            $id,
            'modules/'.$this->name.'/views/css/'.$filename,
            $params
        );
    }
    
    /**
     * @param array $params
     */
    public function hookDisplayShoppingCart($params)
    {
        $this->displayDeliveryDutyPaid($params['cart']);
    }

    /**
     * @param array $params
     */
    public function hookDisplayCheckoutSummaryTop($params)
    {
       $this->displayDeliveryDutyPaid($params['cart']);
    }
    
    /**
     * @param array $params
     */
    public function hookDisplayCartModalContent($params)
    {
       $this->displayDeliveryDutyPaid($params['cart']);
    }

    /**
     * @param array $cart
     */
    public function displayDeliveryDutyPaid($cart)
    {
        $carrier = new Carrier((int) $cart->id_carrier);
        if (Configuration::get('COLISSIMO_ENABLE_DDP') && $carrier->is_free == 0) {
            $deliveryAddr = new Address((int) $cart->id_address_delivery);
            $destinationType = ColissimoTools::getDestinationTypeByIdCountry($deliveryAddr->id_country);
            $accountType = json_decode(Configuration::get('COLISSIMO_ACCOUNT_TYPE'), true);
            $senderAddr = new ColissimoMerchantAddress('sender');
            $isoFrom = $senderAddr->countryCode;
            $idCarrierReference = $carrier->id_reference;
            $idService = ColissimoService::getServiceIdByIdCarrierDestinationType($idCarrierReference, $destinationType);
            if ($idService) {
                if (Validate::isLoadedObject($deliveryAddr)) {
                    $currency = new Currency((int) $cart->id_currency);
                    $isoCountryCustomer = Country::getIsoById((int) $deliveryAddr->id_country);
                    $colissimoDDP = 0;
                    if (in_array($isoCountryCustomer, ColissimoTools::$isoDDP)) {
                        $colissimoDDP = Configuration::get("COLISSIMO_DDP_COST");
                        if ($isoCountryCustomer == "GB") {
                            $colissimoDDP = Configuration::get("COLISSIMO_DDP_GB_COST");
                        }
                        $this->context->smarty->assign(
                            array(
                                'colissimo_customs_fees' => $colissimoDDP,
                                'currency' => $currency->iso_code,
                            )
                        );
                        // Do not display the ddp message if the service is TYPE_NOSIGN for Switzerland
                        if ($isoCountryCustomer == "CH") {
                            if (in_array($isoFrom, ColissimoTools::$isoFR) && isset($accountType['EUROPE'])) {
                                $serviceType = ColissimoService::getServiceTypeByIdCarrier((int) $idCarrierReference);
                                $this->context->smarty->assign(
                                    array(
                                        'check_ddp' => 1,
                                        'serviceType' => $serviceType,
                                    )
                                );
                            }
                        }

                        return $this->context->smarty->display(
                            $this->local_path . 'views/templates/hook/front/displayDeliveryDutyPaid.tpl'
                        );
                    }
                }
            }
            
            return '';
        }
    }

    /**
     * @param array $params
     * @return bool
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function hookNewOrder($params)
    {
        $this->logger->setChannel('HookNewOrder');
        $this->logger->info('Hook newOrder called');
        $order = new Order((int) $params['order']->id);
        if (Validate::isLoadedObject($order) && $order->current_state != Configuration::get('PS_OS_ERROR')) {
            $carrier = new Carrier((int) $order->id_carrier);
            $idCarrierReference = $carrier->id_reference;
            $this->logger->info(
                sprintf('Order #%d - Carrier details', $order->id),
                array('id_carrier' => $order->id_carrier, 'id_reference' => $carrier->id_reference)
            );
            $this->logger->info(
                sprintf('Order #%d - Order Carrier details', $order->id),
                ColissimoOrderCarrier::getAllByIdOrder($order->id)
            );
            if ($carrier->external_module_name != $this->name) {
                $this->logger->info(
                    sprintf('Order #%d - Not a Colissimo Carrier.', $order->id),
                    array('id' => (int) $idCarrierReference)
                );

                return true;
            }
            $deliveryAddr = new Address((int) $order->id_address_delivery);
            $destinationType = ColissimoTools::getDestinationTypeByIdCountry((int) $deliveryAddr->id_country);
            $serviceType = ColissimoService::getServiceTypeByIdCarrier((int) $idCarrierReference);
            $colissimoOrder = new ColissimoOrder();
            $colissimoOrder->id_order = (int) $order->id;
            $isoCountryCustomer = Country::getIsoById((int) $deliveryAddr->id_country);
            $accountType = json_decode(Configuration::get('COLISSIMO_ACCOUNT_TYPE'), true);
            $senderAddr = new ColissimoMerchantAddress('sender');
            $isoFrom = $senderAddr->countryCode;
            if ($serviceType == ColissimoService::TYPE_RELAIS) {
                $this->logger->info(sprintf('Order #%d - Carrier type : RELAIS', $order->id));
                $idColissimoPickupPoint = ColissimoCartPickupPoint::getByCartId($order->id_cart);
                $colissimoPickupPoint = new ColissimoPickupPoint((int) $idColissimoPickupPoint);
                $productCode = $colissimoPickupPoint->product_code;
                $idColissimoService = ColissimoService::getServiceIdByProductCodeDestinationType(
                    $productCode,
                    $destinationType
                );
                $colissimoOrder->id_colissimo_pickup_point = (int) $idColissimoPickupPoint;
            } else {
                $this->logger->info(sprintf('Order #%d - Carrier type : DOMICILE', $order->id));
                $idColissimoService = ColissimoService::getServiceIdByIdCarrierDestinationType(
                    (int) $idCarrierReference,
                    $destinationType
                );
                // Assign product code COLI to Switzerland if DDP
                if (Configuration::get('COLISSIMO_ENABLE_DDP') && $isoCountryCustomer == "CH" && $carrier->is_free == 0) {
                    if (in_array($isoFrom, ColissimoTools::$isoFR) && isset($accountType['EUROPE'])) {
                        if ($serviceType == ColissimoService::TYPE_SIGN) {
                            $idColissimoService = ColissimoService::getServiceIdByIdCarrierDestinationType(
                                (int) $idCarrierReference,
                                'WORLDWIDE'
                            );
                        }
                    }
                }
                $colissimoOrder->id_colissimo_pickup_point = 0;
            }
            $colissimoOrder->id_colissimo_service = (int) $idColissimoService;
            $colissimoOrder->migration = 0;
            // Save DDP fields
            if (Configuration::get('COLISSIMO_ENABLE_DDP') && $carrier->is_free == 0) {
                if (in_array($isoCountryCustomer, ColissimoTools::$isoDDP)) {
                    $colissimoOrder->ddp = 1;
                    $colissimoOrder->ddp_cost = Configuration::get("COLISSIMO_DDP_COST");
                    if ($isoCountryCustomer == "GB") {
                        $colissimoOrder->ddp_cost = Configuration::get("COLISSIMO_DDP_GB_COST");
                    }
                    // No ddp if the service is TYPE_NOSIGN for Switzerland
                    if ($isoCountryCustomer == "CH") {
                        if (in_array($isoFrom, ColissimoTools::$isoFR) && isset($accountType['EUROPE'])) {
                            if ($serviceType == ColissimoService::TYPE_NOSIGN) {
                                $colissimoOrder->ddp = 0;
                                $colissimoOrder->ddp_cost = 0;
                            }
                        }
                    }
                }
            }
            $colissimoOrder->hidden = 0;
            try {
                $colissimoOrder->save();
            } catch (Exception $e) {
                $this->logger->error(sprintf('Order #%d - Cannot save order. '.$e->getMessage(), $order->id));

                return true;
            }
            if (Module::isEnabled('colissimopass')) {
                require_once(_PS_MODULE_DIR_.'colissimopass/colissimopass.php');
                if (ColissimoPassUser::isActive()) {
                    $this->logger->info(sprintf('Order #%d - ColissimoPass order', $order->id));
                    Colissimopass::sendConsignment($order);
                }
            }
            $this->logger->info(
                sprintf('Order #%d - Colissimo Order created', $order->id),
                array('obj' => $colissimoOrder)
            );
        } else {
            $this->logger->error('Not a valid order.');
        }

        return true;
    }

    /**
     * @param array $params
     * @throws SmartyException
     */
    public function hookDisplayAdminColissimoAffranchissementListAfter($params)
    {
        return $this->context->smarty->display(
            $this->local_path.'views/templates/hook/admin/displayAdminColissimoAffranchissementListAfter.tpl'
        );
    }

    /**
     * @param array $params
     * @throws SmartyException
     */
    public function hookDisplayAdminColissimoDashboardListAfter($params)
    {
        return $this->context->smarty->display(
            $this->local_path.'views/templates/hook/admin/displayAdminColissimoDashboardListAfter.tpl'
        );
    }

    /**
     * @param array $params
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function hookActionObjectOrderAddBefore($params)
    {
        /** @var Order $order */
        $order = $params['object'];
        $this->logger->setChannel('HookOrderAddBefore');
        $idCarrier = $order->id_carrier;
        $carrier = new Carrier((int) $idCarrier);
        $deliveryAddressOrigin = new Address((int) $order->id_address_delivery);
        $serviceType = ColissimoService::getServiceTypeByIdCarrier($carrier->id_reference);
        if ($serviceType == ColissimoService::TYPE_RELAIS) {
            $idColissimoPickupPoint = ColissimoCartPickupPoint::getByCartId((int) $order->id_cart);
            $pickupPoint = new ColissimoPickupPoint((int) $idColissimoPickupPoint);
            if (Validate::isLoadedObject($pickupPoint)) {
                try {
                    $idNewAdress = ColissimoTools::createAddressFromPickupPoint(
                        $pickupPoint,
                        $order,
                        $deliveryAddressOrigin->phone
                    );
                } catch (Exception $e) {
                    $this->logger->error($e->getMessage());

                    return;
                }

                $order->id_address_delivery = (int) $idNewAdress;
                Db::getInstance()->update(
                    'customization',
                    array('id_address_delivery' => (int) $idNewAdress),
                    'id_cart = '.(int) $order->id_cart
                );
            } else {
                $this->logger->error('ColissimoPickupPoint object not valid.');
            }
        }
    }

    /**
     * @param array $params
     * @return bool
     */
    public function hookActionObjectColissimoDepositSlipDeleteAfter($params)
    {
        $object = $params['object'];

        return Db::getInstance()
                 ->update(
                     'colissimo_label',
                     array('id_colissimo_deposit_slip' => 0),
                     'id_colissimo_deposit_slip = '.(int) $object->id
                 );
    }

    /**
     * @param array $params
     * @return bool|string
     * @throws Exception
     */
    public function hookDisplayCarrierExtraContent($params)
    {
        $carrier = $params['carrier'];
        $serviceType = ColissimoService::getServiceTypeByIdCarrier((int) $carrier['id_reference']);
        if ($serviceType) {
            /** @var Cart $cart */
            $cart = $params['cart'];
            $deliveryAddr = new Address((int) $cart->id_address_delivery);
            if ($serviceType == ColissimoService::TYPE_RELAIS) {
                $token = $this->getWidgetToken();
                if ($token !== false && $token !== null) {
                    $selectedPickupPoint = ColissimoCartPickupPoint::getByCartId($this->context->cart->id);
                    $pickupPoint = new ColissimoPickupPoint((int) $selectedPickupPoint);
                    if (
                        $pickupPoint->iso_country == 'FR' &&
                        in_array(Country::getIsoById($deliveryAddr->id_country), array('FR', 'MC'))
                    ) {
                        // About the widget, France and Monaco can be considered the same country
                        // For Colissimo, Monaco pickup points are located in France
                    } else {
                        if ($pickupPoint->iso_country != Country::getIsoById($deliveryAddr->id_country)) {
                            $pickupPoint = new ColissimoPickupPoint();
                            Db::getInstance()
                                ->delete('colissimo_cart_pickup_point', 'id_cart = ' . (int) $cart->id);
                        }
                    }
                    $font = ColissimoModuleConfiguration::$widgetFonts[(int) Configuration::get('COLISSIMO_WIDGET_FONT')];
                    $widgetIsoList = ColissimoPickupPoint::$availableLanguages;
                    $customerIso = $this->context->language->iso_code;
                    $widgetLang = in_array($customerIso, $widgetIsoList) ? $customerIso : 'en';
                    $needMobileValidation = Configuration::get('PS_ORDER_PROCESS_TYPE');
                    $mobilePhone = ColissimoCartPickupPoint::getMobilePhoneByCartId((int) $cart->id);
                    if (!$mobilePhone) {
                        $mobilePhone = $deliveryAddr->phone;
                    }
                    $this->context->smarty->assign(
                        array(
                            'link'                   => $this->context->link,
                            'colissimo_widget_token' => $token,
                            'colissimo_pickup_point' => $pickupPoint,
                            'delivery_addr'          => array(
                                'address'     => $deliveryAddr->address1,
                                'zipcode'     => $deliveryAddr->postcode,
                                'city'        => $deliveryAddr->city,
                                'iso_country' => Country::getIsoById($deliveryAddr->id_country),
                            ),
                            'mobile_phone'           => $mobilePhone,
                            'widget_police'          => $font,
                            'widget_color_1'         => Configuration::get('COLISSIMO_WIDGET_COLOR_1'),
                            'widget_color_2'         => Configuration::get('COLISSIMO_WIDGET_COLOR_2'),
                            'preparation_time'       => Configuration::get('COLISSIMO_ORDER_PREPARATION_TIME'),
                            'colissimo_widget_lang'  => $widgetLang,
                            'need_mobile_validation' => (int) $needMobileValidation,
                            'colissimo_img_path'     => $this->getPathUri() . 'views/img/',
                            'colissimo_service'      => 'RELAIS',
                        )
                    );
                }
            } else {
                /** @var Cart $cart */
                $cart = $params['cart'];
                $displayDoorCodes = Configuration::get("COLISSIMO_ENABLE_DOOR_CODES");
                $id_address =  (int) ColissimoAddress::getAddressByCartId((int) $cart->id);
                $colissimoAddress = new ColissimoAddress((int) $id_address);
                $this->context->smarty->assign(
                    array(
                        'colissimo_service' => 'OTHER',
                        'reference' => (int) $carrier['id_reference'],
                        'display_door_codes' => $displayDoorCodes,
                        'colissimo_address' => $colissimoAddress,
                    )
                );
                $carrier = new Carrier((int) $cart->id_carrier);
                if (Configuration::get("COLISSIMO_ENABLE_DDP") && $carrier->is_free == 0) {
                    $currency = new Currency((int) $cart->id_currency);
                    $isoCountryCustomer = Country::getIsoById((int) $deliveryAddr->id_country);
                    $colissimoDDP = 0;
                    if (in_array($isoCountryCustomer, ColissimoTools::$isoDDP)) {
                        $colissimoDDP = Configuration::get("COLISSIMO_DDP_COST");
                        if ($isoCountryCustomer == "GB") {
                            $colissimoDDP = Configuration::get("COLISSIMO_DDP_GB_COST");
                        }
                        $this->context->smarty->assign(
                            array(
                                'colissimo_customs_fees' => $colissimoDDP,
                                'currency' => $currency->iso_code,
                            )
                        );
                         // Do not display the ddp message if the service is TYPE_NOSIGN for Switzerland
                        if ($isoCountryCustomer == "CH") {
                            $accountType = json_decode(Configuration::get('COLISSIMO_ACCOUNT_TYPE'), true);
                            $senderAddr = new ColissimoMerchantAddress('sender');
                            $isoFrom = $senderAddr->countryCode;
                            if (in_array($isoFrom, ColissimoTools::$isoFR) && isset($accountType['EUROPE'])) {
                                $this->context->smarty->assign(
                                    array(
                                        'check_ddp' => 1,
                                        'serviceType' => $serviceType,
                                    )
                                );
                            }
                        }
                    }
                }
            }

            return $this->display(
                __FILE__,
                'views/templates/hook/front/displayCarrierExtraContent.tpl'
            );
        }

        return false;
    }

    /**
     * @param array $params
     * @return bool|string
     * @throws Exception
     */
    public function hookExtraCarrier($params)
    {
        /** @var Cart $cart */
        $cart = $params['cart'];
        $carrier = new Carrier((int) $cart->id_carrier);
        $params['carrier'] = array(
            'id_reference' => $carrier->id_reference,
        );

        return $this->hookDisplayCarrierExtraContent($params);
    }

    /**
     * @param array $params
     */
    public function hookActionValidateStepComplete($params)
    {
        if ($params['step_name'] != 'delivery') {
            return;
        }
        /** @var Cart $cart */
        /*echo '<pre>';
        print_r($params);
        echo '</pre>';
        die;*/
        $cart = $params['cart'];

        $isRelais = false;
        if ( isset($params['request_params']['my_delivery_option']) )
        {
            foreach ($params['request_params']['my_delivery_option'] as $id_carrier_tmp)
            {
                $carrier = new Carrier((int) str_replace(',', '', $id_carrier_tmp));
                $serviceType = ColissimoService::getServiceTypeByIdCarrier($carrier->id_reference);
                if ($serviceType == ColissimoService::TYPE_RELAIS)
                {
                    $isRelais = true;
                }
            }
        }
        else 
        {
            $carrier = new Carrier((int) $cart->id_carrier);
            $serviceType = ColissimoService::getServiceTypeByIdCarrier($carrier->id_reference);
            if ($serviceType == ColissimoService::TYPE_RELAIS)
            {
                $isRelais = true;
            }
        }

        if ($isRelais == true){
            if (!isset($params['request_params']['id_colissimo_pickup_point']) ||
                !$params['request_params']['id_colissimo_pickup_point']
            ) {
                $params['completed'] = false;
                $this->context->controller->errors[] =
                    $this->l('Please select a pickup-point or choose another shipping option.');

                return;
            }
            $phoneNumberIntl = isset($params['request_params']['colissimo_pickup_mobile_phone']['full']) ?
                $params['request_params']['colissimo_pickup_mobile_phone']['full'] :
                false;
            if ( empty($phoneNumberIntl) )
            {
                $phoneNumberIntl = isset($params['request_params']['colissimo_pickup_mobile_phone']['main']) ?
                $params['request_params']['colissimo_pickup_mobile_phone']['main'] :
                false;
            }
            if (!Configuration::get('COLISSIMO_WIDGET_PHONE') && !$phoneNumberIntl) {
                $params['completed'] = false;
                $this->context->controller->errors[] = $this->l('Please fill in your mobile phone number.');

                return;
            }
            $isPhoneNumberValid = isset($params['request_params']['colissimo_is_mobile_valid']) ?
                $params['request_params']['colissimo_is_mobile_valid'] :
                0;
            if (!Configuration::get('COLISSIMO_WIDGET_PHONE') && !$isPhoneNumberValid) {
                $params['completed'] = false;
                $this->context->controller->errors[] = $this->l('Please fill a valid mobile phone number.');

                return;
            }
            try {
                ColissimoCartPickupPoint::updateMobilePhoneByCartId($cart->id, $phoneNumberIntl);
            } catch (Exception $e) {
                $this->logger->error($e->getMessage());
                $params['completed'] = false;
                $this->context->controller->errors[] = $this->l('Cannot save mobile phone number.');

                return;
            }
        } elseif (Tools::getValue('colissimo_code_porte1_'.$carrier->id_reference) || Tools::getValue('colissimo_code_porte2_'.$carrier->id_reference)){
            $id_address =  (int) ColissimoAddress::getAddressByCartId((int) $cart->id);
            $colissimoAddress = new ColissimoAddress($id_address);
            $colissimoAddress->id_cart = (int) $cart->id;
            $colissimoAddress->code_porte1 = pSQL(Tools::getValue('colissimo_code_porte1_'.$carrier->id_reference));
            $colissimoAddress->code_porte2 = pSQL(Tools::getValue('colissimo_code_porte2_'.$carrier->id_reference));
            $colissimoAddress->save();
                
            return;
        }
    }

    public function displayAdminOrder($params)
    {
        $this->setColissimoHeader();

        $idOrder = $params['id_order'];
        if (Tools::isSubmit('submitColissimoValidateService')) {
            try {
                $this->postProcessColissimoValidateService($idOrder);
            } catch (Exception $e) {
                $this->logger->error($e->getMessage());
                $this->context->controller->errors[] = $e->getMessage();
            }
        }
        $idColissimoOrder = ColissimoOrder::exists($idOrder);
        if (!$idColissimoOrder) {
            return $this->getColissimoOrderToAssignHtml($idOrder);
        }
        $this->assignColissimoOrderVariables((int) $idColissimoOrder, 'HookAdminOrder');

        return $this->context->smarty->fetch(
            sprintf(
                'extends:%s|%s',
                $this->local_path.'views/templates/admin/admin_order/'.$this->boTheme.'/layout-block.tpl',
                $this->local_path.'views/templates/admin/admin_order/'.$this->boTheme.'/order-detail.tpl'
            )
        );
    }

    /**
     * @param $params
     * @return string
     * @throws Exception
     */
    public function hookDisplayAdminOrderMainBottom($params)
    {
        return $this->displayAdminOrder($params);
    }

    /**
     * @param array $params
     * @return string
     * @throws Exception
     */
    public function hookAdminOrder($params)
    {
        return Tools::version_compare(_PS_VERSION_, '1.7.7.0', '>=') ? false : $this->displayAdminOrder($params);
    }
    
    /**
     * @param array $params
     */
    public function hookActionAdminOrdersListingFieldsModifier($params)
    {
        if (Configuration::get('COLISSIMO_DISPLAY_TRACKING_NUMBER')) {
            if (isset($params['fields'])) {
               $params['fields']['tracking_number'] = array(
                  'title' => 'Tracking number',
                  'align' => 'text-center',
                  'orderby' => false,
                  'type' => 'text',
                );
            }
            if (isset($params['join'])) {
                $params['join'] .= '
                    LEFT JOIN `'._DB_PREFIX_.'order_carrier` oc ON (a.`id_order` = oc.`id_order`)';
            }
        }
    }
    
    /**
     * @param array $params
     */
    public function hookActionOrderGridDefinitionModifier(array $params)
    {
        if (Configuration::get('COLISSIMO_DISPLAY_TRACKING_NUMBER')) {
            /** @var GridDefinitionInterface $definition */
            $definition = $params['definition'];

            /** @var ColumnCollection */
            $columns = $definition->getColumns();
            $columnTracking = new DataColumn('tracking_number');
            $columnTracking->setName($this->l('Tracking number'));
            $columnTracking->setOptions([
                    'field' => 'tracking_number',
            ]);
            $columns->addAfter('date_add', $columnTracking);

            /** @var FilterCollectionInterface $filters */
            $filters = $definition->getFilters();
            $filterTracking = new Filter('tracking_number', TextType::class);
            $filterTracking->setAssociatedColumn('tracking_number');
            $filterTracking->setTypeOptions(['required' => false]);
            $filters->add($filterTracking);
        }
    }

    /**
     * @param array $params
     */
    public function hookActionOrderGridQueryBuilderModifier(array $params)
    {
        if (Configuration::get('COLISSIMO_DISPLAY_TRACKING_NUMBER')) {
            /** @var QueryBuilder $searchQueryBuilder */
            $searchQueryBuilder = $params['search_query_builder'];
            $searchQueryBuilder->addSelect('(oc.tracking_number)')
                           ->leftJoin('o', _DB_PREFIX_.'order_carrier', 'oc', 'oc.id_order = o.id_order');
            /** @var SearchCriteriaInterface $searchCriteria */
            $searchCriteria = $params['search_criteria'];

            $strictComparisonFilters = [
                'tracking_number' => '(oc.tracking_number)',
            ];
            $filters = $searchCriteria->getFilters();
            foreach ($filters as $filterName => $filterValue) {
                if (isset($strictComparisonFilters[$filterName])) {
                    $alias = $strictComparisonFilters[$filterName];
                    $searchQueryBuilder->andWhere("$alias = :$filterName");
                    $searchQueryBuilder->setParameter($filterName, $filterValue);
                    continue;
                }
            }
        }
    }

    /**
     * @return array
     */
    public function hookModuleRoutes()
    {
        return array(
            'module-colissimo-tracking' => array(
                'controller' => 'tracking',
                'rule'       => 'suivicolissimo/{order_reference}/{hash}',
                'keywords'   => array(
                    'order_reference' => array('regexp' => '[A-Z0-9]{8,9}', 'param' => 'order_reference'),
                    'hash'            => array('regexp' => '[a-z0-9]{32}', 'param' => 'hash'),
                ),
                'params'     => array(
                    'fc'     => 'module',
                    'module' => $this->name,
                ),
            ),
            'module-colissimo-return'   => array(
                'controller' => 'return',
                'rule'       => 'colissimo-retour',
                'keywords'   => array(),
                'params'     => array(
                    'fc'     => 'module',
                    'module' => $this->name,
                ),
            ),
            'module-colissimo-cron-tracking' => array(
                'controller' => 'crontracking',
                'rule'       => 'crontracking/{hash}',
                'keywords'   => array(
                    'hash'   => array('regexp' => '[a-z0-9]{32}', 'param' => 'hash'),
                ),
                'params'     => array(
                    'fc'     => 'module',
                    'module' => $this->name,
                ),
            ),
        );
    }

    /**
     * @param array $params
     * @return string
     * @throws Exception
     */
    public function hookCustomerAccount($params)
    {
        if (!Configuration::get('COLISSIMO_ENABLE_RETURN') ||
            !Configuration::get('COLISSIMO_DISPLAY_RETURN_LABEL_CUSTOMER')
        ) {
            return '';
        }

        return $this->display(__FILE__, 'views/templates/hook/front/displayCustomerAccount.tpl');
    }

    /**
     * @param array $params
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function hookActionAdminOrdersTrackingNumberUpdate($params)
    {
        /** @var Order $order */
        $order = $params['order'];
        /** @var Carrier $carrier */
        $carrier = $params['carrier'];
        $this->logger->setChannel('ManualTrackingUpdate');
        $this->logger->info('Hook called.');
        if (($idColissimoOrder = ColissimoOrder::exists($order->id)) != 0) {
            $orderCarrier = ColissimoOrderCarrier::getByIdOrder($order->id);
            if (Validate::isLoadedObject($orderCarrier) && $orderCarrier->tracking_number) {
                $idCarrierReference = $carrier->id_reference;
                if (!ColissimoService::getServiceTypeByIdCarrier($idCarrierReference)) {
                    $this->logger->info('Not a Colissimo carrier.');

                    return;
                }
                $colissimoOrder = new ColissimoOrder($idColissimoOrder);
                $labelIds = $colissimoOrder->getLabelIds();
                $sendPNAMail = 0;
                if (!count($labelIds)) {
                    $this->logger->info('Label to be created.');
                    $colissimoLabel = new ColissimoLabel();
                    $colissimoLabel->id_colissimo_order = (int) $colissimoOrder->id;
                    $colissimoLabel->id_colissimo_deposit_slip = 0;
                    $colissimoLabel->label_format = 'pdf';
                    $colissimoLabel->return_label = 0;
                    $colissimoLabel->cn23 = 0;
                    $colissimoLabel->coliship = 1;
                    $colissimoLabel->migration = 0;
                    $colissimoLabel->insurance = null;
                    $colissimoLabel->file_deleted = 0;
                    $sendPNAMail = 1;
                } elseif (count($labelIds) == 1) {
                    $this->logger->info('Label to be updated.');
                    $colissimoLabel = new ColissimoLabel((int) $labelIds[0]);
                } else {
                    return;
                }
                $colissimoLabel->shipping_number = pSQL($orderCarrier->tracking_number);
                try {
                    $colissimoLabel->save(true);
                    $this->logger->info(
                        'Label created/updated.',
                        array(
                            'id_label'        => $colissimoLabel->id,
                            'shipping_number' => $orderCarrier->tracking_number,
                        )
                    );
                } catch (Exception $e) {
                    $this->logger->error($e->getMessage());

                    return;
                }
                if ($sendPNAMail) {
                    $this->logger->info('Sending PNA mail.');
                    $isoLangOrder = Language::getIsoById($order->id_lang);
                    if (isset($this->module->PNAMailObject[$isoLangOrder])) {
                        $object = $this->PNAMailObject[$isoLangOrder];
                    } else {
                        $object = $this->PNAMailObject['en'];
                    }
                    $hash = md5($order->reference.$order->secure_key);
                    $link = $this->context->link->getModuleLink(
                        'colissimo',
                        'tracking',
                        array('order_reference' => $order->reference, 'hash' => $hash)
                    );
                    ColissimoTools::sendHandlingShipmentMail(
                        $order,
                        sprintf($object, $order->reference),
                        $link
                    );
                }
            }
        }
    }

    /**
     * @param array $params
     * @return string|bool
     * @throws Exception
     */
    public function hookDisplayOrderDetail($params)
    {
        $order = $params['order'];
        $colissimoOrderExists = ColissimoOrder::exists($order->id);
        if (!$colissimoOrderExists) {
            return false;
        }
        $shipments = ColissimoOrder::getShipmentsByColissimoOrderId($colissimoOrderExists, $order->id_lang);
        if (empty($shipments)) {
            return false;
        }
        $hash = md5($order->reference.$order->secure_key);
        $link = $this->context->link->getModuleLink(
            'colissimo',
            'tracking',
            array('order_reference' => $order->reference, 'hash' => $hash),
            null,
            $order->id_lang,
            $order->id_shop
        );

        $data = array(
            'img_path' => $this->getPathUri().'views/img/',
            'pna_url'  => $link,
        );
        $this->context->smarty->assign('data', $data);

        return $this->display(__FILE__, 'views/templates/hook/front/displayOrderDetail.tpl');
    }

    /**
     * @param array $params
     * @return string
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function hookDisplayAdminProductsExtra($params)
    {
        $idProduct = (int) $params['id_product'];
        $countries = Country::getCountries((int) Context::getContext()->cookie->id_lang);
        $productCustomDetails = ColissimoCustomProduct::getByIdProduct((int) $idProduct);
        $this->context->smarty->assign(
            array(
                'countries'       => $countries,
                'ps_version'      => _PS_VERSION_,
                'product_details' => $productCustomDetails,
            )
        );

        return $this->display(__FILE__, 'views/templates/hook/admin/displayAdminCustomProduct.tpl');
    }
    
    /**
     * @param array $params
     * @return string
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function hookDisplayAdminOrdersListAfter($params)
    {
        return $this->display(__FILE__, 'views/templates/hook/admin/displayAdminOrdersListAfter.tpl');
    }

    /**
     * @param array $params
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function hookActionProductUpdate($params)
    {
        if (Tools::getValue('colissimo_update_product')) {
            $idProduct = $params['id_product'];
            $hsCode = Tools::getValue('colissimo_hs_code');
            $shortDescription = Tools::getValue('colissimo_short_desc');
            $countryOrigin = Tools::getValue('colissimo_country_origin');
            $customProduct = ColissimoCustomProduct::getByIdProduct((int) $idProduct);
            $customProduct->id_product = (int) $idProduct;
            $customProduct->short_desc = $shortDescription;
            $customProduct->id_country_origin = (int) $countryOrigin;
            $customProduct->hs_code = $hsCode;
            $customProduct->save();
        }
    }

    /**
     * @param array $params
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function hookActionAdminCategoriesFormModifier($params)
    {
        $countries = Country::getCountries((int) Context::getContext()->cookie->id_lang);
        array_unshift($countries, ['id_country' => '0', 'name' => $this->l('-- Please select a country --')]);
        $params['fields']['colissimo'] = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Customs data Colissimo'),
                    'icon'  => 'icon-tags',
                ),
                'input'  => array(
                    array(
                        'type'  => 'text',
                        'label' => $this->l('Short description'),
                        'name'  => 'colissimo_short_desc',
                        'size'  => 64,
                    ),
                    array(
                        'type'    => 'select',
                        'label'   => $this->l('Origin country'),
                        'name'    => 'colissimo_country_origin',
                        'options' => array(
                            'query' => $countries,
                            'id'    => 'id_country',
                            'name'  => 'name',
                        ),
                    ),
                    array(
                        'type'  => 'text',
                        'label' => $this->l('HS Code'),
                        'name'  => 'colissimo_hs_code',
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
        $idCategory = (int) Tools::getValue('id_category');
        $categoryCustomDetails = ColissimoCustomCategory::getByIdCategory($idCategory);
        $params['fields_value']['colissimo_short_desc'] = $categoryCustomDetails->short_desc;
        $params['fields_value']['colissimo_country_origin'] = $categoryCustomDetails->id_country_origin;
        $params['fields_value']['colissimo_hs_code'] = $categoryCustomDetails->hs_code;
    }

    /**
     * @param array $params
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function hookActionAdminCategoriesControllerSaveAfter($params)
    {
        $idCategory = (int) Tools::getValue('id_category');
        $shortDescription = Tools::getValue('colissimo_short_desc');
        $countryOrigin = Tools::getValue('colissimo_country_origin');
        $hsCode = Tools::getValue('colissimo_hs_code');

        $customCategory = ColissimoCustomCategory::getByIdCategory((int) $idCategory);
        $customCategory->id_category = (int) $idCategory;
        $customCategory->short_desc = $shortDescription;
        $customCategory->id_country_origin = (int) $countryOrigin;
        $customCategory->hs_code = $hsCode;
        $customCategory->save();
    }

    /**
     * @param array $params
     * @throws \PrestaShop\PrestaShop\Core\Module\Exception\ModuleErrorException
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function hookActionAfterUpdateCategoryFormHandler($params)
    {
        $this->updateCategoryCustomData($params);
    }

    /**
     * @param array $params
     * @throws \PrestaShop\PrestaShop\Core\Module\Exception\ModuleErrorException
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function hookActionAfterCreateCategoryFormHandler($params)
    {
        $this->updateCategoryCustomData($params);
    }

    /**
     * @param array $params
     * @throws \PrestaShop\PrestaShop\Core\Module\Exception\ModuleErrorException
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    private function updateCategoryCustomData($params)
    {
        $customCategory = new ColissimoCustomCategory((int) $params['form_data']['colissimo_custom_category_id']);
        $customCategory->id_category = $params['id'];
        $customCategory->short_desc = $params['form_data']['colissimo_short_desc'];
        $customCategory->id_country_origin = (int) $params['form_data']['colissimo_country_origin'];
        $customCategory->hs_code = $params['form_data']['colissimo_hs_code'];
        try {
            $customCategory->save();
        } catch (Exception $e) {
            throw new \PrestaShop\PrestaShop\Core\Module\Exception\ModuleErrorException($e->getMessage());
        }
    }

    /**
     * @param array $params
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function hookActionCategoryFormBuilderModifier($params)
    {
        /** @var \Symfony\Component\Form\FormBuilderInterface $formBuilder */
        $formBuilder = $params['form_builder'];
        $customCategory = ColissimoCustomCategory::getByIdCategory($params['id']);
        $countries = Country::getCountries($this->context->language->id);
        $countryChoices = array();
        array_walk(
            $countries,
            function (&$country) use (&$countryChoices) {
                $countryChoices[$country['name']] = $country['id_country'];
            }
        );
        $formBuilder->add(
            'colissimo_short_desc',
            \Symfony\Component\Form\Extension\Core\Type\TextType::class,
            array('label' => $this->l('Short description'), 'required' => false)
        );
        $formBuilder->add(
            'colissimo_country_origin',
            \Symfony\Component\Form\Extension\Core\Type\ChoiceType::class,
            array('label' => $this->l('Origin country'), 'choices' => $countryChoices, 'required' => false)
        );
        $formBuilder->add(
            'colissimo_hs_code',
            \Symfony\Component\Form\Extension\Core\Type\TextType::class,
            array('label' => $this->l('HS Code'), 'required' => false)
        );
        $formBuilder->add(
            'colissimo_custom_category_id',
            \Symfony\Component\Form\Extension\Core\Type\HiddenType::class,
            array('data' => $customCategory->id)
        );

        $params['data']['colissimo_short_desc'] = $customCategory->short_desc;
        $params['data']['colissimo_country_origin'] = $customCategory->id_country_origin;
        $params['data']['colissimo_hs_code'] = $customCategory->hs_code;

        $formBuilder->setData($params['data']);
    }

    /**
     *
     */
    public function hookActionAdminControllerSetMedia()
    {
        if (Tools::getValue('module_name') == $this->name || Tools::getValue('configure') == $this->name) {
            Media::addJsDef(array('baseAdminDir' => __PS_BASE_URI__.basename(_PS_ADMIN_DIR_).'/'));
            Media::addJsDef(array('baseDir' => __PS_BASE_URI__));
            $this->context->controller->addCSS($this->_path.'views/css/header.back.css');
            $this->context->controller->addCSS($this->_path.'views/css/config.back.css');
            $this->context->controller->addCSS($this->_path.'views/css/admin.modal.css');
            $this->context->controller->addJqueryPlugin('colorpicker');
            $this->context->controller->addJS($this->_path.'views/js/jquery.inputmask.bundle.js');
            $this->context->controller->addJS($this->_path.'views/js/config.back.js');
        }
        if (in_array(Tools::getValue('controller'), $this->controllersBO)) {
            Media::addJsDef(array('baseAdminDir' => __PS_BASE_URI__.basename(_PS_ADMIN_DIR_).'/'));
            $this->context->controller->addCSS($this->_path.'views/css/header.back.css');
            $this->context->controller->addCSS($this->_path.'views/css/admin.back.css');
            $this->context->controller->addCSS($this->_path.'views/css/admin.modal.css');
            $this->context->controller->addJS($this->_path.'views/js/admin.back.js');
            $this->context->controller->addJS($this->_path.'views/js/print.min.js');
        }
        if (Tools::getValue('controller') == 'AdminOrders') {
            Media::addJsDef(array('baseAdminDir' => __PS_BASE_URI__.basename(_PS_ADMIN_DIR_).'/'));
            $this->context->controller->addCSS($this->_path.'views/css/admin.back.css');
            $this->context->controller->addCSS($this->_path.'views/css/admin.order.css');
            $this->context->controller->addCSS($this->_path.'views/css/header.back.css');
            $this->context->controller->addCSS($this->_path.'views/css/admin.modal.css');
            $this->context->controller->addJS($this->_path.'views/js/admin.back.js');
            $this->context->controller->addJS($this->_path.'views/js/jquery.plugin.colissimo-v2.js');
            $this->context->controller->addJS('https://api.mapbox.com/mapbox-gl-js/v2.3.1/mapbox-gl.js');
            $this->context->controller->addJS($this->_path.'views/js/print.min.js');
            $this->context->controller->addCSS($this->_path.'views/css/bootstrap.colissimo.min.css');
            $this->context->controller->addCSS($this->_path.'views/css/colissimo.widget.css');
            $this->context->controller->addCSS($this->_path.'views/css/mapbox.css');
        }
    }

    /**
     *
     */
    public function hookHeader()
    {
        if ($this->context->controller->php_self == 'order' || $this->context->controller->php_self == 'order-opc') {
            $this->context->controller->addCSS($this->_path.'views/css/colissimo.front.css');
            $this->context->controller->addCSS($this->_path.'views/css/colissimo.modal.css');
            $this->context->controller->addCSS($this->_path.'views/css/intlTelInput.css');
            if (!Configuration::get('COLISSIMO_WIDGET_REMOTE')) {
                $this->context->controller->addCSS($this->_path.'views/css/bootstrap.colissimo.min.css');
                $this->context->controller->addCSS($this->_path.'views/css/colissimo.widget.css');
                $this->context->controller->addCSS($this->_path.'views/css/mapbox.css');
            }
            $pluginPath = Media::getJqueryPluginPath('autocomplete', null);
            if (!empty($pluginPath['js'])) {
                $this->context->controller->unregisterJavascript(
                    str_replace(_PS_JS_DIR_.'jquery/plugins/', '', $pluginPath['js'])
                );
            }
            $this->context->controller->addJqueryUI('ui.autocomplete');
            $this->registerJs(
                'colissimo-intltelinput',
                'intlTelInput.min.js',
                array('position' => 'bottom', 'priority' => 150)
            );
            if (!Configuration::get('COLISSIMO_WIDGET_REMOTE')) {
                $this->registerJs(
                    'colissimo-bootstrap',
                    'bootstrap.min.js',
                    array('position' => 'bottom', 'priority' => 150)
                );
            }
            $this->registerJs(
                'colissimo-module-front-widget',
                'widget.js',
                array('position' => 'bottom', 'priority' => 150)
            );
            if (!Configuration::get('COLISSIMO_WIDGET_REMOTE')) {
                $this->registerExternalJs(
                    'colissimo-front-widget-mapbox',
                    'https://api.mapbox.com/mapbox-gl-js/v2.3.1/mapbox-gl.js',
                    array('server' => 'remote')
                );
                $this->registerJs(
                    'colissimo-plugin-widget',
                    'jquery.plugin.colissimo-v2.js',
                    array('position' => 'bottom', 'priority' => 150)
                );
            } else {
                $this->registerExternalJs(
                    'colissimo-front-widget-mapbox',
                    'https://api.mapbox.com/mapbox-gl-js/v2.3.1/mapbox-gl.js',
                    array('server' => 'remote')
                );
                $this->registerExternalJs(
                    'colissimo-front-widget',
                    'https://ws.colissimo.fr/widget-colissimo/js/jquery.plugin.colissimo.js',
                    array('server' => 'remote')
                );
            }
            $this->registerJS(
                'colissimo-js-custom',
                'colissimo.custom.js',
                array('position' => 'bottom', 'priority' => 250)
            );
            $this->registerCSS(
                'colissimo-css-custom',
                'colissimo.custom.css',
                array('position' => 'bottom', 'priority' => 250)
            );
        }
        if ($this->context->controller->php_self == 'history' ||
            $this->context->controller->php_self == 'order-detail'
        ) {
            $this->context->controller->addCSS($this->_path.'views/css/colissimo.front.css');
        }
    }

    /**
     * @param array $params
     * @return array
     */
    public function hookAddWebserviceResources($params)
    {
        return array(
            'colissimo_custom_products' => array(
                'class' => 'ColissimoCustomProduct',
                'forbidden_method' => array('HEAD', 'POST', 'PUT', 'DELETE'),
            ),
            'colissimo_custom_categories' => array(
                'class' => 'ColissimoCustomCategory',
                'forbidden_method' => array('HEAD', 'POST', 'PUT', 'DELETE'),
            ),
            'colissimo_ace' => array(
                'class' => 'ColissimoACE',
                'forbidden_method' => array('GET', 'HEAD', 'PUT', 'DELETE'),
            ),
        );
    }
}