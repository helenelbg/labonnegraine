<?php

require 'config/config.inc.php';
require 'init.php';


if (isset($_POST["idCustomer"]) && isset($_POST["idAssistant"])) {

	$sql = 'DELETE FROM assistant_client WHERE idClient = '.pSQL($_POST["idCustomer"]).' && idAssistant = '.pSQL($_POST["idAssistant"]);
	Db::getInstance()->execute($sql);
	
	$sql = 'SELECT * FROM assistant_url WHERE id_assistant = '.pSQL($_POST["idAssistant"]);
	$url = Db::getInstance()->executeS($sql);
	
	$sql = 'SELECT * FROM `'._DB_PREFIX_.'customer` WHERE `id_customer` = '.pSQL($_POST["idCustomer"]);
	$mail = Db::getInstance()->executeS($sql);

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url[0]["url_sortie"].'/'.$mail[0]["email"]);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_exec($ch);
	curl_close($ch);

	echo json_encode("desabonnement : succes");
}

if (isset($_POST["idClient"])) {
	
	$sql = 'DELETE FROM assistant_client WHERE idClient = '.pSQL($_POST["idClient"]).' && etat = 0)';
	Db::getInstance()->execute($sql);

	echo json_encode("value");
}

if (isset($_POST["addClient"]) && isset($_POST["addAssistant"]) && isset($_POST["idDepartement"]) && isset($_POST["hauteAltitude"]) && isset($_POST["idCart"])) {
	
	$sql = 'SELECT * FROM assistant_client WHERE idClient = '.pSQL($_POST["addClient").' AND idAssistant = '.pSQL($_POST["addAssistant");
	$lesAssistants = Db::getInstance()->executeS($sql);

	if (isset($_POST["initial_state"])) {
		$etatCyril = $_POST["initial_state"];
	}else{
		$etatCyril = 0;
	}

	if (empty($lesAssistants[0]["idClient"])) {
		
		$sql = 'INSERT INTO assistant_client VALUES (NULL,
		'.pSQL($_POST["addClient"]).',
		'.pSQL($_POST["addAssistant"].',
		'.pSQL($_POST["idCart"].',
		'.date("Y-m-d H:i:s").',
		'.pSQL($_POST["idDepartement"].',
		'.pSQL($_POST["hauteAltitude"].',
		'.pSQL($etatCyril).')';
		Db::getInstance()->execute($sql);

	}

	if ($etatCyril == 2) {
		
		$sql = 'SELECT * FROM assistant_url WHERE id_assistant = '.pSQL($_POST["addAssistant"]);
		$url = Db::getInstance()->executeS($sql);
	
		$sql = 'SELECT * FROM Departements WHERE numDepartement = '.pSQL($_POST["idDepartement"]);
		$departement = Db::getInstance()->executeS($sql);
		
		$sql = 'SELECT * FROM '._DB_PREFIX_.'customer WHERE id_customer = '.pSQL($_POST["addClient"]);
		$mail = Db::getInstance()->executeS($sql);

		$ch = curl_init();

		if ($_POST["hauteAltitude"] == 0) {
			curl_setopt($ch, CURLOPT_URL, $url[0]["url"]."_".$departement[0]["decalage"].'/'.$mail[0]["email"]);
		}else{
			curl_setopt($ch, CURLOPT_URL, $url[0]["url"]."_".$departement[0]["decalageAltitude"].'/'.$mail[0]["email"]);
		}

		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_exec($ch);
		curl_close($ch);
	}

	echo json_encode("Assistant : ajouté");
}

if (isset($_POST["delClient"]) && isset($_POST["delAssistant"])) {

	$sql = 'DELETE FROM assistant_client WHERE idClient = '.pSQL($_POST["delClient"]).' && idAssistant = '.pSQL($_POST["delAssistant"]).' && etat = 0")';
	Db::getInstance()->execute($sql);

	echo json_encode("Assistant : Supprimé");
}

if (isset($_POST["numDep"]) && isset($_POST["idDuCustomer"]) && isset($_POST["uneHauteAltitude"])) {
	
	$sql = 'UPDATE assistant_client SET numDepartement = '.pSQL($_POST["numDep"]).', hauteAltitude = '.pSQL($_POST["uneHauteAltitude"]).' WHERE idClient = '.pSQL($_POST["idDuCustomer"]).'")';
	Db::getInstance()->execute($sql);
	
	$sql = 'SELECT * FROM Departements WHERE numDepartement = '.pSQL($_POST["numDep"]);
	$value = Db::getInstance()->executeS($sql);

	echo json_encode($value[0]["montagne"]);

}

