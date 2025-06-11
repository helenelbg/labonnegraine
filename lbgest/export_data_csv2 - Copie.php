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
	
	$selects = [];
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

	if(isset($_POST['checkbox_decli2']))
	{
        $sql .= ' GROUP BY od.product_name';
	}
	else
	{
        $sql .= ' GROUP BY od.product_id';
	}

	$res = Db::getInstance()->ExecuteS($sql);
	
	header('Content-Type: text/csv');
	header('Content-Disposition: attachment; filename="commandes_cumul.csv"');
	$fp = fopen('php://output', 'wb');
	fputcsv($fp, $header_csv, ',');
	foreach($res as $row){
		if(isset($row['total'])){
			$row['total'] = number_format($row['total'],2);
		}
		$line = $row;
		fputcsv($fp, $line, ',');
	}
	fclose($fp);

}





?>