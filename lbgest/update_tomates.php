<?php

exit;

// ce script met à jour la conversion graine / gramme des tomates à 250

include_once '../config/config.inc.php';
include_once '../config/settings.inc.php';
include_once '../init.php';

// On récupère les produits
$sql = 'SELECT id_product FROM ps_product_lang WHERE name LIKE "%tomate%" and id_lang = 1;';
$produits = Db::getInstance()->ExecuteS($sql);
		
foreach ($produits as $produit){
	$id_product = pSQL($produit['id_product']);

	$sql = 'UPDATE ps_feature_product SET id_feature_value = "112" WHERE id_product = '.$id_product.' AND id_feature = 17;';
	echo $sql.'<br>';

}
