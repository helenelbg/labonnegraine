<?php

// cron Votre avis est important
// Tous les jours à 6 heures du matin.
// Envoi d'un mail aux clients 7 jours après avoir commandé

// https://dev.labonnegraine.com/scripts/cron_avis.php

// 00 06 * * * wget https://dev.labonnegraine.com/scripts/cron_avis.php -O /dev/null --no-check-certificate

// TODO : L'envoi de l'emailing ne doit concerner que les colissimos et mettre 6 jours après le passage au statut livré.

//exit;

include("../config/config.inc.php");


$date = new DateTime('now');
$date->sub(new DateInterval('P6D'));
$ymd = $date->format('Y-m-d');
$state = 5; // livré
 
$sql = 'SELECT o.id_order, c.firstname, c.email FROM ps_orders o
LEFT JOIN ps_customer c ON c.id_customer = o.id_customer
LEFT JOIN ps_order_history oh ON oh.id_order = o.id_order
INNER JOIN ps_colissimo_order co ON co.id_order = o.id_order
WHERE oh.date_add LIKE "'.pSQL($ymd).'%"
AND oh.id_order_state = "'.pSQL($state).'"';

$orders = Db::getInstance()->executeS($sql);


echo '<pre>';
print_r($orders);
echo '</pre>';

define('MAILJET_USER','34b10e378c3e0fa97459c5c143f5ec58');
define('MAILJET_PASS','548160ec9d9e64da604c578c68636f08');
define('MAILJET_TEMPLATE_ID',5393442);



$from_mail = 'info@labonnegraine.com';
$from_name = 'La Bonne Graine';

foreach($orders as $order){
	$prenom = $order['firstname'];
	$to = $order['email'];
	//$to = 'dorian@berry-web.com';
	//$to = 'karine.coussy.lbg@gmail.com';
	$subject = $prenom . ', votre avis est important !';
	$variables = array(
		'prenom' => $prenom,
	);

	// Envoi du mail
	$res = mailjet_send_mail($to, $from_mail, $from_name, $subject, $variables);
	//print_r($res);
	//break;

}

function mailjet_send_mail($to, $from_mail, $from_name, $subject, $variables){

  $auth_key = MAILJET_USER.":".MAILJET_PASS;
  $encoded_auth_key = base64_encode($auth_key);
  $headers = array();
  $headers[] = 'Authorization: Basic '.$encoded_auth_key;
  $headers[] = 'Content-Type: application/json';

  $body = array(
    'Messages' => array(
      array(
        'From' =>  array('Email' => $from_mail, 'Name' => $from_name),
        'Subject' => $subject,
		'TemplateID' => MAILJET_TEMPLATE_ID,
        'TemplateLanguage' => true,
        'To' =>  array(array('Email' => $to)),
		'Variables' => $variables
      )
    )
  );

  $body_encoded = json_encode($body);

  $url = 'https://api.mailjet.com/v3.1/send';

  $ch = curl_init();
  $opt = array(
    CURLOPT_POSTFIELDS => $body_encoded,
    CURLOPT_HTTPHEADER => $headers,
    CURLOPT_URL => $url,
    CURLOPT_FRESH_CONNECT => 1,
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_FORBID_REUSE => 1,
    CURLOPT_TIMEOUT => 4,
  );
  curl_setopt_array($ch, $opt);

  $result=curl_exec($ch);

  if ($result == false) {
    //echo  "error :".curl_error($ch);
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


