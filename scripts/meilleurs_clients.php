<?php

exit;

include("../config/config.inc.php");

$lines = [];

$sql = 'SELECT c.id_customer, c.firstname, c.lastname, c.email, 
		SUM(o.total_paid_real) AS chiffre_d_affaires
		FROM ps_orders o
		INNER JOIN ps_customer c ON o.id_customer = c.id_customer
		GROUP BY c.id_customer
		ORDER BY chiffre_d_affaires DESC
		LIMIT 5000;';

$customers = Db::getInstance()->executeS($sql);

foreach ($customers as $customer){
	$id_customer = (int) $customer['id_customer'];
	$firstname = $customer['firstname'];
	$lastname = $customer['lastname'];
	$email = $customer['email'];
	$sql = 'SELECT id_order, total_paid_real, MONTH(date_add) as mois, YEAR(date_add) as annee
			FROM ps_orders
			WHERE id_customer = '.$id_customer;
	$orders = Db::getInstance()->executeS($sql);
	foreach($orders as $order){
		$id_order = $order['id_order'];
		$montant = $order['total_paid_real'];
		$montant = number_format($montant, 2, '.', ""); 
		$date = $order['mois'].'/'.$order['annee'];
		$lines[] = [
			$lastname,
			$firstname,
			$email,
			$id_order,
			$montant,
			$date
		];
	}
}

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="commandes.csv"');
$fp = fopen('php://output', 'wb');
$line = array('nom', 'prénom', 'email', 'ID commande', 'montant', 'date');
fputcsv($fp, $line, ';');
foreach($lines as $line){
	fputcsv($fp, $line, ';');
}
fclose($fp);