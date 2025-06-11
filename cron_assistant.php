<?php
$connexion = new PDO("mysql:host=92.243.24.83;dbname=lbg", "lbg", "bgtlR-2d");

// $query = $connexion->prepare("SELECT * FROM ps_orders WHERE id_cart IN (SELECT idcart FROM assistant_client WHERE etat = 1)");
// $query->execute();
// $value = $query->fetchAll();
// $query->closeCursor();



//On parcours toutes les inscriptions aux assistants si ils ne sont pas déja dans BOLeads et à condition qu'ils aient une commande valide
//$query = $connexion->prepare("SELECT DISTINCT(a.idCart)'id_cart' FROM assistant_client a INNER JOIN ps_orders o ON o.id_cart = a.idCart WHERE (a.etat <> 2 AND o.valid = 1)");
$query = $connexion->prepare("SELECT DISTINCT(a.idCart)'id_cart' FROM assistant_client a INNER JOIN ps_orders o ON o.id_cart = a.idCart WHERE (a.etat <> 2 AND o.valid = 1) OR (idClient IN (SELECT id_customer FROM ps_customer_group WHERE id_group = 2))");
$query->execute();
$value = $query->fetchAll();
$query->closeCursor();

foreach ($value as $unCart) { //Pour chacun de ces carts :

	$query = $connexion->prepare("SELECT * FROM assistant_client a INNER JOIN assistant_url u ON u.id_assistant = a.idAssistant WHERE a.idCart = ?");
	$query->execute([$unCart["id_cart"]]);
	$lesAssistants = $query->fetchAll();
	$query->closeCursor();

	foreach ($lesAssistants as $unAssistant) {
			if (!empty($unAssistant["url"]) && $unAssistant["etat"] != 2) {

				$query = $connexion->prepare("UPDATE assistant_client SET etat = 2 WHERE idAssistantClient = ?");
				$query->execute([$unAssistant["idAssistantClient"]]);
				$query->closeCursor();

				$query = $connexion->prepare("SELECT * FROM Departements WHERE numDepartement = ?");
				$query->execute([$unAssistant["numDepartement"]]);
				$departement = $query->fetch();
				$query->closeCursor();

				$query = $connexion->prepare("SELECT * FROM ps_customer WHERE id_customer = ?");
				$query->execute([$unAssistant["idClient"]]);
				$mail = $query->fetch();
				$query->closeCursor();

				$ch = curl_init();

				if ($unAssistant["hauteAltitude"] == 0) {
					curl_setopt($ch, CURLOPT_URL, $unAssistant["url"]."_".$departement["decalage"].'/'.$mail["email"]);
					error_log('URL1 : '.$unAssistant["url"]."_".$departement["decalage"].'/'.$mail["email"]);
				}else{
					curl_setopt($ch, CURLOPT_URL, $unAssistant["url"]."_".$departement["decalageAltitude"].'/'.$mail["email"]);
					error_log('URL2 : '.$unAssistant["url"]."_".$departement["decalageAltitude"].'/'.$mail["email"]);
				}

				curl_setopt($ch, CURLOPT_HEADER, 0);
				error_log('curl_exec');
				curl_exec($ch);
				curl_close($ch);
				error_log('curl_close');

				// on inscrit que le 1er assistant disponible pour éviter les doublons qui arrivent si on inscrit trop vite 2 assistants
				break;
			}
		}
}

// foreach ($value as $unCart) {
// 	if ($unCart["valid"] == 1) {
// 		$query = $connexion->prepare("SELECT * FROM assistant_client a INNER JOIN assistant_url u ON u.id_assistant = a.idAssistant WHERE a.idCart = ?");
// 		$query->execute([$unCart["id_cart"]]);
// 		$lesCart = $query->fetchAll();
// 		$query->closeCursor();
//
// 		echo "<pre>";
// 		print_r($lesCart);
// 		echo "</pre>";
//
// 		foreach ($lesCart as $leCart) {
//
// 			if ($leCart["etat"] == 1 && !empty($leCart["url"])) {
// 				$query = $connexion->prepare("UPDATE assistant_client SET etat = 2 WHERE idAssistantClient = ?");
// 				$query->execute([$leCart["idAssistantClient"]]);
// 				$query->closeCursor();
//
// 				$query = $connexion->prepare("SELECT * FROM assistant_url WHERE id_assistant = ?");
// 				$query->execute([$leCart["idAssistant"]]);
// 				$url = $query->fetchAll();
// 				$query->closeCursor();
//
// 				$query = $connexion->prepare("SELECT * FROM Departements WHERE numDepartement = ?");
// 				$query->execute([$leCart["numDepartement"], ]);
// 				$departement = $query->fetchAll();
// 				$query->closeCursor();
//
// 				$query = $connexion->prepare("SELECT * FROM ps_customer WHERE id_customer = ?");
// 				$query->execute([$leCart["idClient"]]);
// 				$mail = $query->fetchAll();
// 				$query->closeCursor();
//
// 				$ch = curl_init();
//
// 				if ($leCart["hauteAltitude"] == 0) {
// 					curl_setopt($ch, CURLOPT_URL, $url[0]["url"]."_".$departement[0]["decalage"].'/'.$mail[0]["email"]);
// 				}else{
// 					curl_setopt($ch, CURLOPT_URL, $url[0]["url"]."_".$departement[0]["decalageAltitude"].'/'.$mail[0]["email"]);
// 				}
//
// 				curl_setopt($ch, CURLOPT_HEADER, 0);
// 				curl_exec($ch);
// 				curl_close($ch);
// 			}
// 		}
// 	}
// }
