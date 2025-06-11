<?php 
die;

include('../../config/config.inc.php');
include('../../init.php');

$id_order = (int)$_GET['id_order'];

echo '<a href="../modules/chronopost/postSkybill.php?orderid='.(int)($id_order).'&shared_secret='
				.Configuration::get('CHRONOPOST_SECRET').'" title="Imprimer la lettre de transport" id="label_'.$order->id.'_'.$order->id_carrier.'" target="_blank">Lien chrono</a>';