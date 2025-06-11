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
 * Class AdminColissimoAffranchissementController
 *
 * Ajax processes:
 *  - orderDetails
 *  - addressDetails
 *  - addressSave
 *  - updateTotalweight
 *  - generateLabel
 *  - displayResult
 *  - purgeDocuments
 *  - sendCustomsDocuments
 *
 */
class AdminColissimoAffranchissementController extends ModuleAdminController
{
    /** @var int $step */
    private $step;

    /** @var Colissimo $module */
    public $module;

    /** @var int $selection */
    private $selection = 0;

    /** @var int $autostart */
    private $autostart = 0;

    /** @var string $header */
    private $header;

    /** @var string $progressBar */
    private $progressBar;

    /** @var string $docsAlert */
    private $docsAlert;

    /**
     * AdminColissimoAffranchissementController constructor.
     * @throws PrestaShopException
     */
    public function __construct()
    {
        $this->bootstrap = true;
        parent::__construct();
        $this->module->logger->setChannel('Affranchissement');
        $this->initSteps();
    }

    /**
     * @throws SmartyException
     */
    public function initModal()
    {
        parent::initModal();
        $this->modals[] = $this->module->getWhatsNewModal();
    }

    /**
     *
     */
    public function initSteps()
    {
        $this->step = 1;
        if (Tools::isSubmit('submitBulkhideAllcolissimo_order')) {
            $this->hideSelectedOrders();
        }
        if (Tools::isSubmit('submitBulkselectAllcolissimo_order')) {
            $this->step = 2;
            $this->selection = 0;
        }
        if (!Configuration::get('COLISSIMO_POSTAGE_MODE_MANUAL')) {
            $this->step = 2;
            $this->selection = 0;
            $this->autostart = 1;
        }
        if (Tools::isSubmit('submitProcessColissimoSelection')) {
            $this->step = 2;
            $this->selection = 1;
        }
        if (Tools::isSubmit('submitProcessColissimoConfiguration')) {
            $this->step = 3;
            $this->selection = 1;
        }
        if (Tools::isSubmit('submitEditColissimoCustomsDocuments')) {
            $this->step = 4;
            $this->selection = 1;
        }
        if (!(int) $this->step) {
            $this->step = 1;
        }
    }

    /**
     * @throws Exception
     * @throws PrestaShopDatabaseException
     * @throws SmartyException
     */
    public function initProcess()
    {
        $this->header = $this->module->setColissimoControllerHeader();
        switch ($this->step) {
            case 1:
            default:
                $this->initStep1();
                break;
            case 2:
                $this->initStep2($this->selection);
                break;
            case 4:
                $this->initStep4();
                break;
        }
        parent::initProcess();
    }

    /**
     * @param bool $isNewTheme
     */
    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        Media::addJsDef(['address_token' => Tools::getAdminTokenLite('AdminAddresses')]);
        Media::addJsDef(['state_token' => Tools::getAdminTokenLite('AdminStates')]);
        $this->addJS($this->module->getLocalPath().'views/js/colissimo.affranchissement.js');
        $this->addJS($this->module->getLocalPath().'views/js/jquery.inputmask.bundle.js');
        $this->addJS($this->module->getLocalPath().'views/js/jquery.plugin.colissimo-v2.js');
        $this->addJS('https://api.mapbox.com/mapbox-gl-js/v2.3.1/mapbox-gl.js');
        $this->addCSS($this->module->getLocalPath().'views/css/bootstrap.colissimo.min.css');
        $this->addCSS($this->module->getLocalPath().'views/css/colissimo.widget.css');
        $this->addCSS($this->module->getLocalPath().'views/css/mapbox.css');
        $this->addJqueryUI('ui.autocomplete');
    }

    /**
     * @return array
     * @throws PrestaShopDatabaseException
     */
    public function getAllOrdersToProcess()
    {
        $selectedStates = array_keys(json_decode(Configuration::get('COLISSIMO_GENERATE_LABEL_STATUSES'), true));
        $dbQuery = new DbQuery();
        //@formatter:off
        $dbQuery->select('co.`id_colissimo_order`')
                ->from('colissimo_order', 'co')
                ->leftJoin('orders', 'o', 'o.`id_order` = co.`id_order`')
                ->leftJoin('colissimo_label', 'cola', 'cola.`id_colissimo_order` = co.`id_colissimo_order`');
        //@formatter:on
        if (!empty($selectedStates)) {
            $dbQuery->where('o.`current_state` IN ('.implode(',', array_map('intval', $selectedStates)).')');
        }
        $dbQuery->where('co.hidden = 0');
        $dbQuery->where('cola.id_colissimo_label IS NULL'.Shop::addSqlRestriction(false, 'o'));
        $dbQuery->orderBy('o.date_add DESC');
        $results = Db::getInstance(_PS_USE_SQL_SLAVE_)
                     ->executeS($dbQuery);

        return array_map(
            function ($element) {
                return $element['id_colissimo_order'];
            },
            $results
        );
    }

    /**
     * @param int $docsLifetime
     * @return array
     * @throws PrestaShopDatabaseException
     */
    public function getFilesToDelete($docsLifetime)
    {
        $dateTime = new DateTime(date('Y-m-d H:i:s'));
        $dateTime->sub(new DateInterval('P'.(int) $docsLifetime.'D'));
        $deleteBefore = $dateTime->format('Y-m-d H:i:s');
        $labelsToDeleteQuery = new DbQuery();
        $labelsToDeleteQuery->select('id_colissimo_label')
                            ->from('colissimo_label')
                            ->where('file_deleted = 0')
                            ->where('migration = 0')
                            ->where('date_add < "'.pSQL($deleteBefore).'"');
        $labelToDeleteIds = array_map(
            function ($element) {
                return $element['id_colissimo_label'];
            },
            Db::getInstance(_PS_USE_SQL_SLAVE_)
              ->executeS($labelsToDeleteQuery)
        );
        $depositSlipsToDeleteQuery = new DbQuery();
        $depositSlipsToDeleteQuery->select('id_colissimo_deposit_slip')
                                  ->from('colissimo_deposit_slip')
                                  ->where('file_deleted = 0')
                                  ->where('date_add < "'.pSQL($deleteBefore).'"');
        $depositSlipToDeleteIds = array_map(
            function ($element) {
                return $element['id_colissimo_deposit_slip'];
            },
            Db::getInstance(_PS_USE_SQL_SLAVE_)
              ->executeS($depositSlipsToDeleteQuery)
        );
        $total = count($labelToDeleteIds) + count($depositSlipToDeleteIds);

        return array('labels' => $labelToDeleteIds, 'deposit_slips' => $depositSlipToDeleteIds, 'total' => $total);
    }

    /**
     * @return string
     * @throws Exception
     * @throws SmartyException
     */
    public function getProgressBar()
    {
        $step = array(
            1 => array(
                'state' => $this->step == 1 ? 'current' : 'complete',
                'line'  => $this->step == 1 ? '' : 'complete',
            ),
            2 => array(
                'state' => $this->step == 2 ? 'current' : 'incomplete',
                'line'  => $this->step == 2 ? '' : 'incomplete',
            ),
            4 => array(
                'state' => 'incomplete',
            ),
        );
        $this->context->smarty->assign('step', $step);

        return $this->createTemplate('_partials/progress-bar.tpl')
                    ->fetch();
    }

    /**
     * @return string
     * @throws Exception
     * @throws SmartyException
     */
    public function getDocsAlert()
    {
        $limit = Configuration::getGlobalValue('COLISSIMO_FILES_LIMIT');
        $docsLifetime = Configuration::getGlobalValue('COLISSIMO_FILES_LIFETIME');
        $dirDetails = ColissimoTools::getDocumentsDirDetails(dirname(__FILE__).'/../../documents/');
        if ($dirDetails['count'] > $limit) {
            $filesToDelete = $this->getFilesToDelete($docsLifetime);
            if (!$filesToDelete['total']) {
                return '';
            }
            $this->context->smarty->assign(
                array(
                    'docs_lifetime'  => $docsLifetime,
                    'docs_count'     => $dirDetails['count'],
                    'docs_size'      => $dirDetails['total_size'],
                    'docs_to_delete' => $filesToDelete['total'],
                )
            );

            return $this->createTemplate('_partials/docs-alert.tpl')
                        ->fetch();
        }

        return '';
    }

    /**
     * @param Helper $helper
     */
    public function setHelperDisplay(Helper $helper)
    {
        parent::setHelperDisplay($helper);
        $this->helper->force_show_bulk_actions = true;
    }

    /**
     * @throws Exception
     * @throws SmartyException
     */
    public function initContent()
    {
        $modePS = Configuration::get('COLISSIMO_GENERATE_LABEL_PRESTASHOP');
        if (!$modePS) {
            //@formatter:off
            $this->errors[] = $this->module->l('Colissimo postage mode is set to "Coliship". If you want to edit labels, change this setting in Colissimo module.', 'AdminColissimoAffranchissementController');
            //@formatter:on
            return;
        }
        $this->docsAlert = $this->getDocsAlert();
        $this->progressBar = $this->getProgressBar();
        $this->content = $this->header.$this->docsAlert.$this->progressBar.$this->content;
        parent::initContent();
    }

    /**
     * @param int  $idLang
     * @param null $orderBy
     * @param null $orderWay
     * @param int  $start
     * @param null $lim
     * @param bool $idLangShop
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function getList($idLang, $orderBy = null, $orderWay = null, $start = 0, $lim = null, $idLangShop = false)
    {
        parent::getList($idLang, $orderBy, $orderWay, $start, $lim, $idLangShop);
        if ($this->_listTotal > 0) {
            $this->bulk_actions = array(
                'selectAll' => array(
                    'text' => $this->module->l('Configure all shipments', 'AdminColissimoAffranchissementController'),
                    'icon' => 'icon-arrow-circle-o-right',
                ),
                'hideAll' => array(
                    'text' => $this->module->l('Hide selected shipments', 'AdminColissimoAffranchissementController'),
                    'icon' => 'icon-eye-close',
                ),
            );
        }
    }

    /**
     * @throws PrestaShopDatabaseException
     */
    public function initStep1()
    {
        $idLang = $this->context->language->id;
        $statusesList = array();
        $statuses = OrderState::getOrderStates((int) $idLang);
        foreach ($statuses as $status) {
            $statusesList[$status['id_order_state']] = $status['name'];
        }
        $colissimoServicesList = array();
        $colissimoServices = ColissimoService::getAll();
        foreach ($colissimoServices as $colissimoService) {
            $colissimoServicesList[$colissimoService['commercial_name']] = $colissimoService['commercial_name'];
        }
        ksort($colissimoServicesList, SORT_ASC);
        $countriesList = array();
        $countries = Country::getCountries((int) $idLang);
        foreach ($countries as $country) {
            $countriesList[$country['id_country']] = $country['name'];
        }
        $selectedStates = array_keys(json_decode(Configuration::get('COLISSIMO_GENERATE_LABEL_STATUSES'), true));
        $select = array(
            'o.`reference`',
            'o.`id_order`',
            'CONCAT(LEFT(c.`firstname`, 1), ". ", c.`lastname`) AS `customer`',
            'osl.`name` AS `osname`',
            'os.`color`',
            'o.`date_add`',
            'cs.`commercial_name`',
            'cl.`name` AS `country`',
            '"--"',
        );
        //@formatter:off
        $join = array(
            'LEFT JOIN `'._DB_PREFIX_.'orders` o ON o.`id_order` = a.`id_order`',
            'LEFT JOIN `'._DB_PREFIX_.'address` ad ON ad.`id_address` = o.`id_address_delivery`',
            'LEFT JOIN `'._DB_PREFIX_.'country` co ON co.`id_country` = ad.`id_country`',
            'LEFT JOIN `'._DB_PREFIX_.'country_lang` cl ON (cl.`id_country` = ad.`id_country` AND cl.`id_lang` = '.$idLang.')',
            'LEFT JOIN `'._DB_PREFIX_.'customer` c ON c.`id_customer` = o.`id_customer`',
            'LEFT JOIN `'._DB_PREFIX_.'order_state` os ON os.`id_order_state` = o.`current_state`',
            'LEFT JOIN `'._DB_PREFIX_.'order_state_lang` osl ON (osl.`id_order_state` = os.`id_order_state` AND osl.`id_lang` = '.(int) $idLang.')',
            'LEFT JOIN `'._DB_PREFIX_.'colissimo_service` cs ON cs.`id_colissimo_service` = a.`id_colissimo_service`',
            'LEFT JOIN `'._DB_PREFIX_.'colissimo_label` cola ON cola.`id_colissimo_order` = a.`id_colissimo_order`',
        );
        //@formatter:on
        $this->identifier = 'id_colissimo_order';
        $this->table = 'colissimo_order';
        $this->className = 'ColissimoOrder';
        $this->list_id = 'colissimo_order';
        $this->_select = implode(',', $select);
        $this->_join = implode(' ', $join);
        if (!empty($selectedStates)) {
            $this->_where = 'AND o.current_state IN('.implode(',', array_map('intval', $selectedStates)).') ';
        } else {
            $this->_where = '';
        }
        $this->_where .= 'AND cola.id_colissimo_label IS NULL';
        $this->_where .= ' AND a.hidden = 0 ';
        $this->_where .= Shop::addSqlRestriction(false, 'o');
        $this->list_no_link = true;
        $this->_orderBy = 'o.date_add';
        $this->_orderWay = 'DESC';
        $this->fields_list = array(
            'reference'       => array(
                'title'          => $this->module->l('Reference', 'AdminColissimoAffranchissementController'),
                'remove_onclick' => true,
                'class'          => 'pointer col-reference-plus',
            ),
            'id_order'        => array(
                'title'          => $this->module->l('ID', 'AdminColissimoAffranchissementController'),
                'havingFilter'   => true,
                'type'           => 'int',
                'filter_key'     => 'o!id_order',
                'remove_onclick' => true,
            ),
            'customer'        => array(
                'title'          => $this->module->l('Customer', 'AdminColissimoAffranchissementController'),
                'havingFilter'   => true,
                'remove_onclick' => true,
            ),
            'osname'          => array(
                'title'          => $this->module->l('Order state', 'AdminColissimoAffranchissementController'),
                'remove_onclick' => true,
                'type'           => 'select',
                'color'          => 'color',
                'list'           => $statusesList,
                'filter_key'     => 'os!id_order_state',
                'filter_type'    => 'int',
                'order_key'      => 'osname',
            ),
            'date_add'        => array(
                'title'          => $this->module->l('Date', 'AdminColissimoAffranchissementController'),
                'remove_onclick' => true,
                'type'           => 'datetime',
                'filter_key'     => 'o!date_add',
            ),
            'commercial_name' => array(
                'title'          => $this->module->l('Colissimo Service', 'AdminColissimoAffranchissementController'),
                'remove_onclick' => true,
                'type'           => 'select',
                'list'           => $colissimoServicesList,
                'filter_key'     => 'cs!commercial_name',
                'filter_type'    => 'string',
                'order_key'      => 'commercial_name',
            ),
            'country'         => array(
                'title'          => $this->module->l('Delivery country', 'AdminColissimoAffranchissementController'),
                'remove_onclick' => true,
                'type'           => 'select',
                'list'           => $countriesList,
                'filter_key'     => 'co!id_country',
                'filter_type'    => 'int',
                'order_key'      => 'country',
            ),
        );
    }

    /**
     * @param int $selection
     * @throws Exception
     * @throws PrestaShopDatabaseException
     * @throws SmartyException
     */
    public function initStep2($selection)
    {
        if (!$selection){
            $ids = $this->getAllOrdersToProcess();
        } else {
            $ids = Tools::getValue('colissimo_orderBox');
        }
        if ($this->autostart){
            if ($ids && !empty($ids)) {
                $this->context->smarty->assign('autostartPostage', 1);
            }
        } else {
            if (!$ids || empty($ids)){
                //@formatter:off
                $this->errors[] = $this->module->l('Please select at least one order.', 'AdminColissimoAffranchissementController');
                //@formatter:on
                $this->step = 1;
                $this->initProcess();

                return;
            }
        }
        $data = array();
        $colissimoDDP = 0;
        $insuranceMsg = 0;
        $ecoOutreMerMsg = 0;
        foreach ($ids as $id){
            $colissimoOrder = new ColissimoOrder((int) $id);
            $order = new Order((int) $colissimoOrder->id_order);
            $deliveryAddr = new Address((int) $order->id_address_delivery);
            $colissimoService = new ColissimoService((int) $colissimoOrder->id_colissimo_service);
            if ($colissimoService->type == ColissimoService::TYPE_RELAIS) {
                $insuranceMsg = 1;
            }
            if ($colissimoService->type == ColissimoService::TYPE_ECO_OM) {
                $ecoOutreMerMsg = 1;
            }
            $data[$id] = array(
                'id_order'       => $order->id,
                'id_colissimo_order' => (int) $id,
                'reference'      => $order->reference,
                'delivery_addr'  => $deliveryAddr,
                'delivery_state' => ColissimoTools::getIsoStateById((int) $deliveryAddr->id_state),
                'address_valid'  => ColissimoTools::validateDeliveryAddress($deliveryAddr),
                'total_weight'   => ColissimoTools::getOrderTotalWeightInKg($order),
                'weight_unit'    => Configuration::get('PS_WEIGHT_UNIT'),
                'service'        => $colissimoService->commercial_name,
                'relais'         => $colissimoService->type == ColissimoService::TYPE_RELAIS,
            );
            $orderDetails = $order->getOrderDetailList();
            $data[$id]['products'] = $orderDetails;
            $isoCustomerAddr = Country::getIsoById((int) $deliveryAddr->id_country);
            $returnDestinationType = ColissimoTools::getReturnDestinationTypeByIsoCountry($isoCustomerAddr);
            $data[$id]['return_label'] = $returnDestinationType !== false ? 1 : -1;
            if ($data[$id]['return_label'] > 0 && !Configuration::get('COLISSIMO_AUTO_PRINT_RETURN_LABEL')) {
                $data[$id]['return_label'] = 0;
            }
            $isoOutreMerFtd = ColissimoTools::$isoOutreMer;
            if (($key = array_search('YT', $isoOutreMerFtd)) !== false){
                unset($isoOutreMerFtd[$key]);
            }
            if (($key = array_search('PM', $isoOutreMerFtd)) !== false){
                unset($isoOutreMerFtd[$key]);
            }
            $productCode = $colissimoService->product_code;
            $data[$id]['ftd'] = in_array($isoCustomerAddr, $isoOutreMerFtd) && $productCode == 'CDS' ? 1 : 0;
            $data[$id]['insurance'] = -1;
            if ($colissimoService->isInsurable($isoCustomerAddr)){
                if (Configuration::get('COLISSIMO_INSURE_SHIPMENTS')){
                    $data[$id]['insurance'] = 1;
                } else {
                    $data[$id]['insurance'] = 0;
                }
            }
            if ($colissimoService->product_code == 'DOS' && in_array($isoCustomerAddr, ColissimoTools::$isoPostalPartner)){
                $this->context->smarty->assign('postal_partner', Configuration::get('COLISSIMO_POSTAL_PARTNER'));
            }
	    if ($colissimoOrder->ddp == 1){
                $colissimoDDP = 1;
            }
        }
        if (Configuration::get('COLISSIMO_USE_WEIGHT_TARE') == '1'){
            $this->context->smarty->assign('weight_tare', Configuration::get('COLISSIMO_DEFAULT_WEIGHT_TARE'));
        }
        $this->context->smarty->assign(
            array('orders' => $data, 'img_path' => $this->module->getPathUri().'views/img/', 'colissimo_ddp' => $colissimoDDP, 'insurance_msg' => $insuranceMsg, 'ecoOm_msg' => $ecoOutreMerMsg)
        );
        $this->content = $this->createTemplate('step2/affranchissement-configuration-form.tpl')
                              ->fetch();
    }
    
    /**
     * @throws Exception
     * @throws PrestaShopDatabaseException
     * @throws SmartyException
     */
    public function initStep4()
    {
        $ids = Tools::getValue('labels');
        $checkedOrders = false;
        $data = array();
        foreach ($ids as $id) {
            $label = new ColissimoLabel((int) $id);
            $colissimoOrder = new ColissimoOrder((int) $label->id_colissimo_order);
            if (Tools::getValue('colissimo_order_'.$label->id_colissimo_order)) {
                $order = new Order((int) $colissimoOrder->id_order);
                $checkedOrders = true;
                $data[$label->id_colissimo_order] = array(
                    'id_order'           => (int) $order->id,
                    'id_colissimo_order' => (int) $label->id_colissimo_order,
                    'reference'          => $order->reference,
                    'id_colissimo_label' => (int) $id,
                    'shipping_number'    => $label->shipping_number,
                    'amount'             => $order->total_paid_tax_incl,
                    'has_invoice'        => $order->invoice_number ? true : false,
            
                );
            }
        }
        if (!$checkedOrders){
            //@formatter:off
            $this->errors[] = $this->module->l('Please select at least one order.', 'AdminColissimoAffranchissementController');
            //@formatter:on
            $this->step = 2;
            $this->initProcess();
            return;
        }
        $this->context->smarty->assign(
            array(
                'orders'   => $data,
                'img_path' => $this->module->getPathUri().'views/img/')
            );
        $this->content = $this->createTemplate('step4/affranchissement-customs-documents-form.tpl')
            ->fetch();
    }
    
    /**
     * @throws PrestaShopException
     */
    public function hideSelectedOrders()
    {
        $ids = Tools::getValue('colissimo_orderBox');
        if (!$ids || empty($ids)) {
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminColissimoAffranchissement'));
        }
        //@formatter:off
        Db::getInstance()->update('colissimo_order', array('hidden' => 1), 'id_colissimo_order IN ('.implode(', ', array_map('intval', $ids)).')');
        $this->confirmations[] = $this->module->l('Orders hidden successfully.', 'AdminColissimoAffranchissementController');
        //@formatter:on
    }

    /**
     * @return bool
     * @throws PrestaShopException
     */
    public function needCustomsDocuments($idColissimoOrder)
    {
        $colissimoOrder = new ColissimoOrder((int) $idColissimoOrder);
        $order = new Order((int) $colissimoOrder->id_order);
        $deliveryAddr = new Address((int) $order->id_address_delivery);
        $iso = Country::getIsoById($deliveryAddr->id_country);
        if (in_array($iso, ColissimoTools::$isoDom)) {
            return true;
        }
        if (in_array($iso, ColissimoTools::$isoDomDDP) && Configuration::get('COLISSIMO_ENABLE_DDP')) {
            return true;
        }
        
        return false;
    }
    
    /**
     *
     */
    public function getPostData()
    {
        $idColissimoOrder = (int) Tools::getValue('id_colissimo_order');
        $colissimoOrder = new ColissimoOrder((int) $idColissimoOrder);
        $colissimoService = new ColissimoService((int) $colissimoOrder->id_colissimo_service);
        $order = new Order((int) $colissimoOrder->id_order);
        $customerAddress = new Address((int) $order->id_address_delivery);
        $customerInvoiceAddress = new Address((int) $order->id_address_invoice);
        $merchantAddress = new ColissimoMerchantAddress('sender');
        $weight = Tools::getValue('colissimo_weight_'.$idColissimoOrder);
        $productsDetail = array();
        $productsPrice = 0;
        foreach ($order->getProductsDetail() as $product){
            $quantity = (int) Tools::getValue('colissimo_orderBox_'.$idColissimoOrder.'_'.$product['product_id'].'_'.$product['product_attribute_id']);
            if ($quantity > 0) {
                $productsDetail[$product['product_id']][$product['product_attribute_id']] = $quantity;
            }
            $productsPrice += (float) $product['unit_price_tax_excl'] * $quantity;
        }
        if (Configuration::get('COLISSIMO_USE_WEIGHT_TARE') == '1'){
           $weight += Tools::getValue('colissimo_weight_tare_'.$idColissimoOrder);
        }
        $id_address =  (int) ColissimoAddress::getAddressByCartId((int) $order->id_cart);
        $colissimoAddress = new ColissimoAddress((int) $id_address);
        $data = array(
            'order'              => $order,
            'products_detail'    => $productsDetail,
            'products_price'     => $productsPrice,
            'version'            => $this->module->version,
            'cart'               => new Cart((int) $order->id_cart),
            'customer'           => new Customer((int) $order->id_customer),
            'colissimo_order'    => $colissimoOrder,
            'colissimo_service'  => $colissimoService,
            'customer_addr'      => $customerAddress,
            'customer_addr_inv'  => $customerInvoiceAddress,
            'merchant_addr'      => $merchantAddress,
            'additional_address' => $colissimoAddress,
            'label_dimensions'   => array(
                'length' => Tools::getValue('colissimo_label_length_'.$idColissimoOrder),
                'width' => Tools::getValue('colissimo_label_width_'.$idColissimoOrder),
                'height' => Tools::getValue('colissimo_label_height_'.$idColissimoOrder),
            ),
            'label_articles_desc' => Tools::getValue('colissimo_articles_description_'.$idColissimoOrder),
            'cn23_category'       => Tools::getValue('colissimo_cn23_category_'.$idColissimoOrder),
            'form_options'        => array(
                'include_return'  => Tools::getValue('colissimo_return_label_'.$idColissimoOrder),
                'insurance'       => Tools::getValue('colissimo_insurance_'.$idColissimoOrder),
                'ta'              => Tools::getValue('colissimo_ta_'.$idColissimoOrder),
                'd150'            => Tools::getValue('colissimo_d150_'.$idColissimoOrder),
                'weight'          => $weight,
                'mobile_phone'    => Tools::getValue('colissimo_pickup_mobile_'.$idColissimoOrder),
                'postal_partner'  => Tools::getValue('colissimo_postal_partner_'.$idColissimoOrder),
                'ddp'             => $colissimoOrder->ddp,
            ),
        );

        return $data;
    }

    /**
     * @param int $idColissimoOrder
     * @param int $nbColumn
     * @return string
     * @throws Exception
     * @throws SmartyException
     */
    public function getAddressFormHtml($idColissimoOrder, $nbColumn)
    {
        $idAddress = ColissimoTools::getDeliveryAddressByIdColissimoOrder((int) $idColissimoOrder);
        $address = new Address((int) $idAddress);
        $fieldsValue = array(
            'id_address'   => $address->id,
            'lastname'     => $address->lastname,
            'firstname'    => $address->firstname,
            'company'      => $address->company,
            'address1'     => $address->address1,
            'address2'     => $address->address2,
            'postcode'     => $address->postcode,
            'city'         => $address->city,
            'id_state'     => $address->id_state,
            'id_country'   => $address->id_country,
            'phone'        => $address->phone,
            'phone_mobile' => $address->phone_mobile,
        );
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->module = $this->module;
        $helper->token = Tools::getAdminTokenLite('AdminColissimoAffranchissement');
        $helper->default_form_language = $this->context->language->id;
        $helper->submit_action = 'submitFixColissimoAddress';
        $helper->name_controller = 'colissimo-update-addr';
        $helper->tpl_vars = array(
            'fields_value' => $fieldsValue,
            'languages'    => $this->context->controller->getLanguages(),
            'id_language'  => $this->context->language->id,
        );
        $form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->module->l('Update address', 'AdminColissimoAffranchissementController'),
                    'icon'  => 'icon-cogs',
                ),
                'input'  => array(
                    array(
                        'type' => 'hidden',
                        'name' => 'id_address',
                    ),
                    array(
                        'type'      => 'text',
                        'label'     => $this->module->l('Firstname', 'AdminColissimoAffranchissementController'),
                        'name'      => 'firstname',
                        'maxchar'   => 29,
                        'maxlength' => 29,
                        'required'  => true,
                        'col'       => '5',
                    ),
                    array(
                        'type'      => 'text',
                        'label'     => $this->module->l('Lastname', 'AdminColissimoAffranchissementController'),
                        'name'      => 'lastname',
                        'maxchar'   => 35,
                        'maxlength' => 35,
                        'required'  => true,
                        'col'       => '6',
                    ),
                    array(
                        'type'      => 'text',
                        'label'     => $this->module->l('Company', 'AdminColissimoAffranchissementController'),
                        'name'      => 'company',
                        'maxchar'   => 35,
                        'maxlength' => 35,
                        'required'  => false,
                        'col'       => '6',
                    ),
                    array(
                        'type'      => 'text',
                        'label'     => $this->module->l('Address 1', 'AdminColissimoAffranchissementController'),
                        'name'      => 'address1',
                        'maxchar'   => 35,
                        'maxlength' => 35,
                        'col'       => '6',
                        'required'  => true,
                    ),
                    array(
                        'type'      => 'text',
                        'label'     => $this->module->l('Address 2', 'AdminColissimoAffranchissementController'),
                        'name'      => 'address2',
                        'maxchar'   => 35,
                        'maxlength' => 35,
                        'col'       => '6',
                        'required'  => false,
                    ),
                    array(
                        'type'     => 'text',
                        'label'    => $this->module->l('Postcode', 'AdminColissimoAffranchissementController'),
                        'name'     => 'postcode',
                        'col'      => '2',
                        'required' => true,
                    ),
                    array(
                        'type'      => 'text',
                        'label'     => $this->module->l('City', 'AdminColissimoAffranchissementController'),
                        'name'      => 'city',
                        'maxchar'   => 35,
                        'maxlength' => 35,
                        'col'       => '6',
                        'required'  => true,
                    ),
                    array(
                        'type'          => 'hidden',
                        'label'         => $this->module->l('Country', 'AdminColissimoAffranchissementController'),
                        'name'          => 'id_country',
                        'col'           => '4',
                        'default_value' => (int) $address->id,
                    ),
                    array(
                        'type'     => 'select',
                        'label'    => $this->module->l('State', 'AdminColissimoAffranchissementController'),
                        'name'     => 'id_state',
                        'required' => false,
                        'col'      => '4',
                        'options'  => array(
                            'query' => array(),
                            'id'    => 'id_state',
                            'name'  => 'name',
                        ),
                    ),
                    array(
                        'type'  => 'text',
                        'label' => $this->module->l('Phone', 'AdminColissimoAffranchissementController'),
                        'name'  => 'phone',
                        'col'   => '4',
                    ),
                    array(
                        'type'  => 'text',
                        'label' => $this->module->l('Mobile phone', 'AdminColissimoAffranchissementController'),
                        'name'  => 'phone_mobile',
                        'col'   => '4',
                    ),
                ),
                'submit' => array(
                    'title' => $this->module->l('Save', 'AdminColissimoAffranchissementController'),
                ),
            ),
        );
        $helperHtml = $helper->generateForm(array($form));
        $this->context->smarty->assign(
            array(
                'id_colissimo_order' => $idColissimoOrder,
                'nb_col'             => $nbColumn,
                'form_html'          => $helperHtml,
            )
        );

        return $this->createTemplate('_partials/address-form.tpl')
                    ->fetch();
    }

    /**
     * @param int $idColissimoOrder
     * @param int $nbColumn
     * @return string
     * @throws Exception
     */
    public function getOrderDetailsHtml($idColissimoOrder, $nbColumn, $step)
    {
        $colissimoOrder = new ColissimoOrder((int) $idColissimoOrder);
        $order = new Order((int) $colissimoOrder->id_order);
        $productsShippedQty = array();
        $orderDetails = $order->getOrderDetailList();
        foreach ($orderDetails as $orderDetail) {
            $shippedQuantity = ColissimoLabelProduct::getProductShippedQuantity($orderDetail['product_id'], $orderDetail['product_attribute_id'], $idColissimoOrder);
            $productsShippedQty[$orderDetail['product_id']][$orderDetail['product_attribute_id']] = $shippedQuantity;
            
        }
        $weightUnit = Configuration::get('PS_WEIGHT_UNIT');
        $orderTotals = array(
            'amount'      => $order->total_paid_tax_incl,
            'shipping'    => $order->total_shipping_tax_incl,
            'weight'      => $order->getTotalWeight(),
            'id_currency' => $order->id_currency,
            'weight_unit' => $weightUnit,
        );
        if ($colissimoOrder->ddp == 1){
            $this->context->smarty->assign(array('delivery_duty_paid' => (float) $colissimoOrder->ddp_cost));
        }
        $merchantAddress = new ColissimoMerchantAddress('sender');
        $customerAddress = new Address((int) $order->id_address_delivery);
        $isoTo = Country::getIsoById((int) $customerAddress->id_country);
        $needCN23 = 0;
        if (ColissimoTools::needCN23($merchantAddress->countryCode, $isoTo, $customerAddress->postcode)) {
            $this->context->smarty->assign(array('needCN23' => $needCN23));
        }
        $this->context->smarty->assign(
            array(
                'id_colissimo_order'   => $idColissimoOrder,
                'nb_col'               => $nbColumn,
                'order_details'        => $orderDetails,
                'order_totals'         => $orderTotals,
                'products_shipped_qty' => $productsShippedQty,
                'step'                 => $step,
            )
        );

        return $this->createTemplate('_partials/td-order-resume.tpl')
                    ->fetch();
    }
    
    /**
     * @return array
     * @throws Exception
    */
    public function processUploadCustomsDocumentsFiles($idLabel, $type)
    {
        try {
            if (!isset($_FILES[$type.'_'.$idLabel]['error']) || is_array($_FILES[$type.'_'.$idLabel]['error'])){
                throw new Exception($this->module->l('Invalid parameters.', 'AdminColissimoAffranchissementController'));
            }
            switch ($_FILES[$type.'_'.$idLabel]['error']) {
                case UPLOAD_ERR_OK:
                    break;
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    throw new Exception($this->module->l('Exceeded filesize limit.', 'AdminColissimoAffranchissementController'));
                default:
                    throw new Exception($this->module->l('Unknown errors.', 'AdminColissimoAffranchissementController'));
            }
            $fileType = $_FILES[$type.'_'.$idLabel]['type'];
            if ($fileType != 'application/pdf') {
                throw new Exception($this->module->l('You must submit only pdf files.', 'AdminColissimoAffranchissementController'));
            }
            $filename = $type.'_'.date('YmdHis').'.pdf';
            $destination = sys_get_temp_dir();
            if ($destination && Tools::substr($destination, -1) != DIRECTORY_SEPARATOR){
                $destination .= DIRECTORY_SEPARATOR;
            }
            $safeDestination = realpath($destination).DIRECTORY_SEPARATOR.$filename;
            if (!move_uploaded_file($_FILES[$type.'_'.$idLabel]['tmp_name'], $safeDestination)){
                throw new Exception($this->module->l('Cannot upload .pdf file.', 'AdminColissimoAffranchissementController'));
            }
            $file = array();
            $file['name'] = $type;
            $file['content'] = $safeDestination;
            
            return $file;
        } catch (Exception $e) {
            $this->module->logger->error($e->getMessage());
            $resultVars = array(
                'id_label'           => $idLabel,
                'error'              => true,
                'message'            => $e->getMessage(),
            );
        }
        $this->context->smarty->assign('result', $resultVars);
            $html = $this->createTemplate('_partials/td-affranchissement-custom-document-result.tpl')
                ->fetch();
            $return = array(
                  'result_html' => $html,
            );
            
        $this->ajaxDie(json_encode($return));
        die();
    }

    /**
     * @throws Exception
     */
    public function ajaxProcessOrderDetails()
    {
        $idColissimoOrder = Tools::getValue('id_colissimo_order');
        $nbColumn = Tools::getValue('nb_col');
        $step = Tools::getValue('step');
        $html = $this->getOrderDetailsHtml((int) $idColissimoOrder, $nbColumn, $step);
        $this->ajaxDie(
            json_encode(
                array(
                    'text' => 'ok',
                    'html' => $html,
                )
            )
        );
    }

    /**
     * @throws Exception
     * @throws SmartyException
     */
    public function ajaxProcessAddressDetails()
    {
        $idColissimoOrder = Tools::getValue('id_colissimo_order');
        $nbColumn = Tools::getValue('nb_col');
        $html = $this->getAddressFormHtml((int) $idColissimoOrder, $nbColumn);
        $this->ajaxDie(
            json_encode(
                array(
                    'text' => 'ok',
                    'html' => $html,
                )
            )
        );
    }

    /**
     * @throws Exception
     * @throws SmartyException
     */
    public function ajaxProcessAddressSave()
    {
        $idAddress = Tools::getValue('id_address');
        $address = new Address((int) $idAddress);
        $address->firstname = Tools::getValue('firstname');
        $address->lastname = Tools::getValue('lastname');
        $address->company = Tools::getValue('company');
        $address->address1 = Tools::getValue('address1');
        $address->address2 = Tools::getValue('address2');
        $address->postcode = Tools::getValue('postcode');
        $address->city = Tools::getValue('city');
        $address->id_country = (int) Tools::getValue('id_country');
        $address->id_state = (int) Tools::getValue('id_state');
        $address->phone = pSQL(Tools::getValue('phone'));
        $address->phone_mobile = pSQL(Tools::getValue('phone_mobile'));
        try {
            $address->save();
        } catch (Exception $e) {
            $this->module->logger->error($e->getMessage());
            $return = array(
                'error'   => true,
                'message' => $e->getMessage(),
            );
            $this->ajaxDie(json_encode($return));
        }
        $data = array(
            'delivery_addr'  => $address,
            'delivery_state' => ColissimoTools::getIsoStateById((int) $address->id_state),
            'address_valid'  => ColissimoTools::validateDeliveryAddress($address),
            'relais'         => 0,
        );
        $this->context->smarty->assign('order', $data);
        $html = $this->createTemplate('_partials/td-affranchissement-delivery-address.tpl')
                     ->fetch();

        $return = array(
            'error'   => false,
            'message' => $this->module->l('Address saved successfully.', 'AdminColissimoAffranchissementController'),
            'html'    => $html,
        );
        $this->ajaxDie(json_encode($return));
    }
    
    /**
     * @throws Exception
     * @throws SmartyException
     */
    public function ajaxProcessUpdateTotalweight()
    {
        $idColissimoOrder = (int) Tools::getValue('id_colissimo_order');
        $colissimoOrder = new ColissimoOrder((int) $idColissimoOrder);
        $order = new Order((int) $colissimoOrder->id_order);
        $orderDetails = $order->getOrderDetailList();
        $totalWeight = 0;
        foreach ($orderDetails as $orderDetail) {
            $quantity = (int) Tools::getValue('colissimo_orderBox_'.$idColissimoOrder.'_'.$orderDetail['product_id'].'_'.$orderDetail['product_attribute_id']);
            $productWeight = (float) $orderDetail['product_weight'] ? ColissimoTools::weightInKG($orderDetail['product_weight']) : 0.05;
            if ($quantity > 0) {
                $totalWeight += $productWeight * $quantity;
            }
        }
 
        $return = array(
            'error'   => false,
            'weight'    => $totalWeight,
        );
        $this->ajaxDie(json_encode($return));
    }

    public function checkEori($data)
    {
        $customerAddress = $data['customer_addr'];
        $isoTo = Country::getIsoById((int) $customerAddress->id_country);
        $productDetails = $data['products_detail'];
        $orderDetails = $data['order']->getOrderDetailList();
        $eori = Configuration::get('COLISSIMO_EORI_NUMBER');
        $eoriUk = '';
        $totalValue = 0;
        foreach ($orderDetails as $orderDetail) {
            if (isset($productDetails[$orderDetail['product_id']]) &&
                isset($productDetails[$orderDetail['product_id']][$orderDetail['product_attribute_id']])
            ) {
                $quantity = $productDetails[$orderDetail['product_id']][$orderDetail['product_attribute_id']];
                $unitPriceTaxExcl = $orderDetail['unit_price_tax_excl'];
                $totalValue += Tools::convertPrice($unitPriceTaxExcl * $quantity, $data['order']->id_currency, Currency::getIdByIsoCode('EUR'));
            }
        }
        if ($isoTo == 'GB' && $totalValue >= 1000){
            if (!Configuration::get('COLISSIMO_EORI_NUMBER') || !Configuration::get('COLISSIMO_EORI_NUMBER_UK')) {
                throw new Exception($this->module->l('Please fill both EORI & EORI UK numbers in module configuration.'));
            }
            $eoriUk = Configuration::get('COLISSIMO_EORI_NUMBER_UK');
        } elseif ($isoTo == 'GB' && $totalValue < 1000){
            if (!Configuration::get('COLISSIMO_EORI_NUMBER_UK')) {
                throw new Exception($this->module->l('Please fill EORI UK number in module configuration.'));
            }
            $eori = Configuration::get('COLISSIMO_EORI_NUMBER_UK');
        }

        $data['eori'] = $eori;
        $data['eoriUk'] = $eoriUk;
        $this->module->labelGenerator->setData($data);
    }

    /**
     * @throws Exception
     * @throws SmartyException
     */
    public function ajaxProcessGenerateLabel()
    {
        $data = $this->getPostData();
        $orderDetails = $data['order']->getOrderDetailList();
        try {
            $this->module->labelGenerator->setData($data);
            $this->checkEori($data);
            $colissimoLabel = $this->module->labelGenerator->generate();
        } catch (Exception $e) {
            $this->module->logger->error('Exception throw while generating label.', $e->getMessage());
            $resultVars = array(
                'id_colissimo_order' => $data['colissimo_order']->id,
                'label'              => array(
                    'id'      => false,
                    'error'   => true,
                    'message' => $e->getMessage(),
                    'cn23'    => false,
                ),
                'return_label'       => false,
            );
            $this->context->smarty->assign('result', $resultVars);
            $html = $this->createTemplate('_partials/td-affranchissement-result.tpl')
                         ->fetch();
            $return = array(
                'id_label'        => 0,
                'id_return_label' => 0,
                'order_weight'    => ColissimoTools::getOrderTotalWeightInKg($data['order']),
                'products' =>    $orderDetails,
                'result_html'     => $html,
            );
            $this->ajaxDie(json_encode($return));
            die();
        }

        /** @var Order $order */
        $order = $data['order'];
        $orderCarrier = ColissimoOrderCarrier::getByIdOrder($order->id);
        if (Validate::isLoadedObject($orderCarrier) && !$orderCarrier->tracking_number){
            $orderCarrier->tracking_number = pSQL($colissimoLabel->shipping_number);
            $orderCarrier->save();
            $hash = md5($order->reference.$order->secure_key);
            $link = $this->context->link->getModuleLink(
                'colissimo',
                'tracking',
                array('order_reference' => $order->reference, 'hash' => $hash),
                null,
                $order->id_lang,
                $order->id_shop
            );
            $isoLangOrder = Language::getIsoById($order->id_lang);
            if (isset($this->module->PNAMailObject[$isoLangOrder])){
                $object = $this->module->PNAMailObject[$isoLangOrder];
            } else {
                $object = $this->module->PNAMailObject['en'];
            }
            ColissimoTools::sendHandlingShipmentMail(
                $order,
                sprintf($object, $order->reference),
                $link
            );
            $this->module->logger->info('Send tracking mail for shipment '.$colissimoLabel->shipping_number);
        }
        if (Configuration::get('COLISSIMO_USE_SHIPPING_IN_PROGRESS')){
            $idShippingInProgressOS = Configuration::get('COLISSIMO_OS_SHIPPING_IN_PROGRESS');
            $shippingInProgressOS = new OrderState((int) $idShippingInProgressOS);
            if (Validate::isLoadedObject($shippingInProgressOS)){
                if (!$order->getHistory($this->context->language->id, (int) $idShippingInProgressOS)){
                    $history = new OrderHistory();
                    $history->id_order = (int) $order->id;
                    $history->changeIdOrderState($idShippingInProgressOS, (int) $order->id);
                    try {
                        $history->add();
                    } catch (Exception $e) {
                        $this->module->logger->error(sprintf('Cannot change status of order #%d', $order->id));
                    }
                }
            } else {
                $this->module->logger->error('Shipping in Progress order state is not valid');
            }
        }

        $resultVars = array(
            'id_colissimo_order' => $data['colissimo_order']->id,
            'label'              => array(
                'id'          => $colissimoLabel->id,
                'number'      => $colissimoLabel->shipping_number,
                'view'        => $colissimoLabel->label_format == 'pdf' ? true : false,
                'base64'      => base64_encode(Tools::file_get_contents($colissimoLabel->getFilePath())),
                'cn23_base64' => $colissimoLabel->cn23 ? base64_encode(
                    Tools::file_get_contents($colissimoLabel->getCN23Path())
                ) : '',
                'error'       => false,
                'message'     => '',
                'cn23'        => $colissimoLabel->cn23,
            ),
            'return_label'       => false,
        );
        if ($data['form_options']['include_return']) {
            $customerCountry = $data['customer_addr']->id_country;
            $returnDestinationType = ColissimoTools::getReturnDestinationTypeByIdCountry($customerCountry);
            if ($returnDestinationType === false){
                //@formatter:off
                $this->module->logger->error(
                    $this->module->l('You cannot edit return label to this destination.', 'AdminColissimoAffranchissementController')
                );
                $resultVars['return_label'] = array(
                    'id'      => false,
                    'error'   => true,
                    'message' => $this->module->l('You cannot edit return label to this destination.', 'AdminColissimoAffranchissementController'),
                    'cn23'    => false,
                );
                //@formatter:on
            } else {
                $idService = ColissimoService::getServiceIdByIdCarrierDestinationType(0, $returnDestinationType);
                $data['colissimo_service_initial'] = $data['colissimo_service'];
                $data['colissimo_service'] = new ColissimoService((int) $idService);
                $data['merchant_addr'] = ColissimoMerchantAddress::getMerchantReturnAddress();
                try {
                    $this->module->labelGenerator->setData($data);
                    $colissimoReturnLabel = $this->module->labelGenerator->generateReturn($colissimoLabel);
                } catch (Exception $e) {
                    $this->module->logger->error('Exception throw while generating return label.', $e->getMessage());
                    $resultVars['return_label'] = array(
                        'id'      => false,
                        'error'   => true,
                        'message' => $e->getMessage(),
                        'cn23'    => false,
                    );
                }
            }
        }
        $needDocuments = false;
        $idColissimoOrder = $data['colissimo_order']->id;
        if ($this->needCustomsDocuments($idColissimoOrder)){
            $needDocuments = true;
        }
        $return = array(
            'id_label'     => $colissimoLabel->id,
            'order_weight' => ColissimoTools::getOrderTotalWeightInKg($order),
            'products' =>    $orderDetails,
            'show_customs_documents'  => $needDocuments,
        );

        if (isset($colissimoReturnLabel)){
            $resultVars['return_label'] = array(
                'id'          => $colissimoReturnLabel->id,
                'number'      => $colissimoReturnLabel->shipping_number,
                'error'       => false,
                'base64'      => base64_encode(Tools::file_get_contents($colissimoReturnLabel->getFilePath())),
                'cn23_base64' => $colissimoReturnLabel->cn23
                    ? base64_encode(Tools::file_get_contents($colissimoReturnLabel->getCN23Path()))
                    : '',
                'message'     => '',
                'cn23'        => $colissimoReturnLabel->cn23,
            );
            $return['id_return_label'] = $colissimoReturnLabel->id;
        }
        $this->context->smarty->assign('result', $resultVars);
        $html = $this->createTemplate('_partials/td-affranchissement-result.tpl')
                     ->fetch();
        $return['result_html'] = $html;
        $this->ajaxDie(json_encode($return));
    }

    /**
     * @throws Exception
     * @throws SmartyException
     */
    public function ajaxProcessDisplayResult()
    {
        $summary = array();
        $labelIds = json_decode(Tools::getValue('label_ids'), true);
        $needDocuments = false;
        foreach ($labelIds as $labelId) {
            $label = new ColissimoLabel((int) $labelId);
            if ($label->return_label){
                $summary['return_label'][] = $label->id;
            } else {
                $summary['label'][] = $label->id;
            }
            if ($label->cn23){
                $summary['cn23'][] = $label->id;
            }
            $idColissimoOrder = (int) $label->id_colissimo_order;
            if ($this->needCustomsDocuments($idColissimoOrder)){
                $needDocuments = true;
            }
        }
        $this->context->smarty->assign(
            array(
                'input_label'        => isset($summary['label']) ? json_encode($summary['label']) : false,
                'input_return_label' => isset($summary['return_label']) ? json_encode($summary['return_label']) : false,
                'input_cn23'         => isset($summary['cn23']) ? json_encode($summary['cn23']) : false,
                'need_documents'     => $needDocuments,
            )
        );
        $html = $this->createTemplate('_partials/download-result.tpl')
                     ->fetch();
        $return = array(
            'result_html' => $html,
            'labels_ids' => isset($summary['label']) ? json_encode($summary['label']) : array(),
        );
        $this->ajaxDie(json_encode($return));
    }

    /**
     * @throws PrestaShopDatabaseException
     */
    public function ajaxProcessPurgeDocuments()
    {
        $this->module->logger->setChannel('FilesManagement');
        $limit = Configuration::getGlobalValue('COLISSIMO_FILES_LIMIT');
        $docsLifetime = Configuration::getGlobalValue('COLISSIMO_FILES_LIFETIME');
        $dirDetails = ColissimoTools::getDocumentsDirDetails(dirname(__FILE__).'/../../documents/');
        $deleted = 0;
        if ($dirDetails['count'] > $limit){
            $filesToDelete = $this->getFilesToDelete($docsLifetime);
            if (!$filesToDelete['total']){
                //@formatter:off
                $this->ajaxDie(json_encode(array(
                    'error'   => false,
                    'message' => $this->module->l('There are no documents to delete.', 'AdminColissimoAffranchissementController'),
                )));
                //@formatter:on
            }
            foreach ($filesToDelete['labels'] as $labelId) {
                try {
                    $label = new ColissimoLabel((int) $labelId);
                    if ($label->deleteFile()){
                        $deleted++;
                        $label->file_deleted = 1;
                        $label->save();
                    }
                } catch (Exception $e) {
                    $this->module->logger->error($e->getMessage());
                    continue;
                }
            }
            foreach ($filesToDelete['deposit_slips'] as $depositSlipId) {
                try {
                    $depositSlip = new ColissimoDepositSlip((int) $depositSlipId);
                    if ($depositSlip->deleteFile()){
                        $deleted++;
                        $depositSlip->file_deleted = 1;
                        $depositSlip->save();
                    }
                } catch (Exception $e) {
                    $this->module->logger->error($e->getMessage());
                    continue;
                }
            }
        }
        if (!$deleted){
            //@formatter:off
            $return = array(
                'error'   => true,
                'message' => $this->module->l('No files were deleted. Please check the module logs.', 'AdminColissimoAffranchissementController'),
            );
            //@formatter:on
            $this->ajaxDie(json_encode($return));
        }

        $return = array(
            'error'   => false,
            'message' => sprintf(
                $this->module->l('%d files were deleted successfully', 'AdminColissimoAffranchissementController'),
                $deleted
            ),
        );
        $this->ajaxDie(json_encode($return));
    }
    
    /**
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     * @throws SmartyException
     */
    public function ajaxProcessSendCustomsDocuments()
    {
        $idLabel = Tools::getValue('id_label');
        $colissimoLabel = new ColissimoLabel((int) $idLabel);
        $colissimoOrder = new ColissimoOrder((int) $colissimoLabel->id_colissimo_order);
        $order = new Order((int) $colissimoOrder->id_order);
        $requests = array();
//        $cn23_content = base64_encode(Tools::file_get_contents($colissimoLabel->getCN23Path()));
//        $cn23file = array(
//            'name'    => 'cn23',
//            'content' => $cn23_content
//        );
//        $requests[] = $cn23file;
        if (Tools::getValue('colissimo_prestashop_invoice_'.$colissimoLabel->id_colissimo_order)){
            $order_invoice_list = $order->getInvoicesCollection();
            $pdfInvoice = new PDF($order_invoice_list, PDF::TEMPLATE_INVOICE, Context::getContext()->smarty);
            $destination = sys_get_temp_dir();
            if ($destination && Tools::substr($destination, -1) != DIRECTORY_SEPARATOR){
                $destination .= DIRECTORY_SEPARATOR;
            }
            $invoicePath = realpath($destination).DIRECTORY_SEPARATOR.'invoice_'.date('YmdHis').'.pdf';
            file_put_contents($invoicePath, $pdfInvoice->render(false));
            $pdfContent = base64_encode(Tools::file_get_contents($invoicePath));
            $pdfFile = array(
                'name'    => 'COMMERCIAL_INVOICE',
                'content' => $pdfContent
            );
            $requests[] = $pdfFile;
        } else {
            if ($_FILES['invoice_'.$idLabel]['tmp_name']) {
                $request = $this->processUploadCustomsDocumentsFiles($idLabel, 'invoice');
                $request['name'] = 'COMMERCIAL_INVOICE';
                $requests[] = $request;
            }
        }
        if (isset($_FILES['administrative_doc_'.$idLabel]) && $_FILES['administrative_doc_'.$idLabel]['tmp_name']) {
            $request = $this->processUploadCustomsDocumentsFiles($idLabel, 'administrative_doc');
            $request['name'] = 'OTHER';
            $requests[] = $request;
        }
        $contractNumbers = Configuration::getMultiShopValues('COLISSIMO_ACCOUNT_LOGIN');
        /* create documents */
        foreach ($requests as $request) {
            $customDocumentRequest = new ColissimoCreateCustomDocumentRequest(ColissimoTools::getCredentials($order->id_shop));
            $customDocumentRequest->setAccountNumber($contractNumbers[(int) $order->id_shop])
                ->setParcelNumber($colissimoLabel->shipping_number)
                ->setDocumentType($request['name'])
                ->setFile($request['content'])
                ->setFilename($request['name'])
                ->buildRequest();
            $client = new ColissimoClient();
            $client->setRequest($customDocumentRequest);
            try {
                /** @var ColissimoCreateCustomDocumentResponse $response */
                $response = $client->request();
                if ($response->documentId) {
                    $customDocument = new ColissimoCustomDocument();
                    $customDocument->id_colissimo_label = (int) $idLabel;
                    $customDocument->id_colissimo_order = (int) $colissimoLabel->id_colissimo_order;
                    $customDocument->id_document = pSQL($response->documentId);
                    $customDocument->type = $request['name'];
                    $customDocument->save();
                } else {
                    $message = $response->errors[0]['message'];
                    throw new Exception(sprintf('error %s : %s',$response->errorCode, $message));
                }
            } catch (Exception $e) {
                $this->module->logger->error('Exception throw while creating custom document for label '.$colissimoLabel->shipping_number.': '.$e->getMessage(), ['trace' => $e->getTraceAsString(), 'line' => $e->getLine(), 'file' => $e->getFile()]);
                $resultVars = array(
                    'id_label' => $idLabel,
                    'error'    => true,
                    'message' => $e->getMessage(),
                );
                $this->context->smarty->assign('result', $resultVars);
                $html = $this->createTemplate('_partials/td-affranchissement-custom-document-result.tpl')
                     ->fetch();
                $return = array(
                  'result_html' => $html,
                );
                $this->ajaxDie(json_encode($return));
                die();
            }
        }
        $resultVars = array(
            'id_label' => $idLabel,
            'error'    => false,
        );
        $this->context->smarty->assign('result', $resultVars);
            $html = $this->createTemplate('_partials/td-affranchissement-custom-document-result.tpl')
                ->fetch();
            $return = array(
                  'result_html' => $html,
            );
        $this->ajaxDie(json_encode($return));
        die();
    }

    /**
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     * @throws SmartyException
     */
    public function ajaxProcessLoadServiceUpdateModal()
    {
        $idOrder = Tools::getValue('id_order');
        $order = new Order($idOrder);
        $deliveryAddr = new Address((int) $order->id_address_delivery);
        $eligibleServices = $this->module->getEligibleServiceByOrder($order);
        $widgetToken = $this->module->getWidgetToken();
        $this->context->smarty->assign(
            array(
                'id_order'               => $idOrder,
                'colissimo_services'     => $eligibleServices,
                'colissimo_widget_token' => $widgetToken,
                'preparation_time'       => Configuration::get('COLISSIMO_ORDER_PREPARATION_TIME'),
                'colissimo_widget_lang'  => $this->context->language->iso_code,
                'delivery_addr'          => array(
                    'address'     => $deliveryAddr->address1,
                    'zipcode'     => $deliveryAddr->postcode,
                    'city'        => $deliveryAddr->city,
                    'iso_country' => Country::getIsoById($deliveryAddr->id_country),
                ),
            )
        );
        $return = array(
            'error'       => false,
            'result_html' => $this->createTemplate('_partials/modal-service-selection.tpl')
                                  ->fetch(),
        );
        $this->ajaxDie(json_encode($return));
    }

    /**
     * @throws PrestaShopException
     * @throws SmartyException
     */
    public function ajaxProcessSelectPickupPoint()
    {
        $infoPoint = json_decode(Tools::getValue('infoPoint'), true);
        $colissimoId = $infoPoint['colissimo_id'];
        $pickupPoint = ColissimoPickupPoint::getPickupPointByIdColissimo($colissimoId);
        $pickupPoint->hydrate(array_map('pSQL', $infoPoint));
        try {
            $pickupPoint->save();
        } catch (Exception $e) {
            $this->module->logger->error($e->getMessage());
            //@formatter:off
            $this->context->smarty->assign(
                'colissimo_pickup_point_error',
                $this->module->l('An unexpected error occurred. Please refresh the window.', 'AdminColissimoAffranchissementController')
            );
            //@formatter:on
            $tpl = $this->module->getTemplatePath(
                'front/_partials/pickup-point-address.tpl'
            );
            $html = $this->context->smarty->fetch($tpl);
            $this->ajaxDie(json_encode(array('html_result' => $html)));
        }
        $this->context->smarty->assign(
            array(
                'pickup_point' => array(
                    'colissimo_id' => $pickupPoint->colissimo_id,
                    'id'           => $pickupPoint->id,
                    'company_name' => $pickupPoint->company_name,
                    'address1'     => $pickupPoint->address1,
                    'zipcode'      => $pickupPoint->zipcode,
                    'city'         => $pickupPoint->city,
                    'country'      => $pickupPoint->country,
                ),
            )
        );

        $html = $this->createTemplate('_partials/modal-service-selection-address.tpl')
                     ->fetch();

        $return = array(
            'result_html' => $html,
            'error'       => false,
        );
        $this->ajaxDie(json_encode($return));
    }

    /**
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     * @throws SmartyException
     */
    public function ajaxProcessValidateServiceUpdate()
    {
        $idOrder = Tools::getValue('upd_id_order');
        try {
            $this->module->postProcessColissimoValidateService($idOrder);
        } catch (Exception $e) {
            $this->module->logger->error($e->getMessage());
            $return = array(
                'error'   => true,
                'message' => $e->getMessage(),
            );
            $this->ajaxDie(json_encode($return));
        }
        $order = new Order($idOrder);
        $idColissimoOrder = ColissimoOrder::exists($idOrder);
        $colissimoOrder = new ColissimoOrder((int) $idColissimoOrder);
        $colissimoService = new ColissimoService((int) $colissimoOrder->id_colissimo_service);
        $deliveryAddr = new Address($order->id_address_delivery);
        $insurance = -1;
        if ($colissimoService->isInsurable(Country::getIsoById($deliveryAddr->id_country))) {
            if (Configuration::get('COLISSIMO_INSURE_SHIPMENTS')) {
                $insurance = 1;
            } else {
                $insurance = 0;
            }
        }
        $isoOutreMerFtd = ColissimoTools::$isoOutreMer;
        if (($key = array_search('YT', $isoOutreMerFtd)) !== false) {
            unset($isoOutreMerFtd[$key]);
        }
        if (($key = array_search('PM', $isoOutreMerFtd)) !== false) {
            unset($isoOutreMerFtd[$key]);
        }
        $productCode = $colissimoService->product_code;
        $isoCustomerAddr = Country::getIsoById((int) $deliveryAddr->id_country);
        $ftd = in_array($isoCustomerAddr, $isoOutreMerFtd) && $productCode == 'CDS' ? 1 : 0;
        $data = array(
            'id_order'       => $idOrder,
            'address_valid'  => ColissimoTools::validateDeliveryAddress($deliveryAddr),
            'delivery_addr'  => $deliveryAddr,
            'delivery_state' => ColissimoTools::getIsoStateById($deliveryAddr->id_state),
            'relais'         => $colissimoService->type === ColissimoService::TYPE_RELAIS,
            'service'        => $colissimoService->commercial_name,
            'insurance'      => $insurance,
            'ftd'            => $ftd,
        );

        $this->context->smarty->assign(array('order' => $data, 'key' => $idColissimoOrder));
        $htmlAddress = $this->createTemplate('_partials/td-affranchissement-delivery-address.tpl')->fetch();
        $htmlService = $this->createTemplate('_partials/td-affranchissement-service.tpl')->fetch();
        $htmlInsurance = $this->createTemplate('_partials/td-affranchissement-insurance.tpl')->fetch();
        $htmlFtd = $this->createTemplate('_partials/td-affranchissement-ftd.tpl')->fetch();

        $return = array(
            'error' => false,
            'html_address' => $htmlAddress,
            'html_service' => $htmlService,
            'html_insurance' => $htmlInsurance,
            'html_ftd' => $htmlFtd,
            'order' => $data,
        );
        $this->ajaxDie(json_encode($return));
    }
}
