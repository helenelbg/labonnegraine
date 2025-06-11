<?php
	include('../config/config.inc.php');
	include('../init.php');
    $db = Db::getInstance();
    $apres_nb_jour = 8;
    $apres_nb_jour_lettreverte = 15;
    $orders = Order::getOrderIdsByStatus(4);
	$date_now_timestamp = intval(time());
	$date_now_x_jour = $date_now_timestamp - ($apres_nb_jour*24*60*60); //Suppression des 8 jours
	$date_now_x_jour_lettreverte = $date_now_timestamp - ($apres_nb_jour_lettreverte*24*60*60); //Suppression des 8 jours
    foreach($orders as $order)
    {
		$new_order = new Order($order);
		$carrierEC = new Carrier($new_order->id_carrier);
		//R�cup�ration de la date du changement de status
		$query_date = "SELECT UNIX_TIMESTAMP(date_add) as date_add FROM ps_order_history WHERE id_order = '".$order."' ORDER BY date_add DESC;";
		$result_date = $db->ExecuteS($query_date);
		$date_last = intval($result_date[0]['date_add']);
		//echo $date_last . "/ ". $order." - ".time()."<br />";
		if( $carrierEC->id_reference == 342 && $date_now_x_jour_lettreverte>$date_last)
		{
		    $new_order->setCurrentState(5, 1);
		}
		elseif( $carrierEC->id_reference != 342 && $date_now_x_jour>$date_last)
		{
		    $new_order->setCurrentState(5, 1);
		}
	}

?>
