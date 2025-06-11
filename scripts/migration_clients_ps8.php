<?php
	// Ce script permet d'importer les informations newsletters des clients
	
	/*
	
	Procédure

	Depuis la BDD de Prestashop 1.6, export la requête suivante en csv :
	SELECT id_customer, newsletter_cyril, newsletter_bonsplans FROM psme_customer

	Importer le csv sur le ftp.

	Commenter le exit ci-dessous, puis lancer par navigateur :
	> /scripts/migration_clients_ps8.php
		
	*/
	
	// Par Dorian, BERRY-WEB, juin 2023
	
	exit;
	
	include("../config/config.inc.php");
	
	$lines = array();
	$local_file = 'psme_customer.csv'; 
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
		$id_customer = $line[0];
		$newsletter_cyril = $line[1];
		$newsletter_bons_plans = $line[2];
	  
		$sql = 'UPDATE ps_customer SET newsletter='.$newsletter_bons_plans.', optin='.$newsletter_cyril.'  WHERE id_customer = '.$id_customer.';';
		echo $sql . '<br>';
		
		$res = Db::getInstance()->execute($sql);
	}




?>
