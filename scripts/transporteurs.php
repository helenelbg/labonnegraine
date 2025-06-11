<?php		
	include("../config/config.inc.php");

	$id_carrier_reference = 390; // l'id reference du transporteur Lettre verte
	$id_shop = 1;

	/*$sql = 'SELECT DISTINCT c.id_product, p.reference FROM ps_product p
	LEFT JOIN ps_product_carrier c ON c.id_product = p.id_product 
	WHERE p.reference LIKE "0-%" 
	OR p.reference LIKE "1-%" 
	OR p.reference LIKE "2-%" ';*/
	$sql = 'SELECT DISTINCT id_product FROM ps_product
	WHERE id_product <> 3128 AND id_product NOT IN (SELECT id_product FROM ps_category_product WHERE id_category IN (273,274,275,276,277,279,278,280,227))';
	$res = Db::getInstance()->executeS($sql);

    foreach($res as $product) {
		$id_product = $product['id_product'];
		if($id_product){
			$sql = 'INSERT IGNORE INTO ps_product_carrier (id_product, id_carrier_reference, id_shop) 
			VALUES ('.$id_product.','.$id_carrier_reference.','.$id_shop.');';
			echo $sql . '<br>';
			//$res = Db::getInstance()->execute($sql);

			/*if($reference[0] == '0'){
				$num = $reference[2].$reference[3].$reference[4];
				$num = (int) $num;
				if($num < 300 || $num >= 450){
					//echo $reference . '<br>';
					$sql = 'DELETE FROM ps_product_carrier 
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