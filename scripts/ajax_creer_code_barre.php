<?php

// Ce script crée un nouveau code barre

include("../config/config.inc.php");

$sql = 'SELECT ean13 FROM `ps_product` WHERE `ean13` LIKE "37014502%" ORDER BY `ean13` ASC';
$res1 = Db::getInstance()->executeS($sql);

$sql = 'SELECT ean13 FROM `ps_product_attribute` WHERE `ean13` LIKE "37014502%" ORDER BY `ean13` ASC';
$res2 = Db::getInstance()->executeS($sql);

$barcodes1 = array_column($res1, 'ean13');
$barcodes2 = array_column($res2, 'ean13');
$barcodes3 = array_merge($barcodes1,$barcodes2);
sort($barcodes3);
$barcodes3 = array_unique($barcodes3);

$i=0;
$quatre_int = 0;
foreach($barcodes3 as $barcode){
	//echo 'barcode '.$barcode.'<br>';
	// on récupère les 4 chiffres produit
	$quatre = substr($barcode, -5, 4);
	$quatre_int = (int) $quatre;
	if($quatre_int != $i){
		$pad_i = str_pad($i, 4, "0", STR_PAD_LEFT); 
		echo '37014502'.$pad_i;
		exit;
	}
	$i++;
}

// si pas de trous
$quatre_int++;
$pad_i = str_pad($quatre_int, 4, "0", STR_PAD_LEFT); 
echo $pad_i;
