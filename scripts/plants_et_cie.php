<?php

// Ce script permet d'ajouter des catégories aux plants.
	
/*

Procédure

Commenter le exit ci-dessous, puis lancer par navigateur :
> /scripts/plants_et_cie.php
	
*/

// Par Dorian, BERRY-WEB, décembre 2023

//exit;
	

ini_set('max_execution_time', '3000'); // 3000 secondes

include("../config/config.inc.php");

$debug = true;

/*

dev + prod 

345 Plants et cie
	344 Plants potagers
		349	Aubergines🌱	
		350	Concombres - Cornichons 🌱	
		351	Courges - Potirons - Potimarrons 🌱	
		352	Courgettes 🌱	
		353	Melons - Pastèques 🌱	
		354	Pâtissons 🌱	
		355	Piments - Poivrons 🌱		
		356	Tomates 🌱	
	346 Plants potagers greffés
	347 Plants d'aromatiques vivaces
	348 Plants de petits fruits

377 cassis/casseilles
378 fraisiers
379 framboisiers
380 goji
381 groseilliers grappes
382 groseilliers maquereaux
383 mures
384 myrtilliers
385 exotiques/originaux

*/

// potagers
$plant_categories = [
	21 => 349,
	45 => 351,
	46 => 352,
	62 => 353,
	69 => 354,
	71 => 355,
	77 => 356,
	195 => 350,
];

/*$products = Product::getProduitsPlantEnPrecommande();
foreach ($products as $p){
	$id_product = $p['id_product'];
			
	// ids spéciaux des packs
	$skip = [2901, 2918, 2919, 3050, 3114];
	if(in_array($id_product, $skip)){
		continue;
	}

	echo $id_product . ' - ' . $p['name'] . ' ';
	$product = new Product($id_product);
	$categories = $product->getCategories();

	// potagers
	$b_newcat = false;
	foreach($categories as $cat){
		if(isset($plant_categories[$cat])){
			$id_newcat = $plant_categories[$cat];
			$b_newcat = true;
			echo $id_newcat .' ';
			if(!$debug){
				$product->addToCategories($id_newcat);
			}
		}
	}
	if($b_newcat){
		echo '345 ';
		echo '344 ';
		if(!$debug){
			$product->addToCategories(345);
			$product->addToCategories(344);
		}
	}
}*/


$fraisiers = [
	958,1127,1153,1148,1152,1150,1151,1294,1145,1293,1296,1149,1147,1154,1343,1342,1978,1977,2204,2205,2207,2206,2203,2595,2596,2933,2934,2935,2936,3109,3110,3111,3112,3113,1126,959,1626,1115,1113,1971,1114,2363,956,957,960,1969,2199,2914,1247,1970,1961,1962,2913,2573,2920,2921,2361,2189,2190,2188,2187,2191,2192,3062,3064,2577,3066,3068,3070,3071,3072
];

$framboisiers = [
	1344,1058,1048,1057,1056,1051,1049,1050,1824,1052,1059,1053,1063,1060,1061,1054,1062,3259,3260,3261,3262,3263,3258,3264,3265,3266,3268,3269,3267
];

$groseilliers_grappes = [
	1068,1064,1065,1066,1067,3487,3488,3277,3270,3272,3273,3275,3271,3274
];

$groseilliers_maquereaux = [
	1072,1069,1070,1071,3278,3279,3280,3281,3282,3283
];

$mures = [
	1075,1073,1074,1077,1076,3291,3292,3293,3294,3295
];

$exotiques = [
	1338,1339,1340,1345,1346,1348,1349,1350,3372,1353,1352,3373,1355,1462,1463,1464,1207,1466,1467,1465,2380,1347,3489
];

foreach($fraisiers as $id_product){
	echo $id_product . ' ';
	$product = new Product($id_product);
	echo '345 ';
	echo '348 ';
	echo '378 ';
	if(!$debug){
		$product->addToCategories(345);
		$product->addToCategories(348);
		$product->addToCategories(378);
	}
	echo '<br>';
}

foreach($framboisiers as $id_product){
	echo $id_product . ' ';
	$product = new Product($id_product);
	echo '345 ';
	echo '348 ';
	echo '379 ';
	if(!$debug){
		$product->addToCategories(345);
		$product->addToCategories(348);
		$product->addToCategories(379);
	}
	echo '<br>';
}

foreach($groseilliers_grappes as $id_product){
	echo $id_product . ' ';
	$product = new Product($id_product);
	echo '345 ';
	echo '348 ';
	echo '381 ';
	if(!$debug){
		$product->addToCategories(345);
		$product->addToCategories(348);
		$product->addToCategories(381);
	}
	echo '<br>';
}

foreach($groseilliers_maquereaux as $id_product){
	echo $id_product . ' ';
	$product = new Product($id_product);
	echo '345 ';
	echo '348 ';
	echo '382 ';
	if(!$debug){
		$product->addToCategories(345);
		$product->addToCategories(348);
		$product->addToCategories(382);
	}
	echo '<br>';
}

foreach($mures as $id_product){
	echo $id_product . ' ';
	$product = new Product($id_product);
	echo '345 ';
	echo '348 ';
	echo '383 ';
	if(!$debug){
		$product->addToCategories(345);
		$product->addToCategories(348);
		$product->addToCategories(383);
	}
	echo '<br>';
}

foreach($exotiques as $id_product){
	echo $id_product . ' ';
	$product = new Product($id_product);
	echo '345 ';
	echo '348 ';
	echo '385 ';
	if(!$debug){
		$product->addToCategories(345);
		$product->addToCategories(348);
		$product->addToCategories(385);
	}
	echo '<br>';
}



?>
