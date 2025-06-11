<?php
	
if ( $_POST['token'] != 'hdf6dfdfs6ddgs' )
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

$json = array();
$json['msg'] = '';
$now_date = date('Y-m-d');

if( $_POST['action'] == 'change_prix'){
	
	$id_product = $_POST['id_product'];
	$prix_achat = $_POST['prix_achat'];
	$unite_achat = $_POST['unite_achat'];

	$prix_achat = str_replace(',','.',$prix_achat); // remplace la notation française pour les décimales
	
	$unite_vente = get_unite_vente($id_product);
	
	// conversion du prix d'achat si l'unité d'achat est différente de l'unité de vente
	// TODO à vérifier

	if($unite_vente == 'gramme' && $unite_achat == 'graines'){
		$conversion = conversion($id_product);
		if($conversion != 0){
			$prix_achat = $prix_achat * $conversion;
		}
	}else if($unite_vente == 'graine' && $unite_achat == 'kg'){
		$conversion = conversion($id_product);
		if($conversion != 0){
			$prix_achat = $prix_achat / $conversion;
		}
	}
	
	
	if($prix_achat){
		// update table ps_product and ps_product_shop
		$sql = 'UPDATE ps_product SET wholesale_price = "'.pSQL($prix_achat).'"
		WHERE id_product = '.pSQL($id_product).'';
		$req = Db::getInstance()->Execute($sql);
		
		$sql = 'UPDATE ps_product_shop SET wholesale_price = "'.pSQL($prix_achat).'"
		WHERE id_product = '.pSQL($id_product).'';
		$req = Db::getInstance()->Execute($sql);
	}

}elseif( $_POST['action'] == 'change_quantite'){
	
	$id_product = $_POST['id_product'];
	$id_detail = $_POST['id_detail'];
	$id_commande = $_POST['id_commande'];
	$qte_demandee = $_POST['qte_demandee'];
	$qte_demandee = str_replace(',','.',$qte_demandee); // remplace la notation française pour les décimales
	$qte_demandee = str_replace(' ','',$qte_demandee); // supprime les espaces dûes au masque de saisie
	$unite_achat = $_POST['unite_achat'];

	// update table cmd_fournisseur_detail
	$sql = 'UPDATE cmd_fournisseur_detail SET qte = "'.pSQL($qte_demandee).'", unite = "'.pSQL($unite_achat).'", date_update = "'.$now_date.'"  
	WHERE id_cmd = '.pSQL($id_commande).'
	AND id_detail = '.pSQL($id_detail).'';
	$req = Db::getInstance()->Execute($sql);

}elseif( $_POST['action'] == 'change_date_reliquat'){
	
	$id_product = $_POST['id_product'];
	$id_detail = $_POST['id_detail'];
	$id_commande = $_POST['id_commande'];
	$date_reliquat = $_POST['date_reliquat'];

	// update table cmd_fournisseur_detail
	$sql = 'UPDATE cmd_fournisseur_detail SET date_reliquat = "'.pSQL($date_reliquat).'", date_update = "'.$now_date.'" 
	WHERE id_cmd = '.pSQL($id_commande).'
	AND id_detail = '.pSQL($id_detail).'';
	$req = Db::getInstance()->Execute($sql);

}elseif( $_POST['action'] == 'change_etat'){
	
	$id_product = $_POST['id_product'];
	$id_detail = $_POST['id_detail'];
	$id_commande = $_POST['id_commande'];
	$id_etat = $_POST['id_etat'];

	// update table cmd_fournisseur_detail
	$sql = 'UPDATE cmd_fournisseur_detail SET id_etat = '.pSQL($id_etat).', date_update = "'.$now_date.'"  
	WHERE id_cmd = '.pSQL($id_commande).'
	AND id_detail = '.pSQL($id_detail).'';
	$req = Db::getInstance()->Execute($sql);

}


echo (json_encode($json));

?>
