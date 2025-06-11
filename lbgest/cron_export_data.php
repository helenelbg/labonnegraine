<?php

// Ce script envoie un mail à Stéphane avec le csv en pièce jointe, tous les mois.
// https://dev.labonnegraine.com/lbgest/cron_export_data.php?token=hdf6dfdfs6ddgs

if ( $_GET['token'] != 'hdf6dfdfs6ddgs' )
{
    die;
}

ini_set('max_execution_time', '7200');

include_once '../config/config.inc.php';
include_once '../config/settings.inc.php';
include_once '../init.php';

try {
	$bdd = new PDO("mysql:host=" . _DB_SERVER_ . ";dbname=" . _DB_NAME_, _DB_USER_, _DB_PASSWD_);
} catch (exeption $ex) {
	die("probleme connexion serveur" . $ex->getMessage());
}



// Catégories

$categories = [
	18 => "Graines Potagères",
	231 => "Plants PDT",
	148 => "Petits fruits",
	112 => "Bulbes Potagers",
	234 => "Accessoires",
	143 => "Produits Originaux",
	//999 => "Plants Potagers",
	248 => "Patates Douces",
	129 => "Rosiers",
	91 => "Bulbes à Fleurs",
	273 => "Serres",
	209 => "Gazon",
	312 => "Cartes Cadeaux",
	227 => "Box",
];

$categories_ak = array_keys($categories);

// + enrobées (les références 0-xxx)
// + frais de livraison

$currentDate = new DateTime();
$currentDate->sub(new DateInterval('P2Y'));
$currentDate->sub(new DateInterval('P1M'));
$months = [];

for ($i = 0; $i < 25; $i++) {
	$date_debut = $currentDate->format('Y-m-d');
    $currentDate->modify('first day of next month');
	$date_fin = $currentDate->format('Y-m-d');
	if($i){ // skip first date
		$months[] = [
			'date_debut' => $date_debut,
			'date_fin' => $date_fin,
		];
	}
}

$ca_categories_by_month = [];
$total_shipping_tax_excl_by_month = [];
$reductions_autres_by_month = []; // par exemple, les -10% pour 100€ d'achat

foreach($months as $id_month => $month){
	
	$date_debut = $month['date_debut'];
	$date_fin = $month['date_fin'];
	// Calcul du chiffre d'affaire HT (hors taxe) avec réductions

	$sql = ' SELECT o.id_order, o.id_cart, od.product_id, od.product_attribute_id, od.product_quantity, od.total_price_tax_excl, o.total_shipping_tax_excl, p.reference, od.tax_rate, o.date_add
		FROM `' . _DB_PREFIX_ . 'order_detail` od
		LEFT JOIN `' . _DB_PREFIX_ . 'orders` o ON o.id_order = od.id_order 
		LEFT JOIN `' . _DB_PREFIX_ . 'product` p ON p.id_product = od.product_id 
		WHERE o.valid = 1  
		AND o.date_add >= "' . $date_debut . '" 
		AND o.date_add < "' . $date_fin . '"';
		
	$res_orders = Db::getInstance()->ExecuteS($sql);
	$orders = group_by($res_orders, 'id_order');

	$total_shipping_tax_excl_by_month[$id_month] = 0;
	$reductions_autres_by_month[$id_month] = 0;
	$ca_categories = [];
	
	foreach($orders as $id_order => $order){
		
		$reductions = [];
		
		$sql = ' SELECT ocr.value_tax_excl, ocr.id_cart_rule 
		FROM `' . _DB_PREFIX_ . 'order_cart_rule` ocr
		WHERE ocr.id_order = '.(int)$id_order;
		$order_cart_rules = Db::getInstance()->ExecuteS($sql);
		if(isset($order[0]) && isset($order[0]['id_cart'])){ 
			$total_shipping_tax_excl_by_month[$id_month] += $order[0]['total_shipping_tax_excl'];
			$cart = new Cart($order[0]['id_cart']);	
			$date_add = $order[0]['date_add'];	
			
			foreach($order_cart_rules as $order_cart_rule){
				$id_cart_rule = $order_cart_rule['id_cart_rule'];
				$cartrule = new CartRule($id_cart_rule);
				
				// Produits concernant les cart rules classiques
				
				$selected_products = $cartrule->checkProductRestrictionsFromCart($cart, true);
				$total_reduction = $order_cart_rule['value_tax_excl'];
				$bool_reductions_autres = true;

				if(is_array($selected_products)){
					$bool_reductions_autres = false;
					$prix_total_des_produits = 0; // prix total des produits incluts dans la réduction
					foreach($order as $product){
						$product_id = $product['product_id'];
						$product_attribute_id = $product['product_attribute_id'];
						if (in_array($product_id . '-' . $product_attribute_id, $selected_products)){
							$prix_total_des_produits += $product['total_price_tax_excl'];
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
								$total_price_tax_excl = $product['total_price_tax_excl']; // ce prix inclut la quantité
								$prix_reduction = $total_price_tax_excl * $total_reduction / $prix_total_des_produits;
								$reductions[$product_id] += $prix_reduction;
							}
						}
					}
				}
				
				// Produits concernant les cart rules du module quantitydiscountpro (par exemple les -20% sur les fraisiers)
				// A désactiver pour les commandes après le 24 novembre 2023

				if($date_add < '2023-11-24'){
					$sql = 'SELECT id_quantity_discount_rule
							FROM `'._DB_PREFIX_.'quantity_discount_rule_cart`
							WHERE `id_cart_rule` = '.(int)$id_cart_rule;
					$qdrs = Db::getInstance()->ExecuteS($sql);
					foreach($qdrs as $qdr){
						$bool_reductions_autres = false;
						$id_rule = $qdr['id_quantity_discount_rule'];
						$quantityDiscountRuleObj = new QuantityDiscountRule((int)$id_rule);

						$prix_total_des_produits = 0;

						foreach($order as $product){
							$product_id = $product['product_id'];
							if($quantityDiscountRuleObj->validateCartRuleForMessages($product_id)){
								$prix_total_des_produits += $product['total_price_tax_excl'];
							}
						}
					
						if($prix_total_des_produits){
							foreach($order as $product){
								$product_id = $product['product_id'];
								$product_attribute_id = $product['product_attribute_id'];

								if($quantityDiscountRuleObj->validateCartRuleForMessages($product_id)){
									if(!isset($reductions[$product_id])){
										$reductions[$product_id] = 0;
									}
									$total_price_tax_excl = $product['total_price_tax_excl']; // ce prix inclut la quantité
									$prix_reduction = $total_price_tax_excl * $total_reduction / $prix_total_des_produits;
									$reductions[$product_id] += $prix_reduction;					
								}
							}
						}
					}	
				}
				
				if($bool_reductions_autres){
					$reductions_autres_by_month[$id_month] += $total_reduction;
				}
			}
			
			
			
			foreach($order as $product){
				$product_id = $product['product_id'];
				$reference = $product['reference'];
				$total_price_tax_excl = $product['total_price_tax_excl'];
				if(isset($reductions[$product_id])){
					$total_price_tax_excl -= $reductions[$product_id];
				}
				$product_categories = Product::getProductCategories($product_id);
				foreach($product_categories as $cat_id){
					if(!isset($ca_categories[$cat_id])){
						$ca_categories[$cat_id] = 0;
					}
					$ca_categories[$cat_id] += $total_price_tax_excl;
					// break pour éviter d'inclure des doublons pour les produits multi-catégories
					if(in_array($cat_id, $categories_ak)){
						//break;
					}
				}
				
				// Références 0-xxx enrobées
				$cat_id = 18; // Graines potagères 
				if(str_starts_with($reference,'0-') && !in_array($cat_id,$product_categories)){
					if(!isset($ca_categories[$cat_id])){
						$ca_categories[$cat_id] = 0;
					}
					$ca_categories[$cat_id] += $total_price_tax_excl;
				}
			}
			
		}
	}
	
	$ca_categories_by_month[$id_month] = $ca_categories;
}






// Ecriture du csv

/*
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="activité.csv"');
$fp = fopen('php://output', 'wb');
*/

$urFilePath = "cron_export_data.csv";
$fp = fopen($urFilePath, 'w+');

$header_csv = ['Activité'];
foreach($months as $id_month => $month){
	$date_debut = $month['date_debut'];
	$datefmt = new IntlDateFormatter('fr_FR', NULL, NULL, NULL, NULL, 'MMMM yyyy');
	$date1 = date_create($date_debut);
	$date = $datefmt->format($date1);
	$header_csv[] = 'CA HT '.$date;
}
	
fputcsv($fp, $header_csv, ',');

foreach($categories as $cat_id => $cat_name){
	$line = [$cat_name];
	foreach($months as $id_month => $month){
		$price = 0;
		if(isset($ca_categories_by_month[$id_month][$cat_id])){
			$price = $ca_categories_by_month[$id_month][$cat_id];
		}
		$price = number_format($price,2,'.','');
		$line[] = $price;
	}

	fputcsv($fp, $line, ',');
}


// Shipping
$line = ['Frais livraison'];
foreach($months as $id_month => $month){
	$price = 0;
	if(isset($total_shipping_tax_excl_by_month[$id_month])){
		$price = $total_shipping_tax_excl_by_month[$id_month];
	}
	$price = number_format($price,2,'.','');
	$line[] = $price;
}
fputcsv($fp, $line, ',');

// Réductions autres
$line = ['Réductions autres'];
foreach($months as $id_month => $month){
	$price = 0;
	if(isset($reductions_autres_by_month[$id_month])){
		$price = $reductions_autres_by_month[$id_month];
	}
	$price = number_format($price,2,'.','');
	$line[] = $price;
}
fputcsv($fp, $line, ',');

fclose($fp);

// Envoi des mails
		
$sujet = 'La Bonne Graine : export data';

$email_to = [
	'copilote29@gmail.com',
	'stephane.dipalma@gmail.com',
	'guillaume@anjouweb.com',
];

$attach = file_get_contents($urFilePath); 
$b64_content = base64_encode($attach); 

$message = 'Export data';

$from_mail = 'info@labonnegraine.com';
$from_name = 'La Bonne Graine';
$user = '34b10e378c3e0fa97459c5c143f5ec58';
$pass = '548160ec9d9e64da604c578c68636f08';
mailjet_send_mail($email_to, $from_mail, $from_name, $sujet, $message, $user, $pass, $urFilePath, $b64_content);

function mailjet_send_mail($to, $from_mail, $from_name, $subject, $message, $user, $pass, $attach_name, $b64_content){

	$toArray = [];
	foreach($to as $email){
	  $toArray[] = ['Email' => trim($email)];
	}
  
	$auth_key = $user.":".$pass;
	$encoded_auth_key = base64_encode($auth_key);
	$headers = array();
	$headers[] = 'Authorization: Basic '.$encoded_auth_key;
	$headers[] = 'Content-Type: application/json';

	$body = array(
		'Messages' => array(
			array(
				'From' =>  array('Email' => $from_mail, 'Name' => $from_name),
				'Subject' => $subject,
				'HTMLPart' => $message,
				"Attachments" => [
						[
								"ContentType" => "text/plain",
								"Filename" => $attach_name,
								"Base64Content" => $b64_content
						]
				],
				'To' =>  $toArray,
			)
		)
	);

	$body = json_encode($body);

	$url = 'https://api.mailjet.com/v3.1/send';
	$ch = curl_init();
	$opt = array(
		CURLOPT_POSTFIELDS => $body,
		CURLOPT_HTTPHEADER => $headers,
		CURLOPT_URL => $url,
		CURLOPT_FRESH_CONNECT => 1,
		CURLOPT_RETURNTRANSFER => 1,
		CURLOPT_FORBID_REUSE => 1,
		CURLOPT_TIMEOUT => 4
	);
	curl_setopt_array($ch, $opt);

	$result=curl_exec($ch);

	if ($result == false) {
		// echo  "error :".curl_error($ch);
	}
	curl_close($ch);

	$status = '';
	$res = json_decode($result);
	if(isset($res->Messages[0]->Status)){
		$status = $res->Messages[0]->Status;
	}

	if($status === "success"){
		return $result;
	}

	return false;
}

/*
$transport = Swift_SmtpTransport::newInstance('in-v3.mailjet.com',587)
->setUsername('34b10e378c3e0fa97459c5c143f5ec58')
->setPassword('548160ec9d9e64da604c578c68636f08');
Swift_Preferences::getInstance()->setCharset('utf-8');
$mailer = Swift_Mailer::newInstance($transport);
$messageEnvoi = Swift_Message::newInstance($sujet)
->setFrom('info@labonnegraine.com') 
->setTo($email_to)
->setBody($message, "text/html")
->attach(\Swift_Attachment::fromPath($urFilePath)->setFilename('Export data'));
$result = $mailer->send($messageEnvoi);
*/




function group_by($array, $key) {
    $return = array();
    foreach($array as $val) {
        $return[$val[$key]][] = $val;
    }
    return $return;
}


?>