<?php

// sur php8 dev, test Cumulé courgettes, date début 01/10/2023
// TODO, réécrire la partie concernant la réduction pour simplier la lecture du code, optimiser la rapidité d'execution, et modifier validateCartRuleForMessages qui ne fonctionne pas avec les déclinaisons (lot of 6 fraisiers par exemple)
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

if (isset($_POST['submit_export2'])) {
	
	$date_debut = $_POST['date_debut2'];
	if(!$date_debut){
		$date_debut = date('Y-m-d');
	}
	$date_debut .= ' 00:00:00';
	$date_debut = pSQL($date_debut);
	
	$date_fin = $_POST['date_fin2'];
	if(!$date_fin){
		$date_fin = '9999-12-31';
	}
	$date_fin .= ' 23:59:59';
	$date_fin = pSQL($date_fin);

	// Catégories
	
	$id_category = (int) $_POST['id_category2'];
	$join_category = '';
	$where_category = '';
	if($id_category){
		$join_category = ' LEFT JOIN `' . _DB_PREFIX_ . 'category_product` ca ON ca.id_product = od.product_id ';
		$where_category = ' AND ca.id_category = ' . $id_category . ' '; 
	}
	 
	// Produit
	
	$id_product = (int) $_POST['id_product2'];
	$where_product = '';
	if($id_product){
		$where_product = ' AND od.product_id = ' . $id_product . ' '; 
	}
	
	// Checkboxes
	
	$selects = ['od.product_id'];
	$header_csv = [];
	if(isset($_POST['checkbox_ref2'])){
		$selects[] = 'od.product_reference';
		$header_csv[] = 'Référence';
	}
	if(isset($_POST['checkbox_nom2'])){
		$selects[] = 'SUBSTRING_INDEX(od.product_name,\' - \',1) as nom';
		$header_csv[] = 'Nom';

		/*$selects[] = 'REPLACE(od.product_name, CONCAT(SUBSTRING_INDEX(od.product_name,\' - \',1), " - "), "") as declinaison';
		$header_csv[] = 'Déclinaison';*/
	}
	if(isset($_POST['checkbox_quantite2'])){
		$selects[] = 'SUM(od.product_quantity) as quantite';
		$header_csv[] = 'Quantité';
	}
	if(isset($_POST['checkbox_commande2'])){
		$selects[] = '(SELECT COUNT(*) as nb_commandes FROM ps_order_detail od2 LEFT JOIN ps_orders o2 ON od2.id_order = o2.id_order WHERE od2.product_id = od.product_id AND od2.product_attribute_id = od.product_attribute_id and o2.date_add >= "' . $date_debut . '" AND o2.date_add <= "' . $date_fin . '") as commandes';
		$header_csv[] = 'Commandes';
	}
	if(isset($_POST['checkbox_prix2'])){
		$selects[] = 'SUM(od.total_price_tax_incl) as ca';
		$header_csv[] = 'CA';
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
	
	
	//print_r($res);
	if(isset($_POST['checkbox_decli2']))
	{
        $sql .= ' GROUP BY od.product_name';
	}
	else
	{
        $sql .= ' GROUP BY od.product_id';
	}

	$res = Db::getInstance()->ExecuteS($sql);
	
	
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
								if(!isset($reductions[$product_id])){
									$reductions[$product_id] = 0;
								}
								$total_price_tax_incl = $product['total_price_tax_incl']; // ce prix inclut la quantité
								$prix_reduction = $total_price_tax_incl * $total_reduction / $prix_total_des_produits;
								$reductions[$product_id] += $prix_reduction;
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
								if(!isset($reductions[$product_id])){
									$reductions[$product_id] = 0;
								}
								$total_price_tax_incl = $product['total_price_tax_incl']; // ce prix inclut la quantité
								$prix_reduction = $total_price_tax_incl * $total_reduction / $prix_total_des_produits;
								$reductions[$product_id] += $prix_reduction;					
							}
						}
					}
				}	
			}
		}
	}
	
	//echo '<pre>'.print_r($reductions,true).'</pre>';
	// Ecriture du csv
	
	header('Content-Type: text/csv');
	header('Content-Disposition: attachment; filename="commandes_cumul.csv"');
	$fp = fopen('php://output', 'wb');
	fputcsv($fp, $header_csv, ',');
	foreach($res as $row){
		if(isset($row['ca'])){
			$product_id = $row['product_id'];
			if(isset($reductions[$product_id])){
				$row['ca'] -= $reductions[$product_id];
			}
			$row['ca'] = number_format($row['ca'],2);
			
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