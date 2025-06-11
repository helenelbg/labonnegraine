<?php
abstract class PaymentModule extends PaymentModuleCore
{
    /*
    * module: quantitydiscountpro
    * date: 2023-06-21 17:26:00
    * version: 2.1.43
    */
	
	/*
	* module: wkbundleproduct
    */
    public function validateOrder(
        $id_cart,
        $id_order_state,
        $amount_paid,
        $payment_method = 'Unknown',
        $message = null,
        $extra_vars = [],
        $currency_special = null,
        $dont_touch_amount = false,
        $secure_key = false,
        Shop $shop = null,
        ?string $order_reference = null
    ) {
        if (self::DEBUG_MODE) {
            PrestaShopLogger::addLog(
                'PaymentModule::validateOrder - Function called',
                1,
                null,
                'Cart',
                (int) $id_cart,
                true
            );
        }

        $this->context->cart = new Cart((int) $id_cart);
        $this->context->customer = new Customer((int) $this->context->cart->id_customer);
        $this->context->cart->setTaxCalculationMethod();

        $this->context->language = $this->context->cart->getAssociatedLanguage();
        $this->context->shop = ($shop ? $shop : new Shop((int) $this->context->cart->id_shop));
        ShopUrl::resetMainDomainCache();
        $id_currency = $currency_special ? (int) $currency_special : (int) $this->context->cart->id_currency;
        $this->context->currency = new Currency((int) $id_currency, null, (int) $this->context->shop->id);
        if (Configuration::get('PS_TAX_ADDRESS_TYPE') == 'id_address_delivery') {
            $context_country = $this->context->country;
        }

        $order_status = new OrderState((int) $id_order_state, (int) $this->context->language->id);
        if (!Validate::isLoadedObject($order_status)) {
            PrestaShopLogger::addLog(
                'PaymentModule::validateOrder - Order Status cannot be loaded',
                3,
                null,
                'Cart',
                (int) $id_cart,
                true
            );

            throw new PrestaShopException('Can\'t load Order status');
        }

        if (!$this->active) {
            PrestaShopLogger::addLog(
                'PaymentModule::validateOrder - Module is not active',
                3,
                null,
                'Cart',
                (int) $id_cart,
                true
            );
            exit(Tools::displayError());
        }

        $cart_is_loaded = Validate::isLoadedObject($this->context->cart);
        if (!$cart_is_loaded || $this->context->cart->OrderExists()) {
            $error = $this->trans('Cart cannot be loaded or an order has already been placed using this cart', [], 'Admin.Payment.Notification');
            PrestaShopLogger::addLog($error, 4, 1, 'Cart', (int) $this->context->cart->id);
            exit(Tools::displayError($error));
        }

        if ($secure_key !== false && $secure_key != $this->context->cart->secure_key) {
            PrestaShopLogger::addLog(
                'PaymentModule::validateOrder - Secure key does not match',
                3,
                null,
                'Cart',
                (int) $id_cart,
                true
            );
            exit(Tools::displayError());
        }

        $delivery_option_list = $this->context->cart->getDeliveryOptionList();
        $package_list = $this->context->cart->getPackageList();
        $cart_delivery_option = $this->context->cart->getDeliveryOption();

        foreach ($delivery_option_list as $id_address => $package) {
            if (!isset($cart_delivery_option[$id_address])
                || !array_key_exists($cart_delivery_option[$id_address], $package)
            ) {
                foreach ($package as $key => $val) {
                    $cart_delivery_option[$id_address] = $key;

                    break;
                }
            }
        }

        $order_list = [];
        $order_detail_list = [];

        if ($order_reference === null) {
            do {
                $reference = Order::generateReference();
            } while (Order::getByReference($reference)->count());
        } else {
            $reference = $order_reference;
        }

        $this->currentOrderReference = $reference;

        $cart_total_paid = (float) Tools::ps_round(
            (float) $this->context->cart->getOrderTotal(true, Cart::BOTH),
            Context::getContext()->getComputingPrecision()
        );

        foreach ($cart_delivery_option as $id_address => $key_carriers) {
            foreach ($delivery_option_list[$id_address][$key_carriers]['carrier_list'] as $id_carrier => $data) {
                foreach ($data['package_list'] as $id_package) {
                    $package_list[$id_address][$id_package]['id_warehouse'] = (int) $this->context->cart->getPackageIdWarehouse($package_list[$id_address][$id_package], (int) $id_carrier);
                    $package_list[$id_address][$id_package]['id_carrier'] = $id_carrier;
                }
            }
        }
        CartRule::cleanCache();
        $cart_rules = $this->context->cart->getCartRules();
        foreach ($cart_rules as $cart_rule) {
            $rule = new CartRule((int) $cart_rule['obj']->id);
            if (Validate::isLoadedObject($rule)) {
                if ($error = $rule->checkValidity($this->context, true, true)) {
                    $this->context->cart->removeCartRule((int) $rule->id);
                    if (isset($this->context->cookie, $this->context->cookie->id_customer)
                        && $this->context->cookie->id_customer
                        && !empty($rule->code)
                    ) {
                        Tools::redirect('index.php?controller=order&submitAddDiscount=1&discount_name=' . urlencode($rule->code));
                    } else {
                        $rule_name = isset($rule->name[(int) $this->context->cart->id_lang]) ? $rule->name[(int) $this->context->cart->id_lang] : $rule->code;
                        $error = $this->trans('The cart rule named "%1s" (ID %2s) used in this cart is not valid and has been withdrawn from cart', [$rule_name, (int) $rule->id], 'Admin.Payment.Notification');
                        PrestaShopLogger::addLog($error, 3, 2, 'Cart', (int) $this->context->cart->id);
                    }
                }
            }
        }

        $comp_precision = Context::getContext()->getComputingPrecision();
        if ($order_status->logable && (number_format($cart_total_paid, $comp_precision) !== number_format($amount_paid, $comp_precision))) {
            PrestaShopLogger::addLog(
                'PaymentModule::validateOrder - Total paid amount does not match cart total',
                3,
                null,
                'Cart',
                (int) $id_cart,
                true
            );
            $id_order_state = Configuration::get('PS_OS_ERROR');
        }

        foreach ($package_list as $id_address => $packageByAddress) {
            foreach ($packageByAddress as $id_package => $package) {
                $orderData = $this->createOrderFromCart(
                    $this->context->cart,
                    $this->context->currency,
                    $package['product_list'],
                    $id_address,
                    $this->context,
                    $reference,
                    $secure_key,
                    $payment_method,
                    $this->name,
                    $dont_touch_amount,
                    $amount_paid,
                    $package_list[$id_address][$id_package]['id_warehouse'],
                    $cart_total_paid,
                    self::DEBUG_MODE,
                    $order_status,
                    $id_order_state,
                    isset($package['id_carrier']) ? $package['id_carrier'] : null
                );
                $order = $orderData['order'];
                $order_list[] = $order;
                $order_detail_list[] = $orderData['orderDetail'];
            }
        }

        if (Configuration::get('PS_TAX_ADDRESS_TYPE') == 'id_address_delivery' && isset($context_country)) {
            $this->context->country = $context_country;
        }

        if (!$this->context->country->active) {
            PrestaShopLogger::addLog(
                'PaymentModule::validateOrder - Country is not active',
                3,
                null,
                'Cart',
                (int) $id_cart,
                true
            );

            throw new PrestaShopException('The order address country is not active.');
        }

        if (self::DEBUG_MODE) {
            PrestaShopLogger::addLog(
                'PaymentModule::validateOrder - Payment is about to be added',
                1,
                null,
                'Cart',
                (int) $id_cart,
                true
            );
        }

        if ($order_status->logable) {
            if (isset($extra_vars['transaction_id'])) {
                $transaction_id = $extra_vars['transaction_id'];
            } else {
                $transaction_id = null;
            }

            if (!isset($order) || !$order->addOrderPayment($amount_paid, null, $transaction_id)) {
                PrestaShopLogger::addLog(
                    'PaymentModule::validateOrder - Cannot save Order Payment',
                    3,
                    null,
                    'Cart',
                    (int) $id_cart,
                    true
                );

                throw new PrestaShopException('Can\'t save Order Payment');
            }
        }

        $products = $this->context->cart->getProducts();

        CartRule::cleanCache();

        foreach ($order_detail_list as $key => $order_detail) {
            $order = $order_list[$key];
            if (!isset($order->id)) {
                $error = $this->trans('Order creation failed', [], 'Admin.Payment.Notification');
                PrestaShopLogger::addLog($error, 4, 2, 'Cart', (int) $order->id_cart);
                exit(Tools::displayError($error));
            }
            if (!$secure_key) {
                $message .= '<br />' . $this->trans('Warning: the secure key is empty, check your payment account before validation', [], 'Admin.Payment.Notification');
            }

            if (!empty($message)) {
                $message = strip_tags($message, '<br>');
                if (Validate::isCleanHtml($message)) {
                    if (self::DEBUG_MODE) {
                        PrestaShopLogger::addLog(
                            'PaymentModule::validateOrder - Message is about to be added',
                            1,
                            null,
                            'Cart',
                            (int) $id_cart,
                            true
                        );
                    }
                    $msg = new Message();
                    $msg->message = $message;
                    $msg->id_cart = (int) $id_cart;
                    $msg->id_customer = (int) $order->id_customer;
                    $msg->id_order = (int) $order->id;
                    $msg->private = true;
                    $msg->add();
                }
            }


            $virtual_product = true;

            $product_var_tpl_list = [];
            foreach ($order->product_list as $product) {
                $price = Product::getPriceStatic((int) $product['id_product'], false, $product['id_product_attribute'] ? (int) $product['id_product_attribute'] : null, 6, null, false, true, $product['cart_quantity'], false, (int) $order->id_customer, (int) $order->id_cart, (int) $order->{Configuration::get('PS_TAX_ADDRESS_TYPE')}, $specific_price, true, true, null, true, $product['id_customization']);
                $price_wt = Product::getPriceStatic((int) $product['id_product'], true, $product['id_product_attribute'] ? (int) $product['id_product_attribute'] : null, 2, null, false, true, $product['cart_quantity'], false, (int) $order->id_customer, (int) $order->id_cart, (int) $order->{Configuration::get('PS_TAX_ADDRESS_TYPE')}, $specific_price, true, true, null, true, $product['id_customization']);

                $product_price = Product::getTaxCalculationMethod() == PS_TAX_EXC ? Tools::ps_round($price, Context::getContext()->getComputingPrecision()) : $price_wt;
                $wkCustomization = 0;
                $prodName = $product['name'] . (isset($product['attributes']) ? ' - ' . $product['attributes'] : '');
                if (Module::isInstalled('wkbundleproduct') && Module::isEnabled('wkbundleproduct')) {
                    include_once _PS_MODULE_DIR_ . 'wkbundleproduct/classes/WkBundleProductRequiredClasses.php';
                    $objBundle = new WkBundle();
                    if ($objBundle->isBundleProduct($product['id_product'])) {
                        $prodName = Hook::exec(
                            'displayAddBpProductName',
                            [
                                'id_order' => 0,
                                'id_cart' => $id_cart,
                                'product_id' => $product['id_product'],
                                'product_name' => $product['name'] . (isset($product['attributes']) ? ' - ' .
                                    $product['attributes'] : ''),
                                'id_customization' => $product['id_customization'],
                            ]
                        );
                        $wkCustomization = 1;
                    }
                }
                $product_var_tpl = [
                    'id_product' => $product['id_product'],
                    'id_product_attribute' => $product['id_product_attribute'],
                    'reference' => $product['reference'],
                    'name' => $prodName,
                    'price' => Tools::getContextLocale($this->context)->formatPrice($product_price * $product['quantity'], $this->context->currency->iso_code),
                    'quantity' => $product['quantity'],
                    'customization' => [],
                ];

                if (isset($product['price']) && $product['price']) {
					if($product_price != ''){
						$product_var_tpl['unit_price'] = Tools::getContextLocale($this->context)->formatPrice($product_price, $this->context->currency->iso_code);
						$product_var_tpl['unit_price_full'] = Tools::getContextLocale($this->context)->formatPrice($product_price, $this->context->currency->iso_code)
							. ' ' . $product['unity'];
					}
                } else {
                    $product_var_tpl['unit_price'] = $product_var_tpl['unit_price_full'] = '';
                }
                if (!$wkCustomization) {
                    $customized_datas = Product::getAllCustomizedDatas((int) $order->id_cart, null, true, null, (int) $product['id_customization']);
                    if (isset($customized_datas[$product['id_product']][$product['id_product_attribute']])) {
                        $product_var_tpl['customization'] = [];
                        foreach ($customized_datas[$product['id_product']][$product['id_product_attribute']][$order->id_address_delivery] as $customization) {
                            $customization_text = '';
                            if (isset($customization['datas'][Product::CUSTOMIZE_TEXTFIELD])) {
                                foreach ($customization['datas'][Product::CUSTOMIZE_TEXTFIELD] as $text) {
                                    $customization_text .= '<strong>' . $text['name'] . '</strong>: ' . $text['value'] . '<br />';
                                }
                            }

                            if (isset($customization['datas'][Product::CUSTOMIZE_FILE])) {
                                $customization_text .= $this->trans('%d image(s)', [count($customization['datas'][Product::CUSTOMIZE_FILE])], 'Admin.Payment.Notification') . '<br />';
                            }

                            $customization_quantity = (int) $customization['quantity'];

                            $product_var_tpl['customization'][] = [
                                'customization_text' => $customization_text,
                                'customization_quantity' => $customization_quantity,
                                'quantity' => Tools::getContextLocale($this->context)->formatPrice($customization_quantity * $product_price, $this->context->currency->iso_code),
                            ];
                        }
                    }
                }

                $product_var_tpl_list[] = $product_var_tpl;
                if (!$product['is_virtual']) {
                    $virtual_product &= false;
                }
            }

            $product_list_txt = '';
            $product_list_html = '';
            if (count($product_var_tpl_list) > 0) {
                $product_list_txt = $this->getEmailTemplateContent('order_conf_product_list.txt', Mail::TYPE_TEXT, $product_var_tpl_list);
                $product_list_html = $this->getEmailTemplateContent('order_conf_product_list.tpl', Mail::TYPE_HTML, $product_var_tpl_list);
            }

            $total_reduction_value_ti = 0;
            $total_reduction_value_tex = 0;

            $cart_rules_list = $this->createOrderCartRules(
                $order,
                $this->context->cart,
                $order_list,
                $total_reduction_value_ti,
                $total_reduction_value_tex,
                $id_order_state
            );

            $cart_rules_list_txt = '';
            $cart_rules_list_html = '';
            if (count($cart_rules_list) > 0) {
                $cart_rules_list_txt = $this->getEmailTemplateContent('order_conf_cart_rules.txt', Mail::TYPE_TEXT, $cart_rules_list);
                $cart_rules_list_html = $this->getEmailTemplateContent('order_conf_cart_rules.tpl', Mail::TYPE_HTML, $cart_rules_list);
            }

            $old_message = Message::getMessageByCartId((int) $this->context->cart->id);
            if ($old_message && !$old_message['private']) {
                $update_message = new Message((int) $old_message['id_message']);
                $update_message->id_order = (int) $order->id;
                $update_message->update();

                $customer_thread = new CustomerThread();
                $customer_thread->id_contact = 0;
                $customer_thread->id_customer = (int) $order->id_customer;
                $customer_thread->id_shop = (int) $this->context->shop->id;
                $customer_thread->id_order = (int) $order->id;
                $customer_thread->id_lang = (int) $this->context->language->id;
                $customer_thread->email = $this->context->customer->email;
                $customer_thread->status = 'open';
                $customer_thread->token = Tools::passwdGen(12);
                $customer_thread->add();

                $customer_message = new CustomerMessage();
                $customer_message->id_customer_thread = $customer_thread->id;
                $customer_message->id_employee = 0;
                $customer_message->message = $update_message->message;
                $customer_message->private = false;

                if (!$customer_message->add()) {
                    $this->_errors[] = $this->trans('An error occurred while saving message', [], 'Admin.Payment.Notification');
                }
            }

            if (self::DEBUG_MODE) {
                PrestaShopLogger::addLog(
                    'PaymentModule::validateOrder - Hook validateOrder is about to be called',
                    1,
                    null,
                    'Cart',
                    (int) $id_cart,
                    true
                );
            }

            Hook::exec('actionValidateOrder', [
                'cart' => $this->context->cart,
                'order' => $order,
                'customer' => $this->context->customer,
                'currency' => $this->context->currency,
                'orderStatus' => $order_status,
            ]);

            foreach ($this->context->cart->getProducts() as $product) {
                if ($order_status->logable) {
                    ProductSale::addProductSale((int) $product['id_product'], (int) $product['cart_quantity']);
                }
            }

            if (self::DEBUG_MODE) {
                PrestaShopLogger::addLog(
                    'PaymentModule::validateOrder - Order Status is about to be added',
                    1,
                    null,
                    'Cart',
                    (int) $id_cart,
                    true
                );
            }

            $new_history = new OrderHistory();
            $new_history->id_order = (int) $order->id;
            $new_history->changeIdOrderState((int) $id_order_state, $order, true);
            $new_history->addWithemail(true, $extra_vars);

            if (Configuration::get('PS_STOCK_MANAGEMENT') &&
                    ($order_detail->getStockState() ||
                    $order_detail->product_quantity_in_stock < 0)) {
                $history = new OrderHistory();
                $history->id_order = (int) $order->id;
                $history->changeIdOrderState(
                    (int) Configuration::get($order->hasBeenPaid() ? 'PS_OS_OUTOFSTOCK_PAID' : 'PS_OS_OUTOFSTOCK_UNPAID'),
                    $order,
                    true
                );
                $history->addWithemail();
            }

            unset($order_detail);

            $order = new Order((int) $order->id);

            if ($id_order_state != Configuration::get('PS_OS_ERROR')
                && $id_order_state != Configuration::get('PS_OS_CANCELED')
                && $this->context->customer->id
            ) {
                $invoice = new Address((int) $order->id_address_invoice);
                $delivery = new Address((int) $order->id_address_delivery);
                $delivery_state = $delivery->id_state ? new State((int) $delivery->id_state) : false;
                $invoice_state = $invoice->id_state ? new State((int) $invoice->id_state) : false;
                $carrier = $order->id_carrier ? new Carrier($order->id_carrier) : false;
                $orderLanguage = new Language((int) $order->id_lang);

                if ((int) Configuration::get('PS_INVOICE') && $order_status->invoice && $order->invoice_number) {
                    $currentLanguage = $this->context->language;
                    $this->context->language = $orderLanguage;
                    $this->context->getTranslator()->setLocale($orderLanguage->locale);
                    $order_invoice_list = $order->getInvoicesCollection();
                    Hook::exec('actionPDFInvoiceRender', ['order_invoice_list' => $order_invoice_list]);
                    $pdf = new PDF($order_invoice_list, PDF::TEMPLATE_INVOICE, $this->context->smarty);
                    $file_attachement['content'] = $pdf->render(false);
                    $file_attachement['name'] = $pdf->getFilename();
					if($payment_method == 'Mandat administratif' || $order->current_state == 17)
					{
						$file_attachement['name'] = "BC".sprintf('%06d', $order->invoice_number).'.pdf';
					}
                    $file_attachement['mime'] = 'application/pdf';
                    $this->context->language = $currentLanguage;
                    $this->context->getTranslator()->setLocale($currentLanguage->locale);
                } else {
                    $file_attachement = null;
                }

                if (self::DEBUG_MODE) {
                    PrestaShopLogger::addLog(
                        'PaymentModule::validateOrder - Mail is about to be sent',
                        1,
                        null,
                        'Cart',
                        (int) $id_cart,
                        true
                    );
                }

                if (Validate::isEmail($this->context->customer->email)) {
                    $data = [
                        '{firstname}' => $this->context->customer->firstname,
                        '{lastname}' => $this->context->customer->lastname,
                        '{email}' => $this->context->customer->email,
                        '{delivery_block_txt}' => $this->_getFormatedAddress($delivery, AddressFormat::FORMAT_NEW_LINE),
                        '{invoice_block_txt}' => $this->_getFormatedAddress($invoice, AddressFormat::FORMAT_NEW_LINE),
                        '{delivery_block_html}' => $this->_getFormatedAddress($delivery, '<br />', [
                            'firstname' => '<span style="font-weight:bold;">%s</span>',
                            'lastname' => '<span style="font-weight:bold;">%s</span>',
                        ]),
                        '{invoice_block_html}' => $this->_getFormatedAddress($invoice, '<br />', [
                            'firstname' => '<span style="font-weight:bold;">%s</span>',
                            'lastname' => '<span style="font-weight:bold;">%s</span>',
                        ]),
                        '{delivery_company}' => $delivery->company,
                        '{delivery_firstname}' => $delivery->firstname,
                        '{delivery_lastname}' => $delivery->lastname,
                        '{delivery_address1}' => $delivery->address1,
                        '{delivery_address2}' => $delivery->address2,
                        '{delivery_city}' => $delivery->city,
                        '{delivery_postal_code}' => $delivery->postcode,
                        '{delivery_country}' => $delivery->country,
                        '{delivery_state}' => $delivery->id_state ? $delivery_state->name : '',
                        '{delivery_phone}' => ($delivery->phone) ? $delivery->phone : $delivery->phone_mobile,
                        '{delivery_other}' => $delivery->other,
                        '{invoice_company}' => $invoice->company,
                        '{invoice_vat_number}' => $invoice->vat_number,
                        '{invoice_firstname}' => $invoice->firstname,
                        '{invoice_lastname}' => $invoice->lastname,
                        '{invoice_address2}' => $invoice->address2,
                        '{invoice_address1}' => $invoice->address1,
                        '{invoice_city}' => $invoice->city,
                        '{invoice_postal_code}' => $invoice->postcode,
                        '{invoice_country}' => $invoice->country,
                        '{invoice_state}' => $invoice->id_state ? $invoice_state->name : '',
                        '{invoice_phone}' => ($invoice->phone) ? $invoice->phone : $invoice->phone_mobile,
                        '{invoice_other}' => $invoice->other,
                        '{order_name}' => $order->getUniqReference(),
                        '{id_order}' => $order->id,
                        '{delivery_info}' => "",
                        '{date}' => Tools::displayDate(date('Y-m-d H:i:s'), true),
                        '{carrier}' => ($virtual_product || !isset($carrier->name)) ? $this->trans('No carrier', [], 'Admin.Payment.Notification') : $carrier->name,
                        '{payment}' => Tools::substr($order->payment, 0, 255) . ($order->hasBeenPaid() ? '' : '&nbsp;' . $this->trans('(waiting for validation)', [], 'Emails.Body')),
                        '{products}' => $product_list_html,
                        '{products_txt}' => $product_list_txt,
                        '{discounts}' => $cart_rules_list_html,
                        '{discounts_txt}' => $cart_rules_list_txt,
                        '{total_paid}' => Tools::getContextLocale($this->context)->formatPrice($order->total_paid, $this->context->currency->iso_code),
                        '{total_shipping_tax_excl}' => Tools::getContextLocale($this->context)->formatPrice($order->total_shipping_tax_excl, $this->context->currency->iso_code),
                        '{total_shipping_tax_incl}' => Tools::getContextLocale($this->context)->formatPrice($order->total_shipping_tax_incl, $this->context->currency->iso_code),
                        '{total_tax_paid}' => Tools::getContextLocale($this->context)->formatPrice($order->total_paid_tax_incl - $order->total_paid_tax_excl, $this->context->currency->iso_code),
                        '{recycled_packaging_label}' => $order->recyclable ? $this->trans('Yes', [], 'Shop.Theme.Global') : $this->trans('No', [], 'Shop.Theme.Global'),
                    ];

                    if (Product::getTaxCalculationMethod() == PS_TAX_EXC) {
                        $data = array_merge($data, [
                            '{total_products}' => Tools::getContextLocale($this->context)->formatPrice($order->total_products, $this->context->currency->iso_code),
                            '{total_discounts}' => Tools::getContextLocale($this->context)->formatPrice($order->total_discounts_tax_excl, $this->context->currency->iso_code),
                            '{total_shipping}' => Tools::getContextLocale($this->context)->formatPrice($order->total_shipping_tax_excl, $this->context->currency->iso_code),
                            '{total_wrapping}' => Tools::getContextLocale($this->context)->formatPrice($order->total_wrapping_tax_excl, $this->context->currency->iso_code),
                        ]);
                    } else {
                        $data = array_merge($data, [
                            '{total_products}' => Tools::getContextLocale($this->context)->formatPrice($order->total_products_wt, $this->context->currency->iso_code),
                            '{total_discounts}' => Tools::getContextLocale($this->context)->formatPrice($order->total_discounts, $this->context->currency->iso_code),
                            '{total_shipping}' => Tools::getContextLocale($this->context)->formatPrice($order->total_shipping, $this->context->currency->iso_code),
                            '{total_wrapping}' => Tools::getContextLocale($this->context)->formatPrice($order->total_wrapping, $this->context->currency->iso_code),
                        ]);
                    }

                    if (is_array($extra_vars)) {
                        $data = array_merge($data, $extra_vars);
                    }

					$template = 'order_conf';
					$subject = $this->context->getTranslator()->trans(
						'Order confirmation',
						[],
						'Emails.Subject',
						$orderLanguage->locale
					);
					if($payment_method == "Mandat administratif"){ 
						$template = 'mandat';
						$subject = 'En attente du paiement par mandat administratif';
					}
					Mail::Send(
						(int) $order->id_lang,
						$template,
						$subject,
						$data,
						$this->context->customer->email,
						$this->context->customer->firstname . ' ' . $this->context->customer->lastname,
						null,
						null,
						$file_attachement,
						null,
						_PS_MAIL_DIR_,
						false,
						(int) $order->id_shop
					);
					
                }
            }

            if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT')) {
                $product_list = $order->getProducts();
                foreach ($product_list as $product) {
                    if (StockAvailable::dependsOnStock($product['product_id'])) {
                        StockAvailable::synchronize($product['product_id'], $order->id_shop);
                    }
                }
            }

            $order->updateOrderDetailTax();

        } // End foreach $order_detail_list


        if (isset($order) && $order->id) {
            $this->currentOrder = (int) $order->id;
        }
		
		
		$product_list_box = $order->getProducts();
		$only_box = true;
		$tab_box = array('1849', '1850', '1851');
		$only_patate_douce = true;
		$tab_patate_douce = array('1940', '1809', '1810', '1575', '1576', '1588', '2336', '1805', '2196', '2335', '2334', '2541', '2542');
		$only_poireau = true;
		$tab_poireau = array('1940', '1809', '1810', '1575', '1576', '1588', '2336', '1805', '2196', '2335', '2334', '2541', '2542');
		$only_plant_en_precommande = true;

        $only_plant = true;

		foreach ($product_list_box as $product_test)
		{
			if ( !in_array($product_test['product_id'], $tab_box) )
			{
				$only_box = false;
			}

            if ( !in_array($product_test['product_id'], $tab_patate_douce) && !Product::isPlantEnPrecommande($product_test['product_name'],$product_test['id_category_default']) )
			{
                $only_plant = false;
            }

			if ( $product_test['id_category_default'] != 358 )
			{
                $only_poireau = false;
            }

		}
		if ( $only_box && $order->current_state == 2 )
		{
			$new_history = new OrderHistory();
			$new_history->id_order = (int)$order->id;
			$new_history->changeIdOrderState(21, $order, true);
			$new_history->add();
		}
        if ( $only_plant && $order->current_state == 2 )
		{
			$new_history = new OrderHistory();
			$new_history->id_order = (int)$order->id;
			$new_history->changeIdOrderState(22, $order, true);
			$new_history->add();
		}
		if ( $only_poireau && $order->current_state == 2 )
		{
			$new_history = new OrderHistory();
			$new_history->id_order = (int)$order->id;
			$new_history->changeIdOrderState(38, $order, true);
			$new_history->add();
		}
			

        if (self::DEBUG_MODE) {
            PrestaShopLogger::addLog(
                'PaymentModule::validateOrder - End of validateOrder',
                1,
                null,
                'Cart',
                (int) $id_cart,
                true
            );
        }

        Hook::exec(
            'actionValidateOrderAfter',
            [
                'cart' => $this->context->cart,
                'order' => $order ?? null,
                'orders' => $order_list,
                'customer' => $this->context->customer,
                'currency' => $this->context->currency,
                'orderStatus' => new OrderState(isset($order) ? $order->current_state : null),
            ]
        );

        return true;
    }
}
