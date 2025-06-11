<?php

class Product extends ProductCore {

    public $not_available_message;

    public function __construct($id_product = null, $full = false, $id_lang = null, $id_shop = null, \Context $context = null)
    {
        self::$definition['fields']['not_available_message'] = ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => '255'];
        parent::__construct($id_product, $full, $id_lang, $id_shop, $context);
    }
	
	public static function getCoeur($id_product = 0) {
		$sql = 'SELECT coeur FROM awpf WHERE id_product = '.pSQL($id_product);
		$res = Db::getInstance()->executeS($sql);
		if(empty($res)){
			return false;
		}
		return $res[0]['coeur'];
	}
	
	public static function getTotalQuantity($id_product = 0) {
		$sql = 'SELECT SUM(s.quantity) AS stock_total
				FROM ps_stock_available s
				WHERE s.id_product = '.pSQL($id_product);

		$res = Db::getInstance()->executeS($sql);
		if(empty($res)){
			return false;
		}
		return $res[0]['stock_total'];
	}
	
	public static function getBotanicName($id_product = 0) {
		$sql = 'SELECT botanic_name FROM '._DB_PREFIX_.'product WHERE id_product = '.pSQL($id_product);
		$res = Db::getInstance()->executeS($sql);
		if(empty($res)){
			return false;
		}
		return $res[0]['botanic_name'];
	}
	
	public static function getJardinTitre($id_product = 0) {
		$sql = 'SELECT jardin_titre FROM awpf WHERE id_product = '.pSQL($id_product);
		$res = Db::getInstance()->executeS($sql);
		if(empty($res)){
			return false;
		}
		return $res[0]['jardin_titre'];
	}

    public static function getTypeEncart($id_product = 0) {
		$sql = 'SELECT type FROM awpf WHERE id_product = '.pSQL($id_product);
		$res = Db::getInstance()->executeS($sql);
		if(empty($res)){
			return false;
		}
		return $res[0]['type'];
	}
	
	public static function getJardinContenu($id_product = 0) {
		$sql = 'SELECT jardin_contenu FROM awpf WHERE id_product = '.pSQL($id_product);
		$res = Db::getInstance()->executeS($sql);
		if(empty($res)){
			return false;
		}
		return $res[0]['jardin_contenu'];
	}
	
	public static function getDefaultCombination($id_product = 0) {
		$id_product = intval($id_product);
		$others_combination_name = '';
        $id_attribute = 0;

		if ($id_product) {
			$db = Db::getInstance();
			$id_attribute = Product::getDefaultAttribute($id_product,1);

			$query = "SELECT al.name 
					  FROM "._DB_PREFIX_."attribute_lang al
					  INNER JOIN (
						  SELECT pac.id_attribute
						  FROM "._DB_PREFIX_."product_attribute_combination pac
						  INNER JOIN "._DB_PREFIX_."attribute a ON a.id_attribute = pac.id_attribute
						  INNER JOIN "._DB_PREFIX_."product_attribute pa ON pa.id_product_attribute = pac.id_product_attribute
						  WHERE pac.id_product_attribute = '".$id_attribute."'
						  AND pa.id_product = '".$id_product."'
						  AND (a.id_attribute_group=8 OR a.id_attribute_group=6)
						  ORDER BY a.id_attribute_group ASC, a.position
					  ) AS subquery ON al.id_attribute = subquery.id_attribute
					  WHERE al.id_lang = '1'";

			$result = $db->ExecuteS($query);
			$result = array_column($result, 'name');
			$result = array_unique($result);

			if(!empty($result)){
				$others_combination_name = $result[0];
			}
		}
		
		return [
            "id_product" => $id_product,
            "id_product_attribute" => $id_attribute,
            "name" => $others_combination_name
        ];
	}

	
	public static function getOthersCombination($id_product = 0) {
		$id_product = intval($id_product);
		$others_combination_name = '';

		if ($id_product) {
			$db = Db::getInstance();
			$id_attribute = Product::getDefaultAttribute($id_product,1);
			
			$query = "SELECT al.name 
					  FROM "._DB_PREFIX_."attribute_lang al
					  INNER JOIN (
						  SELECT pac.id_attribute, a.id_attribute_group, a.position
						  FROM "._DB_PREFIX_."product_attribute_combination pac
						  INNER JOIN "._DB_PREFIX_."attribute a ON a.id_attribute = pac.id_attribute
						  INNER JOIN "._DB_PREFIX_."product_attribute pa ON pa.id_product_attribute = pac.id_product_attribute
						  WHERE pac.id_product_attribute != '".$id_attribute."'
						  AND pa.id_product = '".$id_product."'
						  AND (a.id_attribute_group=8 OR a.id_attribute_group=6)
					  ) AS subquery ON al.id_attribute = subquery.id_attribute
					  WHERE al.id_lang = '1'
					  ORDER BY subquery.id_attribute_group, subquery.position";

			$result = $db->ExecuteS($query);
			$result = array_column($result, 'name');
			$result = array_unique(array_map('trim',$result));
			if(count($result)){
				$others_combination_name = 'Existe aussi en : '. implode(', ', $result);
			}
		}
		
		return $others_combination_name;
	}

	public static function getFichesAttachments($id_product = 0) {
		$id_product = intval($id_product);
		$id_lang = 1;
		
		$attachments = Db::getInstance()->executeS('
			SELECT *
			FROM '._DB_PREFIX_.'attachment a
			LEFT JOIN '._DB_PREFIX_.'attachment_lang al
				ON (a.id_attachment = al.id_attachment AND al.id_lang = '.$id_lang.')
			WHERE a.id_attachment IN (
				SELECT pa.id_attachment
				FROM '._DB_PREFIX_.'product_attachment pa
				WHERE id_product = '.$id_product.'
			)'
		);	
		
		return $attachments;
	}
	
	public static function countInCategory($id_category = 0) {
		$id_category = intval($id_category);
		$id_lang = 1;
		
		// p.visibility = both, catalog, search, or none
		
		$res = Db::getInstance()->executeS('
			SELECT COUNT(cp.id_product) as nb
			FROM '._DB_PREFIX_.'category_product cp
			LEFT JOIN '._DB_PREFIX_.'product p
				ON (p.id_product = cp.id_product)
			WHERE p.active = 1
			AND p.visibility <> "none"
			AND cp.id_category = '.$id_category
		);	
		
		$number = 0;
		if(is_array($res) && count($res)){
			$number = $res[0]['nb'];
		}
		
		return $number;
	}
		
	public static function getPlantationRecolte($id_product = 0) {
		
		$id_product = intval($id_product);
		
		$features = Product::getFeaturesStatic($id_product);
		
		$arr = [];
		$arr['plantation'] = [];
		$arr['recolte'] = [];
		
		foreach($features as $feature){
			$id_feature = $feature['id_feature'];
			$id_feature_value = $feature['id_feature_value'];
            //error_log(print_r($feature, true));
            if ( isset($feature['custom']) && $feature['custom'] == 1 )
            {
                if($id_feature == "27"){
                    foreach(explode(',', $feature['value']) as $monthEC)
                    {
                        $arr['plantation'][$monthEC] = 1;
                    }
                    /*$id_month = $id_feature_value - 2424;
                    if($id_month > 0 && $id_month < 13){
                        $arr['plantation'][$id_month] = 1;
                    }*/
                }
                else if($id_feature == "28"){
                    foreach(explode(',', $feature['value']) as $monthEC)
                    {
                        $arr['recolte'][$monthEC] = 1;
                    }
                    /*$id_month = $id_feature_value - 2412;
                    if($id_month > 0 && $id_month < 13){
                        $arr['recolte'][$id_month] = 1;
                    }*/
                }
            }
            else
            {
                if($id_feature == "27"){
                    $id_month = $id_feature_value - 2424;
                    if($id_month > 0 && $id_month < 13){
                        $arr['plantation'][$id_month] = 1;
                    }
                }
                else if($id_feature == "28"){
                    $id_month = $id_feature_value - 2412;
                    if($id_month > 0 && $id_month < 13){
                        $arr['recolte'][$id_month] = 1;
                    }
                }
            }
		}
		
		if(empty($arr['plantation']) && empty($arr['plantation'])){
			return false;
		}
		
		return $arr;
	}
	
    public static function getFeaturesStatic($id_product)
    {
        if (!Feature::isFeatureActive()) {
            return [];
        }
        if (!array_key_exists($id_product, self::$_cacheFeatures)) {
            self::$_cacheFeatures[$id_product] = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
                '
                SELECT fp.id_feature, fp.id_product, fp.id_feature_value, custom, fvl.value
                FROM `' . _DB_PREFIX_ . 'feature_product` fp
                LEFT JOIN `' . _DB_PREFIX_ . 'feature_value` fv ON (fp.id_feature_value = fv.id_feature_value)
                LEFT JOIN `' . _DB_PREFIX_ . 'feature_value_lang` fvl ON (fp.id_feature_value = fvl.id_feature_value)
                WHERE `id_product` = ' . (int) $id_product .' AND id_lang = 1'
            );
        }

        return self::$_cacheFeatures[$id_product];
    }

	/**
     * Price calculation / Get product price.
     *
     * @param int $id_shop Shop id
     * @param int $id_product Product id
     * @param int $id_product_attribute Product attribute id
     * @param int $id_country Country id
     * @param int $id_state State id
     * @param string $zipcode
     * @param int $id_currency Currency id
     * @param int $id_group Group id
     * @param int $quantity Quantity Required for Specific prices : quantity discount application
     * @param bool $use_tax with (1) or without (0) tax
     * @param int $decimals Number of decimals returned
     * @param bool $only_reduc Returns only the reduction amount
     * @param bool $use_reduc Set if the returned amount will include reduction
     * @param bool $with_ecotax insert ecotax in price output
     * @param null $specific_price If a specific price applies regarding the previous parameters,
     *                             this variable is filled with the corresponding SpecificPrice object
     * @param bool $use_group_reduction
     * @param int $id_customer
     * @param bool $use_customer_price
     * @param int $id_cart
     * @param int $real_quantity
     *
     * @return float Product price
     **/
    public static function priceCalculation(
        $id_shop,
        $id_product,
        $id_product_attribute,
        $id_country,
        $id_state,
        $zipcode,
        $id_currency,
        $id_group,
        $quantity,
        $use_tax,
        $decimals,
        $only_reduc,
        $use_reduc,
        $with_ecotax,
        &$specific_price,
        $use_group_reduction,
        $id_customer = 0,
        $use_customer_price = true,
        $id_cart = 0,
        $real_quantity = 0,
        $id_customization = 0
    ) {
        static $address = null;
        static $context = null;
        if ($context == null) {
            $context = Context::getContext()->cloneContext();
        }
        if ($address === null) {
            if (is_object($context->cart) && $context->cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')} != null) {
                $id_address = $context->cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')};
                $address = new Address($id_address);
            } else {
                $address = new Address();
            }
        }
        if ($id_shop !== null && $context->shop->id != (int) $id_shop) {
            $context->shop = new Shop((int) $id_shop);
        }
        if (!$use_customer_price) {
            $id_customer = 0;
        }
        if ($id_product_attribute === null) {
            $id_product_attribute = Product::getDefaultAttribute($id_product);
        }
        $cache_id = (int) $id_product . '-' . (int) $id_shop . '-' . (int) $id_currency . '-' .
        (int) $id_country . '-' . $id_state . '-' . $zipcode . '-' . (int) $id_group .
            '-' . (int) $quantity . '-' . (int) $id_product_attribute . '-' . (int) $id_customization .
            '-' . (int) $with_ecotax . '-' . (int) $id_customer . '-' . (int) $use_group_reduction . '-' .
            (int) $id_cart . '-' . (int) $real_quantity .
            '-' . ($only_reduc ? '1' : '0') . '-' . ($use_reduc ? '1' : '0') . '-' . ($use_tax ? '1' : '0') . '-' .
             (int) $decimals;
        $specific_price = SpecificPrice::getSpecificPrice(
            (int) $id_product,
            $id_shop,
            $id_currency,
            $id_country,
            $id_group,
            $quantity,
            $id_product_attribute,
            $id_customer,
            $id_cart,
            $real_quantity
        );
        if (isset(self::$_prices[$cache_id])) {
            return self::$_prices[$cache_id];
        }
        $cache_id_2 = $id_product . '-' . $id_shop;
        if (!isset(self::$_pricesLevel2[$cache_id_2])) {
            $sql = new DbQuery();
            $sql->select('product_shop.`price`, product_shop.`ecotax`');
            $sql->from('product', 'p');
            $sql->innerJoin('product_shop', 'product_shop', '(product_shop.id_product=p.id_product
            AND product_shop.id_shop = ' . (int) $id_shop . ')');
            $sql->where('p.`id_product` = ' . (int) $id_product);
            if (Combination::isFeatureActive()) {
                $sql->select('IFNULL(product_attribute_shop.id_product_attribute,0) id_product_attribute,
                product_attribute_shop.`price` AS attribute_price, product_attribute_shop.default_on');
                $sql->leftJoin(
                    'product_attribute_shop',
                    'product_attribute_shop',
                    '(product_attribute_shop.id_product = p.id_product AND product_attribute_shop.id_shop = ' .
                    (int) $id_shop . ')'
                );
            } else {
                $sql->select('0 as id_product_attribute');
            }
            $res = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
            if (is_array($res) && count($res)) {
                foreach ($res as $row) {
                    $array_tmp = [
                        'price' => $row['price'],
                        'ecotax' => $row['ecotax'],
                        'attribute_price' => (isset($row['attribute_price']) ? $row['attribute_price'] : null),
                    ];
                    self::$_pricesLevel2[$cache_id_2][(int) $row['id_product_attribute']] = $array_tmp;
                    if (isset($row['default_on']) && $row['default_on'] == 1) {
                        self::$_pricesLevel2[$cache_id_2][0] = $array_tmp;
                    }
                }
            }
        }
        if (!isset(self::$_pricesLevel2[$cache_id_2][(int) $id_product_attribute])) {
            return;
        }
        $result = self::$_pricesLevel2[$cache_id_2][(int) $id_product_attribute];
        if (!$specific_price || $specific_price['price'] < 0) {
            $price = (float) $result['price'];
            /*
             * Start overriding
             */
            if (Module::isInstalled('wkbundleproduct') && Module::isEnabled('wkbundleproduct')) {
                include_once _PS_MODULE_DIR_ . 'wkbundleproduct/classes/WkBundleProductRequiredClasses.php';
                $objBpHelper = new WkBundleProductHelper();
                if (isset(Context::getContext()->cookie->wk_id_customization)) {
                    $id_customization = Context::getContext()->cookie->wk_id_customization;
                }
                $price = $objBpHelper->bundleProductPriceCalculation(
                    $id_product,
                    $price,
                    $use_tax,
                    $id_customization,
                    $id_cart
                );
                $context->cookie->__unset('wk_id_customization');
            }
        } else {
            $price = (float) $specific_price['price'];
        }
        if (!$specific_price || !($specific_price['price'] >= 0 && $specific_price['id_currency'])) {
            $price = Tools::convertPrice($price, $id_currency);
            if (isset($specific_price['price']) && $specific_price['price'] >= 0) {
                $specific_price['price'] = $price;
            }
        }
        if (is_array($result) && (!$specific_price || !$specific_price['id_product_attribute'] ||
        $specific_price['price'] < 0)) {
            $attribute_price = Tools::convertPrice(
                $result['attribute_price'] !== null ? (float) $result['attribute_price'] : 0,
                $id_currency
            );
            if ($id_product_attribute !== false) {
                $price += $attribute_price;
            }
        }
        if ((int) $id_customization) {
            $price += Tools::convertPrice(Customization::getCustomizationPrice($id_customization), $id_currency);
        }
        $address->id_country = $id_country;
        $address->id_state = $id_state;
        $address->postcode = $zipcode;
        $tax_manager = TaxManagerFactory::getManager(
            $address,
            Product::getIdTaxRulesGroupByIdProduct((int) $id_product, $context)
        );
        $product_tax_calculator = $tax_manager->getTaxCalculator();
        if ($use_tax) {
            $price = $product_tax_calculator->addTaxes($price);
        }
        if (($result['ecotax'] || isset($result['attribute_ecotax'])) && $with_ecotax) {
            $ecotax = $result['ecotax'];
            if (isset($result['attribute_ecotax']) && $result['attribute_ecotax'] > 0) {
                $ecotax = $result['attribute_ecotax'];
            }
            if ($id_currency) {
                $ecotax = Tools::convertPrice($ecotax, $id_currency);
            }
            if ($use_tax) {
                static $psEcotaxTaxRulesGroupId = null;
                if ($psEcotaxTaxRulesGroupId === null) {
                    $psEcotaxTaxRulesGroupId = (int) Configuration::get('PS_ECOTAX_TAX_RULES_GROUP_ID');
                }
                $tax_manager = TaxManagerFactory::getManager(
                    $address,
                    $psEcotaxTaxRulesGroupId
                );
                $ecotax_tax_calculator = $tax_manager->getTaxCalculator();
                $price += $ecotax_tax_calculator->addTaxes($ecotax);
            } else {
                $price += $ecotax;
            }
        }
        $specific_price_reduction = 0;
        if (($only_reduc || $use_reduc) && $specific_price) {
            if ($specific_price['reduction_type'] == 'amount') {
                $reduction_amount = $specific_price['reduction'];
                if (!$specific_price['id_currency']) {
                    $reduction_amount = Tools::convertPrice($reduction_amount, $id_currency);
                }
                $specific_price_reduction = $reduction_amount;
                if (!$use_tax && $specific_price['reduction_tax']) {
                    $specific_price_reduction = $product_tax_calculator->removeTaxes($specific_price_reduction);
                }
                if ($use_tax && !$specific_price['reduction_tax']) {
                    $specific_price_reduction = $product_tax_calculator->addTaxes($specific_price_reduction);
                }
            } else {
                $specific_price_reduction = $price * $specific_price['reduction'];
            }
        }
        if ($use_reduc) {
            $price -= $specific_price_reduction;
        }
        $isBundleProduct = false;
        if (Module::isInstalled('wkbundleproduct') && Module::isEnabled('wkbundleproduct')) {
            include_once _PS_MODULE_DIR_ . 'wkbundleproduct/classes/WkBundleProductRequiredClasses.php';
            $objBundle = new WkBundle();
            if ($objBundle->isBundleProduct($id_product)) {
                $isBundleProduct = true;
            }
        }
        if ($isBundleProduct) {
            if (!Configuration::get('WK_BUNDLE_PRODUCT_DISABLE_GROUP_DISCOUNT')) {
                if ($use_group_reduction) {
                    $reduction_from_category = GroupReduction::getValueForProduct($id_product, $id_group);
                    if ($reduction_from_category !== false) {
                        $group_reduction = $price * (float) $reduction_from_category;
                    } else { // apply group reduction if there is no group reduction for this category
                        $group_reduction = ((
                            $reduc = Group::getReductionByIdGroup($id_group)
                        ) != 0) ? ($price * $reduc / 100) : 0;
                    }
                    $price -= $group_reduction;
                }
            }
        } else {
            if ($use_group_reduction) {
                $reduction_from_category = GroupReduction::getValueForProduct($id_product, $id_group);
                if ($reduction_from_category !== false) {
                    $group_reduction = $price * (float) $reduction_from_category;
                } else { // apply group reduction if there is no group reduction for this category
                    $group_reduction = ((
                        $reduc = Group::getReductionByIdGroup($id_group)
                    ) != 0) ? ($price * $reduc / 100) : 0;
                }
                $price -= $group_reduction;
            }
        }
        if ($only_reduc) {
            return Tools::ps_round($specific_price_reduction, $decimals);
        }
        $price = Tools::ps_round($price, $decimals);
        if ($price < 0) {
            $price = 0;
        }
        self::$_prices[$cache_id] = $price;

        return self::$_prices[$cache_id];
    }

    public static function getProductProperties($id_lang, $row, Context $context = null)
    {
        $row = parent::getProductProperties($id_lang, $row, $context);
        /*if (Module::isInstalled('wkbundleproduct') && Module::isEnabled('wkbundleproduct')) {
            include_once _PS_MODULE_DIR_ . 'wkbundleproduct/classes/WkBundleProductRequiredClasses.php';
            $objSubProduct = new WkBundleSubProduct();
            $forBundleOnly = $objSubProduct->getAllAvailableProduct();
            if ($forBundleOnly) {
                if (in_array($row['id_product'], $forBundleOnly)) {
                    $row['available_for_order'] = 0;
                }
            }
            $objBundle = new WkBundle();
            if ($objBundle->isBundleProduct($row['id_product'])) {
                $objBpHelper = new WkBundleProductHelper();
                $price = 0;
                $price = $objBpHelper->bundleProductPriceCalculation($row['id_product'], $price);
                if ($price <= 0) {
                    $row['available_for_order'] = 0;
                    $row['show_price'] = 0;
                }
            }
            if (Configuration::get('WK_BUNDLE_PRODUCT_RESERVED_QTY') && Configuration::get('PS_STOCK_MANAGEMENT')) {
                if ($objSubProduct->getAllAvailableProduct(0)) {
                    if (in_array($row['id_product'], $objSubProduct->getAllAvailableProduct(0))) {
                        $qty = $objSubProduct->getProductMaximumQuantity(
                            $row['id_product'],
                            $row['id_product_attribute']
                        );
                        if ($qty) {
                            $row['quantity'] = $qty;
                        } else {
                            $row['quantity'] = 0;
                        }
                    }
                }
            }
        }*/

        return $row;
    }
	
	/**
     * Function to check product is bundle or not
     *
     * @param int $idProduct
     *
     * @return array
     */
    public static function isBundleProduct($idProduct)
    {
        $sql = 'SELECT bp.*
        FROM `' . _DB_PREFIX_ . 'wk_bundle_product` bp
        ' . Shop::addSqlAssociation('wk_bundle_product', 'bp') . '
        WHERE bp.`id_ps_product` = ' . (int) $idProduct;

        return Db::getInstance()->getRow($sql);
    }
	
	public static function awGetBundleDetail($idProduct)
    {
		$bundleDetail = [];

        $sql = 'SELECT bspa.id_product, bspa.id_product_attribute
				FROM ' . _DB_PREFIX_ . 'wk_bundle_sub_product_attribute bspa
				LEFT JOIN ps_wk_bundle_section_map bsm
					ON bsm.id_wk_bundle_section = bspa.id_wk_bundle_section
				LEFT JOIN ' . _DB_PREFIX_ . 'wk_bundle_product bp
					ON bp.id_wk_bundle_product = bsm.id_wk_bundle_product
				WHERE bp.id_ps_product = ' . (int) $idProduct . '
				AND bspa.default_attr = 1';

		$res1 = Db::getInstance()->executeS($sql);

		$sql = 'SELECT discount
				FROM ' . _DB_PREFIX_ . 'wk_bundle_product
				WHERE id_ps_product = ' . (int) $idProduct;

		$res2 = Db::getInstance()->executeS($sql);

		$bundleDetail['price'] = 0;
		foreach($res1 as $row){
			$id_product = (int) $row['id_product'];
			$idProductAttribute = (int) $row['id_product_attribute'];

			if($id_product){
				$bundleDetail['price'] += Product::getPriceStatic($id_product, true, $idProductAttribute);
			}
		}
		$bundleDetail['displayPrice'] = Tools::displayPrice($bundleDetail['price']);

		$bundleDetail['discount'] = 0;
		$bundleDetail['displayDiscount'] = '';
		foreach($res2 as $row){
			$bundleDetail['discount'] = $row['discount'];
			$bundleDetail['displayDiscount'] = '-'.(int)$row['discount'].'%';
			break;
		}

		return $bundleDetail;
    }
	
	public static function awIsBundleStock($idProduct)
    {
		$sql = 'SELECT bspa.id_product, bspa.id_product_attribute
				FROM ' . _DB_PREFIX_ . 'wk_bundle_sub_product_attribute bspa
				LEFT JOIN ps_wk_bundle_section_map bsm
					ON bsm.id_wk_bundle_section = bspa.id_wk_bundle_section
				LEFT JOIN ' . _DB_PREFIX_ . 'wk_bundle_product bp 
					ON bp.id_wk_bundle_product = bsm.id_wk_bundle_product
				WHERE bp.id_ps_product = ' . (int) $idProduct;

		$res1 = Db::getInstance()->executeS($sql);
		
		$sql = 'SELECT bsp.id_product, 0 as id_product_attribute
				FROM ' . _DB_PREFIX_ . 'wk_bundle_sub_product bsp
				LEFT JOIN ps_wk_bundle_section_map bsm
					ON bsm.id_wk_bundle_section = bsp.id_wk_bundle_section
				LEFT JOIN ' . _DB_PREFIX_ . 'wk_bundle_product bp 
					ON bp.id_wk_bundle_product = bsm.id_wk_bundle_product
				WHERE bp.id_ps_product = ' . (int) $idProduct;

		$res2 = Db::getInstance()->executeS($sql);
		
		$res3 = array_merge($res1,$res2);
		
		foreach($res3 as $row){
			$id_product = $row['id_product'];
			$idProductAttribute = $row['id_product_attribute'];
			
			if($idProductAttribute){
				$inStock = StockAvailable::getQuantityAvailableByProduct(null, $idProductAttribute);
				if(!$inStock){
					return false;
				}
			}elseif($id_product){
				$inStock = StockAvailable::getQuantityAvailableByProduct($id_product, null);
				if(!$inStock){
					return false;
				}
			}
		}
		
        return true;
    }
	
	/**
     * Get new products.
     *
     * @param int $id_lang Language identifier
     * @param int $page_number Start from
     * @param int $nb_products Number of products to return
     * @param bool $count
     * @param string|null $order_by
     * @param string|null $order_way
     * @param Context|null $context
     *
     * @return array|int|false New products, total of product if $count is true, false if it fail
     */
    public static function getNewProducts($id_lang, $page_number = 0, $nb_products = 10, $count = false, $order_by = null, $order_way = null, Context $context = null)
    {
        $now = date('Y-m-d') . ' 00:00:00';
        if (!$context) {
            $context = Context::getContext();
        }

        $front = true;
        if (!in_array($context->controller->controller_type, ['front', 'modulefront'])) {
            $front = false;
        }

        if ($page_number < 1) {
            $page_number = 1;
        }
        if ($nb_products < 1) {
            $nb_products = 10;
        }
        if (empty($order_by) || $order_by == 'position') {
            $order_by = 'date_add';
        }
        if (empty($order_way)) {
            $order_way = 'DESC';
        }
        if ($order_by == 'id_product' || $order_by == 'price' || $order_by == 'date_add' || $order_by == 'date_upd') {
            $order_by_prefix = 'product_shop';
        } elseif ($order_by == 'name') {
            $order_by_prefix = 'pl';
        }
        if (!Validate::isOrderBy($order_by) || !Validate::isOrderWay($order_way)) {
            die(Tools::displayError());
        }

        $sql_groups = '';
        if (Group::isFeatureActive()) {
            $groups = FrontController::getCurrentCustomerGroups();
            $sql_groups = ' AND EXISTS(SELECT 1 FROM `' . _DB_PREFIX_ . 'category_product` cp
            JOIN `' . _DB_PREFIX_ . 'category_group` cg ON (cp.id_category = cg.id_category AND cg.`id_group` ' . (count($groups) ? 'IN (' . implode(',', $groups) . ')' : '=' . (int) Group::getCurrent()->id) . ')
            WHERE cp.`id_product` = p.`id_product`)';
        }

        if (strpos($order_by, '.') > 0) {
            $order_by = explode('.', $order_by);
            $order_by_prefix = $order_by[0];
            $order_by = $order_by[1];
        }

        $nb_days_new_product = (int) Configuration::get('PS_NB_DAYS_NEW_PRODUCT');

        if ($count) {
            $sql = 'SELECT COUNT(p.`id_product`) AS nb
                    FROM `' . _DB_PREFIX_ . 'product` p
                    ' . Shop::addSqlAssociation('product', 'p') . '
                    WHERE product_shop.`active` = 1
                    AND DATEDIFF(product_shop.`date_add`, DATE_SUB("' . $now . '", INTERVAL ' . $nb_days_new_product . ' DAY)) > 0
                    ' . ($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '') . '
                    ' . $sql_groups;

            return (int) Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
        }
        $sql = new DbQuery();
        $sql->select(
            'p.*, product_shop.*, stock.out_of_stock, IFNULL(stock.quantity, 0) as quantity, pl.`description`, pl.`description_short`, pl.`link_rewrite`, pl.`meta_description`,
            pl.`meta_keywords`, pl.`meta_title`, pl.`name`, pl.`available_now`, pl.`available_later`, image_shop.`id_image` id_image, il.`legend`, m.`name` AS manufacturer_name,
            (DATEDIFF(product_shop.`date_add`,
                DATE_SUB(
                    "' . $now . '",
                    INTERVAL ' . $nb_days_new_product . ' DAY
                )
            ) > 0) as new'
        );

        $sql->from('product', 'p');
        $sql->join(Shop::addSqlAssociation('product', 'p'));
        $sql->leftJoin(
            'product_lang',
            'pl',
            '
            p.`id_product` = pl.`id_product`
            AND pl.`id_lang` = ' . (int) $id_lang . Shop::addSqlRestrictionOnLang('pl')
        );
        $sql->leftJoin('image_shop', 'image_shop', 'image_shop.`id_product` = p.`id_product` AND image_shop.cover=1 AND image_shop.id_shop=' . (int) $context->shop->id);
        $sql->leftJoin('image_lang', 'il', 'image_shop.`id_image` = il.`id_image` AND il.`id_lang` = ' . (int) $id_lang);
        $sql->leftJoin('manufacturer', 'm', 'm.`id_manufacturer` = p.`id_manufacturer`');
        //$sql->leftJoin('stock_available', 's', 's.`id_product` = p.`id_product`'); // new

        $sql->where('product_shop.`active` = 1');
		$sql->where('stock.`quantity` > 0'); // new
        if ($front) {
            $sql->where('product_shop.`visibility` IN ("both", "catalog")');
        }
        $sql->where('DATEDIFF(product_shop.`date_add`,
            DATE_SUB(
                "' . $now . '",
                INTERVAL ' . $nb_days_new_product . ' DAY
            )
        ) > 0');
        if (Group::isFeatureActive()) {
            $groups = FrontController::getCurrentCustomerGroups();
            $sql->where('EXISTS(SELECT 1 FROM `' . _DB_PREFIX_ . 'category_product` cp
            JOIN `' . _DB_PREFIX_ . 'category_group` cg ON (cp.id_category = cg.id_category AND cg.`id_group` ' . (count($groups) ? 'IN (' . implode(',', $groups) . ')' : '=' . (int) Group::getCurrent()->id) . ')
            WHERE cp.`id_product` = p.`id_product`)');
        }

        if ($order_by !== 'price') {
            $sql->orderBy((isset($order_by_prefix) ? pSQL($order_by_prefix) . '.' : '') . '`' . pSQL($order_by) . '` ' . pSQL($order_way));
            $sql->limit($nb_products, (int) (($page_number - 1) * $nb_products));
        }

        if (Combination::isFeatureActive()) {
            $sql->select('product_attribute_shop.minimal_quantity AS product_attribute_minimal_quantity, IFNULL(product_attribute_shop.id_product_attribute,0) id_product_attribute');
            $sql->leftJoin('product_attribute_shop', 'product_attribute_shop', 'p.`id_product` = product_attribute_shop.`id_product` AND product_attribute_shop.`default_on` = 1 AND product_attribute_shop.id_shop=' . (int) $context->shop->id);
        }
        $sql->join(Product::sqlStock('p', 0));

        //error_log($sql->build());

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

        if (!$result) {
            return false;
        }

        if ($order_by === 'price') {
            Tools::orderbyPrice($result, $order_way);
            $result = array_slice($result, (int) (($page_number - 1) * $nb_products), (int) $nb_products);
        }
        $products_ids = [];
        foreach ($result as $row) {
            $products_ids[] = $row['id_product'];
        }
        // Thus you can avoid one query per product, because there will be only one query for all the products of the cart
        Product::cacheFrontFeatures($products_ids, $id_lang);

        return Product::getProductsProperties((int) $id_lang, $result);
    }
	
	public static function getPriceMin($id_product = 0, $id_product_attribute = 0, $regular_price = 0) {
		
		// Recherche des prix dégréssifs
		
		$context = Context::getContext();
		
		$id_customer = (isset($context->customer) ? (int) $context->customer->id : 0);
        $id_group = (int) Group::getCurrent()->id;
        $id_country = $id_customer ? (int) Customer::getCurrentCountry($id_customer) : (int) Tools::getCountry();
        $id_currency = (int) $context->cookie->id_currency;
        $id_shop = $context->shop->id;
		
		// SpecificPrice::getQuantityDiscounts récupère les prix dégréssifs classiques
		
		$quantity_discounts = SpecificPrice::getQuantityDiscounts($id_product, $id_shop, $id_currency, $id_country, $id_group, $id_product_attribute, false, (int) $context->customer->id);

		//$cart_quantity = 99;
		//$unitDiscount = 0;
		//$unitDiscount = max(array(0, Product::getPriceStatic($id_product, true, (isset($id_product_attribute) ? (int)$id_product_attribute : null), 6, null, false, true, $cart_quantity, false, (int)$context->cart->id_customer, (int)$context->cart->id, null, $specific_price_output, true, true, null, true, 0) - (Product::getPriceStatic($id_product, true, (isset($id_product_attribute) ? (int)$id_product_attribute : null), 6, null, false, false, $cart_quantity, false, (int)$context->cart->id_customer, (int)$context->cart->id, null, $specific_price_output, true, true, null, true, 0) - $unitDiscount)));

		// récupère les prix dégréssifs du module Promotion
		/*$priceMinB = 999999;
		$actions = $quantityDiscountRule->getActions(true);
		foreach ($actions as $action) {
			$priceMinB_ = $regular_price*(1-$action->reduction_percent/100);
			$priceMinB = min($priceMinB, $priceMinB_);
		}*/
 
		$priceMin = $regular_price;
		foreach($quantity_discounts as $discount){
			$price = $regular_price*(1-$discount['reduction']);
			$priceMin = min($price, $priceMin);
		}
	
		if($regular_price == $priceMin){
			return 0;
		}
		
		return $priceMin;
	}
	
	public static function getCategoriesPlants() {
		$plant_categories = [21,62,69,71,77,195,45,46,357,346,344,320];
		return $plant_categories;
		
		/*21	Aubergines
		195	Concombres/cornichons
		45	Courges-potirons
		46	Courgettes
		62	Melons/Pastèques
		69	Pâtissons
		71	Piments/poivrons
		77	Tomates*/
	}
	
	public static function isPlantEnPrecommande($name = "", $categories = []) {
		$regex = "plant";  // si il y a le mot "plant" dans le nom de la déclinaison
		$plant_categories = Product::getCategoriesPlants();
		if(!is_array($categories)){
			$categories = [$categories];
		}

		return ((strpos(strtolower($name),$regex) !== false) && !empty(array_intersect($plant_categories, $categories)));
	}
	
	public static function isPlantEnPrecommandeByProductId($id_product = 0) {
		
		$id_product = (int) $id_product;
		$plant_categories = Product::getCategoriesPlants();
		$categories_list = implode(',', array_map('intval', $plant_categories));
		
		$sql = "SELECT p.*, pl.*
            FROM `" . _DB_PREFIX_ . "product` p
            LEFT JOIN `" . _DB_PREFIX_ . "product_lang` pl ON (p.`id_product` = pl.`id_product`)
            LEFT JOIN `" . _DB_PREFIX_ . "category_product` cp ON (p.`id_product` = cp.`id_product`)
			LEFT JOIN `" . _DB_PREFIX_ . "product_attribute` pa ON (p.`id_product` = pa.`id_product`) 
			LEFT JOIN `" . _DB_PREFIX_ . "product_attribute_combination` pac ON (pac.`id_product_attribute` = pa.`id_product_attribute`) 
			LEFT JOIN `" . _DB_PREFIX_ . "attribute_lang` al ON (pac.`id_attribute` = al.`id_attribute`) 
            WHERE cp.`id_category` IN ($categories_list)
			AND p.id_product = $id_product
			AND al.name LIKE 'plant%'";
		
		$res = Db::getInstance()->executeS($sql);
		if(empty($res)){
			return false;
		}

		return true;
	}
	
	public static function isPlantEnPrecommandeById($id_product_attribute = 0) {
		
		$id_product_attribute = (int) $id_product_attribute;
		$plant_categories = Product::getCategoriesPlants();
		$categories_list = implode(',', array_map('intval', $plant_categories));
		
		$sql = "SELECT p.*, pl.*
            FROM `" . _DB_PREFIX_ . "product` p
            LEFT JOIN `" . _DB_PREFIX_ . "product_lang` pl ON (p.`id_product` = pl.`id_product`)
            LEFT JOIN `" . _DB_PREFIX_ . "category_product` cp ON (p.`id_product` = cp.`id_product`)
			LEFT JOIN `" . _DB_PREFIX_ . "product_attribute` pa ON (p.`id_product` = pa.`id_product`) 
			LEFT JOIN `" . _DB_PREFIX_ . "product_attribute_combination` pac ON (pac.`id_product_attribute` = pa.`id_product_attribute`) 
			LEFT JOIN `" . _DB_PREFIX_ . "attribute_lang` al ON (pac.`id_attribute` = al.`id_attribute`) 
            WHERE cp.`id_category` IN ($categories_list)
			AND pa.id_product_attribute = $id_product_attribute
			AND al.name LIKE 'plant%'";
		
		$res = Db::getInstance()->executeS($sql);
		if(empty($res)){
			return false;
		}

		return true;
	}
	
	public static function getProduitsPlantEnPrecommande() {
		// Retourne la liste de tous les produits avec plants en précommande.
		
		$plant_categories = Product::getCategoriesPlants();
		
		$categories_list = implode(',', array_map('intval', $plant_categories));

		$sql = "SELECT p.*, pl.*
            FROM `" . _DB_PREFIX_ . "product` p
            LEFT JOIN `" . _DB_PREFIX_ . "product_lang` pl ON (p.`id_product` = pl.`id_product`)
            LEFT JOIN `" . _DB_PREFIX_ . "category_product` cp ON (p.`id_product` = cp.`id_product`)
			LEFT JOIN `" . _DB_PREFIX_ . "product_attribute` pa ON (p.`id_product` = pa.`id_product`) 
			LEFT JOIN `" . _DB_PREFIX_ . "product_attribute_combination` pac ON (pac.`id_product_attribute` = pa.`id_product_attribute`) 
			LEFT JOIN `" . _DB_PREFIX_ . "attribute_lang` al ON (pac.`id_attribute` = al.`id_attribute`) 
            WHERE cp.`id_category` IN ($categories_list)
			AND al.name LIKE 'plant%'
            GROUP BY p.`id_product`";
		
		$res = Db::getInstance()->executeS($sql);
		if(empty($res)){
			return false;
		}
		return $res;
	}
	
	/**
     * Duplicate features when duplicating a product.
     *
     * @param int $id_product_old Old Product identifier
     * @param int $id_product_new New Product identifier
     *
     * @return bool
     */
    public static function duplicateFeatures($id_product_old, $id_product_new)
    {
        $return = true;

        $result = Db::getInstance()->executeS('
        SELECT *
        FROM `' . _DB_PREFIX_ . 'feature_product`
        WHERE `id_product` = ' . (int) $id_product_old);
        foreach ($result as $row) {
            $result2 = Db::getInstance()->getRow('
            SELECT *
            FROM `' . _DB_PREFIX_ . 'feature_value`
            WHERE `id_feature_value` = ' . (int) $row['id_feature_value']);
            // Custom feature value, need to duplicate it
            if ($result2['custom']) {
                $old_id_feature_value = $result2['id_feature_value'];
                unset($result2['id_feature_value']);
                $return &= Db::getInstance()->insert('feature_value', $result2);
                $max_fv = Db::getInstance()->getRow('
                    SELECT MAX(`id_feature_value`) AS nb
                    FROM `' . _DB_PREFIX_ . 'feature_value`');
                $new_id_feature_value = $max_fv['nb'];

                foreach (Language::getIDs(false) as $id_lang) {
                    $result3 = Db::getInstance()->getRow('
                    SELECT *
                    FROM `' . _DB_PREFIX_ . 'feature_value_lang`
                    WHERE `id_feature_value` = ' . (int) $old_id_feature_value . '
                    AND `id_lang` = ' . (int) $id_lang);

                    if ($result3) {
                        $result3['id_feature_value'] = (int) $new_id_feature_value;
                        $result3['value'] = pSQL($result3['value']);
                        $return &= Db::getInstance()->insert('feature_value_lang', $result3);
                    }
                }
                $row['id_feature_value'] = $new_id_feature_value;
            }

            $row['id_product'] = (int) $id_product_new;
            $return &= Db::getInstance()->insert('feature_product', $row);
        }

		// botanic_name

		$sql = 'SELECT botanic_name FROM '._DB_PREFIX_.'product WHERE id_product = '.pSQL($id_product_old);
		$res = Db::getInstance()->executeS($sql);
		if(empty($res)){
			return false;
		}
		$botanic_name = $res[0]['botanic_name'];
		
		$sql = 'UPDATE '._DB_PREFIX_.'product SET botanic_name = "'.pSQL($botanic_name).'" WHERE id_product = '.pSQL($id_product_new);
		$res = Db::getInstance()->execute($sql);
		
        return $return;
    }
}
