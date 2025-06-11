<?php

// Ce script permet de supprimer des règles panier
	
/*

Procédure

Commenter le exit ci-dessous, puis lancer par navigateur :
> /scripts/nettoyage_cart_rule.php
	
*/

// Par Dorian, BERRY-WEB, décembre 2023

exit;
	
ini_set('memory_limit', '512M');
ini_set('max_execution_time', '10000');
require(dirname(__FILE__).'/../config/config.inc.php');


$cart_rules = [];
$lines = array();
$local_file = 'a.csv'; 
if (($handle = fopen($local_file, "r")) !== FALSE) {
	while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {   
		$lines[] = $data;
	}
	fclose($handle);
}
else{
	exit("Erreur d'ouverture du fichier csv.");
}

foreach ($lines as $line){
	$cart_rules[] = $line[0];
}

//print_r($cart_rules);

foreach($cart_rules as $id_cart_rule){
	$cart_rule = new CartRule($id_cart_rule);
	$res = $cart_rule->delete();
	if($res){
		echo 'Delete cart rule '.$id_cart_rule.' OK<br>';
	}else{
		echo 'Delete cart rule '.$id_cart_rule.' ERROR<br>';
	}
}


