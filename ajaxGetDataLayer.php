<?php

$result["code"] = 0;

//error_log("AJAXGETDATALAYER - DEBUT SCRIPT");


if(isset($_POST["id_order"]) && !empty($_POST["id_order"]) && isset($_POST["token"]) && !empty($_POST["token"])){
    //error_log("AJAXGETDATALAYER - Dans le premier if - ID : ".$_POST["id_order"]);
    if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
        //error_log("AJAXGETDATALAYER - Dans le second if - ID : ".$_POST["id_order"]." - HTTPREQ : ".$_SERVER['HTTP_X_REQUESTED_WITH']);
        if(!empty($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], "labonnegraine.com") !== false){
            //error_log("AJAXGETDATALAYER - Dans le troisieme if - ID : ".$_POST["id_order"]);
            if($_POST["token"] == sha1(md5($_POST["id_order"]))){
                //error_log("AJAXGETDATALAYER - Dans le quatrieme if - ID : ".$_POST["id_order"]." - token : ".$_POST["token"]);

                require_once(dirname(__FILE__).'/config/config.inc.php');
                require_once(dirname(__FILE__)."/init.php");

                $order = new Order($_POST["id_order"]);
                $dataLayerFormated = [];

                $customer = new Customer($order->id_customer);
                $adresse = new Address($order->id_address_delivery);
                $products = $order->getProducts();

                $dataLayerFormated["event"] = "purchase";
                $dataLayerFormated["ecommerce"]["transaction_id"] = $order->reference;
                $dataLayerFormated["ecommerce"]["value"] = number_format($order->getOrdersTotalPaid(), 2, ".", "");
                $dataLayerFormated["ecommerce"]["tax"] = number_format($order->total_paid_tax_incl - $order->total_paid_tax_excl, 2, ".", "");
                $dataLayerFormated["ecommerce"]["currency"] = "EUR";
                $dataLayerFormated["ecommerce"]["shipping"] = number_format($order->total_shipping, 2, ".", "");
                $dataLayerFormated["ecommerce"]["mail"] = $customer->email;
                $dataLayerFormated["ecommerce"]["phone"] = $adresse->phone;

                foreach ($products as $product){
                    $dataLayerFormated["ecommerce"]["items"][] = [
                        "item_id" => $product["product_reference"],
                        "index" => $product["product_id"],
                        "item_name" => $product["product_name"],
                        "currency" => "EUR",
                        "quantity" => $product["product_quantity"],
                        "price" => number_format($product["total_price_tax_incl"], 2, ".", "")
                    ];
                }

                $result["code"] = 200;
                $result["datalayer"] = $dataLayerFormated;
            }else{
                //error_log("AJAXGETDATALAYER - Les données ne correspondent pas");
            }
        }else{
            //error_log("AJAXGETDATALAYER - L'appel ne vient pas du on referer");
        }
    }else{
        //error_log("AJAXGETDATALAYER - Ce n'est pas un appel AJAX");
    }
}else{
    //error_log("AJAXGETDATALAYER - Les données sont manquantes");
}
//error_log("AJAXGETDATALAYER - FIN SCRIPT");

echo json_encode($result);
