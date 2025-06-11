<?php
include('config/config.inc.php');
ini_set('display_errors','on');
error_reporting(E_ALL);

require 'init.php';

if(isset($context->cart->id))
{
  $cart = $context->cart;
}
else
{
    
  $cart = new Cart();
  $context->cart=$cart;
  $cart->id_customer = (int)($context->cookie->id_customer);
  $cart->id_guest = (int)($context->cookie->id_guest);
  $cart->id_address_delivery = (int)  (Address::getFirstCustomerAddressId($cart->id_customer));
  $cart->id_address_invoice = $cart->id_address_delivery;
  $cart->id_lang = (int)($context->cookie->id_lang);
  $cart->id_currency = (int)($context->cookie->id_currency);


  $cart->id_carrier = 1;
  $cart->recyclable = 0;
  $cart->gift = 0;
  $cart->add();
  $context->cookie->id_cart = (int)($cart->id);
  $cart->update();
  $id_cart=$cart->id;
}

foreach($_POST['txts'] as $val) {
  if(strlen($val[1])){
    $index = str_replace("textField", "", $val[0]);
    $context->cart->_addCustomization($_GET["id_product"], 0, $index, 1, $val[1], 0);
  }

}


?>