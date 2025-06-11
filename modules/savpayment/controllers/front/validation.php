<?php
class SavPaymentValidationModuleFrontController extends ModuleFrontController
{
    public function postProcess()
    {
        $cart = $this->context->cart;

        if ($cart->id_customer == 0 || $cart->id_address_delivery == 0 || $cart->id_address_invoice == 0 || !$this->module->active) {
            Tools::redirect('index.php?controller=order&step=1');
        }

        // Vérification du support de la devise
        $authorized_currencies = false;
        foreach (Currency::checkPaymentCurrencies($this->module->id) as $currency) {
            if ($cart->id_currency == $currency['id_currency']) {
                $authorized_currencies = true;
                break;
            }
        }

        if (!$authorized_currencies) {
            die($this->module->l('Cette méthode de paiement n\'est pas disponible.'));
        }

        // Créer l'état de commande personnalisé si nécessaire
        $order_state = Configuration::get('PS_OS_SAV');
        if (!$order_state) {
            $order_state = $this->createSavOrderState();
        }

        // Valider la commande
        $this->module->validateOrder(
            $cart->id, 
            $order_state, 
            $cart->getOrderTotal(true, Cart::BOTH), 
            $this->module->displayName, 
            null, 
            [], 
            $cart->id_currency, 
            false, 
            $cart->secure_key
        );

        // Redirection
        Tools::redirect('index.php?controller=order-confirmation&id_cart='.$cart->id.'&id_module='.$this->module->id.'&id_order='.$this->module->currentOrder.'&key='.$cart->secure_key);
    }

    private function createSavOrderState()
    {
        $order_state = new OrderState();
        $order_state->name = array_fill(0, 10, 'SAV');
        $order_state->send_email = false;
        $order_state->color = '#34209E';
        $order_state->unremovable = true;
        $order_state->logable = true;
        $order_state->add();

        Configuration::updateValue('PS_OS_SAV', $order_state->id);

        return $order_state->id;
    }
}