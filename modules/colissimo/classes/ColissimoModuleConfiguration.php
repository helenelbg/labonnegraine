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

/**
 * Class ColissimoModuleConfiguration
 */
class ColissimoModuleConfiguration
{
    /** @var Context $context */
    private $context;

    /** @var string $localPath */
    private $localPath;

    /** @var string $pathUri */
    private $pathUri;

    /** @var string $version */
    private $version;

    /** @var array $migrateModuleFromList */
    private $migrateModuleFromList = array(
        'colissimo_simplicite',
        'socolissimo',
        'sonice_etiquetage',
        'soflexibilite',
    );

    /** @var array $modulesToMigrate */
    public $modulesToMigrate = array();

    /** @var array $senderAddressFields */
    public $senderAddressFields = array(
        'sender_company',
        'sender_lastname',
        'sender_firstname',
        'sender_address1',
        'sender_address2',
        'sender_address3',
        'sender_address4',
        'sender_city',
        'sender_zipcode',
        'sender_country',
        'sender_phone',
        'sender_email',
    );

    /** @var array $returnAddressFields */
    public $returnAddressFields = array(
        'return_company',
        'return_lastname',
        'return_firstname',
        'return_address1',
        'return_address2',
        'return_address3',
        'return_address4',
        'return_city',
        'return_zipcode',
        'return_country',
        'return_phone',
        'return_email',
    );

    /** @var array $accountFields */
    public $accountFields = array(
        'COLISSIMO_LOGS',
        'COLISSIMO_ACCOUNT_LOGIN',
        'COLISSIMO_ACCOUNT_PASSWORD',
    );

    /** @var array $widgetFields */
    public $widgetFields = array(
        'COLISSIMO_WIDGET_REMOTE',
        'COLISSIMO_WIDGET_ENDPOINT',
        'COLISSIMO_WIDGET_COLOR_1',
        'COLISSIMO_WIDGET_COLOR_2',
        'COLISSIMO_WIDGET_FONT',
    );
    
     /** @var array $backOrdersFields */
    public $backOrdersFields = array(
        'COLISSIMO_ORDER_PREPARATION_TIME',
        'COLISSIMO_USE_SHIPPING_IN_PROGRESS',
        'COLISSIMO_USE_HANDLED_BY_CARRIER',
        'COLISSIMO_ENABLE_PNA_MAIL',
        'COLISSIMO_DISPLAY_TRACKING_NUMBER',
    );
    
     /** @var array $backPrintFields */
    public $backPrintFields = array(
        'COLISSIMO_GENERATE_LABEL_PRESTASHOP',
        'COLISSIMO_POSTAGE_MODE_MANUAL',
        'COLISSIMO_USE_THERMAL_PRINTER',
        'COLISSIMO_USE_ETHERNET',
        'COLISSIMO_USB_PROTOCOLE',
        'COLISSIMO_PRINTER_IP_ADDR',
        'COLISSIMO_LABEL_FORMAT',
        'COLISSIMO_LABEL_DISPLAY_REFERENCE',
    );
    
    /** @var array $backShippingFields */
    public $backShippingFields = array(
        'COLISSIMO_DEFAULT_ORIGIN_COUNTRY',
        'COLISSIMO_DEFAULT_HS_CODE',
        'COLISSIMO_EORI_NUMBER',
        'COLISSIMO_EORI_NUMBER_UK',
        'COLISSIMO_POSTAL_PARTNER',
    );
    
    /** @var array $backPostageFields */
    public $backPostageFields = array(
        'COLISSIMO_USE_WEIGHT_TARE',
        'COLISSIMO_DEFAULT_WEIGHT_TARE',
        'COLISSIMO_INSURE_SHIPMENTS',
    );

    /** @var array $backFields */
    public $backFields = array(
        'COLISSIMO_USE_RETURN_ADDRESS',
        'COLISSIMO_WIDGET_PHONE',
    );
    
    /** @var array $frontConfigFields */
    public $frontConfigFields = array(
        'COLISSIMO_ENABLE_DDP',
        'COLISSIMO_DDP_COST',
        'COLISSIMO_DDP_GB_COST',
        'COLISSIMO_ENABLE_DOOR_CODES',
        'COLISSIMO_WEIGHTING_PRICES',
        'COLISSIMO_WEIGHTING_TYPE',
        'COLISSIMO_WEIGHTING_VALUE_PERCENT',
        'COLISSIMO_WEIGHTING_VALUE_AMOUNT'
    );

    /** @var array $defaultShipmentsFields */
    public $defaultShipmentsFields = array(
        'COLISSIMO_ENABLE_RETURN',
        'COLISSIMO_AUTO_PRINT_RETURN_LABEL',
        'COLISSIMO_DISPLAY_RETURN_LABEL_CUSTOMER',
        'COLISSIMO_GENERATE_RETURN_LABEL_CUSTOMER',
        'COLISSIMO_ENABLE_MAILBOX_RETURN',
    );
    
    /** @var array $defaultShipmentsFields */
    public $filesFields = array(
        'COLISSIMO_FILES_LIMIT',
        'COLISSIMO_FILES_LIFETIME',
    );

    /** @var array $widgetFonts */
    public static $widgetFonts = array(
        'Arial',
        'Arial Black',
        'Comic Sans MS',
        'Courrier New',
        'Georgia',
        'Impact',
        'Lucida Console',
        'Lucida Sans Unicode',
        'Tahoma',
        'Times New Roman',
        'Trebuchet MS',
        'Verdana',
        'MS Sans Serif',
        'MS Serif',
    );

    /** @var array $labelFormats */
    public $labelFormats = array(
        'PDF_A4_300dpi'    => 'PDF A4 300dpi',
        'PDF_10x15_300dpi' => 'PDF 10x15 300dpi',
        'ZPL_10x15_203dpi' => 'ZPL 10x15 203dpi',
        'ZPL_10x15_300dpi' => 'ZPL 10x15 300dpi',
        'DPL_10x15_203dpi' => 'DPL 10x15 203dpi',
        'DPL_10x15_300dpi' => 'DPL 10x15 300dpi',
    );

    /**
     * @var array $usbPrinterProtocoles
     */
    public $usbPrinterProtocoles = array(
        'DATAMAX' => 'DATAMAX',
        'INTERMEC' => 'INTERMEC',
        'ZEBRA' => 'ZEBRA',
    );

    /** @var array $colissimoLinks */
    public $colissimoLinks = array(
        'en' => array(
            'delivery_details' => 'https://www.colissimo.entreprise.laposte.fr/en/possibilites',
            'subscribe'        => 'https://www.colissimo.entreprise.laposte.fr/en/souscrire',
        ),
        'fr' => array(
            'delivery_details' => 'https://www.colissimo.entreprise.laposte.fr/fr/possibilites',
            'subscribe'        => 'https://www.colissimo.entreprise.laposte.fr/fr/souscrire',
        ),
    );

    /**
     * ColissimoModuleConfiguration constructor.
     * @param Context $context
     * @param string  $localPath
     * @param string  $pathUri
     * @param string  $version
     */
    public function __construct(Context $context, $localPath, $pathUri, $version = '')
    {
        $this->context = $context;
        $this->localPath = $localPath;
        $this->pathUri = $pathUri;
        $this->version = $version;
    }

    /**
     * @throws Exception
     * @throws SmartyException
     */
    public function getContent()
    {
        $this->context->controller->addJS($this->localPath.'views/js/intlTelInput.min.js');
        $this->context->controller->addCSS($this->localPath.'views/css/intlTelInput.css');
        $this->context->smarty->assign('link', $this->context->link);
        $merchantSenderAddress = new ColissimoMerchantAddress('sender');
        $senderAddress = array_merge(
            array_fill_keys($this->senderAddressFields, ''),
            $merchantSenderAddress->toArray()
        );
        $merchantReturnAddress = new ColissimoMerchantAddress('return');
        $returnAddress = array_merge(
            array_fill_keys($this->returnAddressFields, ''),
            $merchantReturnAddress->toArray()
        );
        $orderStatuses = OrderState::getOrderStates($this->context->language->id);
        $states = array();
        foreach ($orderStatuses as $orderStatus) {
            $states[(int) $orderStatus['id_order_state']] = $orderStatus['name'];
        }

        $accountFieldsValue = Configuration::getMultiple($this->accountFields);
        $accountFieldsValue['COLISSIMO_ACCOUNT_TYPE'] = json_decode(Configuration::get('COLISSIMO_ACCOUNT_TYPE'), true);
        $widgetFieldsValue = Configuration::getMultiple($this->widgetFields);
        $backOrdersFieldsValue = Configuration::getMultiple($this->backOrdersFields);
        $backOrdersFieldsValue['COLISSIMO_GENERATE_LABEL_STATUSES'] = json_decode(
            Configuration::get('COLISSIMO_GENERATE_LABEL_STATUSES'),
            true
        );
        $backPrintFieldsValue = Configuration::getMultiple($this->backPrintFields);
        $backShippingFieldsValue = Configuration::getMultiple($this->backShippingFields);
        $backPostageFieldsValue = Configuration::getMultiple($this->backPostageFields);
        $backFieldsValue = Configuration::getMultiple($this->backFields);
        $frontConfigFields = Configuration::getMultiple($this->frontConfigFields);
        $defaultShipmentsFieldsValue = Configuration::getMultiple($this->defaultShipmentsFields);
        $filesFieldsValue = ColissimoTools::getMultipleGlobal($this->filesFields);
        $formData = array_merge(
            $senderAddress,
            $returnAddress,
            $accountFieldsValue,
            $widgetFieldsValue,
            $backOrdersFieldsValue,
            $backPrintFieldsValue,
            $backShippingFieldsValue,
            $backPostageFieldsValue,
            $backFieldsValue,
            $frontConfigFields,
            $defaultShipmentsFieldsValue,
            $filesFieldsValue
        );
        $isoLang = Language::getIsoById($this->context->language->id);
        //@formatter:off
        $colissimoLinks = isset($this->colissimoLinks[$isoLang]) ? $this->colissimoLinks[$isoLang] : $this->colissimoLinks['en'];
        //@formatter:on
        $showMigration = $this->mustShowMigration();
        if ($showMigration) {
            $this->context->controller->addJS($this->localPath.'views/js/colissimo.migration.js');
            if (Shop::getContext() != Shop::CONTEXT_ALL) {
                Shop::setContext(Shop::CONTEXT_ALL);
            }
        }
        $documentsDirData = ColissimoTools::getDocumentsDirDetails(dirname(__FILE__).'/../documents/');
        $formData['documents_dir_size'] = ColissimoTools::formatDirectorySize($documentsDirData['total_size']);
        $formData['documents_dir_count'] = $documentsDirData['count'];
        $countriesList = array();
        $isoSender = array_merge(ColissimoTools::$isoSender, ColissimoTools::$isoOutreMer);
        foreach ($isoSender as $iso) {
            $countriesList[$iso] = Country::getNameById($this->context->language->id, Country::getByIso($iso));
        }
        $countries = Country::getCountries((int) $this->context->cookie->id_lang);
        $formData['default_origin_country'] =  Configuration::get('COLISSIMO_DEFAULT_ORIGIN_COUNTRY');
        $this->context->smarty->assign(
            array(
                'form_data'          => $formData,
                'defaultCurrency'    => Currency::getDefaultCurrency(),
                'address_countries'  => $countriesList,
                'countries'          => $countries,
                'widget_fonts'       => self::$widgetFonts,
                'label_formats'      => $this->labelFormats,
                'usb_protocoles'     => $this->usbPrinterProtocoles,
                'order_states'       => $states,
                'colissimo_img_path' => $this->pathUri.'views/img/',
                'colissimo_links'    => $colissimoLinks,
                'show_migration'     => $showMigration,
                'modules_to_migrate' => array_reverse($this->modulesToMigrate),
                'module_version'     => $this->version,
            )
        );
        $tpl = $this->context->smarty->fetch($this->localPath.'views/templates/admin/configuration/layout.config.tpl');

        return $tpl;
    }

    /**
     * @return bool
     */
    public function mustShowMigration()
    {
        if ((int) Configuration::getGlobalValue('COLISSIMO_SHOW_MIGRATION') === -1) {
            return false;
        }
        foreach ($this->migrateModuleFromList as $module) {
            if (Module::isInstalled($module) && Module::isEnabled($module)) {
                $this->modulesToMigrate[] = $module;
            }
        }

        return !empty($this->modulesToMigrate);
    }
}
