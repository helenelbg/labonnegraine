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
 * Class AdminColissimoCustomsDocumentsController
 *
 * Ajax processes:
 *  - orderDetails
 *
 */
class AdminColissimoCustomsDocumentsController extends ModuleAdminController
{
    /** @var Colissimo $module */
    public $module;

    /** @var string $header */
    private $header;

    /**
     * AdminColissimoCustomsDocumentsController constructor.
     * @throws PrestaShopException
     */
    public function __construct()
    {
        $this->bootstrap = true;
        parent::__construct();
        $this->module->logger->setChannel('CustomsDocuments');
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
     * @throws Exception
     * @throws SmartyException
     */
    public function initProcess()
    {
        $tmpDirectory = sys_get_temp_dir();
        if ($tmpDirectory && Tools::substr($tmpDirectory, -1) != DIRECTORY_SEPARATOR) {
            $tmpDirectory .= DIRECTORY_SEPARATOR;
        }
        $tmpDirectory = realpath($tmpDirectory);
        if (!is_writable($tmpDirectory)) {
            //@formatter:off
            $this->errors[] = sprintf($this->module->l('Please grant write permissions to the temporary directory of your server (%s).', 'AdminColissimoCustomsDocumentsController'), $tmpDirectory);
            //@formatter:on
        }
        $this->header = $this->module->setColissimoControllerHeader();
        $this->initCustomsDocuments();
        parent::initProcess();
    }

    /**
     * @param bool $isNewTheme
     */
    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        $this->addJS($this->module->getLocalPath().'views/js/colissimo.customs.documents.js');
    }

    /**
     * @throws Exception
     * @throws SmartyException
     */
    public function initContent()
    {
        $this->content = $this->header.$this->content;
        parent::initContent();
    }
    
    /**
     * 
     */
    public function postProcess()
    {
        parent::postProcess();
        if (Tools::isSubmit('submitProcessColissimoCustomsDocuments')) {
            $this->sendCustomsDocumentsProcess();
            $this->initCustomsDocuments();
        }
    }
    
    /**
     * @throws Exception
     * @throws PrestaShopDatabaseException
     * @throws SmartyException
    */
    public function initCustomsDocuments()
    {
        $ids = $this->getAllOrdersToProcess();
        $data = array();
        foreach ($ids as $id) {
            $colissimoOrder = new ColissimoOrder((int) $id);
            $labelIds = $colissimoOrder->getLabelIds(false);
            $shipments = array();
            foreach ($labelIds as $labelId) {
                $label = new ColissimoLabel((int) $labelId);
                $labelDocuments = $label->getRelatedCustomsDocuments();
                $shipments[$label->id]['id_label'] = $label->id;
                $shipments[$label->id]['shipping_number'] = $label->shipping_number;
                $shipments[$label->id]['documents'] = $labelDocuments;
            }  
       
            $order = new Order((int) $colissimoOrder->id_order);
            $data[$id] = array(
                'id_order'           => $order->id,
                'id_colissimo_order' => (int) $id,
                'reference'          => $order->reference,
                'amount'             => $order->total_paid_tax_incl,
                'shipments'          => $shipments,
                'has_invoice'        => $order->invoice_number ? true : false,
               
            );
            $orderDetails = $order->getOrderDetailList();
            $data[$id]['products'] = $orderDetails;
        }

        $this->context->smarty->assign(
            array('orders' => $data, 'img_path' => $this->module->getPathUri().'views/img/')
        );

        $this->content = $this->createTemplate('customs-documents-form.tpl')
                              ->fetch();
    }
    
    /**
     * @throws Exception
    */
    public function sendCustomsDocumentsProcess()
    {
        $colissimoOrders = Tools::getValue('colissimo_orderBox');
        if (!$colissimoOrders || empty($colissimoOrders)) {
            $this->errors[] = $this->module->l('Please select at least one order.', 'AdminColissimoCustomsDocumentsController');
            return;
        }
        foreach($colissimoOrders as $idcolissimoOrder){
            $colissimoOrder = new ColissimoOrder((int) $idcolissimoOrder); 
            $order = new Order ((int) $colissimoOrder->id_order);
            $labelIds = $colissimoOrder->getLabelIds(false);
            foreach ($labelIds as $labelId){
                $colissimoLabel = new ColissimoLabel((int) $labelId);
                $requests = array();
                if ( Tools::getValue('colissimo_prestashop_invoice_'.$idcolissimoOrder)){
                    $order_invoice_list = $order->getInvoicesCollection();
                    $pdfInvoice = new PDF($order_invoice_list, PDF::TEMPLATE_INVOICE, Context::getContext()->smarty);
                    $destination = sys_get_temp_dir();
                    if ($destination && Tools::substr($destination, -1) != DIRECTORY_SEPARATOR) {
                        $destination .= DIRECTORY_SEPARATOR;
                    }
                    $invoicePath = realpath($destination).DIRECTORY_SEPARATOR.'invoice_'.$idcolissimoOrder.'.pdf';
                    if (!file_exists($invoicePath)) {
                       file_put_contents($invoicePath, $pdfInvoice->render(false));
                    }
                    $pdfContent = base64_encode(Tools::file_get_contents($invoicePath));
                    $pdfFile = array(
                        'name'    => 'COMMERCIAL_INVOICE',
                        'content' => $pdfContent
                    );
                    $requests[] = $pdfFile;
                } else { 
                    if(isset($_FILES['invoice_'.$labelId])){
                        if($_FILES['invoice_'.$labelId]['tmp_name']){
                            $request = $this->processUploadCustomsDocumentsFiles($labelId, 'invoice');
                            $request['name'] = 'COMMERCIAL_INVOICE';
                            $requests[] = $request;
                        }
                    }
                }
                if(isset($_FILES['administrative_doc_'.$labelId])){
                    if($_FILES['administrative_doc_'.$labelId]['tmp_name']){
                       $request =  $this->processUploadCustomsDocumentsFiles($labelId, 'administrative_doc');
                       $request['name'] = 'OTHER';
                       $requests[] = $request;
                    }
                }
                $labelDocuments = $colissimoLabel->getRelatedCustomsDocuments();
                $contractNumbers = Configuration::getMultiShopValues('COLISSIMO_ACCOUNT_LOGIN');
                foreach ($requests as $request){
                    if (isset($labelDocuments[$request['name']])){
                       $customDocumentRequest = new ColissimoUpdateCustomDocumentRequest(ColissimoTools::getCredentials($order->id_shop));
                    } else {
                       $customDocumentRequest = new ColissimoCreateCustomDocumentRequest(ColissimoTools::getCredentials($order->id_shop));
                    }
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
                        $this->module->logger->info('Create document Response', ['response' => $response]);
                        if ($response->documentId) {
                            $idDocument = ColissimoCustomDocument::getCustomDocumentId($labelId, $request['name']);
                            $customDocument = new ColissimoCustomDocument((int) $idDocument);
                            $customDocument->id_colissimo_label = (int) $labelId;
                            $customDocument->id_colissimo_order = (int) $colissimoLabel->id_colissimo_order;
                            $customDocument->id_document = $response->documentId;
                            $customDocument->type = $request['name'];
                            $customDocument->save();
                        } else {
                            $message = $response->errors[0]['message'];
                            $this->errors[] = sprintf($this->module->l('label %s : failed to send %s, %s', 'AdminColissimoCustomsDocumentsController'), $colissimoLabel->shipping_number, $request['name'],$message);
                        }
                    } catch (Exception $e) {
                        $this->module->logger->error('Exception throw while creating custom document for label : '.$e->getMessage());
                        $this->errors[] = sprintf($this->module->l('label %s Exception throw while creating custom document.', 'AdminColissimoCustomsDocumentsController'), $colissimoLabel->shipping_number);
                
                    } 
                }
            }
        }
        
        $this->confirmations[]  = sprintf($this->module->l('All selected orders have been processed', 'AdminColissimoCustomsDocumentsController'), $colissimoLabel->shipping_number);
    }
    
    /**
     * @return array
     * @throws Exception
    */
    public function processUploadCustomsDocumentsFiles($idLabel, $type)
    {
        $label = new ColissimoLabel((int) $idLabel); 
        $uploadErreur = false;
        $file = array();
        try {
            if (!isset($_FILES[$type.'_'.$idLabel]['error']) || is_array($_FILES[$type.'_'.$idLabel]['error'])) {
                $this->errors[] = $this->module->l('Invalid parameters', 'AdminColissimoCustomsDocumentsController');
                $uploadErreur = true;
            }
            switch ($_FILES[$type.'_'.$idLabel]['error']) {
                case UPLOAD_ERR_OK:
                    break;
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    $this->errors[] = $this->module->l('Exceeded filesize limit.', 'AdminColissimoCustomsDocumentsController');
                    $uploadErreur = true;
                default:
                    $this->errors[] = $this->module->l('Unknown errors.', 'AdminColissimoCustomsDocumentsController');
                    $uploadErreur = true;
            }
            $fileType = $_FILES[$type.'_'.$idLabel]['type'];
            if ($fileType != 'application/pdf') {
                $this->errors[] = sprintf($this->module->l('label %s : You must submit only pdf files.', 'AdminColissimoCustomsDocumentsController'), $label->shipping_number);
                $uploadErreur = true;
            }
            $filename = $type.'_'.$idLabel.'_'.date('YmdHis').'.pdf';
            $destination = sys_get_temp_dir();
            if ($destination && Tools::substr($destination, -1) != DIRECTORY_SEPARATOR) {
                $destination .= DIRECTORY_SEPARATOR;
            }
            $safeDestination = realpath($destination).DIRECTORY_SEPARATOR.$filename;
            if(!$uploadErreur){
                if (!move_uploaded_file($_FILES[$type.'_'.$idLabel]['tmp_name'], $safeDestination)) {
                    $this->errors[] = $this->module->l('Cannot upload .pdf file.', 'AdminColissimoCustomsDocumentsController');
                }
                $file['name'] = $type;
                $file['content'] = $safeDestination;
            
                return $file;
            }
        } catch (Exception $e) {
            $this->module->logger->error($e->getMessage());
            $this->errors[] =  $e->getMessage();
        }
    }
    
     /**
     * @return array
     * @throws PrestaShopDatabaseException
     */
    public function getAllOrdersToProcess()
    {
        $idStatusShippingInProgress = (int) Configuration::get('COLISSIMO_OS_SHIPPING_IN_PROGRESS');
        $dbQuery = new DbQuery();
        //@formatter:off
        $dbQuery->select('co.`id_colissimo_order`')
                ->from('colissimo_order', 'co')
                ->leftJoin('orders', 'o', 'o.`id_order` = co.`id_order`')
                ->leftJoin('colissimo_label', 'cola', 'cola.`id_colissimo_order` = co.`id_colissimo_order`');
        //@formatter:on
        $dbQuery->where('o.`current_state` = '. (int) $idStatusShippingInProgress);
        $dbQuery->where('co.hidden = 0');
        $dbQuery->orderBy('o.date_add DESC');
        $results = Db::getInstance(_PS_USE_SQL_SLAVE_)
                     ->executeS($dbQuery);
        foreach ($results as $key => $result){
            $colissimoOrder = new ColissimoOrder((int) $result['id_colissimo_order']);
            $order = new Order((int) $colissimoOrder->id_order);
            $deliveryAddr = new Address((int) $order->id_address_delivery);
            $iso = Country::getIsoById($deliveryAddr->id_country);
            $destinations = array_merge(ColissimoTools::$isoDomDDP, ColissimoTools::$isoDom);
            if (!in_array($iso, $destinations)) {
                unset($results[$key]);
            }
            if (in_array($iso, ColissimoTools::$isoDomDDP) && !Configuration::get('COLISSIMO_ENABLE_DDP')) {
                unset($results[$key]);
            }
        }

        return array_map(
            function ($element) {
                return $element['id_colissimo_order'];
            },
            $results
        );
    }
   
    /**
     * @throws Exception
     * @throws SmartyException
     */
    public function ajaxProcessOrderDetails()
    {
        $idColissimoOrder = Tools::getValue('id_colissimo_order');
        $nbColumn = Tools::getValue('nb_col');
        $colissimoOrder = new ColissimoOrder((int) $idColissimoOrder);
        $order = new Order((int) $colissimoOrder->id_order);
        $productsShippedQty = array();
        $productsRelatedLabels = array();
        $orderDetails = $order->getOrderDetailList();
        foreach ($orderDetails as $orderDetail) {
            $shippedQuantity = ColissimoLabelProduct::getProductShippedQuantity($orderDetail['product_id'], $orderDetail['product_attribute_id'], $idColissimoOrder);
            $productsShippedQty[$orderDetail['product_id']][$orderDetail['product_attribute_id']] = $shippedQuantity;
            $relatedLabels = ColissimoLabelProduct::getRelatedLabels($orderDetail['product_id'], $orderDetail['product_attribute_id'], $idColissimoOrder);
            $productsRelatedLabels[$orderDetail['product_id']][$orderDetail['product_attribute_id']] = $relatedLabels;
        }
        $weightUnit = Configuration::get('PS_WEIGHT_UNIT');
        $orderTotals = array(
            'amount'      => $order->total_paid_tax_incl,
            'shipping'    => $order->total_shipping_tax_incl,
            'weight'      => $order->getTotalWeight(),
            'id_currency' => $order->id_currency,
            'weight_unit' => $weightUnit,
        );
        $this->context->smarty->assign(
            array(
                'id_colissimo_order'      => $idColissimoOrder,
                'nb_col'                  => $nbColumn,
                'order_details'           => $orderDetails,
                'order_totals'            => $orderTotals,
                'products_shipped_qty'    => $productsShippedQty,
                'products_related_labels' => $productsRelatedLabels,
            )
        );
        $html = $this->createTemplate('_partials/td-order-resume.tpl')->fetch();
        $this->ajaxDie(
            json_encode(
                array(
                    'text' => 'ok',
                    'html' => $html,
                )
            )
        );
    }
  
   
}
