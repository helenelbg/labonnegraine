<?php
class HTMLTemplateInvoice extends HTMLTemplateInvoiceCore
{
    /**
     * Returns the template's HTML content.
     *
     * @return string HTML content
     */
    public function getContent()
    {
        $invoiceAddressPatternRules = json_decode(Configuration::get('PS_INVCE_INVOICE_ADDR_RULES'), true);
        $deliveryAddressPatternRules = json_decode(Configuration::get('PS_INVCE_DELIVERY_ADDR_RULES'), true);

        $invoice_address = new Address((int) $this->order->id_address_invoice);
        $country = new Country((int) $invoice_address->id_country);
        $formatted_invoice_address = AddressFormat::generateAddress(
            $invoice_address,
            $invoiceAddressPatternRules,
            '<br />',
            ' '
        );

        $delivery_address = null;
        $formatted_delivery_address = '';
        if (isset($this->order->id_address_delivery) && $this->order->id_address_delivery) {
            $delivery_address = new Address((int) $this->order->id_address_delivery);
            $formatted_delivery_address = AddressFormat::generateAddress(
                $delivery_address,
                $deliveryAddressPatternRules,
                '<br />',
                ' '
            );
        }

        $customer = new Customer((int) $this->order->id_customer);
        $carrier = new Carrier((int) $this->order->id_carrier);

        $order_details = $this->order_invoice->getProducts();

        $has_discount = false;
        foreach ($order_details as $id => &$order_detail) {
            if ($order_detail['reduction_amount_tax_excl'] > 0) {
                $has_discount = true;
                $order_detail['unit_price_tax_excl_before_specific_price'] =
                $order_detail['unit_price_tax_excl_including_ecotax'] + $order_detail['reduction_amount_tax_excl'];
            } elseif ($order_detail['reduction_percent'] > 0) {
                $has_discount = true;
                if ($order_detail['reduction_percent'] == 100) {
                    $order_detail['unit_price_tax_excl_before_specific_price'] = 0;
                } else {
                    $order_detail['unit_price_tax_excl_before_specific_price'] =
                    (
                        100 * $order_detail['unit_price_tax_excl_including_ecotax']
                    ) / (100 - $order_detail['reduction_percent']);
                }
            }
            if (Module::isInstalled('wkbundleproduct') && Module::isEnabled('wkbundleproduct')) {
                include_once _PS_MODULE_DIR_ . 'wkbundleproduct/classes/WkBundleProductRequiredClasses.php';
                $objBundle = new WkBundle();
                if ($objBundle->isBundleProduct($order_detail['product_id'])) {
                    $order_detail['product_name'] = Hook::exec(
                        'displayAddBpProductName',
                        [
                            'id_order' => $order_detail['id_order'],
                            'product_id' => $order_detail['product_id'],
                            'product_name' => $order_detail['product_name'],
                            'id_customization' => $order_detail['id_customization'],
                        ]
                    );
                } else {
                    $order_detail['product_name'] = $order_detail['product_name'];
                }
                $bundleTax = true;
                if (!Configuration::get('WK_BUNDLE_PRODUCT_SPLIT')) {
                    $isBundleTax = WkBundleOrderDetail::getTaxRuleByIdOrderDetail($order_detail['id_order_detail']);
                    if (!$isBundleTax) {
                        $bundleTax = false;
                    }
                }
            }

            $taxes = OrderDetail::getTaxListStatic($id);
            $tax_temp = [];
            foreach ($taxes as $tax) {
                $obj = new Tax($tax['id_tax']);
                $translator = Context::getContext()->getTranslator();
                $tax_temp[] = $translator->trans(
                    '%taxrate%%space%%',
                    [
                        '%taxrate%' => ($obj->rate + 0),
                        '%space%' => '&nbsp;',
                    ],
                    'Shop.Pdf'
                );
            }

            $order_detail['order_detail_tax'] = $taxes;
            $order_detail['order_detail_tax_label'] = implode(', ', $tax_temp);
            if (Module::isInstalled('wkbundleproduct') && Module::isEnabled('wkbundleproduct')) {
                if (!isset($bundleTax) || !$bundleTax) {
                    $tax_temp = WkBundleOrderDetail::getBundleProductTaxRate($this->order->id);
                    $newTemp = [];
                    if ($tax_temp) {
                        $newTemp[0] = '---<br><br>';
                        foreach ($tax_temp as $key => $tmp) {
                            $newTemp[$key + 1] = $tmp;
                        }
                    }
                    $tax_temp = $newTemp;

                    $order_detail['order_detail_tax_label'] = implode('<br> ', $tax_temp);
                }
            }
        }
        unset(
            $tax_temp,
            $order_detail
        );

        if (Configuration::get('PS_PDF_IMG_INVOICE')) {
            foreach ($order_details as &$order_detail) {
                if ($order_detail['image'] != null) {
                    $name = 'product_mini_' . (int) $order_detail['product_id'] .
                    (isset($order_detail['product_attribute_id']) ? '_' . (int)
                    $order_detail['product_attribute_id'] : '') . '.jpg';
                    $path = _PS_PROD_IMG_DIR_ . $order_detail['image']->getExistingImgPath() . '.jpg';

                    $order_detail['image_tag'] = preg_replace(
                        '/\.*' . preg_quote(__PS_BASE_URI__, '/') . '/',
                        _PS_ROOT_DIR_ . DIRECTORY_SEPARATOR,
                        ImageManager::thumbnail($path, $name, 45, 'jpg', false),
                        1
                    );

                    if (file_exists(_PS_TMP_IMG_DIR_ . $name)) {
                        $order_detail['image_size'] = getimagesize(_PS_TMP_IMG_DIR_ . $name);
                    } else {
                        $order_detail['image_size'] = false;
                    }
                }
            }
            unset($order_detail); // don't overwrite the last order_detail later
        }

        $cart_rules = $this->order->getCartRules($this->order_invoice->id);
        $free_shipping = false;
        foreach ($cart_rules as $key => $cart_rule) {
            if ($cart_rule['free_shipping']) {
                $free_shipping = true;
                $cart_rules[$key]['value_tax_excl'] -= $this->order_invoice->total_shipping_tax_excl;
                $cart_rules[$key]['value'] -= $this->order_invoice->total_shipping_tax_incl;

                if ($cart_rules[$key]['value'] == 0) {
                    unset($cart_rules[$key]);
                }
            }
        }

        $product_taxes = 0;
        foreach ($this->order_invoice->getProductTaxesBreakdown($this->order) as $details) {
            $product_taxes += $details['total_amount'];
        }

        $product_discounts_tax_excl = $this->order_invoice->total_discount_tax_excl;
        $product_discounts_tax_incl = $this->order_invoice->total_discount_tax_incl;
        if ($free_shipping) {
            $product_discounts_tax_excl -= $this->order_invoice->total_shipping_tax_excl;
            $product_discounts_tax_incl -= $this->order_invoice->total_shipping_tax_incl;
        }

        $products_after_discounts_tax_excl = $this->order_invoice->total_products - $product_discounts_tax_excl;
        $products_after_discounts_tax_incl = $this->order_invoice->total_products_wt - $product_discounts_tax_incl;

        $shipping_tax_excl = $free_shipping ? 0 : $this->order_invoice->total_shipping_tax_excl;
        $shipping_tax_incl = $free_shipping ? 0 : $this->order_invoice->total_shipping_tax_incl;
        $shipping_taxes = $shipping_tax_incl - $shipping_tax_excl;

        $wrapping_taxes = $this->order_invoice->total_wrapping_tax_incl - $this->order_invoice->total_wrapping_tax_excl;

        $total_taxes = $this->order_invoice->total_paid_tax_incl - $this->order_invoice->total_paid_tax_excl;

        $footer = [
            'products_before_discounts_tax_excl' => $this->order_invoice->total_products,
            'product_discounts_tax_excl' => $product_discounts_tax_excl,
            'products_after_discounts_tax_excl' => $products_after_discounts_tax_excl,
            'products_before_discounts_tax_incl' => $this->order_invoice->total_products_wt,
            'product_discounts_tax_incl' => $product_discounts_tax_incl,
            'products_after_discounts_tax_incl' => $products_after_discounts_tax_incl,
            'product_taxes' => $product_taxes,
            'shipping_tax_excl' => $shipping_tax_excl,
            'shipping_taxes' => $shipping_taxes,
            'shipping_tax_incl' => $shipping_tax_incl,
            'wrapping_tax_excl' => $this->order_invoice->total_wrapping_tax_excl,
            'wrapping_taxes' => $wrapping_taxes,
            'wrapping_tax_incl' => $this->order_invoice->total_wrapping_tax_incl,
            'ecotax_taxes' => $total_taxes - $product_taxes - $wrapping_taxes - $shipping_taxes,
            'total_taxes' => $total_taxes,
            'total_paid_tax_excl' => $this->order_invoice->total_paid_tax_excl,
            'total_paid_tax_incl' => $this->order_invoice->total_paid_tax_incl,
        ];

        foreach ($footer as $key => $value) {
            $footer[$key] = Tools::ps_round($value, _PS_PRICE_COMPUTE_PRECISION_, $this->order->round_mode);
        }

        $round_type = null;
        switch ($this->order->round_type) {
            case Order::ROUND_TOTAL:
                $round_type = 'total';

                break;
            case Order::ROUND_LINE:
                $round_type = 'line';

                break;
            case Order::ROUND_ITEM:
                $round_type = 'item';

                break;
            default:
                $round_type = 'line';

                break;
        }

        $display_product_images = Configuration::get('PS_PDF_IMG_INVOICE');
        $tax_excluded_display = Group::getPriceDisplayMethod($customer->id_default_group);

        $layout = $this->computeLayout(['has_discount' => $has_discount]);

        $legal_free_text = Hook::exec('displayInvoiceLegalFreeText', ['order' => $this->order]);
        if (!$legal_free_text) {
            $legal_free_text = Configuration::get(
                'PS_INVOICE_LEGAL_FREE_TEXT',
                (int) Context::getContext()->language->id,
                null,
                (int) $this->order->id_shop
            );
        }

        $data = [
            'order' => $this->order,
            'order_invoice' => $this->order_invoice,
            'order_details' => $order_details,
            'carrier' => $carrier,
            'cart_rules' => $cart_rules,
            'delivery_address' => $formatted_delivery_address,
            'invoice_address' => $formatted_invoice_address,
            'addresses' => ['invoice' => $invoice_address, 'delivery' => $delivery_address],
            'tax_excluded_display' => $tax_excluded_display,
            'display_product_images' => $display_product_images,
            'layout' => $layout,
            'tax_tab' => $this->getTaxTabContent(),
            'customer' => $customer,
            'footer' => $footer,
            'ps_price_compute_precision' => _PS_PRICE_COMPUTE_PRECISION_,
            'round_type' => $round_type,
            'legal_free_text' => $legal_free_text,
        ];

        if (Tools::getValue('debug')) {
            exit(json_encode($data));
        }

        $this->smarty->assign($data);

        $tpls = [
            'style_tab' => $this->smarty->fetch($this->getTemplate('invoice.style-tab')),
            'addresses_tab' => $this->smarty->fetch($this->getTemplate('invoice.addresses-tab')),
            'summary_tab' => $this->smarty->fetch($this->getTemplate('invoice.summary-tab')),
            'product_tab' => $this->smarty->fetch($this->getTemplate('invoice.product-tab')),
            'tax_tab' => $this->getTaxTabContent(),
            'payment_tab' => $this->smarty->fetch($this->getTemplate('invoice.payment-tab')),
            'note_tab' => $this->smarty->fetch($this->getTemplate('invoice.note-tab')),
            'total_tab' => $this->smarty->fetch($this->getTemplate('invoice.total-tab')),
            'shipping_tab' => $this->smarty->fetch($this->getTemplate('invoice.shipping-tab')),
        ];
        $this->smarty->assign($tpls);

        $ets_payment_with_fee = Module::getInstanceByName('ets_payment_with_fee');
        $ets_payment_with_fee->initContentHTMLTemplateInvoice($this->order->id,$this->smarty);
        
        return $this->smarty->fetch($this->getTemplateByCountry($country->iso_code));
    }

    public function getTaxTabContent()
    {
        parent::getTaxTabContent();
        return $this->smarty->fetch(_PS_MODULE_DIR_.'ets_payment_with_fee/views/templates/hook/invoice.tax-tab.tpl');
    }

    /**
     * Returns different tax breakdown elements
     *
     * @return array Different tax breakdown elements
     */
    protected function getTaxBreakdown()
    {
        if (Configuration::get('WK_BUNDLE_PRODUCT_SPLIT')) {
            return parent::getTaxBreakdown();
        }

        $order_details = $this->order_invoice->getProducts();
        $orderContainsBundle = false;
        $productTaxBreakDown = [];
        foreach ($order_details as &$order_detail) {
            if (Module::isInstalled('wkbundleproduct') && Module::isEnabled('wkbundleproduct')) {
                include_once _PS_MODULE_DIR_ . 'wkbundleproduct/classes/WkBundleProductRequiredClasses.php';
                $objBundle = new WkBundle();
                if ($objBundle->isBundleProduct($order_detail['product_id'])) {
                    $orderContainsBundle = true;
                }
            }
        }
        if ($orderContainsBundle) {
            $bundleSubProductTax = WkBundleOrderDetail::getBundleSubProductsTaxBreakdown($this->order);
            $productTaxBreakOtherThanBundle = $this->order_invoice->getProductTaxesBreakdown($this->order);
            if ($productTaxBreakOtherThanBundle) {
                foreach ($productTaxBreakOtherThanBundle as $taxRule => $productTax) {
                    if ($bundleSubProductTax) {
                        foreach ($bundleSubProductTax as $bTax => $bundleTax) {
                            if ($bTax == $taxRule) {
                                $productTaxBreakDown[$bTax] = $productTax;
                            } else {
                                $productTaxBreakDown[$bTax] = $bundleTax;
                            }
                        }
                    } else {
                        $productTaxBreakDown = $productTaxBreakOtherThanBundle;
                    }
                }
            } else {
                $productTaxBreakDown = $bundleSubProductTax;
            }
        } else {
            $productTaxBreakDown = $this->order_invoice->getProductTaxesBreakdown($this->order);
        }
        $breakdowns = [
            'product_tax' => $productTaxBreakDown,
            'shipping_tax' => $this->order_invoice->getShippingTaxesBreakdown($this->order),
            'ecotax_tax' => $this->order_invoice->getEcoTaxTaxesBreakdown(),
            'wrapping_tax' => $this->order_invoice->getWrappingTaxesBreakdown(),
        ];
        foreach ($breakdowns as $type => $bd) {
            if (empty($bd)) {
                unset($breakdowns[$type]);
            }
        }

        if (empty($breakdowns)) {
            $breakdowns = false;
        }

        if (isset($breakdowns['product_tax'])) {
            foreach ($breakdowns['product_tax'] as &$bd) {
                $bd['total_tax_excl'] = $bd['total_price_tax_excl'];
            }
        }

        if (isset($breakdowns['ecotax_tax'])) {
            foreach ($breakdowns['ecotax_tax'] as &$bd) {
                $bd['total_tax_excl'] = $bd['ecotax_tax_excl'];
                $bd['total_amount'] = $bd['ecotax_tax_incl'] - $bd['ecotax_tax_excl'];
            }
        }

        if($payment_fees = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'ets_paymentmethod_order` WHERE id_order='.(int)$this->order->id.' AND fee_incl > fee'))
        {
            $tax = $payment_fees['fee_incl'] - $payment_fees['fee'];
            $rate = Tools::ps_round($tax/$payment_fees['fee'],3)*100;
            $breakdowns['payment_fee'] = array(
                array(
                    'total_price_tax_excl' => $payment_fees['fee'],
                    'total_tax_excl' => $payment_fees['fee'],
                    'total_amount' => $tax,
                    'rate' => number_format($rate,3),
                    'id_tax' => 1,
                )
            );
        }

        return $breakdowns;
    }
	
	 /**
     * Returns the template's HTML header.
     *
     * @return string HTML header
     */
    public function getHeader()
    {
		$str = "Invoice";
				
		if(isset($this->order->payment) && $this->order->payment == "Mandat administratif"){
			$str = "Bon de commande";
			if(isset($_SERVER['HTTP_REFERER']) && (strpos($_SERVER['HTTP_REFERER'], 'sell/orders/') !== false)){
				$str = "Invoice";
			}	
		}
	
        $this->assignCommonHeaderData();
        $this->smarty->assign(['header' => Context::getContext()->getTranslator()->trans($str, [], 'Shop.Pdf')]);

        return $this->smarty->fetch($this->getTemplate('header'));
    }
}
