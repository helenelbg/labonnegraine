<?php
	// Ce script permet d'importer les stocks de produits
		
	/*
	
	Procédure

	Depuis la BDD de Prestashop 1.6, export la requête suivante en csv :
	SELECT id_product, id_product_attribute, quantity FROM psme_stock_available

	Importer le csv sur le ftp.

	Commenter le exit ci-dessous, puis lancer par navigateur :
	> /scripts/migration_stock_ps8.php
		
	*/
	
	// Par Dorian, BERRY-WEB, septembre 2023
	
	exit;
	
	
	ini_set('max_execution_time', '3000'); // 3000 secondes

	include("../config/config.inc.php");
	
	$lines = array();
	$local_file = 'psme_stock_available.csv'; 
	if (($handle = fopen($local_file, "r")) !== FALSE) {
		while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {   
			$lines[] = $data;
		}
		fclose($handle);
	}
	else{
		exit("Erreur d'ouverture du fichier csv.");
	}

	array_shift($lines); // skip header
 
	foreach ($lines as $line){
		$id_product = $line[0];
		$id_product_attribute = $line[1];
		$quantity = $line[2];
		$id_shop = 1;
		$add_movement = false;
		
		echo $id_product . ', '. $id_product_attribute. ', '.$quantity.'<br>';
	  
		StockAvailable::setQuantity($id_product, $id_product_attribute, $quantity, $id_shop, $add_movement);
	}

echo 'ok';


?>
