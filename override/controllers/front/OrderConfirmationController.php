<?php
use PrestaShop\PrestaShop\Adapter\Presenter\Object\ObjectPresenter;
use PrestaShop\PrestaShop\Adapter\Presenter\Order\OrderPresenter;
use PrestaShop\PrestaShop\Core\Security\PasswordPolicyConfiguration;
use ZxcvbnPhp\Zxcvbn;

class OrderConfirmationController extends OrderConfirmationControllerCore
{
  public function initContent()
  {
	  FrontController::initContent();

    $req = 'SELECT id_order FROM ps_orders WHERE reference = "' . $this->order->reference . '";';
    $rangee = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($req);
    $order_listEC = array();
    foreach($rangee as $cmdEC)
    {
      if ( $cmdEC['id_order'] != $this->order->id )
      {
        $order_listEC[] = (new OrderPresenter())->present(new Order($cmdEC['id_order']));
      }
    }

    if ( count($order_listEC) == 0 )
    {
      $order_listEC = false;
    }

	  $this->context->smarty->assign([
            'HOOK_ORDER_CONFIRMATION' => $this->displayOrderConfirmation($this->order),
            'HOOK_PAYMENT_RETURN' => $this->displayPaymentReturn($this->order),
            'order' => (new OrderPresenter())->present($this->order),
            'order_listEC' => $order_listEC,
            'order_customer' => (new ObjectPresenter())->present($this->customer),
            'registered_customer_exists' => Customer::customerExists($this->customer->email, false, true),
        ]);

        $ets_payment_with_fee = Module::getInstanceByName('ets_payment_with_fee');
            $ets_payment_with_fee ->initContentOrderConfirmation($this->id_cart,$this->order_presenter); 
            $this->setTemplate('checkout/order-confirmation'); 

        if ($this->context->customer->is_guest) {
			
            $this->context->customer->mylogout();
        }   
		else {
			$address_google = new Address($this->context->cart->id_address_delivery);
			$time = mktime(0, 0, 0, date('m'), date('d')+8, date('Y'));

			$this->context->smarty->assign(array(
				'id_order_google' => $this->id_order,
				'email_google' => $this->context->customer->email,
				'iso_country_google' => Country::getIsoById($address_google->id_country),
				'delivery_date_google' => date('Y-m-d', $time)
			));
		}
		
    

    if ($this->context->customer->is_guest)
    {
      $this->context->smarty->assign(array(
        'id_order' => $this->id_order,
        'reference_order' => $this->reference,
        'id_order_formatted' => sprintf('#%06d', $this->id_order),
        'email' => $this->context->customer->email
      ));
      $this->context->customer->mylogout();
    }
    else {
      $address_google = new Address($this->context->cart->id_address_delivery);
      $time = mktime(0, 0, 0, date('m'), date('d')+8, date('Y'));

      $this->context->smarty->assign(array(
        'id_order_google' => $this->id_order,
        'email_google' => $this->context->customer->email,
        'iso_country_google' => Country::getIsoById($address_google->id_country),
        'delivery_date_google' => date('Y-m-d', $time)
      ));
    }

  }
}
