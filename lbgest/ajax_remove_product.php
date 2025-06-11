<?php
	
if ( $_POST['token'] != 'hdf6dfdfs6ddgs' )
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

$json = array();
$json['msg'] = '';

if( $_POST['action'] == 'supprimer_produit' && $_POST['id_commande'] > 0 && $_POST['id_product'] > 0 ){
	
	$id_commande = pSQL($_POST['id_commande']);
	//$id_product = pSQL($_POST['id_product']);
	$id_detail = pSQL($_POST['id_detail']);
	
	// Delete from cmd_fournisseur_detail
	$sql = 'DELETE FROM cmd_fournisseur_detail 
	WHERE id_detail = '.$id_detail.' AND id_cmd = '.$id_commande;

	$req = Db::getInstance()->Execute($sql);
 
	//$json['sql'] = $sql; // affichage debug
	
	$json['msg'] = 'ok';
	
	echo (json_encode($json));
}
?>
