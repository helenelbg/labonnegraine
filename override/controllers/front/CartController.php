<?php
/**
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License version 3.0
* that is bundled with this package in the file LICENSE.txt
* It is also available through the world-wide-web at this URL:
* https://opensource.org/licenses/AFL-3.0
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to a newer
* versions in the future. If you wish to customize this module for your needs
* please refer to CustomizationPolicy.txt file inside our module for more information.
*
* @author Webkul IN
* @copyright Since 2010 Webkul
* @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
*/
class CartController extends CartControllerCore
{
    /**
     * Initialize cart controller.
     *
     * @see FrontController::init()
     */
    public function init()
    {
        FrontController::init();

        // Send noindex to avoid ghost carts by bots
        header('X-Robots-Tag: noindex, nofollow', true);

        // Get page main parameters
        $this->id_product = (int) Tools::getValue('id_product', null);
        $this->id_product_attribute = (int) Tools::getValue('id_product_attribute', Tools::getValue('ipa'));
        $this->customization_id = (int) Tools::getValue('id_customization');
        $this->qty = abs((int)Tools::getValue('qty', 1));
        $this->id_address_delivery = (int) Tools::getValue('id_address_delivery');
        $this->preview = ('1' === Tools::getValue('preview'));

        /* Check if the products in the cart are available */
        if ('show' === Tools::getValue('action')) {
            $isAvailable = $this->areProductsAvailable();
            if (Tools::getIsset('checkout')) {
                return Tools::redirect($this->context->link->getPageLink('order'));
            }
            if (true !== $isAvailable) {
                $this->errors[] = $isAvailable;
            }
        }

        if (Module::isInstalled('wkbundleproduct') && Module::isEnabled('wkbundleproduct')) {
            if (Tools::getValue('add')) {
                require_once _PS_MODULE_DIR_ . 'wkbundleproduct/wkbundleproduct.php';
                $objBundle = new WkBundle();
                $this->id_product = (int)Tools::getValue('id_product', null);
                $isBundle = $objBundle->isBundleProduct(
                    $this->id_product
                );
                if ($isBundle) {
                    if (!$this->context->cart->id && isset($_COOKIE[$this->context->cookie->getName()])) {
                        $this->context->cart->add();
                        $this->context->cookie->id_cart = (int)$this->context->cart->id;
                    }
                    $objFinalCart = new WkBundleCartDataFinal();
                    $tempData = $objFinalCart->getTempCartData(
                        Tools::getValue('id_product'),
                        $this->context->cookie->id_wk_bundle_identifier,
                        $this->context->shop->id
                    );
                    $isSameBundle = $objFinalCart->checkBundleAreSame($this->context->cart, $tempData);
                    if ($isSameBundle) {
                        $this->customization_id = $isSameBundle;
                        $this->context->cookie->wk_id_customization = $isSameBundle;
                    } else {
                        $this->textRecord(new Product($this->id_product));
                        $customization_datas = $this->context->cart->getProductCustomization($this->id_product, null, true);
                        $this->customization_id = empty($customization_datas) ? null :
                            $customization_datas[0]['id_customization'];
                        $this->context->cookie->wk_id_customization = $this->customization_id;
                        if (!empty($tempData)) {
                            foreach ($tempData as $data) {
                                $data['id_customization'] = $this->customization_id;
                                $data['id_cart'] = $this->context->cart->id;
                                $objFinalCart->insertDataTempToFinal($data);
                            }
                        }
                    }
                    $this->context->cookie->write();
                }
            }
        }
    }

    public function initContent()
    {
        parent::initContent();
        $this->context->smarty->assign([
            'cartRulesEnCours' => $this->context->cart->getCartRules()
        ]);
    }

    protected function areProductsAvailable()
    {
        $products = $this->context->cart->getProducts();
        if (Module::isEnabled('wkbundleproduct') && Configuration::get('PS_STOCK_MANAGEMENT')) {
            require_once _PS_MODULE_DIR_ . 'wkbundleproduct/wkbundleproduct.php';
            if (!empty($products)) {
                $objBundle = new WkBundle();
                $objTempData = new WkBundleCartDataFinal();
                $objSubproduct = new WkBundleSubProduct();
                foreach ($products as $productList) {
                    if ($objBundle->isBundleProduct($productList['id_product'])) {
                        $productIdArray = [];
                        $bundleProductInformation = $objTempData->getSelectedBundleProduct(
                            $productList['id_product'],
                            $this->context->cart->id,
                            $this->context->shop->id
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
                                    $this->context->shop->id
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
                            $this->context->cart->deleteProduct($productList['id_product']);
                        }
                        if (!empty($productIdArray)) {
                            $nameArray = [];
                            foreach ($productIdArray as $product) {
                                $nameArray[] = Product::getProductName(
                                    $product,
                                    0,
                                    $this->context->language->id
                                );
                            }
                            if ($nameArray) {
                                $nameArray = implode(',', $nameArray);
                            }

                            return $this->trans(
                                'Decrease bundle quantity some subproducts are out of stock. Product(s) are %product%',
                                ['%product%' => $nameArray],
                                'Shop.Notifications.Error'
                            );
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
                                        return $this->trans(
                                            'Some Product(s) are out of stock',
                                            [],
                                            'Shop.Notifications.Error'
                                        );
                                    }
                                } else {
                                    return $this->trans(
                                        'Some Product(s) are out of stock',
                                        [],
                                        'Shop.Notifications.Error'
                                    );
                                }
                            }
                        }
                    }
                }
            }
        }
        foreach ($products as $product) {
            $currentProduct = new Product();
            $currentProduct->hydrate($product);

            if ($currentProduct->hasAttributes() && $product['id_product_attribute'] === '0') {
                return $this->trans(
                    'The item %product% in your cart is now a product with attributes. Please delete it and choose one of its combinations to proceed with your order.',
                    ['%product%' => $product['name']],
                    'Shop.Notifications.Error'
                );
            }
        }

        $product = $this->context->cart->checkQuantities(true);

        if (true === $product || !is_array($product)) {
            return true;
        }

        if ($product['active']) {
            return $this->trans(
                'The item %product% in your cart is no longer available in this quantity. You cannot proceed with your order until the quantity is adjusted.',
                ['%product%' => $product['name']],
                'Shop.Notifications.Error'
            );
        }

        return $this->trans(
            'This product (%product%) is no longer available.',
            ['%product%' => $product['name']],
            'Shop.Notifications.Error'
        );
    }

    /**
     * Custom function to add custonization data dynamically
     *
     * @param object $objProduct
     *
     * @return void
     */
    protected function textRecord($objProduct)
    {
        if (!$fieldIds = $objProduct->getCustomizationFieldIds()) {
            return false;
        }

        $authorizedTextFields = [];
        foreach ($fieldIds as $fieldId) {
            if ($fieldId['type'] == Product::CUSTOMIZE_TEXTFIELD) {
                $authorizedTextFields[(int) $fieldId['id_customization_field']] = 'textField' .
                 (int) $fieldId['id_customization_field'];
            }
        }

        $indexes = array_flip($authorizedTextFields);
        foreach ($authorizedTextFields as $fieldName) {
            $objModule = Module::getInstanceByName('wkbundleproduct');
            $value = $objModule->getHiddenCustomLabel();

            if (in_array($fieldName, $authorizedTextFields) && $value != '') {
                $this->context->cart->addTextFieldToProduct(
                    $objProduct->id,
                    $indexes[$fieldName],
                    Product::CUSTOMIZE_TEXTFIELD,
                    $value
                );
            } elseif (in_array($fieldName, $authorizedTextFields) && $value == '') {
                $this->context->cart->deleteCustomizationToProduct((int) $objProduct->id, $indexes[$fieldName]);
            }
        }
    }

    protected function updateCart()
    {
        // Update the cart ONLY if it's not a bot, in order to avoid ghost carts
        if (!Connection::isBot()
            && !$this->errors
            && !($this->context->customer->isLogged() && !$this->isTokenValid())
        ) {
            if (Tools::getIsset('add') || Tools::getIsset('update')) {
                $this->processChangeProductInCart();
            } elseif (Tools::getIsset('delete')) {
                $this->processDeleteProductInCart();
            } elseif (CartRule::isFeatureActive()) {
                if (Tools::getIsset('addDiscount')) {
                    if (!($code = trim(Tools::getValue('discount_name')))) {
                        $this->errors[] = $this->trans(
                            'You must enter a voucher code.',
                            [],
                            'Shop.Notifications.Error'
                        );
                    } elseif (!Validate::isCleanHtml($code)) {
                        $this->errors[] = $this->trans(
                            'The voucher code is invalid.',
                            [],
                            'Shop.Notifications.Error'
                        );
                    } else {
                        $cartRule = new CartRule(CartRule::getIdByCode($code));
                        if (Validate::isLoadedObject($cartRule)) {
                            if ($error = $cartRule->checkValidity($this->context)) {
                                $this->errors[] = $error;
                            } else {
                                $this->context->cart->addCartRule($cartRule->id);
                                // Si code de l'affilié on l'applique au customer
                                //if ( $code == 'LBGPO1C' )
                                if ( $cartRule->id == 24424 )
                                {
                                    $this->context->cart->removeCartRule(40711);
                                    $this->context->cart->removeCartRule(115655);

                                    require_once(_PS_MODULE_DIR_."psaffiliate/classes/Tracking.php");
                                    $tracking = new Tracking;
                                    $tracking->startTracking2(8);
                                }
                                elseif ( $cartRule->id == 40711 )
                                {
                                    $this->context->cart->removeCartRule(24424);
                                    $this->context->cart->removeCartRule(115655);

                                    require_once(_PS_MODULE_DIR_."psaffiliate/classes/Tracking.php");
                                    $tracking = new Tracking;
                                    $tracking->startTracking2(5);
                                }
                                elseif ( $cartRule->id == 115655 )
                                {
                                    $this->context->cart->removeCartRule(24424);
                                    $this->context->cart->removeCartRule(40711);

                                    require_once(_PS_MODULE_DIR_."psaffiliate/classes/Tracking.php");
                                    $tracking = new Tracking;
                                    $tracking->startTracking2(4);
                                }
                            }
                        } else {
                            $this->errors[] = $this->trans(
                                'This voucher does not exist.',
                                [],
                                'Shop.Notifications.Error'
                            );
                        }
                    }
                } elseif (($id_cart_rule = (int) Tools::getValue('deleteDiscount'))
                    && Validate::isUnsignedId($id_cart_rule)
                ) {
                    $this->context->cart->removeCartRule($id_cart_rule);
                    CartRule::autoAddToCart($this->context);
                }
            }
        } elseif (!$this->isTokenValid() && Tools::getValue('action') !== 'show' && !Tools::getValue('ajax')) {
            Tools::redirect('index.php');
        }
    }
}
