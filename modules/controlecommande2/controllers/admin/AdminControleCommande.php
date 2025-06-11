<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 1);
/**

 *  @author Anjouweb

 * */
require_once _PS_MODULE_DIR_ . 'controlecommande/controlecommande.php';

class AdminControleCommandeController extends ModuleAdminController
{

    public function __construct()
    {
      if ( $_SERVER["REMOTE_ADDR"] == '86.217.95.57' ) error_log('__construct 1');
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
//                'callback' => 'isValidate'
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
            . ' LEFT JOIN `' . _DB_PREFIX_ . 'order_state_lang` osl ON o.current_state = osl.id_order_state AND osl.id_lang = '.$this->context->language->id.' ';
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
      if ( $_SERVER["REMOTE_ADDR"] == '86.217.95.57' ) error_log('initContent 1');
        //parent::initContent();
          if ( $_SERVER["REMOTE_ADDR"] == '86.217.95.57' ) error_log('initContent 2');
        $smarty = $this->context->smarty;
//
//        if (Tools::isSubmit('showControls'))
//        {
////            $this->setTemplate('showControls.tpl');
//        }
//        if (Tools::isSubmit('add_control') || Tools::isSubmit('updateorder_control') || Tools::isSubmit('submitSearchOrder'))
//        {

  if ( $_SERVER["REMOTE_ADDR"] == '86.217.95.57' ) error_log('initContent 3');
     $tpl_dir = array
                (
                _PS_MODULE_DIR_ . 'controlecommande/views/templates/admin/',
                _PS_BO_ALL_THEMES_DIR_ . $this->bo_theme . DIRECTORY_SEPARATOR . 'template',
                _PS_OVERRIDE_DIR_ . 'controllers' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'templates'
            );


        if(Tools::isSubmit('showControls') || Tools::isSubmit('submitFilter') || Tools::isSubmit('submitReset'))
        {
          if ( $_SERVER["REMOTE_ADDR"] == '86.217.95.57' ) error_log('initContent 4');
            $smarty->setTemplateDir($tpl_dir);
            $this->setTemplate('showControls.tpl');
            $this->renderList();
              if ( $_SERVER["REMOTE_ADDR"] == '86.217.95.57' ) error_log('initContent 5');
        }
        else
        {

            if ( $_SERVER["REMOTE_ADDR"] == '86.217.95.57' ) error_log('initContent 6');
            $smarty->setTemplateDir($tpl_dir);
            $this->setTemplate('controlOrders.tpl');
              if ( $_SERVER["REMOTE_ADDR"] == '86.217.95.57' ) error_log('initContent 7');
        }
          if ( $_SERVER["REMOTE_ADDR"] == '86.217.95.57' ) error_log('initContent 8');

    }

    public function initPageHeaderToolbar()
    {

        /*$this->page_header_toolbar_btn['add'] = array(
            'href' => Context::getContext()->link->getAdminLink('AdminControleCommande') . '&add_control',
            'desc' => $this->l('Set controls'),
            'icon' => 'process-icon-new'
        );
        $this->page_header_toolbar_btn['back'] = array(
            'href' => Context::getContext()->link->getAdminLink('AdminModules') . '&configure=' . $this->module->name,
            'desc' => $this->l('Change settings'),
            'icon' => 'process-icon-back'
        );
*/
        parent::initPageHeaderToolbar();
    }

    public function initToolbar()
    {
        parent::initToolbar();
        /*
        $this->toolbar_btn = array();
        $this->toolbar_btn['new'] = array(
            'href' => Context::getContext()->link->getAdminLink('AdminControleCommande') . '&add_control',
            'desc' => $this->l('Set controls'),
            'icon' => 'process-icon-new'
        );
        $this->toolbar_btn['back'] = array(
            'href' => Context::getContext()->link->getAdminLink('AdminModules') . '&configure=' . $this->module->name,
            'desc' => $this->l('Change settings'),
            'icon' => 'process-icon-back'
        );*/
    }

    public function postProcess()
    {
      if ( $_SERVER["REMOTE_ADDR"] == '86.217.95.57' ) error_log('postProcess 1');
        $current_url = 'http://'.$_SERVER['HTTP_HOST'].'/admin968/index.php?'.$_SERVER['QUERY_STRING'];
        $referer = $_SERVER['HTTP_REFERER'];
        $request_methode = $_SERVER['REQUEST_METHOD'];

        if(Tools::isSubmit('generateProFormat'))
        {
            $id_order = Tools::getValue('id_order',0);
            $order_invoice_list = $this->getProFormatInvoiceCollection($id_order);
            $list[] = $order_invoice_list[0];
            $list[] = $order_invoice_list[0];
            $list[] = $order_invoice_list[0];
            $pdf = new pdfMultipleRender($list, PDF::TEMPLATE_INVOICE, Context::getContext()->smarty);
            $pdf->render();
        }
        if (!Tools::isSubmit("ajax"))
        {
            // Pour debug changement état sur tablette
//            echo '<br>$current : '.$current_url;
//            echo '<br>$referer : '.$referer;
//            echo '<br>$request_methode : '.$request_methode;
        }
          if ( $_SERVER["REMOTE_ADDR"] == '86.217.95.57' ) error_log('postProcess 2');
        if (Tools::isSubmit('submitSearchOrder'))
        {
          if ( $_SERVER["REMOTE_ADDR"] == '86.217.95.57' ) error_log('postProcess 3');
            $search_order = Tools::getValue('order');
            if (intval($search_order) != 0)
            {
                $order = new Order($search_order);
            }
            else
            {
                $order = $this->findOrder($search_order);
            }

            if ($order->id != 0)
            {
                $id_order_control = $this->createControl($order->id);
                $orderControl = new OrderControl($id_order_control);
                $products = $order->getProducts();
                $total_ordered = 0;
                $total_prepared = 0;
                // Add images

                $productst = array();
                foreach ($products as $productt)
                {
                    if ( $productt['product_id'] != 3063 )
                    {
                        $productst[] = $productt;
                    }
                }

                foreach ($productst as &$product)
                {
                    $productObject = new Product($product['product_id']);
                    $images = $productObject->getImages($this->context->language->id, $this->context);
                    $id_image = '';
                    if (!empty($images))
                    {
                        $id_image = $images[0]['id_image'];
                    }
                    $product['id_image'] = $id_image;
                    $product['link_rewrite'] = $productObject->link_rewrite[$this->context->language->id];
                    $product['link'] = $productObject->getLink();
                    $product['control_valid'] = 0;
                    if(empty($product['location']))
                    {
                        $product['location'] = WarehouseProductLocation::getProductLocation($product['product_id'],$product['product_attribute_id'], $product['id_warehouse']);
                    }
                    $product['quantity_prepared'] = 0;

                    $detailsValidation = $orderControl->getProductValidationDetails($product['product_id'], $product['product_attribute_id']);
                    if (!empty($detailsValidation))
                    {
                        $product['control_valid'] = $detailsValidation['validate'];
                        $product['quantity_prepared'] = $detailsValidation['quantity_prepared'];
                    }

                    $total_ordered += $product['product_quantity'];
                    $total_prepared += $product['quantity_prepared'];
                }
                $this->assignControldatas($order, $productst, $id_order_control, $total_ordered, $total_prepared);
            }
            else
            {
                $this->errors[] = $this->l('Order not find');
            }
              if ( $_SERVER["REMOTE_ADDR"] == '86.217.95.57' ) error_log('postProcess 4');
        }
        if (Tools::isSubmit('updateorder_control'))
        {
          if ( $_SERVER["REMOTE_ADDR"] == '86.217.95.57' ) error_log('postProcess 5');
            $id_order_control = Tools::getValue('id_order_control');
            $orderControl = new OrderControl($id_order_control);
            $order = new Order($orderControl->id_order);
            $products = $order->getProducts();
            $total_ordered = 0;
            $total_prepared = 0;
            // Add images
            $productst = array();
            foreach ($products as $productt)
            {
                if ( $productt['product_id'] != 3063 )
                {
                    $productst[] = $productt;
                }
            }

            foreach ($productst as &$product)
            {
                $productObject = new Product($product['product_id']);
                $images = $productObject->getImages($this->context->language->id, $this->context);
                $id_image = '';
                if (!empty($images))
                {
                    $id_image = $images[0]['id_image'];
                }
                $product['id_image'] = $id_image;
                $product['link_rewrite'] = $productObject->link_rewrite[$this->context->language->id];
                $product['link'] = $productObject->getLink();
                $product['control_valid'] = 0;
                $product['quantity_prepared'] = 0;

                $detailsValidation = $orderControl->getProductValidationDetails($product['product_id'], $product['product_attribute_id']);
                if (!empty($detailsValidation))
                {
                    $product['control_valid'] = $detailsValidation['validate'];
                    $product['quantity_prepared'] = $detailsValidation['quantity_prepared'];
                }

                $total_ordered += $product['product_quantity'];
                $total_prepared += $product['quantity_prepared'];
            }
            $this->assignControldatas($order, $productst, $id_order_control, $total_ordered, $total_prepared);
              if ( $_SERVER["REMOTE_ADDR"] == '86.217.95.57' ) error_log('postProcess 6');
        }
        else if (Tools::isSubmit("cancelControl") || Tools::isSubmit('deleteorder_control'))
        {
          if ( $_SERVER["REMOTE_ADDR"] == '86.217.95.57' ) error_log('postProcess 7');
            $id_order_control = Tools::getValue('id_order_control', 0);
            if ($id_order_control != 0)
            {
                $orderControl = new OrderControl($id_order_control);
                if ($orderControl->delete())
                {
                    $this->confirmations[] = $this->l('Contr&ocirc;le supprim&eacute;');
                }
                else
                {
                    $this->errors[] = $this->l('Suppression impossible');
                }
            }
            else
            {
                $this->errors[] = $this->l('Contr&ocirc;le introuvable');
            }

            $this->listOrderToControl();
              if ( $_SERVER["REMOTE_ADDR"] == '86.217.95.57' ) error_log('postProcess 8');
        }
        else if (Tools::isSubmit("saveControl") && $current_url != $referer && $request_methode == 'GET')
        {
          if ( $_SERVER["REMOTE_ADDR"] == '86.217.95.57' ) error_log('postProcess 9');
            $id_order_control = Tools::getValue('id_order_control', 0);
            if ($id_order_control != 0)
            {
                $orderControl = new OrderControl($id_order_control);
                if ($orderControl->isValid())
                {
                    $orderControl->validate = 1;
                    $orderControl->date_validation = date('Y-m-d H:i:s');
                    $orderControl->update();

                    $order = new Order($orderControl->id_order);
                    $order->setCurrentState($this->module->order_state_verified);

                    if($order->id_carrier)
                    {
                        $carrier = new Carrier($order->id_carrier);
                        if($carrier->id_reference == 342 || $carrier->id_reference == 274 || $carrier->id_reference == 275 || $carrier->id_reference == 276 || $carrier->id_reference == 279 || $carrier->id_reference == 280 || $carrier->id_reference == 281 || $carrier->id_reference == 282){
                            $order->setCurrentState(4);
                        }
                    }

                    $this->confirmations[] = $this->l('Contrôle validé, le statut de la commande a changé');
                }
                else
                {
                    $this->warnings[] = $this->l("Contrôle invalide, produits manquants");
                }
                if(Tools::isSubmit('restOnControl'))
                {
                    $link = Context::getContext()->link->getAdminLink('AdminControleCommande').'&submitSearchOrder&order='.$orderControl->id_order;
//                        echo $link;
                    Tools::redirectAdmin($link);
                }
                else
                {
                    $wantLabel = Tools::isSubmit('printLabel');
                    $etiquette = false;
                    if($orderControl->validate && $wantLabel)
                    {
                         $etiquette = true;
                    }
                    else
                    {
                        $this->warnings[] = $this->l('Impression étiquette impossible, contrôle non valide');
                    }
                    $this->listOrderToControl($etiquette, $orderControl->id_order);
                }
            }
            else
            {
                $this->errors[] = $this->l('Contrôle introuvable');
            }
              if ( $_SERVER["REMOTE_ADDR"] == '86.217.95.57' ) error_log('postProcess 10');
        }
        else if (Tools::isSubmit("ajax") && Tools::isSubmit('updateControl'))
        {
          if ( $_SERVER["REMOTE_ADDR"] == '86.217.95.57' ) error_log('postProcess 11');
            $id_order_control = Tools::getValue("id_order_control");
            $id_product = Tools::getValue("id_product");
            $id_product_attribute = Tools::getValue("id_product_attribute");

            error_log("AW U >>> ".$id_order_control." >>> DEBUT DU CONTROLE COMMANDE");
            error_log("AW U >>> ".$id_order_control." >>> id_produit : ".$id_product);
            error_log("AW U >>> ".$id_order_control." >>> id_produit_attr :".$id_product_attribute);

            if (intval($id_order_control) != 0)
            {
                error_log("AW U >>> ".$id_order_control." >>> DEBUT de l'update");
                if (!$this->updateOrderProductControl($id_order_control, $id_product, $id_product_attribute))
                {
                    $this->errors[] = $this->l('Erreur contr&ocirc;le produit');
                }
                else
                {
                    $this->success[] = $this->l('Produit contrôlé');
                }
                error_log("AW U >>> ".$id_order_control." >>> FIN de l'update");
            }
            else
            {
                $this->errors[] = $this->l('Contrôle introuvable');
            }
            $returns = array(
                'errors' => $this->errors,
                'success' => $this->success,
            );
            $this->errors = array();
            $this->success = array();
            error_log("AW U >>> ".$id_order_control." >>> lancement displayAjaxReturns");
            $this->displayAjaxReturns($returns);
            error_log("AW U >>> ".$id_order_control." >>> FIN DU CONTROLE COMMANDE");
              if ( $_SERVER["REMOTE_ADDR"] == '86.217.95.57' ) error_log('postProcess 12');
        }
        else if (Tools::isSubmit("ajax") && Tools::isSubmit('searchProduct'))
        {
            if ( $_SERVER["REMOTE_ADDR"] == '86.217.95.57' ) error_log('postProcess 13');

            $barcode = Tools::getValue("barcode");
            $id_order_control = Tools::getValue("id_order_control");

            error_log("AW S >>> ".$id_order_control." >>> DEBUT DU CONTROLE COMMANDE");
            error_log("AW S >>> ".$id_order_control." >>> BARCODE : ".$barcode);

            error_log("AW S >>> ".$id_order_control." >>> debut recuperation des produits");
            $tabProduct = $this->findProduct($barcode);
            error_log("AW S >>> ".$id_order_control." >>> fin recuperation des produits");

            if (intval($tabProduct['id_product']) == 0)
            {
                $this->errors[] = $this->l('Produit introuvable');
            }
            $returns = array(
                'product' => $tabProduct,
                'errors' => $this->errors,
            );
            $this->errors = array();
            $this->success = array();
            error_log("AW S >>> ".$id_order_control." >>> lancement displayAjaxReturns");
            $this->displayAjaxReturns($returns);
            error_log("AW S >>> ".$id_order_control." >>> FIN DU CONTROLE COMMANDE");
              if ( $_SERVER["REMOTE_ADDR"] == '86.217.95.57' ) error_log('postProcess 14');
        }
        else
        {
          if ( $_SERVER["REMOTE_ADDR"] == '86.217.95.57' ) error_log('postProcess 15');
            $this->listOrderToControl();
              if ( $_SERVER["REMOTE_ADDR"] == '86.217.95.57' ) error_log('postProcess 16');
        }

          if ( $_SERVER["REMOTE_ADDR"] == '86.217.95.57' ) error_log('postProcess 17');

        parent::postProcess();
          if ( $_SERVER["REMOTE_ADDR"] == '86.217.95.57' ) error_log('postProcess 18');
    }

//
    public function updateOrderProductControl($id_order_control, $id_product, $id_product_attribute)
    {
        $is_validate = $this->checkQuantity($id_order_control, $id_product, $id_product_attribute);
        if ($is_validate == 0)
        {
            return false;
        }
        else
        {
            $orderProductsControl = OrderProductsControl::getByProduct($id_order_control, $id_product, $id_product_attribute);

            if (intval($orderProductsControl->id_order_product_control) == 0)
            {
                $orderProductsControl = new OrderProductsControl();
                $orderProductsControl->id_order_control = $id_order_control;
                $orderProductsControl->id_product = $id_product;
                $orderProductsControl->id_product_attribute = $id_product_attribute;
            }

            $orderProductsControl->quantity_prepared += 1;
            if ($is_validate == 2)
            {
                $orderProductsControl->validate = 1;
            }
        }

        return $orderProductsControl->save();
    }

    public function checkQuantity($id_order_control, $id_product, $id_product_attribute)
    {

        $orderProductsControl = OrderProductsControl::getByProduct($id_order_control, $id_product, $id_product_attribute);
        if (intval($orderProductsControl->id_order_product_control) == 0)
        {
            $orderProductsControl = new OrderProductsControl();
            $orderProductsControl->quantity_prepared = 0;
            $orderProductsControl->id_product = $id_product;
            $orderProductsControl->id_product_attribute = $id_product_attribute;
        }
        $orderProductsControl->quantity_prepared++;

        $id_order = OrderControl::getIdOrder($id_order_control);
        $orderProduct = $orderProductsControl->getProductInOrder($id_order);

        if ($orderProduct->id == 0)
        {
            $this->errors[] = $this->l('Ce produit n\'est pas dans la commande');
            return 0;
        }
        else if ($orderProductsControl->quantity_prepared > $orderProduct->product_quantity)
        {
            $this->errors[] = $this->l('Nombre maximum pour ce produit atteint');
            return 0;
        }
        else if ($orderProductsControl->quantity_prepared == $orderProduct->product_quantity)
        {
            return 2; // valid
        }
        else
        {
            return 1; // add
        }
    }

    public function createControl($id_order)
    {
        $orderControl = OrderControl::getByIdOrder($id_order);
        if (!$orderControl->id_order_control)
        {
            $orderControl = new OrderControl();
            $orderControl->id_order = $id_order;
            $orderControl->id_employee = $this->context->employee->id;
            $orderControl->validate = 0;
            $orderControl->date_add = date('Y-m-d H:i:s');
//            $orderControl->date_validation = '0000-00-00 00:00:00';
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

        if (!$id_product_attribute)
        {
            $sql = 'SELECT `id_product`
                FROM `' . _DB_PREFIX_ . 'product`
                WHERE `' . $this->module->first_fields_verification . '` = \'' . pSQL($barcode) . '\'
                OR `' . $this->module->second_fields_verification . '` = \'' . pSQL($barcode) . '\'';

            $id_product = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
            $datas = array('id_product' => (int) $id_product, 'id_product_attribute' => 0);
        }
        else
        {
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

    public function getOldControls()
    {
        $sql = 'SELECT oc.*, emp.lastname, emp.firstname, emp.email, o.reference, o.date_add as date_order, '
                . '(SELECT SUM(quantity_prepared) FROM `' . _DB_PREFIX_ . 'order_products_control` opc WHERE oc.id_order_control = opc.id_order_control) as total_prepared, '
                . '(SELECT SUM(product_quantity) FROM `' . _DB_PREFIX_ . 'order_detail` od WHERE oc.id_order = od.id_order) as total_ordered '
                . 'FROM `' . _DB_PREFIX_ . 'order_control` oc '
                . 'LEFT JOIN `' . _DB_PREFIX_ . 'employee` emp ON oc.id_employee = emp.id_employee '
                . 'LEFT JOIN `' . _DB_PREFIX_ . 'orders` o ON oc.id_order = o.id_order '
                . 'GROUP BY oc.id_order_control';

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    }

    public function delete()
    {
        $id_order_control = Tools::getValue('id_order_control', 0);
        if ($id_order_control != 0)
        {
            $orderControl = new OrderControl($id_order_control);
            if ($orderControl->delete())
            {
                $this->confirmations[] = $this->l('Contr&ocirc;le supprim&eacute;');
            }
            else
            {
                $this->errors[] = $this->l('Suppression impossible');
            }
        }
        else
        {
            $this->errors[] = $this->l('Contr&ocirc;le introuvable');
        }
        if (!empty($this->errors))
        {
            return false;
        }
        return true;
    }

    function isValidate($id)
    {
        $html = '';
        if ($id == 0)
        {
            $html = '<i class="icon-remove list-action-enable action-disabled"></i>'; // non
        }
        else
        {
            $html = '<i class="icon-check list-action-enable action-enabled"></i>'; // oui
        }
        return $html;
    }

    public function assignControldatas($order, $products, $id_order_control, $total_ordered, $total_prepared)
    {

            if(method_exists('Order','printPDFIcons'))
            {
                $printpdf =true;
            } 
            else
            {
                $printpdf = false;
            }

        if($order->id_carrier)
        {
            $carrier = new Carrier($order->id_carrier);
        }
        $address_delivery = new Address($order->id_address_delivery);

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
            'token_etiquetage' => Configuration::get('SONICE_ETQ_TOKEN', null, null, null)
        ));
    }
    public function listOrderToControl($etiquette = false, $id_order_label = 0)
    {

        $ordersToPrepare = array();
        /*foreach (Order::getOrderIdsByStatus($this->module->order_state_to_control) as $id_order)
        {
            $order = new Order($id_order);
            $order->carrier = new Carrier($order->id_carrier);
            $order->customer = new Customer($order->id_customer);
            $ordersToPrepare[] = $order;
        }*/
        $orderToPrint = array();
        //echo '<br>$id_order_label : '.$id_order_label;
        //echo '<br />';
        $printproformat = false;
        if($id_order_label != 0)
        {
            $orderToPrint = new Order($id_order_label);
            $orderToPrint->carrier = new Carrier($orderToPrint->id_carrier);
            //Generation pro format pour transporteur étranger
            if($orderToPrint->carrier->id_reference == 178 || $orderToPrint->carrier->id_reference == 179)
            {
                $printproformat = true;
            }
            else
            {
                $printproformat = false;
            }
        }
        $this->context->smarty->assign('printpdf', method_exists('Order', 'printPDFIcons'));
        $this->context->smarty->assign('printLabel',$etiquette);
        $this->context->smarty->assign('orderToPrint',$orderToPrint);
        $this->context->smarty->assign('ordersToPrepare', $ordersToPrepare);
        $this->context->smarty->assign('printproformat',$printproformat);
    }


    public function getProFormatInvoiceCollection($id_order)
    {
        $sql = 'SELECT * FROM  `' . _DB_PREFIX_ . 'order_invoice` WHERE id_order = '.$id_order;
        $order_invoice_list = Db::getInstance()->executeS($sql);

        return ObjectModel::hydrateCollection('OrderInvoice', $order_invoice_list);
    }
	
	public function l($str, $class = null, $addslashes = false, $htmlentities = true)
    {
		// Correction de bug Prestasghop 8
        return $str;
    }
}
