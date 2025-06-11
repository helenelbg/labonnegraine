<?php

die;
include("../config/config.inc.php");

$products = Db::getInstance()->executes("SELECT `id_product` FROM `ps_product_carrier` WHERE `id_carrier_reference` IN (189)");

foreach ($products as $product){
    
        Db::getInstance()->execute("INSERT INTO ps_product_carrier(id_product, id_carrier_reference, id_shop) VALUES (".$product["id_product"].", 348, 1)");
    

    echo $product["id_product"]."<br>";
//    die;
}

