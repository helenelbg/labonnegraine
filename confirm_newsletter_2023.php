<?php

// Ce script post formulaire de https://dev.labonnegraine.com/content/96-newsletter
// Dorian, BERRY-WEB, janvier 2023

require 'config/config.inc.php';
require 'init.php';

if(!isset($_GET['token'])){
	exit;
}

$token = $_GET['token'];
$email = getEmailByToken($token);

$body = [
  'Action' => "addnoforce",
  'Email' => $email 
];

$list_ID = Tools::get_id_newsletter_bonsplans(); // newsletter bonsplans

$user = '34b10e378c3e0fa97459c5c143f5ec58'; 
     
$pass = '548160ec9d9e64da604c578c68636f08';

$url = 'https://api.mailjet.com/v3/REST/contactslist/' .$list_ID. '/managecontact';


$auth_key = $user.":".$pass;
$encoded_auth_key = base64_encode($auth_key);
$headers = array();
$headers[] = 'Authorization: Basic '.$encoded_auth_key;

$ch = curl_init();
$opt = array(
  CURLOPT_POSTFIELDS => $body,
  CURLOPT_HTTPHEADER => $headers,
  CURLOPT_URL => $url,
  CURLOPT_FRESH_CONNECT => 1,
  CURLOPT_RETURNTRANSFER => 1,
  CURLOPT_FORBID_REUSE => 1,
  CURLOPT_TIMEOUT => 4,
  CURLOPT_SSL_VERIFYHOST => 0, 
  CURLOPT_SSL_VERIFYPEER => 0 
);
curl_setopt_array($ch, $opt);
  
$result = curl_exec($ch);

if ($result == false) {
	echo  "error :".curl_error($ch);
	curl_close($ch);
	$newURL = "/content/96-newsletter?error=1";
	header('Location: '.$newURL);
}else{
	echo 'ok'; 
	curl_close($ch);
	
	// Mise à jour des infos client en BDD
	
	$res_customer = Customer::getCustomersByEmail($email);
	$id_customer = $res_customer[0]['id_customer'];
	if($id_customer){
		$customer = new Customer($id_customer);
		$customer->newsletter = 1;
		$customer->save();
	}
	
	$newURL = "/content/96-newsletter?success=1";
	header('Location: '.$newURL);
}

function getEmailByToken($token)
{
	$sql = 'SELECT `email`
			FROM `'._DB_PREFIX_.'newsletter`
			WHERE MD5(CONCAT( `email` , `newsletter_date_add`, \''.pSQL(Configuration::get('NW_SALT')).'\')) = \''.pSQL($token).'\'
			AND `active` = 0';

	return Db::getInstance()->getValue($sql);
}

?>