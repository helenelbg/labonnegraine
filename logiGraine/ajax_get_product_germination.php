<?php
require 'application_top.php';

if (isset($_POST['ean']) && !empty($_POST['ean'])) {
    $ean = (int)$_POST['ean'];
    
    $sql = 'SELECT id_product 
            FROM ps_product_attribute
            WHERE ean13 = "'.$ean.'";';
    
    $id_produit = Db::getInstance()->executeS($sql);
        
    echo $id_produit[0]['id_product'];
}
?>