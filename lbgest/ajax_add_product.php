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

if( $_POST['action'] == 'ajout_produit' && $_POST['id_commande'] > 0 && $_POST['id_product'] > 0 && $_POST['unite'] ){
	
	$id_commande = pSQL($_POST['id_commande']);
	$id_product = pSQL($_POST['id_product']);
	$unite = pSQL($_POST['unite']);
	$qte = pSQL($_POST['qte']);
	$qte = str_replace(',','.',$qte); // remplace la notation française pour les décimales

	$id_etat = 1; // A approvisioner
	
	// insert into table cmd_fournisseur_detail
	$sql = 'INSERT INTO cmd_fournisseur_detail
	(id_cmd,id_produit,qte,unite,id_etat) 
	VALUES("'.$id_commande.'","'.$id_product.'","'.$qte.'","'.$unite.'","'.$id_etat.'")';
	
	$req = Db::getInstance()->Execute($sql);
 
	//$json['sql'] = $sql; // affichage debug
	
	$json['msg'] = 'ok';
	
	echo (json_encode($json));
}
?>
