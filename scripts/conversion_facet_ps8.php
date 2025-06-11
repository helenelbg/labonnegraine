<?php
	// Ce script est un script standalone qui permet de convertir les facets des dates de semis et de récoltes
	// Dans la BDD, au lieu d'avoir une ligne (4,5,6), on a 3 lignes : une ligne Avril, une ligne Mai, une ligne Juin
	
	// Par Dorian, BERRY-WEB, août 2023
	
	// Après exécution du script, il faut reconstruire l'index facet, et vider le cache facet, depuis le module facet du BO Prestashop 8.
	
	exit;
	
	include("../config/config.inc.php");

	// ID de la langue (français)
	$id_lang = 1;
	
	// IDs des facets de date de semis et de date de récolte
	$date_ids = [27,28]; // 27 = date semi, 28 = date récolte
	
	foreach($date_ids as $date_id) {
		// Requête pour récupérer les produits ayant une valeur de facet spécifique
		$sql = 'SELECT id_feature, id_product, id_feature_value FROM ps_feature_product WHERE id_feature = '.$date_id.' AND id_feature_value > 2436';
		$res = Db::getInstance()->executeS($sql);
		
		// Parcours des résultats de la requête
		foreach($res as $line) {
			$id_feature = $line['id_feature'];
			$id_product = $line['id_product'];
			$id_feature_value = $line['id_feature_value'];
			
			// Requête pour obtenir la valeur de facet
			$sql = 'SELECT value FROM ps_feature_value_lang WHERE id_feature_value = '.$id_feature_value.' AND id_lang = '.$id_lang;
			$values = Db::getInstance()->executeS($sql);
			if(is_array($values) && count($values)) {
				$value = $values[0]['value'];
				$months = explode(',',$value);
				
				// Parcours des mois dans la valeur de facet
				foreach($months as $month){
					if($month){
						$month = (int)$month;
						if(is_numeric($month)){
							// Calcul de la nouvelle valeur à insérer
							$val = 2412 + $month + 12 * (28 - $date_id);
							
							// Insertion de la nouvelle valeur de facet pour le produit
							$sql = 'INSERT INTO ps_feature_product (id_feature, id_product, id_feature_value ) VALUES('.$id_feature.', '.$id_product.', '.$val.')';
							echo $sql.'<br>';
							Db::getInstance()->execute($sql);
							
							// Suppression de l'ancienne valeur de facet
							$sql = 'DELETE FROM ps_feature_value_lang WHERE id_feature_value = '.$id_feature_value.' AND id_lang = '.$id_lang;
							Db::getInstance()->execute($sql);
						}
					}
				}
			}				
		}
		
		// Suppression des anciennes valeurs de facet
		$sql = 'DELETE FROM ps_feature_product WHERE id_feature = '.$date_id.' AND id_feature_value > 2436';
		Db::getInstance()->execute($sql);
	}

?>
