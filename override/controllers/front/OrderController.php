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

use PrestaShop\PrestaShop\Core\Foundation\Templating\RenderableProxy;

class OrderController extends OrderControllerCore
{
    /*
    * module: quantitydiscountpro
    * date: 2023-06-21 17:26:03
    * version: 2.1.43
    */
    protected function bootstrap()
    {
        parent::bootstrap();
        if (Module::isEnabled('quantitydiscountpro') && Tools::getValue('action') == 'updateCarrier') {
            include_once(_PS_MODULE_DIR_.'quantitydiscountpro/quantitydiscountpro.php');
            $quantityDiscount = new QuantityDiscountRule();
            $quantityDiscount->createAndRemoveRules();
        }
    }
	
	public function initContent()
    {        
        $cartLink = $this->context->link->getPageLink('cart', null, null, ['action' => 'show']);
        if (Configuration::isCatalogMode()) {
            Tools::redirect('index.php');
        }
        $this->restorePersistedData($this->checkoutProcess);
        $this->checkoutProcess->handleRequest(
            Tools::getAllValues()
        );
        $presentedCart = $this->cart_presenter->present($this->context->cart);
        if (count($presentedCart['products']) <= 0 || $presentedCart['minimalPurchaseRequired']) {
            $cartLink = $this->context->link->getPageLink('cart');
          
            Tools::redirect($cartLink);
        }
        $product = $this->context->cart->checkQuantities(true);
        if (Module::isEnabled('wkbundleproduct') && Configuration::get('PS_STOCK_MANAGEMENT')) {
            $context = Context::getContext();
            require_once _PS_MODULE_DIR_ . 'wkbundleproduct/wkbundleproduct.php';
            if ($cartProducts = $context->cart->getProducts()) {
                if (count($cartProducts) > 0) {
                    $objBundle = new WkBundle();
                    $objTempData = new WkBundleCartDataFinal();
                    $objSubproduct = new WkBundleSubProduct();
                    foreach ($cartProducts as $productList) {
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
               
                                Tools::redirect($cartLink);
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
         
                                            Tools::redirect($cartLink);
                                        }
                                    } else {
                 
                                        Tools::redirect($cartLink);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        if (is_array($product)) {
            
            Tools::redirect($cartLink);
        }
        $this->checkoutProcess
            ->setNextStepReachable()
            ->markCurrentStep()
            ->invalidateAllStepsAfterCurrent();
        $this->saveDataToPersist($this->checkoutProcess);
        if (!$this->checkoutProcess->hasErrors()) {
            if ($_SERVER['REQUEST_METHOD'] !== 'GET' && !$this->ajax) {
                return $this->redirectWithNotifications(
                    $this->checkoutProcess->getCheckoutSession()->getCheckoutURL()
                );
            }
        }
        $this->context->smarty->assign([
            'checkout_process' => new RenderableProxy($this->checkoutProcess),
            'cart' => $presentedCart,
        ]);
        $this->context->smarty->assign([
            'display_transaction_updated_info' => Tools::getIsset('updatedTransaction'),
        ]);
        
        /*parent::initContent();
       
        $this->setTemplate('checkout/checkout');*/

        $shouldRedirectToCart = false;

        // Check the cart meets minimal order amount treshold
        // Check that the cart is not empty
        if (count($presentedCart['products']) <= 0 || $presentedCart['minimalPurchaseRequired']) {
            $shouldRedirectToCart = true;
        }

        // Check that products are still orderable, at any point in checkout
        if ($this->context->cart->isAllProductsInStock() !== true ||
            $this->context->cart->checkAllProductsAreStillAvailableInThisState() !== true ||
            $this->context->cart->checkAllProductsHaveMinimalQuantities() !== true) {
                $this->errors[] = 'Un ou plusieurs produits de votre panier sont indisponibles';
            $shouldRedirectToCart = true;
        }

        // If there was a problem, we redirect the user to cart, CartController deals with display of detailed errors
        // We don't redirect in case of ajax requests, so we can get our response
        if ($shouldRedirectToCart === true && !$this->ajax) {
            $cartLink = $this->context->link->getPageLink('cart', null, null, ['action' => 'show']);
            $this->redirectWithNotifications($cartLink);
        }

        $this->checkoutProcess
            ->setNextStepReachable()
            ->markCurrentStep()
            ->invalidateAllStepsAfterCurrent();

        $this->saveDataToPersist($this->checkoutProcess);

        if (!$this->checkoutProcess->hasErrors()) {
            if ($_SERVER['REQUEST_METHOD'] !== 'GET' && !$this->ajax) {
                return $this->redirectWithNotifications(
                    $this->checkoutProcess->getCheckoutSession()->getCheckoutURL()
                );
            }
        }

        $this->context->smarty->assign([
            'checkout_process' => new RenderableProxy($this->checkoutProcess),
            'display_transaction_updated_info' => Tools::getIsset('updatedTransaction'),
            'tos_cms' => $this->getDefaultTermsAndConditions(),
        ]);

        FrontController::initContent();
        $this->setTemplate('checkout/checkout');
    }
}
