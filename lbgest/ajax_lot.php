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

if( $_POST['action'] == 'creer_lot'){
	
	$now = date('YmdHis');
	$now_date = date('Y-m-d');
	$numero_lot_origine = pSQL($_POST['numero_lot_origine']);
	$date_approvisionnement = pSQL($_POST['date_approvisionnement']);
	$numero_lot_LBG = pSQL($_POST['numero_lot_LBG']);
	$commentaire = pSQL($_POST['commentaire']);
	$id_commande = pSQL($_POST['id_commande']);
	$id_product = pSQL($_POST['id_product']);
	$fournisseur = pSQL($_POST['fournisseur']);
	$quantite = pSQL($_POST['qte']);
	$quantite = str_replace(',','.',$quantite); // remplace la notation française pour les décimales
	$quantite = str_replace(' ','',$quantite); // supprime les espaces dûes au masque de saisie
	$unite_achat = pSQL($_POST['unite']);
	$date_germination = pSQL($_POST['date_germination']);
	$pourcentage_germination = pSQL($_POST['pourcentage_germination']);
	
	
	// Vérification de l'unicité du numéro du lot LBG
	$sql = 'SELECT numero_lot_LBG FROM ps_inventaire_lots
	WHERE numero_lot_LBG = "'.$numero_lot_LBG.'"';
	$res = Db::getInstance()->ExecuteS($sql);
	
	if(is_array($res)){
		if(count($res)){
			// Recalcul de numero_lot_LBG pour éviter les doublons
			$sql = 'SELECT numero_lot_LBG FROM ps_inventaire_lots
			WHERE id_product = '.$id_product.'
			ORDER BY `numero_lot_LBG` DESC LIMIT 0,1';
			$res2 = Db::getInstance()->ExecuteS($sql);
			$numero_lot_LBG_2 = $res2[0]['numero_lot_LBG']; 
			
			/*$sql = 'SELECT upc FROM ps_product
			WHERE id_product = '.pSQL($produit['id_produit']);*/
			$sql = 'SELECT upc FROM ps_product
			WHERE id_product = '.$id_product;
			$res3 = Db::getInstance()->ExecuteS($sql);
			$nomenclature = $res3[0]['upc']; 
	
			$annee = substr($numero_lot_LBG_2, -4, 2);
			$increment = substr($numero_lot_LBG_2, -2, 2);
			$cette_annee = date("y");
			$cet_increment = "01";
			if($cette_annee == $annee){
				$cet_increment = (int)$increment + 1;
				$cet_increment = str_pad($cet_increment, 2, '0', STR_PAD_LEFT);
			}
			$numero_lot_LBG = $nomenclature.$cette_annee.$cet_increment;
		}
	}
	
	$declinaisons = get_declinaison($id_product);
	$unite_vente = "";
	foreach ($declinaisons as $dec_prod) {
		if($dec_prod['name'] != 'Non traitée' && $dec_prod['name'] != 'BIO'){
			$dec_prod['name'] = str_replace('Par ', '', $dec_prod['name']);
			$exp = explode(' ', $dec_prod['name']);                   
			if ( strtolower($exp[1]) == 'graines' ){
			  $unite_vente = 'graine';
              break;
			}
			else {
			  $unite_vente = 'gramme';
			}
		}
	}
	
	/*if($unite_achat == "kg"){
		// conversion kg => gramme
		$quantite = $quantite * 1000;
	}
	
	if($unite_vente == 'gramme' && $unite_achat == 'graines'){
		// graine en gramme
		$conversion = conversion($id_product);
		if($conversion != 0){
			$quantite = $quantite / $conversion;
			$quantite = intval($quantite);
		}
	}else if($unite_vente == 'graine' && $unite_achat == 'kg'){
		// cas des tomates
		// kg en graine
		$quantite = $quantite * conversion($id_product);	
	}*/
	
	// insert into table lot
	$sql = 'INSERT INTO ps_inventaire_lots (id_product,fournisseur,numero_lot_origine,date_approvisionnement,numero_lot_LBG,commentaire,quantite,graine_gramme,date_test_germination,percent_germination) 
	VALUES('.$id_product.',"'.$fournisseur.'","'.$numero_lot_origine.'","'.$date_approvisionnement.'","'.$numero_lot_LBG.'","'.$commentaire.'","'.$quantite.'","'.$unite_vente.'","'.$date_germination.'","'.$pourcentage_germination.'")';
	$req = Db::getInstance()->Execute($sql);
	
	// Début - Dorian BERRY-WEB 
	// Mise à jour, 31 mars 2022. Addition du stock tampon
	$sqlV = 'SELECT valeur FROM ps_inventaire
	WHERE id_product = "'.$id_product.'" AND id_product_attribute = 0 ORDER BY id DESC LIMIT 0,1';
	$resV = Db::getInstance()->ExecuteS($sqlV);
	if(isset($resV[0]) && count($resV[0])){
		$quantite += $resV[0]['valeur'];
	}
	// Fin - Dorian BERRY-WEB
	
	$sqlV = 'SELECT id_inventaire_lots FROM ps_inventaire_lots
	WHERE numero_lot_LBG = "'.$numero_lot_LBG.'" AND numero_lot_origine = "'.$numero_lot_origine.'"';
	$resV = Db::getInstance()->ExecuteS($sqlV);

	//insert into table AW_test_lots
	$sql = 'INSERT INTO AW_test_lots (id_lot, date_debut_semis, date_etape_1, resultat_etape_1, date_fin_test, pourcentage_germ, origine_test) 
	VALUES('.$resV[0]['id_inventaire_lots'].',"'.$date_germination.'","'.$date_germination.'","'.$pourcentage_germination.'","'.$date_germination.'","'.$pourcentage_germination.'","Frns")';
	//error_log('$sql : '.$sql);
	$req = Db::getInstance()->Execute($sql);

	// insert into table inventaire
	$sql = 'INSERT INTO ps_inventaire
	(date,id_product,id_product_attribute,valeur,type) 
	VALUES("'.$now.'","'.$id_product.'","0","'.$quantite.'","Approvisionnement")';
	$req = Db::getInstance()->Execute($sql);
	
	// update table cmd_fournisseur_detail
	$sql = 'UPDATE cmd_fournisseur_detail SET lot_cree = 1, id_etat = 6, date_creation = "'.$now_date.'", date_update = "'.$now_date.'" 
	WHERE id_cmd = '.$id_commande.'
	AND id_produit = '.$id_product.'';
	$req = Db::getInstance()->Execute($sql);
 
	//$json['sql'] = $sql; // affichage debug
	// TODO afficher un message d'erreur ou un message de confirmation 
	
	$json['msg'] = 'ok';
	
	echo (json_encode($json));
}


function get_declinaison($id_product){
	$sql = 'SELECT *, sa.quantity as qte, pa.weight as poids FROM `ps_product_attribute` AS pa
	LEFT JOIN `ps_product_attribute_combination` AS pac ON pac.id_product_attribute = pa.id_product_attribute
	LEFT JOIN ps_attribute a ON a.id_attribute = pac.id_attribute
	LEFT JOIN `ps_attribute_lang` AS al ON al.id_attribute = pac.id_attribute
	LEFT JOIN ps_stock_available sa ON sa.id_product_attribute = pa.id_product_attribute
	WHERE pa.id_product = '.$id_product.' AND al.id_lang = 1 ORDER BY pa.default_on DESC, a.position ASC';

	$res = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

	return $res;
}

function conversion($id_product){
	// conversion graines/grammes
	$conversion = 1;
	$sql = 'SELECT value FROM ps_feature_value_lang v
	INNER JOIN ps_feature_product p ON p.id_feature_value = v.id_feature_value
	WHERE p.id_product = '.pSQL($id_product).'
	AND p.id_feature = 17
	AND v.id_lang = 1';
	$res = Db::getInstance()->executeS($sql);
	if(is_array($res)){
		if(count($res)){
			foreach($res as $r){
				if($r['value']){
					$conversion = $r['value'];
				}
			}
		}
	}
	return $conversion;
}



?>
