<?php
	// Ce script permet d'importer les informations produits
	
	/*
	
	Procédure

	Depuis la BDD de Prestashop 1.6, export la requête suivante en csv :
	SELECT id_product, description, description_short, link_rewrite, meta_description, meta_keywords, meta_title, name, info_sup FROM psme_product_lang WHERE id_lang = 2

	Importer le csv sur le ftp.

	Commenter le exit ci-dessous, puis lancer par navigateur :
	> /scripts/migration_clients_ps8.php
		
	*/
	
	// Par Dorian, BERRY-WEB, juin 2023
	
	exit;
	
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
	
	include_once '../config/config.inc.php';
	include_once '../config/settings.inc.php';
	include_once '../init.php';

	try {
		   $bdd = new PDO("mysql:host=" . _DB_SERVER_ . ";dbname=" . _DB_NAME_, _DB_USER_, _DB_PASSWD_);
	} catch (exeption $ex) {
		   die("probleme connexion serveur" . $ex->getMessage());
	}
	
	$lines = array();
	$local_file = 'psme_product_lang.csv'; 
	if (($handle = fopen($local_file, "r")) !== FALSE) {
		while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {   
			$lines[] = $data;
		}
		fclose($handle);
	}
	else{
		exit("Erreur d'ouverture du fichier csv.");
	}
	
	array_shift($lines); // skip header

	foreach ($lines as $line){
		$id_product = $line[0];
		$description = cleanstr($line[1]);
		$description_short = cleanstr($line[2]);
		$link_rewrite = cleanstr($line[3]);
		$meta_description = cleanstr($line[4]);
		$meta_keywords = cleanstr($line[5]);
		$meta_title = cleanstr($line[6]);
		$name = cleanstr($line[7]);
		$info_sup = cleanstr($line[8]);

		//echo '<pre>';
		//print_r($line);
		//echo '</pre>';

		if($id_product && is_numeric($id_product)){
			/*$sql = 'UPDATE ps_product_lang SET 
			description="'.$description.'",
			description_short="'.$description_short.'",
			meta_description="'.$meta_description.'",
			meta_keywords="'.$meta_keywords.'",
			meta_title="'.$meta_title.'",
			name="'.$name.'",
			info_sup="'.$info_sup.'"
			WHERE id_product = '.$id_product.';';
			
			echo $sql . '<br>';
			
			//$res = Db::getInstance()->execute($sql);
			*/
			
			$sql = 'UPDATE ps_product_lang SET 
			description=?,
			description_short=?,
			meta_description=?,
			meta_keywords=?,
			meta_title=?,
			name=?,
			info_sup=?
			WHERE id_product = ?;';

			$query = $bdd->prepare($sql);
			$query->execute([$description, $description_short, $meta_description, $meta_keywords, $meta_title, $name, $info_sup, $id_product]);
						
			
		}
	}

	function cleanstr($str){
		$str = str_replace("RETOURCHARIOT","\r\n",$str);
		$str = str_replace("SAUTDELIGNE","\n",$str);
		//$str = str_replace('"','\"',$str);
		//$str = pSQL($str);
		return $str;
	}

	echo 'ok';

?>
