<?php
	// Ce script permet d'importer les informations produits
	
	/*
	
	Procédure

	Depuis la BDD de Prestashop 1.6, export la requête suivante en csv :
	SELECT id_product, botanic_name, titre_plus, contenu_plus, reference FROM psme_product

	Importer le csv sur le ftp.

	Commenter le exit ci-dessous, puis lancer par navigateur :
	> /scripts/migration_product_ps8.php
		
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
		$id_product = $line[0];
		$botanic_name = cleanstr($line[1]);
		$titre_plus = cleanstr($line[2]);
		$contenu_plus = cleanstr($line[3]);
		$reference = cleanstr($line[4]);

		$sql = 'UPDATE ps_product SET 
		botanic_name="'.$botanic_name.'", 
		titre_plus="'.$titre_plus.'", 
		contenu_plus="'.$contenu_plus.'", 
		reference="'.$reference.'" 
		WHERE id_product = '.$id_product.';';
		echo $sql . '<br>';
		
		$res = Db::getInstance()->execute($sql);
	}

	function cleanstr($str){
		$str = str_replace("RETOURCHARIOT","\r\n",$str);
		$str = str_replace("SAUTDELIGNE","\n",$str);
		$str = pSQL($str);
		return $str;
	}



?>
