<?php
  include('../../config/config.inc.php');
  include('../../init.php');
  include('../../modules/colissimo/classes/ColissimoOrder.php');
  include('../../modules/colissimo/classes/ColissimoService.php');
  include('../../modules/colissimo/classes/ColissimoMerchantAddress.php');
  include('../../modules/colissimo/classes/ColissimoTools.php');
  include('../../modules/colissimo/classes/ColissimoPickupPoint.php');
  include('../../modules/colissimo/classes/ColissimoLabelGenerator.php');
  include('../../modules/colissimo/classes/ColissimoLink.php');
  include('../../modules/colissimo/classes/ColissimoLabel.php');
  include('../../modules/colissimo/classes/ColissimoCustomProduct.php');
  include('../../modules/colissimo/classes/ColissimoCustomCategory.php');
  include('../../modules/colissimo/lib/ColissimoClient.php');
  include('../../modules/colissimo/lib/Request/AbstractColissimoRequest.php');
  include('../../modules/colissimo/lib/Request/ColissimoGenerateLabelRequest.php');
  include('../../modules/colissimo/lib/Response/ColissimoResponseParser.php');
  include('../../modules/colissimo/lib/Response/ColissimoReturnedResponseInterface.php');
  include('../../modules/colissimo/lib/Response/AbstractColissimoResponse.php');
  include('../../modules/colissimo/lib/Response/ColissimoGenerateLabelResponse.php');
  include('../../modules/colissimo/classes/logger/AbstractColissimoHandler.php');
  include('../../modules/colissimo/classes/logger/ColissimoLogger.php');
  include('../../modules/colissimo/classes/logger/ColissimoFileHandler.php');
  include('../../modules/colissimo/classes/logger/ColissimoNullHandler.php');

//error_log('G0');

  $chemin = dirname(__FILE__).'/../colissimo/documents/labels/';
  $cheminCN23Exist = dirname(__FILE__).'/../colissimo/documents/cn23/';
  $cheminCN23 = '/modules/colissimo/documents/cn23/';
  $etiquetteCN23 = '0';

  $dbQuery = new DbQuery();
  $dbQuery->select('co.`id_colissimo_order`, cola.`id_colissimo_label`, cola.`cn23`, cola.`shipping_number`, cola.`label_format`')
          ->from('colissimo_order', 'co')
          ->leftJoin('orders', 'o', 'o.`id_order` = co.`id_order`')
          ->leftJoin('colissimo_label', 'cola', 'cola.`id_colissimo_order` = co.`id_colissimo_order`');
  //@formatter:on
  /*if (!empty($selectedStates)) {
      $dbQuery->where('o.`current_state` IN ('.implode(',', array_map('intval', $selectedStates)).')');
  }*/
  //$dbQuery->where('cola.id_colissimo_label IS NULL'.Shop::addSqlRestriction(false, 'o'));
  $dbQuery->where('co.id_order  = '.$_POST['id_order']);

  $results = Db::getInstance(_PS_USE_SQL_SLAVE_)
               ->executeS($dbQuery);

  $dbQuery2 = new DbQuery();
  $dbQuery2->select('o.`id_cart`')
          ->from('orders', 'o');
  //@formatter:on
  /*if (!empty($selectedStates)) {
      $dbQuery->where('o.`current_state` IN ('.implode(',', array_map('intval', $selectedStates)).')');
  }*/
  //$dbQuery->where('cola.id_colissimo_label IS NULL'.Shop::addSqlRestriction(false, 'o'));
  $dbQuery2->where('o.id_order  = '.$_POST['id_order']);

  $results2 = Db::getInstance(_PS_USE_SQL_SLAVE_)
               ->executeS($dbQuery2);

  $dbQuery3 = new DbQuery();
  $dbQuery3->select('c.`mobile_phone`')
          ->from('colissimo_cart_pickup_point', 'c');
  //@formatter:on
  /*if (!empty($selectedStates)) {
      $dbQuery->where('o.`current_state` IN ('.implode(',', array_map('intval', $selectedStates)).')');
  }*/
  //$dbQuery->where('cola.id_colissimo_label IS NULL'.Shop::addSqlRestriction(false, 'o'));
  $dbQuery3->where('c.id_cart  = '.$results2[0]['id_cart']);

  $results3 = Db::getInstance(_PS_USE_SQL_SLAVE_)
               ->executeS($dbQuery3);

               /*echo '<pre>';
               print_r($results);
               echo '</pre>';
               die;*/

$numero_suivi = '';
error_log('ship : '.$results[0]['shipping_number']);
if ( isset($results[0]['shipping_number']) && !empty($results[0]['shipping_number']) )
{
  $etiquette = $results[0]['id_colissimo_label'].'-'.$results[0]['shipping_number'].'.'.$results[0]['label_format'];

  if ( $results[0]['cn23'] == 1 )
  {
    $etiquetteCN23 = $results[0]['id_colissimo_label'].'-CN23-'.$results[0]['shipping_number'].'.pdf';
  }
  $numero_suivi = $results[0]['shipping_number'];
}
else
{
  $idColissimoOrder = $results[0]['id_colissimo_order'];
  $colissimoOrder = new ColissimoOrder((int) $idColissimoOrder);
  $colissimoService = new ColissimoService((int) $colissimoOrder->id_colissimo_service);
  $order = new Order((int) $colissimoOrder->id_order);
  $products = $order->getProducts();
  $totalQ = 0;
  $weightD = '0.2';
  foreach ($products as $product) {
    $totalQ += $product['product_quantity'];
  }
  if ( $totalQ > 200 )
  {
    $weightD = $totalQ / 1000;
  }
  $customerAddress = new Address((int) $order->id_address_delivery);
  $merchantAddress = new ColissimoMerchantAddress('sender');

  if ( empty($results3[0]['mobile_phone']) )
  {
    if ( substr($customerAddress->phone_mobile, 0, 2) == '06' || substr($customerAddress->phone_mobile, 0, 2) == '07' )
    {
      $results3[0]['mobile_phone'] = '+33'.str_replace('.','',substr($customerAddress->phone_mobile, 1));
    }
    else
    {
      $results3[0]['mobile_phone'] = trim(str_replace('.','',$customerAddress->phone_mobile));
    }
  }
  $customerAddress->phone_mobile = $results3[0]['mobile_phone'];
  //error_log('TEL : '.$results3[0]['mobile_phone']);

  $data = array(
      'order'             => $order,
      'version'           => 'Test',
      'cart'              => new Cart((int) $order->id_cart),
      'customer'          => new Customer((int) $order->id_customer),
      'colissimo_order'   => $colissimoOrder,
      'colissimo_service' => $colissimoService,
      'customer_addr'     => $customerAddress,
      'merchant_addr'     => $merchantAddress,
      'form_options'      => array(
          'include_return' => Tools::getValue('colissimo_return_label_'.$idColissimoOrder),
          'insurance'      => Tools::getValue('colissimo_insurance_'.$idColissimoOrder),
          'ta'             => Tools::getValue('colissimo_ta_'.$idColissimoOrder),
          'd150'           => Tools::getValue('colissimo_d150_'.$idColissimoOrder),
          'weight'         => $weightD,
          'mobile_phone'   => $results3[0]['mobile_phone'],
      ),
  );
//error_log('getTotalWeight : '.$order->getTotalWeight);
  /*echo '<pre>';
  print_r($data);
  echo '</pre>';
  die;*/


  //$logFile = ColissimoTools::getCurrentLogFilePath();
  //$logUrl = ColissimoTools::getCurrentLogFileUrl('colissimo');
  //$this->context->smarty->assign('log_url', $logUrl);
  //if (Configuration::get('COLISSIMO_LOGS')) {
 //     $handler = new ColissimoFileHandler($logFile);
  //} else {
      $handler = new ColissimoNullHandler();
  //}
  $logger = new ColissimoLogger($handler, 'Test');

  //error_log( print_r($data, true));
  $labelGenerator = new ColissimoLabelGenerator($logger);
  $labelGenerator->setData($data);
  try
  {
  $colissimoLabel = $labelGenerator->generate($_GET['poste']);
  }
  catch (Exception $e)
  {
    //echo $e.'<br />';
    error_log( 'erreur étiquette : '.print_r($e, true));
  }
//error_log('G1');
	// Début - Dorian, BERRY-WEB, septembre 2022
	function startsWith( $haystack, $needle ) {
		$length = strlen( $needle );
		return substr( $haystack, 0, $length ) === $needle;
	}
	if(is_string($colissimoLabel)){
		if(startsWith($colissimoLabel,'Erreur') || startsWith($colissimoLabel,'No data')){
			echo $colissimoLabel;
      echo '<pre>';
      print_r($data);
      echo '</pre>';
			exit;
		}
	}
	// Fin - Dorian, BERRY-WEB, septembre 2022
					
  /*echo '<pre>';
  print_r($colissimoLabel);
  echo '</pre>';die;*/

  $etiquette = $colissimoLabel->id.'-'.$colissimoLabel->shipping_number.'.'.$colissimoLabel->label_format;

  if ( $colissimoLabel->cn23 == 1 )
  {
    $etiquetteCN23 = $colissimoLabel->id.'-CN23-'.$colissimoLabel->shipping_number.'.pdf';
  }

  $numero_suivi = $colissimoLabel->shipping_number;
}

$sql_up1 = 'UPDATE ps_order_carrier SET tracking_number = "'.$numero_suivi.'" WHERE id_order = "'.$_POST['id_order'].'"';
$res_up1 = Db::getInstance()->execute($sql_up1);
/*$sql_up2 = 'UPDATE ps_orders SET shipping_number = "'.$numero_suivi.'" WHERE id_order = "'.$_POST['id_order'].'"';
$res_up2 = Db::getInstance()->execute($sql_up2);*/
//error_log('AWWWWWWWWWWWW : '.$chemin.$etiquette);
if ( file_exists($chemin.$etiquette) )
{
  $base64 = base64_encode(file_get_contents($chemin.$etiquette));
  //error_log('GG POSTE : '.$_GET['poste']);
  if ( $_GET['poste'] == 'controle1' )
  {
    //error_log('GG AW1');
    echo 'http://localhost:8000/imprimerEtiquetteThermique?port=USB&protocole=DATAMAX&adresseIp=&etiquette='.$base64;
  }
  else
  {
   // error_log('GG AW2');
    echo 'http://localhost:8000/imprimerEtiquetteThermique?port=USB&protocole=ZEBRA&adresseIp=&etiquette='.$base64;
  }
}
else {
  echo 'Erreur Colissimo : '.$chemin.$etiquette;
  echo '<pre>';
      print_r($data);
      echo '</pre>';
}

if ( file_exists($cheminCN23Exist.$etiquetteCN23) )
{
  $cn23 = $cheminCN23.$etiquetteCN23;
  echo '###'.$cn23;
}
else {
  echo '###0';
}
?>
