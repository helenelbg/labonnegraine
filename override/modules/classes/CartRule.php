<?php
class CartRule extends CartRuleCore
{
    public function checkValidity(Context $context, $alreadyInCart = false, $display_error = true, $check_carrier = true, $useOrderPrices = false)
    {
        if (!CartRule::isFeatureActive()) {
            return false;
        }
        $cart = $context->cart;
        if (!$alreadyInCart) {
            if (!$this->active) {
                return (!$display_error) ? false : $this->trans('This voucher is disabled', [], 'Shop.Notifications.Error');
            }
            if (!$this->quantity) {
                return (!$display_error) ? false : $this->trans('This voucher has already been used', [], 'Shop.Notifications.Error');
            }
            if (strtotime($this->date_from) > time()) {
                return (!$display_error) ? false : $this->trans('This voucher is not valid yet', [], 'Shop.Notifications.Error');
            }
            if (strtotime($this->date_to) < time()) {
                return (!$display_error) ? false : $this->trans('This voucher has expired', [], 'Shop.Notifications.Error');
            }
        }
        if ($cart->id_customer) {
            $quantityUsed = Db::getInstance()->getValue('
			SELECT count(*)
			FROM `' . _DB_PREFIX_ . 'orders` o
			LEFT JOIN `' . _DB_PREFIX_ . 'order_cart_rule` ocr ON o.`id_order` = ocr.`id_order`
			WHERE o.`id_customer` = ' . $cart->id_customer . '
			AND ocr.`deleted` = 0
			AND ocr.`id_cart_rule` = ' . (int) $this->id . '
			AND ' . (int) Configuration::get('PS_OS_ERROR') . ' != o.`current_state`
			');
            if ($alreadyInCart) {
                $quantityUsed += (int) Db::getInstance()->getValue('
                    SELECT count(*)
                    FROM `' . _DB_PREFIX_ . 'cart_cart_rule` ccr
                    INNER JOIN `' . _DB_PREFIX_ . 'cart` c ON c.id_cart = ccr.id_cart
                    LEFT JOIN `' . _DB_PREFIX_ . 'orders` o ON o.id_cart = c.id_cart
                    WHERE c.id_customer = ' . $cart->id_customer . ' AND c.id_cart = ' . $cart->id . ' AND ccr.id_cart_rule = ' . (int) $this->id . ' AND o.id_order IS NULL
                ');
            } else {
                ++$quantityUsed;
            }
            if ($quantityUsed > $this->quantity_per_user) {
                return (!$display_error) ? false : $this->trans('You cannot use this voucher anymore (usage limit reached)', [], 'Shop.Notifications.Error');
            }
        }
        if ($this->group_restriction) {
            $id_cart_rule = (int) Db::getInstance()->getValue('
			SELECT crg.id_cart_rule
			FROM ' . _DB_PREFIX_ . 'cart_rule_group crg
			WHERE crg.id_cart_rule = ' . (int) $this->id . '
			AND crg.id_group ' . ($cart->id_customer ? 'IN (SELECT cg.id_group FROM ' . _DB_PREFIX_ . 'customer_group cg WHERE cg.id_customer = ' . (int) $cart->id_customer . ')' : '= ' . (int) Configuration::get('PS_UNIDENTIFIED_GROUP')));
            if (!$id_cart_rule) {
                return (!$display_error) ? false : $this->trans('You cannot use this voucher', [], 'Shop.Notifications.Error');
            }
        }
        if ($this->country_restriction) {
            if (!$cart->id_address_delivery) {
                return (!$display_error) ? false : $this->trans('You must choose a delivery address before applying this voucher to your order', [], 'Shop.Notifications.Error');
            }
            $id_cart_rule = (int) Db::getInstance()->getValue('
			SELECT crc.id_cart_rule
			FROM ' . _DB_PREFIX_ . 'cart_rule_country crc
			WHERE crc.id_cart_rule = ' . (int) $this->id . '
			AND crc.id_country = (SELECT a.id_country FROM ' . _DB_PREFIX_ . 'address a WHERE a.id_address = ' . (int) $cart->id_address_delivery . ' LIMIT 1)');
            if (!$id_cart_rule) {
                return (!$display_error) ? false : $this->trans('You cannot use this voucher in your country of delivery', [], 'Shop.Notifications.Error');
            }
        }
        if ($this->carrier_restriction && $check_carrier) {
            if (!$cart->id_carrier) {
                return (!$display_error) ? false : $this->trans('You must choose a carrier before applying this voucher to your order', [], 'Shop.Notifications.Error');
            }
            $id_cart_rule = (int) Db::getInstance()->getValue('
			SELECT crc.id_cart_rule
			FROM ' . _DB_PREFIX_ . 'cart_rule_carrier crc
			INNER JOIN ' . _DB_PREFIX_ . 'carrier c ON (c.id_reference = crc.id_carrier AND c.deleted = 0)
			WHERE crc.id_cart_rule = ' . (int) $this->id . '
			AND c.id_carrier = ' . (int) $cart->id_carrier);
            if (!$id_cart_rule) {
                return (!$display_error) ? false : $this->trans('You cannot use this voucher with this carrier', [], 'Shop.Notifications.Error');
            }
        }
        if ($this->reduction_exclude_special) {
            $products = $cart->getProducts();
            $is_ok = false;
            foreach ($products as $product) {
                if (!$product['reduction_applies']) {
                    $is_ok = true;
                    break;
                }
            }
            if (!$is_ok) {
                return (!$display_error) ? false : $this->trans('You cannot use this voucher on products on sale', [], 'Shop.Notifications.Error');
            }
        }
        if ($this->shop_restriction && $context->shop->id && Shop::isFeatureActive()) {
            $id_cart_rule = (int) Db::getInstance()->getValue('
			SELECT crs.id_cart_rule
			FROM ' . _DB_PREFIX_ . 'cart_rule_shop crs
			WHERE crs.id_cart_rule = ' . (int) $this->id . '
			AND crs.id_shop = ' . (int) $context->shop->id);
            if (!$id_cart_rule) {
                return (!$display_error) ? false : $this->trans('You cannot use this voucher', [], 'Shop.Notifications.Error');
            }
        }
        if ($this->product_restriction) {
            $r = $this->checkProductRestrictionsFromCart($context->cart, false, $display_error, $alreadyInCart);
            if ($r !== false && $display_error) {
                return $r;
            } elseif (!$r && !$display_error) {
                return false;
            }
        }
        if ($this->id_customer && $cart->id_customer != $this->id_customer) {
            if (!Context::getContext()->customer->isLogged()) {
                return (!$display_error) ? false : ($this->trans('You cannot use this voucher', [], 'Shop.Notifications.Error') . ' - ' . $this->trans('Please log in first', [], 'Shop.Notifications.Error'));
            }
            return (!$display_error) ? false : $this->trans('You cannot use this voucher', [], 'Shop.Notifications.Error');
        }
        if ($this->minimum_amount && $check_carrier) {
            $minimum_amount = $this->minimum_amount;
            if ($this->minimum_amount_currency != Context::getContext()->currency->id) {
                $minimum_amount = Tools::convertPriceFull($minimum_amount, new Currency($this->minimum_amount_currency), Context::getContext()->currency);
            }
            $cartTotal = $cart->getOrderTotal(
                $this->minimum_amount_tax,
                Cart::ONLY_PRODUCTS,
                null,
                null,
                false,
                $useOrderPrices
            );
            if ($this->minimum_amount_shipping) {
                $cartTotal += $cart->getOrderTotal(
                    $this->minimum_amount_tax,
                    Cart::ONLY_SHIPPING,
                    null,
                    null,
                    false,
                    $useOrderPrices
                );
            }
            $products = $cart->getProducts();
            $cart_rules = $cart->getCartRules(CartRule::FILTER_ACTION_ALL, false);
            foreach ($cart_rules as $cart_rule) {
                if ($cart_rule['gift_product']) {
                    foreach ($products as $key => &$product) {
                        if (empty($product['is_gift']) && $product['id_product'] == $cart_rule['gift_product'] && $product['id_product_attribute'] == $cart_rule['gift_product_attribute']) {
                            $cartTotal = Tools::ps_round($cartTotal - $product[$this->minimum_amount_tax ? 'price_wt' : 'price'], (int) $context->currency->decimals * Context::getContext()->getComputingPrecision());
                        }
                    }
                }
                if ( $cart_rule['id_cart_rule'] != $this->id && $cart_rule['reduction_amount'] > 0 )
                {
                    $cartTotal -= $cart_rule['reduction_amount'];
                }
            }
            if ($cartTotal < $minimum_amount) {
                return (!$display_error) ? false : $this->trans('The minimum amount to benefit from this promo code is %s.', [Tools::getContextLocale($context)->formatPrice($minimum_amount, $context->currency->iso_code)], 'Shop.Notifications.Error');
            }
        }
        $nb_products = Cart::getNbProducts($cart->id);
        $otherCartRules = [];
        if ($check_carrier) {
            $otherCartRules = $cart->getCartRules(CartRule::FILTER_ACTION_ALL, false);
        }
        if (count($otherCartRules)) {
            foreach ($otherCartRules as $otherCartRule) {
                if ($otherCartRule['id_cart_rule'] == $this->id && !$alreadyInCart) {
                    return (!$display_error) ? false : $this->trans('This voucher is already in your cart', [], 'Shop.Notifications.Error');
                }
                $giftProductQuantity = $cart->getProductQuantity($otherCartRule['gift_product'], $otherCartRule['gift_product_attribute']);
                if ($otherCartRule['gift_product'] && !empty($giftProductQuantity['quantity'])) {
                    --$nb_products;
                }
                if ($this->cart_rule_restriction && $otherCartRule['cart_rule_restriction'] && $otherCartRule['id_cart_rule'] != $this->id) {
                    $combinable = Db::getInstance()->getValue('
					SELECT id_cart_rule_1
					FROM ' . _DB_PREFIX_ . 'cart_rule_combination
					WHERE (id_cart_rule_1 = ' . (int) $this->id . ' AND id_cart_rule_2 = ' . (int) $otherCartRule['id_cart_rule'] . ')
					OR (id_cart_rule_2 = ' . (int) $this->id . ' AND id_cart_rule_1 = ' . (int) $otherCartRule['id_cart_rule'] . ')');
                    if (!$combinable) {
                        $cart_rule = new CartRule((int) $otherCartRule['id_cart_rule'], $cart->id_lang);
                        if ($cart_rule->priority <= $this->priority) {
                            return (!$display_error) ? false : $this->trans('This voucher is not combinable with an other voucher already in your cart: %s', [$cart_rule->name], 'Shop.Notifications.Error');
                        } else {
                            $cart->removeCartRule($cart_rule->id);
                        }
                    }
                }
            }
        }
        if (!$nb_products) {
            return (!$display_error) ? false : $this->trans('Cart is empty', [], 'Shop.Notifications.Error');
        }
        $removed_order_cartRule_id = (int) Db::getInstance()->getValue('
			SELECT ocr.`id_order_cart_rule`
			FROM `' . _DB_PREFIX_ . 'order_cart_rule` ocr
			LEFT JOIN `' . _DB_PREFIX_ . 'orders` o ON ocr.`id_order` = o.`id_order`
			WHERE ocr.`id_cart_rule` = ' . (int) $this->id . '
			AND ocr.`deleted` = 1
			AND o.`id_cart` = ' . $cart->id);
        if ($removed_order_cartRule_id) {
            return (!$display_error) ? false : $this->trans('You cannot use this voucher because it has manually been removed.', [], 'Shop.Notifications.Error');
        }
        if (!$display_error) {
            return true;
        }
    }
    public function getContextualValue($use_tax, Context $context = null, $filter = null, $package = null, $use_cache = true)
    {
        if (!CartRule::isFeatureActive()) {
            return 0;
        }
        if (!empty($context->virtualTotalTaxIncluded) && !empty($context->virtualTotalTaxExcluded)) {
            $basePriceForPercentReduction = $use_tax ? $context->virtualTotalTaxIncluded : $context->virtualTotalTaxExcluded;
        }
        if (!$context) {
            $context = Context::getContext();
        }
        if (!$filter) {
            $filter = CartRule::FILTER_ACTION_ALL;
        }
        $all_products = $context->cart->getProducts();
        $package_products = (null === $package ? $all_products : $package['products']);
        $all_cart_rules_ids = $context->cart->getOrderedCartRulesIds();
        if (!array_key_exists($context->cart->id, static::$cartAmountCache)) {
            if (Tax::excludeTaxeOption()) {
                static::$cartAmountCache[$context->cart->id]['te'] = $context->cart->getOrderTotal(false, Cart::ONLY_PRODUCTS);
                static::$cartAmountCache[$context->cart->id]['ti'] = static::$cartAmountCache[$context->cart->id]['te'];
            } else {
                static::$cartAmountCache[$context->cart->id]['ti'] = $context->cart->getOrderTotal(true, Cart::ONLY_PRODUCTS);
                static::$cartAmountCache[$context->cart->id]['te'] = $context->cart->getOrderTotal(false, Cart::ONLY_PRODUCTS);
            }
        }
        $cart_amount_te = static::$cartAmountCache[$context->cart->id]['te'];
        $cart_amount_ti = static::$cartAmountCache[$context->cart->id]['ti'];
        $reduction_value = 0;
        $cache_id = 'getContextualValue_' . (int) $this->id . '_' . (int) $use_tax . '_' . (int) $context->cart->id . '_' . (int) $filter;
        foreach ($package_products as $product) {
            $cache_id .= '_' . (int) $product['id_product'] . '_' . (int) $product['id_product_attribute'] . (isset($product['in_stock']) ? '_' . (int) $product['in_stock'] : '');
        }
        if (Cache::isStored($cache_id)) {
            return Cache::retrieve($cache_id);
        }
        $reduction_carrier = 0;
        if ($this->free_shipping && in_array($filter, [CartRule::FILTER_ACTION_ALL, CartRule::FILTER_ACTION_ALL_NOCAP, CartRule::FILTER_ACTION_SHIPPING])) {
            if (!$this->carrier_restriction) {
                $reduction_carrier += $context->cart->getOrderTotal($use_tax, Cart::ONLY_SHIPPING, null === $package ? null : $package['products'], null === $package ? null : $package['id_carrier']);
            } else {
                $data = Db::getInstance()->executeS('
					SELECT crc.id_cart_rule, c.id_carrier
					FROM ' . _DB_PREFIX_ . 'cart_rule_carrier crc
					INNER JOIN ' . _DB_PREFIX_ . 'carrier c ON (c.id_reference = crc.id_carrier AND c.deleted = 0)
					WHERE crc.id_cart_rule = ' . (int) $this->id . '
					AND c.id_carrier = ' . (int) $context->cart->id_carrier);
                if ($data) {
                    foreach ($data as $cart_rule) {
                        $reduction_carrier += $context->cart->getCarrierCost((int) $cart_rule['id_carrier'], $use_tax, $context->country);
                    }
                }
            }
            $reduction_value += $reduction_carrier;
        }
        if (in_array($filter, [CartRule::FILTER_ACTION_ALL, CartRule::FILTER_ACTION_ALL_NOCAP, CartRule::FILTER_ACTION_REDUCTION])) {
            $order_package_products_total = 0;
            if ((float) $this->reduction_amount > 0
                || (float) $this->reduction_percent && $this->reduction_product == 0) {
                $order_package_products_total = $context->cart->getOrderTotal($use_tax, Cart::ONLY_PRODUCTS, $package_products);
                
                $data = Db::getInstance()->executeS('
					SELECT cr.reduction_amount
					FROM ' . _DB_PREFIX_ . 'cart_cart_rule ccr LEFT JOIN ' . _DB_PREFIX_ . 'cart_rule cr ON ccr.id_cart_rule = cr.id_cart_rule
					WHERE ccr.id_cart = "'.$context->cart->id.'" AND cr.id_cart_rule <> ' . (int) $this->id);
                if ($data) {
                    foreach ($data as $cart_rule) {
                        if ( $cart_rule['reduction_amount'] > 0 )
                        {
                            $order_package_products_total -= $cart_rule['reduction_amount'];
                        }
                    }
                }
            }
            if ((float) $this->reduction_percent && $this->reduction_product == 0) {
                $order_total = $order_package_products_total;
                $basePriceContainsDiscount = isset($basePriceForPercentReduction) && $order_total === $basePriceForPercentReduction;
                foreach ($context->cart->getCartRules(CartRule::FILTER_ACTION_GIFT, false) as $cart_rule) {
                    $freeProductsPrice = Tools::ps_round($cart_rule['obj']->getContextualValue($use_tax, $context, CartRule::FILTER_ACTION_GIFT, $package), Context::getContext()->getComputingPrecision());     
                    if ($basePriceContainsDiscount && isset($basePriceForPercentReduction)) {
                        $basePriceForPercentReduction -= $freeProductsPrice;
                    }
                    $order_total -= $freeProductsPrice;
                }
                if ($this->reduction_exclude_special) {
                    foreach ($package_products as $product) {
                        if ($product['reduction_applies']) {
                            $roundTotal = $use_tax ? $product['total_wt'] : $product['total'];
                            $excludedReduction = Tools::ps_round($roundTotal, Context::getContext()->getComputingPrecision());
                            $order_total -= $excludedReduction;
                            if ($basePriceContainsDiscount && isset($basePriceForPercentReduction)) {
                                $basePriceForPercentReduction -= $excludedReduction;
                            }
                        }
                    }
                }
                
                $basePriceForPercentReduction = $basePriceForPercentReduction ?? $order_total;
                $reduction_value += $basePriceForPercentReduction * $this->reduction_percent / 100;                
            }
            if ((float) $this->reduction_percent && $this->reduction_product > 0) {
                foreach ($package_products as $product) {
                    if ($product['id_product'] == $this->reduction_product && (($this->reduction_exclude_special && !$product['reduction_applies']) || !$this->reduction_exclude_special)) {
                        $reduction_value += ($use_tax ? $product['total_wt'] : $product['total']) * $this->reduction_percent / 100;
                    }
                }
            }
            if ((float) $this->reduction_percent && $this->reduction_product == -1) {
                $minPrice = false;
                $cheapest_product = null;
                foreach ($all_products as $product) {
                    $price = $product['price'];
                    if ($use_tax) {
                        $price *= (1 + $context->cart->getAverageProductsTaxRate());
                    }
                    if ($price > 0 && ($minPrice === false || $minPrice > $price) && (($this->reduction_exclude_special && !$product['reduction_applies']) || !$this->reduction_exclude_special)) {
                        $minPrice = $price;
                        $cheapest_product = $product['id_product'] . '-' . $product['id_product_attribute'];
                    }
                }
                $in_package = false;
                foreach ($package_products as $product) {
                    if ($product['id_product'] . '-' . $product['id_product_attribute'] == $cheapest_product || $product['id_product'] . '-0' == $cheapest_product) {
                        $in_package = true;
                    }
                }
                if ($in_package) {
                    $reduction_value += $minPrice * $this->reduction_percent / 100;
                }
            }
            if ((float) $this->reduction_percent && $this->reduction_product == -2) {
                $selected_products_reduction = 0;
                $selected_products = $this->checkProductRestrictionsFromCart($context->cart, true);
                if (is_array($selected_products)) {
                    foreach ($package_products as $product) {
                        if ((in_array($product['id_product'] . '-' . $product['id_product_attribute'], $selected_products)
                                || in_array($product['id_product'] . '-0', $selected_products))
                            && (($this->reduction_exclude_special && !$product['reduction_applies']) || !$this->reduction_exclude_special)) {
                            $price = $product['price'];
                            if ($use_tax) {
                                $price = $product['price_without_reduction'];
                            }
                            $selected_products_reduction += $price * $product['cart_quantity'];
                        }
                    }
                }
                $reduction_value += $selected_products_reduction * $this->reduction_percent / 100;
            }
            if ((float) $this->reduction_amount > 0) {
                $prorata = 1;
                if (null !== $package && count($all_products)) {
                    $total_products = $use_tax ? $cart_amount_ti : $cart_amount_te;
                    if ($total_products) {
                        $prorata = $order_package_products_total / $total_products;
                    }
                }
                $reduction_amount = (float) $this->reduction_amount;
                if ($this->reduction_product > 0) {
                    foreach ($all_products as $product) {
                        if ($product['id_product'] == $this->reduction_product) {
                            $productPrice = $this->reduction_tax ? $product['price_wt'] : $product['price'];
                            $max_reduction_amount = (int) $product['cart_quantity'] * (float) $productPrice;
                            $reduction_amount = min($reduction_amount, $max_reduction_amount);
                            break;
                        }
                    }
                }
                if (isset($context->currency) && $this->reduction_currency != $context->currency->id) {
                    $voucherCurrency = new Currency($this->reduction_currency);
                    if ($reduction_amount == 0 || $voucherCurrency->conversion_rate == 0) {
                        $reduction_amount = 0;
                    } else {
                        $reduction_amount /= $voucherCurrency->conversion_rate;
                    }
                    $reduction_amount *= $context->currency->conversion_rate;
                    $reduction_amount = Tools::ps_round($reduction_amount, Context::getContext()->getComputingPrecision());
                }
                if ($this->reduction_tax == $use_tax) {
                    if ($filter != CartRule::FILTER_ACTION_ALL_NOCAP) {
                        $cart_amount = $use_tax ? $cart_amount_ti : $cart_amount_te;
                        $reduction_amount = min($reduction_amount, $cart_amount);
                    }
                    $reduction_value += $prorata * $reduction_amount;
                } else {
                    if ($this->reduction_product > 0) {
                        foreach ($all_products as $product) {
                            if ($product['id_product'] == $this->reduction_product) {
                                $product_price_ti = $product['price_wt'];
                                $product_price_te = $product['price'];
                                $product_vat_amount = $product_price_ti - $product_price_te;
                                if ($product_vat_amount == 0 || $product_price_te == 0) {
                                    $product_vat_rate = 0;
                                } else {
                                    $product_vat_rate = $product_vat_amount / $product_price_te;
                                }
                                if ($this->reduction_tax && !$use_tax) {
                                    $reduction_value += $prorata * $reduction_amount / (1 + $product_vat_rate);
                                } elseif (!$this->reduction_tax && $use_tax) {
                                    $reduction_value += $prorata * $reduction_amount * (1 + $product_vat_rate);
                                }
                            }
                        }
                    } elseif ($this->reduction_product == 0) {
                        $cart_amount_te = null;
                        $cart_amount_ti = null;
                        $cart_average_vat_rate = $context->cart->getAverageProductsTaxRate($cart_amount_te, $cart_amount_ti);
                        if ($filter != CartRule::FILTER_ACTION_ALL_NOCAP) {
                            $reduction_amount = min($reduction_amount, $this->reduction_tax ? $cart_amount_ti : $cart_amount_te);
                        }
                        if ($this->reduction_tax && !$use_tax) {
                            $reduction_value += $prorata * $reduction_amount / (1 + $cart_average_vat_rate);
                        } elseif (!$this->reduction_tax && $use_tax) {
                            $reduction_value += $prorata * $reduction_amount * (1 + $cart_average_vat_rate);
                        }
                    }
                }
                if ($filter != CartRule::FILTER_ACTION_ALL_NOCAP) {
                    $cart = Context::getContext()->cart;
                    if (!Validate::isLoadedObject($cart)) {
                        $cart = new Cart();
                    }
                    $cart_average_vat_rate = $cart->getAverageProductsTaxRate();
                    $current_cart_amount = $use_tax ? $cart_amount_ti : $cart_amount_te;
                    foreach ($all_cart_rules_ids as $current_cart_rule_id) {
                        if ((int) $current_cart_rule_id['id_cart_rule'] == (int) $this->id) {
                            break;
                        }
                        $previous_cart_rule = new CartRule((int) $current_cart_rule_id['id_cart_rule']);
                        $previous_reduction_amount = $previous_cart_rule->reduction_amount;
                        if ($previous_cart_rule->reduction_tax && !$use_tax) {
                            $previous_reduction_amount = $prorata * $previous_reduction_amount / (1 + $cart_average_vat_rate);
                        } elseif (!$previous_cart_rule->reduction_tax && $use_tax) {
                            $previous_reduction_amount = $prorata * $previous_reduction_amount * (1 + $cart_average_vat_rate);
                        }
                        $current_cart_amount = max($current_cart_amount - (float) $previous_reduction_amount, 0);
                    }
                    $reduction_value = min($reduction_value, $current_cart_amount);
                }
            }
        }
        if ((int) $this->gift_product && in_array($filter, [CartRule::FILTER_ACTION_ALL, CartRule::FILTER_ACTION_ALL_NOCAP, CartRule::FILTER_ACTION_GIFT])) {
            $id_address = (null === $package ? 0 : $package['id_address']);
            foreach ($package_products as $product) {
                if ($product['id_product'] == $this->gift_product && ($product['id_product_attribute'] == $this->gift_product_attribute || !(int) $this->gift_product_attribute)) {
                    if (!isset(CartRule::$only_one_gift[$this->id . '-' . $this->gift_product])
                        || CartRule::$only_one_gift[$this->id . '-' . $this->gift_product] == $id_address
                        || CartRule::$only_one_gift[$this->id . '-' . $this->gift_product] == 0
                        || $id_address == 0
                        || !$use_cache) {
                        $reduction_value += Tools::ps_round($use_tax ? $product['price_wt'] : $product['price'], Context::getContext()->getComputingPrecision());
                        if ($use_cache && (!isset(CartRule::$only_one_gift[$this->id . '-' . $this->gift_product]) || CartRule::$only_one_gift[$this->id . '-' . $this->gift_product] == 0)) {
                            CartRule::$only_one_gift[$this->id . '-' . $this->gift_product] = $id_address;
                        }
                        break;
                    }
                }
            }
        }
        Cache::store($cache_id, $reduction_value);
        if ($use_tax && !empty($context->virtualTotalTaxIncluded)) {
            $context->virtualTotalTaxIncluded -= $reduction_value;
            if ($this->free_shipping) {
                $context->virtualTotalTaxIncluded += $reduction_carrier;
            }
        } elseif (!$use_tax && !empty($context->virtualTotalTaxExcluded)) {
            $context->virtualTotalTaxExcluded -= $reduction_value;
            if ($this->free_shipping) {
                $context->virtualTotalTaxExcluded += $reduction_carrier;
            }
        }
        return $reduction_value;
    }
}
