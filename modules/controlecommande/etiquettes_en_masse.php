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
    $_GET['poste'] == 'controle2';
    $links = array();
    $ids = array();

    function startsWith( $haystack, $needle ) {
		$length = strlen( $needle );
		return substr( $haystack, 0, $length ) === $needle;
	}
    if ( isset($_FILES['file']) && !empty($_FILES['file']) )
    {
        $uploaddir = dirname(__FILE__).'/';
        $uploadfile = $uploaddir . basename($_FILES['file']['name']);

        move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile);
        $ligne = 0;
        //echo dirname(__FILE__).'/'.$_FILES['file']['name'];
        if (($file = fopen(dirname(__FILE__).'/'.$_FILES['file']['name'], "r")) !== false) {
            
            while (($data = fgetcsv($file, 1000, ";")) !== false) {
                
                if ($ligne != 0) {
                    
                    $_POST['id_order'] = $data[0];
                    $ids[] = $data[0];
  $chemin = dirname(__FILE__).'/../colissimo/documents/labels/';
  $cheminCN23Exist = dirname(__FILE__).'/../colissimo/documents/cn23/';
  $cheminCN23 = '/modules/colissimo/documents/cn23/';
  $etiquetteCN23 = '0';

  $dbQuery = new DbQuery();
  $dbQuery->select('co.`id_colissimo_order`, cola.`id_colissimo_label`, cola.`cn23`, cola.`shipping_number`, cola.`label_format`')
          ->from('colissimo_order', 'co')
          ->leftJoin('orders', 'o', 'o.`id_order` = co.`id_order`')
          ->leftJoin('colissimo_label', 'cola', 'cola.`id_colissimo_order` = co.`id_colissimo_order`');
  $dbQuery->where('co.id_order  = '.$_POST['id_order']);
  error_log($dbQuery->build());
  $results = Db::getInstance(_PS_USE_SQL_SLAVE_)
               ->executeS($dbQuery);

  $dbQuery2 = new DbQuery();
  $dbQuery2->select('o.`id_cart`')
          ->from('orders', 'o');
  $dbQuery2->where('o.id_order  = '.$_POST['id_order']);

  $results2 = Db::getInstance(_PS_USE_SQL_SLAVE_)
               ->executeS($dbQuery2);

  $dbQuery3 = new DbQuery();
  $dbQuery3->select('c.`mobile_phone`')
          ->from('colissimo_cart_pickup_point', 'c');
  $dbQuery3->where('c.id_cart  = '.$results2[0]['id_cart']);

  $results3 = Db::getInstance(_PS_USE_SQL_SLAVE_)
               ->executeS($dbQuery3);

$numero_suivi = '';
/*echo '<pre>';
print_r($results);
echo '</pre>';*/
error_log('ship : '.$results[0]['shipping_number']);
/*if ( isset($results[0]['shipping_number']) && !empty($results[0]['shipping_number']) )
{
  $etiquette = $results[0]['id_colissimo_label'].'-'.$results[0]['shipping_number'].'.'.$results[0]['label_format'];

  if ( $results[0]['cn23'] == 1 )
  {
    $etiquetteCN23 = $results[0]['id_colissimo_label'].'-CN23-'.$results[0]['shipping_number'].'.pdf';
  }
  $numero_suivi = $results[0]['shipping_number'];
}
else
{*/
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

      $handler = new ColissimoNullHandler();
 
  $logger = new ColissimoLogger($handler, 'Test');

  $labelGenerator = new ColissimoLabelGenerator($logger);
  $labelGenerator->setData($data);
  try
  {
  $colissimoLabel = $labelGenerator->generate($_GET['poste']);
  }
  catch (Exception $e)
  {
    error_log( 'erreur étiquette : '.print_r($e, true));
  }
//error_log('G1');
	// Début - Dorian, BERRY-WEB, septembre 2022
	
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
//}

$sql_up1 = 'UPDATE ps_order_carrier SET tracking_number = "'.$numero_suivi.'" WHERE id_order = "'.$_POST['id_order'].'"';
$res_up1 = Db::getInstance()->execute($sql_up1);
/*$sql_up2 = 'UPDATE ps_orders SET shipping_number = "'.$numero_suivi.'" WHERE id_order = "'.$_POST['id_order'].'"';
$res_up2 = Db::getInstance()->execute($sql_up2);*/
//error_log('AWWWWWWWWWWWW : '.$chemin.$etiquette);

if ( file_exists($chemin.$etiquette) )
{
  $base64 = base64_encode(file_get_contents($chemin.$etiquette));
  //error_log('GG POSTE : '.$_GET['poste']);
  if ( $_POST['poste'] == 'controle1' )
  {
    //error_log('GG AW1');
    $links[] = 'http://localhost:8000/imprimerEtiquetteThermique?port=USB&protocole=DATAMAX&adresseIp=&etiquette='.$base64;
   //echo '<script>window.open("http://localhost:8000/imprimerEtiquetteThermique?port=USB&protocole=DATAMAX&adresseIp=&etiquette='.$base64.'");</script>';
    //echo 'http://localhost:8000/imprimerEtiquetteThermique?port=USB&protocole=DATAMAX&adresseIp=&etiquette='.$base64;
  }
  else
  {
   // error_log('GG AW2');
   $links[] = 'http://localhost:8000/imprimerEtiquetteThermique?port=USB&protocole=ZEBRA&adresseIp=&etiquette='.$base64;
   //echo '<script>window.open("http://localhost:8000/imprimerEtiquetteThermique?port=USB&protocole=ZEBRA&adresseIp=&etiquette='.$base64.'");</script>';

    //echo 'http://localhost:8000/imprimerEtiquetteThermique?port=USB&protocole=ZEBRA&adresseIp=&etiquette='.$base64;
  }
}
else {
  echo '<br /><br/>Erreur Colissimo : '.$chemin.$etiquette;
  echo '<pre>';
      print_r($data);
      echo '</pre>';
}

if ( file_exists($cheminCN23Exist.$etiquetteCN23) )
{
  $cn23 = 'https://dev.labonnegraine.com/modules/colissimo/documents/cn23/'.$etiquetteCN23;
  $links[] = $cn23;
  //echo '<script>window.open("'.$cn23.'");</script>';
  //echo '###'.$cn23;
}


                }
                $ligne++;
            }
        }
    }
?>
<html>
    <head>
          <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    </head>
    <body>
        <form action="etiquettes_en_masse.php" method="POST" enctype="multipart/form-data">
        <input name="file" type="file" />
        <input name="poste" type="hidden" value="<?php echo $_GET['poste']; ?>" />
            <input type="submit" value="Imprimer" /> 
        </form>
        <?php
        //print_r($links);
        $implode = implode('-', $ids);
        
            if ( isset($links) && count($links) > 0 )
            {
                ?>
                    <script language="Javascript">
                        function imprim()
                        {

                        }

                        $( document ).ready(function() {
                            <?php 
                                echo 'window.open("https://dev.labonnegraine.com/admin123/test_etiquettes2.php?deliveryslipsadmin='.$implode.'");';
                               
                                $i = 0;
                                foreach($links as $link)
                                {
                                  $i++;
                                  echo 'setTimeout(function() { window.open("'.$link.'"); }, '.(5000*$i).');';
                                    
                                }
                            ?>
                        });
                    </script>
                <?php
            }
        ?>
    </body>
</html>