<?php 

include_once '../config/config.inc.php';
include_once '../config/settings.inc.php';
include_once '../init.php';

try {
       $bdd = new PDO("mysql:host=" . _DB_SERVER_ . ";dbname=" . _DB_NAME_, _DB_USER_, _DB_PASSWD_);
} catch (exeption $ex) {
       die("probleme connexion serveur" . $ex->getMessage());
}


$mycont=Context::getContext(); 
$mycont->cart = new Cart(629063);

$delivery_option_list = $mycont->cart->getDeliveryOptionList();
$package_list = $mycont->cart->getPackageList(); 
$cart_delivery_option = $mycont->cart->getDeliveryOption();
 
foreach ($cart_delivery_option as $id_address => $key_carriers) {
    foreach ($delivery_option_list[$id_address][$key_carriers]['carrier_list'] as $id_carrier => $data) {
        foreach ($data['package_list'] as $id_package) {
            // Rewrite the id_warehouse
            $package_list[$id_address][$id_package]['id_warehouse'] = (int) $mycont->cart->getPackageIdWarehouse($package_list[$id_address][$id_package], (int) $id_carrier);
            $package_list[$id_address][$id_package]['id_carrier'] = $id_carrier;
        }
    }
}

foreach ($package_list as $id_address => $packageByAddress) {
    foreach ($packageByAddress as $id_package => $package) {
        
       
$cartec = new Cart(629063); 
$discount = $cartec->getOrderTotal(true, Cart::ONLY_DISCOUNTS, $package['product_list'], $package['id_carrier']);
   
echo $discount.'<br />'; 
}
        }
?>