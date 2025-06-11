<?php
/**
 * 2007-2021 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2021 PrestaShop SA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
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
        $product = Tools::getValue("product") ?: Scalapay::PRODUCT_PAY_IN_3;

        $orderToken = Tools::getValue('orderToken');
        $status = Tools::getValue('status') ?: "";

        $passedTotal = Tools::getValue('total');
        $cartId = Tools::getValue('cart_id');

        switch ($product) {
            case Scalapay::PRODUCT_PAY_IN_4:
                $paymentMethod = "Scalapay PayIn4";
                break;
            case Scalapay::PRODUCT_PAY_LATER:
                $paymentMethod = "Scalapay PayLater";
                break;
            case Scalapay::PRODUCT_PAY_IN_3:
            default:
                $paymentMethod = "Scalapay PayIn3";
                break;
        }

        $this->log(1, "Entered returning flow form scalapay! Context cartId {$this->context->cart->id} OrderToken: $orderToken and status $status!");

        if (strtolower($status) !== "success") {
            $this->log(1, "Request was cancelled by user on scalapay.");

            $this->context->cookie->__set(
                Scalapay::ERROR_MESSAGE_KEY,
                $this->module->l('Your Scalapay order was rejected/abandoned. If it was an error, retry. Otherwise please select another payment method.', 'return')
            );

            Tools::redirect($this->context->link->getPageLink('order', true, $this->context->language->id));

            return;
        }

        if (!$orderToken) {
            $this->log(3, "Scalapay returned success without orderToken!");

            $this->context->cookie->__set(
                Scalapay::ERROR_MESSAGE_KEY,
                $this->module->l('There was an error while processing your order with Scalapay. Please check with the ecommerce referent or try again choosing another payment method.', 'return')
            );

            Tools::redirect($this->context->link->getPageLink('order', true, $this->context->language->id));

            return;
        }

        // @phpstan-ignore-next-line
        if ($this->module->currentOrder) {
            $this->log(3, "Order already in session.");
            $this->context->cookie->__set(
                Scalapay::ERROR_MESSAGE_KEY,
                $this->module->l('There was an error while processing your order with Scalapay. Please check with the ecommerce referent or try again choosing another payment method.', 'return')
            );

            Tools::redirect($this->context->link->getPageLink('cart', true, $this->context->language->id, array(
                "action" => "show",
            )));

            return;
        }

        if (!Validate::isLoadedObject($this->context->cart)) {
            $this->log(3, "Cart not found in session.");
            $this->context->cookie->__set(
                Scalapay::ERROR_MESSAGE_KEY,
                $this->module->l('There was an error while processing your order with Scalapay. Please check with the ecommerce referent or try again choosing another payment method.', 'return')
            );
            Tools::redirect($this->context->link->getPageLink('cart', true, $this->context->language->id, array("action" => "show")));

            return;
        }

        $sessionCartTotal = $this->context->cart->getOrderTotal(true, Cart::BOTH);

        if ($this->context->cart->id != $cartId || $sessionCartTotal != $passedTotal) {
            $this->log(3, sprintf("Session cart and passed cart mismatch. Session Cart: #%s(%s) vs Passed Cart: #%s(%s)", $this->context->cart->id, $sessionCartTotal, $cartId, $passedTotal));
            $this->context->cookie->__set(
                Scalapay::ERROR_MESSAGE_KEY,
                $this->module->l('There was an error while processing your order with Scalapay. Please check with the ecommerce referent or try again choosing another payment method.', 'return')
            );
            Tools::redirect($this->context->link->getPageLink('cart', true, $this->context->language->id, array("action" => "show")));

            return;
        }

        // check if order already set as paid (double call?)

        $sql = new DbQuery();
        $sql->select('*');
        $sql->from('order_payment');
        $sql->where('transaction_id = "' . pSQL($orderToken) . '"');

        if ($orderPayment = Db::getInstance()->getRow($sql)) {
            $this->log(1, "Payment already register! Redirect customer to order confirmation page. ");

            /** @var OrderCore $order */
            $order = Order::getByReference($orderPayment["order_reference"]);

            Tools::redirect(
                'index.php?controller=order-confirmation&id_cart=' .
                $this->context->cart->id .
                '&id_module=' . $this->module->id . '&id_order=' . $order->id .
                '&key=' . $this->context->customer->secure_key
            );

            return;
        }

        // todo: not so sure why the customer cannot exists.. but..
        $this->log(1, "Before customer validation");
        if (!Validate::isLoadedObject(new Customer($this->context->cart->id_customer))) {
            $this->log(3, "Customer not found order not created, refunded_not_charged issue");

            Tools::redirect('index.php?controller=order&step=1');

            return;
        }

        $this->log(1, "Creating the order");

        try {
            // @phpstan-ignore-next-line
            $this->module->validateOrder(
                $this->context->cart->id,
                Configuration::get(Scalapay::SCALAPAY_PS_WAITING_CAPTURE_STATUS_ID),
                $sessionCartTotal,
                $paymentMethod,
                null,
                array('transaction_id' => $orderToken),
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

        $response = $this->doRequest($product, "POST", "/v2/payments/capture", array("token" => $orderToken, "merchantReference" => (string)$order->id));

        if (!isset($response["data"]["status"]) || $response["data"]["status"] !== "APPROVED") {
            $response = $this->doRequest($product, "POST", "payments/$orderToken/void", array("merchantReference" => (string)$order->id));
            $order->setCurrentState((int)Configuration::get("PS_OS_CANCELED"));

            $this->log(3, sprintf("Request for token %s failed!\nResponse:\n%s", $orderToken, json_encode($response)));

            $this->context->cookie->__set(
                Scalapay::ERROR_MESSAGE_KEY,
                $this->module->l('There was an error while processing your order with Scalapay. Please check with the ecommerce referent or try again choosing another payment method.', 'return')
            );

            Tools::redirect($this->context->link->getPageLink('cart', true, $this->context->language->id, array("action" => "show")));

            return;
        }

        $this->setTransactionAsCaptured($orderToken);

        // update order status
        $order->setCurrentState((int)Configuration::get(Scalapay::SCALAPAY_PAY_IN_3_ORDER_STATUS));

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
            '&key=' . $this->context->customer->secure_key
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
