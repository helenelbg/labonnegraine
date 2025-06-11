<?php

if ( $_GET['token'] != 'hdf6dfdfs6ddgs' )
{
    die;
}
include_once '../config/config.inc.php';
include_once '../config/settings.inc.php';
include_once '../init.php';
include_once 'util.php';

try {
       $bdd = new PDO("mysql:host=" . _DB_SERVER_ . ";dbname=" . _DB_NAME_, _DB_USER_, _DB_PASSWD_);
} catch (exeption $ex) {
       die("probleme connexion serveur" . $ex->getMessage());
}

if(!isset($_POST['id_product'])){
	die;
}

$id_product = pSQL($_POST['id_product']);

// Vérification de l'unicité du numéro du lot LBG
$sql = 'SELECT numero_lot_LBG, id_inventaire_lots FROM ps_inventaire_lots
WHERE id_product = "'.$id_product.'"';
$res = Db::getInstance()->ExecuteS($sql);

$lots = array();

if(is_array($res)){
	foreach($res as $r){
		$numero_lot_LBG = $r['numero_lot_LBG'];
		$id_inventaire_lots = $r['id_inventaire_lots'];
		$lot = substr($numero_lot_LBG, -4, 4);
		$lots[$lot] = $id_inventaire_lots;
	}
}

krsort($lots); // tri par ordre décroissant

echo json_encode($lots);   

?>
