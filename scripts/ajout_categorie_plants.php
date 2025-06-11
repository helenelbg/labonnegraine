<?php

// Ce script permet d'ajouter des catégories et des mots clés aux plants.
	
/*

Procédure

Commenter le exit ci-dessous, puis lancer par navigateur :
> /scripts/ajout_categorie_plants.php
	
*/

// Par Dorian, BERRY-WEB, décembre 2023

exit;
	

ini_set('max_execution_time', '3000'); // 3000 secondes

include("../config/config.inc.php");

$id_lang = 1;
$id_category = 0;
if($_SERVER['HTTP_HOST'] == "php8.labonnegraine.com"){
	$id_category = 338;
}elseif($_SERVER['HTTP_HOST'] == "dev.labonnegraine.com"){
	$id_category = 342;
}


$plant_categories = [
	21 => "aubergines",
	45 => "courges-potirons",
	46 => "courgettes",
	62 => "melons-pastèques",
	69 => "pâtissons",
	71 => "piments-poivrons",
	77 => "tomates",
	195 => "concombres-cornichons",
];

$products = Product::getProduitsPlantEnPrecommande();

if($id_category){
	foreach ($products as $p){
		$id_product = $p['id_product'];
				
		// ids spéciaux des packs
		$skip = [2901, 2918, 2919, 3050, 3114];
		if(in_array($id_product, $skip)){
			continue;
		}
	
		echo $id_product . ' - ' . $p['name'].'<br>';
		$product = new Product($id_product);
		$categories = $product->getCategories();
		foreach($categories as $cat){
			if(isset($plant_categories[$cat])){
				$tagName = 'plants '.$plant_categories[$cat];
				$tags = Tag::getProductTags($id_product);
				// Si le tag n'est pas présent, on l'ajoute
				if(isset($tags[$id_lang])){
					if(!in_array($tagName,$tags[$id_lang])){
						Tag::addTags($id_lang, $id_product, $tagName);
					}
				}else{
					echo 'erreur tag : id_product '.$id_product.'<br>';
					Tag::addTags($id_lang, $id_product, $tagName);
				}
				break;
			}
		}

		$product->addToCategories($id_category);
		$product->update();

	}
}





?>
