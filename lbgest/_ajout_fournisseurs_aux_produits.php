<?php

// Ce script permet de lier chaque produit à un ou plusieurs fournisseurs à partir d'un fichier csv.
// Par Dorian BERRY-WEB, le 25 novembre 2021


// die;



include_once '../config/config.inc.php';

include_once '../config/settings.inc.php';

include_once '../init.php';



$lines = array();
$local_file = '_liste_article_par_fournisseur.csv'; 
if (($handle = fopen($local_file, "r")) !== FALSE) {
	while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {		
		$lines[] = $data;
	}
	fclose($handle);
}
else{
	die();
}

if(!is_array($lines)){
	die();	
}

if(!count($lines)){
	die();	
}

$fournisseurs = $lines[0];
$fournisseurs_id_liste = array();
$products = array();

$res1 = Db::getInstance()->ExecuteS('SELECT id_supplier, name FROM ps_supplier');
if(is_array($res1)){
	if(count($res1)){
		$fournisseurs_bdd = [];
		foreach ($res1 as $r){ // à noter que ça serait plus rapide en PDO avec fetchAll(\PDO::FETCH_GROUP|\PDO::FETCH_UNIQUE);
			$name = $r['name'];
			$id_supplier = $r['id_supplier'];
			$fournisseurs_bdd[$name] = $id_supplier;
		}
		for($i = 2; $i < count($fournisseurs); $i++){
			$name = $fournisseurs[$i];
			$id_supplier = $fournisseurs_bdd[$name];
			$fournisseurs_id_liste[$i] = $id_supplier;
		}
	}
}			

$res2 = Db::getInstance()->ExecuteS('SELECT id_product, reference FROM ps_product WHERE active = 1');
if(is_array($res2)){
	if(count($res2)){
		foreach ($res2 as $r){ 
			$reference = $r['reference'];
			$id_product = $r['id_product'];
			$products[$reference] = $id_product;
		}
	}
}		

array_shift($lines);

foreach ($lines as $line){ // on parcourt tous nos produits
	$reference = $line[0];
	$name = $line[1];
	$id_product = $products[$reference];
	for($i = 2; $i < count($line); $i++){
		if($line[$i] == 'x'){
			$id_supplier = $fournisseurs_id_liste[$i];
			if($id_product && $id_supplier){
				$sql = 'INSERT IGNORE INTO ps_product_supplier (id_product, id_supplier) VALUES ('.$id_product.','.$id_supplier.');';
				echo $sql.'<br>';
			}
		}
	}
}

// ------------------------------
// fournisseur par défaut
// ------------------------------ 

foreach ($lines as $line){ // on parcourt tous nos produits
	$reference = $line[0];
	$name = $line[1];
	$id_product = $products[$reference];
	$res3 = Db::getInstance()->ExecuteS('SELECT s.id_supplier FROM ps_inventaire_lots l
		  INNER JOIN ps_supplier s ON s.name = l.fournisseur
		  WHERE id_product = "'.$id_product.'" GROUP BY fournisseur ORDER BY date_approvisionnement DESC;');
		  
	if(is_array($res3)){
		if(count($res3)){
			$id_supplier = $res3[0]['id_supplier'];
			$sql = 'UPDATE ps_product SET id_supplier = '.$id_supplier.' WHERE id_product = '.$id_product.';';
			//echo $sql.'<br>';
		}
	}		

}



     
	 


				