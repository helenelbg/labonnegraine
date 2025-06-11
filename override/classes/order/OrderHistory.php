<?php
use PrestaShop\PrestaShop\Adapter\MailTemplate\MailPartialTemplateRenderer;
use PrestaShop\PrestaShop\Adapter\StockManager as StockManagerAdapter;
use PrestaShop\PrestaShop\Core\Stock\StockManager;

class OrderHistory extends OrderHistoryCore
{
    public static function getLastOrderState($id_order)
	{
		Tools::displayAsDeprecated();
		$id_order_state = Db::getInstance()->getValue('
		SELECT `id_order_state`
		FROM `'._DB_PREFIX_.'order_history`
		WHERE `id_order` = '.(int)$id_order.'
		ORDER BY `date_add` DESC, `id_order_history` DESC');

		// returns false if there is no state
		if (!$id_order_state)
			return false;

		// else, returns an OrderState object
		return new OrderState($id_order_state, Configuration::get('PS_LANG_DEFAULT'));
	}
	
	public function changeIdOrderState($new_order_state, $id_order, $use_existing_payment = false)
    {
        if (!$new_order_state || !$id_order) {
            return;
        }

        if (!is_object($id_order) && is_numeric($id_order)) {
            $order = new Order((int) $id_order);
        } elseif (is_object($id_order)) {
            $order = $id_order;
        } else {
            return;
        }

        ShopUrl::cacheMainDomainForShop($order->id_shop);

        $new_os = new OrderState((int) $new_order_state, $order->id_lang);
        $old_os = new OrderState((int) $order->current_state, $order->id_lang);

        // executes hook
        if (in_array($new_os->id, [Configuration::get('PS_OS_PAYMENT'), Configuration::get('PS_OS_WS_PAYMENT')])) {
            Hook::exec('actionPaymentConfirmation', ['id_order' => (int) $order->id], null, false, true, false, $order->id_shop);
        }

        // executes hook
        Hook::exec('actionOrderStatusUpdate', [
            'newOrderStatus' => $new_os,
            'oldOrderStatus' => $old_os,
            'id_order' => (int) $order->id,
        ], null, false, true, false, $order->id_shop);

        if (Validate::isLoadedObject($order) && $new_os instanceof OrderState) {
            $context = Context::getContext();

            // An email is sent the first time a virtual item is validated
            $virtual_products = $order->getVirtualProducts();
            if ($virtual_products && !$old_os->logable && $new_os->logable) {
                $assign = [];
                foreach ($virtual_products as $key => $virtual_product) {
                    $id_product_download = ProductDownload::getIdFromIdProduct($virtual_product['product_id']);
                    $product_download = new ProductDownload($id_product_download);
                    // If this virtual item has an associated file, we'll provide the link to download the file in the email
                    if ($product_download->display_filename != '') {
                        $assign[$key]['name'] = $product_download->display_filename;
                        $dl_link = $product_download->getTextLink(false, $virtual_product['download_hash'])
                            . '&id_order=' . (int) $order->id
                            . '&secure_key=' . $order->secure_key;
                        $assign[$key]['link'] = $dl_link;
                        if (isset($virtual_product['download_deadline']) && $virtual_product['download_deadline'] != '0000-00-00 00:00:00') {
                            $assign[$key]['deadline'] = Tools::displayDate($virtual_product['download_deadline']);
                        }
                        if ($product_download->nb_downloadable != 0) {
                            $assign[$key]['downloadable'] = (int) $product_download->nb_downloadable;
                        }
                    }
                }

                $customer = new Customer((int) $order->id_customer);
                $links = [];
                foreach ($assign as $product) {
                    $complementaryText = [];
                    if (isset($product['deadline'])) {
                        $complementaryText[] = $this->trans('expires on %s.', [$product['deadline']], 'Admin.Orderscustomers.Notification');
                    }
                    if (isset($product['downloadable'])) {
                        $complementaryText[] = $this->trans('downloadable %d time(s)', [(int) $product['downloadable']], 'Admin.Orderscustomers.Notification');
                    }
                    $links[] = [
                        'text' => Tools::htmlentitiesUTF8($product['name']),
                        'url' => $product['link'],
                        'complementary_text' => implode(' ', $complementaryText),
                    ];
                }

                $context = Context::getContext();
                $partialRenderer = new MailPartialTemplateRenderer($context->smarty);

                $links_txt = $partialRenderer->render('download_product_virtual_products.txt', $context->language, $links, true);
                $links_html = $partialRenderer->render('download_product_virtual_products.tpl', $context->language, $links);

                $data = [
                    '{lastname}' => $customer->lastname,
                    '{firstname}' => $customer->firstname,
                    '{id_order}' => (int) $order->id,
                    '{order_name}' => $order->getUniqReference(),
                    '{nbProducts}' => count($virtual_products),
                    '{virtualProducts}' => $links_html,
                    '{virtualProductsTxt}' => $links_txt,
                ];
                // If there is at least one downloadable file
                if (!empty($assign)) {
                    $orderLanguage = new Language((int) $order->id_lang);
                    Mail::Send(
                        (int) $order->id_lang,
                        'download_product',
                        Context::getContext()->getTranslator()->trans(
                            'The virtual product that you bought is available for download',
                            [],
                            'Emails.Subject',
                            $orderLanguage->locale
                        ),
                        $data,
                        $customer->email,
                        $customer->firstname . ' ' . $customer->lastname,
                        null,
                        null,
                        null,
                        null,
                        _PS_MAIL_DIR_,
                        false,
                        (int) $order->id_shop
                    );
                }
            }

            /** @since 1.5.0 : gets the stock manager */
            $manager = null;
            if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT')) {
                $manager = StockManagerFactory::getManager();
            }

            $error_or_canceled_statuses = [Configuration::get('PS_OS_ERROR'), Configuration::get('PS_OS_CANCELED')];

            $employee = null;
            if (!(int) $this->id_employee || !Validate::isLoadedObject(($employee = new Employee((int) $this->id_employee)))) {
                if (!Validate::isLoadedObject($old_os) && $context != null) {
                    // First OrderHistory, there is no $old_os, so $employee is null before here
                    $employee = $context->employee; // filled if from BO and order created (because no old_os)
                    if ($employee) {
                        $this->id_employee = $employee->id;
                    }
                } else {
                    $employee = null;
                }
            }

            // foreach products of the order
			$prodDetails = $order->getProductsDetail();
            foreach ($prodDetails as $product) {
                if (Validate::isLoadedObject($old_os)) {
                    // if becoming logable => adds sale
                    if ($new_os->logable && !$old_os->logable) {
                        ProductSale::addProductSale($product['product_id'], $product['product_quantity']);
                        // @since 1.5.0 - Stock Management
                        if (!Pack::isPack($product['product_id']) &&
                            in_array($old_os->id, $error_or_canceled_statuses) &&
                            !StockAvailable::dependsOnStock($product['id_product'], (int) $order->id_shop)) {
                            StockAvailable::updateQuantity($product['product_id'], $product['product_attribute_id'], -(int) $product['product_quantity'], $order->id_shop);
                        }
                    } elseif (!$new_os->logable && $old_os->logable) {
                        // if becoming unlogable => removes sale
                        ProductSale::removeProductSale($product['product_id'], $product['product_quantity']);

                        // @since 1.5.0 - Stock Management
                        if (!Pack::isPack($product['product_id']) &&
                            in_array($new_os->id, $error_or_canceled_statuses) &&
                            !StockAvailable::dependsOnStock($product['id_product'])) {
                            StockAvailable::updateQuantity($product['product_id'], $product['product_attribute_id'], (int) $product['product_quantity'], $order->id_shop);
                        }
                    } elseif (!$new_os->logable && !$old_os->logable &&
                        in_array($new_os->id, $error_or_canceled_statuses) &&
                        !in_array($old_os->id, $error_or_canceled_statuses) &&
                        !StockAvailable::dependsOnStock($product['id_product'])
                    ) {
                        // if waiting for payment => payment error/canceled
                        StockAvailable::updateQuantity($product['product_id'], $product['product_attribute_id'], (int) $product['product_quantity'], $order->id_shop);
                    }
                }
                // From here, there is 2 cases : $old_os exists, and we can test shipped state evolution,
                // Or old_os does not exists, and we should consider that initial shipped state is 0 (to allow decrease of stocks)

                // @since 1.5.0 : if the order is being shipped and this products uses the advanced stock management :
                // decrements the physical stock using $id_warehouse
                if ($new_os->shipped == 1 && (!Validate::isLoadedObject($old_os) || $old_os->shipped == 0) &&
                    Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT') &&
                    Warehouse::exists($product['id_warehouse']) &&
                    $manager != null &&
                    (int) $product['advanced_stock_management'] == 1) {
                    // gets the warehouse
                    $warehouse = new Warehouse($product['id_warehouse']);

                    // decrements the stock (if it's a pack, the StockManager does what is needed)
                    $manager->removeProduct(
                        $product['product_id'],
                        $product['product_attribute_id'],
                        $warehouse,
                        ($product['product_quantity'] - $product['product_quantity_refunded'] - $product['product_quantity_return']),
                        (int) Configuration::get('PS_STOCK_CUSTOMER_ORDER_REASON'),
                        true,
                        (int) $order->id
                    );
                } elseif ($new_os->shipped == 0 && Validate::isLoadedObject($old_os) && $old_os->shipped == 1 &&
                    Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT') &&
                    Warehouse::exists($product['id_warehouse']) &&
                    $manager != null &&
                    (int) $product['advanced_stock_management'] == 1
                ) {
                    // @since.1.5.0 : if the order was shipped, and is not anymore, we need to restock products

                    // if the product is a pack, we restock every products in the pack using the last negative stock mvts
                    if (Pack::isPack($product['product_id'])) {
                        $pack_products = Pack::getItems($product['product_id'], Configuration::get('PS_LANG_DEFAULT', null, null, $order->id_shop));
                        foreach ($pack_products as $pack_product) {
                            if ($pack_product->advanced_stock_management == 1) {
                                $mvts = StockMvt::getNegativeStockMvts($order->id, $pack_product->id, 0, $pack_product->pack_quantity * $product['product_quantity']);
                                foreach ($mvts as $mvt) {
                                    $manager->addProduct(
                                        $pack_product->id,
                                        0,
                                        new Warehouse($mvt['id_warehouse']),
                                        $mvt['physical_quantity'],
                                        null,
                                        $mvt['price_te'],
                                        true,
                                        null
                                    );
                                }
                                if (!StockAvailable::dependsOnStock($product['id_product'])) {
                                    StockAvailable::updateQuantity($pack_product->id, 0, (int) $pack_product->pack_quantity * $product['product_quantity'], $order->id_shop);
                                }
                            }
                        }
                    } else {
                        // else, it's not a pack, re-stock using the last negative stock mvts

                        $mvts = StockMvt::getNegativeStockMvts(
                            $order->id,
                            $product['product_id'],
                            $product['product_attribute_id'],
                            ($product['product_quantity'] - $product['product_quantity_refunded'] - $product['product_quantity_return'])
                        );

                        foreach ($mvts as $mvt) {
                            $manager->addProduct(
                                $product['product_id'],
                                $product['product_attribute_id'],
                                new Warehouse($mvt['id_warehouse']),
                                $mvt['physical_quantity'],
                                null,
                                $mvt['price_te'],
                                true
                            );
                        }
                    }
                }

                // Save movement if :
                // not Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT')
                // new_os->shipped != old_os->shipped
                if (Validate::isLoadedObject($old_os) && Validate::isLoadedObject($new_os) && $new_os->shipped != $old_os->shipped && !Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT')) {
                    $product_quantity = (int) ($product['product_quantity'] - $product['product_quantity_refunded'] - $product['product_quantity_return']);

                    if ($product_quantity > 0) {
                        $current_shop_context_type = Context::getContext()->shop->getContextType();
                        if ($current_shop_context_type !== Shop::CONTEXT_SHOP) {
                            //change to order shop context
                            $current_shop_group_id = Context::getContext()->shop->getContextShopGroupID();
                            Context::getContext()->shop->setContext(Shop::CONTEXT_SHOP, $order->id_shop);
                        }
                        (new StockManager())->saveMovement(
                            (int) $product['product_id'],
                            (int) $product['product_attribute_id'],
                            (int) $product_quantity * ($new_os->shipped == 1 ? -1 : 1),
                            [
                                'id_order' => $order->id,
                                'id_stock_mvt_reason' => ($new_os->shipped == 1 ? Configuration::get('PS_STOCK_CUSTOMER_ORDER_REASON') : Configuration::get('PS_STOCK_CUSTOMER_ORDER_CANCEL_REASON')),
                            ]
                        );
                        //back to current shop context
                        if ($current_shop_context_type !== Shop::CONTEXT_SHOP && isset($current_shop_group_id)) {
                            Context::getContext()->shop->setContext($current_shop_context_type, $current_shop_group_id);
                        }
                    }
                }
            }
        }

		/* OVERRIDE POUR CREATION AUTOMATIQUE DE BON D'ACHAT */
		/* Opération Pommes de terre : pour 25€ de pdt acheté, un bon d'achat de 5€ offert, valable jusqu'au 06/06/2025 */
		$currentDate = $order->date_add;
		$currentDate = date('Y-m-d H:i:s', strtotime($currentDate));

		$startDate = date('Y-m-d H:i:s', strtotime("2025-03-04 00:00:00"));
		$endDate = date('Y-m-d H:i:s', strtotime("2025-03-06 23:59:59"));
		if ( $new_order_state == 2 && (($currentDate >= $startDate) && ($currentDate <= $endDate)) )
		{
			if ( !CartRule::cartRuleExists('PDT25'.$order->id_customer) )
			{
                // Test si le client a utilisé le code lors de sa commande
				$testCode = Db::getInstance()->executeS('SELECT id_order_cart_rule FROM `'._DB_PREFIX_.'order_cart_rule` WHERE id_order = "'.$order->id.'" AND id_cart_rule = "791623";');
                if ( isset($testCode[0]['id_order_cart_rule']) )
                {
                    $totalSelected = 0;
					$cptPdt = 0;
                    foreach ($prodDetails as $product) 
                    {
                        // Catégories Pdt 231, 229, 108, 163
                        if ( ( Product::idIsOnCategoryId($product['product_id'], array(array('id_category' => 231))) ) || ( Product::idIsOnCategoryId($product['product_id'], array(array('id_category' => 229))) ) || ( Product::idIsOnCategoryId($product['product_id'], array(array('id_category' => 108))) ) || ( Product::idIsOnCategoryId($product['product_id'], array(array('id_category' => 163))) ) )
                        {
							$cptPdt++;
                            $totalSelected += $product['total_price_tax_incl'];
                        }
                    }
					if ( $totalSelected >= 25 && $cptPdt > 0 )
                    {
                        // Création du bon d'achat pour le client si il est abonné à la newsletter
                        $customerEC = new Customer((int) $order->id_customer);
                        //if ( $customerEC->newsletter == 1 )
                        //{
                            $returned_discount = new CartRule();
                            $returned_discount->code = 'PDT25'.$order->id_customer;
                            $returned_discount->name[1] = 'Bon d\'achat de 5€';
                            $returned_discount->id_customer = (int)($order->id_customer);
                            $returned_discount->id_group = 0;
                            $returned_discount->id_currency = (int)($order->id_currency);
                            $returned_discount->quantity = 1;
                            $returned_discount->quantity_per_user = 1;
                            $returned_discount->priority = 1;
                            $returned_discount->partial_use = 0;
                            $returned_discount->active = 1;
                            $returned_discount->minimum_amount = 0;
                            $returned_discount->minimum_amount_tax = 0;
                            $returned_discount->minimum_amount_currency = (int)$order->id_currency;
                            $returned_discount->minimum_amount_shipping = 0;
                            $returned_discount->highlight = 0;
                            $returned_discount->reduction_currency = (int)$order->id_currency;
                            $returned_discount->reduction_tax = 1;
                            $returned_discount->free_shipping = 0;
                            $returned_discount->reduction_amount = 5;
                            $returned_discount->reduction_percent = 0;
                            $hashV = Tools::displayPrice($returned_discount->reduction_amount);
                            $returned_discount->date_from = $startDate;
                            $returned_discount->date_to = '2025-06-06 23:59:59';
                            $returned_discount->shop_restriction = 1;
                            $returned_discount->product_restriction = 0;
                            $returned_discount->reduction_product = 0;

                            $returned_discount->save();

                            $vars = [
                                '{code}' => 'PDT25'.$order->id_customer,
                                '{firstname}' => $customerEC->firstname,
                                '{montant}' => '5,00€',
                                '{date_fin}' => '06/06/2025',
                            ];
                            
                            Mail::Send(
                                1,
                                'bon_achat',
                                /*Context::getContext()->getTranslator()->trans(
                                    'Your guest account has been transformed into a customer account',
                                    [],
                                    'Emails.Subject',
                                    $language->locale
                                ),*/
                                'Votre bon d\'achat est disponible, ne manquez pas cette opportunité !',
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

                            Db::getInstance()->execute('
                                INSERT INTO `'._DB_PREFIX_.'cart_rule_shop` (`id_cart_rule`, `id_shop`)
                                VALUES ('.(int)$returned_discount->id.', '.(int)Context::getContext()->shop->id.')');
                        //}
                    }
                }
			}
		}
		/* FIN */

        /* OVERRIDE POUR CREATION AUTOMATIQUE DE BON D'ACHAT */
		/* DEBUT 01/10/2024 au 03/10/2024 > Opération Bahco : pour 50€ de produits Bahco acheté, un bon d'achat de 20€ offert (sauf accessoires), valable jusqu'au 31/12/2024
		$currentDate = $order->date_add;
		$currentDate = date('Y-m-d H:i:s', strtotime($currentDate));

		$startDate = date('Y-m-d H:i:s', strtotime("2024-10-01 06:00:00"));
		$endDate = date('Y-m-d H:i:s', strtotime("2024-10-03 23:59:59"));
		if ( $new_order_state == 2 && (($currentDate >= $startDate) && ($currentDate <= $endDate)) )
		{
			if ( !CartRule::cartRuleExists('BAH'.$order->id_customer) )
			{
				// Test si le client a utilisé le code lors de sa commande
				$testCode = Db::getInstance()->executeS('SELECT id_order_cart_rule FROM `'._DB_PREFIX_.'order_cart_rule` WHERE id_order = "'.$order->id.'" AND id_cart_rule = "799165";');
                if ( isset($testCode[0]['id_order_cart_rule']) )
                {
					$totalSelected = 0;
					$cptPdt = 0;
					foreach ($prodDetails as $product) 
					{
						// Catégorie Bahco 313
						if ( Product::idIsOnCategoryId($product['product_id'], array(array('id_category' => 313))) )
						{
							$cptPdt++;
						}
						$totalSelected += $product['total_price_tax_incl'];
					}
					if ( $totalSelected >= 50 && $cptPdt > 0 )
					{
						// Création du bon d'achat pour le client si il est abonné à la newsletter
						$customerEC = new Customer((int) $order->id_customer);
						if ( $customerEC->newsletter == 1 )
						{
							$returned_discount = new CartRule();
							$returned_discount->code = 'BAH'.$order->id_customer;
							$returned_discount->name[1] = 'Bon d\'achat de 15€';
							$returned_discount->id_customer = (int)($order->id_customer);
							$returned_discount->id_group = 0;
							$returned_discount->id_currency = (int)($order->id_currency);
							$returned_discount->quantity = 1;
							$returned_discount->quantity_per_user = 1;
							$returned_discount->priority = 1;
							$returned_discount->partial_use = 0;
							$returned_discount->active = 1;
							$returned_discount->minimum_amount = 0;
							$returned_discount->minimum_amount_tax = 0;
							$returned_discount->minimum_amount_currency = (int)$order->id_currency;
							$returned_discount->minimum_amount_shipping = 0;
							$returned_discount->highlight = 0;
							$returned_discount->reduction_currency = (int)$order->id_currency;
							$returned_discount->reduction_tax = 1;
							$returned_discount->free_shipping = 0;
							$returned_discount->reduction_amount = 15;
							$returned_discount->reduction_percent = 0;
							$hashV = Tools::displayPrice($returned_discount->reduction_amount);
							$returned_discount->date_from = $startDate;
							$returned_discount->date_to = '2024-12-31 23:59:59';
							$returned_discount->shop_restriction = 1;
							$returned_discount->product_restriction = 1;
							$returned_discount->reduction_product = -2;

							$returned_discount->save();

							$vars = [
								'{code}' => 'BAH'.$order->id_customer,
								'{firstname}' => $customerEC->firstname,
								'{montant}' => '15,00€',
								'{date_fin}' => '31/12/2024',
							];
							
							Mail::Send(
								1,
								'bon_achat',
								'Votre bon d\'achat est disponible, ne manquez pas cette opportunité !',
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

							Db::getInstance()->execute('
								INSERT INTO `'._DB_PREFIX_.'cart_rule_product_rule_group` (`id_cart_rule`, `quantity`)
								VALUES ('.(int)$returned_discount->id.', 1)');
							$id_product_rule_group = Db::getInstance()->Insert_ID();

							Db::getInstance()->execute('
								INSERT INTO `'._DB_PREFIX_.'cart_rule_product_rule` (`id_product_rule_group`, `type`)
								VALUES ('.(int)$id_product_rule_group.', "categories")');
							$id_product_rule = Db::getInstance()->Insert_ID();

							Db::getInstance()->execute('
								INSERT INTO `'._DB_PREFIX_.'cart_rule_product_rule_value`  (`id_product_rule`, `id_item`) VALUES
								('.$id_product_rule.', 18),
								('.$id_product_rule.', 19),
								('.$id_product_rule.', 20),
								('.$id_product_rule.', 21),
								('.$id_product_rule.', 22),
								('.$id_product_rule.', 26),
								('.$id_product_rule.', 27),
								('.$id_product_rule.', 28),
								('.$id_product_rule.', 33),
								('.$id_product_rule.', 45),
								('.$id_product_rule.', 46),
								('.$id_product_rule.', 48),
								('.$id_product_rule.', 50),
								('.$id_product_rule.', 54),
								('.$id_product_rule.', 60),
								('.$id_product_rule.', 61),
								('.$id_product_rule.', 62),
								('.$id_product_rule.', 63),
								('.$id_product_rule.', 64),
								('.$id_product_rule.', 67),
								('.$id_product_rule.', 68),
								('.$id_product_rule.', 69),
								('.$id_product_rule.', 70),
								('.$id_product_rule.', 71),
								('.$id_product_rule.', 72),
								('.$id_product_rule.', 75),
								('.$id_product_rule.', 76),
								('.$id_product_rule.', 77),
								('.$id_product_rule.', 78),
								('.$id_product_rule.', 79),
								('.$id_product_rule.', 80),
								('.$id_product_rule.', 81),
								('.$id_product_rule.', 82),
								('.$id_product_rule.', 89),
								('.$id_product_rule.', 91),
								('.$id_product_rule.', 92),
								('.$id_product_rule.', 93),
								('.$id_product_rule.', 96),
								('.$id_product_rule.', 97),
								('.$id_product_rule.', 98),
								('.$id_product_rule.', 99),
								('.$id_product_rule.', 100),
								('.$id_product_rule.', 101),
								('.$id_product_rule.', 102),
								('.$id_product_rule.', 106),
								('.$id_product_rule.', 108),
								('.$id_product_rule.', 112),
								('.$id_product_rule.', 113),
								('.$id_product_rule.', 114),
								('.$id_product_rule.', 115),
								('.$id_product_rule.', 116),
								('.$id_product_rule.', 117),
								('.$id_product_rule.', 118),
								('.$id_product_rule.', 119),
								('.$id_product_rule.', 120),
								('.$id_product_rule.', 121),
								('.$id_product_rule.', 122),
								('.$id_product_rule.', 123),
								('.$id_product_rule.', 124),
								('.$id_product_rule.', 125),
								('.$id_product_rule.', 126),
								('.$id_product_rule.', 127),
								('.$id_product_rule.', 128),
								('.$id_product_rule.', 129),
								('.$id_product_rule.', 131),
								('.$id_product_rule.', 132),
								('.$id_product_rule.', 133),
								('.$id_product_rule.', 134),
								('.$id_product_rule.', 135),
								('.$id_product_rule.', 136),
								('.$id_product_rule.', 137),
								('.$id_product_rule.', 138),
								('.$id_product_rule.', 140),
								('.$id_product_rule.', 141),
								('.$id_product_rule.', 143),
								('.$id_product_rule.', 145),
								('.$id_product_rule.', 146),
								('.$id_product_rule.', 147),
								('.$id_product_rule.', 148),
								('.$id_product_rule.', 149),
								('.$id_product_rule.', 151),
								('.$id_product_rule.', 152),
								('.$id_product_rule.', 153),
								('.$id_product_rule.', 154),
								('.$id_product_rule.', 155),
								('.$id_product_rule.', 163),
								('.$id_product_rule.', 168),
								('.$id_product_rule.', 169),
								('.$id_product_rule.', 170),
								('.$id_product_rule.', 171),
								('.$id_product_rule.', 172),
								('.$id_product_rule.', 173),
								('.$id_product_rule.', 174),
								('.$id_product_rule.', 175),
								('.$id_product_rule.', 176),
								('.$id_product_rule.', 177),
								('.$id_product_rule.', 181),
								('.$id_product_rule.', 182),
								('.$id_product_rule.', 183),
								('.$id_product_rule.', 184),
								('.$id_product_rule.', 191),
								('.$id_product_rule.', 195),
								('.$id_product_rule.', 203),
								('.$id_product_rule.', 206),
								('.$id_product_rule.', 207),
								('.$id_product_rule.', 209),
								('.$id_product_rule.', 210),
								('.$id_product_rule.', 211),
								('.$id_product_rule.', 213),
								('.$id_product_rule.', 229),
								('.$id_product_rule.', 230),
								('.$id_product_rule.', 231),
								('.$id_product_rule.', 232),
								('.$id_product_rule.', 233),
								('.$id_product_rule.', 244),
								('.$id_product_rule.', 246),
								('.$id_product_rule.', 248),
								('.$id_product_rule.', 252),
								('.$id_product_rule.', 253),
								('.$id_product_rule.', 256),
								('.$id_product_rule.', 257),
								('.$id_product_rule.', 258),
								('.$id_product_rule.', 261),
								('.$id_product_rule.', 263),
								('.$id_product_rule.', 284),
								('.$id_product_rule.', 285),
								('.$id_product_rule.', 289),
								('.$id_product_rule.', 290),
								('.$id_product_rule.', 291),
								('.$id_product_rule.', 292),
								('.$id_product_rule.', 293),
								('.$id_product_rule.', 294),
								('.$id_product_rule.', 299),
								('.$id_product_rule.', 320),
								('.$id_product_rule.', 323),
								('.$id_product_rule.', 324),
								('.$id_product_rule.', 325),
								('.$id_product_rule.', 326),
								('.$id_product_rule.', 327),
								('.$id_product_rule.', 328),
								('.$id_product_rule.', 329),
								('.$id_product_rule.', 330),
								('.$id_product_rule.', 332),
								('.$id_product_rule.', 333),
								('.$id_product_rule.', 334),
								('.$id_product_rule.', 337),
								('.$id_product_rule.', 338),
								('.$id_product_rule.', 339),
								('.$id_product_rule.', 340),
								('.$id_product_rule.', 341),
								('.$id_product_rule.', 342),
								('.$id_product_rule.', 343),
								('.$id_product_rule.', 344),
								('.$id_product_rule.', 345),
								('.$id_product_rule.', 346),
								('.$id_product_rule.', 347),
								('.$id_product_rule.', 348),
								('.$id_product_rule.', 349),
								('.$id_product_rule.', 350),
								('.$id_product_rule.', 351),
								('.$id_product_rule.', 352),
								('.$id_product_rule.', 353),
								('.$id_product_rule.', 354),
								('.$id_product_rule.', 355),
								('.$id_product_rule.', 356),
								('.$id_product_rule.', 357),
								('.$id_product_rule.', 358),
								('.$id_product_rule.', 359),
								('.$id_product_rule.', 360),
								('.$id_product_rule.', 361),
								('.$id_product_rule.', 362),
								('.$id_product_rule.', 363),
								('.$id_product_rule.', 364),
								('.$id_product_rule.', 365),
								('.$id_product_rule.', 366),
								('.$id_product_rule.', 367),
								('.$id_product_rule.', 368),
								('.$id_product_rule.', 369),
								('.$id_product_rule.', 370),
								('.$id_product_rule.', 371),
								('.$id_product_rule.', 372),
								('.$id_product_rule.', 373),
								('.$id_product_rule.', 374),
								('.$id_product_rule.', 375),
								('.$id_product_rule.', 376),
								('.$id_product_rule.', 377),
								('.$id_product_rule.', 378),
								('.$id_product_rule.', 379),
								('.$id_product_rule.', 380),
								('.$id_product_rule.', 381),
								('.$id_product_rule.', 382),
								('.$id_product_rule.', 383),
								('.$id_product_rule.', 384),
								('.$id_product_rule.', 385),
								('.$id_product_rule.', 387),
								('.$id_product_rule.', 388),
								('.$id_product_rule.', 390),
								('.$id_product_rule.', 391),
								('.$id_product_rule.', 398),
								('.$id_product_rule.', 399),
								('.$id_product_rule.', 400),
								('.$id_product_rule.', 401),
								('.$id_product_rule.', 402),
								('.$id_product_rule.', 403),
								('.$id_product_rule.', 404),
								('.$id_product_rule.', 405),
								('.$id_product_rule.', 406),
								('.$id_product_rule.', 407)');

							Db::getInstance()->execute('
								INSERT INTO `'._DB_PREFIX_.'cart_rule_shop` (`id_cart_rule`, `id_shop`)
								VALUES ('.(int)$returned_discount->id.', '.(int)Context::getContext()->shop->id.')');
						}
					}
				}
			}
		}
		FIN */

        $this->id_order_state = (int) $new_order_state;

        // changes invoice number of order ?
        if (!Validate::isLoadedObject($new_os) || !Validate::isLoadedObject($order)) {
            throw new PrestaShopException($this->trans('Invalid new order status', [], 'Admin.Orderscustomers.Notification'));
        }

        // the order is valid if and only if the invoice is available and the order is not cancelled
        $order->current_state = $this->id_order_state;
        $order->valid = $new_os->logable;
        $order->update();

        if ($new_os->invoice && !$order->invoice_number) {
            $order->setInvoice($use_existing_payment);
        } elseif ($new_os->delivery && !$order->delivery_number) {
            $order->setDeliverySlip();
        }

        // set orders as paid
        if ($new_os->paid == 1) {
            if ($order->total_paid != 0) {
                $payment_method = Module::getInstanceByName($order->module);
            }

            $invoices = $order->getInvoicesCollection();
            foreach ($invoices as $invoice) {
                /** @var OrderInvoice $invoice */
                $rest_paid = $invoice->getRestPaid();
                if ($rest_paid > 0) {
                    $payment = new OrderPayment();
                    $payment->order_reference = Tools::substr($order->reference, 0, 9);
                    $payment->id_currency = $order->id_currency;
                    $payment->amount = $rest_paid;
                    $payment->payment_method = isset($payment_method) && $payment_method instanceof Module ? $payment_method->displayName : null;
                    $payment->conversion_rate = $order->conversion_rate;
                    $payment->save();

                    // Update total_paid_real value for backward compatibility reasons
                    $order->total_paid_real += $rest_paid;
                    $order->save();

                    Db::getInstance()->insert(
                        'order_invoice_payment',
                        [
                            'id_order_invoice' => (int) $invoice->id,
                            'id_order_payment' => (int) $payment->id,
                            'id_order' => (int) $order->id,
                        ]
                    );
                }
            }
        }

        // updates delivery date even if it was already set by another state change
        if ($new_os->delivery) {
            $order->setDelivery();
        }

        // executes hook
        Hook::exec('actionOrderStatusPostUpdate', [
            'newOrderStatus' => $new_os,
            'oldOrderStatus' => $old_os,
            'id_order' => (int) $order->id,
        ], null, false, true, false, $order->id_shop);

        // sync all stock
        (new StockManagerAdapter())->updatePhysicalProductQuantity(
            (int) $order->id_shop,
            (int) Configuration::get('PS_OS_ERROR'),
            (int) Configuration::get('PS_OS_CANCELED'),
            null,
            (int) $order->id
        );

        ShopUrl::resetMainDomainCache();
    }
}