<?php

use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;

class Multi_Carrier extends Module {
    
     public function __construct() {
        $this->name = 'multi_carrier';
        $this->tab = 'others';
        $this->author = 'La Bonne Graine';
        $this->version = '1.0.0';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Transporteurs multiples');
        $this->description = $this->l('Par package');
        $this->ps_versions_compliancy = array('min' => '1.7.1', 'max' => _PS_VERSION_);
    }
    
   public function install() {
		/*$awCustomCategory = "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."aw_custom_category` (
            `id_aw_custom_category` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
            `id_category` INT(10) NULL DEFAULT 0,
            `date_precommande` VARCHAR(255) NULL DEFAULT NULL,
            PRIMARY KEY (`id_aw_custom_category`)
        )";
		Db::getInstance()->execute($awCustomCategory);*/
		
        if (!parent::install() || !$this->_installSql()
                //Pour les hooks suivants regarder le fichier src\PrestaShopBundle\Resources\views\Admin\Product\form.html.twig
                || ! $this->registerHook('displayHeader')
        ) {
            return false;
        }

        return true;
    }
    
     public function uninstall() {
        return parent::uninstall() && $this->_unInstallSql();
    }

    /**
     * Modifications sql du module
     * @return boolean
     */
    protected function _installSql() {
        return true;
    }

    /**
     * Suppression des modifications sql du module
     * @return boolean
     */
    protected function _unInstallSql() {
		return true;
    }
    
    public function hookDisplayHeader()
    {
        require_once(dirname(__FILE__).'/classes/Warehouse.php');
        require_once(dirname(__FILE__).'/classes/WorkshopAsm.php');
        require_once(dirname(__FILE__).'/classes/WarehouseStock.php');
        require_once(dirname(__FILE__).'/classes/WarehouseStockMvt.php');

        $this->page_name = Dispatcher::getInstance()->getController();
        $allow_multicarriers_cart = 1;
        $js_version = 'v='.time();

        // Checkout page and multi-shipping (multi-carriers) option enabled
        if ($this->page_name == 'order' && $allow_multicarriers_cart &&
            WarehouseStock::isMultiShipping($this->context->cart)) {// <= is multishipping?
			$this->context->controller->registerJavascript(
				'module-'.$this->name.'-order',
				'modules/'.$this->name.'/views/js/order.js',
				array('position' => 'bottom', 'priority' => 99999999)
			);
            $this->context->controller->addJqueryPlugin('fancybox');
            $this->context->controller->addCSS($this->_path.'views/css/wkwarehouses.css', 'all');

            /* Check delivery address of each product in cart and try to fix it if possible */
            WarehouseStock::assignRightDeliveryAddressToEachProductInCart($this->context->cart);

            if (class_exists('PrestaShop\PrestaShop\Adapter\Presenter\Cart\CartPresenter')) {
                $presenter = new PrestaShop\PrestaShop\Adapter\Presenter\Cart\CartPresenter();
            } else {
                $presenter = new PrestaShop\PrestaShop\Adapter\Cart\CartPresenter();
            }
            if (class_exists('PrestaShop\PrestaShop\Adapter\Presenter\Object\ObjectPresenter')) {
                $object_presenter = new PrestaShop\PrestaShop\Adapter\Presenter\Object\ObjectPresenter();
            } else {
                $object_presenter = new PrestaShop\PrestaShop\Adapter\ObjectPresenter();
            }

            $presented_cart = $presenter->present($this->context->cart);
            $id_lang = (int)$this->context->language->id;

            if (count($presented_cart['products']) > 0) {
                $cart_collection = array();

                /***** Generate delivery addresses list related to each product ******/
                /***********************************************************************/
                /*if (Configuration::get('WKWAREHOUSE_ALLOW_MULTI_ADDRESSES')) {
                    foreach ($presented_cart['products'] as $cart_line) {
                        $id_product = (int)$cart_line['id_product'];
                        $id_product_attribute = (int)$cart_line['id_product_attribute'];
                        $id_address_delivery = (int)$cart_line['id_address_delivery'];
                        $product = new Product($id_product, false);

                        $product_tmp = array();
                        $product_tmp['id_product'] = $id_product;
                        $product_tmp['id_product_attribute'] = $id_product_attribute;
                        $product_tmp['id_address_delivery'] = $id_address_delivery;
                        $format = array('cart', 'default');
                        $product_tmp['image'] = $cart_line['cover']['bySize'][$format[0].'_'.$format[1]]['url'];
                        $product_tmp['url'] = $cart_line['url'];
                        $product_tmp['has_discount'] = $cart_line['has_discount'];
                        $product_tmp['name'] = $cart_line['name'];
                        $product_tmp['discount_type'] = $cart_line['discount_type'];
                        $product_tmp['regular_price'] = $cart_line['regular_price'];
                        $product_tmp['discount_percentage_absolute'] = $cart_line['discount_percentage_absolute'];
                        $product_tmp['discount_to_display'] = $cart_line['discount_to_display'];
                        $product_tmp['price'] = $cart_line['price'];
                        $product_tmp['unit_price_full'] = $cart_line['unit_price_full'];
                        $attributes = array();
                        foreach ($cart_line['attributes'] as $k => $attribute) {
                            array_push($attributes, $k.': '.$attribute);
                        }
                        $product_tmp['attributes'] = $attributes;

                        // Get all customer delivery addresses 
                        $addresses = $this->context->customer->getAddresses($id_lang);

                        $id_warehouse = 0;
                        $result = WarehouseStock::productIsPresentInCart(
                            $this->context->cart->id,
                            $id_product,
                            $id_product_attribute
                        );
                        if ($result && $result['id_warehouse'] > 0 && $product->advanced_stock_management) {
                            $id_warehouse = (int)$result['id_warehouse'];
                            // Look for the customer addresses that match with the warehouse 
                            $warehouse = new StoreHouse($id_warehouse, $id_lang);

                            if (Validate::isLoadedObject($warehouse) && Address::isCountryActiveById($warehouse->id_address)) {
                                $product_tmp['warehouse_name'] = $warehouse->name;

                                $wa = Address::getCountryAndState($warehouse->id_address);
                                $warehouse_country = new Country($wa['id_country'], $id_lang);
    
                                // Add warehouse country informations 
                                $product_tmp['warehouse_country_name'] = $warehouse_country->name;
                                // Get the warehouse zone 
                                $id_zone = $warehouse_country->id_zone;
                            }
                        } else {
                            // Handled by Normal stock management 
                            $carriers_list = WarehouseStock::getAvailableCarrierList(
								$product,
								null,
								$id_address_delivery,
								$id_product_attribute
							);
                            if (empty($carriers_list)) {
                            // product can not be delivered to that delivery address
                                $id_zone = 0;
                                if (count($product->getCarriers())) {
                                    // Get the best carrier according to its assigned zones && propose it to user
                                    $best_carrier = WarehouseStock::getBestAvailableProductCarrier($product->id);
                                    if ($best_carrier) {
                                        $id_zone = $best_carrier['id_zone'];
                                        // Get all countries
                                        if (Configuration::get('PS_RESTRICT_DELIVERED_COUNTRIES')) {
                                            $availableCountries = Carrier::getDeliveredCountries($id_lang, true, true);
                                        } else {
                                            $availableCountries = Country::getCountries($id_lang, true);
                                        }
                                        $countries_by_zone = array();
                                        foreach ($availableCountries as $country) {
                                            $countryObject = new Country($country['id_country'], $id_lang);
                                            if ($countryObject->id_zone == $id_zone) {
                                                $countries_by_zone[] = $countryObject->name;
                                            }
                                        }
                                        $product_tmp['best_zone'] = count($countries_by_zone) ? implode(', ', $countries_by_zone) : '';
                                    }
                                }
                            } else {
                                $wa = Address::getCountryAndState($id_address_delivery);
                                $id_zone = (new Country($wa['id_country']))->id_zone;
                            }
                        }

                        // Available delivery addresses for each product 
                        foreach ($addresses as $k => $address) {
                            $id_address_zone = Address::getZoneById((int)$address['id_address']);
                            if (isset($id_zone) && $id_address_zone != $id_zone) {
                                unset($addresses[$k]);
                            }
                        }
                        // Prepare the default delivery selected address 
                        foreach ($addresses as &$addr) {
                            $addr['selected'] = ($addr['id_address'] == $id_address_delivery ? 1 : 0);
                        }
                        $product_tmp['address_list'] = $addresses;
                        $product_tmp['id_warehouse'] = (int)$id_warehouse;
                        array_push($cart_collection, $product_tmp);
                    }
                }*/

                /***** Shipping methods according to the available delivery addresses in cart ****/
                /*************************************************************************************/
                $include_taxes = !Product::getTaxCalculationMethod((int)$this->context->cart->id_customer) && (int)Configuration::get('PS_TAX');
                $display_taxes_label = (Configuration::get('PS_TAX') && !Configuration::get('AEUC_LABEL_TAX_INC_EXC'));
				// Get the default selected carrier for each delivery address
				$selected_delivery_option = $this->context->cart->getDeliveryOption(null, false, false);
                
				if (Configuration::get('WKWAREHOUSE_MODE_MULTICARRIER_CHOICE') == 'carriers-combinations') {
					$delivery_option_list = $this->context->cart->getDeliveryOptionList(); // it's overrided
				} else {
					// group packages by warehouse, for each warehouse, look for its carriers
					$delivery_options_available = $this->context->cart->getMyDeliveryOptionList();
					/*echo '<pre>';
					print_r($delivery_options_available);
					echo '</pre>';*/
					/* Example of generated delivery_option_list grouped by warehouse
					Array
					(
						[10] => Array
							(
								[3 (warehouse ID)] => Array
									(
										[1,3, (carriers of the warehouse)] => Array
											(
												[carrier_list] => Array
													(
														[1] => Array
															(
																[price_with_tax] => 0
																[price_without_tax] => 0
																[logo] => 
															)
														[3] => Array
															(
																[price_with_tax] => 5
																[price_without_tax] => 5
																[logo] => 
															)
													)
												[unique_carrier] => 
											)
									)
								[1 (warehouse ID)] => Array
									(
										[6,(carriers of the warehouse)] => Array
											(
												[carrier_list] => Array
													(
														[6] => Array
															(
																[price_with_tax] => 7
																[price_without_tax] => 7
																[logo] => /ps/ps812/img/s/6.jpg
															)
					
													)
												[unique_carrier] => 1
											)
									)
							)
					)
				}*/
				}

                /*echo '<pre>';
                print_r($delivery_options_available);
                echo '</pre>';*/

				// Check if there is available carrier(s)
				if (count($selected_delivery_option)) {
					$carriers_in_cart = array();
					foreach ($selected_delivery_option as $delivery_option) {
						$carriers_in_cart = array_merge($carriers_in_cart, array_filter(explode(',', $delivery_option)));
					}
					/* If no carrier */
					if (empty($carriers_in_cart)) {
						$delivery_option_list = $selected_delivery_option = array();
					}
				}

                // Generate new delivery options list (just for display),
				// March 2024: If we allow choosing one carrier among a list of carriers by warehouse
				if (Configuration::get('WKWAREHOUSE_MODE_MULTICARRIER_CHOICE') == 'carriers-warehouses') {
					// For each warehouse, look for its carriers options :
					$warehouses_names = array();
					foreach ($delivery_options_available as $id_address => $packages) {
                        
						$selected_carriers = array();
						if (isset($selected_delivery_option[(int)$id_address])) {
							$selected_carriers = array_filter(explode(',', $selected_delivery_option[(int)$id_address]));
						}
						$d = 0; // index to look for the default carrier in $selected_carriers for each $delivery_option
						foreach ($packages as $id_warehouse_key => $delivery_option) {
							foreach ($delivery_option as $key => $value) {
								foreach ($value['carrier_list'] as $id_carrier => &$data) {
                        			$carrier_instance = array_merge($data, $object_presenter->present(new Carrier($id_carrier)));
									// is it default?
									$delivery_options_available[$id_address][$id_warehouse_key][$key]['carrier_list'][$id_carrier]['selected'] = 0;
									if ($id_carrier == $selected_carriers[$d]) {
										$delivery_options_available[$id_address][$id_warehouse_key][$key]['carrier_list'][$id_carrier]['selected'] = 1;
									}
									// logo
									if (file_exists(_PS_SHIP_IMG_DIR_.$id_carrier.'.jpg')) {
										$delivery_options_available[$id_address][$id_warehouse_key][$key]['carrier_list'][$id_carrier]['logo'] = _THEME_SHIP_DIR_.$id_carrier.'.jpg';
									} else {
										$delivery_options_available[$id_address][$id_warehouse_key][$key]['carrier_list'][$id_carrier]['logo'] = false;
									}
									// name
									$delivery_options_available[$id_address][$id_warehouse_key][$key]['carrier_list'][$id_carrier]['name'] = $carrier_instance['name'];
									// delay
									$delivery_options_available[$id_address][$id_warehouse_key][$key]['carrier_list'][$id_carrier]['delay'] = $carrier_instance['delay'][$id_lang];
									// price
									if ($this->isFreeShipping($this->context->cart, $carrier_instance)) {
										$price = $this->trans('Free', array(), 'Shop.Theme.Checkout');
									} else {
										if ($include_taxes) {
											if ($display_taxes_label) {
												$price = $this->trans(
													'%price% tax incl.',
													array('%price%' => (new PriceFormatter())->format($carrier_instance['price_with_tax'])),
													'Shop.Theme.Checkout'
												);
											}
										} else {
											if ($display_taxes_label) {
												$price = $this->trans(
													'%price% tax excl.',
													array('%price%' => (new PriceFormatter())->format($carrier_instance['price_without_tax'])),
													'Shop.Theme.Checkout'
												);
											}
										}
									}
									$delivery_options_available[$id_address][$id_warehouse_key][$key]['carrier_list'][$id_carrier]['price'] = $price;
									// extra content: If carrier related to a module, check for additionnal data to display
									$delivery_options_available[$id_address][$id_warehouse_key][$key]['carrier_list'][$id_carrier]['extraContent'] = '';
									if ($carrier_instance['is_module']) {
										if ($moduleId = Module::getModuleIdByName($carrier_instance['external_module_name'])) {
                							$carrier_instance['instance'] = new Carrier($id_carrier); // add carrier object needed by the hook (product_list is missing! under test)
											$delivery_options_available[$id_address][$id_warehouse_key][$key]['carrier_list'][$id_carrier]['extraContent'] = Hook::exec(
												'displayCarrierExtraContent',
												array('carrier' => $carrier_instance),
												$moduleId
											);
										}
									}
									$carrierTmp = new Carrier($id_carrier);
									if ( $carrierTmp->id_reference == 390 )
									{
										$delivery_options_available[$id_address][$id_warehouse_key][$key]['carrier_list'][$id_carrier]['extraContent'] = '<div class="div_cac"><span class="zone1">Vous avez choisi le retrait sur place à l\'adresse suivante :</span><br /><br />
		<span class="zone2">LA BONNE GRAINE<br />
		ZA LE PONTAIL<br />
		49540 AUBIGNE-SUR-LAYON</span><br /><br />
		Le service de click and collect est ouvert du <span class="zone3">lundi au vendredi* de 8h00 à 17h00<br />
		*Le mercredi de 8h00 à 16h00</span></div>';
									}
									elseif ( $carrierTmp->id_reference == 155 )
									{
										$delivery_options_available[$id_address][$id_warehouse_key][$key]['carrier_list'][$id_carrier]['extraContent'] = '<button type="button" class="btn btn-primary" id="dpd-select-pickup-point" name="dpdSelectPickupPoint" value="1">
			Sélectionner un Point Retrait
			</button>';
									}

									// collect warehouses names
									$id_warehouse = (int)rtrim($id_warehouse_key,',');
									//$warehouses_names[$id_warehouse] = (new StoreHouse($id_warehouse, $id_lang))->name;
									$warehouses_names[$id_warehouse] = $id_warehouse;
								}
								$d++;
							}
						}
					}
					Media::addJsDef(array(
						'warehouses_names' => $warehouses_names,
					));
				} else {
                // Generate new delivery options list (just for display)
				// For each package, get the best carrier (best price, range, weight, etc.)
                	$delivery_options_available = $methods_shipping_collection = array();
					if (isset($delivery_option_list) && count($delivery_option_list)) {
						foreach ($delivery_option_list as $id_address_delivery => $by_address) {
							if (isset($delivery_option_list[$id_address_delivery])) {
								$carriers_available = array();
	
								$package_multi_carriers = false;
								foreach ($by_address as $id_carriers_list => $carriers_list) {
									// IF some products must be delivered to the same address
									// but not delivered by the same carrier (each product has its own carrier => no intersection)
									if (count(array_filter(explode(',', $id_carriers_list))) > 1) {
										$package_multi_carriers = true;
									}
									foreach ($carriers_list as $carriers) {
										// iF we're processing carrier_list index from array
										if (is_array($carriers)) {
											/* default carrier in delivery_option */
											$selected_carrier = 0;
											if (isset($selected_delivery_option[(int)$id_address_delivery])) {
												$selected_carrier = $selected_delivery_option[(int)$id_address_delivery];
											}
											/* collect carriers names, delays, logos before */
											if ($package_multi_carriers) {
												$carriers_table = array();
												foreach ($carriers as $id_carrier => $carrier) {
													if ($id_carrier) {
														$carrier = array_merge($carrier, $object_presenter->present(new Carrier($id_carrier)));

														// Warehouse collection to be displayed below carrier name
														$warehouses_names = array();
														$before_name = '';
														$product_list = $carrier['product_list'];
														$show_pn = (int)Configuration::get('WKWAREHOUSE_PRODUCT_NAME_SHIPMENT_PART');
														$show_wn = (int)Configuration::get('WKWAREHOUSE_WH_NAME_SHIPMENT_PART');
														if ($show_pn || $show_wn) {
															if (count($product_list) == 1) {
																$prod = current($carrier['product_list']);
																if (!empty(current($prod['warehouse_list']))) {
																	$warehouses_names[] = ($show_pn ? '- '.$prod['name'].' ' : '').(
																		$show_wn ? '('.(new StoreHouse(current($prod['warehouse_list']), $id_lang))->name.')' : ''
																	);
																} else {
																	$warehouses_names[] = ($show_pn ? '- '.$prod['name'] : '');
																}
															} else {
																/* despite of knowing that it can not be more than one warehouse, but do collect for security */
																foreach ($product_list as $prod) {
																	$id_warehouse_carrier = current($prod['warehouse_list']);
																	if (!empty($id_warehouse_carrier)) {
																		$warehouses_names[] = ($show_pn ? '- '.$prod['name'].' ' : '').(
																			$show_wn ? '('.(new StoreHouse($id_warehouse_carrier, $id_lang))->name.')' : ''
																		);
																	} else {
																		$warehouses_names[] = ($show_pn ? '- '.$prod['name'] : '');
																	}
																}
																if (!empty($warehouses_names) && count($warehouses_names) != count($product_list)) {
																	$before_name = $this->l('Some products are delivered from').' ';
																}
															}
															$warehouses_names = array_filter($warehouses_names);
														}
														$extraContent = '';
														if ($carrier['is_module']) {
															if ($moduleId = Module::getModuleIdByName($carrier['external_module_name'])) {
																$extraContent = Hook::exec('displayCarrierExtraContent', array('carrier' => $carrier), $moduleId);
															}
														}
														$carriers_table[] = array(
															'name' => $carrier['name'].' ('.(new PriceFormatter())->format($carrier['price_with_tax']).')',
															'delay' => $carrier['delay'][$id_lang],
															'logo' => $carrier['logo'],
															'warehouse_name' => !empty($warehouses_names) && count($warehouses_names) ? $before_name.implode('<br />', $warehouses_names) : '',
															'extraContent' => $extraContent,
														);
													}
												}
											}
											/* loop carriers */
											foreach ($carriers as $id_carrier => $carrier) {
												if ($id_carrier) {
													$carrier = array_merge($carrier, $object_presenter->present($carrier['instance']));
													$delay = $carrier['delay'][$id_lang];
													unset($carrier['instance'], $carrier['delay']);
													// delay
													$carrier['delay'] = $delay;
													// price
													if ($this->isFreeShipping($this->context->cart, $carriers_list)) {
														$carrier['price'] = $this->trans('Free', array(), 'Shop.Theme.Checkout');
													} else {
														if ($include_taxes) {
															$carrier['price'] = (new PriceFormatter())->format($carriers_list['total_price_with_tax']);
															if ($display_taxes_label) {
																$carrier['price'] = $this->trans(
																	'%price% tax incl.',
																	array('%price%' => $carrier['price']),
																	'Shop.Theme.Checkout'
																);
															}
														} else {
															$carrier['price'] = (new PriceFormatter())->format($carriers_list['total_price_without_tax']);
															if ($display_taxes_label) {
																$carrier['price'] = $this->trans(
																	'%price% tax excl.',
																	array('%price%' => $carrier['price']),
																	'Shop.Theme.Checkout'
																);
															}
														}
													}
													// label
													if (count($carriers) > 1) {
														$carrier['label'] = $carrier['price'];
													} else {
														$carrier['label'] = $carrier['name'].' - '.$carrier['delay'].' - '.$carrier['price'];
													}
													// If carrier related to a module, check for additionnal data to display
													$carrier['extraContent'] = '';
													if (!$package_multi_carriers) {
														if ($carrier['is_module']) {
															if ($moduleId = Module::getModuleIdByName($carrier['external_module_name'])) {
																$carrier['extraContent'] = Hook::exec('displayCarrierExtraContent', array('carrier' => $carrier), $moduleId);
															}
														}
													}
													// Which one has to be selected by default
													$carrier['selected'] = 0;
													if ($selected_carrier == $id_carriers_list) {
														$carrier['selected'] = 1;
														array_push($methods_shipping_collection, $carrier);
													}
													if ($package_multi_carriers) {
														if (isset($carriers_table) && count($carriers_table)) {
															$carrier['carriers_table'] = $carriers_table;
														}
													}
													// IF products being delivered to the same address but from different carriers
													$carriers_available[$id_carriers_list] = $carrier;
												}
											}
										}
									}
								}
								$delivery_options_available[$id_address_delivery] = $carriers_available;
							}
						}
					}
				}
                // IF "Enable final summary" is enabled from "Order Settings" preferences page
                if (Configuration::get('PS_FINAL_SUMMARY_ENABLED') && count($selected_delivery_option) >= 1 &&
					isset($methods_shipping_collection) && count($methods_shipping_collection)) {
                    Media::addJsDef(array(
                        'methods_shipping_collection' => $methods_shipping_collection,
                    ));
                }

                $link = new \Link();
                // For delivery addresses checkout tab
                Media::addJsDefL('txt_delivery_addresses', $this->l('Delivery addresses'));
                Media::addJsDefL('txt_choose_addresses', $this->l('Ship to multiple addresses'));
                Media::addJsDefL('txt_warehouse', $this->l('Warehouse'));
                Media::addJsDefL(
					'txt_incomplete_addresses',
					$this->l('Delivery addresses selections are required! May be you need to create new delivery address.')
				);
                Media::addJsDefL('txt_incomplete_carriers', $this->l('Carriers selections are required!'));
                Media::addJsDefL('txt_no_carrier', $this->l('No carriers are available for the selected address!'));
                // For shipping method checkout tab
                Media::addJsDefL('txt_choose_shipping_adress', $this->l('Choose the shipping option for this address:'));
                Media::addJsDefL('txt_choose_shipping', $this->l('Choose the shipping option'));
                Media::addJsDefL('txt_countries_zone', $this->l('Delivery Countries'));
                Media::addJsDefL('txt_country_zone', $this->l('Delivery Country'));
                Media::addJsDefL('txt_delivery_where', $this->l('This product can be delivered to:'));
                Media::addJsDefL('txt_products_not_asm', $this->l('Web products'));
                // Common
                Media::addJsDefL('txt_ok', $this->l('Ok'));

                Media::addJsDef(array(
                    // For delivery addresses checkout tab
                    'mode_multi_carriers_choice' => Configuration::get('WKWAREHOUSE_MODE_MULTICARRIER_CHOICE'),
                    'cart_wkwarehouses_url' => $link->getModuleLink($this->name, 'processactions'),
                    // For shipping method checkout tab
                    'delivery_option' => current($selected_delivery_option),
					'delivery_option_list' => $delivery_options_available,
					'address_collection' => $this->context->cart->getAddressCollection(),
                ));
				/*if (Configuration::get('WKWAREHOUSE_ALLOW_MULTI_ADDRESSES')) {
					Media::addJsDef(array(
						'cart_collection' => $cart_collection,
						'delivery_cart_id' => $this->context->cart->id_address_delivery,
					));
				}*/
            }
        }
    }

    private function isFreeShipping($cart, array $carrier)
    {
        $free_shipping = false;

        if ($carrier['is_free']) {
            $free_shipping = true;
        } else {
            foreach ($cart->getCartRules() as $rule) {
                if ($rule['free_shipping'] && !$rule['carrier_restriction']) {
                    $free_shipping = true;
                    break;
                }
            }
        }
        return $free_shipping;
    }
}
