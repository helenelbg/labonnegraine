<?php
	// Ce script permet de passer les plants en production Bio uniquement lorsque les graines sont Bio.
	/*
	
	Procédure

	Commenter le exit ci-dessous, puis lancer par navigateur :
	> /scripts/plants_bio.php
		
	*/
	
	// Par Dorian, BERRY-WEB, novembre 2023
	
	// exit;
	

ini_set('max_execution_time', '3000'); // 3000 secondes

include("../config/config.inc.php");


$products = Product::getProduitsPlantEnPrecommande();

foreach ($products as $p){
	$id_product = $p['id_product'];
	
	// 53 - production non traité
	// 54 - BIO

	//echo $id_product . ' - ' . $p['name'].'<br>';
	
	$product = new Product($id_product);
	$categories = $product->getCategories();

	$declinaisons = $product->getAttributeCombinations();
	
	/*echo '<pre>';
	print_r($declinaisons);
	echo '</pre>';*/
	
	$bio = false;
	
	foreach($declinaisons as $decl){
		$id_attribute = $decl['id_attribute'];
		if($id_attribute == 54){
			$bio = true;
			break;
		}
	}
	if($bio){
		foreach($declinaisons as $decl){
			$id_product_attribute = (int) $decl['id_product_attribute'];
			$id_attribute = $decl['id_attribute'];
			if($id_attribute == 53){
				$sql = 'UPDATE ps_product_attribute_combination SET id_attribute = 54
				WHERE id_product_attribute = '.$id_product_attribute.'
				AND id_attribute = 53;';
				echo $sql.'<br>';
			}			
		}
	}

	//break;
}




?>
