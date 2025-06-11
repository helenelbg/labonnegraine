<?php

// Ce script permet de supprimer les catégories aux plants.
	
// Par Dorian, BERRY-WEB, janvier 2024

// - il sera lancé à la main après la date de fin de résa
// - les produits dont la 2ème déclinaison seront supprimés de la catégorie "plants & Cie" si la quantité est égale à 0
// - ces produits continueront à être visible ailleurs sur le site,

// Procédure : commenter ou décommenter $plant_categories en fonction des familles souhaitées, puis lancer le script via navigateur.

// https://labonnegraine.com/scripts/plants_et_cie_nettoyage.php

exit;

ini_set('max_execution_time', '3000'); // 3000 secondes

include("../config/config.inc.php");

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

*/

// il n'y a que 3 familles : 
// -aubergines / poivrons / piments
// -tomates
// -cucurbitacées (courges, courgettes, melons, pastèques, concombres, cornichons, pâtissons, potirons et potimarrons)

// -aubergines / poivrons / piments
//$plant_categories = [349, 355];

// -tomates
//$plant_categories = [356];

// -cucurbitacées
$plant_categories = [351, 352, 353, 354, 350];

$products = Product::getProduitsPlantEnPrecommande();

foreach ($products as $p){
	$id_product = (int) $p['id_product'];

	// get declinaisons, et if qty == 0 sur la déclinaison plant 2

	echo $id_product . ' - ' . $p['name'].'<br>';
	
	$sql = 'SELECT pa.id_product_attribute, sa.quantity FROM ps_product_attribute pa
	LEFT JOIN ps_stock_available sa ON sa.id_product_attribute = pa.id_product_attribute
	WHERE pa.id_product = '.$id_product;
	$res = Db::getInstance()->executeS($sql);
	
	$iter_declinaison = 0;
	foreach($res as $product_attribute){
		$id_product_attribute = $product_attribute['id_product_attribute'];
		if(Product::isPlantEnPrecommandebyId($id_product_attribute)){
			$iter_declinaison++;
			if($iter_declinaison == 2){
				$quantity = $product_attribute['quantity'];
				if($quantity == 0){
					// On enlève les catégories
					$product = new Product($id_product);
					$categories = $product->getCategories();

					foreach($categories as $cat){
						if(in_array($cat,$plant_categories)){
							echo 'deleteCategory ' . $cat .'<br>';
							$product->deleteCategory($cat);
							// catégories génériques : 342, 344, 345
							$product->deleteCategory(344);
							$product->deleteCategory(345);
							$product->deleteCategory(342);
						}
					}
				}
			}
		}
	}
	


	//break;
}


?>
