<?php
include('config/config.inc.php');
ini_set('display_errors','on');
error_reporting(E_ALL);

require 'init.php';

foreach($_POST['txts'] as $val) {
  $une_fois = 0;

  if(strlen($val[1])){

    $tt = Customization::getCustomizations($context->cart->id);
    foreach ($tt as $t_value) {
      if($une_fois == 0){
        $id_customization = $t_value['id_customization'];
        $id_product_attr = $t_value['id_product_attribute'];

        $une_fois = 1;
      }
    }

    echo $id_customization.' - '.$id_product_attr;
    Cart::update_cart_product($context->cart->id, $_GET["id_product"], $id_product_attr, $id_customization);
  }

}

?>