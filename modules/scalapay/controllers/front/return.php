<?php
/**
 * Scalapay
 *
 * @author Scalapay Plugin Integration Team
 * @copyright 2022 Scalapay
 * @license LICENCE.md for license details.
 */

use Scalapay\Traits\ScalapayRequest;

/**
 * @since 1.5.0
 */
class ScalapayReturnModuleFrontController extends ModuleFrontController
{
    use ScalapayRequest;

    /**
     * @see FrontController::postProcess()
     */
    public function postProcess()
    {
        $product = Tools::getValue('product') ?: Scalapay::PRODUCT_PAY_IN_3;

        $orderToken = Tools::getValue('orderToken');
        $status = Tools::getValue('status') ?: '';

        $passedTotal = Tools::getValue('total');
        $cartId = Tools::getValue('cart_id');

        switch ($product) {
            case Scalapay::PRODUCT_PAY_IN_4:
                $paymentMethod = 'Scalapay PayIn4';
                break;
            case Scalapay::PRODUCT_PAY_LATER:
                $paymentMethod = 'Scalapay PayLater';
                break;
            case Scalapay::PRODUCT_PAY_IN_3:
            default:
                $paymentMethod = 'Scalapay PayIn3';
                break;
        }

        $this->log(1, "Entered returning flow form scalapay! Context cartId {$this->context->cart->id} OrderToken: $orderToken and status $status!");

        // Check the header. Only the valid SEC_FETCH_SITE header allows the script to be executed.
        //        if ($_SERVER["HTTP_SEC_FETCH_SITE"] == 'same-site') {
        //            $this->log(1, "HTTP_SEC_FETCH_SITE detected is 'same-site'. Preventing multiple order creation.");
        //
        //            $this->context->cookie->__set(
        //                Scalapay::ERROR_MESSAGE_KEY,
        //                $this->module->l('Your Scalapay order was rejected/abandoned. If it was an error, retry. Otherwise please select another payment method.', 'return')
        //            );
        //
        //            Tools::redirect($this->context->link->getPageLink('order', true, $this->context->language->id));
        //            exit(1);
        //        }

        if (strtolower($status) !== 'success') {
            $this->log(1, 'Request was cancelled by user on scalapay.');

            $this->context->cookie->__set(
                Scalapay::ERROR_MESSAGE_KEY,
                $this->module->l('Your Scalapay order was rejected/abandoned. If it was an error, retry. Otherwise please select another payment method.', 'return')
            );

            Tools::redirect($this->context->link->getPageLink('order', true, $this->context->language->id));

            return;
        }

        if (!$orderToken) {
            $this->log(3, 'Scalapay returned success without orderToken!');

            $this->context->cookie->__set(
                Scalapay::ERROR_MESSAGE_KEY,
                $this->module->l('There was an error while processing your order with Scalapay. Please check with the ecommerce referent or try again choosing another payment method.', 'return')
            );

            Tools::redirect($this->context->link->getPageLink('order', true, $this->context->language->id));

            return;
        }

        // @phpstan-ignore-next-line
        if ($this->module->currentOrder) {
            $this->log(3, 'Order already in session.');
            $this->context->cookie->__set(
                Scalapay::ERROR_MESSAGE_KEY,
                $this->module->l('There was an error while processing your order with Scalapay. Please check with the ecommerce referent or try again choosing another payment method.', 'return')
            );

            Tools::redirect($this->context->link->getPageLink('cart', true, $this->context->language->id, [
                'action' => 'show',
            ]));

            return;
        }

        if (!Validate::isLoadedObject($this->context->cart)) {
            $this->log(3, 'Cart not found in session.');
            $this->context->cookie->__set(
                Scalapay::ERROR_MESSAGE_KEY,
                $this->module->l('There was an error while processing your order with Scalapay. Please check with the ecommerce referent or try again choosing another payment method.', 'return')
            );
            Tools::redirect($this->context->link->getPageLink('cart', true, $this->context->language->id, ['action' => 'show']));

            return;
        }

        $sessionCartTotal = $this->context->cart->getOrderTotal(true, Cart::BOTH);

        if ($this->context->cart->id != $cartId || $sessionCartTotal != $passedTotal) {
            $this->log(3, sprintf('Session cart and passed cart mismatch. Session Cart: #%s(%s) vs Passed Cart: #%s(%s)', $this->context->cart->id, $sessionCartTotal, $cartId, $passedTotal));
            $this->context->cookie->__set(
                Scalapay::ERROR_MESSAGE_KEY,
                $this->module->l('There was an error while processing your order with Scalapay. Please check with the ecommerce referent or try again choosing another payment method.', 'return')
            );
            Tools::redirect($this->context->link->getPageLink('cart', true, $this->context->language->id, ['action' => 'show']));

            return;
        }

        // check if order already set as paid (double call?)

        $sql = new DbQuery();
        $sql->select('*');
        $sql->from('order_payment');
        $sql->where('transaction_id = "' . pSQL($orderToken) . '"');

        if ($orderPayment = Db::getInstance()->getRow($sql)) {
            $this->log(1, 'Payment already register! Redirect customer to order confirmation page. ');

            /** @var OrderCore $order */
            $order = Order::getByReference($orderPayment['order_reference']);

            Tools::redirect(
                'index.php?controller=order-confirmation&id_cart=' .
                $this->context->cart->id .
                '&id_module=' . $this->module->id . '&id_order=' . $order->id .
                '&key=' . $this->context->customer->secure_key
            );

            return;
        }

        // todo: not so sure why the customer cannot exists.. but..
        $this->log(1, 'Before customer validation');
        if (!Validate::isLoadedObject(new Customer($this->context->cart->id_customer))) {
            $this->log(3, 'Customer not found order not created, refunded_not_charged issue');

            Tools::redirect('index.php?controller=order&step=1');

            return;
        }

        $this->log(1, 'Creating the order');

        try {
            // @phpstan-ignore-next-line
            $this->module->validateOrder(
                $this->context->cart->id,
                Configuration::get(Scalapay::SCALAPAY_PS_WAITING_CAPTURE_STATUS_ID),
                $sessionCartTotal,
                $paymentMethod,
                null,
                ['transaction_id' => $orderToken],
                $this->context->currency->id,
                false,
                $this->context->customer->secure_key
            );
        } catch (Exception $exception) {
            $this->log(3, "Impossible to create the order: {$exception->getMessage()}\n" . json_encode($exception->getTrace()));
        } finally {
            if (empty($this->module->currentOrder)) {
                $this->context->cookie->__set(
                    Scalapay::ERROR_MESSAGE_KEY,
                    $this->module->l('There was an error while processing your order with Scalapay. Please check with the ecommerce referent.', 'return')
                );

                Tools::redirect($this->context->link->getPageLink('order', true, $this->context->language->id));

                return;
            }
        }

        $order = new Order($this->module->currentOrder);

        $this->storeScalapayTransactionID($order->id, $orderToken, $product, $sessionCartTotal);

        $response = $this->doRequest($product, 'POST', '/v2/payments/capture', ['token' => $orderToken, 'merchantReference' => (string) $order->id]);

        if (!isset($response['data']['status']) || $response['data']['status'] !== 'APPROVED') {
            $response = $this->doRequest($product, 'POST', "payments/$orderToken/void", ['merchantReference' => (string) $order->id]);
            $order->setCurrentState((int) Configuration::get('PS_OS_CANCELED'));

            $this->log(3, sprintf("Request for token %s failed!\nResponse:\n%s", $orderToken, json_encode($response)));

            $this->context->cookie->__set(
                Scalapay::ERROR_MESSAGE_KEY,
                $this->module->l('There was an error while processing your order with Scalapay. Please check with the ecommerce referent or try again choosing another payment method.', 'return')
            );

            Tools::redirect($this->context->link->getPageLink('cart', true, $this->context->language->id, ['action' => 'show']));

            return;
        }

        $this->setTransactionAsCaptured($orderToken);

        // update order status
        switch ($product) {
            case Scalapay::PRODUCT_PAY_IN_4:
                $order->setCurrentState((int) Configuration::get(Scalapay::SCALAPAY_PAY_IN_4_ORDER_STATUS));
                break;
            case Scalapay::PRODUCT_PAY_LATER:
                $order->setCurrentState((int) Configuration::get(Scalapay::SCALAPAY_PAY_LATER_ORDER_STATUS));
                break;
            case Scalapay::PRODUCT_PAY_IN_3:
            default:
                $order->setCurrentState((int) Configuration::get(Scalapay::SCALAPAY_PAY_IN_3_ORDER_STATUS));
                break;
        }

        // search if order has the payment registered!
        $exists = false;
        // @phpstan-ignore-next-line
        foreach (OrderPayment::getByOrderReference($order->reference) as $payment) {
            if ($payment->payment_method === $paymentMethod) {
                $exists = true;
                break;
            }

            if ($payment->payment_method === $this->module->name || $payment->payment_method === $this->module->author) {
                $payment->transaction_id = $orderToken;
                $payment->payment_method = $paymentMethod;

                $payment->save();
                $exists = true;
                break;
            }
        }

        if (!$exists && !$order->addOrderPayment($sessionCartTotal, $paymentMethod, $orderToken)) {
            $this->log(3, "Impossible to add payment row to order: {$order->id} (Scalapay Token: $orderToken - $sessionCartTotal)");
        }

        Tools::redirect(
            'index.php?controller=order-confirmation&id_cart=' . $this->context->cart->id .
            '&id_module=' . $this->module->id .
            '&id_order=' . $order->id .
            '&key=' . $this->context->customer->secure_key .
            '&scalapay_order_token=' . $orderToken .
            '&scalapay_product=' . $product
        );
    }

    private function log($severity, $message)
    {
        PrestaShopLogger::addLog(
            "[scalapay]: $message",
            $severity,
            null,
            null,
            null,
            true,
            true
        );
    }

    protected function storeScalapayTransactionID($orderId, $transactionId, $product, $total)
    {
        return Db::getInstance()->Execute(
            sprintf(
                'INSERT INTO ' . _DB_PREFIX_ . Scalapay::SCALAPAY_DB . ' (`scalapay_tid`,`product`, `order_id`, `payed_amount`, `payed_at`) VALUES ("%s", "%s", "%s", "%s" , NOW())',
                pSQL($transactionId),
                pSQL($product),
                pSQL($orderId),
                pSQL($total)
            )
        );
    }

    protected function setTransactionAsCaptured($transactionId)
    {
        return Db::getInstance()->Execute(sprintf('UPDATE ' . _DB_PREFIX_ . Scalapay::SCALAPAY_DB . ' set `captured`=1 
            where `scalapay_tid`="%s" ', pSQL($transactionId)));
    }
}
