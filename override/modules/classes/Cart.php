<?php
use PrestaShop\PrestaShop\Adapter\Presenter\Cart\CartPresenter;
class Cart extends CartCore
{

    private const DEFAULT_ATTRIBUTES_KEYS = ['attributes' => '', 'attributes_small' => ''];

    public static function cacheSomeAttributesLists($ipa_list, $id_lang)
    {
        if (!Combination::isFeatureActive()) {
            return;
        }

        $pa_implode = [];
        $separator = Configuration::get('PS_ATTRIBUTE_ANCHOR_SEPARATOR');

        if ($separator === '-') {
            $separator = ' -';
        }

        foreach ($ipa_list as $id_product_attribute) {
            if ((int) $id_product_attribute && !array_key_exists($id_product_attribute . '-' . $id_lang, self::$_attributesLists)) {
                $pa_implode[] = (int) $id_product_attribute;
                self::$_attributesLists[(int) $id_product_attribute . '-' . $id_lang] = self::DEFAULT_ATTRIBUTES_KEYS;
            }
        }

        if (!count($pa_implode)) {
            return;
        }

        $result = Db::getInstance()->executeS(
            'SELECT pac.`id_product_attribute`, agl.`public_name` AS public_group_name, al.`name` AS attribute_name, a.`id_attribute`
            FROM `' . _DB_PREFIX_ . 'product_attribute_combination` pac
            LEFT JOIN `' . _DB_PREFIX_ . 'attribute` a ON a.`id_attribute` = pac.`id_attribute`
            LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group` ag ON ag.`id_attribute_group` = a.`id_attribute_group`
            LEFT JOIN `' . _DB_PREFIX_ . 'attribute_lang` al ON (
                a.`id_attribute` = al.`id_attribute`
                AND al.`id_lang` = ' . (int) $id_lang . '
            )
            LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group_lang` agl ON (
                ag.`id_attribute_group` = agl.`id_attribute_group`
                AND agl.`id_lang` = ' . (int) $id_lang . '
            )
            WHERE pac.`id_product_attribute` IN (' . implode(',', $pa_implode) . ')
            ORDER BY ag.`position` ASC, a.`position` ASC'
        );

        $colon = Context::getContext()->getTranslator()->trans(': ', [], 'Shop.Pdf');
        foreach ($result as $row) {
            $key = $row['id_product_attribute'] . '-' . $id_lang;
            self::$_attributesLists[$key]['attributes'] .= $row['public_group_name'] . $colon . $row['attribute_name'] . $separator . ' ';
            self::$_attributesLists[$key]['attributes_small'] .= $row['attribute_name'] . $separator . ' ';
            self::$_attributesLists[$key]['attributes_id'][] = $row['id_attribute'];
        }

        foreach ($pa_implode as $id_product_attribute) {
            self::$_attributesLists[$id_product_attribute . '-' . $id_lang]['attributes'] = rtrim(
                self::$_attributesLists[$id_product_attribute . '-' . $id_lang]['attributes'],
                $separator . ' '
            );

            self::$_attributesLists[$id_product_attribute . '-' . $id_lang]['attributes_small'] = rtrim(
                self::$_attributesLists[$id_product_attribute . '-' . $id_lang]['attributes_small'],
                $separator . ' '
            );
        }
    }

    /*
    * module: quantitydiscountpro
    * date: 2023-06-21 17:26:01
    * version: 2.1.43
    */

	public function getDeliveryOptionList(Country $default_country = null, $flush = false)
	{
		$delivery_option_list = parent::getDeliveryOptionList($default_country,$flush);
		
		
		
		$total_quantity = Cart::getNbProducts($this->id); 
		if($total_quantity > 6){
			foreach($delivery_option_list as &$delivery_option) {
				$remove_id = -1;
				$i = 0;				
				foreach($delivery_option as &$option) {
					foreach( $option['carrier_list'] as $carrier_id => $carrier) {
						$id_reference = $carrier['instance']->id_reference; // 342 (prod)
						$name = $carrier['instance']->name; // Lettre verte
						if($id_reference == 342){
							unset($option['carrier_list'][$carrier_id]);
							$remove_id = $i;
						}
					}
					$i++;
				}	
				
				if($remove_id != -1){
					array_splice($delivery_option,$remove_id,1);
				}				
			}
		}
		 
		
		return $delivery_option_list;
	}
	
	public function getPackageShippingCost($id_carrier = null,
        $use_tax = true,
        Country $default_country = null,
        $product_list = null,
        $id_zone = null,
        bool $keepOrderPrices = false)
	{

		$shipping_cost = parent::getPackageShippingCost($id_carrier, $use_tax, $default_country, $product_list, $id_zone, $keepOrderPrices );

		if (Configuration::get('PS_TAX_ADDRESS_TYPE') == 'id_address_invoice')
			$address_id = (int)$this->id_address_invoice;
		elseif (is_array($product_list) && count($product_list))
		{
			$prod = current($product_list);
			$address_id = (int)$prod['id_address_delivery'];
		}
		else
			$address_id = null;
		if (!Address::addressExists($address_id,true))
			$address_id = null;

		$deliveryAdd = new Address((int)$address_id);

		$orderTotalwithDiscounts = $this->getOrderTotal(true, Cart::BOTH_WITHOUT_SHIPPING, null, null, false);
		$orderTotalProdAW = $orderTotalwithDiscounts;

        $justPlant = true;
        foreach($this->getProducts() as $checkProdPlant)
        {
            $isCheckIsPlant = false;
            if ( isset($checkProdPlant['attributes_id']) )
            {
                foreach($checkProdPlant['attributes_id'] as $attributePlant)
                {
                    if ( in_array($attributePlant, Cart::getPlantIds()) )
                    {
                        $isCheckIsPlant = true;
                    }
                }
            }
            if ( $isCheckIsPlant == true )
            {
                $orderTotalProdAW -= $checkProdPlant['total_wt'];
            }
            else
            {
                $justPlant = false;
            }
        }

        $filter = CartRule::FILTER_ACTION_ALL;
        $checkCRPlant = Db::getInstance()->executeS(
            'SELECT cr.*, crl.`id_lang`, crl.`name`, cd.`id_cart`
            FROM `' . _DB_PREFIX_ . 'cart_cart_rule` cd
            LEFT JOIN `' . _DB_PREFIX_ . 'cart_rule` cr ON cd.`id_cart_rule` = cr.`id_cart_rule`
            LEFT JOIN `' . _DB_PREFIX_ . 'cart_rule_lang` crl ON (
                cd.`id_cart_rule` = crl.`id_cart_rule`
                AND crl.id_lang = ' . (int) $this->getAssociatedLanguage()->getId() . '
            )
            WHERE `id_cart` = ' . (int) $this->id . '
            ' . ($filter == CartRule::FILTER_ACTION_SHIPPING ? 'AND free_shipping = 1' : '') . '
            ' . ($filter == CartRule::FILTER_ACTION_GIFT ? 'AND gift_product != 0' : '') . '
            ' . ($filter == CartRule::FILTER_ACTION_REDUCTION ? 'AND (reduction_percent != 0 OR reduction_amount != 0)' : '')
            . ' ORDER by cr.priority ASC, cr.gift_product DESC'
        );
        foreach ($checkCRPlant as &$cartRuleTmpAW) {
            $temp_CR = new CartRule($cartRuleTmpAW['id_cart_rule']);
            $id_product_rule_group = $temp_CR->getProductRuleGroups();
            $verifCR = false;
            foreach ($id_product_rule_group as $group_CR => $array_group_CR)
            {
                $verifCR = true;
                $product_CR = $temp_CR->getProductRules($group_CR);

                foreach ($array_group_CR['product_rules'] as $id_product_rule_CR => $array_product_rule_CR)
                {
                    if ( $array_product_rule_CR['type'] == 'products' )
                    {
                        $group_CR = $id_product_rule_CR;
                    }
                }

                if ( !empty($product_CR[$group_CR]) && $product_CR[$group_CR]['type'] == 'products' )
                {
                    $product_CR[$group_CR]['id_cart_rule'] = $cartRuleTmpAW['id_cart_rule'];

                    $result_dp = Db::getInstance()->executeS('
                                                    SELECT qdraa.id_attribute
                                                    FROM `'._DB_PREFIX_.'quantity_discount_rule_cart` qdrc
                                                    LEFT JOIN `'._DB_PREFIX_.'quantity_discount_rule_action_attribute` qdraa ON qdrc.`id_quantity_discount_rule` = qdraa.`id_quantity_discount_rule`
                                                    WHERE `id_cart_rule` = '.(int)$cartRuleTmpAW['id_cart_rule'].';'
                                            );
                    if (count($result_dp))
                    {
                        foreach ($result_dp as $listDp)
                        {
                            $product_CR[$group_CR]['id_attribute'][] = $listDp['id_attribute'];
                        }
                    }

                    $cartRuleTmpAW['product_CR'] = $product_CR;
                }
                else
                {
                    $cartRuleTmpAW['product_CR'][0]['values'] = array();
                    $cartRuleTmpAW['product_CR'][0]['id_attribute'] = array();
                    $cartRuleTmpAW['product_CR'][0]['id_cart_rule'] = $cartRuleTmpAW['id_cart_rule'];
                }
            }
            if ( $verifCR == false )
            {
                $cartRuleTmpAW['product_CR'][0]['values'] = array();
                $cartRuleTmpAW['product_CR'][0]['id_attribute'] = array();
                $cartRuleTmpAW['product_CR'][0]['id_cart_rule'] = $cartRuleTmpAW['id_cart_rule'];
            }
            
        }
        foreach ($checkCRPlant as $CRPlant) 
        {
            $isCheckIsPlant = false;
            foreach ($CRPlant['product_CR'] as $itemCRPlant) 
            {
				if(is_array($itemCRPlant)) {
					if(isset($itemCRPlant['id_attribute'])) {
						foreach ( $itemCRPlant['id_attribute'] as $itemCRPlantAttr )
						{
							if ( in_array($itemCRPlantAttr, Cart::getPlantIds()) )
							{
								$isCheckIsPlant = true;
							}
						}
					}
                }
            }
            if ( !empty($CRPlant['value_real']) && $isCheckIsPlant == true )
            {
                $orderTotalProdAW += $CRPlant['value_real'];
            }
        }

		$carrier = new Carrier($id_carrier);

		$result_CC = Db::getInstance()->executeS('
										SELECT *
										FROM `'._DB_PREFIX_.'cart_cart_rule` cd
										LEFT JOIN `'._DB_PREFIX_.'cart_rule` cr ON cd.`id_cart_rule` = cr.`id_cart_rule`
										LEFT JOIN `'._DB_PREFIX_.'cart_rule_lang` crl ON (
												cd.`id_cart_rule` = crl.`id_cart_rule`
												AND crl.id_lang = '.(int)$this->id_lang.'
										)
										WHERE `id_cart` = '.(int)$this->id.' AND (code LIKE "CC_%" OR crl.name = "La carte cadeau") ORDER by cr.priority ASC'
								);
		if (count($result_CC))
		{
			foreach ($result_CC as $otherCartRuleCC)
			{
							$orderTotalProdAW += $otherCartRuleCC['reduction_amount'];
			}
		}


        if (!isset($id_zone))
		{
			if (!$this->isMultiAddressDelivery()
				&& isset($this->id_address_delivery) // Be carefull, id_address_delivery is not usefull one 1.5
				&& $this->id_address_delivery
				&& Customer::customerHasAddress($this->id_customer, $this->id_address_delivery
			))
				$id_zone = Address::getZoneById((int)$this->id_address_delivery);
			else
			{
				if (!Validate::isLoadedObject($default_country))
					$default_country = new Country(Configuration::get('PS_COUNTRY_DEFAULT'), Configuration::get('PS_LANG_DEFAULT'));

				$id_zone = (int)$default_country->id_zone;
			}
		}

        
        if ( $id_zone == 1 && $orderTotalProdAW >= 49 && ($carrier->id_reference == 192 || $carrier->id_reference == 191 || $carrier->id_reference == 189 || $carrier->id_reference == 348 || $carrier->id_reference == 155) )
		{
            $shipping_cost = 0;
        }

        $result_Plant = Db::getInstance()->executeS('
										SELECT pac.*
										FROM `'._DB_PREFIX_.'cart_product` cp
										LEFT JOIN `'._DB_PREFIX_.'product_attribute_combination` pac ON cp.`id_product_attribute` = pac.`id_product_attribute`
										WHERE cp.`id_cart` = '.(int)$this->id.';'
								);
		if (count($result_Plant))
		{
            $verifPlant = false;
            $arrayIsPlant = Cart::getPlantIds();
			foreach ($result_Plant as $Plant)
			{
                if ( in_array($Plant['id_attribute'], $arrayIsPlant) )
                {
                    $verifPlant = true;
                }
            }
            if ( $verifPlant == true )
            {
                Media::addJsDef(
                    [
                        'plant_en_precommande' => true,
                    ]
                );
                if ( $use_tax == true )
				{
                    if ($justPlant == true)
                    { 
					    $shipping_cost = 4.90;
                    }
                    else
                    {
                        $shipping_cost += 4.90;
                    }
				}
				elseif ( $use_tax == false )
				{
                    if ($justPlant == true)
                    { 
					    $shipping_cost = 4.083333;
                    }
                    else
                    {
                        $shipping_cost += 4.083333;
                    }
				}
            }
        }



		$zone_montagne=array('04120', '04130', '04140', '04160', '04170', '04200', '04240', '04260', '04300', '04310', '04330', '04360', '04370', '04400', '04510', '04530', '04600', '04700', '04850', '05100', '05110', '05120', '05130', '05150', '05160', '05170', '05200', '05220', '05240', '05250', '05260', '05290', '05300', '05310', '05320', '05330', '05340', '05350', '05400', '05460', '05470', '05500', '05560', '05600', '05700', '05800', '06140', '06380', '06390', '06410', '06420', '06430', '06450', '06470', '06530', '06540', '06620', '06710', '06750', '06910', '09110', '09140', '09300', '09460', '25120', '25140', '25240', '25370', '25450', '25500', '25650', '30570', '31110', '38112', '38114', '38142', '38190', '38250', '38350', '38380', '38410', '38580', '38660', '38700', '38750', '38860', '38880', '39220', '39310', '39400', '63113', '63210', '63240', '63610', '63660', '63690', '63840', '63850', '64440', '64490', '64560', '64570', '65110', '65120', '65170', '65200', '65240', '65400', '65510', '65710', '66210', '66760', '66800', '68140', '68610', '68650', '73110', '73120', '73130', '73140', '73150', '73160', '73170', '73190', '73210', '73220', '73230', '73250', '73260', '73270', '73300', '73320', '73340', '73350', '73390', '73400', '73440', '73450', '73460', '73470', '73500', '73530', '73550', '73590', '73600', '73620', '73630', '73640', '73710', '73720', '73870', '74110', '74120', '74170', '74220', '74230', '74260', '74310', '74340', '74350', '74360', '74390', '74400', '74420', '74430', '74440', '74450', '74470', '74480', '74660', '74740', '74920', '83111', '83440', '83530', '83560', '83630', '83690', '83830', '83840', '84390', '88310', '88340', '88370', '88400', '90200');
		if ( $carrier->id_reference == 192 && in_array($deliveryAdd->postcode, $zone_montagne) && $this->getTotalWeight($product_list) > 5 )
		{
				$shipping_cost = 0;
		}

		return $shipping_cost;
	}

    public function getTotalWeight($products = null)
    {
        if (null !== $products) {
            $total_weight = 0;
            foreach ($products as $product) {
                $poidsOK = true;
                foreach($product['attributes_id'] as $testAttr)
                {
                    if ( in_array($testAttr, Cart::getPlantIds()) )
                    {
                        $poidsOK = false;
                        break;
                    }
                }
                if ( $poidsOK == true )
                {
                    $total_weight += ($product['weight_attribute'] ?? $product['weight']) * $product['cart_quantity'];
                }
            }
            return $total_weight;
        }

        if (!isset(self::$_totalWeight[$this->id])) {
            $this->updateProductWeight($this->id);
        }

        return self::$_totalWeight[(int) $this->id];
    }
	
	public function duplicate()
    {
        if (!Validate::isLoadedObject($this)) {
            return false;
        }
        $cart = new Cart($this->id);
        $cart->id = null;
        $cart->id_shop = $this->id_shop;
        $cart->id_shop_group = $this->id_shop_group;
        if (!Customer::customerHasAddress((int) $cart->id_customer, (int) $cart->id_address_delivery)) {
            $cart->id_address_delivery = (int) Address::getFirstCustomerAddressId((int) $cart->id_customer);
        }
        if (!Customer::customerHasAddress((int) $cart->id_customer, (int) $cart->id_address_invoice)) {
            $cart->id_address_invoice = (int) Address::getFirstCustomerAddressId((int) $cart->id_customer);
        }
        if ($cart->id_customer) {
            $cart->secure_key = Cart::$_customer->secure_key;
        }
        $cart->add();
        if (!Validate::isLoadedObject($cart)) {
            return false;
        }
        $success = true;
        $products = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT * FROM `' . _DB_PREFIX_ . 'cart_product`
        WHERE `id_cart` = ' . (int) $this->id);
        $orderId = Order::getIdByCartId((int) $this->id);
        $product_gift = [];
        if ($orderId) {
            $product_gift = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT cr.`gift_product`,
            cr.`gift_product_attribute` FROM `' . _DB_PREFIX_ . 'cart_rule` cr LEFT JOIN
            `' . _DB_PREFIX_ . 'order_cart_rule` ocr ON (ocr.`id_order` = ' . (int) $orderId . ') WHERE
            ocr.`id_cart_rule` = cr.`id_cart_rule`');
        }
        $id_address_delivery = Configuration::get('PS_ALLOW_MULTISHIPPING') ? $cart->id_address_delivery : 0;
        $customs = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            'SELECT *
            FROM ' . _DB_PREFIX_ . 'customization c
            LEFT JOIN ' . _DB_PREFIX_ . 'customized_data cd ON cd.id_customization = c.id_customization
            WHERE c.id_cart = ' . (int) $this->id
        );
        $customs_by_id = [];
        foreach ($customs as $key => &$custom) {
            if (Module::isInstalled('wkbundleproduct') && Module::isEnabled('wkbundleproduct')) {
                include_once _PS_MODULE_DIR_ . 'wkbundleproduct/classes/WkBundleProductRequiredClasses.php';
                $objBundle = new WkBundle();
                if ($objBundle->isBundleProduct($custom['id_product'])) {
                    if ($custom['id_customization'] == null) {
                        $customsD = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
                            'SELECT *
                            FROM ' . _DB_PREFIX_ . 'customization
                            WHERE id_cart = ' . (int) $this->id
                        );
                        if ($customsD) {
                            if ($customsD[$key]['id_product'] == $custom['id_product']) {
                                $custom['id_customization'] = $customsD[$key]['id_customization'];
                            }
                        }
                        $custom['type'] = 1;
                        $custom['value'] = rand(1, 100);
                    }
                }
            }
            if (!isset($customs_by_id[$custom['id_customization']])) {
                $customs_by_id[$custom['id_customization']] = [
                    'id_product_attribute' => $custom['id_product_attribute'],
                    'id_product' => $custom['id_product'],
                    'quantity' => $custom['quantity'],
                ];
            }
        }

        $new_customization_method = (int) Db::getInstance()->getValue(
            'SELECT COUNT(`id_customization`) FROM `' . _DB_PREFIX_ . 'cart_product`
            WHERE `id_cart` = ' . (int) $this->id .
            ' AND `id_customization` != 0'
        ) > 0;

        $custom_ids = [];
        foreach ($customs_by_id as $customization_id => $val) {
            if (Module::isInstalled('wkbundleproduct') && Module::isEnabled('wkbundleproduct')) {
                include_once _PS_MODULE_DIR_ . 'wkbundleproduct/classes/WkBundleProductRequiredClasses.php';
                $objBundle = new WkBundle();
                if (!$objBundle->isBundleProduct($val['id_product'])) {
                    if ($new_customization_method) {
                        $val['quantity'] = 0;
                    }
                }
            } else {
                if ($new_customization_method) {
                    $val['quantity'] = 0;
                }
            }

            Db::getInstance()->execute(
                'INSERT INTO `' . _DB_PREFIX_ . 'customization` (id_cart, id_product_attribute,
                id_product, `id_address_delivery`, quantity, `quantity_refunded`, `quantity_returned`, `in_cart`)
                VALUES(' . (int) $cart->id . ', ' . (int) $val['id_product_attribute'] . ', ' . (int)
                $val['id_product'] . ', ' . (int) $id_address_delivery . ', ' . (int) $val['quantity'] . ', 0, 0, 1)'
            );
            $custom_ids[$customization_id] = Db::getInstance(_PS_USE_SQL_SLAVE_)->Insert_ID();
        }
        if (count($customs)) {
            $first = true;
            $sql_custom_data = 'INSERT INTO ' . _DB_PREFIX_ . 'customized_data (`id_customization`, `type`,
            `index`, `value`, `id_module`, `price`, `weight`) VALUES ';
            foreach ($customs as $custom) {
                if (!$first) {
                    $sql_custom_data .= ',';
                } else {
                    $first = false;
                }
                $customized_value = $custom['value'];
                if ((int) $custom['type'] == Product::CUSTOMIZE_FILE) {
                    $customized_value = md5(uniqid(mt_rand(0, mt_getrandmax()), true));
                    Tools::copy(_PS_UPLOAD_DIR_ . $custom['value'], _PS_UPLOAD_DIR_ . $customized_value);
                    Tools::copy(
                        _PS_UPLOAD_DIR_ . $custom['value'] . '_small',
                        _PS_UPLOAD_DIR_ . $customized_value . '_small'
                    );
                }
                $sql_custom_data .= '(' . (int) $custom_ids[$custom['id_customization']] . ', ' .
                (int) $custom['type'] . ', ' .
                    (int) $custom['index'] . ', \'' . pSQL($customized_value) . '\', ' .
                    (int) $custom['id_module'] . ', ' . (float) $custom['price'] . ', ' .
                    (float) $custom['weight'] . ')';
            }
            Db::getInstance()->execute($sql_custom_data);
        }

        foreach ($products as $product) {
            if ($id_address_delivery) {
                if (Customer::customerHasAddress((int) $cart->id_customer, $product['id_address_delivery'])) {
                    $id_address_delivery = $product['id_address_delivery'];
                }
            }

            if (Module::isInstalled('wkbundleproduct') && Module::isEnabled('wkbundleproduct')) {
                include_once _PS_MODULE_DIR_ . 'wkbundleproduct/classes/WkBundleProductRequiredClasses.php';
                $objBundle = new WkBundle();
                if ($objBundle->isBundleProduct($product['id_product'])) {
                    $objOrder = new WkBundleOrderDetail();
                    $orderedprod = $objOrder->getBundleProductOrderInfo(
                        $orderId,
                        $product['id_product'],
                        Context::getContext()->shop->id
                    );
                    if ($orderedprod) {
                        foreach ($orderedprod as $prod) {
                            if (Configuration::get('PS_STOCK_MANAGEMENT')) {
                                $availableStock = StockAvailable::getQuantityAvailableByProduct(
                                    $prod['id_product'],
                                    $prod['id_product_attribute'],
                                    Context::getContext()->shop->id
                                );
                            } else {
                                $availableStock = $prod['product_qty'];
                            }
                            if ($availableStock >= $prod['product_qty']) {
                                $objSubProduct = new WkBundleSubProduct();
                                if (Configuration::get('PS_STOCK_MANAGEMENT')) {
                                    $prodQty = $objSubProduct->checkProductQuantity(
                                        $prod['id_wk_bundle_section'],
                                        $prod['id_product'],
                                        $prod['id_product_attribute']
                                    );
                                } else {
                                    $prodQty = $prod['product_qty'];
                                }
                                $id_customization = (int) $product['id_customization'];
                                $custId = isset($custom_ids[$id_customization]) ?
                                 (int) $custom_ids[$id_customization] : 0;
                                if (!isset($this->context->employee->id)) {
                                    if ($prodQty) {
                                        if ($prodQty >= $prod['product_qty']) {
                                            $objTemp = new WkBundleCartDataFinal();
                                            $objTemp->insertDataIntoTempTable(
                                                $prod['id_wk_bundle_section'],
                                                $prod['id_product'],
                                                $prod['id_product_attribute'],
                                                $product['id_product'],
                                                $prod['product_qty'],
                                                1,
                                                $cart->id,
                                                Context::getcontext()->cookie->id_wk_bundle_identifier,
                                                $custId
                                            );
                                            unset($objTemp);
                                        }
                                    } else {
                                        $objTemp = new WkBundleCartDataFinal();
                                        $objTemp->insertDataIntoTempTable(
                                            $prod['id_wk_bundle_section'],
                                            $prod['id_product'],
                                            $prod['id_product_attribute'],
                                            $product['id_product'],
                                            $prod['product_qty'],
                                            1,
                                            $cart->id,
                                            Context::getcontext()->cookie->id_wk_bundle_identifier,
                                            $custId
                                        );
                                        unset($objTemp);
                                    }
                                }
                            }
                        }
                    }
                }
            }
            foreach ($product_gift as $gift) {
                if (isset($gift['gift_product'], $gift['gift_product_attribute'])
                    && (int) $gift['gift_product'] == (int) $product['id_product']
                    && (int) $gift['gift_product_attribute'] == (int) $product['id_product_attribute']
                ) {
                    $product['quantity'] = (int) $product['quantity'] - 1;
                }
            }
            $id_customization = (int) $product['id_customization'];
            $success &= $cart->updateQty(
                (int) $product['quantity'],
                (int) $product['id_product'],
                (int) $product['id_product_attribute'],
                isset($custom_ids[$id_customization]) ? (int) $custom_ids[$id_customization] : 0,
                'up',
                (int) $id_address_delivery,
                new Shop((int) $cart->id_shop),
                false,
                false
            );
        }

        return ['cart' => $cart, 'success' => $success];
    }

    public function checkQuantities($return_product = false)
    {
        $parentResult = parent::checkQuantities($return_product);
        if (Configuration::get('PS_STOCK_MANAGEMENT')) {
            if (Module::isInstalled('wkbundleproduct') && Module::isEnabled('wkbundleproduct')) {
                include_once _PS_MODULE_DIR_ . 'wkbundleproduct/wkbundleproduct.php';
                if ($parentResult) {
                    $cartProducts = $this->getProducts();
                    if (!empty($cartProducts)) {
                        $objBundle = new WkBundle();
                        $moduleInstance = new WkBundleProduct();
                        $objTempData = new WkBundleCartDataFinal();
                        $objSubproduct = new WkBundleSubProduct();
                        foreach ($cartProducts as $productList) {
                            if ($objBundle->isBundleProduct($productList['id_product'])) {
                                $productIdArray = [];
                                $bundleProductInformation = $objTempData->getSelectedBundleProduct(
                                    $productList['id_product'],
                                    Context::getcontext()->cart->id,
                                    Context::getcontext()->shop->id
                                );
                                if (!empty($bundleProductInformation)) {
                                    foreach ($bundleProductInformation as $bundleInfo) {
                                        $availQty = $objSubproduct->checkProductQuantity(
                                            $bundleInfo['id_wk_bundle_section'],
                                            $bundleInfo['id_product'],
                                            $bundleInfo['id_product_attribute']
                                        );
                                        $availableStock = StockAvailable::getQuantityAvailableByProduct(
                                            $bundleInfo['id_product'],
                                            $bundleInfo['id_product_attribute'],
                                            Context::getContext()->shop->id
                                        );
                                        if ($availableStock
                                            >= ($productList['cart_quantity'] * $bundleInfo['product_qty'])
                                        ) {
                                            if ($availQty) {
                                                if ($availQty['quantity']
                                                    < ($productList['cart_quantity'] * $bundleInfo['product_qty'])
                                                ) {
                                                    $productIdArray[] = $bundleInfo['id_product'];
                                                }
                                            }
                                        } else {
                                            $productIdArray[] = $bundleInfo['id_product'];
                                        }
                                    }
                                } else {
                                    Context::getcontext()->cart->deleteProduct($productList['id_product']);
                                }
                                if (!empty($productIdArray)) {
                                    $nameArray = [];
                                    foreach ($productIdArray as $product) {
                                        $nameArray[] = Product::getProductName(
                                            $product,
                                            0,
                                            Context::getContext()->language->id
                                        );
                                    }
                                    if ($nameArray) {
                                        $nameArray = implode(',', $nameArray);
                                    }
                                    Context::getContext()->controller->errors[] =
                                        $moduleInstance->l('Decrease bundle quantity some subproducts are out of stock. Product(s) are ') . $nameArray;
                                }
                            }
                            if (Configuration::get('WK_BUNDLE_PRODUCT_RESERVED_QTY')) {
                                if ($objSubproduct->getAllAvailableProduct(0)) {
                                    if (in_array(
                                        $productList['id_product'],
                                        $objSubproduct->getAllAvailableProduct(0)
                                    )) {
                                        $qty = $objSubproduct->getProductMaximumQuantity(
                                            $productList['id_product'],
                                            $productList['id_product_attribute']
                                        );
                                        if ($qty) {
                                            if ($productList['cart_quantity'] > $qty) {
                                                Context::getContext()->controller->errors[] =
                                                $moduleInstance->l('Some Product(s) are out of stock');
                                            }
                                        } else {
                                            Context::getContext()->controller->errors[] =
                                            $moduleInstance->l('Some Product(s) are out of stock');
                                        }
                                    }
                                }
                            }
                        }
                    }
                    if (empty(Context::getcontext()->controller->errors)) {
                        return true;
                    } else {
                        return false;
                    }
                }
            }
        }

        return $parentResult;
    }

	protected function getPackageShippingCostFromModule(Carrier $carrier, $shipping_cost, $products)
    {
        if (!$carrier->shipping_external) {
            return $shipping_cost;
        }
		
		if (!$carrier->external_module_name) {
            return false;
        }
		
        $module = Module::getInstanceByName($carrier->external_module_name);

        if (!Validate::isLoadedObject($module)) {
            return false;
        }

        if (property_exists($module, 'id_carrier')) {
            $module->id_carrier = $carrier->id;
        }

        if (!$carrier->need_range) {
            return $module->getOrderShippingCostExternal($this);
        }
        
        if (method_exists($module, 'getPackageShippingCost')) {
            $shipping_cost = $module->getPackageShippingCost($this, $shipping_cost, $products);
        } else {
            $shipping_cost = $module->getOrderShippingCost($this, $shipping_cost);
        }

        return $shipping_cost;
    }
	
	public static function getPlantIds(){
		return array(10512, 10513, 10522, 10523);
	}

    public function updateAddressId($id_address, $id_address_new)
    {
        $to_update = false;
        if (!isset($this->id_address_invoice) || $this->id_address_invoice == $id_address) {
            $to_update = true;
            $this->id_address_invoice = $id_address_new;
        }
        if (!isset($this->id_address_delivery) || $this->id_address_delivery == $id_address) {
            $to_update = true;
            $this->id_address_delivery = $id_address_new;
        }
        if ($to_update) {
            $this->update();
        }

        $sql = 'UPDATE `' . _DB_PREFIX_ . 'cart_product`
        SET `id_address_delivery` = ' . (int) $id_address_new . '
        WHERE  `id_cart` = ' . (int) $this->id;
        Db::getInstance()->execute($sql);

        $sql = 'UPDATE `' . _DB_PREFIX_ . 'customization`
            SET `id_address_delivery` = ' . (int) $id_address_new . '
            WHERE  `id_cart` = ' . (int) $this->id;
        Db::getInstance()->execute($sql);
    }
}
