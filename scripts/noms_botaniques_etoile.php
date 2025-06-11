<?php

	include("../config/config.inc.php");
	
	$lines = array();
	$local_file = 'ps_product.csv'; 
	if (($handle = fopen($local_file, "r")) !== FALSE) {
		while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {   
			$lines[] = $data;
		}
		fclose($handle);
	}
	else{
		exit("Erreur d'ouverture du fichier csv.");
	}
	
	//array_shift($lines); // skip header

	foreach ($lines as $line){
		$id_product = $line[0];
		$botanic_name = $line[2];
	  
		$sql = 'UPDATE ps_product SET botanic_name="'.$botanic_name.'" WHERE id_product = '.$id_product.';';
		echo $sql . '<br>';
		
		//$res = Db::getInstance()->execute($sql);
	}




?>
