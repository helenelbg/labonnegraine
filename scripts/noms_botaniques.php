<?php

include("../config/config.inc.php");

$sql = "SELECT p.id_product, pl.description 
FROM ps_product p
LEFT JOIN ps_product_lang pl ON pl.id_product = p.id_product 
WHERE pl.id_lang = 1";
$products = Db::getInstance()->executeS($sql);

foreach ($products as $product){
    $id_product = $product["id_product"];
	$description = $product["description"];

	//if($id_product != 418) continue;
	
	//echo $id_product."<br>";
	//echo $description."<br>";
	
	if (preg_match('/Nom botanique( ?)\<\/strong\>(.*?)\</', $description, $matches)) {
		$botanic_name = $matches[2];
		if($botanic_name){
			$botanic_name = preg_replace('/^\p{Z}+|\p{Z}+$/u', '', $botanic_name);
			$botanic_name = str_replace(':','',$botanic_name);
			$botanic_name = rtrim($botanic_name,'.');
			$botanic_name = trim($botanic_name);
			//echo $botanic_name.'<br>';
			
			$sql = 'UPDATE ps_product SET botanic_name = "'.$botanic_name.'" WHERE id_product = '.$id_product.';';
			echo $sql.'<br>';
		}
		
	}		

}

