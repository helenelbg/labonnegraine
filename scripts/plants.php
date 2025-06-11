<?php
	// Ce script permet d'ajouter les déclinaisons des plants
		
	/*
	
	Procédure

	Commenter le exit ci-dessous, puis lancer par navigateur :
	> /scripts/plants.php
		
	*/
	
	// Par Dorian, BERRY-WEB, novembre 2023
	
	// exit;
	

ini_set('max_execution_time', '3000'); // 3000 secondes

include("../config/config.inc.php");

//$id_attribute_plant_1 = 10500; // php8
//$id_attribute_plant_2 = 10502; // php8

//$id_attribute_plant_1 = 10512; // www
//$id_attribute_plant_2 = 10513; // www

$categories = [21,45,46,62,69,71,77,195];
/*
21	Aubergines
45	Courges-potirons
46	Courgettes
62	Melons/Pastèques
69	Pâtissons
71	Piments/poivrons
77	Tomates
195	Concombres/cornichons
*/

$products = getProductsByCategories($categories);

foreach ($products as $p){
	$id_product = $p['id_product'];
	
	// ids spéciaux des packs
	$skip = [2901, 2918, 2919, 3050, 3114];
	if(in_array($id_product, $skip)){
		continue;
	}
	
	/*if($id_product != 14){
		continue;
	}*/
	
	// 53 - production non traité
	// 54 - BIO
	// 10501 - godet de 7 cm - dev php8
	// 10514 - godet de 7 cm - www
	
	echo $id_product . ' - ' . $p['name'].'<br>';
	$product = new Product($id_product);
	
	// PLANT AVANT PRECOMMANDE
	$combinationId = getIdProductAttributeByIdAttributes($id_product, [10512,10514,53]);

	if (!$combinationId) {
		$combination = new Combination();
		$combination->id_product = $id_product;
		$combination->quantity = 99999;
		$combination->price = 1.895735;
		$combination->add();
		$combination->setAttributes([10512,10514,53]);
		$product->update();
		$combinationId = getIdProductAttributeByIdAttributes($id_product, [10512,10514,53]);
		StockAvailable::setQuantity($id_product, $combinationId, 99999);
	} else {
		StockAvailable::setQuantity($id_product, $combinationId, 99999);
	}
	

	// PLANT APRES PRECOMMANDE
	$combinationId = getIdProductAttributeByIdAttributes($id_product, [10513,10514,53]);

	if (!$combinationId) {
		$combination = new Combination();
		$combination->id_product = $id_product;
		$combination->quantity = 0;
		$combination->price = 2.369668;
		$combination->add();
		$combination->setAttributes([10513,10514,53]);
		$product->update();
		$combinationId = getIdProductAttributeByIdAttributes($id_product, [10513,10514,53]);
		StockAvailable::setQuantity($id_product, $combinationId, 0);
	} else {
		StockAvailable::setQuantity($id_product, $combinationId, 0);
	}
	

	
}

function getProductsByCategories($categories) {

    $categories_list = implode(',', array_map('intval', $categories));

    $sql = "SELECT p.*, pl.*
            FROM `" . _DB_PREFIX_ . "product` p
            LEFT JOIN `" . _DB_PREFIX_ . "product_lang` pl ON (p.`id_product` = pl.`id_product`)
            LEFT JOIN `" . _DB_PREFIX_ . "category_product` cp ON (p.`id_product` = cp.`id_product`)
            WHERE cp.`id_category` IN ($categories_list)
            GROUP BY p.`id_product`";

    $products = Db::getInstance()->executeS($sql);

    return $products;
	
}

function getIdProductAttributeByIdAttributes($idProduct, $idAttributes, $findBest = false)
{
	$idProduct = (int) $idProduct;

	if (!is_array($idAttributes) && is_numeric($idAttributes)) {
		$idAttributes = [(int) $idAttributes];
	}

	if (!is_array($idAttributes) || empty($idAttributes)) {
		throw new PrestaShopException(sprintf('Invalid parameter $idAttributes with value: "%s"', print_r($idAttributes, true)));
	}

	$idAttributesImploded = implode(',', array_map('intval', $idAttributes));
	$idProductAttribute = Db::getInstance()->getValue(
		'SELECT pac.`id_product_attribute`
			FROM `' . _DB_PREFIX_ . 'product_attribute_combination` pac
			INNER JOIN `' . _DB_PREFIX_ . 'product_attribute` pa ON pa.id_product_attribute = pac.id_product_attribute
			WHERE pa.id_product = ' . $idProduct . '
			AND pac.id_attribute IN (' . $idAttributesImploded . ')
			GROUP BY pac.`id_product_attribute`
			HAVING COUNT(pa.id_product) = ' . count($idAttributes)
	);

	if ($idProductAttribute === false && $findBest) {
		//find the best possible combination
		//first we order $idAttributes by the group position
		$orderred = [];
		$result = Db::getInstance()->executeS(
			'SELECT a.`id_attribute`
			FROM `' . _DB_PREFIX_ . 'attribute` a
			INNER JOIN `' . _DB_PREFIX_ . 'attribute_group` g ON a.`id_attribute_group` = g.`id_attribute_group`
			WHERE a.`id_attribute` IN (' . $idAttributesImploded . ')
			ORDER BY g.`position` ASC'
		);

		foreach ($result as $row) {
			$orderred[] = $row['id_attribute'];
		}

		while ($idProductAttribute === false && count($orderred) > 1) {
			array_pop($orderred);
			$idProductAttribute = Db::getInstance()->getValue(
				'SELECT pac.`id_product_attribute`
				FROM `' . _DB_PREFIX_ . 'product_attribute_combination` pac
				INNER JOIN `' . _DB_PREFIX_ . 'product_attribute` pa ON pa.id_product_attribute = pac.id_product_attribute
				WHERE pa.id_product = ' . (int) $idProduct . '
				AND pac.id_attribute IN (' . implode(',', array_map('intval', $orderred)) . ')
				GROUP BY pac.id_product_attribute
				HAVING COUNT(pa.id_product) = ' . count($orderred)
			);
		}
	}

	if (empty($idProductAttribute)) {
		return false;
	}

	return (int) $idProductAttribute;
}



?>
