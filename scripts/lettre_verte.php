<?php
	// Ce script permet de lier les produits au transporteur Lettre verte
	// Par Dorian, BERRY-WEB, février 2023
	
	exit;
	
	include("../config/config.inc.php");

	$id_carrier_reference = 354; // l'id reference du transporteur Lettre verte
	$id_shop = 1;

	/*$sql = 'SELECT DISTINCT c.id_product, p.reference FROM psme_product p
	LEFT JOIN psme_product_carrier c ON c.id_product = p.id_product 
	WHERE p.reference LIKE "0-%" 
	OR p.reference LIKE "1-%" 
	OR p.reference LIKE "2-%" ';*/
	$sql = 'SELECT DISTINCT c.id_product, p.reference FROM psme_product p
	LEFT JOIN psme_product_carrier c ON c.id_product = p.id_product 
	WHERE p.reference LIKE "4-%" ';
	$res = Db::getInstance()->executeS($sql);

    foreach($res as $product) {
		$id_product = $product['id_product'];
		$reference = $product['reference'];
		if($id_product){
			$sql = 'INSERT IGNORE INTO psme_product_carrier (id_product, id_carrier_reference, id_shop) 
			VALUES ('.$id_product.','.$id_carrier_reference.','.$id_shop.');';
			echo $sql . '<br>';

			/*if($reference[0] == '0'){
				$num = $reference[2].$reference[3].$reference[4];
				$num = (int) $num;
				if($num < 300 || $num >= 450){
					//echo $reference . '<br>';
					$sql = 'DELETE FROM psme_product_carrier 
					WHERE id_product = '.$id_product.' 
					AND	id_carrier_reference = '.$id_carrier_reference.' 
					AND	id_shop = '.$id_shop.' 
					;';
					echo $sql . '<br>';
				}
			}*/
		}
    }

?>
