<?php

if ( $_GET['token'] != 'hdf6dfdfs6ddgs' )
{
    die;
}

include_once '../config/config.inc.php';
include_once '../config/settings.inc.php';
include_once '../init.php';

try {
	$bdd = new PDO("mysql:host=" . _DB_SERVER_ . ";dbname=" . _DB_NAME_, _DB_USER_, _DB_PASSWD_);
} catch (exeption $ex) {
	die("probleme connexion serveur" . $ex->getMessage());
}

// Dans l’idée générale, le but est de pouvoir croiser, trier et comprendre ce qui se vend (ou pas), suivant les déclinaisons.

if (isset($_POST['submit_export'])) {
	
	$date_debut = $_POST['date_debut'];
	if(!$date_debut){
		$date_debut = date('Y-m-d');
	}
	$date_debut .= ' 00:00:00';
	$date_debut = pSQL($date_debut);
	
	$date_fin = $_POST['date_fin'];
	if(!$date_fin){
		$date_fin = '9999-12-31';
	}
	$date_fin .= ' 23:59:59';
	$date_fin = pSQL($date_fin);

	// Catégories
	
	$id_category = (int) $_POST['id_category'];
	$join_category = '';
	$where_category = '';
	if($id_category){
		$join_category = ' LEFT JOIN `' . _DB_PREFIX_ . 'category_product` ca ON ca.id_product = od.product_id ';
		$where_category = ' AND ca.id_category = ' . $id_category . ' '; 
	}
	 
	// Produit
	
	$id_product = (int) $_POST['id_product'];
	$where_product = '';
	if($id_product){
		$where_product = ' AND od.product_id = ' . $id_product . ' '; 
	}
	
	// Checkboxes
	
	$selects = ['od.product_id'];
	$header_csv = [];
	if(isset($_POST['checkbox_ref'])){
		$selects[] = 'od.product_reference';
		$header_csv[] = 'Référence';
	}
	if(isset($_POST['checkbox_nom'])){
		$selects[] = 'SUBSTRING_INDEX(od.product_name,\' - \',1) as nom';
		$header_csv[] = 'Nom';
		
		$selects[] = 'REPLACE(od.product_name, CONCAT(SUBSTRING_INDEX(od.product_name,\' - \',1), " - "), "") as declinaison';
		$header_csv[] = 'Déclinaison';
	}
	if(isset($_POST['checkbox_quantite'])){
		$selects[] = 'od.product_quantity';
		$header_csv[] = 'Quantité';
	}
	//if(isset($_POST['checkbox_commande'])){
		$selects[] = 'od.id_order';
		$header_csv[] = 'ID Commande';
	//}
	if(isset($_POST['checkbox_codepostal'])){
		$selects[] = 'a.postcode';
		$header_csv[] = 'Code postal';
	}
	if(isset($_POST['checkbox_departement'])){
		$selects[] = 'IF(a.id_country=8, LEFT (a.postcode,2), "") as departement';
		$header_csv[] = 'Département';
	}
	if(isset($_POST['checkbox_genre'])){
		$selects[] = 'IF(c.id_gender=1, "M", IF(c.id_gender=2, "F", "")) as gender';
		$header_csv[] = 'Genre';
	}
	if(isset($_POST['checkbox_age'])){
		$selects[] = 'TIMESTAMPDIFF(YEAR, birthday, CURDATE()) AS age';
		$header_csv[] = 'Age';
	}
	if(isset($_POST['checkbox_prix'])){
		$selects[] = 'od.unit_price_tax_incl';
		$header_csv[] = 'Prix de vente unitaire TTC';
	}
	if(isset($_POST['checkbox_date'])){
		$selects[] = 'o.date_add';
		$header_csv[] = 'Date';
	}
	if(isset($_POST['checkbox_jour'])){
		$selects[] = 'CASE DAYNAME(o.date_add)
			WHEN "Monday" THEN "Lundi"
			WHEN "Tuesday" THEN "Mardi"
			WHEN "Wednesday" THEN "Mercredi"
			WHEN "Thursday" THEN "Jeudi"
			WHEN "Friday" THEN "Vendredi"
			WHEN "Saturday" THEN "Samedi"
			WHEN "Sunday" THEN "Dimanche"
		END AS day_of_week';
		$header_csv[] = 'Jour';
	}
	if(isset($_POST['checkbox_email'])){
		$selects[] = 'c.email';
		$header_csv[] = 'Email client';
	}
	if(isset($_POST['checkbox_utm_medium'])){
		$selects[] = 'o.utm_medium';
		$header_csv[] = 'Utm medium';
	}
	if(isset($_POST['checkbox_mode_paiement'])){
		$selects[] = 'o.payment';
		$header_csv[] = 'Mode de paiement';
	}
	if(isset($_POST['checkbox_mode_livraison'])){
		$selects[] = 'car.name';
		$header_csv[] = 'Mode de livraison';
	}
	
	// Ajout d'un moins un select si rien n'est coché
	if(!count($selects)){
		$selects[] = 'od.product_reference';
		$header_csv[] = 'Référence';
	}

	$select_query = implode(', ',$selects);

	// On recupère les produits concernés
	
	// L'id_country 8 est la France
	// id_gender 1 = M, 2 = F

	$sql = ' SELECT '.$select_query.' FROM `' . _DB_PREFIX_ . 'order_detail` od
        LEFT JOIN `' . _DB_PREFIX_ . 'orders` o ON o.id_order = od.id_order 
        LEFT JOIN `' . _DB_PREFIX_ . 'address` a ON a.id_address = o.id_address_invoice 
		LEFT JOIN `' . _DB_PREFIX_ . 'carrier` car ON car.id_carrier = o.id_carrier 
        LEFT JOIN `' . _DB_PREFIX_ . 'customer` c ON c.id_customer = o.id_customer 
		' . $join_category . '
        WHERE o.valid = 1 
		' . $where_category . ' 
		' . $where_product . ' 
        AND o.date_add >= "' . $date_debut . '" 
        AND o.date_add <= "' . $date_fin . '"';
		
	$res = Db::getInstance()->ExecuteS($sql);

	//print_r($res);
	
	
	// Recalcul du chiffre d'affaire pour inclure les réductions
	
	// Attention à aller chercher tous les produits des commandes, contrairement à la requête précédente. On a besoin de tous les produits pour faire le calcul des réductions.
	// La sous reqête est obligatoire pour optimiser la vitesse de calcul (sinon on prend toutes les commandes inutiles, et ça prend trop de temps à calculer)
	// TODO : le calcul des réductions prend quand même beaucoup de temps. Voir si on peut optimiser d'avantage.
	
	$sql = ' SELECT o.id_order, o.id_cart, od.product_id, od.product_attribute_id, od.product_quantity, od.total_price_tax_incl FROM `' . _DB_PREFIX_ . 'order_detail` od
        LEFT JOIN `' . _DB_PREFIX_ . 'orders` o ON o.id_order = od.id_order 
        LEFT JOIN `' . _DB_PREFIX_ . 'address` a ON a.id_address = o.id_address_invoice 
		LEFT JOIN `' . _DB_PREFIX_ . 'carrier` car ON car.id_carrier = o.id_carrier 
        LEFT JOIN `' . _DB_PREFIX_ . 'customer` c ON c.id_customer = o.id_customer 
        WHERE o.valid = 1  
		AND o.id_order IN(
			SELECT DISTINCT o.id_order FROM `' . _DB_PREFIX_ . 'order_detail` od
			LEFT JOIN `' . _DB_PREFIX_ . 'orders` o ON o.id_order = od.id_order 
			' . $join_category . '
			WHERE o.valid = 1 
			' . $where_category . ' 
			' . $where_product . ' 
			AND o.date_add >= "' . $date_debut . '" 
			AND o.date_add <= "' . $date_fin . '"
		)
        AND o.date_add >= "' . $date_debut . '" 
        AND o.date_add <= "' . $date_fin . '"';
		
			
	$res_orders = Db::getInstance()->ExecuteS($sql);
	$orders = group_by($res_orders, 'id_order');
	
	$reductions = [];
	
	foreach($orders as $id_order => $order){
		
		$sql = ' SELECT ocr.value, ocr.id_cart_rule 
		FROM `' . _DB_PREFIX_ . 'order_cart_rule` ocr
        WHERE ocr.id_order = '.(int)$id_order;
		$order_cart_rules = Db::getInstance()->ExecuteS($sql);
		//echo $id_order.'<br>';
		if(isset($order[0]) && isset($order[0]['id_cart'])){ 
			$cart = new Cart($order[0]['id_cart']);
			
			foreach($order_cart_rules as $order_cart_rule){
				$id_cart_rule = $order_cart_rule['id_cart_rule'];
				$cartrule = new CartRule($id_cart_rule);
				
				// Produits concernant les cart rules classiques
				
				$selected_products = $cartrule->checkProductRestrictionsFromCart($cart, true);
				//print_r($selected_products);

				$total_reduction = $order_cart_rule['value'];

				if(is_array($selected_products)){	
					$prix_total_des_produits = 0; // prix total des produits incluts dans la réduction
					foreach($order as $product){
						$product_id = $product['product_id'];
						$product_attribute_id = $product['product_attribute_id'];
						if (in_array($product_id . '-' . $product_attribute_id, $selected_products)){
							$prix_total_des_produits += $product['total_price_tax_incl'];
						}
					}
					
					if($prix_total_des_produits){
						foreach($order as $product){
							$product_id = $product['product_id'];
							$product_attribute_id = $product['product_attribute_id'];
							if (in_array($product_id . '-' . $product_attribute_id, $selected_products)){
								if(!isset($reductions[$id_order.'-'.$product_id])){
									$reductions[$id_order.'-'.$product_id] = 0;
								}
								$total_price_tax_incl = $product['total_price_tax_incl']; // ce prix inclut la quantité
								$prix_reduction = $total_price_tax_incl * $total_reduction / $prix_total_des_produits / $product['product_quantity'];
								$reductions[$id_order.'-'.$product_id] += $prix_reduction;
							}
						}
					}
				}
				
				
				// Produits concernant les cart rules du module quantitydiscountpro (par exemple les -20% sur les fraisiers)

				$sql = 'SELECT id_quantity_discount_rule
						FROM `'._DB_PREFIX_.'quantity_discount_rule_cart`
						WHERE `id_cart_rule` = '.(int)$id_cart_rule;
				$qdrs = Db::getInstance()->ExecuteS($sql);
				foreach($qdrs as $qdr){
					$id_rule = $qdr['id_quantity_discount_rule'];
					$quantityDiscountRuleObj = new QuantityDiscountRule((int)$id_rule);

					$prix_total_des_produits = 0;

					foreach($order as $product){
						$product_id = $product['product_id'];
						// TODO $quantityDiscountRuleObj->validateCartRuleForMessages ne fonctionne pas avec les déclinaisons, donc il faut utiliser une autre fonction
						if($quantityDiscountRuleObj->validateCartRuleForMessages($product_id)){
							$prix_total_des_produits += $product['total_price_tax_incl'];
						}
					}
				
					if($prix_total_des_produits){
						foreach($order as $product){
							$product_id = $product['product_id'];
							$product_attribute_id = $product['product_attribute_id'];
							// TODO $quantityDiscountRuleObj->validateCartRuleForMessages ne fonctionne pas avec les déclinaisons, donc il faut utiliser une autre fonction
							if($quantityDiscountRuleObj->validateCartRuleForMessages($product_id)){
								if(!isset($reductions[$id_order.'-'.$product_id])){
									$reductions[$id_order.'-'.$product_id] = 0;
								}
								$total_price_tax_incl = $product['total_price_tax_incl']; // ce prix inclut la quantité
								$prix_reduction = $total_price_tax_incl * $total_reduction / $prix_total_des_produits / $product['product_quantity'];
								$reductions[$id_order.'-'.$product_id] += $prix_reduction;					
							}
						}
					}
				}	
			}
			
	
			
		}
	}
	
	
	header('Content-Type: text/csv');
	header('Content-Disposition: attachment; filename="commandes.csv"');
	$fp = fopen('php://output', 'wb');
	fputcsv($fp, $header_csv, ',');
	foreach($res as $row){
		if(isset($row['unit_price_tax_incl'])){
			$id_order = $row['id_order'];
			$product_id = $row['product_id'];
			if(isset($reductions[$id_order.'-'.$product_id])){
				$row['unit_price_tax_incl'] -= $reductions[$id_order.'-'.$product_id];
			}
			$row['unit_price_tax_incl'] = number_format($row['unit_price_tax_incl'],2);
		}
		
		unset($row['product_id']);
		$line = $row;
		fputcsv($fp, $line, ',');
	}
	fclose($fp);

}

function group_by($array, $key) {
    $return = array();
    foreach($array as $val) {
        $return[$val[$key]][] = $val;
    }
    return $return;
}




?>

