<?php

// ce script met à jour la nomenclature des produits

exit;

include_once '../config/config.inc.php';
include_once '../config/settings.inc.php';
include_once '../init.php';

// On récupère les produits
$sql = 'SELECT id_product FROM ps_product;';
$produits = Db::getInstance()->ExecuteS($sql);
		
foreach ($produits as $produit){
	$id_product = pSQL($produit['id_product']);
	// On récupère le numéro du lot LBG
	$sql = 'SELECT numero_lot_LBG FROM ps_inventaire_lots
	WHERE id_product = '.$id_product.'
	ORDER BY `numero_lot_LBG` DESC LIMIT 0,1';
	$res = Db::getInstance()->ExecuteS($sql);
	if(is_array($res)){
		if(count($res)){
			$numero_lot_LBG = $res[0]['numero_lot_LBG']; 
			$nomenclature = substr($numero_lot_LBG, 0, 4);
			if($nomenclature){
				$sql = 'UPDATE ps_product SET upc = "'.pSQL($nomenclature).'" WHERE id_product = '.$id_product.';';
				echo $sql.'<br>';
				//$req = Db::getInstance()->Execute($sql);
			}
		}
	}
}

