<?php
/**
 * Scalapay
 *
 * @author Scalapay Plugin Integration Team
 * @copyright 2022 Scalapay
 * @license LICENCE.md for license details.
 */

/*
 * @since 1.5.0
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class ScalapayValidationModuleFrontController extends ModuleFrontController
{
    use Scalapay\Traits\ScalapayRequest;

    public function initContent()
    {
        parent::initContent();
        if (Configuration::get(Scalapay::SCALAPAY_IN_PAGE_CHECKOUT_ENABLE)) {
            $this->ajax = true;
        }
    }

    /**
     * @throws Exception
     *
     * @see FrontController::postProcess()
     */
    public function postProcess()
    {
        $errorMessage = 'There was an error while processing your order with Scalapay. ' .
            'Please check with the ecommerce referent or try again choosing another payment method.';

        $isInPageCheckoutMode = Configuration::get(Scalapay::SCALAPAY_IN_PAGE_CHECKOUT_ENABLE)
            && $this->ajax
            && Tools::getValue('inPage');

        if (!$this->context->cart->id_customer
            || !$this->context->cart->id_address_delivery
            || !$this->context->cart->id_address_invoice
            || !$this->module->active
        ) {
            if ($isInPageCheckoutMode) {
                $this->sendInPageCheckoutResponse(false, $errorMessage, '');
            }

            Tools::redirect('index.php?controller=order&step=1');
        }

        $addressDelivery = new Address((int) $this->context->cart->id_address_delivery);
        $addressInvoice = new Address((int) $this->context->cart->id_address_invoice);

        $cartCurrencyCode = CurrencyCore::getCurrency($this->context->cart->id_currency)['iso_code'];

        $totalCartAmountTaxed = $this->context->cart->getOrderTotal(true, Cart::BOTH);

        $data = [];

        $data['product'] = Tools::getValue('product');
        $data['type'] = 'online';

        switch ($data['product']) {
            case Scalapay::PRODUCT_PAY_LATER:
                $data['frequency']['frequencyType'] = 'daily';
                $data['frequency']['number'] = Configuration::get(Scalapay::SCALAPAY_PAY_LATER_PAY_AFTER_DAYS);
                break;

            case Scalapay::PRODUCT_PAY_IN_4:
            case Scalapay::PRODUCT_PAY_IN_3:
            default:
                $data['frequency']['frequencyType'] = 'monthly';
                $data['frequency']['number'] = '1';

                break;
        }

        $data['totalAmount']['amount'] = (string) ($totalCartAmountTaxed ?: 0);
        $data['totalAmount']['currency'] = $cartCurrencyCode;
        $data['taxAmount']['amount'] = (string) (($totalCartAmountTaxed ?: 0) - ($this->context->cart->getOrderTotal(false, Cart::BOTH) ?: 0));
        $data['taxAmount']['currency'] = $cartCurrencyCode;

        $data['shippingAmount']['amount'] = (string) ($this->context->cart->getOrderTotal(true, Cart::ONLY_SHIPPING) ?: 0);
        $data['shippingAmount']['currency'] = $cartCurrencyCode;

        $customer = new Customer($this->context->cart->id_customer);
        $data['consumer']['phoneNumber'] = $addressInvoice->phone ?: $addressInvoice->phone_mobile;
        $data['consumer']['givenNames'] = $customer->firstname;
        $data['consumer']['surname'] = $customer->lastname;
        $data['consumer']['email'] = $customer->email;

        $data['billing']['name'] = $addressInvoice->firstname . ' ' . $addressInvoice->lastname;
        $data['billing']['line1'] = $addressInvoice->address1;
        $data['billing']['line2'] = $addressInvoice->address2;
        $data['billing']['suburb'] = $addressInvoice->city;
        $data['billing']['state'] = $addressInvoice->id_state ? State::getNameById($addressInvoice->id_state) : '';
        $data['billing']['postcode'] = $addressInvoice->postcode;
        $data['billing']['countryCode'] = Country::getIsoById($addressInvoice->id_country);
        $data['billing']['phoneNumber'] = $addressInvoice->phone ?: $addressInvoice->phone_mobile ?: '';

        $data['shipping']['name'] = $addressDelivery->firstname . ' ' . $addressDelivery->lastname;
        $data['shipping']['line1'] = $addressDelivery->address1;
        $data['shipping']['line2'] = $addressDelivery->address2;
        $data['shipping']['suburb'] = $addressDelivery->city;
        $data['shipping']['state'] = $addressDelivery->id_state ? State::getNameById($addressDelivery->id_state) : '';
        $data['shipping']['postcode'] = $addressDelivery->postcode;
        $data['shipping']['countryCode'] = Country::getIsoById($addressDelivery->id_country);
        $data['shipping']['phoneNumber'] = $addressDelivery->phone ?: $addressDelivery->phone_mobile ?: '';

        $details = $this->context->cart->getSummaryDetails($this->context->cart->id_lang, true);

        foreach ($details['products'] as $product) {
            $category = new Category($product['id_category_default'], $this->context->language->id);
            $manufacture = new Manufacturer($product['id_manufacturer'], $this->context->language->id);
            $p = new Product($product['id_product']);

            $item = [];

            $item['name'] = $product['name'];
            $item['brand'] = $manufacture->name ?: '';
            $item['category'] = $category->name ?: '';
            $item['subCategory'] = array_map(function ($category) {
                return $category['name'];
            }, Product::getProductCategoriesFull($product['id_product']));
            $item['gtin'] = $product['ean13'];
            $item['price']['amount'] = (string) round($product['price_wt'], 2);
            $item['price']['currency'] = $cartCurrencyCode;
            $item['quantity'] = $product['cart_quantity'] ?: 1;
            $item['sku'] = $product['reference'];
            $item['pageUrl'] = $p->getLink();
            $item['imageUrl'] = Context::getContext()->link->getImageLink(
                $product['link_rewrite'] ?: $product['name'],
                $product['id_image'],
                version_compare(_PS_VERSION_, '1.7.0', '<') ? ImageType::getFormatedName('small')  // @phpstan-ignore-line
                    : ImageType::getFormattedName('small')
            );

            // set product sub-categories

            $data['items'][] = $item;
        }

        foreach ($this->context->cart->getCartRules() as $coupon) {
            if (!$coupon['code']) {
                continue;
            }

            $discount = [];
            $discount['displayName'] = $coupon['name'];
            $discount['amount']['amount'] = (string) round($coupon['value_real'] ?: 0, 2);
            $discount['amount']['currency'] = $cartCurrencyCode;

            $data['discounts'][] = $discount;
        }

        $data['merchant']['redirectCancelUrl'] = $this->context->link->getModuleLink('scalapay', 'return');
        $data['merchant']['redirectConfirmUrl'] = $this->context->link->getModuleLink('scalapay', 'return', ['product' => $data['product'], 'cart_id' => $this->context->cart->id, 'total' => $totalCartAmountTaxed]);

        if ($isInPageCheckoutMode) {
            $data['merchant']['redirectCancelUrl'] = Scalapay::IN_PAGE_CHECKOUT_CDN_HTML .
                '?scalapayPopupOutputRedirect=' .
                base64_encode($data['merchant']['redirectCancelUrl']);

            $data['merchant']['redirectConfirmUrl'] = Scalapay::IN_PAGE_CHECKOUT_CDN_HTML .
                '?scalapayPopupOutputRedirect=' .
                base64_encode($data['merchant']['redirectConfirmUrl']);
        }

        $data['pluginDetails']['platform'] = 'PrestaShop';
        $data['pluginDetails']['platformVersion'] = _PS_VERSION_;
        $data['pluginDetails']['pluginVersion'] = $this->module->version;
        $data['pluginDetails']['customized'] = (string) (int) $this->module->isCustomizedPlugin; // @phpstan-ignore-line

        $response = $this->doRequest($data['product'], 'POST', '/v2/orders', $data);

        if (!isset($response['data']['checkoutUrl'])) {
            $this->addLog(
                sprintf(
                    "HTTP Error (%s) during creating a order.\nInfo:\n%s\nResponse:\n%s",
                    $response['info']['http_code'],
                    json_encode($response['info']),
                    json_encode($response['data'])
                ),
                3
            );

            if ($isInPageCheckoutMode) {
                $this->sendInPageCheckoutResponse(false, $errorMessage, '');
            }

            $this->context->cookie->__set('failed_payment_message', $this->module->l($errorMessage, 'validation'));
            Tools::redirect($this->context->link->getPageLink('cart', true, $this->context->language->id));

            return;
        }

        $this->addLog(sprintf("Successfully created order order. Response:\n%s", json_encode($response)));

        if ($isInPageCheckoutMode) {
            $this->sendInPageCheckoutResponse(true, '', $response['data']['checkoutUrl']);
        }

        Tools::redirect($response['data']['checkoutUrl']);
    }

    public function addLog($message, $severity = 1)
    {
        PrestaShopLogger::addLog(
            "[Scalapay] $message",
            $severity,
            null,
            'Cart',
            $this->context->cart->id,
            true
        );
    }

    public function sendInPageCheckoutResponse($result, $messages, $redirect)
    {
        header('Content-Type: application/json');
        echo json_encode(
            [
                'result' => $result,
                'messages' => $messages,
                'redirect' => $redirect,
            ]
        );
        exit;
    }
}
