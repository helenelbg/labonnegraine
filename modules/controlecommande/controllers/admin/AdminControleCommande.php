<?php
/**
 * @author Anjouweb
 * */
require_once _PS_MODULE_DIR_ . 'controlecommande/controlecommande.php';

class AdminControleCommandeController extends ModuleAdminController
{

    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'orders_control';
        $this->className = 'AdminControleCommandeController';
        $this->list_simple_header = false;
        $this->context = Context::getContext();

        $this->fields_list = array(
            'id_order_control' => array(
                'title' => $this->l('Id control'),
                'align' => 'left',
                'type' => 'int',
                'filter_key' => 'a!id_order_control',
                'filter_type' => 'int'
            ),
            'id_order' => array(
                'title' => $this->l('id Order'),
                'align' => 'left',
                'type' => 'int',
                'filter_key' => 'a!id_order',
                'filter_type' => 'int'
            ),
            'reference' => array(
                'title' => $this->l('Order Reference'),
                'align' => 'left',
                'type' => 'varchar',
                'filter_key' => 'o!reference',
                'filter_type' => 'varchar',
                'search' => true,
                'orderby' => true,
            ),
            'validate' => array(
                'title' => $this->l('Validate'),
                'align' => 'left',
                'type' => 'bool',
                'filter_key' => 'a!validate',
                'filter_type' => 'bool',
            ),
            'total_prepared' => array(
                'title' => $this->l('Total prepared'),
                'align' => 'left',
                'type' => 'int',
                'filter_key' => 'total_prepared',
                'filter_type' => 'int'
            ),
            'total_ordered' => array(
                'title' => $this->l('Total ordered'),
                'align' => 'left',
                'type' => 'int',
                'filter_key' => 'total_ordered',
                'filter_type' => 'int'
            ),
            'lastname' => array(
                'title' => $this->l('Emp. lastname'),
                'align' => 'left',
                'type' => 'varchar',
                'filter_key' => 'emp!lastname',
                'filter_type' => 'varchar'
            ),
            'firstname' => array(
                'title' => $this->l('Emp. firstname'),
                'align' => 'left',
                'type' => 'varchar',
                'filter_key' => 'emp!firstname',
                'filter_type' => 'varchar'
            ),
            'email' => array(
                'title' => $this->l('Emp. email'),
                'align' => 'left',
                'type' => 'varchar',
                'filter_key' => 'emp!email',
                'filter_type' => 'varchar'
            ),
            'state' => array(
                'title' => $this->l('etat'),
                'align' => 'left',
                'type' => 'varchar',
            ),
            'date_order' => array(
                'title' => $this->l('Date order'),
                'align' => 'left',
                'type' => 'date',
                'filter_key' => 'o!date_add',
                'filter_type' => 'date'
            ),
            'date_add' => array(
                'title' => $this->l('Date control'),
                'align' => 'left',
                'type' => 'date',
                'filter_key' => 'o!date_add',
                'filter_type' => 'date',
            ),
            'date_validation' => array(
                'title' => $this->l('Date validation'),
                'align' => 'left',
                'filter_key' => 'o!date_validation',
                'filter_type' => 'date',
                'type' => 'date'
            ),
        );

        $this->table = 'order_control';
        $this->_select = ' a.*, emp.lastname, emp.firstname, emp.email, o.reference, o.date_add as date_order, osl.name as state, '
            . '(SELECT SUM(quantity_prepared) FROM `' . _DB_PREFIX_ . 'order_products_control` opc WHERE a.id_order_control = opc.id_order_control) as total_prepared, '
            . '(SELECT SUM(product_quantity) FROM `' . _DB_PREFIX_ . 'order_detail` od WHERE a.id_order = od.id_order) as total_ordered ';
        $this->_join = 'LEFT JOIN `' . _DB_PREFIX_ . 'employee` emp ON a.id_employee = emp.id_employee '
            . 'LEFT JOIN `' . _DB_PREFIX_ . 'orders` o ON a.id_order = o.id_order '
            . ' LEFT JOIN `' . _DB_PREFIX_ . 'order_state_lang` osl ON o.current_state = osl.id_order_state AND osl.id_lang = ' . $this->context->language->id . ' ';
        $this->_group = 'GROUP BY a.id_order_control';
        $this->list_id = 'id_order_control';
        $this->_orderBy = 'a.date_add';
        $this->_orderWay = 'DESC';
        $this->list_simple_header = false;
        $this->identifier = 'id_order_control';
        $this->addRowAction('edit');
        $this->addRowAction('delete');

        $this->list_no_link = false;

        parent::__construct();
    }

    public function initContent()
    {
        $smarty = $this->context->smarty;

        $tpl_dir = array
        (
            _PS_MODULE_DIR_ . 'controlecommande/views/templates/admin/',
            _PS_BO_ALL_THEMES_DIR_ . $this->bo_theme . DIRECTORY_SEPARATOR . 'template',
            _PS_OVERRIDE_DIR_ . 'controllers' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'templates'
        );


        if (Tools::isSubmit('showControls') || Tools::isSubmit('submitFilter') || Tools::isSubmit('submitReset')) {
            $smarty->setTemplateDir($tpl_dir);
            $this->setTemplate('showControls.tpl');
            $this->renderList();
        } else {
            $smarty->setTemplateDir($tpl_dir);
            $this->setTemplate('controlOrders.tpl');
        }
    }

    public function initPageHeaderToolbar()
    {
        parent::initPageHeaderToolbar();
    }

    public function initToolbar()
    {
        parent::initToolbar();
    }

    public function postProcess()
    {
        $current_url = 'http://' . $_SERVER['HTTP_HOST'] . '/admin968/index.php?' . $_SERVER['QUERY_STRING'];
        $referer = $_SERVER['HTTP_REFERER'];
        $request_methode = $_SERVER['REQUEST_METHOD'];

        if (Tools::isSubmit('generateProFormat')) {
            $this->generateProforma();
        }

        if (Tools::isSubmit('submitSearchOrder')) {
            $this->searchOrder();
        }

        if (Tools::isSubmit('updateorder_control')) {
            $this->updateOrderControl();
        } else if (Tools::isSubmit("cancelControl") || Tools::isSubmit('deleteorder_control')) {
            $this->cancelOrDeleteControl();
        } else if (Tools::isSubmit("saveControl") && $current_url != $referer && $request_methode == 'GET') {
            $this->saveControl();
        } else if (Tools::isSubmit("ajax") && Tools::isSubmit('updateControl')) {
            $this->updateControl();
        } else if (Tools::isSubmit("ajax") && Tools::isSubmit('searchProduct')) {
            $this->searchProduct();
        } else if (Tools::isSubmit("ajax") && Tools::isSubmit('searchProductError')) {
            $this->searchProductError();
        } else if (Tools::isSubmit("ajax") && Tools::isSubmit('updateAllControl')) {
            $this->updateAllControl();
            //$this->saveControl();
        } else {
            $this->listOrderToControl();
        }

        parent::postProcess();
    }

    public function updateOrderProductControl($id_order_control, $id_product, $id_product_attribute)
    {
        $is_validate = $this->checkQuantity($id_order_control, $id_product, $id_product_attribute);
        if ($is_validate == 0) {
            return false;
        } else {
            $orderProductsControl = OrderProductsControl::getByProduct($id_order_control, $id_product, $id_product_attribute);

            if (intval($orderProductsControl->id_order_product_control) == 0) {
                $orderProductsControl = new OrderProductsControl();
                $orderProductsControl->id_order_control = $id_order_control;
                $orderProductsControl->id_product = $id_product;
                $orderProductsControl->id_product_attribute = $id_product_attribute;
            }

            $orderProductsControl->quantity_prepared += 1;
            if ($is_validate == 2) {
                $orderProductsControl->validate = 1;
            }
        }

        return $orderProductsControl->save();
    }

    public function checkQuantity($id_order_control, $id_product, $id_product_attribute)
    {

        $orderProductsControl = OrderProductsControl::getByProduct($id_order_control, $id_product, $id_product_attribute);
        if (intval($orderProductsControl->id_order_product_control) == 0) {
            $orderProductsControl = new OrderProductsControl();
            $orderProductsControl->quantity_prepared = 0;
            $orderProductsControl->id_product = $id_product;
            $orderProductsControl->id_product_attribute = $id_product_attribute;
        }
        $orderProductsControl->quantity_prepared++;

        $id_order = OrderControl::getIdOrder($id_order_control);
        $orderProduct = $orderProductsControl->getProductInOrder($id_order);

        if ($orderProduct->id == 0) {
            $this->errors[] = $this->l('Ce produit n\'est pas dans la commande');
            return 0;
        } else if ($orderProductsControl->quantity_prepared > $orderProduct->product_quantity) {
            $this->errors[] = $this->l('Nombre maximum pour ce produit atteint');
            return 0;
        } else if ($orderProductsControl->quantity_prepared == $orderProduct->product_quantity) {
            return 2; // valid
        } else {
            return 1; // add
        }
    }

    public function createControl($id_order)
    {
        $orderControl = OrderControl::getByIdOrder($id_order);
        if (!$orderControl->id_order_control) {
            $orderControl = new OrderControl();
            $orderControl->id_order = $id_order;
            $orderControl->id_employee = $this->context->employee->id;
            $orderControl->validate = 0;
            $orderControl->date_add = date('Y-m-d H:i:s');
            $orderControl->add();
        }

        return $orderControl->id;
    }

    public function findOrder($barcode)
    {
        $sql = 'SELECT `id_order`
                FROM `' . _DB_PREFIX_ . 'orders`
                WHERE `id_order` = \'' . pSQL($barcode) . '\'
                OR `reference` = \'' . pSQL($barcode) . '\'';

        $id_order = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);

        return new Order($id_order);
    }

    public function findProduct($barcode)
    {
        $datas = array();
        $sql = 'SELECT `id_product_attribute`
                FROM `' . _DB_PREFIX_ . 'product_attribute`
                WHERE `' . $this->module->first_fields_verification . '` = \'' . pSQL($barcode) . '\'
                OR `' . $this->module->second_fields_verification . '` = \'' . pSQL($barcode) . '\'';

        $id_product_attribute = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);

        if (!$id_product_attribute) {
            $sql = 'SELECT `id_product`
                FROM `' . _DB_PREFIX_ . 'product`
                WHERE `' . $this->module->first_fields_verification . '` = \'' . pSQL($barcode) . '\'
                OR `' . $this->module->second_fields_verification . '` = \'' . pSQL($barcode) . '\'';

            $id_product = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
            $datas = array('id_product' => (int)$id_product, 'id_product_attribute' => 0);
        } else {
            $combination = new Combination($id_product_attribute);
            $datas = array('id_product' => $combination->id_product, 'id_product_attribute' => $combination->id);
        }
        return $datas;
    }

    function displayAjaxReturns($data)
    {
        echo json_encode($data);
        return false;
    }

    public function delete()
    {
        $id_order_control = Tools::getValue('id_order_control', 0);
        if ($id_order_control != 0) {
            $orderControl = new OrderControl($id_order_control);
            if ($orderControl->delete()) {
                $this->confirmations[] = $this->l('Contr&ocirc;le supprim&eacute;');
            } else {
                $this->errors[] = $this->l('Suppression impossible');
            }
        } else {
            $this->errors[] = $this->l('Contr&ocirc;le introuvable');
        }
        if (!empty($this->errors)) {
            return false;
        }
        return true;
    }

    function isValidate($id)
    {
        $html = '';
        if ($id == 0) {
            $html = '<i class="icon-remove list-action-enable action-disabled"></i>'; // non
        } else {
            $html = '<i class="icon-check list-action-enable action-enabled"></i>'; // oui
        }
        return $html;
    }

    public function assignControldatas($order, $products, $id_order_control, $total_ordered, $total_prepared)
    {


        if (method_exists('Order', 'printPDFIcons')) {
            $printpdf = true;
        } else {
            $printpdf = false;
        }

        if ($order->id_carrier) {
            $carrier = new Carrier($order->id_carrier);
        }
        $address_delivery = new Address($order->id_address_delivery);

        $eans = [];
        foreach ($products as $product) {
            if (empty($product["product_ean13"])) {
                continue;
            }
            $eans[$product["product_ean13"]] = $product["id_product"] . "_" . $product["product_attribute_id"];
        }

        $this->context->smarty->assign(array(
            'order' => $order,
            'address_delivery' => $address_delivery,
            'carrier' => $carrier,
            'products' => $products,
            'id_order_control' => $id_order_control,
            'total_ordered' => $total_ordered,
            'total_prepared' => $total_prepared,
            'imageType' => new ImageType($this->module->id_image_type),
            'printpdf' => $printpdf,
            'token_etiquetage' => Configuration::get('SONICE_ETQ_TOKEN', null, null, null),
            'eans' => json_encode($eans)
        ));
    }

    public function listOrderToControl($etiquette = false, $id_order_label = 0)
    {

        $ordersToPrepare = array();
        $orderToPrint = array();
        $printproformat = false;
        if ($id_order_label != 0) {
            $orderToPrint = new Order($id_order_label);
            $orderToPrint->carrier = new Carrier($orderToPrint->id_carrier);
            //Generation pro format pour transporteur étranger
            if ($orderToPrint->carrier->id_reference == 178 || $orderToPrint->carrier->id_reference == 179) {
                $printproformat = true;
            } else {
                $printproformat = false;
            }
        }
        $this->context->smarty->assign('printpdf', method_exists('Order', 'printPDFIcons'));
        $this->context->smarty->assign('printLabel', $etiquette);
        $this->context->smarty->assign('orderToPrint', $orderToPrint);
        $this->context->smarty->assign('ordersToPrepare', $ordersToPrepare);
        $this->context->smarty->assign('printproformat', $printproformat);
    }


    public function getProFormatInvoiceCollection($id_order)
    {
        $sql = 'SELECT * FROM  `' . _DB_PREFIX_ . 'order_invoice` WHERE id_order = ' . $id_order;
        $order_invoice_list = Db::getInstance()->executeS($sql);

        return ObjectModel::hydrateCollection('OrderInvoice', $order_invoice_list);
    }

    public function l($str, $class = null, $addslashes = false, $htmlentities = true)
    {
        // Correction de bug Prestasghop 8
        return $str;
    }

    private function searchOrder()
    {
        $search_order = Tools::getValue('order');
        if (intval($search_order) != 0) {
            $order = new Order($search_order);
        } else {
            $order = $this->findOrder($search_order);
        }

        if ($order->id != 0) {
            $id_order_control = $this->createControl($order->id);
            $orderControl = new OrderControl($id_order_control);
            $products = $order->getProducts();
            $total_ordered = 0;
            $total_prepared = 0;
            // Add images

            $productst = array();
            foreach ($products as $productt) {
                if ($productt['product_id'] != 3063) {
                    $productst[] = $productt;
                }
            }

            foreach ($productst as &$product) {
                $productObject = new Product($product['product_id']);
                $images = $productObject->getImages($this->context->language->id, $this->context);
                $id_image = '';
                if (!empty($images)) {
                    $id_image = $images[0]['id_image'];
                }
                $product['id_image'] = $id_image;
                $product['link_rewrite'] = $productObject->link_rewrite[$this->context->language->id];
                $product['link'] = $productObject->getLink();
                $product['control_valid'] = 0;
                if (empty($product['location'])) {
                    $product['location'] = WarehouseProductLocation::getProductLocation($product['product_id'], $product['product_attribute_id'], $product['id_warehouse']);
                }
                $product['quantity_prepared'] = 0;

                $detailsValidation = $orderControl->getProductValidationDetails($product['product_id'], $product['product_attribute_id']);
                if (!empty($detailsValidation)) {
                    $product['control_valid'] = $detailsValidation['validate'];
                    $product['quantity_prepared'] = $detailsValidation['quantity_prepared'];
                }

                $total_ordered += $product['product_quantity'];
                $total_prepared += $product['quantity_prepared'];
            }
            $this->assignControldatas($order, $productst, $id_order_control, $total_ordered, $total_prepared);
        } else {
            $this->errors[] = $this->l('Order not find');
        }
    }

    private function generateProform()
    {
        $id_order = Tools::getValue('id_order', 0);
        $order_invoice_list = $this->getProFormatInvoiceCollection($id_order);
        $list[] = $order_invoice_list[0];
        $list[] = $order_invoice_list[0];
        $list[] = $order_invoice_list[0];
        $pdf = new pdfMultipleRender($list, PDF::TEMPLATE_INVOICE, Context::getContext()->smarty);
        $pdf->render();
    }

    private function updateOrderControl()
    {
        $id_order_control = Tools::getValue('id_order_control');
        $orderControl = new OrderControl($id_order_control);
        $order = new Order($orderControl->id_order);
        $products = $order->getProducts();
        $total_ordered = 0;
        $total_prepared = 0;
        // Add images
        $productst = array();
        foreach ($products as $productt) {
            if ($productt['product_id'] != 3063) {
                $productst[] = $productt;
            }
        }

        foreach ($productst as &$product) {
            $productObject = new Product($product['product_id']);
            $images = $productObject->getImages($this->context->language->id, $this->context);
            $id_image = '';
            if (!empty($images)) {
                $id_image = $images[0]['id_image'];
            }
            $product['id_image'] = $id_image;
            $product['link_rewrite'] = $productObject->link_rewrite[$this->context->language->id];
            $product['link'] = $productObject->getLink();
            $product['control_valid'] = 0;
            $product['quantity_prepared'] = 0;

            $detailsValidation = $orderControl->getProductValidationDetails($product['product_id'], $product['product_attribute_id']);
            if (!empty($detailsValidation)) {
                $product['control_valid'] = $detailsValidation['validate'];
                $product['quantity_prepared'] = $detailsValidation['quantity_prepared'];
            }

            $total_ordered += $product['product_quantity'];
            $total_prepared += $product['quantity_prepared'];
        }
        $this->assignControldatas($order, $productst, $id_order_control, $total_ordered, $total_prepared);
    }

    private function cancelOrDeleteControl()
    {
        $id_order_control = Tools::getValue('id_order_control', 0);
        if ($id_order_control != 0) {
            $orderControl = new OrderControl($id_order_control);
            if ($orderControl->delete()) {
                $this->confirmations[] = $this->l('Contr&ocirc;le supprim&eacute;');
            } else {
                $this->errors[] = $this->l('Suppression impossible');
            }
        } else {
            $this->errors[] = $this->l('Contr&ocirc;le introuvable');
        }

        $this->listOrderToControl();
    }

    private function saveControl()
    {
        $id_order_control = Tools::getValue('id_order_control', 0);
        if ($id_order_control != 0) {

            $orderControl = new OrderControl($id_order_control);
            if ($orderControl->isValid()) {
                $orderControl->validate = 1;
                $orderControl->date_validation = date('Y-m-d H:i:s');
                $orderControl->update();

                $order = new Order($orderControl->id_order);
                $order->setCurrentState($this->module->order_state_verified);

                if ($order->id_carrier) {
                    $carrier = new Carrier($order->id_carrier);
                    if ($carrier->id_reference == 342 || $carrier->id_reference == 274 || $carrier->id_reference == 275 || $carrier->id_reference == 276 || $carrier->id_reference == 279 || $carrier->id_reference == 280 || $carrier->id_reference == 281 || $carrier->id_reference == 282) {
                        $order->setCurrentState(4);
                    }
                    if ($carrier->id_reference == 390) {
                        $order->setCurrentState(42);

                        $customerEC = new Customer($order->id_customer);
                        $datetime = new DateTime('tomorrow');

                        $vars = [
                            '{order_number}' => $order->reference,
                            '{firstname}' => $customerEC->firstname,
                            '{lastname}' => $customerEC->lastname,
                            '{date}' => $datetime->format('d/m/Y'),
                        ];
                        
                        Mail::Send(
                            1,
                            'clickandcollect',
                            /*Context::getContext()->getTranslator()->trans(
                                'Your guest account has been transformed into a customer account',
                                [],
                                'Emails.Subject',
                                $language->locale
                            ),*/
                            'Votre commande est prête à être récupérée !',
                            $vars,
                            $customerEC->email,
                            $customerEC->firstname . ' ' . $customerEC->lastname,
                            null,
                            null,
                            null,
                            null,
                            _PS_MAIL_DIR_,
                            false,
                            1
                        );
                    }
                }

                $this->confirmations[] = $this->l('Contrôle validé, le statut de la commande a changé');
            } else {
                $this->warnings[] = $this->l("Contrôle invalide, produits manquants");
            }
            if (Tools::isSubmit('restOnControl')) {
                $link = Context::getContext()->link->getAdminLink('AdminControleCommande') . '&submitSearchOrder&order=' . $orderControl->id_order;
                Tools::redirectAdmin($link);
            } else {
                $wantLabel = Tools::isSubmit('printLabel');
                $etiquette = false;
                if ($orderControl->validate && $wantLabel) {
                    $etiquette = true;
                } else {
                    $this->warnings[] = $this->l('Impression étiquette impossible, contrôle non valide');
                }
                $this->listOrderToControl($etiquette, $orderControl->id_order);
            }
        } else {
            $this->errors[] = $this->l('Contrôle introuvable');
        }
    }

    private function updateControl()
    {
        $id_order_control = Tools::getValue("id_order_control");
        $id_product = Tools::getValue("id_product");
        $id_product_attribute = Tools::getValue("id_product_attribute");

        if (intval($id_order_control) != 0) {
            if (!$this->updateOrderProductControl($id_order_control, $id_product, $id_product_attribute)) {
                $this->errors[] = $this->l('Erreur contr&ocirc;le produit');
            } else {
                $this->success[] = $this->l('Produit contrôlé');
            }
        } else {
            $this->errors[] = $this->l('Contrôle introuvable');
        }
        $returns = array(
            'errors' => $this->errors,
            'success' => $this->success,
        );
        $this->errors = array();
        $this->success = array();
        $this->displayAjaxReturns($returns);
    }

    private function updateAllControl()
    {
        $id_order_control = Tools::getValue("id_order_control");
        $array_order_control = Tools::getValue("array_order_control");

        //$this->delete();
        $order_products_control = $this->getProductsControl();

        foreach ($order_products_control as $product_control)
        {
            $OrderProductControl = new OrderProductsControl($product_control['id_order_product_control']);
            $OrderProductControl->delete();
        }

        foreach ($array_order_control as $array_control) {
            if (intval($id_order_control) != 0) {
                if (!$this->updateOrderProductControl($id_order_control, $array_control["id_product"], $array_control["id_attr"])) {
                    $this->errors[] = $this->l('Erreur contrôle produit');
                } else {
                    $this->success[] = $this->l('Produit contrôlé');
                }
            } else {
                $this->errors[] = $this->l('Contrôle introuvable');
            }
        }

        if ( count($this->errors) == 0 )
        {
            $orderControl = new OrderControl($id_order_control);
            if ($orderControl->isValid()) {    
                $order = new Order($orderControl->id_order);
                if ($order->id_carrier) 
                {
                    $carrier = new Carrier($order->id_carrier);
                    if ($carrier->id_reference == 342 || $carrier->id_reference == 274 || $carrier->id_reference == 275 || $carrier->id_reference == 276 || $carrier->id_reference == 279 || $carrier->id_reference == 280 || $carrier->id_reference == 281 || $carrier->id_reference == 282) 
                    {
                        $orderControl->validate = 1;
                        $orderControl->date_validation = date('Y-m-d H:i:s');
                        $orderControl->update();
                        $order->setCurrentState($this->module->order_state_verified);
                        $order->setCurrentState(4);
                    }
                }
            }
        }

        $returns = array(
            'errors' => $this->errors,
            'success' => $this->success,
        );

        $this->errors = array();
        $this->success = array();

        $this->displayAjaxReturns($returns);
    }

    private function getProductsControl()
    {
        $id_order_control = Tools::getValue("id_order_control");
        $sql = 'SELECT *
            FROM `' . _DB_PREFIX_ . 'order_products_control` 
            WHERE `id_order_control` = ' . intval($id_order_control);

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    }

    private function searchProduct()
    {
        $barcode = Tools::getValue("barcode");
        $id_order_control = Tools::getValue("id_order_control");

        $tabProduct = $this->findProduct($barcode);

        if (intval($tabProduct['id_product']) == 0) {
            $this->errors[] = $this->l('Produit introuvable');
        }
        $returns = array(
            'product' => $tabProduct,
            'errors' => $this->errors,
        );
        $this->errors = array();
        $this->success = array();
        $this->displayAjaxReturns($returns);
    }

    private function searchProductError()
    {
        $barcode = Tools::getValue("barcode");
        $name = "";

        $tabProduct = $this->findProduct($barcode);

        if (intval($tabProduct['id_product']) == 0) {
            $this->errors[] = $this->l('Produit introuvable');
        } else {
            if (intval($tabProduct['id_product_attribute']) == 0){
                $datas = Db::getinstance()->executeS("SELECT p.id_product, p.reference, pl.name, pa.id_product_attribute, pa.reference as 'refAttr', agl.name as 'attrName', al.name as 'attrVal'
                    FROM ps_product p
                    INNER JOIN ps_product_lang pl ON pl.id_product = p.id_product AND pl.id_lang = 1
                    LEFT JOIN ps_product_attribute pa ON pa.id_product = p.id_product
                    LEFT JOIN ps_product_attribute_combination pac ON pac.id_product_attribute = pa.id_product_attribute
                    LEFT JOIN ps_attribute a ON a.id_attribute = pac.id_attribute
                    LEFT JOIN ps_attribute_group_lang agl ON agl.id_attribute_group = a.id_attribute_group AND agl.id_lang = 1
                    LEFT JOIN ps_attribute_lang al ON al.id_attribute = a.id_attribute AND al.id_lang = 1
                    WHERE p.id_product = ".pSQL($tabProduct["id_product"]));
            } else {
                $datas = Db::getinstance()->executeS("SELECT p.id_product, p.reference, pl.name, pa.id_product_attribute, pa.reference as 'refAttr', agl.name as 'attrName', al.name as 'attrVal'
                FROM ps_product p
                INNER JOIN ps_product_lang pl ON pl.id_product = p.id_product AND pl.id_lang = 1
                LEFT JOIN ps_product_attribute pa ON pa.id_product = p.id_product
                LEFT JOIN ps_product_attribute_combination pac ON pac.id_product_attribute = pa.id_product_attribute
                LEFT JOIN ps_attribute a ON a.id_attribute = pac.id_attribute
                LEFT JOIN ps_attribute_group_lang agl ON agl.id_attribute_group = a.id_attribute_group AND agl.id_lang = 1
                LEFT JOIN ps_attribute_lang al ON al.id_attribute = a.id_attribute AND al.id_lang = 1
                WHERE p.id_product = ".pSQL($tabProduct["id_product"])." AND pa.id_product_attribute = ".pSQL($tabProduct['id_product_attribute']));
            }

            if(!empty($datas)){
                if(!empty($datas[0]) && !empty($datas[0]["id_product_attribute"])){
                    $nbAttr = count($datas) - 1;

                    $name = !empty($datas[0]["refAttr"]) ? $datas[0]["refAttr"] : $datas[0]["reference"];
                    $name .= " - ".$datas[0]["name"];

                    for ($i = 0; $i <= $nbAttr; $i++){
                        if($i == 0){
                            $name .= " (";
                        }

                        $name .= $datas[$i]["attrName"]." : ".$datas[$i]["attrVal"];

                        if ($i == $nbAttr){
                            $name .= ")";
                        }else{
                            $name .= " / ";
                        }
                    }

                }else{
                    $name = $datas[0]["reference"]." | ".$datas[0]["name"];
                }
            }
        }

        $returns = array(
            'product' => $name,
            'errors' => $this->errors,
        );

        $this->errors = array();
        $this->success = array();

        $this->displayAjaxReturns($returns);
    }
}
