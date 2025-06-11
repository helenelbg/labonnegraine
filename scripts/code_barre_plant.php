<?php

// Ce script crée un nouveau code barre pour chaque produit plant qui n'a pas de code barre, sur la 2ème déclinaison uniquement

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

$new_barcodes = [];
for($i=0; $i<10000; $i++){
	$pad_i = str_pad($i, 4, "0", STR_PAD_LEFT); 
	$barcode = '37014502'.$pad_i;
	
	$last_digit = checkdigit($barcode);
	
	$barcode .= $last_digit;
	
	if(!in_array($barcode,$barcodes3)){
		$new_barcodes[] = $barcode;
	}
}

$fp = fopen('a.csv', 'w+');

$products = Product::getProduitsPlantEnPrecommande();

foreach ($products as $p){
	$id_product = (int) $p['id_product'];
	
	if(!$p['active']){
		continue;
	}
	
	//echo $id_product . ' - ' . $p['name'].'<br>';
	
	$sql = 'SELECT id_product_attribute FROM ps_product_attribute
	WHERE id_product = '.$id_product;
	$res = Db::getInstance()->executeS($sql);
	
	$iter_declinaison = 0;
	foreach($res as $product_attribute){
		$id_product_attribute = $product_attribute['id_product_attribute'];
		if(Product::isPlantEnPrecommandebyId($id_product_attribute)){
			$iter_declinaison++;
			if($iter_declinaison == 2){
			
				$ean13 = array_shift($new_barcodes);
				
				$sql = 'SELECT ean13 FROM ps_product_attribute 
				WHERE id_product_attribute = '.(int) $id_product_attribute;
				$res2 = Db::getInstance()->executeS($sql);
				
				if(is_array($res2) && count($res2)){
					if(!$res2[0]['ean13']){
					
						$sql = 'UPDATE ps_product_attribute SET ean13 = "'.(int) $ean13.'"  
						WHERE ean13 = "" AND id_product_attribute = '.(int) $id_product_attribute.';';
						
						echo $sql.'<br>';
						
						$ref = $p['reference'];
						$name = $p['name'];

						//$line = [$id_product_attribute,$ref,$name,$ean13];
						$line = [$ref,$name,$ean13];

						fputcsv($fp, $line, ',');
						
						//$res3 = Db::getInstance()->execute($sql);
					}
				}

				
			}
		}
	}

	//break;
}

fclose($fp);


function checkdigit($input) {
    $array = str_split(strrev($input));

    $total = 0;
    $i = 1;
    foreach ($array as $number) {
        $number = intval($number);
        if ($i % 2 === 0) {
            $total = $total + $number;
        } else {
            $total = $total + ($number * 3);
        }
        $i++;
    }

    $res = (ceil($total / 10) * 10) - $total;
    return $res;
}
