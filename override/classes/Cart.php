<?php
use PrestaShop\PrestaShop\Adapter\Presenter\Cart\CartPresenter;
class Cart extends CartCore
{
    public $packageSimple = array();
    /*
    * module: quantitydiscountpro
    * date: 2023-06-21 17:26:01
    * version: 2.1.43
    */
    public function addCartRule($id_cart_rule, bool $useOrderPrices = false)
    {
        $result = parent::addCartRule($id_cart_rule, $useOrderPrices);
        if (Module::isEnabled('quantitydiscountpro')) {
            include_once(_PS_MODULE_DIR_.'quantitydiscountpro/quantitydiscountpro.php');
            if ( isset($this->id) && $this->id > 0 )
            {
                $cartEC = $this->id;
            }
            else 
            {
                $cartEC = (int)Context::getContext()->cart->id;
            }
            $quantityDiscountRulesAtCart = QuantityDiscountRule::getQuantityDiscountRulesAtCart($cartEC);
            if (is_array($quantityDiscountRulesAtCart) && count($quantityDiscountRulesAtCart)) {
                foreach ($quantityDiscountRulesAtCart as $quantityDiscountRuleAtCart) {
                    $quantityDiscountRuleAtCartObj = new QuantityDiscountRule((int)$quantityDiscountRuleAtCart['id_quantity_discount_rule']);
                    if (!$quantityDiscountRuleAtCartObj->compatibleCartRules()) {
                        QuantityDiscountRule::removeQuantityDiscountCartRule($quantityDiscountRuleAtCart['id_cart_rule'], $cartEC);
                    }
                }
            }
        }
        return $result;
    }
    /*
    * module: quantitydiscountpro
    * date: 2023-06-21 17:26:01
    * version: 2.1.43
    */
    public function getCartRules($filter = CartRule::FILTER_ACTION_ALL, $autoAdd = true, $useOrderPrices = false)
    {
        $cartRules = parent::getCartRules($filter, $autoAdd, $useOrderPrices);
        if (Module::isEnabled('quantitydiscountpro')) {
            include_once(_PS_MODULE_DIR_.'quantitydiscountpro/quantitydiscountpro.php');
            foreach ($cartRules as &$cartRule) {
                if (QuantityDiscountRule::isQuantityDiscountRule($cartRule['id_cart_rule'])
                    && !QuantityDiscountRule::isQuantityDiscountRuleWithCode($cartRule['id_cart_rule'])) {
                    $cartRule['code'] = '';
                }
            }
            unset($cartRule);
        }

        foreach ($cartRules as &$cartRule) {
            $temp_CR = new CartRule($cartRule['id_cart_rule']);
            $id_product_rule_group = $temp_CR->getProductRuleGroups();
            $verifCR = false;
            foreach ($id_product_rule_group as $group_CR => $array_group_CR)
            {
                $verifCR = true;
                $product_CR = $temp_CR->getProductRules($group_CR);

                foreach($product_CR as $keytmp => $product_CRtmp)
                {
                    if (isset($product_CRtmp) && $product_CRtmp['type'] == 'products' )
                    {
                        $product_CR[$keytmp]['id_cart_rule'] = $cartRule['id_cart_rule'];

                        $result_dp = Db::getInstance()->executeS('
                                                        SELECT qdraa.id_attribute
                                                        FROM `'._DB_PREFIX_.'quantity_discount_rule_cart` qdrc
                                                        LEFT JOIN `'._DB_PREFIX_.'quantity_discount_rule_action_attribute` qdraa ON qdrc.`id_quantity_discount_rule` = qdraa.`id_quantity_discount_rule`
                                                        WHERE `id_cart_rule` = '.(int)$cartRule['id_cart_rule'].';'
                                                );
                        if (count($result_dp))
                        {
                            foreach ($result_dp as $listDp)
                            {
                                $product_CR[$keytmp]['id_attribute'][] = $listDp['id_attribute'];
                            }
                        }

                        $cartRule['product_CR'] = $product_CR;
                    }
                    else
                    {
                        $cartRule['product_CR'][0]['values'] = array();
                        $cartRule['product_CR'][0]['id_attribute'] = array();
                        $cartRule['product_CR'][0]['id_cart_rule'] = $cartRule['id_cart_rule'];
                    }
                }
            }
            if ( $verifCR == false )
            {
                $cartRule['product_CR'][0]['values'] = array();
                $cartRule['product_CR'][0]['id_attribute'] = array();
                $cartRule['product_CR'][0]['id_cart_rule'] = $cartRule['id_cart_rule'];
            }
        }
        unset($cartRule);
        
        return $cartRules;
    }
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


    public function getMultiCarriersOptionList($default_country, $flush)
    {
		if (isset(static::$cacheDeliveryOptionList[$this->id]) && !$flush) {
			return static::$cacheDeliveryOptionList[$this->id];
		}

		$delivery_option_list = array();
		$carriers_price = array();
		$carrier_collection = array();
		$package_list = $this->getPackageList($flush);

        if ( isset($package_list[$this->id_address_delivery]) )
        {
        foreach($package_list[$this->id_address_delivery] as &$packEC)
        {                
            $formatWH = $packEC['id_warehouse'];
            if ( $formatWH == 0 )
            {
                $formatWH = date('W');
                //$formatWH = '00';
            }
            if ( $formatWH == -2 )
            {
                if ( date('md') > '1010' )
                {
                    // prochaine box semaine du 15/01 de l'année suivante
                    $ddateB = (date('Y')+1)."-01-15";
                }
                elseif ( date('md') > '0710' )
                {
                    // prochaine box semaine du 15/10 de l'année en cours
                    $ddateB = date('Y')."-10-15";
                }
                elseif ( date('md') > '0410' )
                {
                    // prochaine box semaine du 15/07 de l'année en cours
                    $ddateB = date('Y')."-07-15";
                }
                elseif ( date('md') > '0110' )
                {
                    // prochaine box semaine du 15/04 de l'année en cours
                    $ddateB = date('Y')."-04-15";
                }   
                else       
                {
                    // prochaine box semaine du 15/01 de l'année en cours
                    $ddateB = date('Y')."-01-15";
                }     
                $dateB = new DateTime($ddateB);
                $formatWH = $dateB->format("W"); 
                /*if ( $formatWH < 10 )
                {
                    $formatWH = '0'.$formatWH;
                } */ 
            }
            elseif ( $formatWH < 10 )
            {
                $formatWH = '0'.$formatWH;
            }

            $packEC['exped_ordre'] = date('Y').$formatWH;
            if ( $formatWH < date('W') )
            {
                $packEC['exped_ordre'] = (date('Y')+1).$formatWH;
            }
        }

        $wec  = array_column($package_list[$this->id_address_delivery], 'exped_ordre');
        array_multisort($wec, SORT_ASC, $package_list[$this->id_address_delivery]);
        }
/*echo '<pre>';
        print_r($package_list);
echo '</pre>';*/
		// For each address
		foreach ($package_list as $id_address => $packages) {
			// Initialize vars
			$delivery_option_list[$id_address] = array();
			$carriers_price[$id_address] = array();
			$carriers_instance = array();
			// Be sure, for security
			if (count($packages) == 1) {
				return false;
			}

			// Get country
			$country = $id_address ? new Country((new Address($id_address))->id_country) : $default_country;

			// - $carriers_package_lists : collect all carriers list arrays of the whole package in one multi-dimentional array
			// - $packages_tmp and $i: change the keys (indexes) of each carrier in $package['carrier_list'] arrays to ensure the unicity (to be used later)
			$carriers_package_lists = $packages_carriers = array();
			$packages_tmp = $packages;
			$i = 1;
			foreach ($packages_tmp as $id_package => $package) {
				$carriers_list_tmp = array();
				// Handle A.S.M products
				if ((int)$package['id_warehouse'] > 0) {
					foreach ($package['carrier_list'] as $id_carrier) {
						$carriers_list_tmp[$i] = (int)$id_carrier;
						$i++;
					}
					array_push($carriers_package_lists, $carriers_list_tmp);
				} else {
				// Handle not A.S.M products
					$ids_carriers_tmp = WorkshopAsm::getBestCarriersForNotAsmProducts($country, $package, $this);
					if (count($ids_carriers_tmp) > 0) {
						$ids_carriers_tmp = array_unique($ids_carriers_tmp);
						foreach ($ids_carriers_tmp as $id_carrier) {
							$carriers_list_tmp[$i] = (int)$id_carrier;
							$i++;
						}
						array_push($carriers_package_lists, $carriers_list_tmp);
					}
				}
				if ($carriers_list_tmp) {// Re-assign the carrier_list
					$packages_tmp[$id_package]['carrier_list'] = $carriers_list_tmp;
				}
			}
			/* Generate all carriers combinations
				For exemple, I have the 3 carrier_list arrays :
					$arrayA = array('A1','A2','A3');
					$arrayB = array('B1','B2','B3');
					$arrayC = array('C1','C2');
				I would like to generate an array with 3 x 3 x 2 = 18 combinations :
					A1, B1, C1
					A1, B1, C2
					A1, B2, C1
					A1, B2, C2
					A1, B3, C1
					A1, B3, C2
					A2, B1, C1
					A2, B1, C2
					A2, B2, C1 ...
			*/
			$all_carriers_combinations = WorkshopAsm::generateCombinations($carriers_package_lists);
			// use array_unique if we don't need redondant carriers
			//$all_carriers_combinations = array_map('array_unique', $all_carriers_combinations);

			// Prepare the prices, carriers objects and id packages for each carrier to be used in the next code
			foreach ($packages_tmp as $id_package => $package) {
				$carriers_price[$id_address][$id_package] = array();
				foreach ($package['carrier_list'] as $index => $id_carrier) {
					if (!isset($carriers_instance[$id_carrier])) {
						$carriers_instance[$id_carrier] = new Carrier($id_carrier);
					}
					if (!isset($carriers_price[$id_address][$id_package][$id_carrier])) {
						$carriers_price[$id_address][$id_package][$id_carrier] = array(
							'without_tax' => $this->getPackageShippingCost((int)$id_carrier, false, $country, $package['product_list']),
							'with_tax' => $this->getPackageShippingCost((int)$id_carrier, true, $country, $package['product_list']),
						);
					}
					$packages_carriers[$index][$id_carrier] = $id_package;// set package ID according to the index and id carrier
				}
			}
        	unset($packages_tmp);

			// Most important part: Set all deliveries options
			foreach ($all_carriers_combinations as $carriers_combination) {
				$key = '';
				$carriers_list = array();
				// Set each delivery option
				foreach ($carriers_combination as $index => $id_carrier) {// now we need the index to look for the package ID
					$key .= $id_carrier . ',';
					if (!isset($carriers_list[$id_carrier])) {
						$carriers_list[$id_carrier] = array(
							'price_with_tax' => 0,
							'price_without_tax' => 0,
							'package_list' => array(),
							'product_list' => array(),
						);
					}
					$id_package = $packages_carriers[$index][$id_carrier];// now we need the index to look for the package ID
					$carriers_list[$id_carrier]['price_with_tax'] += $carriers_price[$id_address][$id_package][$id_carrier]['with_tax'];
					$carriers_list[$id_carrier]['price_without_tax'] += $carriers_price[$id_address][$id_package][$id_carrier]['without_tax'];
					$carriers_list[$id_carrier]['package_list'][] = $id_package;
					$carriers_list[$id_carrier]['product_list'] = array_merge(
						$carriers_list[$id_carrier]['product_list'],
						$packages[$id_package]['product_list']
					);
					$carriers_list[$id_carrier]['instance'] = $carriers_instance[$id_carrier];
				}
				// Collect deliveries options
				$delivery_option_list[$id_address][$key] = array(
					'carrier_list' => $carriers_list,
					'is_best_price' => false,
					'is_best_grade' => false,
					'unique_carrier' => (count($carriers_list) <= 1),
				);
			}
		}

		$cart_rules = CartRule::getCustomerCartRules(Context::getContext()->cookie->id_lang, Context::getContext()->cookie->id_customer, true, true, false, $this, true);

		$result = false;
		if ($this->id) {
			$result = Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'cart_cart_rule WHERE id_cart = '.(int)$this->id);
		}

		$cart_rules_in_cart = array();
		if (is_array($result)) {
			foreach ($result as $row) {
				$cart_rules_in_cart[] = $row['id_cart_rule'];
			}
		}

		$total_products_wt = $this->getOrderTotal(true, Cart::ONLY_PRODUCTS);
		$total_products = $this->getOrderTotal(false, Cart::ONLY_PRODUCTS);

		$free_carriers_rules = array();
		foreach ($cart_rules as $cart_rule) {
			$total_price = $cart_rule['minimum_amount_tax'] ? $total_products_wt : $total_products;
			if ($cart_rule['free_shipping'] && $cart_rule['carrier_restriction'] &&
				in_array($cart_rule['id_cart_rule'], $cart_rules_in_cart) &&
				$cart_rule['minimum_amount'] <= $total_price) {
				$cr = new CartRule((int) $cart_rule['id_cart_rule']);
				if (Validate::isLoadedObject($cr) &&
					$cr->checkValidity(Context::getContext(), in_array((int) $cart_rule['id_cart_rule'], $cart_rules_in_cart), false, false)) {
					$carriers = $cr->getAssociatedRestrictions('carrier', true, false);
					if (is_array($carriers) && count($carriers) && isset($carriers['selected'])) {
						foreach ($carriers['selected'] as $carrier) {
							if (isset($carrier['id_carrier']) && $carrier['id_carrier']) {
								$free_carriers_rules[] = (int) $carrier['id_carrier'];
							}
						}
					}
				}
			}
		}

		// For each delivery options :
		//    - Set the carrier list
		//    - Calculate the price
		//    - Calculate the average position
		foreach ($delivery_option_list as $id_address => $delivery_option) {
			foreach ($delivery_option as $key => $value) {
				$total_price_with_tax = 0;
				$total_price_without_tax = 0;
				$total_price_without_tax_with_rules = 0;
				$position = 0;
				foreach ($value['carrier_list'] as $id_carrier => $data) {
					$total_price_with_tax += $data['price_with_tax'];
					$total_price_without_tax += $data['price_without_tax'];
					$total_price_without_tax_with_rules = (in_array($id_carrier, $free_carriers_rules)) ? 0 : $total_price_without_tax;

					if (!isset($carrier_collection[$id_carrier])) {
						$carrier_collection[$id_carrier] = new Carrier($id_carrier);
					}
					$delivery_option_list[$id_address][$key]['carrier_list'][$id_carrier]['instance'] = $carrier_collection[$id_carrier];

					if (file_exists(_PS_SHIP_IMG_DIR_ . $id_carrier . '.jpg')) {
						$delivery_option_list[$id_address][$key]['carrier_list'][$id_carrier]['logo'] = _THEME_SHIP_DIR_ . $id_carrier . '.jpg';
					} else {
						$delivery_option_list[$id_address][$key]['carrier_list'][$id_carrier]['logo'] = false;
					}

					$position += $carrier_collection[$id_carrier]->position;
				}
				$delivery_option_list[$id_address][$key]['total_price_with_tax'] = $total_price_with_tax;
				$delivery_option_list[$id_address][$key]['total_price_without_tax'] = $total_price_without_tax;
				$delivery_option_list[$id_address][$key]['is_free'] = !$total_price_without_tax_with_rules ? true : false;
				$delivery_option_list[$id_address][$key]['position'] = $position / count($value['carrier_list']);
			}
		}

		// Sort delivery option list
		foreach ($delivery_option_list as &$array) {
			uasort($array, array('Cart', 'sortDeliveryOptionList'));
		}
		// Hook executed only since Prestashop 8
		Hook::exec(
			'actionFilterDeliveryOptionList',
			array(
				'delivery_option_list' => &$delivery_option_list,
			)
		);
		static::$cacheDeliveryOptionList[$this->id] = $delivery_option_list;
		return static::$cacheDeliveryOptionList[$this->id];
	}

    public function setDeliveryOption($delivery_option = null)
    {
        if (empty($delivery_option)) {
            $this->delivery_option = '';
            $this->id_carrier = 0;

            return;
        }
        Cache::clean('getContextualValue_*');
        $delivery_option_list = $this->getDeliveryOptionList(null, true);
        foreach ($delivery_option_list as $id_address => $options) {
            if (!isset($delivery_option[$id_address])) {
                foreach ($options as $key => $option) {
                    if ($option['is_best_price']) {
                        $delivery_option[$id_address] = $key;

                        break;
                    }
                }
            }
        }

        if (count($delivery_option) == 1) {
            $this->id_carrier = $this->getIdCarrierFromDeliveryOption($delivery_option);
        }

        //error_log('ICIIIIIIIIIII');
        $this->delivery_option = json_encode($delivery_option);

        // update auto cart rules
        CartRule::autoRemoveFromCart();
        CartRule::autoAddToCart();
    }

    /*
    * module: quantitydiscountpro
    * date: 2023-06-21 17:26:01
    * version: 2.1.43
    */

    public function getDeliveryOption($default_country = null, $dontAutoSelectOptions = false, $use_cache = true)
    {
        $cache_id = (int) (is_object($default_country) ? $default_country->id : 0) . '-' . (int) $dontAutoSelectOptions;
        if (isset(static::$cacheDeliveryOption[$cache_id]) && $use_cache) {
            return static::$cacheDeliveryOption[$cache_id];
        }
        $delivery_option_list = $this->getDeliveryOptionList($default_country);

        // The delivery option was selected
        if (isset($this->delivery_option) && $this->delivery_option != '') {
            $delivery_option = json_decode($this->delivery_option, true);
            $validated = true;

            if (is_array($delivery_option)) {
                foreach ($delivery_option as $id_address => $key) {
                    if (!isset($delivery_option_list[$id_address][$key])) {
            
                        $validated = false;

                        break;
                    }
                }

                if ($validated) {
                    static::$cacheDeliveryOption[$cache_id] = $delivery_option;

                    return $delivery_option;
                }
            }
        }

        if ($dontAutoSelectOptions) {
            return false;
        }

        // No delivery option selected or delivery option selected is not valid, get the better for all options
        $delivery_option = [];
        if (is_array($delivery_option_list))
        {
        foreach ($delivery_option_list as $id_address => $options) {
            foreach ($options as $key => $option) {
                if (Configuration::get('PS_CARRIER_DEFAULT') == -1 && $option['is_best_price']) {
                    $delivery_option[$id_address] = $key;

                    break;
                } elseif (Configuration::get('PS_CARRIER_DEFAULT') == -2 && $option['is_best_grade']) {
                    $delivery_option[$id_address] = $key;

                    break;
                } elseif ($option['unique_carrier'] && in_array(Configuration::get('PS_CARRIER_DEFAULT'), array_keys($option['carrier_list']))) {
                    $delivery_option[$id_address] = $key;

                    break;
                }
            }

            reset($options);
            if (!isset($delivery_option[$id_address])) {
                $delivery_option[$id_address] = key($options);
            }
        }
        }

        static::$cacheDeliveryOption[$cache_id] = $delivery_option;

        return $delivery_option;
    }

	public function getDeliveryOptionList(Country $default_country = null, $flush = false)
	{
        $dol = false;
        if (!Configuration::get('WKWAREHOUSE_ALLOW_MULTICARRIER_CHOICE')) {
            $dol = true;
        }
        else {
			if (!class_exists('WarehouseStock')) {
				require_once(dirname(__FILE__).'/../../modules/multi_carrier/classes/WarehouseStock.php');
			}
			if (!class_exists('WorkshopAsm')) {
				require_once(dirname(__FILE__).'/../../modules/multi_carrier/classes/WorkshopAsm.php');
			}
			if (Configuration::get('WKWAREHOUSE_ALLOW_MULTICARRIER_CART') &&
				!Configuration::get('WKWAREHOUSE_ALLOW_MULTI_ADDRESSES') &&
				//WarehouseStock::getNumberOfAsmProductsInCart($this->id) >= 2 &&
				WarehouseStock::isMultiShipping($this)) {
				// Now we're sure the packages are multi-shipping with no common carrier
				// In shipment part, during checkout process, the carriers will be displayed as combinations (multi-carriers per delivery option)
				$multi_delivery_option_list = $this->getMultiCarriersOptionList($default_country, $flush);
				if (!$multi_delivery_option_list) {
                    $dol = true;
				}
				return $multi_delivery_option_list;
			} else {
                $dol = true;
			}
		}

        if ( $dol == true )
        {
        if (isset(static::$cacheDeliveryOptionList[$this->id]) && !$flush) {
            return static::$cacheDeliveryOptionList[$this->id];
        }

        $delivery_option_list = [];
        $carriers_price = [];
        $carrier_collection = [];
        $package_list = $this->getPackageList($flush);

        if ( isset($package_list[$this->id_address_delivery]) )
        {
        foreach($package_list[$this->id_address_delivery] as &$packEC)
        {                
            $formatWH = $packEC['id_warehouse'];
            if ( $formatWH == 0 )
            {
                $formatWH = date('W');
                //$formatWH = '00';
            }
            if ( $formatWH == -2 )
            {
                if ( date('md') > '1010' )
                {
                    // prochaine box semaine du 15/01 de l'année suivante
                    $ddateB = (date('Y')+1)."-01-15";
                }
                elseif ( date('md') > '0710' )
                {
                    // prochaine box semaine du 15/10 de l'année en cours
                    $ddateB = date('Y')."-10-15";
                }
                elseif ( date('md') > '0410' )
                {
                    // prochaine box semaine du 15/07 de l'année en cours
                    $ddateB = date('Y')."-07-15";
                }
                elseif ( date('md') > '0110' )
                {
                    // prochaine box semaine du 15/04 de l'année en cours
                    $ddateB = date('Y')."-04-15";
                }   
                else       
                {
                    // prochaine box semaine du 15/01 de l'année en cours
                    $ddateB = date('Y')."-01-15";
                }     
                $dateB = new DateTime($ddateB);
                $formatWH = $dateB->format("W"); 
                if ( $formatWH < 10 )
                {
                    $formatWH = '0'.$formatWH;
                }  
            }
            elseif ( $formatWH < 10 )
            {
                $formatWH = '0'.$formatWH;
            }

            $packEC['exped_ordre'] = date('Y').$formatWH;
            if ( $formatWH < date('W') )
            {
                $packEC['exped_ordre'] = (date('Y')+1).$formatWH;
            }
        }

        $wec  = array_column($package_list[$this->id_address_delivery], 'exped_ordre');
        array_multisort($wec, SORT_ASC, $package_list[$this->id_address_delivery]);
        }

        // Foreach addresses
        foreach ($package_list as $id_address => $packages) {
            // Initialize vars
            $delivery_option_list[$id_address] = [];
            $carriers_price[$id_address] = [];
            $common_carriers = null;
            $best_price_carriers = [];
            $best_grade_carriers = [];
            $carriers_instance = [];

            // Get country
            if ($id_address) {
                $address = new Address($id_address);
                $country = new Country($address->id_country);
            } else {
                $country = $default_country;
            }

            // Foreach packages, get the carriers with best price, best position and best grade
            foreach ($packages as $id_package => $package) {
                $total_quantity = count($package['product_list']); 

                // 5 paquets de 5g maximum
                $notDefault = false;
                foreach ($package['product_list'] as $product)
                {
                    if ( $product['weight_attribute'] > 0.005 )
                    {          
                        error_log($product['weight_attribute']);          
                        $notDefault = true; 
                        break;
                    }
                }
                $supprLV = false;
                if($total_quantity > 5 || $notDefault == true)
                {
                    $supprLV = true;
                }

                
                $removeCL = -1;

                if ( $supprLV == true )
                {
                    foreach($package['carrier_list'] as $keyCL => $valueCL)
                    {
                        $carrierCL = new Carrier($valueCL);
                        
                        if ( $carrierCL->id_reference == 342)
                        {
                            error_log('111111111111111111');
                            $removeCL = $keyCL;
                        }
                    }
                }
                if($removeCL != -1){
					unset($package['carrier_list'][$removeCL]);
				}	

                // No carriers available
                if (count($packages) == 1 && count($package['carrier_list']) == 1 && current($package['carrier_list']) == 0) {
                    $cache[$this->id] = [];

                    return $cache[$this->id];
                }

                $carriers_price[$id_address][$id_package] = [];

                // Get all common carriers for each packages to the same address
                if (null === $common_carriers) {
                    $common_carriers = $package['carrier_list'];
                } else {
                    $common_carriers = array_intersect($common_carriers, $package['carrier_list']);
                }

                $best_price = null;
                $best_price_carrier = null;
                $best_grade = null;
                $best_grade_carrier = null;

                // Foreach carriers of the package, calculate his price, check if it the best price, position and grade
                foreach ($package['carrier_list'] as $id_carrier) {
                    if (!isset($carriers_instance[$id_carrier])) {
                        $carriers_instance[$id_carrier] = new Carrier($id_carrier);
                    }

                    $price_with_tax = $this->getPackageShippingCost((int) $id_carrier, true, $country, $package['product_list']);
                    $price_without_tax = $this->getPackageShippingCost((int) $id_carrier, false, $country, $package['product_list']);
                    if (null === $best_price || $price_with_tax < $best_price) {
                        $best_price = $price_with_tax;
                        $best_price_carrier = $id_carrier;
                    }
                    $carriers_price[$id_address][$id_package][$id_carrier] = [
                        'without_tax' => $price_without_tax,
                        'with_tax' => $price_with_tax,
                    ];

                    $grade = $carriers_instance[$id_carrier]->grade;
                    if (null === $best_grade || $grade > $best_grade) {
                        $best_grade = $grade;
                        $best_grade_carrier = $id_carrier;
                    }
                }

                $best_price_carriers[$id_package] = $best_price_carrier;
                $best_grade_carriers[$id_package] = $best_grade_carrier;
            }

            // Reset $best_price_carrier, it's now an array
            $best_price_carrier = [];
            $key = '';

            /*echo '<pre style="background:red">';
            print_r($best_price_carriers);
            echo '</pre>';*/

            // Get the delivery option with the lower price
            foreach ($best_price_carriers as $id_package => $id_carrier) {
                $key .= $id_carrier . ',';
                if (!isset($best_price_carrier[$id_carrier])) {
                    $best_price_carrier[$id_carrier] = [
                        'price_with_tax' => 0,
                        'price_without_tax' => 0,
                        'package_list' => [],
                        'product_list' => [],
                    ];
                }
                $best_price_carrier[$id_carrier]['price_with_tax'] += $carriers_price[$id_address][$id_package][$id_carrier]['with_tax'];
                $best_price_carrier[$id_carrier]['price_without_tax'] += $carriers_price[$id_address][$id_package][$id_carrier]['without_tax'];
                $best_price_carrier[$id_carrier]['package_list'][] = $id_package;
                $best_price_carrier[$id_carrier]['product_list'] = array_merge($best_price_carrier[$id_carrier]['product_list'], $packages[$id_package]['product_list']);
                $best_price_carrier[$id_carrier]['instance'] = $carriers_instance[$id_carrier];
                $real_best_price = !isset($real_best_price) || $real_best_price > $carriers_price[$id_address][$id_package][$id_carrier]['with_tax'] ?
                    $carriers_price[$id_address][$id_package][$id_carrier]['with_tax'] : $real_best_price;
                $real_best_price_wt = !isset($real_best_price_wt) || $real_best_price_wt > $carriers_price[$id_address][$id_package][$id_carrier]['without_tax'] ?
                    $carriers_price[$id_address][$id_package][$id_carrier]['without_tax'] : $real_best_price_wt;
            }

            // Add the delivery option with best price as best price
            $delivery_option_list[$id_address][$key] = [
                'carrier_list' => $best_price_carrier,
                'is_best_price' => true,
                'is_best_grade' => false,
                'unique_carrier' => (count($best_price_carrier) <= 1),
            ];

            /*echo '<pre style="background:red">';
            print_r($delivery_option_list);
            echo '</pre>';*/

            // Reset $best_grade_carrier, it's now an array
            $best_grade_carrier = [];
            $key = '';

            // Get the delivery option with the best grade
            foreach ($best_grade_carriers as $id_package => $id_carrier) {
                $key .= $id_carrier . ',';
                if (!isset($best_grade_carrier[$id_carrier])) {
                    $best_grade_carrier[$id_carrier] = [
                        'price_with_tax' => 0,
                        'price_without_tax' => 0,
                        'package_list' => [],
                        'product_list' => [],
                    ];
                }
                $best_grade_carrier[$id_carrier]['price_with_tax'] += $carriers_price[$id_address][$id_package][$id_carrier]['with_tax'];
                $best_grade_carrier[$id_carrier]['price_without_tax'] += $carriers_price[$id_address][$id_package][$id_carrier]['without_tax'];
                $best_grade_carrier[$id_carrier]['package_list'][] = $id_package;
                $best_grade_carrier[$id_carrier]['product_list'] = array_merge($best_grade_carrier[$id_carrier]['product_list'], $packages[$id_package]['product_list']);
                $best_grade_carrier[$id_carrier]['instance'] = $carriers_instance[$id_carrier];
            }

            // Add the delivery option with best grade as best grade
            if (!isset($delivery_option_list[$id_address][$key])) {
                $delivery_option_list[$id_address][$key] = [
                    'carrier_list' => $best_grade_carrier,
                    'is_best_price' => false,
                    'unique_carrier' => (count($best_grade_carrier) <= 1),
                ];
            }
            $delivery_option_list[$id_address][$key]['is_best_grade'] = true;

            // Get all delivery options with a unique carrier
            foreach ($common_carriers as $id_carrier) {
                $key = '';
                $package_list = [];
                $product_list = [];
                $price_with_tax = 0;
                $price_without_tax = 0;

                foreach ($packages as $id_package => $package) {
                    $key .= $id_carrier . ',';
                    $price_with_tax += $carriers_price[$id_address][$id_package][$id_carrier]['with_tax'];
                    $price_without_tax += $carriers_price[$id_address][$id_package][$id_carrier]['without_tax'];
                    $package_list[] = $id_package;
                    $product_list = array_merge($product_list, $package['product_list']);
                }

                if (!isset($delivery_option_list[$id_address][$key])) {
                    $delivery_option_list[$id_address][$key] = [
                        'is_best_price' => false,
                        'is_best_grade' => false,
                        'unique_carrier' => true,
                        'carrier_list' => [
                            $id_carrier => [
                                'price_with_tax' => $price_with_tax,
                                'price_without_tax' => $price_without_tax,
                                'instance' => $carriers_instance[$id_carrier],
                                'package_list' => $package_list,
                                'product_list' => $product_list,
                            ],
                        ],
                    ];
                } else {
                    $delivery_option_list[$id_address][$key]['unique_carrier'] = (count($delivery_option_list[$id_address][$key]['carrier_list']) <= 1);
                }
            }
        }

        $cart_rules = CartRule::getCustomerCartRules(
            (int) Context::getContext()->cookie->id_lang,
            (int) Context::getContext()->cookie->id_customer,
            true,
            true,
            false,
            $this,
            true
        );

        $result = false;
        if ($this->id) {
            $result = Db::getInstance()->executeS('SELECT * FROM ' . _DB_PREFIX_ . 'cart_cart_rule WHERE id_cart = ' . (int) $this->id);
        }

        $cart_rules_in_cart = [];

        if (is_array($result)) {
            foreach ($result as $row) {
                $cart_rules_in_cart[] = $row['id_cart_rule'];
            }
        }

        $total_products_wt = $this->getOrderTotal(true, Cart::ONLY_PRODUCTS);
        $total_products = $this->getOrderTotal(false, Cart::ONLY_PRODUCTS);

        $free_carriers_rules = [];

        $context = Context::getContext();
        foreach ($cart_rules as $cart_rule) {
            $total_price = $cart_rule['minimum_amount_tax'] ? $total_products_wt : $total_products;
            $total_price += ($cart_rule['minimum_amount_tax'] && $cart_rule['minimum_amount_shipping'] && isset($real_best_price)) ? $real_best_price : 0;
            $total_price += (!$cart_rule['minimum_amount_tax'] && $cart_rule['minimum_amount_shipping'] && isset($real_best_price_wt)) ? $real_best_price_wt : 0;
            if ($cart_rule['free_shipping'] && $cart_rule['carrier_restriction']
                && in_array($cart_rule['id_cart_rule'], $cart_rules_in_cart)
                && $cart_rule['minimum_amount'] <= $total_price) {
                $cr = new CartRule((int) $cart_rule['id_cart_rule']);
                if (Validate::isLoadedObject($cr) &&
                    $cr->checkValidity($context, in_array((int) $cart_rule['id_cart_rule'], $cart_rules_in_cart), false, false)) {
                    $carriers = $cr->getAssociatedRestrictions('carrier', true, false);
                    if (is_array($carriers) && count($carriers) && isset($carriers['selected'])) {
                        foreach ($carriers['selected'] as $carrier) {
                            if (isset($carrier['id_carrier']) && $carrier['id_carrier']) {
                                $free_carriers_rules[] = (int) $carrier['id_carrier'];
                            }
                        }
                    }
                }
            }
        }

        // For each delivery options :
        //    - Set the carrier list
        //    - Calculate the price
        //    - Calculate the average position
        foreach ($delivery_option_list as $id_address => $delivery_option) {
            foreach ($delivery_option as $key => $value) {
                $total_price_with_tax = 0;
                $total_price_without_tax = 0;
                $total_price_without_tax_with_rules = 0;
                $position = 0;
                foreach ($value['carrier_list'] as $id_carrier => $data) {
                    $total_price_with_tax += $data['price_with_tax'];
                    $total_price_without_tax += $data['price_without_tax'];
                    $total_price_without_tax_with_rules = (in_array($id_carrier, $free_carriers_rules)) ? 0 : $total_price_without_tax;

                    if (!isset($carrier_collection[$id_carrier])) {
                        $carrier_collection[$id_carrier] = new Carrier($id_carrier);
                    }
                    $delivery_option_list[$id_address][$key]['carrier_list'][$id_carrier]['instance'] = $carrier_collection[$id_carrier];

                    if (file_exists(_PS_SHIP_IMG_DIR_ . $id_carrier . '.jpg')) {
                        $delivery_option_list[$id_address][$key]['carrier_list'][$id_carrier]['logo'] = _THEME_SHIP_DIR_ . $id_carrier . '.jpg';
                    } else {
                        $delivery_option_list[$id_address][$key]['carrier_list'][$id_carrier]['logo'] = false;
                    }

                    $position += $carrier_collection[$id_carrier]->position;
                }
                $delivery_option_list[$id_address][$key]['total_price_with_tax'] = $total_price_with_tax;
                $delivery_option_list[$id_address][$key]['total_price_without_tax'] = $total_price_without_tax;
                $delivery_option_list[$id_address][$key]['is_free'] = !$total_price_without_tax_with_rules ? true : false;
                $delivery_option_list[$id_address][$key]['position'] = $position / count($value['carrier_list']);
            }
        }

        // Sort delivery option list
        foreach ($delivery_option_list as &$array) {
            uasort($array, ['Cart', 'sortDeliveryOptionList']);
        }

        Hook::exec(
            'actionFilterDeliveryOptionList',
            [
                'delivery_option_list' => &$delivery_option_list,
            ]
        );

        static::$cacheDeliveryOptionList[$this->id] = $delivery_option_list;

        //return static::$cacheDeliveryOptionList[$this->id];
        $delivery_option_list = static::$cacheDeliveryOptionList[$this->id];
		//$delivery_option_list = parent::getDeliveryOptionList($default_country,$flush);

		return $delivery_option_list;
        }
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

        $orderTotalProdAW = 0;
        if ( is_array($product_list) )
        {
            foreach($product_list as $plEC)
            {
                $orderTotalProdAW += $plEC['total_wt'];
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
				//$orderTotalProdAW += $otherCartRuleCC['reduction_amount'];
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

        $shipping_cost_tmp = $shipping_cost;

        if ( isset($this->delivery_option) && !empty($this->delivery_option) && isset($packDecode->{$this->id_address_delivery}) && !empty($packDecode->{$this->id_address_delivery}) )
        {
            $packDecode = json_decode($this->delivery_option);
            $explode_do = explode(',', $packDecode->{$this->id_address_delivery});
            $nb_colisEC = count($explode_do)-1;
        }
        else 
        {
            $nb_colisEC = 1;
        }
        if ( $id_zone == 1 && $orderTotalProdAW >= 59 && ($carrier->id_reference == 192 || $carrier->id_reference == 191 || $carrier->id_reference == 189 || $carrier->id_reference == 348 || $carrier->id_reference == 155) )
		{
            $shipping_cost = 0;

            /*if ( $nb_colisEC > 1 )
            {
                $shipping_cost = ($shipping_cost_tmp / $nb_colisEC) * ($nb_colisEC-1);
            }*/
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
                if ( isset($product['attributes_id']))
                {
                    foreach($product['attributes_id'] as $testAttr)
                    {
                        if ( in_array($testAttr, Cart::getPlantIds()) )
                        {
                            $poidsOK = false;
                            break;
                        }
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
            foreach ($customs as $customEC) {
                if (!$first) {
                    $sql_custom_data .= ',';
                } else {
                    $first = false;
                }
                $customized_value = $customEC['value'];
                if ((int) $customEC['type'] == Product::CUSTOMIZE_FILE) {
                    $customized_value = md5(uniqid(mt_rand(0, mt_getrandmax()), true));
                    Tools::copy(_PS_UPLOAD_DIR_ . $customEC['value'], _PS_UPLOAD_DIR_ . $customized_value);
                    Tools::copy(
                        _PS_UPLOAD_DIR_ . $customEC['value'] . '_small',
                        _PS_UPLOAD_DIR_ . $customized_value . '_small'
                    );
                }
                //error_log(print_r($custom, true));
                $sql_custom_data .= '(' . (int) $custom_ids[$customEC['id_customization']] . ', ' .
                (int) $customEC['type'] . ', ' .
                    (int) $customEC['index'] . ', \'' . pSQL($customized_value) . '\', ' .
                    (int) $customEC['id_module'] . ', ' . (float) $customEC['price'] . ', ' .
                    (float) $customEC['weight'] . ')';
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

    public function getOrderTotal($withTaxes = true, $type = Cart::BOTH, $products = null, $id_carrier = null, $use_cache = false, bool $keepOrderPrices = false, $fee_payment = false, $only_cart = false)
    {
        $address_delivery = new Address((int)$this->id_address_delivery);
        if ($address_delivery->id_country != Configuration::get('PS_COUNTRY_DEFAULT')) {
            $vatNumber = $address_delivery->vat_number;

            if (empty($vatNumber)) {
                $address_invoice = new Address((int)$this->id_address_invoice);
                if ($address_invoice->id_country != Configuration::get('PS_COUNTRY_DEFAULT')) {
                    $vatNumber = $address_invoice->vat_number;
                }
            }
            
            // Ajouter une vérification supplémentaire avec isVATValid()
            if (!empty($vatNumber) && preg_match('/^[A-Z]{2}[A-Z0-9]{2,14}$/', $vatNumber)) {
                $withTaxes = false;
            }
        }

        $total = parent::getOrderTotal($withTaxes,$type,$products,$id_carrier,$use_cache,$keepOrderPrices);
        if($only_cart || $type!=Cart::BOTH)
            return $total;
        if($type== Cart::BOTH)
        {
            $custom_payment = Module::getInstanceByName('ets_payment_with_fee');
            $fee = $custom_payment->getFeePayOrderTotal($products,$withTaxes);
        }
        else
            $fee = 0;

        if($fee_payment)
            return $fee;
        return $fee + $total;
    }

    public function getProducts($refresh = false, $id_product = false, $id_country = null, $fullInfos = true, bool $keepOrderPrices = false,$default=false)
    {

        if($keepOrderPrices || $default || !Module::isEnabled('ets_extraoptions'))
        {
            //$products  = parent::getProducts($refresh,$id_product,$id_country,$fullInfos,$keepOrderPrices);
            if (!$this->id) {
                return [];
            }
            // Product cache must be strictly compared to NULL, or else an empty cart will add dozens of queries
            if ($this->_products !== null && !$refresh) {
                // Return product row with specified ID if it exists
                if (is_int($id_product)) {
                    foreach ($this->_products as $product) {
                        if ($product['id_product'] == $id_product) {
                            return [$product];
                        }
                    }
    
                    return [];
                }
    
                return $this->_products;
            }
    
            // Build query
            $sql = new DbQuery();
    
            // Build SELECT
            $sql->select('cp.`id_product_attribute`, cp.`id_product`, cp.`quantity` AS cart_quantity, cp.id_shop, cp.`id_customization`, pl.`name`, p.`is_virtual`,
                            pl.`description_short`, pl.`available_now`, pl.`available_later`, product_shop.`id_category_default`, p.`id_supplier`,
                            p.`id_manufacturer`, m.`name` AS manufacturer_name, product_shop.`on_sale`, product_shop.`ecotax`, product_shop.`additional_shipping_cost`,
                            product_shop.`available_for_order`, product_shop.`show_price`, product_shop.`price`, product_shop.`active`, product_shop.`unity`, product_shop.`unit_price`,
                            stock.`quantity` AS quantity_available, p.`width`, p.`height`, p.`depth`, stock.`out_of_stock`, p.`weight`,
                            p.`available_date`, p.`date_add`, p.`date_upd`, IFNULL(stock.quantity, 0) as quantity, pl.`link_rewrite`, cl.`link_rewrite` AS category,
                            CONCAT(LPAD(cp.`id_product`, 10, 0), LPAD(IFNULL(cp.`id_product_attribute`, 0), 10, 0), IFNULL(cp.`id_address_delivery`, 0), IFNULL(cp.`id_customization`, 0)) AS unique_id, cp.id_address_delivery,
                            product_shop.advanced_stock_management, ps.product_supplier_reference supplier_reference');
    
            // Build FROM
            $sql->from('cart_product', 'cp');
    
            // Build JOIN
            $sql->leftJoin('product', 'p', 'p.`id_product` = cp.`id_product`');
            $sql->innerJoin('product_shop', 'product_shop', '(product_shop.`id_shop` = cp.`id_shop` AND product_shop.`id_product` = p.`id_product`)');
            $sql->leftJoin(
                'product_lang',
                'pl',
                'p.`id_product` = pl.`id_product`
                AND pl.`id_lang` = ' . (int) $this->getAssociatedLanguage()->getId() . Shop::addSqlRestrictionOnLang('pl', 'cp.id_shop')
            );
    
            $sql->leftJoin(
                'category_lang',
                'cl',
                'product_shop.`id_category_default` = cl.`id_category`
                AND cl.`id_lang` = ' . (int) $this->getAssociatedLanguage()->getId() . Shop::addSqlRestrictionOnLang('cl', 'cp.id_shop')
            );
    
            $sql->leftJoin('product_supplier', 'ps', 'ps.`id_product` = cp.`id_product` AND ps.`id_product_attribute` = cp.`id_product_attribute` AND ps.`id_supplier` = p.`id_supplier`');
            $sql->leftJoin('manufacturer', 'm', 'm.`id_manufacturer` = p.`id_manufacturer`');
    
            // @todo test if everything is ok, then refactorise call of this method
            $sql->join(Product::sqlStock('cp', 'cp'));
    
            // Build WHERE clauses
            $sql->where('cp.`id_cart` = ' . (int) $this->id);
            if ($id_product) {
                $sql->where('cp.`id_product` = ' . (int) $id_product);
            }
            $sql->where('p.`id_product` IS NOT NULL');
    
            // Build ORDER BY
            $sql->orderBy('cp.`date_add`, cp.`id_product`, cp.`id_product_attribute` ASC');
    
            if (Customization::isFeatureActive()) {
                $sql->select('cu.`id_customization`, cu.`quantity` AS customization_quantity');
                $sql->leftJoin(
                    'customization',
                    'cu',
                    'p.`id_product` = cu.`id_product` AND cp.`id_product_attribute` = cu.`id_product_attribute` AND cp.`id_customization` = cu.`id_customization` AND cu.`id_cart` = ' . (int) $this->id
                );
                $sql->groupBy('cp.`id_product_attribute`, cp.`id_product`, cp.`id_shop`, cp.`id_customization`');
            } else {
                $sql->select('NULL AS customization_quantity, NULL AS id_customization');
            }
    
            if (Combination::isFeatureActive()) {
                $sql->select('
                    product_attribute_shop.`price` AS price_attribute,
                    product_attribute_shop.`ecotax` AS ecotax_attr,
                    IF (IFNULL(pa.`reference`, \'\') = \'\', p.`reference`, pa.`reference`) AS reference,
                    (p.`weight`+ IFNULL(product_attribute_shop.`weight`, pa.`weight`)) weight_attribute,
                    IF (IFNULL(pa.`ean13`, \'\') = \'\', p.`ean13`, pa.`ean13`) AS ean13,
                    IF (IFNULL(pa.`isbn`, \'\') = \'\', p.`isbn`, pa.`isbn`) AS isbn,
                    IF (IFNULL(pa.`upc`, \'\') = \'\', p.`upc`, pa.`upc`) AS upc,
                    IF (IFNULL(pa.`mpn`, \'\') = \'\', p.`mpn`, pa.`mpn`) AS mpn,
                    IFNULL(product_attribute_shop.`minimal_quantity`, product_shop.`minimal_quantity`) as minimal_quantity,
                    IF(product_attribute_shop.wholesale_price > 0,  product_attribute_shop.wholesale_price, product_shop.`wholesale_price`) wholesale_price
                ');
    
                $sql->leftJoin('product_attribute', 'pa', 'pa.`id_product_attribute` = cp.`id_product_attribute`');
                $sql->leftJoin('product_attribute_shop', 'product_attribute_shop', '(product_attribute_shop.`id_shop` = cp.`id_shop` AND product_attribute_shop.`id_product_attribute` = pa.`id_product_attribute`)');
            } else {
                $sql->select(
                    'p.`reference` AS reference, p.`ean13`, p.`isbn`,
                    p.`upc` AS upc, p.`mpn` AS mpn, product_shop.`minimal_quantity` AS minimal_quantity, product_shop.`wholesale_price` wholesale_price'
                );
            }
    
            $sql->select('image_shop.`id_image` id_image, il.`legend`');
            $sql->leftJoin('image_shop', 'image_shop', 'image_shop.`id_product` = p.`id_product` AND image_shop.cover=1 AND image_shop.id_shop=' . (int) $this->id_shop);
            $sql->leftJoin('image_lang', 'il', 'il.`id_image` = image_shop.`id_image` AND il.`id_lang` = ' . (int) $this->getAssociatedLanguage()->getId());
    
            /** @var array<string, mixed>|false $products */
            $products = Db::getInstance()->executeS($sql);
    
            // Reset the cache before the following return, or else an empty cart will add dozens of queries
            $products_ids = [];
            $pa_ids = [];
            if (is_iterable($products)) {
                foreach ($products as $key => $product) {
                    $products_ids[] = $product['id_product'];
                    $pa_ids[] = $product['id_product_attribute'];
                    $specific_price = SpecificPrice::getSpecificPrice($product['id_product'], $this->id_shop, $this->id_currency, $id_country, $this->id_shop_group, $product['cart_quantity'], $product['id_product_attribute'], $this->id_customer, $this->id);
                    if ($specific_price) {
                        $reduction_type_row = ['reduction_type' => $specific_price['reduction_type']];
                    } else {
                        $reduction_type_row = ['reduction_type' => 0];
                    }
    
                    $products[$key] = array_merge($product, $reduction_type_row);
                }
            }
            // Thus you can avoid one query per product, because there will be only one query for all the products of the cart
            Product::cacheProductsFeatures($products_ids);
            Cart::cacheSomeAttributesLists($pa_ids, (int) $this->getAssociatedLanguage()->getId());
    
            if (empty($products)) {
                $this->_products = [];
    
                return [];
            }
    
            if ($fullInfos) {
                $cart_shop_context = Context::getContext()->cloneContext();
    
                $givenAwayProductsIds = [];
    
                if ($this->shouldSplitGiftProductsQuantity && $refresh) {
                    $gifts = $this->getCartRules(CartRule::FILTER_ACTION_GIFT, false);
                    if (count($gifts) > 0) {
                        foreach ($gifts as $gift) {
                            foreach ($products as $rowIndex => $product) {
                                if (!array_key_exists('is_gift', $products[$rowIndex])) {
                                    $products[$rowIndex]['is_gift'] = false;
                                }
    
                                if (
                                    $product['id_product'] == $gift['gift_product'] &&
                                    $product['id_product_attribute'] == $gift['gift_product_attribute']
                                ) {
                                    $product['is_gift'] = true;
                                    $products[$rowIndex] = $product;
                                }
                            }
    
                            $index = $gift['gift_product'] . '-' . $gift['gift_product_attribute'];
                            if (!array_key_exists($index, $givenAwayProductsIds)) {
                                $givenAwayProductsIds[$index] = 1;
                            } else {
                                ++$givenAwayProductsIds[$index];
                            }
                        }
                    }
                }
    
                $this->_products = [];
    
                foreach ($products as &$product) {
                    if (!array_key_exists('is_gift', $product)) {
                        $product['is_gift'] = false;
                    }
    
                    $props = Product::getProductProperties((int) $this->id_lang, $product);
                    $product['reduction'] = $props['reduction'];
                    $product['reduction_without_tax'] = $props['reduction_without_tax'];
                    $product['price_without_reduction'] = $props['price_without_reduction'];
                    $product['specific_prices'] = $props['specific_prices'];
                    $product['unit_price_ratio'] = $props['unit_price_ratio'];
                    $product['unit_price'] = $product['unit_price_tax_excluded'] = $props['unit_price_tax_excluded'];
                    $product['unit_price_tax_included'] = $props['unit_price_tax_included'];
                    unset($props);
    
                    $givenAwayQuantity = 0;
                    $giftIndex = $product['id_product'] . '-' . $product['id_product_attribute'];
                    if ($product['is_gift'] && array_key_exists($giftIndex, $givenAwayProductsIds)) {
                        $givenAwayQuantity = $givenAwayProductsIds[$giftIndex];
                    }
    
                    if (!$product['is_gift'] || (int) $product['cart_quantity'] === $givenAwayQuantity) {
                        $product = $this->applyProductCalculations($product, $cart_shop_context, null, $keepOrderPrices);
                    } else {
                        // Separate products given away from those manually added to cart
                        $this->_products[] = $this->applyProductCalculations($product, $cart_shop_context, $givenAwayQuantity, $keepOrderPrices);
                        unset($product['is_gift']);
                        $product = $this->applyProductCalculations(
                            $product,
                            $cart_shop_context,
                            $product['cart_quantity'] - $givenAwayQuantity,
                            $keepOrderPrices
                        );
                    }
    
                    $this->_products[] = $product;
                }
            } else {
                $this->_products = $products;
            }
    
            $fullPack = $this->getPackageList();
            
            /*if ( isset($this->delivery_option) && !empty($this->delivery_option) )
            {
                $packDecode = json_decode($this->delivery_option);
                $explode_do = explode(',', $packDecode->{$this->id_address_delivery});
                $new_de_op = '';
                for ($i=0; $i < count($fullPack[$this->id_address_delivery]); $i++)
                {
                    $new_de_op .= $explode_do[0].',';
                }
                $delivery_option_modif[$this->id_address_delivery] = $new_de_op;
                error_log('LAAAA');
                $this->delivery_option = json_encode($delivery_option_modif);
            }*/
            foreach ($this->_products as &$product) {
                $warehouse_en_cours = 0;
                foreach($fullPack[$this->id_address_delivery] as $packEC)
                {
                    $isWH = false;

                    /*echo '<pre style="background:yellow;">';
                        print_r($packEC);
                    echo '</pre>';*/

                    foreach($packEC['product_list'] as $prodEC)
                    {
                        //echo $product['id_product'].' ('.$product['id_product_attribute'].') --'.$prodEC['id_product'].' ('.$prodEC['id_product_attribute'].') //';
                        //print_r($prodEC['warehouse_list']);
                        //echo '<br />';
                        if ( $product['id_product'] == $prodEC['id_product'] && $product['id_product_attribute'] == $prodEC['id_product_attribute'] )
                        {
                            //echo 'LA<br />';
                            $isWH = true;
                            $product['warehouse_list'] = $prodEC['warehouse_list'];
                            $product['warehouse_list_default'] = $prodEC['warehouse_list_default'];
                        }
                    }
                    if ( $isWH == true )
                    {
                        $warehouse_en_cours = $packEC['id_warehouse'];
                        //echo '******'.$packEC['id_warehouse'].'*******';
                    }
                    //echo '<hr>';
                }
                //echo '#######<br />';
                $product['warehouse_en_cours'] = $warehouse_en_cours;
                //echo $product['id_product'].' || '.$product['id_product_attribute'].' || '.$product['warehouse_en_cours'].'<br />';
                $formatWH = $warehouse_en_cours;
                if ( $formatWH == 0 )
                {
                    $formatWH = date('W');
                    //$formatWH = '00';
                }
                if ( $formatWH == -2 )
                {
                    if ( date('md') > '1010' )
                    {
                        // prochaine box semaine du 15/01 de l'année suivante
                        $ddateB = (date('Y')+1)."-01-15";
                    }
                    elseif ( date('md') > '0710' )
                    {
                        // prochaine box semaine du 15/10 de l'année en cours
                        $ddateB = date('Y')."-10-15";
                    }
                    elseif ( date('md') > '0410' )
                    {
                        // prochaine box semaine du 15/07 de l'année en cours
                        $ddateB = date('Y')."-07-15";
                    }
                    elseif ( date('md') > '0110' )
                    {
                        // prochaine box semaine du 15/04 de l'année en cours
                        $ddateB = date('Y')."-04-15";
                    }   
                    else       
                    {
                        // prochaine box semaine du 15/01 de l'année en cours
                        $ddateB = date('Y')."-01-15";
                    }     
                    $dateB = new DateTime($ddateB);
                    $formatWH = $dateB->format("W"); 
                    /*if ( $formatWH < 10 )
                    {
                        $formatWH = '0'.$formatWH;
                    }*/
                }
                elseif ( $formatWH < 10 )
                {
                    $formatWH = '0'.$formatWH;
                }

                $product['exped_ordre'] = date('Y').$formatWH;
                $product['exped_semaine'] = $formatWH;
                if ( $formatWH < date('W') )
                {
                    $product['exped_ordre'] = (date('Y')+1).$formatWH;
                }

                //echo $product['exped_ordre'].'('.$warehouse_en_cours .'<'. date('W').')<br />';
/*
                elseif ( $warehouse_en_cours == -2 )
                {
                    $product['exped'] = 'suivant les saisons';
                }*/
                if ( $warehouse_en_cours == 0 )
                {
                    $product['exped'] = 'immédiate';
                }
                else 
                {
                    $year = date('Y');
                    if ( $warehouse_en_cours < date('W') )
                    {
                        $year++;
                    }
                    if ( $warehouse_en_cours == -2 )
                    {
                        $week = $formatWH;
                    }
                    else 
                    {
                        $week = $warehouse_en_cours;
                    }
                    if ( ($week-2) < 0 )
                    {
                        $start = date("d/m/Y", strtotime("January ".$year." first monday - ".abs($week-2)." weeks"));
                    }
                    else
                    {
                        $start = date("d/m/Y", strtotime("January ".$year." first monday + ".($week-2)." weeks"));
                    }
                    $end = date("d/m/Y", strtotime(substr($start, 6, 4)."/".substr($start, 3, 2)."/".substr($start, 0, 2)." + 4 days"));
                    $product['exped'] = 'entre le '.$start." et le ".$end;
                }
            }
            
            $wec  = array_column($this->_products, 'exped_ordre');
            $catd  = array_column($this->_products, 'id_category_default');
            array_multisort($wec, SORT_ASC, $catd, SORT_ASC, $this->_products);
            $products = $this->_products;
            $custom_payment = Module::getInstanceByName('ets_payment_with_fee');
            $custom_payment->getProductsPaypal($products);
            return $products;
        }
        else
        {
            $this->_products = Module::getInstanceByName('ets_extraoptions')->getProducts($this,$refresh,$id_product,$id_country,$fullInfos,$keepOrderPrices);
            $custom_payment = Module::getInstanceByName('ets_payment_with_fee');
            $custom_payment->getProductsPaypal($this->_products);
            return $this->_products;
        }
    }

    public function getPackageIdWarehouse($package, $id_carrier = null)
    {
        return $package['id_warehouse'];
    }

    public function getPackageList($flush = false)
    {
        $cache_key = (int) $this->id . '_' . (int) $this->id_address_delivery;
        if (isset(static::$cachePackageList[$cache_key]) && static::$cachePackageList[$cache_key] !== false && !$flush) {
            $cptPS = 0;
            $this->packageSimple = array();
            foreach (static::$cachePackageList[$cache_key] as $key => $warehouse_list) {
                foreach ($warehouse_list as $id_warehouse => $products_en_cours) {
                        foreach($products_en_cours['product_list'] as $prodPS)
                        {
                            $this->packageSimple[$cptPS][] = array('id_product' => $prodPS['id_product'], 'price' => $prodPS['total_wt']);
                        }
                        $cptPS++;
                }
            }
            return static::$cachePackageList[$cache_key];
        }

        /* -G- changement pour multi shipping */
        //$cpt_wh = 0;

        $product_list = $this->getProducts($flush);
/*foreach($product_list as $ppp)
{
    echo $ppp['id_product'].'<br />';
}*/

        // Step 1 : Get product informations (warehouse_list and carrier_list), count warehouse
        // Determine the best warehouse to determine the packages
        // For that we count the number of time we can use a warehouse for a specific delivery address
        $warehouse_count_by_address = [];

        $stock_management_active = Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT');
        $stock_management_active = 1;
        foreach ($product_list as &$product) {
            if ((int) $product['id_address_delivery'] == 0) {
                $product['id_address_delivery'] = (int) $this->id_address_delivery;
            }

            if (!isset($warehouse_count_by_address[$product['id_address_delivery']])) {
                $warehouse_count_by_address[$product['id_address_delivery']] = [];
            }

            $product['warehouse_list'] = [];
            $product['advanced_stock_management'] = 1;
           
            if ($stock_management_active &&
                (int) $product['advanced_stock_management'] == 1) {    
                
                $query0 = new DbQuery();
                $query0->select('c.optim');
                $query0->from('cart', 'c');
                $query0->where('c.id_cart = ' . (int) $this->id);
                $rangee_s0 = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query0);

                $query1 = new DbQuery();
                $query1->select('cd.semaine');
                $query1->from('product', 'p');
                $query1->innerJoin('custom_delivery', 'cd', 'cd.id_category = p.id_category_default');
                $query1->where('cd.id_cart = ' . (int) $this->id);
                $query1->where('p.id_product = ' . (int) $product['id_product']);
                $rangee_s1 = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query1);
                if ( !isset($rangee_s1[0]['semaine']) && ($rangee_s0[0]['optim'] == 0) )
                {
                    $query = new DbQuery();
                    $query->select('acc.semaines, p.id_category_default');
                    $query->from('product', 'p');
                    $query->innerJoin('aw_custom_category', 'acc', 'p.id_category_default = acc.id_category');
                    $query->where('p.id_product = ' . (int) $product['id_product']);

                    $rangee_s = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);

                    $retour_semaines = array();
                    if ( isset($rangee_s[0]['semaines']) && !empty($rangee_s[0]['semaines']) )
                    {
                        /*$exp_tmp = explode(';', $rangee_s[0]['semaines']);
                        $query_tmp = 'INSERT INTO ps_custom_delivery SET id_cart = "'.$this->id.'", semaine = "'.$exp_tmp[0].'", id_category = "'.$rangee_s[0]['id_category_default'].'";';
                        Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($query_tmp);*/
                    }
                    else 
                    {
                        $query_tmp = 'INSERT INTO ps_custom_delivery SET id_cart = "'.$this->id.'", semaine = "0", id_category = "0";';
                        Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($query_tmp);
                    }
                }

                $warehouse_list = Warehouse::getProductWarehouseListLBG($product['id_product'], $product['id_product_attribute'], $this->id_shop, $this->id);
                $warehouse_list_default = Warehouse::getProductWarehouseList($product['id_product'], $product['id_product_attribute'], $this->id_shop);

                $warehouse_in_stock = [];
                foreach ($warehouse_list as $key => $warehouse) {
                    $warehouse_in_stock[] = $warehouse;
                }
                if ( count($warehouse_in_stock) == 0 )
                {
                    $warehouse_list = [0 => ['id_warehouse' => 0]];
                    $warehouse_list_default = [0 => ['id_warehouse' => 0]];
                }
                
                $product['in_stock'] = StockAvailable::getQuantityAvailableByProduct($product['id_product'], $product['id_product_attribute']) > 0;
            } else {
                //simulate default warehouse
                /* -G- changement pour multi shipping */
                $warehouse_list = [0 => ['id_warehouse' => 0]];
                $warehouse_list_default = [0 => ['id_warehouse' => 0]];
                $product['in_stock'] = StockAvailable::getQuantityAvailableByProduct($product['id_product'], $product['id_product_attribute']) > 0;
            }
            foreach ($warehouse_list as $warehouse) {
                $product['warehouse_list'][$warehouse['id_warehouse']] = $warehouse['id_warehouse'];
                if (!isset($warehouse_count_by_address[$product['id_address_delivery']][$warehouse['id_warehouse']])) {
                    $warehouse_count_by_address[$product['id_address_delivery']][$warehouse['id_warehouse']] = 0;
                }

                ++$warehouse_count_by_address[$product['id_address_delivery']][$warehouse['id_warehouse']];
            }
            
            foreach ($warehouse_list_default as $warehouse) {
                $product['warehouse_list_default'][$warehouse['id_warehouse']] = $warehouse['id_warehouse'];
            }
        }
        unset($product);

        /* -G- changement pour multi shipping */
        //arsort($warehouse_count_by_address);
        foreach ($warehouse_count_by_address as &$addEC)
        {
            arsort($addEC);
        }

        /*echo '<pre>';
        print_r($warehouse_count_by_address);
        echo '</pre>';*/

        // Step 2 : Group product by warehouse
        $grouped_by_warehouse = [];

        foreach ($product_list as &$product) {
            if (!isset($grouped_by_warehouse[$product['id_address_delivery']])) {
                $grouped_by_warehouse[$product['id_address_delivery']] = [
                    'in_stock' => [],
                    'out_of_stock' => [],
                ];
            }

            $product['carrier_list'] = [];
            $id_warehouse = -1;
            //$id_warehouse = 0;
            foreach ($warehouse_count_by_address[$product['id_address_delivery']] as $id_war => $val) {
                if (array_key_exists((int) $id_war, $product['warehouse_list'])) {
                    
                    /* -G- changement pour multi shipping */
                    $id_warForCarrier = 0;

                    $product['carrier_list'] = array_replace(
                        $product['carrier_list'],
                        Carrier::getAvailableCarrierList(
                            new Product($product['id_product']),
                            //$id_war,
                            $id_warForCarrier,
                            $product['id_address_delivery'],
                            null,
                            $this
                        )
                    );
                    //if (!$id_warehouse) {
                    if ($id_warehouse == -1) {
                        $id_warehouse = (int) $id_war;
                    }
                }
            }
            if ( $id_warehouse == -1 )
            {
                $id_warehouse = 0;
            }

            if (!isset($grouped_by_warehouse[$product['id_address_delivery']]['in_stock'][$id_warehouse])) {
                $grouped_by_warehouse[$product['id_address_delivery']]['in_stock'][$id_warehouse] = [];
                $grouped_by_warehouse[$product['id_address_delivery']]['out_of_stock'][$id_warehouse] = [];
            }

            if (!$this->allow_seperated_package) {
                $key = 'in_stock';
            } else {
                $key = $product['in_stock'] ? 'in_stock' : 'out_of_stock';
                $product_quantity_in_stock = StockAvailable::getQuantityAvailableByProduct($product['id_product'], $product['id_product_attribute']);
                if ($product['in_stock'] && $product['cart_quantity'] > $product_quantity_in_stock) {
                    $out_stock_part = $product['cart_quantity'] - $product_quantity_in_stock;
                    $product_bis = $product;
                    $product_bis['cart_quantity'] = $out_stock_part;
                    $product_bis['in_stock'] = 0;
                    $product['cart_quantity'] -= $out_stock_part;
                    $grouped_by_warehouse[$product['id_address_delivery']]['out_of_stock'][$id_warehouse][] = $product_bis;
                }
            }

            if (empty($product['carrier_list'])) {
                $product['carrier_list'] = [0 => 0];
            }

            $grouped_by_warehouse[$product['id_address_delivery']][$key][$id_warehouse][] = $product;
        }
        unset($product);
        
        /*echo '<pre style="background-color:pink">';
        print_r($grouped_by_warehouse);
        echo '</pre>';*/

        // Step 3 : grouped product from grouped_by_warehouse by available carriers
        $grouped_by_carriers = [];
        foreach ($grouped_by_warehouse as $id_address_delivery => $products_in_stock_list) {
            if (!isset($grouped_by_carriers[$id_address_delivery])) {
                $grouped_by_carriers[$id_address_delivery] = [
                    'in_stock' => [],
                    'out_of_stock' => [],
                ];
            }
            foreach ($products_in_stock_list as $key => $warehouse_list) {
                if (!isset($grouped_by_carriers[$id_address_delivery][$key])) {
                    $grouped_by_carriers[$id_address_delivery][$key] = [];
                }
                foreach ($warehouse_list as $id_warehouse => $product_list) {
                    if (!isset($grouped_by_carriers[$id_address_delivery][$key][$id_warehouse])) {
                        $grouped_by_carriers[$id_address_delivery][$key][$id_warehouse] = [];
                    }
                    foreach ($product_list as $product) {
                        $package_carriers_key = implode(',', $product['carrier_list']);

                        if (!isset($grouped_by_carriers[$id_address_delivery][$key][$id_warehouse][$package_carriers_key])) {
                            $grouped_by_carriers[$id_address_delivery][$key][$id_warehouse][$package_carriers_key] = [
                                'product_list' => [],
                                'carrier_list' => $product['carrier_list'],
                                'warehouse_list' => $product['warehouse_list'],
                            ];
                        }

                        $grouped_by_carriers[$id_address_delivery][$key][$id_warehouse][$package_carriers_key]['product_list'][] = $product;
                    }
                }
            }
        }

        $package_list = [];
        // Step 4 : merge product from grouped_by_carriers into $package to minimize the number of package
        foreach ($grouped_by_carriers as $id_address_delivery => $products_in_stock_list) {
            if (!isset($package_list[$id_address_delivery])) {
                $package_list[$id_address_delivery] = [
                    'in_stock' => [],
                    'out_of_stock' => [],
                ];
            }

            foreach ($products_in_stock_list as $key => $warehouse_list) {
                if (!isset($package_list[$id_address_delivery][$key])) {
                    $package_list[$id_address_delivery][$key] = [];
                }
                // Count occurance of each carriers to minimize the number of packages
                $carrier_count = [];
                foreach ($warehouse_list as $id_warehouse => $products_grouped_by_carriers) {
                    foreach ($products_grouped_by_carriers as $data) {
                        foreach ($data['carrier_list'] as $id_carrier) {
                            if (!isset($carrier_count[$id_carrier])) {
                                $carrier_count[$id_carrier] = 0;
                            }
                            ++$carrier_count[$id_carrier];
                        }
                    }
                }
                arsort($carrier_count);
                foreach ($warehouse_list as $id_warehouse => $products_grouped_by_carriers) {
                    if (!isset($package_list[$id_address_delivery][$key][$id_warehouse])) {
                        $package_list[$id_address_delivery][$key][$id_warehouse] = [];
                    }
                    foreach ($products_grouped_by_carriers as $data) {
                        foreach ($carrier_count as $id_carrier => $rate) {
                            if (array_key_exists($id_carrier, $data['carrier_list'])) {
                                if (!isset($package_list[$id_address_delivery][$key][$id_warehouse][$id_carrier])) {
                                    $package_list[$id_address_delivery][$key][$id_warehouse][$id_carrier] = [
                                        'carrier_list' => $data['carrier_list'],
                                        'warehouse_list' => $data['warehouse_list'],
                                        'product_list' => [],
                                    ];
                                }
                                $package_list[$id_address_delivery][$key][$id_warehouse][$id_carrier]['carrier_list'] =
                                    array_intersect($package_list[$id_address_delivery][$key][$id_warehouse][$id_carrier]['carrier_list'], $data['carrier_list']);
                                $package_list[$id_address_delivery][$key][$id_warehouse][$id_carrier]['product_list'] =
                                    array_merge($package_list[$id_address_delivery][$key][$id_warehouse][$id_carrier]['product_list'], $data['product_list']);

                                break;
                            }
                        }
                    }
                }
            }
        }

        // Step 5 : Reduce depth of $package_list
        $final_package_list = [];
        foreach ($package_list as $id_address_delivery => $products_in_stock_list) {
            if (!isset($final_package_list[$id_address_delivery])) {
                $final_package_list[$id_address_delivery] = [];
            }

            $cptPS = 0;
            foreach ($products_in_stock_list as $key => $warehouse_list) {
                foreach ($warehouse_list as $id_warehouse => $products_grouped_by_carriers) {
                    foreach ($products_grouped_by_carriers as $data) {
                        $final_package_list[$id_address_delivery][] = [
                            'product_list' => $data['product_list'],
                            'carrier_list' => $data['carrier_list'],
                            'warehouse_list' => $data['warehouse_list'],
                            'id_warehouse' => $id_warehouse,
                        ];
                        foreach($data['product_list'] as $prodPS)
                        {
                            $this->packageSimple[$cptPS][] = array('id_product' => $prodPS['id_product'], 'price' => $prodPS['total_wt']);
                        }
                        $cptPS++;
                    }
                }
            }
        }

        static::$cachePackageList[$cache_key] = $final_package_list;

        /*echo '<pre>';
        print_r($final_package_list);
        echo '</pre>';*/

        return $final_package_list;
    }

    public function getTotalShippingCostDetail($delivery_option = null, $use_tax = true, Country $default_country = null)
    {
        if (isset(Context::getContext()->cookie->id_country)) {
            $default_country = new Country((int) Context::getContext()->cookie->id_country);
        }
        if (null === $delivery_option) {
            $delivery_option = $this->getDeliveryOption($default_country, false, false);
        }

        /* CALCUL DU PANIER */
        $valCC = 0;
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
				$valCC += $otherCartRuleCC['reduction_amount'];
			}
		}

        /*foreach($orderTotalProdAW as $cleOT => $item_ot_wh)
        {
            $reducEC = ($orderTotalProdAWOT[$cleOT] - $item_ot_wh);
            if ( $reducEC >= $valCC )
            {
                $orderTotalProdAW[$cleOT] += $valCC;
            }
            else 
            {
                
            }
        }*/
        
$orderTotalProdAW = 0;
        /* **************** */

        $_total_shipping = [
            'with_tax' => 0,
            'without_tax' => 0,
            'detail' => array()
        ];
        $delivery_option_list = $this->getDeliveryOptionList($default_country);
        
        
            $fullPack = $this->getPackageList();
            /*echo '<pre>';
        print_r($fullPack[$this->id_address_delivery]);
        echo '</pre><hr>';*/

        $totalsEC = array();
        $cptPack = 1;
        if ( isset($fullPack[$this->id_address_delivery]) )
        {
            foreach($fullPack[$this->id_address_delivery] as &$packEC)
            {
                $formatWH = $packEC['id_warehouse'];
                if ( $formatWH == 0 )
                {
                    $formatWH = date('W');
                    //$formatWH = '00';
                }
                if ( $formatWH == -2 )
                {
                    if ( date('md') > '1010' )
                    {
                        // prochaine box semaine du 15/01 de l'année suivante
                        $ddateB = (date('Y')+1)."-01-15";
                    }
                    elseif ( date('md') > '0710' )
                    {
                        // prochaine box semaine du 15/10 de l'année en cours
                        $ddateB = date('Y')."-10-15";
                    }
                    elseif ( date('md') > '0410' )
                    {
                        // prochaine box semaine du 15/07 de l'année en cours
                        $ddateB = date('Y')."-07-15";
                    }
                    elseif ( date('md') > '0110' )
                    {
                        // prochaine box semaine du 15/04 de l'année en cours
                        $ddateB = date('Y')."-04-15";
                    }   
                    else       
                    {
                        // prochaine box semaine du 15/01 de l'année en cours
                        $ddateB = date('Y')."-01-15";
                    }     
                    $dateB = new DateTime($ddateB);
                    $formatWH = $dateB->format("W"); 
                    if ( $formatWH < 10 )
                    {
                        $formatWH = '0'.$formatWH;
                    }  
                }
                elseif ( $formatWH < 10 )
                {
                    $formatWH = '0'.$formatWH;
                }

                $packEC['exped_ordre'] = date('Y').$formatWH;
                if ( $formatWH < date('W') )
                {
                    $packEC['exped_ordre'] = (date('Y')+1).$formatWH;
                }
            }

            $wec  = array_column($fullPack[$this->id_address_delivery], 'exped_ordre');
            array_multisort($wec, SORT_ASC, $fullPack[$this->id_address_delivery]);

            /*echo '<pre>';
            print_r($fullPack[$this->id_address_delivery]);
            echo '</pre><hr>';*/

            foreach($fullPack[$this->id_address_delivery] as $packECAux)
            {
                foreach($packECAux['product_list'] as $prodEC)
                {
                    if ( !isset($totalsEC[$cptPack]) )
                    {
                        $totalsEC[$cptPack] = 0;
                    }
                    //echo $cptPack.' > '.$prodEC['total_wt'].'<br />';
                    $totalsEC[$cptPack] += $prodEC['total_wt'];
                }
                $cptPack++;
            }
        }

        /*echo '<pre>';
        print_r($totalsEC);
        echo '</pre>';*/
        
        /*echo '<pre>';
        print_r($delivery_option);
        echo '</pre>';*/
        
        foreach ($delivery_option as $id_address => $key) {

            if (!isset($delivery_option_list[$id_address]) || !isset($delivery_option_list[$id_address][$key])) {
                continue;
            }

            $_total_shipping['with_tax'] += $delivery_option_list[$id_address][$key]['total_price_with_tax'];
            $_total_shipping['without_tax'] += $delivery_option_list[$id_address][$key]['total_price_without_tax'];

            $explode_key = explode(',', $key);
            $cpt_key = 1;
            foreach ( $explode_key as $list_key )
            {
                if ( !empty($list_key) )
                {
                    $carrier = new Carrier($list_key);
                    $id_zone = 1;
                    $orderTotalProdAW = $totalsEC[$cpt_key];
                    if ( $id_zone == 1 && $orderTotalProdAW >= 59 && ($carrier->id_reference == 192 || $carrier->id_reference == 191 || $carrier->id_reference == 189 || $carrier->id_reference == 348 || $carrier->id_reference == 155) )
		            {
                        $_total_shipping['detail'][$list_key.'_'.$cpt_key] = array('with_tax' => 0, 'without_tax' => 0); 
                    }
                    else
                    {
                        $prix_transpo = $this->getPackageShippingCost($carrier->id, true, null, $fullPack[$this->id_address_delivery][($cpt_key-1)]['product_list']);
                        $prix_transpo_wt = $this->getPackageShippingCost($carrier->id, false, null, $fullPack[$this->id_address_delivery][($cpt_key-1)]['product_list']);
                       
                        $_total_shipping['detail'][$list_key.'_'.$cpt_key] = array(
                            'with_tax' => $prix_transpo,
                            'without_tax' => $prix_transpo_wt
                        ); 
                    }
                    $cpt_key++;
                }
            }
        }
        /*echo '<pre>';
        print_r($_total_shipping);
        echo '</pre>';*/
        return $_total_shipping;
    }
    
    public function getOrderTotalDetail(
        $withTaxes = true,
        $type = Cart::BOTH,
        $products = null,
        $id_carrier = null,
        $use_cache = false,
        bool $keepOrderPrices = false
    ) {
        if ((int) $id_carrier <= 0) {
            $id_carrier = null;
        }

        // deprecated type
        if ($type == Cart::ONLY_PRODUCTS_WITHOUT_SHIPPING) {
            $type = Cart::ONLY_PRODUCTS;
        }

        // check type
        $type = (int) $type;
        $allowedTypes = [
            Cart::ONLY_PRODUCTS,
            Cart::ONLY_DISCOUNTS,
            Cart::BOTH,
            Cart::BOTH_WITHOUT_SHIPPING,
            Cart::ONLY_SHIPPING,
            Cart::ONLY_WRAPPING,
            Cart::ONLY_PHYSICAL_PRODUCTS_WITHOUT_SHIPPING,
        ];
        if (!in_array($type, $allowedTypes)) {
            throw new \Exception('Invalid calculation type: ' . $type);
        }

        // EARLY RETURNS

        // if cart rules are not used
        if ($type == Cart::ONLY_DISCOUNTS && !CartRule::isFeatureActive()) {
            return 0;
        }
        // no shipping cost if is a cart with only virtuals products
        $virtual = $this->isVirtualCart();
        if ($virtual && $type == Cart::ONLY_SHIPPING) {
            return 0;
        }
        if ($virtual && $type == Cart::BOTH) {
            $type = Cart::BOTH_WITHOUT_SHIPPING;
        }

        // filter products
        if (null === $products) {
            $products = $this->getProducts(false, false, null, true, $keepOrderPrices);
        }
        if ($type == Cart::ONLY_PHYSICAL_PRODUCTS_WITHOUT_SHIPPING) {
            foreach ($products as $key => $product) {
                if (!empty($product['is_virtual'])) {
                    unset($products[$key]);
                }
            }
            $type = Cart::ONLY_PRODUCTS;
        }

        if ($type == Cart::ONLY_PRODUCTS) {
            foreach ($products as $key => $product) {
                if (!empty($product['is_gift'])) {
                    unset($products[$key]);
                }
            }
        }

        if (Tax::excludeTaxeOption()) {
            $withTaxes = false;
        }

        // CART CALCULATION
        $cartRules = [];
        if (in_array($type, [Cart::BOTH, Cart::BOTH_WITHOUT_SHIPPING, Cart::ONLY_DISCOUNTS])) {
            $cartRules = $this->getTotalCalculationCartRules($type, $type == Cart::BOTH);
        }
        $computePrecision = Context::getContext()->getComputingPrecision();

        $tab_final = array();
        $tab_by_wh = array();
        foreach($products as $productEC)
        {
            //echo $productEC['id_product'].' || '.$productEC['warehouse_en_cours'].'<br />';
            if ( isset($productEC['warehouse_en_cours']) )
            {
                $tab_by_wh[$productEC['warehouse_en_cours']][] = $productEC;
            }
        }

        foreach($tab_by_wh as $wh => $prodByWh)
        {
            $calculator = $this->newCalculator($prodByWh, $cartRules, $id_carrier, $computePrecision, $keepOrderPrices);
            switch ($type) {
                case Cart::ONLY_SHIPPING:
                    $calculator->calculateRows();
                    $calculator->calculateFees();
                    $amount = $calculator->getFees()->getInitialShippingFees();

                    break;
                case Cart::ONLY_WRAPPING:
                    $calculator->calculateRows();
                    $calculator->calculateFees();
                    $amount = $calculator->getFees()->getInitialWrappingFees();

                    break;
                case Cart::BOTH:
                    $calculator->processCalculation();
                    $amount = $calculator->getTotal();

                    break;
                case Cart::BOTH_WITHOUT_SHIPPING:
                    $calculator->calculateRows();
                    // dont process free shipping to avoid calculation loop (and maximum nested functions !)
                    $calculator->calculateCartRulesWithoutFreeShipping();
                    $amount = $calculator->getTotal(true);
                    break;
                case Cart::ONLY_PRODUCTS:
                    $calculator->calculateRows();
                    $amount = $calculator->getRowTotal();

                    break;
                case Cart::ONLY_DISCOUNTS:
                    $calculator->processCalculation();
                    $amount = $calculator->getDiscountTotal();

                    break;
                default:
                    throw new \Exception('unknown cart calculation type : ' . $type);
            }

            // TAXES ?

            $value = $withTaxes ? $amount->getTaxIncluded() : $amount->getTaxExcluded();

            // ROUND AND RETURN

            //return Tools::ps_round($value, $computePrecision);
            $tab_final['detail'][$wh] = Tools::ps_round($value, $computePrecision);
        }
        return $tab_final;
    }

    /**
     * Get all formatted deliveries options available for the current cart.
	 * March 2024: If we allow choosing one carrier among a list of carriers by warehouse
	 * Not used actually but can be used someday (must be placed in Cart.php override)
     */
    public function getMyDeliveryOptionList(Country $default_country = null, $flush = false)
    {
        $delivery_option_list = array();
        $package_list = $this->getPackageList($flush);

        if ( isset($package_list[$this->id_address_delivery]) )
        {
        foreach($package_list[$this->id_address_delivery] as &$packEC)
        {                
            $formatWH = $packEC['id_warehouse'];
            if ( $formatWH == 0 )
            {
                $formatWH = date('W');
                //$formatWH = '00';
            }
            if ( $formatWH == -2 )
            {
                if ( date('md') > '1010' )
                {
                    // prochaine box semaine du 15/01 de l'année suivante
                    $ddateB = (date('Y')+1)."-01-15";
                }
                elseif ( date('md') > '0710' )
                {
                    // prochaine box semaine du 15/10 de l'année en cours
                    $ddateB = date('Y')."-10-15";
                }
                elseif ( date('md') > '0410' )
                {
                    // prochaine box semaine du 15/07 de l'année en cours
                    $ddateB = date('Y')."-07-15";
                }
                elseif ( date('md') > '0110' )
                {
                    // prochaine box semaine du 15/04 de l'année en cours
                    $ddateB = date('Y')."-04-15";
                }   
                else       
                {
                    // prochaine box semaine du 15/01 de l'année en cours
                    $ddateB = date('Y')."-01-15";
                }     
                $dateB = new DateTime($ddateB);
                $formatWH = $dateB->format("W"); 
                /*if ( $formatWH < 10 )
                {
                    $formatWH = '0'.$formatWH;
                } */ 
            }
            elseif ( $formatWH < 10 )
            {
                $formatWH = '0'.$formatWH;
            }

            $packEC['exped_ordre'] = date('Y').$formatWH;
            if ( $formatWH < date('W') )
            {
                $packEC['exped_ordre'] = (date('Y')+1).$formatWH;
            }
        }

        $wec  = array_column($package_list[$this->id_address_delivery], 'exped_ordre');
        array_multisort($wec, SORT_ASC, $package_list[$this->id_address_delivery]);
        }

        // For each address
        foreach ($package_list as $id_address => $packages) {
            $delivery_option_list[$id_address] = array();
            $carriers_price = array();
            // Get country
            $country = $id_address ? new Country((new Address($id_address))->id_country) : $default_country;

            // For each carrier, calculate his price
            foreach ($packages as $id_package => $package) {


                $total_quantity = count($package['product_list']); 

                // 5 paquets de 5g maximum
                $notDefault = false;
                foreach ($package['product_list'] as $product)
                {
                    if ( $product['weight_attribute'] > 0.005 )
                    {          
                        error_log($product['weight_attribute']);          
                        $notDefault = true; 
                        break;
                    }
                }
                $supprLV = false;
                if($total_quantity > 5 || $notDefault == true)
                {
                    $supprLV = true;
                }

                
                $removeCL = -1;

                if ( $supprLV == true )
                {
                    foreach($package['carrier_list'] as $keyCL => $valueCL)
                    {
                        $carrierCL = new Carrier($valueCL);
                        
                        if ( $carrierCL->id_reference == 342)
                        {
                            error_log('22222222222222222222');
                            $removeCL = $keyCL;
                        }
                    }
                }
                if($removeCL != -1){
					unset($package['carrier_list'][$removeCL]);
				}	



				$key = '';
				$best_price_carriers = $best_price_carrier = array();
				// Handle not A.S.M
				if ((int)$package['id_warehouse'] == 0) {
					$package_carrier_list_tmp = WorkshopAsm::getBestCarriersForNotAsmProducts(
						$country,
						$package,
						$this
					);
					if (count($package_carrier_list_tmp)) {
						$package['carrier_list'] = array_unique($package_carrier_list_tmp);
                        $pckTmpArray = array();
                        foreach($package['carrier_list'] as $pckTmp)
                        {
                            $pckTmpArray[] = $pckTmp;
                        }
                        $package['carrier_list'] = $pckTmpArray;
					}
				}
                //error_log('A : '.print_r($package['carrier_list'], true));
                foreach ($package['carrier_list'] as $id_carrier) {
					$key .= $id_carrier . ',';
                	$best_price_carriers[] = $id_carrier;
                    $carriers_price[$id_carrier] = array(
                        'without_tax' => $this->getPackageShippingCost((int)$id_carrier, false, $country, $package['product_list']),
                        'with_tax' => $this->getPackageShippingCost((int)$id_carrier, true, $country, $package['product_list']),
                    );
                }
				foreach ($best_price_carriers as $id_carrier) {
					$best_price_carrier[$id_carrier]['price_with_tax'] = $carriers_price[$id_carrier]['with_tax'];
					$best_price_carrier[$id_carrier]['price_without_tax'] = $carriers_price[$id_carrier]['without_tax'];
				}
                //error_log('B : '.print_r($best_price_carrier, true));
				// Add the delivery option according to the warehouse
				// Add comma (,) just to transform the key from integer to string: WHY?
				// => because later during checkout, the json "delivery_option_list" is ordered automatically according to the warehouse interger ID which causes malfunctioning
				// While string keys are not ordered
				$delivery_option_list[$id_address][$package['id_warehouse'].','][$key] = array(
					'carrier_list' => $best_price_carrier,
					//'carrier_list' => $package['carrier_list'],
					'unique_carrier' => (count($best_price_carrier) <= 1),
					//'unique_carrier' => (count($package['carrier_list']) <= 1),
				);
            }
        }
        return $delivery_option_list;
    }
}