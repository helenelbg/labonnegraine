<?php
die;
include_once '../config/config.inc.php';
include_once '../config/settings.inc.php';
include_once '../init.php';

$close_zone_rosiers = '(od.product_id IN (SELECT id_product FROM ps_category_product WHERE id_category IN (129,135,132,131,133,134,213,299,338)))';
$id_order_state = 2;
$sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'order_detail od LEFT JOIN ' . _DB_PREFIX_ . 'orders o2 ON (od.id_order = o2.id_order) WHERE o2.`current_state` = ' . (int) $id_order_state . ' AND ('.$close_zone_rosiers.');';

$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

$commande = array();
foreach ($result as $prod) {
    /*echo '<pre>';
    print_r($prod);
    echo '</pre>';*/
    $exp = explode(' (', $prod['product_name']);
    if ( !isset($commande[$prod['product_id']]))
    {
        $commande[$prod['product_id']] = array('name' => $exp[0], 'qt' => ($prod['product_quantity'] - $prod['product_quantity_refunded']));
    }
    else 
    {
        $commande[$prod['product_id']]['qt'] += ($prod['product_quantity'] - $prod['product_quantity_refunded']);
    }
}

$cmd_final = '';
foreach ($commande as $final) {
    if ( !empty($cmd_final) )
    {
        $cmd_final .= ', ';
    }
    else 
    {
        $cmd_final .= 'Nouvelle commande La Bonne Graine : ';
    }
    $cmd_final .= $final['qt'].' x '.$final['name'];
}

$service_plan_id = "eb0dde1121c048809c6535042cc2b525";
$bearer_token = "ed5a0f1718a94e818cf7b29523c76435";

//Any phone number assigned to your API
//$send_from = "447537404817";
$send_from = "LBG";
//May be several, separate with a comma ,
$recipient_phone_numbers = "+33679050551"; 
//$recipient_phone_numbers = "+33676900032"; 
$message = $cmd_final;

// Check recipient_phone_numbers for multiple numbers and make it an array.
if(stristr($recipient_phone_numbers, ',')){
  $recipient_phone_numbers = explode(',', $recipient_phone_numbers);
}else{
  $recipient_phone_numbers = [$recipient_phone_numbers];
}

// Set necessary fields to be JSON encoded
$content = [
  'to' => array_values($recipient_phone_numbers),
  'from' => $send_from,
  'body' => $message
];

$data = json_encode($content);

$ch = curl_init("https://us.sms.api.sinch.com/xms/v1/{$service_plan_id}/batches");
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BEARER);
curl_setopt($ch, CURLOPT_XOAUTH2_BEARER, $bearer_token);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$result = curl_exec($ch);

$vars = [
    '{contenu}' => $message,
];

Mail::Send(
    1,
    'rosiers',
    /*Context::getContext()->getTranslator()->trans(
        'Your guest account has been transformed into a customer account',
        [],
        'Emails.Subject',
        $language->locale
    ),*/
    'Nouvelle commande de rosiers',
    $vars,
    'guillaume.amary.lbg@gmail.com',
    'Guillaume Amary',
    null,
    null,
    null,
    null,
    _PS_MAIL_DIR_,
    false,
    1
);

/*if(curl_errno($ch)) {
    echo 'Curl error: ' . curl_error($ch);
} else {
    echo $result;
}*/
curl_close($ch);
?>