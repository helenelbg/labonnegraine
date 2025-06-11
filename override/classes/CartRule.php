<?php
/**
* NOTICE OF LICENSE
*
* This product is licensed for one customer to use on one installation (test stores and multishop included).
* Site developer has the right to modify this module to suit their needs, but can not redistribute the module in
* whole or in part. Any other use of this module constitues a violation of the user agreement.
*
* DISCLAIMER
*
* NO WARRANTIES OF DATA SAFETY OR MODULE SECURITY
* ARE EXPRESSED OR IMPLIED. USE THIS MODULE IN ACCORDANCE
* WITH YOUR MERCHANT AGREEMENT, KNOWING THAT VIOLATIONS OF
* PCI COMPLIANCY OR A DATA BREACH CAN COST THOUSANDS OF DOLLARS
* IN FINES AND DAMAGE A STORES REPUTATION. USE AT YOUR OWN RISK.
*
*  @author    idnovate.com <info@idnovate.com>
*  @copyright 2022 idnovate.com
*  @license   See above
*/
class CartRule extends CartRuleCore
{
    /*
    * module: quantitydiscountpro
    * date: 2023-06-21 17:26:01
    * version: 2.1.43
    */
    public static function autoRemoveFromCart(Context $context = null, bool $useOrderPrice = false)
    {
        if (Module::isEnabled('quantitydiscountpro')) {
            include_once(_PS_MODULE_DIR_.'quantitydiscountpro/quantitydiscountpro.php');
            $quantityDiscount = new QuantityDiscountRule();
            $quantityDiscount->createAndRemoveRules(null, $context);
        }
        parent::autoRemoveFromCart($context, $useOrderPrice);
    }
    /*
    * module: quantitydiscountpro
    * date: 2023-06-21 17:26:01
    * version: 2.1.43
    */
    public static function autoAddToCart(Context $context = null, bool $useOrderPrices = false)
    {
        parent::autoAddToCart($context, $useOrderPrices);
        if (Module::isEnabled('quantitydiscountpro')) {
            include_once(_PS_MODULE_DIR_.'quantitydiscountpro/quantitydiscountpro.php');
            $quantityDiscount = new QuantityDiscountRule();
            $quantityDiscount->createAndRemoveRules(null, $context);
        }
    }
    /*
    * module: quantitydiscountpro
    * date: 2023-06-21 17:26:01
    * version: 2.1.43
    */
    public function update($null_values = false)
    {
        $r = parent::update($null_values);
        if (Module::isEnabled('quantitydiscountpro')) {
            include_once(_PS_MODULE_DIR_.'quantitydiscountpro/quantitydiscountpro.php');
            if (CartRule::isCurrentlyUsed('cart_rule', true) || QuantityDiscountRule::isCurrentlyUsed(null, true)) {
                Configuration::updateGlobalValue('PS_CART_RULE_FEATURE_ACTIVE', true);
            } else {
                Configuration::updateGlobalValue('PS_CART_RULE_FEATURE_ACTIVE', false);
            }
        }
        return $r;
    }
    /*
    * module: quantitydiscountpro
    * date: 2023-06-21 17:26:01
    * version: 2.1.43
    */
    public function delete()
    {
        $r = parent::delete();
        if (Module::isEnabled('quantitydiscountpro')) {
            include_once(_PS_MODULE_DIR_.'quantitydiscountpro/quantitydiscountpro.php');
            if (CartRule::isCurrentlyUsed('cart_rule', true) || QuantityDiscountRule::isCurrentlyUsed(null, true)) {
                Configuration::updateGlobalValue('PS_CART_RULE_FEATURE_ACTIVE', true);
            } else {
                Configuration::updateGlobalValue('PS_CART_RULE_FEATURE_ACTIVE', false);
            }
        }
        return $r;
    }
    /*
    * module: quantitydiscountpro
    * date: 2023-06-21 17:26:01
    * version: 2.1.43
    */
    public static function getCustomerCartRules(
        $id_lang,
        $id_customer,
        $active = false,
        $includeGeneric = true,
        $inStock = false,
        CartCore $cart = null,
        $free_shipping_only = false,
        $highlight_only = false
    ) {
        $result = parent::getCustomerCartRules($id_lang, $id_customer, $active, $includeGeneric, $inStock, $cart, $free_shipping_only, $highlight_only);
        if (!Module::isEnabled('quantitydiscountpro') || !$highlight_only) {
            return $result;
        }
        include_once(_PS_MODULE_DIR_.'quantitydiscountpro/quantitydiscountpro.php');
        $quantityDiscountRule = new QuantityDiscountRule();
        $quantityDiscountProRules = $quantityDiscountRule->getHighlightedQuantityDiscountRules();
        foreach ($quantityDiscountProRules as &$quantityDiscountProRule) {
            if (isset($cart, $cart->id) && $id =  $quantityDiscountRule->getIdCartFruleFromIdQuantityDiscountRuleFromThisCart($quantityDiscountProRule['id_quantity_discount_rule'], $cart->id)) {
                $quantityDiscountProRule['id_cart_rule'] = $id;
            } else {
                $quantityDiscountProRule['id_cart_rule'] = PHP_INT_MAX;
            }
            /*$quantityDiscountProRule['minimum_amount'] = 0;
            $quantityDiscountProRule['cart_rule_restriction'] = 0;
            $quantityDiscountProRule['reduction_percent'] = 0;
            $quantityDiscountProRule['reduction_amount'] = 0;
            $quantityDiscountProRule['free_shipping'] = 1;
            $quantityDiscountProRule['carrier_restriction'] = 1;
            $quantityDiscountProRule['gift_product'] = 0;
            $quantityDiscountProRule['minimum_amount_tax'] = 0;
            $quantityDiscountProRule['minimum_amount_shipping'] = 0;
            $quantityDiscountProRule['quantity_for_user'] = 0;*/
            unset($quantityDiscountProRule);
        }
        return array_merge($result, $quantityDiscountProRules);
    }
    /*
    * module: quantitydiscountpro
    * date: 2023-06-21 17:26:01
    * version: 2.1.43
    */
    public static function getCustomerHighlightedDiscounts(
        $languageId,
        $customerId,
        CartCore $cart
    ) {
        return static::getCustomerCartRules(
            $languageId,
            $customerId,
            $active = true,
            $includeGeneric = true,
            $inStock = true,
            $cart,
            $freeShippingOnly = false,
            $highlightOnly = true
        );
    }
    /*
    * module: quantitydiscountpro
    * date: 2023-06-21 17:26:01
    * version: 2.1.43
    */
    protected function getCartRuleCombinations($offset = null, $limit = null, $search = '')
    {
        if (!Module::isEnabled('quantitydiscountpro')) {
            return parent::getCartRuleCombinations($offset, $limit, $search);
        }
        $array = array();
        if ($offset !== null && $limit !== null) {
            $sql_limit = ' LIMIT ' . (int) $offset . ', ' . (int) ($limit + 1);
        } else {
            $sql_limit = '';
        }
        $array['selected'] = Db::getInstance()->executeS('
        SELECT cr.*, crl.*, 1 as selected
        FROM ' . _DB_PREFIX_ . 'cart_rule cr
        LEFT JOIN ' . _DB_PREFIX_ . 'cart_rule_lang crl ON (cr.id_cart_rule = crl.id_cart_rule AND crl.id_lang = ' . (int) Context::getContext()->language->id . ')
        WHERE cr.id_cart_rule != ' . (int) $this->id . ($search ? ' AND crl.name LIKE "%' . pSQL($search) . '%"' : '') . '
        AND (
            cr.cart_rule_restriction = 0
            OR EXISTS (
                SELECT 1
                FROM ' . _DB_PREFIX_ . 'cart_rule_combination
                WHERE cr.id_cart_rule = ' . _DB_PREFIX_ . 'cart_rule_combination.id_cart_rule_1 AND ' . (int) $this->id . ' = id_cart_rule_2
            )
            OR EXISTS (
                SELECT 1
                FROM ' . _DB_PREFIX_ . 'cart_rule_combination
                WHERE cr.id_cart_rule = ' . _DB_PREFIX_ . 'cart_rule_combination.id_cart_rule_2 AND ' . (int) $this->id . ' = id_cart_rule_1
            )
        )
        AND cr.id_cart_rule NOT IN (SELECT id_cart_rule FROM `'._DB_PREFIX_.'quantity_discount_rule_cart`)
        ORDER BY cr.id_cart_rule' . $sql_limit);
        $array['unselected'] = Db::getInstance()->executeS('
        SELECT cr.*, crl.*, 1 as selected
        FROM ' . _DB_PREFIX_ . 'cart_rule cr
        INNER JOIN ' . _DB_PREFIX_ . 'cart_rule_lang crl ON (cr.id_cart_rule = crl.id_cart_rule AND crl.id_lang = ' . (int) Context::getContext()->language->id . ')
        LEFT JOIN ' . _DB_PREFIX_ . 'cart_rule_combination crc1 ON (cr.id_cart_rule = crc1.id_cart_rule_1 AND crc1.id_cart_rule_2 = ' . (int) $this->id . ')
        LEFT JOIN ' . _DB_PREFIX_ . 'cart_rule_combination crc2 ON (cr.id_cart_rule = crc2.id_cart_rule_2 AND crc2.id_cart_rule_1 = ' . (int) $this->id . ')
        WHERE cr.cart_rule_restriction = 1
        AND cr.id_cart_rule != ' . (int) $this->id . ($search ? ' AND crl.name LIKE "%' . pSQL($search) . '%"' : '') . '
        AND crc1.id_cart_rule_1 IS NULL
        AND crc2.id_cart_rule_1 IS NULL
        AND cr.id_cart_rule NOT IN (SELECT id_cart_rule FROM `'._DB_PREFIX_.'quantity_discount_rule_cart`)
        AND crc2.id_cart_rule_1 IS NULL  ORDER BY cr.id_cart_rule' . $sql_limit);
        return $array;
    }
    /*
    * module: quantitydiscountpro
    * date: 2023-06-21 17:26:01
    * version: 2.1.43
    */
    public function getAssociatedRestrictions(
        $type,
        $active_only,
        $i18n,
        $offset = null,
        $limit = null,
        $search_cart_rule_name = ''
    ) {
        if (!Module::isEnabled('quantitydiscountpro')) {
            return parent::getAssociatedRestrictions($type, $active_only, $i18n, $offset, $limit, $search_cart_rule_name);
        }
        $array = array('selected' => array(), 'unselected' => array());
        if (!in_array($type, array('country', 'carrier', 'group', 'cart_rule', 'shop'))) {
            return false;
        }
        $shop_list = '';
        if ($type == 'shop') {
            $shops = Context::getContext()->employee->getAssociatedShops();
            if (is_array($shops) && count($shops)) {
                $shop_list = ' AND t.id_shop IN (' . implode(',',array_map('intval', $shops)) . ') ';
            }
        }
        if ($offset !== null && $limit !== null) {
            $sql_limit = ' LIMIT ' . (int) $offset . ', ' . (int) ($limit + 1);
        } else {
            $sql_limit = '';
        }
        if (!Validate::isLoadedObject($this) || $this->{$type . '_restriction'} == 0) {
            $array['selected'] = Db::getInstance()->executeS('
            SELECT t.*' . ($i18n ? ', tl.*' : '') . ', 1 as selected
            FROM `' . _DB_PREFIX_ . $type . '` t
            ' . ($i18n ? 'LEFT JOIN `' . _DB_PREFIX_ . $type . '_lang` tl ON (t.id_' . $type . ' = tl.id_' . $type . ' AND tl.id_lang = ' . (int) Context::getContext()->language->id . ')' : '') . '
            WHERE 1
            ' . ($active_only ? 'AND t.active = 1' : '') . '
            ' . (in_array($type, array('carrier', 'shop')) ? ' AND t.deleted = 0' : '') . '
            ' . ($type == 'cart_rule' ? 'AND t.id_cart_rule != '.(int)$this->id.' AND t.id_cart_rule NOT IN (SELECT id_cart_rule FROM `'._DB_PREFIX_.'quantity_discount_rule_cart`)' : '') .
                $shop_list .
                (in_array($type, array('carrier', 'shop')) ? ' ORDER BY t.name ASC ' : '') .
                (in_array($type, array('country', 'group', 'cart_rule')) && $i18n ? ' ORDER BY tl.name ASC ' : '') .
                $sql_limit);
        } else {
            if ($type == 'cart_rule') {
                $array = $this->getCartRuleCombinations($offset, $limit, $search_cart_rule_name);
            } else {
                $resource = Db::getInstance()->executeS(
                    'SELECT t.*' . ($i18n ? ', tl.*' : '') . ', IF(crt.id_' . $type . ' IS NULL, 0, 1) as selected
                    FROM `' . _DB_PREFIX_ . $type . '` t
                    ' . ($i18n ? 'LEFT JOIN `' . _DB_PREFIX_ . $type . '_lang` tl ON (t.id_' . $type . ' = tl.id_' . $type . ' AND tl.id_lang = ' . (int) Context::getContext()->language->id . ')' : '') . '
                    LEFT JOIN (SELECT id_' . $type . ' FROM `' . _DB_PREFIX_ . 'cart_rule_' . $type . '` WHERE id_cart_rule = ' . (int) $this->id . ') crt ON t.id_' . ($type == 'carrier' ? 'reference' : $type) . ' = crt.id_' . $type . '
                    WHERE 1 ' . ($active_only ? ' AND t.active = 1' : '') .
                        $shop_list
                        . (in_array($type, array('carrier', 'shop')) ? ' AND t.deleted = 0' : '') .
                        (in_array($type, array('carrier', 'shop')) ? ' ORDER BY t.name ASC ' : '') .
                        (in_array($type, array('country', 'group')) && $i18n ? ' ORDER BY tl.name ASC ' : '') .
                        $sql_limit,
                    false
                );
                while ($row = Db::getInstance()->nextRow($resource)) {
                    $array[($row['selected'] || $this->{$type . '_restriction'} == 0) ? 'selected' : 'unselected'][] = $row;
                }
            }
        }
        return $array;
    }
    /*
    * module: quantitydiscountpro
    * date: 2023-06-21 17:26:01
    * version: 2.1.43
    */
    public static function getCartsRuleByCode($name, $id_lang, $extended = false)
    {
        if (!Module::isEnabled('quantitydiscountpro')) {
            return parent::getCartsRuleByCode($name, $id_lang, $extended);
        }
        $query = '
            SELECT cr.`id_cart_rule`, cr.`code`, crl.`name`
            FROM '._DB_PREFIX_.'cart_rule cr
            LEFT JOIN '._DB_PREFIX_.'cart_rule_lang crl ON (cr.id_cart_rule = crl.id_cart_rule AND crl.id_lang = '.(int)$id_lang.')
            WHERE cr.`id_cart_rule` NOT IN (SELECT qdrc.`id_cart_rule` FROM '._DB_PREFIX_.'quantity_discount_rule_cart qdrc) AND (code LIKE \'%'.pSQL($name).'%\''
            .($extended ? ' OR name LIKE \'%'.pSQL($name).'%\'' : '').')
            UNION
            SELECT CONCAT("QDP~", qdr.`id_quantity_discount_rule`) as `id_cart_rule`, qdr.`code`, qdrl.`name`
            FROM '._DB_PREFIX_.'quantity_discount_rule qdr
            LEFT JOIN '._DB_PREFIX_.'quantity_discount_rule_lang qdrl ON (qdr.id_quantity_discount_rule = qdrl.id_quantity_discount_rule AND qdrl.id_lang = '.(int)$id_lang.')
            WHERE code LIKE \'%'.pSQL($name).'%\''
            .($extended ? ' OR name LIKE \'%'.pSQL($name).'%\'' : '');
        return Db::getInstance()->executeS($query);
    }

    public function checkValidity(Context $context, $alreadyInCart = false, $display_error = true, $check_carrier = true, $useOrderPrices = false)
    {
        if (!CartRule::isFeatureActive()) {
            return false;
        }
        $cart = $context->cart;

        // All these checks are necessary when you add the cart rule the first time, so when it's not in cart yet
        // However when it's in the cart and you are checking if the cart rule is still valid (when performing auto remove)
        // these rules are outdated For example:
        //  - the cart rule can now be disabled but it was at the time it was applied, so it doesn't need to be removed
        //  - the current date is not in the range any more but it was at the time
        //  - the quantity is now zero but it was not when it was added
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
                // Sometimes a cart rule is already in a cart, but the cart is not yet attached to an order (when logging
                // in for example), these cart rules are not taken into account by the query above:
                // so we count cart rules that are already linked to the current cart but not attached to an order yet.

                $quantityUsed += (int) Db::getInstance()->getValue('
                    SELECT count(*)
                    FROM `' . _DB_PREFIX_ . 'cart_cart_rule` ccr
                    INNER JOIN `' . _DB_PREFIX_ . 'cart` c ON c.id_cart = ccr.id_cart
                    LEFT JOIN `' . _DB_PREFIX_ . 'orders` o ON o.id_cart = c.id_cart
                    WHERE c.id_customer = ' . $cart->id_customer . ' AND c.id_cart = ' . $cart->id . ' AND ccr.id_cart_rule = ' . (int) $this->id . ' AND o.id_order IS NULL
                ');
            } else {
                // When checking the cart rules present in that cart the request result is accurate
                // When we check if using the cart rule one more time is valid then we increment this value
                ++$quantityUsed;
            }
            if ($quantityUsed > $this->quantity_per_user) {
                return (!$display_error) ? false : $this->trans('You cannot use this voucher anymore (usage limit reached)', [], 'Shop.Notifications.Error');
            }
        }

        // Get an intersection of the customer groups and the cart rule groups (if the customer is not logged in, the default group is Visitors)
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

        // Check if the customer delivery address is usable with the cart rule
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

        // Check if the carrier chosen by the customer is usable with the cart rule
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

        // Check if the cart rules appliy to the shop browsed by the customer
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

        // Check if the products chosen by the customer are usable with the cart rule
        if ($this->product_restriction) {
            $r = $this->checkProductRestrictionsFromCart($context->cart, false, $display_error, $alreadyInCart);
            if ($r !== false && $display_error) {
                return $r;
            } elseif (!$r && !$display_error) {
                return false;
            }
        }

        // Check if the cart rule is only usable by a specific customer, and if the current customer is the right one
        if ($this->id_customer && $cart->id_customer != $this->id_customer) {
            if (!Context::getContext()->customer->isLogged()) {
                return (!$display_error) ? false : ($this->trans('You cannot use this voucher', [], 'Shop.Notifications.Error') . ' - ' . $this->trans('Please log in first', [], 'Shop.Notifications.Error'));
            }

            return (!$display_error) ? false : $this->trans('You cannot use this voucher', [], 'Shop.Notifications.Error');
        }

        if ($this->minimum_amount && $check_carrier) {
            // Minimum amount is converted to the contextual currency
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

        /* This loop checks:
          - if the voucher is already in the cart
          - if a non compatible voucher is in the cart
          - if there are products in the cart (gifts excluded)
          Important note: this MUST be the last check, because if the tested cart rule has priority over a non combinable one in the cart, we will switch them
         */
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
                        // The cart rules are not combinable and the cart rule currently in the cart has priority over the one tested
                        if ($cart_rule->priority <= $this->priority) {
                            return (!$display_error) ? false : $this->trans('This voucher is not combinable with an other voucher already in your cart: %s', [$cart_rule->name], 'Shop.Notifications.Error');
                        } else {
                            // But if the cart rule that is tested has priority over the one in the cart, we remove the one in the cart and keep this new one
                            $cart->removeCartRule($cart_rule->id);
                        }
                    }
                }
            }
        }

        if (!$nb_products) {
            return (!$display_error) ? false : $this->trans('Cart is empty', [], 'Shop.Notifications.Error');
        }

        // Check if order cart rule was removed from back office
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

        // set base price that will be used for percent reductions
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

        /*foreach ($package_products as $product) {
            if ( $_SERVER['REMOTE_ADDR'] == '176.134.75.130' || $_SERVER['REMOTE_ADDR'] == '80.15.118.113' )
    {
        echo '<pre>';
        print_r($product);
        echo '</pre><hr>';
    }
}*/

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

        // Free shipping on selected carriers
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
                    /*$data = Db::getInstance()->executeS('
                        SELECT cr.reduction_amount
                        FROM ' . _DB_PREFIX_ . 'cart_cart_rule ccr LEFT JOIN ' . _DB_PREFIX_ . 'cart_rule cr ON ccr.id_cart_rule = cr.id_cart_rule
                        WHERE ccr.id_cart = "'.$context->cart->id.'"');*/

                if ($data) {
                    foreach ($data as $cart_rule) {
                        if ( $cart_rule['reduction_amount'] > 0 )
                        {
                            $order_package_products_total -= $cart_rule['reduction_amount'];
                            /*if (isset($basePriceForPercentReduction))
                            {
                                $basePriceForPercentReduction -= $cart_rule['reduction_amount'];
                            }*/
                        }
                    }
                }
            }
            // Discount (%) on the whole order
            if ((float) $this->reduction_percent && $this->reduction_product == 0) {
                // Do not give a reduction on free products!
                $order_total = $order_package_products_total;
                $basePriceContainsDiscount = isset($basePriceForPercentReduction) && $order_total === $basePriceForPercentReduction;
                foreach ($context->cart->getCartRules(CartRule::FILTER_ACTION_GIFT, false) as $cart_rule) {
                    $freeProductsPrice = Tools::ps_round($cart_rule['obj']->getContextualValue($use_tax, $context, CartRule::FILTER_ACTION_GIFT, $package), Context::getContext()->getComputingPrecision());     

                    if ($basePriceContainsDiscount && isset($basePriceForPercentReduction)) {
                        // Gifts haven't been excluded yet, we need to do it
                        $basePriceForPercentReduction -= $freeProductsPrice;
                    }
                    $order_total -= $freeProductsPrice;
                }

                // Remove products that are on special
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
                
                // set base price on which percentage reduction will be applied
                $basePriceForPercentReduction = $basePriceForPercentReduction ?? $order_total;
                $reduction_value += $basePriceForPercentReduction * $this->reduction_percent / 100;                
            }

            // Discount (%) on a specific product
            if ((float) $this->reduction_percent && $this->reduction_product > 0) {
                foreach ($package_products as $product) {
                   /* if ( $_SERVER['REMOTE_ADDR'] == '176.134.75.130' || $_SERVER['REMOTE_ADDR'] == '80.15.118.113' )
            {
                echo '<pre>';
                print_r($product);
                echo '</pre><hr>';
            }*/
                    if ($product['id_product'] == $this->reduction_product && (($this->reduction_exclude_special && !$product['reduction_applies']) || !$this->reduction_exclude_special)) {
                        $reduction_value += ($use_tax ? $product['total_wt'] : $product['total']) * $this->reduction_percent / 100;
                    }
                }
            }

            // Discount (%) on the cheapest product
            if ((float) $this->reduction_percent && $this->reduction_product == -1) {
                $minPrice = false;
                $cheapest_product = null;
                foreach ($all_products as $product) {
                    $price = $product['price'];
                    if ($use_tax) {
                        // since later on we won't be able to know the product the cart rule was applied to,
                        // use average cart VAT for price_wt
                        $price *= (1 + $context->cart->getAverageProductsTaxRate());
                    }

                    if ($price > 0 && ($minPrice === false || $minPrice > $price) && (($this->reduction_exclude_special && !$product['reduction_applies']) || !$this->reduction_exclude_special)) {
                        $minPrice = $price;
                        $cheapest_product = $product['id_product'] . '-' . $product['id_product_attribute'];
                    }
                }

                // Check if the cheapest product is in the package
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

            // Discount (%) on the selection of products
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

            // Discount (¤)
            if ((float) $this->reduction_amount > 0) {
                $prorata = 1;
                if (null !== $package && count($all_products)) {
                    $total_products = $use_tax ? $cart_amount_ti : $cart_amount_te;
                    if ($total_products) {
                        $prorata = $order_package_products_total / $total_products;
                    }
                }

                $reduction_amount = (float) $this->reduction_amount;


                if ($this->reduction_product == -2) 
                {
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
                    
                    $reduction_amount = min($reduction_amount, $selected_products_reduction);
                }


                // If the cart rule is restricted to one product it can't exceed this product price
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

                // If we need to convert the voucher value to the cart currency
                if (isset($context->currency) && $this->reduction_currency != $context->currency->id) {
                    $voucherCurrency = new Currency($this->reduction_currency);

                    // First we convert the voucher value to the default currency
                    if ($reduction_amount == 0 || $voucherCurrency->conversion_rate == 0) {
                        $reduction_amount = 0;
                    } else {
                        $reduction_amount /= $voucherCurrency->conversion_rate;
                    }

                    // Then we convert the voucher value in the default currency into the cart currency
                    $reduction_amount *= $context->currency->conversion_rate;
                    $reduction_amount = Tools::ps_round($reduction_amount, Context::getContext()->getComputingPrecision());
                }

                // If it has the same tax application that you need, then it's the right value, whatever the product!
                if ($this->reduction_tax == $use_tax) {
                    // The reduction cannot exceed the products total, except when we do not want it to be limited (for the partial use calculation)
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
                        // Discount (¤) on the whole order
                        $cart_amount_te = null;
                        $cart_amount_ti = null;
                        $cart_average_vat_rate = $context->cart->getAverageProductsTaxRate($cart_amount_te, $cart_amount_ti);

                        // The reduction cannot exceed the products total, except when we do not want it to be limited (for the partial use calculation)
                        if ($filter != CartRule::FILTER_ACTION_ALL_NOCAP) {
                            $reduction_amount = min($reduction_amount, $this->reduction_tax ? $cart_amount_ti : $cart_amount_te);
                        }

                        if ($this->reduction_tax && !$use_tax) {
                            $reduction_value += $prorata * $reduction_amount / (1 + $cart_average_vat_rate);
                        } elseif (!$this->reduction_tax && $use_tax) {
                            $reduction_value += $prorata * $reduction_amount * (1 + $cart_average_vat_rate);
                        }
                    }
                    /*
                     * Reduction on the cheapest or on the selection is not really meaningful and has been disabled in the backend, it only applies with percent
                     * Please keep this code, so it won't be considered as a bug
                     * elseif ($this->reduction_product == -1)
                     * elseif ($this->reduction_product == -2)
                     */
                }

                // Take care of the other cart rules values if the filter allow it
                if ($filter != CartRule::FILTER_ACTION_ALL_NOCAP) {
                    // Cart values
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

        // Free gift
        if ((int) $this->gift_product && in_array($filter, [CartRule::FILTER_ACTION_ALL, CartRule::FILTER_ACTION_ALL_NOCAP, CartRule::FILTER_ACTION_GIFT])) {
            $id_address = (null === $package ? 0 : $package['id_address']);
            foreach ($package_products as $product) {
                if ($product['id_product'] == $this->gift_product && ($product['id_product_attribute'] == $this->gift_product_attribute || !(int) $this->gift_product_attribute)) {
                    // The free gift coupon must be applied to one product only (needed for multi-shipping which manage multiple product lists)
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

        // update virtual total values, for percentage reductions that might be applied later
        // but remove the carrier as free shipping is not a real reduction
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
