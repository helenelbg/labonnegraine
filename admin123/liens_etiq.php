<?php
	include(dirname(__FILE__) . '/../config/config.inc.php');
	include(dirname(__FILE__) . '/../init.php');

	$cmds_attentes = '';
	//$id_commandes_attentes = Order::getOrderIdsByStatus(2, 50, $_GET['zone']);
	$id_commandes_attentes = Order::getOrderIdsByStatusByZone(2, 50, $_GET['zone']);

	$tab_order_attr = array();

	foreach ($id_commandes_attentes as $comm) 
	{
		$commande = new Order($comm);
		$products_commande = $commande->getProductsDetail();
		foreach ($products_commande as $produit) {

			$ref_prod_tab = explode('-', $produit['product_reference']);
			
			if(
				(substr($produit['product_reference'], 0, 3) == '0-0')
				|| (substr($produit['product_reference'], 0, 3) == '0-1')
				|| (substr($produit['product_reference'], 0, 3) == '0-2')
				|| (substr($produit['product_reference'], 0, 1) == '8')
			)
			{
				$tab_order_attr[] = $comm.'_'.$produit['product_id'].'_'.$produit['product_attribute_id'].'_'.$produit['product_quantity'];
			}
		}
	}

	if(count($tab_order_attr) > 0 ){
		$param = '';
		$cpte = 0;
		$cptl = 0;
		foreach($tab_order_attr as $item)
		{
			$exp = explode('_', $item);
			if ( $cpte > 30 )
			{
				$cptl++;
				echo '<a href="http://localhost/etiquettes/index_prod_test.php?l='.$param.'" target="_blank">Lien '.$cptl.'</a><br />';
				$param = '';
				$cpte = 0;
			}
			$cpte += $exp[3];
			if ( !empty($param) )
			{
				$param .= '-';
			}
			$param .= $item;
		}
		$cptl++;
		echo '<a href="http://localhost/etiquettes/index_prod_test.php?l='.$param.'" target="_blank">Lien '.$cptl.'</a><br />';
		$liste_comm_prod = implode('-', $tab_order_attr);
	} 
	else 
	{
		$liste_comm_prod = "";
	}
?>