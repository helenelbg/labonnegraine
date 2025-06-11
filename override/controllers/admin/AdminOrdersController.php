<?php
    class AdminOrdersController extends AdminOrdersControllerCore
    {
        public function __construct()
        {
            $this->bootstrap = true;
		$this->table = 'order';
		$this->className = 'Order';
		$this->lang = false;
		$this->addRowAction('view');
		$this->explicitSelect = true;
		$this->allow_export = true;
		$this->deleted = false;
		$this->context = Context::getContext();

		$this->_select = '
		a.id_currency, a.utm_origin_order,
		(SELECT so.utm_source FROM `' . _DB_PREFIX_ . 'orders` so WHERE so.id_order = a.id_order) AS order_utm_source,
        (SELECT so.utm_medium FROM `' . _DB_PREFIX_ . 'orders` so WHERE so.id_order = a.id_order) AS order_utm_medium,
        (SELECT so.utm_campaign FROM `' . _DB_PREFIX_ . 'orders` so WHERE so.id_order = a.id_order) AS order_utm_campaign,
        (SELECT od.id_warehouse FROM `' . _DB_PREFIX_ . 'order_detail` od WHERE od.id_order = a.id_order) AS week_choice,
		a.id_order AS id_pdf,
		CONCAT(LEFT(c.`firstname`, 1), \'. \', c.`lastname`) AS `customer`,
		osl.`name` AS `osname`,
		os.`color`,
		IF((SELECT MIN(so.date_add) FROM `' . _DB_PREFIX_ . 'orders` so WHERE so.id_customer = a.id_customer) = a.date_add, 1, 0) as new,
		country_lang.name as cname,
		IF(a.valid, 1, 0) badge_success';

		$this->_join = '
		LEFT JOIN `'._DB_PREFIX_.'customer` c ON (c.`id_customer` = a.`id_customer`)
		INNER JOIN `'._DB_PREFIX_.'address` address ON address.id_address = a.id_address_delivery
		INNER JOIN `'._DB_PREFIX_.'country` country ON address.id_country = country.id_country
		INNER JOIN `'._DB_PREFIX_.'country_lang` country_lang ON (country.`id_country` = country_lang.`id_country` AND country_lang.`id_lang` = '.(int)$this->context->language->id.')
		LEFT JOIN `'._DB_PREFIX_.'order_state` os ON (os.`id_order_state` = a.`current_state`)
		LEFT JOIN `'._DB_PREFIX_.'order_state_lang` osl ON (os.`id_order_state` = osl.`id_order_state` AND osl.`id_lang` = '.(int)$this->context->language->id.')';
		$this->_orderBy = 'id_order';
		$this->_orderWay = 'DESC';

		$statuses = OrderState::getOrderStates((int)$this->context->language->id);
		foreach ($statuses as $status)
			$this->statuses_array[$status['id_order_state']] = $status['name'];

		$this->fields_list = array(
			'id_order' => array(
				'title' => $this->l('ID'),
				'align' => 'text-center',
				'class' => 'fixed-width-xs'
			),
			'reference' => array(
				'title' => $this->l('Reference')
			),
			'new' => array(
				'title' => $this->l('New client'),
				'align' => 'text-center',
				'type' => 'bool',
				'tmpTableFilter' => true,
                                'callback' => 'newIcon',
				'orderby' => false
			),
			'customer' => array(
				'title' => $this->l('Customer'),
				'havingFilter' => true,
			),
		);

		if (Configuration::get('PS_B2B_ENABLE'))
		{
			$this->fields_list = array_merge($this->fields_list, array(
				'company' => array(
					'title' => $this->l('Company'),
					'filter_key' => 'c!company'
				),
			));
		}

			if(Configuration::get('UTM_SOURCE_VALUE') && Configuration::get('UTM_SOURCE_VALUE') == "yes"){
				$this->fields_list = array_merge($this->fields_list, array(
					'order_utm_source' => array(
						'title' => "utm_source",
						'havingFilter' => true
					),
				));
			}

			if(Configuration::get('UTM_MEDIUM_VALUE') && Configuration::get('UTM_MEDIUM_VALUE') == "yes"){
				$this->fields_list = array_merge($this->fields_list, array(
					'order_utm_medium' => array(
						'title' => "utm_medium",
						'havingFilter' => true
					),
				));
			}

			if(Configuration::get('UTM_CAMPAIGN_VALUE') && Configuration::get('UTM_CAMPAIGN_VALUE') == "yes"){
				$this->fields_list = array_merge($this->fields_list, array(
					'order_utm_campaign' => array(
						'title' => "utm_campaign",
						'havingFilter' => true
					),
				));
			}

			$this->fields_list = array_merge($this->fields_list, array(
					'week_choice' => array(
						'title' => "week_choice",
						'havingFilter' => true
					),
				));

		$this->fields_list = array_merge($this->fields_list, array(
			'total_paid_tax_incl' => array(
				'title' => $this->l('Total'),
				'align' => 'text-right',
				'type' => 'price',
				'currency' => true,
				'callback' => 'setOrderCurrency',
				'badge_success' => true
			),
			'payment' => array(
				'title' => $this->l('Payment')
			),
//			'utm_origin_order' => array(
//				'title' => $this->l('UTM')
//			),
			'osname' => array(
				'title' => $this->l('Status'),
				'type' => 'select',
				'color' => 'color',
				'list' => $this->statuses_array,
				'filter_key' => 'os!id_order_state',
				'filter_type' => 'int',
				'order_key' => 'osname'
			),
			'date_add' => array(
				'title' => $this->l('Date'),
				'align' => 'text-right',
				'type' => 'datetime',
				'filter_key' => 'a!date_add'
			),
			'id_pdf' => array(
				'title' => $this->l('PDF'),
				'align' => 'text-center',
				'callback' => 'printPDFIcons',
				'orderby' => false,
				'search' => false,
				'remove_onclick' => true
			)
		));

		if (Country::isCurrentlyUsed('country', true))
		{
			$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
			SELECT DISTINCT c.id_country, cl.`name`
			FROM `'._DB_PREFIX_.'orders` o
			'.Shop::addSqlAssociation('orders', 'o').'
			INNER JOIN `'._DB_PREFIX_.'address` a ON a.id_address = o.id_address_delivery
			INNER JOIN `'._DB_PREFIX_.'country` c ON a.id_country = c.id_country
			INNER JOIN `'._DB_PREFIX_.'country_lang` cl ON (c.`id_country` = cl.`id_country` AND cl.`id_lang` = '.(int)$this->context->language->id.')
			ORDER BY cl.name ASC');

			$country_array = array();
			foreach ($result as $row)
				$country_array[$row['id_country']] = $row['name'];

			$part1 = array_slice($this->fields_list, 0, 3);
			$part2 = array_slice($this->fields_list, 3);
			$part1['cname'] = array(
				'title' => $this->l('Delivery'),
				'type' => 'select',
				'list' => $country_array,
				'filter_key' => 'country!id_country',
				'filter_type' => 'int',
				'order_key' => 'cname'
			);
			$this->fields_list = array_merge($part1, $part2);
		}

		$this->shopLinkType = 'shop';
		$this->shopShareDatas = Shop::SHARE_ORDER;

		if (Tools::isSubmit('id_order'))
		{
			// Save context (in order to apply cart rule)
			$order = new Order((int)Tools::getValue('id_order'));
			$this->context->cart = new Cart($order->id_cart);
			$this->context->customer = new Customer($order->id_customer);
		}

		$this->bulk_actions = array(
			'updateOrderStatus' => array('text' => $this->l('Change Order Status'), 'icon' => 'icon-refresh')
		);

		AdminController::__construct();
        }

        public function newIcon($id_order, $tr)
        {

		$this->context->smarty->assign(array(
			'new' => $tr['new']
		));

		return $this->createTemplate('_new_icon.tpl')->fetch();

        }

        public function initPageHeaderToolbar()
	{
		parent::initPageHeaderToolbar();

		if (empty($this->display))
			$this->page_header_toolbar_btn['import_inet'] = array(
				'href' => 'import_fichier_iNET.php',
				'desc' => $this->l('Import iNet', null, null, false),
				'icon' => 'process-icon-import'
			);
                if (empty($this->display))
                {
                    $cmds_attentes = '';
                    $id_commandes_attentes = Order::getOrderIdsByStatus(2, 50);

					$tab_order_attr = array();

                    foreach ($id_commandes_attentes as $comm) {
                    	$commande = new Order($comm);
                    	$products_commande = $commande->getProductsDetail();
                    	foreach ($products_commande as $produit) {

                    	  $ref_prod_tab = explode('-', $produit['product_reference']);                        
                          if(

                      		 (
								(substr($produit['product_reference'], 0, 3) == '0-0')
								|| (substr($produit['product_reference'], 0, 3) == '0-1')
								|| (substr($produit['product_reference'], 0, 3) == '0-2')
								|| (substr($produit['product_reference'], 0, 3) == '8-0')
								|| (substr($produit['product_reference'], 0, 3) == '8-1')
								|| (substr($produit['product_reference'], 0, 3) == '8-2')
								|| (substr($produit['product_reference'], 0, 3) == '8-3')
								|| (substr($produit['product_reference'], 0, 3) == '8-4')
								|| (substr($produit['product_reference'], 0, 3) == '8-5')
								|| (substr($produit['product_reference'], 0, 3) == '8-6')
							 )
							 && (substr($produit['product_reference'], 0, 5) != '8-600')
                      		)
							{
                    			$tab_order_attr[] = $comm.'_'.$produit['product_id'].'_'.$produit['product_attribute_id'].'_'.$produit['product_quantity'];
                    		}
                    	}
                    }

                    if(count($tab_order_attr) > 0 ){
                    	$liste_comm_prod = implode('-', $tab_order_attr);
                    } else {
						$liste_comm_prod = "";
					}

                    $cmds_attentes = implode('-',$id_commandes_attentes);
                    $id_commandes_attentes_mandat = Order::getOrderIdsByStatus(12);
                    if(strlen($cmds_attentes)> 0 && !empty($id_commandes_attentes_mandat))
                    {
                        $cmds_attentes .= '-';
                    }

                    $nb_etiq_tmp = explode('-', $liste_comm_prod);
                    $nb_etiq = 0;
                    foreach($nb_etiq_tmp as $tmpp)
                    {
                    	$expltmp = explode('_', $tmpp);
                    	$nb_etiq += $expltmp[3];
                    }





                    	$cmds_attentes .= implode('-',$id_commandes_attentes_mandat);

						$cntCA = explode('-', $cmds_attentes);

                    	/*if($_SERVER['REMOTE_ADDR'] == '82.127.55.60' || $_SERVER['REMOTE_ADDR'] == '80.15.118.113' || $_SERVER['REMOTE_ADDR'] == '176.134.70.47'){
                    		$this->page_header_toolbar_btn['imprimer'] = array(
                    			'href' => 'test_etiquettes2.php?deliveryslipsadmin='.$cmds_attentes.'',
                    			'desc' => $this->l('Impression des BLs TEST', null, null, false),
                    			'icon' => 'process-icon-print',
                    			'js' => "print_bls('".$cmds_attentes."'), window.open('http://localhost/etiquettes/index_prod_test.php?l=".$liste_comm_prod."')"
                    		);
                    	}*/

                    	if ( $nb_etiq > 30 )
                    	{
                    		$this->page_header_toolbar_btn['imprimer'] = array(
                    			'href' => 'test_etiquettes.php?deliveryslipsadmin='.$cmds_attentes.'',
                    			'desc' => $this->l('Impression des BLs... ('.count($cntCA).')', null, null, false),
                    			'icon' => 'process-icon-print',
                    			'js' => "window.open('https://dev.labonnegraine.com/admin123/liens_etiq.php'), print_bls('".$cmds_attentes."')"
                    		);
                    	}
                    	else{
                    		$this->page_header_toolbar_btn['imprimer'] = array(
                    			'href' => 'test_etiquettes.php?deliveryslipsadmin='.$cmds_attentes.'',
                    			'desc' => $this->l('Impression des BLs. ('.count($cntCA).')', null, null, false),
                    			'icon' => 'process-icon-print',
                    			'js' => 'print_bls("'.$cmds_attentes.'"), window.open("http://localhost/etiquettes/index_prod_test.php?l='.$liste_comm_prod.'")'
                    		);
                    	}


                    	$commandes_prep_en_cours = Order::getOrderIdsByStatusEtiq(3);

                    	$tab_id_comm_prep = array();

                    	$cpt_etq = 0;

                    	foreach ($commandes_prep_en_cours as $id_comm_prep) {
                    		$order = new Order($id_comm_prep);
                        $carrier_EC = new Carrier($order->id_carrier);
                        //$transport_colissimo = array(310,311,303,286,300);
                        $transport_colissimo = array(142,189,190,191,192);
                    		//if($order->id_carrier == 296 || $order->id_carrier == 301 || $order->id_carrier == 303 || $order->id_carrier == 286 || $order->id_carrier == 300){
                        if(in_array($carrier_EC->id_reference, $transport_colissimo)){
                        	$tab_id_comm_prep[] = $id_comm_prep;
                    			////$tab_id_comm_prep[] = "print_etiquettes('".$id_comm_prep."')";
                    			$cpt_etq++;
                    		}

                    	}

                      $onclick = '';
                      /*$cpt_o = 0;
                      foreach($tab_id_comm_prep as $item)
                      {
                        $cpt_o++;
                        if ( $cpt_o == 1 )
                        {
                          $onclick .= '$.when('.$item.')';
                        }
                        else
                        {
                          $onclick .= '.then('.$item.')';
                        }
                      }*/

                      //$inline_id_comm_prep = implode(';', $tab_id_comm_prep);
                      $inline_id_comm_prep = implode('-', $tab_id_comm_prep);
                      $onclick = "print_etiquettes('".$inline_id_comm_prep."')";


                    	$this->page_header_toolbar_btn['imprime_etq'] = array(
                    		'desc' => $this->l('Impression des etq Colissimo ('.$cpt_etq.')', null, null, false),
                    		'icon' => 'process-icon-print',
                        //'js' => $inline_id_comm_prep
                        'js' => $onclick
                    	);


                }

                if (empty($this->display))
			$this->page_header_toolbar_btn['new_order'] = array(
				'href' => self::$currentIndex.'&addorder&token='.$this->token,
				'desc' => $this->l('Add new order', null, null, false),
				'icon' => 'process-icon-new'
			);

		if ($this->display == 'add')
			unset($this->page_header_toolbar_btn['save']);

		if (Context::getContext()->shop->getContext() != Shop::CONTEXT_SHOP && isset($this->page_header_toolbar_btn['new_order'])
			&& Shop::isFeatureActive())
			unset($this->page_header_toolbar_btn['new_order']);
	}



        public function renderView()
	{
		$order = new Order(Tools::getValue('id_order'));
		if (!Validate::isLoadedObject($order))
			$this->errors[] = Tools::displayError('The order cannot be found within your database.');

		$customer = new Customer($order->id_customer);
		$carrier = new Carrier($order->id_carrier);
		$products = $this->getProducts($order);
		$currency = new Currency((int)$order->id_currency);
		// Carrier module call
		$carrier_module_call = null;
		if ($carrier->is_module)
		{
			$module = Module::getInstanceByName($carrier->external_module_name);
			if (method_exists($module, 'displayInfoByCart'))
				$carrier_module_call = call_user_func(array($module, 'displayInfoByCart'), $order->id_cart);
		}

		// Retrieve addresses information
		$addressInvoice = new Address($order->id_address_invoice, $this->context->language->id);
		if (Validate::isLoadedObject($addressInvoice) && $addressInvoice->id_state)
			$invoiceState = new State((int)$addressInvoice->id_state);

		if ($order->id_address_invoice == $order->id_address_delivery)
		{
			$addressDelivery = $addressInvoice;
			if (isset($invoiceState))
				$deliveryState = $invoiceState;
		}
		else
		{
			$addressDelivery = new Address($order->id_address_delivery, $this->context->language->id);
			if (Validate::isLoadedObject($addressDelivery) && $addressDelivery->id_state)
				$deliveryState = new State((int)($addressDelivery->id_state));
		}

		$this->toolbar_title = sprintf($this->l('Order #%1$d (%2$s) - %3$s %4$s'), $order->id, $order->reference, $customer->firstname, $customer->lastname);
		if (Shop::isFeatureActive())
		{
			$shop = new Shop((int)$order->id_shop);
			$this->toolbar_title .= ' - '.sprintf($this->l('Shop: %s'), $shop->name);
		}

		// gets warehouses to ship products, if and only if advanced stock management is activated
		$warehouse_list = null;

		$order_details = $order->getOrderDetailList();
		foreach ($order_details as $order_detail)
		{
			$product = new Product($order_detail['product_id']);

			if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT')
				&& $product->advanced_stock_management)
			{
				$warehouses = Warehouse::getWarehousesByProductId($order_detail['product_id'], $order_detail['product_attribute_id']);
				foreach ($warehouses as $warehouse)
				{
					if (!isset($warehouse_list[$warehouse['id_warehouse']]))
						$warehouse_list[$warehouse['id_warehouse']] = $warehouse;
				}
			}
		}

		$payment_methods = array();
		foreach (PaymentModule::getInstalledPaymentModules() as $payment)
		{
			$module = Module::getInstanceByName($payment['name']);
			if (Validate::isLoadedObject($module) && $module->active)
				$payment_methods[] = $module->displayName;
		}

		// display warning if there are products out of stock
		$display_out_of_stock_warning = false;
		$current_order_state = $order->getCurrentOrderState();
		if (Configuration::get('PS_STOCK_MANAGEMENT') && (!Validate::isLoadedObject($current_order_state) || ($current_order_state->delivery != 1 && $current_order_state->shipped != 1)))
			$display_out_of_stock_warning = true;

		// products current stock (from stock_available)

		foreach ($products as &$product)
		{
			$product['current_stock'] = StockAvailable::getQuantityAvailableByProduct($product['product_id'], $product['product_attribute_id'], $product['id_shop']);

			$resume = OrderSlip::getProductSlipResume($product['id_order_detail']);
			$product['quantity_refundable'] = $product['product_quantity'] - $resume['product_quantity'];
			$product['amount_refundable'] = $product['total_price_tax_incl'] - $resume['amount_tax_incl'];
			$product['amount_refund'] = Tools::displayPrice($resume['amount_tax_incl'], $currency);
			$product['refund_history'] = OrderSlip::getProductSlipDetail($product['id_order_detail']);
			$product['return_history'] = OrderReturn::getProductReturnDetail($product['id_order_detail']);
                        $declinaison = new Combination($product['product_attribute_id'], null, $product['id_shop']);
                        if(!empty($declinaison->location))
                        {
                            $product['location'] = $declinaison->location;
                        }

			// if the current stock requires a warning
			if ($product['current_stock'] == 0 && $display_out_of_stock_warning)
				$this->displayWarning($this->l('This product is out of stock: ').' '.$product['product_name']);
			if ($product['id_warehouse'] != 0)
			{
				$warehouse = new Warehouse((int)$product['id_warehouse']);
				$product['warehouse_name'] = $warehouse->name;
			}
			else
				$product['warehouse_name'] = '--';
		}
                //usort($products, array($this, 'tri_location'));

		$gender = new Gender((int)$customer->id_gender, $this->context->language->id);

		$history = $order->getHistory($this->context->language->id);


               /*  if ( $order->id == '38705' )
                {
                    echo '<pre>';
                    echo count($products);
                    echo '</pre>';
                } */

                /* $listProduct = array();
                foreach($products as $key => $value)
                {
                    if(strlen(trim($value['reference'])) == 0)
                    {
                         $listProduct[] = $value;
                    }
                    else
                    {
                        $listProduct[str_replace('-','',$value['reference'])] = $value;
                    }
                }
                if ( $order->id == '38705' )
                {
                    echo '<pre>';
                    echo count($listProduct);
                    echo '</pre>';
                }

                $products = $listProduct;  */
               /*  foreach($products as $key => $value)
                {
                    echo $key.'<br />';
                } */
                ksort($products);
		foreach ($history as &$order_state)
			$order_state['text-color'] = Tools::getBrightness($order_state['color']) < 128 ? 'white' : 'black';

		// Smarty assign
		$this->tpl_view_vars = array(
			'order' => $order,
			'cart' => new Cart($order->id_cart),
			'customer' => $customer,
			'gender' => $gender,
			'customer_addresses' => $customer->getAddresses($this->context->language->id),
			'addresses' => array(
				'delivery' => $addressDelivery,
				'deliveryState' => isset($deliveryState) ? $deliveryState : null,
				'invoice' => $addressInvoice,
				'invoiceState' => isset($invoiceState) ? $invoiceState : null
			),
			'customerStats' => $customer->getStats(),
			'products' => $products,
			'discounts' => $order->getCartRules(),
			'orders_total_paid_tax_incl' => $order->getOrdersTotalPaid(), // Get the sum of total_paid_tax_incl of the order with similar reference
			'total_paid' => $order->getTotalPaid(),
			'returns' => OrderReturn::getOrdersReturn($order->id_customer, $order->id),
			'customer_thread_message' => CustomerThread::getCustomerMessages($order->id_customer),
			'orderMessages' => OrderMessage::getOrderMessages($order->id_lang),
			'messages' => Message::getMessagesByOrderId($order->id, true),
			'carrier' => new Carrier($order->id_carrier),
			'history' => $history,
			'states' => OrderState::getOrderStates($this->context->language->id),
			'warehouse_list' => $warehouse_list,
			'sources' => ConnectionsSource::getOrderSources($order->id),
			'currentState' => $order->getCurrentOrderState(),
			'currency' => new Currency($order->id_currency),
			'currencies' => Currency::getCurrenciesByIdShop($order->id_shop),
			'previousOrder' => $order->getPreviousOrderId(),
			'nextOrder' => $order->getNextOrderId(),
			'nextOrderLBG' => $order->getNextOrderIdLBG(),
			'current_index' => self::$currentIndex,
			'carrierModuleCall' => $carrier_module_call,
			'iso_code_lang' => $this->context->language->iso_code,
			'id_lang' => $this->context->language->id,
			'can_edit' => ($this->tabAccess['edit'] == 1),
			'current_id_lang' => $this->context->language->id,
			'invoices_collection' => $order->getInvoicesCollection(),
			'not_paid_invoices_collection' => $order->getNotPaidInvoicesCollection(),
			'payment_methods' => $payment_methods,
			'invoice_management_active' => Configuration::get('PS_INVOICE', null, null, $order->id_shop),
			'display_warehouse' => (int)Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT')
		);
		$ets_payment_with_fee = Module::getInstanceByName('ets_payment_with_fee');
        $ets_payment_with_fee->renderViewOrder($this->tpl_view_vars);
        return AdminController::renderView();
	}

        public function tri_location($a, $b)
        {
            if($a['location'] == $b['location'])
            {
                return 0;
            }
            else if ($a['location'] > $b['location'])
            {
                return 1;
            }
            else
            {
                return -1;
            }
        }
    }
?>
