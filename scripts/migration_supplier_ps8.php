<?php
	// Ce script permet d'importer les suppliers des produits
	
	/*
	
	Procédure

	Depuis la BDD de Prestashop 1.6, export la requête suivante en csv :
	SELECT id_product, id_supplier FROM psme_product

	Importer le csv sur le ftp.

	Commenter le exit ci-dessous, puis lancer par navigateur :
	> /scripts/migration_supplier_ps8.php
		
	*/
	
	// Par Dorian, BERRY-WEB, juin 2023
	
	exit;
	
	include("../config/config.inc.php");
	
	$lines = array();
	$local_file = 'psme_product.csv'; 
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
		$id_product = (int)$line[0];
		$id_supplier = (int)$line[1];

		// On ne met pas à jour si l'id_supplier est déjà présent
		
		$sql = 'UPDATE ps_product SET 
		id_supplier="'.$id_supplier.'" 
		WHERE id_product = '.$id_product.'
		AND id_supplier = 0;';
		echo $sql . '<br>';
		$res = Db::getInstance()->execute($sql);

	}


?>
