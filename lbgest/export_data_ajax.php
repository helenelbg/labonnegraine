<?php

if ( $_GET['token'] != 'hdf6dfdfs6ddgs' )
{
    die;
}

include_once '../config/config.inc.php';
include_once '../config/settings.inc.php';
include_once '../init.php';

try {
	$bdd = new PDO("mysql:host=" . _DB_SERVER_ . ";dbname=" . _DB_NAME_, _DB_USER_, _DB_PASSWD_);
} catch (exeption $ex) {
	die("probleme connexion serveur" . $ex->getMessage());
}
// On récupère tous les produits 

$id_category = (int) $_POST['id_category'];
$id_lang = 1;

$sql = 'SELECT p.id_product, p.reference, pl.name FROM ps_product p
INNER join ps_product_lang pl ON p.id_product =  pl.id_product
LEFT join ps_category_product cp ON p.id_product =  cp.id_product
WHERE pl.id_lang = '. $id_lang .' AND p.active = 1 
AND cp.id_category = '. $id_category .' 
ORDER BY pl.name';
$product_list = Db::getInstance()->ExecuteS($sql);

$product_list_str = '<option value="0">Produit</option>';
foreach($product_list as $p){
	$product_list_str .= '<option value="'.$p['id_product'].'">'.$p['reference'].' '.$p['name'].'</option>';
}

$product_list_str .= $sql;
echo $product_list_str;




?>

